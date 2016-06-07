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
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [cfg_surface_rate]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [cfg_min_fee]  decimal(19,2) NOT NULL ;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [cfg_max_fee]  decimal(19,2) NOT NULL ;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [cfg_pct_category_profile_discount]  decimal(19,2) NOT NULL ;
ALTER TABLE [#__sdi_pricing_order_supplier_product] ALTER COLUMN [cfg_pct_category_supplier_discount]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier] ALTER COLUMN [cfg_fixed_fee_ti]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier] ALTER COLUMN [cal_fee_ti]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order] ALTER COLUMN [cfg_vat]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order] ALTER COLUMN [cfg_overall_default_fee]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order] ALTER COLUMN [cal_fee_ti]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_organism_category_pricing_rebate] ALTER COLUMN [rebate]  decimal(19,2) NULL;
ALTER TABLE [#__sdi_pricing_profile] ADD CONSTRAINT DF_pp_state DEFAULT 1 FOR [state];
ALTER TABLE [#__sdi_pricing_profile] ADD CONSTRAINT DF_pp_checkedout  DEFAULT '0'  FOR [checked_out];
ALTER TABLE [#__sdi_pricing_profile] ADD CONSTRAINT DF_pp_checkedouttime DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_diffusion] ADD [packageurl] [nvarchar](500);
UPDATE [#__sdi_diffusion] SET [packageurl] ='{CODE}' WHERE [id] IS NOT NULL;

CREATE NONCLUSTERED INDEX IX_NC_text1 ON [#__sdi_translation] (text1);
CREATE NONCLUSTERED INDEX IX_NC_text2 ON [#__sdi_translation] (text2);
ALTER TABLE [#__sdi_order] DROP COLUMN [validate];
ALTER TABLE [#__sdi_order] ADD [validated] [smallint];

SET IDENTITY_INSERT [#__sdi_sys_productstate] ON;
INSERT [#__sdi_sys_productstate] ([id], [ordering], [state], [value]) VALUES (7, 7, 1, N'deleted');
SET IDENTITY_INSERT [#__sdi_sys_productstate] OFF;

SET IDENTITY_INSERT [#__sdi_sys_rendertype_criteriatype] ON;
INSERT [#__sdi_sys_rendertype_criteriatype] ([id], [criteriatype_id], [rendertype_id]) VALUES (3, 3, 2);
SET IDENTITY_INSERT [#__sdi_sys_rendertype_criteriatype] OFF;

DELETE FROM [#__sdi_sys_metadatastate] WHERE [id]=5;

UPDATE [#__sdi_sys_orderstate] SET [value] = 'rejectedbythirdparty' WHERE [id] = 9;
UPDATE [#__sdi_sys_orderstate] SET [value] = 'rejectedbysupplier' WHERE [id] = 10;
       
UPDATE [#__sdi_sys_productstate] SET [value] = 'rejectedbythirdparty' WHERE [id] = 5;
UPDATE [#__sdi_sys_productstate] SET [value] = 'rejectedbysupplier' WHERE [id] = 6;

ALTER TABLE [#__sdi_order] ALTER COLUMN [remark] NVARCHAR (4000);

UPDATE [#__sdi_sys_pricing] SET [value] = 'feewithoutapricingprofile' WHERE [id] = 2;
UPDATE [#__sdi_sys_pricing] SET [value] = 'feewithapricingprofile' WHERE [id] = 3;

ALTER TABLE [#__sdi_pricing_order_supplier_product] ALTER COLUMN [cal_amount_data_te]   decimal(19,2) NULL;
ALTER TABLE [#__sdi_pricing_order_supplier_product] ALTER COLUMN [cal_total_amount_te]  decimal(19,2) NULL;
ALTER TABLE [#__sdi_pricing_order_supplier_product] ALTER COLUMN [cal_total_amount_ti]  decimal(19,2) NULL;
UPDATE  [#__sdi_pricing_order_supplier_product] set [cal_total_rebate_ti] = 0 WHERE [cal_total_rebate_ti] IS NULL;
ALTER TABLE [#__sdi_pricing_order_supplier_product] ALTER COLUMN [cal_total_rebate_ti]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier_product] ADD CONSTRAINT DF_cal_total_rebate_ti DEFAULT 0 FOR [cal_total_rebate_ti];

ALTER TABLE [#__sdi_pricing_order_supplier] ALTER COLUMN [cal_total_rebate_ti]  decimal(19,2) NOT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier] ALTER COLUMN [cal_total_amount_ti]  decimal(19,2) NULL;

ALTER TABLE [#__sdi_pricing_order] ALTER COLUMN [cal_total_amount_ti]  decimal(19,2) NULL;

UPDATE [#__sdi_sys_orderstate] SET [ordering] = 1  WHERE [id] = 7;
UPDATE [#__sdi_sys_orderstate] SET [ordering] = 2  WHERE [id] = 8;
UPDATE [#__sdi_sys_orderstate] SET [ordering] = 3  WHERE [id] = 6;
UPDATE [#__sdi_sys_orderstate] SET [ordering] = 4  WHERE [id] = 4;
UPDATE [#__sdi_sys_orderstate] SET [ordering] = 5  WHERE [id] = 5;
UPDATE [#__sdi_sys_orderstate] SET [ordering] = 6  WHERE [id] = 3;
UPDATE [#__sdi_sys_orderstate] SET [ordering] = 7  WHERE [id] = 1;
UPDATE [#__sdi_sys_orderstate] SET [ordering] = 8  WHERE [id] = 2;
UPDATE [#__sdi_sys_orderstate] SET [ordering] = 9  WHERE [id] = 9;
UPDATE [#__sdi_sys_orderstate] SET [ordering] = 10 WHERE [id] = 10;

ALTER TABLE [#__sdi_order] ADD  [validated_by] [bigint] NULL;
ALTER TABLE #__sdi_order ADD CONSTRAINT #__sdi_order$#__sdi_user_fk5
FOREIGN KEY ([validated_by]) REFERENCES [#__sdi_user] ([id]) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE [#__sdi_order_diffusion] ALTER COLUMN [remark] NVARCHAR (4000);

ALTER TABLE [#__sdi_order] ALTER COLUMN [mandate_ref] NVARCHAR (500);