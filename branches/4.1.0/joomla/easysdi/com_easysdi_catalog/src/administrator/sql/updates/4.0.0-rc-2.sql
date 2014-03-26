ALTER TABLE `#__sdi_application` DROP FOREIGN KEY `#__sdi_application_fk1`;

ALTER TABLE `#__sdi_application` ADD CONSTRAINT `#__sdi_application_fk1` FOREIGN KEY (`resource_id`) REFERENCES `#__sdi_resource` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;