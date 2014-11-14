ALTER TABLE [#__sdi_translation] DROP FOREIGN KEY [#__sdi_translation_ibfk_1];
ALTER TABLE [#__sdi_translation] ADD CONSTRAINT [#__sdi_translation_ibfk_1] FOREIGN KEY (`language_id`) REFERENCES `#__sdi_language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE [#__sdi_translation]  WITH CHECK ADD CONSTRAINT [#__sdi_translation_ibfk_1] FOREIGN KEY([language_id])
REFERENCES [#__sdi_language] ([id])
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE [#__sdi_translation] CHECK CONSTRAINT [#__sdi_translation_ibfk_1];