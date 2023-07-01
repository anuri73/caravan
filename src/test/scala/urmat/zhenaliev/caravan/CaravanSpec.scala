package urmat.zhenaliev.caravan

import zio.test._
import zio.test.Assertion._
import zhttp.http._

object CaravanSpec extends DefaultRunnableSpec {
  override def spec: ZSpec[Environment, Failure] = suite("""CaravanSpec""")(
    testM("200 ok") {
      checkAllM(Gen.fromIterable(List("text", "json"))) { uri =>
        val request = Request(Method.GET, URL(!! / uri))
        assertM(Caravan.app(request).map(_.status))(equalTo(Status.OK))
      }
    },
  )
}
