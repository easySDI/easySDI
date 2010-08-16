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
import java.util.List;

/**
 * @author Administrateur
 * 
 */
public class Config implements Serializable{
	/**
	 * 
	 */
	private static final long serialVersionUID = -4599947106473181240L;
	private static String DEFAULT_DATE_FORMAT = "yyyy/MM/dd HH:mm:ss";
	private String id;
	private List<RemoteServerInfo> remoteServer;
	private String policyFile;
	private String logFile;
	private String xsltPath;
	private String logDateFormat = Config.DEFAULT_DATE_FORMAT;
	private String servletClass = "";
	private double maxRequestNumber = -1;
	private String hostTranslator = "";
	private String toleranceDistance = "0";
	private boolean grouping = true;
	private String title ="";
	private String abst ="";
	private List<String> keywordList ;
	private ServiceContactInfo contactInfo;
	private String fees ="";
	private String accessConstraints ="";
	private String exceptionMode = "";

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
	public Config(String id, List<RemoteServerInfo> remoteServer, String policyFile, String logFile) {
		super();
		this.id = id;
		this.remoteServer = remoteServer;
		this.policyFile = policyFile;
		this.logFile = logFile;
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
		this.title = title;
	}

	/**
	 * @return the title
	 */
	public String getTitle() {
		return title;
	}



	public void setContactInfo(ServiceContactInfo contactInfo) {
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
		this.exceptionMode = exceptionMode;
	}

	/**
	 * 
	 * @return the exception mode
	 */
	public String getExceptionMode() {
		return exceptionMode;
	}

}
