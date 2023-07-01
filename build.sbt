import Dependencies._

// give the user a nice default project!
ThisBuild / organization := "urmat.zhenaliev"
ThisBuild / version := "0.0.1"

lazy val root = (project in file("."))
  .enablePlugins(JavaAppPackaging)
  .settings(BuildHelper.stdSettings)
  .settings(
    name := "Caravan",
    testFrameworks += new TestFramework("zio.test.sbt.ZTestFramework"),
    libraryDependencies ++= Seq(`zio-test`, `zio-test-sbt`, `zio-http`, `zio-http-test`),
  )
  .settings(
    Docker / version          := version.value,
    Compile / run / mainClass := Option("urmat.zhenaliev.caravan.Caravan"),
  )

addCommandAlias("fmt", "scalafmt; Test / scalafmt; sFix;")
addCommandAlias("fmtCheck", "scalafmtCheck; Test / scalafmtCheck; sFixCheck")
addCommandAlias("sFix", "scalafix OrganizeImports; Test / scalafix OrganizeImports")
addCommandAlias("sFixCheck", "scalafix --check OrganizeImports; Test / scalafix --check OrganizeImports")
