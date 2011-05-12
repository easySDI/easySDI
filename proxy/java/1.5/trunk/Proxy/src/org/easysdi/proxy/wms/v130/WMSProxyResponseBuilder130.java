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
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Vector;

import org.easysdi.jdom.filter.AttributeXlinkFilter;
import org.easysdi.jdom.filter.ElementLayerFilter;
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
		nsWMS = Namespace.getNamespace("http://www.opengis.net/wms");
	}
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesOperationsFiltering(java.lang.String, java.lang.String)
	 */
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
	    	Element elementCapability = racine.getChild("Capability", nsWMS);
	    	Element elementRequest = elementCapability.getChild("Request", nsWMS);
	    	List<Element> requestList =  elementRequest.getChildren();
	    	List<Element> requestListToUpdate = new ArrayList<Element>();
	    	Iterator iRequest = requestList.iterator();
	    	while(iRequest.hasNext()){
	    		 Element courant = (Element)iRequest.next();
	    		 requestListToUpdate.add(courant);
	    	}
	    	
	    	List<Element> toRemove =new ArrayList<Element>();
	    	
	    	Iterator iRequestToUpdate = requestList.iterator();
	    	while(iRequestToUpdate.hasNext()){
	    		 Element request = (Element)iRequestToUpdate.next();
	    		 //If Request is not allowed by policy or not supported by the current Easysdy proxy : the element is remove from the capabilities document	    		 
	    		 if(deniedOperations.contains(request.getName())){
//	    			 Parent parent = request.getParent();
//	    			 parent.removeContent (request);
	    			 toRemove.add(request);
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
	    	
	    	Iterator<Element> iToRemove = toRemove.iterator();
	    	while (iToRemove.hasNext()){
	    		Element request = iToRemove.next();
	    		request.getParent().removeContent(request);
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

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesContentsFiltering(java.util.HashMap)
	 */
	@SuppressWarnings("rawtypes")
	@Override
	public Boolean CapabilitiesContentsFiltering(HashMap<String, String> wmsGetCapabilitiesResponseFilePath) {
		servlet.dump("INFO","transform - Start - Capabilities contents filtering");
	    try
	    {
	    	SAXBuilder sxb = new SAXBuilder();
	    	Iterator<Entry<String, String>> iFile =  wmsGetCapabilitiesResponseFilePath.entrySet().iterator();
	    	while (iFile.hasNext())
	    	{
	    		Map.Entry<String, String > fileEntry = iFile.next(); 
	    		String filePath = fileEntry.getValue();
	    		Document  docParent = sxb.build(new File(filePath));
		    	Element racine = docParent.getRootElement();
		      
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
		    		Element nameElement = layerElement.getChild("Name", nsWMS);
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

	/**
	 * @param wmsGetCapabilitiesResponseFilePath
	 * @return
	 */
	@SuppressWarnings("unchecked")
	public Boolean CapabilitiesMerging(HashMap<String, String> wmsGetCapabilitiesResponseFilePath) {
		servlet.dump("INFO","transform - Start - Capabilities merging");
		if (wmsGetCapabilitiesResponseFilePath.size() == 0)
		{
			setLastException(new Exception("No response file"));
			return false;
		}
		if(wmsGetCapabilitiesResponseFilePath.size() == 1)
			return true;

		try {
			SAXBuilder sxb = new SAXBuilder();
			
			//Get the master remote server 
			RemoteServerInfo master = servlet.getRemoteServerInfoMaster();
			String fileMasterPath = wmsGetCapabilitiesResponseFilePath.get(master.getAlias());
			Document documentMaster = sxb.build(new File(fileMasterPath));
			
			//Filter the layer
			Element racineMaster = documentMaster.getRootElement();
			Element capabilityMaster = (Element)racineMaster.getChild("Capability", nsWMS);
			
			Iterator<Entry<String,String>> it = wmsGetCapabilitiesResponseFilePath.entrySet().iterator();
			while (it.hasNext())
			{
				Entry<String,String> entry = it.next();
				String nfile = entry.getValue();
				
				//If it is the master document, continue
				if(nfile.equals(fileMasterPath))
					continue;
				
				Document documentChild = null;
				documentChild = sxb.build(new File(nfile));
				if (documentChild != null) {
					Element racineChild = documentChild.getRootElement();
					
					Element capabilityChild = (Element)racineChild.getChild("Capability", nsWMS);
					
					Iterator<Element> ichild = capabilityChild.getChildren("Layer", nsWMS).iterator();
					while (ichild.hasNext())
					{
						Element child = (Element)((Element)ichild.next()).clone();
						capabilityMaster.addContent(capabilityMaster.getContentSize(), child);
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
	public Boolean CapabilitiesServiceIdentificationWriting(String filePath,String href) {
		return true;
	}

	

}
