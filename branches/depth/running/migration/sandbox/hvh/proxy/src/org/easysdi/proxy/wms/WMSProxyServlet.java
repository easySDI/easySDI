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

import java.awt.AlphaComposite;
import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.Transparency;
import java.awt.geom.Rectangle2D;
import java.awt.image.BufferedImage;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.lang.reflect.Constructor;
import java.lang.reflect.Method;
import java.net.URI;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.TreeMap;
import java.util.Vector;
import java.util.Map.Entry;
import java.util.logging.Level;

import javax.imageio.ImageIO;
import javax.imageio.ImageWriter;
import javax.imageio.stream.MemoryCacheImageOutputStream;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.ParserConfigurationException;

import org.easysdi.jdom.filter.ElementFilter;
import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.core.ProxyRemoteServerResponse;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.wms.thread.WMSProxyServerGetCapabilitiesThread;
import org.easysdi.proxy.wms.thread.WMSProxyServerGetFeatureInfoThread;
import org.easysdi.proxy.wms.thread.WMSProxyServerGetMapThread;
import org.easysdi.proxy.wms.WMSProxyResponseBuilder;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.geotools.data.ows.CRSEnvelope;
import org.geotools.feature.AttributeType;
import org.geotools.feature.AttributeTypeFactory;
import org.geotools.feature.FeatureType;
import org.geotools.feature.FeatureTypes;
import org.geotools.feature.GeometryAttributeType;
import org.geotools.feature.type.GeometricAttributeType;
import org.geotools.geometry.jts.JTS;
import org.geotools.geometry.jts.ReferencedEnvelope;
import org.geotools.referencing.CRS;
import org.geotools.referencing.NamedIdentifier;
import org.geotools.renderer.lite.RendererUtilities;
import org.geotools.xml.DocumentFactory;
import org.integratedmodelling.geospace.gis.FeatureRasterizer;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.opengis.referencing.NoSuchAuthorityCodeException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.operation.MathTransform;
import org.xml.sax.SAXException;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.io.WKTReader;


/**
 * 
 * @author DEPTH SA
 */
@SuppressWarnings("serial")
public class WMSProxyServlet extends ProxyServlet {
	/**
	 * Fill by the WMSProxyServletGetMapThread with
	 * <index of layer in the request,<path,alias>>
	 */
	public TreeMap<Integer, ProxyRemoteServerResponse> wmsGetMapResponseFilePathMap = new TreeMap<Integer, ProxyRemoteServerResponse>();

	/**
	 * Fill by the WMSProxyServletGetFEatureInfoThread with
	 * <index of layer in the request,<path,alias>>
	 */
	public TreeMap<Integer, ProxyRemoteServerResponse> wmsGetFeatureInfoResponseFilePathMap = new TreeMap<Integer, ProxyRemoteServerResponse>();

	/**
	 * Fill by the WMSProxyServletGetCapabilitiesThread with
	 * <alias,path>
	 */
	public HashMap<String, String> wmsGetCapabilitiesResponseFilePathMap = new HashMap<String, String>();

	/**
	 * Fill by the GetLegendGraphic with
	 * <alias,path>
	 */
	public HashMap<String, String> wmsGetLegendGraphicResponseFilePathMap = new HashMap<String, String>();

	/**
	 * 
	 */
	protected WMSProxyResponseBuilder docBuilder;

	/**
	 * Protocol version of the remote server response.
	 * A GetCapabilities response can be made in a protocol version different from the one requested.
	 * @see OGC WMS Server Implementation specification
	 */
	private String responseVersion = null;

	/**
	 * @return the proxyRequest
	 */
	public WMSProxyServletRequest getProxyRequest() {
		return (WMSProxyServletRequest)proxyRequest;
	}

	/**
	 * @param responseVersion the responseVersion to set
	 */
	public void setResponseVersion(String responseVersion) {
		this.responseVersion = responseVersion;
	}

	/**
	 * @return the responseVersion
	 */
	public String getResponseVersion() {
		return responseVersion;
	}

	/**
	 * Constructor
	 */
	public WMSProxyServlet ()
	{
		super();
		ServiceOperations = Arrays.asList( "GetCapabilities", "GetMap", "GetFeatureInfo", "DescribeLayer", "GetLegendGraphic", "PutStyles", "GetStyles" );
	}

	/**
	 * 
	 */
	protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp) {
		this.requestPreTreatmentGET(req, resp);
	}

	/**
	 * 
	 */
	protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp) {
		try {
			//Generate OGC exception and send it to the client if current operation is not supported by the current version of the EasySDI proxy
			if(!isOperationSupportedByProxy(getProxyRequest().getOperation()))
			{
				logger.error(OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED);
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED,OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED,"REQUEST");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}
			//Generate OGC exception and send it to the client if current operation is not allowed by the loaded policy
			if(!isOperationAllowedByPolicy(getProxyRequest().getOperation()))
			{
				logger.error( OWSExceptionReport.TEXT_OPERATION_NOT_ALLOWED);
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_OPERATION_NOT_ALLOWED,OWSExceptionReport.CODE_OPERATION_NOT_ALLOWED,"REQUEST");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}


			Method preTreatmentMethod = this.getClass().getMethod("requestPreTreatment"+getProxyRequest().getOperation(), new Class [] {Class.forName ("javax.servlet.http.HttpServletRequest"), Class.forName ("javax.servlet.http.HttpServletResponse")});
			preTreatmentMethod.invoke(this ,new Object[] {req,resp});

		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error( configuration.getServletClass() + ".requestPreTreatmentGET : ", e);
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error( OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
		}
	}

	/**
	 * @param req
	 * @param resp
	 */
	public void requestPreTreatmentGetCapabilities (HttpServletRequest req, HttpServletResponse resp){
		try{
			List<WMSProxyServerGetCapabilitiesThread> serverThreadList = new Vector<WMSProxyServerGetCapabilitiesThread>();
			Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();
			Iterator<Entry<String, RemoteServerInfo>> it =remoteServersTable.entrySet().iterator();

			while(it.hasNext())
			{
				String requestContent=null;
				if(getProxyRequest().getRequest().getMethod().equalsIgnoreCase("GET"))
					requestContent = getProxyRequest().getUrlParameters();
				else
					requestContent = getProxyRequest().getBodyRequest().toString();
				WMSProxyServerGetCapabilitiesThread s = new WMSProxyServerGetCapabilitiesThread(this,
						requestContent, 
						it.next().getValue(), 
						resp);

				s.start();
				serverThreadList.add(s);
			}

			// Wait for thread results
			for (int i = 0; i < serverThreadList.size(); i++) {
				serverThreadList.get(i).join();
			}

			if (wmsGetCapabilitiesResponseFilePathMap.size() > 0) {
				logger.trace("requestPreTreatmentGetCapabilities begin transform");
				transformGetCapabilities(req, resp);
				logger.trace("requestPreTreatmentGetCapabilities end transform");
			} else {
				StringBuffer out;
				try {
					logger.error(OWSExceptionReport.TEXT_NO_RESULT_RECEIVED_BY_PROXY);
					out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_NO_RESULT_RECEIVED_BY_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				} catch (IOException e) {
					logger.error( configuration.getServletClass() + ".requestPreTreatmentGetCapabilities : ", e);
				}
				return;
			}
		}catch(Exception e){
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error( configuration.getServletClass() + ".requestPreTreatmentGetCapabilities : ", e);
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error( OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
			return;
		}
	}

	/**
	 * @param req
	 * @param resp
	 */
	@SuppressWarnings("unchecked")
	public void requestPreTreatmentGetMap (HttpServletRequest req, HttpServletResponse resp){
		try{
			//Check the LAYERS parameter validity
			if(((WMSProxyServletRequest)getProxyRequest()).getLayers() == null || ((WMSProxyServletRequest)getProxyRequest()).getLayers().equalsIgnoreCase(""))
			{
				logger.error( "LAYERS "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE);
				StringBuffer out = owsExceptionReport.generateExceptionReport("LAYERS "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE,OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE,"LAYERS");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			//Check if the WIDTH and HEIGHT parameters value are allowed by the policy
			if (!isSizeInTheRightRange(Integer.parseInt(((WMSProxyServletRequest)getProxyRequest()).getWidth()), Integer.parseInt(((WMSProxyServletRequest)getProxyRequest()).getHeight())))
			{
				logger.error(OWSExceptionReport.TEXT_INVALID_PARAMETER_VALUE+" Request ImageSize out of bounds, see the policy definition.");
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_PARAMETER_VALUE+" Request ImageSize out of bounds, see the policy definition.",OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"WIDTH");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			//Check the validity of the GetMapRequest :
			//This processing is out of this method because it is also used in the GetFeatureInfo request
			List <Object> r = checkGetMapRequestValidity(req,resp);

			//If the result is null, ann exception has already been sent to the client 
			if (r == null)
				return;

			//The results 
			ArrayList <String> remoteServerToCall = (ArrayList <String>)r.get(0);
			TreeMap<Integer,ProxyLayer> layerTableToKeep = (TreeMap<Integer,ProxyLayer>) r.get(1);
			TreeMap<Integer,String> layerStyleMap = (TreeMap <Integer,String>) r.get(2);

			//No layer to keep from the request according to policy right (scale limitation)
			//Return an empty image
			if(layerTableToKeep.size() == 0){
				logger.debug("WMSProxyServlet.requestPreTreatmentGetMap : no layers allowed, generate an empty image");
				BufferedImage imgOut = generateEmptyImage(getProxyRequest().getWidth(), getProxyRequest().getHeight(), getProxyRequest().getFormat(), true, resp);
				Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(getProxyRequest().getFormat());
				ByteArrayOutputStream out = new ByteArrayOutputStream();
				if (iter.hasNext()) {
					ImageWriter writer = (ImageWriter) iter.next();
					writer.setOutput(javax.imageio.ImageIO.createImageOutputStream(out));
					writer.write(imgOut);
					writer.dispose();
				}
				sendHttpServletResponse(req, resp,out,getProxyRequest().getFormat(), HttpServletResponse.SC_OK);
				return;
			}

			//Send the request
			if(req.getMethod().equalsIgnoreCase("GET")){
				if(!requestSendingGetMapGET(req,resp,remoteServerToCall,layerTableToKeep,layerStyleMap))
					return;
			}else{
				if(!requestSendingGetMapPOST(req,resp,remoteServerToCall,layerTableToKeep,layerStyleMap))
					return;
			}
			//			Method m = this.getClass().getMethod("requestSendingGetMap"+req.getMethod(), new Class[]{Class.forName ("javax.servlet.http.HttpServletRequest"), 
			//																									 Class.forName ("javax.servlet.http.HttpServletResponse"),
			//																									 Class.forName ("java.util.ArrayList"),
			//																									 Class.forName ("java.util.TreeMap"),
			//																									 Class.forName ("java.util.TreeMap")});
			//			if(!(Boolean)m.invoke(this, new Object[] {req,resp,remoteServerToCall,layerTableToKeep,layerStyleMap}))
			//				return;
			//			if(!requestSendingGetMapGET(req,resp,remoteServerToCall,layerTableToKeep,layerStyleMap))
			//				return;
			//Post Treatment
			if (wmsGetMapResponseFilePathMap.size() > 0) {
				logger.trace("requestPreTraitementGET begin transform");
				transformGetMap(req, resp);
				logger.trace("requestPreTraitementGET end transform");
			} else {
				//Generate an empty image
				logger.debug(configuration.getServletClass() + ".requestPreTreatmentGetMap : no response from remote servers, generate an empty image");
				BufferedImage imgOut = generateEmptyImage(getProxyRequest().getWidth(), getProxyRequest().getHeight(), getProxyRequest().getFormat(), true, resp);
				Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(getProxyRequest().getFormat());
				ByteArrayOutputStream out = new ByteArrayOutputStream();
				if (iter.hasNext()) {
					ImageWriter writer = (ImageWriter) iter.next();
					writer.setOutput(javax.imageio.ImageIO.createImageOutputStream(out));
					writer.write(imgOut);
					writer.dispose();
				}
				sendHttpServletResponse(req, resp,out,getProxyRequest().getFormat(), HttpServletResponse.SC_OK);
			}
		}catch(Exception e){
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(configuration.getServletClass() + ".requestPreTreatmentGetMap: ", e);
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
			return;
		}
	}

	/**
	 * @param req
	 * @param resp
	 */
	public void requestPreTreatmentGetLegendGraphic (HttpServletRequest req, HttpServletResponse resp){
		try {
			//Check the LAYER parameter 
			if(((WMSProxyServletRequest)getProxyRequest()).getLayer() == null || ((WMSProxyServletRequest)getProxyRequest()).getLayer().equals(""))
			{
				logger.error("LAYER "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE);
				StringBuffer out = owsExceptionReport.generateExceptionReport("LAYER "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE,OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE,"LAYER");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			//Get the remote server informations
			Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();

			//Check if the layer is allowed against policy rules
			String layerAsString = ((WMSProxyServletRequest)getProxyRequest()).getLayer();
			ProxyLayer layer = new ProxyLayer(layerAsString);
			if(layer.getAlias() == null){
				logger.error(OWSExceptionReport.TEXT_INVALID_LAYER_NAME+" missing alias prefix for "+layerAsString);
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"LAYER");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			//Find the remote server concerning by the current layer
			RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(layer.getAlias());

			//Check the availaibility of the requested LAYERS 
			if( RS == null || !isLayerAllowed(layer.getPrefixedName(), RS.getUrl())){
				logger.error( OWSExceptionReport.TEXT_INVALID_LAYER_NAME+layerAsString+" is not allowed");
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"LAYER");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			//Send the request to the remote server and send back the response directly to client
			sendDataDirectStream(resp,"GET", RS.getUrl(), getProxyRequest().getUrlParameters() + "&LAYER="+layer.getPrefixedName());

		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(configuration.getServletClass() + ".requestPreTreatmentGetLegendGraphic: ", e);
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
			return;
		}
	}

	/**
	 * @param req
	 * @param resp
	 */
	@SuppressWarnings("unchecked")
	public void requestPreTreatmentGetFeatureInfo (HttpServletRequest req, HttpServletResponse resp){
		try{
			if(((WMSProxyServletRequest)getProxyRequest()).getQueryLayers() == null || ((WMSProxyServletRequest)getProxyRequest()).getQueryLayers().equalsIgnoreCase(""))
			{
				logger.error( "QUERY_LAYERS "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE);
				StringBuffer out = owsExceptionReport.generateExceptionReport("QUERY_LAYERS "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE,OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE,"QUERY_LAYERS");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
				return;
			}

			//Check the validity of the GetMapRequest :
			//This processing is out of this method because it is also used in the GetMap request
			List <Object> r = checkGetMapRequestValidity(req,resp);

			//If the result is null, the request is not valid and an OGC exception has been already sent in the checkGetMapRequestValidity() method
			if(r == null)
				return;

			//get the authorized layers in the GetMap request
			TreeMap<Integer,ProxyLayer> layerTableToKeepFromGetMap = (TreeMap<Integer,ProxyLayer>) r.get(1);
			TreeMap<Integer,String> layerStyleMap = (TreeMap <Integer,String>) r.get(2);

			//Layer to keep in the remote request
			TreeMap<Integer,ProxyLayer> layerTableToKeep = new TreeMap<Integer, ProxyLayer>();

			//Get the remote server informations
			Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();

			ArrayList <String> remoteServerToCall = new ArrayList<String>();

			List<String> layerArray = null;
			layerArray = Collections.synchronizedList(new ArrayList<String>(Arrays.asList(((WMSProxyServletRequest)getProxyRequest()).getQueryLayers().split(","))));

			for (int k = 0 ; k < layerArray.size() ; k++){
				ProxyLayer layer = new ProxyLayer(layerArray.get(k));
				if(layer.getAlias() == null)
				{
					logger.error( OWSExceptionReport.TEXT_INVALID_QUERY_LAYERS_NAME+" missing alias prefix for "+layerArray.get(k));
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_QUERY_LAYERS_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"QUERY_LAYERS");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
					return;
				}

				//Find the remote server concerning by the current layer
				RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(layer.getAlias());

				//Check the availaibility of the requested QUERY_LAYERS 
				if( RS == null ){
					logger.info( OWSExceptionReport.TEXT_INVALID_QUERY_LAYERS_NAME+layerArray.get(k)+". Prefix is unknown.");
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_QUERY_LAYERS_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"QUERY_LAYERS");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
					return ;
				}
				if(  !isLayerAllowed(layer.getPrefixedName(), RS.getUrl())){
					logger.info( OWSExceptionReport.TEXT_INVALID_QUERY_LAYERS_NAME+layerArray.get(k)+" is not allowed");
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_QUERY_LAYERS_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"QUERY_LAYERS");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
					return ;
				}
				Iterator<Entry<Integer,ProxyLayer>> i = layerTableToKeepFromGetMap.entrySet().iterator();
				while(i.hasNext()){
					Entry<Integer, ProxyLayer> entry =  i.next();
					if(entry.getValue().getAlias().equals(layer.getAlias()) && entry.getValue().getPrefixedName().equals(layer.getPrefixedName())){
						layerTableToKeep.put(k, layer);
						if(!remoteServerToCall.contains(RS.getAlias()))
							remoteServerToCall.add(RS.getAlias());
					}
				}
			}

			//Find if request is candidate to direct streaming
			if(remoteServerToCall.size() == 1 && (getConfiguration().getXsltPath() == null||getConfiguration().getXsltPath().trim() =="" ) ){
				logger.debug("WMSProxyServlet.requestPreTreatmentGetFeatureInfo : request is streamed.");
				//Request can be send with direct streaming
				Iterator<Entry<Integer, ProxyLayer>> itQPL = layerTableToKeep.entrySet().iterator();
				String queryLayerList ="";
				String styleList ="";
				while(itQPL.hasNext()){
					Entry<Integer, ProxyLayer> layer = itQPL.next();
					queryLayerList += layer.getValue().getPrefixedName() +",";
					styleList += layerStyleMap.get(layer.getKey()) +",";
				}


				String queryLayersUrl = "&QUERY_LAYERS=" + queryLayerList.substring(0, queryLayerList.length()-1);
				String layersUrl = "&LAYERS=" + queryLayerList.substring(0, queryLayerList.length()-1);
				String stylesUrl = "&STYLES=" + styleList.substring(0, styleList.length()-1);
				sendDataDirectStream(resp, "GET", ((RemoteServerInfo)remoteServersTable.get(remoteServerToCall.get(0))).getUrl(), getProxyRequest().getUrlParameters() + queryLayersUrl + "&" + layersUrl + "&" + stylesUrl);
				return;
			}

			//Send the request to the remote servers
			//Loop on the remote server to send the request 
			List<WMSProxyServerGetFeatureInfoThread> serverThreadList = new Vector<WMSProxyServerGetFeatureInfoThread>();
			for(int k = 0; k<remoteServerToCall.size();k++){
				TreeMap<Integer, ProxyLayer> layerByServerTable = new TreeMap<Integer, ProxyLayer>();
				TreeMap<Integer, ProxyLayer> queryLayerByServerTable = new TreeMap<Integer, ProxyLayer>();

				//Get the remote server info
				RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(remoteServerToCall.get(k));

				//Loop on QUERY_LAYERS to keep only those to send to the current RS
				Iterator<Entry<Integer, ProxyLayer>> itLK = layerTableToKeep.entrySet().iterator();
				while(itLK.hasNext()){
					Entry<Integer, ProxyLayer> layerOrdered = itLK.next();
					if(((ProxyLayer)layerOrdered.getValue()).getAlias().equals(RS.getAlias())){
						queryLayerByServerTable.put(layerOrdered.getKey(), layerOrdered.getValue());
					}
				}

				//Loop on LAYERS to keep only those to send to the current RS
				Iterator<Entry<Integer, ProxyLayer>> itLKGM = layerTableToKeepFromGetMap.entrySet().iterator();
				while(itLKGM.hasNext()){
					Entry<Integer, ProxyLayer> layerOrdered = itLKGM.next();
					if(((ProxyLayer)layerOrdered.getValue()).getAlias().equals(RS.getAlias())){
						layerByServerTable.put(layerOrdered.getKey(), layerOrdered.getValue());
					}
				}

				//New Thread to request this remote server
				WMSProxyServerGetFeatureInfoThread s = new WMSProxyServerGetFeatureInfoThread(	this,
						getProxyRequest().getUrlParameters(), 
						queryLayerByServerTable,
						layerByServerTable, 
						layerStyleMap,
						RS,
						resp);

				s.start();
				serverThreadList.add(s);
			}	

			// Wait for thread results
			for (int i = 0; i < serverThreadList.size(); i++) {
				serverThreadList.get(i).join();
			}

			if (wmsGetFeatureInfoResponseFilePathMap.size() > 0) {
				logger.trace("requestPreTreatmentGetFeatureInfo begin transform");
				transformGetFeatureInfo( req, resp);
				logger.trace("requestPreTreatmentGetFeatureInfo end transform");
			} else {
				logger.debug(configuration.getServletClass() + ".requestPreTreatmentGetFeatureInfo : no response from remote servers, generate an empty response");
				ByteArrayOutputStream out = new ByteArrayOutputStream();
				sendHttpServletResponse(req, resp,out,responseContentType, HttpServletResponse.SC_OK);
			}

		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(configuration.getServletClass() + ".requestPreTreatmentGetFeatureInfo: ", e);
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
			return;
		}
	}

	public boolean requestSendingGetMapGET(HttpServletRequest req, HttpServletResponse resp, ArrayList <String> remoteServerToCall, TreeMap<Integer,ProxyLayer> layerTableToKeep, TreeMap<Integer,String> layerStyleMap){
		try{
			//Get the remote server informations
			Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();

			//Find if request is candidate to direct streaming
			//getLayerFilter(getRemoteServerInfo(response.getValue().getAlias()).getUrl(),getProxyRequest().getLayers().split(",")[response.getKey()]),
			if(remoteServerToCall.size() == 1){
				Boolean isCandidateToStreaming = true;
				RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(remoteServerToCall.get(0));
				Iterator<Entry<Integer, ProxyLayer>> itLK = layerTableToKeep.entrySet().iterator();
				while(itLK.hasNext()){
					Entry<Integer, ProxyLayer> layer = itLK.next();
					if(getLayerFilter(RS.getUrl(), layer.getValue().getPrefixedName()) != null){
						isCandidateToStreaming = false;
						break;
					}
				}
				if(isCandidateToStreaming){
					logger.debug("WMSProxyServlet.requestSendingGetMapGET : request is streamed.");
					//Request can be send with direct streaming
					Iterator<Entry<Integer, ProxyLayer>> itPL = layerTableToKeep.entrySet().iterator();
					String layerList ="";
					String styleList ="";
					while(itPL.hasNext()){
						Entry<Integer, ProxyLayer> layer = itPL.next();
						layerList += layer.getValue().getPrefixedName() +",";
						styleList += layerStyleMap.get(layer.getKey()) +",";
					}

					String layersUrl = "&LAYERS=" + layerList.substring(0, layerList.length()-1);
					String stylesUrl = "&STYLES=" + styleList.substring(0, styleList.length()-1);

					//Set TRANSPARENT to TRUE if not present
					String paramUrl = getProxyRequest().getUrlParameters();
					if (paramUrl.toUpperCase().indexOf("TRANSPARENT=") == -1)
						paramUrl += "TRANSPARENT=TRUE&";

					sendDataDirectStream(resp,"GET", RS.getUrl(), paramUrl + layersUrl + stylesUrl);
					return false;
				}
			}

			//Loop on the remote server to send the request 
			List<WMSProxyServerGetMapThread> serverThreadList = new Vector<WMSProxyServerGetMapThread>();
			for(int k = 0; k<remoteServerToCall.size();k++){
				TreeMap<Integer, ProxyLayer> layerByServerTable = new TreeMap<Integer, ProxyLayer>();
				RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(remoteServerToCall.get(k));
				Iterator<Entry<Integer, ProxyLayer>> itLK = layerTableToKeep.entrySet().iterator();
				//Build a list of the layers for the current remote server
				while(itLK.hasNext()){
					Entry<Integer, ProxyLayer> layerOrdered = itLK.next();
					if(((ProxyLayer)layerOrdered.getValue()).getAlias().equals(RS.getAlias())){
						layerByServerTable.put(layerOrdered.getKey(), layerOrdered.getValue());
					}
				}
				//New Thread to request this remote server
				WMSProxyServerGetMapThread s = new WMSProxyServerGetMapThread(	this,
						getProxyRequest().getUrlParameters(), 
						layerByServerTable, 
						layerStyleMap,
						RS,
						resp);

				s.start();
				serverThreadList.add(s);
			}	

			// Wait for thread results
			for (int i = 0; i < serverThreadList.size(); i++) {
				serverThreadList.get(i).join();
			}

			return true;

		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(configuration.getServletClass() +  ".requestSendingGetMapGET: ", e);
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
			return false;
		}
	}

	public boolean requestSendingGetMapPOST(HttpServletRequest req, HttpServletResponse resp, ArrayList <String> remoteServerToCall, TreeMap<Integer,ProxyLayer> layerTableToKeep, TreeMap<Integer,String> layerStyleMap){
		try{
			//Get the remote server informations
			Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();

			//Find if request is candidate to direct streaming
			//getLayerFilter(getRemoteServerInfo(response.getValue().getAlias()).getUrl(),getProxyRequest().getLayers().split(",")[response.getKey()]),
			if(remoteServerToCall.size() == 1){
				Boolean isCandidateToStreaming = true;
				RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(remoteServerToCall.get(0));
				Iterator<Entry<Integer, ProxyLayer>> itLK = layerTableToKeep.entrySet().iterator();
				while(itLK.hasNext()){
					Entry<Integer, ProxyLayer> layer = itLK.next();
					if(getLayerFilter(RS.getUrl(), layer.getValue().getPrefixedName()) != null){
						isCandidateToStreaming = false;
						break;
					}
				}
				if(isCandidateToStreaming){
					logger.debug("WMSProxyServlet.requestSendingGetMapPOST : request is streamed.");
					//Request can be send with direct streaming
					StringBuffer request = rewriteGetMapRequestPOST(layerTableToKeep);

					sendDataDirectStream(resp,"POST", RS.getUrl(), request.toString());
					return false;
				}
			}

			//Loop on the remote server to send the request 
			List<WMSProxyServerGetMapThread> serverThreadList = new Vector<WMSProxyServerGetMapThread>();
			for(int k = 0; k<remoteServerToCall.size();k++){
				TreeMap<Integer, ProxyLayer> layerByServerTable = new TreeMap<Integer, ProxyLayer>();
				RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(remoteServerToCall.get(k));
				Iterator<Entry<Integer, ProxyLayer>> itLK = layerTableToKeep.entrySet().iterator();
				//Build a list of the layers for the current remote server
				while(itLK.hasNext()){
					Entry<Integer, ProxyLayer> layerOrdered = itLK.next();
					if(((ProxyLayer)layerOrdered.getValue()).getAlias().equals(RS.getAlias())){
						layerByServerTable.put(layerOrdered.getKey(), layerOrdered.getValue());
					}
				}

				//New Thread to request this remote server
				WMSProxyServerGetMapThread s = new WMSProxyServerGetMapThread(	this,
						getProxyRequest().getBodyRequest().toString(), 
						layerByServerTable, 
						layerStyleMap,
						RS,
						resp);

				s.start();
				serverThreadList.add(s);
			}	

			// Wait for thread results
			for (int i = 0; i < serverThreadList.size(); i++) {
				serverThreadList.get(i).join();
			}

			return true;

		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(configuration.getServletClass() +  ".requestSendingGetMapPOST: ", e);
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
			return false;
		}
	}

	/**
	 * Overwrite the GetCapabilities response with config and policy informations :
	 * - Service metadata 
	 * - Authorized operations
	 * - Authorized layers
	 * - BBOX (geographic filter)
	 * - online resources
	 * Merge all the remote responses into one single file to send to the client.
	 * @param req
	 * @param resp
	 */
	public void transformGetCapabilities (HttpServletRequest req, HttpServletResponse resp){
		try{
			//Get the responses which are OGC exception (XML)
			HashMap<String, String> remoteServerExceptionFiles = getRemoteServerExceptionResponse(wmsGetCapabilitiesResponseFilePathMap);

			//If the Exception mode is 'restrictive' and at least a response is an exception
			//Or if the Exception mode is 'permissive' and all the response are exceptio
			//Aggegate the exception files and send the result to the client
			if((remoteServerExceptionFiles.size() > 0 && configuration.getExceptionMode().equals("restrictive")) ||  
					(configuration.getExceptionMode().equals("permissive") && wmsGetCapabilitiesResponseFilePathMap.size() == 0)){
				logger.info("Exception(s) returned by remote server(s) are sent to client.");
				ByteArrayOutputStream exceptionOutputStream = docBuilder.ExceptionAggregation(remoteServerExceptionFiles);
				sendHttpServletResponse(req,resp,exceptionOutputStream, "text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			//Check if all the responses are in the same WMS protocol version
			if(!isAllGetCapabilitiesResponseSameVersion(wmsGetCapabilitiesResponseFilePathMap)){
				logger.error(OWSExceptionReport.TEXT_MULTISERVER_VERSION_NEGOCIATION_FAILED);
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_MULTISERVER_VERSION_NEGOCIATION_FAILED,OWSExceptionReport.CODE_MULTISERVER_VERSION_NEGOTIATION_FAILED,null);
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			//Get the remote server response protocol version
			if(!getResponseVersion().equals(getProxyRequest().getVersion())){
				//Instantiate the WMSProxyResponseBuilder corresponding to the response version
				Class<?> classe = Class.forName("org.easysdi.proxy.wms.v"+getResponseVersion().replaceAll("\\.", "")+"."+"WMSProxyResponseBuilder"+getResponseVersion().replaceAll("\\.", ""));
				Constructor<?> constructeur = classe.getConstructor(new Class [] {Class.forName ("org.easysdi.proxy.core.ProxyServlet")});
				docBuilder = (WMSProxyResponseBuilder)constructeur.newInstance(this);
			}

			//Capabilities rewriting
			RemoteServerInfo rs = getRemoteServerInfoMaster();

			try{
				if(!docBuilder.CapabilitiesContentsFiltering(wmsGetCapabilitiesResponseFilePathMap,getServletUrl(req)))
				{
					logger.error(docBuilder.getLastException().getMessage());
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
					return;
				}
			}catch (NoSuchAuthorityCodeException e){
				logger.error(e.getMessage());
				StringBuffer out = owsExceptionReport.generateExceptionReport(e.getMessage(),OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			if(!docBuilder.CapabilitiesOperationsFiltering(wmsGetCapabilitiesResponseFilePathMap.get(rs.getAlias()), getServletUrl(req)))
			{
				logger.error(docBuilder.getLastException().getMessage());
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			if(!docBuilder.CapabilitiesMerging(wmsGetCapabilitiesResponseFilePathMap))
			{
				logger.error(docBuilder.getLastException().getMessage());
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			if(!docBuilder.CapabilitiesServiceMetadataWriting(wmsGetCapabilitiesResponseFilePathMap.get(rs.getAlias()),getServletUrl(req)))
			{
				logger.error(docBuilder.getLastException().getMessage());
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			//If exists, apply a specific user XSLT transformation
			File result = applyUserXSLT(new File(wmsGetCapabilitiesResponseFilePathMap.get(rs.getAlias())));

			//Prepare response
			FileInputStream reader = new FileInputStream(result);
			byte[] data = new byte[reader.available()];
			reader.read(data, 0, reader.available());
			ByteArrayOutputStream out = new ByteArrayOutputStream();
			out.write(data);
			reader.close();
			sendHttpServletResponse(req,resp, out,responseContentType, responseStatusCode);

		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(configuration.getServletClass() + ".transformGetCapabilities: ", e);
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
			return;
		}
	}

	/**
	 * Apply the geographic filter, defined in the policy, on the response from remote servers.
	 * Buffer image.
	 * @param req
	 * @param resp
	 */
	public void transformGetMap (HttpServletRequest req, HttpServletResponse resp){
		try{
			//Get the responses which are OGC exception (XML)
			HashMap<String, String> remoteServerExceptionFiles = getRemoteServerExceptionResponse(wmsGetMapResponseFilePathMap);

			//If the Exception mode is 'restrictive' and at least a response is an exception
			//Or if the Exception mode is 'permissive' and all the response are exceptio
			//Aggegate the exception files and send the result to the client
			if((remoteServerExceptionFiles.size() > 0 && configuration.getExceptionMode().equals("restrictive")) ||  
					(wmsGetMapResponseFilePathMap.size() == 0)){
				logger.info("Exception(s) returned by remote server(s) are sent to client.");
				ByteArrayOutputStream exceptionOutputStream = docBuilder.ExceptionAggregation(remoteServerExceptionFiles);
				sendHttpServletResponse(req,resp,exceptionOutputStream, "text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			//If an OGC XML exception was returned by a remote server, 
			//the responseContentType variable can have a wrong value (it can contain : "text/xml").
			//If the exception has to be returned, this is already done before in the code.
			//If this code section is reached, that means an image has to be returned so we loop in all the contentType of
			//the responses received to find one different from text/xml
			//and we use it as the contentType of the response to return.
			Iterator<String> itL = responseContentTypeList.iterator();
			while (itL.hasNext()) {
				responseContentType = (String) itL.next();
				if(!isXML(responseContentType))
					break;
			} 
			boolean isTransparent = isAcceptingTransparency(responseContentType);

			//
			BufferedImage imageSource = null;
			Graphics2D g = null;
			//Loop over the remote servers response
			Iterator<Entry<Integer, ProxyRemoteServerResponse>> iR = wmsGetMapResponseFilePathMap.entrySet().iterator();
			while (iR.hasNext()){
				Entry<Integer, ProxyRemoteServerResponse> response = iR.next(); 
				ProxyLayer pLayer = new ProxyLayer(getProxyRequest().getLayers().split(",")[response.getKey()]);
				BufferedImage image = filterImage(getLayerFilter(getRemoteServerInfo(response.getValue().getAlias()).getUrl(),pLayer.getPrefixedName()),
						response.getValue().getPath(),
						isTransparent, 
						resp);
				if (g == null) {
					imageSource = image;
					if (imageSource != null)
						g = imageSource.createGraphics();
				} else if (image != null)
					g.drawImage(image, null, 0, 0);
			}
			if (g != null)g.dispose();

			Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(responseContentType);
			ByteArrayOutputStream out = new ByteArrayOutputStream();
			if (iter.hasNext()) {
				ImageWriter writer = (ImageWriter) iter.next();
				writer.setOutput(new MemoryCacheImageOutputStream(out));
				if (imageSource != null)
					writer.write(imageSource);
			}
			sendHttpServletResponse(req,resp, out,responseContentType, responseStatusCode);
		}catch (Exception e){
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(configuration.getServletClass() + ".transformGetMap: ", e);
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
			return;
		}
	}

	/**
	 * @param req
	 * @param resp
	 */
	public void transformGetFeatureInfo (HttpServletRequest req, HttpServletResponse resp){
		try{
			//Get the responses which are OGC exception (XML)
			HashMap<String, String> remoteServerExceptionFiles = getRemoteServerExceptionResponse(wmsGetFeatureInfoResponseFilePathMap);

			//If the Exception mode is 'restrictive' and at least a response is an exception
			//Or if the Exception mode is 'permissive' and all the response are exceptio
			//Aggegate the exception files and send the result to the client
			if((remoteServerExceptionFiles.size() > 0 && configuration.getExceptionMode().equals("restrictive")) ||  
					(wmsGetFeatureInfoResponseFilePathMap.size() == 0)){
				logger.info("Exception(s) returned by remote server(s) are sent to client.");
				ByteArrayOutputStream exceptionOutputStream = docBuilder.ExceptionAggregation(remoteServerExceptionFiles);
				sendHttpServletResponse(req,resp,exceptionOutputStream, "text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			ByteArrayOutputStream outResult = docBuilder.GetFeatureInfoAggregation(wmsGetFeatureInfoResponseFilePathMap);
			if(outResult == null){
				logger.error(docBuilder.getLastException().getMessage());
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			//apply XSLT transformation if needed before send back to client the response
			sendHttpServletResponse(req,resp, applyUserXSLT(outResult),responseContentType, responseStatusCode);

		}catch (Exception e){
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(configuration.getServletClass() + ".transformGetFeatureInfo: ", e);
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
			return;
		}
	}


	/**
	 * Get the exception files return by the remote servers.
	 * @param remoteServerResponseFile
	 * @return
	 */
	protected HashMap<String, String> getRemoteServerExceptionResponse (HashMap<String, String> remoteServerResponseFile)
	{
		HashMap<String, String> toRemove = new HashMap<String, String>();
		HashMap<String, String> remoteServerExceptionFiles = new HashMap<String, String>();

		try {
			Iterator<Entry<String,String>> it = remoteServerResponseFile.entrySet().iterator();
			while(it.hasNext()){
				Entry<String,String> entry = it.next();
				String path  = entry.getValue();
				if(path == null || path.length() == 0)
					continue;

				//Check if the response is an XML exception
				if(isRemoteServerResponseException(path)){
					toRemove.put(entry.getKey(), path);
				}
			}

			Iterator<Entry<String,String>> itR = toRemove.entrySet().iterator();
			while(itR.hasNext())
			{
				Entry<String, String> entry = itR.next();
				remoteServerExceptionFiles.put(entry.getKey(),entry.getValue());
				remoteServerResponseFile.remove(entry.getKey());
			}

			return remoteServerExceptionFiles;
		} catch (SAXException e) {
			logger.error(e.getMessage());
		} catch (IOException e) {
			logger.error(e.getMessage());
		} catch (ParserConfigurationException e) {
			logger.error(e.getMessage());
		} catch (JDOMException e) {
			logger.error(e.getMessage());
		}
		return remoteServerExceptionFiles;
	}

	/**
	 * Get the exception files return by the remote servers.
	 * @param remoteServerResponseFile
	 * @return
	 */
	protected HashMap<String, String> getRemoteServerExceptionResponse (TreeMap<Integer,ProxyRemoteServerResponse> remoteServerResponseFile)
	{
		TreeMap<Integer, ProxyRemoteServerResponse> toRemove = new TreeMap<Integer, ProxyRemoteServerResponse>();
		HashMap<String, String> remoteServerExceptionFiles = new HashMap<String, String>();

		try{
			Iterator<Entry<Integer, ProxyRemoteServerResponse>> it = remoteServerResponseFile.entrySet().iterator();
			while(it.hasNext()){
				Entry<Integer, ProxyRemoteServerResponse> entry = it.next();
				ProxyRemoteServerResponse response = entry.getValue();
				String path = response.getPath();
				if(path == null || path.length() == 0)
					continue;

				//Check if the response is an XML exception
				if(isRemoteServerResponseException(path)){
					toRemove.put(entry.getKey(), response);
				}
			}

			Iterator<Entry<Integer, ProxyRemoteServerResponse>> itR = toRemove.entrySet().iterator();
			while(itR.hasNext())
			{
				Entry<Integer, ProxyRemoteServerResponse> entry = itR.next();
				remoteServerExceptionFiles.put(entry.getValue().getAlias(),entry.getValue().getPath());
				remoteServerResponseFile.remove(entry.getKey());
			}

			return remoteServerExceptionFiles;
		} catch (SAXException e) {
			logger.error(e.getMessage());
		} catch (IOException e) {
			logger.error(e.getMessage());
		} catch (ParserConfigurationException e) {
			logger.error(e.getMessage());
		} catch (JDOMException e) {
			logger.error(e.getMessage());
		}
		return remoteServerExceptionFiles;
	}



	public boolean isAllGetCapabilitiesResponseSameVersion (HashMap<String, String> wmsGetCapabilitiesResponse){

		SAXBuilder sxb = new SAXBuilder();
		Iterator<Entry<String, String>> iFilePath = wmsGetCapabilitiesResponse.entrySet().iterator();

		while (iFilePath.hasNext()){
			Entry<String, String> filePath = iFilePath.next();

			try {
				org.jdom.Document doc = sxb.build(new File (filePath.getValue()));
				org.jdom.Element racine = doc.getRootElement();
				String version = racine.getAttributeValue("version");
				if(getResponseVersion() == null){
					setResponseVersion( version);
				}else{
					if(!getResponseVersion().equals(version)){
						return false;
					}
				}

			} catch (JDOMException e) {
				logger.error(e.getMessage());
				return false;
			} catch (IOException e) {
				logger.error(e.getMessage());
				return false;
			}
		}
		return true;
	}
	/**
	 * Check the validity of the request GETMAP
	 * @param req
	 * @param resp
	 * @return
	 * @throws Exception
	 */
	public List<Object> checkGetMapRequestValidity (HttpServletRequest req, HttpServletResponse resp) throws Exception{
		//Check the WIDTH and HEIGHT parameters validity against the policy rules
		if (!isSizeInTheRightRange(Integer.parseInt(((WMSProxyServletRequest)getProxyRequest()).getWidth()), 
				Integer.parseInt(((WMSProxyServletRequest)getProxyRequest()).getWidth())))
		{
			logger.error(OWSExceptionReport.TEXT_INVALID_DIMENSION_VALUE);
			StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_DIMENSION_VALUE,OWSExceptionReport.CODE_INVALID_DIMENSION_VALUE,"WIDTH");
			sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			return null;
		}

		//Get the remote server informations
		Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();

		//Get the requested layer names
		TreeMap<Integer,String> layerMap = new TreeMap <Integer,String>();
		if(((WMSProxyServletRequest)getProxyRequest()).getLayers()==null){
			logger.error("LAYERS "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE);
			StringBuffer out = owsExceptionReport.generateExceptionReport("LAYERS "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE,OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE,"LAYERS");
			sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			return null;
		}


		ArrayList<String> layerParamAsArray = new ArrayList<String>(Arrays.asList(((WMSProxyServletRequest)getProxyRequest()).getLayers().split(",")));
		for (int index = 0 ; index < layerParamAsArray.size() ; index++){
			layerMap.put(index, layerParamAsArray.get(index));
		}

		//Check the STYLES parameter
		TreeMap<Integer,String> layerStyleMap = new TreeMap <Integer,String>();
		ArrayList<String> layerStyleParamAsArray = new ArrayList<String>();
		if(((WMSProxyServletRequest)getProxyRequest()).getStyles() != null){
			layerStyleParamAsArray = new ArrayList<String>(Arrays.asList(((WMSProxyServletRequest)getProxyRequest()).getStyles().split(",")));
		}

		//A style definition is mandatory for each layer, we create them if needed
		if (layerStyleParamAsArray.size() < layerMap.size()) {
			int diffSize = layerMap.size() - layerStyleParamAsArray.size();
			for (int i = 0; i < diffSize; i++) {
				layerStyleParamAsArray.add("");
			}
		}
		for (int index = 0 ; index < layerStyleParamAsArray.size() ; index++){
			layerStyleMap.put(index, layerStyleParamAsArray.get(index));
		}

		//Get the BBOX parameter
		ReferencedEnvelope rEnvelope;
		try {
			rEnvelope = new ReferencedEnvelope(((WMSProxyServletRequest)getProxyRequest()).getX1(), ((WMSProxyServletRequest)getProxyRequest()).getX2(), ((WMSProxyServletRequest)getProxyRequest()).getY1(), ((WMSProxyServletRequest)getProxyRequest()).getY2(), CRS.decode(((WMSProxyServletRequest)getProxyRequest()).getSrsName()));
		}catch (Exception ex) {
			logger.error(OWSExceptionReport.TEXT_INVALID_SRS);
			StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_SRS,OWSExceptionReport.CODE_INVALID_SRS,"SRS");
			sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
			return null;
		}

		//List of object to keep from the request
		TreeMap<Integer,ProxyLayer> layerTableToKeep = new TreeMap <Integer,ProxyLayer>();

		//Loop on LAYERS to keep only the valid layers
		Iterator<Entry<Integer,String>> it =  layerMap.entrySet().iterator();
		ArrayList <String> remoteServerToCall = new ArrayList<String>();
		while (it.hasNext()){
			Entry <Integer, String> layerOrdered = it.next();
			String layerName = layerOrdered.getValue();
			ProxyLayer layer = new ProxyLayer(layerName);
			if(layer.getAlias() == null)
			{
				logger.error(OWSExceptionReport.TEXT_INVALID_LAYERS_NAME+" missing alias prefix for "+layerName);
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYERS_NAME +layerName,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"LAYERS");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return null;
			}
			//Find the remote server concerning by the current layer
			RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(layer.getAlias());

			//Check the availaibility of the requested LAYERS 
			if( RS == null ){
				logger.error( OWSExceptionReport.TEXT_INVALID_LAYERS_NAME+layerName+". Prefix is unknown");
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYERS_NAME+layerName,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"LAYERS");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return null;
			}
			if( !isLayerAllowed(layer.getPrefixedName(), RS.getUrl())){
				logger.error( OWSExceptionReport.TEXT_INVALID_LAYERS_NAME+layerName+" is not allowed");
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYERS_NAME+layerName,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"LAYERS");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return null;
			}

			//Check if the scale is available
			if (isLayerInScale(layer.getPrefixedName(), RS.getUrl(), RendererUtilities.calculateOGCScale(rEnvelope, Integer.parseInt(((WMSProxyServletRequest)getProxyRequest()).getWidth()), null))) {
				//Layer to keep in the request
				layerTableToKeep.put(layerOrdered.getKey(),layer);
				//Servers to call to complete the request
				if(!remoteServerToCall.contains(layer.getAlias())){
					remoteServerToCall.add(layer.getAlias());
				}
			} else {
				logger.info("requestPreTraitementGetMap says: request Scale out of bounds for "+layerName+", see the policy definition.");
			}
		}
		List<Object> map = new ArrayList<Object>();
		map.add(remoteServerToCall);
		map.add(layerTableToKeep);
		map.add(layerStyleMap);
		return map;
	}

	/**
	 * Generate an empty image
	 * @param width
	 * @param height
	 * @param format
	 * @param isTransparent
	 * @param j
	 * @param resp
	 */
	private BufferedImage generateEmptyImage(String width, String height, String format, boolean isTransparent, HttpServletResponse resp) {
		BufferedImage imgOut = null;
		try {
			if (isTransparent) {
				imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.BITMASK);
			} else {
				imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.TYPE_INT_ARGB);
			}
		} catch (Exception e) {
			logger.error("GenerateEmptyImage: ",e);
		} 
		return imgOut;
	}

	/**
	 * @param filter
	 * @param fileName
	 * @param isTransparent
	 * @param resp
	 * @return
	 */
	private BufferedImage filterImage(String filter, String fileName, boolean isTransparent, HttpServletResponse resp) {
		try {
			if (filter != null) {
				String[] s = ((WMSProxyServletRequest)getProxyRequest()).getBbox().split(",");

				InputStream bis = new ByteArrayInputStream(filter.getBytes());
				System.setProperty("org.geotools.referencing.forceXY", "true");

				Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);
				WKTReader wktReader = new WKTReader();

				Geometry polygon = wktReader.read(object.toString());

				String srs = filter.substring(filter.indexOf("srsName"));
				srs = srs.substring(srs.indexOf("\"") + 1);
				srs = srs.substring(0, srs.indexOf("\""));
				polygon.setSRID(Integer.parseInt(srs.substring(5)));

				CRSEnvelope bbox = new CRSEnvelope(srs, Double.parseDouble(s[0]), Double.parseDouble(s[1]), Double.parseDouble(s[2]), Double
						.parseDouble(s[3]));

				// final WorldFileReader reader = new WorldFileReader(new
				// File(filePath));

				BufferedImage image = ImageIO.read(new File(fileName));
				int type = BufferedImage.TYPE_INT_BGR;
				if (image.getTransparency() == Transparency.BITMASK)
					type = BufferedImage.BITMASK;
				else if (image.getTransparency() == Transparency.TRANSLUCENT)
					type = BufferedImage.TRANSLUCENT;
				BufferedImage canvas = new BufferedImage(image.getWidth(), image.getHeight(), type);
				canvas.getGraphics().drawImage(image, 0, 0, null);
				BufferedImage imageOut = imageFiltering(canvas, bbox, polygon, isTransparent, resp);

				return imageOut;
			} else {
				if (fileName != null) {
					BufferedImage image = ImageIO.read(new File(fileName));
					if (image == null)
						return null;
					int type = BufferedImage.TYPE_INT_BGR;
					if (image.getTransparency() == Transparency.BITMASK)
						type = BufferedImage.BITMASK;
					else if (image.getTransparency() == Transparency.TRANSLUCENT)
						type = BufferedImage.TRANSLUCENT;
					BufferedImage canvas = new BufferedImage(image.getWidth(), image.getHeight(), type);
					canvas.getGraphics().drawImage(image, 0, 0, null);
					return canvas;
				}
			}
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(configuration.getServletClass() + ".filterImage: ",e);
		}
		return null;
	}

	/**
	 * envelope contains the envelope of the whole image
	 * @param imageSource
	 * @param envelope
	 * @param polygonFilter
	 * @param isTransparent
	 * @param resp
	 * @return
	 */
	private BufferedImage imageFiltering(BufferedImage imageSource, CRSEnvelope envelope, Geometry polygonFilter, boolean isTransparent,
			HttpServletResponse resp) {
		try {
			System.setProperty("org.geotools.referencing.forceXY", "true");
			// System.setProperty(
			// (Hints.FORCE_STANDARD_AXIS_DIRECTIONS.toString()), "true" );

			// Transform the srs of the filter if needed.
			String srsName = envelope.getEPSGCode();
			CoordinateReferenceSystem crs = CRS.decode(srsName);

			CoordinateReferenceSystem sourceCRS = CRS.decode("EPSG:" + (new Integer(polygonFilter.getSRID())).toString());
			CoordinateReferenceSystem targetCRS = CRS.decode(envelope.getEPSGCode());

			MathTransform a = CRS.findMathTransform(sourceCRS, targetCRS, false);

			polygonFilter = JTS.transform(polygonFilter, a);

			try {
				for (int i = 0; i < crs.getIdentifiers().size(); i++) {
					if (((NamedIdentifier) crs.getIdentifiers().toArray()[i]).getCodeSpace().equals("EPSG")) {
						polygonFilter.setSRID(Integer.parseInt(((NamedIdentifier) crs.getIdentifiers().toArray()[i]).getCode()));
						break;
					}
				}
			} catch (Exception e) {
				resp.setHeader("easysdi-proxy-error-occured", "true");
				logger.error(configuration.getServletClass() + ".imageFiltering: ",e);
			}

			final GeometryAttributeType geom = new GeometricAttributeType("Geom", Geometry.class, false, null, crs, null);
			final AttributeType attr1 = AttributeTypeFactory.newAttributeType("COLOR", String.class);
			final AttributeType[] attributes = new AttributeType[] { attr1, geom };

			final FeatureType schema = FeatureTypes.newFeatureType(attributes, "TEMPORARYFEATURE", new URI("depth.ch"), false, null, geom);

			// Construction du masque sur la base de l'enveloppe de la bbox et
			// du polygon de filtre
			FeatureRasterizer fr = new FeatureRasterizer(imageSource.getHeight(), imageSource.getWidth());
			double width = envelope.getMaxX() - envelope.getMinX();
			double height = envelope.getMaxY() - envelope.getMinY();
			;
			Rectangle2D.Double bounds = new Rectangle2D.Double(envelope.getMinX(), envelope.getMinY(), width, height);

			fr.setBounds(bounds);
			fr.setAttName("COLOR");

			fr.addFeature(schema.create(new Object[] { Integer.toString(Color.WHITE.getRGB()), polygonFilter }));

			// Construction de l'image de masquage
			BufferedImage bimage2 = fr.getBimage();
			int imageType = BufferedImage.TYPE_INT_RGB;
			if (isTransparent) {
				imageType = BufferedImage.TYPE_INT_ARGB;
			}

			// "dimg" contient l'image source et "bimage2" est utilisé comme
			// masque.
			BufferedImage dimg = new BufferedImage(imageSource.getWidth(), imageSource.getHeight(), imageType);
			Graphics2D g = dimg.createGraphics();
			g.setComposite(AlphaComposite.Src);
			g.drawImage(imageSource, null, 0, 0);
			g.dispose();
			for (int i = 0; i < bimage2.getHeight(); i++) {
				for (int j = 0; j < bimage2.getWidth(); j++) {
					if (bimage2.getRGB(j, i) == 0) {
						// dimg.setRGB(j, i, 0x8F1C1C);
						dimg.setRGB(j, i, 0xFFFFFF);
					}
				}
			}

			// Une fois le masque appliqué sur l'image source, renvoyé l'image
			// filtrée
			return dimg;
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(configuration.getServletClass() + ".imageFiltering: ",e);
		}

		return imageSource;
	}

	@Override
	protected StringBuffer generateOgcException(String errorMessage,String code, String locator, String version) {
		return null;
	}	 

	@SuppressWarnings({ "unchecked", "rawtypes" })
	protected StringBuffer rewriteGetMapRequestPOST (TreeMap<Integer,ProxyLayer> layerTableToKeep){
		SAXBuilder sxb = new SAXBuilder();
		Document document;

		try {
			document = sxb.build(new ByteArrayInputStream(getProxyRequest().getBodyRequest().toString().getBytes()));

			List<Element> toRemove = new ArrayList<Element>();
			List<Element> toRewrite = new ArrayList<Element>();
			Boolean toKeep = false;

			//	    Namespace nsSLD =  Namespace.getNamespace("sld","http://www.opengis.net/sld");
			//	    Namespace nsSE =  Namespace.getNamespace("se","http://www.opengis.net/se");

			Element racine = document.getRootElement();
			Iterator ilNamedLayer = racine.getDescendants(new ElementFilter("NamedLayer"));

			while(ilNamedLayer.hasNext()){
				Element namedLayer = (Element)ilNamedLayer.next();
				List<Element> lName = namedLayer.getChildren();
				Iterator<Element> ilName = lName.iterator();
				while (ilName.hasNext()){
					Element elementName = ilName.next();
					if(elementName.getName().equals("Name")){
						Iterator<Entry<Integer, ProxyLayer>> itLK = layerTableToKeep.entrySet().iterator();
						while(itLK.hasNext()){
							Entry<Integer, ProxyLayer> layerOrdered = itLK.next();
							if(((ProxyLayer)layerOrdered.getValue()).getAliasName().equals(elementName.getText())){
								//Keep the layer in the request
								toRewrite.add(elementName);
								toKeep = true;
								break;
							}
						}
						if(!toKeep){
							toRemove.add(namedLayer);
						}
						toKeep = false;
					}
				}

				Iterator<Element> iToRemove = toRemove.iterator();
				while(iToRemove.hasNext()){
					Element element = iToRemove.next();
					racine.removeContent(element);
				}

				Iterator<Element> iToRewrite = toRewrite.iterator();
				while(iToRewrite.hasNext()){
					Element element = iToRewrite.next();
					ProxyLayer proxyLayer = new ProxyLayer(element.getText());
					element.setText(proxyLayer.getPrefixedName());
				}
			}

			XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
			String out = sortie.outputString(document);
			return new StringBuffer(out);

		} catch (JDOMException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		return null;
	}
}
