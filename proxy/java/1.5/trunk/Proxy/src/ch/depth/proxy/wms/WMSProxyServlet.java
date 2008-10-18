/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
package ch.depth.proxy.wms;

import java.awt.AlphaComposite;
import java.awt.Color;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.RenderingHints;
import java.awt.geom.Rectangle2D;
import java.awt.image.BufferedImage;
import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URL;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Collections;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.UUID;
import java.util.Vector;
import java.util.logging.Level;

import javax.imageio.ImageIO;
import javax.imageio.ImageWriter;
import javax.imageio.stream.FileImageOutputStream;
import javax.imageio.stream.ImageOutputStream;
import javax.media.jai.GeometricOpImage;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.apache.xerces.parsers.XMLParser;
import org.geotools.data.WorldFileReader;
import org.geotools.data.memory.MemoryDataStore;
import org.geotools.data.memory.MemoryFeatureCollection;
import org.geotools.data.ows.CRSEnvelope;
import org.geotools.data.ows.FeatureSetDescription;
import org.geotools.data.ows.Layer;
import org.geotools.data.ows.WMSCapabilities;
import org.geotools.data.wms.WebMapServer;
import org.geotools.data.wms.request.GetMapRequest;
import org.geotools.data.wms.response.GetMapResponse;
import org.geotools.data.wms.xml.WMSSchema;
import org.geotools.factory.GeoTools;
import org.geotools.factory.Hints;
import org.geotools.feature.AttributeType;
import org.geotools.feature.AttributeTypeFactory;
import org.geotools.feature.FeatureCollection;
import org.geotools.feature.FeatureType;
import org.geotools.feature.FeatureTypes;
import org.geotools.feature.GeometryAttributeType;
import org.geotools.feature.type.GeometricAttributeType;
import org.geotools.gce.image.WorldImageReader;
import org.geotools.geometry.jts.GeometryCoordinateSequenceTransformer;
import org.geotools.geometry.jts.JTS;
import org.geotools.geometry.jts.ReferencedEnvelope;
import org.geotools.gml.producer.GeometryTransformer;
import org.geotools.gml2.bindings.GML2ParsingUtils;
import org.geotools.image.ImageWorker;
import org.geotools.map.DefaultMapContext;
import org.geotools.map.MapContext;
import org.geotools.referencing.CRS;
import org.geotools.referencing.FactoryFinder;
import org.geotools.referencing.NamedIdentifier;
import org.geotools.referencing.crs.DefaultGeographicCRS;
import org.geotools.referencing.factory.OrderedAxisAuthorityFactory;
import org.geotools.referencing.operation.DefaultMathTransformFactory;
import org.geotools.renderer.lite.RendererUtilities;
import org.geotools.util.Converter;
import org.geotools.util.GeometryConverterFactory;
import org.geotools.xml.DocumentFactory;
import org.geotools.xml.DocumentWriter;
import org.geotools.xml.gml.GMLFeatureCollection;
import org.geotools.xml.handlers.DocumentHandler;
import org.geotools.xml.wfs.WFSSchema;
import org.integratedmodelling.geospace.gis.FeatureRasterizer;
import org.opengis.referencing.crs.CRSAuthorityFactory;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.operation.CoordinateOperation;
import org.opengis.referencing.operation.CoordinateOperationFactory;
import org.opengis.referencing.operation.MathTransform;
import org.opengis.referencing.operation.MathTransformFactory;
import org.w3c.dom.Document;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.ls.LSParser;
import org.w3c.dom.ls.LSSerializer;
import org.xml.sax.EntityResolver;
import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import com.vividsolutions.jts.geom.Coordinate;
import com.vividsolutions.jts.geom.Envelope;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.geom.GeometryFactory;
import com.vividsolutions.jts.geom.IntersectionMatrix;
import com.vividsolutions.jts.geom.LineString;
import com.vividsolutions.jts.geom.LinearRing;
import com.vividsolutions.jts.geom.Point;
import com.vividsolutions.jts.geom.Polygon;
import com.vividsolutions.jts.geom.PrecisionModel;
import com.vividsolutions.jts.io.WKTReader;

import ch.depth.proxy.core.ProxyServlet;
import ch.depth.proxy.policy.Operation;
import ch.depth.xml.documents.RemoteServerInfo;
import ch.depth.xml.handler.PolicyHandler;
import ch.depth.xml.resolver.ResourceResolver;

/**
 * If no xslt is found in the path, generate the default one that will change the IP address and remove the wrong operation  
 * @author rmi
 */
public class WMSProxyServlet extends ProxyServlet {
    private String layers;
    private String styles;

    protected StringBuffer buildCapabilitiesXSLT(HttpServletRequest req,int remoteServerIndex){

	try {
	    String user="";
	    if(req.getUserPrincipal() != null){
		user= req.getUserPrincipal().getName();
	    }

	    String url = getServletUrl(req);

	    try {		
		StringBuffer WMSCapabilities111 = new StringBuffer ();		


		WMSCapabilities111.append("<xsl:stylesheet version=\"1.00\" xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");


		WMSCapabilities111.append("<xsl:template match=\"OnlineResource\">");
		WMSCapabilities111.append("<OnlineResource>"); 
		WMSCapabilities111.append("<xsl:attribute name=\"xlink:href\">");

		WMSCapabilities111.append(url);				

		WMSCapabilities111.append("</xsl:attribute>");
		WMSCapabilities111.append("</OnlineResource>");
		WMSCapabilities111.append("</xsl:template>");

		if(hasPolicy){

		    if (!policy.getOperations().isAll()){

			List<Operation> operationList =policy.getOperations().getOperation();  
			for (int i=0;i<operationList.size();i++){

			    if (operationList.get(i).getName() !=null){
				WMSCapabilities111.append("<xsl:template match=\"Capability/Request/");

				WMSCapabilities111.append(operationList.get(i).getName());
				WMSCapabilities111.append("\">");
				WMSCapabilities111.append("<!-- Copy the current node -->");
				WMSCapabilities111.append("<xsl:copy>");
				WMSCapabilities111.append("<!-- Including any attributes it has and any child nodes -->");
				WMSCapabilities111.append("<xsl:apply-templates select=\"@*|node()\"/>");
				WMSCapabilities111.append("</xsl:copy>");

				WMSCapabilities111.append("</xsl:template>");
			    }							
			}
		    }
		}
		Map hints = new HashMap();		
		//hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);
		hints.put(DocumentHandler.DEFAULT_NAMESPACE_HINT_KEY, WMSSchema.getInstance());
		hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);

		WMSCapabilities capa  = (WMSCapabilities)DocumentFactory.getInstance(new File(filePathList.get(remoteServerIndex)).toURI(),hints,Level.WARNING);	


		if(hasPolicy){
		    Iterator<Layer> itLayer = capa.getLayerList().iterator();

		    while(itLayer.hasNext()){
			Layer l = (Layer)itLayer.next();

			if (!isLayerAllowed(l.getName(), getRemoteServerUrl(remoteServerIndex))){
			    //Si couche pas permise alors on l'enlève
			    WMSCapabilities111.append("<xsl:template match=\"//Layer[starts-with(Name,'"+l.getName()+"')]");
			    WMSCapabilities111.append("\"></xsl:template>");		    
			}
		    }		
		}

		//Add the WMSxx_ Prefix before the name of the layer. 
		//This prefix will be used to find to witch remote server the layer belongs.
		if (getRemoteServerInfo(remoteServerIndex).getPrefix().length()>0){
		    WMSCapabilities111.append("<xsl:template match=\"//Layer/Name\">");
		    WMSCapabilities111.append("<Name>"+getRemoteServerInfo(remoteServerIndex).getPrefix()+"<xsl:value-of select=\".\"/> </Name>");
		    WMSCapabilities111.append("</xsl:template>");
		}

		WMSCapabilities111.append("  <!-- Whenever you match any node or any attribute -->");
		WMSCapabilities111.append("<xsl:template match=\"node()|@*\">");
		WMSCapabilities111.append("<!-- Copy the current node -->");
		WMSCapabilities111.append("<xsl:copy>");
		WMSCapabilities111.append("<!-- Including any attributes it has and any child nodes -->");
		WMSCapabilities111.append("<xsl:apply-templates select=\"@*|node()\"/>");
		WMSCapabilities111.append("</xsl:copy>");
		WMSCapabilities111.append("</xsl:template>");
		WMSCapabilities111.append("</xsl:stylesheet>");	

		return WMSCapabilities111;
	    } catch (Exception e) {
		// TODO Auto-generated catch block
		e.printStackTrace();
		dump("ERROR",e.getMessage());
	    }

	    //If something goes wrong, an empty stylesheet is returned.	
	    StringBuffer sb = new StringBuffer();		
	    return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");


	} catch (Exception e) {
	    // TODO Auto-generated catch block
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}

	//If something goes wrong, an empty stylesheet is returned.	
	StringBuffer sb = new StringBuffer();		
	return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>"); 
    }


    public void transform(String version, String currentOperation , HttpServletRequest req,HttpServletResponse resp) {	

	try {
	    String userXsltPath = getConfiguration().getXsltPath();
	    if(req.getUserPrincipal() != null){			    				
		userXsltPath=userXsltPath+"/"+req.getUserPrincipal().getName()+"/";
	    }	 

	    userXsltPath = userXsltPath+"/"+version+"/"+currentOperation +".xsl";
	    String globalXsltPath = getConfiguration().getXsltPath()+"/"+version+"/"+currentOperation +".xsl";;

	    File xsltFile = new File(userXsltPath);
	    boolean isPostTreat = false;	    
	    if(!xsltFile.exists()){	
		dump("Postreatment file "+xsltFile.toString()+"does not exist");
		xsltFile = new File(globalXsltPath);
		if (xsltFile.exists()){
		    isPostTreat=true;		    
		}else{
		    dump("Postreatment file "+xsltFile.toString()+"does not exist");
		}
	    }else{
		isPostTreat=true;
	    }

	    // Transforms the results using a xslt before sending the response
	    // back	    
	    //InputStream xml = new FileInputStream(filePathList.get(0));
	    TransformerFactory tFactory = TransformerFactory.newInstance();


	    File tempFile = null;


	    if (isXML(responseContentType)){			    
		tempFile = createTempFile(UUID.randomUUID().toString(), getExtension(responseContentType));			
	    }else{
		tempFile = new File(filePathList.get(0));
	    }
	    Transformer transformer = null;

	    if (currentOperation  != null) {

		if ("GetCapabilities".equalsIgnoreCase(currentOperation) || "capabilities".equalsIgnoreCase(currentOperation)) {		    		    	   

		    //Contains the list of temporary modified Capabilities files. 		    
		    List<File> tempFileCapa = new Vector<File>();
		    for (int iFilePath = 0;iFilePath<filePathList.size();iFilePath++){
			tempFileCapa.add(createTempFile(UUID.randomUUID().toString(), ".xml"));
			FileOutputStream tempFosCapa  = new FileOutputStream(tempFileCapa.get(iFilePath));

			InputStream xslt =  new ByteArrayInputStream(buildCapabilitiesXSLT(req,iFilePath).toString().getBytes());

			InputSource inputSource = new InputSource(new FileInputStream(filePathList.get(iFilePath)));

			XMLReader xmlReader = XMLReaderFactory.createXMLReader();


			String user =(String)getUsername(getRemoteServerUrl(iFilePath));
			String password = (String)getPassword(getRemoteServerUrl(iFilePath));
			if (user!=null && user.length()>0){			
			    ResourceResolver rr = new ResourceResolver(user,password);
			    xmlReader.setEntityResolver(rr);
			}
			// END Added to hook in my EntityResolver		     
			SAXSource saxSource = new SAXSource(xmlReader,inputSource);

			//StreamSource ss = new StreamSource(xml);		    

			transformer = tFactory.newTransformer(new StreamSource(xslt));

			//Write the result in a temporary file		    
			transformer.transform(saxSource, new StreamResult(tempFosCapa));								
		    }

		    //Merge the results of all the capabilities and return it into a single file

		    tempFile = mergeCapabilities(tempFileCapa);


		}else{
		    if (currentOperation.equals("GetMap") || "Map".equalsIgnoreCase(currentOperation)) {			
			
			boolean isTransparent= isAcceptingTransparency(responseContentType);
			BufferedImage imageSource =filterImage(getLayerFilter(layerFilePathList.get(0)),filePathList.get(0),isTransparent);
			Graphics2D g = imageSource.createGraphics();

			for (int iFilePath = 1;iFilePath<filePathList.size();iFilePath++){			    			
			    BufferedImage image = filterImage(getLayerFilter(layerFilePathList.get(iFilePath)),filePathList.get(iFilePath),isTransparent);
			    if (image !=null) g.drawImage(image, null, 0, 0);
			}



			Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(responseContentType);

			if (iter.hasNext()) {
			    ImageWriter writer = (ImageWriter)iter.next();
			    			    
			    
			    tempFile = createTempFile(UUID.randomUUID().toString(), getExtension(responseContentType));
			    FileImageOutputStream output = 
				new FileImageOutputStream(tempFile);			 
			    writer.setOutput(output);
			    writer.write(imageSource);
			}
		    }
		}
	    }



	    /*
	     * if a xslt file exists then 
	     * post-treat the response
	     */

	    if (isPostTreat && isXML(responseContentType)){		    
		PrintWriter out = resp.getWriter();
		transformer = tFactory.newTransformer(new StreamSource(xsltFile));		    
		transformer.transform(new StreamSource(tempFile), new StreamResult(out));
		//delete the temporary file
		tempFile.delete();
		out.close();
		//the job is done. we can go out
		return;

	    }

	    //No post rule to apply. 
	    //Copy the file result on the output stream
	    OutputStream os = resp.getOutputStream();
	    resp.setContentType(responseContentType);
	    InputStream is = new FileInputStream(tempFile);
	    int byteRead;
	    try {
		while((byteRead = is.read()) != -1) {  
		    os.write(byteRead);
		}
	    } finally{		
		os.flush();
		os.close();		
		is.close();
		DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
		Date d = new Date();	
		dump("SYSTEM","ClientResponseDateTime",dateFormat.format(d));		

		if (tempFile !=null) {
		    dump("SYSTEM","ClientResponseLength",tempFile.length());
		    tempFile.delete();	
		}
	    }	
	    DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
	    Date d = new Date();	

	    dump("SYSTEM","ClientResponseDateTime",dateFormat.format(d));

	} catch (Exception e) {
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}

    }
    /**
     * @return
     */
    private BufferedImage filterImage(String filter,String fileName,boolean isTransparent ) {
	try{
	    String []s = bbox.split(",");


	    if (filter !=null){
		InputStream bis = new ByteArrayInputStream(filter.getBytes());
		System.setProperty("org.geotools.referencing.forceXY", "true");

		Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);			
		WKTReader wktReader= new WKTReader();

		Geometry polygon = wktReader.read(object.toString());

		filter.indexOf("srsName");
		String srs= filter.substring(filter.indexOf("srsName"));			
		srs = srs.substring(srs.indexOf("\"")+1);			
		srs = srs.substring(0,srs.indexOf("\""));						
		polygon.setSRID(Integer.parseInt(srs.substring(5)));

		CRSEnvelope bbox = new CRSEnvelope(srsName,Double.parseDouble(s[0]),Double.parseDouble(s[1]),Double.parseDouble(s[2]),Double.parseDouble(s[3]));			

		//final WorldFileReader reader = new WorldFileReader(new File(filePath));

		BufferedImage imageSource = ImageIO.read(new File(fileName));
			
		BufferedImage imageOut = imageFiltering(imageSource,bbox,polygon,isTransparent);
		return imageOut;
	    }else{
		return (ImageIO.read(new File(fileName)));
	    }	    
	}catch(Exception e){
	    e.printStackTrace();
	}
	return null;
    }


    /**
     * @param tempFileCapa
     * @return
     */
    private File mergeCapabilities(List<File> tempFileCapa) {

	if (tempFileCapa.size() == 0) return null;
	try{
	    File fMaster = tempFileCapa.get(0);
	    DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
	    db.setNamespaceAware(false);
	    Document documentMaster = db.newDocumentBuilder().parse(fMaster);
	    DOMImplementationLS implLS = null;
	    if(documentMaster.getImplementation().hasFeature("LS", "3.0")) {
		implLS = (DOMImplementationLS)
		documentMaster.getImplementation();
	    }
	    else { 
		DOMImplementationRegistry enregistreur = 
		    DOMImplementationRegistry.newInstance();
		implLS = (DOMImplementationLS)
		enregistreur.getDOMImplementation("LS 3.0");
	    }
	    if(implLS == null){
		dump("Error", "DOM Load and Save not Supported. Multiple server is not allowed");
		return fMaster;
	    }

	    for (int i=1;i<tempFileCapa.size();i++){
		Document documentChild = db.newDocumentBuilder().parse(tempFileCapa.get(i));
		NodeList nl = documentChild.getElementsByTagName("Layer");
		NodeList nlMaster = documentMaster.getElementsByTagName("Layer");
		Node ItemMaster = nlMaster.item(0);
		ItemMaster.insertBefore(documentMaster.importNode(nl.item(0).cloneNode(true), true), null);


	    }	        	        	        	        

	    File f = createTempFile(UUID.randomUUID().toString(),".xml");
	    System.out.println(f.toURI());
	    FileOutputStream fluxSortie = new FileOutputStream(f);
	    LSSerializer serialiseur = implLS.createLSSerializer();
	    LSOutput sortie = implLS.createLSOutput();
	    sortie.setEncoding("UTF-8");
	    sortie.setSystemId(f.toString());
	    sortie.setByteStream(fluxSortie);
	    serialiseur.write(documentMaster, sortie);
	    fluxSortie.flush();
	    fluxSortie.close();

	    return f;
	}catch(Exception e){
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	    return null;
	}
    }


    protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp){

    }
    protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp){

	try{
	    String operation = null;
	    String version = "000";
	    String service = "";
	    String width = "";
	    String height ="";
	    String format ="";

	    layers=null;
	    boolean sendRequest = true;
	    Enumeration<String> parameterNames = req.getParameterNames();
	    String paramUrlBase = "";	   	    

	    // To build the request to dispatch
	    while (parameterNames.hasMoreElements()) {
		String key = (String) parameterNames.nextElement();
		String value = req.getParameter(key);
		if (!key.equalsIgnoreCase("LAYERS"))
		    if (!key.equalsIgnoreCase("STYLES"))
			paramUrlBase = paramUrlBase + key + "=" + value + "&";

		if (key.equalsIgnoreCase("Request")) {
		    // Gets the requested Operation
		    if (value.equalsIgnoreCase("capabilities")){
			operation = "GetCapabilities";
		    }else{
			operation = value;
		    }

		}else
		    if (key.equalsIgnoreCase("version")) {
			// Gets the requested Operation
			version = value;
		    }else if (key.equalsIgnoreCase("wmtver")) {
			// Gets the requested Operation
			version = value;
			service = "WMS";
		    }else
			if (key.equalsIgnoreCase("service")) {
			    // Gets the requested Operation
			    service = value;
			}else if (key.equalsIgnoreCase("BBOX")) {
			    // Gets the requested Operation
			    bbox = value;
			}else if (key.equalsIgnoreCase("SRS")) {
			    // Gets the requested Operation
			    srsName  = value;
			}else
			    if (key.equalsIgnoreCase("LAYERS")) {
				layers = value;
			    }else if (key.equalsIgnoreCase("STYLES")) {
				styles = value;
			    }else  if (key.equalsIgnoreCase("WIDTH")) {
				width= value;
			    }else  if (key.equalsIgnoreCase("HEIGHT")) {
				height= value;
			    }else  if (key.equalsIgnoreCase("FORMAT")) {
				format= value;
			    } 
	    }

	    String user="";
	    if(req.getUserPrincipal() != null){
		user= req.getUserPrincipal().getName();
	    }	    	  
	    List<RemoteServerInfo> grsiList = getRemoteServerInfoList();
	    /* HashMap<String,List<String>> serverLayerHash = new HashMap<String,List<String>>();
	    HashMap<String,List<String>> serverStylesHash = new HashMap<String,List<String>>();	   	    
	     */
	    for (int j=0;j<grsiList.size();j++){
		String paramUrl = "";	
		boolean loopOnLayer=false;
		List<String> layerToKeepList = new Vector<String>();
		List<String> stylesToKeepList = new Vector<String>();

		if(hasPolicy){
		    //List<String> layerToRemoveList = new Vector<String>();

		    //If not in the right size range then do not send the request.  
		    if (("GetMap".equalsIgnoreCase(operation)||"map".equalsIgnoreCase(operation) )&& !isSizeInTheRightRange(Integer.parseInt(width),Integer.parseInt(height))){			
			sendRequest=false;
		    }		    

		    //Only if the initial request has "LAYER" parameter
		    if (sendRequest && layers!=null && layers.length()>0){
			String[] layerArray = layers.split(",");
			String[] layerStyleArray = styles.split(",");
			String layersParam ="";
			String stylesParam ="";
			for (int i = 0; i<layerArray.length;i++){		    			

			    if (layerArray[i].startsWith(getRemoteServerInfo(j).getPrefix()) || getRemoteServerInfo(j).getPrefix().length()==0){				
				layerArray[i] = layerArray[i].substring((getRemoteServerInfo(j).getPrefix()).length());
				boolean isLayerTypePermited = isLayerAllowed(layerArray[i], getRemoteServerUrl(j));
				String []c = bbox.split(",");

				ReferencedEnvelope re = 
				    new ReferencedEnvelope(Double.parseDouble(c[0]),Double.parseDouble(c[2]),Double.parseDouble(c[1]),Double.parseDouble(c[3]), 
					    CRS.decode(srsName));


				if (isLayerTypePermited ){
				    if (isLayerInScale(layerArray[i], getRemoteServerUrl(j), RendererUtilities.calculateOGCScale(re, Integer.parseInt(width), null))){
					isLayerTypePermited = true;
				    }else{
					isLayerTypePermited = false;
				    }
				}

				//If type name is not authorized then does not request it
				if (isLayerTypePermited) {
				    if (layerStyleArray.length>i){
					stylesToKeepList .add(layerStyleArray[i]);					
				    } else{
					stylesToKeepList .add("");
				    }
				    layerToKeepList.add(layerArray[i]);
				    layersParam = layersParam + layerArray[i] +",";
				    if (layerStyleArray.length>i){
					stylesParam = stylesParam + layerStyleArray[i] +",";					
				    } else{
					stylesParam = stylesParam + "" +",";
				    }


				}
			    }
			}
			/*serverLayerHash.put(getRemoteServerUrl(j), layerToKeepList);
			serverStylesHash.put(getRemoteServerUrl(j), stylesToKeepList);*/
			if (layersParam.length() == 0){
			    sendRequest=false;
			}else{
			    //  paramUrl="LAYERS="+layersParam.substring(0, layersParam.length()-1)+"&STYLES="+stylesParam.substring(0, stylesParam.length()-1);
			    loopOnLayer=true;
			}
		    }
		}else{
		    //@TODO:Manage multiple servers when no policy is existing.  
		    if (layers!=null && layers.length()>0){
			paramUrl="LAYERS="+layers+"&STYLES="+styles;
		    }
		}
		if (sendRequest){		

		    if (loopOnLayer){

			for (int iLayers=0;iLayers<layerToKeepList.size();iLayers++){
			    boolean iscoveredByfilter = true;
			    if (("GetMap".equalsIgnoreCase(operation)||"map".equalsIgnoreCase(operation) )){
				//If the bbox is not in the filter then don't send the request.
				System.setProperty("org.geotools.referencing.forceXY", "true");

				String []s = bbox.split(",");																

				//create the geometry of the filter
				//and transform to the srs of the bbox
				String filter = getLayerFilter(getRemoteServerUrl(j),layerToKeepList.get(iLayers));
				if (filter!=null&&filter.length()>0){
				    InputStream bis = new ByteArrayInputStream(filter.getBytes());
				    Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);			
				    WKTReader wktReader= new WKTReader();
				    Geometry polygon = wktReader.read(object.toString());
				    filter.indexOf("srsName");
				    String srs= filter.substring(filter.indexOf("srsName"));			
				    srs = srs.substring(srs.indexOf("\"")+1);			
				    srs = srs.substring(0,srs.indexOf("\""));						
				    polygon.setSRID(Integer.parseInt(srs.substring(5)));							


				    CoordinateReferenceSystem sourceCRS = CRS.decode("EPSG:"+(new Integer(polygon.getSRID())).toString());
				    CoordinateReferenceSystem targetCRS = CRS.decode(srsName);	    

				    //ReferencedEnvelope env = new ReferencedEnvelope(Double.parseDouble(s[0]),Double.parseDouble(s[1]),Double.parseDouble(s[2]),Double.parseDouble(s[3]),targetCRS);

				    double x1 = 	Double.parseDouble(s[0]);
				    double y1 = Double.parseDouble(s[1]);
				    double x2 =Double.parseDouble(s[2]); 
				    double y2 = Double.parseDouble(s[3]);
				    MathTransform a = CRS.findMathTransform(sourceCRS, targetCRS,false);

				    polygon = JTS.transform(polygon, a);
				    polygon.setSRID(Integer.parseInt(srs.substring(5)));				
				    //Geometry bboxGeom = JTS.toGeometry(env);
				    Coordinate []c = {new Coordinate(x1,y1),new Coordinate(x1,y1),new Coordinate(x2,y1),new Coordinate(x2,y2),new Coordinate(x1,y2),new Coordinate(x1,y1)};
				    GeometryFactory gf= new GeometryFactory (); 
				    Geometry bboxGeom=gf.createPolygon(gf.createLinearRing(c), null);


				    bboxGeom.setSRID(Integer.parseInt(srs.substring(5)));
				    IntersectionMatrix mat1 = bboxGeom.relate(polygon);
				    IntersectionMatrix mat2 = polygon.relate(bboxGeom);
				    /*System.out.println(mat1.isContains());
				System.out.println(mat1.isCoveredBy());
				System.out.println(mat1.isDisjoint());
				System.out.println(mat1.isOverlaps(bboxGeom.getDimension(), polygon.getDimension()));
				System.out.println(mat1.isCrosses(bboxGeom.getDimension(), polygon.getDimension()));
				System.out.println(mat1.isTouches(bboxGeom.getDimension(), polygon.getDimension()));
				System.out.println(mat1.isWithin());
				System.out.println(mat1.isIntersects());
				System.out.println("==================");
				System.out.println(mat2.isContains());
				System.out.println(mat2.isCoveredBy());
				System.out.println(mat2.isDisjoint());
				System.out.println(mat2.isOverlaps(bboxGeom.getDimension(), polygon.getDimension()));
				System.out.println(mat2.isCrosses(bboxGeom.getDimension(), polygon.getDimension()));
				System.out.println(mat2.isTouches(bboxGeom.getDimension(), polygon.getDimension()));
				System.out.println(mat2.isWithin());
				System.out.println(mat2.isIntersects());*/
				    //geom1 hardly knows geom2

				    if(mat1.isIntersects()||mat2.isIntersects() ||bboxGeom.overlaps(polygon)||polygon.overlaps(bboxGeom)||polygon.coveredBy(bboxGeom)||bboxGeom.coveredBy(polygon) || bboxGeom.touches(polygon) ||polygon.touches(bboxGeom) || bboxGeom.intersects((polygon))||bboxGeom.covers((polygon))||bboxGeom.crosses((polygon))||polygon.crosses(bboxGeom)||polygon.intersects((bboxGeom))||polygon.covers((bboxGeom))){
					iscoveredByfilter=true;
					
				    }
				    else {
					iscoveredByfilter=false;
									 
				    }
				}

			    }
			    if(iscoveredByfilter){
				paramUrl="LAYERS="+layerToKeepList.get(iLayers)+"&STYLES="+stylesToKeepList.get(iLayers);
				String filePath  = sendData("GET", getRemoteServerUrl(j), paramUrlBase+paramUrl);
				filePathList.add(filePath);
				layerFilePathList.add(layerToKeepList.get(iLayers));
			    }else{
				generateEmptyImage(width, height, format,true);
			    }
			}


		    }else{
			String filePath  = sendData("GET", getRemoteServerUrl(j), paramUrlBase+paramUrl);
			filePathList.add(filePath);
			layerFilePathList.add("");
		    }

		}else{
		    sendRequest=true;
		    if ("GetMap".equalsIgnoreCase(operation) || "map".equalsIgnoreCase(operation)){
			generateEmptyImage(width,height,format,true);
		    }
		}

	    }
	    version = version.replaceAll("\\.", "");

	    transform(version,operation,req, resp);
	}
	catch(Exception e){
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}
    }

    /**
     * 
     */
    private void generateEmptyImage(String width,String height,String format,boolean isTransparent) {
	//In the case of a GetMap, it should returns an empty image
	try{
	    BufferedImage imgOut = null;
	    if (isTransparent){
		imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.BITMASK);		    
	    }else{
		imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.TYPE_INT_ARGB);
	    }
	    responseContentType=format;
	    Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(responseContentType);

	    if (iter.hasNext()) {
		ImageWriter writer = (ImageWriter)iter.next();
		File tempFile = createTempFile(UUID.randomUUID().toString(), getExtension(responseContentType));
		FileImageOutputStream output = 
		    new FileImageOutputStream(tempFile);			 
		writer.setOutput(output);
		writer.write(imgOut);
		String filePath =tempFile.getPath();
		filePathList.add(filePath);
		layerFilePathList.add("");
	    }
	}catch(Exception e){
	    e.printStackTrace();
	}
    }


    /*
     * envelope contains the envelope of the whole image
     */
    BufferedImage imageFiltering(BufferedImage imageSource,CRSEnvelope envelope,Geometry polygonFilter,boolean isTransparent)
    {	
	try{	
	    System.setProperty("org.geotools.referencing.forceXY", "true");
	    //System.setProperty( (Hints.FORCE_STANDARD_AXIS_DIRECTIONS.toString()), "true" );


	    //Transform the srs of the filter if needed. 
	    String srsName = envelope.getEPSGCode();	    
	    CoordinateReferenceSystem crs = CRS.decode(srsName);

	    CoordinateReferenceSystem sourceCRS = CRS.decode("EPSG:"+(new Integer(polygonFilter.getSRID())).toString());
	    CoordinateReferenceSystem targetCRS = CRS.decode(envelope.getEPSGCode());	    

	    MathTransform a = CRS.findMathTransform(sourceCRS, targetCRS,false);

	    polygonFilter = JTS.transform(polygonFilter, a);



	    try{
		for(int i=0;i<crs.getIdentifiers().size();i++){
		    if (((NamedIdentifier)crs.getIdentifiers().toArray()[i]).getCodeSpace().equals("EPSG")){
			polygonFilter.setSRID(Integer.parseInt(((NamedIdentifier)crs.getIdentifiers().toArray()[i]).getCode()));
			break;
		    }
		}
	    }catch(Exception e){
		e.printStackTrace();
	    }

	    final GeometryAttributeType geom = new GeometricAttributeType("Geom", Geometry.class, false, null, crs, null);
	    final AttributeType attr1 = AttributeTypeFactory.newAttributeType("COLOR", String.class);
	    final AttributeType[] attributes = new AttributeType[] {attr1,geom};

	    final FeatureType schema = FeatureTypes.newFeatureType(attributes,"TEMPORARYFEATURE", new URI("depth.ch"), false, null, geom);

	    FeatureRasterizer fr = new FeatureRasterizer(imageSource.getHeight(),imageSource.getWidth());	        
	    double width = envelope.getMaxX()-envelope.getMinX();
	    double height = envelope.getMaxY()-envelope.getMinY();;
	    Rectangle2D.Double bounds = new Rectangle2D.Double(envelope.getMinX(),envelope.getMinY(),width,height);

	    fr.setBounds(bounds);
	    fr.setAttName("COLOR");

	    fr.addFeature(schema.create(new Object[] {Integer.toString(Color.WHITE.getRGB()),polygonFilter}));

	    BufferedImage bimage2 = fr.getBimage();
	    int imageType = BufferedImage.TYPE_INT_RGB;
	    if (isTransparent){
		imageType = BufferedImage.TYPE_INT_ARGB;
	    }
	    
	    BufferedImage dimg = new BufferedImage(imageSource.getWidth(), imageSource.getHeight(), imageType);
	    Graphics2D g = dimg.createGraphics();
	    g.setComposite(AlphaComposite.Src);
	    g.drawImage(imageSource, null, 0, 0);
	    g.dispose();
	    for(int i = 0; i < bimage2.getHeight(); i++) {
		for(int j = 0; j < bimage2.getWidth(); j++) {		
		    if(bimage2.getRGB(j, i) == 0) {
			//dimg.setRGB(j, i, 0x8F1C1C);				
			dimg.setRGB(j, i, 0xFFFFFF);
		    }
		}
	    }

	    return dimg;


	}catch(Exception e){
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}		

	return imageSource;
    }

    public static void main(String args[]) throws Exception{
	System.setProperty("org.geotools.referencing.forceXY", "true");
	WKTReader wktReader= new WKTReader();
	String s= "<gml:Polygon xmlns:gml=\"http://www.opengis.net/gml\" srsName=\"EPSG:2169\">"+
	"<gml:outerBoundaryIs>"+
	"<gml:LinearRing>"+                    
	"<gml:coordinates>74542.7218,83720.2762 84546.3001,83720.2762 84546.3001,74550.3295 74542.7218,74550.3295 74542.7218,83720.2762</gml:coordinates>"+				                                                                               
	"</gml:LinearRing>"+
	"</gml:outerBoundaryIs>"+
	"</gml:Polygon>";



	InputStream bis = new ByteArrayInputStream(s.getBytes());
	Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);	

	Geometry polygon = wktReader.read(object.toString());

	CoordinateReferenceSystem sourceCRS = CRS.decode("EPSG:2169");
	CoordinateReferenceSystem targetCRS = CRS.decode("EPSG:2169");	    



	polygon.setSRID(2169);							



	ReferencedEnvelope env = new ReferencedEnvelope(Double.parseDouble("73728.0016"),Double.parseDouble("73993.0557"),Double.parseDouble("75421.1025"),Double.parseDouble("75266.4533"),targetCRS);
	/*ReferencedEnvelope env = new ReferencedEnvelope(Double.parseDouble("75097.8417"),
		Double.parseDouble("74759.2374"),
		Double.parseDouble("76790.9425"),
		Double.parseDouble("76032.635"),targetCRS);
	 */
	GeometryFactory gf = new GeometryFactory();
	double x1= 73728.0016;
	double y1= 73993.0557;
	double x2= 75421.1025;
	double y2= 75266.4533;



	MathTransform a = CRS.findMathTransform(sourceCRS, targetCRS,false);

	polygon = JTS.transform(polygon, a);
	polygon.setSRID(Integer.parseInt("2169"));				
	Geometry bboxGeom = JTS.toGeometry(env);
	Coordinate []c = {new Coordinate(x1,y1),new Coordinate(x1,y1),new Coordinate(x2,y1),new Coordinate(x2,y2),new Coordinate(x1,y2),new Coordinate(x1,y1)};	
	bboxGeom=gf.createPolygon(gf.createLinearRing(c), null);

	bboxGeom.setSRID(Integer.parseInt("2169"));
	Geometry in = bboxGeom.intersection(polygon);

	System.out.println(in.getCoordinates().length);
	System.out.println(bboxGeom.getDimension());
	System.out.println(polygon.getDimension());

	IntersectionMatrix mat1 = bboxGeom.relate(polygon);
	IntersectionMatrix mat2 = polygon.relate(bboxGeom);
	System.out.println(mat1);
	System.out.println(mat2);
	System.out.println(mat1.isContains());
	System.out.println(mat1.isCoveredBy());
	System.out.println(mat1.isDisjoint());
	System.out.println(mat1.isOverlaps(bboxGeom.getDimension(), polygon.getDimension()));
	System.out.println(mat1.isCrosses(bboxGeom.getDimension(), polygon.getDimension()));
	System.out.println(mat1.isTouches(bboxGeom.getDimension(), polygon.getDimension()));
	System.out.println(mat1.isWithin());
	System.out.println(mat1.isIntersects());

	System.out.println("==================");
	System.out.println(mat2.isContains());
	System.out.println(mat2.isCoveredBy());
	System.out.println(mat2.isDisjoint());
	System.out.println(mat2.isOverlaps(bboxGeom.getDimension(), polygon.getDimension()));
	System.out.println(mat2.isCrosses(bboxGeom.getDimension(), polygon.getDimension()));
	System.out.println(mat2.isTouches(bboxGeom.getDimension(), polygon.getDimension()));
	System.out.println(mat2.isWithin());
	System.out.println(mat2.isIntersects());
	/*URL url = new URL("http://ecadastre.public.lu:8081/public/wfs/BDLTC?REQUEST=GetCapabilities");
	WebMapServer wms = new WebMapServer(url);

	WMSCapabilities caps = wms.getCapabilities();
	GetMapRequest mapRequest = wms.createGetMapRequest();

	Layer layer = null;
	for( Iterator i = caps.getLayerList().iterator(); i.hasNext();){
	    Layer test = (Layer) i.next();	    
	    if( test.getName() != null && test.getName().length() != 0 ){
		if (test.getName().equals("BATIMENT")){
		    layer = test;		
		    break;
		}
	    }
	}

	mapRequest.addLayer(layer);		

	mapRequest.setDimensions("400", "400");
	mapRequest.setFormat("image/png");

	CRSEnvelope bbox = new CRSEnvelope("EPSG:2169",73657.8965,133942.5699,73898.4137,134157.7131);
	mapRequest.setBBox(bbox);
	mapRequest.setSRS("EPSG:2169");
	System.out.println(mapRequest.getFinalURL());

	GetMapResponse response = wms.issueRequest( mapRequest );

	BufferedImage image = ImageIO.read(response.getInputStream());
	Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType("image/gif");

	if (iter.hasNext()) {
	    ImageWriter writer = (ImageWriter)iter.next();
	    File outFile = new File("C:\\test.gif");
	    FileImageOutputStream output = 
		new FileImageOutputStream(outFile);
	    writer.setOutput(output);
	    writer.write(image);
	}

	//write(image, "image/png", new File());

	String readerNames[] = 
	    ImageIO.getReaderFormatNames();
	printlist(readerNames, "Reader names:");
	String readerMimes[] = 
	    ImageIO.getReaderMIMETypes();
	printlist(readerMimes, "Reader MIME types:");
	String writerNames[] = 
	    ImageIO.getWriterFormatNames();
	printlist(writerNames, "Writer names:");
	String writerMimes[] = 
	    ImageIO.getWriterMIMETypes();
	printlist(writerMimes, "Writer MIME types:");

	 */
    }
    private static void printlist(String names[], 
	    String title) {
	System.out.println(title);
	for (int i=0, n=names.length; i<n; i++) {
	    System.out.println("\t" + names[i]);
	}
    }
}
