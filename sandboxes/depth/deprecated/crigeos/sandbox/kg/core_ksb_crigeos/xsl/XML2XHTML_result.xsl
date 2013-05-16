<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE stylesheet [
<!ENTITY space "<xsl:text> </xsl:text>">
]>


<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:gmd="http://www.isotc211.org/2005/gmd"
	xmlns:sdi="http://www.depth.ch/sdi" xmlns:gco="http://www.isotc211.org/2005/gco"
	xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ext="http://www.depth.ch/2008/ext"
	xmlns:bee="http://www.be.ch/bve/agi/2010/bee" xmlns:date="http://exslt.org/dates-and-times"
	extension-element-prefixes="date">

	<!-- Encodage des résultats -->
	<xsl:output encoding="utf-8" />
	<xsl:output method="html" />

	<xsl:template match="Metadata">
		<!-- Variable Declaration -->
		<xsl:variable name="logo">
			<xsl:value-of select="./sdi:Metadata/sdi:objecttype/@code" />
		</xsl:variable>
		<xsl:variable name="language">
			<xsl:value-of select="./sdi:Metadata/@user_lang" />
		</xsl:variable>
		<xsl:variable name="zeitstand">
			<xsl:value-of
				select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date/gmd:CI_Date[gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='revision']/gmd:date/gco:Date" />
		</xsl:variable>
		<!-- File Variables -->
		<xsl:variable name="published">
			<xsl:value-of select="./sdi:Metadata/sdi:product/@published" />
		</xsl:variable>
		<xsl:variable name="available">
			<xsl:value-of select="./sdi:Metadata/sdi:product/@available" />
		</xsl:variable>
		<xsl:variable name="filesize">
			<xsl:value-of select="./sdi:Metadata/sdi:product/@file_size" />
		</xsl:variable>
		<xsl:variable name="filetype">
			<xsl:value-of select="./sdi:Metadata/sdi:product/@file_type" />
		</xsl:variable>		
		<xsl:variable name="orderproduct">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:order/sdi:link" />
		</xsl:variable>
		<xsl:variable name="downloadProduct">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:downloadProduct/sdi:link" />
		</xsl:variable>
		<xsl:variable name="previewProduct">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:previewProduct/sdi:link" />
		</xsl:variable>
		
		<!-- Helpers -->
		<xsl:variable name="smallcase" select="'abcdefghijklmnopqrstuvwxyz'" />
		<xsl:variable name="uppercase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" />

		<div class="row">
			<div class="metadata-result">
				<!-- Logo of the different geoproducts -->
				<div class="metadata-logo">
					<h4 class="hidden">
						<xsl:value-of select="./sdi:Metadata/sdi:objecttype" />
					</h4>
					<div>
						<xsl:choose>
							<xsl:when test="$language='de-DE'">
								<xsl:choose>
									<xsl:when test="$logo ='layer'">
										<xsl:attribute name="class">metadata-logo-1-de</xsl:attribute>
									</xsl:when>
									<xsl:when test="$logo ='map'">
										<xsl:attribute name="class">metadata-logo-2-de</xsl:attribute>
									</xsl:when>
									<xsl:when test="$logo ='geoproduct'">
										<xsl:attribute name="class">metadata-logo-3-de</xsl:attribute>
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="class">metadata-logo-0</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:when>
							<xsl:when test="$language='en-GB'">
								<xsl:choose>
									<xsl:when test="$logo ='layer'">
										<xsl:attribute name="class">metadata-logo-1-en</xsl:attribute>
									</xsl:when>
									<xsl:when test="$logo ='map'">
										<xsl:attribute name="class">metadata-logo-2-en</xsl:attribute>
									</xsl:when>
									<xsl:when test="$logo ='geoproduct'">
										<xsl:attribute name="class">metadata-logo-3-en</xsl:attribute>
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="class">metadata-logo-0</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:when>
							<xsl:when test="$language='fr-FR'">
								<xsl:choose>
									<xsl:when test="$logo ='layer'">
										<xsl:attribute name="class">metadata-logo-1-fr</xsl:attribute>
									</xsl:when>
									<xsl:when test="$logo ='map'">
										<xsl:attribute name="class">metadata-logo-2-fr</xsl:attribute>
									</xsl:when>
									<xsl:when test="$logo ='geoproduct'">
										<xsl:attribute name="class">metadata-logo-3-fr</xsl:attribute>
									</xsl:when>
									<xsl:otherwise>
										<xsl:attribute name="class">metadata-logo-0</xsl:attribute>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:when>
						</xsl:choose>

					</div>
				</div>

				<!-- Description of the Metadata -->
				<div class="metadata-desc">
					<xsl:choose>
						<xsl:when test="$language='de-DE'">
							<h3>
								<xsl:value-of disable-output-escaping="yes"
									select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString" />
							</h3>
							<xsl:variable name="abstract">
								<xsl:value-of
									select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString" />
							</xsl:variable>
							<p>
								<xsl:value-of select="substring($abstract,1,200)" />
								[...]
							</p>
						</xsl:when>
						<xsl:when test="$language='fr-FR'">
							<h3>
								<xsl:value-of disable-output-escaping="yes"
									select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
							</h3>
							<xsl:variable name="abstract">
								<xsl:value-of
									select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
							</xsl:variable>
							<p>
								<xsl:value-of select="substring($abstract,1,200)" />
								[...]
							</p>
						</xsl:when>
						<xsl:when test="$language='en-GB'">
							<h3>
								<xsl:value-of disable-output-escaping="yes"
									select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#GB']" />
							</h3>
							<xsl:variable name="abstract">
								<xsl:value-of
									select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#GB']" />
							</xsl:variable>
							<p>
								<xsl:value-of select="substring($abstract,1,200)" />
								[...]
							</p>
						</xsl:when>
					</xsl:choose>
					<!-- Metadata: Summary Information -->
					<!-- Date -->
					<div class="metadata-summary">
						<span class="metadata-label">
							<xsl:choose>
								<xsl:when test="$language='de-DE'">
									Datenstand:
								</xsl:when>
								<xsl:when test="$language='fr-FR'">
									Date du jeu de données
								</xsl:when>
								<xsl:when test="$language='en-GB'">
									Data date
								</xsl:when>
							</xsl:choose>
						</span>
						<span class="metadata-value">
							<xsl:value-of select="date:day-in-month($zeitstand)" />
							<xsl:text>.</xsl:text>
							<xsl:value-of select="date:month-in-year($zeitstand)" />
							<xsl:text>.</xsl:text>
							<xsl:value-of select="date:year($zeitstand)" />&space;
						</span>
						<div class="clear" />
						<!-- Access rights -->
						<xsl:choose>
							<xsl:when test="$language='de-DE'">
								<span class="metadata-label">Zugangsberechtigung:</span>
								<span class="metadata-value">
									<xsl:value-of
										select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_Constraints/gmd:useLimitation/gco:CharacterString" />
								</span>
							</xsl:when>
							<xsl:when test="$language='fr-FR'">
								<span class="metadata-label">Accès:</span>
								<span class="metadata-value">
									<xsl:value-of
										select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_Constraints/gmd:useLimitation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
								</span>
							</xsl:when>
							<xsl:when test="$language='en-GB'">
								<span class="metadata-label">Access:</span>
								<span class="metadata-value">
									<xsl:value-of
										select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_Constraints/gmd:useLimitation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#GB']" />
								</span>
							</xsl:when>
						</xsl:choose>
						<div class="clear" />
						<!-- Code -->
						<span class="metadata-label">Code:</span>
						<span class="metadata-value">
							<xsl:value-of select="./sdi:Metadata/sdi:object/@object_name" />
						</span>
						<div class="clear" />
						<!-- Link und Download -->
						<div class="metadata-links">
							<xsl:choose>
								<xsl:when test="$language='de-DE'">
									<span class="metadata-link">
										<a>
											<xsl:attribute name="title">Detaillierte Informationen zu: <xsl:value-of
												disable-output-escaping="yes"
												select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString" /></xsl:attribute>
											<xsl:attribute name="class">modal</xsl:attribute>	
														<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
											<xsl:attribute name="href">index.php?tmpl=index&amp;option=com_easysdi_catalog&amp;Itemid=2&amp;context=geocatalog&amp;toolbar=1&amp;task=showMetadata&amp;type=complete&amp;id=<xsl:value-of
												select="./gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString" /></xsl:attribute>
											<xsl:text>Detaillierte Informationen</xsl:text>
										</a>
									</span>
									<xsl:choose>
										<xsl:when test="$logo ='layer'">
											<xsl:text />
										</xsl:when>
										<xsl:when test="$logo ='map'">
											<span class="metadata-link">
												<xsl:element name="a">
													<xsl:attribute name="class">intern</xsl:attribute>
													<xsl:attribute name="target">_blank</xsl:attribute>
													<xsl:attribute name="href"><xsl:value-of select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource/gmd:linkage/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" /></xsl:attribute>
													<xsl:text>Map view</xsl:text>
												</xsl:element>
											</span>
										</xsl:when>
										<xsl:when test="$logo ='geoproduct'">
											<xsl:choose>
												<xsl:when test="string-length($downloadProduct) > 0">
														<span class="metadata-link">
															<a>
																<xsl:attribute name="class">modal</xsl:attribute>	
																<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
																<xsl:attribute name="href">
																	<xsl:value-of  select="$downloadProduct" />
																</xsl:attribute>
																<xsl:text>Télécharger </xsl:text>
															</a>
														</span>
														<span class="info">
															<xsl:text> (</xsl:text>
															<xsl:value-of select="translate($filetype,$smallcase,$uppercase)" />
															<xsl:text>, </xsl:text>
															<xsl:value-of select="$filesize" />
															<xsl:text> Ko)</xsl:text>
														</span>
													
												</xsl:when >
												<xsl:otherwise>													
													<span class="metadata-link">
														<a>
															<xsl:attribute name="class">icon default</xsl:attribute>
															<xsl:attribute name="href">
																<xsl:value-of select="$orderproduct" />
															</xsl:attribute>
															<xsl:text>Commander</xsl:text>
														</a>
													</span>													
												</xsl:otherwise >
											</xsl:choose>
											<xsl:if test="string-length($previewProduct) > 0">
												<span class="metadata-link">
													<a>
														<xsl:attribute name="class">modal</xsl:attribute>	
														<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
														<xsl:attribute name="href"><xsl:value-of  select="$previewProduct" /></xsl:attribute>
														<xsl:text>Prévisualiser</xsl:text>
													</a>
												</span>
											</xsl:if>
										</xsl:when>
									</xsl:choose>
								</xsl:when>
								<xsl:when test="$language='fr-FR'">
									<span class="metadata-link">
										<a>
											<xsl:attribute name="title">des informations détaillées : <xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString" />
											</xsl:attribute>
											<xsl:attribute name="class">modal</xsl:attribute>	
											<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
											<xsl:attribute name="href">index.php?tmpl=component&amp;option=com_easysdi_catalog&amp;Itemid=2&amp;context=geocatalog&amp;toolbar=1&amp;task=showMetadata&amp;type=complete&amp;id=<xsl:value-of select="./gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString" /></xsl:attribute>
											<xsl:text>Details</xsl:text>
										</a>
									</span>
									<xsl:choose>
										<xsl:when test="$logo ='layer'">
											<xsl:text />
										</xsl:when>
										<xsl:when test="$logo ='map'">
											<span class="metadata-link">
												<xsl:element name="a">
													<xsl:attribute name="class">intern</xsl:attribute>
													<xsl:attribute name="target">_blank</xsl:attribute>
													<xsl:attribute name="href"><xsl:value-of select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource/gmd:linkage/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" /></xsl:attribute>
													<xsl:text>Map view</xsl:text>
												</xsl:element>
											</span>
										</xsl:when>
										<xsl:when test="$logo ='geoproduct'">
											<xsl:choose>
												<xsl:when test="string-length($downloadProduct) > 0">
														<span class="metadata-link">
															<a>
																<xsl:attribute name="class">modal</xsl:attribute>	
																<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
																<xsl:attribute name="href">
																	<xsl:value-of  select="$downloadProduct" />
																</xsl:attribute>
																<xsl:text>Télécharger </xsl:text>
															</a>
														</span>
														<span class="info">
															<xsl:text> (</xsl:text>
															<xsl:value-of select="translate($filetype,$smallcase,$uppercase)" />
															<xsl:text>, </xsl:text>
															<xsl:value-of select="$filesize" />
															<xsl:text> Ko)</xsl:text>
														</span>
													
												</xsl:when >
												<xsl:otherwise>													
													<span class="metadata-link">
														<a>
															<xsl:attribute name="class">icon default</xsl:attribute>
															<xsl:attribute name="href">
																<xsl:value-of select="$orderproduct" />
															</xsl:attribute>
															<xsl:text>Commander</xsl:text>
														</a>
													</span>													
												</xsl:otherwise >
											</xsl:choose>
											<xsl:if test="string-length($previewProduct) > 0">
												<span class="metadata-link">
													<a>
														<xsl:attribute name="class">modal</xsl:attribute>	
														<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
														<xsl:attribute name="href"><xsl:value-of  select="$previewProduct" /></xsl:attribute>
														<xsl:text>Prévisualiser</xsl:text>
													</a>
												</span>
											</xsl:if>
										</xsl:when>
									</xsl:choose>
								</xsl:when>
								<xsl:when test="$language='en-GB'">
									<span class="metadata-link">
										<a>
											<xsl:attribute name="title">des informations détaillées : 
												<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString" />
											</xsl:attribute>
											<xsl:attribute name="class">modal</xsl:attribute>	
														<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
											<xsl:attribute name="href">index.php?tmpl=index&amp;option=com_easysdi_catalog&amp;Itemid=2&amp;context=geocatalog&amp;toolbar=1&amp;task=showMetadata&amp;type=complete&amp;id=
												<xsl:value-of select="./gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString" />
											</xsl:attribute>
											<xsl:text>Details</xsl:text>
										</a>
									</span>
									<xsl:choose>
										<xsl:when test="$logo ='layer'">
											<xsl:text />
										</xsl:when>
										<xsl:when test="$logo ='map'">
											<span class="metadata-link">
												<xsl:element name="a">
													<xsl:attribute name="class">intern</xsl:attribute>
													<xsl:attribute name="target">_blank</xsl:attribute>
													<xsl:attribute name="href"><xsl:value-of select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource/gmd:linkage/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" /></xsl:attribute>
													<xsl:text>Map view</xsl:text>
												</xsl:element>
											</span>
										</xsl:when>
										<xsl:when test="$logo ='geoproduct'">
											<xsl:choose>
												<xsl:when test="string-length($downloadProduct) > 0">
														<span class="metadata-link">
															<a>
																<xsl:attribute name="class">modal</xsl:attribute>	
																<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
																<xsl:attribute name="href">
																	<xsl:value-of  select="$downloadProduct" />
																</xsl:attribute>
																<xsl:text>Télécharger </xsl:text>
															</a>
														</span>
														<span class="info">
															<xsl:text> (</xsl:text>
															<xsl:value-of select="translate($filetype,$smallcase,$uppercase)" />
															<xsl:text>, </xsl:text>
															<xsl:value-of select="$filesize" />
															<xsl:text> Ko)</xsl:text>
														</span>
													
												</xsl:when >
												<xsl:otherwise>													
													<span class="metadata-link">
														<a>
															<xsl:attribute name="class">icon default</xsl:attribute>
															<xsl:attribute name="href">
																<xsl:value-of select="$orderproduct" />
															</xsl:attribute>
															<xsl:text>Commander</xsl:text>
														</a>
													</span>													
												</xsl:otherwise >
											</xsl:choose>
											<xsl:if test="string-length($previewProduct) > 0">
												<span class="metadata-link">
													<a>
														<xsl:attribute name="class">modal</xsl:attribute>	
														<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
														<xsl:attribute name="href"><xsl:value-of  select="$previewProduct" /></xsl:attribute>
														<xsl:text>Prévisualiser</xsl:text>
													</a>
												</span>
											</xsl:if>
										</xsl:when>
									</xsl:choose>
								</xsl:when>
							</xsl:choose>
							<div class="clear" />
						</div>
					</div>
				</div>
				<div class="clear" />
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>


