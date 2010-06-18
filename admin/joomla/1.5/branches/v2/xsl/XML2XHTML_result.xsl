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

<div id="searchResult" class="contentin">
<!-- Title of the metadata -->
<h2 class="contentheading"><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:LocalisedCharacterString"/></h2>

<table class="searchResult_metadata" id="searchResult_metadata">
 <tr>
 	 <td valign="top" rowspan="3">
	    <img width="30px" height="30px" src="logo" alt="LOGO"></img>
	 </td>
	 <td colspan="3">
	 	<span class="mdtitle">DataTitle</span>
	 </td>
	 <td valign="top" rowspan="2">
	    <table id="info_md">
		  <tr>
		     <td><div class="publicMd"></div></td>
		  </tr>
		  <tr>
		     <td><div class="easysdi_product_exists"></div></td>
		  </tr>
		  <tr>
		     <td><div class="freeMd"></div></td>
		  </tr>
		</table>
	  </td>
 </tr>
</table>
</div>
	</xsl:template>
</xsl:stylesheet>