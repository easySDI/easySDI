CREATE TABLE IF NOT EXISTS `#__sdi_diffusion_download` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`diffusion_id` int(11) UNSIGNED NOT NULL ,
`user_id` INT(11) UNSIGNED NULL ,
`executed` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`),
    INDEX `#__sdi_diffusion_download_fk1` (`diffusion_id` ASC) ,
    INDEX `#__sdi_diffusion_download_fk2` (`user_id` ASC) ,
  CONSTRAINT `#__sdi_diffusion_download_fk1`
    FOREIGN KEY (`diffusion_id`)
    REFERENCES `#__sdi_diffusion` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `#__sdi_diffusion_download_fk2`
    FOREIGN KEY (`user_id`)
    REFERENCES `#__sdi_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;
