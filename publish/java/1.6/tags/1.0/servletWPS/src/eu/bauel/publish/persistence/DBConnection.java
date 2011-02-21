package eu.bauel.publish.persistence;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import javax.servlet.ServletConfig;
import ch.depth.services.wps.WPSServlet;
import java.io.File;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import com.sun.org.apache.xerces.internal.dom.DeferredCommentImpl;
import com.sun.org.apache.xerces.internal.dom.DeferredTextImpl;

public class DBConnection {
	static int proc = 0;
	static Connection c = null;
	static Map<String,String> initParams = new HashMap<String,String>();
	static Logger logger = Logger.getLogger("ch.depth.services.wps.WPSServlet");
	
	public static Connection getInstance(String configPath){
		if(c == null)
		{
		    c = getConnection(configPath);
		    return c;
		}else{
			try {
				if(c.isClosed()){
					c = getConnection(configPath);
					return c;
				}
				else{
				    return c;
				}
			} catch (SQLException e) {
				e.printStackTrace();
				return c;
			}
		}
	}
	
	private static Connection getConnection(String configPath){
		String DB_MANUFACTURER;
		String DB_NAME = null;
		//The HSQL Database configuration
		String DB_LOCATION = null;
		//The driver
		String CONNECTION_DRIVER = null;
		//The connection string
		String CONNECTION_URL = null;
		//The connection string
		String CONNECTION_USER = null;
		//The connection string
		String CONNECTION_PASSWORD = null;

		try {
			File file = new File(configPath+"WEB-INF/DbConfig.xml");
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();
			Document doc = db.parse(file);
			doc.getDocumentElement().normalize();
			NodeList nd = doc.getChildNodes().item(0).getChildNodes();

			for(int i=0; i<nd.getLength(); i++){
				//skip comments
				if(!(nd.item(i) instanceof DeferredTextImpl) && !(nd.item(i) instanceof DeferredCommentImpl)){
					String name = ((Element)nd.item(i)).getNodeName();
					String value = ((Element)nd.item(i)).getTextContent();
					initParams.put(name,value);
				}
			}
			
			DB_MANUFACTURER = initParams.get("DB_MANUFACTURER");
			DB_NAME = initParams.get("DB_NAME");
			//The driver
			CONNECTION_DRIVER = initParams.get("CONNECTION_DRIVER");
			//The connection string
			CONNECTION_URL = initParams.get("CONNECTION_URL");
			//The connection string
			CONNECTION_USER = initParams.get("CONNECTION_USER");
			//The connection string
			CONNECTION_PASSWORD = initParams.get("CONNECTION_PASSWORD");

			//init the servlet with defaults values located in WebContent/WEB-INF/HsqldbConfig.xml file
			//Those values are read and loaded once in the HSQL database (version 0).
			//The HSQL Database configuration

			//Start the HSQLDB in Server Mode, with one (default) database with files named "mydb.*".
			//please place hsqldb.lib in tomcat /lib directory
			//for db type and java representation, check here:
			//http://hsqldb.org/doc/guide/ch09.html#datatypes-section

			//org.hsqldb.Server.main(new String[]{"-database.0", "file:"+DB_LOCATION+DB_NAME, "-dbname.0", DB_NAME});
		}
		catch (Exception e) {
			System.out.println("ERROR: failed to start the HSQLDB.");
			e.printStackTrace();
		}

		//Initiate the connection to the database
		try {
			Class.forName(CONNECTION_DRIVER).newInstance();
			System.out.println(CONNECTION_URL+"/"+DB_NAME + CONNECTION_USER + CONNECTION_PASSWORD);
			c = DriverManager.getConnection(CONNECTION_URL+"/"+DB_NAME, CONNECTION_USER, CONNECTION_PASSWORD);
			proc = 1;

			//Now, leave the DB as it is, or create the persistence for the first time
			//and feed them with the default application values located in web.xml file
			//then we have the system up and running
			DbTableInit.init(c);

			return c;
		} 
		catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return null;
		}
		catch (Exception e) {
			logger.warning("Coulde not connect to db");
			logger.warning(e.getMessage());
			e.printStackTrace();
			return null;
		}
	}

	public static String getInitParameter(String s){
		return (String)initParams.get(s);
	}
}
