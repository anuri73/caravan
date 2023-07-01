package urmat.zhenaliev.caravan.infrastructure.slick

import slick.interop.zio.DatabaseProvider
import slick.interop.zio.syntax._
import slick.jdbc.JdbcProfile
import zio._

import urmat.zhenaliev.caravan.api.healthcheck.{DbStatus, HealthCheckService}

class SlickHealthCheckService(databaseProvider: DatabaseProvider, jdbcProfile: JdbcProfile)
    extends HealthCheckService
       with Profile {
  override val profile = jdbcProfile

  private val healthCheckQuery = {
    import profile.api._
    sql"""select 1""".as[Int]
  }

  override def healthCheck: UIO[DbStatus] =
    ZIO
      .fromDBIO(healthCheckQuery)
      .provideLayer(ZLayer.succeed(databaseProvider))
      .fold(
        _ => DbStatus(false),
        _ => DbStatus(true),
      )
}

object SlickHealthCheckService {
  val live: RLayer[DatabaseProvider, HealthCheckService] = ZLayer {
    for {
      databaseProvider <- ZIO.service[DatabaseProvider]
      jdbcProfile      <- databaseProvider.profile
    } yield new SlickHealthCheckService(databaseProvider, jdbcProfile)
  }
}
