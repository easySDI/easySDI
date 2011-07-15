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

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.lang.reflect.Method;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.List;
import java.util.Vector;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.ows.OWSExceptionManager;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.policy.Layer;
import org.easysdi.proxy.policy.Server;
import org.easysdi.proxy.wmts.thread.WMTSProxyServerGetCapabilitiesThread;
import org.easysdi.xml.documents.RemoteServerInfo;

/**
 * @author Depth SA
 *
 */
public class WMTSProxyServlet extends ProxyServlet{

	private static final long serialVersionUID = 1982682293133286643L;
	protected WMTSProxyResponseBuilder docBuilder; 
	protected OWSExceptionManager owsExceptionManager;
	
	/**
	 * Fill by the WMTSProxyServletGetCapabilitiesThread with
	 * <alias,path>
	 */
	public HashMap<String, String> wmtsGetCapabilitiesResponseFilePathMap = new HashMap<String, String>();
		
	public WMTSProxyServlet() {
		super();
		
	}

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyServlet#requestPreTreatmentPOST(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
	 */
	@Override
	protected void requestPreTreatmentPOST(HttpServletRequest req,HttpServletResponse resp) {
		StringBuffer out;
		try {
			logger.info("HTTP POST method is not supported.");
			out = owsExceptionReport.generateExceptionReport("HTTP POST method is not supported.",OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
			sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
		} catch (IOException e) {
			logger.error( e.toString());
			e.printStackTrace();
		}
		
	}

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyServlet#requestPreTreatmentGET(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
	 */
	@Override
	protected void requestPreTreatmentGET(HttpServletRequest req,HttpServletResponse resp) {
		try
		{
			//Generate OGC exception and send it to the client if current request operation is not allowed
			if(!isOperationSupportedByProxy(getProxyRequest().getOperation()))
			{
				logger.error(OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED);
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED ,OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED,"request");
				sendHttpServletResponse(null, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
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
			
		}
		catch (Exception e)
		{
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			logger.error( e.toString());
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
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
			Hashtable<String, RemoteServerInfo> htRemoteServer = getRemoteServerHastable();
			List<WMTSProxyServerGetCapabilitiesThread> serverThreadList = new Vector<WMTSProxyServerGetCapabilitiesThread>();

		
			Enumeration<RemoteServerInfo> enumRS =  htRemoteServer.elements();
			while (enumRS.hasMoreElements())
			{
				RemoteServerInfo rs = (RemoteServerInfo)enumRS.nextElement();
				WMTSProxyServerGetCapabilitiesThread s = new WMTSProxyServerGetCapabilitiesThread( this,getProxyRequest().getUrlParameters(),rs , resp);
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
	
	public void requestPreTreatmentGetTile (HttpServletRequest req, HttpServletResponse resp){
		try{
			//Servers define in config.xml
			Hashtable<String, RemoteServerInfo> htRemoteServer = getRemoteServerHastable();
			
			//Find the remote server concerning by the current request
			ProxyLayer pLayer = new ProxyLayer(((WMTSProxyServletRequest)getProxyRequest()).getLayer());
			
			if(pLayer.getAlias() == null)
			{
				logger.error( OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName());
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName(),OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"LAYER");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}
			RemoteServerInfo RS = (RemoteServerInfo)htRemoteServer.get(pLayer.getAlias());
			
			//Check layer
			if( RS == null || !isLayerAllowed(pLayer.getPrefixedName(), RS.getUrl())){
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName(),OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"LAYER");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}
			
			sendDataDirectStream(resp,"GET", RS.getUrl(), getProxyRequest().getUrlParameters());
			return;
			
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
	
	public void requestPreTreatmentGetFeatureInfo (HttpServletRequest req, HttpServletResponse resp){
		try{
			//Servers define in config.xml
			Hashtable<String, RemoteServerInfo> htRemoteServer = getRemoteServerHastable();
			
			//Find the remote server concerning by the current request
			ProxyLayer pLayer = new ProxyLayer(((WMTSProxyServletRequest)getProxyRequest()).getLayer());
			
			if(pLayer.getAlias() == null)
			{
				logger.error( OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName());
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName(),OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"LAYER");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}
			RemoteServerInfo RS = (RemoteServerInfo)htRemoteServer.get(pLayer.getAlias());
			
			//Check layer
			if( RS == null || !isLayerAllowed(pLayer.getPrefixedName(), RS.getUrl())){
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME+pLayer.getAliasName(),OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"LAYER");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
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
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
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
				sendHttpServletResponse(req,resp,exceptionOutputStream, "text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}
		
//			//Filter remote servers responses 
//			Boolean asRemoteServerServiceException = owsExceptionManager.filterResponseAndExceptionFiles(wmtsGetCapabilitiesResponseFilePathMap, ogcExceptionFilePathTable);
//
//			//Manage exception acccording to servlet configuration 
//			if((configuration.getExceptionMode().equals("restrictive") && asRemoteServerServiceException) || 
//					(configuration.getExceptionMode().equals("permissive") && wmtsFilePathTable.size() == 0))
//			{
//				logger.info("Exception(s) returned by remote server(s) are sent to client.");
//				responseContentType ="text/xml; charset=utf-8";
//				sendHttpServletResponse(req,resp,owsExceptionManager.buildResponseForRemoteOgcException(ogcExceptionFilePathTable), "text/xml; charset=utf-8", responseStatusCode);
//				return;
//			}
			
			// Vérifie et prépare l'application d'un fichier xslt utilisateur
//			String userXsltPath = getConfiguration().getXsltPath();
//			if (SecurityContextHolder.getContext().getAuthentication() != null) {
//				userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
//			}
//			
//			userXsltPath = userXsltPath + "/" + version + "/" + operation + ".xsl";
//			String globalXsltPath = getConfiguration().getXsltPath() + "/" + version + "/" + operation + ".xsl";
//			
//			File xsltFile = new File(userXsltPath);
//			boolean isPostTreat = false;
//			if (!xsltFile.exists()) {
//				dump("Postreatment file " + xsltFile.toString() + "does not exist");
//				xsltFile = new File(globalXsltPath);
//				if (xsltFile.exists()) {
//					isPostTreat = true;
//				} else {
//					dump("Postreatment file " + xsltFile.toString() + "does not exist");
//				}
//			} else {
//				isPostTreat = true;
//			}

			ByteArrayOutputStream tempOut = new ByteArrayOutputStream(); 
			
			if ("GetCapabilities".equalsIgnoreCase(operation)) 
			{
				RemoteServerInfo rs = getRemoteServerInfoMaster();
				
				if(!docBuilder.CapabilitiesContentsFiltering(wmtsGetCapabilitiesResponseFilePathMap))
				{
					logger.error(docBuilder.getLastException().toString());
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
					return;
				}
				
				if(!docBuilder.CapabilitiesOperationsFiltering(wmtsGetCapabilitiesResponseFilePathMap.get(rs.getAlias()), getServletUrl(req)))
				{
					logger.error(docBuilder.getLastException().toString());
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
					return;
				}
				
				if(!docBuilder.CapabilitiesMerging(wmtsGetCapabilitiesResponseFilePathMap))
				{
					logger.error(docBuilder.getLastException().toString());
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
					return;
				}
				
				if(!docBuilder.CapabilitiesServiceMetadataWriting(wmtsGetCapabilitiesResponseFilePathMap.get(rs.getAlias()),getServletUrl(req)))
				{
					logger.error(docBuilder.getLastException().toString());
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
					return;
				}
				
				FileInputStream reader = new FileInputStream(new File(wmtsGetCapabilitiesResponseFilePathMap.get(rs.getAlias())));
				byte[] data = new byte[reader.available()];
				reader.read(data, 0, reader.available());
				tempOut.write(data);
				reader.close();
				sendHttpServletResponse(req,resp, tempOut,responseContentType, responseStatusCode);
			}
		}
		catch (Exception e){
			e.printStackTrace();
			logger.error( e.toString());
			resp.setHeader("easysdi-proxy-error-occured", "true");
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
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
				sendHttpServletResponse(req,resp,bos, "text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}
			
			//apply XSLT transformation if needed before send back to client the response
			sendHttpServletResponse(req,resp, applyUserXSLT(bos),responseContentType, responseStatusCode);
			
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
	 * To Replace by the use of OWSExceptionReport
	 */
	@Override
	@Deprecated
	protected StringBuffer generateOgcException(String errorMessage, String code, String locator, String version) {
		return null;
	}	
	
	protected boolean isTileAllowed (WMTSProxyServletRequest proxyRequest, String serverUrl){
	    String layer = proxyRequest.getLayer();
	    String tileMatrixSet = proxyRequest.getTileMatrixSet();
	    String tileMatrix = proxyRequest.getTileMatrix();
	    String tileRow = proxyRequest.getTileRow();
	    String tileCol = proxyRequest.getTileCol();
	    
	    //If no policy loaded, return false
	    if (policy == null)
		return false;
	    
	    //If the policy period of validity is expired, return null
	    if (policy.getAvailabilityPeriod() != null) {
    		if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
    			return false;
	    }
    
	    //If one of the mandatory parameters is missing, return false
	    if (layer == null || tileMatrixSet == null || tileMatrix == null || tileRow == null || tileCol == null)
    		return false;
	    
	    if(policy.getServers().isAll())
		return true;
	    
	    List<Server> serverList = policy.getServers().getServer();
	    for (int i = 0; i < serverList.size(); i++) {
		if (serverUrl.equalsIgnoreCase(serverList.get(i).getUrl())) {
		    if (serverList.get(i).getLayers().isAll())
			return true;
		    List<Layer> layerList = serverList.get(i).getLayers().getLayer();
		    for (int j = 0; j < layerList.size(); j++) {
			if (layer.equals(layerList.get(j).getName()))
			   return true;
			}
		    }
		}
	    
	    return false;
	}
}
