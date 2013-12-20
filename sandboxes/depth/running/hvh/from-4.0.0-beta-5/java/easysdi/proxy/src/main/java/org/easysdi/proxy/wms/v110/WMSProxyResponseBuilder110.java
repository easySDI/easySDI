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
package org.easysdi.proxy.wms.v110;

import java.util.HashMap;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.wms.WMSProxyResponseBuilder;
import org.opengis.referencing.NoSuchAuthorityCodeException;

/**
 * @author DEPTH SA
 *
 */
public class WMSProxyResponseBuilder110 extends WMSProxyResponseBuilder {

	/**
	 * @param proxyServlet
	 */
	public WMSProxyResponseBuilder110(ProxyServlet proxyServlet) {
		super(proxyServlet);
	}

	@Override
	public Boolean CapabilitiesContentsFiltering(
			HashMap<String, String> filePathList)
			throws NoSuchAuthorityCodeException {
		// TODO Auto-generated method stub
		return null;
	}
}
