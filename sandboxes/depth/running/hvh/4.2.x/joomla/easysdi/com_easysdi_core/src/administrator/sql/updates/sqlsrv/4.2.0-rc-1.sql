ALTER TABLE #__sdi_translation  ADD CONSTRAINT unique_lang_element UNIQUE (element_guid, language_id);

ALTER TABLE #__sdi_translation ADD text3  varchar(255) NULL;

ALTER TABLE #__sdi_resourcetypelink DROP CONSTRAINT #__sdi_resourcetypelink$#__sdi_resourcetypelink_fk3;
ALTER TABLE #__sdi_resourcetypelink DROP COLUMN class_id;

ALTER TABLE #__sdi_resourcetypelink DROP CONSTRAINT #__sdi_resourcetypelink$#__sdi_resourcetypelink_fk4;
ALTER TABLE #__sdi_resourcetypelink DROP COLUMN attribute_id;

ALTER TABLE #__sdi_resourcetype ALTER COLUMN modified_by int NULL;

ALTER TABLE #__sdi_resourcetype ALTER COLUMN modified datetime2 NULL;

ALTER TABLE #__sdi_resourcetypelink ALTER COLUMN modified_by int NULL;

ALTER TABLE #__sdi_resourcetypelink ALTER COLUMN modified datetime2 NULL;

INSERT [#__sdi_sys_perimetertype] ([ordering], [state], [value]) VALUES (1,1,'extraction');
INSERT [#__sdi_sys_perimetertype] ([ordering], [state], [value]) VALUES (2,1,'download');
INSERT [#__sdi_sys_perimetertype] ([ordering], [state], [value]) VALUES (3,1,'both');

ALTER TABLE #__sdi_application ALTER COLUMN modified_by int NULL;

ALTER TABLE #__sdi_application ALTER COLUMN modified datetime2 NULL;

ALTER TABLE #__sdi_diffusion ALTER COLUMN modified datetime2 NULL;

ALTER TABLE #__sdi_diffusion ALTER COLUMN description [nvarchar](500) NULL;

SET IDENTITY_INSERT [#__sdi_sys_productstorage] ON;
INSERT [#__sdi_sys_productstorage] ([id], [ordering], [state], [value]) VALUES (1, 1, 1, N'upload');
INSERT [#__sdi_sys_productstorage] ([id], [ordering], [state], [value]) VALUES (2, 2, 1, N'url');
INSERT [#__sdi_sys_productstorage] ([id], [ordering], [state], [value]) VALUES (3, 3, 1, N'zoning');
SET IDENTITY_INSERT [#__sdi_sys_productstorage] OFF;

SET IDENTITY_INSERT [#__sdi_sys_productstate] ON;
INSERT [#__sdi_sys_productstate] ([id], [ordering], [state], [value]) VALUES (3, 3, 1, N'sent');
SET IDENTITY_INSERT [#__sdi_sys_productstate] OFF;

SET IDENTITY_INSERT [#__sdi_sys_pricing] ON;
INSERT [#__sdi_sys_pricing] ([id], [ordering], [state], [value]) VALUES (1, 1, 1, N'free');
INSERT [#__sdi_sys_pricing] ([id], [ordering], [state], [value]) VALUES (2, 2, 1, N'fee');
SET IDENTITY_INSERT [#__sdi_sys_pricing] OFF;

SET IDENTITY_INSERT [#__sdi_sys_productmining] ON;
INSERT [#__sdi_sys_productmining] ([id], [ordering], [state], [value]) VALUES (1, 1, 1, N'automatic');
INSERT [#__sdi_sys_productmining] ([id], [ordering], [state], [value]) VALUES (2, 2, 1, N'manual');
SET IDENTITY_INSERT [#__sdi_sys_productmining] OFF;

SET IDENTITY_INSERT [#__sdi_perimeter] ON;
INSERT [#__sdi_perimeter] ([id], [guid], [alias], [created_by], [created], [ordering], [state], [name], [description], [accessscope_id], [perimetertype_id]) VALUES (1, N'1a9f342c-bb1e-9bc4-dd19-38910dff0f59', N'freeperimeter', 356, CAST(0x00FC9F003B370B0000 AS DateTime2),1, 1, N'Free perimeter', '',1,1);
INSERT [#__sdi_perimeter] ([id], [guid], [alias], [created_by], [created], [ordering], [state], [name], [description], [accessscope_id], [perimetertype_id]) VALUES (2, N'9adc6d4e-262a-d6e4-e152-6de437ba80ed', N'myperimeter', 356, CAST(0x00FC9F003B370B0000 AS DateTime2),1, 1, N'My perimeter', '',1,1);
SET IDENTITY_INSERT [#__sdi_perimeter] OFF;

ALTER TABLE #__sdi_order ALTER COLUMN modified datetime2 NULL;

ALTER TABLE #__sdi_order ALTER COLUMN remark [nvarchar](500) NULL;

ALTER TABLE #__sdi_order_diffusion ALTER COLUMN remark [nvarchar](500) NULL;
ALTER TABLE #__sdi_order_diffusion ALTER COLUMN fee [decimal](10,0) NULL;
ALTER TABLE #__sdi_order_diffusion ALTER COLUMN completed datetime2 NULL;
ALTER TABLE #__sdi_order_diffusion ALTER COLUMN [file] [nvarchar](500) NULL;
ALTER TABLE #__sdi_order_diffusion ALTER COLUMN size [decimal](10,0) NULL;

UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 5 WHERE [id] = 1;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 2 WHERE [id] = 2;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 2 WHERE [id] = 3;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 5 WHERE [id] = 4;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 6 WHERE [id] = 5;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 6 WHERE [id] = 6;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 4 WHERE [id] = 7;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 4 WHERE [id] = 8;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 2 WHERE [id] = 9;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 2 WHERE [id] = 10;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 2 WHERE [id] = 11;
UPDATE  [#__sdi_searchcriteria] SET [rendertype_id] = 2 WHERE [id] = 12;


