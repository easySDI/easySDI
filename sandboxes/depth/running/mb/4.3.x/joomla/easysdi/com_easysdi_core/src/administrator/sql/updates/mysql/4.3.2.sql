INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('9', '9', '1', 'upload');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('10', '10', '1', 'url');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('11', '11', '1', 'upload and url');

INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('22', '14', '9');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('23', '14', '10');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('24', '14', '11');

DELETE FROM `#__sdi_sys_rendertype_stereotype` WHERE id=20;

ALTER TABLE `#__sdi_visualization` MODIFY `alias` VARCHAR(50) NOT NULL;

INSERT IGNORE INTO `#__sdi_sys_role` SET id=11, ordering=11, `state`=1, `value`='organismmanager';

ALTER TABLE #__sdi_order ADD COLUMN `freeperimetertool` VARCHAR(100) NULL AFTER `level`;

ALTER TABLE `#__sdi_pricing_profile`
MODIFY COLUMN `fixed_fee`  decimal(19,2) NULL DEFAULT NULL AFTER `name`,
MODIFY COLUMN `surface_rate`  decimal(19,2) NULL DEFAULT NULL AFTER `fixed_fee`,
MODIFY COLUMN `min_fee`  decimal(19,2) NULL DEFAULT NULL AFTER `surface_rate`,
MODIFY COLUMN `max_fee`  decimal(19,2) NULL DEFAULT NULL AFTER `min_fee`;

ALTER TABLE `#__sdi_pricing_order_supplier_product_profile`
MODIFY COLUMN `cfg_fixed_fee`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `pricing_profile_name`,
MODIFY COLUMN `cfg_surface_rate`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `cfg_fixed_fee`,
MODIFY COLUMN `cfg_min_fee`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `cfg_surface_rate`,
MODIFY COLUMN `cfg_max_fee`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `cfg_min_fee`,
MODIFY COLUMN `cfg_pct_category_profile_discount`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `cfg_max_fee`;

ALTER TABLE `#__sdi_pricing_order_supplier_product`
MODIFY COLUMN `cfg_pct_category_supplier_discount`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `pricing_id`;

ALTER TABLE `#__sdi_pricing_order_supplier`
MODIFY COLUMN `cfg_fixed_fee_ti`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `cfg_internal_free`,
MODIFY COLUMN `cal_fee_ti`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `cal_total_rebate_ti`;

ALTER TABLE `#__sdi_pricing_order`
MODIFY COLUMN `cfg_vat`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `order_id`,
MODIFY COLUMN `cfg_overall_default_fee`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `cfg_rounding`,
MODIFY COLUMN `cal_fee_ti`  decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `cal_total_amount_ti`;

ALTER TABLE `#__sdi_organism_category_pricing_rebate`
MODIFY COLUMN `rebate`  decimal(19,2) NULL DEFAULT NULL AFTER `category_id`;

ALTER TABLE #__sdi_diffusion ADD packageurl VARCHAR(500) ;
UPDATE #__sdi_diffusion SET packageurl='{CODE}';

ALTER TABLE `#__sdi_translation` ADD INDEX `text1` (`text1`);
ALTER TABLE `#__sdi_translation` ADD INDEX `text2` (`text2`);

INSERT IGNORE INTO `#__sdi_sys_productstate` VALUES ('7', '7', '1', 'deleted');

INSERT INTO `#__sdi_sys_rendertype_criteriatype` VALUES ('3', '3', '2');

DELETE FROM `#__sdi_sys_metadatastate` WHERE id=5;

UPDATE `#__sdi_sys_orderstate` SET `value` = 'rejectedbythirdparty' WHERE `id` = 9;
UPDATE `#__sdi_sys_orderstate` SET `value` = 'rejectedbysupplier' WHERE `id` = 10;

UPDATE `#__sdi_sys_productstate` SET `value` = 'rejectedbythirdparty' WHERE `id` = 5;
UPDATE `#__sdi_sys_productstate` SET `value` = 'rejectedbysupplier' WHERE `id` = 6;

ALTER TABLE `#__sdi_order` MODIFY `remark` VARCHAR(4000);

UPDATE `#__sdi_sys_pricing` SET `value` = 'feewithoutapricingprofile' WHERE `id` = 2;
UPDATE `#__sdi_sys_pricing` SET `value` = 'feewithapricingprofile' WHERE `id` = 3;

ALTER TABLE `#__sdi_pricing_order_supplier_product`
MODIFY COLUMN `cal_amount_data_te`  decimal(19,2) NULL AFTER `ind_lbl_category_supplier_discount`,
MODIFY COLUMN `cal_total_amount_te` decimal(19,2) NULL AFTER `cal_amount_data_te`,
MODIFY COLUMN `cal_total_amount_ti` decimal(19,2) NULL AFTER `cal_total_amount_te`,
MODIFY COLUMN `cal_total_rebate_ti` decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `cal_total_amount_ti`;

ALTER TABLE `#__sdi_pricing_order_supplier`
MODIFY COLUMN `cal_total_rebate_ti` decimal(19,2) NOT NULL DEFAULT 0.00 AFTER `cfg_data_free_fixed_fee`,
MODIFY COLUMN `cal_total_amount_ti` decimal(19,2) NULL AFTER `cal_fee_ti`;

ALTER TABLE `#__sdi_pricing_order`
MODIFY COLUMN `cal_total_amount_ti` decimal(19,2) NULL AFTER `cfg_free_data_fee`;

UPDATE `#__sdi_sys_orderstate` SET `ordering` = 1  WHERE `id` = 7;
UPDATE `#__sdi_sys_orderstate` SET `ordering` = 2  WHERE `id` = 8;
UPDATE `#__sdi_sys_orderstate` SET `ordering` = 3  WHERE `id` = 6;
UPDATE `#__sdi_sys_orderstate` SET `ordering` = 4  WHERE `id` = 4;
UPDATE `#__sdi_sys_orderstate` SET `ordering` = 5  WHERE `id` = 5;
UPDATE `#__sdi_sys_orderstate` SET `ordering` = 6  WHERE `id` = 3;
UPDATE `#__sdi_sys_orderstate` SET `ordering` = 7  WHERE `id` = 1;
UPDATE `#__sdi_sys_orderstate` SET `ordering` = 8  WHERE `id` = 2;
UPDATE `#__sdi_sys_orderstate` SET `ordering` = 9  WHERE `id` = 9;
UPDATE `#__sdi_sys_orderstate` SET `ordering` = 10 WHERE `id` = 10;

ALTER TABLE `#__sdi_order`
ADD COLUMN `validated_by` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `validated_reason`,
ADD CONSTRAINT `#__sdi_order_fk5` FOREIGN KEY (`validated_by`) REFERENCES `#__sdi_user` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;

ALTER TABLE `#__sdi_order_diffusion` MODIFY `remark` VARCHAR(4000);

ALTER TABLE `#__sdi_order` MODIFY `mandate_ref` VARCHAR(500);
