ALTER TABLE #__sdi_metadata ADD COLUMN endpublished timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL AFTER published;
