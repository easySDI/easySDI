<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
Script XSLT de transformation EasySDI sous Joomla!
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"

xmlns:xlink="http://www.w3.org/1999/xlink"
xmlns:ext="http://www.depth.ch/2008/ext"
>

	<!-- Encodage des résultats -->
    <xsl:output encoding="utf-8"/>
    <xsl:output method="html"/>


<xsl:template match="Diffusion">


<h2 class="contentheading">Metadonnée</h2>
<div class="contentin">

<h3>Diffusion</h3>
<table >
<xsl:for-each select="./Property">
	<tr><td><xsl:value-of disable-output-escaping="yes" select="PropertyName"/> : </td><td>  
	<table >
		<xsl:for-each select="PropertyValue">
			<tr><td><xsl:value-of disable-output-escaping="yes" select="value"/></td></tr>  
		</xsl:for-each>
	</table>     
	</td></tr> 
	
</xsl:for-each>

</table>
</div>

</xsl:template>
</xsl:stylesheet>