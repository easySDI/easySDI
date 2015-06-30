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


ALTER TABLE [#__sdi_pricing_profile]
ALTER COLUMN [fixed_fee]  decimal(19,2) NULL,
ALTER COLUMN [surface_rate]  decimal(19,2) NULL,
ALTER COLUMN [min_fee]  decimal(19,2) NULL,
ALTER COLUMN [max_fee]  decimal(19,2) NULL;

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile]
ALTER COLUMN [cfg_fixed_fee]  decimal(19,2) NOT NULL DEFAULT 0.00,
ALTER COLUMN [cfg_surface_rate]  decimal(19,2) NOT NULL DEFAULT 0.00,
ALTER COLUMN [cfg_min_fee]  decimal(19,2) NOT NULL DEFAULT 0.00,
ALTER COLUMN [cfg_max_fee]  decimal(19,2) NOT NULL DEFAULT 0.00,
ALTER COLUMN [cfg_pct_category_profile_discount]  decimal(19,2) NOT NULL DEFAULT 0.00;

ALTER TABLE [#__sdi_pricing_order_supplier_product]
ALTER COLUMN [cfg_pct_category_supplier_discount]  decimal(19,2) NOT NULL DEFAULT 0.00;

ALTER TABLE [#__sdi_pricing_order_supplier]
ALTER COLUMN [cfg_fixed_fee_ti]  decimal(19,2) NOT NULL DEFAULT 0.00,
ALTER COLUMN [cal_fee_ti]  decimal(19,2) NOT NULL DEFAULT 0.00;

ALTER TABLE [#__sdi_pricing_order]
ALTER COLUMN [cfg_vat]  decimal(19,2) NOT NULL DEFAULT 0.00,
ALTER COLUMN [cfg_overall_default_fee]  decimal(19,2) NOT NULL DEFAULT 0.00,
ALTER COLUMN [cal_fee_ti]  decimal(19,2) NOT NULL DEFAULT 0.00;

ALTER TABLE [#__sdi_organism_category_pricing_rebate]
ALTER COLUMN [rebate]  decimal(19,2) NULL;