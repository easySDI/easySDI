ALTER TABLE `#__sdi_class` CHANGE `issystem` `predefined` TINYINT(1)  NOT NULL DEFAULT '0';

ALTER TABLE `#__sdi_class` DROP FOREIGN KEY `#__sdi_class_fk3` ;
ALTER TABLE `#__sdi_class` DROP `accessscope_id` ;

ALTER TABLE `#__sdi_attributevalue` DROP FOREIGN KEY `#__sdi_attributevalue_fk2` ;
ALTER TABLE `#__sdi_attributevalue` DROP `accessscope_id` ;

UPDATE `#__sdi_sys_rendertype_stereotype` SET `rendertype_id` = 6 where `stereotype_id` IN (5,8);

DELETE FROM `#__sdi_sys_rendertype_stereotype` WHERE id = 15;