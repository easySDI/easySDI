CREATE TABLE IF NOT EXISTS `#__sdi_user` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11) ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
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
`alias` VARCHAR(20)   ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
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
`state` TINYINT(1)  NOT NULL DEFAULT '1',
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
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL DEFAULT '0'  ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`acronym` VARCHAR(150)   ,
`description` TEXT  ,
`logo` VARCHAR(500) ,
`name` VARCHAR(255)  NOT NULL ,
`website` VARCHAR(500)  ,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_user_role_organism` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` int(11) UNSIGNED ,
`role_id` int(11) UNSIGNED ,
`organism_id` int(11) UNSIGNED ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;
