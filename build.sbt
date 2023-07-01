import Dependencies._

// give the user a nice default project!
ThisBuild / organization := "urmat.zhenaliev"
ThisBuild / version      := "0.0.2"

val dockerReleaseSettings = Seq(
  dockerExposedPorts   := Seq(8080),
  dockerExposedVolumes := Seq("/opt/docker/logs"),
  dockerBaseImage      := "eclipse-temurin:17.0.4_8-jre",
)

lazy val root = (project in file("."))
  .enablePlugins(JavaAppPackaging)
  .settings(BuildHelper.stdSettings)
  .settings(
    name := "Caravan",
    addCompilerPlugin("com.olegpy" %% "better-monadic-for" % "0.3.1"),
    dockerReleaseSettings,
    testFrameworks += new TestFramework("zio.test.sbt.ZTestFramework"),
    libraryDependencies ++= Seq(
      /* zio-http */
      `zio-http`,
      /* slick */
      slick(),
      slick("hikaricp"),
      `zio-slick-interop`,
      /* general */
      `postgresql`,
      flyway("core"),
      zio(),
      zio("config", Version.ZioConfig),
      zio("config-magnolia", Version.ZioConfig),
      zio("config-typesafe", Version.ZioConfig),
      zio("json", Version.ZioJson),
      /* logging */
      `logback-classic`,
      zio("logging", Version.ZioLogging),
      zio("logging-slf4j", Version.ZioLogging),
      /* test */
      `testcontainers` % Test,
      zio("test-sbt")  % Test,
    ),
  )
  .settings(
    Docker / version          := version.value,
    Compile / run / mainClass := Option("urmat.zhenaliev.caravan.Caravan"),
  )
  .enablePlugins(DockerPlugin, JavaAppPackaging)

addCommandAlias("fmt", "scalafmt; Test / scalafmt; sFix;")
addCommandAlias("fmtCheck", "scalafmtCheck; Test / scalafmtCheck; sFixCheck")
addCommandAlias("sFix", "scalafix OrganizeImports; Test / scalafix OrganizeImports")
addCommandAlias(
  "sFixCheck",
  "scalafix --check OrganizeImports; Test / scalafix --check OrganizeImports",
)
