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
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchSearchLayer{$option}", 'searchSearchLayer', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_searchLayer ";
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT #__sdi_searchLayer.*, #__sdi_featuretype.name as featuretypeName  FROM #__sdi_searchLayer INNER JOIN #__sdi_featuretype ON #__sdi_searchLayer.featuretype = #__sdi_featuretype.id ";
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "name" && $filter_order <> "enable" && $filter_order <> "description" )
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		$query .= $orderby;
				
		$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		HTML_searchlayer::listSearchLayer( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option);
	}
	
	function editSearchLayer ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$search_layer = new searchLayer ($db);
		$search_layer->load($id);
		
		$search_layer->tryCheckOut($option,'geolocation');
		
		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		if ($search_layer->created)
		{ 
			if ($search_layer->createdby and $search_layer->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$search_layer->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($search_layer->updated and $search_layer->updated<> '0000-00-00 00:00:00')
		{ 
			if ($search_layer->updatedby and $search_layer->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$search_layer->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		//Get available feature type 
		$db->setQuery( "SELECT id as value, name as text FROM #__sdi_featuretype WHERE id IN (SELECT ft_id FROM #__sdi_featuretype_usage WHERE usage_id IN (SELECT id from #__sdi_usage WHERE name ='searchLayer' ))" );
		$rowsSearchLayerFT = $db->loadObjectList();
		echo $db->getErrorMsg();
		$db->setQuery( "SELECT id as value, name as text FROM #__sdi_featuretype WHERE id IN (SELECT ft_id FROM #__sdi_featuretype_usage WHERE usage_id IN (SELECT id from #__sdi_usage WHERE name ='rowDetails' ))" );
		$rowsDetailsFT = $db->loadObjectList();
		echo $db->getErrorMsg();

		HTML_searchlayer::editSearchLayer($search_layer, $rowsSearchLayerFT, $rowsDetailsFT,$createUser, $updateUser, $search_layer->getFieldsLength(),$option);
	}
	
	function deleteSearchLayer($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("MAP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=searchlayer" );
			exit;
		}
		foreach( $cid as $search_layer_id )
		{
			$searchLayer = new searchLayer ($db);
			$searchLayer->load($search_layer_id);
				
			if (!$searchLayer->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=searchlayer" );
			}				
		}	
		$mainframe->redirect("index.php?option=$option&task=searchLayer");
	}
	
	function saveSearchLayer($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$searchLayer = new searchLayer ($db);
		if (!$searchLayer->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=searchlayer" );
			exit();
		}		
		
		if($searchLayer->enable == 1)
		{
			/** Disable all other search layers*/
			$db->setQuery( "UPDATE #__sdi_searchLayer SET enable='0'");
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=searchlayer" );				
			}
		}
		
		if (!$searchLayer->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=searchlayer" );
			exit();
		}
		
		$searchLayer->checkin();
		$mainframe->redirect("index.php?option=$option&task=searchLayer");
	}
	
	function cancelSearchLayer($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$searchLayer = new searchLayer ($db);
		$searchLayer->bind(JRequest::get('post'));
		$searchLayer->checkin();

		$mainframe->redirect("index.php?option=$option&task=searchLayer" );
	}
}
?>