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
class SITE_cpanel {
	
	function downloadProduct(){
		
		$database =& JFactory::getDBO();		 	
		$user = JFactory::getUser();
		$order_id = JRequest::getVar('order_id');
		$product_id = JRequest::getVar('product_id');
		
		$query = "select count(*) from #__easysdi_order where order_id = $order_id AND user_id = $user->id";		
		$database->setQuery($query);
		$total = $database->loadResult();
		if ($total == 0) die;

		$query = "SELECT data,filename FROM #__easysdi_order_product_list where product_id = $product_id AND order_id = $order_id";
		$database->setQuery($query);
		$row = $database->loadObject();
		
		
		error_reporting(0);
		
		ini_set('zlib.output_compression', 0);
		header('Pragma: public');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Content-Transfer-Encoding: none');
		header('Content-Type: application/octetstream; name="'.$row->filename.'"');
		header('Content-Disposition: attachement; filename="'.$row->filename.'"');

		echo $row->data;
		die();
	
	
	}
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
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 5 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
	
		
		$database =& JFactory::getDBO();		 	
		$user = JFactory::getUser();
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($user->id);		
		
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
		
		$orderstatus=JRequest::getVar("orderstatus","");
		if ($orderstatus !=""){
				$filter .= " AND (status ='$orderstatus')";
		}
		
		
		$ordertype= JRequest::getVar("ordertype","");
		if ($ordertype !=""){
				$filter .= " AND (type ='$ordertype')";
		}
		
		if ( $search ) {
			$filter .= " AND (name LIKE '%$search%')";			
		}
		
		$query = "select * from #__easysdi_order where archived = 0 AND user_id = ".$user->id;
		$query .= $filter;
			
		$queryCount = "select count(*) from #__easysdi_order where archived = 0 AND  user_id = ".$user->id;
		$queryCount .= $filter;
		
		$database->setQuery($queryCount);
		$total = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		$pageNav = new JPagination($total,$limitstart,$limit);
				
		$database->setQuery($query);		
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		HTML_cpanel::listOrders($pageNav,$rows,$option,$orderstatus,$ordertype,$search);
		
	}
	

function orderReport($id){
	global $mainframe;
	$option = JRequest::getVar('option');	
	
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
			<td><?php echo $row->data_title?><?php if ($row->is_free)  {echo " (".JText::_("EASYSDI_FREE_PRODUCT").")" ; }?></td>
			<?php 
			if ($row->status == "AVAILABLE"){
				if($rows[0]->type=='O'){?>
				
				<td><a target="RAW" href="./index.php?format=raw&option=<?php echo $option; ?>&task=downloadProduct&order_id=<?php echo $row->order_id?>&product_id=<?php echo $row->product_id?>"> <?php echo JText::_("EASYSDI_DOWNLOAD_PRODUCT");?></a></td>				
				<?php
				}
			}						
			 ?>
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
	
	$order_id=JRequest::getVar("order_id",0);
	$query = "UPDATE  #__easysdi_order set status = 'SENT', order_update ='". $date->toMySQL()."' WHERE order_id = $order_id";
	
	$db->setQuery($query );
	
	if (!$db->query()) {		
		echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";						
		}
	
		
		
}
	
	
	
}
?>
