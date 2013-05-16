<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 */


defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.folder' );

	

function com_install(){
	
$migrateServletTablesQuery ="SET FOREIGN_KEY_CHECKS=0;

			DROP TABLE IF EXISTS `actions`;
			CREATE TABLE `actions` (
			  `ID_ACTION` int(10) unsigned NOT NULL,
			  `ID_JOB` int(10) unsigned NOT NULL,
			  `ID_ACTION_TYPE` int(10) unsigned NOT NULL,
			  `TARGET` varchar(255) DEFAULT NULL,
			  `LANGUAGE` char(2) DEFAULT NULL,
			  PRIMARY KEY (`ID_ACTION`),
			  KEY `FK_ACTION_JOB` (`ID_JOB`),
			  KEY `FK_ACTION_TYPE` (`ID_ACTION_TYPE`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
			
			
			DROP TABLE IF EXISTS `action_types`;
			CREATE TABLE `action_types` (
			  `ID_ACTION_TYPE` int(10) unsigned NOT NULL,
			  `NAME` varchar(45) NOT NULL,
			  PRIMARY KEY (`ID_ACTION_TYPE`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
			
			
			INSERT INTO `action_types` VALUES ('1', 'E-MAIL');
			INSERT INTO `action_types` VALUES ('2', 'RSS');
			
			
			DROP TABLE IF EXISTS `alerts`;
			CREATE TABLE `alerts` (
			  `ID_ALERT` int(10) unsigned NOT NULL,
			  `ID_JOB` int(10) unsigned NOT NULL,
			  `ID_OLD_STATUS` int(10) unsigned NOT NULL,
			  `ID_NEW_STATUS` int(10) unsigned NOT NULL,
			  `CAUSE` text NOT NULL,
			  `ALERT_DATE_TIME` datetime NOT NULL,
			  `EXPOSE_RSS` tinyint(1) NOT NULL,
			  `RESPONSE_DELAY` float NOT NULL,
			  `HTTP_CODE` int(10) unsigned DEFAULT NULL,
			  `IMAGE` mediumblob,
  			  `CONTENT_TYPE` varchar(50) DEFAULT NULL,
			  PRIMARY KEY (`ID_ALERT`),
			  KEY `FK_ALERTS_JOB` (`ID_JOB`),
			  KEY `FK_ALERTS_OLD_STATUS` (`ID_OLD_STATUS`),
			  KEY `FK_ALERTS_NEW_STATUS` (`ID_NEW_STATUS`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
			
			DROP TABLE IF EXISTS `holidays`;
			CREATE TABLE IF NOT EXISTS `holidays` (
  				`ID_HOLIDAYS` int(10) unsigned NOT NULL,
  				`NAME` varchar(45) DEFAULT NULL,
  				`DATE` datetime NOT NULL,
  				PRIMARY KEY (`ID_HOLIDAYS`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			
			DROP TABLE IF EXISTS `http_methods`;
			CREATE TABLE `http_methods` (
			  `ID_HTTP_METHOD` int(10) unsigned NOT NULL,
			  `NAME` varchar(10) NOT NULL,
			  PRIMARY KEY (`ID_HTTP_METHOD`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
			
			
			INSERT INTO `http_methods` VALUES ('1', 'GET');
			INSERT INTO `http_methods` VALUES ('2', 'POST');
			
			
			DROP TABLE IF EXISTS `jobs`;
			CREATE TABLE `jobs` (
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
			  `RUN_SIMULTANEOUS` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`ID_JOB`),
			  UNIQUE KEY `UNIQUE_NAME` (`NAME`) USING BTREE,
			  KEY `FK_JOBS_SERVICE_TYPE` (`ID_SERVICE_TYPE`),
			  KEY `FK_JOBS_HTTP_METHOD` (`ID_HTTP_METHOD`),
			  KEY `FK_JOBS_STATUS` (`ID_STATUS`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			DROP TABLE IF EXISTS `job_agg_hour_log_entries`;
			CREATE TABLE IF NOT EXISTS `job_agg_hour_log_entries` (
  				`DATE_LOG` datetime NOT NULL,
  				`ID_JOB` int(10) unsigned NOT NULL,
  				`H1_MEAN_RESP_TIME` float NOT NULL,
  				`H1_MEAN_RESP_TIME_INSPIRE` float NOT NULL,
  				`H1_AVAILABILITY` float NOT NULL,
  				`H1_AVAILABILITY_INSPIRE` float NOT NULL,
  				`H1_NB_BIZ_ERRORS` int(10) unsigned NOT NULL,
  				`H1_NB_BIZ_ERRORS_INSPIRE` int(10) unsigned NOT NULL,
  				`H1_NB_CONN_ERRORS` int(10) unsigned NOT NULL,
  				`H1_NB_CONN_ERRORS_INSPIRE` int(10) unsigned NOT NULL,
  				`H1_MAX_RESP_TIME` float NOT NULL DEFAULT '0',
  				`H1_MIN_RESP_TIME` float NOT NULL DEFAULT '0',
  				`H1_MAX_RESP_TIME_INSPIRE` float NOT NULL DEFAULT '0',
  				`H1_MIN_RESP_TIME_INSPIRE` float NOT NULL DEFAULT '0',
				`H1_UNAVAILABILITY` float NOT NULL DEFAULT '0',
				`H1_UNAVAILABILITY_INSPIRE` float NOT NULL DEFAULT '0',
				`H1_FAILURE` float NOT NULL DEFAULT '0',
				`H1_FAILURE_INSPIRE` float NOT NULL DEFAULT '0',
				`H1_UNTESTED` float NOT NULL DEFAULT '0',
				`H1_UNTESTED_INSPIRE` float NOT NULL DEFAULT '0',
  				PRIMARY KEY (`DATE_LOG`,`ID_JOB`),
  			KEY `FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB` (`ID_JOB`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			DROP TABLE IF EXISTS `job_agg_log_entries`;
			CREATE TABLE `job_agg_log_entries` (
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
			  `H24_MAX_RESP_TIME` float NOT NULL DEFAULT '0',
  			  `H24_MIN_RESP_TIME` float NOT NULL DEFAULT '0',
  			  `SLA_MAX_RESP_TIME` float NOT NULL DEFAULT '0',
  			  `SLA_MIN_RESP_TIME` float NOT NULL DEFAULT '0',
			  `SLA_UNAVAILABILITY` float NOT NULL DEFAULT '0',
			  `H24_UNAVAILABILITY` float NOT NULL DEFAULT '0',
			  `SLA_FAILURE` float NOT NULL DEFAULT '0',
			  `H24_FAILURE` float NOT NULL DEFAULT '0',
			  `SLA_UNTESTED` float NOT NULL DEFAULT '0',
			  `H24_UNTESTED` float NOT NULL DEFAULT '0',
			  PRIMARY KEY (`DATE_LOG`,`ID_JOB`),
			  KEY `FK_JOB_AGG_LOG_ENTRIES_JOB` (`ID_JOB`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			DROP TABLE IF EXISTS `job_defaults`;
			CREATE TABLE `job_defaults` (
			  `ID_PARAM` int(10) unsigned NOT NULL,
			  `COLUMN_NAME` varchar(45) NOT NULL,
			  `STRING_VALUE` varchar(45) DEFAULT NULL,
			  `VALUE_TYPE` varchar(20) NOT NULL,
			  PRIMARY KEY (`ID_PARAM`) USING BTREE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			INSERT INTO `job_defaults` VALUES ('1', 'IS_PUBLIC', 'false', 'bool');
			INSERT INTO `job_defaults` VALUES ('2', 'IS_AUTOMATIC', 'false', 'bool');
			INSERT INTO `job_defaults` VALUES ('3', 'ALLOWS_REALTIME', 'true', 'bool');
			INSERT INTO `job_defaults` VALUES ('4', 'TRIGGERS_ALERTS', 'false', 'bool');
			INSERT INTO `job_defaults` VALUES ('5', 'TEST_INTERVAL', '3600', 'int');
			INSERT INTO `job_defaults` VALUES ('6', 'TIMEOUT', '30', 'int');
			INSERT INTO `job_defaults` VALUES ('7', 'BUSINESS_ERRORS', 'true', 'bool');
			INSERT INTO `job_defaults` VALUES ('8', 'HTTP_ERRORS', 'true', 'bool');
			INSERT INTO `job_defaults` VALUES ('9', 'SLA_START_TIME', '08:00:00', 'time');
			INSERT INTO `job_defaults` VALUES ('10', 'SLA_END_TIME', '18:00:00', 'time');
			INSERT INTO `job_defaults` VALUES ('11', 'RUN_SIMULATANEOUS', 'false', 'bool');
            INSERT INTO `job_defaults` VALUES ('12', 'SAVE_RESPONSE', 'false', 'bool');
			
			
			DROP TABLE IF EXISTS `last_ids`;
			CREATE TABLE `last_ids` (
			  `TABLE_NAME` varchar(255) NOT NULL,
			  `LAST_ID` int(10) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`TABLE_NAME`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			INSERT INTO `last_ids` VALUES ('ACTIONS', '2');
			INSERT INTO `last_ids` VALUES ('ALERTS', '161');
			INSERT INTO `last_ids` VALUES ('HTTP_METHODS', '3');
			INSERT INTO `last_ids` VALUES ('JOBS', '45');
			INSERT INTO `last_ids` VALUES ('JOB_DEFAULTS', '11');
			INSERT INTO `last_ids` VALUES ('LAST_QUERY_RESULTS', '39');
			INSERT INTO `last_ids` VALUES ('LOG_ENTRIES', '1046');
			INSERT INTO `last_ids` VALUES ('OVERVIEW_PAGE', '18');
			INSERT INTO `last_ids` VALUES ('OVERVIEW_QUERIES', '8');
			INSERT INTO `last_ids` VALUES ('QUERIES', '94');
			INSERT INTO `last_ids` VALUES ('QUERY_VALIDATION_RESULTS', '12');
			INSERT INTO `last_ids` VALUES ('QUERY_VALIDATION_SETTINGS', '48');
			INSERT INTO `last_ids` VALUES ('SERVICE_METHODS', '10');
			INSERT INTO `last_ids` VALUES ('SERVICE_TYPES', '8');
			INSERT INTO `last_ids` VALUES ('STATUSES', '5');
			
			DROP TABLE IF EXISTS `last_query_results`;
			CREATE TABLE `last_query_results` (
			  `ID_LAST_QUERY_RESULT` int(10) unsigned NOT NULL,
			  `ID_QUERY` int(10) unsigned NOT NULL,
			   `DATA` mediumblob,
			  `XML_RESULT` mediumtext,
			  `TEXT_RESULT` mediumtext,
			  `PICTURE_URL` varchar(1000) DEFAULT NULL,
			  `CONTENT_TYPE` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`ID_LAST_QUERY_RESULT`),
			  KEY `FK_LAST_QUERY_QUERY` (`ID_QUERY`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			DROP TABLE IF EXISTS `log_entries`;
			CREATE TABLE `log_entries` (
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
			
			DROP TABLE IF EXISTS `overview_page`;
			CREATE TABLE `overview_page` (
			  `ID_OVERVIEW_PAGE` int(10) unsigned NOT NULL,
			  `NAME` varchar(255) NOT NULL,
			  `IS_PUBLIC` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`ID_OVERVIEW_PAGE`),
			  UNIQUE KEY `URL_UNIQUE` (`NAME`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			DROP TABLE IF EXISTS `overview_queries`;
			CREATE TABLE `overview_queries` (
			  `ID_OVERVIEW_QUERY` int(10) unsigned NOT NULL,
			  `ID_OVERVIEW_PAGE` int(10) unsigned NOT NULL,
			  `ID_QUERY` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`ID_OVERVIEW_QUERY`),
			  KEY `FK_OVERVIEW_REQ_PAGE` (`ID_OVERVIEW_PAGE`),
			  KEY `FK_OW_QUERY_PAGE` (`ID_OVERVIEW_PAGE`),
			  KEY `FK_OVERVIEWQUERY_LASTRESULT` (`ID_QUERY`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			DROP TABLE IF EXISTS `periods`;
			CREATE TABLE IF NOT EXISTS `periods` (
  				`ID_PERIODS` int(10) unsigned NOT NULL,
  				`ID_SLA` int(10) unsigned NOT NULL,
  				`NAME` varchar(45) DEFAULT NULL,
  				`MONDAY` tinyint(1) DEFAULT '0',
  				`TUESDAY` tinyint(1) DEFAULT '0',
  				`WEDNESDAY` tinyint(1) DEFAULT '0',
  				`THURSDAY` tinyint(1) DEFAULT '0',
  				`FRIDAY` tinyint(1) DEFAULT '0',
  				`SATURDAY` tinyint(1) DEFAULT '0',
  				`SUNDAY` tinyint(1) DEFAULT '0',
  				`HOLIDAYS` tinyint(1) DEFAULT '0',
  				`SLA_START_TIME` time NOT NULL,
  				`SLA_END_TIME` time NOT NULL,
 				`INCLUDE` tinyint(1) DEFAULT '0',
  				`DATE` varchar(45) DEFAULT NULL,
  				PRIMARY KEY (`ID_PERIODS`),
  				KEY `FK_PERIODS_SLA` (`ID_SLA`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			DROP TABLE IF EXISTS `queries`;
			CREATE TABLE `queries` (
			  `ID_QUERY` int(10) unsigned NOT NULL,
			  `ID_JOB` int(10) unsigned NOT NULL,
			  `ID_SERVICE_METHOD` int(10) unsigned NOT NULL,
			  `ID_STATUS` int(10) unsigned NOT NULL DEFAULT '4',
			  `NAME` varchar(45) NOT NULL,
			  `SOAP_URL` varchar(250) DEFAULT NULL,
			  PRIMARY KEY (`ID_QUERY`),
			  KEY `FK_QUERIES_METHOD` (`ID_SERVICE_METHOD`),
			  KEY `FK_QUERIES_JOB` (`ID_JOB`),
			  KEY `FK_QUERIES_STATUS` (`ID_STATUS`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
			
			DROP TABLE IF EXISTS `query_agg_hour_log_entries`;
			CREATE TABLE IF NOT EXISTS `query_agg_hour_log_entries` (
  				`DATE_LOG` datetime NOT NULL,
  				`ID_QUERY` int(10) unsigned NOT NULL,
  				`H1_MEAN_RESP_TIME` float NOT NULL,
 				`H1_MEAN_RESP_TIME_INSPIRE` float NOT NULL,
  				`H1_AVAILABILITY` float NOT NULL,
  				`H1_AVAILABILITY_INSPIRE` float NOT NULL,
  				`H1_NB_BIZ_ERRORS` int(10) unsigned NOT NULL,
  				`H1_NB_BIZ_ERRORS_INSPIRE` int(10) unsigned NOT NULL,
  				`H1_NB_CONN_ERRORS` int(10) unsigned NOT NULL,
  				`H1_NB_CONN_ERRORS_INSPIRE` int(10) unsigned NOT NULL,
  				`H1_MAX_RESP_TIME` float NOT NULL DEFAULT '0',
  				`H1_MIN_RESP_TIME` float NOT NULL DEFAULT '0',
  				`H1_MAX_RESP_TIME_INSPIRE` float NOT NULL DEFAULT '0',
  				`H1_MIN_RESP_TIME_INSPIRE` float NOT NULL DEFAULT '0',
				`H1_UNAVAILABILITY` float NOT NULL DEFAULT '0',
				`H1_UNAVAILABILITY_INSPIRE` float NOT NULL DEFAULT '0',
				`H1_FAILURE` float NOT NULL DEFAULT '0',
				`H1_FAILURE_INSPIRE` float NOT NULL DEFAULT '0',
				`H1_UNTESTED` float NOT NULL DEFAULT '0',
				`H1_UNTESTED_INSPIRE` float NOT NULL DEFAULT '0',
  				PRIMARY KEY (`DATE_LOG`,`ID_QUERY`),
  				KEY `FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY` (`ID_QUERY`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			
			DROP TABLE IF EXISTS `query_agg_log_entries`;
			CREATE TABLE `query_agg_log_entries` (
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
			  `H24_MAX_RESP_TIME` float NOT NULL DEFAULT '0',
  			  `H24_MIN_RESP_TIME` float NOT NULL DEFAULT '0',
  			  `SLA_MAX_RESP_TIME` float NOT NULL DEFAULT '0',
  			  `SLA_MIN_RESP_TIME` float NOT NULL DEFAULT '0',
			  `SLA_UNAVAILABILITY` float NOT NULL DEFAULT '0',
			  `H24_UNAVAILABILITY` float NOT NULL DEFAULT '0',
			  `SLA_FAILURE` float NOT NULL DEFAULT '0',
			  `H24_FAILURE` float NOT NULL DEFAULT '0',
			  `SLA_UNTESTED` float NOT NULL DEFAULT '0',
			  `H24_UNTESTED` float NOT NULL DEFAULT '0',
			  PRIMARY KEY (`DATE_LOG`,`ID_QUERY`),
			  KEY `FK_QUERY_AGG_LOG_ENTRIES_QUERY` (`ID_QUERY`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			DROP TABLE IF EXISTS `query_params`;
			CREATE TABLE `query_params` (
			  `ID_QUERY` int(10) unsigned NOT NULL,
			  `NAME` varchar(45) NOT NULL,
			  `VALUE` text,
			  PRIMARY KEY (`ID_QUERY`,`NAME`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			DROP TABLE IF EXISTS `query_validation_results`;
			CREATE TABLE `query_validation_results` (
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
			
			DROP TABLE IF EXISTS `query_validation_settings`;
			CREATE TABLE `query_validation_settings` (
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
			
			DROP TABLE IF EXISTS `roles`;
			CREATE TABLE `roles` (
			  `ID_ROLE` int(10) unsigned NOT NULL,
			  `NAME` varchar(45) NOT NULL,
			  `RANK` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`ID_ROLE`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
			
			INSERT INTO `roles` VALUES ('1', 'ROLE_ADMIN', '1');
			INSERT INTO `roles` VALUES ('2', 'ROLE_USER', '3');
			
			DROP TABLE IF EXISTS `service_methods`;
			CREATE TABLE `service_methods` (
			  `ID_SERVICE_METHOD` int(10) unsigned NOT NULL,
			  `NAME` varchar(45) NOT NULL,
			  PRIMARY KEY (`ID_SERVICE_METHOD`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
			
			INSERT INTO `service_methods` VALUES ('1', 'GetCapabilities');
			INSERT INTO `service_methods` VALUES ('2', 'GetMap');
			INSERT INTO `service_methods` VALUES ('3', 'GetFeature');
			INSERT INTO `service_methods` VALUES ('4', 'GetRecordById');
			INSERT INTO `service_methods` VALUES ('5', 'GetTile');
			INSERT INTO `service_methods` VALUES ('6', 'GetRecords');
			INSERT INTO `service_methods` VALUES ('7', 'GetCoverage');
			INSERT INTO `service_methods` VALUES ('8', 'DescribeSensor');
			INSERT INTO `service_methods` VALUES ('9', 'SOAP 1.1');
			INSERT INTO `service_methods` VALUES ('10', 'SOAP 1.2');
			INSERT INTO `service_methods` VALUES ('11', 'HTTP POST');
			INSERT INTO `service_methods` VALUES ('12', 'HTTP GET');
			
			DROP TABLE IF EXISTS `service_types`;
			CREATE TABLE `service_types` (
			  `ID_SERVICE_TYPE` int(10) unsigned NOT NULL,
			  `NAME` varchar(20) NOT NULL,
			  `VERSION` varchar(10) NOT NULL,
			  PRIMARY KEY (`ID_SERVICE_TYPE`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
			
			INSERT INTO `service_types` VALUES ('1', 'WMS', '1.1.1');
			INSERT INTO `service_types` VALUES ('2', 'WFS', '1.1.0');
			INSERT INTO `service_types` VALUES ('4', 'WMTS', '1.0.0');
			INSERT INTO `service_types` VALUES ('5', 'CSW', '2.0.2');
			INSERT INTO `service_types` VALUES ('6', 'SOS', '1.0.0');
			INSERT INTO `service_types` VALUES ('7', 'WCS', '1.0.0');
			INSERT INTO `service_types` VALUES ('8', 'ALL', '0');
			
			DROP TABLE IF EXISTS `service_types_methods`;
			CREATE TABLE `service_types_methods` (
			  `ID_SERVICE_TYPE` int(10) unsigned NOT NULL,
			  `ID_SERVICE_METHOD` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`ID_SERVICE_TYPE`,`ID_SERVICE_METHOD`),
			  KEY `FK_SERVICE_TYPES_METHODS_METHOD` (`ID_SERVICE_METHOD`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			INSERT INTO `service_types_methods` VALUES ('1', '1');
			INSERT INTO `service_types_methods` VALUES ('2', '1');
			INSERT INTO `service_types_methods` VALUES ('4', '1');
			INSERT INTO `service_types_methods` VALUES ('5', '1');
			INSERT INTO `service_types_methods` VALUES ('6', '1');
			INSERT INTO `service_types_methods` VALUES ('7', '1');
			INSERT INTO `service_types_methods` VALUES ('8', '1');
			INSERT INTO `service_types_methods` VALUES ('1', '2');
			INSERT INTO `service_types_methods` VALUES ('8', '2');
			INSERT INTO `service_types_methods` VALUES ('2', '3');
			INSERT INTO `service_types_methods` VALUES ('8', '3');
			INSERT INTO `service_types_methods` VALUES ('5', '4');
			INSERT INTO `service_types_methods` VALUES ('8', '4');
			INSERT INTO `service_types_methods` VALUES ('4', '5');
			INSERT INTO `service_types_methods` VALUES ('8', '5');
			INSERT INTO `service_types_methods` VALUES ('5', '6');
			INSERT INTO `service_types_methods` VALUES ('8', '6');
			INSERT INTO `service_types_methods` VALUES ('7', '7');
			INSERT INTO `service_types_methods` VALUES ('8', '7');
			INSERT INTO `service_types_methods` VALUES ('6', '8');
			INSERT INTO `service_types_methods` VALUES ('8', '8');
			INSERT INTO `service_types_methods` VALUES ('8', '9');
			INSERT INTO `service_types_methods` VALUES ('8', '10');
			INSERT INTO `service_types_methods` VALUES ('8', '11');
			INSERT INTO `service_types_methods` VALUES ('8', '12');
			
			DROP TABLE IF EXISTS `sla`;
			CREATE TABLE IF NOT EXISTS `sla` (
  				`ID_SLA` int(10) unsigned NOT NULL,
  				`NAME` varchar(45) NOT NULL,
  				`EXCLUDE_WORST` tinyint(1) DEFAULT '0',
  				`MEASURE_TIME_TO_FIRST` tinyint(1) DEFAULT '0',
  				PRIMARY KEY (`ID_SLA`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			
			DROP TABLE IF EXISTS `statuses`;
			CREATE TABLE `statuses` (
			  `ID_STATUS` int(10) unsigned NOT NULL,
			  `NAME` varchar(45) NOT NULL,
			  PRIMARY KEY (`ID_STATUS`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
			
			INSERT INTO `statuses` VALUES ('1', 'AVAILABLE');
			INSERT INTO `statuses` VALUES ('2', 'OUT_OF_ORDER');
			INSERT INTO `statuses` VALUES ('3', 'UNAVAILABLE');
			INSERT INTO `statuses` VALUES ('4', 'NOT_TESTED');
			
			DROP TABLE IF EXISTS `users`;
			CREATE TABLE `users` (
			  `LOGIN` varchar(45) NOT NULL,
			  `PASSWORD` varchar(45) NOT NULL,
			  `ID_ROLE` int(10) unsigned DEFAULT NULL,
			  `EXPIRATION` date DEFAULT NULL,
			  `ENABLED` tinyint(1) NOT NULL DEFAULT '1',
			  `LOCKED` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`LOGIN`),
			  KEY `FK_USERS_ROLE` (`ID_ROLE`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
			INSERT INTO `users` VALUES ('Admin', 'adm', '1', null, '1', '0');
			INSERT INTO `users` VALUES ('user', 'usr', '2', null, '1', '0');
			
			DROP VIEW IF EXISTS `overview_query_view`;
			CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `overview_query_view` AS select (select count(0) AS `count(0)` from `overview_queries` where ((`overview_queries`.`ID_QUERY` = `q`.`ID_QUERY`) and (`overview_queries`.`ID_OVERVIEW_PAGE` = `p`.`ID_OVERVIEW_PAGE`))) AS `QUERY_IS_PUBLIC`,`p`.`ID_OVERVIEW_PAGE` AS `ID_OVERVIEW_PAGE`,`p`.`NAME` AS `NAME_OVERVIEW_PAGE`,`q`.`ID_QUERY` AS `ID_QUERY`,`q`.`NAME` AS `NAME_QUERY`,`l`.`ID_LAST_QUERY_RESULT` AS `ID_LAST_QUERY_RESULT` from ((`queries` `q` left join `last_query_results` `l` on((`q`.`ID_QUERY` = `l`.`ID_QUERY`))) join `overview_page` `p`) where `q`.`ID_JOB` in (select `jobs`.`ID_JOB` AS `ID_JOB` from `jobs` where (`jobs`.`SAVE_RESPONSE` = 1));
			
			ALTER TABLE `actions`
  			ADD CONSTRAINT `FK_ACTION_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE,
  			ADD CONSTRAINT `FK_ACTION_TYPE` FOREIGN KEY (`ID_ACTION_TYPE`) REFERENCES `action_types` (`ID_ACTION_TYPE`);
			
  			ALTER TABLE `alerts`
  			ADD CONSTRAINT `FK_ALERTS_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE,
  			ADD CONSTRAINT `FK_ALERTS_NEW_STATUS` FOREIGN KEY (`ID_NEW_STATUS`) REFERENCES `statuses` (`ID_STATUS`),
  			ADD CONSTRAINT `FK_ALERTS_OLD_STATUS` FOREIGN KEY (`ID_OLD_STATUS`) REFERENCES `statuses` (`ID_STATUS`);
  			
  			ALTER TABLE `jobs`
  			ADD CONSTRAINT `FK_JOBS_HTTP_METHOD` FOREIGN KEY (`ID_HTTP_METHOD`) REFERENCES `http_methods` (`ID_HTTP_METHOD`),
  			ADD CONSTRAINT `FK_JOBS_SERVICE_TYPE` FOREIGN KEY (`ID_SERVICE_TYPE`) REFERENCES `service_types` (`ID_SERVICE_TYPE`),
  			ADD CONSTRAINT `FK_JOBS_STATUS` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuses` (`ID_STATUS`);
  			
  			ALTER TABLE `job_agg_hour_log_entries`
  			ADD CONSTRAINT `FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE ON UPDATE CASCADE;
  			
  			ALTER TABLE `job_agg_log_entries`
  			ADD CONSTRAINT `FK_JOB_AGG_LOG_ENTRIES_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE ON UPDATE CASCADE;
  			
  			ALTER TABLE `last_query_results`
 			 ADD CONSTRAINT `FK_LAST_QUERY_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE ON UPDATE NO ACTION;
  			
 			 ALTER TABLE `log_entries`
 			 ADD CONSTRAINT `FK_LOG_ENTRIES_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE,
  			ADD CONSTRAINT `fk_log_entries_statuses_STATUS` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuses` (`ID_STATUS`);

  			ALTER TABLE `overview_queries`
  			ADD CONSTRAINT `FK_OVERVIEWQUERY_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE ON UPDATE NO ACTION,
  			ADD CONSTRAINT `FK_OW_QUERY_PAGE` FOREIGN KEY (`ID_OVERVIEW_PAGE`) REFERENCES `overview_page` (`ID_OVERVIEW_PAGE`) ON DELETE CASCADE ON UPDATE NO ACTION;
  			
  			ALTER TABLE `periods`
  			ADD CONSTRAINT `FK_PERIODS_SLA` FOREIGN KEY (`ID_SLA`) REFERENCES `sla` (`ID_SLA`) ON DELETE CASCADE ON UPDATE NO ACTION;
  			
			ALTER TABLE `queries`
  			ADD CONSTRAINT `FK_QUERIES_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE,
  			ADD CONSTRAINT `FK_QUERIES_METHOD` FOREIGN KEY (`ID_SERVICE_METHOD`) REFERENCES `service_methods` (`ID_SERVICE_METHOD`),
  			ADD CONSTRAINT `FK_QUERIES_STATUS` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuses` (`ID_STATUS`);
  			
  			ALTER TABLE `query_agg_hour_log_entries`
  			ADD CONSTRAINT `FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE ON UPDATE NO ACTION;
  			
  			ALTER TABLE `query_agg_log_entries`
  			ADD CONSTRAINT `FK_QUERY_AGG_LOG_ENTRIES_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE;
  			
  			ALTER TABLE `query_params`
  			ADD CONSTRAINT `FK_QUERY_PARAMS_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE;
  			
  			ALTER TABLE `query_validation_results`
  			ADD CONSTRAINT `fk_query_validation_results_queries1` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE NO ACTION ON UPDATE NO ACTION;
  			
  			ALTER TABLE `query_validation_settings`
  			ADD CONSTRAINT `fk_query_validation_settings_queries1` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE NO ACTION ON UPDATE NO ACTION;
  			
  			ALTER TABLE `service_types_methods`
  			ADD CONSTRAINT `FK_SERVICE_TYPES_METHODS_METHOD` FOREIGN KEY (`ID_SERVICE_METHOD`) REFERENCES `service_methods` (`ID_SERVICE_METHOD`) ON DELETE CASCADE,
  			ADD CONSTRAINT `FK_SERVICE_TYPES_METHODS_TYPE` FOREIGN KEY (`ID_SERVICE_TYPE`) REFERENCES `service_types` (`ID_SERVICE_TYPE`) ON DELETE CASCADE;
  			
  			ALTER TABLE `users`
  			ADD CONSTRAINT `FK_USERS_ROLE` FOREIGN KEY (`ID_ROLE`) REFERENCES `roles` (`ID_ROLE`) ON DELETE SET NULL;
  			
  
			";
	

global  $mainframe;
	$db =& JFactory::getDBO();
	
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');

	$user =& JFactory::getUser();
	$user_id = $user->get('id');

	 //Check the CORE installation
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` WHERE  `option` ='com_easysdi_core'";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count == 0) {
		$mainframe->enqueueMessage("Core component does not exist. Easysdi Monitor could not be installed. Please install core component first.","ERROR");
		// Delete component
		$db =& JFactory::getDBO();
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_monitor'";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		return false;
	}
	
	// Gets the component version
	$query = "SELECT currentversion FROM `#__sdi_list_module` where `code` = 'MONITOR'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if (!$version)
	{
		$version= '0.1';
		$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'MONITOR', 'com_easysdi_monitor', 'com_easysdi_monitor', '".date('Y-m-d H:i:s')."', '".$user_id."', 'com_sdi_monitor', 'com_sdi_monitor', '".$version."')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		$module_id = $db->insertid();
		
		$query="INSERT INTO #__sdi_module_panel (guid, code, name, description, created, createdby,module_id, view_path,ordering) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'MONITOR_PANEL', 'Monitor Panel', 'Monitor Panel', '".date('Y-m-d H:i:s')."', '".$user_id."', '".$module_id."', 'com_easysdi_monitor/views/main/sub.ctrlpanel.admin.easysdi.html.php', '4')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	        
		//Insert configuration keys
		$query = "INSERT  INTO #__sdi_configuration (guid, code, name, description, created, createdby,  value, module_id) VALUES
				('".helper_easysdi::getUniqueId()."', 'MONITOR_URL', 'MONITOR_URL', 'MONITOR', '".date('Y-m-d H:i:s')."', '".$user_id."',  'http://admin:admin@localhost:8080/Monitor', '".$module_id."')";
		$db->setQuery( $query);
		if (!$db->query())
		{	
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	}
	if($version == "0.1")
	{
		// Update component version
		$version="2.0.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='MONITOR'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	
	if($version == "2.0.0")
	{
		// Update component version
		$servletMigrateSuccess = true;
		$sql = explode(";",$migrateServletTablesQuery);
		foreach($sql as $query){
			$query =trim($query);
			if($query !=""){
				$db->setQuery( trim($query));
				if (!$db->query())
				{	$servletMigrateSuccess = false;
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
			}
		}
		if($servletMigrateSuccess){
			
			$query = "CREATE TABLE `#__sdi_monitor_exports` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `exportDesc` varchar(500) ,
					  `exportName` varchar(500),
					  `exportType` varchar(10),
					  `xsltUrl` varchar (500),
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}else{			
			
				$version="2.1.0";
				$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='MONITOR'"; 
				$db->setQuery( $query);	
				if (!$db->query()) 
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
			}
		}
		
		//$sqlFilePath = 	JPATH_COMPONENT_ADMINISTRATOR.DS."sqldump".DS."monitor_2_1.sql";
		//$mainframe->enqueueMessage("filepath".$sqlFilePath);
		//$sqldump = file_get_contents($sqlFilePath);
		//$mainframe->enqueueMessage("dump = \n".$sqldump);
		
	
	
		


	
	}
	
	
	
	$query = "DELETE FROM #__components where `option`= 'com_easysdi_monitor' ";
	$db->setQuery($query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}

	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('EasySDI - Monitor','option=com_easysdi_monitor&view=main','Easysdi Monitor','com_easysdi_monitor','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
	$mainframe->enqueueMessage("Congratulation Monitor for EasySDI is installed and ready to be used. Enjoy EasySdi Monitor!
	 Do not forget to check/change the MONITOR_URL key depending on your servlet container location.","INFO");

	 /*
	$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'" ;
	$db->setQuery( $query);
	$id = $db->loadResult();
	if ($id)
	{
	}
	else
	{
		$mainframe->enqueueMessage("EASYSDI menu was not installed. Usually this menu is created during the installation of the easysdi core component. Please be sure that the easysdi_core component is installed before installing this component.","ERROR");
		return false;
	}

	$query = "DELETE FROM #__components where `option`= 'com_easysdi_monitor' ";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
				return false;	
			}
	
	//Entry in the EasySDI menu
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
	values($id,'Monitor','','option=com_easysdi_monitor&view=main','Monitor','com_easysdi_monitor','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;
	}
	
	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('Monitor','option=com_easysdi_monitor&view=main','Monitor','com_easysdi_monitor','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;		
	}
	
	$query =  "SELECT ID FROM #__components WHERE name ='EasySDI'" ;		
	$db->setQuery( $query);
	$id = $db->loadResult();	
	if ($id)
	{
 		$mainframe->enqueueMessage("EASYSDI menu is already existing. Usually this menu is created during the installation of this component. Maybe something has gone wrong during the previous uninstall !","INFO"); 	 	
	}
	else
	{
		//Insert the EasySdi Main Menu
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_monitor' ";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}
		
		$query =  "insert into #__components (name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
			values('EasySDI','option=com_easysdi_core','option=com_easysdi_core','Easysdi main menu','com_easysdi_core','js/ThemeOffice/component.png','')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}
		$query =  "SELECT ID FROM #__components WHERE name ='EasySDI'"  ;
		$db->setQuery( $query);
		$id = $db->loadResult();	
	}
	
        */

}



?>