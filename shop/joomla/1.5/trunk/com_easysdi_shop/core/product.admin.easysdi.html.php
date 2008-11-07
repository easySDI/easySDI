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



class HTML_product {

	function editProduct( $rowProduct,$id, $option ){
		
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
 
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'geoMetadata.php');
		
		//$geoMD = new geoMetadata($cswResults ->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_Metadata"  )->item(0));
		$geoMD = new geoMetadata($cswResults);				 
		
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_PRODUCT"), 'generic.png' );
			
		?>				
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
		echo $tabs->startPane("productPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"productrPane");

		?>		
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_EASYSDI_GENERIC"); ?></legend>
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
<?php
		
?>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo $row->type_name ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
<?php
			
				$perimeterList = array();
				$query = "SELECT id AS value, perimeter_name AS text FROM #__easysdi_perimeter_definition ";
				$database->setQuery( $query );
				$perimeterList = $database->loadObjectList() ;
				if ($database->getErrorNum()) {						
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
				}		
	
		
				$selected = array();
				$query = "SELECT perimeter_id AS value FROM #__easysdi_product_perimeter WHERE product_id=".$rowProduct->id;				
				$database->setQuery( $query );
				$selected = $database->loadObjectList();
				if ($database->getErrorNum()) {						
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
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
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_PROPERTIES"),"productrPane");
		
		
		?>
		
		<table border="0" cellpadding="0" cellspacing="0">
<?php
		
?>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo $row->type_name ?></legend>
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

				
				$queryProperties = "SELECT b.id as property_id, b.text as text FROM #__easysdi_product_properties_definition b order by b.order";
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
?>
								<td><?php echo JHTML::_("select.genericlist",$propertiesValueList, 'properties_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>
						<?php } ?>								
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
				
		<?php
		
		
	
		echo $tabs->endPanel();		
		echo $tabs->startPanel(JText::_("EASYSDI_DEFINE_METADATA_IDENTIFICATION_TITLE"),"productrPane");
?>		
		<table border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_DEFINE_METADATA_IDENTIFICATION_SUBTITLE"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
						
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_METADATA_DESCRIPTION"); ?></td>
							<td><textarea rows="5" cols="50" name ="metadata_description" ><?php echo $geoMD->getDescription();?></textarea></td>							
							</tr>						
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_METADATA_LAST_UPDATE"); ?></td>
							<td><input size="50" type="text" id="metadata_last_update" name ="metadata_last_update" value="<?php echo $geoMD->getUpdateDate();?>"> 
							<input type="button" onClick="showCalendar('metadata_last_update','%d-%m-%Y');">							</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_METADATA_GEOGRAPHIC_TEXTUAL"); ?></td>
							<td><input size="50" type="text" name ="metadata_geograhic_textual" value="<?php echo $geoMD->getTextualExtent();?>"> </td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_METADATA_SPATIAL_COVERAGE"); ?></td>
							<td><input size="50" type="text" name ="metadata_spatial_coverage" value=""> </td>							
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_METADATA_SYNOPTIQUE"); ?></td>
							<td><input size="50" type="text" name ="metadata_synoptique" value=""> </td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_METADATA_EXTRAIT"); ?></td>
							<td><input size="50" type="text" name ="metadata_extrait" value="<?php echo $geoMD->getGraphicOverviewFileName();?>"> </td>
							</tr>							
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_METADATA_THEMA"); ?></td>
							<td><input size="50" type="text" name ="metadata_thema" value=""> </td>							
							</tr>
							</table>
					</fieldset>
					</td>
				</tr>
												
			</table>
					
	<?php 	
		echo $tabs->endPanel();

		echo $tabs->startPanel(JText::_("EASYSDI_DEFINE_METADATA_DIFFUSION_TITLE"),"productrPane");
?>		
		<table border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_DEFINE_METADATA_DIFFUSION_SUBTITLE"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_CONDITION"); ?></td>
							<td>
							<textarea rows="5" cols="50" name="metadata_legal_constraint"> <?php echo $geoMD->getLegalConstraint();?> </textarea>
							 </td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_RESTRICTION"); ?></td>
							<td><textarea rows="5" cols="50" name="metadata_use_limitation" > <?php echo $geoMD->getUseLimitation();?> </textarea></td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_TARIF"); ?></td>
							<td><input size="50" type="text" name ="metadata_tarif" value="<?php echo $geoMD->getFees();?>"> </td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_STATUT"); ?></td>
							<td><input size="50" type="text" name ="metadata_statut" value=""> </td>
							</tr>
							</table>
							</fieldset>
							</td>
							</tr>
		</table>
		<?php
		echo $tabs->endPanel();
		
		
		
		
		
				echo $tabs->startPanel(JText::_("EASYSDI_DEFINE_METADATA_STATUT_JURIDIQUE"),"productrPane");
?>		
		<table border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_DEFINE_METADATA_STATUT_JURIDIQUE"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_REFERENCE"); ?></td>
							<td><input size="50" type="text" name ="metadata_reference" value=""> </td>
							</tr>							
							</table>
							</fieldset>
							</td>
							</tr>
		</table>
		<?php
		echo $tabs->endPanel();		
		
		echo $tabs->startPanel(JText::_("EASYSDI_DEFINE_METADATA_GESTION"),"productrPane");
?>		
		<table border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_DEFINE_METADATA_GESTION_SUBTITLE"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JText::_("EASYSDI_DEFINE_ACQUISITION"); ?></td>
								<td><input size="50" type="text" name ="metadata_acquisition" value="<?php echo $geoMD->getAcquisitionMode()?>"> </td>							
							</tr>							
							<tr>
								<td><?php echo JText::_("EASYSDI_DEFINE_ACQUISITION_DESC"); ?></td>
								<td><input size="50" type="text" name ="metadata_acquisition_desc" value="<?php echo $geoMD->getAcquisitionDescription();?>"> </td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_ACQUISITION_DATA_SOURCE"); ?></td>
							<td><input size="50" type="text" name ="metadata_acquisition_data_source" value="<?php echo $geoMD->getAcquisitionDataSource(); ?>"> </td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_ACQUISITION_UPDATE_TYPE"); ?></td>
							<td><input size="50" type="text" name ="metadata_acquisition_update_type" value=""> </td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_ACQUISITION_FREQ"); ?></td>
							<td><input size="50" type="text" name ="metadata_acquisition_freq" value="<?php echo $geoMD->getUpdateFrequency();?>"> </td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_DEFINE_ACQUISITION_REM"); ?></td>
							<td><input size="50" type="text" name ="metadata_acquisition_rem" value="<?php echo $geoMD->getAcquisitionRmk();?>"> </td>
							</tr>
							</table>
							</fieldset>
							</td>
							</tr>
		</table>
		<?php
		echo $tabs->endPanel();
		
		
		
		echo $tabs->startPanel(JText::_("EASYSDI_DEFINE_METADATA_MANAGER"),"productrPane");
?>		
		<table border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_DEFINE_METADATA_MANAGER_SUBTITLE"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JText::_("EASYSDI_MANAGER_ORGANISATION_NAME"); ?></td>
								<td><input size="50" type="text" name ="metadata_manager_organisation_name" value="<?php echo $geoMD->getManagerOrganisationName()?>"> </td>							
							</tr>							
							<tr>
								<td><?php echo JText::_("EASYSDI_MANAGER_NAME"); ?></td>
								<td><input size="50" type="text" name ="metadata_manager_name" value="<?php echo $geoMD->getManagerName();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_MANAGER_ADDRESS"); ?></td>
								<td><input size="50" type="text" name ="metadata_manager_address" value="<?php echo $geoMD->getManagerAddress();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_MANAGER_CITY"); ?></td>
								<td><input size="50" type="text" name ="metadata_manager_city" value="<?php echo $geoMD->getManagerCity();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_MANAGER_POSTAL"); ?></td>
								<td><input size="50" type="text" name ="metadata_manager_postal_code" value="<?php echo $geoMD->getManagerPostalCode();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_MANAGER_COUNTRY"); ?></td>
								<td><input size="50" type="text" name ="metadata_manager_country" value="<?php echo $geoMD->getManagerCountry();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_MANAGER_VOICE"); ?></td>
								<td><input size="50" type="text" name ="metadata_manager_voice" value="<?php echo $geoMD->getManagerVoice();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_MANAGER_FAX"); ?></td>
								<td><input size="50" type="text" name ="metadata_manager_fax" value="<?php echo $geoMD->getManagerFax();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_MANAGER_MAIL"); ?></td>
								<td><input size="50" type="text" name ="metadata_manager_mail" value="<?php echo $geoMD->getManagerEmail();?>"> </td>
							</tr>							
							</table>
							</fieldset>
							</td>
							</tr>
		</table>
		<?php
		echo $tabs->endPanel();
				echo $tabs->startPanel(JText::_("EASYSDI_DEFINE_METADATA_POC"),"productrPane");
?>		
		<table border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_DEFINE_METADATA_POC_SUBTITLE"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JText::_("EASYSDI_POC_ORGANISATION_NAME"); ?></td>
								<td><input size="50" type="text" name ="metadata_poc_organisation_name" value="<?php echo $geoMD->getPocOrganisationName()?>"> </td>							
							</tr>							
							<tr>
								<td><?php echo JText::_("EASYSDI_POC_NAME"); ?></td>
								<td><input size="50" type="text" name ="metadata_poc_name" value="<?php echo $geoMD->getPocName();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_POC_ADDRESS"); ?></td>
								<td><input size="50" type="text" name ="metadata_poc_address" value="<?php echo $geoMD->getPocAddress();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_POC_CITY"); ?></td>
								<td><input size="50" type="text" name ="metadata_poc_city" value="<?php echo $geoMD->getPocCity();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_POC_POSTAL"); ?></td>
								<td><input size="50" type="text" name ="metadata_poc_postal_code" value="<?php echo $geoMD->getPocPostalCode();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_POC_COUNTRY"); ?></td>
								<td><input size="50" type="text" name ="metadata_poc_Country" value="<?php echo $geoMD->getPocCountry();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_POC_VOICE"); ?></td>
								<td><input size="50" type="text" name ="metadata_poc_voice" value="<?php echo $geoMD->getPocVoice();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_POC_FAX"); ?></td>
								<td><input size="50" type="text" name ="metadata_poc_fax" value="<?php echo $geoMD->getPocFax();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_POC_MAIL"); ?></td>
								<td><input size="50" type="text" name ="metadata_poc_email" value="<?php echo $geoMD->getPocEmail();?>"> </td>
							</tr>							
							</table>
							</fieldset>
							</td>
							</tr>
		</table>
		<?php
		echo $tabs->endPanel();
		

		
						echo $tabs->startPanel(JText::_("EASYSDI_DEFINE_METADATA_DISTRIBUTOR_POC"),"productrPane");
?>		
		<table border="0" cellpadding="0" cellspacing="0">

			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_DEFINE_METADATA_DISTRIBUTOR_SUBTITLE"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JText::_("EASYSDI_DISTRIBUTOR_ORGANISATION_NAME"); ?></td>
								<td><input size="50" type="text" name ="metadata_distribution_organisation_name" value="<?php echo $geoMD->getDistributionOrganisationName()?>"> </td>							
							</tr>							
							<tr>
								<td><?php echo JText::_("EASYSDI_DISTRIBUTOR_NAME"); ?></td>
								<td><input size="50" type="text" name ="metadata_distribution_name" value="<?php echo $geoMD->getDistributionName();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DISTRIBUTOR_ADDRESS"); ?></td>
								<td><input size="50" type="text" name ="metadata_distribution_address" value="<?php echo $geoMD->getDistributionName();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DISTRIBUTOR_CITY"); ?></td>
								<td><input size="50" type="text" name ="metadata_distribution_city" value="<?php echo $geoMD->getDistributionCity();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DISTRIBUTOR_POSTAL"); ?></td>
								<td><input size="50" type="text" name ="metadata_distribution_code" value="<?php echo $geoMD->getDistributionPostalCode();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DISTRIBUTOR_COUNTRY"); ?></td>
								<td><input size="50" type="text" name ="metadata_distribution_country" value="<?php echo $geoMD->getDistributionCountry();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DISTRIBUTOR_VOICE"); ?></td>
								<td><input size="50" type="text" name ="metadata_distribution_voice" value="<?php echo $geoMD->getDistributionVoice();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DISTRIBUTOR_FAX"); ?></td>
								<td><input size="50" type="text" name ="metadata_distribution_fax" value="<?php echo $geoMD->getDistributionFax();?>"> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_DISTRIBUTOR_MAIL"); ?></td>
								<td><input size="50" type="text" name ="metadata_distribution_mail" value="<?php echo $geoMD->getDistributionEmail();?>"> </td>
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
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	
	
	function listProduct($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_PRODUCT"));
		$partners = array();
		
		
		
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>
				<td align="right">
					<b><?php echo JText::_("FILTER");?></b>&nbsp;
					<input size="50" type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton(\'listProduct\');" />			
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
				<td><?php echo $row->metadata_id; ?></td>
				<td><?php echo $row->data_title; ?></td>
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<td><?php echo date('d.m.Y H:i:s',strtotime($row->creation_date)); ?></td>
								
				
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
		}
		
			?></tbody>
			
		<?php			
		
		if (JRequest::getVar('use_pagination',0))
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
}
?>