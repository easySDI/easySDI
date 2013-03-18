-- System tables
CREATE TABLE IF NOT EXISTS `#__sdi_physicalservice_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`service_id` INT(11) UNSIGNED  NOT NULL ,
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
`capabilities` TEXT,
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
`state` TINYINT(1)  NOT NULL DEFAULT '1',
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
`state` TINYINT(1)  NOT NULL DEFAULT '1',
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
PRIMARY KEY (`id`),
UNIQUE (`name`) 
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- virtual Service
CREATE TABLE IF NOT EXISTS `#__sdi_virtualservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR (36)  NOT NULL ,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
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
`state` TINYINT(1)  NOT NULL DEFAULT '1',
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
`contactadress` VARCHAR(255)  ,
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
) DEFAULT COLLATE=utf8_general_ci;

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
`state` TINYINT(1)  NOT NULL DEFAULT '1',
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
`wms_minimumwidth` INT(11)  ,
`wms_minimumheight` INT(11) ,
`wms_maximumwidth` INT(11)  ,
`wms_maximumheight` INT(11) ,
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
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wmts_spatialpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`spatialoperator_id` int(11) UNSIGNED NOT NULL DEFAULT '1',
`eastboundlongitude` DECIMAL(10,6) ,
`westboundlongitude` DECIMAL(10,6) ,
`northboundlatitude` DECIMAL(10,6) ,
`southboundlatitude` DECIMAL(10,6) ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wms_spatialpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`maxx` DECIMAL(18,6) ,
`maxy` DECIMAL(18,6) ,
`minx` DECIMAL(18,6) ,
`miny` DECIMAL(18,6) ,
`geographicfilter` TEXT,
`maximumcale` INT(11),
`minimumcale` INT(11),
`srssource` VARCHAR (255) ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wfs_spatialpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`localgeographicfilter` TEXT,
`remotegeographicfilter` TEXT,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

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
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

-- WMS
CREATE TABLE IF NOT EXISTS `#__sdi_wmslayer_policy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NOT NULL ,
`enabled` TINYINT(1)  NOT NULL DEFAULT '1',
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






