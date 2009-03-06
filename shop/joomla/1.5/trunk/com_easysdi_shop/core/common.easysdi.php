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

	function generateHtmlLocationSelect($row,$parent){
		$db =& JFactory::getDBO();

		if ($row->id_location_filter > 0 ){
			$query = "SELECT * FROM #__easysdi_location_definition where id = $row->id_location_filter";
			$db->setQuery( $query );
			$rows2 = $db->loadObject();
			helper_easysdi::generateHtmlLocationSelect($rows2,$row->id);

		}
		if ($parent == 0){
			echo "<tr>";
			echo "<td><select id=\"locationsListLocation$row->id\"	onChange=\"recenterOnLocationLocation('locationsListLocation$row->id')\"><option > </option></select></td>";

			echo "</tr>";
		}else{
			echo "<tr>";
			echo "<td><select id=\"locationsListLocation$row->id\"	onChange=\"fillParent ('filter$row->id','locationsListLocation$row->id','locationsListLocation$parent') \"><option > </option></select></td>";
			echo "</tr>";
			if ($row->searchbox == 1) {
				echo "<tr >";
				echo "<td><input size=5 length=5 type=\"text\" id =\"filter$row->id\" value=\"\" >"	;
				echo "<input onClick=\"fillParent ('filter$row->id','locationsListLocation$row->id','locationsListLocation$parent') \" type=\"button\" value=\"".JText::_("EASYSDI_SEARCH")."\" ></td>"	;
				echo "</tr>";
			}
		}

	}


	function generateHtmlPerimeterSelect($row,$parent){
		$db =& JFactory::getDBO();

		?>
		<?php
		if ($row->id_perimeter_filter > 0 ){
			$query = "SELECT * FROM #__easysdi_perimeter_definition where id = $row->id_perimeter_filter";
			$db->setQuery( $query );
			$rows2 = $db->loadObject();
			helper_easysdi::generateHtmlPerimeterSelect($rows2,$row->id);

		}
		if ($parent == 0){
			echo "<tr>";
			echo "<td><select id=\"perimetersListPerimeter$row->id\"	onChange=\"recenterOnPerimeterPerimeter('perimetersListPerimeter$row->id')\"><option > </option></select></td>";

			echo "</tr>";
		}else{
			echo "<tr>";
			echo "<td><select id=\"perimetersListPerimeter$row->id\"	onChange=\"fillPerimeterParent ('filter$row->id','perimetersListPerimeter$row->id','perimetersListPerimeter$parent') \"><option > </option></select></td>";
			echo "</tr>";
			if ($row->searchbox == 1) {
				echo "<tr >";
				echo "<td><input size=5 length=5 type=\"text\" id =\"filter$row->id\" value=\"\" >"	;
				echo "<input onClick=\"fillPerimeterParent ('filter$row->id','perimetersListPerimeter$row->id','perimetersListPerimeter$parent') \" type=\"button\" value=\"".JText::_("EASYSDI_SEARCH")."\" ></td>"	;
				echo "</tr>";
			}
		}

	}

	function hasRight($partner_id,$right){

		$database =& JFactory::getDBO();

		$query = "SELECT count(*) FROM #__easysdi_community_actor a , #__easysdi_community_role b  WHERE a.role_id = b.role_id and partner_id = $partner_id and role_code = '$right'";


		$database->setQuery($query );
		$total = $database->loadResult();

		return ($total > 0 );
	}



	function generateMetadataHtml2($classId,$geoMD,$parentkey,$metadata_id,$root=0){

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
						//echo "<table><tr><td><fieldset><legend>".JText::_($row->description)."looking for //$key  ==> ".($i+1)." of  $count<br>"."</legend>";
						helper_easysdi::generateMetadataHtml2($row->id,$geoMD,$key."[".($i+1)."]",$metadata_id,0);
							
						//echo "</fieldset></td></tr></table>";
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
							
						?>
						<?php if ($rowFreetext->is_constant != 1) { ?>
<tr>
	<td><?php echo JText::_($row->description) ?></td>
	<td><?php } ?> <?php if( $rowFreetext->is_id == 1) { ?> <input
		size="80" type="text"
		<?php if ($rowFreetext->is_constant == 1) echo "READONLY='TRUE'"; ?>
		name="<?php echo "PARAM$row->id[]"?>" value="<?php echo $metadata_id;?>" /> <?php 
	}else{
		if( $rowFreetext->is_constant == 1) {
			?> <input size="80" type="text"
			<?php if ($rowFreetext->is_constant == 1) echo "READONLY='TRUE'";   ?>
		name="<?php echo "PARAM$row->id[]"?>"
		value="<?php echo htmlentities($rowFreetext->default_value);?>" /> <?php 
		}else{
			?> <input size="80" type="text"
			<?php if ($rowFreetext->is_constant == 1) echo "READONLY='TRUE'";   ?>
		name="<?php echo "PARAM$row->id[]"?>"
		value="<?php echo htmlentities($geoMD->getXPathResult("//".$key."/gco:CharacterString"))?>" />
		<?php
		}
	}
	?> <?php if ($rowFreetext->is_constant != 1) { ?></td>
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

	$query = "SELECT * FROM #__easysdi_metadata_classes_locfreetext a, #__easysdi_metadata_loc_freetext b WHERE a.classes_id = $row->id and a.loc_freetext_id = b.id";
	$database->setQuery($query);
	$rowsFreetext = $database->loadObjectList();
	if ($database->getErrorNum()) {
		$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
	}
	foreach($rowsFreetext as $rowFreetext){
		?>
<tr>
	<td><?php echo JText::_($row->description)."[$rowFreetext->lang]"?></td>
	<td><textarea name="<?php echo "PARAM$row->id[]"?>"
		rows="5" cols="40"><?php echo  ($geoMD->getXPathResult("//$key/gmd:LocalisedCharacterString[@locale='$rowFreetext->lang']"))?> </textarea>
	</td>
</tr>
		<?php
	}
	break;

case  "list" :

	$key = $parentkey;


	$query = "SELECT * FROM #__easysdi_metadata_classes_list a, #__easysdi_metadata_list b WHERE a.classes_id = $row->id and a.list_id = b.id";
	$database->setQuery($query);
	$rowsList = $database->loadObjectList();
	if ($database->getErrorNum()) {
		$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
	}
	foreach($rowsList as $rowList){
		?>
<table>
	<tr>
		<td><?php echo JText::_($row->description) ?></td>
		<td><select name="<?php "PARAM$row->id[]"?>"
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
			<?php if ($geoMD->isXPathResultCount("//".$key."[MD_TopicCategoryCode='$rowListContent->key']")>0){echo "selected";}?>><?php echo $rowListContent->value ;?>
			<?php echo "//".$key."[MD_TopicCategoryCode='$rowListContent->key']". "===>".$geoMD->getXPathResult("//".$key."/MD_TopicCategoryCode") ; ?></option>
			<?php

			} ?>
		</select></td>
	</tr>
</table>
			<?php
	}
	break;
			}
		}
			
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
		<?php if ($geoMD->isXPathResultCount("//gmd:MD_Metadata/".$iso_key."[gco:CharacterString='$rowListContent->key']")>0){echo "selected";}?>><?php echo $rowListContent->value ;?></option>
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


	function generateMetadata2($classId,$geoMD,$parentkey,$metadata_id,$root=0,$doc){


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

						helper_easysdi::generateMetadata2($row->id,$geoMD,$key."[".($i+1)."]",$metadata_id,0,&$doc);
							
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


		function generateMetadata($row,$tab_id,$metadata_standard_id,$iso_key,$doc,$n){
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

		//					$value = array_pop  ( $_POST["PARAM$row->class_id"] );
							$array  = JRequest::getVar("PARAM$row->id");
							$value = $array [$n];
		

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
	//$doc=$doc."<gmd:PT_FreeText><gmd:textGroup>";
	foreach($rowsFreetext as $rowFreetext){
		//$mainframe->enqueueMessage($_POST[$iso_key."{lang=$rowFreetext->lang}"],"ERROR");
		
		//$value = array_pop  ( $_POST["PARAM$row->class_id"] );
		$array  = JRequest::getVar("PARAM$row->id");
		$value = $array [$n];
		
		
	
		$doc=$doc."<gmd:LocalisedCharacterString locale=\"$rowFreetext->lang\">".htmlspecialchars    (stripslashes($value) )."</gmd:LocalisedCharacterString>";
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
			helper_easysdi::generateMetadata($rowClassesClasses,$tab_id,$metadata_standard_id,$iso_key."/".$rowClassesClasses->iso_key,&$doc,$n);
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
								$a = 		helper_easysdi::searchForLastEntry($rowClassesClasses,$metadata_standard_id);																				
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
			return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
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

}
?>