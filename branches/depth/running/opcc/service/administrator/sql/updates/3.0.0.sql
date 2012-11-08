CREATE TABLE IF NOT EXISTS `#__sdi_virtualservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` INT(36)  NOT NULL ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  ,
`checked_out_time` DATETIME ,
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)   ,
`modified` DATETIME  ,
`name` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`serviceconnector_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_layer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` INT(36)  NOT NULL ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  ,
`checked_out_time` DATETIME ,
`created_by` INT(11)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  ,
`modified_by` INT(11)  ,
`modified` DATETIME ,
`physicalservice_id` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__sdi_sys_serviceconnector` (ordering,state,checked_out,value) 
VALUES 
(11,1,0,'WMSCSource'),
(12,1,0,'BingSource'),
(13,1,0,'GoogleSource'),
(14,1,0,'OSMSource')
;
