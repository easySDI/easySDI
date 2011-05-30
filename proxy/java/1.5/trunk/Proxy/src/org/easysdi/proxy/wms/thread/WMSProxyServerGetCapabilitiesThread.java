/**
 * 
 */
package org.easysdi.proxy.wms.thread;

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.util.List;
import java.util.Vector;
import java.util.logging.Level;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.wms.*;
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

/**
 * @author Helene
 *
 */
public class WMSProxyServerGetCapabilitiesThread extends Thread {

	protected Vector<String> serverFilePathList = new Vector<String>();
	protected Vector<String> serverLayerFilePathList = new Vector<String>();

	WMSProxyServlet servlet;
	String paramUrlBase;
	RemoteServerInfo remoteServerInfo;
	HttpServletResponse resp;
	

	// **************************************************************************************
	public WMSProxyServerGetCapabilitiesThread( WMSProxyServlet servlet, 
												String paramUrlBase,
												RemoteServerInfo remoteServer, 
												HttpServletResponse resp) {
		this.paramUrlBase = paramUrlBase;
		this.resp = resp;
		this.servlet = servlet;
		this.remoteServerInfo = remoteServer;
	}

	// **************************************************************************************
	public void run() {

		try {
			servlet.logger.trace("Thread Server: " + remoteServerInfo.getUrl() + " work begin");
			
			String filePath = servlet.sendData("GET", remoteServerInfo.getUrl(), paramUrlBase);

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
