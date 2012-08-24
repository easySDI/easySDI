/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
package org.easysdi.services.wps;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.StringReader;
import java.net.URL;
import java.net.URLConnection;
import java.sql.Connection;
import java.sql.Date;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.Enumeration;
import java.util.List;
import java.util.Vector;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.Marshaller;
import javax.xml.bind.Unmarshaller;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.xml.handler.mapper.NamespacePrefixMapperImpl;
import org.w3c.dom.Document;
import org.w3c.dom.NodeList;
import org.xml.sax.InputSource;

import net.opengis.ows._1.CodeType;
import net.opengis.wps._1_0.ComplexDataType;
import net.opengis.wps._1_0.DataType;
import net.opengis.wps._1_0.ExecuteResponse;
import net.opengis.wps._1_0.ObjectFactory;
import net.opengis.wps._1_0.OutputDataType;
import net.opengis.wps._1_0.StatusType;
import net.opengis.wps._1_0.ExecuteResponse.ProcessOutputs;



public class WPSServlet extends HttpServlet {


    //Connexion sttring to the joomla database
    private String connexionString ="jdbc:mysql://localhost/joomla?user=root&password=root&noDatetimeStringSync=true";
    //Joomla table prefix
    private String joomlaPrefix = "jos_";
    //Jdbc Driver to connect to Joomla 
    private String jdbcDriver ="com.mysql.jdbc.Driver";
    //Name of the platform
    private String platformName = "EASYSDI";



    public void init(ServletConfig config) throws ServletException {
	String conn = config.getInitParameter("connexionString");
	String prefix = config.getInitParameter("joomlaPrefix");
	String driver = config.getInitParameter("jdbcDriver");
	String platform = config.getInitParameter("platformName");

	if (conn !=null && conn.length()>0){
	    connexionString= conn;
	}

	if (prefix !=null && prefix.length()>0){
	    joomlaPrefix= prefix;
	}

	if (driver !=null && driver.length()>0){
	    jdbcDriver = driver;
	}

	if (platform !=null && platform.length()>0){
	    platformName = platform;
	}	
    }



    public void doPost(HttpServletRequest req, HttpServletResponse resp){    

	try{
	    JAXBContext jc =JAXBContext.newInstance(net.opengis.wps._1_0.Execute.class);
	    Unmarshaller um = jc.createUnmarshaller();


	    net.opengis.wps._1_0.Execute execute = (net.opengis.wps._1_0.Execute) um.unmarshal(req.getInputStream());
	    if (execute!=null && execute.getIdentifier() !=null && execute.getIdentifier().getValue()!=null){
		String executeType = execute.getIdentifier().getValue();
		if (executeType.equalsIgnoreCase("getOrders")){
		    resp.setContentType("text/xml");
		    resp.getOutputStream().write(executeGetOrders(execute).getBytes());
		}else{		
		    if (executeType.equalsIgnoreCase("setOrder")){
			resp.setContentType("text/xml");
			resp.getOutputStream().write(executeSetOrderResponse(execute).getBytes());
		    }else{
			resp.setContentType("text/xml");
			resp.getOutputStream().write(error("UNKNOWNIDENTIFIER",executeType+" is unkwnon").getBytes());
			// Type not allowed
		    }
		}


	    }else{
		resp.setContentType("text/xml");
		resp.getOutputStream().write(error("UNDEFINEDIDENTIFIER","Identifier is undefined").getBytes());				
	    }
	}catch (Exception e){
	    e.printStackTrace();
	    try{
		resp.setContentType("text/xml");
		resp.getOutputStream().write(error("UNEXPECTED ERROR",e.getMessage()).getBytes());
	    }catch(Exception e2){
		e2.printStackTrace();
	    }

	}    

    }


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

	    // To build the request to dispatch
	    while (parameterNames.hasMoreElements()) {
		String key = (String) parameterNames.nextElement();
		String value  = req.getParameter(key);


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


	    if (operation.equalsIgnoreCase("GetCapabilities")){		
		resp.setContentType("text/xml");		
		resp.getOutputStream().write(getCapabilities(req).getBytes());
	    }else{
		resp.setContentType("text/xml");
		resp.getOutputStream().write(error("OPERATIONNOTDEFINED","This operation is not defined in GET method").getBytes());
		// Type not allowed
	    }	    	 	    	

	}catch(Exception e){
	    e.printStackTrace();
	    try{
		resp.setContentType("text/xml");
		resp.getOutputStream().write(error("UNEXPECTED ERROR",e.getMessage()).getBytes());
	    }catch(Exception e2){
		e2.printStackTrace();
	    }

	}
    }

    private void setJoomlaPrefix(String prefix){

	joomlaPrefix = prefix;
    }
    private String getJoomlaPrefix(){

	return joomlaPrefix;
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
	// http://hostname.com:80/mywebapp/servlet/MyServlet/a/b;c=123?d=789

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





    private String executeGetOrders (net.opengis.wps._1_0.Execute execute){
	Connection conn = null;

	try {



	    List lInputs = execute.getDataInputs().getInput();

	    java.util.Iterator itInputs = lInputs.iterator();

	    String userName = null;
	    String statusToRead ="SENT";

	    while(itInputs.hasNext()){
		net.opengis.wps._1_0.InputType inputType = (net.opengis.wps._1_0.InputType)itInputs.next();
		if (inputType.getIdentifier().getValue().equalsIgnoreCase("userName")){
		    userName = inputType.getData().getLiteralData().getValue();
		}else{
		    if (inputType.getIdentifier().getValue().equalsIgnoreCase("status")){
			statusToRead = inputType.getData().getLiteralData().getValue();
		    }   		    
		}
	    }

	    if (userName == null){		    
		return error("PARTNERNOTDEFINED","paremeter containaing the partner id is not defined");
	    }


	    net.opengis.wps._1_0.ObjectFactory of = new net.opengis.wps._1_0.ObjectFactory();
	    ExecuteResponse er = of.createExecuteResponse();
	    StatusType st = of.createStatusType();
	    st.setProcessSucceeded("processSucceeded");
	    er.setStatus(st);

	    ProcessOutputs po = new ProcessOutputs();
	    OutputDataType odt = of.createOutputDataType();

	    CodeType ct = new CodeType();
	    ct.setValue("getOrders");
	    odt.setIdentifier(ct );


	    DataType dt = of.createDataType();

	    ComplexDataType cdt = of.createComplexDataType();

	    dt.setComplexData(cdt );
	    odt.setData(dt );

	    po.getOutput().add(odt);


	    er.setProcessOutputs(po );




	    Class.forName(getJdbcDriver()).newInstance();


	    conn =  DriverManager.getConnection(getConnexionString());

	    Statement stmt = conn.createStatement();
	    ResultSet rs = stmt.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_order o, "+getJoomlaPrefix()+"easysdi_community_partner p , " +getJoomlaPrefix()+"users u where o.status = '"+statusToRead+"' AND o.user_id = p.user_id AND u.id = p.user_id AND u.username = '"+userName+"'");
	    
	    StringBuffer res = new StringBuffer();
	    res.append("<easysdi:orders  	xmlns:easysdi=\"http://www.easysdi.org\">");
	    List<String> orderIdList = new Vector<String>();
	    while (rs.next()) {
		String order_id = rs.getString("order_id");
		orderIdList.add(order_id);
		String remark = rs.getString("remark");
		String provider_id = rs.getString("provider_id");

		String name = rs.getString("name");
		String type = rs.getString("type");
		String status = rs.getString("status");
		String order_update = rs.getString("order_update");
		String third_party = rs.getString("third_party");
		String archived = rs.getString("archived");
		String RESPONSE_DATE = rs.getString("RESPONSE_DATE");
		String RESPONSE_SEND = rs.getString("RESPONSE_SEND");
		String user_id = rs.getString("user_id");
		String partner_id = rs.getString("partner_id");



		res.append("<easysdi:order>\n");
		res.append("<easysdi:header>\n");
		res.append("<easysdi:VERSION>2.0</easysdi:VERSION>\n");
		res.append("<easysdi:PLATFORM>"+getPlatformName()+"</easysdi:PLATFORM>\n");
		res.append("<easysdi:SERVER>36</easysdi:SERVER>\n");
		res.append("</easysdi:header>\n");
		res.append("<easysdi:REQUEST>\n");
		res.append("<easysdi:ID>"+order_id+"</easysdi:ID>\n");
		res.append("<easysdi:NBELEMENTS>1</easysdi:NBELEMENTS>\n");
		if(type.equalsIgnoreCase("D")){
		    res.append("<easysdi:TYPE>ESTIMATE</easysdi:TYPE>\n");    
		}
		if(type.equalsIgnoreCase("O")){
		    res.append("<easysdi:TYPE>ORDER</easysdi:TYPE>\n");    
		}
		res.append("<easysdi:NAME>"+name+"</easysdi:NAME>\n");
		res.append("</easysdi:REQUEST>\n");
		res.append("<easysdi:CLIENT>\n");
		res.append("<easysdi:ID>"+partner_id+"</easysdi:ID>\n");
		Statement stmtAdd = conn.createStatement();
		ResultSet rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_code = 'EASYSDI_TYPE_CONTACT' and a.partner_id = "+partner_id);

		while(rsAddContact.next()){

		    res.append("<easysdi:CONTACTADDRESS>\n");
		    res.append("<easysdi:NAME1>"+rsAddContact.getString("address_corporate_name1")+"</easysdi:NAME1>\n");
		    res.append("<easysdi:NAME2>"+rsAddContact.getString("address_corporate_name2")+"</easysdi:NAME2>\n");
		    res.append("<easysdi:AGENTFIRSTNAME>"+rsAddContact.getString("address_agent_firstname")+"</easysdi:AGENTFIRSTNAME>\n") ;
		    res.append("<easysdi:AGENTLASTNAME>"+rsAddContact.getString("address_agent_firstname")+"</easysdi:AGENTLASTNAME>\n") ;		
		    res.append("<easysdi:ADDRESSSTREET1>"+rsAddContact.getString("address_street1")+"</easysdi:ADDRESSSTREET1>\n");
		    res.append("<easysdi:ADDRESSSTREET2>"+rsAddContact.getString("address_street2")+"</easysdi:ADDRESSSTREET2>\n");
		    res.append("<easysdi:ZIP>"+rsAddContact.getString("address_postalcode")+"</easysdi:ZIP>\n");
		    res.append("<easysdi:LOCALITY>"+rsAddContact.getString("address_locality")+"</easysdi:LOCALITY>\n");
		    res.append("<easysdi:COUNTRY>"+rsAddContact.getString("country_code")+"</easysdi:COUNTRY>\n");
		    res.append("<easysdi:EMAIL>"+rsAddContact.getString("address_email")+"</easysdi:EMAIL>\n");
		    res.append("<easysdi:PHONE>"+rsAddContact.getString("address_phone")+"</easysdi:PHONE>\n");
		    res.append("<easysdi:FAX>"+rsAddContact.getString("address_fax")+"</easysdi:FAX>\n");
		    res.append("</easysdi:CONTACTADDRESS>\n");																
		}
		rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_code = 'EASYSDI_TYPE_INVOICING' and a.partner_id = "+partner_id);
		while(rsAddContact.next()){		     
		    res.append("<easysdi:INVOICEADDRESS>\n");
		    res.append("<easysdi:NAME1>"+rsAddContact.getString("address_corporate_name1")+"</easysdi:NAME1>\n");
		    res.append("<easysdi:NAME2>"+rsAddContact.getString("address_corporate_name2")+"</easysdi:NAME2>\n");
		    res.append("<easysdi:AGENTFIRSTNAME>"+rsAddContact.getString("address_agent_firstname")+"</easysdi:AGENTFIRSTNAME>\n") ;
		    res.append("<easysdi:AGENTLASTNAME>"+rsAddContact.getString("address_agent_firstname")+"</easysdi:AGENTLASTNAME>\n") ;		
		    res.append("<easysdi:ADDRESSSTREET1>"+rsAddContact.getString("address_street1")+"</easysdi:ADDRESSSTREET1>\n");
		    res.append("<easysdi:ADDRESSSTREET2>"+rsAddContact.getString("address_street2")+"</easysdi:ADDRESSSTREET2>\n");
		    res.append("<easysdi:ZIP>"+rsAddContact.getString("address_postalcode")+"</easysdi:ZIP>\n");
		    res.append("<easysdi:LOCALITY>"+rsAddContact.getString("address_locality")+"</easysdi:LOCALITY>\n");
		    res.append("<easysdi:COUNTRY>"+rsAddContact.getString("country_code")+"</easysdi:COUNTRY>\n");
		    res.append("<easysdi:EMAIL>"+rsAddContact.getString("address_email")+"</easysdi:EMAIL>\n");
		    res.append("<easysdi:PHONE>"+rsAddContact.getString("address_phone")+"</easysdi:PHONE>\n");
		    res.append("<easysdi:FAX>"+rsAddContact.getString("address_fax")+"</easysdi:FAX>\n");
		    res.append("</easysdi:INVOICEADDRESS>\n");																
		}

		rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_code = 'EASYSDI_TYPE_DELIVERY' and a.partner_id = "+partner_id);
		while(rsAddContact.next()){		     
		    res.append("<easysdi:DELIVERYADDRESS>\n");
		    res.append("<easysdi:NAME1>"+rsAddContact.getString("address_corporate_name1")+"</easysdi:NAME1>\n");
		    res.append("<easysdi:NAME2>"+rsAddContact.getString("address_corporate_name2")+"</easysdi:NAME2>\n");
		    res.append("<easysdi:AGENTFIRSTNAME>"+rsAddContact.getString("address_agent_firstname")+"</easysdi:AGENTFIRSTNAME>\n") ;
		    res.append("<easysdi:AGENTLASTNAME>"+rsAddContact.getString("address_agent_firstname")+"</easysdi:AGENTLASTNAME>\n") ;		
		    res.append("<easysdi:ADDRESSSTREET1>"+rsAddContact.getString("address_street1")+"</easysdi:ADDRESSSTREET1>\n");
		    res.append("<easysdi:ADDRESSSTREET2>"+rsAddContact.getString("address_street2")+"</easysdi:ADDRESSSTREET2>\n");
		    res.append("<easysdi:ZIP>"+rsAddContact.getString("address_postalcode")+"</easysdi:ZIP>\n");
		    res.append("<easysdi:LOCALITY>"+rsAddContact.getString("address_locality")+"</easysdi:LOCALITY>\n");
		    res.append("<easysdi:COUNTRY>"+rsAddContact.getString("country_code")+"</easysdi:COUNTRY>\n");
		    res.append("<easysdi:EMAIL>"+rsAddContact.getString("address_email")+"</easysdi:EMAIL>\n");
		    res.append("<easysdi:PHONE>"+rsAddContact.getString("address_phone")+"</easysdi:PHONE>\n");
		    res.append("<easysdi:FAX>"+rsAddContact.getString("address_fax")+"</easysdi:FAX>\n");
		    res.append("</easysdi:DELIVERYADDRESS>\n");																
		}		
		res.append("</easysdi:CLIENT>\n");
		res.append("<easysdi:BILLINGINFO>\n");
		res.append("<easysdi:TIERCE_ID>"+third_party+"</easysdi:TIERCE_ID>\n");
		res.append("<easysdi:TIERCE_NAME></easysdi:TIERCE_NAME>\n");
		res.append("<easysdi:DISCOUNT>0</easysdi:DISCOUNT>\n");
		res.append("<easysdi:REBATE>0</easysdi:REBATE>\n");
		res.append("</easysdi:BILLINGINFO>\n");


		Statement stmtPerim = conn.createStatement();
		ResultSet rsPerim = stmtPerim.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_order_product_perimeters op, "+getJoomlaPrefix()+"easysdi_perimeter_definition pd where op.perimeter_id = pd.id and order_id = "+order_id);



		res.append("<easysdi:PERIMETER>\n");
		if (rsPerim.next()){
		    if (rsPerim.getString("id_field_name")==null || rsPerim.getString("id_field_name").length() ==0){
			res.append("<easysdi:TYPE>COORDINATES</easysdi:TYPE>\n");
		    }else{
			res.append("<easysdi:TYPE>VALUES</easysdi:TYPE>\n");
		    }		 

		    do{							   		
			res.append("<easysdi:CONTENT>"+rsPerim.getString("value")+"</easysdi:CONTENT>\n");
		    }
		    while(rsPerim.next());
		}
		res.append("</easysdi:PERIMETER>\n");
		res.append("<easysdi:PRODUCTS>\n");

		Statement stmtProducts = conn.createStatement();
		ResultSet rsProducts = stmtProducts.executeQuery("SELECT *,p.id as product_id FROM "+getJoomlaPrefix()+"easysdi_order_product_list pl ,"+getJoomlaPrefix()+"easysdi_product p WHERE p.id=pl.product_id and pl.order_id = "+order_id);
		while(rsProducts.next()){
		    res.append("<easysdi:PRODUCT>\n");		
		    res.append("<easysdi:DATA_ID>"+rsProducts.getString("metadata_id")+"</easysdi:DATA_ID>\n");
		    res.append("<easysdi:PRODUCT_ID>"+rsProducts.getString("product_id")+"</easysdi:PRODUCT_ID>\n");
		    res.append("<easysdi:DATA_NAME>"+rsProducts.getString("data_title")+"</easysdi:DATA_NAME>\n");
		    res.append("</easysdi:PRODUCT>\n");		
		}
		res.append("</easysdi:PRODUCTS>\n");

		/*Statement stmtProducts = conn.createStatement();
		ResultSet rsProducts = stmtProducts.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_order_product_list pl ,"+getJoomlaPrefix()+"easysdi_product p WHERE p.id=pl.product_id and pl.order_id = "+order_id);
		while(rsProducts.next()){
		res.append("<easysdi:ELEMENT>\n");	
		res.append("<easysdi:name>PROJECTION</easysdi:name>\n");
		res.append("<easysdi:value>SWITZERLAND</easysdi:value>\n");
		res.append("</easysdi:ELEMENT>\n");
		}	*/

		res.append("</easysdi:order>\n");





		stmtProducts.close();
		stmtProducts.close();
		rsAddContact.close();
		stmtAdd.close();
	    }
	    res.append("</easysdi:orders>\n");

	    
	 try{   
	    javax.xml.parsers.DocumentBuilderFactory factory = javax.xml.parsers.DocumentBuilderFactory.newInstance();
	    javax.xml.parsers.DocumentBuilder db = factory.newDocumentBuilder();
	    org.xml.sax.InputSource inStream = new org.xml.sax.InputSource();
	 
	    inStream.setCharacterStream(new java.io.StringReader(res.toString()));
	     Document doc = db.parse(inStream);
	     NodeList nl = doc.getElementsByTagName("easysdi:orders");	     
	     
	    if (nl.getLength()>0){
		cdt.getContent().add(nl.item(0));
	    }

	 }catch(Exception e){
	     e.printStackTrace();
	 }

	    JAXBContext jc = JAXBContext.newInstance(net.opengis.wps._1_0.Execute.class);


	    Marshaller m = jc.createMarshaller();

	    m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, Boolean.TRUE);
	    m.setProperty("com.sun.xml.bind.namespacePrefixMapper",
		    new NamespacePrefixMapperImpl());
	    m.setProperty(Marshaller.JAXB_ENCODING, "UTF-8");
	    m.setProperty(Marshaller.JAXB_FRAGMENT, Boolean.FALSE);

	    ByteArrayOutputStream baos = new ByteArrayOutputStream();

	    m.marshal(er, baos);



	    if (orderIdList.size()>0){

		java.util.Iterator<String> it = orderIdList.iterator();
		String orderIdClause;
		orderIdClause = it.next();
		while (it.hasNext()){
		    orderIdClause = orderIdClause +","+it.next();		    
		}

		stmt.executeUpdate("update "+getJoomlaPrefix()+"easysdi_order set status ='AWAIT' ,response_date = NOW()  where order_id in ("+orderIdClause+")");	    		    

	    }

	    rs.close();
	    stmt.close();

	    conn.close();    





	    return (baos.toString());

	} catch (Exception ex) {
	    // handle any errors	   
	    ex.printStackTrace();
	}


	return error("ERROR","An error just occured");
    }

    private String error(String errorCode, String errorMessage){

	return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"+
	"<ows:ExceptionReport xmlns:ows=\"http://www.opengis.net/ows/1.1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengis.net/ows/1.1../../../ows/1.1.0/owsExceptionReport.xsd\" version=\"1.0.0\" xml:lang=\"en-CA\">"+
	"<ows:Exception exceptionCode=\""+errorCode+"\">"+
	"<ows:ExceptionText>"+errorMessage+"</ows:ExceptionText>"+
	"</ows:Exception>"+
	"</ows:ExceptionReport>";
    }
    private String getConnexionString() {
	return connexionString;
    }
    private void setConnexionString(String connexionString) {
	this.connexionString = connexionString;
    }
    private String getJdbcDriver() {
	return jdbcDriver;
    }
    private  void setJdbcDriver(String jdbcDriver) {
	this.jdbcDriver = jdbcDriver;
    }
    private  String getPlatformName() {
	return platformName;
    }
    private void setPlatformName(String platformName) {
	this.platformName = platformName;
    }


    public static void main(String args[]) throws Exception{

	try{
	    URL url = new URL("http://localhost:8081/wps/WPSServlet");
	    
	    //URL url = new URL("http://demo.easysdi.org:8080/wps/WPSServlet");
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

    private String executeSetOrderResponse(net.opengis.wps._1_0.Execute execute ){

	try{

	    List lInputs = execute.getDataInputs().getInput();

	    java.util.Iterator it = lInputs.iterator();

	    String responseDate=null;
	    String order_id=null;   
	    String product_id=null;
	    String data = "";
	    String filename= "";

	    while(it.hasNext()){
		net.opengis.wps._1_0.InputType inputType = (net.opengis.wps._1_0.InputType)it.next();
		if (inputType.getIdentifier().getValue().equalsIgnoreCase("DATE")){
		    responseDate  = inputType.getData().getLiteralData().getValue();

		}else
		    if (inputType.getIdentifier().getValue().equalsIgnoreCase("REQUEST_ID")){
			order_id  = inputType.getData().getLiteralData().getValue();

		    }else
			if (inputType.getIdentifier().getValue().equalsIgnoreCase("PRODUCT_ID")){
			    product_id  = inputType.getData().getLiteralData().getValue();

			}else
			    if (inputType.getIdentifier().getValue().equalsIgnoreCase("FILENAME")){
				filename  = inputType.getData().getLiteralData().getValue();

			    }



		if (inputType.getData().getComplexData() !=null){
		    data = inputType.getData().getComplexData().getContent().get(0).toString();		
		}

	    }



	    if (responseDate!=null && order_id!=null && product_id!=null && filename !=null && data !=null){
		Connection conn = null;
		Class.forName(jdbcDriver).newInstance();


		conn =  DriverManager.getConnection(connexionString);

		Statement stmt = conn.createStatement();
		stmt.executeUpdate("update "+getJoomlaPrefix()+"easysdi_order set response_send = '1' ,response_date = str_to_date('"+responseDate+"', '%d.%m.%Y %H:%i:%s')  where order_id = "+order_id);


		PreparedStatement pre = conn.prepareStatement("update "+getJoomlaPrefix()+"easysdi_order_product_list set filename =  '"+ filename+ "', status = 'AVAILABLE',data=? where order_id = "+order_id +" AND product_id = "+product_id);

		ByteArrayInputStream bais = new ByteArrayInputStream(data.getBytes());

		pre.setBinaryStream(1,bais,data.length());

		pre.executeUpdate();



		int count = stmt.executeUpdate("update "+getJoomlaPrefix()+"easysdi_order set status = 'FINISH'   where order_id = "+order_id +" AND (SELECT COUNT(*) FROM "+getJoomlaPrefix()+"easysdi_order_product_list WHERE order_id = "+order_id +")=(SELECT COUNT(*) FROM "+getJoomlaPrefix()+"easysdi_order_product_list WHERE status = 'AVAILABLE' AND order_id = "+order_id +")");
		if (count == 0){
		    stmt.executeUpdate("update "+getJoomlaPrefix()+"easysdi_order set status = 'PROGRESS'   where order_id = "+order_id );
		}

		pre.close();
		stmt.close();
		conn.close();


		
		net.opengis.wps._1_0.ObjectFactory of = new net.opengis.wps._1_0.ObjectFactory();
		    ExecuteResponse er = of.createExecuteResponse();
		    StatusType st = of.createStatusType();
		    st.setProcessSucceeded("processSucceeded");
		    er.setStatus(st);

		    ProcessOutputs po = new ProcessOutputs();
		    OutputDataType odt = of.createOutputDataType();

		    CodeType ct = new CodeType();
		    ct.setValue("setOrders");
		    odt.setIdentifier(ct );


		    DataType dt = of.createDataType();

		    ComplexDataType cdt = of.createComplexDataType();

		    dt.setComplexData(cdt );
		    odt.setData(dt );

		    po.getOutput().add(odt);


		    er.setProcessOutputs(po );

		    
		    JAXBContext jc = JAXBContext.newInstance(net.opengis.wps._1_0.Execute.class);


		    Marshaller m = jc.createMarshaller();

		    m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, Boolean.TRUE);
		    m.setProperty("com.sun.xml.bind.namespacePrefixMapper",
			    new NamespacePrefixMapperImpl());
		    m.setProperty(Marshaller.JAXB_ENCODING, "UTF-8");
		    m.setProperty(Marshaller.JAXB_FRAGMENT, Boolean.FALSE);

		    ByteArrayOutputStream baos = new ByteArrayOutputStream();

		    m.marshal(er, baos);

		    
		return baos.toString();
	    }
	}catch (Exception e){
	    e.printStackTrace();
	}

	return error ("ERROR","An error just occured");
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
