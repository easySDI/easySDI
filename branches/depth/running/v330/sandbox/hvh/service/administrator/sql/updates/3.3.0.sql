ALTER TABLE `#__sdi_sys_serviceconnector` DROP COLUMN checked_out;
ALTER TABLE `#__sdi_sys_serviceconnector` DROP COLUMN checked_out_time;

ALTER TABLE `#__sdi_sys_serviceversion` DROP COLUMN checked_out;
ALTER TABLE `#__sdi_sys_serviceversion` DROP COLUMN checked_out_time;

ALTER TABLE `#__sdi_sys_servicecompliance` DROP COLUMN checked_out;
ALTER TABLE `#__sdi_sys_servicecompliance` DROP COLUMN checked_out_time;

ALTER TABLE `#__sdi_sys_serviceoperation` DROP COLUMN checked_out;
ALTER TABLE `#__sdi_sys_serviceoperation` DROP COLUMN checked_out_time;

ALTER TABLE `#__sdi_sys_operationcompliance` DROP COLUMN checked_out;
ALTER TABLE `#__sdi_sys_operationcompliance` DROP COLUMN checked_out_time;

ALTER TABLE `#__sdi_sys_authenticationlevel` DROP COLUMN checked_out;
ALTER TABLE `#__sdi_sys_authenticationlevel` DROP COLUMN checked_out_time;

ALTER TABLE `#__sdi_sys_authenticationconnector` DROP COLUMN checked_out;
ALTER TABLE `#__sdi_sys_authenticationconnector` DROP COLUMN checked_out_time;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_logroll` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_loglevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_exceptionlevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_proxytype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

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

INSERT INTO `#__sdi_physicalservice_servicecompliance` (service_id,servicecompliance_id) SELECT service_id,servicecompliance_id FROM `#__sdi_service_servicecompliance` WHERE `servicetype`='physical';
INSERT INTO `#__sdi_virtualservice_servicecompliance` (service_id,servicecompliance_id) SELECT service_id,servicecompliance_id FROM `#__sdi_service_servicecompliance` WHERE `servicetype`='virtual';

DROP TABLE `#__sdi_service_servicecompliance`;

ALTER TABLE `#__sdi_physicalservice_servicecompliance`
ADD CONSTRAINT `#__sdi_physicalservice_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_physicalservice_servicecompliance`
ADD CONSTRAINT `#__sdi_physicalservice_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_virtualservice_servicecompliance`
ADD CONSTRAINT `#__sdi_virtualservice_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_virtualservice_servicecompliance`
ADD CONSTRAINT `#__sdi_virtualservice_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `reflectedurl` VARCHAR(255);
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `reflectedmetadata` BOOLEAN NOT NULL DEFAULT '0';
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `xsltfilename` VARCHAR(255);
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `logpath` VARCHAR(255) NOT NULL;
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `harvester` BOOLEAN NOT NULL DEFAULT '0';
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `maximumrecords` INT(10);
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `identifiersearchattribute` VARCHAR(255);
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `proxytype_id` INT(11) UNSIGNED  NOT NULL;
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `exceptionlevel_id` INT(11) UNSIGNED  NOT NULL;
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `loglevel_id` INT(11) UNSIGNED  NOT NULL;
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `logroll_id` INT(11) UNSIGNED  NOT NULL;
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `params` VARCHAR(1024);
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `access` INT(10)  NOT NULL DEFAULT '1';
ALTER TABLE `#__sdi_virtualservice` ADD COLUMN `asset_id` INT(10);

CREATE TABLE IF NOT EXISTS `#__sdi_virtualmetadata` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`title` VARCHAR(255) ,
`inheritedtitle` BOOLEAN NOT NULL DEFAULT '1',
`summary` VARCHAR(255)   ,
`inheritedsummary` BOOLEAN NOT NULL DEFAULT '1',
`keyword` VARCHAR(255)  ,
`inheritedkeyword` BOOLEAN NOT NULL DEFAULT '1',
`contactorganization` VARCHAR(255)  ,
`inheritedcontactorganization` BOOLEAN NOT NULL DEFAULT '1',
`contactname` VARCHAR(255)   ,
`inheritedcontactname` BOOLEAN NOT NULL DEFAULT '1',
`contactposition` VARCHAR(255)  ,
`inheritedcontactposition` BOOLEAN NOT NULL DEFAULT '1',
`contactadress` VARCHAR(255)  ,
`inheritedcontactadress` BOOLEAN NOT NULL DEFAULT '1',
`contactpostalcode` VARCHAR(255)   ,
`inheritedcontactpostalcode` BOOLEAN NOT NULL DEFAULT '1',
`contactlocality` VARCHAR(255)  ,
`inheritedcontactlocality` BOOLEAN NOT NULL DEFAULT '1',
`contactstate` VARCHAR(255)  ,
`inheritedcontactstate` BOOLEAN NOT NULL DEFAULT '1',
`contactcountry` VARCHAR(255)   ,
`inheritedcontactcountry` BOOLEAN NOT NULL DEFAULT '1',
`contactphone` VARCHAR(255)   ,
`inheritedcontactphone` BOOLEAN NOT NULL DEFAULT '1',
`contactfax` VARCHAR(255)   ,
`inheritedcontactfax` BOOLEAN NOT NULL DEFAULT '1',
`contactemail` VARCHAR(255)  ,
`inheritedcontactemail` BOOLEAN NOT NULL DEFAULT '1',
`contacturl` VARCHAR(255)   ,
`inheritedcontacturl` BOOLEAN NOT NULL DEFAULT '1',
`contactavailability` VARCHAR(255) ,
`inheritedcontactavailability` BOOLEAN NOT NULL DEFAULT '1',
`contactinstruction` VARCHAR(255)  ,
`inheritedcontactinstruction` BOOLEAN NOT NULL DEFAULT '1',
`fee` VARCHAR(255)   ,
`inheritedfee` BOOLEAN NOT NULL DEFAULT '1',
`accessconstraint` VARCHAR(255)   ,
`inheritedaccessconstraint` BOOLEAN NOT NULL DEFAULT '1',
`modified_by` INT(11)  ,
`modified` DATETIME ,
`virtualservice_id` INT(11) UNSIGNED  NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR (36)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`anonymousaccess` BOOLEAN NOT NULL DEFAULT '1',
`anygroup` BOOLEAN NOT NULL DEFAULT '1',
`anyoperation` BOOLEAN NOT NULL DEFAULT '1',
`anyservice` BOOLEAN NOT NULL DEFAULT '1',
`allowfrom` DATE NOT NULL DEFAULT '0000-00-00',
`allowto` DATE NOT NULL DEFAULT '0000-00-00',
`priority` INT(11)  NOT NULL DEFAULT '1' ,
`modified_by` INT(11)  ,
`modified` DATETIME ,
`virtualservice_id` INT(11) UNSIGNED  NOT NULL ,
`csw_version` INT(11)  NOT NULL ,
`csw_anystate` BOOLEAN NOT NULL DEFAULT '1',
`csw_anycontext` BOOLEAN NOT NULL DEFAULT '1',
`csw_anyvisibility` BOOLEAN NOT NULL DEFAULT '1',
`csw_geographicfilter` TEXT  ,
`wms_minimumwidth` INT(11)  ,
`wms_minimumheight` INT(11) ,
`wms_maximumwidth` INT(11)  ,
`wms_maximumheight` INT(11) ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_servicepolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR (36)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`prefix` VARCHAR(255),
`namespace` VARCHAR(255) ,
`anyitem` BOOLEAN NOT NULL DEFAULT '1',
`physicalservice_id` INT(11) UNSIGNED  NOT NULL ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`modified_by` INT(11)   ,
`modified` DATETIME ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wmslayerpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`minimumscale` INT(11)  NOT NULL ,
`maximumscale` INT(11)  NOT NULL ,
`geographicfilter` TEXT NOT NULL ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`wmslayer_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_allowedoperation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`serviceoperation_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_versiontype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`versiontype_id` INT(11) ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_metadatastate` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`metadatastate_id` INT(11) UNSIGNED  NOT NULL ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_accessscope` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`accessscope_id` INT(11) UNSIGNED  NOT NULL ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_elementrestriction` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`metadatanode` TEXT NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_featureclass` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  ,
`modified_by` INT(11)   ,
`modified` DATETIME ,
`physicalservice_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_featureclasspolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`attributerestriction` VARCHAR(255)  NOT NULL ,
`boundingboxfilter` VARCHAR(255)  NOT NULL ,
`geographicfilter` VARCHAR(255)  NOT NULL ,
`modified_by` INT(11) ,
`modified` DATETIME ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`featureclass_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wmtslayerpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0',
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`geographicfilter` VARCHAR(255)  NOT NULL ,
`spatialoperator` VARCHAR(255)  NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`wmtslayer_id` INT(11) UNSIGNED  NOT NULL ,
`bbox_minimumx` INT(11)  ,
`bbox_minimumy` INT(11)  ,
`bbox_maximumx` INT(11)  ,
`bbox_maximumy` INT(11)  ,
`enabled` TINYINT(1)  NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_virtual_physical` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`virtualservice_id` INT(11) UNSIGNED NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

ALTER TABLE `#__sdi_virtual_physical`
ADD CONSTRAINT `#__sdi_virtual_physical_fk1` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_virtual_physical`
ADD CONSTRAINT `#__sdi_virtual_physical_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_layer` MODIFY   checked_out INT(11) NOT NULL;
ALTER TABLE `#__sdi_layer` MODIFY   checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';