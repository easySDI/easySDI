<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text" encoding="iso-8859-1"/>

<xsl:strip-space elements="*" />
 
 <xsl:template match="/"> 
 <xsl:for-each select="json/data[1]/*">  
 <xsl:choose>
  <xsl:when test="name() = 'jobname'">
	<xsl:text>Job name</xsl:text>
 </xsl:when>
 <xsl:when test="name() = 'slaname'">
	<xsl:text>SLA name</xsl:text>
 </xsl:when>
  <xsl:when test="name() = 'time'">
	<xsl:text>Time</xsl:text>
 </xsl:when>
 <xsl:when test="name() = 'requestname'">
	<xsl:text>Request name</xsl:text>
 </xsl:when>
 <xsl:when test="name() = 'date'">
	<xsl:text>Date</xsl:text>
 </xsl:when>
 <xsl:when test="name() = 'sizebytes'">
	<xsl:text>Size(bytes)</xsl:text>
 </xsl:when>
  <xsl:when test="name() = 'responsetime'">
	<xsl:text>Response time(ms)</xsl:text>
 </xsl:when>
  <xsl:when test="name() = 'availablecount'">
	<xsl:text>Available count</xsl:text>
 </xsl:when>
 <xsl:when test="name() = 'unavailablecount'">
	<xsl:text>Uavailable count</xsl:text>
 </xsl:when>
  <xsl:when test="name() = 'failurecount'">
	<xsl:text>Failure count</xsl:text>
 </xsl:when>
 <xsl:when test="name() = 'untestedcount'">
	<xsl:text>Untested count</xsl:text>
 </xsl:when>
 <xsl:when test="name() = 'maxresponsetime'">
	<xsl:text>Max response time(ms)</xsl:text>
 </xsl:when>
  <xsl:when test="name() = 'maxresponsetime'">
	<xsl:text>Max responsetime(ms)</xsl:text>
 </xsl:when>
  <xsl:when test="name() = 'minresponsetime'">
	<xsl:text>Min responsetime(ms)</xsl:text>
 </xsl:when>
  <xsl:when test="name() = 'meanresponsetime'">
	<xsl:text>Avarage responsetime(ms)</xsl:text>
 </xsl:when>
   <xsl:when test="name() = 'availability'">
	<xsl:text>Available</xsl:text>
 </xsl:when>
   <xsl:when test="name() = 'unavailability'">
	<xsl:text>Unavailable</xsl:text>
 </xsl:when>
 <xsl:when test="name() = 'failure'">
   <xsl:text>Failure</xsl:text>
 </xsl:when>
 <xsl:when test="name() = 'untested'">
	<xsl:text>Untested</xsl:text>
 </xsl:when>
 <xsl:otherwise>
	<xsl:value-of select="name()" />
  </xsl:otherwise>
</xsl:choose>
 <xsl:if test="not(position() = last())">;</xsl:if>   
 </xsl:for-each>  
 <xsl:text>&#10;</xsl:text>
 <xsl:apply-templates select="/" mode="data"/> 
 </xsl:template> 
 
<xsl:template match="/" mode="data">
<xsl:for-each select="json/data">
<xsl:for-each select="child::*">
<xsl:choose>
 <xsl:when test="name() = 'responsetime'">
	<xsl:if test="position() != last()"><xsl:value-of select="round(normalize-space(.) * 1000)"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'maxresponsetime'">
	<xsl:if test="position() != last()"><xsl:value-of select="round(normalize-space(.) * 1000)"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'minresponsetime'">
	<xsl:if test="position() != last()"><xsl:value-of select="round(normalize-space(.) * 1000)"/>;</xsl:if>
 </xsl:when> 
 <xsl:when test="name() = 'meanresponsetime'">
	<xsl:if test="position() != last()"><xsl:value-of select="round(normalize-space(.) * 1000)"/>;</xsl:if>
 </xsl:when>
  <xsl:when test="name() = 'slamaxresponsetime'">
	<xsl:if test="position() != last()"><xsl:value-of select="round(normalize-space(.) * 1000)"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'slaminresponsetime'">
	<xsl:if test="position() != last()"><xsl:value-of select="round(normalize-space(.) * 1000)"/>;</xsl:if>
 </xsl:when>
  <xsl:when test="name() = 'slameanresponsetime'">
	<xsl:if test="position() != last()"><xsl:value-of select="round(normalize-space(.) * 1000)"/>;</xsl:if>
 </xsl:when>
   <xsl:when test="name() = 'h24maxresponsetime'">
	<xsl:if test="position() != last()"><xsl:value-of select="round(normalize-space(.) * 1000)"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'h24minresponsetime'">
	<xsl:if test="position() != last()"><xsl:value-of select="round(normalize-space(.) * 1000)"/>;</xsl:if>
 </xsl:when>
  <xsl:when test="name() = 'h24meanresponsetime'">
	<xsl:if test="position() != last()"><xsl:value-of select="round(normalize-space(.) * 1000)"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'availability'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'unavailability'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'failure'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'untested'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>
	 </xsl:when>
 <xsl:when test="name() = 'slaavailability'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'slaunavailability'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'slafailure'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'slauntested'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>	
	</xsl:when>
<xsl:when test="name() = 'h24availability'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'h24unavailability'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'h24failure'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>
 </xsl:when>
 <xsl:when test="name() = 'h24untested'">
	<xsl:if test="position() != last()"><xsl:value-of select="floor(normalize-space(.) * 100.00) div 100.00"/>;</xsl:if>	
</xsl:when>	

 <xsl:otherwise>
	<xsl:if test="position() != last()"><xsl:value-of select="normalize-space(.)"/>;</xsl:if>
  </xsl:otherwise>
</xsl:choose>
<xsl:if test="position() = last() ">
<xsl:value-of select="normalize-space(.)"/><xsl:text>&#xD;</xsl:text></xsl:if>
</xsl:for-each>
</xsl:for-each>
</xsl:template>

</xsl:stylesheet>


