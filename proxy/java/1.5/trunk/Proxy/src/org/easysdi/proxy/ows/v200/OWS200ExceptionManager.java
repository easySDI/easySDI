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
package org.easysdi.proxy.ows.v200;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.IOException;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;

import javax.xml.parsers.ParserConfigurationException;

import org.easysdi.jdom.filter.ElementExceptionReportFilter;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.ows.OWSExceptionManager;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.xml.sax.SAXException;

/**
 * @author DEPTH SA
 *
 */
public class OWS200ExceptionManager implements OWSExceptionManager {
	
	/**
	 * Get the exception files return by the remote servers.
	 * @param remoteServerResponseFile
	 * @return
	 */
	public HashMap<String, String> getRemoteServerExceptionResponse (ProxyServlet servlet,HashMap<String, String> remoteServerResponseFile)
	{
		HashMap<String, String> toRemove = new HashMap<String, String>();
		HashMap<String, String> remoteServerExceptionFiles = new HashMap<String, String>();
		
		try{
			Iterator<Entry<String,String>> it = remoteServerResponseFile.entrySet().iterator();
			while(it.hasNext()){
				Entry<String,String> entry = it.next();
				String path  = entry.getValue();
				if(path == null || path.length() == 0)
					continue;
				
				//Check if the response is an XML exception
				if(isRemoteServerResponseException(path)){
					toRemove.put(entry.getKey(), path);
				}
			}
			
			Iterator<Entry<String,String>> itR = toRemove.entrySet().iterator();
			while(itR.hasNext())
			{
				Entry<String, String> entry = itR.next();
				remoteServerExceptionFiles.put(entry.getKey(),entry.getValue());
				remoteServerResponseFile.remove(entry.getKey());
			}
			
			return remoteServerExceptionFiles;
		} catch (SAXException e) {
			servlet.logger.error(e.getMessage());
		} catch (IOException e) {
			servlet.logger.error(e.getMessage());
		} catch (ParserConfigurationException e) {
			servlet.logger.error(e.getMessage());
		} catch (JDOMException e) {
			servlet.logger.error(e.getMessage());
		}
		return remoteServerExceptionFiles;
	}
	
	/**
	 * Return if the file at the given path is an XML OGC exception file.
	 * @param path
	 * @return
	 * @throws SAXException
	 * @throws IOException
	 * @throws ParserConfigurationException
	 * @throws JDOMException 
	 */
	public boolean isRemoteServerResponseException(String path) throws SAXException, IOException, ParserConfigurationException, JDOMException{
		String ext = (path.lastIndexOf(".")==-1)?"":path.substring(path.lastIndexOf(".")+1,path.length());
		if (ext.equals("xml"))
		{
			SAXBuilder sxb = new SAXBuilder();
			Document documentMaster = sxb.build(new File(path));
			if (documentMaster != null) 
			{
				//ServiceExceptionReport is the root element name for WMS, WFS exception
				//ExceptionReport is the root element for OWS, WMTS, CSW exception
				if(documentMaster.getRootElement().getName().equalsIgnoreCase("ServiceExceptionReport") || documentMaster.getRootElement().getName().equalsIgnoreCase("ExceptionReport"))
					return true;
				else
					return false;
			}
		}
		return false;
	}
	
	public boolean filterResponseAndExceptionFiles(Hashtable<String,String> serverResponses, Hashtable<String, String> serverExceptions) throws Exception
	{
		 
		Hashtable<String,String> toRemove = new Hashtable<String,String>();
		
		Iterator<Map.Entry<String, String>> it = serverResponses.entrySet().iterator();
		while(it.hasNext())
		{
			Map.Entry<String,String> entry = (Map.Entry<String,String>)it.next();
			String path  = entry.getValue();
			if(path == null || path.length() == 0)
				continue;
			String ext = (path.lastIndexOf(".")==-1)?"":path.substring(path.lastIndexOf(".")+1,path.length());
			if (ext.equals("xml"))
			{
				SAXBuilder sxb = new SAXBuilder();
				Document documentMaster = sxb.build(new File(path));
				if (documentMaster != null) 
				{
					List<?> exceptionList = documentMaster.getContent(new ElementExceptionReportFilter());
					if(exceptionList.iterator().hasNext())
					{
						toRemove.put(entry.getKey(), path);
					}
				}
			}
		}
		
		if(toRemove.size() == 0)
			return false;
		
		Iterator<Map.Entry<String,String>> itR = toRemove.entrySet().iterator();
		while(itR.hasNext())
		{
			Map.Entry<String,String> entry = (Map.Entry<String,String>)itR.next();
			
			serverExceptions.put(entry.getKey(), entry.getValue());
			serverResponses.remove(entry.getKey());
		}
		return true;
	}
	
	/**
	 * Aggregate exception files from remote servers in one single file
	 * Valid for
	 * WMTS 1.0.0
	 * WFS 1.1
	 * 
	 */
	public ByteArrayOutputStream buildResponseForRemoteOgcException (Hashtable<String, String> ogcExceptionFilePathTable)
	{
		try 
		{
			Iterator<Map.Entry<String, String>> it = ogcExceptionFilePathTable.entrySet().iterator();
			while(it.hasNext())
			{
				Map.Entry<String,String> entry = (Map.Entry<String,String>)it.next();
				String path = entry.getValue();
				SAXBuilder sxb = new SAXBuilder();
				Document documentMaster = sxb.build(new File(path));
				if (documentMaster != null) 
				{
					List<?> exceptionReportList = documentMaster.getContent(new ElementExceptionReportFilter());
					Iterator<?> iExceptionReportList = exceptionReportList.iterator();
					if(iExceptionReportList.hasNext())
					{
						Element exceptionReport = (Element) iExceptionReportList.next();
						Iterator<Map.Entry<String, String>> itChild = ogcExceptionFilePathTable.entrySet().iterator();
						while (itChild.hasNext())
						{
							Map.Entry<String,String> entryChild = (Map.Entry<String,String>)itChild.next();
							String pathChild = entryChild.getValue();
							
							Document documentChild = null;
							if(path.equals(pathChild))
								continue;
							
							documentChild = sxb.build(new File(pathChild));
							
							if (documentChild != null) {
								List<?> exceptionList = documentChild.getContent(new ElementExceptionReportFilter());
								Iterator<?> iExceptionList = exceptionList.iterator();
								if (iExceptionList.hasNext())
								{
									@SuppressWarnings("unchecked")
									List<Element> exceptionListChild = ((Element) iExceptionList.next()).cloneContent();
									exceptionReport.addContent(exceptionListChild);
								}
							}
						}
					}
				}
				
				 XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
		         ByteArrayOutputStream out = new ByteArrayOutputStream();
		         sortie.output(documentMaster,out);
		         return out;
			}
		}
		catch (Exception ex)
		{
			ex.printStackTrace();
			return null;
		}
		return null;
	}

}
