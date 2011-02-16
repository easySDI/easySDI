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
import java.net.URLEncoder;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.List;
import java.util.Vector;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.easysdi.proxy.configuration.ProxyLayer;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.ows.OWSExceptionManager;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.ows.v200.OWS200ExceptionManager;
import org.easysdi.proxy.ows.v200.OWS200ExceptionReport;
import org.easysdi.xml.documents.RemoteServerInfo;

/**
 * @author Depth SA
 *
 */
public class WMTSProxyServlet extends ProxyServlet{

	private static final long serialVersionUID = 1982682293133286643L;
	protected WMTSProxyResponseBuilder docBuilder; 
	protected OWSExceptionManager owsExceptionManager;
	protected OWSExceptionReport owsExceptionReport;
	
	public WMTSProxyServlet() {
		super();
		
	}

	@Override
	protected void requestPreTreatmentPOST(HttpServletRequest req,
			HttpServletResponse resp) {
		ByteArrayOutputStream out;
		try {
			dump("INFO", "HTTP POST method is not supported.");
			out = owsExceptionReport.generateExceptionReport("HTTP POST method is not supported.","NoApplicableCode","");
			sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
		} catch (IOException e) {
			dump("ERROR", e.toString());
			e.printStackTrace();
		}
		
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
					paramUrlBase = paramUrlBase + key + "=" +  new ProxyLayer(value).getName() + "&" ;
				}
				else{
					paramUrlBase = paramUrlBase + key + "=" + value + "&";
				}
				
				if (key.equalsIgnoreCase("service") )
				{
					service = req.getParameter(key);
					if(!service.equalsIgnoreCase("WMTS"))
					{
						dump("INFO", "Service requested is not WMTS.");
						ByteArrayOutputStream out = owsExceptionReport.generateExceptionReport("Operation request contains an invalid parameter value.","InvalidParameterValue","service");
						sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_BAD_REQUEST);
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
						dump("INFO", "WMTS requested version is not supported.");
						ByteArrayOutputStream out = owsExceptionReport.generateExceptionReport("Version not supported.","InvalidParameterValue","acceptVersions");
						sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_BAD_REQUEST);
						return;
					}
				}
				else if (key.equalsIgnoreCase("version"))
				{
					version = req.getParameter(key);
					if(!version.equalsIgnoreCase("1.0.0"))
					{
						dump("INFO", "WMTS requested version is not supported.");
						ByteArrayOutputStream out = owsExceptionReport.generateExceptionReport("Version not supported.","InvalidParameterValue","version");
						sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_BAD_REQUEST);
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
			if(!isOperationSupported(request))
			{
				dump("INFO", "Operation not allowed.");
				ByteArrayOutputStream out = owsExceptionReport.generateExceptionReport("Operation not allowed.","OperationNotSupported","request");
				sendHttpServletResponse(null, resp,out,"text/xml", HttpServletResponse.SC_BAD_REQUEST);
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
						
						dump("DEBUG", "Thread Server: " + remoteServer.getUrl() + " work begin");
						String filePath = sendData("GET", remoteServer.getUrl(), paramUrl);
						synchronized (wmtsFilePathTable) {
							dump("requestPreTraitementGET save response from thread server " + remoteServer.getUrl());
							wmtsFilePathTable.put(remoteServer.getAlias(), filePath);
						}
						dump("DEBUG", "Thread Server: " + remoteServer.getUrl() + " work finished");
					}
					catch (Exception e)
					{
						e.printStackTrace();
						resp.setHeader("easysdi-proxy-error-occured", "true");
						dump("ERROR", "Server Thread " + remoteServer.getUrl()+ " :" + e.getMessage());
						ByteArrayOutputStream out;
						try {
							out = owsExceptionReport.generateExceptionReport("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","");
							sendHttpServletResponse(null, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
						} catch (IOException e1) {
							dump("ERROR", e1.toString());
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
				if(!isLayerAllowed(pLayer.getName(), RS.getUrl())){
					ByteArrayOutputStream out = owsExceptionReport.generateExceptionReport("Invalid layer name given in the LAYER parameter : "+layer,"InvalidParameterValue","layer");
					sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_BAD_REQUEST);
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
				dump("requestPreTraitementGET begin transform");
				transform(requestedVersion, request, req, resp);
				dump("requestPreTraitementGET end transform");
			} else {
				dump("INFO", "This request has no authorized results. Generate an empty response.");
				sendProxyBuiltInResponse(resp,generateEmptyResponse(request));
			}
		}
		catch (Exception e)
		{
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.toString());
			ByteArrayOutputStream out;
			try {
				out = owsExceptionReport.generateExceptionReport("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","");
				sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
			} catch (IOException e1) {
				dump("ERROR", e1.toString());
				e1.printStackTrace();
			}
			return;
		}
		
	}
	
	public void transform(String version, String operation, HttpServletRequest req, HttpServletResponse resp) {
		
		try{
		
			//Filter remote servers responses 
			Boolean asRemoteServerServiceException = owsExceptionManager.filterResponseAndExceptionFiles(wmtsFilePathTable, ogcExceptionFilePathTable);

			//Manage exception acccording to servlet configuration 
			if((configuration.getExceptionMode().equals("restrictive") && asRemoteServerServiceException) || 
					(configuration.getExceptionMode().equals("permissive") && wmtsFilePathTable.size() == 0))
			{
				dump("INFO","Exception(s) returned by remote server(s) are sent to client.");
				responseContentType ="text/xml";
				sendHttpServletResponse(req,resp,owsExceptionManager.buildResponseForRemoteOgcException(ogcExceptionFilePathTable), "text/xml", responseStatusCode);
				return;
			}

			
			
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
				
				if(!docBuilder.CapabilitiesContentsFiltering(wmtsFilePathTable))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					ByteArrayOutputStream out = owsExceptionReport.generateExceptionReport("Error in Capabilities layers filtering. Consult the proxy log for more details.","NoApplicableCode","");
					sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				
				if(!docBuilder.CapabilitiesOperationsFiltering(wmtsFilePathTable.get(rs.getAlias()), getServletUrl(req)))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					ByteArrayOutputStream out = owsExceptionReport.generateExceptionReport("Error in Capabilities operations filtering. Consult the proxy log for more details.","NoApplicableCode","");
					sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				
				if(!docBuilder.CapabilitiesMerging(wmtsFilePathTable))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					ByteArrayOutputStream out = owsExceptionReport.generateExceptionReport("Error in Capabilities merging. Consult the proxy log for more details.","NoApplicableCode","");
					sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				
				if(!docBuilder.CapabilitiesServiceIdentificationWriting(wmtsFilePathTable.get(rs.getAlias()),getServletUrl(req)))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					ByteArrayOutputStream out = owsExceptionReport.generateExceptionReport("Error in Capabilities Service Identification writing. Consult the proxy log for more details.","NoApplicableCode","");
					sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				
				FileInputStream reader = new FileInputStream(new File(wmtsFilePathTable.get(rs.getAlias())));
				byte[] data = new byte[reader.available()];
				reader.read(data, 0, reader.available());
				tempOut.write(data);
				reader.close();
				sendHttpServletResponse(req,resp, tempOut,responseContentType, responseStatusCode);
			}
		}
		catch (Exception e){
			e.printStackTrace();
			dump("ERROR", e.toString());
			resp.setHeader("easysdi-proxy-error-occured", "true");
			ByteArrayOutputStream out;
			try {
				out = owsExceptionReport.generateExceptionReport("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","");
				sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
			} catch (IOException e1) {
				e1.printStackTrace();
				dump("ERROR", e1.toString());
			}
		}
	}

	/**
	 * To Replace by the use of OWSExceptionReport
	 */
	@Override
	protected StringBuffer generateOgcException(String errorMessage, String code, String locator, String version) {
		dump("ERROR", errorMessage);
		
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>");
		sb.append("<ExceptionReport xmlns=\"http://www.opengis.net/ows/1.1\" " +
				"xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" " +
				"xsi:schemaLocation=\"http://www.opengis.net/ows/1.1\" version=\"");
		sb.append("1.1.0");
		sb.append("\">\n");
		sb.append("\t<Exception ");
		sb.append(" exceptionCode=\"");
		sb.append(code);
		sb.append("\"");
		if(locator != null && locator != "" )
		{
			sb.append(" locator=\"");
			sb.append(locator);
			sb.append("\"");
		}
		sb.append(">\n");
		if( errorMessage != null && errorMessage.length()!= 0)
		{
			sb.append("\t<ExceptionText>");
			sb.append(errorMessage);
			sb.append("</ExceptionText>\n");
		}
		sb.append("</Exception>\n");
		sb.append("</ExceptionReport>");

		return sb;
	}
	
	protected StringBuffer generateEmptyResponse (String operation)
	{
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>");
		
		return sb;
	}
}
