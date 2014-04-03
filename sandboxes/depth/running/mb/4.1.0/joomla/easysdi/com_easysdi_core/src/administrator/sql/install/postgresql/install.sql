SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET default_with_oids = false;

CREATE TABLE action_types (
    "ID_ACTION_TYPE" bigint NOT NULL,
    "NAME" character varying(45) NOT NULL
);


CREATE TABLE actions (
    "ID_ACTION" bigint NOT NULL,
    "ID_JOB" bigint NOT NULL,
    "ID_ACTION_TYPE" bigint NOT NULL,
    "TARGET" character varying(255),
    "LANGUAGE" character(2)
);

CREATE TABLE alerts (
    "ID_ALERT" bigint NOT NULL,
    "ID_JOB" bigint NOT NULL,
    "ID_OLD_STATUS" bigint NOT NULL,
    "ID_NEW_STATUS" bigint NOT NULL,
    "CAUSE" text NOT NULL,
    "ALERT_DATE_TIME" timestamp(3) without time zone NOT NULL,
    "EXPOSE_RSS" bit(1) NOT NULL,
    "RESPONSE_DELAY" double precision NOT NULL,
    "HTTP_CODE" bigint,
    "IMAGE" bytea,
    "CONTENT_TYPE" character varying(50)
);


CREATE TABLE holidays (
    "ID_HOLIDAYS" bigint NOT NULL,
    "NAME" character varying(45),
    "DATE" timestamp(3) without time zone NOT NULL
);

CREATE TABLE http_methods (
    "ID_HTTP_METHOD" bigint NOT NULL,
    "NAME" character varying(10) NOT NULL
);

CREATE TABLE job_agg_hour_log_entries (
    "DATE_LOG" timestamp(3) without time zone NOT NULL,
    "ID_JOB" bigint NOT NULL,
    "H1_MEAN_RESP_TIME" double precision NOT NULL,
    "H1_MEAN_RESP_TIME_INSPIRE" double precision NOT NULL,
    "H1_AVAILABILITY" double precision NOT NULL,
    "H1_AVAILABILITY_INSPIRE" double precision NOT NULL,
    "H1_NB_BIZ_ERRORS" bigint NOT NULL,
    "H1_NB_BIZ_ERRORS_INSPIRE" bigint NOT NULL,
    "H1_NB_CONN_ERRORS" bigint NOT NULL,
    "H1_NB_CONN_ERRORS_INSPIRE" bigint NOT NULL,
    "H1_MAX_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "H1_MIN_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "H1_MAX_RESP_TIME_INSPIRE" double precision DEFAULT 0::double precision NOT NULL,
    "H1_MIN_RESP_TIME_INSPIRE" double precision DEFAULT 0::double precision NOT NULL,
    "H1_UNAVAILABILITY" double precision DEFAULT 0::double precision NOT NULL,
    "H1_UNAVAILABILITY_INSPIRE" double precision DEFAULT 0::double precision NOT NULL,
    "H1_FAILURE" double precision DEFAULT 0::double precision NOT NULL,
    "H1_FAILURE_INSPIRE" double precision DEFAULT 0::double precision NOT NULL,
    "H1_UNTESTED" double precision DEFAULT 0::double precision NOT NULL,
    "H1_UNTESTED_INSPIRE" double precision DEFAULT 0::double precision NOT NULL
);


CREATE TABLE job_agg_log_entries (
    "DATE_LOG" timestamp(3) without time zone NOT NULL,
    "ID_JOB" bigint NOT NULL,
    "SLA_MEAN_RESP_TIME" double precision NOT NULL,
    "H24_MEAN_RESP_TIME" double precision NOT NULL,
    "SLA_AVAILABILITY" double precision NOT NULL,
    "H24_AVAILABILITY" double precision NOT NULL,
    "SLA_NB_BIZ_ERRORS" bigint NOT NULL,
    "H24_NB_BIZ_ERRORS" bigint NOT NULL,
    "SLA_NB_CONN_ERRORS" bigint NOT NULL,
    "H24_NB_CONN_ERRORS" bigint NOT NULL,
    "H24_MAX_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "H24_MIN_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "SLA_MAX_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "SLA_MIN_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "SLA_UNAVAILABILITY" double precision DEFAULT 0::double precision NOT NULL,
    "H24_UNAVAILABILITY" double precision DEFAULT 0::double precision NOT NULL,
    "SLA_FAILURE" double precision DEFAULT 0::double precision NOT NULL,
    "H24_FAILURE" double precision DEFAULT 0::double precision NOT NULL,
    "SLA_UNTESTED" double precision DEFAULT 0::double precision NOT NULL,
    "H24_UNTESTED" double precision DEFAULT 0::double precision NOT NULL
);


CREATE TABLE job_defaults (
    "ID_PARAM" bigint NOT NULL,
    "COLUMN_NAME" character varying(45) NOT NULL,
    "STRING_VALUE" character varying(45),
    "VALUE_TYPE" character varying(20) NOT NULL
);


CREATE TABLE jobs (
    "ID_JOB" bigint NOT NULL,
    "NAME" character varying(45) NOT NULL,
    "ID_SERVICE_TYPE" bigint NOT NULL,
    "SERVICE_URL" character varying(255) NOT NULL,
    "ID_HTTP_METHOD" bigint NOT NULL,
    "TEST_INTERVAL" bigint NOT NULL,
    "TIMEOUT" bigint NOT NULL,
    "BUSINESS_ERRORS" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "SLA_START_TIME" timestamp(3) without time zone NOT NULL,
    "LOGIN" character varying(45),
    "PASSWORD" character varying(45),
    "IS_PUBLIC" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "IS_AUTOMATIC" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "ALLOWS_REALTIME" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "TRIGGERS_ALERTS" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "ID_STATUS" bigint DEFAULT 4::bigint NOT NULL,
    "HTTP_ERRORS" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "SLA_END_TIME" timestamp(3) without time zone NOT NULL,
    "STATUS_UPDATE_TIME" timestamp without time zone DEFAULT '2014-04-02 15:33:38.886'::timestamp without time zone NOT NULL,
    "SAVE_RESPONSE" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "RUN_SIMULTANEOUS" bit(1) DEFAULT B'0'::"bit" NOT NULL
);


CREATE TABLE jos_sdi_accessscope (
    id bigint NOT NULL,
    entity_guid character varying(36) NOT NULL,
    organism_id bigint,
    user_id bigint
);

CREATE TABLE jos_sdi_address (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50),
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    user_id bigint,
    organism_id bigint,
    addresstype_id bigint NOT NULL,
    civility character varying(100),
    firstname character varying(100),
    lastname character varying(100),
    function character varying(100),
    address character varying(100),
    addresscomplement character varying(100),
    postalcode character varying(10),
    postalbox character varying(10),
    locality character varying(100),
    country_id bigint,
    phone character varying(20),
    mobile character varying(20),
    fax character varying(20),
    email character varying(100),
    sameascontact bit(1) DEFAULT B'1'::"bit" NOT NULL
);

CREATE TABLE jos_sdi_allowedoperation (
    id bigint NOT NULL,
    policy_id bigint NOT NULL,
    serviceoperation_id bigint NOT NULL
);

CREATE TABLE jos_sdi_application (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    modified_by integer NOT NULL,
    modified timestamp(3) without time zone NOT NULL,
    ordering integer NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500) NOT NULL,
    resource_id bigint NOT NULL,
    options character varying(500) NOT NULL,
    url character varying(500) NOT NULL,
    windowname character varying(255) NOT NULL,
    access integer NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);

CREATE TABLE jos_sdi_assignment (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    assigned timestamp(3) without time zone,
    assigned_by bigint NOT NULL,
    assigned_to bigint NOT NULL,
    version_id bigint NOT NULL,
    text character varying(500)
);

CREATE TABLE jos_sdi_attribute (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500),
    namespace_id bigint NOT NULL,
    isocode character varying(255),
    stereotype_id bigint NOT NULL,
    length integer,
    pattern character varying(500),
    listnamespace_id bigint,
    type_isocode character varying(255),
    codelist character varying(255),
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_attributevalue (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    value character varying(255),
    attribute_id bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);

CREATE TABLE jos_sdi_boundary (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    northbound character varying(255),
    southbound character varying(255),
    eastbound character varying(255),
    westbound character varying(255),
    category_id bigint,
    parent_id bigint,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_boundarycategory (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    parent_id bigint,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);

CREATE TABLE jos_sdi_catalog (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500),
    xsldirectory character varying(255),
    oninitrunsearch bit(1) DEFAULT B'0'::"bit",
    cswfilter text,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_catalog_resourcetype (
    id bigint NOT NULL,
    ordering integer,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    catalog_id bigint NOT NULL,
    resourcetype_id bigint NOT NULL
);

CREATE TABLE jos_sdi_catalog_searchcriteria (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    catalog_id bigint NOT NULL,
    searchcriteria_id bigint NOT NULL,
    searchtab_id bigint NOT NULL,
    defaultvalue character varying(255),
    defaultvaluefrom timestamp(3) without time zone,
    defaultvalueto timestamp(3) without time zone,
    params character varying(500)
);


CREATE TABLE jos_sdi_catalog_searchsort (
    id bigint NOT NULL,
    ordering integer,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    catalog_id bigint NOT NULL,
    language_id bigint NOT NULL,
    ogcsearchsorting character varying(255)
);


CREATE TABLE jos_sdi_class (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500),
    issystem bit(1) DEFAULT B'0'::"bit" NOT NULL,
    isrootclass bit(1) DEFAULT B'0'::"bit" NOT NULL,
    namespace_id bigint NOT NULL,
    isocode character varying(255),
    stereotype_id bigint,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_csw_spatialpolicy (
    id bigint NOT NULL,
    eastboundlongitude numeric(10,6),
    westboundlongitude numeric(10,6),
    northboundlatitude numeric(10,6),
    southboundlatitude numeric(10,6),
    maxx numeric(18,6),
    maxy numeric(18,6),
    minx numeric(18,6),
    miny numeric(18,6),
    srssource character varying(255)
);


CREATE TABLE jos_sdi_diffusion (
    id bigint NOT NULL,
    guid character varying(255) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    version_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500) NOT NULL,
    accessscope_id bigint NOT NULL,
    pricing_id bigint NOT NULL,
    deposit character varying(255),
    productmining_id bigint,
    surfacemin character varying(50),
    surfacemax character varying(50),
    productstorage_id bigint,
    file character varying(255),
    fileurl character varying(500),
    perimeter_id bigint,
    hasdownload bit(1) DEFAULT B'0'::"bit" NOT NULL,
    hasextraction bit(1) DEFAULT B'0'::"bit" NOT NULL,
    restrictedperimeter bit(1) DEFAULT B'0'::"bit" NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_diffusion_download (
    id bigint NOT NULL,
    diffusion_id bigint NOT NULL,
    user_id bigint,
    executed timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL
);

CREATE TABLE jos_sdi_diffusion_notifieduser (
    id bigint NOT NULL,
    ordering integer NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    diffusion_id bigint NOT NULL,
    user_id bigint NOT NULL
);

CREATE TABLE jos_sdi_diffusion_perimeter (
    id bigint NOT NULL,
    ordering integer NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    diffusion_id bigint NOT NULL,
    perimeter_id bigint NOT NULL,
    buffer bit(1) NOT NULL
);


CREATE TABLE jos_sdi_diffusion_propertyvalue (
    id bigint NOT NULL,
    ordering integer NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    diffusion_id bigint NOT NULL,
    propertyvalue_id bigint NOT NULL
);


CREATE TABLE jos_sdi_excludedattribute (
    id bigint NOT NULL,
    path character varying(500) NOT NULL,
    policy_id bigint NOT NULL
);

CREATE TABLE jos_sdi_featuretype_policy (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255),
    enabled bit(1) DEFAULT B'1'::"bit" NOT NULL,
    inheritedspatialpolicy bit(1) DEFAULT B'1'::"bit" NOT NULL,
    spatialpolicy_id bigint,
    physicalservicepolicy_id bigint NOT NULL
);


CREATE TABLE jos_sdi_importref (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500),
    xsl4sdi character varying(255),
    xsl4ext character varying(255),
    cswservice_id bigint,
    cswversion_id bigint,
    cswoutputschema character varying(255),
    importtype_id bigint,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_includedattribute (
    id bigint NOT NULL,
    name character varying(500) NOT NULL,
    featuretypepolicy_id bigint NOT NULL
);


CREATE TABLE jos_sdi_language (
    id bigint NOT NULL,
    ordering bigint DEFAULT 0::bigint NOT NULL,
    state integer DEFAULT 1,
    value character varying(50) NOT NULL,
    code character varying(20) NOT NULL,
    gemet character varying(10) NOT NULL,
    "iso639-2T" character varying(10),
    "iso639-1" character varying(10),
    "iso3166-1-alpha2" character varying(10),
    "iso639-2B" character varying(10)
);


CREATE TABLE jos_sdi_layer (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255),
    physicalservice_id bigint NOT NULL
);


CREATE TABLE jos_sdi_layer_layergroup (
    id bigint NOT NULL,
    layer_id bigint NOT NULL,
    group_id bigint NOT NULL,
    ordering integer
);


CREATE TABLE jos_sdi_layergroup (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(20) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    isdefaultopen bit(1) DEFAULT B'0'::"bit" NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);


CREATE TABLE jos_sdi_map (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(20) NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    created_by integer NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    title character varying(255) NOT NULL,
    rootnodetext character varying(255),
    srs character varying(255) NOT NULL,
    unit_id bigint NOT NULL,
    maxresolution double precision,
    numzoomlevel integer,
    maxextent character varying(255) NOT NULL,
    restrictedextent character varying(255),
    centercoordinates character varying(255),
    zoom character varying(10),
    abstract text,
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);


CREATE TABLE jos_sdi_map_layergroup (
    id bigint NOT NULL,
    map_id bigint NOT NULL,
    group_id bigint NOT NULL,
    isbackground bit(1) DEFAULT B'0'::"bit" NOT NULL,
    isdefault bit(1) DEFAULT B'0'::"bit" NOT NULL,
    ordering integer
);


CREATE TABLE jos_sdi_map_physicalservice (
    id bigint NOT NULL,
    map_id bigint NOT NULL,
    physicalservice_id bigint NOT NULL
);


CREATE TABLE jos_sdi_map_tool (
    id bigint NOT NULL,
    map_id bigint NOT NULL,
    tool_id bigint NOT NULL,
    params character varying(500)
);

CREATE TABLE jos_sdi_map_virtualservice (
    id bigint NOT NULL,
    map_id bigint NOT NULL,
    virtualservice_id bigint NOT NULL
);


CREATE TABLE jos_sdi_maplayer (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(20) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    service_id bigint,
    servicetype character varying(10),
    layername character varying(255) NOT NULL,
    istiled bit(1) DEFAULT B'0'::"bit" NOT NULL,
    isdefaultvisible bit(1) DEFAULT B'0'::"bit" NOT NULL,
    opacity numeric(3,2) DEFAULT 1.00 NOT NULL,
    "asOL" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "asOLstyle" text,
    "asOLmatrixset" text,
    "asOLoptions" text,
    metadatalink text,
    attribution character varying(255),
    accessscope_id bigint DEFAULT 1::bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);


CREATE TABLE jos_sdi_metadata (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    metadatastate_id bigint DEFAULT 1::bigint NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    accessscope_id bigint NOT NULL,
    published timestamp(3) without time zone,
    archived timestamp(3) without time zone,
    lastsynchronization timestamp(3) without time zone,
    synchronized_by bigint,
    notification bit(1) DEFAULT B'0'::"bit" NOT NULL,
    version_id bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_monitor_exports (
    id bigint NOT NULL,
    "exportDesc" character varying(500),
    "exportName" character varying(500),
    "exportType" character varying(10),
    "xsltUrl" character varying(500)
);


CREATE TABLE jos_sdi_namespace (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer DEFAULT 1 NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    prefix character varying(10) NOT NULL,
    uri character varying(255) NOT NULL,
    system bit(1) DEFAULT B'0'::"bit" NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_order (
    id bigint NOT NULL,
    guid character varying(255) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    ordertype_id bigint,
    orderstate_id bigint NOT NULL,
    user_id bigint NOT NULL,
    thirdparty_id bigint,
    buffer double precision,
    surface double precision,
    remark character varying(500) NOT NULL,
    sent timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    completed timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_order_diffusion (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    diffusion_id bigint NOT NULL,
    productstate_id bigint NOT NULL,
    remark character varying(500) NOT NULL,
    fee numeric(10,0) NOT NULL,
    completed timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    file character varying(500) NOT NULL,
    size numeric(10,0) NOT NULL,
    created_by integer NOT NULL
);


CREATE TABLE jos_sdi_order_perimeter (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    perimeter_id bigint NOT NULL,
    value text,
    text text,
    created_by integer NOT NULL
);


CREATE TABLE jos_sdi_order_propertyvalue (
    id bigint NOT NULL,
    orderdiffusion_id bigint NOT NULL,
    property_id bigint NOT NULL,
    propertyvalue_id bigint NOT NULL,
    propertyvalue character varying(4000) NOT NULL,
    created_by integer NOT NULL
);


CREATE TABLE jos_sdi_organism (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    acronym character varying(150),
    description character varying(500),
    logo character varying(500),
    name character varying(255) NOT NULL,
    website character varying(500),
    perimeter text,
    access integer NOT NULL,
    asset_id integer NOT NULL,
    username character varying(150),
    password character varying(65)
);


CREATE TABLE jos_sdi_perimeter (
    id bigint NOT NULL,
    guid character varying(255) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer DEFAULT 1 NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500),
    accessscope_id bigint NOT NULL,
    perimetertype_id bigint NOT NULL,
    wfsservice_id bigint,
    wfsservicetype_id bigint,
    featuretypename character varying(255),
    prefix character varying(255),
    namespace character varying(255),
    featuretypefieldid character varying(255),
    featuretypefieldname character varying(255),
    featuretypefieldsurface character varying(255),
    featuretypefielddescription character varying(255),
    featuretypefieldgeometry character varying(255),
    featuretypefieldresource character varying(255),
    wmsservice_id bigint,
    wmsservicetype_id bigint,
    layername character varying(255),
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_physicalservice (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(20) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255),
    servicescope_id bigint DEFAULT 1::bigint NOT NULL,
    serviceconnector_id bigint NOT NULL,
    resourceauthentication_id bigint,
    resourceurl character varying(500),
    resourceusername character varying(150),
    resourcepassword character varying(150),
    serviceauthentication_id bigint,
    serviceurl character varying(500),
    serviceusername character varying(150),
    servicepassword character varying(150),
    catid integer NOT NULL,
    params character varying(1024),
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);


CREATE TABLE jos_sdi_physicalservice_organism (
    id bigint NOT NULL,
    physicalservice_id bigint NOT NULL,
    organism_id bigint NOT NULL
);


CREATE TABLE jos_sdi_physicalservice_policy (
    id bigint NOT NULL,
    prefix character varying(255),
    namespace character varying(255),
    anyitem bit(1) DEFAULT B'1'::"bit" NOT NULL,
    inheritedspatialpolicy bit(1) DEFAULT B'1'::"bit" NOT NULL,
    csw_spatialpolicy_id bigint,
    wms_spatialpolicy_id bigint,
    wmts_spatialpolicy_id bigint,
    wfs_spatialpolicy_id bigint,
    physicalservice_id bigint NOT NULL,
    policy_id bigint NOT NULL
);


CREATE TABLE jos_sdi_physicalservice_servicecompliance (
    id bigint NOT NULL,
    service_id bigint NOT NULL,
    servicecompliance_id bigint NOT NULL,
    capabilities text
);


CREATE TABLE jos_sdi_policy (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    name character varying(255) NOT NULL,
    alias character varying(20) NOT NULL,
    allowfrom date DEFAULT '0002-11-30'::date NOT NULL,
    allowto date DEFAULT '0002-11-30'::date NOT NULL,
    anyoperation bit(1) DEFAULT B'1'::"bit" NOT NULL,
    anyservice bit(1) DEFAULT B'1'::"bit" NOT NULL,
    accessscope_id bigint DEFAULT 1::bigint NOT NULL,
    virtualservice_id bigint NOT NULL,
    csw_spatialpolicy_id bigint,
    wms_spatialpolicy_id bigint,
    wmts_spatialpolicy_id bigint,
    wfs_spatialpolicy_id bigint,
    csw_version_id bigint DEFAULT 1::bigint NOT NULL,
    csw_anyattribute bit(1) DEFAULT B'1'::"bit" NOT NULL,
    csw_anycontext bit(1) DEFAULT B'1'::"bit" NOT NULL,
    csw_anystate bit(1) DEFAULT B'1'::"bit" NOT NULL,
    csw_anyvisibility bit(1) DEFAULT B'1'::"bit" NOT NULL,
    csw_includeharvested bit(1) DEFAULT B'1'::"bit" NOT NULL,
    csw_anyresourcetype bit(1) DEFAULT B'1'::"bit" NOT NULL,
    csw_accessscope_id bit(1) DEFAULT B'1'::"bit" NOT NULL,
    wms_minimumwidth character varying(255),
    wms_minimumheight character varying(255),
    wms_maximumwidth character varying(255),
    wms_maximumheight character varying(255),
    params character varying(1024),
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);


CREATE TABLE jos_sdi_policy_metadatastate (
    id bigint NOT NULL,
    metadatastate_id bigint NOT NULL,
    policy_id bigint NOT NULL,
    metadataversion_id bigint
);


CREATE TABLE jos_sdi_policy_organism (
    id bigint NOT NULL,
    policy_id bigint NOT NULL,
    organism_id bigint NOT NULL
);


CREATE TABLE jos_sdi_policy_resourcetype (
    id bigint NOT NULL,
    resourcetype_id bigint NOT NULL,
    policy_id bigint NOT NULL
);


CREATE TABLE jos_sdi_policy_user (
    id bigint NOT NULL,
    policy_id bigint NOT NULL,
    user_id bigint NOT NULL
);


CREATE TABLE jos_sdi_policy_visibility (
    id bigint NOT NULL,
    policy_id bigint NOT NULL,
    user_id bigint,
    organism_id bigint
);


CREATE TABLE jos_sdi_profile (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500),
    class_id bigint NOT NULL,
    metadataidentifier bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_property (
    id bigint NOT NULL,
    guid character varying(255) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500) NOT NULL,
    accessscope_id bigint NOT NULL,
    mandatory integer NOT NULL,
    propertytype_id bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_propertyvalue (
    id bigint NOT NULL,
    guid character varying(255) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500) NOT NULL,
    property_id bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_relation (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500),
    parent_id bigint NOT NULL,
    attributechild_id bigint,
    classchild_id bigint,
    lowerbound integer,
    upperbound integer,
    relationtype_id bigint,
    rendertype_id bigint,
    namespace_id bigint,
    isocode character varying(255),
    classassociation_id bigint,
    issearchfilter bit(1) DEFAULT B'0'::"bit" NOT NULL,
    relationscope_id bigint,
    editorrelationscope_id bigint,
    childresourcetype_id bigint,
    childtype_id bigint,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_relation_catalog (
    id bigint NOT NULL,
    ordering integer,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    relation_id bigint NOT NULL,
    catalog_id bigint NOT NULL
);


CREATE TABLE jos_sdi_relation_defaultvalue (
    id bigint NOT NULL,
    relation_id bigint NOT NULL,
    attributevalue_id bigint,
    value character varying(500),
    language_id bigint
);


CREATE TABLE jos_sdi_relation_profile (
    id bigint NOT NULL,
    ordering integer,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    relation_id bigint NOT NULL,
    profile_id bigint NOT NULL
);


CREATE TABLE jos_sdi_resource (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer DEFAULT 1 NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer,
    checked_out_time timestamp(3) without time zone,
    name character varying(255) NOT NULL,
    description character varying(500) NOT NULL,
    organism_id bigint NOT NULL,
    resourcetype_id bigint NOT NULL,
    accessscope_id bigint NOT NULL,
    access integer NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_resourcetype (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    modified_by integer NOT NULL,
    modified timestamp(3) without time zone NOT NULL,
    ordering integer NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500) NOT NULL,
    logo character varying(255) NOT NULL,
    meta bit(1) NOT NULL,
    diffusion bit(1) NOT NULL,
    view bit(1) NOT NULL,
    monitoring bit(1) NOT NULL,
    predefined bit(1) NOT NULL,
    versioning bit(1) NOT NULL,
    profile_id bigint NOT NULL,
    fragmentnamespace_id bigint,
    fragment character varying(255),
    sitemapparams character varying(1000),
    accessscope_id bigint NOT NULL,
    access integer NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);

CREATE TABLE jos_sdi_resourcetypelink (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    modified_by integer NOT NULL,
    modified timestamp(3) without time zone NOT NULL,
    ordering integer NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    parent_id bigint NOT NULL,
    child_id bigint NOT NULL,
    parentboundlower integer NOT NULL,
    parentboundupper integer NOT NULL,
    childboundlower integer NOT NULL,
    childboundupper integer NOT NULL,
    class_id bigint,
    attribute_id bigint,
    viralversioning bit(1) NOT NULL,
    inheritance bit(1) NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_resourcetypelinkinheritance (
    id bigint NOT NULL,
    resourcetypelink_id bigint NOT NULL,
    xpath character varying(500) NOT NULL
);


CREATE TABLE jos_sdi_searchcriteria (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    issystem bit(1) DEFAULT B'0'::"bit" NOT NULL,
    criteriatype_id bigint NOT NULL,
    rendertype_id bigint,
    relation_id bigint,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE jos_sdi_searchcriteriafilter (
    id bigint NOT NULL,
    ordering integer,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    searchcriteria_id bigint NOT NULL,
    language_id bigint NOT NULL,
    ogcsearchfilter character varying(255) NOT NULL
);

CREATE TABLE jos_sdi_sys_accessscope (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_addresstype (
    id bigint NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_authenticationconnector (
    id bigint NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    authenticationlevel_id bigint NOT NULL,
    value character varying(150) NOT NULL
);



CREATE TABLE jos_sdi_sys_authenticationlevel (
    id bigint NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);



CREATE TABLE jos_sdi_sys_country (
    id bigint NOT NULL,
    ordering bigint DEFAULT 1::bigint NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    name character varying(100) NOT NULL,
    iso2 character varying(2),
    iso3 character varying(3)
);


CREATE TABLE jos_sdi_sys_criteriatype (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_entity (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_exceptionlevel (
    id bigint NOT NULL,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL
);


CREATE TABLE jos_sdi_sys_importtype (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_isolanguage (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_loglevel (
    id bigint NOT NULL,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL
);


CREATE TABLE jos_sdi_sys_logroll (
    id bigint NOT NULL,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL
);


CREATE TABLE jos_sdi_sys_maptool (
    id bigint NOT NULL,
    alias character varying(20) NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    name character varying(255) NOT NULL
);


CREATE TABLE jos_sdi_sys_metadatastate (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_metadataversion (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_operationcompliance (
    id bigint NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    servicecompliance_id bigint NOT NULL,
    serviceoperation_id bigint NOT NULL,
    implemented bit(1) DEFAULT B'0'::"bit" NOT NULL
);


CREATE TABLE jos_sdi_sys_orderstate (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_ordertype (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_perimetertype (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_pricing (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_productmining (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_productstate (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_productstorage (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);

CREATE TABLE jos_sdi_sys_propertytype (
    id bigint NOT NULL,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL
);


CREATE TABLE jos_sdi_sys_proxytype (
    id bigint NOT NULL,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL
);


CREATE TABLE jos_sdi_sys_relationscope (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);

CREATE TABLE jos_sdi_sys_relationtype (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_rendertype (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_rendertype_criteriatype (
    id bigint NOT NULL,
    criteriatype_id bigint NOT NULL,
    rendertype_id bigint NOT NULL
);

CREATE TABLE jos_sdi_sys_rendertype_stereotype (
    id bigint NOT NULL,
    stereotype_id bigint NOT NULL,
    rendertype_id bigint NOT NULL
);


CREATE TABLE jos_sdi_sys_role (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE jos_sdi_sys_searchtab (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);

CREATE TABLE jos_sdi_sys_servicecompliance (
    id bigint NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    serviceconnector_id bigint NOT NULL,
    serviceversion_id bigint NOT NULL,
    implemented bit(1) DEFAULT B'0'::"bit" NOT NULL,
    relayable bit(1) DEFAULT B'0'::"bit" NOT NULL,
    aggregatable bit(1) DEFAULT B'0'::"bit" NOT NULL,
    harvestable bit(1) DEFAULT B'0'::"bit" NOT NULL
);

CREATE TABLE jos_sdi_sys_servicecon_authenticationcon (
    id bigint NOT NULL,
    serviceconnector_id bigint NOT NULL,
    authenticationconnector_id bigint NOT NULL
);







CREATE TABLE jos_sdi_sys_serviceconnector (
    id bigint NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE jos_sdi_sys_serviceoperation (
    id bigint NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE jos_sdi_sys_servicescope (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE jos_sdi_sys_servicetype (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE jos_sdi_sys_serviceversion (
    id bigint NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE jos_sdi_sys_spatialoperator (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE jos_sdi_sys_stereotype (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL,
    defaultpattern character varying(255),
    isocode character varying(255),
    namespace_id bigint,
    entity_id bigint
);







CREATE TABLE jos_sdi_sys_topiccategory (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE jos_sdi_sys_unit (
    id bigint NOT NULL,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    alias character varying(50) NOT NULL,
    name character varying(255) NOT NULL
);







CREATE TABLE jos_sdi_sys_versiontype (
    id bigint NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE jos_sdi_tilematrix_policy (
    id bigint NOT NULL,
    tilematrixsetpolicy_id bigint NOT NULL,
    identifier character varying(255) NOT NULL,
    tileminrow integer,
    tilemaxrow integer,
    tilemincol integer,
    tilemaxcol integer,
    anytile bit(1) DEFAULT B'1'::"bit" NOT NULL
);







CREATE TABLE jos_sdi_tilematrixset_policy (
    id bigint NOT NULL,
    wmtslayerpolicy_id bigint NOT NULL,
    identifier character varying(255) NOT NULL,
    anytilematrix bit(1) DEFAULT B'1'::"bit" NOT NULL,
    srssource character varying(255)
);







CREATE TABLE jos_sdi_translation (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    element_guid character varying(36) NOT NULL,
    language_id bigint,
    text1 character varying(255),
    text2 character varying(500)
);







CREATE TABLE jos_sdi_user (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    user_id integer NOT NULL,
    description text,
    notificationrequesttreatment bit(1) DEFAULT B'1'::"bit" NOT NULL,
    catid integer,
    params character varying(1024),
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);







CREATE TABLE jos_sdi_user_role_organism (
    id bigint NOT NULL,
    user_id bigint,
    role_id bigint,
    organism_id bigint
);







CREATE TABLE jos_sdi_user_role_resource (
    id bigint NOT NULL,
    user_id bigint,
    role_id bigint,
    resource_id bigint
);







CREATE TABLE jos_sdi_version (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer NOT NULL,
    state bit(1) DEFAULT B'1'::"bit" NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    resource_id bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);







CREATE TABLE jos_sdi_versionlink (
    id bigint NOT NULL,
    parent_id bigint NOT NULL,
    child_id bigint NOT NULL
);







CREATE TABLE jos_sdi_virtual_physical (
    id bigint NOT NULL,
    virtualservice_id bigint NOT NULL,
    physicalservice_id bigint NOT NULL
);







CREATE TABLE jos_sdi_virtualmetadata (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    title character varying(255),
    inheritedtitle bit(1) DEFAULT B'1'::"bit" NOT NULL,
    summary character varying(255),
    inheritedsummary bit(1) DEFAULT B'1'::"bit" NOT NULL,
    keyword character varying(255),
    inheritedkeyword bit(1) DEFAULT B'1'::"bit" NOT NULL,
    fee character varying(255),
    inheritedfee bit(1) DEFAULT B'1'::"bit" NOT NULL,
    accessconstraint character varying(255),
    inheritedaccessconstraint bit(1) DEFAULT B'1'::"bit" NOT NULL,
    inheritedcontact bit(1) DEFAULT B'1'::"bit" NOT NULL,
    contactorganization character varying(255),
    contactname character varying(255),
    contactposition character varying(255),
    contactaddress character varying(255),
    contactaddresstype character varying(255),
    contactrole character varying(255),
    contactpostalcode character varying(255),
    contactlocality character varying(255),
    contactstate character varying(255),
    country_id bigint,
    contactphone character varying(255),
    contactfax character varying(255),
    contactemail character varying(255),
    contacturl character varying(255),
    contactavailability character varying(255),
    contactinstruction character varying(255),
    virtualservice_id bigint NOT NULL
);







CREATE TABLE jos_sdi_virtualservice (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    name character varying(255) NOT NULL,
    alias character varying(20) NOT NULL,
    servicescope_id bigint DEFAULT 1::bigint NOT NULL,
    url character varying(500),
    serviceconnector_id bigint NOT NULL,
    reflectedurl character varying(255),
    reflectedmetadata bit(1) DEFAULT B'0'::"bit" NOT NULL,
    xsltfilename character varying(255),
    logpath character varying(255) NOT NULL,
    harvester bit(1) DEFAULT B'0'::"bit" NOT NULL,
    maximumrecords integer,
    identifiersearchattribute character varying(255),
    proxytype_id bigint NOT NULL,
    exceptionlevel_id bigint NOT NULL,
    loglevel_id bigint NOT NULL,
    logroll_id bigint NOT NULL,
    params character varying(1024),
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);







CREATE TABLE jos_sdi_virtualservice_organism (
    id bigint NOT NULL,
    virtualservice_id bigint NOT NULL,
    organism_id bigint NOT NULL
);







CREATE TABLE jos_sdi_virtualservice_servicecompliance (
    id bigint NOT NULL,
    service_id bigint NOT NULL,
    servicecompliance_id bigint NOT NULL
);







CREATE TABLE jos_sdi_visualization (
    id bigint NOT NULL,
    guid character varying(36) NOT NULL,
    alias character varying(20) NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    created_by integer NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    version_id bigint NOT NULL,
    accessscope_id bigint NOT NULL,
    maplayer_id bigint,
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);







CREATE TABLE jos_sdi_wfs_spatialpolicy (
    id bigint NOT NULL,
    localgeographicfilter text,
    remotegeographicfilter text
);







CREATE TABLE jos_sdi_wms_spatialpolicy (
    id bigint NOT NULL,
    maxx numeric(18,6),
    maxy numeric(18,6),
    minx numeric(18,6),
    miny numeric(18,6),
    geographicfilter text,
    maximumscale integer,
    minimumscale integer,
    srssource character varying(255)
);







CREATE TABLE jos_sdi_wmslayer_policy (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255) NOT NULL,
    enabled bit(1) DEFAULT B'0'::"bit" NOT NULL,
    inheritedspatialpolicy bit(1) DEFAULT B'1'::"bit" NOT NULL,
    spatialpolicy_id bigint,
    physicalservicepolicy_id bigint NOT NULL
);







CREATE TABLE jos_sdi_wmts_spatialpolicy (
    id bigint NOT NULL,
    spatialoperator_id bigint DEFAULT 1::bigint NOT NULL,
    eastboundlongitude numeric(10,6),
    westboundlongitude numeric(10,6),
    northboundlatitude numeric(10,6),
    southboundlatitude numeric(10,6)
);







CREATE TABLE jos_sdi_wmtslayer_policy (
    id bigint NOT NULL,
    identifier character varying(255) NOT NULL,
    enabled bit(1) DEFAULT B'1'::"bit" NOT NULL,
    inheritedspatialpolicy bit(1) DEFAULT B'1'::"bit" NOT NULL,
    spatialpolicy_id bigint,
    anytilematrixset bit(1) DEFAULT B'1'::"bit" NOT NULL,
    physicalservicepolicy_id bigint NOT NULL
);







CREATE TABLE last_ids (
    "TABLE_NAME" character varying(255) NOT NULL,
    "LAST_ID" integer DEFAULT 0 NOT NULL
);







CREATE TABLE last_query_results (
    "ID_LAST_QUERY_RESULT" bigint NOT NULL,
    "ID_QUERY" bigint NOT NULL,
    "DATA" bytea,
    "XML_RESULT" text,
    "TEXT_RESULT" text,
    "PICTURE_URL" character varying(1000),
    "CONTENT_TYPE" character varying(100)
);







CREATE TABLE log_entries (
    "ID_LOG_ENTRY" bigint NOT NULL,
    "ID_QUERY" bigint NOT NULL,
    "REQUEST_TIME" timestamp(3) without time zone NOT NULL,
    "RESPONSE_DELAY" double precision NOT NULL,
    "MESSAGE" text NOT NULL,
    "ID_STATUS" bigint NOT NULL,
    "HTTP_CODE" bigint,
    "EXCEPTION_CODE" character varying(100),
    "RESPONSE_SIZE" double precision
);







CREATE TABLE overview_page (
    "ID_OVERVIEW_PAGE" bigint NOT NULL,
    "NAME" character varying(255) NOT NULL,
    "IS_PUBLIC" bit(1) DEFAULT B'0'::"bit" NOT NULL
);







CREATE TABLE overview_queries (
    "ID_OVERVIEW_QUERY" bigint NOT NULL,
    "ID_OVERVIEW_PAGE" bigint NOT NULL,
    "ID_QUERY" bigint NOT NULL
);







CREATE TABLE periods (
    "ID_PERIODS" bigint NOT NULL,
    "ID_SLA" bigint NOT NULL,
    "NAME" character varying(45),
    "MONDAY" bit(1) DEFAULT B'0'::"bit",
    "TUESDAY" bit(1) DEFAULT B'0'::"bit",
    "WEDNESDAY" bit(1) DEFAULT B'0'::"bit",
    "THURSDAY" bit(1) DEFAULT B'0'::"bit",
    "FRIDAY" bit(1) DEFAULT B'0'::"bit",
    "SATURDAY" bit(1) DEFAULT B'0'::"bit",
    "SUNDAY" bit(1) DEFAULT B'0'::"bit",
    "HOLIDAYS" bit(1) DEFAULT B'0'::"bit",
    "SLA_START_TIME" time without time zone NOT NULL,
    "SLA_END_TIME" time without time zone NOT NULL,
    "INCLUDE" bit(1) DEFAULT B'0'::"bit",
    "DATE" character varying(45)
);







CREATE TABLE queries (
    "ID_QUERY" bigint NOT NULL,
    "ID_JOB" bigint NOT NULL,
    "ID_SERVICE_METHOD" bigint NOT NULL,
    "ID_STATUS" bigint DEFAULT 4::bigint NOT NULL,
    "NAME" character varying(45) NOT NULL,
    "SOAP_URL" character varying(250)
);







CREATE TABLE query_agg_hour_log_entries (
    "DATE_LOG" timestamp(3) without time zone NOT NULL,
    "ID_QUERY" bigint NOT NULL,
    "H1_MEAN_RESP_TIME" double precision NOT NULL,
    "H1_MEAN_RESP_TIME_INSPIRE" double precision NOT NULL,
    "H1_AVAILABILITY" double precision NOT NULL,
    "H1_AVAILABILITY_INSPIRE" double precision NOT NULL,
    "H1_NB_BIZ_ERRORS" bigint NOT NULL,
    "H1_NB_BIZ_ERRORS_INSPIRE" bigint NOT NULL,
    "H1_NB_CONN_ERRORS" bigint NOT NULL,
    "H1_NB_CONN_ERRORS_INSPIRE" bigint NOT NULL,
    "H1_MAX_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "H1_MIN_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "H1_MAX_RESP_TIME_INSPIRE" double precision DEFAULT 0::double precision NOT NULL,
    "H1_MIN_RESP_TIME_INSPIRE" double precision DEFAULT 0::double precision NOT NULL,
    "H1_UNAVAILABILITY" double precision DEFAULT 0::double precision NOT NULL,
    "H1_UNAVAILABILITY_INSPIRE" double precision DEFAULT 0::double precision NOT NULL,
    "H1_FAILURE" double precision DEFAULT 0::double precision NOT NULL,
    "H1_FAILURE_INSPIRE" double precision DEFAULT 0::double precision NOT NULL,
    "H1_UNTESTED" double precision DEFAULT 0::double precision NOT NULL,
    "H1_UNTESTED_INSPIRE" double precision DEFAULT 0::double precision NOT NULL
);







CREATE TABLE query_agg_log_entries (
    "DATE_LOG" timestamp(3) without time zone NOT NULL,
    "ID_QUERY" bigint NOT NULL,
    "SLA_MEAN_RESP_TIME" double precision NOT NULL,
    "H24_MEAN_RESP_TIME" double precision NOT NULL,
    "SLA_AVAILABILITY" double precision NOT NULL,
    "H24_AVAILABILITY" double precision NOT NULL,
    "SLA_NB_BIZ_ERRORS" bigint NOT NULL,
    "H24_NB_BIZ_ERRORS" bigint NOT NULL,
    "SLA_NB_CONN_ERRORS" bigint NOT NULL,
    "H24_NB_CONN_ERRORS" bigint NOT NULL,
    "H24_MAX_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "H24_MIN_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "SLA_MAX_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "SLA_MIN_RESP_TIME" double precision DEFAULT 0::double precision NOT NULL,
    "SLA_UNAVAILABILITY" double precision DEFAULT 0::double precision NOT NULL,
    "H24_UNAVAILABILITY" double precision DEFAULT 0::double precision NOT NULL,
    "SLA_FAILURE" double precision DEFAULT 0::double precision NOT NULL,
    "H24_FAILURE" double precision DEFAULT 0::double precision NOT NULL,
    "SLA_UNTESTED" double precision DEFAULT 0::double precision NOT NULL,
    "H24_UNTESTED" double precision DEFAULT 0::double precision NOT NULL
);







CREATE TABLE query_params (
    "ID_QUERY" bigint NOT NULL,
    "NAME" character varying(45) NOT NULL,
    "VALUE" text
);







CREATE TABLE query_validation_results (
    "ID_QUERY_VALIDATION_RESULT" integer NOT NULL,
    "ID_QUERY" bigint NOT NULL,
    "SIZE_VALIDATION_RESULT" bit(1),
    "RESPONSE_SIZE" double precision,
    "TIME_VALIDATION_RESULT" bit(1),
    "DELIVERY_TIME" double precision,
    "XPATH_VALIDATION_RESULT" bit(1),
    "XPATH_VALIDATION_OUTPUT" character varying(1000)
);







CREATE TABLE query_validation_settings (
    "ID_QUERY_VALIDATION_SETTINGS" integer NOT NULL,
    "ID_QUERY" bigint NOT NULL,
    "USE_SIZE_VALIDATION" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "NORM_SIZE" double precision,
    "NORM_SIZE_TOLERANCE" double precision,
    "USE_TIME_VALIDATION" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "NORM_TIME" double precision,
    "USE_XPATH_VALIDATION" bit(1) DEFAULT B'0'::"bit" NOT NULL,
    "XPATH_EXPRESSION" character varying(1000),
    "XPATH_EXPECTED_OUTPUT" character varying(1000)
);







CREATE TABLE roles (
    "ID_ROLE" bigint NOT NULL,
    "NAME" character varying(45) NOT NULL,
    "RANK" bigint NOT NULL
);







CREATE TABLE service_methods (
    "ID_SERVICE_METHOD" bigint NOT NULL,
    "NAME" character varying(45) NOT NULL
);







CREATE TABLE service_types (
    "ID_SERVICE_TYPE" bigint NOT NULL,
    "NAME" character varying(20) NOT NULL,
    "VERSION" character varying(10) NOT NULL
);







CREATE TABLE service_types_methods (
    "ID_SERVICE_TYPE" bigint NOT NULL,
    "ID_SERVICE_METHOD" bigint NOT NULL
);







CREATE TABLE sla (
    "ID_SLA" bigint NOT NULL,
    "NAME" character varying(45) NOT NULL,
    "EXCLUDE_WORST" bit(1) DEFAULT B'0'::"bit",
    "MEASURE_TIME_TO_FIRST" bit(1) DEFAULT B'0'::"bit"
);







CREATE TABLE statuses (
    "ID_STATUS" bigint NOT NULL,
    "NAME" character varying(45) NOT NULL
);







CREATE TABLE users (
    "LOGIN" character varying(45) NOT NULL,
    "PASSWORD" character varying(45) NOT NULL,
    "ID_ROLE" bigint,
    "EXPIRATION" date,
    "ENABLED" bit(1) DEFAULT B'1'::"bit" NOT NULL,
    "LOCKED" bit(1) DEFAULT B'0'::"bit" NOT NULL
);




