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

 
<xsl:template match='gmd:graphicOverview[1]'>
	<tr valign="top"><td>Synoptique:</td><td>
   <xsl:value-of disable-output-escaping="yes" select='gmd:MD_BrowseGraphic/gmd:fileDescription/gmd:LocalisedCharacterString'/>
   </td></tr>
</xsl:template>

<xsl:template match='gmd:graphicOverview[2]'>
   <tr valign="top"><td>Extrait:</td><td>
     <xsl:value-of disable-output-escaping="yes" select='gmd:MD_BrowseGraphic/gmd:fileDescription/gmd:LocalisedCharacterString'/>
   </td></tr>
</xsl:template>

<xsl:template match="gmd:MD_Metadata">


<h2 class="contentheading">Metadonnée</h2>
<div class="contentin">

<h3>Identification</h3>
<table  >
<tr valign="top"><td>Id : </td> <td><xsl:value-of disable-output-escaping="yes" select="./gmd:fileIdentifier/gco:CharacterString"/></td></tr>
<tr valign="top"><td>Nom :</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:LocalisedCharacterString"/></td></tr>
<tr valign="top"><td>Description :</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:LocalisedCharacterString"/></td></tr>

<tr valign="top"><td>Etendue géographique*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description/gmd:LocalisedCharacterString"/></td></tr>
<tr valign="top"><td>Couverture spatiale:</td><td>
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/@xlink:title"/>        
</td></tr>

<tr valign="top"><td>Synoptique:</td><td><xsl:value-of disable-output-escaping="yes" select='./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileDescription/gmd:LocalisedCharacterString'/></td></tr>
<tr valign="top"><td>Extrait:</td><td>
 <xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Identification']">
 	<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Extrait' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>
      </xsl:if>
</xsl:for-each>
</td>
</tr>

<tr valign="top"><td>Sous-produits:</td><td>
 <xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Identification']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Sous-produits' ">
			<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gmd:LocalisedCharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td></tr>
<tr valign="top"><td>Thématique*:</td><td>
	<table>
	<xsl:for-each select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory">
		<tr valign="top"><td>
		<xsl:call-template name="categoryCodeTemplate">
			<xsl:with-param name="categoryCode" select="gmd:MD_TopicCategoryCode"/>
		</xsl:call-template>
		</td></tr>
	</xsl:for-each>
	</table>
</td></tr>
</table>


<hr></hr>
<h3>Point de contact</h3>
<table  >
<tr valign="top"><td>Organisme*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:LocalisedCharacterString"/></td></tr>
<tr valign="top"><td>Nom:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/></td></tr>
<tr valign="top"><td>Téléphone:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/></td></tr>
<tr valign="top"><td>Email:</td><td>
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

<!-- Template CategoryCode -->
	<xsl:template name="categoryCodeTemplate">
		<xsl:param name="categoryCode"/>
			<xsl:choose>
				<xsl:when test="$categoryCode = 'farming'">
					<xsl:text>Agriculture</xsl:text>
				</xsl:when>
				<xsl:when test="$categoryCode = 'biota'">
					<xsl:text>Biologie</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'bounderies'">
					<xsl:text>Limites</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'climatologyMeteorologyAtmosphere'">
					<xsl:text>Climatologie/météorologie</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'economy'">
					<xsl:text>Economie</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'elevation'">
					<xsl:text>Altimétrie</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'environment'">
					<xsl:text>Environnement</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'geoscientificinformation'">
					<xsl:text>Sciences de la Terre</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'health'">
					<xsl:text>Santé</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'imageryBaseMapsEarthCover'">
					<xsl:text> 	Cartes de base, imagerie</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'intelligenceMilitary'">
					<xsl:text>Activités militaires</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'inlandWaters'">
					<xsl:text>Eaux intérieures</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'location'">
					<xsl:text>Localisation</xsl:text>	
				</xsl:when>			
				<xsl:when test="$categoryCode = 'oceans'">
					<xsl:text>Océans</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'planningCadastre'">
					<xsl:text>Cadastre, aménagement</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'society'">
					<xsl:text>Société</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'structure'">
					<xsl:text>Constructions et ouvrages</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'transportation'">
					<xsl:text>Transport</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'utilitiesCommunication'">
					<xsl:text>Réseau de dsitribution et d'évacuation</xsl:text>	
				</xsl:when>	
				<xsl:otherwise>
					<xsl:text>Inconnu</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>
</xsl:stylesheet>