ALTER TABLE [#__sdi_pricing_profile] ADD [apply_vat] [smallint] NOT NULL DEFAULT 1;

ALTER TABLE [#__sdi_organism] ADD [fixed_fee_apply_vat] [smallint] NOT NULL DEFAULT 1;
ALTER TABLE [#__sdi_organism] DROP COLUMN [fixed_fee_ti];
ALTER TABLE [#__sdi_organism] ADD [fixed_fee_te][decimal](6,2) DEFAULT 0;


ALTER TABLE [#__sdi_pricing_order] DROP COLUMN [cfg_overall_default_fee];
ALTER TABLE [#__sdi_pricing_order] ADD [cfg_overall_default_fee_te][decimal](19,2) NOT NULL DEFAULT 0 ;
ALTER TABLE [#__sdi_pricing_order] ADD  [cfg_fee_apply_vat] [smallint] NOT NULL DEFAULT 1 ;

ALTER TABLE [#__sdi_pricing_order_supplier] DROP COLUMN [cfg_fixed_fee_ti];
ALTER TABLE [#__sdi_pricing_order_supplier] ADD [cfg_fixed_fee_te] [decimal](19,2) NOT NULL DEFAULT 0;
ALTER TABLE [#__sdi_pricing_order_supplier] ADD  [cfg_fixed_fee_apply_vat] [smallint] NOT NULL DEFAULT 1 ;

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] DROP COLUMN [cfg_fixed_fee];
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ADD [cfg_fixed_fee_te] [decimal](19,2) NOT NULL DEFAULT 0;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ADD [cfg_apply_vat] [smallint] NOT NULL DEFAULT 1;