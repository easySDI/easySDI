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

	
	function listOrders($pageNav,$rows,$option,$orderstatus="",$ordertype="",$search="", $statusFilter="", $typeFilter="", $redirectURL, $saved,$finish,$archived,$historized){
		global $mainframe;
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
	<h2 class="contentheading"><?php echo JText::_("SHOP_ORDER_LIST_ORDERS"); ?></h2>
		<div class="contentin">
		<form action="index.php" method="GET" id="ordersListForm" name="ordersListForm">
		<h3> <?php echo JText::_("SHOP_SEARCH_CRITERIA_TITLE"); ?></h3>
	
		<table width="100%">
			<tr>
				<td align="left"><b><?php echo JText::_("SHOP_SHOP_FILTER_TITLE");?></b>&nbsp;
				<td colspan="4" align="left">
				   <input type="text" id="searchOrder" name="searchOrder" class="inputbox" value="<?php echo $search;?>" />			
				</td>
			</tr>
			<tr>
				<td align="left">
					<?php echo JText::_("SHOP_SEARCH_CRITERIA_TITLE"); ?>
				</td>
				<td class="listOrdersContainer">
				<select name="ordertype" >
				<option value=""><?php echo JText::_("SHOP_ORDER_CMD_FILTER_ALL"); ?></option>
				 <?php  foreach($typeFilter as $type){ ?>
					 <option value="<?php echo $type->id;?>" <?php if ($ordertype==$type->id){?>selected="selected"<?php }?>>
					<?php echo JText::_($type->label); ?>
				  </option>
				 <?php } ?>
				</select>
				</td>
				<td class="listOrdersContainer">
				<select name="orderstatus" >
				 <option value=""><?php echo JText::_("SHOP_ORDER_CMD_STATUS_FILTER_ALL"); ?></option>
				 <?php  foreach($statusFilter as $stat){ ?>
					 <option value="<?php echo $stat->id;?>" <?php if ($orderstatus==$stat->id){?>selected="selected"<?php }?>>
					<?php echo JText::_($stat->label); ?>
				  </option>
				 <?php } ?>
				</select>				
				</td>
				<td align="right">
				<table>
					<tr>
						<td>
							<button type="submit" class="searchButton" > <?php echo JText::_("SHOP_SEARCH_BUTTON"); ?></button>
						</td>
						<td>
							<button id="newQuery" type="button" onClick="document.getElementById('ordersListForm').task.value=''; document.getElementById('ordersListForm').Itemid.value='<?php echo $redirectURL; ?>';document.getElementById('ordersListForm').view.value='shop';document.getElementById('ordersListForm').submit();" ><?php echo JText::_("SHOP_ORDER_NEW_QUERY"); ?></button>
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
				<td align="center"><?php echo JText::_("SHOP_ORDER_DISPLAY"); ?>
				<?php echo $pageNav->getLimitBox(); ?>
				</td>
				<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("SHOP_SEARCH_RESULTS_TITLE"); ?></h3>
	
	<?php
	$param=JRequest::getVar("param");

	JHTML::_("behavior.modal","a.modal",$param); 
	?>
	<?php
	if(count($rows) == 0){
		echo "<table><tbody><tr><td colspan=\"7\">".JText::_("SHOP_ORDER_NO_RESULT_FOUND")."</td>";
	}else{?>
	<table class="box-table" id="orderList" width="100%">
	<thead>
	<tr>
	<th align="center"><?php echo JText::_('CORE_ID'); ?></th>
	<th class="infoLogo"></th>
	<th class="logo"></th>
	<th class="reqDescr"><?php echo JText::_('SHOP_ORDER_LIST_NAME'); ?></th>
	<th align="center"><?php echo JText::_('SHOP_ORDER_STATUS'); ?></th>
	<th class="logo"></th>
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
			<td align="center"><?php echo $row->id; ?></td>
			<td align="center" class="infoLogo"><div class="<?php if($row->type_id == 1) echo"reqDevis"; if($row->type_id == 2) echo"reqOrder";  ?>" title="<?php echo JText::_($row->type_label) ;?>"></div></td>
			<td align="center" class="logo">
			<?php
			if(($row->sent == "0000-00-00 00:00:00" || $row->sent == null) && $row->responsesent == 0)
			{
				?>
				<div class="orderDate" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_DATE_CREATION")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->created));?>"> </div>
				<?php
			}
			else
			{
				if($row->responsesent)
				{
					
					?>
					<div class="orderDate" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_DATE_SEND")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->sent));?> - <?php echo JText::_("SHOP_ORDER_TOOLTIP_DATE_RECEIVE")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->response));?>" > </div>
					<?php 
				}
				else
				{
					?>
					<div class="orderDate" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_DATE_SEND")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->sent));?> " > </div>
					<?php
				}
			}
			
			?>
			</td>			
			<td class="reqDescr"><span class="mdtitle" >
				<a class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->id?>" rel="{handler:'iframe',size:{x:600,y:600}}"> <?php echo $row->name; ?>
				</a>
				</span><br></td>
			<td align="center"><?php echo JText::_($row->status_label) ;?></td>
			<?php
				//Draft
				if($saved == $row->status_id)
				{	
					?>
					<script>
					function orderDraft(){
						var type = <?php echo $row->type_id ;?>;
						if(type == 1){
							 if (confirm("<?php echo JText::_("SHOP_ORDER_DEVIS_TO_ORDER") ?>")) { 
								 document.getElementById('devis_to_order').value=1;							
							 }else{
								 document.getElementById('devis_to_order').value=0;
							 }
						}
							
						document.getElementById('order_id').value='<?php echo $row->id ;?>';
						document.getElementById('task<?php echo $option; ?>').value='orderDraft';
						document.getElementById('ordersListForm').submit();
					}
					</script>
					<td class="logo">
					<div class="savedOrderOrder" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_ORDER") ?>"
					onClick="orderDraft();"></div>
					</td>
					<td class="logo">
					<div class="particular-order-link">
					<a  title="<?php echo JText::_("SHOP_ORDER_VIEW_RECAP");?>" id="viewOrderLink<?php echo $i; ?>" rel="{handler:'iframe',size:{x:600,y:600}}" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->id?>" class="modal">&nbsp;</a>
					</div>
					</td>
					<td class="logo">
					<div class="savedOrderSuppress" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_SUPPRESS") ?>"
					onClick="
					if (confirm('<?php echo JText::_("SHOP_ORDER_SUPPRESS_CONFIRM_ACTION") ?>')){
						document.getElementById('order_id').value='<?php echo $row->id ;?>';
						document.getElementById('task<?php echo $option; ?>').value='suppressOrder';
						document.getElementById('ordersListForm').submit();
						return true;
					}
						return false;
					"></div>
					</td>
					<?php 
				}
				//finished
				else if($finish == $row->status_id)
				{
					?>
					<td><div class="noLogo"></td>
					<td class="logo">
					<div class="particular-order-link">
					<a  title="<?php echo JText::_("SHOP_ORDER_VIEW_RECAP");?>" id="viewOrderLink<?php echo $i; ?>" rel="{handler:'iframe',size:{x:600,y:600}}" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->id?>" class="modal">&nbsp;</a>
					</div>
					</td>
					<td class="logo">
					<div class="savedOrderCopy" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_COPY") ?>"
					onClick="document.getElementById('order_id').value='<?php echo $row->id ;?>';document.getElementById('task<?php echo $option; ?>').value='copyOrder';document.getElementById('ordersListForm').submit();"></div>
					</td>
					<td class="logo">
					<div class="savedOrderArchive" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_ARCHIVE") ?>"
					onClick="
					if (confirm('<?php echo JText::_("SHOP_ORDER_ARCHIVE_CONFIRM_ACTION") ?>')){
						document.getElementById('order_id').value='<?php echo $row->id ;?>';
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
					<a  title="<?php echo JText::_("SHOP_ORDER_VIEW_RECAP");?>" id="viewOrderLink<?php echo $i; ?>" rel="{handler:'iframe',size:{x:600,y:600}}" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->id?>" class="modal">&nbsp;</a>
					</div>
					</td>
					<?php 
					if($archived == $row->status_id || $historized == $row->status_id){
					?>
					<td class="logo">
					<div class="savedOrderCopy" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_COPY") ?>"
					onClick="document.getElementById('order_id').value='<?php echo $row->id ;?>';document.getElementById('task<?php echo $option; ?>').value='copyOrder';document.getElementById('ordersListForm').submit();"></div>
					</td>
					<?php 
					}else{ 
					?>
					<td class="logo"><div class="noLogo"></td>
					<?php
					}
					?>
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
			<input type="hidden" name="devis_to_order" id="devis_to_order" value="0">
			<input type="hidden" name="Itemid" id="Itemid" value="">
			<input type="hidden" name="view" id="view" value="">
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" name="limitstart" value="<?php echo JRequest::getVar("limitstart"); ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listOrders">
		</form>
		</div>
		</div>
	<?php	
	}
	
	function processOrder($rows,$option,$rowOrder,$third_party,$status,$partner,$product_id, $treatmentTranslation, $treatmentCode){
		?>
		<?php JHTML::_("behavior.modal","a.modal",$param); ?>
		<div id="page" class="processTreatmentPage">
		<h2 class="contentheading"><?php echo JText::_($rows[0]->type_translation)." : ".$rowOrder->name; ?></h2>
		<div class="contentin">
		<table class="orderInfo">
			<!-- client -->
			<tr>
				<td>
				<?php echo JText::_("SHOP_ORDER_PROCESS_ORDER_CLIENT") ;?> :
				</td>
				<td>
				<a title="<?php echo $partner->username; ?>" class="modal" href="./index.php?tmpl=component&option=com_easysdi_shop&toolbar=1&task=showSummaryForAccount&SummaryForId=<?php echo $partner->id ;?>" rel="{handler:'iframe',size:{x:565,y:450}}"><?php echo $partner->name; ?></a>
				</td>
			</tr>
			<!-- sent -->
			<tr>
				<td>
				<?php echo JText::_("SHOP_ORDER_TOOLTIP_DATE_SEND") ;?> :
				</td>
				<td>
				<?php 
				$date = $rows[0]->order_send_date;
				echo date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($date)) ;?>
				</td>
			</tr>
			<!-- Third party -->
			<tr>
				<td>
				<?php echo JText::_("SHOP_ORDER_RECAP_THIRD") ;?> :
				</td>
				<td>
				<?php 
				if($third_party == false){
					 echo JText::_("SHOP_ORDER_RECAP_NONE") ;
				}else{?>
					<a title="<?php echo $third_party->username; ?>" class="modal" href="./index.php?tmpl=component&option=com_easysdi_shop&toolbar=1&task=showSummaryForPartner&SummaryForId=<?php echo $third_party->id ;?>" rel="{handler:'iframe',size:{x:565,y:450}}"><?php echo $third_party->name; ?></a>
				<?php }?>
				</td>
			</tr>
			<!-- status -->
			<!--
			<tr>
				<td>
				<?php echo JText::_("SHOP_ORDER_RECAP_STATUS") ;?> :
				</td>
				<td>
				<?php echo JText::_($status); ?>
				</td>
			</tr>
			-->
			<!-- treatment type -->
			<?php if($treatmentTranslation){ ?>
			<tr>
				<td>
				<?php echo JText::_("SHOP_ORDER_PROCESS_ORDER_FILTER") ;?> : 
				</td>
				<td>
				<?php  echo JText::_("SHOP_ORDER_PROCESS_ORDER_FILTER_TREATMENT")." ".JText::_($treatmentTranslation) ;?>
				</td>
			</tr>
			<?php } ?>
			<?php if($treatmentCode == "AUTO"){ ?>
			<tr>
				<td colspan="2">
					<div class="alert">
						<?php echo JText::_("SHOP_ORDER_PROCESS_ORDER_AUTO_WARNING") ;?>
					</div>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<!-- recap -->
			<tr>
				<td>&nbsp;</td>
				<td><span class="mdviewfile"><a title="<?php echo $rowOrder->id.": ".$rowOrder->name; ?>" class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReportForProvider&cid[]=<?php echo $rowOrder->id?>" rel="{handler:'iframe',size:{x:600,y:600}}"><?php echo JText::_("SHOP_ORDER_VIEW_RECAP") ;?></a>
				</span></td>
			</tr>
		</table>
		<br/>
		<h3><?php echo JText::_("SHOP_ORDER_PROCESS_ORDER_SELECTED_PRODUCT") ; ?></h3>	
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
				<tbody>
				<tr>
				<td><?php echo JText::_("SHOP_ORDER_DATA")." ".$i?> : </td>
				<td><a class="modal" title="<?php echo JText::_("SHOP_PRODUCT_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_guid;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"><?php echo $row->data_title ;?></a></td>
				</tr>
				<tr><td><?php echo JText::_("SHOP_ORDER_PRICE") ;?></td><td> <input type="text" name="price<?php echo $row->product_id?>" id="price<?php echo $row->product_id?>" value=""></td></tr>
				<tr><td><?php echo JText::_("SHOP_ORDER_REM") ;?></td><td> <textarea rows="5" cols="30" name="remark<?php echo $row->product_id?>" id="remark<?php echo $row->product_id?>"></textarea></td></tr>
				<tr><td><?php echo JText::_("SHOP_ORDER_FILE") ;?></td><td> <input type="file" name="file<?php echo $row->product_id?>" id="file<?php echo $row->product_id?>" ></td></tr>
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
				<th class="orderInfoTitle"><?php echo JText::_("SHOP_ORDER_DATA")." ".$i?> : </th>
				<th><a class="modal" title="<?php echo JText::_("SHOP_PRODUCT_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_shop&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"><?php echo $row->data_title ;?></a></th>
				</tr>
				</thead>
				<tbody>
				<tr><td><?php echo JText::_("SHOP_ORDER_PRICE") ;?></td><td> <input type="text" name="price<?php echo $row->product_id?>" id="price<?php echo $row->product_id?>" value=""></td></tr>
				<tr><td><?php echo JText::_("SHOP_ORDER_REM") ;?></td><td> <textarea rows="5" cols="30" name="remark<?php echo $row->product_id?>" id="remark<?php echo $row->product_id?>"></textarea></td></tr>
				<tr><td><?php echo JText::_("SHOP_ORDER_FILE") ;?></td><td> <input type="file" name="file<?php echo $row->product_id?>" id="file<?php echo $row->product_id?>" ></td></tr>			
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
		<input type="hidden" id="order_id" name="order_id" value="<?php echo $rowOrder->id;?>">
		<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listOrdersForProvider">
		<input type="hidden" id="limit" name="limit" value="<?php echo JRequest::getVar("limit"); ?>">
		<input type="hidden" id="limitstart" name="limitstart" value="<?php echo JRequest::getVar("limitstart"); ?>">
		<script>
		function validateForm(){
						
			var aSel = $('processOrderForm').getElementsByTagName("input");
			var boolPrice = false;
			var pid = Array();
			var isSomethingTreated = false;
			var j = 0;
			for(var i=0;i<aSel.length;i++){
				if(aSel[i].name == "product_id[]"){
					pid[j] = aSel[i].value;
					j++;
				}
			}
			
			//Data coherence for each product
			for(var i=0;i<pid.length;i++){
				//if we have a remark or a file (or both), we then need a price
				if(document.getElementById('file'+pid[i]).value != "" || document.getElementById('remark'+pid[i]).value != ""){
					if(document.getElementById('price'+pid[i]).value == ""){			
					    boolPrice=true;
					}else{
					   //check for numeric value
					   if(isNaN(document.getElementById('price'+pid[i]).value)){
					      alert("<?php echo JText::_("SHOP_ORDER_PRICE_NOT_A_NUMBER"); ?>");
					      return false;
					   }
					}
						
				        isSomethingTreated = true;
				}
				
				//We can't have a price alone without rem or file
				if(document.getElementById('price'+pid[i]).value != "" && (document.getElementById('file'+pid[i]).value == "" && document.getElementById('remark'+pid[i]).value == "")){
					//check for numeric value
					if(isNaN(document.getElementById('price'+pid[i]).value)){
					    alert("<?php echo JText::_("SHOP_ORDER_PRICE_NOT_A_NUMBER"); ?>");
					    return false;
					}
					alert("<?php echo JText::_("SHOP_ORDER_NO_REM_NO_FILE"); ?>");
					return false;
				}
			}
			
			if(boolPrice){
				alert("<?php echo JText::_("SHOP_ORDER_HAS_PRICE_NULL"); ?>");
				return false;
			}else{
				if(!isSomethingTreated){
					alert("<?php echo JText::_("SHOP_ORDER_NO_PRODUCT_TREATED"); ?>");
					return false;
				}
				else
				{
				   document.getElementById('processOrderForm').task<?php echo $option; ?>.value='saveOrdersForProvider';
				   document.getElementById('processOrderForm').submit();
				   return true;
				}
			}
			
			
		}
		</script>
		<table width="100%">
		<tr><td align="right">
		<table>
			<tr>
				<td>
					<button type="button" onClick="return validateForm();" ><?php echo JText::_("SHOP_ORDER_PROCESS_ORDER"); ?></button>
				</td>
				<td>
					<button type="button" onClick="document.getElementById('processOrderForm').task<?php echo $option; ?>.value='listOrdersForProvider';document.getElementById('processOrderForm').submit();" ><?php echo JText::_("CORE_CANCEL"); ?></button>
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
		<h2 class="contentheading"><?php echo JText::_("SHOP_ORDER_LIST_ORDERS_FOR_PROVIDER"); ?></h2>
		<div class="contentin">
		<form action="index.php" method="GET" id="ordersListForm" name="ordersListForm">
	
		<h3> <?php echo JText::_("SHOP_SEARCH_CRITERIA_TITLE"); ?></h3>
	
		<table class="treatment" width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("SHOP_SHOP_FILTER_TITLE");?></b>&nbsp;
				</td>
				<td align="left" colspan="4">
					<input id="treatmentSearch" type="text" name="search" value="<?php echo $search;?>" class="inputbox"></input>			
				</td>
			</tr>
			<tr>
				<td align="left">
					<?php echo JText::_("SHOP_SEARCH_CRITERIA_TITLE"); ?>
				</td>
				<td>
					<select name="treatmentType" id="treatmentType" >
						<option value="-1"><?php echo JText::_("SHOP_ORDER_CMD_FILTER_ALL_TREATMENT"); ?></option>
						 <?php  foreach($treatmentList as $type){ ?>
			              <option value="<?php echo $type->id;?>" <?php if ($treatmentType==$type->id){?>selected="selected"<?php }?>>
							<?php echo JText::_($type->label); ?>
						  </option>
						 <?php } ?>
					</select>
				</td>
				<td>
					<select name="ordertype" id="ordertype" >
						<option value=""><?php echo JText::_("SHOP_ORDER_CMD_FILTER_ALL_TYPE"); ?></option>
						 <?php  foreach($productTypeFilter as $type){ ?>
			              <option value="<?php echo $type->id;?>" <?php if ($ordertype==$type->id){?>selected="selected"<?php }?>>
							<?php echo JText::_($type->label); ?>
						  </option>
						 <?php } ?>
					</select>
				</td>
				<td width = "125px">&nbsp;<!--
					<select name="orderStatus" id="orderStatus" >
						 <option value=""><?php echo JText::_("SHOP_ORDER_CMD_FILTER_ALL_STATUS"); ?></option>
						 <?php  foreach($productStatusFilter as $stat){ ?>
					         <option value="<?php echo $stat->id;?>" <?php if ($orderStatus==$stat->id){?>selected="selected"<?php }?>>
							<?php echo JText::_($stat->label); ?>
						  </option>
						 <?php } ?>
					</select>
				     -->
				</td>
				<td>
					<button type="submit" class="searchButton" > <?php echo JText::_("SHOP_SEARCH_BUTTON"); ?></button>
				</td>
			</tr>
		</table>
		
		<br/>
		<table width="100%">
			<tr>
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
				<td align="center"><?php echo JText::_("SHOP_ORDER_DISPLAY"); ?>
				<?php echo $pageNav->getLimitBox(); ?>
				</td>
				<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("SHOP_SEARCH_RESULTS_TITLE"); ?></h3>

	<?php JHTML::_("behavior.modal","a.modal",$param); ?>
	<?php
	if(count($rows) == 0){
		echo "<table><tbody><tr><td colspan=\"7\">".JText::_("SHOP_ORDER_NO_RESULT_FOUND")."</td>";
	}else{?>
	<table id="reqTreatTable" class="box-table">
	<thead>
	<tr>
	<th align="center"><?php echo JText::_('CORE_ID'); ?></th>
	<th class="infoLogo"></th>
	<th class="logo"></th>
	<th class="logo"></th>
	<th align="left"><?php echo JText::_('SHOP_ORDER_PRODUCT_NAME'); ?></th>
	<th align="center"><!--<?php echo JText::_('SHOP_ORDER_STATUS'); ?>--></th>
	<th class="logo"></th>
	</tr>
	</thead>
	<?php } ?>
	<tbody>
	<?php
		$i=0;
		foreach ($rows as $row)
		{	
			$i++;
			?>
			<tr>
				<td align="center" class="infoLogo"><a title="<?php echo $row->order_id.": ".$row->name; ?>" class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReportForProvider&cid[]=<?php echo $row->order_id?>" rel="{handler:'iframe',size:{x:600,y:600}}"> <?php echo $row->order_id; ?></a></td>
				<td align="center" class="infoLogo"><div class="<?php if($row->type == 1) echo"reqDevis"; if($row->type == 2) echo"reqOrder";  ?>" title="<?php echo JText::_($row->type_translation) ;?>"></div></td>
				<td align="center" class="logo">
				<?php
				if($row->order_send_date == "0000-00-00 00:00:00" && $row->RESPONSE_SEND == 0)
				{
					?>
					<div class="orderDate" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_DATE_CREATION")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->order_date));?>"> </div>
					<?php
				}
				else
				{
					if($row->RESPONSE_SEND)
					{
						?>
						<div class="orderDate" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_DATE_SEND")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->order_send_date));?> - <?php echo JText::_("SHOP_ORDER_TOOLTIP_DATE_RECEIVE")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->RESPONSE_DATE));?>" > </div>
						<?php 
					}
					else
					{
						?>
						<div class="orderDate" title="<?php echo JText::_("SHOP_ORDER_TOOLTIP_DATE_SEND")." : ".date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($row->order_send_date));?> " > </div>
						<?php
					}
				}
				?>
				</td>
				
				<td align="center" class="logo">
				<div class="<?php if($row->third_party != 0) echo 'particular-link-tierce'; else echo 'particular-link';?>">
				<a  title="<?php echo $row->username ;?>" id="partnerLink<?php echo $i; ?>" rel="{handler:'iframe',size:{x:565,y:450}}" href="./index.php?tmpl=component&option=com_easysdi_shop&toolbar=1&task=showSummaryForPartner&SummaryForId=<?php echo $row->client_id ;?>" class="modal">&nbsp;</a>
				</div>
				</td>
				<td align="left" id ="product_id">
					<a class="modal"
					title="<?php echo JText::_("SHOP_SHOP_VIEW_MD_FILE"); ?>"
					href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_guid;?>"
					rel="{handler:'iframe',size:{x:650,y:600}}"><?php echo $row->productName ;?>
					</a>
				</td>
				<td align="center"><!--<?php echo JText::_($row->status_translation) ;?>--></td>
				<td class="logo" align="center"><div title="<?php echo JText::_('SHOP_ORDER_PROCESS_ORDER'); ?>" class="treatRequest" id="treatReqButton" onClick="document.getElementById('product_list_id').value='<?php echo $row->product_list_id; ?>';document.getElementById('ordersListForm').task.value='processOrder'; document.getElementById('ordersListForm').submit();"/></td>
			</tr>
			<?php
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
			<input type="hidden" id="limitstart" name="limitstart" value="<?php echo JRequest::getVar("limitstart"); ?>">
		</form>
		</div>
		</div>
	<?php	
	}
	
	function orderReportRecap ($id,$isfrontEnd, $isForProvider,$rowOrder, $perimeterRows, $user_name="", $root_name="", $third_name="" , $rowsProduct, $isInMemory)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$print = JRequest::getVar('print');

		JHTML::_( 'behavior.mootools' );
		?>
		<script  type="text/javascript" >
		window.addEvent('domready', function() {
			$('printOrderRecap').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=<?php echo $option; ?>&task=<?php echo $task; ?>&cid[]=<?php echo $id; ?>&print=1','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
			});
		});
		</script>
		
		<div title ="<?php echo JText::_("SHOP_ORDER_PRINT"); ?>" id="printOrderRecap"></div>
		<div id="divOrderRecap">
		<h2 class="orderRecapTitle"><?php echo JText::_("SHOP_ORDER_RECAP_GENERAL_TITLE"); ?></h2>
		<table class="orderRecap" width="100%">
		<tr>
		<td colspan="2" class="ortitle2">
		<?php echo JText::_("SHOP_ORDER_RECAP_REQUEST"); ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("SHOP_ORDER_RECAP_ID"); ?>
		</td>
		<td>
		<?php echo $id == 0 ? JText::_("SHOP_ORDER_RECAP_NO_ID") : $id ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("SHOP_ORDER_RECAP_NAME"); ?>
		</td>
		<td>
		<?php echo $rowOrder->order_name; ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("SHOP_ORDER_RECAP_TYPE"); ?>
		</td>
		<td>
		<?php echo JText::_($rowOrder->tlT); ?>
		</td>
		</tr>
		<?php 
		if($rowOrder->sent == ""){
			?>
			<tr>
			<td class="ortitle3">
			<?php 
			echo JText::_("SHOP_ORDER_RECAP_CREATIONDATE"); ?>
			</td>
			<td>
			<?php echo "-" ?>
			</td>
			</tr>
			<?php 
		}
		else if($rowOrder->sent == "0000-00-00 00:00:00")
		{
			?>
			<tr>
			<td class="ortitle3">
			<?php 
			echo JText::_("SHOP_ORDER_RECAP_CREATIONDATE"); ?>
			</td>
			<td>
			<?php echo date(JText::_("SHOP_ORDER_DATEFORMAT_DAY_MONTH_YEAR_HOUR_MINUTE"), strtotime($rowOrder->created)); ?>
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
			echo JText::_("SHOP_ORDER_RECAP_SENDDATE"); ?>
			</td>
			<td>
			<?php echo date(JText::_("SHOP_ORDER_DATEFORMAT_DAY_MONTH_YEAR_HOUR_MINUTE"), strtotime($rowOrder->sent)); ?>
			</td>
			</tr>
			<tr>
			<td class="ortitle3">
			<?php
			echo JText::_("SHOP_ORDER_RECAP_RESPONSEDATE"); 
			?>
			</td>
			<?php 
			if($rowOrder->response != "0000-00-00 00:00:00" && $rowOrder->responsesent == 1)
			{
				?>
				<td>
				<?php echo date(JText::_("SHOP_ORDER_DATEFORMAT_DAY_MONTH_YEAR_HOUR_MINUTE"), strtotime($rowOrder->response)); ?>
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
		<?php echo JText::_("SHOP_ORDER_RECAP_STATUS"); ?>
		</td>
		<td>
		<?php echo $rowOrder->slT == "" ? "-" : JText::_($rowOrder->slT); ?>
		</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2" class="ortitle2">
		<?php echo JText::_("SHOP_ORDER_RECAP_CLIENT"); ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("SHOP_ORDER_RECAP_NAME"); ?>
		</td>
		<td>
		<?php 
		      if($root_name == "")
		          echo $user_name;
		      else
		          echo $user_name." (".trim($root_name).")";
	        ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("SHOP_ORDER_RECAP_THIRD"); ?>
		</td>
		<td>
		<?php echo $third_name; ?>
		</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2" class="ortitle2">
		<?php echo JText::_("SHOP_ORDER_RECAP_PERIMETER"); ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("SHOP_ORDER_RECAP_PERIMETER_TYPE"); ?>
		</td>
		<td>
		<?php
		$query = "SELECT * FROM  #__sdi_perimeter where id = ".$perimeterRows[0]->perimeter_id;
		$db->setQuery($query );
		$rowsPerimeter = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}
		if ($perimeterRows[0]->perimeter_id > 0){
			echo $rowsPerimeter[0]->description;
		}else
		{
			echo JText::_("SHOP_ORDER_GEOMETRY");

		} ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("SHOP_ORDER_RECAP_PERIMETER_CONTENT"); ?>
		</td>
		<td>
		<table width="100%">
		<?php
		$i=0;		
		foreach ($perimeterRows as $row){?>
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
		<?php echo JText::_("SHOP_ORDER_RECAP_SURFACE"); ?>
		</td>
		<td>
		<?php if($rowOrder->surface != 0)
		{
			echo ($rowOrder->surface)/1000000; 
			echo JText::_("SHOP_ORDER_RECAP_KM2") ; 
		}
		?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("SHOP_ORDER_RECAP_BUFFER"); ?>
		</td>
		<td>
		<?php if($rowOrder->buffer != 0)
		{
			echo $rowOrder->buffer; 
			echo JText::_("SHOP_ORDER_RECAP_METER") ; 
		} 
		else
		{
			echo JText::_("SHOP_ORDER_RECAP_NONE") ; 
		}
		?>
		</td>
		</tr>
		
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("SHOP_ORDER_RECAP_PREVIEW"); ?>
		</td>
		<td>
		<?php HTML_cpanel::viewOrderRecapPerimeterExtent($rowOrder->id,$perimeterRows[0]->perimeter_id , $isfrontEnd, $isInMemory); ?>
		</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
		<td colspan="2" class="ortitle2">
		<?php echo JText::_("SHOP_ORDER_ORDERED_PRODUCT_LIST"); ?>
		</td>
		</tr>
		
		<?php
		$i=0;
		$status_available_id = sdilist::getIdByCode('#__sdi_list_productstatus','AVAILABLE' );
		
		foreach ($rowsProduct as $row){ ?>
		<tr>
		   <td colspan="2" >
		     <fieldset class="orderRecapTreatment"><legend class="orderRecapTreatmentLegend"><?php echo $row->name?><?php if ($row->free)  {echo " (".JText::_("SHOP_PRODUCT_FREE").")" ; }?></legend>
			<table width="100%">
			<tr>
			<td colspan="2" >
			<table >
				<tr>
				</tr>
			</table>
			</td>
			</tr>
			
			<?php
			//Get product properties
			if(!$isInMemory)
			{
				$queryPropertiesCode = "SELECT DISTINCT property_id 
											FROM #__sdi_order_property 
											WHERE orderproduct_id =$row->plId";
				$db->setQuery($queryPropertiesCode);
				$rowsPropertiesCode = $db->loadObjectList();
			}
			else
			{
				$query = "SELECT DISTINCT a.property_id as property_id 
						  FROM #__sdi_product_property b, 
							   #__sdi_property  as a ,
							   #__sdi_propertyvalue as c  
						  WHERE a.id = c.property_id 
						  AND b.propertyvalue_id = c.id 
						  AND b.product_id = ". $row->id." order by a.ordering";
				$db->setQuery($query);
				$rowsPropertiesCode = $db->loadObjectList();
			}
			
			
			foreach($rowsPropertiesCode as $rowPropertyCode)
			{
				if(!$isInMemory)
				{
					$queryProductProperties = "SELECT * FROM #__sdi_order_property 
														where orderproduct_id =$row->plId 
														AND property_id = '$rowPropertyCode->property_id'";
					$db->setQuery($queryProductProperties);
					$rowsProductProperties = $db->loadObjectList();
				}
				else
				{
					$query = "SELECT b.propertyvalue_id as property_id, 
									 t.label as propertyvalue, 
									 a.property_id as property_id, 
									 a.type as type_code 
								FROM #__sdi_product_property b, 
									 #__sdi_property a ,
									 #__sdi_propertyvalue c, 
									  #__sdi_translation t
								WHERE a.id = c.property_id 
								AND b.propertyvalue_id = c.id 
								AND b.product_id = ". $row->id." 
								AND t.element_guid = c.guid
						 		AND a.property_id = '$rowPropertyCode->property_id' order by a.ordering";
					
					$db->setQuery( $query );
					$rowsProductProperties = $db->loadObjectList();
					
					
					//Filter out unselected options
					$temp = Array();
					foreach ($rowsProductProperties as $rowProductProperties)
					{	
						$propr = null;
						switch($rowProductProperties->type_code)
						{
							case "message":
								$temp[] = $rowProductProperties;
								break;
							case "list":
								$propr  = $mainframe->getUserState($rowPropertyCode->property_id."_list_property_".$row->id);
								if(count($propr)>0){
									if($propr[0] == $rowProductProperties->property_id)
										$temp[] = $rowProductProperties;
								}
								break;
							case "text":
								$temp[] = $rowProductProperties;
								break;
							case "textarea":
								$propr  = $mainframe->getUserState($rowPropertyCode->property_id."_textarea_property_".$row->id);
								$temp[] = $rowProductProperties;
								break;
							case "cbox":
								$propr  = $mainframe->getUserState($rowPropertyCode->property_id."_cbox_property_".$row->id);
								if(count($propr)>0){
									for($i = 0; $i < count($propr); $i++){
										if($propr[$i] == $rowProductProperties->property_id)
											$temp[] = $rowProductProperties;
									}
								}
								break;
							case "mlist":
								$propr  = $mainframe->getUserState($rowPropertyCode->property_id."_mlist_property_".$row->id);
								if(count($propr)>0){
									for($i = 0; $i < count($propr); $i++){
										if($propr[$i] == $rowProductProperties->property_id)
											$temp[] = $rowProductProperties;
									}
								}
								break;
								
						}
					}
					$rowsProductProperties = $temp;
				}
				?>
				<tr>
				<td class="ortitle4">
				<?php
						$queryProperty = "SELECT t.label as translation, 
												 p.type 
											FROM #__sdi_property p, #__sdi_translation t
											WHERE p.id = '$rowPropertyCode->property_id'
											AND t.element_guid = p.guid";
						$db->setQuery($queryProperty);
						$rowProperty = $db->loadObject();
						echo JText::_($rowProperty->translation);
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
					if($rowProductProperties->propertyvalue_id == 0)
					{	
						if($rowProperty->type == 'message')
						{
							echo JText::_($rowProductProperties->propertyvalue);
						}
						else
						{
							echo $rowProductProperties->propertyvalue;
						}
					}
					else
					{
						$queryProperty = "SELECT t.label as translation, 
												 a.type as type_code
											FROM #__sdi_product_property b,
												 #__sdi_property a ,
												 #__sdi_propertyvalue c,
												 #__sdi_translation t
											WHERE a.id = c.property_id 
											and b.propertyvalue_id = c.id 
											AND t.element_guid = c.guid
											and c.id = $rowProductProperties->propertyvalue_id";
						
						$db->setQuery($queryProperty);
						$rowProperty = $db->loadObject();
						
						if($isInMemory && $rowProperty->type == "text")
						{
							echo $mainframe->getUserState($rowPropertyCode->property_id."_text_property_".$row->id);
						}
						else if($isInMemory && $rowProperty->type == "textarea")
						{
							$prop = $mainframe->getUserState($rowPropertyCode->property_id."_textarea_property_".$row->id);
							for($i = 0; $i < count($propr); $i++)
								echo $prop[$i];
						}
						else
						{
							echo JText::_($rowProperty->translation);
						}
						
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
			
			if ($row->status_id == $status_available_id)
			{?>
				<tr>
				<td colspan=2>				
				<table class="orderRecapResultTable" width="100%">
				<tr>
				<td rowspan="3" class="orderRecapResult">
				</td>
				<td class="ortitle4">
				<?php echo JText::_("SHOP_ORDER_RECAP_PRICE"); ?>			
				</td>
				<td>
				<?php echo $row->price.JText::_("SHOP_ORDER_RECAP_MONEY"); ?>
				</td>
				</tr>
				
				<tr>
				<td class="ortitle4">
				<?php echo JText::_("SHOP_ORDER_RECAP_REM"); ?>			
				</td>
				<td>
				<?php echo $row->remark; ?>
				</td>
				</tr>
				
				<tr>
				<td class="ortitle4">
				<?php echo JText::_("SHOP_ORDER_RECAP_FILE")?>			
				</td>
				<?php
				$queryOrderStatus = "select l.code 
									 from #__sdi_list_orderstatus l, 
									 	  #__sdi_order o 
									 where l.id=o.status_id 
									 AND o.id=".$id;
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
					<?php echo JText::_("SHOP_ORDER_HISTORIZED_MESSAGE"); ?>
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
	
	function viewOrderRecapPerimeterExtent($order_id, $perimeter_id,$isfrontEnd, $isInMemory)
	{
	?>
	<script
	type="text/javascript"
	src="./administrator/components/com_easysdi_shop/lib/openlayers2.11/lib/OpenLayers.js"></script>
	
	<script
	type="text/javascript"
	src="./administrator/components/com_easysdi_shop/lib/proj4js/lib/proj4js.js">
	</script>
	<?php	
		
	global  $mainframe;
	$db =& JFactory::getDBO(); 
	$isFreeSelectionPerimeter = false;
	$queryPerimeter = "select * from #__sdi_perimeter where id = $perimeter_id";
	$db->setQuery($queryPerimeter);
	$perimeterDef = $db->loadObject();
	if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
	}	
	
	if($perimeterDef->urlwfs == '' && $perimeterDef->urlwms == '')
	{	
		$isFreeSelectionPerimeter = true;
	}
	
	$query = "select * from #__sdi_basemap where `default`=1"; 
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
<?php
	    //default style for manually drawed object and selected
	    if($rowsBaseMap->dfltfillcolor != '')
	    echo "OpenLayers.Feature.Vector.style['default']['fillColor'] = '".$rowsBaseMap->dfltfillcolor."';\n";
	    if($rowsBaseMap->dfltstrkcolor != '')
	    echo "OpenLayers.Feature.Vector.style['default']['strokeColor'] = '".$rowsBaseMap->dfltstrkcolor."';\n";
	    if($rowsBaseMap->dfltstrkwidth != '')
	    echo "OpenLayers.Feature.Vector.style['default']['strokeWidth'] = '".$rowsBaseMap->dfltstrkwidth."';\n";
	    
	    //style for polygon edition
	    if($rowsBaseMap->selectfillcolor != '')
	    echo "OpenLayers.Feature.Vector.style['select']['fillColor'] = '".$rowsBaseMap->selectfillcolor."';\n";
	    if($rowsBaseMap->selectstrkcolor != '')
	    echo "OpenLayers.Feature.Vector.style['select']['strokeColor'] = '".$rowsBaseMap->selectstrkcolor."';\n";
	    
	    //default style for object being drawn
	    if($rowsBaseMap->tempfillcolor != '')
	    echo "OpenLayers.Feature.Vector.style['temporary']['fillColor'] = '".$rowsBaseMap->tempfillcolor."';\n";
	    if($rowsBaseMap->tempstrkcolor != '')
	    echo "OpenLayers.Feature.Vector.style['temporary']['strokeColor'] = '".$rowsBaseMap->tempstrkcolor."';\n";
?>    
	    var options = {
	    projection: new OpenLayers.Projection("<?php echo $rowsBaseMap->projection; ?>"),
            displayProjection: new OpenLayers.Projection("<?php echo $rowsBaseMap->projection; ?>"),
            units: "<?php echo $rowsBaseMap->unit; ?>",
			<?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
            minScale: <?php echo $rowsBaseMap->minresolution; ?>,
            maxScale: <?php echo $rowsBaseMap->maxresolution; ?>,                
			<?php } ?>
            maxExtent: new OpenLayers.Bounds(<?php echo $rowsBaseMap->maxextent; ?>),
            controls: [] 
	    <?php
			if($rowsBaseMap->restrictedExtent == '1') echo  ",restrictedExtent: new OpenLayers.Bounds(".$rowsBaseMap->maxextent.")\n"
	    ?>
		<?php
			if($rowsBaseMap->restrictedscales != '') echo  ",scales: [".$rowsBaseMap->restrictedscales."]\n"
	    ?>
	};
	map = new OpenLayers.Map("map", options);
				  
	baseLayerVector = new OpenLayers.Layer.Vector("BackGround",{isBaseLayer: true,transparent: "true"}); 
	map.addLayer(baseLayerVector);
<?php
$print = JRequest::getVar('print');

$query = "select * from #__sdi_basemapcontent where basemap_id = ".$rowsBaseMap->id." order by ordering"; 
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
				  
		layer<?php echo $i; ?> = new OpenLayers.Layer.<?php echo $row->urltype; ?>( "<?php echo $row->name; ?>",
                    
			<?php 
			if ($row->user != null && strlen($row->user)>0){
				//if a user and password is requested then use the joomla proxy.
				$proxyhost = config_easysdi::getValue("SHOP_CONFIGURATION_PROXYHOST");
				$proxyhost = $proxyhost."&type=wms&basemapscontentid=$row->id&url=";
				echo "\"$proxyhost".urlencode  (trim($row->url))."\",";												
			}else{	
				//if no user and password then don't use any proxy.					
				echo "\"$row->url\",";	
			}					
			?>
			
                    {layers: '<?php echo $row->layers; ?>', format : "<?php echo $row->imgformat; ?>",transparent: "true"},                                          
                     {singleTile: <?php echo $row->singletile; ?>},                                                    
                     {     
                      maxExtent: new OpenLayers.Bounds(<?php echo $row->maxextent; ?>),
                   <?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
                      	minScale: <?php echo $row->minresolution; ?>,
                        maxScale: <?php echo $row->maxresolution; ?>,
                        <?php } ?>                 
                     projection:"<?php echo $row->projection; ?>",
                      units: "<?php echo $row->unit; ?>",
                      transparent: "true"
                     }
                    );
                    <?php
                    if (strtoupper($row->urltype) =="WMS")
                    {
                    	?>
                    	layer<?php echo $i; ?>.alpha = setAlpha('image/png');
                    	<?php
                    } 
                    ?>
		    layer<?php echo $i; ?>.events.register('loadend', this, layerloadend);
		    map.addLayer(layer<?php echo $i; ?>);
<?php 
$i++;
} ?>                   
		<?php
		//Add the command perimeter
		if(!$isInMemory){
			$queryPerimeterValue = "SELECT value FROM #__sdi_order_perimeter WHERE order_id = $order_id order by id";
			$db->setQuery( $queryPerimeterValue);
			$rowsPerimeterValue = $db->loadObjectList();
		}else{
			$selSurfaceListValue = $mainframe->getUserState('selectedSurfaces');
			$rowsPerimeterValue = Array();
			if ($selSurfaceListValue!=null){
				for ($i = 0; $i < count($selSurfaceListValue); $i ++){
					$rowsPerimeterValue[] = (object)array (  
						'value' => $selSurfaceListValue[$i]
					);
				}
			}
		}
		
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
			
			$proxyhostOrig = config_easysdi::getValue("SHOP_CONFIGURATION_PROXYHOST");
			
			$proxyhost = $proxyhostOrig."&type=wfs&perimeterdefid=$perimeterDef->id&url=";
				
			if ($perimeterDef->urlwfs!=null && strlen($perimeterDef->urlwfs)>0){
				$wfs_url =  $proxyhost.urlencode  (trim($perimeterDef->urlwfs));
			}else{
				$wfs_url ="";
			}
			
			?>
			wfsUrlWithFilter = '<?php echo $wfs_url ;?>' + '?request=GetFeature&SERVICE=WFS&TYPENAME=<?php echo $perimeterDef->featuretype; ?>&VERSION=1.0.0';
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
				wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:PropertyIsEqualTo><ogc:PropertyName>' + '<?php echo $perimeterDef->fieldid; ?>' +'</ogc:PropertyName><ogc:Literal>'+ '<?php echo $value->value; ?>' +'</ogc:Literal></ogc:PropertyIsEqualTo>');
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
//print if needed
var layersReady = <?php echo count($rows);?>;
var printWin = <?php if($print == 1) echo "true;\n"; else echo "false;\n"; ?>

window.onload=function(){
	initMap(); 
	if (oldLoad) oldLoad();
}

function layerloadend(){
	layersReady --;
	if(layersReady == 0 && printWin == true)
	       window.print();
}

</script>   
	
	<div id="map" class="tinymap"></div>
	
	<?php
}
	}
?>