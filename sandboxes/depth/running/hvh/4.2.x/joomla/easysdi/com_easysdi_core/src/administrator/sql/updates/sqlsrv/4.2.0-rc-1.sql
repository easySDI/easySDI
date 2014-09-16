
ALTER TABLE #__sdi_resourcetype ALTER COLUMN modified_by int NULL;

ALTER TABLE #__sdi_resourcetype ALTER COLUMN modified datetime2 NULL;

ALTER TABLE #__sdi_resourcetypelink ALTER COLUMN modified_by int NULL;

ALTER TABLE #__sdi_resourcetypelink ALTER COLUMN modified datetime2 NULL;
