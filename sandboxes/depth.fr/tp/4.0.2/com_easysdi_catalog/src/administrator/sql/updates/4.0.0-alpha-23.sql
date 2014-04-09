-- Update name
UPDATE `#__sdi_searchcriteria` SET `name` = 'resourcetype' WHERE `name` = 'objecttype';
UPDATE `#__sdi_searchcriteria` SET `name` = 'resourcename' WHERE `name` = 'code';
UPDATE `#__sdi_searchcriteria` SET `name` = 'organism' WHERE `name` = 'account_id';

-- Update rendertype
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 5 WHERE `name` = 'fulltext';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 2 WHERE `name` = 'resourcetype';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 3 WHERE `name` = 'versions';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 5 WHERE `name` = 'resourcename';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 6 WHERE `name` = 'metadata_created';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 6 WHERE `name` = 'metadata_published';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 4 WHERE `name` = 'definedBoundary';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 4 WHERE `name` = 'organism';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 2 WHERE `name` = 'isDownloadable';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 2 WHERE `name` = 'isFree';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 2 WHERE `name` = 'isOrderable';
UPDATE `#__sdi_searchcriteria` SET rendertype_id = 2 WHERE `name` = 'isViewable';
