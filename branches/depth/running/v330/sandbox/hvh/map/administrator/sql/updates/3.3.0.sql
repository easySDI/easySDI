RENAME `#__sdi_map_group` TO `#__sdi_layergroup`;
RENAME `#__sdi_map_layer` TO `#__sdi_maplayer`;
RENAME `#__sdi_map_context` TO `#__sdi_map`;
RENAME `#__sdi_map_context_tool` TO `#__sdi_map_tool`;
RENAME `#__sdi_map_context_physicalservice` TO `#__sdi_map_physicalservice`;
RENAME `#__sdi_map_context_virtualservice` TO `#__sdi_map_virtualservice`;
RENAME `#__sdi_map_context_group` TO `#__sdi_map_layergroup`;
RENAME `#__sdi_sys_map_tool` TO `#__sdi_sys_maptool`;

ALTER TABLE `#__sdi_layergroup` CHANGE `context_id` `map_id` UNSIGNED NOT NULL;
ALTER TABLE `#__sdi_map_physicalservice` CHANGE `context_id` `map_id` UNSIGNED NOT NULL;
ALTER TABLE `#__sdi_map_tool` CHANGE `context_id` `map_id` UNSIGNED NOT NULL;
ALTER TABLE `#__sdi_map_virtualservice` CHANGE `context_id` `map_id` UNSIGNED NOT NULL;

ALTER TABLE `#__sdi_sys_maptool` DROP COLUMN checked_out;
ALTER TABLE `#__sdi_sys_maptool` DROP COLUMN checked_out_time;
ALTER TABLE `#__sdi_sys_maptool` DROP COLUMN created_by;

CREATE TABLE IF NOT EXISTS `#__sdi_layer_layergroup` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`layer_id` INT(11) UNSIGNED NOT NULL ,
`group_id` INT(11) UNSIGNED NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

ALTER TABLE `#__sdi_layer_layergroup`
ADD CONSTRAINT `#__sdi_layer_layergroup_fk1` FOREIGN KEY (`layer_id`) REFERENCES `#__sdi_maplayer` (`id`) ON DELETE CASCADE;

ALTER TABLE `#__sdi_layer_layergroup`
ADD CONSTRAINT `#__sdi_layer_layergroup_fk2` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_layergroup` (`id`) ON DELETE CASCADE;


