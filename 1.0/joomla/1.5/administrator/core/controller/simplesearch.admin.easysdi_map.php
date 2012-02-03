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

class ADMIN_simplesearch 
{
	/**
	 * Simple searches types 
	*/
	function listSimpleSearch ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		
		$query ="SELECT COUNT(*) FROM #__easysdi_map_simple_search_type";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__easysdi_map_simple_search_type ";
		$query .= " ORDER BY title";
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
		
		HTML_simplesearch::listSimpleSearch($use_pagination, $rows, $pageNav, $option);
	}
	
	function editSimpleSearch ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$simpleSearch = new simpleSearch ($db);
		$simpleSearch->load($id);
		
		//Get availaible additional filters
		$db->setQuery( "SELECT id as value, attribute as text FROM #__easysdi_map_simple_search_additional_filter" );
		$rowsFilters = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		//Get current additional filters
		$db->setQuery( "SELECT id as value FROM #__easysdi_map_simple_search_additional_filter WHERE id IN (SELECT id_saf FROM #__easysdi_map_sst_saf WHERE id_sst=$id)" );
		$rowsSelectedFilter = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		//Get availaible extra results grids
		$db->setQuery( "SELECT id as value, title as text FROM #__easysdi_map_extra_result_grid" );
		$rowsResultGrid = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		//Get current extra results grids
		$db->setQuery( "SELECT id as value FROM #__easysdi_map_extra_result_grid WHERE id IN (SELECT id_erg FROM #__easysdi_map_sst_erg WHERE id_sst=$id)" );
		$rowsSelectedGrid = $db->loadObjectList();
		echo $db->getErrorMsg();

		HTML_simplesearch::editSimpleSearch($simpleSearch, $rowsFilters,$rowsSelectedFilter,$rowsResultGrid,$rowsSelectedGrid, $option);
	}
	
	function deleteSimpleSearch($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=simpleSearch" );
			exit;
		}
		foreach( $cid as $simpleSearch_id )
		{
			$simpleSearch = new simpleSearch ($db);
			$simpleSearch->load($simpleSearch_id);
				
			if (!$simpleSearch->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=simpleSearch" );
			}				
		}	
	}
	
	function saveSimpleSearch($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$simpleSearch =& new simpleSearch($db);
		if (!$simpleSearch->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=simpleSearch" );
			exit();
		}		
				
		if (!$simpleSearch->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=simpleSearch" );
			exit();
		}
		
		/**
		 * Save selected additional filters 
		*/
		$db->setQuery( "DELETE FROM #__easysdi_map_sst_saf WHERE id_sst=".$simpleSearch->id);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=simpleSearch" );
			exit();
		}
		if(isset($_POST['filter_id']))
		{
			if (count ($_POST['filter_id'] )>0)
			{
				foreach( $_POST['filter_id'] as $filter_id )
				{
					$db->setQuery( "INSERT INTO #__easysdi_map_sst_saf (id_sst, id_saf) VALUES (".$simpleSearch->id.",".$filter_id.")" );
					if (!$db->query()) 
					{
						$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
						$mainframe->redirect("index.php?option=$option&task=simpleSearch" );
						exit();
					}
				}
			}
		}
		
		/**
		 * Save selected grids results
		*/
		$db->setQuery( "DELETE FROM #__easysdi_map_sst_erg WHERE id_sst=".$simpleSearch->id);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=simpleSearch" );
			exit();
		}
		if(isset($_POST['grid_id']))
		{
			if (count ($_POST['grid_id'] )>0)
			{
				foreach( $_POST['grid_id'] as $grid_id )
				{
					$db->setQuery( "INSERT INTO #__easysdi_map_sst_erg (id_sst, id_erg) VALUES (".$simpleSearch->id.",".$grid_id.")" );
					if (!$db->query()) 
					{
						$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
						$mainframe->redirect("index.php?option=$option&task=simpleSearch" );
						exit();
					}
				}
			}
		}
	}
	
	/** 
	 * Additionnal filter
	 */
	function listAdditionalFilter ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		
		$query ="SELECT COUNT(*) FROM #__easysdi_map_simple_search_additional_filter";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__easysdi_map_simple_search_additional_filter ";
		$query .= " ORDER BY attribute";
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
		
		HTML_simplesearch::listAdditionalFilter($use_pagination, $rows, $pageNav, $option);
	}
	
	function editAdditionalFilter ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$additionalFilter = new additionalFilter ($db);
		$additionalFilter->load($id);

		HTML_simplesearch::editAdditionalFilter($additionalFilter, $option);
	}
	
	function deleteAdditionalFilter($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=additionalFilter" );
			exit;
		}
		foreach( $cid as $additionalFilter_id )
		{
			$additionalFilter = new additionalFilter ($db);
			$additionalFilter->load($additionalFilter_id);
				
			if (!$additionalFilter->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=additionalFilter" );
			}				
		}	
	}
	
	function saveAdditionalFilter($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$additionalFilter =& new additionalFilter($db);
		if (!$additionalFilter->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=additionalFilter" );
			exit();
		}		
				
		if (!$additionalFilter->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=additionalFilter" );
			exit();
		}
	}
}
?>