package urmat.zhenaliev.caravan.infrastructure.slick

import slick.jdbc.JdbcProfile

import urmat.zhenaliev.caravan.domain.ItemId

trait Profile {
  val profile: JdbcProfile
}

trait EntityIdMappers {
  self: Profile =>
  import profile.api._

  implicit def itemIdMapper: BaseColumnType[ItemId] = MappedColumnType.base[ItemId, Long](
    ent => ent.value,
    value => ItemId(value),
  )
}
