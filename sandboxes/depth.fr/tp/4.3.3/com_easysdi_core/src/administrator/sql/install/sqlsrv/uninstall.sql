IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[users$FK_USERS_ROLE]') AND parent_object_id = OBJECT_ID(N'[users]'))
ALTER TABLE [users] DROP CONSTRAINT [users$FK_USERS_ROLE];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[service_types_methods$FK_SERVICE_TYPES_METHODS_TYPE]') AND parent_object_id = OBJECT_ID(N'[service_types_methods]'))
ALTER TABLE [service_types_methods] DROP CONSTRAINT [service_types_methods$FK_SERVICE_TYPES_METHODS_TYPE];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[service_types_methods$FK_SERVICE_TYPES_METHODS_METHOD]') AND parent_object_id = OBJECT_ID(N'[service_types_methods]'))
ALTER TABLE [service_types_methods] DROP CONSTRAINT [service_types_methods$FK_SERVICE_TYPES_METHODS_METHOD];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[query_validation_settings$fk_query_validation_settings_queries1]') AND parent_object_id = OBJECT_ID(N'[query_validation_settings]'))
ALTER TABLE [query_validation_settings] DROP CONSTRAINT [query_validation_settings$fk_query_validation_settings_queries1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[query_validation_results$fk_query_validation_results_queries1]') AND parent_object_id = OBJECT_ID(N'[query_validation_results]'))
ALTER TABLE [query_validation_results] DROP CONSTRAINT [query_validation_results$fk_query_validation_results_queries1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[query_params$FK_QUERY_PARAMS_QUERY]') AND parent_object_id = OBJECT_ID(N'[query_params]'))
ALTER TABLE [query_params] DROP CONSTRAINT [query_params$FK_QUERY_PARAMS_QUERY];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[query_agg_log_entries$FK_QUERY_AGG_LOG_ENTRIES_QUERY]') AND parent_object_id = OBJECT_ID(N'[query_agg_log_entries]'))
ALTER TABLE [query_agg_log_entries] DROP CONSTRAINT [query_agg_log_entries$FK_QUERY_AGG_LOG_ENTRIES_QUERY];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[query_agg_hour_log_entries$FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY]') AND parent_object_id = OBJECT_ID(N'[query_agg_hour_log_entries]'))
ALTER TABLE [query_agg_hour_log_entries] DROP CONSTRAINT [query_agg_hour_log_entries$FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[queries$FK_QUERIES_STATUS]') AND parent_object_id = OBJECT_ID(N'[queries]'))
ALTER TABLE [queries] DROP CONSTRAINT [queries$FK_QUERIES_STATUS];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[queries$FK_QUERIES_METHOD]') AND parent_object_id = OBJECT_ID(N'[queries]'))
ALTER TABLE [queries] DROP CONSTRAINT [queries$FK_QUERIES_METHOD];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[queries$FK_QUERIES_JOB]') AND parent_object_id = OBJECT_ID(N'[queries]'))
ALTER TABLE [queries] DROP CONSTRAINT [queries$FK_QUERIES_JOB];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[periods$FK_PERIODS_SLA]') AND parent_object_id = OBJECT_ID(N'[periods]'))
ALTER TABLE [periods] DROP CONSTRAINT [periods$FK_PERIODS_SLA];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[overview_queries$FK_OW_QUERY_PAGE]') AND parent_object_id = OBJECT_ID(N'[overview_queries]'))
ALTER TABLE [overview_queries] DROP CONSTRAINT [overview_queries$FK_OW_QUERY_PAGE];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[overview_queries$FK_OVERVIEWQUERY_QUERY]') AND parent_object_id = OBJECT_ID(N'[overview_queries]'))
ALTER TABLE [overview_queries] DROP CONSTRAINT [overview_queries$FK_OVERVIEWQUERY_QUERY];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[log_entries$fk_log_entries_statuses_STATUS]') AND parent_object_id = OBJECT_ID(N'[log_entries]'))
ALTER TABLE [log_entries] DROP CONSTRAINT [log_entries$fk_log_entries_statuses_STATUS];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[log_entries$FK_LOG_ENTRIES_QUERY]') AND parent_object_id = OBJECT_ID(N'[log_entries]'))
ALTER TABLE [log_entries] DROP CONSTRAINT [log_entries$FK_LOG_ENTRIES_QUERY];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[last_query_results$FK_LAST_QUERY_QUERY]') AND parent_object_id = OBJECT_ID(N'[last_query_results]'))
ALTER TABLE [last_query_results] DROP CONSTRAINT [last_query_results$FK_LAST_QUERY_QUERY];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_visualization$#__sdi_visualization_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_visualization]'))
ALTER TABLE [#__sdi_visualization] DROP CONSTRAINT [#__sdi_visualization$#__sdi_visualization_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_versionlink$#__sdi_versionlink_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_versionlink]'))
ALTER TABLE [#__sdi_versionlink] DROP CONSTRAINT [#__sdi_versionlink$#__sdi_versionlink_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_versionlink$#__sdi_versionlink_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_versionlink]'))
ALTER TABLE [#__sdi_versionlink] DROP CONSTRAINT [#__sdi_versionlink$#__sdi_versionlink_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_version$#__sdi_version_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_version]'))
ALTER TABLE [#__sdi_version] DROP CONSTRAINT [#__sdi_version$#__sdi_version_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_user_role_resource$#__sdi_user_role_resource_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_user_role_resource]'))
ALTER TABLE [#__sdi_user_role_resource] DROP CONSTRAINT [#__sdi_user_role_resource$#__sdi_user_role_resource_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_user_role_resource$#__sdi_user_role_resource_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_user_role_resource]'))
ALTER TABLE [#__sdi_user_role_resource] DROP CONSTRAINT [#__sdi_user_role_resource$#__sdi_user_role_resource_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_user_role_resource$#__sdi_user_role_resource_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_user_role_resource]'))
ALTER TABLE [#__sdi_user_role_resource] DROP CONSTRAINT [#__sdi_user_role_resource$#__sdi_user_role_resource_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_user$#__sdi_user_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_user]'))
ALTER TABLE [#__sdi_user] DROP CONSTRAINT [#__sdi_user$#__sdi_user_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_translation$#__sdi_translation_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_translation]'))
ALTER TABLE [#__sdi_translation] DROP CONSTRAINT [#__sdi_translation$#__sdi_translation_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_sys_stereotype$#__sdi_sys_stereotype_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_sys_stereotype]'))
ALTER TABLE [#__sdi_sys_stereotype] DROP CONSTRAINT [#__sdi_sys_stereotype$#__sdi_sys_stereotype_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_sys_stereotype$#__sdi_sys_stereotype_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_sys_stereotype]'))
ALTER TABLE [#__sdi_sys_stereotype] DROP CONSTRAINT [#__sdi_sys_stereotype$#__sdi_sys_stereotype_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_sys_rendertype_stereotype$#__sdi_sys_rendertype_stereotype_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_sys_rendertype_stereotype]'))
ALTER TABLE [#__sdi_sys_rendertype_stereotype] DROP CONSTRAINT [#__sdi_sys_rendertype_stereotype$#__sdi_sys_rendertype_stereotype_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_sys_rendertype_stereotype$#__sdi_sys_rendertype_stereotype_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_sys_rendertype_stereotype]'))
ALTER TABLE [#__sdi_sys_rendertype_stereotype] DROP CONSTRAINT [#__sdi_sys_rendertype_stereotype$#__sdi_sys_rendertype_stereotype_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_sys_rendertype_criteriatype$#__sdi_sys_rendertype_criteriatype_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_sys_rendertype_criteriatype]'))
ALTER TABLE [#__sdi_sys_rendertype_criteriatype] DROP CONSTRAINT [#__sdi_sys_rendertype_criteriatype$#__sdi_sys_rendertype_criteriatype_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_sys_rendertype_criteriatype$#__sdi_sys_rendertype_criteriatype_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_sys_rendertype_criteriatype]'))
ALTER TABLE [#__sdi_sys_rendertype_criteriatype] DROP CONSTRAINT [#__sdi_sys_rendertype_criteriatype$#__sdi_sys_rendertype_criteriatype_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_searchcriteriafilter$#__sdi_searchcriteriafilter_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_searchcriteriafilter]'))
ALTER TABLE [#__sdi_searchcriteriafilter] DROP CONSTRAINT [#__sdi_searchcriteriafilter$#__sdi_searchcriteriafilter_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_searchcriteriafilter$#__sdi_searchcriteriafilter_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_searchcriteriafilter]'))
ALTER TABLE [#__sdi_searchcriteriafilter] DROP CONSTRAINT [#__sdi_searchcriteriafilter$#__sdi_searchcriteriafilter_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_searchcriteria$#__sdi_searchcriteria_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_searchcriteria]'))
ALTER TABLE [#__sdi_searchcriteria] DROP CONSTRAINT [#__sdi_searchcriteria$#__sdi_searchcriteria_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_searchcriteria$#__sdi_searchcriteria_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_searchcriteria]'))
ALTER TABLE [#__sdi_searchcriteria] DROP CONSTRAINT [#__sdi_searchcriteria$#__sdi_searchcriteria_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_searchcriteria$#__sdi_searchcriteria_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_searchcriteria]'))
ALTER TABLE [#__sdi_searchcriteria] DROP CONSTRAINT [#__sdi_searchcriteria$#__sdi_searchcriteria_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_resourcetypelinkinheritance$#__sdi_resourcetypelinkinheritance_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_resourcetypelinkinheritance]'))
ALTER TABLE [#__sdi_resourcetypelinkinheritance] DROP CONSTRAINT [#__sdi_resourcetypelinkinheritance$#__sdi_resourcetypelinkinheritance_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_resourcetypelink$#__sdi_resourcetypelink_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_resourcetypelink]'))
ALTER TABLE [#__sdi_resourcetypelink] DROP CONSTRAINT [#__sdi_resourcetypelink$#__sdi_resourcetypelink_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_resourcetypelink$#__sdi_resourcetypelink_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_resourcetypelink]'))
ALTER TABLE [#__sdi_resourcetypelink] DROP CONSTRAINT [#__sdi_resourcetypelink$#__sdi_resourcetypelink_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_resourcetype$#__sdi_resourcetype_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_resourcetype]'))
ALTER TABLE [#__sdi_resourcetype] DROP CONSTRAINT [#__sdi_resourcetype$#__sdi_resourcetype_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_resourcetype$#__sdi_resourcetype_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_resourcetype]'))
ALTER TABLE [#__sdi_resourcetype] DROP CONSTRAINT [#__sdi_resourcetype$#__sdi_resourcetype_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_resourcetype$#__sdi_resourcetype_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_resourcetype]'))
ALTER TABLE [#__sdi_resourcetype] DROP CONSTRAINT [#__sdi_resourcetype$#__sdi_resourcetype_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_resource$#__sdi_resource_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_resource]'))
ALTER TABLE [#__sdi_resource] DROP CONSTRAINT [#__sdi_resource$#__sdi_resource_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_resource$#__sdi_resource_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_resource]'))
ALTER TABLE [#__sdi_resource] DROP CONSTRAINT [#__sdi_resource$#__sdi_resource_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_resource$#__sdi_resource_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_resource]'))
ALTER TABLE [#__sdi_resource] DROP CONSTRAINT [#__sdi_resource$#__sdi_resource_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation_profile$#__sdi_relation_profile_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation_profile]'))
ALTER TABLE [#__sdi_relation_profile] DROP CONSTRAINT [#__sdi_relation_profile$#__sdi_relation_profile_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation_profile$#__sdi_relation_profile_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation_profile]'))
ALTER TABLE [#__sdi_relation_profile] DROP CONSTRAINT [#__sdi_relation_profile$#__sdi_relation_profile_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation_defaultvalue]'))
ALTER TABLE [#__sdi_relation_defaultvalue] DROP CONSTRAINT [#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation_defaultvalue]'))
ALTER TABLE [#__sdi_relation_defaultvalue] DROP CONSTRAINT [#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation_defaultvalue]'))
ALTER TABLE [#__sdi_relation_defaultvalue] DROP CONSTRAINT [#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation_catalog$#__sdi_relation_catalog_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation_catalog]'))
ALTER TABLE [#__sdi_relation_catalog] DROP CONSTRAINT [#__sdi_relation_catalog$#__sdi_relation_catalog_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation_catalog$#__sdi_relation_catalog_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation_catalog]'))
ALTER TABLE [#__sdi_relation_catalog] DROP CONSTRAINT [#__sdi_relation_catalog$#__sdi_relation_catalog_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation$#__sdi_relation_fk9]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation]'))
ALTER TABLE [#__sdi_relation] DROP CONSTRAINT [#__sdi_relation$#__sdi_relation_fk9];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation$#__sdi_relation_fk8]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation]'))
ALTER TABLE [#__sdi_relation] DROP CONSTRAINT [#__sdi_relation$#__sdi_relation_fk8];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation$#__sdi_relation_fk7]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation]'))
ALTER TABLE [#__sdi_relation] DROP CONSTRAINT [#__sdi_relation$#__sdi_relation_fk7];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation$#__sdi_relation_fk6]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation]'))
ALTER TABLE [#__sdi_relation] DROP CONSTRAINT [#__sdi_relation$#__sdi_relation_fk6];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation$#__sdi_relation_fk5]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation]'))
ALTER TABLE [#__sdi_relation] DROP CONSTRAINT [#__sdi_relation$#__sdi_relation_fk5];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation$#__sdi_relation_fk4]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation]'))
ALTER TABLE [#__sdi_relation] DROP CONSTRAINT [#__sdi_relation$#__sdi_relation_fk4];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation$#__sdi_relation_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation]'))
ALTER TABLE [#__sdi_relation] DROP CONSTRAINT [#__sdi_relation$#__sdi_relation_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation$#__sdi_relation_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation]'))
ALTER TABLE [#__sdi_relation] DROP CONSTRAINT [#__sdi_relation$#__sdi_relation_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation$#__sdi_relation_fk10]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation]'))
ALTER TABLE [#__sdi_relation] DROP CONSTRAINT [#__sdi_relation$#__sdi_relation_fk10];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_relation$#__sdi_relation_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_relation]'))
ALTER TABLE [#__sdi_relation] DROP CONSTRAINT [#__sdi_relation$#__sdi_relation_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_propertyvalue$#__sdi_propertyvalue_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_propertyvalue]'))
ALTER TABLE [#__sdi_propertyvalue] DROP CONSTRAINT [#__sdi_propertyvalue$#__sdi_propertyvalue_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_property$#__sdi_property_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_property]'))
ALTER TABLE [#__sdi_property] DROP CONSTRAINT [#__sdi_property$#__sdi_property_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_property$#__sdi_property_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_property]'))
ALTER TABLE [#__sdi_property] DROP CONSTRAINT [#__sdi_property$#__sdi_property_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_profile$#__sdi_profile_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_profile]'))
ALTER TABLE [#__sdi_profile] DROP CONSTRAINT [#__sdi_profile$#__sdi_profile_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_perimeter$#__sdi_perimeter_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_perimeter]'))
ALTER TABLE [#__sdi_perimeter] DROP CONSTRAINT [#__sdi_perimeter$#__sdi_perimeter_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_perimeter$#__sdi_perimeter_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_perimeter]'))
ALTER TABLE [#__sdi_perimeter] DROP CONSTRAINT [#__sdi_perimeter$#__sdi_perimeter_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order_propertyvalue]'))
ALTER TABLE [#__sdi_order_propertyvalue] DROP CONSTRAINT [#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order_propertyvalue]'))
ALTER TABLE [#__sdi_order_propertyvalue] DROP CONSTRAINT [#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order_propertyvalue]'))
ALTER TABLE [#__sdi_order_propertyvalue] DROP CONSTRAINT [#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order_perimeter$#__sdi_order_perimeter_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order_perimeter]'))
ALTER TABLE [#__sdi_order_perimeter] DROP CONSTRAINT [#__sdi_order_perimeter$#__sdi_order_perimeter_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order_perimeter$#__sdi_order_perimeter_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order_perimeter]'))
ALTER TABLE [#__sdi_order_perimeter] DROP CONSTRAINT [#__sdi_order_perimeter$#__sdi_order_perimeter_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order_diffusion$#__sdi_order_diffusion_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order_diffusion]'))
ALTER TABLE [#__sdi_order_diffusion] DROP CONSTRAINT [#__sdi_order_diffusion$#__sdi_order_diffusion_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order_diffusion$#__sdi_order_diffusion_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order_diffusion]'))
ALTER TABLE [#__sdi_order_diffusion] DROP CONSTRAINT [#__sdi_order_diffusion$#__sdi_order_diffusion_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order_diffusion$#__sdi_order_diffusion_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order_diffusion]'))
ALTER TABLE [#__sdi_order_diffusion] DROP CONSTRAINT [#__sdi_order_diffusion$#__sdi_order_diffusion_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order$#__sdi_order_fk4]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order]'))
ALTER TABLE [#__sdi_order] DROP CONSTRAINT [#__sdi_order$#__sdi_order_fk4];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order$#__sdi_order_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order]'))
ALTER TABLE [#__sdi_order] DROP CONSTRAINT [#__sdi_order$#__sdi_order_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order$#__sdi_order_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order]'))
ALTER TABLE [#__sdi_order] DROP CONSTRAINT [#__sdi_order$#__sdi_order_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_order$#__sdi_order_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_order]'))
ALTER TABLE [#__sdi_order] DROP CONSTRAINT [#__sdi_order$#__sdi_order_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_metadata$#__sdi_metadata_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_metadata]'))
ALTER TABLE [#__sdi_metadata] DROP CONSTRAINT [#__sdi_metadata$#__sdi_metadata_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_metadata$#__sdi_metadata_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_metadata]'))
ALTER TABLE [#__sdi_metadata] DROP CONSTRAINT [#__sdi_metadata$#__sdi_metadata_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_metadata$#__sdi_metadata_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_metadata]'))
ALTER TABLE [#__sdi_metadata] DROP CONSTRAINT [#__sdi_metadata$#__sdi_metadata_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_maplayer$#__sdi_maplayer_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_maplayer]'))
ALTER TABLE [#__sdi_maplayer] DROP CONSTRAINT [#__sdi_maplayer$#__sdi_maplayer_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_map_virtualservice$#__sdi_map_virtualservice_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_map_virtualservice]'))
ALTER TABLE [#__sdi_map_virtualservice] DROP CONSTRAINT [#__sdi_map_virtualservice$#__sdi_map_virtualservice_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_map_virtualservice$#__sdi_map_virtualservice_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_map_virtualservice]'))
ALTER TABLE [#__sdi_map_virtualservice] DROP CONSTRAINT [#__sdi_map_virtualservice$#__sdi_map_virtualservice_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_map_tool$#__sdi_map_tool_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_map_tool]'))
ALTER TABLE [#__sdi_map_tool] DROP CONSTRAINT [#__sdi_map_tool$#__sdi_map_tool_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_map_tool$#__sdi_map_tool_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_map_tool]'))
ALTER TABLE [#__sdi_map_tool] DROP CONSTRAINT [#__sdi_map_tool$#__sdi_map_tool_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_map_physicalservice$#__sdi_map_physicalservice_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_map_physicalservice]'))
ALTER TABLE [#__sdi_map_physicalservice] DROP CONSTRAINT [#__sdi_map_physicalservice$#__sdi_map_physicalservice_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_map_physicalservice$#__sdi_map_physicalservice_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_map_physicalservice]'))
ALTER TABLE [#__sdi_map_physicalservice] DROP CONSTRAINT [#__sdi_map_physicalservice$#__sdi_map_physicalservice_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_map_layergroup$#__sdi_map_layergroup_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_map_layergroup]'))
ALTER TABLE [#__sdi_map_layergroup] DROP CONSTRAINT [#__sdi_map_layergroup$#__sdi_map_layergroup_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_map_layergroup$#__sdi_map_layergroup_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_map_layergroup]'))
ALTER TABLE [#__sdi_map_layergroup] DROP CONSTRAINT [#__sdi_map_layergroup$#__sdi_map_layergroup_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_map$#__sdi_map_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_map]'))
ALTER TABLE [#__sdi_map] DROP CONSTRAINT [#__sdi_map$#__sdi_map_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_layer_layergroup$#__sdi_layer_layergroup_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_layer_layergroup]'))
ALTER TABLE [#__sdi_layer_layergroup] DROP CONSTRAINT [#__sdi_layer_layergroup$#__sdi_layer_layergroup_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_layer_layergroup$#__sdi_layer_layergroup_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_layer_layergroup]'))
ALTER TABLE [#__sdi_layer_layergroup] DROP CONSTRAINT [#__sdi_layer_layergroup$#__sdi_layer_layergroup_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_importref$#__sdi_importref_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_importref]'))
ALTER TABLE [#__sdi_importref] DROP CONSTRAINT [#__sdi_importref$#__sdi_importref_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_importref$#__sdi_importref_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_importref]'))
ALTER TABLE [#__sdi_importref] DROP CONSTRAINT [#__sdi_importref$#__sdi_importref_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_importref$#__sdi_importref_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_importref]'))
ALTER TABLE [#__sdi_importref] DROP CONSTRAINT [#__sdi_importref$#__sdi_importref_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_propertyvalue$#__sdi_diffusion_propertyvalue_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion_propertyvalue]'))
ALTER TABLE [#__sdi_diffusion_propertyvalue] DROP CONSTRAINT [#__sdi_diffusion_propertyvalue$#__sdi_diffusion_propertyvalue_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_propertyvalue$#__sdi_diffusion_propertyvalue_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion_propertyvalue]'))
ALTER TABLE [#__sdi_diffusion_propertyvalue] DROP CONSTRAINT [#__sdi_diffusion_propertyvalue$#__sdi_diffusion_propertyvalue_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_perimeter$#__sdi_diffusion_perimeter_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion_perimeter]'))
ALTER TABLE [#__sdi_diffusion_perimeter] DROP CONSTRAINT [#__sdi_diffusion_perimeter$#__sdi_diffusion_perimeter_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_perimeter$#__sdi_diffusion_perimeter_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion_perimeter]'))
ALTER TABLE [#__sdi_diffusion_perimeter] DROP CONSTRAINT [#__sdi_diffusion_perimeter$#__sdi_diffusion_perimeter_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_notifieduser$#__sdi_diffusion_notifieduser_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion_notifieduser]'))
ALTER TABLE [#__sdi_diffusion_notifieduser] DROP CONSTRAINT [#__sdi_diffusion_notifieduser$#__sdi_diffusion_notifieduser_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_notifieduser$#__sdi_diffusion_notifieduser_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion_notifieduser]'))
ALTER TABLE [#__sdi_diffusion_notifieduser] DROP CONSTRAINT [#__sdi_diffusion_notifieduser$#__sdi_diffusion_notifieduser_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_download$#__sdi_diffusion_download_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion_download]'))
ALTER TABLE [#__sdi_diffusion_download] DROP CONSTRAINT [#__sdi_diffusion_download$#__sdi_diffusion_download_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_download$#__sdi_diffusion_download_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion_download]'))
ALTER TABLE [#__sdi_diffusion_download] DROP CONSTRAINT [#__sdi_diffusion_download$#__sdi_diffusion_download_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion$#__sdi_diffusion_fk5]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion]'))
ALTER TABLE [#__sdi_diffusion] DROP CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk5];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion$#__sdi_diffusion_fk4]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion]'))
ALTER TABLE [#__sdi_diffusion] DROP CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk4];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion$#__sdi_diffusion_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion]'))
ALTER TABLE [#__sdi_diffusion] DROP CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion$#__sdi_diffusion_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion]'))
ALTER TABLE [#__sdi_diffusion] DROP CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion$#__sdi_diffusion_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_diffusion]'))
ALTER TABLE [#__sdi_diffusion] DROP CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_class$#__sdi_class_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_class]'))
ALTER TABLE [#__sdi_class] DROP CONSTRAINT [#__sdi_class$#__sdi_class_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_class$#__sdi_class_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_class]'))
ALTER TABLE [#__sdi_class] DROP CONSTRAINT [#__sdi_class$#__sdi_class_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_catalog_searchsort$#__sdi_catalog_searchsort_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_catalog_searchsort]'))
ALTER TABLE [#__sdi_catalog_searchsort] DROP CONSTRAINT [#__sdi_catalog_searchsort$#__sdi_catalog_searchsort_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_catalog_searchsort$#__sdi_catalog_searchsort_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_catalog_searchsort]'))
ALTER TABLE [#__sdi_catalog_searchsort] DROP CONSTRAINT [#__sdi_catalog_searchsort$#__sdi_catalog_searchsort_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_catalog_searchcriteria]'))
ALTER TABLE [#__sdi_catalog_searchcriteria] DROP CONSTRAINT [#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_catalog_searchcriteria]'))
ALTER TABLE [#__sdi_catalog_searchcriteria] DROP CONSTRAINT [#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_catalog_searchcriteria]'))
ALTER TABLE [#__sdi_catalog_searchcriteria] DROP CONSTRAINT [#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_catalog_resourcetype$#__sdi_catalog_resourcetype_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_catalog_resourcetype]'))
ALTER TABLE [#__sdi_catalog_resourcetype] DROP CONSTRAINT [#__sdi_catalog_resourcetype$#__sdi_catalog_resourcetype_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_catalog_resourcetype$#__sdi_catalog_resourcetype_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_catalog_resourcetype]'))
ALTER TABLE [#__sdi_catalog_resourcetype] DROP CONSTRAINT [#__sdi_catalog_resourcetype$#__sdi_catalog_resourcetype_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_boundarycategory$#__sdi_boundarycategory_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_boundarycategory]'))
ALTER TABLE [#__sdi_boundarycategory] DROP CONSTRAINT [#__sdi_boundarycategory$#__sdi_boundarycategory_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_boundary$#__sdi_boundary_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_boundary]'))
ALTER TABLE [#__sdi_boundary] DROP CONSTRAINT [#__sdi_boundary$#__sdi_boundary_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_boundary$#__sdi_boundary_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_boundary]'))
ALTER TABLE [#__sdi_boundary] DROP CONSTRAINT [#__sdi_boundary$#__sdi_boundary_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_attributevalue$#__sdi_attributevalue]') AND parent_object_id = OBJECT_ID(N'[#__sdi_attributevalue]'))
ALTER TABLE [#__sdi_attributevalue] DROP CONSTRAINT [#__sdi_attributevalue$#__sdi_attributevalue];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_attribute$sdi_attribute_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_attribute]'))
ALTER TABLE [#__sdi_attribute] DROP CONSTRAINT [#__sdi_attribute$sdi_attribute_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_attribute$sdi_attribute_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_attribute]'))
ALTER TABLE [#__sdi_attribute] DROP CONSTRAINT [#__sdi_attribute$sdi_attribute_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_attribute$sdi_attribute_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_attribute]'))
ALTER TABLE [#__sdi_attribute] DROP CONSTRAINT [#__sdi_attribute$sdi_attribute_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_assignment$#__sdi_assignment_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_assignment]'))
ALTER TABLE [#__sdi_assignment] DROP CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_assignment$#__sdi_assignment_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_assignment]'))
ALTER TABLE [#__sdi_assignment] DROP CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_assignment$#__sdi_assignment_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_assignment]'))
ALTER TABLE [#__sdi_assignment] DROP CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_application$#__sdi_application_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_application]'))
ALTER TABLE [#__sdi_application] DROP CONSTRAINT [#__sdi_application$#__sdi_application_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_address$#__sdi_address_fk5]') AND parent_object_id = OBJECT_ID(N'[#__sdi_address]'))
ALTER TABLE [#__sdi_address] DROP CONSTRAINT [#__sdi_address$#__sdi_address_fk5];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_address$#__sdi_address_fk4]') AND parent_object_id = OBJECT_ID(N'[#__sdi_address]'))
ALTER TABLE [#__sdi_address] DROP CONSTRAINT [#__sdi_address$#__sdi_address_fk4];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_address$#__sdi_address_fk3]') AND parent_object_id = OBJECT_ID(N'[#__sdi_address]'))
ALTER TABLE [#__sdi_address] DROP CONSTRAINT [#__sdi_address$#__sdi_address_fk3];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_address$#__sdi_address_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_address]'))
ALTER TABLE [#__sdi_address] DROP CONSTRAINT [#__sdi_address$#__sdi_address_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_accessscope$#__sdi_accessscope_fk2]') AND parent_object_id = OBJECT_ID(N'[#__sdi_accessscope]'))
ALTER TABLE [#__sdi_accessscope] DROP CONSTRAINT [#__sdi_accessscope$#__sdi_accessscope_fk2];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[#__sdi_accessscope$#__sdi_accessscope_fk1]') AND parent_object_id = OBJECT_ID(N'[#__sdi_accessscope]'))
ALTER TABLE [#__sdi_accessscope] DROP CONSTRAINT [#__sdi_accessscope$#__sdi_accessscope_fk1];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[jobs$FK_JOBS_STATUS]') AND parent_object_id = OBJECT_ID(N'[jobs]'))
ALTER TABLE [jobs] DROP CONSTRAINT [jobs$FK_JOBS_STATUS];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[jobs$FK_JOBS_SERVICE_TYPE]') AND parent_object_id = OBJECT_ID(N'[jobs]'))
ALTER TABLE [jobs] DROP CONSTRAINT [jobs$FK_JOBS_SERVICE_TYPE];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[jobs$FK_JOBS_HTTP_METHOD]') AND parent_object_id = OBJECT_ID(N'[jobs]'))
ALTER TABLE [jobs] DROP CONSTRAINT [jobs$FK_JOBS_HTTP_METHOD];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[job_agg_log_entries$FK_JOB_AGG_LOG_ENTRIES_JOB]') AND parent_object_id = OBJECT_ID(N'[job_agg_log_entries]'))
ALTER TABLE [job_agg_log_entries] DROP CONSTRAINT [job_agg_log_entries$FK_JOB_AGG_LOG_ENTRIES_JOB];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[job_agg_hour_log_entries$FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB]') AND parent_object_id = OBJECT_ID(N'[job_agg_hour_log_entries]'))
ALTER TABLE [job_agg_hour_log_entries] DROP CONSTRAINT [job_agg_hour_log_entries$FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[alerts$FK_ALERTS_OLD_STATUS]') AND parent_object_id = OBJECT_ID(N'[alerts]'))
ALTER TABLE [alerts] DROP CONSTRAINT [alerts$FK_ALERTS_OLD_STATUS];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[alerts$FK_ALERTS_NEW_STATUS]') AND parent_object_id = OBJECT_ID(N'[alerts]'))
ALTER TABLE [alerts] DROP CONSTRAINT [alerts$FK_ALERTS_NEW_STATUS];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[alerts$FK_ALERTS_JOB]') AND parent_object_id = OBJECT_ID(N'[alerts]'))
ALTER TABLE [alerts] DROP CONSTRAINT [alerts$FK_ALERTS_JOB];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[actions$FK_ACTION_TYPE]') AND parent_object_id = OBJECT_ID(N'[actions]'))
ALTER TABLE [actions] DROP CONSTRAINT [actions$FK_ACTION_TYPE];
IF  EXISTS (SELECT * FROM sys.foreign_keys WHERE object_id = OBJECT_ID(N'[actions$FK_ACTION_JOB]') AND parent_object_id = OBJECT_ID(N'[actions]'))
ALTER TABLE [actions] DROP CONSTRAINT [actions$FK_ACTION_JOB];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[users]') AND type in (N'U'))
DROP TABLE [users];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[statuses]') AND type in (N'U'))
DROP TABLE [statuses];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[sla]') AND type in (N'U'))
DROP TABLE [sla];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[service_types_methods]') AND type in (N'U'))
DROP TABLE [service_types_methods];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[service_types]') AND type in (N'U'))
DROP TABLE [service_types];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[service_methods]') AND type in (N'U'))
DROP TABLE [service_methods];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[roles]') AND type in (N'U'))
DROP TABLE [roles];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[query_validation_settings]') AND type in (N'U'))
DROP TABLE [query_validation_settings];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[query_validation_results]') AND type in (N'U'))
DROP TABLE [query_validation_results];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[query_params]') AND type in (N'U'))
DROP TABLE [query_params];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[query_agg_log_entries]') AND type in (N'U'))
DROP TABLE [query_agg_log_entries];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[query_agg_hour_log_entries]') AND type in (N'U'))
DROP TABLE [query_agg_hour_log_entries];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[queries]') AND type in (N'U'))
DROP TABLE [queries];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[periods]') AND type in (N'U'))
DROP TABLE [periods];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[overview_queries]') AND type in (N'U'))
DROP TABLE [overview_queries];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[overview_page]') AND type in (N'U'))
DROP TABLE [overview_page];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[log_entries]') AND type in (N'U'))
DROP TABLE [log_entries];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[last_query_results]') AND type in (N'U'))
DROP TABLE [last_query_results];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[last_ids]') AND type in (N'U'))
DROP TABLE [last_ids];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_wmtslayer_policy]') AND type in (N'U'))
DROP TABLE [#__sdi_wmtslayer_policy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_wmts_spatialpolicy]') AND type in (N'U'))
DROP TABLE [#__sdi_wmts_spatialpolicy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_wmslayer_policy]') AND type in (N'U'))
DROP TABLE [#__sdi_wmslayer_policy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_wms_spatialpolicy]') AND type in (N'U'))
DROP TABLE [#__sdi_wms_spatialpolicy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_wfs_spatialpolicy]') AND type in (N'U'))
DROP TABLE [#__sdi_wfs_spatialpolicy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_visualization]') AND type in (N'U'))
DROP TABLE [#__sdi_visualization];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_virtualservice_servicecompliance]') AND type in (N'U'))
DROP TABLE [#__sdi_virtualservice_servicecompliance];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_virtualservice_organism]') AND type in (N'U'))
DROP TABLE [#__sdi_virtualservice_organism];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_virtualservice]') AND type in (N'U'))
DROP TABLE [#__sdi_virtualservice];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_virtualmetadata]') AND type in (N'U'))
DROP TABLE [#__sdi_virtualmetadata];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_virtual_physical]') AND type in (N'U'))
DROP TABLE [#__sdi_virtual_physical];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_versionlink]') AND type in (N'U'))
DROP TABLE [#__sdi_versionlink];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_version]') AND type in (N'U'))
DROP TABLE [#__sdi_version];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_user_role_resource]') AND type in (N'U'))
DROP TABLE [#__sdi_user_role_resource];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_user_role_organism]') AND type in (N'U'))
DROP TABLE [#__sdi_user_role_organism];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_user]') AND type in (N'U'))
DROP TABLE [#__sdi_user];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_translation]') AND type in (N'U'))
DROP TABLE [#__sdi_translation];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_tilematrixset_policy]') AND type in (N'U'))
DROP TABLE [#__sdi_tilematrixset_policy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_tilematrix_policy]') AND type in (N'U'))
DROP TABLE [#__sdi_tilematrix_policy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_versiontype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_versiontype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_unit]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_unit];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_topiccategory]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_topiccategory];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_stereotype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_stereotype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_spatialoperator]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_spatialoperator];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_serviceversion]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_serviceversion];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_servicetype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_servicetype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_servicescope]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_servicescope];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_serviceoperation]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_serviceoperation];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_serviceconnector]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_serviceconnector];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_servicecon_authenticationcon]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_servicecon_authenticationcon];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_servicecompliance]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_servicecompliance];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_searchtab]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_searchtab];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_role]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_role];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_rendertype_stereotype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_rendertype_stereotype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_rendertype_criteriatype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_rendertype_criteriatype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_rendertype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_rendertype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_relationtype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_relationtype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_relationscope]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_relationscope];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_proxytype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_proxytype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_propertytype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_propertytype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_productstorage]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_productstorage];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_productstate]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_productstate];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_productmining]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_productmining];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_pricing]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_pricing];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_perimetertype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_perimetertype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_ordertype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_ordertype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_orderstate]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_orderstate];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_operationcompliance]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_operationcompliance];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_metadataversion]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_metadataversion];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_metadatastate]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_metadatastate];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_maptool]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_maptool];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_logroll]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_logroll];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_loglevel]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_loglevel];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_isolanguage]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_isolanguage];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_importtype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_importtype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_exceptionlevel]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_exceptionlevel];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_entity]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_entity];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_criteriatype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_criteriatype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_country]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_country];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_authenticationlevel]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_authenticationlevel];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_authenticationconnector]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_authenticationconnector];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_addresstype]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_addresstype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_accessscope]') AND type in (N'U'))
DROP TABLE [#__sdi_sys_accessscope];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_searchcriteriafilter]') AND type in (N'U'))
DROP TABLE [#__sdi_searchcriteriafilter];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_searchcriteria]') AND type in (N'U'))
DROP TABLE [#__sdi_searchcriteria];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_resourcetypelinkinheritance]') AND type in (N'U'))
DROP TABLE [#__sdi_resourcetypelinkinheritance];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_resourcetypelink]') AND type in (N'U'))
DROP TABLE [#__sdi_resourcetypelink];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_resourcetype]') AND type in (N'U'))
DROP TABLE [#__sdi_resourcetype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_resource]') AND type in (N'U'))
DROP TABLE [#__sdi_resource];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_relation_profile]') AND type in (N'U'))
DROP TABLE [#__sdi_relation_profile];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_relation_defaultvalue]') AND type in (N'U'))
DROP TABLE [#__sdi_relation_defaultvalue];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_relation_catalog]') AND type in (N'U'))
DROP TABLE [#__sdi_relation_catalog];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_relation]') AND type in (N'U'))
DROP TABLE [#__sdi_relation];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_propertyvalue]') AND type in (N'U'))
DROP TABLE [#__sdi_propertyvalue];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_property]') AND type in (N'U'))
DROP TABLE [#__sdi_property];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_profile]') AND type in (N'U'))
DROP TABLE [#__sdi_profile];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_policy_visibility]') AND type in (N'U'))
DROP TABLE [#__sdi_policy_visibility];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_policy_user]') AND type in (N'U'))
DROP TABLE [#__sdi_policy_user];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_policy_resourcetype]') AND type in (N'U'))
DROP TABLE [#__sdi_policy_resourcetype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_policy_organism]') AND type in (N'U'))
DROP TABLE [#__sdi_policy_organism];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_policy_metadatastate]') AND type in (N'U'))
DROP TABLE [#__sdi_policy_metadatastate];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_policy]') AND type in (N'U'))
DROP TABLE [#__sdi_policy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_physicalservice_servicecompliance]') AND type in (N'U'))
DROP TABLE [#__sdi_physicalservice_servicecompliance];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_physicalservice_policy]') AND type in (N'U'))
DROP TABLE [#__sdi_physicalservice_policy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_physicalservice_organism]') AND type in (N'U'))
DROP TABLE [#__sdi_physicalservice_organism];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_physicalservice]') AND type in (N'U'))
DROP TABLE [#__sdi_physicalservice];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_perimeter]') AND type in (N'U'))
DROP TABLE [#__sdi_perimeter];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_organism]') AND type in (N'U'))
DROP TABLE [#__sdi_organism];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_order_propertyvalue]') AND type in (N'U'))
DROP TABLE [#__sdi_order_propertyvalue];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_order_perimeter]') AND type in (N'U'))
DROP TABLE [#__sdi_order_perimeter];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_order_diffusion]') AND type in (N'U'))
DROP TABLE [#__sdi_order_diffusion];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_order]') AND type in (N'U'))
DROP TABLE [#__sdi_order];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_namespace]') AND type in (N'U'))
DROP TABLE [#__sdi_namespace];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_monitor_exports]') AND type in (N'U'))
DROP TABLE [#__sdi_monitor_exports];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_metadata]') AND type in (N'U'))
DROP TABLE [#__sdi_metadata];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_maplayer]') AND type in (N'U'))
DROP TABLE [#__sdi_maplayer];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_map_virtualservice]') AND type in (N'U'))
DROP TABLE [#__sdi_map_virtualservice];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_map_tool]') AND type in (N'U'))
DROP TABLE [#__sdi_map_tool];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_map_physicalservice]') AND type in (N'U'))
DROP TABLE [#__sdi_map_physicalservice];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_map_layergroup]') AND type in (N'U'))
DROP TABLE [#__sdi_map_layergroup];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_map]') AND type in (N'U'))
DROP TABLE [#__sdi_map];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_layergroup]') AND type in (N'U'))
DROP TABLE [#__sdi_layergroup];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_layer_layergroup]') AND type in (N'U'))
DROP TABLE [#__sdi_layer_layergroup];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_layer]') AND type in (N'U'))
DROP TABLE [#__sdi_layer];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_language]') AND type in (N'U'))
DROP TABLE [#__sdi_language];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_includedattribute]') AND type in (N'U'))
DROP TABLE [#__sdi_includedattribute];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_importref]') AND type in (N'U'))
DROP TABLE [#__sdi_importref];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_featuretype_policy]') AND type in (N'U'))
DROP TABLE [#__sdi_featuretype_policy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_excludedattribute]') AND type in (N'U'))
DROP TABLE [#__sdi_excludedattribute];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_propertyvalue]') AND type in (N'U'))
DROP TABLE [#__sdi_diffusion_propertyvalue];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_perimeter]') AND type in (N'U'))
DROP TABLE [#__sdi_diffusion_perimeter];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_notifieduser]') AND type in (N'U'))
DROP TABLE [#__sdi_diffusion_notifieduser];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion_download]') AND type in (N'U'))
DROP TABLE [#__sdi_diffusion_download];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_diffusion]') AND type in (N'U'))
DROP TABLE [#__sdi_diffusion];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_csw_spatialpolicy]') AND type in (N'U'))
DROP TABLE [#__sdi_csw_spatialpolicy];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_class]') AND type in (N'U'))
DROP TABLE [#__sdi_class];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_catalog_searchsort]') AND type in (N'U'))
DROP TABLE [#__sdi_catalog_searchsort];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_catalog_searchcriteria]') AND type in (N'U'))
DROP TABLE [#__sdi_catalog_searchcriteria];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_catalog_resourcetype]') AND type in (N'U'))
DROP TABLE [#__sdi_catalog_resourcetype];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_catalog]') AND type in (N'U'))
DROP TABLE [#__sdi_catalog];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_boundarycategory]') AND type in (N'U'))
DROP TABLE [#__sdi_boundarycategory];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_boundary]') AND type in (N'U'))
DROP TABLE [#__sdi_boundary];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_attributevalue]') AND type in (N'U'))
DROP TABLE [#__sdi_attributevalue];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_attribute]') AND type in (N'U'))
DROP TABLE [#__sdi_attribute];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_assignment]') AND type in (N'U'))
DROP TABLE [#__sdi_assignment];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_application]') AND type in (N'U'))
DROP TABLE [#__sdi_application];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_allowedoperation]') AND type in (N'U'))
DROP TABLE [#__sdi_allowedoperation];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_address]') AND type in (N'U'))
DROP TABLE [#__sdi_address];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_accessscope]') AND type in (N'U'))
DROP TABLE [#__sdi_accessscope];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[jobs]') AND type in (N'U'))
DROP TABLE [jobs];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[job_defaults]') AND type in (N'U'))
DROP TABLE [job_defaults];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[job_agg_log_entries]') AND type in (N'U'))
DROP TABLE [job_agg_log_entries];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[job_agg_hour_log_entries]') AND type in (N'U'))
DROP TABLE [job_agg_hour_log_entries];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[http_methods]') AND type in (N'U'))
DROP TABLE [http_methods];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[holidays]') AND type in (N'U'))
DROP TABLE [holidays];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[alerts]') AND type in (N'U'))
DROP TABLE [alerts];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[actions]') AND type in (N'U'))
DROP TABLE [actions];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[action_types]') AND type in (N'U'))
DROP TABLE [action_types];


IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_category]') AND type in (N'U'))
DROP TABLE [#__sdi_category];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_organism_category]') AND type in (N'U'))
DROP TABLE [#__sdi_organism_category];
IF  EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_policy_category]') AND type in (N'U'))
DROP TABLE [#__sdi_policy_category];


IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_organism_category_pricing_rebate]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_organism_category_pricing_rebate];
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_pricing_profile]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_pricing_profile];
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_pricing_profile_category_pricing_rebate]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_pricing_profile_category_pricing_rebate];
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_pricing_order]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_pricing_order];
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_pricing_order_supplier]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_pricing_order_supplier];
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_pricing_order_supplier_product]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_pricing_order_supplier_product];
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_pricing_order_supplier_product_profile]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_pricing_order_supplier_product_profile];
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_sys_extractstorage]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_sys_extractstorage];

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_processing]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_processing];
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_processing_obs]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_processing_obs];
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__sdi_processing_order]') AND TYPE IN (N'U'))
DROP TABLE [#__sdi_processing_order];