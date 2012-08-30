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

class ADMIN_config {

	function showConfig($option){
		global  $mainframe;
		$db 		=& JFactory::getDBO(); 
		$coreList	=array();
		$catalogList=array();
		$shopList	=array();
		$proxyList	=array();
		$monitorList=array();
		$publishList=array();
		$mapList	=array();
		
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
		
		$query = "SELECT count(*) FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='MONITOR'";
		$db->setQuery( $query );
		$monitorItem = $db->loadResult();

		if ($monitorItem>0)
		{
			$result=array();
			$query = "SELECT c.* FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='MONITOR'";
			$db->setQuery( $query );
			$result = $db->loadObjectList();
			foreach($result as $row)
			{
				$monitorList[$row->code] = $row;
			}
		}
		
		$query = "SELECT count(*) FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='PUBLISH'";
		$db->setQuery( $query );
		$publishItem = $db->loadResult();

		if ($publishItem>0)
		{
			$result=array();
			$query = "SELECT c.* FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='PUBLISH'";
			$db->setQuery( $query );
			$result = $db->loadObjectList();
			foreach($result as $row)
			{
				$publishList[$row->code] = $row;
			}
		}
		
		$query = "SELECT count(*) FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='MAP'";
		$db->setQuery( $query );
		$mapItem = $db->loadResult();

		if ($mapItem>0)
		{
			$result=array();
			$query = "SELECT c.* FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='MAP'";
			$db->setQuery( $query );
			$result = $db->loadObjectList();
			foreach($result as $row)
			{
				$mapList[$row->code] = $row;
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
		$query = "SELECT id as value, alias as text FROM #__sdi_sys_stereotype";
		$db->setQuery( $query );
		$attributetypelist = $db->loadObjectList();
		
		//List des relation pouvant être utilisées en tant que titre de la métadonnée
		$relationtitlelist = array();
		$query = "SELECT id as value, name as text FROM #__sdi_relation WHERE rendertype_id IN (1,2,3,4,5,8) AND published = 1 ORDER BY name";
		$db->setQuery( $query );
		$relationtitlelist = $db->loadObjectList();
		
		$query = "SELECT id FROM #__sdi_relation WHERE istitle = 1 LIMIT 0,1 ";
		$db->setQuery( $query );
		$relationtitle = $db->loadResult();
		
		//List of context
		$metadatapreviewcontextlist = array();
		$query= "SELECT '' AS value, '-' AS text UNION SELECT code as value, name as text FROM #__sdi_context ORDER BY text";
		$db->setQuery( $query );
		$metadatapreviewcontextlist = $db->loadObjectList();
				
		//List of type
		$metadatapreviewtypelist = array(	array(value => '', 			text => '-'),
											array(value => 'abstract', 	text => 'abstract'), 
											array(value => 'complete', 	text => 'complete'), 
											array(value => 'specific', 	text => 'specific'),
											array(value => 'diffusion', text => 'diffusion'));
		
		HTML_config::showConfig($option, $coreList, $catalogItem, $catalogList, $shopItem, $shopList, $proxyItem, $proxyList,  $monitorItem, $monitorList,$publishItem, $publishList,$mapItem, $mapList, $fieldsLength, $attributetypelist, $relationtitlelist,$relationtitle, $metadatapreviewtypelist, $metadatapreviewcontextlist );
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
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_mxqueryurl'])."\" WHERE code = 'CATALOG_MXQUERYURL'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['catalog_mxquerypagination'])."\" WHERE code = 'CATALOG_MXQUERYPAGINATION'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['defaultBboxConfig']))."\" WHERE code = 'defaultBboxConfig'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['defaultBboxConfigExtentLeft']))."\" WHERE code = 'defaultBboxConfigExtentLeft'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['defaultBboxConfigExtentBottom']))."\" WHERE code = 'defaultBboxConfigExtentBottom'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['defaultBboxConfigExtentRight']))."\" WHERE code = 'defaultBboxConfigExtentRight'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['defaultBboxConfigExtentTop']))."\" WHERE code = 'defaultBboxConfigExtentTop'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['mapMinResolution']))."\" WHERE code = 'mapMinResolution'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['mapMaxResolution']))."\" WHERE code = 'mapMaxResolution'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['thesaurusUrl']))."\" WHERE code = 'thesaurusUrl'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['metadata_linked_file_repository']))."\" WHERE code = 'CATALOG_METADATA_LINKED_FILE_REPOSITORY'");
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['metadata_linked_file_base_uri']))."\" WHERE code = 'CATALOG_METADATA_LINKED_FILE_BASE_URI'");
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_relation SET istitle=0 WHERE istitle = 1");
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_relation SET istitle=1 WHERE id = ".addslashes(trim($_POST['relationtitle'])));
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['metadatapreviewtype']))."\" WHERE code = 'CATALOG_METADATA_PREVIEW_TYPE'");
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes(trim($_POST['metadatapreviewcontext']))."\" WHERE code = 'CATALOG_METADATA_PREVIEW_CONTEXT'");
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
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['max_file_size'])."\" WHERE code = 'SHOP_CONFIGURATION_MAX_FILE_SIZE'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
		}
		
		// Sauvegarde des cl�s PROXY
		if ($_POST['proxy_item'] > 0)
		{
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['proxy_config'])."\" WHERE code = 'PROXY_CONFIG'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}
		
		// Sauvegarde des cl�s MONITOR
		if ($_POST['monitor_item'] > 0)
		{
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['monitor_url'])."\" WHERE code = 'MONITOR_URL'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}
		
		// Sauvegarde des cl�s PUBLISH
		if ($_POST['publish_item'] > 0)
		{
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['publish_url'])."\" WHERE code = 'WPS_PUBLISHER'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}
		
		// Sauvegarde des cl�s MAP
		if ($_POST['map_item'] > 0)
		{
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['componentPath'])."\" WHERE code = 'componentPath'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['componentUrl'])."\" WHERE code = 'componentUrl'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['projection'])."\" WHERE code = 'projection'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['pubWfsUrl'])."\" WHERE code = 'pubWfsUrl'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['pubWfsVersion'])."\" WHERE code = 'pubWfsVersion'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['pubWmsVersion'])."\" WHERE code = 'pubWmsVersion'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['maxFeatures'])."\" WHERE code = 'maxFeatures'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['pubFeatureNS'])."\" WHERE code = 'pubFeatureNS'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['pubFeaturePrefix'])."\" WHERE code = 'pubFeaturePrefix'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['wpsReportsUrl'])."\" WHERE code = 'wpsReportsUrl'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['shp2GmlUrl'])."\" WHERE code = 'shp2GmlUrl'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['featureIdAttribute'])."\" WHERE code = 'featureIdAttribute'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['maxSearchBars'])."\" WHERE code = 'maxSearchBars'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['WMSFilterSupport'])."\" WHERE code = 'WMSFilterSupport'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['pubWmsUrl'])."\" WHERE code = 'pubWmsUrl'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['defaultCoordMapZoom'])."\" WHERE code = 'defaultCoordMapZoom'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['autocompleteNumChars'])."\" WHERE code = 'autocompleteNumChars'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['autocompleteUseFID'])."\" WHERE code = 'autocompleteUseFID'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['autocompleteMaxFeat'])."\" WHERE code = 'autocompleteMaxFeat'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['layerProxyXMLFile'])."\" WHERE code = 'layerProxyXMLFile'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['maptofopURL'])."\" WHERE code = 'maptofopURL'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['numZoomLevels'])."\" WHERE code = 'numZoomLevels'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['localisationInputWidth'])."\" WHERE code = 'localisationInputWidth'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['legendOrFilterPanelWidth'])."\" WHERE code = 'legendOrFilterPanelWidth'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['treePanelWidth'])."\" WHERE code = 'treePanelWidth'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			if($_POST['enableQueryEngine'])
			{
				$database->setQuery( "UPDATE #__sdi_configuration SET value='1' WHERE code = 'enableQueryEngine'");
				if (!$database->query()) {			
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
			}
			else {
				$database->setQuery( "UPDATE #__sdi_configuration SET value='0' WHERE code = 'enableQueryEngine'");
				if (!$database->query()) {			
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				$database->setQuery( "UPDATE #__sdi_mapdisplayoption SET enable=0 WHERE name IN('SimpleSearch','AdvancedSearch','DataPrecision')");
				$database->query();
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['mapResolutionOverScale'])."\" WHERE code = 'mapResolutionOverScale'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['mapUnit'])."\" WHERE code = 'mapUnit'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['mapMaxExtent'])."\" WHERE code = 'mapMaxExtent'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['mapMinScale'])."\" WHERE code = 'mapMinScale'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['mapMaxScale'])."\" WHERE code = 'mapMaxScale'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			$database->setQuery( "UPDATE #__sdi_configuration SET value=\"".addslashes($_POST['mapResolutions'])."\" WHERE code = 'mapResolutions'");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
		}
	}

}

?>
