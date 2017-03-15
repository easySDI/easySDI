ALTER TABLE [#__sdi_diffusion] ADD [pricing_remark] [nvarchar](max) NULL;

UPDATE  [#__sdi_sys_productmining] SET [ordering] = 2 WHERE [id] = 1;
UPDATE  [#__sdi_sys_productmining] SET [ordering] = 1 WHERE [id] = 2;

ALTER TABLE [#__sdi_pricing_profile] ADD [pricing_remark] [nvarchar](max) NULL;