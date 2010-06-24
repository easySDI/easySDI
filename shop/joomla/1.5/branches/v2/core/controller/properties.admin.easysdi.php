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
			
			$query = "select * from  #__easysdi_product_properties_values_definition where id=$cid[0]";
			$db->setQuery( $query );
			
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "select * from  #__easysdi_product_properties_values_definition  where properties_id=$row1->properties_id and `order` > $row1->order   order by `order` ";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_product_properties_values_definition set `order`= $row1->order where id =$row2->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_product_properties_values_definition set `order`= $row2->order where id =$row1->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		

			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=$row1->properties_id" );
	}
	
	function goUpPropertyValue($cid,$option){
			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__easysdi_product_properties_values_definition where id=$cid[0]";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
								
			$query = "select * from  #__easysdi_product_properties_values_definition  where properties_id=$row1->properties_id and `order` < $row1->order  order by `order` desc";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_product_properties_values_definition set `order`= $row1->order where id =$row2->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_product_properties_values_definition set `order`= $row2->order where id =$row1->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}	
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=$row1->properties_id" );
	}
	
	function saveOrderPropertiesValues($cid, $properties_id, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$query = "select count(*) from  #__easysdi_product_properties_values_definition ";								
		$db->setQuery( $query );
		$total = $db->loadResult();

		if (empty( $cid)) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		$rowPropertiesValues =& new properties_values( $db );
		
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}
		
		$order = $_POST[order];
		
		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$rowPropertiesValues->load($cid[$i]);
			if ($rowPropertiesValues->order != $order[$i])
			{
				$rowPropertiesValues->order = $order[$i];
				if (!$rowPropertiesValues->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
			}
		}
		
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=$properties_id" );
	}
	
	function publish($cid,$published){
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		if ($published){
			$query = "update #__easysdi_product_properties_definition  set published = 1  where id=$cid[0]";			
			
		}else{
			$query = "update #__easysdi_product_properties_definition  set published = 0  where id=$cid[0]";
		}
		$db->setQuery( $query );
		if (!$db->query()) {		
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		
	}
	
	function goDownProperties($cid,$option){

			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__easysdi_product_properties_definition where id=$cid[0]";
			$db->setQuery( $query );
			
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "select * from  #__easysdi_product_properties_definition  where `order` > $row1->order   order by `order` ";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_product_properties_definition set `order`= $row1->order where id =$row2->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_product_properties_definition set `order`= $row2->order where id =$row1->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		

			$mainframe->redirect("index.php?option=$option&task=listProperties" );
	}
	
	function goUpProperties($cid,$option){

			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__easysdi_product_properties_definition where id=$cid[0]";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
								
			$query = "select * from  #__easysdi_product_properties_definition  where `order` < $row1->order  order by `order` desc";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_product_properties_definition set `order`= $row1->order where id =$row2->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_product_properties_definition set `order`= $row2->order where id =$row1->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}	
			$mainframe->redirect("index.php?option=$option&task=listProperties" );				
	}
	
	function saveOrderProperties($cid, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$query = "select count(*) from  #__easysdi_product_properties_definition ";								
		$db->setQuery( $query );
		$total = $db->loadResult();

		if (empty( $cid)) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		$rowProperties =& new Properties( $db );
		
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}
		
		$order = $_POST[order];
		
		// update ordering values
		
		for ($i = 0; $i < $total; $i++)
		{
			$rowProperties->load($cid[$i]);
			
			if ($rowProperties->order != $order[$i])
			{
				$rowProperties->order = $order[$i];
				if (!$rowProperties->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
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
		$use_pagination = JRequest::getVar('use_pagination',0);		
		$profile = $mainframe->getUserStateFromRequest( "profile{$option}", 'profile', '' );
		$category = $mainframe->getUserStateFromRequest( "category{$option}", 'category', '' );
		$payment = $mainframe->getUserStateFromRequest( "payment{$option}", 'payment', '' );
		$search	= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
		$search	= JString::strtolower( $search );
		
		$where="";
		if ($search)
		{
			$where = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(text) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		$query = "SELECT COUNT(*) FROM #__easysdi_product_properties_definition`";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'asc',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "published" and $filter_order <> "text" and $filter_order <> "mandatory" and $filter_order <> "update_date" and $filter_order <> "order")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "asc";
		}
		
		$orderby 	= ' order by `'. $filter_order .'` '. $filter_order_Dir;
		
		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM #__easysdi_product_properties_definition ";		
		$query .= $where;
		$query .= $orderby;						
		
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");					 			
		}		
	
		HTML_properties::listProperties($use_pagination, $rows, $pageNav,$option, $filter_order_Dir, $filter_order, $search);	
	}
	
	function editProperties( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowProperties = new properties( $database );
		$rowProperties->load( $id );					
			if ($id==0){
				$rowProperties->order="0";
			}
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_ROOT") );
		$database->setQuery( "  SELECT a.id AS value, b.name AS text 
								FROM #__sdi_account a,#__users b 
								WHERE a.root_id is null 
								AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		HTML_properties::editProperties( $rowProperties,$partners, $id, $option );
	}
	
	function saveProperties($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowProperties =& new properties($database);
				
		if (!$rowProperties->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProperties" );			
		}
		
		$rowProperties->update_date = date('Y-m-d h:i:s');
		
		if ($rowProperties->order == "0")
		{
			$query = "select max( `order`)+1  FROM #__easysdi_product_properties_definition ";
			$database->setQuery( $query );
			$maxOrder = $database->loadResult();
			if(!$maxOrder)$maxOrder = 1;
			$rowProperties->order = $maxOrder; 
		}
		
		if (!$rowProperties->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProperties" );			
		}
		
		$mainframe->redirect("index.php?option=$option&task=listProperties" );
	}
	
	function deleteProperties($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProperties" );
			echo $db->getErrorMsg();
		}
		foreach( $cid as $id )
		{
			$properties = new properties( $database );
			$properties->load( $id );
					
			if (!$properties->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProperties" );
			}												
		}		
		
		$mainframe->redirect("index.php?option=$option&task=listProperties" );		
	}
				
	function listPropertiesValues($properties_id , $option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		$profile = $mainframe->getUserStateFromRequest( "profile{$option}", 'profile', '' );
		$category = $mainframe->getUserStateFromRequest( "category{$option}", 'category', '' );
		$payment = $mainframe->getUserStateFromRequest( "payment{$option}", 'payment', '' );
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		
		if ($properties_id == "")
			$properties_id = JRequest::getVar('properties_id');
		
		$where="";
		if ($search)
		{
			$where = ' and (LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(text) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(translation) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ')';
		}
		
		$query = "SELECT COUNT(*) FROM #__easysdi_product_properties_values_definition WHERE properties_id=".$properties_id;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'asc',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "properties" and $filter_order <> "order" and $filter_order <> "value" and $filter_order <> "text" and $filter_order <> "translation")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "asc";
		}
		$orderby 	= ' order by `'. $filter_order .'` '. $filter_order_Dir;
		
		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM #__easysdi_product_properties_values_definition where properties_id=$properties_id ";		
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

		$query="select text from #__easysdi_product_properties_definition where id = $properties_id";		 
		$db->setQuery( $query );			
		$row = $db->loadObject() ;
	
		HTML_properties::listPropertiesValues($properties_id, $use_pagination, $rows, $row, $pageNav,$option, $filter_order_Dir, $filter_order, $search);	
	}

	function editPropertiesValues( $id, $option ) {
		global $mainframe;
		$database =& JFactory::getDBO(); 
		$rowProperties = new properties_values( $database );
		$rowProperties->load( $id );					
		if ($id==0){
			$rowProperties->order="0";
		}	
		$rowProperties->update_date = date('d.m.Y H:i:s'); 		
		
		$properties_id = JRequest::getVar(properties_id,-1);
		$query = "SELECT * FROM #__easysdi_product_properties_definition where id=".$properties_id;		
		$database->setQuery( $query );
		$property = $database->loadObject();
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						 		
		}
		
		HTML_properties::editPropertiesValues( $rowProperties,$property, $id, $option );
	}
	
	function savePropertiesValues($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		$rowProperties =&	 new properties_values($database);
		
		if (!$rowProperties->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues" );			
		}
		if ($rowProperties->order == "0")
		{
			$query = "select max( `order`)+1  FROM #__easysdi_product_properties_values_definition WHERE properties_id=$rowProperties->properties_id";
			$database->setQuery( $query );
			$maxOrder = $database->loadResult();
			if(!$maxOrder)$maxOrder = 1;
			$rowProperties->order = $maxOrder; 
		}
		if (!$rowProperties->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues" );			
		}
		$properties_id = JRequest::getVar('properties_id');
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$properties_id );
	}
	
	function deletePropertiesValues($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues" );
			echo $db->getErrorMsg();
		}
		foreach( $cid as $id )
		{
			$properties = new properties_values( $database );
			$properties->load( $id );
			
			//Reorder remaining property values
			$query = "SELECT *  FROM #__easysdi_product_properties_values_definition  
								WHERE  properties_id = $properties->properties_id 
								AND `order` > $properties->order  order by `order` ASC";
			$database->setQuery( $query );
			$rows2 = $database->loadObjectList() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}	
			
			$o = $properties->order;
			foreach ($rows2 as $row2 )
			{
				$query = "update #__easysdi_product_properties_values_definition set `order`= $o where id =$row2->id";
				$database->setQuery( $query );				
				if (!$database->query()) {		
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
				}
				$o = $o+1;
			}
			
			//Delete the current property value
			if (!$properties->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPropertiesValues" );
			}												
		}		
		
		$properties_id = JRequest::getVar('properties_id');
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$properties_id );
				
	}
	
}
	
?>