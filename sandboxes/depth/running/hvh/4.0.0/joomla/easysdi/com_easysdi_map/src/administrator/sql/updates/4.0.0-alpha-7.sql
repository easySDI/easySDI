INSERT INTO `#__sdi_sys_maptool` (alias,ordering,state,name) 
VALUES ('mouseposition',15,1,'Mouse position');

ALTER TABLE `#__sdi_maplayer` ADD `attribution` VARCHAR (255);

ALTER TABLE `#__sdi_map` DROP `centercoordinates` ;

ALTER TABLE `#__sdi_map` ADD `restrictedextent` VARCHAR (255);

ALTER TABLE `#__sdi_visualization` ADD `attribution` VARCHAR (255);

ALTER TABLE `#__sdi_visualization` DROP `map_id` ;