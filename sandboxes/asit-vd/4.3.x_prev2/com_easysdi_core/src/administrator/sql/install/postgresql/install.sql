SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET default_with_oids = false;

CREATE TABLE action_types (
    "ID_ACTION_TYPE" serial NOT NULL,
    "NAME" character varying(45) NOT NULL
);


CREATE TABLE actions (
    "ID_ACTION" serial NOT NULL,
    "ID_JOB" bigint NOT NULL,
    "ID_ACTION_TYPE" bigint NOT NULL,
    "TARGET" character varying(255),
    "LANGUAGE" character(2)
);

CREATE TABLE alerts (
    "ID_ALERT" serial NOT NULL,
    "ID_JOB" bigint NOT NULL,
    "ID_OLD_STATUS" bigint NOT NULL,
    "ID_NEW_STATUS" bigint NOT NULL,
    "CAUSE" text NOT NULL,
    "ALERT_DATE_TIME" timestamp(3) without time zone NOT NULL,
    "EXPOSE_RSS" integer NOT NULL,
    "RESPONSE_DELAY" double precision NOT NULL,
    "HTTP_CODE" bigint,
    "IMAGE" bytea,
    "CONTENT_TYPE" character varying(50)
);


CREATE TABLE holidays (
    "ID_HOLIDAYS" serial NOT NULL,
    "NAME" character varying(45),
    "DATE" timestamp(3) without time zone NOT NULL
);

CREATE TABLE http_methods (
    "ID_HTTP_METHOD" serial NOT NULL,
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
    "ID_PARAM" serial NOT NULL,
    "COLUMN_NAME" character varying(45) NOT NULL,
    "STRING_VALUE" character varying(45),
    "VALUE_TYPE" character varying(20) NOT NULL
);


CREATE TABLE jobs (
    "ID_JOB" serial NOT NULL,
    "NAME" character varying(45) NOT NULL,
    "ID_SERVICE_TYPE" bigint NOT NULL,
    "SERVICE_URL" character varying(255) NOT NULL,
    "ID_HTTP_METHOD" bigint NOT NULL,
    "TEST_INTERVAL" bigint NOT NULL,
    "TIMEOUT" bigint NOT NULL,
    "BUSINESS_ERRORS" integer DEFAULT 0 NOT NULL,
    "SLA_START_TIME" timestamp(3) without time zone NOT NULL,
    "LOGIN" character varying(45),
    "PASSWORD" character varying(45),
    "IS_PUBLIC" integer DEFAULT 0 NOT NULL,
    "IS_AUTOMATIC" integer DEFAULT 0 NOT NULL,
    "ALLOWS_REALTIME" integer DEFAULT 0 NOT NULL,
    "TRIGGERS_ALERTS" integer DEFAULT 0 NOT NULL,
    "ID_STATUS" bigint DEFAULT 4::bigint NOT NULL,
    "HTTP_ERRORS" integer DEFAULT 0 NOT NULL,
    "SLA_END_TIME" timestamp(3) without time zone NOT NULL,
    "STATUS_UPDATE_TIME" timestamp without time zone DEFAULT '2014-04-02 15:33:38.886'::timestamp without time zone NOT NULL,
    "SAVE_RESPONSE" integer DEFAULT 0 NOT NULL,
    "RUN_SIMULTANEOUS" integer DEFAULT 0 NOT NULL
);


CREATE TABLE #__sdi_accessscope (
    id serial NOT NULL ,
    entity_guid character varying(36) NOT NULL,
    organism_id bigint,
    user_id bigint,
    category_id bigint
);

CREATE TABLE #__sdi_address (
    id serial NOT NULL ,
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
    sameascontact integer DEFAULT 1 NOT NULL
);

CREATE TABLE #__sdi_allowedoperation (
    id serial NOT NULL ,
    policy_id bigint NOT NULL,
    serviceoperation_id bigint NOT NULL
);

CREATE TABLE #__sdi_application (
    id serial NOT NULL ,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer,
    created timestamp(3) without time zone ,
    modified_by integer NOT NULL,
    modified timestamp(3) without time zone NOT NULL,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
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

CREATE TABLE #__sdi_assignment (
    id serial NOT NULL ,
    guid character varying(36) NOT NULL,
    assigned timestamp(3) without time zone,
    assigned_by bigint NOT NULL,
    assigned_to bigint NOT NULL,
    metadata_id bigint NOT NULL,
    text character varying(500)
);

CREATE TABLE #__sdi_attribute (
    id serial NOT NULL ,
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


CREATE TABLE #__sdi_attributevalue (
    id serial NOT NULL ,
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

CREATE TABLE #__sdi_boundary (
    id serial NOT NULL ,
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


CREATE TABLE #__sdi_boundarycategory (
    id serial NOT NULL ,
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

CREATE TABLE #__sdi_catalog (
    id serial NOT NULL ,
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
    oninitrunsearch integer DEFAULT 0,
    cswfilter text,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL,
    scrolltoresults SMALLINT DEFAULT 1 NOT NULL 
);


CREATE TABLE #__sdi_catalog_resourcetype (
    id serial NOT NULL ,
    guid character varying(36) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    catalog_id bigint NOT NULL,
    resourcetype_id bigint NOT NULL
);

CREATE TABLE #__sdi_catalog_searchcriteria (
    id serial NOT NULL ,
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


CREATE TABLE #__sdi_catalog_searchsort (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    catalog_id bigint NOT NULL,
    language_id bigint NOT NULL,
    ogcsearchsorting character varying(255)
);


CREATE TABLE #__sdi_class (
    id serial NOT NULL ,
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
    issystem integer DEFAULT 0 NOT NULL,
    isrootclass integer DEFAULT 0 NOT NULL,
    namespace_id bigint NOT NULL,
    isocode character varying(255),
    stereotype_id bigint,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE #__sdi_csw_spatialpolicy (
    id serial NOT NULL ,
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


CREATE TABLE #__sdi_diffusion (
    id serial NOT NULL ,
    guid character varying(255) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    version_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500),
    accessscope_id bigint NOT NULL,
    pricing_id bigint NOT NULL,
    pricing_profile_id int(11) UNSIGNED,
    deposit character varying(255),
    productmining_id bigint,
    surfacemin character varying(50),
    surfacemax character varying(50),
    productstorage_id bigint,
    file character varying(255),
    fileurl character varying(500),
    perimeter_id bigint,
    hasdownload integer DEFAULT 0 NOT NULL,
    hasextraction integer DEFAULT 0 NOT NULL,
    restrictedperimeter integer DEFAULT 0 NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE #__sdi_diffusion_download (
    id serial NOT NULL ,
    diffusion_id bigint NOT NULL,
    user_id bigint,
    executed timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL
);

CREATE TABLE #__sdi_diffusion_notifieduser (
    id serial NOT NULL ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    diffusion_id bigint NOT NULL,
    user_id bigint NOT NULL
);

CREATE TABLE #__sdi_diffusion_perimeter (
    id serial NOT NULL ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    diffusion_id bigint NOT NULL,
    perimeter_id bigint NOT NULL,
    buffer integer NOT NULL
);


CREATE TABLE #__sdi_diffusion_propertyvalue (
    id serial NOT NULL ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    diffusion_id bigint NOT NULL,
    propertyvalue_id bigint NOT NULL
);


CREATE TABLE #__sdi_excludedattribute (
    id serial NOT NULL ,
    path character varying(500) NOT NULL,
    policy_id bigint NOT NULL
);

CREATE TABLE #__sdi_featuretype_policy (
    id serial NOT NULL ,
    name character varying(255) NOT NULL,
    description character varying(255),
    enabled integer DEFAULT 1 NOT NULL,
    inheritedspatialpolicy integer DEFAULT 1 NOT NULL,
    spatialpolicy_id bigint,
    physicalservicepolicy_id bigint NOT NULL
);


CREATE TABLE #__sdi_importref (
    id serial NOT NULL ,
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


CREATE TABLE #__sdi_includedattribute (
    id serial NOT NULL ,
    name character varying(500) NOT NULL,
    featuretypepolicy_id bigint NOT NULL
);


CREATE TABLE #__sdi_language (
    id serial NOT NULL ,
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


CREATE TABLE #__sdi_layer (
    id serial NOT NULL ,
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


CREATE TABLE #__sdi_layer_layergroup (
    id serial NOT NULL ,
    layer_id bigint NOT NULL,
    group_id bigint NOT NULL,
    ordering integer
);


CREATE TABLE #__sdi_layergroup (
    id serial NOT NULL ,
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
    isdefaultopen integer DEFAULT 0 NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);


CREATE TABLE #__sdi_map (
    id serial NOT NULL ,
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


CREATE TABLE #__sdi_map_layergroup (
    id serial NOT NULL ,
    map_id bigint NOT NULL,
    group_id bigint NOT NULL,
    isbackground integer DEFAULT 0 NOT NULL,
    isdefault integer DEFAULT 0 NOT NULL,
    ordering integer
);


CREATE TABLE #__sdi_map_physicalservice (
    id serial NOT NULL ,
    map_id bigint NOT NULL,
    physicalservice_id bigint NOT NULL
);


CREATE TABLE #__sdi_map_tool (
    id serial NOT NULL ,
    map_id bigint NOT NULL,
    tool_id bigint NOT NULL,
    params character varying(500)
);

CREATE TABLE #__sdi_map_virtualservice (
    id serial NOT NULL ,
    map_id bigint NOT NULL,
    virtualservice_id bigint NOT NULL
);


CREATE TABLE #__sdi_maplayer (
    id serial NOT NULL ,
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
    istiled integer DEFAULT 0 NOT NULL,
    isdefaultvisible integer DEFAULT 0 NOT NULL,
    opacity numeric(3,2) DEFAULT 1.00 NOT NULL,
    "asOL" integer DEFAULT 0 NOT NULL,
    "asOLstyle" text,
    "asOLmatrixset" text,
    "asOLoptions" text,
    metadatalink text,
    attribution character varying(255),
    accessscope_id bigint DEFAULT 1::bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);


CREATE TABLE #__sdi_metadata (
    id serial NOT NULL ,
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
    notification integer DEFAULT 0 NOT NULL,
    version_id bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE #__sdi_monitor_exports (
    id serial NOT NULL ,
    "exportDesc" character varying(500),
    "exportName" character varying(500),
    "exportType" character varying(10),
    "xsltUrl" character varying(500)
);


CREATE TABLE #__sdi_namespace (
    id serial NOT NULL ,
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
    name character varying(255) NOT NULL,
    prefix character varying(10) NOT NULL,
    uri character varying(255) NOT NULL,
    system integer DEFAULT 0 NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE #__sdi_order (
    id serial NOT NULL ,
    guid character varying(255) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    ordertype_id bigint,
    orderstate_id bigint NOT NULL,
    user_id bigint NOT NULL,
    thirdparty_id bigint,
    validated smallint DEFAULT NULL,
    validated_date timestamp(3) without time zone DEFAULT NULL,
    validated_reason character varying(255),
    buffer double precision,
    surface double precision,
    remark character varying(500),
    sent timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    completed timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE #__sdi_order_diffusion (
    id serial NOT NULL ,
    order_id bigint NOT NULL,
    diffusion_id bigint NOT NULL,
    productstate_id bigint NOT NULL,
    remark character varying(500) ,
    fee numeric(10,0) ,
    completed timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone ,
    file character varying(500) ,
    size numeric(10,0) ,
    created_by integer NOT NULL
);


CREATE TABLE #__sdi_order_perimeter (
    id serial NOT NULL ,
    order_id bigint NOT NULL,
    perimeter_id bigint NOT NULL,
    value text,
    text text,
    created_by integer NOT NULL
);


CREATE TABLE #__sdi_order_propertyvalue (
    id serial NOT NULL ,
    orderdiffusion_id bigint NOT NULL,
    property_id bigint NOT NULL,
    propertyvalue_id bigint NOT NULL,
    propertyvalue character varying(4000) NOT NULL,
    created_by integer NOT NULL
);


CREATE TABLE #__sdi_organism (
    id serial NOT NULL ,
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
    selectable_as_thirdparty TINYINT(1) DEFAULT 0,
    access integer NOT NULL,
    asset_id integer NOT NULL,
    username character varying(150),
    password character varying(65),
    internal_free smallint DEFAULT 0,
    fixed_fee_ti decimal(6,2) UNSIGNED DEFAULT 0,
    data_free_fixed_fee smallint DEFAULT 0,

);

CREATE TABLE IF NOT EXISTS #__sdi_category (
    id serial NOT NULL ,
    guid character varying(36) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    alias character varying(150),
    description character varying(500),
    name character varying(255) NOT NULL,
    access integer NOT NULL,
    asset_id integer NOT NULL,
    overall_fee DECIMAL(6,2) UNSIGNED DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS #__sdi_organism_category (
    id serial NOT NULL ,
    organism_id integer NOT NULL references #__sdi_organism(id),
    category_id integer NOT NULL references #__sdi_category(id)
);


CREATE TABLE #__sdi_perimeter (
    id serial NOT NULL ,
    guid character varying(255) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
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


CREATE TABLE #__sdi_physicalservice (
    id serial NOT NULL ,
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


CREATE TABLE #__sdi_physicalservice_organism (
    id serial NOT NULL ,
    physicalservice_id bigint NOT NULL,
    organism_id bigint NOT NULL
);


CREATE TABLE #__sdi_physicalservice_policy (
    id serial NOT NULL ,
    prefix character varying(255),
    namespace character varying(255),
    anyitem integer DEFAULT 1 NOT NULL,
    inheritedspatialpolicy integer DEFAULT 1 NOT NULL,
    csw_spatialpolicy_id bigint,
    wms_spatialpolicy_id bigint,
    wmts_spatialpolicy_id bigint,
    wfs_spatialpolicy_id bigint,
    physicalservice_id bigint NOT NULL,
    policy_id bigint NOT NULL
);


CREATE TABLE #__sdi_physicalservice_servicecompliance (
    id serial NOT NULL ,
    service_id bigint NOT NULL,
    servicecompliance_id bigint NOT NULL,
    capabilities text
);


CREATE TABLE #__sdi_policy (
    id serial NOT NULL ,
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
    anyoperation integer DEFAULT 1 NOT NULL,
    anyservice integer DEFAULT 1 NOT NULL,
    accessscope_id bigint DEFAULT 1::bigint NOT NULL,
    virtualservice_id bigint NOT NULL,
    csw_spatialpolicy_id bigint,
    wms_spatialpolicy_id bigint,
    wmts_spatialpolicy_id bigint,
    wfs_spatialpolicy_id bigint,
    csw_version_id bigint DEFAULT 1::bigint NOT NULL,
    csw_anyattribute integer DEFAULT 1 NOT NULL,
    csw_anycontext integer DEFAULT 1 NOT NULL,
    csw_anystate integer DEFAULT 1 NOT NULL,
    csw_anyvisibility integer DEFAULT 1 NOT NULL,
    csw_includeharvested integer DEFAULT 1 NOT NULL,
    csw_anyresourcetype integer DEFAULT 1 NOT NULL,
    csw_accessscope_id integer DEFAULT 1 NOT NULL,
    wms_minimumwidth character varying(255),
    wms_minimumheight character varying(255),
    wms_maximumwidth character varying(255),
    wms_maximumheight character varying(255),
    params character varying(1024),
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);


CREATE TABLE #__sdi_policy_metadatastate (
    id serial NOT NULL ,
    metadatastate_id bigint NOT NULL,
    policy_id bigint NOT NULL,
    metadataversion_id bigint
);


CREATE TABLE #__sdi_policy_organism (
    id serial NOT NULL ,
    policy_id bigint NOT NULL,
    organism_id bigint NOT NULL
);

CREATE TABLE IF NOT EXISTS #__sdi_policy_category (
    id serial NOT NULL ,
    policy_id bigint NOT NULL,
    category_id bigint NOT NULL
);


CREATE TABLE #__sdi_policy_resourcetype (
    id serial NOT NULL ,
    resourcetype_id bigint NOT NULL,
    policy_id bigint NOT NULL
);


CREATE TABLE #__sdi_policy_user (
    id serial NOT NULL ,
    policy_id bigint NOT NULL,
    user_id bigint NOT NULL
);


CREATE TABLE #__sdi_policy_visibility (
    id serial NOT NULL ,
    policy_id bigint NOT NULL,
    user_id bigint,
    organism_id bigint
);


CREATE TABLE #__sdi_profile (
    id serial NOT NULL ,
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


CREATE TABLE #__sdi_property (
    id serial NOT NULL ,
    guid character varying(255) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
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


CREATE TABLE #__sdi_propertyvalue (
    id serial NOT NULL ,
    guid character varying(255) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone NOT NULL,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500) NOT NULL,
    property_id bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE #__sdi_relation (
    id serial NOT NULL ,
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
    issearchfilter integer DEFAULT 0 NOT NULL,
    relationscope_id bigint,
    editorrelationscope_id bigint,
    childresourcetype_id bigint,
    childtype_id bigint,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE #__sdi_relation_catalog (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    relation_id bigint NOT NULL,
    catalog_id bigint NOT NULL
);


CREATE TABLE #__sdi_relation_defaultvalue (
    id serial NOT NULL ,
    relation_id bigint NOT NULL,
    attributevalue_id bigint,
    value character varying(500),
    language_id bigint
);


CREATE TABLE #__sdi_relation_profile (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    relation_id bigint NOT NULL,
    profile_id bigint NOT NULL
);


CREATE TABLE #__sdi_resource (
    id serial NOT NULL ,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
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


CREATE TABLE #__sdi_resourcetype (
    id serial NOT NULL ,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    modified_by integer ,
    modified timestamp(3) without time zone ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(500) NOT NULL,
    logo character varying(255) NOT NULL,
    application integer NOT NULL,
    diffusion integer NOT NULL,
    view integer NOT NULL,
    monitoring integer NOT NULL,
    predefined integer NOT NULL,
    versioning integer NOT NULL,
    profile_id bigint NOT NULL,
    fragmentnamespace_id bigint,
    fragment character varying(255),
    sitemapparams character varying(1000),
    accessscope_id bigint NOT NULL,
    access integer NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);

CREATE TABLE #__sdi_resourcetypelink (
    id serial NOT NULL ,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone NOT NULL,
    modified_by integer ,
    modified timestamp(3) without time zone ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    parent_id bigint NOT NULL,
    child_id bigint NOT NULL,
    parentboundlower integer NOT NULL,
    parentboundupper integer NOT NULL,
    childboundlower integer NOT NULL,
    childboundupper integer NOT NULL,
    viralversioning integer NOT NULL,
    inheritance integer NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE #__sdi_resourcetypelinkinheritance (
    id serial NOT NULL ,
    resourcetypelink_id bigint NOT NULL,
    xpath character varying(500) NOT NULL
);


CREATE TABLE #__sdi_searchcriteria (
    id serial NOT NULL ,
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
    issystem integer DEFAULT 0 NOT NULL,
    criteriatype_id bigint NOT NULL,
    rendertype_id bigint,
    relation_id bigint,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);


CREATE TABLE #__sdi_searchcriteriafilter (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    searchcriteria_id bigint NOT NULL,
    language_id bigint NOT NULL,
    ogcsearchfilter character varying(255) NOT NULL
);

CREATE TABLE #__sdi_sys_accessscope (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_addresstype (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_authenticationconnector (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    authenticationlevel_id bigint NOT NULL,
    value character varying(150) NOT NULL
);



CREATE TABLE #__sdi_sys_authenticationlevel (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);



CREATE TABLE #__sdi_sys_country (
    id serial NOT NULL ,
    ordering bigint DEFAULT 1::bigint NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    name character varying(100) NOT NULL,
    iso2 character varying(2),
    iso3 character varying(3)
);


CREATE TABLE #__sdi_sys_criteriatype (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_entity (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_exceptionlevel (
    id serial NOT NULL ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL
);


CREATE TABLE #__sdi_sys_importtype (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_isolanguage (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_loglevel (
    id serial NOT NULL ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL
);


CREATE TABLE #__sdi_sys_logroll (
    id serial NOT NULL ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL
);


CREATE TABLE #__sdi_sys_maptool (
    id serial NOT NULL ,
    alias character varying(20) NOT NULL,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    name character varying(255) NOT NULL
);


CREATE TABLE #__sdi_sys_metadatastate (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_metadataversion (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_operationcompliance (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    servicecompliance_id bigint NOT NULL,
    serviceoperation_id bigint NOT NULL,
    implemented integer DEFAULT 0 NOT NULL
);


CREATE TABLE #__sdi_sys_orderstate (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_ordertype (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_perimetertype (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_pricing (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_productmining (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_productstate (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_productstorage (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);

CREATE TABLE #__sdi_sys_propertytype (
    id serial NOT NULL ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL
);


CREATE TABLE #__sdi_sys_proxytype (
    id serial NOT NULL ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL
);


CREATE TABLE #__sdi_sys_relationscope (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);

CREATE TABLE #__sdi_sys_relationtype (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_rendertype (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_rendertype_criteriatype (
    id serial NOT NULL ,
    criteriatype_id bigint NOT NULL,
    rendertype_id bigint NOT NULL
);

CREATE TABLE #__sdi_sys_rendertype_stereotype (
    id serial NOT NULL ,
    stereotype_id bigint NOT NULL,
    rendertype_id bigint NOT NULL
);


CREATE TABLE #__sdi_sys_role (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);


CREATE TABLE #__sdi_sys_searchtab (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);

CREATE TABLE #__sdi_sys_servicecompliance (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    serviceconnector_id bigint NOT NULL,
    serviceversion_id bigint NOT NULL,
    implemented integer DEFAULT 0 NOT NULL,
    relayable integer DEFAULT 0 NOT NULL,
    aggregatable integer DEFAULT 0 NOT NULL,
    harvestable integer DEFAULT 0 NOT NULL
);

CREATE TABLE #__sdi_sys_servicecon_authenticationcon (
    id serial NOT NULL ,
    serviceconnector_id bigint NOT NULL,
    authenticationconnector_id bigint NOT NULL
);







CREATE TABLE #__sdi_sys_serviceconnector (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE #__sdi_sys_serviceoperation (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE #__sdi_sys_servicescope (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE #__sdi_sys_servicetype (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE #__sdi_sys_serviceversion (
    id serial NOT NULL ,
    ordering integer,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE #__sdi_sys_spatialoperator (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE #__sdi_sys_stereotype (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(255) NOT NULL,
    defaultpattern character varying(255),
    isocode character varying(255),
    namespace_id bigint,
    entity_id bigint
);







CREATE TABLE #__sdi_sys_topiccategory (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE #__sdi_sys_unit (
    id serial NOT NULL ,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    alias character varying(50) NOT NULL,
    name character varying(255) NOT NULL
);







CREATE TABLE #__sdi_sys_versiontype (
    id serial NOT NULL ,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
);







CREATE TABLE #__sdi_tilematrix_policy (
    id serial NOT NULL ,
    tilematrixsetpolicy_id bigint NOT NULL,
    identifier character varying(255) NOT NULL,
    tileminrow integer,
    tilemaxrow integer,
    tilemincol integer,
    tilemaxcol integer,
    anytile integer DEFAULT 1 NOT NULL
);







CREATE TABLE #__sdi_tilematrixset_policy (
    id serial NOT NULL ,
    wmtslayerpolicy_id bigint NOT NULL,
    identifier character varying(255) NOT NULL,
    anytilematrix integer DEFAULT 1 NOT NULL,
    srssource character varying(255)
);







CREATE TABLE #__sdi_translation (
    id serial NOT NULL ,
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
    text2 character varying(500),
    text3 character varying(255)
);







CREATE TABLE #__sdi_user (
    id serial NOT NULL ,
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
    notificationrequesttreatment integer DEFAULT 1 NOT NULL,
    catid integer,
    params character varying(1024),
    access integer DEFAULT 1 NOT NULL,
    asset_id integer
);







CREATE TABLE #__sdi_user_role_organism (
    id serial NOT NULL ,
    user_id bigint,
    role_id bigint,
    organism_id bigint
);







CREATE TABLE #__sdi_user_role_resource (
    id serial NOT NULL ,
    user_id bigint,
    role_id bigint,
    resource_id bigint
);







CREATE TABLE #__sdi_version (
    id serial NOT NULL ,
    guid character varying(36) NOT NULL,
    alias character varying(50) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    name character varying(255) NOT NULL,
    resource_id bigint NOT NULL,
    access integer DEFAULT 1 NOT NULL,
    asset_id bigint DEFAULT 0::bigint NOT NULL
);







CREATE TABLE #__sdi_versionlink (
    id serial NOT NULL ,
    parent_id bigint NOT NULL,
    child_id bigint NOT NULL
);







CREATE TABLE #__sdi_virtual_physical (
    id serial NOT NULL ,
    virtualservice_id bigint NOT NULL,
    physicalservice_id bigint NOT NULL
);







CREATE TABLE #__sdi_virtualmetadata (
    id serial NOT NULL ,
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
    inheritedtitle integer DEFAULT 1 NOT NULL,
    summary character varying(255),
    inheritedsummary integer DEFAULT 1 NOT NULL,
    keyword character varying(255),
    inheritedkeyword integer DEFAULT 1 NOT NULL,
    fee character varying(255),
    inheritedfee integer DEFAULT 1 NOT NULL,
    accessconstraint character varying(255),
    inheritedaccessconstraint integer DEFAULT 1 NOT NULL,
    inheritedcontact integer DEFAULT 1 NOT NULL,
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







CREATE TABLE #__sdi_virtualservice (
    id serial NOT NULL ,
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
    reflectedmetadata integer DEFAULT 0 NOT NULL,
    xsltfilename character varying(255),
    logpath character varying(255) NOT NULL,
    harvester integer DEFAULT 0 NOT NULL,
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







CREATE TABLE #__sdi_virtualservice_organism (
    id serial NOT NULL ,
    virtualservice_id bigint NOT NULL,
    organism_id bigint NOT NULL
);







CREATE TABLE #__sdi_virtualservice_servicecompliance (
    id serial NOT NULL ,
    service_id bigint NOT NULL,
    servicecompliance_id bigint NOT NULL
);







CREATE TABLE #__sdi_visualization (
    id serial NOT NULL ,
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







CREATE TABLE #__sdi_wfs_spatialpolicy (
    id serial NOT NULL ,
    localgeographicfilter text,
    remotegeographicfilter text
);







CREATE TABLE #__sdi_wms_spatialpolicy (
    id serial NOT NULL ,
    maxx numeric(18,6),
    maxy numeric(18,6),
    minx numeric(18,6),
    miny numeric(18,6),
    geographicfilter text,
    maximumscale integer,
    minimumscale integer,
    srssource character varying(255)
);







CREATE TABLE #__sdi_wmslayer_policy (
    id serial NOT NULL ,
    name character varying(255) NOT NULL,
    description character varying(255) NOT NULL,
    enabled integer DEFAULT 0 NOT NULL,
    inheritedspatialpolicy integer DEFAULT 1 NOT NULL,
    spatialpolicy_id bigint,
    physicalservicepolicy_id bigint NOT NULL
);







CREATE TABLE #__sdi_wmts_spatialpolicy (
    id serial NOT NULL ,
    spatialoperator_id bigint DEFAULT 1::bigint NOT NULL,
    eastboundlongitude numeric(10,6),
    westboundlongitude numeric(10,6),
    northboundlatitude numeric(10,6),
    southboundlatitude numeric(10,6)
);







CREATE TABLE #__sdi_wmtslayer_policy (
    id serial NOT NULL ,
    identifier character varying(255) NOT NULL,
    enabled integer DEFAULT 1 NOT NULL,
    inheritedspatialpolicy integer DEFAULT 1 NOT NULL,
    spatialpolicy_id bigint,
    anytilematrixset integer DEFAULT 1 NOT NULL,
    physicalservicepolicy_id bigint NOT NULL
);







CREATE TABLE last_ids (
    "TABLE_NAME" character varying(255) NOT NULL,
    "LAST_ID" integer DEFAULT 0 NOT NULL
);







CREATE TABLE last_query_results (
    "ID_LAST_QUERY_RESULT" serial NOT NULL,
    "ID_QUERY" bigint NOT NULL,
    "DATA" bytea,
    "XML_RESULT" text,
    "TEXT_RESULT" text,
    "PICTURE_URL" character varying(1000),
    "CONTENT_TYPE" character varying(100)
);







CREATE TABLE log_entries (
    "ID_LOG_ENTRY" serial NOT NULL,
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
    "ID_OVERVIEW_PAGE" serial NOT NULL,
    "NAME" character varying(255) NOT NULL,
    "IS_PUBLIC" integer DEFAULT 0 NOT NULL
);







CREATE TABLE overview_queries (
    "ID_OVERVIEW_QUERY" bigint NOT NULL,
    "ID_OVERVIEW_PAGE" bigint NOT NULL,
    "ID_QUERY" bigint NOT NULL
);







CREATE TABLE periods (
    "ID_PERIODS" serial NOT NULL,
    "ID_SLA" bigint NOT NULL,
    "NAME" character varying(45),
    "MONDAY" integer DEFAULT 0,
    "TUESDAY" integer DEFAULT 0,
    "WEDNESDAY" integer DEFAULT 0,
    "THURSDAY" integer DEFAULT 0,
    "FRIDAY" integer DEFAULT 0,
    "SATURDAY" integer DEFAULT 0,
    "SUNDAY" integer DEFAULT 0,
    "HOLIDAYS" integer DEFAULT 0,
    "SLA_START_TIME" time without time zone NOT NULL,
    "SLA_END_TIME" time without time zone NOT NULL,
    "INCLUDE" integer DEFAULT 0,
    "DATE" character varying(45)
);







CREATE TABLE queries (
    "ID_QUERY" serial NOT NULL,
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
    "ID_QUERY" serial NOT NULL,
    "NAME" character varying(45) NOT NULL,
    "VALUE" text
);







CREATE TABLE query_validation_results (
    "ID_QUERY_VALIDATION_RESULT" integer NOT NULL,
    "ID_QUERY" bigint NOT NULL,
    "SIZE_VALIDATION_RESULT" integer,
    "RESPONSE_SIZE" double precision,
    "TIME_VALIDATION_RESULT" integer,
    "DELIVERY_TIME" double precision,
    "XPATH_VALIDATION_RESULT" integer,
    "XPATH_VALIDATION_OUTPUT" character varying(1000)
);







CREATE TABLE query_validation_settings (
    "ID_QUERY_VALIDATION_SETTINGS" integer NOT NULL,
    "ID_QUERY" bigint NOT NULL,
    "USE_SIZE_VALIDATION" integer DEFAULT 0 NOT NULL,
    "NORM_SIZE" double precision,
    "NORM_SIZE_TOLERANCE" double precision,
    "USE_TIME_VALIDATION" integer DEFAULT 0 NOT NULL,
    "NORM_TIME" double precision,
    "USE_XPATH_VALIDATION" integer DEFAULT 0 NOT NULL,
    "XPATH_EXPRESSION" character varying(1000),
    "XPATH_EXPECTED_OUTPUT" character varying(1000)
);







CREATE TABLE roles (
    "ID_ROLE" serial NOT NULL,
    "NAME" character varying(45) NOT NULL,
    "RANK" bigint NOT NULL
);







CREATE TABLE service_methods (
    "ID_SERVICE_METHOD" serial NOT NULL,
    "NAME" character varying(45) NOT NULL
);







CREATE TABLE service_types (
    "ID_SERVICE_TYPE" serial NOT NULL,
    "NAME" character varying(20) NOT NULL,
    "VERSION" character varying(10) NOT NULL
);







CREATE TABLE service_types_methods (
    "ID_SERVICE_TYPE" bigint NOT NULL,
    "ID_SERVICE_METHOD" bigint NOT NULL
);







CREATE TABLE sla (
    "ID_SLA" serial NOT NULL,
    "NAME" character varying(45) NOT NULL,
    "EXCLUDE_WORST" integer DEFAULT 0,
    "MEASURE_TIME_TO_FIRST" integer DEFAULT 0
);







CREATE TABLE statuses (
    "ID_STATUS" serial NOT NULL,
    "NAME" character varying(45) NOT NULL
);







CREATE TABLE users (
    "LOGIN" character varying(45) NOT NULL,
    "PASSWORD" character varying(45) NOT NULL,
    "ID_ROLE" bigint,
    "EXPIRATION" date,
    "ENABLED" integer DEFAULT 1 NOT NULL,
    "LOCKED" integer DEFAULT 0 NOT NULL
);




