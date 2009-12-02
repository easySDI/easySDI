<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
Script XSLT de transformation des partenaires ASIT-VD sous Joomla!
Le r�sultat est fourni sous forme de fichier CSV avec une tabulation comme s�parateur
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:gmd="http://www.isotc211.org/2005/gmd" 
xmlns:gco="http://www.isotc211.org/2005/gco"
xmlns:xlink="http://www.w3.org/1999/xlink"
xmlns:ext="http://www.depth.ch/2008/ext"
>

    <!-- Encodage des r�sultats -->
    <xsl:output encoding="utf-8"/>
    <xsl:output method="html"/>
    



<xsl:template match="gmd:MD_Metadata">

<!-- Title of the metadata -->
<title><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString"/></title>

<div id="metadata" class="contentin">
<h2 class="contentheading"><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString"/></h2>


<table class="descr" id="metadataTitle">
 <tr>
 	<td rowspan="2">__ref_1$s</td>
	<td class="furnisherTitle">Fournisseur: __ref_2$s</td>
 </tr>
 <tr>
 	<td>Fiche cr��e le __ref_3$s, mise � jour le __ref_4$s</td>
 </tr>
</table>
<!-- The buttons links -->
__ref_5$s
<!-- The menu links -->
__ref_6$s
<!-- <h3>Identification</h3> -->

<table class="descr"  >
<tr valign="top"><td class="title">Id : </td> <td><xsl:value-of disable-output-escaping="yes" select="./gmd:fileIdentifier/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Nom :</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Nom de la table:</td><td>
 <xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Identification']">
 	<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Nom de la table' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>
      </xsl:if>
</xsl:for-each>
</td>
</tr>
<tr valign="top"><td class="title">Description :</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Cr�ation de la donn�e:</td><td>
 <xsl:for-each select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date">
     <xsl:if test="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='creation'">
		<xsl:variable name="date_norm" select="gmd:CI_Date/gmd:date/gco:Date" />
		<xsl:variable name="year" select="substring($date_norm, 1, 4)" />
		<xsl:variable name="month" select="substring($date_norm, 6, 2)" />
		<xsl:variable name="day" select="substring($date_norm, 9, 2)" />
		<xsl:variable name="date_complete" select="concat($day, '.', $month, '.', $year)" />
		<xsl:value-of select="$date_complete" />
     </xsl:if>
 </xsl:for-each>
</td></tr>

<tr valign="top"><td class="title">Mise � jour de la donn�e:</td><td>
<xsl:for-each select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:date">
     <xsl:if test="gmd:CI_Date/gmd:dateType/gmd:CI_DateTypeCode/@codeListValue='revision'">
		<xsl:variable name="date_norm" select="gmd:CI_Date/gmd:date/gco:Date" />
		<xsl:variable name="year" select="substring($date_norm, 1, 4)" />
		<xsl:variable name="month" select="substring($date_norm, 6, 2)" />
		<xsl:variable name="day" select="substring($date_norm, 9, 2)" />
		<xsl:variable name="date_complete" select="concat($day, '.', $month, '.', $year)" />
		<xsl:value-of select="$date_complete" />
     </xsl:if>
 </xsl:for-each>
</td></tr>
<tr valign="top"><td class="title">Etendue g�ographique*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:description/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Couverture spatiale:</td><td>
	<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/@xlink:title"/>        
</td></tr>

<tr valign="top"><td class="title">Synoptique:</td><td>
 <xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Identification']">
 	<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Synoptique' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>
      </xsl:if>
</xsl:for-each>
</td>
</tr>
<tr valign="top"><td class="title">Extrait:</td><td><xsl:value-of disable-output-escaping="yes" select='./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:graphicOverview/gmd:MD_BrowseGraphic/gmd:fileName/gco:CharacterString'/></td></tr>


<tr valign="top"><td class="title">Sous-produits:</td><td>
 <xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Identification']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Sous-produits' ">
			<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td></tr>
<tr valign="top"><td class="title">Th�matique*:</td><td>
	<table class="descr">
	<xsl:for-each select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:topicCategory">
		<tr valign="top"><td class="title">
		<xsl:call-template name="categoryCodeTemplate">
			<xsl:with-param name="categoryCode" select="gmd:MD_TopicCategoryCode"/>
		</xsl:call-template>
		</td></tr>
	</xsl:for-each>
	</table>
</td></tr>
</table>

<h3>Diffusion</h3>
<table class="descr"  >
<tr valign="top"><td class="title">Conditions de diffusion:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints[@xlink:title='Conditions de diffusion']/gmd:MD_LegalConstraints/gmd:otherConstraints/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Restriction d'utilisation:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints/gmd:MD_LegalConstraints/gmd:useLimitation/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Principes et mode de tarification:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributionOrderProcess/gmd:MD_StandardOrderProcess/gmd:fees/gco:CharacterString"/></td></tr>
</table>

<h3>Statut juridique</h3>
<table class="descr"  >
<tr valign="top"><td class="title">Statut:</td><td>
<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Statut juridique']">
      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Statut' ">
				<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>        
      </xsl:if>
</xsl:for-each>
</td></tr>
<tr valign="top"><td class="title">R�f�rence du document l�gal:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceConstraints[@xlink:title='R�f�rence du document l�gal']/gmd:MD_LegalConstraints/gmd:otherConstraints/gco:CharacterString"/></td></tr>
</table>

      
<h3>Gestion</h3>
	<h4>Acquisition</h4>
	<table class="descr"   >                   
		<tr valign="top"><td class="title">Mode d'acquisition:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:statement/gco:CharacterString"/></td></tr>
		<tr valign="top"><td class="title">Description du mode d'acquisition:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:processStep/gmd:LI_ProcessStep/gmd:description/gco:CharacterString"/></td></tr>
		<tr valign="top"><td class="title">Donn�es sources:</td><td><xsl:value-of disable-output-escaping="yes" select="                  ./gmd:dataQualityInfo/gmd:DQ_DataQuality/gmd:lineage/gmd:LI_Lineage/gmd:processStep/gmd:LI_ProcessStep/gmd:source/gmd:LI_Source/gmd:description/gco:CharacterString"/></td></tr>
	</table>

	<h4>Mise � jour</h4>
	<table class="descr">
	
		<tr valign="top"><td class="title">Type de mise � jour:</td><td>
			<xsl:call-template name="maintenanceTypeTemplate">
				<xsl:with-param name="maintenanceTypeCode" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode/@codeListValue"/>
			</xsl:call-template>
		</td></tr>
		<tr valign="top"><td class="title">Fr�quence:</td><td>
			<xsl:call-template name="maintenanceFrequencyCodeTemplate">
				<xsl:with-param name="maintenanceFrequencyCode" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceAndUpdateFrequency/gmd:MD_MaintenanceFrequencyCode/@codeListValue"/>
			</xsl:call-template>
		</td></tr>
		<tr valign="top"><td class="title">Remarques:</td><td>
			<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:resourceMaintenance/gmd:MD_MaintenanceInformation/gmd:maintenanceNote/gco:CharacterString"/>
		</td></tr>
	</table>
 
 
<h3>Repr�sentation</h3>
	<table class="descr">
		<tr valign="top"><td class="title">Syst�me de coordonn�es:</td><td>
			<xsl:choose>
					<xsl:when test="./gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:referenceSystemIdentifier/gmd:RS_Identifier/gmd:code/gco:CharacterString = 'EPSG:21781'">            
				  Coordonn�es nationales suisses (EPSG:21781)
			</xsl:when>
			<xsl:otherwise>
				  <xsl:value-of disable-output-escaping="yes" select="./gmd:referenceSystemInfo/gmd:MD_ReferenceSystem/gmd:referenceSystemIdentifier/gmd:RS_Identifier/gmd:code/gco:CharacterString"/>
			</xsl:otherwise>
			</xsl:choose>
		</td></tr>
		<tr valign="top"><td class="title">Echelle de r�f�rence:</td><td>
			<xsl:value-of disable-output-escaping="yes" select ="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:spatialResolution/gmd:MD_Resolution/gmd:equivalentScale/gmd:MD_RepresentativeFraction/gmd:denominator/gco:Integer" />
			<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Repr�sentation']">
				<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Echelle de r�f�rence' ">
							<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>        
						</xsl:if>
			</xsl:for-each>
			</td></tr>
		<tr valign="top"><td class="title">Pr�cision:</td><td>
			<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Repr�sentation']">
			      <xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Pr�cision' ">
							<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>        
			      </xsl:if>
			</xsl:for-each>
		</td></tr>
	</table>

	
	<xsl:if test="./gmd:extendedMetadata[@xlink:title='Produit vecteur']">
	<h4>Produit vecteur*:</h4>
	<table class="descr">
		<tr valign="top"><td class="title">Type d'objet graphique* : </td><td>
		<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Produit vecteur']">
			  	<xsl:variable name="value">Type d'objet graphique</xsl:variable>
		    	<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = $value">
		    			<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>
		     	</xsl:if>
		</xsl:for-each>
		</td></tr>
		<tr valign="top"><td class="title">Coh�rence topologique : </td><td>
		<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Produit vecteur']">
		     	<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Coh�rence topologique' ">
				      		<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>        
		     	 </xsl:if>
		</xsl:for-each>
		</td></tr>
		<tr valign="top"><td class="title">Information altim�trique : </td><td>
		<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Produit vecteur']">
			  	<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Information altim�trique' ">
				      		<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>        
				</xsl:if>
		</xsl:for-each>
		</td></tr>
		<tr valign="top"><td class="title">Mod�le de donn�es : </td><td>
		<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Produit vecteur']">
		     	<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Mod�le de donn�es' ">
				      		<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>        
		        </xsl:if>
		</xsl:for-each>
		</td></tr>
	</table>
</xsl:if>
	
	<xsl:if test="./gmd:extendedMetadata[@xlink:title='Produit raster']">
	<h4>Produit rasteur*:</h4>
	<table class="descr">
		<tr valign="top"><td class="title">R�solution* : </td><td>
			<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Produit raster']">
		    	<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'R�solution'">
		    			<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>
		     	</xsl:if>
		     </xsl:for-each>
		</td></tr>
		<tr valign="top"><td class="title">Date de prise de vue : </td><td>
			<xsl:for-each select="./gmd:extendedMetadata[@xlink:title='Produit raster']">
		     	<xsl:if test="ext:EX_extendedMetadata_Type/ext:name/gco:CharacterString = 'Date de prise de vue' ">
				      		<xsl:value-of disable-output-escaping="yes" select="ext:EX_extendedMetadata_Type/ext:value/gco:CharacterString"/>        
		     	 </xsl:if>
			</xsl:for-each>
		</td></tr>
	</table>
	</xsl:if>
	
<h3>Attribut</h3>
 <xsl:for-each select="./gmd:metadataExtensionInfo">
 <xsl:choose>
 	<xsl:when test="gmd:MD_MetadataExtensionInformation/gmd:extendedElementInformation/gmd:MD_ExtendedElementInformation/gmd:name[@gco:nilReason]">
 	</xsl:when>
 	<xsl:otherwise>
 		<table class="descr"   >
	 	<tr valign="top"><td class="title">Nom*:</td><td>
	 	<xsl:value-of disable-output-escaping="yes" select="gmd:MD_MetadataExtensionInformation/gmd:extendedElementInformation/gmd:MD_ExtendedElementInformation/gmd:name/gco:CharacterString"/>
	 	</td></tr>
	 	 <tr valign="top"><td class="title">Description*:</td><td>
	 	 <xsl:value-of disable-output-escaping="yes" select="gmd:MD_MetadataExtensionInformation/gmd:extendedElementInformation/gmd:MD_ExtendedElementInformation/gmd:definition/gco:CharacterString"/>
	 	 </td></tr>
	 	 <tr valign="top"><td class="title">Format:</td><td>
	 	 <xsl:value-of disable-output-escaping="yes" select="gmd:MD_MetadataExtensionInformation/gmd:extendedElementInformation/gmd:MD_ExtendedElementInformation/gmd:rule/gco:CharacterString"/>
	 	 </td></tr>
	 	 <tr valign="top"><td class="title">Type:</td><td>
	 	 <xsl:call-template name="DataTypeCodeTemplate">
			<xsl:with-param name="DataTypeCode" select="gmd:MD_MetadataExtensionInformation/gmd:extendedElementInformation/gmd:MD_ExtendedElementInformation/gmd:dataType/gmd:MD_DataTypeCode/@codeListValue"/>
		 </xsl:call-template>
		 </td></tr>
	 	 <tr valign="top"><td class="title">Statut:</td><td>
	 	 <xsl:call-template name="obligationCodeTemplate">
			<xsl:with-param name="obligationCode" select="gmd:MD_MetadataExtensionInformation/gmd:extendedElementInformation/gmd:MD_ExtendedElementInformation/gmd:obligation/gmd:MD_ObligationCode"/>
		</xsl:call-template>
	 	 </td></tr>
	 	 </table>
 	</xsl:otherwise>
 </xsl:choose>
 </xsl:for-each>



<h3>Gestionnaire</h3>
<table class="descr"   >	 	 	  	 	
<tr valign="top"><td class="title">Organisme*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Nom:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Adresse:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Localit�:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Code postal:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Pays:</td>
<xsl:if test="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString = 'CH' ">
	<td>Suisse</td>
</xsl:if>
</tr>
<tr valign="top"><td class="title">T�l�phone:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Fax:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString"/></td></tr>

<tr valign="top"><td class="title">Email:</td><td><xsl:element name="a">
<xsl:attribute name="href">
mailto:<xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:attribute>
<xsl:value-of disable-output-escaping="yes" select="./gmd:contact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:element>
</td></tr>
</table>


<h3>Responsable de diffusion</h3>
<table class="descr"   >
<tr valign="top"><td class="title">Organisme*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Nom:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Adresse:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Localit�:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Code postal:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Pays:</td>
<xsl:if test="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString = 'CH' ">
	<td>Suisse</td>
</xsl:if>
</tr>
<tr valign="top"><td class="title">T�l�phone:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Fax:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Email:</td><td>
<xsl:element name="a">
<xsl:attribute name="href">
mailto:<xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:attribute>
<xsl:value-of disable-output-escaping="yes" select="./gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:element>

</td></tr>
</table>


<h3>Point de contact</h3>
<table class="descr"   >
<tr valign="top"><td class="title">Organisme*:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Nom:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:individualName/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Adresse:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:deliveryPoint/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Localit�:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:city/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Code postal:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:postalCode/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Pays:</td>
<xsl:if test="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:country/gco:CharacterString = 'CH' ">
	<td>Suisse</td>
</xsl:if>
</tr>
<tr valign="top"><td class="title">T�l�phone:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:voice/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Fax:</td><td><xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString"/></td></tr>
<tr valign="top"><td class="title">Email:</td><td>
<xsl:element name="a">
<xsl:attribute name="href">
mailto:<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:attribute>
<xsl:value-of disable-output-escaping="yes" select="./gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:contactInfo/gmd:CI_Contact/gmd:address/gmd:CI_Address/gmd:electronicMailAddress/gco:CharacterString"/>
</xsl:element>
</td></tr>

</table>
</div>

<!-- Script to open all hyperlinks in a new window -->
<script>

var container = document.getElementById("metadata");
var hlinks = container.getElementsByTagName("a");
i=0;
while(true){
	if(hlinks.length == i)
		break;
	if(!hlinks[i].href.match("mailto:"))
		hlinks[i].setAttribute('target', '_blank');
	i++;
}
</script>

</xsl:template>

	<!-- Template DataTypeCode -->
	<xsl:template name="DataTypeCodeTemplate">
		<xsl:param name="DataTypeCode"/>
			<xsl:choose>
				<xsl:when test="$DataTypeCode = 'class'">
					<xsl:text>classe</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'codelist'">
					<xsl:text>liste de codes</xsl:text>	
				</xsl:when>	
				<xsl:when test="$DataTypeCode = 'enumeration'">
					<xsl:text>�num�ration</xsl:text>	
				</xsl:when>	
				<xsl:when test="$DataTypeCode = 'codelistElement'">
					<xsl:text>�l�ment d'une liste</xsl:text>	
				</xsl:when>		
				<xsl:when test="$DataTypeCode = 'abstractClass'">
					<xsl:text>classe abstraite</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'aggregatedClass'">
					<xsl:text>classe globale</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'specifiedClass'">
					<xsl:text>classe sp�cifique</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'datatypeClass'">
					<xsl:text>classe d'un type de donn�es</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'interfaceClass'">
					<xsl:text>classe d'interface</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'unionClass'">
					<xsl:text>classe d'union</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'metaClass'">
					<xsl:text>m�taclasse</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'typeClass'">
					<xsl:text>classe de type</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'characterString'">
					<xsl:text>texte libre</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'integer'">
					<xsl:text>entier</xsl:text>
				</xsl:when>
				<xsl:when test="$DataTypeCode = 'association'">
					<xsl:text>association</xsl:text>
				</xsl:when>	
				<xsl:otherwise>
					<xsl:text></xsl:text>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>

<!-- Template ObligationCode -->
	<xsl:template name="obligationCodeTemplate">
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
					<xsl:text></xsl:text>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>

<!-- Template MaintenanceFrequencyCode -->
	<xsl:template name="maintenanceFrequencyCodeTemplate">
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
					<xsl:text>Irr�guli�re</xsl:text>	
				</xsl:when>	
				<xsl:when test="$maintenanceFrequencyCode = 'notPlanned'">
					
				</xsl:when>
				<xsl:when test="$maintenanceFrequencyCode = 'unknown'">
						
				</xsl:when>	
				<xsl:when test="$maintenanceFrequencyCode = 'userDefined'">
					<xsl:text>D�finie par l'utilisateur</xsl:text>
				</xsl:when>	
				<xsl:otherwise>
					<xsl:value-of disable-output-escaping="yes" select="$maintenanceFrequencyCode"></xsl:value-of>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>
	<xsl:template name="maintenanceTypeTemplate">
		<xsl:param name="maintenanceTypeCode"/>
			<xsl:choose>
				<xsl:when test="$maintenanceTypeCode = 'continual'">
					<xsl:text>Mise � jour en continu</xsl:text>
				</xsl:when>
				<xsl:when test="$maintenanceTypeCode = 'notPlanned'">
					<xsl:text>Mise � jour non planifi�e</xsl:text>
				</xsl:when>
				<xsl:when test="$maintenanceTypeCode = 'unknown'">
					<xsl:text>Mise � jour Inconnue</xsl:text>	
				</xsl:when>	
				<xsl:otherwise>
					<xsl:text>Mise � jour p�riodique</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>
	
<!-- Template CategoryCode -->
	<xsl:template name="categoryCodeTemplate">
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
					<xsl:text>Climatologie/m�t�orologie</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'economy'">
					<xsl:text>Economie</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'elevation'">
					<xsl:text>Altim�trie</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'environment'">
					<xsl:text>Environnement</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'geoscientificinformation'">
					<xsl:text>Sciences de la Terre</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'health'">
					<xsl:text>Sant�</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'imageryBaseMapsEarthCover'">
					<xsl:text>Cartes de base, imagerie</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'intelligenceMilitary'">
					<xsl:text>Activit�s militaires</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'inlandWaters'">
					<xsl:text>Eaux int�rieures</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'location'">
					<xsl:text>Localisation</xsl:text>	
				</xsl:when>			
				<xsl:when test="$categoryCode = 'oceans'">
					<xsl:text>Oc�ans</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'planningCadastre'">
					<xsl:text>Cadastre, am�nagement</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'society'">
					<xsl:text>Soci�t�</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'structure'">
					<xsl:text>Constructions et ouvrages</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'transportation'">
					<xsl:text>Transport</xsl:text>	
				</xsl:when>	
				<xsl:when test="$categoryCode = 'utilitiesCommunication'">
					<xsl:text>R�seau de distribution et d'�vacuation</xsl:text>	
				</xsl:when>	
				<xsl:otherwise>
					<xsl:text>Inconnu</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>
	
</xsl:stylesheet>