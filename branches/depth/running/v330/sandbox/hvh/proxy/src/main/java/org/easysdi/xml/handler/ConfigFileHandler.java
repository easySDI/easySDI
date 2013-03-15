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

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.Vector;

import org.easysdi.xml.documents.*;
import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

@Deprecated
public class ConfigFileHandler extends DefaultHandler {
	private org.easysdi.xml.documents.Config config;
	private String data = "";
	private String id = null;
	private boolean grouping = true;
	private List<RemoteServerInfo> remoteServer = null;
	private String policyFile = null;
	private String loginService = null;
	private String alias = null;
	private String user = null;
	private String password = null;
	private String logFile = null;
	private String logPath = "";
	private String logSuffix = "";
	private String logPrefix = "";
	private String logExtension = "";
	private String logPeriod = "";
	private String classLogger = "org.easysdi.proxy.log.ProxyLogger";
	private String logLevel = "INFO";
	private String toleranceDistance = "0";
	private boolean isAuthorization = false;
	private boolean isTransaction = false;
	private boolean isTheGoodId = false;
	private boolean isConfig = false;
	private boolean isRemoteServer = false;
	private boolean isRemoteServerMaster = false;
	private boolean isRemoteServerList = false;
	private String remoteServerUrl = null;
	private boolean isLogConfig = false;
	private boolean isXsltPath = false;
	private String xsltPathUrl = null;
	private String logDateFormat = null;
	private String maxRecords = null;
	private String servletClass;
	private boolean isFileStructure = false;
	private double maxRequestNumber = -1;
	private String prefix = "";
	private String hostTranslator = "";
	private String transaction = "ogc";
	private boolean isDouglasPeuckerSimplifier = false;
	private boolean isServiceMetadata=false;
	private boolean isContactInformation=false;
	private boolean isContactAddress=false;
	private String title =null;
	private String abst =null;
	private List<String> keywordList = null;
	private String contactName =null;
	private String contactOrganisation =null;
	private String contactPosition =null;
	private String adressType =null;
	private String adress =null;
	private String postalCode =null;
	private String city =null;
	private String state =null;
	private String country =null;
	private String voicePhone =null;
	private String facsimile =null;
	private String electronicMailAddress =null;
	private String linkage =null;
	private String hoursOfService =null;
	private String instructions =null;
	private String fees =null;
	private String accessConstraints =null;
	private ServiceContactInfo contactInfo ;
	private ServiceContactAdressInfo contactAdress;
	private String exceptionMode ="Permissive";
	private boolean isException=false;
	private String ogcSearchFilter="";
	
	private Boolean isServiceProvider = false;
	private Boolean isResponsibleParty = false;
	private Boolean isContact = false;
	private Boolean isTelephone = false;
	private Boolean isAddress = false;
	private String providerName = null;
	private String providerSite = null;
	private String individualName = null;
	private String positionName = null;
	private String role = null;
	private String delivryPoint = null;
	private String area = null;
	private OWSAddress owsAddress = null;
	private OWSContact owsContact = null;
	private OWSResponsibleParty owsResponsible = null;
	private OWSServiceProvider owsProvider = null;
	private OWSTelephone owsPhone = null;
	private OWSServiceMetadata owsServiceMetadata = null;
	private OWSServiceIdentification  owsServiceIdentification = null;
	private Boolean isHarvestingConfig = false;
	private String negotiatedVersion = null;
	private List<String> supportedVersions = new ArrayList<String>();
	
	
	public ConfigFileHandler(String id) {
		super();
		this.id = id;
	}

	public void startElement(String nameSpace, String localName, String qName, Attributes attr) throws SAXException {

		if (qName.equals("config")) {
			isConfig = true;
			String s = attr.getValue("id");
			if (s.equals(id)) {
				isTheGoodId = true;
			}
			String sgrouping = attr.getValue("grouping");
			grouping = (sgrouping == null) ? true : Boolean.parseBoolean(sgrouping);
		}

		if (isTheGoodId && isConfig && qName.equals("douglasPeuckerSimplifier")) {
			isDouglasPeuckerSimplifier = true;
		}
	
		if (isTheGoodId && isConfig && qName.equals("remote-server-list")) {
			isRemoteServerList = true;
		}
		
		if (isTheGoodId && isConfig && isRemoteServerList && qName.equals("remote-server")) {
			isRemoteServer = true;
			String master = attr.getValue("master"); 
			if(master!= null && master.equals("true"))
				isRemoteServerMaster = true;
		}
		
		if (isTheGoodId && isConfig && qName.equals("authorization")) {
			isAuthorization = true;
		}
		
		if (isTheGoodId && isConfig && isRemoteServer && qName.equals("transaction")) {
			isTransaction = true;
		}
		
		if (isTheGoodId && isConfig && qName.equals("log-config")) {
			isLogConfig = true;
		}
		
		if (isTheGoodId && isConfig && qName.equals("xslt-path")) {
			isXsltPath = true;
		}
		
		if (isTheGoodId && isConfig && isLogConfig && qName.equals("file-structure")) {
			isFileStructure = true;
		}

		if (isTheGoodId && isConfig && qName.equals("service-metadata")) {
			isServiceMetadata = true;
			return;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && qName.equals("ContactInformation")) {
			isContactInformation = true;
			return;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("ContactAddress")) {
			isContactAddress = true;
			return;
		}
		
		if (isTheGoodId && isConfig && qName.equals("exception")) {
			isException = true;
			return;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && qName.equals("ServiceProvider")) {
			isServiceProvider = true;
			return;
		}

		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && qName.equals("ResponsibleParty")) {
			isResponsibleParty = true;
			return;
		}
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && qName.equals("Contact")) {
			isContact = true;
			return;
		}
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && qName.equals("Telephone")) {
			isTelephone = true;
			return;
		}
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && qName.equals("Address")) {
			isAddress = true;
			return;
		}
	}

	public void endElement(String nameSpace, String localName, String qName) throws SAXException {
		if (qName.equals("config")) {
			isConfig = false;
			if (isTheGoodId) {
				config = new org.easysdi.xml.documents.Config(id, remoteServer, policyFile, logFile, classLogger,logLevel);
				config.setXsltPath(xsltPathUrl);
				config.setLogDateFormat(logDateFormat);
				config.setServletClass(servletClass);
				config.setMaxRequestNumber(maxRequestNumber);
				config.setHostTranslator(hostTranslator);
				config.setToleranceDistance(toleranceDistance);
				config.setGrouping(grouping);
				config.setAbst(abst);
				config.setAccessConstraints(accessConstraints);
				config.setFees(fees);
				config.setKeywordList(keywordList);
				config.setTitle(title);
				config.setContactInfo(contactInfo);
				config.setExceptionMode(exceptionMode);
				config.setOgcSearchFilter(ogcSearchFilter);
				config.setOwsServiceMetadata(owsServiceMetadata);
				config.setPeriod(logPeriod);
				config.setIsHarvestingConfig(isHarvestingConfig);
//				config.setNegotiatedVersion(negotiatedVersion);
				config.setSupportedVersions(supportedVersions);
			}
			isTheGoodId = false;
		}

		if (isTheGoodId && isConfig && qName.equals("douglasPeuckerSimplifier")) {
			isDouglasPeuckerSimplifier = false;
		}
		
		if (isTheGoodId && isConfig && isDouglasPeuckerSimplifier && qName.equals("toleranceDistance")) {

			toleranceDistance = data;
		}

		if (isTheGoodId && isConfig && qName.equals("host-translator")) {
			hostTranslator = data;
		}

		if (isTheGoodId && isConfig && qName.equals("servlet-class")) {
			servletClass = data;
		}
		if (isTheGoodId && isConfig && qName.equals("harvesting-config")) {
			isHarvestingConfig = Boolean.valueOf(data);
		}
		if (isTheGoodId && isConfig && qName.equals("negotiated-version")) {
			negotiatedVersion = data;
		}
		if (isTheGoodId && isConfig  && !isRemoteServerList && qName.equals("version")) {
			supportedVersions.add(data);
		}
		
		if (isTheGoodId && isConfig && qName.equals("remote-server-list")) {
			isRemoteServerList = false;
		}
		
		if (isTheGoodId && isConfig && isRemoteServerList && qName.equals("max-request-number")) {
			maxRequestNumber = Double.parseDouble(data);
		}
		
		if (isTheGoodId && isConfig && isRemoteServerList && qName.equals("remote-server")) {
			if (remoteServer == null)
				remoteServer = new Vector<RemoteServerInfo>();
			RemoteServerInfo rs = new RemoteServerInfo(alias, remoteServerUrl, user, password, maxRecords, loginService, prefix, transaction);
			remoteServer.add(rs);
			isRemoteServer = false;
			rs.isMaster =isRemoteServerMaster;
			isRemoteServerMaster = false;
			remoteServerUrl = null;
			alias = null;
			user = null;
			password = null;
			maxRecords = null;
			loginService = null;
			transaction = null;
		}

		if (isTheGoodId && isConfig && qName.equals("authorization")) {
			isAuthorization = false;
		}
		
		if (isTheGoodId && isConfig && isAuthorization && qName.equals("policy-file")) {
			policyFile = data;
		}

		if (isTheGoodId && isConfig && isTransaction && qName.equals("type")) {
			transaction = data;
		}

		if (isTheGoodId && isConfig && isRemoteServer && qName.equals("transaction")) {
			isTransaction = false;
		}
		
		if (isTheGoodId && isConfig && isRemoteServer && qName.equals("login-service")) {
			loginService = data;
		}
		
		if (isTheGoodId && isConfig && isRemoteServer && qName.equals("alias")) {
			alias = data;
		}
		
		if (isTheGoodId && isConfig && isRemoteServer && qName.equals("url")) {
			remoteServerUrl = data;
		}
		
		if (isTheGoodId && isConfig && isRemoteServer && qName.equals("max-records")) {
			maxRecords = data;
		}
		
		if (isTheGoodId && isConfig && isRemoteServer && qName.equals("prefix")) {
			prefix = data;
		}

		if (isTheGoodId && isConfig && isRemoteServer && qName.equals("user")) {
			if (data.length() == 0)
				user = null;
			else
				user = data;
		}
		
		if (isTheGoodId && isConfig && isRemoteServer && qName.equals("password")) {
			if (data.length() == 0)
				password = null;
			else
				password = data;
		}
		
		if (isTheGoodId && isConfig && qName.equals("xslt-path")) {
			isXsltPath = false;
		}
		
		if (isTheGoodId && isConfig && isXsltPath && qName.equals("url")) {
			xsltPathUrl = data;
		}

		if (isTheGoodId && isConfig && qName.equals("log-config")) {
			isLogConfig = false;
		}
		
		if (isTheGoodId && isConfig && isLogConfig && qName.equals("url")) {
			logFile = data;
		}

		if (isTheGoodId && isConfig && isLogConfig && isFileStructure && qName.equals("path")) {
			logPath = data;
		}
		
		if (isTheGoodId && isConfig && isLogConfig && isFileStructure && qName.equals("suffix")) {
			logSuffix = data;
		}
		
		if (isTheGoodId && isConfig && isLogConfig && isFileStructure && qName.equals("prefix")) {
			logPrefix = data;
		}
		
		if (isTheGoodId && isConfig && isLogConfig && isFileStructure && qName.equals("extension")) {
			logExtension = data;
		}
		
		if (isTheGoodId && isConfig && isLogConfig && isFileStructure && qName.equals("period")) {
			logPeriod = data;
		}
		
		if (isTheGoodId && isConfig && isLogConfig && qName.equals("file-structure")) {
			String period = "";
			if (logPeriod.equalsIgnoreCase("daily")) {
				DateFormat dateFormat = new SimpleDateFormat("yyyyMMdd");
				Date date = new Date();
				period = dateFormat.format(date);
			}
			if (logPeriod.equalsIgnoreCase("monthly")) {
				DateFormat dateFormat = new SimpleDateFormat("MM");
				Date date = new Date();
				period = dateFormat.format(date);
			}
			if (logPeriod.equalsIgnoreCase("weekly")) {
				DateFormat dateFormat = new SimpleDateFormat("yyyy");
				Date date = new Date();
				Calendar c = Calendar.getInstance();
				period = dateFormat.format(date) + c.get(Calendar.WEEK_OF_YEAR);
			}
			if (logPeriod.equalsIgnoreCase("annually")) {
				DateFormat dateFormat = new SimpleDateFormat("yyyy");
				Date date = new Date();
				period = dateFormat.format(date);
			}
			
			logFile = logPath + "/" + logPrefix + "." + period + "." + logSuffix  ;
			if(!logExtension.equals(""))
			{
				logFile += "." + logExtension;
			}
			isFileStructure = false;
		}

		if (isTheGoodId && isConfig && isLogConfig && qName.equals("date-format")) {
			logDateFormat = data;
		}
		
		if (isTheGoodId && isConfig && isLogConfig && qName.equals("logger")) {
			classLogger = data;
		}
		
		if (isTheGoodId && isConfig && isLogConfig && qName.equals("log-level")) {
			logLevel = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && qName.equals("Title")) {
			title = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && qName.equals("Abstract")) {
			abst = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && qName.equals("Keyword")) {
			if(data != null && !"".equals(data))
			{
				if(keywordList == null)
				{
					keywordList = new Vector <String>();
				}
				keywordList.add(data);
			}
		}
	
		if (isTheGoodId && isConfig && isServiceMetadata  && qName.equals("Fees")) {
			fees = data;		
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && qName.equals("AccessConstraints")) {
			accessConstraints = data;			
		}

		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("ContactName")) {
			contactName = data;
		}

		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("ContactOrganization")) {
			contactOrganisation = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("ContactPosition")) {
			contactPosition = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("VoicePhone")) {
			voicePhone = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("Facsimile")) {
			facsimile = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("ElectronicMailAddress")) {
			electronicMailAddress = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("Linkage")) {
			linkage =data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("HoursofSservice")) {
			hoursOfService = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("Instructions")) {
			instructions = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && isContactAddress && qName.equals("AddressType")) {
			adressType = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && isContactAddress && qName.equals("Address")) {
			adress =data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && isContactAddress && qName.equals("PostalCode")) {
			postalCode = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && isContactAddress && qName.equals("City")) {
			city = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && isContactAddress && qName.equals("State")) {
			state = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && isContactAddress && qName.equals("Country")) {
			country = data;
		}
			
		if (isTheGoodId && isConfig && isServiceMetadata && isContactInformation && qName.equals("ContactAddress")) {
			contactAdress = new ServiceContactAdressInfo();
			contactAdress.setAddress(adress);
			contactAdress.setCity(city);
			contactAdress.setCountry(country);
			contactAdress.setPostalCode(postalCode);
			contactAdress.setState(state);
			contactAdress.setType(adressType);
			isContactAddress = false;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && qName.equals("ContactInformation")) {
			contactInfo = new ServiceContactInfo();
			contactInfo.setContactAddress(contactAdress);
			contactInfo.seteMail(electronicMailAddress);
			contactInfo.setFacSimile(facsimile);
			contactInfo.setHoursofSservice(hoursOfService);
			contactInfo.setInstructions(instructions);
			contactInfo.setLinkage(linkage);
			contactInfo.setName(contactName);
			contactInfo.setOrganization(contactOrganisation);
			contactInfo.setPosition(contactPosition);
			contactInfo.setVoicePhone(voicePhone);
			isContactInformation = false;
		}
		
		if (isTheGoodId && isConfig && isException && qName.equals("mode")) {
			exceptionMode = data;
			isException = false;
		}
		
		if (isTheGoodId && isConfig && qName.equals("ogc-search-filter")) {
			ogcSearchFilter = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && qName.equals("ProviderName")) {
			providerName = data;
		}

		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && qName.equals("ProviderSite")) {
			providerSite = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && qName.equals("IndividualName")) {
			individualName = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && qName.equals("PositionName")) {
			positionName = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && qName.equals("Role")) {
			role = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && qName.equals("HoursofService")) {
			hoursOfService = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && qName.equals("Instructions")) {
			instructions = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && qName.equals("onlineResource")) {
			linkage = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && isTelephone && qName.equals("VoicePhone")) {
			voicePhone = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && isTelephone && qName.equals("Facsimile")) {
			facsimile = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && isAddress && qName.equals("AddressType")) {
			adressType = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && isAddress && qName.equals("DelivryPoint")) {
			delivryPoint = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && isAddress && qName.equals("PostalCode")) {
			postalCode = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && isAddress && qName.equals("City")) {
			city = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && isAddress && qName.equals("Area")) {
			area = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && isAddress && qName.equals("Country")) {
			country = data;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && isAddress && qName.equals("ElectronicMailAddress")) {
			electronicMailAddress = data;
		}
		
		if (isTheGoodId && isConfig && qName.equals("service-metadata")) {
			owsServiceMetadata = new OWSServiceMetadata();
			owsServiceMetadata.setIdentification(owsServiceIdentification);
			owsServiceMetadata.setProvider(owsProvider);
			isServiceMetadata = false;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && qName.equals("ServiceIdentification")) {
			owsServiceIdentification = new OWSServiceIdentification();
			owsServiceIdentification.setAbst(abst);
			owsServiceIdentification.setAccessConstraints(accessConstraints);
			owsServiceIdentification.setFees(fees);
			owsServiceIdentification.setKeywords(keywordList);
			owsServiceIdentification.setTitle(title);
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && qName.equals("ServiceProvider")) {
			owsProvider = new OWSServiceProvider();
			owsProvider.setLinkage(providerSite);
			owsProvider.setName(providerName);
			owsProvider.setResponsibleParty(owsResponsible);
			isServiceProvider = false;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && qName.equals("ResponsibleParty")) {
			owsResponsible = new OWSResponsibleParty();
			owsResponsible.setContactInfo(owsContact);
			owsResponsible.setName(individualName);
			owsResponsible.setPosition(positionName);
			owsResponsible.setRole(role);
			isResponsibleParty = false;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && qName.equals("Contact")) {
			owsContact = new OWSContact();
			owsContact.setAdress(owsAddress);
			owsContact.setContactPhone(owsPhone);
			owsContact.setHoursofSservice(hoursOfService);
			owsContact.setInstructions(instructions);
			owsContact.setLinkage(linkage);
			isContact = false;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && qName.equals("Telephone")) {
			owsPhone = new OWSTelephone();
			owsPhone.setFacSimile(facsimile);
			owsPhone.setVoicePhone(voicePhone);
			isTelephone = false;
		}
		
		if (isTheGoodId && isConfig && isServiceMetadata && isServiceProvider && isResponsibleParty && isContact && qName.equals("Address")) {
			owsAddress = new OWSAddress();
			owsAddress.setDelivryPoint(delivryPoint);
			owsAddress.setArea(area);
			owsAddress.setCity(city);
			owsAddress.setCountry(country);
			owsAddress.setElectronicMail(electronicMailAddress);
			owsAddress.setPostalCode(postalCode);
			owsAddress.setType(adressType);
			isAddress = false;
		}
		
		data = "";
	}

	public void startDocument() {
	}

	public void endDocument() {
	}

	public void characters(char[] caracteres, int debut, int longueur) throws SAXException {
		String donnees = new String(caracteres, debut, longueur);
		if (data == null)
			data = donnees.trim();
		else
			data = data + donnees.trim();
	}

	public org.easysdi.xml.documents.Config getConfig() {
		return config;
	}
}
