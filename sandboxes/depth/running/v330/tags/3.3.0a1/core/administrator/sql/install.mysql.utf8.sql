CREATE TABLE IF NOT EXISTS `#__sdi_sys_unit` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` int(11)  NOT NULL DEFAULT '1',
`alias` VARCHAR(20)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`),
INDEX `alias` USING BTREE (`alias`) 
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_role` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE `#__sdi_sys_country` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ordering` bigint(20) NOT NULL DEFAULT '1',
  `state` int(11) NOT NULL DEFAULT '1',
  `name` varchar(100) NOT NULL,
  `iso2` varchar(2) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_versiontype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_accessscope` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_metadatastate` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_metadataversion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_spatialoperator` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11) ,
`state` int(11) NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceversion` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`serviceconnector_id` INT(11) UNSIGNED  NOT NULL ,
`serviceversion_id` INT(11) UNSIGNED  NOT NULL ,
`implemented` TINYINT(1)  NOT NULL DEFAULT '0',
`relayable` TINYINT(1)  NOT NULL DEFAULT '0',
`aggregatable` TINYINT(1)  NOT NULL DEFAULT '0',
`harvestable` TINYINT(1)  NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_serviceoperation` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_operationcompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
`serviceoperation_id` INT(11) UNSIGNED  NOT NULL ,
`implemented` TINYINT(1)  NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationlevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_authenticationconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  ,
`state` int(11)  NOT NULL DEFAULT '1',
`authenticationlevel_id` INT(11) UNSIGNED  NOT NULL ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_logroll` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_loglevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_exceptionlevel` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_proxytype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicecon_authenticationcon` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`serviceconnector_id` INT(11) UNSIGNED NOT NULL ,
`authenticationconnector_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_servicescope` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` INT(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

ALTER TABLE `#__sdi_sys_servicecompliance`
ADD CONSTRAINT `#__sdi_sys_servicecompliance_fk1` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_sys_servicecompliance`
ADD CONSTRAINT `#__sdi_sys_servicecompliance_fk2` FOREIGN KEY (`serviceversion_id`) REFERENCES `#__sdi_sys_serviceversion` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_sys_servicecon_authenticationcon`
ADD CONSTRAINT `#__sdi_sys_servicecon_authenticationcon_fk1` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_sys_servicecon_authenticationcon`
ADD CONSTRAINT `#__sdi_sys_servicecon_authenticationcon_fk2` FOREIGN KEY (`authenticationconnector_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_sys_operationcompliance`
ADD CONSTRAINT `#__sdi_sys_operationcompliance_fk1` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`);

ALTER TABLE `#__sdi_sys_operationcompliance`
ADD CONSTRAINT `#__sdi_sys_operationcompliance_fk2` FOREIGN KEY (`serviceoperation_id`) REFERENCES `#__sdi_sys_serviceoperation` (`id`);

ALTER TABLE `#__sdi_sys_authenticationconnector`
ADD CONSTRAINT `#__sdi_sys_authenticationconnector_fk1` FOREIGN KEY (`authenticationlevel_id`) REFERENCES `#__sdi_sys_authenticationlevel` (`id`);
