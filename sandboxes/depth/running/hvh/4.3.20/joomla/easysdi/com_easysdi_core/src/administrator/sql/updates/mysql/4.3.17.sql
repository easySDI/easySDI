ALTER TABLE #__sdi_diffusion ADD COLUMN `redirectionurl` VARCHAR(500)  NULL AFTER hasdownload;
ALTER TABLE #__sdi_diffusion ADD COLUMN `witheula` tinyint(1) NOT NULL DEFAULT '1' AFTER hasdownload;

INSERT INTO `#__sdi_sys_productstorage` VALUES ('4', '4', '1', 'redirection');


