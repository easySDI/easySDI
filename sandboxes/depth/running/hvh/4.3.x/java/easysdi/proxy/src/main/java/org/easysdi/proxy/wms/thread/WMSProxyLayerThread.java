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
package org.easysdi.proxy.wms.thread;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;
import java.util.Map.Entry;
import java.util.TreeMap;
import java.util.logging.Level;
import java.util.logging.Logger;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.core.ProxyRemoteServerResponse;
import org.easysdi.proxy.domain.SdiPhysicalservice;
import org.easysdi.proxy.jdom.filter.ElementFilter;
import org.easysdi.proxy.wms.WMSProxyServlet;
import org.easysdi.proxy.wms.WMSProxyServletRequest;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.json.JSONObject;

/**
 * @author DEPTH SA
 *
 */
public class WMSProxyLayerThread extends Thread {

    WMSProxyServlet servlet;
    String paramUrlBase;
    TreeMap<Integer, ProxyLayer> layers;
    TreeMap<Integer, String> styles;
    SdiPhysicalservice physicalService;
    HttpServletResponse resp;

    public WMSProxyLayerThread(	WMSProxyServlet servlet, 
	    String paramUrlBase,
	    TreeMap<Integer, ProxyLayer> layers,
	    TreeMap<Integer, String> styles,
	    SdiPhysicalservice physicalService, 
	    HttpServletResponse resp) {
	this.servlet = servlet;
	this.paramUrlBase = paramUrlBase;
	this.layers = layers;
	this.styles = styles;
	this.physicalService = physicalService;
	this.resp = resp;
    }

    public void run() {
	try {
	    servlet.logger.trace( "Thread Layers group: " + layers.values().toString() + " work begin on server " + physicalService.getResourceurl());

	    String requestToSend;
	    if(servlet.getProxyRequest().getRequest().getMethod().equalsIgnoreCase("POST"))
		requestToSend = getRequestPOST();
	    else
		requestToSend = getRequestGET();

	    String filePath = servlet.sendData(servlet.getProxyRequest().getRequest().getMethod(), physicalService.getResourceurl(), requestToSend);

	    ProxyRemoteServerResponse response = new ProxyRemoteServerResponse(physicalService.getAlias(), filePath);

	    synchronized (servlet.wmsGetMapResponseFilePathMap) {
		servlet.wmsGetMapResponseFilePathMap.put(layers.firstKey(),response);
	    }
	    servlet.logger.trace("Thread Layers group: " + layers.values().toString() + " work finished on server " + physicalService.getResourceurl());
	} catch (Exception e) {
	    resp.setHeader("easysdi-proxy-error-occured", "true");
	    servlet.logger.error( "Server " + physicalService.getResourceurl() + " - Layers group Thread " + layers.values().toString() + " :" + e.getMessage());
	}
    }

    @SuppressWarnings("unchecked")
    private String getRequestPOST () throws JDOMException, IOException{
	SAXBuilder sxb = new SAXBuilder();
	Document document;

	document = sxb.build(new ByteArrayInputStream(paramUrlBase.getBytes()));

	List<Element> toRemove = new ArrayList<Element>();
	List<Element> toRewrite = new ArrayList<Element>();
	Boolean toKeep = false;

	//		Namespace nsSLD =  Namespace.getNamespace("sld","http://www.opengis.net/sld");
	//		Namespace nsSE =  Namespace.getNamespace("se","http://www.opengis.net/se");

	Element racine = document.getRootElement();
	Iterator<?> ilNamedLayer = racine.getDescendants(new ElementFilter("NamedLayer"));

	while(ilNamedLayer.hasNext()){
	    Element namedLayer = (Element)ilNamedLayer.next();
	    List<Element> lName = namedLayer.getChildren();
	    Iterator<Element> ilName = lName.iterator();
	    while (ilName.hasNext()){
		Element elementName = ilName.next();
		if(elementName.getName().equals("Name")){
		    Iterator<Entry<Integer, ProxyLayer>> itLK = layers.entrySet().iterator();
		    while(itLK.hasNext()){
			Entry<Integer, ProxyLayer> layerOrdered = itLK.next();
			if(((ProxyLayer)layerOrdered.getValue()).getAliasName().equals(elementName.getText())){
			    //Keep the layer in the request
			    toRewrite.add(elementName);
			    toKeep = true;
			    break;
			}
		    }
		    if(!toKeep){
			toRemove.add(namedLayer);
		    }
		    toKeep = false;
		}
	    }

	    Iterator<Element> iToRewrite = toRewrite.iterator();
	    while(iToRewrite.hasNext()){
		Element element = iToRewrite.next();
		ProxyLayer proxyLayer = new ProxyLayer(element.getText());
		element.setText(proxyLayer.getPrefixedName());
	    }

	    //toRemove.clear();
	    toRewrite.clear();
	}
	Iterator<Element> iToRemove = toRemove.iterator();
	while(iToRemove.hasNext()){
	    Element element = iToRemove.next();
	    element.getParentElement().removeContent(element);
	    //racine.removeContent(element);
	}

	XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	return sortie.outputString(document);
    }

    private String getRequestGET () {
	Iterator<Entry<Integer, ProxyLayer>> itPL = layers.entrySet().iterator();
	String layerList ="";
	String styleList ="";
	while(itPL.hasNext()){
	    Entry<Integer, ProxyLayer> layer = itPL.next();
	    layerList += layer.getValue().getPrefixedName() +",";
	    styleList += styles.get(layer.getKey()) +",";
	}

	String layersUrl = "&LAYERS=" + layerList.substring(0, layerList.length()-1);
	String stylesUrl = "&STYLES=" + styleList.substring(0, styleList.length()-1);
        
        //Handle Esri vendor specific parameter layerDefs
        JSONObject layerdefs = ((WMSProxyServletRequest) servlet.getProxyRequest()).getLayerdefs();
        if(layerdefs != null){
             paramUrlBase += "&layerDefs=";
            try {
                paramUrlBase += URLEncoder.encode(layerdefs.toString(), "UTF-8");
            } catch (UnsupportedEncodingException ex) {
                Logger.getLogger(WMSProxyLayerThread.class.getName()).log(Level.SEVERE, null, ex);
            }
             paramUrlBase += "&";
        }

	//Set TRANSPARENT to TRUE if not present
	if (paramUrlBase.toUpperCase().indexOf("TRANSPARENT=") == -1)
	    paramUrlBase += "TRANSPARENT=TRUE&";

	return paramUrlBase + layersUrl + stylesUrl;
    }
}