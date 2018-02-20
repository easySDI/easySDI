
ALTER TABLE [#__sdi_accessscope] ADD [category_id] [bigint] NULL;

ALTER TABLE [#__sdi_accessscope]  WITH CHECK ADD  CONSTRAINT [#__sdi_accessscope$#__sdi_accessscope_fk3] FOREIGN KEY([category_id])
REFERENCES [#__sdi_category] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_accessscope] CHECK CONSTRAINT [#__sdi_accessscope$#__sdi_accessscope_fk3];