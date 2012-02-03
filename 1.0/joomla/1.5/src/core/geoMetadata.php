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

class geoMetadata{
	var $metadata;
	var $xpath;
	
	
	function __construct( $md ){
		
		if ($md){
			if ($md instanceof  DOMDocument){
						 			
				$this->metadata  = $md;
			}else
			if ($md instanceof  DOMElement){
				 				 
				 $dom = new DOMDocument();
				 $xmlContent = $dom ->importNode($md,true);
				 $dom->appendChild($xmlContent);				 
				
				$this->metadata  = $dom;
			}			
			else{
				
				$dom = new DOMDocument();
					
							 				
				$xmlContent = $dom ->importNode(dom_import_simplexml($md),true);
				$dom->appendChild($xmlContent);							 
				$this->metadata = $dom;
				
			
			}
			
		 
		
					
		$this->xpath = new DomXPath($this->metadata);
		
		$this->xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
		$this->xpath->registerNamespace('gco','http://www.isotc211.org/2005/gco');
		$this->xpath->registerNamespace('ext','http://www.depth.ch/2008/ext');
		
		}		
		
	}
	function getMetadataDom(){
		
		return $this->metadata;
		
	}
	
	
function isXPathResultCount($xpath){
		if ($this->metadata){	
		$nodes = $this->xpath->query($xpath);
		if ($nodes === false) return 0;
		if ($nodes->length==0) return 0;
		$i=0;
		foreach ($nodes as $node){
			$i++;
		}
		return $i;
		}
		return 0;
		
	}
	
	function getXPathResult($xpath){
		if ($this->metadata){	
		$nodes = $this->xpath->query($xpath);
		if ($nodes === false) return "";
		
		if ($nodes->length==0) return "";
		$theNode = $nodes->item(0);
		
		
		 
		$myNode = $theNode->parentNode->removeChild($theNode);
		
		$this->metadata=$theNode->ownerDocument;
		
		$this->xpath = new DomXPath($theNode->ownerDocument);
		
		$this->xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
		$this->xpath->registerNamespace('gco','http://www.isotc211.org/2005/gco');
		$this->xpath->registerNamespace('ext','http://www.depth.ch/2008/ext');
		
		//$this->metadata->save("c:\\".$theNode ->nodeValue.".xml");
		
		return $theNode ->nodeValue;
		}
		return "";
		
	}
	function getExtValue($name){
		if ($this->metadata){	 
			$xpath="//gmd:metadataExtensionInfo/gmd:MD_MetadataExtensionInformation/gmd:extendedElementInformation/gmd:MD_ExtendedElementInformation/gmd:name/gco:CharacterString";
		$nodes = $this->xpath->query($xpath);
		
		$xpath2="//gmd:metadataExtensionInfo/gmd:MD_MetadataExtensionInformation/gmd:extendedElementInformation/gmd:MD_ExtendedElementInformation/gmd:domainValue/gco:CharacterString";
		$nodes2 = $this->xpath->query($xpath2);
		
		if ($nodes === false) return "";

		if ($nodes->length==0) return "";
		$i=0;
		foreach ($nodes as $node){
			if ($node->nodeValue == $name){

				$theNode = $nodes->item($i);
				$theNode2 = $nodes2->item($i);
				break;
			}
			$i++;
		}
		
		$myNode = $theNode->parentNode->removeChild($theNode);
		$myNode2 = $theNode2->parentNode->removeChild($theNode2);
				
		$this->metadata=$theNode->ownerDocument;		
		$this->xpath = new DomXPath($theNode->ownerDocument);
		
		$this->xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
		$this->xpath->registerNamespace('gco','http://www.isotc211.org/2005/gco');
		
		//$this->metadata->save("c:\\".$theNode ->nodeValue.".xml");
		
		return $theNode2 ->nodeValue;
		}
		return "";
		
	}
	function getThema(){
		if ($this->metadata){	 
		$nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:topicCategory/gmd:MD_TopicCategoryCode');
		return $nodes ->item(0)->nodeValue;
		}
		return "";
		
	}
	function getFileIdentifier(){					
		if ($this->metadata){	 
		$nodes = $this->xpath->query('//gmd:fileIdentifier/gco:CharacterString');
		return $nodes ->item(0)->nodeValue;
		}
		return "";
		
	}
	function getFees(){
		if ($this->metadata){
		$nodes = $this->xpath->query("//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributionOrderProcess/gmd:MD_StandardOrderProcess/gmd:fees/gmd:LocalisedCharacterString");
		return $nodes ->item(0)->nodeValue;
		}
		return "";
	}
	function getManagerOrganisationName($lang="fr"){			
				
		if ($this->metadata){
		$nodes = $this->xpath->query('//gmd:contact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:LocalisedCharacterString');
		return $nodes ->item(0)->nodeValue;
		}
		return "";
	}

	

	function getReferenceSystemInfo($lang="fr"){
		
		if ($this->metadata){
		$nodes = $this->xpath->query('//gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:referenceSystemIdentifier/gmd:RS_Identifier/gmd:code/gmd:LocalisedCharacterString');
		return $nodes ->item(0)->nodeValue;
	}return "";
	
		
	}
	
function getManagerName($lang="fr"){			
	if ($this->metadata){
		$nodes = $this->xpath->query('//gmd:contact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString');
		return $nodes ->item(0)->nodeValue;
	}return "";
	}

	
function getManagerAddress($lang="fr"){			
if ($this->metadata){	 
		$nodes = $this->xpath->query('//gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString');
		return $nodes ->item(0)->nodeValue;
		}return "";
	}
	
function getManagerCity(){
	if ($this->metadata){	 
	$nodes = $this->xpath->query("//gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString");
		return $nodes ->item(0)->nodeValue;
		}return "";
}

function getManagerPostalCode(){
	if ($this->metadata){	 
	$nodes = $this->xpath->query("//gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString");
		return $nodes ->item(0)->nodeValue;
		}return "";
}	
function getManagerCountry(){
	
	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString");
		return $nodes ->item(0)->nodeValue;
		}return "";
}		

function getManagerVoice(){
	
	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString");
		return $nodes ->item(0)->nodeValue;
		}return "";
}


function getManagerFax(){
	
	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString");
		return $nodes ->item(0)->nodeValue;
		}return "";
}


function getManagerEmail(){
	
	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString");
		return $nodes ->item(0)->nodeValue;
		}return "";
}		





function getDistributionOrganisationName ($lang="fr") {				
	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:LocalisedCharacterString");
	return $nodes ->item(0)->nodeValue;
	}return "";				
	}
	
	
function getDistributionName($lang="fr"){			

		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString');
		return $nodes ->item(0)->nodeValue;
		}return "";
	}

	
function getDistributionAddress($lang="fr"){			

		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString');
		return $nodes ->item(0)->nodeValue;
		}return "";
	}
	
function getDistributionCity(){
	
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString');
			return $nodes ->item(0)->nodeValue;
			}return "";
}

function getDistributionPostalCode(){
	
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString');
			return $nodes ->item(0)->nodeValue;
			}return "";
}	
function getDistributionCountry(){
	
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString');
			return $nodes ->item(0)->nodeValue;
			}return "";
}		

function getDistributionVoice(){
	
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString');
			return $nodes ->item(0)->nodeValue;
			}return "";
}


function getDistributionFax(){
	
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString');
			return $nodes ->item(0)->nodeValue;
			}return "";
}


function getDistributionEmail(){
	
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString');
			return $nodes ->item(0)->nodeValue;
			
			}return "";
}		




function getPocOrganisationName ($lang="fr") {				
	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:LocalisedCharacterString");
	return $nodes ->item(0)->nodeValue;
	}return "";				
	}
	
	
function getPocName($lang="fr"){			

		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString');
		return $nodes ->item(0)->nodeValue;
		}return "";
	}

	
function getPocAddress($lang="fr"){			

		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString');
			return $nodes ->item(0)->nodeValue;
			}return "";
	}
	
function getPocCity(){
	
				if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString');
		return $nodes ->item(0)->nodeValue;
		}return "";
}

function getPocPostalCode(){
	
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString');
				return $nodes ->item(0)->nodeValue;
				}return "";
}	
function getPocCountry(){
	
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString');
				return $nodes ->item(0)->nodeValue;
				}return "";
}		

function getPocVoice(){
	
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString');
				return $nodes ->item(0)->nodeValue;
				}return "";
}


function getPocFax(){
	
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString');
				return $nodes ->item(0)->nodeValue;
				}return "";
}


function getPocEmail(){
	
		if ($this->metadata){	 
				$nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString');
				return $nodes ->item(0)->nodeValue;
			}
				return "";
}		





	

	function getDataIdentificationTitle($lang="fr"){
		if ($this->metadata){	 												
				$nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString');
				return $nodes ->item(0)->nodeValue;
			}
				return "";					
	}

	function getDescription($lang="fr") {			
		
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString');
		return $nodes ->item(0)->nodeValue;
		}return "";		
								
	}

function getStatus($lang="fr") {			
		
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:status/gmd:MD_ProgressCode/@codeListValue');
		return $nodes ->item(0)->nodeValue;
		}return "";		
								
	}
	
	function getPurpose($lang="fr") {			
		
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:purpose/gmd:LocalisedCharacterString');
		return $nodes ->item(0)->nodeValue;
		}return "";		
								
	}
	
	
	function getAcquisitionRmk(){
		if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceNote/gmd:LocalisedCharacterString");
		return $nodes ->item(0)->nodeValue;
		}return "";
	}
	function getUpdateFrequency(){
	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode[@codeList='./resources/codeList.xml#MD_MaintenanceFrequencyCode']/@codeListValue");
			return $nodes ->item(0)->nodeValue;
			}return "";
	}
	function getUpdateDate($lang="fr") {	
				
		if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date/gmd:CI_Date/gmd:date/gco:Date");
		return $nodes ->item(0)->nodeValue;
		}return "";
					
	}	
	
	function getGeographicBBoxWest(){

		if ($this->metadata){				
				$nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal');
 			return $nodes ->item(0)->nodeValue;
		}return "";
	}
	function getGeographicBBoxEast(){

		if ($this->metadata){				
				$nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal');
 			return $nodes ->item(0)->nodeValue;
		}return "";
	}
		function getGeographicBBoxSouth(){

		if ($this->metadata){				
				$nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal');
 			return $nodes ->item(0)->nodeValue;
		}return "";
	}
	function getGeographicBBoxNorth(){

		if ($this->metadata){				
				$nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal');
 			return $nodes ->item(0)->nodeValue;
		}return "";
	}	
	
	
	function getTextualExtent($lang="fr") {

	if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description/gmd:LocalisedCharacterString');

	return $nodes ->item(0)->nodeValue;
	}return "";
		
	}	
	
	function getGraphicOverviewFileName($lang="fr") {			
	if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileName/gco:CharacterString');
	return $nodes ->item(0)->nodeValue;

	}return "";
						
	}
	
function getGraphicOverviewFileDescription($lang="fr") {
	
	if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileDescription/gmd:LocalisedCharacterString');
	return $nodes ->item(0)->nodeValue;

	}return "";	
	
	}

function getGraphicOverviewFileType($lang="fr") {
	if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileType/gmd:CharacterString');
	return $nodes ->item(0)->nodeValue;

	}return "";	
	
	}
	
function getLegalConstraint($lang="fr") {	

	
	if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:otherConstraints/gmd:LocalisedCharacterString');
	return $nodes ->item(0)->nodeValue;
	}return "";	
	}		
	
	
function getUseLimitation($lang="fr") {		
if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:useLimitation/gmd:LocalisedCharacterString');
	return $nodes ->item(0)->nodeValue;	
			}return "";
	}	
	
function getAcquisitionMode($lang="fr") {		
if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:statement/gmd:LocalisedCharacterString');
return $nodes ->item(0)->nodeValue;	
		}return "";
	}

	
function getAcquisitionDescription ($lang="fr") {			

	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:processStep/gmd:LI_ProcessStep/gmd:description/gmd:LocalisedCharacterString");
	return $nodes ->item(0)->nodeValue;
	}return "";
}			

	
	
	
	
function getAcquisitionDataSource ($lang="fr") {		

	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:processStep/gmd:LI_ProcessStep/gmd:source/gmd:LI_Source/gmd:description/gmd:LocalisedCharacterString");
	return $nodes ->item(0)->nodeValue;
	
}return "";
	}			
	
	
	

	

	
		
				
	


					
	

	
	
		
	
}	
?>