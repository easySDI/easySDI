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

