/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 Remy Baud (remy.baud@asitvd.ch), Antoine Elbel (antoine@probel.eu)
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
package org.easysdi.publish.diffusion;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.StringWriter;
import java.io.UnsupportedEncodingException;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
//import java.util.logging.Logger;
import java.lang.Double;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Result;
import javax.xml.transform.Source;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.apache.commons.httpclient.Cookie;
import org.apache.commons.httpclient.Credentials;
import org.apache.commons.httpclient.DefaultHttpMethodRetryHandler;
import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.HttpException;
import org.apache.commons.httpclient.HttpMethod;
import org.apache.commons.httpclient.HttpStatus;
import org.apache.commons.httpclient.URI;
import org.apache.commons.httpclient.UsernamePasswordCredentials;
import org.apache.commons.httpclient.auth.AuthScope;
import org.apache.commons.httpclient.methods.DeleteMethod;
import org.apache.commons.httpclient.methods.GetMethod;
import org.apache.commons.httpclient.methods.PostMethod;
import org.apache.commons.httpclient.methods.PutMethod;
import org.apache.commons.httpclient.params.HttpMethodParams;
import org.deegree.services.controller.OGCFrontController;
import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.layer.FeatureSource;
import org.easysdi.publish.biz.layer.Layer;
import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.DiffuserException;
import org.easysdi.publish.exception.FeatureSourceException;
import org.easysdi.publish.exception.FeatureSourceNotFoundException;
import org.easysdi.publish.exception.LayerNotFoundException;
import org.easysdi.publish.exception.PublicationException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.PublishGeneralException;
import org.easysdi.publish.helper.Attribute;
import org.easysdi.publish.helper.IFeatureSourceInfo;
import org.easysdi.publish.helper.IHelper;
import org.easysdi.publish.security.CurrentUser;
//import org.geotools.data.postgis.PostgisDataStoreFactory;
import org.springframework.dao.DataAccessException;
import org.w3c.dom.DOMException;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

import com.eaio.uuid.UUID;



public class GeoserverDiffuserControllerAdapter extends CommonDiffuserControllerAdapter {

	// Create an instance of HttpClient.
	private HttpClient client = new HttpClient();
	private Credentials defaultcreds;
	private Map<String, String> PostContent = new HashMap<String, String>();
	private String layerGuid = "";
	private String m_executionPath = OGCFrontController.getInstance().getServletConfig().getServletContext().getRealPath("/");


	//static final String easySDIPublishNamespace = "easySDIPublishNamespace";
	static final String EASY_SDI_NAMESPACE ="easySDIPublishNamespace";
	static final String EASY_SDI_DATASTORE ="easySDIPublishDatastore";	
	static final String REST_PATH = "/rest/workspaces/"+EASY_SDI_NAMESPACE+"/datastores/"+EASY_SDI_DATASTORE+"/featuretypes/"; 
	final static String LOGIN_FORM = "/j_acegi_security_check";
	final static String NEW_FEATURE_TYPE_FORM = "/config/data/typeNewSubmit.do";
	final static String NEW_LAYER_FORM = "/config/data/typeEditorSubmit.do";
	final static String APPLY_FORM = "/admin/saveToGeoServer.do";
	final static String SAVE_FORM = "/admin/saveToXML.do";
	//final static String GEBASTELETE_URL = "http://admin:geoserver@localhost:8080/geoserver";
	//final static String GEBASTELETE_URL = "http://localhost:8080/geoserver";

	public GeoserverDiffuserControllerAdapter(){}

	private void authenticate( String URIstr )
	{
		//URI myURI = new URI(URIStr);
		URL myURL = null;
		try {
			myURL = new URL(URIstr);
		} catch (MalformedURLException e) {
			e.printStackTrace();
		}

		defaultcreds = new UsernamePasswordCredentials( username, passwd );
		client.getState().setCredentials(new AuthScope(myURL.getHost(), myURL.getPort(), AuthScope.ANY_REALM), defaultcreds);
		client.getParams().setAuthenticationPreemptive(true);
	}
	
	private void init(Diffuser diff) throws PublicationException, IOException, PublishConfigurationException{		
		//checking for the diffuser's namespace
		setCredentials(diff.getUser(), diff.getPwd());
		authenticate(diff.getUrl());
		GetMethod methodGetNs = new GetMethod( diff.getUrl()+"/rest/namespaces/"+CurrentUser.getCurrentPrincipal());
		super.getLogger().info("after methodGetNs");
		//methodWriteLayer.setRequestHeader("Content-type", "text/xml");
		ExecuteResponse response = execute(methodGetNs);
		if( response.getHttpCode() == 200 ){
			//Ok the namespace exist so we assume the user is correctly configured (workspace, datastore...).
			return;
		}else if( response.getHttpCode() == 404 ){
			//the namespace doesn't exist so we have to create it
			createWorkspace(diff);
			createDatastore(diff);
		}
		else if( response.getHttpCode() != 200 ){
		    throw new PublicationException( ((Integer)response.getHttpCode()).toString() );
		}
	}
	
	private void createWorkspace(Diffuser diff) throws IOException, PublicationException{
		//Create the workspace
		PostMethod createWorkspace = new PostMethod( diff.getUrl()+"/rest/workspaces");
		String body = "<workspace><name>"+CurrentUser.getCurrentPrincipal()+"</name></workspace>";
		InputStream is = new ByteArrayInputStream(body.getBytes("UTF-8"));
		createWorkspace.setRequestBody(is);
		is.close();
		createWorkspace.setRequestHeader("Content-type", "text/xml");
		ExecuteResponse response = execute(createWorkspace);
		//Expect code 201 created
		if( response.getHttpCode() != 201 && response.getHttpCode() != 200){		    	
		    throw new PublicationException( ((Integer)response.getHttpCode()).toString() );
		}
	}
	
	private void createDatastore(Diffuser diff) throws IOException, PublicationException, PublishConfigurationException{
		//Create the workspace
		PostMethod createDatastore = new PostMethod( diff.getUrl()+"/rest/workspaces/"+CurrentUser.getCurrentPrincipal()+"/datastores");
		//missing user and schema
		String body = 
		"<dataStore>" +
			"<name>"+CurrentUser.getCurrentPrincipal()+"_datastore</name>" +
			"<connectionParameters>" +
				"<host>"+diff.getGeodatabase().getDbHost()+"</host>" +
				"<port>"+diff.getGeodatabase().getDbPort()+"</port>" +
				"<database>"+CurrentUser.getCurrentPrincipal()+"</database>" +
				"<user>"+diff.getGeodatabase().getUser()+"</user>" +
				"<passwd>"+diff.getGeodatabase().getPwd()+"</passwd>" +
				"<schema>public</schema>" +
				"<dbtype>postgis</dbtype>" +
			"</connectionParameters>" +
		"</dataStore>";
		
		InputStream is = new ByteArrayInputStream(body.getBytes("UTF-8"));
		createDatastore.setRequestBody(is);
		is.close();
		createDatastore.setRequestHeader("Content-type", "text/xml");
		ExecuteResponse response = execute(createDatastore);
		//Expect code 201 created
		if( response.getHttpCode() != 201 && response.getHttpCode() != 200){		    	
		    throw new PublicationException( ((Integer)response.getHttpCode()).toString() );
		}
	}

	@Override
	public PublishLayerResponse publishLayer(String layerId, String featureTypeId,
			List<String> attributeAlias, String title, String name, String qualityArea,
			String keywordList, String abstr, String style) throws PublishGeneralException, FeatureSourceException, DiffuserException, PublicationException, PublishConfigurationException {

		//Suppress all logging from geotools or we will have 
		//deadlocks by using java reflection.
		//org.geotools.util.logging.Logging.getLogger("org.geotools").setLevel(java.util.logging.Level.OFF);
		try {
			logger.info("log1");
			boolean isUpdate = false;
			//Getting the diffuser of the featureSourceGuid
			//Throws a FeatureSourceNotFoundException if not found
			//get a reference on this Featuresource
			FeatureSource fs = FeatureSource.getFromGuid(featureTypeId);

			//Get a reference on the Diffuser of this Featuresource
			Diffuser diff = fs.getDiffuser();
			logger.info("log2");
			//Get the layer: if layerId = "none", create a new one, else update existing
			if(layerId.equals("none")){
				//check that the current user has a namespace and a properly configured
				//environment to publish.
				init(diff);
				UUID uuid = new UUID();
				layerGuid = uuid.toString();
				logger.info("A new Layer is created ID: "+layerGuid);
			}
			//we keep the existing layerId
			else{
				layerGuid = layerId;
				logger.info("An existing Layer is updated: "+layerGuid);
				Layer tmp = Layer.getFromGuid( layerGuid );
				if(null == tmp)
				{
					logger.warning("No layer corresponding to " + layerGuid);
					throw new LayerNotFoundException("No layer corresponding to " + layerGuid);
				}
				isUpdate = true;
			}
			logger.info("log3");
			//super.publishLayer( diff, layer);
			super.getLogger().info("Starting Publication with:");
			super.getLogger().info("title: " + title);
			setCredentials(diff.getUser(), diff.getPwd() );
			super.getLogger().info("diffuser: " + diff.getName());
			Geodatabase geoDb = diff.getGeodatabase();
			super.getLogger().info("geodb: " + geoDb.getName());
			super.getLogger().info("dbtype: " + geoDb.getGeodatabaseTypeId());
			//super.getLogger().info("host: " + dbhost );
			//super.getLogger().info("port: " + new Integer(dbport).toString() );
			super.getLogger().info("schema: " + geoDb.getScheme() );
			super.getLogger().info("feature Source name: " + fs.getTableName() );
			super.getLogger().info("feature Source creation date: " + fs.getCreationDate());

			String strDbTypeClass = geoDb.getGeodatabaseType().getName().substring(0, 1).toUpperCase() +  geoDb.getGeodatabaseType().getName().substring(1);
			//get the helper accordingly to the geodatabase and Enforce the coordinate system supplied.
			IHelper helper = geoDb.getHelper();
			geoDb.setUrl(geoDb.getUrl()+"/"+CurrentUser.getCurrentPrincipal());
			//helper = (IHelper) Class.forName("org.easysdi.publish."+geoDb.getGeodatabaseType().getName()+"helper."+strDbTypeClass+"Helper").newInstance();	
			//helper.setGeodatabase(geoDb);

			//Now set the attribute aliases
			//construct the map: (AttrXY=AttrXYAlias)
			Map<String,String> mpColumns = new HashMap<String,String>(); 

			for(String row : attributeAlias){
				String[] temp = row.split("=");
				//skip if no alias set
				if(temp.length < 2){
					super.getLogger().info("no alias to set for: "+row);
					continue;
				}
				if(temp[1].equals("")){
					super.getLogger().info("alias is empty for: "+row);
					continue;
				}
				//skip if same name
				if(temp[0].equals(temp[1])){
					super.getLogger().info("column and alias ar the same for: "+row);
					continue;
				}

				super.getLogger().info("adding:"+row+" detail:"+temp[0]+"="+temp[1]);
				mpColumns.put(temp[0], temp[1]);
			}

			super.getLogger().info("setting attribute aliases");

			if(mpColumns.size() > 0){
				super.getLogger().info("Setting aliases..");
				helper.setAttributeAliases(fs.getTableName(), mpColumns);
				super.getLogger().info("Aliases has been set");
			}else{
				super.getLogger().info("No alias to set");
			}	

			//Helper Class that fetches Info about feature types in the database
			IFeatureSourceInfo fsi = null;

			//Get a class instance of the class to instanciate.
			Class cl=Class.forName("org.easysdi.publish."+geoDb.getGeodatabaseType().getName()+"helper."+strDbTypeClass+"FeatureSourceInfo");
			//PostgisFeatureSourceInfo(Geodatabase geoDb, String table)
			//Get a class instance for the method signature. Here void main(String[])
			Class arg1type = Geodatabase.class;
			Class arg2type = String.class;
			Constructor constructor = cl.getConstructor(arg1type, arg2type);
			fsi = (IFeatureSourceInfo)constructor.newInstance((Object)geoDb, (Object)fs.getTableName());

			super.getLogger().info("FeatureSourceInfo: " + fsi.getGeometry());

			InputStream is = null;
			InputStream inpStrForHttp = null;
			DocumentBuilder builder = null;
			Document doc = null;

			authenticate( diff.getUrl() );

			//remove the layer first 
			if(isUpdate){
				Layer tmp = Layer.getFromGuid( layerGuid );
				removeLayer(diff, tmp);
			}

			//
			//create a new style for the layer if new
			//
			if(layerId.equals("none")){
				//String newStyle = name+"_"+layerGuid;
				String newStyle = CurrentUser.getCurrentPrincipal()+"_"+name;
				//Add a default style to the layer
				PostMethod methodAddStyle = null;
				//Post a default style for the layer
				methodAddStyle = new PostMethod( diff.getUrl()+"/rest/styles");
				//String geom = fsi.getGeometry();
				//super.getLogger().info("Layer geom is "+geom);

				if(style.equalsIgnoreCase("Polygon")){
					is = new FileInputStream(new File(m_executionPath+"WEB-INF/conf/publish/polygon.xml"));
					//is = this.getClass().getResourceAsStream("polygon.xml");
					super.getLogger().info("Style for geom:"+style+" is polygon.xml");
				}else if(style.equalsIgnoreCase("line")){
					//is = this.getClass().getResourceAsStream("line.xml");
					is = new FileInputStream(new File(m_executionPath+"WEB-INF/conf/publish/line.xml"));
					super.getLogger().info("Style for geom:"+style+" is line.xml");
				}else if(style.equalsIgnoreCase("Point")){
					is = new FileInputStream(new File(m_executionPath+"WEB-INF/conf/publish/point.xml"));
					//is = this.getClass().getResourceAsStream("point.xml");
					super.getLogger().info("Style for geom:"+style+" is point.xml");
				}else{
					throw new PublicationException("The layer geometry:"+style+" has not been recognize.");
				}

				DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
				factory.setNamespaceAware(true);  
				builder = factory.newDocumentBuilder();
				doc = builder.parse(is);
				is.close();
				inpStrForHttp = replaceHotTagsValueInSldTemplate( doc, newStyle);

				methodAddStyle.setRequestBody( inpStrForHttp );
				methodAddStyle.setRequestHeader("Content-type", "application/vnd.ogc.sld+xml");
				ExecuteResponse response = execute(methodAddStyle);
				if( response.getHttpCode() != 200 && response.getHttpCode() != 201 ){		    	
				    throw new PublicationException( ((Integer)response.getHttpCode()).toString() );
				}
			}

			//
			//Create the layer
			//
			//is = this.getClass().getResourceAsStream("publishTemplate.xml");
			is = new FileInputStream(new File(m_executionPath+"WEB-INF/conf/publish/publishTemplate.xml"));

			builder = DocumentBuilderFactory.newInstance().newDocumentBuilder();
			doc = builder.parse(is);
			is.close();

			//stuff attributes in the map that will be posted.
			inpStrForHttp = replaceHotTagsValueInLayerTemplate( doc, title, name, keywordList, abstr, fsi);

			PostMethod methodWriteLayer = null;
			//Post the layer
			methodWriteLayer = new PostMethod( diff.getUrl()+"/rest/workspaces/"+CurrentUser.getCurrentPrincipal()+"/datastores/"+CurrentUser.getCurrentPrincipal()+"_datastore/featuretypes");
			super.getLogger().info("post layer to:"+diff.getUrl()+"/rest/workspaces/"+CurrentUser.getCurrentPrincipal()+"/datastores/"+CurrentUser.getCurrentPrincipal()+"_datastore/featuretypes");
			super.getLogger().info("after methodWriteLayer");
			// Send any XML file as the body of the POST request
			//methodWriteLayer.setRequestBody( this.getClass().getResourceAsStream("publishTemplate.xml"));
			methodWriteLayer.setRequestBody( inpStrForHttp );
			super.getLogger().info("after setRequestBody");
			methodWriteLayer.setRequestHeader("Content-type", "text/xml");

			super.getLogger().info("methodWriteLayer:   "   );
			ExecuteResponse response = execute(methodWriteLayer);
			if( response.getHttpCode() != 200 &&  response.getHttpCode() != 201 ){		    	
			    throw new PublicationException( ((Integer)response.getHttpCode()).toString() );
			}

			//Apply style to the layer if new layer
			if(layerId.equals("none")){
				String newStyle = CurrentUser.getCurrentPrincipal()+"_"+name;
				//String newStyle = name+"_"+layerGuid;
				PutMethod methodApplyStyle = new PutMethod( diff.getUrl()+"/rest/layers/"+CurrentUser.getCurrentPrincipal()+":"+name);
				String body = "<layer><enabled>true</enabled><defaultStyle><name>"+newStyle+"</name><atom:link rel=\"alternate\" href=\"http://localhost:8080/geoserver/rest/styles/"+newStyle+".xml\" type=\"application/xml\"/></defaultStyle></layer>";				
				is = new ByteArrayInputStream(body.getBytes("UTF-8"));
				methodApplyStyle.setRequestBody(is);
				is.close();
				methodApplyStyle.setRequestHeader("Content-type", "text/xml");
				response = execute(methodApplyStyle);
				if( response.getHttpCode() != 200 ){		    	
				    throw new PublicationException( ((Integer)response.getHttpCode()).toString() );
				}
			}

			//OK layer created; save it persistently
			Layer layer;
			if(!layerId.equals("none")){
				//existing Layer, retrieve id from Guid
				layer = Layer.getFromGuid(layerId);
				if( null == layer )
				{
					logger.warning("No layer correspondin to " + layerGuid);
					throw new FeatureSourceNotFoundException("No layer corresponding to " + layerGuid);
				}
			}
			else{
				layer = new Layer();
			}
			//assign new values
			layer.setFeatureSource(fs);
			layer.setDescription(abstr);
			layer.setGuid(layerGuid);
			layer.setKeywordList(keywordList);
			layer.setName(name);
			layer.setTitle(title);
			// TODO what about the Layer status?
			layer.setStatus("PUBLISHED");
			layer.set_abstract(abstr);
			layer.setStyle(style);
			layer.setQuality_area(qualityArea);
			Calendar cal = Calendar.getInstance();
			//Set creation date only if new
			if(layerId.equals("none")){
				layer.setCreationDate(cal);
			}

			layer.setUpdateDate(cal);

			try{
				layer.persist();
			}catch(DataAccessException e){
				System.out.println("Error occured, cause:"+e.getCause() +" message:"+ e.getMessage());
				throw new DataInputException("Persist failed, error when saving layer: "+e.getCause() +" message:"+ e.getMessage());
			}

			super.getLogger().info("Storing Layer: " + layer.getName() + " id: " + layer.getGuid() );

			PublishLayerResponse resp = new PublishLayerResponse();

			resp.layerGuid = layerGuid;
			resp.endPointsTypes.add("WMS_URL");
			resp.endPoints.add( diff.getUrl()+ "/" + CurrentUser.getCurrentPrincipal()+"/wms?layers="+EASY_SDI_NAMESPACE+":"+layer.getName());
			resp.endPointsTypes.add("WFS_URL");
			resp.endPoints.add( diff.getUrl()+ "/" + CurrentUser.getCurrentPrincipal()+"/wfs?layers="+EASY_SDI_NAMESPACE+":"+layer.getName());
			resp.endPointsTypes.add("KML_URL");
			resp.endPoints.add( diff.getUrl()+ "/" + CurrentUser.getCurrentPrincipal()+"/wms/kml?layers="+EASY_SDI_NAMESPACE+":"+layer.getName());
			resp.bboxTypes.add("MinX");
			resp.bbox.add(fsi.getBbox().get("MinX").toString());
			resp.bboxTypes.add("MinY");
			resp.bbox.add(fsi.getBbox().get("MinY").toString());
			resp.bboxTypes.add("MaxX");
			resp.bbox.add(fsi.getBbox().get("MaxX").toString());
			resp.bboxTypes.add("MaxY");
			resp.bbox.add(fsi.getBbox().get("MaxY").toString());

			return resp;

		} catch (SecurityException e) {
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublishConfigurationException(e.getMessage());
		} catch (NoSuchMethodException e) {
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublishConfigurationException(e.getMessage());
		} catch (InstantiationException e) {
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublishConfigurationException(e.getMessage());
		} catch (InvocationTargetException e) {
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublishConfigurationException(e.getMessage());
		} catch (IllegalAccessException e) {
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublishConfigurationException(e.getMessage());
		} catch (ClassNotFoundException e) {
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublishConfigurationException(e.getMessage());
		} catch (ParserConfigurationException e) {
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublishConfigurationException(e.getMessage());
		} catch (SAXException e) {
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublishConfigurationException(e.getMessage());
		}
		//During the publication, the feature source relative to the layer was not found
		catch(LayerNotFoundException e){
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublicationException(e.getMessage());
		}//During the publication, the feature source relative to the layer was not found
		catch(FeatureSourceNotFoundException e){
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublicationException(e.getMessage());
		}
		catch (IOException e) {
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublicationException(e.getMessage());
		}catch (Exception e) {
			e.printStackTrace();
			super.getLogger().warning(e.getMessage() );
			throw new PublicationException(e.getMessage());
		}

	}

	/*TODO: improve by making only execute, but not handle exception */
	private ExecuteResponse execute(HttpMethod method) throws PublicationException {
		Integer returnCode = null;
		ExecuteResponse response = new ExecuteResponse();
		BufferedReader br = null;
		try{
			returnCode = client.executeMethod(method);
			response.setHttpCode(returnCode);
			
			//!!!!!! this is new, maybe will bug
			response.setBody(method.getResponseBodyAsString());

			if(returnCode == HttpStatus.SC_NOT_IMPLEMENTED) {
				super.getLogger().warning("The Post method is not implemented by the server at URI " );
				System.err.println("The Post method is not implemented by this URI");
				// still consume the response body
				method.getResponseBodyAsString();
				throw new PublicationException( "The Post method is not implemented by this URI" );
			} else {
				super.getLogger().info("URI: " + method.getURI() );
				super.getLogger().info("StatusLine: " + method.getStatusLine() );	    	  

				//Log if not OK
				if( returnCode != 200 ){		    	
					br = new BufferedReader(new InputStreamReader(method.getResponseBodyAsStream()));
					String readLine;
					StringBuffer sb = new StringBuffer();
					while(((readLine = br.readLine()) != null)) {
						super.getLogger().info(readLine);
						sb.append(readLine);
						//throw new PublicationException( ((Integer)returnCode).toString() );
					}
					response.setMessage(sb.toString());
				}
			}
		} catch (Exception e) {
			super.getLogger().warning( e.getMessage() );
			e.printStackTrace();
			throw new PublicationException( e.getMessage() );
		} finally {
			method.releaseConnection();
			if(br != null) try { br.close(); } catch (Exception fe) {}
		}
		return response;
	}

	@Override
	public boolean removeLayer(Diffuser diff, Layer layer) throws PublishGeneralException, DiffuserException, PublicationException, PublishConfigurationException {
		boolean success = true;
		
		//PutMethod methodApplyStyle = new PutMethod( diff.getUrl()+"/rest/layers/easySDIPublishNamespace:"+name);
		//static final String REST_PATH = "/rest/workspaces/"+EASY_SDI_NAMESPACE+"/datastores/"+EASY_SDI_DATASTORE+"/featuretypes/"; 

		//
		//delete the layer associated to the featuretype
		//
		String LayerRestURL = diff.getUrl() + "/rest/layers/"+CurrentUser.getCurrentPrincipal()+":" + layer.getName() + ".xml";
		setCredentials(diff.getUser(), diff.getPwd() );
		super.getLogger().info("diffuser: " + diff.getName());
		authenticate(LayerRestURL);
		super.removeLayer( diff, layer );
		// Create a method instance.
		DeleteMethod method = new DeleteMethod(LayerRestURL);
		// Provide custom retry handler is necessary
		method.getParams().setParameter(HttpMethodParams.RETRY_HANDLER, 
				new DefaultHttpMethodRetryHandler(3, false));
		try {
			// Execute the method.
			int statusCode = client.executeMethod(method);
			if (statusCode != HttpStatus.SC_OK) {
				//System.err.println("Method failed: " + method.getStatusLine());
				super.logger.warning("Method failed: " + method.getStatusLine());
				success = false;
				throw new PublicationException( ((Integer)statusCode).toString() );
			}
			// Read the response body.
			byte[] responseBody = method.getResponseBody();
			// Deal with the response.
			super.getLogger().fine( new String(responseBody));
		} catch (HttpException e) {
			success = false;
			//System.err.println("Fatal protocol violation: " + e.getMessage());
			super.logger.warning("Fatal protocol violation: " + e.getMessage());
			throw new PublicationException( e.getMessage() );
		} catch (IOException e) {
			success = false;
			super.logger.warning("Fatal transport error: " + e.getMessage());
			//System.err.println("Fatal transport error: " + e.getMessage());
			throw new PublicationException( e.getMessage() );
		} finally {
			// Release the connection.
			method.releaseConnection();
		}  

		//
		//delete the featureType associated to the previous deleted layer
		//
		LayerRestURL = diff.getUrl() + "/rest/workspaces/"+CurrentUser.getCurrentPrincipal()+"/datastores/"+CurrentUser.getCurrentPrincipal()+"_datastore/featuretypes/" + layer.getName() + ".xml";
		setCredentials(diff.getUser(), diff.getPwd() );
		super.getLogger().info("diffuser: " + diff.getName());
		authenticate(LayerRestURL);
		super.removeLayer( diff, layer );
		// Create a method instance.
		method = new DeleteMethod(LayerRestURL);
		// Provide custom retry handler is necessary
		method.getParams().setParameter(HttpMethodParams.RETRY_HANDLER, 
				new DefaultHttpMethodRetryHandler(3, false));
		try {
			// Execute the method.
			int statusCode = client.executeMethod(method);
			if (statusCode != HttpStatus.SC_OK) {
				//System.err.println("Method failed: " + method.getStatusLine());
				super.logger.warning("Method failed: " + method.getStatusLine());
				success = false;
				throw new PublicationException( ((Integer)statusCode).toString() );
			}
			// Read the response body.
			byte[] responseBody = method.getResponseBody();
			// Deal with the response.
			super.getLogger().fine( new String(responseBody));
		} catch (HttpException e) {
			success = false;
			//System.err.println("Fatal protocol violation: " + e.getMessage());
			super.logger.warning("Fatal protocol violation: " + e.getMessage());
			throw new PublicationException( e.getMessage() );
		} catch (IOException e) {
			success = false;
			super.logger.warning("Fatal transport error: " + e.getMessage());
			//System.err.println("Fatal transport error: " + e.getMessage());
			throw new PublicationException( e.getMessage() );
		} finally {
			// Release the connection.
			method.releaseConnection();
		}
		
		return success;
	}

	public static String xmlToString(Node node) {
		try {
			Source source = new DOMSource(node);
			StringWriter stringWriter = new StringWriter();
			Result result = new StreamResult(stringWriter);
			TransformerFactory factory = TransformerFactory.newInstance();
			Transformer transformer = factory.newTransformer();
			transformer.transform(source, result);
			return stringWriter.getBuffer().toString();
		} catch (TransformerConfigurationException e) {
			e.printStackTrace();
		} catch (TransformerException e) {
			e.printStackTrace();
		}
		return null;
	}

	private InputStream replaceHotTagsValueInSldTemplate( Document doc, String sldName ) throws DOMException, UnsupportedEncodingException
	{		
		Element rootElement = doc.getDocumentElement();
		String sldNS = "http://www.opengis.net/sld";

		//edit sldName
		NodeList namedLayer = rootElement.getElementsByTagNameNS(sldNS, "Name");
		Node n = namedLayer.item(0);
		replaceNodeVal( n, sldName );

		display( namedLayer );

		InputStream is = new ByteArrayInputStream(xmlToString( rootElement ).getBytes());
		//snippets that dumps in a file what is being sent to the rest interface of geoserver
		try {
			String tempFile = System.getProperty("java.io.tmpdir")+"/publishLayerPublishedSldDump.xml";
			logger.info("Temp file dir is: " + tempFile);
			OutputStream out = new FileOutputStream(new File(tempFile));

			byte buf[]=new byte[1024];
			int len;
			while((len=is.read(buf))>0)
				out.write(buf,0,len);
			out.close();
			logger.info("Temp file successfully wrote and closed...");
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return new ByteArrayInputStream(xmlToString( rootElement ).getBytes());					
	}

	private InputStream replaceHotTagsValueInLayerTemplate( Document doc, String title, String name, String keywordList, String abstr, IFeatureSourceInfo fsi ) throws DOMException, UnsupportedEncodingException
	{
		//super.getLogger().info("FeatureSourceInfo Geo: " + fsi.getGeometry());
		//super.getLogger().info("FeatureSourceInfo CRS: " + fsi.getCrsCode());
		//super.getLogger().info("FeatureSourceInfo Table: " + fsi.getTable());		
		//super.getLogger().info("FeatureSourceInfo Table: " + fsi.getCrsWkt());		
		HashMap<String, Double> mapBoundingBox =  fsi.getBbox();

		Element rootElement = doc.getDocumentElement();

		//nativeName is the postgis table
		NodeList listOfNativeName = doc.getElementsByTagName("nativeName");        
		replaceNodeVal( listOfNativeName.item(0), fsi.getTable() );
		display( listOfNativeName );


		NodeList listOfNativeCRS = doc.getElementsByTagName("nativeCRS");        
		replaceNodeVal( listOfNativeCRS.item(0), fsi.getCrsWkt() );
		//display( listOfNativeCRS );

		NodeList listOfCRS = doc.getElementsByTagName("srs");        
		replaceNodeVal( listOfCRS.item(0), fsi.getCrsCode() );
		//display( listOfCRS );

		NodeList listOfBoundingBoxMinX = doc.getElementsByTagName("minx");        
		replaceNodeVal( listOfBoundingBoxMinX.item(0), mapBoundingBox.get("MinX_native").toString() );
		replaceNodeVal( listOfBoundingBoxMinX.item(1), mapBoundingBox.get("MinX").toString() );
		//display( listOfBoundingBoxMinX );

		NodeList listOfBoundingBoxMaxX = doc.getElementsByTagName("maxx");        
		replaceNodeVal( listOfBoundingBoxMaxX.item(0), mapBoundingBox.get("MaxX_native").toString() );
		replaceNodeVal( listOfBoundingBoxMaxX.item(1), mapBoundingBox.get("MaxX").toString() );
		//display( listOfBoundingBoxMaxX );

		NodeList listOfBoundingBoxMinY = doc.getElementsByTagName("miny");        
		replaceNodeVal( listOfBoundingBoxMinY.item(0), mapBoundingBox.get("MinY_native").toString() );
		replaceNodeVal( listOfBoundingBoxMinY.item(1), mapBoundingBox.get("MinY").toString() );
		//display( listOfBoundingBoxMinY );

		NodeList listOfBoundingBoxMaxY = doc.getElementsByTagName("maxy");        
		replaceNodeVal( listOfBoundingBoxMaxY.item(0), mapBoundingBox.get("MaxY_native").toString() );
		replaceNodeVal( listOfBoundingBoxMaxY.item(1), mapBoundingBox.get("MaxY").toString() );
		//display( listOfBoundingBoxMaxY );

		NodeList listOfAbstract = doc.getElementsByTagName("abstract");
		replaceNodeVal( listOfAbstract.item(0), abstr );
		//display( listOfAbstract );

		//TODO Should follow the pattern <keywords><string/></keywords>
		NodeList listOfKeywords = doc.getElementsByTagName("string");
		replaceNodeVal( listOfKeywords.item(0), keywordList );
		//display( listOfKeywords );

		NodeList listOfName = doc.getElementsByTagName("name");
		replaceNodeVal( listOfName.item(0), name);
		//display( listOfName );

		NodeList listOfTitle = doc.getElementsByTagName("title");
		replaceNodeVal( listOfTitle.item(0), title);
		display( listOfTitle );

		//insert the "shema" attribute list in the dom
		NodeList listOfNativeAttributes = doc.getElementsByTagName("Attributes");        
		List<Attribute>lAttri = fsi.getAtrList();
		display( listOfNativeAttributes ); 
		Element	nAttributes = (Element)doc.createElement("attributes");
		for( Attribute attr: lAttri){
			Element	nAttri = (Element)doc.createElement("attribute");
			Element	nName = (Element)doc.createElement("name");
			//take care to read in the good charset for latin accent.
			nName.setTextContent( new String(attr.getName().getBytes("UTF-8"), java.nio.charset.Charset.defaultCharset().name()));
			Element	nMinOcc = (Element)doc.createElement("minOccurs");
			nMinOcc.setTextContent("0");
			Element	nMaxOcc = (Element)doc.createElement("maxOccurs");
			nMaxOcc.setTextContent("1");
			Element	nNillable = (Element)doc.createElement("nillable");
			nNillable.setTextContent("true");
			nAttri.appendChild(nName);
			nAttri.appendChild(nMinOcc);
			nAttri.appendChild(nMaxOcc);
			nAttri.appendChild(nNillable);
			nAttributes.appendChild(nAttri);
			//listOfNativeAttributes.item(0).appendChild(nAttri);
		}
		rootElement.appendChild(nAttributes);


		String dbg = xmlToString( rootElement );
		InputStream is = new ByteArrayInputStream(xmlToString( rootElement ).getBytes());

		//snippets that dumps in a file what is being sent to the rest interface of geoserver
		try {
			String tempFile = System.getProperty("java.io.tmpdir")+"/publishLayerPublishedLayerDump.xml";
			logger.info("Temp file dir is: " + tempFile);
			//OutputStream	out = new FileOutputStream(new File("c:\\temp\\publishLayerPublishedLayerDump.xml"));
			OutputStream	out = new FileOutputStream(new File(tempFile));

			byte buf[]=new byte[1024];
			int len;
			while((len=is.read(buf))>0)
				out.write(buf,0,len);
			out.close();
			logger.info("Temp file successfully wrote and closed...");
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return new ByteArrayInputStream(xmlToString( rootElement ).getBytes());					
	}


	void display( NodeList l)
	{
		for(int i = 0; i < l.getLength(); ++i)
		{
			Element el = (Element) ((Node)l.item(i));
			//String str = ((Node)l.item(0)).getNodeValue();
			String str = el.getChildNodes().item(0).getNodeValue();
			super.getLogger().info( str );
		}

	}

	void replaceNodeVal( Node n, String str)
	{
		Element el = (Element) n;
		el.getChildNodes().item(0).setNodeValue( str );    
	}

}
