package urmat.zhenaliev.caravan

import zio._
import zio.http.Server

import urmat.zhenaliev.caravan.api.{HealthCheckApp, ItemApp}
import urmat.zhenaliev.caravan.config.Configuration.ApiConfig
import urmat.zhenaliev.caravan.infrastructure.flyway.FlywayProvider

object BootZioHttp extends ZIOAppDefault {
  private val routes = ItemApp.app ++ HealthCheckApp.app

  private val serverLayer =
    ZLayer
      .service[ApiConfig]
      .flatMap { cfg =>
        Server.defaultWith(_.binding(cfg.get.host, cfg.get.port))
      }
      .orDie

  private val startHttpServer = Server.serve(routes)

  val migrateDbSchema: RIO[FlywayProvider, Unit] =
    FlywayProvider
      .flyway
      .flatMap(_.migrate)
      .retry(Schedule.exponential(200.millis))
      .flatMap(res => Console.printLine(s"Flyway migration completed with: $res"))

  val program =
    migrateDbSchema *>
      startHttpServer

  override val run =
    program.provide(
      ApiConfig.layer,
      Layers.itemRepository,
      Layers.healthCheckService,
      Layers.flyway,
      Layers.logger,
      serverLayer,
    )
}
