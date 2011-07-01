<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:wfs="http://www.opengis.net/wfs" xmlns:gml="http://www.opengis.net/gml"
	xmlns:xalan="http://xml.apache.org/xalan" xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<xsl:strip-space elements="*" />

	<xsl:param name="attributes" />
	<xsl:param name="image" />

	<xsl:template match="wfs:FeatureCollection">
		<xsl:variable name="attrs" select="xalan:nodeset($attributes)/*[not(starts-with(@type,'gml:'))]" />
		<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format" font-size="8pt">
			<fo:layout-master-set>
				<fo:simple-page-master master-name="A4-landscape" page-height="21cm" page-width="29.7cm">
					<fo:region-body margin="1in" />
				</fo:simple-page-master>
			</fo:layout-master-set>

			<fo:page-sequence master-reference="A4-landscape">
				<fo:flow flow-name="xsl-region-body">
					<fo:block>
						<fo:external-graphic>
							<xsl:attribute name="src">
				<xsl:value-of select="$image" />
			</xsl:attribute>
						</fo:external-graphic>
					</fo:block>
					<fo:block break-before="page">
						<fo:table>
							<xsl:for-each select="$attrs">
								<fo:table-column column-width="30mm" />
							</xsl:for-each>
							<fo:table-header>
								<xsl:for-each select="$attrs">
									<fo:table-cell>
										<fo:block font-weight="bold" background-color="#EEEEEE" padding-before="6pt" padding-after="6pt">
											<xsl:value-of select="./@name" />
										</fo:block>
									</fo:table-cell>
								</xsl:for-each>
							</fo:table-header>
							<fo:table-body>
								<xsl:apply-templates select="gml:featureMember">
									<xsl:with-param name="attrs" select="$attrs" />
								</xsl:apply-templates>
							</fo:table-body>
						</fo:table>
					</fo:block>
				</fo:flow>
			</fo:page-sequence>
		</fo:root>
	</xsl:template>

	<xsl:template match="gml:featureMember">
		<xsl:param name="attrs" />
		<fo:table-row>
			<xsl:variable name="current" select="." />
			<xsl:for-each select="$attrs">
				<xsl:variable name="name" select="./@name" />
				<fo:table-cell>
					<fo:block>
						<xsl:value-of select="$current/*/*[local-name()=$name]" />
					</fo:block>
				</fo:table-cell>
			</xsl:for-each>
		</fo:table-row>
	</xsl:template>
</xsl:stylesheet>