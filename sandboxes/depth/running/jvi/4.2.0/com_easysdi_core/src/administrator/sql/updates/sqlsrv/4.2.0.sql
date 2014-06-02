SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_category](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[alias] [nvarchar](150) NULL,
	[description] [nvarchar](500) NULL,
	[name] [nvarchar](255) NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [int] NOT NULL,
 CONSTRAINT [PK_#__sdi_organism_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];

SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_organism_category](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[organism_id] [bigint] NOT NULL FOREIGN KEY REFERENCES #__sdi_organism(id),
	[category_id] [bigint] NOT NULL FOREIGN KEY REFERENCES #__sdi_category(id),
 CONSTRAINT [PK_#__sdi_organism_category_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


INSERT [#__sdi_sys_accessscope] ([id], [ordering], [state], [value]) VALUES (4, 4, 1, N'category');


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_policy_category](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[policy_id] [bigint] NOT NULL,
	[category_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_policy_category_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];



ALTER TABLE [#__sdi_resourcetype] RENAME COLUMN `meta` TO `application`;