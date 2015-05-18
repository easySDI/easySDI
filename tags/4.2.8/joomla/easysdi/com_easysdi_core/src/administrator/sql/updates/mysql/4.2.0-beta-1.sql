

CREATE TABLE IF NOT EXISTS `#__sdi_category` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)   ,
`modified` DATETIME  ,
`ordering` INT(11)  NOT NULL ,
`state` int(11) NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL DEFAULT '0'  ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`alias` VARCHAR(150)   ,
`description` VARCHAR(500)  ,
`name` VARCHAR(255)  NOT NULL ,
`access` INT(11)  NOT NULL ,
`asset_id` INT(10)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `#__sdi_organism_category` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`organism_id` INT(11) UNSIGNED ,
`category_id` INT(11) UNSIGNED ,
PRIMARY KEY (`id`),
INDEX `#__sdi_organism_category_fk1` (`organism_id` ASC) ,
INDEX `#__sdi_organism_category_fk2` (`category_id` ASC) ,
  CONSTRAINT `#__sdi_organism_category_fk1`
    FOREIGN KEY (`organism_id`)
    REFERENCES `#__sdi_organism` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `#__sdi_organism_category_fk2`
    FOREIGN KEY (`category_id`)
    REFERENCES `#__sdi_category` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;



INSERT IGNORE INTO `#__sdi_sys_accessscope` SET id=4, ordering=4, state=1, `value`='category';

CREATE TABLE IF NOT EXISTS `#__sdi_policy_category` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`category_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

DELETE FROM `#__sdi_user_role_organism` WHERE role_id=(SELECT id FROM `#__sdi_sys_role` WHERE `value`='ordereligible');
DELETE FROM `#__sdi_sys_role` WHERE `value`='ordereligible';


ALTER TABLE `#__sdi_order` DROP FOREIGN KEY `#__sdi_order_fk4`;
ALTER TABLE `#__sdi_order` ADD CONSTRAINT `#__sdi_order_fk4` FOREIGN KEY (`thirdparty_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;



ALTER TABLE `#__sdi_user_role_organism` ADD CONSTRAINT `#__sdi_user_role_organism_fk1` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `#__sdi_user_role_organism` ADD CONSTRAINT `#__sdi_user_role_organism_fk2` FOREIGN KEY (`role_id`) REFERENCES `#__sdi_sys_role` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `#__sdi_user_role_organism` ADD CONSTRAINT `#__sdi_user_role_organism_fk3` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;