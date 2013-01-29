/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
package org.easysdi.proxy.csw;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.List;
import java.util.zip.GZIPInputStream;

import org.easysdi.jdom.filter.*;
import org.easysdi.jdom.filter.ElementFragmentFilter;
import org.easysdi.proxy.core.ProxyServlet;
import java.util.Iterator;
import org.jdom.*;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.jdom.xpath.XPath;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;


/**
 * 
 * @author DEPTH SA
 *
 */
public class CSWProxyMetadataContentManager 
{
	private ProxyServlet proxy;
	private String _lastError ="";
	private static final Namespace ns = Namespace.getNamespace("http://www.w3.org/1999/xlink");
	
	public CSWProxyMetadataContentManager (ProxyServlet cswProxy)
	{
		proxy = cswProxy;
	}
		
	private void includeFragment (Document docParent,  Element elementParent, Element elementChild, String fragment)  throws IOException, JDOMException
	{
		elementParent.removeAttribute("show", ns);
		elementParent.removeAttribute("actuate", ns);
		elementParent.removeAttribute("type", ns);
		elementParent.removeAttribute("href", ns);
		
		if(fragment == null || fragment.equalsIgnoreCase(""))
		{
			elementParent.addContent(elementChild.cloneContent());
			return;
		}
		
		Filter filtre = new ElementFragmentFilter(fragment);
		Iterator it= elementChild.getDescendants(filtre);
		  
		while(it.hasNext())
		{
			Element courantChild = (Element)it.next();
			List newContentChild = courantChild.cloneContent();
		    elementParent.addContent(newContentChild);
		}
	}
	
	@SuppressWarnings("unchecked")
	public boolean  buildCompleteMetadata(String filePath )
	{
		SAXBuilder sxb = new SAXBuilder();
	    try
	    {
	    	Document  docParent = sxb.build(new File(filePath));
	    	Element racine = docParent.getRootElement();
	    	
	      
	    	//Get all the attributes 'xlink:href'
	    	Filter filtre = new AttributeXlinkFilter();
	    	
	    	//We can not modify Elements while we loop over them with an iterator.
	    	//We have to use a separate List storing the Elements we want to modify.	    	
	    	List<Element> elList = new ArrayList<Element>();	    	  
	    	//Iterator<?> i= racine.getDescendants(filtre);
	    	
	    	//Get only the Metadata which are not harvested : the complete process is only available for the MD manage by the EasySDI solution
	    	XPath xpa = XPath.newInstance("//gmd:MD_Metadata[sdi:platform/@harvested='false']");   
	    	xpa.addNamespace("gmd", "http://www.isotc211.org/2005/gmd");
	    	xpa.addNamespace("sdi", "http://www.easysdi.org/2011/sdi");
	    	List<Element> easysdMDList = (List<Element>)xpa.selectNodes(racine);
	    	
	    	for (Element element : easysdMDList) {
	    		Iterator<?> i = element.getDescendants(filtre);
	    		
	    		while(i.hasNext())
		    	{
		    	   Element courant = (Element)i.next();
		    	   elList.add(courant);
		    	}
			} 
	    	
	    	/*while(i.hasNext())
	    	{
	    	   Element courant = (Element)i.next();
	    	   elList.add(courant);
	    	}*/
	    	
//	    	proxy.dump("DEBUG","Start - Loop on metadata");
	    	//Modification of the selected Elements
	    	for (int j = 0 ; j < elList.size(); j++)
	    	{
	    		String target = elList.get(j).getAttribute("href", ns).getValue();
	    		CSWProxyGetRequestHandler requestHandler = new CSWProxyGetRequestHandler(elList.get(j).getAttribute("href", ns).getValue());
				String serverUrl = requestHandler.getServer();
				String params = requestHandler.getParameters();
				String fragment = requestHandler.getFragment();

//				proxy.dump("DEBUG","Get fragment's content - "+params);
				InputStream xmlChild = sendData(serverUrl,params);
				if(xmlChild == null)
				{
					_lastError = ("Error on : "+URLEncoder.encode(target));
					return false;
				}
//				proxy.dump("DEBUG","End - Get content metadata");
//				
//				proxy.dump("DEBUG","Start - Check content metadata");
				//Check if the response is an Ogc Exception
				Document documentChild = sxb.build(xmlChild);
				Element elementChild = documentChild.getRootElement();
				ElementFilter exceptionFilter = new ElementFilter("ExceptionReport");
				List  l = elementChild.getContent(exceptionFilter);
				if(l.size() > 0)
				{
					//Exception returned 
					_lastError = ("OGC Exception returned by : "+target);
					return false;
				}
//				proxy.dump("DEBUG","End - Check content metadata");
//				proxy.dump("DEBUG","Start - Include content metadata");
				try
				{
					includeFragment(docParent, elList.get(j), elementChild, fragment);
				}
				catch (Exception ex)
				{
					//Fragment can not be include
					//The all request is aborted
					//An OGC exception will be send
					proxy.logger.error(ex.getMessage());
					_lastError = ("Error on : "+target);
					return false;
				}
//				proxy.dump("DEBUG","End - Include content metadata");
    	   }
//	    	proxy.dump("DEBUG","End - Loop on metadata");
    	   XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
    	   FileOutputStream outStream = new FileOutputStream(filePath);
           sortie.output(docParent, outStream);
           outStream.close();
           return true;	
	    }
		catch (Exception ex )
		{
			//Complete metadata can not be build
			//The request is aborted
			//An OGC exception will be send
			proxy.logger.error(ex.getMessage());
			return false;
		}
	}

	public String GetLastError()
	{
		String temp = _lastError;
		_lastError = "";
		return temp;
	}
	
	private InputStream sendData(String urlstr, String parameters) 
	{
		try 
		{
			String encoding = null;
			
			Authentication token = SecurityContextHolder.getContext().getAuthentication();
			
			if (token != null && token.getPrincipal().toString() != null && token.getCredentials().toString() != null) {
				String user = token.getPrincipal().toString() ;
				String password = token.getCredentials().toString();
				if(password != null && !password.equals(""))
				{
					password = password.split(":")[0];
					String userPassword = user + ":" + password;
					encoding = new sun.misc.BASE64Encoder().encode(userPassword.getBytes());
				}
			}

			urlstr = urlstr + "?" + parameters;
			URL url = new URL(urlstr);
			HttpURLConnection hpcon = null;

			hpcon = (HttpURLConnection) url.openConnection();
			hpcon.setRequestMethod("GET");
			if (encoding != null) {
				hpcon.setRequestProperty("Authorization", "Basic " + encoding);
			}
			hpcon.setUseCaches(false);
			hpcon.setDoInput(true);
			hpcon.setDoOutput(false);
			
			InputStream in = null;

			if (hpcon.getContentEncoding() != null && hpcon.getContentEncoding().indexOf("gzip") != -1) {
				in = new GZIPInputStream(hpcon.getInputStream());
			} 
			else 
			{
				in = hpcon.getInputStream();
			}

			return in;

		} 
		catch (Exception ex) 
		{
			proxy.logger.error(ex.getMessage());
			return null;
		}
	}
}
