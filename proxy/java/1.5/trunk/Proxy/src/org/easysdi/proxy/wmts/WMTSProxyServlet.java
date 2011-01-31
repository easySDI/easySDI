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

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Enumeration;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;
import java.util.UUID;
import java.util.Vector;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.wms.WMSProxyCapabilitiesLayerFilter;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.easysdi.xml.resolver.ResourceResolver;
import org.geotools.referencing.CRS;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.opengis.referencing.FactoryException;
import org.opengis.referencing.NoSuchAuthorityCodeException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.springframework.security.core.context.SecurityContextHolder;
import org.w3c.dom.Document;
import org.w3c.dom.NodeList;
import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import com.google.common.collect.HashMultimap;
import com.google.common.collect.Multimap;


/**
 * @author Depth SA
 *
 */
public class WMTSProxyServlet extends ProxyServlet{

	/**
	 * Store all the possible operations for a WMTS service
	 * Used in buildCapabilitiesXSLT()
	 */
	private String[] WMTSOperation = { "GetCapabilities", "GetTile", "GetFeatureInfo" };
	/**
	 * Store the url of the server which returned the response i
	 */
	private Map<Integer, String> serverUrlPerfilePathList = new TreeMap<Integer, String>(); 
	
	
	public WMTSProxyServlet() {
		super();
		ServiceSupportedOperations = Arrays.asList("GetCapabilities", "GetTile");
	}

	private static final long serialVersionUID = 1982682293133286643L;

	@Override
	protected void requestPreTreatmentPOST(HttpServletRequest req,
			HttpServletResponse resp) {
		// TODO Auto-generated method stub
		
	}

	@Override
	protected void requestPreTreatmentGET(HttpServletRequest req,
			HttpServletResponse resp) {
		try
		{
			String service = "";
			String request = "";
			String acceptVersions = "";
			String sections = "";
			String updateSequence = "";
			String acceptFormats = "";
			String paramUrlBase = "";
			
			Enumeration<String> parameterNames = req.getParameterNames();
			while (parameterNames.hasMoreElements()) {
				String key = (String) parameterNames.nextElement();
				String value = null;
				
				value = URLEncoder.encode(req.getParameter(key), "UTF-8");
				if (key.equalsIgnoreCase("acceptVersions")){
					value = "1.0.0";
					paramUrlBase = paramUrlBase + key + "=" + value + "&";
				}
				else
					paramUrlBase = paramUrlBase + key + "=" + value + "&";
				
				if (key.equalsIgnoreCase("service") )
				{
					service = req.getParameter(key);
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
						requestedVersion = "100";
					}
					else
					{
						dump("ERROR", "WMTS requested version is not supported.");
						sendOgcExceptionBuiltInResponse(resp,generateOgcError("Version not supported.","InvalidParameterValue","acceptVersions", "1.0.0"));
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
				protected Vector<String> serverFilePathList = new Vector<String>();
				protected Vector<String> serverLayerFilePathList = new Vector<String>();

				String operation;
				String paramUrl;
				List layerToKeepList;
				int iServer;
				String paramUrlBase;
				String format;
				int j;
				int layerOrder;
				HttpServletResponse resp;

				public RemoteServerThread(	String pOperation,
											String pParamUrl, 
											List pLayerToKeepList, 
											int pIServer,  
											String pParamUrlBase,
											int pJ, 
											String pFormat, 
											HttpServletResponse res) {
					operation = pOperation;
					paramUrl = pParamUrl;
					layerToKeepList = pLayerToKeepList;
					iServer = pIServer;
					paramUrlBase = pParamUrlBase;
					j = pJ;
					format = pFormat;
					resp = res;
				}
				
				public void run() {
					try {
						dump("DEBUG", "Thread Server: " + getRemoteServerUrl(j) + " work begin");
						if ("GetCapabilities".equalsIgnoreCase(operation)) {
							String filePath = sendData("GET", getRemoteServerUrl(j), paramUrlBase);

							synchronized (wmtsFilePathList) {
								synchronized (layerFilePathList) {
									synchronized (serverUrlPerfilePathList) {
										// Insert the responses
										dump("requestPreTraitementGET save response capabilities from thread server " + getRemoteServerUrl(j));
										layerFilePathList.put(layerOrder, "");
										serverUrlPerfilePathList.put(layerOrder, getRemoteServerUrl(j));
										wmtsFilePathList.put(layerOrder, filePath);
									}
								}
							}
						}
						dump("DEBUG", "Thread Server: " + getRemoteServerUrl(j) + " work finished");
					}
					catch (Exception e)
					{
						resp.setHeader("easysdi-proxy-error-occured", "true");
						dump("ERROR", "Server Thread " + getRemoteServerUrl(j) + " :" + e.getMessage());
						e.printStackTrace();
						sendOgcExceptionBuiltInResponse(resp,generateOgcError("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
					}
				}
			}
			
			
			List<RemoteServerInfo> grsiList = getRemoteServerInfoList();
			List<RemoteServerThread> serverThreadList = new Vector<RemoteServerThread>();
			
			int layerOrder = 0;
			String lastServerURL = null;
			String newServerURL = null;
			String cpOperation = new String(request);
			String cpParamUrl = "";
			String cpParamUrlBase = new String(paramUrlBase);
			String cpFormat = new String(acceptFormats);
			String filter = null;
			if (request.equalsIgnoreCase("getcapabilities")) {
				for (int jj = 0; jj < grsiList.size(); jj++) {
					RemoteServerThread s = new RemoteServerThread(  cpOperation, 
																	cpParamUrl, 
																	null, 
																	serverThreadList.size(), 
																	cpParamUrlBase, 
																	jj, 
																	cpFormat, 
																	resp);

					s.layerOrder = jj;
					s.start();
					serverThreadList.add(s);
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
			sendOgcExceptionBuiltInResponse(resp,generateOgcError("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
		}
		
	}
	
	public void transform(String version, String operation, HttpServletRequest req, HttpServletResponse resp) {
		
		try{
		
			//Filtre les fichiers réponses des serveurs :
			//ajoute les fichiers d'exception dans ogcExceptionFilePathList
			//les enlève de la collection de résultats wmtsFilePathList 
			if(!filterServersResponsesForOgcServiceExceptionFiles())
			{
				sendOgcExceptionBuiltInResponse(resp,generateOgcError("Error in OGC exception management. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
				return;
			}
			
			//Si le mode de gestion des exceptions est "restrictif" et si au moins un serveur retourne une exception OGC
			//Ou
			//Si le mode de gestion des exceptions est "permissif" et que tous les serveurs retournent des exceptions
			//alors le proxy retourne l'ensemble des exceptions concaténées
			if((configuration.getExceptionMode().equals("restrictive") && ogcExceptionFilePathList.size() > 0) || 
					(configuration.getExceptionMode().equals("permissive") && wmtsFilePathList.size() == 0))
			{
				//Traitement des réponses de type exception OGC
				//Le stream retourné contient les exceptions concaténées et mises en forme pour être retournées 
				//directement au client
				dump("INFO","Exception(s) returned by remote server(s) are sent to client.");
				responseContentType ="text/xml";
				ByteArrayOutputStream exceptionOutputStream = buildResponseForOgcServiceException();
				sendHttpServletResponse(req,resp,exceptionOutputStream, "text/xml");
				return;
			}
			
			//Aucun serveur n'a retourné d'exception ou le mode de gestion des exceptions est "permissif"
			// Vérifie et prépare l'application d'un fichier xslt utilisateur
			String userXsltPath = getConfiguration().getXsltPath();
			if (SecurityContextHolder.getContext().getAuthentication() != null) {
				userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
			}
			
			userXsltPath = userXsltPath + "/" + version + "/" + operation + ".xsl";
			String globalXsltPath = getConfiguration().getXsltPath() + "/" + version + "/" + operation + ".xsl";
			
			File xsltFile = new File(userXsltPath);
			boolean isPostTreat = false;
			if (!xsltFile.exists()) {
				dump("Postreatment file " + xsltFile.toString() + "does not exist");
				xsltFile = new File(globalXsltPath);
				if (xsltFile.exists()) {
					isPostTreat = true;
				} else {
					dump("Postreatment file " + xsltFile.toString() + "does not exist");
				}
			} else {
				isPostTreat = true;
			}
			
			// Transforms the results using a xslt before sending the response back
			Transformer transformer = null;
			TransformerFactory tFactory = TransformerFactory.newInstance();
			
			ByteArrayOutputStream tempOut = null; 
			String responseVersion="";
			
			if ("GetCapabilities".equalsIgnoreCase(operation)) {
				dump("transform begin GetCapabilities operation");
	
				// Contains the list of temporary modified Capabilities
				// files.
				List<File> tempFileCapa = new Vector<File>();
	
				// Boucle sur les fichiers réponses
				for (int iFilePath = 0; iFilePath < wmtsFilePathList.size(); iFilePath++) {
					//Load response file
					InputSource iS = new InputSource(new FileInputStream(wmtsFilePathList.get(iFilePath).toArray(new String[1])[0]));
					
					//Get the WMS version of the response
					SAXBuilder sxb = new SAXBuilder();
					org.jdom.Document  docParent = sxb.build(iS);
					responseVersion = docParent.getRootElement().getAttribute("version").getValue();
					responseVersion = responseVersion.replaceAll("\\.", "");
					
					//Transform with XSL
					tempFileCapa.add(createTempFile("transform_GetCapabilities_" + UUID.randomUUID().toString(), ".xml"));
					FileOutputStream tempFosCapa = new FileOutputStream(tempFileCapa.get(iFilePath));
					StringBuffer sb = buildCapabilitiesXSLT(req, resp, iFilePath, responseVersion);
					InputSource inputSource = new InputSource(new FileInputStream(wmsFilePathList.get(iFilePath).toArray(new String[1])[0]));
					InputStream xslt = new ByteArrayInputStream(sb.toString().getBytes());
					XMLReader xmlReader = XMLReaderFactory.createXMLReader();
					String user = (String) getUsername(getRemoteServerUrl(iFilePath));
					String password = (String) getPassword(getRemoteServerUrl(iFilePath));
					if (user != null && user.length() > 0) {
						ResourceResolver rr = new ResourceResolver(user, password);
						xmlReader.setEntityResolver(rr);
					}
					SAXSource saxSource = new SAXSource(xmlReader, inputSource);
					transformer = tFactory.newTransformer(new StreamSource(xslt));
	
					// Write the result in a temporary file
					dump("transform begin xslt transform to response file " + iFilePath);
					transformer.transform(saxSource, new StreamResult(tempFosCapa));
					// Debug tb 06.07.2009
					tempFosCapa.flush();
					tempFosCapa.close();
					// Fin de Debug
					dump("transform end xslt transform to response file " + iFilePath);
				}
	
				// Merge the results of all the capabilities and return it
				// into a single file
				dump("transform begin mergeCapabilities");
				tempOut = mergeCapabilities(tempFileCapa, resp);
				dump("transform end mergeCapabilities");
				
				//Application de la transformation XSLT pour la réécriture des métadonnées du service 
				dump("DEBUG","transform begin apply XSLT on service metadata");
				if(tempOut != null)
				{
					ByteArrayOutputStream out = new ByteArrayOutputStream();
					StringBuffer sb = buildServiceMetadataCapabilitiesXSLT(responseVersion);
					InputStream xslt = new ByteArrayInputStream(sb.toString().getBytes());
					InputSource inputSource = new InputSource(new ByteArrayInputStream(tempOut.toByteArray()) );
					XMLReader xmlReader = XMLReaderFactory.createXMLReader();
					SAXSource saxSource = new SAXSource(xmlReader, inputSource);
					transformer = tFactory.newTransformer(new StreamSource(xslt));
					transformer.setOutputProperty(OutputKeys.INDENT, "yes");
					transformer.setOutputProperty("{http://xml.apache.org/xslt}indent-amount", "2");
					transformer.transform(saxSource, new StreamResult(out));
					tempOut = out;
				}
				dump("DEBUG","transform end apply XSLT on service metadata");
				dump("transform end GetCapabilities operation");
				
				dump("DEBUG","Start - Rewrite BBOX");
				//Réécriture des BBOX
				ByteArrayInputStream in =  new ByteArrayInputStream(tempOut.toByteArray());
				SAXBuilder sxb = new SAXBuilder();
				org.jdom.Document  docParent = sxb.build(in);
				Filter filtre = new WMSProxyCapabilitiesLayerFilter();
		    	Iterator it= docParent.getDescendants(filtre);
		    	List<org.jdom.Element> layersList = new ArrayList<org.jdom.Element>();
		    	while(it.hasNext())
				{
		    		layersList.add((org.jdom.Element)it.next());
				}
		    	if(layersList.size() != 0)
		    	{
			    	CoordinateReferenceSystem wgsCRS = null;
					try {
						wgsCRS = CRS.decode("EPSG:4326");
						if(!rewriteBBOX(layersList, wgsCRS, null, responseVersion))
						{
							sendOgcExceptionBuiltInResponse(resp,generateOgcError("Error in BoundingBox calculation.","NoApplicableCode","",requestedVersion));
							return;
						}
					} catch (NoSuchAuthorityCodeException e1) {
						dump("ERROR","Exception when trying to load SRS EPSG:4326 : "+e1.getMessage());
						sendOgcExceptionBuiltInResponse(resp,generateOgcError("Error in BoundingBox calculation.","NoApplicableCode","",requestedVersion));
						return;
					} catch (FactoryException e1) {
						dump("ERROR",e1.getMessage());
						sendOgcExceptionBuiltInResponse(resp,generateOgcError("Error in BoundingBox calculation.","NoApplicableCode","",requestedVersion));
						return;
					}
					
		    	}
		    	dump("DEBUG","End - Rewrite BBOX");
		    	//Return
				XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
				ByteArrayOutputStream result =new ByteArrayOutputStream ();
				sortie.output(docParent,result );
				tempOut = result;	
			
			}
		}
		catch (Exception e){
			
		}
		
	}

	protected boolean filterServersResponsesForOgcServiceExceptionFiles () 
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
					DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
					Document documentMaster = db.newDocumentBuilder().parse(new File(path));
					if (documentMaster != null) 
					{
						NodeList nl = documentMaster.getElementsByTagName("ExceptionReport");
						if (nl.item(0) != null)
						{
							toRemove.put(entry.getKey(), path);
						}
					}
				}
			}
			
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
			return false;
		}
	}
	
	@Override
	protected StringBuffer generateOgcError(String errorMessage, String code, String locator, String version) {
		dump("ERROR", errorMessage);
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>");
		sb.append("<ExceptionReport xmlns=\"http://www.opengis.net/ows/1.1\" " +
				"xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" " +
				"xsi:schemaLocation=\"http://www.opengis.net/ows/1.1\" version=\"");
		sb.append(version);
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
