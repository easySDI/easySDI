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
class HTML_product{
	
	function editProduct($account,$product,$version,$supplier,$id,$accounts,$object_id, $objecttype_id,$objecttype_list,$object_list,$version_list,$diffusion_list,$baseMap_list,$treatmentType_list,$visibility_list,$accessibility_list,$perimeter_list,$rowsAccount,$rowsUser,$userPreviewSelected,$userDownloadSelected,$grid_list, $option ){
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		if($account->root_id == ""){
			$account->root_id = 0;
		}
		
		JHTML::_('behavior.calendar');
		$tabs =& JPANE::getInstance('Tabs');
		?>
		<div id="page">
		<?php if($id)
		{ ?>
		<h2 class="contentheading"><?php echo JText::_("SHOP_PRODUCT_TITLE_EDIT"); ?></h2>
		<?php
		}
		else
		{ ?>
		<h2 class="contentheading"><?php echo JText::_("SHOP_PRODUCT_TITLE_NEW"); ?></h2>
		<?php
		} ?>
		<div class="contentin">
		<div class="editProductPane">
		<form enctype="multipart/form-data" action="index.php" method="post" name="productForm" id="productForm" class="productForm">
		<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("SHOP_TEXT_GENERAL"),"productPane");
		?>
		<br/>
		<script>
	   function toggle_state(obj){
	   	if(obj.checked){
			if(obj.name == 'external')
				$('data_internal').checked = true;
			if(obj.name == 'metadata_external')
				$('metadata_internal').checked = true;
		}
		else
		{
			if(obj.name == 'internal')
				$('data_external').checked = false;
			if(obj.name == 'metadata_internal')
				$('metadata_external').checked = false;
		}
	   }
	   function fieldManagement()
		{
			if (document.forms['productForm'].free.value == '0')
			{
				document.getElementById('productfile').disabled = true;
				document.getElementById('pathfile').disabled = true;
				document.getElementById('available').disabled = true;
				document.getElementById('available').value = '0';
				document.getElementById('deleteFileButton').disabled = true;
				document.getElementById('linkFile').value = true;
				document.getElementById('surfacemin').disabled = false;
				document.getElementById('surfacemax').disabled = false;
				document.getElementById('notification').disabled = false;
				document.getElementById('treatmenttype_id').disabled = false;
				document.getElementById('grid_id').disabled = true;
			}
			else if (document.forms['productForm'].free.value == '1' && document.forms['productForm'].available.value == '0')
			{
				document.getElementById('productfile').disabled = true;
				document.getElementById('pathfile').disabled = true;
				document.getElementById('available').disabled = false;
				document.getElementById('deleteFileButton').disabled = true;
				document.getElementById('linkFile').disabled = false;
				document.getElementById('surfacemin').disabled = false;
				document.getElementById('surfacemax').disabled = false;
				document.getElementById('notification').disabled = false;
				document.getElementById('treatmenttype_id').disabled = false;
				document.getElementById('grid_id').disabled = true;
			}
			else
			{
				document.getElementById('productfile').disabled = true;
				document.getElementById('pathfile').disabled = true;
				document.getElementById('available').disabled = false;
				document.getElementById('deleteFileButton').disabled = true;
				document.getElementById('linkFile').disabled = false;
				document.getElementById('surfacemin').disabled = true;
				document.getElementById('surfacemax').disabled = true;
				document.getElementById('notification').disabled = true;
				document.getElementById('treatmenttype_id').disabled = true;
				document.getElementById('grid_id').disabled = true;
			}
		}
	   function deleteFile (form){
			
			 if (confirm('<?php echo JText::_("SHOP_PRODUCT_MSG_CONFIRM_DELETE_FILE"); ?>')== true)
			 {
				 form.task.value='deleteProductFile';
				 form.submit();
			 }
		}
	   function ServiceFieldManagement(){
			if (document.forms['productForm'].viewurltype.value == 'WMS')
			{
				document.getElementById('viewminresolution').disabled = false;
				document.getElementById('viewmaxresolution').disabled = false;
				document.getElementById('viewmatrixset').disabled = true;
				document.getElementById('viewmatrixset').value = null;
				document.getElementById('viewmatrix').disabled = true;
				document.getElementById('viewmatrix').value = null;
			}else{
				document.getElementById('viewminresolution').disabled = true;
				document.getElementById('viewminresolution').value = null;
				document.getElementById('viewmaxresolution').disabled = true;
				document.getElementById('viewmaxresolution').value = null;
				document.getElementById('viewmatrixset').disabled = false;
				document.getElementById('viewmatrix').disabled = false;
			}
		}
	   function activateFileManagementOption (selected){
		   if(document.forms['productForm'].available.value == '0')
				return;
			switch (selected)
			{
				case "repository":
					document.getElementById('linkFile').style.visibility = "visible";
					document.getElementById('productfile').disabled = false;
					document.getElementById('deleteFileButton').disabled = false;

					document.getElementById('pathfile').value ='';
					document.getElementById('pathfile').disabled = true;

					document.getElementById('grid_id').value = null;
					document.getElementById('grid_id').disabled = true;
					break;
				case "link":
					document.getElementById('pathfile').value ='';
					document.getElementById('pathfile').disabled = false;

					document.getElementById('grid_id').value = null;
					document.getElementById('grid_id').disabled = true;

					document.getElementById('linkFile').style.visibility = "hidden";
					document.getElementById('productfile').disabled = true;
					document.getElementById('deleteFileButton').disabled = true;
					break;
				case "grid":
					document.getElementById('grid_id').value = '';
					document.getElementById('grid_id').disabled = false;
					
					document.getElementById('linkFile').style.visibility = "hidden";
					document.getElementById('productfile').disabled = true;
					document.getElementById('deleteFileButton').disabled = true;

					document.getElementById('pathfile').value ='';
					document.getElementById('pathfile').disabled = true;
					break;
			}
		}
		</script>
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $product->maxFileSize;?>000000">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("SHOP_GENERAL"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $product->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_OBJECT_TYPE"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$objecttype_list, 'objecttype_id', 'size="1" class="inputbox" onChange="var form = document.getElementById(\'productForm\');form.task.value=\'editProduct\';form.submit();"', 'value', 'text', $objecttype_id ); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_OBJECT"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$object_list, 'object_id', 'size="1" class="inputbox" onChange="var form = document.getElementById(\'productForm\');form.task.value=\'editProduct\';form.submit();"', 'value', 'text', $object_id ); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_VERSION"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$version_list, 'objectversion_id', 'size="1" class="inputbox" onChange="var form = document.getElementById(\'productForm\');form.task.value=\'editProduct\';form.submit();"', 'value', 'text', $version->id ); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_OBJECT_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $product->name; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $product->description; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_CREATED"); ?> : </td>
								<?php $date = new JDate($product->created); ?>
								<input type="hidden" name="created" value="<?php echo $date->toMySQL() ?>" />								
								<td><?php echo date('d.m.Y H:i:s',strtotime($product->created)); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_UPDATED"); ?> : </td>						
								<?php $date = new JDate($product->updated); ?>										
								<input type="hidden"  name="updated" value="<?php echo $date->toMySQL() ?>" />
								<td><?php echo date('d.m.Y H:i:s',strtotime($product->updated)); ?></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_PUBLISHED"); ?> : </td>
								<td><select class="inputbox" name="published" >								
								<option value="0" <?php if( $product->published == 0 ) echo "selected"; ?> ><?php echo JText::_("CORE_FALSE"); ?></option>
								<option value="1" <?php if( $product->published == 1 ) echo "selected"; ?>><?php echo JText::_("CORE_TRUE"); ?></option>
								</select></td>																
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_DIFFUSION"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>							
								<td class="ptitle"><?php echo JText::_("SHOP_DIFFUSION_NAME"); ?> : </td>
								<td colspan="2"><?php echo JHTML::_("select.genericlist",$diffusion_list, 'diffusion_id', 'size="1" class="inputbox"', 'value', 'text', $product->diffusion_id ); ?></td>								
							</tr>
							
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_VISIBILITY"); ?> : </td>
								<td colspan="2"><?php echo JHTML::_("select.genericlist",$visibility_list, 'visibility_id', 'size="1" class="inputbox"', 'value',  'text', $product->visibility_id ); ?></td>															
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_FREE"); ?> : </td>
								<td colspan="2">
									<select class="inputbox" name="free" id="free"  onChange="javascript:fieldManagement();">								
									<option value="0" <?php if( $product->free == 0 ) echo "selected"; ?> ><?php echo JText::_("CORE_NO"); ?></option>
									<option value="1" <?php if( $product->free == 1 ) echo "selected"; ?>><?php echo JText::_("CORE_YES"); ?></option>
									</select>
								</td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_AVAILABLE"); ?> : </td>
								<td  colspan="2">
									<select <?php if( $product->free == 0 ) echo "disabled"; ?> class="inputbox" name="available" id="available"  onChange="javascript:fieldManagement();">								
										<option value="0" <?php if( $product->available == 0 ) echo "selected"; ?> ><?php echo JText::_("CORE_NO"); ?></option>
										<option value="1" <?php if( $product->available == 1 ) echo "selected"; ?>><?php echo JText::_("CORE_YES"); ?></option>
									</select>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
				
			</tr>
			<tr>
				<td >
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_EXTRACTION"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_TREATMENT"); ?> : </td>
								<td colspan="2"><?php $disabled=''; if( $product->available == 1 ) $disabled='disabled'; echo JHTML::_("select.genericlist",$treatmentType_list, 'treatmenttype_id', 'size="1" class="inputbox" '.$disabled, 'value',  'text', $product->treatmenttype_id ); ?></td>															
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_SURFACE_MIN"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="surfacemin" id="surfacemin" <?php if( $product->available == 1 ) echo "disabled"; ?> value="<?php echo $product->surfacemin; ?>" /></td>							
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_SURFACE_MAX"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="surfacemax" id="surfacemax"  <?php if( $product->available == 1 ) echo "disabled"; ?> value="<?php echo $product->surfacemax; ?>" /></td>							
							</tr>
							<tr>							
								<td class="ptitle"><?php echo JText::_("SHOP_NOTIFICATION_EMAIL"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="500" name="notification"  id="notification" <?php if( $product->available == 1 ) echo "disabled"; ?> value="<?php echo $product->notification; ?>" /></td>								
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td >
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_DIFFUSION_MODE"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<?php if ($product->getFileName()) $repositorychecked = 'checked="checked"'; ?>
								<td><input type="radio" name="diffusion_mode" value="repository" onclick="javascript:activateFileManagementOption('repository');"  <?php echo $repositorychecked;?>></td>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_UP_FILE") ;?><br><?php printf( JText::_("SHOP_PRODUCT_FILE_MAX_SIZE"),$product->maxFileSize); ?> </td>
								<td >
									<input type="file" name="productfile" id="productfile" <?php if( $product->available == 0 || !$repositorychecked ) echo "disabled"; ?> >
								</td>
								<td>
									<a id="linkFile" target="RAW" href="./index.php?format=raw&option=<?php echo $option; ?>&task=downloadFinalProduct&product_id=<?php echo $product->id?>"><?php if( $product->available != 0 || $repositorychecked ) echo $product->getFileName();?> </a> 
								</td>
								<td>
									<button type="button" id="deleteFileButton" onCLick="deleteFile(document.getElementById('productForm'));" <?php if( $product->available == 0 || !$repositorychecked ) echo "disabled"; ?>><?php echo JText::_("SHOP_PRODUCT_DELETE_FILE"); ?></button>
								</td>
								
							</tr>
							<tr>
								<?php if ($product->pathfile) $linkchecked = 'checked="checked"'; ?>
								<td><input type="radio" name="diffusion_mode" value="link" onclick="javascript:activateFileManagementOption('link');" <?php echo $linkchecked;?> ></td>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_PATH_FILE") ;?></td>
								<td colspan="3"><input class="inputbox" type="text" size="50" maxlength="100" name="pathfile"  id="pathfile" <?php  if( $product->available == 0 || !$linkchecked) echo "disabled";?> value="<?php echo $product->pathfile; ?>" /></td>
							</tr>
							<tr>
								<?php if ($product->grid_id) $gridchecked = 'checked="checked"'; ?>
								<td><input type="radio" name="diffusion_mode" value="grid" onclick="javascript:activateFileManagementOption('grid');"  <?php echo $gridchecked;?> ></td>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_GRID_SELECTION") ;?></td>
								<td colspan="3">
									<?php
									 if( $product->available == 0 || !$gridchecked)
									 	$griddisabled = 'disabled';
									 echo JHTML::_("select.genericlist",$grid_list, 'grid_id', 'size="1" class="inputbox" '.$griddisabled, 'value',  'text', $product->grid_id ); 
									?>
								</td>
							</tr>
						</table>
					</fieldset>						
				</td>
			</tr>
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_DIFFUSION_RIGHTS"); ?></legend>
						<table  border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_DOWNLOAD_ACCESSIBILITY"); ?> : </td>
								<td ><?php echo JHTML::_("select.genericlist",$accessibility_list, 'loadaccessibility_id', 'size="1" class="inputbox" onChange="javascript:accessibilityEnable(\'loadaccessibility_id\',\'userDownloadList\');"', 'value',  'text', $product->loadaccessibility_id ); ?></td>															
							</tr>			
							<tr>
								<td class="ptitle"><?php echo JText::_( 'SHOP_PRODUCT_DOWNLOAD_ACCESSIBILITY_USER'); ?> </td>
								<td ><?php
								
									if ($product->loadaccessibility_id != 0 || $product->loadaccessibility_id != "" || $product->loadaccessibility_id != null )  {$disabled = 'disabled';} else {$disabled = '';};
								 echo JHTML::_("select.genericlist",$rowsUser, 'userDownloadList[]', 'size="15" multiple="true" class="selectbox" '.$disabled, 'value', 'text', $userDownloadSelected ); ?></td>
							</tr>
						</table>
						
					</fieldset>	
				</td>
			</tr>
			
		</table>
		
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("SHOP_PRODUCT_PERIMETER"),"productPane");
		?>
		<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_PERIMETER") ?></legend>
						<table width="100%">
							<tr>
								<td width="100%">
								<script>
								   function productAvailability_change(obj, id){
								   if(obj.checked){
										$('buffer_'+id).disabled = false;
									}
									else
									{
										$('buffer_'+id).checked = false;
										$('buffer_'+id).disabled = true;
									}
								   }
								</script>
								<table class="box-table">
								<thead>
								<tr>
									<th align="left"><?php echo JText::_("SHOP_PRODUCT_PERIMETER_NAME") ?></th>
									<th align="center"><?php echo JText::_("SHOP_PRODUCT_PERIMETER_AVAILABILITY") ?></th>
									<th align="center"><?php echo JText::_("SHOP_PRODUCT_PERIMETER_HAS_BUFFER") ?></th>
								</tr>
								</thead>
								<tbody>
								   <?php 
							          foreach ($perimeter_list as $curPerim){
									  $query = "SELECT * FROM #__sdi_product_perimeter WHERE product_id=$product->id AND perimeter_id = $curPerim->value";				
									  $database->setQuery( $query );
									  $bufferRow = $database->loadObject() ;
									  if ($database->getErrorNum()) {						
										$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
									  }
									?>
								  <tr>
								  	<td align="left"><?php  echo $curPerim->text; ?></td>
									<td align="center"><input type="checkbox" id="perimeter_<?php echo $curPerim->value;?>" name="perimeter_id[]" value="<?php  echo $curPerim->value ?>" <?php if ($bufferRow->product_id != "") echo "checked"?> onclick="productAvailability_change(this,<?php echo $curPerim->value;?>);"></td>
									<td align="center"><input type="checkbox" id="buffer_<?php echo $curPerim->value;?>" name="buffer[]" value="<?php  echo $curPerim->value ?>" <?php if ($bufferRow->buffer == 1) echo "checked"; else if ($bufferRow->product_id == "") echo "disabled";?>></td>
								</tr>
								<?php } ?>
								</tbody>
							       </table>
							       </td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("SHOP_PRODUCT_PROPERTIES"),"productPane");
		?>
		<br>
		<table class="ProductPropreties">
			<tr>
				<td>
				<table border="0" cellpadding="3" cellspacing="0">
				<?php
				$selected = array();
				$query = "SELECT propertyvalue_id as value FROM #__sdi_product_property WHERE product_id=".$product->id;				
				$database->setQuery( $query );	
				$selected = $database->loadObjectList();
				if ($database->getErrorNum()) {						
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
				}		
				$language =& JFactory::getLanguage();
				$condition="";
				if($supplier->id)
				{
					$condition = " OR p.account_id = $supplier->id ";
				}
	
				$queryProperties = "SELECT p.id as property_id, 
										   t.label as text,
										   p.type as type,
										   p.mandatory as mandatory 
									FROM #__sdi_language l, 
										#__sdi_list_codelang cl,
										#__sdi_property p 
										LEFT OUTER JOIN #__sdi_translation t ON p.guid=t.element_guid 
									WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."' 
									AND p.published =1 
									AND (p.account_id = 0 $condition)
									order by p.ordering";
				$database->setQuery( $queryProperties );
				$propertiesList = $database->loadObjectList() ;
				if ($database->getErrorNum()) 
				{						
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
				}			
				foreach ($propertiesList as $curProperty)
				{
					?>
					<!--<tr><td><?php echo $curProperty->text; ?></td></tr> -->
					<?php
					$propertiesValueList = array();
					$query = "SELECT a.id as value, t.label as text 
								FROM 
									#__sdi_list_codelang cl,
									#__sdi_language l,
									#__sdi_propertyvalue a 
								LEFT OUTER JOIN #__sdi_translation t ON a.guid=t.element_guid 
							WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."' 
								AND a.property_id =".$curProperty->property_id." 
								order by a.ordering";				 
					$database->setQuery( $query );
					$propertiesValueList = $database->loadObjectList() ;
					if ($database->getErrorNum()) 
					{						
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
					}							
					switch($curProperty->type)
					{
						case "list":
							?>
							<tr><td>
							<fieldset class="fieldset_properties">
							<legend><?php echo $curProperty->text; ?></legend>
							<?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', 'size="'.count($propertiesValueList).'" multiple="true" class="inputbox"', 'value', 'text', $selected ); ?>
							</fieldset>
							</td></tr>
							<?php
							break;						
						case "mlist":
							?>
							<tr><td>
							<fieldset class="fieldset_properties">
							<legend><?php echo $curProperty->text; ?></legend>
							<?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', 'size="'.count($propertiesValueList).'" multiple="true" class="inputbox"', 'value', 'text', $selected ); ?>
							</fieldset>
							</td></tr>
							<?php
							break;
						case "cbox":
							?>
							<tr><td>
							<fieldset class="fieldset_properties">
							<legend><?php echo $curProperty->text; ?></legend>
							<?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', 'size="'.count($propertiesValueList).'" multiple="true" class="inputbox"', 'value', 'text', $selected ); ?>
							</fieldset>
							</td></tr>
							<?php
							break;
							
						case "text":
						if ($curProperty->mandatory == 0 ){
							$propertiesTemp1[] = JHTML::_('select.option','-1', JText::_("SHOP_PROPERTY_NONE"));
							$propertiesTemp2[] = JHTML::_('select.option',$propertiesValueList[0]->value, JText::_("SHOP_PROPERTY_YES"));
							$propertiesValueList = array_merge( $propertiesTemp1 , $propertiesTemp2);
							}
							?>
							<tr><td>
							<fieldset class="fieldset_properties">
							<legend><?php echo $curProperty->text; ?></legend>
							<?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', 'class="inputbox"', 'value', 'text', $selected ); ?>
							</fieldset>
							</td></tr>
							<?php
							break;
							
						case "textarea":
						if ($curProperty->mandatory == 0 ){
							$propertiesValueList2[] = JHTML::_('select.option','-1', JText::_("SHOP_PROPERTY_NONE"));
							$propertiesValueList3[] = JHTML::_('select.option',$propertiesValueList[0]->value, JText::_("SHOP_PROPERTY_YES"));
							
							$propertiesValueList = array_merge( $propertiesValueList2 , $propertiesValueList3);
								
							}
							?>
							<tr><td>
							<fieldset class="fieldset_properties">
							<legend><?php echo $curProperty->text; ?></legend>
							<?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', 'class="inputbox" size="1" ', 'value', 'text', $selected ); ?>
							</fieldset>
							</td></tr>
							<?php
							break;
						case "message":
							?>
							<script>
							   var arrMsgs = new Array();
							   <?php
							   foreach ($propertiesValueList as $key => $value) {
								echo "arrMsgs[$value->value] = \"".addslashes($value->text)."\";\n";
							   }
							   ?>
							   function messageContents_change(obj){
							        $('messageContents').innerHTML=arrMsgs[obj.options[obj.selectedIndex].value];
							   }
							</script>
							<?php
							$query = "SELECT a.id as value, a.text as text FROM #__sdi_product_propertyvalue a where a.property_id =".$curProperty->property_id." order by a.ordering";				 
							$database->setQuery( $query );
							$res = $database->loadObjectList() ;
							if ($database->getErrorNum()) 
							{
								$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
							}
							if ($curProperty->mandatory == 0 )
							{
								$propertiesValueList3[] = JHTML::_('select.option','-1', JText::_("SHOP_PROPERTY_NONE") );
								$propertiesValueList = array_merge( $res , $propertiesValueList3  );
							}
							?>
							<tr><td>
							<fieldset class="fieldset_properties">
							<legend><?php echo $curProperty->text; ?></legend>
							<table>
							<tr><td>
							<?php echo JHTML::_("select.genericlist",$res, 'property_id[]', 'class="inputbox" size="3" onchange="messageContents_change(this);"', 'value', 'text', $selected ); ?>
							</td></tr>
							<tr><td id="messageContents">
							</td></tr>
							</table>
							</fieldset>
							<?php
							break;
						}	
					 } ?>		
					</table>
				</td>
			</tr>
		</table>
		<?php
		echo $tabs->endPanel();		
		echo $tabs->startPanel(JText::_("SHOP_PRODUCT_PREVIEW"),"productPane");
		?>
		<br>
		<script>
		function displayAuthentication()
		{
			if (document.forms['productForm'].service_type[0].checked)
			{
				document.getElementById('viewpassword').disabled = true;
				document.getElementById('viewpassword').value = "";
				document.getElementById('viewuser').disabled = true;
				document.getElementById('viewuser').value ="";
				document.getElementById('viewaccount_id').disabled = false;
			}
			else
			{
				document.getElementById('viewpassword').disabled = false;
				document.getElementById('viewuser').disabled = false;
				document.getElementById('viewaccount_id').disabled = true;
				document.getElementById('viewaccount_id').value = '0';
			}
		}	
		function accessibilityEnable(choice,list)
		{
			var form = document.productForm;
			if (form.elements[choice].value=='0')
			{
				form.elements[list].disabled=false;
			}
			else
			{
				form.elements[list].disabled=true;
				for (i = form.elements[list].length - 1; i>=0; i--) 
				{
					form.elements[list].options[i].selected = false;
				}
			}
		}
		
		</script>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_PREVIEW_RIGHTS"); ?></legend>
						<table  border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_PREVIEW_ACCESSIBILITY"); ?> : </td>
								<td ><?php echo JHTML::_("select.genericlist",$accessibility_list, 'viewaccessibility_id', 'size="1" class="inputbox"  onChange="javascript:accessibilityEnable(\'viewaccessibility_id\',\'userPreviewList\');"', 'value',  'text', $product->viewaccessibility_id ); ?></td>
																							
							</tr>			
							<tr>
								<td class="ptitle"><?php echo JText::_( 'SHOP_PRODUCT_PREVIEW_ACCESSIBILITY_USER'); ?> </td>
								<td ><?php
									if ($product->viewaccessibility_id != 0 || $product->viewaccessibility_id != "" || $product->viewaccessibility_id != null)   {$disabled = 'disabled';} else {$disabled = '';};
								 echo JHTML::_("select.genericlist",$rowsUser, 'userPreviewList[]', 'size="15" multiple="true" class="selectbox" '.$disabled, 'value', 'text', $userPreviewSelected ); ?></td>
							</tr>
						</table>
					</fieldset>	
				</td>
			</tr>
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_PREVIEW"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">						
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PREVIEW_BASEMAP"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$baseMap_list, 'viewbasemap_id', 'size="1" class="inputbox"', 'value', 'text', $product->viewbasemap_id ); ?></td>																
							</tr>		
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PREVIEW_URL_TYPE"); ?> : </td>
								<td><select class="inputbox" name="viewurltype" id="viewurltype" onChange="javascript:ServiceFieldManagement();">								
									<option <?php if($product->viewurltype == 'WMS') echo "selected" ; ?> value="WMS"> WMS</option>
									<option <?php if($product->viewurltype == 'WMTS') echo "selected" ; ?> value="WMTS"> WMTS</option>
								</select>
								</td>								
							</tr>						
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PREVIEW_WMS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewurlwms" value="<?php echo $product->viewurlwms; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PREVIEW_LAYERS"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewlayers" value="<?php echo $product->viewlayers; ?>" /></td>								
							</tr>							
							
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_IMG_FORMAT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewimgformat" value="<?php echo $product->viewimgformat; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PROJECTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewprojection" value="<?php echo $product->viewprojection; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_UNIT"); ?> : </td>
								<td><select class="inputbox" name="previewUnit" >								
									<option <?php if($product->viewunit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("SHOP_METERS"); ?></option>
									<option <?php if($product->viewunit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("SHOP_DEGREES"); ?></option>
								</select>
								</td>																						
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PREVIEW_STYLE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewstyle" id="viewstyle" value="<?php echo $product->viewstyle; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_MAXEXTENT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewextent" id="viewextent" value="<?php echo $product->viewextent; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_MINSCALE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewminresolution" id="viewminresolution" value="<?php echo $product->viewminresolution; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_MAXSCALE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewmaxresolution" id="viewmaxresolution" value="<?php echo $product->viewmaxresolution; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PREVIEW_MATRIXSET"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewmatrixset" id="viewmatrixset" value="<?php echo $product->viewmatrixset; ?>" <?php if ($product->viewurltype != 'WMTS') echo 'disabled'; ?> /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PREVIEW_MATRIX"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewmatrix" id="viewmatrix" value="<?php echo $product->viewmatrix; ?>" <?php if ($product->viewurltype != 'WMTS') echo 'disabled'; ?>/></td>								
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td >
				<fieldset class="fieldset_properties">
				<legend><?php echo JText::_("SHOP_AUTHENTICATION"); ?></legend>
					<table border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td >
							<input type="radio" name="service_type" value="via_proxy" onclick="javascript:displayAuthentication();" <?php if ($product->viewaccount_id) echo "checked";?>>
						</td>
						<td class="ptitle" colspan="2">
							<?php echo JText::_("SHOP_AUTH_VIA_PROXY"); ?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><?php echo JText::_("SHOP_EASYSDI_ACCOUNT"); ?> : </td>
						<td><?php $enable = $product->viewaccount_id? "" : "disabled"  ; echo JHTML::_("select.genericlist",$rowsAccount, 'viewaccount_id', 'size="1" class="inputbox" onChange="" '.$enable , 'value', 'text',$product->viewaccount_id); ?></td>
					</tr>
					<tr>
						<td >
						 	<input type="radio" name="service_type" value="direct" onclick="javascript:displayAuthentication();" <?php if ($product->previewUser) echo "checked";?>> 
					 	</td>
					 	<td class="ptitle" colspan="2">
						 	 <?php echo JText::_("SHOP_AUTH_DIRECT"); ?>
					 	</td>
				 	</tr>
					<tr>
						<td></td>
						<td><?php echo JText::_("SHOP_AUTH_USER"); ?> : </td>
						<td><input <?php if (!$product->viewuser){echo "disabled";} ?> class="inputbox" type="text" size="50" maxlength="400" name="viewuser" id="viewuser" value="<?php echo $product->viewuser; ?>" /></td>							
					</tr>							
					<tr>
						<td></td>
						<td><?php echo JText::_("SHOP_AUTH_PASSWORD"); ?> : </td>
						<td><input <?php if (!$product->viewuser){echo "disabled";} ?> class="inputbox" type="password" size="50" maxlength="400" name="viewpassword" id="viewpassword" value="<?php echo $product->viewpassword; ?>" /></td>							
					</tr>
					
					</table>
				</fieldset>	
				</td>	
			</tr>
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();	
		?>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" id="task" name="task" value="cancelEditProduct">
		<input type="hidden" id="limit" name="limit" value="<?php echo JRequest::getVar("limit"); ?>">
		<input type="hidden" id="limitstart" name="limitstart" value="<?php echo JRequest::getVar("limitstart"); ?>">
		<input type="hidden" name="supplier_id" value="<?php echo $supplier->id; ?>" />
		<input type="hidden" name="manager_id" value="<?php echo $account->id; ?>" />
		<input type="hidden" name="createdby" value="<?php echo $product->createdby; ?>" />
		<input type="hidden" name="checked_out" value="<?php echo $product->checked_out; ?>" />
		<input type="hidden" name="checked_out_time" value="<?php echo $product->checked_out_time; ?>" />
		<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
		<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
		<input type="hidden" id="productFileName" name="productFileName" value="<?php echo $product->getFileName();?>">
		</form>
		</div>
		<div class="row">
			<input type="submit" id="save_product" name="save_product" class="submit" value ="<?php echo JText::_("CORE_SAVE"); ?>" onClick="document.getElementById('productForm').task.value='saveProduct';validateForm();"/>
			<input type="submit" id="back_product" name="back_product" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?option=com_easysdi_shop&task=cancelEditProduct')); ?>', '_self');"/>
		 </div>
		<script>
		function validateForm()
		{
			var form = document.getElementById('productForm');
			var text = '';
			var index = 0;

			if (   form.elements['name'].value == '')
			{
				text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_NAME");?>";	
				index = 1;			
			} 
			if (form.elements['objectversion_id'].value == '0')
			{
				if(index != 0)text += ", ";
				text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_VERSION");?>"; 
				index = 1;	
			}
			if (form.elements['diffusion_id'].value == '0')
			{
				if(index != 0)text += ", ";
				text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_DIFFUSION");?>"; 
				index = 1;	
			}
			if (form.elements['available'].value == '0' && form.elements['surfacemin'].value == '')
			{
				if(index != 0)text += ", ";
				text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_SURFACEMIN");?>"; 
				index = 1;	
			}
			if (form.elements['available'].value == '0' && form.elements['surfacemax'].value == '')
			{
				if(index != 0)text += ", ";
				text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_SURFACEMAX");?>";
				index = 1;	
			}
			if (form.elements['available'].value == '1' && form.elements['productfile'].value == '' && form.elements['pathfile'].value == '' && form.elements['productFileName'].value == '' &&  form.elements['grid_id'].value == '')
			{
				if(index != 0)text += ", ";
				text += "\n- <?php echo JText::_("SHOP_MESSAGE_PROVIDE_PRODUCT_FILE");?>";
				index = 1;	
			}
			if(index ==1)
			{
				text += ".";
				alert( "<?php echo JText::_("SHOP_MESSAGE_PROVIDE_VALUES");?> : "+text);
				return;
			}
			else
			{
				form.submit();
			}
		}
		</script>
		</div>
		</div>
	<?php
	}
	
	function valueInArray( $value,$array ){
		$ret = false;
		
		foreach($array as $val){
			if($val->value == $value->value)
				$ret=true;
		}
		
		return $ret;
	}
	
	function listProduct($pageNav,$rows,$option,$account,$search){
		$user	=& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		?>	
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("SHOP_LIST_PRODUCT"); ?></h2>
		<div class="contentin">
		<h3> <?php echo JText::_("CORE_SEARCH_CRITERIA_TITLE"); ?></h3>
		<form action="index.php" method="POST" id="productListForm" name="productListForm">
		<div class="row">
			 <div class="row">
			 	<label for="searchObjectName"><?php echo JText::_("SHOP_PRODUCT_FILTER_PRODUCTNAME");?></label>
			 	<input type="text" name="searchProduct" value="<?php echo $search;?>" class="inputboxSearchProduct text full" />
			 </div>
			 <div class="row2">
				<input type="submit" id="search_product" name="search_product" class="submit" value ="<?php echo JText::_("CORE_SEARCH_BUTTON"); ?>" onClick="document.getElementById('productListForm').task.value='listProduct';document.getElementById('productListForm').submit();"/>
				<input type="submit" id="newProductBtn" name="newProductBtn" class="submit" value ="<?php echo JText::_("SHOP_NEW_PRODUCT"); ?>" onClick="document.getElementById('productListForm').task.value='newProduct';document.getElementById('productListForm').submit();"/>
			</div>	 
		 </div>
	<div class="searchresults">
	<h3><?php echo JText::_("CORE_SEARCH_RESULTS_TITLE"); ?></h3>
	<script>
		function suppressProduct_click(url){
			conf = confirm('<?php echo JText::_("SHOP_CONFIRM_PRODUCT_DELETE"); ?>');
			if(!conf)
				return false;
			window.open(url, '_self');
		}
	</script>
	<?php
	if(count($rows) == 0){
		echo "<p><strong>".JText::_("SHOP_PRODUCT_NORESULTFOUND")."</strong>&nbsp;0&nbsp;</p>";
	}else{?>
	<table id="myProducts" class="box-table">
	<thead>
	<tr>
	<th class="logo2"></th>
	<th class='title'><?php echo JText::_('SHOP_PRODUCT_NAME'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_PRODUCT_OBJECTNAME'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_PRODUCT_VERSIONTITLE'); ?></th>
	<th class='title'><?php echo JText::_('SHOP_PRODUCT_ACTIONS'); ?></th>
	</tr>
	</thead>
	<?php } ?>
	<tbody>
	<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		foreach ($rows as $row)
		{	$i++;
			
			?>		
			<tr>
			<td class="logo2"><div <?php if($row->visibility == 'public' && $row->published == 1) echo 'title="'.JText::_("SHOP_INFOLOGO_ORDERABLE").'" class="easysdi_product_exists"'; else if($row->visibility == 'private' && $row->published == 1) echo 'title="'.JText::_("SHOP_INFOLOGO_ORDERABLE_INTERNAL").'" class="easysdi_product_exists_internal"';?>></div></td>
			<td><a class="modal" title="<?php echo JText::_("SHOP_PRODUCT_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_guid;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"> <?php echo $row->name ;?></a></td>
			<td><?php echo $row->object_name ?></td>
			<td><?php echo $row->version_title ?></td>
			<td class="productActions">
			<?php 
			if (JTable::isCheckedOut($user->get ('id'), $row->checked_out ) )
			{
				?>
				<div title="<?php echo JText::_('SHOP_ACTION_EDIT_PRODUCT_CHECKED_OUT'); ?>" id="editProductCheckedOut" ></div>
				<?php
			} 
			else
			{
				?>
				<div title="<?php echo JText::_('SHOP_ACTION_EDIT_PRODUCT'); ?>" id="editProduct" onClick="window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?option=com_easysdi_shop&task=editProduct&id='.$row->id.'&limitstart='.JRequest::getVar("limitstart").'&limit='.JRequest::getVar("limit"))); ?>', '_self');"></div>
				<?php
			}
			?>
			<div title="<?php echo JText::_('SHOP_ACTION_DELETE_PRODUCT'); ?>" id="deleteProduct" onClick="return suppressProduct_click('<?php echo JRoute::_(displayManager::buildUrl('index.php?option=com_easysdi_shop&task=suppressProduct&limitstart='.JRequest::getVar("limitstart").'&limit='.JRequest::getVar("limit").'&publishedobject=product&cid[]='.$row->id)); ?>');" ></div>
			</td>
			</tr>
			<?php		
		}
		
	?>
	</tbody>
	</table>
	<?php echo $pageNav->getPagesCounter(); ?>&nbsp;<?php echo $pageNav->getPagesLinks(); ?>
	</div>
	
	
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task" name="task" value="listProduct">
			<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
			<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
		</form>
		</div>
		</div>
	<?php
		
		
	}
	
	function array_to_json( $array ){

   		 if( !is_array( $array ) ){
   		     return false;
   		 }
   		
   		 $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
   		 if( $associative ){
   		
   		     $construct = array();
   		     foreach( $array as $key => $value ){
   		
   		         // We first copy each key/value pair into a staging array,
   		         // formatting each key and value properly as we go.
   		
   		         // Format the key:
   		         if( is_numeric($key) ){
   		             $key = "key_$key";
   		         }
   		         $key = "'".addslashes($key)."'";
   		
   		         // Format the value:
   		         if( is_array( $value )){
   		             $value = array_to_json( $value );
   		         } else if( !is_numeric( $value ) || is_string( $value ) ){
   		             $value = "'".addslashes($value)."'";
   		         }
   		
   		         // Add to staging array:
   		         $construct[] = "$key: $value";
   		     }
   		
   		     // Then we collapse the staging array into the JSON form:
   		     $result = "{ " . implode( ", ", $construct ) . " }";
   		
   		 } else { // If the array is a vector (not associative):
   		
   		     $construct = array();
   		     foreach( $array as $value ){
   		
   		         // Format the value:
   		         if( is_array( $value )){
   		             $value = array_to_json( $value );
   		         } else if( !is_numeric( $value ) || is_string( $value ) ){
   		             $value = "'".addslashes($value)."'";
   		         }
   		
   		         // Add to staging array:
   		         $construct[] = $value;
   		     }
   		
   		     // Then we collapse the staging array into the JSON form:
   		     $result = "[ " . implode( ", ", $construct ) . " ]";
   		 }
   		
   		 return $result;
	}
	
	function gridSelection($product, $option, $task,$view,$step,$row)
	{
		$grid 		= $product->getGrid();
		$proxyhost 	= config_easysdi::getValue("SHOP_CONFIGURATION_PROXYHOST");
		$urlwfs 	= $proxyhost."&gridid=$grid->id&type=wfs&url=".urlencode  (trim($grid->urlwfs));
		$urlwms 	= $proxyhost."&gridid=$grid->id&type=wms&url=".urlencode  (trim($grid->urlwms));
		?>
			<script type="text/javascript" src="./administrator/components/com_easysdi_shop/lib/openlayers2.11/lib/OpenLayers.js"></script>
			<script type="text/javascript" src="./administrator/components/com_easysdi_shop/lib/proj4js/lib/proj4js.js"></script>
			<script type="text/javascript" src="./administrator/components/com_easysdi_shop/lib/openlayers2.11/lib/OpenLayers/Control/LoadingPanel.js"></script>
			<script defer="defer" type="text/javascript">
			var protocol, map, popup;

			function init(){
            	var options = { 
                    	<?php if($grid->minscale){?>
                    	minScale:<?php echo $grid->minscale;?>,
                    	<?php }?>
                    	<?php if($grid->maxscale){?>
                    	maxScale:<?php echo $grid->maxscale;?>,
                    	<?php }?>
                    	<?php if($grid->projection){?>
                        projection : new OpenLayers.Projection("<?php echo $grid->projection;?>"),
                        <?php }?>
                        <?php if($grid->extent){?>
                        maxExtent :new OpenLayers.Bounds(<?php echo $grid->extent;?>),
                        <?php }?>
                        units: "<?php echo $grid->unit;?>"
                      };
            	map = new OpenLayers.Map("map",options);

            	var baseLayer = new OpenLayers.Layer.WMS(
            	    "BaseMap",
            	    "<?php echo $urlwms;?>",
            	    {layers: "<?php echo $grid->layername;?>", format:"<?php echo $grid->imgformat;?>", transparent : true},
            	    {isBaseLayer: true, visibility: true}
            	);

            	protocol = new OpenLayers.Protocol.WFS({
        		    version: "1.0.0",
        		    srsName:"<?php echo $grid->projection;?>",
        		    url: "<?php echo $urlwfs;?>",
        		    featureType: "<?php echo $grid->featuretype;?>",
        		    featureNS: "<?php echo $grid->featureNS;?>",
        		    geometryName: "<?php echo $grid->fieldgeom;?>"
        		});
            	
        		var layer = new OpenLayers.Layer.Vector("Grid", {
					strategies : [ new OpenLayers.Strategy.BBOX() ],
					protocol : protocol,
	        		visibility: true,
	        		extractAttributes: true					
				});

        		var select = new OpenLayers.Layer.Vector("Selection", {styleMap: 
                    new OpenLayers.Style(OpenLayers.Feature.Vector.style["select"])
                });

                var hover = new OpenLayers.Layer.Vector("Hover");

                <?php if($grid->detailtooltip){?>
                var control = new OpenLayers.Control.GetFeature({
                    protocol: protocol,
                    box: false,
                    hover: true,
                    multipleKey: "shiftKey",
                    toggleKey: "ctrlKey"
                });
                <?php }else{?>
                var control = new OpenLayers.Control.GetFeature({
                    protocol: protocol,
                    box: false,
                    hover: false,
                    multipleKey: "shiftKey",
                    toggleKey: "ctrlKey"
                });
                <?php }?>
                
                control.events.register("featureselected", this, function(e) {
                    select.addFeatures([e.feature]);
                    document.getElementById('selected-grid-name').innerHTML = e.feature.attributes.<?php echo $grid->fieldname;?>;
					<?php if ($grid->fielddetail){?>
                    document.getElementById('selected-grid-detail').innerHTML = e.feature.attributes.<?php echo $grid->fielddetail;?>;
                    <?php }?>
                    document.getElementById('resource').value = e.feature.attributes.<?php echo $grid->fieldresource;?>;
                    document.getElementById('validateSelectGrid').disabled = false;
                });
                
                control.events.register("featureunselected", this, function(e) {
                    select.removeFeatures([e.feature]);
                    document.getElementById('selected-grid-detail').innerHTML = "";
                    document.getElementById('validateSelectGrid').disabled = true;
                });

                <?php if($grid->detailtooltip){?>
                control.events.register("hoverfeature", this, function(e) {
                    hover.addFeatures([e.feature]);
                    popup = new OpenLayers.Popup.FramedCloud(
                            "chicken", 
                            e.feature.geometry.getBounds().getCenterLonLat(),
                            null,
                            e.feature.attributes.<?php echo $grid->fieldname;?> + "<br>" <?php if($grid->fielddetail){?>+ e.feature.attributes.<?php echo $grid->fielddetail;?><?php }?>,
                            null,
                            true
                        )
                    map.addPopup(popup);
                });
                
                control.events.register("outfeature", this, function(e) {
                    hover.removeFeatures([e.feature]);
                    if(popup != null)
                    	map.removePopup(popup);
                	popup.destroy();
                	popup = null;
                });
                <?php }?>
                
                map.addControl(control);
                control.activate();
                
            	map.addLayers([baseLayer, layer, select, hover]);
            	map.zoomToMaxExtent();
            }
            
			function validate()
			{
				document.getElementById('task').value='downloadAvailableProduct';
				document.getElementById('selectGridForm').submit();
			}
	        </script>
	        <body onload="init()">
	       	<form name="selectGridForm" id="selectGridForm" action='index.php' method='GET'>
	       		<div id="map" class="grid-map" ></div>
				<div id="selected-grid" class="grid-selection">
			        <h1><?php echo JText::_('SHOP_GRID_SELECTION_FEATURE_SELECTED_TITLE'); ?></h1>
			        <div id="selected-grid-name">
			        </div>
			        <div id="selected-grid-detail">
			        </div>
			        <input type="submit" id="validateSelectGrid" name="validateSelectGrid" class="submit" disabled value ="<?php echo JText::_("SHOP_VALIDATE_BUTTON"); ?>" 
							onClick="javascript:validate();"/>
			    </div>
			   
				<input type='hidden' name='option' value='<?php echo $option;?>'> 
				<input type='hidden' id="task" name='task' value='<?php echo $task; ?>'> 
				<input type='hidden' id="view" name='view' value='<?php echo $view; ?>'> 
				<input type='hidden' id="fromStep" name='fromStep' value='1'> 
				<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'>
				<input type='hidden' name='tmpl' value='component'> 
				<input type='hidden' name='resource' id='resource' value=''> 
				<input type='hidden' name='cid[]' value='<?php echo $product->id;?>'>
			</form>
			</body>
			<?php
		}
	}
?>