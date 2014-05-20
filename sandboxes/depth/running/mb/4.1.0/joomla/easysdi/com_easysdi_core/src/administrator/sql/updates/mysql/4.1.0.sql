# Changing the constraint # __sdi_catalog_searchsort_fk1 to allow the removal of a catalog.
ALTER TABLE `#__sdi_catalog_searchsort` DROP FOREIGN KEY `#__sdi_catalog_searchsort_fk1`;
ALTER TABLE `#__sdi_catalog_searchsort` ADD CONSTRAINT `#__sdi_catalog_searchsort_fk1` FOREIGN KEY (`catalog_id` ) REFERENCES `#__sdi_catalog` (`id` ) ON DELETE CASCADE ON UPDATE CASCADE;