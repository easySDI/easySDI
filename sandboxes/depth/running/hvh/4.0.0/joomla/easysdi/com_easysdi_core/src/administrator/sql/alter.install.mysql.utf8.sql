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

ALTER TABLE `#__sdi_sys_rendertype_stereotype`
ADD CONSTRAINT `#__sdi_sys_rendertype_stereotype_fk1` FOREIGN KEY (`stereotype_id`) REFERENCES `#__sdi_sys_stereotype` (`id`);

ALTER TABLE `#__sdi_sys_rendertype_stereotype`
ADD CONSTRAINT `#__sdi_sys_rendertype_stereotype_fk2` FOREIGN KEY (`rendertype_id`) REFERENCES `#__sdi_sys_rendertype` (`id`);

ALTER TABLE `#__sdi_sys_rendertype_criteriatype`
ADD CONSTRAINT `#__sdi_sys_rendertype_criteriatype_fk1` FOREIGN KEY (`criteriatype_id`) REFERENCES `#__sdi_sys_criteriatype` (`id`);

ALTER TABLE `#__sdi_sys_rendertype_criteriatype`
ADD CONSTRAINT `#__sdi_sys_rendertype_criteriatype_fk2` FOREIGN KEY (`rendertype_id`) REFERENCES `#__sdi_sys_rendertype` (`id`);