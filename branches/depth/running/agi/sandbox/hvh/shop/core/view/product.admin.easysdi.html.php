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

	function editProduct($product,$version,$object_id,$objecttype_id,$supplier,$objecttype_list, $object_list,$version_list,$diffusion_list,$baseMap_list,$treatmentType_list,$visibility_list,$accessibility_list,$perimeter_list,$selected_perimeter,$catalogUrlBase,$rowsAccount,$rowsUser,$userPreviewSelected,$userDownloadSelected,$id, $grid_list,$option ){
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

		function ServiceFieldManagement(){
			if (document.forms['adminForm'].viewurltype.value == 'WMS')
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
		function fieldManagement()
		{
			if (document.forms['adminForm'].free.value == '0')
			{
				document.getElementById('productfile').disabled = true;
				document.getElementById('pathfile').disabled = true;
				document.getElementById('available').disabled = true;
				document.getElementById('available').value = '0';
				document.getElementById('deleteFileButton').disabled = true;
				document.getElementById('linkFile').value = true;
				document.getElementById('surfacemin').disabled = false;
				document.getElementById('surfacemax').disabled = false;
				document.getElementById('notification_email').disabled = false;
				document.getElementById('treatmenttype_id').disabled = false;
				document.getElementById('grid_id').disabled = true;
			}
			else if (document.forms['adminForm'].free.value == '1' && document.forms['adminForm'].available.value == '0')
			{
				document.getElementById('productfile').disabled = true;
				document.getElementById('pathfile').disabled = true;
				document.getElementById('available').disabled = false;
				document.getElementById('deleteFileButton').disabled = true;
				document.getElementById('linkFile').disabled = false;
				document.getElementById('surfacemin').disabled = false;
				document.getElementById('surfacemax').disabled = false;
				document.getElementById('notification_email').disabled = false;
				document.getElementById('treatmenttype_id').disabled = false;
				document.getElementById('grid_id').disabled = true;
			}
			else
			{
				document.getElementById('productfile').disabled = false;
				document.getElementById('pathfile').disabled = false;
				document.getElementById('available').disabled = false;
				document.getElementById('deleteFileButton').disabled = false;
				document.getElementById('linkFile').disabled = false;
				document.getElementById('surfacemin').disabled = true;
				document.getElementById('surfacemax').disabled = true;
				document.getElementById('notification_email').disabled = true;
				document.getElementById('treatmenttype_id').disabled = true;
				document.getElementById('grid_id').disabled = false;
			}
		}
		
		function accessibilityEnable(choice,list)
		{
			var form = document.adminForm;
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

		function deleteFile (form){
				
			 if (confirm('<?php echo JText::_("SHOP_PRODUCT_MSG_CONFIRM_DELETE_FILE"); ?>')== true)
			 {
				 form.task.value='deleteProductFile';
				 form.submit();
			 }
		}

		function activateFileManagementOption (selected){
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
	<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("SHOP_GENERAL"),"productrPane");
		?>		
		<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $product->maxFileSize;?>000000">
		<table class="admintable" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2">
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
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<tr>
							<td>
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
											</td>								
										</tr>
										<tr>	
											<td class="key"><?php echo JText::_("SHOP_PRODUCT_AVAILABLE"); ?> : </td>
											<td>
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
								<fieldset>
									<legend><?php echo JText::_("SHOP_PRODUCT_FS_EXTRACTION"); ?></legend>
									<table border="0" cellpadding="3" cellspacing="0">
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
				</td>
				<td valign="top">
					<fieldset>
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_DIFFUSION_RIGHTS"); ?></legend>
						<table  border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_DOWNLOAD_ACCESSIBILITY"); ?> : </td>
								<td ><?php echo JHTML::_("select.genericlist",$accessibility_list, 'loadaccessibility_id', 'size="1" class="inputbox" onChange="javascript:accessibilityEnable(\'loadaccessibility_id\',\'userDownloadList\');"', 'value',  'text', $product->loadaccessibility_id ); ?></td>															
							</tr>			
							<tr>
								<td class="key"><?php echo JText::_( 'SHOP_PRODUCT_DOWNLOAD_ACCESSIBILITY_USER'); ?> </td>
								<td ><?php
								if ($product->loadaccessibility_id != 0 || $product->loadaccessibility_id != "" || $product->loadaccessibility_id != null )  {$disabled = 'disabled';} else {$disabled = '';};
								 echo JHTML::_("select.genericlist",$rowsUser, 'userDownloadList[]', 'size="15" multiple="true" class="selectbox" '.$disabled, 'value', 'text', $userDownloadSelected ); ?></td>
							</tr>
						</table>
						
					</fieldset>	
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<fieldset>
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_DIFFUSION_MODE"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<?php if ($product->getFileName()) $repositorychecked = 'checked="checked"'; ?>
								<td><input type="radio" name="diffusion_mode" value="repository" onclick="javascript:activateFileManagementOption('repository');" <?php echo $repositorychecked;?>></td>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_UP_FILE") ;?></td>
								<td>
									<a id="linkFile" target="RAW" href="./index.php?format=raw&option=<?php echo $option; ?>&task=downloadProduct&product_id=<?php echo $product->id?>"><?php echo $product->getFileName();?> </a> 
								</td>
								<td >
									<input type="file" name="productfile" id="productfile" <?php if( $product->available == 0 || !$repositorychecked ) echo "disabled"; ?> ><?php printf( JText::_("SHOP_PRODUCT_FILE_MAX_SIZE"),$product->maxFileSize); ?> 
								</td>
								<td>
									<button type="button" id="deleteFileButton" onCLick="deleteFile(document.getElementById('adminForm'));" <?php if( $product->available == 0 || !$repositorychecked ) echo "disabled"; ?>><?php echo JText::_("SHOP_PRODUCT_DELETE_FILE"); ?></button>
								</td>
							</tr>
							<tr>
								<?php if ($product->pathfile) $linkchecked = 'checked="checked"'; ?>
								<td><input type="radio" name="diffusion_mode" value="link" onclick="javascript:activateFileManagementOption('link');" <?php echo $linkchecked;?> ></td>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_PATH_FILE") ;?></td>
								<td colspan="3"><input class="inputbox" type="text" size="50" maxlength="100" name="pathfile"  id="pathfile" <?php  if( $product->available == 0 || !$linkchecked) echo "disabled";?> value="<?php echo $product->pathfile; ?>" /></td>
							</tr>
							<tr>
								<?php if ($product->grid_id) $gridchecked = 'checked="checked"'; ?>
								<td><input type="radio" name="diffusion_mode" value="grid" onclick="javascript:activateFileManagementOption('grid');" <?php echo $gridchecked;?> ></td>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_GRID_SELECTION") ;?></td>
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
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("SHOP_PRODUCT_PREVIEW"),"productrPane");
		?>
		<br>
		<table class="admintable" >
			<tr>
				<td colspan="2">
					<fieldset>
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_PREVIEW_DEFINITION"); ?></legend>
						<table >			
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PREVIEW_BASEMAP"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$baseMap_list, 'viewbasemap_id', 'size="1" class="inputbox"', 'value', 'text', $product->viewbasemap_id ); ?></td>																
							</tr>	
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PREVIEW_URL_TYPE"); ?> : </td>
								<td><select class="inputbox" name="viewurltype" id="viewurltype" onChange="javascript:ServiceFieldManagement();">								
									<option <?php if($product->viewurltype == 'WMS') echo "selected" ; ?> value="WMS"> WMS</option>
									<option <?php if($product->viewurltype == 'WMTS') echo "selected" ; ?> value="WMTS"> WMTS</option>
								</select>
								</td>								
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
								<td class="key"><?php echo JText::_("SHOP_IMG_FORMAT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewimgformat" value="<?php echo $product->viewimgformat; ?>" /></td>								
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
								<td class="key"><?php echo JText::_("SHOP_PREVIEW_STYLE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewstyle" id="viewstyle" value="<?php echo $product->viewstyle; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_MAXEXTENT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewextent" id="viewextent" value="<?php echo $product->viewextent; ?>" /></td>								
							</tr>						
							<tr>
								<td class="key"><?php echo JText::_("SHOP_MINSCALE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewminresolution" id="viewminresolution" value="<?php echo $product->viewminresolution; ?>" <?php if ($product->viewurltype != 'WMS') echo 'disabled'; ?> /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_MAXSCALE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewmaxresolution"   id="viewmaxresolution"  value="<?php echo $product->viewmaxresolution; ?>" <?php if ($product->viewurltype != 'WMS') echo 'disabled'; ?> /></td>								
							</tr>			
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PREVIEW_MATRIXSET"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="viewmatrixset" id="viewmatrixset" value="<?php echo $product->viewmatrixset; ?>" <?php if ($product->viewurltype != 'WMTS') echo 'disabled'; ?> /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PREVIEW_MATRIX"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="1000" name="viewmatrix" id="viewmatrix" value="<?php echo $product->viewmatrix; ?>" <?php if ($product->viewurltype != 'WMTS') echo 'disabled'; ?>/></td>								
							</tr>
						</table>
					</fieldset>
				</td>
				</tr>
			<tr>
				<td valign = "top">
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
				<td valign = "top">
					<fieldset>
						<legend><?php echo JText::_("SHOP_PRODUCT_FS_PREVIEW_RIGHTS"); ?></legend>
						<table  border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PRODUCT_PREVIEW_ACCESSIBILITY"); ?> : </td>
								<td ><?php echo JHTML::_("select.genericlist",$accessibility_list, 'viewaccessibility_id', 'size="1" class="inputbox"  onChange="javascript:accessibilityEnable(\'viewaccessibility_id\',\'userPreviewList\');"', 'value',  'text', $product->viewaccessibility_id ); ?></td>
							</tr>			
							<tr>
								<td class="key"><?php echo JText::_( 'SHOP_PRODUCT_PREVIEW_ACCESSIBILITY_USER'); ?> </td>
								<td ><?php
									if ($product->viewaccessibility_id != 0 || $product->viewaccessibility_id != "" || $product->viewaccessibility_id != null)   {$disabled = 'disabled';} else {$disabled = '';};
								 echo JHTML::_("select.genericlist",$rowsUser, 'userPreviewList[]', 'size="15" multiple="true" class="selectbox" '.$disabled, 'value', 'text', $userPreviewSelected ); ?></td>
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
		<input type="hidden" id="productFileName" name="productFileName" value="<?php echo $product->getFileName();?>">
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

	function downloadAvailableProductByGrid($product, $option, $task,$view,$step,$row)
	{
		$grid = $product->getGrid();
		?>
		<script type="text/javascript" src="./administrator/components/com_easysdi_shop/lib/openlayers2.11/lib/OpenLayers.js"></script>
		<script type="text/javascript" src="./administrator/components/com_easysdi_shop/lib/proj4js/lib/proj4js.js"></script>
		<script type="text/javascript" src="./administrator/components/com_easysdi_shop/lib/openlayers2.11/lib/OpenLayers/Control/LoadingPanel.js"></script>
		 <script defer="defer" type="text/javascript">
            var map;
            var untiled;
            var tiled;
            var pureCoverage = false;
            // pink tile avoidance
            OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
            // make OL compute scale according to WMS spec
            OpenLayers.DOTS_PER_INCH = 25.4 / 0.28;
        
            function init(){
                // if this is just a coverage or a group of them, disable a few items,
                // and default to jpeg format
                format = 'image/png';
                if(pureCoverage) {
                    document.getElementById('filterType').disabled = true;
                    document.getElementById('filter').disabled = true;
                    document.getElementById('antialiasSelector').disabled = true;
                    document.getElementById('updateFilterButton').disabled = true;
                    document.getElementById('resetFilterButton').disabled = true;
                    document.getElementById('jpeg').selected = true;
                    format = "image/jpeg";
                }
            
                var bounds = new OpenLayers.Bounds(
                    -124.73142200000001, 24.955967,
                    -66.969849, 49.371735
                );
                var options = {
                    controls: [],
                    maxExtent: bounds,
                    maxResolution: 0.22563114453125,
                    projection: "EPSG:4326",
                    units: 'degrees'
                };
                map = new OpenLayers.Map('map', options);
            
                // setup tiled layer
                tiled = new OpenLayers.Layer.WMS(
                    "topp:states - Tiled", "http://localhost:8888/geoserver/topp/wms",
                    {
                        LAYERS: 'topp:states',
                        STYLES: '',
                        format: format,
                        tiled: true,
                        tilesOrigin : map.maxExtent.left + ',' + map.maxExtent.bottom
                    },
                    {
                        buffer: 0,
                        displayOutsideMaxExtent: true,
                        isBaseLayer: true,
                        yx : {'EPSG:4326' : true}
                    } 
                );
            
                // setup single tiled layer
                untiled = new OpenLayers.Layer.WMS(
                    "topp:states - Untiled", "http://localhost:8888/geoserver/topp/wms",
                    {
                        LAYERS: 'topp:states',
                        STYLES: '',
                        format: format
                    },
                    {
                       singleTile: true, 
                       ratio: 1, 
                       isBaseLayer: true,
                       yx : {'EPSG:4326' : true}
                    } 
                );
        
                map.addLayers([untiled, tiled]);

                // build up all controls
                map.addControl(new OpenLayers.Control.PanZoomBar({
                    position: new OpenLayers.Pixel(2, 15)
                }));
                map.addControl(new OpenLayers.Control.Navigation());
                map.addControl(new OpenLayers.Control.Scale($('scale')));
                map.addControl(new OpenLayers.Control.MousePosition({element: $('location')}));
                map.zoomToExtent(bounds);
                
                // wire up the option button
                var options = document.getElementById("options");
                options.onclick = toggleControlPanel;
                
                // support GetFeatureInfo
                map.events.register('click', map, function (e) {
                    document.getElementById('nodelist').innerHTML = "Loading... please wait...";
                    var params = {
                        REQUEST: "GetFeatureInfo",
                        EXCEPTIONS: "application/vnd.ogc.se_xml",
                        BBOX: map.getExtent().toBBOX(),
                        SERVICE: "WMS",
                        INFO_FORMAT: 'text/html',
                        QUERY_LAYERS: map.layers[0].params.LAYERS,
                        FEATURE_COUNT: 50,
                        Layers: 'topp:states',
                        WIDTH: map.size.w,
                        HEIGHT: map.size.h,
                        format: format,
                        styles: map.layers[0].params.STYLES,
                        srs: map.layers[0].params.SRS};
                    
                    // handle the wms 1.3 vs wms 1.1 madness
                    if(map.layers[0].params.VERSION == "1.3.0") {
                        params.version = "1.3.0";
                        params.j = parseInt(e.xy.x);
                        params.i = parseInt(e.xy.y);
                    } else {
                        params.version = "1.1.1";
                        params.x = parseInt(e.xy.x);
                        params.y = parseInt(e.xy.y);
                    }
                        
                    // merge filters
                    if(map.layers[0].params.CQL_FILTER != null) {
                        params.cql_filter = map.layers[0].params.CQL_FILTER;
                    } 
                    if(map.layers[0].params.FILTER != null) {
                        params.filter = map.layers[0].params.FILTER;
                    }
                    if(map.layers[0].params.FEATUREID) {
                        params.featureid = map.layers[0].params.FEATUREID;
                    }
                    OpenLayers.loadURL("http://localhost:8888/geoserver/topp/wms", params, this, setHTML, setHTML);
                    OpenLayers.Event.stop(e);
                });
            }
            
            // sets the HTML provided into the nodelist element
            function setHTML(response){
                document.getElementById('nodelist').innerHTML = response.responseText;
            };
            
            // shows/hide the control panel
            function toggleControlPanel(event){
                var toolbar = document.getElementById("toolbar");
                if (toolbar.style.display == "none") {
                    toolbar.style.display = "block";
                }
                else {
                    toolbar.style.display = "none";
                }
                event.stopPropagation();
                map.updateSize()
            }
            
            // Tiling mode, can be 'tiled' or 'untiled'
            function setTileMode(tilingMode){
                if (tilingMode == 'tiled') {
                    untiled.setVisibility(false);
                    tiled.setVisibility(true);
                    map.setBaseLayer(tiled);
                }
                else {
                    untiled.setVisibility(true);
                    tiled.setVisibility(false);
                    map.setBaseLayer(untiled);
                }
            }
            
            // Transition effect, can be null or 'resize'
            function setTransitionMode(transitionEffect){
                if (transitionEffect === 'resize') {
                    tiled.transitionEffect = transitionEffect;
                    untiled.transitionEffect = transitionEffect;
                }
                else {
                    tiled.transitionEffect = null;
                    untiled.transitionEffect = null;
                }
            }
            
            // changes the current tile format
            function setImageFormat(mime){
                // we may be switching format on setup
                if(tiled == null)
                  return;
                  
                tiled.mergeNewParams({
                    format: mime
                });
                untiled.mergeNewParams({
                    format: mime
                });
                /*
                var paletteSelector = document.getElementById('paletteSelector')
                if (mime == 'image/jpeg') {
                    paletteSelector.selectedIndex = 0;
                    setPalette('');
                    paletteSelector.disabled = true;
                }
                else {
                    paletteSelector.disabled = false;
                }
                */
            }
            
            // sets the chosen style
            function setStyle(style){
                // we may be switching style on setup
                if(tiled == null)
                  return;
                  
                tiled.mergeNewParams({
                    styles: style
                });
                untiled.mergeNewParams({
                    styles: style
                });
            }
            
            // sets the chosen WMS version
            function setWMSVersion(wmsVersion){
                // we may be switching style on setup
                if(wmsVersion == null)
                  return;
                  
                if(wmsVersion == "1.3.0") {
                   origin = map.maxExtent.bottom + ',' + map.maxExtent.left;
                } else {
                   origin = map.maxExtent.left + ',' + map.maxExtent.bottom;
                }
                  
                tiled.mergeNewParams({
                    version: wmsVersion,
                    tilesOrigin : origin
                });
                untiled.mergeNewParams({
                    version: wmsVersion
                });
            }
            
            function setAntialiasMode(mode){
                tiled.mergeNewParams({
                    format_options: 'antialias:' + mode
                });
                untiled.mergeNewParams({
                    format_options: 'antialias:' + mode
                });
            }
            
            function setPalette(mode){
                if (mode == '') {
                    tiled.mergeNewParams({
                        palette: null
                    });
                    untiled.mergeNewParams({
                        palette: null
                    });
                }
                else {
                    tiled.mergeNewParams({
                        palette: mode
                    });
                    untiled.mergeNewParams({
                        palette: mode
                    });
                }
            }
            
            function setWidth(size){
                var mapDiv = document.getElementById('map');
                var wrapper = document.getElementById('wrapper');
                
                if (size == "auto") {
                    // reset back to the default value
                    mapDiv.style.width = null;
                    wrapper.style.width = null;
                }
                else {
                    mapDiv.style.width = size + "px";
                    wrapper.style.width = size + "px";
                }
                // notify OL that we changed the size of the map div
                map.updateSize();
            }
            
            function setHeight(size){
                var mapDiv = document.getElementById('map');
                
                if (size == "auto") {
                    // reset back to the default value
                    mapDiv.style.height = null;
                }
                else {
                    mapDiv.style.height = size + "px";
                }
                // notify OL that we changed the size of the map div
                map.updateSize();
            }
            
            function updateFilter(){
                if(pureCoverage)
                  return;
            
                var filterType = document.getElementById('filterType').value;
                var filter = document.getElementById('filter').value;
                
                // by default, reset all filters
                var filterParams = {
                    filter: null,
                    cql_filter: null,
                    featureId: null
                };
                if (OpenLayers.String.trim(filter) != "") {
                    if (filterType == "cql") 
                        filterParams["cql_filter"] = filter;
                    if (filterType == "ogc") 
                        filterParams["filter"] = filter;
                    if (filterType == "fid") 
                        filterParams["featureId"] = filter;
                }
                // merge the new filter definitions
                mergeNewParams(filterParams);
            }
            
            function resetFilter() {
                if(pureCoverage)
                  return;
            
                document.getElementById('filter').value = "";
                updateFilter();
            }
            
            function mergeNewParams(params){
                tiled.mergeNewParams(params);
                untiled.mergeNewParams(params);
            }
        </script>
		<form name="dlProductForm" id="dlProductForm" 	 action="index.php" method="GET">
	
	
		</form>
		
		<?php
	}
}
?>