
CREATE TABLE IF NOT EXISTS `#__sdi_namespace` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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

CREATE TABLE IF NOT EXISTS `#__sdi_sys_stereotype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
`defaultpattern` VARCHAR(255)  ,
`isocode` VARCHAR(255) ,
`namespace_id` INT(11) UNSIGNED ,
`entity_id` INT(11) UNSIGNED ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_sys_stereotype_fk1` (`entity_id` ASC) ,
  INDEX `#__sdi_sys_stereotype_fk2` (`namespace_id` ASC) ,
  CONSTRAINT `#__sdi_sys_stereotype_fk1`
    FOREIGN KEY (`entity_id` )
    REFERENCES `#__sdi_sys_entity` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_sys_stereotype_fk2`
    FOREIGN KEY (`namespace_id` )
    REFERENCES `#__sdi_namespace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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


CREATE TABLE IF NOT EXISTS `#__sdi_class` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_class_fk1` (`namespace_id` ASC) ,
  INDEX `#__sdi_class_fk2` (`stereotype_id` ASC) ,
  INDEX `#__sdi_class_fk3` (`accessscope_id` ASC) ,
  CONSTRAINT `#__sdi_class_fk1`
    FOREIGN KEY (`namespace_id` )
    REFERENCES `#__sdi_namespace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_class_fk2`
    FOREIGN KEY (`stereotype_id` )
    REFERENCES `#__sdi_sys_stereotype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_class_fk3`
    FOREIGN KEY (`accessscope_id` )
    REFERENCES `#__sdi_sys_accessscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_profile` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_profile_fk1` (`class_id` ASC) ,
  CONSTRAINT `#__sdi_profile_fk1`
    FOREIGN KEY (`class_id` )
    REFERENCES `#__sdi_class` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_attribute` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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
PRIMARY KEY (`id`) ,
  INDEX `sdi_attribute_fk1` (`namespace_id` ASC) ,
  INDEX `sdi_attribute_fk2` (`listnamespace_id` ASC) ,
  INDEX `sdi_attribute_fk3` (`stereotype_id` ASC) ,
  CONSTRAINT `sdi_attribute_fk1`
    FOREIGN KEY (`namespace_id` )
    REFERENCES `#__sdi_namespace` (`id`  )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `sdi_attribute_fk2`
    FOREIGN KEY (`listnamespace_id` )
    REFERENCES `#__sdi_namespace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `sdi_attribute_fk3`
    FOREIGN KEY (`stereotype_id` )
    REFERENCES `#__sdi_sys_stereotype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_attributevalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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
`accessscope_id` int(11) UNSIGNED  NOT NULL,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_attributevalue` (`attribute_id` ASC) ,
    INDEX `#__sdi_attributevalue_fk2` (`accessscope_id` ASC) ,
  CONSTRAINT `#__sdi_attributevalue`
    FOREIGN KEY (`attribute_id` )
    REFERENCES `#__sdi_attribute` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_attributevalue_fk2`
    FOREIGN KEY (`accessscope_id` )
    REFERENCES `#__sdi_sys_accessscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_resourcetype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`logo` VARCHAR(255)  NOT NULL ,
`meta` BOOLEAN NOT NULL ,
`diffusion` BOOLEAN NOT NULL ,
`view` BOOLEAN NOT NULL ,
`monitoring` BOOLEAN NOT NULL ,
`predefined` BOOLEAN NOT NULL ,
`versioning` BOOLEAN NOT NULL ,
`profile_id` int(11) UNSIGNED  NOT NULL,
`fragmentnamespace_id` int(11) UNSIGNED  ,
`fragment` VARCHAR(255)   ,
`sitemapparams` VARCHAR(1000)   ,
`accessscope_id` int(11) UNSIGNED  NOT NULL,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_resourcetype_fk1` (`profile_id` ASC) ,
  INDEX `#__sdi_resourcetype_fk2` (`fragmentnamespace_id` ASC) ,
INDEX `#__sdi_resourcetype_fk3` (`accessscope_id` ASC) ,
  CONSTRAINT `#__sdi_resourcetype_fk1`
    FOREIGN KEY (`profile_id` )
    REFERENCES `#__sdi_profile` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_resourcetype_fk2`
    FOREIGN KEY (`fragmentnamespace_id` )
    REFERENCES `#__sdi_namespace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_resourcetype_fk3`
    FOREIGN KEY (`accessscope_id` )
    REFERENCES `#__sdi_sys_accessscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_resource` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)   ,
`checked_out_time` DATETIME ,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`organism_id` INT(11)  UNSIGNED NOT NULL ,
`resourcetype_id` INT(11) UNSIGNED NOT NULL ,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_resource_fk1` (`organism_id` ASC) ,
  INDEX `#__sdi_resource_fk2` (`resourcetype_id` ASC) ,

  CONSTRAINT `#__sdi_resource_fk1`
    FOREIGN KEY (`organism_id` )
    REFERENCES `#__sdi_organism` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_resource_fk2`
    FOREIGN KEY (`resourcetype_id` )
    REFERENCES `#__sdi_resourcetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_relation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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
`childtype_id` INT(11)  UNSIGNED,

`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_relation_fk1` (`parent_id` ASC) ,
  INDEX `#__sdi_relation_fk2` (`classchild_id` ASC) ,
  INDEX `#__sdi_relation_fk3` (`attributechild_id` ASC) ,
  INDEX `#__sdi_relation_fk4` (`relationtype_id` ASC) ,
  INDEX `#__sdi_relation_fk5` (`rendertype_id` ASC) ,
  INDEX `#__sdi_relation_fk6` (`namespace_id` ASC) ,
  INDEX `#__sdi_relation_fk7` (`classassociation_id` ASC) ,
  INDEX `#__sdi_relation_fk8` (`relationscope_id` ASC) ,
  INDEX `#__sdi_relation_fk9` (`editorrelationscope_id` ASC) ,
  INDEX `#__sdi_relation_fk10` (`childresourcetype_id` ASC) ,
  CONSTRAINT `#__sdi_relation_fk1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `#__sdi_class` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk2`
    FOREIGN KEY (`classchild_id` )
    REFERENCES `#__sdi_class` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk3`
    FOREIGN KEY (`attributechild_id` )
    REFERENCES `#__sdi_attribute` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk4`
    FOREIGN KEY (`relationtype_id` )
    REFERENCES `#__sdi_sys_relationtype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk5`
    FOREIGN KEY (`rendertype_id` )
    REFERENCES `#__sdi_sys_rendertype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk6`
    FOREIGN KEY (`namespace_id` )
    REFERENCES `#__sdi_namespace` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk7`
    FOREIGN KEY (`classassociation_id` )
    REFERENCES `#__sdi_class` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk8`
    FOREIGN KEY (`relationscope_id` )
    REFERENCES `#__sdi_sys_relationscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk9`
    FOREIGN KEY (`editorrelationscope_id` )
    REFERENCES `#__sdi_sys_relationscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_fk10`
    FOREIGN KEY (`childresourcetype_id` )
    REFERENCES `#__sdi_resourcetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_searchcriteria` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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
`rendertype_id` INT(11) UNSIGNED  ,
`relation_id` INT(11) UNSIGNED  ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_searchcriteria_fk1` (`criteriatype_id` ASC) ,
  INDEX `#__sdi_searchcriteria_fk2` (`rendertype_id` ASC) ,
  INDEX `#__sdi_searchcriteria_fk3` (`relation_id` ASC) ,
  CONSTRAINT `#__sdi_searchcriteria_fk1`
    FOREIGN KEY (`criteriatype_id` )
    REFERENCES `#__sdi_sys_criteriatype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_searchcriteria_fk2`
    FOREIGN KEY (`rendertype_id` )
    REFERENCES `#__sdi_sys_rendertype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_searchcriteria_fk3`
    FOREIGN KEY (`relation_id` )
    REFERENCES `#__sdi_relation` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_searchcriteria` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`catalog_id` INT UNSIGNED NOT NULL ,
`searchcriteria_id` INT UNSIGNED NOT NULL ,
`searchtab_id` INT(11) UNSIGNED NOT NULL ,
`defaultvalue` VARCHAR(255)  ,
`defaultvaluefrom` DATETIME ,
`defaultvalueto` DATETIME ,
`params` VARCHAR(500)  ,
PRIMARY KEY (`id`),
INDEX `#__sdi_catalog_searchcriteria_fk1` (`catalog_id` ASC) ,
  INDEX `#__sdi_catalog_searchcriteria_fk2` (`searchcriteria_id` ASC) ,
  INDEX `#__sdi_catalog_searchcriteria_fk3` (`searchtab_id` ASC) ,
 CONSTRAINT `#__sdi_catalog_searchcriteria_fk1`
    FOREIGN KEY (`catalog_id` )
    REFERENCES `#__sdi_catalog` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__sdi_catalog_searchcriteria_fk2`
    FOREIGN KEY (`searchcriteria_id` )
    REFERENCES `#__sdi_searchcriteria` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__sdi_catalog_searchcriteria_fk3`
    FOREIGN KEY (`searchtab_id` )
    REFERENCES `#__sdi_sys_searchtab` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_searchsort` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`catalog_id` INT(11) UNSIGNED NOT NULL ,
`language_id` INT(11) UNSIGNED NOT NULL ,
`ogcsearchsorting` VARCHAR(255) ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_catalog_searchsort_fk1` (`catalog_id` ASC) ,
  INDEX `#__sdi_catalog_searchsort_fk2` (`language_id` ASC) ,
  CONSTRAINT `#__sdi_catalog_searchsort_fk1`
    FOREIGN KEY (`catalog_id` )
    REFERENCES `#__sdi_catalog` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_catalog_searchsort_fk2`
    FOREIGN KEY (`language_id` )
    REFERENCES `#__sdi_language` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_relation_attributevalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`relation_id` INT(11) UNSIGNED NOT NULL ,
`attribute_id` INT(11) UNSIGNED NOT NULL ,
`attributevalue_id` INT(11) UNSIGNED  NOT NULL ,
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_relation_attributevalue_fk1` (`relation_id` ASC) ,
  INDEX `#__sdi_relation_attributevalue_fk2` (`attribute_id` ASC) ,
  INDEX `#__sdi_relation_attributevalue_fk3` (`attributevalue_id` ASC) ,
  CONSTRAINT `#__sdi_relation_attributevalue_fk1`
    FOREIGN KEY (`relation_id` )
    REFERENCES `#__sdi_relation` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_attributevalue_fk2`
    FOREIGN KEY (`attribute_id` )
    REFERENCES `#__sdi_attribute` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_relation_attributevalue_fk3`
    FOREIGN KEY (`attributevalue_id` )
    REFERENCES `#__sdi_attributevalue` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_relation_profile` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`relation_id` INT(11) UNSIGNED NOT NULL ,
`profile_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_relation_profile_fk1` (`relation_id` ASC) ,
  INDEX `#__sdi_relation_profile_fk2` (`profile_id` ASC) ,
  CONSTRAINT `#__sdi_relation_profile_fk1`
    FOREIGN KEY (`relation_id` )
    REFERENCES `#__sdi_relation` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_profile_fk2`
    FOREIGN KEY (`profile_id` )
    REFERENCES `#__sdi_profile` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_relation_catalog` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`relation_id` INT(11) UNSIGNED NOT NULL ,
`catalog_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_relation_catalog_fk1` (`relation_id` ASC) ,
  INDEX `#__sdi_relation_catalog_fk2` (`catalog_id` ASC) ,
  CONSTRAINT `#__sdi_relation_catalog_fk1`
    FOREIGN KEY (`relation_id` )
    REFERENCES `#__sdi_relation` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_catalog_fk2`
    FOREIGN KEY (`catalog_id` )
    REFERENCES `#__sdi_catalog` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__sdi_boundarycategory` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_boundarycategory_fk1` (`parent_id` ASC) ,
  CONSTRAINT `#__sdi_boundarycategory_fk1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `#__sdi_boundarycategory` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_boundary` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`northbound` VARCHAR(255)  ,
`southbound` VARCHAR(255) ,
`eastbound` VARCHAR(255)  ,
`westbound` VARCHAR(255)  ,
`category_id` INT(11)  UNSIGNED ,
`parent_id` INT(11)  UNSIGNED ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_boundary_fk1` (`parent_id` ASC) ,
INDEX `#__sdi_boundary_fk2` (`category_id` ASC) ,
  CONSTRAINT `#__sdi_boundary_fk1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `#__sdi_boundary` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_boundary_fk2`
    FOREIGN KEY (`category_id` )
    REFERENCES `#__sdi_boundarycategory` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_importref` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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
`cswservice_id` VARCHAR(255)  NOT NULL ,
`cswversion_id` VARCHAR(10)  NOT NULL ,
`cswoutputschema` VARCHAR(255)  ,
`importtype_id` INT(11)  UNSIGNED ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_importref_fk1` (`importtype_id` ASC) ,
  CONSTRAINT `#__sdi_importref_fk1`
    FOREIGN KEY (`importtype_id` )
    REFERENCES `#__sdi_sys_importtype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_translation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
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
`text1` VARCHAR(255)   ,
`text2` VARCHAR(500) ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_translation_fk1` (`language_id` ASC) ,

  CONSTRAINT `#__sdi_translation_fk1`
    FOREIGN KEY (`language_id` )
    REFERENCES `#__sdi_language` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;




CREATE TABLE IF NOT EXISTS `#__sdi_searchcriteriafilter` (
`id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`ordering`  int(11) NULL DEFAULT NULL ,
`state`  tinyint(1) NOT NULL DEFAULT 1 ,
`searchcriteria_id`  int(11) UNSIGNED NOT NULL ,
`language_id`  int(11) UNSIGNED NOT NULL ,
`ogcsearchfilter`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_searchcriteriafilter_fk1` (`searchcriteria_id` ASC) ,
  INDEX `#__sdi_searchcriteriafilter_fk2` (`language_id` ASC) ,
  CONSTRAINT `#__sdi_searchcriteriafilter_fk1`
    FOREIGN KEY (`searchcriteria_id`)
    REFERENCES `#__sdi_searchcriteria` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__sdi_searchcriteriafilter_fk2`
    FOREIGN KEY (`language_id`)
    REFERENCES `#__sdi_language` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;




CREATE TABLE IF NOT EXISTS `#__sdi_sys_rendertype_stereotype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`stereotype_id` INT(11) UNSIGNED  NOT NULL ,
`rendertype_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_sys_rendertype_stereotype_fk1` (`stereotype_id` ASC) ,
  INDEX `#__sdi_sys_rendertype_stereotype_fk1_fk2` (`rendertype_id` ASC) ,
  CONSTRAINT `#__sdi_sys_rendertype_stereotype_fk1`
    FOREIGN KEY (`stereotype_id` )
    REFERENCES `#__sdi_sys_stereotype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_sys_rendertype_stereotype_fk2`
    FOREIGN KEY (`rendertype_id` )
    REFERENCES `#__sdi_sys_rendertype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_version` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`resource_id` int(11) UNSIGNED NOT NULL ,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_version_fk1` (`resource_id` ASC) ,
  CONSTRAINT `#__sdi_version_fk1`
    FOREIGN KEY (`resource_id` )
    REFERENCES `#__sdi_resource` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__sdi_application` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`resource_id` INT(11) UNSIGNED  NOT NULL ,
`options` VARCHAR(500)  NOT NULL ,
`url` VARCHAR(500)  NOT NULL ,
`windowname` VARCHAR(255)  NOT NULL ,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_application_fk1` (`resource_id` ASC) ,
  CONSTRAINT `#__sdi_application_fk1`
    FOREIGN KEY (`resource_id` )
    REFERENCES `#__sdi_resource` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_versionlink` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`parent_id` INT(11) UNSIGNED  NOT NULL ,
`child_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_versionlink_fk1` (`parent_id` ASC) ,
INDEX `#__sdi_versionlink_fk2` (`child_id` ASC) ,
  CONSTRAINT `#__sdi_versionlink_fk1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `#__sdi_version` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_versionlink_fk2`
    FOREIGN KEY (`child_id` )
    REFERENCES `#__sdi_version` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__sdi_resourcetypelink` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`parent_id` INT(11) UNSIGNED NOT NULL ,
`child_id` INT(11) UNSIGNED NOT NULL ,
`parentboundlower` INT(10)  NOT NULL ,
`parentboundupper` INT(10)  NOT NULL ,
`childboundlower` INT(10)  NOT NULL ,
`childboundupper` INT(10)  NOT NULL ,
`class_id` INT(11) UNSIGNED NOT NULL ,
`attribute_id` INT(11) UNSIGNED  NOT NULL ,
`viralversioning` TINYINT(1)  NOT NULL ,
`inheritance` TINYINT(1)  NOT NULL ,
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

PRIMARY KEY (`id`) ,
  INDEX `#__sdi_resourcetypelink_fk1` (`parent_id` ASC) ,
INDEX `#__sdi_resourcetypelink_fk2` (`child_id` ASC) ,
INDEX `#__sdi_resourcetypelink_fk3` (`class_id` ASC) ,
INDEX `#__sdi_resourcetypelink_fk4` (`attribute_id` ASC) ,
  CONSTRAINT `#__sdi_resourcetypelink_fk1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `#__sdi_resourcetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_resourcetypelink_fk2`
    FOREIGN KEY (`child_id` )
    REFERENCES `#__sdi_resourcetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_resourcetypelink_fk3`
    FOREIGN KEY (`class_id` )
    REFERENCES `#__sdi_class` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_resourcetypelink_fk4`
    FOREIGN KEY (`attribute_id` )
    REFERENCES `#__sdi_attribute` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_resourcetypelinkinheritance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`resourcetypelink_id` INT(11) UNSIGNED NOT NULL ,
`xpath` VARCHAR(500)  NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_resourcetypelinkinheritance_fk1` (`resourcetypelink_id` ASC) ,
  CONSTRAINT `#__sdi_resourcetypelinkinheritance_fk1`
    FOREIGN KEY (`resourcetypelink_id` )
    REFERENCES `#__sdi_resourcetypelink` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_metadata` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(50)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME ,
`ordering` INT(11)  ,
`metadatastate_id` INT(11) UNSIGNED NOT NULL DEFAULT '1',
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
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_metadata_fk1` (`metadatastate_id` ASC) ,
INDEX `#__sdi_metadata_fk2` (`accessscope_id` ASC) ,
INDEX `#__sdi_metadata_fk3` (`version_id` ASC) ,
  CONSTRAINT `#__sdi_metadata_fk1`
    FOREIGN KEY (`metadatastate_id` )
    REFERENCES `#__sdi_sys_metadatastate` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_metadata_fk2`
    FOREIGN KEY (`accessscope_id` )
    REFERENCES `#__sdi_sys_accessscope` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_metadata_fk3`
    FOREIGN KEY (`version_id` )
    REFERENCES `#__sdi_version` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_catalog_resourcetype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)   ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`catalog_id` INT(11) UNSIGNED  NOT NULL ,
`resourcetype_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_catalog_resourcetype_fk1` (`catalog_id` ASC) ,
  INDEX `#__sdi_catalog_resourcetype_fk2` (`resourcetype_id` ASC) ,
  CONSTRAINT `#__sdi_catalog_resourcetype_fk1`
    FOREIGN KEY (`catalog_id` )
    REFERENCES `#__sdi_catalog` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_catalog_resourcetype_fk2`
    FOREIGN KEY (`resourcetype_id` )
    REFERENCES `#__sdi_resourcetype` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION

) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_rendertype_criteriatype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`criteriatype_id` INT(11) UNSIGNED  NOT NULL ,
`rendertype_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

ALTER TABLE `#__sdi_sys_rendertype_criteriatype`
ADD CONSTRAINT `#__sdi_sys_rendertype_criteriatype_fk1` FOREIGN KEY (`criteriatype_id`) REFERENCES `#__sdi_sys_criteriatype` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_sys_rendertype_criteriatype`
ADD CONSTRAINT `#__sdi_sys_rendertype_criteriatype_fk2` FOREIGN KEY (`rendertype_id`) REFERENCES `#__sdi_sys_rendertype` (`id`) ON DELETE CASCADE;


CREATE TABLE IF NOT EXISTS `#__sdi_assignment` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`assigned` DATETIME ,
`assigned_by` INT(11) UNSIGNED  NOT NULL,
`assigned_to` INT(11) UNSIGNED NOT NULL ,
`version_id` INT(11) UNSIGNED NOT NULL ,
`text` VARCHAR (500),
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_assignment_fk1` (`assigned_by`) ,
INDEX `#__sdi_assignment_fk2` (`assigned_to`) ,
INDEX `#__sdi_assignment_fk3` (`version_id`) ,
  CONSTRAINT `#__sdi_assignment_fk1`
    FOREIGN KEY (`assigned_by` )
    REFERENCES `#__sdi_user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_assignment_fk2`
    FOREIGN KEY (`assigned_to` )
    REFERENCES `#__sdi_user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_assignment_fk3`
    FOREIGN KEY (`version_id` )
    REFERENCES `#__sdi_version` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__sdi_resource_role_user` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`resource_id` int(11) UNSIGNED ,
`role_id` int(11) UNSIGNED ,
`user_id` int(11) UNSIGNED ,
PRIMARY KEY (`id`),
INDEX `#__sdi_resource_role_user_fk1` (`resource_id`) ,
INDEX `#__sdi_resource_role_user_fk2` (`role_id`) ,
INDEX `#__sdi_resource_role_user_fk3` (`user_id`) ,
CONSTRAINT `#__sdi_resource_role_user_fk1`
    FOREIGN KEY (`resource_id` )
    REFERENCES `#__sdi_resource` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_resource_role_user_fk2`
    FOREIGN KEY (`role_id` )
    REFERENCES `#__sdi_sys_role` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
 CONSTRAINT `#__sdi_resource_role_user_fk3`
    FOREIGN KEY (`user_id` )
    REFERENCES `#__sdi_user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;