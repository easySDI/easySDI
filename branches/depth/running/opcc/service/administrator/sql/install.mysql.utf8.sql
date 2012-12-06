CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` TINYINT(1)  ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceversion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  ,
`checked_out` INT(11) NOT NULL,
`checked_out_time` DATETIME ,
`value` VARCHAR(150)  NOT NULL NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11) ,
`state` TINYINT(1)  ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
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
`ordering` INT(11)   ,
`state` TINYINT(1)  ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_operationcompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` TINYINT(1)  ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
`serviceoperation_id` INT(11) UNSIGNED  NOT NULL ,
`implemented` TINYINT(1)  NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationlevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` TINYINT(1)  ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11) ,
`state` TINYINT(1) ,
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`authenticationlevel_id` INT(11) UNSIGNED  NOT NULL ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_physicalservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL  ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(150)   ,
`serviceconnector_id` INT(11) UNSIGNED  NOT NULL ,
`resourceauthentication_id` INT(11) UNSIGNED   ,
`resourceurl` VARCHAR(500)   ,
`resourceusername` VARCHAR(150)  ,
`resourcepassword` VARCHAR(150)  ,
`serviceauthentication_id` INT(11) UNSIGNED ,
`serviceurl` VARCHAR(500)  ,
`serviceusername` VARCHAR(150)   ,
`servicepassword` VARCHAR(150)   ,
`catid` INT(11)  NOT NULL ,
`params` VARCHAR(1024)   ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10)   ,
PRIMARY KEY (`id`),
UNIQUE (`name`) 
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_service_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`service_id` INT(11) UNSIGNED  NOT NULL ,
`servicetype` VARCHAR(10) NOT NULL,
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecon_authenticationcon` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`serviceconnector_id` INT(11) UNSIGNED NOT NULL ,
`authenticationconnector_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_virtualservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)   ,
`modified` DATETIME  ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`url` VARCHAR(500)   ,
`serviceconnector_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_layer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  ,
`physicalservice_id` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



