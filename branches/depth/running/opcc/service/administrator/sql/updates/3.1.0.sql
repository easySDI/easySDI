CREATE TABLE IF NOT EXISTS `#__sdi_virtualservice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` INT(36)  NOT NULL ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL  ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified_by` INT(11)   ,
`modified` DATETIME  ,
`name` VARCHAR(255)  NOT NULL ,
`alias` VARCHAR(255)  NOT NULL ,
`url` VARCHAR(500)   ,
`serviceconnector_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_layer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` INT(36)  NOT NULL ,
`ordering` INT(11)  ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11) NOT NULL  ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
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
(11,1,0,'WMSC'),
(12,0,0,'Bing'),
(13,0,0,'Google'),
(14,0,0,'OSM')
;

INSERT INTO `#__sdi_sys_servicecompliance` (ordering,state,checked_out,serviceconnector_id,serviceversion_id,implemented,relayable,aggregatable,harvestable) 
VALUES 
(8,1,0,11,2,1,1,1,0),
(9,1,0,11,3,1,1,1,0),
(10,1,0,11,4,1,1,1,0)
;