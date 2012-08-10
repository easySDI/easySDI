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
				$filterList[] = "(o.type_id ='$ordertype')";
		}
		
		$orderstatus=JRequest::getVar("orderstatus","4");
		if ($orderstatus !=""){
				$filterList[] = "(o.status_id ='$orderstatus')";
		}
		
		$orderAccount= JRequest::getVar("orderAccount","");
		if ($orderAccount !=""){
				$filterList[] = "(p.id ='$orderAccount')";
		}
		
		$ordersupplier = JRequest::getVar("ordersupplier","");
		if ($ordersupplier !="")
		{
			$filterList[] = "(prod.manager_id ='$ordersupplier')";
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
			$filterList[] = "o.created >= STR_TO_DATE('".$SendDateFrom."', '".$dateFormat."')";
		}
		if ($SendDateTo !="")
		{
			$filterList[] = "o.created <= TIMESTAMPADD(DAY,1,STR_TO_DATE('".$SendDateTo."', '".$dateFormat."'))";
		}
		
		$ResponsDateFrom = JRequest::getVar("ResponsDateFrom","");
		$ResponsDateTo = JRequest::getVar("ResponsDateTo","");
		if ($ResponsDateFrom !="")
		{
			$filterList[] = "o.response >= STR_TO_DATE('".$ResponsDateFrom."', '".$dateFormat."')";
		}
		if ($ResponsDateTo !="")
		{
			$filterList[] = "o.response <= TIMESTAMPADD(DAY,1,STR_TO_DATE('".$ResponsDateTo."', '".$dateFormat."'))";
		}
		
		if ( $search ) {
			$filterList[] = "(o.name LIKE '%$search%') or (o.id LIKE '%$search%')";
		}
		
		if (count($filterList) > 0)
			$filter .= " where ".implode(" AND ", $filterList);
		
		$queryType = "select * from #__sdi_list_ordertype ";
		$database->setQuery($queryType);
		$typeFilter = $database->loadObjectList();
		
		$queryStatus = "select * from #__sdi_list_orderstatus";
		$database->setQuery($queryStatus);
		$statusFilter = $database->loadObjectList();
		
		$queryAccount = "select a.id as account_id, 
								u.name AS name 
						 from #__users u, 
						 	  #__sdi_account a,
						 	  #__sdi_order o
						 where u.id = a.user_id
						 AND o.user_id = u.id
						 group by name order by name ";
		$database->setQuery($queryAccount);
		$accountFilter = $database->loadObjectList();
		
		$querySupplier = "SELECT a.id AS account_id, u.name AS name 
							FROM #__sdi_account a  INNER JOIN	#__users u ON a.user_id = u.id,
							#__sdi_product prd INNER JOIN #__sdi_objectversion v ON v.id=prd.objectversion_id INNER JOIN #__sdi_object o ON o.id = v.object_id  
							WHERE  o.account_id = a.id
							AND prd.published = 1
							group by name ORDER BY u.name";
		$database->setQuery($querySupplier);
		$database->setQuery($querySupplier);
		$supplierFilter = $database->loadObjectList();
		
		$queryProduct = "select p.* from #__sdi_product p INNER JOIN #__sdi_objectversion v ON v.id=p.objectversion_id
							 WHERE  p.published = 1 order by p.name";
		$database->setQuery($queryProduct);
		$productFilter = $database->loadObjectList();
		
		$query = "select distinct(o.id), 
							o.*, 
							o.created as orderDate, 
							o.sent as orderSendDate,
							o.response as responseDate, 
							sl.code, 
							sl.label as status_translation, 
							tl.label as type_translation 
				 from #__sdi_order o 
				 inner join #__sdi_list_orderstatus sl on o.status_id=sl.id 
				 inner join #__sdi_list_ordertype tl on o.type_id=tl.id 
				 left outer join #__sdi_order_product opl on opl.order_id=o.id 
				 left outer join #__sdi_account p on  o.user_id=p.user_id 
				 left outer join #__sdi_product prod on opl.product_id=prod.id
				 ";
		$query .= $filter;
		$query .= "order by responseDate";

		$queryCount = "select count(*) 
						from #__sdi_order o 
						inner join #__sdi_list_orderstatus sl on o.status_id=sl.id 
						inner join #__sdi_list_ordertype tl on o.type_id=tl.id 
						 left outer join #__sdi_order_product opl on opl.order_id=o.id 
						 left outer join #__sdi_account p on  o.user_id=p.user_id 
						 left outer join #__sdi_product prod on opl.product_id=prod.id";
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
		
		HTMLadmin_cpanel::listOrders($pageNav,$rows,$option,$orderstatus,$ordertype,$search, $statusFilter, $typeFilter, $accountFilter, $supplierFilter, $productFilter, $orderAccount, $ordersupplier, $orderproduct ,$ResponsDateFrom, $ResponsDateTo, $SendDateFrom, $SendDateTo);
		
	}
	
	function deleteOrder($cid,$option){
		global  $mainframe;
		?>
		<script>
		alert("<?php echo JText::_("SHOP_ORDER_SUPPRESS_CONFIRM_ACTION"); ?>");
		</script>	   
		<?php
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listOrders" );
			exit;
		}
		foreach( $cid as $id )
		{
			$Order = new order( $database );
			$Order->load( $id );

			if ($Order->id == 0)
			{
				echo "<div class='alert'>";			
				echo JText::_("SHOP_SHOP_MESSAGE_DELETE_ORDER").$Order->id;
				echo "</div>";
			}
			else 
			{
				if (!$Order->delete()) 
				{
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
			}												
		}
		
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
	}
	
	function orderReport($id)
	{
		global $mainframe;
		
		$db =& JFactory::getDBO();
		$rowOrder = null;
		$perimeterRows = null;			
		$query = "SELECT a.*, 
						 sl.label as slT, 
						 tl.label as tlT, 
						 a.name as order_name  
				  FROM  #__sdi_order a ,  
				  		#__sdi_list_orderstatus sl, 
				  		#__sdi_list_ordertype tl 
				  WHERE a.id = $id 
				  AND tl.id = a.type_id 
				  AND sl.id = a.status_id";
		$db->setQuery($query);
		$rowOrder = $db->loadObject();
		
		$query = "SELECT b.perimeter_id, 
						 b.text, 
						 b.value 
				  FROM  #__sdi_order a, 
				  		#__sdi_order_perimeter b 
				  WHERE a.id = b.order_id 
				  AND a.id = $id order by b.id";
		$db->setQuery($query);
		$perimeterRows = $db->loadObjectList();
		
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}
		

		//Customer name
		$user =$rowOrder->user_id;
		$queryUser = "SELECT name FROM #__users WHERE id = $user";
		$db->setQuery($queryUser );
		$user_name =  $db->loadResult();
		
		$third_name ='';
		//Third name
		$third = $rowOrder->thirdparty_id; 
		if( $third != 0)
		{
			$queryUser = "SELECT name FROM #__users WHERE id =(SELECT user_id FROM #__sdi_account where id= $third)";
			$db->setQuery($queryUser );
			$third_name =  $db->loadResult();
		}
		
		$query = "SELECT *, a.id as plId , opf.filename as filename
			  FROM #__sdi_order_product  a LEFT OUTER JOIN #__sdi_orderproduct_file opf ON opf.orderproduct_id = a.id, 
			       #__sdi_product b
			  WHERE a.product_id  = b.id 
			  AND a.order_id = $id";
			
		$db->setQuery($query );
		$rowsProduct = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";
		}
		
		HTMLadmin_cpanel::orderReportRecap($id,$rowOrder, $perimeterRows, $user_name, $third_name, $rowsProduct);
	}
	

}
?>
