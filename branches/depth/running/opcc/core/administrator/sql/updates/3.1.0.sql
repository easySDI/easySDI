
CREATE TABLE IF NOT EXISTS `#__sdi_sys_unit` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`),
INDEX `alias` USING BTREE (`alias`) 
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__sdi_sys_unit` (ordering,state,checked_out,created_by,created,alias,name) 
VALUES 
(1,1,0,62,NOW(),'m','meter'),
(2,1,0,62,NOW(),'d','degree')
;

