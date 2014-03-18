ALTER TABLE `#__sdi_resource` ADD `accessscope_id` INT(11) UNSIGNED  NOT NULL DEFAULT 1;

ALTER TABLE `#__sdi_resource`
ADD CONSTRAINT `#__sdi_resource_fk3` FOREIGN KEY (`accessscope_id`) REFERENCES `#__sdi_sys_accessscope` (`id`);
