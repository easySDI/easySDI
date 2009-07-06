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
			<td><span class="mdtitle" ><a class="modal" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=orderReport&cid[]=<?php echo $row->order_id?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo $row->name; ?></a></span><br>
			
			<!-- <td><?php echo JText::_("EASYSDI_ORDER_TYPE_".$row->type) ;?></td> -->
			 <td><?php echo JText::_($row->type_translation) ;?></td>
			<td><?php echo JText::_($row->status_translation) ;?></td>
			<td><?php if ($row->orderDate == "0000-00-00 00:00:00") echo ""; else echo date("d-m-Y", strtotime($row->orderDate));?></td>
			<td><?php if ($row->responseDate == "0000-00-00 00:00:00") echo ""; else echo date("d-m-Y", strtotime($row->responseDate));?></td>
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
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listOrders">
		</form>
		
	<?php	
	}

	
	function orderReportRecap ($id,$isfrontEnd, $isForProvider )
	{
		global $mainframe;
		
		if($isForProvider == '')
		{
			$isForProvider == false;
		}
		
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$print = JRequest::getVar('print');
		
		$database =& JFactory::getDBO();
		
		//Get the current logged user
		$u = JFactory::getUser();
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($u->id);
		if($isfrontEnd == true)
		{
			//Check if a user is logged
			if ($u->guest)
			{
				$mainframe->enqueueMessage(JText::_("EASYSDI_ACCOUNT_NOT_CONNECTED"),"INFO");
				return;
			}
			if($isForProvider == false)
			{
				//Check the current user rights
				if(!userManager::hasRight($rootPartner->partner_id,"REQUEST_INTERNAL") &&
					!userManager::hasRight($rootPartner->partner_id,"REQUEST_EXTERNAL"))
				{
					$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE")." :  ".JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE_REQUEST"),"INFO");
					return;
				}
			}
		}
		
		$db =& JFactory::getDBO();
		
		$query = "SELECT *,  sl.translation as slT, tl.translation as tlT, a.name as order_name  FROM  #__easysdi_order a ,  #__easysdi_order_product_perimeters b, #__easysdi_order_status_list sl,#__easysdi_order_type_list tl where a.order_id = b.order_id and a.order_id = $id and tl.id = a.type and sl.id = a.status";
		$db->setQuery($query );

		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		//Customer name
		$user =$rows[0]->user_id;
		
		if($isfrontEnd == true && $isForProvider == false)
		{
			//Check if the current order belongs to the current logged user
			if($user != $u->id)
			{
				$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_ACCESS_ORDER_REPORT") ,"INFO");
				return;
			}
		}
		$queryUser = "SELECT name FROM #__users WHERE id = $user";
		$db->setQuery($queryUser );
		$user_name =  $db->loadResult();
		
		$third_name ='';
		//Third name
		$third = $rows[0]->third_party; 
		if( $third != 0)
		{
			$queryUser = "SELECT name FROM #__users WHERE id = $third";
			$db->setQuery($queryUser );
			$third_name =  $db->loadResult();
		}
		
		$query = '';
		if($isForProvider)
		{
			$query = "SELECT *, a.id as plId FROM #__easysdi_order_product_list  a, #__easysdi_product b where a.product_id  = b.id and order_id = $id and b.partner_id = $rootPartner->partner_id";
		}
		else
		{
			$query = "SELECT *, a.id as plId FROM #__easysdi_order_product_list  a, #__easysdi_product b where a.product_id  = b.id and order_id = $id";
		}
		
		$db->setQuery($query );
		$rowsProduct = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
		if(count($rowsProduct) == 0)
		{
			//The connected user does not have any product to provide in this order
			//Do not display any information and quit with error message
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_ACCESS_ORDER_REPORT") ,"INFO");
			return;
		}
		

		if ($print ==1 ){
			?>
			<script>window.print();</script> 
			<?php
		}
		
		
		?>
		<script type="text/javascript" src="./media/system/js/mootools.js"></script>
		<script>
		window.addEvent('domready', function() {
		$('printOrderRecap').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=<?php echo $option; ?>&task=<?php echo $task; ?>&cid[]=<?php echo $id; ?>&print=1','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
			});
		});
		</script>
		
		<table class="orderRecap" width="100%">
		<tr>
		<td colspan="2" width=100% >
		<div title ="Print" id="printOrderRecap"></div>
		</td>
		</tr>
		<tr>
		<td colspan="2" class="ortitle1" >
		<?php echo JText::_("EASYSDI_RECAP_ORDER_GTITLE"); ?>
		</td>
		</tr>
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
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_SENDDATE"); ?>
		</td>
		<td>
		<?php echo JText::_($rows[0]->RESPONSE_DATE); ?>
		</td>
		</tr>
		<tr>
		<td class="ortitle3">
		<?php echo JText::_("EASYSDI_RECAP_ORDER_STATUS"); ?>
		</td>
		<td>
		<?php echo JText::_($rows[0]->slT); ?>
		</td>
		</tr>
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
				<td width="90%"><?php echo $row->text; ?><br> [<?php echo $row->value;?>]</td>
				
			</tr>
			<?php }?>
		</table>
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
		<?php HTMLadmin_cpanel::viewOrderRecapPerimeterExtent($rows[0]->order_id,$rows[0]->perimeter_id , $isfrontEnd); ?>
		</td>
		</tr>
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
			<table >
				<tr>
					<td class="ornum"><?php echo ++$i; ?> - </td>
					<td class="ortitle3"><?php echo $row->data_title?><?php if ($row->is_free)  {echo " (".JText::_("EASYSDI_FREE_PRODUCT").")" ; }?></td>
							<?php
					if ($row->status == $status_id){
						$queryType = "select id from #__easysdi_order_type_list where code='O'";
						$db->setQuery($queryType);
						$type = $db->loadResult();
					
						if($rows[0]->type==$type){?>
			
					<td ><a target="RAW"
						href="./index.php?format=raw&option=<?php echo $option; ?>&task=downloadProduct&order_id=<?php echo $row->order_id?>&product_id=<?php echo $row->product_id?>">
						<?php echo JText::_("EASYSDI_DOWNLOAD_PRODUCT");?></a></td>
						<?php
						}
						
					}
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
			?>

		<?php }?>
		
		
		</table>

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
	src="./administrator/components/com_easysdi_core/common/lib/js/proj4js/proj4js-compressed.js">
	
	</script>
	<?php
	}
	else
	{ ?>
	<script
	type="text/javascript"
	src="./components/com_easysdi_core/common/lib/js/openlayers2.7/OpenLayers.js"></script>
	
	<script
	type="text/javascript"
	src="./components/com_easysdi_core/common/lib/js/proj4js/proj4js-compressed.js">
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
                    "<?php echo $row->url; ?>",
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
	     	wfs = new OpenLayers.Layer.Vector("selectedFeatures", {
                    strategies: [new OpenLayers.Strategy.Fixed()],
                    protocol: new OpenLayers.Protocol.HTTP({
                        url: wfsUrlWithFilter,
                        format: new OpenLayers.Format.GML()
                    })
                });		    	            
			 map.addLayer(wfs);	
			 
			 //Zoom to extent
			 var wFeatures = wfs.features;
			 map.zoomToMaxExtent();
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