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
	
	function listOrders($pageNav,$rows,$option,$orderstatus="",$ordertype="",$search="", $statusFilter="", $typeFilter="", $accountFilter="", $supplierFilter="", $productFilter="", $orderAccount="", $ordersupplier="", $orderproduct="",$ResponsDateFrom, $ResponsDateTo, $SendDateFrom, $SendDateTo)
	{
		JToolBarHelper::title( JText::_("SHOP_ORDER_LIST_ORDERS"), 'generic.png' );
		$database =& JFactory::getDBO();
		?>	
		<form action="index.php" method="GET" id="adminForm" name="adminForm">		
		<table width="100%" class="adminlist">
			<tr>
				<td align="left">
					<b><?php echo JText::_("SHOP_ORDER_FILTER");?></b>&nbsp;
					<input type="text" name="searchOrder" class="inputbox" value="<?php echo $search;?>" />			
				</td>
				<td>
				<select name="ordertype" >
				<option value=""><?php echo JText::_("SHOP_ORDER_FILTER_TYPES_LIST"); ?></option>
				 <?php  foreach($typeFilter as $type){ ?>
	              <option value="<?php echo $type->id;?>" <?php if ($ordertype==$type->id){?>selected="selected"<?php }?>>
					<?php echo JText::_($type->label); ?>
				  </option>
				 <?php } ?>
				</select>
				</td>
				<td>
				<select name="orderstatus" >
				 <option value=""><?php echo JText::_("SHOP_ORDER_FILTER_STATUS_LIST"); ?></option>
				 <?php  foreach($statusFilter as $stat){ ?>
	              <option value="<?php echo $stat->id;?>" <?php if ($orderstatus==$stat->id){?>selected="selected"<?php }?>>
					<?php echo JText::_($stat->label); ?>
				  </option>
				 <?php } ?>
				</select>				
				</td>
				<td>
				<select name="orderAccount" >
				 <option value=""><?php echo JText::_("SHOP_ORDER_FILTER_ACCOUNT_LIST"); ?></option>
				 <?php  foreach($accountFilter as $account){ ?>
	              <option value="<?php echo $account->account_id;?>" <?php if ($orderAccount==$account->account_id){?>selected="selected"<?php }?>>
					<?php echo JText::_($account->name); ?>
				  </option>
				 <?php } ?>
				</select>				
				</td>
				<td>
				<select name="ordersupplier" >
				 <option value=""><?php echo JText::_("SHOP_ORDER_FILTER_SUPPLIERS_LIST"); ?></option>
				 <?php  foreach($supplierFilter as $supplier){ ?>
	              <option value="<?php echo $supplier->account_id;?>" <?php if ($ordersupplier==$supplier->account_id){?>selected="selected"<?php }?>>
					<?php echo JText::_($supplier->name);?>
				  </option>
				 <?php } ?>
				</select>				
				</td>
				<td>
				<select name="orderproduct" >
				 <option value=""><?php echo JText::_("SHOP_ORDER_FILTER_PRODUCTS_LIST"); ?></option>
				 <?php  foreach($productFilter as $product){ ?>
	              <option value="<?php echo $product->id;?>" <?php if ($orderproduct==$product->id){?>selected="selected"<?php }?>>
					<?php echo JText::_($product->name); ?>
				  </option>
				 <?php } ?>
				</select>				
				</td>
				<!-- <td>
				<select name="orderarchived" >
					<option value=""><?php echo JText::_("SHOP_ORDER_CMD_STATUS_FILTER_ALL"); ?></option>
					<option value="1" <?php if($orderarchived=="1") echo "selected"; ?>><?php echo JText::_("SHOP_ORDER_CMD_ARCHIVED_FILTER_YES"); ?></option>
					<option value="0" <?php if($orderarchived=="0") echo "selected"; ?>><?php echo JText::_("SHOP_ORDER_CMD_ARCHIVED_FILTER_NO"); ?></option>
				</select>				
				</td>-->
				</tr>
				<tr>
					<td colspan=3>
						<b><?php echo JText::_( 'SHOP_ORDER_FILTER_DATE_SEND'); ?> : </b> 
						<?php JHTML::_('behavior.calendar'); ?>
						<b><?php echo JText::_( 'SHOP_ORDER_FILTER_DATE_FROM'); ?></b><?php echo JHTML::_('calendar',$SendDateFrom, "SendDateFrom","SendDateFrom","%d-%m-%Y"); ?>
						<b><?php echo JText::_( 'SHOP_ORDER_FILTER_DATE_TO'); ?></b><?php echo JHTML::_('calendar',$SendDateTo, "SendDateTo","SendDateTo","%d-%m-%Y"); ?>
					</td>
					<td colspan=3>
						<b><?php echo JText::_( 'SHOP_ORDER_FILTER_DATE_RESPONSE'); ?> : </b>
						<?php JHTML::_('behavior.calendar'); ?>
						<b><?php echo JText::_( 'SHOP_ORDER_FILTER_DATE_FROM'); ?></b><?php echo JHTML::_('calendar',$ResponsDateFrom, "ResponsDateFrom","ResponsDateFrom","%d-%m-%Y"); ?>
						<b><?php echo JText::_( 'SHOP_ORDER_FILTER_DATE_TO'); ?></b><?php echo JHTML::_('calendar',$ResponsDateTo, "ResponsDateTo","ResponsDateTo","%d-%m-%Y"); ?>
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
	<th class='title'><?php echo JText::_('SHOP_ORDER_LIST_NAME'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_ORDER_LIST_TYPE'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_ORDER_LIST_STATUS'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_ORDER_LIST_CREATIONDATE'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_ORDER_LIST_SENDDATE'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_ORDER_LIST_RESPONSEDATE'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_ORDER_LIST_ACCOUNT'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_ORDER_LIST_PRODUCT'); ?></th>
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
			<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
			<td><?php echo $row->id; ?></td>
			<td><span class="mdtitle" ><a class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->id?>" rel="{handler:'iframe',size:{x:600,y:600}}"> <?php echo $row->name; ?></a></span><br>
			<td><?php echo JText::_($row->type_translation) ;?></td>
			<td><?php echo JText::_($row->status_translation) ;?></td>
			<td><?php if ($row->created == "0000-00-00 00:00:00") echo ""; else echo date( JText::_("SHOP_ORDER_DATEFORMAT_DAY_MONTH_YEAR_HOUR_MINUTE"), strtotime($row->created));?></td>
			<td><?php if ($row->sent == "0000-00-00 00:00:00") echo ""; else echo date(JText::_("SHOP_ORDER_DATEFORMAT_DAY_MONTH_YEAR_HOUR_MINUTE"), strtotime($row->sent));?></td>
			<td><?php if ($row->response == "0000-00-00 00:00:00") echo ""; else echo date(JText::_("SHOP_ORDER_DATEFORMAT_DAY_MONTH_YEAR_HOUR_MINUTE"), strtotime($row->response));?></td>
			<?php 
				$query = "select u.name AS name 
				from #__users u , #__sdi_account a 
				inner join #__sdi_address ad on a.id = ad.account_id 
				WHERE a.user_id = ".$row->user_id ." and ad.type_id=1 and u.id = a.user_id" ;
				$database->setQuery($query);				 
		 	?>
		 	<td><?php echo $database->loadResult(); ?></td>
			<?php 
				$query = "SELECT p.name FROM #__sdi_order_product pl INNER JOIN #__sdi_product p ON pl.product_id=p.id WHERE pl.order_id=".$row->id ;
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
	
	function orderReportRecap ($id, $rowOrder, $perimeterRows, $user_name="", $third_name="" , $rowsProduct)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		
		?>
		 <link rel="stylesheet" href="<?php echo JURI::root(true)?>/templates/easysdi/css/easysdi.css" type="text/css" />
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
		<?php echo $user_name; ?>
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
		<?php HTMLadmin_cpanel::viewOrderRecapPerimeterExtent($rowOrder->id,$perimeterRows[0]->perimeter_id ,  $isInMemory); ?>
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
		
		$db->setQuery("select id from #__sdi_list_productstatus where code ='AVAILABLE'");
		$status_available_id = $db->loadResult();
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
			
				$queryPropertiesCode = "SELECT DISTINCT property_id 
											FROM #__sdi_order_property 
											WHERE orderproduct_id =$row->plId";
				$db->setQuery($queryPropertiesCode);
				$rowsPropertiesCode = $db->loadObjectList();
			
			
			
			foreach($rowsPropertiesCode as $rowPropertyCode)
			{
				
					$queryProductProperties = "SELECT * FROM #__sdi_order_property 
														where orderproduct_id =$row->plId 
														AND property_id = '$rowPropertyCode->property_id'";
					$db->setQuery($queryProductProperties);
					$rowsProductProperties = $db->loadObjectList();
				
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
						
						
							echo JText::_($rowProperty->translation);
						
						
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
	
	function viewOrderRecapPerimeterExtent($order_id, $perimeter_id, $isInMemory){

	?>
	<script
	type="text/javascript"
	src="components/com_easysdi_shop/lib/openlayers2.8/lib/OpenLayers.js"></script>
	
	<script
	type="text/javascript"
	src="components/com_easysdi_shop/lib/proj4js/lib/proj4js.js">
	
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