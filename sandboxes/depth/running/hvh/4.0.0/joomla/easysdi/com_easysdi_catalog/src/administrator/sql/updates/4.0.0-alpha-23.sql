ALTER TABLE `#__sdi_translation` ADD INDEX `element_guid` (`element_guid`);

ALTER TABLE `#__sdi_relation_defaultvalue` MODIFY `language_id` INT(11) UNSIGNED NULL;