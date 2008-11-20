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
						<input type="text" name="<?php echo $iso_key."{lang=$rowFreetext->lang}[]"?>" value="<?php echo $geoMD->getXPathResult("//gmd:MD_Metadata/".$iso_key."/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='$rowFreetext->lang']")?>">
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
					<select name="<?php echo $iso_key."[]"?>" size="<?php if ($rowList->multiple == 1) {echo "1";}else {echo "10";} ?>" class="inputbox">
					<?php 
				$query = "SELECT * FROM #__easysdi_metadata_list_content where list_id = $rowList->list_id";
				$database->setQuery($query);
				$rowsListContent = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				
				foreach ($rowsListContent as $rowListContent){
					
					?>
						<option value="<?php echo $rowListContent->key ;?>" ><?php echo $rowListContent->value ;?></option>
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
					<tr><td><?php echo JText::_($row->description) ?> </td><td>
					<?php if( $rowFreetext->is_id == 1) { ?>							
					<input type="text" <?php if ($rowFreetext->is_constant == 1) echo "READONLY";   ?> name="<?php echo $iso_key."[]"?>" value="<?php echo $metadata_id;?>">
					<?php 
					}else{
						if( $rowFreetext->is_constant == 1) {
					?>
					<input type="text" <?php if ($rowFreetext->is_constant == 1) echo "READONLY";   ?> name="<?php echo $iso_key."[]"?>" value="<?php echo $rowFreetext->default_value;?>">
					<?php 
						}else{
					?>
					<input type="text" <?php if ($rowFreetext->is_constant == 1) echo "READONLY";   ?> name="<?php echo $iso_key."[]"?>" value="<?php echo $geoMD->getXPathResult("//gmd:MD_Metadata/".$iso_key."/gco:CharacterString")?>">
					<?php 
							
							
						}
					}
					?>
					
					</td></tr>
					<?php					
				}
				break;
			case "class":				
				$query = "select classes_to_id from jos_easysdi_metadata_classes_classes where classes_from_id = $row->class_id";
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
						}
					
					
				}
	
					
						
				/*foreach ($rowsClasses as $rowClasses){
				//				
				}*/
			break;
				
		
		default:		
	
		
		}

		
		

}



	function generateMetadata($row,$tab_id,$metadata_standard_id,$iso_key,$doc){
		global  $mainframe;
		$database =& JFactory::getDBO();
			
		switch($row->type){
				
			case "locfreetext":
			
				$query = "SELECT * FROM #__easysdi_metadata_classes_locfreetext a, #__easysdi_metadata_loc_freetext b WHERE a.classes_id = $row->class_id and a.loc_freetext_id = b.id";
				$database->setQuery($query);
				$rowsFreetext = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				$doc=$doc."<gmd:PT_FreeText><gmd:textGroup>";
				foreach($rowsFreetext as $rowFreetext){
					$value = array_pop  ( $_POST[$iso_key."{lang=$rowFreetext->lang}"] );					
					$doc=$doc."<gmd:LocalisedCharacterString locale=\"$rowFreetext->lang\">".$value."</gmd:LocalisedCharacterString>";
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
					$doc=$doc."<gco:CharacterString>$value</gco:CharacterString>";					
						
				}
				break;
			case "class":

				$query = "select classes_to_id from jos_easysdi_metadata_classes_classes where classes_from_id = $row->class_id";
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