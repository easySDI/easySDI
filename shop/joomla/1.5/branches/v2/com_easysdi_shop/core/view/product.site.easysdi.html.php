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
	
	function editProduct($account,$product,$version,$supplier,$id,$accounts,$object_id, $objecttype_id,$objecttype_list,$object_list,$version_list,$diffusion_list,$baseMap_list,$treatmentType_list,$visibility_list,$perimeter_list,$rowsAccount,$option ){
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		if($account->root_id == "")
		{
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
								<td class="ptitle"><?php echo JText::_("CORE_NAME"); ?> : </td>
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
								<td class="ptitle"><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
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
									document.getElementById('available').disabled = true;
									document.getElementById('available').value = '0';
									document.getElementById('surfacemin').disabled = false;
									document.getElementById('surfacemax').disabled = false;
									document.getElementById('notification').disabled = false;
									document.getElementById('treatmenttype_id').disabled = false;
								}
								else if (document.forms['productForm'].free.value == '1' && document.forms['productForm'].available.value == '0')
								{
									document.getElementById('productfile').disabled = true;
									document.getElementById('available').disabled = false;
									document.getElementById('surfacemin').disabled = false;
									document.getElementById('surfacemax').disabled = false;
									document.getElementById('notification').disabled = false;
									document.getElementById('treatmenttype_id').disabled = false;
								}
								else
								{
									document.getElementById('productfile').disabled = false;
									document.getElementById('available').disabled = false;
									document.getElementById('surfacemin').disabled = true;
									document.getElementById('surfacemax').disabled = true;
									document.getElementById('notification').disabled = true;
									document.getElementById('treatmenttype_id').disabled = true;
								}
							}
						</script>
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
								<td colspan="2"><select class="inputbox" name="free" id="free"  onChange="javascript:fieldManagement();">								
									<option value="0" <?php if( $product->free == 0 ) echo "selected"; ?> ><?php echo JText::_("CORE_NO"); ?></option>
									<option value="1" <?php if( $product->free == 1 ) echo "selected"; ?>><?php echo JText::_("CORE_YES"); ?></option>
									</select>
									</td></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_AVAILABLE"); ?> : </td>
								
									<td>
									<select <?php if( $product->free == 0 ) echo "disabled"; ?> class="inputbox" name="available" id="available"  onChange="javascript:fieldManagement();">								
									<option value="0" <?php if( $product->available == 0 ) echo "selected"; ?> ><?php echo JText::_("CORE_NO"); ?></option>
									<option value="1" <?php if( $product->available == 1 ) echo "selected"; ?>><?php echo JText::_("CORE_YES"); ?></option>
									</select>
									</td>
									<td><a target="RAW" href="./index.php?option=<?php echo $option; ?>&task=downloadFinalProduct&product_id=<?php echo $product->id?>">
									<?php echo $product->getFileName();?></a></td>
									
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PRODUCT_UP_FILE") ;?></td>
								<td colspan="2"><input type="file" name="productfile" id="productfile" <?php if( $product->available == 0 ) echo "disabled"; ?> ></td>
							</tr>
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
		</script>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
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
								<td class="ptitle"><?php echo JText::_("SHOP_PREVIEW_WMS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewurlwms" value="<?php echo $product->viewurlwms; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_PREVIEW_LAYERS"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewlayers" value="<?php echo $product->viewlayers; ?>" /></td>								
							</tr>							
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_MINRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewminresolution" value="<?php echo $product->viewminresolution; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("SHOP_MAXRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewmaxresolution" value="<?php echo $product->viewmaxresolution; ?>" /></td>								
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
								<td class="ptitle"><?php echo JText::_("SHOP_IMG_FORMAT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewimgformat" value="<?php echo $product->viewimgformat; ?>" /></td>								
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
		</form>
		</div>
		<table>
			<tr>
				<td>
					<button type="button" onClick="document.getElementById('productForm').task.value='saveProduct';validateForm();" ><?php echo JText::_("CORE_SAVE"); ?></button>		
				</td>
				<td>
					<button type="button" onClick="document.getElementById('productForm').task.value='cancelEditProduct';document.getElementById('productForm').submit();" ><?php echo JText::_("CORE_CANCEL"); ?></button>
				</td>
			</tr>
		</table>
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
		?>	
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("SHOP_LIST_PRODUCT"); ?></h2>
		<div class="contentin">
		<h3> <?php echo JText::_("CORE_SEARCH_CRITERIA_TITLE"); ?></h3>
		<form action="index.php" method="GET" id="productListForm" name="productListForm">
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("CORE_SHOP_FILTER_TITLE");?></b>&nbsp;
				</td>
				<td align="left">
					<input type="text" name="searchProduct" value="<?php echo $search;?>" class="inputboxSearchProduct"/></td>
				<td align="right">
					<button type="submit" class="searchButton" > <?php echo JText::_("CORE_SEARCH_BUTTON"); ?></button>
				</td>
			</tr>
			<tr>
				<td colspan="3" align="right">
					<button id="newProductBtn" type="button" onClick="document.getElementById('task<?php echo $option; ?>').value='newProduct';document.getElementById('productListForm').submit();" ><?php echo JText::_("SHOP_NEW_PRODUCT"); ?></button>
				</td>
			</tr>
		</table>
		<br/>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
				<td align="center"><?php echo JText::_("CORE_SHOP_DISPLAY"); ?> <?php echo $pageNav->getLimitBox(); ?></td>
				<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("CORE_SEARCH_RESULTS_TITLE"); ?></h3>
	<script>
		function suppressProduct_click(id){
			conf = confirm('<?php echo JText::_("SHOP_CONFIRM_PRODUCT_DELETE"); ?>');
			if(!conf)
				return false;
			window.open('./index.php?option=com_easysdi_shop&task=suppressProduct&limitstart=<?php echo JRequest::getVar("limitstart"); ?>&limit=<?php echo JRequest::getVar("limit"); ?>&publishedobject=product&cid[]='+id, '_self');
		}
	</script>
	<?php
	if(count($rows) == 0){
		echo "<table><tbody><tr><td colspan=\"7\">".JText::_("CORE_NO_RESULT_FOUND")."</td>";
	}else{?>
	<table id="myProducts" class="box-table">
	<thead>
	<tr>
	<th class="logo2"></th>
	<th><?php echo JText::_('SHOP_PRODUCT_DESCRIPTION'); ?></th>
	<th class="logo">&nbsp;</th>
	<th class="logo">&nbsp;</th>
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
			<td width="100%"><a class="modal" title="<?php echo JText::_("SHOP_PRODUCT_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_guid;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"> <?php echo $row->name ;?></a></td>
			<?php 
			if (JTable::isCheckedOut($user->get ('id'), $row->checked_out ) )
			{
				?>
				<td class="logo"><div title="<?php echo JText::_('SHOP_ACTION_EDIT_PRODUCT_CHECKED_OUT'); ?>" id="editObjectCheckedOut" /></td>
				<?php
			} 
			else
			{
				?>
				<td class="logo"><div title="<?php echo JText::_('SHOP_ACTION_EDIT_PRODUCT'); ?>" id="editObject" onClick="window.open('./index.php?option=com_easysdi_shop&task=editProduct&id=<?php echo $row->id;?>&limitstart=<?php echo JRequest::getVar("limitstart"); ?>&limit=<?php echo JRequest::getVar("limit"); ?>', '_self');"/></td>
				<?php
			}
			?>
			<td class="logo"><div title="<?php echo JText::_('SHOP_ACTION_DELETE_PRODUCT'); ?>" id="deleteObject" onClick="return suppressProduct_click('<?php echo $row->id; ?>');" /></td>
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
			<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
		</tr>
	</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listProduct">
			
			<?php if (userManager::hasRight($account->id,"INTERNAL")){?> 
			<?php }  ?>
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
}
?>