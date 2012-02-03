/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
package ch.depth.xml.documents;

/**
 * @author Administrateur
 *
 */
public class RemoteServerInfo {
	private String url;
	private String user;
	private String password;
	private String maxRecords;
	private String loginService;
	private String prefix; 
	private String transaction="ogc";
	private String insertServiceUrl;
	private String deleteServiceUrl;
	private String searchServiceUrl;
	
	public String getInsertServiceUrl() {
	    return insertServiceUrl;
	}
	public void setInsertServiceUrl(String insertServiceUrl) {
	    this.insertServiceUrl = insertServiceUrl;
	}
	public String getDeleteServiceUrl() {
	    return deleteServiceUrl;
	}
	public void setDeleteServiceUrl(String deleteServiceUrl) {
	    this.deleteServiceUrl = deleteServiceUrl;
	}
	public String getSearchServiceUrl() {
	    return searchServiceUrl;
	}
	public void setSearchServiceUrl(String searchServiceUrl) {
	    this.searchServiceUrl = searchServiceUrl;
	}
	public String getTransaction() {
	    return transaction;
	}
	public void setTransaction(String transaction) {
	    this.transaction = transaction;
	}
	public String getMaxRecords() {
	    return maxRecords;
	}
	public void setMaxRecords(String maxRecords) {
	    this.maxRecords = maxRecords;
	}
	/**
	 * @param url
	 * @param user
	 * @param password
	 * @param maxRecords
	 */
	public RemoteServerInfo(String url, String user, String password,
		String maxRecords, String loginService,String preifx,String transaction) {
	    super();
	    this.url = url;
	    this.user = user;
	    this.password = password;
	    this.maxRecords = maxRecords;
	    this.loginService = loginService;
	    this.prefix = prefix;
	    this.transaction = transaction;
	}
	
	public String getLoginService() {
	    return loginService;
	}
	public void setLoginService(String loginService) {
	    this.loginService = loginService;
	}
	public String getUrl() {
	    return url;
	}
	public void setUrl(String url) {
	    this.url = url;
	}
	public String getUser() {
	    return user;
	}
	public void setUser(String user) {
	    this.user = user;
	}
	public String getPassword() {
	    return password;
	}
	public void setPassword(String password) {
	    this.password = password;
	}
	public String getPrefix() {
	    if (prefix == null)return "";
	    return prefix;
	}
	public void setPrefix(String prefix) {
	    this.prefix = prefix;
	}
	
	
	
} 
