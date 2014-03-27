CREATE TABLE IF NOT EXISTS `#__sdi_sys_perimetertype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


INSERT INTO `#__sdi_sys_perimetertype` (ordering,state,value) 
VALUES 
(1,1,'extraction'),
(2,1,'download'),
(3,1,'both')
;