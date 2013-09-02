ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `modified` DATETIME NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `description` VARCHAR(500)  NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `wfsservice_id` INT(11) UNSIGNED NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `wfsservicetype_id` INT(11) UNSIGNED NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `featuretypename` VARCHAR(255)  NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `prefix` VARCHAR(255)  NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `namespace` VARCHAR(255)  NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `featuretypefieldid` VARCHAR(255)  NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `featuretypefieldname` VARCHAR(255)  NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `featuretypefieldsurface` VARCHAR(255)  NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `featuretypefielddescription` VARCHAR(255)  NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `featuretypefieldgeometry` VARCHAR(255)  NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `featuretypefieldresource` VARCHAR(255)  NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `wmsservice_id` INT(11) UNSIGNED NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `wmsservicetype_id` INT(11) UNSIGNED NULL;
ALTER TABLE `#__sdi_perimeter` MODIFY COLUMN `layername` VARCHAR(255)  NULL;


INSERT INTO `#__sdi_perimeter` (id,guid,alias,created_by,created, ordering, state, name, description, accessscope_id, perimetertype_id ) 
VALUES 
('1', '1a9f342c-bb1e-9bc4-dd19-38910dff0f59', 'freeperimeter', '356', '2013-07-23 09:16:11','1', '1', 'Free perimeter', '',1,1),
('2', '9adc6d4e-262a-d6e4-e152-6de437ba80ed', 'myperimeter', '356', '2013-07-23 09:16:11','2', '1', 'My perimeter', '',1,1)
;
