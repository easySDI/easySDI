<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:gmd="http://www.isotc211.org/2005/gmd" 
xmlns:csw="http://www.opengis.net/cat/csw" 
xmlns:gco="http://www.isotc211.org/2005/gco" 
xmlns:ns3="http://www.isotc211.org/2005/gmx" 
xmlns:xlink="http://www.w3.org/1999/xlink" 
xmlns:gml="http://www.opengis.net/gml" 
xmlns:gts="http://www.isotc211.org/2005/gts" 
xmlns:ext="http://www.depth.ch/2008/ext" 
xmlns:srv="http://www.isotc211.org/2005/srv">

<xsl:output indent="yes" method="xml" encoding="UTF-8"/>
	<xsl:template match="*">
		<xsl:copy>
			<xsl:apply-templates/>
		</xsl:copy>
	</xsl:template>
	
	<xsl:template match="csw:SearchResults">
		<xsl:for-each select="gmd:MD_Metadata">
			<xsl:sort select="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:LocalisedCharacterString" case-order="lower-first"/>
			<xsl:copy-of select="."/>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>