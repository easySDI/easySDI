ALTER TABLE `#__sdi_translation` ADD COLUMN `text3`  varchar(255) NULL AFTER `text2`;

ALTER TABLE #__sdi_resourcetype MODIFY modified_by int(11) NULL;

ALTER TABLE #__sdi_resourcetype MODIFY modified datetime NULL;

ALTER TABLE #__sdi_resourcetypelink MODIFY modified_by int(11) NULL;

ALTER TABLE #__sdi_resourcetypelink MODIFY modified datetime NULL;
