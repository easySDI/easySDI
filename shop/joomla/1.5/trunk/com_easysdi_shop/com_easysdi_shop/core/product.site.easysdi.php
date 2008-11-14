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

defined('_JEXEC') or die('Restricted access');

class SITE_product {
	
	function SaveMetadata(){

$xmlstr = "
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<csw:Transaction service=\"CSW\" 
    version=\"2.0.0\" 
    xmlns:csw=\"http://www.opengis.net/cat/csw\" >
   <csw:Insert>
  <gmd:MD_Metadata xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" xmlns:gco=\"http://www.isotc211.org/2005/gco\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:gml=\"http://www.opengis.net/gml\" xmlns:gts=\"http://www.isotc211.org/2005/gts\" xmlns:ext=\"http://www.depth.ch/2008/ext\">
    <gmd:fileIdentifier>
      <gco:CharacterString>".JRequest::getVar("metadata_id")."</gco:CharacterString>
    </gmd:fileIdentifier>
    <gmd:contact>
      <gmd:CI_ResponsibleParty>
        <gmd:individualName>
          <gco:CharacterString >".JRequest::getVar("metadata_manager_name")."</gco:CharacterString >
        </gmd:individualName>
        <gmd:organisationName>
          <gmd:PT_FreeText>
            <gmd:textGroup>
              <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_manager_organisation_name")."</gmd:LocalisedCharacterString>
            </gmd:textGroup>
          </gmd:PT_FreeText>

        </gmd:organisationName>
        <gmd:contactInfo>
          <gmd:CI_Contact>
            <gmd:phone>
              <gmd:CI_Telephone>
                <gmd:voice>
                  <gco:CharacterString>".JRequest::getVar("metadata_manager_voice")."</gco:CharacterString>
                </gmd:voice>
                <gmd:facsimile>
                  <gco:CharacterString>".JRequest::getVar("metadata_manager_fax")."</gco:CharacterString>
                </gmd:facsimile>
              </gmd:CI_Telephone>
            </gmd:phone>
            <gmd:address>
              <gmd:CI_Address>
                <gmd:deliveryPoint>

                  <gco:CharacterString>".JRequest::getVar("metadata_manager_address")."</gco:CharacterString>
                </gmd:deliveryPoint>
                <gmd:city>
                  <gco:CharacterString>".JRequest::getVar("metadata_manager_city")."</gco:CharacterString>
                </gmd:city>
                <gmd:postalCode>
                  <gco:CharacterString>".JRequest::getVar("metadata_manager_postal_code")."</gco:CharacterString>

                </gmd:postalCode>
                <gmd:country>
                  <gco:CharacterString>".JRequest::getVar("metadata_manager_country")."</gco:CharacterString>
                </gmd:country>
                <gmd:electronicMailAddress>
                  <gco:CharacterString >".JRequest::getVar("metadata_manager_mail")."</gco:CharacterString>
                </gmd:electronicMailAddress>
              </gmd:CI_Address>

            </gmd:address>
          </gmd:CI_Contact>
        </gmd:contactInfo>
        <gmd:role>
          <gmd:CI_RoleCode codeList=\"custodian\" />
        </gmd:role>
      </gmd:CI_ResponsibleParty>
    </gmd:contact>
    <gmd:dateStamp>
      <gco:DateTime>".JRequest::getVar("")."</gco:DateTime>
    </gmd:dateStamp>
    <gmd:metadataStandardName>
      <gco:CharacterString>ISO 19115:2003/19139</gco:CharacterString>
    </gmd:metadataStandardName>
    <gmd:locale>
      <gmd:PT_Locale>
        <gmd:languageCode>
          <gmd:LanguageCode codeList=\"./resources/codeList.xml#LanguageCode\" codeListValue=\"fr\" />
        </gmd:languageCode>
        <gmd:country>
          <gmd:Country codeList=\"./resources/codeList.xml#Country\" codeListValue=\"CH\" />
        </gmd:country>
      </gmd:PT_Locale>
    </gmd:locale>
    <gmd:referenceSystemInfo>
      <gmd:MD_ReferenceSystem>
        <gmd:referenceSystemIdentifier>
          <gmd:RS_Identifier>
            <gmd:code>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\" >".JRequest::getVar("ReferenceSystemInfo")."</gmd:LocalisedCharacterString >
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:code>
         </gmd:RS_Identifier>
        </gmd:referenceSystemIdentifier>
      </gmd:MD_ReferenceSystem>
    </gmd:referenceSystemInfo>
    <gmd:identificationInfo>
      <gmd:MD_DataIdentification>
        <gmd:citation>
          <gmd:CI_Citation>
            <gmd:title>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("data_title")."</gmd:LocalisedCharacterString>
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:title>
            <gmd:date>
              <gmd:CI_Date>
                <gmd:date>
                  <gco:Date>".JRequest::getVar("metadata_last_update")."</gco:Date>
                </gmd:date>
                <gmd:dateType>
                  <gmd:CI_DateTypeCode codeList=\"./resources/codeList.xml#CI_DateTypeCode\" codeListValue=\"revision\" />
                </gmd:dateType>
              </gmd:CI_Date>
            </gmd:date>
          </gmd:CI_Citation>
        </gmd:citation>
        <gmd:abstract>
          <gmd:PT_FreeText>
            <gmd:textGroup>
              <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_description")."</gmd:LocalisedCharacterString>
            </gmd:textGroup>
          </gmd:PT_FreeText>
        </gmd:abstract>
        <gmd:status>
        <gmd:MD_ProgressCode codeList=\"./resources/codeList.xml#MD_ProgressCode\" codeListValue=\"".JRequest::getVar("metadata_status")."\"/>
      </gmd:status>        
        <gmd:purpose>
        <gmd:PT_FreeText>
            <gmd:textGroup>
              <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_purpose")."</gmd:LocalisedCharacterString>
            </gmd:textGroup>
          </gmd:PT_FreeText>
      </gmd:purpose>  
        <gmd:pointOfContact>
          <gmd:CI_ResponsibleParty>
            <gmd:individualName>
              <gco:CharacterString>".JRequest::getVar("metadata_poc_name")."</gco:CharacterString>
            </gmd:individualName>
            <gmd:organisationName>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_poc_organisation_name")."</gmd:LocalisedCharacterString>
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:organisationName>
            <gmd:contactInfo>
              <gmd:CI_Contact>
                <gmd:phone>
                  <gmd:CI_Telephone>
                    <gmd:voice>
                      <gco:CharacterString>".JRequest::getVar("metadata_poc_voice")."</gco:CharacterString>
                    </gmd:voice>
                    <gmd:facsimile>
                      <gco:CharacterString>".JRequest::getVar("metadata_poc_fax")."</gco:CharacterString>
                    </gmd:facsimile>
                  </gmd:CI_Telephone>
                </gmd:phone>
                <gmd:address>
                  <gmd:CI_Address>
                    <gmd:deliveryPoint>
                      <gco:CharacterString>".JRequest::getVar("metadata_poc_address")."</gco:CharacterString>
                    </gmd:deliveryPoint>
                    <gmd:city>
                      <gco:CharacterString>".JRequest::getVar("metadata_poc_city")."</gco:CharacterString>
                    </gmd:city>
                    <gmd:postalCode>
                      <gco:CharacterString>".JRequest::getVar("metadata_poc_postal_code")."</gco:CharacterString>
                    </gmd:postalCode>
                    <gmd:country>
                      <gco:CharacterString>".JRequest::getVar("metadata_poc_Country")."</gco:CharacterString>
                    </gmd:country>
                    <gmd:electronicMailAddress>
                      <gco:CharacterString >".JRequest::getVar("metadata_poc_email")."</gco:CharacterString>
                    </gmd:electronicMailAddress>
                  </gmd:CI_Address>
                </gmd:address>
              </gmd:CI_Contact> 
            </gmd:contactInfo>
            <gmd:role>
              <gmd:CI_RoleCode codeList=\"pointOfContact\" />
            </gmd:role>
          </gmd:CI_ResponsibleParty>
        </gmd:pointOfContact>
        <gmd:resourceMaintenance>
          <gmd:MD_MaintenanceInformation>
            <gmd:maintenanceAndUpdateFrequency>
              <gmd:MD_MaintenanceFrequencyCode codeList=\"./resources/codeList.xml#MD_MaintenanceFrequencyCode\" codeListValue=\"".JRequest::getVar("metadata_acquisition_freq")."\" />
            </gmd:maintenanceAndUpdateFrequency>
            <gmd:maintenanceNote>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_acquisition_rem")."</gmd:LocalisedCharacterString >
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:maintenanceNote>
          </gmd:MD_MaintenanceInformation>
        </gmd:resourceMaintenance>
        <gmd:graphicOverview>
          <gmd:MD_BrowseGraphic>
            <gmd:fileName>
              <gco:CharacterString >".JRequest::getVar("metadata_graphic_overview")."</gco:CharacterString>
            </gmd:fileName>
            <gmd:fileDescription>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\" >".JRequest::getVar("metadata_graphic_overview_description")."</gmd:LocalisedCharacterString> 
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:fileDescription>
            <gmd:fileType>
              <gco:CharacterString />
            </gmd:fileType>
          </gmd:MD_BrowseGraphic>
        </gmd:graphicOverview>        
        <gmd:resourceConstraints xlink:title=\"Conditions de diffusion\">
          <gmd:MD_LegalConstraints>
            <gmd:otherConstraints>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_legal_constraint")."</gmd:LocalisedCharacterString>
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:otherConstraints>
          </gmd:MD_LegalConstraints>
        </gmd:resourceConstraints>
        <gmd:resourceConstraints>
          <gmd:MD_LegalConstraints>
            <gmd:useLimitation>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\" >".JRequest::getVar("metadata_use_limitation")."</gmd:LocalisedCharacterString >
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:useLimitation>
          </gmd:MD_LegalConstraints>
        </gmd:resourceConstraints>
        <gmd:resourceConstraints xlink:title=\"Référence du document légal\">
          <gmd:MD_LegalConstraints>
            <gmd:otherConstraints>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\" />
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:otherConstraints>
          </gmd:MD_LegalConstraints>
        </gmd:resourceConstraints>        
        <gmd:topicCategory>
          <gmd:MD_TopicCategoryCode>".JRequest::getVar("metadata_thema")."</gmd:MD_TopicCategoryCode>
        </gmd:topicCategory>
        <gmd:extent>
          <gmd:EX_Extent>
            <gmd:description>
              <gmd:PT_FreeText>
                <gmd:textGroup> 
                  <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_geograhic_textual")."</gmd:LocalisedCharacterString>
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:description>
            <gmd:geographicElement>
              <gmd:EX_GeographicBoundingBox>
                <gmd:westBoundLongitude>
                  <gco:Decimal>".JRequest::getVar("metadata_spatial_coverage_west")."</gco:Decimal>
                </gmd:westBoundLongitude>
                <gmd:eastBoundLongitude>
                  <gco:Decimal>".JRequest::getVar("metadata_spatial_coverage_east")."</gco:Decimal>
                </gmd:eastBoundLongitude>
                <gmd:southBoundLatitude>
                  <gco:Decimal>".JRequest::getVar("metadata_spatial_coverage_south")."</gco:Decimal>
                </gmd:southBoundLatitude>
                <gmd:northBoundLatitude>
                  <gco:Decimal>".JRequest::getVar("metadata_spatial_coverage_north")."</gco:Decimal>
                </gmd:northBoundLatitude>
              </gmd:EX_GeographicBoundingBox>
            </gmd:geographicElement>
          </gmd:EX_Extent>
        </gmd:extent>
      </gmd:MD_DataIdentification>
    </gmd:identificationInfo>
    <gmd:distributionInfo>
      <gmd:MD_Distribution>
        <gmd:distributor>
          <gmd:MD_Distributor>
      		<gmd:distributorContact>              
          <gmd:CI_ResponsibleParty>
        <gmd:individualName>
          <gco:CharacterString >".JRequest::getVar("metadata_distribution_name")."</gco:CharacterString >
        </gmd:individualName>
        <gmd:organisationName>
          <gmd:PT_FreeText>
            <gmd:textGroup>
              <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_distribution_organisation_name")."</gmd:LocalisedCharacterString>
            </gmd:textGroup>
          </gmd:PT_FreeText>

        </gmd:organisationName>
        <gmd:contactInfo>
          <gmd:CI_Contact>
            <gmd:phone>
              <gmd:CI_Telephone>
                <gmd:voice>
                  <gco:CharacterString>".JRequest::getVar("metadata_distribution_voice")."</gco:CharacterString>
                </gmd:voice>
                <gmd:facsimile>
                  <gco:CharacterString>".JRequest::getVar("metadata_distribution_fax")."</gco:CharacterString>
                </gmd:facsimile>
              </gmd:CI_Telephone>
            </gmd:phone>
            <gmd:address>
              <gmd:CI_Address>
                <gmd:deliveryPoint>
                  <gco:CharacterString>".JRequest::getVar("metadata_distribution_address")."</gco:CharacterString>
                </gmd:deliveryPoint>
                <gmd:city>
                  <gco:CharacterString>".JRequest::getVar("metadata_distribution_city")."</gco:CharacterString>
                </gmd:city>
                <gmd:postalCode>
                  <gco:CharacterString>".JRequest::getVar("metadata_distribution_code")."</gco:CharacterString>
                </gmd:postalCode>
                <gmd:country>
                  <gco:CharacterString>".JRequest::getVar("metadata_distribution_country")."</gco:CharacterString>
                </gmd:country>
                <gmd:electronicMailAddress>
                  <gco:CharacterString >".JRequest::getVar("metadata_distribution_mail")."</gco:CharacterString>
                </gmd:electronicMailAddress>
              </gmd:CI_Address>
            </gmd:address>
          </gmd:CI_Contact>
        </gmd:contactInfo>
        <gmd:role>
          <gmd:CI_RoleCode codeList=\"custodian\" />
        </gmd:role>
      </gmd:CI_ResponsibleParty>          
          </gmd:distributorContact>
             <gmd:distributionOrderProcess>
              <gmd:MD_StandardOrderProcess>
                <gmd:fees>
                  <gmd:PT_FreeText>
                    <gmd:textGroup>
                      <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_tarif")."</gmd:LocalisedCharacterString>
                    </gmd:textGroup>
                  </gmd:PT_FreeText>
                </gmd:fees>
              </gmd:MD_StandardOrderProcess>
            </gmd:distributionOrderProcess>
          </gmd:MD_Distributor>
        </gmd:distributor>
      </gmd:MD_Distribution>
    </gmd:distributionInfo>
    <gmd:dataQualityInfo>
      <gmd:DQ_DataQuality>
        <gmd:lineage>
          <gmd:LI_Lineage>
            <gmd:statement>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_acquisition")."</gmd:LocalisedCharacterString>
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:statement>
            <gmd:processStep>
              <gmd:LI_ProcessStep>
                <gmd:description>
                  <gmd:PT_FreeText>
                    <gmd:textGroup>
                      <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_acquisition_desc")."</gmd:LocalisedCharacterString>
                    </gmd:textGroup>
                  </gmd:PT_FreeText>
                </gmd:description>
                <gmd:source>
                  <gmd:LI_Source>
                    <gmd:description>
                      <gmd:PT_FreeText>
                        <gmd:textGroup>
                          <gmd:LocalisedCharacterString locale=\"fr-CH\">".JRequest::getVar("metadata_acquisition_data_source")."  </gmd:LocalisedCharacterString>
                        </gmd:textGroup>
                      </gmd:PT_FreeText>
                    </gmd:description>
                  </gmd:LI_Source>
                </gmd:source>
              </gmd:LI_ProcessStep>
            </gmd:processStep>
          </gmd:LI_Lineage>
        </gmd:lineage>
      </gmd:DQ_DataQuality>
    </gmd:dataQualityInfo>
      
  </gmd:MD_Metadata>
</csw:Insert>
 </csw:Transaction>
";
		
		
		//$xmlstr = utf8_encode($xmlstr);
		$content_length = strlen($xmlstr); 

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		
$session = curl_init($catalogUrlBase);


        curl_setopt ($session, CURLOPT_POST, true);
        curl_setopt ($session, CURLOPT_POSTFIELDS, $xmlstr);


    // Don't return HTTP headers. Do return the contents of the call
    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

    // Make the call
    $xml = curl_exec($session);

    

    echo $xml;
    curl_close($session);			
		
	}
	
	
	function saveProduct($option){
						global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowProduct =&	 new Product($database);
				
		
		if (!$rowProduct->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}
				
		
		if (!$rowProduct->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}

		
		
		$query = "DELETE FROM  #__easysdi_product_perimeter WHERE PRODUCT_ID = ".$rowProduct->id;
		$database->setQuery( $query );
		if (!$database->query()) {		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );	
				exit();		
		}
		
		
		
		foreach( $_POST['perimeter_id'] as $perimeter_id )
		{
			$query = "INSERT INTO #__easysdi_product_perimeter VALUES (0,".$rowProduct->id.",".$perimeter_id.")";
			
			$database->setQuery( $query );
			if (!$database->query()) {
				//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );	
					exit();			
			}
		}
		
		

		
		$query = "DELETE FROM  #__easysdi_product_property WHERE PRODUCT_ID = ".$rowProduct->id;
		$database->setQuery( $query );
		if (!$database->query()) {		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();			
		}
		
		
		
		foreach( $_POST['properties_id'] as $properties_id )
		{
			$query = "INSERT INTO #__easysdi_product_property VALUES (0,".$rowProduct->id.",".$properties_id.")";
			
			$database->setQuery( $query );
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
					exit();				
			}
		}
		
		 SITE_product::SaveMetadata();
		 
	
		
	}
	
function editProduct( $isNew = false) {
		global  $mainframe;
		if (!$isNew){
		$id = JRequest::getVar('id');
		}else {
			$id=0;
		}
		
		$option = JRequest::getVar('option');
		  
		$database =& JFactory::getDBO(); 
		$rowProduct = new product( $database );
		$rowProduct->load( $id );					
	
		if ($id ==0){
			$rowProduct->creation_date =date('d.m.Y H:i:s');
			$rowProduct->metadata_id = uniqid();
			 			
		}
		$rowProduct->update_date = date('d.m.Y H:i:s'); 
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		if (strlen($catalogUrlBase )==0){
				$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}else{
		HTML_product::editProduct( $rowProduct,$id, $option );
		}
	}
	
	
	function listProduct(){

		
		global  $mainframe;
		$option=JRequest::getVar("option");
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 5 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		$database =& JFactory::getDBO();		 	
		$user = JFactory::getUser();
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($user->id);		
		
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
		if ( $search ) {
			$filter .= " AND (data_title LIKE '%$search%')";			
		}
		$partner = new partnerByUserId($database);
		$partner->load($user->id);
		
			
		$queryCount = "select count(*) from #__easysdi_product where partner_id = (partner_id = $partner->partner_id AND internal=1) OR (external=1) ";
		
		  
		
		$queryCount .= $filter;
		
		
		
		$database->setQuery($queryCount);
		$total = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		$pageNav = new JPagination($total,$limitstart,$limit);
		$query = "select * from #__easysdi_product where partner_id = (partner_id = $partner->partner_id AND internal=1) OR (external=1) ";
		$query .= $filter;
	


		$database->setQuery($query,$limitstart,$limit);		
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		HTML_product::listProduct($pageNav,$rows,$option);
		
}
}
?>