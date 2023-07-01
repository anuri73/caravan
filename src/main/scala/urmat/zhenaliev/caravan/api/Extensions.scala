package urmat.zhenaliev.caravan.api

import zio._
import zio.http._
import zio.json._

private[api] object Extensions {
  implicit class RichRequest(val request: Request) extends AnyVal {
    def bodyAs[T: JsonDecoder]: IO[String, T] =
      for {
        body <- request.body.asString.orDie
        t    <- ZIO.succeed(body.fromJson[T]).absolve
      } yield t
  }

  implicit class RichDomain[T](val data: T) extends AnyVal {
    def toResponseZIO(implicit ev: JsonEncoder[T]): UIO[Response] = toResponseZIO(Status.Ok)

    def toResponseZIO(status: Status)(implicit ev: JsonEncoder[T]): UIO[Response] = ZIO.succeed {
      Response.json(data.toJson).withStatus(status)
    }

    def asNoContentResponseZIO: UIO[Response] = ZIO.succeed(Response.status(Status.NoContent))
  }
}
