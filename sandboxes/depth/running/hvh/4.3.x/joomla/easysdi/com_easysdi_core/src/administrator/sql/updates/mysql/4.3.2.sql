INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('9', '9', '1', 'upload');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('10', '10', '1', 'url');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('11', '11', '1', 'upload and url');

INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('22', '14', '9');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('23', '14', '10');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('24', '14', '11');

DELETE FROM `#__sdi_sys_rendertype_stereotype` WHERE id=20;

ALTER TABLE `#__sdi_visualization` MODIFY `alias` VARCHAR(50) NOT NULL;

INSERT IGNORE INTO `#__sdi_sys_role` SET id=11, ordering=11, `state`=1, `value`='organismmanager';
