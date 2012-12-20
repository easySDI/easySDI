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

class ADMIN_properties {
	
	
	function goDownPropertyValue($cid,$option){
			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$property_value = new property_value( $db );
			$property_value->load( $cid[0] );
			$property_value->orderDown();
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=$property_value->property_id" );
	}
	
	function goUpPropertyValue($cid,$option){
			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$property_value = new property_value( $db );
			$property_value->load( $cid [0]);
			$property_value->orderUp();	
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=$property_value->property_id" );
	}
	
	function saveOrderPropertiesValues($cid, $property_id, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$property_value =& new property_value( $db );
		$total = $property_value->getObjectCount($property_id);

		if (empty( $cid)) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		
		$order = $_POST[order];
		
		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$property_value->load($cid[$i]);
			if ($property_value->ordering != $order[$i])
			{
				$property_value->ordering = $order[$i];
				if (!$property_value->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
			}
		}
		
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=$property_id" );
	}
	
	function publish($cid,$published){
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$property = new property( $db );
		$property->load( $cid[0] );
		if ($published){
			if (!$property->publish())$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}else{
			if(!$property->unpublish())$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}
			
	}
	
	function goDownProperties($cid,$option){
			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$property = new property( $db );
			$property->load( $cid [0]);
			$property->orderDown();
			$mainframe->redirect("index.php?option=$option&task=listProperties" );
	}
	
	function goUpProperties($cid,$option){
			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$property = new property( $db );
			$property->load( $cid [0]);
			$property->orderUp();
			$mainframe->redirect("index.php?option=$option&task=listProperties" );
	}
	
	function saveOrderProperties($cid, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$property =& new property( $db );
		$total = $property->getObjectCount();
		
		if (empty( $cid)) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		$order = $_POST[order];
		
		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$property->load($cid[$i]);
			if ($property->ordering != $order[$i])
			{
				$property->ordering = $order[$i];
				if (!$property->store()) {
					return ;
				}
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listProperties" );
	}
	
	function listProperties($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$search	= $mainframe->getUserStateFromRequest( "$option.searchProperty",'searchProperty','','string' );
		$search	= JString::strtolower( $search );
		
		$where="";
		if ($search)
		{
			$where = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		$property = new property( $db );
		$total = $property->getObjectCount();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'asc',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "published" and $filter_order <> "description" and $filter_order <> "mandatory" and $filter_order <> "updated" and $filter_order <> "ordering")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "asc";
		}
		
		$orderby 	= ' order by `'. $filter_order .'` '. $filter_order_Dir;
		
		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM ".$property->_tbl;		
		$query .= $where;
		$query .= $orderby;						
		
		$db->setQuery( $query ,$limitstart,$limit);	
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");					 			
		}		
	
		HTML_properties::listProperties($rows, $pageNav,$option, $filter_order_Dir, $filter_order, $search);	
	}
	
	function editProperties( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$property = new property( $database );
		$property->load( $id );			

		$property->tryCheckOut($option,'listProperties');
		
			if ($id==0){
				$property->order="0";
			}
		$accounts = array();
		$accounts[] = JHTML::_('select.option','0', JText::_("SHOP_ACCOUNT_ROOT") );
		//$accounts = array_merge( $accounts, account::getEasySDIAccountsList() );
		$database =& JFactory::getDBO();
		$database->setQuery( "SELECT p.id as value, u.name as text FROM #__users u INNER JOIN #__sdi_account p ON u.id = p.user_id WHERE (p.root_id IS NULL OR p.root_id = 0)" );
		$accounts = array_merge( $accounts,$database->loadObjectList());
		
		
		$languages = $property->publishedLanguages();
		
		$labels = $property->loadLabels();
		
		HTML_properties::editProperties( $property,$accounts, $id, $languages, $labels,$option );
	}
	
	function saveProperties($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$property =& new property($database);
				
		if (!$property->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProperties" );			
		}
		
		if (!$property->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProperties" );			
		}
		if (!$property->storeLabels()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProperties" );			
		}
		
		$property->checkin();
		
		$mainframe->redirect("index.php?option=$option&task=listProperties" );
	}
	
	function deleteProperties($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProperties" );
			echo $db->getErrorMsg();
		}
		foreach( $cid as $id )
		{
			$property = new property( $database );
			$property->load( $id );
			
			$property->tryCheckOut($option,'listProperties');
			
			$query = "SELECT DISTINCT p.name as name FROM #__sdi_product p 
											INNER JOIN #__sdi_product_property pp ON p.id = pp.product_id 
											INNER JOIN #__sdi_propertyvalue pv ON pv.id = pp.propertyvalue_id  
											WHERE pv.property_id=$id ";
			$database->setQuery($query);
			$results = $database->loadObjectList();
			if(count($results)>0)
			{
				$mainframe->enqueueMessage(JText::sprintf("SHOP_PROPERTY_DELETE_ERROR", $property->name),"INFO");
				foreach($results as $result)
				{
					$mainframe->enqueueMessage(" - ".$result->name,"INFO");
				}
				$mainframe->redirect("index.php?option=$option&task=listProperties" );
			}
					
			if (!$property->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProperties" );
			}												
		}		
		
		$mainframe->redirect("index.php?option=$option&task=listProperties" );		
	}
				
	function listPropertiesValues($property_id , $option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		$search = $mainframe->getUserStateFromRequest( "searchPropertyValue{$option}", 'searchPropertyValue', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		
		if ($property_id == "")
			$property_id = JRequest::getVar('property_id');
		
		$where="";
		if ($search)
		{
			$where = ' and (LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ')';
		}
		
		$property_value = new property_value( $db );
		$total = $property_value->getObjectCount($property_id);
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'asc',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "property" and $filter_order <> "ordering" and $filter_order <> "name" and $filter_order <> "description" and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "asc";
		}
		$orderby 	= ' order by `'. $filter_order .'` '. $filter_order_Dir;
		
		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM ".$property_value->_tbl." where property_id=$property_id ";		
		$query .= $where;
		$query .= $orderby;						
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query );
		}
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			echo $db->getErrorMsg(); 			
		}	

		$property = new property( $db );
		$property->load( $property_id );
		
		HTML_properties::listPropertiesValues( $use_pagination, $rows, $property, $pageNav,$option, $filter_order_Dir, $filter_order, $search);	
	}

	function editPropertiesValues( $id, $option ) {
		global $mainframe;
		$database =& JFactory::getDBO(); 
		$property_value = new property_value( $database );
		$property_value->load( $id );					
		
		$property = new property( $database );
		$property->load( JRequest::getVar('property_id',-1) );
		
		$property_value->tryCheckOut($option,'listPropertiesValues&cid[]='.$property->id);
		
		$languages = $property_value->publishedLanguages();
		$labels = $property_value->loadLabels();
		
		HTML_properties::editPropertiesValues( $property_value,$property, $id,$languages, $labels, $option );
	}
	
	function savePropertiesValues($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		$property_id = JRequest::getVar('property_id');
		$property_value =&	 new property_value($database);
		
		if (!$property_value->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$property_id );			
		}

		if (!$property_value->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$property_id );			
		}
		
		if (!$property_value->storeLabels()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$property_id );			
		}
		
		$property_value->checkin();
		
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$property_id );
	}
	
	function deletePropertiesValues($cid ,$option){
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues" );
			echo $db->getErrorMsg();
		}
		foreach( $cid as $id )
		{
			$property_value = new property_value( $database );
			$property_value->load( $id );
			
			$property_value->tryCheckOut($option,'listPropertiesValues&cid[]='.$property_value->property_id);
			
			//Delete the current property value
			if (!$property_value->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".JRequest::getVar('property_id') );
			}												
		}		
		
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".JRequest::getVar('property_id') );
				
	}
	
	function cancelProperty($option)
	{
		global $mainframe;
		$database = & JFactory::getDBO();
		$property = new property( $database );
		$property->bind(JRequest::get('post'));
		$property->checkin();

		$mainframe->redirect("index.php?option=$option&task=listProperties" );
	}
	
	function cancelPropertyValue($option, $id)
	{
		global $mainframe;
		$database = & JFactory::getDBO();
		$property = new property_value( $database );
		$property->bind(JRequest::get('post'));
		$property->checkin();

		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$id );
	}
}
	
?>