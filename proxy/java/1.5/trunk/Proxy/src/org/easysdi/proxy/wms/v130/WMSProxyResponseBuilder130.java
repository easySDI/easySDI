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
import org.easysdi.jdom.filter.ElementFormatFilter;
import org.easysdi.jdom.filter.ElementLayerFilter;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.policy.BoundingBox;
import org.easysdi.proxy.wms.WMSProxyResponseBuilder;
import org.easysdi.xml.documents.Config;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.easysdi.xml.documents.ServiceContactAdressInfo;
import org.easysdi.xml.documents.ServiceContactInfo;
import org.geotools.referencing.CRS;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.Parent;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.opengis.referencing.crs.CoordinateReferenceSystem;

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
	    	Element getFeatureInfoElement = null;
	    	
	    	Iterator iRequestToUpdate = requestList.iterator();
	    	while(iRequestToUpdate.hasNext()){
	    		 Element request = (Element)iRequestToUpdate.next();
	    		 //If Request is not allowed by policy or not supported by the current Easysdy proxy : the element is remove from the capabilities document	    		 
	    		 if(deniedOperations.contains(request.getName())){
//	    			 Parent parent = request.getParent();
//	    			 parent.removeContent (request);
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
	    	
	    	Element elementException = elementCapability.getChild("Exception", nsWMS);
	    	elementException.removeContent();
	    	elementException.addContent((new Element("Format", nsWMS)).setText("application/vnd.ogc.se_xml"));
	    	
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
	@SuppressWarnings({ "rawtypes", "unused" })
	@Override
	public Boolean CapabilitiesContentsFiltering(HashMap<String, String> wmsGetCapabilitiesResponseFilePath, String href) {
		servlet.dump("INFO","transform - Start - Capabilities contents filtering");
	    try
	    {
	    	CoordinateReferenceSystem wgsCRS = CRS.decode("EPSG:4326");
	    	
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
		    		else if(nameElement != null)
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
		    	
		    	//Rewrite the BBOX according to the policy geographic filter
		    	Filter layerParentFilter = new ElementLayerFilter();
		    	List<Element> layerParentList = new ArrayList<Element>();	    	  
		    	Iterator iLayerParent= racine.getDescendants(layerFilter);
		    	while(iLayerParent.hasNext())
		    	{
		    	   Element courant = (Element)iLayerParent.next();
		    	   layerParentList.add(courant);
		    	}
		    	if(!rewriteBBOX(layerParentList, wgsCRS, null))
		    		return false;
		    	
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
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#CapabilitiesMerging(java.util.HashMap)
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

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#CapabilitiesServiceMetadataWriting(java.lang.String, java.lang.String)
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
			
			Element oldLayerLimit=null;
			Element oldMaxWidth=null;
			Element oldMaxHeight=null;
			if((Element)racine.getChild("LayerLimit",nsWMS) != null)
				oldLayerLimit = (Element)racine.getChild("LayerLimit",nsWMS).clone();
			if((Element)racine.getChild("MaxWidth",nsWMS) != null)
				oldMaxWidth = (Element)racine.getChild("MaxWidth",nsWMS).clone();
			if((Element)racine.getChild("MaxHeight",nsWMS) != null)
				oldMaxHeight = (Element)racine.getChild("MaxHeight",nsWMS).clone();
			racine.removeContent(racine.getChild("Service", nsWMS));
			
			//Create a new Service element
			Element newService  = new Element ("Service", nsWMS);
			newService.addContent((new Element("Name", nsWMS)).setText("WMS"));
			
			if(config == null )
			{
				XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
		        sortie.output(document, new FileOutputStream(filePath));
				return true;
			}
						
			
			if(config.getTitle() != null && config.getTitle().length() != 0)
				newService.addContent((new Element("Title", nsWMS)).setText(config.getTitle()));
			if(config.getAbst() != null && config.getAbst().length() != 0)
				newService.addContent((new Element("Abstract", nsWMS)).setText(config.getAbst()));
			if(config.getKeywordList() != null && config.getKeywordList().size() != 0)
			{
				Element keywords = new Element("KeywordsList", nsWMS);
				Iterator<String> iKeywords = config.getKeywordList().iterator();
				while (iKeywords.hasNext())
				{
					keywords.addContent((new Element("Keyword", nsWMS)).setText(iKeywords.next()));
				}
				newService.addContent(keywords);
			}
			Element onlineResource = new Element("OnlineResource", nsWMS);
			onlineResource.setAttribute("href", href, nsXLINK);
			newService.addContent(onlineResource);
			
			
			if(config.getContactInfo() != null && !config.getContactInfo().isEmpty()){
				Element newContactInformation = new Element("ContactInformation", nsWMS);
				
				ServiceContactInfo contactInfo = config.getContactInfo();
				
				Element newContactPersonPrimary = new Element("ContactPersonPrimary", nsWMS);
				Boolean hasContactPersonPrimary = false;
				if(contactInfo.getName() != null && contactInfo.getName().length() != 0){
					newContactPersonPrimary.addContent((new Element("ContactPerson", nsWMS)).setText(contactInfo.getName()));
					hasContactPersonPrimary = true;
				}
				if(contactInfo.getOrganization() != null && contactInfo.getOrganization().length() != 0){
					newContactPersonPrimary.addContent((new Element("ContactOrganization", nsWMS)).setText(contactInfo.getOrganization()));
					hasContactPersonPrimary = true;
				}
				if(hasContactPersonPrimary)
					newContactInformation.addContent(newContactPersonPrimary);
				
				if (contactInfo.getPosition() != null && contactInfo.getPosition().length() != 0)
					newContactInformation.addContent((new Element("ContactPosition", nsWMS)).setText(contactInfo.getPosition()));
				
				ServiceContactAdressInfo contactAddress = contactInfo.getContactAddress();
				Element newContactAddress = new Element("ContactAddress", nsWMS);
				Boolean hasContactAddress = false;
				if(contactAddress.getType() != null && contactAddress.getType().length() != 0){
					newContactAddress.addContent((new Element("AddressType", nsWMS)).setText(contactAddress.getType()));
					hasContactAddress = true;
				}
				if(contactAddress.getAddress() != null && contactAddress.getAddress().length() != 0){
					newContactAddress.addContent((new Element("Address", nsWMS)).setText(contactAddress.getAddress()));
					hasContactAddress = true;
				}
				if(contactAddress.getCity() != null && contactAddress.getCity().length() != 0){
					newContactAddress.addContent((new Element("City", nsWMS)).setText(contactAddress.getCity()));
					hasContactAddress = true;
				}
				if(contactAddress.getState() != null && contactAddress.getState().length() != 0){
					newContactAddress.addContent((new Element("StateOrProvince", nsWMS)).setText(contactAddress.getState()));
					hasContactAddress = true;
				}
				if(contactAddress.getPostalCode() != null && contactAddress.getPostalCode().length() != 0){
					newContactAddress.addContent((new Element("PostCode", nsWMS)).setText(contactAddress.getPostalCode()));
					hasContactAddress = true;
				}
				if(contactAddress.getCountry() != null && contactAddress.getCountry().length() != 0){
					newContactAddress.addContent((new Element("Country", nsWMS)).setText(contactAddress.getCountry()));
					hasContactAddress = true;
				}
				
				if(hasContactAddress)
					newContactInformation.addContent(newContactAddress);
				
				if (contactInfo.getVoicePhone() != null && contactInfo.getVoicePhone().length() != 0)
					newContactInformation.addContent((new Element("ContactVoiceTelephone", nsWMS)).setText(contactInfo.getVoicePhone()));
				
				if (contactInfo.getFacSimile() != null && contactInfo.getFacSimile().length() != 0)
					newContactInformation.addContent((new Element("ContactFacsimileTelephone", nsWMS)).setText(contactInfo.getFacSimile()));
				
				if (contactInfo.geteMail() != null && contactInfo.geteMail().length() != 0)
					newContactInformation.addContent((new Element("ContactElectronicMailAddress", nsWMS)).setText(contactInfo.geteMail()));
				
				newService.addContent(newContactInformation);
			}
			
				
			if(config.getFees() != null && config.getFees().length() != 0)
				newService.addContent((new Element("Fees", nsWMS)).setText(config.getFees()));
			if(config.getAccessConstraints() != null && config.getAccessConstraints().length() != 0)
				newService.addContent((new Element("AccessConstraints", nsWMS)).setText(config.getAccessConstraints()));
			
			//Add the 3 elements which are not overwrite by the config definition
			if(oldLayerLimit!=null)newService.addContent(oldLayerLimit);
			if(oldMaxWidth!=null)newService.addContent(oldMaxWidth);
			if(oldMaxHeight!=null)newService.addContent(oldMaxHeight);
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
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#writeLatLonBBOX(org.jdom.Element, java.lang.String, java.lang.String, java.lang.String, java.lang.String)
	 */
	@SuppressWarnings("rawtypes")
	@Override
	protected boolean writeLatLonBBOX(Element elementLayer, String wgsMinx, String wgsMiny, String wgsMaxx, String wgsMaxy)
	{
		try
		{
			List llBBOXElements = elementLayer.getChildren("EX_GeographicBoundingBox",nsWMS);
			if(llBBOXElements.size() != 0)
			{
	    		org.jdom.Element llBBOX = (org.jdom.Element) llBBOXElements.get(0);
	    		llBBOX.getChild("westBoundLongitude",nsWMS).setText(wgsMinx);
	    		llBBOX.getChild("eastBoundLongitude",nsWMS).setText(wgsMaxx);
	    		llBBOX.getChild("southBoundLatitude",nsWMS).setText(wgsMiny);
	    		llBBOX.getChild("northBoundLatitude",nsWMS).setText(wgsMaxy);
			}
			else
			{
				//create element
				Element element = new Element("EX_GeographicBoundingBox",nsWMS);
				Element wbl = new Element("westBoundLongitude",nsWMS);
				wbl.setText(wgsMiny);
				Element ebl = new Element("eastBoundLongitude",nsWMS);
				ebl.setText(wgsMaxy);
				Element sbl = new Element("southBoundLatitude",nsWMS);
				sbl.setText(wgsMinx);
				Element nbl = new Element("northBoundLatitude",nsWMS);
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
			setLastException(e);
			return false;
		}
	}
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#getElementBoundingBox(org.jdom.Element)
	 */
	@SuppressWarnings("unchecked")
	@Override
	protected List<Element> getElementBoundingBox (Element elementLayer)
	{
		return elementLayer.getChildren("BoundingBox", nsWMS);
	}
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#createElementBoundingBox()
	 */
	@Override
	protected Element createElementBoundingBox ()
	{
		return new Element("BoundingBox",nsWMS);
	}
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#getAttributeSRS()
	 */
	@Override
	protected String getAttributeSRS ()
	{
			return "CRS";
	}

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#getLayerName(org.jdom.Element)
	 */
	@Override
	protected String getLayerName(Element layer) {
		if(layer.getChild("Name",nsWMS) != null)
			return layer.getChild("Name",nsWMS).getValue();
		else
			return null;
	}
}
