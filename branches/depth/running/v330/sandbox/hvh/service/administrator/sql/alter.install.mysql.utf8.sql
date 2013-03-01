ALTER TABLE `#__sdi_virtualmetadata`
ADD CONSTRAINT `#__sdi_virtualmetadata_fk_virtualservice` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk_proxytype` FOREIGN KEY (`proxytype_id`) REFERENCES `#__sdi_sys_proxytype` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_sys_servicecon_authenticationcon`
ADD CONSTRAINT `#__sdi_sys_servicecon_authenticationcon_fk2` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`);

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk_exceptionlevel` FOREIGN KEY (`exceptionlevel_id`) REFERENCES `#__sdi_sys_exceptionlevel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk_loglevel` FOREIGN KEY (`loglevel_id`) REFERENCES `#__sdi_sys_loglevel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk_logroll` FOREIGN KEY (`logroll_id`) REFERENCES `#__sdi_sys_logroll` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_physicalservice_servicecompliance`
ADD CONSTRAINT `#__sdi_physicalservice_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_physicalservice_servicecompliance`
ADD CONSTRAINT `#__sdi_physicalservice_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_virtualservice_servicecompliance`
ADD CONSTRAINT `#__sdi_virtualservice_servicecompliance_fk1` FOREIGN KEY (`service_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_virtualservice_servicecompliance`
ADD CONSTRAINT `#__sdi_virtualservice_servicecompliance_fk2` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`) ON DELETE CASCADE ;

ALTER TABLE `#__sdi_sys_operationcompliance`
ADD CONSTRAINT `#__sdi_sys_operationcompliance_fk1` FOREIGN KEY (`servicecompliance_id`) REFERENCES `#__sdi_sys_servicecompliance` (`id`);

ALTER TABLE `#__sdi_sys_operationcompliance`
ADD CONSTRAINT `#__sdi_sys_operationcompliance_fk2` FOREIGN KEY (`serviceoperation_id`) REFERENCES `#__sdi_sys_serviceoperation` (`id`);

ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk1` FOREIGN KEY (`resourceauthentication_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`);

ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk2` FOREIGN KEY (`serviceauthentication_id`) REFERENCES `#__sdi_sys_authenticationconnector` (`id`);

ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk_virtualservice` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk3` FOREIGN KEY (`serviceconnector_id`) REFERENCES `#__sdi_sys_serviceconnector` (`id`);

ALTER TABLE `#__sdi_servicepolicy`
ADD CONSTRAINT `#__sdi_servicepolicy_fk_physicalservice` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_servicepolicy`
ADD CONSTRAINT `#__sdi_servicepolicy_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_wmslayer`
ADD CONSTRAINT `#__sdi_wmslayer_fk_physicalservice` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_wmslayerpolicy`
ADD CONSTRAINT `#__sdi_wmslayerpolicy_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_wmslayerpolicy`
ADD CONSTRAINT `#__sdi_wmslayerpolicy_fk_wmslayer` FOREIGN KEY (`wmslayer_id`) REFERENCES `#__sdi_wmslayer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_allowedoperation`
ADD CONSTRAINT `#__sdi_allowedoperation_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_policy_versiontype`
ADD CONSTRAINT `#__sdi_versiontype_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_policy_accessscope`
ADD CONSTRAINT `#__sdi_visibilitytype_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_elementrestriction`
ADD CONSTRAINT `#__sdi_elementrestriction_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_featureclass`
ADD CONSTRAINT `#__sdi_featureclass_fk_physicalservice` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_featureclasspolicy`
ADD CONSTRAINT `#__sdi_featureclasspolicy_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_featureclasspolicy`
ADD CONSTRAINT `#__sdi_featureclasspolicy_fk_featureclass` FOREIGN KEY (`featureclass_id`) REFERENCES `#__sdi_featureclass` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_wmtslayer`
ADD CONSTRAINT `#__sdi_wmtslayer_fk_physicalservice` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_wmtslayerpolicy`
ADD CONSTRAINT `#__sdi_wmtslayerpolicy_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_wmtslayerpolicy`
ADD CONSTRAINT `#__sdi_wmtslayerpolicy_fk_wmtslayer` FOREIGN KEY (`wmtslayer_id`) REFERENCES `#__sdi_wmtslayer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_tilematrixset`
ADD CONSTRAINT `#__sdi_tilematrixset_fk_wmtslayer` FOREIGN KEY (`wmtslayer_id`) REFERENCES `#__sdi_wmtslayer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_tilematrix`
ADD CONSTRAINT `#__sdi_tilematrix_fk_tilematrixset` FOREIGN KEY (`tilematrixset_id`) REFERENCES `#__sdi_tilematrixset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_tilematrixpolicy`
ADD CONSTRAINT `#__sdi_tilematrixpolicy_fk_wmtslayerpolicy` FOREIGN KEY (`wmtslayerpolicy_id`) REFERENCES `#__sdi_wmtslayerpolicy` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_tilematrixpolicy`
ADD CONSTRAINT `#__sdi_tilematrixpolicy_fk_tilematrixset` FOREIGN KEY (`tilematrixset_id`) REFERENCES `#__sdi_tilematrixset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__sdi_tilematrixpolicy`
ADD CONSTRAINT `#__sdi_tilematrixpolicy_fk_tilematrix` FOREIGN KEY (`tilematrix_id`) REFERENCES `#__sdi_tilematrix` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;





