INSERT [#__sdi_sys_rendertype] ([id], [ordering], [state], [value]) VALUES (9, 9, 1, N'upload');
INSERT [#__sdi_sys_rendertype] ([id], [ordering], [state], [value]) VALUES (10, 10, 1, N'url');
INSERT [#__sdi_sys_rendertype] ([id], [ordering], [state], [value]) VALUES (11, 11, 1, N'upload and url');

INSERT [#__sdi_sys_rendertype_stereotype] ([id], [stereotype_id], [rendertype_id]) VALUES (22, 14, 9);
INSERT [#__sdi_sys_rendertype_stereotype] ([id], [stereotype_id], [rendertype_id]) VALUES (23, 14, 10);
INSERT [#__sdi_sys_rendertype_stereotype] ([id], [stereotype_id], [rendertype_id]) VALUES (24, 14, 11);

DELETE FROM [#__sdi_sys_rendertype_stereotype] WHERE id=20;

ALTER TABLE [#__sdi_visualization] ALTER COLUMN [alias] [nvarchar](255) NOT NULL;

INSERT [#__sdi_sys_role] ([id], [ordering], [state], [value]) VALUES (11, 11, 1, N'organismmanager');