ALTER TABLE `#__sdi_pricing_profile` ADD COLUMN `apply_vat` TINYINT DEFAULT 1;

ALTER TABLE `#__sdi_organism` ADD COLUMN `fixed_fee_apply_vat` TINYINT DEFAULT 1;
ALTER TABLE `#__sdi_organism` CHANGE `fixed_fee_ti` `fixed_fee_te` FLOAT(6,2) UNSIGNED DEFAULT 0;

ALTER TABLE `#__sdi_pricing_order` CHANGE `cfg_overall_default_fee` `cfg_overall_default_fee_te` decimal(19,2) NOT NULL DEFAULT 0;
ALTER TABLE `#__sdi_pricing_order` ADD COLUMN `cfg_fee_apply_vat` TINYINT DEFAULT 1 AFTER `cfg_overall_default_fee_te`;

ALTER TABLE `#__sdi_pricing_order_supplier` CHANGE `cfg_fixed_fee_ti` `cfg_fixed_fee_te` decimal(19,2) NOT NULL DEFAULT 0;
ALTER TABLE `#__sdi_pricing_order_supplier` ADD COLUMN `cfg_fixed_fee_apply_vat` TINYINT NOT NULL DEFAULT 1 AFTER `cfg_fixed_fee_te`;

ALTER TABLE `#__sdi_pricing_order_supplier_product_profile` CHANGE `cfg_fixed_fee` `cfg_fixed_fee_te` decimal(19,2) NOT NULL DEFAULT 0;
ALTER TABLE `#__sdi_pricing_order_supplier_product_profile` ADD COLUMN `cfg_apply_vat` TINYINT NOT NULL DEFAULT 1 AFTER `cfg_fixed_fee_te`;

UPDATE `#__sdi_pricing_order` SET cfg_overall_default_fee_te = cfg_overall_default_fee_te / (1+(cfg_vat/100));

UPDATE `#__sdi_pricing_order_supplier` s SET s.cfg_fixed_fee_te = s.cfg_fixed_fee_te / (1+((SELECT o.cfg_vat FROM `#__sdi_pricing_order` o WHERE o.id = s.pricing_order_id)/100));

UPDATE `#__sdi_pricing_order_supplier_product_profile` pospp SET pospp.cfg_fixed_fee_te = pospp.cfg_fixed_fee_te / (1+((SELECT o.cfg_vat FROM `#__sdi_pricing_order` o INNER JOIN `#__sdi_pricing_order_supplier` pos ON o.id = pos.pricing_order_id  INNER JOIN `#__sdi_pricing_order_supplier_product` posp ON pos.id = posp.pricing_order_supplier_id  WHERE posp.id = pospp.pricing_order_supplier_product_id)/100));
