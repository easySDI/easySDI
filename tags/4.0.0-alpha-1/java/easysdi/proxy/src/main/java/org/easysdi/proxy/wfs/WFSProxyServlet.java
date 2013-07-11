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
package org.easysdi.proxy.wfs;

import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.io.StringReader;
import java.io.StringWriter;
import java.net.URI;
import java.util.Arrays;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.TreeMap;
import java.util.UUID;
import java.util.Vector;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.TimeUnit;
import java.util.logging.Level;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathFactory;

import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.UsernamePasswordCredentials;
import org.apache.commons.httpclient.auth.AuthScope;
import org.apache.commons.httpclient.methods.GetMethod;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.core.ProxyServletRequest;
import org.easysdi.proxy.domain.SdiFeaturetypePolicy;
import org.easysdi.proxy.domain.SdiIncludedattribute;
import org.easysdi.proxy.domain.SdiPhysicalservicePolicy;
import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiSysOperationcompliance;
import org.easysdi.proxy.domain.SdiVirtualmetadata;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.exception.AvailabilityPeriodException;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.xml.handler.RequestHandler;
import org.easysdi.proxy.xml.resolver.ResourceResolver;
import org.geotools.data.ows.FeatureSetDescription;
import org.geotools.feature.Feature;
import org.geotools.feature.FeatureCollection;
import org.geotools.feature.FeatureIterator;
import org.geotools.filter.Filter;
import org.geotools.filter.FilterDOMParser;
import org.geotools.gml.producer.FeatureTransformer;
import org.geotools.referencing.CRS;
import org.geotools.renderer.shape.FilterTransformer;
import org.geotools.xml.DocumentFactory;
import org.geotools.xml.SchemaFactory;
import org.geotools.xml.XMLHandlerHints;
import org.geotools.xml.XSISAXHandler;
import org.geotools.xml.gml.GMLFeatureCollection;
import org.geotools.xml.schema.ComplexType;
import org.geotools.xml.schema.Schema;
import org.jdom.Namespace;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.springframework.security.core.context.SecurityContextHolder;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.ls.LSSerializer;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import com.google.common.collect.HashMultimap;
import com.google.common.collect.Multimap;

/**
 * @author rmi
 * 
 */
public class WFSProxyServlet extends ProxyServlet {
	
	private static final long serialVersionUID = 8143233381180843582L;

	private SAXParser parser;
	private List<Integer> serversIndex = new Vector<Integer>(); 
	// list of server index corresponding to the response file index
	private List<String> policyServersPrefix = new Vector<String>(); 
	// list of prefix per server
	private List<String> policyServersNamespace = new Vector<String>(); 
	// list of namespace per server
	private List<WFSProxyGeomAttributes> WFSProxyGeomAttributesList = new Vector<WFSProxyGeomAttributes>(); 
	// list of WFSProxyGeomAttributes objects, un objet contient tout les liens
	// Server-Prefix-Namespace-AuthorizedFeature-ifExist(GeomAttribute)


	//Store all the possible operations for a WFS service
	//Used in buildCapabilitiesXSLT()
	private String[] WFSOperation = { "GetCapabilities", "DescribeFeatureType", "GetFeature", "Transaction", "LockFeature", "GetGmlObject", "GetFeatureWithLock"};
	//Store operations supported by the current version of the proxy
	//Update this list to reflect proxy's capabilities
	//private static final List<String> WFSSupportedOperations = Arrays.asList("GetCapabilities", "DescribeFeature", "GetFeature","Transaction");
	private static final List<String> ServiceSupportedOperations = Arrays.asList("GetCapabilities", "DescribeFeatureType", "GetFeature","Transaction");

	/**
	 * 
	 */
	protected Vector<String> featureTypePathList = new Vector<String>(); 
	/**
     *  Contient	le featureTypetoKeep.get(0) (->reference pour le filtre remoteFilter) par Server
     *  Debug tb 04.06.2009
     */
    protected List<String> policyAttributeListToKeepPerFT = new Vector<String>();
    /**
     * 
     */
    protected int policyAttributeListNb = 0;
    /**
     * Liste des fichiers réponses de chaque serveur qui contiennent des erreurs OGC
     */
    protected Multimap<Integer, String> ogcExceptionFilePathList = HashMultimap.create();
    /**
     * 
     */
    protected Map<Integer, String> wfsFilePathList = new TreeMap<Integer, String>();

	/**
	 * Constructor
	 */
	public WFSProxyServlet (ProxyServletRequest proxyRequest, SdiVirtualservice virtualService, SdiPolicy policy)
	{
		super(proxyRequest, virtualService, policy);
		owsExceptionReport = new WFSExceptionReport();
	}

	protected StringBuffer generateOgcException(String errorMessage, String code, String locator, String version) {
		logger.error(errorMessage);
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>\n");
		sb.append("<ServiceExceptionReport xmlns=\"http://www.opengis.net/ogc\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengis.net/ogc\" version=\"1.2.0\">\n");
		sb.append("\t<ServiceException ");
		if(code != null && code != "")
		{
			sb.append(" code=\"");
			sb.append(code);
			sb.append("\"");
		}
		if(locator != null && locator != "")
		{
			sb.append(" locator=\"");
			sb.append(locator);
			sb.append("\"");
		}
		sb.append(">");
		sb.append(errorMessage);
		sb.append("</ServiceException>\n");
		sb.append("</ServiceExceptionReport>");
		return sb;
	}
	// ***************************************************************************************************************************************
	protected StringBuffer buildCapabilitiesXSLT(HttpServletRequest req, int remoteServerIndex) {

		try {

			String url = getServletUrl(req);

			//Retrieve allowed and denied operations from the policy
			List<String> permitedOperations = new Vector<String>();
			List<String> deniedOperations = new Vector<String>();
			for (int i = 0; i < WFSOperation.length; i++) 
			{
				if (ServiceSupportedOperations.contains(WFSOperation[i]) && isOperationAllowed(WFSOperation[i])) 
				{
					permitedOperations.add(WFSOperation[i]);
					logger.trace(WFSOperation[i] + " is permitted");
				} else 
				{
					deniedOperations.add(WFSOperation[i]);
					logger.trace(WFSOperation[i] + " is denied");
				}
			}
//			Set<SdiSysOperationcompliance> operationCompliances = getProxyRequest().getServiceCompliance().getSdiSysOperationcompliances();
//			Iterator<SdiSysOperationcompliance> ic = operationCompliances.iterator();
//			while (ic.hasNext())
//			{
//				SdiSysOperationcompliance compliance = ic.next();
//				if(compliance.getSdiSysServiceoperation().getState() == 1 && compliance.getState() == 1 && compliance.isImplemented() && isOperationAllowed(compliance.getSdiSysServiceoperation().getValue()))
//				{
//					permitedOperations.add(compliance.getSdiSysServiceoperation().getValue());
//					logger.trace(compliance.getSdiSysServiceoperation().getValue() + " is permitted");
//				}
//				else
//				{
//					deniedOperations.add(compliance.getSdiSysServiceoperation().getValue());
//					logger.trace(compliance.getSdiSysServiceoperation().getValue() + " is denied");
//				}
//			}

			StringBuffer WFSCapabilities100 = new StringBuffer();

			WFSCapabilities100.append("<xsl:stylesheet version=\"1.00\" xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
			WFSCapabilities100.append("<xsl:template match=\"wfs:OnlineResource\">");
			WFSCapabilities100.append("<xsl:element name=\"OnlineResource\" namespace=\"http://www.opengis.net/wfs\">" + url + "</xsl:element>");
			// Changer seulement la partie racine de l'URL, pas les
			// param après '?'
			WFSCapabilities100.append("</xsl:template>");
			WFSCapabilities100.append("<xsl:template match=\"@onlineResource\">");
			WFSCapabilities100.append("<xsl:param name=\"thisValue\">");
			WFSCapabilities100.append("<xsl:value-of select=\".\"/>");
			WFSCapabilities100.append("</xsl:param>");
			WFSCapabilities100.append("<xsl:attribute name=\"onlineResource\">");
			WFSCapabilities100.append(url);
			// Changer seulement la partie racine de l'URL, pas les
			// param après '?'
			WFSCapabilities100.append("<xsl:value-of select=\"substring-after($thisValue,'" + getPhysicalServiceURLByIndex(remoteServerIndex) + "')\"/>");
			WFSCapabilities100.append("</xsl:attribute>");
			WFSCapabilities100.append("</xsl:template>");

			if (!sdiPolicy.isAnyoperation() || deniedOperations.size() > 0) {
				Iterator<String> it = permitedOperations.iterator();
				while (it.hasNext()) {
					String text = it.next();
					if (text != null) {
						WFSCapabilities100.append("<xsl:template match=\"wfs:Capability/wfs:Request/wfs:");
						WFSCapabilities100.append(text);
						WFSCapabilities100.append("\">");
						WFSCapabilities100.append("<!-- Copy the current node -->");
						WFSCapabilities100.append("<xsl:copy>");
						WFSCapabilities100.append("<!-- Including any attributes it has and any child nodes -->");
						WFSCapabilities100.append("<xsl:apply-templates select=\"@*|node()\"/>");
						WFSCapabilities100.append("</xsl:copy>");
						WFSCapabilities100.append("</xsl:template>");
					}
				}

				it = deniedOperations.iterator();
				while (it.hasNext()) {
					WFSCapabilities100.append("<xsl:template match=\"wfs:Capability/wfs:Request/wfs:");
					WFSCapabilities100.append(it.next());
					WFSCapabilities100.append("\"></xsl:template>");
				}
			}
			if (permitedOperations.size() == 0 )
			{
				WFSCapabilities100.append("<xsl:template match=\"wfs:Capability/wfs:Request/\"></xsl:template>");
			}
			WFSCapabilities100.append("  <!-- Whenever you match any node or any attribute -->");
			WFSCapabilities100.append("<xsl:template match=\"node()|@*\">");
			WFSCapabilities100.append("<!-- Copy the current node -->");
			WFSCapabilities100.append("<xsl:copy>");
			WFSCapabilities100.append("<!-- Including any attributes it has and any child nodes -->");
			WFSCapabilities100.append("<xsl:apply-templates select=\"@*|node()\"/>");
			WFSCapabilities100.append("</xsl:copy>");
			WFSCapabilities100.append("</xsl:template>");

			Map<String, Object> hints = new HashMap<String, Object>();
			hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);
			org.geotools.data.ows.WFSCapabilities doc = (org.geotools.data.ows.WFSCapabilities) DocumentFactory.getInstance(new File(wfsFilePathList
					.get(remoteServerIndex)).toURI(), hints, Level.WARNING);
			if (doc != null) {
				List<FeatureSetDescription> l = doc.getFeatureTypes();
				for (int i = 0; i < l.size(); i++) {
					String ftName = l.get(i).getName();
					String tmpFT = ftName;
					if (tmpFT != null) {
						//String[] s = tmpFT.split(":");
						//tmpFT = s[s.length - 1];
					}
					if (!isFeatureTypeAllowed(tmpFT, getPhysicalServiceURLByIndex(remoteServerIndex))) {
						WFSCapabilities100.append("<xsl:template match=\"//wfs:FeatureType[starts-with(wfs:Name,'" + ftName + "')]\">");
						WFSCapabilities100.append("</xsl:template>");
					}
				}
			}
			
			WFSCapabilities100.append("</xsl:stylesheet>");
			return WFSCapabilities100;
		} catch (Exception e) {
			e.printStackTrace();
			logger.error(e.getMessage());
		}

		// If something goes wrong, an empty stylesheet is returned.
		StringBuffer sb = new StringBuffer();
		return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
	}

	protected StringBuffer buildServiceMetadataCapabilitiesXSLT(String version) 
	{
		try
		{
			//Warning : only for version=1.0.0
			StringBuffer serviceMetadataXSLT = new StringBuffer();
			serviceMetadataXSLT.append("<xsl:stylesheet version=\"1.00\" xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
			serviceMetadataXSLT.append("<xsl:output method=\"xml\" omit-xml-declaration=\"no\" version=\"1.0\" encoding=\"UTF-8\" indent=\"yes\"/>");
			serviceMetadataXSLT.append("<xsl:strip-space elements=\"*\" />");
			serviceMetadataXSLT.append("<xsl:template match=\"node()|@*\">");
			serviceMetadataXSLT.append("<!-- Copy the current node -->");
			serviceMetadataXSLT.append("<xsl:copy>");
			serviceMetadataXSLT.append("<!-- Including any attributes it has and any child nodes -->");
			serviceMetadataXSLT.append("<xsl:apply-templates select=\"@*|node()\"/>");
			serviceMetadataXSLT.append("</xsl:copy>");
			serviceMetadataXSLT.append("</xsl:template>");

			if(sdiVirtualService.isReflectedmetadata())
			{
				serviceMetadataXSLT.append("</xsl:stylesheet>");
				return serviceMetadataXSLT;
			}
				
			SdiVirtualmetadata metadata = sdiVirtualService.getSdiVirtualmetadatas().iterator().next();
			serviceMetadataXSLT.append("<xsl:template match=\"wfs:Service\">");
			serviceMetadataXSLT.append("<xsl:copy>");
			//Name
			serviceMetadataXSLT.append("<xsl:element name=\"Name\" namespace=\"http://www.opengis.net/wfs\"> ");
			serviceMetadataXSLT.append("<xsl:text>WFS</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
			//Title
			if(!metadata.isInheritedtitle())
			{
				serviceMetadataXSLT.append("<xsl:element name=\"Title\" namespace=\"http://www.opengis.net/wfs\"> ");
				serviceMetadataXSLT.append("<xsl:text>" + metadata.getTitle() + "</xsl:text>");
				serviceMetadataXSLT.append("</xsl:element>");
			}
			//Abstract
			if(!metadata.isInheritedsummary())
			{
				serviceMetadataXSLT.append("<xsl:element name=\"Abstract\" namespace=\"http://www.opengis.net/wfs\"> ");
				serviceMetadataXSLT.append("<xsl:text>" + metadata.getSummary() + "</xsl:text>");
				serviceMetadataXSLT.append("</xsl:element>");
			}
			//Keyword
			if(!metadata.isInheritedkeyword())
			{
				serviceMetadataXSLT.append("<xsl:element name=\"Keywords\" namespace=\"http://www.opengis.net/wfs\"> ");
				serviceMetadataXSLT.append("<xsl:text>" + metadata.getKeyword() + "</xsl:text>");
				serviceMetadataXSLT.append("</xsl:element>");
			}
			//OnlineResource
			serviceMetadataXSLT.append("<xsl:copy-of select=\"wfs:OnlineResource\" />");

			//Fees
			if(!metadata.isInheritedfee())
			{
				serviceMetadataXSLT.append("<xsl:element name=\"Fees\" namespace=\"http://www.opengis.net/wfs\"> ");
				serviceMetadataXSLT.append("<xsl:text>" + metadata.getFee() + "</xsl:text>");
				serviceMetadataXSLT.append("</xsl:element>");
			}
			//AccesConstraints
			if(!metadata.isInheritedaccessconstraint())
			{
				serviceMetadataXSLT.append("<xsl:element name=\"AccessConstraints\" namespace=\"http://www.opengis.net/wfs\"> ");
				serviceMetadataXSLT.append("<xsl:text>" + metadata.getAccessconstraint() + "</xsl:text>");
				serviceMetadataXSLT.append("</xsl:element>");
			}

			serviceMetadataXSLT.append("</xsl:copy>");
			serviceMetadataXSLT.append("</xsl:template>");

			serviceMetadataXSLT.append("</xsl:stylesheet>");

			return serviceMetadataXSLT;
		}
		catch (Exception ex )
		{
			ex.printStackTrace();
			logger.error(ex.getMessage());
			// If something goes wrong, an empty stylesheet is returned.
			StringBuffer sb = new StringBuffer();
			return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
		}
	}
	// ***************************************************************************************************************************************
	protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp) {
		try {

			XMLReader xr = XMLReaderFactory.createXMLReader(); // Contient la
			// requête
			// utilistateur
			// sous forme
			// d'un xml
			RequestHandler rh = new RequestHandler();
			xr.setContentHandler(rh);

			StringBuffer paramSB = new StringBuffer();

			String param = ""; // Contient la requête utilistateur sous forme
			// d'une string
			String input;
			BufferedReader in = new BufferedReader(new InputStreamReader(req.getInputStream()));
			while ((input = in.readLine()) != null) {
				paramSB.append(input);
			}
			param = paramSB.toString();
			logger.info("Request="+ param);

			xr.parse(new InputSource(new InputStreamReader(new ByteArrayInputStream(param.toString().getBytes()))));

			String version = rh.getVersion();
			requestedVersion = version;

			if (version != null)
				version = version.replaceAll("\\.", "");

			if (!version.equalsIgnoreCase("100")) {
				logger.error("Bad wfs version request: 1.0.0 only");
				owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED+"  Only 1.0.0 supported",OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "VERSION", HttpServletResponse.SC_BAD_REQUEST);
				return;
			}

			String currentOperation = rh.getOperation();
			
			//Generate OGC exception and send it to the client if current operation is not allowed
			if(!isOperationAllowed(currentOperation))
			{
				new WFSExceptionReport().sendExceptionReport(req,resp, OWSExceptionReport.TEXT_OPERATION_NOT_ALLOWED, OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_OK) ;
				return;
			}

			String paramOrig = param;

			// *****************************************************************************************************************************
			// Construction des FeaturesTypes et Attributs associés authorisés
			// -> modification de la requête utilisateur en conséquence
			// Passage de la requête utilisateur à plusieurs serveurs si défini
			// dans Policy <server>!!!
			// ATTENTION: Dans cette fonction iServer == à l'index du serveur
			// défini dans CONFIG
			// L'ordre des balises <server> dans POLICY doit être le même!!!
			int queryCount = 0;
			for (int iServer = 0; iServer < getPhysicalServiceList().size(); iServer++) {

				param = paramOrig;

				Vector<String> featureTypeListToKeep = new Vector<String>();
				List<String> attributeListToKeepPerFT = new Vector<String>(); // see
				List<Integer> attributeListToKeepNbPerFT = new Vector<Integer>(); // see
				List<String> attributeListToRemove = new Vector<String>();
				List<String> featureTypeListToRemove = new Vector<String>();

				// Construcation de la liste des FeatureType autorisé ou non à
				// partir de celles contenues dans la requête utilisateur
				// Si le featureType est autorisé faire de même avec les
				// attributs
				// Attention: dans le cas où la requête utiliseur GetFeature
				// comporte plusieurs Query (typeName), ->
				// featureTypeListToKeep.length()>1
				String filePath = "";
				if (currentOperation.equalsIgnoreCase("GetFeature") || currentOperation.equalsIgnoreCase("DescribeFeatureType")) {
					ExecutorService pool = Executors.newFixedThreadPool(5);
					DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
					DocumentBuilder builder = factory.newDocumentBuilder();
					TransformerFactory tFactory = TransformerFactory.newInstance();
					Transformer transformer = tFactory.newTransformer();
					XPathFactory xpathFactory = XPathFactory.newInstance();
					XPath xpath = xpathFactory.newXPath();

					Object[] fields = (Object[]) rh.getTypeName().toArray();
					int iii = 0;
					for (Object o : fields) {
						Object[] hFields = new Object[] { o };
						 Document dQuery = builder.parse(new InputSource(new StringReader(paramOrig)));
						 String xpathStr = "//*[local-name()='Query'][" + (iii + 1) + "]/@typeName";
						 XPathExpression typeNameExp = xpath.compile(xpathStr);
						 String typeName = (String) typeNameExp.evaluate(dQuery, XPathConstants.STRING);
						 XPathExpression expr = xpath.compile("//*[local-name()='Query'][@typeName!='" + typeName + "']");
						 NodeList nodes = (NodeList) expr.evaluate(dQuery, XPathConstants.NODESET);
						 for (int ni = 0; ni < nodes.getLength(); ni++) {
							 Node n = nodes.item(ni);
							 n.getParentNode().removeChild(n);
						 }
						 StringWriter sw = new StringWriter();
						 transformer.transform(new DOMSource(dQuery), new StreamResult(sw));
						 param = sw.toString();
						 	 int ii = 0; // Authorized FeatureType counter
							 attributeListToKeepNbPerFT.add(0);

							 for (int i = 0; i < hFields.length; i++) {
								 // Proxy est non compatible avec les multi Query
								 // dans les requêtes GetFeature!
								 if (currentOperation.equalsIgnoreCase("GetFeature") && i > 0) {
									 break;
								 }

								 String tmpFT = hFields[i].toString();
								 if (tmpFT != null) {
									
									 if (!tmpFT.equals("")) {
										 if (isFeatureTypeAllowed(tmpFT, getPhysicalServiceURLByIndex(iServer))) {
											 featureTypeListToKeep.add(tmpFT);
											 // Filtrage des attributs autorisés
											 if (currentOperation.equalsIgnoreCase("GetFeature")) {
												 Object[] fieldsAttribute = (Object[]) rh.getPropertyName().toArray(); // Si
												 // req PropertyNames non défini ->
												 // RequestHandler retourne un élément avec ""

												 // Au cas: req PropertyNames défini
												 // et [Policy Attributs sur ALL ou sur sous ensemble limité]
												 // ou
												 // req PropertyNames non défini et Policy Attributs sur sous ensemble limité
												 // ou
												 // req PropertyNames non défini et Policy Attributs sur All
												 for (int k = 0; k < fieldsAttribute.length; k++) //
												 {
													 String tmpFA = fieldsAttribute[k].toString();
													 if (!"".equals(tmpFA) && tmpFA != null) {
														 //String[] ss = tmpFA.split(":");
														 //tmpFA = ss[ss.length - 1];
													 }
													 //Comparaison avec le contenu de la Policy Si attributs dans req et aussi dans Policy-> 
													 //isAttributeAllowed retourne vrai pour l'attribut en cours: tmpFA = AttributFromReq 
													 //Si pas d'attributs dans req et Policy sur All -> isAttributeAllowed edit attributeListToKeepNbPerFT=0 
													 //et retourne vrai: pour l'attribut en cours: tmpFA = "" 
													 //Si attributs dans req mais pas dans Policy-> isAttributeAllowed retourne faux pour l'attribut en cours: 
													 //tmpFA = AttributFromReq Si pas d'attributs dans req -> 
													 //isAttributeAllowed ajoute ceux de la policy dans la globale var policyAttributeListToKeepPerFT 
													 //et retourne faux pour l'attribut en cours: tmpFA = ""
													 if (isAttributeAllowed(getPhysicalServiceURLByIndex(iServer), tmpFT, tmpFA)) {
														 if (!fieldsAttribute[k].toString().equals(""))  
															// au cas: req PropertyNames défini et aussi dans Policy
														 {
															 attributeListToKeepPerFT.add(tmpFA); // Without
															 // namespace
															 attributeListToKeepNbPerFT.set(ii, attributeListToKeepNbPerFT.get(ii) + 1);
														 } else // au cas: req PropertyNames défini et aussi dans Policy
														 {
															 attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
														 }
													 } else {
														 if (!fieldsAttribute[k].toString().equals("")) // au
															 // cas:
															 // req
															 // PropertyNames
															 // défini
															 // mais
															 // pas
															 // dans
															 // Policy
														 {
															 attributeListToRemove.add(tmpFA); // Without namespace
														 } else if (fieldsAttribute[k].toString().equals("")) // au
															 // cas:
															 // req
															 // PropertyNames
															 // non
															 // défini,
															 // ajout
															 // de
															 // ceux
															 // de
															 // la
															 // policy
														 {
															 attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
															 attributeListToKeepPerFT.addAll(policyAttributeListToKeepPerFT);
														 }
													 }
												 }
												 // Au cas: Aucun attribut
												 // demandé
												 // dans req n'est autorisé dans
												 // Policy
												 // -> isAttributeAllowed à
												 // ajouté
												 // ceux de la policy à la
												 // globale
												 // var
												 // policyAttributeListToKeepPerFT
												 if (attributeListToKeepNbPerFT.get(ii) == 0 && !fieldsAttribute[0].toString().equals("")) // ajout de ceux de la policy
												 {
													 isAttributeAllowed(getPhysicalServiceURLByIndex(iServer), tmpFT, "");
													 attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
													 attributeListToKeepPerFT.addAll(policyAttributeListToKeepPerFT);
												 }
											 }
											 ii += 1;
											 attributeListToKeepNbPerFT.add(0);
											 // Suppression des features type non autorisés et Suppression des Attributs respectifs
											 param = removeTypesFromPOSTUrl(featureTypeListToKeep, attributeListToKeepPerFT, attributeListToKeepNbPerFT, param,
													 iServer, currentOperation);

											 if (param != null) {
												 WFSGetFeatureRunnable r = new WFSGetFeatureRunnable("POST", getPhysicalServiceURLByIndex(iServer), param, this,
														 serversIndex, wfsFilePathList, iServer, queryCount);
												 pool.execute(r);
												 iii++;
												 queryCount++;
											 }
										 } else {
											 featureTypeListToRemove.add(tmpFT);
										 }
									 }
								 } 
							 }
						 
					}
					pool.shutdown();
					pool.awaitTermination(120L, TimeUnit.SECONDS);
				}
				boolean send = true;

				// Vérifier que la requête avec opération DescribeFeatureType comporte encore au moins 1 TypeName sinon voici la réponse à retourner
				if ("DescribeFeatureType".equalsIgnoreCase(currentOperation)) {
					if (featureTypeListToKeep.size() == 0) {
						String s = "<?xml version='1.0' encoding='utf-8' ?>"
								+ "<ogcwfs:FeatureCollection xmlns:ogcwfs=\"http://www.opengis.net/wfs\"   xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" >"
								+ "<gml:boundedBy>" + "<gml:null>unavailable</gml:null>" + "</gml:boundedBy>" + "</ogcwfs:FeatureCollection>";
						send = false;
						ByteArrayOutputStream out = new ByteArrayOutputStream();
						out.write(s.getBytes());
						out.flush();
						out.close();
						sendHttpServletResponse(req,resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
						return;
					}
				}
			
				// Ajout du remote filter (BBOX) à la requête utilisateur si opération GetFeature
				if ("GetFeature".equalsIgnoreCase(currentOperation)) {
					// Si la requête modifiée ne comporte plus de TypeName voici la réponse à retourner
					if (featureTypeListToKeep.size() == 0) {
						String s = "<?xml version='1.0' encoding='utf-8' ?>"
								+ "<ogcwfs:FeatureCollection xmlns:ogcwfs=\"http://www.opengis.net/wfs\"   xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" >"
								+ "<gml:boundedBy>" + "<gml:null>unavailable</gml:null>" + "</gml:boundedBy>" + "</ogcwfs:FeatureCollection>";
						send = false;

						ByteArrayOutputStream out = new ByteArrayOutputStream();
						out.write(s.getBytes());
						out.flush();
						out.close();
						sendHttpServletResponse(req,resp,out, "text/xml; charset=utf-8", HttpServletResponse.SC_OK);
						return;
					}

					// Ajouter le "remoteFilter" à la requête posée
					// ********************************************
					// Attention corr: le namespace est ajouté explicitement
					// Concernant les filtres spatiaux de la policy:
					// RemoteFilter s'applique à la requête, LocalFilter
					// s'applique à la réponse
					// Il existe les cas possibles suivants:
					// Policy RemoteFilter is Not Set -> Policy Local Filter is
					// Not Set -> L'attribut géom n'est pas obligatoire
					// Policy RemoteFilter is Set -> Policy Local Filter is Not
					// Set -> L'attribut géom n'est pas obligatoire
					// Policy RemoteFilter is Set -> Policy Local Filter is Set
					// -> L'attribut géom est OBLIGATOIRE
					// ->Lecture de son nom dans RemoteFilter, vérification de
					// sa présence dans attributeListToKeepPerFT
					// -> Si présent: pas de modification de la requête
					// -> Si absent: modification de la requête par ajout de
					// <PropertyName>, puis suppression par filtrage du
					// résultat!
					// 

					// Recherche du remoteFilter dans la Policy et Ajout du
					// FeatureType autorisé
					String userFilter = null;

					if (featureTypeListToKeep.size() > 0) {
						userFilter = getFeatureTypeRemoteFilter(getPhysicalServiceURLByIndex(iServer), featureTypeListToKeep.get(0));
						featureTypePathList = featureTypeListToKeep;
					}

					// Modification de la requête user avec le remoteFilter
					// Debug tb 21.05.2009
					// In case remoteFilter is activ but not Set
					if (send && userFilter != null && !userFilter.equals(""))
						// Fin de Debug
					{
						InputStream isRequestFilter = new ByteArrayInputStream(param.getBytes());
						InputStream isUserFilter = new ByteArrayInputStream(userFilter.getBytes());
						DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
						db.setNamespaceAware(true);
						Document documentRequestFilter = db.newDocumentBuilder().parse(isRequestFilter);
						Document documentUserFilter = db.newDocumentBuilder().parse(isUserFilter);

						DOMImplementationLS implLS = null;
						if (documentRequestFilter.getImplementation().hasFeature("LS", "3.0")) {
							implLS = (DOMImplementationLS) documentRequestFilter.getImplementation();
						} else {
							DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
							implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
						}
						NodeList nlRequestFilter = documentRequestFilter.getElementsByTagName("Filter");
						if (nlRequestFilter.getLength() == 0)
							nlRequestFilter = documentRequestFilter.getElementsByTagNameNS("http://www.opengis.net/ogc", "Filter");
						NodeList nlUserFilter = documentUserFilter.getElementsByTagName("Filter");
						if (nlUserFilter.getLength() == 0)
							nlUserFilter = documentUserFilter.getElementsByTagNameNS("http://www.opengis.net/ogc", "Filter");

						// A filter is existing
						// Could cause some performance issue, when query some
						// WFs based onOracle.
						if (nlRequestFilter.getLength() > 0) {
							Node nodeRequestFilter = nlRequestFilter.item(0);
							Node nodeUserFilter = nlUserFilter.item(0);
							Node andNode = documentRequestFilter.createElement("And");

							for (int i = 0; i < nodeRequestFilter.getChildNodes().getLength(); i++) {
								andNode.appendChild(nodeRequestFilter.getChildNodes().item(i).cloneNode(true));
							}

							for (int i = 0; i < nodeUserFilter.getChildNodes().getLength(); i++) {
								andNode.appendChild(documentRequestFilter.adoptNode(nodeUserFilter.getChildNodes().item(i).cloneNode(true)));
							}

							while (nodeRequestFilter.hasChildNodes()) {
								nodeRequestFilter.removeChild(nodeRequestFilter.getChildNodes().item(0));
							}

							nodeRequestFilter.appendChild(andNode);
						}

						OutputStream fluxSortie = new ByteArrayOutputStream();

						LSSerializer serialiseur = implLS.createLSSerializer();
						LSOutput sortie = implLS.createLSOutput();
						sortie.setEncoding("UTF-8");
						sortie.setByteStream(fluxSortie);
						serialiseur.write(documentRequestFilter, sortie);
						fluxSortie.flush();
						fluxSortie.close();
						param = fluxSortie.toString();
					}
					// Fin d'ajout du "remoteFilter"
				}

				//Quick fix : not support multi-server request!
				if("Transaction".equalsIgnoreCase(currentOperation)){
					logger.debug("Transaction Operation.");
					filePath = sendData("POST", getPhysicalServiceURLByIndex(iServer), param);
					logger.debug("Transaction response :"+filePath);
					wfsFilePathList.put(iServer, filePath);
					logger.debug("End transaction operation.");
				}

				// Exécution de la requête utilisateur modifiée au serveur en
				// cours ->s'il y a plusieurs serveurs, alors cet appel se fait
				// plus d'une fois!!!
				if (send) {
					filePath = sendData("POST", getPhysicalServiceURLByIndex(iServer), param);
					wfsFilePathList.put(iServer, filePath);
				}
			}
			// Fin de la phase de reconstruction de la requête: wfsFilePathList
			// contient les réponses de chaque serveur (une par serveur)
			// *****************************************************************************************************************************

			// *****************************************************************************************************************************
			// Lancement du post traitement
			if (wfsFilePathList.size() > 0) {
				version = version.replaceAll("\\.", "");
				if (version.equalsIgnoreCase("100")||version.equalsIgnoreCase("000")) {
					transform(version, currentOperation, req, resp);
				} else {
					logger.error( "Bad wfs version request: 1.0.0 only");
					owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED+"  Only 1.0.0 supported",OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "VERSION", HttpServletResponse.SC_OK);
					return ;
				}
			} else {
				logger.error("This request has no authorized results!");
			}
			// *****************************************************************************************************************************
			// Fin du post traitement

		} catch (AvailabilityPeriodException e) {
			logger.error( e.getMessage());
			try {
				owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED,OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			logger.error(e.toString());
			try {
				owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
		}
	}

	// ***************************************************************************************************************************************
	@SuppressWarnings("unchecked")
	protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp) {

		try {
			String currentOperation = null;
			String version = "000";
			Enumeration<String> parameterNames = req.getParameterNames();
			String requestParamUrl = "";
			String paramUrl = "";
			String filter = null;
			String typeName = "";
			String propertyName = "";
	
			// Récupération des valeurs des paramètres de la requête utilisateur
			while (parameterNames.hasMoreElements()) {
				String key = parameterNames.nextElement();
				String value = req.getParameter(key);

				if (!key.equalsIgnoreCase("FILTER") && !key.equalsIgnoreCase("version")) {
					// Le FILTER sera ajouté par la suite s'il existe dans la
					// requête utilisateur
					paramUrl = paramUrl + key + "=" + value + "&";
				}
				
				if (key.equalsIgnoreCase("TYPENAME")) {
					typeName = value;
				}
				else if (key.equalsIgnoreCase("PROPERTYNAME")) {
					propertyName = value;
				}
				else if (key.equalsIgnoreCase("Request")) {
					// Gets the requested Operation
					if (value.equalsIgnoreCase("capabilities")) {
						currentOperation = "GetCapabilities";
					} else {
						currentOperation = value;
					}
				} else if (key.equalsIgnoreCase("version")) {
					// Gets the requested version
					requestedVersion= value;
					version = value;
					
				} else if (key.equalsIgnoreCase("service")) {
					// Gets the requested service
//					service = value;
				} else if (key.equalsIgnoreCase("FILTER")) {
					// Gets the requested Filter
					filter = value;
				}
			}
			
			if(!currentOperation.equalsIgnoreCase("GetCapabilities")){
				paramUrl = paramUrl + "version=" + version + "&";
			}
			
			//Set the version to replace the negotiation version process (only the version 1.0.0 is supported for now)
			if(currentOperation.equalsIgnoreCase("GetCapabilities"))
				version = "1.0.0";
			
			requestParamUrl = paramUrl;

			//If version is not 1.0.0, return an ogc exception
			if (!version.replaceAll("\\.", "").equalsIgnoreCase("100") && !currentOperation.equalsIgnoreCase("GetCapabilities")) {
				logger.error( "Bad wfs version request: 1.0.0 only");
				owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED+" - Only 1.0.0 supported.",OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "VERSION", HttpServletResponse.SC_OK);
				return;
			}
			
			//Generate OGC exception and send it to the client if current operation is not allowed
			if(!isOperationAllowed(currentOperation))
			{
				owsExceptionReport.sendExceptionReport(request, response, OWSExceptionReport.TEXT_OPERATION_NOT_ALLOWED, OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_OK) ;
				return;
			}

			// *****************************************************************************************************************************
			// Construction des FeaturesTypes et Attributs associés authorisés
			// -> modification de la requête utilisateur en conséquence
			// Passage de la requête utilisateur à plusieurs serveurs si défini
			// dans Policy <server>!!!
			// ATTENTION: Dans cette fonction iServer == à l'index du serveur
			// défini dans CONFIG
			for (int iServer = 0; iServer < getPhysicalServiceList().size(); iServer++) {
				// Reset the original request url
				paramUrl = requestParamUrl;

				List<String> featureTypeListToKeep = new Vector<String>();
				// Debug tb 24.06.2009
				List<String> attributeListToKeepPerFT = new Vector<String>(); // see
				// the parent ProxyServlet class var
				List<Integer> attributeListToKeepNbPerFT = new Vector<Integer>(); // see
				// the parent ProxyServlet class var
				List<String> attributeListToRemove = new Vector<String>();
				// Fin de debug
				List<String> featureTypeListToRemove = new Vector<String>();
				if (currentOperation != null) {
					if (currentOperation.equalsIgnoreCase("GetFeature") || currentOperation.equalsIgnoreCase("DescribeFeatureType")) {
						String[] fields = typeName.split(",");

						for (int k = 0 ; k < fields.length ; k++){
							if(!isFeatureTypeAllowed(fields[k])){
								owsExceptionReport.sendExceptionReport(request, response, OWSExceptionReport.TEXT_INVALID_PARAMETER_VALUE+fields[k], OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "TYPENAME", HttpServletResponse.SC_OK) ;
								return;
							}
						}

						// Begin
						// removeTypesFromGetUrl********************************************************************************
							// Debug tb 25.06.2009
							int ii = 0; // Authorized FeatureType counter
							attributeListToKeepNbPerFT.add(0);
							// Fin de Debug
							// Passe en revue les typesName(=FeatureTypes) de la requete
							for (int i = 0; i < fields.length; i++) {
								// Proxy est non compatible avec les multi Query dans les requêtes GetFeature !
								if ((currentOperation.equalsIgnoreCase("GetFeature") ) && i > 0) {
									break;
								}

								String tmpFT = fields[i];
								if (tmpFT != null) {
									if (!tmpFT.equals("")) {
										if (isFeatureTypeAllowed(tmpFT, getPhysicalServiceURLByIndex(iServer))) {
											featureTypeListToKeep.add(tmpFT);
											// Debug tb 25.06.2009
											// Filtrage des attributs autorisés
											if (currentOperation.equalsIgnoreCase("GetFeature")) {
												String[] fieldsAttribute = propertyName.split(","); // Si
												// req PropertyNames non défini -> Split retourne un element avec ""
												// Au cas: req PropertyNames défini et [Policy Attributs sur ALL ou sur sous ensemble limité]
												// ou
												// req PropertyNames non défini et Policy Attributs sur sous ensemble limité
												// ou
												// req PropertyNames non défini et Policy Attributs sur All
												for (int k = 0; k < fieldsAttribute.length; k++) //
												{
													String tmpFA = fieldsAttribute[k].toString();
													if (tmpFA != null) {
													}
													// Comparaison avec le contenu de la Policy
													// Si attributs dans req et aussi dans Policy-> isAttributeAllowed retourne vrai pour l'attribut en cours:
													// tmpFA = AttributFromReq
													// Si pas d'attributs dans req et Policy sur All -> isAttributeAllowed edit attributeListToKeepNbPerFT=0 et retourne vrai: pour l'attribut en cours:
													// tmpFA = ""
													// Si attributs dans req mais pas dans Policy-> isAttributeAllowed retourne faux pour l'attribut en cours:
													// tmpFA = AttributFromReq
													// Si pas d'attributs dans req -> isAttributeAllowed ajoute ceux de la policy dans la globale var
													// policyAttributeListToKeepPerFT
													// et retourne faux pour l'attribut en cours:
													// tmpFA = ""
													if (isAttributeAllowed(getPhysicalServiceURLByIndex(iServer), tmpFT, tmpFA)) {
														if (!fieldsAttribute[k].toString().equals("")) // au
															// cas: req PropertyNames défini et aussi dans Policy
														{
															attributeListToKeepPerFT.add(tmpFA); // Without
															// namespace
															attributeListToKeepNbPerFT.set(ii, attributeListToKeepNbPerFT.get(ii) + 1);
														} else // au cas: req PropertyNames non défini et Policy sur All (policyAttributeListNb == 0)
														{
															attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
														}
													} else {
														if (!fieldsAttribute[k].toString().equals("")) // au
															// cas: req PropertyNames défini mais pas dans Policy
														{
															attributeListToRemove.add(tmpFA); // Without namespace
														} else if (fieldsAttribute[k].toString().equals("")) // au cas:
															// req PropertyNames non défini, ajout de ceux de la policy
														{
															// policyAttributeListNb est éditer par isAttributeAllowed dans ce cas
															attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
															attributeListToKeepPerFT.addAll(policyAttributeListToKeepPerFT);
														}
													}
												}
												// Au cas: Aucun attribut demandé dans req n'est autorisé dans Policy
												// -> isAttributeAllowed à ajouté ceux de la policy à la globale var policyAttributeListToKeepPerFT
												if (attributeListToKeepNbPerFT.get(ii) == 0 && !fieldsAttribute[0].toString().equals("")) // ajout de ceux de la policy
												{
													isAttributeAllowed(getPhysicalServiceURLByIndex(iServer), tmpFT, "");
													attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
													attributeListToKeepPerFT.addAll(policyAttributeListToKeepPerFT);
												}
											}
											// Fin de Debug
										} else {
											featureTypeListToRemove.add(tmpFT);
										}
									}
									// Debug tb 25.06.2009
									ii += 1;
									attributeListToKeepNbPerFT.add(0);
									// Fin de Debug
								} 
							}
							if (!currentOperation.equalsIgnoreCase("DescribeFeatureType")) {
								// Recherche du remoteFilter dans la Policy et
								// Modification de la requête user en
								// conséquence **************

								// S'il n'y a pas de filtre dans la requête utilisateur -> ajouter celui de la policy si défini
								if (filter == null) {
									String userFilter = null;
									if (featureTypeListToKeep.size() > 0) {
										userFilter = getFeatureTypeRemoteFilter(getPhysicalServiceURLByIndex(iServer), featureTypeListToKeep.get(0));
										featureTypePathList.add(featureTypeListToKeep.get(0));
									}
									// Debug tb 21.05.2009
									// In case remoteFilter is activ but not Set
									if (userFilter != null && !userFilter.equals(""))
										// Fin de Debug
									{
										if (!paramUrl.endsWith("&")) {
											paramUrl = paramUrl + "&";
										}
										paramUrl = paramUrl + "FILTER=" + java.net.URLEncoder.encode(userFilter, "UTF-8");
									}
								}
								// S'il y a un filtre dans la requête utilisateur -> ajouter ce dernier et ajouter celui de la policy si définie
								else {
									String userFilter = null;
									if (featureTypeListToKeep.size() > 0) {
										userFilter = getFeatureTypeRemoteFilter(getPhysicalServiceURLByIndex(iServer), featureTypeListToKeep.get(0));
										// Debug tb 24.06.2009
										featureTypePathList.add(featureTypeListToKeep.get(0));
										// Fin de Debug
									}

									// Debug tb 21.05.2009
									// True if remoteFilter is activ but not Set
									if (userFilter != null && userFilter.equals("")) {
										if (!paramUrl.endsWith("&")) {
											paramUrl = paramUrl + "&";
										}
										paramUrl = paramUrl + "FILTER=" + java.net.URLEncoder.encode(filter, "UTF-8");
									}
									// True if remoteFilter is activ and Set
									else if (userFilter != null && !userFilter.equals(""))
										// Fin de Debug
									{
										if (!paramUrl.endsWith("&")) {
											paramUrl = paramUrl + "&";
										}

										InputStream isRequestFilter = new ByteArrayInputStream(filter.getBytes());
										InputStream isUserFilter = new ByteArrayInputStream(userFilter.getBytes());
										DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
										db.setNamespaceAware(true);
										Document documentRequestFilter = db.newDocumentBuilder().parse(isRequestFilter);
										Document documentUserFilter = db.newDocumentBuilder().parse(isUserFilter);

										DOMImplementationLS implLS = null;
										if (documentRequestFilter.getImplementation().hasFeature("LS", "3.0")) {
											implLS = (DOMImplementationLS) documentRequestFilter.getImplementation();
										} else {
											DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
											implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
										}
										NodeList nlRequestFilter = documentRequestFilter.getElementsByTagName("Filter");
										if (nlRequestFilter.getLength() == 0)
											nlRequestFilter = documentRequestFilter.getElementsByTagNameNS("http://www.opengis.net/ogc", "Filter");
										NodeList nlUserFilter = documentUserFilter.getElementsByTagName("Filter");
										if (nlUserFilter.getLength() == 0)
											nlUserFilter = documentUserFilter.getElementsByTagNameNS("http://www.opengis.net/ogc", "Filter");

										// A filter is existing Cause some performance issue. Disable it
										Node nodeRequestFilter = nlRequestFilter.item(0);
										Node nodeUserFilter = nlUserFilter.item(0);
										Node andNode = documentRequestFilter.createElement("And");

										for (int i = 0; i < nodeRequestFilter.getChildNodes().getLength(); i++) {
											andNode.appendChild(nodeRequestFilter.getChildNodes().item(i).cloneNode(true));
										}

										for (int i = 0; i < nodeUserFilter.getChildNodes().getLength(); i++) {
											andNode.appendChild(documentRequestFilter.adoptNode(nodeUserFilter.getChildNodes().item(i).cloneNode(true)));
										}

										while (nodeRequestFilter.hasChildNodes()) {
											nodeRequestFilter.removeChild(nodeRequestFilter.getChildNodes().item(0));
										}

										nodeRequestFilter.appendChild(andNode);

										OutputStream fluxSortie = new ByteArrayOutputStream();

										LSSerializer serialiseur = implLS.createLSSerializer();
										LSOutput sortie = implLS.createLSOutput();
										sortie.setEncoding("UTF-8");
										sortie.setByteStream(fluxSortie);
										serialiseur.write(documentRequestFilter, sortie);
										fluxSortie.flush();
										fluxSortie.close();

										paramUrl = paramUrl + "FILTER=" + java.net.URLEncoder.encode(fluxSortie.toString(), "UTF-8");
									}
								}
								// Fin de modification de la requête user avec RemoteFilter
								// ***************************************
							}
							// Suppression des features type non autorisés et Suppression des Attributs respectifs
							// Debug tb 25.06.2009
							paramUrl = removeTypesFromGetUrl(featureTypeListToKeep, attributeListToKeepPerFT, attributeListToKeepNbPerFT, paramUrl, iServer,
									currentOperation);
							// Fin de Debug
						
						// End removeTypesFromGetUrl********************************************************************************

						boolean send = true;
						String filePath = "";
						// Vérifier que la requête avec opération DescribeFeatureType comporte encore au moins 1 TypeName sinon la réponse doit être une exception OGC
						// Faux : dans le cas d'une aggrégation de serveur, le featuretype demandé peux provenir d'un autre serveur, le fait que la liste featureTypeListToKeep soit
						// vide pour un serveur ne veut pas dire quel a requête entière est non valide.
						if ("DescribeFeatureType".equalsIgnoreCase(currentOperation)) {
//							if (featureTypeListToKeep.size() == 0) {
//								owsExceptionReport.sendExceptionReport(request, response, OWSExceptionReport.TEXT_INVALID_PARAMETER_VALUE, OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "TYPENAME", HttpServletResponse.SC_OK) ;
//								return;
//							}
						}

						if ("GetFeature".equalsIgnoreCase(currentOperation)) {
							// Si la requête modifiée ne comporte plus de TypeName voici la réponse à retourner
							if (featureTypeListToKeep.size() == 0) {
								String s = "<?xml version='1.0' encoding='utf-8' ?>"
										+ "<ogcwfs:FeatureCollection xmlns:ogcwfs=\"http://www.opengis.net/wfs\"   xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" >"
										+ "<gml:boundedBy>" + "<gml:null>unavailable</gml:null>" + "</gml:boundedBy>" + "</ogcwfs:FeatureCollection>";
								ByteArrayOutputStream out = new ByteArrayOutputStream();
								out.write(s.getBytes());
								out.flush();
								out.close();
								sendHttpServletResponse(req,resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
								return;
							}
						}
						// Exécution de la requête utilisateur modifiée au serveur en cours ->s'il y a plusieurs serveurs, alors cet appel se fait plus d'une fois!!!
						if (send)
						{
							if ("DescribeFeatureType".equalsIgnoreCase(currentOperation)) {
								if (featureTypeListToKeep.size() != 0) {
									filePath = sendData("GET", getPhysicalServiceURLByIndex(iServer), paramUrl);
									serversIndex.add(iServer);
									wfsFilePathList.put(iServer, filePath);
								}
							}else{
								filePath = sendData("GET", getPhysicalServiceURLByIndex(iServer), paramUrl);
								serversIndex.add(iServer);
								wfsFilePathList.put(iServer, filePath);
							}
							
						}
					}
					else if (currentOperation.equalsIgnoreCase("GetCapabilities"))
					{
						//Add to the request the only version supported
						paramUrl+= "version=1.0.0";
						String filePath = sendData("GET", getPhysicalServiceURLByIndex(iServer), paramUrl);
						serversIndex.add(iServer);
						wfsFilePathList.put(iServer, filePath);
					}
					// Si l'opération courante est différente de DescribeFeature ou GetFeature
					else {
						String filePath = sendData("GET", getPhysicalServiceURLByIndex(iServer), paramUrl);
						serversIndex.add(iServer);
						wfsFilePathList.put(iServer, filePath);
					}
				}
			}
			// Fin de la phase de reconstruction de la requête: wfsFilePathList
			// contient les réponses de chaque serveur (une par serveur)
			// *****************************************************************************************************************************

			// *****************************************************************************************************************************
			// Lancement du post traitement
			if (wfsFilePathList.size() > 0) {
				version = version.replaceAll("\\.", "");
				if (version.equalsIgnoreCase("100")||version.equalsIgnoreCase("000")) {
					transform(version, currentOperation, req, resp);
				} else {
					//Moved to the begining of the requestPreTreatmentGET method  
					logger.error("Bad wfs version request: 1.0.0 only");
					owsExceptionReport.sendExceptionReport(request, response, OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED, OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "VERSION", HttpServletResponse.SC_OK) ;
				}
			} else {
				logger.error( "This request has no authorized results!");
			}
			// *****************************************************************************************************************************
			// Fin du post traitement
		} catch (AvailabilityPeriodException e) {
			logger.error( e.getMessage());
			try {
				owsExceptionReport.sendExceptionReport(request, response, OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED, OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_OK) ;
			} catch (IOException e1) {
				logger.error( OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			logger.error( e.toString());
			try {
				owsExceptionReport.sendExceptionReport(request, response, OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY, OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK) ;
			} catch (IOException e1) {
				logger.error( OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
		}
	}

	// ***************************************************************************************************************************************
	// Retourne l'attribut géométrique de localFilter
	private String getLocalFilterGeomAttribut(int iServer, String featureTypeToKeep) {
		String geomAttributName = "";
		try {
			// Recherche du LocalFilter dans la Policy
			String localFilter = null;

			localFilter = getFeatureTypeLocalFilter(getPhysicalServiceURLByIndex(iServer), featureTypeToKeep);

			// Récupération de l'attribut geom du localFilter
			// In case localFilter is activ but not Set
			if (localFilter != null && !localFilter.equals("")) {
				InputStream isLocalFilter = new ByteArrayInputStream(localFilter.getBytes());
				DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
				db.setNamespaceAware(true);
				Document documentLocalFilter = db.newDocumentBuilder().parse(isLocalFilter);

				NodeList nlLocalFilter = documentLocalFilter.getElementsByTagName("PropertyName");
				if (nlLocalFilter.getLength() == 0)
					nlLocalFilter = documentLocalFilter.getElementsByTagNameNS("http://www.opengis.net/ogc", "PropertyName");

				// A filter is existing
				// Could cause some performance issue, when query some WFs based
				// onOracle.
				if (nlLocalFilter.getLength() > 0) {
					String tmpFTA = nlLocalFilter.item(0).getTextContent();
					//String[] s = tmpFTA.split(":");
					//tmpFTA = s[s.length - 1];
					geomAttributName = tmpFTA;
				}
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
		return geomAttributName;
	}

	// Fin de Debug

	// ***************************************************************************************************************************************
	// private String removeTypesFromPOSTUrl(List<String>
	// featureTypeListToKeep,String paramUrl,int iServer,String operation)
	private String removeTypesFromPOSTUrl(List<String> featureTypeListToKeep, List<String> attributeListToKeepPerFT, List<Integer> attributeListToKeepNbPerFT,
			String paramUrl, int iServer, String operation) {
		try {
			List<Node> nodeListToRemove = new Vector<Node>();
			List<Node> nodeAttributeListToRemove = new Vector<Node>();

			// lecture du contenu de la requête utilisateur (paramUrl) dans un
			// document
			InputStream is = new ByteArrayInputStream(paramUrl.getBytes());
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(true);
			Document documentMaster = db.newDocumentBuilder().parse(is);

			DOMImplementationLS implLS = null;
			if (documentMaster.getImplementation().hasFeature("LS", "3.0")) {
				implLS = (DOMImplementationLS) documentMaster.getImplementation();
			} else {
				DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
				implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
			}

			// Dans le cas d'un requête portant sur une opération GetFeature
			if ("GetFeature".equalsIgnoreCase(operation)) {
				NodeList nl = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs", "Query");

				// Debug tb 11.06.2009
				// Sauvegarde du prefix et namespace pour le iServer
				String policyServerPrefix = getServerPrefix(getPhysicalServiceURLByIndex(iServer));
				policyServersPrefix.add(policyServerPrefix);
				String policyServerNamespace = getServerNamespace(getPhysicalServiceURLByIndex(iServer));
				policyServersNamespace.add(policyServerNamespace);
				// Fin de Debug

				// A) Recherche des FeaturesTypes autorisés
				// for (int i = 0; i < nl.getLength(); i++) {
				boolean isInList = false;
				for (int j = 0; j < featureTypeListToKeep.size(); j++) {
					// Recherche le nom de l'authorized featureType courant
					String tmpFT = nl.item(0).getAttributes().getNamedItem("typeName").getTextContent();
					if (tmpFT != null) {
						//String[] s = tmpFT.split(":");
						//tmpFT = s[s.length - 1];
					}
					
					if (tmpFT.equals(featureTypeListToKeep.get(j))) {
						// Debug tb 05.06.2009
						// Edit l'attribut typeName de <wfs:Query typeName="tmpFT"> avec tmpFT nl.item(i).getAttributes().getNamedItem("typeName").setTextContent(tmpFT);
						// Non nécessaire de réécrire ce qui est déjà présent
						NodeList antltemp = documentMaster.getElementsByTagNameNS("http://www.opengis.net/ogc", "PropertyName");
						// Debug tb 29.09.2009
						// Pour établir la liste des noeuds PropertyName directement sous la balise <Query> tiré de la requête user
						for (int k = 0; k < antltemp.getLength(); k++) {
							Node node = (Node) antltemp.item(k);
							// renommer les noeuds "PropertyName" autre que ceux directement et uniquement sous <Query>:
							// ex ceux de <Filter>
							if (!"Query".equals(node.getParentNode().getLocalName())) {
								String prefix = (node.getPrefix() != null && !"".equals(node.getPrefix())) ? node.getPrefix() + ":" : "";
								documentMaster.renameNode(node, node.getNamespaceURI(), prefix + "MyFilterPropertyName");
							}
						}
						NodeList atnl = documentMaster.getElementsByTagNameNS("http://www.opengis.net/ogc", "PropertyName");

						// Au cas: PropertyNames dans req utilisateur, et restriction dans Policy Attributes
						// A') Recherche des attibuts autorisés pour le FeatureType courant
						if (atnl.getLength() != 0) {
							for (int k = 0; k < atnl.getLength(); k++) {
								boolean isAtInList = false;
								for (int l = 0; l < attributeListToKeepNbPerFT.get(j); l++) {
									String tmpFTA = atnl.item(k).getTextContent();
									if (tmpFTA != null) {
										//String[] s = tmpFTA.split(":");
										//tmpFTA = s[s.length - 1];
									}
			
									if (tmpFTA.equals(attributeListToKeepPerFT.get(j * attributeListToKeepNbPerFT.get(j) + l))) {
										// atnl.item(k).setTextContent(tmpFTA);
										// // Non nécessaire de réécrire ce qui est déjà présent
										isAtInList = true;
									}
								}
								if (!isAtInList) {
									// Attribut présent dans la requête, mais non-autorisés
									nodeAttributeListToRemove.add(atnl.item(k));
								}
							}

							// B') Supressions des Attribut non-autorisés présents dans la requête
							for (int m = 0; m < nodeAttributeListToRemove.size(); m++) {
								nodeAttributeListToRemove.get(m).getParentNode().removeChild(nodeAttributeListToRemove.get(m));
							}
						}

						// Traitement des cas de l'attribut géométrique de localFilter
						String geomAttribut = getLocalFilterGeomAttribut(iServer, featureTypeListToKeep.get(j));
						Boolean hasGeomAttribut = false;

						// Au cas: pas de PropertyName dans req utilisateur ou tous retirés par le if() précédent, mais
						// restriction dans Policy Attributes
						// A') Ajoute des attibuts autorisés de Policy pour le FeatureType courant
						// ou ne fait rien dans le cas ou Attributs autorisée de Policy sur All
						// (attributeListToKeepNbPerFT.get(j)=0)
						if (atnl.getLength() == 0) {
							for (int k = 0; k < attributeListToKeepNbPerFT.get(j); k++) {
								// Debug tb 16.10.2009
								// Si le namespace ogc n'est pas déclaré dans la balise Query de la requete user
								// GetFeature Element docElem = documentMaster.createElement("ogc:PropertyName");
								Element docElem = documentMaster.createElement("PropertyName");
								// Fin de Debug
								docElem.setTextContent(policyServerPrefix + ":" + attributeListToKeepPerFT.get(j * attributeListToKeepNbPerFT.get(j) + k));
								nl.item(0).insertBefore(docElem, nl.item(0).getFirstChild());
								if (geomAttribut.equalsIgnoreCase(attributeListToKeepPerFT.get(j * attributeListToKeepNbPerFT.get(j) + k))) {
									hasGeomAttribut = true;
								}
							}
						}

						// Au cas: localFilter is Set et geom Attribut absent de attributeListToKeepPerFT
						// A') Ajoute l'attribut geom pour le FeatureType courant,
						// -> ce dernier devra ensuite être retiré par XSLT
						// au moment de transform()!!!
						// ou ne s'applique pas dans le cas ou Attributs autorisée de Policy sur All
						// (attributeListToKeepNbPerFT.get(j)=0)
						if (!geomAttribut.equals("") && attributeListToKeepNbPerFT.get(j) != 0) {
							if (atnl.getLength() != 0) {
								for (int k = 0; k < attributeListToKeepNbPerFT.get(j); k++) {
									String tmpFTA = atnl.item(k).getTextContent();
									if (tmpFTA != null) {
										//String[] s = tmpFTA.split(":");
										//tmpFTA = s[s.length - 1];
									}
		
									if (tmpFTA.equals(geomAttribut)) {
										hasGeomAttribut = true;
									}
								}
							}
							if (!hasGeomAttribut) {
								// Contient tout les liens Server-Prefix-Namespace-AuthorizedFeature-GeomAttribute
								WFSProxyGeomAttributes geomAttributesObj = new WFSProxyGeomAttributes(iServer, policyServerPrefix, policyServerNamespace);
								geomAttributesObj.setFeatureTypeName(featureTypeListToKeep.get(j));
								geomAttributesObj.setGeomAttributName(geomAttribut);
								WFSProxyGeomAttributesList.add(geomAttributesObj);

								// Ajoute l'attribut géométrique à la requête utilisateur
								// Debug tb 16.10.2009
								// Si le namespace ogc n'est pas déclaré dans la balise Query de la requete user GetFeature
								// Element docElem = documentMaster.createElement("ogc:PropertyName");
								Element docElem = documentMaster.createElement("PropertyName");
								// Fin de Debug
								docElem.setTextContent(policyServerPrefix + ":" + geomAttribut);
								nl.item(0).insertBefore(docElem, nl.item(0).getFirstChild());
							}
						}
						// Fin de Debug
						// Debug tb 29.09.2009
						// Pour rétablir la liste des noeuds PropertyName de la requête user renommer les noeuds "PropertyName" autre que ceux
						// directement et uniquement sous <Query>: ex ceux de <Filter>
						antltemp = documentMaster.getElementsByTagNameNS("http://www.opengis.net/ogc", "MyFilterPropertyName");
						for (int k = 0; k < antltemp.getLength(); k++) {
							Node node = (Node) antltemp.item(k);
							if (!"Query".equals(node.getParentNode().getLocalName())) {
								String prefix = (node.getPrefix() != null && !"".equals(node.getPrefix())) ? node.getPrefix() + ":" : "";
								documentMaster.renameNode(node, node.getNamespaceURI(), prefix + "PropertyName");
							}
						}
						// Fin de Debug
						isInList = true;
					}
				}
				if (!isInList) {
					// FeatureType présent dans la requête, mais non-autorisés
					nodeListToRemove.add(nl.item(0));
				}
				// }

				// B) Supressions des FeaturesTypes non-autorisés présents dans la requête
				for (int i = 0; i < nodeListToRemove.size(); i++) {
					nodeListToRemove.get(i).getParentNode().removeChild(nodeListToRemove.get(i));
				}
			}

			// Dans le cas d'un requête portant sur une opération DescribeFeatureType
			if ("DescribeFeatureType".equalsIgnoreCase(operation)) {
				NodeList nl = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs", "TypeName");
				for (int i = 0; i < nl.getLength(); i++) {
					boolean isInList = false;
					for (int j = 0; j < featureTypeListToKeep.size(); j++) {
						String tmpFT = nl.item(i).getTextContent();
						if (tmpFT != null) {
							//String[] s = tmpFT.split(":");
							//tmpFT = s[s.length - 1];
						}
						if (tmpFT.equals(featureTypeListToKeep.get(j))) {
							String policyServerPrefix = getServerPrefix(getPhysicalServiceURLByIndex(iServer));
							policyServersPrefix.add(policyServerPrefix);
							//nl.item(i).setTextContent(policyServerPrefix + ":" + tmpFT);
							nl.item(i).setTextContent(tmpFT);
							isInList = true;
						}
					}
					if (!isInList) {
						nodeListToRemove.add(nl.item(i));
					}
				}
				for (int i = 0; i < nodeListToRemove.size(); i++) {
					nodeListToRemove.get(i).getParentNode().removeChild(nodeListToRemove.get(i));
				}
			}

			// Ecriture de la requête utilisateur suite aux modifications
			OutputStream fluxSortie = new ByteArrayOutputStream();
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			sortie.setByteStream(fluxSortie);
			serialiseur.write(documentMaster, sortie);
			fluxSortie.flush();
			fluxSortie.close();
			return fluxSortie.toString();
		} catch (Exception e) {
			e.printStackTrace();
		}
		return paramUrl;
	}

	// ***************************************************************************************************************************************
	private String removeTypesFromGetUrl(List<String> featureTypeListToKeep, List<String> attributeListToKeepPerFT, List<Integer> attributeListToKeepNbPerFT,
			String paramUrl, int iServer, String operation) {

		// Debug tb 25.06.2009
		// Sauvegarde du prefix et namespace pour le iServer
		String policyServerPrefix = getServerPrefix(getPhysicalServiceURLByIndex(iServer));
		policyServersPrefix.add(policyServerPrefix);
		String policyServerNamespace = getServerNamespace(getPhysicalServiceURLByIndex(iServer));
		policyServersNamespace.add(policyServerNamespace);
		Boolean hasPropertyName = false;
		Boolean isPolicyAll = false;
		if (attributeListToKeepNbPerFT.get(0) == 0) {
			// Indique le fait que Policy est sur ALL pour la featureType
			// courant et que tout les attributs sont demandés
			isPolicyAll = true;
		}
		// Fin de Debug

		// A) Recherche des paramètres de la requête: TYPENAME->FeatureType et
		// PROPERTYNAME->Attributes
		String[] paramFields = paramUrl.split("&");
		for (int i = 0; i < paramFields.length; i++) {
			String[] keyValue = paramFields[i].split("=");
			// B) Ajout des FeaturesTypes autorisés
			if ("TYPENAME".equalsIgnoreCase(keyValue[0])) {
				paramFields[i] = keyValue[0] + "=";
				for (int j = 0; j < featureTypeListToKeep.size(); j++) {
					//					paramFields[i] = paramFields[i] + policyServerPrefix + ":" + featureTypeListToKeep.get(j) + ",";
					//Don't need to add the prefix : it is already int he featuretype name
					paramFields[i] = paramFields[i] + featureTypeListToKeep.get(j) + ",";
				}
				if (paramFields[i].endsWith(",")) {
					paramFields[i] = paramFields[i].substring(0, paramFields[i].length() - 1);
				}
				// break;
			}
			// Debug tb 25.06.2009
			// C) Ajout des Attributs autorisés
			if ("PROPERTYNAME".equalsIgnoreCase(keyValue[0])) // N'est vrai que
				// si
				// opération==GetFeature
			{
				paramFields[i] = keyValue[0] + "=";
				for (int j = 0; j < attributeListToKeepPerFT.size(); j++) {
					//					paramFields[i] = paramFields[i] + policyServerPrefix + ":" + attributeListToKeepPerFT.get(j) + ",";
					//Don't need to add the prefix : it is already int he featuretype name
					paramFields[i] = paramFields[i] + attributeListToKeepPerFT.get(j) + ",";
				}

				// Au cas: localFilter is Set et geom Attribut absent de
				// attributeListToKeepPerFT
				// D) Ajoute l'attribut geom pour le FeatureType courant,
				// -> ce dernier devra ensuite être retiré par XSLT au moment de
				// transform()!!!
				int attributeIndex = 0;
				for (int j = 0; j < featureTypeListToKeep.size(); j++) {
					String geomAttribut = getLocalFilterGeomAttribut(iServer, featureTypeListToKeep.get(j));
					if (!geomAttribut.equals("")) {
						Boolean hasGeomAttribut = false;

						for (int k = 0; k < attributeListToKeepNbPerFT.get(j); k++) {
							String tmpFTA = attributeListToKeepPerFT.get(attributeIndex + k);
							if (tmpFTA != null) {
								//String[] s = tmpFTA.split(":");
								//tmpFTA = s[s.length - 1];
							}
							if (tmpFTA.equals(geomAttribut)) {
								hasGeomAttribut = true;
								break;
							}
						}
						if (!hasGeomAttribut) {
							// Contient tout les liens
							// Server-Prefix-Namespace-AuthorizedFeature-GeomAttribute
							WFSProxyGeomAttributes geomAttributesObj = new WFSProxyGeomAttributes(iServer, policyServerPrefix, policyServerNamespace);
							geomAttributesObj.setFeatureTypeName(featureTypeListToKeep.get(j));
							geomAttributesObj.setGeomAttributName(geomAttribut);
							WFSProxyGeomAttributesList.add(geomAttributesObj);

							// Ajoute l'attribut géométrique à la requête
							// utilisateur
							//							paramFields[i] = paramFields[i] + policyServerPrefix + ":" + geomAttribut + ",";
							paramFields[i] = paramFields[i] + geomAttribut + ",";
						}

						attributeIndex += attributeListToKeepNbPerFT.get(j);
					}
				}

				// Supression de la virgule finale
				if (paramFields[i].endsWith(",")) {
					paramFields[i] = paramFields[i].substring(0, paramFields[i].length() - 1);
				}

				hasPropertyName = true;
			}
			// Fin de Debug
		}
		// Debug tb 25.06.2009
		// C') Ajout des Attributs autorisés si pas de demande explicite dans la
		// requête (absence de PROPERTYNAME)
		// et si Policy Attributes != de ALL
		String propertyNameParam = "propertyName=";
		if (!hasPropertyName && "GetFeature".equalsIgnoreCase(operation) && !isPolicyAll) {
			for (int j = 0; j < attributeListToKeepPerFT.size(); j++) {
				//				propertyNameParam = propertyNameParam + policyServerPrefix + ":" + attributeListToKeepPerFT.get(j) + ",";
				propertyNameParam = propertyNameParam + attributeListToKeepPerFT.get(j) + ",";
			}

			// Au cas: localFilter is Set et geom Attribut absent de
			// attributeListToKeepPerFT
			// D') Ajoute l'attribut geom pour le FeatureType courant,
			// -> ce dernier devra ensuite être retiré par XSLT au moment de
			// transform()!!!
			int attributeIndex = 0;
			for (int j = 0; j < featureTypeListToKeep.size(); j++) {
				String geomAttribut = getLocalFilterGeomAttribut(iServer, featureTypeListToKeep.get(j));
				if (!geomAttribut.equals("")) {
					Boolean hasGeomAttribut = false;

					for (int k = 0; k < attributeListToKeepNbPerFT.get(j); k++) {
						String tmpFTA = attributeListToKeepPerFT.get(attributeIndex + k);
						if (tmpFTA != null) {
							//String[] s = tmpFTA.split(":");
							//tmpFTA = s[s.length - 1];
						}
						if (tmpFTA.equals(geomAttribut)) {
							hasGeomAttribut = true;
							break;
						}
					}
					if (!hasGeomAttribut) {
						// Contient tout les liens
						// Server-Prefix-Namespace-AuthorizedFeature-GeomAttribute
						WFSProxyGeomAttributes geomAttributesObj = new WFSProxyGeomAttributes(iServer, policyServerPrefix, policyServerNamespace);
						geomAttributesObj.setFeatureTypeName(featureTypeListToKeep.get(j));
						geomAttributesObj.setGeomAttributName(geomAttribut);
						WFSProxyGeomAttributesList.add(geomAttributesObj);

						// Ajoute l'attribut géométrique à la requête
						// utilisateur
						//						propertyNameParam = propertyNameParam + policyServerPrefix + ":" + geomAttribut + ",";
						propertyNameParam = propertyNameParam + geomAttribut + ",";
					}

					attributeIndex += attributeListToKeepNbPerFT.get(j);
				}
			}

			// Supression de la virgule finale
			if (propertyNameParam.endsWith(",")) {
				propertyNameParam = propertyNameParam.substring(0, propertyNameParam.length() - 1);
			}
		}
		// Fin de Debug

		// Réécriture de la requête après modification
		paramUrl = "";
		for (int i = 0; i < paramFields.length; i++) {
			paramUrl = paramUrl + paramFields[i] + "&";
		}
		if (!"propertyName=".equalsIgnoreCase(propertyNameParam)) {
			paramUrl = paramUrl + propertyNameParam;
		} else {
			paramUrl = paramUrl.substring(0, paramUrl.length() - 1);
		}

		return paramUrl;
	}

	// ************************************************************************************************************************************************************************************************************
	protected ByteArrayOutputStream buildResponseForServiceException ()
	{
		try 
		{
			for (String path : ogcExceptionFilePathList.values()) 
			{
				DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
				db.setNamespaceAware(false);
				File fMaster = new File(path);
				Document documentMaster = db.newDocumentBuilder().parse(fMaster);
				if (documentMaster != null) 
				{
					NodeList nl = documentMaster.getElementsByTagName("ServiceExceptionReport");
					if (nl.item(0) != null)
					{
						logger.trace("transform begin exception response writting");
						DOMImplementationLS implLS = null;
						if (documentMaster.getImplementation().hasFeature("LS", "3.0")) 
						{
							implLS = (DOMImplementationLS) documentMaster.getImplementation();
						} 
						else 
						{
							DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
							implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
						}

						Node ItemMaster = nl.item(0);
						//Loop on other file
						for (String pathChild : ogcExceptionFilePathList.values()) 
						{
							Document documentChild = null;
							if(path.equals(pathChild))
								continue;

							documentChild = db.newDocumentBuilder().parse(pathChild);

							if (documentChild != null) {
								NodeList nlChild = documentChild.getElementsByTagName("ServiceException");
								if (nlChild != null && nlChild.getLength() > 0) 
								{
									for (int i = 0; i < nlChild.getLength(); i++) 
									{
										Node nnode = nlChild.item(i);
										nnode = documentMaster.importNode(nnode, true);
										ItemMaster.appendChild(nnode);
									}
								}
							}
						}
						ByteArrayOutputStream out = new ByteArrayOutputStream();
						LSSerializer serialiseur = implLS.createLSSerializer();
						LSOutput sortie = implLS.createLSOutput();
						sortie.setEncoding("UTF-8");
						sortie.setByteStream(out);
						serialiseur.write(documentMaster, sortie);
						logger.trace("transform end exception response writting");
						return out;
					}	
				}
			}
		}
		catch (Exception ex)
		{
			ex.printStackTrace();
			logger.error(ex.getMessage());
			return null;
		}
		return null;
	}

	protected boolean filterServersResponsesForOgcServiceExceptionFiles ()
	{
		try
		{
			logger.trace("filterServersResponseFiles begin");
			Set<Map.Entry<Integer,String>> r =  wfsFilePathList.entrySet();
			Map<Integer,String> toRemove = new TreeMap<Integer, String>();

			Iterator<Map.Entry<Integer, String>> it = r.iterator();
			while(it.hasNext())
			{
				Map.Entry<Integer, String> entry = it.next();
				String path =entry.getValue();
				String ext = (path.lastIndexOf(".")==-1)?"":path.substring(path.lastIndexOf(".")+1,path.length());
				if (ext.equals("xml"))
				{
					DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
					Document documentMaster = db.newDocumentBuilder().parse(new File(path));
					if (documentMaster != null) 
					{
						NodeList nl = documentMaster.getElementsByTagName("ServiceExceptionReport");
						if (nl.item(0) != null)
						{
							toRemove.put(entry.getKey(), path);
							//							ogcExceptionFilePathList.put(iFilePath, path);
							//							wfsFilePathList.remove(iFilePath);
						}
					}
				}
			}
			Iterator<Map.Entry<Integer,String>> itR = toRemove.entrySet().iterator();
			while(itR.hasNext())
			{
				Map.Entry<Integer,String> entry = itR.next();

				ogcExceptionFilePathList.put(entry.getKey(), entry.getValue());
				wfsFilePathList.remove(entry.getKey());
			}

			logger.trace("filterServersResponseFiles end");
			return true;
		}
		catch (Exception ex)
		{
			ex.printStackTrace();
			logger.error( ex.getMessage());
			return false;
		}
	}

	public void transform(String version, String currentOperation, HttpServletRequest req, HttpServletResponse resp) {
		try {
			String responseVersion="";

			//Filtre les fichiers réponses des serveurs :
			//ajoute les fichiers d'exception dans ogcExceptionFilePathList
			//les enlève de la collection de résultats wmsFilePathList 
			filterServersResponsesForOgcServiceExceptionFiles();

			//Si le mode de gestion des exceptions est "restrictif" et si au moins un serveur retourne une exception OGC
			//Ou
			//Si le mode de gestion des exceptions est "permissif" et que tous les serveurs retournent des exceptions
			//alors le proxy retourne l'ensemble des exceptions concaténées
			if((sdiVirtualService.getSdiSysExceptionlevel().getValue().equals("restrictive") && ogcExceptionFilePathList.size() > 0) || 
					(sdiVirtualService.getSdiSysExceptionlevel().getValue().equals("permissive") && wfsFilePathList.size() == 0))
			{
				//Traitement des réponses de type exception OGC
				//Le stream retourné contient les exceptions concaténées et mises en forme pour être retournées 
				//directement au client
				logger.debug("Exception(s) returned by remote server(s) are sent to client.");
				ByteArrayOutputStream exceptionOutputStream = buildResponseForOgcServiceException();
				sendHttpServletResponse(req,resp,exceptionOutputStream,"text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}

			// ******************************************************************************************
			// Création d'un fichier XSLT correspondant à celui possiblement
			// spécifié par l'utilisateur
			String userXsltPath = sdiVirtualService.getXsltfilename();

			if (SecurityContextHolder.getContext().getAuthentication() != null) {
				userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
			}

			userXsltPath = userXsltPath + "/" + version + "/" + currentOperation + ".xsl";
			String globalXsltPath = userXsltPath + "/" + version + "/" + currentOperation + ".xsl";
			;

			File xsltFile = new File(userXsltPath);
			boolean isPostTreat = false; // Devient vrai si un xslt user est
			// défini! -> voir fin de la fct
			// transform
			if (!xsltFile.exists()) {
				logger.trace("User postreatment file " + xsltFile.toString() + " does not exist");
				xsltFile = new File(globalXsltPath);
				if (xsltFile.exists()) {
					isPostTreat = true;
				} else {
					logger.trace("Global postreatment file " + xsltFile.toString() + " does not exist");
				}
			} else {
				isPostTreat = true;
			}

			// ******************************************************************************************************
			// Transforms the results using a xslt before sending the response
			// back

			InputStream xml = null;// new
			// FileInputStream(wfsFilePathList.get(0));
			TransformerFactory tFactory = TransformerFactory.newInstance();

			File tempFile = null;
			OutputStream tempFos = null;

			Transformer transformer = null;

			// Selon le choix de l'opération exécutée
			// ***************************************************************
			if (currentOperation != null) {
				if (currentOperation.equalsIgnoreCase("GetCapabilities")) {
					List<File> tempFileCapaList = new Vector<File>();

					// Posttraitement par Server intérrogé ->
					// wfsFilePathList.get(i) contient le fichier réponse
					// *******
					for (int i = 0; i < wfsFilePathList.size(); i++) {

						//Load response file
						FileInputStream fileInputStream = new FileInputStream(wfsFilePathList.get(i));
						InputSource iS = new InputSource(fileInputStream);

						//Get the WFS version of the response
						SAXBuilder sxb = new SAXBuilder();
						org.jdom.Document  docParent = sxb.build(iS);
						responseVersion = docParent.getRootElement().getAttribute("version").getValue();
						responseVersion = responseVersion.replaceAll("\\.", "");
						if(!("100").equalsIgnoreCase(responseVersion))
						{
							//If one of the response version is different than 1.0.0, then the request is rejected
							logger.error( "One of the remote servers can not answer in WFS version 1.0.0. The request is rejected.");
							owsExceptionReport.sendExceptionReport(request, response, OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED+"One of the remote servers can not answer in WFS version 1.0.0.", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK) ;
							return;
						}

						tempFile = createTempFile("transform_GetCapabilities" + UUID.randomUUID().toString(), ".xml");
						tempFos = new FileOutputStream(tempFile);
						ByteArrayInputStream xslt = null;

						StringBuffer sb = buildCapabilitiesXSLT(req, serversIndex.get(i));
						xslt = new ByteArrayInputStream(sb.toString().getBytes());

						transformer = tFactory.newTransformer(new StreamSource(xslt));
						// Write the result in a temporary file
						xml = new BufferedInputStream(new FileInputStream(wfsFilePathList.get(i)));
						transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
						tempFos.close();
						tempFileCapaList.add(tempFile);
					}
					tempFile = mergeCapabilities(tempFileCapaList);

					InputStream in = new BufferedInputStream(new FileInputStream(tempFile));
					InputSource inputSource = new InputSource( in);

					//Application de la transformation XSLT pour la réécriture des métadonnées du service 
					logger.trace("transform begin apply XSLT on service metadata");
					File tempFileCapaWithMetadata = createTempFile("transform_MDGetCapabilities_" + UUID.randomUUID().toString(), ".xml");
					FileOutputStream tempServiceMD = new FileOutputStream(tempFileCapaWithMetadata);
					StringBuffer sb = buildServiceMetadataCapabilitiesXSLT(null);
					InputStream xslt = new ByteArrayInputStream(sb.toString().getBytes());
					XMLReader xmlReader = XMLReaderFactory.createXMLReader();
					SAXSource saxSource = new SAXSource(xmlReader, inputSource);
					transformer = tFactory.newTransformer(new StreamSource(xslt));
					transformer.setOutputProperty(OutputKeys.INDENT, "yes");
					transformer.setOutputProperty("{http://xml.apache.org/xslt}indent-amount", "2");
					transformer.transform(saxSource, new StreamResult(tempServiceMD));
					tempServiceMD.flush();
					tempServiceMD.close();

					tempFile = tempFileCapaWithMetadata;
					logger.trace("transform end apply XSLT on service metadata");

				} else if (currentOperation.equalsIgnoreCase("DescribeFeatureType")) {
						List<File> tempFileDescribeType = new Vector<File>(); // Utile
						// si
						// wfsFilePathList.size()>1

						// Posttraitement par Server intérrogé ->
						// wfsFilePathList.get(i) contient le fichier réponse
						// ********************
						for (int j = 0; j < wfsFilePathList.size(); j++) {
							// Debug tb 12.05.2009
							Boolean isWFSDescribeFeatureTypeEdit = false;
							// Fin de Debug
							StringBuffer WFSDescribeFeatureType = new StringBuffer();
							WFSDescribeFeatureType
							.append("<xsl:stylesheet version=\"1.00\"  xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:ogcwfs=\"http://www.opengis.net/wfs\" xmlns:gml=\"http://www.opengis.net/gml\"  xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
							// On récupère le gml
							if(wfsFilePathList.get(j) == null)
								continue;
							InputStream dataSourceInputStream = new FileInputStream(wfsFilePathList.get(j));
							// PBM à la Ligne suivante: attention "schema" est
							// un singleton: n'est pas cleaner entre chaque
							// appel sur les servlets!
							// Or, "SchemaFactory" merge chaque schéma lu s'il
							// est différent des appels précédents
							// Schema schema =
							// org.geotools.xml.SchemaFactory.getInstance(null,
							// dataSourceInputStream);
							// Debug tb 02.10.2009
							Schema schema = getSchema(dataSourceInputStream);
							// XSISAXHandler contentHandler = new
							// XSISAXHandler(null);
							// XSISAXHandler.setLogLevel(Level.WARNING);
							// SAXParserFactory factory =
							// SAXParserFactory.newInstance();
							// factory.setNamespaceAware(true);
							// factory.setValidating(false);
							// SAXParser parser = factory.newSAXParser();
							// parser.parse(dataSourceInputStream,
							// contentHandler);
							//
							// Schema schema = contentHandler.getSchema();
							// "registerSchema" seulement nécessaire si appeler
							// dans une autre méthode
							// URI targetNamespace=schema.getTargetNamespace();
							// org.geotools.xml.SchemaFactory.registerSchema(targetNamespace,schema);
							// Fin de Debug
							ComplexType[] ct = schema.getComplexTypes();
							// Debug tb 09.06.2009
							org.geotools.xml.schema.Element[] el = schema.getElements();
							logger.trace("transform_DescribeFeature_ComplexType: " + ct.length);
							// Fin de Debug

							for (int i = 0; i < ct.length; i++) {
								// Debug tb 05.10.2009
								// Préparation de tmpFT, récupération du nom du
								// featureType: ComplexType.name correspondant
								// au Element.name
								String tmpFT = "";
								tmpFT = schema.getPrefix()+":"+el[i].getName();
								//String[] s = tmpFT.split(":");
								//tmpFT = s[s.length - 1];
								// Fin de Debug
								if (isFeatureTypeAllowed(el[i].getName(), getPhysicalServiceURLByIndex(serversIndex.get(j)))) {
									org.geotools.xml.schema.Element[] elem = ct[i].getChildElements();
									for (int k = 0; k < elem.length; k++) {
										if (!isAttributeAllowed(getPhysicalServiceURLByIndex(serversIndex.get(j)), el[i].getName(), elem[k].getName())) {
											// Cela supprime, de la réponse, les
											// Attributs non autorisés du
											// FeatureType courant qui est
											// autorisé
											WFSDescribeFeatureType.append("<xsl:template match=\"//xsd:complexType[@name ='" + ct[i].getName()
													+ "']//xsd:element[@name='" + elem[k].getName() + "']\">");
											WFSDescribeFeatureType.append("</xsl:template>");
											// Debug tb 12.05.2009
											isWFSDescribeFeatureTypeEdit = true;
											// Fin de Debug
										}
									}
								} else {
									// Cela supprime, de la réponse, le
									// FeatureType courant qui est non autorisé
									WFSDescribeFeatureType.append("<xsl:template match=\"//xsd:complexType[@name ='" + ct[i].getName() + "']\">");
									WFSDescribeFeatureType.append("</xsl:template>");
									WFSDescribeFeatureType.append("<xsl:template match=\"//xsd:element[@name ='" + ct[i].getName() + "']\">");
									WFSDescribeFeatureType.append("</xsl:template>");
									// Debug tb 12.05.2009
									isWFSDescribeFeatureTypeEdit = true;
									// Fin de Debug
								}
							}
							// Debug tb 12.05.2009
							// Si une transformation XSLT doit être éxecutée
							if (isWFSDescribeFeatureTypeEdit) {
								// Fin de Debug
								// Cela applique une copie du contenu de la
								// réponse
								WFSDescribeFeatureType.append("  <!-- Whenever you match any node or any attribute -->");
								WFSDescribeFeatureType.append("<xsl:template match=\"node()|@*\">");
								WFSDescribeFeatureType.append("<!-- Copy the current node -->");
								WFSDescribeFeatureType.append("<xsl:copy>");
								WFSDescribeFeatureType.append("<!-- Including any attributes it has and any child nodes -->");
								WFSDescribeFeatureType.append("<xsl:apply-templates select=\"@*|node()\"/>");
								WFSDescribeFeatureType.append("</xsl:copy>");
								WFSDescribeFeatureType.append("</xsl:template>");
								WFSDescribeFeatureType.append("</xsl:stylesheet>");

								// Debug de 15.06.2009
								// Log le timing avant transformation
								Date d = new Date();
								logger.info( "DescribeFeatureTypeBeginTransfoDateTime="+ dateFormat.format(d));
								// Fin de debug
								xml = new BufferedInputStream(new FileInputStream(wfsFilePathList.get(j)));
								tempFile = createTempFile("transform_DescribeFeatureType" + UUID.randomUUID().toString(), ".xml");
								tempFos = new FileOutputStream(tempFile);
								ByteArrayInputStream xslt = null;
								xslt = new ByteArrayInputStream(WFSDescribeFeatureType.toString().getBytes());
								transformer = tFactory.newTransformer(new StreamSource(xslt));
								// Write the result in a temporary file
								transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
								tempFos.close();
								tempFileDescribeType.add(tempFile);
								// Debug de 15.06.2009
								// Log le timing après transformation
								d = new Date();
								logger.info("DescribeFeatureTypeEndTransfoDateTime="+ dateFormat.format(d));
								// Fin de debug
								// Debug tb 12.05.2009
							} else {
								xml = new BufferedInputStream(new FileInputStream(wfsFilePathList.get(j)));
								tempFile = createTempFile("transform_DescribeFeatureType" + UUID.randomUUID().toString(), ".xml");
								tempFos = new FileOutputStream(tempFile);
								BufferedOutputStream BufTempFos = new BufferedOutputStream(tempFos);

								// Write the result in a temporary file
								byte byteRead[] = new byte[32768];
								int index = xml.read(byteRead, 0, 32768);
								try {
									while (index != -1) {
										BufTempFos.write(byteRead, 0, index);
										index = xml.read(byteRead, 0, 32768);
									}
									BufTempFos.flush();
									xml.close();
									BufTempFos.close();
									tempFileDescribeType.add(tempFile);
								} catch (Exception e) {
									e.printStackTrace();
									logger.error("transform_DescribeFeatureType_BufferedOutputStream ERROR="+ e.getMessage() + " " + e.getLocalizedMessage() + " "
											+ e.getCause());
									logger.error("transform_DescribeFeatureType_BufferedOutputStream ERROR="+ e.toString());
								}
							}
							// Fin de Debug
						}
						// Colle les "tempFile" en un résultat: Util si
						// wfsFilePathList.size()>1
						if (wfsFilePathList.size() > 1)
							tempFile = mergeDescribeFeatureType(tempFileDescribeType);
				} else if (currentOperation.equalsIgnoreCase("GetFeature")) {
						List<File> tempGetFeatureFile = new Vector<File>();

						// Posttraitement par Server intérrogé ->
						// wfsFilePathList.get(i) contient le fichier réponse
						// ***********************
						// Voir dans la Policy <server>
						for (int iFileServer = 0; iFileServer < wfsFilePathList.size(); iFileServer++) {

							// Debug tb 07.05.2009
							// On récupère le srs de la réponse -> srsSource
							// ATTENTION: Cela ne retourne que le srsSource du
							// premier featureType venu dans la réponse du
							// serveur courant
							// DataInputStream est trop lent pour les réponses
							// avec bcp d'entrées (>1000), cette lecture plante
							// Tomcat
							// lors de l'instruction readLine() -> pas de retour
							// à la ligne trouvé
							// La fonction de remplacement lit progressivement
							// le fichier (buffer de 512 caractères) afin de
							// retourner la valeur du "srsName"
							BufferedReader dis = new BufferedReader(new FileReader(wfsFilePathList.get(iFileServer)));
							boolean breakOut = false;
							int bufSize = 512;
							int srsIndex = 1;
							char[] cbuf = new char[bufSize];
							String s = null;

							String srsSource = null;

							logger.trace("GetFeature begin srsName extract");
							if (dis.ready() && dis.markSupported()) {
								dis.mark(bufSize + 1);
								while (dis.read(cbuf, 0, bufSize) != -1) {
									s = new String(cbuf);
									if ((srsIndex = s.indexOf("srsName")) > 0) {
										dis.reset();
										dis.skip(srsIndex); // index of
										// 'srsName' in the
										// buffer
										dis.mark(bufSize + 1);
										dis.read(cbuf, 0, bufSize);
										srsSource = new String(cbuf);
										if (srsSource.indexOf("\"") > 0) {
											srsIndex = srsSource.indexOf("\"") + 1;
											srsSource = srsSource.substring(srsSource.indexOf("\"") + 1);
											if (srsSource.indexOf("\"") > 0) {
												srsSource = srsSource.substring(0, srsSource.indexOf("\""));
												break;
											} else {
												dis.reset();
												dis.skip(srsIndex); // index of
												// first '"'
												// in the
												// buffer
												dis.mark(bufSize + 1);
												srsSource = "";
												while (dis.read(cbuf, 0, bufSize) != -1) {
													s = new String(cbuf);
													srsSource = srsSource.concat(s);
													if (srsSource.indexOf("\"") > 0) {
														srsSource = srsSource.substring(0, srsSource.indexOf("\""));
														breakOut = true;
														break;
													}
												}
											}
										} else if (srsSource.indexOf("\'") > 0) {
											srsIndex = srsSource.indexOf("\'") + 1;
											srsSource = srsSource.substring(srsSource.indexOf("\'") + 1);
											if (srsSource.indexOf("\'") > 0) {
												srsSource = srsSource.substring(0, srsSource.indexOf("\'"));
												break;
											} else {
												dis.reset();
												dis.skip(srsIndex); // index of
												// first "'"
												// in the
												// buffer
												dis.mark(bufSize + 1);
												srsSource = "";
												while (dis.read(cbuf, 0, bufSize) != -1) {
													s = new String(cbuf);
													srsSource = srsSource.concat(s);
													if (srsSource.indexOf("\'") > 0) {
														srsSource = srsSource.substring(0, srsSource.indexOf("\'"));
														breakOut = true;
														break;
													}
												}
											}
										}
									}
									if (breakOut == false) {
										dis.reset();
										dis.skip(bufSize - 8); // 8 because
										// 'srsName'
										// have 7 char
										dis.mark(bufSize + 1);
										srsSource = null;
									} else {
										break;
									}
								}
							}
							dis.close();
							logger.trace("GetFeature end srsName extract");

							// fin de Debug

							// Remplissage de tempFile avec la
							// réponse*******************************************
							logger.trace("GetFeature begin to fill tempFile");
							String tempFileName = "transform_GetFeature" + UUID.randomUUID().toString();
							tempFile = createTempFile(tempFileName, ".xml");
							tempFos = new FileOutputStream(tempFile);

							XMLReader xmlReader = XMLReaderFactory.createXMLReader();
							String user = (String) getUsername(getPhysicalServiceURLByIndex(serversIndex.get(iFileServer)));
							String password = (String) getPassword(getPhysicalServiceURLByIndex(serversIndex.get(iFileServer)));
							ResourceResolver rr = null;
							if (user != null && user.length() > 0) {
								rr = new ResourceResolver(user, password);
								xmlReader.setEntityResolver(rr);
							}

							xml = new BufferedInputStream(new FileInputStream(wfsFilePathList.get(iFileServer)));
							BufferedOutputStream BufTempFos = new BufferedOutputStream(tempFos);

							// Write the result in a temporary file
							byte byteRead[] = new byte[32768];
							int index = xml.read(byteRead, 0, 32768);
							try {
								while (index != -1) {
									BufTempFos.write(byteRead, 0, index);
									index = xml.read(byteRead, 0, 32768);
								}
								BufTempFos.flush();
								xml.close();
								tempFos.close();
							} catch (Exception e) {
								e.printStackTrace();
								logger.error("transform_GetFeature_BufferedOutputStream ERROR="+ e.getMessage() + " " + e.getLocalizedMessage() + " " + e.getCause());
								logger.error("transform_GetFeature_BufferedOutputStream ERROR="+ e.toString());
							}
							logger.trace("GetFeature end to fill tempFile");
							// Fin de remplissage de tempFile avec la réponse

							// Application du filtrage LocalFilter géométrique:
							// polygon de
							// droits*******************************************

							// Récupération de localFilter
							logger.trace("GetFeature begin to apply localFilter");
							String filter = null;
							if (featureTypePathList.size() > 0) {
								// Si localFilter est actif mais not Set ->
								// filter = ""
								if (serversIndex.contains(iFileServer) && featureTypePathList.contains(iFileServer))
									filter = getFeatureTypeLocalFilter(getPhysicalServiceURLByIndex(serversIndex.get(iFileServer)), featureTypePathList.get(iFileServer));
							}
							// Debug tb 15.06.2009
							// Si un localFilter est défini
							if (filter != null) {
								if (!filter.equals("")) { 
									// Fin de Debug
									// Création de "doc" -> un tempFile formaté
									// en GMLFeatureCollection
									Map<String, Boolean> hints = new HashMap<String, Boolean>();
									hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);

									GMLFeatureCollection doc = null; // Réutilise
									// le
									// résultat
									// TempFile
									// Debug tb 28.09.2009
									// if (user!=null && user.length()>0)
									// {
									// doc =
									// (GMLFeatureCollection)DocumentFactory.getInstance(tempFile.toURI(),hints,Level.WARNING,user,password);
									// }
									// else
									// Utilisation de la classe Java
									// "Authenticator" qui ajoute
									// l'authentication, selon les besoins, à la
									// classe java "URLConnection".
									// Pour des raisons de vérification de
									// schema xsd (requete DescribeFeatureType),
									// la classe "DocumentFactory" nécessite
									// l'authentication au cas où geoserver
									// défini un compte de service.
									// Voir ProxyServlet.getPassword
									// org.easysdi.security.EasyAuthenticator.setCredientials(user,
									// password);
									// Fin de Debug
									// doc =
									// (GMLFeatureCollection)org.geotools.xml.SchemaFactory.getInstance(null,
									// tempFile.toURI(), Level.WARNING, user,
									// password); //SchemaFactory Instance non
									// castable en une instance
									// GMLFeatureCollection
									// doc =
									// (GMLFeatureCollection)DocumentFactory.getInstance(tempFile.toURI(),hints,Level.WARNING);
									doc = (GMLFeatureCollection) documentFactoryGetInstance(tempFile, user, password);
									// Création du fichier résultat de la
									// proachaine transformation
									File tempFile2 = createTempFile("transform_GetFeature_2_" + UUID.randomUUID().toString(), ".xml");
									tempFos = new FileOutputStream(tempFile2);
									if (filter != null) {
										logger.trace(filter);
									}

									// Application du filtrage LocalFilter
									// géométrique, il s'agit d'une
									// transformation
									filterFC(tempFos, filter, doc, getServletUrl(req), srsSource, serversIndex.get(iFileServer)); // l'entrée
									// "TempFile"
									// a
									// été
									// 'copié'
									// dans
									// "doc"
									// et
									// la
									// sortie
									// "tempFos"
									// édite
									// "TempFile2"

									// Copy du contenu transformé dans tempFile
									if (filter != null) {
										tempFos.close();

										if (tempFile != null)
											tempFile.delete();
										tempFile = tempFile2; // Copy du fichier
										// suite à la
										// transformation
										// géométrique
									}
									// Debug tb 15.06.2009
								}
							}
							logger.trace("GetFeature end to apply localFilter");
							// Fin de Debug

							// Fin de l'application du filtrage LocalFilter
							// géométrique: "TempFile" a été filtré une première
							// fois
							// et est stocké dans "TempFile"

							// Debug tb 10.06.2009
							// Application du filtrage de l'attribut géom s'il
							// existe et qu'a la base il n'est pas
							// autorisé*******************************************
							// Filtrage de l'attribut geom si ajouté uniquement
							// pour les besoins de localFilter
							for (int a = 0; a < WFSProxyGeomAttributesList.size(); a++) {
								logger.trace("GetFeature begin to apply geomRemover");
								if (WFSProxyGeomAttributesList.get(a).getRequestServerIndex() == serversIndex.get(iFileServer)) {
									// Le test qui suit n'a aucune utilité car
									// le proxy actuel ne prend pas en charge
									// les multi featureType avec géométrie
									// non-autorisée
									// -> on parle en effet d'un LocalFilter par
									// Server à l'étape précédente et l'algo de
									// recherche srsName ne retourne que celui
									// du premier featureType venu!
									// Donc pour un serveur déterminé, il ne
									// peut correspondre que un featureType dans
									// ca cas
									// Donc le test if Serveur n'est valable que
									// lorsque a ==
									// serversIndex.get(iFileServer)
									if (WFSProxyGeomAttributesList.get(a).getFeatureTypeName() == WFSProxyGeomAttributesList.get(a).getFeatureTypeName()) {
										// Log le timing avant transformation
										Date d = new Date();
										logger.info("GetFeatureTypeGeomBeginTransfoDateTime="+ dateFormat.format(d));

										// Création du fichier résultat de la
										// prochaine transformation
										File tempFile2 = createTempFile("transform_GetFeature_3_" + UUID.randomUUID().toString(), ".xml");
										tempFos = new FileOutputStream(tempFile2);
										ByteArrayInputStream xslt = null;

										// Construction du fichier xslt:
										// traitement intermédiaire indépendant
										xslt = new ByteArrayInputStream(buildGetFeatureXSLT(a, a).toString().getBytes());

										xml = new BufferedInputStream(new FileInputStream(tempFile));
										SAXSource saxSource = new SAXSource(xmlReader, new InputSource(xml));
										transformer = tFactory.newTransformer(new StreamSource(xslt));
										// write the tempFile via tempFos
										transformer.transform(saxSource, new StreamResult(tempFos));

										tempFos.close();

										if (tempFile != null)
											tempFile.delete();
										tempFile = tempFile2; // Copy du fichier
										// suite à la
										// suppression
										// de l'attribut
										// geom
										// Log le timing après transformation
										d = new Date();
										logger.info("GetFeatureTypeGeomEndTransfoDateTime="+ dateFormat.format(d));
									}
								}
								logger.trace("GetFeature end to apply geomRemover");
							}
							// Fin de l'application du filtrage de l'attribut
							// géom: la réponse à la requête a été filtrée
							// une deuxiême fois et est stocké dans "TempFile"
							tempGetFeatureFile.add(tempFile);
						}
						// Fin de Debug

						// Construction du fichier xslt: phase 2, jointure des
						// résultats des différents serveurs
						// Debug tb 13.05.2009
						// Au cas où il exite plus d'un serveur qui donne
						// réponse: isWFSGetFeatureRenameFtEdit = true
						if (wfsFilePathList.size() > 1) {
							// Fin de Debug
							logger.trace("GetFeature begin to merge servers Results");
							// Appel à la fonction d'application de la
							// transformation XSLT finale (contenu de
							// "WFSGetFeatureRenameFt")
							// "tempGetFeatureFile" contient entre autre
							// "TempFile" (en fait ici uniquement "TempFile"
							// si seulement un serveur est appelé)
							// Attention: filtrage LocalFilter rend impossible
							// l'application de la nouvelle transformation xslt
							// ci-après
							// Le fichier "tempFile" est devenu non
							// utilisable!!! -> "internal serveur error" lorsque
							// trop de résultats
							tempFile = mergeGetFeatures(tempGetFeatureFile, tFactory, transformer);
							logger.trace("GetFeature end to merge servers Results");
							// Debug tb 13.05.2009
						}
						// Fin de Debug
					

					if(tempFile != null)
					{
						//Replace url pointing to the remote server in the  <wfs:FeatureCollection>  element by proxy url
						SAXBuilder sxb = new SAXBuilder();
						org.jdom.Document  docParent = sxb.build(tempFile);
						org.jdom.Element racine = docParent.getRootElement();
						Namespace nsXSI = null;
						List<?> lns =  racine.getAdditionalNamespaces();
						Iterator<?> ilns = lns.iterator();
						while (ilns.hasNext())
						{
							Namespace ns = (Namespace)ilns.next();
							if(ns.getPrefix().equalsIgnoreCase("xsi"))
							{
								nsXSI = ns;
								break;
							}
						}
						if(nsXSI != null)
						{
							org.jdom.Attribute schemaLocation = racine.getAttribute("schemaLocation", nsXSI);
							String[] values =  schemaLocation.getValue().split(" ");
							for (int i = 0 ; i < values.length ; i++)
							{
								if(values[i].contains("DescribeFeatureType"))
								{
									values[i] = values[i].replace(values[i].subSequence(0, values[i].indexOf("?")),getServletUrl(req) );
									break;
								}
							}
							String newAttValue = "";
							for (int i = 0 ; i < values.length ; i++)
							{
								newAttValue += values[i] + " ";
							}
							racine.setAttribute("schemaLocation", newAttValue, nsXSI);
							XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
							sortie.output(docParent, new FileOutputStream(tempFile.getAbsolutePath()));
						}
					}
				}

				// Envoie du résultat des traitements "TempFile" sur la réponse
				// à la requête *******************************************

				/*
				 * if a user xslt file exists then post-treat again "TempFile"
				 * and write the response
				 */
				if (isPostTreat) {
					logger.trace("GetFeature begin to apply user xslt");
					PrintWriter out = resp.getWriter();
					transformer = tFactory.newTransformer(new StreamSource(xsltFile)); // Voir
					// definition
					// en
					// haut
					// de
					// fct
					// transform
					if (tempFile != null)
						transformer.transform(new StreamSource(tempFile), new StreamResult(out));
					// Pourquoi le fichier du premier serveur??? ->
					// wfsFilePathList.get(0) et pas le résultat joint des
					// transformations sur les serveur: TempFile
					else
						transformer.transform(new StreamSource(wfsFilePathList.get(((TreeMap)wfsFilePathList).firstKey())), new StreamResult(out));
					// delete the temporary file
					tempFile.delete();
					out.close();
					// the job is done. we can go out
					logger.trace("GetFeature end to apply user xslt");
					return;
				}
			}


			//			BufferedReader reader = new BufferedReader(new FileReader(tempFile));
			//            String line = "", oldtext = "";
			//            while((line = reader.readLine()) != null)
			//                {
			//                oldtext += line + "\r\n";
			//            }
			//            reader.close();
			//            // replace a word in a file
			//            //String newtext = oldtext.replaceAll("drink", "Love");
			//           
			//            //To replace a line in a file
			//            String newtext = oldtext.replaceAll("https://secure.vd.ch/asitvd/wfs", "http://localhost/proxy/ogc/asit-wfs");
			//           
			//            FileWriter writer = new FileWriter(tempFile);
			//            writer.write(newtext);
			//            writer.close();

			// No post rule to apply.
			// Copy the file result on the output stream

			resp.setContentType("text/xml");

			InputStream is = null;
			if (tempFile == null) {
				String wfsFileResult = wfsFilePathList.get(((TreeMap)wfsFilePathList).firstKey());
				is = new FileInputStream(wfsFileResult);
				resp.setContentLength((int) new File(wfsFileResult).length());
			} else {
				is = new FileInputStream(tempFile);
				resp.setContentLength((int) tempFile.length());
			}

			// Ecriture du fichier dans le flux de sortie
			// OutputStream os = resp.getOutputStream();
			BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());

			byte byteRead[] = new byte[32768];
			int index = is.read(byteRead, 0, 32768);
			try {
				while (index != -1) {
					os.write(byteRead, 0, index);
					index = is.read(byteRead, 0, 32768);
				}
				os.flush();
				is.close();
			} catch (Exception e) {
				e.printStackTrace();
				logger.error("BufferedOutputStream ERROR="+ e.getMessage() + " " + e.getLocalizedMessage() + " " + e.getCause());
				logger.error("BufferedOutputStream ERROR="+ e.toString());
			} finally {
				Date d = new Date();
				logger.info("ClientResponseDateTime="+ dateFormat.format(d));

				if (tempFile != null) {
					logger.error("ClientResponseLength="+ tempFile.length());
					tempFile.delete();
				}
			}

		}catch (SAXParseException e)
		{
			e.printStackTrace();
			logger.error(e.getMessage());
			try {
				owsExceptionReport.sendExceptionReport(request, response, OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY+"Response format not recognized.", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK) ;
			} catch (IOException e1) {
				logger.error( OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
		} 
		catch (Exception e) {
			e.printStackTrace();
			logger.error("Transform ERROR="+ e.toString());
			try {
				owsExceptionReport.sendExceptionReport(request, response, OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY, OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK) ;
			} catch (IOException e1) {
				logger.error( OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
			}
		}
	}


	// ***************************************************************************************************************************************
	private File mergeGetFeatures(List<File> tempGetFeaturesList, TransformerFactory tFactory, Transformer transformer) {
		if (tempGetFeaturesList.size() == 0)
			return null;

		try {
			File fMaster = tempGetFeaturesList.get(0);
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(true);
			Document documentMaster = db.newDocumentBuilder().parse(fMaster);
			DOMImplementationLS implLS = null;
			if (documentMaster.getImplementation().hasFeature("LS", "3.0")) {
				implLS = (DOMImplementationLS) documentMaster.getImplementation();
			} else {
				DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
				implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
			}
			if (implLS == null) {
				logger.error("DOM Load and Save not Supported. Multiple server is not allowed");
				return fMaster;
			}
			NodeList nlMaster = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs", "FeatureCollection");
			Node ItemMaster = nlMaster.item(0);
			for (int i = 1; i < tempGetFeaturesList.size(); i++) {
				File fChild = tempGetFeaturesList.get(i);
				Document documentChild = db.newDocumentBuilder().parse(fChild);
				NodeList nlFeatureMember = documentChild.getElementsByTagNameNS("http://www.opengis.net/gml", "featureMember");
				for (int j = 0; j < nlFeatureMember.getLength(); j++) {
					ItemMaster.insertBefore(documentMaster.importNode(nlFeatureMember.item(j).cloneNode(true), true), null);
				}
			}

			File f = createTempFile("mergeGetFeatures_f_" + UUID.randomUUID().toString(), ".xml");
			FileOutputStream fluxSortie = new FileOutputStream(f);
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			sortie.setSystemId(f.toString());
			sortie.setByteStream(fluxSortie);
			serialiseur.write(documentMaster, sortie);
			fluxSortie.flush();
			fluxSortie.close();

			return f;
		} catch (Exception e) {
			e.printStackTrace();
			logger.error("mergeGetFeatures ERROR="+ e.getMessage());
			return null;
		}
	}

	// ***************************************************************************************************************************************
	private File mergeDescribeFeatureType(List<File> tempFileDescribeType) {
		if (tempFileDescribeType.size() == 0)
			return null;
		try {
			File fMaster = tempFileDescribeType.get(0);
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(true);
			Document documentMaster = db.newDocumentBuilder().parse(fMaster);
			DOMImplementationLS implLS = null;
			if (documentMaster.getImplementation().hasFeature("LS", "3.0")) {
				implLS = (DOMImplementationLS) documentMaster.getImplementation();
			} else {
				DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
				implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
			}
			if (implLS == null) {
				logger.error("DOM Load and Save not Supported. Multiple server is not allowed");
				return fMaster;
			}
			NodeList nlMaster = documentMaster.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema", "schema");
			Node ItemMaster = nlMaster.item(0);

			NodeList nlElements = documentMaster.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema", "element");
			//Find the current object sdiPhysicalservicePolicy which linked PhysicalService and Policy and hold the prefix and namespace
			SdiPhysicalservicePolicy currentPhysicalservicePolicy=null;
			Set<SdiPhysicalservicePolicy> physicalservicePolicies =  getPhysicalServiceByIndex(0).getSdiPhysicalservicePolicies();
			Iterator<SdiPhysicalservicePolicy> it = physicalservicePolicies.iterator();
			while (it.hasNext())
			{
				SdiPhysicalservicePolicy physicalservicePolicy = it.next();
				if(physicalservicePolicy.getSdiPolicy().getId().equals(sdiPolicy.getId()))
				{
					currentPhysicalservicePolicy = physicalservicePolicy;
					break;
				}
			}
			for (int i = 0; i < nlElements.getLength(); i++) {
				if (nlElements.item(i).getAttributes().getNamedItem("substitutionGroup") != null) {
					if ("gml:_Feature".equals(nlElements.item(i).getAttributes().getNamedItem("substitutionGroup").getTextContent())) {
						String origName = nlElements.item(i).getAttributes().getNamedItem("name").getTextContent();
						
						
						nlElements.item(i).getAttributes().getNamedItem("name").setTextContent(currentPhysicalservicePolicy.getPrefix() + ":" + origName);
					}
				}

			}

			for (int i = 1; i < tempFileDescribeType.size(); i++) {
				Document documentChild = db.newDocumentBuilder().parse(tempFileDescribeType.get(i));

				NodeList nl1 = documentChild.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema", "schema");
				NodeList nl2 = nl1.item(0).getChildNodes();

				nlElements = documentChild.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema", "element");
				
				//Find the current object sdiPhysicalservicePolicy which linked PhysicalService and Policy and hold the prefix and namespace
				SdiPhysicalservicePolicy derivePhysicalservicePolicy=null;
				Set<SdiPhysicalservicePolicy> dphysicalservicePolicies =  getPhysicalServiceByIndex(i).getSdiPhysicalservicePolicies();
				Iterator<SdiPhysicalservicePolicy> idt = dphysicalservicePolicies.iterator();
				while (idt.hasNext())
				{
					SdiPhysicalservicePolicy physicalservicePolicy = idt.next();
					if(physicalservicePolicy.getSdiPolicy().getId().equals(sdiPolicy.getId()))
					{
						derivePhysicalservicePolicy = physicalservicePolicy;
						break;
					}
							
				}
				for (int j = 0; j < nlElements.getLength(); j++) {
					if (nlElements.item(j).getAttributes().getNamedItem("substitutionGroup") != null) {
						if ("gml:_Feature".equals(nlElements.item(j).getAttributes().getNamedItem("substitutionGroup").getTextContent())) {
							String origName = nlElements.item(j).getAttributes().getNamedItem("name").getTextContent();
							nlElements.item(j).getAttributes().getNamedItem("name").setTextContent(derivePhysicalservicePolicy.getPrefix() + ":" + origName);
						}
					}

				}

				for (int j = 0; j < nl2.getLength(); j++) {
					ItemMaster.insertBefore(documentMaster.importNode(nl2.item(j).cloneNode(true), true), null);
				}

			}

			File f = createTempFile("mergeDescribeFeatureType_f_" + UUID.randomUUID().toString(), ".xml");

			FileOutputStream fluxSortie = new FileOutputStream(f);
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			sortie.setSystemId(f.toString());
			sortie.setByteStream(fluxSortie);
			serialiseur.write(documentMaster, sortie);
			fluxSortie.flush();
			fluxSortie.close();

			return f;
		} catch (Exception e) {
			e.printStackTrace();
			logger.error( e.getMessage());
			return null;
		}
	}

	// ***************************************************************************************************************************************
	private File mergeCapabilities(List<File> tempFileCapa) {

		if (tempFileCapa.size() == 0)
			return null;
		try {
			File fMaster = tempFileCapa.get(0);
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(true);
			Document documentMaster = db.newDocumentBuilder().parse(fMaster);
			DOMImplementationLS implLS = null;
			if (documentMaster.getImplementation().hasFeature("LS", "3.0")) {
				implLS = (DOMImplementationLS) documentMaster.getImplementation();
			} else {
				DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
				implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
			}
			if (implLS == null) {
				logger.error("DOM Load and Save not Supported. Multiple server is not allowed");
				return fMaster;
			}
			NodeList nlMaster = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs", "FeatureTypeList");
			Node ItemMaster = nlMaster.item(0);

			for (int i = 1; i < tempFileCapa.size(); i++) {

				Document documentChild = db.newDocumentBuilder().parse(tempFileCapa.get(i));
				NodeList nl = documentChild.getElementsByTagNameNS("http://www.opengis.net/wfs", "FeatureType");

				for (int j = 0; j < nl.getLength(); j++) {
					ItemMaster.insertBefore(documentMaster.importNode(nl.item(j).cloneNode(true), true), null);
				}
			}

			File f = createTempFile("mergeCapabilities_f_" + UUID.randomUUID().toString(), ".xml");

			FileOutputStream fluxSortie = new FileOutputStream(f);
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			sortie.setSystemId(f.toString());
			sortie.setByteStream(fluxSortie);
			serialiseur.write(documentMaster, sortie);
			fluxSortie.flush();
			fluxSortie.close();

			return f;
		} catch (Exception e) {
			e.printStackTrace();
			logger.error(e.getMessage());
			return null;
		}
	}

	// ***************************************************************************************************************************************
	private Schema getSchema(InputStream dataSourceInputStream) throws SAXException {
		try {
			XSISAXHandler contentHandler = new XSISAXHandler(null);
			XSISAXHandler.setLogLevel(Level.WARNING);
			setParser();
			parser.parse(dataSourceInputStream, contentHandler);
			return contentHandler.getSchema();
		} catch (IOException e) {
			e.printStackTrace();
			return null;
		}
	}

	// ***************************************************************************************************************************************

	private void setParser() throws SAXException {
		if (parser == null) {
			SAXParserFactory spfactory = SAXParserFactory.newInstance();
			spfactory.setNamespaceAware(true);
			spfactory.setValidating(false);

			try {
				parser = spfactory.newSAXParser();
			} catch (ParserConfigurationException e) {
				throw new SAXException(e);
			} catch (SAXException e) {
				throw new SAXException(e);
			}
		}
	}

	// ***************************************************************************************************************************************
	/**
	 * Construit le xslt du filtrage de l'attribut géométrique servant au
	 * LocalFilter
	 * 
	 * @param int listIndex : index pointant l'item de la liste
	 *        WFSProxyGeomAttributesList comportant l'attribut géom à retirer
	 * @param int iServer : index pointant le serveur courant
	 * @return list<String> : code de la transformation XSLT
	 */
	private StringBuffer buildGetFeatureXSLT(int listIndex, int iServer) {
		StringBuffer WFSGetFeature = new StringBuffer();

		// Debug tb 15.06.2009
		String prefix = WFSProxyGeomAttributesList.get(iServer).getpolicyServerPrefix();
		String featureType = WFSProxyGeomAttributesList.get(listIndex).getFeatureTypeName();
		String geomAttribut = WFSProxyGeomAttributesList.get(listIndex).getGeomAttributName();

		String nameSpace = WFSProxyGeomAttributesList.get(iServer).getpolicyServerNamespace();
		// On le considère ici indépendant de la requête utilisateur, à la seul
		// fin de satisfaire la contrainte xsl!!!
		// Faux la transformation ne s'applique correctement que si nameSpace
		// est défini en fonction de la requête d'origine...

		WFSGetFeature
		.append("<xsl:stylesheet version=\"1.00\" xmlns:ogcwfs=\"http://www.opengis.net/wfs\" xmlns:gml=\"http://www.opengis.net/gml\"  xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n");
		WFSGetFeature.append("<xsl:template xmlns:" + prefix + "=\"" + nameSpace + "\" match=\"//" + prefix + ":" + featureType + "/" + prefix + ":"
				+ geomAttribut + "\">\n");
		WFSGetFeature.append("</xsl:template>\n");
		WFSGetFeature.append("  <!-- Whenever you match any node or any attribute -->");
		WFSGetFeature.append("<xsl:template match=\"node()|@*\">\n");
		WFSGetFeature.append("<!-- Copy the current node -->\n");
		WFSGetFeature.append("<xsl:copy>\n");
		WFSGetFeature.append("<!-- Including any attributes it has and any child nodes -->\n");
		WFSGetFeature.append("<xsl:apply-templates select=\"@*|node()\"/>\n");
		WFSGetFeature.append("</xsl:copy>\n");
		WFSGetFeature.append("</xsl:template>\n");
		WFSGetFeature.append("</xsl:stylesheet>\n");
		return WFSGetFeature;
		// Fin de debug
	}

	// ***************************************************************************************************************************************

	// Applique le filtrage LocalFilter géométrique, il s'agit d'une
	// transformation
	public void filterFC(OutputStream os, String customFilter, GMLFeatureCollection doc, String urlServlet, String srsDest, int iServer) {

		try {

			// Debug de 15.06.2009
			// Log le timing avant transformation
			Date d = new Date();
			logger.info("LocalFilterBeginTransfoDateTime="+ dateFormat.format(d));
			// Fin de debug

			Filter filter = null;
			// Lecture du contenu de localFilter(ici le param
			// customFilter)**********************************
			if (customFilter != null) {
				InputStream is = new ByteArrayInputStream(customFilter.getBytes());

				DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
				DocumentBuilder db = dbf.newDocumentBuilder();
				Document dom = db.parse(is);
				// first grab a filter node
				NodeList nodes = dom.getElementsByTagName("Filter");
				if (nodes.getLength() == 0) {
					nodes = dom.getElementsByTagNameNS("http://www.opengis.net/ogc", "Filter");
				}
				for (int j = 0; j < nodes.getLength(); j++) {
					Element filterNode = (Element) nodes.item(j);
					NodeList list = filterNode.getChildNodes();
					Node child = null;

					for (int k = 0; k < list.getLength(); k++) {
						child = list.item(k);
						if ((child == null) || (child.getNodeType() != Node.ELEMENT_NODE)) {
							continue;
						}
						filter = FilterDOMParser.parseFilter(child);
					}
				}
			}

			// Application de
			// filter************************************************************
			FeatureTransformer ft = new FeatureTransformer();
			ft.setNamespaceDeclarationEnabled(true);

			ft.setCollectionPrefix(null);
			ft.setGmlPrefixing(true);
			ft.setIndentation(2);
			FeatureCollection fc = null;
			if (filter != null) {
				System.setProperty("org.geotools.referencing.forceXY", "true");
				// Transform the srs of the filter if needed.
				String srsSource = customFilter.substring(customFilter.indexOf("srsName"));
				if (srsSource.indexOf("\"") > 0) {
					srsSource = srsSource.substring(srsSource.indexOf("\"") + 1);
					srsSource = srsSource.substring(0, srsSource.indexOf("\""));
				} else {
					srsSource = srsSource.substring(srsSource.indexOf("\'") + 1);
					srsSource = srsSource.substring(0, srsSource.indexOf("\'"));
				}
				if (srsDest == null)
					srsDest = srsSource;

				CoordinateReferenceSystem sourceCRS = CRS.decode(srsSource);
				CoordinateReferenceSystem targetCRS = CRS.decode(srsDest);
				if (!srsSource.equals(srsDest)) {

					/*
					 * AffineTransform
					 * t=AffineTransform.getTranslateInstance(0,0);
					 * DefaultMathTransformFactory fac=new
					 * DefaultMathTransformFactory(); MathTransform mt =
					 * fac.createAffineTransform(new GeneralMatrix(t));
					 */

					FilterTransformer filterTransformer = new FilterTransformer(CRS.findMathTransform(sourceCRS, targetCRS));
					// Due to an issue in GeoTools
					// MinX MinY MaxX and MaxY are not set for the BBOXImpl.
					// Do it ourself
					if (filter instanceof org.geotools.filter.spatial.BBOXImpl) {
						Object obj = null;
						if (((org.geotools.filter.spatial.BBOXImpl) filter).getRightGeometry() instanceof org.geotools.filter.LiteralExpressionImpl) {
							obj = (((org.geotools.filter.LiteralExpressionImpl) ((org.geotools.filter.spatial.BBOXImpl) filter).getRightGeometry()).getValue());
						}
						if (((org.geotools.filter.spatial.BBOXImpl) filter).getLeftGeometry() instanceof org.geotools.filter.LiteralExpressionImpl) {
							obj = (((org.geotools.filter.LiteralExpressionImpl) ((org.geotools.filter.spatial.BBOXImpl) filter).getLeftGeometry()).getValue()
									.getClass());
						}
						if (obj != null && obj instanceof com.vividsolutions.jts.geom.Polygon) {
							((org.geotools.filter.spatial.BBOXImpl) filter)
							.setMinX(((com.vividsolutions.jts.geom.Polygon) obj).getEnvelopeInternal().getMinX());
							((org.geotools.filter.spatial.BBOXImpl) filter)
							.setMinY(((com.vividsolutions.jts.geom.Polygon) obj).getEnvelopeInternal().getMinY());
							((org.geotools.filter.spatial.BBOXImpl) filter)
							.setMaxY(((com.vividsolutions.jts.geom.Polygon) obj).getEnvelopeInternal().getMaxY());
							((org.geotools.filter.spatial.BBOXImpl) filter)
							.setMaxX(((com.vividsolutions.jts.geom.Polygon) obj).getEnvelopeInternal().getMaxX());
						}
					}
					Filter filter2 = (Filter) filter.accept(filterTransformer, null);
					// Si filter à srsSource (de LocalFilter) != srsDest (de
					// TempFile, la réponse du serveur courant) le contenu de
					// doc est modfié selon filter2
					fc = doc.subCollection(filter2);
				}
				// Si filter à srsSource (de LocalFilter) == srsDest (de
				// TempFile, la réponse du serveur courant) le contenu de doc
				// est modfié selon filter
				else {
					fc = doc.subCollection(filter);
				}
			}
			// filter est null, aucun changement n'est effectué sur le contenu
			// de doc (-> TempFile)
			else
				fc = doc;

			// Debug de 15.06.2009
			// Construction des pramètres de ft afin de préparer la sortie du
			// résultat filtré
			// -> option désactivée: renomage du prefix
			FeatureIterator it = fc.features();
			String lastTypeName = "";
			while (it.hasNext()) {
				Feature feature = it.next();

				if (!feature.getFeatureType().getTypeName().equals(lastTypeName)) {
					String prefix = policyServersPrefix.get(iServer);
					ft.getFeatureTypeNamespaces().declareNamespace(feature.getFeatureType(), prefix, feature.getFeatureType().getNamespace().toString());
					ft.setSrsName((String) feature.getDefaultGeometry().getUserData());
					lastTypeName = feature.getFeatureType().getTypeName();
				}
			}
			// Ecriture du résultat dans le fichier pointé par os
			ft.transform(fc, os);

			// Log le timing après transformation
			d = new Date();
			logger.info("LocalFilterEndTransfoDateTime="+ dateFormat.format(d));
			// Fin de debug

		} catch (Exception e) {
			e.printStackTrace();
			logger.error("FilterFC ERROR="+ e.getMessage());
		}

	}
	
	/**
     * Open a wfs GetFeature response file and load xsd schema correctly. See
     * remote-server-list element in config.xml for credentials.
     * 
     * @param file
     *            temporary response file to parse
     * @param username
     *            xsd schema provider server username.
     * @param password
     *            xsd schema provider server password.
     * @return
     */
    protected Object documentFactoryGetInstance(File file, String username, String password) {
	// File schema = new File("src/main/webapp/schemas/eai/eai.xsd");
	Map<String, Object> hints = new HashMap<String, Object>();
	Map<String, URI> namespaceMapping = new HashMap<String, URI>();
	hints.put(DocumentFactory.VALIDATION_HINT, false);
	hints.put(XMLHandlerHints.NAMESPACE_MAPPING, namespaceMapping);
	SAXBuilder sb = new SAXBuilder();
	org.jdom.Document doc;
	try {
	    doc = sb.build(file);
	    Namespace xsiNS = Namespace.getNamespace("http://www.w3.org/2001/XMLSchema-instance");
	    String schemaLocation = doc.getRootElement().getAttributeValue("schemaLocation", xsiNS);
	    String schemaParts[] = schemaLocation.split(" ");
	    for (int i = 0; i < schemaParts.length; i += 2) {
		String namespace = schemaParts[i];
		String schemaLocationURI = schemaParts[i + 1];
		if (SchemaFactory.getInstance(new URI(namespace)) == null) {
		    GetMethod get = new GetMethod(schemaLocationURI);
		    HttpClient client = new HttpClient();
		    if (username != null && password != null)
			client.getState().setCredentials(AuthScope.ANY, new UsernamePasswordCredentials(username, password));
		    client.executeMethod(get);
		    File tempSchemaFile = File.createTempFile("proxy", ".xsd");
		    FileWriter writer = new FileWriter(tempSchemaFile);
		    writer.write(get.getResponseBodyAsString());
		    writer.close();
		    namespaceMapping.put(namespace, tempSchemaFile.toURI());
		}
	    }
	    Object obj = DocumentFactory.getInstance(file.toURI(), hints, Level.WARNING);
	    return obj;

	} catch (Exception e) {
	    e.printStackTrace();
	}
	return null;
    }
    
    /**
     * @param url
     * @return
     */
    protected String getFeatureTypeRemoteFilter(String url, String ft) 
    {
    	if (ft == null)
		    return null;
	
    	//If all services are fully allowed, check if a spatial policy is defined on the policy
		if(sdiPolicy.isAnyservice())
			return sdiPolicy.getSdiWfsSpatialpolicy() == null ? null : sdiPolicy.getSdiWfsSpatialpolicy().getRemotegeographicfilter();
			
		//Get the feature type policy
		Set<SdiPhysicalservicePolicy> physicalservicePolicies = sdiPolicy.getSdiPhysicalservicePolicies();
    	Iterator<SdiPhysicalservicePolicy> i = physicalservicePolicies.iterator();
    	while(i.hasNext())
    	{
    		SdiPhysicalservicePolicy physicalservicePolicy = i.next();
    		if(physicalservicePolicy.getSdiPhysicalservice().getResourceurl().equals(url))
    		{
    			if(physicalservicePolicy.isAnyitem())
    				return physicalservicePolicy.getSdiWfsSpatialpolicy() == null ? null : physicalservicePolicy.getSdiWfsSpatialpolicy().getRemotegeographicfilter();
    			
    			Set<SdiFeaturetypePolicy> wfsFeatureTypePolicies = physicalservicePolicy.getSdiFeaturetypePolicies();
	    		Iterator<SdiFeaturetypePolicy> it = wfsFeatureTypePolicies.iterator();
	    		while (it.hasNext())
	    		{
	    			SdiFeaturetypePolicy featureTypePolicy = it.next();
	    			if(featureTypePolicy.getName().equals(ft) )
	    			{
	    				if(featureTypePolicy.isEnabled())
	    					return featureTypePolicy.getSdiWfsSpatialpolicy() == null ? null : featureTypePolicy.getSdiWfsSpatialpolicy().getRemotegeographicfilter();
	    				else
	    					return null;
	    			}
	    		}
	    		return null;
    		}
    	}
    	return null;
    }
    
    /**
     * Detects if the attribute of a feature type is allowed or not against the
     * rule.
     * 
     * @param ft
     *            The feature Type to test
     * @param url
     *            the url of the remote server.
     * @param isAllAttributes
     *            if the user request need all attributes.
     * @return true if the feature type is allowed, false if not
     */
    protected boolean isAttributeAllowed(String url, String ft, String attribute) 
    {
	
    	if (ft == null)
		    return false;
	
		if(sdiPolicy.isAnyservice())
			return true;
		
		boolean isServerFound = false;
		boolean isFeatureTypeFound = false;
		boolean FeatureTypeAllowed = false;
		boolean AttributeFound = false;
		
		Set<SdiPhysicalservicePolicy> physicalservicePolicies = sdiPolicy.getSdiPhysicalservicePolicies();
    	Iterator<SdiPhysicalservicePolicy> i = physicalservicePolicies.iterator();
    	while(i.hasNext())
    	{
    		SdiPhysicalservicePolicy physicalservicePolicy = i.next();
    		if(physicalservicePolicy.getSdiPhysicalservice().getResourceurl().equals(url))
    		{
    			isServerFound = true;
    			if(physicalservicePolicy.isAnyitem())
    			{
    				policyAttributeListNb = 0;
    				return true;
    			}
    				
    			FeatureTypeAllowed = physicalservicePolicy.isAnyitem();
    			Set<SdiFeaturetypePolicy> wfsFeatureTypePolicies = physicalservicePolicy.getSdiFeaturetypePolicies();
	    		Iterator<SdiFeaturetypePolicy> it = wfsFeatureTypePolicies.iterator();
	    		while (it.hasNext())
	    		{
	    			SdiFeaturetypePolicy featureTypePolicy = it.next();
	    			if(featureTypePolicy.getName().equalsIgnoreCase(ft)){
	    				isFeatureTypeFound = true;
		    			Set<SdiIncludedattribute> includedAttributes = featureTypePolicy.getSdiIncludedattributes();
		    			if(includedAttributes == null || includedAttributes.isEmpty())
		    			{
		    				policyAttributeListNb = 0;
		    				return true;
		    			}
		    			// Supprime les résultats, contenu dans la globale var, issus du précédent appel de la fonction courante
		    			policyAttributeListToKeepPerFT.clear();
		    			 
		    			for(SdiIncludedattribute includedattribute : includedAttributes){
		    				if(includedattribute.getName().equals(attribute))
		    					return true;
		    				// If no attributes are listed in user req -> all the Policy Attributes will be returned
		    			    else if (attribute.equals("")) {
		    			    	AttributeFound = true;
			    				String tmpFA = includedattribute.getName();
			    				policyAttributeListToKeepPerFT.add(tmpFA);
			    				// then at the end of function -> return false, "" is effectively not a valid attribute
		    			    }
		    			}
		    			if(AttributeFound){
			    			policyAttributeListNb = includedAttributes.size();
			    			return false;
		    			}
		    			else{
		    				return false;
		    			}
	    			}
	    		}
    		}
    	}
    	if (isServerFound && !isFeatureTypeFound && FeatureTypeAllowed)
    	{
    	    policyAttributeListNb = 0; 
    	    return true;
    	}
    	return false;
    }
    
    /**
     * @param url
     * @return
     */
    protected String getFeatureTypeLocalFilter(String url, String ft) {
    	if (ft == null)
		    return null;
    	
    	if(sdiPolicy.isAnyservice())
			return sdiPolicy.getSdiWfsSpatialpolicy() == null ? null : sdiPolicy.getSdiWfsSpatialpolicy().getLocalgeographicfilter();
		
		Set<SdiPhysicalservicePolicy> physicalservicePolicies = sdiPolicy.getSdiPhysicalservicePolicies();
    	Iterator<SdiPhysicalservicePolicy> i = physicalservicePolicies.iterator();
    	while(i.hasNext())
    	{
    		SdiPhysicalservicePolicy physicalservicePolicy = i.next();
    		if(physicalservicePolicy.getSdiPhysicalservice().getResourceurl().equals(url))
    		{
    			if(physicalservicePolicy.isAnyitem())
    				return physicalservicePolicy.getSdiWfsSpatialpolicy() == null ? null : physicalservicePolicy.getSdiWfsSpatialpolicy().getLocalgeographicfilter();
    			
    			Set<SdiFeaturetypePolicy> wfsFeatureTypePolicies = physicalservicePolicy.getSdiFeaturetypePolicies();
	    		Iterator<SdiFeaturetypePolicy> it = wfsFeatureTypePolicies.iterator();
	    		while (it.hasNext())
	    		{
	    			SdiFeaturetypePolicy featureTypePolicy = it.next();
	    			if(featureTypePolicy.getName().equals(ft))
	    			{
	    				if(featureTypePolicy.isEnabled())
	    					return featureTypePolicy.getSdiWfsSpatialpolicy()== null ? null : featureTypePolicy.getSdiWfsSpatialpolicy().getLocalgeographicfilter();
	    				else
	    					return null;
	    			}
	    		}
	    		return null;
    		}
    	}
    	return null;
	}
    
    /**
     * @param url
     * @return
     */
    protected String getServerNamespace(String url) {
    	Set<SdiPhysicalservicePolicy> physicalservicePolicies = sdiPolicy.getSdiPhysicalservicePolicies();
    	Iterator<SdiPhysicalservicePolicy> i = physicalservicePolicies.iterator();
    	while(i.hasNext())
    	{
    		return i.next().getNamespace();
    	}
    	return null;
    }
    
    /**
     * @param url
     * @return
     */
    protected String getServerPrefix(String url) {
    	Set<SdiPhysicalservicePolicy> physicalservicePolicies = sdiPolicy.getSdiPhysicalservicePolicies();
    	Iterator<SdiPhysicalservicePolicy> i = physicalservicePolicies.iterator();
    	while(i.hasNext())
    	{
    		return i.next().getPrefix();
    	}
    	return null;
    }
    
    /**
     * Aggregate exception files from remote servers in one single file
     * The search for tags <ServiceExceptionReport> and <ServiceException> is valid for
     * WMS version 1.1, 1.3
     * WFS version 1.0
     * 
     *  WFS version 1.1 uses tags <ExceptionReport> and subTag <Exception>
     */
    @Deprecated
    protected ByteArrayOutputStream buildResponseForOgcServiceException ()
    {
	try 
	{
	    for (String path : ogcExceptionFilePathList.values()) 
	    {
		DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
		db.setNamespaceAware(false);
		File fMaster = new File(path);
		org.w3c.dom.Document documentMaster = db.newDocumentBuilder().parse(fMaster);
		if (documentMaster != null) 
		{
		    NodeList nl = documentMaster.getElementsByTagName("ServiceExceptionReport");
		    if (nl.item(0) != null)
		    {
			logger.trace("transform begin exception response writting");
			DOMImplementationLS implLS = null;
			if (documentMaster.getImplementation().hasFeature("LS", "3.0")) 
			{
			    implLS = (DOMImplementationLS) documentMaster.getImplementation();
			} 
			else 
			{
			    DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
			    implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
			}

			Node ItemMaster = nl.item(0);
			//Loop on other file
			for (String pathChild : ogcExceptionFilePathList.values()) 
			{
			    org.w3c.dom.Document documentChild = null;
			    if(path.equals(pathChild))
				continue;

			    documentChild = db.newDocumentBuilder().parse(pathChild);

			    if (documentChild != null) {
				NodeList nlChild = documentChild.getElementsByTagName("ServiceException");
				if (nlChild != null && nlChild.getLength() > 0) 
				{
				    for (int i = 0; i < nlChild.getLength(); i++) 
				    {
					Node nnode = nlChild.item(i);
					nnode = documentMaster.importNode(nnode, true);
					ItemMaster.appendChild(nnode);
				    }
				}
			    }
			}
			ByteArrayOutputStream out = new ByteArrayOutputStream();
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			sortie.setByteStream(out);
			serialiseur.write(documentMaster, sortie);
			logger.trace("transform end exception response writting");
			return out;
		    }	
		}
	    }
	}
	catch (Exception ex)
	{
	    ex.printStackTrace();
	    logger.error(ex.getMessage());
	    return null;
	}
	return null;
    }
    
    /**
     * Detects if the feature type is allowed or not against the rule.
     * 
     * @param ft
     *            The feature Type to test
     * @param url
     *            the url of the remote server.
     * @return true if the feature type is allowed, false if not
     */
    protected boolean isFeatureTypeAllowed(String ft, String url) 
    {
    	Set<SdiPhysicalservicePolicy> physicalservicePolicies = sdiPolicy.getSdiPhysicalservicePolicies();
    	Iterator<SdiPhysicalservicePolicy> i = physicalservicePolicies.iterator();
    	while(i.hasNext())
    	{
    		SdiPhysicalservicePolicy physicalservicePolicy = i.next();
    		if(physicalservicePolicy.getSdiPhysicalservice().getResourceurl().equals(url))
    		{
	    		Set<SdiFeaturetypePolicy> featureTypePolicies = physicalservicePolicy.getSdiFeaturetypePolicies();
	    		Iterator<SdiFeaturetypePolicy> it = featureTypePolicies.iterator();
	    		while (it.hasNext())
	    		{
	    			SdiFeaturetypePolicy featureTypePolicy = it.next();
	    			
	    			if(featureTypePolicy.getName().equals(ft))
	    			{
	    				//The feature type exists in a physical service.
	    				//It is allowed if all feature types are allowed by this policy for this physical service
	    				if(physicalservicePolicy.isAnyitem() || sdiPolicy.isAnyservice())
	    					return true;
	    				//Or if this feature type is explicitly set as allowed
	    				if(featureTypePolicy.isEnabled())
	    					return true;
	    				
	    				//Else, the feature is not allowed
	    				return false;
	    			}
	    		}
    		}
    	}
    	return false;
    }
    
    /**
     * Detects if the feature type is allowed :
     * - by being specifically allowed in the policy
     * - by default, when <Servers All = true> or <FeatureTypes All=true> 
     * 
     * @param ft The feature type to test
     * @return true if the layer is allowed, false if not
     */
    protected boolean isFeatureTypeAllowed(String ft) 
    {
    	Set<SdiPhysicalservicePolicy> physicalservicePolicies = sdiPolicy.getSdiPhysicalservicePolicies();
    	Iterator<SdiPhysicalservicePolicy> i = physicalservicePolicies.iterator();
    	while(i.hasNext())
    	{
    		SdiPhysicalservicePolicy physicalservicePolicy = i.next();
    		Set<SdiFeaturetypePolicy> featureTypePolicies = physicalservicePolicy.getSdiFeaturetypePolicies();
    		Iterator<SdiFeaturetypePolicy> it = featureTypePolicies.iterator();
    		while (it.hasNext())
    		{
    			SdiFeaturetypePolicy featureTypePolicy = it.next();
    			if(featureTypePolicy.getName().equals(ft))
    			{
    				//The feature type exists in a physical service.
    				//It is allowed if all feature types are allowed by this policy for this physical service
    				if(physicalservicePolicy.isAnyitem() || sdiPolicy.isAnyservice())
    					return true;
    				//Or if this feature type is explicitly set as allowed
    				if(featureTypePolicy.isEnabled())
    					return true;
    				
    				//Else, the feature is not allowed
    				return false;
    			}	
    		}
    	}
    	//The feature type doesn't exist in all the physical services referenced by the policy, so return false.
    	return false;
    }
}