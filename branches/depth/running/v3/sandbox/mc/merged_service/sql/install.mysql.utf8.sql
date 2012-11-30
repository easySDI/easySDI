CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` TINYINT(1)  ,
`checked_out` INT(11)   ,
`checked_out_time` DATETIME,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceversion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  ,
`checked_out` INT(11) ,
`checked_out_time` DATETIME ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11) ,
`state` TINYINT(1)  ,
`checked_out` INT(11) ,
`checked_out_time` DATETIME,
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
`checked_out` INT(11)  ,
`checked_out_time` DATETIME ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_operationcompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` TINYINT(1)  ,
`checked_out` INT(11)  ,
`checked_out_time` DATETIME ,
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
`serviceoperation_id` INT(11) UNSIGNED  NOT NULL ,
`implemented` TINYINT(1)  NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationlevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` TINYINT(1)  ,
`checked_out` INT(11)  ,
`checked_out_time` DATETIME,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11) ,
`state` TINYINT(1) ,
`checked_out` INT(11)  ,
`checked_out_time` DATETIME,
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
`checked_out` INT(11)   ,
`checked_out_time` DATETIME ,
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

CREATE TABLE IF NOT EXISTS `#__sdi_physicalservice_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`physicalservice_id` INT(11) UNSIGNED  NOT NULL ,
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecon_authenticationcon` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`serviceconnector_id` INT(11) UNSIGNED NOT NULL ,
`authenticationconnector_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;





CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceversion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`serviceconnector_id` INT(11)  NOT NULL ,
`serviceversion_id` INT(11)  NOT NULL ,
`implemented` TINYINT(4)  NOT NULL ,
`relayable` TINYINT(4)  NOT NULL ,
`aggregatable` TINYINT(4)  NOT NULL ,
`harvestable` TINYINT(4)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceoperation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_operationcompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`servicecompliance_id` INT(11) UNSIGNED NOT NULL ,
`serviceoperation_id` INT(11) UNSIGNED NOT NULL ,
`implemented` TINYINT(4)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationlevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`authenticationlevel_id` INT(11) UNSIGNED NOT NULL ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_virtualservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`connector` VARCHAR(255)  NOT NULL ,
`reflectedurl` VARCHAR(255)  NOT NULL ,
`reflectedmetadata` BOOLEAN NOT NULL ,
`xsltfilename` VARCHAR(255)  NOT NULL ,
`logautodriven` BOOLEAN NOT NULL ,
`logpath` VARCHAR(255)  NOT NULL ,
`logprefixfilename` VARCHAR(255)  NOT NULL ,
`logsuffixfilename` VARCHAR(255)  NOT NULL ,
`logextensionfilename` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`harvester` BOOLEAN NOT NULL ,
`maximumrecords` INT(10)  NOT NULL ,
`identifiersearchattribute` VARCHAR(255)  NOT NULL ,
`proxytype_id` INT(11) UNSIGNED NOT NULL ,
`sys_serviceconnector_id` INT(11) UNSIGNED NOT NULL ,
`exceptionlevel_id` INT(11) UNSIGNED NOT NULL ,
`loglevel_id` INT(11) UNSIGNED NOT NULL ,
`logroll_id` INT(11) UNSIGNED NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10)  ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_virtualmetadata` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`inheritedtitle` BOOLEAN NOT NULL DEFAULT '1',
`summary` VARCHAR(255)  NOT NULL ,
`inheritedsummary` BOOLEAN NOT NULL DEFAULT '1',
`keyword` VARCHAR(255)  NOT NULL ,
`inheritedkeyword` BOOLEAN NOT NULL DEFAULT '1',
`contactorganization` VARCHAR(255)  NOT NULL ,
`inheritedcontactorganization` BOOLEAN NOT NULL DEFAULT '1',
`contactname` VARCHAR(255)  NOT NULL ,
`inheritedcontactname` BOOLEAN NOT NULL DEFAULT '1',
`contactposition` VARCHAR(255)  NOT NULL ,
`inheritedcontactposition` BOOLEAN NOT NULL DEFAULT '1',
`contactadress` VARCHAR(255)  NOT NULL ,
`inheritedcontactadress` BOOLEAN NOT NULL DEFAULT '1',
`contactpostalcode` VARCHAR(255)  NOT NULL ,
`inheritedcontactpostalcode` BOOLEAN NOT NULL DEFAULT '1',
`contactlocality` VARCHAR(255)  NOT NULL ,
`inheritedcontactlocality` BOOLEAN NOT NULL DEFAULT '1',
`contactstate` VARCHAR(255)  NOT NULL ,
`inheritedcontactstate` BOOLEAN NOT NULL DEFAULT '1',
`contactcountry` VARCHAR(255)  NOT NULL ,
`inheritedcontactcountry` BOOLEAN NOT NULL DEFAULT '1',
`contactphone` VARCHAR(255)  NOT NULL ,
`inheritedcontactphone` BOOLEAN NOT NULL DEFAULT '1',
`contactfax` VARCHAR(255)  NOT NULL ,
`inheritedcontactfax` BOOLEAN NOT NULL DEFAULT '1',
`contactemail` VARCHAR(255)  NOT NULL ,
`inheritedcontactemail` BOOLEAN NOT NULL DEFAULT '1',
`contacturl` VARCHAR(255)  NOT NULL ,
`inheritedcontacturl` BOOLEAN NOT NULL DEFAULT '1',
`contactavailability` VARCHAR(255)  NOT NULL ,
`inheritedcontactavailability` BOOLEAN NOT NULL DEFAULT '1',
`contactinstruction` VARCHAR(255)  NOT NULL ,
`inheritedcontactinstruction` BOOLEAN NOT NULL DEFAULT '1',
`fee` VARCHAR(255)  NOT NULL ,
`inheritedfee` BOOLEAN NOT NULL DEFAULT '1',
`accessconstraint` VARCHAR(255)  NOT NULL ,
`inheritedaccessconstraint` BOOLEAN NOT NULL DEFAULT '1',
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`virtualservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_policy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`identifier` VARCHAR(255)  NOT NULL ,
`anyoperation` BOOLEAN NOT NULL DEFAULT '1',
`anyservice` BOOLEAN NOT NULL DEFAULT '1',
`allowfrom` DATE NOT NULL DEFAULT '0000-00-00',
`allowto` DATE NOT NULL DEFAULT '0000-00-00',
`priority` INT(11)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`virtualservice_id` INT(11) UNSIGNED NOT NULL ,
`csw_version` INT(11)  NOT NULL ,
`csw_anystate` BOOLEAN NOT NULL DEFAULT '1',
`csw_anycontext` BOOLEAN NOT NULL DEFAULT '1',
`csw_anyvisibility` BOOLEAN NOT NULL DEFAULT '1',
`csw_geographicfilter` TEXT NOT NULL ,
`wms_minimumwidth` INT(11)  NOT NULL ,
`wms_minimumheight` INT(11)  NOT NULL ,
`wms_maximumwidth` INT(11)  NOT NULL ,
`wms_maximumheight` INT(11)  NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10)  ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_servicepolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`prefix` VARCHAR(255)  NOT NULL ,
`namespace` VARCHAR(255)  NOT NULL ,
`anyitem` BOOLEAN NOT NULL DEFAULT '1',
`physicalservice_id` INT(11) UNSIGNED  NOT NULL ,
`policy_id` INT(11) UNSIGNED NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wmslayer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wmslayerpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`minimumscale` INT(11)  NOT NULL ,
`maximumscale` INT(11)  NOT NULL ,
`geographicfilter` TEXT NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`policy_id` INT(11) UNSIGNED NOT NULL ,
`wmslayer_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_allowedoperation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`policy_id` INT(11) UNSIGNED NOT NULL ,
`serviceoperation_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_versiontype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`versiontype` ENUM('all','lastPublishedVersion')  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`policy_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_visibilitytype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`visibilitytype` ENUM('public','protected','private')  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`policy_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_elementrestriction` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`metadatanode` TEXT NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`policy_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_featureclass` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_featureclasspolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`attributerestriction` VARCHAR(255)  NOT NULL ,
`boundingboxfilter` VARCHAR(255)  NOT NULL ,
`geographicfilter` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`policy_id` INT(11) UNSIGNED NOT NULL ,
`featureclass_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wmtslayer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`physicalservice_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_wmtslayerpolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`geographicfilter` VARCHAR(255)  NOT NULL ,
`spatialoperator` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`policy_id` INT(11) UNSIGNED NOT NULL ,
`wmtslayer_id` INT(11) UNSIGNED NOT NULL ,
`bbox_minimumx` INT(11)  NOT NULL ,
`bbox_minimumy` INT(11)  NOT NULL ,
`bbox_maximumx` INT(11)  NOT NULL ,
`bbox_maximumy` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_tilematrixset` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`denominator_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_scalepolicy` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`minscaledenominator` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`wmtslayerpolicy_id` INT(11) UNSIGNED NOT NULL ,
`tilematrixset_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_denominator` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`denominator` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_logroll` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`logroll` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_loglevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`loglevel` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_exceptionlevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`exceptionlevel` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_proxytype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`proxytype` VARCHAR(255)  NOT NULL ,
`guid` INT(36)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

