<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�??Arche 40b, CH-1870 Monthey, easysdi@depth.ch
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
 *
 * string genericlist (array $arr, string $name,
 * [string $attribs = null], [string $key = 'value'],
 * [string $text = 'text'], [mixed $selected = NULL],
 * [ $idtag = false], [ $translate = false])
 */

defined('_JEXEC') or die('Restricted access');


class HTML_classstereotype_builder {
	
	function getGeographicExtentClass( $database, $fieldsetname, $relationObject, $parentFieldsetName, $xpathResults, $path, $scope){
		
// 		echo "QueryPath = ".$XPath."<br>";
// 		echo "Relation name = ".$relationNode->rel_name."<br>";
// 		echo "Relation isocode = ". $relationObject->rel_isocode."<br>"; //gmd:extent
// 		echo "Class stereotype isocode = ". $relationObject->child_isocode."<br>"; //gmd:EX_Extent
// echo ($scope->nodeName);
//  		echo "<hr>";
		
		$nodes = $xpathResults->query($relationObject->rel_isocode."/".$relationObject->child_isocode."/sdi:extentType/gco:CharacterString", $scope);
		foreach ($nodes as $node){
			echo $node->nodeValue;
// 			$extents = $xpathResults->query($relationObject->child_isocode, $node);
// 			foreach ($extents as $extent){
// 				$category_nodes = $xpathResults->query("sdi:extentType", $extent);
// 				foreach ($category_nodes as $category_node){
// 					echo $category_node->nodeValue;
// 				}
				
// 			}
		}
		
		//Liste des catégories de périmètres
		$language =& JFactory::getLanguage();
		
		$query = "SELECT bc.alias as alias, t.label as label
					FROM #__sdi_boundarycategory bc
						INNER JOIN #__sdi_translation t ON bc.guid = t.element_guid
						INNER JOIN #__sdi_language l ON t.language_id=l.id
						INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id
					WHERE c.code='".$language->_lang."'";
		$database->setQuery( $query );
		$categorycontent = $database->loadObjectList();
		
		$dataValues = array();
		foreach ($categorycontent as $cont){
			$dataValues[$cont->alias] = html_Metadata::cleanText($cont->label);
		}
		
		//Multiplicité de la relation
		$rel_lowerbound = $relationObject->rel_lowerbound;
		$rel_upperbound = $relationObject->rel_upperbound;
		
		$comboboxName = $fieldsetname."-sdi_extentType__1";
		
		$this->javascript .="
		var valueList = ".HTML_metadata::array2extjs($dataValues, true).";
		var selectedValueList = '';
		var defaultValueList = '';
		
		
		".$parentFieldsetName.".add(createComboBox('".$comboboxName."', '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_CATEGORY_LABEL"))."', false, '1', '1', valueList, selectedValueList, defaultValueList, false, '".html_Metadata::cleanText(JText::_(""))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
		";
		
		//Ajouter un listener pour recharger la liste des périmètres quand la catégorie a été changée
			
		//Construire le ItemSelector avec la liste des périmètres correspondant à la catégorie sélectionnée
		$itemselectorName = $fieldsetname."-gmd_geographicElement__1";
		
		$query = "SELECT b.id as id, t.label as label
					FROM #__sdi_boundary b
						INNER JOIN #__sdi_translation t ON b.guid = t.element_guid
						INNER JOIN #__sdi_language l ON t.language_id=l.id
						INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id
					WHERE c.code='".$language->_lang."'";
		$database->setQuery( $query );
		$perimetercontent = $database->loadObjectList();
		
		$this->javascript .="
			 var sourceDS = new Ext.data.ArrayStore({
			        data: [
			        	";
		    foreach ($perimetercontent as $perimeter){
		    	$this->javascript .="[
		    	'".$perimeter->id."','".$perimeter->label."'
		    	],
		    	";
			};
		
			$this->javascript = substr($this->javascript, 0, strlen($this->javascript)-1);
			$this->javascript .="],
			        fields: ['value','text'],
			        sortInfo: {
			            field: 'value',
			            direction: 'ASC'
			        }
			    });
    
			var destinationDS = new   Ext.data.ArrayStore({
				data: [],
				fields: ['value','text'],
			        sortInfo: {
			            field: 'value',
			            direction: 'ASC'
			        }
			    });
				  
		 var ".$parentFieldsetName."_itemselector = new Ext.ux.form.ItemSelector({
	                    name: '".$itemselectorName."',
	                    id: '".$itemselectorName."',
			            fieldLabel: '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_PERIMETER_LABEL"))."',
				        imagePath: '/easysdi/administrator/components/com_easysdi_catalog/ext/ux/images/',
			            multiselects: [{
			                width: 250,
			                height: 200,
			                store: sourceDS,
			                displayField: 'text',
			                valueField: 'value'
			            },{
			                width: 250,
			                height: 200,
			                store: destinationDS,
			                displayField: 'text',
			                valueField: 'value'
			            }]
			        });

		    
		".$parentFieldsetName.".add(".$parentFieldsetName."_itemselector);
		";
		

	}
}
?>