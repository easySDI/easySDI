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
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchSimpleSearch{$option}", 'searchSimpleSearch', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_simplesearchtype";
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_simplesearchtype ";
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "name" && $filter_order <> "description" )
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
		
		HTML_simplesearch::listSimpleSearch($rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option);
	}
	
	function editSimpleSearch ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$simpleSearch = new simpleSearch ($db);
		$simpleSearch->load($id);
		
		$simpleSearch->tryCheckOut($option,'simpleSearch');
		
		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		if ($simpleSearch->created)
		{ 
			if ($simpleSearch->createdby and $simpleSearch->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$simpleSearch->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($simpleSearch->updated and $simpleSearch->updated<> '0000-00-00 00:00:00')
		{ 
			if ($simpleSearch->updatedby and $simpleSearch->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$simpleSearch->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		//Get availaible additional filters
		$db->setQuery( "SELECT id as value, attribute as text FROM #__sdi_simplesearchfilter" );
		$rowsFilters = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		//Get current additional filters
		$db->setQuery( "SELECT id as value FROM #__sdi_simplesearchfilter WHERE id IN (SELECT id_saf FROM #__sdi_sst_saf WHERE id_sst=$id)" );
		$rowsSelectedFilter = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		//Get availaible extra results grids
		$db->setQuery( "SELECT id as value, name as text FROM #__sdi_resultgrid" );
		$rowsResultGrid = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		//Get current extra results grids
		$db->setQuery( "SELECT id as value FROM #__sdi_resultgrid WHERE id IN (SELECT id_erg FROM #__sdi_sst_erg WHERE id_sst=$id)" );
		$rowsSelectedGrid = $db->loadObjectList();
		echo $db->getErrorMsg();

		HTML_simplesearch::editSimpleSearch($simpleSearch, $rowsFilters,$rowsSelectedFilter,$rowsResultGrid,$rowsSelectedGrid,$createUser, $updateUser, $simpleSearch->getFieldsLength(),$option);
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
		$mainframe->redirect("index.php?option=$option&task=simpleSearch");
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
		$db->setQuery( "DELETE FROM #__sdi_sst_saf WHERE id_sst=".$simpleSearch->id);
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
					$db->setQuery( "INSERT INTO #__sdi_sst_saf (id_sst, id_saf) VALUES (".$simpleSearch->id.",".$filter_id.")" );
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
		$db->setQuery( "DELETE FROM #__sdi_sst_erg WHERE id_sst=".$simpleSearch->id);
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
					$db->setQuery( "INSERT INTO #__sdi_sst_erg (id_sst, id_erg) VALUES (".$simpleSearch->id.",".$grid_id.")" );
					if (!$db->query()) 
					{
						$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
						$mainframe->redirect("index.php?option=$option&task=simpleSearch" );
						exit();
					}
				}
			}
		}
		
		$simpleSearch->checkin();
		$mainframe->redirect("index.php?option=$option&task=simpleSearch");
	}
	
	function cancelSimpleSearch($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$simpleSearch =& new simpleSearch($db);
		$simpleSearch->bind(JRequest::get('post'));
		$simpleSearch->checkin();

		$mainframe->redirect("index.php?option=$option&task=simpleSearch" );
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
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchAdditionalFilter{$option}", 'searchAdditionalFilter', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_simplesearchfilter";
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_simplesearchfilter ";
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "name" && $filter_order <> "description")
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
		
		HTML_simplesearch::listAdditionalFilter( $rows, $pageNav, $search, $filter_order_Dir, $filter_order,$option);
	}
	
	function editAdditionalFilter ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$additionalFilter = new additionalFilter ($db);
		$additionalFilter->load($id);

		$additionalFilter->tryCheckOut($option,'additionalFilter');
		
		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		if ($additionalFilter->created)
		{ 
			if ($additionalFilter->createdby and $additionalFilter->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$additionalFilter->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($additionalFilter->updated and $additionalFilter->updated<> '0000-00-00 00:00:00')
		{ 
			if ($additionalFilter->updatedby and $additionalFilter->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$additionalFilter->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		HTML_simplesearch::editAdditionalFilter($additionalFilter, $createUser, $updateUser,$additionalFilter->getFieldsLength(),$option);
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
		
		$mainframe->redirect("index.php?option=$option&task=additionalFilter");
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
		
		$additionalFilter->checkin();
		$mainframe->redirect("index.php?option=$option&task=additionalFilter");
	}
	
	function cancelAdditionalFilter($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$additionalFilter =& new additionalFilter($db);
		$additionalFilter->bind(JRequest::get('post'));
		$additionalFilter->checkin();

		$mainframe->redirect("index.php?option=$option&task=additionalFilter" );
	}
}
?>