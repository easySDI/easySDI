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
package org.easysdi.proxy.wms.v111;

import java.util.HashMap;
import java.util.Hashtable;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.wms.WMSProxyResponseBuilder;

/**
 * @author DEPTH SA
 *
 */
public class WMSProxyResponseBuilder111 extends WMSProxyResponseBuilder {

	/**
	 * @param proxyServlet
	 */
	public WMSProxyResponseBuilder111(ProxyServlet proxyServlet) {
		super(proxyServlet);
		// TODO Auto-generated constructor stub
	}
	
	@Override
	public Boolean CapabilitiesOperationsFiltering(String filePath, String href) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public Boolean CapabilitiesContentsFiltering(
			Hashtable<String, String> filePathList) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public Boolean CapabilitiesMerging(Hashtable<String, String> filePathList) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public Boolean CapabilitiesServiceIdentificationWriting(String filePath,
			String href) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public Boolean CapabilitiesContentsFiltering(
			HashMap<String, String> filePathList) {
		// TODO Auto-generated method stub
		return null;
	}

}
