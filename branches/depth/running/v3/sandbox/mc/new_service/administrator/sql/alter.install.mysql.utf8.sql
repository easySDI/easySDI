ALTER TABLE `#__sdi_virtualmetadata`
ADD CONSTRAINT `#__sdi_virtualmetadata_fk_virtualservice` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`);


ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk_proxytype` FOREIGN KEY (`proxytype_id`) REFERENCES `#__sdi_sys_proxytype` (`id`);

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk_exceptionlevel` FOREIGN KEY (`exceptionlevel_id`) REFERENCES `#__sdi_sys_exceptionlevel` (`id`);

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk_loglevel` FOREIGN KEY (`loglevel_id`) REFERENCES `#__sdi_sys_loglevel` (`id`);

ALTER TABLE `#__sdi_virtualservice`
ADD CONSTRAINT `#__sdi_virtualservice_fk_logroll` FOREIGN KEY (`logroll_id`) REFERENCES `#__sdi_sys_logroll` (`id`);


ALTER TABLE `#__sdi_physicalservice`
ADD CONSTRAINT `#__sdi_physicalservice_fk_virtualservice` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`);


ALTER TABLE `#__sdi_policy`
ADD CONSTRAINT `#__sdi_policy_fk_virtualservice` FOREIGN KEY (`virtualservice_id`) REFERENCES `#__sdi_virtualservice` (`id`);


ALTER TABLE `#__sdi_servicepolicy`
ADD CONSTRAINT `#__sdi_servicepolicy_fk_physicalservice` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`);

ALTER TABLE `#__sdi_servicepolicy`
ADD CONSTRAINT `#__sdi_servicepolicy_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`);


ALTER TABLE `#__sdi_wmslayer`
ADD CONSTRAINT `#__sdi_wmslayer_fk_physicalservice` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`);


ALTER TABLE `#__sdi_wmslayerpolicy`
ADD CONSTRAINT `#__sdi_wmslayerpolicy_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`);

ALTER TABLE `#__sdi_wmslayerpolicy`
ADD CONSTRAINT `#__sdi_wmslayerpolicy_fk_wmslayer` FOREIGN KEY (`wmslayer_id`) REFERENCES `#__sdi_wmslayer` (`id`);


ALTER TABLE `#__sdi_allowedoperation`
ADD CONSTRAINT `#__sdi_allowedoperation_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`);


ALTER TABLE `#__sdi_versiontype`
ADD CONSTRAINT `#__sdi_versiontype_fk_cswpolicy` FOREIGN KEY (`cswpolicy_id`) REFERENCES `#__sdi_cswpolicy` (`id`);


ALTER TABLE `#__sdi_visibilitytype`
ADD CONSTRAINT `#__sdi_visibilitytype_fk_cswpolicy` FOREIGN KEY (`cswpolicy_id`) REFERENCES `#__sdi_cswpolicy` (`id`);


ALTER TABLE `#__sdi_elementrestriction`
ADD CONSTRAINT `#__sdi_elementrestriction_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`);


ALTER TABLE `#__sdi_featureclass`
ADD CONSTRAINT `#__sdi_featureclass_fk_physicalservice` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`);


ALTER TABLE `#__sdi_featureclasspolicy`
ADD CONSTRAINT `#__sdi_featureclasspolicy_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`);

ALTER TABLE `#__sdi_featureclasspolicy`
ADD CONSTRAINT `#__sdi_featureclasspolicy_fk_featureclass` FOREIGN KEY (`featureclass_id`) REFERENCES `#__sdi_featureclass` (`id`);


ALTER TABLE `#__sdi_wmtslayer`
ADD CONSTRAINT `#__sdi_wmtslayer_fk_physicalservice` FOREIGN KEY (`physicalservice_id`) REFERENCES `#__sdi_physicalservice` (`id`);


ALTER TABLE `#__sdi_wmtslayerpolicy`
ADD CONSTRAINT `#__sdi_wmtslayerpolicy_fk_policy` FOREIGN KEY (`policy_id`) REFERENCES `#__sdi_policy` (`id`);

ALTER TABLE `#__sdi_wmtslayerpolicy`
ADD CONSTRAINT `#__sdi_wmtslayerpolicy_fk_wmtslayer` FOREIGN KEY (`wmtslayer_id`) REFERENCES `#__sdi_wmtslayer` (`id`);


ALTER TABLE `#__sdi_scalepolicy`
ADD CONSTRAINT `#__sdi_scalepolicy_fk_wmtslayerpolicy` FOREIGN KEY (`wmtslayerpolicy_id`) REFERENCES `#__sdi_wmtslayerpolicy` (`id`);

ALTER TABLE `#__sdi_scalepolicy`
ADD CONSTRAINT `#__sdi_scalepolicy_fk_tilematrixset` FOREIGN KEY (`tilematrixset_id`) REFERENCES `#__sdi_tilematrixset` (`id`);


ALTER TABLE `#__sdi_tilematrixset`
ADD CONSTRAINT `#__sdi_tilematrixset_fk_denominator` FOREIGN KEY (`denominator_id`) REFERENCES `#__sdi_denominator` (`id`);






