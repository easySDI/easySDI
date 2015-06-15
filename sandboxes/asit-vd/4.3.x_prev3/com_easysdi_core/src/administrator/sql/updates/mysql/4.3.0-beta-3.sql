ALTER TABLE #__sdi_order ADD COLUMN mandate_ref VARCHAR(75) NULL AFTER remark;
ALTER TABLE #__sdi_order ADD COLUMN mandate_contact VARCHAR(75) NULL AFTER mandate_ref;
ALTER TABLE #__sdi_order ADD COLUMN mandate_email VARCHAR(100) NULL AFTER mandate_contact;

ALTER TABLE #__sdi_catalog ADD COLUMN contextualsearchresultpaginationnumber int(3) DEFAULT 0 AFTER description;

ALTER TABLE #__sdi_relation ADD COLUMN accessscope_limitation int(1) DEFAULT 0 AFTER childtype_id;