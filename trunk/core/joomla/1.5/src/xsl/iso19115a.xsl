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
<h3>Coucou</h3>
<dl>

<dt>Id :</dt><dd><xsl:value-of select="./gmd:fileIdentifier/gco:CharacterString"/></dd>
<dt>Nom :</dt><dd><xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/></dd>
<dt>Description :</dt><dd><xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/></dd>
<dt>Dernière Mise à jour :</dt><dd><xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date/gmd:CI_Date/gmd:date/gco:Date"/></dd>
<dt>Etendue géographique*:</dt><dd></dd>
<dt>Couverture spatiale:</dt><dd>
 <xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Identification']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Couverture spatiale' ">
				<xsl:value-of select="ext:EX_extendedMetadata_Type/ext:value/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
</dd>
</dl>
<hr></hr>

Synoptique:
Extrait:
Sous-produits:
 <xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Identification']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Sous-produits' ">
				<xsl:value-of select="ext:EX_extendedMetadata_Type/ext:value/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>

Thématique*:

Conditions de diffusion:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints[@xlink:title='Conditions de diffusion']/gmd:MD_LegalConstraints/gmd:otherConstraints/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>
Restriction d'utilisation:
Principes et mode de tarification:<xsl:value-of select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributionOrderProcess/gmd:MD_StandardOrderProcess/gmd:fees/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>
                     
Statut:
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Diffusion']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Statut' ">
				<xsl:value-of select="ext:EX_extendedMetadata_Type/ext:value/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>

Référence du document légal:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints[@xlink:title='Référence du document légal']/gmd:MD_LegalConstraints/gmd:otherConstraints/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>
                   
Mode d'acquisition:<xsl:value-of select="./gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:statement/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>
Description du mode d'acquisition:<xsl:value-of select="./gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:processStep/gmd:LI_ProcessStep/gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>
Données sources:
  
  
  
                      

 
                      
                      

Type de mise à jour:


<xsl:choose>
          <xsl:when test="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode[@codeList='./resources/codeList.xml#MD_MaintenanceFrequencyCode']/@codeListValue = 'continual'">
            
            Continu
          </xsl:when>
          <xsl:otherwise>
            Autre : <xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode[@codeList='./resources/codeList.xml#MD_MaintenanceFrequencyCode']/@codeListValue"/>
</xsl:otherwise>
</xsl:choose>

Fréquence:
Remarques:
<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceNote/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>                  

 



Système de coordonnées:
<xsl:value-of select="./gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:referenceSystemIdentifier/gmd:RS_Identifier/gmd:code/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>

Echelle de référence:
Précision:


Type d'objet graphique*:

<xsl:choose>
          <xsl:when test="./gmd:spatialRepresentationInfo/gmd:MD_VectorSpatialRepresentation/gmd:geometricObjects/gmd:MD_GeometricObjects/gmd:geometricObjectType/gmd:MD_GeometricObjectTypeCode[@codeList='./resources/codeList.xml#MD_GeometricObjectTypeCode']/@codeListValue = 'composite'">            
            Mixte
          </xsl:when>
          <xsl:otherwise>
            Autre : <xsl:value-of select="./gmd:spatialRepresentationInfo/gmd:MD_VectorSpatialRepresentation/gmd:geometricObjects/gmd:MD_GeometricObjects/gmd:geometricObjectType/gmd:MD_GeometricObjectTypeCode[@codeList='./resources/codeList.xml#MD_GeometricObjectTypeCode']/@codeListValue"/>
</xsl:otherwise>
</xsl:choose>

Cohérence topologique:
Information altimétrique:
Modèle de données

<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Représentation']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Modèle de données' ">
				<xsl:value-of select="ext:EX_extendedMetadata_Type/ext:value/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>


 	 	 
Nom*:
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Attribut']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Nom' ">
				<xsl:value-of select="ext:EX_extendedMetadata_Type/ext:value/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>

Description*:
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Attribut']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Description' ">
				<xsl:value-of select="ext:EX_extendedMetadata_Type/ext:value/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>


Type:
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Attribut']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Type' ">
				<xsl:value-of select="ext:EX_extendedMetadata_Type/ext:value/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
 	
Format:
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Attribut']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Format' ">
				<xsl:value-of select="ext:EX_extendedMetadata_Type/ext:value/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
 	
Statut:
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Attribut']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Statut' ">
				<xsl:value-of select="ext:EX_extendedMetadata_Type/ext:value/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>

	 	 	 
 	 	 
Rôle*:Gestionnaire
Organisme*:<xsl:value-of select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>
Nom:<xsl:value-of select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/>
Adresse:<xsl:value-of select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString"/>
Localité:<xsl:value-of select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString"/>
Code postal:<xsl:value-of select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString"/>
Pays:<xsl:value-of select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString"/>
Téléphone:<xsl:value-of select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/>
Fax:<xsl:value-of select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString"/>
Email:<xsl:value-of select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>



Rôle*:Responsable de diffusion
Organisme*:<xsl:value-of select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>
Nom:<xsl:value-of select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/>
Adresse:<xsl:value-of select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString"/>
Localité:<xsl:value-of select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString"/>
Code postal:<xsl:value-of select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString"/>
Pays:<xsl:value-of select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString"/>
Téléphone:<xsl:value-of select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/>
Fax:<xsl:value-of select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString"/>
Email:<xsl:value-of select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>

Rôle*:Point de contact
Organisme*:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"/>
Nom:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/>
Adresse:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString"/>
Localité:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString"/>
Code postal:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString"/>
Pays:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString"/>
Téléphone:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/>
Fax:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString"/>
Email:<xsl:value-of select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>

</xsl:template>

</xsl:stylesheet>