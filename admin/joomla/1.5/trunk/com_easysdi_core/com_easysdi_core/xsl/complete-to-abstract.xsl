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
<xsl:output method="xml" encoding="utf-8" indent="yes"/>

	<xsl:template match="/">
		<xsl:apply-templates />
	</xsl:template>
	
	<xsl:template match="gmd:MD_Metadata">
		<xsl:copy>
			<xsl:apply-templates />
		</xsl:copy>
	</xsl:template>
	
	<xsl:template match="gmd:MD_Metadata/gmd:identificationInfo">
		<xsl:copy>
			<xsl:apply-templates />
		</xsl:copy>
	</xsl:template>
	
	<xsl:template match="gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification">
		<xsl:copy>
			<xsl:apply-templates />
		</xsl:copy>
	</xsl:template>
	
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract">
		<xsl:copy-of select="." />
	</xsl:template>
	
	
	<xsl:template match="gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent">
		<xsl:copy>
			<xsl:value-of select="@xlink:title" />
			<xsl:apply-templates />
		</xsl:copy>
	</xsl:template>
	
	<xsl:template match="gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent">
		<xsl:copy>
			<xsl:apply-templates />
		</xsl:copy>
	</xsl:template>
	
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description">
		<xsl:copy-of select="." />
	</xsl:template>
	
	<xsl:template match="gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview">
		<xsl:copy>
			<xsl:apply-templates />
		</xsl:copy>
	</xsl:template>
	
	<xsl:template match="gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic">
		<xsl:copy>
			<xsl:apply-templates />
		</xsl:copy>
	</xsl:template>
	
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileName">
		<xsl:copy-of select="." />
	</xsl:template>
	
	<xsl:template match="gmd:identificationInfo/gmd:pointOfContact">
		<xsl:copy-of select="." />
	</xsl:template>
	
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory">
		<xsl:copy-of select="." />
	</xsl:template>
	
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation">
		<xsl:copy-of select="." />
	</xsl:template>
	
	
	<!-- Enlever toutes les balises de haut niveau qui ne contiennent pas des données qui nous intéressent -->
	<xsl:template match="gmd:fileIdentifier">
	</xsl:template>
	<xsl:template match="gmd:language">
	</xsl:template>
	<xsl:template match="gmd:metadataExtensionInfo">
	</xsl:template>
	<xsl:template match="gmd:distributionInfo">
	</xsl:template>
	<xsl:template match="gmd:dataQualityInfo">
	</xsl:template>
	<xsl:template match="gmd:dateStamp">
	</xsl:template>
	<xsl:template match="gmd:metadataStandardName">
	</xsl:template>
	<xsl:template match="gmd:extendedMetadata">
	</xsl:template>
	<xsl:template match="gmd:contact">
	</xsl:template>
	
	<!-- Enlever toutes les balises de "gmd:identificationInfo" qui ne contiennent pas des données qui nous intéressent -->
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance">
	</xsl:template>
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints">
	</xsl:template>
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement">
	</xsl:template>
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileDescription">
	</xsl:template>
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileType">
	</xsl:template>
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileType">
	</xsl:template>
	<xsl:template match="gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:referenceSystemIdentifier/gmd:RS_Identifier/gmd:code">
	</xsl:template>
	
	<!-- 
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:LocalisedCharacterString"/>
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description/gmd:LocalisedCharacterString"/>
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/@xlink:title"/>
	<xsl:value-of disable-output-escaping="yes" select='./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileName/gmd:CharacterString'/>
	
	
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:LocalisedCharacterString"/>
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/>
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/>
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
	
	<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>
	 -->
</xsl:stylesheet>