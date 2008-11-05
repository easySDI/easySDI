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
package ch.depth.proxy.csw;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.io.StringBufferInputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Random;
import java.util.UUID;
import java.util.Vector;
import java.util.zip.CRC32;
import java.util.zip.GZIPInputStream;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.Unmarshaller;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.apache.commons.httpclient.HttpURL;
import org.jdom.Element;
import org.jdom.input.SAXBuilder;
import org.jdom.xpath.XPath;
import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import ch.depth.proxy.core.ProxyServlet;
import ch.depth.xml.documents.RemoteServerInfo;
import ch.depth.xml.handler.ConfigFileHandler;
import ch.depth.xml.handler.CswRequestHandler;
import ch.depth.xml.handler.GeonetworkSearchResultHandler;
import ch.depth.xml.handler.PolicyHandler;
import ch.depth.xml.handler.RequestHandler;

public class CSWProxyServlet extends ProxyServlet {

    private static final long serialVersionUID = 7288366261387138250L;
    private String[] CSWOperation = {"GetCapabilities","GetRecords","GetRecordById","Harvest","DescribeRecord","GetExtrinsicContent","Transaction"};



    public void init(ServletConfig config) 	
    throws ServletException{
	super.init(config);			
    }


    public void doPost(HttpServletRequest req, HttpServletResponse resp)
    throws ServletException, IOException {

	super.doPost(req, resp);
    }


    public void doGet(HttpServletRequest req, HttpServletResponse resp)
    throws ServletException, IOException {
	super.doGet(req,resp);

    }			

    protected StringBuffer buildCapabilitiesXSLT(HttpServletRequest req){

	try {
	    String user="";
	    if(req.getUserPrincipal() != null){
		user= req.getUserPrincipal().getName();
	    }

	    String url = getServletUrl(req);
	    List<String> permitedOperations = new Vector<String>();
	    List<String> deniedOperations = new Vector<String>();	    

	    //Fill the vectors with the corresponding information 
	    for (int i= 0;i<CSWOperation.length;i++){

		boolean isOperationPermited = false;

		if (isOperationAllowed(CSWOperation[i])){isOperationPermited = true;}

		if (isOperationPermited){
		    permitedOperations.add(CSWOperation[i]);
		    dump(CSWOperation[i]+" is permitted");
		}else{
		    deniedOperations.add(CSWOperation[i]);
		    dump(CSWOperation[i]+" is denied");
		}
	    }	    	     	    	     

	    return generateXSLTForCSWCapabilities200(url, deniedOperations, permitedOperations);	
	} catch (Exception e) {
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}

	//If something goes wrong, an empty stylesheet is returned.	
	StringBuffer sb = new StringBuffer();		
	return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>"); 
    }

    /***
     * Builds the xslt that will be applied against the csw capabilities version 2.00
     * The generated xslt will remove the unauthorized operations from the capabilities document and
     * will  replace the urls 
     * @param req the HttpServletRequest request
     * @return StringBuffer containing the xslt  
     */
    protected StringBuffer generateXSLTForCSWCapabilities200(String url, List<String>deniedOperations, List<String>permitedOperations){

	try {

	    StringBuffer CSWCapabilities200 = new StringBuffer ();		


	    CSWCapabilities200.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");


	    CSWCapabilities200.append("<xsl:template match=\"ows:Get\">");
	    CSWCapabilities200.append("<ows:Get>"); 
	    CSWCapabilities200.append("<xsl:attribute name=\"xlink:href\">");

	    CSWCapabilities200.append(url);				

	    CSWCapabilities200.append("</xsl:attribute>");
	    CSWCapabilities200.append("<xsl:attribute name=\"xlink:type\">");
	    CSWCapabilities200.append("<xsl:value-of select=\"@xlink:type\"/>");
	    CSWCapabilities200.append("</xsl:attribute>");
	    CSWCapabilities200.append("</ows:Get>");
	    CSWCapabilities200.append("</xsl:template>");
	    CSWCapabilities200.append("<xsl:template match=\"ows:Post\">");
	    CSWCapabilities200.append("<ows:Post>"); 
	    CSWCapabilities200.append("<xsl:attribute name=\"xlink:href\">");		

	    CSWCapabilities200.append(url);

	    CSWCapabilities200.append("</xsl:attribute>");
	    CSWCapabilities200.append("<xsl:attribute name=\"xlink:type\">");
	    CSWCapabilities200.append("<xsl:value-of select=\"@xlink:type\"/>");
	    CSWCapabilities200.append("</xsl:attribute>");
	    CSWCapabilities200.append("</ows:Post>");
	    CSWCapabilities200.append("</xsl:template>");



	    Iterator<String> it = permitedOperations.iterator();

	    boolean first=true;
	    while(it.hasNext()){
		if (first){
		    CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation\"></xsl:template>");
		    first=false;
		}
		String text = it.next();
		if (text !=null){
		    CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation[@name='");

		    CSWCapabilities200.append(text);

		    CSWCapabilities200.append("']\">");

		    CSWCapabilities200.append("<!-- Copy the current node -->");
		    CSWCapabilities200.append("<xsl:copy>");
		    CSWCapabilities200.append("<!-- Including any attributes it has and any child nodes -->");
		    CSWCapabilities200.append("<xsl:apply-templates select=\"@*|node()\"/>");
		    CSWCapabilities200.append("</xsl:copy>");

		    CSWCapabilities200.append("</xsl:template>");
		}							
	    }



	    it = deniedOperations.iterator();
	    while(it.hasNext()){
		CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation[@name='");

		CSWCapabilities200.append(it.next());

		CSWCapabilities200.append("']\"></xsl:template>");
	    }

	    if (permitedOperations.size() == 0 && deniedOperations.size()==0){
		CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation\"></xsl:template>");						
	    }

	    CSWCapabilities200.append("  <!-- Whenever you match any node or any attribute -->");
	    CSWCapabilities200.append("<xsl:template match=\"node()|@*\">");
	    CSWCapabilities200.append("<!-- Copy the current node -->");
	    CSWCapabilities200.append("<xsl:copy>");
	    CSWCapabilities200.append("<!-- Including any attributes it has and any child nodes -->");
	    CSWCapabilities200.append("<xsl:apply-templates select=\"@*|node()\"/>");
	    CSWCapabilities200.append("</xsl:copy>");
	    CSWCapabilities200.append("</xsl:template>");

	    CSWCapabilities200.append("</xsl:stylesheet>");		
	    return CSWCapabilities200;
	} catch (Exception e) {
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}

	//If something goes wrong, an empty stylesheet is returned.	
	StringBuffer sb = new StringBuffer();		
	return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>"); 
    }

    public void transform(String version,String currentOperation, HttpServletRequest req,
	    HttpServletResponse resp, List<String> filePathList) {

	if (isOperationAllowed(currentOperation)){
	    try {

		String userXsltPath = getConfiguration().getXsltPath();
		if(req.getUserPrincipal() != null){		
		    userXsltPath=userXsltPath+"/"+req.getUserPrincipal().getName()+"/";
		}	 

		userXsltPath = userXsltPath+"/"+version+"/"+currentOperation+".xsl";
		String globalXsltPath = getConfiguration().getXsltPath()+"/"+version+"/"+currentOperation+".xsl";;	    
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
		InputStream xml = new FileInputStream(filePathList.get(0));
		TransformerFactory tFactory = TransformerFactory.newInstance();

		File tempFile = null;
		FileOutputStream tempFos = null;


		Transformer transformer = null;

		if (currentOperation != null) {

		    if (currentOperation.equals("GetCapabilities")) {
			tempFile = createTempFile(UUID.randomUUID().toString(), ".xml");
			tempFos = new FileOutputStream(tempFile);

			ByteArrayInputStream xslt = null;
			xslt = new ByteArrayInputStream(buildCapabilitiesXSLT(req).toString().getBytes());
			transformer = tFactory.newTransformer(new StreamSource(xslt));
			//Write the result in a temporary file
			transformer.transform(new StreamSource(xml), new StreamResult(tempFos));		     
			tempFos.close();		   		    
		    }else{
			if("GetRecords".equals(currentOperation) || "GetRecordById".equals(currentOperation)){

			    if (areAllAttributesAllowedForMetadata(getRemoteServerUrl(0))){
				//Keep the metadata as it is
				tempFile = new File(filePathList.get(0));
			    }else{

				tempFile = createTempFile(UUID.randomUUID().toString(), ".xml");
				tempFos = new FileOutputStream(tempFile);

				InputStream xslt = null;
				xslt = new ByteArrayInputStream(generateXSLTForMetadata().toString().getBytes());
				//xslt = new FileInputStream(new File("d:\\xslt\\test.xsl"));

				transformer = tFactory.newTransformer(new StreamSource(xslt));
				//Write the result in a temporary file
				transformer.transform(new StreamSource(xml), new StreamResult(tempFos));		     
				tempFos.close();
			    }

			}else
			    tempFile = new File(filePathList.get(0)); 
		    }

		    /*
		     * if a xslt file exists then 
		     * post-treat the response
		     */				
		    if (isPostTreat){		    
			PrintWriter out = resp.getWriter();
			transformer = tFactory.newTransformer(new StreamSource(xsltFile));		    
			transformer.transform(new StreamSource(tempFile), new StreamResult(out));
			//delete the temporary file
			tempFile.delete();
			out.close();
			//the job is done. we can go out
			return;

		    }
		}

		//No post rule to apply. 
		//Copy the file result on the output stream
		OutputStream os = resp.getOutputStream();
		InputStream is = new FileInputStream(tempFile);

		int byteRead;
		try {
		    while((byteRead = is.read()) != -1) {  
			os.write(byteRead);
		    }
		} finally{
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


	    } catch (Exception e) {
		e.printStackTrace();
		dump("ERROR",e.getMessage());
	    }
	}else{
	    try{
		OutputStream os = resp.getOutputStream();
		os.write(generateOgcError("Operation not allowed").toString().getBytes());
		os.flush();
		os.close();
	    }
	    catch(Exception e){
		e.printStackTrace();
		dump("ERROR",e.getMessage());
	    }
	}
    }


    /* (non-Javadoc)
     * @see ch.depth.proxy.core.ProxyServlet#requestPreTreatmentGET(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    protected void requestPreTreatmentGET(HttpServletRequest req,
	    HttpServletResponse resp) {

	String currentOperation = null;
	String version = "000";
	String service = "";
	boolean sendRequest = true;
	Enumeration<String> parameterNames = req.getParameterNames();
	String paramUrl = "";	

	// Build the request to dispatch
	while (parameterNames.hasMoreElements()) {
	    String key = (String) parameterNames.nextElement();
	    String value = URLEncoder.encode(req.getParameter(key));
	    paramUrl = paramUrl + key + "=" + value + "&";
	    if (key.equalsIgnoreCase("Request")) {
		// Gets the requested Operation
		if (value.equalsIgnoreCase("capabilities")){
		    currentOperation = "GetCapabilities";
		}else{
		    currentOperation = value;
		}		
	    }
	}	    	    
	version= version.replaceAll("\\.", "");	    	    

	// Send the request to the remote server
	List<String> filePathList = new Vector<String>();
	String filePath = sendData("GET", getRemoteServerUrl(0), paramUrl);
	filePathList.add(filePath);
	transform(version,currentOperation,req, resp, filePathList);	 

    }


    /* (non-Javadoc)
     * @see ch.depth.proxy.core.ProxyServlet#requestPreTreatmentPOST(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    protected void requestPreTreatmentPOST(HttpServletRequest req,
	    HttpServletResponse resp) {
	try{
	    XMLReader xr = XMLReaderFactory.createXMLReader();
	    CswRequestHandler rh = new CswRequestHandler();
	    xr.setContentHandler(rh);	    

	    StringBuffer param = new StringBuffer();	    
	    String input;
	    BufferedReader in = new BufferedReader(new InputStreamReader(req
		    .getInputStream()));
	    while ((input = in.readLine()) != null) {
		param.append(input);
	    }	    

	    xr.parse(new InputSource(new InputStreamReader(
		    new ByteArrayInputStream(param.toString().getBytes()))));

	    String version = rh.getVersion();

	    String currentOperation =  rh.getOperation();


	    //In the case of transaction only one remote server is supported.
	    //We use the configuration of the first one.
	    //TODO :add a tag in the configuration file to set the default server.
	    RemoteServerInfo rsi = getRemoteServerInfo(0);
	    String transactionType = "ogc";
	    if (rsi !=null){
		transactionType =  rsi.getTransaction();
	    }

	    if (currentOperation.equalsIgnoreCase("Transaction")&&transactionType.equalsIgnoreCase("geonetwork")){

		if (rh.isTransactionInsert()){

		    String[] sourceName = new String[]{"info.xml", "metadata.xml"};
		    List<String> uuidList = rh.getUUIdListToInsert();
		    Iterator<String> it = uuidList.iterator();
		    int count = 0;
		    while (it.hasNext()){
			String uuid = it.next();


			TransformerFactory tFactory = TransformerFactory.newInstance();
			Transformer transformer = null;
			StringBuffer xslt = new StringBuffer();
			xslt.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" ");
			xslt.append(" xmlns:gmd=\"http://www.isotc211.org/2005/gmd\"");
			xslt.append(" xmlns:csw=\"http://www.opengis.net/cat/csw\">");
			xslt.append("<xsl:template match=\"csw:Insert\">");			    
			xslt.append("<xsl:copy-of select=\"@* | node()\">");	    		
			xslt.append("<xsl:apply-templates  select=\"@* | node()\"/>");	    
			xslt.append("</xsl:copy-of>");	    
			xslt.append("</xsl:template>");
			xslt.append("</xsl:stylesheet>");
			ByteArrayInputStream baisXsl = new ByteArrayInputStream(xslt.toString().getBytes());
			transformer = tFactory.newTransformer(new StreamSource(baisXsl));

			ByteArrayInputStream bais = new ByteArrayInputStream(param.toString().getBytes());
			System.out.println(param);
			//Write the result in a temporary file
			StringBuffer metadata = new StringBuffer();
			ByteArrayOutputStream baos = new ByteArrayOutputStream();	

			transformer.transform(new StreamSource(bais), new StreamResult(baos));
			metadata.append(baos.toString());


			StringBuffer[] sourceContent = new StringBuffer[]{generateInfoFileForMef(uuid, req.getRemoteAddr()),metadata};    
			ByteArrayOutputStream mefFileOutputStream = new ByteArrayOutputStream();			    
			ZipOutputStream out = new ZipOutputStream(mefFileOutputStream);
			// Compress the files
			for (int i=0; i<sourceName.length; i++) {				
			    CRC32 crc = new CRC32();
			    crc.update(sourceContent[i].toString().getBytes(), 0, sourceContent[i].toString().getBytes().length);
			    ZipEntry entry = new ZipEntry(sourceName[i]);
			    entry.setMethod(ZipEntry.STORED);
			    entry.setSize(sourceContent[i].toString().getBytes().length);
			    entry.setCrc(crc.getValue());
			    out.putNextEntry(entry);
			    out.write(sourceContent[i].toString().getBytes(), 0, sourceContent[i].toString().getBytes().length);
			    out.closeEntry();				  
			}    
			out.close();	


			//Search for the geonetwork id using the uuid
			StringBuffer response = send(rsi.getSearchServiceUrl()+"?any="+uuid,rsi.getLoginService());
			dump (response);			    
			InputStream is = new ByteArrayInputStream(response.toString().getBytes());

			XMLReader xrSearchResponse = XMLReaderFactory.createXMLReader();
			GeonetworkSearchResultHandler gnSearchHandler = new GeonetworkSearchResultHandler ();		 
			xrSearchResponse.setContentHandler(gnSearchHandler);
			xrSearchResponse.parse(new InputSource(is));
			List<String> listId = gnSearchHandler.getGeonetworkInternalId(uuid);

			//If a metadata already exists, remove it from Geonetwork 
			if (listId !=null){
			    Iterator<String> itId = listId.iterator();
			    while (itId.hasNext()){
				String s = itId.next();
				response = send(rsi.getDeleteServiceUrl()+"?id="+s,rsi.getLoginService());									 	 		 
			    }
			}
			//Send the generated mef dile				   
			InputStream mefFileInputStream = new ByteArrayInputStream(mefFileOutputStream.toByteArray());

			response = sendFile(rsi.getInsertServiceUrl(), mefFileInputStream, rsi.getLoginService(), "mefFile", uuid+".mef");
			System.out.println(response);			    			    
			sourceContent=null;
			count++;
		    }
		    sourceName = null;
		    StringBuffer cswResponse = new StringBuffer ();

		    cswResponse.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
		    cswResponse.append("<csw:TransactionResponse xmlns:csw=\"http://www.opengis.net/cat/csw\" xmlns:dc=\"http://www.purl.org/dc/elements/1.1/\" xmlns:dct=\"http://www.purl.org/dc/terms/\" xsi:schemaLocation=\"http://www.opengis.net/cat/csw http://localhost:8888/SpatialWS-SpatialWS-context-root/cswservlet?recordTypeId=1 \" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">");
		    cswResponse.append("<csw:TransactionSummary>");
		    cswResponse.append("<csw:totalInserted>"+count+"</csw:totalInserted>");
		    cswResponse.append("</csw:TransactionSummary>");
		    cswResponse.append("</csw:TransactionResponse>");			


		    OutputStream os = resp.getOutputStream();

		    InputStream is = new ByteArrayInputStream(cswResponse.toString().getBytes());
		    int byteRead;
		    try {
			while((byteRead = is.read()) != -1) {  
			    os.write(byteRead);
			}
		    } finally{
			os.flush();
			os.close();   			    
		    }
		    os=null;
		    is=null;
		}

		if (rh.isTransactionDelete()){
		    List<String> uuidList = rh.getUUidListToDelete();
		    Iterator<String> it = uuidList.iterator();
		    int count = 0;
		    while (it.hasNext()){
			String uuid = it.next();
			//Search for the geonetwork id using the uuid
			StringBuffer response = send(rsi.getSearchServiceUrl()+"?any="+uuid,rsi.getLoginService());
			dump (response);			    
			InputStream is = new ByteArrayInputStream(response.toString().getBytes());

			XMLReader xrSearchResponse = XMLReaderFactory.createXMLReader();
			GeonetworkSearchResultHandler gnSearchHandler = new GeonetworkSearchResultHandler ();		 
			xrSearchResponse.setContentHandler(gnSearchHandler);
			xrSearchResponse.parse(new InputSource(is));
			List<String> listId = gnSearchHandler.getGeonetworkInternalId(uuid);

			//If a metadata already exists, remove it from Geonetwork 
			if (listId !=null){
			    Iterator<String> itId = listId.iterator();
			    while (itId.hasNext()){
				String s = itId.next();
				response = send(rsi.getDeleteServiceUrl()+"?id="+s,rsi.getLoginService());									 	 		 
			    }
			}


			count++;
		    }

		    StringBuffer cswResponse = new StringBuffer ();

		    cswResponse.append("<csw:TransactionResponse xmlns:csw=\"http://www.opengis.net/cat/csw\" xmlns:dc=\"http://www.purl.org/dc/elements/1.1/\" xmlns:dct=\"http://www.purl.org/dc/terms/\">");
		    cswResponse.append("<csw:TransactionSummary>");
		    cswResponse.append("<csw:totalDeleted>1</csw:totalDeleted>");
		    cswResponse.append("</csw:TransactionSummary>");
		    cswResponse.append("</csw:TransactionResponse>");
		    OutputStream os = resp.getOutputStream();

		    InputStream is = new ByteArrayInputStream(cswResponse.toString().getBytes());
		    int byteRead;
		    try {
			while((byteRead = is.read()) != -1) {  
			    os.write(byteRead);
			}
		    } finally{
			os.flush();
			os.close();   			    
		    }
		    os=null;
		    is=null;

		}

	    }else{
		if (version!=null)	    version = version.replaceAll("\\.", "");

		dump (param.toString());
		List<String> filePathList = new Vector<String>();
		String filePath = sendData("POST", getRemoteServerUrl(0), param.toString());
		filePathList.add(filePath);
		transform(version,currentOperation,req, resp, filePathList);
	    }
	}catch(Exception e){e.printStackTrace();
	dump("ERROR",e.getMessage());}
    }

    protected StringBuffer generateXSLTForMetadata(){

	try {

	    StringBuffer CSWCapabilities200 = new StringBuffer ();		


	    CSWCapabilities200.append("<xsl:stylesheet version=\"1.00\" xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" xmlns:gco=\"http://www.isotc211.org/2005/gco\" xmlns:ns3=\"http://www.isotc211.org/2005/gmx\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:gml=\"http://www.opengis.net/gml\" xmlns:gts=\"http://www.isotc211.org/2005/gts\"    xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:csw=\"http://www.opengis.net/cat/csw\" xmlns:ows=\"http://www.opengis.net/ows\">");

	    List<String> notAllowedAttributeList = getAttributesNotAllowedInMetadata(getRemoteServerUrl(0));
	    int nsI =0 ;
	    for (int i =0;i<notAllowedAttributeList.size();i++)
	    {
		String text = notAllowedAttributeList.get(i);
		if (text !=null){
		    if (text.indexOf("\":")<0){
			//Pas de namespace. 
			CSWCapabilities200.append("<xsl:template match=\"//gmd:MD_Metadata/"+text+ "\">");

		    }else{												
			CSWCapabilities200.append("<xsl:template xmlns:"+"au"+nsI+"=\""+text.substring(1,text.indexOf("\":"))+"\" match=\"//gmd:MD_Metadata/au"+nsI+text.substring(text.indexOf("\":")+1)+"\">");			
			nsI++;
		    }

		    CSWCapabilities200.append("</xsl:template>");

		}							
	    }

	    CSWCapabilities200.append("  <!-- Whenever you match any node or any attribute -->");
	    CSWCapabilities200.append("<xsl:template match=\"node()|@*\">");
	    CSWCapabilities200.append("<!-- Copy the current node -->");
	    CSWCapabilities200.append("<xsl:copy>");
	    CSWCapabilities200.append("<!-- Including any attributes it has and any child nodes -->");
	    CSWCapabilities200.append("<xsl:apply-templates select=\"@*|node()\"/>");
	    CSWCapabilities200.append("</xsl:copy>");
	    CSWCapabilities200.append("</xsl:template>");

	    CSWCapabilities200.append("</xsl:stylesheet>");	    
	    return CSWCapabilities200;
	} catch (Exception e) {
	    e.printStackTrace();
	    dump("ERROR",e.getMessage());
	}

	//If something goes wrong, an empty stylesheet is returned.	
	StringBuffer sb = new StringBuffer();		
	return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>"); 
    }

    StringBuffer  generateInfoFileForMef(String uuid,String siteId){

	StringBuffer info = new StringBuffer();
	info.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
	info.append("<info version=\"1.0\">");
	info.append("<general>");
	info.append("<createDate>2008-02-29T18:05:34</createDate>");
	info.append("<changeDate>2008-02-29T18:05:34</changeDate>");
	info.append("<schema>iso19139</schema>");
	info.append("<isTemplate>false</isTemplate>");
	info.append("<localId>906</localId>");
	info.append("<format>full</format>");
	info.append("<uuid>"+uuid+"</uuid>");
	info.append("<siteId>"+siteId+"</siteId>");
	info.append("<siteName>dummy</siteName>");
	info.append("</general>");
	info.append("<categories />");
	info.append("<privileges>");
	info.append("<group name=\"sample\">");
	info.append("<operation name=\"view\" />");
	info.append("<operation name=\"download\" />");
	info.append("<operation name=\"notify\" />");
	info.append("<operation name=\"dynamic\" />");
	info.append("</group>");
	info.append("</privileges>");
	info.append("<public>");
	info.append("</public>");
	info.append("<private>");
	info.append("</private>");
	info.append("</info>");

	return info;
    }


    public static void main (String args[]){


	try{
	    StringBuffer xml = new StringBuffer();
	    xml.append("<csw:Transaction service=\"CSW\" "); 
	    xml.append("version=\"2.0.0\" ");
	    xml.append("xmlns:csw=\"http://www.opengis.net/cat/csw\">");
	    xml.append("<csw:Insert>");
	    xml.append("<Record xmlns=\"http://www.opengis.net/cat/csw\" xmlns:dc=\"http://www.purl.org/dc/elements/1.1/\" xmlns:dct=\"http://www.purl.org/dc/terms/\" xmlns:ows=\"http://www.opengis.net/ows\" >");
	    xml.append("<dc:contributor scheme=\"http://www.example.com\">John</dc:contributor>");
	    xml.append("<dc:identifier >REC-2</dc:identifier>");
	    xml.append("<ows:WGS84BoundingBox crs=\"urn:opengis:crs:OGC:2:84\" dimensions=\"2\">");
	    xml.append("<ows:LowerCorner>12 12</ows:LowerCorner>");
	    xml.append("<ows:UpperCorner>102 102</ows:UpperCorner>");
	    xml.append("</ows:WGS84BoundingBox>");
	    xml.append("</Record>");
	    xml.append("</csw:Insert>");
	    xml.append("</csw:Transaction>");

	    System.out.println(xml);


	}catch(Exception e){
	    e.printStackTrace();
	}


    }
}

/*
 * 
 * http://localhost:8082/geonetwork/srv/en/xml.search?any=8888
 http://localhost:8082/geonetwork/srv/en/metadata.delete?id=903



 <csw:Transaction service="CSW" 
   version="2.0.0" 
   xmlns:csw="http://www.opengis.net/cat/csw" 
   xmlns:dc="http://www.purl.org/dc/elements/1.1/"
   xmlns:ogc="http://www.opengis.net/ogc">
  <csw:Delete typeName="csw:Record">
    <csw:Constraint version="2.0.0">
      <ogc:Filter>
        <ogc:PropertyIsEqualTo>
            <ogc:PropertyName>/csw:Record/dc:contributor</ogc:PropertyName>
            <ogc:Literal>Jane</ogc:Literal>
        </ogc:PropertyIsEqualTo>
      </ogc:Filter>
    </csw:Constraint>
  </csw:Delete>
</csw:Transaction>


<csw:TransactionResponse xmlns:csw="http://www.opengis.net/cat/csw" xmlns:dc="http://www.purl.org/dc/elements/1.1/" xmlns:dct="http://www.purl.org/dc/terms/">
   <csw:TransactionSummary>
      <csw:totalDeleted>1</csw:totalDeleted>
   </csw:TransactionSummary>
</csw:TransactionResponse>



<csw:Transaction service="CSW" 
    version="2.0.0" 
    xmlns:csw="http://www.opengis.net/cat/csw" >
   <csw:Insert>
     <Record xmlns="http://www.opengis.net/cat/csw" xmlns:dc="http://www.purl.org/dc/elements/1.1/" xmlns:dct="http://www.purl.org/dc/terms/" xmlns:ows="http://www.opengis.net/ows" >
        <dc:contributor scheme="http://www.example.com">John</dc:contributor>
        <dc:identifier >REC-2</dc:identifier>
        <ows:WGS84BoundingBox crs="urn:opengis:crs:OGC:2:84" dimensions="2">
                <ows:LowerCorner>12 12</ows:LowerCorner>
                <ows:UpperCorner>102 102</ows:UpperCorner>
        </ows:WGS84BoundingBox>
     </Record>
   </csw:Insert>
 </csw:Transaction>



 */