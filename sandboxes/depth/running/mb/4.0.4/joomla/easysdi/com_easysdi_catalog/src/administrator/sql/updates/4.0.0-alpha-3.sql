ALTER TABLE `#__sdi_importref` MODIFY `cswservice_id` INT(11)  UNSIGNED;

ALTER TABLE `#__sdi_importref` MODIFY `cswversion_id` INT(11)  UNSIGNED;

ALTER TABLE `#__sdi_importref`
ADD CONSTRAINT `#__sdi_importref_fk2` FOREIGN KEY (`cswservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_importref`
ADD CONSTRAINT `#__sdi_importref_fk3` FOREIGN KEY (`cswversion_id`) REFERENCES `#__sdi_sys_serviceversion` (`id`) ON DELETE CASCADE ;