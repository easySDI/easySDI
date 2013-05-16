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
		<xsl:variable name="account-logo">
			<xsl:value-of select="./sdi:Metadata/sdi:account/sdi:logo" />
		</xsl:variable>
		<xsl:variable name="account-logo-width">
			<xsl:value-of select="./sdi:Metadata/sdi:account/sdi:logo/@width" />
		</xsl:variable>
		<xsl:variable name="account-logo-height">
			<xsl:value-of select="./sdi:Metadata/sdi:account/sdi:logo/@height" />
		</xsl:variable>
		<xsl:variable name="language">
			<xsl:value-of select="./sdi:Metadata/@user_lang" />
		</xsl:variable>
		<xsl:variable name="title">
			<xsl:value-of
				select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString" />
		</xsl:variable>
		<xsl:variable name="title-fr">
			<xsl:value-of disable-output-escaping="yes"
							select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
		</xsl:variable>
		<xsl:variable name="title-gb">
			<xsl:value-of disable-output-escaping="yes"
							select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#GB']" />
		</xsl:variable>
		<xsl:variable name="abstract">
			<xsl:value-of
				select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString" />
		</xsl:variable>	
		<xsl:variable name="abstract-fr">
			<xsl:value-of
				select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#FR']" />
		</xsl:variable>	
		<xsl:variable name="abstract-gb">
			<xsl:value-of
				select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale='#GB']" />
		</xsl:variable>		
		<xsl:variable name="creationDate">
			<xsl:value-of
				select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date/gmd:CI_Date[gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='creation']/gmd:date/gco:Date" />
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
		<xsl:variable name="downloadProductRight">
			<xsl:value-of select="./sdi:Metadata/sdi:action/sdi:downloadProductRight/sdi:tooltip" />
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
					<div>
						<img  alt="" title="">
							<xsl:choose>
									<xsl:when test="string-length($account-logo) > 0">
										<xsl:attribute name="src"><xsl:value-of select="$account-logo" /></xsl:attribute>	
										<xsl:attribute name="width"><xsl:value-of select="$account-logo-width" /></xsl:attribute>	
										<xsl:attribute name="height"><xsl:value-of select="$account-logo-height" /></xsl:attribute>					
									</xsl:when >
									<xsl:otherwise>			
										<xsl:variable name="pointOfContact">
											<xsl:value-of select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString" />
										</xsl:variable>	
										<xsl:variable name="pointOfContactDomain">
											<xsl:value-of  select="substring-after($pointOfContact,'@')" />
										</xsl:variable>		
										<xsl:attribute name="src">
											<xsl:call-template name="ContactImgResource">
												<xsl:with-param name="contact" select="$pointOfContactDomain" />
											</xsl:call-template>
										</xsl:attribute>	
										<xsl:attribute name="width">60</xsl:attribute>	
										<xsl:attribute name="height">60</xsl:attribute>						
									</xsl:otherwise >
								</xsl:choose>
							
							
						</img>
					</div>
					<h4 class="hidden">
						<xsl:value-of select="./sdi:Metadata/sdi:objecttype" />
					</h4>
				</div>
				<!-- Description of the Metadata -->
				<div class="metadata-desc">
					<h4>
						<xsl:choose>
							<xsl:when test="$language='fr-FR'">
								<xsl:choose>
									<xsl:when test="string-length($title-fr) > 0">
										<xsl:value-of  select="$title-fr" />				
									</xsl:when >
									<xsl:otherwise>			
										<xsl:value-of  select="$title" />										
									</xsl:otherwise >
								</xsl:choose>
							</xsl:when>
							<xsl:when test="$language='en-GB'">
								<xsl:choose>
									<xsl:when test="string-length($title-gb) > 0">
										<xsl:value-of  select="$title-gb" />				
									</xsl:when >
									<xsl:otherwise>			
										<xsl:value-of  select="$title" />										
									</xsl:otherwise >
								</xsl:choose>
							</xsl:when>
						</xsl:choose>
					</h4>
					<p>
						<xsl:choose>
							<xsl:when test="$language='fr-FR'">
								<xsl:choose>
									<xsl:when test="string-length($abstract-fr) > 0">
										<xsl:value-of  select="substring($abstract-fr,1,200)" />				
									</xsl:when >
									<xsl:otherwise>			
										<xsl:value-of  select="substring($abstract,1,200)" />										
									</xsl:otherwise >
								</xsl:choose>
							</xsl:when>
							<xsl:when test="$language='en-GB'">
								<xsl:choose>
									<xsl:when test="string-length($abstract-gb) > 0">
										<xsl:value-of  select="substring($abstract-gb,1,200)" />				
									</xsl:when >
									<xsl:otherwise>			
										<xsl:value-of  select="substring($abstract,1,200)" />										
									</xsl:otherwise >
								</xsl:choose>
							</xsl:when>
						</xsl:choose>
						 [...]
					</p>
						
					<!-- Metadata: Summary Information -->
					
					<!-- Date -->
					<div class="metadata-summary">
						<!-- Object type -->
						<span class="metadata-label">Type:</span>
						<span class="metadata-value">
							<xsl:value-of select="./sdi:Metadata/sdi:objecttype" />
						</span>
						<span class="metadata-label">
							<xsl:choose>
								<xsl:when test="$language='fr-FR'">
									Création:
								</xsl:when>
								<xsl:when test="$language='en-GB'">
									Data date:
								</xsl:when>
							</xsl:choose>
						</span>
						<span class="metadata-value">
							<xsl:value-of select="date:day-in-month($creationDate)" />
							<xsl:text>.</xsl:text>
							<xsl:value-of select="date:month-in-year($creationDate)" />
							<xsl:text>.</xsl:text>
							<xsl:value-of select="date:year($creationDate)" />&space;
						</span>
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
								<xsl:when test="$language='fr-FR'">
									<span class="metadata-link">
										<a>
											<xsl:attribute name="title">des informations détaillées : <xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString" />
											</xsl:attribute>
											<xsl:attribute name="class">link</xsl:attribute>	
											
											<xsl:attribute name="href">index.php?option=com_easysdi_catalog&amp;Itemid=2&amp;context=geocatalog&amp;toolbar=1&amp;task=showMetadata&amp;type=complete&amp;id=<xsl:value-of select="./gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString" /></xsl:attribute>
											<xsl:text>Details</xsl:text>
										</a>
									</span>
									<xsl:choose>
										<xsl:when test="string-length($downloadProduct) > 0">
												<span class="metadata-link">
													<a>
														<xsl:attribute name="title">Téléchargement de : <xsl:value-of select="./sdi:Metadata/sdi:object/@object_name" /></xsl:attribute>
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
										</xsl:when >
										<xsl:when test="string-length($downloadProductRight) > 0">
												<span class="metadata-link">
													<div class="link-off">
														<xsl:attribute name="title">Pour accéder au téléchargement, merci de contacter : <xsl:value-of select="$downloadProductRight" /></xsl:attribute>
														<xsl:text>Téléchargement</xsl:text>
													</div>
												</span>
										</xsl:when >
									</xsl:choose>
									<xsl:if test="string-length($previewProduct) > 0">
										<span class="metadata-link">
											<a>
												<xsl:attribute name="title">Prévisualisation géographique de : <xsl:value-of select="./sdi:Metadata/sdi:object/@object_name" /></xsl:attribute>
												<xsl:attribute name="class">link modal</xsl:attribute>	
												<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:500}}</xsl:attribute>
												<xsl:attribute name="href"><xsl:value-of  select="$previewProduct" /></xsl:attribute>
												<xsl:text>Prévisualiser</xsl:text>
											</a>
										</span>
									</xsl:if>
								</xsl:when>
								<xsl:when test="$language='en-GB'">
									<span class="metadata-link">
										<a>
											<xsl:attribute name="title">des informations détaillées : 
												<xsl:value-of disable-output-escaping="yes" select="./gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString" />
											</xsl:attribute>
											<xsl:attribute name="class">link</xsl:attribute>	
											<xsl:attribute name="href">index.php?tmpl=index&amp;option=com_easysdi_catalog&amp;Itemid=2&amp;context=geocatalog&amp;toolbar=1&amp;task=showMetadata&amp;type=complete&amp;id=
												<xsl:value-of select="./gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString" />
											</xsl:attribute>
											<xsl:text>Details</xsl:text>
										</a>
									</span>
									<xsl:choose>
										<xsl:when test="string-length($downloadProduct) > 0">
												<span class="metadata-link">
													<a>
														<xsl:attribute name="title">Download : <xsl:value-of select="./sdi:Metadata/sdi:object/@object_name" /></xsl:attribute>
														<xsl:attribute name="class">link modal</xsl:attribute>	
														<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
														<xsl:attribute name="href">
															<xsl:value-of  select="$downloadProduct" />
														</xsl:attribute>
														<xsl:text>Download </xsl:text>
														
														<xsl:text> (</xsl:text>
														<xsl:value-of select="translate($filetype,$smallcase,$uppercase)" />
														<xsl:text>, </xsl:text>
														<xsl:value-of select="$filesize" />
														<xsl:text> Ko)</xsl:text>
													</a>
												</span>
										</xsl:when >
										<xsl:when test="string-length($downloadProductRight) > 0">
												<span class="metadata-link">
													<div class="link-off">
														<xsl:attribute name="title">Pour accéder au téléchargement, merci de contacter : <xsl:value-of select="$downloadProductRight" /></xsl:attribute>
														<xsl:text>Téléchargement</xsl:text>
													</div>
												</span>
										</xsl:when >
										
									</xsl:choose>
									<xsl:if test="string-length($previewProduct) > 0">
										<span class="metadata-link">
											<a>
												<xsl:attribute name="title">Geographic preview of : <xsl:value-of select="./sdi:Metadata/sdi:object/@object_name" /></xsl:attribute>
												<xsl:attribute name="class">link modal</xsl:attribute>	
												<xsl:attribute name="rel">{handler:'iframe',size:{x:650,y:600}}</xsl:attribute>
												<xsl:attribute name="href"><xsl:value-of  select="$previewProduct" /></xsl:attribute>
												<xsl:text>Preview</xsl:text>
											</a>
										</span>
									</xsl:if>
								</xsl:when>
							</xsl:choose>
							<div class="clear" />
						</div>
					</div>
				</div>
				<div class="clear" />
			</div>
			<hr></hr>
		</div>
	</xsl:template>
		<xsl:template name="ContactImgResource">
		<xsl:param name="contact"/>
		<xsl:choose>
			<xsl:when test="$contact = 'apem.asso.fr'">
				<xsl:text>/home/crigeos/domains/ids-dev.crigeos.org/public_html/images/logo/apem.jpg</xsl:text>
			</xsl:when>
			<xsl:when test="$contact = 'gers.cci.fr'">
				<xsl:text>/home/crigeos/domains/ids-dev.crigeos.org/public_html/images/logo/crigeos.png</xsl:text>	
			</xsl:when>	
			<xsl:otherwise>
				<xsl:text>/home/crigeos/domains/ids-dev.crigeos.org/public_html/images/logo/logo.jpg</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>


