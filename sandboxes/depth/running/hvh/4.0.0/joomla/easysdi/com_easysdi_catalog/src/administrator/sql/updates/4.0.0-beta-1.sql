ALTER TABLE `#__sdi_translation` ADD INDEX `element_guid` (`element_guid`);

ALTER TABLE `#__sdi_relation_defaultvalue` MODIFY `language_id` INT(11) UNSIGNED NULL;

ALTER TABLE `#__sdi_versionlink` DROP FOREIGN KEY `#__sdi_versionlink_fk1`;

ALTER TABLE `#__sdi_versionlink` DROP FOREIGN KEY `#__sdi_versionlink_fk2`;

ALTER TABLE `#__sdi_versionlink`
   ADD CONSTRAINT `#__sdi_versionlink_fk1`
   FOREIGN KEY (`parent_id` )
   REFERENCES `#__sdi_version` (`id` )
   ON DELETE CASCADE
   ON UPDATE NO ACTION;

ALTER TABLE `#__sdi_versionlink`
   ADD CONSTRAINT `#__sdi_versionlink_fk2`
   FOREIGN KEY (`child_id` )
   REFERENCES `#__sdi_version` (`id` )
   ON DELETE CASCADE
   ON UPDATE NO ACTION;