package org.easysdi.proxy.wms.thread;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map.Entry;
import java.util.TreeMap;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.jdom.filter.ElementNamedLayerFilter;
import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.core.ProxyRemoteServerResponse;
import org.easysdi.proxy.wms.WMSProxyServlet;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.jdom.*;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;

public class WMSProxyLayerThread extends Thread {

	WMSProxyServlet servlet;
	String paramUrlBase;
	TreeMap<Integer, ProxyLayer> layers;
	TreeMap<Integer, String> styles;
	RemoteServerInfo remoteServer;
	HttpServletResponse resp;

	public WMSProxyLayerThread(	WMSProxyServlet servlet, 
								String paramUrlBase,
								TreeMap<Integer, ProxyLayer> layers,
								TreeMap<Integer, String> styles,
								RemoteServerInfo remoteServer, 
								HttpServletResponse resp) {
		this.servlet = servlet;
		this.paramUrlBase = paramUrlBase;
		this.layers = layers;
		this.styles = styles;
		this.remoteServer = remoteServer;
		this.resp = resp;
	}

	public void run() {
		try {
			servlet.logger.trace( "Thread Layers group: " + layers.values().toString() + " work begin on server " + remoteServer.getUrl());

			String requestToSend;
			if(servlet.getProxyRequest().getRequest().getMethod().equalsIgnoreCase("POST"))
				requestToSend = getRequestPOST();
			else
				requestToSend = getRequestGET();
			
			String filePath = servlet.sendData(servlet.getProxyRequest().getRequest().getMethod(), remoteServer.getUrl(), requestToSend);
			
			ProxyRemoteServerResponse response = new ProxyRemoteServerResponse(remoteServer.getAlias(), filePath);
			
			synchronized (servlet.wmsGetMapResponseFilePathMap) {
				servlet.wmsGetMapResponseFilePathMap.put(layers.firstKey(),response);
			}
			servlet.logger.trace("Thread Layers group: " + layers.values().toString() + " work finished on server " + remoteServer.getUrl());
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			servlet.logger.error( "Server " + remoteServer.getUrl() + " - Layers group Thread " + layers.values().toString() + " :" + e.getMessage());
		}
	}
	
	private String getRequestPOST () throws JDOMException, IOException{
		Iterator<Entry<Integer, ProxyLayer>> itPL = layers.entrySet().iterator();
		SAXBuilder sxb = new SAXBuilder();
		Document document;
		
		document = sxb.build(new ByteArrayInputStream(paramUrlBase.getBytes()));
		
		List<Element> toRemove = new ArrayList<Element>();
		List<Element> toRewrite = new ArrayList<Element>();
		Boolean toKeep = false;
		
		Namespace nsSLD =  Namespace.getNamespace("sld","http://www.opengis.net/sld");
		Namespace nsSE =  Namespace.getNamespace("se","http://www.opengis.net/se");
		
		Element racine = document.getRootElement();
		Iterator ilNamedLayer = racine.getDescendants(new ElementNamedLayerFilter());
		
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
			
			Iterator<Element> iToRemove = toRemove.iterator();
			while(iToRemove.hasNext()){
				Element element = iToRemove.next();
				racine.removeContent(element);
			}
			
			Iterator<Element> iToRewrite = toRewrite.iterator();
			while(iToRewrite.hasNext()){
				Element element = iToRewrite.next();
				ProxyLayer proxyLayer = new ProxyLayer(element.getText());
				element.setText(proxyLayer.getPrefixedName());
			}
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
		
		//Set TRANSPARENT to TRUE if not present
		if (paramUrlBase.toUpperCase().indexOf("TRANSPARENT=") == -1)
			paramUrlBase += "TRANSPARENT=TRUE&";
		
		return paramUrlBase + layersUrl + stylesUrl;
	}
}