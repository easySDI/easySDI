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

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;
import java.util.Map.Entry;
import java.util.TreeMap;
import java.util.Vector;
import java.util.logging.Level;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.domain.SdiPhysicalservice;
import org.easysdi.proxy.wms.WMSProxyServlet;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.geotools.geometry.jts.ReferencedEnvelope;
import org.geotools.util.GeometryConverterFactory;
import org.geotools.xml.DocumentFactory;
import org.opengis.referencing.FactoryException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;

import com.vividsolutions.jts.geom.Envelope;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.io.WKTReader;

/**
 * @author DEPTH SA
 *
 */
public class WMSProxyServerGetMapThread extends Thread {

    List<WMSProxyLayerThread> layerThreadList = new Vector<WMSProxyLayerThread>();

    WMSProxyServlet servlet;
    String paramUrlBase;
    TreeMap<Integer, ProxyLayer> layers;
    TreeMap<Integer, String> styles;
    SdiPhysicalservice physicalService;
    HttpServletResponse resp;

    public WMSProxyServerGetMapThread(	WMSProxyServlet servlet, 
	    String paramUrlBase,
	    TreeMap<Integer, ProxyLayer> layers,
	    TreeMap<Integer, String> styles,
	    SdiPhysicalservice physicalService, 
	    HttpServletResponse resp) {
	this.servlet = servlet;
	this.paramUrlBase = paramUrlBase;
	this.layers = layers;
	this.styles = styles;
	this.physicalService = physicalService;
	this.resp = resp;
    }

    public void run() {

	try {
	    servlet.logger.trace( "Thread Server: " + physicalService.getResourceurl() + " work begin");

	    //Layer order
	    List<TreeMap<Integer, ProxyLayer>> layerGroupList = new ArrayList<TreeMap<Integer,ProxyLayer>>() ;
	    TreeMap<Integer, ProxyLayer> layerGroup = new TreeMap<Integer, ProxyLayer>();
	    Integer previous = -1;
	    Iterator<Integer> itKey = layers.keySet().iterator();
	    while (itKey.hasNext()){
		Integer key = itKey.next();
		if(previous == -1)
		{
		    layerGroup.put(key, layers.get(key));
		}else if(key == previous +1){
		    layerGroup.put(key, layers.get(key));
		}else{
		    layerGroupList.add(layerGroup);
		    layerGroup = new TreeMap<Integer, ProxyLayer>();
		    layerGroup.put(key, layers.get(key));
		}
		previous = key;
	    }
	    layerGroupList.add(layerGroup);


	    //request BBOX to geometry
	    //String requestBbox = servlet.getProxyRequest().getBbox();
	    CoordinateReferenceSystem requestCRS = servlet.getProxyRequest().getCoordinateReferenceSystem();
	    ReferencedEnvelope requestEnvelope = new ReferencedEnvelope(servlet.getProxyRequest().getX1(), 
		    servlet.getProxyRequest().getX2(), 
		    servlet.getProxyRequest().getY1(),
		    servlet.getProxyRequest().getY2(), 
		    requestCRS);
	    GeometryConverterFactory factory = new GeometryConverterFactory();
	    Geometry requestGeometry = (Geometry) factory.createConverter( Envelope.class, Geometry.class, null )
	    .convert( requestEnvelope , Geometry.class);

	    Iterator<TreeMap<Integer, ProxyLayer>> itLGL = layerGroupList.iterator();

	    while (itLGL.hasNext()){
		TreeMap<Integer, ProxyLayer> continuousLayers = itLGL.next();
		Geometry previousPolygonFilter = null;
		//Check if the filter on layer covered the requested BBOX
		//If not, the layer is removed from the request
		//In the same time, check if all the layer in a group have the same geographic filter
		boolean isGeographicFilterEqual = true;
		Iterator<Entry<Integer, ProxyLayer>> itL = continuousLayers.entrySet().iterator();
		List<Integer> layerIndexToRemove = new ArrayList<Integer> ();
		while (itL.hasNext()){
		    Entry<Integer, ProxyLayer> layer = itL.next();

		    //Compare the filter with the requested BBOX
		    String filter = servlet.getLayerFilter(physicalService.getResourceurl(), layer.getValue().getPrefixedName());
		    if(filter == null || filter.length() == 0){
			//No filter define on the layer : the layer has to be requested
			continue;
		    }
		    InputStream bis = new ByteArrayInputStream(filter.getBytes());
		    Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);
		    WKTReader wktReader = new WKTReader();
		    Geometry polygonFilter = wktReader.read(object.toString());

		    if(	polygonFilter.crosses(requestGeometry) ||
			    requestGeometry.crosses(polygonFilter) ||
			    polygonFilter.touches(requestGeometry) ||
			    requestGeometry.touches(polygonFilter) ||
			    polygonFilter.overlaps(requestGeometry)||
			    requestGeometry.overlaps(polygonFilter)||
			    polygonFilter.intersects(requestGeometry)||
			    requestGeometry.intersects(polygonFilter)||
			    polygonFilter.coveredBy(requestGeometry) ||
			    polygonFilter.covers(requestGeometry)
		    ){
			//Filter crosses the requested BBOX : the layer has to be requested
			//Check if the current filter is equal to the previous one
			if(previousPolygonFilter != null){
			    if(!polygonFilter.equalsExact(previousPolygonFilter))
				isGeographicFilterEqual = false;
			}
			previousPolygonFilter = polygonFilter;
		    }else{
			//Filter do not crossed the requested BBOX : the layer has not to be requested
			layerIndexToRemove.add(layer.getKey());
		    }
		}

		//Remove from the layers list those who are not covered by the requested BBOX (due to policy geographix filter restriction)
		for (int i = 0 ; i < layerIndexToRemove.size();i++)
		{
		    continuousLayers.remove(layerIndexToRemove.get(i));
		}

		if(continuousLayers.size()==1){
		    //Send the request
		    servlet.logger.trace("WMSProxyServerGetMapThread send request multiLayer to thread server " + physicalService.getResourceurl());
		    WMSProxyLayerThread th = new WMSProxyLayerThread(servlet,paramUrlBase,continuousLayers,styles, physicalService,resp);
		    th.start();
		    layerThreadList.add(th);
		}else if(continuousLayers.size()>1){
		    if(isGeographicFilterEqual){
			//Send all the layer in the same request : layers are in the same order than in the request and the geographic filter are the same
			servlet.logger.trace("WMSProxyServerGetMapThread send request multiLayer to thread server " + physicalService.getResourceurl());
			WMSProxyLayerThread th = new WMSProxyLayerThread(servlet,paramUrlBase,continuousLayers,styles, physicalService,resp);
			th.start();
			layerThreadList.add(th);
		    }else{
			//Layers have to be requested separatly : geographic filter are not the same
			Iterator<Entry<Integer, ProxyLayer>> itLToS = continuousLayers.entrySet().iterator();
			while (itLToS.hasNext()){
			    Entry<Integer, ProxyLayer> layer = itLToS.next();
			    TreeMap<Integer, ProxyLayer> temp = new TreeMap<Integer, ProxyLayer>();
			    temp.put(layer.getKey(), layer.getValue());
			    servlet.logger.trace("WMSProxyServerGetMapThread send request singleLayer to thread server " + physicalService.getResourceurl());
			    WMSProxyLayerThread th = new WMSProxyLayerThread(servlet,paramUrlBase,temp,styles, physicalService,resp);
			    th.start();
			    layerThreadList.add(th);
			}
		    }
		}
	    }

	    //Wait for thread answer
	    for (int i = 0; i < layerThreadList.size(); i++) {
		layerThreadList.get(i).join();
	    }

	    servlet.logger.trace("Thread Server: " + physicalService.getResourceurl() + " work finished");
	} catch (FactoryException e){
	    //CRS can not be determine with the given SRS code
	    resp.setHeader("easysdi-proxy-error-occured", "true");
	    servlet.logger.error( "Server Thread " + physicalService.getResourceurl() + " :" + e.getMessage());
	} catch (Exception e) {
	    resp.setHeader("easysdi-proxy-error-occured", "true");
	    servlet.logger.error( "Server Thread " + physicalService.getResourceurl() + " :" + e.getMessage());
	}			
    }
}
