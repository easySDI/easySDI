
ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk1` FOREIGN KEY (`addresstype_id`) REFERENCES `#__sdi_sys_addresstype` (`id`) ;

ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk2` FOREIGN KEY (`civility`) REFERENCES `#__sdi_sys_civility` (`id`);

ALTER TABLE `#__sdi_address`
ADD CONSTRAINT `#__sdi_address_fk3` FOREIGN KEY (`user_id`) REFERENCES `#__sdi_user` (`id`) ON DELETE CASCADE ;

