ALTER TABLE #__sdi_metadata ADD COLUMN `endpublished` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER published;
UPDATE  #__sdi_sys_stereotype SET defaultpattern = '((http:\/\/|https:\/\/|ftp:\/\/)(www.)?(([a-zA-Z0-9-]){2,}.){1,4}([a-zA-Z]){2,6}(\/([a-zA-Z-_\/.0-9#:?=&;,]*)?)?)|^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$' WHERE id = 7;