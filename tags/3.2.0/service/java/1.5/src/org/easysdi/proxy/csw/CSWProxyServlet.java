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
import java.util.Arrays;
import java.util.Date;
import java.util.Enumeration;
import java.util.Iterator;
import java.util.List;
import java.util.UUID;
import java.util.Vector;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.exception.AvailabilityPeriodException;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.easysdi.xml.handler.CswRequestHandler;
import org.springframework.security.core.context.SecurityContextHolder;
import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

public class CSWProxyServlet extends ProxyServlet {

	private static final long serialVersionUID = 7288366261387138250L;
	private String[] CSWOperation = { "GetCapabilities", "GetRecords", "GetRecordById", "Harvest", "DescribeRecord", "GetExtrinsicContent", "Transaction","GetDomain" };

	/**
	 * Constructor
	 */
	public CSWProxyServlet ()
	{
		super();
		ServiceSupportedOperations = Arrays.asList("GetCapabilities", "GetRecords", "GetRecordById","DescribeRecord","Transaction");
	}
	
	public void init(ServletConfig config) throws ServletException {
		super.init(config);
	}

	public void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		super.doPost(req, resp);
	}

	public void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		super.doGet(req, resp);

	}
	
	protected StringBuffer generateOgcException(String errorMessage, String code, String locator, String version) {
		logger.error(errorMessage);
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8'?>\n");
		sb.append("<ows:ExceptionReport xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" version=\"1.0.0\" xsi:schemaLocation=\"http://www.opengis.net/ows http://schemas.opengis.net/ows/1.0.0/owsExceptionReport.xsd\">\n");
		sb.append("\t<ows:Exception ");
		if(code != null && code != "")
		{
			sb.append(" exceptionCode=\"");
			sb.append(code);
			sb.append("\"");
		}
		if(locator != null && locator != "")
		{
			sb.append(" locator=\"");
			sb.append(locator);
			sb.append("\"");
		}
		sb.append(">\n");
		sb.append("\t\t<ows:ExceptionText>");
		sb.append(errorMessage);
		sb.append("</ows:ExceptionText>\n");
		sb.append("\t</ows:Exception>\n");
		sb.append("</ows:ExceptionReport>");
		return sb;
	}

	protected StringBuffer buildCapabilitiesXSLT(HttpServletRequest req) {

		try {
			String url = getServletUrl(req);
			List<String> permitedOperations = new Vector<String>();
			List<String> deniedOperations = new Vector<String>();

			// Fill the vectors with the corresponding information
			for (int i = 0; i < CSWOperation.length; i++) {

					if (ServiceSupportedOperations.contains(CSWOperation[i]) && isOperationAllowed(CSWOperation[i])) 
					{
						permitedOperations.add(CSWOperation[i]);
						logger.trace(CSWOperation[i] + " is permitted");
					} else {
						deniedOperations.add(CSWOperation[i]);
						logger.trace(CSWOperation[i] + " is denied");
					}
			}

			return generateXSLTForCSWCapabilities200(url, deniedOperations, permitedOperations);
		} catch (Exception e) {
			e.printStackTrace();
			logger.error( e.getMessage());
		}

		// If something goes wrong, an empty stylesheet is returned.
		StringBuffer sb = new StringBuffer();
		return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
	}

	protected StringBuffer buildServiceMetadataCapabilitiesXSLT() 
	{
		StringBuffer serviceMetadataXSLT = new StringBuffer();
		serviceMetadataXSLT.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?><xsl:stylesheet version=\"1.00\" xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
		serviceMetadataXSLT.append("<xsl:output method=\"xml\" omit-xml-declaration=\"no\" version=\"1.0\" encoding=\"UTF-8\" indent=\"yes\"/>");
		serviceMetadataXSLT.append("<xsl:strip-space elements=\"*\" />");
		//Copy all
		serviceMetadataXSLT.append("<xsl:template match=\"node()|@*\">");
		serviceMetadataXSLT.append("<!-- Copy the current node -->");
		serviceMetadataXSLT.append("<xsl:copy>");
		serviceMetadataXSLT.append("<!-- Including any attributes it has and any child nodes -->");
		serviceMetadataXSLT.append("<xsl:apply-templates select=\"@*|node()\"/>");
		serviceMetadataXSLT.append("</xsl:copy>");
		serviceMetadataXSLT.append("</xsl:template>");
		
		//Replace Service node
		serviceMetadataXSLT.append("<xsl:template match=\"ows:ServiceIdentification\">");
		serviceMetadataXSLT.append("<xsl:copy>");
		
		//Service type
		serviceMetadataXSLT.append("<xsl:copy-of select=\"ows:ServiceType\"/>");
		serviceMetadataXSLT.append("<xsl:copy-of select=\"ows:ServiceTypeVersion\"/>");
		
		//Title
		serviceMetadataXSLT.append("<xsl:element name=\"ows:Title\"> ");
		serviceMetadataXSLT.append("<xsl:text>" + getConfiguration().getTitle() + "</xsl:text>");
		serviceMetadataXSLT.append("</xsl:element>");
		//Abstract
		if(getConfiguration().getAbst()!=null)
		{
			serviceMetadataXSLT.append("<xsl:element name=\"ows:Abstract\"> ");
			serviceMetadataXSLT.append("<xsl:text>" + getConfiguration().getAbst() + "</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
		}
		//Keyword
		if(getConfiguration().getKeywordList()!= null)
		{
			List<String> keywords = getConfiguration().getKeywordList();
			serviceMetadataXSLT.append("<xsl:element name=\"ows:Keywords\"> ");
			for (int n = 0; n < keywords.size(); n++) {
				serviceMetadataXSLT.append("<xsl:element name=\"ows:Keyword\"> ");
				serviceMetadataXSLT.append("<xsl:text>" + keywords.get(n) + "</xsl:text>");
				serviceMetadataXSLT.append("</xsl:element>");
			}
			serviceMetadataXSLT.append("</xsl:element>");
		}
		//Fees
		if(getConfiguration().getFees() !=null)
		{
			serviceMetadataXSLT.append("<xsl:element name=\"ows:Fees\"> ");
			serviceMetadataXSLT.append("<xsl:text>" + getConfiguration().getFees() + "</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
		}
		//AccesConstraints
		if(getConfiguration().getAccessConstraints()!=null)
		{
			serviceMetadataXSLT.append("<xsl:element name=\"ows:AccessConstraints\"> ");
			serviceMetadataXSLT.append("<xsl:text>" + getConfiguration().getAccessConstraints() + "</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
		}
		serviceMetadataXSLT.append("</xsl:copy>");
		serviceMetadataXSLT.append("</xsl:template>");
		
		serviceMetadataXSLT.append("<xsl:template match=\"ows:ServiceProvider\"> ");
		serviceMetadataXSLT.append("<xsl:copy>");
		
		//contactInfo
		if(getConfiguration().getContactInfo() != null && !getConfiguration().getContactInfo().isEmpty())
		{
				if(configuration.getContactInfo().getOrganization()!=null){
					serviceMetadataXSLT.append("<xsl:element name=\"ows:ProviderName\"> ");
					serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getOrganization() + "</xsl:text>");
					serviceMetadataXSLT.append("</xsl:element>");
				}
				serviceMetadataXSLT.append("<xsl:element name=\"ows:ServiceContact\"> ");//ows:ServiceContact
				if(configuration.getContactInfo().getName()!=null){
					serviceMetadataXSLT.append("<xsl:element name=\"ows:IndividualName\"> ");
					serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getName()+ "</xsl:text>");
					serviceMetadataXSLT.append("</xsl:element>");
				}
				if(configuration.getContactInfo().getPosition()!=null){
					serviceMetadataXSLT.append("<xsl:element name=\"ows:PositionName\"> ");
					serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getPosition()+ "</xsl:text>");
					serviceMetadataXSLT.append("</xsl:element>");
				}
				
				if(configuration.getContactInfo()!=null && !configuration.getContactInfo().isEmpty())
				{
					serviceMetadataXSLT.append("<xsl:element name=\"ows:ContactInfo\"> ");//ows:ContactInfo
					
					if(configuration.getContactInfo().getVoicePhone()!=null || configuration.getContactInfo().getFacSimile()!=null)
					{
						serviceMetadataXSLT.append("<xsl:element name=\"ows:Phone\"> ");//ows:Phone
						if(configuration.getContactInfo().getVoicePhone()!=null)
						{
							serviceMetadataXSLT.append("<xsl:element name=\"ows:Voice\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getVoicePhone()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getFacSimile()!=null)
						{
							serviceMetadataXSLT.append("<xsl:element name=\"ows:Facsimile\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getFacSimile()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						serviceMetadataXSLT.append("</xsl:element>");//ows:Phone
					}
					
					if (configuration.getContactInfo().getContactAddress() != null && !configuration.getContactInfo().getContactAddress().isEmpty())
					{
						serviceMetadataXSLT.append("<xsl:element name=\"ows:Address\"> ");//ows:Address
						if(configuration.getContactInfo().getContactAddress().getAddress()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\"ows:DelivryPoint\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getAddress()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getContactAddress().getCity()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\"ows:City\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getCity()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getContactAddress().getState()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\"ows:AdministrativeArea\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getState()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getContactAddress().getPostalCode()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\"ows:PostalCode\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getPostalCode()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getContactAddress().getCountry()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\"ows:Country\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getCountry()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().geteMail()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\"ows:ElectronicMailAddress\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().geteMail()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						serviceMetadataXSLT.append("</xsl:element>");//ows:Address
					}
					if(configuration.getContactInfo().getLinkage()!=null)
					{
						serviceMetadataXSLT.append("<xsl:element name=\"ows:OnlineResource\"> ");
						serviceMetadataXSLT.append("<xsl:attribute name=\"xlink:href\">");
						serviceMetadataXSLT.append(configuration.getContactInfo().getLinkage());
						serviceMetadataXSLT.append("</xsl:attribute>");
						serviceMetadataXSLT.append("</xsl:element>");
					}
					serviceMetadataXSLT.append("</xsl:element>");//ows:ContactInfo
					serviceMetadataXSLT.append("</xsl:element>");//ows:ServiceContact
				}
		}
		serviceMetadataXSLT.append("</xsl:copy>");
		serviceMetadataXSLT.append("</xsl:template>");
		serviceMetadataXSLT.append("</xsl:stylesheet>");
		
		return serviceMetadataXSLT;
	}
	
	/***
	 * Builds the xslt that will be applied against the csw capabilities version
	 * 2.00 The generated xslt will remove the unauthorized operations from the
	 * capabilities document and will replace the urls
	 * 
	 * @param req
	 *            the HttpServletRequest request
	 * @return StringBuffer containing the xslt
	 */
	protected StringBuffer generateXSLTForCSWCapabilities200(String url, List<String> deniedOperations, List<String> permitedOperations) {

		try {

			StringBuffer CSWCapabilities200 = new StringBuffer();
			CSWCapabilities200.append("<?xml version=\"1.0\" encoding=\"UTF-8\" ?><xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
			CSWCapabilities200.append("<xsl:template match=\"ows:Get\">");
			CSWCapabilities200.append("<ows:Get>");
			CSWCapabilities200.append("<xsl:attribute name=\"xlink:href\">");
			CSWCapabilities200.append(url);
			CSWCapabilities200.append("</xsl:attribute>");
			CSWCapabilities200.append("<xsl:attribute name=\"xlink:type\">");
			CSWCapabilities200.append("<xsl:value-of select=\"@xlink:type\"/>");
			CSWCapabilities200.append("</xsl:attribute>");
			CSWCapabilities200.append("</ows:Get>");
			CSWCapabilities200.append("</xsl:template>");
			CSWCapabilities200.append("<xsl:template match=\"ows:Post\">");
			CSWCapabilities200.append("<ows:Post>");
			CSWCapabilities200.append("<xsl:attribute name=\"xlink:href\">");
			CSWCapabilities200.append(url);
			CSWCapabilities200.append("</xsl:attribute>");
			CSWCapabilities200.append("<xsl:attribute name=\"xlink:type\">");
			CSWCapabilities200.append("<xsl:value-of select=\"@xlink:type\"/>");
			CSWCapabilities200.append("</xsl:attribute>");
			CSWCapabilities200.append("</ows:Post>");
			CSWCapabilities200.append("</xsl:template>");

			if (hasPolicy) {
				if (!policy.getOperations().isAll() || deniedOperations.size() > 0) {
					Iterator<String> it = permitedOperations.iterator();
					while (it.hasNext()) {
						String text = it.next();
						if (text != null) {
							CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation[@name='");
							CSWCapabilities200.append(text);
							CSWCapabilities200.append("']\">");
							CSWCapabilities200.append("<!-- Copy the current node -->");
							CSWCapabilities200.append("<xsl:copy>");
							CSWCapabilities200.append("<!-- Including any attributes it has and any child nodes -->");
							CSWCapabilities200.append("<xsl:apply-templates select=\"@*|node()\"/>");
							CSWCapabilities200.append("</xsl:copy>");
							CSWCapabilities200.append("</xsl:template>");
						}
					}

					it = deniedOperations.iterator();
					while (it.hasNext()) {
						CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation[@name='");
						CSWCapabilities200.append(it.next());
						CSWCapabilities200.append("']\"></xsl:template>");
					}
				}
				if (permitedOperations.size() == 0 ) {
					CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation\"></xsl:template>");
				}
			}
			else
			{
				CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation\"></xsl:template>");
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
		} catch (Exception e) {
			e.printStackTrace();
			logger.error(e.getMessage());
		}

		// If something goes wrong, an empty stylesheet is returned.
		StringBuffer sb = new StringBuffer();
		return sb
				.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
	}

	public void transform(String version, String currentOperation, HttpServletRequest req, HttpServletResponse resp, List<String> filePathList) {

		if (isOperationAllowed(currentOperation)) {
			try {

				String userXsltPath = getConfiguration().getXsltPath();
				if (SecurityContextHolder.getContext().getAuthentication() != null) {
					userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
				}

				userXsltPath = userXsltPath + "/" + version + "/" + currentOperation + ".xsl";
				String globalXsltPath = getConfiguration().getXsltPath() + "/" + version + "/" + currentOperation + ".xsl";
				;
				File xsltFile = new File(userXsltPath);
				boolean isPostTreat = false;
				if (!xsltFile.exists()) {
					logger.trace("Postreatment file " + xsltFile.toString() + "does not exist");
					xsltFile = new File(globalXsltPath);
					if (xsltFile.exists()) {
						isPostTreat = true;
					} else {
						logger.trace("Postreatment file " + xsltFile.toString() + "does not exist");
					}
				} else {
					isPostTreat = true;
				}

				// Transforms the results using a xslt before sending the response back
				InputStream xml = new FileInputStream(filePathList.get(0));
				TransformerFactory tFactory = TransformerFactory.newInstance();
				File tempFile = null;
				FileOutputStream tempFos = null;
				Transformer transformer = null;

				if (currentOperation != null) {

					if (currentOperation.equals("GetCapabilities")) {
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
						xslt_service.close();
						
						tempFile = tempFileCapaWithMetadata;
						logger.trace("transform end GetCapabilities");
						
					} else {
						if ("GetRecords".equals(currentOperation) || "GetRecordById".equals(currentOperation)) {

							if (areAllAttributesAllowedForMetadata(getRemoteServerUrl(0))) {
								// Keep the metadata as it is
								tempFile = new File(filePathList.get(0));
							} else {

								tempFile = createTempFile(UUID.randomUUID().toString(), ".xml");
								tempFos = new FileOutputStream(tempFile);

								InputStream xslt = new ByteArrayInputStream(generateXSLTForMetadata().toString().getBytes());
								transformer = tFactory.newTransformer(new StreamSource(xslt));
								transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
								tempFos.flush();
								tempFos.close();
								xslt.close();
							}

						} else
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
				try {
					while ((byteRead = is.read()) != -1) {
						os.write(byteRead);
					}
				} finally {
					os.close();
					is.close();
					DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
					Date d = new Date();
					logger.info("ClientResponseDateTime="+ dateFormat.format(d));

					if (tempFile != null) {
						logger.info("ClientResponseLength="+ tempFile.length());
						tempFile.delete();
					}

				}

			} catch (Exception e) {
				e.printStackTrace();
				logger.error( e.toString());
				resp.setHeader("easysdi-proxy-error-occured", "true");
				sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
			}
		} else {
			try {
				resp.setContentType("application/xml");
				resp.setContentLength(Integer.MAX_VALUE);
				OutputStream os = resp.getOutputStream();
				os.write(generateOgcException("Operation not allowed","","",requestedVersion).toString().getBytes());
				os.flush();
				os.close();
			} catch (Exception e) {
				e.printStackTrace();
				logger.error( e.toString());
				resp.setHeader("easysdi-proxy-error-occured", "true");
				sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
			}
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
			
			Enumeration<String> parameterNames = req.getParameterNames();
			String paramUrl = "";
	
			while (parameterNames.hasMoreElements()) 
			{
				String key = (String) parameterNames.nextElement();
				String value = URLEncoder.encode(req.getParameter(key),"UTF-8");
				
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
				}
				if (key.equalsIgnoreCase("version")) 
				{
					requestedVersion = value;
				}
			}
			
			//Generate OGC exception if current operation is not allowed
			if(handleNotAllowedOperation(currentOperation,resp))
				return ;
					
			
			// Build the request to dispatch
			parameterNames = req.getParameterNames();
			while (parameterNames.hasMoreElements()) 
			{
				String key = (String) parameterNames.nextElement();
				String value = URLEncoder.encode(req.getParameter(key),"UTF-8");
				
				paramUrl = paramUrl + key + "=" + value + "&";
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
			logger.error( e.getMessage());
			sendOgcExceptionBuiltInResponse(resp,generateOgcException(e.getMessage(),"OperationNotSupported ","request",requestedVersion));
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
			logger.error( e.toString());
			resp.setHeader("easysdi-proxy-error-occured", "true");
			sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
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
				logger.debug(input);
				param.append(input);
			}

			xr.parse(new InputSource(new InputStreamReader(new ByteArrayInputStream(param.toString().getBytes()))));

			String version = rh.getVersion();
			requestedVersion = version;
			
			String currentOperation = rh.getOperation();

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
			if (rsi != null) {
				transactionType = rsi.getTransaction();
			}

			if (currentOperation.equalsIgnoreCase("Transaction") && transactionType.equalsIgnoreCase("geonetwork")) {

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
						os.flush();
						os.close();
					}
					os = null;
					is = null;
				}

			}
			else {
				if (version != null)
					version = version.replaceAll("\\.", "");

				// dump (param.toString());
				List<String> filePathList = new Vector<String>();
				String filePath = sendData("POST", getRemoteServerUrl(0), param.toString());
				filePathList.add(filePath);
				transform(version, currentOperation, req, resp, filePathList);
			}
		} catch (AvailabilityPeriodException e) 
		{
			logger.error( e.getMessage());
			sendOgcExceptionBuiltInResponse(resp,generateOgcException(e.getMessage(),"OperationNotSupported ","request",requestedVersion));
		} catch (Exception e) {
			e.printStackTrace();
			logger.error( e.toString());
			resp.setHeader("easysdi-proxy-error-occured", "true");
			sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
		}
	}

	protected StringBuffer generateXSLTForMetadata()  
	{
		try 
		{
			StringBuffer CSWCapabilities200 = new StringBuffer();

			CSWCapabilities200.append("<xsl:stylesheet version=\"1.00\" xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" xmlns:gco=\"http://www.isotc211.org/2005/gco\" xmlns:ns3=\"http://www.isotc211.org/2005/gmx\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:gml=\"http://www.opengis.net/gml\" xmlns:gts=\"http://www.isotc211.org/2005/gts\"    xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:csw=\"http://www.opengis.net/cat/csw\" xmlns:ows=\"http://www.opengis.net/ows\">");

			List<String> notAllowedAttributeList = getAttributesNotAllowedInMetadata(getRemoteServerUrl(0));
			int nsI = 0;
			for (int i = 0; i < notAllowedAttributeList.size(); i++) 
			{
				String text = notAllowedAttributeList.get(i);
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
		}

		// If something goes wrong, an empty stylesheet is returned.
		StringBuffer sb = new StringBuffer();
		return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
	}

	StringBuffer generateInfoFileForMef(String uuid, String siteId) 
	{
		StringBuffer info = new StringBuffer();
		info.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
		info.append("<info version=\"1.0\">");
		info.append("<general>");
		info.append("<createDate>2008-02-29T18:05:34</createDate>");
		info.append("<changeDate>2008-02-29T18:05:34</changeDate>");
		info.append("<schema>iso19139</schema>");
		info.append("<isTemplate>false</isTemplate>");
		info.append("<localId>906</localId>");
		info.append("<format>full</format>");
		info.append("<uuid>" + uuid + "</uuid>");
		info.append("<siteId>" + siteId + "</siteId>");
		info.append("<siteName>dummy</siteName>");
		info.append("</general>");
		info.append("<categories />");
		info.append("<privileges>");
		info.append("<group name=\"sample\">");
		info.append("<operation name=\"view\" />");
		info.append("<operation name=\"download\" />");
		info.append("<operation name=\"notify\" />");
		info.append("<operation name=\"dynamic\" />");
		info.append("</group>");
		info.append("</privileges>");
		info.append("<public>");
		info.append("</public>");
		info.append("<private>");
		info.append("</private>");
		info.append("</info>");

		return info;
	}

	public static void main(String args[]) {

		try {
			StringBuffer xml = new StringBuffer();
			xml.append("<csw:Transaction service=\"CSW\" ");
			xml.append("version=\"2.0.0\" ");
			xml.append("xmlns:csw=\"http://www.opengis.net/cat/csw\">");
			xml.append("<csw:Insert>");
			xml
					.append("<Record xmlns=\"http://www.opengis.net/cat/csw\" xmlns:dc=\"http://www.purl.org/dc/elements/1.1/\" xmlns:dct=\"http://www.purl.org/dc/terms/\" xmlns:ows=\"http://www.opengis.net/ows\" >");
			xml.append("<dc:contributor scheme=\"http://www.example.com\">John</dc:contributor>");
			xml.append("<dc:identifier >REC-2</dc:identifier>");
			xml.append("<ows:WGS84BoundingBox crs=\"urn:opengis:crs:OGC:2:84\" dimensions=\"2\">");
			xml.append("<ows:LowerCorner>12 12</ows:LowerCorner>");
			xml.append("<ows:UpperCorner>102 102</ows:UpperCorner>");
			xml.append("</ows:WGS84BoundingBox>");
			xml.append("</Record>");
			xml.append("</csw:Insert>");
			xml.append("</csw:Transaction>");

			System.out.println(xml);

		} catch (Exception e) {
			e.printStackTrace();
		}

	}
}