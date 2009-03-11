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
		
		jimport("joomla.utilities.date");
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		$standardlist = array();
		$standardlist[] = JHTML::_('select.option','0', JText::_("EASYSDI_TABS_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_standard  WHERE is_deleted =0 " );
		$standardlist= $database->loadObjectList() ;
		
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
			
 
						 
		
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		
			
		?>				
	<form action="index.php" method="post" name="productForm" id="productForm" class="productForm">
<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"productrPane");

		?>		
		<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_EASYSDI_GENERIC_PRODUCT"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_PRODUCT_ID"); ?> : </td>
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
								<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->partner_id ); ?></td>								
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_METADATA_SUPPLIER_NAME"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$metadata_partner, 'metadata_partner_id', 'size="1" class="inputbox"', 'value', 'text', $rowProduct->metadata_partner_id ); ?></td>								
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
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_PERIMETER"),"productrPane");
				
		?>
		<br>
		
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php
		
?>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_TEXT_PERIMETER") ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
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
								<td><?php echo JHTML::_("select.genericlist",$perimeterList, 'perimeter_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>
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
								if ($database->getErrorNum()) {						
											$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
									}										
						?>
							<tr>
								<td><?php  echo $curPerim->text; ?></td>
								<td><input type="checkbox" name="buffer[]" value="<?php  echo $curPerim->value ?>" <?php if ($bufferRow->isBufferAllowed == 1) echo "checked"?>></td>
							</tr>
							<?php } ?>
						</table>
						</fieldset>
				</td>
			</tr>
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_PROPERTIES"),"productrPane");
		
		
		?>
		<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php
		
?>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_TEXT_PROPERTIES") ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
<?php
			
																
				$selected = array();
				$query = "SELECT property_value_id as value FROM #__easysdi_product_property WHERE product_id=".$rowProduct->id;				
				$database->setQuery( $query );
	
				$selected = $database->loadObjectList();
				if ($database->getErrorNum()) {						
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
				}		

				
				$queryProperties = "SELECT b.id as property_id, b.text as text,type_code FROM #__easysdi_product_properties_definition b order by b.order";
				$database->setQuery( $queryProperties );
				$propertiesList = $database->loadObjectList() ;
				if ($database->getErrorNum()) {						
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
					}		
					foreach ($propertiesList as $curProperty){
						?><td><?php echo JText::_($curProperty->text); ?></td>
						<?php
					}
					
					?>
					</tr>
					<tr>
					<?php
					foreach ($propertiesList as $curProperty){
					
				$propertiesValueList = array();
				$query = "SELECT a.id as value, a.text as text FROM #__easysdi_product_properties_values_definition a where a.properties_id =".$curProperty->property_id." order by a.order";				 
				$database->setQuery( $query );
				$propertiesValueList = $database->loadObjectList() ;
				if ($database->getErrorNum()) {						
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
					}												
					switch($curProperty->type_code){

					case "list":
						?>
						<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>							
						<?php
						break;
						
					case "mlist":
						?>
						<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>
						<?php
						break;
					case "cbox":
						?>
						<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>
						<?php
						break;
						
					case "text":
					if ($curProperty->mandatory == 0 ){
						$propertiesValueList1[] = JHTML::_('select.option','-1', JText::_("EASYSDI_PROPERTY_NONE") );
						$propertiesValueList = array_merge( $propertiesValueList , $propertiesValueList1  );
							
						}
						?>
						<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', '', 'value', 'text', $selected ); ?></td>
						<?php
						break;
						
					case "textarea":
					if ($curProperty->mandatory == 0 ){
						$propertiesValueList1[] = JHTML::_('select.option','-1', JText::_("EASYSDI_PROPERTY_NONE") );
						$propertiesValueList = array_merge( $propertiesValueList , $propertiesValueList1  );
							
						}
						?>
						<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="1" ', 'value', 'text', $selected ); ?></td>
						<?php
						break;
					}	
					 } ?>								
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
		</form>
		<button type="button" onClick="document.getElementById('task').value='saveProduct';document.getElementById('productForm').submit();" ><?php echo JText::_("EASYSDI_SAVE_PRODUCT"); ?></button>			
		<button type="button" onClick="document.getElementById('task').value='cancelEditProduct';document.getElementById('productForm').submit();" ><?php echo JText::_("EASYSDI_CANCEL_EDIT_PRODUCT"); ?></button>
		
	<?php
	}
	
	
	function editMetadata( $rowProduct,$id, $option ){
		
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
	<form action="index.php" method="post" name="productForm" id="productForm" class="productForm">
<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"productrPane");

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
	
		
		$query = "SELECT b.text as text,a.tab_id as tab_id FROM #__easysdi_metadata_standard_classes a, #__easysdi_metadata_tabs b where a.tab_id =b.id AND (a.standard_id = $rowProduct->metadata_standard_id or a.standard_id in (select inherited from #__easysdi_metadata_standard where is_deleted =0 AND inherited !=0 and  id = $rowProduct->metadata_standard_id)) group by a.tab_id " ;
		$database->setQuery($query);				 
		$rows = $database->loadObjectList();		
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
		}
				
		foreach ($rows as $row){
			
			echo $tabs->startPanel(JText::_($row->text),"productrPane");
			?>
			<br>
			<table border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td>
						
						<?php
							$query = "SELECT  * FROM #__easysdi_metadata_standard_classes a, #__easysdi_metadata_classes b where a.class_id =b.id and a.tab_id = $row->tab_id and (a.standard_id = $rowProduct->metadata_standard_id or a.standard_id in (select inherited from #__easysdi_metadata_standard where  is_deleted =0 AND inherited !=0 and id = $rowProduct->metadata_standard_id)) order by position" ;
							
							$database->setQuery($query);				 
							$rowsClasses = $database->loadObjectList();		
							if ($database->getErrorNum()) {						
									$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
							}
							
							foreach ($rowsClasses as $rowClasses){
								
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
							
							
							<?php } ?>
										
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
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$rowProduct->metadata_id;
				
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
 
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		
		//$geoMD = new geoMetadata($cswResults ->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_Metadata"  )->item(0));
		$geoMD = new geoMetadata($cswResults);				 
		
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		
			
		?>				
	<form action="index.php" method="post" name="productForm" id="productForm" class="productForm">
<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"productrPane");

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
	
		
		$query = "SELECT b.text as text,a.tab_id as tab_id FROM #__easysdi_metadata_standard_classes a, #__easysdi_metadata_tabs b where a.tab_id =b.id AND (a.standard_id = $rowProduct->metadata_standard_id or a.standard_id in (select inherited from #__easysdi_metadata_standard where is_deleted =0 AND inherited !=0 and  id = $rowProduct->metadata_standard_id)) group by a.tab_id " ;
		$database->setQuery($query);				 
		$rows = $database->loadObjectList();		
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
		}
				
		foreach ($rows as $row){
			
			echo $tabs->startPanel(JText::_($row->text),"productrPane");
			?>
			<br>
			<table border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_($row->text); ?></legend>
						
					<?php 
					
					
					$query = "SELECT c.id,c.name as name,c.description as description,c.iso_key as iso_key FROM #__easysdi_metadata_classes c,#__easysdi_metadata_standard_classes a, #__easysdi_metadata_tabs b where a.tab_id =b.id and (a.standard_id = $rowProduct->metadata_standard_id or a.standard_id in (select inherited from #__easysdi_metadata_standard where is_deleted =0 AND inherited !=0 and id = $rowProduct->metadata_standard_id)) and c.id = a.class_id and a.tab_id = $row->tab_id" ;		
					$database->setQuery($query);				 
					$rowstab = $database->loadObjectList();		
					if ($database->getErrorNum()) {						
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
					}				
					foreach ($rowstab as $rowtab){
						
						$count  = $geoMD->isXPathResultCount("//".$rowtab->iso_key);
						?>
						
						<?php 
						for ($i=0 ;$i<$count;$i++){
							?>
							
  						<div id="fieldset<?php echo $rowtab->iso_key."__".$i; ?>">
							<fieldset>
						<legend><?php echo JText::_($rowtab->description); ?><a href="#" onClick="var node1 = document.getElementById('fieldset<?php echo $rowtab->iso_key."__".$i; ?>');var node2 = node1.cloneNode(true);var container = document.getElementById('container<?php echo $rowtab->iso_key."__".$i; ?>');container.appendChild(node2);">+</a></legend>
							<table><?php helper_easysdi::generateMetadataHtml2($rowtab->id,$geoMD,$rowtab->iso_key."[".($i+1)."]", $rowProduct->metadata_id,1);?></table>
						</fieldset>
						</div>
						<div id="container<?php echo $rowtab->iso_key."__".$i; ?>"></div> 							 
							 <?php						
						}
						?>
						<?php
					}	
					 ?>
										
					</fieldset>
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
	
	
	function listProduct($pageNav,$rows,$option,$rootPartner){
		?>	
		<div class="contentin">
		<form action="index.php" method="GET" id="productListForm" name="productListForm">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_LIST_PRODUCT"); ?></h2>
	
		<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
	
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" " />			
				</td>
			</tr>
		</table>
		
		<button type="submit" class="searchButton" > <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
		<br>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td><td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
	
	
	<table>
	<thead>
	<tr>
	<th><?php echo JText::_('EASYSDI_PRODUCT_SHARP'); ?></th>
	<th></th>
	<th><?php echo JText::_('EASYSDI_PRODUCT_NAME'); ?></th>
	<th><?php echo JText::_('EASYSDI_PRODUCT_INTERNAL'); ?></th>
	<th><?php echo JText::_('EASYSDI_PRODUCT_EXTERNAL'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		foreach ($rows as $row)
		{	$i++;
			
			?>		
			<tr>
			<td><?php echo $i; ?></td>
			<td><input type="radio"  name="id" value="<?php echo $row->id ;?>"></td>						
			<td><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:650,y:550}}"> <?php echo $row->data_title ;?></a></td>
			
			<td><input type="checkbox" <?php if ($row->internal) {echo "checked";};?> disabled> </td>
			<td><input type="checkbox" <?php if ($row->external) {echo "checked";};?> disabled> </td>
			</tr>
			
				<?php		
		}
		
	?>
	</tbody>
	</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listProduct">
			
			<?php if (helper_easysdi::hasRight($rootPartner->partner_id,"INTERNAL")){?> 
			<button type="button" onClick="document.getElementById('task<?php echo $option; ?>').value='deleteProduct';document.getElementById('productListForm').submit();" ><?php echo JText::_("EASYSDI_ARCHIVE_PRODUCT"); ?></button>			
			<button type="button" onClick="document.getElementById('task<?php echo $option; ?>').value='editProduct';document.getElementById('productListForm').submit();" ><?php echo JText::_("EASYSDI_EDIT_PRODUCT"); ?></button>			
			<button type="button" onClick="document.getElementById('task<?php echo $option; ?>').value='newProduct';document.getElementById('productListForm').submit();" ><?php echo JText::_("EASYSDI_NEW_PRODUCT"); ?></button>
			<?php }  ?>
		</form>
		</div>
	<?php
		
		
	}
	
	
	
	function listProductMetadata($pageNav,$rows,$option,$rootPartner){
		?>	
		<div class="contentin">
		<form action="index.php" method="GET" id="productListForm" name="productListForm">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_LIST_PRODUCT"); ?></h2>
	
		<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
	
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" " />			
				</td>
			</tr>
		</table>
		
		<button type="submit" class="searchButton" > <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
		<br>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td><td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
	
	
	<table>
	<thead>
	<tr>
	<th><?php echo JText::_('EASYSDI_PRODUCT_SHARP'); ?></th>
	<th></th>
	<th><?php echo JText::_('EASYSDI_PRODUCT_NAME'); ?></th>
	<th><?php echo JText::_('EASYSDI_PRODUCT_INTERNAL'); ?></th>
	<th><?php echo JText::_('EASYSDI_PRODUCT_EXTERNAL'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		foreach ($rows as $row)
		{	$i++;
			
			?>		
			<tr>
			<td><?php echo $i; ?></td>
			<td><input type="radio"  name="id" value="<?php echo $row->id ;?>" onClick="document.getElementById('task<?php echo $option; ?>').value='editMetadata<?php if ($row->hasMetadata == 1) echo "2";?>'"></td>						
			<td><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo $row->data_title ;?></a></td>
			
			<td><input type="checkbox" <?php if ($row->internal) {echo "checked";};?> disabled> </td>
			<td><input type="checkbox" <?php if ($row->external) {echo "checked";};?> disabled> </td>
			</tr>
			
				<?php		
		}
		
	?>
	</tbody>
	</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listProduct">
			<?php
			if (helper_easysdi::hasRight($rootPartner->partner_id,"METADATA")){?>
			
			<button type="button" onClick="document.getElementById('productListForm').submit();" ><?php echo JText::_("EASYSDI_EDIT_METADATA"); ?></button>
			<?php }?>
		</form>
		</div>
	<?php
		
		
	}
	
}
?>