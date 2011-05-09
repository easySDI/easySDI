<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:gmd="http://www.isotc211.org/2005/gmd"
	xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:xlink="http://www.w3.org/1999/xlink"
	xmlns:ext="http://www.depth.ch/2008/ext" xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:fox="http://xml.apache.org/fop/extensions">

	<xsl:output method="html" encoding="utf-8" indent="yes" />

	<xsl:param name="language"></xsl:param>
	<xsl:param name="format"></xsl:param>
	<xsl:param name="reporttype"></xsl:param>
	<xsl:variable name="method"></xsl:variable>

	<!-- Choix du type de retour -->
	<xsl:template match="*">
		<xsl:choose>
			<xsl:when test="$reporttype='Bern'">
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="$language='german'">
			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="$format='xml'">
				<xsl:copy-of select="*"></xsl:copy-of>
			</xsl:when>
			<xsl:when test="$format='pdf'">
				<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">
					<fo:layout-master-set>
						<fo:simple-page-master page-height="11in"
							page-width="8.5in" master-name="only">
							<fo:region-body region-name="xsl-region-body"
								margin="0.7in" />
							<fo:region-before region-name="xsl-region-before"
								extent="0.7in" />
							<fo:region-after region-name="xsl-region-after"
								extent="0.7in" />
						</fo:simple-page-master>
					</fo:layout-master-set>

					<fo:page-sequence master-reference="only" format="A">
						<fo:flow flow-name="xsl-region-body">
							<fo:block>
								Some base content, containing an inline warning,
								<fo:inline>Warning: </fo:inline>
								Do not touch blue paper,
								a fairly straightforward piece requiring emphasis
								<fo:inline font-weight="bold">TEXT</fo:inline>
								, and
								some instructions which require presenting in a different
								way, such as
								<fo:inline font-style="italic">Now light
									the blue paper</fo:inline>
								.
							</fo:block>
						</fo:flow>
					</fo:page-sequence>
				</fo:root>

			</xsl:when>
			<xsl:otherwise>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
