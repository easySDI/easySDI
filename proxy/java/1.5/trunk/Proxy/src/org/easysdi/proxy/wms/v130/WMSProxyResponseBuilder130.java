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
package org.easysdi.proxy.wms.v130;

import java.io.File;
import java.io.FileOutputStream;
import java.util.ArrayList;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Vector;

import org.easysdi.jdom.filter.AttributeXlinkFilter;
import org.easysdi.jdom.filter.ElementLayerFilter;
import org.easysdi.jdom.filter.ElementOperationFilter;
import org.easysdi.jdom.filter.ElementTileMatrixSetFilter;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.wms.WMSProxyResponseBuilder;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.Parent;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;

/**
 * @author DEPTH SA
 *
 */
public class WMSProxyResponseBuilder130 extends WMSProxyResponseBuilder {

	/**
	 * @param proxyServlet
	 */
	public WMSProxyResponseBuilder130(ProxyServlet proxyServlet) {
		super(proxyServlet);
		nsXLINK = Namespace.getNamespace("http://www.w3.org/1999/xlink");
		nsWMS = Namespace.getNamespace("http://www.opengis.net/wms");
	}
	
	@SuppressWarnings({ "rawtypes", "unchecked" })
	@Override
	public Boolean CapabilitiesOperationsFiltering(String filePath, String href) {
		servlet.dump("INFO","transform - Start - Capabilities operations filtering");
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
				
			Document  docParent = sxb.build(new File(filePath));
	    	Element racine = docParent.getRootElement();
	      
	    	//We can not modify Elements while we loop over them with an iterator.
	    	//We have to use a separate List storing the Elements we want to modify.
	    	
	    	//Operation filtering
	    	Filter xlinkFilter = new AttributeXlinkFilter();
	    	Element elementRequest = racine.getChild("Request", nsWMS);
	    	List<Element> requestList =  elementRequest.getChildren();
	    	List<Element> requestListToUpdate = new ArrayList<Element>();
	    	Iterator iRequest = requestList.iterator();
	    	while(iRequest.hasNext()){
	    		 Element courant = (Element)iRequest.next();
	    		 requestListToUpdate.add(courant);
	    	}
	    	
	    	Iterator iRequestToUpdate = requestList.iterator();
	    	while(iRequestToUpdate.hasNext()){
	    		 Element request = (Element)iRequestToUpdate.next();
	    		 //If Request is not allowed by policy or not supported by the current Easysdy proxy : the element is remove from the capabilities document	    		 
	    		 if(deniedOperations.contains(request.getName())){
	    			 Parent parent = request.getParent();
	    			 parent.removeContent (request);
	    		 }else{
					//The request is allowed and supported, we overwrite xlink attribute
					Iterator iXlink = request.getDescendants(xlinkFilter);
					List<Element> xlinkList = new ArrayList<Element>();	  
					while (iXlink.hasNext()){
						Element courant = (Element)iXlink.next();
						xlinkList.add(courant);
					}
					Iterator ilXlink = xlinkList.iterator();
					while(ilXlink.hasNext()){
						Element toUpdate = (Element)ilXlink.next();
						String att = toUpdate.getAttribute("href", nsXLINK).getValue();
						if(att.contains("?")){
							att = att.replace(att.substring(0, att.indexOf("?")), href);
						}else{
							att = href;
						}
						toUpdate.setAttribute("href", att, nsXLINK);
					}
	    		 }
	    	}
	    	
    	   XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
           sortie.output(docParent, new FileOutputStream(filePath));
           servlet.dump("INFO","transform - End - Capabilities operations filtering");
           return true;
		}
		catch (Exception ex){
			setLastException(ex);
			return false;
		}
	}

	@SuppressWarnings("rawtypes")
	@Override
	public Boolean CapabilitiesContentsFiltering(HashMap<String, String> wmsGetCapabilitiesResponseFilePath) {
		servlet.dump("INFO","transform - Start - Capabilities contents filtering");
	    try
	    {
	    	SAXBuilder sxb = new SAXBuilder();
	    	Iterator<Map.Entry<String, String>> iFile =  wmsGetCapabilitiesResponseFilePath.entrySet().iterator();
	    	while (iFile.hasNext())
	    	{
	    		Map.Entry<String, String > fileEntry = iFile.next(); 
	    		String filePath = fileEntry.getValue();
	    		Document  docParent = sxb.build(new File(filePath));
		    	Element racine = docParent.getRootElement();
		      
		    	//get the namespace
		    	Namespace localNsWMS = racine.getNamespace(); 
		    	
		    	//Layer filtering
		    	Filter layerFilter = new ElementLayerFilter();
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
		    		Element nameElement = layerElement.getChild("Name", localNsWMS);
		    		if (nameElement!= null && !servlet.isLayerAllowed(nameElement.getText(),servlet.getRemoteServerInfo(fileEntry.getKey()).getUrl()))
					{
		    				Parent parent = layerElement.getParent();
		    				parent.removeContent (layerElement);
					}
		    		else
		    		{
		    			//Rewrite Layer name with alias prefix
		    			String name = nameElement.getText();
		    			nameElement.setText(fileEntry.getKey()+"_"+name); 
		    		}
		    	}
		    	
	    	   XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	           sortie.output(docParent, new FileOutputStream(filePath));
	    	}
	    	servlet.dump("INFO","transform - End - Capabilities contents filtering");
           return true;
	    }
		catch (Exception ex )
		{
			setLastException(ex);
			return false;
		}
	}

	public Boolean CapabilitiesMerging(HashMap<String, String> filePathList) {
		servlet.dump("INFO","transform - Start - Capabilities merging");
		if (filePathList.size() == 0)
		{
			setLastException(new Exception("No response file"));
			return false;
		}
		if(filePathList.size() == 1)
			return true;

		try {
			SAXBuilder sxb = new SAXBuilder();
			RemoteServerInfo master = servlet.getRemoteServerInfoMaster();
			String fileMasterPath = filePathList.get(master.getAlias());
			Document documentMaster = sxb.build(new File(fileMasterPath));
			Filter layerFilter = new ElementLayerFilter();
			Element racineMaster = documentMaster.getRootElement();
			Iterator<Element> iLayer = racineMaster.getDescendants(layerFilter);
			
			while ()
			
			Element contentsMaster=  ((Element)racineMaster.getDescendants(layerFilter).next()).getParentElement();
			
			Enumeration<String> enumFile = filePathList.elements();
			while (enumFile.hasMoreElements())
			{
				String nfile = enumFile.nextElement();
				if(nfile.equals(fileMasterPath))
					continue;
				Document documentChild = null;
				documentChild = sxb.build(new File(nfile));
				if (documentChild != null) {
					Element racineChild = documentChild.getRootElement();
					Namespace localNsWMTS = racineChild.getNamespace(); 
					Element contentsChild = (Element)racineChild.getChild("Contents", localNsWMTS);
//					contentsMaster.addContent(contentsChild.cloneContent());
					Iterator<Element> ichild = contentsChild.getDescendants(new ElementLayerFilter());
//					int masterLayersSize = contentsMaster.getContent(new ElementLayerFilter()).size()+1;
					while (ichild.hasNext())
					{
						Element child = (Element)((Element)ichild.next()).clone();
						contentsMaster.addContent(1, child);
//						masterLayersSize +=1;
					}
					Iterator<Element> itmschild = contentsChild.getDescendants(new ElementTileMatrixSetFilter());
					while (itmschild.hasNext())
					{
						Element child = (Element)((Element)itmschild.next()).clone();
						contentsMaster.addContent(child);
					}
				}
			}
			
			XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	        sortie.output(documentMaster, new FileOutputStream(fileMasterPath));
	        servlet.dump("INFO","transform - End - Capabilities merging");
			return true;
		} catch (Exception ex) {
			setLastException(ex);
			return false;
		}
	}

	@Override
	public Boolean CapabilitiesServiceIdentificationWriting(String filePath,
			String href) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public Boolean CapabilitiesContentsFiltering(
			Hashtable<String, String> filePathList) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public Boolean CapabilitiesMerging(Hashtable<String, String> filePathList) {
		// TODO Auto-generated method stub
		return null;
	}

}
