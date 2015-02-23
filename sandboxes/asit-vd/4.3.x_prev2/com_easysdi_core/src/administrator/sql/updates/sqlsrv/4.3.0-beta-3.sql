ALTER TABLE [#__sdi_order] ADD [mandate_ref] [nvarchar](75) NULL;
ALTER TABLE [#__sdi_order] ADD [mandate_contact] [nvarchar](75) NULL;
ALTER TABLE [#__sdi_order] ADD [mandate_email] [nvarchar](100) NULL;

ALTER TABLE [#__sdi_catalog] ADD [contextualsearchresultpaginationnumber] [tinyint] DEFAULT 0;

ALTER TABLE [#__sdi_relation] ADD [accessscope_limitation] [tinyint] DEFAULT 0;
