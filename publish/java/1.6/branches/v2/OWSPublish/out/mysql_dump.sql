-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.1.33-community-log


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema publish_server_2
--

CREATE DATABASE IF NOT EXISTS publish_server_2;
USE publish_server_2;

--
-- Definition of table `diffuser`
--

DROP TABLE IF EXISTS `diffuser`;
CREATE TABLE `diffuser` (
  `ID_DIFFUSER` int(10) unsigned NOT NULL,
  `ID_DATABASE` int(10) unsigned NOT NULL,
  `NAME` varchar(100) DEFAULT NULL,
  `URL` varchar(200) DEFAULT NULL,
  `USERNAME` varchar(100) DEFAULT NULL,
  `PASSWORD` varchar(100) DEFAULT NULL,
  `TYPE` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID_DIFFUSER`) USING BTREE,
  UNIQUE KEY `name` (`NAME`) USING BTREE,
  KEY `databaseId` (`ID_DATABASE`) USING BTREE,
  KEY `type` (`TYPE`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `diffuser`
--

/*!40000 ALTER TABLE `diffuser` DISABLE KEYS */;
INSERT INTO `diffuser` (`ID_DIFFUSER`,`ID_DATABASE`,`NAME`,`URL`,`USERNAME`,`PASSWORD`,`TYPE`) VALUES 
 (1,1,'diffuserlocalhost','http://localhost:8080/geoserver','admin','geoserver',1);
/*!40000 ALTER TABLE `diffuser` ENABLE KEYS */;


--
-- Definition of table `diffuser_types`
--

DROP TABLE IF EXISTS `diffuser_types`;
CREATE TABLE `diffuser_types` (
  `ID_DIFFUSER_TYPES` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  PRIMARY KEY (`ID_DIFFUSER_TYPES`) USING BTREE,
  UNIQUE KEY `name` (`NAME`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `diffuser_types`
--

/*!40000 ALTER TABLE `diffuser_types` DISABLE KEYS */;
INSERT INTO `diffuser_types` (`ID_DIFFUSER_TYPES`,`NAME`) VALUES 
 (1,'GEOSERVER'),
 (0,'MAPSERVER');
/*!40000 ALTER TABLE `diffuser_types` ENABLE KEYS */;


--
-- Definition of table `featuresource`
--

DROP TABLE IF EXISTS `featuresource`;
CREATE TABLE `featuresource` (
  `ID_FEATURESOURCE` int(10) unsigned NOT NULL,
  `ID_DIFFUSER` int(10) unsigned NOT NULL,
  `FEATUREGUID` varchar(100) DEFAULT NULL,
  `TABLENAME` varchar(200) DEFAULT NULL,
  `SCRIPTNAME` varchar(100) DEFAULT NULL,
  `SOURCEDATATYPE` varchar(100) DEFAULT NULL,
  `CRSCODE` varchar(50) DEFAULT NULL,
  `FIELDSNAME` varchar(600) DEFAULT NULL,
  `CREATION_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATE_DATE` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `STATUS` varchar(20) DEFAULT NULL,
  `EXCMESSAGE` varchar(2000) DEFAULT NULL,
  `EXCDETAIL` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`ID_FEATURESOURCE`) USING BTREE,
  KEY `diffuserId` (`ID_DIFFUSER`) USING BTREE,
  CONSTRAINT `featuresource_ibfk_1` FOREIGN KEY (`diffuserId`) REFERENCES `diffuser` (`ID_DIFFUSER`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `featuresource`
--

/*!40000 ALTER TABLE `featuresource` DISABLE KEYS */;
/*!40000 ALTER TABLE `featuresource` ENABLE KEYS */;


--
-- Definition of table `geodatabase`
--

DROP TABLE IF EXISTS `geodatabase`;
CREATE TABLE `geodatabase` (
  `ID_GEODATABASE` int(10) unsigned NOT NULL,
  `NAME` varchar(100) DEFAULT NULL,
  `URL` varchar(200) DEFAULT NULL,
  `USERNAME` varchar(100) DEFAULT NULL,
  `PASSWORD` varchar(100) DEFAULT NULL,
  `SCHEME` varchar(100) DEFAULT NULL,
  `TYPE` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID_GEODATABASE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `geodatabase`
--

/*!40000 ALTER TABLE `geodatabase` DISABLE KEYS */;
INSERT INTO `geodatabase` (`ID_GEODATABASE`,`NAME`,`URL`,`USERNAME`,`PASSWORD`,`SCHEME`,`TYPE`) VALUES 
 (1,'postgislocalhost','jdbc:postgresql://localhost:5432/postgis','postgres','rbago000\'\'','public',3);
/*!40000 ALTER TABLE `geodatabase` ENABLE KEYS */;


--
-- Definition of table `geodatabase_types`
--

DROP TABLE IF EXISTS `geodatabase_types`;
CREATE TABLE `geodatabase_types` (
  `ID_GEODATABASE_TYPES` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  PRIMARY KEY (`ID_GEODATABASE_TYPES`) USING BTREE,
  UNIQUE KEY `name` (`NAME`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `geodatabase_types`
--

/*!40000 ALTER TABLE `geodatabase_types` DISABLE KEYS */;
INSERT INTO `geodatabase_types` (`ID_GEODATABASE_TYPES`,`NAME`) VALUES 
 (1,'mysql'),
 (2,'orcl'),
 (3,'postgis');
/*!40000 ALTER TABLE `geodatabase_types` ENABLE KEYS */;


--
-- Definition of table `last_ids`
--

DROP TABLE IF EXISTS `last_ids`;
CREATE TABLE `last_ids` (
  `TABLE_NAME` varchar(255) NOT NULL,
  `LAST_ID` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`TABLE_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `last_ids`
--

/*!40000 ALTER TABLE `last_ids` DISABLE KEYS */;
INSERT INTO `last_ids` (`TABLE_NAME`,`LAST_ID`) VALUES 
 ('DIFFUSER',0),
 ('FEATURESOURCE',0),
 ('GEODATABASE',0),
 ('LAYER',0);
/*!40000 ALTER TABLE `last_ids` ENABLE KEYS */;


--
-- Definition of table `layer`
--

DROP TABLE IF EXISTS `layer`;
CREATE TABLE `layer` (
  `ID_LAYER` int(10) unsigned NOT NULL,
  `GUID` varchar(100) DEFAULT NULL,
  `KEYWORDLIST` varchar(1000) DEFAULT NULL,
  `TITLE` varchar(100) DEFAULT NULL,
  `NAME` varchar(100) DEFAULT NULL,
  `DESCRIPTION` varchar(100) DEFAULT NULL,
  `STATUS` varchar(20) DEFAULT NULL,
  `ID_FEATURESOURCE` int(10) unsigned NOT NULL,
  `CREATION_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `UPDATE_DATE` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID_LAYER`),
  UNIQUE KEY `name` (`NAME`),
  KEY `ID_FEATURESOURCE` (`ID_FEATURESOURCE`),
  CONSTRAINT `layer_ibfk_1` FOREIGN KEY (`ID_FEATURESOURCE`) REFERENCES `featuresource` (`ID_FEATURESOURCE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `layer`
--

/*!40000 ALTER TABLE `layer` DISABLE KEYS */;
/*!40000 ALTER TABLE `layer` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
