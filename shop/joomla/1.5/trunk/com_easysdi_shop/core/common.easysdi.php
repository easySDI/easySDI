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

class helper_easysdi{
	
	
	function hasRight($partner_id,$right){
				
		$database =& JFactory::getDBO();		
		
		$query = "SELECT count(*) FROM #__easysdi_community_actor a , #__easysdi_community_role b  WHERE a.role_id = b.role_id and partner_id = $partner_id and role_code = '$right'";
		
				
		$database->setQuery($query );
		$total = $database->loadResult();
		//echo "renaud : ".$query;
		return ($total > 0 );
	}	

	
	function generateMetadataHtml($row,$tab_id,$metadata_standard_id,$iso_key,$geoMD,$metadata_id){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
	
		
		switch($row->type){
			
			case "ext":
				$query = "SELECT * FROM #__easysdi_metadata_classes_ext a, #__easysdi_metadata_ext b WHERE a.classes_id = $row->class_id and a.ext_id = b.id";
				$database->setQuery($query);
				$rowsExt = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				foreach($rowsExt as $rowExt){
					?>
					<tr><td><?php echo JText::_($row->description);?></td><td>					

												
					<input type="text" name="<?php echo $iso_key."[]"?>" value="<?php echo $geoMD->getExtValue($rowExt->name)?>"></td></tr>
					<?php					
				}
				break;
			case "locfreetext":
				$query = "SELECT * FROM #__easysdi_metadata_classes_locfreetext a, #__easysdi_metadata_loc_freetext b WHERE a.classes_id = $row->class_id and a.loc_freetext_id = b.id";
				$database->setQuery($query);
				$rowsFreetext = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				foreach($rowsFreetext as $rowFreetext){
					?>
					<tr><td><?php echo JText::_($row->description)."[$rowFreetext->lang]"?></td><td>												
						<textarea name="<?php echo $iso_key."{lang=$rowFreetext->lang}[]"?>" rows="5" cols="40"><?php echo  ($geoMD->getXPathResult("//gmd:MD_Metadata/".$iso_key."/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='$rowFreetext->lang']"))?></textarea>
					</td></tr>
					<?php					
				}
				break;
				
			case  "list" :
 	
				$query = "SELECT * FROM #__easysdi_metadata_classes_list a, #__easysdi_metadata_list b WHERE a.classes_id = $row->class_id and a.list_id = b.id";
				$database->setQuery($query);
				$rowsList = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				foreach($rowsList as $rowList){
					?>
					<tr><td><?php echo JText::_($row->description) ?> </td><td>
					<select name="<?php echo $iso_key."[]"?>" <?php if ($rowList->multiple == 1) {echo "multiple";} ?>  size="<?php if ($rowList->multiple == 1) {echo "5";}else {echo "1";} ?>" class="inputbox">
					<?php 
				$query = "SELECT * FROM #__easysdi_metadata_list_content where list_id = $rowList->list_id";
				$database->setQuery($query);
				$rowsListContent = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				
				foreach ($rowsListContent as $rowListContent){
					
					?>
					
					 
						<option value="<?php echo $rowListContent->key ;?>"   <?php if ($geoMD->isXPathResultCount("//gmd:MD_Metadata/".$iso_key."[gco:CharacterString='$rowListContent->key']")>0){echo "selected";}?>  ><?php echo $rowListContent->value ;?></option>
						<?php } ?>
					</select>							
					
					</td></tr>
					<?php					
				}
				break;
				
			case  "freetext" :
 
				$query = "SELECT * FROM #__easysdi_metadata_classes_freetext a, #__easysdi_metadata_freetext b WHERE a.classes_id = $row->class_id and a.freetext_id = b.id";
				
				$database->setQuery($query);
				$rowsFreetext = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				foreach($rowsFreetext as $rowFreetext){
					?>
					<?php if ($rowFreetext->is_constant != 1) { ?>
					<tr><td><?php echo JText::_($row->description) ?> </td><td>
					<?php } ?>
					<?php if( $rowFreetext->is_id == 1) { ?>							
					<input type="<?php if ($rowFreetext->is_constant == 1) echo "hidden"; else echo "text";?>"  name="<?php echo $iso_key."[]"?>" value="<?php echo $metadata_id;?>">
					<?php 
					}else{
						if( $rowFreetext->is_constant == 1) {
					?>
					<input type="<?php if ($rowFreetext->is_constant == 1) echo "hidden"; else echo "text";   ?>"  name="<?php echo $iso_key."[]"?>" value="<?php echo $rowFreetext->default_value;?>">
					<?php 
						}else{
					?>
					<input type="<?php if ($rowFreetext->is_constant == 1) echo "hidden"; else echo "text";   ?>"  name="<?php echo $iso_key."[]"?>" value="<?php echo $geoMD->getXPathResult("//gmd:MD_Metadata/".$iso_key."/gco:CharacterString")?>">
					<?php 
							
							
						}
					}
					?>
					<?php if ($rowFreetext->is_constant != 1) { ?>
					</td></tr>
					<?php					
					}
				}
				break;
			case "class":				
				$query = "select classes_to_id from #__easysdi_metadata_classes_classes where classes_from_id = $row->class_id";
				$database->setQuery($query);				 
				$rowsClasses = $database->loadObjectList();		
				if ($database->getErrorNum()) {
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
					}
				foreach ($rowsClasses as $rowClasses){
					
						$query = "SELECT  *,id as class_id from  #__easysdi_metadata_classes  where id = $rowClasses->classes_to_id" ;
						$database->setQuery($query);				 
						$rowsClassesClasses = $database->loadObjectList();		
						if ($database->getErrorNum()) {						
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
						}
						foreach ($rowsClassesClasses  as $rowClassesClasses ){
							
								helper_easysdi::generateMetadataHtml($rowClassesClasses,$tab_id,$metadata_standard_id,$iso_key."/".$rowClassesClasses->iso_key,$geoMD,$metadata_id);
					?>
					<?php
								
						}
					
				}
																
			break;
				
		
		default:		
			
		
		}

		
		

}



	function generateMetadata($row,$tab_id,$metadata_standard_id,$iso_key,$doc){
		global  $mainframe;
		$database =& JFactory::getDBO();
			
		switch($row->type){
			case  "list" :
 	
				$query = "SELECT * FROM #__easysdi_metadata_classes_list a, #__easysdi_metadata_list b WHERE a.classes_id = $row->class_id and a.list_id = b.id";
				$database->setQuery($query);
				$rowsList = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				foreach($rowsList as $rowList){
				$query = "SELECT * FROM #__easysdi_metadata_list_content where list_id = $rowList->list_id";
				$database->setQuery($query);
				$rowsListContent = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				
				foreach ($rowsListContent as $rowListContent){
										
					$value = array_pop  ( $_POST[$iso_key] );
					
					
					if (strlen  ($value)>0){
					$doc=$doc."<gco:CharacterString>".htmlspecialchars    (stripslashes($value ))."</gco:CharacterString>";
					}																
					?>
						<?php } ?>
					<?php					
				}
				break;
			
			case  "ext" :					
				
				$query = "SELECT * FROM #__easysdi_metadata_classes_ext a, #__easysdi_metadata_ext b WHERE a.classes_id = $row->class_id and a.ext_id = b.id";
				$database->setQuery($query);
				$rowsExt = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				foreach($rowsExt as $rowExt){
					$value = array_pop  ( $_POST[$iso_key] );
					$doc=$doc."<gmd:MD_MetadataExtensionInformation><gmd:extendedElementInformation><gmd:MD_ExtendedElementInformation><gmd:name><gco:CharacterString>".htmlspecialchars    (stripslashes($rowExt->name) )."</gco:CharacterString></gmd:name><gmd:domainValue><gco:CharacterString>$value</gco:CharacterString></gmd:domainValue></gmd:MD_ExtendedElementInformation></gmd:extendedElementInformation></gmd:MD_MetadataExtensionInformation>";																									
				}
				break;
			
			case "locfreetext":
			
				$query = "SELECT * FROM #__easysdi_metadata_classes_locfreetext a, #__easysdi_metadata_loc_freetext b WHERE a.classes_id = $row->class_id and a.loc_freetext_id = b.id";
				$database->setQuery($query);
				$rowsFreetext = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				$doc=$doc."<gmd:PT_FreeText><gmd:textGroup>";
				foreach($rowsFreetext as $rowFreetext){
					//$mainframe->enqueueMessage($_POST[$iso_key."{lang=$rowFreetext->lang}"],"ERROR");
					$value = array_pop  ( $_POST[$iso_key."{lang=$rowFreetext->lang}"] );					
					$doc=$doc."<gmd:LocalisedCharacterString locale=\"$rowFreetext->lang\">".htmlspecialchars    (stripslashes($value) )."</gmd:LocalisedCharacterString>";
				}
				$doc=$doc."</gmd:textGroup></gmd:PT_FreeText>";
				break;
			case  "freetext" :					
				
				$query = "SELECT * FROM #__easysdi_metadata_classes_freetext a, #__easysdi_metadata_freetext b WHERE a.classes_id = $row->class_id and a.freetext_id = b.id";
				$database->setQuery($query);
				$rowsFreetext = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				foreach($rowsFreetext as $rowFreetext){					
					$value = array_pop  ( $_POST[$iso_key] );
					if ($rowFreetext->is_number == 1){
						$doc=$doc."<gco:Decimal>$value</gco:Decimal>";
					}else									
					if ($rowFreetext->is_date == 1){
						$doc=$doc."<gco:Date>$value</gco:Date>";
					}else	$doc=$doc."<gco:CharacterString>".htmlspecialchars    (stripslashes($value) )."</gco:CharacterString>";																
				}
				break;
			case "class":

				$query = "select classes_to_id from #__easysdi_metadata_classes_classes where classes_from_id = $row->class_id";
				$database->setQuery($query);
				$rowsClasses = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				foreach ($rowsClasses as $rowClasses){
						
					$query = "SELECT  *,id as class_id from  #__easysdi_metadata_classes  where id = $rowClasses->classes_to_id" ;
					$database->setQuery($query);
					$rowsClassesClasses = $database->loadObjectList();
					if ($database->getErrorNum()) {
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					}
					foreach ($rowsClassesClasses  as $rowClassesClasses ){							
						$doc=$doc."<$rowClassesClasses->iso_key>";
						helper_easysdi::generateMetadata($rowClassesClasses,$tab_id,$metadata_standard_id,$iso_key."/".$rowClassesClasses->iso_key,&$doc);
						$doc=$doc."</$rowClassesClasses->iso_key>";
					}
				}
			
				break;


			default:
				$mainframe->enqueueMessage($row->type,"INFO");

		}
	}


function getUniqueId(){
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) ); 
}
	
}
?>