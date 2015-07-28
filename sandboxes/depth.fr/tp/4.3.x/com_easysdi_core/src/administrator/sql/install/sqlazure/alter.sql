ALTER TABLE [actions]  WITH CHECK ADD  CONSTRAINT [actions$FK_ACTION_JOB] FOREIGN KEY([ID_JOB])
REFERENCES [jobs] ([ID_JOB])
ON DELETE CASCADE
GO
ALTER TABLE [actions] CHECK CONSTRAINT [actions$FK_ACTION_JOB]
GO
ALTER TABLE [actions]  WITH NOCHECK ADD  CONSTRAINT [actions$FK_ACTION_TYPE] FOREIGN KEY([ID_ACTION_TYPE])
REFERENCES [action_types] ([ID_ACTION_TYPE])
GO
ALTER TABLE [actions] CHECK CONSTRAINT [actions$FK_ACTION_TYPE]
GO
ALTER TABLE [alerts]  WITH CHECK ADD  CONSTRAINT [alerts$FK_ALERTS_JOB] FOREIGN KEY([ID_JOB])
REFERENCES [jobs] ([ID_JOB])
ON DELETE CASCADE
GO
ALTER TABLE [alerts] CHECK CONSTRAINT [alerts$FK_ALERTS_JOB]
GO
ALTER TABLE [alerts]  WITH NOCHECK ADD  CONSTRAINT [alerts$FK_ALERTS_NEW_STATUS] FOREIGN KEY([ID_NEW_STATUS])
REFERENCES [statuses] ([ID_STATUS])
GO
ALTER TABLE [alerts] CHECK CONSTRAINT [alerts$FK_ALERTS_NEW_STATUS]
GO
ALTER TABLE [alerts]  WITH NOCHECK ADD  CONSTRAINT [alerts$FK_ALERTS_OLD_STATUS] FOREIGN KEY([ID_OLD_STATUS])
REFERENCES [statuses] ([ID_STATUS])
GO
ALTER TABLE [alerts] CHECK CONSTRAINT [alerts$FK_ALERTS_OLD_STATUS]
GO
ALTER TABLE [job_agg_hour_log_entries]  WITH CHECK ADD  CONSTRAINT [job_agg_hour_log_entries$FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB] FOREIGN KEY([ID_JOB])
REFERENCES [jobs] ([ID_JOB])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [job_agg_hour_log_entries] CHECK CONSTRAINT [job_agg_hour_log_entries$FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB]
GO
ALTER TABLE [job_agg_log_entries]  WITH CHECK ADD  CONSTRAINT [job_agg_log_entries$FK_JOB_AGG_LOG_ENTRIES_JOB] FOREIGN KEY([ID_JOB])
REFERENCES [jobs] ([ID_JOB])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [job_agg_log_entries] CHECK CONSTRAINT [job_agg_log_entries$FK_JOB_AGG_LOG_ENTRIES_JOB]
GO
ALTER TABLE [jobs]  WITH NOCHECK ADD  CONSTRAINT [jobs$FK_JOBS_HTTP_METHOD] FOREIGN KEY([ID_HTTP_METHOD])
REFERENCES [http_methods] ([ID_HTTP_METHOD])
GO
ALTER TABLE [jobs] CHECK CONSTRAINT [jobs$FK_JOBS_HTTP_METHOD]
GO
ALTER TABLE [jobs]  WITH NOCHECK ADD  CONSTRAINT [jobs$FK_JOBS_SERVICE_TYPE] FOREIGN KEY([ID_SERVICE_TYPE])
REFERENCES [service_types] ([ID_SERVICE_TYPE])
GO
ALTER TABLE [jobs] CHECK CONSTRAINT [jobs$FK_JOBS_SERVICE_TYPE]
GO
ALTER TABLE [jobs]  WITH NOCHECK ADD  CONSTRAINT [jobs$FK_JOBS_STATUS] FOREIGN KEY([ID_STATUS])
REFERENCES [statuses] ([ID_STATUS])
GO
ALTER TABLE [jobs] CHECK CONSTRAINT [jobs$FK_JOBS_STATUS]
GO
ALTER TABLE [jos_sdi_accessscope]  WITH CHECK ADD  CONSTRAINT [jos_sdi_accessscope$jos_sdi_accessscope_fk1] FOREIGN KEY([organism_id])
REFERENCES [jos_sdi_organism] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_accessscope] CHECK CONSTRAINT [jos_sdi_accessscope$jos_sdi_accessscope_fk1]
GO
ALTER TABLE [jos_sdi_accessscope]  WITH CHECK ADD  CONSTRAINT [jos_sdi_accessscope$jos_sdi_accessscope_fk2] FOREIGN KEY([user_id])
REFERENCES [jos_sdi_user] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_accessscope] CHECK CONSTRAINT [jos_sdi_accessscope$jos_sdi_accessscope_fk2]
GO
ALTER TABLE [jos_sdi_address]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_address$jos_sdi_address_fk1] FOREIGN KEY([addresstype_id])
REFERENCES [jos_sdi_sys_addresstype] ([id])
GO
ALTER TABLE [jos_sdi_address] CHECK CONSTRAINT [jos_sdi_address$jos_sdi_address_fk1]
GO
ALTER TABLE [jos_sdi_address]  WITH CHECK ADD  CONSTRAINT [jos_sdi_address$jos_sdi_address_fk3] FOREIGN KEY([user_id])
REFERENCES [jos_sdi_user] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_address] CHECK CONSTRAINT [jos_sdi_address$jos_sdi_address_fk3]
GO
ALTER TABLE [jos_sdi_address]  WITH CHECK ADD  CONSTRAINT [jos_sdi_address$jos_sdi_address_fk4] FOREIGN KEY([organism_id])
REFERENCES [jos_sdi_organism] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_address] CHECK CONSTRAINT [jos_sdi_address$jos_sdi_address_fk4]
GO
ALTER TABLE [jos_sdi_address]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_address$jos_sdi_address_fk5] FOREIGN KEY([country_id])
REFERENCES [jos_sdi_sys_country] ([id])
GO
ALTER TABLE [jos_sdi_address] CHECK CONSTRAINT [jos_sdi_address$jos_sdi_address_fk5]
GO
ALTER TABLE [jos_sdi_application]  WITH CHECK ADD  CONSTRAINT [jos_sdi_application$jos_sdi_application_fk1] FOREIGN KEY([resource_id])
REFERENCES [jos_sdi_resource] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_application] CHECK CONSTRAINT [jos_sdi_application$jos_sdi_application_fk1]
GO
ALTER TABLE [jos_sdi_assignment]  WITH CHECK ADD  CONSTRAINT [jos_sdi_assignment$jos_sdi_assignment_fk1] FOREIGN KEY([assigned_by])
REFERENCES [jos_sdi_user] ([id])
GO
ALTER TABLE [jos_sdi_assignment] CHECK CONSTRAINT [jos_sdi_assignment$jos_sdi_assignment_fk1]
GO
ALTER TABLE [jos_sdi_assignment]  WITH CHECK ADD  CONSTRAINT [jos_sdi_assignment$jos_sdi_assignment_fk2] FOREIGN KEY([assigned_to])
REFERENCES [jos_sdi_user] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_assignment] CHECK CONSTRAINT [jos_sdi_assignment$jos_sdi_assignment_fk2]
GO
ALTER TABLE [jos_sdi_assignment]  WITH CHECK ADD  CONSTRAINT [jos_sdi_assignment$jos_sdi_assignment_fk3] FOREIGN KEY([version_id])
REFERENCES [jos_sdi_version] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_assignment] CHECK CONSTRAINT [jos_sdi_assignment$jos_sdi_assignment_fk3]
GO
ALTER TABLE [jos_sdi_attribute]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_attribute$sdi_attribute_fk1] FOREIGN KEY([namespace_id])
REFERENCES [jos_sdi_namespace] ([id])
GO
ALTER TABLE [jos_sdi_attribute] CHECK CONSTRAINT [jos_sdi_attribute$sdi_attribute_fk1]
GO
ALTER TABLE [jos_sdi_attribute]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_attribute$sdi_attribute_fk2] FOREIGN KEY([listnamespace_id])
REFERENCES [jos_sdi_namespace] ([id])
GO
ALTER TABLE [jos_sdi_attribute] CHECK CONSTRAINT [jos_sdi_attribute$sdi_attribute_fk2]
GO
ALTER TABLE [jos_sdi_attribute]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_attribute$sdi_attribute_fk3] FOREIGN KEY([stereotype_id])
REFERENCES [jos_sdi_sys_stereotype] ([id])
GO
ALTER TABLE [jos_sdi_attribute] CHECK CONSTRAINT [jos_sdi_attribute$sdi_attribute_fk3]
GO
ALTER TABLE [jos_sdi_attributevalue]  WITH CHECK ADD  CONSTRAINT [jos_sdi_attributevalue$jos_sdi_attributevalue] FOREIGN KEY([attribute_id])
REFERENCES [jos_sdi_attribute] ([id])
GO
ALTER TABLE [jos_sdi_attributevalue] CHECK CONSTRAINT [jos_sdi_attributevalue$jos_sdi_attributevalue]
GO
ALTER TABLE [jos_sdi_boundary]  WITH CHECK ADD  CONSTRAINT [jos_sdi_boundary$jos_sdi_boundary_fk1] FOREIGN KEY([parent_id])
REFERENCES [jos_sdi_boundary] ([id])
GO
ALTER TABLE [jos_sdi_boundary] CHECK CONSTRAINT [jos_sdi_boundary$jos_sdi_boundary_fk1]
GO
ALTER TABLE [jos_sdi_boundary]  WITH CHECK ADD  CONSTRAINT [jos_sdi_boundary$jos_sdi_boundary_fk2] FOREIGN KEY([category_id])
REFERENCES [jos_sdi_boundarycategory] ([id])
GO
ALTER TABLE [jos_sdi_boundary] CHECK CONSTRAINT [jos_sdi_boundary$jos_sdi_boundary_fk2]
GO
ALTER TABLE [jos_sdi_boundarycategory]  WITH CHECK ADD  CONSTRAINT [jos_sdi_boundarycategory$jos_sdi_boundarycategory_fk1] FOREIGN KEY([parent_id])
REFERENCES [jos_sdi_boundarycategory] ([id])
GO
ALTER TABLE [jos_sdi_boundarycategory] CHECK CONSTRAINT [jos_sdi_boundarycategory$jos_sdi_boundarycategory_fk1]
GO
ALTER TABLE [jos_sdi_catalog_resourcetype]  WITH CHECK ADD  CONSTRAINT [jos_sdi_catalog_resourcetype$jos_sdi_catalog_resourcetype_fk1] FOREIGN KEY([catalog_id])
REFERENCES [jos_sdi_catalog] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_catalog_resourcetype] CHECK CONSTRAINT [jos_sdi_catalog_resourcetype$jos_sdi_catalog_resourcetype_fk1]
GO
ALTER TABLE [jos_sdi_catalog_resourcetype]  WITH CHECK ADD  CONSTRAINT [jos_sdi_catalog_resourcetype$jos_sdi_catalog_resourcetype_fk2] FOREIGN KEY([resourcetype_id])
REFERENCES [jos_sdi_resourcetype] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_catalog_resourcetype] CHECK CONSTRAINT [jos_sdi_catalog_resourcetype$jos_sdi_catalog_resourcetype_fk2]
GO
ALTER TABLE [jos_sdi_catalog_searchcriteria]  WITH CHECK ADD  CONSTRAINT [jos_sdi_catalog_searchcriteria$jos_sdi_catalog_searchcriteria_fk1] FOREIGN KEY([catalog_id])
REFERENCES [jos_sdi_catalog] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_catalog_searchcriteria] CHECK CONSTRAINT [jos_sdi_catalog_searchcriteria$jos_sdi_catalog_searchcriteria_fk1]
GO
ALTER TABLE [jos_sdi_catalog_searchcriteria]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_catalog_searchcriteria$jos_sdi_catalog_searchcriteria_fk2] FOREIGN KEY([searchcriteria_id])
REFERENCES [jos_sdi_searchcriteria] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_catalog_searchcriteria] CHECK CONSTRAINT [jos_sdi_catalog_searchcriteria$jos_sdi_catalog_searchcriteria_fk2]
GO
ALTER TABLE [jos_sdi_catalog_searchcriteria]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_catalog_searchcriteria$jos_sdi_catalog_searchcriteria_fk3] FOREIGN KEY([searchtab_id])
REFERENCES [jos_sdi_sys_searchtab] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_catalog_searchcriteria] CHECK CONSTRAINT [jos_sdi_catalog_searchcriteria$jos_sdi_catalog_searchcriteria_fk3]
GO
ALTER TABLE [jos_sdi_catalog_searchsort]  WITH CHECK ADD  CONSTRAINT [jos_sdi_catalog_searchsort$jos_sdi_catalog_searchsort_fk1] FOREIGN KEY([catalog_id])
REFERENCES [jos_sdi_catalog] ([id])
GO
ALTER TABLE [jos_sdi_catalog_searchsort] CHECK CONSTRAINT [jos_sdi_catalog_searchsort$jos_sdi_catalog_searchsort_fk1]
GO
ALTER TABLE [jos_sdi_catalog_searchsort]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_catalog_searchsort$jos_sdi_catalog_searchsort_fk2] FOREIGN KEY([language_id])
REFERENCES [jos_sdi_language] ([id])
GO
ALTER TABLE [jos_sdi_catalog_searchsort] CHECK CONSTRAINT [jos_sdi_catalog_searchsort$jos_sdi_catalog_searchsort_fk2]
GO
ALTER TABLE [jos_sdi_class]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_class$jos_sdi_class_fk1] FOREIGN KEY([namespace_id])
REFERENCES [jos_sdi_namespace] ([id])
GO
ALTER TABLE [jos_sdi_class] CHECK CONSTRAINT [jos_sdi_class$jos_sdi_class_fk1]
GO
ALTER TABLE [jos_sdi_class]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_class$jos_sdi_class_fk2] FOREIGN KEY([stereotype_id])
REFERENCES [jos_sdi_sys_stereotype] ([id])
GO
ALTER TABLE [jos_sdi_class] CHECK CONSTRAINT [jos_sdi_class$jos_sdi_class_fk2]
GO
ALTER TABLE [jos_sdi_diffusion]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_diffusion$jos_sdi_diffusion_fk1] FOREIGN KEY([accessscope_id])
REFERENCES [jos_sdi_sys_accessscope] ([id])
GO
ALTER TABLE [jos_sdi_diffusion] CHECK CONSTRAINT [jos_sdi_diffusion$jos_sdi_diffusion_fk1]
GO
ALTER TABLE [jos_sdi_diffusion]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_diffusion$jos_sdi_diffusion_fk2] FOREIGN KEY([productmining_id])
REFERENCES [jos_sdi_sys_productmining] ([id])
GO
ALTER TABLE [jos_sdi_diffusion] CHECK CONSTRAINT [jos_sdi_diffusion$jos_sdi_diffusion_fk2]
GO
ALTER TABLE [jos_sdi_diffusion]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_diffusion$jos_sdi_diffusion_fk3] FOREIGN KEY([productstorage_id])
REFERENCES [jos_sdi_sys_productstorage] ([id])
GO
ALTER TABLE [jos_sdi_diffusion] CHECK CONSTRAINT [jos_sdi_diffusion$jos_sdi_diffusion_fk3]
GO
ALTER TABLE [jos_sdi_diffusion]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_diffusion$jos_sdi_diffusion_fk4] FOREIGN KEY([perimeter_id])
REFERENCES [jos_sdi_perimeter] ([id])
GO
ALTER TABLE [jos_sdi_diffusion] CHECK CONSTRAINT [jos_sdi_diffusion$jos_sdi_diffusion_fk4]
GO
ALTER TABLE [jos_sdi_diffusion]  WITH CHECK ADD  CONSTRAINT [jos_sdi_diffusion$jos_sdi_diffusion_fk5] FOREIGN KEY([version_id])
REFERENCES [jos_sdi_version] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_diffusion] CHECK CONSTRAINT [jos_sdi_diffusion$jos_sdi_diffusion_fk5]
GO
ALTER TABLE [jos_sdi_diffusion_download]  WITH CHECK ADD  CONSTRAINT [jos_sdi_diffusion_download$jos_sdi_diffusion_download_fk1] FOREIGN KEY([diffusion_id])
REFERENCES [jos_sdi_diffusion] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_diffusion_download] CHECK CONSTRAINT [jos_sdi_diffusion_download$jos_sdi_diffusion_download_fk1]
GO
ALTER TABLE [jos_sdi_diffusion_download]  WITH CHECK ADD  CONSTRAINT [jos_sdi_diffusion_download$jos_sdi_diffusion_download_fk2] FOREIGN KEY([user_id])
REFERENCES [jos_sdi_user] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_diffusion_download] CHECK CONSTRAINT [jos_sdi_diffusion_download$jos_sdi_diffusion_download_fk2]
GO
ALTER TABLE [jos_sdi_diffusion_notifieduser]  WITH CHECK ADD  CONSTRAINT [jos_sdi_diffusion_notifieduser$jos_sdi_diffusion_notifieduser_fk1] FOREIGN KEY([diffusion_id])
REFERENCES [jos_sdi_diffusion] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_diffusion_notifieduser] CHECK CONSTRAINT [jos_sdi_diffusion_notifieduser$jos_sdi_diffusion_notifieduser_fk1]
GO
ALTER TABLE [jos_sdi_diffusion_notifieduser]  WITH CHECK ADD  CONSTRAINT [jos_sdi_diffusion_notifieduser$jos_sdi_diffusion_notifieduser_fk2] FOREIGN KEY([user_id])
REFERENCES [jos_sdi_user] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_diffusion_notifieduser] CHECK CONSTRAINT [jos_sdi_diffusion_notifieduser$jos_sdi_diffusion_notifieduser_fk2]
GO
ALTER TABLE [jos_sdi_diffusion_perimeter]  WITH CHECK ADD  CONSTRAINT [jos_sdi_diffusion_perimeter$jos_sdi_diffusion_perimeter_fk1] FOREIGN KEY([diffusion_id])
REFERENCES [jos_sdi_diffusion] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_diffusion_perimeter] CHECK CONSTRAINT [jos_sdi_diffusion_perimeter$jos_sdi_diffusion_perimeter_fk1]
GO
ALTER TABLE [jos_sdi_diffusion_perimeter]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_diffusion_perimeter$jos_sdi_diffusion_perimeter_fk2] FOREIGN KEY([perimeter_id])
REFERENCES [jos_sdi_perimeter] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_diffusion_perimeter] CHECK CONSTRAINT [jos_sdi_diffusion_perimeter$jos_sdi_diffusion_perimeter_fk2]
GO
ALTER TABLE [jos_sdi_diffusion_propertyvalue]  WITH CHECK ADD  CONSTRAINT [jos_sdi_diffusion_propertyvalue$jos_sdi_diffusion_propertyvalue_fk1] FOREIGN KEY([diffusion_id])
REFERENCES [jos_sdi_diffusion] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_diffusion_propertyvalue] CHECK CONSTRAINT [jos_sdi_diffusion_propertyvalue$jos_sdi_diffusion_propertyvalue_fk1]
GO
ALTER TABLE [jos_sdi_diffusion_propertyvalue]  WITH CHECK ADD  CONSTRAINT [jos_sdi_diffusion_propertyvalue$jos_sdi_diffusion_propertyvalue_fk2] FOREIGN KEY([propertyvalue_id])
REFERENCES [jos_sdi_propertyvalue] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_diffusion_propertyvalue] CHECK CONSTRAINT [jos_sdi_diffusion_propertyvalue$jos_sdi_diffusion_propertyvalue_fk2]
GO
ALTER TABLE [jos_sdi_importref]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_importref$jos_sdi_importref_fk1] FOREIGN KEY([importtype_id])
REFERENCES [jos_sdi_sys_importtype] ([id])
GO
ALTER TABLE [jos_sdi_importref] CHECK CONSTRAINT [jos_sdi_importref$jos_sdi_importref_fk1]
GO
ALTER TABLE [jos_sdi_importref]  WITH CHECK ADD  CONSTRAINT [jos_sdi_importref$jos_sdi_importref_fk2] FOREIGN KEY([cswservice_id])
REFERENCES [jos_sdi_physicalservice] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_importref] CHECK CONSTRAINT [jos_sdi_importref$jos_sdi_importref_fk2]
GO
ALTER TABLE [jos_sdi_importref]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_importref$jos_sdi_importref_fk3] FOREIGN KEY([cswversion_id])
REFERENCES [jos_sdi_sys_serviceversion] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_importref] CHECK CONSTRAINT [jos_sdi_importref$jos_sdi_importref_fk3]
GO
ALTER TABLE [jos_sdi_layer_layergroup]  WITH CHECK ADD  CONSTRAINT [jos_sdi_layer_layergroup$jos_sdi_layer_layergroup_fk1] FOREIGN KEY([layer_id])
REFERENCES [jos_sdi_maplayer] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_layer_layergroup] CHECK CONSTRAINT [jos_sdi_layer_layergroup$jos_sdi_layer_layergroup_fk1]
GO
ALTER TABLE [jos_sdi_layer_layergroup]  WITH CHECK ADD  CONSTRAINT [jos_sdi_layer_layergroup$jos_sdi_layer_layergroup_fk2] FOREIGN KEY([group_id])
REFERENCES [jos_sdi_layergroup] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_layer_layergroup] CHECK CONSTRAINT [jos_sdi_layer_layergroup$jos_sdi_layer_layergroup_fk2]
GO
ALTER TABLE [jos_sdi_map]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_map$jos_sdi_map_fk2] FOREIGN KEY([unit_id])
REFERENCES [jos_sdi_sys_unit] ([id])
GO
ALTER TABLE [jos_sdi_map] CHECK CONSTRAINT [jos_sdi_map$jos_sdi_map_fk2]
GO
ALTER TABLE [jos_sdi_map_layergroup]  WITH CHECK ADD  CONSTRAINT [jos_sdi_map_layergroup$jos_sdi_map_layergroup_fk1] FOREIGN KEY([map_id])
REFERENCES [jos_sdi_map] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_map_layergroup] CHECK CONSTRAINT [jos_sdi_map_layergroup$jos_sdi_map_layergroup_fk1]
GO
ALTER TABLE [jos_sdi_map_layergroup]  WITH CHECK ADD  CONSTRAINT [jos_sdi_map_layergroup$jos_sdi_map_layergroup_fk2] FOREIGN KEY([group_id])
REFERENCES [jos_sdi_layergroup] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_map_layergroup] CHECK CONSTRAINT [jos_sdi_map_layergroup$jos_sdi_map_layergroup_fk2]
GO
ALTER TABLE [jos_sdi_map_physicalservice]  WITH CHECK ADD  CONSTRAINT [jos_sdi_map_physicalservice$jos_sdi_map_physicalservice_fk1] FOREIGN KEY([map_id])
REFERENCES [jos_sdi_map] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_map_physicalservice] CHECK CONSTRAINT [jos_sdi_map_physicalservice$jos_sdi_map_physicalservice_fk1]
GO
ALTER TABLE [jos_sdi_map_physicalservice]  WITH CHECK ADD  CONSTRAINT [jos_sdi_map_physicalservice$jos_sdi_map_physicalservice_fk2] FOREIGN KEY([physicalservice_id])
REFERENCES [jos_sdi_physicalservice] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_map_physicalservice] CHECK CONSTRAINT [jos_sdi_map_physicalservice$jos_sdi_map_physicalservice_fk2]
GO
ALTER TABLE [jos_sdi_map_tool]  WITH CHECK ADD  CONSTRAINT [jos_sdi_map_tool$jos_sdi_map_tool_fk1] FOREIGN KEY([map_id])
REFERENCES [jos_sdi_map] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_map_tool] CHECK CONSTRAINT [jos_sdi_map_tool$jos_sdi_map_tool_fk1]
GO
ALTER TABLE [jos_sdi_map_tool]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_map_tool$jos_sdi_map_tool_fk2] FOREIGN KEY([tool_id])
REFERENCES [jos_sdi_sys_maptool] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_map_tool] CHECK CONSTRAINT [jos_sdi_map_tool$jos_sdi_map_tool_fk2]
GO
ALTER TABLE [jos_sdi_map_virtualservice]  WITH CHECK ADD  CONSTRAINT [jos_sdi_map_virtualservice$jos_sdi_map_virtualservice_fk1] FOREIGN KEY([map_id])
REFERENCES [jos_sdi_map] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_map_virtualservice] CHECK CONSTRAINT [jos_sdi_map_virtualservice$jos_sdi_map_virtualservice_fk1]
GO
ALTER TABLE [jos_sdi_map_virtualservice]  WITH CHECK ADD  CONSTRAINT [jos_sdi_map_virtualservice$jos_sdi_map_virtualservice_fk2] FOREIGN KEY([virtualservice_id])
REFERENCES [jos_sdi_virtualservice] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_map_virtualservice] CHECK CONSTRAINT [jos_sdi_map_virtualservice$jos_sdi_map_virtualservice_fk2]
GO
ALTER TABLE [jos_sdi_maplayer]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_maplayer$jos_sdi_maplayer_fk1] FOREIGN KEY([accessscope_id])
REFERENCES [jos_sdi_sys_accessscope] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_maplayer] CHECK CONSTRAINT [jos_sdi_maplayer$jos_sdi_maplayer_fk1]
GO
ALTER TABLE [jos_sdi_metadata]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_metadata$jos_sdi_metadata_fk1] FOREIGN KEY([metadatastate_id])
REFERENCES [jos_sdi_sys_metadatastate] ([id])
GO
ALTER TABLE [jos_sdi_metadata] CHECK CONSTRAINT [jos_sdi_metadata$jos_sdi_metadata_fk1]
GO
ALTER TABLE [jos_sdi_metadata]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_metadata$jos_sdi_metadata_fk2] FOREIGN KEY([accessscope_id])
REFERENCES [jos_sdi_sys_accessscope] ([id])
GO
ALTER TABLE [jos_sdi_metadata] CHECK CONSTRAINT [jos_sdi_metadata$jos_sdi_metadata_fk2]
GO
ALTER TABLE [jos_sdi_metadata]  WITH CHECK ADD  CONSTRAINT [jos_sdi_metadata$jos_sdi_metadata_fk3] FOREIGN KEY([version_id])
REFERENCES [jos_sdi_version] ([id])
GO
ALTER TABLE [jos_sdi_metadata] CHECK CONSTRAINT [jos_sdi_metadata$jos_sdi_metadata_fk3]
GO
ALTER TABLE [jos_sdi_order]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_order$jos_sdi_order_fk1] FOREIGN KEY([ordertype_id])
REFERENCES [jos_sdi_sys_ordertype] ([id])
GO
ALTER TABLE [jos_sdi_order] CHECK CONSTRAINT [jos_sdi_order$jos_sdi_order_fk1]
GO
ALTER TABLE [jos_sdi_order]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_order$jos_sdi_order_fk2] FOREIGN KEY([orderstate_id])
REFERENCES [jos_sdi_sys_orderstate] ([id])
GO
ALTER TABLE [jos_sdi_order] CHECK CONSTRAINT [jos_sdi_order$jos_sdi_order_fk2]
GO
ALTER TABLE [jos_sdi_order]  WITH CHECK ADD  CONSTRAINT [jos_sdi_order$jos_sdi_order_fk3] FOREIGN KEY([user_id])
REFERENCES [jos_sdi_user] ([id])
GO
ALTER TABLE [jos_sdi_order] CHECK CONSTRAINT [jos_sdi_order$jos_sdi_order_fk3]
GO
ALTER TABLE [jos_sdi_order]  WITH CHECK ADD  CONSTRAINT [jos_sdi_order$jos_sdi_order_fk4] FOREIGN KEY([thirdparty_id])
REFERENCES [jos_sdi_user] ([id])
GO
ALTER TABLE [jos_sdi_order] CHECK CONSTRAINT [jos_sdi_order$jos_sdi_order_fk4]
GO
ALTER TABLE [jos_sdi_order_diffusion]  WITH CHECK ADD  CONSTRAINT [jos_sdi_order_diffusion$jos_sdi_order_diffusion_fk1] FOREIGN KEY([order_id])
REFERENCES [jos_sdi_order] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_order_diffusion] CHECK CONSTRAINT [jos_sdi_order_diffusion$jos_sdi_order_diffusion_fk1]
GO
ALTER TABLE [jos_sdi_order_diffusion]  WITH CHECK ADD  CONSTRAINT [jos_sdi_order_diffusion$jos_sdi_order_diffusion_fk2] FOREIGN KEY([diffusion_id])
REFERENCES [jos_sdi_diffusion] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_order_diffusion] CHECK CONSTRAINT [jos_sdi_order_diffusion$jos_sdi_order_diffusion_fk2]
GO
ALTER TABLE [jos_sdi_order_diffusion]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_order_diffusion$jos_sdi_order_diffusion_fk3] FOREIGN KEY([productstate_id])
REFERENCES [jos_sdi_sys_productstate] ([id])
GO
ALTER TABLE [jos_sdi_order_diffusion] CHECK CONSTRAINT [jos_sdi_order_diffusion$jos_sdi_order_diffusion_fk3]
GO
ALTER TABLE [jos_sdi_order_perimeter]  WITH CHECK ADD  CONSTRAINT [jos_sdi_order_perimeter$jos_sdi_order_perimeter_fk1] FOREIGN KEY([order_id])
REFERENCES [jos_sdi_order] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_order_perimeter] CHECK CONSTRAINT [jos_sdi_order_perimeter$jos_sdi_order_perimeter_fk1]
GO
ALTER TABLE [jos_sdi_order_perimeter]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_order_perimeter$jos_sdi_order_perimeter_fk2] FOREIGN KEY([perimeter_id])
REFERENCES [jos_sdi_perimeter] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_order_perimeter] CHECK CONSTRAINT [jos_sdi_order_perimeter$jos_sdi_order_perimeter_fk2]
GO
ALTER TABLE [jos_sdi_order_propertyvalue]  WITH CHECK ADD  CONSTRAINT [jos_sdi_order_propertyvalue$jos_sdi_order_propertyvalue_fk1] FOREIGN KEY([orderdiffusion_id])
REFERENCES [jos_sdi_order_diffusion] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_order_propertyvalue] CHECK CONSTRAINT [jos_sdi_order_propertyvalue$jos_sdi_order_propertyvalue_fk1]
GO
ALTER TABLE [jos_sdi_order_propertyvalue]  WITH CHECK ADD  CONSTRAINT [jos_sdi_order_propertyvalue$jos_sdi_order_propertyvalue_fk2] FOREIGN KEY([property_id])
REFERENCES [jos_sdi_property] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_order_propertyvalue] CHECK CONSTRAINT [jos_sdi_order_propertyvalue$jos_sdi_order_propertyvalue_fk2]
GO
ALTER TABLE [jos_sdi_order_propertyvalue]  WITH CHECK ADD  CONSTRAINT [jos_sdi_order_propertyvalue$jos_sdi_order_propertyvalue_fk3] FOREIGN KEY([propertyvalue_id])
REFERENCES [jos_sdi_propertyvalue] ([id])
GO
ALTER TABLE [jos_sdi_order_propertyvalue] CHECK CONSTRAINT [jos_sdi_order_propertyvalue$jos_sdi_order_propertyvalue_fk3]
GO
ALTER TABLE [jos_sdi_perimeter]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_perimeter$jos_sdi_perimeter_fk1] FOREIGN KEY([accessscope_id])
REFERENCES [jos_sdi_sys_accessscope] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_perimeter] CHECK CONSTRAINT [jos_sdi_perimeter$jos_sdi_perimeter_fk1]
GO
ALTER TABLE [jos_sdi_perimeter]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_perimeter$jos_sdi_perimeter_fk2] FOREIGN KEY([perimetertype_id])
REFERENCES [jos_sdi_sys_perimetertype] ([id])
GO
ALTER TABLE [jos_sdi_perimeter] CHECK CONSTRAINT [jos_sdi_perimeter$jos_sdi_perimeter_fk2]
GO
ALTER TABLE [jos_sdi_profile]  WITH CHECK ADD  CONSTRAINT [jos_sdi_profile$jos_sdi_profile_fk1] FOREIGN KEY([class_id])
REFERENCES [jos_sdi_class] ([id])
GO
ALTER TABLE [jos_sdi_profile] CHECK CONSTRAINT [jos_sdi_profile$jos_sdi_profile_fk1]
GO
ALTER TABLE [jos_sdi_property]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_property$jos_sdi_property_fk1] FOREIGN KEY([accessscope_id])
REFERENCES [jos_sdi_sys_accessscope] ([id])
GO
ALTER TABLE [jos_sdi_property] CHECK CONSTRAINT [jos_sdi_property$jos_sdi_property_fk1]
GO
ALTER TABLE [jos_sdi_property]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_property$jos_sdi_property_fk2] FOREIGN KEY([propertytype_id])
REFERENCES [jos_sdi_sys_propertytype] ([id])
GO
ALTER TABLE [jos_sdi_property] CHECK CONSTRAINT [jos_sdi_property$jos_sdi_property_fk2]
GO
ALTER TABLE [jos_sdi_propertyvalue]  WITH CHECK ADD  CONSTRAINT [jos_sdi_propertyvalue$jos_sdi_propertyvalue_fk1] FOREIGN KEY([property_id])
REFERENCES [jos_sdi_property] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_propertyvalue] CHECK CONSTRAINT [jos_sdi_propertyvalue$jos_sdi_propertyvalue_fk1]
GO
ALTER TABLE [jos_sdi_relation]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk1] FOREIGN KEY([parent_id])
REFERENCES [jos_sdi_class] ([id])
GO
ALTER TABLE [jos_sdi_relation] CHECK CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk1]
GO
ALTER TABLE [jos_sdi_relation]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk10] FOREIGN KEY([childresourcetype_id])
REFERENCES [jos_sdi_resourcetype] ([id])
GO
ALTER TABLE [jos_sdi_relation] CHECK CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk10]
GO
ALTER TABLE [jos_sdi_relation]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk2] FOREIGN KEY([classchild_id])
REFERENCES [jos_sdi_class] ([id])
GO
ALTER TABLE [jos_sdi_relation] CHECK CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk2]
GO
ALTER TABLE [jos_sdi_relation]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk3] FOREIGN KEY([attributechild_id])
REFERENCES [jos_sdi_attribute] ([id])
GO
ALTER TABLE [jos_sdi_relation] CHECK CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk3]
GO
ALTER TABLE [jos_sdi_relation]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk4] FOREIGN KEY([relationtype_id])
REFERENCES [jos_sdi_sys_relationtype] ([id])
GO
ALTER TABLE [jos_sdi_relation] CHECK CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk4]
GO
ALTER TABLE [jos_sdi_relation]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk5] FOREIGN KEY([rendertype_id])
REFERENCES [jos_sdi_sys_rendertype] ([id])
GO
ALTER TABLE [jos_sdi_relation] CHECK CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk5]
GO
ALTER TABLE [jos_sdi_relation]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk6] FOREIGN KEY([namespace_id])
REFERENCES [jos_sdi_namespace] ([id])
GO
ALTER TABLE [jos_sdi_relation] CHECK CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk6]
GO
ALTER TABLE [jos_sdi_relation]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk7] FOREIGN KEY([classassociation_id])
REFERENCES [jos_sdi_class] ([id])
GO
ALTER TABLE [jos_sdi_relation] CHECK CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk7]
GO
ALTER TABLE [jos_sdi_relation]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk8] FOREIGN KEY([relationscope_id])
REFERENCES [jos_sdi_sys_relationscope] ([id])
GO
ALTER TABLE [jos_sdi_relation] CHECK CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk8]
GO
ALTER TABLE [jos_sdi_relation]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk9] FOREIGN KEY([editorrelationscope_id])
REFERENCES [jos_sdi_sys_relationscope] ([id])
GO
ALTER TABLE [jos_sdi_relation] CHECK CONSTRAINT [jos_sdi_relation$jos_sdi_relation_fk9]
GO
ALTER TABLE [jos_sdi_relation_catalog]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation_catalog$jos_sdi_relation_catalog_fk1] FOREIGN KEY([relation_id])
REFERENCES [jos_sdi_relation] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_relation_catalog] CHECK CONSTRAINT [jos_sdi_relation_catalog$jos_sdi_relation_catalog_fk1]
GO
ALTER TABLE [jos_sdi_relation_catalog]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation_catalog$jos_sdi_relation_catalog_fk2] FOREIGN KEY([catalog_id])
REFERENCES [jos_sdi_catalog] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_relation_catalog] CHECK CONSTRAINT [jos_sdi_relation_catalog$jos_sdi_relation_catalog_fk2]
GO
ALTER TABLE [jos_sdi_relation_defaultvalue]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation_defaultvalue$jos_sdi_relation_defaultvalue_fk1] FOREIGN KEY([relation_id])
REFERENCES [jos_sdi_relation] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_relation_defaultvalue] CHECK CONSTRAINT [jos_sdi_relation_defaultvalue$jos_sdi_relation_defaultvalue_fk1]
GO
ALTER TABLE [jos_sdi_relation_defaultvalue]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation_defaultvalue$jos_sdi_relation_defaultvalue_fk2] FOREIGN KEY([attributevalue_id])
REFERENCES [jos_sdi_attributevalue] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_relation_defaultvalue] CHECK CONSTRAINT [jos_sdi_relation_defaultvalue$jos_sdi_relation_defaultvalue_fk2]
GO
ALTER TABLE [jos_sdi_relation_defaultvalue]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_relation_defaultvalue$jos_sdi_relation_defaultvalue_fk3] FOREIGN KEY([language_id])
REFERENCES [jos_sdi_language] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_relation_defaultvalue] CHECK CONSTRAINT [jos_sdi_relation_defaultvalue$jos_sdi_relation_defaultvalue_fk3]
GO
ALTER TABLE [jos_sdi_relation_profile]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation_profile$jos_sdi_relation_profile_fk1] FOREIGN KEY([relation_id])
REFERENCES [jos_sdi_relation] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_relation_profile] CHECK CONSTRAINT [jos_sdi_relation_profile$jos_sdi_relation_profile_fk1]
GO
ALTER TABLE [jos_sdi_relation_profile]  WITH CHECK ADD  CONSTRAINT [jos_sdi_relation_profile$jos_sdi_relation_profile_fk2] FOREIGN KEY([profile_id])
REFERENCES [jos_sdi_profile] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_relation_profile] CHECK CONSTRAINT [jos_sdi_relation_profile$jos_sdi_relation_profile_fk2]
GO
ALTER TABLE [jos_sdi_resource]  WITH CHECK ADD  CONSTRAINT [jos_sdi_resource$jos_sdi_resource_fk1] FOREIGN KEY([organism_id])
REFERENCES [jos_sdi_organism] ([id])
GO
ALTER TABLE [jos_sdi_resource] CHECK CONSTRAINT [jos_sdi_resource$jos_sdi_resource_fk1]
GO
ALTER TABLE [jos_sdi_resource]  WITH CHECK ADD  CONSTRAINT [jos_sdi_resource$jos_sdi_resource_fk2] FOREIGN KEY([resourcetype_id])
REFERENCES [jos_sdi_resourcetype] ([id])
GO
ALTER TABLE [jos_sdi_resource] CHECK CONSTRAINT [jos_sdi_resource$jos_sdi_resource_fk2]
GO
ALTER TABLE [jos_sdi_resource]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_resource$jos_sdi_resource_fk3] FOREIGN KEY([accessscope_id])
REFERENCES [jos_sdi_sys_accessscope] ([id])
GO
ALTER TABLE [jos_sdi_resource] CHECK CONSTRAINT [jos_sdi_resource$jos_sdi_resource_fk3]
GO
ALTER TABLE [jos_sdi_resourcetype]  WITH CHECK ADD  CONSTRAINT [jos_sdi_resourcetype$jos_sdi_resourcetype_fk1] FOREIGN KEY([profile_id])
REFERENCES [jos_sdi_profile] ([id])
GO
ALTER TABLE [jos_sdi_resourcetype] CHECK CONSTRAINT [jos_sdi_resourcetype$jos_sdi_resourcetype_fk1]
GO
ALTER TABLE [jos_sdi_resourcetype]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_resourcetype$jos_sdi_resourcetype_fk2] FOREIGN KEY([fragmentnamespace_id])
REFERENCES [jos_sdi_namespace] ([id])
GO
ALTER TABLE [jos_sdi_resourcetype] CHECK CONSTRAINT [jos_sdi_resourcetype$jos_sdi_resourcetype_fk2]
GO
ALTER TABLE [jos_sdi_resourcetype]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_resourcetype$jos_sdi_resourcetype_fk3] FOREIGN KEY([accessscope_id])
REFERENCES [jos_sdi_sys_accessscope] ([id])
GO
ALTER TABLE [jos_sdi_resourcetype] CHECK CONSTRAINT [jos_sdi_resourcetype$jos_sdi_resourcetype_fk3]
GO
ALTER TABLE [jos_sdi_resourcetypelink]  WITH CHECK ADD  CONSTRAINT [jos_sdi_resourcetypelink$jos_sdi_resourcetypelink_fk1] FOREIGN KEY([parent_id])
REFERENCES [jos_sdi_resourcetype] ([id])
GO
ALTER TABLE [jos_sdi_resourcetypelink] CHECK CONSTRAINT [jos_sdi_resourcetypelink$jos_sdi_resourcetypelink_fk1]
GO
ALTER TABLE [jos_sdi_resourcetypelink]  WITH CHECK ADD  CONSTRAINT [jos_sdi_resourcetypelink$jos_sdi_resourcetypelink_fk2] FOREIGN KEY([child_id])
REFERENCES [jos_sdi_resourcetype] ([id])
GO
ALTER TABLE [jos_sdi_resourcetypelink] CHECK CONSTRAINT [jos_sdi_resourcetypelink$jos_sdi_resourcetypelink_fk2]
GO
ALTER TABLE [jos_sdi_resourcetypelink]  WITH CHECK ADD  CONSTRAINT [jos_sdi_resourcetypelink$jos_sdi_resourcetypelink_fk3] FOREIGN KEY([class_id])
REFERENCES [jos_sdi_class] ([id])
GO
ALTER TABLE [jos_sdi_resourcetypelink] CHECK CONSTRAINT [jos_sdi_resourcetypelink$jos_sdi_resourcetypelink_fk3]
GO
ALTER TABLE [jos_sdi_resourcetypelink]  WITH CHECK ADD  CONSTRAINT [jos_sdi_resourcetypelink$jos_sdi_resourcetypelink_fk4] FOREIGN KEY([attribute_id])
REFERENCES [jos_sdi_attribute] ([id])
GO
ALTER TABLE [jos_sdi_resourcetypelink] CHECK CONSTRAINT [jos_sdi_resourcetypelink$jos_sdi_resourcetypelink_fk4]
GO
ALTER TABLE [jos_sdi_resourcetypelinkinheritance]  WITH CHECK ADD  CONSTRAINT [jos_sdi_resourcetypelinkinheritance$jos_sdi_resourcetypelinkinheritance_fk1] FOREIGN KEY([resourcetypelink_id])
REFERENCES [jos_sdi_resourcetypelink] ([id])
GO
ALTER TABLE [jos_sdi_resourcetypelinkinheritance] CHECK CONSTRAINT [jos_sdi_resourcetypelinkinheritance$jos_sdi_resourcetypelinkinheritance_fk1]
GO
ALTER TABLE [jos_sdi_searchcriteria]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_searchcriteria$jos_sdi_searchcriteria_fk1] FOREIGN KEY([criteriatype_id])
REFERENCES [jos_sdi_sys_criteriatype] ([id])
GO
ALTER TABLE [jos_sdi_searchcriteria] CHECK CONSTRAINT [jos_sdi_searchcriteria$jos_sdi_searchcriteria_fk1]
GO
ALTER TABLE [jos_sdi_searchcriteria]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_searchcriteria$jos_sdi_searchcriteria_fk2] FOREIGN KEY([rendertype_id])
REFERENCES [jos_sdi_sys_rendertype] ([id])
GO
ALTER TABLE [jos_sdi_searchcriteria] CHECK CONSTRAINT [jos_sdi_searchcriteria$jos_sdi_searchcriteria_fk2]
GO
ALTER TABLE [jos_sdi_searchcriteria]  WITH CHECK ADD  CONSTRAINT [jos_sdi_searchcriteria$jos_sdi_searchcriteria_fk3] FOREIGN KEY([relation_id])
REFERENCES [jos_sdi_relation] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_searchcriteria] CHECK CONSTRAINT [jos_sdi_searchcriteria$jos_sdi_searchcriteria_fk3]
GO
ALTER TABLE [jos_sdi_searchcriteriafilter]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_searchcriteriafilter$jos_sdi_searchcriteriafilter_fk1] FOREIGN KEY([searchcriteria_id])
REFERENCES [jos_sdi_searchcriteria] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_searchcriteriafilter] CHECK CONSTRAINT [jos_sdi_searchcriteriafilter$jos_sdi_searchcriteriafilter_fk1]
GO
ALTER TABLE [jos_sdi_searchcriteriafilter]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_searchcriteriafilter$jos_sdi_searchcriteriafilter_fk2] FOREIGN KEY([language_id])
REFERENCES [jos_sdi_language] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_searchcriteriafilter] CHECK CONSTRAINT [jos_sdi_searchcriteriafilter$jos_sdi_searchcriteriafilter_fk2]
GO
ALTER TABLE [jos_sdi_sys_rendertype_criteriatype]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_sys_rendertype_criteriatype$jos_sdi_sys_rendertype_criteriatype_fk1] FOREIGN KEY([criteriatype_id])
REFERENCES [jos_sdi_sys_criteriatype] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_sys_rendertype_criteriatype] CHECK CONSTRAINT [jos_sdi_sys_rendertype_criteriatype$jos_sdi_sys_rendertype_criteriatype_fk1]
GO
ALTER TABLE [jos_sdi_sys_rendertype_criteriatype]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_sys_rendertype_criteriatype$jos_sdi_sys_rendertype_criteriatype_fk2] FOREIGN KEY([rendertype_id])
REFERENCES [jos_sdi_sys_rendertype] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_sys_rendertype_criteriatype] CHECK CONSTRAINT [jos_sdi_sys_rendertype_criteriatype$jos_sdi_sys_rendertype_criteriatype_fk2]
GO
ALTER TABLE [jos_sdi_sys_rendertype_stereotype]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_sys_rendertype_stereotype$jos_sdi_sys_rendertype_stereotype_fk1] FOREIGN KEY([stereotype_id])
REFERENCES [jos_sdi_sys_stereotype] ([id])
GO
ALTER TABLE [jos_sdi_sys_rendertype_stereotype] CHECK CONSTRAINT [jos_sdi_sys_rendertype_stereotype$jos_sdi_sys_rendertype_stereotype_fk1]
GO
ALTER TABLE [jos_sdi_sys_rendertype_stereotype]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_sys_rendertype_stereotype$jos_sdi_sys_rendertype_stereotype_fk2] FOREIGN KEY([rendertype_id])
REFERENCES [jos_sdi_sys_rendertype] ([id])
GO
ALTER TABLE [jos_sdi_sys_rendertype_stereotype] CHECK CONSTRAINT [jos_sdi_sys_rendertype_stereotype$jos_sdi_sys_rendertype_stereotype_fk2]
GO
ALTER TABLE [jos_sdi_sys_stereotype]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_sys_stereotype$jos_sdi_sys_stereotype_fk1] FOREIGN KEY([entity_id])
REFERENCES [jos_sdi_sys_entity] ([id])
GO
ALTER TABLE [jos_sdi_sys_stereotype] CHECK CONSTRAINT [jos_sdi_sys_stereotype$jos_sdi_sys_stereotype_fk1]
GO
ALTER TABLE [jos_sdi_sys_stereotype]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_sys_stereotype$jos_sdi_sys_stereotype_fk2] FOREIGN KEY([namespace_id])
REFERENCES [jos_sdi_namespace] ([id])
GO
ALTER TABLE [jos_sdi_sys_stereotype] CHECK CONSTRAINT [jos_sdi_sys_stereotype$jos_sdi_sys_stereotype_fk2]
GO
ALTER TABLE [jos_sdi_translation]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_translation$jos_sdi_translation_fk1] FOREIGN KEY([language_id])
REFERENCES [jos_sdi_language] ([id])
GO
ALTER TABLE [jos_sdi_translation] CHECK CONSTRAINT [jos_sdi_translation$jos_sdi_translation_fk1]
GO
ALTER TABLE [jos_sdi_user]  WITH CHECK ADD  CONSTRAINT [jos_sdi_user$jos_sdi_user_fk1] FOREIGN KEY([user_id])
REFERENCES [jos_users] ([id])
GO
ALTER TABLE [jos_sdi_user] CHECK CONSTRAINT [jos_sdi_user$jos_sdi_user_fk1]
GO
ALTER TABLE [jos_sdi_user_role_resource]  WITH CHECK ADD  CONSTRAINT [jos_sdi_user_role_resource$jos_sdi_user_role_resource_fk1] FOREIGN KEY([user_id])
REFERENCES [jos_sdi_user] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_user_role_resource] CHECK CONSTRAINT [jos_sdi_user_role_resource$jos_sdi_user_role_resource_fk1]
GO
ALTER TABLE [jos_sdi_user_role_resource]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_user_role_resource$jos_sdi_user_role_resource_fk2] FOREIGN KEY([role_id])
REFERENCES [jos_sdi_sys_role] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_user_role_resource] CHECK CONSTRAINT [jos_sdi_user_role_resource$jos_sdi_user_role_resource_fk2]
GO
ALTER TABLE [jos_sdi_user_role_resource]  WITH CHECK ADD  CONSTRAINT [jos_sdi_user_role_resource$jos_sdi_user_role_resource_fk3] FOREIGN KEY([resource_id])
REFERENCES [jos_sdi_resource] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_user_role_resource] CHECK CONSTRAINT [jos_sdi_user_role_resource$jos_sdi_user_role_resource_fk3]
GO
ALTER TABLE [jos_sdi_version]  WITH CHECK ADD  CONSTRAINT [jos_sdi_version$jos_sdi_version_fk1] FOREIGN KEY([resource_id])
REFERENCES [jos_sdi_resource] ([id])
GO
ALTER TABLE [jos_sdi_version] CHECK CONSTRAINT [jos_sdi_version$jos_sdi_version_fk1]
GO
ALTER TABLE [jos_sdi_versionlink]  WITH CHECK ADD  CONSTRAINT [jos_sdi_versionlink$jos_sdi_versionlink_fk1] FOREIGN KEY([parent_id])
REFERENCES [jos_sdi_version] ([id])
GO
ALTER TABLE [jos_sdi_versionlink] CHECK CONSTRAINT [jos_sdi_versionlink$jos_sdi_versionlink_fk1]
GO
ALTER TABLE [jos_sdi_versionlink]  WITH CHECK ADD  CONSTRAINT [jos_sdi_versionlink$jos_sdi_versionlink_fk2] FOREIGN KEY([child_id])
REFERENCES [jos_sdi_version] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [jos_sdi_versionlink] CHECK CONSTRAINT [jos_sdi_versionlink$jos_sdi_versionlink_fk2]
GO
ALTER TABLE [jos_sdi_visualization]  WITH NOCHECK ADD  CONSTRAINT [jos_sdi_visualization$jos_sdi_visualization_fk1] FOREIGN KEY([accessscope_id])
REFERENCES [jos_sdi_sys_accessscope] ([id])
GO
ALTER TABLE [jos_sdi_visualization] CHECK CONSTRAINT [jos_sdi_visualization$jos_sdi_visualization_fk1]
GO
ALTER TABLE [last_query_results]  WITH CHECK ADD  CONSTRAINT [last_query_results$FK_LAST_QUERY_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE
GO
ALTER TABLE [last_query_results] CHECK CONSTRAINT [last_query_results$FK_LAST_QUERY_QUERY]
GO
ALTER TABLE [log_entries]  WITH CHECK ADD  CONSTRAINT [log_entries$FK_LOG_ENTRIES_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE
GO
ALTER TABLE [log_entries] CHECK CONSTRAINT [log_entries$FK_LOG_ENTRIES_QUERY]
GO
ALTER TABLE [log_entries]  WITH NOCHECK ADD  CONSTRAINT [log_entries$fk_log_entries_statuses_STATUS] FOREIGN KEY([ID_STATUS])
REFERENCES [statuses] ([ID_STATUS])
GO
ALTER TABLE [log_entries] CHECK CONSTRAINT [log_entries$fk_log_entries_statuses_STATUS]
GO
ALTER TABLE [overview_queries]  WITH CHECK ADD  CONSTRAINT [overview_queries$FK_OVERVIEWQUERY_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE
GO
ALTER TABLE [overview_queries] CHECK CONSTRAINT [overview_queries$FK_OVERVIEWQUERY_QUERY]
GO
ALTER TABLE [overview_queries]  WITH CHECK ADD  CONSTRAINT [overview_queries$FK_OW_QUERY_PAGE] FOREIGN KEY([ID_OVERVIEW_PAGE])
REFERENCES [overview_page] ([ID_OVERVIEW_PAGE])
ON DELETE CASCADE
GO
ALTER TABLE [overview_queries] CHECK CONSTRAINT [overview_queries$FK_OW_QUERY_PAGE]
GO
ALTER TABLE [periods]  WITH CHECK ADD  CONSTRAINT [periods$FK_PERIODS_SLA] FOREIGN KEY([ID_SLA])
REFERENCES [sla] ([ID_SLA])
ON DELETE CASCADE
GO
ALTER TABLE [periods] CHECK CONSTRAINT [periods$FK_PERIODS_SLA]
GO
ALTER TABLE [queries]  WITH CHECK ADD  CONSTRAINT [queries$FK_QUERIES_JOB] FOREIGN KEY([ID_JOB])
REFERENCES [jobs] ([ID_JOB])
ON DELETE CASCADE
GO
ALTER TABLE [queries] CHECK CONSTRAINT [queries$FK_QUERIES_JOB]
GO
ALTER TABLE [queries]  WITH NOCHECK ADD  CONSTRAINT [queries$FK_QUERIES_METHOD] FOREIGN KEY([ID_SERVICE_METHOD])
REFERENCES [service_methods] ([ID_SERVICE_METHOD])
GO
ALTER TABLE [queries] CHECK CONSTRAINT [queries$FK_QUERIES_METHOD]
GO
ALTER TABLE [queries]  WITH NOCHECK ADD  CONSTRAINT [queries$FK_QUERIES_STATUS] FOREIGN KEY([ID_STATUS])
REFERENCES [statuses] ([ID_STATUS])
GO
ALTER TABLE [queries] CHECK CONSTRAINT [queries$FK_QUERIES_STATUS]
GO
ALTER TABLE [query_agg_hour_log_entries]  WITH CHECK ADD  CONSTRAINT [query_agg_hour_log_entries$FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE
GO
ALTER TABLE [query_agg_hour_log_entries] CHECK CONSTRAINT [query_agg_hour_log_entries$FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY]
GO
ALTER TABLE [query_agg_log_entries]  WITH CHECK ADD  CONSTRAINT [query_agg_log_entries$FK_QUERY_AGG_LOG_ENTRIES_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE
GO
ALTER TABLE [query_agg_log_entries] CHECK CONSTRAINT [query_agg_log_entries$FK_QUERY_AGG_LOG_ENTRIES_QUERY]
GO
ALTER TABLE [query_params]  WITH CHECK ADD  CONSTRAINT [query_params$FK_QUERY_PARAMS_QUERY] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
ON DELETE CASCADE
GO
ALTER TABLE [query_params] CHECK CONSTRAINT [query_params$FK_QUERY_PARAMS_QUERY]
GO
ALTER TABLE [query_validation_results]  WITH CHECK ADD  CONSTRAINT [query_validation_results$fk_query_validation_results_queries1] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
GO
ALTER TABLE [query_validation_results] CHECK CONSTRAINT [query_validation_results$fk_query_validation_results_queries1]
GO
ALTER TABLE [query_validation_settings]  WITH CHECK ADD  CONSTRAINT [query_validation_settings$fk_query_validation_settings_queries1] FOREIGN KEY([ID_QUERY])
REFERENCES [queries] ([ID_QUERY])
GO
ALTER TABLE [query_validation_settings] CHECK CONSTRAINT [query_validation_settings$fk_query_validation_settings_queries1]
GO
ALTER TABLE [service_types_methods]  WITH NOCHECK ADD  CONSTRAINT [service_types_methods$FK_SERVICE_TYPES_METHODS_METHOD] FOREIGN KEY([ID_SERVICE_METHOD])
REFERENCES [service_methods] ([ID_SERVICE_METHOD])
ON DELETE CASCADE
GO
ALTER TABLE [service_types_methods] CHECK CONSTRAINT [service_types_methods$FK_SERVICE_TYPES_METHODS_METHOD]
GO
ALTER TABLE [service_types_methods]  WITH NOCHECK ADD  CONSTRAINT [service_types_methods$FK_SERVICE_TYPES_METHODS_TYPE] FOREIGN KEY([ID_SERVICE_TYPE])
REFERENCES [service_types] ([ID_SERVICE_TYPE])
ON DELETE CASCADE
GO
ALTER TABLE [service_types_methods] CHECK CONSTRAINT [service_types_methods$FK_SERVICE_TYPES_METHODS_TYPE]
GO
ALTER TABLE [users]  WITH NOCHECK ADD  CONSTRAINT [users$FK_USERS_ROLE] FOREIGN KEY([ID_ROLE])
REFERENCES [roles] ([ID_ROLE])
ON DELETE SET NULL
GO
ALTER TABLE [users] CHECK CONSTRAINT [users$FK_USERS_ROLE]
GO