<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
Script XSLT de transformation EasySDI sous Joomla!
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:gmd="http://www.isotc211.org/2005/gmd" 
xmlns:gco="http://www.isotc211.org/2005/gco"
xmlns:xlink="http://www.w3.org/1999/xlink"
xmlns:ext="http://www.depth.ch/2008/ext"
xmlns:sdi="http://www.depth.ch/sdi"
>

	<!-- Encodage des r�sultats -->
    <xsl:output encoding="utf-8"/>
    <xsl:output method="html"/>

     <!-- Tree containing links and actions upon the object -->
     <xsl:template match="sdi:Metadata">
     </xsl:template>
     
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
     <td>
        <script>
	var cDate = Date.parse("__ref_3$s");
	var uDate = Date.parse("__ref_4$s");
	var fcDate = cDate == null ? "-" : cDate.toString("dd.MM.yyyy � HH:mm:ss");
	var fuDate = uDate == null ? "-" : uDate.toString("dd.MM.yyyy � HH:mm:ss");
	document.write("Fiche cr��e le "+fcDate+", mise � jour le "+fuDate);
	</script>
     </td>
 </tr>
</table>
<!-- The buttons links -->
__ref_5$s
<!-- The menu links -->
__ref_6$s
<!-- <h3>Identification</h3> -->
<br/>




<table class="descr">

<xsl:for-each select="./Properties">
   <xsl:if test="@isProductPublished=0">
        <tr>
   	  <td class="title" valign="top" colspan="2">Ce produit n'est pas publi�.</td>
   	</tr>
   </xsl:if>
</xsl:for-each>

<xsl:for-each select="./Properties">
   <xsl:if test="@count=0">
        <tr>
   	  <td class="title" valign="top" colspan="2">Ce produit n'a pas de propri�t�s de diffusion.</td>
   	</tr>
   </xsl:if>
</xsl:for-each>

<xsl:for-each select="./Properties/Property">
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