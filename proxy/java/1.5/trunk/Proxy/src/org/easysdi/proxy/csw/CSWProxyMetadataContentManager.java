package org.easysdi.proxy.csw;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.InputStream;
import java.util.List;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;

import org.w3c.dom.Document;
import org.w3c.dom.NodeList;

/**
 * 
 * @author DEPTH SA
 *
 */
public class CSWProxyMetadataContentManager 
{
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
			
			String xpath = "//*[@xlink:href]";    
			
			NodeList xlinkNodes = doc.getElementsByTagName("xlink:href");
			
			if (xlinkNodes.getLength() == 0)
			{
				return;
			}
			
			for(int i = 0 ; i < xlinkNodes.getLength() ; i++)
			{
				
			}
			
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
		}
	}
}
