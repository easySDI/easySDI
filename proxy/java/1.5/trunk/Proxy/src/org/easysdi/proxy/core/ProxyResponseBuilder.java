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

/**
 * @author DEPTH SA
 *
 */
public abstract class ProxyResponseBuilder {

	protected ProxyServlet servlet; 
	protected Exception lastException;
	protected Namespace nsOWS ;
	protected Namespace nsXLINK;
	public static final String TEXT_SERVER_ALIAS = "Server %s returns : ";
	
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
	 * @return
	 */
	public Exception getLastException() {
		return lastException;
	}
	
	public abstract Boolean CapabilitiesOperationsFiltering (String filePath, String href );
	public abstract Boolean CapabilitiesContentsFiltering (Hashtable<String, String> filePathList);
	public abstract Boolean CapabilitiesContentsFiltering (HashMap<String, String> filePathList);
	public abstract Boolean CapabilitiesMerging(Hashtable<String, String> filePathList);
	public abstract Boolean CapabilitiesMerging(HashMap<String, String> filePathList);
	public abstract Boolean CapabilitiesServiceIdentificationWriting(String filePath, String href);
	public abstract ByteArrayOutputStream ExceptionAggregation(HashMap<String, String> remoteServerExceptionFiles);
}
