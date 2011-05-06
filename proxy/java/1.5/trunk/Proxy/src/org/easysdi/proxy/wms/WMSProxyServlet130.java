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
package org.easysdi.proxy.wms;

import java.util.Arrays;

import org.easysdi.proxy.wms.WMSProxyServlet;

/**
 * @author DEPTH SA
 *
 */
public class WMSProxyServlet130 extends WMSProxyServlet {

	/**
	 * 
	 */
	private static final long serialVersionUID = -675490104090297877L;

	/**
	 * 
	 */
	public WMSProxyServlet130() {
		super();
		ServiceSupportedOperations = Arrays.asList("GetCapabilities", "GetMap", "GetFeatureInfo", "GetLegendGraphic");
	}

}
