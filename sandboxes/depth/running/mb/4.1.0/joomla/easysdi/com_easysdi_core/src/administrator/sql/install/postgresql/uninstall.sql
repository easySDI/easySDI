DROP TABLE IF EXISTS "jos_sdi_sys_unit" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_role" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_country" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_versiontype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_accessscope" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_metadatastate" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_spatialoperator" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_serviceconnector" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_serviceversion" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_servicecompliance" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_serviceoperation" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_operationcompliance" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_authenticationlevel" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_authenticationconnector" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_logroll" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_loglevel" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_exceptionlevel" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_proxytype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_servicecon_authenticationcon" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_servicescope" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_metadataversion" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_application" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_language" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_resource" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_resourcetype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_resourcetypelink" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_resourcetypelinkinheritance" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_criteriatype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_entity" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_importtype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_orderstate" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_ordertype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_productmining" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_productstate" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_relationscope" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_relationtype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_rendertype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_rendertype_stereotype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_rendertype_criteriatype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_searchtab" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_stereotype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_topiccategory" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_version" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_versionlink" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_productstorage" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_pricing" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_isolanguage" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_perimetertype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_propertytype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_servicetype" CASCADE;

-- com_easysdi_contact

DROP TABLE IF EXISTS "jos_sdi_user" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_address" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_addresstype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_organism" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_user_role_organism" CASCADE;

-- com_easysdi_service

DROP TABLE IF EXISTS "jos_sdi_physicalservice_servicecompliance" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_virtualservice_servicecompliance" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_layer" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_physicalservice" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_virtualservice" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_virtualmetadata" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_virtual_physical" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_policy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_policy_organism" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_policy_user" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_allowedoperation" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_policy_metadatastate" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_physicalservice_policy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_csw_spatialpolicy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_wmts_spatialpolicy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_wms_spatialpolicy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_wfs_spatialpolicy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_excludedattribute" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_wmtslayer_policy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_tilematrixset_policy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_tilematrix_policy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_wmslayer_policy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_featuretype_policy" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_includedattribute" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_virtualservice_organism" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_physicalservice_organism" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_policy_resourcetype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_policy_visibility" CASCADE;

-- com_easysdi_catalog

DROP TABLE IF EXISTS "jos_sdi_metadata" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_catalog" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_catalog_resourcetype" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_searchcriteria" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_catalog_searchcriteria" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_profile" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_class" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_attribute" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_attributevalue" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_relation" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_catalog_searchcriteriafilter" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_catalog_searchsort" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_relation_profile" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_relation_catalog" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_boundary" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_boundarycategory" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_catalog_searchcriteria_tab" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_importref" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_translation" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_namespace" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_accessscope" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_assignment" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_searchcriteriafilter" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_user_role_organism" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_user_role_resource" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_relation_defaultvalue" CASCADE;

-- com_easysdi_map

DROP TABLE IF EXISTS "jos_sdi_layergroup" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_maplayer" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_map" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_sys_maptool" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_map_tool" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_map_layergroup" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_map_physicalservice" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_map_virtualservice" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_layer_layergroup" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_visualization" CASCADE;

-- com_easysdi_shop

DROP TABLE IF EXISTS "jos_sdi_diffusion_download" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_diffusion" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_property" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_propertyvalue" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_diffusion_perimeter" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_diffusion_propertyvalue" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_diffusion_notifieduser" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_perimeter" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_grid" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_order" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_order_diffusion" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_order_propertyvalue" CASCADE;
DROP TABLE IF EXISTS "jos_sdi_order_perimeter" CASCADE;

-- com_easysdi_monitor

DROP TABLE IF EXISTS "jos_sdi_monitor_exports" CASCADE;
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
