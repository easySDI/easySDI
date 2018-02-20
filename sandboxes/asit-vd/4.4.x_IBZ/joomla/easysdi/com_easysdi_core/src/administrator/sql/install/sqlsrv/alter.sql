ALTER TABLE [actions]  WITH CHECK ADD  CONSTRAINT [actions$FK_ACTION_JOB] FOREIGN KEY([ID_JOB])
REFERENCES [jobs] ([ID_JOB])
ON DELETE CASCADE;

ALTER TABLE [actions] CHECK CONSTRAINT [actions$FK_ACTION_JOB];

ALTER TABLE [actions]  WITH NOCHECK ADD  CONSTRAINT [actions$FK_ACTION_TYPE] FOREIGN KEY([ID_ACTION_TYPE])
REFERENCES [action_types] ([ID_ACTION_TYPE]);

ALTER TABLE [actions] CHECK CONSTRAINT [actions$FK_ACTION_TYPE];

ALTER TABLE [alerts]  WITH CHECK ADD  CONSTRAINT [alerts$FK_ALERTS_JOB] FOREIGN KEY([ID_JOB])
REFERENCES [jobs] ([ID_JOB])
ON DELETE CASCADE;

ALTER TABLE [alerts] CHECK CONSTRAINT [alerts$FK_ALERTS_JOB];

ALTER TABLE [alerts]  WITH NOCHECK ADD  CONSTRAINT [alerts$FK_ALERTS_NEW_STATUS] FOREIGN KEY([ID_NEW_STATUS])
REFERENCES [statuses] ([ID_STATUS]);

ALTER TABLE [alerts] CHECK CONSTRAINT [alerts$FK_ALERTS_NEW_STATUS];

ALTER TABLE [alerts]  WITH NOCHECK ADD  CONSTRAINT [alerts$FK_ALERTS_OLD_STATUS] FOREIGN KEY([ID_OLD_STATUS])
REFERENCES [statuses] ([ID_STATUS]);

ALTER TABLE [alerts] CHECK CONSTRAINT [alerts$FK_ALERTS_OLD_STATUS];

ALTER TABLE [job_agg_hour_log_entries]  WITH CHECK ADD  CONSTRAINT [job_agg_hour_log_entries$FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB] FOREIGN KEY([ID_JOB])
REFERENCES [jobs] ([ID_JOB])
ON UPDATE CASCADE
ON DELETE CASCADE;

ALTER TABLE [job_agg_hour_log_entries] CHECK CONSTRAINT [job_agg_hour_log_entries$FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB];

ALTER TABLE [job_agg_log_entries]  WITH CHECK ADD  CONSTRAINT [job_agg_log_entries$FK_JOB_AGG_LOG_ENTRIES_JOB] FOREIGN KEY([ID_JOB])
REFERENCES [jobs] ([ID_JOB])
ON UPDATE CASCADE
ON DELETE CASCADE;

ALTER TABLE [job_agg_log_entries] CHECK CONSTRAINT [job_agg_log_entries$FK_JOB_AGG_LOG_ENTRIES_JOB];

ALTER TABLE [jobs]  WITH NOCHECK ADD  CONSTRAINT [jobs$FK_JOBS_HTTP_METHOD] FOREIGN KEY([ID_HTTP_METHOD])
REFERENCES [http_methods] ([ID_HTTP_METHOD]);

ALTER TABLE [jobs] CHECK CONSTRAINT [jobs$FK_JOBS_HTTP_METHOD];

ALTER TABLE [jobs]  WITH NOCHECK ADD  CONSTRAINT [jobs$FK_JOBS_SERVICE_TYPE] FOREIGN KEY([ID_SERVICE_TYPE])
REFERENCES [service_types] ([ID_SERVICE_TYPE]);

ALTER TABLE [jobs] CHECK CONSTRAINT [jobs$FK_JOBS_SERVICE_TYPE];

ALTER TABLE [jobs]  WITH NOCHECK ADD  CONSTRAINT [jobs$FK_JOBS_STATUS] FOREIGN KEY([ID_STATUS])
REFERENCES [statuses] ([ID_STATUS]);

ALTER TABLE [jobs] CHECK CONSTRAINT [jobs$FK_JOBS_STATUS];

ALTER TABLE [#__sdi_accessscope]  WITH CHECK ADD  CONSTRAINT [#__sdi_accessscope$#__sdi_accessscope_fk1] FOREIGN KEY([organism_id])
REFERENCES [#__sdi_organism] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_accessscope] CHECK CONSTRAINT [#__sdi_accessscope$#__sdi_accessscope_fk1];

ALTER TABLE [#__sdi_accessscope]  WITH CHECK ADD  CONSTRAINT [#__sdi_accessscope$#__sdi_accessscope_fk2] FOREIGN KEY([user_id])
REFERENCES [#__sdi_user] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_accessscope] CHECK CONSTRAINT [#__sdi_accessscope$#__sdi_accessscope_fk2];

ALTER TABLE [#__sdi_accessscope]  WITH CHECK ADD  CONSTRAINT [#__sdi_accessscope$#__sdi_accessscope_fk3] FOREIGN KEY([category_id])
REFERENCES [#__sdi_category] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_accessscope] CHECK CONSTRAINT [#__sdi_accessscope$#__sdi_accessscope_fk3];

ALTER TABLE [#__sdi_address]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_address$#__sdi_address_fk1] FOREIGN KEY([addresstype_id])
REFERENCES [#__sdi_sys_addresstype] ([id]);

ALTER TABLE [#__sdi_address] CHECK CONSTRAINT [#__sdi_address$#__sdi_address_fk1];

ALTER TABLE [#__sdi_address]  WITH CHECK ADD  CONSTRAINT [#__sdi_address$#__sdi_address_fk3] FOREIGN KEY([user_id])
REFERENCES [#__sdi_user] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_address] CHECK CONSTRAINT [#__sdi_address$#__sdi_address_fk3];

ALTER TABLE [#__sdi_address]  WITH CHECK ADD  CONSTRAINT [#__sdi_address$#__sdi_address_fk4] FOREIGN KEY([organism_id])
REFERENCES [#__sdi_organism] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_address] CHECK CONSTRAINT [#__sdi_address$#__sdi_address_fk4];

ALTER TABLE [#__sdi_address]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_address$#__sdi_address_fk5] FOREIGN KEY([country_id])
REFERENCES [#__sdi_sys_country] ([id]);

ALTER TABLE [#__sdi_address] CHECK CONSTRAINT [#__sdi_address$#__sdi_address_fk5];

ALTER TABLE [#__sdi_application]  WITH CHECK ADD  CONSTRAINT [#__sdi_application$#__sdi_application_fk1] FOREIGN KEY([resource_id])
REFERENCES [#__sdi_resource] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_application] CHECK CONSTRAINT [#__sdi_application$#__sdi_application_fk1];

ALTER TABLE [#__sdi_assignment]  WITH CHECK ADD  CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk1] FOREIGN KEY([assigned_by])
REFERENCES [#__sdi_user] ([id]);

ALTER TABLE [#__sdi_assignment] CHECK CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk1];

ALTER TABLE [#__sdi_assignment]  WITH CHECK ADD  CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk2] FOREIGN KEY([assigned_to])
REFERENCES [#__sdi_user] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_assignment] CHECK CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk2];

ALTER TABLE [#__sdi_assignment]  WITH CHECK ADD  CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk3] FOREIGN KEY([metadata_id])
REFERENCES [#__sdi_metadata] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_assignment] CHECK CONSTRAINT [#__sdi_assignment$#__sdi_assignment_fk3];

ALTER TABLE [#__sdi_attribute]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_attribute$sdi_attribute_fk1] FOREIGN KEY([namespace_id])
REFERENCES [#__sdi_namespace] ([id]);

ALTER TABLE [#__sdi_attribute] CHECK CONSTRAINT [#__sdi_attribute$sdi_attribute_fk1];

ALTER TABLE [#__sdi_attribute]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_attribute$sdi_attribute_fk2] FOREIGN KEY([listnamespace_id])
REFERENCES [#__sdi_namespace] ([id]);

ALTER TABLE [#__sdi_attribute] CHECK CONSTRAINT [#__sdi_attribute$sdi_attribute_fk2];

ALTER TABLE [#__sdi_attribute]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_attribute$sdi_attribute_fk3] FOREIGN KEY([stereotype_id])
REFERENCES [#__sdi_sys_stereotype] ([id]);

ALTER TABLE [#__sdi_attribute] CHECK CONSTRAINT [#__sdi_attribute$sdi_attribute_fk3];

ALTER TABLE [#__sdi_attributevalue]  WITH CHECK ADD  CONSTRAINT [#__sdi_attributevalue$#__sdi_attributevalue] FOREIGN KEY([attribute_id])
REFERENCES [#__sdi_attribute] ([id]);

ALTER TABLE [#__sdi_attributevalue] CHECK CONSTRAINT [#__sdi_attributevalue$#__sdi_attributevalue];

ALTER TABLE [#__sdi_boundary]  WITH CHECK ADD  CONSTRAINT [#__sdi_boundary$#__sdi_boundary_fk1] FOREIGN KEY([parent_id])
REFERENCES [#__sdi_boundary] ([id]);

ALTER TABLE [#__sdi_boundary] CHECK CONSTRAINT [#__sdi_boundary$#__sdi_boundary_fk1];

ALTER TABLE [#__sdi_boundary]  WITH CHECK ADD  CONSTRAINT [#__sdi_boundary$#__sdi_boundary_fk2] FOREIGN KEY([category_id])
REFERENCES [#__sdi_boundarycategory] ([id]);

ALTER TABLE [#__sdi_boundary] CHECK CONSTRAINT [#__sdi_boundary$#__sdi_boundary_fk2];

ALTER TABLE [#__sdi_boundarycategory]  WITH CHECK ADD  CONSTRAINT [#__sdi_boundarycategory$#__sdi_boundarycategory_fk1] FOREIGN KEY([parent_id])
REFERENCES [#__sdi_boundarycategory] ([id]);

ALTER TABLE [#__sdi_boundarycategory] CHECK CONSTRAINT [#__sdi_boundarycategory$#__sdi_boundarycategory_fk1];

ALTER TABLE [#__sdi_catalog_resourcetype]  WITH CHECK ADD  CONSTRAINT [#__sdi_catalog_resourcetype$#__sdi_catalog_resourcetype_fk1] FOREIGN KEY([catalog_id])
REFERENCES [#__sdi_catalog] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_catalog_resourcetype] CHECK CONSTRAINT [#__sdi_catalog_resourcetype$#__sdi_catalog_resourcetype_fk1];

ALTER TABLE [#__sdi_catalog_resourcetype]  WITH CHECK ADD  CONSTRAINT [#__sdi_catalog_resourcetype$#__sdi_catalog_resourcetype_fk2] FOREIGN KEY([resourcetype_id])
REFERENCES [#__sdi_resourcetype] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_catalog_resourcetype] CHECK CONSTRAINT [#__sdi_catalog_resourcetype$#__sdi_catalog_resourcetype_fk2];

ALTER TABLE [#__sdi_catalog_searchcriteria]  WITH CHECK ADD  CONSTRAINT [#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk1] FOREIGN KEY([catalog_id])
REFERENCES [#__sdi_catalog] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE;

ALTER TABLE [#__sdi_catalog_searchcriteria] CHECK CONSTRAINT [#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk1];

ALTER TABLE [#__sdi_catalog_searchcriteria]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk2] FOREIGN KEY([searchcriteria_id])
REFERENCES [#__sdi_searchcriteria] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE;

ALTER TABLE [#__sdi_catalog_searchcriteria] CHECK CONSTRAINT [#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk2];

ALTER TABLE [#__sdi_catalog_searchcriteria]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk3] FOREIGN KEY([searchtab_id])
REFERENCES [#__sdi_sys_searchtab] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE;

ALTER TABLE [#__sdi_catalog_searchcriteria] CHECK CONSTRAINT [#__sdi_catalog_searchcriteria$#__sdi_catalog_searchcriteria_fk3];

ALTER TABLE [#__sdi_catalog_searchsort]  WITH CHECK ADD  CONSTRAINT [#__sdi_catalog_searchsort$#__sdi_catalog_searchsort_fk1] FOREIGN KEY([catalog_id])
REFERENCES [#__sdi_catalog] ([id]);

ALTER TABLE [#__sdi_catalog_searchsort] CHECK CONSTRAINT [#__sdi_catalog_searchsort$#__sdi_catalog_searchsort_fk1];

ALTER TABLE [#__sdi_catalog_searchsort]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_catalog_searchsort$#__sdi_catalog_searchsort_fk2] FOREIGN KEY([language_id])
REFERENCES [#__sdi_language] ([id]);

ALTER TABLE [#__sdi_catalog_searchsort] CHECK CONSTRAINT [#__sdi_catalog_searchsort$#__sdi_catalog_searchsort_fk2];

ALTER TABLE [#__sdi_class]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_class$#__sdi_class_fk1] FOREIGN KEY([namespace_id])
REFERENCES [#__sdi_namespace] ([id]);

ALTER TABLE [#__sdi_class] CHECK CONSTRAINT [#__sdi_class$#__sdi_class_fk1];

ALTER TABLE [#__sdi_class]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_class$#__sdi_class_fk2] FOREIGN KEY([stereotype_id])
REFERENCES [#__sdi_sys_stereotype] ([id]);

ALTER TABLE [#__sdi_class] CHECK CONSTRAINT [#__sdi_class$#__sdi_class_fk2];

ALTER TABLE [#__sdi_diffusion]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk1] FOREIGN KEY([accessscope_id])
REFERENCES [#__sdi_sys_accessscope] ([id]);

ALTER TABLE [#__sdi_diffusion] CHECK CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk1];

ALTER TABLE [#__sdi_diffusion]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk2] FOREIGN KEY([productmining_id])
REFERENCES [#__sdi_sys_productmining] ([id]);

ALTER TABLE [#__sdi_diffusion] CHECK CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk2];

ALTER TABLE [#__sdi_diffusion]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk3] FOREIGN KEY([productstorage_id])
REFERENCES [#__sdi_sys_productstorage] ([id]);

ALTER TABLE [#__sdi_diffusion] CHECK CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk3];

ALTER TABLE [#__sdi_diffusion]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk4] FOREIGN KEY([perimeter_id])
REFERENCES [#__sdi_perimeter] ([id]);

ALTER TABLE [#__sdi_diffusion] CHECK CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk4];

ALTER TABLE [#__sdi_diffusion]  WITH CHECK ADD  CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk5] FOREIGN KEY([version_id])
REFERENCES [#__sdi_version] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_diffusion] CHECK CONSTRAINT [#__sdi_diffusion$#__sdi_diffusion_fk5];

ALTER TABLE [#__sdi_diffusion]  WITH CHECK ADD  CONSTRAINT [#__sdi_diffusion#__sdi_diffusion_fk6] FOREIGN KEY([pricing_profile_id])
REFERENCES [#__sdi_pricing_profile] ([id])
ON DELETE SET NULL;

ALTER TABLE [#__sdi_diffusion] CHECK CONSTRAINT [#__sdi_diffusion#__sdi_diffusion_fk6];

ALTER TABLE [#__sdi_pricing_order]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_order#__sdi_pricing_order_fk1] FOREIGN KEY([order_id])
REFERENCES [#__sdi_order] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_order] CHECK CONSTRAINT [#__sdi_pricing_order#__sdi_pricing_order_fk1];

ALTER TABLE [#__sdi_diffusion_download]  WITH CHECK ADD  CONSTRAINT [#__sdi_diffusion_download$#__sdi_diffusion_download_fk1] FOREIGN KEY([diffusion_id])
REFERENCES [#__sdi_diffusion] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_diffusion_download] CHECK CONSTRAINT [#__sdi_diffusion_download$#__sdi_diffusion_download_fk1];

ALTER TABLE [#__sdi_diffusion_download]  WITH CHECK ADD  CONSTRAINT [#__sdi_diffusion_download$#__sdi_diffusion_download_fk2] FOREIGN KEY([user_id])
REFERENCES [#__sdi_user] ([id])
ON DELETE SET NULL;

ALTER TABLE [#__sdi_diffusion_download] CHECK CONSTRAINT [#__sdi_diffusion_download$#__sdi_diffusion_download_fk2];

ALTER TABLE [#__sdi_diffusion_notifieduser]  WITH CHECK ADD  CONSTRAINT [#__sdi_diffusion_notifieduser$#__sdi_diffusion_notifieduser_fk1] FOREIGN KEY([diffusion_id])
REFERENCES [#__sdi_diffusion] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_diffusion_notifieduser] CHECK CONSTRAINT [#__sdi_diffusion_notifieduser$#__sdi_diffusion_notifieduser_fk1];

ALTER TABLE [#__sdi_diffusion_notifieduser]  WITH CHECK ADD  CONSTRAINT [#__sdi_diffusion_notifieduser$#__sdi_diffusion_notifieduser_fk2] FOREIGN KEY([user_id])
REFERENCES [#__sdi_user] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_diffusion_notifieduser] CHECK CONSTRAINT [#__sdi_diffusion_notifieduser$#__sdi_diffusion_notifieduser_fk2];

ALTER TABLE [#__sdi_diffusion_perimeter]  WITH CHECK ADD  CONSTRAINT [#__sdi_diffusion_perimeter$#__sdi_diffusion_perimeter_fk1] FOREIGN KEY([diffusion_id])
REFERENCES [#__sdi_diffusion] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_diffusion_perimeter] CHECK CONSTRAINT [#__sdi_diffusion_perimeter$#__sdi_diffusion_perimeter_fk1];

ALTER TABLE [#__sdi_diffusion_perimeter]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_diffusion_perimeter$#__sdi_diffusion_perimeter_fk2] FOREIGN KEY([perimeter_id])
REFERENCES [#__sdi_perimeter] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_diffusion_perimeter] CHECK CONSTRAINT [#__sdi_diffusion_perimeter$#__sdi_diffusion_perimeter_fk2];

ALTER TABLE [#__sdi_diffusion_propertyvalue]  WITH CHECK ADD  CONSTRAINT [#__sdi_diffusion_propertyvalue$#__sdi_diffusion_propertyvalue_fk1] FOREIGN KEY([diffusion_id])
REFERENCES [#__sdi_diffusion] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_diffusion_propertyvalue] CHECK CONSTRAINT [#__sdi_diffusion_propertyvalue$#__sdi_diffusion_propertyvalue_fk1];

ALTER TABLE [#__sdi_diffusion_propertyvalue]  WITH CHECK ADD  CONSTRAINT [#__sdi_diffusion_propertyvalue$#__sdi_diffusion_propertyvalue_fk2] FOREIGN KEY([propertyvalue_id])
REFERENCES [#__sdi_propertyvalue] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_diffusion_propertyvalue] CHECK CONSTRAINT [#__sdi_diffusion_propertyvalue$#__sdi_diffusion_propertyvalue_fk2];

ALTER TABLE [#__sdi_importref]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_importref$#__sdi_importref_fk1] FOREIGN KEY([importtype_id])
REFERENCES [#__sdi_sys_importtype] ([id]);

ALTER TABLE [#__sdi_importref] CHECK CONSTRAINT [#__sdi_importref$#__sdi_importref_fk1];

ALTER TABLE [#__sdi_importref]  WITH CHECK ADD  CONSTRAINT [#__sdi_importref$#__sdi_importref_fk2] FOREIGN KEY([cswservice_id])
REFERENCES [#__sdi_physicalservice] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_importref] CHECK CONSTRAINT [#__sdi_importref$#__sdi_importref_fk2];

ALTER TABLE [#__sdi_importref]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_importref$#__sdi_importref_fk3] FOREIGN KEY([cswversion_id])
REFERENCES [#__sdi_sys_serviceversion] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_importref] CHECK CONSTRAINT [#__sdi_importref$#__sdi_importref_fk3];

ALTER TABLE [#__sdi_layer_layergroup]  WITH CHECK ADD  CONSTRAINT [#__sdi_layer_layergroup$#__sdi_layer_layergroup_fk1] FOREIGN KEY([layer_id])
REFERENCES [#__sdi_maplayer] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_layer_layergroup] CHECK CONSTRAINT [#__sdi_layer_layergroup$#__sdi_layer_layergroup_fk1];

ALTER TABLE [#__sdi_layer_layergroup]  WITH CHECK ADD  CONSTRAINT [#__sdi_layer_layergroup$#__sdi_layer_layergroup_fk2] FOREIGN KEY([group_id])
REFERENCES [#__sdi_layergroup] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_layer_layergroup] CHECK CONSTRAINT [#__sdi_layer_layergroup$#__sdi_layer_layergroup_fk2];

ALTER TABLE [#__sdi_map]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_map$#__sdi_map_fk2] FOREIGN KEY([unit_id])
REFERENCES [#__sdi_sys_unit] ([id]);

ALTER TABLE [#__sdi_map] CHECK CONSTRAINT [#__sdi_map$#__sdi_map_fk2];

ALTER TABLE [#__sdi_map_layergroup]  WITH CHECK ADD  CONSTRAINT [#__sdi_map_layergroup$#__sdi_map_layergroup_fk1] FOREIGN KEY([map_id])
REFERENCES [#__sdi_map] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_map_layergroup] CHECK CONSTRAINT [#__sdi_map_layergroup$#__sdi_map_layergroup_fk1];

ALTER TABLE [#__sdi_map_layergroup]  WITH CHECK ADD  CONSTRAINT [#__sdi_map_layergroup$#__sdi_map_layergroup_fk2] FOREIGN KEY([group_id])
REFERENCES [#__sdi_layergroup] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_map_layergroup] CHECK CONSTRAINT [#__sdi_map_layergroup$#__sdi_map_layergroup_fk2];

ALTER TABLE [#__sdi_map_physicalservice]  WITH CHECK ADD  CONSTRAINT [#__sdi_map_physicalservice$#__sdi_map_physicalservice_fk1] FOREIGN KEY([map_id])
REFERENCES [#__sdi_map] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_map_physicalservice] CHECK CONSTRAINT [#__sdi_map_physicalservice$#__sdi_map_physicalservice_fk1];

ALTER TABLE [#__sdi_map_physicalservice]  WITH CHECK ADD  CONSTRAINT [#__sdi_map_physicalservice$#__sdi_map_physicalservice_fk2] FOREIGN KEY([physicalservice_id])
REFERENCES [#__sdi_physicalservice] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_map_physicalservice] CHECK CONSTRAINT [#__sdi_map_physicalservice$#__sdi_map_physicalservice_fk2];

ALTER TABLE [#__sdi_map_tool]  WITH CHECK ADD  CONSTRAINT [#__sdi_map_tool$#__sdi_map_tool_fk1] FOREIGN KEY([map_id])
REFERENCES [#__sdi_map] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_map_tool] CHECK CONSTRAINT [#__sdi_map_tool$#__sdi_map_tool_fk1];

ALTER TABLE [#__sdi_map_tool]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_map_tool$#__sdi_map_tool_fk2] FOREIGN KEY([tool_id])
REFERENCES [#__sdi_sys_maptool] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_map_tool] CHECK CONSTRAINT [#__sdi_map_tool$#__sdi_map_tool_fk2];

ALTER TABLE [#__sdi_map_virtualservice]  WITH CHECK ADD  CONSTRAINT [#__sdi_map_virtualservice$#__sdi_map_virtualservice_fk1] FOREIGN KEY([map_id])
REFERENCES [#__sdi_map] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_map_virtualservice] CHECK CONSTRAINT [#__sdi_map_virtualservice$#__sdi_map_virtualservice_fk1];

ALTER TABLE [#__sdi_map_virtualservice]  WITH CHECK ADD  CONSTRAINT [#__sdi_map_virtualservice$#__sdi_map_virtualservice_fk2] FOREIGN KEY([virtualservice_id])
REFERENCES [#__sdi_virtualservice] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_map_virtualservice] CHECK CONSTRAINT [#__sdi_map_virtualservice$#__sdi_map_virtualservice_fk2];

ALTER TABLE [#__sdi_maplayer]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_maplayer$#__sdi_maplayer_fk1] FOREIGN KEY([accessscope_id])
REFERENCES [#__sdi_sys_accessscope] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_maplayer] CHECK CONSTRAINT [#__sdi_maplayer$#__sdi_maplayer_fk1];

ALTER TABLE [#__sdi_metadata]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_metadata$#__sdi_metadata_fk1] FOREIGN KEY([metadatastate_id])
REFERENCES [#__sdi_sys_metadatastate] ([id]);

ALTER TABLE [#__sdi_metadata] CHECK CONSTRAINT [#__sdi_metadata$#__sdi_metadata_fk1];

ALTER TABLE [#__sdi_metadata]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_metadata$#__sdi_metadata_fk2] FOREIGN KEY([accessscope_id])
REFERENCES [#__sdi_sys_accessscope] ([id]);

ALTER TABLE [#__sdi_metadata] CHECK CONSTRAINT [#__sdi_metadata$#__sdi_metadata_fk2];

ALTER TABLE [#__sdi_metadata]  WITH CHECK ADD  CONSTRAINT [#__sdi_metadata$#__sdi_metadata_fk3] FOREIGN KEY([version_id])
REFERENCES [#__sdi_version] ([id]);

ALTER TABLE [#__sdi_metadata] CHECK CONSTRAINT [#__sdi_metadata$#__sdi_metadata_fk3];

ALTER TABLE [#__sdi_virtualmetadata] WITH CHECK ADD CONSTRAINT [#__sdi_virtualmetadata$#__sdi_virtualmetadata_fk1] FOREIGN KEY ([virtualservice_id])
REFERENCES [#__sdi_virtualservice] ([id]);
ALTER TABLE [#__sdi_virtualmetadata] CHECK CONSTRAINT [#__sdi_virtualmetadata$#__sdi_virtualmetadata_fk1];

ALTER TABLE [#__sdi_virtualmetadata] WITH CHECK ADD CONSTRAINT [#__sdi_virtualmetadata$#__sdi_virtualmetadata_fk2] FOREIGN KEY ([country_id])
REFERENCES [#__sdi_sys_country] ([id]);
ALTER TABLE [#__sdi_virtualmetadata] CHECK CONSTRAINT [#__sdi_virtualmetadata$#__sdi_virtualmetadata_fk2];

ALTER TABLE [#__sdi_order]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_order$#__sdi_order_fk1] FOREIGN KEY([ordertype_id])
REFERENCES [#__sdi_sys_ordertype] ([id]);

ALTER TABLE [#__sdi_order] CHECK CONSTRAINT [#__sdi_order$#__sdi_order_fk1];

ALTER TABLE [#__sdi_order]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_order$#__sdi_order_fk2] FOREIGN KEY([orderstate_id])
REFERENCES [#__sdi_sys_orderstate] ([id]);

ALTER TABLE [#__sdi_order] CHECK CONSTRAINT [#__sdi_order$#__sdi_order_fk2];

ALTER TABLE [#__sdi_order]  WITH CHECK ADD  CONSTRAINT [#__sdi_order$#__sdi_order_fk3] FOREIGN KEY([user_id])
REFERENCES [#__sdi_user] ([id]);

ALTER TABLE [#__sdi_order] CHECK CONSTRAINT [#__sdi_order$#__sdi_order_fk3];

ALTER TABLE [#__sdi_order]  WITH CHECK ADD  CONSTRAINT [#__sdi_order$#__sdi_order_fk4] FOREIGN KEY([thirdparty_id])
REFERENCES [#__sdi_organism] ([id]);

ALTER TABLE [#__sdi_order] CHECK CONSTRAINT [#__sdi_order$#__sdi_order_fk4];

ALTER TABLE [#__sdi_order_diffusion]  WITH CHECK ADD  CONSTRAINT [#__sdi_order_diffusion$#__sdi_order_diffusion_fk1] FOREIGN KEY([order_id])
REFERENCES [#__sdi_order] ([id])
ON DELETE CASCADE
ON UPDATE NO ACTION;

ALTER TABLE [#__sdi_order_diffusion] CHECK CONSTRAINT [#__sdi_order_diffusion$#__sdi_order_diffusion_fk1];

ALTER TABLE [#__sdi_order_diffusion]  WITH CHECK ADD  CONSTRAINT [#__sdi_order_diffusion$#__sdi_order_diffusion_fk2] FOREIGN KEY([diffusion_id])
REFERENCES [#__sdi_diffusion] ([id])
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE [#__sdi_order_diffusion] CHECK CONSTRAINT [#__sdi_order_diffusion$#__sdi_order_diffusion_fk2];

ALTER TABLE [#__sdi_order_diffusion]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_order_diffusion$#__sdi_order_diffusion_fk3] FOREIGN KEY([productstate_id])
REFERENCES [#__sdi_sys_productstate] ([id])
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE [#__sdi_order_diffusion]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_order_diffusion$#__sdi_order_diffusion_fk4] FOREIGN KEY([storage_id])
REFERENCES [#__sdi_sys_extractstorage] ([id])
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE [#__sdi_order_diffusion] CHECK CONSTRAINT [#__sdi_order_diffusion$#__sdi_order_diffusion_fk3];

ALTER TABLE [#__sdi_order_perimeter]  WITH CHECK ADD  CONSTRAINT [#__sdi_order_perimeter$#__sdi_order_perimeter_fk1] FOREIGN KEY([order_id])
REFERENCES [#__sdi_order] ([id])
ON DELETE CASCADE
ON UPDATE NO ACTION;
ALTER TABLE [#__sdi_order_perimeter] CHECK CONSTRAINT [#__sdi_order_perimeter$#__sdi_order_perimeter_fk1];

ALTER TABLE [#__sdi_order_perimeter]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_order_perimeter$#__sdi_order_perimeter_fk2] FOREIGN KEY([perimeter_id])
REFERENCES [#__sdi_perimeter] ([id])
ON DELETE CASCADE
ON UPDATE NO ACTION;

ALTER TABLE [#__sdi_order_perimeter] CHECK CONSTRAINT [#__sdi_order_perimeter$#__sdi_order_perimeter_fk2];

ALTER TABLE [#__sdi_organism_category_pricing_rebate]  WITH CHECK ADD  CONSTRAINT [#__sdi_organism_category_pricing_rebate#__sdi_organism_category_pricing_rebate_fk1] FOREIGN KEY([organism_id])
REFERENCES [#__sdi_organism] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_organism_category_pricing_rebate] CHECK CONSTRAINT [#__sdi_organism_category_pricing_rebate#__sdi_organism_category_pricing_rebate_fk1];

ALTER TABLE [#__sdi_organism_category_pricing_rebate]  WITH CHECK ADD  CONSTRAINT [#__sdi_organism_category_pricing_rebate#__sdi_organism_category_pricing_rebate_fk2] FOREIGN KEY([category_id])
REFERENCES [#__sdi_category] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_organism_category_pricing_rebate] CHECK CONSTRAINT [#__sdi_organism_category_pricing_rebate#__sdi_organism_category_pricing_rebate_fk2];

ALTER TABLE [#__sdi_order_propertyvalue]  WITH CHECK ADD  CONSTRAINT [#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk1] FOREIGN KEY([orderdiffusion_id])
REFERENCES [#__sdi_order_diffusion] ([id])
ON DELETE CASCADE
ON UPDATE NO ACTION;

ALTER TABLE [#__sdi_order_propertyvalue] CHECK CONSTRAINT [#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk1];

ALTER TABLE [#__sdi_order_propertyvalue]  WITH CHECK ADD  CONSTRAINT [#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk2] FOREIGN KEY([property_id])
REFERENCES [#__sdi_property] ([id])
ON DELETE CASCADE
ON UPDATE NO ACTION;

ALTER TABLE [#__sdi_order_propertyvalue] CHECK CONSTRAINT [#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk2];

ALTER TABLE [#__sdi_order_propertyvalue]  WITH CHECK ADD  CONSTRAINT [#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk3] FOREIGN KEY([propertyvalue_id])
REFERENCES [#__sdi_propertyvalue] ([id])
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE [#__sdi_order_propertyvalue] CHECK CONSTRAINT [#__sdi_order_propertyvalue$#__sdi_order_propertyvalue_fk3];

ALTER TABLE [#__sdi_perimeter]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_perimeter$#__sdi_perimeter_fk1] FOREIGN KEY([accessscope_id])
REFERENCES [#__sdi_sys_accessscope] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_perimeter] CHECK CONSTRAINT [#__sdi_perimeter$#__sdi_perimeter_fk1];

ALTER TABLE [#__sdi_perimeter]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_perimeter$#__sdi_perimeter_fk2] FOREIGN KEY([perimetertype_id])
REFERENCES [#__sdi_sys_perimetertype] ([id]);

ALTER TABLE [#__sdi_perimeter] CHECK CONSTRAINT [#__sdi_perimeter$#__sdi_perimeter_fk2];

ALTER TABLE [#__sdi_profile]  WITH CHECK ADD  CONSTRAINT [#__sdi_profile$#__sdi_profile_fk1] FOREIGN KEY([class_id])
REFERENCES [#__sdi_class] ([id]);

ALTER TABLE [#__sdi_profile] CHECK CONSTRAINT [#__sdi_profile$#__sdi_profile_fk1];

ALTER TABLE [#__sdi_property]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_property$#__sdi_property_fk1] FOREIGN KEY([accessscope_id])
REFERENCES [#__sdi_sys_accessscope] ([id]);

ALTER TABLE [#__sdi_property] CHECK CONSTRAINT [#__sdi_property$#__sdi_property_fk1];

ALTER TABLE [#__sdi_property]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_property$#__sdi_property_fk2] FOREIGN KEY([propertytype_id])
REFERENCES [#__sdi_sys_propertytype] ([id]);

ALTER TABLE [#__sdi_property] CHECK CONSTRAINT [#__sdi_property$#__sdi_property_fk2];

ALTER TABLE [#__sdi_pricing_profile]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_profile#__sdi_pricing_profile_fk1] FOREIGN KEY([organism_id])
REFERENCES [#__sdi_organism] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_profile] CHECK CONSTRAINT [#__sdi_pricing_profile#__sdi_pricing_profile_fk1];

ALTER TABLE [#__sdi_pricing_profile_category_pricing_rebate]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_profile_category_pricing_rebate#__sdi_pricing_profile_category_pricing_rebate_fk1] FOREIGN KEY([pricing_profile_id])
REFERENCES [#__sdi_pricing_profile] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_profile_category_pricing_rebate] CHECK CONSTRAINT [#__sdi_pricing_profile_category_pricing_rebate#__sdi_pricing_profile_category_pricing_rebate_fk1];

ALTER TABLE [#__sdi_pricing_profile_category_pricing_rebate]  WITH CHECK ADD  CONSTRAINT [#__sdi_pricing_profile_category_pricing_rebate#__sdi_pricing_profile_category_pricing_rebate_fk2] FOREIGN KEY([category_id])
REFERENCES [#__sdi_category] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_pricing_profile_category_pricing_rebate] CHECK CONSTRAINT [#__sdi_pricing_profile_category_pricing_rebate#__sdi_pricing_profile_category_pricing_rebate_fk2];

ALTER TABLE [#__sdi_propertyvalue]  WITH CHECK ADD  CONSTRAINT [#__sdi_propertyvalue$#__sdi_propertyvalue_fk1] FOREIGN KEY([property_id])
REFERENCES [#__sdi_property] ([id]);

ALTER TABLE [#__sdi_propertyvalue] CHECK CONSTRAINT [#__sdi_propertyvalue$#__sdi_propertyvalue_fk1];

ALTER TABLE [#__sdi_relation]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation$#__sdi_relation_fk1] FOREIGN KEY([parent_id])
REFERENCES [#__sdi_class] ([id]);

ALTER TABLE [#__sdi_relation] CHECK CONSTRAINT [#__sdi_relation$#__sdi_relation_fk1];

ALTER TABLE [#__sdi_relation]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation$#__sdi_relation_fk10] FOREIGN KEY([childresourcetype_id])
REFERENCES [#__sdi_resourcetype] ([id]);

ALTER TABLE [#__sdi_relation] CHECK CONSTRAINT [#__sdi_relation$#__sdi_relation_fk10];

ALTER TABLE [#__sdi_relation]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation$#__sdi_relation_fk2] FOREIGN KEY([classchild_id])
REFERENCES [#__sdi_class] ([id]);

ALTER TABLE [#__sdi_relation] CHECK CONSTRAINT [#__sdi_relation$#__sdi_relation_fk2];

ALTER TABLE [#__sdi_relation]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation$#__sdi_relation_fk3] FOREIGN KEY([attributechild_id])
REFERENCES [#__sdi_attribute] ([id]);

ALTER TABLE [#__sdi_relation] CHECK CONSTRAINT [#__sdi_relation$#__sdi_relation_fk3];

ALTER TABLE [#__sdi_relation]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_relation$#__sdi_relation_fk4] FOREIGN KEY([relationtype_id])
REFERENCES [#__sdi_sys_relationtype] ([id]);

ALTER TABLE [#__sdi_relation] CHECK CONSTRAINT [#__sdi_relation$#__sdi_relation_fk4];

ALTER TABLE [#__sdi_relation]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_relation$#__sdi_relation_fk5] FOREIGN KEY([rendertype_id])
REFERENCES [#__sdi_sys_rendertype] ([id]);

ALTER TABLE [#__sdi_relation] CHECK CONSTRAINT [#__sdi_relation$#__sdi_relation_fk5];

ALTER TABLE [#__sdi_relation]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_relation$#__sdi_relation_fk6] FOREIGN KEY([namespace_id])
REFERENCES [#__sdi_namespace] ([id]);

ALTER TABLE [#__sdi_relation] CHECK CONSTRAINT [#__sdi_relation$#__sdi_relation_fk6];

ALTER TABLE [#__sdi_relation]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation$#__sdi_relation_fk7] FOREIGN KEY([classassociation_id])
REFERENCES [#__sdi_class] ([id]);

ALTER TABLE [#__sdi_relation] CHECK CONSTRAINT [#__sdi_relation$#__sdi_relation_fk7];

ALTER TABLE [#__sdi_relation]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_relation$#__sdi_relation_fk8] FOREIGN KEY([relationscope_id])
REFERENCES [#__sdi_sys_relationscope] ([id]);

ALTER TABLE [#__sdi_relation] CHECK CONSTRAINT [#__sdi_relation$#__sdi_relation_fk8];

ALTER TABLE [#__sdi_relation]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_relation$#__sdi_relation_fk9] FOREIGN KEY([editorrelationscope_id])
REFERENCES [#__sdi_sys_relationscope] ([id]);

ALTER TABLE [#__sdi_relation] CHECK CONSTRAINT [#__sdi_relation$#__sdi_relation_fk9];

ALTER TABLE [#__sdi_relation_catalog]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation_catalog$#__sdi_relation_catalog_fk1] FOREIGN KEY([relation_id])
REFERENCES [#__sdi_relation] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_relation_catalog] CHECK CONSTRAINT [#__sdi_relation_catalog$#__sdi_relation_catalog_fk1];

ALTER TABLE [#__sdi_relation_catalog]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation_catalog$#__sdi_relation_catalog_fk2] FOREIGN KEY([catalog_id])
REFERENCES [#__sdi_catalog] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_relation_catalog] CHECK CONSTRAINT [#__sdi_relation_catalog$#__sdi_relation_catalog_fk2];

ALTER TABLE [#__sdi_relation_defaultvalue]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk1] FOREIGN KEY([relation_id])
REFERENCES [#__sdi_relation] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_relation_defaultvalue] CHECK CONSTRAINT [#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk1];

ALTER TABLE [#__sdi_relation_defaultvalue]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk2] FOREIGN KEY([attributevalue_id])
REFERENCES [#__sdi_attributevalue] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_relation_defaultvalue] CHECK CONSTRAINT [#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk2];

ALTER TABLE [#__sdi_relation_defaultvalue]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk3] FOREIGN KEY([language_id])
REFERENCES [#__sdi_language] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_relation_defaultvalue] CHECK CONSTRAINT [#__sdi_relation_defaultvalue$#__sdi_relation_defaultvalue_fk3];

ALTER TABLE [#__sdi_relation_profile]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation_profile$#__sdi_relation_profile_fk1] FOREIGN KEY([relation_id])
REFERENCES [#__sdi_relation] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_relation_profile] CHECK CONSTRAINT [#__sdi_relation_profile$#__sdi_relation_profile_fk1];

ALTER TABLE [#__sdi_relation_profile]  WITH CHECK ADD  CONSTRAINT [#__sdi_relation_profile$#__sdi_relation_profile_fk2] FOREIGN KEY([profile_id])
REFERENCES [#__sdi_profile] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_relation_profile] CHECK CONSTRAINT [#__sdi_relation_profile$#__sdi_relation_profile_fk2];

ALTER TABLE [#__sdi_resource]  WITH CHECK ADD  CONSTRAINT [#__sdi_resource$#__sdi_resource_fk1] FOREIGN KEY([organism_id])
REFERENCES [#__sdi_organism] ([id]);

ALTER TABLE [#__sdi_resource] CHECK CONSTRAINT [#__sdi_resource$#__sdi_resource_fk1];

ALTER TABLE [#__sdi_resource]  WITH CHECK ADD  CONSTRAINT [#__sdi_resource$#__sdi_resource_fk2] FOREIGN KEY([resourcetype_id])
REFERENCES [#__sdi_resourcetype] ([id]);

ALTER TABLE [#__sdi_resource] CHECK CONSTRAINT [#__sdi_resource$#__sdi_resource_fk2];

ALTER TABLE [#__sdi_resource]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_resource$#__sdi_resource_fk3] FOREIGN KEY([accessscope_id])
REFERENCES [#__sdi_sys_accessscope] ([id]);

ALTER TABLE [#__sdi_resource] CHECK CONSTRAINT [#__sdi_resource$#__sdi_resource_fk3];

ALTER TABLE [#__sdi_resourcetype]  WITH CHECK ADD  CONSTRAINT [#__sdi_resourcetype$#__sdi_resourcetype_fk1] FOREIGN KEY([profile_id])
REFERENCES [#__sdi_profile] ([id]);

ALTER TABLE [#__sdi_resourcetype] CHECK CONSTRAINT [#__sdi_resourcetype$#__sdi_resourcetype_fk1];

ALTER TABLE [#__sdi_resourcetype]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_resourcetype$#__sdi_resourcetype_fk2] FOREIGN KEY([fragmentnamespace_id])
REFERENCES [#__sdi_namespace] ([id]);

ALTER TABLE [#__sdi_resourcetype] CHECK CONSTRAINT [#__sdi_resourcetype$#__sdi_resourcetype_fk2];

ALTER TABLE [#__sdi_resourcetype]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_resourcetype$#__sdi_resourcetype_fk3] FOREIGN KEY([accessscope_id])
REFERENCES [#__sdi_sys_accessscope] ([id]);

ALTER TABLE [#__sdi_resourcetype] CHECK CONSTRAINT [#__sdi_resourcetype$#__sdi_resourcetype_fk3];

ALTER TABLE [#__sdi_resourcetypelink]  WITH CHECK ADD  CONSTRAINT [#__sdi_resourcetypelink$#__sdi_resourcetypelink_fk1] FOREIGN KEY([parent_id])
REFERENCES [#__sdi_resourcetype] ([id]);

ALTER TABLE [#__sdi_resourcetypelink] CHECK CONSTRAINT [#__sdi_resourcetypelink$#__sdi_resourcetypelink_fk1];

ALTER TABLE [#__sdi_resourcetypelink]  WITH CHECK ADD  CONSTRAINT [#__sdi_resourcetypelink$#__sdi_resourcetypelink_fk2] FOREIGN KEY([child_id])
REFERENCES [#__sdi_resourcetype] ([id]);

ALTER TABLE [#__sdi_resourcetypelink] CHECK CONSTRAINT [#__sdi_resourcetypelink$#__sdi_resourcetypelink_fk2];

ALTER TABLE [#__sdi_resourcetypelinkinheritance]  WITH CHECK ADD  CONSTRAINT [#__sdi_resourcetypelinkinheritance$#__sdi_resourcetypelinkinheritance_fk1] FOREIGN KEY([resourcetypelink_id])
REFERENCES [#__sdi_resourcetypelink] ([id]);

ALTER TABLE [#__sdi_resourcetypelinkinheritance] CHECK CONSTRAINT [#__sdi_resourcetypelinkinheritance$#__sdi_resourcetypelinkinheritance_fk1];

ALTER TABLE [#__sdi_searchcriteria]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_searchcriteria$#__sdi_searchcriteria_fk1] FOREIGN KEY([criteriatype_id])
REFERENCES [#__sdi_sys_criteriatype] ([id]);

ALTER TABLE [#__sdi_searchcriteria] CHECK CONSTRAINT [#__sdi_searchcriteria$#__sdi_searchcriteria_fk1];

ALTER TABLE [#__sdi_searchcriteria]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_searchcriteria$#__sdi_searchcriteria_fk2] FOREIGN KEY([rendertype_id])
REFERENCES [#__sdi_sys_rendertype] ([id]);

ALTER TABLE [#__sdi_searchcriteria] CHECK CONSTRAINT [#__sdi_searchcriteria$#__sdi_searchcriteria_fk2];

ALTER TABLE [#__sdi_searchcriteria]  WITH CHECK ADD  CONSTRAINT [#__sdi_searchcriteria$#__sdi_searchcriteria_fk3] FOREIGN KEY([relation_id])
REFERENCES [#__sdi_relation] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_searchcriteria] CHECK CONSTRAINT [#__sdi_searchcriteria$#__sdi_searchcriteria_fk3];

ALTER TABLE [#__sdi_searchcriteriafilter]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_searchcriteriafilter$#__sdi_searchcriteriafilter_fk1] FOREIGN KEY([searchcriteria_id])
REFERENCES [#__sdi_searchcriteria] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE;

ALTER TABLE [#__sdi_searchcriteriafilter] CHECK CONSTRAINT [#__sdi_searchcriteriafilter$#__sdi_searchcriteriafilter_fk1];

ALTER TABLE [#__sdi_searchcriteriafilter]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_searchcriteriafilter$#__sdi_searchcriteriafilter_fk2] FOREIGN KEY([language_id])
REFERENCES [#__sdi_language] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE;

ALTER TABLE [#__sdi_searchcriteriafilter] CHECK CONSTRAINT [#__sdi_searchcriteriafilter$#__sdi_searchcriteriafilter_fk2];

ALTER TABLE [#__sdi_sys_rendertype_criteriatype]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_sys_rendertype_criteriatype$#__sdi_sys_rendertype_criteriatype_fk1] FOREIGN KEY([criteriatype_id])
REFERENCES [#__sdi_sys_criteriatype] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_sys_rendertype_criteriatype] CHECK CONSTRAINT [#__sdi_sys_rendertype_criteriatype$#__sdi_sys_rendertype_criteriatype_fk1];

ALTER TABLE [#__sdi_sys_rendertype_criteriatype]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_sys_rendertype_criteriatype$#__sdi_sys_rendertype_criteriatype_fk2] FOREIGN KEY([rendertype_id])
REFERENCES [#__sdi_sys_rendertype] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_sys_rendertype_criteriatype] CHECK CONSTRAINT [#__sdi_sys_rendertype_criteriatype$#__sdi_sys_rendertype_criteriatype_fk2];

ALTER TABLE [#__sdi_sys_rendertype_stereotype]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_sys_rendertype_stereotype$#__sdi_sys_rendertype_stereotype_fk1] FOREIGN KEY([stereotype_id])
REFERENCES [#__sdi_sys_stereotype] ([id]);

ALTER TABLE [#__sdi_sys_rendertype_stereotype] CHECK CONSTRAINT [#__sdi_sys_rendertype_stereotype$#__sdi_sys_rendertype_stereotype_fk1];

ALTER TABLE [#__sdi_sys_rendertype_stereotype]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_sys_rendertype_stereotype$#__sdi_sys_rendertype_stereotype_fk2] FOREIGN KEY([rendertype_id])
REFERENCES [#__sdi_sys_rendertype] ([id]);

ALTER TABLE [#__sdi_sys_rendertype_stereotype] CHECK CONSTRAINT [#__sdi_sys_rendertype_stereotype$#__sdi_sys_rendertype_stereotype_fk2];

ALTER TABLE [#__sdi_sys_stereotype]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_sys_stereotype$#__sdi_sys_stereotype_fk1] FOREIGN KEY([entity_id])
REFERENCES [#__sdi_sys_entity] ([id]);

ALTER TABLE [#__sdi_sys_stereotype] CHECK CONSTRAINT [#__sdi_sys_stereotype$#__sdi_sys_stereotype_fk1];

ALTER TABLE [#__sdi_sys_stereotype]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_sys_stereotype$#__sdi_sys_stereotype_fk2] FOREIGN KEY([namespace_id])
REFERENCES [#__sdi_namespace] ([id]);

ALTER TABLE [#__sdi_sys_stereotype] CHECK CONSTRAINT [#__sdi_sys_stereotype$#__sdi_sys_stereotype_fk2];

ALTER TABLE [#__sdi_translation]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_translation$#__sdi_translation_fk1] FOREIGN KEY([language_id])
REFERENCES [#__sdi_language] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE;

ALTER TABLE [#__sdi_translation] CHECK CONSTRAINT [#__sdi_translation$#__sdi_translation_fk1];

CREATE NONCLUSTERED INDEX IX_NC_text1 ON [#__sdi_translation] (text1);
CREATE NONCLUSTERED INDEX IX_NC_text2 ON [#__sdi_translation] (text2);

ALTER TABLE [#__sdi_user]  WITH CHECK ADD  CONSTRAINT [#__sdi_user$#__sdi_user_fk1] FOREIGN KEY([user_id])
REFERENCES [#__users] ([id]);

ALTER TABLE [#__sdi_user] CHECK CONSTRAINT [#__sdi_user$#__sdi_user_fk1];

ALTER TABLE [#__sdi_user_role_resource]  WITH CHECK ADD  CONSTRAINT [#__sdi_user_role_resource$#__sdi_user_role_resource_fk1] FOREIGN KEY([user_id])
REFERENCES [#__sdi_user] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_user_role_resource] CHECK CONSTRAINT [#__sdi_user_role_resource$#__sdi_user_role_resource_fk1];

ALTER TABLE [#__sdi_user_role_resource]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_user_role_resource$#__sdi_user_role_resource_fk2] FOREIGN KEY([role_id])
REFERENCES [#__sdi_sys_role] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_user_role_resource] CHECK CONSTRAINT [#__sdi_user_role_resource$#__sdi_user_role_resource_fk2];

ALTER TABLE [#__sdi_user_role_resource]  WITH CHECK ADD  CONSTRAINT [#__sdi_user_role_resource$#__sdi_user_role_resource_fk3] FOREIGN KEY([resource_id])
REFERENCES [#__sdi_resource] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_user_role_resource] CHECK CONSTRAINT [#__sdi_user_role_resource$#__sdi_user_role_resource_fk3];

ALTER TABLE [#__sdi_user_role_organism]  WITH CHECK ADD  CONSTRAINT [#__sdi_user_role_organism$#__sdi_user_role_organism_fk1] FOREIGN KEY([user_id])
REFERENCES [#__sdi_user] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_user_role_organism] CHECK CONSTRAINT [#__sdi_user_role_organism$#__sdi_user_role_organism_fk1];

ALTER TABLE [#__sdi_user_role_organism]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_user_role_organism$#__sdi_user_role_organism_fk2] FOREIGN KEY([role_id])
REFERENCES [#__sdi_sys_role] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_user_role_organism] CHECK CONSTRAINT [#__sdi_user_role_organism$#__sdi_user_role_organism_fk2];

ALTER TABLE [#__sdi_user_role_organism]  WITH CHECK ADD  CONSTRAINT [#__sdi_user_role_organism$#__sdi_user_role_organism_fk3] FOREIGN KEY([organism_id])
REFERENCES [#__sdi_organism] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_user_role_organism] CHECK CONSTRAINT [#__sdi_user_role_organism$#__sdi_user_role_organism_fk3];

ALTER TABLE [#__sdi_version]  WITH CHECK ADD  CONSTRAINT [#__sdi_version$#__sdi_version_fk1] FOREIGN KEY([resource_id])
REFERENCES [#__sdi_resource] ([id]);

ALTER TABLE [#__sdi_version] CHECK CONSTRAINT [#__sdi_version$#__sdi_version_fk1];

ALTER TABLE [#__sdi_versionlink]  WITH CHECK ADD  CONSTRAINT [#__sdi_versionlink$#__sdi_versionlink_fk1] FOREIGN KEY([parent_id])
REFERENCES [#__sdi_version] ([id]);

ALTER TABLE [#__sdi_versionlink] CHECK CONSTRAINT [#__sdi_versionlink$#__sdi_versionlink_fk1];

ALTER TABLE [#__sdi_versionlink]  WITH CHECK ADD  CONSTRAINT [#__sdi_versionlink$#__sdi_versionlink_fk2] FOREIGN KEY([child_id])
REFERENCES [#__sdi_version] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_versionlink] CHECK CONSTRAINT [#__sdi_versionlink$#__sdi_versionlink_fk2];

ALTER TABLE [#__sdi_versionlink] ADD CONSTRAINT [#__sdi_versionlink$#__sdi_versionlink_uk] UNIQUE ([parent_id], [child_id]);

ALTER TABLE [#__sdi_visualization]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_visualization$#__sdi_visualization_fk1] FOREIGN KEY([accessscope_id])
REFERENCES [#__sdi_sys_accessscope] ([id]);

ALTER TABLE [#__sdi_visualization] CHECK CONSTRAINT [#__sdi_visualization$#__sdi_visualization_fk1];

ALTER TABLE [#__sdi_visualization]  WITH NOCHECK ADD  CONSTRAINT [#__sdi_visualization$#__sdi_visualization_fk2] FOREIGN KEY([version_id])
REFERENCES [#__sdi_version] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_visualization] CHECK CONSTRAINT [#__sdi_visualization$#__sdi_visualization_fk2];

ALTER TABLE [last_query_results]  WITH CHECK ADD  CONSTRAINT [last_query_results$FK_LAST_QUERY_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE;

ALTER TABLE [last_query_results] CHECK CONSTRAINT [last_query_results$FK_LAST_QUERY_QUERY];

ALTER TABLE [log_entries]  WITH CHECK ADD  CONSTRAINT [log_entries$FK_LOG_ENTRIES_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE;

ALTER TABLE [log_entries] CHECK CONSTRAINT [log_entries$FK_LOG_ENTRIES_QUERY];

ALTER TABLE [log_entries]  WITH NOCHECK ADD  CONSTRAINT [log_entries$fk_log_entries_statuses_STATUS] FOREIGN KEY([ID_STATUS])
REFERENCES [statuses] ([ID_STATUS]);

ALTER TABLE [log_entries] CHECK CONSTRAINT [log_entries$fk_log_entries_statuses_STATUS];

ALTER TABLE [overview_queries]  WITH CHECK ADD  CONSTRAINT [overview_queries$FK_OVERVIEWQUERY_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE;

ALTER TABLE [overview_queries] CHECK CONSTRAINT [overview_queries$FK_OVERVIEWQUERY_QUERY];

ALTER TABLE [overview_queries]  WITH CHECK ADD  CONSTRAINT [overview_queries$FK_OW_QUERY_PAGE] FOREIGN KEY([ID_OVERVIEW_PAGE])
REFERENCES [overview_page] ([ID_OVERVIEW_PAGE])
ON DELETE CASCADE;

ALTER TABLE [overview_queries] CHECK CONSTRAINT [overview_queries$FK_OW_QUERY_PAGE];

ALTER TABLE [periods]  WITH CHECK ADD  CONSTRAINT [periods$FK_PERIODS_SLA] FOREIGN KEY([ID_SLA])
REFERENCES [sla] ([ID_SLA])
ON DELETE CASCADE;

ALTER TABLE [periods] CHECK CONSTRAINT [periods$FK_PERIODS_SLA];

ALTER TABLE [queries]  WITH CHECK ADD  CONSTRAINT [queries$FK_QUERIES_JOB] FOREIGN KEY([ID_JOB])
REFERENCES [jobs] ([ID_JOB])
ON DELETE CASCADE;

ALTER TABLE [queries] CHECK CONSTRAINT [queries$FK_QUERIES_JOB];

ALTER TABLE [queries]  WITH NOCHECK ADD  CONSTRAINT [queries$FK_QUERIES_METHOD] FOREIGN KEY([ID_SERVICE_METHOD])
REFERENCES [service_methods] ([ID_SERVICE_METHOD]);

ALTER TABLE [queries] CHECK CONSTRAINT [queries$FK_QUERIES_METHOD];

ALTER TABLE [queries]  WITH NOCHECK ADD  CONSTRAINT [queries$FK_QUERIES_STATUS] FOREIGN KEY([ID_STATUS])
REFERENCES [statuses] ([ID_STATUS]);

ALTER TABLE [queries] CHECK CONSTRAINT [queries$FK_QUERIES_STATUS];

ALTER TABLE [query_agg_hour_log_entries]  WITH CHECK ADD  CONSTRAINT [query_agg_hour_log_entries$FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE;

ALTER TABLE [query_agg_hour_log_entries] CHECK CONSTRAINT [query_agg_hour_log_entries$FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY];

ALTER TABLE [query_agg_log_entries]  WITH CHECK ADD  CONSTRAINT [query_agg_log_entries$FK_QUERY_AGG_LOG_ENTRIES_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE;

ALTER TABLE [query_agg_log_entries] CHECK CONSTRAINT [query_agg_log_entries$FK_QUERY_AGG_LOG_ENTRIES_QUERY];

ALTER TABLE [query_params]  WITH CHECK ADD  CONSTRAINT [query_params$FK_QUERY_PARAMS_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE;

ALTER TABLE [query_params] CHECK CONSTRAINT [query_params$FK_QUERY_PARAMS_QUERY];

ALTER TABLE [query_validation_results]  WITH CHECK ADD  CONSTRAINT [query_validation_results$fk_query_validation_results_queries1] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY]);

ALTER TABLE [query_validation_results] CHECK CONSTRAINT [query_validation_results$fk_query_validation_results_queries1];

ALTER TABLE [query_validation_settings]  WITH CHECK ADD  CONSTRAINT [query_validation_settings$fk_query_validation_settings_queries1] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY]);

ALTER TABLE [query_validation_settings] CHECK CONSTRAINT [query_validation_settings$fk_query_validation_settings_queries1];

ALTER TABLE [service_types_methods]  WITH NOCHECK ADD  CONSTRAINT [service_types_methods$FK_SERVICE_TYPES_METHODS_METHOD] FOREIGN KEY([ID_SERVICE_METHOD])
REFERENCES [service_methods] ([ID_SERVICE_METHOD])
ON DELETE CASCADE;

ALTER TABLE [service_types_methods] CHECK CONSTRAINT [service_types_methods$FK_SERVICE_TYPES_METHODS_METHOD];

ALTER TABLE [service_types_methods]  WITH NOCHECK ADD  CONSTRAINT [service_types_methods$FK_SERVICE_TYPES_METHODS_TYPE] FOREIGN KEY([ID_SERVICE_TYPE])
REFERENCES [service_types] ([ID_SERVICE_TYPE])
ON DELETE CASCADE;

ALTER TABLE [service_types_methods] CHECK CONSTRAINT [service_types_methods$FK_SERVICE_TYPES_METHODS_TYPE];

ALTER TABLE [users]  WITH NOCHECK ADD  CONSTRAINT [users$FK_USERS_ROLE] FOREIGN KEY([ID_ROLE])
REFERENCES [roles] ([ID_ROLE])
ON DELETE SET NULL;

ALTER TABLE [users] CHECK CONSTRAINT [users$FK_USERS_ROLE];



ALTER TABLE [#__sdi_wmts_spatialpolicy] WITH CHECK ADD CONSTRAINT [#__sdi_wmts_spatialpolicy#__sdi_wmts_spatialpolicy_fk1] FOREIGN KEY ([spatialoperator_id])
REFERENCES [#__sdi_sys_spatialoperator] ([id]);
ALTER TABLE [#__sdi_wmts_spatialpolicy] CHECK CONSTRAINT [#__sdi_wmts_spatialpolicy#__sdi_wmts_spatialpolicy_fk1];
ALTER TABLE [#__sdi_virtualmetadata] WITH NOCHECK ADD CONSTRAINT [#__sdi_virtualmetadata$#__sdi_virtualservice_fk1] FOREIGN KEY ([virtualservice_id])
REFERENCES [#__sdi_virtualservice] ([id])
ON DELETE CASCADE;

ALTER TABLE [#__sdi_virtualmetadata] CHECK CONSTRAINT [#__sdi_virtualmetadata$#__sdi_virtualservice_fk1];

ALTER TABLE [#__sdi_virtualmetadata] WITH NOCHECK ADD CONSTRAINT [#__sdi_virtualmetadata$#__sdi_sys_country_fk2] FOREIGN KEY ([country_id])
REFERENCES [#__sdi_sys_country] ([id])
ON DELETE SET NULL;

ALTER TABLE [#__sdi_virtualmetadata] CHECK CONSTRAINT [#__sdi_virtualmetadata$#__sdi_sys_country_fk2];

ALTER TABLE [#__sdi_wmts_spatialpolicy] WITH NOCHECK ADD CONSTRAINT [#__sdi_wmts_spatialpolicy$#__sdi_sys_spatialoperator_fk1] FOREIGN KEY ([spatialoperator_id])
REFERENCES [#__sdi_sys_spatialoperator] ([id])
ON DELETE NO ACTION;

ALTER TABLE [#__sdi_wmts_spatialpolicy] CHECK CONSTRAINT [#__sdi_wmts_spatialpolicy$#__sdi_sys_spatialoperator_fk1];

ALTER TABLE [#__sdi_sys_server_serviceconnector] WITH CHECK ADD CONSTRAINT [#__sdi_sys_server_serviceconnector$#__sdi_sys_server_fk1] FOREIGN KEY ([server_id])
REFERENCES [#__sdi_sys_server] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_sys_server_serviceconnector] CHECK CONSTRAINT [#__sdi_sys_server_serviceconnector$#__sdi_sys_server_fk1];

ALTER TABLE [#__sdi_sys_server_serviceconnector] WITH CHECK ADD CONSTRAINT [#__sdi_sys_server_serviceconnector$#__sdi_sys_serviceconnector_fk2] FOREIGN KEY ([serviceconnector_id])
REFERENCES [#__sdi_sys_serviceconnector] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_sys_server_serviceconnector] CHECK CONSTRAINT [#__sdi_sys_server_serviceconnector$#__sdi_sys_serviceconnector_fk2];

ALTER TABLE [#__sdi_physicalservice] WITH CHECK ADD CONSTRAINT [#__sdi_physicalservice$#__sdi_sys_server_fk1] FOREIGN KEY ([server_id])
REFERENCES [#__sdi_sys_server] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_physicalservice] CHECK CONSTRAINT [#__sdi_physicalservice$#__sdi_sys_server_fk1];


ALTER TABLE [#__sdi_pricing_order_supplier] WITH CHECK ADD CONSTRAINT [#__sdi_pricing_order_supplier#__sdi_pricing_order_supplier_fk1] FOREIGN KEY ([pricing_order_id])
REFERENCES [#__sdi_pricing_order] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_pricing_order_supplier] CHECK CONSTRAINT [#__sdi_pricing_order_supplier#__sdi_pricing_order_supplier_fk1];
ALTER TABLE [#__sdi_pricing_order_supplier] WITH CHECK ADD CONSTRAINT [#__sdi_pricing_order_supplier#__sdi_pricing_order_supplier_fk2] FOREIGN KEY ([supplier_id])
REFERENCES [#__sdi_organism] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_pricing_order_supplier] CHECK CONSTRAINT [#__sdi_pricing_order_supplier#__sdi_pricing_order_supplier_fk2];


ALTER TABLE [#__sdi_pricing_order_supplier_product] WITH CHECK ADD CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk1] FOREIGN KEY ([pricing_order_supplier_id])
REFERENCES [#__sdi_pricing_order_supplier] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_pricing_order_supplier_product] CHECK CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk1];
ALTER TABLE [#__sdi_pricing_order_supplier_product] WITH CHECK ADD CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk2] FOREIGN KEY ([product_id])
REFERENCES [#__sdi_diffusion] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_pricing_order_supplier_product] CHECK CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk2];
ALTER TABLE [#__sdi_pricing_order_supplier_product] WITH CHECK ADD CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk3] FOREIGN KEY ([pricing_id])
REFERENCES [#__sdi_sys_pricing] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_pricing_order_supplier_product] CHECK CONSTRAINT [#__sdi_pricing_order_supplier_product#__sdi_pricing_order_supplier_product_fk3];


ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] WITH CHECK ADD CONSTRAINT [#__sdi_pricing_order_supplier_product_profile#__sdi_pricing_order_supplier_product_profile_fk1] FOREIGN KEY ([pricing_order_supplier_product_id])
REFERENCES [#__sdi_pricing_order_supplier_product] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] CHECK CONSTRAINT [#__sdi_pricing_order_supplier_product_profile#__sdi_pricing_order_supplier_product_profile_fk1];
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] WITH CHECK ADD CONSTRAINT [#__sdi_pricing_order_supplier_product_profile#__sdi_pricing_order_supplier_product_profile_fk2] FOREIGN KEY ([pricing_profile_id])
REFERENCES [#__sdi_pricing_profile] ([id])
ON DELETE NO ACTION;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] CHECK CONSTRAINT [#__sdi_pricing_order_supplier_product_profile#__sdi_pricing_order_supplier_product_profile_fk2];

ALTER TABLE #__sdi_order ADD CONSTRAINT #__sdi_order$#__sdi_user_fk5
FOREIGN KEY ([validated_by]) REFERENCES [#__sdi_user] ([id]) ON DELETE CASCADE ON UPDATE NO ACTION;

-- 4.4.4
ALTER TABLE [#__sdi_pricing_profile] ADD [apply_vat] TINYINT DEFAULT 1;

ALTER TABLE [#__sdi_organism] ADD [fixed_fee_apply_vat] TINYINT DEFAULT 1;
EXEC sp_rename '#__sdi_organism.fixed_fee_ti', 'fixed_fee_te', 'COLUMN';

EXEC sp_rename '#__sdi_pricing_order.cfg_overall_default_fee', 'cfg_overall_default_fee_te', 'COLUMN';
ALTER TABLE [sdi_sdi_pricing_order] ADD [cfg_fee_apply_vat] TINYINT DEFAULT 1;

EXEC sp_rename '#__sdi_pricing_order_supplier.cfg_fixed_fee_ti', 'cfg_fixed_fee_te', 'COLUMN';
ALTER TABLE [sdi_sdi_pricing_order_supplier] ADD [cfg_fixed_fee_apply_vat] TINYINT NOT NULL DEFAULT 1;

EXEC sp_rename '#__sdi_pricing_order_supplier_product_profile.cfg_fixed_fee', 'cfg_fixed_fee_te', 'COLUMN';

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ADD [cfg_apply_vat] TINYINT NOT NULL DEFAULT 1;

-- 4.4.5
ALTER TABLE [#__sdi_diffusion] ADD [otp] TINYINT NOT NULL DEFAULT 0;
ALTER TABLE [#__sdi_order_diffusion] ADD [otp] TEXT NULL;
ALTER TABLE [#__sdi_order_diffusion] ADD [otpchance] INT DEFAULT 0;

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] DROP CONSTRAINT [sdi_sdi_pricing_order_supplier_product_profilesdi_sdi_pricing_order_supplier_product_profile_fk2];

ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] ALTER COLUMN [pricing_profile_id] BIGINT NULL;
ALTER TABLE [#__sdi_pricing_order_supplier_product_profile] 
ADD CONSTRAINT #__sdi_pricing_order_supplier_product_profilesdi_sdi_pricing_order_supplier_product_profile_fk2 
FOREIGN KEY (pricing_profile_id) 
REFERENCES #__sdi_pricing_profile(id);
