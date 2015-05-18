INSERT INTO `#__sdi_sys_rendertype` VALUES ('9', '9', '1', 'upload');
INSERT INTO `#__sdi_sys_rendertype` VALUES ('10', '10', '1', 'url');
INSERT INTO `#__sdi_sys_rendertype` VALUES ('11', '11', '1', 'upload and url');

INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('22', '14', '9');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('23', '14', '10');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('24', '14', '11');

DELETE FROM `#__sdi_sys_rendertype_stereotype` WHERE id=20;