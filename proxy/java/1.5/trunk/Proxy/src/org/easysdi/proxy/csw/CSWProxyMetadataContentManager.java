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

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Result;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathFactory;

import org.easysdi.proxy.core.ProxyServlet;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.NodeList;
import org.w3c.dom.Node;


/**
 * 
 * @author DEPTH SA
 *
 */
public class CSWProxyMetadataContentManager 
{
	private ProxyServlet proxy;
	
	public CSWProxyMetadataContentManager (ProxyServlet cswProxy)
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
		try 
		{
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			dbf.setNamespaceAware(true);
			DocumentBuilder db;
			db = dbf.newDocumentBuilder();
			InputStream xml = new FileInputStream(filePathList.get(0));
			Document doc = db.parse(xml);
			doc.getDocumentElement().normalize();
			

			Node searchResults = doc.getElementsByTagNameNS("*", "SearchResults").item(0);
			String local_name = searchResults.getFirstChild().getNextSibling().getLocalName();
			String xpath = "//*[@xlink:href]";    
			
			NodeList metadataNodes = doc.getElementsByTagNameNS("*", local_name);
			
			int l = metadataNodes.getLength();
			for (int i=0; i<metadataNodes.getLength(); i++) 
			{
				NodeList xlinkNodes = org.apache.xpath.XPathAPI.selectNodeList(metadataNodes.item(i), xpath,metadataNodes.item(i));
				
				for (int j=0; j<xlinkNodes.getLength(); j++) 
				{
					Element elem = (Element)xlinkNodes.item(j);
					System.out.println(elem.getAttribute("xlink:href"));
					String link = elem.getAttribute("xlink:href");
					
					if (link != null) {
						if (link.contains("?")) {
							GetRequestHandler requestHandler = new GetRequestHandler(link);
							String serverUrl = requestHandler.getServer();
							String params = requestHandler.getParameters();
							String fragment = requestHandler.getFragment();
							serverUrl = "http://localhost:8070/proxy/ogc/geodbmeta_csw";
							
							InputStream xmlResult = sendData(serverUrl,params);
							Document docResult = db.parse(xmlResult);
							docResult.getDocumentElement().normalize();
														
							FileOutputStream tempFos = new FileOutputStream(filePathList.get(0));

							byte[] buf = new byte[32768];
							int nread;

							while ((nread = xml.read(buf, 0, buf.length)) >= 0) {
								tempFos.write(buf, 0, nread);
							}

							tempFos.flush();
							tempFos.close();
							xml.close();
						}
					}
				}
			}
			
			
			
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
		}
	}
	
	private void buildCompleteMetadataForGetRecordById(List<String> filePathList )
	{
		try 
		{
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			dbf.setNamespaceAware(true);
			DocumentBuilder db;
			db = dbf.newDocumentBuilder();
			InputStream xmlParent = new FileInputStream(filePathList.get(0));
			Document docParent = db.parse(xmlParent);
			docParent.getDocumentElement().normalize();
			
//			SAXBuilder sxb = new SAXBuilder();
//		      document = sxb.build(new File(fichier));
			
			Node searchResults = docParent.getElementsByTagNameNS("*", "GetRecordByIdResponse").item(0);
			String local_name = searchResults.getFirstChild().getNextSibling().getLocalName();
			String xpath = "//*[@xlink:href]";    
			
			NodeList metadataNodes = docParent.getElementsByTagNameNS("*", local_name);
			
			int l = metadataNodes.getLength();
			for (int i=0; i<metadataNodes.getLength(); i++) 
			{
				NodeList xlinkNodes = org.apache.xpath.XPathAPI.selectNodeList(metadataNodes.item(i), xpath,metadataNodes.item(i));
				
				for (int j=0; j<xlinkNodes.getLength(); j++) 
				{
					Element elementParent = (Element)xlinkNodes.item(j);
					System.out.println(elementParent.getAttribute("xlink:href"));
					String link = elementParent.getAttribute("xlink:href");
					
					if (link != null) {
						if (link.contains("?")) {
							GetRequestHandler requestHandler = new GetRequestHandler(link);
							String serverUrl = requestHandler.getServer();
							String params = requestHandler.getParameters();
							String fragment = requestHandler.getFragment();
							fragment = "bee:contact";
							serverUrl = "http://localhost:8070/proxy/ogc/geodbmeta_csw";
							
							InputStream xmlChild = sendData(serverUrl,params);
							includeFragment(docParent, elementParent, xmlChild, fragment);
							
							// Création de la source DOM
							DOMSource domSource = new DOMSource(docParent);
							
							// Création du fichier de sortie
							Result resultat = new StreamResult(filePathList.get(0));
							
							// Configuration du transformer
							TransformerFactory fabrique = TransformerFactory.newInstance();
							Transformer transformer = fabrique.newTransformer();
							transformer.setOutputProperty(OutputKeys.INDENT, "yes");
//							transformer.setOutputProperty(OutputKeys.ENCODING, "ISO-8859-1");
							
							// Transformation
							transformer.transform(domSource, resultat);
							
//							DOMSource domSource = new DOMSource(docParent);
//							StringWriter writer = new StringWriter();
//							StreamResult result = new StreamResult(writer);
//							TransformerFactory tf = TransformerFactory.newInstance();
//							Transformer transformer = tf.newTransformer();
//							transformer.transform(domSource, result);
//							String resultS = writer.getBuffer().toString();
//							FileOutputStream tempFos = new FileOutputStream(filePathList.get(0));
//							tempFos.write(resultS.getBytes());
//							tempFos.flush();
//							tempFos.close();
							xmlParent.close();
							xmlChild.close();
						}
					}
				}
			}
			
			
			
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
		}
	}
	
	private void includeFragment (Document docParent,  Element elementParent, InputStream xmlChild, String fragment)
	{
		try
		{
			//Remove parent node attribute
			elementParent.removeAttribute("xlink:show");
			elementParent.removeAttribute("xlink:actuate");
			elementParent.removeAttribute("xlink:href");
			elementParent.removeAttribute("xlink:type");
			
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			dbf.setNamespaceAware(true);
			DocumentBuilder db;
			db = dbf.newDocumentBuilder();
			Document docChild = db.parse(xmlChild);
			docChild.getDocumentElement().normalize();
			
			NodeList fragmentNodes = docChild.getElementsByTagName(fragment);
			for(int i = 0 ; i < fragmentNodes.getLength() ; i++)
			{
				Node oldChild = fragmentNodes.item(i);
				Node newChild = docParent.importNode(oldChild,true);
				elementParent.appendChild( newChild);
			}
			
		}
		catch (Exception ex)
		{
			ex.printStackTrace();
		}
		
	}
	
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
