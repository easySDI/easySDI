DROP TABLE IF EXISTS "#__sdi_sys_unit" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_role" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_country" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_versiontype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_accessscope" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_metadatastate" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_spatialoperator" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_serviceconnector" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_serviceversion" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_servicecompliance" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_serviceoperation" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_operationcompliance" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_authenticationlevel" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_authenticationconnector" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_logroll" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_loglevel" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_exceptionlevel" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_proxytype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_servicecon_authenticationcon" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_servicescope" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_metadataversion" CASCADE;
DROP TABLE IF EXISTS "#__sdi_application" CASCADE;
DROP TABLE IF EXISTS "#__sdi_language" CASCADE;
DROP TABLE IF EXISTS "#__sdi_resource" CASCADE;
DROP TABLE IF EXISTS "#__sdi_resourcetype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_resourcetypelink" CASCADE;
DROP TABLE IF EXISTS "#__sdi_resourcetypelinkinheritance" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_criteriatype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_entity" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_importtype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_orderstate" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_ordertype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_productmining" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_productstate" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_relationscope" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_relationtype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_rendertype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_rendertype_stereotype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_rendertype_criteriatype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_searchtab" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_stereotype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_topiccategory" CASCADE;
DROP TABLE IF EXISTS "#__sdi_version" CASCADE;
DROP TABLE IF EXISTS "#__sdi_versionlink" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_productstorage" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_pricing" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_isolanguage" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_perimetertype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_propertytype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_servicetype" CASCADE;

-- com_easysdi_contact

DROP TABLE IF EXISTS "#__sdi_user" CASCADE;
DROP TABLE IF EXISTS "#__sdi_address" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_addresstype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_organism" CASCADE;
DROP TABLE IF EXISTS "#__sdi_user_role_organism" CASCADE;

-- com_easysdi_service

DROP TABLE IF EXISTS "#__sdi_physicalservice_servicecompliance" CASCADE;
DROP TABLE IF EXISTS "#__sdi_virtualservice_servicecompliance" CASCADE;
DROP TABLE IF EXISTS "#__sdi_layer" CASCADE;
DROP TABLE IF EXISTS "#__sdi_physicalservice" CASCADE;
DROP TABLE IF EXISTS "#__sdi_virtualservice" CASCADE;
DROP TABLE IF EXISTS "#__sdi_virtualmetadata" CASCADE;
DROP TABLE IF EXISTS "#__sdi_virtual_physical" CASCADE;
DROP TABLE IF EXISTS "#__sdi_policy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_policy_organism" CASCADE;
DROP TABLE IF EXISTS "#__sdi_policy_user" CASCADE;
DROP TABLE IF EXISTS "#__sdi_allowedoperation" CASCADE;
DROP TABLE IF EXISTS "#__sdi_policy_metadatastate" CASCADE;
DROP TABLE IF EXISTS "#__sdi_physicalservice_policy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_csw_spatialpolicy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_wmts_spatialpolicy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_wms_spatialpolicy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_wfs_spatialpolicy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_excludedattribute" CASCADE;
DROP TABLE IF EXISTS "#__sdi_wmtslayer_policy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_tilematrixset_policy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_tilematrix_policy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_wmslayer_policy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_featuretype_policy" CASCADE;
DROP TABLE IF EXISTS "#__sdi_includedattribute" CASCADE;
DROP TABLE IF EXISTS "#__sdi_virtualservice_organism" CASCADE;
DROP TABLE IF EXISTS "#__sdi_physicalservice_organism" CASCADE;
DROP TABLE IF EXISTS "#__sdi_policy_resourcetype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_policy_visibility" CASCADE;

-- com_easysdi_catalog

DROP TABLE IF EXISTS "#__sdi_metadata" CASCADE;
DROP TABLE IF EXISTS "#__sdi_catalog" CASCADE;
DROP TABLE IF EXISTS "#__sdi_catalog_resourcetype" CASCADE;
DROP TABLE IF EXISTS "#__sdi_searchcriteria" CASCADE;
DROP TABLE IF EXISTS "#__sdi_catalog_searchcriteria" CASCADE;
DROP TABLE IF EXISTS "#__sdi_profile" CASCADE;
DROP TABLE IF EXISTS "#__sdi_class" CASCADE;
DROP TABLE IF EXISTS "#__sdi_attribute" CASCADE;
DROP TABLE IF EXISTS "#__sdi_attributevalue" CASCADE;
DROP TABLE IF EXISTS "#__sdi_relation" CASCADE;
DROP TABLE IF EXISTS "#__sdi_catalog_searchcriteriafilter" CASCADE;
DROP TABLE IF EXISTS "#__sdi_catalog_searchsort" CASCADE;
DROP TABLE IF EXISTS "#__sdi_relation_profile" CASCADE;
DROP TABLE IF EXISTS "#__sdi_relation_catalog" CASCADE;
DROP TABLE IF EXISTS "#__sdi_boundary" CASCADE;
DROP TABLE IF EXISTS "#__sdi_boundarycategory" CASCADE;
DROP TABLE IF EXISTS "#__sdi_catalog_searchcriteria_tab" CASCADE;
DROP TABLE IF EXISTS "#__sdi_importref" CASCADE;
DROP TABLE IF EXISTS "#__sdi_translation" CASCADE;
DROP TABLE IF EXISTS "#__sdi_namespace" CASCADE;
DROP TABLE IF EXISTS "#__sdi_accessscope" CASCADE;
DROP TABLE IF EXISTS "#__sdi_assignment" CASCADE;
DROP TABLE IF EXISTS "#__sdi_searchcriteriafilter" CASCADE;
DROP TABLE IF EXISTS "#__sdi_user_role_organism" CASCADE;
DROP TABLE IF EXISTS "#__sdi_user_role_resource" CASCADE;
DROP TABLE IF EXISTS "#__sdi_relation_defaultvalue" CASCADE;

-- com_easysdi_map

DROP TABLE IF EXISTS "#__sdi_layergroup" CASCADE;
DROP TABLE IF EXISTS "#__sdi_maplayer" CASCADE;
DROP TABLE IF EXISTS "#__sdi_map" CASCADE;
DROP TABLE IF EXISTS "#__sdi_sys_maptool" CASCADE;
DROP TABLE IF EXISTS "#__sdi_map_tool" CASCADE;
DROP TABLE IF EXISTS "#__sdi_map_layergroup" CASCADE;
DROP TABLE IF EXISTS "#__sdi_map_physicalservice" CASCADE;
DROP TABLE IF EXISTS "#__sdi_map_virtualservice" CASCADE;
DROP TABLE IF EXISTS "#__sdi_layer_layergroup" CASCADE;
DROP TABLE IF EXISTS "#__sdi_visualization" CASCADE;

-- com_easysdi_shop

DROP TABLE IF EXISTS "#__sdi_diffusion_download" CASCADE;
DROP TABLE IF EXISTS "#__sdi_diffusion" CASCADE;
DROP TABLE IF EXISTS "#__sdi_property" CASCADE;
DROP TABLE IF EXISTS "#__sdi_propertyvalue" CASCADE;
DROP TABLE IF EXISTS "#__sdi_diffusion_perimeter" CASCADE;
DROP TABLE IF EXISTS "#__sdi_diffusion_propertyvalue" CASCADE;
DROP TABLE IF EXISTS "#__sdi_diffusion_notifieduser" CASCADE;
DROP TABLE IF EXISTS "#__sdi_perimeter" CASCADE;
DROP TABLE IF EXISTS "#__sdi_grid" CASCADE;
DROP TABLE IF EXISTS "#__sdi_order" CASCADE;
DROP TABLE IF EXISTS "#__sdi_order_diffusion" CASCADE;
DROP TABLE IF EXISTS "#__sdi_order_propertyvalue" CASCADE;
DROP TABLE IF EXISTS "#__sdi_order_perimeter" CASCADE;

-- com_easysdi_monitor

DROP TABLE IF EXISTS "#__sdi_monitor_exports" CASCADE;
DROP TABLE IF EXISTS "action_types" CASCADE;
DROP TABLE IF EXISTS "actions" CASCADE;
DROP TABLE IF EXISTS "alerts" CASCADE;
DROP TABLE IF EXISTS "holidays" CASCADE;
DROP TABLE IF EXISTS "http_methods" CASCADE;
DROP TABLE IF EXISTS "job_agg_hour_log_entries" CASCADE;
DROP TABLE IF EXISTS "job_agg_log_entries" CASCADE;
DROP TABLE IF EXISTS "job_defaults" CASCADE;
DROP TABLE IF EXISTS "jobs" CASCADE;
DROP TABLE IF EXISTS "last_ids" CASCADE;
DROP TABLE IF EXISTS "last_query_results" CASCADE;
DROP TABLE IF EXISTS "log_entries" CASCADE;
DROP TABLE IF EXISTS "overview_page" CASCADE;
DROP TABLE IF EXISTS "overview_queries" CASCADE;
DROP TABLE IF EXISTS "overview_query_view" CASCADE;
DROP TABLE IF EXISTS "periods" CASCADE;
DROP TABLE IF EXISTS "queries" CASCADE;
DROP TABLE IF EXISTS "query_agg_hour_log_entries" CASCADE;
DROP TABLE IF EXISTS "query_agg_log_entries" CASCADE;
DROP TABLE IF EXISTS "query_params" CASCADE;
DROP TABLE IF EXISTS "query_validation_results" CASCADE;
DROP TABLE IF EXISTS "query_validation_settings" CASCADE;
DROP TABLE IF EXISTS "roles" CASCADE;
DROP TABLE IF EXISTS "service_methods" CASCADE;
DROP TABLE IF EXISTS "service_types" CASCADE;
DROP TABLE IF EXISTS "service_types_methods" CASCADE;
DROP TABLE IF EXISTS "sla" CASCADE;
DROP TABLE IF EXISTS "statuses" CASCADE;
DROP TABLE IF EXISTS "users" CASCADE;

DROP TABLE IF EXISTS "#__sdi_category" CASCADE;
DROP TABLE IF EXISTS "#__sdi_organism_category" CASCADE;
DROP TABLE IF EXISTS "#__sdi_policy_category" CASCADE;
