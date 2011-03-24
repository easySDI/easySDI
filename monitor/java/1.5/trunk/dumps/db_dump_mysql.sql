-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost Database monitor
-- Generation Time: Mar 24, 2011 at 09:19 
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `monitor`
--

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `ID_ACTION` int(10) unsigned NOT NULL,
  `ID_JOB` int(10) unsigned NOT NULL,
  `ID_ACTION_TYPE` int(10) unsigned NOT NULL,
  `TARGET` varchar(255) DEFAULT NULL,
  `LANGUAGE` char(2) DEFAULT NULL,
  PRIMARY KEY (`ID_ACTION`),
  KEY `FK_ACTION_JOB` (`ID_JOB`),
  KEY `FK_ACTION_TYPE` (`ID_ACTION_TYPE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `actions`
--

LOCK TABLES `actions` WRITE;
/*!40000 ALTER TABLE `actions` DISABLE KEYS */;
/*!40000 ALTER TABLE `actions` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `action_types`
--

CREATE TABLE IF NOT EXISTS `action_types` (
  `ID_ACTION_TYPE` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  PRIMARY KEY (`ID_ACTION_TYPE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `action_types`
--

LOCK TABLES `action_types` WRITE;
/*!40000 ALTER TABLE `action_types` DISABLE KEYS */;
INSERT INTO `action_types` VALUES (1,'E-MAIL'),(2,'RSS');
/*!40000 ALTER TABLE `action_types` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE IF NOT EXISTS `alerts` (
  `ID_ALERT` int(10) unsigned NOT NULL,
  `ID_JOB` int(10) unsigned NOT NULL,
  `ID_OLD_STATUS` int(10) unsigned NOT NULL,
  `ID_NEW_STATUS` int(10) unsigned NOT NULL,
  `CAUSE` text NOT NULL,
  `ALERT_DATE_TIME` datetime NOT NULL,
  `EXPOSE_RSS` tinyint(1) NOT NULL,
  `RESPONSE_DELAY` float NOT NULL,
  `HTTP_CODE` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ID_ALERT`),
  KEY `FK_ALERTS_JOB` (`ID_JOB`),
  KEY `FK_ALERTS_OLD_STATUS` (`ID_OLD_STATUS`),
  KEY `FK_ALERTS_NEW_STATUS` (`ID_NEW_STATUS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `alerts`
--

LOCK TABLES `alerts` WRITE;
/*!40000 ALTER TABLE `alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `alerts` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `http_methods`
--

CREATE TABLE IF NOT EXISTS `http_methods` (
  `ID_HTTP_METHOD` int(10) unsigned NOT NULL,
  `NAME` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_HTTP_METHOD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `http_methods`
--

LOCK TABLES `http_methods` WRITE;
/*!40000 ALTER TABLE `http_methods` DISABLE KEYS */;
INSERT INTO `http_methods` (`ID_HTTP_METHOD`, `NAME`) VALUES
(1, 'GET'),
(2, 'POST');
/*!40000 ALTER TABLE `http_methods` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `ID_JOB` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  `ID_SERVICE_TYPE` int(10) unsigned NOT NULL,
  `SERVICE_URL` varchar(255) NOT NULL,
  `ID_HTTP_METHOD` int(10) unsigned NOT NULL,
  `TEST_INTERVAL` int(10) unsigned NOT NULL,
  `TIMEOUT` int(10) unsigned NOT NULL,
  `BUSINESS_ERRORS` tinyint(1) NOT NULL DEFAULT '0',
  `SLA_START_TIME` datetime NOT NULL,
  `LOGIN` varchar(45) DEFAULT NULL,
  `PASSWORD` varchar(45) DEFAULT NULL,
  `IS_PUBLIC` tinyint(1) NOT NULL DEFAULT '0',
  `IS_AUTOMATIC` tinyint(1) NOT NULL DEFAULT '0',
  `ALLOWS_REALTIME` tinyint(1) NOT NULL DEFAULT '0',
  `TRIGGERS_ALERTS` tinyint(1) NOT NULL DEFAULT '0',
  `ID_STATUS` int(10) unsigned NOT NULL DEFAULT '4',
  `HTTP_ERRORS` tinyint(1) NOT NULL DEFAULT '0',
  `SLA_END_TIME` datetime NOT NULL,
  `STATUS_UPDATE_TIME` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `SAVE_RESPONSE` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_JOB`),
  UNIQUE KEY `UNIQUE_NAME` (`NAME`) USING BTREE,
  KEY `FK_JOBS_SERVICE_TYPE` (`ID_SERVICE_TYPE`),
  KEY `FK_JOBS_HTTP_METHOD` (`ID_HTTP_METHOD`),
  KEY `FK_JOBS_STATUS` (`ID_STATUS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `job_agg_log_entries`
--

CREATE TABLE IF NOT EXISTS `job_agg_log_entries` (
  `DATE_LOG` datetime NOT NULL,
  `ID_JOB` int(10) unsigned NOT NULL,
  `SLA_MEAN_RESP_TIME` float NOT NULL,
  `H24_MEAN_RESP_TIME` float NOT NULL,
  `SLA_AVAILABILITY` float NOT NULL,
  `H24_AVAILABILITY` float NOT NULL,
  `SLA_NB_BIZ_ERRORS` int(10) unsigned NOT NULL,
  `H24_NB_BIZ_ERRORS` int(10) unsigned NOT NULL,
  `SLA_NB_CONN_ERRORS` int(10) unsigned NOT NULL,
  `H24_NB_CONN_ERRORS` int(10) unsigned NOT NULL,
  PRIMARY KEY (`DATE_LOG`,`ID_JOB`),
  KEY `FK_JOB_AGG_LOG_ENTRIES_JOB` (`ID_JOB`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `job_agg_log_entries`
--

LOCK TABLES `job_agg_log_entries` WRITE;
/*!40000 ALTER TABLE `job_agg_log_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_agg_log_entries` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `job_defaults`
--

CREATE TABLE IF NOT EXISTS `job_defaults` (
  `ID_PARAM` int(10) unsigned NOT NULL,
  `COLUMN_NAME` varchar(45) NOT NULL,
  `STRING_VALUE` varchar(45) DEFAULT NULL,
  `VALUE_TYPE` varchar(20) NOT NULL,
  PRIMARY KEY (`ID_PARAM`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `job_defaults`
--

LOCK TABLES `job_defaults` WRITE;
/*!40000 ALTER TABLE `job_defaults` DISABLE KEYS */;
INSERT INTO `job_defaults` (`ID_PARAM`, `COLUMN_NAME`, `STRING_VALUE`, `VALUE_TYPE`) VALUES
(1, 'IS_PUBLIC', 'false', 'bool'),
(2, 'IS_AUTOMATIC', 'false', 'bool'),
(3, 'ALLOWS_REALTIME', 'true', 'bool'),
(4, 'TRIGGERS_ALERTS', 'false', 'bool'),
(5, 'TEST_INTERVAL', '3600', 'int'),
(6, 'TIMEOUT', '30', 'int'),
(7, 'BUSINESS_ERRORS', 'true', 'bool'),
(8, 'HTTP_ERRORS', 'true', 'bool'),
(9, 'SLA_START_TIME', '08:00:00', 'time'),
(10, 'SLA_END_TIME', '18:00:00', 'time');
/*!40000 ALTER TABLE `job_defaults` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `last_ids`
--

CREATE TABLE IF NOT EXISTS `last_ids` (
  `TABLE_NAME` varchar(255) NOT NULL,
  `LAST_ID` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`TABLE_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `last_ids`
--

LOCK TABLES `last_ids` WRITE;
/*!40000 ALTER TABLE `last_ids` DISABLE KEYS */;
INSERT INTO `last_ids` (`TABLE_NAME`, `LAST_ID`) VALUES
('ACTIONS', 2),
('ALERTS', 128),
('HTTP_METHODS', 3),
('JOBS', 24),
('JOB_DEFAULTS', 11),
('LAST_QUERY_RESULTS', 31),
('LOG_ENTRIES', 850),
('OVERVIEW_PAGE', 18),
('OVERVIEW_QUERIES', 8),
('QUERIES', 56),
('QUERY_VALIDATION_RESULTS', 6),
('QUERY_VALIDATION_SETTINGS', 23),
('SERVICE_METHODS', 10),
('SERVICE_TYPES', 8),
('STATUSES', 5);
/*!40000 ALTER TABLE `last_ids` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `last_query_results`
--

CREATE TABLE IF NOT EXISTS `last_query_results` (
  `ID_LAST_QUERY_RESULT` int(10) unsigned NOT NULL,
  `ID_QUERY` int(10) unsigned NOT NULL,
  `PICTURE` blob,
  `XML_RESULT` mediumtext,
  `TEXT_RESULT` mediumtext,
  `PICTURE_URL` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`ID_LAST_QUERY_RESULT`),
  KEY `FK_LAST_QUERY_QUERY` (`ID_QUERY`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `last_query_results`
--

LOCK TABLES `last_query_results` WRITE;
/*!40000 ALTER TABLE `last_query_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `last_query_results` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `log_entries`
--

CREATE TABLE IF NOT EXISTS `log_entries` (
  `ID_LOG_ENTRY` int(10) unsigned NOT NULL,
  `ID_QUERY` int(10) unsigned NOT NULL,
  `REQUEST_TIME` datetime NOT NULL,
  `RESPONSE_DELAY` float NOT NULL,
  `MESSAGE` text NOT NULL,
  `ID_STATUS` int(10) unsigned NOT NULL,
  `HTTP_CODE` int(10) unsigned DEFAULT NULL,
  `EXCEPTION_CODE` varchar(100) DEFAULT NULL,
  `RESPONSE_SIZE` float DEFAULT NULL,
  PRIMARY KEY (`ID_LOG_ENTRY`),
  KEY `fk_log_entries_statuses_STATUS` (`ID_STATUS`),
  KEY `FK_LOG_ENTRIES_QUERY` (`ID_QUERY`),
  KEY `IX_LOG_ENTRIES_ID_QUERY_REQUEST_TIME` (`ID_QUERY`,`REQUEST_TIME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `log_entries`
--

LOCK TABLES `log_entries` WRITE;
/*!40000 ALTER TABLE `log_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_entries` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `overview_page`
--

CREATE TABLE IF NOT EXISTS `overview_page` (
  `ID_OVERVIEW_PAGE` int(10) unsigned NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `IS_PUBLIC` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_OVERVIEW_PAGE`),
  UNIQUE KEY `URL_UNIQUE` (`NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `overview_page`
--

LOCK TABLES `overview_page` WRITE;
/*!40000 ALTER TABLE `overview_page` DISABLE KEYS */;
/*!40000 ALTER TABLE `overview_page` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `overview_queries`
--

CREATE TABLE IF NOT EXISTS `overview_queries` (
  `ID_OVERVIEW_QUERY` int(10) unsigned NOT NULL,
  `ID_OVERVIEW_PAGE` int(10) unsigned NOT NULL,
  `ID_QUERY` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID_OVERVIEW_QUERY`),
  KEY `FK_OVERVIEW_REQ_PAGE` (`ID_OVERVIEW_PAGE`),
  KEY `FK_OW_QUERY_PAGE` (`ID_OVERVIEW_PAGE`),
  KEY `FK_OVERVIEWQUERY_LASTRESULT` (`ID_QUERY`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `overview_queries`
--

LOCK TABLES `overview_queries` WRITE;
/*!40000 ALTER TABLE `overview_queries` DISABLE KEYS */;
/*!40000 ALTER TABLE `overview_queries` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `queries`
--

CREATE TABLE IF NOT EXISTS `queries` (
  `ID_QUERY` int(10) unsigned NOT NULL,
  `ID_JOB` int(10) unsigned NOT NULL,
  `ID_SERVICE_METHOD` int(10) unsigned NOT NULL,
  `ID_STATUS` int(10) unsigned NOT NULL DEFAULT '4',
  `NAME` varchar(45) NOT NULL,
  PRIMARY KEY (`ID_QUERY`),
  KEY `FK_QUERIES_METHOD` (`ID_SERVICE_METHOD`),
  KEY `FK_QUERIES_JOB` (`ID_JOB`),
  KEY `FK_QUERIES_STATUS` (`ID_STATUS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `queries`
--

LOCK TABLES `queries` WRITE;
/*!40000 ALTER TABLE `queries` DISABLE KEYS */;
/*!40000 ALTER TABLE `queries` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `query_agg_log_entries`
--

CREATE TABLE IF NOT EXISTS `query_agg_log_entries` (
  `DATE_LOG` datetime NOT NULL,
  `ID_QUERY` int(10) unsigned NOT NULL,
  `SLA_MEAN_RESP_TIME` float NOT NULL,
  `H24_MEAN_RESP_TIME` float NOT NULL,
  `SLA_AVAILABILITY` float NOT NULL,
  `H24_AVAILABILITY` float NOT NULL,
  `SLA_NB_BIZ_ERRORS` int(10) unsigned NOT NULL,
  `H24_NB_BIZ_ERRORS` int(10) unsigned NOT NULL,
  `SLA_NB_CONN_ERRORS` int(10) unsigned NOT NULL,
  `H24_NB_CONN_ERRORS` int(10) unsigned NOT NULL,
  PRIMARY KEY (`DATE_LOG`,`ID_QUERY`),
  KEY `FK_QUERY_AGG_LOG_ENTRIES_QUERY` (`ID_QUERY`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `query_agg_log_entries`
--

LOCK TABLES `query_agg_log_entries` WRITE;
/*!40000 ALTER TABLE `query_agg_log_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `query_agg_log_entries` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `query_params`
--

CREATE TABLE IF NOT EXISTS `query_params` (
  `ID_QUERY` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  `VALUE` text,
  PRIMARY KEY (`ID_QUERY`,`NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `query_params`
--

LOCK TABLES `query_params` WRITE;
/*!40000 ALTER TABLE `query_params` DISABLE KEYS */;
/*!40000 ALTER TABLE `query_params` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `query_validation_results`
--

CREATE TABLE IF NOT EXISTS `query_validation_results` (
  `ID_QUERY_VALIDATION_RESULT` int(11) NOT NULL,
  `ID_QUERY` int(10) unsigned NOT NULL,
  `SIZE_VALIDATION_RESULT` tinyint(1) DEFAULT NULL,
  `RESPONSE_SIZE` float DEFAULT NULL,
  `TIME_VALIDATION_RESULT` tinyint(1) DEFAULT NULL,
  `DELIVERY_TIME` float DEFAULT NULL,
  `XPATH_VALIDATION_RESULT` tinyint(1) DEFAULT NULL,
  `XPATH_VALIDATION_OUTPUT` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`ID_QUERY_VALIDATION_RESULT`,`ID_QUERY`),
  KEY `fk_query_validation_results_queries1` (`ID_QUERY`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `query_validation_results`
--

LOCK TABLES `query_validation_results` WRITE;
/*!40000 ALTER TABLE `query_validation_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `query_validation_results` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `query_validation_settings`
--

CREATE TABLE IF NOT EXISTS `query_validation_settings` (
  `ID_QUERY_VALIDATION_SETTINGS` int(10) NOT NULL,
  `ID_QUERY` int(10) unsigned NOT NULL,
  `USE_SIZE_VALIDATION` tinyint(1) NOT NULL DEFAULT '0',
  `NORM_SIZE` float DEFAULT NULL,
  `NORM_SIZE_TOLERANCE` float DEFAULT NULL,
  `USE_TIME_VALIDATION` tinyint(1) NOT NULL DEFAULT '0',
  `NORM_TIME` float DEFAULT NULL,
  `USE_XPATH_VALIDATION` tinyint(1) NOT NULL DEFAULT '0',
  `XPATH_EXPRESSION` varchar(1000) DEFAULT NULL,
  `XPATH_EXPECTED_OUTPUT` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`ID_QUERY_VALIDATION_SETTINGS`,`ID_QUERY`),
  KEY `fk_query_validation_settings_queries1` (`ID_QUERY`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `query_validation_settings`
--

LOCK TABLES `query_validation_settings` WRITE;
/*!40000 ALTER TABLE `query_validation_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `query_validation_settings` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `ID_ROLE` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  `RANK` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID_ROLE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ROLE_ADMIN',1),(2,'ROLE_USER',3);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `service_methods`
--

CREATE TABLE IF NOT EXISTS `service_methods` (
  `ID_SERVICE_METHOD` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  PRIMARY KEY (`ID_SERVICE_METHOD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `service_methods`
--

LOCK TABLES `service_methods` WRITE;
/*!40000 ALTER TABLE `service_methods` DISABLE KEYS */;
INSERT INTO `service_methods` VALUES (1,'GetCapabilities'),(2,'GetMap'),(3,'GetFeature'),(4,'GetRecordById'),(5,'GetTile'),(6,'GetRecords'),(7,'GetCoverage'),(8,'DescribeSensor');
/*!40000 ALTER TABLE `service_methods` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `service_types`
--

CREATE TABLE IF NOT EXISTS `service_types` (
  `ID_SERVICE_TYPE` int(10) unsigned NOT NULL,
  `NAME` varchar(20) NOT NULL,
  `VERSION` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_SERVICE_TYPE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `service_types`
--

LOCK TABLES `service_types` WRITE;
/*!40000 ALTER TABLE `service_types` DISABLE KEYS */;
INSERT INTO `service_types` VALUES (1,'WMS','1.1.1'),(2,'WFS','1.1.0'),(4,'WMTS','1.0.0'),(5,'CSW','2.0.2'),(6,'SOS','1.0.0'),(7,'WCS','1.0.0');
/*!40000 ALTER TABLE `service_types` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `service_types_methods`
--

CREATE TABLE IF NOT EXISTS `service_types_methods` (
  `ID_SERVICE_TYPE` int(10) unsigned NOT NULL,
  `ID_SERVICE_METHOD` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID_SERVICE_TYPE`,`ID_SERVICE_METHOD`),
  KEY `FK_SERVICE_TYPES_METHODS_METHOD` (`ID_SERVICE_METHOD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `service_types_methods`
--

LOCK TABLES `service_types_methods` WRITE;
/*!40000 ALTER TABLE `service_types_methods` DISABLE KEYS */;
INSERT INTO `service_types_methods` VALUES (1,1),(2,1),(4,1),(5,1),(6,1),(7,1),(1,2),(2,3),(5,4),(4,5),(5,6),(7,7),(6,8);
/*!40000 ALTER TABLE `service_types_methods` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE IF NOT EXISTS `statuses` (
  `ID_STATUS` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  PRIMARY KEY (`ID_STATUS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'AVAILABLE'),(2,'OUT_OF_ORDER'),(3,'UNAVAILABLE'),(4,'NOT_TESTED');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `LOGIN` varchar(45) NOT NULL,
  `PASSWORD` varchar(45) NOT NULL,
  `ID_ROLE` int(10) unsigned DEFAULT NULL,
  `EXPIRATION` date DEFAULT NULL,
  `ENABLED` tinyint(1) NOT NULL DEFAULT '1',
  `LOCKED` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`LOGIN`),
  KEY `FK_USERS_ROLE` (`ID_ROLE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('admin','adm',1,NULL,1,0),('user','usr',2,NULL,1,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

--
-- Table structure for table `overview_query_view`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `overview_query_view` AS select (select count(0) AS `count(0)` from `overview_queries` where ((`overview_queries`.`ID_QUERY` = `q`.`ID_QUERY`) and (`overview_queries`.`ID_OVERVIEW_PAGE` = `p`.`ID_OVERVIEW_PAGE`))) AS `QUERY_IS_PUBLIC`,`p`.`ID_OVERVIEW_PAGE` AS `ID_OVERVIEW_PAGE`,`p`.`NAME` AS `NAME_OVERVIEW_PAGE`,`q`.`ID_QUERY` AS `ID_QUERY`,`q`.`NAME` AS `NAME_QUERY`,`l`.`ID_LAST_QUERY_RESULT` AS `ID_LAST_QUERY_RESULT` from ((`queries` `q` left join `last_query_results` `l` on((`q`.`ID_QUERY` = `l`.`ID_QUERY`))) join `overview_page` `p`) where `q`.`ID_JOB` in (select `jobs`.`ID_JOB` AS `ID_JOB` from `jobs` where (`jobs`.`SAVE_RESPONSE` = 1));

--
-- Dumping data for table `overview_query_view`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `actions`
--
ALTER TABLE `actions`
  ADD CONSTRAINT `FK_ACTION_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_ACTION_TYPE` FOREIGN KEY (`ID_ACTION_TYPE`) REFERENCES `action_types` (`ID_ACTION_TYPE`);

--
-- Constraints for table `alerts`
--
ALTER TABLE `alerts`
  ADD CONSTRAINT `FK_ALERTS_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_ALERTS_NEW_STATUS` FOREIGN KEY (`ID_NEW_STATUS`) REFERENCES `statuses` (`ID_STATUS`),
  ADD CONSTRAINT `FK_ALERTS_OLD_STATUS` FOREIGN KEY (`ID_OLD_STATUS`) REFERENCES `statuses` (`ID_STATUS`);

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `FK_JOBS_HTTP_METHOD` FOREIGN KEY (`ID_HTTP_METHOD`) REFERENCES `http_methods` (`ID_HTTP_METHOD`),
  ADD CONSTRAINT `FK_JOBS_SERVICE_TYPE` FOREIGN KEY (`ID_SERVICE_TYPE`) REFERENCES `service_types` (`ID_SERVICE_TYPE`),
  ADD CONSTRAINT `FK_JOBS_STATUS` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuses` (`ID_STATUS`);


--
-- Constraints for table `job_agg_log_entries`
--
ALTER TABLE `job_agg_log_entries`
  ADD CONSTRAINT `FK_JOB_AGG_LOG_ENTRIES_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `last_query_results`
--
ALTER TABLE `last_query_results`
  ADD CONSTRAINT `FK_LAST_QUERY_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `log_entries`
--
ALTER TABLE `log_entries`
  ADD CONSTRAINT `FK_LOG_ENTRIES_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_log_entries_statuses_STATUS` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuses` (`ID_STATUS`);

--
-- Constraints for table `overview_queries`
--
ALTER TABLE `overview_queries`
  ADD CONSTRAINT `FK_OVERVIEWQUERY_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_OW_QUERY_PAGE` FOREIGN KEY (`ID_OVERVIEW_PAGE`) REFERENCES `overview_page` (`ID_OVERVIEW_PAGE`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `queries`
--
ALTER TABLE `queries`
  ADD CONSTRAINT `FK_QUERIES_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_QUERIES_METHOD` FOREIGN KEY (`ID_SERVICE_METHOD`) REFERENCES `service_methods` (`ID_SERVICE_METHOD`),
  ADD CONSTRAINT `FK_QUERIES_STATUS` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuses` (`ID_STATUS`);

--
-- Constraints for table `query_agg_log_entries`
--
ALTER TABLE `query_agg_log_entries`
  ADD CONSTRAINT `FK_QUERY_AGG_LOG_ENTRIES_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE;

--
-- Constraints for table `query_params`
--
ALTER TABLE `query_params`
  ADD CONSTRAINT `FK_QUERY_PARAMS_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE;

--
-- Constraints for table `query_validation_results`
--
ALTER TABLE `query_validation_results`
  ADD CONSTRAINT `fk_query_validation_results_queries1` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `query_validation_settings`
--
ALTER TABLE `query_validation_settings`
  ADD CONSTRAINT `fk_query_validation_settings_queries1` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `service_types_methods`
--
ALTER TABLE `service_types_methods`
  ADD CONSTRAINT `FK_SERVICE_TYPES_METHODS_METHOD` FOREIGN KEY (`ID_SERVICE_METHOD`) REFERENCES `service_methods` (`ID_SERVICE_METHOD`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_SERVICE_TYPES_METHODS_TYPE` FOREIGN KEY (`ID_SERVICE_TYPE`) REFERENCES `service_types` (`ID_SERVICE_TYPE`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_USERS_ROLE` FOREIGN KEY (`ID_ROLE`) REFERENCES `roles` (`ID_ROLE`) ON DELETE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
