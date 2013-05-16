<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
Script XSLT de transformation des partenaires ASIT-VD sous Joomla!
Le résultat est fourni sous forme de fichier CSV avec une tabulation comme séparateur
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<!-- Encodage des résultats -->
    <xsl:output encoding="ISO-8859-1"/>
    <xsl:output method="text"/>

	<!-- Variables -->
	<xsl:variable name="version" select="1.0"/>
	<xsl:variable name="separator" select="'&#9;'"/>
	<xsl:variable name="linefeed" select="'&#10;'"/>

	<!-- Analyse du flux XML -->
    <xsl:template match="/">
		<xsl:apply-templates select="easysdi"/>
    </xsl:template>

	<!-- En-tête du fichier de sortie -->
 	<xsl:template match="asitvd">
		<xsl:text>partenaire_id</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>partenaire_entree</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_profil</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_nom</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_titre</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_representant</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_adresse1</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_adresse2</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_npa</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_localite</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_pays;</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_telephone</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_fax</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_email</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>contact_url</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_code</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_type</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_fondateur</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_adhesion</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_nom1</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_nom2</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_titre</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_prenom</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_nom</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_adresse1</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_adresse2</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_npa</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_localite</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_telephone1</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_telephone2</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_fax</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_email</xsl:text><xsl:value-of select="$separator"/>
		<xsl:text>facturation_url</xsl:text>
		<xsl:value-of select="$linefeed"/>
		<xsl:apply-templates select="partner"/>
    </xsl:template>

	<!-- Transformation d'un noeud "partner" -->
    <xsl:template match="partner">
		<xsl:for-each select="record/*">
			<xsl:value-of select="."/>
			<xsl:value-of select="$separator"/>
		</xsl:for-each>
		<xsl:apply-templates select="user"/>
		<xsl:apply-templates select="contact"/>
		<xsl:apply-templates select="subscription"/>
		<xsl:value-of select="$linefeed"/>
    </xsl:template>

	<!-- Transformation d'un noeud "user" -->
    <xsl:template match="user">
		<xsl:for-each select="record/*">
			<xsl:value-of select="."/>
			<xsl:value-of select="$separator"/>
		</xsl:for-each>
    </xsl:template>

	<!-- Transformation d'un noeud "contact" -->
    <xsl:template match="contact">
		<xsl:for-each select="record/*">
			<xsl:value-of select="."/>
			<xsl:value-of select="$separator"/>
		</xsl:for-each>
    </xsl:template>

	<!-- Transformation d'un noeud "subscription" -->
    <xsl:template match="subscription">
		<xsl:for-each select="record/*">
			<xsl:value-of select="."/>
			<xsl:if test="position() != last()">
				<xsl:value-of select="$separator"/>
			</xsl:if>
		</xsl:for-each>
    </xsl:template>

</xsl:stylesheet>
