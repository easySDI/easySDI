<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:wfs="http://www.opengis.net/wfs"
	xmlns:gml="http://www.opengis.net/gml"
	xmlns:xalan="http://xml.apache.org/xalan">
<xsl:output method="text"/>
<xsl:strip-space elements="*"/>

<xsl:param name="attributes"/>

<xsl:template match="wfs:FeatureCollection">
	<xsl:variable name="attrs" select="xalan:nodeset($attributes)/*[not(starts-with(@type,'gml:'))]" />
	<xsl:for-each select="$attrs">
        <xsl:value-of select="./@name"/><xsl:if test="position() != last()"><xsl:text>,</xsl:text></xsl:if>
    </xsl:for-each>
    <xsl:text>&#10;</xsl:text>
    <xsl:apply-templates select="gml:featureMember">
      <xsl:with-param name="attrs" select="$attrs" />
    </xsl:apply-templates>
</xsl:template>

<xsl:template match="gml:featureMember">
<xsl:param name="attrs" />
<xsl:variable name="current" select="."/>
  <xsl:for-each select="$attrs">
    <xsl:variable name="name" select="./@name"/>
      <xsl:call-template name="display_csv_field">
        <xsl:with-param name="field" select="$current/*/*[local-name()=$name]"/>
	  </xsl:call-template>
    <xsl:if test="position() != last()"><xsl:text>,</xsl:text></xsl:if>
  </xsl:for-each>
<xsl:text>&#10;</xsl:text>
</xsl:template>

<!-- Template to escape csv field -->
<xsl:template name="display_csv_field">
  <xsl:param name="field"/>
  <xsl:variable name="linefeed">
    <xsl:text>&#10;</xsl:text>
  </xsl:variable>

  <xsl:choose>
    <xsl:when test="contains( $field, '&quot;' )">
      <!-- Field contains a quote. We must enclose this field in quotes,
        and we must escape each of the quotes in the field value. -->
      <xsl:text>"</xsl:text>
      <xsl:call-template name="escape_quotes">
        <xsl:with-param name="string" select="$field" />
      </xsl:call-template>
      <xsl:text>"</xsl:text>
    </xsl:when>
    <xsl:when test="contains( $field, ',' ) or contains( $field, $linefeed )">
      <!-- Field contains a comma and/or a linefeed.
         We must enclose this field in quotes. -->
      <xsl:text>"</xsl:text>
      <xsl:value-of select="$field" />
      <xsl:text>"</xsl:text>
    </xsl:when>

    <xsl:otherwise>
      <!-- No need to enclose this field in quotes. -->
      <xsl:value-of select="$field" />
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

 <!-- Helper for escaping CSV field -->
<xsl:template name="escape_quotes">
  <xsl:param name="string" />

  <xsl:value-of select="substring-before( $string, '&quot;' )" />
  <xsl:text>""</xsl:text>

  <xsl:variable name="substring_after_first_quote" select="substring-after( $string, '&quot;' )" />

  <xsl:choose>
    <xsl:when test="not( contains( $substring_after_first_quote, '&quot;' ) )">
      <xsl:value-of select="$substring_after_first_quote" />
    </xsl:when>
	<xsl:otherwise>
	  <!-- The substring after the first quote contains a quote.
	     So, we call ourself recursively to escape the quotes
	     in the substring after the first quote. -->
	  <xsl:call-template name="escape_quotes">
	    <xsl:with-param name="string" select="$substring_after_first_quote"/>
	  </xsl:call-template>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

</xsl:stylesheet>