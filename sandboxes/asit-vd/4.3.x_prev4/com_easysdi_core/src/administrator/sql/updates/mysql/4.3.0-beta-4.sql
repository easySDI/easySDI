CALL drop_column('sdi_maplayer', 'levelfield');
ALTER TABLE `#__sdi_maplayer` ADD COLUMN `levelfield`  varchar(255) NULL AFTER `asOLoptions`;
CALL drop_column('sdi_maplayer', 'isindoor');
ALTER TABLE `#__sdi_maplayer` ADD COLUMN `isindoor`  TINYINT(1) NULL AFTER `asOLoptions`;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_server` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT IGNORE INTO `#__sdi_sys_server` VALUES ('1','1','1','geoserver' );
INSERT IGNORE INTO `#__sdi_sys_server` VALUES ('2','1','1','arcgisserver' );

CREATE TABLE IF NOT EXISTS `#__sdi_sys_server_serviceconnector` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`server_id` INT(11) UNSIGNED  NOT NULL ,
`serviceconnector_id` int(11) UNSIGNED NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CALL drop_foreign_key('sdi_sys_server_serviceconnector', 'sdi_sys_server_serviceconnector_fk1');
ALTER TABLE `#__sdi_sys_server_serviceconnector`
ADD CONSTRAINT `#__sdi_sys_server_serviceconnector_fk1` FOREIGN KEY (`server_id`) REFERENCES `#__sdi_sys_server` (`id`);

CALL drop_foreign_key('sdi_sys_server_serviceconnector', 'sdi_sys_server_serviceconnector_fk2');
ALTER TABLE `#__sdi_sys_server_serviceconnector`
ADD CONSTRAINT `#__sdi_sys_server_serviceconnector_fk2` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`);

INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('1', '1', '2');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('2', '1', '3');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('3', '1', '4');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('4', '1', '5');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('5', '1', '11');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('6', '2', '2');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('7', '2', '4');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('8', '2', '5');

CALL drop_foreign_key('sdi_physicalservice', 'sdi_physicalservice_server_fk1');
CALL drop_column('sdi_physicalservice', 'server_id');
ALTER TABLE `#__sdi_physicalservice` ADD COLUMN `server_id` INT(11) UNSIGNED NULL;
ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_server_fk1` FOREIGN KEY (`server_id`) REFERENCES `#__sdi_sys_server` (`id`);

ALTER TABLE `#__sdi_map_tool` MODIFY `params` VARCHAR(4000);
