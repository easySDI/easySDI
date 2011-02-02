package org.easysdi.proxy.wmts;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;
import java.util.Vector;

import javax.naming.NameParser;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilderFactory;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.csw.CSWProxyGetRequestHandler;
import org.easysdi.proxy.csw.CSWProxyMetadataContentFilter;
import org.easysdi.proxy.csw.CSWProxyMetadataExceptionFilter;
import org.easysdi.proxy.policy.Server;
import org.jdom.Attribute;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.Parent;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.ls.LSSerializer;

import com.google.common.collect.Multimap;

public class WMTS100ProxyDocumentBuilder extends WMTSProxyDocumentBuilder {

	private Namespace nsOWS = Namespace.getNamespace("http://www.opengis.net/ows/1.1");
	private Namespace nsWMTS = Namespace.getNamespace("http://www.opengis.net/wmts/1.0");
	
	public WMTS100ProxyDocumentBuilder(WMTSProxyServlet proxyServlet) {
		super(proxyServlet);
	}
	
	public Boolean CapabilitiesFiltering (Multimap<Integer, String> filePathList ){
		
	    try
	    {
	    	SAXBuilder sxb = new SAXBuilder();
	    	//Retrieve allowed and denied operations from the policy
			List<String> permitedOperations = new Vector<String>();
			List<String> deniedOperations = new Vector<String>();
			for (int i = 0; i < ProxyServlet.ServiceOperations.size(); i++) 
			{
				if (ProxyServlet.ServiceSupportedOperations.contains(ProxyServlet.ServiceOperations.get(i)) 
						&& servlet.isOperationAllowed(ProxyServlet.ServiceOperations.get(i))) 
				{
					permitedOperations.add(ProxyServlet.ServiceOperations.get(i));
					servlet.dump("INFO",ProxyServlet.ServiceOperations.get(i) + " is permitted");
				} else 
				{
					deniedOperations.add(ProxyServlet.ServiceOperations.get(i));
					servlet.dump("INFO",ProxyServlet.ServiceOperations.get(i) + " is denied");
				}
			}
			
			
			for (int iFilePath = 0; iFilePath < filePathList.size(); iFilePath++) {
			
				String filePath = filePathList.get(iFilePath).toArray(new String[1])[0];
		    	Document  docParent = sxb.build(new File(filePath));
		    	Element racine = docParent.getRootElement();
		      
		    	//get the namespace
		    	nsWMTS = racine.getNamespace();
		    	List lns = racine.getAdditionalNamespaces();
		    	Iterator ilns = lns.iterator();
		    	while (ilns.hasNext())
		    	{
		    		Namespace ns = (Namespace)ilns.next();
		    		if(ns.getPrefix().equalsIgnoreCase("ows"))
		    			nsOWS = ns;
		    	}
		    	
		    	
		    	//We can not modify Elements while we loop over them with an iterator.
		    	//We have to use a separate List storing the Elements we want to modify.
		    	
		    	//Operation filtering
		    	Filter operationFilter = new WMTSProxyCapabilitiesOperationFilter();
		    	List<Element> operationList = new ArrayList<Element>();	    	  
		    	Iterator iOperation= racine.getDescendants(operationFilter);
		    	while(iOperation.hasNext())
		    	{
		    	   Element courant = (Element)iOperation.next();
		    	   operationList.add(courant);
		    	}
		    	//Modification of the selected Elements
		    	Iterator iLOperation = operationList.iterator();
		    	while (iLOperation.hasNext())
		    	{
		    		Element child = (Element)iLOperation.next();
		    		if (deniedOperations.contains(child.getAttribute("name").getValue()))
					{
		    				Parent parent = child.getParent();
		    				parent.removeContent (child);
					}
		    	}
	
		    	//Layer filtering
		    	Filter layerFilter = new WMTSProxyCapabilitiesLayerFilter();
		    	List<Element> layerList = new ArrayList<Element>();	    	  
		    	Iterator iLayer= racine.getDescendants(layerFilter);
		    	while(iLayer.hasNext())
		    	{
		    	   Element courant = (Element)iLayer.next();
		    	   layerList.add(courant);
		    	}
		    	//Modification of the selected Elements
		    	Iterator iLLayer = layerList.iterator();
		    	while (iLLayer.hasNext())
		    	{
		    		Element layerElement = (Element)iLLayer.next();
		    		Element idElement = layerElement.getChild("Identifier", nsOWS);
		    		if (idElement!= null && !servlet.isLayerAllowed(idElement.getText(),servlet.getRemoteServerUrl(iFilePath)))
					{
//		    				Element tileMatrixLink = (layerElement.getChild("TileMatrixSetLink", nsWMTS)).getChild("TileMatrixSet", nsWMTS);
		    				Parent parent = layerElement.getParent();
		    				parent.removeContent (layerElement);
//		    				List<Element> TileMatrixSet = ((Element)parent).getChildren("TileMatrixSet", nsWMTS);
//		    				Iterator<Element> iTileMatrixSet = TileMatrixSet.iterator();
//		    				while (iTileMatrixSet.hasNext())
//		    				{
//		    					Element m = iTileMatrixSet.next();
//		    					if(m.getChild("Identifier", nsOWS).getText().equalsIgnoreCase(tileMatrixLink.getText()))
//		    					{
//		    						parent.removeContent(m);
//		    						break;
//		    					}
//		    				}
					}
		    	}
		    	
	    	   XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	           sortie.output(docParent, new FileOutputStream(filePath));
			}
			
           return true;
	    }
		catch (Exception ex )
		{
			setLastException(ex);
			return false;
		}
	}
	
	public Boolean mergeCapabilities(Multimap<Integer, String> filePathList)
	{
		if (filePathList.size() == 0)
		{
			setLastException(new Exception("No response file"));
			return false;
		}
		if(filePathList.size() == 1)
			return true;

		try {
			SAXBuilder sxb = new SAXBuilder();
			String fileMasterPath = filePathList.get(0).toArray(new String[1])[0];
			Document documentMaster = sxb.build(new File(fileMasterPath));
			Filter layerFilter = new WMTSProxyCapabilitiesLayerFilter();
			Element racineMaster = documentMaster.getRootElement();
			Element contentsMaster=  ((Element)racineMaster.getDescendants(layerFilter).next()).getParentElement();
			
			for (int iFilePath = 1; iFilePath < filePathList.size(); iFilePath++) {
				Document documentChild = null;
				documentChild = sxb.build(new File(filePathList.get(iFilePath).toArray(new String[1])[0]));
				if (documentChild != null) {
					Element racineChild = documentChild.getRootElement();
					nsWMTS = racineChild.getNamespace();
					Element contentsChild = racineChild.getChild("Contents", nsWMTS);
			    	contentsMaster.addContent(contentsChild.cloneContent());
				}
			}
			
			XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	        sortie.output(documentMaster, new FileOutputStream(fileMasterPath));

			return true;
		} catch (Exception ex) {
			setLastException(ex);
			return false;
		}
	}
}
