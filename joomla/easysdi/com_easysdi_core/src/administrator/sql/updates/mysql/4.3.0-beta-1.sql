ALTER TABLE `#__sdi_sys_logroll` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `#__sdi_sys_logroll` ENGINE=InnoDB;

ALTER TABLE `#__sdi_virtualmetadata` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `#__sdi_virtualmetadata` ENGINE=InnoDB;
CALL drop_foreign_key('sdi_virtualmetadata','sdi_virtualmetadata_fk1');
ALTER TABLE `#__sdi_virtualmetadata` ADD CONSTRAINT `#__sdi_virtualmetadata_fk1` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
CALL drop_foreign_key('sdi_virtualmetadata', 'sdi_virtualmetadata_fk2');
ALTER TABLE `#__sdi_virtualmetadata` ADD CONSTRAINT `#__sdi_virtualmetadata_fk2` FOREIGN KEY (`country_id`) REFERENCES `#__sdi_sys_country` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `#__sdi_csw_spatialpolicy` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `#__sdi_csw_spatialpolicy` ENGINE=InnoDB;

ALTER TABLE `#__sdi_wmts_spatialpolicy` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `#__sdi_wmts_spatialpolicy` ENGINE=InnoDB;
CALL drop_foreign_key('sdi_wmts_spatialpolicy', 'sdi_wmts_spatialpolicy_fk1');
ALTER TABLE `#__sdi_wmts_spatialpolicy` ADD CONSTRAINT `#__sdi_wmts_spatialpolicy_fk1` FOREIGN KEY (`spatialoperator_id`) REFERENCES `#__sdi_sys_spatialoperator` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `#__sdi_wms_spatialpolicy` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `#__sdi_wms_spatialpolicy` ENGINE=InnoDB;

ALTER TABLE `#__sdi_wfs_spatialpolicy` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `#__sdi_wfs_spatialpolicy` ENGINE=InnoDB;

ALTER TABLE `#__sdi_order_diffusion` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `#__sdi_order_diffusion` ENGINE=InnoDB;
CALL drop_foreign_key('sdi_order_diffusion', 'sdi_order_diffusion_fk1');
ALTER TABLE `#__sdi_order_diffusion` ADD CONSTRAINT `#__sdi_order_diffusion_fk1` FOREIGN KEY (`order_id`) REFERENCES `#__sdi_order` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
CALL drop_foreign_key('sdi_order_diffusion', 'sdi_order_diffusion_fk2');
ALTER TABLE `#__sdi_order_diffusion` ADD CONSTRAINT `#__sdi_order_diffusion_fk2` FOREIGN KEY (`diffusion_id`) REFERENCES `#__sdi_diffusion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
CALL drop_foreign_key('sdi_order_diffusion', 'sdi_order_diffusion_fk3');
ALTER TABLE `#__sdi_order_diffusion` ADD CONSTRAINT `#__sdi_order_diffusion_fk3` FOREIGN KEY (`productstate_id`) REFERENCES `#__sdi_sys_productstate` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `#__sdi_order_propertyvalue` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `#__sdi_order_propertyvalue` ENGINE=InnoDB;
CALL drop_foreign_key('sdi_order_propertyvalue', 'sdi_order_propertyvalue_fk1');
ALTER TABLE `#__sdi_order_propertyvalue` ADD CONSTRAINT `#__sdi_order_propertyvalue_fk1` FOREIGN KEY (`orderdiffusion_id`) REFERENCES `#__sdi_order_diffusion` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
CALL drop_foreign_key('sdi_order_propertyvalue', 'sdi_order_propertyvalue_fk2');
ALTER TABLE `#__sdi_order_propertyvalue` ADD CONSTRAINT `#__sdi_order_propertyvalue_fk2` FOREIGN KEY (`property_id`) REFERENCES `#__sdi_property` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
CALL drop_foreign_key('sdi_order_propertyvalue', 'sdi_order_propertyvalue_fk3');
ALTER TABLE `#__sdi_order_propertyvalue` ADD CONSTRAINT `#__sdi_order_propertyvalue_fk3` FOREIGN KEY (`propertyvalue_id`) REFERENCES `#__sdi_propertyvalue` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `#__sdi_order_perimeter` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `#__sdi_order_perimeter` ENGINE=InnoDB;
CALL drop_foreign_key('sdi_order_perimeter', 'sdi_order_perimeter_fk1');
ALTER TABLE `#__sdi_order_perimeter` ADD CONSTRAINT `#__sdi_order_perimeter_fk1` FOREIGN KEY (`order_id`) REFERENCES `#__sdi_order` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
CALL drop_foreign_key('sdi_order_perimeter', 'sdi_order_perimeter_fk2');
ALTER TABLE `#__sdi_order_perimeter` ADD CONSTRAINT `#__sdi_order_perimeter_fk2` FOREIGN KEY (`perimeter_id`) REFERENCES `#__sdi_perimeter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

INSERT IGNORE INTO `#__sdi_sys_role` SET id=9, ordering=9, `state`=1, value='pricingmanager';
INSERT IGNORE INTO `#__sdi_sys_role` SET id=10, ordering=10, `state`=1, `value`='validationmanager';

CALL drop_column('sdi_category', 'overall_fee');
ALTER TABLE `#__sdi_category` ADD `overall_fee` FLOAT(6,2) UNSIGNED DEFAULT 0;

CALL drop_column('sdi_organism', 'internal_free');
ALTER TABLE `#__sdi_organism` ADD `internal_free` TINYINT DEFAULT 0;
CALL drop_column('sdi_organism', 'fixed_fee_ti');
ALTER TABLE `#__sdi_organism` ADD `fixed_fee_ti` FLOAT(6,2) UNSIGNED DEFAULT 0;
CALL drop_column('sdi_organism', 'data_free_fixed_fee');
ALTER TABLE `#__sdi_organism` ADD `data_free_fixed_fee` TINYINT DEFAULT 0;
CALL drop_column('sdi_organism', 'selectable_as_thirdparty');
ALTER TABLE `#__sdi_organism` ADD `selectable_as_thirdparty` TINYINT(1) DEFAULT 0 AFTER perimeter;

CREATE TABLE IF NOT EXISTS `#__sdi_organism_category_pricing_rebate` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `organism_id` INT(11) UNSIGNED,
    `category_id` INT(11) UNSIGNED,
    `rebate` FLOAT(6,2),
    PRIMARY KEY (`id`),
  KEY `#__sdi_organism_category_pricing_rebate_fk1` (`organism_id`),
  KEY `#__sdi_organism_category_pricing_rebate_fk2` (`category_id`),
  CONSTRAINT `#__sdi_organism_category_pricing_rebate_fk1` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_organism_category_pricing_rebate_fk2` FOREIGN KEY (`category_id`) REFERENCES `#__sdi_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

UPDATE `#__sdi_sys_pricing` SET `value`='fee without a pricing profile' WHERE id=2;
INSERT IGNORE INTO `#__sdi_sys_pricing` SET `ordering`=3, `state`=1, `value`='fee with a pricing profile';

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_profile` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `guid` VARCHAR(36)  NOT NULL ,
    `alias` VARCHAR(50)   ,
    `created_by` INT(11)  NOT NULL ,
    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` INT(11)   ,
    `modified` DATETIME ,
    `ordering` INT(11)  ,
    `state` int(11)  NOT NULL DEFAULT '1',
    `checked_out` INT(11) NOT NULL DEFAULT '0'  ,
    `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `organism_id` int(11) UNSIGNED NOT NULL,
    `name` varchar(75) NOT NULL,
    `fixed_fee` FLOAT(6,2),
    `surface_rate` FLOAT(6,2),
    `min_fee` FLOAT(6,2),
    `max_fee` FLOAT(6,2),
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_profile_fk1` (`organism_id`),
    CONSTRAINT `#__sdi_pricing_profile_fk1` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_profile_category_pricing_rebate` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `pricing_profile_id` int(11) UNSIGNED NOT NULL,
    `category_id` int(11) UNSIGNED NOT NULL,
    `rebate` FLOAT(6,2) UNSIGNED DEFAULT 100,
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_profile_category_pricing_rebate_fk1` (`pricing_profile_id`),
    KEY `#__sdi_pricing_profile_category_pricing_rebate_fk2` (`category_id`),
    CONSTRAINT `#__sdi_pricing_profile_category_pricing_rebate_fk1` FOREIGN KEY (`pricing_profile_id`) REFERENCES `#__sdi_pricing_profile` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `#__sdi_pricing_profile_category_pricing_rebate_fk2` FOREIGN KEY (`category_id`) REFERENCES `#__sdi_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CALL drop_foreign_key('sdi_diffusion', 'sdi_diffusion_fk6');
CALL drop_column('sdi_diffusion', 'pricing_profile_id');
ALTER TABLE `#__sdi_diffusion` ADD pricing_profile_id int(11) UNSIGNED AFTER pricing_id;
ALTER TABLE `#__sdi_diffusion` ADD CONSTRAINT `#__sdi_diffusion_fk6` FOREIGN KEY (`pricing_profile_id`) REFERENCES `#__sdi_pricing_profile` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;



INSERT IGNORE INTO `#__sdi_sys_orderstate` SET id=8, ordering=8, `state`=1, `value`='validation';
INSERT IGNORE INTO `#__sdi_sys_orderstate` SET id=9, ordering=9, `state`=1, `value`='rejected by thirdparty';
INSERT IGNORE INTO `#__sdi_sys_orderstate` SET id=10, ordering=10, `state`=1, `value`='rejected by supplier';

CALL drop_column('sdi_order', 'validated');
ALTER TABLE `#__sdi_order` ADD `validated` TINYINT(1) DEFAULT NULL AFTER thirdparty_id;
CALL drop_column('sdi_order', 'validated_date');
ALTER TABLE `#__sdi_order` ADD `validated_date` DATETIME DEFAULT NULL AFTER validated;
CALL drop_column('sdi_order', 'validated_reason');
ALTER TABLE `#__sdi_order` ADD `validated_reason` VARCHAR(500) AFTER validated_date;


CREATE TABLE IF NOT EXISTS `#__sdi_pricing_order` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `guid` VARCHAR(36)  NOT NULL ,
    `created_by` INT(11)  NOT NULL ,
    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` INT(11)   ,
    `modified` DATETIME ,
    `ordering` INT(11)  ,
    `state` int(11)  NOT NULL DEFAULT '1',
    `checked_out` INT(11) NOT NULL DEFAULT '0'  ,
    `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `order_id` int(11) UNSIGNED NOT NULL,
    `cfg_vat` decimal(6,2) NOT NULL DEFAULT 0,
    `cfg_currency` char(3) NOT NULL DEFAULT 'CHF',
    `cfg_rounding` decimal(3,2) NOT NULL DEFAULT '0.05',
    `cfg_overall_default_fee` decimal(6,2) NOT NULL DEFAULT 0,
    `cfg_free_data_fee` TINYINT DEFAULT 0,
    `cal_total_amount_ti` float,
    `cal_fee_ti` decimal(6,2) NOT NULL DEFAULT 0,
    `ind_lbl_category_order_fee` varchar(255),
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_order_fk1` (`order_id`),
    CONSTRAINT `#__sdi_pricing_order_fk1` FOREIGN KEY (`order_id`) REFERENCES `#__sdi_order` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_order_supplier` (
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `guid` VARCHAR(36)  NOT NULL ,
    `created_by` INT(11)  NOT NULL ,
    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` INT(11)   ,
    `modified` DATETIME ,
    `ordering` INT(11)  ,
    `state` int(11)  NOT NULL DEFAULT '1',
    `checked_out` INT(11) NOT NULL DEFAULT '0'  ,
    `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `pricing_order_id` int(11) UNSIGNED NOT NULL,
    `supplier_id` int(11) UNSIGNED NOT NULL,
    `supplier_name` varchar(255) NOT NULL,
    `cfg_internal_free` TINYINT NOT NULL DEFAULT 1,
    `cfg_fixed_fee_ti` decimal(6,2) NOT NULL DEFAULT 0,
    `cfg_data_free_fixed_fee` TINYINT NOT NULL DEFAULT 0,
    `cal_total_rebate_ti` float NOT NULL DEFAULT 0,
    `cal_fee_ti` decimal(6,2) NOT NULL DEFAULT 0,
    `cal_total_amount_ti` float,
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_order_supplier_fk1` (`pricing_order_id`),
    KEY `#__sdi_pricing_order_supplier_fk2` (`supplier_id`),
    CONSTRAINT `#__sdi_pricing_order_supplier_fk1` FOREIGN KEY (`pricing_order_id`) REFERENCES `#__sdi_pricing_order` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `#__sdi_pricing_order_supplier_fk2` FOREIGN KEY (`supplier_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_order_supplier_product` (
    `id` int(11) unsigned not null auto_increment,
    `guid` VARCHAR(36)  NOT NULL ,
    `created_by` INT(11)  NOT NULL ,
    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` INT(11)   ,
    `modified` DATETIME ,
    `ordering` INT(11)  ,
    `state` int(11)  NOT NULL DEFAULT '1',
    `checked_out` INT(11) NOT NULL DEFAULT '0'  ,
    `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `pricing_order_supplier_id` int(11) unsigned not null,
    `product_id` int(11) unsigned not null,
    `pricing_id` int(11) unsigned not null,
    `cfg_pct_category_supplier_discount` decimal(6,2) NOT NULL DEFAULT 0,
    `ind_lbl_category_supplier_discount` varchar(255),
    `cal_amount_data_te` float,
    `cal_total_amount_te` float,
    `cal_total_amount_ti` float,
    `cal_total_rebate_ti` float,
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_order_supplier_product_fk1` (`pricing_order_supplier_id`),
    KEY `#__sdi_pricing_order_supplier_product_fk2` (`product_id`),
    KEY `#__sdi_pricing_order_supplier_product_fk3` (`pricing_id`),
    CONSTRAINT `#__sdi_pricing_order_supplier_product_fk1` FOREIGN KEY (`pricing_order_supplier_id`) REFERENCES `#__sdi_pricing_order_supplier` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `#__sdi_pricing_order_supplier_product_fk2` FOREIGN KEY (`product_id`) REFERENCES `#__sdi_diffusion` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `#__sdi_pricing_order_supplier_product_fk3` FOREIGN KEY (`pricing_id`) REFERENCES `#__sdi_sys_pricing` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_pricing_order_supplier_product_profile` (
    `id` int(11) unsigned not null auto_increment,
    `guid` VARCHAR(36)  NOT NULL ,
    `created_by` INT(11)  NOT NULL ,
    `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_by` INT(11)   ,
    `modified` DATETIME ,
    `ordering` INT(11)  ,
    `state` int(11)  NOT NULL DEFAULT '1',
    `checked_out` INT(11) NOT NULL DEFAULT '0'  ,
    `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `pricing_order_supplier_product_id` int(11) unsigned not null,
    `pricing_profile_id` int(11) unsigned not null,
    `pricing_profile_name` varchar(255) not null,
    `cfg_fixed_fee` decimal(6,2) NOT NULL DEFAULT 0,
    `cfg_surface_rate` decimal(6,2) NOT NULL DEFAULT 0,
    `cfg_min_fee` decimal(6,2) NOT NULL DEFAULT 0,
    `cfg_max_fee` decimal(6,2) NOT NULL DEFAULT 0,
    `cfg_pct_category_profile_discount` decimal(6,2) NOT NULL DEFAULT 0,
    `ind_lbl_category_profile_discount` varchar(255),
    PRIMARY KEY (`id`),
    KEY `#__sdi_pricing_order_supplier_product_profile_fk1` (`pricing_order_supplier_product_id`),
    KEY `#__sdi_pricing_order_supplier_product_profile_fk2` (`pricing_profile_id`),
    CONSTRAINT `#__sdi_pricing_order_supplier_product_profile_fk1` FOREIGN KEY (`pricing_order_supplier_product_id`) REFERENCES `#__sdi_pricing_order_supplier_product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT `#__sdi_pricing_order_supplier_product_profile_fk2` FOREIGN KEY (`pricing_profile_id`) REFERENCES `#__sdi_pricing_profile` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT IGNORE INTO `#__sdi_sys_productstate` SET id=4, ordering=4, `state`=1, `value`='validation';
INSERT IGNORE INTO `#__sdi_sys_productstate` SET id=5, ordering=5, `state`=1, `value`='rejected by thirdparty';
INSERT IGNORE INTO `#__sdi_sys_productstate` SET id=6, ordering=6, `state`=1, `value`='rejected by supplier';

CREATE TABLE IF NOT EXISTS `#__sdi_sys_extractstorage` (
    `id` int(11) unsigned not null auto_increment,
    `ordering` int(11),
    `state` int(11) not null default '1',
    `value` varchar(255) not null,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT IGNORE INTO `#__sdi_sys_extractstorage` SET id=1, ordering=1, `state`=1, `value`='local';
INSERT IGNORE INTO `#__sdi_sys_extractstorage` SET id=2, ordering=2, `state`=1, `value`='remote';


