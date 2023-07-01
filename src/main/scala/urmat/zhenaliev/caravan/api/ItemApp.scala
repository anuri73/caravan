package urmat.zhenaliev.caravan.api

import zio._
import zio.http._

import urmat.zhenaliev.caravan.api.Extensions._
import urmat.zhenaliev.caravan.application.ApplicationService
import urmat.zhenaliev.caravan.domain._

object ItemApp extends JsonSupport {
  val app: HttpApp[ItemRepository, Nothing] = Http.collectZIO[Request] {

    case Method.GET -> Root / "items" =>
      ApplicationService
        .getAllItems
        .foldZIO(Utils.handleError, _.toResponseZIO)

    case request @ Method.POST -> Root / "items" =>
      val effect: ZIO[ItemRepository, DomainError, Item] = for {
        r  <- request.bodyAs[CreateItemRequest].mapError(ValidationError)
        id <- ApplicationService.addItem(r.name, r.price)
      } yield Item(id, name = r.name, price = r.price)

      effect.foldZIO(Utils.handleError, _.toResponseZIO(Status.Created))

    case Method.GET -> Root / "items" / itemId =>
      val effect: ZIO[ItemRepository, DomainError, Item] = for {
        id        <- Utils.extractLong(itemId)
        maybeItem <- ApplicationService.getItemById(ItemId(id))
        ans       <- maybeItem
                       .map((lll: Item) => ZIO.succeed(lll))
                       .getOrElse(ZIO.fail(NotFoundError(s"Item $id not found")))
      } yield ans

      effect.foldZIO(Utils.handleError, _.toResponseZIO)

    case Method.DELETE -> Root / "items" / itemId =>
      val effect: ZIO[ItemRepository, DomainError, Unit] = for {
        id     <- Utils.extractLong(itemId)
        amount <- ApplicationService.deleteItem(ItemId(id))
        _      <- if (amount == 0) ZIO.fail(NotFoundError.empty)
                  else ZIO.unit
      } yield ()

      effect.foldZIO(Utils.handleError, _.asNoContentResponseZIO)

    case request @ Method.PATCH -> Root / "items" / itemId =>
      val effect: ZIO[ItemRepository, DomainError, Item] = for {
        id        <- Utils.extractLong(itemId)
        r         <- request.bodyAs[PartialUpdateItemRequest].mapError(ValidationError)
        maybeItem <- ApplicationService.partialUpdateItem(ItemId(id), r.name, r.price)
        item      <- maybeItem
                       .map(ZIO.succeed(_))
                       .getOrElse(ZIO.fail(NotFoundError.empty))
      } yield item

      effect.foldZIO(Utils.handleError, _.toResponseZIO)

    case request @ Method.PUT -> Root / "items" / itemId =>
      val effect: ZIO[ItemRepository, DomainError, Item] = for {
        id        <- Utils.extractLong(itemId)
        r         <- request.bodyAs[UpdateItemRequest].mapError(ValidationError)
        maybeItem <- ApplicationService.updateItem(ItemId(id), r.name, r.price)
        item      <- maybeItem
                       .map(ZIO.succeed(_))
                       .getOrElse(ZIO.fail(NotFoundError.empty))
      } yield item

      effect.foldZIO(Utils.handleError, _.toResponseZIO)
  }
}
