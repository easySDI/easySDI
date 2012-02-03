package org.easysdi.proxy.ows.v200;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.util.Collection;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import org.easysdi.jdom.filter.ElementExceptionReportFilter;
import org.easysdi.proxy.ows.OWSExceptionManager;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;


public class OWS200ExceptionManager implements OWSExceptionManager {
	
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
