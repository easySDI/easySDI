<?xml version="1.0"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:apem="http://www.apem.asso.fr" xmlns:gml="http://www.opengis.net/gml">
	<xsl:output indent="yes" method="html" omit-xml-declaration="yes" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" />
	<xsl:template match="/">
		<html>
			<head>
			</head>
			<body>
				<xsl:choose>
					<xsl:when test="//gml:featureMember">
						<div class="tabs">
							<ul>
								<xsl:if test="//apem:pays">
									<li>
										<a href="#fragment-1">Pays</a>
									</li>
								</xsl:if>
								<xsl:if test="//apem:ville">
									<li>
										<a href="#fragment-2">Villes principales</a>
									</li>
								</xsl:if>
								<xsl:if test="//apem:UP_enquete99">
									<li>
										<a href="#fragment-3">UP enquete99</a>
									</li>
								</xsl:if>
								<xsl:if test="//apem:ZDE">
									<li>
										<a href="#fragment-4">ZDE</a>
									</li>
								</xsl:if>

							</ul>
							<xsl:if test="//apem:pays">
								<div id="fragment-1" class="tabContent"> <!-- Ne pas changer frgament-x -->
									<ul>
										<xsl:for-each select="//apem:pays">
											<li>
												<xsl:value-of select="apem:lib" />
											</li>
											<li>
												<xsl:value-of select="apem:code_territoire" />
											</li>
											<li>
												<xsl:value-of select="apem:commentaire" />
											</li>
											<br />
										</xsl:for-each>
									</ul>
								</div>
							</xsl:if>
							<xsl:if test="//apem:ville">
								<div id="fragment-2" class="tabContent">
									<ul>
										<xsl:for-each select="//apem:ville">
											<li>
												<xsl:value-of select="apem:nom_commun" />
											</li>
											<li>
												<xsl:value-of select="apem:insee_comm" />
											</li>
											<li>
												<xsl:value-of select="apem:nom_can" />
											</li>
											<li>
												<xsl:value-of select="apem:status" />
											</li>
											<li>
												<xsl:value-of select="apem:de_status" />
											</li>
											<br />
										</xsl:for-each>
									</ul>
								</div>
							</xsl:if>
							<xsl:if test="//apem:UP_enquete99">
								<div id="fragment-3" class="tabContent">
									<ul>
										<xsl:for-each select="//apem:UP_enquete99">
											<li>
												<xsl:value-of select="apem:NUMERO" />
											</li>
											<li>
												<xsl:value-of select="apem:NOM" />
											</li>
											<li>
												<xsl:value-of select="apem:DEP" />
											</li>
											<br />
										</xsl:for-each>
									</ul>
								</div>
							</xsl:if>
							<xsl:if test="//apem:ZDE">
								<div id="fragment-4" class="tabContent">
									<ul>
										<xsl:for-each select="//apem:ZDE">
											<li>
												<xsl:value-of select="apem:ZONE" />
											</li>
											<br />
										</xsl:for-each>
									</ul>
								</div>
							</xsl:if>
						</div>
					</xsl:when>
					<xsl:otherwise>
						<h1>Aucune information</h1>
					</xsl:otherwise>
				</xsl:choose>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>