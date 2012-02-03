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
jimport("joomla.html.pagination");
jimport("joomla.html.pane");
jimport("joomla.database.table");
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'component.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'content.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'plugin.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'menu.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'section.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');

JHTML::_('stylesheet', 'common_easysdi_admin.css', 'administrator/components/com_easysdi_core/templates/css/');

$task = JRequest::getVar('task');
$cid = JRequest::getVar ('cid', array(0) );
$JId = JRequest::getVar('JId', '');
if (!is_array( $cid )) {
	$cid = array(0);
}

global $mainframe;
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'sditable.easysdi.class.php');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mailing.easysdi.class.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mailing.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mailing.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mailing.admin.easysdi.php');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'common'.DS.'easysdi.usertree.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'sditable.easysdi.class.php');

// Imports pour la partie SHOP
/*require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'metadata.admin.easysdi.html.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'metadata.admin.easysdi.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'metadata.easysdi.class.php');
*/

$option = JRequest::getVar('option');

switch($task){
/*
	case "cancelConfig":
	case "listConfig":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'config.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'config.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'config.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'config.admin.easysdi.php');
		
		TOOLBAR_config::_DEFAULT();
		ADMIN_config::listConfig( $option );
		break;
	case "editConfig":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'config.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'config.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'config.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'config.admin.easysdi.php');
		
		TOOLBAR_config::_EDIT();
		ADMIN_config::editConfig($cid[0], $option );
		break;
	case "newConfig":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'config.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'config.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'config.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'config.admin.easysdi.php');
		
		TOOLBAR_config::_EDIT();
		ADMIN_config::editConfig($cid[0], $option );
		break;
	case "saveConfig":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'config.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'config.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'config.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'config.admin.easysdi.php');
		
		ADMIN_config::saveConfig($option );
		TOOLBAR_config::_DEFAULT();
		ADMIN_config::listConfig( $option );
		break;
	case "deleteConfig":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'config.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'config.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'config.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'config.admin.easysdi.php');
		
		ADMIN_config::removeConfig($cid,$option );
		TOOLBAR_config::_DEFAULT();
		ADMIN_config::listConfig( $option );
		break;
		*/
	
	/**
		 * System account
		 */
	case 'systemAccount':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'systemaccount.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'systemaccount.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'systemaccount.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'systemaccount.admin.easysdi.php');
		TOOLBAR_systemaccount::_EDIT();
		$id = JRequest::getVar ('account_id', '' );
		$code = JRequest::getVar ('code', 'guest' );
		ADMIN_systemaccount::editSystemAccount($id,$option,$code);
		break;
	case 'saveSystemAccount':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'systemaccount.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'systemaccount.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'systemaccount.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'systemaccount.admin.easysdi.php');
		ADMIN_systemaccount::saveSystemAccount($option);
		$mainframe->redirect("index.php?option=$option");
		break;
		
	case "exportAccount":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'account.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'account.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'account.admin.easysdi.php');
		
		ADMIN_account::exportAccount( $cid, $option );
		break;
	
	case "cancelAccount":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'account.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'account.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'account.admin.easysdi.php');
		
		ADMIN_account::cancelAccount( false, $option );
		TOOLBAR_account::_DEFAULT();
		ADMIN_account::listAccount( $option );
		
		break;	
	
	case "editAffiliateAccount":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'account.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'account.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'account.admin.easysdi.php');
		
		TOOLBAR_account::_EDIT();
		include (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'core.admin.easysdi.php');
		ADMIN_account::editAffiliateAccount( $cid[0], $option );
		break;
	
	case "newAffiliateAccount":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'account.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'account.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'account.admin.easysdi.php');
		
		include (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'core.admin.easysdi.php');
		TOOLBAR_account::_EDIT();
		ADMIN_account::editAffiliateAccount( 0, $option );
		break;
	
	case "removeAccount":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'account.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'account.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'account.admin.easysdi.php');
		
		ADMIN_account::removeAccount( $cid, $option );
		break;
	
	case "editRootAccount":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'account.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'account.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'account.admin.easysdi.php');
		
		include (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'core.admin.easysdi.php');
		TOOLBAR_account::_EDIT();
		ADMIN_account::editRootAccount( $cid[0], $option);
		break;
	
	case "saveAccount":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'account.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'account.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'account.admin.easysdi.php');
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'helper.php');
		
		ADMIN_account::saveAccount( true, $option );
		//TOOLBAR_account::_DEFAULT();
		//ADMIN_account::listAccount( $option );
		break;
	
	case "newRootAccount":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'account.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'account.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'account.admin.easysdi.php');
		
		include (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'core.admin.easysdi.php');
		TOOLBAR_account::_EDIT();
		ADMIN_account::editRootAccount( 0, $option );
		break;
	
	case "listAccount":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'account.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'account.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'account.admin.easysdi.php');
		
		TOOLBAR_account::_DEFAULT();
		ADMIN_account::listAccount( $option );
		break;
		
	case "listResources":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'resources.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'resources.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resources.admin.easysdi.php');

		TOOLBAR_resources::_DEFAULT();
		ADMIN_resources::listResources( $option );
		break;
		
	case "editResource":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'resources.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'resources.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resources.admin.easysdi.php');

		TOOLBAR_resources::_EDIT();
		ADMIN_resources::editResource($option);
		break;
		
	case "saveResource":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'resources.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'resources.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resources.admin.easysdi.php');

		ADMIN_resources::saveResource($option);
		TOOLBAR_resources::_DEFAULT();
		ADMIN_resources::listResources($option);
		break;
		
	case "cancelResource":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'resources.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'resources.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resources.admin.easysdi.php');

		TOOLBAR_resources::_DEFAULT();
		ADMIN_resources::listResources( $option );
		break;

	case "listAccountProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'accountprofile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'accountprofile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'accountprofile.admin.easysdi.php');

		TOOLBAR_accountProfile::_DEFAULT();
		ADMIN_accountProfile::listAccountProfile( $option );
		break;
	case "newAccountProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'accountprofile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'accountprofile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'accountprofile.admin.easysdi.php');

		TOOLBAR_accountProfile::_EDIT();
		ADMIN_accountProfile::editAccountProfile(0, $option );
		break;
	case "editAccountProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'accountprofile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'accountprofile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'accountprofile.admin.easysdi.php');

		TOOLBAR_accountProfile::_EDIT();
		ADMIN_accountProfile::editAccountProfile($cid[0], $option );
		break;
	case "saveAccountProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'accountprofile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'accountprofile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'accountprofile.admin.easysdi.php');

		ADMIN_accountProfile::saveAccountProfile( $option );
		TOOLBAR_accountProfile::_DEFAULT();
		ADMIN_accountProfile::listAccountProfile( $option );
		break;
	case "applyAccountProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'accountprofile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'accountprofile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'accountprofile.admin.easysdi.php');

		ADMIN_accountProfile::saveAccountProfile( $option );
		TOOLBAR_accountProfile::_EDIT();
		ADMIN_accountProfile::editAccountProfile($cid[0], $option );
		break;
	case "cancelAccountProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'accountprofile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'accountprofile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'accountprofile.admin.easysdi.php');

		TOOLBAR_accountProfile::_DEFAULT();
		ADMIN_accountProfile::listAccountProfile( $option );
		break;
	case "removeAccountProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'accountprofile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'accountprofile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'accountprofile.admin.easysdi.php');

		ADMIN_accountProfile::removeAccountProfile($cid,  $option );
		TOOLBAR_accountProfile::_DEFAULT();
		ADMIN_accountProfile::listAccountProfile( $option );
		break;
	case "saveOrderAccountProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'accountprofile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'accountprofile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'accountprofile.admin.easysdi.php');

		ADMIN_accountProfile::saveOrder($option);
		break;
	case "orderupAccountProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'accountprofile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'accountprofile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'accountprofile.admin.easysdi.php');

		ADMIN_accountProfile::orderContent(-1, $option);
		break;
	case "orderdownAccountProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'accountprofile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'accountprofile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'accountprofile.admin.easysdi.php');

		ADMIN_accountProfile::orderContent(1, $option);
		break;
				
	case "listLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');

		TOOLBAR_language::_DEFAULT();
		ADMIN_language::listLanguage( $option );
		break;
	
	case "newLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');

		TOOLBAR_language::_EDIT();
		ADMIN_language::editLanguage(0, $option);
		break;

	case "removeLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');

		ADMIN_language::removeLanguage($cid, $option);
		TOOLBAR_language::_DEFAULT();
		ADMIN_language::listLanguage($option);
		break;
		
	case "editLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');

		TOOLBAR_language::_EDIT();
		ADMIN_language::editLanguage($cid[0], $option);
		break;
		
	case "saveLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');

		ADMIN_language::saveLanguage($option);
		TOOLBAR_language::_DEFAULT();
		ADMIN_language::listLanguage($option);
		break;

	case "applyLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');

		ADMIN_language::saveLanguage($option);
		//TOOLBAR_language::_EDIT();
		//ADMIN_language::editLanguage($cid[0], $option);
		break;
	
	case "cancelLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');

		TOOLBAR_language::_DEFAULT();
		ADMIN_language::listLanguage( $option );
		break;
	case "saveOrderLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');
		ADMIN_language::saveOrder($option);
		break;
	case "orderupLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');
		ADMIN_language::orderContent(-1, $option);
		break;
	case "orderdownLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');
		ADMIN_language::orderContent(1, $option);
		break;
	case "language_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');
		ADMIN_language::changeContent(1);
		break;
	case "language_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');
		ADMIN_language::changeContent(0);
		break;

	case "setDefaultLanguage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'language.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'language.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'language.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'language.admin.easysdi.php');
		ADMIN_language::setDefault($cid[0], $option);
		break;
		
	case 'cpanel':
		$mainframe->redirect("index.php?option=com_easysdi_core" );
		break;
		
	
		
	case "editMetadata":
		// Imports pour la partie CATALOG
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		//TOOLBAR_metadata::_EDIT();		
		//ADMIN_metadata::editMetadata($_POST['metadata_id'],$option);
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=editMetadata&cid[0]=".$cid[0] );
		break;
	
	case "ctrlPanelAccountManager":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'ctrlpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'account.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'account.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'account.admin.easysdi.html.php');
		
		
		TOOLBAR_account::_DEFAULT();
		ADMIN_account::listAccount( $option );
		break;
		
	// Appels vers le proxy
	case 'showConfigList':
		$mainframe->redirect("index.php?option=com_easysdi_proxy&task=showConfigList" );
		break;
		
	// Appels vers le shop	
	case "listMetadataStandardClasses":
		// Imports pour la partie CATALOG
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		TOOLBAR_metadata::_LISTSTANDARDCLASSES();;
		ADMIN_metadata::listStandardClasses($option);
		break;
	
	case "listMetadataStandard":
		// Imports pour la partie CATALOG
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		TOOLBAR_metadata::_LISTSTANDARD();;
		ADMIN_metadata::listStandard($option);
		break;
	
	case "listMetadataLocfreetext":
		// Imports pour la partie CATALOG
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		TOOLBAR_metadata::_LISTLOCFREETEXT();
		ADMIN_metadata::listLocfreetext($option);
		break;
	
	case "listMetadataClass":
		// Imports pour la partie CATALOG
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		TOOLBAR_metadata::_LISTClass();
		ADMIN_metadata::listClass($option);
		break;
	
	case "listMetadataFreetext":
		// Imports pour la partie CATALOG
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		TOOLBAR_metadata::_LISTFREETEXT();
		ADMIN_metadata::listFreetext($option);
		break;
	
	case "listMetadataFreetext":
		// Imports pour la partie CATALOG
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		TOOLBAR_metadata::_LISTFREETEXT();
		ADMIN_metadata::listFreetext($option);
		break;
	
	case "listMetadataExt":
		// Imports pour la partie CATALOG
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		TOOLBAR_metadata::_LISTEXT();
		ADMIN_metadata::listExt($option);
		break;
	
	case "listMetadataTabs":
		// Imports pour la partie CATALOG
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		TOOLBAR_metadata::_LISTMDTABS();
		ADMIN_metadata::listMetadataTabs($option);
		break;

	case "ctrlPanelConfig":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'config.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'config.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'config.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'config.admin.easysdi.php');
		
		TOOLBAR_config::_DEFAULT();
		ADMIN_config::showConfig($option);
		break;

	case "saveShowConfig":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'config.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'config.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'config.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'config.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'ctrlpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'module.panel.easysdi.class.php');

		ADMIN_config::saveShowConfig($option);
		ADMIN_ctrlpanel::ctrlPanelCore($option);
		break;
		
	case "applyShowConfig":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'config.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'config.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'config.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'config.admin.easysdi.php');
		
		ADMIN_config::saveShowConfig($option);
		TOOLBAR_config::_DEFAULT();
		ADMIN_config::showConfig($option);
		break;
		
	case "cancelConfig":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'ctrlpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'module.panel.easysdi.class.php');

		ADMIN_ctrlpanel::ctrlPanelCore($option);
		break;
	
	case "listPackage":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listPackage" );
		break;
		
	case "listModel":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listModel" );
		break;
		
	case "listProfile":
		// Imports pour la partie CATALOG
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
				
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listProfile" );
		break;
	
	case "listObject":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listObject" );
		break;
		
	case "listObjectType":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listObjectType" );
		break;
		
	case "listAttributeType":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listAttributeType" );
		break;

	case "listBoundary":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listBoundary" );
		break;
	
	case "listMDNamespace":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listMDNamespace" );
		break;
	
	case "listImportRef":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listImportRef" );
		break;
	
	case "listClass":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listClass" );
		break;
	
	case "listAttribute":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listAttribute" );
		break;
		
	case "listList":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listList" );
		break;
		
	case "listRelation":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listRelation" );
		break;
		
	case "listQueryReports":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listQueryReports" );
		break;
		
	case "listObjectTypeLink":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listObjectTypeLink" );
		break;
		
	case "listContext":
		$mainframe->redirect("index.php?option=com_easysdi_catalog&task=listContext" );
		break;
		
	default:
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'ctrlpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'module.panel.easysdi.class.php');

		ADMIN_ctrlpanel::ctrlPanelCore($option);	
		break;
		
}

?>