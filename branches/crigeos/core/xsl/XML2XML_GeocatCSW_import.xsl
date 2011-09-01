<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
Script XSLT de transformation des partenaires ASIT-VD sous Joomla!
Le r�sultat est fourni sous forme de fichier CSV avec une tabulation comme s�parateur
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:gmd="http://www.isotc211.org/2005/gmd" 
xmlns:gco="http://www.isotc211.org/2005/gco"
xmlns:xlink="http://www.w3.org/1999/xlink"
xmlns:ext="http://www.depth.ch/2008/ext"
xmlns:che="http://www.geocat.ch/2008/che"
>
<!-- Encodage des r�sultats -->
<xsl:output method="xml" encoding="utf-8" indent="yes"/>
	<xsl:template match="che:CHE_MD_Metadata">
		<!--<xsl:value-of select="@gco:isoType" />-->
		<gmd:MD_Metadata>
			<xsl:copy-of select="node()" />
		</gmd:MD_Metadata>
	</xsl:template>
</xsl:stylesheet>