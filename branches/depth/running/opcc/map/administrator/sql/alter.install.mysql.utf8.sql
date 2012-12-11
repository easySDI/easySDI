ALTER TABLE `#__sdi_map_context_tool`
ADD CONSTRAINT `#__sdi_map_context_tool_fk1` FOREIGN KEY (`context_id`) REFERENCES `#__sdi_map_context` (`id`);

ALTER TABLE `#__sdi_map_context_tool`
ADD CONSTRAINT `#__sdi_map_context_tool_fk2` FOREIGN KEY (`tool_id`) REFERENCES `#__sdi_sys_map_tool` (`id`);

ALTER TABLE `#__sdi_map_context_group`
ADD CONSTRAINT `#__sdi_map_context_group_fk1` FOREIGN KEY (`context_id`) REFERENCES `#__sdi_map_context` (`id`);

ALTER TABLE `#__sdi_map_context_group`
ADD CONSTRAINT `#__sdi_map_context_group_fk2` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_map_group` (`id`);

ALTER TABLE `#__sdi_map_context_physicalservice`
ADD CONSTRAINT `#__sdi_map_context_physicalservice_fk1` FOREIGN KEY (`context_id`) REFERENCES `#__sdi_map_context` (`id`);

ALTER TABLE `#__sdi_map_context_physicalservice`
ADD CONSTRAINT `#__sdi_map_context_physicalservice_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`);

ALTER TABLE `#__sdi_map_context_virtualservice`
ADD CONSTRAINT `#__sdi_map_context_virtualservice_fk1` FOREIGN KEY (`context_id`) REFERENCES `#__sdi_map_context` (`id`);

ALTER TABLE `#__sdi_map_context_virtualservice`
ADD CONSTRAINT `#__sdi_map_context_virtualservice_fk2` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`);

ALTER TABLE `#__sdi_map_context`
ADD CONSTRAINT `#__sdi_map_context_fk2` FOREIGN KEY (`unit_id`) REFERENCES `#__sdi_sys_unit` (`id`);

ALTER TABLE `#__sdi_map_context`
ADD CONSTRAINT `#__sdi_map_context_fk3` FOREIGN KEY (`defaultserviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`);

--ALTER TABLE `#__sdi_map_layer`
--ADD CONSTRAINT `#__sdi_map_layer_fk1` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`);
--
--ALTER TABLE `#__sdi_map_layer`
--ADD CONSTRAINT `#__sdi_map_layer_fk2` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`);

ALTER TABLE `#__sdi_map_layer`
ADD CONSTRAINT `#__sdi_map_layer_fk3` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_map_group` (`id`);