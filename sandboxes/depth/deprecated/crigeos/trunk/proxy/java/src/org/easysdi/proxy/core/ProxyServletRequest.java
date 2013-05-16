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

package org.easysdi.proxy.core;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;

import javax.servlet.http.HttpServletRequest;

import org.easysdi.proxy.exception.ProxyServletException;

public abstract class ProxyServletRequest {
	
	/**
	 * 
	 */
	protected HttpServletRequest request;
	
	/**
	 * 
	 */
	protected String urlParameters="";
	
	/**
	 * 
	 */
	protected String version;
	
	/**
	 * 
	 */
	protected String requestedVersion;
	
	/**
	 * 
	 */
	protected String operation;
	
	/**
	 * 
	 */
	protected String service;
	
	/**
	 * @return the request
	 */
	public HttpServletRequest getRequest() {
		return request;
	}
	
	/**
	 * @return the urlParameters
	 */
	public String getUrlParameters() {
		return urlParameters;
	}

	/**
	 * @return the operation
	 */
	public String getOperation() {
		return operation;
	}

	/**
	 * @return the requestedVersion
	 */
	public String getRequestedVersion() {
		return requestedVersion;
	}

	/**
	 * @return the service
	 */
	public String getService() {
		return service;
	}
	
	/**
	 * @return the version
	 */
	public String getVersion() {
		return version;
	}
	/**
	 * 
	 */
	public void setVersion(String  version) {
	    this.version=version;
	    urlParameters += "&VERSION="+version;
	}
	
	/**
	 * 
	 * @param req
	 * @throws Throwable
	 */
	public  ProxyServletRequest (HttpServletRequest req) throws Throwable{
		request= req;
		parseRequest();
	}
	
	/**
	 * 
	 * @throws Throwable
	 */
	protected  void parseRequest () throws Throwable {
		String method = request.getMethod();
		try {
			Method m = this.getClass().getMethod("parseRequest"+method, (Class[])null);
			m.invoke(this, (Object[]) null);
		} catch (ProxyServletException e){
			throw e;
		} catch (SecurityException e) {
			throw e;
		} catch (NoSuchMethodException e) {
			throw e;
		} catch (IllegalArgumentException e) {
			throw e;
		} catch (IllegalAccessException e) {
			throw e;
		} catch (InvocationTargetException e) {
			throw e;
		}
	}
}
