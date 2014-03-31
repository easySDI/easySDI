ALTER TABLE `#__sdi_sys_servicecompliance`
ADD CONSTRAINT `#__sdi_sys_servicecompliance_fk1` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_sys_servicecompliance`
ADD CONSTRAINT `#__sdi_sys_servicecompliance_fk2` FOREIGN KEY (`serviceversion_id`) REFERENCES `#__sdi_sys_serviceversion` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_sys_servicecon_authenticationcon`
ADD CONSTRAINT `#__sdi_sys_servicecon_authenticationcon_fk1` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_sys_servicecon_authenticationcon`
ADD CONSTRAINT `#__sdi_sys_servicecon_authenticationcon_fk2` FOREIGN KEY (`authenticationconnector_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_sys_operationcompliance`
ADD CONSTRAINT `#__sdi_sys_operationcompliance_fk1` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`);

ALTER TABLE `#__sdi_sys_operationcompliance`
ADD CONSTRAINT `#__sdi_sys_operationcompliance_fk2` FOREIGN KEY (`serviceoperation_id`) REFERENCES `#__sdi_sys_serviceoperation` (`id`);

ALTER TABLE `#__sdi_sys_authenticationconnector`
ADD CONSTRAINT `#__sdi_sys_authenticationconnector_fk1` FOREIGN KEY (`authenticationlevel_id`) REFERENCES `#__sdi_sys_authenticationlevel` (`id`);

-- com_easysdi_contact

ALTER TABLE `#__sdi_user`
ADD CONSTRAINT `#__sdi_user_fk1` FOREIGN KEY (`user_id`) REFERENCES `#__users` (`id`) ;

ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk1` FOREIGN KEY (`addresstype_id`) REFERENCES `#__sdi_sys_addresstype` (`id`) ;

ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk3` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk4` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk5` FOREIGN KEY (`country_id`) REFERENCES `#__sdi_sys_country` (`id`) ;


-- com_easysdi_map

ALTER TABLE `#__sdi_map_tool`
ADD CONSTRAINT `#__sdi_map_tool_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_map_tool`
ADD CONSTRAINT `#__sdi_map_tool_fk2` FOREIGN KEY (`tool_id`) REFERENCES `#__sdi_sys_maptool` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_map_layergroup`
ADD CONSTRAINT `#__sdi_map_layergroup_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_map_layergroup`
ADD CONSTRAINT `#__sdi_map_layergroup_fk2` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_layergroup` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_map_physicalservice`
ADD CONSTRAINT `#__sdi_map_physicalservice_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_map_physicalservice`
ADD CONSTRAINT `#__sdi_map_physicalservice_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_map_virtualservice`
ADD CONSTRAINT `#__sdi_map_virtualservice_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_map_virtualservice`
ADD CONSTRAINT `#__sdi_map_virtualservice_fk2` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_map`
ADD CONSTRAINT `#__sdi_map_fk2` FOREIGN KEY (`unit_id`) REFERENCES `#__sdi_sys_unit` (`id`);

ALTER TABLE `#__sdi_layer_layergroup`
ADD CONSTRAINT `#__sdi_layer_layergroup_fk1` FOREIGN KEY (`layer_id`) REFERENCES `#__sdi_maplayer` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_layer_layergroup`
ADD CONSTRAINT `#__sdi_layer_layergroup_fk2` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_layergroup` (`id`) ON DELETE CASCADE;


-- com_easysdi_service


-- Google Bing and OSM layer
ALTER TABLE `#__sdi_layer`
ADD CONSTRAINT `#__sdi_layer_fk1` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ;

-- Physical service
ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk1` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`) ;

ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk2` FOREIGN KEY (`resourceauthentication_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`) ;

ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk3` FOREIGN KEY (`serviceauthentication_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`);

ALTER TABLE `#__sdi_physicalservice_servicecompliance`
ADD CONSTRAINT `#__sdi_physicalservice_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_physicalservice_servicecompliance`
ADD CONSTRAINT `#__sdi_physicalservice_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk4` FOREIGN KEY (`servicescope_id`) REFERENCES `#__sdi_sys_servicescope` (`id`) ;

ALTER TABLE `#__sdi_physicalservice_organism`
ADD CONSTRAINT `#__sdi_physicalservice_organism_fk1` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_physicalservice_organism`
ADD CONSTRAINT `#__sdi_physicalservice_organism_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE;

-- Virtual Service
ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk1` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`) ;

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk2` FOREIGN KEY (`proxytype_id`) REFERENCES `#__sdi_sys_proxytype` (`id`) ;

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk3` FOREIGN KEY (`exceptionlevel_id`) REFERENCES `#__sdi_sys_exceptionlevel` (`id`) ;

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk4` FOREIGN KEY (`loglevel_id`) REFERENCES `#__sdi_sys_loglevel` (`id`) ;

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk5` FOREIGN KEY (`logroll_id`) REFERENCES `#__sdi_sys_logroll` (`id`) ;

ALTER TABLE `#__sdi_virtualmetadata`
ADD CONSTRAINT `#__sdi_virtualmetadata_fk1` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_virtualmetadata`
ADD CONSTRAINT `#__sdi_virtualmetadata_fk2` FOREIGN KEY (`country_id`) REFERENCES `#__sdi_sys_country` (`id`) ;

ALTER TABLE `#__sdi_virtual_physical`
ADD CONSTRAINT `#__sdi_virtual_physical_fk1` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_virtual_physical`
ADD CONSTRAINT `#__sdi_virtual_physical_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_virtualservice_servicecompliance`
ADD CONSTRAINT `#__sdi_virtualservice_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_virtualservice_servicecompliance`
ADD CONSTRAINT `#__sdi_virtualservice_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk6` FOREIGN KEY (`servicescope_id`) REFERENCES `#__sdi_sys_servicescope` (`id`) ;

ALTER TABLE `#__sdi_virtualservice_organism`
ADD CONSTRAINT `#__sdi_virtualservice_organism_fk1` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_virtualservice_organism`
ADD CONSTRAINT `#__sdi_virtualservice_organism_fk2` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE;

-- Policy
ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk1` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk2` FOREIGN KEY (`accessscope_id`) REFERENCES `#__sdi_sys_accessscope` (`id`) ;

ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk7` FOREIGN KEY (`csw_version_id`) REFERENCES `#__sdi_sys_metadataversion` (`id`) ;

ALTER TABLE `#__sdi_allowedoperation`
ADD CONSTRAINT `#__sdi_allowedoperationy_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_allowedoperation`
ADD CONSTRAINT `#__sdi_allowedoperationy_fk2` FOREIGN KEY (`serviceoperation_id`) REFERENCES `#__sdi_sys_serviceoperation` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_metadatastate`
ADD CONSTRAINT `#__sdi_policy_metadatastate_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_metadatastate`
ADD CONSTRAINT `#__sdi_policy_metadatastate_fk2` FOREIGN KEY (`metadatastate_id`) REFERENCES `#__sdi_sys_metadatastate` (`id`)  ON DELETE CASCADE;

ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_excludedattribute`
ADD CONSTRAINT `#__sdi_excludedattribute_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_wmtslayer_policy`
ADD CONSTRAINT `#__sdi_wmtslayer_policy_fk1` FOREIGN KEY (`physicalservicepolicy_id`) REFERENCES `#__sdi_physicalservice_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_tilematrixset_policy`
ADD CONSTRAINT `#__sdi_tilematrixset_policy_fk1` FOREIGN KEY (`wmtslayerpolicy_id`) REFERENCES `#__sdi_wmtslayer_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_tilematrix_policy`
ADD CONSTRAINT `#__sdi_tilematrix_policy_fk1` FOREIGN KEY (`tilematrixsetpolicy_id`) REFERENCES `#__sdi_tilematrixset_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_wmslayer_policy`
ADD CONSTRAINT `#__sdi_wmslayer_policy_fk1` FOREIGN KEY (`physicalservicepolicy_id`) REFERENCES `#__sdi_physicalservice_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_featuretype_policy`
ADD CONSTRAINT `#__sdi_featuretype_policy_fk1` FOREIGN KEY (`physicalservicepolicy_id`) REFERENCES `#__sdi_physicalservice_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_includedattribute`
ADD CONSTRAINT `#__sdi_includedattribute_fk1` FOREIGN KEY (`featuretypepolicy_id`) REFERENCES `#__sdi_featuretype_policy` (`id`) ON DELETE CASCADE;

-- Policy authorized access
ALTER TABLE `#__sdi_policy_organism`
ADD CONSTRAINT `#__sdi_policy_organism_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_organism`
ADD CONSTRAINT `#__sdi_policy_organism_fk2` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_user`
ADD CONSTRAINT `#__sdi_policy_user_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_user`
ADD CONSTRAINT `#__sdi_policy_user_fk2` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`) ON DELETE CASCADE;

-- Spatial Policy
ALTER TABLE `#__sdi_wmslayer_policy`
ADD CONSTRAINT `#__sdi_wmslayer_policy_fk2` FOREIGN KEY (`spatialpolicy_id`) REFERENCES `#__sdi_wms_spatialpolicy` (`id`);

ALTER TABLE `#__sdi_wmtslayer_policy`
ADD CONSTRAINT `#__sdi_wmtslayer_policy_fk2` FOREIGN KEY (`spatialpolicy_id`) REFERENCES `#__sdi_wmts_spatialpolicy` (`id`) ;

ALTER TABLE `#__sdi_wmts_spatialpolicy`
ADD CONSTRAINT `#__sdi_wmts_spatialpolicy_fk1` FOREIGN KEY (`spatialoperator_id`) REFERENCES `#__sdi_sys_spatialoperator` (`id`) ;

ALTER TABLE `#__sdi_featuretype_policy`
ADD CONSTRAINT `#__sdi_featuretype_policy_fk2` FOREIGN KEY (`spatialpolicy_id`) REFERENCES `#__sdi_wfs_spatialpolicy` (`id`) ;

ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk3` FOREIGN KEY (`csw_spatialpolicy_id`) REFERENCES `#__sdi_csw_spatialpolicy` (`id`) ;

ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk4` FOREIGN KEY (`wms_spatialpolicy_id`) REFERENCES `#__sdi_wms_spatialpolicy` (`id`) ;

ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk5` FOREIGN KEY (`wfs_spatialpolicy_id`) REFERENCES `#__sdi_wfs_spatialpolicy` (`id`) ;

ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk6` FOREIGN KEY (`wmts_spatialpolicy_id`) REFERENCES `#__sdi_wmts_spatialpolicy` (`id`) ;

ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk3` FOREIGN KEY (`csw_spatialpolicy_id`) REFERENCES `#__sdi_csw_spatialpolicy` (`id`) ;

ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk4` FOREIGN KEY (`wms_spatialpolicy_id`) REFERENCES `#__sdi_wms_spatialpolicy` (`id`) ;

ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk5` FOREIGN KEY (`wfs_spatialpolicy_id`) REFERENCES `#__sdi_wfs_spatialpolicy` (`id`) ;

ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk6` FOREIGN KEY (`wmts_spatialpolicy_id`) REFERENCES `#__sdi_wmts_spatialpolicy` (`id`) ;


ALTER TABLE `#__sdi_policy_resourcetype`
ADD CONSTRAINT `#__sdi_policy_resourcetype_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_resourcetype`
ADD CONSTRAINT `#__sdi_policy_resourcetype_fk2` FOREIGN KEY (`resourcetype_id`) REFERENCES `#__sdi_resourcetype` (`id`)  ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_visibility`
ADD CONSTRAINT `#__sdi_policy_visibility_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_visibility`
ADD CONSTRAINT `#__sdi_policy_visibility_fk2` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`)  ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_visibility`
ADD CONSTRAINT `#__sdi_policy_visibility_fk3` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`)  ON DELETE CASCADE;

