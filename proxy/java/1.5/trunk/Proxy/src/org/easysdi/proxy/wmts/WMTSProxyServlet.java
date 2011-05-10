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

import org.easysdi.proxy.core.ProxyLayer;
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
		
	public WMTSProxyServlet() {
		super();
		
	}

	@Override
	protected void requestPreTreatmentPOST(HttpServletRequest req,
			HttpServletResponse resp) {
		StringBuffer out;
		try {
			dump("INFO", "HTTP POST method is not supported.");
			out = owsExceptionReport.generateExceptionReport("HTTP POST method is not supported.",OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
			sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
		} catch (IOException e) {
			dump("ERROR", e.toString());
			e.printStackTrace();
		}
		
	}

	@Override
	protected void requestPreTreatmentGET(HttpServletRequest req,
			HttpServletResponse resp) {
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
				responseContentType ="text/xml; charset=utf-8";
				sendHttpServletResponse(req,resp,owsExceptionManager.buildResponseForRemoteOgcException(ogcExceptionFilePathTable), "text/xml; charset=utf-8", responseStatusCode);
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
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				
				if(!docBuilder.CapabilitiesOperationsFiltering(wmtsFilePathTable.get(rs.getAlias()), getServletUrl(req)))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				
				if(!docBuilder.CapabilitiesMerging(wmtsFilePathTable))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
					return;
				}
				
				if(!docBuilder.CapabilitiesServiceIdentificationWriting(wmtsFilePathTable.get(rs.getAlias()),getServletUrl(req)))
				{
					dump("ERROR",docBuilder.getLastException().toString());
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
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
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
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
