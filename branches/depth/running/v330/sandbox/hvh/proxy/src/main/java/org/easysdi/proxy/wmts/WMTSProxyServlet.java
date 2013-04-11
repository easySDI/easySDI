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
package org.easysdi.proxy.wmts;

import java.awt.image.BufferedImage;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.lang.reflect.Method;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Set;
import java.util.Vector;

import javax.imageio.ImageIO;
import javax.imageio.ImageWriter;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.jdom.filter.ElementTileMatrixSetFilter;
import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.core.ProxyServletRequest;
import org.easysdi.proxy.domain.SdiPhysicalservicePolicy;
import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.domain.SdiWmslayerPolicy;
import org.easysdi.proxy.domain.SdiWmtslayerPolicy;
import org.easysdi.proxy.ows.OWSExceptionManager;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.policy.Layer;
import org.easysdi.proxy.policy.Server;
import org.easysdi.proxy.policy.TileMatrix;
import org.easysdi.proxy.policy.TileMatrixSet;
import org.easysdi.proxy.wmts.thread.WMTSProxyServerGetCapabilitiesThread;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.input.SAXBuilder;

/**
 * @author Depth SA
 *
 */
public class WMTSProxyServlet extends ProxyServlet{

    private static final long serialVersionUID = 1982682293133286643L;

    /**
     * Object to build the response to send to the client :
     * - rewrite capabilities accordng to policy restriction
     * - aggregate OGC exception to send them in one XMl document to the client
     */
    protected WMTSProxyResponseBuilder docBuilder; 

    /**
     * Exception manager object :
     * - use to build OGC exception according to scpecification
     */
    protected OWSExceptionManager owsExceptionManager;

    /**
     * Fill by the WMTSProxyServletGetCapabilitiesThread with
     * <alias,path>
     */
    public HashMap<String, String> wmtsGetCapabilitiesResponseFilePathMap = new HashMap<String, String>();

    /**
     * 
     */
    public WMTSProxyServlet(ProxyServletRequest proxyRequest, SdiVirtualservice virtualService, SdiPolicy policy) {
		super(proxyRequest, virtualService, policy);
    }

    /**
     * @return the proxyRequest
     */
    public WMTSProxyServletRequest getProxyRequest() {
	return (WMTSProxyServletRequest)proxyRequest;
    }

    /* (non-Javadoc)
     * @see org.easysdi.proxy.core.ProxyServlet#requestPreTreatmentPOST(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    protected void requestPreTreatmentPOST(HttpServletRequest req,HttpServletResponse resp) {
    	requestPreTreatmentGET(req,resp);
    }

    /* (non-Javadoc)
     * @see org.easysdi.proxy.core.ProxyServlet#requestPreTreatmentGET(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    protected void requestPreTreatmentGET(HttpServletRequest req,HttpServletResponse resp) {
		try
		{
		    
		    Method preTreatmentMethod = this.getClass().getMethod("requestPreTreatment"+getProxyRequest().getOperation(), new Class [] {Class.forName ("javax.servlet.http.HttpServletRequest"), Class.forName ("javax.servlet.http.HttpServletResponse")});
		    preTreatmentMethod.invoke(this ,new Object[] {req,resp});
	
		}
		catch (Exception e)
		{
		    resp.setHeader("easysdi-proxy-error-occured", "true");
		    e.printStackTrace();
		    logger.error( e.toString());
		    try {
		    	owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
		    } catch (IOException e1) {
				logger.error( e1.toString());
				e1.printStackTrace();
		    }
		    return;
		}
    }

    /**
     * @param req
     * @param resp
     */
    public void requestPreTreatmentGetCapabilities (HttpServletRequest req, HttpServletResponse resp){
	try{
	    //Servers define in config.xml
	    Hashtable<String, RemoteServerInfo> htRemoteServer = getPhysicalServiceHastable();
	    List<WMTSProxyServerGetCapabilitiesThread> serverThreadList = new Vector<WMTSProxyServerGetCapabilitiesThread>();


	    Enumeration<RemoteServerInfo> enumRS =  htRemoteServer.elements();
	    while (enumRS.hasMoreElements())
	    {
		RemoteServerInfo rs = (RemoteServerInfo)enumRS.nextElement();

		String requestContent=null;
		if(getProxyRequest().getRequest().getMethod().equalsIgnoreCase("GET"))
		    requestContent = getProxyRequest().getUrlParameters();
		else
		    requestContent = getProxyRequest().getBodyRequest().toString();

		WMTSProxyServerGetCapabilitiesThread s = new WMTSProxyServerGetCapabilitiesThread( this,requestContent,rs , resp);
		s.start();
		serverThreadList.add(s);
	    }

	    for (int i = 0; i < serverThreadList.size(); i++) {
		serverThreadList.get(i).join();
	    }
	    if (wmtsGetCapabilitiesResponseFilePathMap.size() > 0) {
		//Post treatment
		logger.trace("requestPreTraitementGET begin transform");
		transformGetCapabilities(requestedVersion, getProxyRequest().getOperation(), req, resp);
		logger.trace("requestPreTraitementGET end transform");
	    } else {
		StringBuffer out;
		try {
		    logger.error(OWSExceptionReport.TEXT_NO_RESULT_RECEIVED_BY_PROXY);
		    owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_NO_RESULT_RECEIVED_BY_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
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
	    	owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
	    } catch (IOException e1) {
	    	logger.error( OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
	    }
	    return;
	}
    }

    public void requestPreTreatmentGetTile (HttpServletRequest req, HttpServletResponse resp){
	try{
	    //Servers define in config.xml
	    Hashtable<String, RemoteServerInfo> htRemoteServer = getPhysicalServiceHastable();

	    //Find the remote server concerning by the current request
	    ProxyLayer pLayer = new ProxyLayer(((WMTSProxyServletRequest)getProxyRequest()).getLayer());

	    if(pLayer.getAlias() == null)
	    {
			logger.error( OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName());
			owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName(),OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "LAYER", HttpServletResponse.SC_BAD_REQUEST);
			return;
	    }
	    RemoteServerInfo RS = (RemoteServerInfo)htRemoteServer.get(pLayer.getAlias());

	    //Check layer
	    if( RS == null ){
	    	owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName(),OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "LAYER", HttpServletResponse.SC_BAD_REQUEST);
		return;
	    }

	    //Check if the requested tile is allowed
	    String tileAllowed = isTileAllowed((WMTSProxyServletRequest)getProxyRequest(), RS.getUrl());
	    if(!tileAllowed.equalsIgnoreCase("true")){
		//the tile is not allowed, a blank image is returned.
		logger.debug("WMTSProxyServlet.requestPreTreatmentGetTile : tile is not allowed, generate an empty image");
		BufferedImage imgOut = generateEmptyImage(RS.getUrl(), getProxyRequest().getFormat());
		if(imgOut != null){
		    Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(getProxyRequest().getFormat());
		    ByteArrayOutputStream out = new ByteArrayOutputStream();
		    if (iter.hasNext()) {
			ImageWriter writer = (ImageWriter) iter.next();
			writer.setOutput(javax.imageio.ImageIO.createImageOutputStream(out));
			writer.write(imgOut);
			writer.dispose();
		    }
		    sendHttpServletResponse(req, resp,out,getProxyRequest().getFormat(), HttpServletResponse.SC_OK);
		}else{
		    ByteArrayOutputStream out = null;
		    sendHttpServletResponse(req, resp,out,getProxyRequest().getFormat(), HttpServletResponse.SC_OK);
		}
		
		return;
	    }

	    String requestContent=null;
	    if(getProxyRequest().getRequest().getMethod().equalsIgnoreCase("GET"))
		requestContent = getProxyRequest().getUrlParameters();
	    else
		requestContent = getProxyRequest().getBodyRequest().toString();

	    sendDataDirectStream(resp,getProxyRequest().getRequest().getMethod(), RS.getUrl(), requestContent);
	    return;

	}catch(Exception e){
	    resp.setHeader("easysdi-proxy-error-occured", "true");
	    logger.error( configuration.getServletClass() + ".requestPreTreatmentGetCapabilities : ", e);
	    StringBuffer out;
	    try {
	    	owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
	    } catch (IOException e1) {
	    	logger.error( OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
	    }
	    return;
	}
    }

    public void requestPreTreatmentGetFeatureInfo (HttpServletRequest req, HttpServletResponse resp){
	try{
	    //Servers define in config.xml
	    Hashtable<String, RemoteServerInfo> htRemoteServer = getPhysicalServiceHastable();

	    //Find the remote server concerning by the current request
	    ProxyLayer pLayer = new ProxyLayer(((WMTSProxyServletRequest)getProxyRequest()).getLayer());

	    if(pLayer.getAlias() == null)
	    {
			logger.error( OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName());
			owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName(),OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "LAYER", HttpServletResponse.SC_BAD_REQUEST);
			return;
	    }
	    RemoteServerInfo RS = (RemoteServerInfo)htRemoteServer.get(pLayer.getAlias());

	    //Check layer
	    if( RS == null ){
	    	owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName(),OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "LAYER", HttpServletResponse.SC_BAD_REQUEST);
		return;
	    }

	    //Check if the requested tile is allowed
	    String tileAllowed = isTileAllowed((WMTSProxyServletRequest)getProxyRequest(), RS.getUrl());
	    if(!tileAllowed.equalsIgnoreCase("true")){
		//The tile is not allowed, an empty response is sent
		ByteArrayOutputStream out = null;
		sendHttpServletResponse(req, resp,out,getProxyRequest().getInfoFormat(), HttpServletResponse.SC_OK);
		return;
	    }

	    if((getConfiguration().getXsltPath() == null||getConfiguration().getXsltPath().trim() =="" )){
		sendDataDirectStream(resp,"GET", RS.getUrl(), getProxyRequest().getUrlParameters());
		return;
	    }else{
		String tempFile = sendData("GET", RS.getUrl(), getProxyRequest().getUrlParameters());
		transformGetFeatureInfo(req, resp, tempFile);
	    }
	}catch(Exception e){
	    resp.setHeader("easysdi-proxy-error-occured", "true");
	    logger.error( configuration.getServletClass() + ".requestPreTreatmentGetFeatureInfo : ", e);
	    StringBuffer out;
	    try {
	    	owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
	    } catch (IOException e1) {
		logger.error( OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
	    }
	    return;
	}
    }


    /**
     * @param version
     * @param operation
     * @param req
     * @param resp
     */
    public void transformGetCapabilities(String version, String operation, HttpServletRequest req, HttpServletResponse resp) {

	try{
	    //Get the responses which are OGC exception (XML)
	    HashMap<String, String> remoteServerExceptionFiles = owsExceptionManager.getRemoteServerExceptionResponse(this,wmtsGetCapabilitiesResponseFilePathMap);

	    //If the Exception mode is 'restrictive' and at least a response is an exception
	    //Or if the Exception mode is 'permissive' and all the response are exceptio
	    //Aggegate the exception files and send the result to the client
	    if((remoteServerExceptionFiles.size() > 0 && configuration.getExceptionMode().equals("restrictive")) ||  
		    (wmtsGetCapabilitiesResponseFilePathMap.size() == 0)){
		logger.info("Exception(s) returned by remote server(s) are sent to client.");
		ByteArrayOutputStream exceptionOutputStream = docBuilder.ExceptionAggregation(remoteServerExceptionFiles);
		sendHttpServletResponse(req,resp,exceptionOutputStream, "text/xml; charset=utf-8", responseStatusCode);
		return;
	    }

	    ByteArrayOutputStream tempOut = new ByteArrayOutputStream(); 

	    if ("GetCapabilities".equalsIgnoreCase(operation)) 
	    {
		RemoteServerInfo rs = getRemoteServerInfoMaster();

		if(!docBuilder.CapabilitiesContentsFiltering(wmtsGetCapabilitiesResponseFilePathMap))
		{
		    logger.error(docBuilder.getLastException().toString());
		    owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
		    return;
		}

		if(!docBuilder.CapabilitiesOperationsFiltering(wmtsGetCapabilitiesResponseFilePathMap.get(rs.getAlias()), getServletUrl(req)))
		{
		    logger.error(docBuilder.getLastException().toString());
		    owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
		    return;
		}

		if(!docBuilder.CapabilitiesMerging(wmtsGetCapabilitiesResponseFilePathMap))
		{
		    logger.error(docBuilder.getLastException().toString());
		    owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
		    return;
		}

		if(!docBuilder.CapabilitiesServiceMetadataWriting(wmtsGetCapabilitiesResponseFilePathMap.get(rs.getAlias()),getServletUrl(req)))
		{
		    logger.error(docBuilder.getLastException().toString());
		    owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
		    return;
		}

		FileInputStream reader = new FileInputStream(new File(wmtsGetCapabilitiesResponseFilePathMap.get(rs.getAlias())));
		byte[] data = new byte[reader.available()];
		reader.read(data, 0, reader.available());
		tempOut.write(data);
		reader.close();
		//Send response to client after applying XSLT transformation id needed
		sendHttpServletResponse(req,resp, applyUserXSLT(tempOut),responseContentType, responseStatusCode);
	    }
	}
	catch (Exception e){
	    e.printStackTrace();
	    logger.error( e.toString());
	    resp.setHeader("easysdi-proxy-error-occured", "true");
	    StringBuffer out;
	    try {
	    	owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
	    } catch (IOException e1) {
		e1.printStackTrace();
		logger.error( e1.toString());
	    }
	}
    }

    public void transformGetFeatureInfo (HttpServletRequest req, HttpServletResponse resp, String responseFile){
	try{
	    FileInputStream fis = new FileInputStream(responseFile);
	    ByteArrayOutputStream bos = new ByteArrayOutputStream();
	    byte[] buf = new byte[1024];
	    for (int readNum; (readNum = fis.read(buf)) != -1;) {
		bos.write(buf, 0, readNum); 
	    }
	    //If the response is an OGC exception, the XSLT transformation is not applied
	    //the response is send to the client
	    if(isRemoteServerResponseException(responseFile)){
		logger.info("Exception returned by remote server is sent to client.");
		sendHttpServletResponse(req,resp,bos, "text/xml; charset=utf-8", responseStatusCode);
		return;
	    }

	    //apply XSLT transformation if needed before send back to client the response
	    sendHttpServletResponse(req,resp, applyUserXSLT(bos),responseContentType, responseStatusCode);
	}catch (Exception e){
	    resp.setHeader("easysdi-proxy-error-occured", "true");
	    logger.error(configuration.getServletClass() + ".transformGetFeatureInfo: ", e);
	    StringBuffer out;
	    try {
	    	owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
	    } catch (IOException e1) {
		logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
	    }
	    return;
	}
    }

    /**
     * To Replace by the use of OWSExceptionReport
     */
    @Override
    @Deprecated
    protected StringBuffer generateOgcException(String errorMessage, String code, String locator, String version) {
	return null;
    }	

    /**
     * return if the tile is allowed or not according to the policy restriction
     * @param proxyRequest : the request object
     * @param serverUrl : url of the remote server
     * @return 
     */
    protected String isTileAllowed (WMTSProxyServletRequest proxyRequest, String serverUrl){
	ProxyLayer pLayer = proxyRequest.getpLayer();
	WMTSProxyTileMatrixSet pTileMatrixSet = proxyRequest.getpTileMatrixSet();
	String tileMatrix = proxyRequest.getTileMatrix();
	Integer tileRow = Integer.parseInt( proxyRequest.getTileRow());
	Integer tileCol = Integer.parseInt( proxyRequest.getTileCol());

	

	//If one of the mandatory parameters is missing, return false
	if (pLayer == null) 
	    return "Layer";
	if (pTileMatrixSet == null )
	    return "TileMatrixSet";
	if(tileMatrix == null)
	    return "tileMatrix";
	if( tileRow == null ) 
	    return "tileRow";
	if (tileCol == null)
	    return "tileCol";

	if(policy.getServers().isAll())
	    return "true";

	List<Server> serverList = policy.getServers().getServer();
	boolean isServerFound = false;
	for (int i = 0; i < serverList.size(); i++) {
	    if (serverUrl.equalsIgnoreCase(serverList.get(i).getUrl())) {
		isServerFound = true;
		if (serverList.get(i).getLayers().isAll())
		    return "true";
		List<Layer> layerList = serverList.get(i).getLayers().getLayer();
		boolean isLayerFound = false;
		for (int j = 0; j < layerList.size(); j++) {
		    if (pLayer.getPrefixedName().equals(layerList.get(j).getName())){
			isLayerFound = true;
			if( layerList.get(j).isAll())
			    return "true";
			List<TileMatrixSet> lTileMatrixSet = layerList.get(j).getTileMatrixSet();
			boolean isTileMatrixSetFound = false;
			for(int k = 0 ; k <lTileMatrixSet.size() ; k++){
			    if(lTileMatrixSet.get(k).getId().equals(pTileMatrixSet.getName())){
				isTileMatrixSetFound = true;
				if(lTileMatrixSet.get(k).isAll())
				    return "true";
				List<TileMatrix> lTileMatrix = lTileMatrixSet.get(k).getTileMatrix();
				boolean isTileMatrixFound = false; 
				for(int l = 0 ; l < lTileMatrix.size(); l++){
				    if(lTileMatrix.get(l).getId().equals(tileMatrix)){
					isTileMatrixFound = true;
					if(lTileMatrix.get(l).isAll())
					    return "true";
					Integer minCol = Integer.parseInt( lTileMatrix.get(l).getTileMinCol() );
					Integer maxCol = Integer.parseInt(lTileMatrix.get(l).getTileMaxCol());
					Integer minRow = Integer.parseInt(lTileMatrix.get(l).getTileMinRow());
					Integer maxRow = Integer.parseInt(lTileMatrix.get(l).getTileMaxRow());
					if(tileRow <= maxRow && tileRow >= minRow && tileCol <= maxCol && tileCol >= minCol )
					    return "true";
					else{
					    if(tileRow > maxRow || tileRow < minRow)
						return "tileRow";
					    if(tileCol > maxCol || tileCol < minCol)
						return "tileCol";
					}
				    }
				}
				if(!isTileMatrixFound){
				    //TileMatrix is not allowed
				    return "TileMatrix";
				}
			    }
			}
			if(!isTileMatrixSetFound){
			    //tileMatrixSet is not allowed
			    return "TileMatrixSet";
			}
		    }
		}
		if(!isLayerFound){
		    //layer is not allowed
		    return "Layer";
		}
	    }
	}
	if(!isServerFound){
	    //Server is not allowed
	    return "Server";
	}
	return "false";
    }

    /**
     * Detects if the layer is allowed or not against the rule.
     * Overwrite the generic ProxyServlet method because in WMTS policy, 
     * when all layer are allowed for a physical service or all physical services are allowed
     * the name of the layer are not stored.
     * This is a difference with the others policy, which contain all the layer name allowed 
     * @param layer
     *            The layer to test
     * @param url
     *            the url of the remote server.
     * @return true if the layer is allowed, false if not
     */
    public boolean isLayerAllowed(String layer, String url) 
    {
    	if (layer == null)
		    return false;
	
    	if(sdiPolicy.isAnyservice())
    		return true;
    	
    	Set<SdiPhysicalservicePolicy> physicalservicePolicies = sdiPolicy.getSdiPhysicalservicePolicies();
    	Iterator<SdiPhysicalservicePolicy> i = physicalservicePolicies.iterator();
    	while(i.hasNext())
    	{
    		SdiPhysicalservicePolicy physicalservicePolicy = i.next();
    		if(physicalservicePolicy.getSdiPhysicalservice().getResourceurl().equals(url))
    		{
    			if(physicalservicePolicy.isAnyitem())
    				return true;
    			Set<SdiWmtslayerPolicy> wmtsLayerPolicies = physicalservicePolicy.getSdiWmtslayerPolicies();
	    		Iterator<SdiWmtslayerPolicy> it = wmtsLayerPolicies.iterator();
	    		while (it.hasNext())
	    		{
	    			SdiWmtslayerPolicy layerPolicy = it.next();
	    			if(layerPolicy.getIdentifier().equals(layer) && layerPolicy.isEnabled())
	    				return true;
	    		}
	    		break;
    		}
    	}
    	return false;
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
    @SuppressWarnings({ "unchecked" })
    private BufferedImage generateEmptyImage(String remoteServerUrl, String format) {
	try {
	    String encoding = null;
	    if (getUsername(remoteServerUrl) != null && getPassword(remoteServerUrl) != null) {
		String userPassword = getUsername(remoteServerUrl) + ":" + getPassword(remoteServerUrl);
		encoding = new sun.misc.BASE64Encoder().encode(userPassword.getBytes());

	    }
	    URL url = new URL(remoteServerUrl+"?request=GetCapabilities&service=WMTS");
	    HttpURLConnection hpcon = null;
	    hpcon = (HttpURLConnection) url.openConnection();
	    hpcon.setRequestMethod("GET");
	    if (encoding != null) {
		hpcon.setRequestProperty("Authorization", "Basic " + encoding);
	    }
	    hpcon.setUseCaches(false);
	    hpcon.setDoInput(true);
	    hpcon.setDoOutput(false);

	    InputStream in = null;
	    in = hpcon.getInputStream();

	    String width = null;
	    String height = null;

	    SAXBuilder sxb = new SAXBuilder();
	    Document  docParent = sxb.build(in);
	    Element racine = docParent.getRootElement();
	    Namespace nsWMTS = racine.getNamespace();
	    Namespace nsOWS = null;
	    List<Namespace> lns = racine.getAdditionalNamespaces();
	    Iterator<Namespace> ilns = lns.iterator();
	    while (ilns.hasNext())
	    {
		Namespace ns = (Namespace)ilns.next();
		if(ns.getPrefix().equalsIgnoreCase("ows"))
		    nsOWS = ns;
	    }
	    Element contentsChild = (Element)racine.getChild("Contents", nsWMTS);
	    Iterator<Element> itmschild = contentsChild.getDescendants(new ElementTileMatrixSetFilter());
	    while (itmschild.hasNext())
	    {
		Element tms = (Element)itmschild.next();
		Element tmsId = tms.getChild("Identifier", nsOWS);
		if(tmsId.getValue().equalsIgnoreCase(getProxyRequest().getpTileMatrixSet().getName())){
		    Iterator<Element> ltm = tms.getChildren("TileMatrix", nsWMTS).iterator();
		    while (ltm.hasNext()){
			Element tm = ltm.next();
			Element tmId = tm.getChild("Identifier", nsOWS);
			if(tmId.getValue().equalsIgnoreCase(getProxyRequest().getTileMatrix())){
			    width = tm.getChild("TileWidth", nsWMTS).getValue();
			    height = tm.getChild("TileHeight", nsWMTS).getValue();
			    break;
			}
		    }
		}
	    }

	    BufferedImage imgOut = null;

	    imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.BITMASK);

	    return imgOut;
	} catch (Exception e) {
	    logger.error("GenerateEmptyImage: ",e);
	    return null;
	} 

    }

}
