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
package ch.depth.proxy.wfs;

import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.DataInputStream;
import java.io.File;
import java.io.FileReader;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.UUID;
import java.util.Vector;
import java.util.logging.Level;

import javax.naming.NoPermissionException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.geotools.data.ows.FeatureSetDescription;
import org.geotools.feature.Feature;
import org.geotools.feature.FeatureCollection;
import org.geotools.feature.FeatureIterator;
import org.geotools.feature.FeatureType;
import org.geotools.filter.Filter;
import org.geotools.filter.FilterDOMParser;
import org.geotools.gml.producer.FeatureTransformer;
import org.geotools.referencing.CRS;
import org.geotools.renderer.shape.FilterTransformer;
import org.geotools.xml.DocumentFactory;
import org.geotools.xml.gml.GMLComplexTypes;
import org.geotools.xml.gml.GMLFeatureCollection;
import org.geotools.xml.schema.ComplexType;
import org.geotools.xml.schema.Schema;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.ls.LSSerializer;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import ch.depth.proxy.core.ProxyServlet;
import ch.depth.proxy.policy.Operation;
import ch.depth.xml.handler.RequestHandler;
import ch.depth.xml.resolver.ResourceResolver;

/**
 * @author rmi
 *
 */
public class WFSProxyServlet extends ProxyServlet {
    private StringBuffer WFSGetFeatureRenameFt = new StringBuffer();


    protected StringBuffer buildCapabilitiesXSLT(HttpServletRequest req,int remoteServerIndex){

	try {

	    String url = getServletUrl(req);


	    StringBuffer WFSCapabilities100 = new StringBuffer ();		

	    WFSCapabilities100.append("<xsl:stylesheet version=\"1.00\" xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");

	    WFSCapabilities100.append("<xsl:template match=\"wfs:OnlineResource\">");
	    WFSCapabilities100.append("<wfs:OnlineResource>"); 		
	    WFSCapabilities100.append(url);				
	    WFSCapabilities100.append("</wfs:OnlineResource>");
	    WFSCapabilities100.append("</xsl:template>");

	    WFSCapabilities100.append("<xsl:template match=\"wfs:Get\">");
	    WFSCapabilities100.append("<wfs:Get>"); 
	    WFSCapabilities100.append("<xsl:attribute name=\"onlineResource\">");

	    WFSCapabilities100.append(url);				

	    WFSCapabilities100.append("</xsl:attribute>");
	    WFSCapabilities100.append("</wfs:Get>");
	    WFSCapabilities100.append("</xsl:template>");
	    WFSCapabilities100.append("<xsl:template match=\"wfs:Post\">");
	    WFSCapabilities100.append("<wfs:Post>"); 
	    WFSCapabilities100.append("<xsl:attribute name=\"onlineResource\">");		

	    WFSCapabilities100.append(url);

	    WFSCapabilities100.append("</xsl:attribute>");
	    WFSCapabilities100.append("</wfs:Post>");
	    WFSCapabilities100.append("</xsl:template>");
	    if (hasPolicy){
		if (!policy.getOperations().isAll()){

		    Iterator<Operation> it = policy.getOperations().getOperation().iterator();

		    while(it.hasNext()){

			String text = it.next().getName();
			if (text !=null){
			    WFSCapabilities100.append("<xsl:template match=\"wfs:Capability/wfs:Request/wfs:");

			    WFSCapabilities100.append(text);

			    WFSCapabilities100.append("\">");

			    WFSCapabilities100.append("<!-- Copy the current node -->");
			    WFSCapabilities100.append("<xsl:copy>");
			    WFSCapabilities100.append("<!-- Including any attributes it has and any child nodes -->");
			    WFSCapabilities100.append("<xsl:apply-templates select=\"@*|node()\"/>");
			    WFSCapabilities100.append("</xsl:copy>");

			    WFSCapabilities100.append("</xsl:template>");
			}							
		    }

		    if (policy.getOperations().getOperation().size() == 0 ){
			WFSCapabilities100.append("<xsl:template match=\"wfs:Capability/wfs:Request/\"></xsl:template>");						
		    }
		}
	    }
	    WFSCapabilities100.append("  <!-- Whenever you match any node or any attribute -->");
	    WFSCapabilities100.append("<xsl:template match=\"node()|@*\">");
	    WFSCapabilities100.append("<!-- Copy the current node -->");
	    WFSCapabilities100.append("<xsl:copy>");
	    WFSCapabilities100.append("<!-- Including any attributes it has and any child nodes -->");
	    WFSCapabilities100.append("<xsl:apply-templates select=\"@*|node()\"/>");
	    WFSCapabilities100.append("</xsl:copy>");
	    WFSCapabilities100.append("</xsl:template>");

	    if (hasPolicy){

		Map hints = new HashMap();		
		hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);


		org.geotools.data.ows.WFSCapabilities doc = (org.geotools.data.ows.WFSCapabilities)DocumentFactory.getInstance(new File(filePathList.get(remoteServerIndex)).toURI(),hints,Level.WARNING);
		List<FeatureSetDescription> l = doc.getFeatureTypes();		   

		for (int i = 0;i< l.size();i++){		    
		    String ftName= l.get(i).getName();								
		    if (hasPolicy){
			String tmpFT = ftName;
			if (tmpFT!=null){
			    String [] s = tmpFT.split(":");
			    tmpFT = s[s.length-1];
			}
			if (!isFeatureTypeAllowed(tmpFT, getRemoteServerUrl(remoteServerIndex))){

			    WFSCapabilities100.append("<xsl:template match=\"//wfs:FeatureType[starts-with(wfs:Name,'"+ftName+"')]\">");		
			    WFSCapabilities100.append("</xsl:template>");
			}					
		    }
		}

		//Add the prefix before the feature type
		if(getRemoteServerInfo(remoteServerIndex).getPrefix().length()>0){
		    WFSCapabilities100.append("<xsl:template match=\"//wfs:FeatureType/wfs:Name\">");
		    WFSCapabilities100.append("<Name>");
		    WFSCapabilities100.append("<xsl:if test=\"contains(.,':')\">");		
		    WFSCapabilities100.append("<xsl:value-of select=\"substring-before(.,':')\"/>"+":"+getRemoteServerInfo(remoteServerIndex).getPrefix()+"<xsl:value-of select=\"substring-after(., ':')\"/>");
		    WFSCapabilities100.append("</xsl:if>");
		    WFSCapabilities100.append("<xsl:if test=\"not(contains(.,':'))\">");		
		    WFSCapabilities100.append(getRemoteServerInfo(remoteServerIndex).getPrefix()+"<xsl:value-of select=\".\"/>");
		    WFSCapabilities100.append("</xsl:if>");

		    WFSCapabilities100.append("</Name>");
		    WFSCapabilities100.append("</xsl:template>");
		}
	    }
	    WFSCapabilities100.append("</xsl:stylesheet>");	

	    return WFSCapabilities100;
	} catch (Exception e) {
	    // TODO Auto-generated catch block
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}

	//If something goes wrong, an empty stylesheet is returned.	
	StringBuffer sb = new StringBuffer();		
	return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>"); 
    }

    protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp){
	try {

	    XMLReader xr = XMLReaderFactory.createXMLReader();
	    HashMap<String,String> env = new HashMap<String,String>();
	    RequestHandler rh = new RequestHandler();
	    xr.setContentHandler(rh);	    

	    StringBuffer paramSB = new StringBuffer();
	    String param="";
	    String input;
	    BufferedReader in = new BufferedReader(new InputStreamReader(req
		    .getInputStream()));
	    while ((input = in.readLine()) != null) {
		paramSB.append(input);
	    }
	    param = paramSB.toString();
	    dump("SYSTEM","Request",param);

	    xr.parse(new InputSource(new InputStreamReader(
		    new ByteArrayInputStream(param.toString().getBytes()))));

	    String version = rh.getVersion();
	    if (version!=null) version = version.replaceAll("\\.", "");

	    String currentOperation =  rh.getOperation();


	    String user="";
	    if(req.getUserPrincipal() != null){
		user= req.getUserPrincipal().getName();
	    }
	    if (hasPolicy){
		if (!isOperationAllowed(currentOperation)) throw new NoPermissionException("operation is not allowed");
	    }

	    String paramOrig = param;
	    for (int iServer = 0 ; iServer <getRemoteServerInfoList().size();iServer++){
		param = paramOrig;
		List<String> featureTypeListToKeep = new Vector<String>();
		List<String> featureTypeListToRemove = new Vector<String>();

		if (currentOperation.equalsIgnoreCase("GetFeature") || currentOperation.equalsIgnoreCase("DescribeFeatureType")){		

		    Object [] fields = (Object [])rh.getTypeName().toArray();
		    if (hasPolicy){
			for (int i = 0; i<fields.length;i++){
			    String tmpFT = fields[i].toString();
			    if (tmpFT!=null){
				String [] s = tmpFT.split(":");
				tmpFT = s[s.length-1];
			    }

			    if (tmpFT.startsWith(getRemoteServerInfo(iServer).getPrefix())||getRemoteServerInfo(iServer).getPrefix().length()==0){
				if (!tmpFT.equals("")){
				    tmpFT = tmpFT.substring((getRemoteServerInfo(iServer).getPrefix()).length());
				    if (isFeatureTypeAllowed(tmpFT, getRemoteServerUrl(iServer))){				    
					featureTypeListToKeep.add(tmpFT);			    
				    }		
				    else{
					featureTypeListToRemove.add(tmpFT);
				    }
				}
			    }else{
				if (tmpFT.equals("")){
				    featureTypeListToKeep.add("");   
				}
			    }
			}
		    }
		    param = removeTypesFromPOSTUrl(featureTypeListToKeep,param,iServer,currentOperation);
		}
		boolean send = true;
		String filePath = "";
		if ("GetFeature".equalsIgnoreCase(currentOperation)){
		    if (featureTypeListToKeep.size()==0){
			String s=
			    "<?xml version='1.0' encoding='utf-8' ?>"+		   
			    "<ogcwfs:FeatureCollection xmlns:ogcwfs=\"http://www.opengis.net/wfs\"   xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" >"+
			    "<gml:boundedBy>"+
			    "<gml:null>unavailable</gml:null>"+
			    "</gml:boundedBy>"+
			    "</ogcwfs:FeatureCollection>";
			File tempFile = createTempFile("requestPreTreatmentPOST"+UUID.randomUUID().toString(), ".xml");

			FileOutputStream tempFos = new FileOutputStream(tempFile);
			tempFos.write(s.getBytes());
			tempFos.flush();
			tempFos.close();
			in.close();
			filePath = tempFile.toString();
			send = false;

		    }


		    String userFilter = null;
		    if (featureTypeListToKeep.size()>0){
			userFilter = getFeatureTypeRemoteFilter(getRemoteServerUrl(iServer),featureTypeListToKeep.get(0));
			featureTypePathList.add(featureTypeListToKeep.get(0));
		    }else{
			featureTypePathList.add("");
		    }

		    if (send&&userFilter!=null){			    
			InputStream isRequestFilter = new ByteArrayInputStream(param.getBytes());
			InputStream isUserFilter = new ByteArrayInputStream(userFilter.getBytes());
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(true);
			Document documentRequestFilter = db.newDocumentBuilder().parse(isRequestFilter);
			Document documentUserFilter = db.newDocumentBuilder().parse(isUserFilter);

			DOMImplementationLS implLS = null;
			if(documentRequestFilter.getImplementation().hasFeature("LS", "3.0")) {
			    implLS = (DOMImplementationLS)
			    documentRequestFilter.getImplementation();
			}
			else { 
			    DOMImplementationRegistry enregistreur = 
				DOMImplementationRegistry.newInstance();
			    implLS = (DOMImplementationLS)
			    enregistreur.getDOMImplementation("LS 3.0");
			}
			NodeList nlRequestFilter = documentRequestFilter.getElementsByTagName("Filter");
			if (nlRequestFilter.getLength() == 0) nlRequestFilter = documentRequestFilter.getElementsByTagNameNS("http://www.opengis.net/ogc","Filter");
			NodeList nlUserFilter = documentUserFilter.getElementsByTagName( "Filter");
			if (nlUserFilter.getLength() == 0) nlUserFilter = documentUserFilter.getElementsByTagNameNS("http://www.opengis.net/ogc","Filter");

			//A filter is existing 
			//Could cause some performance issue, when query some WFs based onOracle.
			if (nlRequestFilter.getLength()>0 ){
			    Node nodeRequestFilter = nlRequestFilter.item(0);
			    Node nodeUserFilter = nlUserFilter.item(0);
			    Node andNode = documentRequestFilter.createElement("And");

			    for (int i=0;i<nodeRequestFilter.getChildNodes().getLength();i++){
				andNode.appendChild(nodeRequestFilter.getChildNodes().item(i).cloneNode(true));
			    }

			    for (int i=0;i<nodeUserFilter.getChildNodes().getLength();i++){					
				andNode.appendChild(documentRequestFilter.adoptNode(nodeUserFilter.getChildNodes().item(i).cloneNode(true)));
			    }


			    while(nodeRequestFilter.hasChildNodes()){
				nodeRequestFilter.removeChild(nodeRequestFilter.getChildNodes().item(0));					    
			    }

			    nodeRequestFilter.appendChild(andNode);
			}

			OutputStream fluxSortie = 	new ByteArrayOutputStream();

			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			//sortie.setSystemId();
			sortie.setByteStream(fluxSortie);
			serialiseur.write(documentRequestFilter, sortie);
			fluxSortie.flush();
			fluxSortie.close();
			param=fluxSortie.toString();
		    }
		    
		}
		if (send) filePath = sendData("POST", getRemoteServerUrl(iServer), param);
		filePathList.add(filePath);		
	    }	  	  	
	    version=version.replaceAll("\\.", "");   
	    transform(version, currentOperation,req,resp);	    

	} catch (Exception e) {
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}
    }

    protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp){

	try{
	    String currentOperation = null;
	    String version = "000";
	    String service = "";
	    Enumeration<String> parameterNames = req.getParameterNames();
	    String paramUrl = "";	    
	    String filter=null;

	    HashMap<String,String> env = new HashMap<String,String>();
	    String typeName = "";
	    // Build the request to dispatch
	    while (parameterNames.hasMoreElements()) {
		String key = (String) parameterNames.nextElement();
		//String value = URLEncoder.encode(req.getParameter(key));
		String value = req.getParameter(key);	
		if (!key.equalsIgnoreCase("FILTER")) {
		    paramUrl = paramUrl + key + "=" + value + "&";
		}
		if (key.equalsIgnoreCase("TYPENAME")){
		    typeName= value; 
		}else
		    if (key.equalsIgnoreCase("Request")) {
			// Gets the requested Operation
			if (value.equalsIgnoreCase("capabilities")){
			    currentOperation = "GetCapabilities";
			}else{
			    currentOperation = value;
			}

		    }else
			if (key.equalsIgnoreCase("version")) {
			    // Gets the requested Operation
			    version = value;
			}else
			    if (key.equalsIgnoreCase("service")) {
				// Gets the requested Operation
				service = value;
			    }else
				if (key.equalsIgnoreCase("FILTER")) {
				    // Gets the requested Operation
				    filter = value;
				}

	    }
	    String user="";
	    if(req.getUserPrincipal() != null){
		user= req.getUserPrincipal().getName();
	    }	
	    if (hasPolicy){
		if (!isOperationAllowed(currentOperation)) throw new NoPermissionException("operation is not allowed");
	    }



	    for (int iServer = 0 ; iServer <getRemoteServerInfoList().size();iServer++){
		List<String> featureTypeListToKeep = new Vector<String>();
		List<String> featureTypeListToRemove = new Vector<String>();
		if(currentOperation!=null){
		    if (currentOperation.equalsIgnoreCase("GetFeature") || currentOperation.equalsIgnoreCase("DescribeFeatureType")){		

			String [] fields = typeName.split(",");
			if (hasPolicy){
			    for (int i = 0; i<fields.length;i++){
				String tmpFT = fields[i];
				if (tmpFT!=null){
				    String [] s = tmpFT.split(":");
				    tmpFT = s[s.length-1];
				}			    
				if (tmpFT.startsWith(getRemoteServerInfo(iServer).getPrefix()) || getRemoteServerInfo(iServer).getPrefix().length()==0){
				    if (!tmpFT.equals("")){
					tmpFT = tmpFT.substring((getRemoteServerInfo(iServer).getPrefix()).length());
					if (isFeatureTypeAllowed(tmpFT, getRemoteServerUrl(iServer))){				    
					    featureTypeListToKeep.add(tmpFT);			    
					}		
					else{
					    featureTypeListToRemove.add(tmpFT);
					}
				    }
				}else{
				    if (tmpFT.equals("")){
					featureTypeListToKeep.add("");   
				    }
				}
			    }
			    if( !currentOperation.equalsIgnoreCase("DescribeFeatureType")){
				if (filter == null) {
				    //If there is no filter then add the filter defined in the policy file			    
				    String userFilter = null;
				    if (featureTypeListToKeep.size()>0){
					userFilter = getFeatureTypeRemoteFilter(getRemoteServerUrl(iServer),featureTypeListToKeep.get(0));
					featureTypePathList.add(featureTypeListToKeep.get(0));
				    }else{
					featureTypePathList.add("");
				    }
				    if (userFilter!=null){			    
					if (!paramUrl.endsWith("&")){
					    paramUrl = paramUrl +"&";
					}
					paramUrl = paramUrl +"FILTER=" +java.net.URLEncoder.encode(userFilter);
				    }			    
				}else{

				    //Combine both user defined filter and request filter	
				    String userFilter = null;
				    if (featureTypeListToKeep.size()>0){
					userFilter = getFeatureTypeRemoteFilter(getRemoteServerUrl(iServer),featureTypeListToKeep.get(0));
				    }else{
					
				    }
				    if (userFilter!=null){			    
					if (!paramUrl.endsWith("&")){
					    paramUrl = paramUrl +"&";
					}

					InputStream isRequestFilter = new ByteArrayInputStream(filter.getBytes());
					InputStream isUserFilter = new ByteArrayInputStream(userFilter.getBytes());
					DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
					db.setNamespaceAware(true);
					Document documentRequestFilter = db.newDocumentBuilder().parse(isRequestFilter);
					Document documentUserFilter = db.newDocumentBuilder().parse(isUserFilter);

					DOMImplementationLS implLS = null;
					if(documentRequestFilter.getImplementation().hasFeature("LS", "3.0")) {
					    implLS = (DOMImplementationLS)
					    documentRequestFilter.getImplementation();
					}
					else { 
					    DOMImplementationRegistry enregistreur = 
						DOMImplementationRegistry.newInstance();
					    implLS = (DOMImplementationLS)
					    enregistreur.getDOMImplementation("LS 3.0");
					}
					NodeList nlRequestFilter = documentRequestFilter.getElementsByTagName("Filter");
					if (nlRequestFilter.getLength() == 0) nlRequestFilter = documentRequestFilter.getElementsByTagNameNS("http://www.opengis.net/ogc","Filter");
					NodeList nlUserFilter = documentUserFilter.getElementsByTagName( "Filter");
					if (nlUserFilter.getLength() == 0) nlUserFilter = documentUserFilter.getElementsByTagNameNS("http://www.opengis.net/ogc","Filter");

					//A filter is existing
					//Cause some performance issue. Disable it
					if (nlRequestFilter.getLength()>0&&false){
					    Node nodeRequestFilter = nlRequestFilter.item(0);
					    Node nodeUserFilter = nlUserFilter.item(0);
					    Node andNode = documentRequestFilter.createElement("And");

					    for (int i=0;i<nodeRequestFilter.getChildNodes().getLength();i++){
						andNode.appendChild(nodeRequestFilter.getChildNodes().item(i).cloneNode(true));
					    }

					    for (int i=0;i<nodeUserFilter.getChildNodes().getLength();i++){					
						andNode.appendChild(documentRequestFilter.adoptNode(nodeUserFilter.getChildNodes().item(i).cloneNode(true)));
					    }


					    while(nodeRequestFilter.hasChildNodes()){
						nodeRequestFilter.removeChild(nodeRequestFilter.getChildNodes().item(0));					    
					    }

					    nodeRequestFilter.appendChild(andNode);
					}

					OutputStream fluxSortie = 	new ByteArrayOutputStream();

					LSSerializer serialiseur = implLS.createLSSerializer();
					LSOutput sortie = implLS.createLSOutput();
					sortie.setEncoding("UTF-8");
					//sortie.setSystemId();
					sortie.setByteStream(fluxSortie);
					serialiseur.write(documentRequestFilter, sortie);
					fluxSortie.flush();
					fluxSortie.close();

					paramUrl = paramUrl +"FILTER=" +java.net.URLEncoder.encode(fluxSortie.toString());								
				    }
				}
			    }
			    paramUrl = removeTypesFromGetUrl(featureTypeListToKeep,paramUrl);
			}

			boolean send = true;
			String filePath = "";
			if ("GetFeature".equalsIgnoreCase(currentOperation)){
			    if (featureTypeListToKeep.size()==0){
				String s=
				    "<?xml version='1.0' encoding='utf-8' ?>"+		   
				    "<ogcwfs:FeatureCollection xmlns:ogcwfs=\"http://www.opengis.net/wfs\"   xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" >"+
				    "<gml:boundedBy>"+
				    "<gml:null>unavailable</gml:null>"+
				    "</gml:boundedBy>"+
				    "</ogcwfs:FeatureCollection>";
				File tempFile = createTempFile(UUID.randomUUID().toString(), ".xml");

				FileOutputStream tempFos = new FileOutputStream(tempFile);
				tempFos.write(s.getBytes());
				tempFos.flush();
				tempFos.close();			
				filePath = tempFile.toString();
				send = false;
				featureTypeListToKeep.add("");
			    }
			}

			if(send) filePath = sendData("GET", getRemoteServerUrl(iServer), paramUrl);
			filePathList.add(filePath);
			
		    }else{
			String filePath = sendData("GET", getRemoteServerUrl(iServer), paramUrl);
			filePathList.add(filePath);			
		    }
		}
		version=version.replaceAll("\\.", "");   

		transform(version,currentOperation,req, resp);
	    }
	}
	catch(Exception e){
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}

    }


    private String removeTypesFromPOSTUrl(List<String> featureTypeListToKeep,String paramUrl,int iServer,String operation) {
	try{
	    List<Node> nodeListToRemove = new Vector<Node>();
	    InputStream is = new ByteArrayInputStream(paramUrl.getBytes());
	    DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
	    db.setNamespaceAware(true);
	    Document documentMaster = db.newDocumentBuilder().parse(is);

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

	    if ("GetFeature".equalsIgnoreCase(operation)){
		NodeList nl = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs", "Query");

		for (int i=0;i<nl.getLength();i++){	    
		    boolean isInList = false;
		    for (int j=0;j<featureTypeListToKeep.size();j++){
			String tmpFT = nl.item(i).getAttributes().getNamedItem("typeName").getTextContent();
			if (tmpFT!=null){
			    String [] s = tmpFT.split(":");
			    tmpFT = s[s.length-1];
			}
			if (tmpFT.startsWith(getRemoteServerInfo(iServer).getPrefix())){			
			    tmpFT = tmpFT.substring((getRemoteServerInfo(iServer).getPrefix()).length());			
			}

			if (tmpFT.equals(featureTypeListToKeep.get(j))){
			    nl.item(i).getAttributes().getNamedItem("typeName").setTextContent(tmpFT);
			    isInList = true;
			}				
		    }
		    if (!isInList){
			nodeListToRemove.add(nl.item(i));
		    }
		}
		for (int i=0;i<nodeListToRemove.size();i++){
		    nodeListToRemove.get(i).getParentNode().removeChild(nodeListToRemove.get(i));		
		}



	    }
	    if ("DescribeFeatureType".equalsIgnoreCase(operation)){
		NodeList nl = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs", "TypeName");
		for (int i=0;i<nl.getLength();i++){	    
		    boolean isInList = false;
		    for (int j=0;j<featureTypeListToKeep.size();j++){
			String tmpFT = nl.item(i).getTextContent();
			if (tmpFT!=null){
			    String [] s = tmpFT.split(":");
			    tmpFT = s[s.length-1];
			}
			if (tmpFT.startsWith(getRemoteServerInfo(iServer).getPrefix()) || getRemoteServerInfo(iServer).getPrefix().length()==0){			
			    tmpFT = tmpFT.substring((getRemoteServerInfo(iServer).getPrefix()).length());			
			}

			if (tmpFT.equals(featureTypeListToKeep.get(j))){
			    nl.item(i).setTextContent(tmpFT);
			    isInList = true;
			}				
		    }
		    if (!isInList){
			nodeListToRemove.add(nl.item(i));

		    }


		}
		for (int i=0;i<nodeListToRemove.size();i++){
		    nodeListToRemove.get(i).getParentNode().removeChild(nodeListToRemove.get(i));		
		}
	    }
	    OutputStream fluxSortie = 	new ByteArrayOutputStream();

	    LSSerializer serialiseur = implLS.createLSSerializer();
	    LSOutput sortie = implLS.createLSOutput();
	    sortie.setEncoding("UTF-8");
	    //sortie.setSystemId();
	    sortie.setByteStream(fluxSortie);
	    serialiseur.write(documentMaster, sortie);
	    fluxSortie.flush();
	    fluxSortie.close();
	    return fluxSortie.toString();
	}catch(Exception e){
	    e.printStackTrace();	    
	}
	return paramUrl;
    }
    /**
     * @param featureTypeListToKeep
     * @return
     */
    private String removeTypesFromGetUrl(List<String> featureTypeListToKeep,String paramUrl) {
	String [] fields = paramUrl.split("&");
	for (int i=0;i<fields.length;i++){
	    String []keyValue = fields[i].split("=");
	    if ("TYPENAME".equalsIgnoreCase(keyValue[0])){
		fields[i]=keyValue[0]+"=";
		for(int j = 0;j<featureTypeListToKeep.size();j++){
		    fields[i]=fields[i]+ featureTypeListToKeep.get(j)+","; 
		}
		if (fields[i].endsWith(",")){
		    fields[i]= fields[i].substring(0,fields[i].length()-1);
		}
		break;
	    } 	    	    
	}	
	paramUrl="";
	for (int i=0;i<fields.length;i++){
	    paramUrl=paramUrl+fields[i]+"&";	        	    
	}
	paramUrl= paramUrl.substring(0,paramUrl.length()-1);

	return paramUrl;
    }

//************************************************************************************************************************************************************************************************************
//************************************************************************************************************************************************************************************************************	
    public void transform(String version,String currentOperation,  HttpServletRequest req, HttpServletResponse resp) 
		{
		try 
			{
		    String userXsltPath = getConfiguration().getXsltPath();

		    if(req.getUserPrincipal() != null)
				{
				userXsltPath=userXsltPath+"/"+req.getUserPrincipal().getName()+"/";
				} 

		    userXsltPath = userXsltPath+"/"+version+"/"+currentOperation+".xsl";
		    String globalXsltPath = getConfiguration().getXsltPath()+"/"+version+"/"+currentOperation+".xsl";;

		    File xsltFile = new File(userXsltPath);
		    boolean isPostTreat = false;	    
		    if(!xsltFile.exists())
				{	
				dump("User postreatment file "+xsltFile.toString()+" does not exist");
				xsltFile = new File(globalXsltPath);
				if (xsltFile.exists())
					{
					isPostTreat=true;		    
					}
				else
					{
					dump("Global postreatment file "+xsltFile.toString()+" does not exist");
					}
				}
			else
				{
				isPostTreat=true;
				}
			
//***********************************************************************************************************************!!!!********************************************************************************************

		    // Transforms the results using a xslt before sending the response
		    // back	    

		    InputStream xml = null;//new FileInputStream(filePathList.get(0));
		    TransformerFactory tFactory = TransformerFactory.newInstance();

		    File tempFile = null;
		    OutputStream tempFos = null;	    	  	    	    

		    Transformer transformer = null;


		    if (currentOperation != null)
				{
				if (currentOperation.equals("GetCapabilities"))
					{			    
				    List<File> tempFileCapaList = new Vector<File>();

				    for (int i = 0;i<getRemoteServerInfoList().size();i++)
						{

						tempFile = createTempFile("transform_GetCapabilities"+UUID.randomUUID().toString(), ".xml");
						tempFos = new FileOutputStream(tempFile);
						ByteArrayInputStream xslt = null;

						xslt = new ByteArrayInputStream(buildCapabilitiesXSLT(req,i).toString().getBytes());

						transformer = tFactory.newTransformer(new StreamSource(xslt));
						//Write the result in a temporary file
						xml = new BufferedInputStream(new FileInputStream(filePathList.get(i)));
						transformer.transform(new StreamSource(xml), new StreamResult(tempFos));		     
						tempFos.close();
						tempFileCapaList.add(tempFile);
						}		   		    
				    tempFile = mergeCapabilities(tempFileCapaList);

					}
				else if(currentOperation.equals("DescribeFeatureType"))
					{
					if (hasPolicy)
						{
					    List<File> tempFileDescribeType = new Vector<File>();
					    for (int j = 0;j<getRemoteServerInfoList().size();j++)
							{
							StringBuffer WFSDescribeFeatureType = new StringBuffer ();
							WFSDescribeFeatureType.append("<xsl:stylesheet version=\"1.00\"  xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:ogcwfs=\"http://www.opengis.net/wfs\" xmlns:gml=\"http://www.opengis.net/gml\"  xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
							//On récupère le gml
							InputStream dataSourceInputStream = new FileInputStream(filePathList.get(j));			
							Schema schema = org.geotools.xml.SchemaFactory.getInstance(null, dataSourceInputStream);

							ComplexType[] ct = schema.getComplexTypes();

							for (int i =0;i<ct.length;i++)
								{
							    String tmpFT = ct[i].getName();
							    if (tmpFT!=null)
									{
									String [] s = tmpFT.split(":");
									tmpFT = s[s.length-1];
									}

							    if (isFeatureTypeAllowed(tmpFT, getRemoteServerUrl(j)))
									{
									org.geotools.xml.schema.Element[] elem = ct[i].getChildElements();
									for (int k=0;k<elem.length;k++)
										{				    
									    if (!isAttributeAllowed(getRemoteServerUrl(j),tmpFT,elem[k].getName()))
											{
											WFSDescribeFeatureType.append("<xsl:template match=\"//xsd:complexType[@name ='"+ct[i].getName()+"']//xsd:element[@name='"+elem[k].getName()+"']\">"); 
											WFSDescribeFeatureType.append("</xsl:template>");
											}			       
										}					
									}
								else
									{
									WFSDescribeFeatureType.append("<xsl:template match=\"//xsd:complexType[@name ='"+ct[i].getName()+"']\">"); 
									WFSDescribeFeatureType.append("</xsl:template>");
									WFSDescribeFeatureType.append("<xsl:template match=\"//xsd:element[@name ='"+ct[i].getName()+"']\">"); 
									WFSDescribeFeatureType.append("</xsl:template>");
								    }    
								}			
							WFSDescribeFeatureType.append("  <!-- Whenever you match any node or any attribute -->");
							WFSDescribeFeatureType.append("<xsl:template match=\"node()|@*\">");
							WFSDescribeFeatureType.append("<!-- Copy the current node -->");
							WFSDescribeFeatureType.append("<xsl:copy>");
							WFSDescribeFeatureType.append("<!-- Including any attributes it has and any child nodes -->");
							WFSDescribeFeatureType.append("<xsl:apply-templates select=\"@*|node()\"/>");
							WFSDescribeFeatureType.append("</xsl:copy>");
							WFSDescribeFeatureType.append("</xsl:template>");

							WFSDescribeFeatureType.append("</xsl:stylesheet>");
							
							File tempFileXslt = createTempFile("transform_DescribeFeatureType_xslt"+UUID.randomUUID().toString(), ".xml");
							PrintWriter bwrite = new PrintWriter(new BufferedWriter(new FileWriter(tempFileXslt)));
							bwrite.write(WFSDescribeFeatureType.toString());
							bwrite.flush();
							bwrite.close();
							xml = new BufferedInputStream(new FileInputStream(filePathList.get(j)));
							tempFile = createTempFile("transform_DescribeFeatureType"+UUID.randomUUID().toString(), ".xml");
							tempFos = new FileOutputStream(tempFile);
							ByteArrayInputStream xslt = null;
							xslt = new ByteArrayInputStream(WFSDescribeFeatureType.toString().getBytes());
							transformer = tFactory.newTransformer(new StreamSource(xslt));
							//Write the result in a temporary file
							transformer.transform(new StreamSource(xml), new StreamResult(tempFos));		     
							tempFos.close();
							tempFileDescribeType.add(tempFile);
						    }
					    tempFile = mergeDescribeFeatureType(tempFileDescribeType);
						}
					}
				else if (currentOperation.equals("GetFeature"))
					{
					dump("CurrentOperation GetFeature");
					
				    //On récupère le srs
				    if (hasPolicy)
						{
						dump("GetFeature hasPolicy");
						
						List<File> tempGetFeatureFile = new Vector();
						WFSGetFeatureRenameFt.append("<xsl:stylesheet version=\"1.00\" xmlns:ogcwfs=\"http://www.opengis.net/wfs\" xmlns:gml=\"http://www.opengis.net/gml\"  xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n");
						for (int iServer = 0;iServer<getRemoteServerInfoList().size();iServer++)
							{
							dump("GetFeature begin for loop");
//**************************************************************************************************************************************************************************
							
					   	 	//DataInputStream dis = new DataInputStream(new FileInputStream(filePathList.get(iServer)));
							BufferedReader  dis = new BufferedReader(new FileReader(filePathList.get(iServer)));
							boolean breakOut = false;
							int bufSize = 512;
							int srsIndex = 1;
							char[] cbuf = new char[bufSize];
							String s=null;
							String srsSource = null;
							dump("GetFeature begin srsName extract");
							if(dis.ready() && dis.markSupported())
								{
								dis.mark(bufSize+1);
								while(dis.read(cbuf,0,bufSize) != -1)
									{
									s= new String(cbuf);
									if ((srsIndex=s.indexOf("srsName"))>0)
										{
										dis.reset();
										dis.skip(srsIndex); // index of 'srsName' in  the buffer
										dis.mark(bufSize+1);
										dis.read(cbuf,0,bufSize);
										srsSource = new String(cbuf);
									    if (srsSource.indexOf("\"")>0)
											{
									    	srsIndex = srsSource.indexOf("\"")+1;
										    srsSource = srsSource.substring(srsSource.indexOf("\"")+1);
										    if (srsSource.indexOf("\"")>0)
										    	{
										    	srsSource = srsSource.substring(0,srsSource.indexOf("\""));
										    	break;
										    	}
										    else
										    	{						    	
												dis.reset();
												dis.skip(srsIndex); // index of first '"' in  the buffer
												dis.mark(bufSize+1);
												srsSource = "";
												while(dis.read(cbuf,0,bufSize) != -1)
													{
													s = new String(cbuf);
													srsSource = srsSource.concat(s);
													if (srsSource.indexOf("\"")>0)
												    	{
												    	srsSource = srsSource.substring(0,srsSource.indexOf("\""));
												    	breakOut = true;
												    	break;
												    	}
													}
										    	}
											}
										else if (srsSource.indexOf("\'")>0)
											{
											srsIndex = srsSource.indexOf("\'")+1;
											srsSource = srsSource.substring(srsSource.indexOf("\'")+1);
										    if (srsSource.indexOf("\'")>0)
										    	{
										    	srsSource = srsSource.substring(0,srsSource.indexOf("\'"));
										    	break;
										    	}
										    else
									    		{
												dis.reset();
												dis.skip(srsIndex); // index of first "'" in  the buffer
												dis.mark(bufSize+1);
												srsSource = "";
												while(dis.read(cbuf,0,bufSize) != -1)
													{
													s = new String(cbuf);
													srsSource = srsSource.concat(s);
													if (srsSource.indexOf("\'")>0)
												    	{
												    	srsSource = srsSource.substring(0,srsSource.indexOf("\'"));
												    	breakOut = true;
												    	break;
												    	}
													}
										    	}
											}
										}
									if(breakOut == false)
										{
										dis.reset();
										dis.skip(bufSize-8); // 8 because 'srsName' have 7 char
										dis.mark(bufSize+1);
										srsSource = null;
										}
									else
										{
										break;
										}
									}
								}
							System.out.println(srsSource);
							dis.close();
							dump("GetFeature end srsName extract");
//**************************************************************************************************************************************************************************
							
							String tempFileName = "transform_GetFeature"+UUID.randomUUID().toString();
						    tempFile = createTempFile(tempFileName, ".xml");			    
							
							dump("GetFeature tempFile created");
							
						    tempFos = new FileOutputStream(tempFile);
							
					//*******************************************************************************************************************************************
							dump("GetFeature tempFos created");
							
						    ByteArrayInputStream xslt = null;
						    List<String> featureTypeListToRemove = null;
						    List<String> featureTypeListToKeep = null;

						    xslt = new ByteArrayInputStream(buildGetFeatureXSLT(iServer).toString().getBytes());

						    XMLReader xmlReader = XMLReaderFactory.createXMLReader();
						    String user =(String)getUsername(getRemoteServerUrl(iServer));
						    String password = (String)getPassword(getRemoteServerUrl(iServer));
						    ResourceResolver rr = null;
						    if (user!=null && user.length()>0)
								{			
								rr = new ResourceResolver(user,password);
								xmlReader.setEntityResolver(rr);
								
								dump("GetFeature xmlReader set");
								}
						    // END Added to hook in my EntityResolver					  
						    xml = new BufferedInputStream(new FileInputStream(filePathList.get(iServer)));
						    SAXSource saxSource = new SAXSource(xmlReader,new InputSource(xml));
						    transformer = tFactory.newTransformer(new StreamSource(xslt));
						    //write the tempFile via tempFos
							transformer.transform(saxSource, new StreamResult(tempFos));
							
							tempFos.close();
							
							dump("GetFeature tempFos closed");
					//*******************************************************************************************************************************************
/*
							BufferedReader  dis = new BufferedReader(new FileReader(tempFileName+".xml"));
							String s = null;
							String srsSource = null;
							
							//Extract the srsName GML attribute value. Example of searched value: http://www.opengis.net/gml/srs/epsg.xml#4181
							while ((s = dis.readLine()) != null)
								{
								dump("GetFeature begin while loop");
								
								if (s.indexOf("srsName")>0)
									{
									dump("GetFeature if index of srsName");
								    srsSource= s.substring(s.indexOf("srsName"));
									dump("GetFeature substring at index");
								    if (srsSource.indexOf("\"")>0)
										{
										dump("GetFeature if index of bkslash 1");
									    srsSource = srsSource.substring(srsSource.indexOf("\"")+1);
										dump("GetFeature substring at index bkslash 1");
									    srsSource = srsSource.substring(0,srsSource.indexOf("\""));
										dump("GetFeature substring at index bkslash 2");
										}
									else
										{
										dump("GetFeature else index of bkslash 1");
										if (srsSource.indexOf("\'")>0)
											{
											dump("GetFeature if index of bkslash 2");
											srsSource = srsSource.substring(srsSource.indexOf("\'")+1);
											dump("GetFeature substring at index bkslash 1");
											srsSource = srsSource.substring(0,srsSource.indexOf("\'"));
											dump("GetFeature substring at index bkslash 2");
											}
										}
									dump("GetFeature break while loop");									
									break;
									}									
								dump("GetFeature end while loop");
								}
							dis.close();
*/
								
							String filter= null;			
							if (featureTypePathList.get(iServer).length()>0)
								{
								filter =getFeatureTypeLocalFilter(getRemoteServerUrl(iServer), featureTypePathList.get(iServer));
								dump("GetFeature filter get");
								}
							// filter =getFeatureTypeFilter(getRemoteServerUrl(iServer));


						    Map hints = new HashMap();		
						    hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);

						    GMLFeatureCollection  doc =null;
						    if (user!=null && user.length()>0)
								{
								doc = (GMLFeatureCollection)DocumentFactory.getInstance(tempFile.toURI(),hints,Level.WARNING,user,password);
								}
							else
								doc = (GMLFeatureCollection)DocumentFactory.getInstance(tempFile.toURI(),hints,Level.WARNING);				     

						    File tempFile2 = createTempFile("transform_GetFeature_2_"+UUID.randomUUID().toString(), ".xml");

						    tempFos = new FileOutputStream(tempFile2);
							
							dump("GetFeature tempFos created");
						    
						    if (filter !=null)
								{
								dump(filter);
								}
//***********************************************************************************************************************!!!!********************************************************************************************				    
							filterFC( tempFos, filter,doc,getServletUrl(req),srsSource);
							if (filter!=null)
								{
								tempFos.close();
								
								dump("GetFeature tempFos closed");
								
								if (tempFile!=null) tempFile.delete();
									tempFile = tempFile2;    
								}

							tempGetFeatureFile.add(tempFile);
							
							dump("GetFeature end for loops");
							}
						
						dump("GetFeature begin of append");
						WFSGetFeatureRenameFt.append("  <!-- Whenever you match any node or any attribute -->");
						WFSGetFeatureRenameFt.append("<xsl:template match=\"node()|@*\">\n");
						WFSGetFeatureRenameFt.append("<!-- Copy the current node -->\n");
						WFSGetFeatureRenameFt.append("<xsl:copy>\n");
						WFSGetFeatureRenameFt.append("<!-- Including any attributes it has and any child nodes -->\n");
						WFSGetFeatureRenameFt.append("<xsl:apply-templates select=\"@*|node()\"/>\n");
						WFSGetFeatureRenameFt.append("</xsl:copy>\n");
						WFSGetFeatureRenameFt.append("</xsl:template>\n");
						WFSGetFeatureRenameFt.append("</xsl:stylesheet>");
						
						File tempFileXslt = createTempFile("transform_GetFeature_xslt"+UUID.randomUUID().toString(), ".xml");
						PrintWriter bwrite = new PrintWriter(new BufferedWriter(new FileWriter(tempFileXslt)));
						bwrite.write(WFSGetFeatureRenameFt.toString());
						bwrite.flush();
						bwrite.close();
						
						dump("GetFeature begin of merge");
						tempFile = mergeGetFeatures(tempGetFeatureFile);
						dump("GetFeature end of merge");
						}
					}
				/*
				 * if a xslt file exists then 
				 * post-treat the response
				 */				
				if (isPostTreat)
					{
					dump("GetFeature begin is PostTreat");
				    PrintWriter out = resp.getWriter();
				    transformer = tFactory.newTransformer(new StreamSource(xsltFile));
				    if (tempFile !=null) transformer.transform(new StreamSource(tempFile), new StreamResult(out));
				    else transformer.transform(new StreamSource(filePathList.get(0)), new StreamResult(out)); 
				    //delete the temporary file
				    tempFile.delete();
				    out.close();
				    //the job is done. we can go out
				    dump("GetFeature the job is done. we can go out");
				    return;
					}
				}

		    //No post rule to apply. 
		    //Copy the file result on the output stream
		    
		    resp.setContentType("text/xml");
		    
		    InputStream is = null;
		    dump("GetFeature 1");
		    if (tempFile == null )
				{
		    	dump("GetFeature if 1");
				is  = new FileInputStream(filePathList.get(0));
				resp.setContentLength((int)new File(filePathList.get(0)).length());
				}
		    else
				{
		    	dump("GetFeature else 1");
				is = new FileInputStream(tempFile);
				resp.setContentLength((int)tempFile.length());
				}
		    
		    
		    //OutputStream os = resp.getOutputStream();	    
		    dump("GetFeature 2");	    
		    BufferedOutputStream os = new BufferedOutputStream( resp.getOutputStream() );
		      	     
		    byte byteRead[] = new byte[ 32768 ];
		    int index = is.read( byteRead, 0, 32768 );
		    dump("GetFeature 2b");
		    try 
				{		
				while(index != -1) 
					{			    
				    os.write( byteRead, 0, index );
				    index = is.read( byteRead, 0, 32768 );		
					}		
				os.flush();
				is.close();
				} 
			catch(Exception e)
				{
				e.printStackTrace();
				dump("BufferedOutputStream ERROR",e.getMessage()+" "+e.getLocalizedMessage()+" "+e.getCause());
				dump("BufferedOutputStream ERROR",e.toString());
				}
			finally
				{		
				DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
				Date d = new Date();	
				dump("SYSTEM","ClientResponseDateTime",dateFormat.format(d));		

				if (tempFile !=null)
					{
					dump("SYSTEM","ClientResponseLength",tempFile.length());
					tempFile.delete();	
					}
				}
			dump("GetFeature 3");
			}
		catch (Exception e)
			{
		    e.printStackTrace();
		    dump("Transform ERROR",e.getMessage()+" "+e.getLocalizedMessage()+" "+e.getCause());
		    dump("Transform ERROR",e.toString());
			}
	}

//************************************************************************************************************************************************************************************************************
//************************************************************************************************************************************************************************************************************
    private File mergeGetFeatures(List<File> tempGetFeaturesList){
	if (tempGetFeaturesList.size() == 0) return null;



	try{
		dump("mergeGetFeatures enter try");
	    File fMaster = tempGetFeaturesList.get(0);
	    DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
	    db.setNamespaceAware(true);
	    Document documentMaster = db.newDocumentBuilder().parse(fMaster);
	    DOMImplementationLS implLS = null;
	    if(documentMaster.getImplementation().hasFeature("LS", "3.0"))
			{
			implLS = (DOMImplementationLS)
			documentMaster.getImplementation();
			}
	    else 
			{ 
			DOMImplementationRegistry enregistreur = 
		    DOMImplementationRegistry.newInstance();
			implLS = (DOMImplementationLS)
			enregistreur.getDOMImplementation("LS 3.0");
			}
	    if(implLS == null)
			{
			dump("Error", "DOM Load and Save not Supported. Multiple server is not allowed");
			return fMaster;
			}
	    NodeList nlMaster = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs","FeatureCollection");
	    Node ItemMaster = nlMaster.item(0);
	    for (int i=1;i<tempGetFeaturesList.size();i++)
			{
			File fChild = tempGetFeaturesList.get(i);
			Document documentChild = db.newDocumentBuilder().parse(fChild);
			NodeList nlFeatureMember = documentChild.getElementsByTagNameNS("http://www.opengis.net/gml","featureMember");
			for (int j=0;j<nlFeatureMember.getLength();j++)
				{		    		    
				ItemMaster.insertBefore(documentMaster.importNode(nlFeatureMember.item(j).cloneNode(true), true), null);
				}	    
			}

	    File f = createTempFile("mergeGetFeatures_f_"+UUID.randomUUID().toString(),".xml");
	    FileOutputStream fluxSortie = new FileOutputStream(f);
	    LSSerializer serialiseur = implLS.createLSSerializer();
	    LSOutput sortie = implLS.createLSOutput();
	    sortie.setEncoding("UTF-8");
	    sortie.setSystemId(f.toString());
	    sortie.setByteStream(fluxSortie);
	    serialiseur.write(documentMaster, sortie);
	    fluxSortie.flush();
	    fluxSortie.close();
	    
	    dump("mergeGetFeatures before XML reader");
	    XMLReader xmlReader = XMLReaderFactory.createXMLReader();
	    String user =(String)getUsername(getRemoteServerUrl(0));
	    String password = (String)getPassword(getRemoteServerUrl(0));
	    ResourceResolver rr = null;
	    if (user!=null && user.length()>0)
			{
			dump("mergeGetFeatures before resolver");
			rr = new ResourceResolver(user,password);
			xmlReader.setEntityResolver(rr);
			}
	    
	    dump("mergeGetFeatures before InputStream");
	    InputStream xslt = new ByteArrayInputStream(WFSGetFeatureRenameFt.toString().getBytes());
	    InputStream xml = new BufferedInputStream(new FileInputStream(f));
		//xmlReader has detect user and password!
	    SAXSource saxSource = new SAXSource(xmlReader,new InputSource(xml));
	    File f2 = createTempFile("mergeGetFeatures_f2_"+UUID.randomUUID().toString(),".xml");
	    FileOutputStream tempFos = new FileOutputStream(f2);
	    TransformerFactory tFactory = TransformerFactory.newInstance();
		//InputStream xslt = new ByteArrayInputStream(WFSGetFeatureRenameFt.toString().getBytes());
	    Transformer  transformer = tFactory.newTransformer(new StreamSource(xslt));
	    dump("mergeGetFeatures before transformer.transform test4");
		//this line is the problem!!!
	    transformer.transform(saxSource, new StreamResult(tempFos));
		//transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
	    dump("mergeGetFeatures after transformer.transform");
		
	    tempFos.close();
	    
	    return f2;
		}
		catch(Exception e)
		{
	    e.printStackTrace();
	    dump("ERROR on mergeGetFeatures",e.getMessage());
	    return null;
		}
    }
    
    private File mergeDescribeFeatureType(List<File> tempFileDescribeType){
	if (tempFileDescribeType.size() == 0) return null;
	try{
	    File fMaster = tempFileDescribeType.get(0);
	    DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
	    db.setNamespaceAware(true);
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
	    NodeList nlMaster = documentMaster.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema","schema");
	    Node ItemMaster = nlMaster.item(0);

	    NodeList nlElements = documentMaster.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema","element");
	    for (int i=0;i<nlElements.getLength();i++){
		if (nlElements.item(i).getAttributes().getNamedItem("substitutionGroup")!=null){
		    if("gml:_Feature".equals(nlElements.item(i).getAttributes().getNamedItem("substitutionGroup").getTextContent())){
			String origName = nlElements.item(i).getAttributes().getNamedItem("name").getTextContent();
			nlElements.item(i).getAttributes().getNamedItem("name").setTextContent(getRemoteServerInfo(0).getPrefix()+origName);
		    }
		}

	    }	    

	    for (int i=1;i<tempFileDescribeType.size();i++){				
		Document documentChild = db.newDocumentBuilder().parse(tempFileDescribeType.get(i));

		NodeList nl1 = documentChild.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema","schema");
		NodeList nl2  = nl1.item(0).getChildNodes();		

		nlElements = documentChild.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema","element");
		for (int j=0;j<nlElements.getLength();j++){
		    if (nlElements.item(j).getAttributes().getNamedItem("substitutionGroup")!=null){
			if("gml:_Feature".equals(nlElements.item(j).getAttributes().getNamedItem("substitutionGroup").getTextContent())){
			    String origName = nlElements.item(j).getAttributes().getNamedItem("name").getTextContent();
			    nlElements.item(j).getAttributes().getNamedItem("name").setTextContent(getRemoteServerInfo(i).getPrefix()+origName);
			}
		    }

		}



		for (int j=0;j<nl2.getLength();j++){
		    ItemMaster.insertBefore(documentMaster.importNode(nl2.item(j).cloneNode(true), true), null);
		}

	    }

	    File f = createTempFile("mergeDescribeFeatureType_f_"+UUID.randomUUID().toString(),".xml");

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
    private File mergeCapabilities(List<File> tempFileCapa) {

	if (tempFileCapa.size() == 0) return null;
	try{
	    File fMaster = tempFileCapa.get(0);
	    DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
	    db.setNamespaceAware(true);
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
	    NodeList nlMaster = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs","FeatureTypeList");
	    Node ItemMaster = nlMaster.item(0);

	    for (int i=1;i<tempFileCapa.size();i++){

		Document documentChild = db.newDocumentBuilder().parse(tempFileCapa.get(i));
		NodeList nl = documentChild.getElementsByTagNameNS("http://www.opengis.net/wfs","FeatureType");		    

		for (int j=0;j<nl.getLength();j++){		
		    ItemMaster.insertBefore(documentMaster.importNode(nl.item(j).cloneNode(true), true), null);
		}
	    }	        	        	        	        

	    File f = createTempFile("mergeCapabilities_f_"+UUID.randomUUID().toString(),".xml");

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

    private FeatureType parseDescribeFeatureTypeResponse( String typeName, Schema schema ) throws SAXException {
	org.geotools.xml.schema.Element[] elements = schema.getElements();

	if (elements == null) {
	    return null; // not found
	}

	org.geotools.xml.schema.Element element = null;

	String ttname = typeName.substring(typeName.indexOf(":") + 1);

	for (int i = 0; (i < elements.length) && (element == null); i++) {
	    // HACK -- namspace related -- should be checking ns as opposed to removing prefix
	    if (typeName.equals(elements[i].getName())
		    || ttname.equals(elements[i].getName())) {
		element = elements[i];
	    }
	}

	if (element == null) {
	    return null;
	}

	FeatureType ft = GMLComplexTypes.createFeatureType(element);


	return ft;
    }




    /**
     * @param req
     * @return
     */
    private StringBuffer buildGetFeatureXSLT(int remoteServerIndex) {

	StringBuffer WFSGetFeature = new StringBuffer ();		
	StringBuffer header = new StringBuffer();

	WFSGetFeature.append("<xsl:stylesheet version=\"1.00\" xmlns:ogcwfs=\"http://www.opengis.net/wfs\" xmlns:gml=\"http://www.opengis.net/gml\"  xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n");


	try{


	    int nsI = 0;
	    int nsI2 = 0;

	    URL url = new URL(getRemoteServerUrl(remoteServerIndex)+"?VERSION=1.0.0&REQUEST=DescribeFeatureType&SERVICE=WFS");
	    boolean isAuthenticated=false;	
	    URLConnection hpcon = url.openConnection();		    
	    String encoding =null;
	    if (getUsername(getRemoteServerUrl(remoteServerIndex)) != null && getPassword(getRemoteServerUrl(remoteServerIndex)) != null) {
		String userPassword = getUsername(getRemoteServerUrl(remoteServerIndex)) + ":" + getPassword(getRemoteServerUrl(remoteServerIndex));
		encoding = new sun.misc.BASE64Encoder()
		.encode(userPassword.getBytes());
		isAuthenticated=true;
	    }		    
	    if (isAuthenticated==true){
		hpcon.setRequestProperty("Authorization", "Basic " + encoding);
	    }

	    Schema schema = org.geotools.xml.SchemaFactory.getInstance(null,hpcon.getInputStream() );		    		    
	    ComplexType[] ct = schema.getComplexTypes();

	    for (int i =0;i<ct.length;i++){
		if (isFeatureTypeAllowed(ct[i].getName(), getRemoteServerUrl(remoteServerIndex))){
		    String tmpFT = ct[i].getName();
		    if (tmpFT!=null){
			String [] s = tmpFT.split(":");
			tmpFT = s[s.length-1];
		    }

		    org.geotools.xml.schema.Element[] elem = ct[i].getChildElements();
		    for (int j=0;j<elem.length;j++){				

			if (!isAttributeAllowed(getRemoteServerUrl(remoteServerIndex), tmpFT, elem[j].getName())){

			    WFSGetFeature.append("<xsl:template xmlns:"+"au"+nsI+"=\""+ct[i].getNamespace()+"\" match=\"//"+"au"+nsI+":"+ct[i].getName()+"/"+"au"+nsI+":"+elem[j].getName()+"\">\n"); 
			    WFSGetFeature.append("</xsl:template>\n");
			    nsI++;
			}			       
		    }			    		    

		    if (getRemoteServerInfo(remoteServerIndex).getPrefix().length()>0){
			WFSGetFeatureRenameFt.append("<xsl:template xmlns:"+"au"+nsI2+"=\""+ct[i].getNamespace()+"\" match=\"//gml:featureMember/au"+nsI2+":"+ct[i].getName()+"\">\n");
			WFSGetFeatureRenameFt.append("<au"+nsI2+":"+getRemoteServerInfo(remoteServerIndex).getPrefix()+ct[i].getName()+" xmlns:au"+nsI2+"=\""+ct[i].getNamespace()+"\">");
			WFSGetFeatureRenameFt.append("<xsl:apply-templates/>");
			WFSGetFeatureRenameFt.append(" </au"+nsI2+":"+getRemoteServerInfo(remoteServerIndex).getPrefix()+ct[i].getName()+">\n");
			WFSGetFeatureRenameFt.append("</xsl:template>\n");
		    }
		    nsI2++;
		}
		else{
		    WFSGetFeature.append("<xsl:template xmlns:"+"au"+nsI2+"=\""+ct[i].getNamespace()+"\" match=\"//gml:featureMember\">\n");
		    WFSGetFeature.append("<xsl:if test = \"count(./au"+nsI2+":"+ct[i].getName()+")=0\" >\n");     
		    WFSGetFeature.append("<xsl:copy>\n");
		    WFSGetFeature.append("<xsl:apply-templates select=\"@*|node()\"/>\n");
		    WFSGetFeature.append("</xsl:copy>\n");
		    WFSGetFeature.append("</xsl:if>\n"); 
		    WFSGetFeature.append("</xsl:template>\n");
		    nsI2++;
		}

	    }	
	}catch(Exception e){
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}    
	WFSGetFeature.append("  <!-- Whenever you match any node or any attribute -->");
	WFSGetFeature.append("<xsl:template match=\"node()|@*\">\n");
	WFSGetFeature.append("<!-- Copy the current node -->\n");
	WFSGetFeature.append("<xsl:copy>\n");
	WFSGetFeature.append("<!-- Including any attributes it has and any child nodes -->\n");
	WFSGetFeature.append("<xsl:apply-templates select=\"@*|node()\"/>\n");
	WFSGetFeature.append("</xsl:copy>\n");
	WFSGetFeature.append("</xsl:template>\n");






	WFSGetFeature.append("</xsl:stylesheet>\n");		
	return WFSGetFeature;
    }


    public void filterFC( OutputStream os ,String customFilter,GMLFeatureCollection doc,String urlServlet,String srsDest) {

	try{
	    //File file = new File(filePath);	        

	    //GMLFeatureCollection  doc = (GMLFeatureCollection)DocumentFactory.getInstance(file.toURI(),null,Level.FINE);	     




	    Filter filter = null;
	    if (customFilter!=null)
	    	{
			InputStream is = new ByteArrayInputStream(customFilter.getBytes());		 
	
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();		       
			Document dom = db.parse(is);
			// first grab a filter node
			NodeList nodes = dom.getElementsByTagName("Filter");
			if (nodes.getLength() == 0)
				{
				nodes = dom.getElementsByTagNameNS("http://www.opengis.net/ogc","Filter");
				}
			for (int j = 0; j < nodes.getLength(); j++)
				{
			    Element filterNode = (Element) nodes.item(j);
			    NodeList list = filterNode.getChildNodes();
			    Node child = null;
	
			    for (int k = 0; k < list.getLength(); k++) 
			    	{
			    	child = list.item(k);
			    	if ((child == null) || (child.getNodeType() != Node.ELEMENT_NODE)) 
			    		{
			    		continue;
			    		}
			    	filter = FilterDOMParser.parseFilter(child);		                
			    	}
				}		
		    }


	    FeatureTransformer ft = new FeatureTransformer();
	    ft.setNamespaceDeclarationEnabled(true);		

	    ft.setCollectionPrefix(null);
	    ft.setGmlPrefixing(true);	    
	    ft.setIndentation(2);
	    FeatureCollection fc = null;
	    if (filter!=null){
		System.setProperty("org.geotools.referencing.forceXY", "true");
		//Transform the srs of the filter if needed. 
		String srsSource= customFilter.substring(customFilter.indexOf("srsName"));
		if (srsSource.indexOf("\"")>0){
		srsSource = srsSource.substring(srsSource.indexOf("\"")+1);			
		srsSource = srsSource.substring(0,srsSource.indexOf("\""));	
		}else{
		    srsSource = srsSource.substring(srsSource.indexOf("\'")+1);			
			srsSource = srsSource.substring(0,srsSource.indexOf("\'"));
		}
		if (srsDest==null) srsDest = srsSource;

		CoordinateReferenceSystem sourceCRS = CRS.decode(srsSource);
		CoordinateReferenceSystem targetCRS = CRS.decode(srsDest);	    		
		if (!srsSource.equals(srsDest)){

		    /*AffineTransform t=AffineTransform.getTranslateInstance(0,0);
		    DefaultMathTransformFactory fac=new DefaultMathTransformFactory();
		    MathTransform mt = fac.createAffineTransform(new GeneralMatrix(t));
		     */

		    FilterTransformer filterTransformer = new FilterTransformer(CRS.findMathTransform(sourceCRS, targetCRS));
		    //Due to an issue in GeoTools
		    //MinX MinY MaxX and MaxY are not set for the BBOXImpl.
		    //Do it ourself
		    if (filter instanceof org.geotools.filter.spatial.BBOXImpl){
			Object obj = null;
			if (((org.geotools.filter.spatial.BBOXImpl)filter).getRightGeometry() instanceof org.geotools.filter.LiteralExpressionImpl){
			    obj = (((org.geotools.filter.LiteralExpressionImpl)((org.geotools.filter.spatial.BBOXImpl)filter).getRightGeometry()).getValue());


			}
			if (((org.geotools.filter.spatial.BBOXImpl)filter).getLeftGeometry() instanceof org.geotools.filter.LiteralExpressionImpl){
			    obj =(((org.geotools.filter.LiteralExpressionImpl)((org.geotools.filter.spatial.BBOXImpl)filter).getLeftGeometry()).getValue().getClass());	    
			}
			if (obj!=null && obj instanceof com.vividsolutions.jts.geom.Polygon){

			    ((org.geotools.filter.spatial.BBOXImpl)filter).setMinX(((com.vividsolutions.jts.geom.Polygon)obj).getEnvelopeInternal().getMinX());
			    ((org.geotools.filter.spatial.BBOXImpl)filter).setMinY(((com.vividsolutions.jts.geom.Polygon)obj).getEnvelopeInternal().getMinY());
			    ((org.geotools.filter.spatial.BBOXImpl)filter).setMaxY(((com.vividsolutions.jts.geom.Polygon)obj).getEnvelopeInternal().getMaxY());
			    ((org.geotools.filter.spatial.BBOXImpl)filter).setMaxX(((com.vividsolutions.jts.geom.Polygon)obj).getEnvelopeInternal().getMaxX());
			}
		    }
		    Filter filter2 = (Filter)filter.accept(filterTransformer, null);

		    fc = doc.subCollection(filter2);


		}else{
		    fc = doc.subCollection(filter);
		}

	    }
	    else fc = doc;

	    FeatureIterator it = fc.features();
	    int i=0;
	    String lastTypeName="";
	    while(it.hasNext()){
		Feature feature = it.next();

		if (!feature.getFeatureType().getTypeName().equals(lastTypeName)){
		    String prefix = "au"+i;
		    ft.getFeatureTypeNamespaces().declareNamespace(feature.getFeatureType(), prefix, feature.getFeatureType().getNamespace().toString());		
		    ft.setSrsName((String)feature.getDefaultGeometry().getUserData());
		    lastTypeName=feature.getFeatureType().getTypeName();
		}
		i++;

	    }

	    ft.transform(fc, os);

	}catch(Exception e){
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}

    }
}