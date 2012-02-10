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
import java.util.Iterator;
import java.util.List;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.wms.WMSProxyResponseBuilder;
import org.easysdi.xml.documents.Config;
import org.easysdi.xml.documents.ServiceContactAdressInfo;
import org.easysdi.xml.documents.ServiceContactInfo;
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
			onlineResource.setAttribute("type", "simple",nsXLINK);
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
