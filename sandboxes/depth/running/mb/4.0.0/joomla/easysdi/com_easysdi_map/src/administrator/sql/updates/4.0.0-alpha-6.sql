CREATE TABLE IF NOT EXISTS `#__sdi_visualization` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR(36)  NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`created` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  ,
`modified` DATETIME  ,
`ordering` INT(11)   ,
`state` INT(11)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL DEFAULT '0' ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`name` VARCHAR(255)  NOT NULL ,
`version_id` INT(11) UNSIGNED NOT NULL ,
`accessscope_id` INT(11) UNSIGNED NOT NULL ,
`wmsservice_id` INT(11) UNSIGNED  NOT NULL ,
`wmsservicetype_id` INT(11) UNSIGNED  ,
`layername` VARCHAR(255)  NOT NULL ,
`map_id` INT(11) UNSIGNED   ,
`access` INT(11)  NOT NULL DEFAULT '1',
`asset_id` INT(10) ,
PRIMARY KEY (`id`), 
INDEX `#__sdi_visualization_fk1` (`accessscope_id` ASC) ,
CONSTRAINT `#__sdi_visualization_fk1`
    FOREIGN KEY (`accessscope_id`)
    REFERENCES `#__sdi_sys_accessscope` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
UNIQUE (`alias`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__sdi_sys_maptool` (alias,ordering,state,name) 
VALUES ('layertree',13,1,'Layer tree');

ALTER TABLE `#__sdi_map_tool` ADD `params` VARCHAR (500);

INSERT INTO `#__sdi_sys_maptool` (alias,ordering,state,name) 
VALUES ('scaleline',14,1,'Scale line');

INSERT INTO `#__sdi_sys_maptool` (alias,ordering,state,name) 
VALUES ('mouseposition',15,1,'Mouse position');

