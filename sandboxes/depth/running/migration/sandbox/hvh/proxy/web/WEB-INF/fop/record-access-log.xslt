<?xml version="1.0"?>

<xsl:stylesheet version="2.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:wfs="http://www.opengis.net/wfs"
	xmlns:gml="http://www.opengis.net/gml"
	xmlns:xalan="http://xml.apache.org/xalan">
<xsl:output method="xml" omit-xml-declaration="yes"/>
<xsl:strip-space elements="*"/>

<xsl:param name="type"/>
<xsl:param name="spatial"/>
<xsl:param name="timestamp"/>
<xsl:param name="filters"/>
<xsl:param name="userName"/>

<xsl:template match="wfs:FeatureCollection">
<log><report>
	<type><xsl:value-of select="$type" /></type>
	<spatial><xsl:value-of select="$spatial" /></spatial>
	<datetime><xsl:value-of select="$timestamp" /></datetime>
	<filters><xsl:value-of select="$filters" /></filters>
	<username><xsl:value-of select="$userName" /></username>
	<records>
		<xsl:apply-templates select="gml:featureMember" />
	</records>
</report></log>
</xsl:template>

<xsl:template match="gml:featureMember">
  <id><xsl:value-of select="*/*[local-name()='unit_guid']" /></id>  
</xsl:template>

</xsl:stylesheet>