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
package org.easysdi.proxy.wmts.v100;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Vector;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.wmts.*;
import org.easysdi.proxy.jdom.filter.*;
import org.easysdi.xml.documents.*;
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

/**
 * @author Depth SA
 *
 */
public class WMTSProxyResponseBuilder100 extends WMTSProxyResponseBuilder {

    public WMTSProxyResponseBuilder100(WMTSProxyServlet proxyServlet) {
	super(proxyServlet);
	nsWMTS = Namespace.getNamespace("http://www.opengis.net/wmts/1.0");
    }

    /**
     * Filter the operations allowed
     */
    @SuppressWarnings({ "unchecked", "rawtypes" })
    public Boolean CapabilitiesOperationsFiltering (String filePath, String href ){
	servlet.logger.trace("transform - Start - Capabilities operations filtering");
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
		    servlet.logger.trace(ProxyServlet.ServiceOperations.get(i) + " is permitted");
		} else 
		{
		    deniedOperations.add(ProxyServlet.ServiceOperations.get(i));
		    servlet.logger.trace(ProxyServlet.ServiceOperations.get(i) + " is denied");
		}
	    }

	    Document  docParent = sxb.build(new File(filePath));
	    Element racine = docParent.getRootElement();

	    //get the namespaces
	    nsWMTS = racine.getNamespace();
	    List lns = racine.getAdditionalNamespaces();
	    Iterator<Namespace> ilns = lns.iterator();
	    while (ilns.hasNext())
	    {
		Namespace ns = (Namespace)ilns.next();
		if(ns.getPrefix().equalsIgnoreCase("ows"))
		    nsOWS = ns;
		if(ns.getPrefix().equalsIgnoreCase("xlink"))
		    nsXLINK = ns;
	    }


	    //We can not modify Elements while we loop over them with an iterator.
	    //We have to use a separate List storing the Elements we want to modify.

	    //Operation filtering
	    Filter operationFilter = new ElementOperationFilter();
	    Filter xlinkFilter = new AttributeXlinkFilter();
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
			String att = toUpdate.getAttribute("href", nsXLINK).getValue();
			if(att.contains("?"))
			{
			    att = att.replace(att.substring(0, att.indexOf("?")), href);
			}
			else
			{
			    att = href;
			}
			toUpdate.setAttribute("href", att, nsXLINK);
		    }
		}
	    }

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

    /**
     * Filter the contents of the capabilities :
     * - allowed layers
     */
    @SuppressWarnings({ "unchecked", "rawtypes" })
    public Boolean CapabilitiesContentsFiltering (HashMap<String, String> filePathList ){
	servlet.logger.trace("transform - Start - Capabilities contents filtering");
	try
	{
	    SAXBuilder sxb = new SAXBuilder();
	    Iterator<Map.Entry<String, String>> iFile =  filePathList.entrySet().iterator();
	    while (iFile.hasNext())
	    {
		Map.Entry<String, String > fileEntry = iFile.next(); 
		String filePath = fileEntry.getValue();
		Document  docParent = sxb.build(new File(filePath));
		Element racine = docParent.getRootElement();

		//get the namespace
		Namespace localNsWMTS = racine.getNamespace(); 
		Namespace localNsOWS = nsOWS;
		List lns = racine.getAdditionalNamespaces();
		Iterator ilns = lns.iterator();
		while (ilns.hasNext())
		{
		    Namespace ns = (Namespace)ilns.next();
		    if(ns.getPrefix().equalsIgnoreCase("ows"))
			localNsOWS = ns;
		}

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
		Iterator<Element> iLLayer = layerList.iterator();
		while (iLLayer.hasNext())
		{
		    Element layerElement = (Element)iLLayer.next();
		    Element idElement = layerElement.getChild("Identifier", localNsOWS);
		    if (idElement!= null && !servlet.isLayerAllowed(idElement.getText(),servlet.getPhysicalServiceByAlias(fileEntry.getKey()).getUrl()))
		    {
			Parent parent = layerElement.getParent();
			parent.removeContent (layerElement);
		    }
		    else
		    {
			//Rewrite Layer name with alias prefix
			String name = idElement.getText();
			//Keep the prefix before the alias
			if(name.contains(":")){
			    idElement.setText(name.substring(0, name.indexOf(":"))+":"+fileEntry.getKey()+"_"+name.substring(name.indexOf(":",0)+1));
			}else{
			    idElement.setText(fileEntry.getKey()+"_"+name);
			}
			//		    			idElement.setText(fileEntry.getKey()+"_"+name); 

			//Remove the <ResourceURL> element which are not supported by the proxy and not mandatory in the OGC standard
			layerElement.removeChildren("ResourceURL",localNsWMTS);

			List<Element> lElementTileMatrixSetLink = layerElement.getChildren("TileMatrixSetLink", localNsWMTS);
			Iterator<Element> iElementTileMatrixSetLink = lElementTileMatrixSetLink.iterator();
			while (iElementTileMatrixSetLink.hasNext()){
			    Element elementTileMatrixSetLink = iElementTileMatrixSetLink.next();
			    Element elementTileMatrixSetIndentifier = elementTileMatrixSetLink.getChild("TileMatrixSet",localNsWMTS);
			    String tmsLinkIdentifier = elementTileMatrixSetIndentifier.getText();
			    elementTileMatrixSetIndentifier.setText(fileEntry.getKey()+"_"+tmsLinkIdentifier);
			}
		    }
		}

		//Rewrite TileMatrixSet identifier linked with the alias to avoid duplicate TilMatrixSet identifier when aggregate several remote server
		Element elementContents = racine.getChild("Contents", localNsWMTS);
		List<Element> lElementTileMatrixSet = elementContents.getChildren("TileMatrixSet", localNsWMTS);
		Iterator<Element> iElementTileMatrixSet = lElementTileMatrixSet.iterator();
		while (iElementTileMatrixSet.hasNext()){
		    Element elementTileMatrixSet = iElementTileMatrixSet.next();
		    Element elementTileMatrixSetIndentifier = elementTileMatrixSet.getChild("Identifier",localNsOWS);
		    String tmsLinkIdentifier = elementTileMatrixSetIndentifier.getText();
		    elementTileMatrixSetIndentifier.setText(fileEntry.getKey()+"_"+tmsLinkIdentifier);
		}

		XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
		sortie.output(docParent, new FileOutputStream(filePath));
	    }
	    servlet.logger.trace("transform - End - Capabilities contents filtering");
	    return true;
	}
	catch (Exception ex )
	{
	    setLastException(ex);
	    return false;
	}
    }

    /**
     * Merge the capabilities into one single file
     */
    @SuppressWarnings("unchecked")
    public Boolean CapabilitiesMerging(HashMap<String, String> filePathList)
    {
	servlet.logger.trace("transform - Start - Capabilities merging");
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
	    Element contentsMaster=  ((Element)racineMaster.getDescendants(layerFilter).next()).getParentElement();

	    Collection<String> enumFile = filePathList.values();
	    Iterator<String> it = enumFile.iterator();
	    while (it.hasNext())
	    {
		String nfile = it.next();
		if(nfile.equals(fileMasterPath))
		    continue;
		Document documentChild = null;
		documentChild = sxb.build(new File(nfile));
		if (documentChild != null) {
		    Element racineChild = documentChild.getRootElement();
		    Namespace localNsWMTS = racineChild.getNamespace(); 
		    Element contentsChild = (Element)racineChild.getChild("Contents", localNsWMTS);
		    Iterator<Element> ichild = contentsChild.getDescendants(new ElementLayerFilter());
		    while (ichild.hasNext())
		    {
			Element child = (Element)((Element)ichild.next()).clone();
			contentsMaster.addContent(1, child);
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
	    servlet.logger.trace("transform - End - Capabilities merging");
	    return true;
	} catch (Exception ex) {
	    setLastException(ex);
	    return false;
	}
    }

    public Boolean CapabilitiesServiceMetadataWriting(String filePath, String href)
    {
	servlet.logger.trace("transform - Start - Capabilities metadata writing");
	try
	{
	    Config config = servlet.getConfiguration();
	    OWSServiceMetadata serviceMetadata = config.getOwsServiceMetadata();

	    SAXBuilder sxb = new SAXBuilder();
	    Document document = sxb.build(new File(filePath));
	    Element racine = document.getRootElement();
	    racine.removeContent(racine.getChild("ServiceIdentification", nsOWS));
	    racine.removeContent(racine.getChild("ServiceProvider", nsOWS));

	    if(serviceMetadata == null || serviceMetadata.isEmpty())
	    {
		XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
		sortie.output(document, new FileOutputStream(filePath));
		return true;
	    }

	    if(serviceMetadata.getIdentification() != null && !serviceMetadata.getIdentification().isEmpty())
	    {
		Element newServiceIdentification = new Element("ServiceIdentification", nsOWS);
		if(serviceMetadata.getIdentification().getTitle() != null && serviceMetadata.getIdentification().getTitle().length() != 0)
		    newServiceIdentification.addContent((new Element("Title", nsOWS)).setText(serviceMetadata.getIdentification().getTitle()));
		if(serviceMetadata.getIdentification().getAbst() != null && serviceMetadata.getIdentification().getAbst().length() != 0)
		    newServiceIdentification.addContent((new Element("Abstract", nsOWS)).setText(serviceMetadata.getIdentification().getAbst()));
		if(serviceMetadata.getIdentification().getKeywords() != null && serviceMetadata.getIdentification().getKeywords().size() != 0)
		{
		    Element keywords = new Element("Keywords", nsOWS);
		    Iterator<String> iKeywords = serviceMetadata.getIdentification().getKeywords().iterator();
		    while (iKeywords.hasNext())
		    {
			keywords.addContent((new Element("Keyword", nsOWS)).setText(iKeywords.next()));
		    }
		    newServiceIdentification.addContent(keywords);
		}
		newServiceIdentification.addContent((new Element("ServiceType", nsOWS)).setText("OGC WMTS"));
		newServiceIdentification.addContent((new Element("ServiceTypeVersion", nsOWS)).setText("1.0.0"));
		if(serviceMetadata.getIdentification().getFees() != null && serviceMetadata.getIdentification().getFees().length() != 0)
		    newServiceIdentification.addContent((new Element("Fees", nsOWS)).setText(serviceMetadata.getIdentification().getFees()));
		if(serviceMetadata.getIdentification().getAccessConstraints() != null && serviceMetadata.getIdentification().getAccessConstraints().length() != 0)
		    newServiceIdentification.addContent((new Element("AccessConstraints", nsOWS)).setText(serviceMetadata.getIdentification().getAccessConstraints()));

		racine.addContent( 0, newServiceIdentification);
	    }
	    OWSServiceProvider serviceProvider = serviceMetadata.getProvider();
	    if(serviceProvider != null && !serviceProvider.isEmpty())
	    {	
		Element newServiceProvider = new Element("ServiceProvider", nsOWS);
		if(serviceProvider.getName() != null )
		    newServiceProvider.addContent((new Element("ProviderName", nsOWS)).setText(serviceProvider.getName()));
		if(serviceProvider.getLinkage() != null )
		{
		    Element site = new Element("ProviderSite", nsOWS);
		    site.setAttribute("href", serviceProvider.getLinkage(),nsXLINK);
		    newServiceProvider.addContent(site);
		}

		OWSResponsibleParty responsibleParty = serviceProvider.getResponsibleParty();
		if(responsibleParty != null && !responsibleParty.isEmpty())
		{
		    Element newServiceContact = new Element("ServiceContact", nsOWS);
		    if(responsibleParty.getName() != null)
			newServiceContact.addContent((new Element("IndividualName", nsOWS)).setText(responsibleParty.getName()));
		    if(responsibleParty.getPosition() != null)
			newServiceContact.addContent((new Element("PositionName", nsOWS)).setText(responsibleParty.getPosition()));
		    if(responsibleParty.getRole() != null)
			newServiceContact.addContent((new Element("Role", nsOWS)).setText(responsibleParty.getRole()));

		    OWSContact contact  = responsibleParty.getContactInfo();
		    if(contact != null && !contact.isEmpty())
		    {
			Element newContactInfo = new Element("ContactInfo", nsOWS);
			OWSTelephone phone = contact.getContactPhone();
			if(phone != null && !phone.isEmpty())
			{
			    Element newPhone = new Element("Phone", nsOWS);
			    if(phone.getVoicePhone() != null)
				newPhone.addContent((new Element("Voice", nsOWS)).setText(phone.getVoicePhone()));
			    if(phone.getFacSimile() != null)
				newPhone.addContent((new Element("Facsimile", nsOWS)).setText(phone.getFacSimile()));
			    newContactInfo.addContent(newPhone);
			}
			OWSAddress address = contact.getAdress();
			if(address != null && !address.isEmpty())
			{
			    Element newAddress = new Element("Address", nsOWS);
			    if (address.getDelivryPoint() != null)
			    {
				newAddress.addContent((new Element ("DelivryPoint", nsOWS)).setText(address.getDelivryPoint()));
			    }
			    if (address.getCity() != null)
			    {
				newAddress.addContent((new Element ("City", nsOWS)).setText(address.getCity()));
			    }
			    if (address.getArea() != null)
			    {
				newAddress.addContent((new Element ("AdministrativeArea", nsOWS)).setText(address.getArea()));
			    }
			    if (address.getPostalCode() != null)
			    {
				newAddress.addContent((new Element ("PostalCode", nsOWS)).setText(address.getPostalCode()));
			    }
			    if (address.getCountry() != null)
			    {
				newAddress.addContent((new Element ("Country", nsOWS)).setText(address.getCountry()));
			    }
			    if (address.getElectronicMail() != null)
			    {
				newAddress.addContent((new Element ("ElectronicMailAddress", nsOWS)).setText(address.getElectronicMail()));
			    }
			    newContactInfo.addContent(newAddress);
			}
			newServiceContact.addContent(newContactInfo);
		    }
		    newServiceProvider.addContent(newServiceContact);
		}
		racine.addContent( 1, newServiceProvider);
	    }

	    Element serviceMetadataUrl = racine.getChild("ServiceMetadataURL", nsWMTS);
	    if(serviceMetadataUrl != null)
	    {
		String metadataUrl = serviceMetadataUrl.getAttributeValue("href", nsXLINK);
		if(metadataUrl.contains("?"))
		{
		    metadataUrl = metadataUrl.replace(metadataUrl.substring(0, metadataUrl.indexOf("?")), href);
		}
		else if (metadataUrl.contains("/1.0.0/WMTSCapabilities.xml"))
		{
		    metadataUrl = metadataUrl.replace(metadataUrl.substring(0, metadataUrl.indexOf("/1.0.0")), href);
		}
		serviceMetadataUrl.setAttribute("href", metadataUrl, nsXLINK);
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

		    //Get the Exception elements of the parent document
		    List<Element> exceptionList = new ArrayList<Element>();
		    Filter exceptionFilter = new ElementExceptionFilter();
		    Iterator<Element> iE = racineParent.getDescendants(exceptionFilter);
		    while (iE.hasNext()){
			Element exception = (Element)iE.next();
			exceptionList.add(exception);
		    }

		    //Add the server alias in the exception text
		    Iterator<Element> iEL = exceptionList.iterator();
		    List<Element> exceptionTextToUpdate = new ArrayList<Element>();
		    List<Element> exceptionToComplete = new ArrayList<Element>();
		    while (iEL.hasNext()){
			Element exception = (Element)iEL.next();
			Iterator<Element> iET = exception.getDescendants(new ElementExceptionTextFilter());
			boolean found = false;
			while (iET.hasNext()){
			    exceptionTextToUpdate.add((Element)iET.next());
			    //			    Element exceptionText = (Element)iET.next();
			    //			    exceptionText.setText( String.format(TEXT_SERVER_ALIAS, key) + exceptionText.getText());
			    found = true;
			}
			if(!found){
			    //Element <Exception> does not have child element <ExceptionText>
			    //One is created to indicate the remote server alias
			    exceptionToComplete.add(exception);
			    //			    Element exceptionText = new Element("ExceptionText");
			    //			    exceptionText.setText(String.format(TEXT_SERVER_ALIAS, key) + "[no text].");
			    //			    exception.addContent(exceptionText);
			}
		    }
		    Iterator<Element> iToUpdate = exceptionTextToUpdate.iterator();
		    while (iToUpdate.hasNext()){
			Element exceptionText = (Element)iToUpdate.next();
			exceptionText.setText( String.format(TEXT_SERVER_ALIAS, key) + exceptionText.getText());
		    }
		    Iterator<Element> iToComplete = exceptionToComplete.iterator();
		    while (iToComplete.hasNext()){
			Element exception = (Element)iToComplete.next();
			Element exceptionText = new Element("ExceptionText");
			exceptionText.setText( String.format(TEXT_SERVER_ALIAS, key) + exceptionText.getText());
			exception.addContent(exceptionText);
		    }
		    continue;
		}

		//Child document
		Document docChild = sxb.build(new File(path));
		Element racine = docChild.getRootElement();
		//Get the Exception elements of the child document
		List<Element> exceptionList = new ArrayList<Element>();
		Filter exceptionFilter = new ElementExceptionFilter();
		Iterator<Element> iE = racine.getDescendants(exceptionFilter);
		while (iE.hasNext()){
		    Element exception = (Element)iE.next();
		    exceptionList.add(exception);
		}

		//Add the server alias in the exception text
		Iterator<Element> iEL = exceptionList.iterator();
		List<Element> exceptionTextToUpdate = new ArrayList<Element>();
		List<Element> exceptionToComplete = new ArrayList<Element>();
		while (iEL.hasNext()){
		    Element exception = (Element)iEL.next();
		    Iterator<Element> iET = exception.getDescendants(new ElementExceptionTextFilter());
		    boolean found = false;
		    while (iET.hasNext()){
			exceptionTextToUpdate.add((Element)iET.next());
			//			    Element exceptionText = (Element)iET.next();
			//			    exceptionText.setText( String.format(TEXT_SERVER_ALIAS, key) + exceptionText.getText());
			found = true;
		    }
		    if(!found){
			//Element <Exception> does not have child element <ExceptionText>
			//One is created to indicate the remote server alias
			exceptionToComplete.add(exception);
			//			    Element exceptionText = new Element("ExceptionText");
			//			    exceptionText.setText(String.format(TEXT_SERVER_ALIAS, key) + "[no text].");
			//			    exception.addContent(exceptionText);
		    }
		    if(exception.getParent().removeContent(exception)){
			racineParent.addContent(exception);
		    }else{
			servlet.logger.error("WMSProxyResponseBuilder.ExceptionAggregation can not correctly rewrite exception.");
		    }
		}	
		Iterator<Element> iToUpdate = exceptionTextToUpdate.iterator();
		while (iToUpdate.hasNext()){
		    Element exceptionText = (Element)iToUpdate.next();
		    exceptionText.setText( String.format(TEXT_SERVER_ALIAS, key) + exceptionText.getText());
		}
		Iterator<Element> iToComplete = exceptionToComplete.iterator();
		    while (iToComplete.hasNext()){
			Element exception = (Element)iToComplete.next();
			Element exceptionText = new Element("ExceptionText");
			exceptionText.setText( String.format(TEXT_SERVER_ALIAS, key) + exceptionText.getText());
			exception.addContent(exceptionText);
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

    @Override
    public Boolean CapabilitiesContentsFiltering(HashMap<String, String> filePathList,String href) {
	return null;
    }


    @Override
    public Boolean CapabilitiesContentsFiltering(Hashtable<String, String> filePathList) throws NoSuchAuthorityCodeException {
	return null;
    }

}
