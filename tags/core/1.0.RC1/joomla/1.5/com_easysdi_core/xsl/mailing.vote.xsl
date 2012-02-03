<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
Script XSLT de génération de bulletin de vote pour l'Assemblée Générale de l'ASIT-VD sous Joomla!
Le résultat est fourni sous forme de fichier XML-FO pour une génération PDF
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	
	<xsl:decimal-format decimal-separator="." grouping-separator="'"/>
	
	<!-- Définition des variables globales -->
	<xsl:variable name="amount_base" select="0.00"/>
	<xsl:variable name="amount_proportional" select="0.00"/>
	<xsl:variable name="amount_voice" select="0"/>
	<xsl:variable name="fo:layout-master-set">
		<fo:layout-master-set>
			<fo:simple-page-master master-name="default-page" page-height="297mm" page-width="210mm" margin-left="0mm" margin-right="0mm" margin-bottom="0mm" margin-top="0mm" reference-orientation="0">
				<fo:region-body margin-top="0mm" margin-bottom="0mm" margin-left="0mm" margin-right="0mm" font-family="Arial" font-size="10pt"/>
			</fo:simple-page-master>
		</fo:layout-master-set>
	</xsl:variable>
	
	<!-- Template racine -->
	<xsl:template match="/">
		<fo:root>
			<xsl:copy-of select="$fo:layout-master-set"/>
			<fo:page-sequence master-reference="default-page" initial-page-number="1" format="1">
				<fo:flow flow-name="xsl-region-body">
					<xsl:for-each select="asit-vd">
						<xsl:variable name="vote_date" select="parameters/@date"/>
						<xsl:variable name="vote_location" select="parameters/@locality"/>
						<xsl:for-each select="account">
							<fo:block>
								<fo:block-container height="130mm" width="170mm" top="10mm" left="20mm" position="absolute" border="1pt" border-style="solid" border-color="silver">
									<xsl:call-template name="designBulletin">
										<xsl:with-param name="vote_date" select="$vote_date"/>
										<xsl:with-param name="vote_location" select="$vote_location"/>
									</xsl:call-template>
								</fo:block-container>
								<fo:block-container height="130mm" width="170mm" top="155mm" left="20mm" position="absolute" border="1pt" border-style="solid" border-color="silver">
									<xsl:call-template name="designBulletin">
										<xsl:with-param name="signature" select="'[double ASIT-VD]'"/>
										<xsl:with-param name="vote_date" select="$vote_date"/>
										<xsl:with-param name="vote_location" select="$vote_location"/>
									</xsl:call-template>
								</fo:block-container>
							</fo:block>	
							
							<!-- Saut de page pour feuille suivante -->
							<xsl:if test="position() &lt; last()">
								<fo:block break-after="page"/>
							</xsl:if>
						</xsl:for-each>
					</xsl:for-each>	
				</fo:flow>
			</fo:page-sequence>
		</fo:root>
	</xsl:template>

	<!-- Template pour bulletin de vote -->
	<xsl:template name="designBulletin">
		<xsl:param name="signature" select="''"/>
		<xsl:param name="vote_date" select="''"/>
		<xsl:param name="vote_location" select="''"/>
		<fo:block>
			<!-- En-tête bulletin de vote -->
			<fo:block-container height="40mm" width="40mm" top="5mm" left="5mm" position="absolute">
				<fo:block>
					<fo:external-graphic src="file:C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\joomla\administrator\components\com_easysdi\img\logo.gif" content-height="20mm" content-width="35mm"/>
				</fo:block>
			</fo:block-container>
			<fo:block-container height="20mm" width="100mm" top="5mm" left="50mm" position="absolute">
				<fo:block text-align="start" line-height="16pt" font-family="arial" font-size="14pt" font-weight="bold">
					<xsl:text>ASSEMBLEE GENERALE STATUTAIRE</xsl:text>
				</fo:block> 
				<fo:block text-align="start" line-height="16pt" font-family="arial" font-size="14pt" font-weight="bold">
					<xsl:value-of select="$vote_location"/><xsl:text> - </xsl:text><xsl:value-of select="$vote_date"/>
				</fo:block> 
				<fo:block text-align="start" line-height="14pt" font-family="arial" font-size="12pt" font-weight="bold">
					<xsl:text>Carte d'accès / Bulletin de vote</xsl:text>
				</fo:block> 
			</fo:block-container>
			
			<!-- Membre -->
			<fo:block-container height="30mm" width="80mm" top="35mm" left="50mm" position="absolute">
				<fo:block text-align="start" line-height="12pt" font-family="arial" font-size="10pt"><xsl:value-of select="contact/corporate1"/></fo:block> 
				<fo:block text-align="start" line-height="12pt" font-family="arial" font-size="10pt"><xsl:value-of select="contact/corporate2"/></fo:block> 
				<xsl:if test="contact/lastname != ''">
					<fo:block text-align="start" line-height="12pt" font-family="arial" font-size="10pt">
						<xsl:choose>
							<xsl:when test="contact/title = '1'"><xsl:text>Madame </xsl:text></xsl:when>
							<xsl:when test="contact/title = '2'"><xsl:text>Monsieur </xsl:text></xsl:when>
							<xsl:when test="contact/title = '3'"><xsl:text>Mademoiselle </xsl:text></xsl:when>
							<xsl:when test="contact/title = '4'"><xsl:text>Maître </xsl:text></xsl:when>
							<xsl:when test="contact/title = '5'"><xsl:text>Madame la Présidente </xsl:text></xsl:when>
							<xsl:when test="contact/title = '6'"><xsl:text>Monsieur le Président </xsl:text></xsl:when>
							<xsl:when test="contact/title = '7'"><xsl:text>Madame la Syndic </xsl:text></xsl:when>
							<xsl:when test="contact/title = '8'"><xsl:text>Monsieur le Syndic </xsl:text></xsl:when>
						</xsl:choose>
						<xsl:value-of select="contact/lastname"/><xsl:text> </xsl:text><xsl:value-of select="contact/firstname"/>
					</fo:block> 
				</xsl:if>
				<fo:block text-align="start" line-height="12pt" font-family="arial" font-size="10pt"><xsl:value-of select="contact/address1"/></fo:block> 
				<fo:block text-align="start" line-height="12pt" font-family="arial" font-size="10pt"><xsl:value-of select="contact/address2"/></fo:block> 
				<fo:block text-align="start" line-height="12pt" font-family="arial" font-size="10pt"><xsl:value-of select="contact/country"/><xsl:text> - </xsl:text><xsl:value-of select="contact/postalcode"/><xsl:text> </xsl:text><xsl:value-of select="contact/locality"/></fo:block> 
		  	</fo:block-container>
			
			<!-- Calcul du nombre de voix
				(selon charte ASIT-VD du 2 décembre 1994, article 21, sans vérification de la limite des 20% des voix totales) -->
			<xsl:variable name="amount_base" select="100.00"/>
			<xsl:variable name="amount_proportional">
				<xsl:call-template name="calculateAmount">
					<xsl:with-param name="categoryCode" select="detail/@category"/>
					<xsl:with-param name="memberCode" select="detail/member"/>
					<xsl:with-param name="employeeCode" select="detail/collaborator"/>
					<xsl:with-param name="activityCode" select="detail/activity"/>
					<xsl:with-param name="flatrate" select="detail/contract"/>
					<xsl:with-param name="inhabitant" select="detail/inhabitant"/>
					<xsl:with-param name="subscriberElectricity" select="detail/electricity"/>
					<xsl:with-param name="subscriberGaz" select="detail/gas"/>
					<xsl:with-param name="subscriberHeat" select="detail/heating"/>
					<xsl:with-param name="subscriberTV" select="detail/telcom"/>
					<xsl:with-param name="subscriberOther" select="detail/network"/>
				</xsl:call-template>
			</xsl:variable>
			<xsl:variable name="amount_voice" select="1+ceiling(($amount_base + $amount_proportional) div ($amount_base * 10))"/>
			<fo:block-container height="10mm" width="80mm" top="35mm" left="5mm" position="absolute">
				<fo:block text-align="start" line-height="12pt" font-family="arial" font-size="10pt" font-weight="bold">
					<xsl:text>Nombre de voix : </xsl:text><xsl:value-of select="$amount_voice"/>
				</fo:block>
			</fo:block-container>
			
			<!-- Détail bulletin de vote -->
			<fo:block-container height="60mm" width="170mm" top="70mm" left="50mm" position="absolute">
				<fo:table border="0pt" text-align="center" border-spacing="3pt" border-style="solid" border-color="silver">
					<fo:table-column column-width="10mm"/>
					<fo:table-column column-width="35mm" number-columns-repeated="3"/>
					<fo:table-header>
						<fo:table-row>
							<fo:table-cell padding="5pt" background-color="silver">
								<fo:block text-align="start" font-family="arial" font-size="10pt" font-weight="bold">N°</fo:block>
							</fo:table-cell>
							<fo:table-cell padding="5pt" background-color="silver" number-columns-spanned="3">
								<fo:block text-align="center" font-family="arial" font-size="10pt" font-weight="bold">VOTE</fo:block>
							</fo:table-cell>
						</fo:table-row>
					</fo:table-header>
				  	<fo:table-body>
					  	<xsl:call-template name="voteLine">
					  		<xsl:with-param name="numberLine" select="'1.'"/>
					  	</xsl:call-template>
					  	<xsl:call-template name="voteLine">
					  		<xsl:with-param name="numberLine" select="'2.'"/>
					  	</xsl:call-template>
					  	<xsl:call-template name="voteLine">
					  		<xsl:with-param name="numberLine" select="'3.'"/>
					  	</xsl:call-template>
					  	<xsl:call-template name="voteLine">
					  		<xsl:with-param name="numberLine" select="'4.'"/>
					  	</xsl:call-template>
					  	<xsl:call-template name="voteLine">
					  		<xsl:with-param name="numberLine" select="'5.'"/>
					  	</xsl:call-template>
					  </fo:table-body>
				</fo:table>
			</fo:block-container>
			
			<!-- Signature bas de bulletin -->
			<fo:block-container height="10mm" width="165mm" top="125mm" left="0mm" position="absolute">
				<fo:block text-align="right" font-family="arial" font-size="8pt"><xsl:value-of select="$signature"/></fo:block>
			</fo:block-container>
			
		</fo:block>
	</xsl:template>

	<!-- Template pour ligne de vote -->
	<xsl:template name="voteLine">
		<xsl:param name="numberLine" select="0"/>
		<fo:table-row>
			<fo:table-cell padding="5pt" background-color="silver">
				<fo:block text-align="start" font-family="arial" font-size="10pt" font-weight="bold"><xsl:value-of select="$numberLine"/></fo:block>
			</fo:table-cell>
			<fo:table-cell padding="5pt">
				<fo:block text-align="start" font-family="arial" font-size="10pt"><xsl:text>Accepté _____</xsl:text></fo:block>
			</fo:table-cell>
			<fo:table-cell padding="5pt">
				<fo:block text-align="start" font-family="arial" font-size="10pt"><xsl:text>Refusé _____</xsl:text></fo:block>
			</fo:table-cell>
			<fo:table-cell padding="5pt">
				<fo:block text-align="start" font-family="arial" font-size="10pt"><xsl:text>Abstention _____</xsl:text></fo:block>
			</fo:table-cell>
		</fo:table-row>
	</xsl:template>

	<!-- Template pour calcul coût proportionel
		(selon fiche "Mode de financement de l'ASIT-VD, 28.04.2004) -->
	<xsl:template name="calculateAmount">
		<xsl:param name="categoryCode" select="0"/>
		<xsl:param name="memberCode" select="0"/>
		<xsl:param name="employeeCode" select="0"/>
		<xsl:param name="activityCode" select="0"/>
		<xsl:param name="flatrate" select="0"/>
		<xsl:param name="inhabitant" select="0"/>
		<xsl:param name="subscriberElectricity" select="0"/>
		<xsl:param name="subscriberGaz" select="0"/>
		<xsl:param name="subscriberHeat" select="0"/>
		<xsl:param name="subscriberTV" select="0"/>
		<xsl:param name="subscriberOther" select="0"/>
		<xsl:variable name="costCommuneMax" select="40000.00"/>
		<xsl:choose>
			<xsl:when test="$categoryCode = '1'">
				<xsl:variable name="costCommune"  select="$inhabitant * 0.18 + $subscriberElectricity * 0.18 + $subscriberGaz * 0.18 + $subscriberHeat * 0.18 + $subscriberTV * 0.10 + $subscriberOther * 0.18"/>
				<xsl:choose>
					<xsl:when test="$costCommune &lt; $costCommuneMax"><xsl:value-of select="$costCommune"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="$costCommuneMax"/></xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:when test="$categoryCode = '2'">
				<xsl:value-of select="$inhabitant * 0.18"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '3'">
				<xsl:value-of select="$subscriberElectricity * 0.18 + $subscriberGaz * 0.18 + $subscriberHeat * 0.18 + $subscriberTV * 0.10 + $subscriberOther * 0.18"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '4'">
				<xsl:value-of select="$subscriberElectricity * 0.18 + $subscriberGaz * 0.18 + $subscriberHeat * 0.18 + $subscriberTV * 0.10 + $subscriberOther * 0.18"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '5' and $memberCode = '1' and $activityCode = '3'">
				<xsl:value-of select="100.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '5' and $memberCode = '2' and $activityCode = '3'">
				<xsl:value-of select="200.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '5' and $memberCode = '3' and $activityCode = '3'">
				<xsl:value-of select="400.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '5' and $memberCode = '4' and $activityCode = '3'">
				<xsl:value-of select="800.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '5' and $memberCode = '1' and $activityCode = '2'">
				<xsl:value-of select="200.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '5' and $memberCode = '2' and $activityCode = '2'">
				<xsl:value-of select="400.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '5' and $memberCode = '3' and $activityCode = '2'">
				<xsl:value-of select="800.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '5' and $memberCode = '4' and $activityCode = '2'">
				<xsl:value-of select="1600.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '6'">
				<xsl:value-of select="0.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '7' and $employeeCode = '1' and $activityCode = '3'">
				<xsl:value-of select="100.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '7' and $employeeCode = '2' and $activityCode = '3'">
				<xsl:value-of select="200.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '7' and $employeeCode = '3' and $activityCode = '3'">
				<xsl:value-of select="400.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '7' and $employeeCode = '4' and $activityCode = '3'">
				<xsl:value-of select="800.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '7' and $employeeCode = '1' and $activityCode = '2'">
				<xsl:value-of select="400.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '7' and $employeeCode = '2' and $activityCode = '2'">
				<xsl:value-of select="800.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '7' and $employeeCode = '3' and $activityCode = '2'">
				<xsl:value-of select="1600.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '7' and $employeeCode = '4' and $activityCode = '2'">
				<xsl:value-of select="3200.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '8'">
				<xsl:value-of select="$flatrate"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '9'">
				<xsl:value-of select="$flatrate"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '10'">
				<xsl:value-of select="NaN"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '11'">
				<xsl:value-of select="0.00"/>
			</xsl:when>
			<xsl:when test="$categoryCode = '12'">
				<xsl:value-of select="$flatrate"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="NaN"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
</xsl:stylesheet>
