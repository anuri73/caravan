package urmat.zhenaliev.caravan

import scala.jdk.CollectionConverters.MapHasAsJava

import com.typesafe.config.{Config, ConfigFactory}
import com.zaxxer.hikari.{HikariConfig, HikariDataSource}
import slick.interop.zio.DatabaseProvider
import slick.jdbc.PostgresProfile
import slick.util.ConfigExtensionMethods._
import zio._
import zio.logging.backend.SLF4J
import zio.test.ZIOSpecDefault

import urmat.zhenaliev.caravan.domain.ItemRepository
import urmat.zhenaliev.caravan.infrastructure.Postgres
import urmat.zhenaliev.caravan.infrastructure.Postgres.SchemaAwarePostgresContainer
import urmat.zhenaliev.caravan.infrastructure.flyway.FlywayProvider
import urmat.zhenaliev.caravan.infrastructure.slick.SlickItemRepository

abstract class ITSpec(schema: Option[String]) extends ZIOSpecDefault {
  val itLayers: ZLayer[Any, Throwable, FlywayProvider with ItemRepository] = {

    val logging: ULayer[Unit] = ZLayer.make[Unit](
      Runtime.removeDefaultLoggers,
      SLF4J.slf4j,
    )

    val postgres: ULayer[SchemaAwarePostgresContainer] = Postgres.postgres(schema)

    val config: URLayer[SchemaAwarePostgresContainer, Config] = ZLayer {
      for {
        container <- ZIO.service[SchemaAwarePostgresContainer]
      } yield ConfigFactory.parseMap(
        Map[String, Any](
          "dataSource.jdbcUrl"         -> container.jdbcUrl,
          "dataSource.username"        -> container.username,
          "dataSource.password"        -> container.password,
          "dataSource.driverClassName" -> "org.postgresql.Driver",
        ).asJava,
      )
    }

    val jdbcProfileLayer: ULayer[PostgresProfile] = ZLayer.succeed(PostgresProfile)

    val dbConfigLayer: RLayer[SchemaAwarePostgresContainer, Config] = config.flatMap { rawConfig =>
      ZLayer.succeed(rawConfig.get.getConfig("dataSource"))
    }

    val dataSourceLayer = dbConfigLayer.project[HikariDataSource] { config =>
      new HikariDataSource(
        new HikariConfig(config.toProperties),
      )
    }

    val slickLayer: RLayer[SchemaAwarePostgresContainer, DatabaseProvider] =
      (jdbcProfileLayer ++ dataSourceLayer) >>> DatabaseProvider.fromDataSource().orDie

    val itemRepositoryLayer: RLayer[SchemaAwarePostgresContainer, ItemRepository] =
      slickLayer >>> SlickItemRepository.live

    val flywayLayer: RLayer[SchemaAwarePostgresContainer, FlywayProvider] =
      dataSourceLayer >>> FlywayProvider.live

    ZLayer.make[FlywayProvider with ItemRepository](
      logging,
      postgres,
      itemRepositoryLayer,
      flywayLayer,
    )
  }
}
