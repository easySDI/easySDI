<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:gmd="http://www.isotc211.org/2005/gmd"
	xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:xlink="http://www.w3.org/1999/xlink"
	xmlns:ext="http://www.depth.ch/2008/ext" xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns:fox="http://xml.apache.org/fop/extensions">

	<xsl:output method="html" encoding="utf-8" indent="yes" />

	<xsl:param name="error_type"/>
	<xsl:param name="user_language"/>
	<xsl:param name="missing_parameter"/>

	<!-- Choix du type de retour -->
	<xsl:template match="*">
		<legend><xsl:value-of select="error_type"/></legend>
		<i><xsl:value-of select="user_language"/></i>
		<xsl:choose>
			<xsl:when test="$error_type='MISSINGPARAMETER'">
				<p>Paramètre <xsl:value-of select="$missing_parameter"/>manquant</p>
			</xsl:when>
			<xsl:when test="$error_type='FORMATINVALID'">
				<p>Format invalide</p>
			</xsl:when>
			<xsl:when test="$error_type='LASTVERSIONINVALID'">
				<p>lastVersion invalide</p>
			</xsl:when>
			<xsl:when test="$error_type='NOMETADATA'">
				<p>Aucune métadonnée publique ne correspond aux guids demandés.</p>
			</xsl:when>
			<xsl:when test="$error_type='OWSEXCEPTION'">
				<p>Exception OWS</p>
			</xsl:when>
			<xsl:otherwise>
				<p>Erreur inconnue</p>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
