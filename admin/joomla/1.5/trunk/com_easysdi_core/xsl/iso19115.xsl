<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
Script XSLT de transformation des partenaires ASIT-VD sous Joomla!
Le résultat est fourni sous forme de fichier CSV avec une tabulation comme séparateur
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:gmd="http://www.isotc211.org/2005/gmd" 
xmlns:gco="http://www.isotc211.org/2005/gco"
xmlns:xlink="http://www.w3.org/1999/xlink"
xmlns:ext="http://www.depth.ch/2008/ext"
>

	<!-- Encodage des résultats -->
    <xsl:output encoding="utf-8"/>
    <xsl:output method="html"/>


<xsl:template match="gmd:MD_Metadata">


<h2 class="contentheading">Metadonnée</h2>
<div class="contentin">
<h3>Identification</h3>

<table border="1">
<tr><td>Id : </td> <td><xsl:value-of disable-output-escaping="yes" select="./gmd:fileIdentifier/gco:CharacterString"/></td></tr>
<tr><td>Nom :</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:LocalisedCharacterString"/></td></tr>
<tr><td>Description :</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:LocalisedCharacterString"/></td></tr>
<tr><td>Dernière Mise à jour :</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date/gmd:CI_Date/gmd:date/gco:Date"/></td></tr>
<tr><td>Etendue géographique*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description/gmd:LocalisedCharacterString"/></td></tr>
<tr><td>Couverture spatiale:</td><td>
	 
					<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/@xlink:title"/>        

</td></tr>

<tr><td>Synoptique:</td><td>
	<xsl:for-each select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview">
		<xsl:value-of disable-output-escaping="yes" select="gmd:MD_BrowseGraphic/gmd:fileDescription/gmd:LocalisedCharacterString"/>
		<br/>
	</xsl:for-each>
</td></tr>
<tr><td>Extrait:</td></tr><tr><td>Sous-produits:</td><td>
 <xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Identification']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Sous-produits' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td></tr>
<tr><td>Thématique*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory/gmd:MD_TopicCategoryCode"/></td></tr>
</table>
<hr></hr>

<h3>Diffusion</h3>
<table border="1">
<tr><td>Conditions de diffusion:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints[@xlink:title='Conditions de diffusion']/gmd:MD_LegalConstraints/gmd:otherConstraints/gmd:LocalisedCharacterString"/></td></tr>
<tr><td>Restriction d'utilisation:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:useLimitation/gmd:LocalisedCharacterString"/></td></tr>
<tr><td>Principes et mode de tarification:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributionOrderProcess/gmd:MD_StandardOrderProcess/gmd:fees/gmd:LocalisedCharacterString"/></td></tr>


</table>
<hr></hr>
<h3>Statut juridique</h3>
<table border="1">
                     
<tr><td>Statut:</td><td>
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Statut juridique']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Statut' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td></tr>
<tr><td>Référence du document légal:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints[@xlink:title='Référence du document légal']/gmd:MD_LegalConstraints/gmd:otherConstraints/gmd:LocalisedCharacterString"/></td></tr>
  
  </table>
<hr></hr> 

      
<h3>Gestion</h3>
<table  border="1">                   
<tr><td>Mode d'acquisition:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:statement/gmd:LocalisedCharacterString"/></td></tr>
<tr><td>Description du mode d'acquisition:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:processStep/gmd:LI_ProcessStep/gmd:description/gmd:LocalisedCharacterString"/></td></tr>
<tr><td>Données sources:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:processStep/gmd:LI_ProcessStep/gmd:source/gmd:LI_Source/gmd:description/gmd:LocalisedCharacterString"/></td></tr>
                 
<tr><td>Type de mise à jour:</td><td>
	<xsl:choose>
          <xsl:when test="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode/@codeListValue = 'continual'">
            Continu
          </xsl:when>
          <xsl:otherwise>
            Autre : 
</xsl:otherwise>
</xsl:choose>
</td></tr>
<tr><td>Fréquence:</td><td> <xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode/@codeListValue"/></td></tr>
<tr><td>Remarques:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceNote/gmd:LocalisedCharacterString"/></td></tr>
</table>
 <hr></hr>
 
 
<h3>Représentation</h3>
<table  border="1">
<tr><td>Système de coordonnées:</td><td>
<xsl:choose>
          <xsl:when test="./gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:referenceSystemIdentifier/gmd:RS_Identifier/gmd:code/gmd:LocalisedCharacterString = 'EPSG:21781'">            
            Coordonnées nationales suisses (EPSG:21781)
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of disable-output-escaping="yes" select="./gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:referenceSystemIdentifier/gmd:RS_Identifier/gmd:code/gmd:LocalisedCharacterString"/>
</xsl:otherwise>
</xsl:choose>
</td></tr>
<tr><td>Echelle de référence:</td><td></td></tr>
<tr><td>Précision:</td><td></td></tr>


<tr><td>Type d'objet graphique*:</td><td>

<xsl:choose>
          <xsl:when test="./gmd:spatialRepresentationInfo/gmd:MD_VectorSpatialRepresentation/gmd:geometricObjects/gmd:MD_GeometricObjects/gmd:geometricObjectType/gmd:MD_GeometricObjectTypeCode[@codeList='./resources/codeList.xml#MD_GeometricObjectTypeCode']/@codeListValue = 'composite'">            
            Mixte
          </xsl:when>
          <xsl:otherwise>
            Autre : <xsl:value-of disable-output-escaping="yes" select="./gmd:spatialRepresentationInfo/gmd:MD_VectorSpatialRepresentation/gmd:geometricObjects/gmd:MD_GeometricObjects/gmd:geometricObjectType/gmd:MD_GeometricObjectTypeCode[@codeList='./resources/codeList.xml#MD_GeometricObjectTypeCode']/@codeListValue"/>
</xsl:otherwise>
</xsl:choose>
</td></tr>
<tr><td>Cohérence topologique:</td><td></td></tr>
<tr><td>Information altimétrique:</td><td></td></tr>
<tr><td>Modèle de données</td><td>
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Représentation']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Modèle de données' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>
				
      </xsl:if>
</xsl:for-each>

</td></tr>
</table>

<hr></hr>
<h3>Attribut</h3>
<table  border="1">
 	 	 
<tr><td>Nom*:</td><td>
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Attribut']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Nom' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td></tr>
<tr><td>Description*:</td><td>
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Attribut']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Description' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td></tr>

<tr><td>Type:</td><td>
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Attribut']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Type' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td></tr> 	
<tr><td>Format:</td><td>
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Attribut']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Format' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td></tr> 	
<tr><td>Statut:</td><td>
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Attribut']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Statut' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td></tr>
</table>


<hr></hr>
<h3>Gestionnaire</h3>
<table  border="1">	 	 	  	 	
<tr><td>Organisme*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:LocalisedCharacterString"/></td></tr>
<tr><td>Nom:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/></td></tr>
<tr><td>Adresse:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString"/></td></tr>
<tr><td>Localité:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString"/></td></tr>
<tr><td>Code postal:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString"/></td></tr>
<tr><td>Pays:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString"/></td></tr>
<tr><td>Téléphone:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/></td></tr>
<tr><td>Fax:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString"/></td></tr>

<tr><td>Email:</td><td><xsl:element name="a">
<xsl:attribute name="href">
mailto:<xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:attribute>
<xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:element>
</td></tr>
</table>


<hr></hr>
<h3>Responsable de diffusion</h3>
<table  border="1">
<tr><td>Organisme*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:LocalisedCharacterString"/></td></tr>
<tr><td>Nom:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/></td></tr>
<tr><td>Adresse:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString"/></td></tr>
<tr><td>Localité:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString"/></td></tr>
<tr><td>Code postal:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString"/></td></tr>
<tr><td>Pays:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString"/></td></tr>
<tr><td>Téléphone:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/></td></tr>
<tr><td>Fax:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString"/></td></tr>
<tr><td>Email:</td><td>
<xsl:element name="a">
<xsl:attribute name="href">
mailto:<xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:attribute>
<xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:element>

</td></tr>
</table>


<hr></hr>
<h3>Point de contact</h3>
<table  border="1">
<tr><td>Organisme*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:LocalisedCharacterString"/></td></tr>
<tr><td>Nom:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/></td></tr>
<tr><td>Adresse:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString"/></td></tr>
<tr><td>Localité:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString"/></td></tr>
<tr><td>Code postal:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString"/></td></tr>
<tr><td>Pays:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString"/></td></tr>
<tr><td>Téléphone:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/></td></tr>
<tr><td>Fax:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString"/></td></tr>
<tr><td>Email:</td><td>
<xsl:element name="a">
<xsl:attribute name="href">
mailto:<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:attribute>
<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:element>
</td></tr>

</table>
</div>

</xsl:template>

</xsl:stylesheet>