<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
class ADMIN_cpanel {
	
	
	function archiveOrder(){
		global  $mainframe;
		$option=JRequest::getVar("option");
		$order_id=JRequest::getVar("order_id",0);
		if ($order_id == 0){
			echo "<div class='alert'>";			
			echo JText::_("EASYSDI_ERROR_NO_ORDER_ID");
			echo "</div>";
		}else {
		$database =& JFactory::getDBO();		 	
		$user = JFactory::getUser();
		
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($user->id);		
		$query = "update #__easysdi_order set archived = 1 where user_id = ".$user->id." AND ORDER_ID =".$order_id;
		$database->setQuery($query);
		if (!$database->query()) {
				echo "<div class='alert'>";			
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
		}
		
		
		}		
		
		
	}
	function listOrders(){
		
		global  $mainframe;
		$option=JRequest::getVar("option");
		
		/*
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		*/
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		
		$database =& JFactory::getDBO();		 	
		
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
	
		/*$orderarchived=JRequest::getVar("orderarchived","");
		if ($orderarchived !=""){
				$filterList[] = "(o.archived ='orderarchived')";
		}*/
		
		$ordertype= JRequest::getVar("ordertype","");
		if ($ordertype !=""){
				$filterList[] = "(o.type ='$ordertype')";
		}
		
		$orderstatus=JRequest::getVar("orderstatus","");
		if ($orderstatus !=""){
				$filterList[] = "(o.status ='$orderstatus')";
		}
		
		$orderpartner= JRequest::getVar("orderpartner","");
		if ($orderpartner !=""){
				$filterList[] = "(p.partner_id ='$orderpartner')";
		}
		
		$ordersupplier = JRequest::getVar("ordersupplier","");
		if ($ordersupplier !="")
		{
			$filterList[] = "(prod.partner_id ='$ordersupplier')";
		}
		
		$orderproduct = JRequest::getVar("orderproduct","");
		if ($orderproduct !="")
		{
			$filterList[] = "(opl.product_id ='$orderproduct')";
		}
		
		$dateFormat = JRequest::getVar("dateFormat","");
		
		$SendDateFrom = JRequest::getVar("SendDateFrom","");
		$SendDateTo = JRequest::getVar("SendDateTo","");
		if ($SendDateFrom !="")
		{
			$filterList[] = "o.order_date >= STR_TO_DATE('".$SendDateFrom."', '".$dateFormat."')";
		}
		if ($SendDateTo !="")
		{
			$filterList[] = "o.order_date <= TIMESTAMPADD(DAY,1,STR_TO_DATE('".$SendDateTo."', '".$dateFormat."'))";
		}
		
		$ResponsDateFrom = JRequest::getVar("ResponsDateFrom","");
		$ResponsDateTo = JRequest::getVar("ResponsDateTo","");
		if ($ResponsDateFrom !="")
		{
			$filterList[] = "o.response_date >= STR_TO_DATE('".$ResponsDateFrom."', '".$dateFormat."')";
		}
		if ($ResponsDateTo !="")
		{
			$filterList[] = "o.response_date <= TIMESTAMPADD(DAY,1,STR_TO_DATE('".$ResponsDateTo."', '".$dateFormat."'))";
		}
		
		if ( $search ) {
			$filterList[] = "(o.name LIKE '%$search%') or (o.order_id LIKE '%$search%')";
		}
		
		if (count($filterList) > 0)
			$filter .= " where ".implode(" AND ", $filterList);
		
		$queryType = "select * from #__easysdi_order_type_list ";
		$database->setQuery($queryType);
		$typeFilter = $database->loadObjectList();
		
		$queryStatus = "select * from #__easysdi_order_status_list ";
		$database->setQuery($queryStatus);
		$statusFilter = $database->loadObjectList();
		
		$queryPartner = "select p.partner_id as partner_id, CONCAT( CONCAT( a.address_agent_firstname, ' ' ) , a.address_agent_lastname ) AS name from #__easysdi_community_partner p inner join #__easysdi_community_address a on p.partner_id=a.partner_id where a.type_id=1 order by name";
		$database->setQuery($queryPartner);
		$partnerFilter = $database->loadObjectList();
		
		$querySupplier = "SELECT p.partner_id AS partner_id, CONCAT( CONCAT( a.address_agent_firstname, ' ' ) , a.address_agent_lastname ) AS name FROM #__easysdi_community_partner p INNER JOIN	#__easysdi_community_address a ON p.partner_id = a.partner_id INNER JOIN	#__users u ON p.user_id = u.id where a.type_id=1 AND p.partner_id IN (SELECT partner_id FROM #__easysdi_community_actor WHERE role_id = (SELECT role_id FROM #__easysdi_community_role WHERE role_code ='PRODUCT')) ORDER BY u.name";
		$database->setQuery($querySupplier);
		$supplierFilter = $database->loadObjectList();
		
		$queryProduct = "select * from #__easysdi_product ";
		$database->setQuery($queryProduct);
		$productFilter = $database->loadObjectList();
		
		//$query = "select o.*, sl.code, sl.translation, p.partner_id as partner_id, p.partner_acronym as partner_acronym, tp.partner_id as thirdparty_id, tp.partner_acronym as thirdparty_acronym from #__easysdi_order o inner join #__easysdi_order_status_list sl on o.status=sl.id inner join #__easysdi_community_partner p on o.provider_id=p.partner_id inner join #__easysdi_community_partner tp on o.third_party=tp.partner_id";
		$query = "select distinct(o.order_id), prod.partner_id as supplier, o.*, o.order_date as orderDate, o.response_date as responseDate, sl.code, sl.translation as status_translation, tl.translation as type_translation from #__easysdi_order o inner join #__easysdi_order_status_list sl on o.status=sl.id inner join #__easysdi_order_type_list tl on o.type=tl.id left outer join #__easysdi_order_product_list opl on opl.order_id=o.order_id left outer join #__easysdi_community_partner p on  o.user_id=p.user_id left outer join #__easysdi_product prod on opl.product_id=prod.id";
		$query .= $filter;
		
		$queryCount = "select count(*) from #__easysdi_order o inner join #__easysdi_order_status_list sl on o.status=sl.id inner join #__easysdi_order_type_list tl on o.type=tl.id left outer join #__easysdi_order_product_list opl on opl.order_id=o.order_id left outer join #__easysdi_community_partner p on  o.user_id=p.user_id left outer join #__easysdi_product prod on opl.product_id=prod.id";
		$queryCount .= $filter;
		
		$database->setQuery($queryCount);
		$total = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		$pageNav = new JPagination($total,$limitstart,$limit);
				
		$database->setQuery($query,$limitstart,$limit);		
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		HTML_cpanel::listOrders($pageNav,$rows,$option,$orderstatus,$ordertype,$search, $statusFilter, $typeFilter, $partnerFilter, $supplierFilter, $productFilter, $orderpartner, $ordersupplier, $orderproduct ,$ResponsDateFrom, $ResponsDateTo, $SendDateFrom, $SendDateTo);
		
	}
	

function orderReport($id){
	global $mainframe;
	
	$db =& JFactory::getDBO();
	
	$query = "SELECT * FROM  #__easysdi_order a ,  #__easysdi_order_product_perimeters b where a.order_id = b.order_id and a.order_id = $id";
		
	$db->setQuery($query );
	
	
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	

		

	$query = "SELECT * FROM #__easysdi_order_product_list  a, #__easysdi_product b where a.product_id  = b.id and order_id = $id";	
	$db->setQuery($query );
	$rowsProduct = $db->loadObjectList();
	if ($db->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
	
	
		?>
		<h1><?php echo JText::_("EASYSDI_ORDERED_PRODUCT_LIST") ?></h1>
		<table>
		<?php
	$i=0;
	foreach ($rowsProduct as $row){?>
		<tr>
		<td><?php echo ++$i; ?></td>
		<td><?php echo $row->data_title?></td>				
		
		</tr>
	<?php }?>
	
	
		</table>
		<?php 
		
	$query = "SELECT * FROM  #__easysdi_perimeter_definition where id = ".$rows[0]->perimeter_id;	
	$db->setQuery($query );
	$rowsPerimeter = $db->loadObjectList();
	if ($db->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
		}	
	if ($rows[0]->perimeter_id > 0){		
	?>
	<h1><?php echo $rowsPerimeter[0]->perimeter_name; ?> (<?php echo $rowsPerimeter[0]->perimeter_desc; ?>)</h1>
		<?php }else{
			echo "<h1>".JText::_("EASYSDI_GEOMETRY_TEXT")."</h1>";
			
		} ?>
	<table>
		
	<?php
	$i=0;
	foreach ($rows as $row){?>
		<tr>
		<td><?php echo ++$i; ?></td>
		<td><?php echo $row->text?><?php if ($rows[0]->perimeter_id > 0) {echo "($row->value)";}?></td>				
		
		</tr>
	<?php }?>
	 </table>
	 
	
	 <?php
		
}

function sendOrder(){
	global $mainframe;
	
	$db =& JFactory::getDBO();
	
	 jimport("joomla.utilities.date");
	$date = new JDate();
	
	$queryStatus = "select id from #__easysdi_order_status_list where code ='SENT'";
	$database->setQuery($queryStatus);
	$status_id = $database->loadResult();
	
	$order_id=JRequest::getVar("order_id",0);
	$query = "UPDATE  #__easysdi_order set status = ".$status_id.", order_update ='". $date->toMySQL()."' WHERE order_id = $order_id";
	
	$db->setQuery($query );
	
	if (!$db->query()) {		
		echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";						
		}
}
	/*
	function editOrder($id,$option){
		
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
		$rowOrder = new Order( $database );
		
		$rowOrder->load( $id );

		HTML_cpanel::editOrder( $rowOrder,$id, $option );
		
	}	
	*/
	function deleteOrder($cid,$option){
		global  $mainframe;
		
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listOrders" );
			exit;
		}
		foreach( $cid as $id )
		{
			$Order = new Order( $database );
			$Order->load( $id );

			if ($order_id == 0){
				echo "<div class='alert'>";			
				echo JText::_("EASYSDI_DELETE_ORDER_MSG").$Order->id;
				echo "</div>";
			}else {
				if (!$Order->delete()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listOrders" );
				}
			}												
		}

		$mainframe->redirect("index.php?option=$option&task=listOrders" );
	}
	
	function saveOrder($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowOrder =& new Order($database);
				
		if (!$rowOrder->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			exit();
		}
		
		if (!$rowOrder->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			exit();
		}
	}
}
?>
