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
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.PrintWriter;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.net.URI;
import java.net.URLDecoder;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;
import java.util.UUID;
import java.util.Vector;
import java.util.Map.Entry;
import java.util.logging.Level;

import javax.imageio.ImageIO;
import javax.imageio.ImageWriter;
import javax.imageio.stream.FileImageOutputStream;
import javax.imageio.stream.MemoryCacheImageOutputStream;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathFactory;

import org.apache.xerces.dom.DeferredElementImpl;
import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.core.ProxyRemoteServerResponse;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.policy.BoundingBox;
import org.easysdi.proxy.policy.Layer;
import org.easysdi.proxy.policy.Server;
import org.easysdi.proxy.wms.thread.WMSProxyServerGetCapabilitiesThread;
import org.easysdi.proxy.wms.thread.WMSProxyServerGetFeatureInfoThread;
import org.easysdi.proxy.wms.thread.WMSProxyServerGetMapThread;
import org.easysdi.proxy.wms.WMSProxyResponseBuilder;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.easysdi.xml.resolver.ResourceResolver;
import org.geotools.data.ows.CRSEnvelope;
import org.geotools.feature.AttributeType;
import org.geotools.feature.AttributeTypeFactory;
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
import org.integratedmodelling.geospace.gis.FeatureRasterizer;
import org.jdom.JDOMException;
import org.jdom.Namespace;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.opengis.referencing.FactoryException;
import org.opengis.referencing.NoSuchAuthorityCodeException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.operation.MathTransform;
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

import com.google.common.collect.HashMultimap;
import com.google.common.collect.Multimap;
import com.vividsolutions.jts.geom.Envelope;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.io.WKTReader;


/**
 * 
 * @author DEPTH SA
 */
public class WMSProxyServlet extends ProxyServlet {

	/**
	 * 
	 */
	private static final long serialVersionUID = -6946633057100675490L;
	
	/**
	 * Store all the possible operations for a WMS service
	 * Used in buildCapabilitiesXSLT()
	 */
	private String[] WMSOperation = { "GetCapabilities", "GetMap", "GetFeatureInfo", "DescribeLayer", "GetLegendGraphic", "PutStyles", "GetStyles" };

	/**
	 * Url du serveur ayant renvoyé la réponse i.
	 */
	public Map<Integer, String> serverUrlPerfilePathList = new TreeMap<Integer, String>(); 

	/**
	 * Fill by the WMSProxyServletGetMapThread with
	 * <index of layer in the request,<path,alias>>
	 */
	public TreeMap<Integer, ProxyRemoteServerResponse> wmsGetMapResponseFilePathMap = new TreeMap<Integer, ProxyRemoteServerResponse>();
	
	/**
	 * Fill by the WMSProxyServletGetFEatureInfoThread with
	 * <index of layer in the request,<path,alias>>
	 */
	public TreeMap<Integer, ProxyRemoteServerResponse> wmsGetFeatureInfoResponseFilePathMap = new TreeMap<Integer, ProxyRemoteServerResponse>();
	
	/**
	 * Fill by the WMSProxyServletGetCapabilitiesThread with
	 * <alias,path>
	 */
	public HashMap<String, String> wmsGetCapabilitiesResponseFilePathMap = new HashMap<String, String>();
	
	/**
	 * Fill by the GetLegendGraphic with
	 * <alias,path>
	 */
	public HashMap<String, String> wmsGetLegendGraphicResponseFilePathMap = new HashMap<String, String>();
	
	/**
	 * 
	 */
	protected WMSProxyResponseBuilder docBuilder;
	

	/**
	 * @return the proxyRequest
	 */
	public WMSProxyServletRequest getProxyRequest() {
		return (WMSProxyServletRequest)proxyRequest;
	}

	/**
	 * Constructor
	 */
	public WMSProxyServlet ()
	{
		super();
		ServiceOperations = Arrays.asList( "GetCapabilities", "GetMap", "GetFeatureInfo", "DescribeLayer", "GetLegendGraphic", "PutStyles", "GetStyles" );
	}
	
	protected StringBuffer generateOgcException(String errorMessage, String code, String locator, String version) {
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
//			String user = "";
//			if (SecurityContextHolder.getContext().getAuthentication() != null) {
//				user = SecurityContextHolder.getContext().getAuthentication().getName();
//			}

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
				String prefixe = ""; 
				if("130".equalsIgnoreCase(version))
					prefixe = "wms:";

				WMSCapabilities111
						.append("<xsl:stylesheet version=\"1.00\" " +
								"xmlns:wms=\"http://www.opengis.net/wms\" " +
								"xmlns:ows=\"http://www.opengis.net/ows\" " +
								"xmlns:sld=\"http://www.opengis.net/sld\" " + 
								"xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "+ 
								"xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" " +
								"xsi:schemaLocation=\"http://www.opengis.net/wms http://schemas.opengeospatial.net/wms/1.3.0/capabilities_1_3_0.xsd  http://www.opengis.net/sld http://schemas.opengis.net/sld/1.1.0/sld_capabilities.xsd  http://mapserver.gis.umn.edu/mapserver http://geoservices.brgm.fr/geologie?service=WMS&amp;version=1.3.0&amp;request=GetSchemaExtension\" " +
								"xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
				WMSCapabilities111.append("<xsl:output method=\"xml\" omit-xml-declaration=\"no\" version=\"1.0\" encoding=\"UTF-8\" indent=\"yes\"/>");
				
				//HVH-10.12.2010 : WMS 1.3.0 --> namespace wms 
				if (!"100".equalsIgnoreCase(version)) {
					// Debug tb 19.11.2009
					WMSCapabilities111.append("<xsl:template match=\"//"+prefixe+"OnlineResource/@xlink:href\">");
					WMSCapabilities111.append("<xsl:param name=\"thisValue\">");
					WMSCapabilities111.append("<xsl:value-of select=\".\"/>");
					WMSCapabilities111.append("</xsl:param>");
					WMSCapabilities111.append("<xsl:attribute name=\"xlink:href\">");
					WMSCapabilities111.append(url);
					// Changer seulement la partie racine de l'URL, pas les param après '?'
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
					// Changer seulement la partie racine de l'URL, pas les param après '?'
					WMSCapabilities100.append("<xsl:value-of select=\"substring-after($thisValue,'" + getRemoteServerUrl(remoteServerIndex) + "')\"/>");
					WMSCapabilities100.append("</OnlineResource>");
					WMSCapabilities100.append("</xsl:template>");
					WMSCapabilities100.append("<xsl:template match=\"@onlineResource\">");
					WMSCapabilities100.append("<xsl:param name=\"thisValue\">");
					WMSCapabilities100.append("<xsl:value-of select=\".\"/>");
					WMSCapabilities100.append("</xsl:param>");
					WMSCapabilities100.append("<xsl:attribute name=\"onlineResource\">");
					WMSCapabilities100.append(url);
					// Changer seulement la partie racine de l'URL, pas les param après '?'
					WMSCapabilities100.append("<xsl:value-of select=\"substring-after($thisValue,'" + getRemoteServerUrl(remoteServerIndex) + "')\"/>");
					WMSCapabilities100.append("</xsl:attribute>");
					WMSCapabilities100.append("</xsl:template>");
					WMSCapabilities111.append(WMSCapabilities100);
				}
				// Fin de Debug

				// Filtrage xsl des opérations
				//HVH-30.08.2010 : replace old version which did not work				
				if (!policy.getOperations().isAll() || deniedOperations.size() > 0 ) {
					Iterator<String> it = permitedOperations.iterator();
					while (it.hasNext()) {
						String text = it.next();
						if (text != null) {
							WMSCapabilities111.append("<xsl:template match=\""+prefixe+"Capability/Request/");
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
						WMSCapabilities111.append("<xsl:template match=\""+prefixe+"Capability/wms:Request/");
						WMSCapabilities111.append(it.next());
						WMSCapabilities111.append("\"></xsl:template>");
					}
				}
				if (permitedOperations.size() == 0 )
				{
					WMSCapabilities111.append("<xsl:template match=\"wms:Capability/"+prefixe+"Request/\"></xsl:template>");
				}
				
				SAXBuilder saxBuilder = new SAXBuilder();
				org.jdom.Document jDoc = saxBuilder.build(new File(wmsFilePathList.get(remoteServerIndex).toArray(new String[1])[0]));
				Filter filtre = new WMSProxyCapabilitiesLayerFilter();
    			Iterator itL = jDoc.getDescendants(filtre);
    			while(itL.hasNext())
				{
    				org.jdom.Element layer = (org.jdom.Element)itL.next();
    				org.jdom.Element layerName;
    				if ("130".equalsIgnoreCase(version)) 
    					layerName = layer.getChild("Name", Namespace.getNamespace("http://www.opengis.net/wms"));
    				else
    					layerName = layer.getChild("Name");
    				if(layerName == null)
    					continue;
    				boolean allowed = isLayerAllowed(layerName.getValue(), getRemoteServerUrl(remoteServerIndex));
					if (!allowed) {
						// Si couche pas permise alors on l'enlève
						WMSCapabilities111.append("<xsl:template match=\"//"+prefixe+"Layer[starts-with("+prefixe+"Name,'" + layerName.getValue() + "')]");
						WMSCapabilities111.append("\"></xsl:template>");
					}
				}
				
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
			String prefixe = ""; 
			if("130".equalsIgnoreCase(version))
				prefixe = "wms:";
			
			StringBuffer serviceMetadataXSLT = new StringBuffer();
			serviceMetadataXSLT.append("<xsl:stylesheet version=\"1.00\" " +
					"xmlns:wms=\"http://www.opengis.net/wms\" " +
					"xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" " +
					"xmlns:ows=\"http://www.opengis.net/ows\" " +
					"xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
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
			serviceMetadataXSLT.append("<xsl:template match=\""+prefixe+"Service\">");
			serviceMetadataXSLT.append("<xsl:copy>");
			//Name
			serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"Name\"> ");
			serviceMetadataXSLT.append("<xsl:text>WMS</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
			//Title
			serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"Title\"> ");
			serviceMetadataXSLT.append("<xsl:text>" + getConfiguration().getTitle() + "</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
			//Abstract
			if(getConfiguration().getAbst()!=null)
			{
				serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"Abstract\"> ");
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
					serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"KeywordList\"> ");
					for (int n = 0; n < keywords.size(); n++) {
						serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"Keyword\"> ");
						serviceMetadataXSLT.append("<xsl:text>" + keywords.get(n) + "</xsl:text>");
						serviceMetadataXSLT.append("</xsl:element>");
					}
					serviceMetadataXSLT.append("</xsl:element>");
				}
			}
			//OnlineResource
			serviceMetadataXSLT.append("<xsl:copy-of select=\""+prefixe+"OnlineResource\"/>");
			//contactInfo
			if(!"100".equals(version))
			{
				if(getConfiguration().getContactInfo()!= null && !getConfiguration().getContactInfo().isEmpty())
				{
					serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"ContactInformation\"> ");
					
						serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"ContactPersonPrimary\"> ");
						if(configuration.getContactInfo().getName()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"ContactPerson\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getName() + "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getOrganization()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"ContactOrganization\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getOrganization()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						serviceMetadataXSLT.append("</xsl:element>");
						if(configuration.getContactInfo().getPosition()!=null){
							serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"ContactPosition\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getPosition()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if (!configuration.getContactInfo().getContactAddress().isEmpty())
						{
							serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"ContactAddress\"> ");
							if(configuration.getContactInfo().getContactAddress().getType()!=null){
								serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"AddressType\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getType()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							if(configuration.getContactInfo().getContactAddress().getAddress()!=null){
								serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"Address\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getAddress()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							if(!configuration.getContactInfo().getContactAddress().getCity().equals("")){
								serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"City\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getCity()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							if(configuration.getContactInfo().getContactAddress().getState()!=null){
								serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"StateOrProvince\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getState()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							if(configuration.getContactInfo().getContactAddress().getPostalCode()!=null){
								serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"PostCode\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getPostalCode()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							if(configuration.getContactInfo().getContactAddress().getCountry()!=null){
								serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"Country\"> ");
								serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getContactAddress().getCountry()+ "</xsl:text>");
								serviceMetadataXSLT.append("</xsl:element>");
							}
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getVoicePhone()!=null)
						{
							serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"ContactVoiceTelephone\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getVoicePhone()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().getFacSimile()!=null)
						{
							serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"ContactFacsimileTelephone\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().getFacSimile()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
						if(configuration.getContactInfo().geteMail()!=null)
						{
							serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"ContactElectronicMailAddress\"> ");
							serviceMetadataXSLT.append("<xsl:text>" + configuration.getContactInfo().geteMail()+ "</xsl:text>");
							serviceMetadataXSLT.append("</xsl:element>");
						}
					
					serviceMetadataXSLT.append("</xsl:element>");
				}
			}
			//Fees
			serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"Fees\"> ");
			serviceMetadataXSLT.append("<xsl:text>" + getConfiguration().getFees() + "</xsl:text>");
			serviceMetadataXSLT.append("</xsl:element>");
			//AccesConstraints
			serviceMetadataXSLT.append("<xsl:element name=\""+prefixe+"AccessConstraints\"> ");
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
			
			Collection<Map.Entry<Integer,String>> r =  wmsFilePathList.entries();
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
						NodeList nl = documentMaster.getElementsByTagName("ServiceExceptionReport");
						if (nl.item(0) != null)
						{
							toRemove.put(entry.getKey(), path);
//							ogcExceptionFilePathList.put(entry.getKey(), path);
//							wmsFilePathList.remove(entry.getKey(), path);
						}
					}
				}
			}
			
			Iterator<Map.Entry<Integer,String>> itR = toRemove.entries().iterator();
			while(itR.hasNext())
			{
				Map.Entry<Integer,String> entry = (Map.Entry<Integer,String>)itR.next();
				
				ogcExceptionFilePathList.put(entry.getKey(), entry.getValue());
				wmsFilePathList.remove(entry.getKey(), entry.getValue());
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

	private org.jdom.Element getElementLayerName (org.jdom.Element elementLayer, Namespace wmsNS, String version)
	{
		if(("130").equalsIgnoreCase(version))
			return (org.jdom.Element)elementLayer.getChild("Name",wmsNS);
		else
			return (org.jdom.Element)elementLayer.getChild("Name");
			
	}
	
	private boolean rewriteBBOX(List<org.jdom.Element> layersList,CoordinateReferenceSystem wgsCRS,CoordinateReferenceSystem parentCRS, String version)
	{
		String wgsMaxx;
		String wgsMaxy;
		String wgsMinx;
		String wgsMiny;
		Namespace wmsNS = Namespace.getNamespace("http://www.opengis.net/wms");
		
		for(int l = 0 ; l < layersList.size();l++)
		{
    		org.jdom.Element elementLayer = (org.jdom.Element)layersList.get(l);
    		
    		List<Server> serverList = policy.getServers().getServer();
    		if(getElementLayerName(elementLayer,wmsNS,version) == null)
    		{
    			continue;
    		}
    		for (int i=0 ; i < serverList.size() ; i++)
    		{
    			try
    			{
	    			Server server = serverList.get(i);
	    			Layer currentLayer ;
	    			currentLayer = server.getLayers().getLayerByName(getElementLayerName(elementLayer,wmsNS,version).getValue());
	    			
	    			if(currentLayer == null)
	    				continue;
	    			
	    			if(currentLayer.getBoundingBox() == null)
	    				continue;
	    			
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
	    			
	    			if(version.equalsIgnoreCase("130"))
	    			{
	    				if ( !writeLatLonBBOX130(elementLayer, wgsMinx, wgsMiny,wgsMaxx, wgsMaxy, wmsNS)) return false;
	    			}
	    			else
	    			{
	    				if( !writeLatLonBBOX111(elementLayer, wgsMinx, wgsMiny,wgsMaxx, wgsMaxy)) return false;
	    			}
	    			parentCRS = writeCRSBBOX(elementLayer,srsBBOX,wgsCRS,parentCRS,wgsMinx,wgsMiny,wgsMaxx,wgsMaxy,wmsNS,version);
	    			
	    			Filter filtre = new WMSProxyCapabilitiesLayerFilter();
	    			Iterator itL = elementLayer.getDescendants(filtre);
	    			List<org.jdom.Element> sublayersList = new ArrayList<org.jdom.Element>();
			    	while(itL.hasNext())
					{
			    		sublayersList.add((org.jdom.Element)itL.next());
					}
			    	if(sublayersList.size() != 0)
			    		if ( !rewriteBBOX(sublayersList, wgsCRS,parentCRS, version) ) return false;
    			}
    			catch (Exception e)
    			{
    				dump("ERROR", e.toString());
    				return false;
    			}
    		}
		}
		return true;
	}
	
	private boolean writeLatLonBBOX130(org.jdom.Element elementLayer, String wgsMinx, String wgsMiny, String wgsMaxx, String wgsMaxy, Namespace wmsNS)
	{
		try 
		{
			List llBBOXElements = elementLayer.getChildren("EX_GeographicBoundingBox",wmsNS);
			if(llBBOXElements.size() != 0)
			{
	    		org.jdom.Element llBBOX = (org.jdom.Element) llBBOXElements.get(0);
	    		llBBOX.getChild("westBoundLongitude",wmsNS).setText(wgsMiny);
	    		llBBOX.getChild("eastBoundLongitude",wmsNS).setText(wgsMaxy);
	    		llBBOX.getChild("southBoundLatitude",wmsNS).setText(wgsMinx);
	    		llBBOX.getChild("northBoundLatitude",wmsNS).setText(wgsMaxx);
			}
			else
			{
				//create element
				org.jdom.Element element = new org.jdom.Element("EX_GeographicBoundingBox",wmsNS);
				org.jdom.Element wbl = new org.jdom.Element("westBoundLongitude",wmsNS);
				wbl.setText(wgsMiny);
				org.jdom.Element ebl = new org.jdom.Element("eastBoundLongitude",wmsNS);
				ebl.setText(wgsMaxy);
				org.jdom.Element sbl = new org.jdom.Element("southBoundLatitude",wmsNS);
				sbl.setText(wgsMinx);
				org.jdom.Element nbl = new org.jdom.Element("northBoundLatitude",wmsNS);
				nbl.setText(wgsMaxx);
				element.addContent(wbl );
				element.addContent(ebl );
				element.addContent(sbl );
				element.addContent(nbl );
				elementLayer.addContent(element);
	   		}
			return true;
		}
		catch (Exception e)
		{
			dump("ERROR", e.toString());
			return false;
		}
	}
	
	private boolean writeLatLonBBOX111(org.jdom.Element elementLayer, String wgsMinx, String wgsMiny, String wgsMaxx, String wgsMaxy)
	{
		try
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
			return true;
		}
		catch (Exception e)
		{
			dump("ERROR", e.toString());
			return false;
		}
	}
	
	private List<org.jdom.Element> getElementBoundingBox (org.jdom.Element elementLayer, Namespace wmsNS, String version)
	{
		if(("130").equalsIgnoreCase(version))
			return elementLayer.getChildren("BoundingBox", wmsNS);
		else
			return elementLayer.getChildren("BoundingBox"); 
			
	}
	private org.jdom.Element createElementBoundingBox (Namespace wmsNS, String version)
	{
		if(("130").equalsIgnoreCase(version))
			return new org.jdom.Element("BoundingBox",wmsNS);
		else
			return new org.jdom.Element("BoundingBox");
			
	}
	private String getAttributeSRS (Namespace wmsNS, String version)
	{
		if(("130").equalsIgnoreCase(version))
			return "CRS";
		else
			return "SRS";
			
	}
	
	private CoordinateReferenceSystem writeCRSBBOX(org.jdom.Element elementLayer,BoundingBox srsBBOX,CoordinateReferenceSystem wgsCRS,CoordinateReferenceSystem parentCRS,String wgsMinx, String wgsMiny, String wgsMaxx, String wgsMaxy, Namespace wmsNS,String version)
	{
		try
		{
			List srsBBOXElements = getElementBoundingBox(elementLayer, wmsNS, version);
			if(srsBBOXElements.size() == 0)
			{
				//No BoundingBox for specific SRS : get the SRS parent to create one --> Needed only for WFS 1.3.0 but
				if(parentCRS == null)
					return null;
				MathTransform transform = CRS.findMathTransform(wgsCRS, parentCRS);
				Envelope sourceEnvelope = new Envelope(Double.valueOf(wgsMinx),Double.valueOf(wgsMaxx),Double.valueOf(wgsMiny),Double.valueOf(wgsMaxy));
				Envelope targetEnvelope = JTS.transform( sourceEnvelope, transform);
			
				org.jdom.Element BBOX = createElementBoundingBox(wmsNS,version);
				BBOX.setAttribute(getAttributeSRS(wmsNS,version), parentCRS.getIdentifiers().toArray()[0].toString());
				BBOX.setAttribute("minx", String.valueOf(targetEnvelope.getMinX()));
				BBOX.setAttribute("miny", String.valueOf(targetEnvelope.getMinY()));
				BBOX.setAttribute("maxx", String.valueOf(targetEnvelope.getMaxX()));
				BBOX.setAttribute("maxy", String.valueOf(targetEnvelope.getMaxY()));
				elementLayer.addContent(BBOX);
				return parentCRS;
			}
			else
			{
				for (int j = 0 ; j < srsBBOXElements.size() ; j++)
				{
					org.jdom.Element BBOX = (org.jdom.Element) srsBBOXElements.get(j);
					String attSRS = getAttributeSRS(wmsNS,version);
					if(BBOX.getAttributeValue(attSRS).equals(srsBBOX.getSRS()))
					{
						if(j == 0)
						{
							parentCRS = CRS.decode(BBOX.getAttributeValue(attSRS));
						}
						BBOX.setAttribute("minx", srsBBOX.getMinx());
						BBOX.setAttribute("miny", srsBBOX.getMiny());
						BBOX.setAttribute("maxx", srsBBOX.getMaxx());
						BBOX.setAttribute("maxy", srsBBOX.getMaxy());
					}
					else
					{
						CoordinateReferenceSystem targetCRS =CRS.decode(BBOX.getAttributeValue(attSRS));
						if(j == 0)
						{
							parentCRS = targetCRS;
						}
						MathTransform transform = CRS.findMathTransform(wgsCRS, targetCRS);
						Envelope sourceEnvelope = new Envelope(Double.valueOf(wgsMinx),Double.valueOf(wgsMaxx),Double.valueOf(wgsMiny),Double.valueOf(wgsMaxy));
						Envelope targetEnvelope = JTS.transform( sourceEnvelope, transform);
						BBOX.setAttribute("minx", String.valueOf(targetEnvelope.getMinX()));
						BBOX.setAttribute("miny", String.valueOf(targetEnvelope.getMinY()));
						BBOX.setAttribute("maxx", String.valueOf(targetEnvelope.getMaxX()));
						BBOX.setAttribute("maxy", String.valueOf(targetEnvelope.getMaxY()));
					}
				}
			}
			return parentCRS;
		}
		catch (Exception e)
		{
			dump("ERROR", e.toString());
			return parentCRS;
		}
	}

	public void transform(String version, String currentOperation, HttpServletRequest req, HttpServletResponse resp) {
		try 
		{
			String responseVersion="";
			
			/**
			 * Exception mangement
			 */
			//Filtre les fichiers réponses des serveurs :
			//ajoute les fichiers d'exception dans ogcExceptionFilePathList
			//les enlève de la collection de résultats wmsFilePathList 
			if(!filterServersResponsesForOgcServiceExceptionFiles())
			{
				sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in OGC exception management. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
				return;
			}
			
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
				dump("INFO","Exception(s) returned by remote server(s) are sent to client.");
				responseContentType ="text/xml; charset=utf-8";
				ByteArrayOutputStream exceptionOutputStream = buildResponseForOgcServiceException();
				sendHttpServletResponse(req,resp,exceptionOutputStream, responseContentType, HttpServletResponse.SC_OK);
				return;
			}
			/***/
			
			/**
			 * XSLT 
			 */
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
			/***/
			
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
						//Load response file
						InputSource iS = new InputSource(new FileInputStream(wmsFilePathList.get(iFilePath).toArray(new String[1])[0]));
						
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
								sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in BoundingBox calculation.","NoApplicableCode","",requestedVersion));
								return;
							}
						} catch (NoSuchAuthorityCodeException e1) {
							dump("ERROR","Exception when trying to load SRS EPSG:4326 : "+e1.getMessage());
							sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in BoundingBox calculation.","NoApplicableCode","",requestedVersion));
							return;
						} catch (FactoryException e1) {
							dump("ERROR",e1.getMessage());
							sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in BoundingBox calculation.","NoApplicableCode","",requestedVersion));
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
				// Pour une requête utilisateur de type: Map
				// ************************************************************
				else if (currentOperation.equalsIgnoreCase("GetMap") || "Map".equalsIgnoreCase(currentOperation)) {
					dump("transform begin GetMap operation");

					//Debug - HVH 17.12.2010 : if an OGC XML exception was returned by a remote server, 
					//the responseContentType variable can have a wrong value (it can contain : "text/xml").
					//If the exception has to be returned, this is already done before in the code.
					//If this code section is reached, that means an image has to be returned so we loop in all the contentType of
					//the responses received to find one different from text/xml
					//and we use it as the contentType of the response to return.
					Iterator<String> itL = responseContentTypeList.iterator();
					while (itL.hasNext()) {
						responseContentType = (String) itL.next();
						if(!isXML(responseContentType))
							break;
//						if(!responseContentType.contains("xml"))
//						{
//							break;
//						}
					} 
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
			sendHttpServletResponse(req,resp,tempOut, responseContentType,  HttpServletResponse.SC_OK);
			// No post rule to apply. Copy the file result on the output stream


		} 
		catch (SAXParseException e)
		{
			e.printStackTrace();
			dump("ERROR", e.getMessage());
			sendOgcExceptionBuiltInResponse(resp,generateOgcException("Response format not recognized. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
		}
		catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.toString());
			sendOgcExceptionBuiltInResponse(resp,generateOgcException("Error in EasySDI Proxy. Consult the proxy log for more details.","NoApplicableCode","",requestedVersion));
		}
	}


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

	/**
	 * 
	 */
	protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp) {
		this.requestPreTreatmentGET(req, resp);
	}

	/**
	 * 
	 */
	protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp) {
		
		try {
			//Generate OGC exception and send it to the client if current operation is not supported by the current version of the EasySDI proxy
			if(!isOperationSupportedByProxy(getProxyRequest().getOperation()))
			{
				StringBuffer out;
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED,OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED,"REQUEST");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
				return;
			}
			//Generate OGC exception and send it to the client if current operation is not allowed by the loaded policy
			if(!isOperationAllowedByPolicy(getProxyRequest().getOperation()))
			{
				StringBuffer out;
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_OPERATION_NOT_ALLOWED,OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED,"REQUEST");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
				return;
			}
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		Method preTreatmentMethod;
		try {
			if(getProxyRequest().getOperation().equals("GetCapabilities")){
				requestPreTreatmentGetCapabilities(req, resp);
			}else{
				preTreatmentMethod = this.getClass().getMethod("requestPreTreatment"+getProxyRequest().getOperation(), new Class [] {Class.forName ("javax.servlet.http.HttpServletRequest"), Class.forName ("javax.servlet.http.HttpServletResponse")});
				preTreatmentMethod.invoke(this ,new Object[] {req,resp});
			}
			
		} catch (SecurityException e2) {
			// TODO Auto-generated catch block
			e2.printStackTrace();
		} catch (NoSuchMethodException e2) {
			// Operation NotSupported
			e2.printStackTrace();
		} catch (IllegalArgumentException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		} catch (IllegalAccessException e1) {
			// TODO Auto-generated catch block
			resp.setHeader("easysdi-proxy-error-occured", "true");
			dump("ERROR", e1.toString());
			e1.printStackTrace();
		} catch (InvocationTargetException e1) {
			// TODO Auto-generated catch block
			resp.setHeader("easysdi-proxy-error-occured", "true");
			dump("ERROR", e1.toString());
			e1.printStackTrace();
		} catch (ClassNotFoundException e) {
			// TODO Auto-generated catch block
			resp.setHeader("easysdi-proxy-error-occured", "true");
			dump("ERROR", e.toString());
			e.printStackTrace();
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
			} catch (IOException e1) {
				dump("ERROR", e1.toString());
				e1.printStackTrace();
			}
		}
	}

	/**
	 * @param req
	 * @param resp
	 */
	public void requestPreTreatmentGetCapabilities (HttpServletRequest req, HttpServletResponse resp){
		List<WMSProxyServerGetCapabilitiesThread> serverThreadList = new Vector<WMSProxyServerGetCapabilitiesThread>();
		Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();
		Iterator<Entry<String, RemoteServerInfo>> it =remoteServersTable.entrySet().iterator();
		
		while(it.hasNext())
		{
			WMSProxyServerGetCapabilitiesThread s = new WMSProxyServerGetCapabilitiesThread(this,
																							getProxyRequest().getUrlParameters(), 
																							it.next().getValue(), 
																							resp);

			s.start();
			serverThreadList.add(s);
		}
		
		// Wait for thread results
		for (int i = 0; i < serverThreadList.size(); i++) {
			try {
				serverThreadList.get(i).join();
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}

		if (wmsGetCapabilitiesResponseFilePathMap.size() > 0) {
			dump("requestPreTraitementGET begin transform");
			transformGetCapabilities(req, resp);
			dump("requestPreTraitementGET end transform");
		} else {
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_NO_RESULT_RECEIVED_BY_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
			} catch (IOException e1) {
				dump("ERROR", e1.toString());
				e1.printStackTrace();
			}
			return;
		}
	}

	/**
	 * @param req
	 * @param resp
	 */
	public void requestPreTreatmentGetMap (HttpServletRequest req, HttpServletResponse resp){
		try{
			//Check the LAYERS parameter validity
			if(((WMSProxyServletRequest)getProxyRequest()).getLayers() == null || ((WMSProxyServletRequest)getProxyRequest()).getLayers().equalsIgnoreCase(""))
			{
				StringBuffer out = owsExceptionReport.generateExceptionReport("LAYERS "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE,OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE,"LAYERS");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
				return;
			}
			
			//Check the validity of the GetMapRequest :
			//This processing is out of this method because it is also used in the GetFeatureInfo request
			List <Object> r = checkGetMapRequestValidity(req,resp);
			
			//If the result is null, ann exception has already been sent to the client 
			if (r == null)
				return;
			
			//The results 
			@SuppressWarnings("unchecked")
			ArrayList <String> remoteServerToCall = (ArrayList <String>)r.get(0);
			@SuppressWarnings("unchecked")
			TreeMap<Integer,ProxyLayer> layerTableToKeep = (TreeMap<Integer,ProxyLayer>) r.get(1);
			@SuppressWarnings("unchecked")
			TreeMap<Integer,String> layerStyleMap = (TreeMap <Integer,String>) r.get(2);
						
			if(layerTableToKeep.size() == 0){
				generateEmptyImage(getProxyRequest().getWidth(), getProxyRequest().getHeight(), getProxyRequest().getFormat(), true, resp);
				//TODO : send directly to the client or need a transform call?
			}
			
			//Get the remote server informations
			Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();
			
			//Loop on the remote server to send the request 
			List<WMSProxyServerGetMapThread> serverThreadList = new Vector<WMSProxyServerGetMapThread>();
			for(int k = 0; k<remoteServerToCall.size();k++){
				TreeMap<Integer, ProxyLayer> layerByServerTable = new TreeMap<Integer, ProxyLayer>();
				RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(remoteServerToCall.get(k));
				Iterator<Entry<Integer, ProxyLayer>> itLK = layerTableToKeep.entrySet().iterator();
				//Build a list of the layers for the current remote server
				while(itLK.hasNext()){
					Entry<Integer, ProxyLayer> layerOrdered = itLK.next();
					if(((ProxyLayer)layerOrdered.getValue()).getAlias().equals(RS.getAlias())){
						layerByServerTable.put(layerOrdered.getKey(), layerOrdered.getValue());
					}
				}
				//New Thread to request this remote server
				WMSProxyServerGetMapThread s = new WMSProxyServerGetMapThread(	this,
																				getProxyRequest().getUrlParameters(), 
																				layerByServerTable, 
																				layerStyleMap,
																				RS,
																				resp);
	
				s.start();
				serverThreadList.add(s);
			}	
			
			// Wait for thread results
			for (int i = 0; i < serverThreadList.size(); i++) {
				try {
					serverThreadList.get(i).join();
				} catch (InterruptedException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
			
			//Post Treatment
			if (wmsGetMapResponseFilePathMap.size() > 0) {
				dump("requestPreTraitementGET begin transform");
				transform(getProxyRequest().getVersion().replaceAll("\\.", ""), getProxyRequest().getOperation(), req, resp);
				dump("requestPreTraitementGET end transform");
			} else {
				// TODO : generate an empty response 
				
			}
			
		}catch(Exception e){
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.toString());
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
			} catch (IOException e1) {
				dump("ERROR", e1.toString());
				e1.printStackTrace();
			}
			return;
		}
		
	}
	
	/**
	 * @param req
	 * @param resp
	 */
	public void requestPreTreatmentGetLegendGraphic (HttpServletRequest req, HttpServletResponse resp){
		try {
			//Check the LAYER parameter 
			if(((WMSProxyServletRequest)getProxyRequest()).getLayer() == null || ((WMSProxyServletRequest)getProxyRequest()).getLayer().equals(""))
			{
				StringBuffer out = owsExceptionReport.generateExceptionReport("LAYER "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE,OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE,"LAYER");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
				return;
			}
			
			//Get the remote server informations
			Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();
			
			//Check if the layer is allowed against policy rules
			String layerAsString = ((WMSProxyServletRequest)getProxyRequest()).getLayer();
			ProxyLayer layer = new ProxyLayer(layerAsString);
			if(layer.getAlias() == null){
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"LAYER");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
				return;
			}
			
			//Find the remote server concerning by the current layer
			RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(layer.getAlias());
			
			//Check the availaibility of the requested LAYERS 
			if( RS == null || !isLayerAllowed(layer.getName(), RS.getUrl())){
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"LAYER");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
				return;
			}
			
			//Send the request to the remote server and send back the response directly to client
			sendDataDirectStream(resp,"GET", RS.getUrl(), getProxyRequest().getUrlParameters());
						
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.toString());
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
			} catch (IOException e1) {
				dump("ERROR", e1.toString());
				e1.printStackTrace();
			}
			return;
		}
	}
	
	/**
	 * @param req
	 * @param resp
	 */
	public void requestPreTreatmentGetFeatureInfo (HttpServletRequest req, HttpServletResponse resp){
		try{
			if(((WMSProxyServletRequest)getProxyRequest()).getQueryLayers() == null || ((WMSProxyServletRequest)getProxyRequest()).getQueryLayers().equalsIgnoreCase(""))
			{
				StringBuffer out = owsExceptionReport.generateExceptionReport("QUERY_LAYERS "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE,OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE,"QUERY_LAYERS");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
				return;
			}
			
			//Check the validity of the GetMapRequest :
			//This processing is out of this method because it is also used in the GetMap request
			List <Object> r = checkGetMapRequestValidity(req,resp);
			
			//If the result is null, the request is not valid and an OGC exception has been already sent in the checkGetMapRequestValidity() method
			if(r == null)
				return;
			
			//get the authorized layers in the GetMap request
			@SuppressWarnings("unchecked")
			TreeMap<Integer,ProxyLayer> layerTableToKeepFromGetMap = (TreeMap<Integer,ProxyLayer>) r.get(1);
			@SuppressWarnings("unchecked")
			TreeMap<Integer,String> layerStyleMap = (TreeMap <Integer,String>) r.get(2);
			
			//Layer to keep in the remote request
			TreeMap<Integer,ProxyLayer> layerTableToKeep = new TreeMap<Integer, ProxyLayer>();
			
			//Get the remote server informations
			Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();
			
			ArrayList <String> remoteServerToCall = new ArrayList<String>();
				
			List<String> layerArray = null;
			layerArray = Collections.synchronizedList(new ArrayList<String>(Arrays.asList(((WMSProxyServletRequest)getProxyRequest()).getQueryLayers().split(","))));
			
			for (int k = 0 ; k < layerArray.size() ; k++){
				ProxyLayer layer = new ProxyLayer(layerArray.get(k));
				if(layer.getAlias() == null)
				{
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"QUERY_LAYERS");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
					return;
				}
				
				//Find the remote server concerning by the current layer
				RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(layer.getAlias());
				
				//Check the availaibility of the requested QUERY_LAYERS 
				if( RS == null || !isLayerAllowed(layer.getName(), RS.getUrl())){
					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"QUERY_LAYERS");
					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
					return ;
				}
				
				if (layerTableToKeepFromGetMap.containsValue(layer)){
					layerTableToKeep.put(k, layer);
					remoteServerToCall.add(RS.getAlias());
				}
			}
			
			
			//Send the request to the remote servers
			//Loop on the remote server to send the request 
			List<WMSProxyServerGetFeatureInfoThread> serverThreadList = new Vector<WMSProxyServerGetFeatureInfoThread>();
			for(int k = 0; k<remoteServerToCall.size();k++){
				TreeMap<Integer, ProxyLayer> layerByServerTable = new TreeMap<Integer, ProxyLayer>();
				TreeMap<Integer, ProxyLayer> queryLayerByServerTable = new TreeMap<Integer, ProxyLayer>();
				
				//Get the remote server info
				RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(remoteServerToCall.get(k));
				
				//Loop on QUERY_LAYERS to keep only those to send to the current RS
				Iterator<Entry<Integer, ProxyLayer>> itLK = layerTableToKeep.entrySet().iterator();
				while(itLK.hasNext()){
					Entry<Integer, ProxyLayer> layerOrdered = itLK.next();
					if(((ProxyLayer)layerOrdered.getValue()).getAlias().equals(RS.getAlias())){
						queryLayerByServerTable.put(layerOrdered.getKey(), layerOrdered.getValue());
					}
				}
				
				//Loop on LAYERS to keep only those to send to the current RS
				Iterator<Entry<Integer, ProxyLayer>> itLKGM = layerTableToKeepFromGetMap.entrySet().iterator();
				while(itLKGM.hasNext()){
					Entry<Integer, ProxyLayer> layerOrdered = itLKGM.next();
					if(((ProxyLayer)layerOrdered.getValue()).getAlias().equals(RS.getAlias())){
						layerByServerTable.put(layerOrdered.getKey(), layerOrdered.getValue());
					}
				}
				
				//New Thread to request this remote server
				WMSProxyServerGetFeatureInfoThread s = new WMSProxyServerGetFeatureInfoThread(	this,
																								getProxyRequest().getUrlParameters(), 
																								queryLayerByServerTable,
																								layerByServerTable, 
																								layerStyleMap,
																								RS,
																								resp);
	
				s.start();
				serverThreadList.add(s);
			}	
			
			// Wait for thread results
			for (int i = 0; i < serverThreadList.size(); i++) {
				try {
					serverThreadList.get(i).join();
				} catch (InterruptedException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
			
			if (wmsGetFeatureInfoResponseFilePathMap.size() > 0) {
				dump("requestPreTraitementGET begin transform");
				transform(getProxyRequest().getVersion().replaceAll("\\.", ""), getProxyRequest().getOperation(), req, resp);
				dump("requestPreTraitementGET end transform");
			} else {
				// TODO : generate an OGC exception
				dump("ERROR", "This request has no authorized results!");
			}
			
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.toString());
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
			} catch (IOException e1) {
				dump("ERROR", e1.toString());
				e1.printStackTrace();
			}
			return;
		}
	}

	/**
	 * Overwrite the GetCapabilities response with config and policy informations :
	 * - Service metadata 
	 * - Authorized operations
	 * - Authorized layers
	 * - BBOX (geographic filter)
	 * - online resources
	 * Merge all the remote response in one single file to send to the client.
	 * @param req
	 * @param resp
	 */
	public void transformGetCapabilities (HttpServletRequest req, HttpServletResponse resp){
		try{
			//Get the responses which are OGC exception (XML)
			HashMap<String, String> remoteServerExceptionFiles = getRemoteServerExceptionResponse(wmsGetCapabilitiesResponseFilePathMap);
	
			//If the Exception mode is 'restrictive' and at least a response is an exception
			//Or if the Exception mode is 'permissive' and all the response are exceptio
			//Aggegate the exception files and send the result to the client
			if((remoteServerExceptionFiles.size() > 0 && configuration.getExceptionMode().equals("restrictive")) ||  
					(configuration.getExceptionMode().equals("permissive") && wmsGetCapabilitiesResponseFilePathMap.size() == 0)){
				dump("INFO","Exception(s) returned by remote server(s) are sent to client.");
				ByteArrayOutputStream exceptionOutputStream = docBuilder.ExceptionAggregation(remoteServerExceptionFiles);
				sendHttpServletResponse(req,resp,exceptionOutputStream, "text/xml; charset=utf-8", HttpServletResponse.SC_OK);
				return;
			}
			
			//Check if all the responses are in the same WMS protocol version
			if(!isAllGetCapabilitiesResponseSameVersion(wmsGetCapabilitiesResponseFilePathMap)){
				dump("ERROR",OWSExceptionReport.TEXT_VERSION_NEGOCIATION_FAILED);
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_VERSION_NEGOCIATION_FAILED,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,null);
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
				return;
			}
				
			//Capabilities rewriting
			RemoteServerInfo rs = getRemoteServerInfoMaster();
			
			if(!docBuilder.CapabilitiesContentsFiltering(wmsGetCapabilitiesResponseFilePathMap,getServletUrl(req)))
			{
				dump("ERROR",docBuilder.getLastException().toString());
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
				return;
			}
			
			if(!docBuilder.CapabilitiesOperationsFiltering(wmsGetCapabilitiesResponseFilePathMap.get(rs.getAlias()), getServletUrl(req)))
			{
				dump("ERROR",docBuilder.getLastException().toString());
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
				return;
			}
			
			if(!docBuilder.CapabilitiesMerging(wmsGetCapabilitiesResponseFilePathMap))
			{
				dump("ERROR",docBuilder.getLastException().toString());
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
				return;
			}
			
			if(!docBuilder.CapabilitiesServiceMetadataWriting(wmsGetCapabilitiesResponseFilePathMap.get(rs.getAlias()),getServletUrl(req)))
			{
				dump("ERROR",docBuilder.getLastException().toString());
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
				return;
			}
			
			File result = applyUserXSLT(new File(wmsGetCapabilitiesResponseFilePathMap.get(rs.getAlias())));
			FileInputStream reader = new FileInputStream(result);
			byte[] data = new byte[reader.available()];
			reader.read(data, 0, reader.available());
			ByteArrayOutputStream out = new ByteArrayOutputStream();
			out.write(data);
			reader.close();
			sendHttpServletResponse(req,resp, out,responseContentType, responseStatusCode);

		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			dump("ERROR", e.toString());
			StringBuffer out;
			try {
				out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE,"");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
			} catch (IOException e1) {
				dump("ERROR", e1.toString());
				e1.printStackTrace();
			}
			return;
		}
	}
	
	private File applyUserXSLT (File response){
		String userXsltPath = getConfiguration().getXsltPath();
		if (SecurityContextHolder.getContext().getAuthentication() != null) {
			userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
		}

		userXsltPath = userXsltPath + "/" + getProxyRequest().getVersion() + "/" + getProxyRequest().getOperation() + ".xsl";
		String globalXsltPath = getConfiguration().getXsltPath() + "/" + getProxyRequest().getVersion() + "/" + getProxyRequest().getOperation() + ".xsl";
		
		File result = new File (response.getPath()+".xml");
		File xsltFile = new File(userXsltPath);
		if (!xsltFile.exists()) {
			dump("Postreatment file " + xsltFile.toString() + "does not exist");
			xsltFile = new File(globalXsltPath);
		} 
		
		if (xsltFile.exists() && isXML(responseContentType)) {
			dump("transform begin userTransform xslt");

			Transformer transformer = null;
			TransformerFactory tFactory = TransformerFactory.newInstance();
			try {
				transformer = tFactory.newTransformer(new StreamSource(xsltFile));
				transformer.transform(new StreamSource(response), new StreamResult(result));
			} catch (TransformerConfigurationException e1) {
				// TODO Auto-generated catch block
				e1.printStackTrace();
			} catch (TransformerException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			dump("transform end userTransform xslt");
			return result;
		}else{
			return response;
		}
	}
	
	/**
	 * Get the exception files return by the remote servers.
	 * @param remoteServerResponseFile
	 * @return
	 */
	protected HashMap<String, String> getRemoteServerExceptionResponse (HashMap<String, String> remoteServerResponseFile)
	{
		HashMap<String, String> toRemove = new HashMap<String, String>();
		HashMap<String, String> remoteServerExceptionFiles = new HashMap<String, String>();
		
		try {
			Iterator<Entry<String,String>> it = remoteServerResponseFile.entrySet().iterator();
			while(it.hasNext()){
				Entry<String,String> entry = it.next();
				String path  = entry.getValue();
				if(path == null || path.length() == 0)
					continue;
				
				//Check if the response is an XML exception
				if(isRemoteServerResponseException(path)){
					toRemove.put(entry.getKey(), path);
				}
			}
			
			Iterator<Entry<String,String>> itR = toRemove.entrySet().iterator();
			while(itR.hasNext())
			{
				Entry<String, String> entry = itR.next();
				remoteServerExceptionFiles.put(entry.getKey(),entry.getValue());
				remoteServerResponseFile.remove(entry.getKey());
			}
			
			return remoteServerExceptionFiles;
		} catch (SAXException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (ParserConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return remoteServerExceptionFiles;
	}
	
	/**
	 * Get the exception files return by the remote servers.
	 * @param remoteServerResponseFile
	 * @return
	 */
	protected HashMap<String, String> getRemoteServerExceptionResponse (TreeMap<Integer,ProxyRemoteServerResponse> remoteServerResponseFile)
	{
		TreeMap<Integer, ProxyRemoteServerResponse> toRemove = new TreeMap<Integer, ProxyRemoteServerResponse>();
		HashMap<String, String> remoteServerExceptionFiles = new HashMap<String, String>();
		
		try{
			Iterator<Entry<Integer, ProxyRemoteServerResponse>> it = remoteServerResponseFile.entrySet().iterator();
			while(it.hasNext()){
				Entry<Integer, ProxyRemoteServerResponse> entry = it.next();
				ProxyRemoteServerResponse response = entry.getValue();
				String path = response.getPath();
				if(path == null || path.length() == 0)
					continue;
				
				//Check if the response is an XML exception
				if(isRemoteServerResponseException(path)){
					toRemove.put(entry.getKey(), response);
				}
			}
			
			Iterator<Entry<Integer, ProxyRemoteServerResponse>> itR = toRemove.entrySet().iterator();
			while(itR.hasNext())
			{
				Entry<Integer, ProxyRemoteServerResponse> entry = itR.next();
				remoteServerExceptionFiles.put(entry.getValue().getAlias(),entry.getValue().getPath());
				remoteServerResponseFile.remove(entry.getKey());
			}
			
			return remoteServerExceptionFiles;
		} catch (SAXException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (ParserConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return remoteServerExceptionFiles;
	}
	
	/**
	 * Return if the file at the given path is an XML OGC exception file.
	 * @param path
	 * @return
	 * @throws SAXException
	 * @throws IOException
	 * @throws ParserConfigurationException
	 */
	private boolean isRemoteServerResponseException(String path) throws SAXException, IOException, ParserConfigurationException{
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
					return true;
				}
			}
		}
		return false;
	}
	
	public boolean isAllGetCapabilitiesResponseSameVersion (HashMap<String, String> wmsGetCapabilitiesResponse){
		
		if(wmsGetCapabilitiesResponse.size() == 1)
			return true;
		
		SAXBuilder sxb = new SAXBuilder();
		Iterator<Entry<String, String>> iFilePath = wmsGetCapabilitiesResponse.entrySet().iterator();
		
		String firstVersion = null;
		
		while (iFilePath.hasNext()){
			Entry<String, String> filePath = iFilePath.next();

			try {
				org.jdom.Document doc = sxb.build(new File (filePath.getValue()));
				org.jdom.Element racine = doc.getRootElement();
				String version = racine.getAttributeValue("version");
				if(firstVersion == null){
					firstVersion = version;
				}else{
					if(!firstVersion.equals(version)){
						return false;
					}
				}
					
			} catch (JDOMException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
				return false;
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
				return false;
			}
		}
		return true;
	}
	/**
	 * Check the validity of the request GETMAP
	 * @param req
	 * @param resp
	 * @return
	 * @throws Exception
	 */
	public List<Object> checkGetMapRequestValidity (HttpServletRequest req, HttpServletResponse resp) throws Exception{
		//Check the WIDTH and HEIGHT parameters validity against the policy rules
		if (!isSizeInTheRightRange(Integer.parseInt(((WMSProxyServletRequest)getProxyRequest()).getWidth()), 
				   Integer.parseInt(((WMSProxyServletRequest)getProxyRequest()).getWidth())))
		{
			dump("requestPreTraitementGET says: request ImageSize out of bounds, see the policy definition.");
			StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_DIMENSION_VALUE,OWSExceptionReport.CODE_INVALID_DIMENSION_VALUE,"WIDTH");
			sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
			return null;
		}
		
		//Get the remote server informations
		Hashtable<String, RemoteServerInfo> remoteServersTable = getRemoteServerHastable();
		
		//Get the requested layer names
		TreeMap<Integer,String> layerMap = new TreeMap <Integer,String>();
		ArrayList<String> layerParamAsArray = new ArrayList<String>(Arrays.asList(((WMSProxyServletRequest)getProxyRequest()).getLayers().split(",")));
		for (int index = 0 ; index < layerParamAsArray.size() ; index++){
			layerMap.put(index, layerParamAsArray.get(index));
		}
		
		//Check the STYLES parameter
		TreeMap<Integer,String> layerStyleMap = new TreeMap <Integer,String>();
		ArrayList<String> layerStyleParamAsArray = new ArrayList<String>();
		if(((WMSProxyServletRequest)getProxyRequest()).getStyles() != null){
			layerStyleParamAsArray = new ArrayList<String>(Arrays.asList(((WMSProxyServletRequest)getProxyRequest()).getStyles().split(",")));
		}
		
		//A style definition is mandatory for each layer, we create them if needed
		if (layerStyleParamAsArray.size() < layerMap.size()) {
			int diffSize = layerMap.size() - layerStyleParamAsArray.size();
			for (int i = 0; i < diffSize; i++) {
				layerStyleParamAsArray.add("");
			}
		}
		for (int index = 0 ; index < layerStyleParamAsArray.size() ; index++){
			layerStyleMap.put(index, layerStyleParamAsArray.get(index));
		}
		
		//Get the BBOX parameter
		String[] c = ((WMSProxyServletRequest)getProxyRequest()).getBbox().split(",");
		ReferencedEnvelope rEnvelope;
		try {
			rEnvelope = new ReferencedEnvelope(Double.parseDouble(c[0]), Double.parseDouble(c[2]), Double.parseDouble(c[1]), Double.parseDouble(c[3]), CRS.decode(((WMSProxyServletRequest)getProxyRequest()).getSrsName()));
		}catch (Exception ex) {
			StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_SRS,OWSExceptionReport.CODE_INVALID_SRS,"SRS");
			sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
			return null;
		}
		
		//List of object to keep from the request
		TreeMap<Integer,ProxyLayer> layerTableToKeep = new TreeMap <Integer,ProxyLayer>();
		
		//Loop on LAYERS to keep only the valid layers
		Iterator<Entry<Integer,String>> it =  layerMap.entrySet().iterator();
		ArrayList <String> remoteServerToCall = new ArrayList<String>();
		while (it.hasNext()){
			Entry <Integer, String> layerOrdered = it.next();
			String layerName = layerOrdered.getValue();
			ProxyLayer layer = new ProxyLayer(layerName);
			if(layer.getAlias() == null)
			{
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"LAYERS");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
				return null;
			}
			//Find the remote server concerning by the current layer
			RemoteServerInfo RS = (RemoteServerInfo)remoteServersTable.get(layer.getAlias());
			
			//Check the availaibility of the requested LAYERS 
			if( RS == null || !isLayerAllowed(layer.getName(), RS.getUrl())){
				StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_LAYER_NAME,OWSExceptionReport.CODE_LAYER_NOT_DEFINED,"LAYERS");
				sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
				return null;
			}
			
			//Check if the scale is available
			if (isLayerInScale(layerName, RS.getUrl(), RendererUtilities.calculateOGCScale(rEnvelope, Integer.parseInt(((WMSProxyServletRequest)getProxyRequest()).getWidth()), null))) {
				//Layer to keep in the request
				layerTableToKeep.put(layerOrdered.getKey(),layer);
				//Servers to call to complete the request
				if(!remoteServerToCall.contains(layer.getAlias())){
					remoteServerToCall.add(layer.getAlias());
				}
			} else {
				dump("requestPreTraitementGetMap says: request Scale out of bounds for "+layerName+", see the policy definition.");
			}
		}
		List<Object> map = new ArrayList<Object>();
		map.add(remoteServerToCall);
		map.add(layerTableToKeep);
		map.add(layerStyleMap);
		return map;
	}
	
	/**
	 * Generate an empty image
	 * @param width
	 * @param height
	 * @param format
	 * @param isTransparent
	 * @param j
	 * @param resp
	 */
	private void generateEmptyImage(String width, String height, String format, boolean isTransparent, HttpServletResponse resp) {
		try {
			BufferedImage imgOut = null;
			if (isTransparent) {
				imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.BITMASK);
			} else {
				imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.TYPE_INT_ARGB);
			}
			responseContentType = URLDecoder.decode(format, "UTF-8");
			responseContentTypeList.add(responseContentType);
			Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(responseContentType);

			if (iter.hasNext()) {
				ImageWriter writer = (ImageWriter) iter.next();
				File tempFile = createTempFile(UUID.randomUUID().toString(), getExtension(responseContentType));
				FileImageOutputStream output = new FileImageOutputStream(tempFile);
				writer.setOutput(output);
				writer.write(imgOut);
				String filePath = tempFile.getPath();
				wmsGetCapabilitiesResponseFilePathMap.put("proxy", filePath);
				writer.dispose();
			}
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
		}
	}

	/**
	 * envelope contains the envelope of the whole image
	 * @param imageSource
	 * @param envelope
	 * @param polygonFilter
	 * @param isTransparent
	 * @param resp
	 * @return
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
}
