# Changing the constraint # __sdi_catalog_searchsort_fk1 to allow the removal of a catalog.
ALTER TABLE [#__sdi_catalog_searchsort] DROP CONSTRAINT [#__sdi_catalog_searchsort_fk1];

ALTER TABLE [#__sdi_catalog_searchsort]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_catalog_searchsort$#__sdi_visualization_fk1] FOREIGN KEY([catalog_id])
REFERENCES [#__sdi_catalog] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_catalog_searchsort] CHECK CONSTRAINT [#__sdi_catalog_searchsort$#__sdi_visualization_fk1];

# Add version constraint on visualisation after delete orphans
DELETE FROM [#__sdi_visualization] WHERE [version_id] NOT IN (SELECT [id] FROM [#__sdi_version]);

ALTER TABLE [#__sdi_visualization]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_visualization$#__sdi_visualization_fk2] FOREIGN KEY([version_id])
REFERENCES [#__sdi_version] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_visualization] CHECK CONSTRAINT [#__sdi_visualization$#__sdi_visualization_fk2];

# Correct definitions are inverted
UPDATE [#__sdi_sys_isolanguage] SET [value] = 'iso639-1' WHERE [id] = 1;
UPDATE [#__sdi_sys_isolanguage] SET [value] = 'iso639-2T' WHERE [id] = 3;
