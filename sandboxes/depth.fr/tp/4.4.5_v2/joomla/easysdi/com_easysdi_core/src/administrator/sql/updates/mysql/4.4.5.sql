ALTER TABLE `#__sdi_diffusion` ADD COLUMN `otp` tinyint(1) NOT NULL DEFAULT 0 AFTER `restrictedperimeter`;
ALTER TABLE `#__sdi_order_diffusion` ADD COLUMN `otp` TEXT NULL AFTER `displayName`;
ALTER TABLE `#__sdi_order_diffusion` ADD COLUMN `otpchance` INT(11) DEFAULT 0 AFTER `otp`;
INSERT IGNORE INTO `#__sdi_sys_productstate` VALUES ('8', '8', '1', 'blocked');
ALTER TABLE `#__sdi_map` ADD COLUMN `default_backgroud_layer` NOT NULL DEFAULT '0' AFTER `state`;
ALTER TABLE `#__sdi_catalog_searchcriteria` MODIFY COLUMN `defaultvalue` text;
CALL drop_foreign_key('sdi_pricing_order_supplier_product_profile', 'sdi_pricing_order_supplier_product_profile_fk2');
ALTER TABLE `#__sdi_pricing_order_supplier_product_profile` MODIFY `pricing_profile_id` INT(11) UNSIGNED NULL, 
ADD CONSTRAINT `#__sdi_pricing_order_supplier_product_profile_fk2` FOREIGN KEY (`pricing_profile_id`) REFERENCES `#__sdi_pricing_profile` (`id`) ON UPDATE NO ACTION ON DELETE SET NULL;