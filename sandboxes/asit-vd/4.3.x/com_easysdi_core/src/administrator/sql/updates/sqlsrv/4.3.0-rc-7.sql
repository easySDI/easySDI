
ALTER TABLE [#__sdi_order_diffusion] ADD  [guid] [nvarchar](36) NULL;
ALTER TABLE [#__sdi_order_diffusion] ADD  [created_by] [int] NULL;
ALTER TABLE [#__sdi_order_diffusion] ADD  [created] [datetime2](0) NULL;
ALTER TABLE [#__sdi_order_diffusion] ADD  [modified_by] [int] NULL;
ALTER TABLE [#__sdi_order_diffusion] ADD  [modified] [datetime2](0) NULL;
ALTER TABLE [#__sdi_order_diffusion] ADD  [ordering] [int]  NOT NULL AFTER modified;
ALTER TABLE [#__sdi_order_diffusion] ADD  [state] [int]  NOT NULL DEFAULT '1' AFTER ordering;
ALTER TABLE [#__sdi_order_diffusion] ADD  [checked_out] [int] NULL;
ALTER TABLE [#__sdi_order_diffusion] ADD  [checked_out_time] [datetime2](0) NULL;