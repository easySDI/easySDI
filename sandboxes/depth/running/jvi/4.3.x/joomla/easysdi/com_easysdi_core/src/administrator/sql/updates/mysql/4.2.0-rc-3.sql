DELETE vl FROM #__sdi_versionlink vl CROSS JOIN (SELECT vl2.id FROM #__sdi_versionlink vl1 INNER JOIN #__sdi_versionlink vl2 ON vl1.parent_id=vl2.parent_id AND vl1.child_id=vl2.child_id WHERE vl1.id<vl2.id) vl3 USING (id);
ALTER TABLE #__sdi_versionlink ADD CONSTRAINT #__sdi_versionlink_uk UNIQUE (parent_id, child_id);


ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN `guid` VARCHAR(36) NOT NULL AFTER id;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN `created_by` INT(11) NOT NULL AFTER guid;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER created_by;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN `modified_by` INT(11) AFTER created;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN `modified` DATETIME AFTER modified_by;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN `checked_out` INT(11) NOT NULL DEFAULT '0' AFTER `state`;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER checked_out;