package urmat.zhenaliev.caravan

import com.typesafe.config.{Config, ConfigFactory}
import com.zaxxer.hikari.{HikariConfig, HikariDataSource}
import slick.interop.zio.DatabaseProvider
import slick.jdbc.{JdbcProfile, PostgresProfile}
import slick.util.ConfigExtensionMethods._
import zio._
import zio.logging.backend.SLF4J

import urmat.zhenaliev.caravan.api.healthcheck.HealthCheckService
import urmat.zhenaliev.caravan.domain.ItemRepository
import urmat.zhenaliev.caravan.infrastructure.flyway.FlywayProvider
import urmat.zhenaliev.caravan.infrastructure.slick.{SlickHealthCheckService, SlickItemRepository}

object Layers {
  val logger: ULayer[Unit] = ZLayer.make[Unit](
    Runtime.removeDefaultLoggers,
    SLF4J.slf4j,
  )

  private val jdbcProfileLayer: ULayer[JdbcProfile] = ZLayer.succeed[JdbcProfile](PostgresProfile)

  private val dbConfigLayer: ULayer[Config] =
    ZLayer {
      ZIO
        .attempt(ConfigFactory.load.resolve)
        .orDie
        .map(_.getConfig("dataSource"))
    }

  private val dataSourceLayer = dbConfigLayer.project[HikariDataSource] { config =>
    new HikariDataSource(
      new HikariConfig(config.toProperties),
    )
  }

  private val slickLayer: ULayer[DatabaseProvider] =
    (jdbcProfileLayer ++ dataSourceLayer) >>> DatabaseProvider.fromDataSource().orDie

  val itemRepository: ULayer[ItemRepository] = (slickLayer >>> SlickItemRepository.live).orDie

  val healthCheckService: ULayer[HealthCheckService] =
    (slickLayer >>> SlickHealthCheckService.live).orDie

  val flyway: ULayer[FlywayProvider] = (dataSourceLayer >>> FlywayProvider.live).orDie
}
