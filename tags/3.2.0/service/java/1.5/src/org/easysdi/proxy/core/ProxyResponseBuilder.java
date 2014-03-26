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

import java.io.ByteArrayOutputStream;
import java.util.HashMap;
import java.util.Hashtable;

import org.jdom.Namespace;
import org.opengis.referencing.NoSuchAuthorityCodeException;

/**
 * @author DEPTH SA
 *
 */
public abstract class ProxyResponseBuilder {

	/**
	 * The calling servlet
	 */
	protected ProxyServlet servlet; 
	
	/**
	 * Last exception catched by one of the class methods
	 */
	protected Exception lastException;
	
	/**
	 * OWS namespace "http://www.opengis.net/ows/1.1"
	 */
	protected Namespace nsOWS ;
	
	/**
	 * Xlink namespace "http://www.w3.org/1999/xlink"
	 */
	protected Namespace nsXLINK;
	
	/**
	 * TExt message used for exception aggregation
	 */
	public static final String TEXT_SERVER_ALIAS = "Server %s returned : ";
	
	/**
	 * @param proxyServlet
	 */
	public ProxyResponseBuilder(ProxyServlet proxyServlet) {
		super();
		this.servlet = proxyServlet;
	}

	/**
	 * @param lastException
	 */
	public void setLastException(Exception lastException) {
		this.lastException = lastException;
	}

	/**
	 * @return the last exception catched by one the class methods
	 */
	public Exception getLastException() {
		return lastException;
	}
	
	/**
	 * Remove the unauthorized operations (defined in the policy) from the GetCapabilities response.
	 * Overwrite the OnlineResource link for all the authorized operations with the URL of he current servlet. 
	 * @param filePath : file path of the GetCapabilities response file of the master remote server
	 * @param href : Url of the current servlet (can be overwrite with the param 'HostTranslator' of the config.xml) @see ProxyServlet.getServletUrl()
	 * @return true if the GetCapabilities was succesfully updated, false otherwise
	 * If returns false, the caller should call getLastException() to get the catched exception.
	 */
	public abstract Boolean CapabilitiesOperationsFiltering (String filePath, String href );
	
	/**
	 * Remove the unauthorized Layers (defined in the policy) from the GetCapabilities responses.
	 * @param filePathList : List of the file path of the remote server responses
	 * @return true if the GetCapabilities was succesfully updated, false otherwise
	 * If returns false, the caller should call getLastException() to get the catched exception.
	 */
	public abstract Boolean CapabilitiesContentsFiltering (Hashtable<String, String> filePathList) throws NoSuchAuthorityCodeException; 
	
	/**
	 * Remove the unauthorized Layers (defined in the policy) from the GetCapabilities responses.
	 * @param filePathList : List of the file path of the remote server responses
	 * @return true if the GetCapabilities was succesfully updated, false otherwise
	 * If returns false, the caller should call getLastException() to get the catched exception.
	 */
	public abstract Boolean CapabilitiesContentsFiltering (HashMap<String, String> filePathList) throws NoSuchAuthorityCodeException; 
	
	/**
	 * Remove the unauthorized Layers (defined in the policy) from the GetCapabilities responses.
	 * @param filePathList : File paths of the remote server responses
	 * @return true if the GetCapabilities was succesfully updated, false otherwise
	 * If returns false, the caller should call getLastException() to get the catched exception.
	 */
	public abstract Boolean CapabilitiesContentsFiltering (HashMap<String, String> filePathList, String href) throws NoSuchAuthorityCodeException;
	
	/**
	 * Merge all the remote servers GetCapabilities response into one single file
	 * @param filePathList : File paths of the remote server responses 
	 * @return true if the GetCapabilities was succesfully merged, false otherwise
	 * If returns false, the caller should call getLastException() to get the catched exception.
	 */
	public abstract Boolean CapabilitiesMerging(HashMap<String, String> filePathList);
	
	/**
	 * 
	 * @param filePath :file path of the GetCapabilities response file of the master remote server
	 * @param href :: Url of the current servlet (can be overwrite with the param 'HostTranslator' of the config.xml) @see ProxyServlet.getServletUrl()
	 * @return true if the GetCapabilities was succesfully updated, false otherwise
	 * If returns false, the caller should call getLastException() to get the catched exception.
	 */
	public abstract Boolean CapabilitiesServiceMetadataWriting(String filePath, String href);
	
	/**
	 * Aggregate all the xml exceptions received from remote servers into one single xml exception (OGC compliant)
	 * @param remoteServerExceptionFiles : file paths of the remote exception
	 * @return the outputstream to send to the client
	 */
	public abstract ByteArrayOutputStream ExceptionAggregation(HashMap<String, String> remoteServerExceptionFiles);
}
