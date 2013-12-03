-- Update name
UPDATE jos_sdi_searchcriteria SET `name` = 'resourcetype' WHERE `name` = 'objecttype';
UPDATE jos_sdi_searchcriteria SET `name` = 'resourcename' WHERE `name` = 'code';
UPDATE jos_sdi_searchcriteria SET `name` = 'organism' WHERE `name` = 'account_id';

-- Update rendertype
UPDATE jos_sdi_searchcriteria SET rendertype_id = 5 WHERE `name` = 'fulltext';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 2 WHERE `name` = 'resourcetype';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 3 WHERE `name` = 'versions';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 5 WHERE `name` = 'resourcename';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 6 WHERE `name` = 'metadata_created';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 6 WHERE `name` = 'metadata_published';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 4 WHERE `name` = 'definedBoundary';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 4 WHERE `name` = 'organism';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 2 WHERE `name` = 'isDownloadable';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 2 WHERE `name` = 'isFree';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 2 WHERE `name` = 'isOrderable';
UPDATE jos_sdi_searchcriteria SET rendertype_id = 2 WHERE `name` = 'isViewable';
