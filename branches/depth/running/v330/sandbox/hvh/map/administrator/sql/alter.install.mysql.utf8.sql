ALTER TABLE `#__sdi_map_tool`
ADD CONSTRAINT `#__sdi_map_tool_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`);

ALTER TABLE `#__sdi_map_tool`
ADD CONSTRAINT `#__sdi_map_tool_fk2` FOREIGN KEY (`tool_id`) REFERENCES `#__sdi_sys_maptool` (`id`);

ALTER TABLE `#__sdi_map_layergroup`
ADD CONSTRAINT `#__sdi_map_layergroup_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`);

ALTER TABLE `#__sdi_map_layergroup`
ADD CONSTRAINT `#__sdi_map_layergroup_fk2` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_layergroup` (`id`);

ALTER TABLE `#__sdi_map_physicalservice`
ADD CONSTRAINT `#__sdi_map_physicalservice_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`);

ALTER TABLE `#__sdi_map_physicalservice`
ADD CONSTRAINT `#__sdi_map_physicalservice_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`);

ALTER TABLE `#__sdi_map_virtualservice`
ADD CONSTRAINT `#__sdi_map_virtualservice_fk1` FOREIGN KEY (`map_id`) REFERENCES `#__sdi_map` (`id`);

ALTER TABLE `#__sdi_map_virtualservice`
ADD CONSTRAINT `#__sdi_map_virtualservice_fk2` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`);

ALTER TABLE `#__sdi_map`
ADD CONSTRAINT `#__sdi_map_fk2` FOREIGN KEY (`unit_id`) REFERENCES `#__sdi_sys_unit` (`id`);

ALTER TABLE `#__sdi_map`
ADD CONSTRAINT `#__sdi_map_fk3` FOREIGN KEY (`defaultserviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`);

ALTER TABLE `#__sdi_maplayer`
ADD CONSTRAINT `#__sdi_maplayer_fk3` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_layergroup` (`id`);
