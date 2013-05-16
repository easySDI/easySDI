CREATE TABLE #__easysdi_overlay_definition LIKE #__easysdi_basemap_definition;
CREATE TABLE #__easysdi_overlay_content LIKE #__easysdi_basemap_content;
	CREATE TABLE #__easysdi_overlay_group(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(30)
	);
	
ALTER TABLE #__easysdi_overlay_content 
CHANGE basemap_def_id overlay_def_id BIGINT(20),
ADD COLUMN (overlay_group_id BIGINT(20));