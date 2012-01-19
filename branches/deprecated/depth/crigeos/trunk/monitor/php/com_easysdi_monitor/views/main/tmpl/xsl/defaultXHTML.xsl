<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="iso-8859-1"/>
    <xsl:strip-space elements="*" />
    <xsl:template match="/">
        <html>
        <body>
        <table border="1">
        <tr>
        <xsl:for-each select="json/data[1]/*">
            <th><xsl:value-of select="name()" /></th>
        </xsl:for-each>  
        </tr>
        <xsl:apply-templates select="/" mode="data"/>
        </table>
        </body>
        </html>
    </xsl:template> 

    <xsl:template match="/" mode="data">
        <xsl:for-each select="json/data">
            <tr>
            <xsl:for-each select="child::*">
                <td><xsl:value-of select="normalize-space(.)"/></td>
            </xsl:for-each>
            </tr>
        </xsl:for-each>
    </xsl:template>

</xsl:stylesheet>
