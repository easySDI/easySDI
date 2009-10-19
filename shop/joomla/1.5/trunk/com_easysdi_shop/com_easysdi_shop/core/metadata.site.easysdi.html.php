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
		
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');

//JHTML::script('ext-all.js', 'includes/js/extjs/');
JHTML::script('ext-base.js', 'administrator/components/com_easysdi_shop/ext/adapter/ext/');
JHTML::script('ext-all-debug.js', 'administrator/components/com_easysdi_shop/ext/');
JHTML::script('dynamic.js', 'administrator/components/com_easysdi_shop/js/');
JHTML::script('ExtendedField.js', 'administrator/components/com_easysdi_shop/js/');
JHTML::script('ExtendedFieldSet.js', 'administrator/components/com_easysdi_shop/js/');
JHTML::script('ExtendedFormPanel.js', 'administrator/components/com_easysdi_shop/js/');
JHTML::script('ExtendedHidden.js', 'administrator/components/com_easysdi_shop/js/');
JHTML::script('MultiSelect.js', 'administrator/components/com_easysdi_shop/js/');
class HTML_Metadata {


	function cleanText($text)
	{
	  $text = utf8_decode($text);
	  $text = str_replace("\n","\\n",$text);
	  $text = str_replace("'","\'",$text);
	  $text = utf8_encode($text);
	  return $text;
	}
		
	function editMetadata($root, $metadata_id, $xpathResults, $option)
	{
		$uri =& JUri::getInstance();
		$database =& JFactory::getDBO(); 
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base() . 'includes/js/extjs/resources/css/ext-all.css');
		$document->addStyleSheet($uri->base() . 'components/com_easysdi_shop/templates/css/form_layout_frontend.css');
		$document->addStyleSheet($uri->base() . 'components/com_easysdi_shop/templates/css/MultiSelect.css');
		
		$url = 'index.php?option='.$option.'&task=saveMetadata';
		?>
		<!-- Pour permettre le retour à la liste des produits depuis la toolbar Joomla -->
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
		</form>
		
			<script type="text/javascript">
				// Créer le formulaire qui va contenir la structure
				var form = new Ext.ux.ExtendedFormPanel({
						id:'metadataForm',
						url: 'index.php',
						labelAlign: 'left',
				        labelWidth: 200,
				        border: false,
				        collapsed:false,
				        renderTo:document.getElementById('maincolumn'),
				        buttons: [{
				            text: 'Envoyer',
				            handler: function(){
					        	form.getForm().submit({
								    	scope: this,
										method	: 'POST',
										success: function(form, action) 
										{
											console.log("SUCCESS !!!");
											console.log(form);
											console.log(action);
										},
										failure: function(form, action) 
										{
											console.log("FAIL !!!");
											console.log(action.result.errors.xml);
										},
										url:'<?php echo $url;?>'
									});
					        	}
				        }]
				    });
					
				var <?php echo 'fieldset'.$root[0]->id;?> = new Ext.form.FieldSet({id:'<?php echo "//".$root[0]->iso_key;?>', cls: 'easysdi_shop_backend_form', title:'<?php echo $root[0]->name;?>', xtype: 'fieldset', tabtip:'<?php echo $root[0]->label;?>'});
				form.add(<?php echo 'fieldset'.$root[0]->id;?>);
			</script>
			<?php 
				$queryPath="/";
				// Bouclage pour construire la structure
				$node = $xpathResults->query($queryPath."/".$root[0]->iso_key);
				$nodeCount = $node->length;
				
				HTML_Metadata::buildTree($database, $root[0]->id, $root[0]->id, "//".$root[0]->iso_key, $xpathResults, $node->item(0), $queryPath, $root[0]->iso_key, $option);
			?>
			<script type="text/javascript">
				form.add(createHidden('option', 'option', '<?php echo $option;?>'));
				form.add(createHidden('task', 'task', 'saveMetadata'));
				form.add(createHidden('metadata_id', 'metadata_id', '<?php echo $metadata_id; ?>'));
				
	    		// Affichage du formulaire
	    		form.doLayout();
/*	    		var fieldsets = form.findByType("fieldset");
	    		fieldsets.each(function(fieldset)
	    		{
	    		});
	    		form.doLayout();*/
		    	form.render(document.body);
			</script>
		<?php
		
	}
	
	function buildTree($database, $parent, $parentFieldset, $parentName, $xpathResults, $scope, $queryPath, $currentIsocode, $option)
	{
		//echo "<hr>SCOPE: ".$scope->nodeName."<br>";
		$classScope = $scope;
		$attributScope = $scope;
		$rowChilds = array();
		
		// Stockage du path pour atteindre ce noeud du XML
		$queryPath = $queryPath."/".$currentIsocode;
		
		// Traitement des enfants de type freetext 
		$rowAttributeChilds = array();
		//$query = "SELECT rel.id as rel_id, rel.name as rel_name, rel.isocode as rel_isocode, rel.upperbound as rel_upperbound, rel.lowerbound as rel_lowerbound, rel.attribute_id as attribute_id, rel.rendertype_id as rendertype_id, a.* FROM #__sdi_attributerelation rel, #__sdi_attribute as a WHERE rel.attribute_id=a.id AND rel.class_id=".$parent;
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type = 'freetext' and rel.classes_from_id=".$parent." ORDER BY c.ordering";
		$database->setQuery( $query );
		$rowAttributeChilds = array_merge( $rowAttributeChilds, $database->loadObjectList() );
		
		foreach($rowAttributeChilds as $child)
		{
			// Stockage du path pour atteindre ce noeud du XML
			//$path = $queryPath."/".$child->iso_key;
			$path = $child->iso_key;
			
			// Traitement de la multiplicité
			// Récupération du path du bloc de champs qui va être créé pour construire le nom
			$name = $parentName."/".$child->iso_key;
			
			// Selon le type de noeud, on lit un type de balise
			$query = "SELECT f.* FROM #__easysdi_metadata_freetext f, #__easysdi_metadata_classes_freetext rel WHERE rel.freetext_id = f.id and rel.classes_id=".$child->classes_to_id;
			$database->setQuery( $query );
			$type = $database->loadObject();
			
			// Traitement de chaque attribut
			if ($type->is_id)
			{
				$path = $path."/gco:CharacterString";
				$name = $name."/gco:CharacterString";
			}
			else if ($type->is_date)
			{		
				$path = $path."/gco:Date";
				$name = $name."/gco:Date";
			}
			else if ($type->is_datetime)
			{		
				$path = $path."/gco:DateTime";
				$name = $name."/gco:DateTime";
			}
			else if ($type->is_number)
			{
				$path = $path."/gco:Decimal";
				$name = $name."/gco:Decimal";
			}
			else if ($type->is_integer)
			{
				$path = $path."/gco:Integer";
				$name = $name."/gco:Integer";
			}
			else if ($type->is_constant)
			{
				$path = $path."/gco:CharacterString";
				$name = $name."/gco:CharacterString";
			}
			else
			{
				$path = $path."/gco:CharacterString";
				$name = $name."/gco:CharacterString";
			}
					
			// Valeur de l'attribut 
			$node = $xpathResults->query($path, $attributScope);
			
			for ($pos=0; $pos<$node->length; $pos++)
			{
				if ($node->length > 0)
					$nodeValue = HTML_Metadata::cleanText($node->item($pos)->nodeValue);
				else
					$nodeValue = "";
					
				$currentName = $name."__".($pos+1);
					
				// Traitement de chaque attribut
				if ($type->default_value <> "" and $nodeValue == "")
					$nodeValue = HTML_Metadata::cleanText($type->default_value);

				if ($pos==0)
				{
					if ($type->is_id)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createTextField('<?php echo $currentName;?>', '<?php echo $child->name;?>',true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '<?php echo $child->length;?>', true));
						</script>
						<?php
					}
					else if ($type->is_date)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createDateField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>'));
						</script>
						<?php
					}
					else if ($type->is_datetime)
					{
						//echo $attributScope->nodeName." - ".$path." - ".$nodeValue."<br>";
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createDateTimeField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>'));
						</script>
						<?php
					}
					else if ($type->is_number)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createNumberField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '<?php echo $type->default_value;?>', true, 15));
						</script>
						<?php
					}
					else if ($type->is_integer)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createNumberField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '<?php echo $type->default_value;?>', false, 0));
						</script>
						<?php
					}
					else
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createTextArea('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '500', '50'));
						</script>
						<?php
					}
					
					?>
					<script type="text/javascript">
						// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
						<?php echo 'fieldset'.$parent;?>.add(createHidden('<?php echo $currentName.'_index';?>', '<?php echo $currentName.'_index';?>', '1'));
					</script>
					<?php
				}
				else
				{
					$currentName = $name."__".($pos+1);
					$master = substr($currentName,0,strlen($currentName)-2)."1";
					
					?>
					<script type="text/javascript">
						var master = Ext.getCmp('<?php echo $master;?>');
						
						var index = Ext.getCmp('<?php echo $master."_index";?>');
						oldIndex = index.getValue();
						index.setValue(Number(oldIndex)+1);
						
					</script>
					<?php
					if ($type->is_id)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createTextField('<?php echo $currentName;?>', '<?php echo $child->name;?>',true, true, master, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '<?php echo $child->length;?>', true));
						</script>
						<?php
					}
					else if ($type->is_date)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createDateField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, true, master, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>'));
						</script>
						<?php
					}
					else if ($type->is_datetime)
					{
						//echo $attributScope->nodeName." - ".$path." - ".$nodeValue."<br>";
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createDateTimeField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, true, master, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>'));
						</script>
						<?php
					}
					else if ($type->is_number)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createNumberField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, true, master, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '<?php echo $type->default_value;?>', true, 15));
						</script>
						<?php
					}
					else if ($type->is_integer)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createNumberField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, true, master, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '<?php echo $type->default_value;?>', false, 0));
						</script>
						<?php
					}
					else
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createTextArea('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, true, master, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '500', '50'));
						</script>
						<?php
					}
				}
			}
			
			// Ajout d'une occurence de création si la classe est obligatoire
			// et qu'il n'y a aucune occurence de celle-ci dans le XML
				
			if ($node->length==0 and $child->lowerbound>=0)
			{
				$nodeValue = "";
					
				$currentName = $name."__1";
				//echo $nodeValue."<br>";
				//echo $path."(".$node->length."): ".$nodeValue."<br>";
					
				// Traitement de chaque attribut
				if ($type->default_value <> "")
					$nodeValue = HTML_Metadata::cleanText($type->default_value);
					
				if ($type->is_id)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createTextField('<?php echo $currentName;?>', '<?php echo $child->name;?>',true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '<?php echo $child->length;?>', true));
						</script>
						<?php
					}
					else if ($type->is_date)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createDateField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>'));
						</script>
						<?php
					}
					else if ($type->is_datetime)
					{
						//echo $attributScope->nodeName." - ".$path." - ".$nodeValue."<br>";
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createDateTimeField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>'));
						</script>
						<?php
					}
					else if ($type->is_number)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createNumberField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '<?php echo $type->default_value;?>', true, 15));
						</script>
						<?php
					}
					else if ($type->is_integer)
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createNumberField('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '<?php echo $type->default_value;?>', false, 0));
						</script>
						<?php
					}
					else
					{
						?>
						<script type="text/javascript">
							// La relation entre la classe et l'attribut
							// L'attribut
							<?php echo 'fieldset'.$parent;?>.add(createTextArea('<?php echo $currentName;?>', '<?php echo $child->name;?>', true, false, null, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', '<?php echo $nodeValue;?>', '500', '50'));
						</script>
						<?php
					}
					
					?>
					<script type="text/javascript">
						// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
						<?php echo 'fieldset'.$parent;?>.add(createHidden('<?php echo $currentName.'_index';?>', '<?php echo $currentName.'_index';?>', '1'));
					</script>
					<?php
			}
		}
		
		// Traitement des enfants de type list 
		$rowListClass = array();
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." ORDER BY c.ordering";
		//$query = "SELECT c.lowerbound as lowerbound, c.upperbound as upperbound, c.translation as c_translation, c.iso_key as c_isokey, l.multiple as multiple, l.name as label, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_list rel, #__easysdi_metadata_list l WHERE rel.classes_id = c.id and rel.list_id=l.id and c.type = 'list' and rel.classes_id=".$parent;
		$database->setQuery( $query );
		$rowListClass = array_merge( $rowListClass, $database->loadObjectList() );
		
	 foreach($rowListClass as $child)
	  {
	  	
	   $content = array();
	   $query = "SELECT cont.id as cont_id, cont.code_key as cont_code_key, cont.translation as cont_translation, c.lowerbound as lowerbound, c.upperbound as upperbound, c.translation as c_translation, c.iso_key as c_isokey, l.multiple as multiple, l.name as label, l.translation as l_translation, l.iso_key as l_iso_key, l.codeValue as l_codeValue, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_list rel, #__easysdi_metadata_list l, #__easysdi_metadata_list_content cont WHERE rel.classes_id = c.id and rel.list_id=l.id and cont.list_id = l.id and c.type = 'list' and rel.classes_id=".$child->classes_to_id;
	   $database->setQuery( $query );
	   $content = $database->loadObjectList();
	   
	   $dataValues = array();
	   $nodeValues = array();
	   
	   // Traitement de la multiplicité
	   // Récupération du path du bloc de champs qui va être créé pour construire le nom
	   $listName = $parentName."/".$child->iso_key."__1";
	  
	   // Construction de la liste
	   foreach ($content as $cont)
	   {
	   		$dataValues[$cont->cont_code_key] = JText::_($cont->cont_translation);
	   }
	   //print_r($dataValues);echo "<br>";

	   $relNode = $xpathResults->query($child->iso_key, $attributScope);
	   
	   for ($pos=0;$pos<$relNode->length;$pos++)
	   {
		   $listNode = $xpathResults->query($content[0]->l_iso_key, $relNode->item($pos));
		   if ($listNode->length > 0)
		     if ($content[0]->l_codeValue)
		      $nodeValues[]=HTML_Metadata::cleanText($listNode->item(0)->getAttribute('codeListValue'));
		     else
		      $nodeValues[]=HTML_Metadata::cleanText($listNode->item(0)->nodeValue);
		    else
		     $nodeValues[]="";
	   }
	   
	   // Deux traitement pour deux types de listes
	   if ($content[0]->multiple)
	   {
	   	?>
	    <script type="text/javascript">
	     var valueList = <?php print_r(HTML_Metadata::array2extjs($dataValues));?>;
	     var selectedValueList = <?php echo json_encode($nodeValues);?>;
	     // La liste
	     //<?php echo 'fieldset'.$parentFieldset;?>.add(createMultiSelector('<?php echo $listName;?>', '<?php echo JText::_($content[0]->l_translation);?>', true, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', valueList, selectedValueList));
	     // L'index pour les potentiels clones de la liste 
	     <?php echo 'fieldset'.$parentFieldset;?>.add(createHidden('<?php echo $listName.'_index';?>', '<?php echo $listName.'_index';?>', '1'));
	    </script>
	    <?php
	   }
	   else
	   {
	    ?>
	    <script type="text/javascript">
	     var valueList = <?php print_r(HTML_Metadata::array2extjs($dataValues));?>;
	     var selectedValueList = <?php echo json_encode($nodeValues);?>;
	     // La liste
	     <?php echo 'fieldset'.$parentFieldset;?>.add(createComboBox('<?php echo $listName;?>', '<?php echo JText::_($content[0]->l_translation);?>', true, '<?php echo $child->lowerbound;?>', '<?php echo $child->upperbound;?>', valueList, selectedValueList));
	     // L'index pour les potentiels clones de la liste 
	     <?php echo 'fieldset'.$parentFieldset;?>.add(createHidden('<?php echo $listName.'_index';?>', '<?php echo $listName.'_index';?>', '1'));
	    </script>
	    <?php
	   }
	  }	
			
		// Traitement des enfants de type local freetext 
		$rowLocText = array();
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type = 'locfreetext' and rel.classes_from_id=".$parent." ORDER BY c.ordering";
		$database->setQuery( $query );
		$rowLocText = array_merge( $rowLocText, $database->loadObjectList() );
		
		foreach($rowLocText as $child)
		{
			// Stockage du path pour atteindre ce noeud du XML
			$queryPath = $child->iso_key."/gmd:LocalisedCharacterString";
			
			$relNode = $xpathResults->query($child->iso_key, $attributScope);

			for($pos=0;$pos<=$relNode->length;$pos++)
	   		{
		   		// Traitement de la multiplicité
				// Récupération du path du bloc de champs qui va être créé pour construire le nom
				$LocName = $parentName."/".$child->iso_key."__".($pos+1);
				
				if ($pos==0)
				{
					?>
					<script type="text/javascript">
						var <?php echo 'fieldset'.$child->classes_to_id;?> = createFieldSet('<?php echo $LocName;?>', '<?php echo $child->name;?>', true, false, true, true, true, null, <?php echo $child->lowerbound;?>, 5); 
						<?php echo 'fieldset'.$parent;?>.add(<?php echo 'fieldset'.$child->classes_to_id;?>);	
						// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
						<?php echo 'fieldset'.$parent;?>.add(createHidden('<?php echo $LocName.'_index';?>', '<?php echo $LocName.'_index';?>', '1'));
					</script>
					<?php
					
					// Création des enfants langue
					$langages = array();
					$query = "SELECT loc.* FROM #__easysdi_metadata_classes_locfreetext rel, #__easysdi_metadata_loc_freetext loc WHERE rel.loc_freetext_id = loc.id and rel.classes_id=".$child->classes_to_id;
					$database->setQuery( $query );
					$langages = array_merge( $langages, $database->loadObjectList() );
					
					foreach($langages as $lang)
					{
						$LocLangName = $LocName."/gmd:LocalisedCharacterString/".$lang->lang."__".($pos+1);
						
						$node = $xpathResults->query($queryPath."[@locale='".$lang->lang."']", $attributScope);
						if ($node->	length > 0)
							$nodeValue = HTML_Metadata::cleanText($node->item($pos)->nodeValue);
						else
							$nodeValue = "";
						
						?>
						<script type="text/javascript">
							<?php echo 'fieldset'.$child->classes_to_id;?>.add(createTextArea('<?php echo $LocLangName;?>', '<?php echo JText::_($lang->translation);?>', true, false, null, '1', '1', '<?php echo $nodeValue;?>', '500', '50'));
							// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
							<?php echo 'fieldset'.$child->classes_to_id;?>.add(createHidden('<?php echo $LocLangName.'_index';?>', '<?php echo $LocLangName.'_index';?>', '1'));
						</script>
						<?php
					}
				}
				else
				{
					$master = $parentName."/".$child->iso_key."__1";
					?>
					<script type="text/javascript">
						var master = Ext.getCmp('<?php echo $master;?>');
						var index = Ext.getCmp('<?php echo $master."_index";?>');
						oldIndex = index.getValue();
						index.setValue(Number(oldIndex)+1);
						var <?php echo 'fieldset'.$child->classes_to_id;?> = createFieldSet('<?php echo $LocName;?>', '<?php echo $child->name;?>', true, true, true, true, true, master, <?php echo $child->lowerbound;?>, 5); 
						<?php echo 'fieldset'.$parent;?>.add(<?php echo 'fieldset'.$child->classes_to_id;?>);	
						
						master.manageIcons(master);
					</script>
					<?php
					
					// Création des enfants langue
					$langages = array();
					$query = "SELECT loc.* FROM #__easysdi_metadata_classes_locfreetext rel, #__easysdi_metadata_loc_freetext loc WHERE rel.loc_freetext_id = loc.id and rel.classes_id=".$child->classes_to_id;
					$database->setQuery( $query );
					$langages = array_merge( $langages, $database->loadObjectList() );
					
					foreach($langages as $lang)
					{
						$LocLangName = $LocName."/gmd:LocalisedCharacterString/".$lang->lang."__".($pos+1);
						
						$node = $xpathResults->query($queryPath."[@locale='".$lang->lang."']", $attributScope);
						if ($node->	length > 0)
							$nodeValue = HTML_Metadata::cleanText($node->item($pos-1)->nodeValue);
						else
							$nodeValue = "";
						
						?>
						<script type="text/javascript">
							<?php echo 'fieldset'.$child->classes_to_id;?>.add(createTextArea('<?php echo $LocLangName;?>', '<?php echo JText::_($lang->translation);?>', true, false, null, '1', '1', '<?php echo $nodeValue;?>', '500', '50'));
							// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
							<?php echo 'fieldset'.$child->classes_to_id;?>.add(createHidden('<?php echo $LocLangName.'_index';?>', '<?php echo $LocLangName.'_index';?>', '1'));
						</script>
						<?php
					}
				}
	   		}
			
	   		// Ajout d'une occurence de création si la classe est obligatoire
			// et qu'il n'y a aucune occurence de celle-ci dans le XML
			if ($relNode->length==0 and $child->lowerbound>=0)
			{
			// Traitement de la multiplicité
				// Récupération du path du bloc de champs qui va être créé pour construire le nom
				$LocName = $parentName."/".$child->iso_key."__2";
				$master = $parentName."/".$child->iso_key."__1";
					
				?>
				<script type="text/javascript">
					var master = Ext.getCmp('<?php echo $master;?>');
					var index = Ext.getCmp('<?php echo $master."_index";?>');
					oldIndex = index.getValue();
					index.setValue(Number(oldIndex)+1);
					var <?php echo 'fieldset'.$child->classes_to_id;?> = createFieldSet('<?php echo $LocName;?>', '<?php echo $child->name;?>', true, true, true, true, true, master, <?php echo $child->lowerbound;?>, 5); 
					<?php echo 'fieldset'.$parent;?>.add(<?php echo 'fieldset'.$child->classes_to_id;?>);	
					
					master.manageIcons(master);
				</script>
				<?php
				
				// Création des enfants langue
				$langages = array();
				$query = "SELECT loc.* FROM #__easysdi_metadata_classes_locfreetext rel, #__easysdi_metadata_loc_freetext loc WHERE rel.loc_freetext_id = loc.id and rel.classes_id=".$child->classes_to_id;
				$database->setQuery( $query );
				$langages = array_merge( $langages, $database->loadObjectList() );
				
				foreach($langages as $lang)
				{
					$LocLangName = $LocName."/gmd:LocalisedCharacterString/".$lang->lang."__1";
					
					$node = $xpathResults->query($queryPath."[@locale='".$lang->lang."']", $attributScope);
					if ($node->	length > 0)
						$nodeValue = HTML_Metadata::cleanText($node->item(0)->nodeValue);
					else
						$nodeValue = "";
					
					?>
					<script type="text/javascript">
						<?php echo 'fieldset'.$child->classes_to_id;?>.add(createTextArea('<?php echo $LocLangName;?>', '<?php echo JText::_($lang->translation);?>', true, false, null, '1', '1', '<?php echo $nodeValue;?>', '500', '50'));
						// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
						<?php echo 'fieldset'.$child->classes_to_id;?>.add(createHidden('<?php echo $LocLangName.'_index';?>', '<?php echo $LocLangName.'_index';?>', '1'));
					</script>
					<?php
				}
			}
		}
		
		// Récupération des classes enfants du noeud
		$rowClassChilds = array();
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='class' and rel.classes_from_id=".$parent." ORDER BY c.ordering";
		$database->setQuery( $query );
		$rowClassChilds = array_merge( $rowClassChilds, $database->loadObjectList() );
		
		foreach($rowClassChilds as $child)
		{
			// Compte du nombre d'occurence de ce noeud (Multiplicité)
			$node = $xpathResults->query($child->iso_key, $classScope);
			$nodeCount = $node->length;
			
			// Traitement de la multiplicité
			// Récupération du path du bloc de champs qui va être créé pour construire le nom
			$name = $parentName."/".$child->iso_key;
			
			// Cas de la classe qui n'est pas une relation
			if (!$child->is_relation)
			{
				// Flag d'index dans le nom
				$name = $parentName."/".$child->iso_key."__1";
			
				if ($nodeCount > 0)
				{
					$classScope = $node->item(0);
				}
				else
				{
					$classScope = $scope;
				}
				
				// Parcours récursif des classes
				?>
				<script type="text/javascript">
					var <?php echo 'fieldset'.$child->classes_to_id;?> = createFieldSet('<?php echo $name;?>', '', false, false, false, false, true, null, <?php echo $child->lowerbound;?>, <?php echo $child->upperbound;?>); 
					<?php echo 'fieldset'.$parent;?>.add(<?php echo 'fieldset'.$child->classes_to_id;?>);	
					// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
					<?php echo 'fieldset'.$parentFieldset;?>.add(createHidden('<?php echo $name.'_index';?>', '<?php echo $name.'_index';?>', '1'));
				</script>
				<?php
				
				// Récupération des codes ISO et appel récursif de la fonction
				$nextIsocode = $child->iso_key;
				HTML_Metadata::buildTree($database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $option);
			}
			//Cas de la classe relation
			else
			{
				for ($pos=0; $pos<=$nodeCount; $pos++)
				{
					// Construction du master
					if ($pos==0)
					{
						// Flag d'index dans le nom
						$name = $parentName."/".$child->iso_key."__".($pos+1);
					
						if ($nodeCount > 0)
						{
							$classScope = $node->item($pos);
						}
						else
						{
							$classScope = $scope;
						}
						
						// Construction de la relation
						?>
						<script type="text/javascript">
							// Créer un nouveau fieldset
							var <?php echo 'fieldset'.$child->classes_to_id;?> = createFieldSet('<?php echo $name;?>', '<?php echo $child->name;?>', true, false, true, true, true, null, <?php echo $child->lowerbound;?>, <?php echo $child->upperbound;?>); 
							<?php echo 'fieldset'.$parent;?>.add(<?php echo 'fieldset'.$child->classes_to_id;?>);	
							// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
							<?php echo 'fieldset'.$parentFieldset;?>.add(createHidden('<?php echo $name.'_index';?>', '<?php echo $name.'_index';?>', '1'));
						</script>
						<?php
						
						// Récupération des codes ISO et appel récursif de la fonction
						$nextIsocode = $child->iso_key;
						
						HTML_Metadata::buildTree($database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $option);
					}
					else
					{
						// Création du clone
						// Flag d'index dans le nom
						$name = $parentName."/".$child->iso_key."__".($pos+1);
					
						$master = $parentName."/".$child->iso_key."__1";
						if ($nodeCount > 0)
						{
							$classScope = $node->item($pos-1);
						}
						else
						{
							$classScope = $scope;
						}	
						
						// Construction de la relation
						?>
						<script type="text/javascript">
							var master = Ext.getCmp('<?php echo $master;?>');
							
							var index = Ext.getCmp('<?php echo $master."_index";?>');
							oldIndex = index.getValue();
							index.setValue(Number(oldIndex)+1);
							// Créer un nouveau fieldset
							var <?php echo 'fieldset'.$child->classes_to_id;?> = createFieldSet('<?php echo $name;?>', '<?php echo $child->name;?>', true, true, true, true, true, master, <?php echo $child->lowerbound;?>, <?php echo $child->upperbound;?>); 
							<?php echo 'fieldset'.$parent;?>.add(<?php echo 'fieldset'.$child->classes_to_id;?>);	
							
							master.manageIcons(master);
						</script>
						<?php
						
						$nextIsocode = $child->iso_key;
													
						// Récupération des codes ISO et appel récursif de la fonction
						HTML_Metadata::buildTree($database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $option);
					}	
				}
				
				// Ajout d'une occurence de création si la classe est obligatoire
				// et qu'il n'y a aucune occurence de celle-ci dans le XML
				if ($nodeCount==0 and $child->lowerbound>0)
				{
					// Création du clone
					// Flag d'index dans le nom
					$name = $parentName."/".$child->iso_key."__2";
				
					$master = $parentName."/".$child->iso_key."__1";
					
					$classScope = $scope;
					
					// Construction du fieldset
					?>
					<script type="text/javascript">
						var master = Ext.getCmp('<?php echo $master;?>');
						
						var index = Ext.getCmp('<?php echo $master."_index";?>');
						oldIndex = index.getValue();
						index.setValue(Number(oldIndex)+1);
						// Créer un nouveau fieldset
						var <?php echo 'fieldset'.$child->classes_to_id;?> = createFieldSet('<?php echo $name;?>', '<?php echo $child->name;?>', true, true, true, true, true, master, <?php echo $child->lowerbound;?>, <?php echo $child->upperbound;?>); 
						<?php echo 'fieldset'.$parent;?>.add(<?php echo 'fieldset'.$child->classes_to_id;?>);	
						
						master.manageIcons(master);
					</script>
					<?php
					
					$nextIsocode = $child->iso_key;
												
					// Récupération des codes ISO et appel récursif de la fonction
					HTML_Metadata::buildTree($database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $option);
				}
			}
		}
	}
	
	
function array2extjs($arr) {
	$extjsArray = "";
	 
    foreach($arr as $key=>$value) 
    {
    	$extjsArray .= "[";
    	$extjsArray .= "'".$key."', ";
        $extjsArray .= "'".$value."'";
        $extjsArray .= "], ";
    } 
    $extjsArray = '[' . $extjsArray. ']';
    return str_replace("], ]", "]]", $extjsArray);//Return ExtJS array 
} 
	
	
function listMetadataTabs($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataStandardClasses\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TABS_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TABS_ID"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TABS_TEXT"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_PARTNER_NAME"); ?></th>																															
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
				<td><?php echo $row->id; ?></td>				
				<td><?php echo $row->text; ?></td>
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>																											
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
	  	<input type="hidden" name="task" value="listMetadataTabs" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	
	
function editMetadataTabs($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
														
		
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">	
	<table border="0" cellpadding="3" cellspacing="0">			
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_TEXT_KEY"); ?></td>
		<td><input size="50" type="text" name ="text" value="<?php echo $row->text?>"> </td>							
	</tr>							

	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_TABS_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				

	 
	<input type="hidden" name="order" value="0" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
	</table>	
</form>
	
	<?php 	
		
	}
	
function listStandardClasses($use_pagination, $rows, $pageNav,$option,$type){
	
		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$partner = new partnerByUserId($database);
		$partner->load($user->id);		

		$database->setQuery( "SELECT id AS value, name AS text FROM #__easysdi_metadata_standard WHERE is_deleted =0 AND (partner_id in (SELECT partner_id FROM #__easysdi_community_partner where  root_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id) OR  partner_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id)  OR root_id = $partner->partner_id OR  partner_id = $partner->partner_id))" );
						
 
			$types =  $database->loadObjectList() ;													
	
		$partners = array();
		
		?>
		<script>
		function submitform(pressbutton){
				if (pressbutton) {
						document.standardClassForm.task.value=pressbutton;
					}
				if (typeof document.standardClassForm.onsubmit == "function") {
						document.standardClassForm.onsubmit();	
						}
				document.standardClassForm.submit();
		}
		
		</script>
		
	<form action="index.php" method="post" id="standardClassForm" name="standardClassForm">
		
		<table width="100%">
			<tr>																																			
											
				<td class="user"><b><?php echo JText::_("EASYSDI_TITLE_STANDARD"); ?></b><?php if ($types) {echo JHTML::_("select.genericlist", $types, 'type', 'size="1" class="inputbox" onChange="javascript:submitform(\'listMetadataClasses\');"', 'value', 'text', $type );} ?></td>
			</tr>
		
	
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_ID"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_STANTARD_NAME"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_CLASS_NAME"); ?></th>																							
			
			</tr>
		</thead>
		
		<tbody>		
<?php
if ($rows){ 
		$k = 0;
		$i=0;
		
		foreach ($rows as $row)
		{				  				
?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>												
				<td><?php echo $row->id; ?></td>				
				<td><?php echo $row->standard_name; ?></td>
				<td><?php echo $row->class_name; ?></td>
										
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
		}
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
	  	<input type="hidden" name="task" id="task" value="listMetadataStandardClasses" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
	  <button type="button" onClick="document.getElementById('task').value='newStandard';document.getElementById('standardClassForm').submit();" ><?php echo JText::_("EASYSDI_NEW_STANDARD"); ?></button>			
	 <button type="button" onClick="document.getElementById('task').value='newStandardClass';document.getElementById('standardClassForm').submit();" ><?php echo JText::_("EASYSDI_NEW_STANDARD_CLASS"); ?></button>
	  <button type="button" onClick="document.getElementById('task').value='editStandardClass';document.getElementById('standardClassForm').submit();" ><?php echo JText::_("EASYSDI_EDIT_STANDARD_CLASS"); ?></button>
	  
	  
<?php
		
}	
	
function editStandardClasses($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		$partner = new partnerByUserId($database);
		$partner->load($user->id);		
		
	
		
		$tabslist = array();
		$tabslist[] = JHTML::_('select.option','0', JText::_("EASYSDI_TABS_LIST") );
		$database->setQuery( "SELECT id AS value,  text AS text FROM #__easysdi_metadata_tabs  " );
		$tabslist = $database->loadObjectList() ;
		
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		
			
		$classeslist = array();
		$classeslist[] = JHTML::_('select.option','0', JText::_("EASYSDI_CLASS_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_classes  WHERE is_final = 1 ORDER BY name" );
		$classeslist = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		
		$selClassesList = array();		
		$database->setQuery( "SELECT class_id AS value FROM #__easysdi_metadata_standard_classes  WHERE id = $row->id ");
		$selClassesList  = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}						
		
		
		$standardlist = array();
		$standardlist[] = JHTML::_('select.option','0', JText::_("EASYSDI_STANDARD_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_standard  WHERE is_deleted =0 AND ORDER BY name" );
		$standardlist = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
					
		$selStandardList = array();		
		$database->setQuery( "SELECT standard_id AS value FROM #__easysdi_metadata_standard_classes  WHERE id = $row->id ");
		$selStandardList  = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}	
		
		
?>
<form action="index.php" method="post" name="standardForm" id="standardForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
									
	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$classeslist, 'class_id', 'size="1" class="inputbox"', 'value', 'text', $selClassesList ); ?></td>
	 </tr>
	 <tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_TAB_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$tabslist, 'tab_id', 'size="1" class="inputbox"', 'value', 'text', $row->tab_id ); ?></td>							
	</tr> 
	 
	 		
	 
	<input type="hidden" name="standard_id" value="<?php echo $row->standard_id ;?>">
	<input type="hidden" name="partner_id" value="<?php echo $partner->partner_id; ?>">	 
	<input type="hidden" name="order" value="0" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" id="task" name="task" value="" />		
	</table>
</form>
	
			  <button type="button" onClick="document.getElementById('task').value='saveStandardClass';document.getElementById('standardForm').submit();" ><?php echo JText::_("EASYSDI_SAVE_STANDARD_CLASS"); ?></button>			
		<button type="button" onClick="document.getElementById('task').value='cancelStandardClass';document.getElementById('standardForm').submit();" ><?php echo JText::_("EASYSDI_CANCEL_STANDARD_CLASS"); ?></button>
	
	<?php 	
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
function listStandard($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataStandard\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_NAME"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_INHERITED"); ?></th>																							
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<td><?php echo $row->name; ?></td>
				<td><?php echo $row->inherited; ?></td>							
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
	  	<input type="hidden" name="task" value="listMetadataStandard" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	
	
function editStandard($row,$id, $option ){
		global  $mainframe;
				
		$database =& JFactory::getDBO(); 

		$standards = array();
		$database->setQuery( "SELECT id AS value, name AS text FROM #__easysdi_metadata_standard  where is_global = 1 ORDER BY name" );
		$standards =  $database->loadObjectList() ;
		$user = JFactory::getUser();
		$partner = new partnerByUserId($database);
		$partner->load($user->id);		
		
?>
<form action="index.php" method="post" name="standardForm" id="standardForm" class="standardForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>							

	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_INHERITED"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$standards, 'inherited', 'size="1" class="inputbox"', 'value', 'text',  $row->inherited ); ?></td>		 
	</tr>
	 
	 
	<input type="hidden" name="partner_id" value="<?php echo $partner->partner_id	;?>"/> 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" id="task" name="task" value="" />		
			
	
	 
	</table>
</form>
	 <button type="button" onClick="document.getElementById('task').value='saveStandard';document.getElementById('standardForm').submit();" ><?php echo JText::_("EASYSDI_SAVE_STANDARD"); ?></button>			
		<button type="button" onClick="document.getElementById('task').value='cancelStandard';document.getElementById('standardForm').submit();" ><?php echo JText::_("EASYSDI_CANCEL_STANDARD"); ?></button>
	
	<?php 	
		
	}
	
	
	
	
	
	
	
function listExt($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_EXT"));
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataLocfreetext\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_EXT_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_EXT_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_EXT_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_EXT_NAME"); ?></th>				
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<td><?php echo $row->name; ?></td>
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
	  	<input type="hidden" name="task" value="listMetadataExt" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	
	
	
function listLocfreetext($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_LOCFREETEXT"));
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataLocfreetext\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_NAME"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_LANG"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_DEFAULT_VALUE"); ?></th>																			
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<td><?php echo $row->name; ?></td>
				<td><?php echo $row->lang; ?></td>
				<td><?php echo $row->default_value; ?></td>				
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
	  	<input type="hidden" name="task" value="listMetadataFreetext" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	
	
function editExt($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_EXT_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
			
	
	</table>
</form>
	<?php 	
		
	}

function editLocfreetext($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_DESCRIPTION"); ?></td>
		<td><input size="50" type="text" name ="description" value="<?php echo $row->description?>"> </td>							
	</tr>
		<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_LANG"); ?></td>
		<td><input size="50" type="text" name ="lang" value="<?php echo $row->lang?>"> </td>		 
	</tr>								
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_IS_GLOBAL"); ?></td>
		<td><select name="is_global" > <option value="1" <?php if($row->is_global == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_global == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_DEFAULT_VALUE"); ?></td>
		<td><input size="50" type="text" name ="default_value" value="<?php echo $row->default_value?>"> </td>		 
	</tr>
	 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
			
	
	</table>
</form>
	<?php 	
		
	}
	
	
	
function listClass($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_CLASS"));
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataClass\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_CLASS_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_CLASS_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_CLASS_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_CLASS_NAME"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_CLASS_ISOKEY"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_CLASS_TYPE"); ?></th>																						
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>		 				 		
				<td><?php echo $database->loadResult(); ?></td>
				
				<?php $link =  "index.php?option=$option&amp;task=editMetadataClass&cid[]=$row->id";?>
				<td><a href="<?php echo link;?>"><?php echo $row->name; ?></a></td>												
				<td><?php echo $row->iso_key; ?></td>
				<td><?php echo $row->type; ?></td>						
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
	  	<input type="hidden" name="task" value="listMetadataClass" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	
	
	function editClass($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
		$freetextlist = array();
		$freetextlist[] = JHTML::_('select.option','0', JText::_("EASYSDI_FREETEXT_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_freetext ORDER BY name" );
		$freetextlist = array_merge( $freetextlist, $database->loadObjectList() );

		$locfreetextlist = array();
		$locfreetextlist[] = JHTML::_('select.option','0', JText::_("EASYSDI_LOCFREETEXT_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_loc_freetext ORDER BY name" );
		$locfreetextlist = $database->loadObjectList() ;
		
		$classeslist = array();
		$classeslist[] = JHTML::_('select.option','0', JText::_("EASYSDI_CLASS_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_classes  where  id <> $row->id ORDER BY name" );
		$classeslist = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		
		$selClassesList = array();
		if($row->type == 'class'){
			
			$database->setQuery( "SELECT classes_to_id AS value FROM #__easysdi_metadata_classes_classes  WHERE classes_from_id = $row->id ");
			$selClassesList  = $database->loadObjectList() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
			
		}
		$selFreetextList = array();
		if($row->type == 'freetext'){
			$database->setQuery( "SELECT freetext_id AS value FROM #__easysdi_metadata_classes_freetext  WHERE classes_id = $row->id ");
			$selFreetextList   = $database->loadObjectList() ;		
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		}
		
		$selLocfreetextList = array();
		if($row->type == 'locfreetext'){
			$database->setQuery( "SELECT loc_freetext_id AS value FROM #__easysdi_metadata_classes_locfreetext  WHERE classes_id = $row->id ");
			$selLocfreetextList   = $database->loadObjectList() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}				
		}

		
		
		$listlist = array();
		$listist[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_list ORDER BY name" );
		$listlist = array_merge( $listlist, $database->loadObjectList() );

		
		$selListList = array();
		if($row->type == 'list'){
			$database->setQuery( "SELECT list_id AS value FROM #__easysdi_metadata_classes_list  WHERE classes_id = $row->id ");
			$selListList   = $database->loadObjectList() ;		
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		}
?>
<script>
function selectTypeParameters(){

var typevalue = document.getElementById('type').value;
document.getElementById('freetext').disabled=true;
document.getElementById('locfreetext').disabled=true;
document.getElementById('class').disabled=true;
document.getElementById('list').disabled=true;
document.getElementById(typevalue).disabled=false;
}
var oldLoad = window.onload;
window.onload=function(){
		selectTypeParameters();		
		if (oldLoad) oldLoad();
}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_DESCRIPTION"); ?></td>
		<td><input size="50" type="text" name ="description" value="<?php echo $row->description?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_IS_FINAL"); ?></td>
		<td><select name="is_final" > <option value="1" <?php if($row->is_final == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_final == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_IS_GLOBAL"); ?></td>
		<td><select name="is_global" > <option value="1" <?php if($row->is_global == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_global == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
					
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_ISO_KEY"); ?></td>
		<td><input size="50" type="text" name ="iso_key" value="<?php echo $row->iso_key?>"> </td>		 
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_TYPE"); ?></td>
		<td><select name="type" id="type" onChange="selectTypeParameters()"> 
				<option value="freetext" <?php if($row->type == "freetext") echo "selected"; ?>><?php echo JText::_("EASYSDI_FREETEXT"); ?></option> 
 				<option value="locfreetext" <?php if($row->type == "locfreetext") echo "selected"; ?>><?php echo JText::_("EASYSDI_LOCFREETEXT"); ?></option>
 				<option value="date" <?php if($row->type == "date") echo "selected"; ?>><?php echo JText::_("EASYSDI_DATE"); ?></option>
 				<option value="class" <?php if($row->type == "class") echo "selected"; ?>><?php echo JText::_("EASYSDI_CLASS"); ?></option>
 				<option value="list" <?php if($row->type == "list") echo "selected"; ?>><?php echo JText::_("EASYSDI_LIST"); ?></option>
		</select></td> 				
	</tr>	
	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_LIST_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$listlist, 'list[]', 'size="1" class="inputbox"', 'value', 'text', $selListList ); ?></td>
	 </tr>
	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_FREETEXT_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$freetextlist, 'freetext[]', 'size="1" class="inputbox"', 'value', 'text', $selFreetextList ); ?></td>
	 </tr>
	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_LOCFREETEXT_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$locfreetextlist, 'locfreetext[]', 'size="10" multiple class="inputbox"', 'value', 'text', $selLocfreetextList ); ?></td>
	 </tr>

	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_CLASSES_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$classeslist, 'class[]', 'size="10" multiple class="inputbox"', 'value', 'text', $selClassesList ); ?></td>
	 </tr>
	 
	 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
			
	
	</table>
</form>
	<?php 	
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	

	function editFreetext($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_DESCRIPTION"); ?></td>
		<td><input size="50" type="text" name ="description" value="<?php echo $row->description?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_GLOBAL"); ?></td>
		<td><select name="is_global" > <option value="1" <?php if($row->is_global == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_global == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_CONSTANT"); ?></td>
		<td><select name="is_constant" > <option value="1" <?php if($row->is_constant == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_constant == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_ID"); ?></td>
		<td><select name="is_id" > <option value="1" <?php if($row->is_id == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_id == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_DATE"); ?></td>
		<td><select name="is_date" > <option value="1" <?php if($row->is_date == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_date == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_CONSTANT"); ?></td>
		<td><select name="is_constant" > <option value="1" <?php if($row->is_constant == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_constant == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
					
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_DEFAULT_VALUE"); ?></td>
		<td><input size="50" type="text" name ="default_value" value="<?php echo $row->default_value?>"> </td>		 
	</tr>
	
	 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
			
	
	</table>
</form>
	<?php 	
		
	}
		
	
	
	
	function editNumerics($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_NUMERICS_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_NUMERICS_DESCRIPTION"); ?></td>
		<td><input size="50" type="text" name ="description" value="<?php echo $row->description?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_NUMERICS_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
					
	
	 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
			
	
	</table>
</form>
	<?php 	
		
	}
	
	function editListContent($rowMDList,$id, $option,$list_id ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_CODE_KEY"); ?></td>
		<td><input size="50" type="text" name ="code_key" value="<?php echo $rowMDList->code_key?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_ISO_CODE"); ?></td>
		<td><input size="50" type="text" name ="key" value="<?php echo $rowMDList->key?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_ISO_VALUE"); ?></td>
		<td><input size="50" type="text" name ="value" value="<?php echo $rowMDList->value?>"> </td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	
	 <input type="hidden" name="list_id" value="<?php echo $list_id; ?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
			
	
	</table>
</form>
	<?php 	
		
	}
	
	
	function editList($rowMDList,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_ID"); ?></td>
		<td><?php echo $rowMDList->id?> </td>							
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $rowMDList->name?>"> </td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_MULTIPLE"); ?></td>
		<td><select name="multiple" > <option value="1" <?php if($rowMDList->multiple == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($rowMDList->multiple == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td>		 
	</tr>

	 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $rowMDList->id?>" />
	<input type="hidden" name="task" value="" />		
			
	
	</table>
</form>
	<?php 	
		
	}
	function listDate($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_DATE"));
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataDate\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_DATE_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_DATE_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_DATE_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_DATE_NAME"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_DATE_DEFAULT_VALUE"); ?></th>															
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
				<td><?php echo $row->id; ?></td>
				<td><?php echo $row->partner_id; ?></td>
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<td><?php echo $row->name; ?></td>
				<td><?php echo $row->default_value; ?></td>
				
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
	  	<input type="hidden" name="task" value="listMetadataDate" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	

	function listFreetext($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_FREETEXT"));
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataFreetext\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_FREETEXT_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_FREETEXT_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_FREETEXT_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_FREETEXT_NAME"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_FREETEXT_DEFAULT_VALUE"); ?></th>																			
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<td><?php echo $row->name; ?></td>
				<td><?php echo $row->default_value; ?></td>				
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
	  	<input type="hidden" name="task" value="listMetadataFreetext" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	

	function listNumerics($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_NUMERICS"));
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataFreetext\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_NUMERICS_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_NUMERICS_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_NUMERICS_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_NUMERICS_NAME"); ?></th>																											
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<td><?php echo $row->name; ?></td>
							
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
	  	<input type="hidden" name="task" value="listMetadataFreetext" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	



	function listListContent($use_pagination, $rows, $pageNav,$option,$list_id){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_LIST"));
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataList\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_CODE_KEY"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_VALUE"); ?></th>															
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<td><?php echo $row->code_key; ?></td>
				<td><?php echo $row->value; ?></td>
				
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
	  	<input type="hidden" name="task" value="listMetadataList" />
	  	<input type="hidden" name="list_id" value="<?php echo $list_id; ?>" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	


	function listList($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_LIST"));
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataList\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_NAME"); ?></th>															
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<td><?php echo $row->name; ?></td>
				
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
	  	<input type="hidden" name="task" value="listMetadataList" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	
}
?>