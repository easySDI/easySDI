ALTER TABLE `#__sdi_diffusion` ADD COLUMN `otp` tinyint(1) NOT NULL DEFAULT 0 AFTER `restrictedperimeter`;
ALTER TABLE `#__sdi_order_diffusion` ADD COLUMN `otp` TEXT NULL AFTER `displayName`;
ALTER TABLE `#__sdi_order_diffusion` ADD COLUMN `otpchance` INT(11) DEFAULT 0 AFTER `otp`;
INSERT IGNORE INTO `#__sdi_sys_productstate` VALUES ('8', '8', '1', 'blocked');