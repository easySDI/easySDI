
ALTER TABLE ONLY jobs
    ADD CONSTRAINT "UNIQUE_NAME" UNIQUE ("NAME");
ALTER TABLE ONLY overview_page
    ADD CONSTRAINT "URL_UNIQUE" UNIQUE ("NAME");
ALTER TABLE ONLY action_types
    ADD CONSTRAINT action_types_pkey PRIMARY KEY ("ID_ACTION_TYPE");
ALTER TABLE ONLY actions
    ADD CONSTRAINT actions_pkey PRIMARY KEY ("ID_ACTION");
ALTER TABLE ONLY alerts
    ADD CONSTRAINT alerts_pkey PRIMARY KEY ("ID_ALERT");
ALTER TABLE ONLY #__sdi_layergroup
    ADD CONSTRAINT alias UNIQUE (alias);
ALTER TABLE ONLY holidays
    ADD CONSTRAINT holidays_pkey PRIMARY KEY ("ID_HOLIDAYS");
ALTER TABLE ONLY http_methods
    ADD CONSTRAINT http_methods_pkey PRIMARY KEY ("ID_HTTP_METHOD");
ALTER TABLE ONLY job_agg_hour_log_entries
    ADD CONSTRAINT job_agg_hour_log_entries_pkey PRIMARY KEY ("DATE_LOG", "ID_JOB");
ALTER TABLE ONLY job_agg_log_entries
    ADD CONSTRAINT job_agg_log_entries_pkey PRIMARY KEY ("DATE_LOG", "ID_JOB");
ALTER TABLE ONLY job_defaults
    ADD CONSTRAINT job_defaults_pkey PRIMARY KEY ("ID_PARAM");
ALTER TABLE ONLY jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY ("ID_JOB");
ALTER TABLE ONLY #__sdi_accessscope
    ADD CONSTRAINT #__sdi_accessscope_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_address
    ADD CONSTRAINT #__sdi_address_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_allowedoperation
    ADD CONSTRAINT #__sdi_allowedoperation_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_application
    ADD CONSTRAINT #__sdi_application_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_assignment
    ADD CONSTRAINT #__sdi_assignment_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_attribute
    ADD CONSTRAINT #__sdi_attribute_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_attributevalue
    ADD CONSTRAINT #__sdi_attributevalue_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_boundary
    ADD CONSTRAINT #__sdi_boundary_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_boundarycategory
    ADD CONSTRAINT #__sdi_boundarycategory_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_catalog
    ADD CONSTRAINT #__sdi_catalog_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_catalog_resourcetype
    ADD CONSTRAINT #__sdi_catalog_resourcetype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_catalog_searchcriteria
    ADD CONSTRAINT #__sdi_catalog_searchcriteria_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_catalog_searchsort
    ADD CONSTRAINT #__sdi_catalog_searchsort_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_class
    ADD CONSTRAINT #__sdi_class_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_csw_spatialpolicy
    ADD CONSTRAINT #__sdi_csw_spatialpolicy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_diffusion_download
    ADD CONSTRAINT #__sdi_diffusion_download_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_diffusion_notifieduser
    ADD CONSTRAINT #__sdi_diffusion_notifieduser_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_diffusion_perimeter
    ADD CONSTRAINT #__sdi_diffusion_perimeter_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_diffusion
    ADD CONSTRAINT #__sdi_diffusion_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_diffusion_propertyvalue
    ADD CONSTRAINT #__sdi_diffusion_propertyvalue_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_excludedattribute
    ADD CONSTRAINT #__sdi_excludedattribute_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_featuretype_policy
    ADD CONSTRAINT #__sdi_featuretype_policy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_importref
    ADD CONSTRAINT #__sdi_importref_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_includedattribute
    ADD CONSTRAINT #__sdi_includedattribute_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_language
    ADD CONSTRAINT #__sdi_language_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_layer_layergroup
    ADD CONSTRAINT #__sdi_layer_layergroup_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_layer
    ADD CONSTRAINT #__sdi_layer_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_layergroup
    ADD CONSTRAINT #__sdi_layergroup_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_map
    ADD CONSTRAINT #__sdi_map_alias_key UNIQUE (alias);
ALTER TABLE ONLY #__sdi_map_layergroup
    ADD CONSTRAINT #__sdi_map_layergroup_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_map_physicalservice
    ADD CONSTRAINT #__sdi_map_physicalservice_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_map
    ADD CONSTRAINT #__sdi_map_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_map_tool
    ADD CONSTRAINT #__sdi_map_tool_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_map_virtualservice
    ADD CONSTRAINT #__sdi_map_virtualservice_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_maplayer
    ADD CONSTRAINT #__sdi_maplayer_alias_key UNIQUE (alias);
ALTER TABLE ONLY #__sdi_maplayer
    ADD CONSTRAINT #__sdi_maplayer_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_metadata
    ADD CONSTRAINT #__sdi_metadata_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_monitor_exports
    ADD CONSTRAINT #__sdi_monitor_exports_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_namespace
    ADD CONSTRAINT #__sdi_namespace_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_order_diffusion
    ADD CONSTRAINT #__sdi_order_diffusion_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_order_perimeter
    ADD CONSTRAINT #__sdi_order_perimeter_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_order
    ADD CONSTRAINT #__sdi_order_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_order_propertyvalue
    ADD CONSTRAINT #__sdi_order_propertyvalue_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_organism
    ADD CONSTRAINT #__sdi_organism_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_perimeter
    ADD CONSTRAINT #__sdi_perimeter_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_physicalservice_organism
    ADD CONSTRAINT #__sdi_physicalservice_organism_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_physicalservice
    ADD CONSTRAINT #__sdi_physicalservice_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_physicalservice_policy
    ADD CONSTRAINT #__sdi_physicalservice_policy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_physicalservice_servicecompliance
    ADD CONSTRAINT #__sdi_physicalservice_servicecompliance_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_policy_metadatastate
    ADD CONSTRAINT #__sdi_policy_metadatastate_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_policy_organism
    ADD CONSTRAINT #__sdi_policy_organism_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_policy
    ADD CONSTRAINT #__sdi_policy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_policy_resourcetype
    ADD CONSTRAINT #__sdi_policy_resourcetype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_policy_user
    ADD CONSTRAINT #__sdi_policy_user_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_policy_visibility
    ADD CONSTRAINT #__sdi_policy_visibility_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_profile
    ADD CONSTRAINT #__sdi_profile_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_property
    ADD CONSTRAINT #__sdi_property_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_propertyvalue
    ADD CONSTRAINT #__sdi_propertyvalue_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_relation_catalog
    ADD CONSTRAINT #__sdi_relation_catalog_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_relation_defaultvalue
    ADD CONSTRAINT #__sdi_relation_defaultvalue_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_relation_profile
    ADD CONSTRAINT #__sdi_relation_profile_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_resource
    ADD CONSTRAINT #__sdi_resource_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_resourcetype
    ADD CONSTRAINT #__sdi_resourcetype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_resourcetypelink
    ADD CONSTRAINT #__sdi_resourcetypelink_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_resourcetypelinkinheritance
    ADD CONSTRAINT #__sdi_resourcetypelinkinheritance_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_searchcriteria
    ADD CONSTRAINT #__sdi_searchcriteria_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_searchcriteriafilter
    ADD CONSTRAINT #__sdi_searchcriteriafilter_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_accessscope
    ADD CONSTRAINT #__sdi_sys_accessscope_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_addresstype
    ADD CONSTRAINT #__sdi_sys_addresstype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_authenticationconnector
    ADD CONSTRAINT #__sdi_sys_authenticationconnector_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_authenticationlevel
    ADD CONSTRAINT #__sdi_sys_authenticationlevel_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_country
    ADD CONSTRAINT #__sdi_sys_country_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_criteriatype
    ADD CONSTRAINT #__sdi_sys_criteriatype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_entity
    ADD CONSTRAINT #__sdi_sys_entity_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_exceptionlevel
    ADD CONSTRAINT #__sdi_sys_exceptionlevel_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_importtype
    ADD CONSTRAINT #__sdi_sys_importtype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_isolanguage
    ADD CONSTRAINT #__sdi_sys_isolanguage_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_loglevel
    ADD CONSTRAINT #__sdi_sys_loglevel_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_logroll
    ADD CONSTRAINT #__sdi_sys_logroll_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_maptool
    ADD CONSTRAINT #__sdi_sys_maptool_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_metadatastate
    ADD CONSTRAINT #__sdi_sys_metadatastate_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_metadataversion
    ADD CONSTRAINT #__sdi_sys_metadataversion_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_operationcompliance
    ADD CONSTRAINT #__sdi_sys_operationcompliance_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_orderstate
    ADD CONSTRAINT #__sdi_sys_orderstate_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_ordertype
    ADD CONSTRAINT #__sdi_sys_ordertype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_perimetertype
    ADD CONSTRAINT #__sdi_sys_perimetertype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_pricing
    ADD CONSTRAINT #__sdi_sys_pricing_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_productmining
    ADD CONSTRAINT #__sdi_sys_productmining_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_productstate
    ADD CONSTRAINT #__sdi_sys_productstate_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_productstorage
    ADD CONSTRAINT #__sdi_sys_productstorage_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_propertytype
    ADD CONSTRAINT #__sdi_sys_propertytype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_proxytype
    ADD CONSTRAINT #__sdi_sys_proxytype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_relationscope
    ADD CONSTRAINT #__sdi_sys_relationscope_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_relationtype
    ADD CONSTRAINT #__sdi_sys_relationtype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_rendertype_criteriatype
    ADD CONSTRAINT #__sdi_sys_rendertype_criteriatype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_rendertype
    ADD CONSTRAINT #__sdi_sys_rendertype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_rendertype_stereotype
    ADD CONSTRAINT #__sdi_sys_rendertype_stereotype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_role
    ADD CONSTRAINT #__sdi_sys_role_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_searchtab
    ADD CONSTRAINT #__sdi_sys_searchtab_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_servicecompliance
    ADD CONSTRAINT #__sdi_sys_servicecompliance_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_servicecon_authenticationcon
    ADD CONSTRAINT #__sdi_sys_servicecon_authenticationcon_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_serviceconnector
    ADD CONSTRAINT #__sdi_sys_serviceconnector_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_serviceoperation
    ADD CONSTRAINT #__sdi_sys_serviceoperation_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_servicescope
    ADD CONSTRAINT #__sdi_sys_servicescope_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_servicetype
    ADD CONSTRAINT #__sdi_sys_servicetype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_serviceversion
    ADD CONSTRAINT #__sdi_sys_serviceversion_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_spatialoperator
    ADD CONSTRAINT #__sdi_sys_spatialoperator_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_stereotype
    ADD CONSTRAINT #__sdi_sys_stereotype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_topiccategory
    ADD CONSTRAINT #__sdi_sys_topiccategory_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_unit
    ADD CONSTRAINT #__sdi_sys_unit_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_sys_versiontype
    ADD CONSTRAINT #__sdi_sys_versiontype_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_tilematrix_policy
    ADD CONSTRAINT #__sdi_tilematrix_policy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_tilematrixset_policy
    ADD CONSTRAINT #__sdi_tilematrixset_policy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_translation
    ADD CONSTRAINT #__sdi_translation_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_user
    ADD CONSTRAINT #__sdi_user_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_user_role_organism
    ADD CONSTRAINT #__sdi_user_role_organism_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_user_role_resource
    ADD CONSTRAINT #__sdi_user_role_resource_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_version
    ADD CONSTRAINT #__sdi_version_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_versionlink
    ADD CONSTRAINT #__sdi_versionlink_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_virtual_physical
    ADD CONSTRAINT #__sdi_virtual_physical_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_virtualmetadata
    ADD CONSTRAINT #__sdi_virtualmetadata_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_virtualservice_organism
    ADD CONSTRAINT #__sdi_virtualservice_organism_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_virtualservice
    ADD CONSTRAINT #__sdi_virtualservice_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_virtualservice_servicecompliance
    ADD CONSTRAINT #__sdi_virtualservice_servicecompliance_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_visualization
    ADD CONSTRAINT #__sdi_visualization_alias_key UNIQUE (alias);
ALTER TABLE ONLY #__sdi_visualization
    ADD CONSTRAINT #__sdi_visualization_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_wfs_spatialpolicy
    ADD CONSTRAINT #__sdi_wfs_spatialpolicy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_wms_spatialpolicy
    ADD CONSTRAINT #__sdi_wms_spatialpolicy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_wmslayer_policy
    ADD CONSTRAINT #__sdi_wmslayer_policy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_wmts_spatialpolicy
    ADD CONSTRAINT #__sdi_wmts_spatialpolicy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY #__sdi_wmtslayer_policy
    ADD CONSTRAINT #__sdi_wmtslayer_policy_pkey PRIMARY KEY (id);
ALTER TABLE ONLY last_ids
    ADD CONSTRAINT last_ids_pkey PRIMARY KEY ("TABLE_NAME");
ALTER TABLE ONLY last_query_results
    ADD CONSTRAINT last_query_results_pkey PRIMARY KEY ("ID_LAST_QUERY_RESULT");
ALTER TABLE ONLY log_entries
    ADD CONSTRAINT log_entries_pkey PRIMARY KEY ("ID_LOG_ENTRY");
ALTER TABLE ONLY #__sdi_physicalservice
    ADD CONSTRAINT name UNIQUE (name);
ALTER TABLE ONLY overview_page
    ADD CONSTRAINT overview_page_pkey PRIMARY KEY ("ID_OVERVIEW_PAGE");
ALTER TABLE ONLY overview_queries
    ADD CONSTRAINT overview_queries_pkey PRIMARY KEY ("ID_OVERVIEW_QUERY");
ALTER TABLE ONLY periods
    ADD CONSTRAINT periods_pkey PRIMARY KEY ("ID_PERIODS");
ALTER TABLE ONLY queries
    ADD CONSTRAINT queries_pkey PRIMARY KEY ("ID_QUERY");
ALTER TABLE ONLY query_agg_hour_log_entries
    ADD CONSTRAINT query_agg_hour_log_entries_pkey PRIMARY KEY ("DATE_LOG", "ID_QUERY");
ALTER TABLE ONLY query_agg_log_entries
    ADD CONSTRAINT query_agg_log_entries_pkey PRIMARY KEY ("DATE_LOG", "ID_QUERY");
ALTER TABLE ONLY query_params
    ADD CONSTRAINT query_params_pkey PRIMARY KEY ("ID_QUERY", "NAME");
ALTER TABLE ONLY query_validation_results
    ADD CONSTRAINT query_validation_results_pkey PRIMARY KEY ("ID_QUERY", "ID_QUERY_VALIDATION_RESULT");
ALTER TABLE ONLY query_validation_settings
    ADD CONSTRAINT query_validation_settings_pkey PRIMARY KEY ("ID_QUERY", "ID_QUERY_VALIDATION_SETTINGS");
ALTER TABLE ONLY roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY ("ID_ROLE");
ALTER TABLE ONLY service_methods
    ADD CONSTRAINT service_methods_pkey PRIMARY KEY ("ID_SERVICE_METHOD");
ALTER TABLE ONLY service_types_methods
    ADD CONSTRAINT service_types_methods_pkey PRIMARY KEY ("ID_SERVICE_METHOD", "ID_SERVICE_TYPE");
ALTER TABLE ONLY service_types
    ADD CONSTRAINT service_types_pkey PRIMARY KEY ("ID_SERVICE_TYPE");
ALTER TABLE ONLY sla
    ADD CONSTRAINT sla_pkey PRIMARY KEY ("ID_SLA");
ALTER TABLE ONLY statuses
    ADD CONSTRAINT statuses_pkey PRIMARY KEY ("ID_STATUS");
ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY ("LOGIN");
 
CREATE INDEX "FK_ACTION_JOB1" ON actions USING btree ("ID_JOB");
 
CREATE INDEX "FK_ACTION_TYPE1" ON actions USING btree ("ID_ACTION_TYPE");
 
CREATE INDEX "FK_ALERTS_JOB1" ON alerts USING btree ("ID_JOB");
 
CREATE INDEX "FK_ALERTS_NEW_STATUS1" ON alerts USING btree ("ID_NEW_STATUS");
 
CREATE INDEX "FK_ALERTS_OLD_STATUS1" ON alerts USING btree ("ID_OLD_STATUS");
 
CREATE INDEX "FK_JOBS_HTTP_METHOD1" ON jobs USING btree ("ID_HTTP_METHOD");
 
CREATE INDEX "FK_JOBS_SERVICE_TYPE1" ON jobs USING btree ("ID_SERVICE_TYPE");
 
CREATE INDEX "FK_JOBS_STATUS1" ON jobs USING btree ("ID_STATUS");
 
CREATE INDEX "FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB1" ON job_agg_hour_log_entries USING btree ("ID_JOB");
 
CREATE INDEX "FK_JOB_AGG_LOG_ENTRIES_JOB1" ON job_agg_log_entries USING btree ("ID_JOB");
 
CREATE INDEX "FK_LAST_QUERY_QUERY1" ON last_query_results USING btree ("ID_QUERY");
 
CREATE INDEX "FK_LOG_ENTRIES_QUERY1" ON log_entries USING btree ("ID_QUERY");
 
CREATE INDEX "FK_OVERVIEWQUERY_LASTRESULT" ON overview_queries USING btree ("ID_QUERY");
 
CREATE INDEX "FK_OVERVIEW_REQ_PAGE" ON overview_queries USING btree ("ID_OVERVIEW_PAGE");
 
CREATE INDEX "FK_OW_QUERY_PAGE1" ON overview_queries USING btree ("ID_OVERVIEW_PAGE");
 
CREATE INDEX "FK_PERIODS_SLA1" ON periods USING btree ("ID_SLA");
 
CREATE INDEX "FK_QUERIES_JOB1" ON queries USING btree ("ID_JOB");
 
CREATE INDEX "FK_QUERIES_METHOD1" ON queries USING btree ("ID_SERVICE_METHOD");
 
CREATE INDEX "FK_QUERIES_STATUS1" ON queries USING btree ("ID_STATUS");
 
CREATE INDEX "FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY1" ON query_agg_hour_log_entries USING btree ("ID_QUERY");
 
CREATE INDEX "FK_QUERY_AGG_LOG_ENTRIES_QUERY1" ON query_agg_log_entries USING btree ("ID_QUERY");
 
CREATE INDEX "FK_SERVICE_TYPES_METHODS_METHOD1" ON service_types_methods USING btree ("ID_SERVICE_METHOD");
 
CREATE INDEX "FK_USERS_ROLE1" ON users USING btree ("ID_ROLE");
 
CREATE INDEX "IX_LOG_ENTRIES_ID_QUERY_REQUEST_TIME" ON log_entries USING btree ("ID_QUERY", "REQUEST_TIME");
 
CREATE INDEX alias1 ON #__sdi_sys_unit USING btree (alias);
 
CREATE INDEX element_guid ON #__sdi_translation USING btree (element_guid);
 
CREATE INDEX "fk_log_entries_statuses_STATUS1" ON log_entries USING btree ("ID_STATUS");
 
CREATE INDEX fk_query_validation_results_queries11 ON query_validation_results USING btree ("ID_QUERY");
 
CREATE INDEX fk_query_validation_settings_queries11 ON query_validation_settings USING btree ("ID_QUERY");
 
CREATE INDEX #__sdi_accessscope_fk11 ON #__sdi_accessscope USING btree (organism_id);
 
CREATE INDEX #__sdi_accessscope_fk21 ON #__sdi_accessscope USING btree (user_id);
 
CREATE INDEX #__sdi_address_fk11 ON #__sdi_address USING btree (addresstype_id);
 
CREATE INDEX #__sdi_address_fk31 ON #__sdi_address USING btree (user_id);
 
CREATE INDEX #__sdi_address_fk41 ON #__sdi_address USING btree (organism_id);
 
CREATE INDEX #__sdi_address_fk51 ON #__sdi_address USING btree (country_id);
 
CREATE INDEX #__sdi_allowedoperationy_fk11 ON #__sdi_allowedoperation USING btree (policy_id);
 
CREATE INDEX #__sdi_allowedoperationy_fk21 ON #__sdi_allowedoperation USING btree (serviceoperation_id);
 
CREATE INDEX #__sdi_application_fk11 ON #__sdi_application USING btree (resource_id);
 
CREATE INDEX #__sdi_assignment_fk11 ON #__sdi_assignment USING btree (assigned_by);
 
CREATE INDEX #__sdi_assignment_fk21 ON #__sdi_assignment USING btree (assigned_to);
 
CREATE INDEX #__sdi_assignment_fk31 ON #__sdi_assignment USING btree (version_id);
 
CREATE INDEX #__sdi_attributevalue1 ON #__sdi_attributevalue USING btree (attribute_id);
 
CREATE INDEX #__sdi_boundary_fk11 ON #__sdi_boundary USING btree (parent_id);
 
CREATE INDEX #__sdi_boundary_fk21 ON #__sdi_boundary USING btree (category_id);
 
CREATE INDEX #__sdi_boundarycategory_fk11 ON #__sdi_boundarycategory USING btree (parent_id);
 
CREATE INDEX #__sdi_catalog_resourcetype_fk11 ON #__sdi_catalog_resourcetype USING btree (catalog_id);
 
CREATE INDEX #__sdi_catalog_resourcetype_fk21 ON #__sdi_catalog_resourcetype USING btree (resourcetype_id);
 
CREATE INDEX #__sdi_catalog_searchcriteria_fk11 ON #__sdi_catalog_searchcriteria USING btree (catalog_id);
 
CREATE INDEX #__sdi_catalog_searchcriteria_fk21 ON #__sdi_catalog_searchcriteria USING btree (searchcriteria_id);
 
CREATE INDEX #__sdi_catalog_searchcriteria_fk31 ON #__sdi_catalog_searchcriteria USING btree (searchtab_id);
 
CREATE INDEX #__sdi_catalog_searchsort_fk11 ON #__sdi_catalog_searchsort USING btree (catalog_id);
 
CREATE INDEX #__sdi_catalog_searchsort_fk21 ON #__sdi_catalog_searchsort USING btree (language_id);
 
CREATE INDEX #__sdi_class_fk11 ON #__sdi_class USING btree (namespace_id);
 
CREATE INDEX #__sdi_class_fk21 ON #__sdi_class USING btree (stereotype_id);
 
CREATE INDEX #__sdi_diffusion_download_fk11 ON #__sdi_diffusion_download USING btree (diffusion_id);
 
CREATE INDEX #__sdi_diffusion_download_fk21 ON #__sdi_diffusion_download USING btree (user_id);
 
CREATE INDEX #__sdi_diffusion_fk11 ON #__sdi_diffusion USING btree (accessscope_id);
 
CREATE INDEX #__sdi_diffusion_fk21 ON #__sdi_diffusion USING btree (productmining_id);
 
CREATE INDEX #__sdi_diffusion_fk31 ON #__sdi_diffusion USING btree (productstorage_id);
 
CREATE INDEX #__sdi_diffusion_fk41 ON #__sdi_diffusion USING btree (perimeter_id);
 
CREATE INDEX #__sdi_diffusion_fk51 ON #__sdi_diffusion USING btree (version_id);
 
CREATE INDEX #__sdi_diffusion_notifieduser_fk11 ON #__sdi_diffusion_notifieduser USING btree (diffusion_id);
 
CREATE INDEX #__sdi_diffusion_notifieduser_fk21 ON #__sdi_diffusion_notifieduser USING btree (user_id);
 
CREATE INDEX #__sdi_diffusion_perimeter_fk11 ON #__sdi_diffusion_perimeter USING btree (diffusion_id);
 
CREATE INDEX #__sdi_diffusion_perimeter_fk21 ON #__sdi_diffusion_perimeter USING btree (perimeter_id);
 
CREATE INDEX #__sdi_diffusion_propertyvalue_fk11 ON #__sdi_diffusion_propertyvalue USING btree (diffusion_id);
 
CREATE INDEX #__sdi_diffusion_propertyvalue_fk21 ON #__sdi_diffusion_propertyvalue USING btree (propertyvalue_id);
 
CREATE INDEX #__sdi_excludedattribute_fk11 ON #__sdi_excludedattribute USING btree (policy_id);
 
CREATE INDEX #__sdi_featuretype_policy_fk11 ON #__sdi_featuretype_policy USING btree (physicalservicepolicy_id);
 
CREATE INDEX #__sdi_featuretype_policy_fk21 ON #__sdi_featuretype_policy USING btree (spatialpolicy_id);
 
CREATE INDEX #__sdi_importref_fk11 ON #__sdi_importref USING btree (importtype_id);
 
CREATE INDEX #__sdi_importref_fk21 ON #__sdi_importref USING btree (cswservice_id);
 
CREATE INDEX #__sdi_importref_fk31 ON #__sdi_importref USING btree (cswversion_id);
 
CREATE INDEX #__sdi_includedattribute_fk11 ON #__sdi_includedattribute USING btree (featuretypepolicy_id);
 
CREATE INDEX #__sdi_layer_fk11 ON #__sdi_layer USING btree (physicalservice_id);
 
CREATE INDEX #__sdi_layer_layergroup_fk11 ON #__sdi_layer_layergroup USING btree (layer_id);
 
CREATE INDEX #__sdi_layer_layergroup_fk21 ON #__sdi_layer_layergroup USING btree (group_id);
 
CREATE INDEX #__sdi_map_fk21 ON #__sdi_map USING btree (unit_id);
 
CREATE INDEX #__sdi_map_layergroup_fk11 ON #__sdi_map_layergroup USING btree (map_id);
 
CREATE INDEX #__sdi_map_layergroup_fk21 ON #__sdi_map_layergroup USING btree (group_id);
 
CREATE INDEX #__sdi_map_physicalservice_fk11 ON #__sdi_map_physicalservice USING btree (map_id);
 
CREATE INDEX #__sdi_map_physicalservice_fk21 ON #__sdi_map_physicalservice USING btree (physicalservice_id);
 
CREATE INDEX #__sdi_map_tool_fk11 ON #__sdi_map_tool USING btree (map_id);
 
CREATE INDEX #__sdi_map_tool_fk21 ON #__sdi_map_tool USING btree (tool_id);
 
CREATE INDEX #__sdi_map_virtualservice_fk11 ON #__sdi_map_virtualservice USING btree (map_id);
 
CREATE INDEX #__sdi_map_virtualservice_fk21 ON #__sdi_map_virtualservice USING btree (virtualservice_id);
 
CREATE INDEX #__sdi_maplayer_fk11 ON #__sdi_maplayer USING btree (accessscope_id);
 
CREATE INDEX #__sdi_metadata_fk11 ON #__sdi_metadata USING btree (metadatastate_id);
 
CREATE INDEX #__sdi_metadata_fk21 ON #__sdi_metadata USING btree (accessscope_id);
 
CREATE INDEX #__sdi_metadata_fk31 ON #__sdi_metadata USING btree (version_id);
 
CREATE INDEX #__sdi_order_diffusion_fk11 ON #__sdi_order_diffusion USING btree (order_id);
 
CREATE INDEX #__sdi_order_diffusion_fk21 ON #__sdi_order_diffusion USING btree (diffusion_id);
 
CREATE INDEX #__sdi_order_diffusion_fk31 ON #__sdi_order_diffusion USING btree (productstate_id);
 
CREATE INDEX #__sdi_order_fk11 ON #__sdi_order USING btree (ordertype_id);
 
CREATE INDEX #__sdi_order_fk21 ON #__sdi_order USING btree (orderstate_id);
 
CREATE INDEX #__sdi_order_fk31 ON #__sdi_order USING btree (user_id);
 
CREATE INDEX #__sdi_order_fk41 ON #__sdi_order USING btree (thirdparty_id);
 
CREATE INDEX #__sdi_order_perimeter_fk11 ON #__sdi_order_perimeter USING btree (order_id);
 
CREATE INDEX #__sdi_order_perimeter_fk21 ON #__sdi_order_perimeter USING btree (perimeter_id);
 
CREATE INDEX #__sdi_order_propertyvalue_fk11 ON #__sdi_order_propertyvalue USING btree (orderdiffusion_id);
 
CREATE INDEX #__sdi_order_propertyvalue_fk21 ON #__sdi_order_propertyvalue USING btree (property_id);
 
CREATE INDEX #__sdi_order_propertyvalue_fk31 ON #__sdi_order_propertyvalue USING btree (propertyvalue_id);
 
CREATE INDEX #__sdi_perimeter_fk11 ON #__sdi_perimeter USING btree (accessscope_id);
 
CREATE INDEX #__sdi_perimeter_fk21 ON #__sdi_perimeter USING btree (perimetertype_id);
 
CREATE INDEX #__sdi_physicalservice_fk11 ON #__sdi_physicalservice USING btree (serviceconnector_id);
 
CREATE INDEX #__sdi_physicalservice_fk21 ON #__sdi_physicalservice USING btree (resourceauthentication_id);
 
CREATE INDEX #__sdi_physicalservice_fk31 ON #__sdi_physicalservice USING btree (serviceauthentication_id);
 
CREATE INDEX #__sdi_physicalservice_fk41 ON #__sdi_physicalservice USING btree (servicescope_id);
 
CREATE INDEX #__sdi_physicalservice_organism_fk11 ON #__sdi_physicalservice_organism USING btree (organism_id);
 
CREATE INDEX #__sdi_physicalservice_organism_fk21 ON #__sdi_physicalservice_organism USING btree (physicalservice_id);
 
CREATE INDEX #__sdi_physicalservice_policy_fk11 ON #__sdi_physicalservice_policy USING btree (policy_id);
 
CREATE INDEX #__sdi_physicalservice_policy_fk21 ON #__sdi_physicalservice_policy USING btree (physicalservice_id);
 
CREATE INDEX #__sdi_physicalservice_policy_fk31 ON #__sdi_physicalservice_policy USING btree (csw_spatialpolicy_id);
 
CREATE INDEX #__sdi_physicalservice_policy_fk41 ON #__sdi_physicalservice_policy USING btree (wms_spatialpolicy_id);
 
CREATE INDEX #__sdi_physicalservice_policy_fk51 ON #__sdi_physicalservice_policy USING btree (wfs_spatialpolicy_id);
 
CREATE INDEX #__sdi_physicalservice_policy_fk61 ON #__sdi_physicalservice_policy USING btree (wmts_spatialpolicy_id);
 
CREATE INDEX #__sdi_physicalservice_servicecompliance_fk11 ON #__sdi_physicalservice_servicecompliance USING btree (service_id);
 
CREATE INDEX #__sdi_physicalservice_servicecompliance_fk21 ON #__sdi_physicalservice_servicecompliance USING btree (servicecompliance_id);
 
CREATE INDEX #__sdi_policy_fk11 ON #__sdi_policy USING btree (virtualservice_id);
 
CREATE INDEX #__sdi_policy_fk21 ON #__sdi_policy USING btree (accessscope_id);
 
CREATE INDEX #__sdi_policy_fk31 ON #__sdi_policy USING btree (csw_spatialpolicy_id);
 
CREATE INDEX #__sdi_policy_fk41 ON #__sdi_policy USING btree (wms_spatialpolicy_id);
 
CREATE INDEX #__sdi_policy_fk51 ON #__sdi_policy USING btree (wfs_spatialpolicy_id);
 
CREATE INDEX #__sdi_policy_fk61 ON #__sdi_policy USING btree (wmts_spatialpolicy_id);
 
CREATE INDEX #__sdi_policy_fk71 ON #__sdi_policy USING btree (csw_version_id);
 
CREATE INDEX #__sdi_policy_metadatastate_fk11 ON #__sdi_policy_metadatastate USING btree (policy_id);
 
CREATE INDEX #__sdi_policy_metadatastate_fk21 ON #__sdi_policy_metadatastate USING btree (metadatastate_id);
 
CREATE INDEX #__sdi_policy_organism_fk11 ON #__sdi_policy_organism USING btree (policy_id);
 
CREATE INDEX #__sdi_policy_organism_fk21 ON #__sdi_policy_organism USING btree (organism_id);
 
CREATE INDEX #__sdi_policy_resourcetype_fk11 ON #__sdi_policy_resourcetype USING btree (policy_id);
 
CREATE INDEX #__sdi_policy_resourcetype_fk21 ON #__sdi_policy_resourcetype USING btree (resourcetype_id);
 
CREATE INDEX #__sdi_policy_user_fk11 ON #__sdi_policy_user USING btree (policy_id);
 
CREATE INDEX #__sdi_policy_user_fk21 ON #__sdi_policy_user USING btree (user_id);
 
CREATE INDEX #__sdi_policy_visibility_fk11 ON #__sdi_policy_visibility USING btree (policy_id);
 
CREATE INDEX #__sdi_policy_visibility_fk21 ON #__sdi_policy_visibility USING btree (user_id);
 
CREATE INDEX #__sdi_policy_visibility_fk31 ON #__sdi_policy_visibility USING btree (organism_id);
 
CREATE INDEX #__sdi_profile_fk11 ON #__sdi_profile USING btree (class_id);
 
CREATE INDEX #__sdi_property_fk11 ON #__sdi_property USING btree (accessscope_id);
 
CREATE INDEX #__sdi_property_fk21 ON #__sdi_property USING btree (propertytype_id);
 
CREATE INDEX #__sdi_propertyvalue_fk11 ON #__sdi_propertyvalue USING btree (property_id);
 
CREATE INDEX #__sdi_relation_catalog_fk11 ON #__sdi_relation_catalog USING btree (relation_id);
 
CREATE INDEX #__sdi_relation_catalog_fk21 ON #__sdi_relation_catalog USING btree (catalog_id);
 
CREATE INDEX #__sdi_relation_defaultvalue_fk11 ON #__sdi_relation_defaultvalue USING btree (relation_id);
 
CREATE INDEX #__sdi_relation_defaultvalue_fk21 ON #__sdi_relation_defaultvalue USING btree (attributevalue_id);
 
CREATE INDEX #__sdi_relation_defaultvalue_fk31 ON #__sdi_relation_defaultvalue USING btree (language_id);
 
CREATE INDEX #__sdi_relation_fk101 ON #__sdi_relation USING btree (childresourcetype_id);
 
CREATE INDEX #__sdi_relation_fk11 ON #__sdi_relation USING btree (parent_id);
 
CREATE INDEX #__sdi_relation_fk21 ON #__sdi_relation USING btree (classchild_id);
 
CREATE INDEX #__sdi_relation_fk31 ON #__sdi_relation USING btree (attributechild_id);
 
CREATE INDEX #__sdi_relation_fk41 ON #__sdi_relation USING btree (relationtype_id);
 
CREATE INDEX #__sdi_relation_fk51 ON #__sdi_relation USING btree (rendertype_id);
 
CREATE INDEX #__sdi_relation_fk61 ON #__sdi_relation USING btree (namespace_id);
 
CREATE INDEX #__sdi_relation_fk71 ON #__sdi_relation USING btree (classassociation_id);
 
CREATE INDEX #__sdi_relation_fk81 ON #__sdi_relation USING btree (relationscope_id);
 
CREATE INDEX #__sdi_relation_fk91 ON #__sdi_relation USING btree (editorrelationscope_id);
 
CREATE INDEX #__sdi_relation_profile_fk11 ON #__sdi_relation_profile USING btree (relation_id);
 
CREATE INDEX #__sdi_relation_profile_fk21 ON #__sdi_relation_profile USING btree (profile_id);
 
CREATE INDEX #__sdi_resource_fk11 ON #__sdi_resource USING btree (organism_id);
 
CREATE INDEX #__sdi_resource_fk21 ON #__sdi_resource USING btree (resourcetype_id);
 
CREATE INDEX #__sdi_resource_fk31 ON #__sdi_resource USING btree (accessscope_id);
 
CREATE INDEX #__sdi_resourcetype_fk11 ON #__sdi_resourcetype USING btree (profile_id);
 
CREATE INDEX #__sdi_resourcetype_fk21 ON #__sdi_resourcetype USING btree (fragmentnamespace_id);
 
CREATE INDEX #__sdi_resourcetype_fk31 ON #__sdi_resourcetype USING btree (accessscope_id);
 
CREATE INDEX #__sdi_resourcetypelink_fk11 ON #__sdi_resourcetypelink USING btree (parent_id);
 
CREATE INDEX #__sdi_resourcetypelink_fk21 ON #__sdi_resourcetypelink USING btree (child_id);
 
CREATE INDEX #__sdi_resourcetypelink_fk31 ON #__sdi_resourcetypelink USING btree (class_id);
 
CREATE INDEX #__sdi_resourcetypelink_fk41 ON #__sdi_resourcetypelink USING btree (attribute_id);
 
CREATE INDEX #__sdi_resourcetypelinkinheritance_fk11 ON #__sdi_resourcetypelinkinheritance USING btree (resourcetypelink_id);
 
CREATE INDEX #__sdi_searchcriteria_fk11 ON #__sdi_searchcriteria USING btree (criteriatype_id);
 
CREATE INDEX #__sdi_searchcriteria_fk21 ON #__sdi_searchcriteria USING btree (rendertype_id);
 
CREATE INDEX #__sdi_searchcriteria_fk31 ON #__sdi_searchcriteria USING btree (relation_id);
 
CREATE INDEX #__sdi_searchcriteriafilter_fk11 ON #__sdi_searchcriteriafilter USING btree (searchcriteria_id);
 
CREATE INDEX #__sdi_searchcriteriafilter_fk21 ON #__sdi_searchcriteriafilter USING btree (language_id);
 
CREATE INDEX #__sdi_sys_authenticationconnector_fk11 ON #__sdi_sys_authenticationconnector USING btree (authenticationlevel_id);
 
CREATE INDEX #__sdi_sys_operationcompliance_fk11 ON #__sdi_sys_operationcompliance USING btree (servicecompliance_id);
 
CREATE INDEX #__sdi_sys_operationcompliance_fk21 ON #__sdi_sys_operationcompliance USING btree (serviceoperation_id);
 
CREATE INDEX #__sdi_sys_rendertype_criteriatype_fk11 ON #__sdi_sys_rendertype_criteriatype USING btree (criteriatype_id);
 
CREATE INDEX #__sdi_sys_rendertype_criteriatype_fk21 ON #__sdi_sys_rendertype_criteriatype USING btree (rendertype_id);
 
CREATE INDEX #__sdi_sys_rendertype_stereotype_fk11 ON #__sdi_sys_rendertype_stereotype USING btree (stereotype_id);
 
CREATE INDEX #__sdi_sys_rendertype_stereotype_fk1_fk2 ON #__sdi_sys_rendertype_stereotype USING btree (rendertype_id);
 
CREATE INDEX #__sdi_sys_servicecompliance_fk11 ON #__sdi_sys_servicecompliance USING btree (serviceconnector_id);
 
CREATE INDEX #__sdi_sys_servicecompliance_fk21 ON #__sdi_sys_servicecompliance USING btree (serviceversion_id);
 
CREATE INDEX #__sdi_sys_servicecon_authenticationcon_fk11 ON #__sdi_sys_servicecon_authenticationcon USING btree (serviceconnector_id);
 
CREATE INDEX #__sdi_sys_servicecon_authenticationcon_fk21 ON #__sdi_sys_servicecon_authenticationcon USING btree (authenticationconnector_id);
 
CREATE INDEX #__sdi_sys_stereotype_fk11 ON #__sdi_sys_stereotype USING btree (entity_id);
 
CREATE INDEX #__sdi_sys_stereotype_fk21 ON #__sdi_sys_stereotype USING btree (namespace_id);
 
CREATE INDEX #__sdi_tilematrix_policy_fk11 ON #__sdi_tilematrix_policy USING btree (tilematrixsetpolicy_id);
 
CREATE INDEX #__sdi_tilematrixset_policy_fk11 ON #__sdi_tilematrixset_policy USING btree (wmtslayerpolicy_id);
 
CREATE INDEX #__sdi_translation_fk11 ON #__sdi_translation USING btree (language_id);
 
CREATE INDEX #__sdi_user_fk11 ON #__sdi_user USING btree (user_id);
 
CREATE INDEX #__sdi_user_role_resource_fk11 ON #__sdi_user_role_resource USING btree (user_id);
 
CREATE INDEX #__sdi_user_role_resource_fk21 ON #__sdi_user_role_resource USING btree (role_id);
 
CREATE INDEX #__sdi_user_role_resource_fk31 ON #__sdi_user_role_resource USING btree (resource_id);
 
CREATE INDEX #__sdi_user_role_organism_fk11 ON #__sdi_user_role_organism USING btree (user_id);
 
CREATE INDEX #__sdi_user_role_organism_fk21 ON #__sdi_user_role_organism USING btree (role_id);
 
CREATE INDEX #__sdi_user_role_organism_fk31 ON #__sdi_user_role_organism USING btree (organism_id);
 
CREATE INDEX #__sdi_version_fk11 ON #__sdi_version USING btree (resource_id);
 
CREATE INDEX #__sdi_versionlink_fk11 ON #__sdi_versionlink USING btree (parent_id);
 
CREATE INDEX #__sdi_versionlink_fk21 ON #__sdi_versionlink USING btree (child_id);
 
CREATE INDEX #__sdi_virtual_physical_fk11 ON #__sdi_virtual_physical USING btree (virtualservice_id);
 
CREATE INDEX #__sdi_virtual_physical_fk21 ON #__sdi_virtual_physical USING btree (physicalservice_id);
 
CREATE INDEX #__sdi_virtualmetadata_fk11 ON #__sdi_virtualmetadata USING btree (virtualservice_id);
 
CREATE INDEX #__sdi_virtualmetadata_fk21 ON #__sdi_virtualmetadata USING btree (country_id);
 
CREATE INDEX #__sdi_virtualservice_fk11 ON #__sdi_virtualservice USING btree (serviceconnector_id);
 
CREATE INDEX #__sdi_virtualservice_fk21 ON #__sdi_virtualservice USING btree (proxytype_id);
 
CREATE INDEX #__sdi_virtualservice_fk31 ON #__sdi_virtualservice USING btree (exceptionlevel_id);
 
CREATE INDEX #__sdi_virtualservice_fk41 ON #__sdi_virtualservice USING btree (loglevel_id);
 
CREATE INDEX #__sdi_virtualservice_fk51 ON #__sdi_virtualservice USING btree (logroll_id);
 
CREATE INDEX #__sdi_virtualservice_fk61 ON #__sdi_virtualservice USING btree (servicescope_id);
 
CREATE INDEX #__sdi_virtualservice_organism_fk11 ON #__sdi_virtualservice_organism USING btree (organism_id);
 
CREATE INDEX #__sdi_virtualservice_organism_fk21 ON #__sdi_virtualservice_organism USING btree (virtualservice_id);
 
CREATE INDEX #__sdi_virtualservice_servicecompliance_fk11 ON #__sdi_virtualservice_servicecompliance USING btree (service_id);
 
CREATE INDEX #__sdi_virtualservice_servicecompliance_fk21 ON #__sdi_virtualservice_servicecompliance USING btree (servicecompliance_id);
 
CREATE INDEX #__sdi_visualization_fk11 ON #__sdi_visualization USING btree (accessscope_id);
 
CREATE INDEX #__sdi_wmslayer_policy_fk11 ON #__sdi_wmslayer_policy USING btree (physicalservicepolicy_id);
 
CREATE INDEX #__sdi_wmslayer_policy_fk21 ON #__sdi_wmslayer_policy USING btree (spatialpolicy_id);
 
CREATE INDEX #__sdi_wmts_spatialpolicy_fk11 ON #__sdi_wmts_spatialpolicy USING btree (spatialoperator_id);
 
CREATE INDEX #__sdi_wmtslayer_policy_fk11 ON #__sdi_wmtslayer_policy USING btree (physicalservicepolicy_id);
 
CREATE INDEX #__sdi_wmtslayer_policy_fk21 ON #__sdi_wmtslayer_policy USING btree (spatialpolicy_id);
 
CREATE INDEX sdi_attribute_fk11 ON #__sdi_attribute USING btree (namespace_id);
 
CREATE INDEX sdi_attribute_fk21 ON #__sdi_attribute USING btree (listnamespace_id);
 
CREATE INDEX sdi_attribute_fk31 ON #__sdi_attribute USING btree (stereotype_id);
ALTER TABLE ONLY actions
    ADD CONSTRAINT "FK_ACTION_JOB" FOREIGN KEY ("ID_JOB") REFERENCES jobs("ID_JOB") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY actions
    ADD CONSTRAINT "FK_ACTION_TYPE" FOREIGN KEY ("ID_ACTION_TYPE") REFERENCES action_types("ID_ACTION_TYPE") MATCH FULL;
ALTER TABLE ONLY alerts
    ADD CONSTRAINT "FK_ALERTS_JOB" FOREIGN KEY ("ID_JOB") REFERENCES jobs("ID_JOB") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY alerts
    ADD CONSTRAINT "FK_ALERTS_NEW_STATUS" FOREIGN KEY ("ID_NEW_STATUS") REFERENCES statuses("ID_STATUS") MATCH FULL;
ALTER TABLE ONLY alerts
    ADD CONSTRAINT "FK_ALERTS_OLD_STATUS" FOREIGN KEY ("ID_OLD_STATUS") REFERENCES statuses("ID_STATUS") MATCH FULL;
ALTER TABLE ONLY jobs
    ADD CONSTRAINT "FK_JOBS_HTTP_METHOD" FOREIGN KEY ("ID_HTTP_METHOD") REFERENCES http_methods("ID_HTTP_METHOD") MATCH FULL;
ALTER TABLE ONLY jobs
    ADD CONSTRAINT "FK_JOBS_SERVICE_TYPE" FOREIGN KEY ("ID_SERVICE_TYPE") REFERENCES service_types("ID_SERVICE_TYPE") MATCH FULL;
ALTER TABLE ONLY jobs
    ADD CONSTRAINT "FK_JOBS_STATUS" FOREIGN KEY ("ID_STATUS") REFERENCES statuses("ID_STATUS") MATCH FULL;
ALTER TABLE ONLY job_agg_hour_log_entries
    ADD CONSTRAINT "FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB" FOREIGN KEY ("ID_JOB") REFERENCES jobs("ID_JOB") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY job_agg_log_entries
    ADD CONSTRAINT "FK_JOB_AGG_LOG_ENTRIES_JOB" FOREIGN KEY ("ID_JOB") REFERENCES jobs("ID_JOB") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY last_query_results
    ADD CONSTRAINT "FK_LAST_QUERY_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY log_entries
    ADD CONSTRAINT "FK_LOG_ENTRIES_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY overview_queries
    ADD CONSTRAINT "FK_OVERVIEWQUERY_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY overview_queries
    ADD CONSTRAINT "FK_OW_QUERY_PAGE" FOREIGN KEY ("ID_OVERVIEW_PAGE") REFERENCES overview_page("ID_OVERVIEW_PAGE") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY periods
    ADD CONSTRAINT "FK_PERIODS_SLA" FOREIGN KEY ("ID_SLA") REFERENCES sla("ID_SLA") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY queries
    ADD CONSTRAINT "FK_QUERIES_JOB" FOREIGN KEY ("ID_JOB") REFERENCES jobs("ID_JOB") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY queries
    ADD CONSTRAINT "FK_QUERIES_METHOD" FOREIGN KEY ("ID_SERVICE_METHOD") REFERENCES service_methods("ID_SERVICE_METHOD") MATCH FULL;
ALTER TABLE ONLY queries
    ADD CONSTRAINT "FK_QUERIES_STATUS" FOREIGN KEY ("ID_STATUS") REFERENCES statuses("ID_STATUS") MATCH FULL;
ALTER TABLE ONLY query_agg_hour_log_entries
    ADD CONSTRAINT "FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY query_agg_log_entries
    ADD CONSTRAINT "FK_QUERY_AGG_LOG_ENTRIES_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY query_params
    ADD CONSTRAINT "FK_QUERY_PARAMS_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY service_types_methods
    ADD CONSTRAINT "FK_SERVICE_TYPES_METHODS_METHOD" FOREIGN KEY ("ID_SERVICE_METHOD") REFERENCES service_methods("ID_SERVICE_METHOD") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY service_types_methods
    ADD CONSTRAINT "FK_SERVICE_TYPES_METHODS_TYPE" FOREIGN KEY ("ID_SERVICE_TYPE") REFERENCES service_types("ID_SERVICE_TYPE") MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY users
    ADD CONSTRAINT "FK_USERS_ROLE" FOREIGN KEY ("ID_ROLE") REFERENCES roles("ID_ROLE") MATCH FULL ON DELETE SET NULL;
ALTER TABLE ONLY log_entries
    ADD CONSTRAINT "fk_log_entries_statuses_STATUS" FOREIGN KEY ("ID_STATUS") REFERENCES statuses("ID_STATUS") MATCH FULL;
ALTER TABLE ONLY query_validation_results
    ADD CONSTRAINT fk_query_validation_results_queries1 FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL;
ALTER TABLE ONLY query_validation_settings
    ADD CONSTRAINT fk_query_validation_settings_queries1 FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL;
ALTER TABLE ONLY #__sdi_accessscope
    ADD CONSTRAINT #__sdi_accessscope_fk1 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_accessscope
    ADD CONSTRAINT #__sdi_accessscope_fk2 FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_accessscope
    ADD CONSTRAINT #__sdi_accessscope_fk3 FOREIGN KEY (category_id) REFERENCES #__sdi_category(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_address
    ADD CONSTRAINT #__sdi_address_fk1 FOREIGN KEY (addresstype_id) REFERENCES #__sdi_sys_addresstype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_address
    ADD CONSTRAINT #__sdi_address_fk3 FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_address
    ADD CONSTRAINT #__sdi_address_fk4 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_address
    ADD CONSTRAINT #__sdi_address_fk5 FOREIGN KEY (country_id) REFERENCES #__sdi_sys_country(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_allowedoperation
    ADD CONSTRAINT #__sdi_allowedoperationy_fk1 FOREIGN KEY (policy_id) REFERENCES #__sdi_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_allowedoperation
    ADD CONSTRAINT #__sdi_allowedoperationy_fk2 FOREIGN KEY (serviceoperation_id) REFERENCES #__sdi_sys_serviceoperation(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_application
    ADD CONSTRAINT #__sdi_application_fk1 FOREIGN KEY (resource_id) REFERENCES #__sdi_resource(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_assignment
    ADD CONSTRAINT #__sdi_assignment_fk1 FOREIGN KEY (assigned_by) REFERENCES #__sdi_user(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_assignment
    ADD CONSTRAINT #__sdi_assignment_fk2 FOREIGN KEY (assigned_to) REFERENCES #__sdi_user(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_assignment
    ADD CONSTRAINT #__sdi_assignment_fk3 FOREIGN KEY (version_id) REFERENCES #__sdi_version(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_attributevalue
    ADD CONSTRAINT #__sdi_attributevalue FOREIGN KEY (attribute_id) REFERENCES #__sdi_attribute(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_boundary
    ADD CONSTRAINT #__sdi_boundary_fk1 FOREIGN KEY (parent_id) REFERENCES #__sdi_boundary(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_boundary
    ADD CONSTRAINT #__sdi_boundary_fk2 FOREIGN KEY (category_id) REFERENCES #__sdi_boundarycategory(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_boundarycategory
    ADD CONSTRAINT #__sdi_boundarycategory_fk1 FOREIGN KEY (parent_id) REFERENCES #__sdi_boundarycategory(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_catalog_resourcetype
    ADD CONSTRAINT #__sdi_catalog_resourcetype_fk1 FOREIGN KEY (catalog_id) REFERENCES #__sdi_catalog(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_catalog_resourcetype
    ADD CONSTRAINT #__sdi_catalog_resourcetype_fk2 FOREIGN KEY (resourcetype_id) REFERENCES #__sdi_resourcetype(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_catalog_searchcriteria
    ADD CONSTRAINT #__sdi_catalog_searchcriteria_fk1 FOREIGN KEY (catalog_id) REFERENCES #__sdi_catalog(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_catalog_searchcriteria
    ADD CONSTRAINT #__sdi_catalog_searchcriteria_fk2 FOREIGN KEY (searchcriteria_id) REFERENCES #__sdi_searchcriteria(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_catalog_searchcriteria
    ADD CONSTRAINT #__sdi_catalog_searchcriteria_fk3 FOREIGN KEY (searchtab_id) REFERENCES #__sdi_sys_searchtab(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_catalog_searchsort
    ADD CONSTRAINT #__sdi_catalog_searchsort_fk1 FOREIGN KEY (catalog_id) REFERENCES #__sdi_catalog(id) MATCH FULL ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE ONLY #__sdi_catalog_searchsort
    ADD CONSTRAINT #__sdi_catalog_searchsort_fk2 FOREIGN KEY (language_id) REFERENCES #__sdi_language(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_class
    ADD CONSTRAINT #__sdi_class_fk1 FOREIGN KEY (namespace_id) REFERENCES #__sdi_namespace(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_class
    ADD CONSTRAINT #__sdi_class_fk2 FOREIGN KEY (stereotype_id) REFERENCES #__sdi_sys_stereotype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_diffusion_download
    ADD CONSTRAINT #__sdi_diffusion_download_fk1 FOREIGN KEY (diffusion_id) REFERENCES #__sdi_diffusion(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_diffusion_download
    ADD CONSTRAINT #__sdi_diffusion_download_fk2 FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_diffusion
    ADD CONSTRAINT #__sdi_diffusion_fk1 FOREIGN KEY (accessscope_id) REFERENCES #__sdi_sys_accessscope(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_diffusion
    ADD CONSTRAINT #__sdi_diffusion_fk2 FOREIGN KEY (productmining_id) REFERENCES #__sdi_sys_productmining(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_diffusion
    ADD CONSTRAINT #__sdi_diffusion_fk3 FOREIGN KEY (productstorage_id) REFERENCES #__sdi_sys_productstorage(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_diffusion
    ADD CONSTRAINT #__sdi_diffusion_fk4 FOREIGN KEY (perimeter_id) REFERENCES #__sdi_perimeter(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_diffusion
    ADD CONSTRAINT #__sdi_diffusion_fk5 FOREIGN KEY (version_id) REFERENCES #__sdi_version(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_diffusion
    ADD CONSTRAINT #__sdi_diffusion_fk6 FOREIGN KEY (pricing_profile_id) REFERENCES #__sdi_pricing_profile(id) MATCH FULL ON DELETE SET NULL;
ALTER TABLE ONLY #__sdi_diffusion_notifieduser
    ADD CONSTRAINT #__sdi_diffusion_notifieduser_fk1 FOREIGN KEY (diffusion_id) REFERENCES #__sdi_diffusion(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_diffusion_notifieduser
    ADD CONSTRAINT #__sdi_diffusion_notifieduser_fk2 FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_diffusion_perimeter
    ADD CONSTRAINT #__sdi_diffusion_perimeter_fk1 FOREIGN KEY (diffusion_id) REFERENCES #__sdi_diffusion(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_diffusion_perimeter
    ADD CONSTRAINT #__sdi_diffusion_perimeter_fk2 FOREIGN KEY (perimeter_id) REFERENCES #__sdi_perimeter(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_diffusion_propertyvalue
    ADD CONSTRAINT #__sdi_diffusion_propertyvalue_fk1 FOREIGN KEY (diffusion_id) REFERENCES #__sdi_diffusion(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_diffusion_propertyvalue
    ADD CONSTRAINT #__sdi_diffusion_propertyvalue_fk2 FOREIGN KEY (propertyvalue_id) REFERENCES #__sdi_propertyvalue(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_excludedattribute
    ADD CONSTRAINT #__sdi_excludedattribute_fk1 FOREIGN KEY (policy_id) REFERENCES #__sdi_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_featuretype_policy
    ADD CONSTRAINT #__sdi_featuretype_policy_fk1 FOREIGN KEY (physicalservicepolicy_id) REFERENCES #__sdi_physicalservice_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_featuretype_policy
    ADD CONSTRAINT #__sdi_featuretype_policy_fk2 FOREIGN KEY (spatialpolicy_id) REFERENCES #__sdi_wfs_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_importref
    ADD CONSTRAINT #__sdi_importref_fk1 FOREIGN KEY (importtype_id) REFERENCES #__sdi_sys_importtype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_importref
    ADD CONSTRAINT #__sdi_importref_fk2 FOREIGN KEY (cswservice_id) REFERENCES #__sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_importref
    ADD CONSTRAINT #__sdi_importref_fk3 FOREIGN KEY (cswversion_id) REFERENCES #__sdi_sys_serviceversion(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_includedattribute
    ADD CONSTRAINT #__sdi_includedattribute_fk1 FOREIGN KEY (featuretypepolicy_id) REFERENCES #__sdi_featuretype_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_layer
    ADD CONSTRAINT #__sdi_layer_fk1 FOREIGN KEY (physicalservice_id) REFERENCES #__sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_layer_layergroup
    ADD CONSTRAINT #__sdi_layer_layergroup_fk1 FOREIGN KEY (layer_id) REFERENCES #__sdi_maplayer(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_layer_layergroup
    ADD CONSTRAINT #__sdi_layer_layergroup_fk2 FOREIGN KEY (group_id) REFERENCES #__sdi_layergroup(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_map
    ADD CONSTRAINT #__sdi_map_fk2 FOREIGN KEY (unit_id) REFERENCES #__sdi_sys_unit(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_map_layergroup
    ADD CONSTRAINT #__sdi_map_layergroup_fk1 FOREIGN KEY (map_id) REFERENCES #__sdi_map(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_map_layergroup
    ADD CONSTRAINT #__sdi_map_layergroup_fk2 FOREIGN KEY (group_id) REFERENCES #__sdi_layergroup(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_map_physicalservice
    ADD CONSTRAINT #__sdi_map_physicalservice_fk1 FOREIGN KEY (map_id) REFERENCES #__sdi_map(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_map_physicalservice
    ADD CONSTRAINT #__sdi_map_physicalservice_fk2 FOREIGN KEY (physicalservice_id) REFERENCES #__sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_map_tool
    ADD CONSTRAINT #__sdi_map_tool_fk1 FOREIGN KEY (map_id) REFERENCES #__sdi_map(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_map_tool
    ADD CONSTRAINT #__sdi_map_tool_fk2 FOREIGN KEY (tool_id) REFERENCES #__sdi_sys_maptool(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_map_virtualservice
    ADD CONSTRAINT #__sdi_map_virtualservice_fk1 FOREIGN KEY (map_id) REFERENCES #__sdi_map(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_map_virtualservice
    ADD CONSTRAINT #__sdi_map_virtualservice_fk2 FOREIGN KEY (virtualservice_id) REFERENCES #__sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_maplayer
    ADD CONSTRAINT #__sdi_maplayer_fk1 FOREIGN KEY (accessscope_id) REFERENCES #__sdi_sys_accessscope(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_metadata
    ADD CONSTRAINT #__sdi_metadata_fk1 FOREIGN KEY (metadatastate_id) REFERENCES #__sdi_sys_metadatastate(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_metadata
    ADD CONSTRAINT #__sdi_metadata_fk2 FOREIGN KEY (accessscope_id) REFERENCES #__sdi_sys_accessscope(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_metadata
    ADD CONSTRAINT #__sdi_metadata_fk3 FOREIGN KEY (version_id) REFERENCES #__sdi_version(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_order_diffusion
    ADD CONSTRAINT #__sdi_order_diffusion_fk1 FOREIGN KEY (order_id) REFERENCES #__sdi_order(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_order_diffusion
    ADD CONSTRAINT #__sdi_order_diffusion_fk2 FOREIGN KEY (diffusion_id) REFERENCES #__sdi_diffusion(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_order_diffusion
    ADD CONSTRAINT #__sdi_order_diffusion_fk3 FOREIGN KEY (productstate_id) REFERENCES #__sdi_sys_productstate(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_order
    ADD CONSTRAINT #__sdi_order_fk1 FOREIGN KEY (ordertype_id) REFERENCES #__sdi_sys_ordertype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_order
    ADD CONSTRAINT #__sdi_order_fk2 FOREIGN KEY (orderstate_id) REFERENCES #__sdi_sys_orderstate(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_order
    ADD CONSTRAINT #__sdi_order_fk3 FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_order
    ADD CONSTRAINT #__sdi_order_fk4 FOREIGN KEY (thirdparty_id) REFERENCES #__sdi_organism(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_order_perimeter
    ADD CONSTRAINT #__sdi_order_perimeter_fk1 FOREIGN KEY (order_id) REFERENCES #__sdi_order(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_order_perimeter
    ADD CONSTRAINT #__sdi_order_perimeter_fk2 FOREIGN KEY (perimeter_id) REFERENCES #__sdi_perimeter(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_order_propertyvalue
    ADD CONSTRAINT #__sdi_order_propertyvalue_fk1 FOREIGN KEY (orderdiffusion_id) REFERENCES #__sdi_order_diffusion(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_order_propertyvalue
    ADD CONSTRAINT #__sdi_order_propertyvalue_fk2 FOREIGN KEY (property_id) REFERENCES #__sdi_property(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_order_propertyvalue
    ADD CONSTRAINT #__sdi_order_propertyvalue_fk3 FOREIGN KEY (propertyvalue_id) REFERENCES #__sdi_propertyvalue(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_perimeter
    ADD CONSTRAINT #__sdi_perimeter_fk1 FOREIGN KEY (accessscope_id) REFERENCES #__sdi_sys_accessscope(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_perimeter
    ADD CONSTRAINT #__sdi_perimeter_fk2 FOREIGN KEY (perimetertype_id) REFERENCES #__sdi_sys_perimetertype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_physicalservice
    ADD CONSTRAINT #__sdi_physicalservice_fk1 FOREIGN KEY (serviceconnector_id) REFERENCES #__sdi_sys_serviceconnector(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_physicalservice
    ADD CONSTRAINT #__sdi_physicalservice_fk2 FOREIGN KEY (resourceauthentication_id) REFERENCES #__sdi_sys_authenticationconnector(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_physicalservice
    ADD CONSTRAINT #__sdi_physicalservice_fk3 FOREIGN KEY (serviceauthentication_id) REFERENCES #__sdi_sys_authenticationconnector(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_physicalservice
    ADD CONSTRAINT #__sdi_physicalservice_fk4 FOREIGN KEY (servicescope_id) REFERENCES #__sdi_sys_servicescope(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_physicalservice_organism
    ADD CONSTRAINT #__sdi_physicalservice_organism_fk1 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_physicalservice_organism
    ADD CONSTRAINT #__sdi_physicalservice_organism_fk2 FOREIGN KEY (physicalservice_id) REFERENCES #__sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_physicalservice_policy
    ADD CONSTRAINT #__sdi_physicalservice_policy_fk1 FOREIGN KEY (policy_id) REFERENCES #__sdi_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_physicalservice_policy
    ADD CONSTRAINT #__sdi_physicalservice_policy_fk2 FOREIGN KEY (physicalservice_id) REFERENCES #__sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_physicalservice_policy
    ADD CONSTRAINT #__sdi_physicalservice_policy_fk3 FOREIGN KEY (csw_spatialpolicy_id) REFERENCES #__sdi_csw_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_physicalservice_policy
    ADD CONSTRAINT #__sdi_physicalservice_policy_fk4 FOREIGN KEY (wms_spatialpolicy_id) REFERENCES #__sdi_wms_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_physicalservice_policy
    ADD CONSTRAINT #__sdi_physicalservice_policy_fk5 FOREIGN KEY (wfs_spatialpolicy_id) REFERENCES #__sdi_wfs_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_physicalservice_policy
    ADD CONSTRAINT #__sdi_physicalservice_policy_fk6 FOREIGN KEY (wmts_spatialpolicy_id) REFERENCES #__sdi_wmts_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_physicalservice_servicecompliance
    ADD CONSTRAINT #__sdi_physicalservice_servicecompliance_fk1 FOREIGN KEY (service_id) REFERENCES #__sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_physicalservice_servicecompliance
    ADD CONSTRAINT #__sdi_physicalservice_servicecompliance_fk2 FOREIGN KEY (servicecompliance_id) REFERENCES #__sdi_sys_servicecompliance(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy
    ADD CONSTRAINT #__sdi_policy_fk1 FOREIGN KEY (virtualservice_id) REFERENCES #__sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy
    ADD CONSTRAINT #__sdi_policy_fk2 FOREIGN KEY (accessscope_id) REFERENCES #__sdi_sys_accessscope(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_policy
    ADD CONSTRAINT #__sdi_policy_fk3 FOREIGN KEY (csw_spatialpolicy_id) REFERENCES #__sdi_csw_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_policy
    ADD CONSTRAINT #__sdi_policy_fk4 FOREIGN KEY (wms_spatialpolicy_id) REFERENCES #__sdi_wms_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_policy
    ADD CONSTRAINT #__sdi_policy_fk5 FOREIGN KEY (wfs_spatialpolicy_id) REFERENCES #__sdi_wfs_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_policy
    ADD CONSTRAINT #__sdi_policy_fk6 FOREIGN KEY (wmts_spatialpolicy_id) REFERENCES #__sdi_wmts_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_policy
    ADD CONSTRAINT #__sdi_policy_fk7 FOREIGN KEY (csw_version_id) REFERENCES #__sdi_sys_metadataversion(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_policy_metadatastate
    ADD CONSTRAINT #__sdi_policy_metadatastate_fk1 FOREIGN KEY (policy_id) REFERENCES #__sdi_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy_metadatastate
    ADD CONSTRAINT #__sdi_policy_metadatastate_fk2 FOREIGN KEY (metadatastate_id) REFERENCES #__sdi_sys_metadatastate(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy_organism
    ADD CONSTRAINT #__sdi_policy_organism_fk1 FOREIGN KEY (policy_id) REFERENCES #__sdi_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy_organism
    ADD CONSTRAINT #__sdi_policy_organism_fk2 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy_resourcetype
    ADD CONSTRAINT #__sdi_policy_resourcetype_fk1 FOREIGN KEY (policy_id) REFERENCES #__sdi_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy_resourcetype
    ADD CONSTRAINT #__sdi_policy_resourcetype_fk2 FOREIGN KEY (resourcetype_id) REFERENCES #__sdi_resourcetype(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy_user
    ADD CONSTRAINT #__sdi_policy_user_fk1 FOREIGN KEY (policy_id) REFERENCES #__sdi_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy_user
    ADD CONSTRAINT #__sdi_policy_user_fk2 FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy_visibility
    ADD CONSTRAINT #__sdi_policy_visibility_fk1 FOREIGN KEY (policy_id) REFERENCES #__sdi_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy_visibility
    ADD CONSTRAINT #__sdi_policy_visibility_fk2 FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_policy_visibility
    ADD CONSTRAINT #__sdi_policy_visibility_fk3 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_profile
    ADD CONSTRAINT #__sdi_profile_fk1 FOREIGN KEY (class_id) REFERENCES #__sdi_class(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_property
    ADD CONSTRAINT #__sdi_property_fk1 FOREIGN KEY (accessscope_id) REFERENCES #__sdi_sys_accessscope(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_property
    ADD CONSTRAINT #__sdi_property_fk2 FOREIGN KEY (propertytype_id) REFERENCES #__sdi_sys_propertytype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_propertyvalue
    ADD CONSTRAINT #__sdi_propertyvalue_fk1 FOREIGN KEY (property_id) REFERENCES #__sdi_property(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_relation_catalog
    ADD CONSTRAINT #__sdi_relation_catalog_fk1 FOREIGN KEY (relation_id) REFERENCES #__sdi_relation(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_relation_catalog
    ADD CONSTRAINT #__sdi_relation_catalog_fk2 FOREIGN KEY (catalog_id) REFERENCES #__sdi_catalog(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_relation_defaultvalue
    ADD CONSTRAINT #__sdi_relation_defaultvalue_fk1 FOREIGN KEY (relation_id) REFERENCES #__sdi_relation(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_relation_defaultvalue
    ADD CONSTRAINT #__sdi_relation_defaultvalue_fk2 FOREIGN KEY (attributevalue_id) REFERENCES #__sdi_attributevalue(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_relation_defaultvalue
    ADD CONSTRAINT #__sdi_relation_defaultvalue_fk3 FOREIGN KEY (language_id) REFERENCES #__sdi_language(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_fk1 FOREIGN KEY (parent_id) REFERENCES #__sdi_class(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_fk10 FOREIGN KEY (childresourcetype_id) REFERENCES #__sdi_resourcetype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_fk2 FOREIGN KEY (classchild_id) REFERENCES #__sdi_class(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_fk3 FOREIGN KEY (attributechild_id) REFERENCES #__sdi_attribute(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_fk4 FOREIGN KEY (relationtype_id) REFERENCES #__sdi_sys_relationtype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_fk5 FOREIGN KEY (rendertype_id) REFERENCES #__sdi_sys_rendertype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_fk6 FOREIGN KEY (namespace_id) REFERENCES #__sdi_namespace(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_fk7 FOREIGN KEY (classassociation_id) REFERENCES #__sdi_class(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_fk8 FOREIGN KEY (relationscope_id) REFERENCES #__sdi_sys_relationscope(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_relation
    ADD CONSTRAINT #__sdi_relation_fk9 FOREIGN KEY (editorrelationscope_id) REFERENCES #__sdi_sys_relationscope(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_relation_profile
    ADD CONSTRAINT #__sdi_relation_profile_fk1 FOREIGN KEY (relation_id) REFERENCES #__sdi_relation(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_relation_profile
    ADD CONSTRAINT #__sdi_relation_profile_fk2 FOREIGN KEY (profile_id) REFERENCES #__sdi_profile(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_resource
    ADD CONSTRAINT #__sdi_resource_fk1 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_resource
    ADD CONSTRAINT #__sdi_resource_fk2 FOREIGN KEY (resourcetype_id) REFERENCES #__sdi_resourcetype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_resource
    ADD CONSTRAINT #__sdi_resource_fk3 FOREIGN KEY (accessscope_id) REFERENCES #__sdi_sys_accessscope(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_resourcetype
    ADD CONSTRAINT #__sdi_resourcetype_fk1 FOREIGN KEY (profile_id) REFERENCES #__sdi_profile(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_resourcetype
    ADD CONSTRAINT #__sdi_resourcetype_fk2 FOREIGN KEY (fragmentnamespace_id) REFERENCES #__sdi_namespace(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_resourcetype
    ADD CONSTRAINT #__sdi_resourcetype_fk3 FOREIGN KEY (accessscope_id) REFERENCES #__sdi_sys_accessscope(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_resourcetypelink
    ADD CONSTRAINT #__sdi_resourcetypelink_fk1 FOREIGN KEY (parent_id) REFERENCES #__sdi_resourcetype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_resourcetypelink
    ADD CONSTRAINT #__sdi_resourcetypelink_fk2 FOREIGN KEY (child_id) REFERENCES #__sdi_resourcetype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_resourcetypelink
    ADD CONSTRAINT #__sdi_resourcetypelink_fk3 FOREIGN KEY (class_id) REFERENCES #__sdi_class(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_resourcetypelink
    ADD CONSTRAINT #__sdi_resourcetypelink_fk4 FOREIGN KEY (attribute_id) REFERENCES #__sdi_attribute(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_resourcetypelinkinheritance
    ADD CONSTRAINT #__sdi_resourcetypelinkinheritance_fk1 FOREIGN KEY (resourcetypelink_id) REFERENCES #__sdi_resourcetypelink(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_searchcriteria
    ADD CONSTRAINT #__sdi_searchcriteria_fk1 FOREIGN KEY (criteriatype_id) REFERENCES #__sdi_sys_criteriatype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_searchcriteria
    ADD CONSTRAINT #__sdi_searchcriteria_fk2 FOREIGN KEY (rendertype_id) REFERENCES #__sdi_sys_rendertype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_searchcriteria
    ADD CONSTRAINT #__sdi_searchcriteria_fk3 FOREIGN KEY (relation_id) REFERENCES #__sdi_relation(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_searchcriteriafilter
    ADD CONSTRAINT #__sdi_searchcriteriafilter_fk1 FOREIGN KEY (searchcriteria_id) REFERENCES #__sdi_searchcriteria(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_searchcriteriafilter
    ADD CONSTRAINT #__sdi_searchcriteriafilter_fk2 FOREIGN KEY (language_id) REFERENCES #__sdi_language(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_sys_authenticationconnector
    ADD CONSTRAINT #__sdi_sys_authenticationconnector_fk1 FOREIGN KEY (authenticationlevel_id) REFERENCES #__sdi_sys_authenticationlevel(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_sys_operationcompliance
    ADD CONSTRAINT #__sdi_sys_operationcompliance_fk1 FOREIGN KEY (servicecompliance_id) REFERENCES #__sdi_sys_servicecompliance(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_sys_operationcompliance
    ADD CONSTRAINT #__sdi_sys_operationcompliance_fk2 FOREIGN KEY (serviceoperation_id) REFERENCES #__sdi_sys_serviceoperation(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_sys_rendertype_criteriatype
    ADD CONSTRAINT #__sdi_sys_rendertype_criteriatype_fk1 FOREIGN KEY (criteriatype_id) REFERENCES #__sdi_sys_criteriatype(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_sys_rendertype_criteriatype
    ADD CONSTRAINT #__sdi_sys_rendertype_criteriatype_fk2 FOREIGN KEY (rendertype_id) REFERENCES #__sdi_sys_rendertype(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_sys_rendertype_stereotype
    ADD CONSTRAINT #__sdi_sys_rendertype_stereotype_fk1 FOREIGN KEY (stereotype_id) REFERENCES #__sdi_sys_stereotype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_sys_rendertype_stereotype
    ADD CONSTRAINT #__sdi_sys_rendertype_stereotype_fk2 FOREIGN KEY (rendertype_id) REFERENCES #__sdi_sys_rendertype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_sys_servicecompliance
    ADD CONSTRAINT #__sdi_sys_servicecompliance_fk1 FOREIGN KEY (serviceconnector_id) REFERENCES #__sdi_sys_serviceconnector(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_sys_servicecompliance
    ADD CONSTRAINT #__sdi_sys_servicecompliance_fk2 FOREIGN KEY (serviceversion_id) REFERENCES #__sdi_sys_serviceversion(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_sys_servicecon_authenticationcon
    ADD CONSTRAINT #__sdi_sys_servicecon_authenticationcon_fk1 FOREIGN KEY (serviceconnector_id) REFERENCES #__sdi_sys_serviceconnector(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_sys_servicecon_authenticationcon
    ADD CONSTRAINT #__sdi_sys_servicecon_authenticationcon_fk2 FOREIGN KEY (authenticationconnector_id) REFERENCES #__sdi_sys_authenticationconnector(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_sys_stereotype
    ADD CONSTRAINT #__sdi_sys_stereotype_fk1 FOREIGN KEY (entity_id) REFERENCES #__sdi_sys_entity(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_sys_stereotype
    ADD CONSTRAINT #__sdi_sys_stereotype_fk2 FOREIGN KEY (namespace_id) REFERENCES #__sdi_namespace(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_tilematrix_policy
    ADD CONSTRAINT #__sdi_tilematrix_policy_fk1 FOREIGN KEY (tilematrixsetpolicy_id) REFERENCES #__sdi_tilematrixset_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_tilematrixset_policy
    ADD CONSTRAINT #__sdi_tilematrixset_policy_fk1 FOREIGN KEY (wmtslayerpolicy_id) REFERENCES #__sdi_wmtslayer_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_translation
    ADD CONSTRAINT #__sdi_translation_fk1 FOREIGN KEY (language_id) REFERENCES #__sdi_language(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_user
    ADD CONSTRAINT #__sdi_user_fk1 FOREIGN KEY (user_id) REFERENCES #__users(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_user_role_resource
    ADD CONSTRAINT #__sdi_user_role_resource_fk1 FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_user_role_resource
    ADD CONSTRAINT #__sdi_user_role_resource_fk2 FOREIGN KEY (role_id) REFERENCES #__sdi_sys_role(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_user_role_resource
    ADD CONSTRAINT #__sdi_user_role_resource_fk3 FOREIGN KEY (resource_id) REFERENCES #__sdi_resource(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_user_role_organism
    ADD CONSTRAINT #__sdi_user_role_organism_fk1 FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_user_role_organism
    ADD CONSTRAINT #__sdi_user_role_organism_fk2 FOREIGN KEY (role_id) REFERENCES #__sdi_sys_role(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_user_role_organism
    ADD CONSTRAINT #__sdi_user_role_organism_fk3 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_version
    ADD CONSTRAINT #__sdi_version_fk1 FOREIGN KEY (resource_id) REFERENCES #__sdi_resource(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_versionlink
    ADD CONSTRAINT #__sdi_versionlink_fk1 FOREIGN KEY (parent_id) REFERENCES #__sdi_version(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_versionlink
    ADD CONSTRAINT #__sdi_versionlink_fk2 FOREIGN KEY (child_id) REFERENCES #__sdi_version(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_virtual_physical
    ADD CONSTRAINT #__sdi_virtual_physical_fk1 FOREIGN KEY (virtualservice_id) REFERENCES #__sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_virtual_physical
    ADD CONSTRAINT #__sdi_virtual_physical_fk2 FOREIGN KEY (physicalservice_id) REFERENCES #__sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_virtualmetadata
    ADD CONSTRAINT #__sdi_virtualmetadata_fk1 FOREIGN KEY (virtualservice_id) REFERENCES #__sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_virtualmetadata
    ADD CONSTRAINT #__sdi_virtualmetadata_fk2 FOREIGN KEY (country_id) REFERENCES #__sdi_sys_country(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_virtualservice
    ADD CONSTRAINT #__sdi_virtualservice_fk1 FOREIGN KEY (serviceconnector_id) REFERENCES #__sdi_sys_serviceconnector(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_virtualservice
    ADD CONSTRAINT #__sdi_virtualservice_fk2 FOREIGN KEY (proxytype_id) REFERENCES #__sdi_sys_proxytype(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_virtualservice
    ADD CONSTRAINT #__sdi_virtualservice_fk3 FOREIGN KEY (exceptionlevel_id) REFERENCES #__sdi_sys_exceptionlevel(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_virtualservice
    ADD CONSTRAINT #__sdi_virtualservice_fk4 FOREIGN KEY (loglevel_id) REFERENCES #__sdi_sys_loglevel(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_virtualservice
    ADD CONSTRAINT #__sdi_virtualservice_fk5 FOREIGN KEY (logroll_id) REFERENCES #__sdi_sys_logroll(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_virtualservice
    ADD CONSTRAINT #__sdi_virtualservice_fk6 FOREIGN KEY (servicescope_id) REFERENCES #__sdi_sys_servicescope(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_virtualservice_organism
    ADD CONSTRAINT #__sdi_virtualservice_organism_fk1 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_virtualservice_organism
    ADD CONSTRAINT #__sdi_virtualservice_organism_fk2 FOREIGN KEY (virtualservice_id) REFERENCES #__sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_virtualservice_servicecompliance
    ADD CONSTRAINT #__sdi_virtualservice_servicecompliance_fk1 FOREIGN KEY (service_id) REFERENCES #__sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_virtualservice_servicecompliance
    ADD CONSTRAINT #__sdi_virtualservice_servicecompliance_fk2 FOREIGN KEY (servicecompliance_id) REFERENCES #__sdi_sys_servicecompliance(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_visualization
    ADD CONSTRAINT #__sdi_visualization_fk1 FOREIGN KEY (accessscope_id) REFERENCES #__sdi_sys_accessscope(id) MATCH FULL;
ALTER TABLE #__sdi_visualization  
    ADD CONSTRAINT #__sdi_visualization_fk2 FOREIGN KEY ("version_id") REFERENCES #__sdi_version ("id") MATCH FULL ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE ONLY #__sdi_wmslayer_policy
    ADD CONSTRAINT #__sdi_wmslayer_policy_fk1 FOREIGN KEY (physicalservicepolicy_id) REFERENCES #__sdi_physicalservice_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_wmslayer_policy
    ADD CONSTRAINT #__sdi_wmslayer_policy_fk2 FOREIGN KEY (spatialpolicy_id) REFERENCES #__sdi_wms_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_wmts_spatialpolicy
    ADD CONSTRAINT #__sdi_wmts_spatialpolicy_fk1 FOREIGN KEY (spatialoperator_id) REFERENCES #__sdi_sys_spatialoperator(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_wmtslayer_policy
    ADD CONSTRAINT #__sdi_wmtslayer_policy_fk1 FOREIGN KEY (physicalservicepolicy_id) REFERENCES #__sdi_physicalservice_policy(id) MATCH FULL ON DELETE CASCADE;
ALTER TABLE ONLY #__sdi_wmtslayer_policy
    ADD CONSTRAINT #__sdi_wmtslayer_policy_fk2 FOREIGN KEY (spatialpolicy_id) REFERENCES #__sdi_wmts_spatialpolicy(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_attribute
    ADD CONSTRAINT sdi_attribute_fk1 FOREIGN KEY (namespace_id) REFERENCES #__sdi_namespace(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_attribute
    ADD CONSTRAINT sdi_attribute_fk2 FOREIGN KEY (listnamespace_id) REFERENCES #__sdi_namespace(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_attribute
    ADD CONSTRAINT sdi_attribute_fk3 FOREIGN KEY (stereotype_id) REFERENCES #__sdi_sys_stereotype(id) MATCH FULL;

