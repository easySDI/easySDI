ALTER TABLE `#__sdi_translation` ADD UNIQUE `unique_lang_element`(element_guid, language_id);

ALTER TABLE `#__sdi_translation` ADD COLUMN `text3`  varchar(255) NULL AFTER `text2`;

ALTER TABLE `#__sdi_resourcetypelink` DROP FOREIGN KEY `#__sdi_resourcetypelink_fk3`;
ALTER TABLE `#__sdi_resourcetypelink` DROP COLUMN `class_id`;

ALTER TABLE `#__sdi_resourcetypelink` DROP FOREIGN KEY `#__sdi_resourcetypelink_fk4`;
ALTER TABLE `#__sdi_resourcetypelink` DROP COLUMN `attribute_id`;


ALTER TABLE `#__sdi_assignment` DROP FOREIGN KEY `#__sdi_assignment_fk3`;
ALTER TABLE `#__sdi_assignment` CHANGE `version_id` `metadata_id` INT(11) UNSIGNED NOT NULL;
TRUNCATE `#__sdi_assignment`;
ALTER TABLE `#__sdi_assignment` ADD CONSTRAINT `#__sdi_assignment_fk3`
    FOREIGN KEY (`metadata_id` )
    REFERENCES `#__sdi_metadata` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION;

ALTER TABLE #__sdi_resourcetype MODIFY modified_by int(11) NULL;

ALTER TABLE #__sdi_resourcetype MODIFY modified datetime NULL;

ALTER TABLE #__sdi_resourcetypelink MODIFY modified_by int(11) NULL;

ALTER TABLE #__sdi_resourcetypelink MODIFY modified datetime NULL;

ALTER TABLE #__sdi_application MODIFY modified_by int(11) NULL;

ALTER TABLE #__sdi_application MODIFY modified datetime NULL;

ALTER TABLE #__sdi_diffusion MODIFY modified datetime NULL;

ALTER TABLE #__sdi_diffusion MODIFY description VARCHAR(500) NULL;

ALTER TABLE #__sdi_order MODIFY modified datetime NULL;

ALTER TABLE #__sdi_order MODIFY remark VARCHAR(500) NULL;

ALTER TABLE #__sdi_order_diffusion MODIFY remark VARCHAR(500) NULL;
ALTER TABLE #__sdi_order_diffusion MODIFY fee DECIMAL(10) NULL;
ALTER TABLE #__sdi_order_diffusion MODIFY completed datetime NULL;
ALTER TABLE #__sdi_order_diffusion MODIFY `file` VARCHAR(500) NULL;
ALTER TABLE #__sdi_order_diffusion MODIFY `size` DECIMAL(10) NULL;

UPDATE  #__sdi_searchcriteria SET rendertype_id = 5 WHERE id = 1;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 2 WHERE id = 2;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 2 WHERE id = 3;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 5 WHERE id = 4;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 6 WHERE id = 5;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 6 WHERE id = 6;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 4 WHERE id = 7;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 4 WHERE id = 8;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 2 WHERE id = 9;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 2 WHERE id = 10;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 2 WHERE id = 11;
UPDATE  #__sdi_searchcriteria SET rendertype_id = 2 WHERE id = 12;

UPDATE `#__sdi_sys_accessscope` SET value='public' WHERE id=1;
UPDATE `#__sdi_sys_accessscope` SET value='category' WHERE id=2;
UPDATE `#__sdi_sys_accessscope` SET value='organism' WHERE id=3;
UPDATE `#__sdi_sys_accessscope` SET value='user' WHERE id=4;

UPDATE #__sdi_sys_accessscope SET ordering=1 WHERE value='public';
UPDATE #__sdi_sys_accessscope SET ordering=2 WHERE value='category';
UPDATE #__sdi_sys_accessscope SET ordering=3 WHERE value='organism';
UPDATE #__sdi_sys_accessscope SET ordering=4 WHERE value='user';

INSERT INTO `#__sdi_sys_rendertype` VALUES 
(7, 7, 1, 'datetime'),
(8, 8, 1, 'gemet');

INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('21', '11', '8');