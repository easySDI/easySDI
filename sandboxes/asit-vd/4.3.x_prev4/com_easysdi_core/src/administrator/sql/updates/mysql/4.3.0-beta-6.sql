CALL drop_column('sdi_map_tool', 'activated');
ALTER TABLE `#__sdi_map_tool` ADD COLUMN `activated`  TINYINT(1) DEFAULT 0 ;

UPDATE `#__sdi_map_tool` SET `activated`=1;