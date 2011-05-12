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

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Vector;
import java.util.Map.Entry;

import org.easysdi.jdom.filter.AttributeXlinkFilter;
import org.easysdi.jdom.filter.ElementLayerFilter;
import org.easysdi.jdom.filter.ElementServiceExceptionFilter;
import org.easysdi.jdom.filter.ElementServiceExceptionReportFilter;
import org.easysdi.proxy.core.ProxyResponseBuilder;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.xml.documents.Config;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.easysdi.xml.documents.ServiceContactAdressInfo;
import org.easysdi.xml.documents.ServiceContactInfo;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
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
public abstract class WMSProxyResponseBuilder extends ProxyResponseBuilder{

	protected Namespace nsWMS ;
	
	public WMSProxyResponseBuilder(ProxyServlet proxyServlet) {
		super(proxyServlet);
		nsXLINK = Namespace.getNamespace("xlink","http://www.w3.org/1999/xlink");
	}
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesOperationsFiltering(java.lang.String, java.lang.String)
	 */
	@SuppressWarnings({ "unchecked", "rawtypes" })
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
	    	Element elementCapability = racine.getChild("Capability");
	    	Element elementRequest = elementCapability.getChild("Request");
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
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesContentsFiltering(java.util.Hashtable)
	 */
	@Override
	public Boolean CapabilitiesContentsFiltering( Hashtable<String, String> wmsGetCapabilitiesResponseFilePath) {
		return false;
	}

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesContentsFiltering(java.util.HashMap)
	 */
	@SuppressWarnings("rawtypes")
	@Override
	public Boolean CapabilitiesContentsFiltering(HashMap<String, String> wmsGetCapabilitiesResponseFilePath, String href) {
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
		    		Element nameElement = layerElement.getChild("Name");
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
		    					    			
		    			//Get the remote server URL
		    			String serverUrl = servlet.getRemoteServerInfo(fileEntry.getKey()).getUrl();
		    			//Rewrite the online resource present in the <Style> element
		    			Iterator iXlink = layerElement.getDescendants(new AttributeXlinkFilter());
		    			List<Element> xlinkList = new ArrayList<Element>();	  
						while (iXlink.hasNext()){
							Element courant = (Element)iXlink.next();
							xlinkList.add(courant);
						}
						Iterator ilXlink = xlinkList.iterator();
						while(ilXlink.hasNext()){
							Element toUpdate = (Element)ilXlink.next();
							String att = toUpdate.getAttribute("href", nsXLINK).getValue();
							if(att.contains(serverUrl)){
								att = att.replace(att.substring(0, att.indexOf("?")), href);
							}
							toUpdate.setAttribute("href", att, nsXLINK);
						}
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

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesMerging(java.util.Hashtable)
	 */
	@Override
	public Boolean CapabilitiesMerging(Hashtable<String, String> filePathList) {
		return false;
	}

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesMerging(java.util.HashMap)
	 */
	@SuppressWarnings({ "unused", "unchecked" })
	@Override
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
			Filter layerFilter = new ElementLayerFilter();
			Element racineMaster = documentMaster.getRootElement();
			Element capabilityMaster = (Element)racineMaster.getChild("Capability");
			
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
					
					Element capabilityChild = (Element)racineChild.getChild("Capability");
					
					Iterator<Element> ichild = capabilityChild.getChildren("Layer").iterator();
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

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesServiceIdentificationWriting(java.lang.String, java.lang.String)
	 */
	@Override
	public Boolean CapabilitiesServiceMetadataWriting(String filePath,String href) {
		servlet.dump("INFO","transform - Start - Capabilities metadata writing");
		try
		{
			Config config = servlet.getConfiguration();
			
			SAXBuilder sxb = new SAXBuilder();
			Document document = sxb.build(new File(filePath));
			
			//Remove the current Service element
			Element racine = document.getRootElement();
			racine.removeContent(racine.getChild("Service"));
			
			//Create a new Service element
			Element newService  = new Element ("Service");
			newService.addContent((new Element("Name")).setText("WMS"));
			
			if(config == null )
			{
				XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
		        sortie.output(document, new FileOutputStream(filePath));
				return true;
			}
						
			
			if(config.getTitle() != null && config.getTitle().length() != 0)
				newService.addContent((new Element("Title")).setText(config.getTitle()));
			if(config.getAbst() != null && config.getAbst().length() != 0)
				newService.addContent((new Element("Abstract")).setText(config.getAbst()));
			if(config.getKeywordList() != null && config.getKeywordList().size() != 0)
			{
				Element keywords = new Element("KeywordsList");
				Iterator<String> iKeywords = config.getKeywordList().iterator();
				while (iKeywords.hasNext())
				{
					keywords.addContent((new Element("Keyword")).setText(iKeywords.next()));
				}
				newService.addContent(keywords);
			}
			Element onlineResource = new Element("OnlineResource");
			onlineResource.setAttribute("href", href, nsXLINK);
//			onlineResource.setAttribute("type", "simple");
			newService.addContent(onlineResource);
			
			
			if(config.getContactInfo() != null && !config.getContactInfo().isEmpty()){
				Element newContactInformation = new Element("ContactInformation");
				
				ServiceContactInfo contactInfo = config.getContactInfo();
				
				Element newContactPersonPrimary = new Element("ContactPersonPrimary");
				Boolean hasContactPersonPrimary = false;
				if(contactInfo.getName() != null && contactInfo.getName().length() != 0){
					newContactPersonPrimary.addContent((new Element("ContactPerson")).setText(contactInfo.getName()));
					hasContactPersonPrimary = true;
				}
				if(contactInfo.getOrganization() != null && contactInfo.getOrganization().length() != 0){
					newContactPersonPrimary.addContent((new Element("ContactOrganization")).setText(contactInfo.getOrganization()));
					hasContactPersonPrimary = true;
				}
				if(hasContactPersonPrimary)
					newContactInformation.addContent(newContactPersonPrimary);
				
				if (contactInfo.getPosition() != null && contactInfo.getPosition().length() != 0)
					newContactInformation.addContent((new Element("ContactPosition")).setText(contactInfo.getPosition()));
				
				ServiceContactAdressInfo contactAddress = contactInfo.getContactAddress();
				Element newContactAddress = new Element("ContactAddress");
				Boolean hasContactAddress = false;
				if(contactAddress.getType() != null && contactAddress.getType().length() != 0){
					newContactAddress.addContent((new Element("AddressType")).setText(contactAddress.getType()));
					hasContactAddress = true;
				}
				if(contactAddress.getAddress() != null && contactAddress.getAddress().length() != 0){
					newContactAddress.addContent((new Element("Address")).setText(contactAddress.getAddress()));
					hasContactAddress = true;
				}
				if(contactAddress.getCity() != null && contactAddress.getCity().length() != 0){
					newContactAddress.addContent((new Element("City")).setText(contactAddress.getCity()));
					hasContactAddress = true;
				}
				if(contactAddress.getState() != null && contactAddress.getState().length() != 0){
					newContactAddress.addContent((new Element("StateOrProvince")).setText(contactAddress.getState()));
					hasContactAddress = true;
				}
				if(contactAddress.getPostalCode() != null && contactAddress.getPostalCode().length() != 0){
					newContactAddress.addContent((new Element("PostCode")).setText(contactAddress.getPostalCode()));
					hasContactAddress = true;
				}
				if(contactAddress.getCountry() != null && contactAddress.getCountry().length() != 0){
					newContactAddress.addContent((new Element("Country")).setText(contactAddress.getCountry()));
					hasContactAddress = true;
				}
				
				if(hasContactAddress)
					newContactInformation.addContent(newContactAddress);
				
				if (contactInfo.getVoicePhone() != null && contactInfo.getVoicePhone().length() != 0)
					newContactInformation.addContent((new Element("ContactVoiceTelephone")).setText(contactInfo.getVoicePhone()));
				
				if (contactInfo.getFacSimile() != null && contactInfo.getFacSimile().length() != 0)
					newContactInformation.addContent((new Element("ContactFacsimileTelephone")).setText(contactInfo.getFacSimile()));
				
				if (contactInfo.geteMail() != null && contactInfo.geteMail().length() != 0)
					newContactInformation.addContent((new Element("ContactElectronicMailAddress")).setText(contactInfo.geteMail()));
				
				newService.addContent(newContactInformation);
			}
			
				
			if(config.getFees() != null && config.getFees().length() != 0)
				newService.addContent((new Element("Fees", nsWMS)).setText(config.getFees()));
			if(config.getAccessConstraints() != null && config.getAccessConstraints().length() != 0)
				newService.addContent((new Element("AccessConstraints", nsWMS)).setText(config.getAccessConstraints()));
			
			racine.addContent( 1, newService);
			
			XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	        sortie.output(document, new FileOutputStream(filePath));

	        servlet.dump("INFO","transform - End - Capabilities metadata writing");
			return true;
		}
		catch (Exception ex)
		{
			setLastException(ex);
			return false;
		}
	}

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#ExceptionAggregation(java.util.HashMap)
	 */
	@SuppressWarnings("unchecked")
	@Override
	public ByteArrayOutputStream ExceptionAggregation(HashMap<String, String> remoteServerExceptionFiles) {
		SAXBuilder sxb = new SAXBuilder();
		
		Document docParent = null; 
		Element serviceExceptionReport=null;
		for (String key : remoteServerExceptionFiles.values()) {
			String path = remoteServerExceptionFiles.get(key);
			try {
				//Parent document
				if(docParent == null){
					docParent = sxb.build(new File(path));
					Element racine = docParent.getRootElement();
					
					//Get the serviceExceptionReport element
					Filter serviceExceptionReportFilter = new ElementServiceExceptionReportFilter();
					Iterator<Element> iSER= racine.getDescendants(serviceExceptionReportFilter);
					while (iSER.hasNext()){
						serviceExceptionReport = (Element)iSER.next();
						break;
					}
					
					//Get the serviceException elements of the parent document
					List<Element> serviceExceptionList = new ArrayList<Element>();
					Filter serviceExceptionFilter = new ElementServiceExceptionFilter();
					Iterator<Element> iSE = serviceExceptionReport.getDescendants(serviceExceptionFilter);
					while (iSE.hasNext()){
						Element serviceException = (Element)iSE.next();
						serviceExceptionList.add(serviceException);
					}
					
					//Add the server alias in the exception text
					Iterator<Element> iSEL = serviceExceptionList.iterator();
			    	while (iSEL.hasNext()){
			    		Element serviceException = (Element)iSEL.next();
			    		serviceException.setText( String.format(TEXT_SERVER_ALIAS, key) + serviceException.getText());
			    	}
				}
				
				//Child document
		    	Document docChild = sxb.build(new File(path));
		    	Element racine = docChild.getRootElement();
		    	//Get the serviceException elements of the child document
				List<Element> serviceExceptionList = new ArrayList<Element>();
				Filter serviceExceptionFilter = new ElementServiceExceptionFilter();
				Iterator<Element> iSE = racine.getDescendants(serviceExceptionFilter);
				while (iSE.hasNext()){
					Element serviceException = (Element)iSE.next();
					serviceExceptionList.add(serviceException);
				}
				
				//Add the server alias in the exception text
				Iterator<Element> iSEL = serviceExceptionList.iterator();
		    	while (iSEL.hasNext()){
		    		Element serviceException = (Element)iSEL.next();
		    		serviceException.setText( String.format(TEXT_SERVER_ALIAS, key) + serviceException.getText());
		    		if(serviceException.getParent().removeContent(serviceException)){
		    			serviceExceptionReport.addContent(serviceException);
		    		}else{
		    			//TODO :error
		    		}
		    	}
		    	
				
			} catch (JDOMException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		
		ByteArrayOutputStream out = new ByteArrayOutputStream();
		XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
		try {
			sortie.output(docParent, out);
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return out;
	}

}
