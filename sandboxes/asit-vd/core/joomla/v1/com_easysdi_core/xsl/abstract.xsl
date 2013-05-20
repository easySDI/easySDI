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

<!-- Title of the metadata -->
<title><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString"/></title>

<div id="metadata" class="contentin">
<!-- Title of the metadata -->
<h2 class="contentheading"><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString"/></h2>

<table class="descr" id="metadataTitle">
 <tr>
 	<td rowspan="2">__ref_1$s</td>
	<td class="furnisherTitle">Fournisseur: __ref_2$s</td>
 </tr>
 <tr>
 	<td>Fiche créée le __ref_3$s, mise à jour le __ref_4$s</td>
 </tr>
</table>
<!-- The buttons links -->
__ref_5$s
<!-- The menu links -->
__ref_6$s
<!-- <h3>Identification</h3> -->
<br/>
<table class="descr">
<!--<tr valign="top"><td class="title">Id : </td> <td><xsl:value-of disable-output-escaping="yes" select="./gmd:fileIdentifier/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Nom :</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString"/></td></tr>
-->
<tr valign="top"><td class="title">Description :</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Création de la donnée:</td><td>
 <xsl:for-each select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date">
     <xsl:if test="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='creation'">
		<xsl:variable name="date_norm" select="gmd:CI_Date/gmd:date/gco:Date" />
		<xsl:variable name="year" select="substring($date_norm, 1, 4)" />
		<xsl:variable name="month" select="substring($date_norm, 6, 2)" />
		<xsl:variable name="day" select="substring($date_norm, 9, 2)" />
		<xsl:variable name="date_complete" select="concat($day, '.', $month, '.', $year)" />
		<xsl:if test="$date_complete != '..'">
			<xsl:value-of select="$date_complete" />
		</xsl:if>
		<xsl:if test="$date_complete = '..'">
			non renseignée
		</xsl:if>
     </xsl:if>
 </xsl:for-each>
</td></tr>

<tr valign="top"><td class="title">Mise à jour de la donnée:</td><td>
<xsl:for-each select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date">
     <xsl:if test="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='revision'">
		<xsl:variable name="date_norm" select="gmd:CI_Date/gmd:date/gco:Date" />
		<xsl:variable name="year" select="substring($date_norm, 1, 4)" />
		<xsl:variable name="month" select="substring($date_norm, 6, 2)" />
		<xsl:variable name="day" select="substring($date_norm, 9, 2)" />
		<xsl:variable name="date_complete" select="concat($day, '.', $month, '.', $year)" />
		<xsl:if test="$date_complete != '..'">
			<xsl:value-of select="$date_complete" />
		</xsl:if>
		<xsl:if test="$date_complete = '..'">
			non renseignée
		</xsl:if>
     </xsl:if>
 </xsl:for-each>
 (fréquence <xsl:call-template name="maintenanceFrequencyCodeTemplate">
	<xsl:with-param name="maintenanceFrequencyCode" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode/@codeListValue"/>
 </xsl:call-template>)
</td></tr>
<tr valign="top"><td class="title">Etendue géographique*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Couverture spatiale:</td><td>
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/@xlink:title"/>        
</td></tr>


<tr valign="top"><td class="title">Synoptique:</td><td>
 <xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Identification']">
 	<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Synoptique' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>
      </xsl:if>
</xsl:for-each>
</td>
</tr>
<tr valign="top"><td class="title">Extrait:</td><td><xsl:value-of disable-output-escaping="yes" select='./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileName/gco:CharacterString'/></td></tr>


<tr valign="top"><td class="title">Sous-produits:</td><td>
 <xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Identification']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Sous-produits' ">
			<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td>
</tr>

<tr valign="top"><td class="title">Thématique*:</td><td>
	<table class="descr">
	<xsl:for-each select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory">
		<tr valign="top"><td class="title">
		<xsl:call-template name="categoryCodeTemplate">
			<xsl:with-param name="categoryCode" select="gmd:MD_TopicCategoryCode"/>
		</xsl:call-template>
		</td></tr>
	</xsl:for-each>
	</table>
</td></tr>
</table>
<br/>
<table class="descr" id="contactInfo">
<tr valign="top"><td class="title">Organisme*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Nom:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Téléphone:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Email:</td><td>
<xsl:element name="a">
<xsl:attribute name="href">
mailto:<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:attribute>
<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:element>
</td></tr>
</table>
</div>
<!-- Script to open all hyperlinks in a new window -->
<script xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr-fr" lang="fr-fr" dir="ltr">
window.addEvent('domready', function() {
var container = document.getElementById("metadata");
var hlinks = container.getElementsByTagName("a");
i=0;
while(true){
	if(hlinks.length == i)
		break;
	if(!hlinks[i].href.match("mailto:"))
		hlinks[i].setAttribute('target', '_blank');
	i++;
}
});
</script>
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
	<!-- Template MaintenanceFrequencyCode -->
	<xsl:template name="maintenanceFrequencyCodeTemplate">
		<xsl:param name="maintenanceFrequencyCode"/>
			<xsl:choose>
				<xsl:when test="$maintenanceFrequencyCode = 'continual'">
				<xsl:text>continue</xsl:text>	
				</xsl:when>
				<xsl:when test="$maintenanceFrequencyCode = 'daily'">
					<xsl:text>quotidienne</xsl:text>	
				</xsl:when>		
				<xsl:when test="$maintenanceFrequencyCode = 'weekly'">
					<xsl:text>hebdomadaire</xsl:text>
				</xsl:when>
				<xsl:when test="$maintenanceFrequencyCode = 'fortnightly'">
					<xsl:text>bimensuelle</xsl:text>	
				</xsl:when>	
				<xsl:when test="$maintenanceFrequencyCode = 'monthly'">
					<xsl:text>mensuelle</xsl:text>
				</xsl:when>
				<xsl:when test="$maintenanceFrequencyCode = 'quarterly'">
					<xsl:text>trimestrielle</xsl:text>	
				</xsl:when>	
				<xsl:when test="$maintenanceFrequencyCode = 'biannually'">
					<xsl:text>semestrielle</xsl:text>
				</xsl:when>
				<xsl:when test="$maintenanceFrequencyCode = 'annually'">
					<xsl:text>annuelle</xsl:text>	
				</xsl:when>	
				<xsl:when test="$maintenanceFrequencyCode = 'asNeeded'">
					<xsl:text>au besoin</xsl:text>
				</xsl:when>
				<xsl:when test="$maintenanceFrequencyCode = 'irregular'">
					<xsl:text>irrégulière</xsl:text>	
				</xsl:when>	
				<xsl:when test="$maintenanceFrequencyCode = 'notPlanned'">
				<xsl:text>non planifiée</xsl:text>	
				</xsl:when>
				<xsl:when test="$maintenanceFrequencyCode = 'unknown'">
				<xsl:text>inconnue</xsl:text>
				</xsl:when>	
				<xsl:when test="$maintenanceFrequencyCode = 'userDefined'">
					<xsl:text>définie par l'utilisateur</xsl:text>
				</xsl:when>	
				<xsl:otherwise>
					<xsl:value-of disable-output-escaping="yes" select="$maintenanceFrequencyCode"></xsl:value-of>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>
</xsl:stylesheet>