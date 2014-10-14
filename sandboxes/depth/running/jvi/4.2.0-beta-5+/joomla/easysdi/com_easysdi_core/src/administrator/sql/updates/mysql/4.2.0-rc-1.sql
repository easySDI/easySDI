ALTER TABLE `#__sdi_translation` ADD UNIQUE `unique_lang_element`(element_guid, language_id);

ALTER TABLE `#__sdi_translation` ADD COLUMN `text3`  varchar(255) NULL AFTER `text2`;

ALTER TABLE `#__sdi_resourcetypelink` DROP FOREIGN KEY `#__sdi_resourcetypelink_fk3`;
ALTER TABLE `#__sdi_resourcetypelink` DROP COLUMN `class_id`;

ALTER TABLE `#__sdi_resourcetypelink` DROP FOREIGN KEY `#__sdi_resourcetypelink_fk4`;
ALTER TABLE `#__sdi_resourcetypelink` DROP COLUMN `attribute_id`;
