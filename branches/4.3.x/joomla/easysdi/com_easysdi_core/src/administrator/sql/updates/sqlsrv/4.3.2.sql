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