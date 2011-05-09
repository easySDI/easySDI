package org.easysdi.publish.util;

import java.util.HashMap;
import java.util.Map;
import java.util.logging.Logger;

//import ch.depth.services.wps.WPSServlet;
import java.io.File;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import org.deegree.services.controller.OGCFrontController;

public class TransformerPluginsLoader {
	static int proc = 0;
	static Map<String,String> initParams = null;
	static Logger logger = Logger.getLogger("org.easysdi.publish.TransformerPluginsLoader");

	public static String getInitParameter(String s){
		if(initParams == null){
			try{
				initParams = new HashMap<String,String>();
				File file = new File(OGCFrontController.getInstance().getServletConfig().getServletContext().getRealPath("/")+"WEB-INF/TransformerPluginConfig.xml");
				DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
				DocumentBuilder db = dbf.newDocumentBuilder();
				Document doc = db.parse(file);
				doc.getDocumentElement().normalize();
				NodeList nd = doc.getChildNodes().item(0).getChildNodes();
				logger.info("Read init parameters:");
				for(int i=0; i<nd.getLength(); i++){
					//org.apache.xerces.dom.DeferredElementImpl
					Node n = nd.item(i);
					if(!n.getNodeName().equals("#text")&&!n.getNodeName().equals("#comment")){
						String name = ((Element)nd.item(i)).getNodeName();
						String value = ((Element)nd.item(i)).getTextContent();
						initParams.put(name,value);
						logger.info(name+":"+value);
					}
				}
				return (String)initParams.get(s);
			}
			catch (Exception e) {
				System.out.println("ERROR: Unable to parse plugin document: WEB-INF/TransformerPluginConfig.xml.");
				e.printStackTrace();
				return null;
			}
			
		}
		else{
			return (String)initParams.get(s);
		}
	}
}

