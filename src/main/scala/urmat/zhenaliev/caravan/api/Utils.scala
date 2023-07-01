package urmat.zhenaliev.caravan.api

import zio._
import zio.http._

import urmat.zhenaliev.caravan.api.Extensions._
import urmat.zhenaliev.caravan.domain._

private[api] object Utils {
  def extractLong(str: String): IO[ValidationError, Long] = ZIO
    .attempt(str.toLong)
    .refineToOrDie[NumberFormatException]
    .mapError(err => ValidationError(err.getMessage))

  def handleError(err: DomainError): UIO[Response] = err match {
    case NotFoundError(None)      => ZIO.succeed(Response.status(Status.NotFound))
    case NotFoundError(Some(msg)) => msg.toResponseZIO(Status.NotFound)
    case ValidationError(msg)     => msg.toResponseZIO(Status.BadRequest)
    case RepositoryError(cause)   => cause.getMessage.toResponseZIO(Status.InternalServerError)
  }
}
