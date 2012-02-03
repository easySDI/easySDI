<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:wfs="http://www.opengis.net/wfs" xmlns:gml="http://www.opengis.net/gml"
	xmlns:xalan="http://xml.apache.org/xalan" xmlns:fo="http://www.w3.org/1999/XSL/Format">

	<xsl:param name="file" select="/features/@file" />
	<xsl:param name="image" select="/features/@image" />
	<xsl:param name="title" select="/features/@title" />

	<xsl:param name="showMap" select="1" />
	<xsl:param name="showLegend" select="1" />
	<xsl:param name="showList" select="1" />

	<xsl:template match="/">
		<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format" font-size="8pt">
			<fo:layout-master-set>
				<fo:simple-page-master master-name="A4-landscape" page-height="21cm" page-width="29.7cm">
					<fo:region-body margin="1in" />
				</fo:simple-page-master>
			</fo:layout-master-set>

			<fo:page-sequence master-reference="A4-landscape">
				<fo:flow flow-name="xsl-region-body">
					<fo:block font-family="Times Roman" font-weight="bold" font-size="14pt" font-style="italic" space-after="0.5em">
						<xsl:value-of select="$title" />
					</fo:block>
					<xsl:if test="$showMap = 1">
						<fo:block margin-bottom="1cm" width="100%">
							<fo:table table-layout="fixed" width="100%">
								<fo:table-column column-width="18cm" />
								<xsl:if test="$showLegend = 1">
									<fo:table-column column-width="8cm" />
								</xsl:if>
								<fo:table-body>
									<fo:table-row>
										<fo:table-cell display-align="center">
											<fo:block border-top-style="solid" border-left-style="solid" border-right-style="solid" border-bottom-style="solid">
												<fo:external-graphic content-height="scale-to-fit" content-width="scale-to-fit" scaling="uniform" width="100%">
													<xsl:attribute name="src">
								<xsl:value-of select="$image" />
							</xsl:attribute>
												</fo:external-graphic>
											</fo:block>
										</fo:table-cell>
										<xsl:if test="$showLegend = 1">
											<xsl:if test="//feature">
												<fo:table-cell>
													<fo:list-block>
														<xsl:for-each select="//feature">
															<fo:list-item>
																<fo:list-item-label>
																	<fo:block margin-top="5mm" margin-left="5mm">
																		<fo:external-graphic>
																			<xsl:attribute name="src">
												<xsl:value-of select="@legend-url" />
											</xsl:attribute>
																		</fo:external-graphic>
																	</fo:block>
																</fo:list-item-label>
																<fo:list-item-body>
																	<fo:block margin-left="5mm">
																		<xsl:value-of select="@title" />
																	</fo:block>
																</fo:list-item-body>
															</fo:list-item>
														</xsl:for-each>
													</fo:list-block>
												</fo:table-cell>
											</xsl:if>
										</xsl:if>
									</fo:table-row>
								</fo:table-body>
							</fo:table>
						</fo:block>
					</xsl:if>
					<xsl:if test="$showList = 1">
						<xsl:if test="//feature">
							<xsl:for-each select="//feature">
								<xsl:variable name="feature" select="@name" />
								<xsl:variable name="featureFields" select="*" />
								<xsl:if test="count(document($file)//*[name() = $feature]) &gt; 0">
									<fo:block>
										<fo:block font-family="Times Roman" font-weight="bold" font-size="12pt" font-style="italic" space-after="0.5em">
											<xsl:value-of select="@title" />
										</fo:block>
										<fo:table table-layout="fixed" width="100%" margin-bottom="1cm">
											<xsl:for-each select="*">
												<fo:table-column column-width="30mm" />
											</xsl:for-each>
											<fo:table-header>
												<xsl:for-each select="*">
													<fo:table-cell>
														<fo:block font-weight="bold" background-color="#EEEEEE" padding-before="6pt" padding-after="6pt">
															<xsl:value-of select="@name" />
														</fo:block>
													</fo:table-cell>
												</xsl:for-each>
											</fo:table-header>
											<fo:table-body>
												<xsl:for-each select="document($file)//*[name()=$feature]">
													<xsl:variable name="featureRow" select="." />
													<fo:table-row>
														<xsl:for-each select="$featureFields">
															<xsl:variable name="featurePropertyName" select="@name" />
															<fo:table-cell>
																<fo:block>
																	<xsl:value-of select="$featureRow/*[local-name() = $featurePropertyName]" />
																</fo:block>
															</fo:table-cell>
														</xsl:for-each>
													</fo:table-row>
												</xsl:for-each>
											</fo:table-body>
										</fo:table>
									</fo:block>
								</xsl:if>
							</xsl:for-each>
						</xsl:if>
					</xsl:if>
				</fo:flow>
			</fo:page-sequence>
		</fo:root>
	</xsl:template>
</xsl:stylesheet>