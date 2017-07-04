ALTER TABLE `#__sdi_diffusion` ADD COLUMN `otp` tinyint(1) NOT NULL DEFAULT 0 AFTER `restrictedperimeter`;
ALTER TABLE `#__sdi_order_diffusion` ADD COLUMN `otp` TEXT NULL AFTER `displayName`;
ALTER TABLE `#__sdi_order_diffusion` ADD COLUMN `otpchance` INT(11) DEFAULT 0 AFTER `otp`;
ALTER TABLE `#__sdi_catalog_searchcriteria` MODIFY COLUMN `defaultvalue` text;
CALL drop_foreign_key('sdi_pricing_order_supplier_product_profile', 'sdi_pricing_order_supplier_product_profile_fk2');
ALTER TABLE `#__sdi_pricing_order_supplier_product_profile` MODIFY `pricing_profile_id` INT(11) UNSIGNED NULL, 
ADD CONSTRAINT `#__sdi_pricing_order_supplier_product_profile_fk2` FOREIGN KEY (`pricing_profile_id`) REFERENCES `#__sdi_pricing_profile` (`id`) ON UPDATE NO ACTION ON DELETE SET NULL;