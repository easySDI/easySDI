ALTER TABLE [#__sdi_maplayer] ADD [isindoor] [smallint] NULL;
ALTER TABLE [#__sdi_maplayer] ADD [levelfield] [nvarchar](255) NULL;

SET QUOTED_IDENTIFIER ON;
CREATE TABLE [#__sdi_sys_server](
	[id] [bigint] IDENTITY(4,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_server] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];
SET ANSI_NULLS ON;

SET IDENTITY_INSERT [#__sdi_sys_server] ON
INSERT [#__sdi_sys_server] ([id], [ordering], [state], [value]) VALUES ('1', '1', '1', N'geoserver');
INSERT [#__sdi_sys_server] ([id], [ordering], [state], [value]) VALUES ('2', '2', '1', N'arcgisserver');
SET IDENTITY_INSERT [#__sdi_sys_server] OFF

SET QUOTED_IDENTIFIER ON;
CREATE TABLE [#__sdi_sys_server_serviceconnector](
	[id] [bigint] IDENTITY(12,1) NOT NULL,
	[server_id] [bigint] NOT NULL,
	[serviceconnector_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_server_serviceconnector] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];
SET ANSI_NULLS ON;

ALTER TABLE [#__sdi_sys_server_serviceconnector] WITH CHECK ADD CONSTRAINT [#__sdi_sys_server_serviceconnector$#__sdi_sys_server_fk1] FOREIGN KEY ([server_id])
REFERENCES [#__sdi_sys_server] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_sys_server_serviceconnector] CHECK CONSTRAINT [#__sdi_sys_server_serviceconnector$#__sdi_sys_server_fk1];

ALTER TABLE [#__sdi_sys_server_serviceconnector] WITH CHECK ADD CONSTRAINT [#__sdi_sys_server_serviceconnector$#__sdi_sys_serviceconnector_fk2] FOREIGN KEY ([serviceconnector_id])
REFERENCES [#__sdi_sys_serviceconnector] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_sys_server_serviceconnector] CHECK CONSTRAINT [#__sdi_sys_server_serviceconnector$#__sdi_sys_serviceconnector_fk2];

SET IDENTITY_INSERT [#__sdi_sys_server_serviceconnector] ON
INSERT [#__sdi_sys_server_serviceconnector] ([id], [server_id], [serviceconnector_id]) VALUES ('1', '1', '2');
INSERT [#__sdi_sys_server_serviceconnector] ([id], [server_id], [serviceconnector_id]) VALUES ('2', '1', '3');
INSERT [#__sdi_sys_server_serviceconnector] ([id], [server_id], [serviceconnector_id]) VALUES ('3', '1', '4');
INSERT [#__sdi_sys_server_serviceconnector] ([id], [server_id], [serviceconnector_id]) VALUES ('4', '1', '5');
INSERT [#__sdi_sys_server_serviceconnector] ([id], [server_id], [serviceconnector_id]) VALUES ('5', '1', '11');
INSERT [#__sdi_sys_server_serviceconnector] ([id], [server_id], [serviceconnector_id]) VALUES ('6', '2', '2');
INSERT [#__sdi_sys_server_serviceconnector] ([id], [server_id], [serviceconnector_id]) VALUES ('7', '2', '4');
INSERT [#__sdi_sys_server_serviceconnector] ([id], [server_id], [serviceconnector_id]) VALUES ('8', '2', '5');
SET IDENTITY_INSERT [#__sdi_sys_server_serviceconnector] OFF

ALTER TABLE [#__sdi_physicalservice] ADD [server_id] [bigint] NULL;
ALTER TABLE [#__sdi_physicalservice] WITH CHECK ADD CONSTRAINT [#__sdi_physicalservice$#__sdi_sys_server_fk1] FOREIGN KEY ([server_id])
REFERENCES [#__sdi_sys_server] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_physicalservice] CHECK CONSTRAINT [#__sdi_physicalservice$#__sdi_sys_server_fk1];

ALTER TABLE [#__sdi_map_tool] ALTER COLUMN params [nvarchar](MAX);