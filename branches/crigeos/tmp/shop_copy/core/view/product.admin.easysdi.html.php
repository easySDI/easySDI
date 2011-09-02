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

	function editProduct($product,$version,$object_id,$objecttype_id,$supplier,$objecttype_list, $object_list,$version_list,$diffusion_list,$baseMap_list,$treatmentType_list,$visibility_list,$perimeter_list,$selected_perimeter,$catalogUrlBase,$rowsAccount,$id, $option ){
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		JHTML::_('behavior.calendar');

		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("SHOP_PRODUCT_TITLE_EDIT"), 'generic.png' );
		?>	
		<script>
		function displayAuthentication()
		{
			if (document.forms['adminForm'].service_type[0].checked)
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
		
		function fieldManagement()
		{
			if (document.forms['adminForm'].free.value == '0')
			{
				document.getElementById('productfile').disabled = true;
				document.getElementById('available').disabled = true;
				document.getElementById('available').value = '0';
				document.getElementById('surfacemin').disabled = false;
				document.getElementById('surfacemax').disabled = false;
				document.getElementById('notification_email').disabled = false;
				document.getElementById('treatmenttype_id').disabled = false;
			}
			else if (document.forms['adminForm'].free.value == '1' && document.forms['adminForm'].available.value == '0')
			{
				document.getElementById('productfile').disabled = true;
				document.getElementById('available').disabled = false;
				document.getElementById('surfacemin').disabled = false;
				document.getElementById('surfacemax').disabled = false;
				document.getElementById('notification_email').disabled = false;
				document.getElementById('treatmenttype_id').disabled = false;
			}
			else
			{
				document.getElementById('productfile').disabled = false;
				document.getElementById('available').disabled = false;
				document.getElementById('surfacemin').disabled = true;
				document.getElementById('surfacemax').disabled = true;
				document.getElementById('notification_email').disabled = true;
				document.getElementById('treatmenttype_id').disabled = true;
			}
		}
		</script>			
	<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("SHOP_GENERAL"),"productrPane");
		?>		
		<table class="admintable" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("SHOP_GENERAL"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="key"><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $product->id; ?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_OBJECT_TYPE"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$objecttype_list, 'objecttype_id', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'editProduct\');"', 'value', 'text', $objecttype_id ); ?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_OBJECT"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$object_list, 'object_id', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'editProduct\');"', 'value', 'text', $object_id ); ?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_VERSION"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$version_list, 'objectversion_id', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'editProduct\');"', 'value', 'text', $version->id ); ?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("CORE_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" id="name" value="<?php echo $product->name; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" id="description" value="<?php echo $product->description; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("CORE_CREATED"); ?> : </td>
								<?php $date = new JDate($product->created); ?>
								<input type="hidden" name="created" value="<?php echo $date->toMySQL(); ?>" />								
								<td><?php echo date('d.m.Y H:i:s',strtotime($product->created)); ?></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("CORE_UPDATED"); ?> : </td>						
								<?php $date = new JDate($product->updated); ?>										
								<input type="hidden"  name="updated" value="<?php echo $date->toMySQL(); ?>" />
								<td><?php echo date('d.m.Y H:i:s',strtotime($product->updated)); ?></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
								<td><select class="inputbox" name="published" >								
								<option value="0" <?php if( $product->published == 0 ) echo "selected"; ?> ><?php echo JText::_("CORE_FALSE"); ?></option>
								<option value="1" <?php if( $product->published == 1 ) echo "selected"; ?>><?php echo JText::_("CORE_TRUE"); ?></option>
								</select></td>																
							</tr>
							
							</table>
					</fieldset>
					<fieldset>
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_DIFFUSION"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_DIFFUSION_NAME"); ?> : </td>
								<td colspan="2"><?php echo JHTML::_("select.genericlist",$diffusion_list, 'diffusion_id', 'size="1" class="inputbox"', 'value', 'text', $product->diffusion_id ); ?></td>								
							</tr>
							
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_VISIBILITY"); ?> : </td>
								<td colspan="2"><?php echo JHTML::_("select.genericlist",$visibility_list, 'visibility_id', 'size="1" class="inputbox"', 'value',  'text', $product->visibility_id ); ?></td>															
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_FREE"); ?> : </td>
								<td colspan="2"><select class="inputbox" name="free" id="free"  onChange="javascript:fieldManagement();">								
									<option value="0" <?php if( $product->free == 0 ) echo "selected"; ?> ><?php echo JText::_("CORE_NO"); ?></option>
									<option value="1" <?php if( $product->free == 1 ) echo "selected"; ?>><?php echo JText::_("CORE_YES"); ?></option>
									</select>
									</td></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_AVAILABLE"); ?> : </td>
								
									<td>
									<select <?php if( $product->free == 0 ) echo "disabled"; ?> class="inputbox" name="available" id="available"  onChange="javascript:fieldManagement();">								
									<option value="0" <?php if( $product->available == 0 ) echo "selected"; ?> ><?php echo JText::_("CORE_NO"); ?></option>
									<option value="1" <?php if( $product->available == 1 ) echo "selected"; ?>><?php echo JText::_("CORE_YES"); ?></option>
									</select>
									</td>
									<td><a target="RAW" href="./index.php?format=raw&option=<?php echo $option; ?>&task=downloadProduct&product_id=<?php echo $product->id?>">
									<?php echo $product->getFileName();?></a></td>
									
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_UP_FILE") ;?></td>
								<td colspan="2"><input type="file" name="productfile" id="productfile" <?php if( $product->available == 0 ) echo "disabled"; ?> ></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_TREATMENT"); ?> : </td>
								<td colspan="2"><?php $disabled=''; if( $product->available == 1 ) $disabled='disabled'; echo JHTML::_("select.genericlist",$treatmentType_list, 'treatmenttype_id', 'size="1" class="inputbox" '.$disabled, 'value',  'text', $product->treatmenttype_id ); ?></td>															
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_SURFACE_MIN"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="surfacemin"  id="surfacemin" <?php  if( $product->available == 1 ) echo $disabled;?> value="<?php echo $product->surfacemin; ?>" /></td>							
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_SURFACE_MAX"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="100" name="surfacemax" id="surfacemax"  <?php if( $product->available == 1 ) echo $disabled;?> value="<?php echo $product->surfacemax; ?>" /></td>							
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_NOTIFICATION_EMAIL"); ?> : </td>
								<td colspan="2"><input class="inputbox" type="text" size="50" maxlength="500" name="notification_email" id="notification_email" <?php if( $product->available == 1 ) echo $disabled;?> value="<?php echo $product->notification_email; ?>" /></td>								
							</tr>
							
						</table>
					</fieldset>	
				</td>
			</tr>
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("SHOP_PRODUCT_PREVIEW"),"productrPane");
		?>
		<br>
		<table class="admintable" >
			<tr>
				<td>
					<fieldset>
						<table   >						
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PREVIEW_BASEMAP"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$baseMap_list, 'viewbasemap_id', 'size="1" class="inputbox"', 'value', 'text', $product->viewbasemap_id ); ?></td>																
							</tr>							
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PREVIEW_WMS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewurlwms" value="<?php echo $product->viewurlwms; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PREVIEW_LAYERS"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewlayers" value="<?php echo $product->viewlayers; ?>" /></td>								
							</tr>							
							<tr>
								<td class="key"><?php echo JText::_("SHOP_MINRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewminresolution" value="<?php echo $product->viewminresolution; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_MAXRESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewmaxresolution" value="<?php echo $product->viewmaxresolution; ?>" /></td>								
							</tr>			
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PROJECTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewprojection" value="<?php echo $product->viewprojection; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_UNIT"); ?> : </td>
								<td><select class="inputbox" name="previewUnit" >								
									<option <?php if($product->viewunit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("SHOP_METERS"); ?></option>
									<option <?php if($product->viewunit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("SHOP_DEGREES"); ?></option>
								</select>
								</td>																						
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_IMG_FORMAT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewimgformat" value="<?php echo $product->viewimgformat; ?>" /></td>								
							</tr>
														
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td >
				<fieldset>
				<legend><?php echo JText::_("SHOP_AUTHENTICATION"); ?></legend>
					<table class="admintable" >
					<tr>
						<td >
							<input type="radio" name="service_type" value="via_proxy" onclick="javascript:displayAuthentication();" <?php if ($product->viewaccount_id) echo "checked";?>>
						</td>
						<td class="key" colspan="2">
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
					 	<td class="key" colspan="2">
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
		echo $tabs->startPanel(JText::_("SHOP_PRODUCT_PERIMETER"),"productrPane");
		?>
		<table class="admintable" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset >
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
									<th align="center" ><?php echo JText::_("SHOP_PRODUCT_PERIMETER_NAME") ?></th>
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
								  	<td  class="key" align="center"><?php  echo $curPerim->text; ?></td>
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
		echo $tabs->startPanel(JText::_("SHOP_PRODUCT_PROPERTIES"),"productrPane");
		?>
		<table class="admintable"  border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
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
				
				foreach ($propertiesList as $curProperty){
						?><tr><td class="key"><?php echo $curProperty->text; ?></td>
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
							
							if ($database->getErrorNum()) {						
									$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
								}
								switch($curProperty->type){
			
								case "list":
									?>
									<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', 'size="'.count($propertiesValueList).'" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>							
									<?php
									break;
									
								case "mlist":
									?>
									<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', 'size="'.count($propertiesValueList).'" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>
									<?php
									break;
								case "cbox":
									?>
									<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', 'size="'.count($propertiesValueList).'" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>
									<?php
									break;
									
								case "text":
									if ($curProperty->mandatory == 0 ){
										$propertiesTemp1[] = JHTML::_('select.option','-1', JText::_("SHOP_PROPERTY_NONE"));
										$propertiesTemp2[] = JHTML::_('select.option',$propertiesValueList[0]->value, JText::_("SHOP_PROPERTY_YES"));
										$propertiesValueList = array_merge( $propertiesTemp1 , $propertiesTemp2);
									}
									?>
									<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', '', 'value', 'text', $selected ); ?></td>
									<?php
									break;
									
								case "textarea":
									if ($curProperty->mandatory == 0 ){
										
									$propertiesValueList2[] = JHTML::_('select.option','-1', JText::_("SHOP_PROPERTY_NONE") );
									$propertiesValueList = array_merge( $propertiesValueList , $propertiesValueList2  );
										
									}
									?>
									<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', 'size="1" ', 'value', 'text', $selected ); ?></td>
									<?php
									break;
								case "message":
									if ($curProperty->mandatory == 0 )
									{
										
										$propertiesValueList3[] = JHTML::_('select.option','-1', JText::_("SHOP_PROPERTY_NONE") );
										$propertiesValueList = array_merge( $propertiesValueList , $propertiesValueList3  );
										
									}
									?>
									<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'property_id[]', 'size="1" ', 'value', 'text', $selected ); ?></td>
									<?php
									break;
								}	
							?>	
						<?php } ?>
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
		<input type="hidden" name="id" value="<?php echo $product->id;?>">		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="editProduct" />
		<input type="hidden" name="createdby" value="<?php echo $product->createdby; ?>" />
		<input type="hidden" name="created" value="<?php echo $product->created; ?>" />
		<input type="hidden" name="checked_out" value="<?php echo $product->checked_out; ?>" />
		<input type="hidden" name="checked_out_time" value="<?php echo $product->checked_out_time; ?>" />
		</form>
	<?php
	}
	
	function listProduct( $rows, $filter_order_Dir, $filter_order,$search,$pageNav, $option){
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		JToolBarHelper::title(JText::_("SHOP_LIST_PRODUCT")); 
		$partners = array(); ?>
		<form action="index.php" method="post" name="adminForm">
				<table  width="100%">
					<tr>
						<td class="key"  width="100%">
							<?php echo JText::_("FILTER"); ?>:
							<input type="text" name="searchProduct" id="searchProduct" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
							<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
							<button onclick="document.getElementById('searchProduct').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
						</td>
					</tr>
				</table>
				<table class="adminlist">
				<thead>
					<tr>					 			
						<th class='title'><?php echo JText::_("CORE_SHARP"); ?></th>
						<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
						<th class='title'><?php echo JText::_("CORE_PUBLISHED"); ?></th>
						<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
						<th class='title'><?php echo JText::_("SHOP_PRODUCT_METADATA"); ?></th>
						<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
						<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_PRODUCT_OBJECT"), 'object_name', @$filter_order_Dir, @$filter_order); ?></th>
						<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_PRODUCT_VERSION"), 'version_title', @$filter_order_Dir, @$filter_order); ?></th>
						<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_PRODUCT_TREATMENT"), 'treatment', @$filter_order_Dir, @$filter_order); ?></th>
						<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>	
					</tr>
				</thead>
				<tbody>		
		<?php
		$k = 0;
		$i=0;
		JHTML::_("behavior.modal","a.modal",$param); 
		foreach ($rows as $row)
		{			
			$link = 'index.php?option='.$option.'&task=editProduct&cid[]='.$row->id;			
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td> <?php echo JHTML::_('grid.published',$row,$i); ?></td>
				<td>
				<?php 
				if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
				{
					echo $row->name;
				} 
				else 
				{
					?>
					<a href="<?php echo $link;?>"><?php echo $row->name; ?></a>
					<?php
				}
				?>
				</td>
				<td align="center">
					<a class="modal" href="<?php echo JURI::root(true); ?>/index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_guid;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}" title="<?php echo JText::_( 'SHOP_PRODUCT_VIEW_METADATA' ); ?>">
					<img src="<?php echo JURI::root(true); ?>/includes/js/ThemeOffice/document.png" border="0" /></a>
				</td>
				<td><?php echo $row->description; ?></a></td>
				<td><?php echo $row->object_name; ?></a></td>
				<td><?php echo $row->version_title; ?></a></td>
				<td><?php echo JText::_($row->treatment); ?></td>								
				<td><?php echo date('d.m.Y H:i:s',strtotime($row->created)); ?></td>
			</tr>
			<?php
			$k = 1 - $k;
			$i ++;
		}
		?></tbody>
		<tfoot>
		<tr>	
		<td colspan="10"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listProduct" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0" />
	  	<input type="hidden" name="publishedobject" value="product" />
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
		<?php
	}	


}
?>