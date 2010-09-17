package org.easysdi.proxy.csw;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.StringBufferInputStream;
import java.io.StringWriter;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.UUID;
import java.util.zip.GZIPInputStream;


import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathFactory;

import org.easysdi.proxy.core.ProxyServlet;
import java.util.Iterator;
import org.jdom.*;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
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
		
	private void includeFragment (Document docParent,  Element elementParent, InputStream xmlChild, String fragment)  throws IOException, JDOMException
	{
		elementParent.removeAttribute("show", ns);
		elementParent.removeAttribute("actuate", ns);
		elementParent.removeAttribute("type", ns);
		elementParent.removeAttribute("href", ns);
		
		SAXBuilder sxb = new SAXBuilder();
		Document documentChild = sxb.build(xmlChild);
		Element elementChild = documentChild.getRootElement();
		
		if(fragment == null || fragment.equalsIgnoreCase(""))
		{
			elementParent.addContent(elementChild.cloneContent());
			return;
		}
		
		Filter filtre = new CSWProxyMetadataFragmentFilter(fragment);
		Iterator it= elementChild.getDescendants(filtre);
		  
		while(it.hasNext())
		{
			Element courantChild = (Element)it.next();
			List newContentChild = courantChild.cloneContent();
		    elementParent.addContent(newContentChild);
		}
	}
	
	public boolean  buildCompleteMetadata(String filePath )
	{
		SAXBuilder sxb = new SAXBuilder();
	    try
	    {
	    	Document  docParent = sxb.build(new File(filePath));
	    	Element racine = docParent.getRootElement();
	      
	    	Filter filtre = new CSWProxyMetadataContentFilter();
	    	
	    	//We can not modify Elements while we loop over them with an iterator.
	    	//We have to use a separate List storing the Elements we want to modify.	    	
	    	List<Element> elList = new ArrayList<Element>();	    	  
	    	Iterator i= racine.getDescendants(filtre);
	    	   
	    	while(i.hasNext())
	    	{
	    	   Element courant = (Element)i.next();
	    	   elList.add(courant);
	    	}
	    	
	    	//Modification of the selected Elements
	    	for (int j = 0 ; j < elList.size(); j++)
	    	{
	    		GetRequestHandler requestHandler = new GetRequestHandler(elList.get(j).getAttribute("href", ns).getValue());
				String serverUrl = requestHandler.getServer();
				String params = requestHandler.getParameters();
				String fragment = requestHandler.getFragment();
				fragment = "bee:contact";
				serverUrl = "http://localhost:8070/proxy/ogc/geodbmeta_csw";

				InputStream xmlChild = sendData(serverUrl,params);
				if(xmlChild == null)
				{
					_lastError = ("Error on : "+elList.get(j).getAttribute("href", ns).getValue());
					return false;
				}
				try
				{
					includeFragment(docParent, elList.get(j), xmlChild, fragment);
				}
				catch (Exception ex)
				{
					//Fragment can not be include
					//The all request is aborted
					//An OGC exception will be send
					proxy.dump("ERROR",ex.getMessage());
					_lastError = ("Error on : "+elList.get(j).getAttribute("href", ns).getValue());
					return false;
				}
    	   }
	    	  
    	   XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
           sortie.output(docParent, new FileOutputStream(filePath));
           return true;
	    }
		catch (Exception ex )
		{
			//Complete metadata can not be build
			//The request is aborted
			//An OGC exception will be send
			proxy.dump("ERROR",ex.getMessage());
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
			proxy.dump("ERROR",ex.getMessage());
			return null;
		}
	}
}
