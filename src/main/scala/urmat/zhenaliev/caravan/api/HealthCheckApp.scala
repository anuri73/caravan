package urmat.zhenaliev.caravan.api

import zio._
import zio.http._

import urmat.zhenaliev.caravan.api.healthcheck.HealthCheckService

object HealthCheckApp {
  val app: HttpApp[HealthCheckService, Nothing] = Http.collectZIO[Request] {

    case Method.HEAD -> Root / "healthcheck" =>
      ZIO.succeed(Response.status(Status.NoContent))

    case Method.GET -> Root / "healthcheck" =>
      HealthCheckService.healthCheck.map { dbStatus =>
        if (dbStatus.status) Response.ok
        else Response.status(Status.InternalServerError)
      }
  }
}
