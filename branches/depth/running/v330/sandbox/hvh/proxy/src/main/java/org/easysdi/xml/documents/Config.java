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

package org.easysdi.xml.documents;


import java.io.Serializable;
import java.util.Collections;
import java.util.Iterator;
import java.util.List;

/**
 * @author Depth SA
 * 
 */
@Deprecated
public class Config implements Serializable{
	/**
	 * 
	 */
	private static final long serialVersionUID = -4599947106473181240L;
	private static String DEFAULT_DATE_FORMAT = "dd/MM/yyyy HH:mm:ss:SSS";
	private String id;
	private List<RemoteServerInfo> remoteServer;
	private String policyFile;
	private String logFile;
	private String classLogger;
	private String logLevel;
	private String xsltPath;
	private String logDateFormat = Config.DEFAULT_DATE_FORMAT;
	private String servletClass = "";
	private double maxRequestNumber = -1;
	private String hostTranslator = "";
	private String toleranceDistance = "0";
	private boolean grouping = true;
	private String title =null;
	private String abst =null;
	private List<String> keywordList = null;
	private ServiceContactInfo contactInfo;
	private String fees =null;
	private String accessConstraints =null;
	private String exceptionMode = "permissive";
	private String ogcSearchFilter=null;
	private OWSServiceMetadata owsServiceMetadata = null;
	private String period = "";
	private Boolean isHarvestingConfig = false;
//	private String negotiatedVersion = null;
	private List<String> supportedVersions ;

	/**
	 * @param ogcSearchFilter the ogcSearchFilter to set
	 */
	public void setOgcSearchFilter(String ogcSearchFilter) {
		if(ogcSearchFilter != null && !"".equals(ogcSearchFilter))
			this.ogcSearchFilter = ogcSearchFilter;
	}

	/**
	 * @return the ogcSearchFilter
	 */
	public String getOgcSearchFilter() {
		return ogcSearchFilter;
	}

	public String getToleranceDistance() {
		return toleranceDistance;
	}

	public void setToleranceDistance(String toleranceDistance) {
		this.toleranceDistance = toleranceDistance;
	}

	public String getHostTranslator() {
		return hostTranslator;
	}

	public void setHostTranslator(String hostTranslator) {
		this.hostTranslator = hostTranslator;
	}

	public String getXsltPath() {
		return xsltPath;
	}

	public void setXsltPath(String xsltPath) {
		this.xsltPath = xsltPath;
	}

	/**
     * 
     */
	public Config() {
		super();
	}

	/**
	 * @param id
	 * @param remoteServer
	 * @param policyFile
	 * @param loginService
	 * @param user
	 * @param password
	 * @param logFile
	 */
	public Config(String id, List<RemoteServerInfo> remoteServer, String policyFile, String logFile, String classLogger, String logLevel) {
		super();
		this.id = id;
		this.remoteServer = remoteServer;
		this.policyFile = policyFile;
		this.logFile = logFile;
		this.classLogger=classLogger;
		this.logLevel=logLevel;
	}

	public String getLogFile() {
		return logFile;
	}

	public void setLogFile(String logFile) {
		this.logFile = logFile;
	}

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public List<RemoteServerInfo> getRemoteServer() {
		return remoteServer;
	}

	public void setRemoteServer(List<RemoteServerInfo> remoteServer) {
		this.remoteServer = remoteServer;
	}

	public String getPolicyFile() {
		return policyFile;
	}

	public void setPolicyFile(String policyFile) {
		this.policyFile = policyFile;
	}

	public String getLogDateFormat() {
		return logDateFormat;
	}

	public void setLogDateFormat(String logDateFormat) {
		if (logDateFormat == null)
			logDateFormat = DEFAULT_DATE_FORMAT;
		else if (logDateFormat.length() == 0)
			logDateFormat = DEFAULT_DATE_FORMAT;
		else
			this.logDateFormat = logDateFormat;
	}

	public double getMaxRequestNumber() {
		return maxRequestNumber;
	}

	public void setMaxRequestNumber(double maxRequestNumber) {
		this.maxRequestNumber = maxRequestNumber;
	}

	public String getServletClass() {
		return servletClass;
	}

	public void setServletClass(String sc) {
		servletClass = sc;
	}

	public void setGrouping(boolean value) {
		this.grouping = value;
	}

	public boolean isGrouping() {
		return grouping;
	}

	/**
	 * @param accessConstraints the accessConstraints to set
	 */
	public void setAccessConstraints(String accessConstraints) {
		if(accessConstraints != null && accessConstraints != "")
			this.accessConstraints = accessConstraints;
	}

	/**
	 * @return the accessConstraints
	 */
	public String getAccessConstraints() {
		return accessConstraints;
	}

	/**
	 * @param fees the fees to set
	 */
	public void setFees(String fees) {
		if(fees != null && !"".equals(fees))
			this.fees = fees;
	}

	/**
	 * @return the fees
	 */
	public String getFees() {
		return fees;
	}

	/**
	 * @param keyword the keyword to set
	 */
	public void setKeywordList(List<String> keywordList) {
		if(keywordList != null && keywordList.size() != 0)
			this.keywordList = keywordList;
	}

	/**
	 * @return the keyword
	 */
	public List<String> getKeywordList() {
		return keywordList;
	}

	/**
	 * @param abst the abst to set
	 */
	public void setAbst(String abst) {
		if(abst != null && !"".equals(abst))
			this.abst = abst;
	}

	/**
	 * @return the abst
	 */
	public String getAbst() {
		return abst;
	}

	/**
	 * @param title the title to set
	 */
	public void setTitle(String title) {
		if(title != null && !"".equals(title))
			this.title = title;
	}

	/**
	 * @return the title
	 */
	public String getTitle() {
		return title;
	}



	public void setContactInfo(ServiceContactInfo contactInfo) {
		if(contactInfo != null && !"".equals(contactInfo))
			this.contactInfo = contactInfo;
	}

	public ServiceContactInfo getContactInfo() {
		return contactInfo;
	}

	/**
	 * 
	 * @param exceptionMode
	 */
	public void setExceptionMode(String exceptionMode) {
		if(exceptionMode != null && !"".equals(exceptionMode))
			this.exceptionMode = exceptionMode;
	}

	/**
	 * 
	 * @return the exception mode
	 */
	public String getExceptionMode() {
		return exceptionMode;
	}

	/**
	 * @param owsServiceIdentification the owsServiceIdentification to set
	 */
	public void setOwsServiceMetadata(OWSServiceMetadata owsServiceMetadata) {
		if(owsServiceMetadata != null && !owsServiceMetadata.isEmpty())
			this.owsServiceMetadata = owsServiceMetadata;
	}

	/**
	 * @return the owsServiceIdentification
	 */
	public OWSServiceMetadata getOwsServiceMetadata() {
		return owsServiceMetadata;
	}

	/**
	 * @param classLogger the classLogger to set
	 */
	public void setClassLogger(String classLogger) {
		this.classLogger = classLogger;
	}

	/**
	 * @return the classLogger
	 */
	public String getClassLogger() {
		return classLogger;
	}
	
	/**
	 * @return the logLevel
	 */
	public String getLogLevel() {
		return logLevel;
	}

	/**
	 * @return the period
	 */
	public String getPeriod() {
		return period;
	}

	/**
	 * @param period the period to set
	 */
	public void setPeriod(String period) {
		this.period = period;
	}

	/**
	 * @return the isHarvestingConfig
	 */
	public Boolean isHarvestingConfig() {
		return isHarvestingConfig;
	}

	/**
	 * @param isHarvestingConfig the isHarvestingConfig to set
	 */
	public void setIsHarvestingConfig(Boolean isHarvestingConfig) {
		this.isHarvestingConfig = isHarvestingConfig;
	}

//	/**
//	 * @return the negotiatedVersion
//	 */
//	public String getNegotiatedVersion() {
//		return negotiatedVersion;
//	}
//
//	/**
//	 * @param negotiatedVersion the negotiatedVersion to set
//	 */
//	public void setNegotiatedVersion(String negotiatedVersion) {
//		this.negotiatedVersion = negotiatedVersion;
//	}
	
	/**
	 * @return the supportedVersions
	 */
	public List<String> getSupportedVersions() {
		return supportedVersions;
	}

	/**
	 * @param supportedVersions the supportedVersions to set
	 */
	public void setSupportedVersions(List<String> supportedVersions) {
		this.supportedVersions = supportedVersions;
	}
	
	/**
	 * 
	 * @param requestedVersion
	 * @return
	 */
	public String getRequestNegotiatedVersion(String requestedVersion, String service){
		if(supportedVersions.size() == 0){
			if(service.equalsIgnoreCase("WMS"))
				return "1.1.1";
			if(service.equalsIgnoreCase("WFS"))
				return "1.0.0";
			if(service.equalsIgnoreCase("WMTS"))
				return "1.0.0";
			if(service.equalsIgnoreCase("CSW"))
				return "2.0.2";
			return null;
		}else if (requestedVersion == null ){
			//Get the highest supported version
			Collections.sort(supportedVersions);
			return supportedVersions.get(supportedVersions.size()-1);
		}else {
			if(!supportedVersions.contains(requestedVersion)){
				Collections.sort(supportedVersions);
				//Requested version is lower than the lowest version supported
				//return the lowest version supported
				if(requestedVersion.compareTo(supportedVersions.get(0)) < 0){
					requestedVersion = supportedVersions.get(0);
				}else {
					//return the highest version supported less than the requested one
					Iterator<String> i =  supportedVersions.iterator();
					String returnedVersion = null;
					while(i.hasNext()){
						String v = i.next();
						if(returnedVersion == null)
							returnedVersion = v;
						if(v.compareTo(requestedVersion) < 0 && v.compareTo(returnedVersion) > 0){
							returnedVersion = v;
						}
					}
					return returnedVersion;
				}
			}
			return requestedVersion;
		}
	}
}
