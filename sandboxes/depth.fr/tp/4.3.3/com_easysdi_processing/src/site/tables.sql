-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Ven 02 Octobre 2015 à 14:30
-- Version du serveur: 5.5.43
-- Version de PHP: 5.4.42-1+deb.sury.org~precise+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `bdt32`
--

DELIMITER $$
--
-- Procédures
--
DROP PROCEDURE IF EXISTS `debug_msg`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `debug_msg`(enabled INTEGER, msg VARCHAR(255))
BEGIN
  IF enabled THEN BEGIN
    select concat("** ", msg) AS '** DEBUG:';
  END; END IF;
END$$

DROP PROCEDURE IF EXISTS `drop_column`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `drop_column`(IN tableName VARCHAR(64), IN columnName VARCHAR(64))
BEGIN
                    IF EXISTS(
                        SELECT * FROM information_schema.COLUMNS
                        WHERE
                            table_schema    = DATABASE()     AND
                            table_name      = CONCAT('bdt32_',tableName) AND
                            column_name = columnName)
                    THEN
                        SET @query = CONCAT('ALTER TABLE ','bdt32_',tableName, ' DROP COLUMN ',columnName, ';');
                        PREPARE stmt FROM @query;
                        EXECUTE stmt;
                        DEALLOCATE PREPARE stmt;
                    END IF;
                END$$

DROP PROCEDURE IF EXISTS `drop_foreign_key`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `drop_foreign_key`(IN tableName VARCHAR(64), IN constraintName VARCHAR(64))
BEGIN
                    IF EXISTS(
                        SELECT * FROM information_schema.table_constraints
                        WHERE
                            table_schema    = DATABASE()     AND
                            table_name      = CONCAT('bdt32_',tableName) AND
                            constraint_name = CONCAT('bdt32_',constraintName) AND
                            constraint_type = 'FOREIGN KEY')
                    THEN
                        SET @query = CONCAT('ALTER TABLE ','bdt32_',tableName, ' DROP FOREIGN KEY ','bdt32_',constraintName, ';');
                        PREPARE stmt FROM @query;
                        EXECUTE stmt;
                        DEALLOCATE PREPARE stmt;
                    END IF;
                END$$

DROP PROCEDURE IF EXISTS `sdi_foreign_key`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sdi_foreign_key`(IN tableName VARCHAR(64), IN constraintName VARCHAR(64))
BEGIN
    IF EXISTS(
        SELECT * FROM information_schema.table_constraints
        WHERE
            table_schema    = DATABASE()     AND
            table_name      = tableName      AND
            constraint_name = constraintName AND
            constraint_type = 'FOREIGN KEY')
    THEN
        SET @query = CONCAT('ALTER TABLE ', tableName, ' DROP FOREIGN KEY ', constraintName, ';');
        PREPARE stmt FROM @query;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `test_procedure`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `test_procedure`(arg1 INTEGER, arg2 INTEGER)
BEGIN
  SET @enabled = TRUE;

  call debug_msg(@enabled, "my first debug message");
  call debug_msg(@enabled, (select concat_ws('',"arg1:", arg1)));
  call debug_msg(TRUE, "This message always shows up");
  call debug_msg(FALSE, "This message will never show up");
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `bdt32_sdi_processing`
--

DROP TABLE IF EXISTS `bdt32_sdi_processing`;
CREATE TABLE IF NOT EXISTS `bdt32_sdi_processing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guid` varchar(255) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `accessscope_id` int(11) NOT NULL,
  `command` text,
  `map_id` int(10) NOT NULL,
  `parameters` text,
  `plugins` text NOT NULL,
  `access` int(10) unsigned NOT NULL DEFAULT '1',
  `access_id` int(10) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`),
  KEY `state` (`state`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `bdt32_sdi_processing_obs`
--

DROP TABLE IF EXISTS `bdt32_sdi_processing_obs`;
CREATE TABLE IF NOT EXISTS `bdt32_sdi_processing_obs` (
  `processing_id` int(10) unsigned NOT NULL,
  `sdi_user_id` int(10) unsigned NOT NULL,
  KEY `processing_id` (`processing_id`),
  KEY `sdi_user_id` (`sdi_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bdt32_sdi_processing_order`
--

DROP TABLE IF EXISTS `bdt32_sdi_processing_order`;
CREATE TABLE IF NOT EXISTS `bdt32_sdi_processing_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `processing_id` int(10) unsigned NOT NULL,
  `parameters` text NOT NULL,
  `input` text,
  `output` text,
  `exec_pid` int(11) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT '',
  `info` text NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `processing_id` (`processing_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
