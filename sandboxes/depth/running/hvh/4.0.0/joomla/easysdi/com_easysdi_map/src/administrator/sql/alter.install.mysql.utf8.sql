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
