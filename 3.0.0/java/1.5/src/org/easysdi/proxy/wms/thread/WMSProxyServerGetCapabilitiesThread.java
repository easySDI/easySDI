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
package org.easysdi.proxy.wms.thread;

import java.util.Vector;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.wms.*;
import org.easysdi.xml.documents.RemoteServerInfo;

/**
 * @author DEPTH SA
 *
 */
public class WMSProxyServerGetCapabilitiesThread extends Thread {

    protected Vector<String> serverFilePathList = new Vector<String>();
    protected Vector<String> serverLayerFilePathList = new Vector<String>();

    WMSProxyServlet servlet;
    String paramUrlBase;
    RemoteServerInfo remoteServerInfo;
    HttpServletResponse resp;


    public WMSProxyServerGetCapabilitiesThread( WMSProxyServlet servlet, 
	    String paramUrlBase,
	    RemoteServerInfo remoteServer, 
	    HttpServletResponse resp) {
	this.paramUrlBase = paramUrlBase;
	this.resp = resp;
	this.servlet = servlet;
	this.remoteServerInfo = remoteServer;
    }

    public void run() {

	try {
	    servlet.logger.trace("Thread Server: " + remoteServerInfo.getUrl() + " work begin");

	    String filePath = servlet.sendData(servlet.getProxyRequest().getRequest().getMethod(), remoteServerInfo.getUrl(), paramUrlBase);

	    synchronized (servlet.wmsGetCapabilitiesResponseFilePathMap) {
		servlet.logger.trace("requestPreTraitementGET save response from thread server " + remoteServerInfo.getUrl());
		servlet.wmsGetCapabilitiesResponseFilePathMap.put(remoteServerInfo.getAlias(), filePath);
	    }

	} catch (Exception e) {
	    resp.setHeader("easysdi-proxy-error-occured", "true");
	    servlet.logger.error("Server Thread " + remoteServerInfo.getUrl() + " :" + e.getMessage());
	}


    }

}
