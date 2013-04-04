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

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.ows.OWSExceptionReport;

/**
 * @author DEPTH SA
 *
 */
public abstract class WMSExceptionReport extends OWSExceptionReport {


    public StringBuffer getServiceExceptionBody (String errorMessage,String code, String locator){
		StringBuffer sb = new StringBuffer();
		sb.append("\n\t<ServiceException code=\"");
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
		    sb.append("\n\t"+errorMessage);
		}
		sb.append("\n\t</ServiceException>");
	
		return sb;
    }

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.ows.OWSExceptionReport#sendExceptionReport(javax.servlet.http.HttpServletResponse, java.lang.String, java.lang.String, java.lang.String)
	 */
	@Override
	public void sendExceptionReport(HttpServletResponse response,
			String errorMessage, String code, String locator)
			throws IOException {
		// TODO Auto-generated method stub
		
	}

}
