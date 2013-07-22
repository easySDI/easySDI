ALTER TABLE `#__sdi_class` CHANGE `issystem` `predefined` TINYINT(1)  NOT NULL DEFAULT '0'

ALTER TABLE `#__sdi_class` DROP `accessscope_id` 

ALTER TABLE `#__sdi_attributevalue` DROP `accessscope_id` 