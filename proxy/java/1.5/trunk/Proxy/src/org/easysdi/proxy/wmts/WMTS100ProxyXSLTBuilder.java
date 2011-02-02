package org.easysdi.proxy.wmts;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.util.Arrays;
import java.util.Iterator;
import java.util.List;
import java.util.Vector;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilderFactory;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.policy.Layer;
import org.easysdi.proxy.policy.Server;
import org.easysdi.proxy.wms.WMSProxyCapabilitiesLayerFilter;
import org.jdom.Namespace;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.w3c.dom.Document;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.ls.LSSerializer;

public class WMTS100ProxyXSLTBuilder extends WMTSProxyXSLTBuilder{

	
	public WMTS100ProxyXSLTBuilder(WMTSProxyServlet proxyServlet) {
		super(proxyServlet);
	}

	public StringBuffer getCapabilitiesXSLT (HttpServletRequest req, HttpServletResponse resp, int remoteServerIndex)
	{
		try {
			String url = servlet.getServletUrl(req);
			
			//Retrieve allowed and denied operations from the policy
			List<String> permitedOperations = new Vector<String>();
			List<String> deniedOperations = new Vector<String>();
			for (int i = 0; i < ProxyServlet.ServiceOperations.size(); i++) 
			{
				if (ProxyServlet.ServiceSupportedOperations.contains(ProxyServlet.ServiceOperations.get(i)) && servlet.isOperationAllowed(ProxyServlet.ServiceOperations.get(i))) 
				{
					permitedOperations.add(ProxyServlet.ServiceOperations.get(i));
					servlet.dump("INFO",ProxyServlet.ServiceOperations.get(i) + " is permitted");
				} else 
				{
					deniedOperations.add(ProxyServlet.ServiceOperations.get(i));
					servlet.dump("INFO",ProxyServlet.ServiceOperations.get(i) + " is denied");
				}
			}
			
			try {
				StringBuffer WMTSCapabilities100 = new StringBuffer();
				WMTSCapabilities100.append("<xsl:stylesheet version=\"1.00\" " +
								"xmlns:ows=\"http://www.opengis.net/ows/1.1\" " +
								"xmlns:wmts=\"http://www.opengis.net/wmts/1.0\" " +
								"xmlns:sld=\"http://www.opengis.net/sld\" " + 
								"xmlns:xsi=\"http://www.opengis.net/wmts/1.0\" "+ 
								"xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" " +
								"xmlns:xlink=\"http://www.w3.org/1999/xlink\" " +
								"xmlns:gml=\"http://www.opengis.net/gml\" "+
								">");
				WMTSCapabilities100.append("<xsl:output method=\"xml\" omit-xml-declaration=\"no\" version=\"1.0\" encoding=\"UTF-8\" indent=\"yes\"/>");
				WMTSCapabilities100.append("<xsl:template match=\"/\">");
				WMTSCapabilities100.append("<xsl:apply-templates/>");
				WMTSCapabilities100.append("</xsl:template>");
				// Operations filtering
				if (!servlet.policy.getOperations().isAll() || deniedOperations.size() > 0 ) {
					Iterator<String> it = deniedOperations.iterator();
					while (it.hasNext()) {
						String text = it.next();
						if (text != null) {
							WMTSCapabilities100.append("<xsl:template match=\"//ows:OperationsMetadata/ows:Operation[@name='"+text+"']\">");
							WMTSCapabilities100.append("</xsl:template>");
						}
					}
				}
				if (permitedOperations.size() == 0 )
				{
					WMTSCapabilities100.append("<xsl:template match=\"//ows:OperationsMetadata");
					WMTSCapabilities100.append("\"></xsl:template>");
				}
				
				//Layers filtering
				SAXBuilder saxBuilder = new SAXBuilder();
				org.jdom.Document jDoc = saxBuilder.build(new File(((WMTSProxyServlet)servlet).wmtsFilePathList.get(remoteServerIndex).toArray(new String[1])[0]));
				Filter filtre = new WMTSProxyCapabilitiesLayerFilter();
    			Iterator itL = jDoc.getDescendants(filtre);
    			while(itL.hasNext())
				{
    				org.jdom.Element layer = (org.jdom.Element)itL.next();
    				org.jdom.Element layerTitle;
    				
    				layerTitle = layer.getChild("Title", Namespace.getNamespace("http://www.opengis.net/ows/1.1"));
    				
    				if(layerTitle == null)
    					continue;
    				boolean allowed = servlet.isLayerAllowed(layerTitle.getValue(), servlet.getRemoteServerUrl(remoteServerIndex));
					if (!allowed) {
						// The layer is not allowed, it is removed from the capabilities
						WMTSCapabilities100.append("<xsl:template match=\"//wmts:Layer[ows:Title='" + layerTitle.getValue() + "']");
						WMTSCapabilities100.append("\"></xsl:template>");
					}
				}
				
				WMTSCapabilities100.append("  <!-- Whenever you match any node or any attribute -->");
				WMTSCapabilities100.append("<xsl:template match=\"node()|@*\">");
				WMTSCapabilities100.append("<!-- Copy the current node -->");
				WMTSCapabilities100.append("<xsl:copy>");
				WMTSCapabilities100.append("<!-- Including any attributes it has and any child nodes -->");
				WMTSCapabilities100.append("<xsl:apply-templates select=\"@*|node()\"/>");
				WMTSCapabilities100.append("</xsl:copy>");
				WMTSCapabilities100.append("</xsl:template>");
				WMTSCapabilities100.append("</xsl:stylesheet>");

				return WMTSCapabilities100;
			} catch (Exception e) {
				resp.setHeader("easysdi-proxy-error-occured", "true");
				e.printStackTrace();
				servlet.dump("ERROR", e.getMessage());
			}

			// If something goes wrong, null is returned.
			return null;
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			servlet.dump("ERROR", e.getMessage());
		}
		return null;
	}
	
	public ByteArrayOutputStream mergeCapabilities(List<File> tempFileCapa, HttpServletResponse resp)
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
				servlet.dump("Error", "DOM Load and Save not Supported. Multiple server is not allowed");
				ByteArrayOutputStream out = new ByteArrayOutputStream();
				FileInputStream reader = new FileInputStream(fMaster);
				byte[] data = new byte[reader.available()];
				reader.read(data, 0, reader.available());
				out.write(data);
				reader.close();
				return out;
			}

			for (int i = 1; i < tempFileCapa.size(); i++) {
				Document documentChild = null;
				try {
					documentChild = db.newDocumentBuilder().parse(tempFileCapa.get(i));
				} catch (Exception e) {
					e.printStackTrace();
					servlet.dump("ERROR", e.getMessage());
				}
				if (documentChild != null) {
					NodeList nl = documentChild.getElementsByTagNameNS("http://www.opengis.net/wmts/1.0","Layer");
					NodeList nlMaster = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wmts/1.0","Layer");
					Node ItemMaster = nlMaster.item(0);
					if (nl.item(0) != null)
						ItemMaster.insertBefore(documentMaster.importNode(nl.item(0).cloneNode(true), true), null);
					
					NodeList nM = documentChild.getElementsByTagNameNS("http://www.opengis.net/wmts/1.0","TileMatrixSet");
					NodeList nMMaster = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wmts/1.0","TileMatrixSet");
					Node ItemMMaster = nMMaster.item(0);
					if (nM.item(0) != null)
						ItemMMaster.insertBefore(documentMaster.importNode(nM.item(0).cloneNode(true), true), null);
				}
			}

			ByteArrayOutputStream out = new ByteArrayOutputStream();
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			sortie.setByteStream(out);
			serialiseur.write(documentMaster, sortie);
			return out;
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			e.printStackTrace();
			servlet.dump("ERROR", e.getMessage());
			return null;
		}
	}
}
