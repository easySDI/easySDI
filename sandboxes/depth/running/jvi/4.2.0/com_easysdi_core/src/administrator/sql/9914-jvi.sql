

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

-- access scope
INSERT INTO `#__sdi_sys_accessscope` SET ordering=4, state=1, `value`='category';

CREATE TABLE IF NOT EXISTS `#__sdi_policy_category` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`category_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;
