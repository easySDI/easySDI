<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
Script XSLT de transformation des partenaires ASIT-VD sous Joomla!
Le résultat est fourni sous forme de fichier CSV avec une tabulation comme séparateur
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:gmd="http://www.isotc211.org/2005/gmd" 
xmlns:gco="http://www.isotc211.org/2005/gco" 
xmlns:bee="http://www.depth.ch/2008/bee"
>

<!-- Encodage des résultats -->
<xsl:output method="xml" encoding="utf-8" indent="yes"/>
	
	<xsl:template match="/">
		<gmd:MD_Metadata>
			<gmd:identificationInfo>
				<gmd:MD_DataIdentification>
					<gmd:extent>
						<gmd:EX_Extent>
							<gmd:geographicElement>
								<gmd:EX_GeographicBoundingBox>
									<gmd:northBoundLatitude>
										<gco:Decimal>
											<xsl:value-of select ="metadata/idinfo/spdom/bounding/northbc"/>
										</gco:Decimal>
									</gmd:northBoundLatitude>
									<gmd:southBoundLatitude>
										<gco:Decimal>
											<xsl:value-of select ="metadata/idinfo/spdom/bounding/southbc"/>
										</gco:Decimal>
									</gmd:southBoundLatitude>
									<gmd:eastBoundLongitude>
										<gco:Decimal>
											<xsl:value-of select ="metadata/idinfo/spdom/bounding/eastbc"/>
										</gco:Decimal>
									</gmd:eastBoundLongitude>
									<gmd:westBoundLongitude>
										<gco:Decimal>
											<xsl:value-of select ="metadata/idinfo/spdom/bounding/westbc"/>
										</gco:Decimal>
									</gmd:westBoundLongitude>
								</gmd:EX_GeographicBoundingBox>
							</gmd:geographicElement>
						</gmd:EX_Extent>
					</gmd:extent>
				</gmd:MD_DataIdentification>
			</gmd:identificationInfo>
			<gmd:contentInfo xmlns:bee="http://www.depth.ch/2008/bee">
				<gmd:MD_FeatureCatalogueDescription>
					<gmd:class>
						<gmd:MD_Class>
							<xsl:for-each select="metadata/eainfo/detailed/attr">
								<gmd:attribute>
									<gmd:MD_Attribute>
										<gmd:name>
											<gco:CharacterString>
												<xsl:value-of select="attrlabl"/>
											</gco:CharacterString>
										</gmd:name>
										<bee:scale>
											<gco:Decimal>
												<xsl:value-of select="atprecis"/>
											</gco:Decimal>
										</bee:scale>
										<bee:precision>
											<gco:Decimal>
												<xsl:value-of select="attscale"/>
											</gco:Decimal>
										</bee:precision>
										<bee:length>
											<gco:Decimal>
												<xsl:value-of select="attwidth"/>
											</gco:Decimal>
										</bee:length>
										<gmd:anonymousType>
											<gmd:MD_Type>
												<gmd:type>
													<gco:CharacterString>
														<xsl:choose>
															<xsl:when test="attrtype = 'OID'">
																<xsl:text disable-output-escaping="yes">Object ID</xsl:text>
														    </xsl:when>
															<xsl:when test="attrtype = 'Boolean'">
																<xsl:text disable-output-escaping="yes">Short Integer</xsl:text>
														    </xsl:when>
															<xsl:when test="attrtype = 'SmallInteger'">
																<xsl:text disable-output-escaping="yes">Short Integer</xsl:text>
														    </xsl:when>
															<xsl:when test="attrtype = 'Integer'">
																<xsl:text disable-output-escaping="yes">Long Integer</xsl:text>
														    </xsl:when>
														    <xsl:when test="attrtype = 'Single'">
																<xsl:text disable-output-escaping="yes">Double</xsl:text>
														    </xsl:when>
															<xsl:when test="attrtype = 'String'">
																<xsl:text disable-output-escaping="yes">Text</xsl:text>
														    </xsl:when>
															<xsl:when test="attrtype = 'Blob'">
																<xsl:text disable-output-escaping="yes">BLOB</xsl:text>
														    </xsl:when>
															<xsl:when test="attrtype = 'Binary'">
																<xsl:text disable-output-escaping="yes">BLOB</xsl:text>
														    </xsl:when>
															<xsl:when test="attrtype = 'Number'">
																<xsl:text disable-output-escaping="yes">Double</xsl:text>
														    </xsl:when>
															<xsl:when test="attrtype = 'Character'">
																<xsl:text disable-output-escaping="yes">Text</xsl:text>
														    </xsl:when>
														    <xsl:otherwise>
																<xsl:value-of select="attrtype"/>
														    </xsl:otherwise>
														</xsl:choose>
													</gco:CharacterString>
												</gmd:type>
											</gmd:MD_Type>
										</gmd:anonymousType>
									</gmd:MD_Attribute>
								</gmd:attribute>
							</xsl:for-each>
						</gmd:MD_Class>
					</gmd:class>
				</gmd:MD_FeatureCatalogueDescription>
			</gmd:contentInfo>
		</gmd:MD_Metadata>
	</xsl:template>
</xsl:stylesheet>