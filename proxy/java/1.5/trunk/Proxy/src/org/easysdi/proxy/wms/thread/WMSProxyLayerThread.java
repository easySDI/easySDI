package org.easysdi.proxy.wms.thread;

import java.util.HashMap;
import java.util.Iterator;
import java.util.Map.Entry;
import java.util.TreeMap;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.core.ProxyRemoteServerResponse;
import org.easysdi.proxy.wms.WMSProxyServlet;
import org.easysdi.xml.documents.RemoteServerInfo;

public class WMSProxyLayerThread extends Thread {

	WMSProxyServlet servlet;
	String paramUrlBase;
	TreeMap<Integer, ProxyLayer> layers;
	TreeMap<Integer, String> styles;
	RemoteServerInfo remoteServer;
	HttpServletResponse resp;

	public WMSProxyLayerThread(	WMSProxyServlet servlet, 
								String paramUrlBase,
								TreeMap<Integer, ProxyLayer> layers,
								TreeMap<Integer, String> styles,
								RemoteServerInfo remoteServer, 
								HttpServletResponse resp) {
		this.servlet = servlet;
		this.paramUrlBase = paramUrlBase;
		this.layers = layers;
		this.styles = styles;
		this.remoteServer = remoteServer;
		this.resp = resp;
	}

	public void run() {
		try {
			servlet.dump("DEBUG", "Thread Layers group: " + "" + " work begin on server " + remoteServer.getUrl());

			Iterator<Entry<Integer, ProxyLayer>> itPL = layers.entrySet().iterator();
			String layerList ="";
			String styleList ="";
			while(itPL.hasNext()){
				Entry<Integer, ProxyLayer> layer = itPL.next();
				layerList += layer.getValue().getName() +",";
				styleList += styles.get(layer.getKey()) +",";
			}
			
			String layersUrl = "&LAYERS=" + layerList.substring(0, layerList.length()-1);
			String stylesUrl = "&STYLES=" + styleList.substring(0, styleList.length()-1);
			
			//Set TRANSPARENT to TRUE if not present
			if (paramUrlBase.toUpperCase().indexOf("TRANSPARENT=") == -1)
				paramUrlBase += "TRANSPARENT=TRUE&";
			
			String filePath = servlet.sendData("GET", remoteServer.getUrl(), paramUrlBase + layersUrl + stylesUrl);
			
			ProxyRemoteServerResponse response = new ProxyRemoteServerResponse(remoteServer.getAlias(), filePath);
			
			synchronized (servlet.wmsGetMapResponseFilePathMap) {
				servlet.wmsGetMapResponseFilePathMap.put(layers.firstKey(),response);
			}
			servlet.dump("DEBUG", "Thread Layers group: " + "" + " work finished on server " + remoteServer.getUrl());
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			servlet.dump("ERROR", "Server " + remoteServer.getUrl() + " Layers group Thread " + "" + " :" + e.getMessage());
			e.printStackTrace();
		}
	}
}