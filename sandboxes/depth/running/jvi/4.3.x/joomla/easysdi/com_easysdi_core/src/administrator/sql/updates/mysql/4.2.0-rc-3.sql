DELETE vl FROM #__sdi_versionlink vl CROSS JOIN (SELECT vl2.id FROM #__sdi_versionlink vl1 INNER JOIN #__sdi_versionlink vl2 ON vl1.parent_id=vl2.parent_id AND vl1.child_id=vl2.child_id WHERE vl1.id<vl2.id) vl3 USING (id);
ALTER TABLE #__sdi_versionlink ADD CONSTRAINT #__sdi_versionlink_uk UNIQUE (parent_id, child_id);
