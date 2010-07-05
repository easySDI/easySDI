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
		
		$rootPartner = new accountByUserId($database);
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
		$database =& JFactory::getDBO();
		$option=JRequest::getVar("option");
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		
		$search = $mainframe->getUserStateFromRequest( "searchOrder{$option}", 'searchOrder', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
	
		$ordertype= JRequest::getVar("ordertype","");
		if ($ordertype !=""){
				$filterList[] = "(o.type ='$ordertype')";
		}
		
		$orderstatus=JRequest::getVar("orderstatus","4");
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
		
		$queryPartner = "select p.id as partner_id, u.name AS name 
						 from #__users u, #__sdi_account p 
						 inner join #__sdi_address a on p.id=a.account_id ,
						 #__easysdi_order o
						 where a.type_id=1 
						 AND u.id = p.user_id
						 AND o.user_id = u.id
						 group by name order by name ";
		$database->setQuery($queryPartner);
		$partnerFilter = $database->loadObjectList();
		
		$querySupplier = "SELECT p.id AS partner_id, u.name AS name 
							FROM #__sdi_account p 
							INNER JOIN	#__sdi_address a ON p.id = a.account_id 
							INNER JOIN	#__users u ON p.user_id = u.id,
							#__easysdi_product prd 
							where a.type_id=1 AND p.id 
							IN (SELECT account_id FROM #__sdi_actor 
								WHERE role_id = (SELECT id FROM #__sdi_list_role WHERE role_code ='PRODUCT'))
							AND prd.partner_id = p.id
							AND prd.orderable = 1
							AND prd.published = 1
							group by name ORDER BY u.name";
		$database->setQuery($querySupplier);
		$database->setQuery($querySupplier);
		$supplierFilter = $database->loadObjectList();
		
		$queryProduct = "select * from #__easysdi_product WHERE orderable = 1 and published = 1 order by data_title";
		$database->setQuery($queryProduct);
		$productFilter = $database->loadObjectList();
		
		$query = "select distinct(o.order_id), 
							o.*, 
							o.order_date as orderDate, 
							o.order_send_date as orderSendDate,
							o.response_date as responseDate, 
							sl.code, 
							sl.translation as status_translation, 
							tl.translation as type_translation 
				 from #__easysdi_order o 
				 inner join #__easysdi_order_status_list sl on o.status=sl.id 
				 inner join #__easysdi_order_type_list tl on o.type=tl.id 
				 left outer join #__easysdi_order_product_list opl on opl.order_id=o.order_id 
				 left outer join #__sdi_account p on  o.user_id=p.user_id 
				 left outer join #__easysdi_product prod on opl.product_id=prod.id
				 ";
		$query .= $filter;
		$query .= "order by responseDate";

		$queryCount = "select count(*) 
						from #__easysdi_order o 
						inner join #__easysdi_order_status_list sl on o.status=sl.id 
						inner join #__easysdi_order_type_list tl on o.type=tl.id 
						left outer join #__easysdi_order_product_list opl on opl.order_id=o.order_id 
						left outer join #__sdi_account p on  o.user_id=p.user_id 
						left outer join #__easysdi_product prod on opl.product_id=prod.id";
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
		
		HTMLadmin_cpanel::listOrders($pageNav,$rows,$option,$orderstatus,$ordertype,$search, $statusFilter, $typeFilter, $partnerFilter, $supplierFilter, $productFilter, $orderpartner, $ordersupplier, $orderproduct ,$ResponsDateFrom, $ResponsDateTo, $SendDateFrom, $SendDateTo);
		
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

	function deleteOrder($cid,$option){
		global  $mainframe;
		?>
		<script>
		alert("<?php echo JText::_("EASYSDI_CONFIRM_ORDER_DELETE"); ?>");
		</script>	   
		<?php
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listOrders" );
			exit;
		}
		foreach( $cid as $id )
		{
			$Order = new order( $database );
			$Order->load( $id );

			if ($Order->order_id == 0)
			{
				echo "<div class='alert'>";			
				echo JText::_("EASYSDI_DELETE_ORDER_MSG").$Order->id;
				echo "</div>";
			}
			else 
			{
				if (!$Order->delete()) 
				{
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				
				$OrderProductList = new orderProductListByOrder($database);
				$OrderProductList->load($id);
				
				$query = "DELETE FROM #__easysdi_order_product_properties  WHERE order_product_list_id IN(SELECT id FROM #__easysdi_order_product_list WHERE order_id = $id)";
				$database->setQuery($query);
				$database->query();
				
				if(!$OrderProductList->delete())
				{
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				
				$OrderProductPerimeters = new orderProductPerimeterByOrder($database);
				$OrderProductPerimeters->load($id);
				if(!$OrderProductPerimeters->delete())
				{
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
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
