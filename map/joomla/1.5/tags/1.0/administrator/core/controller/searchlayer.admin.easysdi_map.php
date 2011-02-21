<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
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

class ADMIN_searchlayer 
{
	function listSearchLayer ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		
		$query ="SELECT COUNT(*) FROM #__easysdi_map_search_layer";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT #__easysdi_map_search_layer.*, #__easysdi_map_feature_type.name  FROM #__easysdi_map_search_layer INNER JOIN #__easysdi_map_feature_type ON #__easysdi_map_search_layer.feature_type = #__easysdi_map_feature_type.id ";
		$query .= " ORDER BY feature_type";
		if ($use_pagination) 
		{
			$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		}
		else
		{
			$db->setQuery( $query);
		}
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		HTML_searchlayer::listSearchLayer($use_pagination, $rows, $pageNav, $option);
	}
	
	function editSearchLayer ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$search_layer = new search_layer ($db);
		$search_layer->load($id);
		
		//Get available feature type 
		$db->setQuery( "SELECT id as value, name as text FROM #__easysdi_map_feature_type WHERE id IN (SELECT id_ft FROM #__easysdi_map_feature_type_use WHERE id_use IN (SELECT id from #__easysdi_map_use WHERE name ='searchLayer' ))" );
		$rowsSearchLayerFT = $db->loadObjectList();
		echo $db->getErrorMsg();
		$db->setQuery( "SELECT id as value, name as text FROM #__easysdi_map_feature_type WHERE id IN (SELECT id_ft FROM #__easysdi_map_feature_type_use WHERE id_use IN (SELECT id from #__easysdi_map_use WHERE name ='rowDetails' ))" );
		$rowsDetailsFT = $db->loadObjectList();
		echo $db->getErrorMsg();

		HTML_searchlayer::editSearchLayer($search_layer, $rowsSearchLayerFT, $rowsDetailsFT, $option);
	}
	
	function deleteSearchLayer($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=searchlayer" );
			exit;
		}
		foreach( $cid as $search_layer_id )
		{
			$search_layer = new search_layer ($db);
			$search_layer->load($search_layer_id);
				
			if (!$search_layer->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=searchlayer" );
			}				
		}	
	}
	
	function saveSearchLayer($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$search_layer = new search_layer ($db);
		if (!$search_layer->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=searchlayer" );
			exit();
		}		
		
		if($search_layer->enable == 1)
		{
			/** Disable all other search layers*/
			$db->setQuery( "UPDATE #__easysdi_map_search_layer SET enable='0'");
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=searchlayer" );				
			}
		}
		
		if (!$search_layer->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=searchlayer" );
			exit();
		}
	}

}
?>