ALTER TABLE `#__sdi_mapcontext_maptool`
ADD CONSTRAINT `#__sdi_mapcontext_maptool_fk1` FOREIGN KEY (`mapcontext_id`) REFERENCES `#__sdi_mapcontext` (`id`);

ALTER TABLE `#__sdi_mapcontext_maptool`
ADD CONSTRAINT `#__sdi_mapcontext_maptool_fk2` FOREIGN KEY (`maptool_id`) REFERENCES `#__sdi_sys_maptool` (`id`);

ALTER TABLE `#__sdi_mapcontext_maplayergroup`
ADD CONSTRAINT `#__sdi_mapcontext_maplayergroup_fk1` FOREIGN KEY (`mapcontext_id`) REFERENCES `#__sdi_mapcontext` (`id`);

ALTER TABLE `#__sdi_mapcontext_maplayergroup`
ADD CONSTRAINT `#__sdi_mapcontext_maplayergroup_fk2` FOREIGN KEY (`maplayergroup_id`) REFERENCES `#__sdi_maplayergroup` (`id`);

ALTER TABLE `#__sdi_mapcontext_physicalservice`
ADD CONSTRAINT `#__sdi_mapcontext_physicalservice_fk1` FOREIGN KEY (`mapcontext_id`) REFERENCES `#__sdi_mapcontext` (`id`);

ALTER TABLE `#__sdi_mapcontext_physicalservice`
ADD CONSTRAINT `#__sdi_mapcontext_physicalservice_fk2` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`);

ALTER TABLE `#__sdi_mapcontext_virtualservice`
ADD CONSTRAINT `#__sdi_mapcontext_virtualservice_fk1` FOREIGN KEY (`mapcontext_id`) REFERENCES `#__sdi_mapcontext` (`id`);

ALTER TABLE `#__sdi_mapcontext_virtualservice`
ADD CONSTRAINT `#__sdi_mapcontext_virtualservice_fk2` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`);

ALTER TABLE `#__sdi_mapcontext`
ADD CONSTRAINT `#__sdi_mapcontext_fk1` FOREIGN KEY (`srs_id`) REFERENCES `#__sdi_srs` (`id`);

ALTER TABLE `#__sdi_mapcontext`
ADD CONSTRAINT `#__sdi_mapcontext_fk2` FOREIGN KEY (`unit_id`) REFERENCES `#__sdi_unit` (`id`);

ALTER TABLE `#__sdi_maplayer`
ADD CONSTRAINT `#__sdi_maplayer_fk1` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`);

ALTER TABLE `#__sdi_maplayer`
ADD CONSTRAINT `#__sdi_maplayer_fk2` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`);
