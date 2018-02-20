CALL drop_column('sdi_catalog', 'scrolltoresults');
ALTER TABLE `#__sdi_catalog` ADD `scrolltoresults` TINYINT(1) NOT NULL DEFAULT '1';