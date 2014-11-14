ALTER TABLE [#__sdi_translation] DROP CONSTRAINT [#__sdi_translation$#__sdi_translation_fk1];

ALTER TABLE [#__sdi_translation]  WITH CHECK ADD CONSTRAINT [#__sdi_translation$#__sdi_translation_fk1] FOREIGN KEY([language_id])
REFERENCES [#__sdi_language] ([id])
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE [#__sdi_translation] CHECK CONSTRAINT [#__sdi_translation$#__sdi_translation_fk1];