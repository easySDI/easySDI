ALTER TABLE `#__sdi_translation` ADD COLUMN `text3`  varchar(255) NULL AFTER `text2`;

ALTER TABLE #__sdi_resourcetype MODIFY modified_by int(11) NULL;

ALTER TABLE #__sdi_resourcetype MODIFY modified datetime NULL;

ALTER TABLE #__sdi_resourcetypelink MODIFY modified_by int(11) NULL;

ALTER TABLE #__sdi_resourcetypelink MODIFY modified datetime NULL;

ALTER TABLE #__sdi_application MODIFY modified_by int(11) NULL;

ALTER TABLE #__sdi_application MODIFY modified datetime NULL;

ALTER TABLE #__sdi_diffusion MODIFY modified datetime NULL;

ALTER TABLE #__sdi_diffusion MODIFY description VARCHAR(500) NULL;

ALTER TABLE #__sdi_order MODIFY modified datetime NULL;

ALTER TABLE #__sdi_order MODIFY remark VARCHAR(500) NULL;

ALTER TABLE #__sdi_order_diffusion MODIFY remark VARCHAR(500) NULL;
ALTER TABLE #__sdi_order_diffusion MODIFY fee DECIMAL(10) NULL;
ALTER TABLE #__sdi_order_diffusion MODIFY completed datetime NULL;
ALTER TABLE #__sdi_order_diffusion MODIFY `file` VARCHAR(500) NULL;
ALTER TABLE #__sdi_order_diffusion MODIFY `size` DECIMAL(10 NULL;
