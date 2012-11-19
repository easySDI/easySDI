CREATE TABLE IF NOT EXISTS `#__sdi_map_group` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  ,
`checked_out_time` DATETIME ,
`name` VARCHAR(255)  NOT NULL ,
`isdefaultopen` BOOLEAN NOT NULL DEFAULT '1',
`access` INT(11)  NOT NULL DEFAULT '1' ,
`asset_id` INT(10)  ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_layer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11) ,
`checked_out_time` DATETIME ,
`name` VARCHAR(255)  NOT NULL ,
`group_id` INT(11) UNSIGNED NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED   ,
`virtualservice_id` INT(11) UNSIGNED   ,
`layername` VARCHAR(255)  NOT NULL ,
`istiled` BOOLEAN NOT NULL DEFAULT '0',
`isdefaultvisible` BOOLEAN NOT NULL DEFAULT '1' ,
`opacity` DECIMAL NOT NULL DEFAULT '1',
`metadatalink` TEXT  ,
`access` INT(11)  NOT NULL DEFAULT '1',
`asset_id` INT(10),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_context` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME  ,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  ,
`checked_out_time` DATETIME ,
`name` VARCHAR(255)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`srs` VARCHAR(255)  NOT NULL ,
`unit_id` INT(11) UNSIGNED NOT NULL ,
`centercoordinates` VARCHAR(255)  ,
`maxresolution` DECIMAL  ,
`maxextent` VARCHAR(255)  NOT NULL ,
`abstract` TEXT  ,
`access` INT(11)  NOT NULL DEFAULT '1',
`asset_id` INT(10) ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_map_tool` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`alias` VARCHAR(20)  NOT NULL ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  ,
`checked_out_time` DATETIME ,
`created_by` INT(11)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_context_tool` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`context_id` INT(11) UNSIGNED  NOT NULL ,
`tool_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_context_group` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`context_id` INT(11) UNSIGNED NOT NULL ,
`group_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_context_physicalservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`context_id` INT(11) UNSIGNED  NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_context_virtualservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`context_id` INT(11) UNSIGNED NOT NULL ,
`virtualservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

