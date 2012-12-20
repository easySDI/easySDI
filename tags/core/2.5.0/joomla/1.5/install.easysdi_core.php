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

function com_install(){

	global  $mainframe;
	$db =& JFactory::getDBO();

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');

	$user =& JFactory::getUser();
	$user_id = $user->get('id');
	
	/**
	 * Creates the database structure
	 */
	$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_module` (
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
			  `ordering` bigint(20),
			  `value` varchar(100),
			  `currentversion` varchar(20),
			  `lastversion` varchar(20),
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `guid` (`guid`),
			  UNIQUE KEY `code` (`code`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
	 		
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;
	}

	
	
	/**
	 * Gets the component versions
	 */	
	$version = '0.0';
	$query = "SELECT currentversion FROM `#__sdi_list_module` where `code` = 'CORE'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if (!$version)
	{
		$version="0.1";
		
		$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'CORE', 'com_easysdi_core', 'com_easysdi_core', '".date('Y-m-d H:i:s')."', ".$user_id.", 'com_easysdi_core', 'com_easysdi_core', '".$version."')";
		$db->setQuery( $query);
		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	}	
	if ($db->getErrorNum()) 
	{
		//The table does'nt exist
		//That means nothing is installed.
		$mainframe->enqueueMessage("EASYSDI IS NOT INSTALLED","ERROR");		
		exit;		
	}	
	if ($version == "0.1")
	{
		// Get the current component ID
		$query = "SELECT id FROM `#__sdi_list_module` where code = 'CORE'";
		$db->setQuery( $query);
		$id = $db->loadResult();
		
		
			/**
			 * Create the configuration table
			 */
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_configuration` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36),
					  `code` varchar(50),
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100),
					  `created` datetime NOT NULL,
					  `updated` datetime,
					  `createdby` bigint(20),
					  `updatedby` bigint(20),
					  `label` varchar(50),
					  `ordering` bigint(20),
					  `value` varchar(100),
					  `module_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`),
					  KEY `module_id` (`module_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			// Configuration keys for CORE
			$query = "INSERT INTO #__sdi_configuration (guid, code, name, description, created, createdby, label, value, module_id) VALUES 
					  ('".helper_easysdi::getUniqueId()."', 'DESCRIPTION_LENGTH', 'DESCRIPTION_LENGTH', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '100', '".$id."'),
					  ('".helper_easysdi::getUniqueId()."', 'LOGO_WIDTH', 'LOGO_WIDTH', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '30', '".$id."'),
					  ('".helper_easysdi::getUniqueId()."', 'LOGO_HEIGHT', 'LOGO_HEIGHT', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, '30', '".$id."'),
					  ('".helper_easysdi::getUniqueId()."', 'WELCOME_REDIRECT_URL', 'WELCOME_REDIRECT_URL', null, '".date('Y-m-d H:i:s')."', '".$user_id."', null, 'index.php?option=com_content&view=article&id=46&Itemid=104', '".$id."')
					  
					 ";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			/*
			 * Create and complete system tables 
			 */
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_accounttab` (
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
			$query="INSERT INTO `#__sdi_list_accounttab` (`guid`, `code`, `name`, `description`, `created`, `createdby`, `label`) VALUES
					('".helper_easysdi::getUniqueId()."', 'general', 'General', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'general'),
					('".helper_easysdi::getUniqueId()."', 'contact', 'Contact', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'contact'),
					('".helper_easysdi::getUniqueId()."', 'billing', 'Billing', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'billing'),
					('".helper_easysdi::getUniqueId()."', 'delivery', 'Delivery', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'delivery'),
					('".helper_easysdi::getUniqueId()."', 'rights', 'Rights', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'rights'),
					('".helper_easysdi::getUniqueId()."', 'affiliate', 'Affiliate', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'affiliate')";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_addresstype` (
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
					  `ordering` bigint(20),
					  `publish_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`),
					  KEY `publish_id` (`publish_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_country` (
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
					  `ordering` bigint(20),
					  `publish_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`),
					  KEY `publish_id` (`publish_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__sdi_list_country` (`guid`, `code`, `name`, `description`, `created`, `createdby`) VALUES
					('".helper_easysdi::getUniqueId()."', 'AD', 'ANDORRE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AE', 'EMIRATS ARABES UNIS', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AF', 'AFGHANISTAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AG', 'ANTIGUA-ET-BARBUDA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AI', 'ANGUILLA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AL', 'ALBANIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AM', 'ARMENIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AN', 'ANTILLES NEERLANDAISES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AO', 'ANGOLA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AQ', 'ANTARCTIQUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AR', 'ARGENTINE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AS', 'SAMOA AMERICAINES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AT', 'AUTRICHE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AU', 'AUSTRALIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AW', 'ARUBA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AX', 'ELAND, ELES D''', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'AZ', 'AZERBAEDJAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BA', 'BOSNIE-HERZEGOVINE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BB', 'BARBADE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BD', 'BANGLADESH', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BE', 'BELGIQUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BF', 'BURKINA FASO', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BG', 'BULGARIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BH', 'BAHREIN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BI', 'BURUNDI', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BJ', 'BENIN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BM', 'BERMUDES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BN', 'BRUNDI DARUSSALAM', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BO', 'BOLIVIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BR', 'BRESIL', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BS', 'BAHAMAS', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BT', 'BHOUTAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BV', 'BOUVET, ILE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BW', 'BOTSWANA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BY', 'BILARUS', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'BZ', 'BELIZE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CA', 'CANADA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CC', 'COCOS (KEELING), ILES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CD', 'CONGO, LA REPUBLIQUE DEMOCRATIQUE DU', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CF', 'CENTRAFRICAINE, REPUBLIQUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CG', 'CONGO', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CH', 'SUISSE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CI', 'COTE D''IVOIRE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CK', 'COOK, ILES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CL', 'CHILI', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CM', 'CAMEROUN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CN', 'CHINE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CO', 'COLOMBIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CR', 'COSTA RICA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CS', 'SERBIE-ET-MONTENEGRO', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CU', 'CUBA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CV', 'CAP-VERT', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CX', 'CHRISTMAS, ILE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CY', 'CHYPRE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'CZ', 'TCHEQUE, REPUBLIQUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'DE', 'ALLEMAGNE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'DJ', 'DJIBOUTI', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'DK', 'DANEMARK', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'DM', 'DOMINIQUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'DO', 'DOMINICAINE, REPUBLIQUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'DZ', 'ALGERIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'EC', 'EQUATEUR', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'EE', 'ESTONIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'EG', 'EGYPTE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'EH', 'SAHARA OCCIDENTAL', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ER', 'ERYTHREE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ES', 'ESPAGNE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ET', 'ETHIOPIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'FI', 'FINLANDE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'FJ', 'FIDJI', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'FK', 'FALKLAND, ELES (MALVINAS)', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'FM', 'MICRONESIE, ETATS FEDERES DE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'FO', 'FEROE, ELES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'FR', 'FRANCE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GA', 'GABON', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GB', 'ROYAUME-UNI', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GD', 'GRENADE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GE', 'GEORGIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GF', 'GUYANE FRANEAISE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GH', 'GHANA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GI', 'GIBRALTAR', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GL', 'GROENLAND', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GM', 'GAMBIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GN', 'GUINEE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GP', 'GUADELOUPE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GQ', 'GUINEE EQUATORIALE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GR', 'GRECE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GS', 'GEORGIE DU SUD ET LES ELES SANDWICH DU SUD', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GT', 'GUATEMALA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GU', 'GUAM', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GW', 'GUINEE-BISSAU', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'GY', 'GUYANA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'HK', 'HONG-KONG', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'HM', 'HEARD, ELE ET MCDONALD, ELES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'HN', 'HONDURAS', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'HR', 'CROATIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'HT', 'HAETI', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'HU', 'HONGRIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ID', 'INDONESIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'IE', 'IRLANDE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'IL', 'ISRAEL', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'IN', 'INDE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'IO', 'OCEAN INDIEN, TERRITOIRE BRITANNIQUE DE L''', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'IQ', 'IRAQ', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'IR', 'IRAN, REPUBLIQUE ISLAMIQUE D''', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'IS', 'ISLANDE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'IT', 'ITALIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'JM', 'JAMAEQUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'JO', 'JORDANIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'JP', 'JAPON', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KE', 'KENYA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KG', 'KIRGHIZISTAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KH', 'CAMBODGE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KI', 'KIRIBATI', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KM', 'COMORES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KN', 'SAINT-KITTS-ET-NEVIS', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KP', 'COREE, REPUBLIQUE POPULAIRE DEMOCRATIQUE DE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KR', 'COREE, REPUBLIQUE DE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KW', 'KOWEET', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KY', 'CAEMANES, ELES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'KZ', 'KAZAKHSTAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LA', 'LAO, REPUBLIQUE DEMOCRATIQUE POPULAIRE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LB', 'LIBAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LC', 'SAINTE-LUCIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LI', 'LIECHTENSTEIN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LK', 'SRI LANKA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LR', 'LIBERIA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LS', 'LESOTHO', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LT', 'LITUANIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LU', 'LUXEMBOURG', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LV', 'LETTONIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'LY', 'LIBYENNE, JAMAHIRIYA ARABE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MA', 'MAROC', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MC', 'MONACO', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MD', 'MOLDOVA, REPUBLIQUE DE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MG', 'MADAGASCAR', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MH', 'MARSHALL, ELES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MK', 'MACEDOINE, L''EX-REPUBLIQUE YOUGOSLAVE DE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ML', 'MALI', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MM', 'MYANMAR', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MN', 'MONGOLIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MO', 'MACAO', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MP', 'MARIANNES DU NORD, ELES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MQ', 'MARTINIQUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MR', 'MAURITANIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MS', 'MONTSERRAT', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MT', 'MALTE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MU', 'MAURICE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MV', 'MALDIVES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MW', 'MALAWI', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MX', 'MEXIQUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MY', 'MALAISIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'MZ', 'MOZAMBIQUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NA', 'NAMIBIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NC', 'NOUVELLE-CALEDONIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NE', 'NIGER', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NF', 'NORFOLK, ELE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NG', 'NIGERIA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NI', 'NICARAGUA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NL', 'PAYS-BAS', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NO', 'NORVEGE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NP', 'NEPAL', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NR', 'NAURU', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NU', 'NIUE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'NZ', 'NOUVELLE-ZELANDE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'OM', 'OMAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PA', 'PANAMA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PE', 'PEROU', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PF', 'POLYNESIE FRANEAISE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PG', 'PAPOUASIE-NOUVELLE-GUINEE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PH', 'PHILIPPINES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PK', 'PAKISTAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PL', 'POLOGNE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PM', 'SAINT-PIERRE-ET-MIQUELON', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PN', 'PITCAIRN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PR', 'PORTO RICO', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PS', 'PALESTINIEN OCCUPE, TERRITOIRE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PT', 'PORTUGAL', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PW', 'PALAOS', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'PY', 'PARAGUAY', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'QA', 'QATAR', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'RE', 'REUNION', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'RO', 'ROUMANIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'RU', 'RUSSIE, FEDERATION DE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'RW', 'RWANDA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SA', 'ARABIE SAOUDITE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SB', 'SALOMON, ELES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SC', 'SEYCHELLES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SD', 'SOUDAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SE', 'SUEDE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SG', 'SINGAPOUR', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SH', 'SAINTE-HELENE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SI', 'SLOVENIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SJ', 'SVALBARD ET ELE JAN MAYEN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SK', 'SLOVAQUIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SL', 'SIERRA LEONE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SM', 'SAINT-MARIN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SN', 'SENEGAL', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SO', 'SOMALIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SR', 'SURINAME', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ST', 'SAO TOME-ET-PRINCIPE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SV', 'EL SALVADOR', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SY', 'SYRIENNE, REPUBLIQUE ARABE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'SZ', 'SWAZILAND', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TC', 'TURKS ET CAEQUES, ELES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TD', 'TCHAD', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TF', 'TERRES AUSTRALES FRANEAISES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TG', 'TOGO', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TH', 'THAELANDE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TJ', 'TADJIKISTAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TK', 'TOKELAU', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TL', 'TIMOR-LESTE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TM', 'TURKMENISTAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TN', 'TUNISIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TO', 'TONGA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TR', 'TURQUIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TT', 'TRINITE-ET-TOBAGO', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TV', 'TUVALU', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TW', 'TAEWAN, PROVINCE DE CHINE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'TZ', 'TANZANIE, REPUBLIQUE-UNIE DE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'UA', 'UKRAINE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'UG', 'OUGANDA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'UM', 'ELES MINEURES ELOIGNEES DES ETATS-UNIS', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'US', 'ETATS-UNIS', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'UY', 'URUGUAY', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'UZ', 'OUZBEKISTAN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'VA', 'SAINT-SIEGE (ETAT DE LA CITE DU VATICAN)', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'VC', 'SAINT-VINCENT-ET-LES GRENADINES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'VE', 'VENEZUELA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'VG', 'ELES VIERGES BRITANNIQUES', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'VI', 'ELES VIERGES DES ETATS-UNIS', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'VN', 'VIET NAM', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'VU', 'VANUATU', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'WF', 'WALLIS ET FUTUNA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'WS', 'SAMOA', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'YE', 'YEMEN', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'YT', 'MAYOTTE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ZA', 'AFRIQUE DU SUD', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ZM', 'ZAMBIE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ZW', 'ZIMBABWE', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."')";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_role` (
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
					  `ordering` bigint(20),
					  `publish_id` bigint(20) NOT NULL,
					  `roletype_id` bigint(20),
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `code` (`code`),
					  KEY `roletype_id` (`roletype_id`),
					  KEY `publish_id` (`publish_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__sdi_list_role` (`guid`, `code`, `name`, `label`, `description`, `created`, `createdby`, `publish_id`, `roletype_id`) VALUES
					('".helper_easysdi::getUniqueId()."', 'REQUEST_EXTERNAL', 'REQUEST_EXTERNAL', 'CORE_ACCOUNT_REQUEST_EXTERNAL_RIGHT', 'Commande de données externes', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'METADATA', 'METADATA', 'CORE_ACCOUNT_METADATA_RIGHT', 'Gestion de métadonnEes', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'FORMULARY', 'FORMULARY', 'CORE_ACCOUNT_FORMULARY_RIGHT', 'Gestion de formulaires', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'REQUEST_INTERNAL', 'REQUEST_INTERNAL', 'CORE_ACCOUNT_REQUEST_INTERNAL_RIGHT', 'Commande de données internes', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'ACCOUNT', 'ACCOUNT', 'CORE_ACCOUNT_ACCOUNT_RIGHT', 'Gestion d''acomptes affiliés', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'MYACCOUNT', 'MYACCOUNT', 'CORE_ACCOUNT_MYACCOUNT_RIGHT', null, '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'INTERNAL', 'INTERNAL', 'CORE_ACCOUNT_INTERNAL_RIGHT', null, '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'TIERCE', 'TIERCE', 'CORE_ACCOUNT_TIERCE_RIGHT', 'Commande pour un tiers', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'DIFFUSION', 'DIFFUSION', 'CORE_ACCOUNT_DIFFUSION_RIGHT', 'Gestionnaire de diffusion', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'FAVORITE', 'FAVORITE', 'CORE_ACCOUNT_FAVORITE_RIGHT', 'Gestion des favoris', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'PRODUCT', 'PRODUCT', 'CORE_ACCOUNT_PRODUCT_RIGHT', 'Gestion des produits', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'EASYSDI_CACHE', 'EASYSDI_CACHE', 'CORE_ACCOUNT_PROXY_CACHE_RIGHT', 'Gestion du cache par le proxy', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1')";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_catalogtype` (
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
				  `ordering` bigint(20),
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`),
				  UNIQUE KEY `code` (`code`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__sdi_list_catalogtype` (`guid`, `code`, `name`, `label`, `description`, `created`, `createdby`) VALUES
					('".helper_easysdi::getUniqueId()."', 'data', 'Data', 'data', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'service', 'Service', 'service', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."')";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_metadatastate` (
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
					  `ordering` bigint(20),
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__sdi_list_metadatastate` (`guid`, `code`, `name`, `label`, `description`, `created`, `createdby`, `ordering`) VALUES
					('".helper_easysdi::getUniqueId()."', 'published', 'Published', 'CORE_PUBLISHED', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."', 3),
					('".helper_easysdi::getUniqueId()."', 'archived', 'Archived', 'CORE_ARCHIVED', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."', 4),
					('".helper_easysdi::getUniqueId()."', 'validated', 'Validated', 'CORE_VALIDATED', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."', 2),
					('".helper_easysdi::getUniqueId()."', 'unpublished', 'Unpublished', 'CORE_UNPUBLISHED', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."', 1)";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_projection` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36),
					  `code` varchar(20),
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100),
					  `label` varchar(50) NOT NULL,
					  `created` datetime NOT NULL,
					  `createdby` bigint(20),
					  `updated` datetime,
					  `updatedby` bigint(20),
					  `ordering` bigint(20),
					  `unit` varchar(50),
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__sdi_list_projection` (`guid`, `code`, `name`, `label`, `description`, `created`, `createdby`, `unit`) VALUES
					('".helper_easysdi::getUniqueId()."', 'degrees', 'degrees', 'degrees', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."', 'degrees'),
					('".helper_easysdi::getUniqueId()."', 'meters', 'meters', 'meters', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."', 'meters')";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_tablocation` (
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
					  `ordering` bigint(20),
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__sdi_list_tablocation` (`guid`, `code`, `name`, `label`, `description`, `created`, `createdby`) VALUES
					('".helper_easysdi::getUniqueId()."', 'top', 'top', 'top', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'bottom', 'bottom', 'bottom', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."')";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_visibility` (
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
					  `ordering` bigint(20),
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__sdi_list_visibility` (`guid`, `code`, `name`, `label`, `description`, `created`, `createdby`) VALUES
					('".helper_easysdi::getUniqueId()."', 'public', 'public', 'public', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'private', 'private', 'private', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'protected', 'protected', 'protected', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."')";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			/*
			 * End of system tables
			 */
			
			
			/*
			 * Create tables for accounts
			 */
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_account` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) DEFAULT NULL,
					  `code` varchar(20) DEFAULT NULL,
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100) DEFAULT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime DEFAULT NULL,
					  `createdby` bigint(20) DEFAULT NULL,
					  `updatedby` bigint(20) DEFAULT NULL,
					  `label` varchar(50) DEFAULT NULL,
					  `ordering` bigint(20) DEFAULT NULL,
					  `publish_id` bigint(20) DEFAULT NULL,
					  `user_id` bigint(20) DEFAULT NULL,
					  `root_id` bigint(20) DEFAULT NULL,
					  `parent_id` bigint(20) DEFAULT NULL,
					  `state_id` bigint(20) DEFAULT NULL,
					  `acronym` varchar(50) DEFAULT NULL,
					  `url` varchar(50) DEFAULT NULL,
					  `invoice` datetime DEFAULT NULL,
					  `call1` datetime DEFAULT NULL,
					  `call2` datetime DEFAULT NULL,
					  `contract` bigint(20) DEFAULT NULL,
					  `notify_new_metadata` tinyint(1) DEFAULT NULL,
					  `notify_distribution` tinyint(1) DEFAULT NULL,
					  `notify_order_ready` tinyint(1) DEFAULT NULL,
					  `rebate` bigint(20) DEFAULT NULL,
					  `isrebate` tinyint(1) DEFAULT NULL,
					  `logo` varchar(400) DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`),
					  KEY `publish_id` (`publish_id`),
					  KEY `user_id` (`user_id`),
					  KEY `root_id` (`root_id`),
					  KEY `parent_id` (`parent_id`),
					  KEY `state_id` (`state_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_accountextension` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20),
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100),
					  `created` datetime NOT NULL,
					  `updated` datetime,
					  `createdby` bigint(20) NOT NULL,
					  `updatedby` bigint(20),
					  `label` varchar(50) DEFAULT NULL,
					  `ordering` bigint(20),
					  `value` varchar(100),
					  `accounttab_id` bigint(20) NOT NULL,
					  `controller` varchar(4000),
					  `tablocation_id` bigint(20) NOT NULL,
					  `action` varchar(4000),
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`),
					  KEY `accounttab_id` (`accounttab_id`),
					  KEY `tablocation_id` (`tablocation_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			// Link between account and attribute tables
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_account_attribute` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `account_id` bigint(20) NOT NULL,
					  `attribute_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `account_id` (`account_id`),
					  KEY `attribute_id` (`attribute_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			// Link between account and class tables
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_account_class` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `account_id` bigint(20) NOT NULL,
					  `class_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `account_id` (`account_id`),
					  KEY `class_id` (`class_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			// Link between account and codevalue tables
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_account_codevalue` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `account_id` bigint(20) NOT NULL,
					  `codevalue_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `account_id` (`account_id`),
					  KEY `codevalue_id` (`codevalue_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			// Link between account and object tables
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_account_object` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `account_id` bigint(20) NOT NULL,
					  `object_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `account_id` (`account_id`),
					  KEY `object_id` (`object_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			// Link between account and objecttype tables
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_account_objecttype` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `account_id` bigint(20) NOT NULL,
					  `objecttype_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `account_id` (`account_id`),
					  KEY `objecttype_id` (`objecttype_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			/*
			 *  
			 */
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_actor` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20),
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100),
					  `created` datetime NOT NULL,
					  `updated` datetime,
					  `createdby` bigint(20) NOT NULL,
					  `updatedby` bigint(20),
					  `label` varchar(50),
					  `ordering` bigint(20),
					  `role_id` bigint(20) NOT NULL,
					  `account_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`),
					  KEY `role_id` (`role_id`),
					  KEY `account_id` (`account_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			/*
			 * Addresses
			 */
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_address` (
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
					  `ordering` bigint(20),
					  `account_id` bigint(20) NOT NULL,
					  `type_id` bigint(20) NOT NULL,
					  `title_id` bigint(20) NOT NULL,
					  `country_id` bigint(20) NOT NULL,
					  `corporatename1` varchar(100) NOT NULL,
					  `corporatename2` varchar(100) NOT NULL,
					  `agentfirstname` varchar(50) NOT NULL,
					  `agentlastname` varchar(50) NOT NULL,
					  `function` varchar(100) NOT NULL,
					  `street1` varchar(100) NOT NULL,
					  `street2` varchar(100) NOT NULL,
					  `postalcode` varchar(10) NOT NULL,
					  `locality` varchar(50) NOT NULL,
					  `phone` varchar(20) NOT NULL,
					  `fax` varchar(20) NOT NULL,
					  `email` varchar(50) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `code` (`code`),
					  KEY `account_id` (`account_id`),
					  KEY `type_id` (`type_id`),
					  KEY `title_id` (`title_id`),
					  KEY `country_id` (`country_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_catalog` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20) DEFAULT NULL,
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100) DEFAULT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime DEFAULT NULL,
					  `createdby` bigint(20) DEFAULT NULL,
					  `updatedby` bigint(20) DEFAULT NULL,
					  `label` varchar(50) DEFAULT NULL,
					  `ordering` bigint(20) DEFAULT NULL,
					  `catalogtype_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`),
					  KEY `catalogtype_id` (`catalogtype_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}

			$query="CREATE TABLE IF NOT EXISTS `#__sdi_catalog_objecttype` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `catalog_id` bigint(20) NOT NULL,
					  `objecttype_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `catalog_id` (`catalog_id`),
					  KEY `objecttype_id` (`objecttype_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_metadata` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20) DEFAULT NULL,
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100) DEFAULT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime DEFAULT NULL,
					  `createdby` bigint(20) DEFAULT NULL,
					  `updatedby` bigint(20) DEFAULT NULL,
					  `label` varchar(50) DEFAULT NULL,
					  `ordering` bigint(20) DEFAULT NULL,
					  `published` date DEFAULT NULL,
					  `archived` date DEFAULT NULL,
					  `metadatastate_id` bigint(20) NOT NULL,
					  `editor_id` bigint(20) DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  KEY `metadatastate_id` (`metadatastate_id`),
					  KEY `editor_id` (`editor_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_revision` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20) DEFAULT NULL,
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100) DEFAULT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime DEFAULT NULL,
					  `createdby` bigint(20) DEFAULT NULL,
					  `updatedby` bigint(20) DEFAULT NULL,
					  `label` varchar(50) DEFAULT NULL,
					  `ordering` bigint(20) DEFAULT NULL,
					  `date` date DEFAULT NULL,
					  `isfirst` tinyint(1) DEFAULT NULL,
					  `islast` tinyint(1) DEFAULT NULL,
					  `parent_id` bigint(20) NOT NULL,
					  `metadata_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`),
					  KEY `parent_id` (`parent_id`),
					  KEY `metadata_id` (`metadata_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_title` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(50),
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100),
					  `created` datetime NOT NULL,
					  `updated` datetime,
					  `createdby` bigint(20),
					  `updatedby` bigint(20),
					  `label` varchar(50),
					  `ordering` bigint(20),
					  `publish_id` bigint(20),
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`),
					  KEY `publish_id` (`publish_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}

			$query="INSERT INTO `#__sdi_title` (`guid`, `label`, `name`, `description`, `created`, `createdby`, `code`, `publish_id`) VALUES
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MADAM', 'CORE_ACCOUNT_CONTACT_LIST_MADAM', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MADAM', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISTER', 'CORE_ACCOUNT_CONTACT_LIST_MISTER', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISTER', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISS', 'CORE_ACCOUNT_CONTACT_LIST_MISS', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISS', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MASTER', 'CORE_ACCOUNT_CONTACT_LIST_MASTER', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MASTER', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISS_PRESIDENT', 'CORE_ACCOUNT_CONTACT_LIST_MISS_PRESIDENT', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISS_PRESIDENT', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISTER_PRESIDENT', 'CORE_ACCOUNT_CONTACT_LIST_MISTER_PRESIDENT', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISTER_PRESIDENT', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISS_PROPERTY_AGENT', 'CORE_ACCOUNT_CONTACT_LIST_MISS_PROPERTY_AGENT', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISS_PROPERTY_AGENT', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISTER_PROPERTY_AGENT', 'CORE_ACCOUNT_CONTACT_LIST_MISTER_PROPERTY_AGENT', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISTER_PROPERTY_AGENT', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISS_MISTER', 'CORE_ACCOUNT_CONTACT_LIST_MISS_MISTER', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISS_MISTER', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MADAMS', 'CORE_ACCOUNT_CONTACT_LIST_MADAMS', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MADAMS', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISTERS', 'CORE_ACCOUNT_CONTACT_LIST_MISTERS', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISTERS', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISSES', 'CORE_ACCOUNT_CONTACT_LIST_MISSES', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISSES', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MASTERS', 'CORE_ACCOUNT_CONTACT_LIST_MASTERS', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MASTERS', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISS_MISTERS', 'CORE_ACCOUNT_CONTACT_LIST_MISS_MISTERS', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISS_MISTERS', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MISSES_MISTERS', 'CORE_ACCOUNT_CONTACT_LIST_MISSES_MISTERS', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MISSES_MISTERS', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MADAM_MISTERS', 'CORE_ACCOUNT_CONTACT_LIST_MADAM_MISTERS', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MADAM_MISTERS', NULL),
					('".helper_easysdi::getUniqueId()."', 'CORE_ACCOUNT_CONTACT_LIST_MADAMS_MISTER', 'CORE_ACCOUNT_CONTACT_LIST_MADAMS_MISTER', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.", 'CORE_ACCOUNT_CONTACT_LIST_MADAMS_MISTER', NULL)";
					$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_object` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20),
					  `name` varchar(100) NOT NULL,
					  `description` varchar(100),
					  `created` datetime NOT NULL,
					  `updated` datetime,
					  `createdby` bigint(20),
					  `updatedby` bigint(20),
					  `label` varchar(50),
					  `ordering` bigint(20) NOT NULL DEFAULT 0,
					  `objecttype_id` bigint(20) NOT NULL,
					  `published` tinyint(1) NOT NULL,
					  `account_id` bigint(20) NOT NULL,
					  `checked_out` bigint(20) NOT NULL,
					  `checked_out_time` datetime,
					  `visibility_id` bigint(20) NOT NULL,
					  `metadata_id` bigint(20),
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  KEY `objecttype_id` (`objecttype_id`),
					  KEY `account_id` (`account_id`),
					  KEY `visibility_id` (`visibility_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
				
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_objecttype` (
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
				  `isoscopecode` varchar(50) DEFAULT 'dataset',
				  `profile_id` bigint(20),
				  `predefined` tinyint(1),
				  `hasVersioning` tinyint(4) NOT NULL,
				  `logo` varchar(400),
				  `fragment` varchar(50),
				  `fragmentnamespace_id` bigint(20),
				  PRIMARY KEY (`id`),
				  KEY `profile_id` (`profile_id`),
				  KEY `fragmentnamespace_id` (`fragmentnamespace_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_codelang` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20) NOT NULL,
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100) NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime NULL,
					  `createdby` bigint(20),
					  `updatedby` bigint(20) NULL,
					  `ordering` bigint(20) NOT NULL DEFAULT 0,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="INSERT INTO `#__sdi_list_codelang` (`guid`, `code`, `name`, `description`, `created`, `createdby`) VALUES
					('".helper_easysdi::getUniqueId()."', 'ar-DZ', 'ar-DZ - Arabic - Algeria', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'be-BY', 'be-BY - Belarusian (Belarus)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'bg-BG', 'bg-BG - Bulgarian', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'bn-BD', 'bn-BD - Bengali (Bangladesh)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'ca-ES', 'ca-ES - Catalan', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'cs-CZ', 'cs-CZ - Czech (Czech)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'da-DK', 'da-DK - Danish(DK)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'de-DE', 'de-DE - Deutsch (DE)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'el-GR', 'el-GR - Greek', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'en-GB', 'en-GB - English (United Kingdom)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'en-US', 'en-US - English (US)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'eo-XX', 'eo-XX - Esperanto', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'es-ES', 'es-ES - Spanish (Espa�ol internacional)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'et-EE', 'et-EE - Estonian - (Estonia)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'eu-ES', 'eu-ES - Basque (Euskara estandarra)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'fa-IR', 'fa-IR - Persian', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'fi-FI', 'fi-FI - Finnish (Suomi)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'fr-FR', 'fr-FR - French (Fr)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'gu-IN', 'gu-IN - Gujarati (India)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'hr-HR', 'hr-HR - Croatian (HR)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'hu-HU', 'hu-HU - Hungarian (Magyar)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'is-IS', 'is-IS - �slenska (Iceland)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'it-IT', 'it-IT - Italian (Italy)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'ja-JP', 'ja-JP - Japanese(JP)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'lo-LA', 'lo-LA - Lao', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'lt-LT', 'lt-LT - Lithuanian', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'lv-LV', 'lv-LV - Latvian', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'mn-MN', 'mn-MN - Mongolian', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'nb-BO', 'nb-NO - Norsk bokm�l (Norway)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'nl-NL', 'nl-NL - Nederlands (nl-NL)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'pl-PL', 'pl-PL - Polish (Poland)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'pt-BR', 'pt-BR - Portugu�s (Brasil)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'pt-PT', 'pt-PT - Portugu�s (pt-PT)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'ro-RO', 'ro-RO - Rom�na (Rom�nia)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'ru-RU', 'ru-RU - Russian (Russian Federation)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'sd-PK', 'sd-PK - Sindhi', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'si-LK', 'si-LK - Sinhala (Sri Lanka)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'sk-SK', 'sk-SK - Slovencina (Slovensk� Republika)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'sr-ME', 'sr-ME - Ijekavski (ME)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'sr-RS', 'sr-RS - Serbian (RS)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'sv-SE', 'sv-SE - Svenska (Sverige)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'sy-IQ', 'sy-IQ - Syriac(Iraq)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'ta-LK', 'ta-LK - Tamil (Sri Lanka)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'th-TH', 'th-TH - Thai', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'tr-TR', 'tr-TR - T�rk�e (T�rkiye)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'uk-UA', 'uk-UA - Ukrainian (Ukraine)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'ur-PK', 'ur-PK - Urdu Pakistan', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'vi-VN', 'vi-VN - Vietnamese', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'zh-CN', 'zh-CN - Simplified Chinese', NULL, '".date('Y-m-d H:i:s')."', ".$user_id."),
					('".helper_easysdi::getUniqueId()."', 'zh-TW', 'zh-TW - Traditional Chinese (Taiwan)', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.")";
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			
			$query="CREATE TABLE IF NOT EXISTS #__sdi_language (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `codelang_id` bigint(20),
					  `name` varchar(50) NOT NULL,
					  `label` varchar(50),
					  `description` varchar(100),
					  `created` datetime NOT NULL,
					  `updated` datetime,
					  `createdby` bigint(20),
					  `updatedby` bigint(20),
					  `ordering` bigint(20) NOT NULL DEFAULT 0,
					  `published` tinyint(1) DEFAULT 0,
					  `code` varchar(3),
					  `isocode` varchar(3),
					  `defaultlang` tinyint(1) NOT NULL,
					  `gemetlang` varchar(2),
					  PRIMARY KEY (`id`),
					  KEY `codelang_id` (`codelang_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_accountprofile` (
						  `id` bigint(20) NOT NULL AUTO_INCREMENT,
						  `guid` varchar(36) NOT NULL,
						  `code` varchar(20) DEFAULT NULL,
						  `name` varchar(50) NOT NULL,
						  `description` varchar(100) DEFAULT NULL,
						  `created` datetime NOT NULL,
						  `updated` datetime DEFAULT NULL,
						  `createdby` bigint(20) NOT NULL,
						  `updatedby` bigint(20) DEFAULT NULL,
						  `translation` varchar(50) DEFAULT NULL,
						  `ordering` bigint(20) DEFAULT NULL,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `guid` (`guid`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			// Link between account and accountprofile tables
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_account_accountprofile` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `account_id` bigint(20) NOT NULL,
					  `accountprofile_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `account_id` (`account_id`),
					  KEY `accountprofile_id` (`accountprofile_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_roletype` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20) DEFAULT NULL,
					  `name` varchar(50) NOT NULL,
					  `description` varchar(100) DEFAULT NULL,
					  `created` datetime NOT NULL,
					  `updated` datetime DEFAULT NULL,
					  `createdby` bigint(20) DEFAULT NULL,
					  `updatedby` bigint(20) DEFAULT NULL,
					  `ordering` bigint(20) DEFAULT NULL,
					  `publish_id` tinyint(1) NOT NULL DEFAULT 0,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  KEY `publish_id` (`publish_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="INSERT INTO `#__sdi_list_roletype` (`guid`, `code`, `name`, `description`, `created`, `createdby`) VALUES  
					('".helper_easysdi::getUniqueId()."', NULL, 'CORE_CATALOG_ROLE_TYPE_FCT', NULL, '".date('Y-m-d H:i:s')."', ".$user_id.");
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}		
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_module_panel` (
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
					  `ordering` bigint(20) ,
					  `module_id` bigint(20) NOT NULL,
					  `view_path` varchar(250) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";	 		
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_systemaccount` (
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `guid` varchar(36) NOT NULL,
					  `code` varchar(20),
					  `name` varchar(50),
					  `description` varchar(100),
					  `created` datetime NOT NULL,
					  `updated` datetime,
					  `createdby` bigint(20) NOT NULL,
					  `updatedby` bigint(20),
					  `label` varchar(50),
					  `ordering` bigint(20) ,
					  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
					  `checked_out_time`  datetime NULL DEFAULT NULL ,
					  `account_id` bigint(20) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `guid` (`guid`),
					  UNIQUE KEY `code` (`code`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";	 		
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				return false;
			}
			
			/*
			 * Constraints
			 */
			$query="ALTER TABLE `#__sdi_actor`
					  ADD CONSTRAINT `#__sdi_actor_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `#__sdi_list_role` (`id`),
					  ADD CONSTRAINT `#__sdi_actor_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `#__sdi_account` (`id`);
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="ALTER TABLE `#__sdi_catalog`
  						ADD CONSTRAINT `#__sdi_catalog_ibfk_1` FOREIGN KEY (`catalogtype_id`) REFERENCES `#__sdi_list_catalogtype` (`id`);
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="ALTER TABLE `#__sdi_catalog_objecttype`
					  ADD CONSTRAINT `#__sdi_catalog_objecttype_ibfk_1` FOREIGN KEY (`catalog_id`) REFERENCES `#__sdi_catalog` (`id`),
					  ADD CONSTRAINT `#__sdi_catalog_objecttype_ibfk_2` FOREIGN KEY (`objecttype_id`) REFERENCES `#__sdi_objecttype` (`id`);
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="ALTER TABLE `#__sdi_configuration`
  						ADD CONSTRAINT `#__sdi_configuration_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `#__sdi_list_module` (`id`);
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="ALTER TABLE `#__sdi_metadata`
  						ADD CONSTRAINT `#__sdi_metadata_ibfk_1` FOREIGN KEY (`metadatastate_id`) REFERENCES `#__sdi_list_metadatastate` (`id`);
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="ALTER TABLE `#__sdi_object`
					  ADD CONSTRAINT `#__sdi_object_ibfk_1` FOREIGN KEY (`objecttype_id`) REFERENCES `#__sdi_objecttype` (`id`),
					  ADD CONSTRAINT `#__sdi_object_ibfk_4` FOREIGN KEY (`account_id`) REFERENCES `#__sdi_account` (`id`);
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="ALTER TABLE `#__sdi_revision`
					  ADD CONSTRAINT `#__sdi_revision_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `#__sdi_revision` (`id`),
					  ADD CONSTRAINT `#__sdi_revision_ibfk_2` FOREIGN KEY (`metadata_id`) REFERENCES `#__sdi_metadata` (`id`);
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="ALTER TABLE `#__sdi_language`
	  				ADD CONSTRAINT `#__sdi_language_ibfk_1` FOREIGN KEY (`codelang_id`) REFERENCES `#__sdi_list_codelang` (`id`);";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			
			$query="ALTER TABLE `#__sdi_account_accountprofile`
					  ADD CONSTRAINT `#__sdi_account_accountprofile_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `#__sdi_account` (`id`),
					  ADD CONSTRAINT `#__sdi_account_accountprofile_ibfk_2` FOREIGN KEY (`accountprofile_id`) REFERENCES `#__sdi_accountprofile` (`id`);
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			$query="ALTER TABLE `#__sdi_list_role`
  						ADD CONSTRAINT `#__sdi_list_role_ibfk_1` FOREIGN KEY (`roletype_id`) REFERENCES `#__sdi_list_roletype` (`id`);
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
				
			$query="ALTER TABLE `#__sdi_object`
					  ADD CONSTRAINT `#__sdi_object_ibfk_6` FOREIGN KEY (`visibility_id`) REFERENCES `#__sdi_list_visibility` (`id`)
					";
			$db->setQuery( $query);	
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
			}
			
			// Update component version
			$version="1.0";
			$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CORE'"; 
			$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
	}
	if ($version == "1.0")
	{
		// Update component version
		$version="2.0.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CORE'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version == "2.0.0")
	{
		// Update component version
		$version="2.1.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CORE'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version == "2.1.0")
	{
		$query="ALTER TABLE #__sdi_account MODIFY COLUMN url VARCHAR(200)";
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		// Update component version
		$version="2.1.1";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CORE'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version == "2.1.1")
	{
		$query="ALTER TABLE #__sdi_accountprofile ADD COLUMN applyRebate TINYINT(1)";
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		// Update component version
		$version="2.1.1.1";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CORE'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version == "2.1.1.1")
	{
		
		// Update component version
		$version="2.2.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CORE'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version =="2.2.0"){
		
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
			FOREIGN KEY (`language_id`) REFERENCES `#__sdi_language` (`id`),
			KEY `language_id` (`language_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
				
		
		// Update component version
		$version="2.3.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CORE'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version == "2.3.0")
	{
		// Update component version
		$version="2.4.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CORE'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version == "2.4.0")
	{
		// Update component version
		$version="2.5.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='CORE'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	
	/**
	 * Menu creation
	 */
	
	$query =  "SELECT ID FROM #__components WHERE name ='EasySDI'" ;		
	$db->setQuery( $query);
	$id = $db->loadResult();	
	if ($id)
	{
 		$mainframe->enqueueMessage("EASYSDI menu is already existing. Usually this menu is created during the installation of this component. Maybe something goes wrong during the previous uninstall !","INFO"); 	 	
	}
	else
	{
		//Insert the EasySdi Main Menu
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_core' ";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}
		
		$query =  "INSERT INTO #__components (name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
			values('EasySDI','option=com_easysdi_core','option=com_easysdi_core','Easysdi main menu','com_easysdi_core','components/com_easysdi_core/common/icons/favicon16.ico','')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}
		$query =  "SELECT ID FROM #__components WHERE name ='EasySDI'"  ;
		$db->setQuery( $query);
		$id = $db->loadResult();	
	}
	
	$mainframe->enqueueMessage("Congratulation core components for EasySdi Core are installed and ready to be used. 
								Enjoy EasySdi Core!","INFO");
	

}
?>