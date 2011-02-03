package org.easysdi.proxy.wmts.v100;

import java.io.File;
import java.io.FileOutputStream;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;
import java.util.Vector;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.wmts.*;
import org.easysdi.xml.documents.Config;
import org.easysdi.xml.documents.ServiceContactAdressInfo;
import org.easysdi.xml.documents.ServiceContactInfo;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.Parent;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import com.google.common.collect.Multimap;

public class WMTS100ProxyResponseBuilder extends WMTSProxyResponseBuilder {

	public WMTS100ProxyResponseBuilder(WMTSProxyServlet proxyServlet) {
		super(proxyServlet);
		nsOWS = Namespace.getNamespace("http://www.opengis.net/ows/1.1");
		nsWMTS = Namespace.getNamespace("http://www.opengis.net/wmts/1.0");
	}
	
	public Boolean CapabilitiesOperationFiltering (Multimap<Integer, String> filePathList, String href ){
		try{
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
				
			String filePath = filePathList.get(0).toArray(new String[1])[0];
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
	    	Filter xlinkFilter = new WMTSProxyCapabilitiesXlinkFilter();
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
	    		else
	    		{
	    			Iterator iXlink = child.getDescendants(xlinkFilter);
	    			List<Element> xlinkList = new ArrayList<Element>();	  
	    			while (iXlink.hasNext())
	    			{
	    				Element courant = (Element)iXlink.next();
	    				xlinkList.add(courant);
	    			}
	    			Iterator ilXlink = xlinkList.iterator();
	    			while(ilXlink.hasNext())
	    			{
	    				Element toUpdate = (Element)ilXlink.next();
	    				toUpdate.setAttribute("href", href, nsXLINK);
	    			}
	    		}
	    	}
	    	
    	   XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
           sortie.output(docParent, new FileOutputStream(filePath));
			
           return true;
		}
		catch (Exception ex){
			setLastException(ex);
			return false;
		}
	}
	public Boolean CapabilitiesLayerFiltering (Multimap<Integer, String> filePathList ){
		
	    try
	    {
	    	SAXBuilder sxb = new SAXBuilder();
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
	
	public Boolean CapabilitiesMerging(Multimap<Integer, String> filePathList)
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
//					List layers = contentsChild.getChildren("Layer", nsWMTS);
//					List matrix = contentsChild.getChildren("TileMatrixSet", nsWMTS);
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
	
	public Boolean CapabilitiesServiceMetadataWriting(Multimap<Integer, String> filePathList)
	{
		try
		{
			Config config = servlet.getConfiguration();
			ServiceContactInfo serviceContactInfo = config.getContactInfo();
			
			SAXBuilder sxb = new SAXBuilder();
			Document document = sxb.build(new File(filePathList.get(0).toArray(new String[1])[0]));
			Element racine = document.getRootElement();
			racine.removeContent(racine.getChild("ServiceIdentification", nsOWS));
			
			Element newServiceIdentification = new Element("ServiceIdentification", nsOWS);
			
			if(config.getTitle() != null && config.getTitle().length() != 0)
				newServiceIdentification.addContent((new Element("Title", nsOWS)).setText(config.getTitle()));
			
			if(config.getAbst() != null && config.getAbst().length() != 0)
				newServiceIdentification.addContent((new Element("Abstract", nsOWS)).setText(config.getAbst()));
			
			if(config.getKeywordList() != null && config.getKeywordList().size() != 0)
			{
				Element keywords = new Element("Keywords", nsOWS);
				Iterator<String> iKeywords = config.getKeywordList().iterator();
				while (iKeywords.hasNext())
				{
					keywords.addContent((new Element("Keyword", nsOWS)).setText(iKeywords.next()));
				}
				newServiceIdentification.addContent(keywords);
			}
			
			newServiceIdentification.addContent((new Element("ServiceType", nsOWS)).setText("OGC WMTS"));
			newServiceIdentification.addContent((new Element("ServiceTypeVersion", nsOWS)).setText("1.0.0"));
			
			if(config.getFees() != null && config.getFees().length() != 0)
				newServiceIdentification.addContent((new Element("Fees", nsOWS)).setText(config.getFees()));
			
			if(config.getAccessConstraints() != null && config.getAccessConstraints().length() != 0)
				newServiceIdentification.addContent((new Element("AccessConstraints", nsOWS)).setText(config.getAccessConstraints()));
			
			racine.addContent( 0, newServiceIdentification);
			if(serviceContactInfo != null && !serviceContactInfo.isEmpty())
			{	
				Element newServiceProvider = new Element("ServiceProvider", nsOWS);
				if(serviceContactInfo.getOrganization() != null && serviceContactInfo.getOrganization().length() != 0)
					newServiceIdentification.addContent((new Element("ProviderName", nsOWS)).setText(serviceContactInfo.getOrganization()));
				if(serviceContactInfo.getLinkage() != null && serviceContactInfo.getLinkage().length() != 0)
				{
					Element site = new Element("ProviderSite", nsOWS);
					site.setAttribute("href", serviceContactInfo.getLinkage(),nsXLINK);
					newServiceIdentification.addContent(site);
				}
				
				ServiceContactAdressInfo  serviceContactAddressInfo = serviceContactInfo.getContactAddress();
				if(!serviceContactAddressInfo.isEmpty() && serviceContactInfo.getVoicePhone() != null && serviceContactInfo.getVoicePhone().length() != 0 && serviceContactInfo.getFacSimile() != null && serviceContactInfo.getFacSimile().length() != 0)
				{
					Element newProvider = new Element("ServiceProvider", nsOWS);
				}
				racine.addContent( 1, newServiceProvider);
			}
			XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	        sortie.output(document, new FileOutputStream(filePathList.get(0).toArray(new String[1])[0]));

			return true;
		}
		catch (Exception ex)
		{
			return false;
		}
	}
	
}
