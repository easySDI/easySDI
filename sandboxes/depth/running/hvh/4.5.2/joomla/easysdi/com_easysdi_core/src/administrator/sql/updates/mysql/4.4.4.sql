ALTER TABLE `#__sdi_pricing_profile` ADD COLUMN `apply_vat` TINYINT DEFAULT 1;

ALTER TABLE `#__sdi_organism` ADD COLUMN `fixed_fee_apply_vat` TINYINT DEFAULT 1;
ALTER TABLE `#__sdi_organism` CHANGE `fixed_fee_ti` `fixed_fee_te` FLOAT(6,2) UNSIGNED DEFAULT 0;

ALTER TABLE `#__sdi_pricing_order` CHANGE `cfg_overall_default_fee` `cfg_overall_default_fee_te` decimal(19,2) NOT NULL DEFAULT 0;
ALTER TABLE `#__sdi_pricing_order` ADD COLUMN `cfg_fee_apply_vat` TINYINT DEFAULT 1 AFTER `cfg_overall_default_fee_te`;

ALTER TABLE `#__sdi_pricing_order_supplier` CHANGE `cfg_fixed_fee_ti` `cfg_fixed_fee_te` decimal(19,2) NOT NULL DEFAULT 0;
ALTER TABLE `#__sdi_pricing_order_supplier` ADD COLUMN `cfg_fixed_fee_apply_vat` TINYINT NOT NULL DEFAULT 1 AFTER `cfg_fixed_fee_te`;

ALTER TABLE `#__sdi_pricing_order_supplier_product_profile` CHANGE `cfg_fixed_fee` `cfg_fixed_fee_te` decimal(19,2) NOT NULL DEFAULT 0;
ALTER TABLE `#__sdi_pricing_order_supplier_product_profile` ADD COLUMN `cfg_apply_vat` TINYINT NOT NULL DEFAULT 1 AFTER `cfg_fixed_fee_te`;
