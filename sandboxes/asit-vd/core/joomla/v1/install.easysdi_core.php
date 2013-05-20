<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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


	/**
	 * Creates the database structure
	 */
	$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_version` (
		 	 `component` varchar(100) NOT NULL default '',
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `version` varchar(100) NOT NULL default '',
		 	 PRIMARY KEY  (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;"; 		
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	/**
	 * Gets the component versions
	 */
	$version = '0';
	$query = "SELECT version FROM #__easysdi_version where component = 'com_easysdi_core'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if (!$version)
	{
		$version="0";
	}
	if ($db->getErrorNum())
	{
		//The table does'nt exist
		//That means nothing is installed.
		$mainframe->enqueueMessage("EASYSDI IS NOT INSTALLED","ERROR");
		exit;
	}
	else
	{
		if ($version == "0")
		{
			/**
			 * Create the configuration table
			 * Insert value for JAVA_BRIDGE_URL
			 */
			$query="CREATE TABLE IF NOT EXISTS  `#__easysdi_config` (
				 	 `id` bigint(20) NOT NULL auto_increment,
				  	`thekey` varchar(100) NOT NULL default '',
				  	`value` varchar(100) NOT NULL default '',
				 	 PRIMARY KEY  (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query = "insert into `#__easysdi_config` values(0,'JAVA_BRIDGE_URL','http://localhost:8081/JavaBridge/java/Java.inc')";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE `#__easysdi_partner_extension` (
					  `ext_id` bigint(20) NOT NULL auto_increment,
					  `tab_id` bigint(20) NOT NULL,
					  `order_number` bigint(20) NOT NULL,
					  `code` varchar(4000) NOT NULL,
					  `action` varchar(4000) NOT NULL,
					  `tab_location` varchar(3000) NOT NULL,
					  PRIMARY KEY  (`ext_id`)
					  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"; 
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query ="SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query = "CREATE TABLE IF NOT EXISTS `#__easysdi_community_partner` (
				  `user_id` int(11) NOT NULL default '0',
				  `publish_id` tinyint(1) NOT NULL default '0',
				  `partner_id` bigint(20) NOT NULL auto_increment,
				  `root_id` bigint(20) default NULL,
				  `parent_id` bigint(20) default NULL,
				  `state_id` bigint(20) default NULL,
				  `partner_code` varchar(50) NOT NULL,  
				  `partner_acronym` varchar(50) default NULL,
				  `partner_url` varchar(50) default NULL,
				  `partner_description` varchar(100) default NULL,
				  `partner_invoice` date default NULL,
				  `partner_call1` date default NULL,
				  `partner_call2` date default NULL,
				  `partner_contract` bigint(20) NOT NULL default '0',  
				  `partner_update` datetime default NULL,
				  PRIMARY KEY  (`partner_id`),
				  UNIQUE KEY `partner_code` (`partner_code`),
				  KEY `user_id` (`user_id`),
				  KEY `parent_id` (`parent_id`),
				  KEY `root_id` (`root_id`),
				  KEY `state_id` (`state_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";	
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__easysdi_community_actor` (
					  `actor_id` bigint(20) NOT NULL auto_increment,
					  `role_id` bigint(20) default NULL,
					  `partner_id` bigint(20) default NULL,
					  `actor_update` timestamp NULL default NULL,
					  PRIMARY KEY  (`actor_id`),
					  KEY `partner_id` (`partner_id`),
					  KEY `role_id` (`role_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__easysdi_community_address` (
					  `address_id` bigint(20) NOT NULL auto_increment,
					  `partner_id` bigint(20) NOT NULL,
					  `type_id` bigint(20) NOT NULL,
					  `title_id` bigint(20) NOT NULL,
					  `country_code` varchar(2) NOT NULL,
					  `address_corporate_name1` varchar(100) default NULL,
					  `address_corporate_name2` varchar(100) default NULL,
					  `address_agent_firstname` varchar(50) default NULL,
					  `address_agent_lastname` varchar(50) default NULL,
					  `address_agent_function` varchar(100) default NULL,
					  `address_street1` varchar(100) default NULL,
					  `address_street2` varchar(100) default NULL,
					  `address_postalcode` varchar(10) default NULL,
					  `address_locality` varchar(50) default NULL,
					  `address_phone` varchar(20) default NULL,
					  `address_fax` varchar(20) default NULL,
					  `address_email` varchar(50) default NULL,
					  `address_update` datetime default NULL,
					  PRIMARY KEY  (`address_id`),
					  KEY `partner_id` (`partner_id`),
					  KEY `type_id` (`type_id`),
					  KEY `title_id` (`title_id`),
					  KEY `country_code` (`country_code`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__easysdi_community_address_type` (
				  `type_id` bigint(20) NOT NULL auto_increment,
				  `publish_id` tinyint(1) NOT NULL default '0',
				  `type_code` varchar(20) default NULL,
				  `type_name` varchar(50) NOT NULL,
				  `type_description` varchar(100) default NULL,
				  `type_update` datetime default NULL,
				  PRIMARY KEY  (`type_id`),
				  KEY `publish_id` (`publish_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__easysdi_community_country` (
					  `country_code` varchar(2) NOT NULL,
					  `publish_id` tinyint(1) NOT NULL default '0',
					  `country_name` varchar(100) NOT NULL,
					  `country_update` datetime default NULL,
					  PRIMARY KEY  (`country_code`),
					  KEY `publish_id` (`publish_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__easysdi_community_country` (`country_code`, `publish_id`, `country_name`, `country_update`) VALUES
					('AD', 0, 'ANDORRE', NULL),
					('AE', 0, 'EMIRATS ARABES UNIS', NULL),
					('AF', 0, 'AFGHANISTAN', NULL),
					('AG', 0, 'ANTIGUA-ET-BARBUDA', NULL),
					('AI', 0, 'ANGUILLA', NULL),
					('AL', 0, 'ALBANIE', NULL),
					('AM', 0, 'ARMENIE', NULL),
					('AN', 0, 'ANTILLES NEERLANDAISES', NULL),
					('AO', 0, 'ANGOLA', NULL),
					('AQ', 0, 'ANTARCTIQUE', NULL),
					('AR', 0, 'ARGENTINE', NULL),
					('AS', 0, 'SAMOA AMERICAINES', NULL),
					('AT', 0, 'AUTRICHE', NULL),
					('AU', 0, 'AUSTRALIE', NULL),
					('AW', 0, 'ARUBA', NULL),
					('AX', 0, 'ELAND, ELES D''', NULL),
					('AZ', 0, 'AZERBAEDJAN', NULL),
					('BA', 0, 'BOSNIE-HERZEGOVINE', NULL),
					('BB', 0, 'BARBADE', NULL),
					('BD', 0, 'BANGLADESH', NULL),
					('BE', 0, 'BELGIQUE', NULL),
					('BF', 0, 'BURKINA FASO', NULL),
					('BG', 0, 'BULGARIE', NULL),
					('BH', 0, 'BAHREIN', NULL),
					('BI', 0, 'BURUNDI', NULL),
					('BJ', 0, 'BENIN', NULL),
					('BM', 0, 'BERMUDES', NULL),
					('BN', 0, 'BRUNDI DARUSSALAM', NULL),
					('BO', 0, 'BOLIVIE', NULL),
					('BR', 0, 'BRESIL', NULL),
					('BS', 0, 'BAHAMAS', NULL),
					('BT', 0, 'BHOUTAN', NULL),
					('BV', 0, 'BOUVET, ILE', NULL),
					('BW', 0, 'BOTSWANA', NULL),
					('BY', 0, 'BILARUS', NULL),
					('BZ', 0, 'BELIZE', NULL),
					('CA', 0, 'CANADA', NULL),
					('CC', 0, 'COCOS (KEELING), ILES', NULL),
					('CD', 0, 'CONGO, LA REPUBLIQUE DEMOCRATIQUE DU', NULL),
					('CF', 0, 'CENTRAFRICAINE, REPUBLIQUE', NULL),
					('CG', 0, 'CONGO', NULL),
					('CH', 0, 'SUISSE', NULL),
					('CI', 0, 'COTE D''IVOIRE', NULL),
					('CK', 0, 'COOK, ILES', NULL),
					('CL', 0, 'CHILI', NULL),
					('CM', 0, 'CAMEROUN', NULL),
					('CN', 0, 'CHINE', NULL),
					('CO', 0, 'COLOMBIE', NULL),
					('CR', 0, 'COSTA RICA', NULL),
					('CS', 0, 'SERBIE-ET-MONTENEGRO', NULL),
					('CU', 0, 'CUBA', NULL),
					('CV', 0, 'CAP-VERT', NULL),
					('CX', 0, 'CHRISTMAS, ILE', NULL),
					('CY', 0, 'CHYPRE', NULL),
					('CZ', 0, 'TCHEQUE, REPUBLIQUE', NULL),
					('DE', 0, 'ALLEMAGNE', NULL),
					('DJ', 0, 'DJIBOUTI', NULL),
					('DK', 0, 'DANEMARK', NULL),
					('DM', 0, 'DOMINIQUE', NULL),
					('DO', 0, 'DOMINICAINE, REPUBLIQUE', NULL),
					('DZ', 0, 'ALGERIE', NULL),
					('EC', 0, 'EQUATEUR', NULL),
					('EE', 0, 'ESTONIE', NULL),
					('EG', 0, 'EGYPTE', NULL),
					('EH', 0, 'SAHARA OCCIDENTAL', NULL),
					('ER', 0, 'ERYTHREE', NULL),
					('ES', 0, 'ESPAGNE', NULL),
					('ET', 0, 'ETHIOPIE', NULL),
					('FI', 0, 'FINLANDE', NULL),
					('FJ', 0, 'FIDJI', NULL),
					('FK', 0, 'FALKLAND, ELES (MALVINAS)', NULL),
					('FM', 0, 'MICRONESIE, ETATS FEDERES DE', NULL),
					('FO', 0, 'FEROE, ELES', NULL),
					('FR', 0, 'FRANCE', NULL),
					('GA', 0, 'GABON', NULL),
					('GB', 0, 'ROYAUME-UNI', NULL),
					('GD', 0, 'GRENADE', NULL),
					('GE', 0, 'GEORGIE', NULL),
					('GF', 0, 'GUYANE FRANEAISE', NULL),
					('GH', 0, 'GHANA', NULL),
					('GI', 0, 'GIBRALTAR', NULL),
					('GL', 0, 'GROENLAND', NULL),
					('GM', 0, 'GAMBIE', NULL),
					('GN', 0, 'GUINEE', NULL),
					('GP', 0, 'GUADELOUPE', NULL),
					('GQ', 0, 'GUINEE EQUATORIALE', NULL),
					('GR', 0, 'GRECE', NULL),
					('GS', 0, 'GEORGIE DU SUD ET LES ELES SANDWICH DU SUD', NULL),
					('GT', 0, 'GUATEMALA', NULL),
					('GU', 0, 'GUAM', NULL),
					('GW', 0, 'GUINEE-BISSAU', NULL),
					('GY', 0, 'GUYANA', NULL),
					('HK', 0, 'HONG-KONG', NULL),
					('HM', 0, 'HEARD, ELE ET MCDONALD, ELES', NULL),
					('HN', 0, 'HONDURAS', NULL),
					('HR', 0, 'CROATIE', NULL),
					('HT', 0, 'HAETI', NULL),
					('HU', 0, 'HONGRIE', NULL),
					('ID', 0, 'INDONESIE', NULL),
					('IE', 0, 'IRLANDE', NULL),
					('IL', 0, 'ISRAEL', NULL),
					('IN', 0, 'INDE', NULL),
					('IO', 0, 'OCEAN INDIEN, TERRITOIRE BRITANNIQUE DE L''', NULL),
					('IQ', 0, 'IRAQ', NULL),
					('IR', 0, 'IRAN, REPUBLIQUE ISLAMIQUE D''', NULL),
					('IS', 0, 'ISLANDE', NULL),
					('IT', 0, 'ITALIE', NULL),
					('JM', 0, 'JAMAEQUE', NULL),
					('JO', 0, 'JORDANIE', NULL),
					('JP', 0, 'JAPON', NULL),
					('KE', 0, 'KENYA', NULL),
					('KG', 0, 'KIRGHIZISTAN', NULL),
					('KH', 0, 'CAMBODGE', NULL),
					('KI', 0, 'KIRIBATI', NULL),
					('KM', 0, 'COMORES', NULL),
					('KN', 0, 'SAINT-KITTS-ET-NEVIS', NULL),
					('KP', 0, 'COREE, REPUBLIQUE POPULAIRE DEMOCRATIQUE DE', NULL),
					('KR', 0, 'COREE, REPUBLIQUE DE', NULL),
					('KW', 0, 'KOWEET', NULL),
					('KY', 0, 'CAEMANES, ELES', NULL),
					('KZ', 0, 'KAZAKHSTAN', NULL),
					('LA', 0, 'LAO, REPUBLIQUE DEMOCRATIQUE POPULAIRE', NULL),
					('LB', 0, 'LIBAN', NULL),
					('LC', 0, 'SAINTE-LUCIE', NULL),
					('LI', 0, 'LIECHTENSTEIN', NULL),
					('LK', 0, 'SRI LANKA', NULL),
					('LR', 0, 'LIBERIA', NULL),
					('LS', 0, 'LESOTHO', NULL),
					('LT', 0, 'LITUANIE', NULL),
					('LU', 0, 'LUXEMBOURG', NULL),
					('LV', 0, 'LETTONIE', NULL),
					('LY', 0, 'LIBYENNE, JAMAHIRIYA ARABE', NULL),
					('MA', 0, 'MAROC', NULL),
					('MC', 0, 'MONACO', NULL),
					('MD', 0, 'MOLDOVA, REPUBLIQUE DE', NULL),
					('MG', 0, 'MADAGASCAR', NULL),
					('MH', 0, 'MARSHALL, ELES', NULL),
					('MK', 0, 'MACEDOINE, L''EX-REPUBLIQUE YOUGOSLAVE DE', NULL),
					('ML', 0, 'MALI', NULL),
					('MM', 0, 'MYANMAR', NULL),
					('MN', 0, 'MONGOLIE', NULL),
					('MO', 0, 'MACAO', NULL),
					('MP', 0, 'MARIANNES DU NORD, ELES', NULL),
					('MQ', 0, 'MARTINIQUE', NULL),
					('MR', 0, 'MAURITANIE', NULL),
					('MS', 0, 'MONTSERRAT', NULL),
					('MT', 0, 'MALTE', NULL),
					('MU', 0, 'MAURICE', NULL),
					('MV', 0, 'MALDIVES', NULL),
					('MW', 0, 'MALAWI', NULL),
					('MX', 0, 'MEXIQUE', NULL),
					('MY', 0, 'MALAISIE', NULL),
					('MZ', 0, 'MOZAMBIQUE', NULL),
					('NA', 0, 'NAMIBIE', NULL),
					('NC', 0, 'NOUVELLE-CALEDONIE', NULL),
					('NE', 0, 'NIGER', NULL),
					('NF', 0, 'NORFOLK, ELE', NULL),
					('NG', 0, 'NIGERIA', NULL),
					('NI', 0, 'NICARAGUA', NULL),
					('NL', 0, 'PAYS-BAS', NULL),
					('NO', 0, 'NORVEGE', NULL),
					('NP', 0, 'NEPAL', NULL),
					('NR', 0, 'NAURU', NULL),
					('NU', 0, 'NIUE', NULL),
					('NZ', 0, 'NOUVELLE-ZELANDE', NULL),
					('OM', 0, 'OMAN', NULL),
					('PA', 0, 'PANAMA', NULL),
					('PE', 0, 'PEROU', NULL),
					('PF', 0, 'POLYNESIE FRANEAISE', NULL),
					('PG', 0, 'PAPOUASIE-NOUVELLE-GUINEE', NULL),
					('PH', 0, 'PHILIPPINES', NULL),
					('PK', 0, 'PAKISTAN', NULL),
					('PL', 0, 'POLOGNE', NULL),
					('PM', 0, 'SAINT-PIERRE-ET-MIQUELON', NULL),
					('PN', 0, 'PITCAIRN', NULL),
					('PR', 0, 'PORTO RICO', NULL),
					('PS', 0, 'PALESTINIEN OCCUPE, TERRITOIRE', NULL),
					('PT', 0, 'PORTUGAL', NULL),
					('PW', 0, 'PALAOS', NULL),
					('PY', 0, 'PARAGUAY', NULL),
					('QA', 0, 'QATAR', NULL),
					('RE', 0, 'REUNION', NULL),
					('RO', 0, 'ROUMANIE', NULL),
					('RU', 0, 'RUSSIE, FEDERATION DE', NULL),
					('RW', 0, 'RWANDA', NULL),
					('SA', 0, 'ARABIE SAOUDITE', NULL),
					('SB', 0, 'SALOMON, ELES', NULL),
					('SC', 0, 'SEYCHELLES', NULL),
					('SD', 0, 'SOUDAN', NULL),
					('SE', 0, 'SUEDE', NULL),
					('SG', 0, 'SINGAPOUR', NULL),
					('SH', 0, 'SAINTE-HELENE', NULL),
					('SI', 0, 'SLOVENIE', NULL),
					('SJ', 0, 'SVALBARD ET ELE JAN MAYEN', NULL),
					('SK', 0, 'SLOVAQUIE', NULL),
					('SL', 0, 'SIERRA LEONE', NULL),
					('SM', 0, 'SAINT-MARIN', NULL),
					('SN', 0, 'SENEGAL', NULL),
					('SO', 0, 'SOMALIE', NULL),
					('SR', 0, 'SURINAME', NULL),
					('ST', 0, 'SAO TOME-ET-PRINCIPE', NULL),
					('SV', 0, 'EL SALVADOR', NULL),
					('SY', 0, 'SYRIENNE, REPUBLIQUE ARABE', NULL),
					('SZ', 0, 'SWAZILAND', NULL),
					('TC', 0, 'TURKS ET CAEQUES, ELES', NULL),
					('TD', 0, 'TCHAD', NULL),
					('TF', 0, 'TERRES AUSTRALES FRANEAISES', NULL),
					('TG', 0, 'TOGO', NULL),
					('TH', 0, 'THAELANDE', NULL),
					('TJ', 0, 'TADJIKISTAN', NULL),
					('TK', 0, 'TOKELAU', NULL),
					('TL', 0, 'TIMOR-LESTE', NULL),
					('TM', 0, 'TURKMENISTAN', NULL),
					('TN', 0, 'TUNISIE', NULL),
					('TO', 0, 'TONGA', NULL),
					('TR', 0, 'TURQUIE', NULL),
					('TT', 0, 'TRINITE-ET-TOBAGO', NULL),
					('TV', 0, 'TUVALU', NULL),
					('TW', 0, 'TAEWAN, PROVINCE DE CHINE', NULL),
					('TZ', 0, 'TANZANIE, REPUBLIQUE-UNIE DE', NULL),
					('UA', 0, 'UKRAINE', NULL),
					('UG', 0, 'OUGANDA', NULL),
					('UM', 0, 'ELES MINEURES ELOIGNEES DES ETATS-UNIS', NULL),
					('US', 0, 'ETATS-UNIS', NULL),
					('UY', 0, 'URUGUAY', NULL),
					('UZ', 0, 'OUZBEKISTAN', NULL),
					('VA', 0, 'SAINT-SIEGE (ETAT DE LA CITE DU VATICAN)', NULL),
					('VC', 0, 'SAINT-VINCENT-ET-LES GRENADINES', NULL),
					('VE', 0, 'VENEZUELA', NULL),
					('VG', 0, 'ELES VIERGES BRITANNIQUES', NULL),
					('VI', 0, 'ELES VIERGES DES ETATS-UNIS', NULL),
					('VN', 0, 'VIET NAM', NULL),
					('VU', 0, 'VANUATU', NULL),
					('WF', 0, 'WALLIS ET FUTUNA', NULL),
					('WS', 0, 'SAMOA', NULL),
					('YE', 0, 'YEMEN', NULL),
					('YT', 0, 'MAYOTTE', NULL),
					('ZA', 0, 'AFRIQUE DU SUD', NULL),
					('ZM', 0, 'ZAMBIE', NULL),
					('ZW', 0, 'ZIMBABWE', NULL)";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__easysdi_community_role` (
					  `role_id` bigint(20) NOT NULL auto_increment,
					  `publish_id` tinyint(1) NOT NULL default '0',
					  `type_id` bigint(20) NOT NULL,
					  `role_code` varchar(20) NOT NULL,
					  `role_name` varchar(50) NOT NULL,
					  `role_description` varchar(100) default NULL,
					  `role_update` datetime default NULL,
					  PRIMARY KEY  (`role_id`),
					  UNIQUE KEY `role_code` (`role_code`),
					  KEY `publish_id` (`publish_id`),
					  KEY `type_id` (`type_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__easysdi_community_role` (`role_id`, `publish_id`, `type_id`, `role_code`, `role_name`, `role_description`, `role_update`) VALUES
					(1, 0, 1, 'REQUEST_EXTERNAL', 'EASYSDI_REQUEST_EXTERNAL_RIGHT', 'Commande de données externes', NULL),
					(2, 0, 1, 'METADATA', 'EASYSDI_METADATA_RIGHT', 'Gestion de métadonnEes', NULL),
					(3, 0, 1, 'FORMULARY', 'EASYSDI_FORMULARY_RIGHT', 'Gestion de formulaires', NULL),
					(4, 0, 1, 'REQUEST_INTERNAL', 'EASYSDI_REQUEST_INTERNAL_RIGHT', 'Commande de données internes', NULL),
					(5, 0, 1, 'ACCOUNT', 'EASYSDI_ACCOUNT_RIGHT', 'Gestion d''acomptes affiliés', NULL),
					(6, 0, 1, 'MYACCOUNT', 'EASYSDI_MYACCOUNT_RIGHT', NULL, NULL),
					(7, 0, 1, 'INTERNAL', 'EASYSDI_INTERNAL_RIGHT', NULL, NULL),
					(8, 0, 1, 'TIERCE', 'EASYSDI_TIERCE_RIGHT', 'Commande pour un tiers', NULL)";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__easysdi_community_role_type` (
				  `type_id` bigint(20) NOT NULL auto_increment,
				  `publish_id` tinyint(1) NOT NULL,
				  `type_code` varchar(20) default NULL,
				  `type_name` varchar(50) NOT NULL,
				  `type_description` varchar(100) default NULL,
				  `type_update` datetime default NULL,
				  PRIMARY KEY  (`type_id`),
				  KEY `publish_id` (`publish_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__easysdi_community_role_type` (`type_id`, `publish_id`, `type_code`, `type_name`, `type_description`, `type_update`) VALUES
					(1, 0, NULL, 'GEoportail', NULL, '0000-00-00 00:00:00')";	
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="CREATE TABLE IF NOT EXISTS `#__easysdi_community_title` (
					  `title_id` bigint(20) NOT NULL auto_increment,
					  `publish_id` tinyint(1) NOT NULL default '0',
					  `title_code` varchar(20) default NULL,
					  `title_name` varchar(50) NOT NULL,
					  `title_description` varchar(100) default NULL,
					  `title_update` datetime default NULL,
					  PRIMARY KEY  (`title_id`),
					  KEY `publish_id` (`publish_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="INSERT INTO `#__easysdi_community_title` (`title_id`, `publish_id`, `title_code`, `title_name`, `title_description`, `title_update`) VALUES
					(1, 0, NULL, 'Madame', NULL, NULL),
					(2, 0, NULL, 'Monsieur', NULL, NULL),
					(3, 0, NULL, 'Mademoiselle', NULL, NULL),
					(4, 0, NULL, 'MaEtre', NULL, NULL),
					(5, 0, NULL, 'Madame la PrEsidente', NULL, NULL),
					(6, 0, NULL, 'Monsieur le PrEsident', NULL, NULL),
					(7, 0, NULL, 'Madame la Syndic', NULL, NULL),
					(8, 0, NULL, 'Monsieur le Syndic', NULL, NULL),
					(9, 0, NULL, 'Madame, Monsieur', NULL, NULL)";
			$db->setQuery( $query);
			if (!$db->query())
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="
					INSERT INTO `#__easysdi_community_address_type` (`type_id`, `publish_id`, `type_code`, `type_name`, `type_description`, `type_update`) VALUES
					(1, 0, '', 'Contact', NULL, '0000-00-00 00:00:00'),
					(2, 0, '', 'Facturation', NULL, '0000-00-00 00:00:00'),
					(3, 0, '', 'Livraison', NULL, '0000-00-00 00:00:00')";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__easysdi_community_role`
					  ADD CONSTRAINT `#__easysdi_community_role_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `#__easysdi_community_role_type` (`type_id`) ON UPDATE CASCADE";		
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__easysdi_community_address`
				  ADD CONSTRAINT `#__easysdi_community_address_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `#__easysdi_community_partner` (`partner_id`) ON DELETE CASCADE ON UPDATE CASCADE,
				  ADD CONSTRAINT `#__easysdi_community_address_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `#__easysdi_community_address_type` (`type_id`) ON UPDATE CASCADE,
				  ADD CONSTRAINT `#__easysdi_community_address_ibfk_3` FOREIGN KEY (`title_id`) REFERENCES `#__easysdi_community_title` (`title_id`) ON UPDATE CASCADE,
				  ADD CONSTRAINT `#__easysdi_community_address_ibfk_4` FOREIGN KEY (`country_code`) REFERENCES `#__easysdi_community_country` (`country_code`) ON UPDATE CASCADE";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__easysdi_community_actor`
				  ADD CONSTRAINT `#__easysdi_community_actor_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `#__easysdi_community_partner` (`partner_id`) ON DELETE CASCADE ON UPDATE CASCADE,
				  ADD CONSTRAINT `#__easysdi_community_actor_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `#__easysdi_community_role` (`role_id`) ON UPDATE CASCADE";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$query="ALTER TABLE `#__easysdi_community_partner`
				  ADD CONSTRAINT `#__easysdi_community_partner_ibfk_16` FOREIGN KEY (`root_id`) REFERENCES `#__easysdi_community_partner` (`partner_id`) ON UPDATE CASCADE,
				  ADD CONSTRAINT `#__easysdi_community_partner_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `#__easysdi_community_partner` (`partner_id`) ON DELETE CASCADE ON UPDATE CASCADE";
			$db->setQuery( $query);
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

			$version="0.9";
			$query="INSERT INTO #__easysdi_version (id,component,version) VALUES
					(null, 'com_easysdi_core', '0.9')";	
			$db->setQuery( $query);
			if (!$db->query())
			{
				//The table does not exists then create it
				$query="CREATE TABLE `#__easysdi_version` (
					  `component` varchar(100) NOT NULL default '',
					  `id` bigint(20) NOT NULL auto_increment,
					  `version` varchar(100) NOT NULL default '',
					  PRIMARY KEY  (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;"; 		
				$db->setQuery( $query);

				if (!$db->query())
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
				$query="INSERT INTO #__easysdi_version (id,component,version) VALUES
						(null, 'com_easyssdi_partner', '0.9')";	
				$db->setQuery( $query);
				if (!$db->query())
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
			}
		}

	}
	if ($version == "0.9")
	{
		$version="0.91";
		$query="UPDATE #__easysdi_version SET version ='0.91' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_role` SET role_name='EASYSDI_REQUEST_EXTERNAL_RIGHT' where role_code ='REQUEST_EXTERNAL'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_role` SET role_name='EASYSDI_METADATA_RIGHT' where role_code ='METADATA'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_role` SET role_name='EASYSDI_FORMULARY_RIGHT' where role_code ='FORMULARY'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_role` SET role_name='EASYSDI_REQUEST_INTERNAL_RIGHT' where role_code ='REQUEST_INTERNAL'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_role` SET role_name='EASYSDI_ACCOUNT_RIGHT' where role_code ='ACCOUNT'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_role` SET role_name='EASYSDI_MYACCOUNT_RIGHT' where role_code ='MYACCOUNT'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_role` SET role_name='EASYSDI_INTERNAL_RIGHT' where role_code ='INTERNAL'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_role` SET role_name='EASYSDI_TIERCE_RIGHT' where role_code ='TIERCE'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_title` SET title_name='EASYSDI_MADAM' where title_name ='Madame'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_title` SET title_name='EASYSDI_MISTER' where title_name ='Monsieur'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_title` SET title_name='EASYSDI_MISS' where title_name ='Mademoiselle'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_title` SET title_name='EASYSDI_MAITRE' where title_name ='MaEtre'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_title` SET title_name='EASYSDI_MISS_PRESIDENT' where title_name ='Madame la PrEsidente'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_title` SET title_name='EASYSDI_MISTER_PRESIDENT' where title_name ='Monsieur le PrEsident'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_title` SET title_name='EASYSDI_MISS_PROPERTY_AGENT' where title_name ='Madame la Syndic'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_title` SET title_name='EASYSDI_MISTER_PROPERTY_AGENT' where title_name ='Monsieur le Syndic'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_title` SET title_name='EASYSDI_MISS_MISTER' where title_name ='Madame, Monsieur'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_address_type` SET type_name='EASYSDI_TYPE_CONTACT' where type_name ='Contact'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_address_type` SET type_name='EASYSDI_TYPE_INVOICING' where type_name ='Facturation'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="UPDATE `#__easysdi_community_address_type` SET type_name='EASYSDI_TYPE_DELIVERY' where type_name ='Livraison'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version == "0.91")
	{
		$version="0.92";
		$query="UPDATE #__easysdi_version SET version ='0.92' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__easysdi_community_partner add column notify_new_metadata tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__easysdi_community_partner add column notify_distribution tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__easysdi_community_partner add column notify_order_ready tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version == "0.92")
	{
		$version="0.93";
		$query="UPDATE #__easysdi_version SET version ='0.93' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="CREATE TABLE `#__easysdi_location_definition` (
		  `id` bigint(20) NOT NULL auto_increment,
		  `wfs_url` varchar(4000) NOT NULL default '',
		  `location_name` varchar(4000) NOT NULL default '',
		  `area_field_name` varchar(100) NOT NULL default '',
		  `name_field_name` varchar(100) NOT NULL default '',
		  `id_field_name` varchar(100) NOT NULL default '',
		  `feature_type_name` varchar(400) NOT NULL default '',
		  `filter_field_name` varchar(100) NOT NULL default '',
		  `id_location_filter` bigint(20) NOT NULL default '0',
		  `is_localisation` tinyint(1) NOT NULL default '0',
		  `location_desc` varchar(4000) NOT NULL default '',
		  `maxfeatures` int(11) NOT NULL default '-1',
		  `searchbox` tinyint(1) NOT NULL default '0',
		   `sort` tinyint(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}


	if ($version == "0.93")
	{
		$version="0.94";
		$query="UPDATE #__easysdi_version SET version ='0.94' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_community_partner add column rebate bigint(20) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__easysdi_community_partner add column isrebate tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

	}



	if ($version == "0.94")
	{
		$version="0.95";
		$query="UPDATE #__easysdi_version SET version ='0.95' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}


		$query="ALTER TABLE #__easysdi_location_definition add column user varchar(400)";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_location_definition add column password varchar(400)";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}

	if ($version == "0.95")
	{
		$query="ALTER TABLE #__easysdi_community_partner add column partner_logo varchar(400) ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query = "insert  into #__easysdi_config (thekey, value) values('DESCRIPTION_LENGTH','150')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query = "insert  into #__easysdi_config (thekey, value) values('LOGO_WIDTH','50')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query = "insert  into #__easysdi_config (thekey, value) values('LOGO_HEIGHT','20')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query = "insert  into #__easysdi_config (thekey, value) values('PAGINATION_METADATA','20')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		//Url used for redirection after a new user made a registration request
		$query = "insert  into #__easysdi_config (thekey, value) values('WELCOME_REDIRECT_URL','index.php?option=com_content&view=article&id=46&Itemid=104')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		// New role for diffusion
		$query = "insert  into #__easysdi_community_role (publish_id, type_id, role_code, role_name, role_description) values(0,1, 'DIFFUSION', 'EASYSDI_DIFFUSION_RIGHT', 'Gestionnaire de diffusion')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$version="0.96";
		$query="UPDATE #__easysdi_version SET version ='0.96' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version == "0.96")
	{
		$query = "insert  into #__easysdi_config (thekey, value) values('FOP_URL','http://localhost:8080/fop')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$version="0.97";
		$query="UPDATE #__easysdi_version SET version ='0.97' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ( $version == "0.97")
	{

		$version="0.98";
		$query="UPDATE #__easysdi_version SET version ='0.98' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ( $version == "0.98")
	{
		//Remove FOP_URL key configuration
		$query="DELETE FROM #__easysdi_config WHERE thekey='FOP_URL'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$version="0.99";
		$query="UPDATE #__easysdi_version SET version ='0.99' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ( $version == "0.99")
	{
		//Create tables for user profile (role) managment
		$query="CREATE TABLE IF NOT EXISTS `#__easysdi_community_profile` (
					  `profile_id` bigint(20) NOT NULL auto_increment,
					  `profile_code` varchar(20) NOT NULL,
					  `profile_translation` varchar(50) NOT NULL,
					  `profile_description` varchar(100) default NULL,
					  `profile_update` datetime default NULL,
					  PRIMARY KEY  (`profile_id`),
					  UNIQUE KEY `profile_code` (`profile_code`)					  
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="CREATE TABLE IF NOT EXISTS `#__easysdi_community_partner_profile` (
					  `id` bigint(20) NOT NULL auto_increment,
					  `partner_id` bigint(20) NOT NULL,
					  `profile_id` bigint(20)  NOT NULL,
					  PRIMARY KEY  (`id`), 
					  UNIQUE `user_profile_id` (`partner_id`,`profile_id`),
					  FOREIGN KEY (partner_id) REFERENCES	#__easysdi_community_partner(partner_id) ON DELETE CASCADE ON UPDATE CASCADE,
					FOREIGN KEY (profile_id) REFERENCES	#__easysdi_community_profile(profile_id)ON DELETE CASCADE ON UPDATE CASCADE						  		  
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		//Update the value of the unique right type used in this version of easysdi
		$query="UPDATE #__easysdi_community_role_type SET type_name='EASYSDI_ROLE_TYPE_FCT' WHERE type_name='GEoportail'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$version="0.991";
		$query="UPDATE #__easysdi_version SET version ='0.991' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
  }
  if ( $version == "0.991")
	{ 
		//Update the value of the unique right type used in this version of easysdi
		$query="INSERT INTO `#__easysdi_community_role`
		(`publish_id`, `type_id`, `role_code`, `role_name`, `role_description`, `role_update`) 
		VALUES 
		('0', '1', 'CACHE', 'EASYSDI_CACHE', 'Autorise l\'utlisation du cache de tuile en écriture', null);
-- ----------------------------
-- Table structure for `#__easysdi_map_profile_role`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_profile_role`;
CREATE TABLE `#__easysdi_map_profile_role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_role` bigint(20) NOT NULL,
  `id_prof` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_role` (`id_role`),
  KEY `id_prof` (`id_prof`),
  CONSTRAINT `#__easysdi_map_profile_role_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `#__easysdi_community_role` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `#__easysdi_map_profile_role_ibfk_2` FOREIGN KEY (`id_prof`) REFERENCES `#__easysdi_community_profile` (`profile_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		$db->setQuery( $query);
		if (!$db->queryBatch())		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$version="1.0.1";
		$query="UPDATE #__easysdi_version SET version ='1.0.1' where component = 'com_easysdi_core'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	/**
	 * Menu creation
	 */
	$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'" ;
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
			
		$query =  "insert into #__components (name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
				values('Easy SDI','option=com_easysdi_core','option=com_easysdi_core','Easysdi main menu','com_easysdi_core','js/ThemeOffice/component.png','')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'"  ;
		$db->setQuery( $query);
		$id = $db->loadResult();
	}

	//Partner
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
			values($id,'Partners','','option=com_easysdi_core&task=listPartner','Partners','com_easysdi_core','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	//Configuration
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
			values($id,'Configuration','','option=com_easysdi_core&task=listConfig','Configuration','com_easysdi_core','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	//Resources
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
			values($id,'Ressources','','option=com_easysdi_core&task=listResources','Ressources','com_easysdi_core','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	//Profiles
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
			values($id,'Roles','','option=com_easysdi_core&task=listProfile','Roles','com_easysdi_core','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	$mainframe->enqueueMessage("Congratulation core components for EasySdi Core are installed and ready to be used.
								Enjoy EasySdi Core!","INFO");


}


?>