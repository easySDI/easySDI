<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:wfs="http://www.opengis.net/wfs"
	xmlns:gml="http://www.opengis.net/gml"
	xmlns:xalan="http://xml.apache.org/xalan"
	xmlns:foo="http://www/depth.ch/foo"
>
<xsl:output method="text"/>
<xsl:strip-space elements="*"/>

<xsl:param name="attributes"/>

<foo:string_replacement>
  <foo:search>
    <foo:find>&#xE4;</foo:find>
    <foo:replace>\'E4</foo:replace>
  </foo:search>
</foo:string_replacement>

<xsl:template match="wfs:FeatureCollection">
	<xsl:variable name="attrs" select="xalan:nodeset($attributes)/*[not(starts-with(@type,'gml:'))]" />
	<xsl:text>{\rtf1\ansi\deff0 \paperw16840\paperh11907 \margl1440\margr1440\margt1797\margb1797 \fs16 </xsl:text>
	<xsl:text>\trowd\trgaph144 </xsl:text>

	<xsl:for-each select="$attrs">
		<xsl:text>\clbrdrt\brdrs\clbrdrl\brdrs\clbrdrb\brdrs\clbrdrr\brdrs \cellx</xsl:text><xsl:value-of select="position()*1000" /><xsl:text> </xsl:text>
	</xsl:for-each>
	<xsl:text>\intbl</xsl:text>
	<xsl:for-each select="$attrs">
		<xsl:text>\b </xsl:text><xsl:value-of select="./@name"/><xsl:text> \b0\cell</xsl:text>
	</xsl:for-each>
	<xsl:text>\row </xsl:text>
	<xsl:apply-templates select="gml:featureMember">
		<xsl:with-param name="attrs" select="$attrs" />
	</xsl:apply-templates>
	<xsl:text>}</xsl:text>
</xsl:template>

<xsl:template match="gml:featureMember">
<xsl:param name="attrs" />
  <xsl:text>\trowd\trgaph144 </xsl:text>
  <xsl:for-each select="$attrs">
    <xsl:text>\clbrdrt\brdrs\clbrdrl\brdrs\clbrdrb\brdrs\clbrdrr\brdrs \cellx</xsl:text><xsl:value-of select="position()*1000" /><xsl:text> </xsl:text>
  </xsl:for-each>
  <xsl:text>\intbl</xsl:text>
  <xsl:variable name="current" select="."/>
    <xsl:for-each select="$attrs">
      <xsl:variable name="name" select="./@name"/>
      <xsl:text> </xsl:text>
      <xsl:call-template name="process-chars">
        <xsl:with-param name="text" select="$current/*/*[local-name()=$name]"/>
        <xsl:with-param name="pos">1</xsl:with-param>
      </xsl:call-template>      
      <xsl:text> \cell</xsl:text>
    </xsl:for-each>
  <xsl:text>\row </xsl:text>
</xsl:template>

<xsl:template name="process-chars">
  <xsl:param name="text"/>
  <xsl:param name="pos"/>
  
  <xsl:param name="search"
      select="document('webapps\reports\WEB-INF\classes\ch\depth\reports\special-chars.xml')//string_replacement/search" />
  <xsl:variable name="replaced_text">
	  <xsl:call-template name="process-char">
		<xsl:with-param name="text" select="$text"/>
		<xsl:with-param name="char" select="$search[$pos]/find"/>
		<xsl:with-param name="replacement" select="$search[$pos]/replace"/>
	  </xsl:call-template>
  </xsl:variable>
  <xsl:choose>  
    <xsl:when test="$pos &lt; count($search)">
      <xsl:call-template name="process-chars">
        <xsl:with-param name="text" select="$replaced_text" />
        <xsl:with-param name="pos" select="$pos + 1" />
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:value-of select="$replaced_text" />
    </xsl:otherwise>
  </xsl:choose>
  
</xsl:template>

<xsl:template name="process-char">
  <xsl:param name="text"/>
  <xsl:param name="char"/>
  <xsl:param name="replacement"/>
  <xsl:choose>
    <xsl:when test="contains($text, $char)">		
      <xsl:value-of select="substring-before($text, $char)"/>      	  
      <xsl:value-of select="$replacement"/>	  
	  <xsl:call-template name="process-char">
        <xsl:with-param name="text" select="substring-after($text, $char)"/>
        <xsl:with-param name="char" select="$char"/>
        <xsl:with-param name="replacement" select="$replacement"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>      	
      <xsl:value-of select="$text"/>      
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

</xsl:stylesheet>