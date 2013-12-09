ALTER TABLE `#__sdi_maplayer` ADD `accessscope_id` INT(11) UNSIGNED NOT NULL ;

ALTER TABLE `#__sdi_maplayer`
   ADD CONSTRAINT `#__sdi_maplayer_fk1`
   FOREIGN KEY (`accessscope_id` )
   REFERENCES `#__sdi_sys_accessscope` (`id` )
   ON DELETE CASCADE
   ON UPDATE NO ACTION;

ALTER TABLE `#__sdi_visualization` ADD `maplayer_id` INT(11) UNSIGNED  NOT NULL ;