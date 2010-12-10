<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dâ€™Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

class ADMIN_config {

	/*
	function listConfig($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
/*		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
		if ( $search ) {
			$filter .= " AND (#__users.name LIKE '%$search%'";
			$filter .= " OR #__users.username LIKE '%$search%'";		
			$filter .= " OR #__easysdi_community_partner.partner_acronym LIKE '%$search%'";		
			$filter .= " OR #__easysdi_community_partner.partner_id LIKE '%$search%'";		
			$filter .= " OR #__easysdi_community_partner.partner_code LIKE '%$search%')";		
		}
	*/
/*
		
		$query = "SELECT COUNT(*) FROM #__sdi_configuration";					
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		
		$query = "SELECT *  FROM #__sdi_configuration ";
		$query .= " ORDER BY code";
		if ($use_pagination) {
		$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		}
		else{
			$db->setQuery( $query);
		}
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_config::listConfig($use_pagination, $rows, $pageNav,$option);
	}

	
	//id = 0 means new Config entry
	function editConfig( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowConfig = new config( $database );
		$rowConfig->load( $id );
		 

		HTML_config::editConfig( $rowConfig,$option );
	}


	function removeConfig( $cid, $option ) {
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listConfig" );
			exit;
		}
		foreach( $cid as $config_id )
		{
			$config = new config( $database );
			$config->load( $config_id );
		
		
			if (!$config->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listConfig" );
			}				
		}				
	}

	
	function saveConfig($option ) {
		global $mainframe;
		$database=& JFactory::getDBO(); 
		
	
		$rowConfig= new config( $database );
		if (!$rowConfig->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listConfig" );
			exit();
		}		
				
		if (!$rowConfig->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listAccount" );
			exit();
		}
	
		
	}
	*/
	function showConfig($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$coreList=array();
		$catalogList=array();
		$shopList=array();
		$proxyList=array();
		
		$result=array();
		$query = "SELECT c.* FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='CORE'";
		$db->setQuery( $query );
		$result = $db->loadObjectList();
		foreach($result as $row)
		{
			$coreList[$row->code] = $row;
		}
			
		$query = "SELECT count(*) FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='CATALOG'";
		$db->setQuery( $query );
		$catalogItem = $db->loadResult();

		if ($catalogItem>0)
		{
			$result=array();
			$query = "SELECT c.* FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='CATALOG'";
			$db->setQuery( $query );
			$result = $db->loadObjectList();
			foreach($result as $row)
			{
				$catalogList[$row->code] = $row;
			}
		}
		
		$query = "SELECT count(*) FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='SHOP'";
		$db->setQuery( $query );
		$shopItem = $db->loadResult();
		
		if ($shopItem>0)
		{
			$result=array();
			$query = "SELECT c.* FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='SHOP' order by c.ordering";
			$db->setQuery( $query );
			$result = $db->loadObjectList();
			foreach($result as $row)
			{
				$shopList[$row->code] = $row;
			}
		}
		
		$query = "SELECT count(*) FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='PROXY'";
		$db->setQuery( $query );
		$proxyItem = $db->loadResult();

		if ($proxyItem>0)
		{
			$result=array();
			$query = "SELECT c.* FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='PROXY'";
			$db->setQuery( $query );
			$result = $db->loadObjectList();
			foreach($result as $row)
			{
				$proxyList[$row->code] = $row;
			}
		}
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $db->getTableFields("#__sdi_configuration", false);
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caractères
		$fieldsLength = array();
		foreach($tableFields as $table)
		{
			foreach ($table as $field)
			{
				if (substr($field->Type, 0, strlen("varchar")) == "varchar")
				{
					$length = strpos($field->Type, ")")-strpos($field->Type, "(")-1;
					$fieldsLength[$field->Field] = substr($field->Type, strpos($field->Type, "(")+ 1, $length);
				}
			} 
		}
		
		// Liste des types d'attributs
		$attributetypelist = array();
		$query = "SELECT id as value, name as text FROM #__sdi_list_attributetype";
		$db->setQuery( $query );
		$attributetypelist = $db->loadObjectList();
		
		
		HTML_config::showConfig($option, $coreList, $catalogItem, $catalogList, $shopItem, $shopList, $proxyItem, $proxyList, $fieldsLength, $attributetypelist );
	}

	function saveShowConfig($option) {
		global $mainframe;
		$database=& JFactory::getDBO(); 
		
		// Sauvegarde des clés CORE
		$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['description_length'])."\" WHERE code = 'DESCRIPTION_LENGTH'");
		if (!$database->query()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['logo_width'])."\" WHERE code = 'LOGO_WIDTH'");
		if (!$database->query()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['logo_height'])."\" WHERE code = 'LOGO_HEIGHT'");
		if (!$database->query()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['welcome_redirect_url'])."\" WHERE code = 'WELCOME_REDIRECT_URL'");
		if (!$database->query()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		
		// Sauvegarde des clés CATALOG
		if ($_POST['catalog_item'] > 0)
		{
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_url'])."\" WHERE code = 'CATALOG_URL'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['metadata_collapse'])."\" WHERE code = 'METADATA_COLLAPSE'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_encoding_code'])."\" WHERE code = 'CATALOG_ENCODING_CODE'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_encoding_val'])."\" WHERE code = 'CATALOG_ENCODING_VAL'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_boundary_isocode'])."\" WHERE code = 'CATALOG_BOUNDARY_ISOCODE'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_boundary_north'])."\" WHERE code = 'CATALOG_BOUNDARY_NORTH'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_boundary_south'])."\" WHERE code = 'CATALOG_BOUNDARY_SOUTH'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_boundary_east'])."\" WHERE code = 'CATALOG_BOUNDARY_EAST'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_boundary_west'])."\" WHERE code = 'CATALOG_BOUNDARY_WEST'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_boundary_type'])."\" WHERE code = 'CATALOG_BOUNDARY_TYPE'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery("UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['java_bridge_url'])."\" WHERE code = 'JAVA_BRIDGE_URL'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery("UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_search_multilist_length'])."\" WHERE code = 'CATALOG_SEARCH_MULTILIST_LENGTH'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery("UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_metadata_qtipdelay'])."\" WHERE code = 'CATALOG_METADATA_QTIPDELAY'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_pagination_searchresult'])."\" WHERE code = 'CATALOG_PAGINATION_SEARCHRESULT'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_search_ogcfilterfileid'])."\" WHERE code = 'CATALOG_SEARCH_OGCFILTERFILEID'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}
		
		// Sauvegarde des clés SHOP
		if ($_POST['shop_item'] > 0)
		{
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['proxyhost'])."\" WHERE code = 'SHOP_CONFIGURATION_PROXYHOST'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['archive_delay'])."\" WHERE code = 'SHOP_CONFIGURATION_ARCHIVE_DELAY'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['history_delay'])."\" WHERE code = 'SHOP_CONFIGURATION_HISTORY_DELAY'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['caddy_description_length'])."\" WHERE code = 'SHOP_CONFIGURATION_CADDY_DESC_LENGTH'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['mod_perim_area_precision'])."\" WHERE code = 'SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['mod_perim_metertokilometerlimit'])."\" WHERE code = 'SHOP_CONFIGURATION_MOD_PERIM_METERTOKILOMETERLIMIT'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['shop_article_step4'])."\" WHERE code = 'SHOP_CONFIGURATION_ARTICLE_STEP4'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['shop_article_step5'])."\" WHERE code = 'SHOP_CONFIGURATION_ARTICLE_STEP5'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['shop_article_terms_of_use'])."\" WHERE code = 'SHOP_CONFIGURATION_ARTICLE_TERMS_OF_USE'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}
		
		// Sauvegarde des clés PROXY
		if ($_POST['proxy_item'] > 0)
		{
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['proxy_config'])."\" WHERE code = 'PROXY_CONFIG'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}
	}

}

?>
