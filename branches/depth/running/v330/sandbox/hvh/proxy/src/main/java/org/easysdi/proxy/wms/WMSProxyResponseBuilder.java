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
import java.util.Map.Entry;
import java.util.Set;
import java.util.TreeMap;
import java.util.Vector;

import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.core.ProxyRemoteServerResponse;
import org.easysdi.proxy.core.ProxyResponseBuilder;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.domain.SdiPhysicalservice;
import org.easysdi.proxy.domain.SdiPhysicalservicePolicy;
import org.easysdi.proxy.domain.SdiSysOperationcompliance;
import org.easysdi.proxy.domain.SdiVirtualmetadata;
import org.easysdi.proxy.domain.SdiWmsSpatialpolicy;
import org.easysdi.proxy.domain.SdiWmslayerPolicy;
import org.easysdi.proxy.jdom.filter.AttributeXlinkFilter;
import org.easysdi.proxy.jdom.filter.ElementExceptionFilter;
import org.easysdi.proxy.jdom.filter.ElementFormatFilter;
import org.easysdi.proxy.jdom.filter.ElementLayerFilter;
import org.easysdi.proxy.jdom.filter.ElementServiceExceptionFilter;
import org.geotools.geometry.jts.JTS;
import org.geotools.referencing.CRS;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.Namespace;
import org.jdom.Parent;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.opengis.referencing.NoSuchAuthorityCodeException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.operation.MathTransform;

import com.vividsolutions.jts.geom.Envelope;

/**
 * @author DEPTH SA
 *
 */
public abstract class WMSProxyResponseBuilder extends ProxyResponseBuilder{

	protected Namespace nsWMS ;
	
	public WMSProxyResponseBuilder(ProxyServlet proxyServlet) {
		super(proxyServlet);
		nsOWS = Namespace.getNamespace("http://www.opengis.net/ows");
		nsXLINK = Namespace.getNamespace("xlink","http://www.w3.org/1999/xlink");
	}
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesOperationsFiltering(java.lang.String, java.lang.String)
	 */
	@SuppressWarnings({ "unchecked", "rawtypes" })
	@Override
	public Boolean CapabilitiesOperationsFiltering(String filePath, String href) {
		servlet.logger.trace("transform - Start - Capabilities operations filtering");
		try{
			SAXBuilder sxb = new SAXBuilder();
	    	//Retrieve allowed and denied operations from the policy
			List<String> permitedOperations = new Vector<String>();
			List<String> deniedOperations = new Vector<String>();
			Set<SdiSysOperationcompliance> operationCompliances = servlet.getProxyRequest().getServiceCompliance().getSdiSysOperationcompliances();
		   Iterator<SdiSysOperationcompliance> i = operationCompliances.iterator();
		   while (i.hasNext())
		   {
			   SdiSysOperationcompliance compliance = i.next();
			   if(compliance.getSdiSysServiceoperation().getState() == 1 && compliance.getState() == 1 && compliance.isImplemented() && servlet.isOperationAllowed(compliance.getSdiSysServiceoperation().getValue()))
			   {
				   permitedOperations.add(compliance.getSdiSysServiceoperation().getValue());
				   servlet.logger.trace(compliance.getSdiSysServiceoperation().getValue() + " is permitted");
			   }
			   else
			   {
				   deniedOperations.add(compliance.getSdiSysServiceoperation().getValue());
				   servlet.logger.trace(compliance.getSdiSysServiceoperation().getValue() + " is denied");
				   
			   }
		   }
				
			Document  docParent = sxb.build(new File(filePath));
	    	Element racine = docParent.getRootElement();
	      
	    	//We can not modify Elements while we loop over them with an iterator.
	    	//We have to use a separate List storing the Elements we want to modify.
	    	
	    	//Operation filtering
	    	Filter xlinkFilter = new AttributeXlinkFilter();
	    	Element elementCapability = getChildElementCapability(racine);
	    	Element elementRequest = getChildElementRequest(elementCapability);
	    	List<Element> requestList =  elementRequest.getChildren();
	    	List<Element> requestListToUpdate = new ArrayList<Element>();
	    	Iterator iRequest = requestList.iterator();
	    	while(iRequest.hasNext()){
	    		 Element courant = (Element)iRequest.next();
	    		 requestListToUpdate.add(courant);
	    	}
	    	
	    	List<Element> toRemove =new ArrayList<Element>();
	    	Element getFeatureInfoElement = null;
	    	
	    	Iterator iRequestToUpdate = requestList.iterator();
	    	while(iRequestToUpdate.hasNext()){
	    		 Element request = (Element)iRequestToUpdate.next();
	    		 //If Request is not allowed by policy or not supported by the current Easysdy proxy : the element is remove from the capabilities document	    		 
	    		 if(deniedOperations.contains(request.getName())){
	    			 toRemove.add(request);
	    		 }else{
	    			 //The request is allowed and supported
	    			 //If request is GetFeatureInfo, only keep the format XML, other are not supported (can't be agregate by the proxy)
	    			 if(request.getName().equalsIgnoreCase("GetFeatureInfo")){
	    				 //Saved the element, the modifications will be made outside of the iteration loop
	    				 getFeatureInfoElement = request;
	    			 }
	    			//Overwrite xlink attribute
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
	    	
	    	if(getFeatureInfoElement != null){
	    		Filter formatFilter = new ElementFormatFilter();
	    		Iterator iFormat = getFeatureInfoElement.getDescendants(formatFilter);
				List<Element> formatList = new ArrayList<Element>();	  
				while (iFormat.hasNext()){
					Element courant = (Element)iFormat.next();
					if(!courant.getValue().contains("xml") && !courant.getValue().contains("gml"))
						formatList.add(courant);
				}
				Iterator<Element> ilFormat = formatList.iterator();
				while(ilFormat.hasNext()){
					getFeatureInfoElement.removeContent(ilFormat.next());
				}
	    	}
	    	
	    	Element elementException = getChildElementException(elementCapability);
	    	elementException.removeContent();
	    	elementException.addContent(getNewElementFormat().setText("application/vnd.ogc.se_xml"));
	    	
    	   XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
           sortie.output(docParent, new FileOutputStream(filePath));
           servlet.logger.trace("transform - End - Capabilities operations filtering");
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
	@SuppressWarnings({ "rawtypes", "unused" })
	@Override
	public Boolean CapabilitiesContentsFiltering(HashMap<String, String> wmsGetCapabilitiesResponseFilePath, String href) throws NoSuchAuthorityCodeException{
		servlet.logger.trace("transform - Start - Capabilities contents filtering");
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
		    		Element nameElement = getChildElementName(layerElement);
		    		if (nameElement!= null && !((WMSProxyServlet)servlet).isLayerAllowed(nameElement.getText(),servlet.getPhysicalServiceByAlias(fileEntry.getKey()).getResourceurl()))
					{
		    				Parent parent = layerElement.getParent();
		    				parent.removeContent (layerElement);
					}
		    		else if(nameElement != null)
		    		{
		    			//Rewrite Layer name with alias prefix
		    			String name = nameElement.getText();
		    			//Keep the prefix before the alias
		    			if(name.contains(":")){
		    				nameElement.setText(name.substring(0, name.indexOf(":"))+":"+fileEntry.getKey()+"_"+name.substring(name.indexOf(":",0)+1));
		    			}else{
		    				nameElement.setText(fileEntry.getKey()+"_"+name);
		    			}
		    					    			
		    			//Get the remote server URL
		    			String serverUrl = servlet.getPhysicalServiceByAlias(fileEntry.getKey()).getResourceurl();
		    			
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
		    	
		    	//Rewrite the BBOX according to the policy geographic filter
		    	Filter layerParentFilter = new ElementLayerFilter();
		    	List<Element> layerParentList = new ArrayList<Element>();	    	  
		    	Iterator iLayerParent= racine.getDescendants(layerFilter);
		    	while(iLayerParent.hasNext())
		    	{
		    	   Element courant = (Element)iLayerParent.next();
		    	   layerParentList.add(courant);
		    	}
		    	CoordinateReferenceSystem wgsCRS = CRS.decode("EPSG:4326");
			    if(!rewriteBBOX(layerParentList, wgsCRS, null))
			    	return false;
		    	
	    	   XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	           sortie.output(docParent, new FileOutputStream(filePath));
	    	}
	    	servlet.logger.trace("transform - End - Capabilities contents filtering");
           return true;
	    }catch (NoSuchAuthorityCodeException e){
	    	throw e;
	    }catch (Exception ex ) {
			setLastException(ex);
			return false;
		}
	}

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesMerging(java.util.HashMap)
	 */
	@SuppressWarnings({ "unused" })
	@Override
	public Boolean CapabilitiesMerging(HashMap<String, String> wmsGetCapabilitiesResponseFilePath) {
		servlet.logger.trace("transform - Start - Capabilities merging");
		if (wmsGetCapabilitiesResponseFilePath.size() == 0)
		{
			setLastException(new Exception("No response file"));
			return false;
		}
		if(wmsGetCapabilitiesResponseFilePath.size() == 1)
			return true;

		try {
			SAXBuilder sxb = new SAXBuilder();
			
			//Get the master physical service : first get from hashtable... 
			SdiPhysicalservice physicalServiceMaster = servlet.getPhysicalServiceMaster();
			
			String fileMasterPath = wmsGetCapabilitiesResponseFilePath.get(physicalServiceMaster.getAlias());
			Document documentMaster = sxb.build(new File(fileMasterPath));
			
			//Filter the layer
			Filter layerFilter = new ElementLayerFilter();
			Element racineMaster = documentMaster.getRootElement();
			Element capabilityMaster = getChildElementCapability(racineMaster);
			
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
					
					Element capabilityChild = getChildElementCapability(racineChild);
					
					Iterator<Element> ichild = getChildrenElementsLayer(capabilityChild).iterator();
					while (ichild.hasNext())
					{
						Element child = (Element)((Element)ichild.next()).clone();
						capabilityMaster.addContent(capabilityMaster.getContentSize(), child);
					}
				}
			}
			
			XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	        sortie.output(documentMaster, new FileOutputStream(fileMasterPath));
	        servlet.logger.trace("transform - End - Capabilities merging");
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
		servlet.logger.trace("transform - Start - Capabilities metadata writing");
		try
		{
			SAXBuilder sxb = new SAXBuilder();
			Document document = sxb.build(new File(filePath));
			
			if(servlet.getVirtualService().isReflectedmetadata())
			{
				//Service Metadata doesn't have to be overwrite 
				//Keep the service Metadata
				//Overwrite OnLineResource
				Element racine = document.getRootElement();
				Element service = racine.getChild("service");
				service.removeContent(service.getChild("OnlineResource"));
				
				Element onlineResource = new Element("OnlineResource");
				onlineResource.setAttribute("type", "simple",nsXLINK);
				onlineResource.setAttribute("href", href, nsXLINK);
				service.addContent(onlineResource);
			}
			else
			{
				SdiVirtualmetadata virtualMetadata = servlet.getVirtualService().getSdiVirtualmetadatas().iterator().next();
				
				//Remove the current Service element
				Element racine = document.getRootElement();
				Element oldService = (Element)racine.getChild("Service").clone();
				
				//Create a new Service element
				Element newService  = new Element ("Service");
				newService.addContent((new Element("Name")).setText("WMS"));
				
				if(!virtualMetadata.isInheritedtitle() && virtualMetadata.getTitle() != null && virtualMetadata.getTitle().length() != 0)
					newService.addContent((new Element("Title")).setText(virtualMetadata.getTitle()));
				else if(virtualMetadata.isInheritedtitle())
					newService.addContent((new Element("Title")).setText(oldService.getChildText("Title")));
				
				if(!virtualMetadata.isInheritedsummary() && virtualMetadata.getSummary() != null && virtualMetadata.getSummary().length() != 0)
					newService.addContent((new Element("Abstract")).setText(virtualMetadata.getSummary()));
				else if(virtualMetadata.isInheritedsummary())
					newService.addContent((new Element("Abstract")).setText(oldService.getChildText("Abstract")));
				
				if(!virtualMetadata.isInheritedkeyword() && virtualMetadata.getKeyword() != null && virtualMetadata.getKeyword().length() != 0)
				{
					Element keywords = new Element("KeywordsList");
					String[] words = virtualMetadata.getKeyword().split(",");
					for(String word: words){
						keywords.addContent((new Element("Keyword")).setText(word));
					}
					newService.addContent(keywords);
				}
				else if (virtualMetadata.isInheritedkeyword())
					newService.addContent((new Element("KeywordsList")).setContent(oldService.getChild("KeywordsList")));
				
				Element onlineResource = new Element("OnlineResource");
				onlineResource.setAttribute("type", "simple",nsXLINK);
				onlineResource.setAttribute("href", href, nsXLINK);
				newService.addContent(onlineResource);
				
				if(!virtualMetadata.isInheritedcontact())
				{
					Element newContactInformation = new Element("ContactInformation");
					Element newContactPersonPrimary = new Element("ContactPersonPrimary");
					Boolean hasContactPersonPrimary = false;
					
					if(virtualMetadata.getContactname() != null && virtualMetadata.getContactname().length() != 0){
						newContactPersonPrimary.addContent((new Element("ContactPerson")).setText(virtualMetadata.getContactname()));
						hasContactPersonPrimary = true;
					}
					if(virtualMetadata.getContactorganization() != null && virtualMetadata.getContactorganization().length() != 0){
						newContactPersonPrimary.addContent((new Element("ContactOrganization")).setText(virtualMetadata.getContactorganization()));
						hasContactPersonPrimary = true;
					}
					if(hasContactPersonPrimary)
						newContactInformation.addContent(newContactPersonPrimary);
					
					if (virtualMetadata.getContactposition() != null && virtualMetadata.getContactposition().length() != 0)
						newContactInformation.addContent((new Element("ContactPosition")).setText(virtualMetadata.getContactposition()));
					
					Element newContactAddress = new Element("ContactAddress");
					Boolean hasContactAddress = false;
					//TODO add the address type
//						if(virtualMetadata.getC != null && contactAddress.getType().length() != 0){
//							newContactAddress.addContent((new Element("AddressType")).setText(contactAddress.getType()));
//							hasContactAddress = true;
//						}
					if(virtualMetadata.getContactadress() != null && virtualMetadata.getContactadress().length() != 0){
						newContactAddress.addContent((new Element("Address")).setText(virtualMetadata.getContactadress()));
						hasContactAddress = true;
					}
					if(virtualMetadata.getContactlocality() != null && virtualMetadata.getContactlocality().length() != 0){
						newContactAddress.addContent((new Element("City")).setText(virtualMetadata.getContactlocality()));
						hasContactAddress = true;
					}
					
					if(virtualMetadata.getContactstate() != null && virtualMetadata.getContactstate().length() != 0){
						newContactAddress.addContent((new Element("StateOrProvince")).setText(virtualMetadata.getContactstate()));
						hasContactAddress = true;
					}
					if(virtualMetadata.getContactpostalcode() != null && virtualMetadata.getContactpostalcode().length() != 0){
						newContactAddress.addContent((new Element("PostCode")).setText(virtualMetadata.getContactpostalcode()));
						hasContactAddress = true;
					}
					if(virtualMetadata.getSdiSysCountry() != null && virtualMetadata.getSdiSysCountry().getName() != null){
						newContactAddress.addContent((new Element("Country")).setText(virtualMetadata.getSdiSysCountry().getName()));
						hasContactAddress = true;
					}
					
					if(hasContactAddress)
						newContactInformation.addContent(newContactAddress);
					
					if (virtualMetadata.getContactphone() != null && virtualMetadata.getContactphone().length() != 0)
						newContactInformation.addContent((new Element("ContactVoiceTelephone")).setText(virtualMetadata.getContactphone()));
					
					if (virtualMetadata.getContactfax() != null && virtualMetadata.getContactfax().length() != 0)
						newContactInformation.addContent((new Element("ContactFacsimileTelephone")).setText(virtualMetadata.getContactfax()));
					
					if (virtualMetadata.getContactemail() != null && virtualMetadata.getContactemail().length() != 0)
						newContactInformation.addContent((new Element("ContactElectronicMailAddress")).setText(virtualMetadata.getContactemail()));
					
					newService.addContent(newContactInformation);
				}
				else if (!virtualMetadata.isInheritedcontact())
					newService.addContent((new Element("ContactInformation")).setContent(oldService.getChild("ContactInformation")));
				
				if(!virtualMetadata.isInheritedfee() && virtualMetadata.getFee() != null && virtualMetadata.getFee().length() != 0)
					newService.addContent((new Element("Fees")).setText(virtualMetadata.getFee()));
				else if (virtualMetadata.isInheritedfee())
					newService.addContent((new Element("Fees")).setText(oldService.getChildText("Fees")));
				
				if(!virtualMetadata.isInheritedaccessconstraint() && virtualMetadata.getAccessconstraint() != null && virtualMetadata.getAccessconstraint().length() != 0)
					newService.addContent((new Element("AccessConstraints")).setText(virtualMetadata.getAccessconstraint()));
				else if (virtualMetadata.isInheritedaccessconstraint())
					newService.addContent((new Element("AccessConstraints")).setText(oldService.getChildText("AccessConstraints")));
				
				racine.removeContent(racine.getChild("Service"));
				racine.addContent( 1, newService);
			}
			
			XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	        sortie.output(document, new FileOutputStream(filePath));

	        servlet.logger.trace("transform - End - Capabilities metadata writing");
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
		Element racineParent = null;
		for (String key : remoteServerExceptionFiles.keySet()) {
			String path = remoteServerExceptionFiles.get(key);
			try {
				//Parent document
				if(docParent == null){
					docParent = sxb.build(new File(path));
					racineParent = docParent.getRootElement();
					
					//Get the serviceException elements of the parent document
					List<Element> serviceExceptionList = new ArrayList<Element>();
					Filter serviceExceptionFilter = new ElementServiceExceptionFilter();
					Iterator<Element> iSE = racineParent.getDescendants(serviceExceptionFilter);
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
			    	
			    	if(serviceExceptionList.size() == 0)
			    	{
			    		//This WMS Exception should contain serviceException element according to the service specifications (see OGC documentation)
			    		//But, it can also contain ows:Exception element define in the OWS common specification (see OGC documentation)
			    		//Get the serviceows:Exception element if serviceException does not exist
			    		Filter exceptionFilter = new ElementExceptionFilter();
			    		Iterator<Element> iE = racineParent.getDescendants(exceptionFilter);
						while (iE.hasNext()){
							Element serviceException = (Element)iE.next();
							serviceExceptionList.add(serviceException);
						}
						
						//Add the server alias in the exception text
						Iterator<Element> iEL = serviceExceptionList.iterator();
				    	while (iEL.hasNext()){
				    		Element serviceException = (Element)iEL.next();
				    		Element exceptionText = serviceException.getChild("ExceptionText",nsOWS );
				    		exceptionText.setText( String.format(TEXT_SERVER_ALIAS, key) + exceptionText.getText());
				    	}
			    	}
			    	continue;
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
		    			racineParent.addContent(serviceException);
		    		}else{
		    			servlet.logger.error("WMSProxyResponseBuilder.ExceptionAggregation can not correctly rewrite exception.");
		    		}
		    	}
		    	
		    	if(serviceExceptionList.size() == 0)
		    	{
		    		//This WMS Exception should contain serviceException element according to the service specifications (see OGC documentation)
		    		//But, it can also contain ows:Exception element define in the OWS common specification (see OGC documentation)
		    		//Get the serviceows:Exception element if serviceException does not exist
		    		Filter exceptionFilter = new ElementExceptionFilter();
		    		Iterator<Element> iE = racine.getDescendants(exceptionFilter);
					while (iE.hasNext()){
						Element serviceException = (Element)iE.next();
						serviceExceptionList.add(serviceException);
					}
					
					//Add the server alias in the exception text
					Iterator<Element> iEL = serviceExceptionList.iterator();
			    	while (iEL.hasNext()){
			    		Element serviceException = (Element)iEL.next();
			    		Element exceptionText = serviceException.getChild("ExceptionText",nsOWS );
			    		exceptionText.setText( String.format(TEXT_SERVER_ALIAS, key) + exceptionText.getText());
			    		if(serviceException.getParent().removeContent(serviceException)){
			    			racineParent.addContent(serviceException);
			    		}else{
			    			servlet.logger.error("WMSProxyResponseBuilder.ExceptionAggregation can not correctly rewrite exception.");
			    		}
			    	}
		    	}
		    	
				
			} catch (JDOMException e) {
				servlet.logger.error("WMSProxyResponseBuilder.ExceptionAggregation - ",e);
			} catch (IOException e) {
				servlet.logger.error("WMSProxyResponseBuilder.ExceptionAggregation - ",e);			
			}
		}
		
		ByteArrayOutputStream out = new ByteArrayOutputStream();
		XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
		try {
			sortie.output(docParent, out);
		} catch (IOException e) {
			servlet.logger.error("WMSProxyResponseBuilder.ExceptionAggregation - ",e);
		}
		return out;
	}

	/**
	 * Rewrite the BoundingBox definition according to the geographic filter defined in the policy 
	 * @param elementLayer : the layer
	 * @param wgsCRS : the EPSG:4326 CoordinateReferenceSystem
	 * @param parentCRS : the CoordinateReferenceSystem of the parent layer
	 * @return
	 */
	@SuppressWarnings("unchecked")
	protected boolean rewriteBBOX(List<Element> layersList,CoordinateReferenceSystem wgsCRS,CoordinateReferenceSystem parentCRS) throws NoSuchAuthorityCodeException
	{
		try{
			String wgsMaxx;
			String wgsMaxy;
			String wgsMinx;
			String wgsMiny;
			
			for(int l = 0 ; l < layersList.size();l++)
			{
				Element elementLayer = (Element)layersList.get(l);
				String layerName = getLayerName(elementLayer);
				//Layer does not have a <Name>, it is not a layer that can be requested  
    			if(layerName == null)
    					continue;
    			ProxyLayer proxyLayer = new ProxyLayer(layerName);
    							
				Set<SdiPhysicalservicePolicy> physicalServicePolicies = servlet.getPolicy().getSdiPhysicalservicePolicies();
				Iterator<SdiPhysicalservicePolicy> ip = physicalServicePolicies.iterator();
				while(ip.hasNext())
				{
					SdiPhysicalservicePolicy physicalservicePolicy = ip.next();
					Set<SdiWmslayerPolicy> wmsLayerPolicies =  physicalservicePolicy.getSdiWmslayerPolicies();
					Iterator<SdiWmslayerPolicy> ilayers = wmsLayerPolicies.iterator();
					while(ilayers.hasNext())
					{
						SdiWmslayerPolicy layer = ilayers.next();
						if(layer.getName().equals(proxyLayer.getPrefixedName()))
						{
							SdiWmsSpatialpolicy spatialPolicy = layer.getSdiWmsSpatialpolicy();
							if(spatialPolicy != null)
							{
								if(spatialPolicy.getSrssource()!= null)
								{
									if(spatialPolicy.getSrssource().equalsIgnoreCase("EPSG:4326"))
					    			{
					    				wgsMaxx = (spatialPolicy.getMaxx()).toString();
					    				wgsMaxy = (spatialPolicy.getMaxy()).toString();
					    				wgsMinx = (spatialPolicy.getMinx()).toString();
					    				wgsMiny = (spatialPolicy.getMiny()).toString();
					    			}	
					    			else
					    			{
					    				
						    				CoordinateReferenceSystem sourceCRS = CRS.decode(spatialPolicy.getSrssource());
						    				MathTransform transform = CRS.findMathTransform(sourceCRS, wgsCRS);
						    				Envelope sourceEnvelope = new Envelope(spatialPolicy.getMinx().doubleValue(),spatialPolicy.getMaxx().doubleValue(),spatialPolicy.getMiny().doubleValue(),spatialPolicy.getMaxy().doubleValue());
						    				Envelope targetEnvelope = JTS.transform( sourceEnvelope, transform);
						    				wgsMaxx = (String.valueOf(targetEnvelope.getMaxX()));
						    				wgsMaxy = (String.valueOf(targetEnvelope.getMaxY()));
						    				wgsMinx = (String.valueOf(targetEnvelope.getMinX()));
						    				wgsMiny = (String.valueOf(targetEnvelope.getMinY()));
					    				
					    			}
									
									if( !writeLatLonBBOX(elementLayer, wgsMinx, wgsMiny,wgsMaxx, wgsMaxy)) return false;
					    			
					    			parentCRS = writeCRSBBOX(elementLayer,spatialPolicy,wgsCRS,parentCRS,wgsMinx,wgsMiny,wgsMaxx,wgsMaxy);
					    			
					    			Filter filtre = new WMSProxyCapabilitiesLayerFilter();
					    			Iterator<Element> itL = elementLayer.getDescendants(filtre);
					    			List<org.jdom.Element> sublayersList = new ArrayList<org.jdom.Element>();
							    	while(itL.hasNext())
									{
							    		sublayersList.add((org.jdom.Element)itL.next());
									}
					    			while(itL.hasNext())
									{
							    		if ( !rewriteBBOX(sublayersList, wgsCRS,parentCRS) ) return false;
									}
								}
							}
						}
					}
				}
			}
			return true;
		}catch (NoSuchAuthorityCodeException e){
			throw e;
		} catch (Exception e) {
			setLastException(e);
			return false;
		} 
	}
	
	/**
	 * Rewrite the LatLonBoundingBox Element
	 * <LatLonBoundingBox minx="-6.06258" miny="41.1632" maxx="10.8783" maxy="51.2918"/>
	 * @param elementLayer : the layer
	 * @param wgsMinx : minx in EPSG:4326 reference system
	 * @param wgsMiny : miny in EPSG:4326 reference system
	 * @param wgsMaxx : maxx in EPSG:4326 reference system
	 * @param wgsMaxy : maxy in EPSG:4326 reference system
	 * @return
	 */
	@SuppressWarnings("rawtypes")
	protected boolean writeLatLonBBOX(Element elementLayer, String wgsMinx, String wgsMiny, String wgsMaxx, String wgsMaxy)
	{
		try
		{
			List llBBOXElements = elementLayer.getChildren("LatLonBoundingBox");
			if(llBBOXElements.size() != 0)
			{
	    		Element llBBOX = (Element) llBBOXElements.get(0);
	    		llBBOX.setAttribute("minx",wgsMinx);
	    		llBBOX.setAttribute("miny", wgsMiny);
	    		llBBOX.setAttribute("maxx", wgsMaxx);
	    		llBBOX.setAttribute("maxy", wgsMaxy);
			}
			else
			{
				//create element
				Element element = new Element("LatLonBoundingBox");
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
			setLastException(e);
			return false;
		}
	}
	
	/**
	 * Rewrite the BoundingBox element
	 * <BoundingBox SRS="EPSG:27582" minx="10000" miny="1.6e+06" maxx="1.2e+06" maxy="2.7e+06"/>
	 * @param elementLayer
	 * @param spatialPolicy : the BoundingBox define in the policy filter
	 * @param wgsCRS : the EPSG:4326 CoordinateReferenceSystem
	 * @param parentCRS : the CoordinateReferenceSystem of the parent layer
	 * @param wgsMinx : minx in EPSG:4326 reference system
	 * @param wgsMiny : miny in EPSG:4326 reference system
	 * @param wgsMaxx : maxx in EPSG:4326 reference system
	 * @param wgsMaxy : maxy in EPSG:4326 reference system
	 * @return
	 */
	protected CoordinateReferenceSystem writeCRSBBOX(Element elementLayer,SdiWmsSpatialpolicy spatialPolicy,CoordinateReferenceSystem wgsCRS,CoordinateReferenceSystem parentCRS,String wgsMinx, String wgsMiny, String wgsMaxx, String wgsMaxy)
	{
		try
		{
			List<Element> srsBBOXElements = getElementBoundingBox(elementLayer); 
			if(srsBBOXElements.size() == 0)
			{
				//No BoundingBox for specific SRS : get the SRS parent to create one --> Needed only for WFS 1.3.0 but
				if(parentCRS == null)
					return null;
				MathTransform transform = CRS.findMathTransform(wgsCRS, parentCRS);
				Envelope sourceEnvelope = new Envelope(Double.valueOf(wgsMinx),Double.valueOf(wgsMaxx),Double.valueOf(wgsMiny),Double.valueOf(wgsMaxy));
				Envelope targetEnvelope = JTS.transform( sourceEnvelope, transform);
			
				Element BBOX = createElementBoundingBox();	
				BBOX.setAttribute(getAttributeSRS(), parentCRS.getIdentifiers().toArray()[0].toString());
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
					Element BBOX = (Element) srsBBOXElements.get(j);
					if(BBOX.getAttributeValue(getAttributeSRS()).equals(spatialPolicy.getSrssource()))
					{
						if(j == 0)
						{
							parentCRS = CRS.decode(BBOX.getAttributeValue(getAttributeSRS()));
						}
						BBOX.setAttribute("minx", spatialPolicy.getMinx().toString());
						BBOX.setAttribute("miny", spatialPolicy.getMiny().toString());
						BBOX.setAttribute("maxx", spatialPolicy.getMaxx().toString());
						BBOX.setAttribute("maxy", spatialPolicy.getMaxy().toString());
					}
					else
					{
						CoordinateReferenceSystem targetCRS =CRS.decode(BBOX.getAttributeValue(getAttributeSRS()));
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
			setLastException(e);
			return parentCRS;
		}
	}
	
	/**
	 * Return a list  containing the BoundingBox definition of the specified element <Layer>
	 * @param elementLayer
	 * @return
	 */
	@SuppressWarnings("unchecked")
	protected List<Element> getElementBoundingBox (Element elementLayer)
	{
		return elementLayer.getChildren("BoundingBox"); 
	}
	
	/**
	 * @return a new Element defining a BoundingBox
	 */
	protected Element createElementBoundingBox ()
	{
		return new Element("BoundingBox");
	}

	
	/**
	 * @return the attribute name to use to define the coordinate reference system
	 */
	protected String getAttributeSRS ()
	{
		return "SRS";
	}
	
	/**
	 * @param layer
	 * @return the value of the <Name> element
	 */
	protected String getLayerName (Element layer){
		if(layer.getChild("Name") != null)
			return layer.getChild("Name").getValue();
		else
			return null;
	}
	
	/**
	 * @param parent
	 * @return
	 */
	protected Element getChildElementName (Element parent){
		return parent.getChild("Name");
	}
	
	/**
	 * @param parent
	 * @return
	 */
	protected Element getChildElementCapability (Element parent){
		return parent.getChild("Capability");
	}
	
	/**
	 * @param parent
	 * @return
	 */
	@SuppressWarnings("unchecked")
	protected List<Element> getChildrenElementsLayer (Element parent){
		return parent.getChildren("Layer");
	}
	
	/**
	 * @param parent
	 * @return
	 */
	protected Element getChildElementRequest (Element parent){
		return parent.getChild("Request");
	}
	
	/**
	 * @param parent
	 * @return
	 */
	protected Element getChildElementException (Element parent){
		return parent.getChild("Exception");
	}
	
	/**
	 * @param parent
	 * @return
	 */
	protected Element getNewElementFormat (){
		return new Element("Format");
	}
	
	public ByteArrayOutputStream GetFeatureInfoAggregation (TreeMap<Integer, ProxyRemoteServerResponse> wmsGetFeatureInfoResponseFilePath){
		try {
			SAXBuilder sxb = new SAXBuilder();
			
			Document doc = new Document();
			Element root = new Element("GetFeatureInfoResponse");
			doc.setRootElement(root);
			
			Iterator<Entry<Integer, ProxyRemoteServerResponse>> it = wmsGetFeatureInfoResponseFilePath.entrySet().iterator();
			while (it.hasNext()){
				Entry<Integer, ProxyRemoteServerResponse> entry = it.next();
				Element child = new Element(entry.getValue().getAlias());
				Document docChild = sxb.build(new File(entry.getValue().getPath()));
				Element rootChild = docChild.getRootElement();
				rootChild.detach();
				child.addContent(rootChild);
				root.addContent(child);
			}
			 
			ByteArrayOutputStream out = new ByteArrayOutputStream();
			XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
			sortie.output(doc, out);
			return out;
			
		} catch (JDOMException e) {
			setLastException(e);
		} catch (IOException e) {
			setLastException(e);
		}
		return null;
		
	}

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.core.ProxyResponseBuilder#CapabilitiesContentsFiltering(java.util.Hashtable)
	 */
	@Override
	public Boolean CapabilitiesContentsFiltering(Hashtable<String, String> filePathList)throws NoSuchAuthorityCodeException {
		return null;
	}	
}
