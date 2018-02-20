ALTER TABLE [#__sdi_diffusion_download]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_diffusion_download$#__sdi_diffusion_download_fk2] FOREIGN KEY([user_id])
REFERENCES [#__sdi_user] ([id])
ON DELETE SET NULL;

ALTER TABLE [#__sdi_order] ADD [archived] [int] NOT NULL DEFAULT 0;
UPDATE [#__sdi_order] SET [archived]=1 WHERE [orderstate_id]=1;
UPDATE [#__sdi_order] SET [orderstate_id]=3 WHERE [orderstate_id]=1;
DELETE FROM [#__sdi_sys_orderstate] WHERE [id]=1;

ALTER TABLE  [#__sdi_order_diffusion] DROP COLUMN [fee];

ALTER TABLE  [#__sdi_order] DROP COLUMN [buffer];

ALTER TABLE  [#__sdi_diffusion_perimeter] DROP COLUMN [buffer];