SET QUOTED_IDENTIFIER ON;

CREATE TABLE [action_types] (
	[ID_ACTION_TYPE] [bigint] NOT NULL,
	[NAME] [nvarchar](45) NOT NULL,
 CONSTRAINT [PK_action_types_ID_ACTION_TYPE] PRIMARY KEY CLUSTERED 
(
	[ID_ACTION_TYPE] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [actions] (
	[ID_ACTION] [bigint] NOT NULL,
	[ID_JOB] [bigint] NOT NULL,
	[ID_ACTION_TYPE] [bigint] NOT NULL,
	[TARGET] [nvarchar](255) NULL,
	[LANGUAGE] [nchar](2) NULL,
 CONSTRAINT [PK_actions_ID_ACTION] PRIMARY KEY CLUSTERED 
(
	[ID_ACTION] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

SET ANSI_PADDING ON;

CREATE TABLE [alerts] (
	[ID_ALERT] [bigint] NOT NULL,
	[ID_JOB] [bigint] NOT NULL,
	[ID_OLD_STATUS] [bigint] NOT NULL,
	[ID_NEW_STATUS] [bigint] NOT NULL,
	[CAUSE] [nvarchar](max) NOT NULL,
	[ALERT_DATE_TIME] [datetime2](0) NOT NULL,
	[EXPOSE_RSS] [smallint] NOT NULL,
	[RESPONSE_DELAY] [real] NOT NULL,
	[HTTP_CODE] [bigint] NULL,
	[IMAGE] [varbinary](max) NULL,
	[CONTENT_TYPE] [nvarchar](50) NULL,
 CONSTRAINT [PK_alerts_ID_ALERT] PRIMARY KEY CLUSTERED 
(
	[ID_ALERT] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];

SET ANSI_PADDING OFF;


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [holidays] (
	[ID_HOLIDAYS] [bigint] NOT NULL,
	[NAME] [nvarchar](45) NULL,
	[DATE] [datetime2](0) NOT NULL,
 CONSTRAINT [PK_holidays_ID_HOLIDAYS] PRIMARY KEY CLUSTERED 
(
	[ID_HOLIDAYS] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [http_methods] (
	[ID_HTTP_METHOD] [bigint] NOT NULL,
	[NAME] [nvarchar](10) NOT NULL,
 CONSTRAINT [PK_http_methods_ID_HTTP_METHOD] PRIMARY KEY CLUSTERED 
(
	[ID_HTTP_METHOD] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [job_agg_hour_log_entries] (
	[DATE_LOG] [datetime2](0) NOT NULL,
	[ID_JOB] [bigint] NOT NULL,
	[H1_MEAN_RESP_TIME] [real] NOT NULL,
	[H1_MEAN_RESP_TIME_INSPIRE] [real] NOT NULL,
	[H1_AVAILABILITY] [real] NOT NULL,
	[H1_AVAILABILITY_INSPIRE] [real] NOT NULL,
	[H1_NB_BIZ_ERRORS] [bigint] NOT NULL,
	[H1_NB_BIZ_ERRORS_INSPIRE] [bigint] NOT NULL,
	[H1_NB_CONN_ERRORS] [bigint] NOT NULL,
	[H1_NB_CONN_ERRORS_INSPIRE] [bigint] NOT NULL,
	[H1_MAX_RESP_TIME] [real] NOT NULL,
	[H1_MIN_RESP_TIME] [real] NOT NULL,
	[H1_MAX_RESP_TIME_INSPIRE] [real] NOT NULL,
	[H1_MIN_RESP_TIME_INSPIRE] [real] NOT NULL,
	[H1_UNAVAILABILITY] [real] NOT NULL,
	[H1_UNAVAILABILITY_INSPIRE] [real] NOT NULL,
	[H1_FAILURE] [real] NOT NULL,
	[H1_FAILURE_INSPIRE] [real] NOT NULL,
	[H1_UNTESTED] [real] NOT NULL,
	[H1_UNTESTED_INSPIRE] [real] NOT NULL,
 CONSTRAINT [PK_job_agg_hour_log_entries_DATE_LOG] PRIMARY KEY CLUSTERED 
(
	[DATE_LOG] ASC,
	[ID_JOB] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [job_agg_log_entries] (
	[DATE_LOG] [datetime2](0) NOT NULL,
	[ID_JOB] [bigint] NOT NULL,
	[SLA_MEAN_RESP_TIME] [real] NOT NULL,
	[H24_MEAN_RESP_TIME] [real] NOT NULL,
	[SLA_AVAILABILITY] [real] NOT NULL,
	[H24_AVAILABILITY] [real] NOT NULL,
	[SLA_NB_BIZ_ERRORS] [bigint] NOT NULL,
	[H24_NB_BIZ_ERRORS] [bigint] NOT NULL,
	[SLA_NB_CONN_ERRORS] [bigint] NOT NULL,
	[H24_NB_CONN_ERRORS] [bigint] NOT NULL,
	[H24_MAX_RESP_TIME] [real] NOT NULL,
	[H24_MIN_RESP_TIME] [real] NOT NULL,
	[SLA_MAX_RESP_TIME] [real] NOT NULL,
	[SLA_MIN_RESP_TIME] [real] NOT NULL,
	[SLA_UNAVAILABILITY] [real] NOT NULL,
	[H24_UNAVAILABILITY] [real] NOT NULL,
	[SLA_FAILURE] [real] NOT NULL,
	[H24_FAILURE] [real] NOT NULL,
	[SLA_UNTESTED] [real] NOT NULL,
	[H24_UNTESTED] [real] NOT NULL,
 CONSTRAINT [PK_job_agg_log_entries_DATE_LOG] PRIMARY KEY CLUSTERED 
(
	[DATE_LOG] ASC,
	[ID_JOB] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [job_defaults] (
	[ID_PARAM] [bigint] NOT NULL,
	[COLUMN_NAME] [nvarchar](45) NOT NULL,
	[STRING_VALUE] [nvarchar](45) NULL,
	[VALUE_TYPE] [nvarchar](20) NOT NULL,
 CONSTRAINT [PK_job_defaults_ID_PARAM] PRIMARY KEY CLUSTERED 
(
	[ID_PARAM] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [jobs] (
	[ID_JOB] [bigint] NOT NULL,
	[NAME] [nvarchar](45) NOT NULL,
	[ID_SERVICE_TYPE] [bigint] NOT NULL,
	[SERVICE_URL] [nvarchar](255) NOT NULL,
	[ID_HTTP_METHOD] [bigint] NOT NULL,
	[TEST_INTERVAL] [bigint] NOT NULL,
	[TIMEOUT] [bigint] NOT NULL,
	[BUSINESS_ERRORS] [smallint] NOT NULL,
	[SLA_START_TIME] [datetime2](0) NOT NULL,
	[LOGIN] [nvarchar](45) NULL,
	[PASSWORD] [nvarchar](45) NULL,
	[IS_PUBLIC] [smallint] NOT NULL,
	[IS_AUTOMATIC] [smallint] NOT NULL,
	[ALLOWS_REALTIME] [smallint] NOT NULL,
	[TRIGGERS_ALERTS] [smallint] NOT NULL,
	[ID_STATUS] [bigint] NOT NULL,
	[HTTP_ERRORS] [smallint] NOT NULL,
	[SLA_END_TIME] [datetime2](0) NOT NULL,
	[STATUS_UPDATE_TIME] [datetime] NOT NULL,
	[SAVE_RESPONSE] [smallint] NOT NULL,
	[RUN_SIMULTANEOUS] [smallint] NOT NULL,
 CONSTRAINT [PK_jobs_ID_JOB] PRIMARY KEY CLUSTERED 
(
	[ID_JOB] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [jobs$UNIQUE_NAME] UNIQUE NONCLUSTERED 
(
	[NAME] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_accessscope] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[entity_guid] [nvarchar](36) NOT NULL,
	[organism_id] [bigint] NULL,
	[user_id] [bigint] NULL,
	[category_id] [bigint] NULL,
 CONSTRAINT [PK_#__sdi_accessscope_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_address] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[user_id] [bigint] NULL,
	[organism_id] [bigint] NULL,
	[addresstype_id] [bigint] NOT NULL,
	[civility] [nvarchar](100) NULL,
	[firstname] [nvarchar](100) NULL,
	[lastname] [nvarchar](100) NULL,
	[function] [nvarchar](100) NULL,
	[address] [nvarchar](100) NULL,
	[addresscomplement] [nvarchar](100) NULL,
	[postalcode] [nvarchar](10) NULL,
	[postalbox] [nvarchar](10) NULL,
	[locality] [nvarchar](100) NULL,
	[country_id] [bigint] NULL,
	[phone] [nvarchar](20) NULL,
	[mobile] [nvarchar](20) NULL,
	[fax] [nvarchar](20) NULL,
	[email] [nvarchar](100) NULL,
	[sameascontact] [smallint] NOT NULL,
 CONSTRAINT [PK_#__sdi_address_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_allowedoperation] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[policy_id] [bigint] NOT NULL,
	[serviceoperation_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_allowedoperation_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_application] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NOT NULL,
	[resource_id] [bigint] NOT NULL,
	[options] [nvarchar](500) NOT NULL,
	[url] [nvarchar](500) NOT NULL,
	[windowname] [nvarchar](255) NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_application_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_assignment] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[assigned] [datetime2](0) NULL,
	[assigned_by] [bigint] NOT NULL,
	[assigned_to] [bigint] NOT NULL,
	[metadata_id] [bigint] NOT NULL,
	[text] [nvarchar](500) NULL,
 CONSTRAINT [PK_#__sdi_assignment_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_attribute] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NULL,
	[namespace_id] [bigint] NOT NULL,
	[isocode] [nvarchar](255) NULL,
	[stereotype_id] [bigint] NOT NULL,
	[length] [int] NULL,
	[pattern] [nvarchar](500) NULL,
	[listnamespace_id] [bigint] NULL,
	[type_isocode] [nvarchar](255) NULL,
	[codelist] [nvarchar](255) NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_attribute_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_attributevalue] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[value] [nvarchar](255) NULL,
	[attribute_id] [bigint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_attributevalue_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_boundary] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[northbound] [nvarchar](255) NULL,
	[southbound] [nvarchar](255) NULL,
	[eastbound] [nvarchar](255) NULL,
	[westbound] [nvarchar](255) NULL,
	[category_id] [bigint] NULL,
	[parent_id] [bigint] NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_boundary_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_boundarycategory] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[parent_id] [bigint] NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_boundarycategory_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_catalog] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NULL,
        [contextualsearchresultpaginationnumber] [tinyint] DEFAULT 0,
	[xsldirectory] [nvarchar](255) NULL,
	[oninitrunsearch] [smallint] NULL,
	[cswfilter] [nvarchar](max) NULL,        
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
    [scrolltoresults] [smallint] NOT NULL DEFAULT 1,
 CONSTRAINT [PK_#__sdi_catalog_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_catalog_resourcetype] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[catalog_id] [bigint] NOT NULL,
	[resourcetype_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_catalog_resourcetype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_catalog_searchcriteria] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[catalog_id] [bigint] NOT NULL,
	[searchcriteria_id] [bigint] NOT NULL,
	[searchtab_id] [bigint] NOT NULL,
	[defaultvalue] [nvarchar](255) NULL,
	[defaultvaluefrom] [datetime2](0) NULL,
	[defaultvalueto] [datetime2](0) NULL,
	[params] [nvarchar](500) NULL,
 CONSTRAINT [PK_#__sdi_catalog_searchcriteria_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_catalog_searchsort] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [smallint] NOT NULL,
	[catalog_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[ogcsearchsorting] [nvarchar](255) NULL,
 CONSTRAINT [PK_#__sdi_catalog_searchsort_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_class] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NULL,
	[issystem] [smallint] NOT NULL,
	[isrootclass] [smallint] NOT NULL,
	[namespace_id] [bigint] NOT NULL,
	[isocode] [nvarchar](255) NULL,
	[stereotype_id] [bigint] NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_class_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_csw_spatialpolicy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[eastboundlongitude] [decimal](10, 6) NULL,
	[westboundlongitude] [decimal](10, 6) NULL,
	[northboundlatitude] [decimal](10, 6) NULL,
	[southboundlatitude] [decimal](10, 6) NULL,
	[maxx] [decimal](18, 6) NULL,
	[maxy] [decimal](18, 6) NULL,
	[minx] [decimal](18, 6) NULL,
	[miny] [decimal](18, 6) NULL,
	[srssource] [nvarchar](255) NULL,
 CONSTRAINT [PK_#__sdi_csw_spatialpolicy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_diffusion] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](255) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[version_id] [bigint] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NULL,
	[accessscope_id] [bigint] NOT NULL,
	[pricing_id] [bigint] NOT NULL,
        [pricing_profile_id] [bigint],
        [pricing_remark] [nvarchar](max) NULL,
	[deposit] [nvarchar](255) NULL,
	[productmining_id] [bigint] NULL,
	[surfacemin] [nvarchar](50) NULL,
	[surfacemax] [nvarchar](50) NULL,
	[productstorage_id] [bigint] NULL,
	[file] [nvarchar](255) NULL,
	[fileurl] [nvarchar](500) NULL,
        [packageurl] [nvarchar](500) ,
	[perimeter_id] [bigint] NULL,
	[hasdownload] [smallint] NOT NULL,
	[hasextraction] [smallint] NOT NULL,
	[restrictedperimeter] [smallint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
        [otp][tinyint](1) NOT NULL DEFAULT 0,
 CONSTRAINT [PK_#__sdi_diffusion_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_diffusion_download] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[diffusion_id] [bigint] NOT NULL,
	[user_id] [bigint] NULL,
	[executed] [datetime2](0) NOT NULL,
 CONSTRAINT [PK_#__sdi_diffusion_download_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_diffusion_notifieduser] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[diffusion_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_diffusion_notifieduser_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_diffusion_perimeter] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[diffusion_id] [bigint] NOT NULL,
	[perimeter_id] [bigint] NOT NULL,	
 CONSTRAINT [PK_#__sdi_diffusion_perimeter_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_diffusion_propertyvalue] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[diffusion_id] [bigint] NOT NULL,
	[propertyvalue_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_diffusion_propertyvalue_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_excludedattribute] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[path] [nvarchar](500) NOT NULL,
	[policy_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_excludedattribute_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_featuretype_policy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](255) NULL,
	[enabled] [smallint] NOT NULL,
	[inheritedspatialpolicy] [smallint] NOT NULL,
	[spatialpolicy_id] [bigint] NULL,
	[physicalservicepolicy_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_featuretype_policy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_importref] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NULL,
	[xsl4sdi] [nvarchar](255) NULL,
	[xsl4ext] [nvarchar](255) NULL,
	[cswservice_id] [bigint] NULL,
	[cswversion_id] [bigint] NULL,
	[cswoutputschema] [nvarchar](255) NULL,
	[importtype_id] [bigint] NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_importref_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_includedattribute] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](500) NOT NULL,
	[featuretypepolicy_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_includedattribute_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_language] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [bigint] NOT NULL,
	[state] [int] NULL,
	[value] [nvarchar](50) NOT NULL,
	[code] [nvarchar](20) NOT NULL,
	[gemet] [nvarchar](10) NOT NULL,
	[iso639-2T] [nvarchar](10) NULL,
	[iso639-1] [nvarchar](10) NULL,
	[iso3166-1-alpha2] [nvarchar](10) NULL,
	[iso639-2B] [nvarchar](10) NULL,
	[datatable] [nvarchar](50) NOT NULL DEFAULT 'English',
 CONSTRAINT [PK_#__sdi_language_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_layer] (
	[id] [bigint] IDENTITY(10,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](255) NULL,
	[physicalservice_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_layer_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_layer_layergroup] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[layer_id] [bigint] NOT NULL,
	[group_id] [bigint] NOT NULL,
	[ordering] [int] NULL,
 CONSTRAINT [PK_#__sdi_layer_layergroup_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_layergroup] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](20) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[isdefaultopen] [smallint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [int] NULL,
 CONSTRAINT [PK_#__sdi_layergroup_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [#__sdi_layergroup$alias] UNIQUE NONCLUSTERED 
(
	[alias] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_map] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](20) NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[created_by] [int] NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[title] [nvarchar](255) NOT NULL,
        [type] [nvarchar](10)  NOT NULL DEFAULT 'geoext',
	[rootnodetext] [nvarchar](255) NULL,
	[srs] [nvarchar](255) NOT NULL,
	[unit_id] [bigint] NOT NULL,
	[maxresolution] [float] NULL,
	[numzoomlevel] [int] NULL,
	[maxextent] [nvarchar](255) NOT NULL,
	[restrictedextent] [nvarchar](255) NULL,
	[centercoordinates] [nvarchar](255) NULL,
	[zoom] [nvarchar](10) NULL,
	[abstract] [nvarchar](max) NULL,
	[access] [int] NOT NULL,
	[asset_id] [int] NULL,
 CONSTRAINT [PK_#__sdi_map_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [#__sdi_map$alias] UNIQUE NONCLUSTERED 
(
	[alias] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_map_layergroup] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[map_id] [bigint] NOT NULL,
	[group_id] [bigint] NOT NULL,
	[isbackground] [smallint] NOT NULL,
	[isdefault] [smallint] NOT NULL,
	[ordering] [int] NULL,
 CONSTRAINT [PK_#__sdi_map_layergroup_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_map_physicalservice] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[map_id] [bigint] NOT NULL,
	[physicalservice_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_map_physicalservice_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_map_tool] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[map_id] [bigint] NOT NULL,
	[tool_id] [bigint] NOT NULL,
	[params] [nvarchar](MAX) NULL,
        [activated] [tinyint] DEFAULT 0,
 CONSTRAINT [PK_#__sdi_map_tool_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_map_virtualservice] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[map_id] [bigint] NOT NULL,
	[virtualservice_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_map_virtualservice_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_maplayer] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](255) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[service_id] [bigint] NULL,
	[servicetype] [nvarchar](10) NULL,
	[layername] [nvarchar](255) NOT NULL,
	[istiled] [smallint] NOT NULL,
	[isdefaultvisible] [smallint] NOT NULL,
	[opacity] [decimal](3, 2) NOT NULL,
	[asOL] [smallint] NOT NULL,
	[asOLstyle] [nvarchar](max) NULL,
	[asOLmatrixset] [nvarchar](max) NULL,
	[asOLoptions] [nvarchar](max) NULL,
	[metadatalink] [nvarchar](max) NULL,
	[attribution] [nvarchar](255) NULL,
	[accessscope_id] [bigint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [int] NULL,
        [isindoor] [smallint] NULL,
        [levelfield] [nvarchar](255) NULL,
 CONSTRAINT [PK_#__sdi_maplayer_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [#__sdi_maplayer$alias] UNIQUE NONCLUSTERED 
(
	[alias] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_metadata] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[metadatastate_id] [bigint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[accessscope_id] [bigint] NOT NULL,
	[published] [datetime2](0) NULL,
        [endpublished] [datetime2](0) NULL,
	[archived] [datetime2](0) NULL,
	[lastsynchronization] [datetime2](0) NULL,
	[synchronized_by] [bigint] NULL,
	[notification] [smallint] NOT NULL,
	[version_id] [bigint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_metadata_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_monitor_exports] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[exportDesc] [nvarchar](500) NULL,
	[exportName] [nvarchar](500) NULL,
	[exportType] [nvarchar](10) NULL,
	[xsltUrl] [nvarchar](500) NULL,
 CONSTRAINT [PK_#__sdi_monitor_exports_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_namespace] (
	[id] [bigint] IDENTITY(6,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[prefix] [nvarchar](10) NOT NULL,
	[uri] [nvarchar](255) NOT NULL,
	[system] [smallint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_namespace_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_order] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](255) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[ordertype_id] [bigint] NULL,
	[orderstate_id] [bigint] NOT NULL,
        [archived] [int] NOT NULL DEFAULT 0,
	[user_id] [bigint] NOT NULL,
	[thirdparty_id] [bigint] NULL,
        [validated] [smallint] ,
        [validated_date] [datetime2](0) ,
        [validated_reason] [nvarchar](500) ,
	[validated_by] [int] NULL,
	[surface] [numeric](38, 18) NULL,
	[remark] [nvarchar](4000) NULL,
        [mandate_ref] [nvarchar](500) NULL,
        [mandate_contact] [nvarchar](75) NULL,
        [mandate_email] [nvarchar](100) NULL,
        [level] [nvarchar](100) NULL,
        [freeperimetertool] [nvarchar](100) NULL,
	[sent] [datetime2](0) NOT NULL,
	[completed] [datetime2](0) NOT NULL,
	[usernotified] [tinyint] NOT NULL,
	[access_token] [nvarchar](64) NULL,
	[validation_token] [nvarchar](64) NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_order_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_order_diffusion] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [smallint] NOT NULL DEFAULT 1,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[order_id] [bigint] NOT NULL,
	[diffusion_id] [bigint] NOT NULL,
	[productstate_id] [bigint] NOT NULL,
	[remark] [nvarchar](4000) NULL,
	[completed] [datetime2](0) NULL,
	[storage_id] [bigint] NULL,
	[file] [nvarchar](500) NULL,
	[size] [decimal](10, 0) NULL,
        [displayName] [nvarchar](75) NULL,
        [opt] [nvarchar] NULL,
        [optchance] [int] (11) DEFAULT 0,
 CONSTRAINT [PK_#__sdi_order_diffusion_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_order_perimeter] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[order_id] [bigint] NOT NULL,
	[perimeter_id] [bigint] NOT NULL,
	[value] [nvarchar](max) NULL,
	[text] [nvarchar](max) NULL,
	[created_by] [int] NOT NULL,
 CONSTRAINT [PK_#__sdi_order_perimeter_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_order_propertyvalue] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[orderdiffusion_id] [bigint] NOT NULL,
	[property_id] [bigint] NOT NULL,
	[propertyvalue_id] [bigint] NOT NULL,
	[propertyvalue] [nvarchar](4000) NOT NULL,
	[created_by] [int] NOT NULL,
 CONSTRAINT [PK_#__sdi_order_propertyvalue_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_organism] (
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
	[acronym] [nvarchar](150) NULL,
	[description] [nvarchar](500) NULL,
	[logo] [nvarchar](500) NULL,
	[name] [nvarchar](255) NOT NULL,
	[website] [nvarchar](500) NULL,
	[perimeter] [nvarchar](max) NULL,
        [selectable_as_thirdparty] [smallint] NULL,
	[access] [int] NOT NULL,
	[asset_id] [int] NOT NULL,
	[username] [nvarchar](150) NULL,
	[password] [nvarchar](65) NULL,
        [internal_free] [smallint] NULL,
        [fixed_fee_te] [decimal](6,2) NULL,
        [data_free_fixed_fee] [smallint] NULL,
        [fixed_fee_apply_vat] [smallint] NOT NULL DEFAULT 1,
 CONSTRAINT [PK_#__sdi_organism_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];

SET ANSI_NULLS ON;



SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_category] (
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
        [overall_fee] decimal(6,2)  NULL,
        [backend_only] [tinyint] NOT NULL,
 CONSTRAINT [PK_#__sdi_category_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];

SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_organism_category] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[organism_id] [bigint] NOT NULL FOREIGN KEY REFERENCES #__sdi_organism(id),
	[category_id] [bigint] NOT NULL FOREIGN KEY REFERENCES #__sdi_category(id),
 CONSTRAINT [PK_#__sdi_organism_category_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_perimeter] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](255) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NULL,
	[accessscope_id] [bigint] NOT NULL,
	[perimetertype_id] [bigint] NOT NULL,
	[wfsservice_id] [bigint] NULL,
	[wfsservicetype_id] [bigint] NULL,
	[featuretypename] [nvarchar](255) NULL,
	[prefix] [nvarchar](255) NULL,
	[namespace] [nvarchar](255) NULL,
	[featuretypefieldid] [nvarchar](255) NULL,
	[featuretypefieldname] [nvarchar](255) NULL,
	[featuretypefieldsurface] [nvarchar](255) NULL,
	[featuretypefielddescription] [nvarchar](255) NULL,
	[featuretypefieldgeometry] [nvarchar](255) NULL,
	[featuretypefieldresource] [nvarchar](255) NULL,
        [featuretypefieldlevel] [nvarchar](255) NULL,
        [maplayer_id] [bigint] NULL,
	[wmsservice_id] [bigint] NULL,
	[wmsservicetype_id] [bigint] NULL,
	[layername] [nvarchar](255) NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_perimeter_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_physicalservice] (
	[id] [bigint] IDENTITY(4,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](20) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NULL,
	[servicescope_id] [bigint] NOT NULL,
	[serviceconnector_id] [bigint] NOT NULL,
	[resourceauthentication_id] [bigint] NULL,
	[resourceurl] [nvarchar](500) NULL,
	[resourceusername] [nvarchar](150) NULL,
	[resourcepassword] [nvarchar](150) NULL,
	[serviceauthentication_id] [bigint] NULL,
	[serviceurl] [nvarchar](500) NULL,
	[serviceusername] [nvarchar](150) NULL,
	[servicepassword] [nvarchar](150) NULL,
	[catid] [int] NOT NULL,
	[params] [nvarchar](1024) NULL,
	[access] [int] NOT NULL,
	[asset_id] [int] NULL,
 CONSTRAINT [PK_#__sdi_physicalservice_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [#__sdi_physicalservice$name] UNIQUE NONCLUSTERED 
(
	[name] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_physicalservice_organism] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[physicalservice_id] [bigint] NOT NULL,
	[organism_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_physicalservice_organism_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_physicalservice_policy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[prefix] [nvarchar](255) NULL,
	[namespace] [nvarchar](255) NULL,
	[anyitem] [smallint] NOT NULL,
	[inheritedspatialpolicy] [smallint] NOT NULL,
	[csw_spatialpolicy_id] [bigint] NULL,
	[wms_spatialpolicy_id] [bigint] NULL,
	[wmts_spatialpolicy_id] [bigint] NULL,
	[wfs_spatialpolicy_id] [bigint] NULL,
	[physicalservice_id] [bigint] NOT NULL,
	[policy_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_physicalservice_policy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_physicalservice_servicecompliance] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[service_id] [bigint] NOT NULL,
	[servicecompliance_id] [bigint] NOT NULL,
	[capabilities] [nvarchar](max) NULL,
 CONSTRAINT [PK_#__sdi_physicalservice_servicecompliance_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_policy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[name] [nvarchar](255) NOT NULL,
	[alias] [nvarchar](20) NOT NULL,
	[allowfrom] [date] NOT NULL,
	[allowto] [date] NOT NULL,
	[anyoperation] [smallint] NOT NULL,
	[anyservice] [smallint] NOT NULL,
	[accessscope_id] [bigint] NOT NULL,
	[virtualservice_id] [bigint] NOT NULL,
	[csw_spatialpolicy_id] [bigint] NULL,
	[wms_spatialpolicy_id] [bigint] NULL,
	[wmts_spatialpolicy_id] [bigint] NULL,
	[wfs_spatialpolicy_id] [bigint] NULL,
	[csw_version_id] [bigint] NOT NULL,
	[csw_anyattribute] [smallint] NOT NULL,
	[csw_anycontext] [smallint] NOT NULL,
	[csw_anystate] [smallint] NOT NULL,
	[csw_anyvisibility] [smallint] NOT NULL,
	[csw_includeharvested] [smallint] NOT NULL,
	[csw_anyresourcetype] [smallint] NOT NULL,
	[csw_accessscope_id] [smallint] NOT NULL,
	[wms_minimumwidth] [nvarchar](255) NULL,
	[wms_minimumheight] [nvarchar](255) NULL,
	[wms_maximumwidth] [nvarchar](255) NULL,
	[wms_maximumheight] [nvarchar](255) NULL,
	[params] [nvarchar](1024) NULL,
	[access] [int] NOT NULL,
	[asset_id] [int] NULL,
 CONSTRAINT [PK_#__sdi_policy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_policy_metadatastate] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[metadatastate_id] [bigint] NOT NULL,
	[policy_id] [bigint] NOT NULL,
	[metadataversion_id] [bigint] NULL,
 CONSTRAINT [PK_#__sdi_policy_metadatastate_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_policy_organism] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[policy_id] [bigint] NOT NULL,
	[organism_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_policy_organism_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];

SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_policy_category] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[policy_id] [bigint] NOT NULL,
	[category_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_policy_category_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_policy_resourcetype] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[resourcetype_id] [bigint] NOT NULL,
	[policy_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_policy_resourcetype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_policy_user] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[policy_id] [bigint] NOT NULL,
	[user_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_policy_user_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_policy_visibility] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[policy_id] [bigint] NOT NULL,
	[user_id] [bigint] NULL,
	[organism_id] [bigint] NULL,
 CONSTRAINT [PK_#__sdi_policy_visibility_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_profile] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NULL,
	[class_id] [bigint] NOT NULL,
	[metadataidentifier] [bigint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_profile_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_property] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](255) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NOT NULL,
	[accessscope_id] [bigint] NOT NULL,
	[mandatory] [int] NOT NULL,
	[propertytype_id] [bigint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_property_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_propertyvalue] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](255) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NOT NULL,
	[property_id] [bigint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_propertyvalue_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_relation] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NULL,
	[parent_id] [bigint] NOT NULL,
	[attributechild_id] [bigint] NULL,
	[classchild_id] [bigint] NULL,
	[lowerbound] [int] NULL,
	[upperbound] [int] NULL,
	[relationtype_id] [bigint] NULL,
	[rendertype_id] [bigint] NULL,
	[namespace_id] [bigint] NULL,
	[isocode] [nvarchar](255) NULL,
	[classassociation_id] [bigint] NULL,
	[issearchfilter] [smallint] NOT NULL,
	[relationscope_id] [bigint] NULL,
	[editorrelationscope_id] [bigint] NULL,
	[childresourcetype_id] [bigint] NULL,
	[childtype_id] [bigint] NULL,
        [accessscope_limitation] [tinyint] DEFAULT 0,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_relation_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_relation_catalog] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [smallint] NOT NULL,
	[relation_id] [bigint] NOT NULL,
	[catalog_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_relation_catalog_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_relation_defaultvalue] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[relation_id] [bigint] NOT NULL,
	[attributevalue_id] [bigint] NULL,
	[value] [nvarchar](500) NULL,
	[language_id] [bigint] NULL,
 CONSTRAINT [PK_#__sdi_relation_defaultvalue_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_relation_profile] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [smallint] NOT NULL,
	[relation_id] [bigint] NOT NULL,
	[profile_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_relation_profile_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_resource] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NULL,
	[checked_out_time] [datetime2](0) NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NOT NULL,
	[organism_id] [bigint] NOT NULL,
	[resourcetype_id] [bigint] NOT NULL,
	[accessscope_id] [bigint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_resource_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_resourcetype] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](500) NOT NULL,
	[logo] [nvarchar](255) NOT NULL,
	[application] [smallint] NOT NULL,
	[diffusion] [smallint] NOT NULL,
	[view] [smallint] NOT NULL,
	[monitoring] [smallint] NOT NULL,
	[predefined] [smallint] NOT NULL,
	[versioning] [smallint] NOT NULL,
	[profile_id] [bigint] NOT NULL,
	[fragmentnamespace_id] [bigint] NULL,
	[fragment] [nvarchar](255) NULL,
	[sitemapparams] [nvarchar](1000) NULL,
	[accessscope_id] [bigint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_resourcetype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_resourcetypelink] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[parent_id] [bigint] NOT NULL,
	[child_id] [bigint] NOT NULL,
	[parentboundlower] [int] NOT NULL,
	[parentboundupper] [int] NOT NULL,
	[childboundlower] [int] NOT NULL,
	[childboundupper] [int] NOT NULL,
	[viralversioning] [smallint] NOT NULL,
	[inheritance] [smallint] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_resourcetypelink_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_resourcetypelinkinheritance] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[resourcetypelink_id] [bigint] NOT NULL,
	[xpath] [nvarchar](500) NOT NULL,
 CONSTRAINT [PK_#__sdi_resourcetypelinkinheritance_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_searchcriteria] (
	[id] [bigint] IDENTITY(14,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[issystem] [smallint] NOT NULL,
	[criteriatype_id] [bigint] NOT NULL,
	[rendertype_id] [bigint] NULL,
	[relation_id] [bigint] NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_searchcriteria_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_searchcriteriafilter] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [smallint] NOT NULL,
	[searchcriteria_id] [bigint] NOT NULL,
	[language_id] [bigint] NOT NULL,
	[ogcsearchfilter] [nvarchar](255) NOT NULL,
 CONSTRAINT [PK_#__sdi_searchcriteriafilter_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_accessscope] (
	[id] [bigint] IDENTITY(4,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_accessscope_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_addresstype] (
	[id] [bigint] IDENTITY(4,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_addresstype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_authenticationconnector] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[authenticationlevel_id] [bigint] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_authenticationconnector_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_authenticationlevel] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_authenticationlevel_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_country] (
	[id] [bigint] IDENTITY(242,1) NOT NULL,
	[ordering] [bigint] NOT NULL,
	[state] [int] NOT NULL,
	[name] [nvarchar](100) NOT NULL,
	[iso2] [nvarchar](2) NULL,
	[iso3] [nvarchar](3) NULL,
 CONSTRAINT [PK_#__sdi_sys_country_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_criteriatype] (
	[id] [bigint] IDENTITY(4,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_criteriatype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_entity] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_entity_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_exceptionlevel] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](255) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_exceptionlevel_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_importtype] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_importtype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_isolanguage] (
	[id] [bigint] IDENTITY(4,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_isolanguage_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_loglevel] (
	[id] [bigint] IDENTITY(9,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](255) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_loglevel_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_logroll] (
	[id] [bigint] IDENTITY(5,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](255) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_logroll_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_maptool] (
	[id] [bigint] IDENTITY(21,1) NOT NULL,
	[alias] [nvarchar](20) NOT NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[name] [nvarchar](255) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_maptool_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_metadatastate] (
	[id] [bigint] IDENTITY(6,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_metadatastate_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_metadataversion] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_metadataversion_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_operationcompliance] (
	[id] [bigint] IDENTITY(50,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[servicecompliance_id] [bigint] NOT NULL,
	[serviceoperation_id] [bigint] NOT NULL,
	[implemented] [smallint] NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_operationcompliance_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_orderstate] (
	[id] [bigint] IDENTITY(8,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_orderstate_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_ordertype] (
	[id] [bigint] IDENTITY(4,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_ordertype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_perimetertype] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_perimetertype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_pricing] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_pricing_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];

SET ANSI_NULLS ON;

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
    [fixed_fee] [decimal](19,2),
    [surface_rate] [decimal](19,2),
    [min_fee] [decimal](19,2),
    [max_fee] [decimal](19,2),
    [apply_vat] [smallint] NOT NULL DEFAULT 1,
CONSTRAINT [PK_#__sdi_pricing_profile] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_order] (
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
    [cfg_vat] [decimal](19,2) NOT NULL DEFAULT 0,
    [cfg_currency] [char](3) NOT NULL DEFAULT 'CHF',
    [cfg_rounding] [decimal](3,2) NOT NULL DEFAULT '0.05',
    [cfg_overall_default_fee_te] [decimal](19,2) NOT NULL DEFAULT 0,
    [cfg_fee_apply_vat] [smallint] NOT NULL DEFAULT 1,
    [cfg_free_data_fee] [smallint] DEFAULT 0,
    [cal_total_amount_ti] [decimal](19,2),
    [cal_fee_ti] [decimal](19,2) NOT NULL DEFAULT 0,
    [ind_lbl_category_order_fee] [nvarchar](255),
CONSTRAINT [PK_#__sdi_pricing_order] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_order_supplier] (
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
    [pricing_order_id] [int] NOT NULL,
    [supplier_id] [int] NOT NULL,
    [supplier_name] [nvarchar](255) NOT NULL,
    [cfg_internal_free] [smallint] NOT NULL DEFAULT 1,
    [cfg_fixed_fee_te] [decimal](19,2) NOT NULL DEFAULT 0,
    [cfg_fixed_fee_apply_vat] [smallint] NOT NULL DEFAULT 1,
    [cfg_data_free_fixed_fee] [smallint] NOT NULL DEFAULT 0,
    [cal_total_rebate_ti] [decimal](19,2) NOT NULL DEFAULT 0,
    [cal_fee_ti] decimal(19,2) NOT NULL DEFAULT 0,
    [cal_total_amount_ti] [decimal](19,2),
CONSTRAINT [PK_#__sdi_pricing_order_supplier] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_order_supplier_product] (
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
    [pricing_order_supplier_id] [int] not null,
    [product_id] [int] not null,
    [pricing_id] [int] not null,
    [cfg_pct_category_supplier_discount] [decimal](19,2) NOT NULL DEFAULT 0,
    [ind_lbl_category_supplier_discount] [nvarchar](255),
    [cal_amount_data_te] [decimal](19,2),
    [cal_total_amount_te] [decimal](19,2),
    [cal_total_amount_ti] [decimal](19,2),
    [cal_total_rebate_ti] [decimal](19,2) NOT NULL DEFAULT 0,
CONSTRAINT [PK_#__sdi_pricing_order_supplier_product] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_order_supplier_product_profile] (
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
    [pricing_order_supplier_product_id] [int](11) not null,
    [pricing_profile_id] [int](11) null,
    [pricing_profile_name] [nvarchar](255) not null,
    [cfg_fixed_fee_te] [decimal](19,2) NOT NULL DEFAULT 0,
    [cfg_apply_vat] [smallint] NOT NULL DEFAULT 1,
    [cfg_surface_rate] [decimal](19,2) NOT NULL DEFAULT 0,
    [cfg_min_fee] [decimal](19,2) NOT NULL DEFAULT 0,
    [cfg_max_fee] [decimal](19,2) NOT NULL DEFAULT 0,
    [cfg_pct_category_profile_discount] [decimal](19,2) NOT NULL DEFAULT 0,
    [ind_lbl_category_profile_discount] [nvarchar](255),
CONSTRAINT [PK_#__sdi_pricing_order_supplier_product_profile] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
)ON [PRIMARY];

SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_pricing_profile_category_pricing_rebate] (
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    [pricing_profile_id] [bigint] NOT NULL,
    [category_id] [bigint] NOT NULL,
    [rebate] [decimal](19,2) default 100,
CONSTRAINT [PK_#__sdi_pricing_profile_category_pricing_rebate] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];

SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_productmining] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_productmining_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_productstate] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_productstate_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_extractstorage] (
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

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_productstorage] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_productstorage_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_propertytype] (
	[id] [bigint] IDENTITY(7,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](255) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_propertytype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_proxytype] (
	[id] [bigint] IDENTITY(4,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](255) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_proxytype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_relationscope] (
	[id] [bigint] IDENTITY(4,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_relationscope_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_relationtype] (
	[id] [bigint] IDENTITY(5,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_relationtype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_rendertype] (
	[id] [bigint] IDENTITY(7,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_rendertype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_rendertype_criteriatype] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[criteriatype_id] [bigint] NOT NULL,
	[rendertype_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_rendertype_criteriatype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_rendertype_stereotype] (
	[id] [bigint] IDENTITY(21,1) NOT NULL,
	[stereotype_id] [bigint] NOT NULL,
	[rendertype_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_rendertype_stereotype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_role] (
	[id] [bigint] IDENTITY(9,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_role_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_searchtab] (
	[id] [bigint] IDENTITY(5,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_searchtab_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_servicecompliance] (
	[id] [bigint] IDENTITY(11,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[serviceconnector_id] [bigint] NOT NULL,
	[serviceversion_id] [bigint] NOT NULL,
	[implemented] [smallint] NOT NULL,
	[relayable] [smallint] NOT NULL,
	[aggregatable] [smallint] NOT NULL,
	[harvestable] [smallint] NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_servicecompliance_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_servicecon_authenticationcon] (
	[id] [bigint] IDENTITY(12,1) NOT NULL,
	[serviceconnector_id] [bigint] NOT NULL,
	[authenticationconnector_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_servicecon_authenticationcon_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_serviceconnector] (
	[id] [bigint] IDENTITY(15,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_serviceconnector_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_serviceoperation] (
	[id] [bigint] IDENTITY(22,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_serviceoperation_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_servicescope] (
	[id] [bigint] IDENTITY(4,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_servicescope_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_servicetype] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_servicetype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_serviceversion] (
	[id] [bigint] IDENTITY(9,1) NOT NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_serviceversion_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_spatialoperator] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_spatialoperator_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_stereotype] (
	[id] [bigint] IDENTITY(16,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](255) NOT NULL,
	[defaultpattern] [nvarchar](255) NULL,
	[isocode] [nvarchar](255) NULL,
	[namespace_id] [bigint] NULL,
	[entity_id] [bigint] NULL,
 CONSTRAINT [PK_#__sdi_sys_stereotype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_topiccategory] (
	[id] [bigint] IDENTITY(20,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_topiccategory_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_unit] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_unit_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_sys_versiontype] (
	[id] [bigint] IDENTITY(3,1) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[value] [nvarchar](150) NOT NULL,
 CONSTRAINT [PK_#__sdi_sys_versiontype_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_tilematrix_policy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[tilematrixsetpolicy_id] [bigint] NOT NULL,
	[identifier] [nvarchar](255) NOT NULL,
	[tileminrow] [int] NULL,
	[tilemaxrow] [int] NULL,
	[tilemincol] [int] NULL,
	[tilemaxcol] [int] NULL,
	[anytile] [smallint] NOT NULL,
 CONSTRAINT [PK_#__sdi_tilematrix_policy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_tilematrixset_policy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[wmtslayerpolicy_id] [bigint] NOT NULL,
	[identifier] [nvarchar](255) NOT NULL,
	[anytilematrix] [smallint] NOT NULL,
	[srssource] [nvarchar](255) NULL,
 CONSTRAINT [PK_#__sdi_tilematrixset_policy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_translation] (
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
	[element_guid] [nvarchar](36) NOT NULL,
	[language_id] [bigint] NULL,
	[text1] [nvarchar](255) NULL,
	[text2] [nvarchar](500) NULL,
	[text3] [nvarchar](255) NULL,
 CONSTRAINT [PK_#__sdi_translation_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_user] (
	[id] [bigint] IDENTITY(2,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[user_id] [int] NOT NULL,
	[description] [nvarchar](max) NULL,
	[notificationrequesttreatment] [smallint] NOT NULL,
	[catid] [int] NULL,
	[params] [nvarchar](1024) NULL,
	[access] [int] NOT NULL,
	[asset_id] [int] NULL,
 CONSTRAINT [PK_#__sdi_user_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_user_role_organism] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NULL,
	[role_id] [bigint] NULL,
	[organism_id] [bigint] NULL,
 CONSTRAINT [PK_#__sdi_user_role_organism_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_user_role_resource] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[user_id] [bigint] NULL,
	[role_id] [bigint] NULL,
	[resource_id] [bigint] NULL,
 CONSTRAINT [PK_#__sdi_user_role_resource_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_version] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NOT NULL,
	[state] [smallint] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[resource_id] [bigint] NOT NULL,
	[access] [int] NOT NULL,
	[asset_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_version_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_versionlink] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[parent_id] [bigint] NOT NULL,
	[child_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_versionlink_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_virtual_physical] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[virtualservice_id] [bigint] NOT NULL,
	[physicalservice_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_virtual_physical_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_virtualmetadata] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[ordering] [int] NOT NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[title] [nvarchar](255) NULL,
	[inheritedtitle] [smallint] NOT NULL,
	[summary] [nvarchar](255) NULL,
	[inheritedsummary] [smallint] NOT NULL,
	[keyword] [nvarchar](255) NULL,
	[inheritedkeyword] [smallint] NOT NULL,
	[fee] [nvarchar](255) NULL,
	[inheritedfee] [smallint] NOT NULL,
	[accessconstraint] [nvarchar](255) NULL,
	[inheritedaccessconstraint] [smallint] NOT NULL,
	[inheritedcontact] [smallint] NOT NULL,
	[contactorganization] [nvarchar](255) NULL,
	[contactname] [nvarchar](255) NULL,
	[contactposition] [nvarchar](255) NULL,
	[contactaddress] [nvarchar](255) NULL,
	[contactaddresstype] [nvarchar](255) NULL,
	[contactrole] [nvarchar](255) NULL,
	[contactpostalcode] [nvarchar](255) NULL,
	[contactlocality] [nvarchar](255) NULL,
	[contactstate] [nvarchar](255) NULL,
	[country_id] [bigint] NULL,
	[contactphone] [nvarchar](255) NULL,
	[contactfax] [nvarchar](255) NULL,
	[contactemail] [nvarchar](255) NULL,
	[contacturl] [nvarchar](255) NULL,
	[contactavailability] [nvarchar](255) NULL,
	[contactinstruction] [nvarchar](255) NULL,
	[virtualservice_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_virtualmetadata_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_virtualservice] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[name] [nvarchar](255) NOT NULL,
	[alias] [nvarchar](20) NOT NULL,
	[servicescope_id] [bigint] NOT NULL,
	[url] [nvarchar](500) NULL,
	[serviceconnector_id] [bigint] NOT NULL,
	[reflectedurl] [nvarchar](255) NULL,
	[reflectedmetadata] [smallint] NOT NULL,
	[xsltfilename] [nvarchar](255) NULL,
	[logpath] [nvarchar](255) NOT NULL,
	[harvester] [smallint] NOT NULL,
	[maximumrecords] [int] NULL,
	[identifiersearchattribute] [nvarchar](255) NULL,
	[proxytype_id] [bigint] NOT NULL,
	[exceptionlevel_id] [bigint] NOT NULL,
	[loglevel_id] [bigint] NOT NULL,
	[logroll_id] [bigint] NOT NULL,
	[params] [nvarchar](1024) NULL,
	[access] [int] NOT NULL,
	[asset_id] [int] NULL,
 CONSTRAINT [PK_#__sdi_virtualservice_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_virtualservice_organism] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[virtualservice_id] [bigint] NOT NULL,
	[organism_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_virtualservice_organism_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_virtualservice_servicecompliance] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[service_id] [bigint] NOT NULL,
	[servicecompliance_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_virtualservice_servicecompliance_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_visualization] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[guid] [nvarchar](36) NOT NULL,
	[alias] [nvarchar](50) NOT NULL,
	[created] [datetime2](0) NOT NULL,
	[created_by] [int] NOT NULL,
	[modified_by] [int] NULL,
	[modified] [datetime2](0) NULL,
	[ordering] [int] NULL,
	[state] [int] NOT NULL,
	[checked_out] [int] NOT NULL,
	[checked_out_time] [datetime2](0) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[version_id] [bigint] NOT NULL,
	[accessscope_id] [bigint] NOT NULL,
	[maplayer_id] [bigint] NULL,
	[access] [int] NOT NULL,
	[asset_id] [int] NULL,
 CONSTRAINT [PK_#__sdi_visualization_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [#__sdi_visualization$alias] UNIQUE NONCLUSTERED 
(
	[alias] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_wfs_spatialpolicy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[localgeographicfilter] [nvarchar](max) NULL,
	[remotegeographicfilter] [nvarchar](max) NULL,
 CONSTRAINT [PK_#__sdi_wfs_spatialpolicy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_wms_spatialpolicy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[maxx] [decimal](18, 6) NULL,
	[maxy] [decimal](18, 6) NULL,
	[minx] [decimal](18, 6) NULL,
	[miny] [decimal](18, 6) NULL,
	[geographicfilter] [nvarchar](max) NULL,
	[maximumscale] [int] NULL,
	[minimumscale] [int] NULL,
	[srssource] [nvarchar](255) NULL,
 CONSTRAINT [PK_#__sdi_wms_spatialpolicy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_wmslayer_policy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](255) NOT NULL,
	[description] [nvarchar](255) NOT NULL,
	[enabled] [smallint] NOT NULL,
	[inheritedspatialpolicy] [smallint] NOT NULL,
	[spatialpolicy_id] [bigint] NULL,
	[physicalservicepolicy_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_wmslayer_policy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_wmts_spatialpolicy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[spatialoperator_id] [bigint] NOT NULL,
	[eastboundlongitude] [decimal](10, 6) NULL,
	[westboundlongitude] [decimal](10, 6) NULL,
	[northboundlatitude] [decimal](10, 6) NULL,
	[southboundlatitude] [decimal](10, 6) NULL,
 CONSTRAINT [PK_#__sdi_wmts_spatialpolicy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_wmtslayer_policy] (
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[identifier] [nvarchar](255) NOT NULL,
	[enabled] [smallint] NOT NULL,
	[inheritedspatialpolicy] [smallint] NOT NULL,
	[spatialpolicy_id] [bigint] NULL,
	[anytilematrixset] [smallint] NOT NULL,
	[physicalservicepolicy_id] [bigint] NOT NULL,
 CONSTRAINT [PK_#__sdi_wmtslayer_policy_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [#__sdi_organism_category_pricing_rebate] (
    [id] [bigint] IDENTITY(1,1) NOT NULL,
    [organism_id] [bigint],
    [category_id] [bigint],
    [rebate] [decimal](19,2),
CONSTRAINT [PK_#__sdi_organism_category_pricing_rebate] PRIMARY KEY CLUSTERED
(
    [id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];

SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [last_ids] (
	[TABLE_NAME] [nvarchar](255) NOT NULL,
	[LAST_ID] [int] NOT NULL,
 CONSTRAINT [PK_last_ids_TABLE_NAME] PRIMARY KEY CLUSTERED 
(
	[TABLE_NAME] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

SET ANSI_PADDING ON;

CREATE TABLE [last_query_results] (
	[ID_LAST_QUERY_RESULT] [bigint] NOT NULL,
	[ID_QUERY] [bigint] NOT NULL,
	[DATA] [varbinary](max) NULL,
	[XML_RESULT] [nvarchar](max) NULL,
	[TEXT_RESULT] [nvarchar](max) NULL,
	[PICTURE_URL] [nvarchar](1000) NULL,
	[CONTENT_TYPE] [nvarchar](100) NULL,
 CONSTRAINT [PK_last_query_results_ID_LAST_QUERY_RESULT] PRIMARY KEY CLUSTERED 
(
	[ID_LAST_QUERY_RESULT] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];

SET ANSI_PADDING OFF;


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [log_entries] (
	[ID_LOG_ENTRY] [bigint] NOT NULL,
	[ID_QUERY] [bigint] NOT NULL,
	[REQUEST_TIME] [datetime2](0) NOT NULL,
	[RESPONSE_DELAY] [real] NOT NULL,
	[MESSAGE] [nvarchar](max) NOT NULL,
	[ID_STATUS] [bigint] NOT NULL,
	[HTTP_CODE] [bigint] NULL,
	[EXCEPTION_CODE] [nvarchar](100) NULL,
	[RESPONSE_SIZE] [real] NULL,
 CONSTRAINT [PK_log_entries_ID_LOG_ENTRY] PRIMARY KEY CLUSTERED 
(
	[ID_LOG_ENTRY] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [overview_page] (
	[ID_OVERVIEW_PAGE] [bigint] NOT NULL,
	[NAME] [nvarchar](255) NOT NULL,
	[IS_PUBLIC] [smallint] NOT NULL,
 CONSTRAINT [PK_overview_page_ID_OVERVIEW_PAGE] PRIMARY KEY CLUSTERED 
(
	[ID_OVERVIEW_PAGE] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY],
 CONSTRAINT [overview_page$URL_UNIQUE] UNIQUE NONCLUSTERED 
(
	[NAME] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [overview_queries] (
	[ID_OVERVIEW_QUERY] [bigint] NOT NULL,
	[ID_OVERVIEW_PAGE] [bigint] NOT NULL,
	[ID_QUERY] [bigint] NOT NULL,
 CONSTRAINT [PK_overview_queries_ID_OVERVIEW_QUERY] PRIMARY KEY CLUSTERED 
(
	[ID_OVERVIEW_QUERY] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [periods] (
	[ID_PERIODS] [bigint] NOT NULL,
	[ID_SLA] [bigint] NOT NULL,
	[NAME] [nvarchar](45) NULL,
	[MONDAY] [smallint] NULL,
	[TUESDAY] [smallint] NULL,
	[WEDNESDAY] [smallint] NULL,
	[THURSDAY] [smallint] NULL,
	[FRIDAY] [smallint] NULL,
	[SATURDAY] [smallint] NULL,
	[SUNDAY] [smallint] NULL,
	[HOLIDAYS] [smallint] NULL,
	[SLA_START_TIME] [time](7) NOT NULL,
	[SLA_END_TIME] [time](7) NOT NULL,
	[INCLUDE] [smallint] NULL,
	[DATE] [nvarchar](45) NULL,
 CONSTRAINT [PK_periods_ID_PERIODS] PRIMARY KEY CLUSTERED 
(
	[ID_PERIODS] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [queries] (
	[ID_QUERY] [bigint] NOT NULL,
	[ID_JOB] [bigint] NOT NULL,
	[ID_SERVICE_METHOD] [bigint] NOT NULL,
	[ID_STATUS] [bigint] NOT NULL,
	[NAME] [nvarchar](45) NOT NULL,
	[SOAP_URL] [nvarchar](250) NULL,
 CONSTRAINT [PK_queries_ID_QUERY] PRIMARY KEY CLUSTERED 
(
	[ID_QUERY] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [query_agg_hour_log_entries] (
	[DATE_LOG] [datetime2](0) NOT NULL,
	[ID_QUERY] [bigint] NOT NULL,
	[H1_MEAN_RESP_TIME] [real] NOT NULL,
	[H1_MEAN_RESP_TIME_INSPIRE] [real] NOT NULL,
	[H1_AVAILABILITY] [real] NOT NULL,
	[H1_AVAILABILITY_INSPIRE] [real] NOT NULL,
	[H1_NB_BIZ_ERRORS] [bigint] NOT NULL,
	[H1_NB_BIZ_ERRORS_INSPIRE] [bigint] NOT NULL,
	[H1_NB_CONN_ERRORS] [bigint] NOT NULL,
	[H1_NB_CONN_ERRORS_INSPIRE] [bigint] NOT NULL,
	[H1_MAX_RESP_TIME] [real] NOT NULL,
	[H1_MIN_RESP_TIME] [real] NOT NULL,
	[H1_MAX_RESP_TIME_INSPIRE] [real] NOT NULL,
	[H1_MIN_RESP_TIME_INSPIRE] [real] NOT NULL,
	[H1_UNAVAILABILITY] [real] NOT NULL,
	[H1_UNAVAILABILITY_INSPIRE] [real] NOT NULL,
	[H1_FAILURE] [real] NOT NULL,
	[H1_FAILURE_INSPIRE] [real] NOT NULL,
	[H1_UNTESTED] [real] NOT NULL,
	[H1_UNTESTED_INSPIRE] [real] NOT NULL,
 CONSTRAINT [PK_query_agg_hour_log_entries_DATE_LOG] PRIMARY KEY CLUSTERED 
(
	[DATE_LOG] ASC,
	[ID_QUERY] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [query_agg_log_entries] (
	[DATE_LOG] [datetime2](0) NOT NULL,
	[ID_QUERY] [bigint] NOT NULL,
	[SLA_MEAN_RESP_TIME] [real] NOT NULL,
	[H24_MEAN_RESP_TIME] [real] NOT NULL,
	[SLA_AVAILABILITY] [real] NOT NULL,
	[H24_AVAILABILITY] [real] NOT NULL,
	[SLA_NB_BIZ_ERRORS] [bigint] NOT NULL,
	[H24_NB_BIZ_ERRORS] [bigint] NOT NULL,
	[SLA_NB_CONN_ERRORS] [bigint] NOT NULL,
	[H24_NB_CONN_ERRORS] [bigint] NOT NULL,
	[H24_MAX_RESP_TIME] [real] NOT NULL,
	[H24_MIN_RESP_TIME] [real] NOT NULL,
	[SLA_MAX_RESP_TIME] [real] NOT NULL,
	[SLA_MIN_RESP_TIME] [real] NOT NULL,
	[SLA_UNAVAILABILITY] [real] NOT NULL,
	[H24_UNAVAILABILITY] [real] NOT NULL,
	[SLA_FAILURE] [real] NOT NULL,
	[H24_FAILURE] [real] NOT NULL,
	[SLA_UNTESTED] [real] NOT NULL,
	[H24_UNTESTED] [real] NOT NULL,
 CONSTRAINT [PK_query_agg_log_entries_DATE_LOG] PRIMARY KEY CLUSTERED 
(
	[DATE_LOG] ASC,
	[ID_QUERY] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [query_params] (
	[ID_QUERY] [bigint] NOT NULL,
	[NAME] [nvarchar](45) NOT NULL,
	[VALUE] [nvarchar](max) NULL,
 CONSTRAINT [PK_query_params_ID_QUERY] PRIMARY KEY CLUSTERED 
(
	[ID_QUERY] ASC,
	[NAME] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

SET ANSI_PADDING ON;

CREATE TABLE [query_validation_results] (
	[ID_QUERY_VALIDATION_RESULT] [int] NOT NULL,
	[ID_QUERY] [bigint] NOT NULL,
	[SIZE_VALIDATION_RESULT] [smallint] NULL,
	[RESPONSE_SIZE] [real] NULL,
	[TIME_VALIDATION_RESULT] [smallint] NULL,
	[DELIVERY_TIME] [real] NULL,
	[XPATH_VALIDATION_RESULT] [smallint] NULL,
	[XPATH_VALIDATION_OUTPUT] [varchar](1000) NULL,
 CONSTRAINT [PK_query_validation_results_ID_QUERY_VALIDATION_RESULT] PRIMARY KEY CLUSTERED 
(
	[ID_QUERY_VALIDATION_RESULT] ASC,
	[ID_QUERY] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];

SET ANSI_PADDING OFF;


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

SET ANSI_PADDING ON;

CREATE TABLE [query_validation_settings] (
	[ID_QUERY_VALIDATION_SETTINGS] [int] NOT NULL,
	[ID_QUERY] [bigint] NOT NULL,
	[USE_SIZE_VALIDATION] [smallint] NOT NULL,
	[NORM_SIZE] [real] NULL,
	[NORM_SIZE_TOLERANCE] [real] NULL,
	[USE_TIME_VALIDATION] [smallint] NOT NULL,
	[NORM_TIME] [real] NULL,
	[USE_XPATH_VALIDATION] [smallint] NOT NULL,
	[XPATH_EXPRESSION] [varchar](1000) NULL,
	[XPATH_EXPECTED_OUTPUT] [varchar](1000) NULL,
 CONSTRAINT [PK_query_validation_settings_ID_QUERY_VALIDATION_SETTINGS] PRIMARY KEY CLUSTERED 
(
	[ID_QUERY_VALIDATION_SETTINGS] ASC,
	[ID_QUERY] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];

SET ANSI_PADDING OFF;


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [roles] (
	[ID_ROLE] [bigint] NOT NULL,
	[NAME] [nvarchar](45) NOT NULL,
	[RANK] [bigint] NOT NULL,
 CONSTRAINT [PK_roles_ID_ROLE] PRIMARY KEY CLUSTERED 
(
	[ID_ROLE] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [service_methods] (
	[ID_SERVICE_METHOD] [bigint] NOT NULL,
	[NAME] [nvarchar](45) NOT NULL,
 CONSTRAINT [PK_service_methods_ID_SERVICE_METHOD] PRIMARY KEY CLUSTERED 
(
	[ID_SERVICE_METHOD] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [service_types] (
	[ID_SERVICE_TYPE] [bigint] NOT NULL,
	[NAME] [nvarchar](20) NOT NULL,
	[VERSION] [nvarchar](10) NOT NULL,
 CONSTRAINT [PK_service_types_ID_SERVICE_TYPE] PRIMARY KEY CLUSTERED 
(
	[ID_SERVICE_TYPE] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [service_types_methods] (
	[ID_SERVICE_TYPE] [bigint] NOT NULL,
	[ID_SERVICE_METHOD] [bigint] NOT NULL,
 CONSTRAINT [PK_service_types_methods_ID_SERVICE_TYPE] PRIMARY KEY CLUSTERED 
(
	[ID_SERVICE_TYPE] ASC,
	[ID_SERVICE_METHOD] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [sla] (
	[ID_SLA] [bigint] NOT NULL,
	[NAME] [nvarchar](45) NOT NULL,
	[EXCLUDE_WORST] [smallint] NULL,
	[MEASURE_TIME_TO_FIRST] [smallint] NULL,
 CONSTRAINT [PK_sla_ID_SLA] PRIMARY KEY CLUSTERED 
(
	[ID_SLA] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [statuses] (
	[ID_STATUS] [bigint] NOT NULL,
	[NAME] [nvarchar](45) NOT NULL,
 CONSTRAINT [PK_statuses_ID_STATUS] PRIMARY KEY CLUSTERED 
(
	[ID_STATUS] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];


SET ANSI_NULLS ON;

SET QUOTED_IDENTIFIER ON;

CREATE TABLE [users] (
	[LOGIN] [nvarchar](45) NOT NULL,
	[PASSWORD] [nvarchar](45) NOT NULL,
	[ID_ROLE] [bigint] NULL,
	[EXPIRATION] [date] NULL,
	[ENABLED] [smallint] NOT NULL,
	[LOCKED] [smallint] NOT NULL,
 CONSTRAINT [PK_users_LOGIN] PRIMARY KEY CLUSTERED 
(
	[LOGIN] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY];

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
  [sdi_user_id] [bigint] IDENTITY(12,1) NOT NULL,
) SET ANSI_NULLS ON;

CREATE TABLE IF NOT EXISTS [#__sdi_processing_order] (
  [id] [bigint] IDENTITY(12,1) NOT NULL,
  [guid] [nvarchar](255) NOT NULL,
  `name` [nvarchar](255) NOT NULL,
  `user_id` [int] NOT NULL,
  `processing_id`[int] NOT NULL,
  `parameters` [varchar](1000) NOT NULL,
  `filestorage` [nvarchar](20) NOT NULL,
  `file` [varchar](1000),
  `fileurl` [varchar](1000) NOT NULL,
  `output` [varchar](1000),
  `outputpreview` [varchar](1000) NOT NULL,
  `exec_pid` [nvarchar](255) NOT NULL,
  `status` [int] NOT NULL,
  `info` [varchar](1000),
  `created_by` [int] NULL,
  `created` [nvarchar](45) NOT NULL,
  `modified_by` [int] NOT NULL,
  `modified` [nvarchar](45) NULL,
  `sent` [nvarchar](45) NULL,
    CONSTRAINT [PK_#__sdi_processing_order] PRIMARY KEY CLUSTERED (
            [id] ASC
    )WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
    ) ON [PRIMARY];
    SET ANSI_NULLS ON;


ALTER TABLE [job_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_MAX_RESP_TIME];

ALTER TABLE [job_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_MIN_RESP_TIME];

ALTER TABLE [job_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_MAX_RESP_TIME_INSPIRE];

ALTER TABLE [job_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_MIN_RESP_TIME_INSPIRE];

ALTER TABLE [job_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_UNAVAILABILITY];

ALTER TABLE [job_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_UNAVAILABILITY_INSPIRE];

ALTER TABLE [job_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_FAILURE];

ALTER TABLE [job_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_FAILURE_INSPIRE];

ALTER TABLE [job_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_UNTESTED];

ALTER TABLE [job_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_UNTESTED_INSPIRE];

ALTER TABLE [job_agg_log_entries] ADD  DEFAULT '0' FOR [H24_MAX_RESP_TIME];

ALTER TABLE [job_agg_log_entries] ADD  DEFAULT '0' FOR [H24_MIN_RESP_TIME];

ALTER TABLE [job_agg_log_entries] ADD  DEFAULT '0' FOR [SLA_MAX_RESP_TIME];

ALTER TABLE [job_agg_log_entries] ADD  DEFAULT '0' FOR [SLA_MIN_RESP_TIME];

ALTER TABLE [job_agg_log_entries] ADD  DEFAULT '0' FOR [SLA_UNAVAILABILITY];

ALTER TABLE [job_agg_log_entries] ADD  DEFAULT '0' FOR [H24_UNAVAILABILITY];

ALTER TABLE [job_agg_log_entries] ADD  DEFAULT '0' FOR [SLA_FAILURE];

ALTER TABLE [job_agg_log_entries] ADD  DEFAULT '0' FOR [H24_FAILURE];

ALTER TABLE [job_agg_log_entries] ADD  DEFAULT '0' FOR [SLA_UNTESTED];

ALTER TABLE [job_agg_log_entries] ADD  DEFAULT '0' FOR [H24_UNTESTED];

ALTER TABLE [jobs] ADD  DEFAULT '0' FOR [BUSINESS_ERRORS];

ALTER TABLE [jobs] ADD  DEFAULT '0' FOR [IS_PUBLIC];

ALTER TABLE [jobs] ADD  DEFAULT '0' FOR [IS_AUTOMATIC];

ALTER TABLE [jobs] ADD  DEFAULT '0' FOR [ALLOWS_REALTIME];

ALTER TABLE [jobs] ADD  DEFAULT '0' FOR [TRIGGERS_ALERTS];

ALTER TABLE [jobs] ADD  DEFAULT '4' FOR [ID_STATUS];

ALTER TABLE [jobs] ADD  DEFAULT '0' FOR [HTTP_ERRORS];

ALTER TABLE [jobs] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [STATUS_UPDATE_TIME];

ALTER TABLE [jobs] ADD  DEFAULT '0' FOR [SAVE_RESPONSE];

ALTER TABLE [jobs] ADD  DEFAULT '0' FOR [RUN_SIMULTANEOUS];

ALTER TABLE [#__sdi_address] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_address] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_address] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_address] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_address] ADD  DEFAULT '1' FOR [sameascontact];

ALTER TABLE [#__sdi_application] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_application] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_application] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_attribute] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_attribute] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_attribute] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_attribute] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_attribute] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_attribute] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_attributevalue] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_attributevalue] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_attributevalue] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_attributevalue] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_attributevalue] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_attributevalue] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_boundary] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_boundary] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_boundary] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_boundary] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_boundary] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_boundary] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_boundarycategory] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_boundarycategory] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_boundarycategory] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_boundarycategory] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_boundarycategory] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_boundarycategory] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_catalog] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_catalog] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_catalog] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_catalog] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_catalog] ADD  DEFAULT '0' FOR [oninitrunsearch];

ALTER TABLE [#__sdi_catalog] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_catalog] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_catalog_resourcetype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_catalog_searchcriteria] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_catalog_searchcriteria] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_catalog_searchsort] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_category] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_category] ADD  DEFAULT '0' FOR [backend_only];

ALTER TABLE [#__sdi_class] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_class] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_class] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_class] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_class] ADD  DEFAULT '0' FOR [issystem];

ALTER TABLE [#__sdi_class] ADD  DEFAULT '0' FOR [isrootclass];

ALTER TABLE [#__sdi_class] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_class] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_diffusion] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_diffusion] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_diffusion] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_diffusion] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_diffusion] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_diffusion] ADD  DEFAULT '0' FOR [hasdownload];

ALTER TABLE [#__sdi_diffusion] ADD  DEFAULT '0' FOR [hasextraction];

ALTER TABLE [#__sdi_diffusion] ADD  DEFAULT '0' FOR [restrictedperimeter];

ALTER TABLE [#__sdi_diffusion] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_diffusion] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_diffusion_download] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [executed];

ALTER TABLE [#__sdi_diffusion_notifieduser] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_diffusion_perimeter] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_diffusion_propertyvalue] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_featuretype_policy] ADD  DEFAULT '1' FOR [enabled];

ALTER TABLE [#__sdi_featuretype_policy] ADD  DEFAULT '1' FOR [inheritedspatialpolicy];

ALTER TABLE [#__sdi_importref] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_importref] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_importref] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_importref] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_importref] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_importref] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_language] ADD  DEFAULT '0' FOR [ordering];

ALTER TABLE [#__sdi_language] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_language] ADD  DEFAULT (N'') FOR [gemet];

ALTER TABLE [#__sdi_layer] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_layer] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_layer] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_layergroup] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_layergroup] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_layergroup] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_layergroup] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_layergroup] ADD  DEFAULT '0' FOR [isdefaultopen];

ALTER TABLE [#__sdi_layergroup] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_map] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_map] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_map] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_map] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_map_layergroup] ADD  DEFAULT '0' FOR [isbackground];

ALTER TABLE [#__sdi_map_layergroup] ADD  DEFAULT '0' FOR [isdefault];

ALTER TABLE [#__sdi_maplayer] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_maplayer] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_maplayer] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_maplayer] ADD  DEFAULT '0' FOR [istiled];

ALTER TABLE [#__sdi_maplayer] ADD  DEFAULT '0' FOR [isdefaultvisible];

ALTER TABLE [#__sdi_maplayer] ADD  DEFAULT '1.00' FOR [opacity];

ALTER TABLE [#__sdi_maplayer] ADD  DEFAULT '0' FOR [asOL];

ALTER TABLE [#__sdi_maplayer] ADD  DEFAULT '1' FOR [accessscope_id];

ALTER TABLE [#__sdi_maplayer] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_metadata] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_metadata] ADD  DEFAULT '1' FOR [metadatastate_id];

ALTER TABLE [#__sdi_metadata] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_metadata] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_metadata] ADD  DEFAULT '0' FOR [notification];

ALTER TABLE [#__sdi_metadata] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_metadata] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_namespace] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_namespace] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_namespace] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_namespace] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_namespace] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_namespace] ADD  DEFAULT '0' FOR [system];

ALTER TABLE [#__sdi_namespace] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_namespace] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_order] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_order] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_order] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_order] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_order] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_order] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [sent];

ALTER TABLE [#__sdi_order] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [completed];

ALTER TABLE [#__sdi_order] ADD  DEFAULT '0' FOR [usernotified];

ALTER TABLE [#__sdi_order] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_order] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_order_diffusion] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [completed];

ALTER TABLE [#__sdi_organism] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_organism] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_organism] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_organism] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_organism] ADD  DEFAULT '0'  FOR [asset_id];

ALTER TABLE [#__sdi_perimeter] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_perimeter] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_perimeter] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_perimeter] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_perimeter] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_perimeter] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_perimeter] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_physicalservice] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_physicalservice] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_physicalservice] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_physicalservice] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_physicalservice] ADD  DEFAULT '1' FOR [servicescope_id];

ALTER TABLE [#__sdi_physicalservice] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_physicalservice_policy] ADD  DEFAULT '1' FOR [anyitem];

ALTER TABLE [#__sdi_physicalservice_policy] ADD  DEFAULT '1' FOR [inheritedspatialpolicy];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [allowfrom];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [allowto];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [anyoperation];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [anyservice];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [accessscope_id];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [csw_version_id];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [csw_anyattribute];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [csw_anycontext];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [csw_anystate];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [csw_anyvisibility];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [csw_includeharvested];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [csw_anyresourcetype];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [csw_accessscope_id];

ALTER TABLE [#__sdi_policy] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_profile] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_profile] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_profile] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_profile] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_profile] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_profile] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_property] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_property] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_property] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_property] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_property] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_property] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_property] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_propertyvalue] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_propertyvalue] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_propertyvalue] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_propertyvalue] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_propertyvalue] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_propertyvalue] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_propertyvalue] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_relation] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_relation] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_relation] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_relation] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_relation] ADD  DEFAULT '0' FOR [issearchfilter];

ALTER TABLE [#__sdi_relation] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_relation] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_relation_catalog] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_relation_profile] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_resource] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_resource] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_resource] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_resourcetype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_resourcetype] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_resourcetype] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_resourcetypelink] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_resourcetypelink] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_resourcetypelink] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_searchcriteria] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_searchcriteria] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_searchcriteria] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_searchcriteria] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_searchcriteria] ADD  DEFAULT '0' FOR [issystem];

ALTER TABLE [#__sdi_searchcriteria] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_searchcriteria] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_searchcriteriafilter] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_accessscope] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_accessscope] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_addresstype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_authenticationconnector] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_authenticationlevel] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_country] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_country] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_criteriatype] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_criteriatype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_entity] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_entity] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_exceptionlevel] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_importtype] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_importtype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_isolanguage] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_isolanguage] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_loglevel] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_logroll] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_maptool] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_metadatastate] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_metadatastate] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_metadataversion] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_metadataversion] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_operationcompliance] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_operationcompliance] ADD  DEFAULT '0' FOR [implemented];

ALTER TABLE [#__sdi_sys_orderstate] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_orderstate] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_ordertype] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_ordertype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_perimetertype] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_perimetertype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_pricing] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_pricing] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_productmining] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_productmining] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_productstate] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_productstate] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_productstorage] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_productstorage] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_propertytype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_proxytype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_relationscope] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_relationscope] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_relationtype] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_relationtype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_rendertype] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_rendertype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_role] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_role] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_searchtab] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_searchtab] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_servicecompliance] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_servicecompliance] ADD  DEFAULT '0' FOR [implemented];

ALTER TABLE [#__sdi_sys_servicecompliance] ADD  DEFAULT '0' FOR [relayable];

ALTER TABLE [#__sdi_sys_servicecompliance] ADD  DEFAULT '0' FOR [aggregatable];

ALTER TABLE [#__sdi_sys_servicecompliance] ADD  DEFAULT '0' FOR [harvestable];

ALTER TABLE [#__sdi_sys_serviceconnector] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_serviceoperation] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_servicescope] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_servicescope] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_servicetype] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_servicetype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_serviceversion] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_spatialoperator] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_spatialoperator] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_stereotype] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_stereotype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_topiccategory] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_topiccategory] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_unit] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_sys_versiontype] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_sys_versiontype] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_tilematrix_policy] ADD  DEFAULT '1' FOR [anytile];

ALTER TABLE [#__sdi_tilematrixset_policy] ADD  DEFAULT '1' FOR [anytilematrix];

ALTER TABLE [#__sdi_translation] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_translation] ADD  DEFAULT '1' FOR [ordering];

ALTER TABLE [#__sdi_translation] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_translation] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_translation] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_user] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_user] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_user] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_user] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_user] ADD  DEFAULT '1' FOR [notificationrequesttreatment];

ALTER TABLE [#__sdi_user] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_version] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_version] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_version] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_version] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_version] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_version] ADD  DEFAULT '0' FOR [asset_id];

ALTER TABLE [#__sdi_virtualmetadata] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_virtualmetadata] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_virtualmetadata] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_virtualmetadata] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_virtualmetadata] ADD  DEFAULT '1' FOR [inheritedtitle];

ALTER TABLE [#__sdi_virtualmetadata] ADD  DEFAULT '1' FOR [inheritedsummary];

ALTER TABLE [#__sdi_virtualmetadata] ADD  DEFAULT '1' FOR [inheritedkeyword];

ALTER TABLE [#__sdi_virtualmetadata] ADD  DEFAULT '1' FOR [inheritedfee];

ALTER TABLE [#__sdi_virtualmetadata] ADD  DEFAULT '1' FOR [inheritedaccessconstraint];

ALTER TABLE [#__sdi_virtualmetadata] ADD  DEFAULT '1' FOR [inheritedcontact];

ALTER TABLE [#__sdi_virtualservice] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_virtualservice] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_virtualservice] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_virtualservice] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [created];

ALTER TABLE [#__sdi_virtualservice] ADD  DEFAULT '1' FOR [servicescope_id];

ALTER TABLE [#__sdi_virtualservice] ADD  DEFAULT '0' FOR [reflectedmetadata];

ALTER TABLE [#__sdi_virtualservice] ADD  DEFAULT '0' FOR [harvester];

ALTER TABLE [#__sdi_virtualservice] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_visualization] ADD  DEFAULT '1' FOR [state];

ALTER TABLE [#__sdi_visualization] ADD  DEFAULT '0'  FOR [checked_out];

ALTER TABLE [#__sdi_visualization] ADD  DEFAULT '1900-01-01T00:00:00.000' FOR [checked_out_time];

ALTER TABLE [#__sdi_visualization] ADD  DEFAULT '1' FOR [access];

ALTER TABLE [#__sdi_wmslayer_policy] ADD  DEFAULT '0' FOR [enabled];

ALTER TABLE [#__sdi_wmslayer_policy] ADD  DEFAULT '1' FOR [inheritedspatialpolicy];

ALTER TABLE [#__sdi_wmts_spatialpolicy] ADD  DEFAULT '1' FOR [spatialoperator_id];

ALTER TABLE [#__sdi_wmtslayer_policy] ADD  DEFAULT '1' FOR [enabled];

ALTER TABLE [#__sdi_wmtslayer_policy] ADD  DEFAULT '1' FOR [inheritedspatialpolicy];

ALTER TABLE [#__sdi_wmtslayer_policy] ADD  DEFAULT '1' FOR [anytilematrixset];

ALTER TABLE [last_ids] ADD  DEFAULT '0' FOR [LAST_ID];

ALTER TABLE [overview_page] ADD  DEFAULT '0' FOR [IS_PUBLIC];

ALTER TABLE [periods] ADD  DEFAULT '0' FOR [MONDAY];

ALTER TABLE [periods] ADD  DEFAULT '0' FOR [TUESDAY];

ALTER TABLE [periods] ADD  DEFAULT '0' FOR [WEDNESDAY];

ALTER TABLE [periods] ADD  DEFAULT '0' FOR [THURSDAY];

ALTER TABLE [periods] ADD  DEFAULT '0' FOR [FRIDAY];

ALTER TABLE [periods] ADD  DEFAULT '0' FOR [SATURDAY];

ALTER TABLE [periods] ADD  DEFAULT '0' FOR [SUNDAY];

ALTER TABLE [periods] ADD  DEFAULT '0' FOR [HOLIDAYS];

ALTER TABLE [periods] ADD  DEFAULT '0' FOR [INCLUDE];

ALTER TABLE [queries] ADD  DEFAULT '4' FOR [ID_STATUS];

ALTER TABLE [query_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_MAX_RESP_TIME];

ALTER TABLE [query_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_MIN_RESP_TIME];

ALTER TABLE [query_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_MAX_RESP_TIME_INSPIRE];

ALTER TABLE [query_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_MIN_RESP_TIME_INSPIRE];

ALTER TABLE [query_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_UNAVAILABILITY];

ALTER TABLE [query_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_UNAVAILABILITY_INSPIRE];

ALTER TABLE [query_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_FAILURE];

ALTER TABLE [query_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_FAILURE_INSPIRE];

ALTER TABLE [query_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_UNTESTED];

ALTER TABLE [query_agg_hour_log_entries] ADD  DEFAULT '0' FOR [H1_UNTESTED_INSPIRE];

ALTER TABLE [query_agg_log_entries] ADD  DEFAULT '0' FOR [H24_MAX_RESP_TIME];

ALTER TABLE [query_agg_log_entries] ADD  DEFAULT '0' FOR [H24_MIN_RESP_TIME];

ALTER TABLE [query_agg_log_entries] ADD  DEFAULT '0' FOR [SLA_MAX_RESP_TIME];

ALTER TABLE [query_agg_log_entries] ADD  DEFAULT '0' FOR [SLA_MIN_RESP_TIME];

ALTER TABLE [query_agg_log_entries] ADD  DEFAULT '0' FOR [SLA_UNAVAILABILITY];

ALTER TABLE [query_agg_log_entries] ADD  DEFAULT '0' FOR [H24_UNAVAILABILITY];

ALTER TABLE [query_agg_log_entries] ADD  DEFAULT '0' FOR [SLA_FAILURE];

ALTER TABLE [query_agg_log_entries] ADD  DEFAULT '0' FOR [H24_FAILURE];

ALTER TABLE [query_agg_log_entries] ADD  DEFAULT '0' FOR [SLA_UNTESTED];

ALTER TABLE [query_agg_log_entries] ADD  DEFAULT '0' FOR [H24_UNTESTED];

ALTER TABLE [query_validation_settings] ADD  DEFAULT '0' FOR [USE_SIZE_VALIDATION];

ALTER TABLE [query_validation_settings] ADD  DEFAULT '0' FOR [USE_TIME_VALIDATION];

ALTER TABLE [query_validation_settings] ADD  DEFAULT '0' FOR [USE_XPATH_VALIDATION];

ALTER TABLE [sla] ADD  DEFAULT '0' FOR [EXCLUDE_WORST];

ALTER TABLE [sla] ADD  DEFAULT '0' FOR [MEASURE_TIME_TO_FIRST];

ALTER TABLE [users] ADD  DEFAULT '1' FOR [ENABLED];

ALTER TABLE [users] ADD  DEFAULT '0' FOR [LOCKED];










