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

//foreach($_POST as $key => $val) 
//echo '$_POST["'.$key.'"]='.$val.'<br />';


class HTML_product {

	function editProduct( $rowProduct, $current_manager_partner,$rowsAccount,$partners,$metadata_partner,$admin_partner,$diffusion_partner,$baseMaplist,$treatmentTypeList,$standardlist,$perimeterList,$selected_perimeter,$catalogUrlBase,$id, $option ){
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		//$product_partner = $current_manager_partner;
		JHTML::_('behavior.calendar');

					
//		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$rowProduct->metadata_id;
//		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		//$geoMD = new geoMetadata($cswResults ->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_Metadata"  )->item(0));
		//$geoMD = new geoMetadata($cswResults);				 
		
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_PRODUCT"), 'generic.png' );
		?>	
		<script>
		function displayAuthentication()
		{
			if (document.forms['adminForm'].service_type[0].checked)
			{
				document.getElementById('previewPassword').disabled = true;
				document.getElementById('previewPassword').value = "";
				document.getElementById('previewUser').disabled = true;
				document.getElementById('previewUser').value ="";
				document.getElementById('easysdi_account_id').disabled = false;
			}
			else
			{
				document.getElementById('previewPassword').disabled = false;
				document.getElementById('previewUser').disabled = false;
				document.getElementById('easysdi_account_id').disabled = true;
				document.getElementById('easysdi_account_id').value = '0';
			}
		}		
		</script>			
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"productrPane");
		?>		
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_EASYSDI_GENERIC_PRODUCT"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td ><?php echo JText::_("EASYSDI_PRODUCT_ID"); ?> : </td>
								<td><?php echo $rowProduct->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_METADATA_ID"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="metadata_id" value="<?php echo $rowProduct->metadata_id; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_UPDATE_DATE"); ?> : </td>						
								<?php $date = new JDate($rowProduct->update_date); ?>										
								<input type="hidden"  name="update_date" value="<?php echo $date->toMySQL(); ?>" />
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowProduct->update_date)); ?></td>								
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_CREATION_DATE"); ?> : </td>
								<?php $date = new JDate($rowProduct->creation_date); ?>
								<input type="hidden" name="creation_date" value="<?php echo $date->toMySQL(); ?>" />								
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowProduct->creation_date)); ?></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_SUPPLIER_NAME"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'editProduct\');"', 'value', 'text', $current_manager_partner ); ?></td>								
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_ADMIN_PARTNER_NAME"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$admin_partner, 'admin_partner_id', 'size="1" class="inputbox" ', 'value', 'text', $rowProduct->admin_partner_id  ); ?></td>								
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_METADATA_SUPPLIER_NAME"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$metadata_partner, 'metadata_partner_id', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->metadata_partner_id ); ?></td>								
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_DIFFUSION_PARTNER_NAME"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$diffusion_partner, 'diffusion_partner_id', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->diffusion_partner_id ); ?></td>								
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PRODUCT_NOTIFICATION_EMAIL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="500" name="notification_email" value="<?php echo $rowProduct->notification_email; ?>" /></td>								
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_SURFACE_MIN"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="surface_min" value="<?php echo $rowProduct->surface_min; ?>" /></td>							
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_SURFACE_MAX"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="surface_max" value="<?php echo $rowProduct->surface_max; ?>" /></td>							
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISHED"); ?> : </td>
								<td><select class="inputbox" name="published" >								
								<option value="0" <?php if( $rowProduct->published == 0 ) echo "selected"; ?> ><?php echo JText::_("EASYSDI_FALSE"); ?></option>
								<option value="1" <?php if( $rowProduct->published == 1 ) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option>
								</select></td>																
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_ORDERABLE"); ?> : </td>
								<td><select class="inputbox" name="orderable" >								
								<option value="0" <?php if( $rowProduct->orderable == 0 ) echo "selected"; ?> ><?php echo JText::_("EASYSDI_FALSE"); ?></option>
								<option value="1" <?php if( $rowProduct->orderable == 1 ) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option>
								</select></td>																
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PRODUCT_TREATMENT"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$treatmentTypeList, 'treatment_type', 'size="1" class="inputbox"', 'value',  'text', $rowProduct->treatment_type ); ?></td>															
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DATA_TITLE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="data_title" value="<?php echo $rowProduct->data_title; ?>" /></td>								
							</tr>		
							<tr>
								<td><?php echo JText::_("EASYSDI_DATA_INTERNAL"); ?> : </td>
								<td><input name="internal" value="1" type="checkbox" <?php if ($rowProduct->internal) {echo "checked";};?> > </td>								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DATA_EXTERNAL"); ?> : </td>
								<td><input name="external" value="1" type="checkbox" <?php if ($rowProduct->external) {echo "checked";};?> > </td>								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_METADATA_INTERNAL"); ?> : </td>
								<td><input name="metadata_internal" value="1" type="checkbox" <?php if ($rowProduct->metadata_internal) {echo "checked";};?> > </td>								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_METADATA_EXTERNAL"); ?> : </td>
								<td><input name="metadata_external" value="1" type="checkbox" <?php if ($rowProduct->metadata_external) {echo "checked";};?> > </td>								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DATA_FREE"); ?> : </td>
								<td><input name="is_free" value="1" type="checkbox" <?php if ($rowProduct->is_free) {echo "checked";};?> > </td>								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DATA_STANDARD_ID"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$standardlist, 'metadata_standard_id', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->metadata_standard_id ); ?></td>																
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_PREVIEW"),"productrPane");
		?>
		<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_PREVIEW"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">						
							<tr>
								<td><?php echo JText::_("EASYSDI_PREVIEW_BASE_MAP_ID"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$baseMaplist, 'previewBaseMapId', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->previewBaseMapId ); ?></td>																
							</tr>							
							<tr>
								<td><?php echo JText::_("EASYSDI_PREVIEW_WMS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewWmsUrl" value="<?php echo $rowProduct->previewWmsUrl; ?>" /></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PREVIEW_WMS_LAYERS"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewWmsLayers" value="<?php echo $rowProduct->previewWmsLayers; ?>" /></td>								
							</tr>							
							<tr>
								<td><?php echo JText::_("EASYSDI_PREVIEW_MIN_RESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewMinResolution" value="<?php echo $rowProduct->previewMinResolution; ?>" /></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PREVIEW_MAX_RESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewMaxResolution" value="<?php echo $rowProduct->previewMaxResolution; ?>" /></td>								
							</tr>			
							<tr>
								<td><?php echo JText::_("EASYSDI_PREVIEW_PROJECTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewProjection" value="<?php echo $rowProduct->previewProjection; ?>" /></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PREVIEW_UNIT"); ?> : </td>
								<td><select class="inputbox" name="previewUnit" >								
									<option <?php if($rowProduct->previewUnit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("EASYSDI_PREVIEW_METERS"); ?></option>
									<option <?php if($rowProduct->previewUnit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("EASYSDI_PREVIEW_DEGREES"); ?></option>
								</select>
								</td>																						
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PREVIEW_IMAGE_FORMAT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewImageFormat" value="<?php echo $rowProduct->previewImageFormat; ?>" /></td>								
							</tr>
							<tr>
							<td colspan ="3">
							<fieldset>
							<legend><?php echo JText::_("EASYSDI_BASE_MAP_AUTHENTICATION"); ?></legend>
								<table>
								<tr>
									<td >
										<input type="radio" name="service_type" value="via_proxy" onclick="javascript:displayAuthentication();" <?php if ($rowProduct->easysdi_account_id) echo "checked";?>>
									</td>
									<td colspan="2">
										<?php echo JText::_("EASYSDI_BASEMAP_VIA_PROXY"); ?>
									</td>
								</tr>
								<tr>
									<td></td>
									<td><?php echo JText::_("EASYSDI_BASEMAP_EASYSDI_ACCOUNT"); ?> : </td>
									<td><?php $enable = $rowProduct->easysdi_account_id? "" : "disabled"  ; echo JHTML::_("select.genericlist",$rowsAccount, 'easysdi_account_id', 'size="1" class="inputbox" onChange="" '.$enable , 'value', 'text',$rowProduct->easysdi_account_id); ?></td>
								</tr>
								<tr>
									<td >
									 	<input type="radio" name="service_type" value="direct" onclick="javascript:displayAuthentication();" <?php if ($rowProduct->previewUser) echo "checked";?>> 
								 	</td>
								 	<td colspan="2">
									 	 <?php echo JText::_("EASYSDI_BASEMAP_DIRECT"); ?>
								 	</td>
							 	</tr>
								<tr>
									<td></td>
									<td><?php echo JText::_("EASYSDI_BASEMAP_USER"); ?> : </td>
									<td><input <?php if (!$rowProduct->previewUser){echo "disabled";} ?> class="inputbox" type="text" size="50" maxlength="400" name="previewUser" id="previewUser" value="<?php echo $rowProduct->previewUser; ?>" /></td>							
								</tr>							
								<tr>
									<td></td>
									<td><?php echo JText::_("EASYSDI_BASEMAP_PASSWORD"); ?> : </td>
									<td><input <?php if (!$rowProduct->previewUser){echo "disabled";} ?> class="inputbox" type="password" size="50" maxlength="400" name="previewPassword" id="previewPassword" value="<?php echo $rowProduct->previewPassword; ?>" /></td>							
								</tr>
								
								</table>
							</fieldset>	
							</td>	
							</tr>										
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_PERIMETER"),"productrPane");
		?>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo $row->type_name ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JHTML::_("select.genericlist",$perimeterList, 'perimeter_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected_perimeter ); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>	
		<?php
		echo $tabs->endPanel();		
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_PERIMETER_BUFFER"),"productrPane");
		?>	
		<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_TEXT_PERIMETER_BUFFER_TITLE") ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
						<tr><th><?php echo JText::_("EASYSDI_PERIMETER_NAME") ?></th><th><?php echo JText::_("EASYSDI_PERIMETER_HAS_BUFFER") ?></th></tr>
						<?php 
							foreach ($perimeterList as $curPerim){
								$query = "SELECT * FROM #__easysdi_product_perimeter WHERE product_id=$rowProduct->id AND perimeter_id = $curPerim->value";				
								$database->setQuery( $query );
								$bufferRow = $database->loadObject() ;
								if ($database->getErrorNum()) 
								{						
									$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
								}										
						?>
						<tr>
							<td><?php  echo $curPerim->text; ?></td>
							<td><input type="checkbox" name="buffer[]" value="<?php  echo $curPerim->value ?>" <?php if ($bufferRow->isBufferAllowed == 1) echo "checked"?>></td>
						</tr>
						<?php 
							} 
						?>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php 
		echo $tabs->endPanel();			
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_PROPERTIES"),"productrPane");
		?>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo $row->type_name ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
				<?php
															
				$selected = array();
				$query = "SELECT property_value_id as value FROM #__easysdi_product_property WHERE product_id=".$rowProduct->id;				
				$database->setQuery( $query );	
				$selected = $database->loadObjectList();
				if ($database->getErrorNum()) {						
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
				}		

				$queryProperties = "SELECT b.id as property_id, 
										   b.translation as text,
										   type_code,
										   mandatory 
									FROM #__easysdi_product_properties_definition b 
									where published =1 
									AND (partner_id = 0 OR partner_id = $rowProduct->partner_id )
									order by b.order";
				$database->setQuery( $queryProperties );
				$propertiesList = $database->loadObjectList() ;
				if ($database->getErrorNum()) 
				{						
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
				}		
				HTML_product::alter_array_value_with_JTEXT_($propertiesList);
				
				foreach ($propertiesList as $curProperty){
						?><tr><?php echo $curProperty->text; ?></tr>
						<?php			
							$propertiesValueList = array();
							$query = "SELECT a.id as value, a.translation as text FROM #__easysdi_product_properties_values_definition a where a.properties_id =".$curProperty->property_id." order by a.order";				 
							$database->setQuery( $query );
							$propertiesValueList = $database->loadObjectList() ;
							HTML_product::alter_array_value_with_JTEXT_($propertiesValueList);
							
							if ($database->getErrorNum()) {						
									$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
								}
								switch($curProperty->type_code){
			
								case "list":
									?>
									<tr><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></tr>							
									<?php
									break;
									
								case "mlist":
									?>
									<tr><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></tr>
									<?php
									break;
								case "cbox":
									?>
									<tr><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></tr>
									<?php
									break;
									
								case "text":
									if ($curProperty->mandatory == 0 ){
										
									$propertiesValueList1[] = JHTML::_('select.option','-1', JText::_("EASYSDI_PROPERTY_NONE") );
									$propertiesValueList = array_merge( $propertiesValueList , $propertiesValueList1  );
										
									}
									?>
									<tr><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', '', 'value', 'text', $selected ); ?></tr>
									<?php
									break;
									
								case "textarea":
									if ($curProperty->mandatory == 0 ){
										
									$propertiesValueList2[] = JHTML::_('select.option','-1', JText::_("EASYSDI_PROPERTY_NONE") );
									$propertiesValueList = array_merge( $propertiesValueList , $propertiesValueList2  );
										
									}
									?>
									<tr><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="1" ', 'value', 'text', $selected ); ?></tr>
									<?php
									break;
								case "message":
									if ($curProperty->mandatory == 0 )
									{
										
										$propertiesValueList3[] = JHTML::_('select.option','-1', JText::_("EASYSDI_PROPERTY_NONE") );
										$propertiesValueList = array_merge( $propertiesValueList , $propertiesValueList3  );
										
									}
									?>
									<tr><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="1" ', 'value', 'text', $selected ); ?></tr>
									<?php
									break;
								}	
							?>	
						<?php } ?>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
		echo $tabs->endPanel();		
		echo $tabs->endPane();	
		?>
		<input type="hidden" name="id" value="<?php echo $rowProduct->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	function listProduct($use_pagination, $rows, $search,$pageNav, $option){
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_PRODUCT")); $partners = array(); ?>
		<form action="index.php" method="post" name="adminForm">
				<table width="100%">
					<tr>
						<td align="right">
							<b><?php echo JText::_("FILTER");?></b>&nbsp;
							<input size="50" type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton('listProduct');" />			
						</td>
					</tr>
				</table>
				<table width="100%">
					<tr>																																			
						<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listProduct\');"',$use_pagination); ?></td>
					</tr>
				</table>
				<table class="adminlist">
				<thead>
					<tr>					 			
						<th class='title'><?php echo JText::_("EASYSDI_PRODUCT_DEF"); ?></th>
						<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
						<th class='title'><?php echo JText::_("EASYSDI_PRODUCT_PUBLISHED"); ?></th>
						<th class='title'><?php echo JText::_("EASYSDI_PRODUCT_METADATA_ID"); ?></th>
						<th class='title'><?php echo JText::_("EASYSDI_PRODUCT_DATA_TITLE"); ?></th>
						<th class='title'><?php echo JText::_("EASYSDI_PRODUCT_SUPPLIER_NAME"); ?></th>
						<th class='title'><?php echo JText::_("EASYSDI_PRODUCT_CREATION_DATE"); ?></th>	
						<th class='title'><?php echo JText::_("EASYSDI_PRODUCT_TREATMENT"); ?></th>
					</tr>
				</thead>
				<tbody>		
		<?php
		$k = 0;
		$i=0;
		foreach ($rows as $row)
		{				  				
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td> <?php echo JHTML::_('grid.published',$row,$i); ?></td>
				<?php
				if ($row->hasMetadata == 1){
					$link =  "index.php?option=$option&amp;task=editProductMetadata2&cid[]=$row->id";					
				}else{					
					$link =  "index.php?option=$option&amp;task=editProductMetadata&cid[]=$row->id";
				}
				?>								
				<td><a href="<?php echo $link;?>"><?php echo $row->metadata_id; ?></a></td>
				<td><a href="<?php echo $link;?>"><?php echo $row->data_title; ?></a></td>																												
				<?php 
//				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
//				$database->setQuery($query);				 
		 		?>
				<td><?php 
//				echo $database->loadResult();
				echo $row->text; 
				?></td>								
				<td><?php echo date('d.m.Y H:i:s',strtotime($row->creation_date)); ?></td>
				<?php
				
				 ?>
				<td><?php echo JText::_($row->translation); ?></td>
			</tr>
			<?php
			$k = 1 - $k;
			$i ++;
		}
		?></tbody>
		<?php			
		if ($use_pagination == 1)
		{?>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
		<?php
		}
		?>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listProduct" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="publishedobject" value="product">
	  </form>
		<?php
	}	

	function alter_array_value_with_JTEXT_(&$rows)
	{		
		if (count($rows)>0)
		{
			foreach($rows as $key => $row)
			{		  	
      			$rows[$key]->text = JText::_($rows[$key]->text);
  			}			    
		}
	}
}
?>