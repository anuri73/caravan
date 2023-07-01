package urmat.zhenaliev.caravan.infrastructure.slick.tables

import urmat.zhenaliev.caravan.domain._
import urmat.zhenaliev.caravan.infrastructure.slick.{EntityIdMappers, Profile}

trait ItemsTable extends EntityIdMappers {
  self: Profile =>
  import profile.api._

  class Items(tag: Tag) extends Table[Item](tag, "items") {
    def id    = column[ItemId]("id", O.PrimaryKey, O.AutoInc)
    def name  = column[String]("name")
    def price = column[BigDecimal]("price")
    def *     = (id, name, price).<>((Item.apply _).tupled, Item.unapply)
  }

  val table = TableQuery[self.Items]
}
