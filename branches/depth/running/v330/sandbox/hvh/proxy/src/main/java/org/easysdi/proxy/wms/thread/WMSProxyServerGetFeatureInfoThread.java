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

import java.util.Iterator;
import java.util.List;
import java.util.TreeMap;
import java.util.Vector;
import java.util.Map.Entry;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.core.ProxyRemoteServerResponse;
import org.easysdi.proxy.domain.SdiPhysicalservice;
import org.easysdi.proxy.wms.WMSProxyServlet;
import org.easysdi.xml.documents.RemoteServerInfo;


/**
 * @author DEPTH SA
 *
 */
public class WMSProxyServerGetFeatureInfoThread extends Thread {

    List<WMSProxyLayerThread> layerThreadList = new Vector<WMSProxyLayerThread>();

    WMSProxyServlet servlet;
    String paramUrlBase;
    TreeMap<Integer, ProxyLayer> queryLayers;
    TreeMap<Integer, ProxyLayer> layers;
    TreeMap<Integer, String> styles;
    SdiPhysicalservice physicalService;
    HttpServletResponse resp;

    public WMSProxyServerGetFeatureInfoThread(	WMSProxyServlet servlet, 
	    String paramUrlBase,
	    TreeMap<Integer, ProxyLayer> queryLayers,
	    TreeMap<Integer, ProxyLayer> layers,
	    TreeMap<Integer, String> styles,
	    SdiPhysicalservice physicalService, 
	    HttpServletResponse resp) {
	this.servlet = servlet;
	this.paramUrlBase = paramUrlBase;
	this.queryLayers = queryLayers;
	this.layers = layers;
	this.styles = styles;
	this.physicalService = physicalService;
	this.resp = resp;
    }

    public void run() {

	try {
	    Iterator<Entry<Integer, ProxyLayer>> itPL = layers.entrySet().iterator();
	    String layerList ="";
	    String styleList ="";
	    while(itPL.hasNext()){
		Entry<Integer, ProxyLayer> layer = itPL.next();
		layerList += layer.getValue().getPrefixedName() +",";
		styleList += styles.get(layer.getKey()) +",";
	    }

	    Iterator<Entry<Integer, ProxyLayer>> itQPL = queryLayers.entrySet().iterator();
	    String queryLayerList ="";
	    while(itQPL.hasNext()){
		Entry<Integer, ProxyLayer> layer = itQPL.next();
		queryLayerList += layer.getValue().getPrefixedName() +",";
	    }

	    String queryLayersUrl = "&QUERY_LAYERS=" + queryLayerList.substring(0, queryLayerList.length()-1);
	    String layersUrl = "&LAYERS=" + layerList.substring(0, layerList.length()-1);
	    String stylesUrl = "&STYLES=" + styleList.substring(0, styleList.length()-1);

	    String filePath = servlet.sendData("GET", physicalService.getResourceurl(), paramUrlBase + queryLayersUrl + "&" + layersUrl + "&" + stylesUrl);

	    ProxyRemoteServerResponse response = new ProxyRemoteServerResponse(physicalService.getAlias(), filePath);

	    synchronized (servlet.wmsGetMapResponseFilePathMap) {
		servlet.wmsGetFeatureInfoResponseFilePathMap.put(layers.firstKey(),response );
	    }
	} catch (Exception e) {
	    resp.setHeader("easysdi-proxy-error-occured", "true");
	    servlet.logger.error("Server Thread " + physicalService.getResourceurl() + " :" + e.getMessage());
	}
    }
}
