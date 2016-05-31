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
package org.easysdi.proxy.wmts.thread;

import java.io.IOException;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.domain.SdiPhysicalservice;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.wmts.WMTSProxyServlet;

/**
 * @author DEPTH SA
 *
 */
public class WMTSProxyServerGetCapabilitiesThread extends Thread {

	String paramUrl;
	SdiPhysicalservice physicalService;
	HttpServletResponse resp;
	WMTSProxyServlet servlet;

	public WMTSProxyServerGetCapabilitiesThread(WMTSProxyServlet servlet,String pParamUrl, SdiPhysicalservice physicalService, HttpServletResponse response) {
		paramUrl = pParamUrl;
		this.physicalService = physicalService;
		resp = response;
		this.servlet = servlet;
	}
	
	public void run() {
		try {
			
			servlet.logger.trace( "Thread Server: " + physicalService.getResourceurl() + " work begin");
			String filePath = servlet.sendData(servlet.getProxyRequest().getRequest().getMethod(), physicalService.getResourceurl(), paramUrl);
			synchronized (servlet.wmtsGetCapabilitiesResponseFilePathMap) {
				servlet.logger.trace("WMTSProxyServerGetCapabilitiesThread save response from thread server " + physicalService.getResourceurl());
				servlet.wmtsGetCapabilitiesResponseFilePathMap.put(physicalService.getAlias(), filePath);
			}
			servlet.logger.trace( "Thread Server: " + physicalService.getResourceurl() + " work finished");
		}
		catch (Exception e)
		{
			e.printStackTrace();
			resp.setHeader("easysdi-proxy-error-occured", "true");
			servlet.logger.error( "Server Thread " + physicalService.getResourceurl()+ " :" + e.getMessage());
			try {
				servlet.owsExceptionReport.sendExceptionReport(servlet.request, servlet.response, OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY, OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
			} catch (IOException e1) {
				servlet.logger.error( e1.toString());
				e1.printStackTrace();
			}
			return;
		}
	}
}
