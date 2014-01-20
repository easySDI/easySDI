CREATE TABLE IF NOT EXISTS `#__sdi_relation_defaultvalue` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`relation_id`  INT(11) UNSIGNED NOT NULL  ,
`attributevalue_id`  INT(11) UNSIGNED ,
`value` VARCHAR (500),
`language_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`) ,
  INDEX `#__sdi_relation_defaultvalue_fk1` (`relation_id` ASC) ,
  INDEX `#__sdi_relation_defaultvalue_fk2` (`attributevalue_id` ASC) ,
INDEX `#__sdi_relation_defaultvalue_fk3` (`language_id` ASC) ,
  CONSTRAINT `#__sdi_relation_defaultvalue_fk1`
    FOREIGN KEY (`relation_id` )
    REFERENCES `#__sdi_relation` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_relation_defaultvalue_fk2`
    FOREIGN KEY (`attributevalue_id` )
    REFERENCES `#__sdi_attributevalue` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
CONSTRAINT `#__sdi_relation_defaultvalue_fk3`
    FOREIGN KEY (`language_id` )
    REFERENCES `#__sdi_language` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `#__sdi_relation_attributevalue`;