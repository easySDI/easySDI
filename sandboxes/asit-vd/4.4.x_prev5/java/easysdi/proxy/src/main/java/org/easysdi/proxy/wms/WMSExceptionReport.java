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

import java.io.IOException;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.ows.OWSExceptionReport;

/**
 * @author DEPTH SA
 *
 */
public abstract class WMSExceptionReport extends OWSExceptionReport {


	

	public void sendExceptionReport(HttpServletRequest request, HttpServletResponse response, String errorMessage, String code, String locator, int responseCode) throws IOException {
		StringBuffer sb = this.generateExceptionReport(request, response, errorMessage, code, locator, responseCode);
		sendHttpServletResponse(request, response,sb,"text/xml; charset=utf-8", responseCode);
	}
	
}
