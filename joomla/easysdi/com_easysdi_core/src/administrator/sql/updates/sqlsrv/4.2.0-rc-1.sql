ALTER TABLE [#__sdi_translation] ADD [text3] [nvarchar](255) NULL;

ALTER TABLE [#__sdi_resourcetypelink] DROP CONSTRAINT [#__sdi_resourcetypelink_fk3];
ALTER TABLE [#__sdi_resourcetypelink] DROP COLUMN [class_id];

ALTER TABLE [#__sdi_resourcetypelink] DROP CONSTRAINT [#__sdi_resourcetypelink_fk4];
ALTER TABLE [#__sdi_resourcetypelink] DROP COLUMN [attribute_id];

ALTER TABLE [#__sdi_assignment] DROP CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk3];
SP_RENAME [#__sdi_assignment].[version_id], [metadata_id], 'COLUMN';
TRUNCATE TABLE [#__sdi_assignment];
ALTER TABLE [#__sdi_assignment]  WITH CHECK ADD  CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk3] FOREIGN KEY([metadata_id])
REFERENCES [#__sdi_metadata] ([id])
ON DELETE CASCADE;
ALTER TABLE [#__sdi_assignment] CHECK CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk3];

INSERT INTO [#__sdi_sys_rendertype] VALUES 
(7, 7, 1, 'datetime'),
(8, 8, 1, 'gemet');

INSERT INTO [#__sdi_sys_rendertype_stereotype] VALUES ('21', '11', '8');