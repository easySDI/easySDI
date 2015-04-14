ALTER TABLE [#__sdi_virtualmetadata] WITH CHECK ADD CONSTRAINT [#__sdi_virtualmetadata$#__sdi_virtualmetadata_fk1] FOREIGN KEY ([virtualservice_id])
REFERENCES [#__sdi_virtualservice] ([id]);
ALTER TABLE [#__sdi_virtualmetadata] CHECK CONSTRAINT [#__sdi_virtualmetadata$#__sdi_virtualmetadata_fk1];

ALTER TABLE [#__sdi_virtualmetadata] WITH CHECK ADD CONSTRAINT [#__sdi_virtualmetadata$#__sdi_virtualmetadata_fk2] FOREIGN KEY ([country_id])
REFERENCES [#__sdi_sys_country] ([id]);
ALTER TABLE [#__sdi_virtualmetadata] CHECK CONSTRAINT [#__sdi_virtualmetadata$#__sdi_virtualmetadata_fk2];

ALTER TABLE [#__sdi_wmts_spatialpolicy] WITH CHECK ADD CONSTRAINT [#__sdi_wmts_spatialpolicy#__sdi_wmts_spatialpolicy_fk1] FOREIGN KEY ([spatialoperator_id])
REFERENCES [#__sdi_sys_spatialoperator] ([id]);
ALTER TABLE [#__sdi_wmts_spatialpolicy] CHECK CONSTRAINT [#__sdi_wmts_spatialpolicy#__sdi_wmts_spatialpolicy_fk1];

SET IDENTITY_INSERT [#__sdi_sys_role] ON;
INSERT [#__sdi_sys_role] ([id], [ordering], [state], [value]) VALUES (9, 9, 1, N'pricingmanager');
INSERT [#__sdi_sys_role] ([id], [ordering], [state], [value]) VALUES (10, 10, 1, N'validationmanager');
SET IDENTITY_INSERT [#__sdi_sys_role] OFF

ALTER TABLE [#__sdi_category] ADD [overall_fee] decimal(6,2) default 0;

ALTER TABLE [#__sdi_organism] ADD [internal_free] smallint NULL;
ALTER TABLE [#__sdi_organism] ADD [fixed_fee_ti] decimal(6,2) NULL;
ALTER TABLE [#__sdi_organism] ADD [data_free_fixed_fee] smallint NULL;
ALTER TABLE [#__sdi_organism] ADD [selectable_as_thirdparty] smallint NULL;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_organism_category_pricing_rebate](
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    [organism_id] [bigint],
    [category_id] [bigint],
    [rebate] [decimal](6,2),
CONSTRAINT [PK_#__sdi_organism_category_pricing_rebate] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];

SET ANSI_NULLS ON;

ALTER TABLE [#__sdi_organism_category_pricing_rebate]  WITH CHECK ADD  CONSTRAINT [#__sdi_organism_category_pricing_rebate#__sdi_organism_category_pricing_rebate_fk1] FOREIGN KEY([organism_id])
REFERENCES [#__sdi_organism] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_organism_category_pricing_rebate] CHECK CONSTRAINT [#__sdi_organism_category_pricing_rebate#__sdi_organism_category_pricing_rebate_fk1];

ALTER TABLE [#__sdi_organism_category_pricing_rebate]  WITH CHECK ADD  CONSTRAINT [#__sdi_organism_category_pricing_rebate#__sdi_organism_category_pricing_rebate_fk2] FOREIGN KEY([category_id])
REFERENCES [#__sdi_category] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_organism_category_pricing_rebate] CHECK CONSTRAINT [#__sdi_organism_category_pricing_rebate#__sdi_organism_category_pricing_rebate_fk2];

SET IDENTITY_INSERT [#__sdi_sys_pricing] ON;
UPDATE [#__sdi_sys_pricing] SET [value] = 'fee without a pricing profile' WHERE [id] = 2;
INSERT [#__sdi_sys_pricing] ([id], [ordering], [state], [value]) VALUES ('3', '3', '1', N'fee with a pricing profile');
SET IDENTITY_INSERT [#__sdi_sys_pricing] OFF;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_profile](
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    [guid] [nvarchar](36) NOT NULL,
    [alias] [nvarchar](50) NOT NULL,
    [created_by] [int] NOT NULL,
    [created] [datetime2](0) NOT NULL,
    [modified_by] [int] NULL,
    [modified] [datetime2](0) NULL,
    [ordering] [int] NOT NULL,
    [state] [int] NOT NULL,
    [checked_out] [int] NOT NULL,
    [checked_out_time] [datetime2](0) NOT NULL,
    [organism_id] [bigint] NOT NULL,
    [name] [nvarchar](75) NOT NULL,
    [fixed_fee] [decimal](6,2),
    [surface_rate] [decimal](6,2),
    [min_fee] [decimal](6,2),
    [max_fee] [decimal](6,2),
CONSTRAINT [PK_#__sdi_pricing_profile] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

ALTER TABLE [#__sdi_pricing_profile]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_profile#__sdi_pricing_profile_fk1] FOREIGN KEY([organism_id])
REFERENCES [#__sdi_organism] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_profile] CHECK CONSTRAINT [#__sdi_pricing_profile#__sdi_pricing_profile_fk1];

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_profile_category_pricing_rebate](
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    [pricing_profile_id] [bigint] NOT NULL,
    [category_id] [bigint] NOT NULL,
    [rebate] [decimal](6,2) default 100,
CONSTRAINT [PK_#__sdi_pricing_profile_category_pricing_rebate] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];

SET ANSI_NULLS ON;

ALTER TABLE [#__sdi_pricing_profile_category_pricing_rebate]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_profile_category_pricing_rebate#__sdi_pricing_profile_category_pricing_rebate_fk1] FOREIGN KEY([pricing_profile_id])
REFERENCES [#__sdi_pricing_profile] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_profile_category_pricing_rebate] CHECK CONSTRAINT [#__sdi_pricing_profile_category_pricing_rebate#__sdi_pricing_profile_category_pricing_rebate_fk1];

ALTER TABLE [#__sdi_pricing_profile_category_pricing_rebate]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_profile_category_pricing_rebate#__sdi_pricing_profile_category_pricing_rebate_fk2] FOREIGN KEY([category_id])
REFERENCES [#__sdi_category] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_profile_category_pricing_rebate] CHECK CONSTRAINT [#__sdi_pricing_profile_category_pricing_rebate#__sdi_pricing_profile_category_pricing_rebate_fk2];


ALTER TABLE [#__sdi_diffusion] ADD [pricing_profile_id] [bigint];

ALTER TABLE [#__sdi_diffusion]  WITH CHECK ADD  CONSTRAINT [#__sdi_diffusion#__sdi_diffusion_fk6] FOREIGN KEY([pricing_profile_id])
REFERENCES [#__sdi_pricing_profile] ([id])
ON DELETE SET NULL;

ALTER TABLE [#__sdi_diffusion] CHECK CONSTRAINT [#__sdi_diffusion#__sdi_diffusion_fk6];

SET IDENTITY_INSERT [#__sdi_sys_orderstate] ON
INSERT [#__sdi_sys_orderstate] ([id], [ordering], [state], [value]) VALUES ('8', '8', '1', N'validation');
INSERT [#__sdi_sys_orderstate] ([id], [ordering], [state], [value]) VALUES ('9', '9', '1', N'rejected by thirdparty');
INSERT [#__sdi_sys_orderstate] ([id], [ordering], [state], [value]) VALUES ('10', '10', '1', N'rejected by supplier');
SET IDENTITY_INSERT [#__sdi_sys_orderstate] OFF

ALTER TABLE [#__sdi_order] ADD [validate] [smallint] DEFAULT NULL;
ALTER TABLE [#__sdi_order] ADD [validated_date] [datetime2](0) DEFAULT NULL;
ALTER TABLE [#__sdi_order] ADD [validated_reason] [nvarchar](500) DEFAULT NULL;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_order](
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    [guid] [nvarchar](36) NOT NULL,
    [alias] [nvarchar](50) NOT NULL,
    [created_by] [int] NOT NULL,
    [created] [datetime2](0) NOT NULL,
    [modified_by] [int] NULL,
    [modified] [datetime2](0) NULL,
    [ordering] [int] NOT NULL,
    [state] [int] NOT NULL,
    [checked_out] [int] NOT NULL,
    [checked_out_time] [datetime2](0) NOT NULL,
    [order_id] [bigint] NOT NULL,
    [cfg_vat] [decimal](6,2) NOT NULL DEFAULT 0,
    [cfg_currency] [char](3) NOT NULL DEFAULT 'CHF',
    [cfg_rounding] [decimal](3,2) NOT NULL DEFAULT '0.05',
    [cfg_overall_default_fee] [decimal](6,2) NOT NULL DEFAULT 0,
    [cfg_free_data_fee] [smallint] DEFAULT 0,
    [cal_total_amount_ti] [decimal],
    [cal_fee_ti] [decimal](6,2) NOT NULL DEFAULT 0,
    [ind_lbl_category_order_fee] [nvarchar](255),
CONSTRAINT [PK_#__sdi_pricing_order] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

ALTER TABLE [#__sdi_pricing_order]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_order#__sdi_pricing_order_fk1] FOREIGN KEY([order_id])
REFERENCES [#__sdi_order] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_order] CHECK CONSTRAINT [#__sdi_pricing_order#__sdi_pricing_order_fk1];


SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_order_supplier](
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    [guid] [nvarchar](36) NOT NULL,
    [alias] [nvarchar](50) NOT NULL,
    [created_by] [int] NOT NULL,
    [created] [datetime2](0) NOT NULL,
    [modified_by] [int] NULL,
    [modified] [datetime2](0) NULL,
    [ordering] [int] NOT NULL,
    [state] [int] NOT NULL,
    [checked_out] [int] NOT NULL,
    [checked_out_time] [datetime2](0) NOT NULL,
    [pricing_order_id] [bigint] NOT NULL,
    [supplier_id] [bigint] NOT NULL,
    [supplier_name] [nvarchar](255) NOT NULL,
    [cfg_internal_free] [smallint] NOT NULL DEFAULT 1,
    [cfg_fixed_fee_ti] [decimal](6,2) NOT NULL DEFAULT 0,
    [cfg_data_free_fixed_fee] [smallint] NOT NULL DEFAULT 0,
    [cal_total_rebate_ti] [decimal] NOT NULL DEFAULT 0,
    [cal_fee_ti] [decimal](6,2) NOT NULL DEFAULT 0,
    [cal_total_amount_ti] [decimal],
CONSTRAINT [PK_#__sdi_pricing_order_supplier] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

ALTER TABLE [#__sdi_pricing_order_supplier]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_order_supplier#__sdi_pricing_order_supplier_fk1] FOREIGN KEY([pricing_order_id])
REFERENCES [#__sdi_pricing_order] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_order_supplier] CHECK CONSTRAINT [#__sdi_pricing_order_supplier#__sdi_pricing_order_supplier_fk1];

ALTER TABLE [#__sdi_pricing_order_supplier]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_order_supplier#__sdi_pricing_order_supplier_fk2] FOREIGN KEY([supplier_id])
REFERENCES [#__sdi_organism] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_order_supplier] CHECK CONSTRAINT [#__sdi_pricing_order_supplier#__sdi_pricing_order_supplier_fk2];


SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_order_supplier_product](
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    [guid] [nvarchar](36) NOT NULL,
    [alias] [nvarchar](50) NOT NULL,
    [created_by] [int] NOT NULL,
    [created] [datetime2](0) NOT NULL,
    [modified_by] [int] NULL,
    [modified] [datetime2](0) NULL,
    [ordering] [int] NOT NULL,
    [state] [int] NOT NULL,
    [checked_out] [int] NOT NULL,
    [checked_out_time] [datetime2](0) NOT NULL,
    [pricing_order_supplier_id] [bigint] not null,
    [product_id] [bigint] not null,
    [pricing_id] [bigint] not null,
    [cfg_pct_category_supplier_discount] [decimal](6,2) NOT NULL DEFAULT 0,
    [ind_lbl_category_supplier_discount] [nvarchar](255),
    [cal_amount_data_te] [decimal],
    [cal_total_amount_te] [decimal],
    [cal_total_amount_ti] [decimal],
    [cal_total_rebate_ti] [decimal],
CONSTRAINT [PK_#__sdi_pricing_order_supplier_product] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

ALTER TABLE [#__sdi_pricing_order_supplier_product]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk1] FOREIGN KEY([pricing_order_supplier_id])
REFERENCES [#__sdi_pricing_order_supplier] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_order_supplier_product] CHECK CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk1];

ALTER TABLE [#__sdi_pricing_order_supplier_product]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk2] FOREIGN KEY([product_id])
REFERENCES [#__sdi_diffusion] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_order_supplier_product] CHECK CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk2];

ALTER TABLE [#__sdi_pricing_order_supplier_product]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk3] FOREIGN KEY([pricing_id])
REFERENCES [#__sdi_sys_pricing] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_order_supplier_product] CHECK CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk3];


SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_order_supplier_product_profile](
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    [guid] [nvarchar](36) NOT NULL,
    [alias] [nvarchar](50) NOT NULL,
    [created_by] [int] NOT NULL,
    [created] [datetime2](0) NOT NULL,
    [modified_by] [int] NULL,
    [modified] [datetime2](0) NULL,
    [ordering] [int] NOT NULL,
    [state] [int] NOT NULL,
    [checked_out] [int] NOT NULL,
    [checked_out_time] [datetime2](0) NOT NULL,
    [pricing_order_supplier_product_id] [bigint]  not null,
    [pricing_profile_id] [bigint]  not null,
    [pricing_profile_name] [nvarchar](255) not null,
    [cfg_fixed_fee] [decimal](6,2) NOT NULL DEFAULT 0,
    [cfg_surface_rate] [decimal](6,2) NOT NULL DEFAULT 0,
    [cfg_min_fee] [decimal](6,2) NOT NULL DEFAULT 0,
    [cfg_max_fee] [decimal](6,2) NOT NULL DEFAULT 0,
    [cfg_pct_category_profile_discount] [decimal](6,2) NOT NULL DEFAULT 0,
    [ind_lbl_category_profile_discount] [nvarchar](255),
CONSTRAINT [PK_#__sdi_pricing_order_supplier_product_profile] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_order_supplier_product_profile#__sdi_pricing_order_supplier_product_profile_fk1] FOREIGN KEY([pricing_order_supplier_product_id])
REFERENCES [#__sdi_pricing_order_supplier_product] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] CHECK CONSTRAINT [#__sdi_pricing_order_supplier_product_profile#__sdi_pricing_order_supplier_product_profile_fk1];

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_order_supplier_product_profile#__sdi_pricing_order_supplier_product_profile_fk2] FOREIGN KEY([pricing_profile_id])
REFERENCES [#__sdi_pricing_profile] ([id])
ON DELETE NO ACTION;

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] CHECK CONSTRAINT [#__sdi_pricing_order_supplier_product_profile#__sdi_pricing_order_supplier_product_profile_fk2];

SET IDENTITY_INSERT [#__sdi_sys_productstate] ON
INSERT [#__sdi_sys_productstate] ([id], [ordering], [state], [value]) VALUES (4, 4, 1, N'validation');
INSERT [#__sdi_sys_productstate] ([id], [ordering], [state], [value]) VALUES (5, 5, 1, N'rejected by thirdparty');
INSERT [#__sdi_sys_productstate] ([id], [ordering], [state], [value]) VALUES (6, 6, 1, N'rejected by supplier');
SET IDENTITY_INSERT [#__sdi_sys_productstate] OFF

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_extractstorage](
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    [ordering] [int],
    [state] [int] NOT NULL DEFAULT 1,
    [value] [nvarchar](255) NOT NULL,
CONSTRAINT [PK_#__sdi_sys_extractstorage] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

SET IDENTITY_INSERT [#__sdi_sys_extractstorage] ON
INSERT [#__sdi_sys_extractstorage] ([id], [ordering], [state], [value]) VALUES (1, 1, 1, N'local');
INSERT [#__sdi_sys_extractstorage] ([id], [ordering], [state], [value]) VALUES (2, 2, 1, N'remote');
SET IDENTITY_INSERT [#__sdi_sys_extractstorage] OFF

