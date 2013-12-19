INSERT INTO `#__sdi_sys_maptool` (alias,ordering,state,name) 
VALUES 
('layerdetailsheet',18,1,'Layer detail sheet'),
('layerdownload',19,1,'Layer download'),
('layerorder',20,1,'Layer order')
;

ALTER TABLE `#__sdi_visualization` DROP `wmsservice_id` ;
ALTER TABLE `#__sdi_visualization` DROP `wmsservicetype_id` ;
ALTER TABLE `#__sdi_visualization` DROP `layername` ;
ALTER TABLE `#__sdi_visualization` DROP `attribution` ;

ALTER TABLE `#__sdi_maplayer` DROP `user` ;
ALTER TABLE `#__sdi_maplayer` DROP `password` ;