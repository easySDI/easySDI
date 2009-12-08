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

class HTML_cpanel {

	
	function listOrders($pageNav,$rows,$option,$orderstatus="",$ordertype="",$search="", $statusFilter="", $typeFilter="", $redirectURL){
		global $mainframe;
		$db =& JFactory::getDBO();
	
		$queryStatus = "select id from #__easysdi_order_status_list where code ='SAVED'";
		$db->setQuery($queryStatus);
		$saved = $db->loadResult();
		
		$queryStatus = "select id from #__easysdi_order_status_list where code ='FINISH'";
		$db->setQuery($queryStatus);
		$finish = $db->loadResult();
	?>	
	<script>
	
	
	function showAllowedButton(status, saved, finish){
		
		if (status == saved){
		 document.getElementById('buttonArchive').disabled=false;
		 document.getElementById('buttonSent').disabled=false;
		}else
		if (status == finish){
		 document.getElementById('buttonArchive').disabled=false;
		 document.getElementById('buttonSent').disabled=true;
		}else{
		 document.getElementById('buttonArchive').disabled=true;
		 document.getElementById('buttonSent').disabled=true;
		}
	}
	
	
	</script>
	<div id="page" class="listOrders">
	<h2 class="contentheading"><?php echo JText::_("EASYSDI_LIST_ORDERS"); ?></h2>
		<div class="contentin">
		<form action="index.php" method="GET" id="ordersListForm" name="ordersListForm">
		<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
	
		<table width="100%">
			<tr>
				<td align="left"><b><?php echo JText::_("EASYSDI_SHOP_FILTER_TITLE");?></b>&nbsp;
				<td colspan="4" align="left">
				   <input type="text" id="searchOrder" name="searchOrder" class="inputbox" value="<?php echo $search;?>" />			
				</td>
			</tr>
			<tr>
				<td align="left">
					<?php echo JText::_("EASYSDI_CATALOG_SEARCH_CRITERIA"); ?>
				</td>
				<td class="listOrdersContainer">
				<select name="ordertype" >
				<option value=""><?php echo JText::_("EASYSDI_CMD_FILTER_ALL"); ?></option>
				 <?php  foreach($typeFilter as $type){ ?>
					 <option value="<?php echo $type->id;?>" <?php if ($ordertype==$type->id){?>selected="selected"<?php }?>>
					<?php echo JText::_($type->translation); ?>
				  </option>
				 <?php } ?>
				</select>
				</td>
				<td class="listOrdersContainer">
				<select name="orderstatus" >
				 <option value=""><?php echo JText::_("EASYSDI_CMD_STATUS_FILTER_ALL"); ?></option>
				 <?php  foreach($statusFilter as $stat){ ?>
					 <option value="<?php echo $stat->id;?>" <?php if ($orderstatus==$stat->id){?>selected="selected"<?php }?>>
					<?php echo JText::_($stat->translation); ?>
				  </option>
				 <?php } ?>
				</select>				
				</td>
				<td align="right">
				<table>
					<tr>
						<td>
							<button type="submit" class="searchButton" > <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
						</td>
						<td>
							<button id="newQuery" type="button" onClick="document.getElementById('Itemid').value='<?php echo $redirectURL; ?>';document.getElementById('view').value='shop';document.getElementById('ordersListForm').submit();" ><?php echo JText::_("EASYSDI_ORDER_NEW_QUERY"); ?></button>
						</td>
					</tr>
				</table>
				</td>
				</tr>
		</table>
		
		<br/>		
		<table width="100%">
			<tr>
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
				<td align="center"><?php echo JText::_("EASYSDI_SHOP_DISPLAY"); ?>
				<?php echo $pageNav->getLimitBox(); ?>
				</td>
				<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
	
	<?php
	$param=JRequest::getVar("param");
	JHTML::_("behavior.modal","a.modal",$param); 
	?>
	<?php
	if(count($rows) == 0){
		echo "<table><tbody><tr><td colspan=\"7\">".JText::_("EASYSDI_NO_RESULT_FOUND")."</td>";
	}else{?>
	<table class="box-table" id="orderList" width="100%">
	<thead>
	<tr>
	<th align="center"><?php echo JText::_('EASYSDI_ID'); ?></th>
	<th class="infoLogo"></th>
	<th class="logo"></th>
	<th class="reqDescr"><?php echo JText::_('EASYSDI_ORDER_NAME'); ?></th>
	<th align="center"><?php echo JText::_('EASYSDI_ORDER_STATUS'); ?></th>
	<th class="logo"></th>
	<th class="logo"></th>
	<th class="logo"></th>
	</tr>
	</thead>
	<tbody>
	<?php } ?>
	<?php
		$i=0;
		foreach ($rows as $row)
		{	$i++;
			?>		
			<tr>
			<td align="center"><?php echo $row->order_id; ?></td>
			<td align="center" class="infoLogo"><div class="<?php if($row->type == 1) echo"reqDevis"; if($row->type == 2) echo"reqOrder";  ?>" title="<?php echo JText::_($row->type_translation) ;?>"></div></td>
			<td align="center" class="logo">
			<?php
			if($row->order_send_date == "0000-00-00 00:00:00" && $row->RESPONSE_SEND == 0)
			{
				?>
				<div class="orderDate" title="<?php echo JText::_("EASYSDI_ORDER_TOOLTIP_DATE_CREATION")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->order_date));?>"> </div>
				<?php
			}
			else
			{
				if($row->RESPONSE_SEND)
				{
					
					?>
					<div class="orderDate" title="<?php echo JText::_("EASYSDI_ORDER_TOOLTIP_DATE_SEND")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->order_send_date));?> - <?php echo JText::_("EASYSDI_ORDER_TOOLTIP_DATE_RECEIVE")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->RESPONSE_DATE));?>" > </div>
					<?php 
				}
				else
				{
					?>
					<div class="orderDate" title="<?php echo JText::_("EASYSDI_ORDER_TOOLTIP_DATE_SEND")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->order_send_date));?> " > </div>
					<?php
				}
			}
			
			?>
			</td>			
			<td class="reqDescr"><span class="mdtitle" >
				<a class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->order_id?>" rel="{handler:'iframe',size:{x:600,y:600}}"> <?php echo $row->name; ?>
				</a>
				</span><br></td>
			<td align="center"><?php echo JText::_($row->status_translation) ;?></td>
			<?php
				if($saved == $row->status)
				{	?>
					<td class="logo">
					<div class="savedOrderOrder" title="<?php echo JText::_("EASYSDI_ORDER_TOOLTIP_ORDER") ?>"
					onClick="document.getElementById('order_id').value='<?php echo $row->order_id ;?>';document.getElementById('task<?php echo $option; ?>').value='orderDraft';document.getElementById('ordersListForm').submit();"></div>
					</td>
					<td class="logo">
					<div class="particular-order-link">
					<a  title="<?php echo JText::_("EASYSDI_SHOP_VIEW_RECAP");?>" id="viewOrderLink<?php echo $i; ?>" rel="{handler:'iframe',size:{x:600,y:600}}" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->order_id?>" class="modal">&nbsp;</a>
					</div>
					</td>
					<td class="logo">
					<div class="savedOrderSuppress" title="<?php echo JText::_("EASYSDI_ORDER_TOOLTIP_SUPPRESS") ?>"
					onClick="
					if (confirm('<?php echo JText::_("EASYSDI_ORDER_SUPPRESS_CONFIRM_ACTION") ?>')){
						document.getElementById('order_id').value='<?php echo $row->order_id ;?>';
						document.getElementById('task<?php echo $option; ?>').value='suppressOrder';
						document.getElementById('ordersListForm').submit();
						return true;
					}
						return false;
					"></div>
					</td>
					<?php 
				}
				else if($finish == $row->status)
				{
					?>
					<td><div class="noLogo"></td>
					<td class="logo">
					<div class="particular-order-link">
					<a  title="<?php echo JText::_("EASYSDI_SHOP_VIEW_RECAP");?>" id="viewOrderLink<?php echo $i; ?>" rel="{handler:'iframe',size:{x:600,y:600}}" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->order_id?>" class="modal">&nbsp;</a>
					</div>
					</td>
					<td class="logo">
					<div class="savedOrderArchive" title="<?php echo JText::_("EASYSDI_ORDER_TOOLTIP_ARCHIVE") ?>"
					onClick="
					if (confirm('<?php echo JText::_("EASYSDI_ORDER_ARCHIVE_CONFIRM_ACTION") ?>')){
						document.getElementById('order_id').value='<?php echo $row->order_id ;?>';
						document.getElementById('task<?php echo $option; ?>').value='archiveOrder';
						document.getElementById('ordersListForm').submit();
						return true;
					}
						return false;
					"></div>
					</td>
					<?php 
				}
				else{
					?>
					<td class="logo"><div class="noLogo"></td>
					<td class="logo">
					<div class="particular-order-link">
					<a  title="<?php echo JText::_("EASYSDI_SHOP_VIEW_RECAP");?>" id="viewOrderLink<?php echo $i; ?>" rel="{handler:'iframe',size:{x:600,y:600}}" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->order_id?>" class="modal">&nbsp;</a>
					</div>
					</td>
					<td class="logo"><div class="noLogo"></td>
					<?php 
				}
			?>
			</tr>
			
				<?php		
		}
	?>
	</tbody>
	</table>
	<br/>
	<table width="100%">
		<tr>
			<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
			<td align="center">&nbsp;</td>
			<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
		</tr>
	</table>
			<input type="hidden" name="order_id" id="order_id" value="">
			<input type="hidden" name="Itemid" id="Itemid" value="">
			<input type="hidden" name="view" id="view" value="">
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listOrders">
			<!--  <button id="buttonArchive" type="button" onClick="document.getElementById('task<?php echo $option; ?>').value='archiveOrder';document.getElementById('ordersListForm').submit();" ><?php echo JText::_("EASYSDI_ARCHIVE_ORDER"); ?></button>
			<button id="buttonSent" type="button" onClick="document.getElementById('task<?php echo $option; ?>').value='changeOrderToSend';document.getElementById('ordersListForm').submit();" ><?php echo JText::_("EASYSDI_SEND_ORDER"); ?></button>-->
		</form>
		</div>
		</div>
	<?php	
	}
	
	function processOrder($rows,$option,$rowOrder,$partner,$product_id, $treatmentTranslation, $treatmentCode){
				
		?>
		<?php JHTML::_("behavior.modal","a.modal",$param); ?>
		<div id="page" class="processTreatmentPage">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_PROCESS_ORDER_TITLE")." : ".$rowOrder->name; ?></h2>
		<div class="contentin">
		<table class="orderInfo">
			<tr>
				<td class="orderInfoTitle">
				<?php echo JText::_("EASYSDI_PROCESS_ORDER_TYPE") ;?> :
				</td>
				<td>
				 <?php echo JText::_($rows[0]->type_translation) ;?>
				</td>
			</tr>
			<tr>
				<td class="orderInfoTitle">
				<?php echo JText::_("EASYSDI_PROCESS_ORDER_CLIENT") ;?> :
				</td>
				<td>
				<a title="<?php echo $partner->username; ?>" class="modal" href="./index.php?tmpl=component&option=com_easysdi_shop&toolbar=1&task=showSummaryForPartner&SummaryForId=<?php echo $partner->id ;?>" rel="{handler:'iframe',size:{x:565,y:450}}"><?php echo $partner->name; ?></a>
				</td>
			</tr>
			<!-- recap -->
			<tr>
				<td>&nbsp;</td>
				<td><a title="<?php echo $rowOrder->order_id.": ".$rowOrder->name; ?>" class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReportForProvider&cid[]=<?php echo $rowOrder->order_id?>" rel="{handler:'iframe',size:{x:600,y:600}}"><?php echo JText::_("EASYSDI_SHOP_VIEW_RECAP") ;?></a></td>
			</tr>
			<?php if($treatmentTranslation){ ?>
			<tr>
				<td class="orderInfoTitle">
				<?php echo JText::_("EASYSDI_PROCESS_ORDER_FILTER") ;?> : 
				</td>
				<td>
				<?php  echo JText::_("EASYSDI_PROCESS_ORDER_FILTER_TREATMENT")." ".JText::_($treatmentTranslation) ;?>
				</td>
			</tr>
			<?php } ?>
			<?php if($treatmentCode == "AUTO"){ ?>
			<tr>
				<td colspan="2">
					<div class="alert">
						<?php echo JText::_("EASYSDI_PROCESS_ORDER_AUTO_WARNING") ;?>
					</div>
				</td>
			</tr>
			<?php } ?>
		</table>
		<br/>
		<h3><?php echo JText::_("EASYSDI_PROCESS_ORDER_SELECTED_PRODUCT") ; ?></h3>	
		<form enctype="multipart/form-data" action="index.php?option=<?php echo $option; ?>" method="POST" id="processOrderForm" name="processOrderForm">
		<?php
		$i=0;
		foreach ($rows as $row)
		{
			if($row->product_id == $product_id)
			{
				$i++;	
				?>		
				<table class="orderInfo">
				<thead>
				<tr>
				<th class="orderInfoTitle"><?php echo JText::_("EASYSDI_DATA")." ".$i?> : </th>
				<th><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_shop&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"><?php echo $row->data_title ;?></a></th>
				</tr>
				</thead>
				<tbody>
				<tr><td><?php echo JText::_("EASYSDI_PRICE") ;?></td><td> <input type="text" name="price<?php echo $row->product_id?>" value=""></td></tr>
				<tr><td><?php echo JText::_("EASYSDI_REMARK") ;?></td><td> <textarea rows="5" cols="30" name="remark<?php echo $row->product_id?>"></textarea></td></tr>
				<tr><td><?php echo JText::_("EASYSDI_FILE") ;?></td><td> <input type="file" name="file<?php echo $row->product_id?>" ></td></tr>
				</tbody>
				</table>
				<input type="hidden" name="product_id[]" value="<?php echo $row->product_id?>">
				<?php	
				break;
			}		
			
		}	
		if(count($rows) > $i)
		{
		?>
		<br>
		<h3><?php echo JText::_("Autres produits de la commande") ;?></h3>
		<?php 
		}
		foreach ($rows as $row)
		{
			?>
			<?php 
					
			if($row->product_id != $product_id)
			{
				$i++;
				?>		
				<table class="orderInfo">
				<thead>
				<tr>
				<th class="orderInfoTitle"><?php echo JText::_("EASYSDI_DATA")." ".$i?> : </th>
				<th><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_shop&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"><?php echo $row->data_title ;?></a></th>
				</tr>
				</thead>
				<tbody>
				<tr><td><?php echo JText::_("EASYSDI_PRICE") ;?></td><td> <input type="text" name="price<?php echo $row->product_id?>" value=""></td></tr>
				<tr><td><?php echo JText::_("EASYSDI_REMARK") ;?></td><td> <textarea rows="5" cols="30" name="remark<?php echo $row->product_id?>"></textarea></td></tr>
				<tr><td><?php echo JText::_("EASYSDI_FILE") ;?></td><td> <input type="file" name="file<?php echo $row->product_id?>" ></td></tr>			
				<!-- separator -->
				<?php
				if($i > 2){?>
				<tr><td colspan="2" halign="middle"><div class="separator" /></td></tr>
				<?php } ?>
				</tbody>
				</table>
				<input type="hidden" name="product_id[]" value="<?php echo $row->product_id?>">
				<?php
			}
		}			
		?>
		
		<input type="hidden" id="ordertype" name="ordertype" value="<?php echo JRequest::getVar("ordertype",""); ?>" />
		<input type="hidden" id="treatmentType" name="treatmentType" value="<?php echo JRequest::getVar("treatmentType",""); ?>" />
		<input type="hidden" id="orderStatus" name="orderStatus" value="<?php echo JRequest::getVar("orderStatus",""); ?>" />
		<input type="hidden" id="order_id" name="order_id" value="<?php echo $rowOrder->order_id;?>">
		<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listOrdersForProvider">
		
		<table width="100%">
		<tr><td align="right">
		<table>
			<tr>
				<td>
					<button type="button" onClick="document.getElementById('processOrderForm').task<?php echo $option; ?>.value='saveOrdersForProvider';document.getElementById('processOrderForm').submit();" ><?php echo JText::_("EASYSDI_SEND_RESULT"); ?></button>
				</td>
				<td>
					<button type="button" onClick="document.getElementById('processOrderForm').task<?php echo $option; ?>.value='listOrdersForProvider';document.getElementById('processOrderForm').submit();" ><?php echo JText::_("EASYSDI_CANCEL"); ?></button>
				</td>
			</tr>
		</table>
		</td></tr>
		</table>
		</form>
		</div>
		</div>
		<?php
		
	}
	
	function listOrdersForProvider($pageNav,$rows,$option,$ordertype="",$search="", $orderStatus="", $productorderstatus="", $productStatusFilter="", $productTypeFilter="", $treatmentList="", $treatmentType){
	
	?>	
		<div id="page" class="listOrdersForProvider">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_LIST_ORDERS_FOR_PROVIDER"); ?></h2>
		<div class="contentin">
		<form action="index.php" method="GET" id="ordersListForm" name="ordersListForm">
	
		<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
	
		<table class="treatment" width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_SHOP_FILTER_TITLE");?></b>&nbsp;
				</td>
				<td align="left" colspan="4">
					<input id="treatmentSearch" type="text" name="search" value="<?php echo $search;?>" class="inputbox"></input>			
				</td>
			</tr>
			<tr>
				<td align="left">
					<?php echo JText::_("EASYSDI_CATALOG_SEARCH_CRITERIA"); ?>
				</td>
				<td>
					<select name="treatmentType" id="treatmentType" >
						<option value="-1"><?php echo JText::_("EASYSDI_CMD_FILTER_ALL_TREATMENT"); ?></option>
						 <?php  foreach($treatmentList as $type){ ?>
			              <option value="<?php echo $type->id;?>" <?php if ($treatmentType==$type->id){?>selected="selected"<?php }?>>
							<?php echo JText::_($type->translation); ?>
						  </option>
						 <?php } ?>
					</select>
				</td>
				<td>
					<select name="ordertype" id="ordertype" >
						<option value=""><?php echo JText::_("EASYSDI_CMD_FILTER_ALL_TYPE"); ?></option>
						 <?php  foreach($productTypeFilter as $type){ ?>
			              <option value="<?php echo $type->id;?>" <?php if ($ordertype==$type->id){?>selected="selected"<?php }?>>
							<?php echo JText::_($type->translation); ?>
						  </option>
						 <?php } ?>
					</select>
				</td>
				<td>
					<select name="orderStatus" id="orderStatus" >
						 <option value=""><?php echo JText::_("EASYSDI_CMD_FILTER_ALL_STATUS"); ?></option>
						 <?php  foreach($productStatusFilter as $stat){ ?>
			              <option value="<?php echo $stat->id;?>" <?php if ($orderStatus==$stat->id){?>selected="selected"<?php }?>>
							<?php echo JText::_($stat->translation); ?>
						  </option>
						 <?php } ?>
					</select>				
				</td>
				<td>
					<button type="submit" class="searchButton" > <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
				</td>
			</tr>
		</table>
		
		<br/>
		<table width="100%">
			<tr>
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
				<td align="center"><?php echo JText::_("EASYSDI_SHOP_DISPLAY"); ?>
				<?php echo $pageNav->getLimitBox(); ?>
				</td>
				<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>

	<?php JHTML::_("behavior.modal","a.modal",$param); ?>
	<?php
	if(count($rows) == 0){
		echo "<table><tbody><tr><td colspan=\"7\">".JText::_("EASYSDI_NO_RESULT_FOUND")."</td>";
	}else{?>
	<table id="reqTreatTable" class="box-table">
	<thead>
	<tr>
	<th align="center"><?php echo JText::_('EASYSDI_ID'); ?></th>
	<th class="infoLogo"></th>
	<th class="logo"></th>
	<th class="logo"></th>
	<th align="left"><?php echo JText::_('EASYSDI_PRODUCT_TITLE_NAME'); ?></th>
	<th align="center"><?php echo JText::_('EASYSDI_ORDER_STATUS'); ?></th>
	<th class="logo"></th>
	</tr>
	</thead>
	<?php } ?>
	<tbody>
	<?php
		$i=0;
		//$old_order_id = -1;
		foreach ($rows as $row)
		{	
			//if ($old_order_id != $row->order_id){
				//$old_order_id = $row->order_id;
				$i++;
				?>
			<tr>
			<td align="center"><a title="<?php echo $row->order_id.": ".$row->name; ?>" class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReportForProvider&cid[]=<?php echo $row->order_id?>" rel="{handler:'iframe',size:{x:600,y:600}}"> <?php echo $row->order_id; ?></a></td>
				<td align="center" class="infoLogo"><div class="<?php if($row->type == 1) echo"reqDevis"; if($row->type == 2) echo"reqOrder";  ?>" title="<?php echo JText::_($row->type_translation) ;?>"></div></td>
				<td align="center" class="logo">
				<?php
				if($row->order_send_date == "0000-00-00 00:00:00" && $row->RESPONSE_SEND == 0)
				{
					?>
					<div class="orderDate" title="<?php echo JText::_("EASYSDI_ORDER_TOOLTIP_DATE_CREATION")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->order_date));?>"> </div>
					<?php
				}
				else
				{
					if($row->RESPONSE_SEND)
					{
						?>
						<div class="orderDate" title="<?php echo JText::_("EASYSDI_ORDER_TOOLTIP_DATE_SEND")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->order_send_date));?> - <?php echo JText::_("EASYSDI_ORDER_TOOLTIP_DATE_RECEIVE")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->RESPONSE_DATE));?>" > </div>
						<?php 
					}
					else
					{
						?>
						<div class="orderDate" title="<?php echo JText::_("EASYSDI_ORDER_TOOLTIP_DATE_SEND")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->order_send_date));?> " > </div>
						<?php
					}
				}
				?>
				</td>
				
				
				
				<td align="center" class="logo">
				<div class="particular-link">
				<a  title="<?php echo $row->username ;?>" id="partnerLink<?php echo $i; ?>" rel="{handler:'iframe',size:{x:565,y:450}}" href="./index.php?tmpl=component&option=com_easysdi_shop&toolbar=1&task=showSummaryForPartner&SummaryForId=<?php echo $row->client_id ;?>" class="modal">&nbsp;</a>
				</div>
				</td>
				
				<!-- <td class="reqName"><span class="mdtitle" ><b><a class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReportForProvider&cid[]=<?php echo $row->order_id?>" rel="{handler:'iframe',size:{x:600,y:600}}"> <?php echo $row->name; ?></a></b></span><br></td>-->
				<td align="left" id ="product_id"><?php echo $row->productName ;?></td>
				<td align="center"><?php echo JText::_($row->status_translation) ;?></td>
				<td class="logo" align="center"><div title="<?php echo JText::_('EASYSDI_PROCESS_ORDER'); ?>" class="treatRequest" id="treatReqButton" onClick="document.getElementById('product_list_id').value='<?php echo $row->product_list_id; ?>';document.getElementById('ordersListForm').task.value='processOrder'; document.getElementById('ordersListForm').submit();"/></td>
			</tr>
				
			<?php
			//}			
		
			?>
								
	<?php		
		}
		
	?>
	</tbody>
	</table>
	<br/>
	<table width="100%">
		<tr>
			<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
			<td align="center">&nbsp;</td>
			<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
		</tr>
	</table>
			<input type="hidden" id="product_list_id" name="product_list_id" value=""/>
			<input type="hidden" name="option" value="<?php echo $option; ?>"/>
			<input type="hidden" id="task" name="task" value="listOrdersForProvider"/>
		</form>
		</div>
		</div>
	<?php	
	}
	
	function orderReportRecap ($id,$isfrontEnd, $isForProvider,$rows, $user_name="", $third_name="" , $rowsProduct)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$print = JRequest::getVar('print');
				if ($print ==1 ){
			?>
			<script>window.print();</script> 
			<?php
		}
		
		?>
		<script>
		window.addEvent('domready', function() {
		$('printOrderRecap').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=<?php echo $option; ?>&task=<?php echo $task; ?>&cid[]=<?php echo $id; ?>&print=1','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
			});
		});
		</script>
		
		
		<div title ="<?php echo JText::_("EASYSDI_SHOP_PRINT"); ?>" id="printOrderRecap"></div>
		<div id="divOrderRecap">
		<h2 class="orderRecapTitle"><?php echo JText::_("EASYSDI_RECAP_ORDER_GTITLE"); ?></h2>
		<table class="orderRecap" width="100%">
		<tr>
		<td colspan="2" class="ortitle2">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_REQUEST"); ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_ID"); ?>
		</td>
		<td>
		<?php echo $id; ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_NAME"); ?>
		</td>
		<td>
		<?php echo $rows[0]->order_name; ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_TYPE"); ?>
		</td>
		<td>
		<?php echo JText::_($rows[0]->tlT); ?>
		</td>
		</tr>
		
		<?php 
		if($rows[0]->order_send_date == "0000-00-00 00:00:00")
		{
			?>
			<tr>
			<td class="ortitle3">
			<?php 
			echo JText::_("EASYSDI_RECAP_ORDER_CREATIONDATE"); ?>
			</td>
			<td>
			<?php echo date(JText::_("EASYSDI_DATEFORMAT_DAY_MONTH_YEAR_HOUR_MINUTE"), strtotime($rows[0]->order_date)); ?>
			</td>
			</tr>
			<?php 
		}
		else
		{
			?>
			<tr>
			<td class="ortitle3">
			<?php 
			echo JText::_("EASYSDI_RECAP_ORDER_SENDDATE"); ?>
			</td>
			<td>
			<?php echo date(JText::_("EASYSDI_DATEFORMAT_DAY_MONTH_YEAR_HOUR_MINUTE"), strtotime($rows[0]->order_send_date)); ?>
			</td>
			</tr>
			<tr>
			<td class="ortitle3">
			<?php
			echo JText::_("EASYSDI_RECAP_ORDER_RESPONSEDATE"); 
			?>
			</td>
			<?php 
			if($rows[0]->RESPONSE_DATE != "0000-00-00 00:00:00" && $rows[0]->RESPONSE_SEND == 1)
			{
				?>
				<td>
				<?php echo date(JText::_("EASYSDI_DATEFORMAT_DAY_MONTH_YEAR_HOUR_MINUTE"), strtotime($rows[0]->RESPONSE_DATE)); ?>
				</td>
				<?php 
			}
			?>
			</tr>
			<?php 
			
		}
		?>
		
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_STATUS"); ?>
		</td>
		<td>
		<?php echo JText::_($rows[0]->slT); ?>
		</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2" class="ortitle2">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_CLIENT"); ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_NAME"); ?>
		</td>
		<td>
		<?php echo $user_name; ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_THIRD"); ?>
		</td>
		<td>
		<?php echo $third_name; ?>
		</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2" class="ortitle2">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_PERIMETER"); ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_PERIMETER_TYPE"); ?>
		</td>
		<td>
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
			echo $rowsPerimeter[0]->perimeter_desc;
			
		}else
		{
			echo JText::_("EASYSDI_GEOMETRY_TEXT");

		} ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_PERIMETER_CONTENT"); ?>
		</td>
		<td>
		<table width="100%">
		<?php
		$i=0;
		foreach ($rows as $row){?>
			<tr>
				<td width="10%" class="ornum"><?php echo ++$i; ?> - </td>
				<td width="90%"><?php echo $row->text; ?><!--<br> [<?php echo $row->value;?>]</td>
				
			--></tr>
			<?php }?>
		</table>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_SURFACE"); ?>
		</td>
		<td>
		<?php if($rows[0]->surface != 0)
		{
			echo ($rows[0]->surface)/1000000; 
			echo JText::_("EASYSDI_RECAP_ORDER_KM2") ; 
		}
		?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_BUFFER"); ?>
		</td>
		<td>
		<?php if($rows[0]->buffer != 0)
		{
			echo $rows[0]->buffer; 
			echo JText::_("EASYSDI_RECAP_ORDER_METER") ; 
		} 
		else
		{
			echo JText::_("EASYSDI_RECAP_ORDER_NONE") ; 
		}
		?>
		</td>
		</tr>
		
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_PREVIEW"); ?>
		</td>
		<td>
		<?php HTML_cpanel::viewOrderRecapPerimeterExtent($rows[0]->order_id,$rows[0]->perimeter_id , $isfrontEnd); ?>
		</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2" class="ortitle2">
		<?php echo JText::_("EASYSDI_ORDERED_PRODUCT_LIST"); ?>
		</td>
		</tr>
		
		<?php
		$i=0;
		
		$queryStatus = "select id from #__easysdi_order_product_status_list where code ='AVAILABLE'";
		$db->setQuery($queryStatus);
		$status_id = $db->loadResult();
				
		foreach ($rowsProduct as $row){ ?>
		<tr>
		   <td colspan="2" >
		     <fieldset class="orderRecapTreatment"><legend class="orderRecapTreatmentLegend"><?php echo $row->data_title?><?php if ($row->is_free)  {echo " (".JText::_("EASYSDI_FREE_PRODUCT").")" ; }?></legend>
			<table width="100%">
			<tr>
			<td colspan="2" >
			<table >
				<tr>
  				  <?php
				/*	if ($row->status == $status_id){
						$queryType = "select id from #__easysdi_order_type_list where code='O'";
						$db->setQuery($queryType);
						$type = $db->loadResult();
					
						if($rows[0]->type==$type){?>
			
					<td ><a target="RAW"
						href="./index.php?format=raw&option=<?php echo $option; ?>&task=downloadProduct&order_id=<?php echo $row->order_id?>&product_id=<?php echo $row->product_id?>">
						<?php echo JText::_("EASYSDI_DOWNLOAD_PRODUCT");?></a></td>
						<?php
						}
						
					}*/
					?>
				</tr>
			</table>
			</td>
			</tr>
			
			<?php
			//Get product properties
			$queryPropertiesCode = "SELECT DISTINCT code FROM #__easysdi_order_product_properties where order_product_list_id =$row->plId";
			$db->setQuery($queryPropertiesCode);
			$rowsPropertiesCode = $db->loadObjectList();
			
			foreach($rowsPropertiesCode as $rowPropertyCode)
			{
			
				$queryProductProperties = "SELECT * FROM #__easysdi_order_product_properties where order_product_list_id =$row->plId AND code = '$rowPropertyCode->code'";
				$db->setQuery($queryProductProperties);
				$rowsProductProperties = $db->loadObjectList();
				?>
				<tr>
				<td class="ortitle4">
				<?php
						$queryProperty = "SELECT translation, type_code FROM #__easysdi_product_properties_definition WHERE code = '$rowPropertyCode->code'";
						$db->setQuery($queryProperty);
						$rowProperty = $db->loadObject();
						echo JText::_($rowProperty->translation);
				//echo JText::_($rowPropertyCode->code);
				?>
				</td>
				
				<td>
				<table>
				<?php 
				foreach ($rowsProductProperties as $rowProductProperties)
				{
					?>
					<tr>
					<td>
					<?php 
					if($rowProductProperties->property_id == 0)
					{	
						if($rowProperty->type_code == 'message')
						{
							echo JText::_($rowProductProperties->property_value);
						}
						else
						{
							echo $rowProductProperties->property_value;
						}
					}
					else
					{
						$queryPropertyValue = "SELECT translation FROM #__easysdi_product_properties_values_definition WHERE id = $rowProductProperties->property_id";
						$db->setQuery($queryPropertyValue);
						$rowProperty = $db->loadResult();
						echo JText::_($rowProperty);
					}
					?>
					</td>
					</tr>
					<?php
				}
				?>
				</table>
				</td>
				</tr>
				<?php
			}
			if ($row->status == $status_id)
			{?>
				<tr>
				<td colspan=2>				
				<table class="orderRecapResultTable" width="100%">
				<tr>
				<td rowspan="3" class="orderRecapResult">
				</td>
				<td class="ortitle4">
				<?php echo JText::_("EASYSDI_RECAP_ORDER_PRICE"); ?>			
				</td>
				<td>
				<?php echo $row->price.JText::_("EASYSDI_RECAP_ORDER_MONEY"); ?>
				</td>
				</tr>
				
				<tr>
				<td class="ortitle4">
				<?php echo JText::_("EASYSDI_RECAP_ORDER_REM"); ?>			
				</td>
				<td>
				<?php echo $row->remark; ?>
				</td>
				</tr>
				
				<tr>
				<td class="ortitle4">
				<?php echo JText::_("EASYSDI_RECAP_ORDER_FILE")?>			
				</td>
				<?php
				$queryOrderStatus = "select l.code from #__easysdi_order_status_list l, #__easysdi_order o where l.id=o.status AND o.order_id=".$id;
				$db->setQuery($queryOrderStatus);
				$status_code = $db->loadResult();
				if($status_code != "HISTORIZED"){
				?>
				<td>
					<a target="RAW"
						href="./index.php?format=raw&option=<?php echo $option; ?>&task=downloadProduct&order_id=<?php echo $row->order_id?>&product_id=<?php echo $row->product_id?>">
						<?php echo $row->filename ?></a>
				</td>
				<?php }else{ ?>
				<td>
					<div class='info'>
					<?php echo JText::_("EASYSDI_ORDER_HISTORIZED MESSAGE"); ?>
					</div>
				</td>
				<?php } ?>
				</tr>
				</table>
				
				</td>
				</tr>
				<?php 
			}
			?>
		   </table>
		   </fieldset>
		   </td>
		   </tr>

		<?php
		}?>
		
		
		</table>
		</div>
		<?php
	}
	
	function viewOrderRecapPerimeterExtent($order_id, $perimeter_id,$isfrontEnd){
	if($isfrontEnd == true)
	{
	?>
	<script
	type="text/javascript"
	src="./administrator/components/com_easysdi_core/common/lib/js/openlayers2.7/OpenLayers.js"></script>
	
	<script
	type="text/javascript"
	src="./administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/proj4js.js">
	
	</script>
	<?php
	}
	else
	{ ?>
	<script
	type="text/javascript"
	src="./administrator/components/com_easysdi_core/common/lib/js/openlayers2.7/OpenLayers.js"></script>
	
	<script
	type="text/javascript"
	src="./administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/proj4js.js">
	</script>
	<?php
	} ?>

	
	<?php	
		
	global  $mainframe;
	$db =& JFactory::getDBO(); 
	$isFreeSelectionPerimeter = false;
	$queryPerimeter = "select * from #__easysdi_perimeter_definition where id = $perimeter_id";
	$db->setQuery($queryPerimeter);
	$perimeterDef = $db->loadObject();
	if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
	}	
	
	if($perimeterDef->wfs_url == '' && $perimeterDef->wms_url == '')
	{	
		$isFreeSelectionPerimeter = true;
	}
	
	$query = "select * from #__easysdi_basemap_definition where def=1"; 
	$db->setQuery( $query);
	$rowsBaseMap = $db->loadObject();		  
	if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
	}					  
?>
<script>
function setAlpha(imageformat)
{
	var filter = false;
	if (imageformat.toLowerCase().indexOf("png") > -1) {
		filter = OpenLayers.Util.alphaHack(); 
	}
	return filter;
}

var map;
function initMap()
{
	var options = {
	    	projection: new OpenLayers.Projection("<?php echo $rowsBaseMap->projection; ?>"),
            displayProjection: new OpenLayers.Projection("<?php echo $rowsBaseMap->projection; ?>"),
            units: "<?php echo $rowsBaseMap->unit; ?>",
			<?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
            minScale: <?php echo $rowsBaseMap->minResolution; ?>,
            maxScale: <?php echo $rowsBaseMap->maxResolution; ?>,                
			<?php } ?>
            maxExtent: new OpenLayers.Bounds(<?php echo $rowsBaseMap->maxExtent; ?>),
            controls: [] 
	    <?php
			if($rowsBaseMap->restrictedExtent == '1') echo  ",restrictedExtent: new OpenLayers.Bounds(".$rowsBaseMap->maxExtent.")\n"
	    ?>
		<?php
			if($rowsBaseMap->restrictedScales != '') echo  ",scales: [".$rowsBaseMap->restrictedScales."]\n"
	    ?>
	};
	map = new OpenLayers.Map("map", options);
				  
	baseLayerVector = new OpenLayers.Layer.Vector("BackGround",{isBaseLayer: true,transparent: "true"}); 
	map.addLayer(baseLayerVector);
<?php

$query = "select * from #__easysdi_basemap_content where basemap_def_id = ".$rowsBaseMap->id." order by ordering"; 
$db->setQuery( $query);
$rows = $db->loadObjectList();
		  
if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
}
$i=0;
foreach ($rows as $row){				  
?>				
				  
		layer<?php echo $i; ?> = new OpenLayers.Layer.<?php echo $row->url_type; ?>( "<?php echo $row->name; ?>",
                    
			<?php 
			if ($row->user != null && strlen($row->user)>0){
				//if a user and password is requested then use the joomla proxy.
				$proxyhost = config_easysdi::getValue("PROXYHOST");
				$proxyhost = $proxyhost."&type=wms&basemapscontentid=$row->id&url=";
				echo "\"$proxyhost".urlencode  (trim($row->url))."\",";												
			}else{	
				//if no user and password then don't use any proxy.					
				echo "\"$row->url\",";	
			}					
			?>
			
                    {layers: '<?php echo $row->layers; ?>', format : "<?php echo $row->img_format; ?>",transparent: "true"},                                          
                     {singleTile: <?php echo $row->singletile; ?>},                                                    
                     {     
                      maxExtent: new OpenLayers.Bounds(<?php echo $row->maxExtent; ?>),
                   <?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
                      	minScale: <?php echo $row->minResolution; ?>,
                        maxScale: <?php echo $row->maxResolution; ?>,
                        <?php } ?>                 
                     projection:"<?php echo $row->projection; ?>",
                      units: "<?php echo $row->unit; ?>",
                      transparent: "true"
                     }
                    );
                    <?php
                    if (strtoupper($row->url_type) =="WMS")
                    {
                    	?>
                    	layer<?php echo $i; ?>.alpha = setAlpha('image/png');
                    	<?php
                    } 
                    ?>
                 map.addLayer(layer<?php echo $i; ?>);
<?php 
$i++;
} ?>                   
		<?php
		//Add the command perimeter
		$queryPerimeterValue = "SELECT value FROM #__easysdi_order_product_perimeters WHERE order_id = $order_id";
		$db->setQuery( $queryPerimeterValue);
		$rowsPerimeterValue = $db->loadObjectList();
		?>
		
	     <?php 
		if($isFreeSelectionPerimeter == true)
		{
			?>
			var vectors;
			vectors = new OpenLayers.Layer.Vector("Vector Layer",{isBaseLayer: false,transparent: "true"});
			map.addLayer(vectors);
			//Draw polygon
			var newLinearRingComponents = new Array();
			<?php
			foreach($rowsPerimeterValue as $value)
			{
				?>
					var curValue = "<?php echo $value->value; ?>";
					var x= curValue.substring(0,curValue .indexOf(" ", 0));
					var y= curValue.substring(curValue .indexOf(" ", 0)+1,curValue .length);
					newLinearRingComponents.push (new OpenLayers.Geometry.Point(x,y));
					<?php
			}	
			?>
			var newLinearRing = new OpenLayers.Geometry.LinearRing(newLinearRingComponents);
			var feature = new OpenLayers. Feature. Vector(new OpenLayers.Geometry.Polygon([newLinearRing]));									  			
			vectors.addFeatures([feature]);
			
			//Zoom to extent
			var vFeatures = vectors.features;
			map.zoomToExtent(vFeatures[0].geometry.getBounds(),false);
			<?php 
		}
		else
		{
			//Call wfs
			
			$proxyhostOrig = config_easysdi::getValue("PROXYHOST");
			
			$proxyhost = $proxyhostOrig."&type=wfs&perimeterdefid=$perimeterDef->id&url=";
				
			if ($perimeterDef->wfs_url!=null && strlen($perimeterDef->wfs_url)>0){
				$wfs_url =  $proxyhost.urlencode  (trim($perimeterDef->wfs_url));
			}else{
				$wfs_url ="";
			}
			
			?>
			wfsUrlWithFilter = '<?php echo $wfs_url ;?>' + '?request=GetFeature&SERVICE=WFS&TYPENAME=<?php echo $perimeterDef->feature_type_name; ?>&VERSION=1.0.0';
			wfsUrlWithFilter = wfsUrlWithFilter + '&FILTER=';
			wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Filter xmlns:ogc="http://www.opengis.net/ogc">');
			<?php
			if(count($rowsPerimeterValue) >1)
			{
				?>
				wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Or>');
				<?php	
			}
			foreach ( $rowsPerimeterValue as $value)
			{
				?>
				wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:PropertyIsEqualTo><ogc:PropertyName>' + '<?php echo $perimeterDef->id_field_name; ?>' +'</ogc:PropertyName><ogc:Literal>'+ '<?php echo $value->value; ?>' +'</ogc:Literal></ogc:PropertyIsEqualTo>');
				<?php 
			}
			if(count($rowsPerimeterValue) >1)
			{
				?>
				wfsUrlWithFilter = wfsUrlWithFilter + escape('</ogc:Or>');
				<?php	
			}
			?>
			wfsUrlWithFilter = wfsUrlWithFilter + escape('</ogc:Filter>');
			
			var wfs;
			//map.zoomToMaxExtent();
	     	wfs = new OpenLayers.Layer.Vector("selectedFeatures", {
                    strategies: [new OpenLayers.Strategy.Fixed()],
                    protocol: new OpenLayers.Protocol.HTTP({
                        url: wfsUrlWithFilter
                        ,
                        format: new OpenLayers.Format.GML()
                    })
                });		   	    	   
                    
				
			  wfs.events.register("featureadded", wfs, function() { map.zoomToExtent(OpenLayers.Layer.Vector.prototype.getDataExtent.apply(this));});
			  map.addLayer(wfs);
			
			<?php
		}
		?>
		
                                                            
}

var oldLoad = window.onload;
window.onload=function(){
initMap();
if (oldLoad) oldLoad();
}                       
</script>   
	
	<div id="map" class="tinymap"></div>
	
	<?php
}
	}
?>