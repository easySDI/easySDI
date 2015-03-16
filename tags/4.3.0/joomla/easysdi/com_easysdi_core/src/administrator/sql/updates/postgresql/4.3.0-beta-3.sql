ALTER TABLE #__sdi_order ADD COLUMN mandate_ref VARCHAR(75) NULL;
ALTER TABLE #__sdi_order ADD COLUMN mandate_contact VARCHAR(75) NULL;
ALTER TABLE #__sdi_order ADD COLUMN mandate_email VARCHAR(100) NULL;

ALTER TABLE #__sdi_catalog ADD COLUMN contextualsearchresultpaginationnumber integer DEFAULT 0 AFTER description;

ALTER TABLE #__sdi_relation ADD COLUMN accessscope_limitation integer DEFAULT 0 AFTER childtype_id;
