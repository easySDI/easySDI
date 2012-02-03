-- MySQL dump 10.13  Distrib 5.1.41, for Win32 (ia32)
--
-- Host: localhost    Database: monitor
-- ------------------------------------------------------
-- Server version	5.1.41-community
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,POSTGRESQL' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table "action_types"
--

DROP TABLE IF EXISTS "action_types";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "action_types" (
  "ID_ACTION_TYPE" int(10) unsigned NOT NULL,
  "NAME" varchar(45) NOT NULL,
  PRIMARY KEY ("ID_ACTION_TYPE")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "action_types"
--

LOCK TABLES "action_types" WRITE;
/*!40000 ALTER TABLE "action_types" DISABLE KEYS */;
INSERT INTO "action_types" VALUES (1,'E-MAIL'),(2,'RSS');
/*!40000 ALTER TABLE "action_types" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "actions"
--

DROP TABLE IF EXISTS "actions";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "actions" (
  "ID_ACTION" int(10) unsigned NOT NULL,
  "ID_JOB" int(10) unsigned NOT NULL,
  "ID_ACTION_TYPE" int(10) unsigned NOT NULL,
  "TARGET" varchar(255) DEFAULT NULL,
  "LANGUAGE" char(2) DEFAULT NULL,
  PRIMARY KEY ("ID_ACTION"),
  KEY "FK_ACTION_JOB" ("ID_JOB"),
  KEY "FK_ACTION_TYPE" ("ID_ACTION_TYPE"),
  CONSTRAINT "FK_ACTION_JOB" FOREIGN KEY ("ID_JOB") REFERENCES "jobs" ("ID_JOB") ON DELETE CASCADE,
  CONSTRAINT "FK_ACTION_TYPE" FOREIGN KEY ("ID_ACTION_TYPE") REFERENCES "action_types" ("ID_ACTION_TYPE")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "actions"
--

LOCK TABLES "actions" WRITE;
/*!40000 ALTER TABLE "actions" DISABLE KEYS */;
/*!40000 ALTER TABLE "actions" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "alerts"
--

DROP TABLE IF EXISTS "alerts";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "alerts" (
  "ID_ALERT" int(10) unsigned NOT NULL,
  "ID_JOB" int(10) unsigned NOT NULL,
  "ID_OLD_STATUS" int(10) unsigned NOT NULL,
  "ID_NEW_STATUS" int(10) unsigned NOT NULL,
  "CAUSE" text NOT NULL,
  "ALERT_DATE_TIME" datetime NOT NULL,
  "EXPOSE_RSS" tinyint(1) NOT NULL,
  PRIMARY KEY ("ID_ALERT"),
  KEY "FK_ALERTS_JOB" ("ID_JOB"),
  KEY "FK_ALERTS_OLD_STATUS" ("ID_OLD_STATUS"),
  KEY "FK_ALERTS_NEW_STATUS" ("ID_NEW_STATUS"),
  CONSTRAINT "FK_ALERTS_JOB" FOREIGN KEY ("ID_JOB") REFERENCES "jobs" ("ID_JOB") ON DELETE CASCADE,
  CONSTRAINT "FK_ALERTS_NEW_STATUS" FOREIGN KEY ("ID_NEW_STATUS") REFERENCES "statuses" ("ID_STATUS"),
  CONSTRAINT "FK_ALERTS_OLD_STATUS" FOREIGN KEY ("ID_OLD_STATUS") REFERENCES "statuses" ("ID_STATUS")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "alerts"
--

LOCK TABLES "alerts" WRITE;
/*!40000 ALTER TABLE "alerts" DISABLE KEYS */;
/*!40000 ALTER TABLE "alerts" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "http_methods"
--

DROP TABLE IF EXISTS "http_methods";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "http_methods" (
  "ID_HTTP_METHOD" int(10) unsigned NOT NULL,
  "NAME" varchar(10) NOT NULL,
  PRIMARY KEY ("ID_HTTP_METHOD")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "http_methods"
--

LOCK TABLES "http_methods" WRITE;
/*!40000 ALTER TABLE "http_methods" DISABLE KEYS */;
INSERT INTO "http_methods" VALUES (1,'GET'),(2,'POST');
/*!40000 ALTER TABLE "http_methods" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "job_agg_log_entries"
--

DROP TABLE IF EXISTS "job_agg_log_entries";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "job_agg_log_entries" (
  "DATE_LOG" datetime NOT NULL,
  "ID_JOB" int(10) unsigned NOT NULL,
  "SLA_MEAN_RESP_TIME" float NOT NULL,
  "H24_MEAN_RESP_TIME" float NOT NULL,
  "SLA_AVAILABILITY" float NOT NULL,
  "H24_AVAILABILITY" float NOT NULL,
  "SLA_NB_BIZ_ERRORS" int(10) unsigned NOT NULL,
  "H24_NB_BIZ_ERRORS" int(10) unsigned NOT NULL,
  "SLA_NB_CONN_ERRORS" int(10) unsigned NOT NULL,
  "H24_NB_CONN_ERRORS" int(10) unsigned NOT NULL,
  PRIMARY KEY ("DATE_LOG","ID_JOB"),
  KEY "FK_JOB_AGG_LOG_ENTRIES_JOB" ("ID_JOB"),
  CONSTRAINT "FK_JOB_AGG_LOG_ENTRIES_JOB" FOREIGN KEY ("ID_JOB") REFERENCES "jobs" ("ID_JOB") ON DELETE CASCADE ON UPDATE CASCADE
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "job_agg_log_entries"
--

LOCK TABLES "job_agg_log_entries" WRITE;
/*!40000 ALTER TABLE "job_agg_log_entries" DISABLE KEYS */;
/*!40000 ALTER TABLE "job_agg_log_entries" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "job_defaults"
--

DROP TABLE IF EXISTS "job_defaults";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "job_defaults" (
  "ID_PARAM" int(10) unsigned NOT NULL,
  "COLUMN_NAME" varchar(45) NOT NULL,
  "STRING_VALUE" varchar(45) DEFAULT NULL,
  "VALUE_TYPE" varchar(20) NOT NULL,
  PRIMARY KEY ("ID_PARAM")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "job_defaults"
--

LOCK TABLES "job_defaults" WRITE;
/*!40000 ALTER TABLE "job_defaults" DISABLE KEYS */;
INSERT INTO "job_defaults" VALUES (1,'IS_PUBLIC','false','bool'),(2,'IS_AUTOMATIC','false','bool'),(3,'ALLOWS_REALTIME','true','bool'),(4,'TRIGGERS_ALERTS','false','bool'),(5,'TEST_INTERVAL','3600','int'),(6,'TIMEOUT','30','int'),(7,'BUSINESS_ERRORS','true','bool'),(8,'HTTP_ERRORS','true','bool'),(9,'SLA_START_TIME','08:00:00','time'),(10,'SLA_END_TIME','18:00:00','time');
/*!40000 ALTER TABLE "job_defaults" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "jobs"
--

DROP TABLE IF EXISTS "jobs";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "jobs" (
  "ID_JOB" int(10) unsigned NOT NULL,
  "NAME" varchar(45) NOT NULL,
  "ID_SERVICE_TYPE" int(10) unsigned NOT NULL,
  "SERVICE_URL" varchar(255) NOT NULL,
  "ID_HTTP_METHOD" int(10) unsigned NOT NULL,
  "TEST_INTERVAL" int(10) unsigned NOT NULL,
  "TIMEOUT" int(10) unsigned NOT NULL,
  "BUSINESS_ERRORS" tinyint(1) NOT NULL DEFAULT '0',
  "SLA_START_TIME" datetime NOT NULL,
  "LOGIN" varchar(45) DEFAULT NULL,
  "PASSWORD" varchar(45) DEFAULT NULL,
  "IS_PUBLIC" tinyint(1) NOT NULL DEFAULT '0',
  "IS_AUTOMATIC" tinyint(1) NOT NULL DEFAULT '0',
  "ALLOWS_REALTIME" tinyint(1) NOT NULL DEFAULT '0',
  "TRIGGERS_ALERTS" tinyint(1) NOT NULL DEFAULT '0',
  "ID_STATUS" int(10) unsigned NOT NULL DEFAULT '4',
  "HTTP_ERRORS" tinyint(1) NOT NULL DEFAULT '0',
  "SLA_END_TIME" datetime NOT NULL,
  PRIMARY KEY ("ID_JOB"),
  UNIQUE KEY "UNIQUE_NAME" ("NAME"),
  KEY "FK_JOBS_SERVICE_TYPE" ("ID_SERVICE_TYPE"),
  KEY "FK_JOBS_HTTP_METHOD" ("ID_HTTP_METHOD"),
  KEY "FK_JOBS_STATUS" ("ID_STATUS"),
  CONSTRAINT "FK_JOBS_HTTP_METHOD" FOREIGN KEY ("ID_HTTP_METHOD") REFERENCES "http_methods" ("ID_HTTP_METHOD"),
  CONSTRAINT "FK_JOBS_SERVICE_TYPE" FOREIGN KEY ("ID_SERVICE_TYPE") REFERENCES "service_types" ("ID_SERVICE_TYPE"),
  CONSTRAINT "FK_JOBS_STATUS" FOREIGN KEY ("ID_STATUS") REFERENCES "statuses" ("ID_STATUS")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "jobs"
--

LOCK TABLES "jobs" WRITE;
/*!40000 ALTER TABLE "jobs" DISABLE KEYS */;
/*!40000 ALTER TABLE "jobs" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "last_ids"
--

DROP TABLE IF EXISTS "last_ids";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "last_ids" (
  "TABLE_NAME" varchar(255) NOT NULL,
  "LAST_ID" int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY ("TABLE_NAME")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "last_ids"
--

LOCK TABLES "last_ids" WRITE;
/*!40000 ALTER TABLE "last_ids" DISABLE KEYS */;
INSERT INTO "last_ids" VALUES ('ACTIONS',1),('ALERTS',1),('HTTP_METHODS',3),('JOBS',1),('JOB_DEFAULTS',11),('LOG_ENTRIES',1),('QUERIES',1),('SERVICE_METHODS',10),('SERVICE_TYPES',8),('STATUSES',5);
/*!40000 ALTER TABLE "last_ids" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "log_entries"
--

DROP TABLE IF EXISTS "log_entries";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "log_entries" (
  "ID_LOG_ENTRY" int(10) unsigned NOT NULL,
  "ID_QUERY" int(10) unsigned NOT NULL,
  "REQUEST_TIME" datetime NOT NULL,
  "RESPONSE_DELAY" float NOT NULL,
  "MESSAGE" text NOT NULL,
  "ID_STATUS" int(10) unsigned NOT NULL,
  "HTTP_CODE" int(10) unsigned DEFAULT NULL,
  "EXCEPTION_CODE" varchar(100) DEFAULT NULL,
  PRIMARY KEY ("ID_LOG_ENTRY"),
  KEY "fk_log_entries_statuses_STATUS" ("ID_STATUS"),
  KEY "FK_LOG_ENTRIES_QUERY" ("ID_QUERY"),
  CONSTRAINT "FK_LOG_ENTRIES_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES "queries" ("ID_QUERY") ON DELETE CASCADE,
  CONSTRAINT "fk_log_entries_statuses_STATUS" FOREIGN KEY ("ID_STATUS") REFERENCES "statuses" ("ID_STATUS")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "log_entries"
--

LOCK TABLES "log_entries" WRITE;
/*!40000 ALTER TABLE "log_entries" DISABLE KEYS */;
/*!40000 ALTER TABLE "log_entries" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "queries"
--

DROP TABLE IF EXISTS "queries";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "queries" (
  "ID_QUERY" int(10) unsigned NOT NULL,
  "ID_JOB" int(10) unsigned NOT NULL,
  "ID_SERVICE_METHOD" int(10) unsigned NOT NULL,
  "ID_STATUS" int(10) unsigned NOT NULL DEFAULT '4',
  "NAME" varchar(45) NOT NULL,
  PRIMARY KEY ("ID_QUERY"),
  KEY "FK_QUERIES_METHOD" ("ID_SERVICE_METHOD"),
  KEY "FK_QUERIES_JOB" ("ID_JOB"),
  KEY "FK_QUERIES_STATUS" ("ID_STATUS"),
  CONSTRAINT "FK_QUERIES_JOB" FOREIGN KEY ("ID_JOB") REFERENCES "jobs" ("ID_JOB") ON DELETE CASCADE,
  CONSTRAINT "FK_QUERIES_METHOD" FOREIGN KEY ("ID_SERVICE_METHOD") REFERENCES "service_methods" ("ID_SERVICE_METHOD"),
  CONSTRAINT "FK_QUERIES_STATUS" FOREIGN KEY ("ID_STATUS") REFERENCES "statuses" ("ID_STATUS")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "queries"
--

LOCK TABLES "queries" WRITE;
/*!40000 ALTER TABLE "queries" DISABLE KEYS */;
/*!40000 ALTER TABLE "queries" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "query_agg_log_entries"
--

DROP TABLE IF EXISTS "query_agg_log_entries";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "query_agg_log_entries" (
  "DATE_LOG" datetime NOT NULL,
  "ID_QUERY" int(10) unsigned NOT NULL,
  "SLA_MEAN_RESP_TIME" float NOT NULL,
  "H24_MEAN_RESP_TIME" float NOT NULL,
  "SLA_AVAILABILITY" float NOT NULL,
  "H24_AVAILABILITY" float NOT NULL,
  "SLA_NB_BIZ_ERRORS" int(10) unsigned NOT NULL,
  "H24_NB_BIZ_ERRORS" int(10) unsigned NOT NULL,
  "SLA_NB_CONN_ERRORS" int(10) unsigned NOT NULL,
  "H24_NB_CONN_ERRORS" int(10) unsigned NOT NULL,
  PRIMARY KEY ("DATE_LOG","ID_QUERY"),
  KEY "FK_QUERY_AGG_LOG_ENTRIES_QUERY" ("ID_QUERY"),
  CONSTRAINT "FK_QUERY_AGG_LOG_ENTRIES_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES "queries" ("ID_QUERY") ON DELETE CASCADE
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "query_agg_log_entries"
--

LOCK TABLES "query_agg_log_entries" WRITE;
/*!40000 ALTER TABLE "query_agg_log_entries" DISABLE KEYS */;
/*!40000 ALTER TABLE "query_agg_log_entries" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "query_params"
--

DROP TABLE IF EXISTS "query_params";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "query_params" (
  "ID_QUERY" int(10) unsigned NOT NULL,
  "NAME" varchar(45) NOT NULL,
  "VALUE" text NOT NULL,
  PRIMARY KEY ("ID_QUERY","NAME"),
  CONSTRAINT "FK_QUERY_PARAMS_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES "queries" ("ID_QUERY") ON DELETE CASCADE
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "query_params"
--

LOCK TABLES "query_params" WRITE;
/*!40000 ALTER TABLE "query_params" DISABLE KEYS */;
/*!40000 ALTER TABLE "query_params" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "roles"
--

DROP TABLE IF EXISTS "roles";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "roles" (
  "ID_ROLE" int(10) unsigned NOT NULL,
  "NAME" varchar(45) NOT NULL,
  "RANK" int(10) unsigned NOT NULL,
  PRIMARY KEY ("ID_ROLE")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "roles"
--

LOCK TABLES "roles" WRITE;
/*!40000 ALTER TABLE "roles" DISABLE KEYS */;
INSERT INTO "roles" VALUES (1,'ROLE_ADMIN',1),(2,'ROLE_USER',3);
/*!40000 ALTER TABLE "roles" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "service_methods"
--

DROP TABLE IF EXISTS "service_methods";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "service_methods" (
  "ID_SERVICE_METHOD" int(10) unsigned NOT NULL,
  "NAME" varchar(45) NOT NULL,
  PRIMARY KEY ("ID_SERVICE_METHOD")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "service_methods"
--

LOCK TABLES "service_methods" WRITE;
/*!40000 ALTER TABLE "service_methods" DISABLE KEYS */;
INSERT INTO "service_methods" VALUES (1,'GetCapabilities'),(2,'GetMap'),(3,'GetFeature'),(4,'GetRecordById'),(5,'GetTile'),(6,'GetRecords'),(7,'GetCoverage'),(8,'DescribeSensor');
/*!40000 ALTER TABLE "service_methods" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "service_types"
--

DROP TABLE IF EXISTS "service_types";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "service_types" (
  "ID_SERVICE_TYPE" int(10) unsigned NOT NULL,
  "NAME" varchar(20) NOT NULL,
  "VERSION" varchar(10) NOT NULL,
  PRIMARY KEY ("ID_SERVICE_TYPE")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "service_types"
--

LOCK TABLES "service_types" WRITE;
/*!40000 ALTER TABLE "service_types" DISABLE KEYS */;
INSERT INTO "service_types" VALUES (1,'WMS','1.1.1'),(2,'WFS','1.1.0'),(4,'WMTS','1.0.0'),(5,'CSW','2.0.2'),(6,'SOS','1.0.0'),(7,'WCS','1.0.0');
/*!40000 ALTER TABLE "service_types" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "service_types_methods"
--

DROP TABLE IF EXISTS "service_types_methods";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "service_types_methods" (
  "ID_SERVICE_TYPE" int(10) unsigned NOT NULL,
  "ID_SERVICE_METHOD" int(10) unsigned NOT NULL,
  PRIMARY KEY ("ID_SERVICE_TYPE","ID_SERVICE_METHOD"),
  KEY "FK_SERVICE_TYPES_METHODS_METHOD" ("ID_SERVICE_METHOD"),
  CONSTRAINT "FK_SERVICE_TYPES_METHODS_METHOD" FOREIGN KEY ("ID_SERVICE_METHOD") REFERENCES "service_methods" ("ID_SERVICE_METHOD") ON DELETE CASCADE,
  CONSTRAINT "FK_SERVICE_TYPES_METHODS_TYPE" FOREIGN KEY ("ID_SERVICE_TYPE") REFERENCES "service_types" ("ID_SERVICE_TYPE") ON DELETE CASCADE
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "service_types_methods"
--

LOCK TABLES "service_types_methods" WRITE;
/*!40000 ALTER TABLE "service_types_methods" DISABLE KEYS */;
INSERT INTO "service_types_methods" VALUES (1,1),(2,1),(4,1),(5,1),(6,1),(7,1),(1,2),(2,3),(5,4),(4,5),(5,6),(7,7),(6,8);
/*!40000 ALTER TABLE "service_types_methods" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "statuses"
--

DROP TABLE IF EXISTS "statuses";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "statuses" (
  "ID_STATUS" int(10) unsigned NOT NULL,
  "NAME" varchar(45) NOT NULL,
  PRIMARY KEY ("ID_STATUS")
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "statuses"
--

LOCK TABLES "statuses" WRITE;
/*!40000 ALTER TABLE "statuses" DISABLE KEYS */;
INSERT INTO "statuses" VALUES (1,'AVAILABLE'),(2,'OUT_OF_ORDER'),(3,'UNAVAILABLE'),(4,'NOT_TESTED');
/*!40000 ALTER TABLE "statuses" ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table "users"
--

DROP TABLE IF EXISTS "users";
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE "users" (
  "LOGIN" varchar(45) NOT NULL,
  "PASSWORD" varchar(45) NOT NULL,
  "ID_ROLE" int(10) unsigned DEFAULT NULL,
  "EXPIRATION" date DEFAULT NULL,
  "ENABLED" tinyint(1) NOT NULL DEFAULT '1',
  "LOCKED" tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY ("LOGIN"),
  KEY "FK_USERS_ROLE" ("ID_ROLE"),
  CONSTRAINT "FK_USERS_ROLE" FOREIGN KEY ("ID_ROLE") REFERENCES "roles" ("ID_ROLE") ON DELETE SET NULL
);
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table "users"
--

LOCK TABLES "users" WRITE;
/*!40000 ALTER TABLE "users" DISABLE KEYS */;
INSERT INTO "users" VALUES ('admin ','adm',1,NULL,1,0),('user','usr',2,NULL,1,0);
/*!40000 ALTER TABLE "users" ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-10-07  3:52:59
