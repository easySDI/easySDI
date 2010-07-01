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
		$profile = $mainframe->getUserStateFromRequest( "profile{$option}", 'profile', '' );
		$category = $mainframe->getUserStateFromRequest( "category{$option}", 'category', '' );
		$payment = $mainframe->getUserStateFromRequest( "payment{$option}", 'payment', '' );
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		$search				= $mainframe->getUserStateFromRequest( "$option.searchPerimeter",'searchPerimeter','','string' );
		$search				= JString::strtolower( $search );
		
		$where="";
		if ($search)
		{
			$where = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(wfs_url) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(layer_name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(perimeter_name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(perimeter_desc) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		$query = "SELECT COUNT(*) FROM #__sdi_perimeter";
		
		//$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "urlwfs" and $filter_order <> "layername" and $filter_order <> "name" and $filter_order <> "description" and $filter_order <> "ordering")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		// Recherche des enregistrements selon les limites
		$query = "SELECT id,urlwfs,layername,name,description, ordering FROM #__sdi_perimeter ";		
		$query .= $where;
		$query .= $orderby;
									
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	

		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_Perimeter::listPerimeter($use_pagination, $rows, $pageNav,$option, $filter_order_Dir, $filter_order, $search);	

	}
	
	function goDownPerimeter($cid,$option){

			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__sdi_perimeter  where id=$cid[0]";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
							
			$query = "select * from  #__sdi_perimeter  where ordering > $row1->ordering   order by ordering ";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__sdi_perimeter set ordering= $row1->ordering where id =$row2->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__sdi_perimeter set ordering= $row2->ordering where id =$row1->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		

			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
	}
	
	function goUpPerimeter($cid,$option){

			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__sdi_perimeter where id=$cid[0]";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
								
			$query = "select * from  #__sdi_perimeter  where ordering < $row1->ordering  order by ordering desc";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__sdi_perimeter set ordering= $row1->ordering where id =$row2->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__sdi_perimeter set ordering= $row2->ordering where id =$row1->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}	
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );				
	}
	
	function saveOrderPerimeter($cid, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$query = "select count(*) from  #__sdi_perimeter ";								
		$db->setQuery( $query );
		$total = $db->loadResult();

		if (empty( $cid)) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		$rowMPerimeter =& new Perimeter( $db );
		
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}
		
		$order = $_POST[order];
		
		// update ordering values
		for ($i = 0; $i < $total; $i++)
		{
			$rowMPerimeter->load($cid[$i]);
			if ($rowMPerimeter->ordering != $order[$i])
			{
				$rowMPerimeter->ordering = $order[$i];
				if (!$rowMPerimeter->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
	}
	
	function editPerimeter( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowPerimeter = new Perimeter( $database );
		$rowPerimeter->load( $id );					
		 
		
		$perimList = array();
		$perimList [] = JHTML::_('select.option','-1', JText::_("EASYSDI_PERIM_LIST") );
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_perimeter order by name" );
		$perimList = array_merge($perimList, $database->loadObjectList());
									
		//Select all available easysdi Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_ACCOUNT_SELECT" ));
		$database->setQuery( "SELECT p.id as value, u.name as text FROM #__users u INNER JOIN #__sdi_account p ON u.id = p.user_id " );
		$rowsAccount = array_merge($rowsAccount, $database->loadObjectList());
		
		HTML_Perimeter::editPerimeter( $rowPerimeter, $rowsAccount, $perimList,$id, $option );
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
			$query = "UPDATE #__sdi_perimeter  SET is_localisation = 0 WHERE id = $rowPerimeter->filterperimeter_id";
			$database->setQuery($query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->stderr(),'error');
			}
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
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listPerimeter");
		}
	}
	
	function deletePerimeter($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			exit;
		}
		foreach( $cid as $id )
		{
			$Perimeter = new Perimeter( $database );
			$Perimeter->load( $id );
			
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
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_COPY"),"error");
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			exit;
		}
		$user = JFactory::getUser();
		$account = new accountByUserId($database);
		$account->load($user->id);
		foreach( $cid as $id )
		{
			$Perimeter = new Perimeter( $database );
			$Perimeter->load( $id );
			$Perimeter->id=0;
			$Perimeter->created =date('d.m.Y H:i:s');
			$Perimeter->createdby = $account->id;	
			if (!$Perimeter->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			}												
		}

		$mainframe->redirect("index.php?option=$option&task=listPerimeter" );		
	}
		
}
	
?>