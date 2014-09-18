ALTER TABLE #__sdi_translation ADD text3  varchar(255) NULL;

ALTER TABLE #__sdi_resourcetype ALTER COLUMN modified_by int NULL;

ALTER TABLE #__sdi_resourcetype ALTER COLUMN modified datetime2 NULL;

ALTER TABLE #__sdi_resourcetypelink ALTER COLUMN modified_by int NULL;

ALTER TABLE #__sdi_resourcetypelink ALTER COLUMN modified datetime2 NULL;

INSERT [#__sdi_sys_perimetertype] ([ordering], [state], [value]) VALUES (1,1,'extraction');
INSERT [#__sdi_sys_perimetertype] ([ordering], [state], [value]) VALUES (2,1,'download');
INSERT [#__sdi_sys_perimetertype] ([ordering], [state], [value]) VALUES (3,1,'both');
