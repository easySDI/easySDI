CALL drop_foreign_key('sdi_sys_servicecompliance', 'sdi_sys_servicecompliance_fk1');
ALTER TABLE `#__sdi_sys_servicecompliance`
ADD CONSTRAINT `#__sdi_sys_servicecompliance_fk1` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_sys_servicecompliance', 'sdi_sys_servicecompliance_fk2');
ALTER TABLE `#__sdi_sys_servicecompliance`
ADD CONSTRAINT `#__sdi_sys_servicecompliance_fk2` FOREIGN KEY (`serviceversion_id`) REFERENCES `#__sdi_sys_serviceversion` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_sys_servicecon_authenticationcon', 'sdi_sys_servicecon_authenticationcon_fk1');
ALTER TABLE `#__sdi_sys_servicecon_authenticationcon`
ADD CONSTRAINT `#__sdi_sys_servicecon_authenticationcon_fk1` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_sys_servicecon_authenticationcon', 'sdi_sys_servicecon_authenticationcon_fk2');
ALTER TABLE `#__sdi_sys_servicecon_authenticationcon`
ADD CONSTRAINT `#__sdi_sys_servicecon_authenticationcon_fk2` FOREIGN KEY (`authenticationconnector_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_sys_operationcompliance', 'sdi_sys_operationcompliance_fk1');
ALTER TABLE `#__sdi_sys_operationcompliance`
ADD CONSTRAINT `#__sdi_sys_operationcompliance_fk1` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`);

CALL drop_foreign_key('sdi_sys_operationcompliance', 'sdi_sys_operationcompliance_fk2');
ALTER TABLE `#__sdi_sys_operationcompliance`
ADD CONSTRAINT `#__sdi_sys_operationcompliance_fk2` FOREIGN KEY (`serviceoperation_id`) REFERENCES `#__sdi_sys_serviceoperation` (`id`);

CALL drop_foreign_key('sdi_sys_authenticationconnector', 'sdi_sys_authenticationconnector_fk1');
ALTER TABLE `#__sdi_sys_authenticationconnector`
ADD CONSTRAINT `#__sdi_sys_authenticationconnector_fk1` FOREIGN KEY (`authenticationlevel_id`) REFERENCES `#__sdi_sys_authenticationlevel` (`id`);

ALTER TABLE `#__sdi_translation` ADD INDEX `element_guid` (`element_guid`);
ALTER TABLE `#__sdi_translation` ADD INDEX `text1` (`text1`);
ALTER TABLE `#__sdi_translation` ADD INDEX `text2` (`text2`(255));

CALL drop_foreign_key('sdi_sys_rendertype_criteriatype', 'sdi_sys_rendertype_criteriatype_fk1');
ALTER TABLE `#__sdi_sys_rendertype_criteriatype`
ADD CONSTRAINT `#__sdi_sys_rendertype_criteriatype_fk1` FOREIGN KEY (`criteriatype_id`) REFERENCES `#__sdi_sys_criteriatype` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_sys_rendertype_criteriatype', 'sdi_sys_rendertype_criteriatype_fk2');
ALTER TABLE `#__sdi_sys_rendertype_criteriatype`
ADD CONSTRAINT `#__sdi_sys_rendertype_criteriatype_fk2` FOREIGN KEY (`rendertype_id`) REFERENCES `#__sdi_sys_rendertype` (`id`) ON DELETE CASCADE;

-- com_easysdi_contact

CALL drop_foreign_key('sdi_user', 'sdi_user_fk1');
ALTER TABLE `#__sdi_user`
ADD CONSTRAINT `#__sdi_user_fk1` FOREIGN KEY (`user_id`) REFERENCES `#__users` (`id`) ;

CALL drop_foreign_key('sdi_address', 'sdi_address_fk1');
ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk1` FOREIGN KEY (`addresstype_id`) REFERENCES `#__sdi_sys_addresstype` (`id`) ;

CALL drop_foreign_key('sdi_address', 'sdi_address_fk3');
ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk3` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_address', 'sdi_address_fk4');
ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk4` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_address', 'sdi_address_fk5');
ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk5` FOREIGN KEY (`country_id`) REFERENCES `#__sdi_sys_country` (`id`) ;

CALL drop_foreign_key('sdi_organism_category', 'sdi_organism_category_fk1');
ALTER TABLE `#__sdi_organism_category`
ADD CONSTRAINT `#__sdi_organism_category_fk1` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CALL drop_foreign_key('sdi_organism_category', 'sdi_organism_category_fk2');
ALTER TABLE `#__sdi_organism_category`
ADD CONSTRAINT `#__sdi_organism_category_fk2` FOREIGN KEY (`category_id`) REFERENCES `#__sdi_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


-- com_easysdi_map

CALL drop_foreign_key('sdi_map_tool', 'sdi_map_tool_fk1');
ALTER TABLE `#__sdi_map_tool`
ADD CONSTRAINT `#__sdi_map_tool_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_map_tool', 'sdi_map_tool_fk2');
ALTER TABLE `#__sdi_map_tool`
ADD CONSTRAINT `#__sdi_map_tool_fk2` FOREIGN KEY (`tool_id`) REFERENCES `#__sdi_sys_maptool` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_map_layergroup', 'sdi_map_layergroup_fk1');
ALTER TABLE `#__sdi_map_layergroup`
ADD CONSTRAINT `#__sdi_map_layergroup_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_map_layergroup', 'sdi_map_layergroup_fk2');
ALTER TABLE `#__sdi_map_layergroup`
ADD CONSTRAINT `#__sdi_map_layergroup_fk2` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_layergroup` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_map_physicalservice', 'sdi_map_physicalservice_fk1');
ALTER TABLE `#__sdi_map_physicalservice`
ADD CONSTRAINT `#__sdi_map_physicalservice_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_map_physicalservice', 'sdi_map_physicalservice_fk2');
ALTER TABLE `#__sdi_map_physicalservice`
ADD CONSTRAINT `#__sdi_map_physicalservice_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_map_virtualservice', 'sdi_map_virtualservice_fk1');
ALTER TABLE `#__sdi_map_virtualservice`
ADD CONSTRAINT `#__sdi_map_virtualservice_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_map_virtualservice', 'sdi_map_virtualservice_fk2');
ALTER TABLE `#__sdi_map_virtualservice`
ADD CONSTRAINT `#__sdi_map_virtualservice_fk2` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_map', 'sdi_map_fk2');
ALTER TABLE `#__sdi_map`
ADD CONSTRAINT `#__sdi_map_fk2` FOREIGN KEY (`unit_id`) REFERENCES `#__sdi_sys_unit` (`id`);

CALL drop_foreign_key('sdi_layer_layergroup', 'sdi_layer_layergroup_fk1');
ALTER TABLE `#__sdi_layer_layergroup`
ADD CONSTRAINT `#__sdi_layer_layergroup_fk1` FOREIGN KEY (`layer_id`) REFERENCES `#__sdi_maplayer` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_layer_layergroup', 'sdi_layer_layergroup_fk2');
ALTER TABLE `#__sdi_layer_layergroup`
ADD CONSTRAINT `#__sdi_layer_layergroup_fk2` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_layergroup` (`id`) ON DELETE CASCADE;


-- com_easysdi_service


-- Google Bing and OSM layer
CALL drop_foreign_key('sdi_layer', 'sdi_layer_fk1');
ALTER TABLE `#__sdi_layer`
ADD CONSTRAINT `#__sdi_layer_fk1` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ;

-- Physical service
CALL drop_foreign_key('sdi_physicalservice', 'sdi_physicalservice_fk1');
ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk1` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`) ;

CALL drop_foreign_key('sdi_physicalservice', 'sdi_physicalservice_fk2');
ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk2` FOREIGN KEY (`resourceauthentication_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`) ;

CALL drop_foreign_key('sdi_physicalservice', 'sdi_physicalservice_fk3');
ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk3` FOREIGN KEY (`serviceauthentication_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`);

CALL drop_foreign_key('sdi_physicalservice_servicecompliance', 'sdi_physicalservice_servicecompliance_fk1');
ALTER TABLE `#__sdi_physicalservice_servicecompliance`
ADD CONSTRAINT `#__sdi_physicalservice_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_physicalservice_servicecompliance', 'sdi_physicalservice_servicecompliance_fk2');
ALTER TABLE `#__sdi_physicalservice_servicecompliance`
ADD CONSTRAINT `#__sdi_physicalservice_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_physicalservice', 'sdi_physicalservice_fk4');
ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk4` FOREIGN KEY (`servicescope_id`) REFERENCES `#__sdi_sys_servicescope` (`id`) ;

CALL drop_foreign_key('sdi_physicalservice_organism', 'sdi_physicalservice_organism_fk1');
ALTER TABLE `#__sdi_physicalservice_organism`
ADD CONSTRAINT `#__sdi_physicalservice_organism_fk1` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_physicalservice_organism', 'sdi_physicalservice_organism_fk2');
ALTER TABLE `#__sdi_physicalservice_organism`
ADD CONSTRAINT `#__sdi_physicalservice_organism_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE;

-- Virtual Service
CALL drop_foreign_key('sdi_virtualservice', 'sdi_virtualservice_fk1');
ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk1` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`) ;

CALL drop_foreign_key('sdi_virtualservice', 'sdi_virtualservice_fk2');
ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk2` FOREIGN KEY (`proxytype_id`) REFERENCES `#__sdi_sys_proxytype` (`id`) ;

CALL drop_foreign_key('sdi_virtualservice', 'sdi_virtualservice_fk3');
ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk3` FOREIGN KEY (`exceptionlevel_id`) REFERENCES `#__sdi_sys_exceptionlevel` (`id`) ;

CALL drop_foreign_key('sdi_virtualservice', 'sdi_virtualservice_fk4');
ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk4` FOREIGN KEY (`loglevel_id`) REFERENCES `#__sdi_sys_loglevel` (`id`) ;

CALL drop_foreign_key('sdi_virtualservice', 'sdi_virtualservice_fk5');
ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk5` FOREIGN KEY (`logroll_id`) REFERENCES `#__sdi_sys_logroll` (`id`) ;

CALL drop_foreign_key('sdi_virtualmetadata', 'sdi_virtualmetadata_fk1');
ALTER TABLE `#__sdi_virtualmetadata`
ADD CONSTRAINT `#__sdi_virtualmetadata_fk1` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_virtualmetadata', 'sdi_virtualmetadata_fk2');
ALTER TABLE `#__sdi_virtualmetadata`
ADD CONSTRAINT `#__sdi_virtualmetadata_fk2` FOREIGN KEY (`country_id`) REFERENCES `#__sdi_sys_country` (`id`) ;

CALL drop_foreign_key('sdi_virtual_physical', 'sdi_virtual_physical_fk1');
ALTER TABLE `#__sdi_virtual_physical`
ADD CONSTRAINT `#__sdi_virtual_physical_fk1` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_virtual_physical', 'sdi_virtual_physical_fk2');
ALTER TABLE `#__sdi_virtual_physical`
ADD CONSTRAINT `#__sdi_virtual_physical_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_virtualservice_servicecompliance', 'sdi_virtualservice_servicecompliance_fk1');
ALTER TABLE `#__sdi_virtualservice_servicecompliance`
ADD CONSTRAINT `#__sdi_virtualservice_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_virtualservice_servicecompliance', 'sdi_virtualservice_servicecompliance_fk2');
ALTER TABLE `#__sdi_virtualservice_servicecompliance`
ADD CONSTRAINT `#__sdi_virtualservice_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_virtualservice', 'sdi_virtualservice_fk6');
ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk6` FOREIGN KEY (`servicescope_id`) REFERENCES `#__sdi_sys_servicescope` (`id`) ;

CALL drop_foreign_key('sdi_virtualservice_organism', 'sdi_virtualservice_organism_fk1');
ALTER TABLE `#__sdi_virtualservice_organism`
ADD CONSTRAINT `#__sdi_virtualservice_organism_fk1` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_virtualservice_organism', 'sdi_virtualservice_organism_fk2');
ALTER TABLE `#__sdi_virtualservice_organism`
ADD CONSTRAINT `#__sdi_virtualservice_organism_fk2` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE;

-- Policy
CALL drop_foreign_key('sdi_policy', 'sdi_policy_fk1');
ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk1` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_policy', 'sdi_policy_fk2');
ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk2` FOREIGN KEY (`accessscope_id`) REFERENCES `#__sdi_sys_accessscope` (`id`) ;

CALL drop_foreign_key('sdi_policy', 'sdi_policy_fk7');
ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk7` FOREIGN KEY (`csw_version_id`) REFERENCES `#__sdi_sys_metadataversion` (`id`) ;

CALL drop_foreign_key('sdi_allowedoperation', 'sdi_allowedoperationy_fk1');
ALTER TABLE `#__sdi_allowedoperation`
ADD CONSTRAINT `#__sdi_allowedoperationy_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_allowedoperation', 'sdi_allowedoperationy_fk2');
ALTER TABLE `#__sdi_allowedoperation`
ADD CONSTRAINT `#__sdi_allowedoperationy_fk2` FOREIGN KEY (`serviceoperation_id`) REFERENCES `#__sdi_sys_serviceoperation` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_policy_metadatastate', 'sdi_policy_metadatastate_fk1');
ALTER TABLE `#__sdi_policy_metadatastate`
ADD CONSTRAINT `#__sdi_policy_metadatastate_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_policy_metadatastate', 'sdi_policy_metadatastate_fk2');
ALTER TABLE `#__sdi_policy_metadatastate`
ADD CONSTRAINT `#__sdi_policy_metadatastate_fk2` FOREIGN KEY (`metadatastate_id`) REFERENCES `#__sdi_sys_metadatastate` (`id`)  ON DELETE CASCADE;

CALL drop_foreign_key('sdi_physicalservice_policy', 'sdi_physicalservice_policy_fk1');
ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_physicalservice_policy', 'sdi_physicalservice_policy_fk2');
ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_excludedattribute', 'sdi_excludedattribute_fk1');
ALTER TABLE `#__sdi_excludedattribute`
ADD CONSTRAINT `#__sdi_excludedattribute_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_wmtslayer_policy', 'sdi_wmtslayer_policy_fk1');
ALTER TABLE `#__sdi_wmtslayer_policy`
ADD CONSTRAINT `#__sdi_wmtslayer_policy_fk1` FOREIGN KEY (`physicalservicepolicy_id`) REFERENCES `#__sdi_physicalservice_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_tilematrixset_policy', 'sdi_tilematrixset_policy_fk1');
ALTER TABLE `#__sdi_tilematrixset_policy`
ADD CONSTRAINT `#__sdi_tilematrixset_policy_fk1` FOREIGN KEY (`wmtslayerpolicy_id`) REFERENCES `#__sdi_wmtslayer_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_tilematrix_policy', 'sdi_tilematrix_policy_fk1');
ALTER TABLE `#__sdi_tilematrix_policy`
ADD CONSTRAINT `#__sdi_tilematrix_policy_fk1` FOREIGN KEY (`tilematrixsetpolicy_id`) REFERENCES `#__sdi_tilematrixset_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_wmslayer_policy', 'sdi_wmslayer_policy_fk1');
ALTER TABLE `#__sdi_wmslayer_policy`
ADD CONSTRAINT `#__sdi_wmslayer_policy_fk1` FOREIGN KEY (`physicalservicepolicy_id`) REFERENCES `#__sdi_physicalservice_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_featuretype_policy', 'sdi_featuretype_policy_fk1');
ALTER TABLE `#__sdi_featuretype_policy`
ADD CONSTRAINT `#__sdi_featuretype_policy_fk1` FOREIGN KEY (`physicalservicepolicy_id`) REFERENCES `#__sdi_physicalservice_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_includedattribute', 'sdi_includedattribute_fk1');
ALTER TABLE `#__sdi_includedattribute`
ADD CONSTRAINT `#__sdi_includedattribute_fk1` FOREIGN KEY (`featuretypepolicy_id`) REFERENCES `#__sdi_featuretype_policy` (`id`) ON DELETE CASCADE;

-- Policy authorized access
CALL drop_foreign_key('sdi_policy_organism', 'sdi_policy_organism_fk1');
ALTER TABLE `#__sdi_policy_organism`
ADD CONSTRAINT `#__sdi_policy_organism_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_policy_organism', 'sdi_policy_organism_fk2');
ALTER TABLE `#__sdi_policy_organism`
ADD CONSTRAINT `#__sdi_policy_organism_fk2` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_policy_user`
ADD CONSTRAINT `#__sdi_policy_user_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_policy_user', 'sdi_policy_user_fk2');
ALTER TABLE `#__sdi_policy_user`
ADD CONSTRAINT `#__sdi_policy_user_fk2` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`) ON DELETE CASCADE;

-- Spatial Policy
CALL drop_foreign_key('sdi_wmslayer_policy', 'sdi_wmslayer_policy_fk2');
ALTER TABLE `#__sdi_wmslayer_policy`
ADD CONSTRAINT `#__sdi_wmslayer_policy_fk2` FOREIGN KEY (`spatialpolicy_id`) REFERENCES `#__sdi_wms_spatialpolicy` (`id`);

CALL drop_foreign_key('sdi_wmtslayer_policy', 'sdi_wmtslayer_policy_fk2');
ALTER TABLE `#__sdi_wmtslayer_policy`
ADD CONSTRAINT `#__sdi_wmtslayer_policy_fk2` FOREIGN KEY (`spatialpolicy_id`) REFERENCES `#__sdi_wmts_spatialpolicy` (`id`) ;

CALL drop_foreign_key('sdi_wmts_spatialpolicy', 'sdi_wmts_spatialpolicy_fk1');
ALTER TABLE `#__sdi_wmts_spatialpolicy`
ADD CONSTRAINT `#__sdi_wmts_spatialpolicy_fk1` FOREIGN KEY (`spatialoperator_id`) REFERENCES `#__sdi_sys_spatialoperator` (`id`) ;

CALL drop_foreign_key('sdi_featuretype_policy', 'sdi_featuretype_policy_fk2');
ALTER TABLE `#__sdi_featuretype_policy`
ADD CONSTRAINT `#__sdi_featuretype_policy_fk2` FOREIGN KEY (`spatialpolicy_id`) REFERENCES `#__sdi_wfs_spatialpolicy` (`id`) ;

CALL drop_foreign_key('sdi_policy', 'sdi_policy_fk3');
ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk3` FOREIGN KEY (`csw_spatialpolicy_id`) REFERENCES `#__sdi_csw_spatialpolicy` (`id`) ;

CALL drop_foreign_key('sdi_policy', 'sdi_policy_fk4');
ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk4` FOREIGN KEY (`wms_spatialpolicy_id`) REFERENCES `#__sdi_wms_spatialpolicy` (`id`) ;

CALL drop_foreign_key('sdi_policy', 'sdi_policy_fk5');
ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk5` FOREIGN KEY (`wfs_spatialpolicy_id`) REFERENCES `#__sdi_wfs_spatialpolicy` (`id`) ;

CALL drop_foreign_key('sdi_policy', 'sdi_policy_fk6');
ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk6` FOREIGN KEY (`wmts_spatialpolicy_id`) REFERENCES `#__sdi_wmts_spatialpolicy` (`id`) ;

CALL drop_foreign_key('sdi_physicalservice_policy', 'sdi_physicalservice_policy_fk3');
ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk3` FOREIGN KEY (`csw_spatialpolicy_id`) REFERENCES `#__sdi_csw_spatialpolicy` (`id`) ;

CALL drop_foreign_key('sdi_physicalservice_policy', 'sdi_physicalservice_policy_fk4');
ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk4` FOREIGN KEY (`wms_spatialpolicy_id`) REFERENCES `#__sdi_wms_spatialpolicy` (`id`) ;

CALL drop_foreign_key('sdi_physicalservice_policy', 'sdi_physicalservice_policy_fk5');
ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk5` FOREIGN KEY (`wfs_spatialpolicy_id`) REFERENCES `#__sdi_wfs_spatialpolicy` (`id`) ;

CALL drop_foreign_key('sdi_physicalservice_policy', 'sdi_physicalservice_policy_fk6');
ALTER TABLE `#__sdi_physicalservice_policy`
ADD CONSTRAINT `#__sdi_physicalservice_policy_fk6` FOREIGN KEY (`wmts_spatialpolicy_id`) REFERENCES `#__sdi_wmts_spatialpolicy` (`id`) ;

CALL drop_foreign_key('sdi_policy_resourcetype', 'sdi_policy_resourcetype_fk1');
ALTER TABLE `#__sdi_policy_resourcetype`
ADD CONSTRAINT `#__sdi_policy_resourcetype_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_policy_resourcetype', 'sdi_policy_resourcetype_fk2');
ALTER TABLE `#__sdi_policy_resourcetype`
ADD CONSTRAINT `#__sdi_policy_resourcetype_fk2` FOREIGN KEY (`resourcetype_id`) REFERENCES `#__sdi_resourcetype` (`id`)  ON DELETE CASCADE;

CALL drop_foreign_key('sdi_policy_visibility', 'sdi_policy_visibility_fk1');
ALTER TABLE `#__sdi_policy_visibility`
ADD CONSTRAINT `#__sdi_policy_visibility_fk1` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE;

CALL drop_foreign_key('sdi_policy_visibility', 'sdi_policy_visibility_fk2');
ALTER TABLE `#__sdi_policy_visibility`
ADD CONSTRAINT `#__sdi_policy_visibility_fk2` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`)  ON DELETE CASCADE;

CALL drop_foreign_key('sdi_policy_visibility', 'sdi_policy_visibility_fk3');
ALTER TABLE `#__sdi_policy_visibility`
ADD CONSTRAINT `#__sdi_policy_visibility_fk3` FOREIGN KEY (`organism_id`) REFERENCES `#__sdi_organism` (`id`)  ON DELETE CASCADE;

-- com_easysdi_catalog
CALL drop_foreign_key('sdi_importref', 'sdi_importref_fk2');
ALTER TABLE `#__sdi_importref`
ADD CONSTRAINT `#__sdi_importref_fk2` FOREIGN KEY (`cswservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ;

CALL drop_foreign_key('sdi_importref', 'sdi_importref_fk3');
ALTER TABLE `#__sdi_importref`
ADD CONSTRAINT `#__sdi_importref_fk3` FOREIGN KEY (`cswversion_id`) REFERENCES `#__sdi_sys_serviceversion` (`id`) ON DELETE CASCADE ;

-- com_easysdi_monitor
ALTER TABLE `actions`
ADD CONSTRAINT `FK_ACTION_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_ACTION_TYPE` FOREIGN KEY (`ID_ACTION_TYPE`) REFERENCES `action_types` (`ID_ACTION_TYPE`);

ALTER TABLE `alerts`
ADD CONSTRAINT `FK_ALERTS_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_ALERTS_NEW_STATUS` FOREIGN KEY (`ID_NEW_STATUS`) REFERENCES `statuses` (`ID_STATUS`),
ADD CONSTRAINT `FK_ALERTS_OLD_STATUS` FOREIGN KEY (`ID_OLD_STATUS`) REFERENCES `statuses` (`ID_STATUS`);

ALTER TABLE `jobs`
ADD CONSTRAINT `FK_JOBS_HTTP_METHOD` FOREIGN KEY (`ID_HTTP_METHOD`) REFERENCES `http_methods` (`ID_HTTP_METHOD`),
ADD CONSTRAINT `FK_JOBS_SERVICE_TYPE` FOREIGN KEY (`ID_SERVICE_TYPE`) REFERENCES `service_types` (`ID_SERVICE_TYPE`),
ADD CONSTRAINT `FK_JOBS_STATUS` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuses` (`ID_STATUS`);

ALTER TABLE `job_agg_hour_log_entries`
ADD CONSTRAINT `FK_JOB_AGG_HOUR_LOG_ENTRIES_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `job_agg_log_entries`
ADD CONSTRAINT `FK_JOB_AGG_LOG_ENTRIES_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `last_query_results`
 ADD CONSTRAINT `FK_LAST_QUERY_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE ON UPDATE NO ACTION;

 ALTER TABLE `log_entries`
 ADD CONSTRAINT `FK_LOG_ENTRIES_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_log_entries_statuses_STATUS` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuses` (`ID_STATUS`);

ALTER TABLE `overview_queries`
ADD CONSTRAINT `FK_OVERVIEWQUERY_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE ON UPDATE NO ACTION,
ADD CONSTRAINT `FK_OW_QUERY_PAGE` FOREIGN KEY (`ID_OVERVIEW_PAGE`) REFERENCES `overview_page` (`ID_OVERVIEW_PAGE`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `periods`
ADD CONSTRAINT `FK_PERIODS_SLA` FOREIGN KEY (`ID_SLA`) REFERENCES `sla` (`ID_SLA`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `queries`
ADD CONSTRAINT `FK_QUERIES_JOB` FOREIGN KEY (`ID_JOB`) REFERENCES `jobs` (`ID_JOB`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_QUERIES_METHOD` FOREIGN KEY (`ID_SERVICE_METHOD`) REFERENCES `service_methods` (`ID_SERVICE_METHOD`),
ADD CONSTRAINT `FK_QUERIES_STATUS` FOREIGN KEY (`ID_STATUS`) REFERENCES `statuses` (`ID_STATUS`);

ALTER TABLE `query_agg_hour_log_entries`
ADD CONSTRAINT `FK_QUERY_AGG_HOUR_LOG_ENTRIES_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `query_agg_log_entries`
ADD CONSTRAINT `FK_QUERY_AGG_LOG_ENTRIES_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE;

ALTER TABLE `query_params`
ADD CONSTRAINT `FK_QUERY_PARAMS_QUERY` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE CASCADE;

ALTER TABLE `query_validation_results`
ADD CONSTRAINT `fk_query_validation_results_queries1` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `query_validation_settings`
ADD CONSTRAINT `fk_query_validation_settings_queries1` FOREIGN KEY (`ID_QUERY`) REFERENCES `queries` (`ID_QUERY`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `service_types_methods`
ADD CONSTRAINT `FK_SERVICE_TYPES_METHODS_METHOD` FOREIGN KEY (`ID_SERVICE_METHOD`) REFERENCES `service_methods` (`ID_SERVICE_METHOD`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_SERVICE_TYPES_METHODS_TYPE` FOREIGN KEY (`ID_SERVICE_TYPE`) REFERENCES `service_types` (`ID_SERVICE_TYPE`) ON DELETE CASCADE;

ALTER TABLE `users`
ADD CONSTRAINT `FK_USERS_ROLE` FOREIGN KEY (`ID_ROLE`) REFERENCES `roles` (`ID_ROLE`) ON DELETE SET NULL;

CALL drop_foreign_key('sdi_sys_server_serviceconnector', 'sdi_sys_server_serviceconnector_fk1');
ALTER TABLE `#__sdi_sys_server_serviceconnector`
ADD CONSTRAINT `#__sdi_sys_server_serviceconnector_fk1` FOREIGN KEY (`server_id`) REFERENCES `#__sdi_sys_server` (`id`);

CALL drop_foreign_key('sdi_sys_server_serviceconnector', 'sdi_sys_server_serviceconnector_fk2');
ALTER TABLE `#__sdi_sys_server_serviceconnector`
ADD CONSTRAINT `#__sdi_sys_server_serviceconnector_fk2` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`);

CALL drop_foreign_key('sdi_physicalservice', 'sdi_physicalservice_server_fk1');
ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_server_fk1` FOREIGN KEY (`server_id`) REFERENCES `#__sdi_sys_server` (`id`);

ALTER TABLE `#__sdi_order`
ADD CONSTRAINT `#__sdi_order_fk5` FOREIGN KEY (`validated_by`) REFERENCES `#__sdi_user` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;
