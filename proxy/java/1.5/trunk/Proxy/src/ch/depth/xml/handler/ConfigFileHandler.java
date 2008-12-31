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
package ch.depth.xml.handler;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.Vector;

import javax.swing.text.html.HTMLDocument.HTMLReader.IsindexAction;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

import ch.depth.xml.documents.RemoteServerInfo;

public class ConfigFileHandler extends DefaultHandler {
    private ch.depth.xml.documents.Config config;
    private String data = "";
    private String id=null;
    private List<RemoteServerInfo> remoteServer = null;
    private String policyFile=null;
    private String loginService=null;
    private String user=null;
    private String password=null;
    private String logFile =null;
    private String logPath = "";
    private String logSuffix = "";
    private String logPrefix = "";
    private String logExtension = "";
    private String logPeriod = "";
    private String toleranceDistance="0";
    private boolean isAuthorization = false;
    private boolean isPolicyFile = false;
    private boolean isLoginService = false;
    private boolean isTransaction = false;
    private boolean isUser = false;
    private boolean isPassword = false;
    private boolean isTheGoodId = false;
    private boolean isConfig = false;
    private boolean isRemoteServer = false;
    private boolean isRemoteServerList = false;
    private boolean isLogFile= false;
    private boolean isRemoteServerUrl= false;
    private String remoteServerUrl=null;
    private boolean isLogConfig= false;
    private boolean isXsltPath= false;
    private boolean isLogConfigUrl= false;
    private boolean isXsltPathUrl= false;    
    private String xsltPathUrl=null;
    private boolean isLogDateFormat= false;
    private String logDateFormat= null;
    private boolean isMaxRecords= false;
    private String maxRecords=null;
    private boolean isServletClass=false;
    private String servletClass; 
    private boolean isFileStructure = false;
    private boolean isMaxRequestNumber=false;
    private double maxRequestNumber=-1;
    private boolean isAvailabilityPeriod =false;
    private boolean isAvailabilityPeriodFrom=false;
    private boolean isAvailabilityPeriodTo=false;
    private boolean isAvailabilityPeriodFromDate=false;
    private boolean isAvailabilityPeriodFromMask=false;
    private boolean isAvailabilityPeriodToDate=false;
    private boolean isAvailabilityPeriodToMask=false;
    private String availabilityPeriodFromDate=null;
    private String availabilityPeriodFromMask=null;
    private String availabilityPeriodToDate=null;
    private String availabilityPeriodToMask=null;
    private String prefix="";
    private String hostTranslator="";
    private String transaction="ogc";
    private boolean isPrefix=false;
    private String deleteServiceUrl=null;
    private String insertServiceUrl=null;
    private String searchServiceUrl=null;
    private boolean isDouglasPeuckerSimplifier=false;
    private boolean isToleranceDistance=false;
    
    public ConfigFileHandler(String id) {
	super();
	this.id = id;
    }

    public void startElement(String nameSpace, String localName, String qName,
	    Attributes attr) throws SAXException {

	if (qName.equals("config")) {
	    isConfig=true;
	    String s = attr.getValue("id");
	    if (s.equals(id)){
		isTheGoodId = true;
	    }
	    
	}
	
	if (isTheGoodId && isConfig && qName.equals("douglasPeuckerSimplifier")) {	    
	    isDouglasPeuckerSimplifier=true;
	}
	if (isTheGoodId && isConfig && isDouglasPeuckerSimplifier && qName.equals("toleranceDistance")) {	    
	    isToleranceDistance=true;
	}
	
	
	if (isTheGoodId && isConfig && qName.equals("servlet-class")) {	    
	    isServletClass=true;
	}
	if (isTheGoodId && isConfig && qName.equals("availability-period")) {	    
	    isAvailabilityPeriod=true;
	}
	if (isTheGoodId && isConfig && isAvailabilityPeriod && qName.equals("from")) {	    
	    isAvailabilityPeriodFrom=true;
	}
	if (isTheGoodId && isConfig && isAvailabilityPeriod && isAvailabilityPeriodFrom && qName.equals("date")) {	    
	    isAvailabilityPeriodFromDate=true;
	}
	if (isTheGoodId && isConfig && isAvailabilityPeriod && isAvailabilityPeriodFrom && qName.equals("mask")) {	    
	    isAvailabilityPeriodFromMask=true;
	}
	
	if (isTheGoodId && isConfig && isAvailabilityPeriod && qName.equals("to")) {	    
	    isAvailabilityPeriodTo=true;
	}
	if (isTheGoodId && isConfig && isAvailabilityPeriod && isAvailabilityPeriodTo && qName.equals("date")) {	    
	    isAvailabilityPeriodToDate=true;
	}
	if (isTheGoodId && isConfig && isAvailabilityPeriod && isAvailabilityPeriodTo && qName.equals("mask")) {	    
	    isAvailabilityPeriodToMask=true;
	}
	
	
	if (isTheGoodId && isConfig &&  qName.equals("remote-server-list")) {	    
	    isRemoteServerList=true;
	}
	if (isTheGoodId && isConfig && isRemoteServerList && qName.equals("max-request-number")) {	    
	    isMaxRequestNumber=true;
	}
	
	
	if (isTheGoodId && isConfig && isRemoteServerList && qName.equals("remote-server")) {	    
	    isRemoteServer=true;
	}
	if (isTheGoodId &&  isConfig && qName.equals("authorization")) {
	    isAuthorization = true;
	}
	if (isTheGoodId && isConfig &&isAuthorization && qName.equals("policy-file")) {
	    isPolicyFile = true;	    
	}
	if (isTheGoodId && isConfig && isRemoteServer && qName.equals("transaction")) {	    
	    isTransaction = true;	    
	}
	if (isTheGoodId && isConfig &&isRemoteServer && qName.equals("login-service")) {
	    isLoginService = true;	    
	}
	if (isTheGoodId && isConfig &&isRemoteServer && qName.equals("url")) {
	    isRemoteServerUrl = true;
	}
	if (isTheGoodId && isConfig &&isRemoteServer && qName.equals("user")) {
	    isUser = true;
	}
	if (isTheGoodId && isConfig &&isRemoteServer && qName.equals("prefix")) {
	    isPrefix = true;
	}
	if (isTheGoodId && isConfig &&isRemoteServer && qName.equals("password")) {
	    isPassword = true;
	}
	if (isTheGoodId && isConfig &&isRemoteServer && qName.equals("max-records")) {
	    isMaxRecords = true;
	}
	
	
	if (isTheGoodId && isConfig && qName.equals("log-config")) {	    	    
	    isLogConfig = true;		    
	}
	if (isTheGoodId && isConfig && qName.equals("xslt-path")) {	    	    
	    isXsltPath = true;		    
	}
	if (isTheGoodId && isConfig  && isLogConfig && qName.equals("url")) {	    
	    isLogConfigUrl = true;		    
	}
	if (isTheGoodId && isConfig  && isLogConfig && qName.equals("date-format")) {	    
	    isLogDateFormat = true;		    
	}
	if (isTheGoodId && isConfig  && isLogConfig && qName.equals("file-structure")) {	    
	    isFileStructure = true;		    
	}
	
	if (isTheGoodId && isConfig  && isXsltPath && qName.equals("url")) {	    
	    isXsltPathUrl = true;		    
	}
    }
    
    
    public void endElement(String nameSpace, String localName, String qName)
    throws SAXException {
	if (qName.equals("config")) {
	    isConfig = false;	    
	    if (isTheGoodId) {
		config = new ch.depth.xml.documents.Config(id,remoteServer,policyFile,logFile);
		config.setXsltPath(xsltPathUrl);
		config.setLogDateFormat(logDateFormat);
		config.setServletClass(servletClass);
		config.setMaxRequestNumber(maxRequestNumber);
		config.setHostTranslator(hostTranslator);
		config.setToleranceDistance(toleranceDistance);
	    }
	    isTheGoodId = false;
	}
	
	if (isTheGoodId && isConfig && qName.equals("douglasPeuckerSimplifier")) {	    
	    isDouglasPeuckerSimplifier=false;
	}
	if (isTheGoodId && isConfig && isDouglasPeuckerSimplifier && qName.equals("toleranceDistance")) {	    
	    
	    toleranceDistance = data;
	    isToleranceDistance=false;
	}
	
	if (isTheGoodId && isConfig && qName.equals("host-translator")) {
	    hostTranslator = data;	    
	}
	
	if (isTheGoodId && isConfig && qName.equals("servlet-class")) {
	    servletClass = data;
	    isServletClass=false;
	}
	if (isTheGoodId && isConfig && qName.equals("remote-server-list")) {
	    isRemoteServerList = false;
	}
	if (isTheGoodId && isConfig && isRemoteServerList && qName.equals("max-request-number")) {
	    maxRequestNumber = Double.parseDouble(data);
	    isMaxRequestNumber = false;
	}

	if (isTheGoodId && isConfig && qName.equals("availability-period")) {	    
	    isAvailabilityPeriod=false;
	}
	if (isTheGoodId && isConfig && isAvailabilityPeriod && qName.equals("from")) {	    
	    isAvailabilityPeriodFrom=false;
	}
	if (isTheGoodId && isConfig && isAvailabilityPeriod && isAvailabilityPeriodFrom && qName.equals("date")) {
	    availabilityPeriodFromDate=data;
	    isAvailabilityPeriodFromDate=false;
	}
	if (isTheGoodId && isConfig && isAvailabilityPeriod && isAvailabilityPeriodFrom && qName.equals("mask")) {
	    availabilityPeriodFromMask=data;
	    isAvailabilityPeriodFromMask=false;
	}
	
	if (isTheGoodId && isConfig && isAvailabilityPeriod && qName.equals("to")) {	    
	    isAvailabilityPeriodTo=false;
	}
	if (isTheGoodId && isConfig && isAvailabilityPeriod && isAvailabilityPeriodTo && qName.equals("date")) {
	    availabilityPeriodToDate=data;
	    isAvailabilityPeriodToDate=false;
	}
	if (isTheGoodId && isConfig && isAvailabilityPeriod && isAvailabilityPeriodTo && qName.equals("mask")) {
	    availabilityPeriodToMask=data;
	    isAvailabilityPeriodToMask=false;
	}
	
	
	if (isTheGoodId && isConfig && isRemoteServerList && qName.equals("remote-server")) {
	    if (remoteServer == null) remoteServer = new Vector<RemoteServerInfo>();
	    RemoteServerInfo rs = new RemoteServerInfo(remoteServerUrl,user,password,maxRecords,loginService,prefix,transaction);
	    rs.setDeleteServiceUrl(deleteServiceUrl);
	    rs.setInsertServiceUrl(insertServiceUrl);
	    rs.setSearchServiceUrl(searchServiceUrl);
	    remoteServer.add(rs);
	    
	    isRemoteServer=false;
	    remoteServerUrl = null; 
	    user = null  ;
	    password= null;
	    maxRecords = null;
	    loginService=null;	    
	    transaction=null;
	    deleteServiceUrl=null;
	    insertServiceUrl=null;
	    searchServiceUrl=null;
	}
	
	if (isTheGoodId && isConfig && qName.equals("authorization")) {
	    isAuthorization = false;
	}
	if (isTheGoodId && isConfig && isAuthorization && qName.equals("policy-file")) {
	    policyFile = data;
	    isPolicyFile = false;	    
	}

								     
	if (isTheGoodId && isConfig &&isTransaction && qName.equals("search-service-url")) {	    
	    searchServiceUrl = data;	    	    
	}
	if (isTheGoodId && isConfig &&isTransaction && qName.equals("delete-service-url")) {
	    
	    deleteServiceUrl = data;	    	    
	}
	if (isTheGoodId && isConfig &&isTransaction && qName.equals("insert-service-url")) {
	    
	    insertServiceUrl = data;	    	    
	}
	
	if (isTheGoodId && isConfig &&isTransaction && qName.equals("type")) {
	    transaction = data;	    	   
	}
	
	if (isTheGoodId && isConfig && isRemoteServer && qName.equals("transaction")) {	
	    isTransaction = false;	    
	}
	if (isTheGoodId && isConfig && isRemoteServer && qName.equals("login-service")) {
	    loginService = data;
	    isLoginService = false;	    
	}
	if (isTheGoodId && isConfig && isRemoteServer && qName.equals("url")) {	    	    	    
	    remoteServerUrl = data;	    	    
	    isRemoteServerUrl = false;
	}
	if (isTheGoodId && isConfig && isRemoteServer && qName.equals("max-records")) {	    	    	    
	    maxRecords = data;	    	    
	    isMaxRecords = false;
	}
	if (isTheGoodId && isConfig && isRemoteServer && qName.equals("prefix")) {	    	    	    
	    prefix = data;	    	    
	    isPrefix = false;
	}
	
	if (isTheGoodId && isConfig && isRemoteServer && qName.equals("user")) {
	    if (data.length() == 0) user= null;
	    else    user= data;
	    isUser = false;
	}
	if (isTheGoodId && isConfig && isRemoteServer && qName.equals("password")) {
	    if (data.length() == 0) password= null;
	    else    password=data;	    
	    isPassword = false;		    
	}
	if (isTheGoodId && isConfig && qName.equals("xslt-path")) {	    	    
	    isXsltPath = false;		    
	}
	if (isTheGoodId && isConfig  && isXsltPath && qName.equals("url")) {
	    xsltPathUrl = data;
	    isXsltPathUrl = false;		    
	}
	
	
	if (isTheGoodId && isConfig && qName.equals("log-config")) {	    	    
	    isLogConfig = false;		    
	}
	if (isTheGoodId && isConfig  && isLogConfig && qName.equals("url")) {
	    logFile= data;
	    isLogConfigUrl = false;		    
	}

	if (isTheGoodId && isConfig  && isLogConfig && isFileStructure && qName.equals("path")) {
	    logPath= data;	    		   
	}
	if (isTheGoodId && isConfig  && isLogConfig && isFileStructure && qName.equals("suffix")) {
	    logSuffix= data;	    		    
	}
	if (isTheGoodId && isConfig  && isLogConfig && isFileStructure && qName.equals("prefix")) {
	    logPrefix= data;	    		    
	}
	if (isTheGoodId && isConfig  && isLogConfig && isFileStructure && qName.equals("extension")) {
	    logExtension= data;	    		    
	}
	if (isTheGoodId && isConfig  && isLogConfig && isFileStructure && qName.equals("period")) {
	    logPeriod= data;	    		    
	}
	if (isTheGoodId && isConfig  && isLogConfig && qName.equals("file-structure")) {
	    String period ="";
	    if (logPeriod.equalsIgnoreCase("daily")){		
		 DateFormat dateFormat = new SimpleDateFormat("yyyyMMdd");
		 Date date = new Date();
		 period = dateFormat.format(date);		
	    }
	    if (logPeriod.equalsIgnoreCase("monthly")){
		 DateFormat dateFormat = new SimpleDateFormat("MM");
		 Date date = new Date();
		 period = dateFormat.format(date);		
	    }
	    if (logPeriod.equalsIgnoreCase("weekly")){
		 DateFormat dateFormat = new SimpleDateFormat("yyyy");
		 Date date = new Date();
		 Calendar c = Calendar.getInstance();				
		 period = dateFormat.format(date)+c.get(Calendar.WEEK_OF_YEAR);		
	    }
	    if (logPeriod.equalsIgnoreCase("annually")){
		 DateFormat dateFormat = new SimpleDateFormat("yyyy");
		 Date date = new Date();
		 period = dateFormat.format(date);		
	    }
	    logFile= logPath+"/"+logSuffix+"."+period+"."+logPrefix+"."+logExtension;
	    isFileStructure =false;	    		    
	}	  

	
	
	if (isTheGoodId && isConfig  && isLogConfig && qName.equals("date-format")) {
	    logDateFormat=data;
	    isLogDateFormat = false;		    
	}
	
	data="";
    }

    public void startDocument() {

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

    public ch.depth.xml.documents.Config getConfig() {
        return config;
    }
}
