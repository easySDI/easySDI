CREATE TABLE IF NOT EXISTS `#__sdi_grid` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00',
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`map_id` INT(11)  NOT NULL ,
`wfsservice_id` INT(11)  NOT NULL ,
`featuretype` VARCHAR(255)  NOT NULL ,
`prefix` VARCHAR(255)  NOT NULL ,
`namespace` VARCHAR(255)  NOT NULL ,
`featuretypefieldname` VARCHAR(255)  NOT NULL ,
`featuretypefielddescription` VARCHAR(255)  NOT NULL ,
`featuretypefieldgeometry` VARCHAR(255)  NOT NULL ,
`featuretypefieldresource` VARCHAR(255)  NOT NULL ,
`tooltip` TINYINT(1)  NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
  INDEX `#__sdi_grid_fk1` (`map_id` ASC) ,
  CONSTRAINT `#__sdi_grid_fk1`
    FOREIGN KEY (`map_id`)
    REFERENCES `#__sdi_map` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_diffusion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00',
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00',
`version_id` INT(11)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`accessscope_id` INT(11)  NOT NULL ,
`pricing_id` INT(11)  NOT NULL ,
`deposit` VARCHAR(255)  NOT NULL ,
`productmining_id` INT(11)  NOT NULL ,
`surfacemin` BIGINT(20)  NOT NULL ,
`surfacemax` BIGINT(20)  NOT NULL ,
`productstorage_id` INT(11)  NOT NULL ,
`file` VARCHAR(255)  NOT NULL ,
`fileurl` VARCHAR(500)  NOT NULL ,
`grid_id` INT(11)  NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
    INDEX `#__sdi_diffusion_fk1` (`accessscope_id` ASC) ,
    INDEX `#__sdi_diffusion_fk2` (`productmining_id` ASC) ,
    INDEX `#__sdi_diffusion_fk3` (`productstorage_id` ASC) ,
    INDEX `#__sdi_diffusion_fk4` (`grid_id` ASC) ,
    INDEX `#__sdi_diffusion_fk5` (`version_id` ASC) ,
  CONSTRAINT `#__sdi_diffusion_fk1`
    FOREIGN KEY (`accessscope_id`)
    REFERENCES `#__sdi_sys_accessscope` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_fk2`
    FOREIGN KEY (`productmining_id`)
    REFERENCES `#__sdi_sys_productmining` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_fk3`
    FOREIGN KEY (`productstorage_id`)
    REFERENCES `#__sdi_sys_productstorage` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_fk4`
    FOREIGN KEY (`grid_id`)
    REFERENCES `#__sdi_grid` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_fk5`
    FOREIGN KEY (`version_id`)
    REFERENCES `#__sdi_version` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_propertytype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_property` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00',
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`accessscope_id` INT(11)  NOT NULL ,
`mandatory` INT(1)  NOT NULL ,
`propertytype_id` INT(11)  NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
    INDEX `#__sdi_property_fk1` (`accessscope_id` ASC) ,
    INDEX `#__sdi_property_fk2` (`propertytype_id` ASC) ,
  CONSTRAINT `#__sdi_property_fk1`
    FOREIGN KEY (`accessscope_id`)
    REFERENCES `#__sdi_sys_accessscope` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_property_fk2`
    FOREIGN KEY (`propertytype_id`)
    REFERENCES `#__sdi_sys_propertytype` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_propertyvalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00',
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`property_id` INT(11)  NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
    INDEX `#__sdi_propertyvalue_fk1` (`property_id` ASC) ,
  CONSTRAINT `#__sdi_propertyvalue_fk1`
    FOREIGN KEY (`property_id`)
    REFERENCES `#__sdi_property` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_perimeter` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00',
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00',
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(500)  NOT NULL ,
`accessscope_id` INT(11)  NOT NULL ,
`wfsservice_id` INT(11)  NOT NULL ,
`featuretypeid` VARCHAR(255)  NOT NULL ,
`featuretypename` VARCHAR(255)  NOT NULL ,
`featuretypefieldsurface` VARCHAR(255)  NOT NULL ,
`featuretypefielddisplay` VARCHAR(255)  NOT NULL ,
`featuretypefieldsearch` VARCHAR(255)  NOT NULL ,
`wmsservice_id` INT(11)  NOT NULL ,
`layername` VARCHAR(255)  NOT NULL ,
`minscale` VARCHAR(255)  NOT NULL ,
`maxscale` VARCHAR(255)  NOT NULL ,
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
  INDEX `#__sdi_perimeter_fk1` (`accessscope_id` ASC) ,
  CONSTRAINT `#__sdi_perimeter_fk1`
    FOREIGN KEY (`accessscope_id`)
    REFERENCES `#__sdi_sys_accessscope` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



CREATE TABLE IF NOT EXISTS `#__sdi_order` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00',
`modified_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`ordering` INT(11)  NOT NULL DEFAULT '1',
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00',
`name` VARCHAR(255)  NOT NULL ,
`ordertype_id` INT(11)  NOT NULL ,
`orderstate_id` INT(11)  NOT NULL ,
`user_id` INT(11)  NOT NULL ,
`thirdparty_id` INT(11)  NOT NULL ,
`buffer` BIGINT(20)  NOT NULL ,
`surface` BIGINT(20)  NOT NULL ,
`remark` VARCHAR(500)  NOT NULL ,
`sent` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`completed` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`access` INT(10)  NOT NULL DEFAULT '1',
`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
PRIMARY KEY (`id`),
  INDEX `#__sdi_order_fk1` (`ordertype_id` ASC) ,
  INDEX `#__sdi_order_fk2` (`orderstate_id` ASC) ,
  INDEX `#__sdi_order_fk3` (`user_id` ASC) ,
  INDEX `#__sdi_order_fk4` (`thirdparty_id` ASC) ,
  CONSTRAINT `#__sdi_order_fk1`
    FOREIGN KEY (`ordertype_id`)
    REFERENCES `#__sdi_sys_ordertype` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_fk2`
    FOREIGN KEY (`orderstate_id`)
    REFERENCES `#__sdi_sys_orderstate` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_fk3`
    FOREIGN KEY (`user_id`)
    REFERENCES `#__sdi_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION.
  CONSTRAINT `#__sdi_order_fk4`
    FOREIGN KEY (`thirdparty_id`)
    REFERENCES `#__sdi_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_order_diffusion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`order_id` INT NOT NULL ,
`diffusion_id` INT NOT NULL ,
`productstate_id` INT(11)  NOT NULL ,
`remark` VARCHAR(500)  NOT NULL ,
`fee` DECIMAL(10)  NOT NULL ,
`completed` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`file` VARCHAR(500)  NOT NULL ,
`size` DECIMAL(10)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`),
  INDEX `#__sdi_order_diffusion_fk1` (`order_id` ASC) ,
  INDEX `#__sdi_order_diffusion_fk2` (`diffusion_id` ASC) ,
  INDEX `#__sdi_order_diffusion_fk3` (`productstate_id` ASC) ,
  CONSTRAINT `#__sdi_order_diffusion_fk1`
    FOREIGN KEY (`order_id`)
    REFERENCES `#__sdi_order` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_diffusion_fk2`
    FOREIGN KEY (`diffusion_id`)
    REFERENCES `#__sdi_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_diffusion_fk3`
    FOREIGN KEY (`productstate_id`)
    REFERENCES `#__sdi_sys_productstate` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_order_propertyvalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`orderdiffusion_id` INT NOT NULL ,
`property_id` INT NOT NULL ,
`propertyvalue_id` INT NOT NULL ,
`propertyvalue` VARCHAR(4000)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`),
  INDEX `#__sdi_order_propertyvalue_fk1` (`orderdiffusion_id` ASC) ,
  INDEX `#__sdi_order_propertyvalue_fk2` (`property_id` ASC) ,
  INDEX `#__sdi_order_propertyvalue_fk3` (`propertyvalue_id` ASC) ,
  CONSTRAINT `#__sdi_order_propertyvalue_fk1`
    FOREIGN KEY (`orderdiffusion_id`)
    REFERENCES `#__sdi_order_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_propertyvalue_fk2`
    FOREIGN KEY (`property_id`)
    REFERENCES `#__sdi_property` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_propertyvalue_fk3`
    FOREIGN KEY (`propertyvalue_id`)
    REFERENCES `#__sdi_propertyvalue` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_order_perimeter` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`order_id` INT NOT NULL ,
`perimeter_id` INT NOT NULL ,
`value` VARCHAR(500)  NOT NULL ,
`text` VARCHAR(500)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`),
  INDEX `#__sdi_order_perimeter_fk1` (`order_id` ASC) ,
  INDEX `#__sdi_order_perimeter_fk2` (`perimeter_id` ASC) ,
  CONSTRAINT `#__sdi_order_perimeter_fk1`
    FOREIGN KEY (`order_id`)
    REFERENCES `#__sdi_order` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_order_perimeter_fk2`
    FOREIGN KEY (`perimeter_id`)
    REFERENCES `#__sdi_perimeter` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_diffusion_notifieduser` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`diffusion_id` INT NOT NULL ,
`user_id` INT(11)  NOT NULL ,
PRIMARY KEY (`id`),
    INDEX `#__sdi_diffusion_notifieduser_fk1` (`diffusion_id` ASC) ,
    INDEX `#__sdi_diffusion_notifieduser_fk2` (`user_id` ASC) ,
  CONSTRAINT `#__sdi_diffusion_notifieduser_fk1`
    FOREIGN KEY (`diffusion_id`)
    REFERENCES `#__sdi_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_diffusion_notifieduser_fk2`
    FOREIGN KEY (`user_id`)
    REFERENCES `#__sdi_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_diffusion_perimeter` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`diffusion_id` INT NOT NULL ,
`perimeter_id` INT NOT NULL ,
`buffer` TINYINT(1)  NOT NULL ,
PRIMARY KEY (`id`),
    INDEX `#__sdi_diffusion_perimeter_fk1` (`diffusion_id` ASC) ,
    INDEX `#__sdi_diffusion_perimeter_fk2` (`perimeter_id` ASC) ,
  CONSTRAINT `#__sdi_diffusion_perimeter_fk1`
    FOREIGN KEY (`diffusion_id`)
    REFERENCES `#__sdi_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_perimeter_fk2`
    FOREIGN KEY (`perimeter_id`)
    REFERENCES `#__sdi_perimeter` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_diffusion_propertyvalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`diffusion_id` INT NOT NULL ,
`propertyvalue_id` INT(11)  NOT NULL ,
PRIMARY KEY (`id`),
    INDEX `#__sdi_diffusion_propertyvalue_fk1` (`diffusion_id` ASC) ,
    INDEX `#__sdi_diffusion_propertyvalue_fk2` (`propertyvalue_id` ASC) ,
  CONSTRAINT `#__sdi_diffusion_propertyvalue_fk1`
    FOREIGN KEY (`diffusion_id`)
    REFERENCES `#__sdi_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_propertyvalue_fk2`
    FOREIGN KEY (`propertyvalue_id`)
    REFERENCES `#__sdi_propertyvalue` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

