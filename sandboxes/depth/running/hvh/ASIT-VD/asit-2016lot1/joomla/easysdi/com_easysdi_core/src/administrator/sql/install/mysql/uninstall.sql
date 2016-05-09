SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `#__sdi_sys_unit`;
DROP TABLE IF EXISTS `#__sdi_sys_role`;
DROP TABLE IF EXISTS `#__sdi_sys_country`;
DROP TABLE IF EXISTS `#__sdi_sys_versiontype`;
DROP TABLE IF EXISTS `#__sdi_sys_accessscope`;
DROP TABLE IF EXISTS `#__sdi_sys_metadatastate`;
DROP TABLE IF EXISTS `#__sdi_sys_spatialoperator`;
DROP TABLE IF EXISTS `#__sdi_sys_serviceconnector`;
DROP TABLE IF EXISTS `#__sdi_sys_serviceversion`;
DROP TABLE IF EXISTS `#__sdi_sys_servicecompliance`;
DROP TABLE IF EXISTS `#__sdi_sys_serviceoperation`;
DROP TABLE IF EXISTS `#__sdi_sys_operationcompliance`;
DROP TABLE IF EXISTS `#__sdi_sys_authenticationlevel`;
DROP TABLE IF EXISTS `#__sdi_sys_authenticationconnector`;
DROP TABLE IF EXISTS `#__sdi_sys_logroll`;
DROP TABLE IF EXISTS `#__sdi_sys_loglevel`;
DROP TABLE IF EXISTS `#__sdi_sys_exceptionlevel`;
DROP TABLE IF EXISTS `#__sdi_sys_proxytype`;
DROP TABLE IF EXISTS `#__sdi_sys_servicecon_authenticationcon`;
DROP TABLE IF EXISTS `#__sdi_sys_servicescope`;
DROP TABLE IF EXISTS `#__sdi_sys_metadataversion`;
DROP TABLE IF EXISTS `#__sdi_application`;
DROP TABLE IF EXISTS `#__sdi_language`;
DROP TABLE IF EXISTS `#__sdi_resource`;
DROP TABLE IF EXISTS `#__sdi_resourcetype`;
DROP TABLE IF EXISTS `#__sdi_resourcetypelink`;
DROP TABLE IF EXISTS `#__sdi_resourcetypelinkinheritance`;
DROP TABLE IF EXISTS `#__sdi_sys_criteriatype`;
DROP TABLE IF EXISTS `#__sdi_sys_entity`;
DROP TABLE IF EXISTS `#__sdi_sys_importtype`;
DROP TABLE IF EXISTS `#__sdi_sys_orderstate`;
DROP TABLE IF EXISTS `#__sdi_sys_ordertype`;
DROP TABLE IF EXISTS `#__sdi_sys_productmining`;
DROP TABLE IF EXISTS `#__sdi_sys_productstate`;
DROP TABLE IF EXISTS `#__sdi_sys_relationscope`;
DROP TABLE IF EXISTS `#__sdi_sys_relationtype`;
DROP TABLE IF EXISTS `#__sdi_sys_rendertype`;
DROP TABLE IF EXISTS `#__sdi_sys_rendertype_stereotype`;
DROP TABLE IF EXISTS `#__sdi_sys_rendertype_criteriatype`;
DROP TABLE IF EXISTS `#__sdi_sys_searchtab`;
DROP TABLE IF EXISTS `#__sdi_sys_stereotype`;
DROP TABLE IF EXISTS `#__sdi_sys_topiccategory`;
DROP TABLE IF EXISTS `#__sdi_version`;
DROP TABLE IF EXISTS `#__sdi_versionlink`;
DROP TABLE IF EXISTS `#__sdi_sys_productstorage`;
DROP TABLE IF EXISTS `#__sdi_sys_pricing`;
DROP TABLE IF EXISTS `#__sdi_sys_isolanguage`;
DROP TABLE IF EXISTS `#__sdi_sys_perimetertype`;
DROP TABLE IF EXISTS `#__sdi_sys_propertytype`;
DROP TABLE IF EXISTS `#__sdi_sys_servicetype`;
DROP TABLE IF EXISTS `#__sdi_sys_extractstorage`;

-- com_easysdi_contact

DROP TABLE IF EXISTS `#__sdi_user`;
DROP TABLE IF EXISTS `#__sdi_address`;
DROP TABLE IF EXISTS `#__sdi_sys_addresstype`;
DROP TABLE IF EXISTS `#__sdi_organism`;
DROP TABLE IF EXISTS `#__sdi_category`;
DROP TABLE IF EXISTS `#__sdi_organism_category`;
DROP TABLE IF EXISTS `#__sdi_user_role_organism`;

-- com_easysdi_service

DROP TABLE IF EXISTS `#__sdi_physicalservice_servicecompliance`;
DROP TABLE IF EXISTS `#__sdi_virtualservice_servicecompliance`;
DROP TABLE IF EXISTS `#__sdi_layer`;
DROP TABLE IF EXISTS `#__sdi_physicalservice`;
DROP TABLE IF EXISTS `#__sdi_virtualservice`;
DROP TABLE IF EXISTS `#__sdi_virtualmetadata`;
DROP TABLE IF EXISTS `#__sdi_virtual_physical`;
DROP TABLE IF EXISTS `#__sdi_policy`;
DROP TABLE IF EXISTS `#__sdi_policy_organism`;
DROP TABLE IF EXISTS `#__sdi_policy_category`;
DROP TABLE IF EXISTS `#__sdi_policy_user`;
DROP TABLE IF EXISTS `#__sdi_allowedoperation`;
DROP TABLE IF EXISTS `#__sdi_policy_metadatastate`;
DROP TABLE IF EXISTS `#__sdi_physicalservice_policy`;
DROP TABLE IF EXISTS `#__sdi_csw_spatialpolicy`;
DROP TABLE IF EXISTS `#__sdi_wmts_spatialpolicy`;
DROP TABLE IF EXISTS `#__sdi_wms_spatialpolicy`;
DROP TABLE IF EXISTS `#__sdi_wfs_spatialpolicy`;
DROP TABLE IF EXISTS `#__sdi_excludedattribute`;
DROP TABLE IF EXISTS `#__sdi_wmtslayer_policy`;
DROP TABLE IF EXISTS `#__sdi_tilematrixset_policy`;
DROP TABLE IF EXISTS `#__sdi_tilematrix_policy`;
DROP TABLE IF EXISTS `#__sdi_wmslayer_policy`;
DROP TABLE IF EXISTS `#__sdi_featuretype_policy`;
DROP TABLE IF EXISTS `#__sdi_includedattribute`;
DROP TABLE IF EXISTS `#__sdi_virtualservice_organism`;
DROP TABLE IF EXISTS `#__sdi_physicalservice_organism`;
DROP TABLE IF EXISTS `#__sdi_policy_resourcetype`;
DROP TABLE IF EXISTS `#__sdi_policy_visibility`;

-- com_easysdi_catalog

DROP TABLE IF EXISTS `#__sdi_metadata`;
DROP TABLE IF EXISTS `#__sdi_catalog`;
DROP TABLE IF EXISTS `#__sdi_catalog_resourcetype`;
DROP TABLE IF EXISTS `#__sdi_searchcriteria`;
DROP TABLE IF EXISTS `#__sdi_catalog_searchcriteria`;
DROP TABLE IF EXISTS `#__sdi_profile`;
DROP TABLE IF EXISTS `#__sdi_class`;
DROP TABLE IF EXISTS `#__sdi_attribute`;
DROP TABLE IF EXISTS `#__sdi_attributevalue`;
DROP TABLE IF EXISTS `#__sdi_relation`;
DROP TABLE IF EXISTS `#__sdi_catalog_searchcriteriafilter`;
DROP TABLE IF EXISTS `#__sdi_catalog_searchsort`;
DROP TABLE IF EXISTS `#__sdi_relation_profile`;
DROP TABLE IF EXISTS `#__sdi_relation_catalog`;
DROP TABLE IF EXISTS `#__sdi_boundary`;
DROP TABLE IF EXISTS `#__sdi_boundarycategory`;
DROP TABLE IF EXISTS `#__sdi_catalog_searchcriteria_tab`;
DROP TABLE IF EXISTS `#__sdi_importref`;
DROP TABLE IF EXISTS `#__sdi_translation`;
DROP TABLE IF EXISTS `#__sdi_namespace`;
DROP TABLE IF EXISTS `#__sdi_accessscope`;
DROP TABLE IF EXISTS `#__sdi_assignment`;
DROP TABLE IF EXISTS `#__sdi_searchcriteriafilter`;
DROP TABLE IF EXISTS `#__sdi_user_role_organism`;
DROP TABLE IF EXISTS `#__sdi_user_role_resource`;
DROP TABLE IF EXISTS `#__sdi_relation_defaultvalue`;

-- com_easysdi_map

DROP TABLE IF EXISTS `#__sdi_layergroup`;
DROP TABLE IF EXISTS `#__sdi_maplayer`;
DROP TABLE IF EXISTS `#__sdi_map`;
DROP TABLE IF EXISTS `#__sdi_sys_maptool`;
DROP TABLE IF EXISTS `#__sdi_map_tool`;
DROP TABLE IF EXISTS `#__sdi_map_layergroup`;
DROP TABLE IF EXISTS `#__sdi_map_physicalservice`;
DROP TABLE IF EXISTS `#__sdi_map_virtualservice`;
DROP TABLE IF EXISTS `#__sdi_layer_layergroup`;
DROP TABLE IF EXISTS `#__sdi_visualization`;

-- com_easysdi_shop

DROP TABLE IF EXISTS `#__sdi_diffusion_download`;
DROP TABLE IF EXISTS `#__sdi_diffusion`;
DROP TABLE IF EXISTS `#__sdi_property`;
DROP TABLE IF EXISTS `#__sdi_propertyvalue`;
DROP TABLE IF EXISTS `#__sdi_diffusion_perimeter`;
DROP TABLE IF EXISTS `#__sdi_diffusion_propertyvalue`;
DROP TABLE IF EXISTS `#__sdi_diffusion_notifieduser`;
DROP TABLE IF EXISTS `#__sdi_perimeter`;
DROP TABLE IF EXISTS `#__sdi_grid`;
DROP TABLE IF EXISTS `#__sdi_order`;
DROP TABLE IF EXISTS `#__sdi_order_diffusion`;
DROP TABLE IF EXISTS `#__sdi_order_propertyvalue`;
DROP TABLE IF EXISTS `#__sdi_order_perimeter`;
DROP TABLE IF EXISTS `#__sdi_organism_category_pricing_rebate`;
DROP TABLE IF EXISTS `#__sdi_pricing_profile`;
DROP TABLE IF EXISTS `#__sdi_pricing_profile_category_pricing_rebate`;
DROP TABLE IF EXISTS `#__sdi_pricing_order`;
DROP TABLE IF EXISTS `#__sdi_pricing_order_supplier`;
DROP TABLE IF EXISTS `#__sdi_pricing_order_supplier_product`;
DROP TABLE IF EXISTS `#__sdi_pricing_order_supplier_product_profile`;

-- com_easysdi_monitor

DROP TABLE IF EXISTS `#__sdi_monitor_exports`;
DROP TABLE IF EXISTS `action_types`;
DROP TABLE IF EXISTS `actions`;
DROP TABLE IF EXISTS `alerts`;
DROP TABLE IF EXISTS `holidays`;
DROP TABLE IF EXISTS `http_methods`;
DROP TABLE IF EXISTS `job_agg_hour_log_entries`;
DROP TABLE IF EXISTS `job_agg_log_entries`;
DROP TABLE IF EXISTS `job_defaults`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `last_ids`;
DROP TABLE IF EXISTS `last_query_results`;
DROP TABLE IF EXISTS `log_entries`;
DROP TABLE IF EXISTS `overview_page`;
DROP TABLE IF EXISTS `overview_queries`;
DROP TABLE IF EXISTS `overview_query_view`;
DROP TABLE IF EXISTS `periods`;
DROP TABLE IF EXISTS `queries`;
DROP TABLE IF EXISTS `query_agg_hour_log_entries`;
DROP TABLE IF EXISTS `query_agg_log_entries`;
DROP TABLE IF EXISTS `query_params`;
DROP TABLE IF EXISTS `query_validation_results`;
DROP TABLE IF EXISTS `query_validation_settings`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `service_methods`;
DROP TABLE IF EXISTS `service_types`;
DROP TABLE IF EXISTS `service_types_methods`;
DROP TABLE IF EXISTS `sla`;
DROP TABLE IF EXISTS `statuses`;
DROP TABLE IF EXISTS `users`;

-- com_easysdi_processing
DROP TABLE IF EXISTS `#__sdi_processing`;
DROP TABLE IF EXISTS `#__sdi_processing_obs`;
DROP TABLE IF EXISTS `#__sdi_processing_order`;

SET foreign_key_checks = 1;