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

class geoMetadata{
	var $metadata;
	var $xpath;
	
	function __construct( &$md ){
		
		if ($md){			
		$this->metadata  = $md;		
		$this->xpath = new DomXPath($md);
		
		$this->xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
		$this->xpath->registerNamespace('gco','http://www.isotc211.org/2005/gco');
		}		
		
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
		$nodes = $this->xpath->query("//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributionOrderProcess/gmd:MD_StandardOrderProcess/gmd:fees/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString");
		return $nodes ->item(0)->nodeValue;
		}
		return "";
	}
	function getManagerOrganisationName($lang="fr"){			
		if ($this->metadata){
		$nodes = $this->xpath->query('//gmd:contact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString');
		return $nodes ->item(0)->nodeValue;
		}
		return "";
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
	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString");
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
	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString");
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
		foreach ($this->metadata->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_DataIdentification"  ) as $MD_DataIdentification){			
			foreach ($MD_DataIdentification->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "pointOfContact"  ) as $pointOfContact){
				foreach ($pointOfContact->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "CI_ResponsibleParty"  ) as $CI_ResponsibleParty){
					foreach ($CI_ResponsibleParty->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "organisationName"  ) as $organisationName){
						foreach ($organisationName->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "textGroup"  ) as $organisationName){
								return $organisationName->nodeValue;
					}							
				}	
			}			
		  }		
		}					
		}return "";										
	}

	function getDescription($lang="fr") {			
		
		if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString');
		return $nodes ->item(0)->nodeValue;
		}return "";		
								
	}

	function getAcquisitionRmk(){
		if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceNote/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString");
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
	
	function getGeographicBBox(){

		if ($this->metadata){		
	
		}return "";
	}
	function getTextualExtent($lang="fr") {

	if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString');

	return $nodes ->item(0)->nodeValue;
	}return "";
		
	}	
	
	function getGraphicOverviewFileName($lang="fr") {			
	if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileName/gco:CharacterString');
	return $nodes ->item(0)->nodeValue;

	}return "";
						
	}
	
function getGraphicOverviewFileDescription($lang="fr") {				
	if ($this->metadata){	
		foreach ($this->metadata->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_DataIdentification"  ) as $MD_DataIdentification){			
			foreach ($MD_DataIdentification->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "graphicOverview"  ) as $graphicOverview){
				foreach ($graphicOverview->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_BrowseGraphic"  ) as $MD_BrowseGraphic){
					foreach ($MD_BrowseGraphic->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "PT_FreeText"  ) as $PT_FreeText){
						foreach ($PT_FreeText->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "textGroup"  ) as $textGroup){
							foreach ($textGroup->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "LocalisedCharacterString"  ) as $LocalisedCharacterString){
								return $LocalisedCharacterString->nodeValue;
														}							
					}							
				}	
			}			
		  }		
		}			
		}return "";
	}

function getGraphicOverviewFileType($lang="fr") {
	if ($this->metadata){	
		foreach ($this->metadata->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_DataIdentification"  ) as $MD_DataIdentification){			
			foreach ($MD_DataIdentification->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "graphicOverview"  ) as $graphicOverview){
				foreach ($graphicOverview->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_BrowseGraphic"  ) as $MD_BrowseGraphic){
					foreach ($MD_BrowseGraphic->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "fileType"  ) as $fileType){
						foreach ($PT_FreeText->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gco" , "CharacterString"  ) as $CharacterString){
							
								return $CharacterString->nodeValue;							
					}							
				}	
			}			
		  }		
		}		
		}return "";
	}
	
function getLegalConstraint($lang="fr") {	

	
	if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:otherConstraints/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString');
	return $nodes ->item(0)->nodeValue;
	}return "";	
	}		
	
	
function getUseLimitation($lang="fr") {		
if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:useLimitation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString');
	return $nodes ->item(0)->nodeValue;	
			}return "";
	}	
	
function getAcquisitionMode($lang="fr") {		
if ($this->metadata){	 $nodes = $this->xpath->query('//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:statement/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString');
return $nodes ->item(0)->nodeValue;	
		}return "";
	}

	
function getAcquisitionDescription ($lang="fr") {			

	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:processStep/gmd:LI_ProcessStep/gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString");
	return $nodes ->item(0)->nodeValue;
	}return "";
}			

	
	
	
	
function getAcquisitionDataSource ($lang="fr") {		

	if ($this->metadata){	 $nodes = $this->xpath->query("//gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:processStep/gmd:LI_ProcessStep/gmd:source/gmd:LI_Source/gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString");
	return $nodes ->item(0)->nodeValue;
	
}return "";
	}			
	
	
	

	

	
		
				
	


					
	

	
	
		
	
}	
?>