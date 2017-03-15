ALTER TABLE `#__sdi_translation` DROP FOREIGN KEY `#__sdi_translation_fk1`;
ALTER TABLE `#__sdi_translation` ADD CONSTRAINT `#__sdi_translation_fk1` FOREIGN KEY (`language_id`) REFERENCES `#__sdi_language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

UPDATE #__sdi_sys_stereotype SET defaultpattern = '^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$' WHERE id = 1;
UPDATE #__sdi_sys_stereotype SET defaultpattern = '^[\-+]?[0-9.]+$' WHERE id = 4;
UPDATE #__sdi_sys_stereotype SET defaultpattern = '^([0-9]{4}-[0-9]{2}-[0-9]{2})$' WHERE id = 5;
UPDATE #__sdi_sys_stereotype SET defaultpattern = '^((https?://)?([w.-]+).([a-z.]{2,6})([/w .#:+?%=&;,]*)*/?)$' WHERE id = 7;
UPDATE #__sdi_sys_stereotype SET defaultpattern = '^([0-9]{4}-[0-9]{2}-[0-9]{2})$' WHERE id = 8;
UPDATE #__sdi_sys_stereotype SET defaultpattern = '^[\-+]?[0-9.]*[0-9]([Ee]\-?[0-9.]*[0-9])?$' WHERE id = 12;
UPDATE #__sdi_sys_stereotype SET defaultpattern = '^[\-+]?[0-9]+$' WHERE id = 13;