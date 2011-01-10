<?php
defined('_JEXEC') or die('Restricted access');
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

		return ($total > 0 );
	}

	function generateMetadataHtml2($classId,$geoMD,$parentkey,$metadata_id,$root=0){
		/*
		echo "<hr>Passage dans generateMetadataHTML2. Etat:<br>";
		echo "classID: $classId<br>";
		//echo "geoMD: ".$geoMD->."<br>";
		echo "Parentkey: $parentkey<br>";
		echo "Metadata id: $metadata_id<br>";
		echo "Root: $root<hr>";
		*/		
		global  $mainframe;
		$database =& JFactory::getDBO();

		/* R�cup�rer les classes enfant de cette classe */
		$query = "SELECT a.type as type ,a.id,a.name as name,a.description as description,a.iso_key as iso_key  FROM #__easysdi_metadata_classes a,#__easysdi_metadata_classes_classes b WHERE a.id =b.classes_to_id  and $classId=b.classes_from_id";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}

		$directinroot=0;
		//Si il n'y a pas de r�sultat c'est que la sous classe n'est pas une classe mais un type
		if (count ($rows)==0 && $root == 1)
		{
			$query = "SELECT a.type as type ,a.id,a.name as name,a.description as description,a.iso_key as iso_key  FROM #__easysdi_metadata_classes a WHERE a.id = $classId";
			$database->setQuery($query);
			$rows = $database->loadObjectList();
			$directinroot=1;
		}
		//echo "Nombre de lignes: ".count($rows)."<br>";
		
		foreach($rows as $row)
		{
			//echo "$classId : $row->type<br>";
			switch($row->type)
			{
				case "class":
					if ($directinroot == 0){
						$key = $parentkey."/".$row->iso_key;
					}else{
						$key = $parentkey;
					}

					/* Trouver le nombre d'occurences de cet �l�ment dans la m�tadonn�e */
					$count  = $geoMD->isXPathResultCount("//".$key);
					
					//echo "Nombre d'occurences dans la m�tadonn�e: ".$count."<br>";
		
					/* S'il n'y en a pas c'est que l'�l�ment n'est pas au premier niveau. Reboucler pour chercher un niveau plus bas */
					if ($count == 0) // and $directinroot == 1)
					{
						//echo "Bouclage 1<br>";
						helper_easysdi::generateMetadataHtml2($row->id,$geoMD,$key."[1]",$metadata_id,0);
					}
					else
					{
						/* S'il y en a (des enfants), analyser chaque enfant. Reboucler pour chercher un niveau plus bas */
						for ($i=0 ;$i<$count;$i++)
						{
							//echo "Bouclage 2<br>";
							//echo "<table><tr><td><fieldset><legend>".JText::_($row->description)."looking for //$key  ==> ".($i+1)." of  $count<br>"."</legend>";
							helper_easysdi::generateMetadataHtml2($row->id,$geoMD,$key."[".($i+1)."]",$metadata_id,0);
								
							//echo "</fieldset></td></tr></table>";
						}
					}
					break;
				case  "freetext" :
					if ($directinroot == 0){
						$key = $parentkey."/".$row->iso_key;
					}else{
						$key = $parentkey;
					}

					$query = "SELECT *, b.translation as trans FROM #__easysdi_metadata_classes_freetext a, #__easysdi_metadata_freetext b WHERE a.classes_id = $row->id and a.freetext_id = b.id";

					$database->setQuery($query);
					$rowsFreetext = $database->loadObjectList();
					if ($database->getErrorNum()) {
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					}

					foreach($rowsFreetext as $rowFreetext)
					{
						if ($rowFreetext->is_constant != 1) 
						{ ?>
							<tr>
								<td><?php echo JText::_($row->description) ?></td>
								<td><?php 
						} ?> <?php 
						if( $rowFreetext->is_id == 1) 
						{ ?> <input size="20" type="text"
									<?php if ($rowFreetext->is_constant == 1) echo "READONLY='TRUE'"; ?>
									name="<?php echo "PARAM$row->id[]"?>" value="<?php echo $metadata_id;?>" /> <?php 
						}
						else
						{
							if( $rowFreetext->is_constant == 1) 
							{
								?> <input size="50" type="text"
								<?php if ($rowFreetext->is_constant == 1) echo "READONLY='TRUE'";   ?>
									name="<?php echo "PARAM$row->id[]"?>"
									value="<?php echo htmlentities($rowFreetext->trans);?>" /> <?php 
							}
							else
							{
								?> <input size="50" type="text"
								<?php if ($rowFreetext->is_constant == 1) echo "READONLY='TRUE'";   ?>
									name="<?php echo "PARAM$row->id[]"?>"
									value="<?php echo htmlentities($geoMD->getXPathResult("//".$key."/gco:CharacterString"))?>" />
									<?php
							}
						}
						?> <?php 
						if ($rowFreetext->is_constant != 1) 
						{
							?></td>
							</tr>
							<?php
						}
					}
					break;
				case "locfreetext":
					if ($directinroot == 0){
						$key = $parentkey."/".$row->iso_key;
					}else{
						$key = $parentkey;
					}
				
					/* R�cup�rer toutes les langues pour les locfreetext */
					$query = "SELECT * FROM #__easysdi_metadata_loc_freetext";
					$database->setQuery($query);
					$LangLocfreetext = $database->loadObjectList();
					
					/* R�cup�rer toutes les entr�es de langues de ce champ */
					/*
					$query = "SELECT *, b.translation as trans FROM #__easysdi_metadata_classes_locfreetext a, #__easysdi_metadata_loc_freetext b WHERE a.classes_id = $row->id and a.loc_freetext_id = b.id";
					$database->setQuery($query);
					$rowsFreetext = $database->loadObjectList();
					*/
					if ($database->getErrorNum()) {
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					}
					foreach($LangLocfreetext as $langLoc)
					{
						?>
						<tr>
							<td><?php echo JText::_($row->description)."[".JText::_($langLoc->translation)."]"?></td>
							<!-- <td><?php //echo "//$key/gmd:LocalisedCharacterString[@locale='$langLoc->lang']" ?></td> -->
							<td><textarea name="<?php echo "PARAM$row->id[]"?>"
								rows="5" cols="40"><?php echo  ($geoMD->getXPathResult("//$key/gmd:LocalisedCharacterString[@locale='$langLoc->lang']"))?></textarea>
							</td>
						</tr>
						<?php
					}
					break;
				
				case  "list" :
				
					$key = $parentkey;
				
				
					$query = "SELECT *, b.translation as trans FROM #__easysdi_metadata_classes_list a, #__easysdi_metadata_list b WHERE a.classes_id = $row->id and a.list_id = b.id";
					$database->setQuery($query);
					$rowsList = $database->loadObjectList();
					if ($database->getErrorNum()) {
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					}
					foreach($rowsList as $rowList){
						?>
					<tr>
						<td><?php echo JText::_($rowList->trans) ?></td>
						<td><select name="<?php echo "PARAM$row->id[]"?>"
						<?php if ($rowList->multiple == 1) {echo "multiple";} ?>
							size="<?php if ($rowList->multiple == 1) {echo "5";}else {echo "1";} ?>"
							class="inputbox">
							<?php
							$query = "SELECT * FROM #__easysdi_metadata_list_content where list_id = $rowList->list_id";
							$database->setQuery($query);
							$rowsListContent = $database->loadObjectList();
							if ($database->getErrorNum()) {
								$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
							}
					
							foreach ($rowsListContent as $rowListContent)
							{
								?>
								<option value="<?php echo $rowListContent->key ;?>"
								<?php if ($geoMD->isXPathResultCount("//".$key."[MD_TopicCategoryCode='$rowListContent->key']")>0){echo "selected";}?>><?php echo  JText::_($rowListContent->translation) ;?></option>
								<!-- <?php //echo "//".$key."[MD_TopicCategoryCode='$rowListContent->key']". "===>".$geoMD->getXPathResult("//".$key."/MD_TopicCategoryCode") ; ?></option>-->
								<?php
							} ?>
						</select></td>
					</tr>
				
							<?php
					}
					break;
				}
			}
	}

	function generateMetadataHtml($row,$tab_id,$metadata_standard_id,$iso_key,$geoMD,$metadata_id){
		global $mainframe;

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
<tr>
	<td><?php echo JText::_($row->description);?></td>
	<td><input type="text" name="<?php echo "PARAM$row->class_id[]"?>"
		value="<?php echo $geoMD->getExtValue($rowExt->name)?>"></td>
</tr>
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
<tr>
	<td><?php echo JText::_($row->description)."[$rowFreetext->lang]"?></td>
	<td><textarea
		name="<?php echo "PARAM$row->class_id[]"?>" rows="5"
		cols="40"><?php echo  ($geoMD->getXPathResult("//gmd:MD_Metadata/".$iso_key."/gmd:LocalisedCharacterString[@locale='$rowFreetext->lang']"))?></textarea>
	</td>
</tr>
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
<tr>
	<td><?php echo JText::_($row->description) ?></td>
	<td><select name="<?php echo "PARAM$row->class_id[]"?>"
	<?php if ($rowList->multiple == 1) {echo "multiple";} ?>
		size="<?php if ($rowList->multiple == 1) {echo "5";}else {echo "1";} ?>"
		class="inputbox">
		<?php
		$query = "SELECT * FROM #__easysdi_metadata_list_content where list_id = $rowList->list_id";
		$database->setQuery($query);
		$rowsListContent = $database->loadObjectList();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}

		foreach ($rowsListContent as $rowListContent){
			?>
		<option value="<?php echo $rowListContent->key ;?>"
		<?php if ($geoMD->isXPathResultCount("//gmd:MD_Metadata/".$iso_key."[gco:CharacterString='$rowListContent->key']")>0){echo "selected=selected";}?>><?php echo $rowListContent->translation ;?></option>
		<?php } ?>
	</select></td>
</tr>
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
<tr>
	<td><?php echo JText::_($row->description) ?></td>
	<td><?php } ?> <?php if( $rowFreetext->is_id == 1) { ?> <input
		type="<?php if ($rowFreetext->is_constant == 1) echo "hidden"; else echo "text";?>"
		name="<?php echo "PARAM$row->class_id[]"?>" value="<?php echo $metadata_id;?>">
		<?php
	}else{
		if( $rowFreetext->is_constant == 1) {
			?> <input
		type="<?php if ($rowFreetext->is_constant == 1) echo "hidden"; else echo "text";   ?>"
		name="<?php echo "PARAM$row->class_id[]"?>"
		value="<?php echo $rowFreetext->default_value;?>"> <?php 
		}else{
			?> <input
		type="<?php if ($rowFreetext->is_constant == 1) echo "hidden"; else echo "text";   ?>"
		name="<?php echo "PARAM$row->class_id[]"?>"
		value="<?php echo $geoMD->getXPathResult("//gmd:MD_Metadata/".$iso_key."/gco:CharacterString")?>">
		<?php

		}
	}
	?> <?php if ($rowFreetext->is_constant != 1) { ?></td>
</tr>
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

	
	function generateMetadata2($classId,$geoMD,$parentkey,$metadata_id,$root=0,&$doc){


		global  $mainframe;
		$database =& JFactory::getDBO();

		$query = "SELECT a.type as type ,a.id,a.name as name,a.description as description,a.iso_key as iso_key  FROM #__easysdi_metadata_classes a,#__easysdi_metadata_classes_classes b WHERE a.id =b.classes_to_id  and $classId=b.classes_from_id";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}

		$directinroot=0;
		//Si il n'y a pas de résultat c'est que la sous classe n'est pas une classe mais un type
		if (count ($rows)==0 && $root == 1){

			$query = "SELECT a.type as type ,a.id,a.name as name,a.description as description,a.iso_key as iso_key  FROM #__easysdi_metadata_classes a WHERE a.id = $classId";
			$database->setQuery($query);
			$rows = $database->loadObjectList();
			$directinroot=1;
		}
		foreach($rows as $row){
			switch($row->type){
				case "class":
					if ($directinroot == 0){
						$key = $parentkey."/".$row->iso_key;
					}else{
						$key = $parentkey;
					}

					$count  = $geoMD->isXPathResultCount("//".$key);

					for ($i=0 ;$i<$count;$i++){

						helper_easysdi::generateMetadata2($row->id,$geoMD,$key."[".($i+1)."]",$metadata_id,0,$doc);	
					}
					break;
				case  "freetext" :
					if ($directinroot == 0){
						$key = $parentkey."/".$row->iso_key;
					}else{
						$key = $parentkey;
					}

					$query = "SELECT * FROM #__easysdi_metadata_classes_freetext a, #__easysdi_metadata_freetext b WHERE a.classes_id = $row->id and a.freetext_id = b.id";

					$database->setQuery($query);
					$rowsFreetext = $database->loadObjectList();
					if ($database->getErrorNum()) {
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					}

					foreach($rowsFreetext as $rowFreetext){
							
						$value = array_pop  ( $_POST[$key] );
						if ($rowFreetext->is_number == 1){
							$doc=$doc."<gco:Decimal>$value</gco:Decimal>";
						}else
						if ($rowFreetext->is_date == 1){
							$doc=$doc."<gco:Date>$value</gco:Date>";
						}else	$doc=$doc."<gco:CharacterString>".htmlspecialchars    (stripslashes($value) )."</gco:CharacterString>";

					}
					break;
				case "locfreetext":
					if ($directinroot == 0){
						$key = $parentkey."/".$row->iso_key;
					}else{
						$key = $parentkey;
					}

					$query = "SELECT * FROM #__easysdi_metadata_classes_locfreetext a, #__easysdi_metadata_loc_freetext b WHERE a.classes_id = $row->id and a.loc_freetext_id = b.id";
					$database->setQuery($query);
					$rowsFreetext = $database->loadObjectList();
					if ($database->getErrorNum()) {
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					}
					foreach($rowsFreetext as $rowFreetext){
														
						$value = array_pop  ( $_POST[key."{lang=$rowFreetext->lang}"] );
						$doc=$doc."<gmd:LocalisedCharacterString locale=\"$rowFreetext->lang\">".htmlspecialchars    (stripslashes($value) )."</gmd:LocalisedCharacterString>";

					}
					break;			
		}
		}
	}

	
	function generateMetadata($row,$tab_id,$metadata_standard_id,$iso_key,&$doc,$n){
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
					/* Lister les contenus de chaque liste */
					$query = "SELECT * FROM #__easysdi_metadata_list_content where list_id = $rowList->list_id";
					$database->setQuery($query);
					$rowsListContent = $database->loadObjectList();
					if ($database->getErrorNum()) {
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					}

					foreach ($rowsListContent as $rowListContent){
						// $value = array_pop  ( $_POST["PARAM$row->class_id"] );
						/* R�cup�rer les valeurs entr�es par l'utilisateur */
						$array  = JRequest::getVar("PARAM$row->id");
						$value = $array [$n];
						print_r ($array);
						echo "<br><b>Value ".$n."</b>: ".$value."<br>";
						/* Cr�er chaque �l�ment de la valeur */
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
					//$value = array_pop  ( $_POST["PARAM$row->class_id"] );
					$array  = JRequest::getVar("PARAM$row->id");
					$value = $array [$n];
					print_r ($array);
					echo "<br><b>Value ".$n."</b>: ".$value."<br>";
					
					$doc=$doc."<gmd:MD_MetadataExtensionInformation><gmd:extendedElementInformation><gmd:MD_ExtendedElementInformation><gmd:name><gco:CharacterString>".htmlspecialchars    (stripslashes($rowExt->name) )."</gco:CharacterString></gmd:name><gmd:domainValue><gco:CharacterString>$value</gco:CharacterString></gmd:domainValue></gmd:MD_ExtendedElementInformation></gmd:extendedElementInformation></gmd:MD_MetadataExtensionInformation>";
				}
				break;
			
			case "locfreetext":
				/* R�cup�rer toutes les langues pour les locfreetext */
					$query = "SELECT * FROM #__easysdi_metadata_loc_freetext";
					$database->setQuery($query);
					$LangLocfreetext = $database->loadObjectList();
					
				/* R�cup�rer les textes localis�s associ�s � cette m�tadonn�e */
				$query = "SELECT * FROM #__easysdi_metadata_classes_locfreetext a, #__easysdi_metadata_loc_freetext b WHERE a.classes_id = $row->class_id and a.loc_freetext_id = b.id";
				$database->setQuery($query);
				$rowsFreetext = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				//$doc=$doc."<gmd:PT_FreeText><gmd:textGroup>";
				foreach($LangLocfreetext as $LangLoc){
					//$mainframe->enqueueMessage($_POST[$iso_key."{lang=$rowFreetext->lang}"],"ERROR");
					
					//$value = array_pop  ( $_POST["PARAM$row->class_id"] );
					$array  = JRequest::getVar("PARAM$row->id");
					$value = $array [$n];
					print_r ($array);
					echo "<br><b>Value ".$n."</b>: ".$value."<br>";
					
					$doc=$doc."<gmd:LocalisedCharacterString locale=\"$LangLoc->lang\">".htmlspecialchars    (stripslashes($value) )."</gmd:LocalisedCharacterString>";
				}
				//$doc=$doc."</gmd:textGroup></gmd:PT_FreeText>";
				break;
				
			case  "freetext" :
				$query = "SELECT * FROM #__easysdi_metadata_classes_freetext a, #__easysdi_metadata_freetext b WHERE a.classes_id = $row->class_id and a.freetext_id = b.id";
				$database->setQuery($query);
				$rowsFreetext = $database->loadObjectList();
				if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				foreach($rowsFreetext as $rowFreetext){
					//$value = array_pop  ( $_POST["PARAM$row->class_id"] );
					$array  = JRequest::getVar("PARAM$row->id");
					$value = $array [$n];
					print_r ($array);
					echo "<br><b>Value ".$n."</b>: ".$value."<br>";
							
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
						helper_easysdi::generateMetadata($rowClassesClasses,$tab_id,$metadata_standard_id,$iso_key."/".$rowClassesClasses->iso_key,$doc,$n);
						$doc=$doc."</$rowClassesClasses->iso_key>";
					}
				}
				
				break;
			
			default:
				$mainframe->enqueueMessage($row->type,"INFO");
		}
	}

	function searchForLastEntry($row,$metadata_standard_id){
		global  $mainframe;
		$database =& JFactory::getDBO();

		switch($row->type){
		
			case "class":
				/* R�cup�rer les classes enfants */
				$query = "select classes_to_id from #__easysdi_metadata_classes_classes where classes_from_id = $row->class_id";
				$database->setQuery($query);
				$rowsClasses = $database->loadObjectList();
				if ($database->getErrorNum()) {
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				}
				foreach ($rowsClasses as $rowClasses){
						/* R�cup�rer les donn�es de chaque classe enfant */
						$query = "SELECT  *,id as class_id from  #__easysdi_metadata_classes  where id = $rowClasses->classes_to_id" ;
						$database->setQuery($query);
						$rowsClassesClasses = $database->loadObjectList();
						if ($database->getErrorNum()) {
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
							}
						
						/* Parcourir chaque enfant */
						foreach ($rowsClassesClasses  as $rowClassesClasses ){
							$a = helper_easysdi::searchForLastEntry($rowClassesClasses,$metadata_standard_id);																				
							return $a;																																		
						}
				}
			break;

			default:										
					return count(JRequest::getVar("PARAM$row->id"));
					//$mainframe->enqueueMessage($row->type,"ERROR");
		}
		
	}

	function getUniqueId(){
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
						mt_rand( 0, 0xffff ), 
						mt_rand( 0, 0xffff ), 
						mt_rand( 0, 0xffff ), 
						mt_rand( 0, 0x0fff ) | 0x4000, 
						mt_rand( 0, 0x3fff ) | 0x8000, 
						mt_rand( 0, 0xffff ), 
						mt_rand( 0, 0xffff ), 
						mt_rand( 0, 0xffff ) 
					   );
	}

	function exportPDF( $myHtml) {
		/*	global  $mainframe;

			$database =& JFactory::getDBO();


			$document  = new DomDocument();

			$document ->load(JPATH_COMPONENT_SITE.'/xsl/xhtml-to-xslfo.xsl');
			$processor = new xsltProcessor();
			$processor->importStylesheet($document);


			//Problem with loadHTML() and encoding : work around method
			$pageDom = new DomDocument();
			$searchPage = mb_convert_encoding($myHtml, 'HTML-ENTITIES', "UTF-8");
			$pageDom->loadHTML($searchPage);
			$result = $processor->transformToXml($pageDom);
			//$result = $processor->transformToXml(DOMDocument::loadHTML($myHtml));


			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');

			$bridge_url = config_easysdi::getValue("JAVA_BRIDGE_URL");

			if ($bridge_url ){
					
				require_once($bridge_url);
					
				$java_library_path = 'file:'.JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'java'.DS.'fop'.DS.'fop.jar;';
				$java_library_path .= 'file:'.JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'java'.DS.'fop'.DS.'FOPWrapper.jar';
					
				$fopcfg = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'config'.DS.'fop.xml';
				$foptmp = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'tmp'.DS.uniqid().'pdf';



					
				try {
					@java_reset();
					java_require($java_library_path);

					$j_fw = new Java("FOPWrapper");

					$version = $j_fw->FOPVersion();
					//G�n�ration du document PDF sous forme de fichier
					$j_fw->convert($fopcfg,$result,$foptmp);

					@java_reset();

					$fp = fopen ($foptmp, 'r');
					$result = fread($fp, filesize($foptmp));
					fclose ($fp);



					error_reporting(0);
					ini_set('zlib.output_compression', 0);
					header('Pragma: public');
					header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
					header('Content-Tran§sfer-Encoding: none');
					header('Content-Type: application/octetstream; name="metadata.pdf"');
					header('Content-Disposition: attachement; filename="metadata.pdf"');

					echo $result;


				} catch (JavaException $ex) {
					$trace = new Java("java.io.ByteArrayOutputStream");
					$ex->printStackTrace(new Java("java.io.PrintStream", $trace));
					print "java stack trace: $trace\n";
				}
			}else {
				$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE CONFIGURATION KEY FOR FOP JAVA BRIDGE'  ),'error');
	}*/
	}

	function array_obj_diff ($array1, $array2) {
    
    foreach ($array1 as $key => $value) {
        $array1[$key] = serialize ($value);
    }

    foreach ($array2 as $key => $value) {
        $array2[$key] = serialize ($value);
    }
    
    $array_diff = array_diff ($array1, $array2);
    
    foreach ($array_diff as $key => $value) {
        $array_diff[$key] = unserialize ($value);
    }
    
    return $array_diff;
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
	
	function arrayTranslate(&$rows)
	{		
		if (count($rows)>0)
		{
			foreach($rows as $key => &$row)
			{		  	
				$row = JText::_($row);
  			}			    
		}
	}
	

	
	function escapeString($text)
	{
		/*$text = str_replace(chr(13),"\\r",$text);
		$text = str_replace(chr(10),"\\n",$text);
		$text = str_replace(chr(34),"&quot;",$text);
		$text = str_replace(chr(39),"&#39;",$text);
		$text = str_replace(chr(92),"&#92;",$text);
		$text = str_replace(chr(47),"&#47;",$text);
		$text = str_replace(chr(60),"&#60;",$text);
		$text = str_replace(chr(62),"&#62;",$text);*/
		$text = str_replace(chr(13),"\\r",$text);
		$text = str_replace(chr(10),"\\n",$text);
		$text = htmlentities($text);
		//$text = addslashes($text);
		//echo $text."<br>";
		return $text;
	}
	
	/**
	 * Generates an HTML checkbox list
	 * 
	 * Joomla! radiolist code, override for EasySDI
	 *
	 * @param array An array of objects
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @param string The name of the object variable for the option value
	 * @param string The name of the object variable for the option text
	 * @returns string HTML for the select list
	 */
	function checkboxlist( $arr, $name, $attribs = null, $labelattribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false )
	{
		reset( $arr );
		$html = '';

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		$id_text = $name;
		if ( $idtag ) {
			$id_text = $idtag;
		}

		for ($i=0, $n=count( $arr ); $i < $n; $i++ )
		{
			$k    = $arr[$i]->$key;
			$t    = $translate ? JText::_( $arr[$i]->$text ) : $arr[$i]->$text;
			$id    = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra    = '';
			$extra    .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected ))
			{
				foreach ($selected as $val)
				{
					$k2 = is_object( $val ) ? $val->$key : $val;
						
					if ($k == $k2)
					{
						$extra .= " checked=\"checked\"";
						break;
					}
				}
			} else {
				$extra .= ((string)$k == (string)$selected ? " checked=\"checked\"" : '');
			}
			
			$id_text		= str_replace('[','',$id_text);
			$id_text		= str_replace(']','',$id_text);
		
			$html .= "<div>\n";
			$html .= "\n\t<input type=\"checkbox\" name=\"$name\" id=\"$id_text$k\" value=\"".$k."\"$extra $attribs />";
			$html .= "\n\t<label for=\"$id_text$k\" $labelattribs>$t</label>";
			$html .= "</div>\n";
		}
		$html .= "\n";
		return $html;
	}
	
	/**
	 * Generates an HTML radio list
	 * 
	 * Joomla! Code, override for EasySDI
	 *
	 * @param array An array of objects
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @param string The name of the object variable for the option value
	 * @param string The name of the object variable for the option text
	 * @returns string HTML for the select list
	 */
	function radiolist( $arr, $name, $attribs = null, $labelattribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false )
	{
		reset( $arr );
		$html = '';

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		 }

		$id_text = $name;
		if ( $idtag ) {
			$id_text = $idtag;
		}

		for ($i=0, $n=count( $arr ); $i < $n; $i++ )
		{
			$k	= $arr[$i]->$key;
			$t	= $translate ? JText::_( $arr[$i]->$text ) : $arr[$i]->$text;
			$id	= ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra	= '';
			$extra	.= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected ))
			{
				foreach ($selected as $val)
				{
					$k2 = is_object( $val ) ? $val->$key : $val;
					if ($k == $k2)
					{
						$extra .= " selected=\"selected\"";
						break;
					}
				}
			} else {
				$extra .= ((string)$k == (string)$selected ? " checked=\"checked\"" : '');
			}
			
			$id_text		= str_replace('[','',$id_text);
			$id_text		= str_replace(']','',$id_text);
		
			$html .= "<div>\n";
			$html .= "\n\t<input type=\"radio\" name=\"$name\" id=\"$id_text$k\" value=\"".$k."\"$extra $attribs />";
			$html .= "\n\t<label for=\"$id_text$k\" $labelattribs>$t</label>";
			$html .= "</div>\n";
		}
		$html .= "\n";
		return $html;
	}
	
	/**
	* Generates a yes/no radio list
	*
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	function booleanlist( $name, $attribs = null, $labelattribs = null, $selected = null, $yes='yes', $no='no', $id=false )
	{
		$arr = array(
			JHTML::_('select.option',  '0', JText::_( $no ) ),
			JHTML::_('select.option',  '1', JText::_( $yes ) )
		);
		return helper_easysdi::radiolist($arr, $name, $attribs, $labelattribs, 'value', 'text', (int) $selected, $id);
	}
	
	/**
	 * Displays a calendar control field
	 * 
	 * Joomla! Code, override for EasySDI
	 *
	 * @param	string	The date value
	 * @param	string	The name of the text field
	 * @param	string	The id of the text field
	 * @param	string	The date format
	 * @param	array	Additional html attributes
	 */
	function calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null, $imgattribs = null, $src, $alt)
	{
		JHTML::_('behavior.calendar'); //load the calendar behavior

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString( $attribs );
		}
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration('window.addEvent(\'domready\', function() {Calendar.setup({
        inputField     :    "'.$id.'",     // id of the input field
        ifFormat       :    "'.$format.'",      // format of the input field
        button         :    "'.$id.'_img",  // trigger for the calendar (button ID)
        align          :    "Tl",           // alignment (defaults to "Bl")
        singleClick    :    true
    });});');

		return '<input type="text" name="'.$name.'" id="'.$id.'" value="'.htmlspecialchars($value, ENT_COMPAT, 'UTF-8').'" '.$attribs.' />'.
				 '<img src="'.$src.'" alt="'.$alt.'" id="'.$id.'_img" '.$imgattribs.'/>';
	}
}

?>


