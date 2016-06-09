ALTER TABLE [#__sdi_map] ADD [type] [nvarchar](10)  NOT NULL DEFAULT 'geoext';
UPDATE [#__sdi_map] SET [type] = 'geoext';

CREATE TABLE [#__sdi_processing] (
  [id] [bigint] IDENTITY(12,1) NOT NULL,
  [guid] [nvarchar](255) NOT NULL,
  [alias] [nvarchar](50) NOT NULL,
  [name] [nvarchar](255) NOT NULL,
  [contact_id] [int] NOT NULL,
  [description] [varchar](1000) NOT NULL,
  [auto] [int] NOT NULL,
  [state] [int] NOT NULL,
  [checked_out] [int] NOT NULL,
  [checked_out_time] [nvarchar](45) NOT NULL,
  [accessscope_id] [int] NOT NULL,
  [command] [varchar](1000) NOT NULL,
  [map_id] [int] NOT NULL,
  [parameters] [varchar](1000) NOT NULL,
  [access] [int] NOT NULL,
  [access_id] [int] NULL,
  [created_by] [int] NULL,
  [created] [nvarchar](45) NOT NULL,
  [modified_by] [int] NULL,
  [modified] [nvarchar](45) NOT NULL,
  [ordering] [int] NOT NULL,
  CONSTRAINT [PK_#__sdi_processing] PRIMARY KEY CLUSTERED (
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];
SET ANSI_NULLS ON;

CREATE TABLE [#__sdi_processing_obs] (
  [processing_id] [bigint] IDENTITY(12,1) NOT NULL,
  [sdi_user_id] [bigint]  NOT NULL,
) SET ANSI_NULLS ON;

CREATE TABLE [#__sdi_processing_order] (
  [id] [bigint] IDENTITY(12,1) NOT NULL,
  [guid] [nvarchar](255) NOT NULL,
  [name] [nvarchar](255) NOT NULL,
  [user_id] [int] NOT NULL,
  [processing_id] [int] NOT NULL,
  [parameters] [varchar](1000) NOT NULL,
  [filestorage] [nvarchar](20) NOT NULL,
  [file] [varchar](1000),
  [fileurl] [varchar](1000) NOT NULL,
  [output] [varchar](1000),
  [outputpreview] [varchar](1000) NOT NULL,
  [exec_pid] [nvarchar](255) NOT NULL,
  [status] [int] NOT NULL,
  [info] [varchar](1000),
  [created_by] [int] NULL,
  [created] [nvarchar](45) NOT NULL,
  [modified_by] [int] NOT NULL,
  [modified] [nvarchar](45) NULL,
  [sent] [nvarchar](45) NULL,
    CONSTRAINT [PK_#__sdi_processing_order] PRIMARY KEY CLUSTERED (
            [id] ASC
    )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
    ) ON [PRIMARY];
    SET ANSI_NULLS ON;

ALTER TABLE [#__sdi_category] ADD [backend_only] [tinyint] NOT NULL DEFAULT 0;

ALTER TABLE [#__sdi_order] ADD  [usernotified]     [tinyint]      NOT NULL DEFAULT 0;
ALTER TABLE [#__sdi_order] ADD  [access_token]     [nvarchar](64) NULL;
ALTER TABLE [#__sdi_order] ADD  [validation_token] [nvarchar](64) NULL;

UPDATE [#__sdi_order] SET [access_token]     = LOWER( CONVERT(NVARCHAR(64), HASHBYTES('SHA2_512', CONVERT(NVARCHAR(36),NEWID()) ),2));
UPDATE [#__sdi_order] SET [validation_token] = LOWER( CONVERT(NVARCHAR(64), HASHBYTES('SHA2_512', CONVERT(NVARCHAR(36),NEWID()) ),2));

ALTER TABLE [#__sdi_maplayer] ALTER COLUMN [attribution]  [nvarchar](MAX);

ALTER TABLE [#__sdi_organism] ALTER COLUMN [perimeter]  [nvarchar](MAX);