

ALTER TABLE `#__sdi_order_diffusion` ADD `storage_id` INT(11) UNSIGNED NOT NULL AFTER completed;
UPDATE `#__sdi_order_diffusion` SET `storage_id`=1;
ALTER TABLE `#__sdi_order_diffusion` ADD CONSTRAINT `#__sdi_order_diffusion_fk4` FOREIGN KEY (`storage_id`) REFERENCES `#__sdi_sys_extractstorage` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
