ALTER TABLE [#__sdi_diffusion] ADD [otp] [tinyint] NOT NULL DEFAULT 0;

ALTER TABLE [#__sdi_order_diffusion] ADD [otp] [nvarchar](50) NULL;
ALTER TABLE [#__sdi_order_diffusion] ADD [otpchance] [INT] NULL DEFAULT 0 ;

SET IDENTITY_INSERT [#__sdi_sys_productstate] ON;
INSERT [#__sdi_sys_productstate] ([id], [ordering], [state], [value]) VALUES (8, 8, 1,N'blocked');
SET IDENTITY_INSERT [#__sdi_sys_productstate] OFF;

ALTER TABLE [#__sdi_catalog_searchcriteria] ALTER COLUMN [defaultvalue] [nvarchar](500);

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] DROP  CONSTRAINT [sdi_sdi_pricing_order_supplier_product_profilesdi_sdi_pricing_order_supplier_product_profile_fk2];
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [pricing_profile_id] [BIGINT] NULL; 

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ADD CONSTRAINT #__sdi_pricing_order_supplier_product_profile$#__sdi_pricing_profilefk5
FOREIGN KEY ([pricing_profile_id]) REFERENCES [#__sdi_pricing_profile] ([id]) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE [#__sdi_map] ADD [default_backgroud_layer] [INT] NOT NULL DEFAULT 0;
