WITH CTE AS (SELECT [id], RN = ROW_NUMBER() OVER (PARTITION BY [parent_id], [child_id] ORDER BY [id]) FROM [#__sdi_versionlink])
DELETE FROM CTE WHERE RN>1;

ALTER TABLE [#__sdi_versionlink] ADD CONSTRAINT [#__sdi_versionlink$#__sdi_versionlink_uk] UNIQUE ([parent_id], [child_id]);

ALTER TABLE [#__sdi_catalog_resourcetype] ADD  [guid] [nvarchar](36) NULL;
ALTER TABLE [#__sdi_catalog_resourcetype] ADD  [created_by] [int] NULL;
ALTER TABLE [#__sdi_catalog_resourcetype] ADD  [created] [datetime2](0) NULL;
ALTER TABLE [#__sdi_catalog_resourcetype] ADD  [modified_by] [int] NULL;
ALTER TABLE [#__sdi_catalog_resourcetype] ADD  [modified] [datetime2](0) NULL;
ALTER TABLE [#__sdi_catalog_resourcetype] ADD  [checked_out] [int] NULL;
ALTER TABLE [#__sdi_catalog_resourcetype] ADD  [checked_out_time] [datetime2](0) NULL;