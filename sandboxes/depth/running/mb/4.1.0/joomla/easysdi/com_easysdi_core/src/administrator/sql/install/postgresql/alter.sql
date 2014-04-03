--
-- TOC entry 2980 (class 2606 OID 29699)
-- Name: UNIQUE_NAME; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jobs
    ADD CONSTRAINT "UNIQUE_NAME" UNIQUE ("NAME");


--
-- TOC entry 3454 (class 2606 OID 30121)
-- Name: URL_UNIQUE; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY overview_page
    ADD CONSTRAINT "URL_UNIQUE" UNIQUE ("NAME");


--
-- TOC entry 2954 (class 2606 OID 29681)
-- Name: action_types_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY action_types
    ADD CONSTRAINT action_types_pkey PRIMARY KEY ("ID_ACTION_TYPE");


--
-- TOC entry 2958 (class 2606 OID 29683)
-- Name: actions_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY actions
    ADD CONSTRAINT actions_pkey PRIMARY KEY ("ID_ACTION");


--
-- TOC entry 2963 (class 2606 OID 29685)
-- Name: alerts_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY alerts
    ADD CONSTRAINT alerts_pkey PRIMARY KEY ("ID_ALERT");


--
-- TOC entry 3089 (class 2606 OID 29867)
-- Name: alias; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_layergroup
    ADD CONSTRAINT alias UNIQUE (alias);


--
-- TOC entry 2965 (class 2606 OID 29687)
-- Name: holidays_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY holidays
    ADD CONSTRAINT holidays_pkey PRIMARY KEY ("ID_HOLIDAYS");


--
-- TOC entry 2967 (class 2606 OID 29689)
-- Name: http_methods_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY http_methods
    ADD CONSTRAINT http_methods_pkey PRIMARY KEY ("ID_HTTP_METHOD");


--
-- TOC entry 2970 (class 2606 OID 29691)
-- Name: job_agg_hour_log_entries_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY job_agg_hour_log_entries
    ADD CONSTRAINT job_agg_hour_log_entries_pkey PRIMARY KEY ("DATE_LOG", "ID_JOB");


--
-- TOC entry 2973 (class 2606 OID 29693)
-- Name: job_agg_log_entries_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY job_agg_log_entries
    ADD CONSTRAINT job_agg_log_entries_pkey PRIMARY KEY ("DATE_LOG", "ID_JOB");


--
-- TOC entry 2975 (class 2606 OID 29695)
-- Name: job_defaults_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY job_defaults
    ADD CONSTRAINT job_defaults_pkey PRIMARY KEY ("ID_PARAM");


--
-- TOC entry 2982 (class 2606 OID 29697)
-- Name: jobs_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY ("ID_JOB");


--
-- TOC entry 2986 (class 2606 OID 29811)
-- Name: jos_sdi_accessscope_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_accessscope
    ADD CONSTRAINT jos_sdi_accessscope_pkey PRIMARY KEY (id);


--
-- TOC entry 2992 (class 2606 OID 29813)
-- Name: jos_sdi_address_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_address
    ADD CONSTRAINT jos_sdi_address_pkey PRIMARY KEY (id);


--
-- TOC entry 2994 (class 2606 OID 29815)
-- Name: jos_sdi_allowedoperation_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_allowedoperation
    ADD CONSTRAINT jos_sdi_allowedoperation_pkey PRIMARY KEY (id);


--
-- TOC entry 2999 (class 2606 OID 29817)
-- Name: jos_sdi_application_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_application
    ADD CONSTRAINT jos_sdi_application_pkey PRIMARY KEY (id);


--
-- TOC entry 3004 (class 2606 OID 29819)
-- Name: jos_sdi_assignment_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_assignment
    ADD CONSTRAINT jos_sdi_assignment_pkey PRIMARY KEY (id);


--
-- TOC entry 3006 (class 2606 OID 29821)
-- Name: jos_sdi_attribute_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_attribute
    ADD CONSTRAINT jos_sdi_attribute_pkey PRIMARY KEY (id);


--
-- TOC entry 3012 (class 2606 OID 29823)
-- Name: jos_sdi_attributevalue_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_attributevalue
    ADD CONSTRAINT jos_sdi_attributevalue_pkey PRIMARY KEY (id);


--
-- TOC entry 3016 (class 2606 OID 29825)
-- Name: jos_sdi_boundary_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_boundary
    ADD CONSTRAINT jos_sdi_boundary_pkey PRIMARY KEY (id);


--
-- TOC entry 3019 (class 2606 OID 29827)
-- Name: jos_sdi_boundarycategory_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_boundarycategory
    ADD CONSTRAINT jos_sdi_boundarycategory_pkey PRIMARY KEY (id);


--
-- TOC entry 3021 (class 2606 OID 29829)
-- Name: jos_sdi_catalog_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog
    ADD CONSTRAINT jos_sdi_catalog_pkey PRIMARY KEY (id);


--
-- TOC entry 3025 (class 2606 OID 29831)
-- Name: jos_sdi_catalog_resourcetype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog_resourcetype
    ADD CONSTRAINT jos_sdi_catalog_resourcetype_pkey PRIMARY KEY (id);


--
-- TOC entry 3030 (class 2606 OID 29833)
-- Name: jos_sdi_catalog_searchcriteria_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog_searchcriteria
    ADD CONSTRAINT jos_sdi_catalog_searchcriteria_pkey PRIMARY KEY (id);


--
-- TOC entry 3034 (class 2606 OID 29835)
-- Name: jos_sdi_catalog_searchsort_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog_searchsort
    ADD CONSTRAINT jos_sdi_catalog_searchsort_pkey PRIMARY KEY (id);


--
-- TOC entry 3038 (class 2606 OID 29837)
-- Name: jos_sdi_class_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_class
    ADD CONSTRAINT jos_sdi_class_pkey PRIMARY KEY (id);


--
-- TOC entry 3040 (class 2606 OID 29839)
-- Name: jos_sdi_csw_spatialpolicy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_csw_spatialpolicy
    ADD CONSTRAINT jos_sdi_csw_spatialpolicy_pkey PRIMARY KEY (id);


--
-- TOC entry 3051 (class 2606 OID 29843)
-- Name: jos_sdi_diffusion_download_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_download
    ADD CONSTRAINT jos_sdi_diffusion_download_pkey PRIMARY KEY (id);


--
-- TOC entry 3055 (class 2606 OID 29845)
-- Name: jos_sdi_diffusion_notifieduser_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_notifieduser
    ADD CONSTRAINT jos_sdi_diffusion_notifieduser_pkey PRIMARY KEY (id);


--
-- TOC entry 3059 (class 2606 OID 29847)
-- Name: jos_sdi_diffusion_perimeter_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_perimeter
    ADD CONSTRAINT jos_sdi_diffusion_perimeter_pkey PRIMARY KEY (id);


--
-- TOC entry 3047 (class 2606 OID 29841)
-- Name: jos_sdi_diffusion_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion
    ADD CONSTRAINT jos_sdi_diffusion_pkey PRIMARY KEY (id);


--
-- TOC entry 3063 (class 2606 OID 29849)
-- Name: jos_sdi_diffusion_propertyvalue_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_propertyvalue
    ADD CONSTRAINT jos_sdi_diffusion_propertyvalue_pkey PRIMARY KEY (id);


--
-- TOC entry 3066 (class 2606 OID 29851)
-- Name: jos_sdi_excludedattribute_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_excludedattribute
    ADD CONSTRAINT jos_sdi_excludedattribute_pkey PRIMARY KEY (id);


--
-- TOC entry 3070 (class 2606 OID 29853)
-- Name: jos_sdi_featuretype_policy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_featuretype_policy
    ADD CONSTRAINT jos_sdi_featuretype_policy_pkey PRIMARY KEY (id);


--
-- TOC entry 3075 (class 2606 OID 29855)
-- Name: jos_sdi_importref_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_importref
    ADD CONSTRAINT jos_sdi_importref_pkey PRIMARY KEY (id);


--
-- TOC entry 3078 (class 2606 OID 29857)
-- Name: jos_sdi_includedattribute_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_includedattribute
    ADD CONSTRAINT jos_sdi_includedattribute_pkey PRIMARY KEY (id);


--
-- TOC entry 3080 (class 2606 OID 29859)
-- Name: jos_sdi_language_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_language
    ADD CONSTRAINT jos_sdi_language_pkey PRIMARY KEY (id);


--
-- TOC entry 3087 (class 2606 OID 29863)
-- Name: jos_sdi_layer_layergroup_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_layer_layergroup
    ADD CONSTRAINT jos_sdi_layer_layergroup_pkey PRIMARY KEY (id);


--
-- TOC entry 3083 (class 2606 OID 29861)
-- Name: jos_sdi_layer_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_layer
    ADD CONSTRAINT jos_sdi_layer_pkey PRIMARY KEY (id);


--
-- TOC entry 3091 (class 2606 OID 29865)
-- Name: jos_sdi_layergroup_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_layergroup
    ADD CONSTRAINT jos_sdi_layergroup_pkey PRIMARY KEY (id);


--
-- TOC entry 3093 (class 2606 OID 29871)
-- Name: jos_sdi_map_alias_key; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map
    ADD CONSTRAINT jos_sdi_map_alias_key UNIQUE (alias);


--
-- TOC entry 3100 (class 2606 OID 29873)
-- Name: jos_sdi_map_layergroup_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_layergroup
    ADD CONSTRAINT jos_sdi_map_layergroup_pkey PRIMARY KEY (id);


--
-- TOC entry 3104 (class 2606 OID 29875)
-- Name: jos_sdi_map_physicalservice_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_physicalservice
    ADD CONSTRAINT jos_sdi_map_physicalservice_pkey PRIMARY KEY (id);


--
-- TOC entry 3096 (class 2606 OID 29869)
-- Name: jos_sdi_map_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map
    ADD CONSTRAINT jos_sdi_map_pkey PRIMARY KEY (id);


--
-- TOC entry 3108 (class 2606 OID 29877)
-- Name: jos_sdi_map_tool_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_tool
    ADD CONSTRAINT jos_sdi_map_tool_pkey PRIMARY KEY (id);


--
-- TOC entry 3112 (class 2606 OID 29879)
-- Name: jos_sdi_map_virtualservice_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_virtualservice
    ADD CONSTRAINT jos_sdi_map_virtualservice_pkey PRIMARY KEY (id);


--
-- TOC entry 3114 (class 2606 OID 29883)
-- Name: jos_sdi_maplayer_alias_key; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_maplayer
    ADD CONSTRAINT jos_sdi_maplayer_alias_key UNIQUE (alias);


--
-- TOC entry 3117 (class 2606 OID 29881)
-- Name: jos_sdi_maplayer_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_maplayer
    ADD CONSTRAINT jos_sdi_maplayer_pkey PRIMARY KEY (id);


--
-- TOC entry 3122 (class 2606 OID 29885)
-- Name: jos_sdi_metadata_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_metadata
    ADD CONSTRAINT jos_sdi_metadata_pkey PRIMARY KEY (id);


--
-- TOC entry 3124 (class 2606 OID 29887)
-- Name: jos_sdi_monitor_exports_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_monitor_exports
    ADD CONSTRAINT jos_sdi_monitor_exports_pkey PRIMARY KEY (id);


--
-- TOC entry 3126 (class 2606 OID 29889)
-- Name: jos_sdi_namespace_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_namespace
    ADD CONSTRAINT jos_sdi_namespace_pkey PRIMARY KEY (id);


--
-- TOC entry 3137 (class 2606 OID 29893)
-- Name: jos_sdi_order_diffusion_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_diffusion
    ADD CONSTRAINT jos_sdi_order_diffusion_pkey PRIMARY KEY (id);


--
-- TOC entry 3141 (class 2606 OID 29895)
-- Name: jos_sdi_order_perimeter_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_perimeter
    ADD CONSTRAINT jos_sdi_order_perimeter_pkey PRIMARY KEY (id);


--
-- TOC entry 3132 (class 2606 OID 29891)
-- Name: jos_sdi_order_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order
    ADD CONSTRAINT jos_sdi_order_pkey PRIMARY KEY (id);


--
-- TOC entry 3146 (class 2606 OID 29897)
-- Name: jos_sdi_order_propertyvalue_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_propertyvalue
    ADD CONSTRAINT jos_sdi_order_propertyvalue_pkey PRIMARY KEY (id);


--
-- TOC entry 3148 (class 2606 OID 29899)
-- Name: jos_sdi_organism_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_organism
    ADD CONSTRAINT jos_sdi_organism_pkey PRIMARY KEY (id);


--
-- TOC entry 3152 (class 2606 OID 29901)
-- Name: jos_sdi_perimeter_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_perimeter
    ADD CONSTRAINT jos_sdi_perimeter_pkey PRIMARY KEY (id);


--
-- TOC entry 3164 (class 2606 OID 29907)
-- Name: jos_sdi_physicalservice_organism_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_organism
    ADD CONSTRAINT jos_sdi_physicalservice_organism_pkey PRIMARY KEY (id);


--
-- TOC entry 3158 (class 2606 OID 29903)
-- Name: jos_sdi_physicalservice_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice
    ADD CONSTRAINT jos_sdi_physicalservice_pkey PRIMARY KEY (id);


--
-- TOC entry 3172 (class 2606 OID 29909)
-- Name: jos_sdi_physicalservice_policy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_policy
    ADD CONSTRAINT jos_sdi_physicalservice_policy_pkey PRIMARY KEY (id);


--
-- TOC entry 3176 (class 2606 OID 29911)
-- Name: jos_sdi_physicalservice_servicecompliance_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_servicecompliance
    ADD CONSTRAINT jos_sdi_physicalservice_servicecompliance_pkey PRIMARY KEY (id);


--
-- TOC entry 3189 (class 2606 OID 29915)
-- Name: jos_sdi_policy_metadatastate_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_metadatastate
    ADD CONSTRAINT jos_sdi_policy_metadatastate_pkey PRIMARY KEY (id);


--
-- TOC entry 3193 (class 2606 OID 29917)
-- Name: jos_sdi_policy_organism_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_organism
    ADD CONSTRAINT jos_sdi_policy_organism_pkey PRIMARY KEY (id);


--
-- TOC entry 3185 (class 2606 OID 29913)
-- Name: jos_sdi_policy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy
    ADD CONSTRAINT jos_sdi_policy_pkey PRIMARY KEY (id);


--
-- TOC entry 3197 (class 2606 OID 29919)
-- Name: jos_sdi_policy_resourcetype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_resourcetype
    ADD CONSTRAINT jos_sdi_policy_resourcetype_pkey PRIMARY KEY (id);


--
-- TOC entry 3201 (class 2606 OID 29921)
-- Name: jos_sdi_policy_user_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_user
    ADD CONSTRAINT jos_sdi_policy_user_pkey PRIMARY KEY (id);


--
-- TOC entry 3206 (class 2606 OID 29923)
-- Name: jos_sdi_policy_visibility_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_visibility
    ADD CONSTRAINT jos_sdi_policy_visibility_pkey PRIMARY KEY (id);


--
-- TOC entry 3209 (class 2606 OID 29925)
-- Name: jos_sdi_profile_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_profile
    ADD CONSTRAINT jos_sdi_profile_pkey PRIMARY KEY (id);


--
-- TOC entry 3213 (class 2606 OID 29927)
-- Name: jos_sdi_property_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_property
    ADD CONSTRAINT jos_sdi_property_pkey PRIMARY KEY (id);


--
-- TOC entry 3216 (class 2606 OID 29929)
-- Name: jos_sdi_propertyvalue_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_propertyvalue
    ADD CONSTRAINT jos_sdi_propertyvalue_pkey PRIMARY KEY (id);


--
-- TOC entry 3232 (class 2606 OID 29933)
-- Name: jos_sdi_relation_catalog_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation_catalog
    ADD CONSTRAINT jos_sdi_relation_catalog_pkey PRIMARY KEY (id);


--
-- TOC entry 3237 (class 2606 OID 29935)
-- Name: jos_sdi_relation_defaultvalue_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation_defaultvalue
    ADD CONSTRAINT jos_sdi_relation_defaultvalue_pkey PRIMARY KEY (id);


--
-- TOC entry 3228 (class 2606 OID 29931)
-- Name: jos_sdi_relation_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_pkey PRIMARY KEY (id);


--
-- TOC entry 3241 (class 2606 OID 29937)
-- Name: jos_sdi_relation_profile_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation_profile
    ADD CONSTRAINT jos_sdi_relation_profile_pkey PRIMARY KEY (id);


--
-- TOC entry 3246 (class 2606 OID 29939)
-- Name: jos_sdi_resource_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resource
    ADD CONSTRAINT jos_sdi_resource_pkey PRIMARY KEY (id);


--
-- TOC entry 3251 (class 2606 OID 29941)
-- Name: jos_sdi_resourcetype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetype
    ADD CONSTRAINT jos_sdi_resourcetype_pkey PRIMARY KEY (id);


--
-- TOC entry 3257 (class 2606 OID 29943)
-- Name: jos_sdi_resourcetypelink_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetypelink
    ADD CONSTRAINT jos_sdi_resourcetypelink_pkey PRIMARY KEY (id);


--
-- TOC entry 3260 (class 2606 OID 29945)
-- Name: jos_sdi_resourcetypelinkinheritance_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetypelinkinheritance
    ADD CONSTRAINT jos_sdi_resourcetypelinkinheritance_pkey PRIMARY KEY (id);


--
-- TOC entry 3265 (class 2606 OID 29947)
-- Name: jos_sdi_searchcriteria_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_searchcriteria
    ADD CONSTRAINT jos_sdi_searchcriteria_pkey PRIMARY KEY (id);


--
-- TOC entry 3269 (class 2606 OID 29949)
-- Name: jos_sdi_searchcriteriafilter_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_searchcriteriafilter
    ADD CONSTRAINT jos_sdi_searchcriteriafilter_pkey PRIMARY KEY (id);


--
-- TOC entry 3271 (class 2606 OID 29951)
-- Name: jos_sdi_sys_accessscope_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_accessscope
    ADD CONSTRAINT jos_sdi_sys_accessscope_pkey PRIMARY KEY (id);


--
-- TOC entry 3273 (class 2606 OID 29953)
-- Name: jos_sdi_sys_addresstype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_addresstype
    ADD CONSTRAINT jos_sdi_sys_addresstype_pkey PRIMARY KEY (id);


--
-- TOC entry 3276 (class 2606 OID 29955)
-- Name: jos_sdi_sys_authenticationconnector_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_authenticationconnector
    ADD CONSTRAINT jos_sdi_sys_authenticationconnector_pkey PRIMARY KEY (id);


--
-- TOC entry 3278 (class 2606 OID 29957)
-- Name: jos_sdi_sys_authenticationlevel_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_authenticationlevel
    ADD CONSTRAINT jos_sdi_sys_authenticationlevel_pkey PRIMARY KEY (id);


--
-- TOC entry 3280 (class 2606 OID 29959)
-- Name: jos_sdi_sys_country_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_country
    ADD CONSTRAINT jos_sdi_sys_country_pkey PRIMARY KEY (id);


--
-- TOC entry 3282 (class 2606 OID 29961)
-- Name: jos_sdi_sys_criteriatype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_criteriatype
    ADD CONSTRAINT jos_sdi_sys_criteriatype_pkey PRIMARY KEY (id);


--
-- TOC entry 3284 (class 2606 OID 29963)
-- Name: jos_sdi_sys_entity_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_entity
    ADD CONSTRAINT jos_sdi_sys_entity_pkey PRIMARY KEY (id);


--
-- TOC entry 3286 (class 2606 OID 29965)
-- Name: jos_sdi_sys_exceptionlevel_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_exceptionlevel
    ADD CONSTRAINT jos_sdi_sys_exceptionlevel_pkey PRIMARY KEY (id);


--
-- TOC entry 3288 (class 2606 OID 29967)
-- Name: jos_sdi_sys_importtype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_importtype
    ADD CONSTRAINT jos_sdi_sys_importtype_pkey PRIMARY KEY (id);


--
-- TOC entry 3290 (class 2606 OID 29969)
-- Name: jos_sdi_sys_isolanguage_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_isolanguage
    ADD CONSTRAINT jos_sdi_sys_isolanguage_pkey PRIMARY KEY (id);


--
-- TOC entry 3292 (class 2606 OID 29971)
-- Name: jos_sdi_sys_loglevel_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_loglevel
    ADD CONSTRAINT jos_sdi_sys_loglevel_pkey PRIMARY KEY (id);


--
-- TOC entry 3294 (class 2606 OID 29973)
-- Name: jos_sdi_sys_logroll_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_logroll
    ADD CONSTRAINT jos_sdi_sys_logroll_pkey PRIMARY KEY (id);


--
-- TOC entry 3296 (class 2606 OID 29975)
-- Name: jos_sdi_sys_maptool_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_maptool
    ADD CONSTRAINT jos_sdi_sys_maptool_pkey PRIMARY KEY (id);


--
-- TOC entry 3298 (class 2606 OID 29977)
-- Name: jos_sdi_sys_metadatastate_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_metadatastate
    ADD CONSTRAINT jos_sdi_sys_metadatastate_pkey PRIMARY KEY (id);


--
-- TOC entry 3300 (class 2606 OID 29979)
-- Name: jos_sdi_sys_metadataversion_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_metadataversion
    ADD CONSTRAINT jos_sdi_sys_metadataversion_pkey PRIMARY KEY (id);


--
-- TOC entry 3304 (class 2606 OID 29981)
-- Name: jos_sdi_sys_operationcompliance_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_operationcompliance
    ADD CONSTRAINT jos_sdi_sys_operationcompliance_pkey PRIMARY KEY (id);


--
-- TOC entry 3306 (class 2606 OID 29983)
-- Name: jos_sdi_sys_orderstate_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_orderstate
    ADD CONSTRAINT jos_sdi_sys_orderstate_pkey PRIMARY KEY (id);


--
-- TOC entry 3308 (class 2606 OID 29985)
-- Name: jos_sdi_sys_ordertype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_ordertype
    ADD CONSTRAINT jos_sdi_sys_ordertype_pkey PRIMARY KEY (id);


--
-- TOC entry 3310 (class 2606 OID 29987)
-- Name: jos_sdi_sys_perimetertype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_perimetertype
    ADD CONSTRAINT jos_sdi_sys_perimetertype_pkey PRIMARY KEY (id);


--
-- TOC entry 3312 (class 2606 OID 29989)
-- Name: jos_sdi_sys_pricing_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_pricing
    ADD CONSTRAINT jos_sdi_sys_pricing_pkey PRIMARY KEY (id);


--
-- TOC entry 3314 (class 2606 OID 29991)
-- Name: jos_sdi_sys_productmining_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_productmining
    ADD CONSTRAINT jos_sdi_sys_productmining_pkey PRIMARY KEY (id);


--
-- TOC entry 3316 (class 2606 OID 29993)
-- Name: jos_sdi_sys_productstate_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_productstate
    ADD CONSTRAINT jos_sdi_sys_productstate_pkey PRIMARY KEY (id);


--
-- TOC entry 3318 (class 2606 OID 29995)
-- Name: jos_sdi_sys_productstorage_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_productstorage
    ADD CONSTRAINT jos_sdi_sys_productstorage_pkey PRIMARY KEY (id);


--
-- TOC entry 3320 (class 2606 OID 29997)
-- Name: jos_sdi_sys_propertytype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_propertytype
    ADD CONSTRAINT jos_sdi_sys_propertytype_pkey PRIMARY KEY (id);


--
-- TOC entry 3322 (class 2606 OID 29999)
-- Name: jos_sdi_sys_proxytype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_proxytype
    ADD CONSTRAINT jos_sdi_sys_proxytype_pkey PRIMARY KEY (id);


--
-- TOC entry 3324 (class 2606 OID 30001)
-- Name: jos_sdi_sys_relationscope_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_relationscope
    ADD CONSTRAINT jos_sdi_sys_relationscope_pkey PRIMARY KEY (id);


--
-- TOC entry 3326 (class 2606 OID 30003)
-- Name: jos_sdi_sys_relationtype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_relationtype
    ADD CONSTRAINT jos_sdi_sys_relationtype_pkey PRIMARY KEY (id);


--
-- TOC entry 3332 (class 2606 OID 30007)
-- Name: jos_sdi_sys_rendertype_criteriatype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_rendertype_criteriatype
    ADD CONSTRAINT jos_sdi_sys_rendertype_criteriatype_pkey PRIMARY KEY (id);


--
-- TOC entry 3328 (class 2606 OID 30005)
-- Name: jos_sdi_sys_rendertype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_rendertype
    ADD CONSTRAINT jos_sdi_sys_rendertype_pkey PRIMARY KEY (id);


--
-- TOC entry 3336 (class 2606 OID 30009)
-- Name: jos_sdi_sys_rendertype_stereotype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_rendertype_stereotype
    ADD CONSTRAINT jos_sdi_sys_rendertype_stereotype_pkey PRIMARY KEY (id);


--
-- TOC entry 3338 (class 2606 OID 30011)
-- Name: jos_sdi_sys_role_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_role
    ADD CONSTRAINT jos_sdi_sys_role_pkey PRIMARY KEY (id);


--
-- TOC entry 3340 (class 2606 OID 30013)
-- Name: jos_sdi_sys_searchtab_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_searchtab
    ADD CONSTRAINT jos_sdi_sys_searchtab_pkey PRIMARY KEY (id);


--
-- TOC entry 3344 (class 2606 OID 30015)
-- Name: jos_sdi_sys_servicecompliance_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_servicecompliance
    ADD CONSTRAINT jos_sdi_sys_servicecompliance_pkey PRIMARY KEY (id);


--
-- TOC entry 3348 (class 2606 OID 30017)
-- Name: jos_sdi_sys_servicecon_authenticationcon_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_servicecon_authenticationcon
    ADD CONSTRAINT jos_sdi_sys_servicecon_authenticationcon_pkey PRIMARY KEY (id);


--
-- TOC entry 3350 (class 2606 OID 30019)
-- Name: jos_sdi_sys_serviceconnector_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_serviceconnector
    ADD CONSTRAINT jos_sdi_sys_serviceconnector_pkey PRIMARY KEY (id);


--
-- TOC entry 3352 (class 2606 OID 30021)
-- Name: jos_sdi_sys_serviceoperation_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_serviceoperation
    ADD CONSTRAINT jos_sdi_sys_serviceoperation_pkey PRIMARY KEY (id);


--
-- TOC entry 3354 (class 2606 OID 30023)
-- Name: jos_sdi_sys_servicescope_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_servicescope
    ADD CONSTRAINT jos_sdi_sys_servicescope_pkey PRIMARY KEY (id);


--
-- TOC entry 3356 (class 2606 OID 30025)
-- Name: jos_sdi_sys_servicetype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_servicetype
    ADD CONSTRAINT jos_sdi_sys_servicetype_pkey PRIMARY KEY (id);


--
-- TOC entry 3358 (class 2606 OID 30027)
-- Name: jos_sdi_sys_serviceversion_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_serviceversion
    ADD CONSTRAINT jos_sdi_sys_serviceversion_pkey PRIMARY KEY (id);


--
-- TOC entry 3360 (class 2606 OID 30029)
-- Name: jos_sdi_sys_spatialoperator_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_spatialoperator
    ADD CONSTRAINT jos_sdi_sys_spatialoperator_pkey PRIMARY KEY (id);


--
-- TOC entry 3364 (class 2606 OID 30031)
-- Name: jos_sdi_sys_stereotype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_stereotype
    ADD CONSTRAINT jos_sdi_sys_stereotype_pkey PRIMARY KEY (id);


--
-- TOC entry 3366 (class 2606 OID 30033)
-- Name: jos_sdi_sys_topiccategory_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_topiccategory
    ADD CONSTRAINT jos_sdi_sys_topiccategory_pkey PRIMARY KEY (id);


--
-- TOC entry 3369 (class 2606 OID 30035)
-- Name: jos_sdi_sys_unit_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_unit
    ADD CONSTRAINT jos_sdi_sys_unit_pkey PRIMARY KEY (id);


--
-- TOC entry 3371 (class 2606 OID 30037)
-- Name: jos_sdi_sys_versiontype_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_versiontype
    ADD CONSTRAINT jos_sdi_sys_versiontype_pkey PRIMARY KEY (id);


--
-- TOC entry 3374 (class 2606 OID 30039)
-- Name: jos_sdi_tilematrix_policy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_tilematrix_policy
    ADD CONSTRAINT jos_sdi_tilematrix_policy_pkey PRIMARY KEY (id);


--
-- TOC entry 3377 (class 2606 OID 30041)
-- Name: jos_sdi_tilematrixset_policy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_tilematrixset_policy
    ADD CONSTRAINT jos_sdi_tilematrixset_policy_pkey PRIMARY KEY (id);


--
-- TOC entry 3381 (class 2606 OID 30043)
-- Name: jos_sdi_translation_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_translation
    ADD CONSTRAINT jos_sdi_translation_pkey PRIMARY KEY (id);


--
-- TOC entry 3384 (class 2606 OID 30045)
-- Name: jos_sdi_user_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_user
    ADD CONSTRAINT jos_sdi_user_pkey PRIMARY KEY (id);


--
-- TOC entry 3386 (class 2606 OID 30047)
-- Name: jos_sdi_user_role_organism_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_user_role_organism
    ADD CONSTRAINT jos_sdi_user_role_organism_pkey PRIMARY KEY (id);


--
-- TOC entry 3391 (class 2606 OID 30049)
-- Name: jos_sdi_user_role_resource_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_user_role_resource
    ADD CONSTRAINT jos_sdi_user_role_resource_pkey PRIMARY KEY (id);


--
-- TOC entry 3394 (class 2606 OID 30051)
-- Name: jos_sdi_version_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_version
    ADD CONSTRAINT jos_sdi_version_pkey PRIMARY KEY (id);


--
-- TOC entry 3398 (class 2606 OID 30053)
-- Name: jos_sdi_versionlink_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_versionlink
    ADD CONSTRAINT jos_sdi_versionlink_pkey PRIMARY KEY (id);


--
-- TOC entry 3402 (class 2606 OID 30055)
-- Name: jos_sdi_virtual_physical_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtual_physical
    ADD CONSTRAINT jos_sdi_virtual_physical_pkey PRIMARY KEY (id);


--
-- TOC entry 3406 (class 2606 OID 30057)
-- Name: jos_sdi_virtualmetadata_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualmetadata
    ADD CONSTRAINT jos_sdi_virtualmetadata_pkey PRIMARY KEY (id);


--
-- TOC entry 3418 (class 2606 OID 30061)
-- Name: jos_sdi_virtualservice_organism_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice_organism
    ADD CONSTRAINT jos_sdi_virtualservice_organism_pkey PRIMARY KEY (id);


--
-- TOC entry 3414 (class 2606 OID 30059)
-- Name: jos_sdi_virtualservice_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice
    ADD CONSTRAINT jos_sdi_virtualservice_pkey PRIMARY KEY (id);


--
-- TOC entry 3422 (class 2606 OID 30063)
-- Name: jos_sdi_virtualservice_servicecompliance_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice_servicecompliance
    ADD CONSTRAINT jos_sdi_virtualservice_servicecompliance_pkey PRIMARY KEY (id);


--
-- TOC entry 3424 (class 2606 OID 30067)
-- Name: jos_sdi_visualization_alias_key; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_visualization
    ADD CONSTRAINT jos_sdi_visualization_alias_key UNIQUE (alias);


--
-- TOC entry 3427 (class 2606 OID 30065)
-- Name: jos_sdi_visualization_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_visualization
    ADD CONSTRAINT jos_sdi_visualization_pkey PRIMARY KEY (id);


--
-- TOC entry 3429 (class 2606 OID 30069)
-- Name: jos_sdi_wfs_spatialpolicy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_wfs_spatialpolicy
    ADD CONSTRAINT jos_sdi_wfs_spatialpolicy_pkey PRIMARY KEY (id);


--
-- TOC entry 3431 (class 2606 OID 30071)
-- Name: jos_sdi_wms_spatialpolicy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_wms_spatialpolicy
    ADD CONSTRAINT jos_sdi_wms_spatialpolicy_pkey PRIMARY KEY (id);


--
-- TOC entry 3435 (class 2606 OID 30073)
-- Name: jos_sdi_wmslayer_policy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_wmslayer_policy
    ADD CONSTRAINT jos_sdi_wmslayer_policy_pkey PRIMARY KEY (id);


--
-- TOC entry 3438 (class 2606 OID 30075)
-- Name: jos_sdi_wmts_spatialpolicy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_wmts_spatialpolicy
    ADD CONSTRAINT jos_sdi_wmts_spatialpolicy_pkey PRIMARY KEY (id);


--
-- TOC entry 3442 (class 2606 OID 30077)
-- Name: jos_sdi_wmtslayer_policy_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_wmtslayer_policy
    ADD CONSTRAINT jos_sdi_wmtslayer_policy_pkey PRIMARY KEY (id);


--
-- TOC entry 3444 (class 2606 OID 30113)
-- Name: last_ids_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY last_ids
    ADD CONSTRAINT last_ids_pkey PRIMARY KEY ("TABLE_NAME");


--
-- TOC entry 3447 (class 2606 OID 30115)
-- Name: last_query_results_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY last_query_results
    ADD CONSTRAINT last_query_results_pkey PRIMARY KEY ("ID_LAST_QUERY_RESULT");


--
-- TOC entry 3452 (class 2606 OID 30117)
-- Name: log_entries_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY log_entries
    ADD CONSTRAINT log_entries_pkey PRIMARY KEY ("ID_LOG_ENTRY");


--
-- TOC entry 3160 (class 2606 OID 29905)
-- Name: name; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice
    ADD CONSTRAINT name UNIQUE (name);


--
-- TOC entry 3456 (class 2606 OID 30119)
-- Name: overview_page_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY overview_page
    ADD CONSTRAINT overview_page_pkey PRIMARY KEY ("ID_OVERVIEW_PAGE");


--
-- TOC entry 3461 (class 2606 OID 30123)
-- Name: overview_queries_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY overview_queries
    ADD CONSTRAINT overview_queries_pkey PRIMARY KEY ("ID_OVERVIEW_QUERY");


--
-- TOC entry 3464 (class 2606 OID 30125)
-- Name: periods_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY periods
    ADD CONSTRAINT periods_pkey PRIMARY KEY ("ID_PERIODS");


--
-- TOC entry 3469 (class 2606 OID 30127)
-- Name: queries_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY queries
    ADD CONSTRAINT queries_pkey PRIMARY KEY ("ID_QUERY");


--
-- TOC entry 3472 (class 2606 OID 30129)
-- Name: query_agg_hour_log_entries_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY query_agg_hour_log_entries
    ADD CONSTRAINT query_agg_hour_log_entries_pkey PRIMARY KEY ("DATE_LOG", "ID_QUERY");


--
-- TOC entry 3475 (class 2606 OID 30131)
-- Name: query_agg_log_entries_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY query_agg_log_entries
    ADD CONSTRAINT query_agg_log_entries_pkey PRIMARY KEY ("DATE_LOG", "ID_QUERY");


--
-- TOC entry 3477 (class 2606 OID 30133)
-- Name: query_params_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY query_params
    ADD CONSTRAINT query_params_pkey PRIMARY KEY ("ID_QUERY", "NAME");


--
-- TOC entry 3480 (class 2606 OID 30135)
-- Name: query_validation_results_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY query_validation_results
    ADD CONSTRAINT query_validation_results_pkey PRIMARY KEY ("ID_QUERY", "ID_QUERY_VALIDATION_RESULT");


--
-- TOC entry 3483 (class 2606 OID 30137)
-- Name: query_validation_settings_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY query_validation_settings
    ADD CONSTRAINT query_validation_settings_pkey PRIMARY KEY ("ID_QUERY", "ID_QUERY_VALIDATION_SETTINGS");


--
-- TOC entry 3485 (class 2606 OID 30139)
-- Name: roles_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY ("ID_ROLE");


--
-- TOC entry 3487 (class 2606 OID 30141)
-- Name: service_methods_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY service_methods
    ADD CONSTRAINT service_methods_pkey PRIMARY KEY ("ID_SERVICE_METHOD");


--
-- TOC entry 3492 (class 2606 OID 30145)
-- Name: service_types_methods_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY service_types_methods
    ADD CONSTRAINT service_types_methods_pkey PRIMARY KEY ("ID_SERVICE_METHOD", "ID_SERVICE_TYPE");


--
-- TOC entry 3489 (class 2606 OID 30143)
-- Name: service_types_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY service_types
    ADD CONSTRAINT service_types_pkey PRIMARY KEY ("ID_SERVICE_TYPE");


--
-- TOC entry 3494 (class 2606 OID 30147)
-- Name: sla_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY sla
    ADD CONSTRAINT sla_pkey PRIMARY KEY ("ID_SLA");


--
-- TOC entry 3496 (class 2606 OID 30149)
-- Name: statuses_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY statuses
    ADD CONSTRAINT statuses_pkey PRIMARY KEY ("ID_STATUS");


--
-- TOC entry 3499 (class 2606 OID 30151)
-- Name: users_pkey; Type: CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY ("LOGIN");


--
-- TOC entry 2955 (class 1259 OID 31237)
-- Name: FK_ACTION_JOB1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_ACTION_JOB1" ON actions USING btree ("ID_JOB");


--
-- TOC entry 2956 (class 1259 OID 31238)
-- Name: FK_ACTION_TYPE1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_ACTION_TYPE1" ON actions USING btree ("ID_ACTION_TYPE");


--
-- TOC entry 2959 (class 1259 OID 31239)
-- Name: FK_ALERTS_JOB1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_ALERTS_JOB1" ON alerts USING btree ("ID_JOB");


--
-- TOC entry 2960 (class 1259 OID 31241)
-- Name: FK_ALERTS_NEW_STATUS1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_ALERTS_NEW_STATUS1" ON alerts USING btree ("ID_NEW_STATUS");


--
-- TOC entry 2961 (class 1259 OID 31240)
-- Name: FK_ALERTS_OLD_STATUS1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_ALERTS_OLD_STATUS1" ON alerts USING btree ("ID_OLD_STATUS");


--
-- TOC entry 2976 (class 1259 OID 31245)
-- Name: FK_JOBS_HTTP_METHOD1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_JOBS_HTTP_METHOD1" ON jobs USING btree ("ID_HTTP_METHOD");


--
-- TOC entry 2977 (class 1259 OID 31244)
-- Name: FK_JOBS_SERVICE_TYPE1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_JOBS_SERVICE_TYPE1" ON jobs USING btree ("ID_SERVICE_TYPE");


--
-- TOC entry 2978 (class 1259 OID 31246)
-- Name: FK_JOBS_STATUS1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_JOBS_STATUS1" ON jobs USING btree ("ID_STATUS");


--
-- TOC entry 2968 (class 1259 OID 31242)
-- Name: FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB1" ON job_agg_hour_log_entries USING btree ("ID_JOB");


--
-- TOC entry 2971 (class 1259 OID 31243)
-- Name: FK_JOB_AGG_LOG_ENTRIES_JOB1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_JOB_AGG_LOG_ENTRIES_JOB1" ON job_agg_log_entries USING btree ("ID_JOB");


--
-- TOC entry 3445 (class 1259 OID 31597)
-- Name: FK_LAST_QUERY_QUERY1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_LAST_QUERY_QUERY1" ON last_query_results USING btree ("ID_QUERY");


--
-- TOC entry 3448 (class 1259 OID 31599)
-- Name: FK_LOG_ENTRIES_QUERY1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_LOG_ENTRIES_QUERY1" ON log_entries USING btree ("ID_QUERY");


--
-- TOC entry 3457 (class 1259 OID 31603)
-- Name: FK_OVERVIEWQUERY_LASTRESULT; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_OVERVIEWQUERY_LASTRESULT" ON overview_queries USING btree ("ID_QUERY");


--
-- TOC entry 3458 (class 1259 OID 31601)
-- Name: FK_OVERVIEW_REQ_PAGE; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_OVERVIEW_REQ_PAGE" ON overview_queries USING btree ("ID_OVERVIEW_PAGE");


--
-- TOC entry 3459 (class 1259 OID 31602)
-- Name: FK_OW_QUERY_PAGE1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_OW_QUERY_PAGE1" ON overview_queries USING btree ("ID_OVERVIEW_PAGE");


--
-- TOC entry 3462 (class 1259 OID 31604)
-- Name: FK_PERIODS_SLA1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_PERIODS_SLA1" ON periods USING btree ("ID_SLA");


--
-- TOC entry 3465 (class 1259 OID 31606)
-- Name: FK_QUERIES_JOB1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_QUERIES_JOB1" ON queries USING btree ("ID_JOB");


--
-- TOC entry 3466 (class 1259 OID 31605)
-- Name: FK_QUERIES_METHOD1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_QUERIES_METHOD1" ON queries USING btree ("ID_SERVICE_METHOD");


--
-- TOC entry 3467 (class 1259 OID 31607)
-- Name: FK_QUERIES_STATUS1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_QUERIES_STATUS1" ON queries USING btree ("ID_STATUS");


--
-- TOC entry 3470 (class 1259 OID 31608)
-- Name: FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY1" ON query_agg_hour_log_entries USING btree ("ID_QUERY");


--
-- TOC entry 3473 (class 1259 OID 31609)
-- Name: FK_QUERY_AGG_LOG_ENTRIES_QUERY1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_QUERY_AGG_LOG_ENTRIES_QUERY1" ON query_agg_log_entries USING btree ("ID_QUERY");


--
-- TOC entry 3490 (class 1259 OID 31612)
-- Name: FK_SERVICE_TYPES_METHODS_METHOD1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_SERVICE_TYPES_METHODS_METHOD1" ON service_types_methods USING btree ("ID_SERVICE_METHOD");


--
-- TOC entry 3497 (class 1259 OID 31613)
-- Name: FK_USERS_ROLE1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "FK_USERS_ROLE1" ON users USING btree ("ID_ROLE");


--
-- TOC entry 3449 (class 1259 OID 31600)
-- Name: IX_LOG_ENTRIES_ID_QUERY_REQUEST_TIME; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "IX_LOG_ENTRIES_ID_QUERY_REQUEST_TIME" ON log_entries USING btree ("ID_QUERY", "REQUEST_TIME");


--
-- TOC entry 3367 (class 1259 OID 31522)
-- Name: alias1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX alias1 ON jos_sdi_sys_unit USING btree (alias);


--
-- TOC entry 3378 (class 1259 OID 31526)
-- Name: element_guid; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX element_guid ON jos_sdi_translation USING btree (element_guid);


--
-- TOC entry 3450 (class 1259 OID 31598)
-- Name: fk_log_entries_statuses_STATUS1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX "fk_log_entries_statuses_STATUS1" ON log_entries USING btree ("ID_STATUS");


--
-- TOC entry 3478 (class 1259 OID 31610)
-- Name: fk_query_validation_results_queries11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX fk_query_validation_results_queries11 ON query_validation_results USING btree ("ID_QUERY");


--
-- TOC entry 3481 (class 1259 OID 31611)
-- Name: fk_query_validation_settings_queries11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX fk_query_validation_settings_queries11 ON query_validation_settings USING btree ("ID_QUERY");


--
-- TOC entry 2983 (class 1259 OID 31362)
-- Name: jos_sdi_accessscope_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_accessscope_fk11 ON jos_sdi_accessscope USING btree (organism_id);


--
-- TOC entry 2984 (class 1259 OID 31363)
-- Name: jos_sdi_accessscope_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_accessscope_fk21 ON jos_sdi_accessscope USING btree (user_id);


--
-- TOC entry 2987 (class 1259 OID 31364)
-- Name: jos_sdi_address_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_address_fk11 ON jos_sdi_address USING btree (addresstype_id);


--
-- TOC entry 2988 (class 1259 OID 31365)
-- Name: jos_sdi_address_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_address_fk31 ON jos_sdi_address USING btree (user_id);


--
-- TOC entry 2989 (class 1259 OID 31366)
-- Name: jos_sdi_address_fk41; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_address_fk41 ON jos_sdi_address USING btree (organism_id);


--
-- TOC entry 2990 (class 1259 OID 31367)
-- Name: jos_sdi_address_fk51; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_address_fk51 ON jos_sdi_address USING btree (country_id);


--
-- TOC entry 2995 (class 1259 OID 31368)
-- Name: jos_sdi_allowedoperationy_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_allowedoperationy_fk11 ON jos_sdi_allowedoperation USING btree (policy_id);


--
-- TOC entry 2996 (class 1259 OID 31369)
-- Name: jos_sdi_allowedoperationy_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_allowedoperationy_fk21 ON jos_sdi_allowedoperation USING btree (serviceoperation_id);


--
-- TOC entry 2997 (class 1259 OID 31370)
-- Name: jos_sdi_application_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_application_fk11 ON jos_sdi_application USING btree (resource_id);


--
-- TOC entry 3000 (class 1259 OID 31371)
-- Name: jos_sdi_assignment_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_assignment_fk11 ON jos_sdi_assignment USING btree (assigned_by);


--
-- TOC entry 3001 (class 1259 OID 31372)
-- Name: jos_sdi_assignment_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_assignment_fk21 ON jos_sdi_assignment USING btree (assigned_to);


--
-- TOC entry 3002 (class 1259 OID 31373)
-- Name: jos_sdi_assignment_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_assignment_fk31 ON jos_sdi_assignment USING btree (version_id);


--
-- TOC entry 3010 (class 1259 OID 31377)
-- Name: jos_sdi_attributevalue1; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_attributevalue1 ON jos_sdi_attributevalue USING btree (attribute_id);


--
-- TOC entry 3013 (class 1259 OID 31378)
-- Name: jos_sdi_boundary_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_boundary_fk11 ON jos_sdi_boundary USING btree (parent_id);


--
-- TOC entry 3014 (class 1259 OID 31379)
-- Name: jos_sdi_boundary_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_boundary_fk21 ON jos_sdi_boundary USING btree (category_id);


--
-- TOC entry 3017 (class 1259 OID 31380)
-- Name: jos_sdi_boundarycategory_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_boundarycategory_fk11 ON jos_sdi_boundarycategory USING btree (parent_id);


--
-- TOC entry 3022 (class 1259 OID 31381)
-- Name: jos_sdi_catalog_resourcetype_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_catalog_resourcetype_fk11 ON jos_sdi_catalog_resourcetype USING btree (catalog_id);


--
-- TOC entry 3023 (class 1259 OID 31382)
-- Name: jos_sdi_catalog_resourcetype_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_catalog_resourcetype_fk21 ON jos_sdi_catalog_resourcetype USING btree (resourcetype_id);


--
-- TOC entry 3026 (class 1259 OID 31383)
-- Name: jos_sdi_catalog_searchcriteria_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_catalog_searchcriteria_fk11 ON jos_sdi_catalog_searchcriteria USING btree (catalog_id);


--
-- TOC entry 3027 (class 1259 OID 31384)
-- Name: jos_sdi_catalog_searchcriteria_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_catalog_searchcriteria_fk21 ON jos_sdi_catalog_searchcriteria USING btree (searchcriteria_id);


--
-- TOC entry 3028 (class 1259 OID 31385)
-- Name: jos_sdi_catalog_searchcriteria_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_catalog_searchcriteria_fk31 ON jos_sdi_catalog_searchcriteria USING btree (searchtab_id);


--
-- TOC entry 3031 (class 1259 OID 31386)
-- Name: jos_sdi_catalog_searchsort_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_catalog_searchsort_fk11 ON jos_sdi_catalog_searchsort USING btree (catalog_id);


--
-- TOC entry 3032 (class 1259 OID 31387)
-- Name: jos_sdi_catalog_searchsort_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_catalog_searchsort_fk21 ON jos_sdi_catalog_searchsort USING btree (language_id);


--
-- TOC entry 3035 (class 1259 OID 31388)
-- Name: jos_sdi_class_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_class_fk11 ON jos_sdi_class USING btree (namespace_id);


--
-- TOC entry 3036 (class 1259 OID 31389)
-- Name: jos_sdi_class_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_class_fk21 ON jos_sdi_class USING btree (stereotype_id);


--
-- TOC entry 3048 (class 1259 OID 31395)
-- Name: jos_sdi_diffusion_download_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_download_fk11 ON jos_sdi_diffusion_download USING btree (diffusion_id);


--
-- TOC entry 3049 (class 1259 OID 31396)
-- Name: jos_sdi_diffusion_download_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_download_fk21 ON jos_sdi_diffusion_download USING btree (user_id);


--
-- TOC entry 3041 (class 1259 OID 31390)
-- Name: jos_sdi_diffusion_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_fk11 ON jos_sdi_diffusion USING btree (accessscope_id);


--
-- TOC entry 3042 (class 1259 OID 31391)
-- Name: jos_sdi_diffusion_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_fk21 ON jos_sdi_diffusion USING btree (productmining_id);


--
-- TOC entry 3043 (class 1259 OID 31392)
-- Name: jos_sdi_diffusion_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_fk31 ON jos_sdi_diffusion USING btree (productstorage_id);


--
-- TOC entry 3044 (class 1259 OID 31393)
-- Name: jos_sdi_diffusion_fk41; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_fk41 ON jos_sdi_diffusion USING btree (perimeter_id);


--
-- TOC entry 3045 (class 1259 OID 31394)
-- Name: jos_sdi_diffusion_fk51; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_fk51 ON jos_sdi_diffusion USING btree (version_id);


--
-- TOC entry 3052 (class 1259 OID 31397)
-- Name: jos_sdi_diffusion_notifieduser_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_notifieduser_fk11 ON jos_sdi_diffusion_notifieduser USING btree (diffusion_id);


--
-- TOC entry 3053 (class 1259 OID 31398)
-- Name: jos_sdi_diffusion_notifieduser_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_notifieduser_fk21 ON jos_sdi_diffusion_notifieduser USING btree (user_id);


--
-- TOC entry 3056 (class 1259 OID 31399)
-- Name: jos_sdi_diffusion_perimeter_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_perimeter_fk11 ON jos_sdi_diffusion_perimeter USING btree (diffusion_id);


--
-- TOC entry 3057 (class 1259 OID 31400)
-- Name: jos_sdi_diffusion_perimeter_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_perimeter_fk21 ON jos_sdi_diffusion_perimeter USING btree (perimeter_id);


--
-- TOC entry 3060 (class 1259 OID 31401)
-- Name: jos_sdi_diffusion_propertyvalue_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_propertyvalue_fk11 ON jos_sdi_diffusion_propertyvalue USING btree (diffusion_id);


--
-- TOC entry 3061 (class 1259 OID 31402)
-- Name: jos_sdi_diffusion_propertyvalue_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_diffusion_propertyvalue_fk21 ON jos_sdi_diffusion_propertyvalue USING btree (propertyvalue_id);


--
-- TOC entry 3064 (class 1259 OID 31403)
-- Name: jos_sdi_excludedattribute_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_excludedattribute_fk11 ON jos_sdi_excludedattribute USING btree (policy_id);


--
-- TOC entry 3067 (class 1259 OID 31404)
-- Name: jos_sdi_featuretype_policy_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_featuretype_policy_fk11 ON jos_sdi_featuretype_policy USING btree (physicalservicepolicy_id);


--
-- TOC entry 3068 (class 1259 OID 31405)
-- Name: jos_sdi_featuretype_policy_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_featuretype_policy_fk21 ON jos_sdi_featuretype_policy USING btree (spatialpolicy_id);


--
-- TOC entry 3071 (class 1259 OID 31406)
-- Name: jos_sdi_importref_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_importref_fk11 ON jos_sdi_importref USING btree (importtype_id);


--
-- TOC entry 3072 (class 1259 OID 31407)
-- Name: jos_sdi_importref_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_importref_fk21 ON jos_sdi_importref USING btree (cswservice_id);


--
-- TOC entry 3073 (class 1259 OID 31408)
-- Name: jos_sdi_importref_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_importref_fk31 ON jos_sdi_importref USING btree (cswversion_id);


--
-- TOC entry 3076 (class 1259 OID 31409)
-- Name: jos_sdi_includedattribute_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_includedattribute_fk11 ON jos_sdi_includedattribute USING btree (featuretypepolicy_id);


--
-- TOC entry 3081 (class 1259 OID 31410)
-- Name: jos_sdi_layer_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_layer_fk11 ON jos_sdi_layer USING btree (physicalservice_id);


--
-- TOC entry 3084 (class 1259 OID 31411)
-- Name: jos_sdi_layer_layergroup_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_layer_layergroup_fk11 ON jos_sdi_layer_layergroup USING btree (layer_id);


--
-- TOC entry 3085 (class 1259 OID 31412)
-- Name: jos_sdi_layer_layergroup_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_layer_layergroup_fk21 ON jos_sdi_layer_layergroup USING btree (group_id);


--
-- TOC entry 3094 (class 1259 OID 31413)
-- Name: jos_sdi_map_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_map_fk21 ON jos_sdi_map USING btree (unit_id);


--
-- TOC entry 3097 (class 1259 OID 31414)
-- Name: jos_sdi_map_layergroup_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_map_layergroup_fk11 ON jos_sdi_map_layergroup USING btree (map_id);


--
-- TOC entry 3098 (class 1259 OID 31415)
-- Name: jos_sdi_map_layergroup_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_map_layergroup_fk21 ON jos_sdi_map_layergroup USING btree (group_id);


--
-- TOC entry 3101 (class 1259 OID 31416)
-- Name: jos_sdi_map_physicalservice_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_map_physicalservice_fk11 ON jos_sdi_map_physicalservice USING btree (map_id);


--
-- TOC entry 3102 (class 1259 OID 31417)
-- Name: jos_sdi_map_physicalservice_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_map_physicalservice_fk21 ON jos_sdi_map_physicalservice USING btree (physicalservice_id);


--
-- TOC entry 3105 (class 1259 OID 31418)
-- Name: jos_sdi_map_tool_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_map_tool_fk11 ON jos_sdi_map_tool USING btree (map_id);


--
-- TOC entry 3106 (class 1259 OID 31419)
-- Name: jos_sdi_map_tool_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_map_tool_fk21 ON jos_sdi_map_tool USING btree (tool_id);


--
-- TOC entry 3109 (class 1259 OID 31420)
-- Name: jos_sdi_map_virtualservice_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_map_virtualservice_fk11 ON jos_sdi_map_virtualservice USING btree (map_id);


--
-- TOC entry 3110 (class 1259 OID 31421)
-- Name: jos_sdi_map_virtualservice_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_map_virtualservice_fk21 ON jos_sdi_map_virtualservice USING btree (virtualservice_id);


--
-- TOC entry 3115 (class 1259 OID 31422)
-- Name: jos_sdi_maplayer_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_maplayer_fk11 ON jos_sdi_maplayer USING btree (accessscope_id);


--
-- TOC entry 3118 (class 1259 OID 31423)
-- Name: jos_sdi_metadata_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_metadata_fk11 ON jos_sdi_metadata USING btree (metadatastate_id);


--
-- TOC entry 3119 (class 1259 OID 31424)
-- Name: jos_sdi_metadata_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_metadata_fk21 ON jos_sdi_metadata USING btree (accessscope_id);


--
-- TOC entry 3120 (class 1259 OID 31425)
-- Name: jos_sdi_metadata_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_metadata_fk31 ON jos_sdi_metadata USING btree (version_id);


--
-- TOC entry 3133 (class 1259 OID 31430)
-- Name: jos_sdi_order_diffusion_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_diffusion_fk11 ON jos_sdi_order_diffusion USING btree (order_id);


--
-- TOC entry 3134 (class 1259 OID 31431)
-- Name: jos_sdi_order_diffusion_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_diffusion_fk21 ON jos_sdi_order_diffusion USING btree (diffusion_id);


--
-- TOC entry 3135 (class 1259 OID 31432)
-- Name: jos_sdi_order_diffusion_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_diffusion_fk31 ON jos_sdi_order_diffusion USING btree (productstate_id);


--
-- TOC entry 3127 (class 1259 OID 31426)
-- Name: jos_sdi_order_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_fk11 ON jos_sdi_order USING btree (ordertype_id);


--
-- TOC entry 3128 (class 1259 OID 31427)
-- Name: jos_sdi_order_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_fk21 ON jos_sdi_order USING btree (orderstate_id);


--
-- TOC entry 3129 (class 1259 OID 31428)
-- Name: jos_sdi_order_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_fk31 ON jos_sdi_order USING btree (user_id);


--
-- TOC entry 3130 (class 1259 OID 31429)
-- Name: jos_sdi_order_fk41; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_fk41 ON jos_sdi_order USING btree (thirdparty_id);


--
-- TOC entry 3138 (class 1259 OID 31433)
-- Name: jos_sdi_order_perimeter_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_perimeter_fk11 ON jos_sdi_order_perimeter USING btree (order_id);


--
-- TOC entry 3139 (class 1259 OID 31434)
-- Name: jos_sdi_order_perimeter_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_perimeter_fk21 ON jos_sdi_order_perimeter USING btree (perimeter_id);


--
-- TOC entry 3142 (class 1259 OID 31435)
-- Name: jos_sdi_order_propertyvalue_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_propertyvalue_fk11 ON jos_sdi_order_propertyvalue USING btree (orderdiffusion_id);


--
-- TOC entry 3143 (class 1259 OID 31436)
-- Name: jos_sdi_order_propertyvalue_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_propertyvalue_fk21 ON jos_sdi_order_propertyvalue USING btree (property_id);


--
-- TOC entry 3144 (class 1259 OID 31437)
-- Name: jos_sdi_order_propertyvalue_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_order_propertyvalue_fk31 ON jos_sdi_order_propertyvalue USING btree (propertyvalue_id);


--
-- TOC entry 3149 (class 1259 OID 31438)
-- Name: jos_sdi_perimeter_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_perimeter_fk11 ON jos_sdi_perimeter USING btree (accessscope_id);


--
-- TOC entry 3150 (class 1259 OID 31439)
-- Name: jos_sdi_perimeter_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_perimeter_fk21 ON jos_sdi_perimeter USING btree (perimetertype_id);


--
-- TOC entry 3153 (class 1259 OID 31440)
-- Name: jos_sdi_physicalservice_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_fk11 ON jos_sdi_physicalservice USING btree (serviceconnector_id);


--
-- TOC entry 3154 (class 1259 OID 31441)
-- Name: jos_sdi_physicalservice_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_fk21 ON jos_sdi_physicalservice USING btree (resourceauthentication_id);


--
-- TOC entry 3155 (class 1259 OID 31442)
-- Name: jos_sdi_physicalservice_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_fk31 ON jos_sdi_physicalservice USING btree (serviceauthentication_id);


--
-- TOC entry 3156 (class 1259 OID 31443)
-- Name: jos_sdi_physicalservice_fk41; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_fk41 ON jos_sdi_physicalservice USING btree (servicescope_id);


--
-- TOC entry 3161 (class 1259 OID 31444)
-- Name: jos_sdi_physicalservice_organism_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_organism_fk11 ON jos_sdi_physicalservice_organism USING btree (organism_id);


--
-- TOC entry 3162 (class 1259 OID 31445)
-- Name: jos_sdi_physicalservice_organism_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_organism_fk21 ON jos_sdi_physicalservice_organism USING btree (physicalservice_id);


--
-- TOC entry 3165 (class 1259 OID 31446)
-- Name: jos_sdi_physicalservice_policy_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_policy_fk11 ON jos_sdi_physicalservice_policy USING btree (policy_id);


--
-- TOC entry 3166 (class 1259 OID 31447)
-- Name: jos_sdi_physicalservice_policy_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_policy_fk21 ON jos_sdi_physicalservice_policy USING btree (physicalservice_id);


--
-- TOC entry 3167 (class 1259 OID 31448)
-- Name: jos_sdi_physicalservice_policy_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_policy_fk31 ON jos_sdi_physicalservice_policy USING btree (csw_spatialpolicy_id);


--
-- TOC entry 3168 (class 1259 OID 31449)
-- Name: jos_sdi_physicalservice_policy_fk41; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_policy_fk41 ON jos_sdi_physicalservice_policy USING btree (wms_spatialpolicy_id);


--
-- TOC entry 3169 (class 1259 OID 31450)
-- Name: jos_sdi_physicalservice_policy_fk51; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_policy_fk51 ON jos_sdi_physicalservice_policy USING btree (wfs_spatialpolicy_id);


--
-- TOC entry 3170 (class 1259 OID 31451)
-- Name: jos_sdi_physicalservice_policy_fk61; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_policy_fk61 ON jos_sdi_physicalservice_policy USING btree (wmts_spatialpolicy_id);


--
-- TOC entry 3173 (class 1259 OID 31452)
-- Name: jos_sdi_physicalservice_servicecompliance_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_servicecompliance_fk11 ON jos_sdi_physicalservice_servicecompliance USING btree (service_id);


--
-- TOC entry 3174 (class 1259 OID 31453)
-- Name: jos_sdi_physicalservice_servicecompliance_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_physicalservice_servicecompliance_fk21 ON jos_sdi_physicalservice_servicecompliance USING btree (servicecompliance_id);


--
-- TOC entry 3177 (class 1259 OID 31454)
-- Name: jos_sdi_policy_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_fk11 ON jos_sdi_policy USING btree (virtualservice_id);


--
-- TOC entry 3178 (class 1259 OID 31455)
-- Name: jos_sdi_policy_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_fk21 ON jos_sdi_policy USING btree (accessscope_id);


--
-- TOC entry 3179 (class 1259 OID 31457)
-- Name: jos_sdi_policy_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_fk31 ON jos_sdi_policy USING btree (csw_spatialpolicy_id);


--
-- TOC entry 3180 (class 1259 OID 31458)
-- Name: jos_sdi_policy_fk41; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_fk41 ON jos_sdi_policy USING btree (wms_spatialpolicy_id);


--
-- TOC entry 3181 (class 1259 OID 31459)
-- Name: jos_sdi_policy_fk51; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_fk51 ON jos_sdi_policy USING btree (wfs_spatialpolicy_id);


--
-- TOC entry 3182 (class 1259 OID 31460)
-- Name: jos_sdi_policy_fk61; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_fk61 ON jos_sdi_policy USING btree (wmts_spatialpolicy_id);


--
-- TOC entry 3183 (class 1259 OID 31456)
-- Name: jos_sdi_policy_fk71; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_fk71 ON jos_sdi_policy USING btree (csw_version_id);


--
-- TOC entry 3186 (class 1259 OID 31461)
-- Name: jos_sdi_policy_metadatastate_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_metadatastate_fk11 ON jos_sdi_policy_metadatastate USING btree (policy_id);


--
-- TOC entry 3187 (class 1259 OID 31462)
-- Name: jos_sdi_policy_metadatastate_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_metadatastate_fk21 ON jos_sdi_policy_metadatastate USING btree (metadatastate_id);


--
-- TOC entry 3190 (class 1259 OID 31463)
-- Name: jos_sdi_policy_organism_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_organism_fk11 ON jos_sdi_policy_organism USING btree (policy_id);


--
-- TOC entry 3191 (class 1259 OID 31464)
-- Name: jos_sdi_policy_organism_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_organism_fk21 ON jos_sdi_policy_organism USING btree (organism_id);


--
-- TOC entry 3194 (class 1259 OID 31465)
-- Name: jos_sdi_policy_resourcetype_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_resourcetype_fk11 ON jos_sdi_policy_resourcetype USING btree (policy_id);


--
-- TOC entry 3195 (class 1259 OID 31466)
-- Name: jos_sdi_policy_resourcetype_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_resourcetype_fk21 ON jos_sdi_policy_resourcetype USING btree (resourcetype_id);


--
-- TOC entry 3198 (class 1259 OID 31467)
-- Name: jos_sdi_policy_user_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_user_fk11 ON jos_sdi_policy_user USING btree (policy_id);


--
-- TOC entry 3199 (class 1259 OID 31468)
-- Name: jos_sdi_policy_user_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_user_fk21 ON jos_sdi_policy_user USING btree (user_id);


--
-- TOC entry 3202 (class 1259 OID 31469)
-- Name: jos_sdi_policy_visibility_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_visibility_fk11 ON jos_sdi_policy_visibility USING btree (policy_id);


--
-- TOC entry 3203 (class 1259 OID 31470)
-- Name: jos_sdi_policy_visibility_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_visibility_fk21 ON jos_sdi_policy_visibility USING btree (user_id);


--
-- TOC entry 3204 (class 1259 OID 31471)
-- Name: jos_sdi_policy_visibility_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_policy_visibility_fk31 ON jos_sdi_policy_visibility USING btree (organism_id);


--
-- TOC entry 3207 (class 1259 OID 31472)
-- Name: jos_sdi_profile_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_profile_fk11 ON jos_sdi_profile USING btree (class_id);


--
-- TOC entry 3210 (class 1259 OID 31473)
-- Name: jos_sdi_property_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_property_fk11 ON jos_sdi_property USING btree (accessscope_id);


--
-- TOC entry 3211 (class 1259 OID 31474)
-- Name: jos_sdi_property_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_property_fk21 ON jos_sdi_property USING btree (propertytype_id);


--
-- TOC entry 3214 (class 1259 OID 31475)
-- Name: jos_sdi_propertyvalue_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_propertyvalue_fk11 ON jos_sdi_propertyvalue USING btree (property_id);


--
-- TOC entry 3229 (class 1259 OID 31486)
-- Name: jos_sdi_relation_catalog_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_catalog_fk11 ON jos_sdi_relation_catalog USING btree (relation_id);


--
-- TOC entry 3230 (class 1259 OID 31487)
-- Name: jos_sdi_relation_catalog_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_catalog_fk21 ON jos_sdi_relation_catalog USING btree (catalog_id);


--
-- TOC entry 3233 (class 1259 OID 31488)
-- Name: jos_sdi_relation_defaultvalue_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_defaultvalue_fk11 ON jos_sdi_relation_defaultvalue USING btree (relation_id);


--
-- TOC entry 3234 (class 1259 OID 31489)
-- Name: jos_sdi_relation_defaultvalue_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_defaultvalue_fk21 ON jos_sdi_relation_defaultvalue USING btree (attributevalue_id);


--
-- TOC entry 3235 (class 1259 OID 31490)
-- Name: jos_sdi_relation_defaultvalue_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_defaultvalue_fk31 ON jos_sdi_relation_defaultvalue USING btree (language_id);


--
-- TOC entry 3217 (class 1259 OID 31485)
-- Name: jos_sdi_relation_fk101; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_fk101 ON jos_sdi_relation USING btree (childresourcetype_id);


--
-- TOC entry 3218 (class 1259 OID 31476)
-- Name: jos_sdi_relation_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_fk11 ON jos_sdi_relation USING btree (parent_id);


--
-- TOC entry 3219 (class 1259 OID 31477)
-- Name: jos_sdi_relation_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_fk21 ON jos_sdi_relation USING btree (classchild_id);


--
-- TOC entry 3220 (class 1259 OID 31478)
-- Name: jos_sdi_relation_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_fk31 ON jos_sdi_relation USING btree (attributechild_id);


--
-- TOC entry 3221 (class 1259 OID 31479)
-- Name: jos_sdi_relation_fk41; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_fk41 ON jos_sdi_relation USING btree (relationtype_id);


--
-- TOC entry 3222 (class 1259 OID 31480)
-- Name: jos_sdi_relation_fk51; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_fk51 ON jos_sdi_relation USING btree (rendertype_id);


--
-- TOC entry 3223 (class 1259 OID 31481)
-- Name: jos_sdi_relation_fk61; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_fk61 ON jos_sdi_relation USING btree (namespace_id);


--
-- TOC entry 3224 (class 1259 OID 31482)
-- Name: jos_sdi_relation_fk71; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_fk71 ON jos_sdi_relation USING btree (classassociation_id);


--
-- TOC entry 3225 (class 1259 OID 31483)
-- Name: jos_sdi_relation_fk81; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_fk81 ON jos_sdi_relation USING btree (relationscope_id);


--
-- TOC entry 3226 (class 1259 OID 31484)
-- Name: jos_sdi_relation_fk91; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_fk91 ON jos_sdi_relation USING btree (editorrelationscope_id);


--
-- TOC entry 3238 (class 1259 OID 31491)
-- Name: jos_sdi_relation_profile_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_profile_fk11 ON jos_sdi_relation_profile USING btree (relation_id);


--
-- TOC entry 3239 (class 1259 OID 31492)
-- Name: jos_sdi_relation_profile_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_relation_profile_fk21 ON jos_sdi_relation_profile USING btree (profile_id);


--
-- TOC entry 3242 (class 1259 OID 31493)
-- Name: jos_sdi_resource_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resource_fk11 ON jos_sdi_resource USING btree (organism_id);


--
-- TOC entry 3243 (class 1259 OID 31494)
-- Name: jos_sdi_resource_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resource_fk21 ON jos_sdi_resource USING btree (resourcetype_id);


--
-- TOC entry 3244 (class 1259 OID 31495)
-- Name: jos_sdi_resource_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resource_fk31 ON jos_sdi_resource USING btree (accessscope_id);


--
-- TOC entry 3247 (class 1259 OID 31496)
-- Name: jos_sdi_resourcetype_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resourcetype_fk11 ON jos_sdi_resourcetype USING btree (profile_id);


--
-- TOC entry 3248 (class 1259 OID 31497)
-- Name: jos_sdi_resourcetype_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resourcetype_fk21 ON jos_sdi_resourcetype USING btree (fragmentnamespace_id);


--
-- TOC entry 3249 (class 1259 OID 31498)
-- Name: jos_sdi_resourcetype_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resourcetype_fk31 ON jos_sdi_resourcetype USING btree (accessscope_id);


--
-- TOC entry 3252 (class 1259 OID 31499)
-- Name: jos_sdi_resourcetypelink_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resourcetypelink_fk11 ON jos_sdi_resourcetypelink USING btree (parent_id);


--
-- TOC entry 3253 (class 1259 OID 31500)
-- Name: jos_sdi_resourcetypelink_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resourcetypelink_fk21 ON jos_sdi_resourcetypelink USING btree (child_id);


--
-- TOC entry 3254 (class 1259 OID 31501)
-- Name: jos_sdi_resourcetypelink_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resourcetypelink_fk31 ON jos_sdi_resourcetypelink USING btree (class_id);


--
-- TOC entry 3255 (class 1259 OID 31502)
-- Name: jos_sdi_resourcetypelink_fk41; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resourcetypelink_fk41 ON jos_sdi_resourcetypelink USING btree (attribute_id);


--
-- TOC entry 3258 (class 1259 OID 31503)
-- Name: jos_sdi_resourcetypelinkinheritance_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_resourcetypelinkinheritance_fk11 ON jos_sdi_resourcetypelinkinheritance USING btree (resourcetypelink_id);


--
-- TOC entry 3261 (class 1259 OID 31504)
-- Name: jos_sdi_searchcriteria_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_searchcriteria_fk11 ON jos_sdi_searchcriteria USING btree (criteriatype_id);


--
-- TOC entry 3262 (class 1259 OID 31505)
-- Name: jos_sdi_searchcriteria_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_searchcriteria_fk21 ON jos_sdi_searchcriteria USING btree (rendertype_id);


--
-- TOC entry 3263 (class 1259 OID 31506)
-- Name: jos_sdi_searchcriteria_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_searchcriteria_fk31 ON jos_sdi_searchcriteria USING btree (relation_id);


--
-- TOC entry 3266 (class 1259 OID 31507)
-- Name: jos_sdi_searchcriteriafilter_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_searchcriteriafilter_fk11 ON jos_sdi_searchcriteriafilter USING btree (searchcriteria_id);


--
-- TOC entry 3267 (class 1259 OID 31508)
-- Name: jos_sdi_searchcriteriafilter_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_searchcriteriafilter_fk21 ON jos_sdi_searchcriteriafilter USING btree (language_id);


--
-- TOC entry 3274 (class 1259 OID 31509)
-- Name: jos_sdi_sys_authenticationconnector_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_authenticationconnector_fk11 ON jos_sdi_sys_authenticationconnector USING btree (authenticationlevel_id);


--
-- TOC entry 3301 (class 1259 OID 31510)
-- Name: jos_sdi_sys_operationcompliance_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_operationcompliance_fk11 ON jos_sdi_sys_operationcompliance USING btree (servicecompliance_id);


--
-- TOC entry 3302 (class 1259 OID 31511)
-- Name: jos_sdi_sys_operationcompliance_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_operationcompliance_fk21 ON jos_sdi_sys_operationcompliance USING btree (serviceoperation_id);


--
-- TOC entry 3329 (class 1259 OID 31512)
-- Name: jos_sdi_sys_rendertype_criteriatype_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_rendertype_criteriatype_fk11 ON jos_sdi_sys_rendertype_criteriatype USING btree (criteriatype_id);


--
-- TOC entry 3330 (class 1259 OID 31513)
-- Name: jos_sdi_sys_rendertype_criteriatype_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_rendertype_criteriatype_fk21 ON jos_sdi_sys_rendertype_criteriatype USING btree (rendertype_id);


--
-- TOC entry 3333 (class 1259 OID 31514)
-- Name: jos_sdi_sys_rendertype_stereotype_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_rendertype_stereotype_fk11 ON jos_sdi_sys_rendertype_stereotype USING btree (stereotype_id);


--
-- TOC entry 3334 (class 1259 OID 31515)
-- Name: jos_sdi_sys_rendertype_stereotype_fk1_fk2; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_rendertype_stereotype_fk1_fk2 ON jos_sdi_sys_rendertype_stereotype USING btree (rendertype_id);


--
-- TOC entry 3341 (class 1259 OID 31516)
-- Name: jos_sdi_sys_servicecompliance_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_servicecompliance_fk11 ON jos_sdi_sys_servicecompliance USING btree (serviceconnector_id);


--
-- TOC entry 3342 (class 1259 OID 31517)
-- Name: jos_sdi_sys_servicecompliance_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_servicecompliance_fk21 ON jos_sdi_sys_servicecompliance USING btree (serviceversion_id);


--
-- TOC entry 3345 (class 1259 OID 31518)
-- Name: jos_sdi_sys_servicecon_authenticationcon_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_servicecon_authenticationcon_fk11 ON jos_sdi_sys_servicecon_authenticationcon USING btree (serviceconnector_id);


--
-- TOC entry 3346 (class 1259 OID 31519)
-- Name: jos_sdi_sys_servicecon_authenticationcon_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_servicecon_authenticationcon_fk21 ON jos_sdi_sys_servicecon_authenticationcon USING btree (authenticationconnector_id);


--
-- TOC entry 3361 (class 1259 OID 31520)
-- Name: jos_sdi_sys_stereotype_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_stereotype_fk11 ON jos_sdi_sys_stereotype USING btree (entity_id);


--
-- TOC entry 3362 (class 1259 OID 31521)
-- Name: jos_sdi_sys_stereotype_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_sys_stereotype_fk21 ON jos_sdi_sys_stereotype USING btree (namespace_id);


--
-- TOC entry 3372 (class 1259 OID 31523)
-- Name: jos_sdi_tilematrix_policy_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_tilematrix_policy_fk11 ON jos_sdi_tilematrix_policy USING btree (tilematrixsetpolicy_id);


--
-- TOC entry 3375 (class 1259 OID 31524)
-- Name: jos_sdi_tilematrixset_policy_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_tilematrixset_policy_fk11 ON jos_sdi_tilematrixset_policy USING btree (wmtslayerpolicy_id);


--
-- TOC entry 3379 (class 1259 OID 31525)
-- Name: jos_sdi_translation_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_translation_fk11 ON jos_sdi_translation USING btree (language_id);


--
-- TOC entry 3382 (class 1259 OID 31527)
-- Name: jos_sdi_user_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_user_fk11 ON jos_sdi_user USING btree (user_id);


--
-- TOC entry 3387 (class 1259 OID 31528)
-- Name: jos_sdi_user_role_resource_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_user_role_resource_fk11 ON jos_sdi_user_role_resource USING btree (user_id);


--
-- TOC entry 3388 (class 1259 OID 31529)
-- Name: jos_sdi_user_role_resource_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_user_role_resource_fk21 ON jos_sdi_user_role_resource USING btree (role_id);


--
-- TOC entry 3389 (class 1259 OID 31530)
-- Name: jos_sdi_user_role_resource_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_user_role_resource_fk31 ON jos_sdi_user_role_resource USING btree (resource_id);


--
-- TOC entry 3392 (class 1259 OID 31531)
-- Name: jos_sdi_version_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_version_fk11 ON jos_sdi_version USING btree (resource_id);


--
-- TOC entry 3395 (class 1259 OID 31532)
-- Name: jos_sdi_versionlink_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_versionlink_fk11 ON jos_sdi_versionlink USING btree (parent_id);


--
-- TOC entry 3396 (class 1259 OID 31533)
-- Name: jos_sdi_versionlink_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_versionlink_fk21 ON jos_sdi_versionlink USING btree (child_id);


--
-- TOC entry 3399 (class 1259 OID 31534)
-- Name: jos_sdi_virtual_physical_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtual_physical_fk11 ON jos_sdi_virtual_physical USING btree (virtualservice_id);


--
-- TOC entry 3400 (class 1259 OID 31535)
-- Name: jos_sdi_virtual_physical_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtual_physical_fk21 ON jos_sdi_virtual_physical USING btree (physicalservice_id);


--
-- TOC entry 3403 (class 1259 OID 31536)
-- Name: jos_sdi_virtualmetadata_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualmetadata_fk11 ON jos_sdi_virtualmetadata USING btree (virtualservice_id);


--
-- TOC entry 3404 (class 1259 OID 31537)
-- Name: jos_sdi_virtualmetadata_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualmetadata_fk21 ON jos_sdi_virtualmetadata USING btree (country_id);


--
-- TOC entry 3407 (class 1259 OID 31538)
-- Name: jos_sdi_virtualservice_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualservice_fk11 ON jos_sdi_virtualservice USING btree (serviceconnector_id);


--
-- TOC entry 3408 (class 1259 OID 31539)
-- Name: jos_sdi_virtualservice_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualservice_fk21 ON jos_sdi_virtualservice USING btree (proxytype_id);


--
-- TOC entry 3409 (class 1259 OID 31540)
-- Name: jos_sdi_virtualservice_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualservice_fk31 ON jos_sdi_virtualservice USING btree (exceptionlevel_id);


--
-- TOC entry 3410 (class 1259 OID 31541)
-- Name: jos_sdi_virtualservice_fk41; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualservice_fk41 ON jos_sdi_virtualservice USING btree (loglevel_id);


--
-- TOC entry 3411 (class 1259 OID 31542)
-- Name: jos_sdi_virtualservice_fk51; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualservice_fk51 ON jos_sdi_virtualservice USING btree (logroll_id);


--
-- TOC entry 3412 (class 1259 OID 31543)
-- Name: jos_sdi_virtualservice_fk61; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualservice_fk61 ON jos_sdi_virtualservice USING btree (servicescope_id);


--
-- TOC entry 3415 (class 1259 OID 31544)
-- Name: jos_sdi_virtualservice_organism_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualservice_organism_fk11 ON jos_sdi_virtualservice_organism USING btree (organism_id);


--
-- TOC entry 3416 (class 1259 OID 31545)
-- Name: jos_sdi_virtualservice_organism_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualservice_organism_fk21 ON jos_sdi_virtualservice_organism USING btree (virtualservice_id);


--
-- TOC entry 3419 (class 1259 OID 31546)
-- Name: jos_sdi_virtualservice_servicecompliance_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualservice_servicecompliance_fk11 ON jos_sdi_virtualservice_servicecompliance USING btree (service_id);


--
-- TOC entry 3420 (class 1259 OID 31547)
-- Name: jos_sdi_virtualservice_servicecompliance_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_virtualservice_servicecompliance_fk21 ON jos_sdi_virtualservice_servicecompliance USING btree (servicecompliance_id);


--
-- TOC entry 3425 (class 1259 OID 31548)
-- Name: jos_sdi_visualization_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_visualization_fk11 ON jos_sdi_visualization USING btree (accessscope_id);


--
-- TOC entry 3432 (class 1259 OID 31549)
-- Name: jos_sdi_wmslayer_policy_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_wmslayer_policy_fk11 ON jos_sdi_wmslayer_policy USING btree (physicalservicepolicy_id);


--
-- TOC entry 3433 (class 1259 OID 31550)
-- Name: jos_sdi_wmslayer_policy_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_wmslayer_policy_fk21 ON jos_sdi_wmslayer_policy USING btree (spatialpolicy_id);


--
-- TOC entry 3436 (class 1259 OID 31551)
-- Name: jos_sdi_wmts_spatialpolicy_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_wmts_spatialpolicy_fk11 ON jos_sdi_wmts_spatialpolicy USING btree (spatialoperator_id);


--
-- TOC entry 3439 (class 1259 OID 31552)
-- Name: jos_sdi_wmtslayer_policy_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_wmtslayer_policy_fk11 ON jos_sdi_wmtslayer_policy USING btree (physicalservicepolicy_id);


--
-- TOC entry 3440 (class 1259 OID 31553)
-- Name: jos_sdi_wmtslayer_policy_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX jos_sdi_wmtslayer_policy_fk21 ON jos_sdi_wmtslayer_policy USING btree (spatialpolicy_id);


--
-- TOC entry 3007 (class 1259 OID 31374)
-- Name: sdi_attribute_fk11; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX sdi_attribute_fk11 ON jos_sdi_attribute USING btree (namespace_id);


--
-- TOC entry 3008 (class 1259 OID 31375)
-- Name: sdi_attribute_fk21; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX sdi_attribute_fk21 ON jos_sdi_attribute USING btree (listnamespace_id);


--
-- TOC entry 3009 (class 1259 OID 31376)
-- Name: sdi_attribute_fk31; Type: INDEX; Schema: joomla; Owner: -
--

CREATE INDEX sdi_attribute_fk31 ON jos_sdi_attribute USING btree (stereotype_id);


--
-- TOC entry 3500 (class 2606 OID 30152)
-- Name: FK_ACTION_JOB; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY actions
    ADD CONSTRAINT "FK_ACTION_JOB" FOREIGN KEY ("ID_JOB") REFERENCES jobs("ID_JOB") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3501 (class 2606 OID 30157)
-- Name: FK_ACTION_TYPE; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY actions
    ADD CONSTRAINT "FK_ACTION_TYPE" FOREIGN KEY ("ID_ACTION_TYPE") REFERENCES action_types("ID_ACTION_TYPE") MATCH FULL;


--
-- TOC entry 3502 (class 2606 OID 30162)
-- Name: FK_ALERTS_JOB; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY alerts
    ADD CONSTRAINT "FK_ALERTS_JOB" FOREIGN KEY ("ID_JOB") REFERENCES jobs("ID_JOB") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3503 (class 2606 OID 30167)
-- Name: FK_ALERTS_NEW_STATUS; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY alerts
    ADD CONSTRAINT "FK_ALERTS_NEW_STATUS" FOREIGN KEY ("ID_NEW_STATUS") REFERENCES statuses("ID_STATUS") MATCH FULL;


--
-- TOC entry 3504 (class 2606 OID 30172)
-- Name: FK_ALERTS_OLD_STATUS; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY alerts
    ADD CONSTRAINT "FK_ALERTS_OLD_STATUS" FOREIGN KEY ("ID_OLD_STATUS") REFERENCES statuses("ID_STATUS") MATCH FULL;


--
-- TOC entry 3507 (class 2606 OID 30187)
-- Name: FK_JOBS_HTTP_METHOD; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jobs
    ADD CONSTRAINT "FK_JOBS_HTTP_METHOD" FOREIGN KEY ("ID_HTTP_METHOD") REFERENCES http_methods("ID_HTTP_METHOD") MATCH FULL;


--
-- TOC entry 3508 (class 2606 OID 30192)
-- Name: FK_JOBS_SERVICE_TYPE; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jobs
    ADD CONSTRAINT "FK_JOBS_SERVICE_TYPE" FOREIGN KEY ("ID_SERVICE_TYPE") REFERENCES service_types("ID_SERVICE_TYPE") MATCH FULL;


--
-- TOC entry 3509 (class 2606 OID 30197)
-- Name: FK_JOBS_STATUS; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jobs
    ADD CONSTRAINT "FK_JOBS_STATUS" FOREIGN KEY ("ID_STATUS") REFERENCES statuses("ID_STATUS") MATCH FULL;


--
-- TOC entry 3505 (class 2606 OID 30177)
-- Name: FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY job_agg_hour_log_entries
    ADD CONSTRAINT "FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB" FOREIGN KEY ("ID_JOB") REFERENCES jobs("ID_JOB") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3506 (class 2606 OID 30182)
-- Name: FK_JOB_AGG_LOG_ENTRIES_JOB; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY job_agg_log_entries
    ADD CONSTRAINT "FK_JOB_AGG_LOG_ENTRIES_JOB" FOREIGN KEY ("ID_JOB") REFERENCES jobs("ID_JOB") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3700 (class 2606 OID 31152)
-- Name: FK_LAST_QUERY_QUERY; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY last_query_results
    ADD CONSTRAINT "FK_LAST_QUERY_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3701 (class 2606 OID 31157)
-- Name: FK_LOG_ENTRIES_QUERY; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY log_entries
    ADD CONSTRAINT "FK_LOG_ENTRIES_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3703 (class 2606 OID 31167)
-- Name: FK_OVERVIEWQUERY_QUERY; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY overview_queries
    ADD CONSTRAINT "FK_OVERVIEWQUERY_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3704 (class 2606 OID 31172)
-- Name: FK_OW_QUERY_PAGE; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY overview_queries
    ADD CONSTRAINT "FK_OW_QUERY_PAGE" FOREIGN KEY ("ID_OVERVIEW_PAGE") REFERENCES overview_page("ID_OVERVIEW_PAGE") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3705 (class 2606 OID 31177)
-- Name: FK_PERIODS_SLA; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY periods
    ADD CONSTRAINT "FK_PERIODS_SLA" FOREIGN KEY ("ID_SLA") REFERENCES sla("ID_SLA") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3706 (class 2606 OID 31182)
-- Name: FK_QUERIES_JOB; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY queries
    ADD CONSTRAINT "FK_QUERIES_JOB" FOREIGN KEY ("ID_JOB") REFERENCES jobs("ID_JOB") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3707 (class 2606 OID 31187)
-- Name: FK_QUERIES_METHOD; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY queries
    ADD CONSTRAINT "FK_QUERIES_METHOD" FOREIGN KEY ("ID_SERVICE_METHOD") REFERENCES service_methods("ID_SERVICE_METHOD") MATCH FULL;


--
-- TOC entry 3708 (class 2606 OID 31192)
-- Name: FK_QUERIES_STATUS; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY queries
    ADD CONSTRAINT "FK_QUERIES_STATUS" FOREIGN KEY ("ID_STATUS") REFERENCES statuses("ID_STATUS") MATCH FULL;


--
-- TOC entry 3709 (class 2606 OID 31197)
-- Name: FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY query_agg_hour_log_entries
    ADD CONSTRAINT "FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3710 (class 2606 OID 31202)
-- Name: FK_QUERY_AGG_LOG_ENTRIES_QUERY; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY query_agg_log_entries
    ADD CONSTRAINT "FK_QUERY_AGG_LOG_ENTRIES_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3711 (class 2606 OID 31207)
-- Name: FK_QUERY_PARAMS_QUERY; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY query_params
    ADD CONSTRAINT "FK_QUERY_PARAMS_QUERY" FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3714 (class 2606 OID 31222)
-- Name: FK_SERVICE_TYPES_METHODS_METHOD; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY service_types_methods
    ADD CONSTRAINT "FK_SERVICE_TYPES_METHODS_METHOD" FOREIGN KEY ("ID_SERVICE_METHOD") REFERENCES service_methods("ID_SERVICE_METHOD") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3715 (class 2606 OID 31227)
-- Name: FK_SERVICE_TYPES_METHODS_TYPE; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY service_types_methods
    ADD CONSTRAINT "FK_SERVICE_TYPES_METHODS_TYPE" FOREIGN KEY ("ID_SERVICE_TYPE") REFERENCES service_types("ID_SERVICE_TYPE") MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3716 (class 2606 OID 31232)
-- Name: FK_USERS_ROLE; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY users
    ADD CONSTRAINT "FK_USERS_ROLE" FOREIGN KEY ("ID_ROLE") REFERENCES roles("ID_ROLE") MATCH FULL ON DELETE SET NULL;


--
-- TOC entry 3702 (class 2606 OID 31162)
-- Name: fk_log_entries_statuses_STATUS; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY log_entries
    ADD CONSTRAINT "fk_log_entries_statuses_STATUS" FOREIGN KEY ("ID_STATUS") REFERENCES statuses("ID_STATUS") MATCH FULL;


--
-- TOC entry 3712 (class 2606 OID 31212)
-- Name: fk_query_validation_results_queries1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY query_validation_results
    ADD CONSTRAINT fk_query_validation_results_queries1 FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL;


--
-- TOC entry 3713 (class 2606 OID 31217)
-- Name: fk_query_validation_settings_queries1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY query_validation_settings
    ADD CONSTRAINT fk_query_validation_settings_queries1 FOREIGN KEY ("ID_QUERY") REFERENCES queries("ID_QUERY") MATCH FULL;


--
-- TOC entry 3510 (class 2606 OID 30202)
-- Name: jos_sdi_accessscope_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_accessscope
    ADD CONSTRAINT jos_sdi_accessscope_fk1 FOREIGN KEY (organism_id) REFERENCES jos_sdi_organism(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3511 (class 2606 OID 30207)
-- Name: jos_sdi_accessscope_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_accessscope
    ADD CONSTRAINT jos_sdi_accessscope_fk2 FOREIGN KEY (user_id) REFERENCES jos_sdi_user(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3512 (class 2606 OID 30212)
-- Name: jos_sdi_address_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_address
    ADD CONSTRAINT jos_sdi_address_fk1 FOREIGN KEY (addresstype_id) REFERENCES jos_sdi_sys_addresstype(id) MATCH FULL;


--
-- TOC entry 3513 (class 2606 OID 30217)
-- Name: jos_sdi_address_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_address
    ADD CONSTRAINT jos_sdi_address_fk3 FOREIGN KEY (user_id) REFERENCES jos_sdi_user(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3514 (class 2606 OID 30222)
-- Name: jos_sdi_address_fk4; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_address
    ADD CONSTRAINT jos_sdi_address_fk4 FOREIGN KEY (organism_id) REFERENCES jos_sdi_organism(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3515 (class 2606 OID 30227)
-- Name: jos_sdi_address_fk5; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_address
    ADD CONSTRAINT jos_sdi_address_fk5 FOREIGN KEY (country_id) REFERENCES jos_sdi_sys_country(id) MATCH FULL;


--
-- TOC entry 3516 (class 2606 OID 30232)
-- Name: jos_sdi_allowedoperationy_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_allowedoperation
    ADD CONSTRAINT jos_sdi_allowedoperationy_fk1 FOREIGN KEY (policy_id) REFERENCES jos_sdi_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3517 (class 2606 OID 30237)
-- Name: jos_sdi_allowedoperationy_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_allowedoperation
    ADD CONSTRAINT jos_sdi_allowedoperationy_fk2 FOREIGN KEY (serviceoperation_id) REFERENCES jos_sdi_sys_serviceoperation(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3518 (class 2606 OID 30242)
-- Name: jos_sdi_application_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_application
    ADD CONSTRAINT jos_sdi_application_fk1 FOREIGN KEY (resource_id) REFERENCES jos_sdi_resource(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3519 (class 2606 OID 30247)
-- Name: jos_sdi_assignment_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_assignment
    ADD CONSTRAINT jos_sdi_assignment_fk1 FOREIGN KEY (assigned_by) REFERENCES jos_sdi_user(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3520 (class 2606 OID 30252)
-- Name: jos_sdi_assignment_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_assignment
    ADD CONSTRAINT jos_sdi_assignment_fk2 FOREIGN KEY (assigned_to) REFERENCES jos_sdi_user(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3521 (class 2606 OID 30257)
-- Name: jos_sdi_assignment_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_assignment
    ADD CONSTRAINT jos_sdi_assignment_fk3 FOREIGN KEY (version_id) REFERENCES jos_sdi_version(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3525 (class 2606 OID 30277)
-- Name: jos_sdi_attributevalue; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_attributevalue
    ADD CONSTRAINT jos_sdi_attributevalue FOREIGN KEY (attribute_id) REFERENCES jos_sdi_attribute(id) MATCH FULL;


--
-- TOC entry 3526 (class 2606 OID 30282)
-- Name: jos_sdi_boundary_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_boundary
    ADD CONSTRAINT jos_sdi_boundary_fk1 FOREIGN KEY (parent_id) REFERENCES jos_sdi_boundary(id) MATCH FULL;


--
-- TOC entry 3527 (class 2606 OID 30287)
-- Name: jos_sdi_boundary_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_boundary
    ADD CONSTRAINT jos_sdi_boundary_fk2 FOREIGN KEY (category_id) REFERENCES jos_sdi_boundarycategory(id) MATCH FULL;


--
-- TOC entry 3528 (class 2606 OID 30292)
-- Name: jos_sdi_boundarycategory_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_boundarycategory
    ADD CONSTRAINT jos_sdi_boundarycategory_fk1 FOREIGN KEY (parent_id) REFERENCES jos_sdi_boundarycategory(id) MATCH FULL;


--
-- TOC entry 3529 (class 2606 OID 30297)
-- Name: jos_sdi_catalog_resourcetype_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog_resourcetype
    ADD CONSTRAINT jos_sdi_catalog_resourcetype_fk1 FOREIGN KEY (catalog_id) REFERENCES jos_sdi_catalog(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3530 (class 2606 OID 30302)
-- Name: jos_sdi_catalog_resourcetype_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog_resourcetype
    ADD CONSTRAINT jos_sdi_catalog_resourcetype_fk2 FOREIGN KEY (resourcetype_id) REFERENCES jos_sdi_resourcetype(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3531 (class 2606 OID 30307)
-- Name: jos_sdi_catalog_searchcriteria_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog_searchcriteria
    ADD CONSTRAINT jos_sdi_catalog_searchcriteria_fk1 FOREIGN KEY (catalog_id) REFERENCES jos_sdi_catalog(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3532 (class 2606 OID 30312)
-- Name: jos_sdi_catalog_searchcriteria_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog_searchcriteria
    ADD CONSTRAINT jos_sdi_catalog_searchcriteria_fk2 FOREIGN KEY (searchcriteria_id) REFERENCES jos_sdi_searchcriteria(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3533 (class 2606 OID 30317)
-- Name: jos_sdi_catalog_searchcriteria_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog_searchcriteria
    ADD CONSTRAINT jos_sdi_catalog_searchcriteria_fk3 FOREIGN KEY (searchtab_id) REFERENCES jos_sdi_sys_searchtab(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3534 (class 2606 OID 30322)
-- Name: jos_sdi_catalog_searchsort_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog_searchsort
    ADD CONSTRAINT jos_sdi_catalog_searchsort_fk1 FOREIGN KEY (catalog_id) REFERENCES jos_sdi_catalog(id) MATCH FULL;


--
-- TOC entry 3535 (class 2606 OID 30327)
-- Name: jos_sdi_catalog_searchsort_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_catalog_searchsort
    ADD CONSTRAINT jos_sdi_catalog_searchsort_fk2 FOREIGN KEY (language_id) REFERENCES jos_sdi_language(id) MATCH FULL;


--
-- TOC entry 3536 (class 2606 OID 30332)
-- Name: jos_sdi_class_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_class
    ADD CONSTRAINT jos_sdi_class_fk1 FOREIGN KEY (namespace_id) REFERENCES jos_sdi_namespace(id) MATCH FULL;


--
-- TOC entry 3537 (class 2606 OID 30337)
-- Name: jos_sdi_class_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_class
    ADD CONSTRAINT jos_sdi_class_fk2 FOREIGN KEY (stereotype_id) REFERENCES jos_sdi_sys_stereotype(id) MATCH FULL;


--
-- TOC entry 3543 (class 2606 OID 30367)
-- Name: jos_sdi_diffusion_download_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_download
    ADD CONSTRAINT jos_sdi_diffusion_download_fk1 FOREIGN KEY (diffusion_id) REFERENCES jos_sdi_diffusion(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3544 (class 2606 OID 30372)
-- Name: jos_sdi_diffusion_download_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_download
    ADD CONSTRAINT jos_sdi_diffusion_download_fk2 FOREIGN KEY (user_id) REFERENCES jos_sdi_user(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3538 (class 2606 OID 30342)
-- Name: jos_sdi_diffusion_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion
    ADD CONSTRAINT jos_sdi_diffusion_fk1 FOREIGN KEY (accessscope_id) REFERENCES jos_sdi_sys_accessscope(id) MATCH FULL;


--
-- TOC entry 3539 (class 2606 OID 30347)
-- Name: jos_sdi_diffusion_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion
    ADD CONSTRAINT jos_sdi_diffusion_fk2 FOREIGN KEY (productmining_id) REFERENCES jos_sdi_sys_productmining(id) MATCH FULL;


--
-- TOC entry 3540 (class 2606 OID 30352)
-- Name: jos_sdi_diffusion_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion
    ADD CONSTRAINT jos_sdi_diffusion_fk3 FOREIGN KEY (productstorage_id) REFERENCES jos_sdi_sys_productstorage(id) MATCH FULL;


--
-- TOC entry 3541 (class 2606 OID 30357)
-- Name: jos_sdi_diffusion_fk4; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion
    ADD CONSTRAINT jos_sdi_diffusion_fk4 FOREIGN KEY (perimeter_id) REFERENCES jos_sdi_perimeter(id) MATCH FULL;


--
-- TOC entry 3542 (class 2606 OID 30362)
-- Name: jos_sdi_diffusion_fk5; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion
    ADD CONSTRAINT jos_sdi_diffusion_fk5 FOREIGN KEY (version_id) REFERENCES jos_sdi_version(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3545 (class 2606 OID 30377)
-- Name: jos_sdi_diffusion_notifieduser_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_notifieduser
    ADD CONSTRAINT jos_sdi_diffusion_notifieduser_fk1 FOREIGN KEY (diffusion_id) REFERENCES jos_sdi_diffusion(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3546 (class 2606 OID 30382)
-- Name: jos_sdi_diffusion_notifieduser_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_notifieduser
    ADD CONSTRAINT jos_sdi_diffusion_notifieduser_fk2 FOREIGN KEY (user_id) REFERENCES jos_sdi_user(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3547 (class 2606 OID 30387)
-- Name: jos_sdi_diffusion_perimeter_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_perimeter
    ADD CONSTRAINT jos_sdi_diffusion_perimeter_fk1 FOREIGN KEY (diffusion_id) REFERENCES jos_sdi_diffusion(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3548 (class 2606 OID 30392)
-- Name: jos_sdi_diffusion_perimeter_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_perimeter
    ADD CONSTRAINT jos_sdi_diffusion_perimeter_fk2 FOREIGN KEY (perimeter_id) REFERENCES jos_sdi_perimeter(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3549 (class 2606 OID 30397)
-- Name: jos_sdi_diffusion_propertyvalue_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_propertyvalue
    ADD CONSTRAINT jos_sdi_diffusion_propertyvalue_fk1 FOREIGN KEY (diffusion_id) REFERENCES jos_sdi_diffusion(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3550 (class 2606 OID 30402)
-- Name: jos_sdi_diffusion_propertyvalue_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_diffusion_propertyvalue
    ADD CONSTRAINT jos_sdi_diffusion_propertyvalue_fk2 FOREIGN KEY (propertyvalue_id) REFERENCES jos_sdi_propertyvalue(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3551 (class 2606 OID 30407)
-- Name: jos_sdi_excludedattribute_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_excludedattribute
    ADD CONSTRAINT jos_sdi_excludedattribute_fk1 FOREIGN KEY (policy_id) REFERENCES jos_sdi_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3552 (class 2606 OID 30412)
-- Name: jos_sdi_featuretype_policy_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_featuretype_policy
    ADD CONSTRAINT jos_sdi_featuretype_policy_fk1 FOREIGN KEY (physicalservicepolicy_id) REFERENCES jos_sdi_physicalservice_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3553 (class 2606 OID 30417)
-- Name: jos_sdi_featuretype_policy_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_featuretype_policy
    ADD CONSTRAINT jos_sdi_featuretype_policy_fk2 FOREIGN KEY (spatialpolicy_id) REFERENCES jos_sdi_wfs_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3554 (class 2606 OID 30422)
-- Name: jos_sdi_importref_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_importref
    ADD CONSTRAINT jos_sdi_importref_fk1 FOREIGN KEY (importtype_id) REFERENCES jos_sdi_sys_importtype(id) MATCH FULL;


--
-- TOC entry 3555 (class 2606 OID 30427)
-- Name: jos_sdi_importref_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_importref
    ADD CONSTRAINT jos_sdi_importref_fk2 FOREIGN KEY (cswservice_id) REFERENCES jos_sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3556 (class 2606 OID 30432)
-- Name: jos_sdi_importref_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_importref
    ADD CONSTRAINT jos_sdi_importref_fk3 FOREIGN KEY (cswversion_id) REFERENCES jos_sdi_sys_serviceversion(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3557 (class 2606 OID 30437)
-- Name: jos_sdi_includedattribute_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_includedattribute
    ADD CONSTRAINT jos_sdi_includedattribute_fk1 FOREIGN KEY (featuretypepolicy_id) REFERENCES jos_sdi_featuretype_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3558 (class 2606 OID 30442)
-- Name: jos_sdi_layer_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_layer
    ADD CONSTRAINT jos_sdi_layer_fk1 FOREIGN KEY (physicalservice_id) REFERENCES jos_sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3559 (class 2606 OID 30447)
-- Name: jos_sdi_layer_layergroup_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_layer_layergroup
    ADD CONSTRAINT jos_sdi_layer_layergroup_fk1 FOREIGN KEY (layer_id) REFERENCES jos_sdi_maplayer(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3560 (class 2606 OID 30452)
-- Name: jos_sdi_layer_layergroup_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_layer_layergroup
    ADD CONSTRAINT jos_sdi_layer_layergroup_fk2 FOREIGN KEY (group_id) REFERENCES jos_sdi_layergroup(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3561 (class 2606 OID 30457)
-- Name: jos_sdi_map_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map
    ADD CONSTRAINT jos_sdi_map_fk2 FOREIGN KEY (unit_id) REFERENCES jos_sdi_sys_unit(id) MATCH FULL;


--
-- TOC entry 3562 (class 2606 OID 30462)
-- Name: jos_sdi_map_layergroup_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_layergroup
    ADD CONSTRAINT jos_sdi_map_layergroup_fk1 FOREIGN KEY (map_id) REFERENCES jos_sdi_map(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3563 (class 2606 OID 30467)
-- Name: jos_sdi_map_layergroup_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_layergroup
    ADD CONSTRAINT jos_sdi_map_layergroup_fk2 FOREIGN KEY (group_id) REFERENCES jos_sdi_layergroup(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3564 (class 2606 OID 30472)
-- Name: jos_sdi_map_physicalservice_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_physicalservice
    ADD CONSTRAINT jos_sdi_map_physicalservice_fk1 FOREIGN KEY (map_id) REFERENCES jos_sdi_map(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3565 (class 2606 OID 30477)
-- Name: jos_sdi_map_physicalservice_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_physicalservice
    ADD CONSTRAINT jos_sdi_map_physicalservice_fk2 FOREIGN KEY (physicalservice_id) REFERENCES jos_sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3566 (class 2606 OID 30482)
-- Name: jos_sdi_map_tool_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_tool
    ADD CONSTRAINT jos_sdi_map_tool_fk1 FOREIGN KEY (map_id) REFERENCES jos_sdi_map(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3567 (class 2606 OID 30487)
-- Name: jos_sdi_map_tool_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_tool
    ADD CONSTRAINT jos_sdi_map_tool_fk2 FOREIGN KEY (tool_id) REFERENCES jos_sdi_sys_maptool(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3568 (class 2606 OID 30492)
-- Name: jos_sdi_map_virtualservice_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_virtualservice
    ADD CONSTRAINT jos_sdi_map_virtualservice_fk1 FOREIGN KEY (map_id) REFERENCES jos_sdi_map(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3569 (class 2606 OID 30497)
-- Name: jos_sdi_map_virtualservice_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_map_virtualservice
    ADD CONSTRAINT jos_sdi_map_virtualservice_fk2 FOREIGN KEY (virtualservice_id) REFERENCES jos_sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3570 (class 2606 OID 30502)
-- Name: jos_sdi_maplayer_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_maplayer
    ADD CONSTRAINT jos_sdi_maplayer_fk1 FOREIGN KEY (accessscope_id) REFERENCES jos_sdi_sys_accessscope(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3571 (class 2606 OID 30507)
-- Name: jos_sdi_metadata_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_metadata
    ADD CONSTRAINT jos_sdi_metadata_fk1 FOREIGN KEY (metadatastate_id) REFERENCES jos_sdi_sys_metadatastate(id) MATCH FULL;


--
-- TOC entry 3572 (class 2606 OID 30512)
-- Name: jos_sdi_metadata_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_metadata
    ADD CONSTRAINT jos_sdi_metadata_fk2 FOREIGN KEY (accessscope_id) REFERENCES jos_sdi_sys_accessscope(id) MATCH FULL;


--
-- TOC entry 3573 (class 2606 OID 30517)
-- Name: jos_sdi_metadata_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_metadata
    ADD CONSTRAINT jos_sdi_metadata_fk3 FOREIGN KEY (version_id) REFERENCES jos_sdi_version(id) MATCH FULL;


--
-- TOC entry 3578 (class 2606 OID 30542)
-- Name: jos_sdi_order_diffusion_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_diffusion
    ADD CONSTRAINT jos_sdi_order_diffusion_fk1 FOREIGN KEY (order_id) REFERENCES jos_sdi_order(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3579 (class 2606 OID 30547)
-- Name: jos_sdi_order_diffusion_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_diffusion
    ADD CONSTRAINT jos_sdi_order_diffusion_fk2 FOREIGN KEY (diffusion_id) REFERENCES jos_sdi_diffusion(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3580 (class 2606 OID 30552)
-- Name: jos_sdi_order_diffusion_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_diffusion
    ADD CONSTRAINT jos_sdi_order_diffusion_fk3 FOREIGN KEY (productstate_id) REFERENCES jos_sdi_sys_productstate(id) MATCH FULL;


--
-- TOC entry 3574 (class 2606 OID 30522)
-- Name: jos_sdi_order_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order
    ADD CONSTRAINT jos_sdi_order_fk1 FOREIGN KEY (ordertype_id) REFERENCES jos_sdi_sys_ordertype(id) MATCH FULL;


--
-- TOC entry 3575 (class 2606 OID 30527)
-- Name: jos_sdi_order_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order
    ADD CONSTRAINT jos_sdi_order_fk2 FOREIGN KEY (orderstate_id) REFERENCES jos_sdi_sys_orderstate(id) MATCH FULL;


--
-- TOC entry 3576 (class 2606 OID 30532)
-- Name: jos_sdi_order_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order
    ADD CONSTRAINT jos_sdi_order_fk3 FOREIGN KEY (user_id) REFERENCES jos_sdi_user(id) MATCH FULL;


--
-- TOC entry 3577 (class 2606 OID 30537)
-- Name: jos_sdi_order_fk4; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order
    ADD CONSTRAINT jos_sdi_order_fk4 FOREIGN KEY (thirdparty_id) REFERENCES jos_sdi_user(id) MATCH FULL;


--
-- TOC entry 3581 (class 2606 OID 30557)
-- Name: jos_sdi_order_perimeter_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_perimeter
    ADD CONSTRAINT jos_sdi_order_perimeter_fk1 FOREIGN KEY (order_id) REFERENCES jos_sdi_order(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3582 (class 2606 OID 30562)
-- Name: jos_sdi_order_perimeter_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_perimeter
    ADD CONSTRAINT jos_sdi_order_perimeter_fk2 FOREIGN KEY (perimeter_id) REFERENCES jos_sdi_perimeter(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3583 (class 2606 OID 30567)
-- Name: jos_sdi_order_propertyvalue_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_propertyvalue
    ADD CONSTRAINT jos_sdi_order_propertyvalue_fk1 FOREIGN KEY (orderdiffusion_id) REFERENCES jos_sdi_order_diffusion(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3584 (class 2606 OID 30572)
-- Name: jos_sdi_order_propertyvalue_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_propertyvalue
    ADD CONSTRAINT jos_sdi_order_propertyvalue_fk2 FOREIGN KEY (property_id) REFERENCES jos_sdi_property(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3585 (class 2606 OID 30577)
-- Name: jos_sdi_order_propertyvalue_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_order_propertyvalue
    ADD CONSTRAINT jos_sdi_order_propertyvalue_fk3 FOREIGN KEY (propertyvalue_id) REFERENCES jos_sdi_propertyvalue(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3586 (class 2606 OID 30582)
-- Name: jos_sdi_perimeter_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_perimeter
    ADD CONSTRAINT jos_sdi_perimeter_fk1 FOREIGN KEY (accessscope_id) REFERENCES jos_sdi_sys_accessscope(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3587 (class 2606 OID 30587)
-- Name: jos_sdi_perimeter_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_perimeter
    ADD CONSTRAINT jos_sdi_perimeter_fk2 FOREIGN KEY (perimetertype_id) REFERENCES jos_sdi_sys_perimetertype(id) MATCH FULL;


--
-- TOC entry 3588 (class 2606 OID 30592)
-- Name: jos_sdi_physicalservice_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice
    ADD CONSTRAINT jos_sdi_physicalservice_fk1 FOREIGN KEY (serviceconnector_id) REFERENCES jos_sdi_sys_serviceconnector(id) MATCH FULL;


--
-- TOC entry 3589 (class 2606 OID 30597)
-- Name: jos_sdi_physicalservice_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice
    ADD CONSTRAINT jos_sdi_physicalservice_fk2 FOREIGN KEY (resourceauthentication_id) REFERENCES jos_sdi_sys_authenticationconnector(id) MATCH FULL;


--
-- TOC entry 3590 (class 2606 OID 30602)
-- Name: jos_sdi_physicalservice_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice
    ADD CONSTRAINT jos_sdi_physicalservice_fk3 FOREIGN KEY (serviceauthentication_id) REFERENCES jos_sdi_sys_authenticationconnector(id) MATCH FULL;


--
-- TOC entry 3591 (class 2606 OID 30607)
-- Name: jos_sdi_physicalservice_fk4; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice
    ADD CONSTRAINT jos_sdi_physicalservice_fk4 FOREIGN KEY (servicescope_id) REFERENCES jos_sdi_sys_servicescope(id) MATCH FULL;


--
-- TOC entry 3592 (class 2606 OID 30612)
-- Name: jos_sdi_physicalservice_organism_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_organism
    ADD CONSTRAINT jos_sdi_physicalservice_organism_fk1 FOREIGN KEY (organism_id) REFERENCES jos_sdi_organism(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3593 (class 2606 OID 30617)
-- Name: jos_sdi_physicalservice_organism_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_organism
    ADD CONSTRAINT jos_sdi_physicalservice_organism_fk2 FOREIGN KEY (physicalservice_id) REFERENCES jos_sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3594 (class 2606 OID 30622)
-- Name: jos_sdi_physicalservice_policy_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_policy
    ADD CONSTRAINT jos_sdi_physicalservice_policy_fk1 FOREIGN KEY (policy_id) REFERENCES jos_sdi_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3595 (class 2606 OID 30627)
-- Name: jos_sdi_physicalservice_policy_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_policy
    ADD CONSTRAINT jos_sdi_physicalservice_policy_fk2 FOREIGN KEY (physicalservice_id) REFERENCES jos_sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3596 (class 2606 OID 30632)
-- Name: jos_sdi_physicalservice_policy_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_policy
    ADD CONSTRAINT jos_sdi_physicalservice_policy_fk3 FOREIGN KEY (csw_spatialpolicy_id) REFERENCES jos_sdi_csw_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3597 (class 2606 OID 30637)
-- Name: jos_sdi_physicalservice_policy_fk4; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_policy
    ADD CONSTRAINT jos_sdi_physicalservice_policy_fk4 FOREIGN KEY (wms_spatialpolicy_id) REFERENCES jos_sdi_wms_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3598 (class 2606 OID 30642)
-- Name: jos_sdi_physicalservice_policy_fk5; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_policy
    ADD CONSTRAINT jos_sdi_physicalservice_policy_fk5 FOREIGN KEY (wfs_spatialpolicy_id) REFERENCES jos_sdi_wfs_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3599 (class 2606 OID 30647)
-- Name: jos_sdi_physicalservice_policy_fk6; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_policy
    ADD CONSTRAINT jos_sdi_physicalservice_policy_fk6 FOREIGN KEY (wmts_spatialpolicy_id) REFERENCES jos_sdi_wmts_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3600 (class 2606 OID 30652)
-- Name: jos_sdi_physicalservice_servicecompliance_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_servicecompliance
    ADD CONSTRAINT jos_sdi_physicalservice_servicecompliance_fk1 FOREIGN KEY (service_id) REFERENCES jos_sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3601 (class 2606 OID 30657)
-- Name: jos_sdi_physicalservice_servicecompliance_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_physicalservice_servicecompliance
    ADD CONSTRAINT jos_sdi_physicalservice_servicecompliance_fk2 FOREIGN KEY (servicecompliance_id) REFERENCES jos_sdi_sys_servicecompliance(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3602 (class 2606 OID 30662)
-- Name: jos_sdi_policy_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy
    ADD CONSTRAINT jos_sdi_policy_fk1 FOREIGN KEY (virtualservice_id) REFERENCES jos_sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3603 (class 2606 OID 30667)
-- Name: jos_sdi_policy_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy
    ADD CONSTRAINT jos_sdi_policy_fk2 FOREIGN KEY (accessscope_id) REFERENCES jos_sdi_sys_accessscope(id) MATCH FULL;


--
-- TOC entry 3605 (class 2606 OID 30677)
-- Name: jos_sdi_policy_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy
    ADD CONSTRAINT jos_sdi_policy_fk3 FOREIGN KEY (csw_spatialpolicy_id) REFERENCES jos_sdi_csw_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3606 (class 2606 OID 30682)
-- Name: jos_sdi_policy_fk4; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy
    ADD CONSTRAINT jos_sdi_policy_fk4 FOREIGN KEY (wms_spatialpolicy_id) REFERENCES jos_sdi_wms_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3607 (class 2606 OID 30687)
-- Name: jos_sdi_policy_fk5; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy
    ADD CONSTRAINT jos_sdi_policy_fk5 FOREIGN KEY (wfs_spatialpolicy_id) REFERENCES jos_sdi_wfs_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3608 (class 2606 OID 30692)
-- Name: jos_sdi_policy_fk6; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy
    ADD CONSTRAINT jos_sdi_policy_fk6 FOREIGN KEY (wmts_spatialpolicy_id) REFERENCES jos_sdi_wmts_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3604 (class 2606 OID 30672)
-- Name: jos_sdi_policy_fk7; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy
    ADD CONSTRAINT jos_sdi_policy_fk7 FOREIGN KEY (csw_version_id) REFERENCES jos_sdi_sys_metadataversion(id) MATCH FULL;


--
-- TOC entry 3609 (class 2606 OID 30697)
-- Name: jos_sdi_policy_metadatastate_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_metadatastate
    ADD CONSTRAINT jos_sdi_policy_metadatastate_fk1 FOREIGN KEY (policy_id) REFERENCES jos_sdi_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3610 (class 2606 OID 30702)
-- Name: jos_sdi_policy_metadatastate_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_metadatastate
    ADD CONSTRAINT jos_sdi_policy_metadatastate_fk2 FOREIGN KEY (metadatastate_id) REFERENCES jos_sdi_sys_metadatastate(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3611 (class 2606 OID 30707)
-- Name: jos_sdi_policy_organism_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_organism
    ADD CONSTRAINT jos_sdi_policy_organism_fk1 FOREIGN KEY (policy_id) REFERENCES jos_sdi_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3612 (class 2606 OID 30712)
-- Name: jos_sdi_policy_organism_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_organism
    ADD CONSTRAINT jos_sdi_policy_organism_fk2 FOREIGN KEY (organism_id) REFERENCES jos_sdi_organism(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3613 (class 2606 OID 30717)
-- Name: jos_sdi_policy_resourcetype_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_resourcetype
    ADD CONSTRAINT jos_sdi_policy_resourcetype_fk1 FOREIGN KEY (policy_id) REFERENCES jos_sdi_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3614 (class 2606 OID 30722)
-- Name: jos_sdi_policy_resourcetype_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_resourcetype
    ADD CONSTRAINT jos_sdi_policy_resourcetype_fk2 FOREIGN KEY (resourcetype_id) REFERENCES jos_sdi_resourcetype(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3615 (class 2606 OID 30727)
-- Name: jos_sdi_policy_user_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_user
    ADD CONSTRAINT jos_sdi_policy_user_fk1 FOREIGN KEY (policy_id) REFERENCES jos_sdi_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3616 (class 2606 OID 30732)
-- Name: jos_sdi_policy_user_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_user
    ADD CONSTRAINT jos_sdi_policy_user_fk2 FOREIGN KEY (user_id) REFERENCES jos_sdi_user(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3617 (class 2606 OID 30737)
-- Name: jos_sdi_policy_visibility_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_visibility
    ADD CONSTRAINT jos_sdi_policy_visibility_fk1 FOREIGN KEY (policy_id) REFERENCES jos_sdi_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3618 (class 2606 OID 30742)
-- Name: jos_sdi_policy_visibility_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_visibility
    ADD CONSTRAINT jos_sdi_policy_visibility_fk2 FOREIGN KEY (user_id) REFERENCES jos_sdi_user(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3619 (class 2606 OID 30747)
-- Name: jos_sdi_policy_visibility_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_policy_visibility
    ADD CONSTRAINT jos_sdi_policy_visibility_fk3 FOREIGN KEY (organism_id) REFERENCES jos_sdi_organism(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3620 (class 2606 OID 30752)
-- Name: jos_sdi_profile_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_profile
    ADD CONSTRAINT jos_sdi_profile_fk1 FOREIGN KEY (class_id) REFERENCES jos_sdi_class(id) MATCH FULL;


--
-- TOC entry 3621 (class 2606 OID 30757)
-- Name: jos_sdi_property_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_property
    ADD CONSTRAINT jos_sdi_property_fk1 FOREIGN KEY (accessscope_id) REFERENCES jos_sdi_sys_accessscope(id) MATCH FULL;


--
-- TOC entry 3622 (class 2606 OID 30762)
-- Name: jos_sdi_property_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_property
    ADD CONSTRAINT jos_sdi_property_fk2 FOREIGN KEY (propertytype_id) REFERENCES jos_sdi_sys_propertytype(id) MATCH FULL;


--
-- TOC entry 3623 (class 2606 OID 30767)
-- Name: jos_sdi_propertyvalue_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_propertyvalue
    ADD CONSTRAINT jos_sdi_propertyvalue_fk1 FOREIGN KEY (property_id) REFERENCES jos_sdi_property(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3634 (class 2606 OID 30822)
-- Name: jos_sdi_relation_catalog_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation_catalog
    ADD CONSTRAINT jos_sdi_relation_catalog_fk1 FOREIGN KEY (relation_id) REFERENCES jos_sdi_relation(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3635 (class 2606 OID 30827)
-- Name: jos_sdi_relation_catalog_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation_catalog
    ADD CONSTRAINT jos_sdi_relation_catalog_fk2 FOREIGN KEY (catalog_id) REFERENCES jos_sdi_catalog(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3636 (class 2606 OID 30832)
-- Name: jos_sdi_relation_defaultvalue_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation_defaultvalue
    ADD CONSTRAINT jos_sdi_relation_defaultvalue_fk1 FOREIGN KEY (relation_id) REFERENCES jos_sdi_relation(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3637 (class 2606 OID 30837)
-- Name: jos_sdi_relation_defaultvalue_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation_defaultvalue
    ADD CONSTRAINT jos_sdi_relation_defaultvalue_fk2 FOREIGN KEY (attributevalue_id) REFERENCES jos_sdi_attributevalue(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3638 (class 2606 OID 30842)
-- Name: jos_sdi_relation_defaultvalue_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation_defaultvalue
    ADD CONSTRAINT jos_sdi_relation_defaultvalue_fk3 FOREIGN KEY (language_id) REFERENCES jos_sdi_language(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3624 (class 2606 OID 30772)
-- Name: jos_sdi_relation_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_fk1 FOREIGN KEY (parent_id) REFERENCES jos_sdi_class(id) MATCH FULL;


--
-- TOC entry 3633 (class 2606 OID 30817)
-- Name: jos_sdi_relation_fk10; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_fk10 FOREIGN KEY (childresourcetype_id) REFERENCES jos_sdi_resourcetype(id) MATCH FULL;


--
-- TOC entry 3625 (class 2606 OID 30777)
-- Name: jos_sdi_relation_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_fk2 FOREIGN KEY (classchild_id) REFERENCES jos_sdi_class(id) MATCH FULL;


--
-- TOC entry 3626 (class 2606 OID 30782)
-- Name: jos_sdi_relation_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_fk3 FOREIGN KEY (attributechild_id) REFERENCES jos_sdi_attribute(id) MATCH FULL;


--
-- TOC entry 3627 (class 2606 OID 30787)
-- Name: jos_sdi_relation_fk4; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_fk4 FOREIGN KEY (relationtype_id) REFERENCES jos_sdi_sys_relationtype(id) MATCH FULL;


--
-- TOC entry 3628 (class 2606 OID 30792)
-- Name: jos_sdi_relation_fk5; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_fk5 FOREIGN KEY (rendertype_id) REFERENCES jos_sdi_sys_rendertype(id) MATCH FULL;


--
-- TOC entry 3629 (class 2606 OID 30797)
-- Name: jos_sdi_relation_fk6; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_fk6 FOREIGN KEY (namespace_id) REFERENCES jos_sdi_namespace(id) MATCH FULL;


--
-- TOC entry 3630 (class 2606 OID 30802)
-- Name: jos_sdi_relation_fk7; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_fk7 FOREIGN KEY (classassociation_id) REFERENCES jos_sdi_class(id) MATCH FULL;


--
-- TOC entry 3631 (class 2606 OID 30807)
-- Name: jos_sdi_relation_fk8; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_fk8 FOREIGN KEY (relationscope_id) REFERENCES jos_sdi_sys_relationscope(id) MATCH FULL;


--
-- TOC entry 3632 (class 2606 OID 30812)
-- Name: jos_sdi_relation_fk9; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation
    ADD CONSTRAINT jos_sdi_relation_fk9 FOREIGN KEY (editorrelationscope_id) REFERENCES jos_sdi_sys_relationscope(id) MATCH FULL;


--
-- TOC entry 3639 (class 2606 OID 30847)
-- Name: jos_sdi_relation_profile_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation_profile
    ADD CONSTRAINT jos_sdi_relation_profile_fk1 FOREIGN KEY (relation_id) REFERENCES jos_sdi_relation(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3640 (class 2606 OID 30852)
-- Name: jos_sdi_relation_profile_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_relation_profile
    ADD CONSTRAINT jos_sdi_relation_profile_fk2 FOREIGN KEY (profile_id) REFERENCES jos_sdi_profile(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3641 (class 2606 OID 30857)
-- Name: jos_sdi_resource_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resource
    ADD CONSTRAINT jos_sdi_resource_fk1 FOREIGN KEY (organism_id) REFERENCES jos_sdi_organism(id) MATCH FULL;


--
-- TOC entry 3642 (class 2606 OID 30862)
-- Name: jos_sdi_resource_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resource
    ADD CONSTRAINT jos_sdi_resource_fk2 FOREIGN KEY (resourcetype_id) REFERENCES jos_sdi_resourcetype(id) MATCH FULL;


--
-- TOC entry 3643 (class 2606 OID 30867)
-- Name: jos_sdi_resource_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resource
    ADD CONSTRAINT jos_sdi_resource_fk3 FOREIGN KEY (accessscope_id) REFERENCES jos_sdi_sys_accessscope(id) MATCH FULL;


--
-- TOC entry 3644 (class 2606 OID 30872)
-- Name: jos_sdi_resourcetype_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetype
    ADD CONSTRAINT jos_sdi_resourcetype_fk1 FOREIGN KEY (profile_id) REFERENCES jos_sdi_profile(id) MATCH FULL;


--
-- TOC entry 3645 (class 2606 OID 30877)
-- Name: jos_sdi_resourcetype_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetype
    ADD CONSTRAINT jos_sdi_resourcetype_fk2 FOREIGN KEY (fragmentnamespace_id) REFERENCES jos_sdi_namespace(id) MATCH FULL;


--
-- TOC entry 3646 (class 2606 OID 30882)
-- Name: jos_sdi_resourcetype_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetype
    ADD CONSTRAINT jos_sdi_resourcetype_fk3 FOREIGN KEY (accessscope_id) REFERENCES jos_sdi_sys_accessscope(id) MATCH FULL;


--
-- TOC entry 3647 (class 2606 OID 30887)
-- Name: jos_sdi_resourcetypelink_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetypelink
    ADD CONSTRAINT jos_sdi_resourcetypelink_fk1 FOREIGN KEY (parent_id) REFERENCES jos_sdi_resourcetype(id) MATCH FULL;


--
-- TOC entry 3648 (class 2606 OID 30892)
-- Name: jos_sdi_resourcetypelink_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetypelink
    ADD CONSTRAINT jos_sdi_resourcetypelink_fk2 FOREIGN KEY (child_id) REFERENCES jos_sdi_resourcetype(id) MATCH FULL;


--
-- TOC entry 3649 (class 2606 OID 30897)
-- Name: jos_sdi_resourcetypelink_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetypelink
    ADD CONSTRAINT jos_sdi_resourcetypelink_fk3 FOREIGN KEY (class_id) REFERENCES jos_sdi_class(id) MATCH FULL;


--
-- TOC entry 3650 (class 2606 OID 30902)
-- Name: jos_sdi_resourcetypelink_fk4; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetypelink
    ADD CONSTRAINT jos_sdi_resourcetypelink_fk4 FOREIGN KEY (attribute_id) REFERENCES jos_sdi_attribute(id) MATCH FULL;


--
-- TOC entry 3651 (class 2606 OID 30907)
-- Name: jos_sdi_resourcetypelinkinheritance_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_resourcetypelinkinheritance
    ADD CONSTRAINT jos_sdi_resourcetypelinkinheritance_fk1 FOREIGN KEY (resourcetypelink_id) REFERENCES jos_sdi_resourcetypelink(id) MATCH FULL;


--
-- TOC entry 3652 (class 2606 OID 30912)
-- Name: jos_sdi_searchcriteria_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_searchcriteria
    ADD CONSTRAINT jos_sdi_searchcriteria_fk1 FOREIGN KEY (criteriatype_id) REFERENCES jos_sdi_sys_criteriatype(id) MATCH FULL;


--
-- TOC entry 3653 (class 2606 OID 30917)
-- Name: jos_sdi_searchcriteria_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_searchcriteria
    ADD CONSTRAINT jos_sdi_searchcriteria_fk2 FOREIGN KEY (rendertype_id) REFERENCES jos_sdi_sys_rendertype(id) MATCH FULL;


--
-- TOC entry 3654 (class 2606 OID 30922)
-- Name: jos_sdi_searchcriteria_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_searchcriteria
    ADD CONSTRAINT jos_sdi_searchcriteria_fk3 FOREIGN KEY (relation_id) REFERENCES jos_sdi_relation(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3655 (class 2606 OID 30927)
-- Name: jos_sdi_searchcriteriafilter_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_searchcriteriafilter
    ADD CONSTRAINT jos_sdi_searchcriteriafilter_fk1 FOREIGN KEY (searchcriteria_id) REFERENCES jos_sdi_searchcriteria(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3656 (class 2606 OID 30932)
-- Name: jos_sdi_searchcriteriafilter_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_searchcriteriafilter
    ADD CONSTRAINT jos_sdi_searchcriteriafilter_fk2 FOREIGN KEY (language_id) REFERENCES jos_sdi_language(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3657 (class 2606 OID 30937)
-- Name: jos_sdi_sys_authenticationconnector_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_authenticationconnector
    ADD CONSTRAINT jos_sdi_sys_authenticationconnector_fk1 FOREIGN KEY (authenticationlevel_id) REFERENCES jos_sdi_sys_authenticationlevel(id) MATCH FULL;


--
-- TOC entry 3658 (class 2606 OID 30942)
-- Name: jos_sdi_sys_operationcompliance_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_operationcompliance
    ADD CONSTRAINT jos_sdi_sys_operationcompliance_fk1 FOREIGN KEY (servicecompliance_id) REFERENCES jos_sdi_sys_servicecompliance(id) MATCH FULL;


--
-- TOC entry 3659 (class 2606 OID 30947)
-- Name: jos_sdi_sys_operationcompliance_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_operationcompliance
    ADD CONSTRAINT jos_sdi_sys_operationcompliance_fk2 FOREIGN KEY (serviceoperation_id) REFERENCES jos_sdi_sys_serviceoperation(id) MATCH FULL;


--
-- TOC entry 3660 (class 2606 OID 30952)
-- Name: jos_sdi_sys_rendertype_criteriatype_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_rendertype_criteriatype
    ADD CONSTRAINT jos_sdi_sys_rendertype_criteriatype_fk1 FOREIGN KEY (criteriatype_id) REFERENCES jos_sdi_sys_criteriatype(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3661 (class 2606 OID 30957)
-- Name: jos_sdi_sys_rendertype_criteriatype_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_rendertype_criteriatype
    ADD CONSTRAINT jos_sdi_sys_rendertype_criteriatype_fk2 FOREIGN KEY (rendertype_id) REFERENCES jos_sdi_sys_rendertype(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3662 (class 2606 OID 30962)
-- Name: jos_sdi_sys_rendertype_stereotype_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_rendertype_stereotype
    ADD CONSTRAINT jos_sdi_sys_rendertype_stereotype_fk1 FOREIGN KEY (stereotype_id) REFERENCES jos_sdi_sys_stereotype(id) MATCH FULL;


--
-- TOC entry 3663 (class 2606 OID 30967)
-- Name: jos_sdi_sys_rendertype_stereotype_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_rendertype_stereotype
    ADD CONSTRAINT jos_sdi_sys_rendertype_stereotype_fk2 FOREIGN KEY (rendertype_id) REFERENCES jos_sdi_sys_rendertype(id) MATCH FULL;


--
-- TOC entry 3664 (class 2606 OID 30972)
-- Name: jos_sdi_sys_servicecompliance_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_servicecompliance
    ADD CONSTRAINT jos_sdi_sys_servicecompliance_fk1 FOREIGN KEY (serviceconnector_id) REFERENCES jos_sdi_sys_serviceconnector(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3665 (class 2606 OID 30977)
-- Name: jos_sdi_sys_servicecompliance_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_servicecompliance
    ADD CONSTRAINT jos_sdi_sys_servicecompliance_fk2 FOREIGN KEY (serviceversion_id) REFERENCES jos_sdi_sys_serviceversion(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3666 (class 2606 OID 30982)
-- Name: jos_sdi_sys_servicecon_authenticationcon_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_servicecon_authenticationcon
    ADD CONSTRAINT jos_sdi_sys_servicecon_authenticationcon_fk1 FOREIGN KEY (serviceconnector_id) REFERENCES jos_sdi_sys_serviceconnector(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3667 (class 2606 OID 30987)
-- Name: jos_sdi_sys_servicecon_authenticationcon_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_servicecon_authenticationcon
    ADD CONSTRAINT jos_sdi_sys_servicecon_authenticationcon_fk2 FOREIGN KEY (authenticationconnector_id) REFERENCES jos_sdi_sys_authenticationconnector(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3668 (class 2606 OID 30992)
-- Name: jos_sdi_sys_stereotype_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_stereotype
    ADD CONSTRAINT jos_sdi_sys_stereotype_fk1 FOREIGN KEY (entity_id) REFERENCES jos_sdi_sys_entity(id) MATCH FULL;


--
-- TOC entry 3669 (class 2606 OID 30997)
-- Name: jos_sdi_sys_stereotype_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_sys_stereotype
    ADD CONSTRAINT jos_sdi_sys_stereotype_fk2 FOREIGN KEY (namespace_id) REFERENCES jos_sdi_namespace(id) MATCH FULL;


--
-- TOC entry 3670 (class 2606 OID 31002)
-- Name: jos_sdi_tilematrix_policy_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_tilematrix_policy
    ADD CONSTRAINT jos_sdi_tilematrix_policy_fk1 FOREIGN KEY (tilematrixsetpolicy_id) REFERENCES jos_sdi_tilematrixset_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3671 (class 2606 OID 31007)
-- Name: jos_sdi_tilematrixset_policy_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_tilematrixset_policy
    ADD CONSTRAINT jos_sdi_tilematrixset_policy_fk1 FOREIGN KEY (wmtslayerpolicy_id) REFERENCES jos_sdi_wmtslayer_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3672 (class 2606 OID 31012)
-- Name: jos_sdi_translation_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_translation
    ADD CONSTRAINT jos_sdi_translation_fk1 FOREIGN KEY (language_id) REFERENCES jos_sdi_language(id) MATCH FULL;


--
-- TOC entry 3673 (class 2606 OID 31017)
-- Name: jos_sdi_user_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_user
    ADD CONSTRAINT jos_sdi_user_fk1 FOREIGN KEY (user_id) REFERENCES jos_users(id) MATCH FULL;


--
-- TOC entry 3674 (class 2606 OID 31022)
-- Name: jos_sdi_user_role_resource_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_user_role_resource
    ADD CONSTRAINT jos_sdi_user_role_resource_fk1 FOREIGN KEY (user_id) REFERENCES jos_sdi_user(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3675 (class 2606 OID 31027)
-- Name: jos_sdi_user_role_resource_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_user_role_resource
    ADD CONSTRAINT jos_sdi_user_role_resource_fk2 FOREIGN KEY (role_id) REFERENCES jos_sdi_sys_role(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3676 (class 2606 OID 31032)
-- Name: jos_sdi_user_role_resource_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_user_role_resource
    ADD CONSTRAINT jos_sdi_user_role_resource_fk3 FOREIGN KEY (resource_id) REFERENCES jos_sdi_resource(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3677 (class 2606 OID 31037)
-- Name: jos_sdi_version_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_version
    ADD CONSTRAINT jos_sdi_version_fk1 FOREIGN KEY (resource_id) REFERENCES jos_sdi_resource(id) MATCH FULL;


--
-- TOC entry 3678 (class 2606 OID 31042)
-- Name: jos_sdi_versionlink_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_versionlink
    ADD CONSTRAINT jos_sdi_versionlink_fk1 FOREIGN KEY (parent_id) REFERENCES jos_sdi_version(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3679 (class 2606 OID 31047)
-- Name: jos_sdi_versionlink_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_versionlink
    ADD CONSTRAINT jos_sdi_versionlink_fk2 FOREIGN KEY (child_id) REFERENCES jos_sdi_version(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3680 (class 2606 OID 31052)
-- Name: jos_sdi_virtual_physical_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtual_physical
    ADD CONSTRAINT jos_sdi_virtual_physical_fk1 FOREIGN KEY (virtualservice_id) REFERENCES jos_sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3681 (class 2606 OID 31057)
-- Name: jos_sdi_virtual_physical_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtual_physical
    ADD CONSTRAINT jos_sdi_virtual_physical_fk2 FOREIGN KEY (physicalservice_id) REFERENCES jos_sdi_physicalservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3682 (class 2606 OID 31062)
-- Name: jos_sdi_virtualmetadata_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualmetadata
    ADD CONSTRAINT jos_sdi_virtualmetadata_fk1 FOREIGN KEY (virtualservice_id) REFERENCES jos_sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3683 (class 2606 OID 31067)
-- Name: jos_sdi_virtualmetadata_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualmetadata
    ADD CONSTRAINT jos_sdi_virtualmetadata_fk2 FOREIGN KEY (country_id) REFERENCES jos_sdi_sys_country(id) MATCH FULL;


--
-- TOC entry 3684 (class 2606 OID 31072)
-- Name: jos_sdi_virtualservice_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice
    ADD CONSTRAINT jos_sdi_virtualservice_fk1 FOREIGN KEY (serviceconnector_id) REFERENCES jos_sdi_sys_serviceconnector(id) MATCH FULL;


--
-- TOC entry 3685 (class 2606 OID 31077)
-- Name: jos_sdi_virtualservice_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice
    ADD CONSTRAINT jos_sdi_virtualservice_fk2 FOREIGN KEY (proxytype_id) REFERENCES jos_sdi_sys_proxytype(id) MATCH FULL;


--
-- TOC entry 3686 (class 2606 OID 31082)
-- Name: jos_sdi_virtualservice_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice
    ADD CONSTRAINT jos_sdi_virtualservice_fk3 FOREIGN KEY (exceptionlevel_id) REFERENCES jos_sdi_sys_exceptionlevel(id) MATCH FULL;


--
-- TOC entry 3687 (class 2606 OID 31087)
-- Name: jos_sdi_virtualservice_fk4; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice
    ADD CONSTRAINT jos_sdi_virtualservice_fk4 FOREIGN KEY (loglevel_id) REFERENCES jos_sdi_sys_loglevel(id) MATCH FULL;


--
-- TOC entry 3688 (class 2606 OID 31092)
-- Name: jos_sdi_virtualservice_fk5; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice
    ADD CONSTRAINT jos_sdi_virtualservice_fk5 FOREIGN KEY (logroll_id) REFERENCES jos_sdi_sys_logroll(id) MATCH FULL;


--
-- TOC entry 3689 (class 2606 OID 31097)
-- Name: jos_sdi_virtualservice_fk6; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice
    ADD CONSTRAINT jos_sdi_virtualservice_fk6 FOREIGN KEY (servicescope_id) REFERENCES jos_sdi_sys_servicescope(id) MATCH FULL;


--
-- TOC entry 3690 (class 2606 OID 31102)
-- Name: jos_sdi_virtualservice_organism_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice_organism
    ADD CONSTRAINT jos_sdi_virtualservice_organism_fk1 FOREIGN KEY (organism_id) REFERENCES jos_sdi_organism(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3691 (class 2606 OID 31107)
-- Name: jos_sdi_virtualservice_organism_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice_organism
    ADD CONSTRAINT jos_sdi_virtualservice_organism_fk2 FOREIGN KEY (virtualservice_id) REFERENCES jos_sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3692 (class 2606 OID 31112)
-- Name: jos_sdi_virtualservice_servicecompliance_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice_servicecompliance
    ADD CONSTRAINT jos_sdi_virtualservice_servicecompliance_fk1 FOREIGN KEY (service_id) REFERENCES jos_sdi_virtualservice(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3693 (class 2606 OID 31117)
-- Name: jos_sdi_virtualservice_servicecompliance_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_virtualservice_servicecompliance
    ADD CONSTRAINT jos_sdi_virtualservice_servicecompliance_fk2 FOREIGN KEY (servicecompliance_id) REFERENCES jos_sdi_sys_servicecompliance(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3694 (class 2606 OID 31122)
-- Name: jos_sdi_visualization_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_visualization
    ADD CONSTRAINT jos_sdi_visualization_fk1 FOREIGN KEY (accessscope_id) REFERENCES jos_sdi_sys_accessscope(id) MATCH FULL;


--
-- TOC entry 3695 (class 2606 OID 31127)
-- Name: jos_sdi_wmslayer_policy_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_wmslayer_policy
    ADD CONSTRAINT jos_sdi_wmslayer_policy_fk1 FOREIGN KEY (physicalservicepolicy_id) REFERENCES jos_sdi_physicalservice_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3696 (class 2606 OID 31132)
-- Name: jos_sdi_wmslayer_policy_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_wmslayer_policy
    ADD CONSTRAINT jos_sdi_wmslayer_policy_fk2 FOREIGN KEY (spatialpolicy_id) REFERENCES jos_sdi_wms_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3697 (class 2606 OID 31137)
-- Name: jos_sdi_wmts_spatialpolicy_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_wmts_spatialpolicy
    ADD CONSTRAINT jos_sdi_wmts_spatialpolicy_fk1 FOREIGN KEY (spatialoperator_id) REFERENCES jos_sdi_sys_spatialoperator(id) MATCH FULL;


--
-- TOC entry 3698 (class 2606 OID 31142)
-- Name: jos_sdi_wmtslayer_policy_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_wmtslayer_policy
    ADD CONSTRAINT jos_sdi_wmtslayer_policy_fk1 FOREIGN KEY (physicalservicepolicy_id) REFERENCES jos_sdi_physicalservice_policy(id) MATCH FULL ON DELETE CASCADE;


--
-- TOC entry 3699 (class 2606 OID 31147)
-- Name: jos_sdi_wmtslayer_policy_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_wmtslayer_policy
    ADD CONSTRAINT jos_sdi_wmtslayer_policy_fk2 FOREIGN KEY (spatialpolicy_id) REFERENCES jos_sdi_wmts_spatialpolicy(id) MATCH FULL;


--
-- TOC entry 3522 (class 2606 OID 30262)
-- Name: sdi_attribute_fk1; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_attribute
    ADD CONSTRAINT sdi_attribute_fk1 FOREIGN KEY (namespace_id) REFERENCES jos_sdi_namespace(id) MATCH FULL;


--
-- TOC entry 3523 (class 2606 OID 30267)
-- Name: sdi_attribute_fk2; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_attribute
    ADD CONSTRAINT sdi_attribute_fk2 FOREIGN KEY (listnamespace_id) REFERENCES jos_sdi_namespace(id) MATCH FULL;


--
-- TOC entry 3524 (class 2606 OID 30272)
-- Name: sdi_attribute_fk3; Type: FK CONSTRAINT; Schema: joomla; Owner: -
--

ALTER TABLE ONLY jos_sdi_attribute
    ADD CONSTRAINT sdi_attribute_fk3 FOREIGN KEY (stereotype_id) REFERENCES jos_sdi_sys_stereotype(id) MATCH FULL;


-- Completed on 2014-04-02 15:46:25

--
-- PostgreSQL database dump complete
--