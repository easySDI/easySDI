ALTER TABLE `#__sdi_sys_servicecompliance`
ADD CONSTRAINT `#__sdi_sys_servicecompliance_fk1` FOREIGN KEY (`serviceversion_id`) REFERENCES `#__sdi_sys_serviceversion` (`id`);
	
ALTER TABLE `#__sdi_sys_servicecompliance`
ADD CONSTRAINT `#__sdi_sys_servicecompliance_fk2` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`);

ALTER TABLE `#__sdi_sys_authenticationconnector`
ADD CONSTRAINT `#__sdi_sys_authenticationconnector_fk1` FOREIGN KEY (`authenticationlevel_id`) REFERENCES `#__sdi_sys_authenticationlevel` (`id`);

ALTER TABLE `#__sdi_sys_servicecon_authenticationcon`
ADD CONSTRAINT `#__sdi_sys_servicecon_authenticationcon_fk1` FOREIGN KEY (`authenticationconnector_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`);

ALTER TABLE `#__sdi_sys_servicecon_authenticationcon`
ADD CONSTRAINT `#__sdi_sys_servicecon_authenticationcon_fk2` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`);

ALTER TABLE `#__sdi_service_servicecompliance`
ADD CONSTRAINT `#__sdi_service_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_service` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_service_servicecompliance`
ADD CONSTRAINT `#__sdi_service_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_sys_operationcompliance`
ADD CONSTRAINT `#__sdi_sys_operationcompliance_fk1` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`);

ALTER TABLE `#__sdi_sys_operationcompliance`
ADD CONSTRAINT `#__sdi_sys_operationcompliance_fk2` FOREIGN KEY (`serviceoperation_id`) REFERENCES `#__sdi_sys_serviceoperation` (`id`);

ALTER TABLE `#__sdi_service`
ADD CONSTRAINT `#__sdi_service_fk1` FOREIGN KEY (`resourceauthentication_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`);

ALTER TABLE `#__sdi_service`
ADD CONSTRAINT `#__sdi_service_fk2` FOREIGN KEY (`serviceauthentication_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`);

ALTER TABLE `#__sdi_service`
ADD CONSTRAINT `#__sdi_service_fk3` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`);