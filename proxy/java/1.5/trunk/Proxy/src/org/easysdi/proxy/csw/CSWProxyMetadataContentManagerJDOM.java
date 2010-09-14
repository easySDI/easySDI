package org.easysdi.proxy.csw;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.StringBufferInputStream;
import java.io.StringWriter;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
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


/**
 * 
 * @author DEPTH SA
 *
 */
public class CSWProxyMetadataContentManagerJDOM 
{
	private ProxyServlet proxy;
	
	public CSWProxyMetadataContentManagerJDOM (ProxyServlet cswProxy)
	{
		proxy = cswProxy;
	}
	
	public void buildCompleteMetadata(String method, List<String> filePathList )
	{
		if("GetRecords".equalsIgnoreCase(method))
		{
			buildCompleteMetadataForGetRecords(filePathList);
		}
		else if ("GetRecordById".equalsIgnoreCase(method))
		{
			buildCompleteMetadataForGetRecordById(filePathList);
		}
	}
	
	private void buildCompleteMetadataForGetRecords(List<String> filePathList )
	{
		
	}
	
	private void includeFragment (Document docParent,  Element elementParent, InputStream xmlChild, String fragment)
	{
		try
		{
			Namespace ns = Namespace.getNamespace("http://www.w3.org/1999/xlink");
			elementParent.removeAttribute("show", ns);
			elementParent.removeAttribute("actuate", ns);
			elementParent.removeAttribute("type", ns);
			elementParent.removeAttribute("href", ns);
			
			 SAXBuilder sxb = new SAXBuilder();
			Document documentChild = sxb.build(xmlChild);
			Element elementChild = documentChild.getRootElement();
			
			Filter filtre = new Filter()
	    	   {
	    	     
				private static final long serialVersionUID = 1L;

				//On défini les propriétés du filtre à l'aide
	    	      //de la méthode matches
	    	      public boolean matches(Object ob)
	    	      {
	    	         //1 ère vérification : on vérifie que les objets
	    	         //qui seront filtrés sont bien des Elements
	    	         if(!(ob instanceof Element)){return false;}

	    	         //On crée alors un Element sur lequel on va faire les
	    	         //vérifications suivantes.
	    	         Element element = (Element)ob;
	    	         Namespace ns = Namespace.getNamespace("http://www.w3.org/1999/xlink");
	    	        
	    	         if(element.getQualifiedName().equals("bee:contact"))
	    	         {
	    	        	 return true;
	    	         }
	    	         else
	    	         {
	    	        	 return false;
	    	         }

	    	      }
	    	   };//Fin du filtre
	    	   Iterator it= elementChild.getDescendants(filtre);
	    	   
	    	   while(it.hasNext())
	    	   {
	    	      Element courantChild = (Element)it.next();
	    	      Element newChild = (Element)courantChild.clone();
	    	      elementParent.addContent(newChild);
			}
			
		}
		catch (Exception ex)
		{
			ex.printStackTrace();
		}
		
	}
	private void buildCompleteMetadataForGetRecordById(List<String> filePathList )
	{
		//On crée une instance de SAXBuilder
	      SAXBuilder sxb = new SAXBuilder();
	      Namespace ns = Namespace.getNamespace("http://www.w3.org/1999/xlink");
	      try
	      {
	         //On crée un nouveau document JDOM avec en argument le fichier XML
	         //Le parsing est terminé ;)
	    	  Document  docParent = sxb.build(new File(filePathList.get(0)));
	    	  
	    	  Element racine = docParent.getRootElement();
	    	  
	    	   //On crée un nouveau filtre
	    	   Filter filtre = new Filter()
	    	   {
	    	      /**
				 * 
				 */
				private static final long serialVersionUID = 1L;

				//On défini les propriétés du filtre à l'aide
	    	      //de la méthode matches
	    	      public boolean matches(Object ob)
	    	      {
	    	         //1 ère vérification : on vérifie que les objets
	    	         //qui seront filtrés sont bien des Elements
	    	         if(!(ob instanceof Element)){return false;}

	    	         //On crée alors un Element sur lequel on va faire les
	    	         //vérifications suivantes.
	    	         Element element = (Element)ob;
	    	         Namespace ns = Namespace.getNamespace("http://www.w3.org/1999/xlink");
	    	         Attribute xlink = element.getAttribute("href", ns);
	    	         if(xlink != null)
	    	         {
	    	        	 return true;
	    	         }
	    	         else
	    	         {
	    	        	 return false;
	    	         }

	    	      }
	    	   };//Fin du filtre
	    	  
	    	   Iterator i= racine.getDescendants(filtre);
	    	   
	    	   while(i.hasNext())
	    	   {
	    	      Element courant = (Element)i.next();
	    	      
	    	      GetRequestHandler requestHandler = new GetRequestHandler(courant.getAttribute("href", ns).getValue());
					String serverUrl = requestHandler.getServer();
					String params = requestHandler.getParameters();
					String fragment = requestHandler.getFragment();
					fragment = "bee:contact";
					serverUrl = "http://localhost:8070/proxy/ogc/geodbmeta_csw";
					
					
					InputStream xmlChild = sendData(serverUrl,params);
					
					includeFragment(docParent, courant, xmlChild, fragment);
				}
	    	  
	    	   XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
	           sortie.output(docParent, new FileOutputStream(filePathList.get(0)));
	    	   
	    	  
	      }
		catch (Exception ex )
		{
			ex.printStackTrace();
		}
		finally
		{
			
		}

	}
	
//	private void includeFragment (Document docParent,  Element elementParent, InputStream xmlChild, String fragment)
//	{
//		try
//		{
////			//Remove parent node attribute
////			elementParent.removeAttribute("xlink:show");
////			elementParent.removeAttribute("xlink:actuate");
////			elementParent.removeAttribute("xlink:href");
////			elementParent.removeAttribute("xlink:type");
////			
////			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
////			dbf.setNamespaceAware(true);
////			DocumentBuilder db;
////			db = dbf.newDocumentBuilder();
////			Document docChild = db.parse(xmlChild);
////			docChild.getDocumentElement().normalize();
////			
////			NodeList fragmentNodes = docChild.getElementsByTagName(fragment);
////			for(int i = 0 ; i < fragmentNodes.getLength() ; i++)
////			{
////				Node oldChild = fragmentNodes.item(i);
////				Node newChild = docParent.importNode(oldChild,true);
////				elementParent.appendChild( newChild);
////			}
//			
//		}
//		catch (Exception ex)
//		{
//			ex.printStackTrace();
//		}
//		
//	}
	
	private InputStream sendData(String urlstr, String parameters) 
	{
		String responseContentType = null;
		try 
		{
			String cookie = null;

			String encoding = null;
//			if (getUsername(urlstr) != null && getPassword(urlstr) != null) {
//				String userPassword = getUsername(urlstr) + ":" + getPassword(urlstr);
//				encoding = new sun.misc.BASE64Encoder().encode(userPassword.getBytes());
//			}
			urlstr = urlstr + "?" + parameters;
			URL url = new URL(urlstr);
			HttpURLConnection hpcon = null;

			hpcon = (HttpURLConnection) url.openConnection();
			hpcon.setRequestMethod("GET");
			if (cookie != null) {
				hpcon.addRequestProperty("Cookie", cookie);
			}
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

//			int input;

//			responseContentType = hpcon.getContentType().split(";")[0];
//			String tmpDir = System.getProperty("java.io.tmpdir");
//
//			File tempFile = createTempFile("sendData_" + UUID.randomUUID().toString(), proxy.getExtension(responseContentType));
//
//			FileOutputStream tempFos = new FileOutputStream(tempFile);
//
//			byte[] buf = new byte[32768];
//			int nread;
//
//			while ((nread = in.read(buf, 0, buf.length)) >= 0) {
//				tempFos.write(buf, 0, nread);
//			}
//
//			tempFos.flush();
//			tempFos.close();
//			in.close();

			return in;

		} 
		catch (Exception e) 
		{
			e.printStackTrace();
			return null;
		}
	}
}
