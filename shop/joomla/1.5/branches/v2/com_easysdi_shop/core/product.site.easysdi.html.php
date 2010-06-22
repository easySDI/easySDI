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
	
	function editProduct( $rowProduct,$id, $option ){
		
		global  $mainframe;
		
		 
		$database =& JFactory::getDBO(); 
		
		$user = JFactory::getUser();
		$partner = new partnerByUserId($database);
		$partner->load($user->id);
		if($partner->root_id == "")
		{
			$partner->root_id = 0;
		}
		
		//List of partner with the same root as current logged user
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, 
									 b.name AS text 
									 FROM 
									 #__easysdi_community_partner a,
									 #__users b 
									 where (a.root_id = $partner->root_id OR a.root_id = $partner->partner_id OR a.partner_id = $partner->partner_id OR a.partner_id = $partner->root_id)  
									 AND 
									 a.user_id = b.id 
									 AND a.partner_id IN (SELECT partner_id FROM #__easysdi_community_actor
								    					 WHERE role_id = (SELECT role_id FROM #__easysdi_community_role WHERE role_code ='PRODUCT'))
									 ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		JHTML::_('behavior.calendar');

		//List of partner with METADATA right 
		$metadata_partner = array();
		$metadata_partner[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery("SELECT a.partner_id AS value, 
								   b.name AS text 
								   FROM 
								    #__easysdi_community_partner a,
								    #__users b  
								    where (a.root_id = $partner->root_id OR a.root_id = $partner->partner_id OR a.partner_id = $partner->partner_id OR a.partner_id = $partner->root_id)
								    AND 
								    a.user_id = b.id 
								    AND a.partner_id IN (SELECT partner_id FROM #__easysdi_community_actor
								    					 WHERE role_id = (SELECT role_id FROM #__easysdi_community_role WHERE role_code ='METADATA'))
								    ORDER BY b.name");
		$metadata_partner = array_merge( $metadata_partner, $database->loadObjectList() );
		
		//List of partner with DIFFUSION right
		$database->setQuery("SELECT a.partner_id AS value, 
								   b.name AS text 
								   FROM 
								    #__easysdi_community_partner a,
								    #__users b  
								    where (a.root_id = $partner->root_id OR a.root_id = $partner->partner_id OR a.partner_id = $partner->partner_id OR a.partner_id = $partner->root_id)
								    AND 
								    a.user_id = b.id 
								    AND a.partner_id IN (SELECT partner_id FROM #__easysdi_community_actor
								    					 WHERE role_id = (SELECT role_id FROM #__easysdi_community_role WHERE role_code ='DIFFUSION'))
								    ORDER BY b.name");
		$diffusion_partner = array();
		$diffusion_partner[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$diffusion_partner = array_merge($diffusion_partner, $database->loadObjectList());
		
		jimport("joomla.utilities.date");
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		$standardlist = array();
		$standardlist[] = JHTML::_('select.option','0', JText::_("EASYSDI_TABS_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_standard  WHERE is_deleted =0 " );
		$standardlist= $database->loadObjectList() ;
		
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
			
 
		$baseMaplist = array();		
		$database->setQuery( "SELECT id AS value,  alias AS text FROM #__easysdi_basemap_definition " );
		$baseMaplist = $database->loadObjectList() ;
		
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		
		//Product treatment
		$treatmentTypeList = array();		
		$database->setQuery( "SELECT id AS value,  translation AS text FROM #__easysdi_product_treatment_type " );
		$treatmentTypeList = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		HTML_product::alter_array_value_with_JTEXT_($treatmentTypeList);
		
			
		?>
		
	<div id="page">
	<?php if($id)
	{ ?>
	<h2 class="contentheading"><?php echo JText::_("EASYSDI_TITLE_EDIT_PRODUCT"); ?></h2>
	<?php
	}
	else
	{ ?>
	<h2 class="contentheading"><?php echo JText::_("EASYSDI_TITLE_NEW_PRODUCT"); ?></h2>
	<?php
	} ?>
	<div class="contentin">
	<div class="editProductPane">
	<form action="index.php" method="post" name="productForm" id="productForm" class="productForm">
<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"productPane");
		?>
		<br/>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("EASYSDI_TEXT_IDENT"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_DATA_TITLE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="data_title" value="<?php echo $rowProduct->data_title; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PRODUCT_ID"); ?> : </td>
								<td><?php echo $rowProduct->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_CREATION_DATE"); ?> : </td>
								<?php $date = new JDate($rowProduct->creation_date); ?>
								<input type="hidden" name="creation_date" value="<?php echo $date->toMySQL() ?>" />								
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowProduct->creation_date)); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_UPDATE_DATE"); ?> : </td>						
								<?php $date = new JDate($rowProduct->update_date); ?>										
								<input type="hidden"  name="update_date" value="<?php echo $date->toMySQL() ?>" />
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowProduct->update_date)); ?></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PUBLISHED"); ?> : </td>
								<td><select class="inputbox" name="published" >								
								<option value="0" <?php if( $rowProduct->published == 0 ) echo "selected"; ?> ><?php echo JText::_("EASYSDI_FALSE"); ?></option>
								<option value="1" <?php if( $rowProduct->published == 1 ) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option>
								</select></td>																
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("EASYSDI_METADATA"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_METADATA_ID"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="metadata_id" value="<?php echo $rowProduct->metadata_id; ?>" /></td>
							</tr>
							<!-- 
							<tr>							
								<td class="ptitle"><?php //echo JText::_("EASYSDI_SUPPLIER_NAME"); ?> : </td>
								<td><?php //echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->partner_id ); ?></td>								
							</tr>
							 -->
							<tr>							
								<td class="ptitle"><?php echo JText::_("EASYSDI_METADATA_SUPPLIER_NAME"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$metadata_partner, 'metadata_partner_id', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->metadata_partner_id ); ?></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_DATA_STANDARD_ID"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$standardlist, 'metadata_standard_id', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->metadata_standard_id ); ?></td>																
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_METADATA_INTERNAL"); ?> : </td>
								<td><input id="metadata_internal" name="metadata_internal" value="1" type="checkbox" onchange="toggle_state(this);" <?php if ($rowProduct->metadata_internal) {echo "checked";};?> > </td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_METADATA_EXTERNAL"); ?> : </td>
								<td><input id="metadata_external" name="metadata_external" value="1" type="checkbox" onchange="toggle_state(this);" <?php if ($rowProduct->metadata_external) {echo "checked";};?> > </td>								
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("EASYSDI_DIFFUSION"); ?></legend>
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
						</script>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>							
								<td class="ptitle"><?php echo JText::_("EASYSDI_DIFFUSION_PARTNER_NAME"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$diffusion_partner, 'diffusion_partner_id', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->diffusion_partner_id ); ?></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_SURFACE_MIN"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="surface_min" value="<?php echo $rowProduct->surface_min; ?>" /></td>							
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_SURFACE_MAX"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="surface_max" value="<?php echo $rowProduct->surface_max; ?>" /></td>							
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_ORDERABLE"); ?> : </td>
								<td><select class="inputbox" name="orderable" >								
								<option value="0" <?php if( $rowProduct->orderable == 0 ) echo "selected"; ?> ><?php echo JText::_("EASYSDI_FALSE"); ?></option>
								<option value="1" <?php if( $rowProduct->orderable == 1 ) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option>
								</select></td>																
							</tr>			
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_DATA_INTERNAL"); ?> : </td>
								<td><input id="data_internal" name="internal" value="1" type="checkbox" onchange="toggle_state(this);" <?php if ($rowProduct->internal) {echo "checked";};?> > </td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_DATA_EXTERNAL"); ?> : </td>
								<td><input id="data_external" name="external" value="1" type="checkbox" onchange="toggle_state(this);" <?php if ($rowProduct->external) {echo "checked";};?> > </td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_DATA_FREE"); ?> : </td>
								<td><input name="is_free" value="1" type="checkbox" <?php if ($rowProduct->is_free) {echo "checked";};?> > </td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PRODUCT_TREATMENT"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$treatmentTypeList, 'treatment_type', 'size="1" class="inputbox"', 'value',  'text', $rowProduct->treatment_type ); ?></td>															
							</tr>
							<tr>							
								<td class="ptitle"><?php echo JText::_("EASYSDI_PRODUCT_NOTIFICATION_EMAIL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="500" name="notification_email" value="<?php echo $rowProduct->notification_email; ?>" /></td>								
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_PERIMETER"),"productPane");
				
		?>
		<br>
		
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php
		
?>
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("EASYSDI_TEXT_PERIMETER") ?></legend>
						<table width="100%">
							<tr>
<?php
			
				$perimeterList = array();
				$query = "SELECT id AS value, perimeter_name AS text FROM #__easysdi_perimeter_definition ";
				$database->setQuery( $query );
				$perimeterList = $database->loadObjectList() ;
				if ($database->getErrorNum()) {						
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						exit;					 			
				}		
	
		
				$selected = array();
				$query = "SELECT perimeter_id AS value FROM #__easysdi_product_perimeter WHERE product_id=".$rowProduct->id;				
				$database->setQuery( $query );
				$selected = $database->loadObjectList();
				if ($database->getErrorNum()) {						
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					exit;					 			
					}		
?>
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
									<th align="left"><?php echo JText::_("EASYSDI_PERIMETER_NAME") ?></th>
									<th align="center"><?php echo JText::_("EASYSDI_PERIMETER_AVAILABILITY") ?></th>
									<th align="center"><?php echo JText::_("EASYSDI_PERIMETER_HAS_BUFFER") ?></th>
								</tr>
								</thead>
								<tbody>
								   <?php
								   //echo JHTML::_("select.genericlist",$perimeterList, 'perimeter_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); 
								   
								   ?>
								
								   <?php 
							          foreach ($perimeterList as $curPerim){
									  $query = "SELECT * FROM #__easysdi_product_perimeter WHERE product_id=$rowProduct->id AND perimeter_id = $curPerim->value";				
									  $database->setQuery( $query );
									  $bufferRow = $database->loadObject() ;
									  if ($database->getErrorNum()) {						
										$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
									  }
?>
								  <tr>
								  	<td align="left"><?php  echo $curPerim->text; ?></td>
									<td align="center"><input type="checkbox" id="perimeter_<?php echo $curPerim->value;?>" name="perimeter_id[]" value="<?php  echo $curPerim->value ?>" <?php if ($bufferRow->product_id != "") echo "checked"?> onclick="productAvailability_change(this,<?php echo $curPerim->value;?>);"></td>
									<td align="center"><input type="checkbox" id="buffer_<?php echo $curPerim->value;?>" name="buffer[]" value="<?php  echo $curPerim->value ?>" <?php if ($bufferRow->isBufferAllowed == 1) echo "checked"; else if ($bufferRow->product_id == "") echo "disabled";?>></td>
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
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_PROPERTIES"),"productPane");
		
		?>
		<br>
		<table class="ProductPropreties">
			<tr>
				<td>
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
										   type_code
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
				
				foreach ($propertiesList as $curProperty)
				{
					?>
					
					<!--<tr><td><?php echo $curProperty->text; ?></td></tr> -->
					
					<?php
					
				$propertiesValueList = array();
				$query = "SELECT a.id as value, a.translation as text FROM #__easysdi_product_properties_values_definition a where a.properties_id =".$curProperty->property_id." order by a.order";				 
				$database->setQuery( $query );
				$propertiesValueList = $database->loadObjectList() ;
				if ($database->getErrorNum()) 
				{						
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
				}							
				HTML_product::alter_array_value_with_JTEXT_($propertiesValueList);					
					switch($curProperty->type_code){

					case "list":
						?>
						<tr><td>
						<fieldset class="fieldset_properties">
						<legend><?php echo $curProperty->text; ?></legend>
						<?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="'.count($propertiesValueList).'" multiple="true" class="inputbox"', 'value', 'text', $selected ); ?>
						</fieldset>
						</td></tr>
						<?php
						break;
						
					case "mlist":
						?>
						<tr><td>
						<fieldset class="fieldset_properties">
						<legend><?php echo $curProperty->text; ?></legend>
						<?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="'.count($propertiesValueList).'" multiple="true" class="inputbox"', 'value', 'text', $selected ); ?>
						</fieldset>
						</td></tr>
						<?php
						break;
					case "cbox":
						?>
						<tr><td>
						<fieldset class="fieldset_properties">
						<legend><?php echo $curProperty->text; ?></legend>
						<?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="'.count($propertiesValueList).'" multiple="true" class="inputbox"', 'value', 'text', $selected ); ?>
						</fieldset>
						</td></tr>
						<?php
						break;
						
					case "text":
					if ($curProperty->mandatory == 0 ){
						$propertiesTemp1[] = JHTML::_('select.option','-1', JText::_("EASYSDI_PROPERTY_NONE"));
						$propertiesTemp2[] = JHTML::_('select.option',$propertiesValueList[0]->value, JText::_("EASYSDI_PROPERTY_YES"));
						$propertiesValueList = array_merge( $propertiesTemp1 , $propertiesTemp2);
						}
						?>
						<tr><td>
						<fieldset class="fieldset_properties">
						<legend><?php echo $curProperty->text; ?></legend>
						<?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'class="inputbox"', 'value', 'text', $selected ); ?>
						</fieldset>
						</td></tr>
						<?php
						break;
						
					case "textarea":
					if ($curProperty->mandatory == 0 ){
						$propertiesValueList2[] = JHTML::_('select.option','-1', JText::_("EASYSDI_PROPERTY_NONE"));
						$propertiesValueList3[] = JHTML::_('select.option',$propertiesValueList[0]->value, JText::_("EASYSDI_PROPERTY_YES"));
						
						$propertiesValueList = array_merge( $propertiesValueList2 , $propertiesValueList3);
							
						}
						?>
						<tr><td>
						<fieldset class="fieldset_properties">
						<legend><?php echo $curProperty->text; ?></legend>
						<?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'class="inputbox" size="1" ', 'value', 'text', $selected ); ?>
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
						$query = "SELECT a.id as value, a.text as text FROM #__easysdi_product_properties_values_definition a where a.properties_id =".$curProperty->property_id." order by a.order";				 
						$database->setQuery( $query );
						$res = $database->loadObjectList() ;
						if ($database->getErrorNum()) 
						{
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
						}
						if ($curProperty->mandatory == 0 )
						{
							$propertiesValueList3[] = JHTML::_('select.option','-1', JText::_("EASYSDI_PROPERTY_NONE") );
							$propertiesValueList = array_merge( $res , $propertiesValueList3  );
						}
						?>
						<tr><td>
						<fieldset class="fieldset_properties">
						<legend><?php echo $curProperty->text; ?></legend>
						<table>
						<tr><td>
						<?php echo JHTML::_("select.genericlist",$res, 'properties_id[]', 'class="inputbox" size="3" onchange="messageContents_change(this);"', 'value', 'text', $selected ); ?>
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
				echo $tabs->startPanel(JText::_("EASYSDI_PREVIEW"),"productPane");
		?>
		<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("EASYSDI_PREVIEW"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">						
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PREVIEW_BASE_MAP_ID"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$baseMaplist, 'previewBaseMapId', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->previewBaseMapId ); ?></td>																
							</tr>							
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PREVIEW_WMS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewWmsUrl" value="<?php echo $rowProduct->previewWmsUrl; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PREVIEW_WMS_LAYERS"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewWmsLayers" value="<?php echo $rowProduct->previewWmsLayers; ?>" /></td>								
							</tr>							
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PREVIEW_MIN_RESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewMinResolution" value="<?php echo $rowProduct->previewMinResolution; ?>" /></td>								
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PREVIEW_MAX_RESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewMaxResolution" value="<?php echo $rowProduct->previewMaxResolution; ?>" /></td>								
							</tr>
							
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PREVIEW_PROJECTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewProjection" value="<?php echo $rowProduct->previewProjection; ?>" /></td>								
							</tr>
							
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PREVIEW_UNIT"); ?> : </td>
								<td><select class="inputbox" name="previewUnit" >								
									<option <?php if($rowProduct->previewUnit == 'm') echo "selected" ; ?> value="m"> <?php echo JText::_("EASYSDI_PREVIEW_METERS"); ?></option>
									<option <?php if($rowProduct->previewUnit == 'degrees') echo "selected" ; ?> value="degrees"> <?php echo JText::_("EASYSDI_PREVIEW_DEGREES"); ?></option>
								</select>
								</td>																						
							</tr>
							
							<tr>
								<td class="ptitle"><?php echo JText::_("EASYSDI_PREVIEW_IMAGE_FORMAT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="previewImageFormat" value="<?php echo $rowProduct->previewImageFormat; ?>" /></td>								
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
		</form>
		</div>
		<table>
			<tr>
				<td>
					<button type="button" onClick="document.getElementById('productForm').task.value='saveProduct';document.getElementById('productForm').submit();" ><?php echo JText::_("EASYSDI_SAVE_PRODUCT"); ?></button>		
				</td>
				<td>
					<button type="button" onClick="document.getElementById('productForm').task.value='cancelEditProduct';document.getElementById('productForm').submit();" ><?php echo JText::_("EASYSDI_CANCEL_EDIT_PRODUCT"); ?></button>
				</td>
			</tr>
		</table>
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
	
	function editMetadata( $rowProduct,$id, $option ){
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		JHTML::_('behavior.calendar');

		
		
		jimport("joomla.utilities.date");
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		
		$catalogUrlBase = config_easysdi::getValue("catalog_url");				
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$rowProduct->metadata_id;
				
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
 
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		
		//$geoMD = new geoMetadata($cswResults ->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_Metadata"  )->item(0));
		$geoMD = new geoMetadata($cswResults);				 
		
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		
			
		?>
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_TITLE_EDIT_METADATA"); ?></h2>
		<div class="contentin">	

	<form action="index.php" method="post" name="productForm" id="productForm" class="productForm">
<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"productPane");

		?>		
		<br>
	<table width ="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_EASYSDI_GENERIC_METADATA"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_PRODUCT_ID"); ?> : </td>
								<td><?php echo $rowProduct->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>
			
							<tr>
								<td><?php echo JText::_("EASYSDI_METADATA_ID"); ?> : </td>
								<td><?php echo $rowProduct->metadata_id; ?></td>
								<input type="hidden"  name="metadata_id" value="<?php echo $rowProduct->metadata_id; ?>" />
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_UPDATE_DATE"); ?> : </td>						
								<?php $date = new JDate($rowProduct->update_date); ?>										
								<input type="hidden"  name="update_date" value="<?php echo $date->toMySQL() ?>" />
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowProduct->update_date)); ?></td>								
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_CREATION_DATE"); ?> : </td>
								<?php $date = new JDate($rowProduct->creation_date); ?>
								<input type="hidden" name="creation_date" value="<?php echo $date->toMySQL() ?>" />								
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowProduct->creation_date)); ?></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_SUPPLIER_NAME"); ?> : </td>
								<?php
									$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.user_id = b.id AND partner_id=".$rowProduct->partner_id ;
									$database->setQuery($query);				 
		 						?>
								<td><?php echo $database->loadResult(); ?></td>																																
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_METADATA_SUPPLIER_NAME"); ?> : </td>
								<?php
									$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.user_id = b.id AND partner_id=".$rowProduct->metadata_partner_id ;
									$database->setQuery($query);				 
		 						?>
								<td><?php echo $database->loadResult(); ?></td>									
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_DATA_FREE"); ?> : </td>								
								<td><input type="checkbox" <?php if ($rowProduct->is_free == 1) echo "checked"; ?> disabled/> </td>
																																								
							</tr>
							
							
							
							<tr>
								<td><?php echo JText::_("EASYSDI_DATA_STANDARD_ID"); ?> : </td>
								<?php
									$query = "SELECT name FROM #__easysdi_metadata_standard  where is_deleted =0 AND id =".$rowProduct->metadata_standard_id ;
									$database->setQuery($query);				 
		 						?>
								<td><?php echo $database->loadResult(); ?></td>																																								
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			
		</table>
			
		
		<?php
		echo $tabs->endPanel();
	
		/* Faire autant d'onglets qu'il y a de tab associï¿½s ï¿½ la mï¿½tadonnï¿½e */
		$query = "SELECT b.text as text,a.tab_id as tab_id FROM #__easysdi_metadata_standard_classes a, #__easysdi_metadata_tabs b where a.tab_id =b.id AND (a.standard_id = $rowProduct->metadata_standard_id or a.standard_id in (select inherited from #__easysdi_metadata_standard where is_deleted =0 AND inherited !=0 and  id = $rowProduct->metadata_standard_id)) group by a.tab_id " ;
		$database->setQuery($query);				 
		$rows = $database->loadObjectList();		
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
		}
				
		foreach ($rows as $row)
		{
			echo $tabs->startPanel(JText::_($row->text),"productPane");
			?>
			<br>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<?php
							/* Afficher toutes les classes associï¿½es aux onglets */
							$query = "SELECT  * FROM #__easysdi_metadata_standard_classes a, #__easysdi_metadata_classes b where a.class_id =b.id and a.tab_id = $row->tab_id and (a.standard_id = $rowProduct->metadata_standard_id or a.standard_id in (select inherited from #__easysdi_metadata_standard where  is_deleted =0 AND inherited !=0 and id = $rowProduct->metadata_standard_id)) order by position" ;
							
							$database->setQuery($query);				 
							$rowsClasses = $database->loadObjectList();		
							if ($database->getErrorNum()) 
							{						
								$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
							}
							
							foreach ($rowsClasses as $rowClasses)
							{
								?>				
								<div id="fieldset<?php echo $rowClasses->iso_key."__0"; ?>">
								<fieldset>
									<legend><?php echo JText::_($rowClasses->description); ?><a href="#" onClick="var node1 = document.getElementById('fieldset<?php echo $rowClasses->iso_key."__0"; ?>');var node2 = node1.cloneNode(true);var container = document.getElementById('container<?php echo $rowClasses->iso_key."__0"; ?>');container.appendChild(node2);">+</a></legend>
									<table border="0" cellpadding="3" cellspacing="0">														
										<?php helper_easysdi::generateMetadataHtml($rowClasses,$row->tab_id,$rowProduct->metadata_standard_id,$rowClasses->iso_key,$geoMD,$rowProduct->metadata_id);  ?>
									</table>	
								</fieldset>
								</div>
								<div id="container<?php echo $rowClasses->iso_key."__0"; ?>"></div>
								<?php 
							}
						?>			
					</td>
				</tr>
			</table>
			<?php 
			echo $tabs->endPanel();						
		}		
		
		echo $tabs->endPane();	

		?>
		<input type="hidden" name="standard_id" value="<?php echo $rowProduct->metadata_standard_id; ?>" />
		<input type="hidden" name="product_id" value="<?php echo $rowProduct->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" id="task" name="task" value="cancelEditProductMetadata">

		</form>
		
		<button type="button" onClick="document.getElementById('task').value='saveProductMetadata';document.getElementById('productForm').submit();" ><?php echo JText::_("EASYSDI_SAVE_PRODUCT"); ?></button>			
		<button type="button" onClick="document.getElementById('task').value='cancelEditProductMetadata';document.getElementById('productForm').submit();" ><?php echo JText::_("EASYSDI_CANCEL_EDIT_PRODUCT"); ?></button>
	<?php
	}
	
	
	
	function editMetadata2( $rowProduct,$id, $option ){
		
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		JHTML::_('behavior.calendar');

		
		
		jimport("joomla.utilities.date");
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		
		$catalogUrlBase = config_easysdi::getValue("catalog_url");				
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&id=".$rowProduct->metadata_id;
	      //$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&outputSchema=csw:IsoRecord&elementSetName=full&id=".$id;
	      	
		
		if ($_FILES)
		 	$fileName = $_FILES['isoFile']["name"];
		 else
		 	$fileName = "";
		 if (strlen($fileName)>0){
			 	$catalogUrlGetRecordById =  $_FILES['isoFile']["tmp_name"];
		
		 }	 	
			

		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
				
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		
		//$geoMD = new geoMetadata($cswResults ->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_Metadata"  )->item(0));
		$geoMD = new geoMetadata($cswResults);				 

		// Compter les occurences de la balise gmd:fileIndentifier dans la mï¿½tadonnï¿½e
		$count  = $geoMD->isXPathResultCount("//gmd:fileIdentifier");
		if ($count == 0)
		{
			$mainframe->redirect("index.php?option=$option&task=editMetadata&id=$id" );	
			exit;
		}
		
		jimport('joomla.html.pane');
		
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
			
		?>				
	<form action="index.php" method="post" name="productForm" id="productForm" class="productForm" >
	<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"productPane");

		?>		
		<br>
		<table width ="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_EASYSDI_GENERIC_METADATA"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_PRODUCT_ID"); ?> : </td>
								<td><?php echo $rowProduct->id; ?><input type="hidden" name="id" value="<?php echo $id;?>"></td>
							</tr>
			
							<tr>
								<td><?php echo JText::_("EASYSDI_METADATA_ID"); ?> : </td>
								<td><?php echo $rowProduct->metadata_id; ?><input type="hidden"  name="metadata_id" value="<?php echo $rowProduct->metadata_id; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_UPDATE_DATE"); ?> : </td>						
								<?php $date = new JDate($rowProduct->update_date); ?>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowProduct->update_date)); ?><input type="hidden"  name="update_date" value="<?php echo $date->toMySQL() ?>" /></td>								
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_CREATION_DATE"); ?> : </td>
								<?php $date = new JDate($rowProduct->creation_date); ?>						
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowProduct->creation_date)); ?><input type="hidden" name="creation_date" value="<?php echo $date->toMySQL() ?>" /></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_SUPPLIER_NAME"); ?> : </td>
								<?php
									$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.user_id = b.id AND partner_id=".$rowProduct->partner_id ;
									$database->setQuery($query);				 
		 						?>
								<td><?php echo $database->loadResult(); ?></td>																																
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_METADATA_SUPPLIER_NAME"); ?> : </td>
								<?php
									$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.user_id = b.id AND partner_id=".$rowProduct->metadata_partner_id ;
									$database->setQuery($query);				 
		 						?>
								<td><?php echo $database->loadResult(); ?></td>									
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_DATA_FREE"); ?> : </td>								
								<td><input type="checkbox" <?php if ($rowProduct->is_free == 1) echo "checked"; ?> disabled/> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DATA_STANDARD_ID"); ?> : </td>
								<?php
									$query = "SELECT name FROM #__easysdi_metadata_standard  where is_deleted =0 AND id =".$rowProduct->metadata_standard_id ;
									$database->setQuery($query);				 
		 						?>
								<td><?php echo $database->loadResult(); ?></td>																																								
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
		echo $tabs->endPanel();
	
		/* Faire autant d'onglets qu'il y a de tab associï¿½s ï¿½ la mï¿½tadonnï¿½e */
		$query = "SELECT b.text as text,a.tab_id as tab_id FROM #__easysdi_metadata_standard_classes a, #__easysdi_metadata_tabs b where a.tab_id =b.id AND (a.standard_id = $rowProduct->metadata_standard_id or a.standard_id in (select inherited from #__easysdi_metadata_standard where is_deleted =0 AND inherited !=0 and  id = $rowProduct->metadata_standard_id)) group by a.tab_id " ;
		$database->setQuery($query);				 
		$rows = $database->loadObjectList();		
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");	
			echo $database->getErrorMsg(); 						
			exit;	
		}
				
		foreach ($rows as $row)
		{	
			echo $tabs->startPanel(JText::_($row->text),"productPane");
			?>
			<br>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_($row->text); ?></legend>
						
					<?php 
					/* Afficher toutes les classes associï¿½es aux onglets */
					$query = "SELECT c.id,c.name as name,c.description as description,c.iso_key as iso_key, c.type as type FROM #__easysdi_metadata_classes c,#__easysdi_metadata_standard_classes a, #__easysdi_metadata_tabs b where a.tab_id =b.id and (a.standard_id = $rowProduct->metadata_standard_id or a.standard_id in (select inherited from #__easysdi_metadata_standard where is_deleted =0 AND inherited !=0 and id = $rowProduct->metadata_standard_id)) and c.id = a.class_id and a.tab_id = $row->tab_id" ;		
					$database->setQuery($query);				 
					$rowstab = $database->loadObjectList();		
					if ($database->getErrorNum()) {						
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
					}
					//print_r($rowstab);
					foreach ($rowstab as $rowtab)
					{
						/* Retrouver dans la mï¿½tadonnï¿½e la balise correspondant ï¿½ la classe qu'on veut ï¿½diter */
						$count  = $geoMD->isXPathResultCount("//".$rowtab->iso_key);
						
						for ($i=0 ;$i<$count;$i++)
						{
							?>
	  						<div id="fieldset<?php echo $rowtab->iso_key."__".$i; ?>">
								<fieldset>
									<legend><?php echo JText::_($rowtab->description); ?>
										<?php 
										/* Afficher la possibilitï¿½ d'insï¿½rer un nouvel ï¿½lï¿½ment de ce type ou pas */
										if ($rowtab->type == "class")
										{ 
										?>
										<a href="#" onClick="var node1 = document.getElementById('fieldset<?php echo $rowtab->iso_key."__".$i; ?>');var node2 = node1.cloneNode(true);var container = document.getElementById('container<?php echo $rowtab->iso_key."__".$i; ?>');container.appendChild(node2);">+</a>
										<?php 
										}
										?>
									</legend>
									<table width="100%"><?php helper_easysdi::generateMetadataHtml2($rowtab->id,$geoMD,$rowtab->iso_key."[".($i+1)."]", $rowProduct->metadata_id,1);?></table>
								</fieldset>
							</div>
							<div id="container<?php echo $rowtab->iso_key."__".$i; ?>"></div> 							 
							 <?php						
						}
					}	
					 ?>	
					</fieldset>
					</td>
					</tr>
				</table>
			<?php 
			echo $tabs->endPanel();						
		}			
		?>
		<input type="hidden" name="standard_id" value="<?php echo $rowProduct->metadata_standard_id; ?>" />
		<input type="hidden" name="product_id" value="<?php echo $rowProduct->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" id="task" name="task" value="cancelEditProductMetadata">
		<input type="hidden" id="limit" name="limit" value="<?php echo JRequest::getVar("limit"); ?>">
		<input type="hidden" id="limitstart" name="limitstart" value="<?php echo JRequest::getVar("limitstart"); ?>">

		</form>
		<?php
			echo $tabs->startPanel(JText::_("EASYSDI_UPLOADMETADATA"),"productPane");
			?>
			<br>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<fieldset>
							<legend><?php echo JText::_("EASYSDI_UPLOADMETADATA") ?></legend>				
							<table width="100%">
								<tr><td><?php echo JText::_("EASYSDI_METADATA_FILE") ;?></td><td> 
								<form action="index.php?option=<?php echo $option; ?>&id=<?php echo $rowProduct->id; ?>&standard_id=<?php echo $rowProduct->metadata_standard_id; ?>&task=editMetadata2" method="post" name="loadMetadataForm" id="loadMetadataForm" class="productForm" enctype="multipart/form-data">
									<input type="file" name="isoFile" >
									<button onClick="document.getElementById('loadMetadataForm').submit()" ><?php echo JText::_("EASYSDI_LOAD_METADATA_FILE") ;?></button>
								</form>
			
								</td></tr>
							</table>						
						</fieldset>
					</td>
				</tr>
		</table>		
		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
		?>
		<button type="button" onClick="document.getElementById('productForm').task.value='saveProductMetadata';document.getElementById('productForm').submit();" ><?php echo JText::_("EASYSDI_SAVE_PRODUCT"); ?></button>			
		<button type="button" onClick="document.getElementById('productForm').task.value='cancelEditProductMetadata';document.getElementById('productForm').submit();" ><?php echo JText::_("EASYSDI_CANCEL_EDIT_PRODUCT"); ?></button>
		
	<?php
	}
	
	
	function listProduct($pageNav,$rows,$option,$rootPartner,$search){
		?>	
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_LIST_PRODUCT"); ?></h2>
		<div class="contentin">
		<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
		<form action="index.php" method="GET" id="productListForm" name="productListForm">
	
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_SHOP_FILTER_TITLE");?></b>&nbsp;
				</td>
				<td align="left">
					<input type="text" name="searchProduct" value="<?php echo $search;?>" class="inputboxSearchProduct"/></td>
				<td align="right">
					<button type="submit" class="searchButton" > <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
				</td>
			</tr>
			<tr>
				<td colspan="3" align="right">
					<button id="newProductBtn" type="button" onClick="document.getElementById('task<?php echo $option; ?>').value='newProduct';document.getElementById('productListForm').submit();" ><?php echo JText::_("EASYSDI_NEW_PRODUCT"); ?></button>
				</td>
			</tr>
		</table>
		<br/>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
				<td align="center"><?php echo JText::_("EASYSDI_SHOP_DISPLAY"); ?> <?php echo $pageNav->getLimitBox(); ?></td>
				<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
	<script>
		function suppressProduct_click(id){
			conf = confirm('<?php echo JText::_("EASYSDI_SHOP_CONFIRM_PRODUCT_DELETE"); ?>');
			if(!conf)
				return false;
			window.open('./index.php?option=com_easysdi_shop&task=suppressProduct&limitstart=<?php echo JRequest::getVar("limitstart"); ?>&limit=<?php echo JRequest::getVar("limit"); ?>&publishedobject=product&cid[]='+id, '_self');
		}
	</script>
	<?php
	if(count($rows) == 0){
		echo "<table><tbody><tr><td colspan=\"7\">".JText::_("EASYSDI_NO_RESULT_FOUND")."</td>";
	}else{?>
	<table id="myProducts" class="box-table">
	<thead>
	<tr>
	<th class="logo2"></th>
	<th><?php echo JText::_('EASYSDI_PRODUCT_NAME'); ?></th>
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
			<td class="logo2"><div <?php if($row->external && $row->orderable == 1) echo 'title="'.JText::_("EASYSDI_SHOP_INFOLOGO_ORDERABLE").'" class="easysdi_product_exists"'; else if($row->internal && $row->orderable == 1) echo 'title="'.JText::_("EASYSDI_SHOP_INFOLOGO_ORDERABLE_INTERNAL").'" class="easysdi_product_exists_internal"';?>></div></td>
			<td width="100%"><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"> <?php echo $row->data_title ;?></a></td>
			
			<td class="logo"><div title="<?php echo JText::_('EASYSDI_SHOP_ACTION_EDIT_PRODUCT'); ?>" id="editProduct" onClick="window.open('./index.php?option=com_easysdi_shop&task=editProduct&id=<?php echo $row->id;?>&limitstart=<?php echo JRequest::getVar("limitstart"); ?>&limit=<?php echo JRequest::getVar("limit"); ?>', '_self');"/></td>
			<td class="logo"><div title="<?php echo JText::_('EASYSDI_SHOP_ACTION_DELETE_PRODUCT'); ?>" id="deleteProduct" onClick="return suppressProduct_click('<?php echo $row->id; ?>');" /></td>
			
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
			
			<?php if (userManager::hasRight($rootPartner->partner_id,"INTERNAL")){?> 
			<?php }  ?>
		</form>
		</div>
		</div>
	<?php
		
		
	}
	
	
	
	function listProductMetadata($pageNav,$rows,$option,$rootPartner,$search){
		?>
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_TITLE_LIST_METADATA"); ?></h2>
		<div class="contentin">
		<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
		
		<form action="index.php" method="GET" id="productListForm" name="productListForm">
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_SHOP_FILTER_TITLE");?></b>&nbsp;
				</td>
				<td align="left">
					<input type="text" name="search" value="<?php echo $search;?>" class="inputboxSearchProduct" " />
				</td>
				<td align="right">
					<button type="submit" class="searchButton" > <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
				</td>
			</tr>
		</table>
		<br>	
		<!-- pageNav header -->
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
	if(count($rows) == 0){
		echo "<table><tbody><tr><td colspan=\"7\">".JText::_("EASYSDI_NO_RESULT_FOUND")."</td>";
	}else{?>
	<table class="box-table" id="manageMetadata">
	<thead>
	<tr>
	<th></th>
	<th><?php echo JText::_('EASYSDI_PRODUCT_NAME'); ?></th>
	<th></th>
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
			<td class="logo2"><div <?php if($row->metadata_external) echo 'class="publicMd" title="'.JText::_("EASYSDI_METADATA_EXTERNAL").'"'; else if($row->metadata_internal && !$row->metadata_external) echo '"class="privateMd" title="'.JText::_("EASYSDI_METADATA_INTERNAL").'"';?>></div></td>
			<!--<td><input type="radio"  name="id" value="<?php echo $row->id ;?>" onClick="document.getElementById('task<?php echo $option; ?>').value='editMetadata<?php if ($row->hasMetadata == 1) echo "2";?>'"></td>	-->					
			<td><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"> <?php echo $row->data_title ;?></a></td>
			<td class="logo" align="center"><div title="<?php echo JText::_('EASYSDI_ACTION_EDIT_METADATA'); ?>" id="editMetadata" onClick="document.getElementById('task<?php echo $option; ?>').value='editMetadata<?php if ($row->hasMetadata == 1) echo "2";?>';document.getElementById('id').value='<?php echo $row->id ;?>';document.getElementById('productListForm').submit();"/></td>
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
			
			<input type="hidden" id="id" name="id" value="">
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listProductMetadata">
			<?php
			if (userManager::hasRight($rootPartner->partner_id,"METADATA")){?>
			
			<!-- <button type="button" onClick="document.getElementById('productListForm').submit();" ><?php echo JText::_("EASYSDI_EDIT_METADATA"); ?></button> -->
			<?php }?>
		</form>
		</div>
		</div>
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