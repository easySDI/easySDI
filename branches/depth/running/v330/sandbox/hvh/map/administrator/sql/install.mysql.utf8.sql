CREATE TABLE IF NOT EXISTS `#__sdi_layergroup` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL   ,
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
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`name` VARCHAR(255)  NOT NULL ,
`group_id` INT(11) UNSIGNED NOT NULL ,
`service_id` INT(11) UNSIGNED   ,
`servicetype` VARCHAR(10)    ,
`layername` VARCHAR(255)  NOT NULL ,
`user` VARCHAR(255)   ,
`password` VARCHAR(255)  ,
`istiled` BOOLEAN NOT NULL DEFAULT '0',
`isdefaultvisible` BOOLEAN NOT NULL DEFAULT '0' ,
`opacity` DECIMAL (10,5) NOT NULL DEFAULT '1',
`asOL` TINYINT(1)  NOT NULL DEFAULT '0',
`asOLstyle` TEXT,
`asOLmatrixset` TEXT,
`asOLoptions` TEXT,
`metadatalink` TEXT  ,
`access` INT(11)  NOT NULL DEFAULT '1',
`asset_id` INT(10),
PRIMARY KEY (`id`), 
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
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`rootnodetext` VARCHAR(255)  ,
`srs` VARCHAR(255)  NOT NULL ,
`unit_id` INT(11) UNSIGNED NOT NULL ,
`centercoordinates` VARCHAR(255)  ,
`maxresolution` DECIMAL  ,
`numzoomlevel` INT(10)  ,
`maxextent` VARCHAR(255)  NOT NULL ,
`defaultserviceconnector_id` INT(11) UNSIGNED NOT NULL ,
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
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`name` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_tool` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`map_id` INT(11) UNSIGNED  NOT NULL ,
`tool_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_map_layergroup` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`map_id` INT(11) UNSIGNED NOT NULL ,
`group_id` INT(11) UNSIGNED  NOT NULL ,
`isbackground` TINYINT(1)  NOT NULL DEFAULT '0',
`isdefault` TINYINT(1)  NOT NULL DEFAULT '0',
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

