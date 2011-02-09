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
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;
import java.util.Enumeration;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;
import java.util.Vector;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.easysdi.jdom.filter.ElementExceptionReportFilter;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.jdom.*;
import org.jdom.input.SAXBuilder;
import com.google.common.collect.HashMultimap;
import com.google.common.collect.Multimap;


/**
 * @author Depth SA
 *
 */
public class WMTSProxyServlet extends ProxyServlet{

	private static final long serialVersionUID = 1982682293133286643L;
	protected WMTSProxyResponseBuilder docBuilder; 
	
	public WMTSProxyServlet() {
		super();
		
	}

	@Override
	protected void requestPreTreatmentPOST(HttpServletRequest req,
			HttpServletResponse resp) {
		// Auto-generated method stub
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
				else{
					paramUrlBase = paramUrlBase + key + "=" + value + "&";
				}
				
				if (key.equalsIgnoreCase("service") )
				{
					service = req.getParameter(key);
					if(!service.equalsIgnoreCase("WMTS"))
					{
						dump("ERROR", "Service requested is not WMTS.");
						ByteArrayOutputStream out = generateProxyOgcException("Operation request contains an invalid parameter value.","InvalidParameterValue","service","1.0.0");
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
						dump("ERROR", "WMTS requested version is not supported.");
						ByteArrayOutputStream out = generateProxyOgcException("Version not supported.","InvalidParameterValue","acceptVersions","1.0.0");
						sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_BAD_REQUEST);
						return;
					}
				}
				else if (key.equalsIgnoreCase("version"))
				{
					version = req.getParameter(key);
					if(!version.equalsIgnoreCase("1.0.0"))
					{
						dump("ERROR", "WMTS requested version is not supported.");
						ByteArrayOutputStream out = generateProxyOgcException("Version not supported.","InvalidParameterValue","version","1.0.0");
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
			if(handleNotAllowedOperation(request,resp))
				return;
			
			/**
			 * 
			 * @author Depth SA
			 *
			 */
			class RemoteServerThread extends Thread {
				String paramUrl;
				int remoteServerIndex;
				HttpServletResponse resp;

				public RemoteServerThread(	String pParamUrl, int pRemoteServerIndex, HttpServletResponse response) {
					paramUrl = pParamUrl;
					remoteServerIndex = pRemoteServerIndex;
					resp = response;
				}
				
				public void run() {
					String remoteServerUrl =  getRemoteServerUrl(remoteServerIndex);
					try {
						
						dump("DEBUG", "Thread Server: " + remoteServerUrl + " work begin");
						String filePath = sendData("GET", remoteServerUrl, paramUrl);
						synchronized (wmtsFilePathList) {
							dump("requestPreTraitementGET save response from thread server " + remoteServerUrl);
							wmtsFilePathList.put(remoteServerIndex, filePath);
						}
						dump("DEBUG", "Thread Server: " + remoteServerUrl + " work finished");
					}
					catch (Exception e)
					{
						e.printStackTrace();
						resp.setHeader("easysdi-proxy-error-occured", "true");
						dump("ERROR", "Server Thread " + remoteServerUrl+ " :" + e.getMessage());
						ByteArrayOutputStream out = generateProxyOgcException("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion);
						sendHttpServletResponse(null, resp,out,"text/xml", HttpServletResponse.SC_BAD_REQUEST);
						return;
					}
				}
			}
			
			//Servers define in config.xml
			List<RemoteServerInfo> remoteServerList = getRemoteServerInfoList();
			List<RemoteServerThread> serverThreadList = new Vector<RemoteServerThread>();

			if (request.equalsIgnoreCase("getcapabilities")) {
				for (int iRS = 0; iRS < remoteServerList.size(); iRS++) {
					RemoteServerThread s = new RemoteServerThread( paramUrlBase, iRS, resp);
					s.start();
					serverThreadList.add(s);
				}

			}
			else if(request.equalsIgnoreCase("gettile"))
			{
				//Check layer
				if(!isLayerAllowed(layer)){
					ByteArrayOutputStream out = generateProxyOgcException("Invalid layer name given in the LAYER parameter : "+layer,"InvalidParameterValue","layer",requestedVersion);
					sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_BAD_REQUEST);
					return;
				}
				//Find the remote server which is storing the requested layer.
				for (int iRS = 0; iRS < remoteServerList.size(); iRS++) {
					String remoteServerUrl = remoteServerList.get(iRS).getUrl();
					if(isLayerAllowed(layer, remoteServerUrl))
					{
						sendDataStream(resp,"GET", remoteServerUrl, paramUrlBase);
						return;
					}
				}
			}
			
			for (int i = 0; i < serverThreadList.size(); i++) {
				serverThreadList.get(i).join();
			}
			if (wmtsFilePathList.size() > 0) {
				//Post treatment
				dump("requestPreTraitementGET begin transform");
				transform(requestedVersion, request, req, resp);
				dump("requestPreTraitementGET end transform");
			} else {
				dump("INFO", "This request has no authorized results! Generate an empty response.");
				sendProxyBuiltInResponse(resp,generateEmptyResponse(request));
			}
		}
		catch (Exception e)
		{
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.toString());
			ByteArrayOutputStream out = generateProxyOgcException("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion);
			sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
			return;
		}
		
	}
	
	public void transform(String version, String operation, HttpServletRequest req, HttpServletResponse resp) {
		
		try{
		
			//Filter remote servers responses 
			Boolean asRemoteServerServiceException = filterServersResponsesForOgcServiceExceptionFiles();

			//Manage exception acccording to servlet configuration 
			if((configuration.getExceptionMode().equals("restrictive") && asRemoteServerServiceException) || 
					(configuration.getExceptionMode().equals("permissive") && wmtsFilePathList.size() == 0))
			{
				dump("INFO","Exception(s) returned by remote server(s) are sent to client.");
				responseContentType ="text/xml";
				sendHttpServletResponse(req,resp,buildResponseForRemoteOgcException(), "text/xml", responseStatusCode);
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
			
			if ("GetCapabilities".equalsIgnoreCase(operation)) {
				dump("INFO","transform - Start - Capabilities contents filtering");
				if(!docBuilder.CapabilitiesContentsFiltering(wmtsFilePathList))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					ByteArrayOutputStream out = generateProxyOgcException("Error in Capabilities layers filtering. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion);
					sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				dump("INFO","transform - End - Capabilities contents filtering");
				dump("INFO","transform - Start - Capabilities operations filtering");
				if(!docBuilder.CapabilitiesOperationsFiltering(wmtsFilePathList, getServletUrl(req)))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					ByteArrayOutputStream out = generateProxyOgcException("Error in Capabilities operations filtering. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion);
					sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				dump("INFO","transform - End - Capabilities operations filtering");
				
				dump("INFO","transform - Start - Capabilities merging");
				if(!docBuilder.CapabilitiesMerging(wmtsFilePathList))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					ByteArrayOutputStream out = generateProxyOgcException("Error in Capabilities merging. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion);
					sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				dump("INFO","transform - End - Capabilities merging");
				
				dump("INFO","transform - Start - Capabilities metadata writing");
				if(!docBuilder.CapabilitiesServiceIdentificationWriting(wmtsFilePathList,getServletUrl(req)))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					ByteArrayOutputStream out = generateProxyOgcException("Error in Capabilities Service Identification writing. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion);
					sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				dump("INFO","transform - End - Capabilities metadata writing");
				
				
				FileInputStream reader = new FileInputStream(new File(wmtsFilePathList.get(0).toArray(new String[1])[0]));
				byte[] data = new byte[reader.available()];
				reader.read(data, 0, reader.available());
				tempOut.write(data);
				reader.close();
				sendHttpServletResponse(req,resp, tempOut,responseContentType, responseStatusCode);
			}
			else if("GetTile".equalsIgnoreCase(operation))
			{
				dump("INFO","transform - Start - GetTile");
				FileInputStream reader = new FileInputStream(new File(wmtsFilePathList.get(0).toArray(new String[1])[0]));
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
			ByteArrayOutputStream out = generateProxyOgcException("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion);
			sendHttpServletResponse(req, resp,out,"text/xml", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
		}
		
	}

	/**
	 * Filter on the remote servers response files :
			- add exception files in ogcExceptionFilePathList
			- remove exception file from wmtsFilePathList 
	 */
	protected boolean filterServersResponsesForOgcServiceExceptionFiles () throws Exception
	{
		try
		{
			dump("DEBUG","filterServerResponseFile begin");
			
			Collection<Map.Entry<Integer,String>> r =  wmtsFilePathList.entries();
			Multimap<Integer,String> toRemove = HashMultimap.create();
			
			Iterator<Map.Entry<Integer, String>> it = r.iterator();
			while(it.hasNext())
			{
				Map.Entry<Integer,String> entry = (Map.Entry<Integer,String>)it.next();
				String path  = entry.getValue();
				if(path == null || path.length() == 0)
					continue;
				String ext = (path.lastIndexOf(".")==-1)?"":path.substring(path.lastIndexOf(".")+1,path.length());
				if (ext.equals("xml"))
				{
					SAXBuilder sxb = new SAXBuilder();
					Document documentMaster = sxb.build(new File(path));
					if (documentMaster != null) 
					{
						List<?> exceptionList = documentMaster.getContent(new ElementExceptionReportFilter());
						if(exceptionList.iterator().hasNext())
						{
							toRemove.put(entry.getKey(), path);
						}
					}
				}
			}
			
			if(toRemove.size() == 0)
				return false;
			
			Iterator<Map.Entry<Integer,String>> itR = toRemove.entries().iterator();
			while(itR.hasNext())
			{
				Map.Entry<Integer,String> entry = (Map.Entry<Integer,String>)itR.next();
				
				ogcExceptionFilePathList.put(entry.getKey(), entry.getValue());
				wmtsFilePathList.remove(entry.getKey(), entry.getValue());
			}
			
			dump("DEBUG","filterServerResponseFile end");
			return true;
		}
		catch (Exception ex)
		{
			ex.printStackTrace();
			dump("ERROR", ex.getMessage());
			throw ex;
		}
	}
	
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

	
	protected ByteArrayOutputStream generateProxyOgcException(String errorMessage, String code, String locator, String version) {
		dump("ERROR", errorMessage);
		ByteArrayOutputStream out = new ByteArrayOutputStream();
		String s = new String ();
		s = "<?xml version='1.0' encoding='utf-8'?>";
		s+= "\n<ExceptionReport xmlns=\"http://www.opengis.net/ows/1.1\" ";
		s+= "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ";
		s+= "xsi:schemaLocation=\"http://www.opengis.net/ows/1.1\" version=\"1.1.0\">";
		s+= "\n\t<Exception exceptionCode=\"";
		s+= code;
		s+= "\"";
		if(locator != null && locator != "" )
		{
			s+= " locator=\"";
			s+= locator;
			s+= "\"";
		}
		s+= ">";
		if( errorMessage != null && errorMessage.length()!= 0)
		{
			s+= "\n\t\t<ExceptionText>";
			s+= errorMessage;
			s+= "</ExceptionText>";
		}
		s+= "\n\t</Exception>";
		s+= "\n</ExceptionReport>";
		
		byte buf[] = s.getBytes(); 
		try {
			out.write(buf);
		} catch (IOException e) {
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}
		 
		return out;
	}
	protected StringBuffer generateEmptyResponse (String operation)
	{
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>");
		
		return sb;
	}
}
