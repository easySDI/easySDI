RENAME `#__sdi_map_group` TO `#__sdi_layergroup`;
RENAME `#__sdi_map_layer` TO `#__sdi_layer`;
RENAME `#__sdi_map_context` TO `#__sdi_map`;
RENAME `#__sdi_map_context_tool` TO `#__sdi_map_tool`;
RENAME `#__sdi_map_context_physicalservice` TO `#__sdi_map_physicalservice`;
RENAME `#__sdi_map_context:virtualservice` TO `#__sdi_map_virtualservice`;
RENAME `#__sdi_map_context_group` TO `#__sdi_layergroup`;

ALTER TABLE `#__sdi_layergroup` CHANGE `context_id` `map_id` UNSIGNED NOT NULL;
ALTER TABLE `#__sdi_map_physicalservice` CHANGE `context_id` `map_id` UNSIGNED NOT NULL;
ALTER TABLE `#__sdi_map_tool` CHANGE `context_id` `map_id` UNSIGNED NOT NULL;
ALTER TABLE `#__sdi_map_virtualservice` CHANGE `context_id` `map_id` UNSIGNED NOT NULL;
