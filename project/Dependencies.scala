import sbt._

object Dependencies {
  lazy val `zio-http`          = zio("http", Version.ZioHTTP)
  lazy val `zio-slick-interop` = "io.scalac"     %% "zio-slick-interop" % Version.ZioSlickInterop
  lazy val `postgresql`        = "org.postgresql" % "postgresql"        % Version.Postgresql
  lazy val `logback-classic`   = "ch.qos.logback" % "logback-classic"   % Version.LogbackClassic
  lazy val `testcontainers`    = "com.dimafeng" %% "testcontainers-scala-postgresql" % Version.TestContainers

  def flyway(project: String = "", version: String = Version.Flyway): ModuleID =
    "org.flywaydb" % Seq("flyway", project).filter(_.nonEmpty).mkString("-") % version

  def slick(project: String = "", version: String = Version.Slick): ModuleID =
    "com.typesafe.slick" %% Seq("slick", project).filter(_.nonEmpty).mkString("-") % version

  def zio(project: String = "", version: String = Version.Zio): ModuleID =
    "dev.zio" %% Seq("zio", project).filter(_.nonEmpty).mkString("-") % version
}
