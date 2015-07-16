SET IDENTITY_INSERT [#__sdi_sys_rendertype] ON;
INSERT [#__sdi_sys_rendertype] ([id], [ordering], [state], [value]) VALUES (9, 9, 1, N'upload');
INSERT [#__sdi_sys_rendertype] ([id], [ordering], [state], [value]) VALUES (10, 10, 1, N'url');
INSERT [#__sdi_sys_rendertype] ([id], [ordering], [state], [value]) VALUES (11, 11, 1, N'upload and url');
SET IDENTITY_INSERT [#__sdi_sys_rendertype] OFF;

SET IDENTITY_INSERT [#__sdi_sys_rendertype_stereotype] ON;
INSERT [#__sdi_sys_rendertype_stereotype] ([id], [stereotype_id], [rendertype_id]) VALUES (22, 14, 9);
INSERT [#__sdi_sys_rendertype_stereotype] ([id], [stereotype_id], [rendertype_id]) VALUES (23, 14, 10);
INSERT [#__sdi_sys_rendertype_stereotype] ([id], [stereotype_id], [rendertype_id]) VALUES (24, 14, 11);
SET IDENTITY_INSERT [#__sdi_sys_rendertype_stereotype] OFF;

DELETE FROM [#__sdi_sys_rendertype_stereotype] WHERE id=20;

ALTER TABLE [#__sdi_visualization] ALTER COLUMN [alias] [nvarchar](255) NOT NULL;

SET IDENTITY_INSERT [#__sdi_sys_role] ON;
INSERT [#__sdi_sys_role] ([id], [ordering], [state], [value]) VALUES (11, 11, 1, N'organismmanager');
SET IDENTITY_INSERT [#__sdi_sys_role] OFF;

ALTER TABLE [#__sdi_relation] ALTER COLUMN [accessscope_limitation] [tinyint] ;

ALTER TABLE [#__sdi_order] ADD [freeperimetertool] [nvarchar](100) NULL;

ALTER TABLE [#__sdi_pricing_profile] ALTER COLUMN [fixed_fee]  decimal(19,2) NULL;
ALTER TABLE [#__sdi_pricing_profile]  ALTER COLUMN [surface_rate]  decimal(19,2) NULL;
ALTER TABLE [#__sdi_pricing_profile] ALTER COLUMN [min_fee]  decimal(19,2) NULL ;
ALTER TABLE [#__sdi_pricing_profile]  ALTER COLUMN [max_fee]  decimal(19,2) NULL;

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [cfg_fixed_fee]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ADD CONSTRAINT DF_cfg_fixed_fee DEFAULT 0.00 FOR [cfg_fixed_fee];
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [cfg_surface_rate]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ADD CONSTRAINT DF_cfg_surface_rate DEFAULT 0.00 FOR [cfg_surface_rate];
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [cfg_min_fee]  decimal(19,2) NOT NULL ;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ADD CONSTRAINT DF_cfg_min_fee DEFAULT 0.00 FOR [cfg_min_fee];
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [cfg_max_fee]  decimal(19,2) NOT NULL ;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ADD CONSTRAINT DF_cfg_max_fee DEFAULT 0.00 FOR [cfg_max_fee];
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [cfg_pct_category_profile_discount]  decimal(19,2) NOT NULL ;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ADD CONSTRAINT DF_cfg_pct_cpd DEFAULT 0.00 FOR [cfg_pct_category_profile_discount];

ALTER TABLE [#__sdi_pricing_order_supplier_product] ALTER COLUMN [cfg_pct_category_supplier_discount]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier_product] ADD CONSTRAINT DF_cfg_pct_csd DEFAULT 0.00 FOR [cfg_pct_category_supplier_discount];

ALTER TABLE [#__sdi_pricing_order_supplier] ALTER COLUMN [cfg_fixed_fee_ti]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier] ADD CONSTRAINT DF_cfg_fixed_fee_ti DEFAULT 0.00 FOR [cfg_fixed_fee_ti];
ALTER TABLE [#__sdi_pricing_order_supplier] ALTER COLUMN [cal_fee_ti]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier] ADD CONSTRAINT DF_cal_fee_ti DEFAULT 0.00 FOR [cal_fee_ti];

ALTER TABLE [#__sdi_pricing_order] ALTER COLUMN [cfg_vat]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order] ADD CONSTRAINT DF_cfg_vat DEFAULT 0.00 FOR [cfg_vat];
ALTER TABLE [#__sdi_pricing_order] ALTER COLUMN [cfg_overall_default_fee]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order] ADD CONSTRAINT DF_cfg_overall_default_fee DEFAULT 0.00 FOR [cfg_overall_default_fee];
ALTER TABLE [#__sdi_pricing_order] ALTER COLUMN [cal_fee_ti]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order] ADD CONSTRAINT DF_cal_fee_ti_po DEFAULT 0.00 FOR [cal_fee_ti];

ALTER TABLE [#__sdi_organism_category_pricing_rebate] ALTER COLUMN [rebate]  decimal(19,2) NULL;

ALTER TABLE [#__sdi_pricing_profile] ADD CONSTRAINT DF_pp_state DEFAULT 1 FOR [state];
ALTER TABLE [#__sdi_pricing_profile] ADD CONSTRAINT DF_pp_checkedout  DEFAULT '0'  FOR [checked_out];
ALTER TABLE [#__sdi_pricing_profile] ADD CONSTRAINT DF_pp_checkedouttime DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_diffusion] ADD [packageurl] [nvarchar](500);
UPDATE [#__sdi_diffusion] SET [packageurl]='{CODE}';