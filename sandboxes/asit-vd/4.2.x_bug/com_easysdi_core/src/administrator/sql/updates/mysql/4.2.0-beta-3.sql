
ALTER TABLE `#__sdi_accessscope` ADD category_id INT(11) UNSIGNED;
ALTER TABLE `#__sdi_accessscope` ADD CONSTRAINT `#__sdi_accessscope_fk3` FOREIGN KEY (category_id) REFERENCES `#__sdi_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;