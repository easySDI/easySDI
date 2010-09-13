package org.easysdi.proxy.csw;

import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.InputStream;
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
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathFactory;

import org.easysdi.proxy.core.ProxyServlet;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
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
	
	public void buildCompleteMetadata(List<String> filePathList )
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
							String serverUrl = "";
							String request = "";
							String fragment = "";
							serverUrl = link.substring(0, link.indexOf("?"));
//							System.out.println(serverUrl);
							request = link.substring(link.indexOf("?")+1);
							fragment = link.substring(link.indexOf("fragment")+ 9);
//							System.out.println(request);
//							System.out.println(proxy.sendData("GET", serverUrl, request));
							String resultFile = proxy.sendData("GET", serverUrl, request);
							InputStream xmlResult = new FileInputStream(resultFile);
							Document docResult = db.parse(xmlResult);
							docResult.getDocumentElement().normalize();
							
							
							
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
	
//	public String sendData(String method, String urlstr, String parameters) 
//	{
//		String responseContentType = null;
//		try {
//			if (urlstr != null) {
//				if (urlstr.endsWith("?")) {
//					urlstr = urlstr.substring(0, urlstr.length() - 1);
//				}
//			}
////			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
////			Date d = new Date();
////			dump("SYSTEM", "RemoteRequestUrl", urlstr);
////			dump("SYSTEM", "RemoteRequest", parameters);
////			dump("SYSTEM", "RemoteRequestLength", parameters.length());
////			dump("SYSTEM", "RemoteRequestDateTime", dateFormat.format(d));
//			String cookie = null;
//
//			if (proxy.getLoginService(urlstr) != null) {
//				cookie = geonetworkLogIn(getLoginService(urlstr));
//			}
//
//			String encoding = null;
//
//			if (getUsername(urlstr) != null && getPassword(urlstr) != null) {
//				String userPassword = getUsername(urlstr) + ":" + getPassword(urlstr);
//				encoding = new sun.misc.BASE64Encoder().encode(userPassword.getBytes());
//
//			}
//
//			if (method.equalsIgnoreCase("GET")) {
//
//				urlstr = urlstr + "?" + parameters;
//
//			}
//			URL url = new URL(urlstr);
//			HttpURLConnection hpcon = null;
//
//			hpcon = (HttpURLConnection) url.openConnection();
//			hpcon.setRequestMethod(method);
//			if (cookie != null) {
//				hpcon.addRequestProperty("Cookie", cookie);
//			}
//			if (encoding != null) {
//				hpcon.setRequestProperty("Authorization", "Basic " + encoding);
//			}
//			hpcon.setUseCaches(false);
//			hpcon.setDoInput(true);
//
//			if (method.equalsIgnoreCase("POST")) {
//				hpcon.setRequestProperty("Content-Length", "" + Integer.toString(parameters.getBytes().length));
//				// hpcon.setRequestProperty("Content-Type", contentType);
//				hpcon.setRequestProperty("Content-Type", "text/xml");
//
//				hpcon.setDoOutput(true);
//				DataOutputStream printout = new DataOutputStream(hpcon.getOutputStream());
//				printout.writeBytes(parameters);
//				printout.flush();
//				printout.close();
//			} else {
//				hpcon.setDoOutput(false);
//			}
//
//			// getting the response is required to force the request, otherwise
//			// it might not even be sent at all
//			InputStream in = null;
//
//			if (hpcon.getContentEncoding() != null && hpcon.getContentEncoding().indexOf("gzip") != -1) {
//				in = new GZIPInputStream(hpcon.getInputStream());
////				dump("DEBUG", "return of the remote server is zipped");
//			} else {
//				in = hpcon.getInputStream();
//			}
//
//			int input;
//
//			responseContentType = hpcon.getContentType().split(";")[0];
//			String tmpDir = System.getProperty("java.io.tmpdir");
////			dump(" tmpDir :  " + tmpDir);
//
//			File tempFile = createTempFile("sendData_" + UUID.randomUUID().toString(), getExtension(responseContentType));
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
////			dump("SYSTEM", "RemoteResponseToRequestUrl", urlstr);
////			dump("SYSTEM", "RemoteResponseLength", tempFile.length());
////
////			dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
////			d = new Date();
////			dump("SYSTEM", "RemoteResponseDateTime", dateFormat.format(d));
//			return tempFile.toString();
//
//		} 
//		catch (Exception e) 
//		{
//			e.printStackTrace();
//			return null;
//		}
//	}
}
