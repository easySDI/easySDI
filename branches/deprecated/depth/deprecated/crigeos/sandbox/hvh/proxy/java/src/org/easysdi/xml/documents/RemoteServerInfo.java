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

/**
 * @author Administrateur
 *
 */
public class RemoteServerInfo implements Serializable{
	/**
	 * 
	 */
	private static final long serialVersionUID = -1522579526391545420L;
	private String url;
	private String user;
	private String password;
	private String maxRecords;
	private String loginService;
	private String prefix; 
	private String transaction="ogc";
	private String alias;
	public Boolean isMaster = false;

	/**
	 * @param alias the alias to set
	 */
	public void setAlias(String alias) {
		this.alias = alias;
	}
	/**
	 * @return the alias
	 */
	public String getAlias() {
		return alias;
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
	public RemoteServerInfo(String alias, String url, String user, String password,
		String maxRecords, String loginService,String preifx,String transaction) {
	    super();
	    this.alias = alias;
	    this.url = url;
	    this.user = user;
	    this.password = password;
	    this.maxRecords = maxRecords;
	    if(loginService != null && !loginService.equalsIgnoreCase(""))
	    	this.loginService = loginService;
//Debug tb 02.07.2009
	    // Le probl�me avec cette ligne, c'est que WFSProxyServlet a �t� corrig� sur la base du bug existant
	    //-> getPrefix ne retournais rien, donc c'est un splite sur ":" qui s'en charge maintenant.
	    //Sauf les m�thodes de postraitement builtCapabilitiesXSLT et mergeDescribeFeatureTypes
	    this.prefix = preifx;
//Fin de Debug
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
