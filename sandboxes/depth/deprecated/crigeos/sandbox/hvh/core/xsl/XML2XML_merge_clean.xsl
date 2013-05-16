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

	<xsl:template match="@*|node()">
		<xsl:copy>
			<xsl:apply-templates select="@*|node()"/>
		</xsl:copy>
	</xsl:template>
	
	<!-- Enlever toutes les balises qui sont potentiellement dans la métadonnée ESRI -->
	<xsl:template match="gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/gmd:class"/>
	<xsl:template match="gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent"/>
</xsl:stylesheet>
