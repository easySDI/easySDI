ALTER TABLE `#__sdi_map` ADD `zoom` VARCHAR(10) NULL;
ALTER TABLE `#__sdi_map` ADD `centercoordinates` VARCHAR(255) NULL;
ALTER TABLE `#__sdi_map` DROP FOREIGN KEY `#__sdi_map_fk3`;
ALTER TABLE `#__sdi_map` DROP `defaultserviceconnector_id` ;
