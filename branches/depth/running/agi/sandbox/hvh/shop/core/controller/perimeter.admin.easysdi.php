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

class ADMIN_perimeter {

	function listPerimeter($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		
		$search				= $mainframe->getUserStateFromRequest( "$option.searchPerimeter",'searchPerimeter','','string' );
		$search				= JString::strtolower( $search );
		
		$where="";
		if ($search)
		{
			$where = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(urlwfs) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		$perimeter = new perimeter( $db );
		$total = $perimeter->getObjectCount();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "urlwfs" and $filter_order <> "updated" and $filter_order <> "name" and $filter_order <> "description" and $filter_order <> "ordering")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM ".$perimeter->_tbl;		
		$query .= $where;
		$query .= $orderby;
									
		$db->setQuery( $query ,$limitstart,$limit);	
		

		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_Perimeter::listPerimeter( $rows, $pageNav,$option, $filter_order_Dir, $filter_order, $search);	

	}
	
	function goDownPerimeter($cid,$option){
			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$perimeter = new perimeter( $db );
			$perimeter->load( $cid [0]);
			$perimeter->orderDown();
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
	}
	
	function goUpPerimeter($cid,$option){
			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$perimeter = new perimeter( $db );
			$perimeter->load( $cid [0]);
			$perimeter->orderUp();
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );				
	}
	
	function saveOrderPerimeter($cid, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$perimeter =& new perimeter( $db );
		$total = $perimeter->getObjectCount();

		if (empty( $cid)) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		
		$order = $_POST[order];
		
		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$perimeter->load($cid[$i]);
			if ($perimeter->ordering != $order[$i])
			{
				$perimeter->ordering = $order[$i];
				if (!$perimeter->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
	}
	
	function editPerimeter( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$perimeter = new perimeter( $database );
		$perimeter->load( $id );					
		 
		$perimeter->tryCheckOut($option,'listPerimeter');
		
		$perimList = array();
		$perimList [] = JHTML::_('select.option','-1', JText::_("SHOP_PERIMETER_LIST") );
		$perimList = array_merge($perimList, $perimeter->getObjectListAsArray());
									
		//Select all available easysdi Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("SHOP_LIST_ACCOUNT_SELECT" ));
		$rowsAccount = array_merge($rowsAccount,account::getEasySDIAccountsList());
		
		HTML_Perimeter::editPerimeter( $perimeter, $rowsAccount, $perimList,$id, $option );
	}
	
	function savePerimeter($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowPerimeter =&	 new perimeter($database);
		
		if (!$rowPerimeter->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			exit();
		}

		//If a filter perimeter is selected, disable it for the use in manual perimeter (AKA localisation)
		if($rowPerimeter->filterperimeter_id > 0 )
		{
			$perimeter =&	 new perimeter($database);
			$perimeter->load($rowPerimeter->filterperimeter_id);
			$perimeter->setLocalisation(0);
		}
		else
		{
			//delete the default value
			$rowPerimeter->filterperimeter_id = null;
		}	

		
		$service_type = JRequest::getVar('service_type');
		if($service_type == "via_proxy")
		{
			$rowPerimeter->user = "";
			$rowPerimeter->password = "";
		}
		else
		{
			$rowPerimeter->account_id="";
		}
		
		if (!$rowPerimeter->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			exit();
		}
		
		$rowPerimeter->checkin();
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listPerimeter");
		}
	}
	
	function deletePerimeter($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			exit;
		}
		foreach( $cid as $id )
		{
			$Perimeter = new Perimeter( $database );
			$Perimeter->load( $id );
			
			$query = "SELECT DISTINCT o.name as name FROM #__sdi_order o 
											INNER JOIN #__sdi_order_perimeter pp ON o.id = pp.order_id 
											INNER JOIN #__sdi_perimeter p ON p.id = pp.perimeter_id  
											WHERE p.id=$id ";
			$database->setQuery($query);
			$results = $database->loadObjectList();
			if(count($results)>0)
			{
				$mainframe->enqueueMessage(JText::sprintf("SHOP_PERIMETER_DELETE_ERROR", $Perimeter->name),"INFO");
				foreach($results as $result)
				{
					$mainframe->enqueueMessage(" - ".$result->name,"INFO");
				}
				$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			}
			
			if (!$Perimeter->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			}												
		}

		$mainframe->redirect("index.php?option=$option&task=listPerimeter" );		
	}
	
	function copyPerimeter($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_COPY"),"error");
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			exit;
		}

		foreach( $cid as $id )
		{
			$Perimeter = new Perimeter( $database );
			$Perimeter->load( $id );
			$Perimeter->id=0;
			$Perimeter->guid=0;
			$Perimeter->ordering=0;
			$Perimeter->code="";
			if (!$Perimeter->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			}												
		}

		$mainframe->redirect("index.php?option=$option&task=listPerimeter" );		
	}
		
	function cancelPerimeter($option)
	{
		global $mainframe;
		$database = & JFactory::getDBO();
		$Perimeter = new Perimeter( $database );
		$Perimeter->bind(JRequest::get('post'));
		$Perimeter->checkin();

		$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
	}
}
	
?>