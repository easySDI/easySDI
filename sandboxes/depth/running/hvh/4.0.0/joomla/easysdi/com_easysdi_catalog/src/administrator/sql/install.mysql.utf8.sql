CREATE TABLE IF NOT EXISTS `#__sdi_metadata` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`metadatastate_id` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`accessscope_id` INT(11) UNSIGNED  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`published` DATETIME ,
`archived` DATETIME ,
`lastsynchronization` DATETIME ,
`synchronized_by` INT(11) UNSIGNED,
`notification` TINYINT(1)  NOT NULL DEFAULT '0',
`version_id` INT(11) UNSIGNED NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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
`xsldirectory` VARCHAR(255) ,
`oninitrunsearch` TINYINT(1) DEFAULT '0' ,
`cswfilter` TEXT(1000)   ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_resourcetype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`catalog_id` INT(11) UNSIGNED  NOT NULL ,
`resourcetype_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_searchcriteria` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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
`rendertype_id` INT(11) UNSIGNED NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_searchcriteria` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`catalog_id` INT UNSIGNED NOT NULL ,
`searchcriteria_id` INT UNSIGNED NOT NULL ,
`searchtab_id` INT(11) UNSIGNED NOT NULL ,
`defaultvalue` VARCHAR(255)  ,
`defaultvaluefrom` DATETIME ,
`defaultvalueto` DATETIME ,
`params` VARCHAR(500)  ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_profile` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_class` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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
`accessscope_id` INT(11) UNSIGNED NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_attribute` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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
`issystem` TINYINT(1)  NOT NULL DEFAULT '0' ,
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
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_attributevalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_relation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_searchcriteriafilter` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`catalog_id` INT(11) UNSIGNED NOT NULL ,
`searchcriteria_id` INT(11) UNSIGNED NOT NULL ,
`language_id` INT(11) UNSIGNED  NOT NULL ,
`ogcsearchfilter` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_searchsort` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`catalog_id` INT(11) UNSIGNED NOT NULL ,
`language_id` INT(11) UNSIGNED NOT NULL ,
`ogcsearchsorting` VARCHAR(255) ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_relation_attributevalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`relation_id` INT(11) UNSIGNED NOT NULL ,
`attribute_id` INT(11) UNSIGNED NOT NULL ,
`attributevalue_id` INT(11) UNSIGNED  NOT NULL ,
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_relation_profile` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`relation_id` INT(11) UNSIGNED NOT NULL ,
`profile_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_relation_catalog` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`relation_id` INT(11) UNSIGNED NOT NULL ,
`catalog_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_boundary` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`northbound` DOUBLE NOT NULL ,
`southbound` DOUBLE NOT NULL ,
`eastbound` DOUBLE NOT NULL ,
`westbound` DOUBLE NOT NULL ,
`category_id` INT(11)  UNSIGNED ,
`parent_id` INT(11)  UNSIGNED ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_boundarycategory` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_searchcriteria_tab` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`catalog_id` INT(11) UNSIGNED  NOT NULL ,
`searchcriteria_id` INT(11) UNSIGNED NOT NULL ,
`tab_id` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_importref` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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
`cswurl` VARCHAR(255)  NOT NULL ,
`cswversion` VARCHAR(10)  NOT NULL ,
`cswoutputschema` VARCHAR(255)  ,
`importtype_id` INT(11)  UNSIGNED ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_translation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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
`label` VARCHAR(255)   ,
`information` VARCHAR(500) ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_namespace` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
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

