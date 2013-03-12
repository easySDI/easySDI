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
package org.easysdi.proxy.wms.v130;

import java.io.IOException;

import org.easysdi.proxy.wms.WMSExceptionReport;

/**
 * @author DEPTH SA
 *
 */
public class WMSExceptionReport130 extends WMSExceptionReport {

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.ows.OWSExceptionReport#generateExceptionReport(java.lang.String, java.lang.String, java.lang.String, java.lang.String)
	 */
	public StringBuffer generateExceptionReport(String errorMessage,String code, String locator) throws IOException {
		
		StringBuffer sb = new StringBuffer();
		sb.append("<?xml version='1.0' encoding='utf-8'?>");
		sb.append("\n<ServiceExceptionReport xmlns=\"http://www.opengis.net/ogc\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengis.net/ogc http://schemas.opengis.net/wms/1.3.0/exceptions_1_3_0.xsd\" ");
		sb.append("version=\"1.3.0\">");
		sb.append(this.getServiceExceptionBody(errorMessage, code, locator));
		sb.append("\n</ServiceExceptionReport>");
		
		return sb;
	}	
}
