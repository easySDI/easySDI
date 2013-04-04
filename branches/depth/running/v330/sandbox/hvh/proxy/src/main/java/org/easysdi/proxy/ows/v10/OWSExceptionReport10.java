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
package org.easysdi.proxy.ows.v10;

import java.io.IOException;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.ows.OWSExceptionReport;


/**
 * @author DEPTH SA
 *
 */
public class OWSExceptionReport10 extends OWSExceptionReport {

	public StringBuffer generateExceptionReport(String errorMessage,String code, String locator) throws IOException {
			return generateExceptionReport(errorMessage, code, locator, "1.0.0");
	}

	public StringBuffer generateExceptionReport(String errorMessage,String code, String locator, String version) throws IOException {
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8'?>\n");
		
		sb.append("\n<ExceptionReport xmlns=\"http://www.opengis.net/ows\" ");
		sb.append("xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ");
		sb.append("xsi:schemaLocation=\"http://www.opengis.net/ows owsExceptionReport.xsd\" version=\""+version+"\">");
		sb.append("\n\t<Exception exceptionCode=\"");
		sb.append(code);
		sb.append("\"");
		if(locator != null && locator != "" )
		{
			sb.append(" locator=\"");
			sb.append(locator);
			sb.append("\"");
		}
		sb.append(">");
		if( errorMessage != null && errorMessage.length()!= 0)
		{
			sb.append("\n\t\t<ExceptionText>");
			sb.append(errorMessage);
			sb.append("</ExceptionText>");
		}
		sb.append("\n\t</Exception>");
		sb.append("\n</ExceptionReport>");
		
		return sb;
	}

	@Override
	public void sendExceptionReport(HttpServletResponse response,
			String errorMessage, String code, String locator)
			throws IOException {
		// TODO Auto-generated method stub
		
	}

}
