<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
Script XSLT de transformation EasySDI sous Joomla!
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


<xsl:template match="Diffusion">

<div id="metadata" class="contentin">
<!-- Title of the metadata -->
<h2 class="contentheading"><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:LocalisedCharacterString"/></h2>

<table class="descr" id="metadataTitle">
 <tr>
 	<td rowspan="2">__ref_1$s</td>
	<td>Fournisseur: __ref_2$s</td>
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
<hr/>

<table class="descr">
<xsl:for-each select="./Property">
	<tr><td class="title" valign="top"><xsl:value-of disable-output-escaping="yes" select="PropertyName"/> : </td><td>  
	<table >
		<xsl:for-each select="PropertyValue">
			<tr><td><xsl:value-of disable-output-escaping="yes" select="value"/></td></tr>  
		</xsl:for-each>
	</table>     
	</td></tr> 
	
</xsl:for-each>

</table>
</div>

<!-- Script to open all hyperlinks in a new window -->
<script>

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
</script>

</xsl:template>
</xsl:stylesheet>