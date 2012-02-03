/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & Remy Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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
package ch.depth.services.wps;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.lang.ref.WeakReference;
import java.net.URL;
import java.net.URLConnection;
import java.security.CodeSource;
import java.security.ProtectionDomain;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.Unmarshaller;

import net.opengis.wps._1_0.InputType;
import eu.bauel.publish.diffusion.*;
import eu.bauel.publish.exception.DataInputException;
import eu.bauel.publish.exception.DataSourceNotFoundException;
import eu.bauel.publish.exception.DataSourceWrongFormatException;
import eu.bauel.publish.exception.DiffuserException;
import eu.bauel.publish.exception.DiffuserNotFoundException;
import eu.bauel.publish.exception.FeatureSourceException;
import eu.bauel.publish.exception.FeatureSourceNotFoundException;
import eu.bauel.publish.exception.IncompatibleUpdateFeatureSourceException;
import eu.bauel.publish.exception.LayerExistingForFeatureSourceException;
import eu.bauel.publish.exception.LayerNotFoundException;
import eu.bauel.publish.exception.PublicationException;
import eu.bauel.publish.exception.PublishConfigurationException;
import eu.bauel.publish.exception.PublishGeneralException;
import eu.bauel.publish.exception.ScriptNotFoundException;
import eu.bauel.publish.exception.TransformationException;
import eu.bauel.publish.persistence.*;
import eu.bauel.publish.transformation.*;
import eu.bauel.publish.validation.InputValidator;
import eu.bauel.publish.ClassPathHacker;
import eu.bauel.publish.Utils;

//Sample GetCapabilities of this servlet:
//http://localhost:8083/servletWPS/WPSServlet?request=GetCapabilities
public class WPSServlet extends HttpServlet {

	//the location to the transformer plug-in directory
	public static final String PLUGIN_SUBDIR = "WEB-INF/transformerPlugIns/";

	private Map<String, String> ERROR_DEFINITION_MAP = new HashMap<String, String>();

	Logger logger = Logger.getLogger("ch.depth.services.wps.WPSServlet");

	//The connection to HSQLDB
	public static Connection c;

	//Execution path for the transformer plug-in.
	public static String m_executionPath = "";

	public static FeatureSourceManager fsm;

	public void init(ServletConfig config) throws ServletException {
		//Get a connection to the database
		c = DBConnection.getInstance(config.getServletContext().getRealPath("/"));
		JTable.setConnection(c);
		
		m_executionPath = config.getServletContext().getRealPath("/");
		fsm = new FeatureSourceManager();
		
		TransformerHelper.init( config, m_executionPath + PLUGIN_SUBDIR );
		TransformerHelper.getFileTypeToTransformerAssociation();

		//Exception definition
		ERROR_DEFINITION_MAP.put("DataInput", "Wrong input value: ");
		ERROR_DEFINITION_MAP.put("FeatureSource", "Problem accessing the Feature Source: ");	
		ERROR_DEFINITION_MAP.put("Diffuser", "Problem accessing the Diffuser: ");	
		ERROR_DEFINITION_MAP.put("PublishConfiguration", "The server has a configuration troubles: ");
		ERROR_DEFINITION_MAP.put("Transformation", "Error performing the transformation: ");
		ERROR_DEFINITION_MAP.put("PublishGeneral", "The system encountered a fatal error: ");
		ERROR_DEFINITION_MAP.put("Publication", "Error performing the publication: ");
		ERROR_DEFINITION_MAP.put("UnknownIdentifier", "The execute type %s is not known.");
		ERROR_DEFINITION_MAP.put("UndefinedIdentifier", "The identifier is not defined.");


		
	}

	//Answer the WPS "execute" process.
	public void doPost(HttpServletRequest req, HttpServletResponse resp){    

		try{
			JAXBContext jc =JAXBContext.newInstance(net.opengis.wps._1_0.Execute.class);
			Unmarshaller um = jc.createUnmarshaller();	    

			//put WPS execute request in an object and call the method accordingly to the identifier,
			//then return the xml response.

			StringBuilder sb = new StringBuilder(); 
			/*
			 * 
			 * 
			String line;
			try { 
				BufferedReader reader = new BufferedReader(new InputStreamReader(req.getInputStream()));
				while ((line = reader.readLine()) != null) { 
					sb.append(line).append("\n"); 
				} 
			} finally { 
				req.getInputStream().close(); 
			}
			System.out.println("req->");
			System.out.println(sb.toString());
			System.out.println("<- req");
            */
			net.opengis.wps._1_0.Execute execute = (net.opengis.wps._1_0.Execute) um.unmarshal(req.getInputStream());
			if (execute!=null && execute.getIdentifier() !=null && execute.getIdentifier().getValue()!=null){
				String executeType = execute.getIdentifier().getValue();

				if (executeType.equalsIgnoreCase("transformDataset")){
					resp.setContentType("text/xml");
					resp.getOutputStream().write(transformDataset(execute).getBytes("UTF-8"));
				}
				else if (executeType.equalsIgnoreCase("publishLayer")){
					resp.setContentType("text/xml");
					resp.getOutputStream().write(publishLayer(execute).getBytes("UTF-8"));
				}
				else if (executeType.equalsIgnoreCase("deleteLayer")){
					resp.setContentType("text/xml");
					resp.getOutputStream().write(removeLayer(execute).getBytes("UTF-8"));
				}			
				else if (executeType.equalsIgnoreCase("deleteFeatureSource")){
					resp.setContentType("text/xml");
					resp.getOutputStream().write(deleteFeatureSource(execute).getBytes("UTF-8"));
				}
				else{
					logger.info( "*************************************");
					logger.info( "doPost() returned: UnknownIdentifier:");
					logger.info( "*************************************");
					String error = String.format
					(ERROR_DEFINITION_MAP.get("UnknownIdentifier"), executeType);
					resp.setContentType("text/xml");
					resp.getOutputStream().write(Utils.exceptiontoXML("UnknownIdentifier",error).getBytes("UTF-8"));
					// Type not allowed
				}
			}
			else{
				logger.info( "*************************************");
				logger.info( "doPost() returned: UndefinedIdentifier:");
				logger.info( "*************************************");
				resp.setContentType("text/xml");
				resp.getOutputStream().write(Utils.exceptiontoXML("UndefinedIdentifier",ERROR_DEFINITION_MAP.get("UnknownIdentifier")).getBytes("UTF-8"));				
			}
		}catch (Exception e){
			e.printStackTrace();
			try{
				resp.setContentType("text/xml");
				resp.getOutputStream().write(Utils.exceptiontoXML("PublishGeneralError",ERROR_DEFINITION_MAP.get("PublishGeneral")+e.getMessage()).getBytes("UTF-8"));
			}catch(Exception e2){
				e2.printStackTrace();
			}
		}
		finally{
			try {
				resp.flushBuffer();
				resp.getOutputStream().close();

				System.out.println("Close Buffer");
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}

	}

	// for now only answer to a "sample" GetCapabilities.
	public void doGet(HttpServletRequest req, HttpServletResponse resp)    {
		try{
			String operation = null;
			String version = "000";
			String service = "";
			String width = "";
			String height ="";
			String format ="";

			Enumeration<String> parameterNames = req.getParameterNames();
			String paramUrlBase = "";	

			logger.info( "******************DO GET REQUESTED *************************");
			// To build the request to dispatch
			while (parameterNames.hasMoreElements()) {
				String key = (String) parameterNames.nextElement();
				String value  = req.getParameter(key);
				logger.info( "Key:"+key+" value:"+value);
				if (key.equalsIgnoreCase("Request")) {
					// Gets the requested Operation
					if (value.equalsIgnoreCase("capabilities")){
						operation = "GetCapabilities";
					}else{
						operation = value;
					}
				}else
					if (key.equalsIgnoreCase("service")) {
						// Gets the requested Operation
						service = value;
					}	    
			}

			if ( operation == null)
			{
				resp.setContentType("text/xml");
				resp.getOutputStream().write(Utils.exceptiontoXML("OPERATIONNOTDEFINED","The parameter OPERATION is not defined.").getBytes("UTF-8"));
			}
			else if (operation.equalsIgnoreCase("GetCapabilities")){
				resp.setContentType("text/xml");		
				resp.getOutputStream().write(getCapabilities(req).getBytes());
			}
			else{
				resp.setContentType("text/xml");
				resp.getOutputStream().write(Utils.exceptiontoXML("OPERATIONNOTDEFINED","This operation is not defined in GET method").getBytes("UTF-8"));
				// Type not allowed
			}	    	 	    	

		}catch(IOException e){
			e.printStackTrace();
			try{
				resp.setContentType("text/xml");
				resp.getOutputStream().write(Utils.exceptiontoXML("PublishCommunicationException",ERROR_DEFINITION_MAP.get("PublishCommunicationException")+e.getMessage()).getBytes("UTF-8"));
			}catch(Exception e2){
				e2.printStackTrace();
			}

		}
	}

	private String getCapabilities(HttpServletRequest req){

		InputStream is = this.getClass().getResourceAsStream("capabilities.xml");
		StringBuffer sb = new StringBuffer();

		try{

			BufferedReader in = new BufferedReader(new InputStreamReader(is));
			String input;

			while ((input = in.readLine()) != null) {
				sb.append (input);
			}

		}catch (Exception e){
			e.printStackTrace();
		}
		return sb.toString().replaceAll("xlink:href=\"IP\"", "xlink:href=\""+getServletUrl(req)+"\"").toString();
	}


	protected String getServletUrl(HttpServletRequest req) {

		String scheme = req.getScheme(); // http
		String serverName = req.getServerName(); // hostname.com
		int serverPort = req.getServerPort(); // 80
		String contextPath = req.getContextPath(); // /mywebapp
		String servletPath = req.getServletPath(); // /servlet/MyServlet
		String pathInfo = req.getPathInfo(); // /a/b;c=123
		// String queryString = req.getQueryString(); // d=789

		String url = scheme + "://" + serverName + ":" + serverPort
		+ contextPath + servletPath;
		if (pathInfo != null) {
			url += pathInfo;
		}

		return url;
	}

	/*
	 * This method must catch all exceptions and wrap them into an XML envelope
	 */
	private String publishLayer( net.opengis.wps._1_0.Execute execute){
		try
		{
			//Fill with map content
			Map<String, List<String>> paramMap = Utils.makeParamMap(execute);
			//DiffusorControllerAdaptor diffusorControllerMock = new MockDiffusorControllerAdaptor();
			IDiffuserAdapter diffusorController = new GeoserverDiffuserControllerAdaptor();
			//read request parameters
			String layerId = Utils.getParameter("LayerId", paramMap).get(0);
			String FeatureTypeId = Utils.getParameter("FeatureTypeId", paramMap).get(0);
			List<String> AttributeAlias = Utils.getParameter("AttributeAlias", paramMap);
			String Title = Utils.getParameter("Title", paramMap).get(0);
			String Quality_Area = Utils.getParameter("Quality/Area", paramMap).get(0);		
			String KeywordList = Utils.getParameter("KeywordList", paramMap).get(0);		
			String Abstract = Utils.getParameter("Abstract", paramMap).get(0);		
			String Name = Utils.getParameter("Name", paramMap).get(0);
			String Geometry = Utils.getParameter("geometry", paramMap).get(0);

			//Check the input values
			//this method returns nothing, but can throw a DataInputException if the checks fail
			//these errors are returned to the caller.
			InputValidator.publishLayerValidator(layerId, FeatureTypeId, AttributeAlias, Title, Name, Quality_Area, KeywordList, Abstract);
			//Todo use mutable StringBuilder
			StringBuilder ret = new StringBuilder();
			PublishLayerResponse resp = diffusorController.publishLayer( layerId, FeatureTypeId, AttributeAlias, Title, Name, Quality_Area, KeywordList, Abstract, Geometry);
			//BUILD the WPS Envelope
			ret.append(Utils.WPSHeader("publishLayer") +
					Utils.WPSResponseSectionDataInput( paramMap )+
					Utils.WPSResponseFiller("LayerId", resp.layerGuid ));

			for( int i = 0; i < resp.endPoints.size(); i++ )
			{
				ret.append(Utils.WPSResponseFiller(resp.endPointsTypes.get(i), resp.endPoints.get(i)));
			}
			for( int i = 0; i < resp.bbox.size(); i++ )
			{
				ret.append(Utils.WPSResponseFiller(resp.bboxTypes.get(i), resp.bbox.get(i)));
			}
			ret.append(Utils.WPSFooter());
			logger.info( "**************************************************************");
			logger.info( "publishLayer() returned: " +  ret.toString());
			logger.info( "**************************************************************");
			return ret.toString();
		}
		catch(DataInputException e){
			logger.info( "**************************************************************");
			logger.info( "publishLayer() returned: DataInputException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("DataInputException", ERROR_DEFINITION_MAP.get("DataInput")+ e.getMessage());
		}
		catch (FeatureSourceException e){
			logger.info( "******************************************************************");
			logger.info( "publishLayer() returned: FeatureSourceException:" +  e.getMessage());
			logger.info( "******************************************************************");
			return Utils.exceptiontoXML("FeatureSourceException", ERROR_DEFINITION_MAP.get("FeatureSource")+ e.getMessage());
		}
		catch (PublicationException e){
			logger.info( "*****************************************************************");
			logger.info( "publishLayer() returned: PublicationException:" +  e.getMessage());
			logger.info( "*****************************************************************");
			return Utils.exceptiontoXML("PublicationException", ERROR_DEFINITION_MAP.get("Publication")+ e.getMessage());
		}
		catch (DiffuserException e) {
			logger.info( "**************************************************************");
			logger.info( "publishLayer() returned: DiffuserException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("DiffuserException", ERROR_DEFINITION_MAP.get("Diffuser")+e.getMessage());
		}
		catch (PublishConfigurationException e) {
			logger.info( "**************************************************************");
			logger.info( "publishLayer() returned: PublishConfigurationException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("PublishConfigurationException", ERROR_DEFINITION_MAP.get("PublishConfiguration")+e.getMessage());
		}
		catch (PublishGeneralException e) {
			logger.info( "**************************************************************");
			logger.info( "publishLayer() returned: PublishGeneralException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("PublishGeneralException", ERROR_DEFINITION_MAP.get("PublishGeneral")+e.getMessage());
		}
		catch (Exception e) {
			e.printStackTrace();
			logger.info( "**************************************************************");
			logger.info( "publishLayer() returned: PublishGeneralException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("PublishGeneralException", ERROR_DEFINITION_MAP.get("PublishGeneral")+e.getMessage());
		}
	}

	/*
	 * This method must catch all exceptions and wrap them into an XML envelope
	 */
	private String removeLayer( net.opengis.wps._1_0.Execute execute){

		Map<String, List<String>> paramMap = Utils.makeParamMap(execute);

		try{
			IDiffuserAdapter diffusorController = new GeoserverDiffuserControllerAdaptor();        
			String layerId = Utils.getParameter("LayerId", paramMap).get(0);
			logger.info("Layer id to remove: " + layerId );
			Layer layerFromWebTier = Layer.getLayerFromGUID( layerId );

			if( layerFromWebTier == null)
			{
				logger.info("No layer with guid found: " + layerId );
				throw new LayerNotFoundException("no layer with guid found" + layerId);
			}

			Diffuser myDiffuser = layerFromWebTier.getDiffuser();
			if( myDiffuser == null)
			{
				logger.warning("No diffuser found that corresponds to layer" + layerId);
				throw new DiffuserNotFoundException("No diffuser found that corresponds to layer" + layerId);
			}

			logger.info("Layer Name to remove: " + layerFromWebTier.getLayerName() );
			layerFromWebTier.delete();

			boolean res = diffusorController.removeLayer( myDiffuser, layerFromWebTier );
			logger.info("RemoveLayer DataTier Result: " + res );
			if( false == res){ return Utils.exceptiontoXML("LayerNotOnDiffusionServer", "" );}

			String ret = Utils.WPSHeader("removeLayer") +
			Utils.WPSResponseSectionDataInput( paramMap )+
			Utils.WPSResponseFiller("LayerId",  layerId)+
			Utils.WPSFooter();
			logger.info( "**************************************************************");
			logger.info( "removeLayer() returned: " +  ret);
			logger.info( "**************************************************************");
			return ret;
		}
		catch(DataInputException e){
			logger.info( "**************************************************************");
			logger.info( "publishLayer() returned: DataInputException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("DataInputException", ERROR_DEFINITION_MAP.get("DataInput")+ e.getMessage());
		}
		catch (PublicationException e){
			logger.info( "**************************************************************");
			logger.info( "removeLayer() returned: PublicationException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("PublicationException", ERROR_DEFINITION_MAP.get("Publication")+ e.getMessage());
		}
		catch (DiffuserException e) {
			logger.info( "**************************************************************");
			logger.info( "removeLayer() returned: DiffuserException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("DiffuserException", ERROR_DEFINITION_MAP.get("Diffuser")+e.getMessage());
		}
		catch (PublishConfigurationException e) {
			logger.info( "**************************************************************");
			logger.info( "removeLayer() returned: PublishConfigurationException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("PublishConfigurationException", ERROR_DEFINITION_MAP.get("PublishConfiguration")+e.getMessage());
		}
		catch (PublishGeneralException e) {
			logger.info( "**************************************************************");
			logger.info( "removeLayer() returned: PublishGeneralException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("PublishGeneralException", ERROR_DEFINITION_MAP.get("PublishGeneral")+e.getMessage());
		}
	}

	/*
	 * This method must catch all exceptions and wrap them into an XML envelope
	 */
	private String deleteFeatureSource( net.opengis.wps._1_0.Execute execute){
		try
		{
			//ITransformerAdapter transformer = null;
			Map<String, List<String>> paramMap = Utils.makeParamMap(execute);
			String fsId = Utils.getParameter("FeatureSourceId", paramMap).get(0);
			logger.info("Starting deletion of FeatureSource, guid: " + fsId );

			fsm.deleteFeatureSource(fsId);

			String ret = Utils.WPSHeader("deleteFeatureSource") +
			Utils.WPSResponseSectionDataInput( paramMap )+
			Utils.WPSResponseFiller("FeatureSourceId",  fsId)+
			Utils.WPSFooter();
			logger.info( "**************************************************************");
			logger.info( "deleteFeatureSource() returned: " +  ret);
			logger.info( "**************************************************************");
			return ret;
		}
		catch(DataInputException e){
			logger.info( "**************************************************************");
			logger.info( "publishLayer() returned: DataInputException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("DataInputException", ERROR_DEFINITION_MAP.get("DataInput")+ e.getMessage());
		}
		catch (FeatureSourceException e){
			logger.info( "**************************************************************");
			logger.info( "deleteFeatureSource() returned: FeatureSourceException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("FeatureSourceException", ERROR_DEFINITION_MAP.get("FeatureSource")+ e.getMessage());
		}
		catch (DiffuserException e) {
			logger.info( "**************************************************************");
			logger.info( "deleteFeatureSource() returned: DiffuserException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("DiffuserException", ERROR_DEFINITION_MAP.get("Diffuser")+e.getMessage());
		}
		catch (PublishConfigurationException e) {
			logger.info( "**************************************************************");
			logger.info( "deleteFeatureSource() returned: PublishConfigurationException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("PublishConfigurationException", ERROR_DEFINITION_MAP.get("PublishConfiguration")+e.getMessage());
		}
		catch (PublishGeneralException e) {
			logger.info( "**************************************************************");
			logger.info( "deleteFeatureSource() returned: PublishGeneralException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("PublishGeneralException", ERROR_DEFINITION_MAP.get("PublishGeneral")+e.getMessage());
		}
	}


	/**
	 * Possible Exceptions:
	 * 		FileNotFound
	 * 		DiffusorNotFound
	 * 		ScriptNotFound
	 * 		UnknownFormat
	 * 		UnknownScript
	 * 		PublisherError
	 * @param execute
	 * @return
	 */
	/*
	 * This method must catch all exceptions and wrap them into an XML envelope
	 */
	private String transformDataset( net.opengis.wps._1_0.Execute execute){
		try {
			String ret;
			Map<String, List<String> > paramMap = Utils.makeParamMap(execute);

			List<String> URLList = Utils.getParameter("URLFile", paramMap);
			String diffusorName = Utils.getParameter("diffusionServerName", paramMap).get(0);
			String scriptName = Utils.getParameter("scriptName", paramMap).get(0);
			String sourceDataType = Utils.getParameter("sourceDataType", paramMap).get(0);
			String featureSourceId = Utils.getParameter("FeatureSourceId", paramMap).get(0);
			String coordEpsgCode = Utils.getParameter("coordEpsgCode", paramMap).get(0);			
			String dataset = Utils.getParameter("dataset", paramMap).get(0);

			//Check the input values
			//this method returns nothing, but can throw a DataInputException if the checks fail
			//these errors are returned to the caller.
			InputValidator.transformDatasetValidator(featureSourceId, diffusorName, URLList,scriptName, sourceDataType,  coordEpsgCode, dataset);

			//check that this type of file can be handled by any transformer that has been plugged in
			TransformerHelper.getTransformerPlugIns();
			TransformerHelper.getFileTypeToTransformerAssociation();

			TransformDatasetResponse transfoResult = null;

			//call the transformer and handle exceptions, then dispatch the relevant to the GUI
			//transfoResult = fsm.manageDataset(scriptName, featureSourceId, diffusorName, URLList,scriptName, sourceDataType, coordEpsgCode);
			String response = fsm.manageDataset(scriptName, featureSourceId, diffusorName, URLList,scriptName, sourceDataType, coordEpsgCode, dataset);

			//BUILD the WPS Envelope
			ret = Utils.WPSHeader("transformDataset") +
			Utils.WPSResponseSectionDataInput( paramMap )+
			Utils.WPSResponseFiller("featuresourceGuid", response)+
			//Utils.WPSResponseFiller("Attribute", transfoResult.attributes)+
			Utils.WPSFooter();
			logger.info( "**************************************************************");
			logger.info( "transformDataset() returned: " +  ret);
			logger.info( "**************************************************************");
			return ret;
		}
		catch (DataInputException e){
			logger.info( "**************************************************************");
			logger.info( "transformDataset() returned: DataInputException:" +  e.getMessage());
			logger.info( "**************************************************************");
			return Utils.exceptiontoXML("DataInputException", ERROR_DEFINITION_MAP.get("DataInput")+ e.getMessage());
		}
	}

	public static void main(String args[]) throws Exception{

		try{
			URL url = new URL("http://localhost:8081/wps/WPSServlet");
			URLConnection conn = url.openConnection();
			conn.setDoOutput(true);
			OutputStreamWriter wr = new OutputStreamWriter(conn.getOutputStream());
			WPSServlet w = new WPSServlet();

			wr.write(w.getResourceAsString("executeGetOrders.xml"));
			wr.flush();


			FileOutputStream fos = new FileOutputStream(new File("C:\\output.xml"));

			// Get the response
			BufferedReader rd = new BufferedReader(new InputStreamReader(conn.getInputStream()));
			String line;
			while ((line = rd.readLine()) != null) {
				fos.write(line.getBytes());
				System.out.println(line);
			}
			fos.close();
			wr.close();
			rd.close();
		}catch(Exception e){
			e.printStackTrace();
		}

	}
	private String getResourceAsString(String resourceName){
		try{

			InputStream is = this.getClass().getResourceAsStream(resourceName);
			StringBuffer sb = new StringBuffer();

			BufferedReader in = new BufferedReader(new InputStreamReader(is));
			String input;

			while ((input = in.readLine()) != null) {
				sb.append (input);
			}
			return sb.toString();

		}catch(Exception e){
			e.printStackTrace();
		}
		return "";
	}
}
