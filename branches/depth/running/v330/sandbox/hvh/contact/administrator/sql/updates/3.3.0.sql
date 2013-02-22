ALTER TABLE `#__sdi_user` DROP COLUMN acronym;
ALTER TABLE `#__sdi_user` DROP COLUMN logo;
ALTER TABLE `#__sdi_user` DROP COLUMN website;

CREATE TABLE IF NOT EXISTS `#__sdi_organism` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)  NULL ,
`modified` DATETIME  ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NULL ,
`checked_out_time` DATETIME NULL,
`acronym` VARCHAR(150)  NULL ,
`description` TEXT NULL ,
`logo` VARCHAR(500)  NULL ,
`name` VARCHAR(255) NOT NULL ,
`website` VARCHAR(500)  NULL ,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

ALTER TABLE `#__sdi_address` DROP COLUMN organismcomplement;
ALTER TABLE `#__sdi_address` DROP COLUMN organism;
ALTER TABLE `#__sdi_address` ADD COLUMN organism_id INT(11) UNSIGNED;
ALTER TABLE `#__sdi_address` MODIFY COLUMN user_id INT(11) UNSIGNED ;
ALTER TABLE `#__sdi_address` DROP FOREIGN KEY #__sdi_address_fk2;
ALTER TABLE `#__sdi_address` MODIFY COLUMN civility VARCHAR(100) ;

DROP TABLE `#__sdi_sys_civility` ;

ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk4` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_address` MODIFY COLUMN country INT(11) UNSIGNED ;
ALTER TABLE `#__sdi_address` CHANGE country country_id INT(11) UNSIGNED;


ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk5` FOREIGN KEY (`country_id`) REFERENCES `#__sdi_sys_country` (`id`) ;

