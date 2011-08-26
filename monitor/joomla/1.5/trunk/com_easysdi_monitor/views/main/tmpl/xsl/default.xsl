<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text" encoding="iso-8859-1"/>

<xsl:strip-space elements="*" />

 <xsl:template match="/"> 
 <xsl:for-each select="json/data[1]/*">    
 <xsl:value-of select="name()" />  
 <xsl:if test="not(position() = last())">;</xsl:if>   
 </xsl:for-each>  
 <xsl:text>&#10;</xsl:text>
 <xsl:apply-templates select="/" mode="data"/> 
 </xsl:template> 
 
<xsl:template match="/" mode="data">
<xsl:for-each select="json/data">
<xsl:for-each select="child::*">
<xsl:if test="position() != last()">"<xsl:value-of select="normalize-space(.)"/>"; </xsl:if>
<xsl:if test="position()  = last()"><xsl:value-of select="normalize-space(.)"/><xsl:text>&#xD;</xsl:text>
</xsl:if>
</xsl:for-each>
</xsl:for-each>
</xsl:template>

</xsl:stylesheet>
