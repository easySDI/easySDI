<!--
   Merging two XML files
   Version 1.6
   LGPL (c) Oliver Becker, 2002-07-05
   obecker@informatik.hu-berlin.de
   
   UPDATED BY EASYSDI, 2010-11
-->
<xslt:transform xmlns:xslt="http://www.w3.org/1999/XSL/Transform" version="1.0"  
				xmlns:m="http://informatik.hu-berlin.de/merge" exclude-result-prefixes="m">

<xslt:output method="xml"/>
<xslt:template match="m:merge">
	<xslt:variable name="file1" select="string(m:file1)" />
	<xslt:variable name="file2" select="string(m:file2)" />
	<!-- <xslt:value-of select="$file1"/> -->
	<xslt:for-each select="document($file1,/*)/node()">
		<xslt:value-of select="."/>
	</xslt:for-each>
</xslt:template>
</xslt:transform>
