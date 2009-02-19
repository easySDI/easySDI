/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
package ch.depth.migration.asit;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.math.BigDecimal;
import java.math.BigInteger;
import java.util.Hashtable;
import java.util.List;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.Marshaller;
import javax.xml.bind.Unmarshaller;

import org.isotc211._2005.gco.CharacterStringPropertyType;
import org.isotc211._2005.gco.CodeListValueType;
import org.isotc211._2005.gco.DatePropertyType;
import org.isotc211._2005.gco.DecimalPropertyType;
import org.isotc211._2005.gco.IntegerPropertyType;
import org.isotc211._2005.gmd.AbstractMDIdentificationType;
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
import org.isotc211._2005.gmd.DQDataQualityPropertyType;
import org.isotc211._2005.gmd.DQDataQualityType;
import org.isotc211._2005.gmd.EXExtentPropertyType;
import org.isotc211._2005.gmd.EXExtentType;
import org.isotc211._2005.gmd.EXGeographicBoundingBoxType;
import org.isotc211._2005.gmd.EXGeographicExtentPropertyType;
import org.isotc211._2005.gmd.LIProcessStepPropertyType;
import org.isotc211._2005.gmd.LIProcessStepType;
import org.isotc211._2005.gmd.LISourcePropertyType;
import org.isotc211._2005.gmd.LISourceType;
import org.isotc211._2005.gmd.LocalisedCharacterStringPropertyType;
import org.isotc211._2005.gmd.LocalisedCharacterStringType;
import org.isotc211._2005.gmd.MDBrowseGraphicPropertyType;
import org.isotc211._2005.gmd.MDBrowseGraphicType;
import org.isotc211._2005.gmd.MDConstraintsPropertyType;
import org.isotc211._2005.gmd.MDDataIdentificationType;
import org.isotc211._2005.gmd.MDDistributionPropertyType;
import org.isotc211._2005.gmd.MDDistributionType;
import org.isotc211._2005.gmd.MDDistributorPropertyType;
import org.isotc211._2005.gmd.MDDistributorType;
import org.isotc211._2005.gmd.MDExtendedElementInformationPropertyType;
import org.isotc211._2005.gmd.MDExtendedElementInformationType;
import org.isotc211._2005.gmd.MDIdentificationPropertyType;
import org.isotc211._2005.gmd.MDLegalConstraintsType;
import org.isotc211._2005.gmd.MDMaintenanceFrequencyCodePropertyType;
import org.isotc211._2005.gmd.MDMaintenanceInformationPropertyType;
import org.isotc211._2005.gmd.MDMaintenanceInformationType;
import org.isotc211._2005.gmd.MDMetadataExtensionInformationPropertyType;
import org.isotc211._2005.gmd.MDMetadataExtensionInformationType;
import org.isotc211._2005.gmd.MDMetadataType;
import org.isotc211._2005.gmd.MDObligationCodePropertyType;
import org.isotc211._2005.gmd.MDObligationCodeType;
import org.isotc211._2005.gmd.MDReferenceSystemPropertyType;
import org.isotc211._2005.gmd.MDReferenceSystemType;
import org.isotc211._2005.gmd.MDRepresentativeFractionPropertyType;
import org.isotc211._2005.gmd.MDRepresentativeFractionType;
import org.isotc211._2005.gmd.MDResolutionPropertyType;
import org.isotc211._2005.gmd.MDResolutionType;
import org.isotc211._2005.gmd.MDStandardOrderProcessPropertyType;
import org.isotc211._2005.gmd.MDStandardOrderProcessType;
import org.isotc211._2005.gmd.MDTopicCategoryCodePropertyType;
import org.isotc211._2005.gmd.MDTopicCategoryCodeType;
import org.isotc211._2005.gmd.ObjectFactory;
import org.isotc211._2005.gmd.PTFreeTextPropertyType;
import org.isotc211._2005.gmd.PTFreeTextType;
import org.isotc211._2005.gmd.RSIdentifierPropertyType;
import org.isotc211._2005.gmd.RSIdentifierType;

import ch.depth._2008.ext.EXExtendedMetadataPropertyType;
import ch.depth._2008.ext.EXExtendedMetadataType;
import ch.depth.migration.asit.metadata.Class;
import ch.depth.xml.handler.mapper.NamespacePrefixMapperImpl;

/**
 * @author Administrateur
 *
 */
public class Asit2Iso {

    ObjectFactory ofMDMetadata ;
    org.isotc211._2005.gmd.MDMetadataType iso19139;
    org.isotc211._2005.gco.ObjectFactory ofGco ;
    ch.depth.migration.asit.metadata.ASITVD.Metadata m;
    Hashtable  v = new Hashtable();


    private IntegerPropertyType toIntegerPropertyType(String param) {

	IntegerPropertyType ipt = ofGco.createIntegerPropertyType();
	ipt.setInteger(new BigInteger(param.trim()));

	return ipt;
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

	return cspt;
    }

    private DecimalPropertyType toDecimalPropertyType(double param) {
	DecimalPropertyType dpt = ofGco.createDecimalPropertyType();
	dpt.setDecimal(new BigDecimal(param));

	return dpt;
    }


    public Asit2Iso(ch.depth.migration.asit.metadata.ASITVD.Metadata pM){
	m = pM;
	ofMDMetadata = new ObjectFactory();
	iso19139 = ofMDMetadata.createMDMetadataType();
	ofGco = new org.isotc211._2005.gco.ObjectFactory();
	//ofMDMetadata.createMDMetadataExtensionInformationPropertyType();



	System.out.println("============================================");
	System.out.println(m.getId());
	CharacterStringPropertyType fileIndentifier = ofGco.createCharacterStringPropertyType();
	fileIndentifier.setCharacterString(ofGco.createCharacterString(m.getId()));


	iso19139.setFileIdentifier(fileIndentifier);
	CharacterStringPropertyType metadataStandardName = ofGco.createCharacterStringPropertyType();
	metadataStandardName.setCharacterString(ofGco.createCharacterString("ISO 19115:2003/19139"));
	iso19139.setMetadataStandardName(metadataStandardName);
	DatePropertyType dpt = ofGco.createDatePropertyType();
	dpt.setDate(m.getCreation());
	iso19139.setDateStamp(dpt);


    }
    private void addExtendedMetadataProperty(String className,String attributeName,String data){

	if(data!= null && data.length()>0){
	    ch.depth._2008.ext.ObjectFactory ofExt = new ch.depth._2008.ext.ObjectFactory();
	    
	    EXExtendedMetadataPropertyType exExtendedMetadataPropertyType = ofExt.createEXExtendedMetadataPropertyType();	    
	    EXExtendedMetadataType exExtendedMetadataType = ofExt.createEXExtendedMetadataType();
	    exExtendedMetadataPropertyType.setEXExtendedMetadataType(exExtendedMetadataType);
	    exExtendedMetadataPropertyType.setTitle(className);
	    exExtendedMetadataType.setName(toCharacterStringPropertyType(attributeName));
	    exExtendedMetadataType.setValue(toLocalisedCharacterStringPropertyType(data));

	    iso19139.getExtendedMetadata().add(exExtendedMetadataPropertyType);

	}
    }
    private void checkIdentification (){

	//Si pas de rubrique identification on en crée une
	if (iso19139.getIdentificationInfo().size()==0){
	    iso19139.getIdentificationInfo().add(ofMDMetadata.createMDIdentificationPropertyType());
	}

	MDIdentificationPropertyType mdIdentificationPropertyType = iso19139.getIdentificationInfo().get(0);

	if (mdIdentificationPropertyType.getAbstractMDIdentification() == null){
	    MDDataIdentificationType mdDataIdentificationType = ofMDMetadata.createMDDataIdentificationType();
	    JAXBElement<MDDataIdentificationType> dataIdentificationType = ofMDMetadata.createMDDataIdentification(mdDataIdentificationType);	
	    mdIdentificationPropertyType.setAbstractMDIdentification(dataIdentificationType);
	}
    }

    private void checkDiffusion(){

	if (iso19139.getDistributionInfo() == null){
	    MDDistributionPropertyType mdDistributionPropertyType = ofMDMetadata.createMDDistributionPropertyType();
	    iso19139.setDistributionInfo(mdDistributionPropertyType);			
	}    
	if (iso19139.getDistributionInfo().getMDDistribution() == null){
	    iso19139.getDistributionInfo().setMDDistribution(ofMDMetadata.createMDDistributionType());
	}

    }




    private void checkGestionAcquisition(){

	if (iso19139.getDataQualityInfo().size()==0){

	    DQDataQualityPropertyType dqDataQualityPropertyType = ofMDMetadata.createDQDataQualityPropertyType();

	    iso19139.getDataQualityInfo().add(dqDataQualityPropertyType);
	}

	DQDataQualityPropertyType dqDataQualityPropertyType = iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1));
	if (dqDataQualityPropertyType.getDQDataQuality()==null){
	    DQDataQualityType dqDataQualityType = ofMDMetadata.createDQDataQualityType();
	    dqDataQualityPropertyType.setDQDataQuality(dqDataQualityType);
	    if (dqDataQualityPropertyType.getDQDataQuality().getLineage() == null){
		dqDataQualityPropertyType.getDQDataQuality().setLineage(ofMDMetadata.createLILineagePropertyType());




	    }
	    if (dqDataQualityPropertyType.getDQDataQuality().getLineage().getLILineage() == null){
		dqDataQualityPropertyType.getDQDataQuality().getLineage().setLILineage(ofMDMetadata.createLILineageType());	    
	    }




	}		
    }

    public void  getAttribut(ch.depth.migration.asit.metadata.Class c){

	List attributes = c.getAttribute();
	String description="";
	String nom="";
	String type="";
	MDObligationCodePropertyType statut = ofMDMetadata.createMDObligationCodePropertyType();
	String format="";

	for (int n=0;n<attributes.size();n++){
	    ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
	    String data = catt.getValue();
	    if (catt.getName() .equals("Nom")){
		nom = data; 
	    }else if (catt.getName() .equals("Description")){
		description = data;
	    }else if (catt.getName() .equals("Type")){
		//Alphanumérique
		type=data;
	    }else if (catt.getName() .equals("Format")){
		//Texte de 8 caractères
		format=data;
	    }else if (catt.getName() .equals("Statut")){
		//Obligatoire
		//statut=data;
		if (data !=null && data.length()>0){
		    if (data.equals("Obligatoire")){			
			statut.setMDObligationCode(MDObligationCodeType.fromValue("mandatory"));

		    }else if (data.equals("Facultatif")){			
			statut.setMDObligationCode(MDObligationCodeType.fromValue("optional"));

		    }else {
			statut.setMDObligationCode(MDObligationCodeType.fromValue("optional"));
			System.err.println("ATTENTION : STATUT NON GERER "+data);
		    }
		}


	    }else {

		System.out.println("ATTENTION : Attribut non pris en charge : "+catt.getName());
	    }
	}


	List<MDMetadataExtensionInformationPropertyType> info = iso19139.getMetadataExtensionInfo();

	MDMetadataExtensionInformationPropertyType o = ofMDMetadata.createMDMetadataExtensionInformationPropertyType();
	info.add(o);
	if (o.getMDMetadataExtensionInformation()==null){

	    MDMetadataExtensionInformationType a = ofMDMetadata.createMDMetadataExtensionInformationType();

	    o.setMDMetadataExtensionInformation(a );
	}
	List<MDExtendedElementInformationPropertyType> elem = o.getMDMetadataExtensionInformation().getExtendedElementInformation();

	MDExtendedElementInformationPropertyType ex = ofMDMetadata.createMDExtendedElementInformationPropertyType();
	elem.add(ex);

	MDExtendedElementInformationType mdExtendedElementInformationType = ofMDMetadata.createMDExtendedElementInformationType();
	mdExtendedElementInformationType.setName(toCharacterStringPropertyType(nom));
	mdExtendedElementInformationType.setDefinition(toCharacterStringPropertyType(description));
	mdExtendedElementInformationType.setRule(toCharacterStringPropertyType(format));	
	mdExtendedElementInformationType.setObligation(statut);	

	ex.setMDExtendedElementInformation(mdExtendedElementInformationType);

    }

    public void  getRepresentation(ch.depth.migration.asit.metadata.Class c){

	List attributes = c.getAttribute();


	if (c.getName().equals("Représentation")){
	    for (int n=0;n<attributes.size();n++){
		ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
		String data = catt.getValue();
		if (data!=null && data.length()>0){
		    if (catt.getName() .equals("Système de coordonnées")){

			if (data != null) {

			    String systemecoord  = data;
			    if (data.equals("Coordonnées nationales suisses")) {

				systemecoord = "EPSG:21781";
			    } 		    		
			    MDReferenceSystemPropertyType mdReferenceSystemPropertyType = ofMDMetadata.createMDReferenceSystemPropertyType();
			    MDReferenceSystemType mdReferenceSystemType = ofMDMetadata.createMDReferenceSystemType();
			    RSIdentifierPropertyType rsIdentifierPropertyType = ofMDMetadata.createRSIdentifierPropertyType();
			    RSIdentifierType rsIdentifierType = ofMDMetadata.createRSIdentifierType();
			    rsIdentifierType.setCode(toLocalisedCharacterStringPropertyType(systemecoord));
			    rsIdentifierPropertyType.setRSIdentifier(rsIdentifierType);
			    mdReferenceSystemType.setReferenceSystemIdentifier(rsIdentifierPropertyType);
			    mdReferenceSystemPropertyType.setMDReferenceSystem(mdReferenceSystemType);

			    iso19139.getReferenceSystemInfo().add(mdReferenceSystemPropertyType);	
			}
		    }else if (catt.getName() .equals("Echelle de référence")){
			if (data !=null && data.length()>0){
			    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();


			    MDResolutionPropertyType mdResolutionPropertyType = ofMDMetadata.createMDResolutionPropertyType();
			    MDResolutionType mdResolutionType = ofMDMetadata.createMDResolutionType();
			    
			    MDRepresentativeFractionPropertyType mdRepresentativeFractionPropertyType = ofMDMetadata.createMDRepresentativeFractionPropertyType();

			    
			    MDRepresentativeFractionType mdRepresentativeFractionType = ofMDMetadata.createMDRepresentativeFractionType();

			    if (data!=null && data.length()>0){

				//Format de l'échelle 1:x ou 1/x
				String sep=":";
				if(data.indexOf("/")>0){
				    sep="/";
				}
				if (data.indexOf(sep)>0){
				    String numerator = data.substring(0, data.indexOf(sep));
				    String sDenominator = data.substring(data.indexOf(sep));
				    sDenominator = sDenominator.replace('\'', ' ');
				    sDenominator = sDenominator.replace('\'', ' ');
				    sDenominator = sDenominator.replace('/', ' ');
				    sDenominator = sDenominator.replace("´", "");
				    sDenominator = sDenominator.replace("´", "");			    
				    sDenominator = sDenominator.replace(":", "");
				    sDenominator = sDenominator.replace(".", "");
				    sDenominator = sDenominator.replace(" ", "");


				    try{
					if(sDenominator.length()>0){
					    Integer.decode(sDenominator.trim());

					    IntegerPropertyType denominator = toIntegerPropertyType(sDenominator);
					    mdRepresentativeFractionType.setDenominator(denominator);


					    mdRepresentativeFractionPropertyType.setMDRepresentativeFraction(mdRepresentativeFractionType );
					    mdResolutionType.setEquivalentScale(mdRepresentativeFractionPropertyType );
					    mdResolutionPropertyType.setMDResolution(mdResolutionType );
					    ((MDDataIdentificationType)mdDataIdentificationType.getValue()).getSpatialResolution().add(mdResolutionPropertyType );
					}else{

					    System.out.println("ATTENTION 1 : Echelle de référence pas définie correctement : '"+data+"' '"+sDenominator+"'");
					}
				    }catch(Exception e){

					System.out.println("ATTENTION 2: Echelle de référence pas définie correctement : '"+data+"'"+sDenominator);
					addExtendedMetadataProperty(c.getName(),catt.getName(),data);
				    }
				}else{
				    System.out.println("ATTENTION 4: Echelle de référence pas définie correctement : '"+data+"'");
				    addExtendedMetadataProperty(c.getName(),catt.getName(),data);				
				}
			    }else{
				if (data!=null && data.length()>0){
				    System.out.println("ATTENTION 3: Echelle de référence pas définie correctement : '"+data+"'");			    
				    addExtendedMetadataProperty(c.getName(),catt.getName(),data);
				}

			    }
			}
		    }else if (catt.getName() .equals("Précision")){

			addExtendedMetadataProperty(c.getName(),catt.getName(),data);	
		    }else {
			System.out.println("ATTENTION "+ c.getName()+" " +catt.getName()+ " " +data+ " Non géré");
			addExtendedMetadataProperty(c.getName(),catt.getName(),data);
		    }


		}
	    }
	}else if (c.getName().equals("Produit vecteur")){

	    for (int n=0;n<attributes.size();n++){
		ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
		String data = catt.getValue();
		if (data!=null && data.length()>0){
		    addExtendedMetadataProperty(c.getName(),catt.getName(),data);
		}
	    }

	}else if (c.getName().equals("Produit raster")){

	    for (int n=0;n<attributes.size();n++){
		ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
		String data = catt.getValue();
		if (data!=null && data.length()>0){
		    addExtendedMetadataProperty(c.getName(),catt.getName(),data);
		}
	    }
	} else {

	    System.out.println("ATTENTION : Classe "+ c.getName()+"  non gérée");
	    for (int n=0;n<attributes.size();n++){
		ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
		String data = catt.getValue();
		if (data!=null && data.length()>0){
		    addExtendedMetadataProperty(c.getName(),catt.getName(),data);
		}
	    }
	}


	List<ch.depth.migration.asit.metadata.Class> c2 = c.getClazz();
	for (int i = 0; i < c2.size();i++){

	    checkGestionAcquisition();
	    getRepresentation(c2.get(i));

	}


    }

    public void getGestion(ch.depth.migration.asit.metadata.Class c){
	List attributes = c.getAttribute();
	DQDataQualityPropertyType dqDataQualityPropertyType = iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1));

	for (int n=0;n<attributes.size();n++){
	    ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
	    String data = catt.getValue();
	    if (data!=null && data.length()>0){
		if (catt.getName() .equals("Mode d'acquisition")){
		    iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1)).getDQDataQuality().getLineage().getLILineage().setStatement(toLocalisedCharacterStringPropertyType(data));		
		}else if (catt.getName() .equals("Description du mode d'acquisition")){

		    if(iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1)).getDQDataQuality().getLineage().getLILineage().getProcessStep().size()==0){
			LIProcessStepType liProcessStepType = ofMDMetadata.createLIProcessStepType();
			LIProcessStepPropertyType liProcessStepPropertyType = ofMDMetadata.createLIProcessStepPropertyType();
			liProcessStepPropertyType.setLIProcessStep(liProcessStepType);
			iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1)).getDQDataQuality().getLineage().getLILineage().getProcessStep().add(liProcessStepPropertyType);
		    }

		    LIProcessStepPropertyType liProcessStepPropertyType = iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1)).getDQDataQuality().getLineage().getLILineage().getProcessStep().get(iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1)).getDQDataQuality().getLineage().getLILineage().getProcessStep().size()-1);
		    liProcessStepPropertyType .getLIProcessStep().setDescription(toLocalisedCharacterStringPropertyType(data));

		}else if (catt.getName() .equals("Données sources")){

		    if(iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1)).getDQDataQuality().getLineage().getLILineage().getProcessStep().size()==0){
			LIProcessStepType liProcessStepType = ofMDMetadata.createLIProcessStepType();
			LIProcessStepPropertyType liProcessStepPropertyType = ofMDMetadata.createLIProcessStepPropertyType();
			liProcessStepPropertyType.setLIProcessStep(liProcessStepType);
			iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1)).getDQDataQuality().getLineage().getLILineage().getProcessStep().add(liProcessStepPropertyType);
		    }


		    LIProcessStepPropertyType liProcessStepPropertyType = iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1)).getDQDataQuality().getLineage().getLILineage().getProcessStep().get(iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1)).getDQDataQuality().getLineage().getLILineage().getProcessStep().size()-1);
		    LIProcessStepType liProcessStepType = liProcessStepPropertyType.getLIProcessStep();
		    LISourcePropertyType liSourcePropertyType = ofMDMetadata.createLISourcePropertyType();
		    LISourceType liSourceType = ofMDMetadata.createLISourceType();
		    liSourcePropertyType.setLISource(liSourceType);
		    liSourceType.setDescription(toLocalisedCharacterStringPropertyType(data));



		    liProcessStepType.getSource().add(liSourcePropertyType);

		    liProcessStepPropertyType.setLIProcessStep(liProcessStepType);



		}else if (catt.getName() .equals("Type de mise à jour")){



		    //iso19139.getDataQualityInfo().get((iso19139.getDataQualityInfo().size()-1)).getDQDataQuality().getLineage().getLILineage().getProcessStep().get(0).getLIProcessStep();


		    MDMaintenanceFrequencyCodePropertyType mdMaintenanceFrequencyCodePropertyType = ofMDMetadata .createMDMaintenanceFrequencyCodePropertyType();
		    String TypeDeMiseAJour = data;
		    if (TypeDeMiseAJour.equalsIgnoreCase("Mise à jour en continu")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("continual"));
		    }else if (TypeDeMiseAJour.equalsIgnoreCase("Mise à jour occasionnelle")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("unknown"));
		    } else if (TypeDeMiseAJour.equalsIgnoreCase("Mise à jour par objet")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("unknown"));
		    }else  if (TypeDeMiseAJour.equalsIgnoreCase("Mise à jour périodique")) {

		    }else
			if (TypeDeMiseAJour.equalsIgnoreCase("Pas de mise à jour")) {
			    mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("notPlanned"));
			}



		    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();

		    if ((mdDataIdentificationType.getValue()).getResourceMaintenance().size() == 0){

			MDMaintenanceInformationPropertyType mdMaintenanceInformationPropertyType = ofMDMetadata.createMDMaintenanceInformationPropertyType();
			(mdDataIdentificationType.getValue()).getResourceMaintenance().add(mdMaintenanceInformationPropertyType);    				
		    }

		    MDMaintenanceInformationPropertyType mdMaintenanceInformationPropertyType = (mdDataIdentificationType.getValue()).getResourceMaintenance().get((mdDataIdentificationType.getValue()).getResourceMaintenance().size()-1);
		    if (mdMaintenanceInformationPropertyType.getMDMaintenanceInformation()==null){
			MDMaintenanceInformationType mdMaintenanceInformationType = ofMDMetadata.createMDMaintenanceInformationType();
			mdMaintenanceInformationPropertyType.setMDMaintenanceInformation(mdMaintenanceInformationType);    
		    }
		    MDMaintenanceInformationType mdMaintenanceInformationType =mdMaintenanceInformationPropertyType.getMDMaintenanceInformation();




		    mdMaintenanceInformationPropertyType.setMDMaintenanceInformation(mdMaintenanceInformationType);

		    mdMaintenanceInformationType.setMaintenanceAndUpdateFrequency(mdMaintenanceFrequencyCodePropertyType);

		}else if (catt.getName() .equals("Fréquence")){
		    String frequence = data.trim();
		    MDMaintenanceFrequencyCodePropertyType mdMaintenanceFrequencyCodePropertyType = ofMDMetadata .createMDMaintenanceFrequencyCodePropertyType();


		    if (frequence.equalsIgnoreCase("Hebdomadaire")) {
			mdMaintenanceFrequencyCodePropertyType .setMDMaintenanceFrequencyCode(toCodeListValueType("weekly"));
		    } else if (frequence.equalsIgnoreCase("6 mois") || frequence.equalsIgnoreCase("Semestrielle")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("biannually"));
		    } else if (frequence.equalsIgnoreCase("annuelle (théorique)") || frequence.equalsIgnoreCase("Une fois par an") || frequence.equalsIgnoreCase("annuelle") || frequence.equalsIgnoreCase("Annuelle") || frequence.equalsIgnoreCase("Anuelle")|| frequence.equalsIgnoreCase("1 fois par année")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("annually"));
		    }else  if (frequence.equalsIgnoreCase("Tous les 3 ans")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("3 years"));
		    }else if (frequence.equalsIgnoreCase("Selon mandat légal de la Confédération")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("asneeded"));
		    } else if (frequence.equalsIgnoreCase("tous les 6 ans")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("6 years"));
		    } else if (frequence.equalsIgnoreCase("5 ans")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("5 years"));
		    } else if (frequence.equalsIgnoreCase("10 ans")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("10 years"));
		    } else if (frequence.equalsIgnoreCase("Non définie")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("unkwnon"));
		    } else if (frequence.equalsIgnoreCase("3 mois") || frequence.equalsIgnoreCase("Trimestrielle")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("3 monthes"));
		    }else if (frequence.equalsIgnoreCase("temps réel")) {
			mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType("continual"));
		    }		
		    else {			    
			if (data!=null && data.length()>0) {
			    System.out.println("ATTENTION FREQUENCE NON NORMALISEE PAR L'ISO: '" + frequence+"'");
			    mdMaintenanceFrequencyCodePropertyType.setMDMaintenanceFrequencyCode(toCodeListValueType(data));

			}
		    }


		    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();

		    if ((mdDataIdentificationType.getValue()).getResourceMaintenance().size() == 0){

			MDMaintenanceInformationPropertyType mdMaintenanceInformationPropertyType = ofMDMetadata.createMDMaintenanceInformationPropertyType();
			(mdDataIdentificationType.getValue()).getResourceMaintenance().add(mdMaintenanceInformationPropertyType);    				
		    }

		    MDMaintenanceInformationPropertyType mdMaintenanceInformationPropertyType = (mdDataIdentificationType.getValue()).getResourceMaintenance().get((mdDataIdentificationType.getValue()).getResourceMaintenance().size()-1);
		    if (mdMaintenanceInformationPropertyType.getMDMaintenanceInformation()==null){
			MDMaintenanceInformationType mdMaintenanceInformationType = ofMDMetadata.createMDMaintenanceInformationType();
			mdMaintenanceInformationPropertyType.setMDMaintenanceInformation(mdMaintenanceInformationType);    
		    }
		    MDMaintenanceInformationType mdMaintenanceInformationType =mdMaintenanceInformationPropertyType.getMDMaintenanceInformation();




		    mdMaintenanceInformationPropertyType.setMDMaintenanceInformation(mdMaintenanceInformationType);

		    mdMaintenanceInformationType.setMaintenanceAndUpdateFrequency(mdMaintenanceFrequencyCodePropertyType);



		} else if (catt.getName() .equals("Remarques")){
		    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();						
		    if ((mdDataIdentificationType.getValue()).getResourceMaintenance().size() ==0){
			MDMaintenanceInformationPropertyType mdMaintenanceInformationPropertyType = ofMDMetadata.createMDMaintenanceInformationPropertyType();
			(mdDataIdentificationType.getValue()).getResourceMaintenance().add(mdMaintenanceInformationPropertyType);
		    }




		    MDMaintenanceInformationPropertyType mdMaintenanceInformationPropertyType = (mdDataIdentificationType.getValue()).getResourceMaintenance().get((mdDataIdentificationType.getValue()).getResourceMaintenance().size()-1);
		    if (mdMaintenanceInformationPropertyType.getMDMaintenanceInformation()==null){
			MDMaintenanceInformationType mdMaintenanceInformationType = ofMDMetadata.createMDMaintenanceInformationType();
			mdMaintenanceInformationPropertyType.setMDMaintenanceInformation(mdMaintenanceInformationType);    
		    }
		    MDMaintenanceInformationType mdMaintenanceInformationType =mdMaintenanceInformationPropertyType.getMDMaintenanceInformation(); 
		    mdMaintenanceInformationType.getMaintenanceNote().add(toLocalisedCharacterStringPropertyType(data));






		}  else{
		    System.out.println("ATTENTION L'attrbut : "+catt.getName()+" n'est pas géré : "+data );
		    addExtendedMetadataProperty(c.getName(),catt.getName(),data);

		}	

	    }
	}

	List<ch.depth.migration.asit.metadata.Class> c2 = c.getClazz();
	for (int i = 0; i < c2.size();i++){
	    checkGestionAcquisition();
	    getGestion(c2.get(i));

	}
    }



    public void getDiffusion(ch.depth.migration.asit.metadata.Class c){


	List attributes = c.getAttribute();

	for (int n=0;n<attributes.size();n++){
	    ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
	    String data = catt.getValue();

	    if (data!=null && data.length()>0){

		//Nom de la métadonnée
		if (catt.getName() .equals("Conditions de diffusion")){

		    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();		


		    MDLegalConstraintsType mdLegalConstraintsType = ofMDMetadata.createMDLegalConstraintsType();
		    mdLegalConstraintsType.getOtherConstraints().add(toLocalisedCharacterStringPropertyType(data));

		    MDConstraintsPropertyType mdConstraintsPropertyType = ofMDMetadata.createMDConstraintsPropertyType();
		    mdConstraintsPropertyType.setTitle("Conditions de diffusion");
		    JAXBElement<MDLegalConstraintsType> mdConstraints = ofMDMetadata.createMDLegalConstraints(mdLegalConstraintsType);
		    mdConstraintsPropertyType.setMDConstraints(mdConstraints);
		    (mdDataIdentificationType.getValue()).getResourceConstraints().add(mdConstraintsPropertyType);				
		}	else if (catt.getName() .equals("Restriction d'utilisation")){

		    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();

		    MDLegalConstraintsType mdLegalConstraintsType = ofMDMetadata.createMDLegalConstraintsType();
		    mdLegalConstraintsType.getUseLimitation().add(toLocalisedCharacterStringPropertyType(data));
		    MDConstraintsPropertyType mdConstraintsPropertyType = ofMDMetadata.createMDConstraintsPropertyType();

		    JAXBElement<MDLegalConstraintsType> mdConstraints = ofMDMetadata.createMDLegalConstraints(mdLegalConstraintsType);
		    mdConstraintsPropertyType.setMDConstraints(mdConstraints);

		    (mdDataIdentificationType.getValue()).getResourceConstraints().add(mdConstraintsPropertyType);		    		
		} else if (catt.getName() .equals("Principes et mode de tarification")){

		    MDStandardOrderProcessPropertyType mdStandardOrderProcessPropertyType = ofMDMetadata.createMDStandardOrderProcessPropertyType();
		    MDStandardOrderProcessType mdStandardOrderProcessType = ofMDMetadata.createMDStandardOrderProcessType();
		    mdStandardOrderProcessType.setFees(toLocalisedCharacterStringPropertyType(data));
		    mdStandardOrderProcessPropertyType.setMDStandardOrderProcess(mdStandardOrderProcessType);

		    MDDistributorType mdDistributorType = ofMDMetadata.createMDDistributorType();
		    mdDistributorType.getDistributionOrderProcess().add(mdStandardOrderProcessPropertyType);

		    MDDistributorPropertyType mdDistributorPropertyType = ofMDMetadata.createMDDistributorPropertyType();
		    mdDistributorPropertyType.setMDDistributor(mdDistributorType);

		    iso19139.getDistributionInfo().getMDDistribution().getDistributor().add(mdDistributorPropertyType);
		}else  if (catt.getName() .equals("Statut")){		
		    addExtendedMetadataProperty(c.getName(),catt.getName(),data);

		}else  if (catt.getName() .equals("Référence du document légal")){
		    MDLegalConstraintsType mdLegalConstraintsType = ofMDMetadata.createMDLegalConstraintsType();
		    mdLegalConstraintsType.getOtherConstraints().add(toLocalisedCharacterStringPropertyType(data));
		    MDConstraintsPropertyType mdConstraintsPropertyType = ofMDMetadata.createMDConstraintsPropertyType();
		    mdConstraintsPropertyType.setTitle(catt.getName());		
		    JAXBElement<MDLegalConstraintsType> mdConstraints = ofMDMetadata.createMDLegalConstraints(mdLegalConstraintsType);
		    mdConstraintsPropertyType.setMDConstraints(mdConstraints);
		    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();		
		    (mdDataIdentificationType.getValue()).getResourceConstraints().add(mdConstraintsPropertyType);		
		} else{
		    System.out.println("ATTENTION L'attrbut : "+catt.getName()+" n'est pas géré " +data);
		    addExtendedMetadataProperty(c.getName(),catt.getName(),data);
		}
	    }
	}



	List<ch.depth.migration.asit.metadata.Class> c2 = c.getClazz();
	for (int i = 0; i < c2.size();i++){

	    getDiffusion(c2.get(i));

	}

    }

    public void getContact(ch.depth.migration.asit.metadata.Class c){

	String nom ="";
	String organisme="";
	String role="";
	String adresse="";
	String localite="";
	String codePostal="";
	String pays="";
	String tel="";
	String fax="";
	String email="";	

	List attributes = c.getAttribute();	
	for (int n=0;n<attributes.size();n++){
	    ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
	    String data = catt.getValue();
	    if (data!=null && data.length()>0){
		if (catt.getName() .equals("Rôle")){
		    role=data;
		}else
		    if (catt.getName() .equals("Nom")){
			nom = data;

		    }else if (catt.getName() .equals("Organisme")){
			organisme=data;
		    }else if (catt.getName() .equals("Adresse")){
			adresse = data;
		    }else if (catt.getName() .equals("Localité")){
			localite=data;
		    }else if (catt.getName() .equals("Code postal")){
			codePostal=data;
		    }else if (catt.getName() .equals("Pays")){
			pays=data;
		    }else if (catt.getName() .equals("Téléphone")){
			tel=data;
		    }else if (catt.getName() .equals("Fax")){
			fax=data;
		    }else if (catt.getName() .equals("Email")){
			email=data;
		    }else{

			System.out.println("ATTENTION : "+ catt.getName()+ " PAS GERE "+ data);
		    }
	    }

	}




	CIContactType ciContactType = ofMDMetadata.createCIContactType();
	CIContactPropertyType ciContactPropertyType = ofMDMetadata.createCIContactPropertyType();
	CIAddressPropertyType ciAddressPropertyType = ofMDMetadata.createCIAddressPropertyType();

	CIAddressType ciAddressType = ofMDMetadata.createCIAddressType();
	ciAddressType.getDeliveryPoint().add(toCharacterStringPropertyType(adresse));
	ciAddressType.setCountry(toCharacterStringPropertyType(pays));
	ciAddressType.setPostalCode(toCharacterStringPropertyType(codePostal));
	ciAddressType.setCity(toCharacterStringPropertyType(localite));
	ciAddressType.getElectronicMailAddress().add(toCharacterStringPropertyType(email));
	ciAddressPropertyType.setCIAddress(ciAddressType);
	ciContactType.setAddress(ciAddressPropertyType);

	CITelephonePropertyType ciTelephonePropertyType = ofMDMetadata.createCITelephonePropertyType();
	CITelephoneType ciTelephoneType = ofMDMetadata.createCITelephoneType();
	ciTelephoneType.getFacsimile().add(toCharacterStringPropertyType(tel));
	ciTelephoneType.getVoice().add(toCharacterStringPropertyType(fax));
	ciTelephonePropertyType.setCITelephone(ciTelephoneType);
	ciContactType.setPhone(ciTelephonePropertyType);

	ciContactPropertyType.setCIContact(ciContactType);

	CIResponsiblePartyPropertyType ciResponsiblePartyPropertyType = ofMDMetadata.createCIResponsiblePartyPropertyType();
	CIResponsiblePartyType ciResponsiblePartyType = ofMDMetadata.createCIResponsiblePartyType();
	ciResponsiblePartyType.setContactInfo(ciContactPropertyType);
	ciResponsiblePartyType.setIndividualName(toCharacterStringPropertyType(nom));
	ciResponsiblePartyType.setOrganisationName(toLocalisedCharacterStringPropertyType(organisme));

	CodeListValueType codeListValueType = ofGco.createCodeListValueType();
	CIRoleCodePropertyType ciRoleCodePropertyType = ofMDMetadata.createCIRoleCodePropertyType();
	ciRoleCodePropertyType.setCIRoleCode(codeListValueType);
	ciResponsiblePartyType.setRole(ciRoleCodePropertyType);
	ciResponsiblePartyPropertyType.setCIResponsibleParty(ciResponsiblePartyType);

	if (role.equalsIgnoreCase("Gestionnaire")) {
	    codeListValueType.setCodeList("custodian");	        
	    iso19139.getContact().add(ciResponsiblePartyPropertyType);
	    
	} else if (role.equalsIgnoreCase("Responsable de diffusion") || role.equalsIgnoreCase("Responsablede diffusion")) {
	    codeListValueType.setCodeList("distributor");
	    
	  //gmd:distributionInfo/gmd:MD_Distribution/gmd:distributor/gmd:MD_Distributor/gmd:distributorContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:LocalisedCharacterString");
	    
	    if (iso19139.getDistributionInfo() == null){		
		MDDistributionPropertyType mdDistributionPropertyType = ofMDMetadata.createMDDistributionPropertyType();
		iso19139.setDistributionInfo(mdDistributionPropertyType );
		
	    }
	    if (iso19139.getDistributionInfo().getMDDistribution() == null){
		
		MDDistributionType mdDistributionType = ofMDMetadata.createMDDistributionType();
		iso19139.getDistributionInfo().setMDDistribution(mdDistributionType );
	    }
	    
	    
	    MDDistributorPropertyType mdDistributorPropertyType = ofMDMetadata.createMDDistributorPropertyType();

	    if (mdDistributorPropertyType.getMDDistributor() == null) {		
		MDDistributorType mdDistributorType = ofMDMetadata.createMDDistributorType();
		mdDistributorPropertyType.setMDDistributor(mdDistributorType);
	    }
	    mdDistributorPropertyType.getMDDistributor().setDistributorContact(ciResponsiblePartyPropertyType);
	    iso19139.getDistributionInfo().getMDDistribution().getDistributor().add(mdDistributorPropertyType );
	    
	    
	    
	    
	    
	    //iso19139.getDistributionInfo().getMDDistribution().getDistributor().get(iso19139.getDistributionInfo().getMDDistribution().getDistributor().size()-1).getMDDistributor().setDistributorContact(ciResponsiblePartyPropertyType);
	    
	    //iso19139.getContact().add(ciResponsiblePartyPropertyType);	    
	} else if (role.equalsIgnoreCase("Point de contact")) {
	    codeListValueType.setCodeList("pointOfContact");
	    
	  //gmd:identificationInfo/gmd:MD_DataIdentification/gmd:pointOfContact/gmd:CI_ResponsibleParty/gmd:organisationName/gmd:LocalisedCharacterString
	    
	    iso19139.getIdentificationInfo().get(iso19139.getIdentificationInfo().size()-1).getAbstractMDIdentification().getValue().getPointOfContact().add(ciResponsiblePartyPropertyType);
	    //getContact().add(ciResponsiblePartyPropertyType);	    

	} else {
	    System.out.println("role personne de contact inconnu : '"+ role+"' role par défaut utilisé : pointOfContact");
	    codeListValueType.setCodeList("pointOfContact");
	    iso19139.getContact().add(ciResponsiblePartyPropertyType);
	}



    }


    public void getIdentification(ch.depth.migration.asit.metadata.Class c){




	if (c.getClazz().size() > 0) {

	    System.out.println("ATTENTION Classe : "+c.getClazz().get(0).getName()+" dans Identification non prise en charge");	    	    	    
	}


	List attributes = c.getAttribute();


	for (int n=0;n<attributes.size();n++){
	    ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
	    String data = catt.getValue();


	    if (data!=null && data.length()>0){
		//Nom de la métadonnée
		if (catt.getName() .equals("Nom")){

		    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();


		    CICitationPropertyType ciCitationPropertyType = (mdDataIdentificationType.getValue()).getCitation();
		    if (ciCitationPropertyType == null) {
			ciCitationPropertyType = ofMDMetadata.createCICitationPropertyType();		    
		    }
		    CICitationType ciCitationType = ciCitationPropertyType.getCICitation();
		    if (ciCitationType == null) {
			ciCitationType = ofMDMetadata.createCICitationType();
		    }
		    ciCitationType.setTitle(toLocalisedCharacterStringPropertyType(data));
		    ciCitationPropertyType.setCICitation(ciCitationType);		
		    (mdDataIdentificationType.getValue()).setCitation(ciCitationPropertyType);		
		} else if (catt.getName() .equals("Sous-produits")){
		    if (data !=null && data.length()>0){		
			addExtendedMetadataProperty(c.getName(),catt.getName(),data);
		    }

		}else


		    if (catt.getName() .equals("Description")){
			JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();		
			(mdDataIdentificationType.getValue()).setAbstract(toLocalisedCharacterStringPropertyType(data));		
		    } else

			if (catt.getName() .equals("Dernière mise à jour")){
			    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();		

			    CICitationPropertyType ciCitationPropertyType = (mdDataIdentificationType.getValue()).getCitation();
			    if (ciCitationPropertyType == null) {
				ciCitationPropertyType = ofMDMetadata.createCICitationPropertyType();
			    }

			    CICitationType ciCitationType = ciCitationPropertyType.getCICitation();
			    if (ciCitationType == null) {
				ciCitationType = ofMDMetadata.createCICitationType();
			    }

			    DatePropertyType dpt = ofGco.createDatePropertyType();
			    dpt.setDate(data);
			    CIDateType ciDateType = ofMDMetadata.createCIDateType();
			    CIDateTypeCodePropertyType ciDateTypeCodePropertyType = ofMDMetadata.createCIDateTypeCodePropertyType();
			    ciDateType.setDate(dpt);

			    ciDateTypeCodePropertyType.setCIDateTypeCode(toCodeListValueType("revision"));
			    ciDateType.setDateType(ciDateTypeCodePropertyType);

			    CIDatePropertyType ciDatePropertyType = ofMDMetadata.createCIDatePropertyType();
			    ciDatePropertyType.setCIDate(ciDateType);
			    ciCitationType.getDate().add(ciDatePropertyType);

			    ciCitationPropertyType.setCICitation(ciCitationType);

			    (mdDataIdentificationType.getValue()).setCitation(ciCitationPropertyType);
			} else   if (catt.getName() .equals("Couverture spatiale")){

			    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();
			    if (((MDDataIdentificationType)mdDataIdentificationType.getValue()).getExtent().size()==0){
				EXExtentType exExtentType = ofMDMetadata.createEXExtentType();
				EXExtentPropertyType exExtentPropertyType = ofMDMetadata.createEXExtentPropertyType();
				exExtentPropertyType.setEXExtent(exExtentType);
				((MDDataIdentificationType)mdDataIdentificationType.getValue()).getExtent().add(exExtentPropertyType);
			    }

			    EXExtentPropertyType exExtentPropertyType =  ((MDDataIdentificationType)mdDataIdentificationType.getValue()).getExtent().get(((MDDataIdentificationType)mdDataIdentificationType.getValue()).getExtent().size()-1);
			    EXExtentType exExtentType = exExtentPropertyType .getEXExtent();
			    exExtentPropertyType.setTitle(data);

			}else if (catt.getName() .equals("Etendue géographique")){


			    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();
			    if (((MDDataIdentificationType)mdDataIdentificationType.getValue()).getExtent().size()==0){
				EXExtentType exExtentType = ofMDMetadata.createEXExtentType();
				EXExtentPropertyType exExtentPropertyType = ofMDMetadata.createEXExtentPropertyType();
				exExtentPropertyType.setEXExtent(exExtentType);
				((MDDataIdentificationType)mdDataIdentificationType.getValue()).getExtent().add(exExtentPropertyType);
			    }

			    EXExtentPropertyType exExtentPropertyType =  ((MDDataIdentificationType)mdDataIdentificationType.getValue()).getExtent().get(((MDDataIdentificationType)mdDataIdentificationType.getValue()).getExtent().size()-1);
			    EXExtentType exExtentType = exExtentPropertyType .getEXExtent();


			    exExtentType.setDescription(toLocalisedCharacterStringPropertyType(data));
			    EXGeographicExtentPropertyType exGeographicExtentPropertyType = ofMDMetadata.createEXGeographicExtentPropertyType();
			    EXGeographicBoundingBoxType exGeographicBoundingBoxType = ofMDMetadata.createEXGeographicBoundingBoxType();

			    exGeographicBoundingBoxType.setNorthBoundLatitude(toDecimalPropertyType(46.980));
			    exGeographicBoundingBoxType.setSouthBoundLatitude(toDecimalPropertyType(46.180));
			    exGeographicBoundingBoxType.setEastBoundLongitude(toDecimalPropertyType(7.250));
			    exGeographicBoundingBoxType.setWestBoundLongitude(toDecimalPropertyType(6.070));

			    exGeographicExtentPropertyType.setAbstractEXGeographicExtent(ofMDMetadata.createEXGeographicBoundingBox(exGeographicBoundingBoxType));
			    exExtentType.getGeographicElement().add(exGeographicExtentPropertyType);

			}else   if (catt.getName() .equals("Synoptique")){
			    MDBrowseGraphicType mdBrowseGraphicType = ofMDMetadata.createMDBrowseGraphicType();
			    mdBrowseGraphicType.setFileDescription(toLocalisedCharacterStringPropertyType(data));
			    mdBrowseGraphicType.setFileType(toCharacterStringPropertyType(data));
			    mdBrowseGraphicType.setFileName(toCharacterStringPropertyType(data));
			    MDBrowseGraphicPropertyType mdBrowseGraphicPropertyType = ofMDMetadata.createMDBrowseGraphicPropertyType();
			    mdBrowseGraphicPropertyType.setMDBrowseGraphic(mdBrowseGraphicType);

			    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();
			    ((MDDataIdentificationType)mdDataIdentificationType.getValue()).getGraphicOverview().add(mdBrowseGraphicPropertyType);
			}else if (catt.getName() .equals("Extrait")){
			    MDBrowseGraphicType mdBrowseGraphicType = ofMDMetadata.createMDBrowseGraphicType();
			    mdBrowseGraphicType.setFileDescription(toLocalisedCharacterStringPropertyType(data));
			    mdBrowseGraphicType.setFileType(toCharacterStringPropertyType(data));
			    mdBrowseGraphicType.setFileName(toCharacterStringPropertyType(data));
			    MDBrowseGraphicPropertyType mdBrowseGraphicPropertyType = ofMDMetadata.createMDBrowseGraphicPropertyType();
			    mdBrowseGraphicPropertyType.setMDBrowseGraphic(mdBrowseGraphicType);
			    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();			 
			    ((MDDataIdentificationType)mdDataIdentificationType.getValue()).getGraphicOverview().add(mdBrowseGraphicPropertyType);

			}else if (catt.getName() .equals("Thématique")){
			    MDTopicCategoryCodePropertyType mdTopicCategoryCodePropertyType = ofMDMetadata.createMDTopicCategoryCodePropertyType();
			    if (data.equalsIgnoreCase("Cadastre, aménagement")) {
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.PLANNING_CADASTRE);			    
			    } else if (data.equalsIgnoreCase("Activité militaire")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.INTELLIGENCE_MILITARY);
			    } else if (data.equalsIgnoreCase("Agriculture")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.FARMING);
			    } else if (data.equalsIgnoreCase("Biologie")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.BIOTA);
			    } else if (data.equalsIgnoreCase("Cartes de base, imagerie")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.IMAGERY_BASE_MAPS_EARTH_COVER);
			    } else if (data.equalsIgnoreCase("Climatologie/Météorologie")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.CLIMATOLOGY_METEOROLOGY_ATMOSPHERE);
			    } else if (data.equalsIgnoreCase("Construction, équipements, monuments")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.STRUCTURE);
			    } else if (data.equalsIgnoreCase("Eaux douces")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.INLAND_WATERS);
			    } else if (data.equalsIgnoreCase("Economie")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.ECONOMY);
			    } else if (data.equalsIgnoreCase("Elévation, altimétrie")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.ELEVATION);
			    } else if (data.equalsIgnoreCase("Environnement")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.ENVIRONMENT);
			    } else if (data.equalsIgnoreCase("Frontières")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.BOUNDARIES);
			    } else if (data.equalsIgnoreCase("Information géoscientifique")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.GEOSCIENTIFIC_INFORMATION);
			    } else if (data.equalsIgnoreCase("Infrastructure ou service de réseau")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.UTILITIES_COMMUNICATION);
			    } else if (data.equalsIgnoreCase("Localisation")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.LOCATION);
			    } else if (data.equalsIgnoreCase("Océans")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.OCEANS);
			    } else if (data.equalsIgnoreCase("Santé")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.HEALTH);
			    } else if (data.equalsIgnoreCase("Société")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.SOCIETY);
			    } else if (data.equalsIgnoreCase("Transport")) {			    
				mdTopicCategoryCodePropertyType.setMDTopicCategoryCode(MDTopicCategoryCodeType.TRANSPORTATION);
			    } else
			    {
				if(data!=null && data.length()>0){
				    System.out.println("Le theme n'est pas connu : " + data);
				    addExtendedMetadataProperty(c.getName(),catt.getName(),data);
				}


			    }

			    JAXBElement<? extends AbstractMDIdentificationType> mdDataIdentificationType = iso19139.getIdentificationInfo().get(0).getAbstractMDIdentification();			 
			    ((MDDataIdentificationType)mdDataIdentificationType.getValue()).getTopicCategory().add(mdTopicCategoryCodePropertyType);

			}else {
			    System.out.println("ATTENTION L'attribut : "+catt.getName()+" n'est pas géré "+data );
			    addExtendedMetadataProperty(c.getName(),catt.getName(),data);
			}
	    }
	}

    }


    public JAXBElement<MDMetadataType> getIso(){

	checkIdentification();
	checkGestionAcquisition();
	checkDiffusion();


	List<Class> classesList = m.getClazz();
	for (int k=0;k<classesList.size();k++){
	    ch.depth.migration.asit.metadata.Class c = ((ch.depth.migration.asit.metadata.Class) classesList.get(k));
	    if (c.getName().equals("Identification")){		
		getIdentification(c);
	    }else if (c.getName().equals("Diffusion")){		

		getDiffusion(c);

	    }else

		if (c.getName().equals("Gestion")){		

		    getGestion(c);

		}else
		    if (c.getName().equals("Représentation")){		

			getRepresentation(c);

		    }else

			if (c.getName().equals("Attribut")){
			    getAttribut(c);
			    /* List attributes = c.getAttribute();
			    for (int n=0;n<attributes.size();n++){
				ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
				String data = catt.getValue();
				addExtendedMetadataProperty(c.getName(),catt.getName(),data);
			    }*/

			}else
			    if (c.getName().equals("Contact")){		
				getContact(c);

			    }else{		
				System.out.println("ATTENTION : Classe non traitée : "+c.getName());

				List attributes = c.getAttribute();
				for (int n=0;n<attributes.size();n++){
				    ch.depth.migration.asit.metadata.Class.Attribute catt = (ch.depth.migration.asit.metadata.Class.Attribute)attributes.get(n);
				    String data = catt.getValue();
				    if (data!=null && data.length()>0){
					addExtendedMetadataProperty(c.getName(),catt.getName(),data);
				    }
				}

			    }

	}
	;
	return ofMDMetadata.createMDMetadata(iso19139);
    }



    public void generateCreateSql(){




    }


    public static void main(String args[]) {

	final String DIR =  "C:\\download2\\";

	File dir = new File(DIR);
	String[] s = dir.list();
	for (int i = 0; i < s.length; i++) {
	    File f = new File(DIR + s[i]);
	    if (f.isFile()) {

		InputStream is = null;
		try{
		    is = new java.io.FileInputStream(new File( DIR + s[i]));
		}catch(Exception e){
		    e.printStackTrace();
		}

		System.out.println(DIR + s[i]);

		JAXBContext jc = null;
		Unmarshaller un =null;
		try{
		    jc = JAXBContext.newInstance(ch.depth.migration.asit.metadata.ASITVD.class);
		    un = jc.createUnmarshaller();
		}catch(Exception e){
		    e.printStackTrace();
		}

		ch.depth.migration.asit.metadata.ASITVD elem = null;
		try{
		    elem = (ch.depth.migration.asit.metadata.ASITVD) un.unmarshal(is);
		}catch(Exception e){
		    e.printStackTrace();
		}

		List l = elem.getClazzOrMetadata();


		for (int j=0;j<l.size();j++){
		    try{
			Asit2Iso asit2iso = new Asit2Iso(((ch.depth.migration.asit.metadata.ASITVD.Metadata)l.get(j)));
			JAXBElement<MDMetadataType> iso = asit2iso.getIso();

			JAXBContext jc2 = JAXBContext.newInstance(org.isotc211._2005.gmd.MDMetadataType.class);

			Marshaller m = jc2.createMarshaller();

			m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, Boolean.TRUE);
			m.setProperty("com.sun.xml.bind.namespacePrefixMapper", new NamespacePrefixMapperImpl());
			m.setProperty(Marshaller.JAXB_ENCODING, "UTF-8");
			m.setProperty(Marshaller.JAXB_FRAGMENT, Boolean.FALSE);

			m.marshal(iso, new FileOutputStream(new File(DIR+"\\iso\\" + i + "_" + j + ".xml")));
		    }catch(Exception e){
			e.printStackTrace();
		    }
		}


	    }

	}


    }




}
