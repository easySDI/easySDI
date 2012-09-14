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

class ADMIN_productextent {
	
	function listProductExtent($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$productextent = new productextent( $db );
		$total = $productextent->getObjectCount();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM ".$productextent->_tbl ;
		$search	= $mainframe->getUserStateFromRequest( "$option.searchProductextent",'searchProductextent','','string' );
		$search	= JString::strtolower( $search );
		if ($search)
		{
			$query .= ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query .= ' or LOWER(urlwfs) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );			
		}		
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if (				$filter_order <> "id" 
						and $filter_order <> "name"  
						and $filter_order <> "urlwfs" 
						and $filter_order <> "featuretype" 
						)
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		$query .= $orderby;
		
		$query .= " LIMIT $pageNav->limitstart, $pageNav->limit";	
		
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_productextent::listProductExtent($rows, $pageNav,$option, $filter_order_Dir, $filter_order, $search);	
	}
	
	function editProductExtent( $id, $option ) {
		$db =& JFactory::getDBO();
		$rowProductextent = new productextent($db);
		$rowProductextent->load( $id );					
		
		$rowProductextent->tryCheckOut($option,'listProductextent');
		
		//Select all available easysdi Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("SHOP_LIST_ACCOUNT_SELECT" ));
		$rowsAccount = array_merge($rowsAccount,account::getEasySDIAccountsList());
		
		HTML_productextent::editProductExtent( $rowProductextent,$rowsAccount,$id, $option );
	}
	
	function saveProductExtent($returnList ,$option){
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$productextent = new productextent( $db );
				
		if (!$productextent->bind( $_POST )) {			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProductExtent" );
			exit();
		}
		
		$service_type = JRequest::getVar('service_type');
		if($service_type == "via_proxy")
		{
			$productextent->user = "";
			$productextent->password = "";
		}
		else
		{
			$productextent->account_id="";
		}
		
		$productextent->checkin();
		
		if (!$productextent->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProductExtent" );
			exit();
		}
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listProductExtent");
		}
	}
		
	function deleteProductExtent($cid ,$option){
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProductExtent" );
			exit;
		}
		foreach( $cid as $id )
		{
			$productextent = new productextent( $db );
			$productextent->load( $id );
					
			if (!$productextent->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProductExtent" );
			}												
		}
		$mainframe->redirect("index.php?option=$option&task=listProductExtent" );		
	}
	
	function copyProductExtent($cid ,$option){		
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_COPY"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProductExtent" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$productextent = new productextent( $db );
			$productextent->load( $id );
			$productextent->id=0;
			$productextent->guid=0;
			if (!$productextent->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProductExtent" );
			}												
		}
		$mainframe->redirect("index.php?option=$option&task=listProductExtent" );		
	}
	
	function cancelProductExtent($option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		$productextent = new productextent( $db );
		$productextent->bind(JRequest::get('post'));
		$productextent->checkin();

		$mainframe->redirect("index.php?option=$option&task=listProductExtent" );
	}
}
	
?>