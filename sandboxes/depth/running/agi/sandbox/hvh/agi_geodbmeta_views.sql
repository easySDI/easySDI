USE agi_geoportal;
SET FOREIGN_KEY_CHECKS = 0;

/* START OF SCRIPT */

/* CORE */

DROP TABLE IF EXISTS jos_sdi_account;
DROP VIEW IF EXISTS jos_sdi_account;
CREATE VIEW jos_sdi_account AS SELECT * FROM agi_geodbmeta.jos_sdi_account;

DROP TABLE IF EXISTS jos_sdi_account_accountprofile;
DROP VIEW IF EXISTS jos_sdi_account_accountprofile;
CREATE VIEW jos_sdi_account_accountprofile AS SELECT * FROM agi_geodbmeta.jos_sdi_account_accountprofile;

DROP TABLE IF EXISTS jos_sdi_account_attribute;
DROP VIEW IF EXISTS jos_sdi_account_attribute;
CREATE VIEW jos_sdi_account_attribute AS SELECT * FROM agi_geodbmeta.jos_sdi_account_attribute;

DROP TABLE IF EXISTS jos_sdi_accountprofile;
DROP VIEW IF EXISTS jos_sdi_accountprofile;
CREATE VIEW jos_sdi_accountprofile AS SELECT * FROM agi_geodbmeta.jos_sdi_accountprofile;

DROP TABLE IF EXISTS jos_sdi_attribute;
DROP VIEW IF EXISTS jos_sdi_attribute;
CREATE VIEW jos_sdi_attribute AS SELECT * FROM agi_geodbmeta.jos_sdi_attribute;

DROP TABLE IF EXISTS jos_sdi_account_class;
DROP VIEW IF EXISTS jos_sdi_account_class;
CREATE VIEW jos_sdi_account_class AS SELECT * FROM agi_geodbmeta.jos_sdi_account_class;

DROP TABLE IF EXISTS jos_sdi_account_codevalue;
DROP VIEW IF EXISTS jos_sdi_account_codevalue;
CREATE VIEW jos_sdi_account_codevalue AS SELECT * FROM agi_geodbmeta.jos_sdi_account_codevalue;

DROP TABLE IF EXISTS jos_sdi_account_object;
DROP VIEW IF EXISTS jos_sdi_account_object;
CREATE VIEW jos_sdi_account_object AS SELECT * FROM agi_geodbmeta.jos_sdi_account_object;

DROP TABLE IF EXISTS jos_sdi_account_objecttype;
DROP VIEW IF EXISTS jos_sdi_account_objecttype;
CREATE VIEW jos_sdi_account_objecttype AS SELECT * FROM agi_geodbmeta.jos_sdi_account_objecttype;

DROP TABLE IF EXISTS jos_sdi_accountextension;
DROP VIEW IF EXISTS jos_sdi_accountextension;
CREATE VIEW jos_sdi_accountextension AS SELECT * FROM agi_geodbmeta.jos_sdi_accountextension;

DROP TABLE IF EXISTS jos_sdi_accountprofile;
DROP VIEW IF EXISTS jos_sdi_accountprofile;
CREATE VIEW jos_sdi_accountprofile AS SELECT * FROM agi_geodbmeta.jos_sdi_accountprofile;

DROP TABLE IF EXISTS jos_sdi_actor;
DROP VIEW IF EXISTS jos_sdi_actor;
CREATE VIEW jos_sdi_actor AS SELECT * FROM agi_geodbmeta.jos_sdi_actor;

DROP TABLE IF EXISTS jos_sdi_address;
DROP VIEW IF EXISTS jos_sdi_address;
CREATE VIEW jos_sdi_address AS SELECT * FROM agi_geodbmeta.jos_sdi_address;

DROP TABLE IF EXISTS jos_sdi_catalog;
DROP VIEW IF EXISTS jos_sdi_catalog;
CREATE VIEW jos_sdi_catalog AS SELECT * FROM agi_geodbmeta.jos_sdi_catalog;

DROP TABLE IF EXISTS jos_sdi_catalog_objecttype;
DROP VIEW IF EXISTS jos_sdi_catalog_objecttype;
CREATE VIEW jos_sdi_catalog_objecttype AS SELECT * FROM agi_geodbmeta.jos_sdi_catalog_objecttype;

DROP TABLE IF EXISTS jos_sdi_ogcservice;
DROP VIEW IF EXISTS jos_sdi_ogcservice;
CREATE VIEW jos_sdi_ogcservice AS SELECT * FROM agi_geodbmeta.jos_sdi_ogcservice;

DROP TABLE IF EXISTS jos_sdi_ogcversion;
DROP VIEW IF EXISTS jos_sdi_ogcversion;
CREATE VIEW jos_sdi_ogcversion AS SELECT * FROM agi_geodbmeta.jos_sdi_ogcversion;

DROP TABLE IF EXISTS jos_sdi_ogcservice_version;
DROP VIEW IF EXISTS jos_sdi_ogcservice_version;
CREATE VIEW jos_sdi_ogcservice_version AS SELECT * FROM agi_geodbmeta.jos_sdi_ogcservice_version;

/*
DO NOT REPLACE THIS TABLE BY A VIEW BECAUSE THEY HAVE DIFFERENT PATHS BETWEEN THE INTERNET AND THE 
INTRANET WEBSITES FOR KEYS CATALOG_URL, JAVA_BRIDGE_URL AND PROXY_CONFIG

DROP TABLE IF EXISTS jos_sdi_configuration;
DROP VIEW IF EXISTS jos_sdi_configuration;
CREATE VIEW jos_sdi_configuration AS SELECT * FROM agi_geodbmeta.jos_sdi_configuration;
*/

DROP TABLE IF EXISTS jos_sdi_language;
DROP VIEW IF EXISTS jos_sdi_language;
CREATE VIEW jos_sdi_language AS SELECT * FROM agi_geodbmeta.jos_sdi_language;

DROP TABLE IF EXISTS jos_sdi_list_accounttab;
DROP VIEW IF EXISTS jos_sdi_list_accounttab;
CREATE VIEW jos_sdi_list_accounttab AS SELECT * FROM agi_geodbmeta.jos_sdi_list_accounttab;

DROP TABLE IF EXISTS jos_sdi_list_addresstype;
DROP VIEW IF EXISTS jos_sdi_list_addresstype;
CREATE VIEW jos_sdi_list_addresstype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_addresstype;

DROP TABLE IF EXISTS jos_sdi_list_codelang;
DROP VIEW IF EXISTS jos_sdi_list_codelang;
CREATE VIEW jos_sdi_list_codelang AS SELECT * FROM agi_geodbmeta.jos_sdi_list_codelang;

DROP TABLE IF EXISTS jos_sdi_list_country;
DROP VIEW IF EXISTS jos_sdi_list_country;
CREATE VIEW jos_sdi_list_country AS SELECT * FROM agi_geodbmeta.jos_sdi_list_country;

DROP TABLE IF EXISTS jos_sdi_list_catalogtype;
DROP VIEW IF EXISTS jos_sdi_list_catalogtype;
CREATE VIEW jos_sdi_list_catalogtype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_catalogtype;

DROP TABLE IF EXISTS jos_sdi_list_metadatastate;
DROP VIEW IF EXISTS jos_sdi_list_metadatastate;
CREATE VIEW jos_sdi_list_metadatastate AS SELECT * FROM agi_geodbmeta.jos_sdi_list_metadatastate;

DROP TABLE IF EXISTS jos_sdi_list_module;
DROP VIEW IF EXISTS jos_sdi_list_module;
CREATE VIEW jos_sdi_list_module AS SELECT * FROM agi_geodbmeta.jos_sdi_list_module;

DROP TABLE IF EXISTS jos_sdi_list_projection;
DROP VIEW IF EXISTS jos_sdi_list_projection;
CREATE VIEW jos_sdi_list_projection AS SELECT * FROM agi_geodbmeta.jos_sdi_list_projection;

DROP TABLE IF EXISTS jos_sdi_list_role;
DROP VIEW IF EXISTS jos_sdi_list_role;
CREATE VIEW jos_sdi_list_role AS SELECT * FROM agi_geodbmeta.jos_sdi_list_role;

DROP TABLE IF EXISTS jos_sdi_list_roletype;
DROP VIEW IF EXISTS jos_sdi_list_roletype;
CREATE VIEW jos_sdi_list_roletype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_roletype;

DROP TABLE IF EXISTS jos_sdi_list_tablocation;
DROP VIEW IF EXISTS jos_sdi_list_tablocation;
CREATE VIEW jos_sdi_list_tablocation AS SELECT * FROM agi_geodbmeta.jos_sdi_list_tablocation;

DROP TABLE IF EXISTS jos_sdi_list_visibility;
DROP VIEW IF EXISTS jos_sdi_list_visibility;
CREATE VIEW jos_sdi_list_visibility AS SELECT * FROM agi_geodbmeta.jos_sdi_list_visibility;

DROP TABLE IF EXISTS jos_sdi_metadata;
DROP VIEW IF EXISTS jos_sdi_metadata;
CREATE VIEW jos_sdi_metadata AS SELECT * FROM agi_geodbmeta.jos_sdi_metadata;

DROP TABLE IF EXISTS jos_sdi_module_panel;
DROP VIEW IF EXISTS jos_sdi_module_panel;
CREATE VIEW jos_sdi_module_panel AS SELECT * FROM agi_geodbmeta.jos_sdi_module_panel;

DROP TABLE IF EXISTS jos_sdi_object;
DROP VIEW IF EXISTS jos_sdi_object;
CREATE VIEW jos_sdi_object AS SELECT * FROM agi_geodbmeta.jos_sdi_object;

DROP TABLE IF EXISTS jos_sdi_objecttype;
DROP VIEW IF EXISTS jos_sdi_objecttype;
CREATE VIEW jos_sdi_objecttype AS SELECT * FROM agi_geodbmeta.jos_sdi_objecttype;

DROP TABLE IF EXISTS jos_sdi_revision;
DROP VIEW IF EXISTS jos_sdi_revision;
CREATE VIEW jos_sdi_revision AS SELECT * FROM agi_geodbmeta.jos_sdi_revision;

DROP TABLE IF EXISTS jos_sdi_systemaccount;
DROP VIEW IF EXISTS jos_sdi_systemaccount;
CREATE VIEW jos_sdi_systemaccount AS SELECT * FROM agi_geodbmeta.jos_sdi_systemaccount;

DROP TABLE IF EXISTS jos_sdi_title;
DROP VIEW IF EXISTS jos_sdi_title;
CREATE VIEW jos_sdi_title AS SELECT * FROM agi_geodbmeta.jos_sdi_title;

/* CATALOG */

DROP TABLE IF EXISTS jos_sdi_list_attributetype;
DROP VIEW IF EXISTS jos_sdi_list_attributetype;


DROP TABLE IF EXISTS jos_sdi_list_relationtype;
DROP VIEW IF EXISTS jos_sdi_list_relationtype;
CREATE VIEW jos_sdi_list_relationtype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_relationtype;

DROP TABLE IF EXISTS jos_sdi_list_rendertype;
DROP VIEW IF EXISTS jos_sdi_list_rendertype;
CREATE VIEW jos_sdi_list_rendertype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_rendertype;

DROP TABLE IF EXISTS jos_sdi_list_renderattributetype;
DROP VIEW IF EXISTS jos_sdi_list_renderattributetype;
CREATE VIEW jos_sdi_list_renderattributetype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_renderattributetype;

DROP TABLE IF EXISTS jos_sdi_list_rendercriteriatype;
DROP VIEW IF EXISTS jos_sdi_list_rendercriteriatype;
CREATE VIEW jos_sdi_list_rendercriteriatype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_rendercriteriatype;

DROP TABLE IF EXISTS jos_sdi_profile;
DROP VIEW IF EXISTS jos_sdi_profile;
CREATE VIEW jos_sdi_profile AS SELECT * FROM agi_geodbmeta.jos_sdi_profile;

DROP TABLE IF EXISTS jos_sdi_class;
DROP VIEW IF EXISTS jos_sdi_class;
CREATE VIEW jos_sdi_class AS SELECT * FROM agi_geodbmeta.jos_sdi_class;

DROP TABLE IF EXISTS jos_sdi_attribute;
DROP VIEW IF EXISTS jos_sdi_attribute;
CREATE VIEW jos_sdi_attribute AS SELECT * FROM agi_geodbmeta.jos_sdi_attribute;

DROP TABLE IF EXISTS jos_sdi_codevalue;
DROP VIEW IF EXISTS jos_sdi_codevalue;
CREATE VIEW jos_sdi_codevalue AS SELECT * FROM agi_geodbmeta.jos_sdi_codevalue;

DROP TABLE IF EXISTS jos_sdi_translation;
DROP VIEW IF EXISTS jos_sdi_translation;
CREATE VIEW jos_sdi_translation AS SELECT * FROM agi_geodbmeta.jos_sdi_translation;

DROP TABLE IF EXISTS jos_sdi_defaultvalue;
DROP VIEW IF EXISTS jos_sdi_defaultvalue;
CREATE VIEW jos_sdi_defaultvalue AS SELECT * FROM agi_geodbmeta.jos_sdi_defaultvalue;

DROP TABLE IF EXISTS jos_sdi_relation;
DROP VIEW IF EXISTS jos_sdi_relation;
CREATE VIEW jos_sdi_relation AS SELECT * FROM agi_geodbmeta.jos_sdi_relation;

DROP TABLE IF EXISTS jos_sdi_relation_profile;
DROP VIEW IF EXISTS jos_sdi_relation_profile;
CREATE VIEW jos_sdi_relation_profile AS SELECT * FROM agi_geodbmeta.jos_sdi_relation_profile;

DROP TABLE IF EXISTS jos_sdi_manager_object;
DROP VIEW IF EXISTS jos_sdi_manager_object;
CREATE VIEW jos_sdi_manager_object AS SELECT * FROM agi_geodbmeta.jos_sdi_manager_object;

DROP TABLE IF EXISTS jos_sdi_editor_object;
DROP VIEW IF EXISTS jos_sdi_editor_object;
CREATE VIEW jos_sdi_editor_object AS SELECT * FROM agi_geodbmeta.jos_sdi_editor_object;

DROP TABLE IF EXISTS jos_sdi_boundary;
DROP VIEW IF EXISTS jos_sdi_boundary;
CREATE VIEW jos_sdi_boundary AS SELECT * FROM agi_geodbmeta.jos_sdi_boundary;

DROP TABLE IF EXISTS jos_sdi_history_assign;
DROP VIEW IF EXISTS jos_sdi_history_assign;
CREATE VIEW jos_sdi_history_assign AS SELECT * FROM agi_geodbmeta.jos_sdi_history_assign;

DROP TABLE IF EXISTS jos_sdi_namespace;
DROP VIEW IF EXISTS jos_sdi_namespace;
CREATE VIEW jos_sdi_namespace AS SELECT * FROM agi_geodbmeta.jos_sdi_namespace;

DROP TABLE IF EXISTS jos_sdi_list_topiccategory;
DROP VIEW IF EXISTS jos_sdi_list_topiccategory;
CREATE VIEW jos_sdi_list_topiccategory AS SELECT * FROM agi_geodbmeta.jos_sdi_list_topiccategory;

DROP TABLE IF EXISTS jos_sdi_importref;
DROP VIEW IF EXISTS jos_sdi_importref;
CREATE VIEW jos_sdi_importref AS SELECT * FROM agi_geodbmeta.jos_sdi_importref;

DROP TABLE IF EXISTS jos_sdi_list_importtype;
DROP VIEW IF EXISTS jos_sdi_list_importtype;
CREATE VIEW jos_sdi_list_importtype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_importtype;

DROP TABLE IF EXISTS jos_sdi_objecttypelink;
DROP VIEW IF EXISTS jos_sdi_objecttypelink;
CREATE VIEW jos_sdi_objecttypelink AS SELECT * FROM agi_geodbmeta.jos_sdi_objecttypelink;

DROP TABLE IF EXISTS jos_sdi_objectversion;
DROP VIEW IF EXISTS jos_sdi_objectversion;
CREATE VIEW jos_sdi_objectversion AS SELECT * FROM agi_geodbmeta.jos_sdi_objectversion;

DROP TABLE IF EXISTS jos_sdi_objectversionlink;
DROP VIEW IF EXISTS jos_sdi_objectversionlink;
CREATE VIEW jos_sdi_objectversionlink AS SELECT * FROM agi_geodbmeta.jos_sdi_objectversionlink;

DROP TABLE IF EXISTS jos_sdi_context;
DROP VIEW IF EXISTS jos_sdi_context;
CREATE VIEW jos_sdi_context AS SELECT * FROM agi_geodbmeta.jos_sdi_context;

DROP TABLE IF EXISTS jos_sdi_context_objecttype;
DROP VIEW IF EXISTS jos_sdi_context_objecttype;
CREATE VIEW jos_sdi_context_objecttype AS SELECT * FROM agi_geodbmeta.jos_sdi_context_objecttype;

DROP TABLE IF EXISTS jos_sdi_list_criteriatype;
DROP VIEW IF EXISTS jos_sdi_list_criteriatype;
CREATE VIEW jos_sdi_list_criteriatype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_criteriatype;

DROP TABLE IF EXISTS jos_sdi_searchcriteria;
DROP VIEW IF EXISTS jos_sdi_searchcriteria;
CREATE VIEW jos_sdi_searchcriteria AS SELECT * FROM agi_geodbmeta.jos_sdi_searchcriteria;

DROP TABLE IF EXISTS jos_sdi_list_searchtab;
DROP VIEW IF EXISTS jos_sdi_list_searchtab;
CREATE VIEW jos_sdi_list_searchtab AS SELECT * FROM agi_geodbmeta.jos_sdi_list_searchtab;

DROP TABLE IF EXISTS jos_sdi_searchcriteria_tab;
DROP VIEW IF EXISTS jos_sdi_searchcriteria_tab;
CREATE VIEW jos_sdi_searchcriteria_tab AS SELECT * FROM agi_geodbmeta.jos_sdi_searchcriteria_tab;

DROP TABLE IF EXISTS jos_sdi_context_sc_filter;
DROP VIEW IF EXISTS jos_sdi_context_sc_filter;
CREATE VIEW jos_sdi_context_sc_filter AS SELECT * FROM agi_geodbmeta.jos_sdi_context_sc_filter;

DROP TABLE IF EXISTS jos_sdi_context_sort;
DROP VIEW IF EXISTS jos_sdi_context_sort;
CREATE VIEW jos_sdi_context_sort AS SELECT * FROM agi_geodbmeta.jos_sdi_context_sort;

DROP TABLE IF EXISTS jos_sdi_relation_context;
DROP VIEW IF EXISTS jos_sdi_relation_context;
CREATE VIEW jos_sdi_relation_context AS SELECT * FROM agi_geodbmeta.jos_sdi_relation_context;

DROP TABLE IF EXISTS jos_sdi_application;
DROP VIEW IF EXISTS jos_sdi_application;
CREATE VIEW jos_sdi_application AS SELECT * FROM agi_geodbmeta.jos_sdi_application;

DROP TABLE IF EXISTS jos_sdi_xqueryreport;
DROP VIEW IF EXISTS jos_sdi_xqueryreport;
CREATE VIEW jos_sdi_xqueryreport AS SELECT * FROM agi_geodbmeta.jos_sdi_xqueryreport;

DROP TABLE IF EXISTS jos_sdi_xqueryreportadmin;
DROP VIEW IF EXISTS jos_sdi_xqueryreportadmin;
CREATE VIEW jos_sdi_xqueryreportadmin AS SELECT * FROM agi_geodbmeta.jos_sdi_xqueryreportadmin;

DROP TABLE IF EXISTS jos_sdi_xqueryreportassignation;
DROP VIEW IF EXISTS jos_sdi_xqueryreportassignation;
CREATE VIEW jos_sdi_xqueryreportassignation AS SELECT * FROM agi_geodbmeta.jos_sdi_xqueryreportassignation;

DROP TABLE IF EXISTS jos_sdi_context_criteria;
DROP VIEW IF EXISTS jos_sdi_context_criteria;
CREATE VIEW jos_sdi_context_criteria AS SELECT * FROM agi_geodbmeta.jos_sdi_context_criteria;

DROP TABLE IF EXISTS jos_sdi_sys_stereotype;
DROP VIEW IF EXISTS jos_sdi_sys_stereotype;
CREATE VIEW jos_sdi_sys_stereotype AS SELECT * FROM agi_geodbmeta.jos_sdi_sys_stereotype;

DROP TABLE IF EXISTS jos_sdi_sys_attribute;
DROP VIEW IF EXISTS jos_sdi_sys_attribute;
CREATE VIEW jos_sdi_sys_attribute AS SELECT * FROM agi_geodbmeta.jos_sdi_sys_attribute;

DROP TABLE IF EXISTS jos_sdi_relation_attribute;
DROP VIEW IF EXISTS jos_sdi_relation_attribute;
CREATE VIEW jos_sdi_relation_attribute AS SELECT * FROM agi_geodbmeta.jos_sdi_relation_attribute;

DROP TABLE IF EXISTS jos_sdi_sys_fieldproperty;
DROP VIEW IF EXISTS jos_sdi_sys_fieldproperty;
CREATE VIEW jos_sdi_sys_fieldproperty AS SELECT * FROM agi_geodbmeta.jos_sdi_sys_fieldproperty;

DROP TABLE IF EXISTS jos_sdi_boundarycategory;
DROP VIEW IF EXISTS jos_sdi_boundarycategory;
CREATE VIEW jos_sdi_boundarycategory AS SELECT * FROM agi_geodbmeta.jos_sdi_boundarycategory;

DROP TABLE IF EXISTS jos_sdi_objecttypelinkinheritance;
DROP VIEW IF EXISTS jos_sdi_objecttypelinkinheritance;
CREATE VIEW jos_sdi_objecttypelinkinheritance AS SELECT * FROM agi_geodbmeta.jos_sdi_objecttypelinkinheritance;


/* SHOP */

DROP TABLE IF EXISTS jos_sdi_basemap;
DROP VIEW IF EXISTS jos_sdi_basemap;
CREATE VIEW jos_sdi_basemap AS SELECT * FROM agi_geodbmeta.jos_sdi_basemap;

DROP TABLE IF EXISTS jos_sdi_basemapcontent;
DROP VIEW IF EXISTS jos_sdi_basemapcontent;
CREATE VIEW jos_sdi_basemapcontent AS SELECT * FROM agi_geodbmeta.jos_sdi_basemapcontent;

DROP TABLE IF EXISTS jos_sdi_favorite;
DROP VIEW IF EXISTS jos_sdi_favorite;
CREATE VIEW jos_sdi_favorite AS SELECT * FROM agi_geodbmeta.jos_sdi_favorite;

DROP TABLE IF EXISTS jos_sdi_list_orderstatus;
DROP VIEW IF EXISTS jos_sdi_list_orderstatus;
CREATE VIEW jos_sdi_list_orderstatus AS SELECT * FROM agi_geodbmeta.jos_sdi_list_orderstatus;

DROP TABLE IF EXISTS jos_sdi_list_ordertype;
DROP VIEW IF EXISTS jos_sdi_list_ordertype;
CREATE VIEW jos_sdi_list_ordertype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_ordertype;

DROP TABLE IF EXISTS jos_sdi_list_productstatus;
DROP VIEW IF EXISTS jos_sdi_list_productstatus;
CREATE VIEW jos_sdi_list_productstatus AS SELECT * FROM agi_geodbmeta.jos_sdi_list_productstatus;

DROP TABLE IF EXISTS jos_sdi_list_treatmenttype;
DROP VIEW IF EXISTS jos_sdi_list_treatmenttype;
CREATE VIEW jos_sdi_list_treatmenttype AS SELECT * FROM agi_geodbmeta.jos_sdi_list_treatmenttype;

DROP TABLE IF EXISTS jos_sdi_location;
DROP VIEW IF EXISTS jos_sdi_location;
CREATE VIEW jos_sdi_location AS SELECT * FROM agi_geodbmeta.jos_sdi_location;

DROP TABLE IF EXISTS jos_sdi_order;
DROP VIEW IF EXISTS jos_sdi_order;
CREATE VIEW jos_sdi_order AS SELECT * FROM agi_geodbmeta.jos_sdi_order;

DROP TABLE IF EXISTS jos_sdi_order_perimeter;
DROP VIEW IF EXISTS jos_sdi_order_perimeter;
CREATE VIEW jos_sdi_order_perimeter AS SELECT * FROM agi_geodbmeta.jos_sdi_order_perimeter;

DROP TABLE IF EXISTS jos_sdi_order_product;
DROP VIEW IF EXISTS jos_sdi_order_product;
CREATE VIEW jos_sdi_order_product AS SELECT * FROM agi_geodbmeta.jos_sdi_order_product;

DROP TABLE IF EXISTS jos_sdi_order_property;
DROP VIEW IF EXISTS jos_sdi_order_property;
CREATE VIEW jos_sdi_order_property AS SELECT * FROM agi_geodbmeta.jos_sdi_order_property;

DROP TABLE IF EXISTS jos_sdi_orderproduct_file;
DROP VIEW IF EXISTS jos_sdi_orderproduct_file;
CREATE VIEW jos_sdi_orderproduct_file AS SELECT * FROM agi_geodbmeta.jos_sdi_orderproduct_file;

DROP TABLE IF EXISTS jos_sdi_perimeter;
DROP VIEW IF EXISTS jos_sdi_perimeter;
CREATE VIEW jos_sdi_perimeter AS SELECT * FROM agi_geodbmeta.jos_sdi_perimeter;

DROP TABLE IF EXISTS jos_sdi_product;
DROP VIEW IF EXISTS jos_sdi_product;
CREATE VIEW jos_sdi_product AS SELECT * FROM agi_geodbmeta.jos_sdi_product;

DROP TABLE IF EXISTS jos_sdi_product_file;
DROP VIEW IF EXISTS jos_sdi_product_file;
CREATE VIEW jos_sdi_product_file AS SELECT * FROM agi_geodbmeta.jos_sdi_product_file;

DROP TABLE IF EXISTS jos_sdi_product_perimeter;
DROP VIEW IF EXISTS jos_sdi_product_perimeter;
CREATE VIEW jos_sdi_product_perimeter AS SELECT * FROM agi_geodbmeta.jos_sdi_product_perimeter;

DROP TABLE IF EXISTS jos_sdi_product_property;
DROP VIEW IF EXISTS jos_sdi_product_property;
CREATE VIEW jos_sdi_product_property AS SELECT * FROM agi_geodbmeta.jos_sdi_product_property;

DROP TABLE IF EXISTS jos_sdi_property;
DROP VIEW IF EXISTS jos_sdi_property;
CREATE VIEW jos_sdi_property AS SELECT * FROM agi_geodbmeta.jos_sdi_property;

DROP TABLE IF EXISTS jos_sdi_propertyvalue;
DROP VIEW IF EXISTS jos_sdi_propertyvalue;
CREATE VIEW jos_sdi_propertyvalue AS SELECT * FROM agi_geodbmeta.jos_sdi_propertyvalue;

DROP TABLE IF EXISTS jos_sdi_list_accessibility;
DROP VIEW IF EXISTS jos_sdi_list_accessibility;
CREATE VIEW jos_sdi_list_accessibility AS SELECT * FROM agi_geodbmeta.jos_sdi_list_accessibility;

DROP TABLE IF EXISTS jos_sdi_product_account;
DROP VIEW IF EXISTS jos_sdi_product_account;
CREATE VIEW jos_sdi_product_account AS SELECT * FROM agi_geodbmeta.jos_sdi_product_account;

DROP TABLE IF EXISTS jos_sdi_grid;
DROP VIEW IF EXISTS jos_sdi_grid;
CREATE VIEW jos_sdi_grid AS SELECT * FROM agi_geodbmeta.jos_sdi_grid;

/* END OF SCRIPT */
SET FOREIGN_KEY_CHECKS = 1;