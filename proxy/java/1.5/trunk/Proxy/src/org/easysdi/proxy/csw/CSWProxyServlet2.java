/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
package org.easysdi.proxy.csw;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.io.StringBufferInputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Random;
import java.util.UUID;
import java.util.Vector;
import java.util.zip.CRC32;
import java.util.zip.GZIPInputStream;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.Unmarshaller;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.apache.commons.httpclient.HttpURL;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.exception.AvailabilityPeriodException;
import org.easysdi.security.JoomlaProvider;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.easysdi.xml.handler.ConfigFileHandler;
import org.easysdi.xml.handler.CswRequestHandler;
import org.easysdi.xml.handler.GeonetworkSearchResultHandler;
import org.easysdi.xml.handler.PolicyHandler;
import org.easysdi.xml.handler.RequestHandler;
import org.jdom.Element;
import org.jdom.input.SAXBuilder;
import org.jdom.xpath.XPath;
import org.w3c.dom.Document;
import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

public class CSWProxyServlet2 extends CSWProxyServlet {

	private static final long serialVersionUID = 1L;
	private List<String> recordsToKeep = new ArrayList<String>();
	private List<String> recordsToRemove = new ArrayList<String>();
	private List<String> recordsReturned = new ArrayList <String>();
	
	private String requestedOutputSchema;
	private String requestedResutlType;
	private String requestedElementSetName;
	
	public static final List<String> cswOutputSchemas = Arrays.asList("http://www.opengis.net/cat/csw/2.0.2","csw:Record","csw:IsoRecord");
	public static final List<String> gmdOutputSchemas = Arrays.asList("http://www.isotc211.org/2005/gmd");
	
	/** The outputSchema specified in the request is 'http://www.opengis.net/cat/csw/2.0.2'
	 * or the request used the default value
	 * @return
	 */
	protected StringBuffer generateXSLTForMetadata()
	{
		try 
		{
			StringBuffer CSWCapabilities200 = new StringBuffer();

			CSWCapabilities200.append("<xsl:stylesheet version=\"1.00\" " +
					"xmlns:dc=\"http://purl.org/dc/elements/1.1/\" " +
					"xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" " +
					"xmlns:gco=\"http://www.isotc211.org/2005/gco\" " +
					"xmlns:ns3=\"http://www.isotc211.org/2005/gmx\" " +
					"xmlns:xlink=\"http://www.w3.org/1999/xlink\" " +
					"xmlns:gml=\"http://www.opengis.net/gml\" " +
					"xmlns:gts=\"http://www.isotc211.org/2005/gts\"   " +
					"xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" " +
					"xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" " +
					"xmlns:ows=\"http://www.opengis.net/ows\"> ");

			//According to the value of ElementSetName, the researched element's name can be : 
			//- csw:Record
			//- csw:BriefRecord
			//- csw:SummaryRecord
			//We include the 3 cases in the stylesheet
			
			//Remove the not allowed attributes
			List<String> notAllowedAttributeList = getAttributesNotAllowedInMetadata(getRemoteServerUrl(0));
			if(notAllowedAttributeList.size()!=0)
			{
				int nsI = 0;
				for (int i = 0; i < notAllowedAttributeList.size(); i++) 
				{
					String text = notAllowedAttributeList.get(i);
					CSWCapabilities200.append("<xsl:template match=\"//csw:SearchResults/" + text + "\">");
					CSWCapabilities200.append("</xsl:template>");
					
					if (text != null) 
					{
						if (text.indexOf("\":") < 0) 
						{
							// Pas de namespace.
							CSWCapabilities200.append("<xsl:template match=\"//gmd:MD_Metadata/" + text + "\">");
						} 
						else 
						{
							CSWCapabilities200.append("<xsl:template xmlns:" + "au" + nsI + "=\"" + text.substring(1, text.indexOf("\":"))
									+ "\" match=\"//gmd:MD_Metadata/au" + nsI + text.substring(text.indexOf("\":") + 1) + "\">");
							nsI++;
						}
						CSWCapabilities200.append("</xsl:template>");
					}
				}
				
			}
			
		
			

		
			//Copy all other nodes
			CSWCapabilities200.append("  <!-- Whenever you match any node or any attribute -->");
			CSWCapabilities200.append("<xsl:template match=\"node()|@*\">");
			CSWCapabilities200.append("<!-- Copy the current node -->");
			CSWCapabilities200.append("<xsl:copy>");
			CSWCapabilities200.append("<!-- Including any attributes it has and any child nodes -->");
			CSWCapabilities200.append("<xsl:apply-templates select=\"@*|node()\"/>");
			CSWCapabilities200.append("</xsl:copy>");
			CSWCapabilities200.append("</xsl:template>");

			CSWCapabilities200.append("</xsl:stylesheet>");
			return CSWCapabilities200;
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}

		// If something goes wrong, an empty stylesheet is returned.
		StringBuffer sb = new StringBuffer();
		return sb.append("<xsl:stylesheet version=\"1.00\" " +
				"xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" " +
				"xmlns:ows=\"http://www.opengis.net/ows\" " +
				"xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
	}
	
	/** The outputSchema specified in the request is 'http://www.isotc211.org/2005/gmd'
	 * @return
	 */
	protected StringBuffer generateXSLTForMetadataGMD()  
	{
		try 
		{
			StringBuffer CSWCapabilities200 = new StringBuffer();

			CSWCapabilities200.append("<xsl:stylesheet version=\"1.00\" " +
					"xmlns:dc=\"http://purl.org/dc/elements/1.1/\" " +
					"xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" " +
					"xmlns:gco=\"http://www.isotc211.org/2005/gco\" " +
					"xmlns:ns3=\"http://www.isotc211.org/2005/gmx\" " +
					"xmlns:xlink=\"http://www.w3.org/1999/xlink\" " +
					"xmlns:gml=\"http://www.opengis.net/gml\" " +
					"xmlns:gts=\"http://www.isotc211.org/2005/gts\"   " +
					"xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" " +
					"xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" " +
					"xmlns:ows=\"http://www.opengis.net/ows\"> ");

			List<String> notAllowedAttributeList = getAttributesNotAllowedInMetadata(getRemoteServerUrl(0));
			if(notAllowedAttributeList.size()!=0)
			{
//				int nsI = 0;
				for (int i = 0; i < notAllowedAttributeList.size(); i++) 
				{
					CSWCapabilities200.append("<xsl:template match=\"csw:GetRecordsResponse/csw:SearchResults/csw:Record/");
					CSWCapabilities200.append(notAllowedAttributeList.get(i));
					CSWCapabilities200.append("\">");
					CSWCapabilities200.append("</xsl:template>");
//					String text = notAllowedAttributeList.get(i);
//					if (text != null) 
//					{
//						if (text.indexOf("\":") < 0) 
//						{
//							// Pas de namespace.
//							CSWCapabilities200.append("<xsl:template match=\"//gmd:MD_Metadata/" + text + "\">");
//						} 
//						else 
//						{
//							CSWCapabilities200.append("<xsl:template xmlns:" + "au" + nsI + "=\"" + text.substring(1, text.indexOf("\":"))
//									+ "\" match=\"//gmd:MD_Metadata/au" + nsI + text.substring(text.indexOf("\":") + 1) + "\">");
//							nsI++;
//						}
//						CSWCapabilities200.append("</xsl:template>");
//					}
				}
				
			}
			
			CSWCapabilities200.append("<xsl:template match=\"csw:GetRecordsResponse/csw:SearchResults/@numberOfRecordsReturned\">");
			CSWCapabilities200.append("<xsl:attribute name=\"numberOfRecordsReturned\">");
			CSWCapabilities200.append("<xsl:value-of select=\"'");
			CSWCapabilities200.append(recordsReturned.size()- recordsToRemove.size());
			CSWCapabilities200.append("'\"/>");
			CSWCapabilities200.append("</xsl:attribute>");
			CSWCapabilities200.append("</xsl:template>");
//			
//			CSWCapabilities200.append("<xsl:template match=\"csw:GetRecordsResponse/csw:SearchResults/@numberOfRecordsMatched\">");
//			CSWCapabilities200.append("<xsl:attribute name=\"numberOfRecordsMatched\">");
//			CSWCapabilities200.append("<xsl:value-of select=\"'your value here'\"/>");
//			CSWCapabilities200.append("</xsl:attribute>");
//			CSWCapabilities200.append("</xsl:template>");
			
			//csw:Record
			//csw:BriefRecord
			//csw:SummaryRecord
			
			if(recordsToRemove.size() != 0)
			{
				for (int i = 0; i < recordsToRemove.size() ; i++)
				{
					CSWCapabilities200.append("<xsl:template match=\"csw:GetRecordsResponse/csw:SearchResults/csw:Record[dc:identifier='");
					CSWCapabilities200.append(recordsToRemove.get(i));
					CSWCapabilities200.append("']\">");
					CSWCapabilities200.append("</xsl:template>");
					
					CSWCapabilities200.append("<xsl:template match=\"csw:GetRecordsResponse/csw:SearchResults/csw:BriefRecord[dc:identifier='");
					CSWCapabilities200.append(recordsToRemove.get(i));
					CSWCapabilities200.append("']\">");
					CSWCapabilities200.append("</xsl:template>");
					
					CSWCapabilities200.append("<xsl:template match=\"csw:GetRecordsResponse/csw:SearchResults/csw:SummaryRecord[dc:identifier='");
					CSWCapabilities200.append(recordsToRemove.get(i));
					CSWCapabilities200.append("']\">");
					CSWCapabilities200.append("</xsl:template>");
				}
			}
		
			CSWCapabilities200.append("  <!-- Whenever you match any node or any attribute -->");
			CSWCapabilities200.append("<xsl:template match=\"node()|@*\">");
			CSWCapabilities200.append("<!-- Copy the current node -->");
			CSWCapabilities200.append("<xsl:copy>");
			CSWCapabilities200.append("<!-- Including any attributes it has and any child nodes -->");
			CSWCapabilities200.append("<xsl:apply-templates select=\"@*|node()\"/>");
			CSWCapabilities200.append("</xsl:copy>");
			CSWCapabilities200.append("</xsl:template>");

			CSWCapabilities200.append("</xsl:stylesheet>");
			return CSWCapabilities200;
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}

		// If something goes wrong, an empty stylesheet is returned.
		StringBuffer sb = new StringBuffer();
		return sb.append("<xsl:stylesheet version=\"1.00\" " +
				"xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" " +
				"xmlns:ows=\"http://www.opengis.net/ows\" " +
				"xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
	}
	
	public void transform(String version, String currentOperation, HttpServletRequest req, HttpServletResponse resp, List<String> filePathList) 
	{
		try 
		{
			String userXsltPath = getConfiguration().getXsltPath();
			if (req.getUserPrincipal() != null) 
			{
				userXsltPath = userXsltPath + "/" + req.getUserPrincipal().getName() + "/";
			}

			userXsltPath = userXsltPath + "/" + version + "/" + currentOperation + ".xsl";
			String globalXsltPath = getConfiguration().getXsltPath() + "/" + version + "/" + currentOperation + ".xsl";
			
			File xsltFile = new File(userXsltPath);
			boolean isPostTreat = false;
			if (!xsltFile.exists()) 
			{
				dump("Postreatment file " + xsltFile.toString() + "does not exist");
				xsltFile = new File(globalXsltPath);
				if (xsltFile.exists()) 
				{
					isPostTreat = true;
				} 
				else 
				{
					dump("Postreatment file " + xsltFile.toString() + "does not exist");
				}
			} 
			else 
			{
				isPostTreat = true;
			}

			// Transforms the results using a xslt before sending the
			// response
			// back
			InputStream xml = new FileInputStream(filePathList.get(0));
			TransformerFactory tFactory = TransformerFactory.newInstance();

			File tempFile = null;
			FileOutputStream tempFos = null;

			Transformer transformer = null;

			if (currentOperation != null) 
			{

				if (currentOperation.equals("GetCapabilities")) 
				{
					tempFile = createTempFile(UUID.randomUUID().toString(), ".xml");
					tempFos = new FileOutputStream(tempFile);
					ByteArrayInputStream xslt = null;
					xslt = new ByteArrayInputStream(buildCapabilitiesXSLT(req).toString().getBytes());
					transformer = tFactory.newTransformer(new StreamSource(xslt));
					// Write the result in a temporary file
					transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
					tempFos.close();

					dump("DEBUG","transform begin apply XSLT on service metadata");
					InputStream in = new BufferedInputStream(new FileInputStream(tempFile));
					InputSource inputSource = new InputSource( in);
					
					//Application de la transformation XSLT pour la réécriture des métadonnées du service 
					File tempFileCapaWithMetadata = createTempFile("transform_MDGetCapabilities_" + UUID.randomUUID().toString(), ".xml");
					FileOutputStream tempServiceMD = new FileOutputStream(tempFileCapaWithMetadata);
					StringBuffer sb = buildServiceMetadataCapabilitiesXSLT();
					InputStream xslt_service = new ByteArrayInputStream(sb.toString().getBytes());
				
					XMLReader xmlReader = XMLReaderFactory.createXMLReader();
					SAXSource saxSource = new SAXSource(xmlReader, inputSource);
					transformer = tFactory.newTransformer(new StreamSource(xslt_service));
					transformer.transform(saxSource, new StreamResult(tempServiceMD));
					tempServiceMD.flush();
					tempServiceMD.close();
					
					tempFile = tempFileCapaWithMetadata;
					dump("DEBUG","transform end apply XSLT on service metadata");
					
				}
				else if("GetRecords".equals(currentOperation))
				{
					//Filter according to data accessibility
//					if(hasPolicy)
//					{
//						dump("DEBUG","GetRecords - Start Check for data accessibility.");
//						CSWProxyDataAccessibilityManager cswDataManager = new CSWProxyDataAccessibilityManager(policy, getJoomlaProvider());
//						if(!cswDataManager.isAllDataAccessible())
//						{
//							recordsReturned = cswDataManager.extractRecordIDFromGetRecordsResponse(new File(filePathList.get(0)), requestedOutputSchema);
//							if(recordsReturned != null)
//							{
//								for (int i = 0; i< recordsReturned.size();i++)
//								{
//									if(cswDataManager.isDataVersionAccessible(recordsReturned.get(i)) && cswDataManager.isDataAccessible(recordsReturned.get(i)))
//									{
//										recordsToKeep.add(recordsReturned.get(i));
//									}
//									else
//									{
//										recordsToRemove.add(recordsReturned.get(i));
//									}
//								}
//							}
//						}
//					}
//					dump("DEBUG","GetRecords - End Check for data accessibility.");
					if (areAllAttributesAllowedForMetadata(getRemoteServerUrl(0)) && recordsToRemove.size()==0) 
					{
						// Keep the metadata as it is
						tempFile = new File(filePathList.get(0));
					} 
					else 
					{
						dump("DEBUG","GetRecords - Start apply XSLT on response.");
						tempFile = createTempFile(UUID.randomUUID().toString(), ".xml");
						tempFos = new FileOutputStream(tempFile);
	
						InputStream xslt = null;
						xslt = new ByteArrayInputStream(generateXSLTForMetadata().toString().getBytes());

						transformer = tFactory.newTransformer(new StreamSource(xslt));
						// Write the result in a temporary file
						transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
						tempFos.close();
						dump("DEBUG","GetRecords - End apply XSLT on response.");
					}
				} 
				else if("GetRecordById".equals(currentOperation))
				{
					if (areAllAttributesAllowedForMetadata(getRemoteServerUrl(0))) {
						// Keep the metadata as it is
						tempFile = new File(filePathList.get(0));
					} 
					else 
					{
						dump("DEBUG","GetRecordById - Start apply XSLT on response.");
						tempFile = createTempFile(UUID.randomUUID().toString(), ".xml");
						tempFos = new FileOutputStream(tempFile);
						
						InputStream xslt = null;
						xslt = new ByteArrayInputStream(generateXSLTForMetadata().toString().getBytes());

						transformer = tFactory.newTransformer(new StreamSource(xslt));
						// Write the result in a temporary file
						transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
						tempFos.close();
						dump("DEBUG","GetRecordById - End apply XSLT on response.");
					}
				}
				else
				{
					tempFile = new File(filePathList.get(0));
				}

				/*
				 * if a xslt file exists then post-treat the response
				 */
				if (isPostTreat) {
					PrintWriter out = resp.getWriter();
					transformer = tFactory.newTransformer(new StreamSource(xsltFile));
					transformer.transform(new StreamSource(tempFile), new StreamResult(out));
					// delete the temporary file
					tempFile.delete();
					out.close();
					// the job is done. we can go out
					return;
				}
			}

			// No post rule to apply.
			// Copy the file result on the output stream
			resp.setContentType("application/xml");
			// resp.setContentLength(Integer.MAX_VALUE);
			resp.setContentLength((int) tempFile.length());
			OutputStream os = resp.getOutputStream();
			InputStream is = new FileInputStream(tempFile);

			int byteRead;
			try 
			{
				while ((byteRead = is.read()) != -1) 
				{
					os.write(byteRead);
				}
			} 
			finally 
			{
				recordsToKeep.clear();
				recordsToRemove.clear();
				recordsReturned.clear();
				os.close();
				is.close();
				DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
				Date d = new Date();
				dump("SYSTEM", "ClientResponseDateTime", dateFormat.format(d));
				if (tempFile != null) 
				{
					dump("SYSTEM", "ClientResponseLength", tempFile.length());
					tempFile.delete();
				}
			}
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see
	 * org.easysdi.proxy.core.ProxyServlet#requestPreTreatmentGET(javax.servlet
	 * .http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
	 */
	@Override
	protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp) 
	{
		try
		{
			String currentOperation = null;
			String version = "000";
			String requestedId = "";

			Enumeration<String> parameterNames = req.getParameterNames();
			String paramUrl = "";
			
			while (parameterNames.hasMoreElements()) 
			{
				String key = (String) parameterNames.nextElement();
				String value = URLEncoder.encode(req.getParameter(key),"UTF-8");
				if(key.equalsIgnoreCase("id"))
				{
					requestedId = value;
					continue;
				}
				if(key.equalsIgnoreCase("Request"))
				{
					// Gets the requested Operation
					if (value.equalsIgnoreCase("capabilities")) 
					{
						currentOperation = "GetCapabilities";
					} 
					else 
					{
						currentOperation = value;
					}
					continue;
				}
				if (key.equalsIgnoreCase("version")) 
				{
					requestedVersion = value;
					continue;
				}
				//TODO : verify the syntax for those 2 attributes
				if (key.equalsIgnoreCase("OutputSchema")) 
				{
					requestedOutputSchema = value;
					continue;
				}
				if (key.equalsIgnoreCase("ResultType")) 
				{
					requestedResutlType = value;
					continue;
				}
			}
			
			//Generate OGC exception if current operation is not allowed
			if(handleNotAllowedOperation(currentOperation,resp))
				return ;
			
			if(currentOperation.equalsIgnoreCase("GetRecordById"))
			{
				if(hasPolicy)
				{
					CSWProxyDataAccessibilityManager cswDataManager = new CSWProxyDataAccessibilityManager(policy, getJoomlaProvider());
					if(!cswDataManager.isAllDataAccessible())
					{
						String dataIDaccessible="";
						if(!cswDataManager.isDataVersionAccessible(requestedId))
						{
							dataIDaccessible = cswDataManager.getDataIdVersionAccessible();
						}
						else
						{
							dataIDaccessible = requestedId;
						}
							
						if(!cswDataManager.isDataAccessible(dataIDaccessible))
						{
							sendProxyBuiltInResponse(resp,cswDataManager.generateEmptyResponse(requestedVersion));
							return;
						}
						requestedId = dataIDaccessible;
					}
				}
			}
			else if(currentOperation.equalsIgnoreCase("GetRecords"))
			{
				if(hasPolicy)
				{
					CSWProxyDataAccessibilityManager cswDataManager = new CSWProxyDataAccessibilityManager(policy, getJoomlaProvider());
					if(!cswDataManager.isAllDataAccessible())
					{
						cswDataManager.getAccessibleDataIds();
						//Add a filter on the data id in the request
//						param = cswDataManager.addFilterOnDataAccessible(param, cswDataManager.getAccessibleDataIds());
//						dump("INFO", "GetRecords request send : "+param);
					}
				}
			}
			
			// Build the request to dispatch
			parameterNames = req.getParameterNames();
			while (parameterNames.hasMoreElements()) 
			{
				String key = (String) parameterNames.nextElement();
				String value = URLEncoder.encode(req.getParameter(key),"UTF-8");
				
				if(key.equalsIgnoreCase("id"))
				{
					paramUrl = paramUrl + key + "=" + requestedId + "&";
				}
				else
				{
					paramUrl = paramUrl + key + "=" + value + "&";
				}
			}
			
			if(requestedVersion != null)
				version = requestedVersion;
			version = version.replaceAll("\\.", "");
	
			
			// Send the request to the remote server
			List<String> filePathList = new Vector<String>();
			String filePath = sendData("GET", getRemoteServerUrl(0), paramUrl);
			filePathList.add(filePath);
			transform(version, currentOperation, req, resp, filePathList);
			
		} 
		catch (AvailabilityPeriodException e) 
		{
			dump("ERROR", e.getMessage());
			sendOgcExceptionBuiltInResponse(resp,generateOgcError(e.getMessage(),"OperationNotSupported ","request",requestedVersion));
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}
	}



	/**
	 * 
	 */
	@Override
	protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp) {
		try {

			XMLReader xr = XMLReaderFactory.createXMLReader();
			CswRequestHandler rh = new CswRequestHandler();
			xr.setContentHandler(rh);

			StringBuffer param = new StringBuffer();
			String input;
			BufferedReader in = new BufferedReader(new InputStreamReader(req.getInputStream()));
			while ((input = in.readLine()) != null) {
				dump(input);
				param.append(input);
			}

			xr.parse(new InputSource(new InputStreamReader(new ByteArrayInputStream(param.toString().getBytes()))));

			String version = rh.getVersion();
			requestedVersion = version;
			if (version != null)
				version = version.replaceAll("\\.", "");
			
			String currentOperation = rh.getOperation();
			
			//Use for the GetRecords request
			requestedOutputSchema = rh.getOutputSchema();
			requestedResutlType = rh.getResultType();

			//Generate OGC exception if current operation is not allowed
			if(handleNotAllowedOperation(currentOperation,resp))
				return;

			
			// In the case of transaction only one remote server is supported.
			// We use the configuration of the first one.
			// add a tag in the configuration file to set the default
			// server --> HVH-27.08.2010 : Only one server is supported in the config file fot the moment,
			// default server tag will be implemented when several servers will be supported
			RemoteServerInfo rsi = getRemoteServerInfo(0);
			String transactionType = "ogc";
			if (rsi != null) 
			{
				transactionType = rsi.getTransaction();
			}

			if (currentOperation.equalsIgnoreCase("Transaction") && transactionType.equalsIgnoreCase("geonetwork")) 
			{
				if (rh.isTransactionInsert()) {
					// Send the xml
					StringBuffer response = sendFile(rsi.getUrl(), param, rsi.getLoginService());

					// Get the response
					OutputStream os = resp.getOutputStream();
					InputStream is = new ByteArrayInputStream(response.toString().getBytes());
					int byteRead;
					try {
						while ((byteRead = is.read()) != -1) {
							os.write(byteRead);
							// dump(byteRead);
						}
					} finally {
						DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
						Date d = new Date();
						dump("SYSTEM", "ClientResponseDateTime", dateFormat.format(d));
						if (os != null) 
						{
							dump("SYSTEM", "ClientResponseLength", os.toString().length());
						}
						os.flush();
						os.close();
					}
					os = null;
					is = null;
				}

				if (rh.isTransactionDelete()) {
					// Send the xml
					StringBuffer response = sendFile(rsi.getUrl(), param, rsi.getLoginService());

					// Get the response
					OutputStream os = resp.getOutputStream();
					InputStream is = new ByteArrayInputStream(response.toString().getBytes());
					int byteRead;
					try {
						while ((byteRead = is.read()) != -1) {
							os.write(byteRead);
						}
					} finally {
						DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
						Date d = new Date();
						dump("SYSTEM", "ClientResponseDateTime", dateFormat.format(d));
						if (os != null) 
						{
							dump("SYSTEM", "ClientResponseLength", os.toString().length());
						}
						os.flush();
						os.close();
					}
					os = null;
					is = null;
				}

			}
			else if(currentOperation.equalsIgnoreCase("GetRecordById"))
			{
				if(hasPolicy)
				{
					CSWProxyDataAccessibilityManager cswDataManager = new CSWProxyDataAccessibilityManager(policy, getJoomlaProvider());
					String dataId = rh.getRecordId();
					if(!cswDataManager.isAllDataAccessible())
					{
						if(!cswDataManager.isDataVersionAccessible(dataId))
						{
							dataId = cswDataManager.getDataIdVersionAccessible();
						}
						if(!cswDataManager.isDataAccessible(dataId))
						{
							sendProxyBuiltInResponse(resp,cswDataManager.generateEmptyResponse(requestedVersion));
							return;
						}
						if(dataId.compareTo(rh.getRecordId()) != 0)
						{
							//Change the metadata's id in the request
							int start = param.indexOf(rh.getRecordId());
							int end = start + rh.getRecordId().length();
							
							param.replace(start, end, dataId);
							
							String result = param.toString();
							
						}
					}
				}
				
				List<String> filePathList = new Vector<String>();
				String filePath = sendData("POST", getRemoteServerUrl(0), param.toString());
				filePathList.add(filePath);
				transform(version, currentOperation, req, resp, filePathList);
				
			}
			else if(currentOperation.equalsIgnoreCase("GetRecords"))
			{
				if(hasPolicy)
				{
					CSWProxyDataAccessibilityManager cswDataManager = new CSWProxyDataAccessibilityManager(policy, getJoomlaProvider());
					if(!cswDataManager.isAllDataAccessible())
					{
						cswDataManager.getAccessibleDataIds();
						//Add a filter on the data id in the request
						param = cswDataManager.addFilterOnDataAccessible(configuration.getOgcSearchFilter(), param, cswDataManager.getAccessibleDataIds());
						dump("INFO", "GetRecords request send : "+param);
					}
				}
				List<String> filePathList = new Vector<String>();
				String filePath = sendData("POST", getRemoteServerUrl(0), param.toString());
				filePathList.add(filePath);
				transform(version, currentOperation, req, resp, filePathList);
			}
			else 
			{
				List<String> filePathList = new Vector<String>();
				String filePath = sendData("POST", getRemoteServerUrl(0), param.toString());
				filePathList.add(filePath);
				transform(version, currentOperation, req, resp, filePathList);
			}
		} 
		catch (AvailabilityPeriodException e) 
		{
			dump("ERROR", e.getMessage());
			sendOgcExceptionBuiltInResponse(resp,generateOgcError(e.getMessage(),"OperationNotSupported ","request",requestedVersion));
		}
		catch (Exception e) 
		{
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}
	}

	

}