
-- force default storage to InnoDB in case of missing in CREATE statement
SET storage_engine=InnoDB;

-- force default charset/collation for the same reason
SET NAMES 'utf8';
SET CHARACTER SET utf8;

-- force default collation for the same reason
-- find how ?!


CREATE TABLE IF NOT EXISTS `#__sdi_sys_unit` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` int(11)  NOT NULL DEFAULT '1',
`alias` VARCHAR(50)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`),
INDEX `alias` USING BTREE (`alias`) 
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_role` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_country` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ordering` bigint(20) NOT NULL DEFAULT '1',
  `state` int(11) NOT NULL DEFAULT '1',
  `name` varchar(100) NOT NULL,
  `iso2` varchar(2) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_versiontype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_accessscope` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_metadatastate` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_metadataversion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_spatialoperator` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11) ,
`state` int(11) NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceversion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`serviceconnector_id` INT(11) UNSIGNED  NOT NULL ,
`serviceversion_id` INT(11) UNSIGNED  NOT NULL ,
`implemented` TINYINT(1)  NOT NULL DEFAULT '0',
`relayable` TINYINT(1)  NOT NULL DEFAULT '0',
`aggregatable` TINYINT(1)  NOT NULL DEFAULT '0',
`harvestable` TINYINT(1)  NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceoperation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_operationcompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
`serviceoperation_id` INT(11) UNSIGNED  NOT NULL ,
`implemented` TINYINT(1)  NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationlevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`authenticationlevel_id` INT(11) UNSIGNED  NOT NULL ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_logroll` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_loglevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_exceptionlevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_proxytype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecon_authenticationcon` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`serviceconnector_id` INT(11) UNSIGNED NOT NULL ,
`authenticationconnector_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicescope` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_entity` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__sdi_sys_criteriatype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_importtype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_orderstate` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_ordertype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_productstate` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_relationtype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_rendertype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;





CREATE TABLE IF NOT EXISTS `#__sdi_sys_searchtab` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_topiccategory` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_productmining` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_relationscope` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_language` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ordering` bigint(20) NOT NULL DEFAULT '0',
  `state` int(11) DEFAULT '1',
  `value` varchar(50) NOT NULL,
  `code` varchar(20) NOT NULL,
  `gemet` varchar(10) NOT NULL DEFAULT '',
  `iso639-2T` varchar(10) DEFAULT NULL,
  `iso639-1` varchar(10) DEFAULT NULL,
  `iso3166-1-alpha2` varchar(10) DEFAULT NULL,
  `iso639-2B` varchar(10) DEFAULT NULL,
  `datatable` varchar(50) NOT NULL DEFAULT 'English',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_isolanguage` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_pricing` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_productstorage` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_productmining` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_sys_perimetertype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_server` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_server_serviceconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`server_id` INT(11) UNSIGNED  NOT NULL ,
`serviceconnector_id` int(11) UNSIGNED NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- com_easysdi_contact

CREATE TABLE IF NOT EXISTS `#__sdi_user` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11) ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` int(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL DEFAULT '0'  ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`user_id` INT(11)  NOT NULL ,
`description` text  ,
`notificationrequesttreatment` TINYINT(1)  NOT NULL DEFAULT '1',
`catid` INT(11)  ,
`params` VARCHAR(1024)  ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10)  ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_address` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)   ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL DEFAULT '0'  ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`user_id` INT(11) UNSIGNED ,
`organism_id` INT(11) UNSIGNED ,
`addresstype_id` INT(11) UNSIGNED  NOT NULL ,
`civility` VARCHAR(100)  ,
`firstname` VARCHAR(100)  ,
`lastname` VARCHAR(100)  ,
`function` VARCHAR(100) ,
`address` VARCHAR(100)   ,
`addresscomplement` VARCHAR(100)  ,
`postalcode` VARCHAR(10)   ,
`postalbox` VARCHAR(10)  ,
`locality` VARCHAR(100)  ,
`country_id` int(11) UNSIGNED  ,
`phone` VARCHAR(20)   ,
`mobile` VARCHAR(20) ,
`fax` VARCHAR(20)   ,
`email` VARCHAR(100)   ,
`sameascontact` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_addresstype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11),
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_organism` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME  ,
`ordering` INT(11)  NOT NULL ,
`state` int(11) NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL DEFAULT '0'  ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`acronym` VARCHAR(150)   ,
`description` VARCHAR(500)  ,
`logo` VARCHAR(500) ,
`name` VARCHAR(255)  NOT NULL ,
`website` VARCHAR(500)  ,
`perimeter` LONGTEXT  ,
`selectable_as_thirdparty` TINYINT(1) DEFAULT 0,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10)  NOT NULL ,
`username` VARCHAR(150) ,
`password` VARCHAR(65) ,
`internal_free` TINYINT DEFAULT 0,
`fixed_fee_te` FLOAT(6,2) UNSIGNED DEFAULT 0,
`data_free_fixed_fee` TINYINT DEFAULT 0,
`fixed_fee_apply_vat` TINYINT DEFAULT 0,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_user_role_organism` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` int(11) UNSIGNED ,
`role_id` int(11) UNSIGNED ,
`organism_id` int(11) UNSIGNED ,
PRIMARY KEY (`id`),
  KEY `#__sdi_user_role_organism_fk1` (`user_id`),
  KEY `#__sdi_user_role_organism_fk2` (`role_id`),
  KEY `#__sdi_user_role_organism_fk3` (`organism_id`),
  CONSTRAINT `#__sdi_user_role_organism_fk1` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_user_role_organism_fk2` FOREIGN KEY (`role_id`) REFERENCES `#__sdi_sys_role` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_user_role_organism_fk3` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_category` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME  ,
`ordering` INT(11)  NOT NULL ,
`state` int(11) NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL DEFAULT '0'  ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`alias` VARCHAR(150)   ,
`description` VARCHAR(500)  ,
`name` VARCHAR(255)  NOT NULL ,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10)  NOT NULL ,
`overall_fee` FLOAT(6,2) UNSIGNED DEFAULT NULL,
`backend_only` TINYINT(1) NOT NULL DEFAULT 0,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_organism_category` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`organism_id` INT(11) UNSIGNED ,
`category_id` INT(11) UNSIGNED ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_accessscope` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`entity_guid` VARCHAR(36)  NOT NULL ,
`organism_id` INT(11) UNSIGNED   ,
`user_id` INT(11) UNSIGNED   ,
`category_id` INT(11) UNSIGNED   ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_accessscope_fk1` (`organism_id` ASC) ,
  INDEX `#__sdi_accessscope_fk2` (`user_id` ASC) ,
  INDEX `#__sdi_accessscope_fk3` (`category_id` ASC) ,
  CONSTRAINT `#__sdi_accessscope_fk1`
    FOREIGN KEY (`organism_id`)
    REFERENCES `#__sdi_organism` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_accessscope_fk2`
    FOREIGN KEY (`user_id`)
    REFERENCES `#__sdi_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_accessscope_fk3`
    FOREIGN KEY (`category_id`)
    REFERENCES `#__sdi_category` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- com_easysdi_service

-- System tables
CREATE TABLE IF NOT EXISTS `#__sdi_physicalservice_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`service_id` INT(11) UNSIGNED  NOT NULL ,
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
`capabilities` LONGTEXT,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_virtualservice_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`service_id` INT(11) UNSIGNED  NOT NULL ,
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- Layer of Google, Bing and OSM services
CREATE TABLE IF NOT EXISTS `#__sdi_layer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  ,
`physicalservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- Physical Service
CREATE TABLE IF NOT EXISTS `#__sdi_physicalservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL  DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)   ,
`servicescope_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
`serviceconnector_id` INT(11) UNSIGNED  NOT NULL ,
`resourceauthentication_id` INT(11) UNSIGNED   ,
`resourceurl` VARCHAR(500)   ,
`resourceusername` VARCHAR(150)  ,
`resourcepassword` VARCHAR(150)  ,
`serviceauthentication_id` INT(11) UNSIGNED  ,
`serviceurl` VARCHAR(500)  ,
`serviceusername` VARCHAR(150)  ,
`servicepassword` VARCHAR(150)  ,
`catid` INT(11)  NOT NULL ,
`params` VARCHAR(1024)   ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10)   ,
`server_id` INT(11) UNSIGNED NULL,
PRIMARY KEY (`id`),
UNIQUE (`name`) 
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- virtual Service
CREATE TABLE IF NOT EXISTS `#__sdi_virtualservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR (36)  NOT NULL ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)  ,
`modified` DATETIME ,
`name` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`servicescope_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
`url` VARCHAR(500) ,
`serviceconnector_id` INT(11) UNSIGNED  NOT NULL ,
`reflectedurl` VARCHAR(255) ,
`reflectedmetadata` BOOLEAN NOT NULL DEFAULT '0' ,
`xsltfilename` VARCHAR(255)  ,
`logpath` VARCHAR(255)  NOT NULL ,
`harvester` BOOLEAN NOT NULL  DEFAULT '0',
`maximumrecords` INT(10)  ,
`identifiersearchattribute` VARCHAR(255)  ,
`proxytype_id` INT(11) UNSIGNED  NOT NULL ,
`exceptionlevel_id` INT(11) UNSIGNED  NOT NULL ,
`loglevel_id` INT(11) UNSIGNED  NOT NULL ,
`logroll_id` INT(11) UNSIGNED  NOT NULL ,
`params` VARCHAR(1024)   ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10)   ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_virtualmetadata` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)  ,
`modified` DATETIME ,
`title` VARCHAR(255) ,
`inheritedtitle` TINYINT(1)  NOT NULL DEFAULT '1',
`summary` VARCHAR(255)   ,
`inheritedsummary` TINYINT(1)  NOT NULL DEFAULT '1',
`keyword` VARCHAR(255)  ,
`inheritedkeyword` TINYINT(1)  NOT NULL DEFAULT '1',
`fee` VARCHAR(255)   ,
`inheritedfee` TINYINT(1)  NOT NULL DEFAULT '1',
`accessconstraint` VARCHAR(255)   ,
`inheritedaccessconstraint` TINYINT(1)  NOT NULL DEFAULT '1',
`inheritedcontact` TINYINT(1)  NOT NULL DEFAULT '1',
`contactorganization` VARCHAR(255)  ,
`contactname` VARCHAR(255)   ,
`contactposition` VARCHAR(255)  ,
`contactaddress` VARCHAR(255)  ,
`contactaddresstype`  varchar(255)  ,
`contactrole`  varchar(255) ,
`contactpostalcode` VARCHAR(255)   ,
`contactlocality` VARCHAR(255)  ,
`contactstate` VARCHAR(255)  ,
`country_id` INT(11) UNSIGNED   ,
`contactphone` VARCHAR(255)   ,
`contactfax` VARCHAR(255)   ,
`contactemail` VARCHAR(255)  ,
`contacturl` VARCHAR(255)   ,
`contactavailability` VARCHAR(255) ,
`contactinstruction` VARCHAR(255)  ,
`virtualservice_id` INT(11) UNSIGNED  NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_virtual_physical` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`virtualservice_id` INT(11) UNSIGNED  NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- Policy
CREATE TABLE IF NOT EXISTS `#__sdi_policy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR (36)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)  ,
`modified` DATETIME ,
`name` VARCHAR (255) NOT NULL,
`alias` VARCHAR(20)  NOT NULL ,
`allowfrom` DATE NOT NULL DEFAULT '0000-00-00',
`allowto` DATE NOT NULL DEFAULT '0000-00-00',
`anyoperation` BOOLEAN NOT NULL DEFAULT '1',
`anyservice` BOOLEAN NOT NULL DEFAULT '1',
`accessscope_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
`virtualservice_id` INT(11) UNSIGNED  NOT NULL ,
`csw_spatialpolicy_id` INT(11) UNSIGNED ,
`wms_spatialpolicy_id` INT(11) UNSIGNED ,
`wmts_spatialpolicy_id` INT(11) UNSIGNED ,
`wfs_spatialpolicy_id` INT(11) UNSIGNED ,
`csw_version_id` INT(11) UNSIGNED  NOT NULL DEFAULT '1' ,
`csw_anyattribute` BOOLEAN NOT NULL DEFAULT '1',
`csw_anycontext` BOOLEAN NOT NULL DEFAULT '1',
`csw_anystate` BOOLEAN NOT NULL DEFAULT '1',
`csw_anyvisibility` BOOLEAN NOT NULL DEFAULT '1',
`csw_includeharvested` BOOLEAN NOT NULL DEFAULT '1',
`csw_anyresourcetype` TINYINT(1) NOT NULL DEFAULT 1,
`csw_accessscope_id` TINYINT(1) NOT NULL DEFAULT 1,
`wms_minimumwidth` VARCHAR (255)  ,
`wms_minimumheight`VARCHAR (255) ,
`wms_maximumwidth` VARCHAR (255),
`wms_maximumheight` VARCHAR (255) ,
`params` VARCHAR(1024)   ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10)   ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_organism` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`organism_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_user` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`user_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_category` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`category_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_allowedoperation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`serviceoperation_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_metadatastate` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`metadatastate_id` INT(11) UNSIGNED  NOT NULL ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`metadataversion_id` INT(11) UNSIGNED   ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- ServicePolicy
CREATE TABLE IF NOT EXISTS `#__sdi_physicalservice_policy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`prefix` VARCHAR(255),
`namespace` VARCHAR(255) ,
`anyitem` BOOLEAN NOT NULL DEFAULT '1',
`inheritedspatialpolicy` BOOLEAN NOT NULL DEFAULT '1',
`csw_spatialpolicy_id` INT(11) UNSIGNED ,
`wms_spatialpolicy_id` INT(11) UNSIGNED ,
`wmts_spatialpolicy_id` INT(11) UNSIGNED ,
`wfs_spatialpolicy_id` INT(11) UNSIGNED ,
`physicalservice_id` INT(11) UNSIGNED  NOT NULL ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- SpatialPolicy
CREATE TABLE IF NOT EXISTS `#__sdi_csw_spatialpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`eastboundlongitude` DECIMAL(10,6) ,
`westboundlongitude` DECIMAL(10,6) ,
`northboundlatitude` DECIMAL(10,6) ,
`southboundlatitude` DECIMAL(10,6) ,
`maxx` DECIMAL(18,6) ,
`maxy` DECIMAL(18,6) ,
`minx` DECIMAL(18,6) ,
`miny` DECIMAL(18,6) ,
`srssource` VARCHAR (255) ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wmts_spatialpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`spatialoperator_id` int(11) UNSIGNED NOT NULL DEFAULT '1',
`eastboundlongitude` DECIMAL(10,6) ,
`westboundlongitude` DECIMAL(10,6) ,
`northboundlatitude` DECIMAL(10,6) ,
`southboundlatitude` DECIMAL(10,6) ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wms_spatialpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`maxx` DECIMAL(18,6) ,
`maxy` DECIMAL(18,6) ,
`minx` DECIMAL(18,6) ,
`miny` DECIMAL(18,6) ,
`geographicfilter` TEXT,
`maximumscale` INT(11),
`minimumscale` INT(11),
`srssource` VARCHAR (255) ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wfs_spatialpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`localgeographicfilter` TEXT,
`remotegeographicfilter` TEXT,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- CSW
CREATE TABLE IF NOT EXISTS `#__sdi_excludedattribute` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`path` VARCHAR(500)  NOT NULL ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- WMTS
CREATE TABLE IF NOT EXISTS `#__sdi_wmtslayer_policy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`identifier` varchar(255)  NOT NULL ,
`enabled` TINYINT(1)  NOT NULL DEFAULT '1',
`inheritedspatialpolicy` BOOLEAN NOT NULL DEFAULT '1',
`spatialpolicy_id` INT(11) UNSIGNED ,
`anytilematrixset` TINYINT(1)  NOT NULL DEFAULT '1',
`physicalservicepolicy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_tilematrixset_policy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`wmtslayerpolicy_id` INT(11) UNSIGNED  NOT NULL ,
`identifier` varchar(255)  NOT NULL ,
`anytilematrix` TINYINT(1)  NOT NULL DEFAULT '1',
`srssource` VARCHAR (255) ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_tilematrix_policy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`tilematrixsetpolicy_id` INT(11) UNSIGNED  NOT NULL ,
`identifier` varchar(255)  NOT NULL ,
`tileminrow` INT(11) ,
`tilemaxrow` INT(11) ,
`tilemincol` INT(11) ,
`tilemaxcol` INT(11) ,
`anytile` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- WMS
CREATE TABLE IF NOT EXISTS `#__sdi_wmslayer_policy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NOT NULL ,
`enabled` TINYINT(1)  NOT NULL DEFAULT '0',
`inheritedspatialpolicy` BOOLEAN NOT NULL DEFAULT '1',
`spatialpolicy_id` INT(11) UNSIGNED ,
`physicalservicepolicy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- WFS
CREATE TABLE IF NOT EXISTS `#__sdi_featuretype_policy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  ,
`enabled` TINYINT(1)  NOT NULL DEFAULT '1',
`inheritedspatialpolicy` BOOLEAN NOT NULL DEFAULT '1',
`spatialpolicy_id` INT(11) UNSIGNED ,
`physicalservicepolicy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_includedattribute` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`name` VARCHAR(500)  NOT NULL ,
`featuretypepolicy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_physicalservice_organism` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`physicalservice_id` INT(11) UNSIGNED  NOT NULL ,
`organism_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_virtualservice_organism` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`virtualservice_id` INT(11) UNSIGNED  NOT NULL ,
`organism_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_resourcetype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`resourcetype_id` INT(11) UNSIGNED  NOT NULL ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_visibility` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`user_id` INT(11) UNSIGNED   NULL ,
`organism_id` INT(11) UNSIGNED   NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- com_easysd_catalog

CREATE TABLE IF NOT EXISTS `#__sdi_namespace` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME  ,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`prefix` VARCHAR(10)  NOT NULL ,
`uri` VARCHAR(255)  NOT NULL ,
`system` TINYINT(1)  NOT NULL DEFAULT '0' ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_stereotype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
`defaultpattern` VARCHAR(255)  ,
`isocode` VARCHAR(255) ,
`namespace_id` INT(11) UNSIGNED ,
`entity_id` INT(11) UNSIGNED ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_sys_stereotype_fk1` (`entity_id` ASC) ,
  INDEX `#__sdi_sys_stereotype_fk2` (`namespace_id` ASC) ,
  CONSTRAINT `#__sdi_sys_stereotype_fk1`
    FOREIGN KEY (`entity_id` )
    REFERENCES `#__sdi_sys_entity` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_sys_stereotype_fk2`
    FOREIGN KEY (`namespace_id` )
    REFERENCES `#__sdi_namespace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  ,
`contextualsearchresultpaginationnumber` int(3) DEFAULT 0,
`xsldirectory` VARCHAR(255) ,
`oninitrunsearch` TINYINT(1) DEFAULT '0' ,
`cswfilter` TEXT(1000)   ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
`scrolltoresults` TINYINT(1) NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_class` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)   ,
`issystem` TINYINT(1)  NOT NULL DEFAULT '0',
`isrootclass` TINYINT(1)  NOT NULL DEFAULT '0',
`namespace_id` INT(11) UNSIGNED NOT NULL ,
`isocode` VARCHAR(255)  ,
`stereotype_id` INT(11) UNSIGNED  ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_class_fk1` (`namespace_id` ASC) ,
  INDEX `#__sdi_class_fk2` (`stereotype_id` ASC) ,
  CONSTRAINT `#__sdi_class_fk1`
    FOREIGN KEY (`namespace_id` )
    REFERENCES `#__sdi_namespace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_class_fk2`
    FOREIGN KEY (`stereotype_id` )
    REFERENCES `#__sdi_sys_stereotype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_profile` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  ,
`class_id` INT(11) UNSIGNED  NOT NULL ,
`metadataidentifier` INT(11) UNSIGNED NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_profile_fk1` (`class_id` ASC) ,
  CONSTRAINT `#__sdi_profile_fk1`
    FOREIGN KEY (`class_id` )
    REFERENCES `#__sdi_class` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_attribute` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  ,
`namespace_id` INT(11) UNSIGNED  NOT NULL ,
`isocode` VARCHAR(255)  ,
`stereotype_id` INT(11) UNSIGNED  NOT NULL ,
`length` INT(20)  ,
`pattern` VARCHAR(500)  ,
`listnamespace_id` INT(11) UNSIGNED  ,
`type_isocode` VARCHAR(255)   ,
`codelist` VARCHAR(255)   ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `sdi_attribute_fk1` (`namespace_id` ASC) ,
  INDEX `sdi_attribute_fk2` (`listnamespace_id` ASC) ,
  INDEX `sdi_attribute_fk3` (`stereotype_id` ASC) ,
  CONSTRAINT `sdi_attribute_fk1`
    FOREIGN KEY (`namespace_id` )
    REFERENCES `#__sdi_namespace` (`id`  )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `sdi_attribute_fk2`
    FOREIGN KEY (`listnamespace_id` )
    REFERENCES `#__sdi_namespace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `sdi_attribute_fk3`
    FOREIGN KEY (`stereotype_id` )
    REFERENCES `#__sdi_sys_stereotype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_attributevalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`value` VARCHAR(255)   ,
`attribute_id` INT UNSIGNED NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_attributevalue` (`attribute_id` ASC) ,
  CONSTRAINT `#__sdi_attributevalue`
    FOREIGN KEY (`attribute_id` )
    REFERENCES `#__sdi_attribute` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_resourcetype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`logo` VARCHAR(255)  NOT NULL ,
`application` BOOLEAN NOT NULL ,
`diffusion` BOOLEAN NOT NULL ,
`view` BOOLEAN NOT NULL ,
`monitoring` BOOLEAN NOT NULL ,
`predefined` BOOLEAN NOT NULL ,
`versioning` BOOLEAN NOT NULL ,
`profile_id` int(11) UNSIGNED  NOT NULL,
`fragmentnamespace_id` int(11) UNSIGNED  ,
`fragment` VARCHAR(255)   ,
`sitemapparams` VARCHAR(1000)   ,
`accessscope_id` int(11) UNSIGNED  NOT NULL,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_resourcetype_fk1` (`profile_id` ASC) ,
  INDEX `#__sdi_resourcetype_fk2` (`fragmentnamespace_id` ASC) ,
INDEX `#__sdi_resourcetype_fk3` (`accessscope_id` ASC) ,
  CONSTRAINT `#__sdi_resourcetype_fk1`
    FOREIGN KEY (`profile_id` )
    REFERENCES `#__sdi_profile` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_resourcetype_fk2`
    FOREIGN KEY (`fragmentnamespace_id` )
    REFERENCES `#__sdi_namespace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_resourcetype_fk3`
    FOREIGN KEY (`accessscope_id` )
    REFERENCES `#__sdi_sys_accessscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_resource` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)   ,
`checked_out_time` DATETIME ,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`organism_id` INT(11)  UNSIGNED NOT NULL ,
`resourcetype_id` INT(11) UNSIGNED NOT NULL ,
`accessscope_id` INT(11) UNSIGNED  NOT NULL ,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_resource_fk1` (`organism_id` ASC) ,
  INDEX `#__sdi_resource_fk2` (`resourcetype_id` ASC) ,
  INDEX `#__sdi_resource_fk3` (`accessscope_id` ASC) ,
  CONSTRAINT `#__sdi_resource_fk1`
    FOREIGN KEY (`organism_id` )
    REFERENCES `#__sdi_organism` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_resource_fk2`
    FOREIGN KEY (`resourcetype_id` )
    REFERENCES `#__sdi_resourcetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_resource_fk3`
    FOREIGN KEY (`accessscope_id` )
    REFERENCES `#__sdi_sys_accessscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_relation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  ,
`parent_id` INT(11) UNSIGNED NOT NULL ,
`attributechild_id` INT(11) UNSIGNED ,
`classchild_id` INT(11) UNSIGNED ,
`lowerbound` INT(20)  ,
`upperbound` INT(20)  ,
`relationtype_id` INT(11) UNSIGNED ,
`rendertype_id` INT(11)  UNSIGNED ,
`namespace_id` INT(11)  UNSIGNED ,
`isocode` VARCHAR(255)   ,
`classassociation_id` INT(11)  UNSIGNED ,
`issearchfilter` TINYINT(1)  NOT NULL DEFAULT '0',
`relationscope_id` INT(11) UNSIGNED  ,
`editorrelationscope_id` INT(11) UNSIGNED  ,
`childresourcetype_id` INT(11)  UNSIGNED,
`childtype_id` INT(11)  UNSIGNED,
`accessscope_limitation` INT(1) DEFAULT 0,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_relation_fk1` (`parent_id` ASC) ,
  INDEX `#__sdi_relation_fk2` (`classchild_id` ASC) ,
  INDEX `#__sdi_relation_fk3` (`attributechild_id` ASC) ,
  INDEX `#__sdi_relation_fk4` (`relationtype_id` ASC) ,
  INDEX `#__sdi_relation_fk5` (`rendertype_id` ASC) ,
  INDEX `#__sdi_relation_fk6` (`namespace_id` ASC) ,
  INDEX `#__sdi_relation_fk7` (`classassociation_id` ASC) ,
  INDEX `#__sdi_relation_fk8` (`relationscope_id` ASC) ,
  INDEX `#__sdi_relation_fk9` (`editorrelationscope_id` ASC) ,
  INDEX `#__sdi_relation_fk10` (`childresourcetype_id` ASC) ,
  CONSTRAINT `#__sdi_relation_fk1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `#__sdi_class` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk2`
    FOREIGN KEY (`classchild_id` )
    REFERENCES `#__sdi_class` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk3`
    FOREIGN KEY (`attributechild_id` )
    REFERENCES `#__sdi_attribute` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk4`
    FOREIGN KEY (`relationtype_id` )
    REFERENCES `#__sdi_sys_relationtype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk5`
    FOREIGN KEY (`rendertype_id` )
    REFERENCES `#__sdi_sys_rendertype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk6`
    FOREIGN KEY (`namespace_id` )
    REFERENCES `#__sdi_namespace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk7`
    FOREIGN KEY (`classassociation_id` )
    REFERENCES `#__sdi_class` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk8`
    FOREIGN KEY (`relationscope_id` )
    REFERENCES `#__sdi_sys_relationscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk9`
    FOREIGN KEY (`editorrelationscope_id` )
    REFERENCES `#__sdi_sys_relationscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk10`
    FOREIGN KEY (`childresourcetype_id` )
    REFERENCES `#__sdi_resourcetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_searchcriteria` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`issystem` TINYINT(1)  NOT NULL DEFAULT '0' ,
`criteriatype_id` INT(11) UNSIGNED  NOT NULL ,
`rendertype_id` INT(11) UNSIGNED  ,
`relation_id` INT(11) UNSIGNED  ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_searchcriteria_fk1` (`criteriatype_id` ASC) ,
  INDEX `#__sdi_searchcriteria_fk2` (`rendertype_id` ASC) ,
  INDEX `#__sdi_searchcriteria_fk3` (`relation_id` ASC) ,
  CONSTRAINT `#__sdi_searchcriteria_fk1`
    FOREIGN KEY (`criteriatype_id` )
    REFERENCES `#__sdi_sys_criteriatype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_searchcriteria_fk2`
    FOREIGN KEY (`rendertype_id` )
    REFERENCES `#__sdi_sys_rendertype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_searchcriteria_fk3`
    FOREIGN KEY (`relation_id` )
    REFERENCES `#__sdi_relation` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_searchcriteria` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`catalog_id` INT UNSIGNED NOT NULL ,
`searchcriteria_id` INT UNSIGNED NOT NULL ,
`searchtab_id` INT(11) UNSIGNED NOT NULL ,
`defaultvalue` TEXT  ,
`defaultvaluefrom` DATETIME ,
`defaultvalueto` DATETIME ,
`params` VARCHAR(500)  ,
PRIMARY KEY (`id`),
INDEX `#__sdi_catalog_searchcriteria_fk1` (`catalog_id` ASC) ,
  INDEX `#__sdi_catalog_searchcriteria_fk2` (`searchcriteria_id` ASC) ,
  INDEX `#__sdi_catalog_searchcriteria_fk3` (`searchtab_id` ASC) ,
 CONSTRAINT `#__sdi_catalog_searchcriteria_fk1`
    FOREIGN KEY (`catalog_id` )
    REFERENCES `#__sdi_catalog` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__sdi_catalog_searchcriteria_fk2`
    FOREIGN KEY (`searchcriteria_id` )
    REFERENCES `#__sdi_searchcriteria` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__sdi_catalog_searchcriteria_fk3`
    FOREIGN KEY (`searchtab_id` )
    REFERENCES `#__sdi_sys_searchtab` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_searchsort` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`catalog_id` INT(11) UNSIGNED NOT NULL ,
`language_id` INT(11) UNSIGNED NOT NULL ,
`ogcsearchsorting` VARCHAR(255) ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_catalog_searchsort_fk1` (`catalog_id` ASC) ,
  INDEX `#__sdi_catalog_searchsort_fk2` (`language_id` ASC) ,
  CONSTRAINT `#__sdi_catalog_searchsort_fk1`
    FOREIGN KEY (`catalog_id` )
    REFERENCES `#__sdi_catalog` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__sdi_catalog_searchsort_fk2`
    FOREIGN KEY (`language_id` )
    REFERENCES `#__sdi_language` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_relation_profile` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`relation_id` INT(11) UNSIGNED NOT NULL ,
`profile_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_relation_profile_fk1` (`relation_id` ASC) ,
  INDEX `#__sdi_relation_profile_fk2` (`profile_id` ASC) ,
  CONSTRAINT `#__sdi_relation_profile_fk1`
    FOREIGN KEY (`relation_id` )
    REFERENCES `#__sdi_relation` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_profile_fk2`
    FOREIGN KEY (`profile_id` )
    REFERENCES `#__sdi_profile` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_relation_catalog` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`relation_id` INT(11) UNSIGNED NOT NULL ,
`catalog_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_relation_catalog_fk1` (`relation_id` ASC) ,
  INDEX `#__sdi_relation_catalog_fk2` (`catalog_id` ASC) ,
  CONSTRAINT `#__sdi_relation_catalog_fk1`
    FOREIGN KEY (`relation_id` )
    REFERENCES `#__sdi_relation` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_catalog_fk2`
    FOREIGN KEY (`catalog_id` )
    REFERENCES `#__sdi_catalog` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__sdi_boundarycategory` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`parent_id` INT(11)  UNSIGNED ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_boundarycategory_fk1` (`parent_id` ASC) ,
  CONSTRAINT `#__sdi_boundarycategory_fk1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `#__sdi_boundarycategory` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_boundary` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`northbound` VARCHAR(255)  ,
`southbound` VARCHAR(255) ,
`eastbound` VARCHAR(255)  ,
`westbound` VARCHAR(255)  ,
`category_id` INT(11)  UNSIGNED ,
`parent_id` INT(11)  UNSIGNED ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_boundary_fk1` (`parent_id` ASC) ,
INDEX `#__sdi_boundary_fk2` (`category_id` ASC) ,
  CONSTRAINT `#__sdi_boundary_fk1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `#__sdi_boundary` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_boundary_fk2`
    FOREIGN KEY (`category_id` )
    REFERENCES `#__sdi_boundarycategory` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_importref` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)   ,
`xsl4sdi` VARCHAR(255)   ,
`xsl4ext` VARCHAR(255)  ,
`cswservice_id` INT(11)  UNSIGNED   ,
`cswversion_id` INT(11)  UNSIGNED   ,
`cswoutputschema` VARCHAR(255)  ,
`importtype_id` INT(11)  UNSIGNED ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_importref_fk1` (`importtype_id` ASC) ,
  CONSTRAINT `#__sdi_importref_fk1`
    FOREIGN KEY (`importtype_id` )
    REFERENCES `#__sdi_sys_importtype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_translation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`element_guid` VARCHAR(36)  NOT NULL ,
`language_id` INT(11) UNSIGNED ,
`text1` VARCHAR(255)   ,
`text2` VARCHAR(500) ,
`text3` VARCHAR(255) ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_translation_fk1` (`language_id` ASC) ,
  CONSTRAINT `#__sdi_translation_fk1`
    FOREIGN KEY (`language_id` )
    REFERENCES `#__sdi_language` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_searchcriteriafilter` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`ordering`  int(11) NULL DEFAULT NULL ,
`state`  tinyint(1) NOT NULL DEFAULT 1 ,
`searchcriteria_id`  int(11) UNSIGNED NOT NULL ,
`language_id`  int(11) UNSIGNED NOT NULL ,
`ogcsearchfilter`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_searchcriteriafilter_fk1` (`searchcriteria_id` ASC) ,
  INDEX `#__sdi_searchcriteriafilter_fk2` (`language_id` ASC) ,
  CONSTRAINT `#__sdi_searchcriteriafilter_fk1`
    FOREIGN KEY (`searchcriteria_id`)
    REFERENCES `#__sdi_searchcriteria` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__sdi_searchcriteriafilter_fk2`
    FOREIGN KEY (`language_id`)
    REFERENCES `#__sdi_language` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;




CREATE TABLE IF NOT EXISTS `#__sdi_sys_rendertype_stereotype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`stereotype_id` INT(11) UNSIGNED  NOT NULL ,
`rendertype_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_sys_rendertype_stereotype_fk1` (`stereotype_id` ASC) ,
  INDEX `#__sdi_sys_rendertype_stereotype_fk1_fk2` (`rendertype_id` ASC) ,
  CONSTRAINT `#__sdi_sys_rendertype_stereotype_fk1`
    FOREIGN KEY (`stereotype_id` )
    REFERENCES `#__sdi_sys_stereotype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_sys_rendertype_stereotype_fk2`
    FOREIGN KEY (`rendertype_id` )
    REFERENCES `#__sdi_sys_rendertype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_version` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`resource_id` int(11) UNSIGNED NOT NULL ,
`access` INT(11)  NOT NULL DEFAULT '1' ,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_version_fk1` (`resource_id` ASC) ,
  CONSTRAINT `#__sdi_version_fk1`
    FOREIGN KEY (`resource_id` )
    REFERENCES `#__sdi_resource` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__sdi_application` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  NULL ,
`modified` DATETIME NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`resource_id` INT(11) UNSIGNED  NOT NULL ,
`options` VARCHAR(500)  NOT NULL ,
`url` VARCHAR(500)  NOT NULL ,
`windowname` VARCHAR(255)  NOT NULL ,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_application_fk1` (`resource_id` ASC) ,
  CONSTRAINT `#__sdi_application_fk1`
    FOREIGN KEY (`resource_id` )
    REFERENCES `#__sdi_resource` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_versionlink` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`parent_id` INT(11) UNSIGNED  NOT NULL ,
`child_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_versionlink_fk1` (`parent_id` ASC) ,
INDEX `#__sdi_versionlink_fk2` (`child_id` ASC) ,
  CONSTRAINT `#__sdi_versionlink_fk1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `#__sdi_version` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_versionlink_fk2`
    FOREIGN KEY (`child_id` )
    REFERENCES `#__sdi_version` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
CONSTRAINT #__sdi_versionlink_uk UNIQUE (parent_id, child_id)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__sdi_resourcetypelink` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`parent_id` INT(11) UNSIGNED NOT NULL ,
`child_id` INT(11) UNSIGNED NOT NULL ,
`parentboundlower` INT(10)  NOT NULL ,
`parentboundupper` INT(10)  NOT NULL ,
`childboundlower` INT(10)  NOT NULL ,
`childboundupper` INT(10)  NOT NULL ,
`viralversioning` TINYINT(1)  NOT NULL ,
`inheritance` TINYINT(1)  NOT NULL ,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

PRIMARY KEY (`id`) ,
  INDEX `#__sdi_resourcetypelink_fk1` (`parent_id` ASC) ,
INDEX `#__sdi_resourcetypelink_fk2` (`child_id` ASC) ,
  CONSTRAINT `#__sdi_resourcetypelink_fk1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `#__sdi_resourcetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_resourcetypelink_fk2`
    FOREIGN KEY (`child_id` )
    REFERENCES `#__sdi_resourcetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_resourcetypelinkinheritance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`resourcetypelink_id` INT(11) UNSIGNED NOT NULL ,
`xpath` VARCHAR(500)  NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_resourcetypelinkinheritance_fk1` (`resourcetypelink_id` ASC) ,
  CONSTRAINT `#__sdi_resourcetypelinkinheritance_fk1`
    FOREIGN KEY (`resourcetypelink_id` )
    REFERENCES `#__sdi_resourcetypelink` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_metadata` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`metadatastate_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`accessscope_id` INT(11) UNSIGNED  NOT NULL ,
`published` DATETIME ,
`endpublished` DATETIME ,
`archived` DATETIME ,
`lastsynchronization` DATETIME ,
`synchronized_by` INT(11) UNSIGNED,
`notification` TINYINT(1)  NOT NULL DEFAULT '0',
`version_id` INT(11) UNSIGNED NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_metadata_fk1` (`metadatastate_id` ASC) ,
INDEX `#__sdi_metadata_fk2` (`accessscope_id` ASC) ,
INDEX `#__sdi_metadata_fk3` (`version_id` ASC) ,
  CONSTRAINT `#__sdi_metadata_fk1`
    FOREIGN KEY (`metadatastate_id` )
    REFERENCES `#__sdi_sys_metadatastate` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_metadata_fk2`
    FOREIGN KEY (`accessscope_id` )
    REFERENCES `#__sdi_sys_accessscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_metadata_fk3`
    FOREIGN KEY (`version_id` )
    REFERENCES `#__sdi_version` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_resourcetype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36) NOT NULL,
`created_by` INT(11) NOT NULL,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11),
`modified` DATETIME,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`catalog_id` INT(11) UNSIGNED  NOT NULL ,
`resourcetype_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_catalog_resourcetype_fk1` (`catalog_id` ASC) ,
  INDEX `#__sdi_catalog_resourcetype_fk2` (`resourcetype_id` ASC) ,
  CONSTRAINT `#__sdi_catalog_resourcetype_fk1`
    FOREIGN KEY (`catalog_id` )
    REFERENCES `#__sdi_catalog` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_catalog_resourcetype_fk2`
    FOREIGN KEY (`resourcetype_id` )
    REFERENCES `#__sdi_resourcetype` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_rendertype_criteriatype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`criteriatype_id` INT(11) UNSIGNED  NOT NULL ,
`rendertype_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_assignment` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`assigned` DATETIME ,
`assigned_by` INT(11) UNSIGNED  NOT NULL,
`assigned_to` INT(11) UNSIGNED NOT NULL ,
`metadata_id` INT(11) UNSIGNED NOT NULL ,
`text` VARCHAR (500),
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_assignment_fk1` (`assigned_by`) ,
INDEX `#__sdi_assignment_fk2` (`assigned_to`) ,
INDEX `#__sdi_assignment_fk3` (`metadata_id`) ,
  CONSTRAINT `#__sdi_assignment_fk1`
    FOREIGN KEY (`assigned_by` )
    REFERENCES `#__sdi_user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_assignment_fk2`
    FOREIGN KEY (`assigned_to` )
    REFERENCES `#__sdi_user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_assignment_fk3`
    FOREIGN KEY (`metadata_id` )
    REFERENCES `#__sdi_metadata` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__sdi_user_role_resource` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` int(11) UNSIGNED ,
`role_id` int(11) UNSIGNED ,
`resource_id` int(11) UNSIGNED ,
PRIMARY KEY (`id`),
    INDEX `#__sdi_user_role_resource_fk1` (`user_id` ASC) ,
    INDEX `#__sdi_user_role_resource_fk2` (`role_id` ASC) ,
    INDEX `#__sdi_user_role_resource_fk3` (`resource_id` ASC) ,
CONSTRAINT `#__sdi_user_role_resource_fk1`
    FOREIGN KEY (`user_id`)
    REFERENCES `#__sdi_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_user_role_resource_fk2`
    FOREIGN KEY (`role_id`)
    REFERENCES `#__sdi_sys_role` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_user_role_resource_fk3`
    FOREIGN KEY (`resource_id`)
    REFERENCES `#__sdi_resource` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_relation_defaultvalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`relation_id`  INT(11) UNSIGNED NOT NULL  ,
`attributevalue_id`  INT(11) UNSIGNED ,
`value` VARCHAR (500),
`language_id` INT(11) UNSIGNED ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_relation_defaultvalue_fk1` (`relation_id` ASC) ,
  INDEX `#__sdi_relation_defaultvalue_fk2` (`attributevalue_id` ASC) ,
INDEX `#__sdi_relation_defaultvalue_fk3` (`language_id` ASC) ,
  CONSTRAINT `#__sdi_relation_defaultvalue_fk1`
    FOREIGN KEY (`relation_id` )
    REFERENCES `#__sdi_relation` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_defaultvalue_fk2`
    FOREIGN KEY (`attributevalue_id` )
    REFERENCES `#__sdi_attributevalue` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_relation_defaultvalue_fk3`
    FOREIGN KEY (`language_id` )
    REFERENCES `#__sdi_language` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- com_easysdi_map

CREATE TABLE IF NOT EXISTS `#__sdi_layergroup` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL DEFAULT '0'  ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`isdefaultopen` BOOLEAN NOT NULL DEFAULT '0',
`access` INT(11)  NOT NULL DEFAULT '1' ,
`asset_id` INT(10)  ,
PRIMARY KEY (`id`), 
UNIQUE (`alias`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_maplayer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`name` VARCHAR(255)  NOT NULL ,
`service_id` INT(11) UNSIGNED   ,
`servicetype` VARCHAR(10)    ,
`layername` VARCHAR(255)  NOT NULL ,
`istiled` BOOLEAN NOT NULL DEFAULT '0',
`isdefaultvisible` BOOLEAN NOT NULL DEFAULT '0' ,
`opacity` DECIMAL (3,2) NOT NULL DEFAULT '1',
`asOL` TINYINT(1)  NOT NULL DEFAULT '0',
`asOLstyle` TEXT,
`asOLmatrixset` TEXT,
`asOLoptions` TEXT,
`isindoor`  TINYINT(1) NULL,
`levelfield`  varchar(255) NULL,
`metadatalink` TEXT  ,
`attribution` TEXT   ,
`accessscope_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
`access` INT(11)  NOT NULL DEFAULT '1',
`asset_id` INT(10),
PRIMARY KEY (`id`), 
INDEX `#__sdi_maplayer_fk1` (`accessscope_id` ASC) ,
CONSTRAINT `#__sdi_maplayer_fk1`
    FOREIGN KEY (`accessscope_id`)
    REFERENCES `#__sdi_sys_accessscope` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
UNIQUE (`alias`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME  ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`default_backgroud_layer` INT(11)  NOT NULL DEFAULT '0',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`type` VARCHAR(10)  NOT NULL DEFAULT 'geoext',
`rootnodetext` VARCHAR(255)  ,
`srs` VARCHAR(255)  NOT NULL ,
`unit_id` INT(11) UNSIGNED NOT NULL ,
`maxresolution` DOUBLE  ,
`numzoomlevel` INT(10)  ,
`maxextent` VARCHAR(255)  NOT NULL ,
`restrictedextent` VARCHAR(255) ,
`centercoordinates` VARCHAR(255) ,
`zoom` VARCHAR(10) ,
`abstract` TEXT  ,
`access` INT(11)  NOT NULL DEFAULT '1',
`asset_id` INT(10) ,
PRIMARY KEY (`id`), 
UNIQUE (`alias`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_maptool` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`alias` VARCHAR(20)  NOT NULL ,
`ordering` INT(11)  ,
`state` INT(11)  NOT NULL DEFAULT '1',
`name` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_tool` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`map_id` INT(11) UNSIGNED  NOT NULL ,
`tool_id` INT(11) UNSIGNED NOT NULL ,
`params` VARCHAR(4000) ,
`activated`  TINYINT(1) DEFAULT 0 ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_layergroup` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`map_id` INT(11) UNSIGNED NOT NULL ,
`group_id` INT(11) UNSIGNED  NOT NULL ,
`isbackground` TINYINT(1)  NOT NULL DEFAULT '0',
`isdefault` TINYINT(1)  NOT NULL DEFAULT '0',
`ordering` INT(11)   ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_physicalservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`map_id` INT(11) UNSIGNED  NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_virtualservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`map_id` INT(11) UNSIGNED NOT NULL ,
`virtualservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_layer_layergroup` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`layer_id` INT(11) UNSIGNED NOT NULL ,
`group_id` INT(11) UNSIGNED NOT NULL ,
`ordering` INT(11)   ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_visualization` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME  ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`version_id` INT(11) UNSIGNED NOT NULL ,
`accessscope_id` INT(11) UNSIGNED NOT NULL ,
`maplayer_id` INT(11) UNSIGNED ,
`access` INT(11)  NOT NULL DEFAULT '1',
`asset_id` INT(10) ,
PRIMARY KEY (`id`), 
INDEX `#__sdi_visualization_fk1` (`accessscope_id` ASC) ,
CONSTRAINT `#__sdi_visualization_fk1`
    FOREIGN KEY (`accessscope_id`)
    REFERENCES `#__sdi_sys_accessscope` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_visualization_fk2`
    FOREIGN KEY (`version_id`)
    REFERENCES `#__sdi_version` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
UNIQUE (`alias`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


-- com_easysdi_shop

CREATE TABLE IF NOT EXISTS `#__sdi_sys_propertytype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_property` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`accessscope_id` INT(11) UNSIGNED NOT NULL ,
`mandatory` INT(1)  NOT NULL ,
`propertytype_id` INT(11) UNSIGNED NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
    INDEX `#__sdi_property_fk1` (`accessscope_id` ASC) ,
    INDEX `#__sdi_property_fk2` (`propertytype_id` ASC) ,
  CONSTRAINT `#__sdi_property_fk1`
    FOREIGN KEY (`accessscope_id`)
    REFERENCES `#__sdi_sys_accessscope` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_property_fk2`
    FOREIGN KEY (`propertytype_id`)
    REFERENCES `#__sdi_sys_propertytype` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_propertyvalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`property_id` INT(11) UNSIGNED NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
    INDEX `#__sdi_propertyvalue_fk1` (`property_id` ASC) ,
  CONSTRAINT `#__sdi_propertyvalue_fk1`
    FOREIGN KEY (`property_id`)
    REFERENCES `#__sdi_property` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicetype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_perimeter` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NULL ,
`accessscope_id` INT(11) UNSIGNED NOT NULL ,
`perimetertype_id` INT(11) UNSIGNED NOT NULL ,
`wfsservice_id` INT(11) UNSIGNED  NULL ,
`wfsservicetype_id` INT(11) UNSIGNED  NULL ,
`featuretypename` VARCHAR(255)   NULL ,
`prefix` VARCHAR(255)   NULL ,
`namespace` VARCHAR(255)   NULL ,
`featuretypefieldid` VARCHAR(255)   NULL ,
`featuretypefieldname` VARCHAR(255)   NULL ,
`featuretypefieldsurface` VARCHAR(255)   NULL ,
`featuretypefielddescription` VARCHAR(255)   NULL ,
`featuretypefieldgeometry` VARCHAR(255)   NULL ,
`featuretypefieldresource` VARCHAR(255)   NULL ,
`featuretypefieldlevel` VARCHAR(255)   NULL ,
`maplayer_id` INT(11) UNSIGNED   NULL ,
`wmsservice_id` INT(11) UNSIGNED   NULL ,
`wmsservicetype_id` INT(11) UNSIGNED   NULL ,
`layername` VARCHAR(255)   NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
  INDEX `#__sdi_perimeter_fk1` (`accessscope_id` ASC) ,
  INDEX `#__sdi_perimeter_fk2` (`perimetertype_id` ASC) ,
  CONSTRAINT `#__sdi_perimeter_fk1`
    FOREIGN KEY (`accessscope_id`)
    REFERENCES `#__sdi_sys_accessscope` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_perimeter_fk2`
    FOREIGN KEY (`perimetertype_id`)
    REFERENCES `#__sdi_sys_perimetertype` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_profile` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `guid` VARCHAR(36)  NOT NULL ,
    `alias` VARCHAR(50)   ,
    `created_by` INT(11)  NOT NULL ,
    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` INT(11)   ,
    `modified` DATETIME ,
    `ordering` INT(11)  ,
    `state` int(11)  NOT NULL DEFAULT '1',
    `checked_out` INT(11) NOT NULL DEFAULT '0'  ,
    `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `organism_id` int(11) UNSIGNED NOT NULL,
    `name` varchar(75) NOT NULL,
    `fixed_fee` decimal(19,2),
    `surface_rate` decimal(19,2),
    `min_fee` decimal(19,2),
    `max_fee` decimal(19,2),
    `apply_vat` TINYINT DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_profile_fk1` (`organism_id`),
    CONSTRAINT `#__sdi_pricing_profile_fk1` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_diffusion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`version_id` INT(11) UNSIGNED NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NULL ,
`accessscope_id` INT(11) UNSIGNED NOT NULL ,
`pricing_id` INT(11) UNSIGNED NOT NULL ,
`pricing_profile_id` int(11) UNSIGNED,
`pricing_remark` TEXT NULL ,
`deposit` VARCHAR(255)   ,
`productmining_id` INT(11) UNSIGNED ,
`surfacemin` VARCHAR(50)    ,
`surfacemax` VARCHAR(50)    ,
`productstorage_id` INT(11) UNSIGNED ,
`file` VARCHAR(255)   ,
`fileurl` VARCHAR(500)   ,
`packageurl` VARCHAR(500)   ,
`perimeter_id` INT(11) UNSIGNED  ,
`hasdownload` TINYINT(1)  NOT NULL DEFAULT 0 ,
`hasextraction` TINYINT(1)  NOT NULL DEFAULT 0 ,
`restrictedperimeter` TINYINT(1)  NOT NULL DEFAULT 0 ,
`otp` TINYINT(1)  NOT NULL DEFAULT 0 ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
    INDEX `#__sdi_diffusion_fk1` (`accessscope_id` ASC) ,
    INDEX `#__sdi_diffusion_fk2` (`productmining_id` ASC) ,
    INDEX `#__sdi_diffusion_fk3` (`productstorage_id` ASC) ,
    INDEX `#__sdi_diffusion_fk4` (`perimeter_id` ASC) ,
    INDEX `#__sdi_diffusion_fk5` (`version_id` ASC) ,
    INDEX `#__sdi_diffusion_fk6` (`pricing_profile_id` ASC) ,
  CONSTRAINT `#__sdi_diffusion_fk1`
    FOREIGN KEY (`accessscope_id`)
    REFERENCES `#__sdi_sys_accessscope` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_fk2`
    FOREIGN KEY (`productmining_id`)
    REFERENCES `#__sdi_sys_productmining` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_fk3`
    FOREIGN KEY (`productstorage_id`)
    REFERENCES `#__sdi_sys_productstorage` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_fk4`
    FOREIGN KEY (`perimeter_id`)
    REFERENCES `#__sdi_perimeter` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_fk5`
    FOREIGN KEY (`version_id`)
    REFERENCES `#__sdi_version` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_fk6` 
    FOREIGN KEY (`pricing_profile_id`) 
    REFERENCES `#__sdi_pricing_profile` (`id`) 
    ON DELETE SET NULL 
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_order` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`ordertype_id` INT(11) UNSIGNED NULL ,
`orderstate_id` INT(11) UNSIGNED NOT NULL ,
`archived` tinyint(1) NOT NULL DEFAULT 0,
`user_id` INT(11) UNSIGNED  NOT NULL ,
`thirdparty_id` INT(11) UNSIGNED  NULL ,
`validated` TINYINT(1) DEFAULT NULL,
`validated_date` DATETIME DEFAULT NULL,
`validated_reason` VARCHAR(500),
`validated_by` INT(11) UNSIGNED NULL DEFAULT NULL,
`surface` FLOAT(40,20)  NULL ,
`remark` VARCHAR(4000)  NULL ,
`mandate_ref` VARCHAR(500) NULL,
`mandate_contact` VARCHAR(75),
`mandate_email` VARCHAR(100),
`level` VARCHAR(100),
`freeperimetertool` VARCHAR(100),
`sent` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`completed` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`usernotified` TINYINT(1)  NOT NULL DEFAULT '0',
`access_token` VARCHAR(64) NULL,
`validation_token` VARCHAR(64) NULL,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
  INDEX `#__sdi_order_fk1` (`ordertype_id` ASC) ,
  INDEX `#__sdi_order_fk2` (`orderstate_id` ASC) ,
  INDEX `#__sdi_order_fk3` (`user_id` ASC) ,
  INDEX `#__sdi_order_fk4` (`thirdparty_id` ASC) ,
  CONSTRAINT `#__sdi_order_fk1`
    FOREIGN KEY (`ordertype_id`)
    REFERENCES `#__sdi_sys_ordertype` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_fk2`
    FOREIGN KEY (`orderstate_id`)
    REFERENCES `#__sdi_sys_orderstate` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_fk3`
    FOREIGN KEY (`user_id`)
    REFERENCES `#__sdi_user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_fk4`
    FOREIGN KEY (`thirdparty_id`)
    REFERENCES `#__sdi_organism` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_extractstorage` (
    `id` int(11) unsigned not null auto_increment,
    `ordering` int(11),
    `state` int(11) not null default '1',
    `value` varchar(255) not null,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_order_diffusion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36) NOT NULL,
`created_by` INT(11) NOT NULL,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11),
`modified` DATETIME,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`order_id` INT(11) UNSIGNED NOT NULL ,
`diffusion_id` INT(11) UNSIGNED NOT NULL ,
`productstate_id` INT(11) UNSIGNED NOT NULL ,
`remark` VARCHAR(4000)  NULL ,
`completed` DATETIME NULL DEFAULT '0000-00-00 00:00:00',
`storage_id` INT(11) UNSIGNED NULL ,
`file` VARCHAR(4000)  NULL ,
`size` DECIMAL(10)  NULL ,
`displayName` VARCHAR(75) NULL,
`otp` TEXT NULL,
`otpchance` INT(11) DEFAULT '0',
PRIMARY KEY (`id`),
  INDEX `#__sdi_order_diffusion_fk1` (`order_id` ASC) ,
  INDEX `#__sdi_order_diffusion_fk2` (`diffusion_id` ASC) ,
  INDEX `#__sdi_order_diffusion_fk3` (`productstate_id` ASC) ,
  INDEX `#__sdi_order_diffusion_fk4` (`storage_id` ASC) ,
  CONSTRAINT `#__sdi_order_diffusion_fk1`
    FOREIGN KEY (`order_id`)
    REFERENCES `#__sdi_order` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_diffusion_fk2`
    FOREIGN KEY (`diffusion_id`)
    REFERENCES `#__sdi_diffusion` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_diffusion_fk3`
    FOREIGN KEY (`productstate_id`)
    REFERENCES `#__sdi_sys_productstate` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_diffusion_fk4`
    FOREIGN KEY (`storage_id`)
    REFERENCES `#__sdi_sys_extractstorage` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_order_propertyvalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`orderdiffusion_id` int(11) UNSIGNED NOT NULL ,
`property_id` int(11) UNSIGNED NOT NULL ,
`propertyvalue_id` int(11) UNSIGNED NOT NULL ,
`propertyvalue` VARCHAR(4000)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`),
  INDEX `#__sdi_order_propertyvalue_fk1` (`orderdiffusion_id` ASC) ,
  INDEX `#__sdi_order_propertyvalue_fk2` (`property_id` ASC) ,
  INDEX `#__sdi_order_propertyvalue_fk3` (`propertyvalue_id` ASC) ,
  CONSTRAINT `#__sdi_order_propertyvalue_fk1`
    FOREIGN KEY (`orderdiffusion_id`)
    REFERENCES `#__sdi_order_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_propertyvalue_fk2`
    FOREIGN KEY (`property_id`)
    REFERENCES `#__sdi_property` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_propertyvalue_fk3`
    FOREIGN KEY (`propertyvalue_id`)
    REFERENCES `#__sdi_propertyvalue` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_order_perimeter` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`order_id` int(11) UNSIGNED NOT NULL ,
`perimeter_id` int(11) UNSIGNED NOT NULL ,
`value` TEXT  NULL ,
`text` TEXT  NULL ,
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`),
  INDEX `#__sdi_order_perimeter_fk1` (`order_id` ASC) ,
  INDEX `#__sdi_order_perimeter_fk2` (`perimeter_id` ASC) ,
  CONSTRAINT `#__sdi_order_perimeter_fk1`
    FOREIGN KEY (`order_id`)
    REFERENCES `#__sdi_order` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_perimeter_fk2`
    FOREIGN KEY (`perimeter_id`)
    REFERENCES `#__sdi_perimeter` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_diffusion_notifieduser` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`diffusion_id` int(11) UNSIGNED NOT NULL ,
`user_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`),
    INDEX `#__sdi_diffusion_notifieduser_fk1` (`diffusion_id` ASC) ,
    INDEX `#__sdi_diffusion_notifieduser_fk2` (`user_id` ASC) ,
  CONSTRAINT `#__sdi_diffusion_notifieduser_fk1`
    FOREIGN KEY (`diffusion_id`)
    REFERENCES `#__sdi_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_diffusion_notifieduser_fk2`
    FOREIGN KEY (`user_id`)
    REFERENCES `#__sdi_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_diffusion_perimeter` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`diffusion_id` int(11) UNSIGNED NOT NULL ,
`perimeter_id` int(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`),
    INDEX `#__sdi_diffusion_perimeter_fk1` (`diffusion_id` ASC) ,
    INDEX `#__sdi_diffusion_perimeter_fk2` (`perimeter_id` ASC) ,
  CONSTRAINT `#__sdi_diffusion_perimeter_fk1`
    FOREIGN KEY (`diffusion_id`)
    REFERENCES `#__sdi_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_perimeter_fk2`
    FOREIGN KEY (`perimeter_id`)
    REFERENCES `#__sdi_perimeter` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_diffusion_propertyvalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`diffusion_id` int(11) UNSIGNED NOT NULL ,
`propertyvalue_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`),
    INDEX `#__sdi_diffusion_propertyvalue_fk1` (`diffusion_id` ASC) ,
    INDEX `#__sdi_diffusion_propertyvalue_fk2` (`propertyvalue_id` ASC) ,
  CONSTRAINT `#__sdi_diffusion_propertyvalue_fk1`
    FOREIGN KEY (`diffusion_id`)
    REFERENCES `#__sdi_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_propertyvalue_fk2`
    FOREIGN KEY (`propertyvalue_id`)
    REFERENCES `#__sdi_propertyvalue` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_diffusion_download` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`diffusion_id` int(11) UNSIGNED NOT NULL ,
`user_id` INT(11) UNSIGNED NULL ,
`executed` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`),
    INDEX `#__sdi_diffusion_download_fk1` (`diffusion_id` ASC) ,
    INDEX `#__sdi_diffusion_download_fk2` (`user_id` ASC) ,
  CONSTRAINT `#__sdi_diffusion_download_fk1`
    FOREIGN KEY (`diffusion_id`)
    REFERENCES `#__sdi_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_download_fk2`
    FOREIGN KEY (`user_id`)
    REFERENCES `#__sdi_user` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_organism_category_pricing_rebate` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `organism_id` INT(11) UNSIGNED,
    `category_id` INT(11) UNSIGNED,
    `rebate` decimal(19,2),
    PRIMARY KEY (`id`),
  KEY `#__sdi_organism_category_pricing_rebate_fk1` (`organism_id`),
  KEY `#__sdi_organism_category_pricing_rebate_fk2` (`category_id`),
  CONSTRAINT `#__sdi_organism_category_pricing_rebate_fk1` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_organism_category_pricing_rebate_fk2` FOREIGN KEY (`category_id`) REFERENCES `#__sdi_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_profile_category_pricing_rebate` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `pricing_profile_id` int(11) UNSIGNED NOT NULL,
    `category_id` int(11) UNSIGNED NOT NULL,
    `rebate` FLOAT(6,2) UNSIGNED DEFAULT 100,
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_profile_category_pricing_rebate_fk1` (`pricing_profile_id`),
    KEY `#__sdi_pricing_profile_category_pricing_rebate_fk2` (`category_id`),
    CONSTRAINT `#__sdi_pricing_profile_category_pricing_rebate_fk1` FOREIGN KEY (`pricing_profile_id`) REFERENCES `#__sdi_pricing_profile` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `#__sdi_pricing_profile_category_pricing_rebate_fk2` FOREIGN KEY (`category_id`) REFERENCES `#__sdi_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_order` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `guid` VARCHAR(36)  NOT NULL ,
    `created_by` INT(11)  NOT NULL ,
    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` INT(11)   ,
    `modified` DATETIME ,
    `ordering` INT(11)  ,
    `state` int(11)  NOT NULL DEFAULT '1',
    `checked_out` INT(11) NOT NULL DEFAULT '0'  ,
    `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `order_id` int(11) UNSIGNED NOT NULL,
    `cfg_vat` decimal(19,2) NOT NULL DEFAULT 0,
    `cfg_currency` char(3) NOT NULL DEFAULT 'CHF',
    `cfg_rounding` decimal(3,2) NOT NULL DEFAULT '0.05',
    `cfg_overall_default_fee_te` decimal(19,2) NOT NULL DEFAULT 0,
    `cfg_fee_apply_vat` TINYINT DEFAULT 1,
    `cfg_free_data_fee` TINYINT DEFAULT 0,
    `cal_total_amount_ti` decimal(19,2),
    `cal_fee_ti` decimal(19,2) NOT NULL DEFAULT 0,
    `ind_lbl_category_order_fee` varchar(255),
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_order_fk1` (`order_id`),
    CONSTRAINT `#__sdi_pricing_order_fk1` FOREIGN KEY (`order_id`) REFERENCES `#__sdi_order` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_order_supplier` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `guid` VARCHAR(36)  NOT NULL ,
    `created_by` INT(11)  NOT NULL ,
    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` INT(11)   ,
    `modified` DATETIME ,
    `ordering` INT(11)  ,
    `state` int(11)  NOT NULL DEFAULT '1',
    `checked_out` INT(11) NOT NULL DEFAULT '0'  ,
    `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `pricing_order_id` int(11) UNSIGNED NOT NULL,
    `supplier_id` int(11) UNSIGNED NOT NULL,
    `supplier_name` varchar(255) NOT NULL,
    `cfg_internal_free` TINYINT NOT NULL DEFAULT 1,
    `cfg_fixed_fee_te` decimal(19,2) NOT NULL DEFAULT 0,
    `cfg_fixed_fee_apply_vat` TINYINT NOT NULL DEFAULT 1,
    `cfg_data_free_fixed_fee` TINYINT NOT NULL DEFAULT 0,
    `cal_total_rebate_ti` decimal(19,2) NOT NULL DEFAULT 0,
    `cal_fee_ti` decimal(19,2) NOT NULL DEFAULT 0,
    `cal_total_amount_ti` decimal(19,2),
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_order_supplier_fk1` (`pricing_order_id`),
    KEY `#__sdi_pricing_order_supplier_fk2` (`supplier_id`),
    CONSTRAINT `#__sdi_pricing_order_supplier_fk1` FOREIGN KEY (`pricing_order_id`) REFERENCES `#__sdi_pricing_order` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `#__sdi_pricing_order_supplier_fk2` FOREIGN KEY (`supplier_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_order_supplier_product` (
    `id` int(11) unsigned not null auto_increment,
    `guid` VARCHAR(36)  NOT NULL ,
    `created_by` INT(11)  NOT NULL ,
    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` INT(11)   ,
    `modified` DATETIME ,
    `ordering` INT(11)  ,
    `state` int(11)  NOT NULL DEFAULT '1',
    `checked_out` INT(11) NOT NULL DEFAULT '0'  ,
    `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `pricing_order_supplier_id` int(11) unsigned not null,
    `product_id` int(11) unsigned not null,
    `pricing_id` int(11) unsigned not null,
    `cfg_pct_category_supplier_discount` decimal(19,2) NOT NULL DEFAULT 0,
    `ind_lbl_category_supplier_discount` varchar(255),
    `cal_amount_data_te` decimal(19,2),
    `cal_total_amount_te` decimal(19,2),
    `cal_total_amount_ti` decimal(19,2),
    `cal_total_rebate_ti` decimal(19,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_order_supplier_product_fk1` (`pricing_order_supplier_id`),
    KEY `#__sdi_pricing_order_supplier_product_fk2` (`product_id`),
    KEY `#__sdi_pricing_order_supplier_product_fk3` (`pricing_id`),
    CONSTRAINT `#__sdi_pricing_order_supplier_product_fk1` FOREIGN KEY (`pricing_order_supplier_id`) REFERENCES `#__sdi_pricing_order_supplier` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `#__sdi_pricing_order_supplier_product_fk2` FOREIGN KEY (`product_id`) REFERENCES `#__sdi_diffusion` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `#__sdi_pricing_order_supplier_product_fk3` FOREIGN KEY (`pricing_id`) REFERENCES `#__sdi_sys_pricing` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_order_supplier_product_profile` (
    `id` int(11) unsigned not null auto_increment,
    `guid` VARCHAR(36)  NOT NULL ,
    `created_by` INT(11)  NOT NULL ,
    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` INT(11)   ,
    `modified` DATETIME ,
    `ordering` INT(11)  ,
    `state` int(11)  NOT NULL DEFAULT '1',
    `checked_out` INT(11) NOT NULL DEFAULT '0'  ,
    `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `pricing_order_supplier_product_id` int(11) unsigned not null,
    `pricing_profile_id` int(11) unsigned null,
    `pricing_profile_name` varchar(255) not null,
    `cfg_fixed_fee_te` decimal(19,2) NOT NULL DEFAULT 0,
    `cfg_apply_vat` TINYINT DEFAULT 1,
    `cfg_surface_rate` decimal(19,2) NOT NULL DEFAULT 0,
    `cfg_min_fee` decimal(19,2) NOT NULL DEFAULT 0,
    `cfg_max_fee` decimal(19,2) NOT NULL DEFAULT 0,
    `cfg_pct_category_profile_discount` decimal(19,2) NOT NULL DEFAULT 0,
    `ind_lbl_category_profile_discount` varchar(255),
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_order_supplier_product_profile_fk1` (`pricing_order_supplier_product_id`),
    KEY `#__sdi_pricing_order_supplier_product_profile_fk2` (`pricing_profile_id`),
    CONSTRAINT `#__sdi_pricing_order_supplier_product_profile_fk1` FOREIGN KEY (`pricing_order_supplier_product_id`) REFERENCES `#__sdi_pricing_order_supplier_product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `#__sdi_pricing_order_supplier_product_profile_fk2` FOREIGN KEY (`pricing_profile_id`) REFERENCES `#__sdi_pricing_profile` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- com_easysdi_monitor

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `actions`;
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


DROP TABLE IF EXISTS `action_types`;
CREATE TABLE IF NOT EXISTS `action_types` (
  `ID_ACTION_TYPE` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  PRIMARY KEY (`ID_ACTION_TYPE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


INSERT INTO `action_types` VALUES ('1', 'E-MAIL');
INSERT INTO `action_types` VALUES ('2', 'RSS');


DROP TABLE IF EXISTS `alerts`;
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
CREATE TABLE IF NOT EXISTS `http_methods` (
  `ID_HTTP_METHOD` int(10) unsigned NOT NULL,
  `NAME` varchar(10) NOT NULL,
  PRIMARY KEY (`ID_HTTP_METHOD`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


INSERT INTO `http_methods` VALUES ('1', 'GET');
INSERT INTO `http_methods` VALUES ('2', 'POST');


DROP TABLE IF EXISTS `jobs`;
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
CREATE TABLE IF NOT EXISTS `job_defaults` (
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
CREATE TABLE IF NOT EXISTS `last_ids` (
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
CREATE TABLE IF NOT EXISTS `last_query_results` (
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

DROP TABLE IF EXISTS `overview_page`;
CREATE TABLE IF NOT EXISTS `overview_page` (
  `ID_OVERVIEW_PAGE` int(10) unsigned NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `IS_PUBLIC` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_OVERVIEW_PAGE`),
  UNIQUE KEY `URL_UNIQUE` (`NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `overview_queries`;
CREATE TABLE IF NOT EXISTS `overview_queries` (
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
CREATE TABLE IF NOT EXISTS `queries` (
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
CREATE TABLE IF NOT EXISTS `query_params` (
  `ID_QUERY` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  `VALUE` text,
  PRIMARY KEY (`ID_QUERY`,`NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `query_validation_results`;
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

DROP TABLE IF EXISTS `query_validation_settings`;
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

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `ID_ROLE` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  `RANK` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID_ROLE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO `roles` VALUES ('1', 'ROLE_ADMIN', '1');
INSERT INTO `roles` VALUES ('2', 'ROLE_USER', '3');

DROP TABLE IF EXISTS `service_methods`;
CREATE TABLE IF NOT EXISTS `service_methods` (
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
CREATE TABLE IF NOT EXISTS `service_types` (
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
CREATE TABLE IF NOT EXISTS `service_types_methods` (
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
CREATE TABLE IF NOT EXISTS `statuses` (
  `ID_STATUS` int(10) unsigned NOT NULL,
  `NAME` varchar(45) NOT NULL,
  PRIMARY KEY (`ID_STATUS`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO `statuses` VALUES ('1', 'AVAILABLE');
INSERT INTO `statuses` VALUES ('2', 'OUT_OF_ORDER');
INSERT INTO `statuses` VALUES ('3', 'UNAVAILABLE');
INSERT INTO `statuses` VALUES ('4', 'NOT_TESTED');

DROP TABLE IF EXISTS `users`;
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

INSERT INTO `users` VALUES ('Admin', 'adm', '1', null, '1', '0');
INSERT INTO `users` VALUES ('user', 'usr', '2', null, '1', '0');

DROP VIEW IF EXISTS `overview_query_view`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `overview_query_view` AS select (select count(0) AS `count(0)` from `overview_queries` where ((`overview_queries`.`ID_QUERY` = `q`.`ID_QUERY`) and (`overview_queries`.`ID_OVERVIEW_PAGE` = `p`.`ID_OVERVIEW_PAGE`))) AS `QUERY_IS_PUBLIC`,`p`.`ID_OVERVIEW_PAGE` AS `ID_OVERVIEW_PAGE`,`p`.`NAME` AS `NAME_OVERVIEW_PAGE`,`q`.`ID_QUERY` AS `ID_QUERY`,`q`.`NAME` AS `NAME_QUERY`,`l`.`ID_LAST_QUERY_RESULT` AS `ID_LAST_QUERY_RESULT` from ((`queries` `q` left join `last_query_results` `l` on((`q`.`ID_QUERY` = `l`.`ID_QUERY`))) join `overview_page` `p`) where `q`.`ID_JOB` in (select `jobs`.`ID_JOB` AS `ID_JOB` from `jobs` where (`jobs`.`SAVE_RESPONSE` = 1));

DROP TABLE IF EXISTS `#__sdi_monitor_exports`;
CREATE TABLE IF NOT EXISTS `#__sdi_monitor_exports` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `exportDesc` varchar(500) ,
                  `exportName` varchar(500),
                  `exportType` varchar(10),
                  `xsltUrl` varchar (500),
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- com_easysdi_processing
DROP TABLE IF EXISTS `#__sdi_processing`;
CREATE TABLE IF NOT EXISTS `#__sdi_processing` (
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
  `access` int(10) unsigned NOT NULL DEFAULT '1',
  `access_id` int(10) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `#__sdi_processing_fk1` (`contact_id`),
  CONSTRAINT `#__sdi_processing_fk1` FOREIGN KEY (`contact_id`) REFERENCES `#__sdi_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__sdi_processing_obs`;
CREATE TABLE IF NOT EXISTS `#__sdi_processing_obs` (
  `processing_id` int(10) unsigned NOT NULL,
  `sdi_user_id` int(10) unsigned NOT NULL,
  KEY `#__sdi_processing_obs_fk1` (`processing_id`),
  KEY `#__sdi_processing_obs_fk2` (`sdi_user_id`),
  CONSTRAINT `#__sdi_processing_obs_fk1` FOREIGN KEY (`processing_id`) REFERENCES `#__sdi_processing` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_processing_obs_fk2` FOREIGN KEY (`sdi_user_id`) REFERENCES `#__sdi_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__sdi_processing_order`;
CREATE TABLE IF NOT EXISTS `#__sdi_processing_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `processing_id` int(10) unsigned NOT NULL,
  `parameters` text NOT NULL,
  `filestorage` varchar(20) NOT NULL,
  `file` text,
  `fileurl` text NOT NULL,
  `output` text,
  `outputpreview` text NOT NULL,
  `exec_pid` int(11) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT '',
  `info` text NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(10) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sent` timestamp NULL DEFAULT NULL,
  `access_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `processing_id` (`processing_id`),
  CONSTRAINT `#__sdi_processing_order_fk1` FOREIGN KEY (`processing_id`) REFERENCES `#__sdi_processing` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_processing_order_fk2` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



