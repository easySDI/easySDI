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
import java.util.HashMap;
import java.util.List;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.domain.SdiVirtualmetadata;
import org.easysdi.proxy.wms.WMSProxyResponseBuilder;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.Namespace;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.opengis.referencing.NoSuchAuthorityCodeException;

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
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#CapabilitiesServiceMetadataWriting(java.lang.String, java.lang.String)
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
				Element service = racine.getChild("service",nsWMS);
				service.removeContent(service.getChild("OnlineResource",nsWMS));
				Element onlineResource = new Element("OnlineResource",nsWMS);
				onlineResource.setAttribute("type", "simple",nsXLINK);
				onlineResource.setAttribute("href", href, nsXLINK);
				service.addContent(onlineResource);
				XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
		        sortie.output(document, new FileOutputStream(filePath));
				return true;
			}
			
			//Clone service element to keep value which are inherited 
			Element racine = document.getRootElement();
			Element oldService = (Element)racine.getChild("Service", nsWMS).clone();
			
			//Clone elements which are not yet proposed to be overwrite
			Element oldLayerLimit=null;
			Element oldMaxWidth=null;
			Element oldMaxHeight=null;
			if((Element)racine.getChild("LayerLimit",nsWMS) != null)
				oldLayerLimit = (Element)racine.getChild("LayerLimit",nsWMS).clone();
			if((Element)racine.getChild("MaxWidth",nsWMS) != null)
				oldMaxWidth = (Element)racine.getChild("MaxWidth",nsWMS).clone();
			if((Element)racine.getChild("MaxHeight",nsWMS) != null)
				oldMaxHeight = (Element)racine.getChild("MaxHeight",nsWMS).clone();
			
			//Remove the current Service element
			racine.removeContent(racine.getChild("Service", nsWMS));
			
			//Create a new Service element
			Element newService  = new Element ("Service", nsWMS);
			newService.addContent((new Element("Name", nsWMS)).setText("WMS"));
			
			//Metadata overwrite in the virtal service configuration			
			SdiVirtualmetadata virtualMetadata = servlet.getVirtualService().getSdiVirtualmetadatas().iterator().next();
			
			if(!virtualMetadata.isInheritedtitle() && virtualMetadata.getTitle() != null && virtualMetadata.getTitle().length() != 0)
				newService.addContent((new Element("Title", nsWMS)).setText(virtualMetadata.getTitle()));
			else if(virtualMetadata.isInheritedtitle() && oldService.getChildText("Title", nsWMS) != null)
				newService.addContent((new Element("Title", nsWMS)).setText(oldService.getChildText("Title", nsWMS)));
			
			if(!virtualMetadata.isInheritedsummary() && virtualMetadata.getSummary() != null && virtualMetadata.getSummary().length() != 0)
				newService.addContent((new Element("Abstract", nsWMS)).setText(virtualMetadata.getSummary()));
			else if (virtualMetadata.isInheritedsummary() && oldService.getChildText("Abstract", nsWMS) != null)
				newService.addContent((new Element("Abstract", nsWMS)).setText(oldService.getChildText("Abstract", nsWMS)));
			
			if(!virtualMetadata.isInheritedkeyword() && virtualMetadata.getKeyword() != null && virtualMetadata.getKeyword().length() != 0)
			{
				Element keywords = new Element("KeywordsList", nsWMS);
				String skeywords = virtualMetadata.getKeyword();
				if(skeywords != null)
				{
					String[] words = skeywords.split(",");
					for (int n = 0; n < words.length; n++) {
						keywords.addContent((new Element("Keyword", nsWMS)).setText(words[n] ));
					}
					newService.addContent(keywords);
				}
			}
			else if (virtualMetadata.isInheritedkeyword() && oldService.getChild("KeywordsList") != null)
				newService.addContent((new Element("KeywordsList", nsWMS)).setContent(oldService.getChild("KeywordsList")));
			
			Element onlineResource = new Element("OnlineResource", nsWMS);
			onlineResource.setAttribute("type", "simple",nsXLINK);
			onlineResource.setAttribute("href", href, nsXLINK);
			newService.addContent(onlineResource);
			
			if(!virtualMetadata.isInheritedcontact()){
				Element newContactInformation = new Element("ContactInformation", nsWMS);
				Element newContactPersonPrimary = new Element("ContactPersonPrimary", nsWMS);
				
				boolean hasPersonPrimary = false;
				if(virtualMetadata.getContactname() != null && virtualMetadata.getContactname().length() != 0)
				{
					newContactPersonPrimary.addContent((new Element("ContactPerson", nsWMS)).setText(virtualMetadata.getContactname()));
					hasPersonPrimary = true;
				}
				if(virtualMetadata.getContactorganization() != null  && virtualMetadata.getContactorganization().length() != 0)
				{
					newContactPersonPrimary.addContent((new Element("ContactOrganization", nsWMS)).setText(virtualMetadata.getContactorganization()));
					hasPersonPrimary = true;
				}
				if(hasPersonPrimary)
					newContactInformation.addContent(newContactPersonPrimary);
				
				if(virtualMetadata.getContactposition() != null && virtualMetadata.getContactposition().length() != 0)
					newContactInformation.addContent((new Element("ContactPosition", nsWMS)).setText(virtualMetadata.getContactposition()));
				
				Element newContactAddress = new Element("ContactAddress", nsWMS);
				boolean hasContactAddress = false;
				//TODO add address type
//				newContactAddress.addContent((new Element("AddressType", nsWMS)).setText(metadata.getContactadress()));
				if(virtualMetadata.getContactadress() != null && virtualMetadata.getContactadress().length() != 0)
				{
					newContactAddress.addContent((new Element("Address", nsWMS)).setText(virtualMetadata.getContactadress()));
					hasContactAddress = true;
				}
				if(virtualMetadata.getContactlocality() != null && virtualMetadata.getContactlocality().length() != 0)
				{
					newContactAddress.addContent((new Element("City", nsWMS)).setText(virtualMetadata.getContactlocality()));
					hasContactAddress = true;
				}
				if(virtualMetadata.getContactstate() != null && virtualMetadata.getContactstate().length() != 0)
				{
					newContactAddress.addContent((new Element("StateOrProvince", nsWMS)).setText(virtualMetadata.getContactstate()));
					hasContactAddress = true;
				}
				if(virtualMetadata.getContactpostalcode() != null  && virtualMetadata.getContactpostalcode().length() != 0)
				{
					newContactAddress.addContent((new Element("PostCode", nsWMS)).setText(virtualMetadata.getContactpostalcode()));
					hasContactAddress = true;
				}
				if(virtualMetadata.getSdiSysCountry() != null && virtualMetadata.getSdiSysCountry().getName() != null)
				{
					newContactAddress.addContent((new Element("Country", nsWMS)).setText(virtualMetadata.getSdiSysCountry().getName()));
					hasContactAddress = true;
				}
				
				if(hasContactAddress)
					newContactInformation.addContent(newContactAddress);
				
				if(virtualMetadata.getContactphone() != null && virtualMetadata.getContactphone().length() != 0)
					newContactInformation.addContent((new Element("ContactVoiceTelephone", nsWMS)).setText(virtualMetadata.getContactphone()));
				
				if(virtualMetadata.getContactfax() != null && virtualMetadata.getContactfax().length() != 0)
					newContactInformation.addContent((new Element("ContactFacsimileTelephone", nsWMS)).setText(virtualMetadata.getContactfax()));
				
				if(virtualMetadata.getContactemail() != null && virtualMetadata.getContactemail().length() != 0)
					newContactInformation.addContent((new Element("ContactElectronicMailAddress", nsWMS)).setText(virtualMetadata.getContactemail()));
				
				newService.addContent(newContactInformation);
			}
			else  if (!virtualMetadata.isInheritedcontact() && oldService.getChild("ContactInformation", nsWMS) != null )
				newService.addContent((new Element("ContactInformation", nsWMS)).setContent(oldService.getChild("ContactInformation", nsWMS)));

			if(!virtualMetadata.isInheritedfee() && virtualMetadata.getFee() != null && virtualMetadata.getFee().length() != 0)
				newService.addContent((new Element("Fees", nsWMS)).setText(virtualMetadata.getFee()));
			else if (virtualMetadata.isInheritedfee() && oldService.getChildText("Fees", nsWMS) != null )
				newService.addContent((new Element("Fees", nsWMS)).setText(oldService.getChildText("Fees", nsWMS)));
			
			if(!virtualMetadata.isInheritedaccessconstraint() && virtualMetadata.getAccessconstraint() != null && virtualMetadata.getAccessconstraint().length() != 0)
				newService.addContent((new Element("AccessConstraints", nsWMS)).setText(virtualMetadata.getAccessconstraint()));
			else if (virtualMetadata.isInheritedaccessconstraint() && oldService.getChildText("AccessConstraints", nsWMS) != null )
				newService.addContent((new Element("AccessConstraints", nsWMS)).setText(oldService.getChildText("AccessConstraints", nsWMS)));
			
			//Add the 3 elements which are not overwrite by the virtual service definition
			if(oldLayerLimit!=null)newService.addContent(oldLayerLimit);
			if(oldMaxWidth!=null)newService.addContent(oldMaxWidth);
			if(oldMaxHeight!=null)newService.addContent(oldMaxHeight);
			
			racine.addContent( 1, newService);
						
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
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#getChildElementName(org.jdom.Element)
	 */
	@Override
	protected Element getChildElementName (Element parent){
		return parent.getChild("Name", nsWMS);
	}
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#getChildElementCapability(org.jdom.Element)
	 */
	@Override
	protected Element getChildElementCapability (Element parent){
		return parent.getChild("Capability", nsWMS);
	}
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#getChildrenElementsLayer(org.jdom.Element)
	 */
	@Override
	@SuppressWarnings("unchecked")
	protected List<Element> getChildrenElementsLayer (Element parent){
		return parent.getChildren("Layer", nsWMS);
	}
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#getChildElementRequest(org.jdom.Element)
	 */
	@Override
	protected Element getChildElementRequest (Element parent){
		return parent.getChild("Request", nsWMS);
	}
	
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#getChildElementException(org.jdom.Element)
	 */
	@Override
	protected Element getChildElementException (Element parent){
		return parent.getChild("Exception", nsWMS);
	}
	
	/* (non-Javadoc)
	 * @see org.easysdi.proxy.wms.WMSProxyResponseBuilder#getNewElementFormat()
	 */
	@Override
	protected Element getNewElementFormat (){
		return new Element("Format", nsWMS);
	}

	@Override
	public Boolean CapabilitiesContentsFiltering(HashMap<String, String> filePathList) throws NoSuchAuthorityCodeException {
	    return null;
	}

	
}
