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

class ADMIN_classstereotype_saver {
	
	function saveGeographicExtentClass($database, $relationObject, &$XMLDoc, $XMLNode, $fieldsetName){
		
// 		echo ("Name = ".$fieldsetName);
// 		echo("\n");
// 		$key = $fieldsetName."-sdi_extentType__1_hidden";
// 		echo ("Key = ".$key);
// 		echo("\n");
// 		echo ("POST[key] = ".$_POST[$key]);
// 		echo("\n");
// 		$key = $fieldsetName."-gmd_geographicElement__1";
// 		echo ("Key = ".$key);
// 		echo("\n");
// 		echo ("POST[key] = ".$_POST[$key]);
// 		echo("\n");
		
		//Le doc XML contient déjà le noeud de la relation et le noeud de la classe du stereotype
		
		//Boundary category
		$node = $XMLDoc->createElement("sdi:extentType");
		$XMLNode->appendChild($node);
		
		//BoundaryCategory alias
		$key = $fieldsetName."-sdi_extentType__1_hidden";
		$node1 = $XMLDoc->createElement("gco:CharacterString",$_POST[$key]);
		$node->appendChild($node1);
		
		//Description
		$node2 = $XMLDoc->createElement("gmd:description");
		$XMLNode->appendChild($node2);
		//Boundary id
		$key = $fieldsetName."-gmd_geographicElement__1";
		$boundary_id = $_POST[$key];
		$query = "SELECT b.id as id, t.label as label, c.code as codelang, t.title as title
					FROM #__sdi_boundary b
						INNER JOIN #__sdi_translation t ON b.guid = t.element_guid
						INNER JOIN #__sdi_language l ON t.language_id=l.id
						INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id
					WHERE b.id=".$boundary_id;
		$database->setQuery( $query );
		$boundaryLocal = $database->loadObjectList();
		$usefullVals=array();
		foreach($boundaryLocal as $boundary){
			$usefullVals[$boundary->codelang] = $boundary->label;
		}
		foreach($this->langList as $lang)
		{
			if ($lang->defaultlang == true) // La langue par défaut
			{
				$nodelang = $XMLDoc->createElement("gco:CharacterString", $usefullVals[$lang->code_easysdi]);
				$node2->appendChild($nodelang);
			}else // Les autres langues
			{
				$nodelang = $XMLDoc->createElement("gmd:PT_FreeText");
				$node2->appendChild($nodelang);
				
				
				$nodelanggroup = $XMLDoc->createElement("gmd:textGroup");
				$node2->appendChild($nodelanggroup);
				
				// Ajout de la valeur
				$nodelangvalue = $XMLDoc->createElement("gmd:LocalisedCharacterString", $usefullVals[$lang->code_easysdi]);
				$nodelanggroup->appendChild($nodelangvalue);
				// Indication de la langue concernée
				$nodelanggroup->setAttribute('locale', "#".$lang->code);
			}
			
		}
		
		//Geographic element BoundingBox
		$query ="SELECT * from #__sdi_boundary WHERE id =".$boundary_id;
		$database->setQuery( $query );
		$boundaryLocal = $database->loadObject();
		
		$nodeA = $XMLDoc->createElement("gmd:geographicElement");
		$XMLNode->appendChild($nodeA);
		
		$nodeB = $XMLDoc->createElement("gmd:EX_GeographicBoundingBox");
		$nodeA->appendChild($nodeB);
		
		$nodeC = $XMLDoc->createElement("gmd:extentTypeCode", 'true');
		$nodeB->appendChild($nodeC);
		
		$nodeD = $XMLDoc->createElement("gmd:northBoundLatitude");
		$nodeB->appendChild($nodeD);
		$nodeD_ = $XMLDoc->createElement("gco:Decimal", $boundaryLocal->northbound);
		$nodeD->appendChild($nodeD_);
		
		$nodeE = $XMLDoc->createElement("gmd:southBoundLatitude");
		$nodeB->appendChild($nodeE);
		$nodeE_ = $XMLDoc->createElement("gco:Decimal", $boundaryLocal->southbound);
		$nodeE->appendChild($nodeE_);
		
		$nodeF = $XMLDoc->createElement("gmd:eastBoundLatitude");
		$nodeB->appendChild($nodeF);
		$nodeF_ = $XMLDoc->createElement("gco:Decimal", $boundaryLocal->eastbound);
		$nodeF->appendChild($nodeF_);
		
		$nodeG = $XMLDoc->createElement("gmd:westBoundLatitude");
		$nodeB->appendChild($nodeG);
		$nodeG_ = $XMLDoc->createElement("gco:Decimal", $boundaryLocal->westbound);
		$nodeG->appendChild($nodeG_);
		
		//Geographic element Identifier
		$nodeI = $XMLDoc->createElement("gmd:geographicElement");
		$XMLNode->appendChild($nodeI);
		
		$nodeII = $XMLDoc->createElement("gmd:EX_GeographicDescription");
		$nodeI->appendChild($nodeII);
		
		$nodeIII = $XMLDoc->createElement("gmd:extentTypeCode", 'true');
		$nodeII->appendChild($nodeIII);
		
		$nodeIV = $XMLDoc->createElement("gmd:geographicIdentier");
		$nodeII->appendChild($nodeIV);
		
		$nodeV = $XMLDoc->createElement("gmd:code");
		$nodeIV->appendChild($nodeV);
		
		$usefullVals=array();
		foreach($boundaryLocal as $boundary){
			$usefullVals[$boundary->codelang] = $boundary->title;
		}
		foreach($this->langList as $lang)
		{
			if ($lang->defaultlang == true) // La langue par défaut
			{
				$nodelang = $XMLDoc->createElement("gco:CharacterString", $usefullVals[$lang->code_easysdi]);
				$nodeV->appendChild($nodelang);
			}else // Les autres langues
			{
				$nodelang = $XMLDoc->createElement("gmd:PT_FreeText");
				$nodeV->appendChild($nodelang);
				
				
				$nodelanggroup = $XMLDoc->createElement("gmd:textGroup");
				$nodeV->appendChild($nodelanggroup);
				
				// Ajout de la valeur
				$nodelangvalue = $XMLDoc->createElement("gmd:LocalisedCharacterString", $usefullVals[$lang->code_easysdi]);
				$nodelanggroup->appendChild($nodelangvalue);
				// Indication de la langue concernée
				$nodelanggroup->setAttribute('locale', "#".$lang->code);
			}
			
		}
	}
}
?>