CREATE TABLE IF NOT EXISTS `#__sdi_physicalservice_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`service_id` INT(11) UNSIGNED  NOT NULL ,
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
`capabilities` TEXT,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_virtualservice_servicecompliance` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`service_id` INT(11) UNSIGNED  NOT NULL ,
`servicecompliance_id` INT(11) UNSIGNED  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__sdi_physicalservice_servicecompliance` (service_id,servicecompliance_id) SELECT service_id,servicecompliance_id FROM `#__sdi_service_servicecompliance` WHERE `servicetype`='physical';
INSERT INTO `#__sdi_virtualservice_servicecompliance` (service_id,servicecompliance_id) SELECT service_id,servicecompliance_id FROM `#__sdi_service_servicecompliance` WHERE `servicetype`='virtual';

DROP TABLE `#__sdi_service_servicecompliance`;


ALTER TABLE `#__sdi_physicalservice_servicecompliance`
ADD CONSTRAINT `#__sdi_physicalservice_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_physicalservice_servicecompliance`
ADD CONSTRAINT `#__sdi_physicalservice_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_virtualservice_servicecompliance`
ADD CONSTRAINT `#__sdi_virtualservice_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_virtualservice_servicecompliance`
ADD CONSTRAINT `#__sdi_virtualservice_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;