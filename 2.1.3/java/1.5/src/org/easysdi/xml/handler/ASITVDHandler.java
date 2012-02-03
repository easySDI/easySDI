/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */
package org.easysdi.xml.handler;

import java.util.List;
import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.math.BigDecimal;
import java.math.BigInteger;
import java.util.Vector;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.Marshaller;

import org.easysdi._2008.ext.EXExtendedMetadataPropertyType;
import org.easysdi._2008.ext.EXExtendedMetadataType;
import org.easysdi.xml.handler.mapper.NamespacePrefixMapperImpl;
import org.isotc211._2005.gco.CharacterStringPropertyType;
import org.isotc211._2005.gco.CodeListValueType;
import org.isotc211._2005.gco.DatePropertyType;
import org.isotc211._2005.gco.DecimalPropertyType;
import org.isotc211._2005.gco.IntegerPropertyType;
import org.isotc211._2005.gmd.CIAddressPropertyType;
import org.isotc211._2005.gmd.CIAddressType;
import org.isotc211._2005.gmd.CICitationPropertyType;
import org.isotc211._2005.gmd.CICitationType;
import org.isotc211._2005.gmd.CIContactPropertyType;
import org.isotc211._2005.gmd.CIContactType;
import org.isotc211._2005.gmd.CIDatePropertyType;
import org.isotc211._2005.gmd.CIDateType;
import org.isotc211._2005.gmd.CIDateTypeCodePropertyType;
import org.isotc211._2005.gmd.CIResponsiblePartyPropertyType;
import org.isotc211._2005.gmd.CIResponsiblePartyType;
import org.isotc211._2005.gmd.CIRoleCodePropertyType;
import org.isotc211._2005.gmd.CITelephonePropertyType;
import org.isotc211._2005.gmd.CITelephoneType;
import org.isotc211._2005.gmd.CountryPropertyType;
import org.isotc211._2005.gmd.DQDataQualityPropertyType;
import org.isotc211._2005.gmd.DQDataQualityType;
import org.isotc211._2005.gmd.EXExtentPropertyType;
import org.isotc211._2005.gmd.EXExtentType;
import org.isotc211._2005.gmd.EXGeographicBoundingBoxType;
import org.isotc211._2005.gmd.EXGeographicExtentPropertyType;
import org.isotc211._2005.gmd.LILineagePropertyType;
import org.isotc211._2005.gmd.LILineageType;
import org.isotc211._2005.gmd.LIProcessStepPropertyType;
import org.isotc211._2005.gmd.LIProcessStepType;
import org.isotc211._2005.gmd.LISourcePropertyType;
import org.isotc211._2005.gmd.LISourceType;
import org.isotc211._2005.gmd.LanguageCodePropertyType;
import org.isotc211._2005.gmd.LocalisedCharacterStringPropertyType;
import org.isotc211._2005.gmd.LocalisedCharacterStringType;
import org.isotc211._2005.gmd.MDBrowseGraphicPropertyType;
import org.isotc211._2005.gmd.MDBrowseGraphicType;
import org.isotc211._2005.gmd.MDConstraintsPropertyType;
import org.isotc211._2005.gmd.MDDataIdentificationType;
import org.isotc211._2005.gmd.MDDatatypeCodePropertyType;
import org.isotc211._2005.gmd.MDDistributionPropertyType;
import org.isotc211._2005.gmd.MDDistributionType;
import org.isotc211._2005.gmd.MDDistributorPropertyType;
import org.isotc211._2005.gmd.MDDistributorType;
import org.isotc211._2005.gmd.MDExtendedElementInformationPropertyType;
import org.isotc211._2005.gmd.MDExtendedElementInformationType;
import org.isotc211._2005.gmd.MDGeometricObjectTypeCodePropertyType;
import org.isotc211._2005.gmd.MDGeometricObjectsPropertyType;
import org.isotc211._2005.gmd.MDGeometricObjectsType;
import org.isotc211._2005.gmd.MDIdentificationPropertyType;
import org.isotc211._2005.gmd.MDLegalConstraintsType;
import org.isotc211._2005.gmd.MDMaintenanceFrequencyCodePropertyType;
import org.isotc211._2005.gmd.MDMaintenanceInformationPropertyType;
import org.isotc211._2005.gmd.MDMaintenanceInformationType;
import org.isotc211._2005.gmd.MDMetadataExtensionInformationPropertyType;
import org.isotc211._2005.gmd.MDMetadataExtensionInformationType;
import org.isotc211._2005.gmd.MDReferenceSystemPropertyType;
import org.isotc211._2005.gmd.MDReferenceSystemType;
import org.isotc211._2005.gmd.MDSpatialRepresentationPropertyType;
import org.isotc211._2005.gmd.MDStandardOrderProcessPropertyType;
import org.isotc211._2005.gmd.MDStandardOrderProcessType;
import org.isotc211._2005.gmd.MDTopicCategoryCodePropertyType;
import org.isotc211._2005.gmd.MDTopicCategoryCodeType;
import org.isotc211._2005.gmd.MDVectorSpatialRepresentationType;
import org.isotc211._2005.gmd.ObjectFactory;
import org.isotc211._2005.gmd.PTFreeTextPropertyType;
import org.isotc211._2005.gmd.PTFreeTextType;
import org.isotc211._2005.gmd.PTLocalePropertyType;
import org.isotc211._2005.gmd.PTLocaleType;
import org.isotc211._2005.gmd.RSIdentifierPropertyType;
import org.isotc211._2005.gmd.RSIdentifierType;
import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.DefaultHandler;
import org.xml.sax.helpers.XMLReaderFactory;


public class ASITVDHandler extends DefaultHandler {

    private boolean isASITVD = false;
    private boolean isMetadta = false;
    private boolean isClass = false;
    private boolean isAttribute = false;
    private String className = "";
    private String className2 = "";
    private String attributeName = "";

    private String data;
    private org.isotc211._2005.gmd.MDMetadataType iso19139;
    private org.isotc211._2005.gco.ObjectFactory ofGco;
    private ObjectFactory ofMDMetadata;
    private MDDataIdentificationType mdDataIdentificationType;
    private MDDistributionType mdDistributionType;
    private String TypeDeMiseAJour = "";
    private String frequence = "";
    private String remarque = "";
    private String systemecoord = "", echelle = "", precision = "";
    private String role = "";
    private String Organisme = "";
    private String nom = "";
    private String adresse = "";
    private String localite = "";
    private String codepostal = "";
    private String pays = "";
    private String tel = "";
    private String fax = "";
    private String eMail = "";
    private String modeacquisition = "";
    private String descriptionmodeacquisition = "";
    private String donneesource = "";
    private Vector v = new Vector();

    public void startElement(String nameSpace, String localName, String qName,
	    Attributes attr) throws SAXException {

	if (qName.equals("ASIT-VD")) {
	    isASITVD = true;
	}
	if (qName.equals("metadata")) {
	    isMetadta = true;

	    //debut
	    startDocument();

	    CharacterStringPropertyType fileIndentifier = ofGco
		    .createCharacterStringPropertyType();
	    fileIndentifier.setCharacterString(ofGco.createCharacterString(attr
		    .getValue("id")));
	    iso19139.setFileIdentifier(fileIndentifier);
	    CharacterStringPropertyType metadataStandardName = ofGco
		    .createCharacterStringPropertyType();
	    metadataStandardName.setCharacterString(ofGco
		    .createCharacterString("ISO 19115:2003/19139"));
	    iso19139.setMetadataStandardName(metadataStandardName);
	    DatePropertyType dpt = ofGco.createDatePropertyType();
	    dpt.setDate(attr.getValue("creation"));
	    iso19139.setDateStamp(dpt);

	}
	if (qName.equals("class")) {

	    if (isClass == true) {
		//Sous classe
		className2 = attr.getValue("name");
	    } else {
		className = attr.getValue("name");
		className2 = attr.getValue("name");
	    }

	    isClass = true;
	}
	if (qName.equals("attribute")) {
	    isAttribute = true;
	    attributeName = attr.getValue("name");
	}

    }

    private IntegerPropertyType toIntegerPropertyType(String param) {
	IntegerPropertyType ipt = ofGco.createIntegerPropertyType();
	ipt.setInteger(new BigInteger(param));

	return ipt;
    }

    private DecimalPropertyType toDecimalPropertyType(Double param) {
	DecimalPropertyType dpt = ofGco.createDecimalPropertyType();
	dpt.setDecimal(new BigDecimal(param));

	return dpt;
    }

    private CharacterStringPropertyType toLocalisedCharacterStringPropertyType(
	    String param) {
	String locale = "fr-CH";

	PTFreeTextPropertyType ptFreeTextPropertyType = ofMDMetadata
		.createPTFreeTextPropertyType();
	PTFreeTextType ptFreeTextType = ofMDMetadata.createPTFreeTextType();
	ptFreeTextPropertyType.setPTFreeText(ptFreeTextType);

	LocalisedCharacterStringPropertyType localisedCharacterStringPropertyType = ofMDMetadata
		.createLocalisedCharacterStringPropertyType();
	LocalisedCharacterStringType localisedCharacterStringType = ofMDMetadata
		.createLocalisedCharacterStringType();
	localisedCharacterStringPropertyType
		.setLocalisedCharacterString(localisedCharacterStringType);
	localisedCharacterStringType.setLocale(locale);
	localisedCharacterStringType.setValue(param);

	ptFreeTextType.getTextGroup().add(localisedCharacterStringPropertyType);

	CharacterStringPropertyType cspt = ofGco
		.createCharacterStringPropertyType();

	cspt.setCharacterString(ofMDMetadata
		.createLocalisedCharacterString(localisedCharacterStringType));
	//cspt.setCharacterString(ofMDMetadata.createPTFreeText(ptFreeTextType));
	
	return cspt;
    }

    private CharacterStringPropertyType toCharacterStringPropertyType(
	    String param) {
	CharacterStringPropertyType cspt = ofGco
		.createCharacterStringPropertyType();
	cspt.setCharacterString(ofGco.createCharacterString(param));
	return cspt;
    }

    private CodeListValueType toCodeListValueType(String param) {
	CodeListValueType codeListValueType = ofGco.createCodeListValueType();
	codeListValueType.setCodeListValue(param);
	return codeListValueType;
    }

    public void endElement(String nameSpace, String localName, String qName)
	    throws SAXException {

	if (qName.equals("ASIT-VD")) {
	    isASITVD = false;
	}
	if (qName.equals("metadata")) {
	    isMetadta = false;
	    //FIN
	    endDocument2();
	    JAXBElement el = ofMDMetadata.createMDMetadata(iso19139);
	    v.add(el);

	}
	if (qName.equals("class")) {

	    if (className.equals("Contact")) {

		CIContactType ciContactType = ofMDMetadata
			.createCIContactType();
		CIContactPropertyType ciContactPropertyType = ofMDMetadata
			.createCIContactPropertyType();
		CIAddressPropertyType ciAddressPropertyType = ofMDMetadata
			.createCIAddressPropertyType();

		CIAddressType ciAddressType = ofMDMetadata
			.createCIAddressType();
		ciAddressType.getDeliveryPoint().add(
			toCharacterStringPropertyType(adresse));
		ciAddressType.setCountry(toCharacterStringPropertyType(pays));
		ciAddressType
			.setPostalCode(toCharacterStringPropertyType(codepostal));
		ciAddressType.setCity(toCharacterStringPropertyType(localite));
		ciAddressType.getElectronicMailAddress().add(
			toCharacterStringPropertyType(eMail));
		ciAddressPropertyType.setCIAddress(ciAddressType);
		ciContactType.setAddress(ciAddressPropertyType);

		CITelephonePropertyType ciTelephonePropertyType = ofMDMetadata
			.createCITelephonePropertyType();
		CITelephoneType ciTelephoneType = ofMDMetadata
			.createCITelephoneType();
		ciTelephoneType.getFacsimile().add(
			toCharacterStringPropertyType(tel));
		ciTelephoneType.getVoice().add(
			toCharacterStringPropertyType(fax));
		ciTelephonePropertyType.setCITelephone(ciTelephoneType);
		ciContactType.setPhone(ciTelephonePropertyType);

		ciContactPropertyType.setCIContact(ciContactType);

		CIResponsiblePartyPropertyType ciResponsiblePartyPropertyType = ofMDMetadata
			.createCIResponsiblePartyPropertyType();
		CIResponsiblePartyType ciResponsiblePartyType = ofMDMetadata
			.createCIResponsiblePartyType();
		ciResponsiblePartyType.setContactInfo(ciContactPropertyType);
		ciResponsiblePartyType
			.setIndividualName(toCharacterStringPropertyType(nom));
		ciResponsiblePartyType
			.setOrganisationName(toLocalisedCharacterStringPropertyType(Organisme));

		CodeListValueType codeListValueType = ofGco
			.createCodeListValueType();
		CIRoleCodePropertyType ciRoleCodePropertyType = ofMDMetadata
			.createCIRoleCodePropertyType();
		ciRoleCodePropertyType.setCIRoleCode(codeListValueType);
		ciResponsiblePartyType.setRole(ciRoleCodePropertyType);
		ciResponsiblePartyPropertyType
			.setCIResponsibleParty(ciResponsiblePartyType);

		if (role.equalsIgnoreCase("Gestionnaire")) {

		    codeListValueType.setCodeList("custodian");
		    iso19139.getContact().add(ciResponsiblePartyPropertyType);
		} else if (role.equalsIgnoreCase("Responsable de diffusion")
			|| role.equalsIgnoreCase("Responsablede diffusion")) {
		    codeListValueType.setCodeList("distributor");
		    MDDistributorPropertyType mdDistributorPropertyType = ofMDMetadata
			    .createMDDistributorPropertyType();
		    MDDistributorType mdDistributorType = ofMDMetadata
			    .createMDDistributorType();
		    mdDistributorType
			    .setDistributorContact(ciResponsiblePartyPropertyType);
		    mdDistributorPropertyType
			    .setMDDistributor(mdDistributorType);
		    mdDistributionType.getDistributor().add(
			    mdDistributorPropertyType);
		} else if (role.equalsIgnoreCase("Point de contact")) {
		    codeListValueType.setCodeList("pointOfContact");
		    mdDataIdentificationType.getPointOfContact().add(
			    ciResponsiblePartyPropertyType);
		} else {
		    System.err.println("role personne de contact inconnu : "
			    + role);
		    codeListValueType.setCodeList("custodian");
		    iso19139.getContact().add(ciResponsiblePartyPropertyType);
		}
	    }

	    if (className.equals("Représentation")
		    && className2.equals("Représentation")) {

		MDReferenceSystemPropertyType mdReferenceSystemPropertyType = ofMDMetadata
			.createMDReferenceSystemPropertyType();
		MDReferenceSystemType mdReferenceSystemType = ofMDMetadata
			.createMDReferenceSystemType();
		RSIdentifierPropertyType rsIdentifierPropertyType = ofMDMetadata
			.createRSIdentifierPropertyType();

		RSIdentifierType rsIdentifierType = ofMDMetadata
			.createRSIdentifierType();
		rsIdentifierType
			.setCode(toLocalisedCharacterStringPropertyType(systemecoord));
		rsIdentifierPropertyType.setRSIdentifier(rsIdentifierType);
		mdReferenceSystemType
			.setReferenceSystemIdentifier(rsIdentifierPropertyType);
		mdReferenceSystemPropertyType
			.setMDReferenceSystem(mdReferenceSystemType);

		iso19139.getReferenceSystemInfo().add(
			mdReferenceSystemPropertyType);
	    }

	    if (className.equals("Gestion") && className2.equals("Acquisition")) {

		LIProcessStepPropertyType liProcessStepPropertyType = ofMDMetadata
			.createLIProcessStepPropertyType();
		LIProcessStepType liProcessStepType = ofMDMetadata
			.createLIProcessStepType();
		liProcessStepType
			.setDescription(toLocalisedCharacterStringPropertyType(descriptionmodeacquisition));

		LISourcePropertyType liSourcePropertyType = ofMDMetadata
			.createLISourcePropertyType();
		LISourceType liSourceType = ofMDMetadata.createLISourceType();
		liSourcePropertyType.setLISource(liSourceType);
		liSourceType
			.setDescription(toLocalisedCharacterStringPropertyType(donneesource));

		liProcessStepType.getSource().add(liSourcePropertyType);

		liProcessStepPropertyType.setLIProcessStep(liProcessStepType);

		LILineageType liLineageType = ofMDMetadata
			.createLILineageType();
		LILineagePropertyType liLineagePropertyType = ofMDMetadata
			.createLILineagePropertyType();
		liLineageType.getProcessStep().add(liProcessStepPropertyType);
		liLineageType
			.setStatement(toLocalisedCharacterStringPropertyType(modeacquisition));
		liLineagePropertyType.setLILineage(liLineageType);

		DQDataQualityType dqDataQualityType = ofMDMetadata
			.createDQDataQualityType();
		DQDataQualityPropertyType dqDataQualityPropertyType = ofMDMetadata
			.createDQDataQualityPropertyType();
		dqDataQualityType.setLineage(liLineagePropertyType);

		dqDataQualityPropertyType.setDQDataQuality(dqDataQualityType);

		iso19139.getDataQualityInfo().add(dqDataQualityPropertyType);
	    }

	    if (className.equals("Gestion") && className2.equals("Mise à jour")) {
		MDMaintenanceFrequencyCodePropertyType mdMaintenanceFrequencyCodePropertyType = ofMDMetadata
			.createMDMaintenanceFrequencyCodePropertyType();
		if (TypeDeMiseAJour != null) {
		    if (TypeDeMiseAJour
			    .equalsIgnoreCase("Mise à jour en continu")) {
			mdMaintenanceFrequencyCodePropertyType
				.setMDMaintenanceFrequencyCode(toCodeListValueType("continual"));
		    }

		    if (TypeDeMiseAJour
			    .equalsIgnoreCase("Mise à jour occasionnelle")) {
			mdMaintenanceFrequencyCodePropertyType
				.setMDMaintenanceFrequencyCode(toCodeListValueType("unknown"));
		    }
		    if (TypeDeMiseAJour
			    .equalsIgnoreCase("Mise à jour par objet")) {
			mdMaintenanceFrequencyCodePropertyType
				.setMDMaintenanceFrequencyCode(toCodeListValueType("unknown"));
		    }
		    if (TypeDeMiseAJour
			    .equalsIgnoreCase("Mise à jour périodique")) {
			if (frequence.equalsIgnoreCase("Hebdomadaire")) {
			    mdMaintenanceFrequencyCodePropertyType
				    .setMDMaintenanceFrequencyCode(toCodeListValueType("weekly"));
			}
			if (frequence.equalsIgnoreCase("6 mois")) {
			    mdMaintenanceFrequencyCodePropertyType
				    .setMDMaintenanceFrequencyCode(toCodeListValueType("biannually"));
			} else if (frequence.equalsIgnoreCase("Annuelle")
				|| frequence.equalsIgnoreCase("Anuelle")
				|| frequence
					.equalsIgnoreCase("1 fois par année")) {
			    mdMaintenanceFrequencyCodePropertyType
				    .setMDMaintenanceFrequencyCode(toCodeListValueType("annually"));
			}
			if (frequence.equalsIgnoreCase("Tous les 3 ans")) {
			    mdMaintenanceFrequencyCodePropertyType
				    .setMDMaintenanceFrequencyCode(toCodeListValueType("3 years"));
			}
			if (frequence
				.equalsIgnoreCase("Selon mandat légal de la Confédération")) {
			    mdMaintenanceFrequencyCodePropertyType
				    .setMDMaintenanceFrequencyCode(toCodeListValueType("asneeded"));
			} else if (frequence.equalsIgnoreCase("tous les 6 ans")) {
			    mdMaintenanceFrequencyCodePropertyType
				    .setMDMaintenanceFrequencyCode(toCodeListValueType("6 years"));
			} else {
			    System.err.println("Fréquence inconnue : "
				    + frequence);
			    mdMaintenanceFrequencyCodePropertyType
				    .setMDMaintenanceFrequencyCode(toCodeListValueType(data));
			}

		    }
		    if (TypeDeMiseAJour.equalsIgnoreCase("Pas de mise à jour")) {
			mdMaintenanceFrequencyCodePropertyType
				.setMDMaintenanceFrequencyCode(toCodeListValueType("notPlanned"));
		    }
		}

		MDMaintenanceInformationType mdMaintenanceInformationType = ofMDMetadata
			.createMDMaintenanceInformationType();
		mdMaintenanceInformationType.getMaintenanceNote().add(
			toLocalisedCharacterStringPropertyType(remarque));
		mdMaintenanceInformationType
			.setMaintenanceAndUpdateFrequency(mdMaintenanceFrequencyCodePropertyType);

		MDMaintenanceInformationPropertyType mdMaintenanceInformationPropertyType = ofMDMetadata
			.createMDMaintenanceInformationPropertyType();
		mdMaintenanceInformationPropertyType
			.setMDMaintenanceInformation(mdMaintenanceInformationType);

		mdDataIdentificationType.getResourceMaintenance().add(
			mdMaintenanceInformationPropertyType);

	    }

	    if (className.equals(className2)) {
		isClass = false;
		className = "";
		className2 = "";
	    } else {
		//fin de la sous classe
		className2 = className;
		isClass = true;
	    }

	}

	if (qName.equals("attribute")) {

	    if (isAttribute && isClass && isMetadta && isASITVD) {

		if (className.equals("Diffusion")
			&& className2.equals("Statut juridique")
			&& attributeName.equals("Statut")) {
		    org.easysdi._2008.ext.ObjectFactory ofExt = new org.easysdi._2008.ext.ObjectFactory();
		    EXExtendedMetadataPropertyType exExtendedMetadataPropertyType = ofExt
			    .createEXExtendedMetadataPropertyType();
		    EXExtendedMetadataType exExtendedMetadataType = ofExt
			    .createEXExtendedMetadataType();
		    exExtendedMetadataPropertyType
			    .setEXExtendedMetadataType(exExtendedMetadataType);
		    exExtendedMetadataPropertyType.setTitle(className);
		    exExtendedMetadataType
			    .setName(toCharacterStringPropertyType(attributeName));
		    exExtendedMetadataType
			    .setValue(toLocalisedCharacterStringPropertyType(data));

		    iso19139.getExtendedMetadata().add(
			    exExtendedMetadataPropertyType);
		} else if (className.equals("Attribut")
			&& (attributeName.equals("Nom")
				|| attributeName.equals("Description")
				|| attributeName.equals("Type")
				|| attributeName.equals("Format") || attributeName
				.equals("Statut"))) {
		    org.easysdi._2008.ext.ObjectFactory ofExt = new org.easysdi._2008.ext.ObjectFactory();
		    EXExtendedMetadataPropertyType exExtendedMetadataPropertyType = ofExt
			    .createEXExtendedMetadataPropertyType();
		    EXExtendedMetadataType exExtendedMetadataType = ofExt
			    .createEXExtendedMetadataType();
		    exExtendedMetadataPropertyType
			    .setEXExtendedMetadataType(exExtendedMetadataType);
		    exExtendedMetadataPropertyType.setTitle(className);
		    exExtendedMetadataType
			    .setName(toCharacterStringPropertyType(attributeName));
		    exExtendedMetadataType
			    .setValue(toLocalisedCharacterStringPropertyType(data));
		    iso19139.getExtendedMetadata().add(
			    exExtendedMetadataPropertyType);
		} else if (className.equals("Représentation")
			&& className2.equals("Produit vecteur")
			&& (attributeName.equals("Modèle de données")
				|| attributeName.equals("Echelle de référence")
				|| attributeName.equals("Précision")
				|| attributeName
					.equals("Cohérence topologique") || attributeName
				.equals("Information altimétrique"))) {
		    org.easysdi._2008.ext.ObjectFactory ofExt = new org.easysdi._2008.ext.ObjectFactory();
		    EXExtendedMetadataPropertyType exExtendedMetadataPropertyType = ofExt
			    .createEXExtendedMetadataPropertyType();
		    EXExtendedMetadataType exExtendedMetadataType = ofExt
			    .createEXExtendedMetadataType();
		    exExtendedMetadataPropertyType
			    .setEXExtendedMetadataType(exExtendedMetadataType);
		    exExtendedMetadataPropertyType.setTitle(className);
		    exExtendedMetadataType
			    .setName(toCharacterStringPropertyType(attributeName));
		    exExtendedMetadataType
			    .setValue(toLocalisedCharacterStringPropertyType(data));
		    iso19139.getExtendedMetadata().add(
			    exExtendedMetadataPropertyType);
		} else

		if (className.equals("Représentation")
			&& className2.equals("Produit vecteur")
			&& attributeName.equals("Type d'objet graphique")) {

		    MDGeometricObjectTypeCodePropertyType mdGeometricObjectTypeCodePropertyType = ofMDMetadata
			    .createMDGeometricObjectTypeCodePropertyType();
		    if (data.equals("Ligne")) {
			mdGeometricObjectTypeCodePropertyType
				.setMDGeometricObjectTypeCode(toCodeListValueType("line"));
		    } else if (data.equals("Mixte")) {
			mdGeometricObjectTypeCodePropertyType
				.setMDGeometricObjectTypeCode(toCodeListValueType("composite"));
		    } else if (data.equals("Point")) {
			mdGeometricObjectTypeCodePropertyType
				.setMDGeometricObjectTypeCode(toCodeListValueType("point"));
		    } else if (data.equals("Surface")) {
			mdGeometricObjectTypeCodePropertyType
				.setMDGeometricObjectTypeCode(toCodeListValueType("surface"));
		    } else {
			System.out
				.println("Type d'objet graphique non pris en charge :"
					+ data);
		    }
		    MDGeometricObjectsType mdGeometricObjectsType = ofMDMetadata
			    .createMDGeometricObjectsType();
		    MDGeometricObjectsPropertyType mdGeometricObjectsPropertyType = ofMDMetadata
			    .createMDGeometricObjectsPropertyType();
		    mdGeometricObjectsType
			    .setGeometricObjectType(mdGeometricObjectTypeCodePropertyType);
		    mdGeometricObjectsPropertyType
			    .setMDGeometricObjects(mdGeometricObjectsType);
		    //MDVectorSpatialRepresentationPropertyType mdVectorSpatialRepresentationPropertyType = ofMDMetadata.createMDVectorSpatialRepresentationPropertyType();
		    MDVectorSpatialRepresentationType mdVectorSpatialRepresentationType = ofMDMetadata
			    .createMDVectorSpatialRepresentationType();
		    mdVectorSpatialRepresentationType.getGeometricObjects()
			    .add(mdGeometricObjectsPropertyType);

		    //mdVectorSpatialRepresentationPropertyType.setMDVectorSpatialRepresentation(mdVectorSpatialRepresentationType);
		    MDSpatialRepresentationPropertyType mdSpatialRepresentationPropertyType = ofMDMetadata
			    .createMDSpatialRepresentationPropertyType();
		    //ofMDMetadata.createMDSpatialRepresentationTypeCode();					
		    mdSpatialRepresentationPropertyType
			    .setAbstractMDSpatialRepresentation(ofMDMetadata
				    .createMDVectorSpatialRepresentation(mdVectorSpatialRepresentationType));

		    iso19139.getSpatialRepresentationInfo().add(
			    mdSpatialRepresentationPropertyType);

		} else

		if (className.equals("Contact") && attributeName.equals("Rôle")) {
		    role = data;
		} else if (className.equals("Contact")
			&& attributeName.equals("Organisme")) {
		    Organisme = data;
		} else if (className.equals("Contact")
			&& attributeName.equals("Nom")) {
		    nom = data;
		} else if (className.equals("Contact")
			&& attributeName.equals("Adresse")) {
		    adresse = data;
		} else if (className.equals("Contact")
			&& attributeName.equals("Localité")) {
		    localite = data;
		} else if (className.equals("Contact")
			&& attributeName.equals("Code postal")) {
		    codepostal = data;
		} else if (className.equals("Contact")
			&& attributeName.equals("Pays")) {
		    pays = data;
		} else if (className.equals("Contact")
			&& attributeName.equals("Téléphone")) {
		    tel = data;
		} else if (className.equals("Contact")
			&& attributeName.equals("Fax")) {
		    fax = data;
		} else if (className.equals("Contact")
			&& attributeName.equals("Email")) {
		    eMail = data;
		} else if (className.equals("Représentation")
			&& attributeName.equals("Système de coordonnées")) {
		    if (data != null) {
			if (data.equals("Coordonnées nationales suisses")) {
			    systemecoord = "EPSG:21781";
			} else {
			    systemecoord = data;
			}
		    }
		} else if (className.equals("Représentation")
			&& attributeName.equals("Echelle de référence")) {
		    echelle = data;
		} else if (className.equals("Représentation")
			&& attributeName.equals("Précision")) {
		    precision = data;
		} else

		if (className.equals("Gestion")
			&& className2.equals("Acquisition")
			&& attributeName.equals("Mode d'acquisition")) {
		    modeacquisition = data;
		} else if (className.equals("Gestion")
			&& className2.equals("Acquisition")
			&& attributeName
				.equals("Description du mode d'acquisition")) {
		    descriptionmodeacquisition = data;
		} else if (className.equals("Gestion")
			&& className2.equals("Acquisition")
			&& attributeName.equals("Données sources")) {
		    donneesource = data;
		} else

		if (className.equals("Gestion")
			&& className2.equals("Mise à jour")
			&& attributeName.equals("Remarques")) {
		    remarque = data;
		} else if (className.equals("Gestion")
			&& className2.equals("Mise à jour")
			&& attributeName.equals("Fréquence")) {
		    frequence = data;
		} else if (className.equals("Gestion")
			&& className2.equals("Mise à jour")
			&& attributeName.equals("Type de mise à jour")) {
		    TypeDeMiseAJour = data;
		} else

		if (className.equals("Diffusion")
			&& className2.equals("Statut juridique")
			&& attributeName.equals("Référence du document légal")) {
		    MDLegalConstraintsType mdLegalConstraintsType = ofMDMetadata
			    .createMDLegalConstraintsType();
		    mdLegalConstraintsType.getOtherConstraints().add(
			    toLocalisedCharacterStringPropertyType(data));

		    MDConstraintsPropertyType mdConstraintsPropertyType = ofMDMetadata
			    .createMDConstraintsPropertyType();
		    mdConstraintsPropertyType.setTitle(attributeName);

		    JAXBElement<MDLegalConstraintsType> mdConstraints = ofMDMetadata
			    .createMDLegalConstraints(mdLegalConstraintsType);

		    mdConstraintsPropertyType.setMDConstraints(mdConstraints);

		    mdDataIdentificationType.getResourceConstraints().add(
			    mdConstraintsPropertyType);
		} else

		if (className.equals("Diffusion")
			&& attributeName
				.equals("Principes et mode de tarification")) {
		    MDStandardOrderProcessPropertyType mdStandardOrderProcessPropertyType = ofMDMetadata
			    .createMDStandardOrderProcessPropertyType();
		    MDStandardOrderProcessType mdStandardOrderProcessType = ofMDMetadata
			    .createMDStandardOrderProcessType();
		    mdStandardOrderProcessType
			    .setFees(toLocalisedCharacterStringPropertyType(data));
		    mdStandardOrderProcessPropertyType
			    .setMDStandardOrderProcess(mdStandardOrderProcessType);

		    MDDistributorType mdDistributorType = ofMDMetadata
			    .createMDDistributorType();
		    mdDistributorType.getDistributionOrderProcess().add(
			    mdStandardOrderProcessPropertyType);

		    MDDistributorPropertyType mdDistributorPropertyType = ofMDMetadata
			    .createMDDistributorPropertyType();
		    mdDistributorPropertyType
			    .setMDDistributor(mdDistributorType);

		    mdDistributionType.getDistributor().add(
			    mdDistributorPropertyType);
		} else if (className.equals("Diffusion")
			&& attributeName.equals("Restriction d'utilisation")) {

		    MDLegalConstraintsType mdLegalConstraintsType = ofMDMetadata
			    .createMDLegalConstraintsType();
		    mdLegalConstraintsType.getUseLimitation().add(
			    toLocalisedCharacterStringPropertyType(data));
		    MDConstraintsPropertyType mdConstraintsPropertyType = ofMDMetadata
			    .createMDConstraintsPropertyType();

		    JAXBElement<MDLegalConstraintsType> mdConstraints = ofMDMetadata
			    .createMDLegalConstraints(mdLegalConstraintsType);

		    mdConstraintsPropertyType.setMDConstraints(mdConstraints);

		    mdDataIdentificationType.getResourceConstraints().add(
			    mdConstraintsPropertyType);
		} else

		if (className.equals("Diffusion")
			&& attributeName.equals("Conditions de diffusion")) {

		    MDLegalConstraintsType mdLegalConstraintsType = ofMDMetadata
			    .createMDLegalConstraintsType();
		    mdLegalConstraintsType.getOtherConstraints().add(
			    toLocalisedCharacterStringPropertyType(data));

		    MDConstraintsPropertyType mdConstraintsPropertyType = ofMDMetadata
			    .createMDConstraintsPropertyType();
		    mdConstraintsPropertyType.setTitle(attributeName);

		    JAXBElement<MDLegalConstraintsType> mdConstraints = ofMDMetadata
			    .createMDLegalConstraints(mdLegalConstraintsType);

		    mdConstraintsPropertyType.setMDConstraints(mdConstraints);

		    mdDataIdentificationType.getResourceConstraints().add(
			    mdConstraintsPropertyType);
		} else if (className.equals("Identification")
			&& attributeName.equals("Thématique")) {
		    MDTopicCategoryCodePropertyType mdTopicCategoryCodePropertyType = ofMDMetadata
			    .createMDTopicCategoryCodePropertyType();

		    if (data.equalsIgnoreCase("Cadastre, aménagement")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.PLANNING_CADASTRE);
		    } else if (data.equalsIgnoreCase("Activité militaire")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.INTELLIGENCE_MILITARY);
		    } else if (data.equalsIgnoreCase("Agriculture")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.FARMING);
		    } else if (data.equalsIgnoreCase("Biologie")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.BIOTA);
		    } else if (data
			    .equalsIgnoreCase("Cartes de base, imagerie")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.IMAGERY_BASE_MAPS_EARTH_COVER);
		    } else if (data
			    .equalsIgnoreCase("Climatologie/Météorologie")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.CLIMATOLOGY_METEOROLOGY_ATMOSPHERE);
		    } else if (data
			    .equalsIgnoreCase("Construction, équipements, monuments")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.STRUCTURE);
		    } else if (data.equalsIgnoreCase("Eaux douces")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.INLAND_WATERS);
		    } else if (data.equalsIgnoreCase("Economie")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.ECONOMY);
		    } else if (data.equalsIgnoreCase("Elévation, altimétrie")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.ELEVATION);
		    } else if (data.equalsIgnoreCase("Environnement")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.ENVIRONMENT);
		    } else if (data.equalsIgnoreCase("Frontières")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.BOUNDARIES);
		    } else if (data
			    .equalsIgnoreCase("Information géoscientifique")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.GEOSCIENTIFIC_INFORMATION);
		    } else if (data
			    .equalsIgnoreCase("Infrastructure ou service de réseau")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.UTILITIES_COMMUNICATION);
		    } else if (data.equalsIgnoreCase("Localisation")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.LOCATION);
		    } else if (data.equalsIgnoreCase("Océans")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.OCEANS);
		    } else if (data.equalsIgnoreCase("Santé")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.HEALTH);
		    } else if (data.equalsIgnoreCase("Société")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.SOCIETY);
		    } else if (data.equalsIgnoreCase("Transport")) {
			mdTopicCategoryCodePropertyType
				.setMDTopicCategoryCode(MDTopicCategoryCodeType.TRANSPORTATION);
		    } else
			System.err
				.println("Le theme n'est pas connu : " + data);

		    mdDataIdentificationType.getTopicCategory().add(
			    mdTopicCategoryCodePropertyType);

		} else

		if (className.equals("Identification")
			&& attributeName.equals("Dernière mise à jour")) {

		    CICitationPropertyType ciCitationPropertyType = mdDataIdentificationType
			    .getCitation();
		    if (ciCitationPropertyType == null) {
			ciCitationPropertyType = ofMDMetadata
				.createCICitationPropertyType();
		    }

		    CICitationType ciCitationType = ciCitationPropertyType
			    .getCICitation();
		    if (ciCitationType == null) {
			ciCitationType = ofMDMetadata.createCICitationType();
		    }

		    DatePropertyType dpt = ofGco.createDatePropertyType();
		    dpt.setDate(data);
		    CIDateType ciDateType = ofMDMetadata.createCIDateType();
		    CIDateTypeCodePropertyType ciDateTypeCodePropertyType = ofMDMetadata
			    .createCIDateTypeCodePropertyType();
		    ciDateType.setDate(dpt);

		    ciDateTypeCodePropertyType
			    .setCIDateTypeCode(toCodeListValueType("revision"));
		    ciDateType.setDateType(ciDateTypeCodePropertyType);

		    CIDatePropertyType ciDatePropertyType = ofMDMetadata
			    .createCIDatePropertyType();
		    ciDatePropertyType.setCIDate(ciDateType);

		    ciCitationType.getDate().add(ciDatePropertyType);

		    ciCitationPropertyType.setCICitation(ciCitationType);
		    mdDataIdentificationType
			    .setCitation(ciCitationPropertyType);

		} else

		if (className.equals("Identification")
			&& attributeName.equals("Extrait")) {
		    MDBrowseGraphicType mdBrowseGraphicType = ofMDMetadata
			    .createMDBrowseGraphicType();
		    mdBrowseGraphicType
			    .setFileDescription(toLocalisedCharacterStringPropertyType(data));
		    mdBrowseGraphicType
			    .setFileType(toCharacterStringPropertyType(data));
		    mdBrowseGraphicType
			    .setFileName(toCharacterStringPropertyType(data));
		    MDBrowseGraphicPropertyType mdBrowseGraphicPropertyType = ofMDMetadata
			    .createMDBrowseGraphicPropertyType();
		    mdBrowseGraphicPropertyType
			    .setMDBrowseGraphic(mdBrowseGraphicType);

		    mdDataIdentificationType.getGraphicOverview().add(
			    mdBrowseGraphicPropertyType);
		} else

		if (className.equals("Identification")
			&& attributeName.equals("Synoptique")) {
		    MDBrowseGraphicType mdBrowseGraphicType = ofMDMetadata
			    .createMDBrowseGraphicType();
		    mdBrowseGraphicType
			    .setFileDescription(toLocalisedCharacterStringPropertyType(data));
		    mdBrowseGraphicType
			    .setFileType(toCharacterStringPropertyType(data));
		    mdBrowseGraphicType
			    .setFileName(toCharacterStringPropertyType(data));
		    MDBrowseGraphicPropertyType mdBrowseGraphicPropertyType = ofMDMetadata
			    .createMDBrowseGraphicPropertyType();
		    mdBrowseGraphicPropertyType
			    .setMDBrowseGraphic(mdBrowseGraphicType);

		    mdDataIdentificationType.getGraphicOverview().add(
			    mdBrowseGraphicPropertyType);
		} else if (className.equals("Identification")
			&& attributeName.equals("Etendue géographique")) {
		    EXExtentType exExtentType = ofMDMetadata
			    .createEXExtentType();
		    exExtentType
			    .setDescription(toLocalisedCharacterStringPropertyType(data));

		    EXGeographicExtentPropertyType exGeographicExtentPropertyType = ofMDMetadata
			    .createEXGeographicExtentPropertyType();

		    EXGeographicBoundingBoxType exGeographicBoundingBoxType = ofMDMetadata
			    .createEXGeographicBoundingBoxType();

		    exGeographicBoundingBoxType
			    .setNorthBoundLatitude(toDecimalPropertyType(46.98));
		    exGeographicBoundingBoxType
			    .setSouthBoundLatitude(toDecimalPropertyType(46.18));
		    exGeographicBoundingBoxType
			    .setEastBoundLongitude(toDecimalPropertyType(7.25));
		    exGeographicBoundingBoxType
			    .setWestBoundLongitude(toDecimalPropertyType(6.07));

		    exGeographicExtentPropertyType
			    .setAbstractEXGeographicExtent(ofMDMetadata
				    .createEXGeographicBoundingBox(exGeographicBoundingBoxType));

		    exExtentType.getGeographicElement().add(
			    exGeographicExtentPropertyType);

		    EXExtentPropertyType exExtentPropertyType = ofMDMetadata
			    .createEXExtentPropertyType();
		    exExtentPropertyType.setEXExtent(exExtentType);

		    mdDataIdentificationType.getExtent().add(
			    exExtentPropertyType);
		} else

		if (className.equals("Identification")
			&& attributeName.equals("Description")) {
		    mdDataIdentificationType
			    .setAbstract(toLocalisedCharacterStringPropertyType(data));
		} else

		if (className.equals("Identification")
			&& attributeName.equals("Nom")) {

		    CICitationPropertyType ciCitationPropertyType = mdDataIdentificationType
			    .getCitation();
		    if (ciCitationPropertyType == null) {
			ciCitationPropertyType = ofMDMetadata
				.createCICitationPropertyType();
		    }

		    CICitationType ciCitationType = ciCitationPropertyType
			    .getCICitation();
		    if (ciCitationType == null) {
			ciCitationType = ofMDMetadata.createCICitationType();
		    }
		    ciCitationType
			    .setTitle(toLocalisedCharacterStringPropertyType(data));

		    ciCitationPropertyType.setCICitation(ciCitationType);
		    mdDataIdentificationType
			    .setCitation(ciCitationPropertyType);
		} else {
				    
		    MDMetadataExtensionInformationPropertyType o = ofMDMetadata.createMDMetadataExtensionInformationPropertyType();
		    MDMetadataExtensionInformationType a = ofMDMetadata.createMDMetadataExtensionInformationType();
		    
		    MDExtendedElementInformationPropertyType b = ofMDMetadata.createMDExtendedElementInformationPropertyType();
		    MDExtendedElementInformationType d = ofMDMetadata.createMDExtendedElementInformationType();
		    d.setName(toCharacterStringPropertyType(attributeName));
		    //className
		    d.setDomainValue(toLocalisedCharacterStringPropertyType(data));
		    
		    b.setMDExtendedElementInformation(d);
		    b.setTitle(className);
		    //ofMDMetadata.
		    a.getExtendedElementInformation().add(b);
		    o.setMDMetadataExtensionInformation(a);
		    
		    /*
		     * 
		    MDExtendedElementInformationType a = ofMDMetadata.createMDExtendedElementInformationType();
		    JAXBElement<MDExtendedElementInformationType> b = ofMDMetadata.createMDExtendedElementInformation(a);
		    MDExtendedElementInformationPropertyType c = ofMDMetadata.createMDExtendedElementInformationPropertyType();
		    
		    
		    iso19139.getExtendedMetadata().add(c);
		    */
		    iso19139.getMetadataExtensionInfo().add(o);
		    
		    /*org.easysdi._2008.ext.ObjectFactory ofExt = new org.easysdi._2008.ext.ObjectFactory();
		    EXExtendedMetadataPropertyType exExtendedMetadataPropertyType = ofExt
			    .createEXExtendedMetadataPropertyType();
		    EXExtendedMetadataType exExtendedMetadataType = ofExt
			    .createEXExtendedMetadataType();
		    exExtendedMetadataPropertyType
			    .setEXExtendedMetadataType(exExtendedMetadataType);
		    exExtendedMetadataPropertyType.setTitle(className);
		    exExtendedMetadataType
			    .setName(toCharacterStringPropertyType(attributeName));
		    exExtendedMetadataType
			    .setValue(toLocalisedCharacterStringPropertyType(data));

		    iso19139.getExtendedMetadata().add(
			    exExtendedMetadataPropertyType);*/
		}

	    }
	    data = "";
	    isAttribute = false;

	}
    }

    public void startDocument() {
	try {
	    isClass = false;
	    isAttribute = false;
	    className = "";
	    className2 = "";
	    attributeName = "";
	    data = "";
	    iso19139 = null;
	    ofGco = null;
	    ofMDMetadata = null;
	    mdDataIdentificationType = null;
	    mdDistributionType = null;
	    TypeDeMiseAJour = "";
	    frequence = "";
	    remarque = "";
	    systemecoord = "";
	    echelle = "";
	    precision = "";
	    role = "";
	    Organisme = "";
	    nom = "";
	    adresse = "";
	    localite = "";
	    codepostal = "";
	    pays = "";
	    tel = "";
	    fax = "";
	    eMail = "";
	    modeacquisition = "";
	    descriptionmodeacquisition = "";
	    donneesource = "";

	    ofMDMetadata = new ObjectFactory();
	    ofGco = new org.isotc211._2005.gco.ObjectFactory();

	    iso19139 = ofMDMetadata.createMDMetadataType();
	    ofMDMetadata.createMDMetadataExtensionInformationPropertyType();

	    mdDataIdentificationType = ofMDMetadata
		    .createMDDataIdentificationType();

	    mdDistributionType = ofMDMetadata.createMDDistributionType();

	} catch (Exception e) {
	    e.printStackTrace();
	}

    }

    public void endDocument2() {
	JAXBElement<MDDataIdentificationType> dataIdentificationType = ofMDMetadata
		.createMDDataIdentification(mdDataIdentificationType);
	MDIdentificationPropertyType mdIdentificationPropertyType = ofMDMetadata
		.createMDIdentificationPropertyType();
	mdIdentificationPropertyType
		.setAbstractMDIdentification(dataIdentificationType);
	iso19139.getIdentificationInfo().add(mdIdentificationPropertyType);

	MDDistributionPropertyType mdDistributionPropertyType = ofMDMetadata
		.createMDDistributionPropertyType();
	mdDistributionPropertyType.setMDDistribution(mdDistributionType);

	iso19139.setDistributionInfo(mdDistributionPropertyType);
	PTLocalePropertyType ptLocalePropertyType = ofMDMetadata
		.createPTLocalePropertyType();
	PTLocaleType ptLocaleType = ofMDMetadata.createPTLocaleType();
	ptLocalePropertyType.setPTLocale(ptLocaleType);

	CountryPropertyType countryPropertyType = ofMDMetadata
		.createCountryPropertyType();
	countryPropertyType.setCountry(toCodeListValueType("CH"));
	ptLocaleType.setCountry(countryPropertyType);

	LanguageCodePropertyType languageCodePropertyType = ofMDMetadata
		.createLanguageCodePropertyType();
	languageCodePropertyType.setLanguageCode(toCodeListValueType("fr"));
	ptLocaleType.setLanguageCode(languageCodePropertyType);

	iso19139.getLocale().add(ptLocalePropertyType);

    }

    public void endDocument() {
    }

    public void characters(char[] caracteres, int debut, int longueur)
	    throws SAXException {

	String donnees = new String(caracteres, debut, longueur);
	if (data == null)
	    data = donnees.trim();
	else
	    data = data + donnees.trim();
    }

    public List getMDMetadata() {
	return v;
	//JAXBElement el = ofMDMetadata.createMDMetadata(iso19139);		
	//return el;
    }

    /**
     * This program reads all the file in the folder c:\download and
     * converts the non-standard asitvd metadata into the standard iso19139 under the directory c:\download\iso
     * @param args
     */
    public static void main(String args[]) {
	try {

	    File dir = new File("C:\\download\\");
	    //String[] s = dir.list();
	    String [] s={"391.txt"};
	    //String [] s={"10by10\\0.txt"};
	    for (int i = 0; i < s.length; i++) {
		File f = new File("C:\\download\\" + s[i]);
		if (f.isFile()) {
		    XMLReader xr = XMLReaderFactory.createXMLReader();
		    ASITVDHandler avdHandler = new ASITVDHandler();

		    InputStream is = new java.io.FileInputStream(new File(
			    "C:\\download\\" + s[i]));
		    System.out.println("C:\\download\\" + s[i]);

		    xr.setContentHandler(avdHandler);
		    xr.parse(new InputSource(is));
		    List l = avdHandler.getMDMetadata();
		    for (int j = 0; j < l.size(); j++) {
			JAXBElement el = (JAXBElement) l.get(j);

			JAXBContext jc2 =

			JAXBContext
				.newInstance(org.isotc211._2005.gmd.MDMetadataType.class);

			Marshaller m = jc2.createMarshaller();

			m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT,
				Boolean.TRUE);
			m.setProperty("com.sun.xml.bind.namespacePrefixMapper",
				new NamespacePrefixMapperImpl());
			m.setProperty(Marshaller.JAXB_ENCODING, "UTF-8");
			m.setProperty(Marshaller.JAXB_FRAGMENT, Boolean.FALSE);

			m.marshal(el, new FileOutputStream(new File(
				"C:\\download\\iso\\" + i + "_" + j + ".xml")));
			//m.marshal( el, System.out );

		    }
		}
	    }
	} catch (Exception e) {
	    // TODO Auto-generated catch block
	    e.printStackTrace();
	}

    }
}
