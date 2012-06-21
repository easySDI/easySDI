CREATE TABLE IF NOT EXISTS `#__sdi_user` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`user_id` INT(11)  NOT NULL ,
`acronym` VARCHAR(150)  NOT NULL ,
`logo` VARCHAR(500)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`website` VARCHAR(500)  NOT NULL ,
`notificationrequesttreatment` TINYINT(1)  NOT NULL DEFAULT '1',
`catid` INT(11)  NOT NULL ,
`params` VARCHAR(1024)  NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10)  ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_address` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`user_id` INT(11)  NOT NULL ,
`addresstype_id` INT(11)  NOT NULL ,
`organismcomplement` VARCHAR(150)  NOT NULL ,
`organism` VARCHAR(150)  NOT NULL ,
`civility` INT(11)  NOT NULL ,
`firstname` VARCHAR(100)  NOT NULL ,
`lastname` VARCHAR(100)  NOT NULL ,
`function` VARCHAR(100)  NOT NULL ,
`address` VARCHAR(100)  NOT NULL ,
`addresscomplement` VARCHAR(100)  NOT NULL ,
`postalcode` VARCHAR(10)  NOT NULL ,
`postalbox` VARCHAR(10)  NOT NULL ,
`locality` VARCHAR(100)  NOT NULL ,
`country` VARCHAR(100)  NOT NULL ,
`phone` VARCHAR(20)  NOT NULL ,
`mobile` VARCHAR(20)  NOT NULL ,
`fax` VARCHAR(20)  NOT NULL ,
`email` VARCHAR(100)  NOT NULL ,
`sameascontact` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_addresstype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_civility` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceversion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`serviceconnector_id` INT(11)  NOT NULL ,
`serviceversion_id` INT(11)  NOT NULL ,
`implemented` TINYINT(1)  NOT NULL ,
`relayable` TINYINT(1)  NOT NULL ,
`aggregatable` TINYINT(1)  NOT NULL ,
`harvestable` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceoperation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_operationcompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`servicecompliance_id` INT(11)  NOT NULL ,
`serviceoperation_id` INT(11)  NOT NULL ,
`implemented` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationlevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`authenticationlevel_id` INT(11)  NOT NULL ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_service` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(150)  NOT NULL ,
`serviceconnector_id` INT(11)  NOT NULL ,
`resourceauthentication_id` INT(11)  NOT NULL ,
`resourceurl` VARCHAR(500)  NOT NULL ,
`resourceusername` VARCHAR(150)  NOT NULL ,
`resourcepassword` VARCHAR(150)  NOT NULL ,
`serviceauthentication_id` INT(11)  NOT NULL ,
`serviceurl` VARCHAR(500)  NOT NULL ,
`serviceusername` VARCHAR(150)  NOT NULL ,
`servicepassword` VARCHAR(150)  NOT NULL ,
`catid` INT(11)  NOT NULL ,
`params` VARCHAR(1024)  NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10)   ,
PRIMARY KEY (`id`),
UNIQUE (`name`) 
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_service_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`service_id` INT(11)  NOT NULL ,
`servicecompliance_id` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecon_authenticationcon` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`serviceconnector_id` INT(11)  NOT NULL ,
`authenticationconnector_id` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

