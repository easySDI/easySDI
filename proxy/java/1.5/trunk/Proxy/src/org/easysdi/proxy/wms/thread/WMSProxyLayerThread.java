package org.easysdi.proxy.wms.thread;

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map.Entry;
import java.util.TreeMap;
import java.util.Vector;
import java.util.logging.Level;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.configuration.ProxyLayer;
import org.easysdi.proxy.wms.WMSProxyServlet;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.geotools.geometry.jts.JTS;
import org.geotools.referencing.CRS;
import org.geotools.xml.DocumentFactory;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.operation.MathTransform;

import com.vividsolutions.jts.geom.Coordinate;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.geom.GeometryFactory;
import com.vividsolutions.jts.geom.IntersectionMatrix;
import com.vividsolutions.jts.io.WKTReader;

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
			
			HashMap<String, String> resultMap = new HashMap<String, String>();
			resultMap.put(filePath, remoteServer.getAlias());
			
			synchronized (servlet.wmsGetMapResponseFilePathMap) {
				servlet.wmsGetMapResponseFilePathMap.put(layers.firstKey(),resultMap );
			}
			servlet.dump("DEBUG", "Thread Layers group: " + "" + " work finished on server " + remoteServer.getUrl());
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			servlet.dump("ERROR", "Server " + remoteServer.getUrl() + " Layers group Thread " + "" + " :" + e.getMessage());
			e.printStackTrace();
		}
	}
}