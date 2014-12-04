
WITH CTE AS (SELECT [id], RN = ROW_NUMBER() OVER (PARTITION BY [parent_id], [child_id] ORDER BY [id]) FROM [#__sdi_versionlink]);
DELETE FROM CTE WHERE RN>1;
ALTER TABLE [#__sdi_versionlink] ADD CONSTRAINT [#__sdi_versionlink$#__sdi_versionlink_uk] UNIQUE ([parent_id], [child_id]);
