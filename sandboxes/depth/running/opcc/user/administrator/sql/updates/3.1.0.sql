ALTER TABLE `#__sdi_user` MODIFY   checked_out INT(11) NOT NULL;
ALTER TABLE `#__sdi_user` MODIFY   checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `#__sdi_address` MODIFY   checked_out INT(11) NOT NULL;
ALTER TABLE `#__sdi_address` MODIFY   checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `#__sdi_sys_addresstype` MODIFY   checked_out INT(11) NOT NULL;
ALTER TABLE `#__sdi_sys_addresstype` MODIFY   checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `#__sdi_sys_civility` MODIFY   checked_out INT(11) NOT NULL;
ALTER TABLE `#__sdi_sys_civility` MODIFY   checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';