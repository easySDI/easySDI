<?php

/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�??Arche 40b, CH-1870 Monthey, easysdi@depth.ch
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
 *
 * string genericlist (array $arr, string $name,
 * [string $attribs = null], [string $key = 'value'],
 * [string $text = 'text'], [mixed $selected = NULL],
 * [ $idtag = false], [ $translate = false])
 */

defined('_JEXEC') or die('Restricted access');

class SERVICE_boundary {
	
	function getBoundariesByCategoriesAlias ($category_alias = null, $exclude = null){
		$db =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		
		$boudaries = array();
		
		$query = "SELECT b.id as id, 
						 Concat (t.label,' [',tbc.label,']') as label, 
						 b.northbound as northbound ,
						 b.southbound as southbound, 
						 b.eastbound as eastbound ,
						 b.westbound as westbound
		FROM #__sdi_boundary b
		INNER JOIN #__sdi_boundarycategory bc ON b.category_id = bc.id
		INNER JOIN #__sdi_translation tbc ON bc.guid = tbc.element_guid
		INNER JOIN #__sdi_language lbc ON tbc.language_id=lbc.id
		INNER JOIN #__sdi_list_codelang cbc ON lbc.codelang_id=cbc.id
		INNER JOIN #__sdi_translation t ON b.guid = t.element_guid
		INNER JOIN #__sdi_language l ON t.language_id=l.id
		INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id
		WHERE c.code='".$language->_lang."'
		AND cbc.code='".$language->_lang."' 
		";
		
		if(isset($category_alias) && strlen($category_alias)> 0){
			$query .= " AND bc.alias = '".$category_alias."'";
		}
		
		if(isset($exclude)&& strlen($exclude)> 0){
			$query .= " AND b.id NOT IN ( ".$exclude.")";
		}
		$query .= " ORDER BY label";
		$db->setQuery( $query );
		$boudaries =  $db->loadObjectList();
		echo json_encode($boudaries);
		die();
	}
	
	function getBoundariesByCategoriesId ($categories_id = null){
		if(!isset($categories_id) || $categories_id == null || strlen($categories_id)== 0){
			echo '[]';
			die();
		}
		
		$db =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
	
		$boudaries = array();
	
		$query = "SELECT b.guid as value,
		b.name as text
		FROM #__sdi_boundary b
		INNER JOIN #__sdi_boundarycategory bc ON b.category_id = bc.id
		INNER JOIN #__sdi_translation tbc ON bc.guid = tbc.element_guid
		INNER JOIN #__sdi_language lbc ON tbc.language_id=lbc.id
		INNER JOIN #__sdi_list_codelang cbc ON lbc.codelang_id=cbc.id
		INNER JOIN #__sdi_translation t ON b.guid = t.element_guid
		INNER JOIN #__sdi_language l ON t.language_id=l.id
		INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id
		WHERE c.code='".$language->_lang."'
		AND cbc.code='".$language->_lang."' ";
	
		if(isset($categories_id) && strlen($categories_id)> 0){
			$query .= " AND bc.id IN (".$categories_id.")";
		}
	
		$query .=" ORDER by b.name";
		$db->setQuery( $query );
		$boudaries =  $db->loadObjectList();
		echo json_encode($boudaries);
		die();
	}
	
	function getBoundariesByLabel ($boundary, $categories_id = null ){
		if(!isset($categories_id) || $categories_id == null || strlen($categories_id)== 0){
			echo '[]';
			die();
		}
		
		$db =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
	
		$boudaries = array();
	
		$query = "SELECT b.guid as value,
		Concat (t.label,' [',tbc.label,']') as text
		FROM #__sdi_boundary b
		INNER JOIN #__sdi_boundarycategory bc ON b.category_id = bc.id
		INNER JOIN #__sdi_translation tbc ON bc.guid = tbc.element_guid
		INNER JOIN #__sdi_language lbc ON tbc.language_id=lbc.id
		INNER JOIN #__sdi_list_codelang cbc ON lbc.codelang_id=cbc.id
		INNER JOIN #__sdi_translation t ON b.guid = t.element_guid
		INNER JOIN #__sdi_language l ON t.language_id=l.id
		INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id
		WHERE c.code='".$language->_lang."'
		AND cbc.code='".$language->_lang."' 
		AND t.label LIKE '%".$boundary."%' ";
		
		if(isset($categories_id) && strlen($categories_id)> 0){
			$query .= " AND bc.id IN (".$categories_id.")";
		}
		
		$query .=" ORDER by t.label";
		$db->setQuery( $query );
		
		$boudaries =  $db->loadObjectList();
		echo json_encode($boudaries);
		die();
	}
}
?>