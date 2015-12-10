CALL drop_column('sdi_map', 'type');
ALTER TABLE `#__sdi_map`
ADD COLUMN `type` VARCHAR(10) DEFAULT 'geoext' AFTER `title`;

CREATE TABLE IF NOT EXISTS `#__sdi_processing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guid` varchar(255) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `accessscope_id` int(11) NOT NULL,
  `command` text,
  `map_id` int(10) NOT NULL,
  `parameters` text,
  `access` int(10) unsigned NOT NULL DEFAULT '1',
  `access_id` int(10) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`),
  KEY `state` (`state`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__sdi_processing_obs` (
  `processing_id` int(10) unsigned NOT NULL,
  `sdi_user_id` int(10) unsigned NOT NULL,
  KEY `processing_id` (`processing_id`),
  KEY `sdi_user_id` (`sdi_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__sdi_processing_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(10) NOT NULL,
  `processing_id` int(10) unsigned NOT NULL,
  `parameters` text NOT NULL,
  `filestorage` varchar(20) NOT NULL,
  `file` text,
  `fileurl` text NOT NULL,
  `output` text,
  `outputpreview` text NOT NULL,
  `exec_pid` int(11) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT '',
  `info` text NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(10) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sent` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `processing_id` (`processing_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;