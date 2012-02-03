<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE stylesheet [
<!ENTITY space "<xsl:text/>">
]>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:gmd="http://www.isotc211.org/2005/gmd" 
xmlns:sdi="http://www.depth.ch/sdi"
xmlns:gco="http://www.isotc211.org/2005/gco"
xmlns:xlink="http://www.w3.org/1999/xlink"
xmlns:ext="http://www.depth.ch/2008/ext"
xmlns:bee="http://www.be.ch/bve/agi/2010/bee"
xmlns:che="http://www.geocat.ch/2008/che"
xmlns:date="http://exslt.org/dates-and-times" extension-element-prefixes="date"
>

	<!-- Encodage des résultats -->
	<xsl:output encoding="utf-8"/>
	<xsl:output method="html"/>
	<xsl:output media-type="text/html" indent="yes" />

	<xsl:template match="Metadata">

		<!-- Variable Declaration -->
		<xsl:variable name="logo">
			<xsl:value-of select="./sdi:Metadata/sdi:objecttype/@code" />
		</xsl:variable>
		<xsl:variable name="language">
			<xsl:value-of select="./sdi:Metadata/@user_lang" />
		</xsl:variable>
		<xsl:variable name="zeitstand">
			<xsl:value-of select="./sdi:Metadata/sdi:object/@objectversion_title" />
		</xsl:variable>
		<xsl:variable name="imgreplace">
			<xsl:text>&lt;img src=</xsl:text>
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

		<!-- Download Links -->
		<xsl:variable name="downloadPDF">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:exportPDF/sdi:link" />
		</xsl:variable>
		<xsl:variable name="exportXML">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:exportXML/sdi:link" />
		</xsl:variable>
		<xsl:variable name="orderproduct">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:order/sdi:link" />
		</xsl:variable>
		<xsl:variable name="print">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:print/sdi:link" />
		</xsl:variable>
		<xsl:variable name="downloadProduct">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:downloadProduct/sdi:link" />
		</xsl:variable>
		<xsl:variable name="previewProduct">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:previewProduct/sdi:link" />
		</xsl:variable>

		<!-- Dates -->
		<xsl:variable name="datetimecreated">
			<xsl:value-of select="./sdi:Metadata/sdi:object/@metadata_created" />
		</xsl:variable>
		<xsl:variable name="datecreated">
			<xsl:choose>
				<xsl:when test="$datetimecreated !='0000-00-00T00:00:00'">
					<xsl:value-of select="date:day-in-month($datetimecreated)"/>
					<xsl:text>.</xsl:text>
					<xsl:value-of select="date:month-in-year($datetimecreated)"/>
					<xsl:text>.</xsl:text>
					<xsl:value-of select="date:year($datetimecreated)"/>
					<xsl:text/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:variable name="datetimeupdated">
			<xsl:value-of select="./sdi:Metadata/sdi:object/@metadata_updated" />
		</xsl:variable>
		<xsl:variable name="dateupdated">
			<xsl:choose>
				<xsl:when test="$datetimeupdated !='0000-00-00T00:00:00'">
					<xsl:value-of select="date:day-in-month($datetimeupdated)"/>
					<xsl:text>.</xsl:text>
					<xsl:value-of select="date:month-in-year($datetimeupdated)"/>
					<xsl:text>.</xsl:text>
					<xsl:value-of select="date:year($datetimeupdated)"/>
					<xsl:text/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:text/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<!-- Helpers -->
		<xsl:variable name="smallcase" select="'abcdefghijklmnopqrstuvwxyz'" />
		<xsl:variable name="uppercase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" />

		<!-- Title of the metadata -->
		<div id="metadata-body">	
			<xsl:choose>
				<xsl:when test="$language='fr-FR'">
					<h1>
						<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
					</h1>
					<xsl:choose>
						<xsl:when test="$logo='geoproduct'"> 
							<div class="metadata-box box news-small">
									<div class="title">
										<h2>Actions</h2>
									</div>
										<div class="body">	
											<xsl:choose>
												<xsl:when test="$logo ='layer'">
													<xsl:text/>
												</xsl:when>
												<xsl:when test="$logo ='map'">
													<p>
														<xsl:element name="a">
															<xsl:attribute name="class">extern</xsl:attribute>
															<xsl:attribute name="target">_blank</xsl:attribute>
															<xsl:attribute name="href">
																<xsl:value-of select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/bee:section/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
															</xsl:attribute>
															<xsl:text>Carte</xsl:text>
														</xsl:element>
													</p>
												</xsl:when>
												<xsl:when test="$logo ='geoproduct'">
													<xsl:choose>
														<xsl:when test="string-length($downloadProduct) > 0">
															<p>
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
															</p>
														</xsl:when >
														<xsl:otherwise>
															<p>
															<span class="metadata-link">
																<a>
																	<xsl:attribute name="class">icon default</xsl:attribute>
																	<xsl:attribute name="href">
																		<xsl:value-of select="$orderproduct" />
																	</xsl:attribute>
																	<xsl:text>Commander</xsl:text>
																</a>
															</span>
															</p>
														</xsl:otherwise >
													</xsl:choose>
													<xsl:if test="string-length($previewProduct) > 0">
														<p>
															<span class="metadata-link">
																<a>
																	<xsl:attribute name="class">modal</xsl:attribute>	
																	<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
																	<xsl:attribute name="href">
																		<xsl:value-of  select="$previewProduct" />
																	</xsl:attribute>
																	<xsl:text>Prévisualiser</xsl:text>
																</a>
															</span>
														</p>
													</xsl:if>	
												</xsl:when>
											</xsl:choose>
											<p/>
											<p>
												<a>
													<xsl:attribute name="class">icon default</xsl:attribute>
													<xsl:attribute name="href">
														<xsl:value-of select="$print" />
													</xsl:attribute>Imprimer</a>
											</p>
											<p>
												<a>
													<xsl:attribute name="class">icon default</xsl:attribute>
													<xsl:attribute name="href">
														<xsl:value-of select="$exportXML" />
													</xsl:attribute>XML</a>
											</p>
											<p>
												<a>
													<xsl:attribute name="class">icon default</xsl:attribute>
													<xsl:attribute name="href">
														<xsl:value-of select="$downloadPDF" />
													</xsl:attribute>PDF</a>
											</p>
											
										</div>
									</div>
							<div class="summary active">
								<h2>Informations principales</h2>
								<div class="metadata-maininfo">
																	<xsl:call-template name="bildausschnitt">
									</xsl:call-template>

									<p>
										<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
									</p>
								</div>
								<div class="clear"/>
								<table class="alternative">
									<tr>
										<td width="30%">Type:</td>
										<td>Géoproduit</td>
									</tr>
									<tr>
										<td width="30%">Code:</td>
										<td>
											<xsl:value-of select="./sdi:Metadata/sdi:object/@object_name" />
										</td>
									</tr>
									<tr>
										<td width="30%">Accès:</td>
										<td>
											<xsl:value-of select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_Constraints/gmd:useLimitation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
										</td>
									</tr>
									<tr>
										<td width="30%">Statut:</td>
										<td>
											<xsl:call-template name="ProgressCodeTemplateFR">
												<xsl:with-param name="ProgressCode" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:status/gmd:MD_ProgressCode/@codeListValue" />
											</xsl:call-template>
										</td>
									</tr>
									<tr>
										<td width="30%">Mise à jour:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date">
												<xsl:if test="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='revision'">
													<xsl:apply-templates select="gmd:CI_Date/gmd:date" />
													<br />
												</xsl:if>
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Publication:</td>
										<td>

											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date">
												<xsl:if test="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='publication'">
													<xsl:apply-templates select="gmd:CI_Date/gmd:date" />
													<br />
												</xsl:if>
											</xsl:for-each>
										</td>
									</tr>
								</table>
							</div>
							<p/>
						
							<div class="section">	
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Plus d'informations</th>
									</tr>
									<tr>
										<td width="30%">Appellation:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:otherCitationDetails/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Thématique:</td>
										<td>		
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory">
												<xsl:call-template name="categoryCodeTemplateFR">
												</xsl:call-template>
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Mots-clés:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword">
												<xsl:value-of disable-output-escaping="yes" select="gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>, 
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Lien vers les renseignements détaillés sur le produit</td>
										<td>

											<xsl:element name="a">
												<xsl:attribute name="class">extern</xsl:attribute>
												<xsl:attribute name="href">
													<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/bee:detailedInformation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
												</xsl:attribute>
												<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/bee:detailedInformation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>		
											</xsl:element>

										</td>
									</tr>
									<tr>
										<td width="30%">Langue:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:language">
												<xsl:call-template name="languageISOCodeTemplateFR">
													<xsl:with-param name="languageISOCode" select="gmd:LanguageCodeISO" />
												</xsl:call-template>
												<xsl:text>, </xsl:text>
											</xsl:for-each>
										</td>
									</tr>

									<tr>
										<td width="30%">Coordonnées de l'étendue:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Informations supplémentaires:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:supplementalInformation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>

									<tr>
										<th colspan="2" scope="col">Mise à jour</th>
									</tr>
									<tr>
										<td width="30%">Fréquence de mise à jour:</td>
										<td>
											<xsl:call-template name="maintenanceTypeTemplateFR">
												<xsl:with-param name="maintenanceTypeCode" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode/@codeListValue"/>
											</xsl:call-template>
										</td>
									</tr>
									<tr>
										<td width="30%">Remarque sur la mise à jour:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceNote/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Version:</td>
										<td>Année: <xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/bee:version/bee:DD_DataDictionary/bee:versionYear/gco:Decimal"/>
											<br />Version: <xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/bee:version/bee:DD_DataDictionary/bee:versionNumber/gco:Decimal"/>
										</td>
									</tr>
									<tr>
										<th colspan="2" scope="col">Modèle de données</th>
									</tr>
									<tr>
										<td width="30%">Type du modèle de données:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/bee:modelType/bee:modelTypeCode/@codeListValue"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Modèle de données:</td>
										<td>
											<xsl:element name="a">
												<xsl:attribute name="class">extern</xsl:attribute>
												<xsl:attribute name="href">
													<xsl:value-of  select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/gmd:dataModel/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
												</xsl:attribute>
												<xsl:value-of  select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/gmd:dataModel/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
											</xsl:element>					
										</td>
									</tr>
									<tr>
										<td width="30%">Résultats du Delta Checker:</td>
										<td>
											<xsl:element name="a">
												<xsl:attribute name="class">extern</xsl:attribute>
												<xsl:attribute name="href">
													<xsl:value-of  select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/bee:deltacheckerReport/gco:CharacterString"/>
												</xsl:attribute>
												<xsl:value-of  select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/bee:deltacheckerReport/gco:CharacterString"/>
											</xsl:element>					
										</td>
									</tr>
									<tr>
										<th colspan="2" scope="col">Règles topologique</th>
									</tr>
									<tr>
										<td width="30%">Description:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:report/gmd:DQ_Element">
												<xsl:value-of disable-output-escaping="yes" select="bee:topologyDescription/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
												<br />
											</xsl:for-each>	
										</td>
									</tr>	
									<tr>
										<th colspan="2" scope="col">Contraintes légales</th>
									</tr>	
									<tr>
										<td width="30%">Force légale:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/bee:legality/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>	
									<tr>
										<td width="30%">Reproduction:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/bee:reproduction/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Protection des données:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/bee:dataProtection/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>

									<tr>
										<td width="30%">Conditions d'utilisation:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:otherConstraints/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>	
									<tr>
										<th colspan="2" scope="col">Législation</th>
									</tr>	
									<xsl:for-each select="./gmd:MD_Metadata/gmd:legislationInformation/gmd:MD_Legislation">
										<tr>
											<td width="30%">Titre:</td>
											<td>
												<xsl:value-of  select="gmd:title/gmd:CI_Citation/gmd:title/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
											</td>
										</tr>
										<tr>
											<td width="30%">Appellation:</td>
											<td>
												<xsl:value-of  select="gmd:title/gmd:CI_Citation/gmd:otherCitationDetails/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
											</td>
										</tr>
										<tr>
											<td width="30%">Type de législation:</td>
											<td>
												<xsl:call-template name="legislationTypeTemplateFR">
													<xsl:with-param name="legislationTypeCode" select="gmd:legislationType/gmd:CI_LegislationTypeCode/@codeListValue"/>
												</xsl:call-template>
											</td>
										</tr>
										<tr>
											<td width="30%">Pays:</td>
											<td>
												<xsl:value-of  select="gmd:country/gmd:CountryCodeISO" />
											</td>
										</tr>
										<tr>
											<td width="30%">Langue:</td>
											<td>
												<xsl:call-template name="languageISOCodeTemplateFR">
													<xsl:with-param name="languageISOCode" select="gmd:language/gmd:LanguageCodeISO" />
												</xsl:call-template>
											</td>
										</tr>
										<tr>
											<td width="30%">Date:</td>
											<td>
												<xsl:for-each select="gmd:title/gmd:CI_Citation/gmd:date">
													<xsl:apply-templates select="gmd:CI_Date/gmd:date" /> (<xsl:call-template name="dateTypeCodeTemplateFR">
														<xsl:with-param name="dateTypeCode" select="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue" />
													</xsl:call-template>)<br />
												</xsl:for-each>
											</td>
										</tr>
									</xsl:for-each>
									<tr>
										<th colspan="2" scope="col">Informations sur la transmission des géodonnées</th>
									</tr>
									<xsl:for-each select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine">
										<tr>
											<td width="30%">Description:</td>
											<td>
												<xsl:value-of  select="gmd:CI_OnlineResource/gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
											</td>
										</tr>
									</xsl:for-each>
									<xsl:for-each select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:distributionFormat/gmd:MD_Format">
										<tr>
											<td width="30%">Nom du format:</td>
											<td>
												<xsl:value-of  select="gmd:name/gco:CharacterString" />
											</td>
										</tr>
										<tr>
											<td width="30%">Version du format:</td>
											<td>
												<xsl:value-of  select="gmd:version/gco:CharacterString" />
											</td>
										</tr>		
										<tr>
											<td width="30%">Frais:</td>
											<td>
												<xsl:value-of select="gmd:distributorFormat/gmd:MD_Distributor/gmd:distributionOrderProcess/gmd:MD_StandardOrderProcess/gmd:orderingInstructions/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
											</td>
										</tr>
									</xsl:for-each>
									<tr>
										<td width="30%">Remarques sur la diffusion des données:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/bee:publicationRemark/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
										</td>
									</tr>
								</table>
							</div>
							<div class="section">	
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Contacts</th>
									</tr>

									<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact">
										<tr>
											<xsl:call-template name="addressTemplateFR">
											</xsl:call-template>
										</tr>
									</xsl:for-each>		
								</table>
							</div>


							<!-- ************************ Relation ******************************* -->


							<div class="section">

								<xsl:call-template name="relationshipTableFR">
								</xsl:call-template>
					
							</div>


							<!-- ************************ Informations sur les métadonnéesen ******************************* -->

							<div class="section">
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Informations sur les métadonnées</th>
									</tr>

									<xsl:for-each select="./gmd:MD_Metadata/gmd:contact">
										<tr>
											<xsl:call-template name="addressTemplateFR">
											</xsl:call-template>
										</tr>
									</xsl:for-each>
									<tr>
										<td width="30%">Fichier mis à jour le:</td>
										<td>
											<xsl:value-of select="$dateupdated" />
										</td>
									</tr>
									<tr>
										<td width="30%">Fichier créé le:</td>
										<td>
											<xsl:value-of select="$datecreated" />
										</td>
									</tr>
									<tr>
										<td width="30%">ID de la métadonnée:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Nom du standard de métadonnées:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:metadataStandardName/gco:CharacterString" />
										</td>
									</tr>
									<tr>
										<td width="30%">Version du standard de métadonnées:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:metadataStandardversion/gco:CharacterString" />
										</td>
									</tr>
									<tr>
										<td width="30%">Domaine des métadonnées:</td>
										<td>

											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:hierarchyLevelName/gco:CharacterString" />
										</td>
									</tr>

								</table>
							</div>


						</xsl:when> 
						<!--******************** ENDE GEOPRODUKT ******************* -->

						<xsl:when test="$logo='map'">
							<!-- Alle Angaben zur Karte -->

							<!-- ************************ START KARTE *********************** -->

							<!-- ************************ Hauptinformation ********************* -->
							<h3 class="showsummary active">
								<a class="icon show" title="Plus de détails" href="/">Informations principales</a>
							</h3>
							<div class="summary active">
								<h2>Informations principales</h2>
								<div class="metadata-maininfo">

									<div class="metadata-box box news-small">
										<div class="title">
											<h2>Télécharger</h2>
										</div>
										<div class="body">	
											<xsl:choose>
												<xsl:when test="$logo ='layer'">
													<xsl:text/>
												</xsl:when>
												<xsl:when test="$logo ='map'">
													<p>
														<xsl:element name="a">
															<xsl:attribute name="class">extern</xsl:attribute>
															<xsl:attribute name="target">_blank</xsl:attribute>
															<xsl:attribute name="href">
																<xsl:value-of select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource/gmd:linkage/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
															</xsl:attribute>
															<xsl:text>Afficher la carte</xsl:text>
														</xsl:element>
													</p>
												</xsl:when>
												<xsl:when test="$logo ='geoproduct'">
													<xsl:if test="string-length($filetype) = 3">
														<p>
															<a>
																<xsl:attribute name="class">zip</xsl:attribute>
																<xsl:attribute name="href">index.php?tmpl=index&amp;option=com_easysdi_shop&amp;task=downloadAvailableProduct&amp;cid=<xsl:value-of select="./gmd:MD_Metadata/gmd:fileIdentifier/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
																</xsl:attribute>
																<xsl:text>ZIP</xsl:text>
															</a>
															<span class="info">
																<xsl:text> (</xsl:text>
																<xsl:value-of select="translate($filetype,$smallcase,$uppercase)" />
																<xsl:text>, </xsl:text>
																<xsl:value-of select="$filesize" />
																<xsl:text> Ko)</xsl:text>
															</span>
														</p>

													</xsl:if>  	
												</xsl:when>
											</xsl:choose>
											<p>
												<script language="javascript" type="text/javascript">
												</script>

											</p>
											<p>
												<a>
													<xsl:attribute name="class">icon default</xsl:attribute>
													<xsl:attribute name="href">
														<xsl:value-of select="$exportXML" />
													</xsl:attribute>
					XML</a>

											</p>
										</div>
									</div>
									<!-- *******************************  Bildausschnitt  ************************************* -->
									<xsl:call-template name="bildausschnitt">
									</xsl:call-template>

									<p>
										<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
									</p>
								</div>
								<div class="clear"/>
								<table class="alternative">
									<tr>
										<td width="30%">Type:</td>
										<td>Karte</td>
									</tr>
									<tr>
										<td width="30%">Code:</td>
										<td>
											<xsl:value-of select="./sdi:Metadata/sdi:object/@object_name" />
										</td>
									</tr>
									<tr>
										<td width="30%">Accès:</td>
										<td>
											<xsl:value-of select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_Constraints/gmd:useLimitation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
										</td>
									</tr>
									<tr>
										<td width="30%">Cercle d'utilisateurs autorisé:</td>
										<td>
											<xsl:value-of select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource/gmd:applicationProfile/gco:CharacterString" />, <xsl:value-of select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource/bee:passwordProtection/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
										</td>
									</tr>


									<tr>
										<td width="30%">Nom de la source en ligne:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource">
												<xsl:value-of select="gmd:name/gco:CharacterString" />
												<br />
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Statut:</td>
										<td>
											<xsl:call-template name="ProgressCodeTemplateFR">
												<xsl:with-param name="ProgressCode" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:status/gmd:MD_ProgressCode/@codeListValue" />
											</xsl:call-template>
										</td>
									</tr>
									<tr>
										<td width="30%">Mise à jour:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date">
												<xsl:if test="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='revision'">
													<xsl:apply-templates select="gmd:CI_Date/gmd:date" />
													<br />
												</xsl:if>
											</xsl:for-each>
										</td>
									</tr>
								</table>
							</div>
							<p/>
							
							<div class="section">	
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Plus d'informations</th>
									</tr>
									<tr>
										<td width="30%">Appellation:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:otherCitationDetails/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Thématique:</td>
										<td>		
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory">
												<xsl:call-template name="categoryCodeTemplateFR">
													<!--								<xsl:with-param name="categoryCode" select="gmd:MD_TopicCategoryCode/@codeListValue"/>,-->
												</xsl:call-template>
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Mots-clés:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword">
												<xsl:value-of disable-output-escaping="yes" select="gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>, 
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Langue:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:language">
												<xsl:call-template name="languageISOCodeTemplateFR">
													<xsl:with-param name="languageISOCode" select="gmd:LanguageCodeISO" />
												</xsl:call-template>
												<xsl:text>, </xsl:text>
											</xsl:for-each>
										</td>
									</tr>

									<tr>
										<td width="30%">Coordonnées de l'étendue:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Système de référence:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:referenceSystemIdentifier/gmd:RS_Identifier/gmd:code/gco:CharacterString"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Informations supplémentaires:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:supplementalInformation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>

									<tr>
										<th colspan="2" scope="col">Mise à jour</th>
									</tr>
									<tr>
										<td width="30%">Fréquence de mise à jour:</td>
										<td>
											<xsl:call-template name="maintenanceTypeTemplateFR">
												<xsl:with-param name="maintenanceTypeCode" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode/@codeListValue"/>
											</xsl:call-template>
										</td>
									</tr>
									<tr>
										<td width="30%">Remarque sur la mise à jour:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceNote/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<th colspan="2" scope="col">Contraintes légales</th>
									</tr>	
									<tr>
										<td width="30%">Force légale:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/bee:legality/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>	
									<tr>
										<td width="30%">Reproduction:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/bee:reproduction/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Protection des données:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/bee:dataProtection/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>


								</table>
							</div>
									<div class="section">	
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Contacts</th>
									</tr>

									<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact">
										<tr>
											<xsl:call-template name="addressTemplateFR">
											</xsl:call-template>
										</tr>
									</xsl:for-each>


								</table>
							</div>

							<!-- ************************ Information sur la structure de la carte ******************************* -->

							<div class="section">	
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Information sur la structure de la carte</th>
									</tr>
									<tr>
										<td width="30%">Thème et l'échelle:</td>						
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/bee:scales">
												<xsl:element name="img">
													<xsl:attribute name="class">metadata-image-scales</xsl:attribute>
													<xsl:attribute name="src">
														<xsl:value-of select="gco:CharacterString" />
													</xsl:attribute>
												</xsl:element>
											</xsl:for-each>

										</td>
									</tr>
									<tr>
										<td width="30%">Vue de la carte:</td>						
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/bee:viewDescription">
												<p>
													<xsl:value-of select="gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
												</p>
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Description:</td>						
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine">
												<p>
													<xsl:value-of select="gmd:CI_OnlineResource/gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
												</p>
											</xsl:for-each>
										</td>
									</tr>
								</table>
							</div>


							<!-- ************************ Relationen ******************************* -->


							<div class="section">

								<xsl:call-template name="relationshipTableFR">
								</xsl:call-template>
				
							</div>


							<!-- ************************ Informations sur les métadonnéesen ******************************* -->

							<div class="section">
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Informations sur les métadonnées</th>
									</tr>


									<xsl:for-each select="./gmd:MD_Metadata/gmd:contact">
										<tr>
											<xsl:call-template name="addressTemplateFR">
											</xsl:call-template>
										</tr>
									</xsl:for-each>


									<tr>
										<td width="30%">Fichier mis à jour le:</td>
										<td>
											<xsl:value-of select="$dateupdated" />
										</td>
									</tr>
									<tr>
										<td width="30%">Fichier créé le:</td>
										<td>
											<xsl:value-of select="$datecreated" />
										</td>
									</tr>
									<tr>
										<td width="30%">ID de la métadonnée:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Nom du standard de métadonnées:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:metadataStandardName/gco:CharacterString" />
										</td>
									</tr>
									<tr>
										<td width="30%">Version du standard de métadonnées:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:metadataStandardversion/gco:CharacterString" />
										</td>
									</tr>
									<tr>
										<td width="30%">Domaine des métadonnées:</td>
										<td>

											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:hierarchyLevelName/gco:CharacterString" />
											<!---
								<xsl:call-template name="ScopeCodeTemplateFR">
									<xsl:with-param name="ScopeCode" select="./gmd:MD_Metadata/gmd:hierarchyLevel/gmd:MD_ScopeCode/@codeListValue" />
								</xsl:call-template>
							-->
										</td>
									</tr>
								</table>
							</div>




							<!-- ********************** ENDE KARTE **************************** -->
						</xsl:when>
						<!-- Ende Angaben Karte -->

						<xsl:when test="$logo='layer'">
							<!-- Alle Angaben zum Layer -->

							<!-- ************************ START LAYER *********************** -->

							<!-- ************************ Hauptinformation ********************* -->
							<h3 class="showsummary active">
								<a class="icon show" title="Plus de détails" href="/">Informations principales</a>
							</h3>
							<div class="summary active">
								<h2>Informations principales</h2>
								<div class="metadata-maininfo">

									<div class="metadata-box box news-small">
										<div class="title">
											<h2>Télécharger</h2>
										</div>
										<div class="body">	
											<xsl:choose>
												<xsl:when test="$logo ='layer'">
													<xsl:text/>
												</xsl:when>
												<xsl:when test="$logo ='map'">
													<p>
														<xsl:element name="a">
															<xsl:attribute name="class">extern</xsl:attribute>
															<xsl:attribute name="target">_blank</xsl:attribute>
															<xsl:attribute name="href">
																<xsl:value-of select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/bee:section/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
															</xsl:attribute>
															<xsl:text>Afficher la carte</xsl:text>
														</xsl:element>
													</p>
												</xsl:when>
												<xsl:when test="$logo ='geoproduct'">
													<xsl:if test="string-length($filetype) = 3">
														<p>
															<a>
																<xsl:attribute name="class">zip</xsl:attribute>
																<xsl:attribute name="href">index.php?tmpl=index&amp;option=com_easysdi_shop&amp;task=downloadAvailableProduct&amp;cid=<xsl:value-of select="./gmd:MD_Metadata/gmd:fileIdentifier/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
																</xsl:attribute>
																<xsl:text>ZIP</xsl:text>
															</a>
															<span class="info">
																<xsl:text> (</xsl:text>
																<xsl:value-of select="translate($filetype,$smallcase,$uppercase)" />
																<xsl:text>, </xsl:text>
																<xsl:value-of select="$filesize" />
																<xsl:text> Ko)</xsl:text>
															</span>
														</p>

													</xsl:if>  	
												</xsl:when>
											</xsl:choose>
											<p>
												<script language="javascript" type="text/javascript">
				                  /* <![CDATA[ */
				                        document.write('<a target="_blank" class="pdf" href="templates/geoportal/makepdf.php?url=' + encodeURIComponent(location.href) +'">');
				                        document.write('PDF');
				                        document.write('</a>');
				                  /* ]]> */
												</script>

											</p>
											<p>
												<a>
													<xsl:attribute name="class">icon default</xsl:attribute>
													<xsl:attribute name="href">
														<xsl:value-of select="$exportXML" />
													</xsl:attribute>
					XML</a>

											</p>
										</div>
									</div>
									<!-- *******************************  Bildausschnitt  ************************************* -->
									<xsl:call-template name="bildausschnitt">
									</xsl:call-template>
									<p>
										<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
									</p>
								</div>
								<div class="clear"/>
								<table class="alternative">
									<tr>
										<td width="30%">Type:</td>
										<td>Couche</td>
									</tr>
									<tr>
										<td width="30%">Code:</td>
										<td>
											<xsl:value-of select="./sdi:Metadata/sdi:object/@object_name" />
										</td>
									</tr>
									<tr>
										<td width="30%">Accès:</td>
										<td>
											<xsl:value-of select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_Constraints/gmd:useLimitation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
										</td>
									</tr>
									<tr>
										<td width="30%">Statut:</td>
										<td>
											<xsl:call-template name="ProgressCodeTemplateFR">
												<xsl:with-param name="ProgressCode" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:status/gmd:MD_ProgressCode/@codeListValue" />
											</xsl:call-template>
										</td>
									</tr>
									<tr>
										<td width="30%">Mise à jour:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date">
												<xsl:if test="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='revision'">
													<xsl:apply-templates select="gmd:CI_Date/gmd:date" />
													<br />
												</xsl:if>
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Publication:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date">
												<xsl:if test="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='publication'">
													<xsl:apply-templates select="gmd:CI_Date/gmd:date" />
													<br />
												</xsl:if>
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Type de représentation:</td>
										<td>
											<xsl:call-template name="SpatialRepresentationTypeTemplateFR">
												<xsl:with-param name="SpatialRepresentationType" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:spatialRepresentationType/gmd:MD_SpatialRepresentationTypeCode/@codeListValue" />
											</xsl:call-template>
										</td>
									</tr>
									<tr>
										<td width="30%">Type de données:</td>
										<td>
											<xsl:call-template name="dataTypeTemplateFR">
												<xsl:with-param name="dataType" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/bee:dataType/bee:dataTypecode/@codeListValue" />
											</xsl:call-template>
										</td>
									</tr>
									<tr>
										<td width="30%">Note sur les sources et les bases:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:useLimitation">
												<xsl:value-of select="gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
												<br />
											</xsl:for-each>
										</td>
									</tr>
								</table>

							</div>
							<p/>
							
							<div class="section">	
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Plus d'informations</th>
									</tr>
									<tr>
										<td width="30%">Appellation:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:otherCitationDetails/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Thématique:</td>
										<td>		
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory">
												<xsl:call-template name="categoryCodeTemplateFR">
													<!--								<xsl:with-param name="categoryCode" select="gmd:MD_TopicCategoryCode/@codeListValue"/>,-->
												</xsl:call-template>
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Mots-clés:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword">
												<xsl:value-of disable-output-escaping="yes" select="gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>, 
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Langue:</td>
										<td>
											<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:language">
												<xsl:call-template name="languageISOCodeTemplateFR">
													<xsl:with-param name="languageISOCode" select="gmd:LanguageCodeISO" />
												</xsl:call-template>
												<xsl:text>, </xsl:text>
											</xsl:for-each>
										</td>
									</tr>
									<tr>
										<td width="30%">Code du jeu de caractères:</td>
										<td>
											<xsl:call-template name="CharacterSetTemplateFR">
												<xsl:with-param name="CharacterSetCode" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:characterSet/gmd:MD_CharacterSetCode/@codeListValue" />

											</xsl:call-template>
										</td>
									</tr>
									<tr>
										<td width="30%">Informations supplémentaires:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:supplementalInformation/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>

									<tr>
										<th colspan="2" scope="col">Mise à jour</th>
									</tr>
									<tr>
										<td width="30%">Fréquence de mise à jour:</td>
										<td>
											<xsl:call-template name="maintenanceTypeTemplateFR">
												<xsl:with-param name="maintenanceTypeCode" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode/@codeListValue"/>
											</xsl:call-template>
										</td>
									</tr>
									<tr>
										<td width="30%">Remarque sur la mise à jour:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceNote/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<th colspan="2" scope="col">Origine des données</th>
									</tr>
									<tr>
										<td width="30%">Description des sources de données:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:LI_Lineage/gmd:LI_Lineage/gmd:source/gmd:LI_Source/gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Description de l'environnement de production:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:environmentDescription/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<th colspan="2" scope="col">L'étendue et le système de référence</th>
									</tr>
									<tr>
										<td width="30%">Coordonnées de l'étendue:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Latitude nord:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Latitude sud:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Longitude est:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Longitude ouest:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Système de référence:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:referenceSystemIdentifier/gmd:RS_Identifier/gmd:code/gco:CharacterString"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Echelle d'acquisition</td>
										<td>1:<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:spatialResolution/gmd:MD_Resolution/gmd:MD_RepresentativeFraction/gmd:denominator/gco:Decimal"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Résolution spatiale:</td>
										<td>
											<xsl:value-of select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:spatialResolution/gmd:MD_Resolution/gmd:distance/gco:CharacterString" />
										</td>
									</tr>
									<tr>
										<th colspan="2" scope="col">Information sur les données sous forme de grille</th>
									</tr>
									<tr>
										<td width="30%">Type de compression:</td>
										<td>
											<xsl:call-template name="CompressionCodeTemplateFR">
												<xsl:with-param name="CompressionCode"  select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/bee:compression/bee:compressionCode/@codeListValue"/>
											</xsl:call-template>
										</td>
									</tr>
									<tr>
										<td width="30%">Compression MrSID:</td>
										<td>
											<xsl:value-of  select="./gmd:MD_Metadata/gmd:distributionInfo/gmd:MD_Distribution/bee:SIDcompression/gco:Decimal"/>
										</td>
									</tr>
									<xsl:for-each select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_CoverageDescription/gmd:dimension/gmd:MD_Band">
										<tr>
											<td width="30%">Informations sur la couleur:</td>
											<td>
							Table des couleurs: <xsl:value-of  select="bee:colors/gco:CharacterString"/>
												<br />

							Nombre de canaux: <xsl:value-of  select="gmd:sequenceIdentifier/gco:CharacterString"/>
												<br />

							Bits par cellule: <xsl:value-of  select="gmd:bitsPerValue/gco:Decimal"/>


											</td>
										</tr>
									</xsl:for-each>
									<tr>
										<th colspan="2" scope="col">Représentation</th>
									</tr>
									<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic">
										<tr>
											<td width="30%">Force légale:</td>
											<td>
												<xsl:value-of  select="gmd:fileDescription/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']"/>
											</td>
										</tr>
										<tr>
											<td width="30%">Légende:</td>
											<td>
												<xsl:variable name="image">
													<xsl:call-template name="string-replace-all">
														<xsl:with-param name="text" select="bee:URL/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
														<xsl:with-param name="replace" select="$imgreplace" />
														<xsl:with-param name="by" select="''" />
													</xsl:call-template>
												</xsl:variable>

												<xsl:element name="img">
													<xsl:attribute name="src">
														<xsl:value-of select="$image" />
													</xsl:attribute>
													<xsl:attribute name="class">metadata-img-key</xsl:attribute>
												</xsl:element>
											</td>
										</tr>
									</xsl:for-each>







								</table>
							</div>

							<!-- ************************ Attributbeschreibung ******************************* -->

							<h3 class="trigger">
								<a class="icon show" title="Attributbeschreibungen anzeigen" href="/">Description des attributs</a>
							</h3>
							<div class="section">	
								<xsl:for-each select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/gmd:class/gmd:MD_Class/gmd:attribute/gmd:MD_Attribute">
									<table class="alternative">
										<tr>
											<th colspan="2" scope="col">Description des attributs pour <xsl:value-of select="gmd:name/gco:CharacterString" />
											</th>
										</tr>
										<tr>
											<td width="30%">Nom:</td>
											<td>
												<p>
													<xsl:value-of select="gmd:name/gco:CharacterString" />
												</p>
												<xsl:value-of select="gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
											</td>
										</tr>
										<tr>
											<td width="30%">Type d'attribut:</td>
											<td>
												<xsl:value-of select="gmd:anonymousType/gmd:MD_Type/gmd:type/gco:CharacterString" />
											</td>
										</tr>
										<tr>
											<td width="30%">Unité de masse:</td>
											<td>
												<xsl:value-of select="bee:unit/gco:CharacterString" />
											</td>
										</tr>
										<tr>
											<td width="30%">Requis:</td>
											<td>
												<xsl:choose>
													<xsl:when  test="bee:mandatory/gco:Boolean = 'oui'">
														<xsl:text>Oui</xsl:text>
													</xsl:when>
													<xsl:otherwise>
														<xsl:text>Non</xsl:text>
													</xsl:otherwise>
												</xsl:choose>
											</td>
										</tr>
										<tr>
											<td width="30%">Valeurs uniques:</td>
											<td>
												<xsl:choose>
													<xsl:when  test="bee:unique/gco:Boolean = 'yes'">
														<xsl:text>Oui</xsl:text>
													</xsl:when>
													<xsl:otherwise>
														<xsl:text>Non</xsl:text>
													</xsl:otherwise>
												</xsl:choose>
											</td>
										</tr>
										<tr>
											<td width="30%">Nombre de chiffres:</td>
											<td>
												<xsl:value-of select="bee:scale/gco:Decimal" />
											</td>
										</tr>
										<tr>
											<td width="30%">Nombre de décimales:</td>
											<td>
												<xsl:value-of select="bee:precision/gco:Decimal" />
											</td>
										</tr>
										<tr>
											<td width="30%">Nombre de caractères:</td>
											<td>
												<xsl:value-of select="bee:length/gco:Decimal" />
											</td>
										</tr>
										<tr>
											<td width="30%">Remarque:</td>
											<td>
												<xsl:value-of select="bee:remark/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
											</td>
										</tr>
										<tr>
											<td width="30%">Gamme:</td>
											<td>
												<xsl:value-of select="bee:range/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
											</td>
										</tr>
										<tr>
											<td colspan="2">
												<h3>Table de valeurs</h3>
											</td>
										</tr>
										<xsl:for-each select="gmd:namedType/gmd:MD_CodeDomain">
											<tr>
												<td width="30%">Tabel de valeurs:</td>
												<td>Nom: <xsl:value-of select="gmd:name/gco:CharacterString" />
													<br />
								Appelation: <xsl:value-of select="gmd:description/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
													<br />
								Join-Field: <xsl:value-of select="bee:primaryKey/gco:CharacterString" />
												</td>
											</tr>
										</xsl:for-each>


									</table>
								</xsl:for-each>
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Delta Checker</th>
									</tr>
									<tr>
										<td width="30%">Résultats du Delta Checker:</td>
										<td>
											<xsl:element name="a">
												<xsl:attribute name="class">extern</xsl:attribute>
												<xsl:attribute name="href">
													<xsl:value-of  select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/bee:deltacheckerReport/gco:CharacterString"/>
												</xsl:attribute>
												<xsl:value-of  select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/bee:deltacheckerReport/gco:CharacterString"/>
											</xsl:element>					
										</td>
									</tr>

								</table>
							</div>

							<!-- ************************ Kontaktinformationen ******************************* -->

							<h3 class="trigger">
								<a class="icon show" title="Contacts" href="/">Contacts</a>
							</h3>
							<div class="section">	
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Contacts</th>
									</tr>

									<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact">
										<tr>
											<xsl:call-template name="addressTemplateFR">
											</xsl:call-template>
										</tr>
									</xsl:for-each>	

								</table>
							</div>

							<!-- ************************ Relationen ******************************* -->


							<h3 class="trigger">
								<a class="icon show" title="Géoinformations en relations" href="/">Géoinformations en relation
								</a>
							</h3>

							<div class="section">

								<xsl:call-template name="relationshipTableFR">
								</xsl:call-template>
												</div>

							<!-- ************************ Informations sur les métadonnées ******************************* -->

							<h3 class="trigger">
								<a class="icon show" title="Informations sur les métadonnées anzeigen" href="/">Informations sur les métadonnées</a>
							</h3>
							<div class="section">
								<table class="alternative">
									<tr>
										<th colspan="2" scope="col">Informations sur les métadonnées</th>
									</tr>
									<xsl:for-each select="./gmd:MD_Metadata/gmd:contact">
										<tr>
											<xsl:call-template name="addressTemplateFR">
											</xsl:call-template>
										</tr>
									</xsl:for-each>

									<tr>
										<td width="30%">Fichier mis à jour le:</td>
										<td>
											<xsl:value-of select="$dateupdated" />
										</td>
									</tr>
									<tr>
										<td width="30%">Fichier créé le:</td>
										<td>
											<xsl:value-of select="$datecreated" />
										</td>
									</tr>
									<tr>
										<td width="30%">ID de la métadonnée:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString"/>
										</td>
									</tr>
									<tr>
										<td width="30%">Nom du standard de métadonnées:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:metadataStandardName/gco:CharacterString" />
										</td>
									</tr>
									<tr>
										<td width="30%">Version du standard de métadonnées:</td>
										<td>
											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:metadataStandardversion/gco:CharacterString" />
										</td>
									</tr>
									<tr>
										<td width="30%">Domaine des métadonnées:</td>
										<td>

											<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:hierarchyLevelName/gco:CharacterString" />

										</td>
									</tr>

								</table>
							</div>




							<!-- ************************ ENDE LAYER *********************** -->

						</xsl:when>
						<!-- Ende Angaben zum Layer -->
					</xsl:choose>
					<!-- Ende Auswahl nach Typ -->




					<!-- ZurÃ¼ck zur vorhergehenden Seite (leider geht hier nur der Aufruf via UA-History) -->
					<p>
					</p>
				</xsl:when>
			</xsl:choose>
		</div>	 

		<!-- Script for the open/close links -->
		<script type="text/javascript">
jQuery(document).ready(function(){

	//Hide (Collapse) the toggle containers on load
	jQuery(".section").hide(); 

	//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
	jQuery("h3.trigger").click(function(){
	jQuery(this).toggleClass("active").next().slideToggle("slow");
	return false; //Prevent the browser jump to the link anchor
	});

	//Show / Hide all (depending on open/close state)
	jQuery(".showall").click(function(){


    	if (jQuery("p.showall").is(".active")) {
		    jQuery("h3.trigger").removeClass("active");
		    jQuery(".section").slideUp("slow");

	    }
	    else{
		    jQuery("h3.trigger").addClass("active");
		    jQuery(".section").slideDown("slow");

	     }


    	jQuery(".showall").toggleClass("active");
	return false; //Prevent the browser jump to the link anchor
	});

	//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
	jQuery(".showsummary").click(function(){
	jQuery(this).toggleClass("active").next().slideToggle("slow");
	return false; //Prevent the browser jump to the link anchor
	});



});

		</script>

	</xsl:template>

	<xsl:template match="gmd:MD_Metadata/gmd:contact/gmd:CI_ResponsibleParty/gmd:address/gmd:CI_Address">
		<xsl:value-of disable-output-escaping="yes" select="gmd:streetName/gco:CharacterString"/>
		<xsl:text/>
		<xsl:value-of disable-output-escaping="yes" select="gmd:streetNumber/gco:CharacterString"/>
		<xsl:element name="br"/>
		<xsl:value-of disable-output-escaping="yes" select="gmd:addressLine/gco:CharacterString"/>
		<xsl:element name="br"/>
		<xsl:value-of disable-output-escaping="yes" select="gmd:postBox/gco:CharacterString"/>
		<xsl:element name="br"/>
		<xsl:value-of disable-output-escaping="yes" select="gmd:postalCode/gco:CharacterString"/>
		<xsl:text/>
		<xsl:value-of disable-output-escaping="yes" select="gmd:city/gco:CharacterString"/>
		<xsl:element name="br"/>
		<xsl:value-of disable-output-escaping="yes" select="gmd:country/gco:CharacterString"/>
	</xsl:template>

	<xsl:template match="gmd:CI_Date/gmd:date">
		<xsl:value-of disable-output-escaping="yes" select="date:day-in-month(gco:Date)"/>.<xsl:value-of disable-output-escaping="yes" select="date:month-in-year(gco:Date)"/>.<xsl:value-of disable-output-escaping="yes" select="date:year(gco:Date)"/>
	</xsl:template>



	<!-- Template DataTypeCodeFR -->
	<xsl:template name="DataTypeCodeTemplateFR">
		<xsl:param name="DataTypeCode"/>
		<xsl:choose>
			<xsl:when test="$DataTypeCode = 'class'">
				<xsl:text>classe</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'codelist'">
				<xsl:text>Liste de codes</xsl:text>	
			</xsl:when>	
			<xsl:when test="$DataTypeCode = 'enumeration'">
				<xsl:text>Enumération</xsl:text>	
			</xsl:when>	
			<xsl:when test="$DataTypeCode = 'codelistElement'">
				<xsl:text>Elément d'une liste</xsl:text>	
			</xsl:when>		
			<xsl:when test="$DataTypeCode = 'abstractClass'">
				<xsl:text>Classe abstraite</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'aggregatedClass'">
				<xsl:text>Classe globale</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'specifiedClass'">
				<xsl:text>Classe spécifique</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'datatypeClass'">
				<xsl:text>Classe d'un type de données</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'interfaceClass'">
				<xsl:text>Classe d'interface</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'unionClass'">
				<xsl:text>Classe d'union</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'metaClass'">
				<xsl:text>Métaclasse</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'typeClass'">
				<xsl:text>Classe de type</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'characterString'">
				<xsl:text>Texte libre</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'integer'">
				<xsl:text>Entier</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'association'">
				<xsl:text>Association</xsl:text>
			</xsl:when>	
			<xsl:otherwise>
				<xsl:text/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>



	<!-- Template DataTypeCode -->
	<xsl:template name="DataTypeCodeTemplate">
		<xsl:param name="DataTypeCode"/>
		<xsl:choose>
			<xsl:when test="$DataTypeCode = 'class'">
				<xsl:text>Klasse</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'codelist'">
				<xsl:text>Liste der Codes</xsl:text>	
			</xsl:when>	
			<xsl:when test="$DataTypeCode = 'enumeration'">
				<xsl:text>AufzÃ¤hlung</xsl:text>	
			</xsl:when>	
			<xsl:when test="$DataTypeCode = 'codelistElement'">
				<xsl:text>Auswahllistenelement</xsl:text>	
			</xsl:when>		
			<xsl:when test="$DataTypeCode = 'abstractClass'">
				<xsl:text>Abstrakte Klasse</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'aggregatedClass'">
				<xsl:text>Gesamtklasse</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'specifiedClass'">
				<xsl:text>Spezifische Klasse</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'datatypeClass'">
				<xsl:text>Datentypklasse</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'interfaceClass'">
				<xsl:text>Schnittstellenklasse</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'unionClass'">
				<xsl:text>Vereinigungsklasse</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'metaClass'">
				<xsl:text>Metaklasse</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'typeClass'">
				<xsl:text>Typenklasse</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'characterString'">
				<xsl:text>Textfeld</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'integer'">
				<xsl:text>Ganzzahl</xsl:text>
			</xsl:when>
			<xsl:when test="$DataTypeCode = 'association'">
				<xsl:text>Beziehung</xsl:text>
			</xsl:when>	
			<xsl:otherwise>
				<xsl:text>Unbekannt</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Template ObligationCodeFR -->
	<xsl:template name="obligationCodeTemplateFR">
		<xsl:param name="obligationCode"/>
		<xsl:choose>
			<xsl:when test="$obligationCode = 'optional'">
				<xsl:text>Optionnel</xsl:text>
			</xsl:when>
			<xsl:when test="$obligationCode = 'mandatory'">
				<xsl:text>Obligatoire</xsl:text>	
			</xsl:when>	
			<xsl:when test="$obligationCode = 'conditionnal'">
				<xsl:text>Conditionnel</xsl:text>	
			</xsl:when>			
			<xsl:otherwise>
				<xsl:text/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<!-- Template ObligationCode -->
	<xsl:template name="obligationCodeTemplate">
		<xsl:param name="obligationCode"/>
		<xsl:choose>
			<xsl:when test="$obligationCode = 'optional'">
				<xsl:text>Optional</xsl:text>
			</xsl:when>
			<xsl:when test="$obligationCode = 'mandatory'">
				<xsl:text>Obligatorisch</xsl:text>	
			</xsl:when>	
			<xsl:when test="$obligationCode = 'conditionnal'">
				<xsl:text>AbhÃ¤ngig</xsl:text>	
			</xsl:when>			
			<xsl:otherwise>
				<xsl:text>Unbekannt</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Template CompressionCode -->
	<xsl:template name="CompressionCodeTemplateFR">
		<xsl:param name="CompressionCode"/>
		<xsl:choose>
			<xsl:when test="$CompressionCode = 'JPEG'">
				<xsl:text>JPEG</xsl:text>
			</xsl:when>
			<xsl:when test="$CompressionCode = 'LZ77'">
				<xsl:text>LZ77</xsl:text>	
			</xsl:when>	

			<xsl:otherwise>
				<xsl:text>Inconnu</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Template CompressionCode -->
	<xsl:template name="CompressionCodeTemplate">
		<xsl:param name="CompressionCode"/>
		<xsl:choose>
			<xsl:when test="$CompressionCode = 'JPEG'">
				<xsl:text>Verlustbehaftete JPEG-Komprimierung</xsl:text>
			</xsl:when>
			<xsl:when test="$CompressionCode = 'LZ77'">
				<xsl:text>Verlustfreie Komprimierung nach dem Lempel-Ziv-Algorithmus</xsl:text>	
			</xsl:when>	

			<xsl:otherwise>
				<xsl:text>Unbekannte Komprimierung</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Template CharacterSetFR -->
	<xsl:template name="CharacterSetTemplateFR">
		<xsl:param name="CharacterSetCode"/>
		<xsl:choose>
			<xsl:when test="$CharacterSetCode = 'usc2'">
				<xsl:text>Jeu de caractères universel à 16 bits, basé sur la norme ISO 10646</xsl:text>
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'usc4'">
				<xsl:text>Jeu de caractères universel à 32 bits, basé sur la norme ISO 10646</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = 'utf7'">
				<xsl:text>Jeu de caractères universel à 7 bits, de taille variable, basé sur la norme ISO 10646</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'utf8'">
				<xsl:text>Jeu de caractères universel à 8 bits, de taille variable, basé sur la norme ISO 10646</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = 'utf16'">
				<xsl:text>Jeu de caractères universel à 16 bits, de taille variable, basé sur la norme ISO 10646</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part1'">
				<xsl:text>ISO/IEC 8859-1, TI - jeux de caractères graphiques codés sur un seul octet - partie 1 : alphabet latin nÂ°1</xsl:text>	
			</xsl:when>		
			<xsl:when test="$CharacterSetCode = '8859part2'">
				<xsl:text>ISO/IEC 8859-2, TI - jeux de caractères graphiques codés sur un seul octet - partie 2 : alphabet latin nÂ°2</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part3'">
				<xsl:text>ISO/IEC 8859-3, TI - jeux de caractères graphiques codés sur un seul octet - partie 3 : alphabet latin nÂ°3</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part4'">
				<xsl:text>ISO/IEC 8859-4, TI - jeux de caractères graphiques codés sur un seul octet - partie 4 : alphabet latin nÂ°4</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part5'">
				<xsl:text>ISO/IEC 8859-5, TI - jeux de caractères graphiques codés sur un seul octet - partie 5 : alphabet latin / cyrillique</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part6'">
				<xsl:text>ISO/IEC 8859-6, TI - jeux de caractères graphiques codés sur un seul octet - partie 6 : alphabet latin / arabe</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part7'">
				<xsl:text>ISO/IEC 8859-7, TI - jeux de caractères graphiques codés sur un seul octet - partie 7 : alphabet latin / grec</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part8'">
				<xsl:text>ISO/IEC 8859-8, TI - jeux de caractères graphiques codés sur un seul octet - partie 8 : alphabet latin / hébreu</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part9'">
				<xsl:text>ISO/IEC 8859-9, TI - jeux de caractères graphiques codés sur un seul octet - partie 9 : alphabet latin nÂ°5</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part11'">
				<xsl:text>ISO/IEC 8859-11, TI - jeux de caractères graphiques codés sur un seul octet - partie 11 : alphabet latin / thaÃ¯</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part14'">
				<xsl:text>ISO/IEC 8859-14, TI - jeux de caractères graphiques codés sur un seul octet - partie 14 : alphabet latin nÂ°8 (celtique)</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part15'">
				<xsl:text>ISO/IEC 8859-15, TI - jeux de caractères graphiques codés sur un seul octet - partie 15 : alphabet latin nÂ°9</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'jis'">
				<xsl:text>Jeu de codes japonais pour la transmission informatique</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'shiftJIS'">
				<xsl:text>Jeu de codes japonais pour les ordinateurs MS-DOS</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'eucJP'">
				<xsl:text>Jeu de codes japonais pour les ordinateurs UNIX</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'usAscii'">
				<xsl:text>Code ASCII des Etats-Unis (ISO 646 US)</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'ebcdic'">
				<xsl:text>Jeu de codes pour unité centrale IBM</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'eucKR'">
				<xsl:text>Jeu de codes coréen</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'big5'">
				<xsl:text>Jeu de codes chinois traditionnel, utilisé à TaÃ¯wan, Hong Kong et en dâ€™autres lieux</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part10'">
				<xsl:text>ISO/IEC 8859-10, TI - jeux de caractères graphiques codés sur un seul octet - partie 10 : alphabet latin nÂ°6</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part13'">
				<xsl:text>ISO/IEC 8859-13, TI - jeux de caractères graphiques codés sur un seul octet - partie 13 : alphabet latin nÂ°7</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part16'">
				<xsl:text>ISO/IEC 8859-16, TI - jeux de caractères graphiques codés sur un seul octet - partie 16 : alphabet latin nÂ°10</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'GB2312'">
				<xsl:text>Jeu de caractères chinois simplifié</xsl:text>	
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Innconu</xsl:text>
			</xsl:otherwise>
		</xsl:choose>


	</xsl:template>

	<!-- Template CharacterSet -->
	<xsl:template name="CharacterSetTemplate">
		<xsl:param name="CharacterSetCode"/>
		<xsl:choose>
			<xsl:when test="$CharacterSetCode = 'usc2'">
				<xsl:text>16-Bit Zeichensatz, universell, basierend auf ISO 10646</xsl:text>
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'usc4'">
				<xsl:text>32-Bit Zeichensatz, universell, basierend auf ISO 10646</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = 'utf7'">
				<xsl:text>7-Bit Zeichensatz mit variabler GrÃ¶sse, universell, basierend auf ISO 10646</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'utf8'">
				<xsl:text>8-Bit Zeichensatz mit variabler GrÃ¶sse, universell, basierend auf ISO 10646</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = 'utf16'">
				<xsl:text>16-Bit Zeichensatz mit variabler GrÃ¶sse, universell, basierend auf ISO 10646</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part1'">
				<xsl:text>ISO/IEC 8859-1, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 1: Lateinisches Alphabet Nr. 1</xsl:text>	
			</xsl:when>		
			<xsl:when test="$CharacterSetCode = '8859part2'">
				<xsl:text>ISO/IEC 8859-2, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 2: Lateinisches Alphabet Nr. 2</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part3'">
				<xsl:text>ISO/IEC 8859-3, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 3: Lateinisches Alphabet Nr. 3</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part4'">
				<xsl:text>ISO/IEC 8859-4, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 4: Lateinisches Alphabet Nr. 4</xsl:text>	
			</xsl:when>		
			<xsl:when test="$CharacterSetCode = '8859part5'">
				<xsl:text>ISO/IEC 8859-5, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 5: Lateinisch/ Kyrillisches Alphabet</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part6'">
				<xsl:text>ISO/IEC 8859-6, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 6: Lateinisch/ Arabisches Alphabet</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part7'">
				<xsl:text>ISO/IEC 8859-7, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 7: Lateinisch/ Griechisches Alphabet</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part8'">
				<xsl:text>ISO/IEC 8859-8, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 8: Lateinisch/ HebrÃ¤isch Alphabet</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part9'">
				<xsl:text>ISO/IEC 8859-9, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 9: Lateinisches Alphabet Nr. 5</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part11'">
				<xsl:text>ISO/IEC 8859-11, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 11: Lateinisch/ ThailÃ¤ndisch Alphabet</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part14'">
				<xsl:text>ISO/IEC 8859-14, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 14: Lateinisches Alphabet Nr. 8 (Keltisch)</xsl:text>	
			</xsl:when>	
			<xsl:when test="$CharacterSetCode = '8859part15'">
				<xsl:text>ISO/IEC 8859-15, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 15: Lateinisches Alphabet Nr. 9</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'jis'">
				<xsl:text>Japanischer Codierungssatz fÃ¼r elektronische Transmission</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'shiftJIS'">
				<xsl:text>Japanischer Codierungssatz fÃ¼r MS-DOS-Rechner</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'eucJP'">
				<xsl:text>Japanischer Codierungssatz fÃ¼r UNIX-Rechner</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'usAscii'">
				<xsl:text>ASCII-Code der Vereinigten Staaten (ISO 646 US)</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'ebcdic'">
				<xsl:text>IBM-Mainframe Codierungssatz</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'eucKR'">
				<xsl:text>Koreanischer Codierungssatz</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'big5'">
				<xsl:text>Traditioneller chinesischer Codierungssatz, benutzt in Taiwan, Hong Kong und anderen Regionen</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part10'">
				<xsl:text>ISO/IEC 8859-10, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 10: Lateinisches Alphabet Nr. 6</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part13'">
				<xsl:text>ISO/IEC 8859-13, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 13: Lateinisches Alphabet Nr. 7</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = '8859part16'">
				<xsl:text>ISO/IEC 8859-16, IT - 8-Bit Einzelbyte codierter graphischer Zeichensatz - Teil 16: Lateinisches Alphabet Nr. 10</xsl:text>	
			</xsl:when>
			<xsl:when test="$CharacterSetCode = 'GB2312'">
				<xsl:text>Vereinfachter Chinesischer Zeichensatz</xsl:text>	
			</xsl:when>		
			<xsl:otherwise>
				<xsl:text>Undefinierter Zeichensatz</xsl:text>
			</xsl:otherwise>
		</xsl:choose>


	</xsl:template>


	<!-- Template MaintenanceFrequencyCodeFR -->
	<xsl:template name="maintenanceFrequencyCodeTemplateFR">
		<xsl:param name="maintenanceFrequencyCode"/>
		<xsl:choose>
			<xsl:when test="$maintenanceFrequencyCode = 'continual'">

			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'daily'">
				<xsl:text>Quotidienne</xsl:text>	
			</xsl:when>		
			<xsl:when test="$maintenanceFrequencyCode = 'weekly'">
				<xsl:text>Hebdomadaire</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'fortnightly'">
				<xsl:text>Bimensuelle</xsl:text>	
			</xsl:when>	
			<xsl:when test="$maintenanceFrequencyCode = 'monthly'">
				<xsl:text>Mensuelle</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'quarterly'">
				<xsl:text>Trimestrielle</xsl:text>	
			</xsl:when>	
			<xsl:when test="$maintenanceFrequencyCode = 'biannually'">
				<xsl:text>Semestrielle</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'annually'">
				<xsl:text>Annuelle</xsl:text>	
			</xsl:when>	
			<xsl:when test="$maintenanceFrequencyCode = 'asNeeded'">
				<xsl:text>Au besoin</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'irregular'">
				<xsl:text>Irrégulière</xsl:text>	
			</xsl:when>	
			<xsl:when test="$maintenanceFrequencyCode = 'notPlanned'">
				<xsl:text>non planifiée</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'unknown'">
				<xsl:text>inconnue</xsl:text>
			</xsl:when>	
			<xsl:when test="$maintenanceFrequencyCode = 'userDefined'">
				<xsl:text>Définie par l'utilisateur</xsl:text>
			</xsl:when>	
			<xsl:otherwise>
				<xsl:value-of disable-output-escaping="yes" select="$maintenanceFrequencyCode"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Template MaintenanceFrequencyCode -->
	<xsl:template name="maintenanceFrequencyCodeTemplate">
		<xsl:param name="maintenanceFrequencyCode"/>
		<xsl:choose>
			<xsl:when test="$maintenanceFrequencyCode = 'continual'">
				<xsl:text>Laufend</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'daily'">
				<xsl:text>TÃ¤glich</xsl:text>	
			</xsl:when>		
			<xsl:when test="$maintenanceFrequencyCode = 'weekly'">
				<xsl:text>WÃ¶chentlich</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'fortnightly'">
				<xsl:text>VierzehntÃ¤glich</xsl:text>	
			</xsl:when>	
			<xsl:when test="$maintenanceFrequencyCode = 'monthly'">
				<xsl:text>Monatlich</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'quarterly'">
				<xsl:text>VierteljÃ¤hrlich</xsl:text>	
			</xsl:when>	
			<xsl:when test="$maintenanceFrequencyCode = 'biannually'">
				<xsl:text>HalbjÃ¤hrlich</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'annually'">
				<xsl:text>JÃ¤hrlich</xsl:text>	
			</xsl:when>	
			<xsl:when test="$maintenanceFrequencyCode = 'asNeeded'">
				<xsl:text>Wenn NÃ¶tig</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'irregular'">
				<xsl:text>UnregelmÃ¤ssig</xsl:text>	
			</xsl:when>	
			<xsl:when test="$maintenanceFrequencyCode = 'notPlanned'">
				<xsl:text>Nicht geplant</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceFrequencyCode = 'unknown'">
				<xsl:text>Unbekannt</xsl:text>	
			</xsl:when>	
			<xsl:when test="$maintenanceFrequencyCode = 'userDefined'">
				<xsl:text>Benutzerdefiniert</xsl:text>
			</xsl:when>	
			<xsl:otherwise>
				<xsl:value-of disable-output-escaping="yes" select="$maintenanceFrequencyCode" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template name="maintenanceTypeTemplateFR">
		<xsl:param name="maintenanceTypeCode"/>
		<xsl:choose>
			<xsl:when test="$maintenanceTypeCode = 'continual'">
				<xsl:text>Mise à jour en continu</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceTypeCode = 'notPlanned'">
				<xsl:text>Mise à jour non planifiée</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceTypeCode = 'unknown'">
				<xsl:text>Mise à jour Inconnue</xsl:text>	
			</xsl:when>	
			<xsl:otherwise>
				<xsl:text>Mise à jour périodique</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>				

	<xsl:template name="maintenanceTypeTemplate">
		<xsl:param name="maintenanceTypeCode"/>
		<xsl:choose>
			<xsl:when test="$maintenanceTypeCode = 'continual'">
				<xsl:text>Laufende NachfÃ¼hrung</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceTypeCode = 'notPlanned'">
				<xsl:text>Keine NachfÃ¼rhung geplant</xsl:text>
			</xsl:when>
			<xsl:when test="$maintenanceTypeCode = 'unknown'">
				<xsl:text>NachfÃ¼hrungsfrequenz ist unbekannt</xsl:text>	
			</xsl:when>	
			<xsl:otherwise>
				<xsl:text>periodische NachfÃ¼hrung</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template name="legislationTypeTemplateFR">
		<xsl:param name="legislationTypeCode"/>
		<xsl:choose>
			<xsl:when test="$legislationTypeCode = 'bylawsPrivatLaw'">
				<xsl:text>Statuts de droit privé</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'bylawsPublicLaw'">
				<xsl:text>Statuts de droit public</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'communalLaw'">
				<xsl:text>Loi communale</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'cantonalLaw'">
				<xsl:text>Loi cantonale</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'nationalLaw'">
				<xsl:text>Loi nationale</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'nationalDecree'">
				<xsl:text>Décret national</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'internationalObligation'">
				<xsl:text>Obligation internationale</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'otherLegalText'">
				<xsl:text>Autre texte législatif</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Inconnue</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template name="legislationTypeTemplate">
		<xsl:param name="legislationTypeCode"/>
		<xsl:choose>
			<xsl:when test="$legislationTypeCode = 'bylawsPrivatLaw'">
				<xsl:text>Privat-rechtliche Statuten</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'bylawsPublicLaw'">
				<xsl:text>Ã–ffentlich-rechtliche Statuten</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'communalLaw'">
				<xsl:text>Kommunales Gesetz</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'cantonalLaw'">
				<xsl:text>Kantonales Gesetz</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'nationalLaw'">
				<xsl:text>Nationales Gesetz</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'nationalDecree'">
				<xsl:text>Nationale Verordnungl</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'internationalObligation'">
				<xsl:text>Internationales Abkommen</xsl:text>
			</xsl:when>
			<xsl:when test="$legislationTypeCode = 'otherLegalText'">
				<xsl:text>Sonstiger Gesetzestext</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Unbekannt</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="languageISOCodeTemplateFR">
		<xsl:param name="languageISOCode"/>
		<xsl:choose>
			<xsl:when test="$languageISOCode = 'ger'">
				<xsl:text>Allemand</xsl:text>
			</xsl:when>
			<xsl:when test="$languageISOCode = 'fre'">
				<xsl:text>Français</xsl:text>
			</xsl:when>
			<xsl:when test="$languageISOCode = 'fra'">
				<xsl:text>Français</xsl:text>
			</xsl:when>
			<xsl:when test="$languageISOCode = 'eng'">
				<xsl:text>Anglais</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Autre langue</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	


	<xsl:template name="languageISOCodeTemplate">
		<xsl:param name="languageISOCode"/>
		<xsl:choose>
			<xsl:when test="$languageISOCode = 'ger'">
				<xsl:text>Deutsch</xsl:text>
			</xsl:when>
			<xsl:when test="$languageISOCode = 'fre'">
				<xsl:text>FranzÃ¶sisch</xsl:text>
			</xsl:when>
			<xsl:when test="$languageISOCode = 'fra'">
				<xsl:text>FranzÃ¶sisch</xsl:text>
			</xsl:when>
			<xsl:when test="$languageISOCode = 'eng'">
				<xsl:text>Englisch</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Andere Sprache</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	

	<xsl:template name="SpatialRepresentationTypeTemplateFR">
		<xsl:param name="SpatialRepresentationType"/>
		<xsl:choose>
			<xsl:when test="$SpatialRepresentationType = 'vector'">
				<xsl:text>Données vectorielles</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'grid'">
				<xsl:text>Données tramées</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'textTable'">
				<xsl:text>Texte ou tableau</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'tin'">
				<xsl:text>Maillage triangulaire irrégulier</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'stereoModel'">
				<xsl:text>Vue tridimensionnelle résultant de deux clichés stéréoscopiques</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'video'">
				<xsl:text>Scène dâ€™un enregistrement vidéo</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'paperMap'">
				<xsl:text>Carte imprimée</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$SpatialRepresentationType" />
				<xsl:text> (Inconnue)</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	

	<xsl:template name="SpatialRepresentationTypeTemplate">
		<xsl:param name="SpatialRepresentationType"/>
		<xsl:choose>
			<xsl:when test="$SpatialRepresentationType = 'vector'">
				<xsl:text>Vektordaten</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'grid'">
				<xsl:text>Rasterdaten</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'textTable'">
				<xsl:text>Text oder Tabelle</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'tin'">
				<xsl:text>UnregelmÃ¤ssige Dreiecksvermaschung</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'stereoModel'">
				<xsl:text>3D-Sicht, entstanden aus 2 Stereobildern</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'video'">
				<xsl:text>Szene einer Videoaufnahme</xsl:text>
			</xsl:when>
			<xsl:when test="$SpatialRepresentationType = 'paperMap'">
				<xsl:text>Gedruckte Karte</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$SpatialRepresentationType" />
				<xsl:text> (unbekanntes Format)</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	

	<xsl:template name="dataTypeTemplateFR">
		<xsl:param name="dataType"/>
		<xsl:choose>
			<xsl:when test="$dataType = 'Pt'">
				<xsl:text>Point</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'MR'">
				<xsl:text>MosaÃ¯que de raster</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'CR'">
				<xsl:text>Catalogue de raster</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'P'">
				<xsl:text>Polygone</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'L'">
				<xsl:text>Ligne</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'A'">
				<xsl:text>Annotation</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'I'">
				<xsl:text>Type inconnu</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'T'">
				<xsl:text>Table</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Type inconnu</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	


	<xsl:template name="dataTypeTemplate">
		<xsl:param name="dataType"/>
		<xsl:choose>
			<xsl:when test="$dataType = 'Pt'">
				<xsl:text>Punkt</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'MR'">
				<xsl:text>Rastermosaik</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'CR'">
				<xsl:text>Rasterkatalog</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'P'">
				<xsl:text>Polygon</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'L'">
				<xsl:text>Linie</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'A'">
				<xsl:text>Annotation</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'I'">
				<xsl:text>Unbekannter Datentyp</xsl:text>
			</xsl:when>
			<xsl:when test="$dataType = 'T'">
				<xsl:text>Tabelle</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Unbekannter Datentyp</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	


	<xsl:template name="dateTypeCodeTemplateFR">
		<xsl:param name="dateTypeCode"/>
		<xsl:choose>
			<xsl:when test="$dateTypeCode = 'publication'">
				<xsl:text>Publication</xsl:text>
			</xsl:when>
			<xsl:when test="$dateTypeCode = 'creation'">
				<xsl:text>Création</xsl:text>
			</xsl:when>
			<xsl:when test="$dateTypeCode = 'revision'">
				<xsl:text>Révison</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Inconnue</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	


	<xsl:template name="dateTypeCodeTemplate">
		<xsl:param name="dateTypeCode"/>
		<xsl:choose>
			<xsl:when test="$dateTypeCode = 'publication'">
				<xsl:text>Publikation</xsl:text>
			</xsl:when>
			<xsl:when test="$dateTypeCode = 'creation'">
				<xsl:text>Erstellung</xsl:text>
			</xsl:when>
			<xsl:when test="$dateTypeCode = 'revision'">
				<xsl:text>Ãœberarbeitung</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Unbekannter Datumstyp</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>			



	<xsl:template name="RoleCodeTemplateFR">
		<xsl:param name="RoleCode"/>
		<xsl:choose>
			<xsl:when test="$RoleCode = 'pointOfContact'">
				<xsl:text>Point de contact</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'resourceProvider'">
				<xsl:text>Fournisseur</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'custodian'">
				<xsl:text>Administrateur</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'owner'">
				<xsl:text>Propriétaire</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'user'">
				<xsl:text>Utilisateur</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'distributor'">
				<xsl:text>Distributeur</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'originator'">
				<xsl:text>Créateur des données</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'principalInvestigator'">
				<xsl:text>Analyste principal</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'processor'">
				<xsl:text>Responsable du traitement</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'publisher'">
				<xsl:text>Editeur (publication)</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'author'">
				<xsl:text>Auteur</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'editor'">
				<xsl:text>Editeur</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'partner'">
				<xsl:text>Partenaire</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Inconnue</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="RoleCodeTemplate">
		<xsl:param name="RoleCode"/>
		<xsl:choose>
			<xsl:when test="$RoleCode = 'pointOfContact'">
				<xsl:text>ZustÃ¤ndigkeit</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'resourceProvider'">
				<xsl:text>Anbieter</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'custodian'">
				<xsl:text>Verwalter</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'owner'">
				<xsl:text>EigentÃ¼mer</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'user'">
				<xsl:text>Anwender</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'distributor'">
				<xsl:text>Vertreiber</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'originator'">
				<xsl:text>Datenerzeuger</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'principalInvestigator'">
				<xsl:text>Datenermittler</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'processor'">
				<xsl:text>Bearbeiter</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'publisher'">
				<xsl:text>Herausgeber</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'author'">
				<xsl:text>Autor</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'editor'">
				<xsl:text>Editor</xsl:text>
			</xsl:when>
			<xsl:when test="$RoleCode = 'partner'">
				<xsl:text>Partner</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>Undefiniert</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="ProgressCodeTemplateFR">
		<xsl:param name="ProgressCode"/>
		<xsl:choose>
			<xsl:when test="$ProgressCode = 'completed'">
				<xsl:text>Production achevée</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'historicalArchive'">
				<xsl:text>Données archivées hors ligne</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'obsolete'">
				<xsl:text>Données ayant perdu toute actualité</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'onGoing'">
				<xsl:text>Données actualisées en continu</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'planned'">
				<xsl:text>Date de génération ou dâ€™actualisation prévue</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'required'">
				<xsl:text>Données à générer ou à actualiser</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'underDevelopment'">
				<xsl:text>Données en cours de traitement</xsl:text>
			</xsl:when>													
			<xsl:otherwise>
				<xsl:text>Inconnue</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template name="ProgressCodeTemplate">
		<xsl:param name="ProgressCode"/>
		<xsl:choose>
			<xsl:when test="$ProgressCode = 'completed'">
				<xsl:text>Produktion ist abgeschlossen</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'historicalArchive'">
				<xsl:text>Daten sind in offline archiviert</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'obsolete'">
				<xsl:text>Daten sind nicht mehr relevant</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'onGoing'">
				<xsl:text>Daten werden laufen aktualisiert</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'planned'">
				<xsl:text>Datum der Erstellung oder Aktualisierung ist geplant</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'required'">
				<xsl:text>Daten mÃ¼ssen erstellt oder aktualisiert werden</xsl:text>
			</xsl:when>
			<xsl:when test="$ProgressCode = 'underDevelopment'">
				<xsl:text>Daten sind in Bearbeitung</xsl:text>
			</xsl:when>													
			<xsl:otherwise>
				<xsl:text>Undefiniert</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>




	<!-- Template CategoryCode -->
	<xsl:template name="categoryCodeTemplateFR">
		<xsl:param name="categoryCode"/>
		<xsl:choose>
			<xsl:when test="$categoryCode = 'farming'">
				<xsl:text>Agriculture</xsl:text>
			</xsl:when>
			<xsl:when test="$categoryCode = 'biota'">
				<xsl:text>Biologie</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'bounderies'">
				<xsl:text>Limites</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'climatologyMeteorologyAtmosphere'">
				<xsl:text>Climatologie/météorologie</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'economy'">
				<xsl:text>Economie</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'elevation'">
				<xsl:text>Altimétrie</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'environment'">
				<xsl:text>Environnement</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'geoscientificinformation'">
				<xsl:text>Sciences de la Terre</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'health'">
				<xsl:text>Santé</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'imageryBaseMapsEarthCover'">
				<xsl:text>Cartes de base, imagerie</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'intelligenceMilitary'">
				<xsl:text>Activités militaires</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'inlandWaters'">
				<xsl:text>Eaux intérieures</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'location'">
				<xsl:text>Localisation</xsl:text>	
			</xsl:when>			
			<xsl:when test="$categoryCode = 'oceans'">
				<xsl:text>Océans</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'planningCadastre'">
				<xsl:text>Cadastre, aménagement</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'society'">
				<xsl:text>Société</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'structure'">
				<xsl:text>Constructions et ouvrages</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'transportation'">
				<xsl:text>Transport</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'utilitiesCommunication'">
				<xsl:text>Réseau de distribution et d'évacuation</xsl:text>	
			</xsl:when>	
			<xsl:otherwise>
				<xsl:text>Inconnu</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="categoryCodeTemplate">
		<xsl:param name="categoryCode"/>
		<xsl:choose>
			<xsl:when test="$categoryCode = 'farming'">
				<xsl:text>Landwirtschaft</xsl:text>
			</xsl:when>
			<xsl:when test="$categoryCode = 'biota'">
				<xsl:text>Biologie</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'bounderies'">
				<xsl:text>Grenzen</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'climatologyMeteorologyAtmosphere'">
				<xsl:text>Klimatologie / Meteorologie</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'economy'">
				<xsl:text>Wirtschaft</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'elevation'">
				<xsl:text>HÃ¶henangaben</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'environment'">
				<xsl:text>Umwelt</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'geoscientificinformation'">
				<xsl:text>Erdwissenschaft</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'health'">
				<xsl:text>Gesundheit</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'imageryBaseMapsEarthCover'">
				<xsl:text>Basiskarten; Bsp: Bodenbedeckung, Topographische Karten, Bilder, unklassifizierte Bilder, Anmerkungen, etc</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'intelligenceMilitary'">
				<xsl:text>AufklÃ¤rung MilitÃ¤r</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'inlandWaters'">
				<xsl:text>BinnengewÃ¤sser</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'location'">
				<xsl:text>Ortsangaben</xsl:text>	
			</xsl:when>			
			<xsl:when test="$categoryCode = 'oceans'">
				<xsl:text>Meere</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'planningCadastre'">
				<xsl:text>Plaungskataster</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'society'">
				<xsl:text>Gesellschaft</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'structure'">
				<xsl:text>Konstruktion / Bauten</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'transportation'">
				<xsl:text>Transport</xsl:text>	
			</xsl:when>	
			<xsl:when test="$categoryCode = 'utilitiesCommunication'">
				<xsl:text>Strom / Ver- und Entsorgung</xsl:text>	
			</xsl:when>	
			<xsl:otherwise>
				<xsl:text>Unbekannt</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- SCOPECODE -->
	<xsl:template name="ScopeCodeTemplate">
		<xsl:param  name="ScopeCode" />
		<xsl:choose>
			<xsl:when test="$ScopeCode='attribut'">
				<xsl:text>Attribut	</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='attributeType'">
				<xsl:text>Attributs-Typ</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='collectionHardware'">
				<xsl:text>Erfassungs-Hardware</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='collectionSession'">
				<xsl:text>Erfassungs-Session</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='dataset'">
				<xsl:text>Datenbestand</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='series'">
				<xsl:text>Serie</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='nonGeographicDataset'">
				<xsl:text>Nichtgeografischer Datenbestand</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='dimensionGroup'">
				<xsl:text>Dimensiongruppe</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='feature'">
				<xsl:text>Objekt</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='featureType'">
				<xsl:text>Objekttyp</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='propertyType'">
				<xsl:text>Merkmalstyp</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='fieldSession'">
				<xsl:text>Feldkampagne</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='software'">
				<xsl:text>Software</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='service'">
				<xsl:text>Dienstleitsung</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='model'">
				<xsl:text>Modell</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='tile'">
				<xsl:text>Kachel</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='project'">
				<xsl:text>Projekt</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='stationSite'">
				<xsl:text>Station</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='publication'">
				<xsl:text>Publikation</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				 			Kein definierter Metadatenbereich
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="ScopeCodeTemplateFR">
		<xsl:param  name="ScopeCode" />
		<xsl:choose>
			<xsl:when test="$ScopeCode='attribut'">
				<xsl:text>Attribut	</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='attributeType'">
				<xsl:text>Type d'attribut</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='collectionHardware'">
				<xsl:text>Equipement de saisie</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='collectionSession'">
				<xsl:text>Session de saisie</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='dataset'">
				<xsl:text>Jeu de données</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='series'">
				<xsl:text>Série</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='nonGeographicDataset'">
				<xsl:text>Jeu de données non géographique</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='dimensionGroup'">
				<xsl:text>Groupe de dimension</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='feature'">
				<xsl:text>Objet</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='featureType'">
				<xsl:text>Type d'objet</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='propertyType'">
				<xsl:text>Type de propriété</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='fieldSession'">
				<xsl:text>Campagne de terrain</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='software'">
				<xsl:text>Logiciel</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='service'">
				<xsl:text>Service</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='model'">
				<xsl:text>Modèle</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='tile'">
				<xsl:text>Elément dâ€™une mosaÃ¯que</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='project'">
				<xsl:text>Projet</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='stationSite'">
				<xsl:text>Station</xsl:text>
			</xsl:when>
			<xsl:when test="$ScopeCode='publication'">
				<xsl:text>Publication</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				 			Inconnu
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- Relationen in einer Tabelle darstellen -->
	<xsl:template name="relationshipTable">
		<!-- Variable Declaration -->
		<xsl:variable name="logo">
			<xsl:value-of select="./sdi:Metadata/sdi:objecttype/@code" />
		</xsl:variable>

		<table class="alternative">
			<tr>
				<th colspan="2" scope="col">In Beziehung stehende Geoinformationen</th>

			</tr>
			<xsl:choose>
				<xsl:when test="$logo='geoproduct' or $logo='map'"> 
					<!-- There are only child-nodes for a geoproduct and maps -->
					<xsl:for-each select="./sdi:Metadata/sdi:links/sdi:child">
						<tr>
							<td width="30%">
								<xsl:if test="$logo='geoproduct'">
			  								ZugehÃ¶rige Ebene: &space;
								</xsl:if>
								<xsl:if test="$logo='map'">
			  								Dargestellte Ebene: &space;
								</xsl:if>

							</td>
							<td>
								<xsl:call-template name="createLinkRelation">
								</xsl:call-template>			  								
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>

				<xsl:when test="$logo='layer'"> 
					<!-- There are only parent-nodes for a layer -->
					<xsl:for-each select="./sdi:Metadata/sdi:links/sdi:parent">
						<tr>
							<td width="30%">
								<xsl:choose>
									<xsl:when test="@objecttype='geoproduct'">
			  										ZugehÃ¶riges Geoprodukt:
									</xsl:when>
									<xsl:when test="@objecttype='map'">
			  										Dargestellt in Karte: 
									</xsl:when>
									<xsl:otherwise>
			  										Kein zugeordnetes Element
									</xsl:otherwise>
								</xsl:choose>

							</td>
							<td>
								<xsl:call-template name="createLinkRelation">
								</xsl:call-template>			  								
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>							
			</xsl:choose>
		</table>
	</xsl:template>


	<!-- Relationen in einer Tabelle darstellen (FranzÃ¶sische Version) -->
	<xsl:template name="relationshipTableFR">
		<!-- Variable Declaration -->
		<xsl:variable name="logo">
			<xsl:value-of select="./sdi:Metadata/sdi:objecttype/@code" />
		</xsl:variable>

		<table class="alternative">
			<tr>
				<th colspan="2" scope="col">Géoinformations en relation</th>
			</tr>
			<xsl:choose>
				<xsl:when test="$logo='geoproduct' or $logo='map'"> 
					<!-- There are only child-nodes for a geoproduct and maps -->
					<xsl:for-each select="./sdi:Metadata/sdi:links/sdi:child">
						<tr>
							<td width="30%">
								<xsl:if test="$logo='geoproduct'">
			  								Couche associée: &space;
								</xsl:if>
								<xsl:if test="$logo='map'">
			  								Couche représentée: &space;
								</xsl:if>

							</td>
							<td>
								<xsl:call-template name="createLinkRelationFR">
								</xsl:call-template>			  								
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>

				<xsl:when test="$logo='layer'"> 
					<!-- There are only parent-nodes for a layer -->
					<xsl:for-each select="./sdi:Metadata/sdi:links/sdi:parent">
						<tr>
							<td width="30%">
								<xsl:choose>
									<xsl:when test="@objecttype='geoproduct'">
			  										Géoproduit associé:
									</xsl:when>
									<xsl:when test="@objecttype='map'">
			  										Représenté dans la carte:
									</xsl:when>
									<xsl:otherwise>
			  										Aucun élément associé
									</xsl:otherwise>
								</xsl:choose>

							</td>
							<td>
								<xsl:call-template name="createLinkRelationFR">
								</xsl:call-template>			  								
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>							
			</xsl:choose>
		</table>
	</xsl:template>




	<!-- createLinkRelation -->

	<xsl:template name="createLinkRelation">

		<a>
			<xsl:attribute name="title">Detaillierte Informationen zu: <xsl:value-of disable-output-escaping="yes" select="@object_name" />
			</xsl:attribute>
			<xsl:attribute name="class">intern</xsl:attribute>
			<xsl:attribute name="href">index.php?tmpl=index&amp;option=com_easysdi_catalog&amp;Itemid=2&amp;context=geocatalog&amp;toolbar=1&amp;task=showMetadata&amp;type=complete&amp;id=<xsl:value-of select="@metadata_guid" />
			</xsl:attribute>
			<xsl:value-of disable-output-escaping="yes" select="@object_name" />
			<xsl:text>: Detaillierte Informationen</xsl:text>
		</a>

	</xsl:template>


	<xsl:template name="createLinkRelationFR">

		<a>
			<xsl:attribute name="title">Informations détailliées sur: <xsl:value-of disable-output-escaping="yes" select="@object_name" />
			</xsl:attribute>
			<xsl:attribute name="class">intern</xsl:attribute>
			<xsl:attribute name="href">index.php?tmpl=index&amp;option=com_easysdi_catalog&amp;Itemid=2&amp;context=geocatalog&amp;toolbar=1&amp;task=showMetadata&amp;type=complete&amp;id=<xsl:value-of select="@metadata_guid" />
			</xsl:attribute>
			<xsl:value-of disable-output-escaping="yes" select="@object_name" />
			<xsl:text>: Informations détailliées</xsl:text>
		</a>

	</xsl:template>

	<!-- Bildausschnitt -->
	<xsl:template name="bildausschnitt">
		<xsl:variable name="bildquelle">
			<xsl:value-of  select="./gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/bee:section/gco:CharacterString"/>
		</xsl:variable>
		<xsl:if test="$bildquelle!=''">

			<p class="metatada-imgcontainer">
				<xsl:element name="img">
					<xsl:attribute name="class">metadata-image</xsl:attribute>
					<xsl:attribute name="src">
						<xsl:value-of select="$bildquelle" />
					</xsl:attribute>
				</xsl:element>
			</p>
		</xsl:if>
	</xsl:template>


	<!-- string replacement -->


	<xsl:template name="string-replace-all">
		<xsl:param name="text" />
		<xsl:param name="replace" />
		<xsl:param name="by" />
		<xsl:choose>
			<xsl:when test="contains($text, $replace)">
				<xsl:value-of select="substring-before($text,$replace)" />
				<xsl:value-of select="$by" />
				<xsl:call-template name="string-replace-all">
					<xsl:with-param name="text" select="substring-after($text,$replace)" />
					<xsl:with-param name="replace" select="$replace" />
					<xsl:with-param name="by" select="$by" />
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$text" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- ADRESSE TEMPLATE -->
	<xsl:template name="addressTemplate">
		<td width="30%">
			<xsl:call-template name="RoleCodeTemplate">
				<xsl:with-param name="RoleCode" select="gmd:role/gmd:CI_RoleCode/@codeListValue" />
			</xsl:call-template>:
		</td>
		<td>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString != ''">
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString" />
				<br />
			</xsl:if>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:address/gmd:addressLine/gco:CharacterString != ''">
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:address/gmd:addressLine/gco:CharacterString" />
				<br />
			</xsl:if>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:address/gmd:postBox/gco:Decimal != ''">
				  		Postfach <xsl:value-of select="gmd:CI_ResponsibleParty/gmd:address/gmd:postBox/gco:Decimal" />
				<br />
			</xsl:if>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:address/gmd:streetName/gco:CharacterString != ''">
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:address/gmd:streetName/gco:CharacterString" />
				<xsl:text>, </xsl:text>
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:address/gmd:postalCode/gco:CharacterString" />
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:address/gmd:city/gco:CharacterString" />
				<br />
			</xsl:if>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:individualFirstName/gco:CharacterString != ''">
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:individualFirstName/gco:CharacterString" />
				<xsl:text/>
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:individualLastName/gco:CharacterString" />
				<br />
			</xsl:if>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:electronicalMailAddress/gco:CharacterString != ''">
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:electronicalMailAddress/gco:CharacterString" />
				<br />
			</xsl:if>
			<xsl:for-each select="gmd:CI_ResponsibleParty/gmd:phone">
				<xsl:if test="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='directNumber']/gmd:number/gco:CharacterString != ''">
					    	 		Direktwahl: <xsl:value-of select="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='directNumber']/gmd:number/gco:CharacterString" />
					<br />
				</xsl:if>
				<xsl:if test="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='mainNumber']/gmd:number/gco:CharacterString != ''">
					    	 		Hauptnummer: <xsl:value-of select="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='mainNumber']/gmd:number/gco:CharacterString" />
					<br />
				</xsl:if>
				<xsl:if test="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='facsimile']/gmd:number/gco:CharacterString != ''">
					    	 		Fax: <xsl:value-of select="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='facsimile']/gmd:number/gco:CharacterString" />
					<br />
				</xsl:if>
			</xsl:for-each>


		</td>

	</xsl:template>


	<!-- ADRESSE TEMPLATE FR -->
	<xsl:template name="addressTemplateFR">
		<td width="30%">
			<xsl:call-template name="RoleCodeTemplateFR">
				<xsl:with-param name="RoleCode" select="gmd:role/gmd:CI_RoleCode/@codeListValue" />
			</xsl:call-template>:
		</td>
		<td>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString != ''">
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString" />
				<br />
			</xsl:if>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:address/gmd:addressLine/gco:CharacterString != ''">
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:address/gmd:addressLine/gco:CharacterString" />
				<br />
			</xsl:if>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:address/gmd:postBox/gco:Decimal != ''">
				  		Boîte postale <xsl:value-of select="gmd:CI_ResponsibleParty/gmd:address/gmd:postBox/gco:Decimal" />
				<br />
			</xsl:if>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:address/gmd:streetName/gco:CharacterString != ''">
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:address/gmd:streetName/gco:CharacterString" />
				<xsl:text>, </xsl:text>
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:address/gmd:postalCode/gco:CharacterString" />
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:address/gmd:city/gco:CharacterString" />
				<br />
			</xsl:if>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:individualFirstName/gco:CharacterString != ''">
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:individualFirstName/gco:CharacterString" />
				<xsl:text/>
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:individualLastName/gco:CharacterString" />
				<br />
			</xsl:if>
			<xsl:if test="gmd:CI_ResponsibleParty/gmd:electronicalMailAddress/gco:CharacterString != ''">
				<xsl:value-of select="gmd:CI_ResponsibleParty/gmd:electronicalMailAddress/gco:CharacterString" />
				<br />
			</xsl:if>
			<xsl:for-each select="gmd:CI_ResponsibleParty/gmd:phone">
				<xsl:if test="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='directNumber']/gmd:number/gco:CharacterString != ''">
					    	 		Direct: <xsl:value-of select="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='directNumber']/gmd:number/gco:CharacterString" />
					<br />
				</xsl:if>
				<xsl:if test="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='mainNumber']/gmd:number/gco:CharacterString != ''">
					    	 		Numéro principale: <xsl:value-of select="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='mainNumber']/gmd:number/gco:CharacterString" />
					<br />
				</xsl:if>
				<xsl:if test="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='facsimile']/gmd:number/gco:CharacterString != ''">
					    	 		Télécopieur: <xsl:value-of select="gmd:CI_Telephone[gmd:numberType/gmd:CI_NumberTypeCode/@codeListValue='facsimile']/gmd:number/gco:CharacterString" />
					<br />
				</xsl:if>
			</xsl:for-each>


		</td>

	</xsl:template>




</xsl:stylesheet>
