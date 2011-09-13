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
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Enumeration;
import java.util.Iterator;
import java.util.List;
import java.util.UUID;
import java.util.Vector;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.easysdi.jdom.filter.ElementSDIPlatformFilter;
import org.easysdi.jdom.filter.ElementSearchResultsFilter;
import org.easysdi.jdom.filter.ElementTransactionTypeFilter;
import org.easysdi.proxy.exception.AvailabilityPeriodException;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.easysdi.xml.handler.CswRequestHandler;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.Namespace;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.springframework.security.core.context.SecurityContextHolder;
import org.xml.sax.InputSource;
import org.xml.sax.SAXParseException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

public class CSWProxyServlet2 extends CSWProxyServlet {

	private static final long serialVersionUID = 1L;
	
	/** 
	 * 
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
			logger.error( e.getMessage());
			// If something goes wrong, an empty stylesheet is returned.
			StringBuffer sb = new StringBuffer();
			return sb.append("<xsl:stylesheet version=\"1.00\" " +
					"xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" " +
					"xmlns:ows=\"http://www.opengis.net/ows\" " +
					"xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
		}
	}
		
	/**
	 * Apply an XSLT transformation on the XML response to remove unauthorized attributes (according to policy file)
	 * @param xml
	 * @return transformed File
	 * @throws TransformerException
	 * @throws IOException
	 */
	protected File applyXSLTToRemoveAttribute (InputStream xml) throws TransformerException, IOException{
		
		logger.trace("Start apply XSLT on response.");
		File tempFile = createTempFile(UUID.randomUUID().toString(), ".xml");
		FileOutputStream tempFos = new FileOutputStream(tempFile);

		InputStream xslt = null;
		xslt = new ByteArrayInputStream(generateXSLTForMetadata().toString().getBytes());
		TransformerFactory tFactory = TransformerFactory.newInstance();
		Transformer transformer = tFactory.newTransformer(new StreamSource(xslt));
		// Write the result in a temporary file
		transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
		tempFos.close();
		logger.trace("- End apply XSLT on response.");
		return tempFile;
	}
	
	/**
	 * 
	 */
	@SuppressWarnings("unchecked")
	public void transform(String version, String currentOperation, HttpServletRequest req, HttpServletResponse resp, List<String> filePathList) 
	{
		try 
		{
			String userXsltPath = getConfiguration().getXsltPath();
			if (SecurityContextHolder.getContext().getAuthentication() != null) 
			{
				userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
			}

			userXsltPath = userXsltPath + "/" + version + "/" + currentOperation + ".xsl";
			String globalXsltPath = getConfiguration().getXsltPath() + "/" + version + "/" + currentOperation + ".xsl";
			
			File xsltFile = new File(userXsltPath);
			boolean isPostTreat = false;
			if (!xsltFile.exists()) 
			{
				logger.trace("Postreatment file " + xsltFile.toString() + "does not exist");
				xsltFile = new File(globalXsltPath);
				if (xsltFile.exists()) 
				{
					isPostTreat = true;
				} 
				else 
				{
					logger.trace("Postreatment file " + xsltFile.toString() + "does not exist");
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

					logger.trace("transform begin apply XSLT on service metadata");
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
					transformer.setOutputProperty(OutputKeys.INDENT, "yes");
					transformer.setOutputProperty("{http://xml.apache.org/xslt}indent-amount", "2");
					transformer.transform(saxSource, new StreamResult(tempServiceMD));
					tempServiceMD.flush();
					tempServiceMD.close();
					
					tempFile = tempFileCapaWithMetadata;
					logger.trace("transform end apply XSLT on service metadata");
				}
				else if ("DescribeRecord".equals(currentOperation)){
					if (areAllAttributesAllowedForMetadata(getRemoteServerUrl(0)) ) 
					{
						// Keep the metadata as it is
						tempFile = new File(filePathList.get(0));
					} 
					else 
					{
						tempFile = applyXSLTToRemoveAttribute(xml);
					}
				}
				else if ("GetRecords".equals(currentOperation)){
					if (areAllAttributesAllowedForMetadata(getRemoteServerUrl(0)) ) 
					{
						// Keep the metadata as it is
						tempFile = new File(filePathList.get(0));
					} 
					else 
					{
						tempFile = applyXSLTToRemoveAttribute(xml);
					}
					
					//If the current config is used to harvest remote catalog (see config file : <harvesting-config>true</harvesting-config>),
					//add dynamically an XML node (and its namespace definition) to the metadata to indicate that this metadata was haversting
					if(configuration.getIsHarvestingConfig()){
						SAXBuilder sb = new SAXBuilder();
	
						Document doc = null;
				        try {
				            doc = sb.build(tempFile);
				            Element racine = doc.getRootElement();
				            
				            //Get the metadata element from the complete response file
				            List<Element> resultListStorage = new ArrayList<Element> ();
				            Iterator<Element> resultIterator = racine.getDescendants(new ElementSearchResultsFilter());
				            while(resultIterator.hasNext()){
				            	Element result = resultIterator.next();
				            	resultListStorage.addAll(result.getChildren());
				            }
				            
				            //Add a new node to the metadata element
				            //<gmd:MD_Metadata xmlns:sdi="http://www.easysdi.org/2011/sdi">
				            //<sdi:platform harvested="true" />
				            //</gmd:MD_Metadata>
				            //Or update the existing node if the remote catalog is driven by EasySDI too
				            Iterator<Element> resultStorageIterator = resultListStorage.iterator();
				            Element result = null;
				            Namespace nsSDI = Namespace.getNamespace("sdi","http://www.easysdi.org/2011/sdi") ;
				            while (resultStorageIterator.hasNext()){
				            	result = resultStorageIterator.next();
				            	List<Element> platformElementList = result.getChildren("platform", nsSDI);
				            	if(platformElementList.size() > 0 ){
				            		//Update the existing node
				            		Element e = platformElementList.get(0);
					            	e.setAttribute("harvested", "true");
				            	}else{
				            		//Add a new node
					            	Element e = new Element("platform", nsSDI);
					            	e.setAttribute("harvested", "true");
					            	result.addContent(e);
				            	}
				            }
				            if(result != null)
				            	result.getParentElement().addNamespaceDeclaration(nsSDI);
				            
				            XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
				            sortie.output(doc, new FileOutputStream(tempFile));
				        }
				        catch (JDOMException e) {
				            e.printStackTrace();
				        }
				        catch (IOException e) {
				            e.printStackTrace();
				        }
					}
				}
				else if( "GetRecordById".equals(currentOperation) )
				{
					if (areAllAttributesAllowedForMetadata(getRemoteServerUrl(0)) ) 
					{
						// Keep the metadata as it is
						tempFile = new File(filePathList.get(0));
					} 
					else 
					{
						tempFile = applyXSLTToRemoveAttribute(xml);
					}
					
					//If the current config is used to harvest remote catalog (see config file : <harvesting-config>true</harvesting-config>),
					//add dynamically an XML node (and its namespace definition) to the metadata to indicate that this metadata was haversting
					if(configuration.getIsHarvestingConfig()){
						SAXBuilder sb = new SAXBuilder();
	
						Document doc = null;
				        try {
				            doc = sb.build(tempFile);
				            Element racine = doc.getRootElement();
				            
				            //Get the metadata element from the complete response file
				            List<Element> metadataListStorage = new ArrayList<Element> ();
				            Iterator<Element> metadataIterator = racine.getChildren().iterator();
				            while(metadataIterator.hasNext()){
				            	Element metadata = metadataIterator.next();
				            	metadataListStorage.add(metadata);
				            }
				            
				            //Add a new node to the metadata element
				            //<gmd:MD_Metadata xmlns:sdi="http://www.easysdi.org/2011/sdi">
				            //<sdi:platform harvested="true" />
				            //</gmd:MD_Metadata>
				            //Or update the existing node if the remote catalog is driven by EasySDI too
				            Iterator<Element> metadataStorageIterator = metadataListStorage.iterator();
				            while (metadataStorageIterator.hasNext()){
				            	Element metadata = metadataStorageIterator.next();
				            	Namespace nsSDI = Namespace.getNamespace("sdi", "http://www.easysdi.org/2011/sdi");
				            	metadata.addNamespaceDeclaration(nsSDI);
				            	List<Element> platformElementList = metadata.getChildren("platform", nsSDI);
				            	if(platformElementList.size() > 0 ){
				            		//Update the existing node
				            		Element e = platformElementList.get(0);
					            	e.setAttribute("harvested", "true");
				            	}else{
				            		//Add a new node
					            	Element e = new Element("platform", nsSDI);
					            	e.setAttribute("harvested", "true");
					            	metadata.addContent(e);
				            	}
				            }
				            
				            XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
				            sortie.output(doc, new FileOutputStream(tempFile));
				        }
				        catch (JDOMException e) {
				            e.printStackTrace();
				        }
				        catch (IOException e) {
				            e.printStackTrace();
				        }
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
			try 
			{
		        byte[] buf = new byte[1024];
			    int nread;
			    while ((nread = is.read(buf)) >= 0) {
			    	os.write(buf, 0, nread);
			    }
			} 
			finally 
			{
				os.close();
				is.close();
				DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
				Date d = new Date();
				logger.info("ClientResponseDateTime="+ dateFormat.format(d));
				if (tempFile != null) 
				{
					logger.info("ClientResponseLength="+ tempFile.length());
					tempFile.delete();
				}
			}
		} 
		catch (SAXParseException e)
		{
			e.printStackTrace();
			logger.error(e.getMessage());
			resp.setHeader("easysdi-proxy-error-occured", "true");
			sendOgcExceptionBuiltInResponse(resp,generateOgcException("Response format not recognized. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
			logger.error(e.toString());
			resp.setHeader("easysdi-proxy-error-occured", "true");
			sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
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
			String constraint = "";
			String content = "";

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
				//TODO : verify the syntax for those 3 attributes
//				if (key.equalsIgnoreCase("OutputSchema")) 
//				{
//					requestedOutputSchema = value;
//					continue;
//				}
//				if (key.equalsIgnoreCase("ResultType")) 
//				{
//					requestedResutlType = value;
//					continue;
//				}
				if (key.equalsIgnoreCase("Constraint")) 
				{
					constraint = value;
					continue;
				}
				
				//Content specific vendor parameter
				if(key.equalsIgnoreCase("content"))
				{
					content = value;
					continue;
				}
			}
			logger.info("Request="+req.getQueryString());
			logger.info("RequestOperation="+ currentOperation);
			
			//Generate OGC exception if current operation is not allowed
			if(handleNotAllowedOperation(currentOperation,resp))
				return ;
			
			//GetRecords is not supported in GET request
			if(currentOperation.equalsIgnoreCase("GetRecords"))
				sendOgcExceptionBuiltInResponse(resp,generateOgcException("Operation not supported in a GET request","OperationNotSupported ","request", requestedVersion));
			
			//GetRecordById
			if(currentOperation.equalsIgnoreCase("GetRecordById"))
			{
				logger.trace("Start - Data Accessibility");
				CSWProxyDataAccessibilityManager cswDataManager = new CSWProxyDataAccessibilityManager(policy, getJoomlaProvider());
				if(!cswDataManager.isAllEasySDIDataAccessible())
				{
					String dataIDaccessible="";
					if(!cswDataManager.isObjectAccessible(requestedId))
					{
						sendProxyBuiltInResponse(resp,cswDataManager.generateEmptyResponse(requestedVersion));
						return;
					}
					if(!cswDataManager.isMetadataAccessible(requestedId))
						requestedId = cswDataManager.getMetadataVersionAccessible();
					
					if (requestedId == null)
					{
						sendProxyBuiltInResponse(resp,cswDataManager.generateEmptyResponse(requestedVersion));
						return;
					}
				}
				logger.trace("End - Data Accessibility");
				
			}
			//GetRecords
			//TODO : check the validity of this code for the support of the GET GetRecords
//			else if(currentOperation.equalsIgnoreCase("GetRecords"))
//			{
////				dump("INFO","Start - Data Accessibility");
////				CSWProxyDataAccessibilityManager cswDataManager = new CSWProxyDataAccessibilityManager(policy, getJoomlaProvider());
////				if(!cswDataManager.isAllDataAccessible())
////				{
////					cswDataManager.getAccessibleDataIds();
////					//Add a filter on the data id in the request
//////					constraint = cswDataManager.addFilterOnDataAccessible(configuration.getOgcSearchFilter(), URLDecoder.decode(constraint, "UTF-8"));
//////					dump("INFO", "GetRecords request send : "+constraint);
////				}
//			}
			
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
				else if (key.equalsIgnoreCase("constraint"))
				{
					paramUrl = paramUrl + key + "=" + URLEncoder.encode(constraint, "UTF-8") + "&";
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
			
			if(currentOperation.equalsIgnoreCase("GetRecords") || currentOperation.equalsIgnoreCase("GetRecordById"))
			{
				if( content.equalsIgnoreCase("") || content.equalsIgnoreCase("complete"))
				{
					logger.trace("Start - Complete metadata");
					//Build complete metadata
					CSWProxyMetadataContentManager cswManager = new CSWProxyMetadataContentManager(this);
					if ( !cswManager.buildCompleteMetadata(filePathList.get(0)))
					{
						sendOgcExceptionBuiltInResponse(resp, generateOgcException("Request can not be completed. "+cswManager.GetLastError(), "NoApplicableCode", "", requestedVersion));
						return;
					}
					logger.trace("End - Complete metadata");
				}
			}
			
			//Transform the request response
			transform(version, currentOperation, req, resp, filePathList);
			
		} 
		catch (AvailabilityPeriodException e) 
		{
			logger.error( e.getMessage());
			sendOgcExceptionBuiltInResponse(resp,generateOgcException(e.getMessage(),"OperationNotSupported","request",requestedVersion));
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
			logger.error( e.toString());
			resp.setHeader("easysdi-proxy-error-occured", "true");
			sendOgcExceptionBuiltInResponse(resp,generateOgcException(e.getMessage(),"NoApplicableCode","request",requestedVersion));
		}
	}



	/**
	 * 
	 */
	@SuppressWarnings("unchecked")
	@Override
	protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp) {
		try {
			//Read request
			XMLReader xr = XMLReaderFactory.createXMLReader();
			CswRequestHandler rh = new CswRequestHandler();
			xr.setContentHandler(rh);
			
			BufferedReader r = req.getReader();
			StringBuffer param = new StringBuffer();
			char[] buf = new char[4 * 1024]; // 4Kchar buffer
	        int len;
	        while ((len = r.read(buf, 0, buf.length)) != -1) {
	        	param.append(buf, 0, len);
	        }
			
			logger.info("Request="+param.toString().replace('\n',' ').replace('\r',' '));
			InputStreamReader in = new InputStreamReader(new ByteArrayInputStream(param.toString().getBytes()));
			xr.parse(new InputSource(in));
			
			String version = rh.getVersion();
			requestedVersion = version;
			if (version != null)
				version = version.replaceAll("\\.", "");
			String currentOperation = rh.getOperation();
			logger.info("RequestOperation="+ currentOperation);
			
			//Generate OGC exception if current operation is not allowed
			if(handleNotAllowedOperation(currentOperation,resp))
				return;

			//Check the value of the PARAMETER 'content'
			String content = rh.getContent();
			if( !content.equalsIgnoreCase("") && !content.equalsIgnoreCase("core") && !content.equalsIgnoreCase("complete"))
			{
				sendOgcExceptionBuiltInResponse(resp, generateOgcException("Invalid value for parameter 'content' : "+content, "InvalidParameterValue", "content", requestedVersion));
				return;
			}
			
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

			//Transaction
			if(currentOperation.equalsIgnoreCase("Transaction") && !configuration.getIsHarvestingConfig()){
				//If the transaction is INSERT OR UPDATE, add the specific node to indicate that the metadata is handle by EasySDI
				//sdi:platform harvested="false"
				if(rh.isTransactionInsert() || rh.isTransactionUpdate()){
					SAXBuilder sb = new SAXBuilder();
					Document doc = sb.build(new InputStreamReader(new ByteArrayInputStream(param.toString().getBytes())));
					
					Element racine = doc.getRootElement();
		            
		            //Get the transaction type element (Update or Insert) from the complete response file
		            List<Element> resultListStorage = new ArrayList<Element> ();
		            Iterator<Element> resultIterator = racine.getDescendants(new ElementTransactionTypeFilter());
		            while(resultIterator.hasNext()){
		            	Element result = resultIterator.next();
		            	resultListStorage.addAll(result.getChildren());
		            	for(int i=resultListStorage.size()-1;i>=0;i--)
		            	{
		            		if (((Element)resultListStorage.get(i)).getName().equalsIgnoreCase("Constraint") || ((Element)resultListStorage.get(i)).getName().equalsIgnoreCase("RecordProperty")){
		            			resultListStorage.remove(i);
		            		}
		            	}

		            }
		            
		            //Add a new node to the metadata element
		            //<gmd:MD_Metadata xmlns:sdi="http://www.easysdi.org/2011/sdi">
		            //<sdi:platform harvested="false" />
		            //</gmd:MD_Metadata>
		            Iterator<Element> resultStorageIterator = resultListStorage.iterator();
		            Element result = null;
		            Namespace nsSDI = Namespace.getNamespace("sdi","http://www.easysdi.org/2011/sdi") ;
		            while (resultStorageIterator.hasNext()){
		            	result = resultStorageIterator.next();
		            	if(result.getChild("harvested", nsSDI) == null){
			            	Element e = new Element("platform", nsSDI);
			            	e.setAttribute("harvested", "false");
			            	result.addContent(e);
		            	}
		            }
		            if(result != null)
		            	result.addNamespaceDeclaration(nsSDI);
		            
		            XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
		            ByteArrayOutputStream out = new ByteArrayOutputStream ();
		            sortie.output(doc, out);
		            param = new StringBuffer(out.toString("UTF-8"));
				}
			}
			
			if (currentOperation.equalsIgnoreCase("Transaction") && transactionType.equalsIgnoreCase("geonetwork")) 
			{
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
					logger.info( "ClientResponseDateTime="+ dateFormat.format(d));
					if (os != null) 
					{
						logger.info("ClientResponseLength="+ os.toString().length());
					}
					os.flush();
					os.close();
				}
				os = null;
				is = null;
			}
			//GetRecordById
			else if(currentOperation.equalsIgnoreCase("GetRecordById"))
			{
				logger.trace("Start - Data Accessibility");
				CSWProxyDataAccessibilityManager cswDataManager = new CSWProxyDataAccessibilityManager(policy, getJoomlaProvider());
				String dataId = rh.getRecordId();
				if(!cswDataManager.isAllEasySDIDataAccessible())
				{
					String dataIDaccessible="";
					if(!cswDataManager.isObjectAccessible(dataId))
					{
						sendProxyBuiltInResponse(resp,cswDataManager.generateEmptyResponse(requestedVersion));
						return;
					}
					if(!cswDataManager.isMetadataAccessible(dataId))
						dataId = cswDataManager.getMetadataVersionAccessible();
					
					if (dataId == null)
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
					}
				}
				logger.trace("End - Data Accessibility");
				
				List<String> filePathList = new Vector<String>();
				String filePath = sendData("POST", getRemoteServerUrl(0), param.toString());
				filePathList.add(filePath);
				if( rh.getContent().equalsIgnoreCase("") || rh.getContent().equalsIgnoreCase("complete"))
				{
					logger.trace("Start - Complete metadata");
					//Build complete metadata
					CSWProxyMetadataContentManager cswManager = new CSWProxyMetadataContentManager(this);
					if ( !cswManager.buildCompleteMetadata(filePathList.get(0)))
					{
						sendOgcExceptionBuiltInResponse(resp, generateOgcException("Request can not be completed. "+cswManager.GetLastError(), "NoApplicableCode", "", requestedVersion));
						return;
					}
					logger.trace("End - Complete metadata");
					
				}
				//Transform the request response
				transform(version, currentOperation, req, resp, filePathList);
			}
			//GetRecords
			else if(currentOperation.equalsIgnoreCase("GetRecords"))
			{
				logger.trace("Start - Data Accessibility");
				CSWProxyDataAccessibilityManager cswDataManager = new CSWProxyDataAccessibilityManager(policy, getJoomlaProvider());
				if(!cswDataManager.isAllDataAccessibleForGetRecords() || (policy.getGeographicFilter()!=null && policy.getGeographicFilter().length() != 0) || !policy.getIncludeHarvested())
				{
					//Add a filter on the data id in the request
					param = cswDataManager.addFilterOnDataAccessible(configuration.getOgcSearchFilter(), param);
					if(param == null)
					{
						sendProxyBuiltInResponse(resp,cswDataManager.generateEmptyResponseForGetRecords(requestedVersion));
						return;
					}
				}
				//Add a filter on the data id in the request
				logger.trace("End - Data Accessibility");
				
//				dump("INFO","Start - Get response");
				List<String> filePathList = new Vector<String>();
				String filePath = sendData("POST", getRemoteServerUrl(0), param.toString());
				filePathList.add(filePath);
//				dump("INFO","End - Get response");
				if( rh.getContent().equalsIgnoreCase("") || rh.getContent().equalsIgnoreCase("complete"))
				{
					logger.trace("Start - Complete metadata");
					//Build complete metadata
					CSWProxyMetadataContentManager cswManager = new CSWProxyMetadataContentManager(this);
					if ( !cswManager.buildCompleteMetadata(filePathList.get(0)))
					{
						sendOgcExceptionBuiltInResponse(resp, generateOgcException("Request can not be completed. "+cswManager.GetLastError(), "NoApplicableCode", "", requestedVersion));
						return;
					}
					logger.trace("End - Complete metadata");
					
				}
				//Transform the request response
				transform(version, currentOperation, req, resp, filePathList);
			}
			//Others operations
			else 
			{
				List<String> filePathList = new Vector<String>();
				String filePath = sendData("POST", getRemoteServerUrl(0), param.toString());
				filePathList.add(filePath);
				//Transform the request response
				transform(version, currentOperation, req, resp, filePathList);
			}
		} 
		catch (AvailabilityPeriodException e) 
		{
			logger.error( e.getMessage());
			sendOgcExceptionBuiltInResponse(resp,generateOgcException(e.getMessage(),"OperationNotSupported","request",requestedVersion));
		}
		catch (SAXParseException e)
		{
			logger.error(e.toString());
			resp.setHeader("easysdi-proxy-error-occured", "true");
			sendOgcExceptionBuiltInResponse(resp,generateOgcException("The query syntax is invalid","NoApplicableCode","",requestedVersion));
		}
		catch (Exception e) 
		{
			e.printStackTrace();
			logger.error( e.toString());
			resp.setHeader("easysdi-proxy-error-occured", "true");
			sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
		}
	}
}