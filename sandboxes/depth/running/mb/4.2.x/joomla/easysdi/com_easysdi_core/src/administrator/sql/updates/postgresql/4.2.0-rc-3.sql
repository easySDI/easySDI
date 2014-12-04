DELETE FROM #__sdi_versionlink WHERE id IN (SELECT id FROM (SELECT id, row_number() over (partition BY parent_id, child_id ORDER BY id) as rnum FROM #__sdi_versionlink) vl WHERE vl.rnum>1);
ALTER TABLE ONLY #__sdi_versionlink ADD CONSTRAINT #__sdi_versionlink_uk UNIQUE (parent_id, child_id);
