ALTER TABLE `#__sdi_diffusion` ADD COLUMN `pricing_remark` TEXT NULL AFTER `pricing_profile_id`;

UPDATE  `#__sdi_sys_productmining` SET `ordering` = 2 WHERE `id` = 1;
UPDATE  `#__sdi_sys_productmining` SET `ordering` = 1 WHERE `id` = 2;
