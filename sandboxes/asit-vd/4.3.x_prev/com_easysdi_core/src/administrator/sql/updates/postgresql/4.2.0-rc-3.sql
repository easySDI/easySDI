DELETE FROM #__sdi_versionlink WHERE id IN (SELECT id FROM (SELECT id, row_number() over (partition BY parent_id, child_id ORDER BY id) as rnum FROM #__sdi_versionlink) vl WHERE vl.rnum>1);
ALTER TABLE ONLY #__sdi_versionlink ADD CONSTRAINT #__sdi_versionlink_uk UNIQUE (parent_id, child_id);


ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN guid character varying(36) NOT NULL AFTER id;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN created_by integer NOT NULL AFTER guid;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL AFTER created_by;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN modified_by integer AFTER created;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN modified timestamp(3) without time zone AFTER modified_by;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN checked_out integer DEFAULT 0 NOT NULL AFTER state;
ALTER TABLE #__sdi_catalog_resourcetype ADD COLUMN checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL AFTER checked_out;