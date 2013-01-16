<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 */


defined('_JEXEC') or die('Restricted access');
define("TABLE_MIGRATE", 1); // this runs the migrations for easysdi tables



function com_install(){


		global  $mainframe;
		$db =& JFactory::getDBO();

		/*if (!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'license.txt')){
		 $mainframe->enqueueMessage("Core component does not exists. Easysdi_catalog could not be installed. Please install core component first.","ERROR");
		 return false;
		 }*/

		/**
		 * Check the CORE installation
		 */
		$count = 0;
		$query = "SELECT COUNT(*) FROM `#__components` WHERE `option` ='com_easysdi_core'";
		$db->setQuery( $query);
		$count = $db->loadResult();
		if ($count == 0) {
			$mainframe->enqueueMessage("Core component does not exist. Easysdi Catalog could not be installed. Please install core component first.","ERROR");
			/**
			 * Delete components
			 */
			$query = "DELETE FROM #__components where `option`= 'com_easysdi_catalog'";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			return false;
		}

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');

		$user =& JFactory::getUser();
		$user_id = $user->get('id');

		$version = '0.0';
		$query = "SELECT currentversion FROM `#__sdi_list_module` where `code` = 'CATALOG'";
		$db->setQuery( $query);
		$version = $db->loadResult();
		if (!$version)
		{
			$version= '0.1';
			$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion)
				VALUES ('".helper_easysdi::getUniqueId()."', 'CATALOG', 'com_easysdi_catalog', 'com_easysdi_catalog', '".date('Y-m-d H:i:s')."', '".$user_id."', 'com_sdi_catalog', 'com_sdi_catalog', '".$version."')";
			$db->setQuery( $query);

			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
		}
		if ($db->getErrorNum())
		{
			//The table doesn't exist
			//That means nothing is installed.
			$mainframe->enqueueMessage("EASYSDI IS NOT INSTALLED","ERROR");
			exit;
		}
		if ($version == "0.1")
		{
			$query = "SELECT id FROM `#__sdi_list_module` where code = 'CATALOG'";
			$db->setQuery( $query);
			$id = $db->loadResult();

			$query="INSERT INTO #__sdi_module_panel (guid, code, name, description, created, createdby,module_id, view_path,ordering)
										VALUES ('".helper_easysdi::getUniqueId()."', 'CATALOG_PANEL', 'Catalog Panel', 'Catalog Panel', '".date('Y-m-d H:i:s')."', '".$user_id."', '".$id."', 'com_easysdi_catalog/core/view/sub.ctrlpanel.admin.easysdi.html.php', '2')";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			/**
			 * Insert value for CATALOG_URL in configuration table
			 */
			$key='';
			$query = "INSERT INTO #__sdi_configuration (guid, code, name, description, created, createdby, label, value, module_id) VALUES
				  ('".helper_easysdi::getUniqueId()."', 'JAVA_BRIDGE_URL', 'JAVA_BRIDGE_URL', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'http://localhost:8080/exportpdf/PdfServlet', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_URL', 'CATALOG_URL', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'http://localhost:8081/proxy/ogc/geonetwork', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_BOUNDARY_ISOCODE', 'CATALOG_BOUNDARY_ISOCODE', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'gmd:EX_GeographicBoundingBox', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_BOUNDARY_NORTH', 'CATALOG_BOUNDARY_NORTH', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'gmd:northBoundLatitude', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_BOUNDARY_SOUTH', 'CATALOG_BOUNDARY_SOUTH', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'gmd:southBoundLatitude', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_BOUNDARY_EAST', 'CATALOG_BOUNDARY_EAST', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'gmd:eastBoundLongitude', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_BOUNDARY_WEST', 'CATALOG_BOUNDARY_WEST', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'gmd:westBoundLongitude', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_BOUNDARY_TYPE', 'CATALOG_BOUNDARY_TYPE', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '4', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_ENCODING_CODE', 'CATALOG_ENCODING_CODE', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'UTF8', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_ENCODING_VAL', 'CATALOG_ENCODING_VAL', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'utf8', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'METADATA_COLLAPSE', 'METADATA_COLLAPSE', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'true', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_SEARCH_MULTILIST_LENGTH', 'CATALOG_SEARCH_MULTILIST_LENGTH', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '4', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_METADATA_QTIPDELAY', 'CATALOG_METADATA_QTIPDELAY', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '10000', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_PAGINATION_SEARCHRESULT', 'CATALOG_PAGINATION_SEARCHRESULT', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '20', '".$id."'),
				  ('".helper_easysdi::getUniqueId()."', 'CATALOG_SEARCH_OGCFILTERFILEID', 'CATALOG_SEARCH_OGCFILTERFILEID', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'fileId', '".$id."')
				 ";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			/*
			 * Create and complete system tables
			 */
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_attributetype` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20),
				  `name` varchar(50) NOT NULL,
				  `description` varchar(100),
				  `created` datetime NOT NULL,
				  `updated` datetime,
				  `createdby` bigint(20),
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `ordering` bigint(20) NOT NULL DEFAULT 0,
				  `defaultpattern` varchar(200),
				  `isocode` varchar(50),
				  `namespace_id` bigint(20),
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`),
				  UNIQUE KEY `code` (`code`),
				  KEY `namespace_id` (`namespace_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			 
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query = "INSERT INTO #__sdi_list_attributetype (guid, code, name, description, created, createdby, label, defaultpattern, isocode, namespace_id) VALUES
					('".helper_easysdi::getUniqueId()."', 'guid', 'guid', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_GUID', '([A-Z0-9]{8}|-|[A-Z0-9]{4}|-|[A-Z0-9]{4}|-|[A-Z0-9]{4}|-|[A-Z0-9]{12})', 'CharacterString', 2),
					('".helper_easysdi::getUniqueId()."', 'text', 'text', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_TEXT', '^[a-zA-Z0-9_]{1,}$', 'CharacterString', 2),
					('".helper_easysdi::getUniqueId()."', 'locale', 'locale', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_LOCALE', '^[a-zA-Z0-9_]{1,}$', null, null),
					('".helper_easysdi::getUniqueId()."', 'number', 'number', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_NUMBER', '[0-9\\.\\-]', 'Decimal', 2),
					('".helper_easysdi::getUniqueId()."', 'date', 'date', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_DATE', '(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}', 'Date', 2),
					('".helper_easysdi::getUniqueId()."', 'list', 'list', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_LIST', '^[a-zA-Z0-9_]{1,}$', null, null),
					('".helper_easysdi::getUniqueId()."', 'link', 'link', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_LINK', '^[a-zA-Z0-9_]{1,}$', 'CharacterString', 2),
					('".helper_easysdi::getUniqueId()."', 'datetime', 'datetime', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_DATETIME', '(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}', 'DateTime', 2),
					('".helper_easysdi::getUniqueId()."', 'textchoice', 'textchoice', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_TEXTCHOICE', '^[a-zA-Z0-9_]{1,}$', 'CharacterString', 2),
					('".helper_easysdi::getUniqueId()."', 'localechoice', 'localechoice', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_LOCALECHOICE', '^[a-zA-Z0-9_]{1,}$', null, null),
					('".helper_easysdi::getUniqueId()."', 'Thesaurus GEMET', 'Thesaurus GEMET', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_THESAURUS', '', null, null)";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_relationtype` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20),
				  `name` varchar(50) NOT NULL,
				  `description` varchar(100),
				  `created` date NOT NULL,
				  `updated` date,
				  `createdby` bigint(20),
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `ordering` bigint(20) NOT NULL DEFAULT 0,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`),
				  UNIQUE KEY `code` (`code`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			 
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query = "INSERT INTO #__sdi_list_relationtype (guid, code, name, description, created, createdby, label) VALUES
					('".helper_easysdi::getUniqueId()."', 'association', 'association', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'association'),
					('".helper_easysdi::getUniqueId()."', 'aggregation', 'aggregation', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'aggregation'),
					('".helper_easysdi::getUniqueId()."', 'composition', 'composition', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'composition'),
					('".helper_easysdi::getUniqueId()."', 'generalization', 'generalization', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'generalization')
			";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_rendertype` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20),
				  `name` varchar(50) NOT NULL,
				  `description` varchar(100),
				  `created` date NOT NULL,
				  `updated` date,
				  `createdby` bigint(20),
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `ordering` bigint(20) NOT NULL DEFAULT 0,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`),
				  UNIQUE KEY `code` (`code`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			 
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query = "INSERT INTO #__sdi_list_rendertype (guid, code, name, description, created, createdby, label) VALUES
					('".helper_easysdi::getUniqueId()."', 'textarea', 'textarea', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'textarea'),
					('".helper_easysdi::getUniqueId()."', 'checkbox', 'checkbox', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'checkbox'),
					('".helper_easysdi::getUniqueId()."', 'radiobutton', 'radiobutton', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'radiobutton'),
					('".helper_easysdi::getUniqueId()."', 'list', 'list', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'list'),
					('".helper_easysdi::getUniqueId()."', 'textbox', 'textbox', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'textbox'),
					('".helper_easysdi::getUniqueId()."', 'date', 'date', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'date')";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_renderattributetype` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `attributetype_id` bigint(20) NOT NULL,
				  `rendertype_id` bigint(20) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `attributetype_id` (`attributetype_id`),
				  KEY `rendertype_id` (`rendertype_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			 
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query = "INSERT INTO #__sdi_list_renderattributetype (attributetype_id, rendertype_id) VALUES
					( 1, 5),
					( 2, 1),
					( 2, 5),
					( 3, 1),
					( 3, 5),
					( 4, 1),
					( 4, 5),
					( 5, 1),
					( 6, 2),
					( 6, 3),
					( 6, 4),
					( 7, 1),
					( 7, 5),
					( 8, 1),
					( 8, 5),
					( 9, 4),
					( 10, 4)";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_rendercriteriatype` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `criteriatype_id` bigint(20) NOT NULL,
				  `rendertype_id` bigint(20) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `criteriatype_id` (`criteriatype_id`),
				  KEY `rendertype_id` (`rendertype_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			 
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query = "INSERT INTO #__sdi_list_rendercriteriatype (criteriatype_id, rendertype_id) VALUES
					( 3, 5),
					( 3, 6)";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			/*
			 * End of system tables
			 */

			/*
			 * Profile table. Contains standard.
			 * Filling by the user interface.
			 */
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_profile` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36),
				  `code` varchar(20),
				  `name` varchar(50) NOT NULL,
				  `description` varchar(100),
				  `created` datetime NOT NULL,
				  `updated` datetime,
				  `createdby` bigint(20),
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `ordering` bigint(20) NOT NULL DEFAULT 0,
				  `class_id` bigint(20) NOT NULL,
				  `metadataid` bigint(20) NOT NULL DEFAULT 0,
				  PRIMARY KEY (`id`),
				  KEY `class_id` (`class_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			 
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			/*
			 * Class table. Contains classes defining the metadatas.
			 * Filling by the user interface.
			 */
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_class` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36),
				  `code` varchar(20),
				  `name` varchar(50) NOT NULL,
				  `description` varchar(100),
				  `created` datetime NOT NULL,
				  `updated` datetime,
				  `createdby` bigint(20),
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `ordering` bigint(20) NOT NULL DEFAULT 0,
				  `isocode` varchar(50) NOT NULL,
				  `isextensible` tinyint(1) DEFAULT '0',
				  `issystem` tinyint(1) DEFAULT '0',
				  `isrootclass` tinyint(1),
				  `checked_out` bigint(20) NOT NULL,
				  `checked_out_time` datetime,
				  `namespace_id` bigint(20),
				  PRIMARY KEY (`id`),
				  KEY `namespace_id` (`namespace_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			 
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			/*
			 * Attribute table. Contains attributes defining the metadatas.
			 * Filling by the user interface.
			 */
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_attribute` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20),
				  `name` varchar(50) NOT NULL,
				  `description` varchar(100),
				  `created` datetime NOT NULL,
				  `updated` datetime,
				  `createdby` bigint(20),
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `ordering` bigint(20) NOT NULL DEFAULT 0,
				  `isocode` varchar(50) NOT NULL,
				  `information` varchar(200),
				  `attributetype_id` bigint(20) NOT NULL,
				  `default` varchar(4000),
				  `length` bigint(20),
				  `pattern` varchar(500),
				  `issystem` tinyint(1),
				  `isextensible` tinyint(1),
				  `type_isocode` varchar(50),
				  `codeList` varchar(200),
				  `checked_out` bigint(20) NOT NULL,
				  `checked_out_time` datetime,
				  `namespace_id` bigint(20),
				  `listnamespace_id` bigint(20),
				  PRIMARY KEY (`id`),
				  KEY `attributetype_id` (`attributetype_id`),
				  KEY `namespace_id` (`namespace_id`),
				  KEY `listnamespace_id` (`listnamespace_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			 
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}


			/*
			 * CodeValue table. Define the items attributes list type.
			 * Filling by the user interface.
			 */
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_codevalue` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20),
				  `name` varchar(50) NOT NULL,
				  `description` varchar(100),
				  `created` datetime NOT NULL,
				  `updated` datetime,
				  `createdby` bigint(20),
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `ordering` bigint(20) NOT NULL DEFAULT 0,
				  `isocode` varchar(50),
				  `value` varchar(50),
				  `attribute_id` bigint(20) NOT NULL,
				  `checked_out` bigint(20) NOT NULL,
				  `checked_out_time` datetime,
				  `published` tinyint(1) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `attribute_id` (`attribute_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			 
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_translation` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `element_guid` varchar(36) NOT NULL,
				  `language_id` bigint(20) NOT NULL,
				  `label` varchar(200),
				  `defaultvalue` varchar(4000),
				  `information` varchar(200),
				  `created` datetime NOT NULL,
				  `updated` datetime,
				  `createdby` bigint(20),
				  `updatedby` bigint(20),
				  `regexmsg` varchar(200),
				  `title` varchar(100),
				  `content` varchar(500),
				  PRIMARY KEY (`id`),
				  KEY `language_id` (`language_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_defaultvalue` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `attribute_id` bigint(20) NOT NULL,
				  `codevalue_id` bigint(20) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `attribute_id` (`attribute_id`),
				  KEY `codevalue_id` (`codevalue_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_relation` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `parent_id` bigint(20) NOT NULL,
				  `attributechild_id` bigint(20) DEFAULT NULL,
				  `classchild_id` bigint(20) DEFAULT NULL,
				  `name` varchar(50) NOT NULL,
				  `lowerbound` bigint(20),
				  `upperbound` bigint(20),
				  `rendertype_id` bigint(20) DEFAULT NULL,
				  `relationtype_id` bigint(20) DEFAULT NULL,
				  `isocode` varchar(50) DEFAULT NULL,
				  `description` varchar(100) DEFAULT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime DEFAULT NULL,
				  `createdby` bigint(20) DEFAULT NULL,
				  `updatedby` bigint(20) DEFAULT NULL,
				  `ordering` bigint(20) NOT NULL DEFAULT 0,
				  `published` tinyint(4) NOT NULL DEFAULT '0',
				  `classassociation_id` bigint(20) DEFAULT NULL,
				  `checked_out` bigint(20) NOT NULL,
				  `checked_out_time` datetime,
				  `objecttypechild_id` bigint(20) DEFAULT NULL,
				  `namespace_id` bigint(20) DEFAULT NULL,
				  `issearchfilter` tinyint(4) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `parent_id` (`parent_id`),
				  KEY `attributechild_id` (`attributechild_id`),
				  KEY `classchild_id` (`classchild_id`),
				  KEY `rendertype_id` (`rendertype_id`),
				  KEY `relationtype_id` (`relationtype_id`),
				  KEY `classassociation_id` (`classassociation_id`),
				  KEY `objecttypechild_id` (`objecttypechild_id`),
				  KEY `namespace_id` (`namespace_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_relation_profile` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `relation_id` bigint(20) NOT NULL,
			  `profile_id` bigint(20) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `relation_id` (`relation_id`,`profile_id`),
			  KEY `profile_id` (`profile_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_manager_object` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `account_id` bigint(20) NOT NULL,
				  `object_id` bigint(20) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `account_id` (`account_id`),
				  KEY `object_id` (`object_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_editor_object` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `account_id` bigint(20) NOT NULL,
				  `object_id` bigint(20) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `account_id` (`account_id`),
				  KEY `object_id` (`object_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_boundary` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20) DEFAULT NULL,
				  `name` varchar(50) NOT NULL,
				  `description` varchar(100) DEFAULT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime DEFAULT NULL,
				  `createdby` bigint(20) DEFAULT NULL,
				  `updatedby` bigint(20) DEFAULT NULL,
				  `ordering` bigint(20) NOT NULL DEFAULT '0',
				  `northbound` double DEFAULT NULL,
				  `southbound` double DEFAULT NULL,
				  `eastbound` double DEFAULT NULL,
				  `westbound` double DEFAULT NULL,
				  `checked_out` bigint(20) NOT NULL DEFAULT '0',
				  `checked_out_time` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_history_assign` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `assigned` datetime NOT NULL,
				  `assignedby` bigint(20) NOT NULL,
				  `object_id` bigint(20) DEFAULT NULL,
				  `objectversion_id` bigint(20) DEFAULT NULL,
				  `account_id` bigint(20) DEFAULT NULL,
				  `information` varchar(2000) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`),
				  KEY `object_id` (`object_id`),
				  KEY `objectversion_id` (`objectversion_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_namespace` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20) DEFAULT NULL,
				  `name` varchar(50) NOT NULL,
				  `description` varchar(50) DEFAULT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime DEFAULT NULL,
				  `createdby` bigint(20),
				  `updatedby` bigint(20) DEFAULT NULL,
				  `ordering` bigint(20) NOT NULL DEFAULT '0',
				  `prefix` varchar(10) NOT NULL,
				  `uri` varchar(100) NOT NULL,
				  `issystem` tinyint(1) DEFAULT 0,
				  `checked_out` bigint(20) NOT NULL DEFAULT '0',
				  `checked_out_time` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__sdi_namespace` (`guid`, `name`, `description`, `created`, `createdby`, `ordering`, `prefix`, `uri`, `issystem`) VALUES
					('".helper_easysdi::getUniqueId()."', 'gmd', '', '".date('Y-m-d H:i:s')."', ".$user_id.", 1, 'gmd', 'http://www.isotc211.org/2005/gmd', 1),
					('".helper_easysdi::getUniqueId()."', 'gco', '', '".date('Y-m-d H:i:s')."', ".$user_id.", 2, 'gco', 'http://www.isotc211.org/2005/gco', 1),
					('".helper_easysdi::getUniqueId()."', 'gml', '', '".date('Y-m-d H:i:s')."', ".$user_id.", 0, 'gml', 'http://www.opengis.net/gml', 1)
					;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE  IF NOT EXISTS `#__sdi_list_topiccategory` (
			  	  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(100) NOT NULL UNIQUE default '',
				  `name` varchar(50) NOT NULL,
				  `description` varchar(50) DEFAULT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime DEFAULT NULL,
				  `createdby` bigint(20),
				  `updatedby` bigint(20) DEFAULT NULL,
				  `ordering` bigint(20) NOT NULL DEFAULT '0',
				  `label` varchar(100) NOT NULL default '',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			$query = "INSERT INTO `#__sdi_list_topiccategory` (`guid`, `code`, `name`, `label`, `created`, `createdby`) VALUES
				  ('".helper_easysdi::getUniqueId()."', 'farming', 'farming', 'EASYSDI_METADATA_CATEGORY_FARMING', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'biota', 'biota', 'EASYSDI_METADATA_CATEGORY_BIOTA', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'bounderies', 'bounderies', 'EASYSDI_METADATA_CATEGORY_BOUNDERIES', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'climatologyMeteorologyAtmosphere', 'climatologyMeteorologyAtmosphere', 'EASYSDI_METADATA_CATEGORY_CLIMATOLOGYMETEOROLOGYATMOSPHERE', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'economy', 'economy', 'EASYSDI_METADATA_CATEGORY_ECONOMY', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'elevation', 'elevation', 'EASYSDI_METADATA_CATEGORY_ELEVATION', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'environment', 'environment', 'EASYSDI_METADATA_CATEGORY_ENVIRONMENT', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'geoscientificinformation', 'geoscientificinformation', 'EASYSDI_METADATA_CATEGORY_GEOSCIENTIFICINFORMATION', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'health', 'health', 'EASYSDI_METADATA_CATEGORY_HEALTH', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'imageryBaseMapsEarthCover', 'imageryBaseMapsEarthCover', 'EASYSDI_METADATA_CATEGORY_IMAGERYBASEMAPSEARTHCOVER', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'intelligenceMilitary', 'intelligenceMilitary', 'EASYSDI_METADATA_CATEGORY_INTELLIGENCEMILITARY', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'inlandWaters', 'inlandWaters', 'EASYSDI_METADATA_CATEGORY_INLANDWATERS', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'location', 'location', 'EASYSDI_METADATA_CATEGORY_LOCATION', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'oceans', 'oceans', 'EASYSDI_METADATA_CATEGORY_OCEANS', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'planningCadastre', 'planningCadastre', 'EASYSDI_METADATA_CATEGORY_PLANNINGCADASTRE', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'society', 'society', 'EASYSDI_METADATA_CATEGORY_SOCIETY', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'structure', 'structure', 'EASYSDI_METADATA_CATEGORY_STRUCTURE', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'transportation', 'transportation', 'EASYSDI_METADATA_CATEGORY_TRANSPORTATION', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'utilitiesCommunication', 'utilitiesCommunication', 'EASYSDI_METADATA_CATEGORY_UTILITIESCOMMUNICATION', '".date('Y-m-d H:i:s')."', ".$user_id.")
				";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE  IF NOT EXISTS `#__sdi_importref` (
			  	  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(100) NOT NULL,
				  `name` varchar(50) NOT NULL,
				  `description` varchar(50) DEFAULT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime DEFAULT NULL,
				  `createdby` bigint(20),
				  `updatedby` bigint(20) DEFAULT NULL,
				  `ordering` bigint(20) NOT NULL DEFAULT '0',
				  `xslfile` varchar(200) NOT NULL,
				  `pretreatmentxslfile` varchar(200),
				  `url` varchar(200),
				  `checked_out` bigint(20) NOT NULL,
				  `checked_out_time` datetime DEFAULT NULL,
				  `importtype_id` bigint(20) DEFAULT 1,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`),
				  KEY `importtype_id` (`importtype_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_importtype` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20) DEFAULT NULL,
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100) DEFAULT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime DEFAULT NULL,
					  `createdby` bigint(20),
					  `updatedby` bigint(20) DEFAULT NULL,
					  `label` varchar(50) DEFAULT NULL,
					  `ordering` bigint(20) DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			$query="INSERT INTO `#__sdi_list_importtype` (`guid`, `code`, `name`, `description`, `created`, `createdby`, `label`) VALUES
				('".helper_easysdi::getUniqueId()."', 'replace', 'Replacing', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_IMPORTEF_REPLACING'),
				('".helper_easysdi::getUniqueId()."', 'merge', 'Merging', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_IMPORTEF_MERGING')
				";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE  IF NOT EXISTS `#__sdi_objecttypelink` (
			  	  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `parent_id` bigint(20) NOT NULL,
				  `child_id` bigint(20) NOT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime DEFAULT NULL,
				  `createdby` bigint(20),
				  `updatedby` bigint(20) DEFAULT NULL,
				  `ordering` bigint(20) NOT NULL DEFAULT '0',
				  `flowdown_versioning` tinyint(1) NOT NULL DEFAULT '0',
				  `escalate_versioning_update` tinyint(1) NOT NULL DEFAULT '0',
				  `checked_out` bigint(20) NOT NULL,
				  `checked_out_time` datetime DEFAULT NULL,
				  `parentbound_lower` bigint(20) NOT NULL DEFAULT '0',
				  `parentbound_upper` bigint(20) NOT NULL DEFAULT '999',
				  `childbound_lower` bigint(20) NOT NULL DEFAULT '0',
				  `childbound_upper` bigint(20) NOT NULL DEFAULT '999',
				  `class_id` bigint(20),
				  `attribute_id` bigint(20),
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE  IF NOT EXISTS `#__sdi_objectversion` (
			  	  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `object_id` bigint(20) NOT NULL,
				  `metadata_id` bigint(20) NOT NULL,
				  `parent_id` bigint(20),
				  `code` varchar(20),
				  `name` varchar(50),
				  `title` datetime NOT NULL,
				  `description` varchar(100),
			  	  `created` datetime NOT NULL,
				  `createdby` bigint(20),
				  `updated` datetime,
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `checked_out` bigint(20) NOT NULL,
				  `checked_out_time` datetime DEFAULT NULL,
				  `ordering` bigint(20) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `object_id` (`object_id`),
				  KEY `metadata_id` (`metadata_id`),
				  KEY `parent_id` (`parent_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE  IF NOT EXISTS `#__sdi_objectversionlink` (
			  	  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `parent_id` bigint(20) NOT NULL,
				  `child_id` bigint(20) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `parent_id` (`parent_id`),
				  KEY `child_id` (`child_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}


			$query="CREATE TABLE  IF NOT EXISTS `#__sdi_context` (
			  	  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20) NOT NULL,
				  `name` varchar(50) NOT NULL,
				  `description` varchar(100),
				  `created` datetime NOT NULL,
				  `createdby` bigint(20),
				  `updated` datetime,
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `ordering` bigint(20) NOT NULL DEFAULT 0,
				  `objecttype_id` bigint(20),
				  `checked_out` bigint(20) NOT NULL  DEFAULT 0,
				  `checked_out_time` datetime,
				  `xsldirectory` varchar(150),
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `code` (`code`),
				  KEY `objecttype_id` (`objecttype_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_context_objecttype` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `context_id` bigint(20) NOT NULL,
				  `objecttype_id` bigint(20) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `context_id` (`context_id`),
				  KEY `objecttype_id` (`objecttype_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_criteriatype` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20) NOT NULL DEFAULT '',
				  `name` varchar(50) NOT NULL,
				  `description` varchar(50) DEFAULT NULL,
				  `label` varchar(100) DEFAULT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime DEFAULT NULL,
				  `createdby` bigint(20),
				  `updatedby` bigint(20) DEFAULT NULL,
				  `ordering` bigint(20) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query = "INSERT INTO `#__sdi_list_criteriatype` (`guid`, `code`, `name`, `label`, `created`, `createdby`) VALUES
				  ('".helper_easysdi::getUniqueId()."', 'system', 'system', 'CATALOG_CRITERIATYPE_SYSTEM', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'relation', 'relation', 'CATALOG_CRITERIATYPE_RELATION', '".date('Y-m-d H:i:s')."', ".$user_id."),
				  ('".helper_easysdi::getUniqueId()."', 'csw', 'csw', 'CATALOG_CRITERIATYPE_CSW', '".date('Y-m-d H:i:s')."', ".$user_id.")
				";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_searchcriteria` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(100) NOT NULL DEFAULT '',
				  `name` varchar(50) NOT NULL,
				  `description` varchar(50) DEFAULT NULL,
				  `label` varchar(100) DEFAULT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime DEFAULT NULL,
				  `createdby` bigint(20),
				  `updatedby` bigint(20) DEFAULT NULL,
				  `ordering` bigint(20) NOT NULL DEFAULT '0',
				  `checked_out` bigint(20) NOT NULL,
				  `checked_out_time` datetime DEFAULT NULL,
				  `issystem` tinyint(4) NOT NULL DEFAULT '0',
				  `simpletab` tinyint(1) NOT NULL DEFAULT '0',
				  `advancedtab` tinyint(1) DEFAULT '0',
				  `relation_id` bigint(20) DEFAULT NULL,
				  `criteriatype_id` bigint(20) NOT NULL DEFAULT 1,
				  `context_id` bigint(20) DEFAULT NULL,
  				  `rendertype_id` bigint(20) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`),
				  KEY `relation_id` (`relation_id`),
				  KEY `criteriatype_id` (`criteriatype_id`),
				  KEY `context_id` (`context_id`),
  				  KEY `rendertype_id` (`rendertype_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query = "INSERT INTO `#__sdi_searchcriteria` (`guid`, `code`, `name`, `label`, `created`, `createdby`, `criteriatype_id`) VALUES
				  ('".helper_easysdi::getUniqueId()."', 'fulltext', 'fulltext', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_FULLTEXT', '".date('Y-m-d H:i:s')."', ".$user_id.", 1),
				  ('".helper_easysdi::getUniqueId()."', 'objecttype', 'objecttype', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_OBJECTTYPE', '".date('Y-m-d H:i:s')."', ".$user_id.", 1),
				  ('".helper_easysdi::getUniqueId()."', 'versions', 'versions', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_VERSION', '".date('Y-m-d H:i:s')."', ".$user_id.", 1),
				  ('".helper_easysdi::getUniqueId()."', 'object_name', 'code', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_CODE', '".date('Y-m-d H:i:s')."', ".$user_id.", 1),
				  ('".helper_easysdi::getUniqueId()."', 'metadata_created', 'metadata_created', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_METADATACREATED', '".date('Y-m-d H:i:s')."', ".$user_id.", 1),
				  ('".helper_easysdi::getUniqueId()."', 'metadata_published', 'metadata_published', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_METADATAPUBLISHED', '".date('Y-m-d H:i:s')."', ".$user_id.", 1),
				  ('".helper_easysdi::getUniqueId()."', 'account_id', 'account_id', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_ACCOUNTID', '".date('Y-m-d H:i:s')."', ".$user_id.", 1)
				";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_searchtab` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20) DEFAULT NULL,
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100) DEFAULT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime DEFAULT NULL,
					  `createdby` bigint(20),
					  `updatedby` bigint(20) DEFAULT NULL,
					  `label` varchar(50) DEFAULT NULL,
					  `ordering` bigint(20) DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			$query="INSERT INTO `#__sdi_list_searchtab` (`guid`, `code`, `name`, `description`, `created`, `createdby`, `label`) VALUES
				('".helper_easysdi::getUniqueId()."', 'simple', 'Simple', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_SEARCHTAB_SIMPLE'),
				('".helper_easysdi::getUniqueId()."', 'advanced', 'Advanced', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_SEARCHTAB_ADVANCED')
				";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
				
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_searchcriteria_tab` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `searchcriteria_id` bigint(20) NOT NULL,
				  `context_id` bigint(20) NOT NULL,
				  `tab_id` bigint(20) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `searchcriteria_id` (`searchcriteria_id`),
				  KEY `context_id` (`context_id`),
				  KEY `tab_id` (`tab_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_context_sc_filter` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `searchcriteria_id` bigint(20) NOT NULL,
				  `context_id` bigint(20) NOT NULL,
				  `language_id` bigint(20) DEFAULT NULL,
				  `ogcsearchfilter` varchar(100) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `searchcriteria_id` (`searchcriteria_id`),
				  KEY `context_id` (`context_id`),
				  KEY `language_id` (`language_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_context_sort` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `context_id` bigint(20) NOT NULL,
				  `language_id` bigint(20) DEFAULT NULL,
				  `ogcsearchsorting` varchar(100) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `context_id` (`context_id`),
				  KEY `language_id` (`language_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_relation_context` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `relation_id` bigint(20) NOT NULL,
				  `context_id` bigint(20) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `context_id` (`context_id`),
				  KEY `relation_id` (`relation_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_application` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20) DEFAULT NULL,
				  `name` varchar(50) NOT NULL,
				  `description` varchar(100) DEFAULT NULL,
				  `created` datetime NOT NULL,
				  `updated` datetime DEFAULT NULL,
				  `createdby` bigint(20) DEFAULT NULL,
				  `updatedby` bigint(20) DEFAULT NULL,
				  `ordering` bigint(20) NOT NULL DEFAULT '0',
				  `windowname` varchar(50) NOT NULL,
				  `url` varchar(200) NOT NULL,
				  `options` varchar(200) NOT NULL,
				  `object_id` bigint(20) NOT NULL,
				  `checked_out` bigint(20) NOT NULL DEFAULT '0',
				  `checked_out_time` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`),
  				  KEY `object_id` (`object_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}


			/*
			 * Constraints
			 */

			$query="ALTER TABLE `#__sdi_list_renderattributetype`
  				ADD CONSTRAINT `#__sdi_list_renderattributetype_ibfk_1` FOREIGN KEY (`rendertype_id`) REFERENCES `#__sdi_list_rendertype` (`id`),
				ADD CONSTRAINT `#__sdi_list_renderattributetype_ibfk_2` FOREIGN KEY (`attributetype_id`) REFERENCES `#__sdi_list_attributetype` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_list_rendercriteriatype`
  				ADD CONSTRAINT `#__sdi_list_rendercriteriatype_ibfk_1` FOREIGN KEY (`rendertype_id`) REFERENCES `#__sdi_list_rendertype` (`id`),
				ADD CONSTRAINT `#__sdi_list_rendercriteriatype_ibfk_2` FOREIGN KEY (`criteriatype_id`) REFERENCES `#__sdi_list_criteriatype` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_class`
  				ADD CONSTRAINT `#__sdi_class_ibfk_2` FOREIGN KEY (`namespace_id`) REFERENCES `#__sdi_namespace` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_attribute`
  				ADD CONSTRAINT `#__sdi_attribute_ibfk_1` FOREIGN KEY (`attributetype_id`) REFERENCES `#__sdi_list_attributetype` (`id`),
				ADD CONSTRAINT `#__sdi_attribute_ibfk_2` FOREIGN KEY (`namespace_id`) REFERENCES `#__sdi_namespace` (`id`),
				ADD CONSTRAINT `#__sdi_attribute_ibfk_3` FOREIGN KEY (`listnamespace_id`) REFERENCES `#__sdi_namespace` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_list_attributetype`
  				ADD CONSTRAINT `#__sdi_list_attributetype_ibfk_1` FOREIGN KEY (`namespace_id`) REFERENCES `#__sdi_namespace` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_codevalue`
  				ADD CONSTRAINT `#__sdi_codevalue_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `#__sdi_attribute` (`id`) ON DELETE CASCADE;
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_account_attribute`
				ADD CONSTRAINT `#__sdi_account_attribute_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `#__sdi_account` (`id`),
				ADD CONSTRAINT `#__sdi_account_attribute_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `#__sdi_attribute` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_account_codevalue`
				ADD CONSTRAINT `#__sdi_account_codevalue_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `#__sdi_account` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_account_object`
				ADD CONSTRAINT `#__sdi_account_object_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `#__sdi_object` (`id`),
				ADD CONSTRAINT `#__sdi_account_object_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `#__sdi_account` (`id`)
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_profile`
  				ADD CONSTRAINT `#__sdi_profile_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `#__sdi_class` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_objecttype`
  				ADD CONSTRAINT `#__sdi_objecttype_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `#__sdi_profile` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_defaultvalue`
				ADD CONSTRAINT `#__sdi_defaultvalue_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `#__sdi_attribute` (`id`),
				ADD CONSTRAINT `#__sdi_defaultvalue_ibfk_2` FOREIGN KEY (`codevalue_id`) REFERENCES `#__sdi_codevalue` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_relation`
				ADD CONSTRAINT `#__sdi_relation_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `#__sdi_class` (`id`),
				ADD CONSTRAINT `#__sdi_relation_ibfk_2` FOREIGN KEY (`attributechild_id`) REFERENCES `#__sdi_attribute` (`id`),
				ADD CONSTRAINT `#__sdi_relation_ibfk_3` FOREIGN KEY (`classchild_id`) REFERENCES `#__sdi_class` (`id`),
				ADD CONSTRAINT `#__sdi_relation_ibfk_4` FOREIGN KEY (`rendertype_id`) REFERENCES `#__sdi_list_rendertype` (`id`),
				ADD CONSTRAINT `#__sdi_relation_ibfk_5` FOREIGN KEY (`relationtype_id`) REFERENCES `#__sdi_list_relationtype` (`id`),
				ADD CONSTRAINT `#__sdi_relation_ibfk_6` FOREIGN KEY (`classassociation_id`) REFERENCES `#__sdi_class` (`id`),
				ADD CONSTRAINT `#__sdi_relation_ibfk_7` FOREIGN KEY (`objecttypechild_id`) REFERENCES `#__sdi_objecttype` (`id`),
				ADD CONSTRAINT `#__sdi_relation_ibfk_8` FOREIGN KEY (`namespace_id`) REFERENCES `#__sdi_namespace` (`id`);
				";

			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_relation_profile`
				ADD CONSTRAINT `#__sdi_relation_profile_ibfk_1` FOREIGN KEY (`relation_id`) REFERENCES `#__sdi_relation` (`id`),
				ADD CONSTRAINT `#__sdi_relation_profile_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `#__sdi_profile` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query = "ALTER TABLE `#__sdi_manager_object`
				  ADD CONSTRAINT `#__sdi_manager_object_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `#__sdi_account` (`id`),
				  ADD CONSTRAINT `#__sdi_manager_object_ibfk_2` FOREIGN KEY (`object_id`) REFERENCES `#__sdi_object` (`id`);
							";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query = "ALTER TABLE `#__sdi_editor_object`
	  			  ADD CONSTRAINT `#__sdi_editor_object_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `#__sdi_account` (`id`),
				  ADD CONSTRAINT `#__sdi_editor_object_ibfk_2` FOREIGN KEY (`object_id`) REFERENCES `#__sdi_object` (`id`);
							";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}

			$query = "ALTER TABLE `#__sdi_account_objecttype`
				  ADD CONSTRAINT `#__sdi_account_objecttype_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `#__sdi_account` (`id`),
				  ADD CONSTRAINT `#__sdi_account_objecttype_ibfk_2` FOREIGN KEY (`objecttype_id`) REFERENCES `#__sdi_objecttype` (`id`);
							";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}


			$query="ALTER TABLE `#__sdi_objectversion`
				ADD CONSTRAINT `#__sdi_objectversion_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `#__sdi_object` (`id`),
				ADD CONSTRAINT `#__sdi_objectversion_ibfk_2` FOREIGN KEY (`metadata_id`) REFERENCES `#__sdi_metadata` (`id`),
				ADD CONSTRAINT `#__sdi_objectversion_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `#__sdi_objectversion` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_objectversionlink`
				ADD CONSTRAINT `#__sdi_objectversionlink_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `#__sdi_objectversion` (`id`),
				ADD CONSTRAINT `#__sdi_objectversionlink_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `#__sdi_objectversion` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_context`
				ADD CONSTRAINT `#__sdi_context_ibfk_1` FOREIGN KEY (`objecttype_id`) REFERENCES `#__sdi_objecttype` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_context_objecttype`
				ADD CONSTRAINT `#__sdi_context_objecttype_ibfk_1` FOREIGN KEY (`context_id`) REFERENCES `#__sdi_context` (`id`),
				ADD CONSTRAINT `#__sdi_context_objecttype_ibfk_2` FOREIGN KEY (`objecttype_id`) REFERENCES `#__sdi_objecttype` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_searchcriteria`
				ADD CONSTRAINT `#__sdi_searchcriteria_ibfk_1` FOREIGN KEY (`relation_id`) REFERENCES `#__sdi_relation` (`id`),
				ADD CONSTRAINT `#__sdi_searchcriteria_ibfk_2` FOREIGN KEY (`criteriatype_id`) REFERENCES `#__sdi_list_criteriatype` (`id`),
				ADD CONSTRAINT `#__sdi_searchcriteria_ibfk_3` FOREIGN KEY (`context_id`) REFERENCES `#__sdi_context` (`id`),
				ADD CONSTRAINT `#__sdi_searchcriteria_ibfk_4` FOREIGN KEY (`rendertype_id`) REFERENCES `#__sdi_list_rendertype` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_relation_context`
				ADD CONSTRAINT `#__sdi_relation_context_ibfk_1` FOREIGN KEY (`context_id`) REFERENCES `#__sdi_context` (`id`),
				ADD CONSTRAINT `#__sdi_relation_context_ibfk_2` FOREIGN KEY (`relation_id`) REFERENCES `#__sdi_relation` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_searchcriteria_tab`
				ADD CONSTRAINT `#__sdi_searchcriteria_tab_ibfk_1` FOREIGN KEY (`searchcriteria_id`) REFERENCES `#__sdi_searchcriteria` (`id`),
				ADD CONSTRAINT `#__sdi_searchcriteria_tab_ibfk_2` FOREIGN KEY (`context_id`) REFERENCES `#__sdi_context` (`id`),
				ADD CONSTRAINT `#__sdi_searchcriteria_tab_ibfk_3` FOREIGN KEY (`tab_id`) REFERENCES `#__sdi_list_searchtab` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_importref`
  				ADD CONSTRAINT `#__sdi_importref_ibfk_1` FOREIGN KEY (`importtype_id`) REFERENCES `#__sdi_list_importtype` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__sdi_application`
  				ADD CONSTRAINT `#__sdi_application_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `#__sdi_object` (`id`);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}


			/*
			 $query="ALTER TABLE `#__sdi_history_assign`
			 ADD CONSTRAINT `#__sdi_history_assign_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `#__sdi_object` (`id`),
			 ADD CONSTRAINT `#__sdi_history_assign_ibfk_2` FOREIGN KEY (`objectversion_id`) REFERENCES `#__sdi_objectversion` (`id`);
				";
				$db->setQuery( $query);
				if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
				*/

			// Update component version
			$version="1.0";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "1.0")
		{
			// Update component version
			$version="2.0.0";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.0.0")
		{

			$query="ALTER TABLE `#__sdi_searchcriteria_tab` ADD ordering bigint(20) NOT NULL";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}



			// Update component version
			$version="2.0.1";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.0.1")
		{
			
			$query = "SELECT id FROM `#__sdi_list_module` where code = 'CATALOG'";
			$db->setQuery( $query);
			$id = $db->loadResult();

			$query = "INSERT INTO #__sdi_configuration (guid, code, name, description, created, createdby, label, value, module_id) VALUES
				 ('".helper_easysdi::getUniqueId()."', 'CATALOG_MXQUERYURL', 'CATALOG_MXQUERYURL', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'http://localhost:8080/MXQuery', '".$id."'),
				 ('".helper_easysdi::getUniqueId()."', 'CATALOG_MXQUERYPAGINATION', 'CATALOG_MXQUERYPAGINATION', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 100, '".$id."')		 
				 ";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			//adding sitemap Params column
			$query="ALTER TABLE `#__sdi_objecttype` ADD sitemapParams varchar(1000)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			

	
			
			// add further migrations for tables once finalised
			
			//create table xqueryreport
			
			$query="CREATE TABLE IF NOT EXISTS #__sdi_xqueryreport (		
					id bigint(20) NOT NULL AUTO_INCREMENT,
					sqlfilter varchar(1000)  DEFAULT NULL,
					ogcfilter varchar(1000)  DEFAULT NULL,
					xslttemplateurl varchar(1000)  DEFAULT NULL,
					xqueryname varchar(1000)  DEFAULT NULL,
					xfileid varchar(1000)  DEFAULT NULL,
					reportcode  varchar(5000)  DEFAULT NULL,
					description varchar(5000)  DEFAULT NULL,
					applicationType smallint(8) DEFAULT 0,				  
				  	PRIMARY KEY (id)				  
				) 	ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//create table xqueryreportadmin
			$query="CREATE TABLE IF NOT EXISTS #__sdi_xqueryreportadmin (		
					id bigint(20) NOT NULL AUTO_INCREMENT,
					xquery_id  bigint(20) DEFAULT NULL,  
					admin_id  bigint(20) DEFAULT NULL,
				  	PRIMARY KEY (id)				  
				) 	ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//create table xqueryreportassignation
			$query="CREATE TABLE IF NOT EXISTS #__sdi_xqueryreportassignation (		
					id bigint(20) NOT NULL AUTO_INCREMENT,
					report_id  bigint(20) DEFAULT NULL,  
					user_id  bigint(20) DEFAULT NULL,
				  	PRIMARY KEY (id)				  
				) 	ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			// Update component version
			$version="2.0.2";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		
		if($version == "2.0.2")
		{
			$query = "INSERT INTO #__sdi_list_attributetype (guid, code, name, description, created, createdby, label, defaultpattern, isocode, namespace_id) VALUES
						('".helper_easysdi::getUniqueId()."', 'distance', 'distance', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_DISTANCE', '[0-9\\.\\-]', 'Distance', 2), 
						('".helper_easysdi::getUniqueId()."', 'integer', 'integer', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_INTEGER', '[0-9]', 'Integer', 2)";	
			
			$updateOk = false ;
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}else{
				$updateOk = true;
			}
		
			if($updateOk){
				$query = "select id from #__sdi_list_attributetype where code='distance'";
				$db->setQuery( $query);
				$id = $db->loadResult();
				if($id){
						
					$query = "INSERT INTO #__sdi_list_renderattributetype (attributetype_id, rendertype_id) VALUES (".$id.",5)";
					$db->setQuery( $query);
					if (!$db->query()) {
						$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					}
						
				}
				
				$query = "select id from #__sdi_list_attributetype where code='integer'";
				$db->setQuery( $query);
				$id = $db->loadResult();
				if($id){
						
					$query = "INSERT INTO #__sdi_list_renderattributetype (attributetype_id, rendertype_id) VALUES (".$id.",5)";
					$db->setQuery( $query);
					if (!$db->query()) {
						$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					}
						
				}

			}
			$query="ALTER TABLE #__sdi_configuration  MODIFY value varchar(1000)"; 
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
		
		
			$version="2.0.3";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

		}
		if($version == "2.0.3" || $version== "2.0.3.3")
		{
		
			$query = "SELECT id FROM #__sdi_list_module  WHERE  code='CATALOG'";
			$db->setQuery( $query );
			$module_id = $db->loadResult();
		
			$query="INSERT INTO #__sdi_configuration (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'defaultBboxConfig','defaultBboxConfig','default Bbox Config.','".date('Y-m-d H:i:s')."', '".$user_id."', '','".$module_id."')";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
					
			$query = "INSERT INTO `#__sdi_searchcriteria` (`guid`, `code`, `name`, `label`, `created`, `createdby`, `criteriatype_id`) VALUES
				  ('".helper_easysdi::getUniqueId()."', 'definedBoundary', 'definedBoundary', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_DEFINEDBOUNDARY', '".date('Y-m-d H:i:s')."', ".$user_id.", 1)";
		
			$db->setQuery( $query);
			
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
		
			$version="2.0.4";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.0.4")
		{
				
			
			$query = "INSERT INTO `#__sdi_searchcriteria` (`guid`, `code`, `name`, `label`, `created`, `createdby`, `criteriatype_id`) VALUES
				  ('".helper_easysdi::getUniqueId()."', 'isDownloadable', 'isDownloadable', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_ISDOWNLOADABLE', '".date('Y-m-d H:i:s')."', ".$user_id.", 1)";
			
							
			$db->setQuery( $query);
			
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
		
			$version="2.0.5";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.0.5")
		{
				
			
			$query = "SELECT id FROM #__sdi_list_module  WHERE  code='CATALOG'";
			$db->setQuery( $query );
			$module_id = $db->loadResult();
		
			$query="INSERT INTO #__sdi_configuration (guid,code,name,description, created,createdby,value,module_id) VALUES  
					('".helper_easysdi::getUniqueId()."', 'defaultBboxConfigExtentLeft','defaultBboxConfigExtentLeft','default Bbox ConfigExtent Left.','".date('Y-m-d H:i:s')."', '".$user_id."', '','".$module_id."'), 
					('".helper_easysdi::getUniqueId()."', 'defaultBboxConfigExtentBottom','defaultBboxConfigExtentBottom','default Bbox ConfigExtent Bottom.','".date('Y-m-d H:i:s')."', '".$user_id."', '','".$module_id."'), 
					('".helper_easysdi::getUniqueId()."', 'defaultBboxConfigExtentRight','defaultBboxConfigExtentRight','default Bbox ConfigExtent Right','".date('Y-m-d H:i:s')."', '".$user_id."', '','".$module_id."'), 
					('".helper_easysdi::getUniqueId()."', 'defaultBboxConfigExtentTop','defaultBboxConfigExtentTop','default Bbox ConfigExtent Top.','".date('Y-m-d H:i:s')."', '".$user_id."', '','".$module_id."')";
			
			$db->setQuery( $query);	
			
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			
			$version="2.0.6";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.0.6")
		{
				
			
			$query = "SELECT id FROM #__sdi_list_module  WHERE  code='CATALOG'";
			$db->setQuery( $query );
			$module_id = $db->loadResult();
		
			$query="INSERT INTO #__sdi_configuration (guid,code,name,description, created,createdby,value,module_id) VALUES  
					('".helper_easysdi::getUniqueId()."', 'thesaurusUrl','thesaurusUrl','thesaurus Url.','".date('Y-m-d H:i:s')."', '".$user_id."', '','".$module_id."')";
			
			$db->setQuery( $query);	
			
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			
			$version="2.0.7";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.0.7"){
			$query="ALTER TABLE `#__sdi_importref`
  				ADD column serviceversion VARCHAR(10);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_importref`
  				ADD column outputschema VARCHAR(100);
				";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$version="2.0.8";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.0.8"){
			
			$version="2.1.0";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.1.0"){
			
			$query="INSERT INTO `#__sdi_list_searchtab` (`guid`, `code`, `name`, `description`, `created`, `createdby`, `label`) VALUES
							('".helper_easysdi::getUniqueId()."', 'hidden', 'Hidden', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_SEARCHTAB_HIDDEN')
							";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_context_criteria` (
							  `id` bigint(20) NOT NULL AUTO_INCREMENT,
							  `context_id` bigint(20) NOT NULL,
							  `criteria_id` bigint(20) NOT NULL,
							  `defaultvalue` varchar(500) DEFAULT NULL,
							  `defaultvaluefrom` datetime DEFAULT NULL,
							  `defaultvalueto` datetime DEFAULT NULL,
							  PRIMARY KEY (`id`)
							) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_context_criteria`
			  				ADD CONSTRAINT `#__sdi_context_criteria_fk_1` FOREIGN KEY (`context_id`) REFERENCES `#__sdi_context` (`id`);
							";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_context_criteria`
						  				ADD CONSTRAINT `#__sdi_context_criteria_fk_2` FOREIGN KEY (`criteria_id`) REFERENCES `#__sdi_searchcriteria` (`id`);
										";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_context`
			  				ADD column runinitsearch tinyint(1) DEFAULT '0'
							";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "INSERT INTO `#__sdi_searchcriteria` (`guid`, `code`, `name`, `label`, `created`, `createdby`, `criteriatype_id`,`checked_out`) VALUES
							  ('".helper_easysdi::getUniqueId()."', 'isFree', 'isFree', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_ISFREE', '".date('Y-m-d H:i:s')."', ".$user_id.", 1,0)";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			$query = "INSERT INTO `#__sdi_searchcriteria` (`guid`, `code`, `name`, `label`, `created`, `createdby`, `criteriatype_id`,`checked_out`) VALUES
										  ('".helper_easysdi::getUniqueId()."', 'isOrderable', 'isOrderable', 'CATALOG_SEARCHCRITERIA_SYSTEMFIELD_ISORDERABLE', '".date('Y-m-d H:i:s')."', ".$user_id.", 1,0)";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			$version="2.2.0";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.2.0"){
			$query = "SELECT id FROM `#__sdi_list_module` where code = 'CATALOG'";
			$db->setQuery( $query);
			$id = $db->loadResult();
			
			$query = "INSERT INTO #__sdi_list_attributetype (guid, code, name, description, created, createdby, label, defaultpattern, isocode, namespace_id) VALUES
								('".helper_easysdi::getUniqueId()."', 'file', 'file', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CATALOG_ATTRIBUTETYPE_FILE', '', 'MI_Identifier', 1)";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
	
			$query = "INSERT INTO #__sdi_list_renderattributetype (attributetype_id, rendertype_id) VALUES
								( 14, 5)";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			$query = "INSERT INTO #__sdi_configuration (guid, code, name, description, created, createdby, label, value, module_id) VALUES
			('".helper_easysdi::getUniqueId()."', 'CATALOG_METADATA_LINKED_FILE_REPOSITORY', 'CATALOG_METADATA_LINKED_FILE_REPOSITORY', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '/home/tmp', '".$id."'),
			('".helper_easysdi::getUniqueId()."', 'CATALOG_METADATA_LINKED_FILE_BASE_URI', 'CATALOG_METADATA_LINKED_FILE_BASE_URI', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'http://localhost/easysdi/file/', '".$id."')
			";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
				
			$version="2.3.0";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}		
		if($version == "2.3.0"){
			
			$db->setQuery(" ALTER TABLE #__sdi_history_assign DROP FOREIGN KEY #__sdi_history_assign_ibfk_1");
			$db->query();
			$db->setQuery(" ALTER TABLE #__sdi_history_assign DROP FOREIGN KEY #__sdi_history_assign_ibfk_2");
			$db->query();			
			
			$version="2.3.1";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.3.1"){
			
			//CREATE #__sdi_catalog_namespace
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_catalog_namespace` (
					`id` bigint(20) NOT NULL AUTO_INCREMENT,
					`guid` varchar(36) NOT NULL,
					`prefix` varchar(10) NOT NULL,
					`uri` varchar(100) NOT NULL,
					`state` tinyint(1) DEFAULT 0,
					`ordering` bigint(20) NOT NULL DEFAULT '0',
					`system` tinyint(1) DEFAULT 0,
					`created` datetime DEFAULT NULL ,
					`created_by` bigint(20) DEFAULT NULL,
					`modified` datetime DEFAULT NULL,
					`modified_by` bigint(20) DEFAULT NULL,
					`checked_out` bigint(20) NOT NULL DEFAULT '0',
					`checked_out_time` datetime DEFAULT NULL,
					PRIMARY KEY (`id`),
					UNIQUE KEY `guid` (`guid`),
					UNIQUE KEY `prefix` (`prefix`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "INSERT INTO `#__sdi_catalog_namespace`  (id, 
																   guid, 
																   prefix, 
																   uri, 
																   ordering, 
																   system, 
																   created, 
																   created_by, 
																   modified, 
																   modified_by,
																   checked_out,
																   checked_out_time)
						SELECT id, 
							   guid, 
							   prefix, 
							   uri, 
							   ordering,
							   issystem,
							   created,
							   createdby,
							   updated,
							   updatedby,
							   checked_out,
							   checked_out_time
						FROM `#__sdi_namespace` ";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//CREATE #__sdi_sys_stereotype
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_sys_stereotype` (
					`id` bigint(20) NOT NULL AUTO_INCREMENT,
					`guid` varchar(36) NOT NULL,
					`alias` varchar(20) NOT NULL,
					`state` tinyint(1) NOT NULL DEFAULT 0,
					`ordering` bigint(20) NOT NULL DEFAULT '0',
					`entity_id` bigint(20) NOT NULL,
					`namespace_id` bigint(20),
					`regex_pattern` varchar(200),
					`regex_overwrite` tinyint(1) NOT NULL DEFAULT 0,
					`isocode` varchar(50),
					`checked_out` bigint(20) NOT NULL DEFAULT '0',
					`checked_out_time` datetime DEFAULT NULL,
					PRIMARY KEY (`id`),
					KEY `entity_id` (`entity_id`),
					KEY `namespace_id` (`namespace_id`),
					UNIQUE KEY `alias` (`alias`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			$query = "INSERT INTO `#__sdi_sys_stereotype`  (id,
															guid,
															alias,
															ordering,
															namespace_id,
															regex_pattern,
															isocode
															)
						SELECT  id,
								guid,
								code,
								ordering,
								namespace_id,
								defaultpattern,
								isocode
						FROM `#__sdi_list_attributetype`";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$db->setQuery( "UPDATE #__sdi_sys_stereotype SET entity_id = 1");
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//CREATE #__sdi_sys_entity
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_sys_entity` (
					`id` bigint(20) NOT NULL AUTO_INCREMENT,
					`guid` varchar(36) NOT NULL,
					`alias` varchar(20) NOT NULL,
					`state` tinyint(1) NOT NULL DEFAULT 0,
					`ordering` bigint(20) NOT NULL DEFAULT '0',
					`checked_out` bigint(20) NOT NULL DEFAULT '0',
					`checked_out_time` datetime DEFAULT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			//INSERT #__sdi_sys_entity
			$query="INSERT INTO `#__sdi_sys_entity` VALUE (1,'".helper_easysdi::getUniqueId()."','attribute',1,0,0,NULL)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			$query="INSERT INTO `#__sdi_sys_entity` VALUE (2,'".helper_easysdi::getUniqueId()."','class',1,1,0,NULL)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//DROP #__sdi_list_attributetype CONSTRAINTS
			$query="ALTER TABLE `#__sdi_list_attributetype` DROP FOREIGN KEY `#__sdi_list_attributetype_ibfk_1`;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//DROP #__sdi_attribute CONSTRAINTS
			$query="ALTER TABLE `#__sdi_attribute` DROP FOREIGN KEY `#__sdi_attribute_ibfk_1`;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			$query="ALTER TABLE `#__sdi_attribute` DROP FOREIGN KEY `#__sdi_attribute_ibfk_2`;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			$query="ALTER TABLE `#__sdi_attribute` DROP FOREIGN KEY `#__sdi_attribute_ibfk_3`;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//DROP #__sdi_relation CONSTRAINTS
			$query="ALTER TABLE `#__sdi_relation` DROP FOREIGN KEY `#__sdi_relation_ibfk_8`;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			//DROP #__sdi_class CONSTRAINTS
			$query="ALTER TABLE `#__sdi_class` DROP FOREIGN KEY `#__sdi_class_ibfk_2`;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//DROP TABLE #__sdi_namespace
			$query="DROP TABLE `#__sdi_namespace` ";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			//RENAME TABLE #__sdi_namespace
			$query="RENAME TABLE `#__sdi_catalog_namespace` TO `#__sdi_namespace` ";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//CREATE #__sdi_attribute CONSTRAINTS
			$query="ALTER TABLE `#__sdi_attribute` ADD CONSTRAINT `#__sdi_attribute_ibfk_1` FOREIGN KEY (`attributetype_id`) REFERENCES `#__sdi_sys_stereotype` (`id`);";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			$query="ALTER TABLE `#__sdi_attribute` ADD CONSTRAINT `#__sdi_attribute_ibfk_2` FOREIGN KEY (`namespace_id`) REFERENCES `#__sdi_namespace` (`id`);";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			$query="ALTER TABLE `#__sdi_attribute` ADD CONSTRAINT `#__sdi_attribute_ibfk_3` FOREIGN KEY (`listnamespace_id`) REFERENCES `#__sdi_namespace` (`id`);";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			//CREATE #__sdi_relation CONSTRAINTS
			$query="ALTER TABLE `#__sdi_relation` ADD CONSTRAINT `#__sdi_relation_ibfk_8` FOREIGN KEY (`namespace_id`) REFERENCES `#__sdi_namespace` (`id`);";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//CREATE #__sdi_class CONSTRAINTS
			$query="ALTER TABLE `#__sdi_class` ADD CONSTRAINT `#__sdi_class_ibfk_2` FOREIGN KEY (`namespace_id`) REFERENCES `#__sdi_namespace` (`id`);";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//CREATE #__sdi_sys_stereotype CONSTRAINTS
			$query="ALTER TABLE `#__sdi_sys_stereotype` ADD CONSTRAINT `#__sdi_sys_stereotype_ibfk_1` FOREIGN KEY (`namespace_id`) REFERENCES `#__sdi_namespace` (`id`);";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_sys_stereotype` ADD CONSTRAINT `#__sdi_sys_stereotype_ibfk_2` FOREIGN KEY (`entity_id`) REFERENCES `#__sdi_sys_entity` (`id`);";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//DROP #__sdi_list_renderattributetype CONSTRAINTS
			$query="ALTER TABLE `#__sdi_list_renderattributetype` DROP FOREIGN KEY `#__sdi_list_renderattributetype_ibfk_2`;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//CREATE #__sdi_list_renderattributetype CONSTRAINTS
			$query="ALTER TABLE `#__sdi_list_renderattributetype`  ADD CONSTRAINT `#__sdi_list_renderattributetype_ibfk_2` FOREIGN KEY (`attributetype_id`) REFERENCES `#__sdi_sys_stereotype` (`id`);";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//DROP TABLE #__sdi_list_attributetype
			$query="DROP TABLE `#__sdi_list_attributetype` ";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//INSERT namespace sdi
			$query = "SELECT count(*) FROM `#__sdi_namespace` where prefix = 'sdi'";
			$db->setQuery( $query);
			$count = $db->loadResult();
			
			if($count == 0){
				$query="INSERT INTO `#__sdi_namespace` (`guid`, `created`, `created_by`, `ordering`, `prefix`, `uri`, `system`) VALUES
				('".helper_easysdi::getUniqueId()."','".date('Y-m-d H:i:s')."', ".$user_id.", 0, 'sdi', 'http://www.easysdi.org/2011/sdi', 1)
				;";
				$db->setQuery( $query);
				if (!$db->query()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
			}
				
			$query = "INSERT INTO #__sdi_sys_stereotype (guid, alias, entity_id) VALUES
			('".helper_easysdi::getUniqueId()."', 'geographicextent', 2)";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			$geo_stereotype_id = $db->insertid(); 
				
			$query="ALTER TABLE `#__sdi_class` ADD stereotype_id bigint(20)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_attribute` DROP isextensible ";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_relation` ADD editable bigint(20)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//CREATE #__sdi_sys_attribute
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_sys_attribute` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`guid` varchar(36) NOT NULL,
				`alias` varchar(20) NOT NULL,
				`label` varchar(100) NOT NULL,
				`state` tinyint(1) NOT NULL DEFAULT 0,
				`ordering` bigint(20) NOT NULL DEFAULT '0',
				`checked_out` bigint(20) NOT NULL DEFAULT '0',
				`checked_out_time` datetime DEFAULT NULL,
				`type` varchar(50) NOT NULL,
				`length` int(10) NOT NULL,
				`stereotype_id` bigint(20) NULL,
				`fieldtype` varchar(500)  NULL,
				FOREIGN KEY (`stereotype_id`) REFERENCES `#__sdi_sys_stereotype` (`id`) ,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			//CREATE #__sdi_relationattribute
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_relation_attribute` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`guid` varchar(36) NOT NULL,
				`relation_id` bigint(20) NOT NULL,
				`attribute_id` bigint(20) NOT NULL,
				`value` varchar(500) NULL,
				PRIMARY KEY (`id`),
				FOREIGN KEY (`relation_id`) REFERENCES `#__sdi_relation` (`id`) ,
				FOREIGN KEY (`attribute_id`) REFERENCES `#__sdi_sys_attribute` (`id`) 
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			
			//INSERT #__sdi_sys_attribute
			$query="INSERT INTO `#__sdi_sys_attribute` VALUE (1,'".helper_easysdi::getUniqueId()."','strictperimeter','CATALOG_RELATION_ATTRIBUT_STRICTPERIMETER',0,0,0,NULL,'boolean',1,$geo_stereotype_id, null)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			//INSERT #__sdi_sys_attribute
			$query="INSERT INTO `#__sdi_sys_attribute` VALUE (2,'".helper_easysdi::getUniqueId()."','displaymap','CATALOG_RELATION_ATTRIBUT_DISPLAYMAP',0,0,0,NULL,'boolean',1,$geo_stereotype_id, null)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			//INSERT #__sdi_sys_attribute
			$query="INSERT INTO `#__sdi_sys_attribute` VALUE (3,'".helper_easysdi::getUniqueId()."','params','CATALOG_RELATION_ATTRIBUT_PARAMS',0,0,0,NULL,'json',500,$geo_stereotype_id, '{\"defaultBboxConfig\":\"textarea\",\"defaultBboxConfigExtentLeft\":\"input\",\"defaultBboxConfigExtentRight\":\"input\",\"defaultBboxConfigExtentBottom\":\"input\",\"defaultBboxConfigExtentTop\":\"input\"}')";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			
			//CREATE #__sdi_sys_fieldproperty
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_sys_fieldproperty` (
								`id` bigint(20) NOT NULL AUTO_INCREMENT,
								`guid` varchar(36) NOT NULL,
								`alias` varchar(20) NOT NULL,
								`state` tinyint(1) NOT NULL DEFAULT 0,
								`ordering` bigint(20) NOT NULL DEFAULT '0',
								`checked_out` bigint(20) NOT NULL DEFAULT '0',
								`checked_out_time` datetime DEFAULT NULL,
								PRIMARY KEY (`id`)
								) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			//INSERT #__sdi_sys_fieldproperty
			$query="INSERT INTO `#__sdi_sys_fieldproperty` VALUE (1,'".helper_easysdi::getUniqueId()."','editable',1,0,0,NULL)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			//INSERT #__sdi_sys_fieldproperty
			$query="INSERT INTO `#__sdi_sys_fieldproperty` VALUE (2,'".helper_easysdi::getUniqueId()."','visible',1,0,0,NULL)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			//INSERT #__sdi_sys_fieldproperty
			$query="INSERT INTO `#__sdi_sys_fieldproperty` VALUE (3,'".helper_easysdi::getUniqueId()."','hidden',1,0,0,NULL)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="UPDATE `#__sdi_relation` r INNER JOIN `#__sdi_attribute` a ON r.attributechild_id=a.id SET r.editable = 1 WHERE a.issystem=0";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="UPDATE `#__sdi_relation` r INNER JOIN `#__sdi_attribute` a ON r.attributechild_id=a.id SET r.editable = 2 WHERE a.issystem=1";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//CREATE #__sdi_boundarycategory
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_boundarycategory` (
					`id` bigint(20) NOT NULL AUTO_INCREMENT,
					`guid` varchar(36) NOT NULL,
					`title` varchar(100) NOT NULL,
					`alias` varchar(20) NOT NULL,
					`parent_id` bigint(20) NULL,
					`state` tinyint(1) NOT NULL DEFAULT 0,
					`ordering` bigint(20) NOT NULL DEFAULT '0',
					`created` datetime DEFAULT NULL ,
					`created_by` bigint(20) DEFAULT NULL,
					`modified` datetime DEFAULT NULL,
					`modified_by` bigint(20) DEFAULT NULL,
					`checked_out` bigint(20) NOT NULL DEFAULT '0',
					`checked_out_time` datetime DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `guid` (`guid`),
			UNIQUE KEY `alias` (`alias`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			$query="ALTER TABLE `#__sdi_boundarycategory` ADD CONSTRAINT `#__sdi_bounderycategory_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `#__sdi_boundarycategory` (`id`)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_boundary` ADD category_id bigint(20)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_boundary` ADD parent_id bigint(20)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_boundary` ADD CONSTRAINT `#__sdi_boundery_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `#__sdi_boundarycategory` (`id`)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="ALTER TABLE `#__sdi_boundary` ADD CONSTRAINT `#__sdi_boundery_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `#__sdi_boundary` (`id`)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}	
			
// 			//ALTER __sdi_searchcriteria
// 			$query="ALTER TABLE `#__sdi_searchcriteria` ADD paramsdef varchar(500)";
// 			$db->setQuery( $query);
// 			if (!$db->query()) {
// 				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
// 			}
			
			//ALTER __sdi_context_criteria
			$query="ALTER TABLE `#__sdi_context_criteria` ADD params varchar(500)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			//ALTER __sdi_context_criteria
			$query="ALTER TABLE `#__sdi_context` ADD filter varchar(1000)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//Extent tooltip text field in backend
			$query="ALTER TABLE `#__sdi_translation` CHANGE COLUMN `information` `information` VARCHAR(400) NULL DEFAULT NULL";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$version="2.4.0";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.4.0"){
			$version="2.4.1";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
        
		//TODO in next version :
		//Drop table sdi_list_attribute_type, no more used by the CATALOG since release 2.4.0
		//Table is kept until next version in case of migration data problems between tables 'sdi_list_attribute_type' and 'sdi_sys_stereotype'
        if($version == "2.4.1"){
        
            //issue #540
            $query="ALTER TABLE `#__sdi_translation` ADD INDEX `element_guid` (`element_guid` ASC)" ;
            $db->setQuery( $query);
            if (!$db->query()) {
                $mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
            }

        
			$version="2.4.2";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		
		if($version == "2.4.2"){
			
			//ALTER __sdi_metadata
			$query="ALTER TABLE `#__sdi_metadata` ADD lastsynchronization datetime";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//ALTER __sdi_metadata
			$query="ALTER TABLE `#__sdi_metadata` ADD synchronizedby BIGINT(20)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//ALTER __sdi_metadata
			$query="ALTER TABLE `#__sdi_metadata` ADD notification TINYINT(1) NOT NULL DEFAULT '0'";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//ALTER __sdi_relation
			$query="ALTER TABLE `#__sdi_relation` ADD editoraccessibility BIGINT(20)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//ALTER __sdi_relation
			$query="ALTER TABLE `#__sdi_relation` ADD istitle TINYINT(1) NOT NULL DEFAULT '0'";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "SELECT id FROM `#__sdi_list_module` where code = 'CATALOG'";
			$db->setQuery( $query);
			$id = $db->loadResult();
			
			$query = "INSERT INTO #__sdi_configuration (guid, code, name, description, created, createdby, label, value, module_id) VALUES
					('".helper_easysdi::getUniqueId()."', 'CATALOG_METADATA_PREVIEW_CONTEXT_PUBLIC', 'CATALOG_METADATA_PREVIEW_CONTEXT_PUBLIC', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '', '".$id."'),
					('".helper_easysdi::getUniqueId()."', 'CATALOG_METADATA_PREVIEW_TYPE_PUBLIC', 'CATALOG_METADATA_PREVIEW_TYPE_PUBLIC', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '', '".$id."'),
					('".helper_easysdi::getUniqueId()."', 'CATALOG_METADATA_PREVIEW_CONTEXT_EDITOR', 'CATALOG_METADATA_PREVIEW_CONTEXT_EDITOR', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '', '".$id."'),
					('".helper_easysdi::getUniqueId()."', 'CATALOG_METADATA_PREVIEW_TYPE_EDITOR', 'CATALOG_METADATA_PREVIEW_TYPE_EDITOR', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '', '".$id."'),
					('".helper_easysdi::getUniqueId()."', 'CATALOG_VERSION_DATETIME_DISPLAY', 'CATALOG_VERSION_DATETIME_DISPLAY', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'Y-m-d', '".$id."')
					";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			//ALTER __sdi_objecttypelink
			$query="ALTER TABLE `#__sdi_objecttypelink` ADD inheritance TINYINT(1) NOT NULL DEFAULT '0'";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			//CREATE #__sdi_objecttypelinkinheritance
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_objecttypelinkinheritance` (
					`id` bigint(20) NOT NULL AUTO_INCREMENT,
					`objecttypelink_id` bigint(20) NOT NULL,
					`xpath` varchar(500) NOT NULL,
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			$query="ALTER TABLE `#__sdi_objecttypelinkinheritance` ADD CONSTRAINT `#__sdi_objecttypelinkinheritance_fk_1` FOREIGN KEY (`objecttypelink_id`) REFERENCES `#__sdi_objecttypelink` (`id`)";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$version="2.5.0";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.5.0")
		{
			$query = "SELECT id FROM `#__sdi_list_module` where code = 'CATALOG'";
			$db->setQuery( $query);
			$id = $db->loadResult();
			
			$query = "SELECT COUNT(*) FROM #__sdi_configuration WHERE code ='CATALOG_METADATA_TITLE_XPATH'";
			$db->setQuery( $query);
			$count = $db->loadResult();
			
			if($count == 0)
			{
				$query = "ALTER TABLE #__sdi_configuration MODIFY value varchar(500)";
				$db->setQuery( $query);
				if (!$db->query()){
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					return false;
				}
				
				$query = "INSERT INTO #__sdi_configuration (guid, code, name, description, created, createdby, label, value, module_id) VALUES
				('".helper_easysdi::getUniqueId()."', 'CATALOG_METADATA_TITLE_XPATH', 'CATALOG_METADATA_TITLE_XPATH', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title', '".$id."')
				";
				$db->setQuery( $query);
				if (!$db->query()){
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		if($version == "2.5.0")
		{
			$query="UPDATE #__sdi_list_visibility SET label ='CATALOG_PUBLIC' WHERE code='public'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			$query="UPDATE #__sdi_list_visibility SET label ='CATALOG_PRIVATE' WHERE code='private'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			$query="UPDATE #__sdi_list_visibility SET label ='CATALOG_PROTECTED' WHERE code='protected'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$version="2.5.1";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.5.1")
		{
			$version="2.5.2";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.5.2")
		{
			$version="2.5.3";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		if($version == "2.5.3")
		{
			$version="2.5.4";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CATALOG'";
			$db->setQuery( $query);
			if (!$db->query()){
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
        
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_catalog' ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('EasySDI - Catalog','option=com_easysdi_catalog','Easysdi Catalog','com_easysdi_catalog','js/ThemeOffice/component.png','')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$mainframe->enqueueMessage("Congratulation catalog for EasySdi is installed and ready to be used. Enjoy EasySdi Catalog!","INFO");
		return true;
	}
	


?>