
ALTER TABLE #__sdi_order_diffusion ADD COLUMN `guid` VARCHAR(36) NOT NULL AFTER id;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER created_by;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN `modified_by` INT(11) AFTER created;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN `modified` DATETIME AFTER modified_by;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN `ordering` INT(11)  NOT NULL AFTER modified;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN `state` int(11)  NOT NULL DEFAULT '1' AFTER ordering;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN `checked_out` INT(11) NOT NULL DEFAULT '0' AFTER `state`;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER checked_out;