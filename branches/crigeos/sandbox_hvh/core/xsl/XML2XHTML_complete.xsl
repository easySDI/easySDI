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
		<xsl:variable name="title">
			<xsl:value-of
				select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString" />
		</xsl:variable>
		<xsl:variable name="title-fr">
			<xsl:value-of disable-output-escaping="yes"
							select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
		</xsl:variable>
		<xsl:variable name="abstract">
			<xsl:value-of
				select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString" />
		</xsl:variable>	
		<xsl:variable name="abstract-fr">
			<xsl:value-of
				select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
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
		<xsl:variable name="datepublication">
			<xsl:value-of
				select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date/gmd:CI_Date[gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='publication']/gmd:date/gco:Date" />
		</xsl:variable>
		

		<!-- Download Links -->
		<xsl:variable name="mPDF">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:makePDF/sdi:link" />
		</xsl:variable>
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
	<xsl:variable name="datetimepublished">
			<xsl:value-of select="./sdi:Metadata/sdi:object/@metadata_published" />
		</xsl:variable>
		<xsl:variable name="datepublished">
			<xsl:choose>
				<xsl:when test="$datetimepublished !='0000-00-00T00:00:00'">
					<xsl:value-of select="date:day-in-month($datetimepublished)"/>
					<xsl:text>.</xsl:text>
					<xsl:value-of select="date:month-in-year($datetimepublished)"/>
					<xsl:text>.</xsl:text>
					<xsl:value-of select="date:year($datetimepublished)"/>
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
		<div class="metadata-sheet">	
		<div id="back-metadata-result">
			<a class="back-link" href="javascript:history.back()" >Retour à la page précédente</a>
		</div>
					
					
					
			<div class="metadata-details">			
					<div class="metadata-title">
						<h1>
							<xsl:value-of select="$title" />
						</h1>
					</div>
									
					<hr></hr>
					
					<table class="metadata-header">
						<tr>
							<td width="80%">
								<div class="metadata-abstract">
									<p>
									<xsl:choose>
										<xsl:when test="string-length($abstract-fr) > 0">
											<xsl:value-of  select="substring($abstract-fr,1,200)" />				
										</xsl:when >
										<xsl:otherwise>			
											<xsl:value-of  select="substring($abstract,1,200)" />										
										</xsl:otherwise >
									</xsl:choose>
									</p>
								</div>
							</td>
							<td rowspan="2">
									<div class="metadata-links">
										<xsl:if test="string-length($downloadProduct) > 0">
												<span class="metadata-link">
													<a>
														<xsl:attribute name="class">link modal</xsl:attribute>	
														<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
														<xsl:attribute name="href">
															<xsl:value-of  select="$downloadProduct" />
														</xsl:attribute>
														<xsl:text>Télécharger </xsl:text>
													</a>
													<xsl:text> (</xsl:text>
													<xsl:value-of select="translate($filetype,$smallcase,$uppercase)" />
													<xsl:text>, </xsl:text>
													<xsl:value-of select="round($filesize*0.001)" />
													<xsl:text> Ko)</xsl:text>
												</span>
												<p></p>
										</xsl:if >
										<xsl:if test="string-length($previewProduct) > 0">
											<span class="metadata-link">
												<a>
													<xsl:attribute name="class">link modal</xsl:attribute>	
													<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
													<xsl:attribute name="href">
														<xsl:value-of  select="$previewProduct" />
													</xsl:attribute>
													<xsl:text>Prévisualiser</xsl:text>
												</a>
											</span>
											<p></p>
										</xsl:if>	
																				
										<span class="metadata-link">
											<a>
												<xsl:attribute name="class">link</xsl:attribute>
												<xsl:attribute name="href">
													<xsl:value-of select="$exportXML" />
												</xsl:attribute><xsl:text>XML</xsl:text></a>
										</span>
										<p></p>
										<span class="metadata-link">
											<a>
												<xsl:attribute name="class">link</xsl:attribute>
												<xsl:attribute name="href">
													<xsl:value-of select="$mPDF" />
													<xsl:text>&amp;metadatatype=</xsl:text>
													<xsl:value-of select="$logo" />
													<xsl:text>&amp;format=makepdf</xsl:text>
													<xsl:text>&amp;metadata_guid[]=</xsl:text>
													<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString"/>
													<xsl:text>&amp;lastVersion=yes</xsl:text>
													<xsl:text>&amp;reporttype=CRIGEOS</xsl:text>
													<xsl:text>&amp;language=fr</xsl:text>
													<xsl:text>&amp;context=geocatalog</xsl:text>
												</xsl:attribute><xsl:text>PDF</xsl:text></a>
										</span>
										<p></p>
									</div>
								
							</td>
						</tr>
						<tr>
							<td>
								<div class="metadata-resume">
									<table class="metadata-table-short">
										<tr>
											<td width="30%">Type:</td>
											<td><xsl:value-of select="$logo" /></td>
										</tr>
										<tr>
											<td width="30%">Code:</td>
											<td>
												<xsl:value-of select="./sdi:Metadata/sdi:object/@object_name" />
											</td>
										</tr>
										<tr>
											<td width="30%">Création:</td>
											<td>
												<xsl:value-of select="$datecreated" />
											</td>
										</tr>
										<tr>
											<td width="30%">Mise à jour:</td>
											<td>
												<xsl:value-of select="$dateupdated" />
											</td>
										</tr>
										<tr>
											<td width="30%">Publication:</td>
											<td>
												<xsl:value-of select="$datetimepublished" />
											</td>
										</tr>
									</table>
								</div>
							</td>
							
							
						</tr>
					</table>
					
						
					<div class="metadata-content">	
						<table  class="metadata-table">
							<tr>
								<th class="metadata-table-th" colspan="2" scope="col">Informations principales</th>
							</tr>
							
							<tr>
								<td class="key" width="30%">Thématique:</td>
								<td class="value">		
									<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory">
										<xsl:call-template name="categoryCodeTemplateFR">
											<xsl:with-param name="categoryCode" select="gmd:MD_TopicCategoryCode" />
										</xsl:call-template>
									</xsl:for-each>
								</td>
							</tr>
							<tr>
								<td class="key" width="30%">Mots-clés:</td>
								<td class="value">
									<xsl:for-each select="./gmd:MD_Metadata/gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword">
										<xsl:value-of disable-output-escaping="yes" select="gco:CharacterString"/>, 
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
								<td width="30%">Système de référence:</td>
								<td>
									<xsl:value-of  select="./gmd:MD_Metadata/gmd:sourcereferencesystem/gmd:md_referencesystem/gmd:referencesystemidentifier/gmd:rs_identifier/gmd:codespace/gco:CharacterString"/>
								</td>
							</tr>
							
							<tr>
								<td width="30%">Emprise géographique:</td>
								<td>
									<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent">
										<table class="metadata-extent">
											<tr>
												<td colspan="3"><xsl:value-of disable-output-escaping="yes" select="gmd:EX_Extent/gmd:description/gco:CharacterString"/> : </td>
											</tr>
											<tr>
												<td></td>
												<td><xsl:value-of disable-output-escaping="yes" select="gmd:EX_Extent/gmd:geographicElement/gmd:Ex_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal"/>  </td>
												<td></td>
											</tr>
											<tr>
												<td><xsl:value-of disable-output-escaping="yes" select="gmd:EX_Extent/gmd:geographicElement/gmd:Ex_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal"/></td>
												<td></td>
												<td><xsl:value-of disable-output-escaping="yes" select="gmd:EX_Extent/gmd:geographicElement/gmd:Ex_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal"/> </td>
											</tr>
											<tr>
												<td></td>
												<td><xsl:value-of disable-output-escaping="yes" select="gmd:EX_Extent/gmd:geographicElement/gmd:Ex_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal"/> </td>
												<td></td>
											</tr>
										</table>
									</xsl:for-each>
								</td>
							</tr>
							<tr>
								<td width="30%">Résolution spatiale:</td>
								<td>
									<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:spatialresolution">
										<table class="metadata-resolution">
											<xsl:for-each select="gmd:MD_Resolution/gco:Distance">
												<tr>
												<td>Distance au sol</td>
												<td><xsl:value-of disable-output-escaping="yes" select="gco:Decimal"/>  </td>
												</tr>
											</xsl:for-each>
											<xsl:for-each select="gmd:MD_Resolution/gmd:EquivalentScale">
												<tr>
												<td>Dénominateur d'échelle</td>
												<td><xsl:value-of disable-output-escaping="yes" select="gmd:md_representativefraction/gmd:denominator/gco:integer"/>  </td>
												</tr>
											</xsl:for-each>
										</table>
									</xsl:for-each>
								</td>
							</tr>
							</table>
							
						<table class="metadata-table">
							<tr>
								<th class="metadata-table-th" colspan="2" scope="col">Maintenance</th>
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
								<td width="30%">Dernière mise à jour:</td>
								<td>
									<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:dateOfLastUpdate/gco:Date"/>
								</td>
							</tr>
							<tr>
								<td width="30%">Prochaine mise à jour:</td>
								<td>
									<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:dateOfNextUpdate/gco:Date"/>
								</td>
							</tr>
							<tr>
								<td width="30%">Information sur la mise à jour:</td>
								<td>
									<xsl:value-of  select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceNote/gco:CharacterString"/>
								</td>
							</tr>
						</table>
						
						<table class="metadata-table">
							<tr>
								<th class="metadata-table-th" colspan="2" scope="col">Publication de la donnée</th>
							</tr>
							<tr>
								<td width="30%">Origine de la donnée:</td>
								<td>
									<xsl:call-template name="RoleCodeTemplateFR">
										<xsl:with-param name="RoleCode" select="./gmd:MD_Metadata/gmd:distributioninfo/gmd:md_distribution/gmd:md_distributor/gmd:distributorcontact/gmd:ci_responsibleparty/gmd:role/gmd:ci_rolecode/@codeListValue"/>
									</xsl:call-template>
									<xsl:value-of  select="./gmd:MD_Metadata/gmd:distributioninfo/gmd:md_distribution/gmd:md_distributor/gmd:distributorcontact/gmd:ci_responsibleparty/gmd:organisationname"/>
									<xsl:for-each select="./gmd:MD_Metadata/gmd:distributioninfo/gmd:md_distribution/gmd:md_distributor/gmd:distributorcontact/gmd:ci_responsibleparty/gmd:electronicmailadress">
										<xsl:value-of  select="gco:CharacterString"/>
									</xsl:for-each>
								</td>
							</tr>
							<tr>
								<td width="30%">Url de la métadonnée d'origine:</td>
								<td>
									<xsl:value-of  select="./gmd:MD_Metadata/gmd:distributioninfo/gmd:md_distribution/gmd:md_distributor/gmd:originalmetadata/gco:CharacterString"/>
								</td>
							</tr>
							<tr>
								<td width="30%">Format de la donnée:</td>
								<td>
									<xsl:value-of  select="./gmd:MD_Metadata/gmd:distributioninfo/gmd:md_distribution/gmd:md_distributor/gmd:storageformat/gco:CharacterString"/>
								</td>
							</tr>
							<tr>
								<td width="30%">Accès en ligne:</td>
								<td>
									<xsl:for-each select="./gmd:MD_Metadata/gmd:distributioninfo/gmd:md_distribution/gmd:transferoptions/gmd:md_digitaltransferoptions/gmd:online">
										<xsl:value-of  select="gmd:ci_onlineresource/gmd:linkage/gmd:url"/>
										(<xsl:value-of  select="gmd:ci_onlineresource/gmd:protocol/gco:CharacterString"/>)
									</xsl:for-each>
								</td>
							</tr>
						</table>
							
						<table class="metadata-table">
							<tr>
								<th class="metadata-table-th" colspan="2" scope="col">Qualité de la donnée</th>
							</tr>
							
							<xsl:for-each select="./gmd:MD_Metadata/gmd:dataQualityInfo">
							<tr>
								<td width="30%">Critère d'évaluation de la donnée:</td>
								<td>
									<table class="metadata-quality">
										<tr>
											<td>Norme</td>
											<td>
												<xsl:value-of  select="gmd:DQ_DataQuality/gmd:report/gmd:dq_domainconsistency/gmd:result/gmd:dq_conformanceresult/gmd:specification/gmd:CI_Citation/gmd:title/gco:CharacterString"/>
												(<xsl:call-template name="dateTypeCodeTemplateFR">
													<xsl:with-param name="dateTypeCode" select="gmd:DQ_DataQuality/gmd:report/gmd:dq_domainconsistency/gmd:result/gmd:dq_conformanceresult/gmd:specification/gmd:CI_Citation/gmd:date/gmd:CI_Date/gmd:date/gco:Datetype/gmd:CI_DateTypeCode/@codeListValue"/>
												</xsl:call-template> :
												<xsl:value-of  select="gmd:DQ_DataQuality/gmd:report/gmd:dq_domainconsistency/gmd:result/gmd:dq_conformanceresult/gmd:specification/gmd:CI_Citation/gmd:date/gmd:CI_Date/gmd:date/gco:Date"/>)
												
											</td>
										</tr>
										<tr>
											<td>Explication</td>
											<td>
											</td>
										</tr>
										<tr>
											<td>Conformité</td>
											<td>
											</td>
										</tr>
										
									</table>
								</td>
							</tr>	
							<tr>
								<td width="30%">Qualité de la provenance:</td>
								<td>
									<xsl:value-of  select="gmd:DQ_DataQuality/gmd:lineage/gmd:statement/gco:CharacterString"/>
								</td>
							</tr>
							</xsl:for-each>
							
						</table>
						
						
						<table class="metadata-table">
							<tr>
								<th class="metadata-table-th" colspan="2" scope="col">Contraintes</th>
							</tr>
							<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints">
							<tr>
								<td width="30%">Contraintes d'accès:</td>
								<td>
									<xsl:for-each select="gmd:MD_LegalConstraints/gmd:accessConstraints">
										<xsl:call-template name="constraintsTypeTemplateFR">
											<xsl:with-param name="constraintsTypeCode" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:accessConstraints/gmd:MD_RestrictionCode/@codeListValue"/>
										</xsl:call-template>  
									</xsl:for-each>
									
								</td>
							</tr>
							<tr>
								<td width="30%">Limitation d'utilisation de la ressource:</td>
								<td>
									<xsl:for-each select="gmd:MD_LegalConstraints/gmd:useLimitation">
										 <xsl:value-of disable-output-escaping="yes"  select="gco:CharacterString"/>
									</xsl:for-each>
									
								</td>
							</tr>
							<tr>
								<td width="30%">Autres contraintes:</td>
								<td>
									<xsl:for-each select="gmd:MD_LegalConstraints/gmd:otherConstraints">
										 <xsl:value-of disable-output-escaping="yes"  select="gco:CharacterString"/>
									</xsl:for-each>
								</td>
							</tr>
							</xsl:for-each>
						</table>
						
						<table class="metadata-table">
							<xsl:choose>
								<xsl:when test="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact != ''">
									<tr>
										<th class="metadata-table-th" colspan="2" scope="col">Contacts</th>
									</tr>
									<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact">
									<tr>
										<xsl:call-template name="addressTemplateFR">
										</xsl:call-template>
									</tr>
									</xsl:for-each>	
								</xsl:when>
							</xsl:choose>
						</table>
						<table class="metadata-table">
							<tr>
								<th class="metadata-table-th" colspan="2" scope="col">Informations système</th>
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
									<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:MetadataStandardVersion/gco:CharacterString" />
								</td>
							</tr>
							<tr>
								<td width="30%">Hiérarchie:</td>
								<td>
									<xsl:call-template name="scopeCodeTemplateFR">
										<xsl:with-param name="scopeCode" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:hierarchyLevel/gmd:MD_ScopeCode"/>
									</xsl:call-template>
								</td>
							</tr>
							<xsl:for-each select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:date">
								<tr>
								<td>
									<xsl:call-template name="dateTypeCodeTemplateFR">
										<xsl:with-param name="dateTypeCode" select="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue"/>
									</xsl:call-template>
									</td>
								
								<td>
									<xsl:value-of disable-output-escaping="yes" select="gmd:CI_Date/gmd:date/gco:Date" />
									</td>
								</tr>
							</xsl:for-each>
						</table>
					</div>
					
				</div>
				
		</div>	 


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

	<xsl:template name="constraintsTypeTemplateFR">
		<xsl:param name="constraintsTypeCode"/>
		<xsl:choose>
			<xsl:when test="$constraintsTypeCode = 'copyright'">
				<xsl:text>Droit d'auteur</xsl:text>
			</xsl:when>
			<xsl:when test="$constraintsTypeCode = 'patent'">
				<xsl:text>Brevet</xsl:text>
			</xsl:when>
			<xsl:when test="$constraintsTypeCode = 'patentPending'">
				<xsl:text>Brevet en instance</xsl:text>
			</xsl:when>
			<xsl:when test="$constraintsTypeCode = 'trademark'">
				<xsl:text>Marque de com</xsl:text>
			</xsl:when>
			<xsl:when test="$constraintsTypeCode = 'licence'">
				<xsl:text>Licence</xsl:text>
			</xsl:when>
			<xsl:when test="$constraintsTypeCode = 'intellectualPropertyRights'">
				<xsl:text>Droit de propriété intellectuelle</xsl:text>
			</xsl:when>
			<xsl:when test="$constraintsTypeCode = 'restricted'">
				<xsl:text>Restreint</xsl:text>
			</xsl:when>
			<xsl:when test="$constraintsTypeCode = 'otherRestrictions'">
				<xsl:text>Autres restrictions</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>?</xsl:text>
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
				<xsl:text>Date non qualifiée</xsl:text>
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
	
	<xsl:template name="scopeCodeTemplateFR">
		<xsl:param name="scopeCode"/>
		<xsl:choose>
			<xsl:when test="$scopeCode = 'attribute'">
				<xsl:text>Attribut</xsl:text>
			</xsl:when>
			<xsl:when test="$scopeCode = 'attributeType'">
				<xsl:text>Type d'attribut</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'collectionHardware'">
				<xsl:text>collectionHardware</xsl:text>	
			</xsl:when>			
			<xsl:when test="$scopeCode = 'collectionSession'">
				<xsl:text>collectionSession</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'dataset'">
				<xsl:text>dataset</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'series'">
				<xsl:text>series</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'nonGeographicDataset'">
				<xsl:text>nonGeographicDataset</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'dimensionGroup'">
				<xsl:text>dimensionGroup</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'feature'">
				<xsl:text>feature</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'featureType'">
				<xsl:text>featureType</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'propertyType'">
				<xsl:text>propertyType</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'fieldSession'">
				<xsl:text>fieldSession</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'software'">
				<xsl:text>software</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'service'">
				<xsl:text>service</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'model'">
				<xsl:text>model</xsl:text>	
			</xsl:when>	
			<xsl:when test="$scopeCode = 'tile'">
				<xsl:text>tile</xsl:text>	
			</xsl:when>	
			
			<xsl:otherwise>
				<xsl:text/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
