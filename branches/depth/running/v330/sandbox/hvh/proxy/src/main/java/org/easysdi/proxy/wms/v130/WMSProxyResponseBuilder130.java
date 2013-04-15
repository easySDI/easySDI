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
			if(servlet.getVirtualService().isReflectedmetadata())
			{
				XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
		        sortie.output(document, new FileOutputStream(filePath));
				return true;
			}
						
			SdiVirtualmetadata metadata = servlet.getVirtualService().getSdiVirtualmetadatas().iterator().next();
			if(!metadata.isInheritedtitle())
				newService.addContent((new Element("Title", nsWMS)).setText(metadata.getTitle()));
			if(!metadata.isInheritedsummary())
				newService.addContent((new Element("Abstract", nsWMS)).setText(metadata.getSummary()));
			if(!metadata.isInheritedkeyword())
			{
				Element keywords = new Element("KeywordsList", nsWMS);
				String skeywords = metadata.getKeyword();
				if(skeywords != null)
				{
					String[] words = skeywords.split(",");
					for (int n = 0; n < words.length; n++) {
						keywords.addContent((new Element("Keyword", nsWMS)).setText(words[n] ));
					}
					newService.addContent(keywords);
				}
			}
			Element onlineResource = new Element("OnlineResource", nsWMS);
			onlineResource.setAttribute("type", "simple",nsXLINK);
			onlineResource.setAttribute("href", href, nsXLINK);
			newService.addContent(onlineResource);
			
			if(!metadata.isInheritedcontact()){
				Element newContactInformation = new Element("ContactInformation", nsWMS);
				Element newContactPersonPrimary = new Element("ContactPersonPrimary", nsWMS);
				
				if(metadata.getContactname() != null)
					newContactPersonPrimary.addContent((new Element("ContactPerson", nsWMS)).setText(metadata.getContactname()));
				if(metadata.getContactorganization() != null)
					newContactPersonPrimary.addContent((new Element("ContactOrganization", nsWMS)).setText(metadata.getContactorganization()));
				
				newContactInformation.addContent(newContactPersonPrimary);
				
				if(metadata.getContactposition() != null)
					newContactInformation.addContent((new Element("ContactPosition", nsWMS)).setText(metadata.getContactposition()));
				
				Element newContactAddress = new Element("ContactAddress", nsWMS);
				//TODO add address type
//				newContactAddress.addContent((new Element("AddressType", nsWMS)).setText(metadata.getContactadress()));
				if(metadata.getContactadress() != null)
					newContactAddress.addContent((new Element("Address", nsWMS)).setText(metadata.getContactadress()));
				if(metadata.getContactlocality() != null)
					newContactAddress.addContent((new Element("City", nsWMS)).setText(metadata.getContactlocality()));
				if(metadata.getContactstate() != null)
					newContactAddress.addContent((new Element("StateOrProvince", nsWMS)).setText(metadata.getContactstate()));
				if(metadata.getContactpostalcode() != null)
					newContactAddress.addContent((new Element("PostCode", nsWMS)).setText(metadata.getContactpostalcode()));
				if(metadata.getSdiSysCountry() != null)
					newContactAddress.addContent((new Element("Country", nsWMS)).setText(metadata.getSdiSysCountry().getName()));
				newContactInformation.addContent(newContactAddress);
				
				if(metadata.getContactphone() != null)
					newContactInformation.addContent((new Element("ContactVoiceTelephone", nsWMS)).setText(metadata.getContactphone()));
				
				if(metadata.getContactfax() != null)
					newContactInformation.addContent((new Element("ContactFacsimileTelephone", nsWMS)).setText(metadata.getContactfax()));
				
				if(metadata.getContactemail() != null)
					newContactInformation.addContent((new Element("ContactElectronicMailAddress", nsWMS)).setText(metadata.getContactemail()));
				
				newService.addContent(newContactInformation);
			}
			
				
			if(metadata.getFee() != null)
				newService.addContent((new Element("Fees", nsWMS)).setText(metadata.getFee()));
			if(metadata.getAccessconstraint() != null)
				newService.addContent((new Element("AccessConstraints", nsWMS)).setText(metadata.getAccessconstraint()));
			
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
