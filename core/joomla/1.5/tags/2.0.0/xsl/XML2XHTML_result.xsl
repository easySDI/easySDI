<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
Script XSLT de transformation des partenaires ASIT-VD sous Joomla!
Le résultat est fourni sous forme de fichier CSV avec une tabulation comme séparateur
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:gmd="http://www.isotc211.org/2005/gmd" 
xmlns:sdi="http://www.depth.ch/sdi"
xmlns:gco="http://www.isotc211.org/2005/gco"
xmlns:xlink="http://www.w3.org/1999/xlink"
xmlns:ext="http://www.depth.ch/2008/ext"
xmlns:date="http://exslt.org/dates-and-times" extension-element-prefixes="date"
>

	<!-- Encodage des résultats -->
    <xsl:output encoding="utf-8"/>
    <xsl:output method="html"/>

<xsl:template match="Metadata">
<div class="ticker">
<div class="row">
  
  <div class="metadata-result">
  <div class="metadata-logo"><h4 class="hidden"><xsl:value-of select="./sdi:Metadata/sdi:objecttype" /></h4>
  	<xsl:variable name="logo"><xsl:value-of select="./sdi:Metadata/sdi:objecttype/@code" /></xsl:variable>
  	<div>
  	<xsl:choose>
  		
		<xsl:when test="$logo ='layer'">
			<xsl:attribute name="class">metadata-logo-1</xsl:attribute>
		</xsl:when>
		<xsl:when test="$logo ='map'">
			<xsl:attribute name="class">metadata-logo-2</xsl:attribute>
		</xsl:when>
		<xsl:when test="$logo ='geoproduct'">
			<xsl:attribute name="class">metadata-logo-3</xsl:attribute>
		</xsl:when>
		
		<xsl:otherwise>
			<xsl:attribute name="class">metadata-logo-0</xsl:attribute>
		</xsl:otherwise>
	</xsl:choose>
  	
  	 </div>
  </div>
  <div class="metadata-desc">
  		<h3><xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString" /></h3>
  	 	<p><xsl:value-of select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString" /></p>
  	 	<div class="metadata-summary">
  	 		<span class="metadata-label">Zeitstand:</span>
         <span class="metada-value"><strong>
  	 		<xsl:value-of select="date:day-in-month(./sdi:Metadata/sdi:object/@objectversion_title)"/><xsl:text>.</xsl:text>
				<xsl:value-of select="date:month-in-year(./sdi:Metadata/sdi:object/@objectversion_title)"/><xsl:text>.</xsl:text>
				<xsl:value-of select="date:year(./sdi:Metadata/sdi:object/@objectversion_title)"/>
				</strong></span>
				<div class="clear"></div>
				<span class="metadata-label">Zugangsberechtigung:</span>
         <span class="metada-value"><strong><xsl:value-of select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_Constraints/gmd:useLimitation/gco:CharacterString" /></strong></span>
         <div class="clear"></div>
				<span class="metadata-label">Code:</span>
        <span class="metadata-value"><strong><xsl:value-of select="./sdi:Metadata/sdi:object/@object_name" /></strong></span>
        <div class="clear"></div>
			

  	 	</div>
  </div>
  <div class="clear"></div>	
  </div>
  </div>
</div>
</xsl:template>


</xsl:stylesheet>


