CREATE TABLE IF NOT EXISTS `#__sdi_maplayergroup` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`isdefaultopen` BOOLEAN NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_maplayer` (
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
`name` VARCHAR(255)  NOT NULL ,
`maplayergroup_id` INT(11)  NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED  NOT NULL ,
`virtualservice_id` INT(11) UNSIGNED NOT NULL ,
`layername` VARCHAR(255)  NOT NULL ,
`ispublished` BOOLEAN NOT NULL ,
`istiled` BOOLEAN NOT NULL ,
`isdefaultvisible` BOOLEAN NOT NULL ,
`opacity` DECIMAL NOT NULL DEFAULT '1',
`metadatalink` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_mapcontext` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`srs` VARCHAR(256) NOT NULL ,
`unit_id` INT(11) UNSIGNED NOT NULL ,
`centercoordinates` VARCHAR(255)  NOT NULL ,
`maxresolution` DECIMAL NOT NULL ,
`maxextent` VARCHAR(255)  NOT NULL ,
`abstract` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_maptool` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`alias` VARCHAR(20)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_mapcontext_maptool` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`mapcontext_id` INT(11) UNSIGNED  NOT NULL ,
`maptool_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_mapcontext_maplayergroup` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`mapcontext_id` INT(11) UNSIGNED NOT NULL ,
`maplayergroup_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_mapcontext_physicalservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`mapcontext_id` INT(11) UNSIGNED NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_mapcontext_virtualservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`mapcontext_id` INT(11) UNSIGNED NOT NULL ,
`virtualservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

