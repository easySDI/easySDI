ALTER TABLE `#__sdi_diffusion_download` DROP FOREIGN KEY `#__sdi_diffusion_download_fk2`;
ALTER TABLE `#__sdi_diffusion_download` ADD CONSTRAINT `#__sdi_diffusion_download_fk2` FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) ON DELETE SET NULL ON UPDATE NO ACTION;

ALTER TABLE `#__sdi_order` ADD COLUMN `archived` tinyint(1) NOT NULL DEFAULT 0 AFTER `orderstate_id`;
UPDATE `#__sdi_order` SET `archived`=1 WHERE `orderstate_id`=1;
UPDATE `#__sdi_order` SET `orderstate_id`=3 WHERE `orderstate_id`=1;
DELETE FROM `#__sdi_sys_orderstate` WHERE `id`=1;

ALTER TABLE `#__sdi_order_diffusion` DROP COLUMN `fee`;

ALTER TABLE `#__sdi_order` DROP COLUMN `buffer`;

ALTER TABLE `#__sdi_diffusion_perimeter` DROP COLUMN `buffer`;