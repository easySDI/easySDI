ALTER TABLE [slnvp_sdi_translation] DROP FOREIGN KEY [slnvp_sdi_translation_ibfk_1];
ALTER TABLE [slnvp_sdi_translation] ADD CONSTRAINT [slnvp_sdi_translation_ibfk_1] FOREIGN KEY (`language_id`) REFERENCES `slnvp_sdi_language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE [slnvp_sdi_translation]  WITH CHECK ADD CONSTRAINT [slnvp_sdi_translation_ibfk_1] FOREIGN KEY([language_id])
REFERENCES [slnvp_sdi_language] ([id])
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE [slnvp_sdi_translation] CHECK CONSTRAINT [slnvp_sdi_translation_ibfk_1];