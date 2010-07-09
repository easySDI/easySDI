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
 



class HTMLadmin_cpanel {
	
	function listOrders($pageNav,$rows,$option,$orderstatus="",$ordertype="",$search="", $statusFilter="", $typeFilter="", $partnerFilter="", $supplierFilter="", $productFilter="", $orderpartner="", $ordersupplier="", $orderproduct="",$ResponsDateFrom, $ResponsDateTo, $SendDateFrom, $SendDateTo)
	{
		JToolBarHelper::title( JText::_("EASYSDI_LIST_ORDERS"), 'generic.png' );
		$database =& JFactory::getDBO();
		?>	
		<form action="index.php" method="GET" id="adminForm" name="adminForm">		
		<table width="100%" class="adminlist">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="searchOrder" class="inputbox" value="<?php echo $search;?>" />			
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
		<button type="submit" class="searchButton" > <?php echo JText::_("SHOP_SEARCH_BUTTON"); ?></button>
		<br>		
	<h3><?php echo JText::_("SHOP_SEARCH_RESULTS_TITLE"); ?></h3>
	
	<!--<?php JHTML::_("behavior.modal","a.modal",$param); ?> -->
	<table class="adminlist">
	<thead>
	<tr>
	<th class='title'><?php echo JText::_('CORE_SHARP'); ?></th>
	<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows)+1; ?>);" /></th>
	<th class='title'><?php echo JText::_('CORE_ID'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_ORDER_NAME'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_TYPE'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_STATUS'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_CREATION_DATE'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_DATE'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_RESPONSE_DATE'); ?></th>
	<th class='title'><?php echo JText::_('EASYSDI_ORDER_PARTNER'); ?></th>
	<!-- <th class='title'><?php echo JText::_('EASYSDI_ORDER_PROVIDER'); ?></th>-->
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
			<!--   <td><span class="mdtitle" ><a class="modal" href="http://localhost/Joomla/index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->order_id?>" rel="{handler:'iframe',size:{x:600,y:600}}"> <?php echo $row->name; ?></a></span><br>-->
			  <td><span class="mdtitle" ><a class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->order_id?>" rel="{handler:'iframe',size:{x:600,y:600}}"> <?php echo $row->name; ?></a></span><br>
			<!-- <td><?php echo JText::_("EASYSDI_ORDER_TYPE_".$row->type) ;?></td> -->
			 <td><?php echo JText::_($row->type_translation) ;?></td>
			<td><?php echo JText::_($row->status_translation) ;?></td>
			<td><?php if ($row->orderDate == "0000-00-00 00:00:00") echo ""; else echo date( JText::_("EASYSDI_DATEFORMAT_DAY_MONTH_YEAR"), strtotime($row->orderDate));?></td>
			<td><?php if ($row->orderSendDate == "0000-00-00 00:00:00") echo ""; else echo date(JText::_("EASYSDI_DATEFORMAT_DAY_MONTH_YEAR"), strtotime($row->orderSendDate));?></td>
			<td><?php if ($row->responseDate == "0000-00-00 00:00:00") echo ""; else echo date(JText::_("EASYSDI_DATEFORMAT_DAY_MONTH_YEAR"), strtotime($row->responseDate));?></td>
			<?php 
				$query = "select u.name AS name 
				from #__users u , #__easysdi_community_partner p 
				inner join #__easysdi_community_address a on p.partner_id = a.partner_id 
				WHERE p.user_id = ".$row->user_id ." and a.type_id=1 and u.id = p.user_id" ;
				$database->setQuery($query);				 
		 	?>
		 	<td><?php echo $database->loadResult(); ?></td>
		 	<!--
			<?php 
		
				$query = "select CONCAT( CONCAT( a.agentfirstname, ' ' ) , a.agentlastname ) AS name from #__sdi_address a inner join #__easysdi_product p on a.account_id = p.partner_id inner join #__easysdi_order_product_list pl on p.id=pl.product_id WHERE pl.order_id = ".$row->order_id." and a.type_id=1" ;
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
			-->
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
			<input type="hidden" name="isFrontend" value="false" />
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listOrders">
		</form>
		
	<?php	
	}
}
?>