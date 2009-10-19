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
/*foreach($_POST as $key => $val) 
echo '$_POST["'.$key.'"]='.$val.'<br />';*/
defined('_JEXEC') or die('Restricted access');
		
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');

JHTML::script('ext-base.js', 'administrator/components/com_easysdi_shop/ext/adapter/ext/');
JHTML::script('ext-all.js', 'administrator/components/com_easysdi_shop/ext/');
JHTML::script('dynamic.js', 'administrator/components/com_easysdi_shop/js/');
JHTML::script('ExtendedField.js', 'administrator/components/com_easysdi_shop/js/');
JHTML::script('ExtendedFieldSet.js', 'administrator/components/com_easysdi_shop/js/');
JHTML::script('ExtendedFormPanel.js', 'administrator/components/com_easysdi_shop/js/');
JHTML::script('ExtendedHidden.js', 'administrator/components/com_easysdi_shop/js/');
JHTML::script('MultiSelect.js', 'administrator/components/com_easysdi_shop/js/');

class HTML_metadata {
	var $javascript = "";
	
	function editMetadata($product_id, $root, $metadata_id, $xpathResults, $option)
	{
		$uri =& JUri::getInstance();
		$database =& JFactory::getDBO(); 
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base() . 'components/com_easysdi_shop/ext/resources/css/ext-all.css');
		$document->addStyleSheet($uri->base() . 'components/com_easysdi_shop/templates/css/form_layout_backend.css');
		$document->addStyleSheet($uri->base() . 'components/com_easysdi_shop/templates/css/MultiSelect.css');
		
		$url = 'index.php?option='.$option.'&task=saveMetadata';
		
		$user =& JFactory::getUser();
		$user_id = $user->get('id');
		
		$this->javascript = "";
		/*
		$database->setQuery( "SELECT a.partner_id as partner_id FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id and b.id=".$user_id." ORDER BY b.name" );
		$partner_id = $database->loadResult();
		*/
		
		$database->setQuery( "SELECT a.root_id FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id and b.id=".$user_id." ORDER BY b.name" );
		$partner_id = $database->loadResult();
		
		if ($partner_id == null)
			$partner_id = $user_id;
		
		?>
		<!-- Pour permettre le retour à la liste des produits depuis la toolbar Joomla -->
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
		</form>
		<?php
		$this->javascript .="
				var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
				Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
				
				// Créer le formulaire qui va contenir la structure
				var form = new Ext.ux.ExtendedFormPanel({
						id:'metadataForm',
						url: 'index.php',
						labelAlign: 'left',
				        labelWidth: 200,
				        border: false,
				        collapsed:false,
				        renderTo: document.getElementById('formContainer'),
				        buttons: [{
				            text: 'Envoyer',
				            handler: function(){
					        	form.getForm().submit({
								    	scope: this,
										method	: 'POST',
										success: function(form, action) 
										{
											console.log('SUCCESS !!!');
											console.log(form);
											console.log(action);
										},
										failure: function(form, action) 
										{
											console.log('FAIL !!!');
											console.log(action.result.errors.xml);
										},
										url:'".$url."'
									});
					        	}
				        }]
				    });
					
				var fieldset".$root[0]->id."= new Ext.form.FieldSet({id:'//".$root[0]->iso_key."', cls: 'easysdi_shop_backend_form', title:'".$root[0]->name."', xtype: 'fieldset', tabtip:'".$root[0]->label."'});
				form.add(fieldset".$root[0]->id.");";
		
			 
				$queryPath="/";
				// Bouclage pour construire la structure
				$node = $xpathResults->query($queryPath."/".$root[0]->iso_key);
				$nodeCount = $node->length;
				HTML_metadata::buildTree($database, $root[0]->id, $root[0]->id, "//".$root[0]->iso_key, $xpathResults, $node->item(0), $queryPath, $root[0]->iso_key, $partner_id, $option);
			
			$this->javascript .="
				form.add(createHidden('option', 'option', '".$option."'));
				form.add(createHidden('task', 'task', 'saveMetadata'));
				form.add(createHidden('metadata_id', 'metadata_id', '".$metadata_id."'));
				form.add(createHidden('product_id', 'product_id', '".$product_id."'));
				
	    		// Affichage du formulaire
	    		form.doLayout();";
			
			print_r("<script type='text/javascript'>".$this->javascript."</script>");	
	}
	
	function buildTree($database, $parent, $parentFieldset, $parentName, $xpathResults, $scope, $queryPath, $currentIsocode, $partner_id, $option)
	{
		//echo "<hr>SCOPE: ".$scope->nodeName."<br>";
		$classScope = $scope;
		$attributScope = $scope;
		$rowChilds = array();
		
		// Stockage du path pour atteindre ce noeud du XML
		$queryPath = $queryPath."/".$currentIsocode;
		//echo "QueryPath: ".$queryPath."<br>";
		
		// Traitement des enfants de type freetext 
		$rowAttributeChilds = array();
		//$query = "SELECT rel.id as rel_id, rel.name as rel_name, rel.isocode as rel_isocode, rel.upperbound as rel_upperbound, rel.lowerbound as rel_lowerbound, rel.attribute_id as attribute_id, rel.rendertype_id as rendertype_id, a.* FROM #__sdi_attributerelation rel, #__sdi_attribute as a WHERE rel.attribute_id=a.id AND rel.class_id=".$parent;
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type = 'freetext' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$partner_id.") ORDER BY c.ordering";
		$database->setQuery( $query );
		$rowAttributeChilds = array_merge( $rowAttributeChilds, $database->loadObjectList() );
		//echo "rowAttributeChilds: ".count($rowAttributeChilds)."<br>";
		
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
			if ($type->is_system)
			{
				if ($type->is_datetime)
				{
					$path = $path."/gco:DateTime";
					$name = $name."/gco:DateTime";
				}
				else
				{
					$path = $path."/gco:CharacterString";
					$name = $name."/gco:CharacterString";
				}
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
			else
			{
				$path = $path."/gco:CharacterString";
				$name = $name."/gco:CharacterString";
			}
			// Valeur de l'attribut 
			//echo $path." -".$name."<br>";
			$node = $xpathResults->query($path, $attributScope);
			
			//echo "NodeLength: ".$node->length."<br>";
			
			for ($pos=0; $pos<$node->length; $pos++)
			{
				if ($node->length > 0)
					$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
				else
					$nodeValue = "";
					
				$currentName = $name."__".($pos+1);
					
				//echo $currentName." - ".$nodeValue."<br>";
				// Traitement de chaque attribut
				if ($type->default_value <> "" and $nodeValue == "")
					$nodeValue = html_Metadata::cleanText($type->default_value);

				if ($pos==0)
				{
					if ($type->is_system)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createTextField('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."', '".$child->length."', true));
							fieldset".$parent.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
							";
					}
					else if ($type->is_date)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createDateField('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."'));
							";
					}
					else if ($type->is_datetime)
					{
						/*$date = substr($nodeValue,0,strpos($nodeValue,"T"));
						$date = date_format(date_create($date), 'd.m.Y');*/
						$date = date('d.m.Y', strtotime($nodeValue));
						
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createDateTimeField('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$date."'));
							";
					}
					else if ($type->is_number)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createNumberField('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."','".$type->default_value."', true, 15));
							";
					}
					else if ($type->is_integer)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createNumberField('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."','".$type->default_value."', false, 0));
							";
					}
					else
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createTextArea('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."'));
							";
					}
					$this->javascript .="
						// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
						fieldset".$parent.".add(createHidden('".$currentName."_index', '".$currentName."_index','1'));
					";
				}
				else
				{
					$currentName = $name."__".($pos+1);
					$master = substr($currentName,0,strlen($currentName)-2)."1";
					
					$this->javascript .="
						var master = Ext.getCmp('".$master."');						
						var index = Ext.getCmp('".$master."_index');
						oldIndex = index.getValue();
						index.setValue(Number(oldIndex)+1);
						";
					
					if ($type->is_system)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createTextField('".$currentName."', '".$child->name."',true, false, master, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."', '".$child->length."', true));
							fieldset".$parent.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
							";
					}
					else if ($type->is_date)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createDateField('".$currentName."', '".$child->name."',true, true, master, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."'));
							";
					}
					else if ($type->is_datetime)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createDateTimeField('".$currentName."', '".$child->name."',true, true, master, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."'));
							";
					}
					else if ($type->is_number)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createNumberField('".$currentName."', '".$child->name."',true, true, master, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."','".$type->default_value."', true, 15));
							";
					}
					else if ($type->is_integer)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createNumberField('".$currentName."', '".$child->name."',true, true, master, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."','".$type->default_value."', false, 0));
							";
					}
					else
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createTextArea('".$currentName."', '".$child->name."',true, true, master, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."'));
							";
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
					$nodeValue = html_Metadata::cleanText($type->default_value);
					
					if ($type->is_system)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createTextField('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."', '".$child->length."', true));
							fieldset".$parent.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
							";
					}
					else if ($type->is_date)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createDateField('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."'));
							";
					}
					else if ($type->is_datetime)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createDateTimeField('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."'));
							";
					}
					else if ($type->is_number)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createNumberField('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."','".$type->default_value."', true, 15));
							";
					}
					else if ($type->is_integer)
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createNumberField('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."','".$type->default_value."', false, 0));
							";
					}
					else
					{
						$this->javascript .="
							// La relation entre la classe et l'attribut
							// L'attribut
							fieldset".$parent.".add(createTextArea('".$currentName."', '".$child->name."',true, false, null, '".$child->lowerbound."', '".$child->upperbound."', '".$nodeValue."'));
							";
					}
						$this->javascript .="
							// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
							fieldset".$parent.".add(createHidden('".$currentName."_index', '".$currentName."_index','1'));
						";
				}
		}
		
		// Traitement des enfants de type list 
		$rowListClass = array();
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$partner_id.") ORDER BY c.ordering";
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
		      $nodeValues[]=html_Metadata::cleanText($listNode->item(0)->getAttribute('codeListValue'));
		     else
		      $nodeValues[]=html_Metadata::cleanText($listNode->item(0)->nodeValue);
		    else
		     $nodeValues[]="";
	   }
	   
	    // Deux traitement pour deux types de listes
	   if ($content[0]->multiple)
	   {
	   	$this->javascript .="
			var valueList = ".HTML_metadata::array2extjs($dataValues)."
	     	var selectedValueList = ".HTML_metadata::array2json($nodeValues)."
	     	// La liste
	     	fieldset".$parentFieldset.".add(createMultiSelector('".$listName."', '".JText::_($content[0]->l_translation)."', true, '".$child->lowerbound."', '".$child->upperbound."', valueList, selectedValueList));
	    	// L'index pour les potentiels clones de la liste 
	     	fieldset".$parentFieldset.".add(createHidden('".$listName."_index', '".$listName."_index', '1'));
	     	";
	   }
	   else
	   {
	    $this->javascript .="
		var valueList = ".HTML_metadata::array2extjs($dataValues).";
	     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
	     // La liste
	     fieldset".$parentFieldset.".add(createComboBox('".$listName."', '".JText::_($content[0]->l_translation)."', true, '".$child->lowerbound."', '".$child->upperbound."', valueList, selectedValueList));
	     // L'index pour les potentiels clones de la liste 
	     fieldset".$parentFieldset.".add(createHidden('".$listName."_index', '".$listName."_index', '1'));
	    ";
	   }
	  }	
			
		// Traitement des enfants de type local freetext 
		$rowLocText = array();
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type = 'locfreetext' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$partner_id.") ORDER BY c.ordering";
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
	$this->javascript .="
					var fieldset".$child->classes_to_id." = createFieldSet('".$LocName."', '".$child->name."', true, false, true, true, true, null, ".$child->lowerbound.", 5); 
						fieldset".$parent.".add(fieldset".$child->classes_to_id.");	
						// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
						fieldset".$parent.".add(createHidden('".$LocName."_index', '".$LocName."_index', '1'));
					";
					
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
							$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
						else
							$nodeValue = "";
						
						$this->javascript .="
							fieldset".$child->classes_to_id.".add(createTextArea('".$LocLangName."', '".JText::_($lang->translation)."', true, false, null, '1', '1', '".$nodeValue."'));
							// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
							fieldset".$child->classes_to_id.".add(createHidden('".$LocLangName."_index', '".$LocLangName."_index', '1'));
						";
					}
				}
				else
				{
					$master = $parentName."/".$child->iso_key."__1";
					$this->javascript .="
						var master = Ext.getCmp('".$master."');
						var index = Ext.getCmp('".$master."_index');
						oldIndex = index.getValue();
						index.setValue(Number(oldIndex)+1);
						var fieldset".$child->classes_to_id." = createFieldSet('".$LocName."', '".$child->name."', true, true, true, true, true, master, ".$child->lowerbound.", 5); 
						fieldset".$parent.".add(fieldset".$child->classes_to_id.");	
						master.manageIcons(master);
					";
					
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
							$nodeValue = html_Metadata::cleanText($node->item($pos-1)->nodeValue);
						else
							$nodeValue = "";
						
						$this->javascript .="
							fieldset".$child->classes_to_id.".add(createTextArea('".$LocLangName."', '".JText::_($lang->translation)."', true, false, null, '1', '1', '".$nodeValue."'));
							// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
							fieldset".$child->classes_to_id.".add(createHidden('".$LocLangName."_index', '".$LocLangName."_index', '1'));
						";
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
					
				$this->javascript .="
					var master = Ext.getCmp('".$master."');
					var index = Ext.getCmp('".$master."_index');
					oldIndex = index.getValue();
					index.setValue(Number(oldIndex)+1);
					var fieldset".$child->classes_to_id." = createFieldSet('".$LocName."', '".$child->name."', true, true, true, true, true, master, ".$child->lowerbound.", 5); 
					fieldset".$parent.".add(fieldset".$child->classes_to_id.");	
					
					master.manageIcons(master);
				";
				
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
						$nodeValue = html_Metadata::cleanText($node->item(0)->nodeValue);
					else
						$nodeValue = "";
					
					$this->javascript .="
						fieldset".$child->classes_to_id.".add(createTextArea('".$LocLangName."', '".JText::_($lang->translation)."', true, false, null, '1', '1', '".$nodeValue."'));
						// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
						fieldset".$child->classes_to_id.".add(createHidden('".$LocLangName."_index', '".$LocLangName."_index', '1'));
					";
				}
			}
		}
		
		// Récupération des classes enfants du noeud
		$rowClassChilds = array();
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='class' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$partner_id.") ORDER BY c.ordering";
		$database->setQuery( $query );
		$rowClassChilds = array_merge( $rowClassChilds, $database->loadObjectList() );
		
		foreach($rowClassChilds as $child)
		{
			// Compte du nombre d'occurence de ce noeud (Multiplicité)
			$node = $xpathResults->query($child->iso_key, $scope);
			$nodeCount = $node->length;
			
			// Traitement de la multiplicité
			// Récupération du path du bloc de champs qui va être créé pour construire le nom
			$name = $parentName."/".$child->iso_key;
			
			//echo $name." (".$nodeCount." dans ".$classScope->nodeName.") -".$child->is_relation."<br>";
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
				
				//echo $name." - ".$child->classes_to_id." - ".$classScope->nodeName."<br>";
				// Parcours récursif des classes
				$this->javascript .="
					var fieldset".$child->classes_to_id." = createFieldSet('".$name."', '', false, false, false, false, true, null, ".$child->lowerbound.", ".$child->upperbound."); 
					fieldset".$parent.".add(fieldset".$child->classes_to_id.");	
					// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
					fieldset".$parentFieldset.".add(createHidden('".$name."_index', '".$name."_index', '1'));
				";
				
				// Récupération des codes ISO et appel récursif de la fonction
				$nextIsocode = $child->iso_key;
				HTML_metadata::buildTree($database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $partner_id, $option);
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
						$this->javascript .="
							// Créer un nouveau fieldset
							var fieldset".$child->classes_to_id." = createFieldSet('".$name."', '".$child->name."', true, false, true, true, true, null, ".$child->lowerbound.", ".$child->upperbound."); 
							fieldset".$parent.".add(fieldset".$child->classes_to_id.");	
							// Création du champ caché (qui conservera l'index) lié au bloc de champs multiple
							fieldset".$parentFieldset.".add(createHidden('".$name."_index', '".$name."_index', '1'));
						";
						
						// S'il y a un xlink:title défini, alors afficher une balise pour le saisir
						if ($child->has_xlinkTitle)
						{
							if ($nodeCount > 0)
								$xlinkTitleValue = html_Metadata::cleanText($node->item($pos)->getAttribute('xlink:title'));
							else
								$xlinkTitleValue = "";
								
							$this->javascript .="
							// La saisie de la balise xlink:title
							fieldset".$child->classes_to_id.".add(createTextArea('".$name."_xlinktitle', 'Titre de l\'extension',true, false, null, '1', '1', '".$xlinkTitleValue."'));
							";
						}
						
						
						// Récupération des codes ISO et appel récursif de la fonction
						$nextIsocode = $child->iso_key;
						
						HTML_metadata::buildTree($database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $partner_id, $option);
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
						$this->javascript .="
							var master = Ext.getCmp('".$master."');							
							var index = Ext.getCmp('".$master."_index');
							oldIndex = index.getValue();
							index.setValue(Number(oldIndex)+1);
							// Créer un nouveau fieldset
							var fieldset".$child->classes_to_id." = createFieldSet('".$name."', '".$child->name."', true, true, true, true, true, master, ".$child->lowerbound.", ".$child->upperbound."); 
							fieldset".$parent.".add(fieldset".$child->classes_to_id.");	
							master.manageIcons(master);
						";
						
						// S'il y a un xlink:title défini, alors afficher une balise pour le saisir
						if ($child->has_xlinkTitle)
						{
							if ($nodeCount > 0)
								$xlinkTitleValue = html_Metadata::cleanText($node->item($pos-1)->getAttribute('xlink:title'));
							else
								$xlinkTitleValue = "";
								
							$this->javascript .="
							// La saisie de la balise xlink:title
							fieldset".$child->classes_to_id.".add(createTextArea('".$name."_xlinktitle', 'Titre de l\'extension',true, false, null, '1', '1', '".$xlinkTitleValue."'));
							";
						}
						
						
						$nextIsocode = $child->iso_key;
													
						// Récupération des codes ISO et appel récursif de la fonction
						HTML_metadata::buildTree($database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $partner_id, $option);
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
					$this->javascript .="
						var master = Ext.getCmp('".$master."');							
						var index = Ext.getCmp('".$master."_index');
						oldIndex = index.getValue();
						index.setValue(Number(oldIndex)+1);
						// Créer un nouveau fieldset
						var fieldset".$child->classes_to_id." = createFieldSet('".$name."', '".$child->name."', true, true, true, true, true, master, ".$child->lowerbound.", ".$child->upperbound."); 
						fieldset".$parent.".add(fieldset".$child->classes_to_id.");	
						master.manageIcons(master);
					";			
					
					// S'il y a un xlink:title défini, alors afficher une balise pour le saisir
					if ($child->has_xlinkTitle)
					{
						$xlinkTitleValue = "";
						
						$this->javascript .="
						// La saisie de la balise xlink:title
						fieldset".$child->classes_to_id.".add(createTextArea('".$name."_xlinktitle', 'Titre de l\'extension',true, false, null, '1', '1', '".$xlinkTitleValue."'));
						";
					}
					
					
					$nextIsocode = $child->iso_key;
												
					// Récupération des codes ISO et appel récursif de la fonction
					HTML_metadata::buildTree($database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $partner_id, $option);
				}
			}
		}
	}
	
function listMetadataTabs($use_pagination, $rows, $pageNav,$option, $filter_order_Dir, $filter_order, $search){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_TABS"));
		
		$partners = array();
		
		$ordering = ($filter_order == 'ordering');
		?>
	<form action="index.php" method="post" name="adminForm">
		<table>
			<tr>
				<td align="left" width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="search" id="search" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataStandardClasses\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title' width="10px"><?php echo JText::_("EASYSDI_METADATA_TABS_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_TABS_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderMetadataTabs' ); ?></th>			
				<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_TABS_TEXT"), 'text', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_PARTNER_NAME"), 'partner_name', @$filter_order_Dir, @$filter_order); ?></th>														
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
				<td align="center" width="10px"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>												
				<td width="30px" align="center"><?php echo $row->id; ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataTabs', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataTabs', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataTabs', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataTabs', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataTabs', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataTabs', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataTabs', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataTabs', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="order[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>	
				<?php $link =  "index.php?option=$option&amp;task=editMetadataTab&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->text; ?></a></td>
				<td><?php echo $row->partner_name; ?></td>
				<!-- <td><?php echo $row->partner_name; ?></td>  -->																											
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
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
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

	</table> 
	<input type="hidden" name="order" value="0" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
			
</form>
	<?php 	
		
	}
	
function listStandardClasses($use_pagination, $rows, $pageNav,$option,$type, $filter_order, $filter_order_Dir, $search){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_STANDARD_CLASSES"));
		
		$database->setQuery( "SELECT id AS value, name AS text FROM #__easysdi_metadata_standard WHERE is_deleted =0 " );
						
 
			$types =  $database->loadObjectList() ;													
	
		$partners = array();
		
		$ordering = ($filter_order == 'ordering');
		?>
	<form action="index.php" method="post" name="adminForm">
		<table>
			<tr>
				<td align="left" width="100%">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="searchStd" id="searchStd" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('searchStd').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataStandardClasses\');"',$use_pagination); ?></td>
											
				<td class="user"><b><?php echo JText::_("EASYSDI_TITLE_STANDARD"); ?></b><?php echo JHTML::_("select.genericlist", $types, 'type', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'listMetadataStandardClasses\');"', 'value', 'text', $type ); ?></td>
			</tr>
		
	
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title' width="10px"><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_STANDARD_CLASSES_ID"), 'id', @$filter_order_Dir, @$filter_order ); ?>		
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order ); ?>		
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderMetadataStandardClasses' ); ?></th>		
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_STANDARD_CLASSES_STANTARD_NAME"), 'standard_name', @$filter_order_Dir, @$filter_order ); ?>		
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_STANDARD_CLASSES_CLASS_NAME"), 'class_name', @$filter_order_Dir, @$filter_order ); ?>		
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_STANDARD_CLASSES_POSITION"), 'position', @$filter_order_Dir, @$filter_order ); ?>
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
				<td width="10px" align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>												
				<td width="30px"><?php echo $row->id; ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataStandardClasses', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataStandardClasses', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataStandardClasses', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataStandardClasses', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataStandardClasses', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataStandardClasses', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataStandardClasses', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataStandardClasses', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="order[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>	
				<?php $link =  "index.php?option=$option&amp;task=editMetadataStandardClasses&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->standard_name; ?></a></td>
				<td><a href="<?php echo $link;?>"><?php echo $row->class_name; ?></a></td>																												
				<td><?php echo $row->position; ?></td>							
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
	  	<input type="hidden" name="task" value="listMetadataStandardClasses" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
		
}	
	
function editStandardClasses($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
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
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_standard  WHERE is_deleted =0  ORDER BY name" );
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
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
		
	<tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_STANDARD"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$standardlist, 'standard_id', 'size="1" class="inputbox"', 'value', 'text', $selStandardList ); ?></td>
	 </tr>
	 								
	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$classeslist, 'class_id', 'size="1" class="inputbox"', 'value', 'text', $selClassesList ); ?></td>
	 </tr>
	 <tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>
	 <tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_TAB_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$tabslist, 'tab_id', 'size="1" class="inputbox"', 'value', 'text', $row->tab_id ); ?></td>							
	</tr> 		
			
	
	</table>
	 
	<input type="hidden" name="order" value="0" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />
</form>
	<?php 	
		
	}
	
		
	
function listStandard($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_STANDARD"));
		
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
				<?php $link =  "index.php?option=$option&amp;task=editMetadataStandard&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>																												
												
				
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
		
		JToolBarHelper::title(JText::_("EASYSDI_EDIT_METADATA_STANDARD"));
		
		$database =& JFactory::getDBO(); 
/*
		$standards = array();
		$standards[] = JHTML::_('select.option','0', JText::_("EASYSDI_STANDARDS_LIST") );
		$database->setQuery( "SELECT id AS value, name AS text FROM #__easysdi_metadata_standard  where is_deleted =0 AND is_global = 1 ORDER BY name" );
		$standards = array_merge( $standards, $database->loadObjectList() );
*/		
		$classes = array();
		$classes[] = JHTML::_('select.option','0', JText::_("EASYSDI_CLASSES_LIST") );
		$database->setQuery( "SELECT id AS value, name AS text FROM #__easysdi_metadata_classes  where is_final=true ORDER BY name" );
		$classes = array_merge( $classes, $database->loadObjectList() );
		
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id); ?></td>							
	</tr>
	<!-- 				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_INHERITED"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$standards, 'inherited', 'size="1" class="inputbox"', 'value', 'text',  $row->inherited ); ?></td>		 
	</tr>
	 -->
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_IS_GLOBAL"); ?></td>
		<td><select name="is_global" > <option value="1" <?php if($row->is_global == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_global == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td>		 
	</tr>		
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$classes, 'classes_id', 'size="1" class="inputbox"', 'value', 'text', $row->classes_id); ?></td>							
	</tr>		
	
	</table>
	 
	 
	 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />
</form>
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
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></th>	
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
				<?php $link =  "index.php?option=$option&amp;task=editMetadataExt&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>																												
				<td><?php echo $row->translation; ?></td>
												
				
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
		<td><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></td>
		<td><input size="50" type="text" name ="translation" value="<?php echo $row->translation?>"> </td>							
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>						
			
	
	</table>
	 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />
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
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></th>
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
				<?php $link =  "index.php?option=$option&amp;task=editMetadataLocfreetext&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>																												
				<td><?php echo $row->translation; ?></td>
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
		<td><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></td>
		<td><input size="50" type="text" name ="translation" value="<?php echo $row->translation?>"> </td>							
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
	 
	</table>
	
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
	
</form>
	<?php 	
		
	}
	
	
	
function listClass($use_pagination, $rows, $pageNav,$option, $filter_order, $filter_order_Dir, $search){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_CLASS"));
		
		$partners = array();
		
		$ordering = ($filter_order == 'ordering');
		?>
	<form action="index.php" method="post" name="adminForm">
	
		<table>
			<tr>
				
				<td align="left" width="100%">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="search" id="search" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataClass\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th width="10px" class='title'><?php echo JText::_("EASYSDI_METADATA_CLASS_SHARP"); ?></th>
				<th width="10px" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th width="30px" class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_CLASS_ID"), 'id', @$filter_order_Dir, @$filter_order ); ?></th>		
				<th width="100px" class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order ); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderMetadataClass' ); ?></th>		
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_CLASS_PARTNER_NAME"), 'user_name', @$filter_order_Dir, @$filter_order ); ?></th>		
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_CLASS_NAME"), 'class_name', @$filter_order_Dir, @$filter_order ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_CLASS_ISOKEY"), 'iso_key', @$filter_order_Dir, @$filter_order ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_CLASS_TYPE"), 'type', @$filter_order_Dir, @$filter_order ); ?></th>																				
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
				<td width="10px" align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>												
				<td width="30px"><?php echo $row->id; ?></td>
	            <?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataClass', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataClass', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataClass', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataClass', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataClass', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataClass', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataClass', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataClass', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="order[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
				<td><?php echo $row->user_name; ?></td>								
				<?php $link =  "index.php?option=$option&amp;task=editMetadataClass&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->class_name; ?></a></td>												
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
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
		
}	
	
	function editClass($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_EMPTY_LIST") );
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
		
		$listext = array();
		$listext[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_EXT") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_ext ORDER BY name" );
		$listext = array_merge( $listext, $database->loadObjectList() );

		
		$selExtList = array();
		if($row->type == 'ext'){
			$database->setQuery( "SELECT ext_id AS value FROM #__easysdi_metadata_classes_ext  WHERE classes_id = $row->id ");
			$selExtList   = $database->loadObjectList() ;		
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
document.getElementById('ext').disabled=true;
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
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_XLINKTITLE"); ?></td>
		<td><select name="has_xlinkTitle" > <option value="1" <?php if($row->has_xlinkTitle == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->has_xlinkTitle == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td>						
	</tr>	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_TRANSLATION"); ?></td>
		<td><input size="100" type="text" name ="translation" value="<?php echo $row->translation?>"> </td>							
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
 				<option value="ext" <?php if($row->type == "ext") echo "selected"; ?>><?php echo JText::_("EASYSDI_EXT"); ?></option>
 				<option value="class" <?php if($row->type == "class") echo "selected"; ?>><?php echo JText::_("EASYSDI_CLASS"); ?></option>
 				<option value="list" <?php if($row->type == "list") echo "selected"; ?>><?php echo JText::_("EASYSDI_LIST"); ?></option>
		</select></td> 				
	</tr>	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_IS_RELATION"); ?></td>
		<td><select name="is_relation" > <option value="1" <?php if($row->is_relation == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_relation == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_LOWERBOUND"); ?></td>
		<td><input size="3" type="text" name ="lowerbound" value="<?php echo $row->lowerbound?>"> </td>							
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_UPPERBOUND"); ?></td>
		<td><input size="3" type="text" name ="upperbound" value="<?php echo $row->upperbound?>"> </td>							
	</tr>
	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_LIST_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$listlist, 'list[]', 'size="1" class="inputbox"', 'value', 'text', $selListList ); ?></td>
	 </tr>
	  <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_EXT_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$listext, 'ext[]', 'size="1" class="inputbox"', 'value', 'text', $selExtList ); ?></td>
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
	 
	</table> 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />
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
		<td><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></td>
		<td><input size="50" type="text" name ="translation" value="<?php echo $row->translation?>"> </td>							
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
	<!-- 
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
	 -->
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_SYSTEM"); ?></td>
		<td><select name="is_system" > <option value="1" <?php if($row->is_system == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_system == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_DATE"); ?></td>
		<td><select name="is_date" > <option value="1" <?php if($row->is_date == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_date == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_DATETIME"); ?></td>
		<td><select name="is_datetime" > <option value="1" <?php if($row->is_datetime == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_datetime == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_NUMBER"); ?></td>
		<td><select name="is_number" > <option value="1" <?php if($row->is_number == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_number == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_INTEGER"); ?></td>
		<td><select name="is_integer" > <option value="1" <?php if($row->is_integer == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_integer == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>
	<!-- 
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_CONSTANT"); ?></td>
		<td><select name="is_constant" > <option value="1" <?php if($row->is_constant == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_constant == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	-->			
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_DEFAULT_VALUE"); ?></td>
		<td><input size="50" type="text" name ="default_value" value="<?php echo $row->default_value?>"> </td>		 
	</tr>
	
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />
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
	<!-- 						
	<tr>
		<td><?php //echo JText::_("EASYSDI_METADATA_LIST_ISO_VALUE"); ?></td>
		<td><input size="50" type="text" name ="value" value="<?php //echo $rowMDList->value?>"> </td>							
	</tr>
	 -->			
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></td>
		<td><input size="50" type="text" name ="translation" value="<?php echo $rowMDList->translation?>"> </td>							
	</tr>	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	
	</table>
	 <input type="hidden" name="list_id" value="<?php echo $list_id; ?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $id;?>" />
	<input type="hidden" name="task" value="" />
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
		<td><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></td>
		<td><input size="50" type="text" name ="translation" value="<?php echo $rowMDList->translation?>"> </td>							
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
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_ISOKEY"); ?></td>
		<td><input size="50" type="text" name ="iso_key" value="<?php echo $rowMDList->iso_key?>"> </td>							
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_CODEVALUE"); ?></td>
		<td><select name="codeValue" > <option value="1" <?php if($rowMDList->codeValue == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($rowMDList->codeValue == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td>		 
	</tr>	
	</table> 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $rowMDList->id?>" />
	<input type="hidden" name="task" value="" />
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
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></th>				
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
				<?php $link =  "index.php?option=$option&amp;task=editMetadataFreetext&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>																												
				<td><?php echo $row->translation; ?></td>

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
				<!-- <th class='title'><?php //echo JText::_("EASYSDI_METADATA_LIST_VALUE"); ?></th> -->															
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></th>
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
				<?php $link =  "index.php?option=$option&amp;task=editMetadataListContent&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->code_key; ?></a></td>																												
												
				
				<!-- <td><?php //echo $row->value; ?></td> -->
				<td><?php echo $row->translation; ?></td>
				
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
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></th>
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
				<?php $link =  "index.php?option=$option&amp;task=editMetadataList&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>																												
				<td><?php echo $row->translation; ?></td>
				
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
	function cleanText($text)
	 {
	  $text = utf8_decode($text);
	  $text = str_replace("\n","\\n",$text);
	  $text = str_replace("'","\'",$text);
	  $text = utf8_encode($text);
	  return $text;
	 }
 
	 function array2json($arr) 
	 { 
	 	if(function_exists('json_encode')) 
	    	return json_encode($arr); //Lastest versions of PHP already has this functionality. 
	    
	    $parts = array(); 
	    $is_list = false; 
	
	    //Find out if the given array is a numerical array 
	    $keys = array_keys($arr); 
	    $max_length = count($arr)-1; 
	    if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1 
	        $is_list = true; 
	        for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position 
	            if($i != $keys[$i]) { //A key fails at position check. 
	                $is_list = false; //It is an associative array. 
	                break; 
	            } 
	        } 
	    } 
	
	    foreach($arr as $key=>$value) 
	    { 
	        if(is_array($value)) { //Custom handling for arrays 
	            if($is_list) 
	            	$parts[] = array2json($value); /* :RECURSION: */ 
	            else 
	            	$parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */ 
	        } 
	        else 
	        { 
	            $str = ''; 
	            if(!$is_list) 
	            	$str = '"' . $key . '":'; 
	
	            //Custom handling for multiple data types 
	            if(is_numeric($value)) 
	            	$str .= $value; //Numbers 
	            elseif($value === false) 
	            	$str .= 'false'; //The booleans 
	            elseif($value === true) 
	            	$str .= 'true'; 
	            else 
	            	$str .= '"' . addslashes($value) . '"'; //All other things 
	            // :TODO: Is there any more datatype we should be in the lookout for? (Object?) 
	
	            $parts[] = $str; 
	        } 
	    } 
	    $json = implode(',',$parts); 
	     
	    if($is_list) 
	    	return '[' . $json . ']';//Return numerical JSON 
		
	    $return = '[' . $json . ']';
		return $return;//Return associative JSON 
	} 
}


?>
