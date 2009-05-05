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

class HTML_cpanel {
	
	function listOrders($pageNav,$rows,$option,$orderstatus="",$ordertype="",$search="", $statusFilter="", $typeFilter="", $partnerFilter="", $supplierFilter="", $productFilter="", $orderpartner="", $ordersupplier="", $orderproduct="",$ResponsDateFrom, $ResponsDateTo, $SendDateFrom, $SendDateTo){
	JToolBarHelper::title( JText::_("EASYSDI_LIST_ORDERS"), 'generic.png' );
		$database =& JFactory::getDBO();
	?>	
		
		<form action="index.php" method="GET" id="adminForm" name="adminForm">		
	
		<table width="100%" class="adminlist">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" class="inputbox" value="<?php echo $search;?>" />			
				</td>
				
				<td>
				<select name="ordertype" >
				<option value=""><?php echo JText::_("EASYSDI_TYPES_LIST"); ?></option>
				 <?php  foreach($typeFilter as $type){ ?>
	              <option value="<?php echo $type->id;?>" <?php if ($ordertype==$type->id){?>selected="selected"<?php }?>>
					<?php echo JText::_($type->translation); ?>
				  </option>
				 <?php } ?>
				</select>
				</td>
				<td>
				<select name="orderstatus" >
				 <option value=""><?php echo JText::_("EASYSDI_STATUS_LIST"); ?></option>
				 <?php  foreach($statusFilter as $stat){ ?>
	              <option value="<?php echo $stat->id;?>" <?php if ($orderstatus==$stat->id){?>selected="selected"<?php }?>>
					<?php echo JText::_($stat->translation); ?>
				  </option>
				 <?php } ?>
				</select>				
				</td>
				<td>
				<select name="orderpartner" >
				 <option value=""><?php echo JText::_("EASYSDI_PARTNERS_LIST"); ?></option>
				 <?php  foreach($partnerFilter as $partner){ ?>
	              <option value="<?php echo $partner->partner_id;?>" <?php if ($orderpartner==$partner->partner_id){?>selected="selected"<?php }?>>
					<?php echo JText::_($partner->name); ?>
				  </option>
				 <?php } ?>
				</select>				
				</td>
				<td>
				<select name="ordersupplier" >
				 <option value=""><?php echo JText::_("EASYSDI_SUPPLIERS_LIST"); ?></option>
				 <?php  foreach($supplierFilter as $supplier){ ?>
	              <option value="<?php echo $supplier->partner_id;?>" <?php if ($ordersupplier==$supplier->partner_id){?>selected="selected"<?php }?>>
					<?php echo JText::_($supplier->name);?>
				  </option>
				 <?php } ?>
				</select>				
				</td>
				<td>
				<select name="orderproduct" >
				 <option value=""><?php echo JText::_("EASYSDI_PRODUCTS_LIST"); ?></option>
				 <?php  foreach($productFilter as $product){ ?>
	              <option value="<?php echo $product->id;?>" <?php if ($orderproduct==$product->id){?>selected="selected"<?php }?>>
					<?php echo JText::_($product->data_title); ?>
				  </option>
				 <?php } ?>
				</select>				
				</td>
				<!-- <td>
				<select name="orderarchived" >
					<option value=""><?php echo JText::_("EASYSDI_CMD_ARCHIVED_FILTER_ALL"); ?></option>
					<option value="1" <?php if($orderarchived=="1") echo "selected"; ?>><?php echo JText::_("EASYSDI_CMD_ARCHIVED_FILTER_YES"); ?></option>
					<option value="0" <?php if($orderarchived=="0") echo "selected"; ?>><?php echo JText::_("EASYSDI_CMD_ARCHIVED_FILTER_NO"); ?></option>
				</select>				
				</td>-->
				</tr>
				<tr>
					<td colspan=3>
						<b><?php echo JText::_( 'EASYSDI_DATE_SEND'); ?> </b>: 
						<br>
						<?php JHTML::_('behavior.calendar'); ?>
						<b><?php echo JText::_( 'EASYSDI_FROM'); ?></b><?php echo JHTML::_('calendar',$SendDateFrom, "SendDateFrom","SendDateFrom","%d-%m-%Y"); ?>
						<b><?php echo JText::_( 'EASYSDI_TO'); ?></b><?php echo JHTML::_('calendar',$SendDateTo, "SendDateTo","SendDateTo","%d-%m-%Y"); ?>
					</td>
					<td colspan=3>
						<b><?php echo JText::_( 'EASYSDI_DATE_RESPONSE'); ?> </b>: 
						<br>
						<?php JHTML::_('behavior.calendar'); ?>
						<b><?php echo JText::_( 'EASYSDI_FROM'); ?></b><?php echo JHTML::_('calendar',$ResponsDateFrom, "ResponsDateFrom","ResponsDateFrom","%d-%m-%Y"); ?>
						<b><?php echo JText::_( 'EASYSDI_TO'); ?></b><?php echo JHTML::_('calendar',$ResponsDateTo, "ResponsDateTo","ResponsDateTo","%d-%m-%Y"); ?>
						<input name="dateFormat" type="hidden" value="%d-%m-%Y">
					</td>
				</tr>
			</table>
		<br>
		<button type="submit" class="searchButton" > <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
		<br>		
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
	
	<!--<?php JHTML::_("behavior.modal","a.modal",$param); ?> -->
	<table class="adminlist">
	<thead>
	<tr>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_SHARP'); ?></th>
	<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows)+1; ?>);" /></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_ID'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_NAME'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_TYPE'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_STATUS'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_DATE'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_RESPONSE_DATE'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_PARTNER'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_PROVIDER'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_PRODUCT'); ?></th>
	<!-- <th><?php echo JText::_('EASYSDI_ORDER_ARCHIVED'); ?></th> -->
	</tr>
	</thead>
	<tbody>
	<?php
		$i=0;
		foreach ($rows as $row)
		{	$i++;
			?>		
			<tr>
			<td><?php echo $i; ?></td>
			<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->order_id; ?>" onclick="isChecked(this.checked);" /></td>
			<td><?php echo $row->order_id; ?></td>
			<td><span class="mdtitle" ><a class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->order_id?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo $row->name; ?></a></span><br>
			
			<!-- <td><?php echo JText::_("EASYSDI_ORDER_TYPE_".$row->type) ;?></td> -->
			 <td><?php echo JText::_($row->type_translation) ;?></td>
			<td><?php echo JText::_($row->status_translation) ;?></td>
			<td><?php if ($row->orderDate == "0000-00-00 00:00:00") echo ""; else echo date("d-m-Y", strtotime($row->orderDate));?></td>
			<td><?php if ($row->responseDate == "0000-00-00 00:00:00") echo ""; else echo date("d-m-Y", strtotime($row->responseDate));?></td>
			<?php 
		
				$query = "select CONCAT( CONCAT( a.address_agent_firstname, ' ' ) , a.address_agent_lastname ) AS name from #__easysdi_community_partner p inner join #__easysdi_community_address a on p.partner_id = a.partner_id WHERE p.user_id = ".$row->user_id ." and a.type_id=1" ;
				$database->setQuery($query);				 
		 	?>
		 	<td><?php echo $database->loadResult(); ?></td>
			<?php 
		
				$query = "select CONCAT( CONCAT( a.address_agent_firstname, ' ' ) , a.address_agent_lastname ) AS name from #__easysdi_community_address a inner join #__easysdi_product p on a.partner_id = p.partner_id inner join #__easysdi_order_product_list pl on p.id=pl.product_id WHERE pl.order_id = ".$row->order_id." and a.type_id=1" ;
				$database->setQuery($query);
				$partners = $database->loadResultArray();
				
				if (count($partners)>1)
					$partners=implode(", ", $partners);
				else if (count($partners)==1)
					$partners=$partners[0];
				else
					$partners="";
		 	?>
		 	<td><?php echo $partners; ?></td>
			<?php 
		
		
				$query = "SELECT p.data_title FROM #__easysdi_order_product_list pl INNER JOIN #__easysdi_product p ON pl.product_id=p.id WHERE order_id=".$row->order_id ;
				$database->setQuery($query);
				$products = $database->loadResultArray();	

				if (count($products)>1)
					$products=implode(", ", $products);
				else if (count($products)==1)
					$products=$products[0];
				else
					$products="";
		 	?>
		 	<td><?php echo $products; ?></td>
			<!-- <td><?php echo JText::_("EASYSDI_ORDER_ARCHIVED_".$row->archived) ;?></td> -->
			</tr>
				<?php		
		}
		
	?>
	</tbody>
	
	<tfoot>
		<tr>	
		<td colspan="11"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
	</table>
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listOrders">
		</form>
		
	<?php	
	}
	/*
	function editOrder($rowOrder,$id, $option){
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_ORDER"), 'generic.png' );
			
		?>				
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_ORDER_ID"); ?> : </td>
								<td><?php echo $rowOrder->order_id; ?></td>
								<td><input type="hidden" name="order_id" value="<?php echo $id;?>"></td>								
							</tr>			

							<tr>
								<td><?php echo JText::_("EASYSDI_ORDER_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowOrder->name; ?>" /></td>
							</tr>
							
							<tr>							
								<td><?php echo JText::_("EASYSDI_ORDER_TYPE"); ?> : </td>
								<td><?php echo $rowOrder->type; ?></td>
								<td><input type="hidden" name="type" value="<?php echo $rowOrder->type;?>"></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_ORDER_STATUS"); ?> : </td>
								<td><?php echo $rowOrder->status; ?></td>
								<td><input type="hidden" name="status" value="<?php echo$rowOrder->status;?>"></td>
							</tr>
							
							<tr>
								<td><?php echo JText::_("EASYSDI_ORDER_PROVIDER"); ?> : </td>
								<td><?php echo $rowOrder->provider_id; ?></td>
								<td><input type="hidden" name="provider_id" value="<?php echo $rowOrder->provider_id;?>"></td>
							</tr>
							

							<tr>
								<td><?php echo JText::_("EASYSDI_ORDER_REMARK"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="remark" value="<?php echo $rowOrder->remark; ?>" /></td>
							</tr>
							
														
							<tr>
								<td><?php echo JText::_("EASYSDI_ORDER_UPDATE"); ?> : </td>
								<td><?php echo $rowOrder->order_update; ?></td>
								<td><input type="hidden" name="order_update" value="<?php echo $rowOrder->order_update;?>"></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_ORDER_THIRD_PARTY"); ?> : </td>
								<td><?php echo $rowOrder->third_party; ?></td>
								<td><input type="hidden" name="third_party" value="<?php echo $rowOrder->third_party;?>"></td>
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_ORDER_RESPONSE_DATE"); ?> : </td>
								<td><?php echo $rowOrder->response_date; ?></td>	
								<td><input type="hidden" name="response_date" value="<?php echo $rowOrder->response_date;?>"></td>						
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_ORDER_RESPONSE_SEND"); ?> : </td>
								<td><?php echo $rowOrder->response_send; ?></td>	
								<td><input type="hidden" name="response_send" value="<?php echo $rowOrder->response_send;?>"></td>						
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_ORDER_USER"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="user" value="<?php echo $rowOrder->user_id; ?>" /></td>							
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	*/
}
?>