ALTER TABLE `#__sdi_policy` ADD `csw_anyresourcetype` TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__sdi_policy` ADD `csw_accessscope_id` TINYINT(1) NOT NULL DEFAULT 1;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_resourcetype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`resourcetype_id` INT(11) UNSIGNED  NOT NULL ,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

ALTER TABLE `#__sdi_policy_resourcetype`
ADD CONSTRAINT `#__sdi_policy_resourcetype_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_resourcetype`
ADD CONSTRAINT `#__sdi_policy_resourcetype_fk2` FOREIGN KEY (`resourcetype_id`) REFERENCES `#__sdi_resourcetype` (`id`)  ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS `#__sdi_policy_visibility` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`policy_id` INT(11) UNSIGNED  NOT NULL ,
`user_id` INT(11) UNSIGNED   NULL ,
`organism_id` INT(11) UNSIGNED   NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

ALTER TABLE `#__sdi_policy_visibility`
ADD CONSTRAINT `#__sdi_policy_visibility_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_visibility`
ADD CONSTRAINT `#__sdi_policy_visibility_fk2` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`)  ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_visibility`
ADD CONSTRAINT `#__sdi_policy_visibility_fk3` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`)  ON DELETE CASCADE;
