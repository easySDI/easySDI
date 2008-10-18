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

class ADMIN_product {
	
	function publish($cid,$published){
			global  $mainframe;
			$db =& JFactory::getDBO(); 
		if ($published){
			$query = "update #__easysdi_product  set published = 1  where id=$cid[0]";			
			
		}else{
			$query = "update #__easysdi_product  set published = 0  where id=$cid[0]";
		}
		$db->setQuery( $query ,$limitstart,$limit);
		if (!$db->query()) {		
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		
		
	}
	function listProduct($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		$profile = $mainframe->getUserStateFromRequest( "profile{$option}", 'profile', '' );
		$category = $mainframe->getUserStateFromRequest( "category{$option}", 'category', '' );
		$payment = $mainframe->getUserStateFromRequest( "payment{$option}", 'payment', '' );
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );

		$query = "SELECT COUNT(*) FROM #__easysdi_product";
		
		//$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		
		
		$query = "SELECT * FROM #__easysdi_product ";		
									
		
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}
		else {
		$db->setQuery( $query );
		}
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		
	
		HTML_product::listProduct($use_pagination, $rows, $pageNav,$option);	

	}
	

	function editProduct( $id, $option ) {
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$rowProduct = new product( $database );
		$rowProduct->load( $id );					
	
		if ($id == '0'){
			$rowProduct->creation_date =date('d.m.Y H:i:s');
			$rowProduct->metadata_id = uniqid();
			 			
		}
		$rowProduct->update_date = date('d.m.Y H:i:s'); 
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		if (strlen($catalogUrlBase )==0){
				$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}else{
		HTML_product::editProduct( $rowProduct,$id, $option );
		}
	}
	
	function saveProduct($returnList ,$option){
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
		
		 ADMIN_product::SaveMetadata();
		 
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listProduct");
		}
		
	}
	
	function deleteProduct($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit;
		}
		foreach( $cid as $id )
		{
			$product = new product( $database );
			$product->load( $id );
					
			if (!$product->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
			}												
		
			ADMIN_product::deleteMetadata($product->metadata_id);

		
		$query = "DELETE FROM  #__easysdi_product_perimeter WHERE PRODUCT_ID = ".$id;
		$database->setQuery( $query );
		if (!$database->query()) {		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}
		
		$query = "DELETE FROM  #__easysdi_product_property WHERE PRODUCT_ID = ".$id;
		$database->setQuery( $query );
		if (!$database->query()) {		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}
		
		
		
		}
		
		
		
		$mainframe->redirect("index.php?option=$option&task=listProduct" );		
	}
		
function deleteMetadata($metadata_id){
		$xmlstr = "
		<csw:Transaction service=\"CSW\" 
   version=\"2.0.0\" 
   xmlns:csw=\"http://www.opengis.net/cat/csw\" 
   xmlns:dc=\"http://www.purl.org/dc/elements/1.1/\"
   xmlns:ogc=\"http://www.opengis.net/ogc\"
   xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" 
   xmlns:gco=\"http://www.isotc211.org/2005/gco\">
  <csw:Delete typeName=\"csw:Record\">
    <csw:Constraint version=\"2.0.0\">
      <ogc:Filter>
        <ogc:PropertyIsEqualTo>
            <ogc:PropertyName>//gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString</ogc:PropertyName>
            <ogc:Literal>".$metadata_id."</ogc:Literal>
        </ogc:PropertyIsEqualTo>
      </ogc:Filter>
    </csw:Constraint>
  </csw:Delete>
</csw:Transaction>";
				
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'easysdi.config.php');
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
                  <gmd:LocalisedCharacterString locale=\"fr-CH\" />
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
              <gco:CharacterString >".JRequest::getVar("metadata_extrait")."</gco:CharacterString>
            </gmd:fileName>
            <gmd:fileDescription>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\" />
                </gmd:textGroup>
              </gmd:PT_FreeText>

            </gmd:fileDescription>
            <gmd:fileType>
              <gco:CharacterString />
            </gmd:fileType>
          </gmd:MD_BrowseGraphic>
        </gmd:graphicOverview>
        <gmd:graphicOverview>
          <gmd:MD_BrowseGraphic>
            <gmd:fileName>

              <gco:CharacterString></gco:CharacterString>
            </gmd:fileName>
            <gmd:fileDescription>
              <gmd:PT_FreeText>
                <gmd:textGroup>
                  <gmd:LocalisedCharacterString locale=\"fr-CH\"></gmd:LocalisedCharacterString>

                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:fileDescription>
            <gmd:fileType>
              <gco:CharacterString></gco:CharacterString>
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
          <gmd:MD_TopicCategoryCode>utilitiesCommunication</gmd:MD_TopicCategoryCode>
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

                  <gco:Decimal></gco:Decimal>
                </gmd:westBoundLongitude>
                <gmd:eastBoundLongitude>
                  <gco:Decimal></gco:Decimal>
                </gmd:eastBoundLongitude>
                <gmd:southBoundLatitude>
                  <gco:Decimal></gco:Decimal>

                </gmd:southBoundLatitude>
                <gmd:northBoundLatitude>
                  <gco:Decimal></gco:Decimal>
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
                  <gmd:LocalisedCharacterString locale=\"fr-CH\"> ".JRequest::getVar("metadata_acquisition")."</gmd:LocalisedCharacterString>
                </gmd:textGroup>
              </gmd:PT_FreeText>
            </gmd:statement>
            <gmd:processStep>
              <gmd:LI_ProcessStep>

                <gmd:description>
                  <gmd:PT_FreeText>
                    <gmd:textGroup>
                      <gmd:LocalisedCharacterString locale=\"fr-CH\"> ".JRequest::getVar("metadata_acquisition_desc")."</gmd:LocalisedCharacterString>
                    </gmd:textGroup>
                  </gmd:PT_FreeText>
                </gmd:description>
                <gmd:source>

                  <gmd:LI_Source>
                    <gmd:description>
                      <gmd:PT_FreeText>
                        <gmd:textGroup>
                          <gmd:LocalisedCharacterString locale=\"fr-CH\">  ".JRequest::getVar("metadata_acquisition_data_source")."  </gmd:LocalisedCharacterString>
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
    <gmd:extendedMetadata xlink:title=\"Identification\">

      <ext:EX_extendedMetadata_Type>
        <ext:name>
          <gco:CharacterString>Couverture spatiale</gco:CharacterString>
        </ext:name>
        <ext:value>
          <gmd:PT_FreeText>
            <gmd:textGroup>
              <gmd:LocalisedCharacterString locale=\"fr-CH\"></gmd:LocalisedCharacterString>

            </gmd:textGroup>
          </gmd:PT_FreeText>
        </ext:value>
      </ext:EX_extendedMetadata_Type>
    </gmd:extendedMetadata>
    <gmd:extendedMetadata xlink:title=\"Identification\">
      <ext:EX_extendedMetadata_Type>
        <ext:name>
          <gco:CharacterString></gco:CharacterString>

        </ext:name>
        <ext:value>
          <gmd:PT_FreeText>
            <gmd:textGroup>
              <gmd:LocalisedCharacterString locale=\"fr-CH\" />
            </gmd:textGroup>
          </gmd:PT_FreeText>
        </ext:value>
      </ext:EX_extendedMetadata_Type>

    </gmd:extendedMetadata>
    <gmd:extendedMetadata xlink:title=\"Diffusion\">
      <ext:EX_extendedMetadata_Type>
        <ext:name>
          <gco:CharacterString></gco:CharacterString>
        </ext:name>
        <ext:value>
          <gmd:PT_FreeText>

            <gmd:textGroup>
              <gmd:LocalisedCharacterString locale=\"fr-CH\"></gmd:LocalisedCharacterString>
            </gmd:textGroup>
          </gmd:PT_FreeText>
        </ext:value>
      </ext:EX_extendedMetadata_Type>
    </gmd:extendedMetadata>
    <gmd:extendedMetadata xlink:title=\"Diffusion\">

      <ext:EX_extendedMetadata_Type>
        <ext:name>
          <gco:CharacterString></gco:CharacterString>
        </ext:name>
        <ext:value>
          <gmd:PT_FreeText>
            <gmd:textGroup>
              <gmd:LocalisedCharacterString locale=\"fr-CH\"></gmd:LocalisedCharacterString>

            </gmd:textGroup>
          </gmd:PT_FreeText>
        </ext:value>
      </ext:EX_extendedMetadata_Type>
    </gmd:extendedMetadata>
  </gmd:MD_Metadata>
</csw:Insert>
 </csw:Transaction>
";
		
		
		//$xmlstr = utf8_encode($xmlstr);
		$content_length = strlen($xmlstr); 

	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'easysdi.config.php');
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
	
	
	
}
	
?>