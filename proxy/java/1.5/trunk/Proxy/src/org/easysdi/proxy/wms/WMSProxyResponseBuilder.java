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
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;

import org.easysdi.jdom.filter.ElementServiceExceptionFilter;
import org.easysdi.jdom.filter.ElementServiceExceptionReportFilter;
import org.easysdi.proxy.core.ProxyResponseBuilder;
import org.easysdi.proxy.core.ProxyServlet;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.Namespace;
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
	}

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
