package org.easysdi.proxy.wmts.v100;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.net.URLEncoder;
import java.util.Arrays;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.List;
import java.util.Vector;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.ows.v200.*;
import org.easysdi.proxy.wmts.*;
import org.easysdi.xml.documents.RemoteServerInfo;

public class WMTS100ProxyServlet extends WMTSProxyServlet{

	private static final long serialVersionUID = 9165610435888466203L;
	
	public WMTS100ProxyServlet() {
		super();
		ServiceOperations =  Arrays.asList( "GetCapabilities", "GetTile", "GetFeatureInfo" );
		ServiceSupportedOperations = Arrays.asList("GetCapabilities", "GetTile");
		docBuilder = new WMTS100ProxyResponseBuilder(this);
		owsExceptionManager = new OWS200ExceptionManager();
		owsExceptionReport = new WMTS100ExceptionReport();
	}
	
	@Override
	protected void requestPreTreatmentGET(HttpServletRequest req,
			HttpServletResponse resp) {
		try
		{
			String service = "";
			String request = "";
			String acceptVersions = "";
			String version ="";
			String sections = "";
			String updateSequence = "";
			String acceptFormats = "";
			String paramUrlBase = "";
			String layer = "";
			ProxyLayer pLayer = null;
			String style = "";
			String format ="";
			String tileMatrixSet = "";
			String tileMatrix = "";
			String tileRow = "";
			String tileCol = "";
			
			Enumeration<String> parameterNames = req.getParameterNames();
			while (parameterNames.hasMoreElements()) {
				String key = (String) parameterNames.nextElement();
				String value = null;
				
				value = URLEncoder.encode(req.getParameter(key), "UTF-8");
				if (key.equalsIgnoreCase("acceptVersions")){
					value = "1.0.0";
					paramUrlBase = paramUrlBase + key + "=" + value + "&";
				}
				else if (key.equalsIgnoreCase("Layer")){
					paramUrlBase = paramUrlBase + key + "=" +  new ProxyLayer(value).getPrefixedName() + "&" ;
				}
				else{
					paramUrlBase = paramUrlBase + key + "=" + value + "&";
				}
				
				if (key.equalsIgnoreCase("service") )
				{
					service = req.getParameter(key);
					if(!service.equalsIgnoreCase("WMTS"))
					{
						logger.info( "Service requested is not WMTS.");
						StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_SERVICE_NAME,OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"service");
						sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
						return;
					}
				}
				else if (key.equalsIgnoreCase("request"))
				{
					request = req.getParameter(key);
				}
				else if (key.equalsIgnoreCase("acceptVersions"))
				{
					acceptVersions = req.getParameter(key);
					if(acceptVersions.contains("1.0.0"))
					{
						acceptVersions = "1.0.0";
						requestedVersion = "1.0.0";
					}
					else
					{
						logger.info("WMTS requested version is not supported.");
						StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED,OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"acceptVersions");
						sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
						return;
					}
				}
				else if (key.equalsIgnoreCase("version"))
				{
					version = req.getParameter(key);
					if(!version.equalsIgnoreCase("1.0.0"))
					{
						logger.info( "WMTS requested version is not supported.");
						StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED,OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"version");
						sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
						return;
					}
				}
				else if (key.equalsIgnoreCase("sections"))
				{
					sections = req.getParameter(key);
				}
				else if (key.equalsIgnoreCase("updateSequence"))
				{
					updateSequence = req.getParameter(key);
				}
				else if (key.equalsIgnoreCase("acceptFormats"))
				{
					acceptFormats = req.getParameter(key);
				}
				else if (key.equalsIgnoreCase("Layer"))
				{
					layer = req.getParameter(key);
					pLayer = new ProxyLayer(layer);
					if(pLayer.getAlias() == null)
					{
						StringBuffer out = owsExceptionReport.generateExceptionReport("Invalid layer name given in the LAYER parameter : "+layer,OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"layer");
						sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
						return;
					}
				}
				else if (key.equalsIgnoreCase("Style"))
				{
					style = req.getParameter(key);
				}
				else if (key.equalsIgnoreCase("Format"))
				{
					format = req.getParameter(key);
				}
				else if (key.equalsIgnoreCase("TileMatrixSet"))
				{
					tileMatrixSet = req.getParameter(key);
				}
				else if (key.equalsIgnoreCase("TileMatrix"))
				{
					tileMatrix = req.getParameter(key);
				}
				else if (key.equalsIgnoreCase("TileRow"))
				{
					tileRow = req.getParameter(key);
				}
				else if (key.equalsIgnoreCase("TileCol"))
				{
					tileCol = req.getParameter(key);
				}
			}
			
			//Generate OGC exception and send it to the client if current request operation is not allowed
			if(!isOperationSupportedByProxy(request))
			{
				logger.info( "Operation not supported.");
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED ,OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED,"request");
				sendHttpServletResponse(null, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_NOT_IMPLEMENTED);
				return;
			}
			if(!isOperationAllowedByPolicy(request))
			{
				logger.info("Operation not allowed.");
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_OPERATION_NOT_ALLOWED ,OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED,"request");
				sendHttpServletResponse(null, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_NOT_IMPLEMENTED);
				return;
			}
			
			/**
			 * 
			 * @author Depth SA
			 *
			 */
			class RemoteServerThread extends Thread {
				String paramUrl;
				RemoteServerInfo remoteServer;
				HttpServletResponse resp;

				public RemoteServerThread(	String pParamUrl, RemoteServerInfo pRemoteServer, HttpServletResponse response) {
					paramUrl = pParamUrl;
					remoteServer = pRemoteServer;
					resp = response;
				}
				
				public void run() {
					try {
						
						logger.trace( "Thread Server: " + remoteServer.getUrl() + " work begin");
						String filePath = sendData("GET", remoteServer.getUrl(), paramUrl);
						synchronized (wmtsFilePathTable) {
							logger.trace("requestPreTraitementGET save response from thread server " + remoteServer.getUrl());
							wmtsFilePathTable.put(remoteServer.getAlias(), filePath);
						}
						logger.trace( "Thread Server: " + remoteServer.getUrl() + " work finished");
					}
					catch (Exception e)
					{
						e.printStackTrace();
						resp.setHeader("easysdi-proxy-error-occured", "true");
						logger.error( "Server Thread " + remoteServer.getUrl()+ " :" + e.getMessage());
						StringBuffer out;
						try {
							out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
							sendHttpServletResponse(null, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
						} catch (IOException e1) {
							logger.error( e1.toString());
							e1.printStackTrace();
						}
						return;
					}
				}
			}
			
			//Servers define in config.xml
			Hashtable<String, RemoteServerInfo> htRemoteServer = getRemoteServerHastable();
			List<RemoteServerThread> serverThreadList = new Vector<RemoteServerThread>();

			if (request.equalsIgnoreCase("getcapabilities")) {
				Enumeration<RemoteServerInfo> enumRS =  htRemoteServer.elements();
				while (enumRS.hasMoreElements())
				{
					RemoteServerInfo rs = (RemoteServerInfo)enumRS.nextElement();
					RemoteServerThread s = new RemoteServerThread( paramUrlBase,rs , resp);
					s.start();
					serverThreadList.add(s);
				}
			}
			else if(request.equalsIgnoreCase("gettile"))
			{
				//Find the remote server concerning by the current request
				RemoteServerInfo RS = (RemoteServerInfo)htRemoteServer.get(pLayer.getAlias());
				
				//Check layer
				if( RS == null || !isLayerAllowed(pLayer.getPrefixedName(), RS.getUrl())){
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME+layer,OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"layer");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
					return;
				}
				
				sendDataDirectStream(resp,"GET", RS.getUrl(), paramUrlBase);
				return;
			}
			
			for (int i = 0; i < serverThreadList.size(); i++) {
				serverThreadList.get(i).join();
			}
			if (wmtsFilePathTable.size() > 0) {
				//Post treatment
				logger.trace("requestPreTraitementGET begin transform");
				transform(requestedVersion, request, req, resp);
				logger.trace("requestPreTraitementGET end transform");
			} else {
				logger.info( "This request has no authorized results. Generate an empty response.");
				sendProxyBuiltInResponse(resp,generateEmptyResponse(request));
			}
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
}
