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
/*
foreach($_POST as $key => $val) 
echo '$_POST["'.$key.'"]='.$val.'<br />';
*/
defined('_JEXEC') or die('Restricted access');

class ADMIN_baselayer 
{
	function changeContent( $state = 0 )
	{
		global $mainframe;
		
		// Initialize variables
		$db		= & JFactory::getDBO();
		
		$cid = JRequest::getVar('cid', array());
		JArrayHelper::toInteger($cid);
		$option	= JRequest::getCmd( 'option' );
		$task	= JRequest::getCmd( 'task' );
		$total	= count($cid);
		$cids	= implode(',', $cid);
		
		$query = 'UPDATE #__sdi_baselayer' .
				' SET published = '. (int) $state .
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listObject" );
			exit();
		}

		

		switch ($state)
		{
			case 1 :
				$msg = $total." ".JText::sprintf('Item(s) successfully Published');
				break;

			case 0 :
			default :
				$msg = $total." ".JText::sprintf('Item(s) successfully Unpublished');
				break;
		}

		$cache = & JFactory::getCache('com_easysdi_map');
		$cache->clean();
		
		$mainframe->enqueueMessage($msg,"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=baseLayer" );
		exit();
	}
	function listBaseLayer ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$overviewLayerId	= JRequest::getCmd( 'overviewLayerId', '' );
		$overviewLayerMode	= JRequest::getCmd( 'overviewLayerMode', '' );
		
		if(($overviewLayerId !='')&& ($overviewLayerMode!='')){
			if($overviewLayerMode == 1){
				$query ="UPDATE #__sdi_baselayer set isoverviewlayer =0";
				$db->setQuery( $query );
				if (!$db->query())
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
					
				$query ="UPDATE #__sdi_baselayer set isoverviewlayer =1 where id =".$overviewLayerId;
				$db->setQuery( $query );
				if (!$db->query())
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
			}else if($overviewLayerMode == 0){
				
				$query ="UPDATE #__sdi_baselayer set isoverviewlayer =0 where id =".$overviewLayerId;
				$db->setQuery( $query );
				if (!$db->query())
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
			}		
			
		}
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchBaseLayer{$option}", 'searchBaseLayer', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(url) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(layers) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_baselayer ";
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_baselayer l ";
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "name" && $filter_order <> "url" && $filter_order <> "layers" && $filter_order <> "ordering" && $filter_order <> "description")
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
		
		HTML_baselayer::listBaseLayer($rows, $pageNav, $search, $filter_order_Dir, $filter_order,$option);
	}
	
	function editBaseLayer ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$baseLayer = new baseLayer ($db);
		$baseLayer->load($id);
		
		$baseLayer->tryCheckOut($option,'baseLayer');
		
		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		
		if ($baseLayer->created)
		{ 
			if ($baseLayer->createdby and $baseLayer->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$baseLayer->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($baseLayer->updated and $baseLayer->updated<> '0000-00-00 00:00:00')
		{ 
			if ($baseLayer->updatedby and $baseLayer->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$baseLayer->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		HTML_baselayer::editBaseLayer($baseLayer,$createUser, $updateUser, $baseLayer->getFieldsLength(), $option);
	}
	
	function deleteBaseLayer($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			exit;
		}
		foreach( $cid as $baseLayer_id )
		{
			$baseLayer = new baseLayer ($db);
			$baseLayer->load($baseLayer_id);
			
			if (!$baseLayer->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			}				
		}	
		$mainframe->redirect("index.php?option=$option&task=baseLayer");
	}
	
	function saveBaseLayer($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
		
		$overviewLayerId	= JRequest::getCmd( 'id', '' );
		$overviewLayerMode	= JRequest::getCmd( 'isoverviewlayer', '' );
		
		if(($overviewLayerId !='')&& ($overviewLayerMode!='')){
			if($overviewLayerMode == 1){
				$query ="UPDATE #__sdi_baselayer set isoverviewlayer =0";
				$db->setQuery( $query );
				if (!$db->query())
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
					
				$query ="UPDATE #__sdi_baselayer set isoverviewlayer =1 where id =".$overviewLayerId;
				$db->setQuery( $query );
				if (!$db->query())
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
			}else if($overviewLayerMode == 0){
				
				$query ="UPDATE #__sdi_baselayer set isoverviewlayer =0 where id =".$overviewLayerId;
				$db->setQuery( $query );
				if (!$db->query())
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
			}		
			
		}
		
		$baseLayer =& new baseLayer($db);
		if (!$baseLayer->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			exit();
		}				
		  
		if (!$baseLayer->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			exit();
		}
		
		$baseLayer->checkin();
		$mainframe->redirect("index.php?option=$option&task=baseLayer");
	}
	
	function orderUpBasemapLayer($option,$id){
		global  $mainframe;
		$db =& JFactory::getDBO();
		$baseLayer = new baseLayer( $db );
		$baseLayer->load( $id);
		$baseLayer->orderUp();
		$mainframe->redirect("index.php?option=$option&task=baseLayer" );
	}
	
	function orderDownBasemapLayer($option,$id){
		global  $mainframe;
		$db =& JFactory::getDBO();
		$baseLayer = new baseLayer( $db );
		$baseLayer->load( $id);
		$baseLayer->orderDown();
		$mainframe->redirect("index.php?option=$option&task=baseLayer" );
	}
	
	function cancelBaseLayer($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$baseLayer = new baseLayer( $db );
		$baseLayer->bind(JRequest::get('post'));
		$baseLayer->checkin();

		$mainframe->redirect("index.php?option=$option&task=baseLayer" );
	}	
	
	function saveOrderBaseMapLayer($option, $cid,$order)
	{
		global $mainframe;
		if( $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' ) != 'ordering')
		{
			$mainframe->redirect("index.php?option=$option&task=baseLayer" );
		}

		$db			= & JFactory::getDBO();
		$total		= count($cid);
		$conditions	= array ();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			// Instantiate an article table object
			$row = new baseLayer( $db );
			$row->load( (int) $cid[$i]);
			
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=baseLayer" );
					exit();
				}
			}
		}

		$cache = & JFactory::getCache('com_easysdi_map');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=baseLayer" );
		exit();
	}
}
?>