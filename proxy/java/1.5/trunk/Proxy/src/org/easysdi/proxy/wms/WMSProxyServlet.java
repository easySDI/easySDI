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
package org.easysdi.proxy.wms;

import java.awt.AlphaComposite;
import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.Transparency;
import java.awt.geom.Rectangle2D;
import java.awt.image.BufferedImage;
import java.io.BufferedOutputStream;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.PrintWriter;
import java.io.UnsupportedEncodingException;
import java.net.URI;
import java.net.URLDecoder;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;
import java.util.UUID;
import java.util.Vector;
import java.util.logging.Level;

import javax.imageio.ImageIO;
import javax.imageio.ImageWriter;
import javax.imageio.stream.FileImageOutputStream;
import javax.imageio.stream.MemoryCacheImageOutputStream;
import javax.naming.NoPermissionException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
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

import org.apache.commons.collections.functors.WhileClosure;
import org.apache.xerces.dom.DeferredElementImpl;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.csw.CSWProxyMetadataConstraintFilter;
import org.easysdi.proxy.exception.AvailabilityPeriodException;
import org.easysdi.proxy.policy.BoundingBox;
import org.easysdi.proxy.policy.Layer;
import org.easysdi.proxy.policy.Operation;
import org.easysdi.proxy.policy.Server;
import org.easysdi.proxy.policy.Servers;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.easysdi.xml.resolver.ResourceResolver;
import org.geotools.data.ows.CRSEnvelope;
import org.geotools.data.wms.xml.WMSSchema;
import org.geotools.feature.AttributeType;
import org.geotools.feature.AttributeTypeFactory;
import org.geotools.feature.Feature;
import org.geotools.feature.FeatureCollection;
import org.geotools.feature.FeatureType;
import org.geotools.feature.FeatureTypes;
import org.geotools.feature.GeometryAttributeType;
import org.geotools.feature.type.GeometricAttributeType;
import org.geotools.geometry.jts.JTS;
import org.geotools.geometry.jts.ReferencedEnvelope;
import org.geotools.referencing.CRS;
import org.geotools.referencing.NamedIdentifier;
import org.geotools.renderer.lite.RendererUtilities;
import org.geotools.xml.DocumentFactory;
import org.geotools.xml.handlers.DocumentHandler;
import org.integratedmodelling.geospace.gis.FeatureRasterizer;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.opengis.geometry.MismatchedDimensionException;
import org.opengis.referencing.FactoryException;
import org.opengis.referencing.NoSuchAuthorityCodeException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.operation.MathTransform;
import org.opengis.referencing.operation.TransformException;
import org.springframework.security.core.context.SecurityContextHolder;
import org.w3c.dom.Attr;
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

import com.vividsolutions.jts.geom.Coordinate;
import com.vividsolutions.jts.geom.Envelope;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.geom.GeometryFactory;
import com.vividsolutions.jts.geom.IntersectionMatrix;
import com.vividsolutions.jts.geom.Polygon;
import com.vividsolutions.jts.io.WKTReader;


/**
 * If no xslt is found in the path, generate the default one that will change
 * the IP address and remove the wrong operation
 * 
 * @author rmi
 */
public class WMSProxyServlet extends ProxyServlet {

	// ***************************************************************************************************************************************
	//Store all the possible operations for a WMS service
	//Used in buildCapabilitiesXSLT()
	private String[] WMSOperation = { "GetCapabilities", "GetMap", "GetFeatureInfo", "DescribeLayer", "GetLegendGraphic", "PutStyles", "GetStyles" };
//	//Store operations supported by the current version of the proxy
//	//Update this list to reflect proxy's capabilities
//	private static final List<String> WMSSupportedOperations = Arrays.asList("GetCapabilities", "GetMap", "GetFeatureInfo", "GetLegendGraphic");
	// Debug tb 08.07.2009
	private Map<Integer, String> serverUrlPerfilePathList = new TreeMap<Integer, String>(); // Url
	// du
	// serveur
	// ayant
	// renvoyé
	// la
	// réponse
	// i.
	// private Vector<String> filterPerFilePathList = new Vector<String>(); //
	// Filtre du groupe de layers ayant renvoyé la réponse i.
	// Fin de Debug
	private String layers;
	private String styles;
	private static DocumentBuilder builder = null;
	
	static {
		DocumentBuilderFactory domFactory = DocumentBuilderFactory.newInstance();
		domFactory.setNamespaceAware(true);
		try {
			builder = domFactory.newDocumentBuilder();
		} catch (ParserConfigurationException e) {
			e.printStackTrace();
		}
	}

	/**
	 * Constructor
	 */
	public WMSProxyServlet ()
	{
		super();
		ServiceSupportedOperations = Arrays.asList("GetCapabilities", "GetMap", "GetFeatureInfo", "GetLegendGraphic");
	}
	
	protected StringBuffer generateOgcError(String errorMessage, String code, String locator, String version) {
		dump("ERROR", errorMessage);
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>");
		sb.append("<ServiceExceptionReport xmlns=\"http://www.opengis.net/ogc\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengis.net/ogc\" version=\"");
//		sb.append("<ServiceExceptionReport version=\"");
		sb.append(version);
		sb.append("\">\n");
		sb.append("\t<ServiceException ");
		if(code != null && code != "")
		{
			sb.append(" code=\"");
			sb.append(code);
			sb.append("\"");
		}
		if(locator != null && locator != "" && version.equals("1.3.0"))
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

	protected StringBuffer buildCapabilitiesXSLT(HttpServletRequest req, HttpServletResponse resp, int remoteServerIndex, String version) {

		try {
			String user = "";
			if (SecurityContextHolder.getContext().getAuthentication() != null) {
				user = SecurityContextHolder.getContext().getAuthentication().getName();
			}

			String url = getServletUrl(req);
			
			//Retrieve allowed and denied operations from the policy
			List<String> permitedOperations = new Vector<String>();
			List<String> deniedOperations = new Vector<String>();
			for (int i = 0; i < WMSOperation.length; i++) 
			{
				if (ServiceSupportedOperations.contains(WMSOperation[i]) && isOperationAllowed(WMSOperation[i])) 
				{
					permitedOperations.add(WMSOperation[i]);
					dump(WMSOperation[i] + " is permitted");
				} else 
				{
					deniedOperations.add(WMSOperation[i]);
					dump(WMSOperation[i] + " is denied");
				}
			}
			
			try {
				StringBuffer WMSCapabilities111 = new StringBuffer();

				WMSCapabilities111
						.append("<xsl:stylesheet version=\"1.00\" xmlns=\"http://www.opengis.net/wms\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
				WMSCapabilities111.append("<xsl:output method=\"xml\" omit-xml-declaration=\"no\" version=\"1.0\" encoding=\"UTF-8\" indent=\"yes\"/>");
				// Debug tb 19.11.2009
				if (!"100".equalsIgnoreCase(version)) {
					WMSCapabilities111.append("<xsl:template match=\"OnlineResource/@xlink:href\">");
					WMSCapabilities111.append("<xsl:param name=\"thisValue\">");
					WMSCapabilities111.append("<xsl:value-of select=\".\"/>");
					WMSCapabilities111.append("</xsl:param>");
					WMSCapabilities111.append("<xsl:attribute name=\"xlink:href\">");
					WMSCapabilities111.append(url);
					// Changer seulement la partie racine de l'URL, pas les
					// param après '?'
					WMSCapabilities111.append("<xsl:value-of select=\"substring-after($thisValue,'" + getRemoteServerUrl(remoteServerIndex) + "')\"/>");
					WMSCapabilities111.append("</xsl:attribute>");
					WMSCapabilities111.append("</xsl:template>");
				} else {
					// Add change on wmtver=1.0.0&request=capabilities support
					StringBuffer WMSCapabilities100 = new StringBuffer();
					WMSCapabilities100.append("<xsl:template match=\"OnlineResource\">");
					WMSCapabilities100.append("<xsl:param name=\"thisValue\">");
					WMSCapabilities100.append("<xsl:value-of select=\".\"/>");
					WMSCapabilities100.append("</xsl:param>");
					WMSCapabilities100.append("<OnlineResource>");
					WMSCapabilities100.append(url);
					// Changer seulement la partie racine de l'URL, pas les
					// param après '?'
					WMSCapabilities100.append("<xsl:value-of select=\"substring-after($thisValue,'" + getRemoteServerUrl(remoteServerIndex) + "')\"/>");
					WMSCapabilities100.append("</OnlineResource>");
					WMSCapabilities100.append("</xsl:template>");
					WMSCapabilities100.append("<xsl:template match=\"@onlineResource\">");
					WMSCapabilities100.append("<xsl:param name=\"thisValue\">");
					WMSCapabilities100.append("<xsl:value-of select=\".\"/>");
					WMSCapabilities100.append("</xsl:param>");
					WMSCapabilities100.append("<xsl:attribute name=\"onlineResource\">");
					WMSCapabilities100.append(url);
					// Changer seulement la partie racine de l'URL, pas les
					// param après '?'
					WMSCapabilities100.append("<xsl:value-of select=\"substring-after($thisValue,'" + getRemoteServerUrl(remoteServerIndex) + "')\"/>");
					WMSCapabilities100.append("</xsl:attribute>");
					WMSCapabilities100.append("</xsl:template>");
					WMSCapabilities111.append(WMSCapabilities100);
				}
				// Fin de Debug

				// Filtrage xsl des opérations
				//HVH-30.08.2010 : replace old version which did not work				
				if (hasPolicy) {
					if (!policy.getOperations().isAll() || deniedOperations.size() > 0 ) {
						Iterator<String> it = permitedOperations.iterator();
						while (it.hasNext()) {
							String text = it.next();
							if (text != null) {
								WMSCapabilities111.append("<xsl:template match=\"Capability/Request/");
								WMSCapabilities111.append(text);
								WMSCapabilities111.append("\">");
								WMSCapabilities111.append("<!-- Copy the current node -->");
								WMSCapabilities111.append("<xsl:copy>");
								WMSCapabilities111.append("<!-- Including any attributes it has and any child nodes -->");
								WMSCapabilities111.append("<xsl:apply-templates select=\"@*|node()\"/>");
								WMSCapabilities111.append("</xsl:copy>");
								WMSCapabilities111.append("</xsl:template>");
							}
						}

						it = deniedOperations.iterator();
						while (it.hasNext()) {
							WMSCapabilities111.append("<xsl:template match=\"Capability/Request/");
							WMSCapabilities111.append(it.next());
							WMSCapabilities111.append("\"></xsl:template>");
						}
					}
					if (permitedOperations.size() == 0 )
					{
						WMSCapabilities111.append("<xsl:template match=\"Capability/Request/\"></xsl:template>");
					}
				}
				else
				{
					WMSCapabilities111.append("<xsl:template match=\"Capability/Request/\"></xsl:template>");
				}
				
				Map hints = new HashMap();
				// hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);
				hints.put(DocumentHandler.DEFAULT_NAMESPACE_HINT_KEY, WMSSchema.getInstance());
				hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);

				// WMSCapabilities capa = (WMSCapabilities)
				// DocumentFactory.getInstance(new
				// File(wmsFilePathList.get(remoteServerIndex).toArray()[0].toString()).toURI(),
				// hints, Level.SEVERE);
				Document doc = builder.parse(wmsFilePathList.get(remoteServerIndex).toArray()[0].toString());
				XPath xpath = XPathFactory.newInstance().newXPath();
				// XPath Query for showing all nodes value
				XPathExpression expr = xpath.compile("//Layer/Name");
				Object result = expr.evaluate(doc, XPathConstants.NODESET);
				NodeList nodes = (NodeList) result;

				// Filtrage xsl des layers
				
				for (int i = 0; i < nodes.getLength(); i++) {
					Node l = nodes.item(i);
					boolean allowed = isLayerAllowed(l.getTextContent(), getRemoteServerUrl(remoteServerIndex));
					if (!allowed) {
						// Si couche pas permise alors on l'enlève
						WMSCapabilities111.append("<xsl:template match=\"//Layer[starts-with(Name,'" + l.getTextContent() + "')]");
						WMSCapabilities111.append("\"></xsl:template>");
					}
				}
//				WMSCapabilities capa = (WMSCapabilities) DocumentFactory.getInstance(new File(wmsFilePathList.get(remoteServerIndex).toArray(new String[1])[0])
//				.toURI(), hints, Level.WARNING);

//				Iterator<Layer> itLayer = capa.getLayerList().iterator();
//				while (itLayer.hasNext()) {
//					Layer l = (Layer) itLayer.next();
//					// Debug tb 03.07.2009
//					// String tmpFT = l.getName();
//					boolean allowed = isLayerAllowed(l.getName(), getRemoteServerUrl(remoteServerIndex));
//					if (!allowed)
//					// Fin de Debug
//					// if (!isLayerAllowed(l.getName(),
//					// getRemoteServerUrl(remoteServerIndex)))
//					{
//						// Si couche pas permise alors on l'enlève
//						WMSCapabilities111.append("<xsl:template match=\"//Layer[starts-with(Name,'" + l.getName() + "')]");
//						WMSCapabilities111.append("\"></xsl:template>");
//					}
//				}
				

				// Debug tb 03.07.2009
				// -> le prefix est déjà intégré dans l.getName!
				// //Add the WMSxx_ Prefix before the name of the layer.
				// //This prefix will be used to find to witch remote server the
				// layer belongs.
				// if
				// (getRemoteServerInfo(remoteServerIndex).getPrefix().length()>0)
				// {
				// WMSCapabilities111.append("<xsl:template match=\"//Layer/Name\">");
				// WMSCapabilities111.append("<Name>"+getRemoteServerInfo(remoteServerIndex).getPrefix()+"<xsl:value-of select=\".\"/> </Name>");
				// WMSCapabilities111.append("</xsl:template>");
				// }
				// Fin de Debug

				WMSCapabilities111.append("  <!-- Whenever you match any node or any attribute -->");
				WMSCapabilities111.append("<xsl:template match=\"node()|@*\">");
				WMSCapabilities111.append("<!-- Copy the current node -->");
				WMSCapabilities111.append("<xsl:copy>");
				WMSCapabilities111.append("<!-- Including any attributes it has and any child nodes -->");
				WMSCapabilities111.append("<xsl:apply-templates select=\"@*|node()\"/>");
				WMSCapabilities111.append("</xsl:copy>");
				WMSCapabilities111.append("</xsl:template>");
				WMSCapabilities111.append("</xsl:stylesheet>");

				return WMSCapabilities111;
			} catch (Exception e) {
				resp.setHeader("easysdi-proxy-error-occured", "true");
				e.printStackTrace();
				dump("ERROR", e.getMessage());
			}

			// If something goes wrong, an empty stylesheet is returned.
			StringBuffer sb = new StringBuffer();
			return sb
					.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}

		// If something goes wrong, an empty stylesheet is returned.
		StringBuffer sb = new StringBuffer();
		return sb
				.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
	}

	protected StringBuffer buildServiceMetadataCapabilitiesXSLT( String version) 
	{
		try
		{
			StringBuffer serviceMetadataXSLT = new StringBuffer();
			serviceMetadataXSLT.append("<xsl:stylesheet version=\"1.00\" xmlns=\"http://www.opengis.net/wms\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
			serviceMetadataXSLT.append("<xsl:output method=\"xml\" omit-xml-declaration=\"no\" version=\"1.0\" encoding=\"UTF-8\" indent=\"yes\"/>");
			serviceMetadataXSLT.append("<xsl:strip-space elements=\"*\" />");
			serviceMetadataXSLT.append("<xsl:template match=\"node()|@*\">");
			serviceMetadataXSLT.append("<!-- Copy the current node -->");
			serviceMetadataXSLT.append("<xsl:copy>");
			serviceMetadataXSLT.append("<!-- Including any attributes it has and any child nodes -->");
			serviceMetadataXSLT.append("<xsl:apply-templates select=\"@*|node()\"/>");
			serviceMetadataXSLT.append("</xsl:copy>");
			serviceMetadataXSLT.append("</xsl:template>");
			//Version 1.2 and 1.3 supported so apply xslt to rewrite service metadata
			serviceMetadataXSLT.append("<xsl:template match=\"Service\">");
			serviceMetadataXSLT.append("<xsl:copy>");
			//Name
			serviceMetadataXSLT.append("<xsl:element name=\"Name\"> ");
			serviceMetadataXSLT.append("<xsl:text>WMS</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
			//Title
			serviceMetadataXSLT.append("<xsl:element name=\"Title\"> ");
			serviceMetadataXSLT.append("<xsl:text>" + getConfiguration().getTitle() + "</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
			//Abstract
			if(getConfiguration().getAbst()!=null)
			{
				serviceMetadataXSLT.append("<xsl:element name=\"Abstract\"> ");
				serviceMetadataXSLT.append("<xsl:text>" + getConfiguration().getAbst() + "</xsl:text>");
				serviceMetadataXSLT.append("</xsl:element>");
			}
			//Keyword
			if(getConfiguration().getKeywordList()!= null)
			{
				if("100".equals(version))
				{
					List<String> keywords = getConfiguration().getKeywordList();
					serviceMetadataXSLT.append("<xsl:element name=\"Keywords\"> ");
					String sKeyWords = new String() ;
					for (int n = 0; n < keywords.size(); n++) {
						sKeyWords+= keywords.get(n);
						if(n != keywords.size()-1)
						{
							sKeyWords += ", ";
						}
					}
					serviceMetadataXSLT.append("<xsl:text>" + sKeyWords + "</xsl:text>");
					serviceMetadataXSLT.append("</xsl:element>");
				}
				else
				{
					List<String> keywords = getConfiguration().getKeywordList();
					serviceMetadataXSLT.append("<xsl:element name=\"KeywordList\"> ");
					for (int n = 0; n < keywords.size(); n++) {
						serviceMetadataXSLT.append("<xsl:element name=\"Keyword\"> ");
						serviceMetadataXSLT.append("<xsl:text>" + keywords.get(n) + "</xsl:text>");
						serviceMetadataXSLT.append("</xsl:element>");
					}
					serviceMetadataXSLT.append("</xsl:element>");
				}
			}
			//OnlineResource
			serviceMetadataXSLT.append("<xsl:copy-of select=\"OnlineResource\"/>");
			//contactInfo
			if(!"100".equals(version))
			{
				if(getConfiguration().getContactInfo()!= null && !getConfiguration().getContactInfo().isEmpty())
				{
					serviceMetadataXSLT.append("<xsl:element name=\"ContactInformation\"> ");
					
						serviceMetadataXSLT.append("<xsl:element name=\"ContactPersonPrimary\"> ");
						if(configuration.getContactInfo().getName()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\"ContactPerson\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getName() + "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getOrganization()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\"ContactOrganization\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getOrganization()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						serviceMetadataXSLT.append("</xsl:element>");
						if(configuration.getContactInfo().getPosition()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\"ContactPosition\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getPosition()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if (!configuration.getContactInfo().getContactAddress().isEmpty())
						{
							serviceMetadataXSLT.append("<xsl:element name=\"ContactAddress\"> ");
							if(configuration.getContactInfo().getContactAddress().getType()!=null){
								serviceMetadataXSLT.append("<xsl:element name=\"AddressType\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getType()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							if(configuration.getContactInfo().getContactAddress().getAddress()!=null){
								serviceMetadataXSLT.append("<xsl:element name=\"Address\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getAddress()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							if(!configuration.getContactInfo().getContactAddress().getCity().equals("")){
								serviceMetadataXSLT.append("<xsl:element name=\"City\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getCity()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							if(configuration.getContactInfo().getContactAddress().getState()!=null){
								serviceMetadataXSLT.append("<xsl:element name=\"StateOrProvince\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getState()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							if(configuration.getContactInfo().getContactAddress().getPostalCode()!=null){
								serviceMetadataXSLT.append("<xsl:element name=\"PostCode\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getPostalCode()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							if(configuration.getContactInfo().getContactAddress().getCountry()!=null){
								serviceMetadataXSLT.append("<xsl:element name=\"Country\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getCountry()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getVoicePhone()!=null)
						{
							serviceMetadataXSLT.append("<xsl:element name=\"ContactVoiceTelephone\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getVoicePhone()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getFacSimile()!=null)
						{
							serviceMetadataXSLT.append("<xsl:element name=\"ContactFacsimileTelephone\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getFacSimile()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().geteMail()!=null)
						{
							serviceMetadataXSLT.append("<xsl:element name=\"ContactElectronicMailAddress\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().geteMail()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
					
					serviceMetadataXSLT.append("</xsl:element>");
				}
			}
			//Fees
			serviceMetadataXSLT.append("<xsl:element name=\"Fees\"> ");
			serviceMetadataXSLT.append("<xsl:text>" + getConfiguration().getFees() + "</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
			//AccesConstraints
			serviceMetadataXSLT.append("<xsl:element name=\"AccessConstraints\"> ");
			serviceMetadataXSLT.append("<xsl:text>" + getConfiguration().getAccessConstraints() + "</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
			
			
			serviceMetadataXSLT.append("</xsl:copy>");
			serviceMetadataXSLT.append("</xsl:template>");
		
			serviceMetadataXSLT.append("</xsl:stylesheet>");	
			return serviceMetadataXSLT;
		}
		catch (Exception ex )
		{
			ex.printStackTrace();
			dump("ERROR", ex.getMessage());
			// If something goes wrong, an empty stylesheet is returned.
			StringBuffer sb = new StringBuffer();
			return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
		}
	}
	// ***************************************************************************************************************************************

	
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
						dump("transform begin exception response writting");
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
						dump("transform end exception response writting");
						return out;
					}	
				}
			}
		}
		catch (Exception ex)
		{
			ex.printStackTrace();
			dump("ERROR", ex.getMessage());
			return null;
		}
		return null;
	}
	
	protected boolean filterServersResponsesForOgcServiceExceptionFiles ()
	{
		try
		{
			dump("DEBUG","filterServerResponseFile begin");
			int end = wmsFilePathList.size();
			for (int iFilePath = 0; iFilePath < end; iFilePath++) 
			{
				String path = wmsFilePathList.get(iFilePath).toArray(new String[1])[0];
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
							ogcExceptionFilePathList.put(iFilePath, path);
							wmsFilePathList.remove(iFilePath, path);
						}
					}
				}
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
//	private void calculateLatLonBBOX ()
//	{
//		//create the parser with the gml 2.0 configuration
//		org.geotools.xml.Configuration configuration = new org.geotools.gml2.GMLConfiguration();
//		org.geotools.xml.Parser parser = new org.geotools.xml.Parser( configuration );
//
//		//the xml instance document above
//		InputStream xml = null;
//		try {
//			xml = new ByteArrayInputStream(policy.getServers().getServer().get(0).getLayers().getLayer().get(0).getFilter().getContent().getBytes("UTF-8"));
//		} catch (UnsupportedEncodingException e) {
//			// TODO Auto-generated catch block
//			e.printStackTrace();
//		}
//	
//		//parse
//		try 
//		{
//			Geometry obj =  (Geometry)parser.parse( xml );
//			Geometry bbox = obj.getEnvelope();
//			if (bbox.getGeometryType().equalsIgnoreCase("polygon"))
//			{
//				CoordinateReferenceSystem sourceCRS;
//				try {
////					((DefaultReference)bbox.getUserData())
//					sourceCRS = CRS.decode("EPSG:"+String.valueOf(bbox.getSRID()));
//					CoordinateReferenceSystem targetCRS = CRS.decode("EPSG:4326");
//					MathTransform transform = CRS.findMathTransform(sourceCRS, targetCRS);
//					Geometry targetGeometry = JTS.transform( bbox, transform);
//				 
//					System.out.println((bbox.getEnvelopeInternal()).getMinX());
//					System.out.println((targetGeometry.getEnvelopeInternal()).getMinX());
//				
//				} catch (NoSuchAuthorityCodeException e) {
//					// TODO Auto-generated catch block
//					e.printStackTrace();
//				} catch (FactoryException e) {
//					// TODO Auto-generated catch block
//					e.printStackTrace();
//				}
//				catch (MismatchedDimensionException e) {
//					// TODO Auto-generated catch block
//					e.printStackTrace();
//				} catch (TransformException e) {
//					// TODO Auto-generated catch block
//					e.printStackTrace();
//				}
//			}
//			
////			FeatureCollection fc = (FeatureCollection) parser.parse( xml );
//		} catch (IOException e) {
//			// TODO Auto-generated catch block
//			e.printStackTrace();
//		} catch (SAXException e) {
//			// TODO Auto-generated catch block
//			e.printStackTrace();
//		} catch (ParserConfigurationException e) {
//			// TODO Auto-generated catch block
//			e.printStackTrace();
//		}
////		for ( Iterator i = fc.iterator(); i.hasNext(); ) {
////		  Feature f = (Feature) i.next();
////
////		  Point point = (Point) f.getDefaultGeometry();
////		  String name = (String) f.getAttribute( "name" );
////		}
//	}
	
	private void rewriteBBOX(List<org.jdom.Element> layersList,CoordinateReferenceSystem wgsCRS, String version)
	{
		String wgsMaxx;
		String wgsMaxy;
		String wgsMinx;
		String wgsMiny;
		String srsAttributeName = "SRS";
		if(version.equalsIgnoreCase("1.3.0"))
		{
			srsAttributeName = "CRS";
		}
		
		
		for(int l = 0 ; l < layersList.size();l++)
		{
    		org.jdom.Element elementLayer = (org.jdom.Element)layersList.get(l);
    		
    		List<Server> serverList = policy.getServers().getServer();
    		List listName = elementLayer.getChildren("Name");
    		if(listName.size() == 0)
    		{
    			continue;
    		}
    		for (int i=0 ; i < serverList.size() ; i++)
    		{
    			try
    			{
	    			Server server = serverList.get(i);
	    			Layer currentLayer = server.getLayers().getLayerByName(((org.jdom.Element)elementLayer.getChildren("Name").get(0)).getValue());
	    			if(currentLayer == null)
	    			{
	    				continue;
	    			}
	    			if(currentLayer.getBoundingBox() == null)
	    			{
	    				continue;
	    			}
	    			
	    			//Calculate WGS BBOX
	    			BoundingBox srsBBOX =  currentLayer.getBoundingBox();
	    			if(srsBBOX.getSRS().equalsIgnoreCase("EPSG:4326"))
	    			{
	    				wgsMaxx = (srsBBOX.getMaxx());
	    				wgsMaxy = (srsBBOX.getMaxy());
	    				wgsMinx = (srsBBOX.getMinx());
	    				wgsMiny = (srsBBOX.getMiny());
	    			}	
	    			else
	    			{
	    				CoordinateReferenceSystem sourceCRS = CRS.decode(srsBBOX.getSRS());
	    				MathTransform transform = CRS.findMathTransform(sourceCRS, wgsCRS);
	    				Envelope sourceEnvelope = new Envelope(Double.valueOf(srsBBOX.getMinx()),Double.valueOf(srsBBOX.getMaxx()),Double.valueOf(srsBBOX.getMiny()),Double.valueOf(srsBBOX.getMaxy()));
	    				Envelope targetEnvelope = JTS.transform( sourceEnvelope, transform);
	    				wgsMaxx = (String.valueOf(targetEnvelope.getMaxX()));
	    				wgsMaxy = (String.valueOf(targetEnvelope.getMaxY()));
	    				wgsMinx = (String.valueOf(targetEnvelope.getMinX()));
	    				wgsMiny = (String.valueOf(targetEnvelope.getMinY()));
	    			}
	    			
	    			//LatLongBoundingBox
	    			if(version.equalsIgnoreCase("1.3.0"))
	    			{
	    				writeLatLonBBOX130(elementLayer, wgsMinx, wgsMiny,wgsMaxx, wgsMaxy);
	    			}
	    			else
	    			{
	    				writeLatLonBBOX111(elementLayer, wgsMinx, wgsMiny,wgsMaxx, wgsMaxy);
	    			}

	        		//Srs Boundingbox
	    			List srsBBOXElements = elementLayer.getChildren("BoundingBox");
	    			for (int j = 0 ; j < srsBBOXElements.size() ; j++)
	    			{
	    				org.jdom.Element BBOX = (org.jdom.Element) srsBBOXElements.get(j);
	    				if(BBOX.getAttributeValue(srsAttributeName).equals(srsBBOX.getSRS()))
	    				{
	    					BBOX.setAttribute("minx", srsBBOX.getMinx());
		    				BBOX.setAttribute("miny", srsBBOX.getMiny());
		    				BBOX.setAttribute("maxx", srsBBOX.getMaxx());
		    				BBOX.setAttribute("maxy", srsBBOX.getMaxy());
	    				}
	    				else
	    				{
		    				CoordinateReferenceSystem targetCRS =CRS.decode(BBOX.getAttributeValue(srsAttributeName));
		    				MathTransform transform = CRS.findMathTransform(wgsCRS, targetCRS);
		    				Envelope sourceEnvelope = new Envelope(Double.valueOf(wgsMinx),Double.valueOf(wgsMaxx),Double.valueOf(wgsMiny),Double.valueOf(wgsMaxy));
		    				Envelope targetEnvelope = JTS.transform( sourceEnvelope, transform);
	        				
		    				BBOX.setAttribute("minx", String.valueOf(targetEnvelope.getMinX()));
		    				BBOX.setAttribute("miny", String.valueOf(targetEnvelope.getMinY()));
		    				BBOX.setAttribute("maxx", String.valueOf(targetEnvelope.getMaxX()));
		    				BBOX.setAttribute("maxy", String.valueOf(targetEnvelope.getMaxY()));
	    				}
	    			}
	    			
	    			Filter filtre = new WMSProxyCapabilitiesLayerFilter();
	    			Iterator itL = elementLayer.getDescendants(filtre);
	    			List<org.jdom.Element> sublayersList = new ArrayList<org.jdom.Element>();
			    	while(itL.hasNext())
					{
			    		sublayersList.add((org.jdom.Element)itL.next());
					}
			    	if(sublayersList.size() != 0)
			    		rewriteBBOX(sublayersList, wgsCRS, version);
    			}
    			catch (Exception e)
    			{
    				
    			}
    		}
		}
	}
	
	private void writeLatLonBBOX130(org.jdom.Element elementLayer, String wgsMinx, String wgsMiny, String wgsMaxx, String wgsMaxy)
	{
		List llBBOXElements = elementLayer.getChildren("EX_GeographicBoundingBox");
		if(llBBOXElements.size() != 0)
		{
    		org.jdom.Element llBBOX = (org.jdom.Element) llBBOXElements.get(0);
    		llBBOX.getChild("westBoundLongitude").setText(wgsMinx);
    		llBBOX.getChild("eastBoundLongitude").setText(wgsMaxx);
    		llBBOX.getChild("southBoundLatitude").setText(wgsMiny);
    		llBBOX.getChild("northBoundLatitude").setText(wgsMaxy);
		}
		else
		{
			//create element
			org.jdom.Element element = new org.jdom.Element("EX_GeographicBoundingBox");
			org.jdom.Element wbl = new org.jdom.Element("westBoundLongitude");
			wbl.setText(wgsMinx);
			org.jdom.Element ebl = new org.jdom.Element("eastBoundLongitude");
			ebl.setText(wgsMaxx);
			org.jdom.Element sbl = new org.jdom.Element("southBoundLatitude");
			sbl.setText(wgsMiny);
			org.jdom.Element nbl = new org.jdom.Element("northBoundLatitude");
			nbl.setText(wgsMaxy);
			element.addContent(wbl );
			element.addContent(ebl );
			element.addContent(sbl );
			element.addContent(nbl );
			elementLayer.addContent(element);
   		}
	}
	
	private void writeLatLonBBOX111(org.jdom.Element elementLayer, String wgsMinx, String wgsMiny, String wgsMaxx, String wgsMaxy)
	{
		List llBBOXElements = elementLayer.getChildren("LatLonBoundingBox");
		if(llBBOXElements.size() != 0)
		{
    		org.jdom.Element llBBOX = (org.jdom.Element) llBBOXElements.get(0);
    		llBBOX.setAttribute("minx",wgsMinx);
    		llBBOX.setAttribute("miny", wgsMiny);
    		llBBOX.setAttribute("maxx", wgsMaxx);
    		llBBOX.setAttribute("maxy", wgsMaxy);
		}
		else
		{
			//create element
			org.jdom.Element element = new org.jdom.Element("LatLonBoundingBox");
			element.setAttribute("minx",wgsMinx);
			element.setAttribute("miny", wgsMiny);
			element.setAttribute("maxx", wgsMaxx);
			element.setAttribute("maxy", wgsMaxy);
			elementLayer.addContent(element);
   		}
	}
	
	public void transform(String version, String currentOperation, HttpServletRequest req, HttpServletResponse resp) {

		try 
		{
			//Filtre les fichiers réponses des serveurs :
			//ajoute les fichiers d'exception dans ogcExceptionFilePathList
			//les enlève de la collection de résultats wmsFilePathList 
			filterServersResponsesForOgcServiceExceptionFiles();
			
			//Si le mode de gestion des exceptions est "restrictif" et si au moins un serveur retourne une exception OGC
			//Ou
			//Si le mode de gestion des exceptions est "permissif" et que tous les serveurs retournent des exceptions
			//alors le proxy retourne l'ensemble des exceptions concaténées
			if((configuration.getExceptionMode().equals("restrictive") && ogcExceptionFilePathList.size() > 0) || 
					(configuration.getExceptionMode().equals("permissive") && wmsFilePathList.size() == 0))
			{
				//Traitement des réponses de type exception OGC
				//Le stream retourné contient les exceptions concaténées et mises en forme pour être retournées 
				//directement au client
				responseContentType ="text/xml";
				ByteArrayOutputStream exceptionOutputStream = buildResponseForOgcServiceException();
				sendHttpServletResponse(req,resp,exceptionOutputStream);
				return;
			}
			
			//Aucun serveur n'a retourné d'exception ou le mode de gestion des exceptions est "permissif"
			// Vérifie et prépare l'application d'un fichier xslt utilisateur
			String userXsltPath = getConfiguration().getXsltPath();
			if (SecurityContextHolder.getContext().getAuthentication() != null) {
				userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
			}

			userXsltPath = userXsltPath + "/" + version + "/" + currentOperation + ".xsl";
			String globalXsltPath = getConfiguration().getXsltPath() + "/" + version + "/" + currentOperation + ".xsl";
			
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

			// Debug tb 21.12.2009
			ByteArrayOutputStream tempOut = null; 

			// ********************************************************************************************************************
			// Postraitement des réponses à la requête contenue dans les
			// fichiers filePathList.get(i)
			if (currentOperation != null) {
				// Pour une requête utilisateur de type: Capabilities
				// ************************************************************
				if ("GetCapabilities".equalsIgnoreCase(currentOperation) || "capabilities".equalsIgnoreCase(currentOperation)) {
					dump("transform begin GetCapabilities operation");

					// Contains the list of temporary modified Capabilities
					// files.
					List<File> tempFileCapa = new Vector<File>();

					// Boucle sur les fichiers réponses
					for (int iFilePath = 0; iFilePath < wmsFilePathList.size(); iFilePath++) {
						tempFileCapa.add(createTempFile("transform_GetCapabilities_" + UUID.randomUUID().toString(), ".xml"));
						FileOutputStream tempFosCapa = new FileOutputStream(tempFileCapa.get(iFilePath));
						StringBuffer sb = buildCapabilitiesXSLT(req, resp, iFilePath, version);
						InputStream xslt = new ByteArrayInputStream(sb.toString().getBytes());
						InputSource inputSource = new InputSource(new FileInputStream(wmsFilePathList.get(iFilePath).toArray(new String[1])[0]));
						XMLReader xmlReader = XMLReaderFactory.createXMLReader();
						String user = (String) getUsername(getRemoteServerUrl(iFilePath));
						String password = (String) getPassword(getRemoteServerUrl(iFilePath));
						if (user != null && user.length() > 0) {
							ResourceResolver rr = new ResourceResolver(user, password);
							xmlReader.setEntityResolver(rr);
						}
						// END Added to hook in my EntityResolver
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
						StringBuffer sb = buildServiceMetadataCapabilitiesXSLT(version);
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
					
					//Réécriture des BBOX
					ByteArrayInputStream in =  new ByteArrayInputStream(tempOut.toByteArray());
					SAXBuilder sxb = new SAXBuilder();
					org.jdom.Document  docParent = sxb.build(in);
					String responseVersion = docParent.getRootElement().getAttribute("version").getValue();
					org.jdom.Element racine = docParent.getRootElement();
					
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
						} catch (NoSuchAuthorityCodeException e1) {
							// TODO Auto-generated catch block
							e1.printStackTrace();
						} catch (FactoryException e1) {
							// TODO Auto-generated catch block
							e1.printStackTrace();
						}
				    	rewriteBBOX(layersList, wgsCRS, responseVersion);
			    	}
			    	
			    	//Return
					XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
					ByteArrayOutputStream result =new ByteArrayOutputStream ();
					sortie.output(docParent,result );
					tempOut = result;
					
				}
				// Pour une requête utilisateur de type: Map
				// ************************************************************
				else if (currentOperation.equalsIgnoreCase("GetMap") || "Map".equalsIgnoreCase(currentOperation)) {
					dump("transform begin GetMap operation");

					boolean isTransparent = isAcceptingTransparency(responseContentType);
					// dump("DEBUG","LAYER N°:"+0+" "+layerFilePathList.get(0));

					dump("transform begin filterImage to layer " + 0);
					// Debug tb 08.07.2009
					BufferedImage imageSource = null;
					// Si les threads ont renvoyés une réponse
					if (serverUrlPerfilePathList.size() > 0) {
						// imageSource =
						// filterImage(getLayerFilter(serverUrlPerfilePathList.get(0),
						// layerFilePathList.get(0)), wmsFilePathList.get(0),
						// isTransparent);
						// Fin de Debug
						Graphics2D g = null;
						dump("transform end filterImage to layer " + 0);

						// Boucle sur les fichiers réponses
						TreeMap<Integer, Collection<String>> tm = new TreeMap<Integer, Collection<String>>(wmsFilePathList.asMap());
						for (Map.Entry<Integer, Collection<String>> e : tm.entrySet()) {
							// dump("DEBUG","LAYER N°:"+iFilePath+" "+layerFilePathList.get(iFilePath));
							int iFilePath = e.getKey();
							dump("transform begin filterImage to layer " + iFilePath);
							// Debug tb 08.07.2009
							BufferedImage image = filterImage(getLayerFilter(serverUrlPerfilePathList.get(iFilePath), layerFilePathList.get(iFilePath)),
									wmsFilePathList.get(iFilePath), isTransparent, resp);
							// Fin de Debug
							if (g == null) {
								imageSource = image;
								if (imageSource != null)
									g = imageSource.createGraphics();
							} else if (image != null)
								g.drawImage(image, null, 0, 0);
							dump("transform end filterImage to layer " + iFilePath);
						}
						if (g != null)
							g.dispose();
						// Debug tb 11.08.2009
					}
					// Si aucune requête n'a été envoyé au serveur, retourne:
					// empty image
					else {
						imageSource = ImageIO.read(new File(wmsFilePathList.get(0).toArray(new String[1])[0]));
					}
					// Fin de Debug

					// Etape nécessaire car "resp.getOutputStream()" ne peux pas
					// lire directement le flux d' "imageSource"
					Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(responseContentType);
					if (iter.hasNext()) {
						ImageWriter writer = (ImageWriter) iter.next();
						tempOut = new ByteArrayOutputStream();
						// tempFile =
						// createTempFile("transform_GetMap_"+UUID.randomUUID().toString(),
						// getExtension(responseContentType));
						// FileImageOutputStream output = new
						// FileImageOutputStream(tempFile);
						writer.setOutput(new MemoryCacheImageOutputStream(tempOut));
						// writer.setOutput(output);
						dump("transform begin write tempOut");
						if (imageSource != null)
							writer.write(imageSource);
						// Debug tb 06.07.2009
						dump("transform end write tempOut");
						// output.flush();
						// output.close();
						// Fin de Debug
					}
					dump("transform end GetMap operation");
				}
				// Debug tb 04.11.2009
				// Pour une requête utilisateur de type: GetLegendGraphic
				// ********************************************************
				else if (currentOperation.equalsIgnoreCase("GetLegendGraphic")) {
					dump("transform begin GetLegendGraphic operation");

					// boolean isTransparent=
					// isAcceptingTransparency(responseContentType);

					dump("transform begin add Legend Image " + 0);
					Graphics2D g = null;
					dump("transform end filterImage to layer " + 0);
					// Boucle sur les fichiers réponses

					BufferedImage imageSource = null;
					String format = "jpeg";
					for (Map.Entry<Integer, String> e : wmsFilePathList.entries()) {
						// dump("DEBUG","LAYER N°:"+iFilePath+" "+layerFilePathList.get(iFilePath));
						int iFilePath = e.getKey();
						dump("transform begin Legend Image " + iFilePath);
						// Debug tb 08.07.2009

						BufferedImage image = ImageIO.read(new File(e.getValue()));
						if (image != null && image.getWidth() > 1) {
							int type = BufferedImage.TYPE_INT_BGR;
							if (image.getTransparency() == Transparency.BITMASK) {
								type = BufferedImage.BITMASK;
								format = "png";
							} else if (image.getTransparency() == Transparency.TRANSLUCENT) {
								type = BufferedImage.TRANSLUCENT;
								format = "png";

							}
							BufferedImage canvas = new BufferedImage(image.getWidth(), image.getHeight(), type);
							canvas.getGraphics().drawImage(image, 0, 0, null);

							// Fin de Debug
							if (g == null) {
								imageSource = canvas;
								g = imageSource.createGraphics();
							} else if (image != null)
								g.drawImage(canvas, null, 0, 0);
							dump("transform end add Legend Image " + iFilePath);
						}
					}
					if (g != null)
						g.dispose();

					// Si aucune requête n'a été envoyé au serveur, retourne:
					// empty image
					else {
						format = "png";
						imageSource = new BufferedImage(32, 32, BufferedImage.TRANSLUCENT);
					}
					tempOut = new ByteArrayOutputStream();
					ImageIO.write(imageSource, format, tempOut);

					// Etape nécessaire car "resp.getOutputStream()" ne peux pas
					// lire directement le flux d' "imageSource"
					// Iterator<ImageWriter> iter =
					// ImageIO.getImageWritersByMIMEType(responseContentType);

					// if (iter.hasNext()) {
					// ImageWriter writer = (ImageWriter) iter.next();

					// tempFile =
					// createTempFile("transform_GetLegendGraphic_"+UUID.randomUUID().toString(),
					// getExtension(responseContentType));
					// FileImageOutputStream output = new
					// FileImageOutputStream(tempFile);
					// writer.setOutput(new
					// MemoryCacheImageOutputStream(tempOut));
					// writer.setOutput(output);
					// writer.write(imageSource);
					// output.flush();
					// output.close();
					// Fin de Debug
					// }
					dump("transform end GetLegendGraphic operation");
				}

				else if (currentOperation.equalsIgnoreCase("GetFeatureInfo")) {
					dump("transform begin GetFeatureInfo operation");
					// resp.setHeader("Content-Encoding", "gzip");

					tempOut = new ByteArrayOutputStream();
					DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
					XPathFactory xpathFactory = XPathFactory.newInstance();
					DocumentBuilder builder = factory.newDocumentBuilder();
					Document doc = builder.newDocument();
					transformer = tFactory.newTransformer();
					XPath xpath = xpathFactory.newXPath();
					XPathExpression expr = xpath.compile("/FeatureCollection");
					Element rootNode = null;

					for (String path : wmsFilePathList.values()) {
						Document resultDoc = builder.parse(new File(path));
						if (rootNode == null) {
							DeferredElementImpl result = (DeferredElementImpl) expr.evaluate(resultDoc, XPathConstants.NODE);
							if (result != null) {
								rootNode = (Element) doc.importNode(result, true);
								doc.appendChild(rootNode);
							}
						} else {
							DeferredElementImpl result = (DeferredElementImpl) resultDoc.getDocumentElement().getChildNodes();
							for (int i = 0; i < result.getAttributes().getLength(); i++) {
								Attr attr = (Attr) doc.importNode(result.getAttributes().item(i), true);
								rootNode.setAttributeNode(attr);
							}
							if (result != null && result.getLength() > 0) {
								for (int i = 0; i < result.getLength(); i++) {
									Node nnode = result.item(i);
									if (!"gml:boundedBy".equals(nnode.getNodeName())) {
										nnode = doc.importNode(nnode, true);
										rootNode.appendChild(nnode);
									}
								}
							}

						}
					}
					transformer.transform(new DOMSource(doc), new StreamResult(tempOut));
					dump("transform end GetFeatureInfo operation");
				}
			}

			// ********************************************************************************************************************
			// Traitement du résultat final avec le xslt utilisateur s'il exist
			// (voir début de transform())
			// if a xslt file exists then post-treat the response
			if (isPostTreat && isXML(responseContentType)) {
				dump("transform begin userTransform xslt");

				PrintWriter out = resp.getWriter();
				transformer = tFactory.newTransformer(new StreamSource(xsltFile));
				ByteArrayInputStream is = new ByteArrayInputStream(tempOut.toByteArray());
				tempOut = new ByteArrayOutputStream();
				transformer.transform(new StreamSource(is), new StreamResult(out));
				// transformer.transform(new StreamSource(tempFile), new
				// StreamResult(out));
				// delete the temporary file
				// tempFile.delete();
				out.close();

				dump("transform end userTransform xslt");
				// the job is done. we can go out
				return;
			}

			// Ou Ecriture du résultat final dans resp de
			// httpServletResponse*****************************************************
			sendHttpServletResponse(req,resp,tempOut);
			// No post rule to apply. Copy the file result on the output stream
//			BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());
//			resp.setContentType(responseContentType);
//			// BufferedInputStream is = new BufferedInputStream(new
//			// FileInputStream(tempFile));
//
//			// Debug tb 06.12.2009
//
//			// Pour une bonne performances en écriture
//			// byte[] data = new byte[131072];
//			// int byteRead;
//			try {
//				dump("transform begin response writting");
//				if ("1".equals(req.getParameter("download"))) {
//					String format = req.getParameter("format");
//					if (format == null)
//						format = req.getParameter("FORMAT");
//					if (format != null) {
//						String parts[] = format.split("/");
//						String ext = "";
//						if (parts.length > 1)
//							ext = parts[1];
//						resp.setHeader("Content-Disposition", "attachment; filename=download." + ext);
//					}
//				}
//				if (tempOut != null)
//					os.write(tempOut.toByteArray());
//				// while((byteRead = is.read()) != -1)
//				// while((byteRead = is.read(data)) != -1)
//				// {
//				// os.write(byteRead);
//				// os.write(data);
//				// os.flush();
//				// }
//				dump("transform end response writting");
//			} finally {
//				os.flush();
//				os.close();
//				// is.close();
//				// Fin de Debug
//
//				// Log le résultat et supprime les fichiers temporaires
//				DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
//				Date d = new Date();
//				dump("SYSTEM", "ClientResponseDateTime", dateFormat.format(d));
//				if (tempOut != null)
//					dump("SYSTEM", "ClientResponseLength", tempOut.size());
//				// if (tempFile !=null)
//				// {
//				// dump("SYSTEM","ClientResponseLength",tempFile.length());
//				// tempFile.delete();
//				// }
//				// Fin de Debug
//			}
//
//			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
//			Date d = new Date();
//
//			dump("SYSTEM", "ClientResponseDateTime", dateFormat.format(d));

		} 
		catch (SAXParseException e)
		{
			e.printStackTrace();
			dump("ERROR", e.getMessage());
			sendOgcExceptionBuiltInResponse(resp,generateOgcError("Response format not recognized. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
		}
		catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.toString());
			sendOgcExceptionBuiltInResponse(resp,generateOgcError("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
		}
	}

	private void sendHttpServletResponse (HttpServletRequest req, HttpServletResponse resp, ByteArrayOutputStream tempOut)
	{
		try
		{
			// Ecriture du résultat final dans resp de
			// httpServletResponse*****************************************************
			// No post rule to apply. Copy the file result on the output stream
			BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());
			resp.setContentType(responseContentType);
			try {
				dump("transform begin response writting");
				if ("1".equals(req.getParameter("download"))) {
					String format = req.getParameter("format");
					if (format == null)
						format = req.getParameter("FORMAT");
					if (format != null) {
						String parts[] = format.split("/");
						String ext = "";
						if (parts.length > 1)
							ext = parts[1];
						resp.setHeader("Content-Disposition", "attachment; filename=download." + ext);
					}
				}
				if (tempOut != null)
					os.write(tempOut.toByteArray());
				dump("transform end response writting");
			} finally {
				os.flush();
				os.close();
				// Log le résultat et supprime les fichiers temporaires
				DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
				Date d = new Date();
				dump("SYSTEM", "ClientResponseDateTime", dateFormat.format(d));
				if (tempOut != null)
					dump("SYSTEM", "ClientResponseLength", tempOut.size());
			}
	
			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			Date d = new Date();
			dump("SYSTEM", "ClientResponseDateTime", dateFormat.format(d));
		} 
		catch (Exception e) 
		{
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}
	}
	// ***************************************************************************************************************************************

	/**
	 * @return
	 */
	private BufferedImage filterImage(String filter, Collection<String> fileNames, boolean isTransparent, HttpServletResponse resp) {
		BufferedImage imageSource = null;
		Graphics2D g = null;
		for (String fileName : fileNames) {
			BufferedImage image = filterImage(filter, fileName, isTransparent, resp);
			if (g == null) {
				imageSource = image;
				if (imageSource != null)
					g = imageSource.createGraphics();
			} else if (image != null)
				g.drawImage(image, null, 0, 0);
		}
		if (g != null)

			g.dispose();
		return imageSource;
	}

	private BufferedImage filterImage(String filter, String fileName, boolean isTransparent, HttpServletResponse resp) {
		try {
			if (filter != null) {
				String[] s = bbox.split(",");

				InputStream bis = new ByteArrayInputStream(filter.getBytes());
				System.setProperty("org.geotools.referencing.forceXY", "true");

				Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);
				WKTReader wktReader = new WKTReader();

				Geometry polygon = wktReader.read(object.toString());

				filter.indexOf("srsName");
				String srs = filter.substring(filter.indexOf("srsName"));
				srs = srs.substring(srs.indexOf("\"") + 1);
				srs = srs.substring(0, srs.indexOf("\""));
				polygon.setSRID(Integer.parseInt(srs.substring(5)));

				CRSEnvelope bbox = new CRSEnvelope(srsName, Double.parseDouble(s[0]), Double.parseDouble(s[1]), Double.parseDouble(s[2]), Double
						.parseDouble(s[3]));

				// final WorldFileReader reader = new WorldFileReader(new
				// File(filePath));

				BufferedImage image = ImageIO.read(new File(fileName));
				int type = BufferedImage.TYPE_INT_BGR;
				if (image.getTransparency() == Transparency.BITMASK)
					type = BufferedImage.BITMASK;
				else if (image.getTransparency() == Transparency.TRANSLUCENT)
					type = BufferedImage.TRANSLUCENT;
				BufferedImage canvas = new BufferedImage(image.getWidth(), image.getHeight(), type);
				canvas.getGraphics().drawImage(image, 0, 0, null);
				BufferedImage imageOut = imageFiltering(canvas, bbox, polygon, isTransparent, resp);

				return imageOut;
			} else {
				if (fileName != null) {
					BufferedImage image = ImageIO.read(new File(fileName));
					if (image == null)
						return null;
					int type = BufferedImage.TYPE_INT_BGR;
					if (image.getTransparency() == Transparency.BITMASK)
						type = BufferedImage.BITMASK;
					else if (image.getTransparency() == Transparency.TRANSLUCENT)
						type = BufferedImage.TRANSLUCENT;
					BufferedImage canvas = new BufferedImage(image.getWidth(), image.getHeight(), type);
					canvas.getGraphics().drawImage(image, 0, 0, null);
					return canvas;
				}
			}
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
		}
		return null;
	}

	// ***************************************************************************************************************************************

	/**
	 * @param tempFileCapa
	 * @return
	 */
	private ByteArrayOutputStream mergeCapabilities(List<File> tempFileCapa, HttpServletResponse resp)
	// private File mergeCapabilities(List<File> tempFileCapa)
	{

		if (tempFileCapa.size() == 0)
			return null;

		try {
			File fMaster = tempFileCapa.get(0);
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(false);
			Document documentMaster = db.newDocumentBuilder().parse(fMaster);
			DOMImplementationLS implLS = null;
			if (documentMaster.getImplementation().hasFeature("LS", "3.0")) {
				implLS = (DOMImplementationLS) documentMaster.getImplementation();
			} else {
				DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
				implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
			}
			if (implLS == null) {
				dump("Error", "DOM Load and Save not Supported. Multiple server is not allowed");
				ByteArrayOutputStream out = new ByteArrayOutputStream();
				FileInputStream reader = new FileInputStream(fMaster);
				byte[] data = new byte[reader.available()];
				reader.read(data, 0, reader.available());
				out.write(data);
				reader.close();
				return out;
				// return fMaster;
			}

			for (int i = 1; i < tempFileCapa.size(); i++) {
				Document documentChild = null;
				try {
					documentChild = db.newDocumentBuilder().parse(tempFileCapa.get(i));
				} catch (Exception e) {
					e.printStackTrace();
					dump("ERROR", e.getMessage());
				}
				if (documentChild != null) {
					NodeList nl = documentChild.getElementsByTagName("Layer");
					NodeList nlMaster = documentMaster.getElementsByTagName("Layer");
					Node ItemMaster = nlMaster.item(0);
					if (nl.item(0) != null)
						ItemMaster.insertBefore(documentMaster.importNode(nl.item(0).cloneNode(true), true), null);
				}
			}

			ByteArrayOutputStream out = new ByteArrayOutputStream();
			// File f = createTempFile(UUID.randomUUID().toString(),".xml");
			// FileOutputStream fluxSortie = new FileOutputStream(f);
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			// sortie.setSystemId(f.toString());
			// sortie.setByteStream(fluxSortie);
			sortie.setByteStream(out);
			serialiseur.write(documentMaster, sortie);
			// fluxSortie.flush();
			// fluxSortie.close();

			return out;
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.getMessage());
			return null;
		}
	}

	// ***************************************************************************************************************************************

	protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp) {
		this.requestPreTreatmentGET(req, resp);
	}

	// ***************************************************************************************************************************************

	protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp) {
		try {
			String operation = null;
			String version = "000";
			String service = "";
			String layer = ""; // Pour l'opération GetLegendGraphic seulement
			String queryLayers = ""; // Pour l'opération GetFeatureInfo
			// seulement
			String width = "1";
			String height = "1";
			String format = "";

			layers = null;
			boolean sendRequest = true;
			Enumeration<String> parameterNames = req.getParameterNames();
			String paramUrlBase = "";

			// *********************************************************************
			// To build the request to dispatch
			// Lecture des paramètres de la requête utlisateur en mode GET
			while (parameterNames.hasMoreElements()) {
				String key = (String) parameterNames.nextElement();
				String value = "";
				if (key.equalsIgnoreCase("LAYER") || key.equalsIgnoreCase("QUERY_LAYERS") || key.equalsIgnoreCase("LAYERS") || key.equalsIgnoreCase("STYLES")
						|| key.equalsIgnoreCase("BBOX") || key.equalsIgnoreCase("SRS") || key.equalsIgnoreCase("CRS")) {
					value = req.getParameter(key);
				} else {
					value = URLEncoder.encode(req.getParameter(key));
				}

				// String value = req.getParameter(key);
				if (!key.equalsIgnoreCase("QUERY_LAYERS") && !key.equalsIgnoreCase("LAYERS") && !key.equalsIgnoreCase("STYLES")) {
					paramUrlBase = paramUrlBase + key + "=" + value + "&";
				}

				if (key.equalsIgnoreCase("Request")) {
					// Gets the requested Operation
					if (value.equalsIgnoreCase("capabilities")) {
						operation = "GetCapabilities";
					} else {
						operation = value;
					}
				} else if (key.equalsIgnoreCase("version")) {
					// Gets the requested version
					requestedVersion = value;
					version = value;
					if (version.replaceAll("\\.", "").equalsIgnoreCase("100")) {
						dump("ERROR", "Bad WMS version request.");
						sendOgcExceptionBuiltInResponse(resp,generateOgcError("Version not supported.","InvalidParameterValue","version", "1.1.1"));
						return;
					}
				} else if (key.equalsIgnoreCase("wmtver")) {
					// Gets the requested wmtver
					version = value;
					service = "WMS";
				} else if (key.equalsIgnoreCase("service")) {
					// Gets the requested service
					service = value;
				} else if (key.equalsIgnoreCase("BBOX")) {
					// Gets the requested bbox
					bbox = value;
				} else if (key.equalsIgnoreCase("SRS")) {
					// Gets the requested srs
					srsName = value;
				} else if (key.equalsIgnoreCase("CRS")) // Version 1.3.0
				{
					// Gets the requested srs
					srsName = value;
				} else if (key.equalsIgnoreCase("LAYER")) {
					// Gets the requested layer -> GetLegendGraphic only
					layer = value;
				}
				// Debug tb 18.01.2010
				else if (key.equalsIgnoreCase("QUERY_LAYERS")) {
					// Gets the requested querylayers -> GetFeatureInfo
					queryLayers = value;
				}
				// Fin de Debug
				else if (key.equalsIgnoreCase("LAYERS")) {
					// Gets the requested layers -> GetMap
					layers = value;
				} else if (key.equalsIgnoreCase("STYLES")) {
					styles = value;
				} else if (key.equalsIgnoreCase("WIDTH")) {
					width = value;
				} else if (key.equalsIgnoreCase("HEIGHT")) {
					height = value;
				} else if (key.equalsIgnoreCase("FORMAT")) {
					format = value;
				}
			}
						
			//Generate OGC exception and send it to the client if current operation is not allowed
			if(handleNotAllowedOperation(operation,resp))
				return;
			
			// Debug tb 18.01.2010
			// Pour éviter le cas où "layers" est absent de la requête
			// GetFeatureInfo
			if (!queryLayers.equalsIgnoreCase("")) {
				layers = queryLayers;
			}
			// Fin de Debug
			
			//HVH - 4.11.2010 : sen an OGC Exception if parameters LAYERS OR QUERY_LAYERS are empty or missing
			if ("GetMap".equals(operation) ||
				 "GetLegendGraphic".equals(operation)) 
			{
				if(layers == null || layers.equalsIgnoreCase(""))
				{
					String param =  "GetMap".equals(operation) ? "LAYERS" : "QUERY_LAYERS";
					sendOgcExceptionBuiltInResponse(resp,generateOgcError(param+" parameter is missing.","LayerNotDefined",param,requestedVersion));
					return;
				}
			}
			//-- HVH

			String user = "";
			if (SecurityContextHolder.getContext().getAuthentication()!= null) {
				user = SecurityContextHolder.getContext().getAuthentication().getName();
			}

			// Debug tb 11.11.2009
//			if (hasPolicy) {
//				if (!isOperationAllowed(operation))
//					throw new NoPermissionException("operation is not allowed");
//			}
			// Fin de Debug
			
			

			// *********************************************************************

			// Debug tb 09.07.2009
			// Définition de la classe des threads d'interrogation sur les
			// serveurs
			// **************************************************************************************
			// **************************************************************************************
			class SendServerThread extends Thread {

				protected Vector<String> serverFilePathList = new Vector<String>();
				protected Vector<String> serverLayerFilePathList = new Vector<String>();

				String operation;
				String paramUrl;
				List layerToKeepList;
				int iServer;
				List stylesToKeepList;
				String paramUrlBase;
				String width;
				String height;
				String format;
				int j;
				int layerOrder;
				HttpServletResponse resp;

				// **************************************************************************************
				public SendServerThread(String pOperation, String pParamUrl, List pLayerToKeepList, int pIServer, List pStylesToKeepList, String pParamUrlBase,
						int pJ, String pWidth, String pHeight, String pFormat, HttpServletResponse res) {
					operation = pOperation;
					paramUrl = pParamUrl;
					layerToKeepList = pLayerToKeepList;
					iServer = pIServer;
					stylesToKeepList = pStylesToKeepList;
					paramUrlBase = pParamUrlBase;
					j = pJ;
					width = pWidth;
					height = pHeight;
					format = pFormat;
					resp = res;
				}

				// **************************************************************************************
				public void run() {

					// Pour créer les threads d'interrogation des couches du
					// serveur j
					// -> nécessaire car filter peut être différent d'une couche
					// à l'autre du même serveur!!!
					// **************************************************************************************
					// **************************************************************************************
					class SendLayerThread extends Thread {

						List layerToKeepList;
						int iLayers;
						List stylesToKeepList;
						String paramUrlBase;
						String width;
						String height;
						String format;
						HttpServletResponse resp;
						int j;

						// **************************************************************************************
						// public SendLayerThread(String pOperation,String
						// pParamUrl,List pLayerToKeepList,int pILayers,List
						// pStylesToKeepList,String pParamUrlBase,int pJ,String
						// pWidth,String pHeight,String pFormat)
						public SendLayerThread(int pILayers, List pLayerToKeepList, List pStylesToKeepList, String pParamUrlBase, int pJ, String pWidth,
								String pHeight, String pFormat, HttpServletResponse res) {
							layerToKeepList = pLayerToKeepList;
							iLayers = pILayers;
							stylesToKeepList = pStylesToKeepList;
							paramUrlBase = pParamUrlBase;
							j = pJ;
							width = pWidth;
							height = pHeight;
							format = pFormat;
							resp = res;
						}

						// **************************************************************************************
						public void run() {
							try {
								dump("DEBUG", "Thread Layers group: " + layerToKeepList.get(0) + " work begin on server " + getRemoteServerUrl(j));

								String layersUrl = "LAYERS=" + layerToKeepList.get(0);
								String stylesUrl = "&STYLES=" + stylesToKeepList.get(0);
								for (int n = 1; n < layerToKeepList.size(); n++) {
									layersUrl = layersUrl + "," + layerToKeepList.get(n);
									stylesUrl = stylesUrl + "," + stylesToKeepList.get(n);
								}

								if (paramUrlBase.toUpperCase().indexOf("TRANSPARENT=") == -1)
									paramUrlBase += "TRANSPARENT=TRUE&";
								String filePath = sendData("GET", getRemoteServerUrl(j), paramUrlBase + layersUrl + stylesUrl);

								synchronized (serverFilePathList) {
									synchronized (serverLayerFilePathList) {
										// Ecriture de la réponse du thread,
										// dans le respect de l'ordre des layers
										// de la requête utilisateur
										if (iLayers >= serverFilePathList.size()) {
											serverFilePathList.add(filePath);
											serverLayerFilePathList.add((String) layerToKeepList.get(0));
										} else {
											serverFilePathList.add(iLayers, filePath);
											serverLayerFilePathList.add(iLayers, (String) layerToKeepList.get(0));
										}
									}
								}
								dump("DEBUG", "Thread Layers group: " + layerToKeepList.get(0) + " work finished on server " + getRemoteServerUrl(j));
							} catch (Exception e) {
								resp.setHeader("easysdi-proxy-error-occured", "true");
								dump("ERROR", "Server " + getRemoteServerUrl(j) + " Layers group Thread " + layerToKeepList.get(0) + " :" + e.getMessage());
								e.printStackTrace();
							}
						}
					}
					// **************************************************************************************
					// **************************************************************************************

					try {
						dump("DEBUG", "Thread Server: " + getRemoteServerUrl(j) + " work begin");
						List<SendLayerThread> layerThreadList = new Vector<SendLayerThread>();

						if ("GetMap".equalsIgnoreCase(operation) || "map".equalsIgnoreCase(operation)) {
							// Test si les filtres des layers sont différents
							// les uns des autres:
							// ->envoi de 1 "thread layer" par groupe de couches
							// ayant un "policy filter" identique
							// for(int
							// iLayers=0;iLayers<layerToKeepList.size();iLayers++)
							while (layerToKeepList.size() > 0) {
								List<String> layerToKeepListPerThread = new Vector<String>();
								List<String> stylesToKeepListPerThread = new Vector<String>();

								String filter = getLayerFilter(getRemoteServerUrl(j), (String) layerToKeepList.get(0));

								layerToKeepListPerThread.add((String) layerToKeepList.remove(0));
								stylesToKeepListPerThread.add((String) stylesToKeepList.remove(0));

								// Création du polygon A à partir du filtre de
								// iLayer
								Boolean isNoFilterA = false;
								Geometry polygonA = null;

								// Par la même occasion, vérification que la
								// bbox de la requête utilisateur est dans le
								// filter de layerToKeepList(0)
								// Sinon les layers ayant le même filtre ne sont
								// pas conservées dans la requête.
								System.setProperty("org.geotools.referencing.forceXY", "true");
								String[] s = bbox.split(",");
								boolean iscoveredByfilter = true;

								if (filter != null && filter.length() > 0) {
									InputStream bis = new ByteArrayInputStream(filter.getBytes());
									Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);
									WKTReader wktReader = new WKTReader();

									polygonA = wktReader.read(object.toString());

									Geometry polygon = wktReader.read(object.toString());
									filter.indexOf("srsName");
									String srs = filter.substring(filter.indexOf("srsName"));
									srs = srs.substring(srs.indexOf("\"") + 1);
									srs = srs.substring(0, srs.indexOf("\""));
									polygon.setSRID(Integer.parseInt(srs.substring(5)));
									CoordinateReferenceSystem sourceCRS = CRS.decode("EPSG:" + (new Integer(polygon.getSRID())).toString());
									CoordinateReferenceSystem targetCRS = CRS.decode(srsName);
									double x1 = Double.parseDouble(s[0]);
									double y1 = Double.parseDouble(s[1]);
									double x2 = Double.parseDouble(s[2]);
									double y2 = Double.parseDouble(s[3]);
									MathTransform a = CRS.findMathTransform(sourceCRS, targetCRS, false);
									polygon = JTS.transform(polygon, a);
									polygon.setSRID(Integer.parseInt(srs.substring(5)));
									Coordinate[] c = { new Coordinate(x1, y1), new Coordinate(x1, y1), new Coordinate(x2, y1), new Coordinate(x2, y2),
											new Coordinate(x1, y2), new Coordinate(x1, y1) };
									GeometryFactory gf = new GeometryFactory();
									Geometry bboxGeom = gf.createPolygon(gf.createLinearRing(c), null);
									bboxGeom.setSRID(Integer.parseInt(srs.substring(5)));
									IntersectionMatrix mat1 = bboxGeom.relate(polygon);
									IntersectionMatrix mat2 = polygon.relate(bboxGeom);

									if (mat1.isIntersects() || mat2.isIntersects() || bboxGeom.overlaps(polygon) || polygon.overlaps(bboxGeom)
											|| polygon.coveredBy(bboxGeom) || bboxGeom.coveredBy(polygon) || bboxGeom.touches(polygon)
											|| polygon.touches(bboxGeom) || bboxGeom.intersects((polygon)) || bboxGeom.covers((polygon))
											|| bboxGeom.crosses((polygon)) || polygon.crosses(bboxGeom) || polygon.intersects((bboxGeom))
											|| polygon.covers((bboxGeom))) {
										iscoveredByfilter = true;
									} else {
										iscoveredByfilter = false;
									}
								} else {
									isNoFilterA = true;
								}
								for (int k = 0; k < layerToKeepList.size(); k++) {
									// Création du polygon B à partir du filtre
									// de iLayer
									Boolean isNoFilterB = false;
									filter = getLayerFilter(getRemoteServerUrl(j), (String) layerToKeepList.get(k));
									Geometry polygonB = null;
									if (filter != null && filter.length() > 0) {
										InputStream bis = new ByteArrayInputStream(filter.getBytes());
										Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);
										WKTReader wktReader = new WKTReader();
										polygonB = wktReader.read(object.toString());
									} else {
										isNoFilterB = true;
									}

									// Comparaison des filtres
									if (!isNoFilterA && !isNoFilterB) {
										if (polygonA.equalsExact(polygonB)) {
											layerToKeepListPerThread.add((String) layerToKeepList.remove(k));
											stylesToKeepListPerThread.add((String) stylesToKeepList.remove(k));
											k--;
										}
									} else if (isNoFilterA && isNoFilterB) {
										layerToKeepListPerThread.add((String) layerToKeepList.remove(k));
										stylesToKeepListPerThread.add((String) stylesToKeepList.remove(k));
										k--;
									}
								}

								if (iscoveredByfilter) {
									// Création et lancement des threads sur
									// serveur j pour chaque groupe de couches
									// (à filtres identiques)
									dump("requestPreTraitementGET send request multiLayer to thread server " + getRemoteServerUrl(j));
									SendLayerThread th = new SendLayerThread(layerThreadList.size(), layerToKeepListPerThread, stylesToKeepListPerThread,
											paramUrlBase, j, width, height, format, resp);
									th.start();
									layerThreadList.add(th);
								} else {
									dump("ERROR", "Thread Layers group: " + layerToKeepListPerThread.get(0) + " work finished on server "
											+ getRemoteServerUrl(j) + " : bbox not covered by policy filter.");
								}
							}
							// Récupération du résultat des threads sur serveur
							// j
							// Autant de filePath à ajouter que de couches
							for (int i = 0; i < layerThreadList.size(); i++) {
								layerThreadList.get(i).join();

								// Si une réponse a bien été renvoyée par le
								// thread i
								if (!serverFilePathList.isEmpty() && !((String) serverFilePathList.get(i)).equals("")) {
									synchronized (wmsFilePathList) {
										synchronized (layerFilePathList) {
											synchronized (serverUrlPerfilePathList) {
												// Insert les réponses
												layerFilePathList.put(layerOrder, serverLayerFilePathList.get(i));
												serverUrlPerfilePathList.put(layerOrder, getRemoteServerUrl(j));
												wmsFilePathList.put(layerOrder, serverFilePathList.get(i));
											}
										}
									}
								}
							}
						} else if ("GetCapabilities".equalsIgnoreCase(operation) || "capabilities".equalsIgnoreCase(operation)) {
							if (paramUrlBase.toUpperCase().indexOf("SERVICE=") == -1)
							{
								paramUrlBase += "&SERVICE=WMS";
							}
							String filePath = sendData("GET", getRemoteServerUrl(j), paramUrlBase);

							synchronized (wmsFilePathList) {
								synchronized (layerFilePathList) {
									synchronized (serverUrlPerfilePathList) {
										// Insert les réponses
										dump("requestPreTraitementGET save response capabilities from thread server " + getRemoteServerUrl(j));
										layerFilePathList.put(layerOrder, "");
										serverUrlPerfilePathList.put(layerOrder, getRemoteServerUrl(j));
										wmsFilePathList.put(layerOrder, filePath);
									}
								}
							}
						}
						// Debug tb 04.11.2009
						else if ("GetLegendGraphic".equalsIgnoreCase(operation)) {
							String filePath = sendData("GET", getRemoteServerUrl(j), paramUrlBase);

							synchronized (wmsFilePathList) {
								synchronized (layerFilePathList) {
									synchronized (serverUrlPerfilePathList) {
										// Insert les réponses
										dump("requestPreTraitementGET save response legendGraphic from thread server " + getRemoteServerUrl(j));
										layerFilePathList.put(layerOrder, "");
										serverUrlPerfilePathList.put(layerOrder, getRemoteServerUrl(j));
										wmsFilePathList.put(layerOrder, filePath);
									}
								}
							}
						}
						// Fin de Debug
						// Debug tb 15.01.2010
						else if ("GetFeatureInfo".equalsIgnoreCase(operation)) {
							// Debug tb 18.01.2010
							String queryLayersUrl = "QUERY_LAYERS=" + layerToKeepList.get(0);
							String layersUrl = "LAYERS=" + layerToKeepList.get(0);
							for (int n = 1; n < layerToKeepList.size(); n++) {
								queryLayersUrl = queryLayersUrl + "," + layerToKeepList.get(n);
								layersUrl = layersUrl + "," + layerToKeepList.get(n);
							}
							String stylesUrl = "STYLES=" + stylesToKeepList.get(0);
							for (int n = 1; n < stylesToKeepList.size(); n++) {
								stylesUrl = stylesUrl + "," + stylesToKeepList.get(n);
							}

							String filePath = sendData("GET", getRemoteServerUrl(j), paramUrlBase + queryLayersUrl + "&" + layersUrl + "&" + stylesUrl);
							// Fin de Debug
							synchronized (wmsFilePathList) {
								synchronized (layerFilePathList) {
									synchronized (serverUrlPerfilePathList) {
										// Insert les réponses
										dump("requestPreTraitementGET save response GetFeatureInfo from thread server " + getRemoteServerUrl(j));
										layerFilePathList.put(layerOrder, "");
										serverUrlPerfilePathList.put(layerOrder, getRemoteServerUrl(j));
										wmsFilePathList.put(layerOrder, filePath);
									}
								}
							}
						}
						// Fin de Debug
						dump("DEBUG", "Thread Server: " + getRemoteServerUrl(j) + " work finished");
					} catch (Exception e) {
						resp.setHeader("easysdi-proxy-error-occured", "true");
						dump("ERROR", "Server Thread " + getRemoteServerUrl(j) + " :" + e.getMessage());
						e.printStackTrace();
					}
				}
			}
			// **************************************************************************************
			// **************************************************************************************
			// Fin de Debug

			// Boucle sur les serveur définis dans config.xml
			// *************************
			List<RemoteServerInfo> grsiList = getRemoteServerInfoList();
			List<SendServerThread> serverThreadList = new Vector<SendServerThread>();

			List<String> layerArray = null;
			if (layer != null && layer != "" && layers == null)
				layers = layer;
			if (layers != null)
			{
				layerArray = Collections.synchronizedList(new ArrayList<String>(Arrays.asList(layers.split(","))));
			}
			else
			{
				layerArray = Collections.synchronizedList(new ArrayList<String>());
			}
			int layerOrder = 0;
			String lastServerURL = null;
			String newServerURL = null;
			String cpOperation = new String(operation);
			String cpParamUrl = "";
			String cpParamUrlBase = new String(paramUrlBase);
			String cpWidth = new String(width);
			String cpHeight = new String(height);
			String cpFormat = new String(format);
			String filter = null;
			if (operation.equalsIgnoreCase("getcapabilities")) {
				for (int jj = 0; jj < grsiList.size(); jj++) {
					SendServerThread s = new SendServerThread(cpOperation, cpParamUrl, null, serverThreadList.size(), null, cpParamUrlBase, jj, cpWidth,
							cpHeight, cpFormat, resp);

					s.layerOrder = jj;
					s.start();
					serverThreadList.add(s);
				}

			} else {
				while (layerArray.size() > 0) {
					List<String> layerToKeepList = new Vector<String>();
					List<String> stylesToKeepList = new Vector<String>();
					int j = 0;
					if (policy != null) {
						if (hasPolicy) {
							// Vérfication de la taille image req VS policy ->
							// si vrai:
							// la requête n'est pas envoyée
							sendRequest = true;
							if (("GetMap".equalsIgnoreCase(operation) || 
								 "map".equalsIgnoreCase(operation)) || 
								 "getfeatureinfo".equalsIgnoreCase(operation)) 
							{
								if (!isSizeInTheRightRange(Integer.parseInt(width), Integer.parseInt(height)))
								{
									dump("requestPreTraitementGET says: request ImageSize out of bounds, see the policy definition.");
									sendRequest = false;
									layerArray.remove(0);
								}

								// Vérification de la présence du pramètre 
								// "LAYERS"
								// dans la
								// requête -> si vrai: recherche des layers
								// autorisées et
								// styles correspondant
								// Permet la réécriture des paramètres "LAYERS"
								// et
								// "STYLES"
								// de la requête
								if (sendRequest && layers != null && layers.length() > 0) 
								{
									// Debug tb 09.07.2009
									String[] layerStyleArray;
									if (styles != null) {
										layerStyleArray = styles.split(",");
									} else {
										styles = "";
										layerStyleArray = styles.split(",");
									}

									// Le paramètre style est obligatoire par
									// couche, mais
									// on l'émule s'il n'est pas présent
									if (layerStyleArray.length < layerArray.size()) {
										int diffSize = layerArray.size() - layerStyleArray.length;
										for (int i = 0; i < diffSize; i++) {
											styles = styles + ",";
										}
									}
									// Fin de Debug
									layerStyleArray = styles.split(",");

									// Vérification des couches autorisées
									// *********************************
									int li = 0;
									boolean serverOK = false;
									String tmpFT = null;
									boolean cnt = true;
									boolean found = false;
									for (int jj = 0; jj < grsiList.size(); jj++) {
										for (int i = 0; i <= layerArray.size(); i++) {
											if (layerArray.size() == 0) {
												cnt = false;
												break;
											}
											tmpFT = layerArray.get(li);
											// int sindex =
											// tmpFTorj.indexOf(":") + 1;
											// tmpFT =
											// tmpFTorj.substring(sindex);

											if (layerToKeepList.size() > 0) {
												if (filter != null) {
													filter = null;
													cnt = false;
													break;
												}
												filter = getLayerFilter(getRemoteServerUrl(j), tmpFT);
												if (filter != null) {
													cnt = false;
													break;
												}
											}

											// Debug tb 03.07.2009
											newServerURL = grsiList.get(jj).getUrl();
											serverOK = isLayerAllowed(tmpFT, newServerURL);
											//HVH - 05.11.2010 : according to the WMS specification, all layers requested must be valid
											//So : if one is not allowed, the proxy returns an OGC Exception
											if (serverOK && tmpFT != null) {

												// Fin de Debug
												// Vérification que la couche de
												// la
												// req
												// est
												// autorisée par Policy

												String[] c = bbox.split(",");

												ReferencedEnvelope re;
												try
												{
												re = new ReferencedEnvelope(Double.parseDouble(c[0]), Double.parseDouble(c[2]), Double
														.parseDouble(c[1]), Double.parseDouble(c[3]), CRS.decode(srsName));
												}
												catch (Exception ex)
												{
													sendOgcExceptionBuiltInResponse(resp, generateOgcError("Invalid SRS given","InvalidSRS ","SRS",requestedVersion));
													return;
												}

												// Vérification que l'échelle de
												// la
												// requête est
												// autorisée
												if (isLayerInScale(tmpFT, newServerURL, RendererUtilities.calculateOGCScale(re, Integer.parseInt(width), null))) {
													serverOK = true;
												} else {
													dump("requestPreTraitementGET says: request Scale out of bounds, see the policy definition.");
													serverOK = false;
													layerArray.remove(li);
												}

												// Ajout de la couche et de son
												// sytle
												// associé,
												// si cette dernière est
												// autorisée
												// par
												// Policy
												if (serverOK) {
													if (layerStyleArray.length > i) {
														stylesToKeepList.add(layerStyleArray[i]);
													} else {
														stylesToKeepList.add("");
													}
													layerArray.remove(li);
													layerToKeepList.add(tmpFT);
													found = true;
													j = jj;
													lastServerURL = newServerURL;
													i--;
												}
											}
											else
											{
												//A requested layer is not allowed, the proxy returns an OGC exception
												sendOgcExceptionBuiltInResponse(resp,generateOgcError("Invalid layer(s) given in the LAYERS parameter","LayerNotDefined","layers",requestedVersion));
												return;
											}

											if ((layerToKeepList.size() > 0 && !serverOK && (newServerURL.equals(lastServerURL)))) {
												cnt = false;
												break;
											}
										}
										if (!cnt || layerArray.size() == 0)
											break;
									}
									if (!found && !layerArray.isEmpty()) {
										layerArray.remove(li);
									}
									// Vérfication de l'absence de "LAYER"
									// autorisées
									// restantes -> si vrai: la requête n'est
									// pas
									// envoyée
									if (layerToKeepList.size() <= 0) {
										sendRequest = false;
									}
									// Fin de Debug
								}
								// Debug tb 04.09.2009
								// Vérification de l'authorisation policy pour
								// la
								// couche
								// "LAYER" de l'opération GetLegendGraphic
							} 
							else if ("GetLegendGraphic".equalsIgnoreCase(operation) ) 
							{
								// Vérification que la couche de la req est
								// autorisée
								// par Policy
								String tmpFT = layerArray.get(0);
								layerArray.remove(0);
								for (int jj = 0; jj < grsiList.size(); jj++) {
									j = jj;
									boolean isLayerTypePermited = isLayerAllowed(tmpFT, getRemoteServerUrl(j));
									if (!isLayerTypePermited) {
										dump("requestPreTraitementGET says: GetLegendGraphic request Layer is not allowed by policy");
										sendRequest = isLayerTypePermited;
									} else {
										SendServerThread glgs = new SendServerThread(cpOperation, cpParamUrl, layerToKeepList, serverThreadList.size(),
												stylesToKeepList, cpParamUrlBase, j, null, null, cpFormat, resp);

										glgs.layerOrder = jj;
										glgs.start();
										serverThreadList.add(glgs);

									}
								}

								// Fin de Debug
							}
							else
							{
								//Not supported operation
								try {
									sendOgcExceptionBuiltInResponse(resp,generateOgcError("Operation not supported : "+operation,"OperationNotSupported ","request",requestedVersion));
									return;
								} catch (Exception e) {
									e.printStackTrace();
									dump("ERROR", e.getMessage());
									return;
								}
							}
							

							// Si pas de fichier Policy défini, envoi direct de
							// la
							// requête
							// sur le serveur j
							// else
							// {
							// //@TODO:Manage multiple servers when no policy is
							// existing.
							// if (layers!=null && layers.length()>0)
							// {
							// paramUrl="LAYERS="+layers+"&STYLES="+styles;
							// }
							// }

							// Si requête à envoyer sur serveur j
							if (sendRequest) {
								// Debug tb 08.07.2009
								// Nouvelle version des threads -> par serveur
								// ->
								// par couche
								// Création et lancement du thread sur serveur j
								// Copie des strings pour utlisation dans
								// threads.
								// Originales rééctrites dans boucle serveur
								// courante!
								if (layers != null && operation.equalsIgnoreCase("getmap") || operation.equalsIgnoreCase("getfeatureinfo")) {
									List<String> layersTabWithNS = Arrays.asList(layers.split(","));
									List<String> layersTab = new ArrayList<String>();
									for (String layerName : layersTabWithNS) {
										// String[] layerNameFinal =
										// layerName.split(":", 2);
										// layersTab.add((layerNameFinal.length
										// > 1) ? layerNameFinal[1] :
										// layerNameFinal[0]);
										layersTab.add(layerName);
									}

									if (!configuration.isGrouping() && layerToKeepList.size() > 0) {
										for (String layerToKeepElement : layerToKeepList) {
											List<String> singleLayerList = new ArrayList<String>();
											singleLayerList.add(layerToKeepElement);
											SendServerThread s = new SendServerThread(cpOperation, cpParamUrl, singleLayerList, serverThreadList.size(),
													stylesToKeepList, cpParamUrlBase, j, cpWidth, cpHeight, cpFormat, resp);
											s.layerOrder = layersTab.indexOf(layerToKeepElement);

											s.start();
											serverThreadList.add(s);

										}
									} else {
										SendServerThread s = new SendServerThread(cpOperation, cpParamUrl, layerToKeepList, serverThreadList.size(),
												stylesToKeepList, cpParamUrlBase, j, cpWidth, cpHeight, cpFormat, resp);

										s.layerOrder = layerOrder;
										layerOrder++;
										s.start();
										serverThreadList.add(s);

									}
								}
							}
							// Si pas de requête à envoyer sur serveur j:
							// sendRequest=false
							else {
								sendRequest = true;
								if (("GetMap".equalsIgnoreCase(operation) || "map".equalsIgnoreCase(operation) || "GetLegendGraphic"
										.equalsIgnoreCase(operation))) {
									dump("requestPreTraitementGET save response server " + getRemoteServerUrl(j)
											+ ": emptyImage. Proxy bloc the request to this server due to policy config.");
									generateEmptyImage(width, height, format, true, j, resp);
								}
							}
						}
					}
				}
			}
			// Sortie de la boucle des
			// serveurs*************************************************

			// Attente de l'arrivée des résultats des threads sur chaque serveur
			// avant de passer à la suite du traitement
			for (int i = 0; i < serverThreadList.size(); i++) {
				serverThreadList.get(i).join();

				// les réponses ont été insérées, par les threads servers, dans
				// wmsFilePathList;
				// layerFilePathList-> layer names et serverUrlPerFilePathList->
				// url server, ont aussi été mis à jour en conséquence
			}

			// Debug tb 15.01.2010
			// Si aucun des layerThread n'a passé de requête, car policy filter
			// et req bbox incompatibles
			// if(wmsFilePathList.size()<=0 &&
			// ("GetMap".equalsIgnoreCase(operation) ||
			// "map".equalsIgnoreCase(operation)))
			// {
			// sendRequest=true;
			// dump("requestPreTraitementGET save response servers: emptyImage");
			// generateEmptyImage(width,height,format,true);
			// }
			// Fin de la phase de reconstruction de la requête: wmsFilePathList
			// contient les réponses de chaque serveur (une par serveur)
			// *****************************************************************************************************************************

			// *****************************************************************************************************************************
			if (wmsFilePathList.size() > 0) {
				// Lancement du post traitement
				version = version.replaceAll("\\.", "");

				dump("requestPreTraitementGET begin transform");
				transform(version, operation, req, resp);
				dump("requestPreTraitementGET end transform");
				// Fin du post traitement
				// *****************************************************************************************************************************
			} else {
				dump("ERROR", "This request has no authorized results!");
			}
			// Fin de Debug
		} catch (AvailabilityPeriodException e) {
			dump("ERROR", e.getMessage());
			resp.setHeader("easysdi-proxy-error-occured", "true");
			sendOgcExceptionBuiltInResponse(resp,generateOgcError(e.getMessage(),"OperationNotSupported","request",requestedVersion));
			
//			dump("ERROR", e.getMessage());
//			resp.setStatus(401);
//			try {
//				resp.getWriter().println(e.getMessage());
//			} catch (IOException e1) {
//				resp.setHeader("easysdi-proxy-error-occured", "true");
//				e1.printStackTrace();
//			}
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.toString());
			sendOgcExceptionBuiltInResponse(resp,generateOgcError("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
		}
	}

	// ***************************************************************************************************************************************

	private void generateEmptyImage(String width, String height, String format, boolean isTransparent, int j, HttpServletResponse resp) {
		// In the case of a GetMap, it should returns an empty image
		try {
			BufferedImage imgOut = null;
			if (isTransparent) {
				imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.BITMASK);
			} else {
				imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.TYPE_INT_ARGB);
			}
			responseContentType = URLDecoder.decode(format);
			Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(responseContentType);

			if (iter.hasNext()) {
				ImageWriter writer = (ImageWriter) iter.next();
				File tempFile = createTempFile(UUID.randomUUID().toString(), getExtension(responseContentType));
				FileImageOutputStream output = new FileImageOutputStream(tempFile);
				writer.setOutput(output);
				writer.write(imgOut);
				String filePath = tempFile.getPath();
				wmsFilePathList.put(j, filePath);
				layerFilePathList.put(j, "");
			}
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
		}
	}

	// ***************************************************************************************************************************************

	/*
	 * envelope contains the envelope of the whole image
	 */
	private BufferedImage imageFiltering(BufferedImage imageSource, CRSEnvelope envelope, Geometry polygonFilter, boolean isTransparent,
			HttpServletResponse resp) {
		try {
			System.setProperty("org.geotools.referencing.forceXY", "true");
			// System.setProperty(
			// (Hints.FORCE_STANDARD_AXIS_DIRECTIONS.toString()), "true" );

			// Transform the srs of the filter if needed.
			String srsName = envelope.getEPSGCode();
			CoordinateReferenceSystem crs = CRS.decode(srsName);

			CoordinateReferenceSystem sourceCRS = CRS.decode("EPSG:" + (new Integer(polygonFilter.getSRID())).toString());
			CoordinateReferenceSystem targetCRS = CRS.decode(envelope.getEPSGCode());

			MathTransform a = CRS.findMathTransform(sourceCRS, targetCRS, false);

			polygonFilter = JTS.transform(polygonFilter, a);

			try {
				for (int i = 0; i < crs.getIdentifiers().size(); i++) {
					if (((NamedIdentifier) crs.getIdentifiers().toArray()[i]).getCodeSpace().equals("EPSG")) {
						polygonFilter.setSRID(Integer.parseInt(((NamedIdentifier) crs.getIdentifiers().toArray()[i]).getCode()));
						break;
					}
				}
			} catch (Exception e) {
				resp.setHeader("easysdi-proxy-error-occured", "true");
				e.printStackTrace();
			}

			final GeometryAttributeType geom = new GeometricAttributeType("Geom", Geometry.class, false, null, crs, null);
			final AttributeType attr1 = AttributeTypeFactory.newAttributeType("COLOR", String.class);
			final AttributeType[] attributes = new AttributeType[] { attr1, geom };

			final FeatureType schema = FeatureTypes.newFeatureType(attributes, "TEMPORARYFEATURE", new URI("depth.ch"), false, null, geom);

			// Construction du masque sur la base de l'enveloppe de la bbox et
			// du polygon de filtre
			FeatureRasterizer fr = new FeatureRasterizer(imageSource.getHeight(), imageSource.getWidth());
			double width = envelope.getMaxX() - envelope.getMinX();
			double height = envelope.getMaxY() - envelope.getMinY();
			;
			Rectangle2D.Double bounds = new Rectangle2D.Double(envelope.getMinX(), envelope.getMinY(), width, height);

			fr.setBounds(bounds);
			fr.setAttName("COLOR");

			fr.addFeature(schema.create(new Object[] { Integer.toString(Color.WHITE.getRGB()), polygonFilter }));

			// Construction de l'image de masquage
			BufferedImage bimage2 = fr.getBimage();
			int imageType = BufferedImage.TYPE_INT_RGB;
			if (isTransparent) {
				imageType = BufferedImage.TYPE_INT_ARGB;
			}

			// "dimg" contient l'image source et "bimage2" est utilisé comme
			// masque.
			BufferedImage dimg = new BufferedImage(imageSource.getWidth(), imageSource.getHeight(), imageType);
			Graphics2D g = dimg.createGraphics();
			g.setComposite(AlphaComposite.Src);
			g.drawImage(imageSource, null, 0, 0);
			g.dispose();
			for (int i = 0; i < bimage2.getHeight(); i++) {
				for (int j = 0; j < bimage2.getWidth(); j++) {
					if (bimage2.getRGB(j, i) == 0) {
						// dimg.setRGB(j, i, 0x8F1C1C);
						dimg.setRGB(j, i, 0xFFFFFF);
					}
				}
			}

			// Une fois le masque appliqué sur l'image source, renvoyé l'image
			// filtrée
			return dimg;
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}

		return imageSource;
	}

	// ***************************************************************************************************************************************

}
