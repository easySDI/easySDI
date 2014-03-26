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
package ch.depth.services.wps;

import java.io.FileWriter;
import java.io.StringWriter;
import java.io.PrintWriter;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.net.URL;
import java.net.URLConnection;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
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

import net.opengis.ows._1.CodeType;
import net.opengis.wps._1_0.ComplexDataType;
import net.opengis.wps._1_0.DataType;
import net.opengis.wps._1_0.ExecuteResponse;
import net.opengis.wps._1_0.OutputDataType;
import net.opengis.wps._1_0.StatusType;
import net.opengis.wps._1_0.ExecuteResponse.ProcessOutputs;

import org.w3c.dom.Document;
import org.w3c.dom.NodeList;


public class WPSServlet extends HttpServlet {


    //Connexion string to the joomla database
    private String connexionString ="jdbc:mysql://localhost/joomla?user=root&password=root&noDatetimeStringSync=true";
    //Joomla table prefix
    private String joomlaPrefix = "jos_";
    //Jdbc Driver to connect to Joomla 
    private String jdbcDriver ="com.mysql.jdbc.Driver";
    //Name of the platform
    private String platformName = "EASYSDI";

    private String languageFile = "C:\\www\\Site\\Joomla\\language\\fr-FR\\fr-FR.com_easysdi_shop.ini";
    
    private String senderEmail = "webmaster@depth.ch";
    
    private String senderName = "Depth SA";
    
    public void init(ServletConfig config) throws ServletException {
	String conn = config.getInitParameter("connexionString");
	String prefix = config.getInitParameter("joomlaPrefix");
	String driver = config.getInitParameter("jdbcDriver");
	String platform = config.getInitParameter("platformName");
	String language = config.getInitParameter("languageFile");
	String sender = config.getInitParameter("sender");
	String senderTitle = config.getInitParameter("senderTitle");
	
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
	
	if (language !=null && language.length()>0){
		languageFile = language;
	}	
	
	if (sender !=null && sender.length()>0){
		senderEmail = sender;
	}	
	
	if (senderTitle !=null && senderTitle.length()>0){
		senderName = senderTitle;
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

	    dt.setComplexData(cdt);
	    odt.setData(dt );

	    po.getOutput().add(odt);


	    er.setProcessOutputs(po);




	    Class.forName(getJdbcDriver()).newInstance();


	    conn =  DriverManager.getConnection(getConnexionString());
	    
	    Statement stmt = conn.createStatement();
	    
	    ResultSet rs = stmt.executeQuery("SELECT *, osl.code as orderCode FROM "+getJoomlaPrefix()+"easysdi_order o, "+getJoomlaPrefix()+"easysdi_community_partner p, "+getJoomlaPrefix()+"easysdi_order_status_list osl WHERE osl.id=o.status AND osl.code = '"+statusToRead+"' AND o.user_id = p.user_id "+"AND (SELECT COUNT(*) FROM "+getJoomlaPrefix()+"easysdi_order_product_list opl ,  "+getJoomlaPrefix()+"easysdi_product p, "+getJoomlaPrefix()+"easysdi_community_partner part, "+getJoomlaPrefix()+"users u  WHERE opl.order_id = o.order_id AND opl.product_id = p.id AND p.partner_id = part.partner_id AND part.user_id = u.id AND u.username='"+userName+"')   > 0");
	    
	    StringBuffer res = new StringBuffer();
	    res.append("<easysdi:orders 	xmlns:easysdi=\"http://www.easysdi.org\">");
	    List<String> orderIdList = new Vector<String>();
	    while (rs.next()) 
	    {
			String order_id = rs.getString("order_id");
			orderIdList.add(order_id);
			String remark = rs.getString("remark");
			String provider_id = rs.getString("provider_id");
			
			String name = rs.getString("name");
			int type = rs.getInt("type");
			String order_update = rs.getString("order_update");
			String third_party = rs.getString("third_party");
			//String archived = rs.getString("archived");
			String RESPONSE_DATE = rs.getString("RESPONSE_DATE");
			String RESPONSE_SEND = rs.getString("RESPONSE_SEND");
			String user_id = rs.getString("user_id");
			String partner_id = rs.getString("partner_id");
			String root_id = rs.getString("root_id");
			String status = rs.getString("orderCode");
			int buffer = 0;
			buffer = rs.getInt("buffer");
			
			// Recuperation du rabais pour le fournisseur
			int isRebate = 0;
			String rebate = "0";
			Statement stmtRebate = conn.createStatement();
		    ResultSet rsRebate = stmtRebate.executeQuery("SELECT isrebate, rebate FROM "+getJoomlaPrefix()+"easysdi_community_partner part, "+getJoomlaPrefix()+"users u WHERE u.id=part.user_id AND u.username='"+userName+"'");
	    
		    while(rsRebate.next()){
		    	isRebate = rsRebate.getInt("isrebate");
				rebate = rsRebate.getString("rebate");
			}
		    
		    rsRebate.close();
		    stmtRebate.close();
		    
		    // HACK ASIT-VD: Contr�le de l'application ou pas du rabais selon
		    // le profil du client qui passe la commande et/ou du tiers pour qui la commande est pass�e
		    boolean ApplicableRebate=false;
		    Statement stmtProfile = conn.createStatement();
		    ResultSet rsProfile;
		    String qry="SELECT prof.profile_id, prof.profile_name as profile_name FROM "+getJoomlaPrefix()+"asitvd_community_partner part, "+getJoomlaPrefix()+"asitvd_community_profile prof WHERE prof.profile_id=part.profile_id AND part.partner_id='"+third_party+"'";
		    if (!third_party.equalsIgnoreCase("0")){
		    	rsProfile = stmtProfile.executeQuery(qry);
		    }
		    else {
		    	int rootId = 0;
					Statement stmtRootId = conn.createStatement();
		    	ResultSet rsRootId = stmtRootId.executeQuery("SELECT root_id as root_id FROM "+getJoomlaPrefix()+"asitvd_community_partner where user_id='"+user_id+"'");
		    	while(rsRootId.next()){
		    		rootId = rsRootId.getInt("root_id");
					}
		    	rsRootId.close();
		    	stmtRootId.close();
					
					if(root_id != null){
					//Not a root
						stmtRootId = conn.createStatement();
		    		rsRootId = stmtRootId.executeQuery("SELECT user_id as root_id FROM "+getJoomlaPrefix()+"asitvd_community_partner where partner_id='"+root_id+"'");
						while(rsRootId.next()){
		    			rootId = rsRootId.getInt("root_id");
						}
		    		rsRootId.close();
		    		stmtRootId.close();
						qry="SELECT prof.profile_id, prof.profile_name as profile_name FROM "+getJoomlaPrefix()+"asitvd_community_partner part, "+getJoomlaPrefix()+"asitvd_community_profile prof WHERE prof.profile_id=part.profile_id AND part.user_id='"+rootId+"'";
						rsProfile = stmtProfile.executeQuery(qry);
					}else{
						qry="SELECT prof.profile_id, prof.profile_name as profile_name FROM "+getJoomlaPrefix()+"asitvd_community_partner part, "+getJoomlaPrefix()+"asitvd_community_profile prof WHERE prof.profile_id=part.profile_id AND part.user_id='"+user_id+"'";
						rsProfile = stmtProfile.executeQuery(qry);
					}
		    }
		    	    
		    while(rsProfile.next()){
		    	if (rsProfile.getString("profile_name").equals("ASITVD_FOUNDER") || rsProfile.getString("profile_name").equals("ASITVD_MEMBER"))
		    		ApplicableRebate=true;
			}
		    
		    // D�but de la construction du fichier
			res.append("<easysdi:order>\n");
			res.append("<easysdi:header>\n");
			res.append("<easysdi:VERSION>2.0</easysdi:VERSION>\n");
			res.append("<easysdi:PLATFORM>"+getPlatformName()+"</easysdi:PLATFORM>\n");
			res.append("<easysdi:SERVER>36</easysdi:SERVER>\n");
			res.append("</easysdi:header>\n");
			res.append("<easysdi:REQUEST>\n");
			res.append("<easysdi:ID>"+order_id+"</easysdi:ID>\n");
			
			Statement stmtType = conn.createStatement();
			ResultSet rsType = stmtType.executeQuery("SELECT code FROM "+getJoomlaPrefix()+"easysdi_order_type_list where id = "+ type);
			
			String typeCode = "";
			while(rsType.next())
			{
				typeCode = rsType.getString("code");
			}
			
			rsType.close();
			stmtType.close();
			
			if(typeCode.equalsIgnoreCase("D")){
			    res.append("<easysdi:TYPE>ESTIMATE</easysdi:TYPE>\n");    
			}
			if(typeCode.equalsIgnoreCase("O")){
			    res.append("<easysdi:TYPE>ORDER</easysdi:TYPE>\n");    
			}
			
			res.append("<easysdi:NAME>"+encodeSpecialChars(name)+"</easysdi:NAME>\n");
			res.append("</easysdi:REQUEST>\n");
			res.append("<easysdi:CLIENT>\n");
			res.append("<easysdi:ID>"+partner_id+"</easysdi:ID>\n");
			Statement stmtAdd = conn.createStatement();
			ResultSet rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_name = 'EASYSDI_TYPE_CONTACT' and a.partner_id = "+partner_id);
	
			while(rsAddContact.next()){
	
			    res.append("<easysdi:CONTACTADDRESS>\n");
			    res.append("<easysdi:NAME1>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name1"))+"</easysdi:NAME1>\n");
			    res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name2"))+"</easysdi:NAME2>\n");
			    res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_firstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
			    res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_lastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
			    res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("address_street1"))+"</easysdi:ADDRESSSTREET1>\n");
			    res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("address_street2"))+"</easysdi:ADDRESSSTREET2>\n");
			    res.append("<easysdi:ZIP>"+rsAddContact.getString("address_postalcode")+"</easysdi:ZIP>\n");
			    res.append("<easysdi:LOCALITY>"+rsAddContact.getString("address_locality")+"</easysdi:LOCALITY>\n");
			    res.append("<easysdi:COUNTRY>"+rsAddContact.getString("country_code")+"</easysdi:COUNTRY>\n");
			    res.append("<easysdi:EMAIL>"+rsAddContact.getString("address_email")+"</easysdi:EMAIL>\n");
			    res.append("<easysdi:PHONE>"+rsAddContact.getString("address_phone")+"</easysdi:PHONE>\n");
			    res.append("<easysdi:FAX>"+rsAddContact.getString("address_fax")+"</easysdi:FAX>\n");
			    res.append("</easysdi:CONTACTADDRESS>\n");																
			}
			if (root_id != null)
				rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_name = 'EASYSDI_TYPE_INVOICING' and a.partner_id = "+root_id);
			else
				rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_name = 'EASYSDI_TYPE_INVOICING' and a.partner_id = "+partner_id);
			
			while(rsAddContact.next()){		     
			    res.append("<easysdi:INVOICEADDRESS>\n");
			    res.append("<easysdi:NAME1>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name1"))+"</easysdi:NAME1>\n");
			    res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name2"))+"</easysdi:NAME2>\n");
			    res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_firstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
			    res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_lastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
			    res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("address_street1"))+"</easysdi:ADDRESSSTREET1>\n");
			    res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("address_street2"))+"</easysdi:ADDRESSSTREET2>\n");
			    res.append("<easysdi:ZIP>"+rsAddContact.getString("address_postalcode")+"</easysdi:ZIP>\n");
			    res.append("<easysdi:LOCALITY>"+rsAddContact.getString("address_locality")+"</easysdi:LOCALITY>\n");
			    res.append("<easysdi:COUNTRY>"+rsAddContact.getString("country_code")+"</easysdi:COUNTRY>\n");
			    res.append("<easysdi:EMAIL>"+rsAddContact.getString("address_email")+"</easysdi:EMAIL>\n");
			    res.append("<easysdi:PHONE>"+rsAddContact.getString("address_phone")+"</easysdi:PHONE>\n");
			    res.append("<easysdi:FAX>"+rsAddContact.getString("address_fax")+"</easysdi:FAX>\n");
			    res.append("</easysdi:INVOICEADDRESS>\n");																
			}
	
			if (root_id != null)
				rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_name = 'EASYSDI_TYPE_DELIVERY' and a.partner_id = "+root_id);
			else
				rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_name = 'EASYSDI_TYPE_DELIVERY' and a.partner_id = "+partner_id);
			while(rsAddContact.next()){		     
			    res.append("<easysdi:DELIVERYADDRESS>\n");
			    res.append("<easysdi:NAME1>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name1"))+"</easysdi:NAME1>\n");
			    res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name2"))+"</easysdi:NAME2>\n");
			    res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_firstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
			    res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_lastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
			    res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("address_street1"))+"</easysdi:ADDRESSSTREET1>\n");
			    res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("address_street2"))+"</easysdi:ADDRESSSTREET2>\n");
			    res.append("<easysdi:ZIP>"+rsAddContact.getString("address_postalcode")+"</easysdi:ZIP>\n");
			    res.append("<easysdi:LOCALITY>"+rsAddContact.getString("address_locality")+"</easysdi:LOCALITY>\n");
			    res.append("<easysdi:COUNTRY>"+rsAddContact.getString("country_code")+"</easysdi:COUNTRY>\n");
			    res.append("<easysdi:EMAIL>"+rsAddContact.getString("address_email")+"</easysdi:EMAIL>\n");
			    res.append("<easysdi:PHONE>"+rsAddContact.getString("address_phone")+"</easysdi:PHONE>\n");
			    res.append("<easysdi:FAX>"+rsAddContact.getString("address_fax")+"</easysdi:FAX>\n");
			    res.append("</easysdi:DELIVERYADDRESS>\n");																
			}		
			res.append("</easysdi:CLIENT>\n");
			
			
			if (!third_party.equalsIgnoreCase("0")){
				res.append("<easysdi:TIERCE>\n");
				res.append("<easysdi:ID>"+encodeSpecialChars(third_party)+"</easysdi:ID>\n");
				Statement stmtTierce = conn.createStatement();
			    
				rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_name = 'EASYSDI_TYPE_CONTACT' and a.partner_id = "+third_party);
				
				while(rsAddContact.next()){
	
				    res.append("<easysdi:CONTACTADDRESS>\n");
				    res.append("<easysdi:NAME1>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name1"))+"</easysdi:NAME1>\n");
				    res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name2"))+"</easysdi:NAME2>\n");
				    res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_firstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
				    res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_lastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
				    res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("address_street1"))+"</easysdi:ADDRESSSTREET1>\n");
				    res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("address_street2"))+"</easysdi:ADDRESSSTREET2>\n");
				    res.append("<easysdi:ZIP>"+rsAddContact.getString("address_postalcode")+"</easysdi:ZIP>\n");
				    res.append("<easysdi:LOCALITY>"+rsAddContact.getString("address_locality")+"</easysdi:LOCALITY>\n");
				    res.append("<easysdi:COUNTRY>"+rsAddContact.getString("country_code")+"</easysdi:COUNTRY>\n");
				    res.append("<easysdi:EMAIL>"+rsAddContact.getString("address_email")+"</easysdi:EMAIL>\n");
				    res.append("<easysdi:PHONE>"+rsAddContact.getString("address_phone")+"</easysdi:PHONE>\n");
				    res.append("<easysdi:FAX>"+rsAddContact.getString("address_fax")+"</easysdi:FAX>\n");
				    res.append("</easysdi:CONTACTADDRESS>\n");																
				}
				rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_name = 'EASYSDI_TYPE_INVOICING' and a.partner_id = "+third_party);
				while(rsAddContact.next()){		     
				    res.append("<easysdi:INVOICEADDRESS>\n");
				    res.append("<easysdi:NAME1>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name1"))+"</easysdi:NAME1>\n");
				    res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name2"))+"</easysdi:NAME2>\n");
				    res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_firstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
				    res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_lastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
				    res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("address_street1"))+"</easysdi:ADDRESSSTREET1>\n");
				    res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("address_street2"))+"</easysdi:ADDRESSSTREET2>\n");
				    res.append("<easysdi:ZIP>"+rsAddContact.getString("address_postalcode")+"</easysdi:ZIP>\n");
				    res.append("<easysdi:LOCALITY>"+rsAddContact.getString("address_locality")+"</easysdi:LOCALITY>\n");
				    res.append("<easysdi:COUNTRY>"+rsAddContact.getString("country_code")+"</easysdi:COUNTRY>\n");
				    res.append("<easysdi:EMAIL>"+rsAddContact.getString("address_email")+"</easysdi:EMAIL>\n");
				    res.append("<easysdi:PHONE>"+rsAddContact.getString("address_phone")+"</easysdi:PHONE>\n");
				    res.append("<easysdi:FAX>"+rsAddContact.getString("address_fax")+"</easysdi:FAX>\n");
				    res.append("</easysdi:INVOICEADDRESS>\n");																
				}
	
				rsAddContact = stmtAdd.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_address a,"+getJoomlaPrefix()+"easysdi_community_address_type t where a.type_id = t.type_id and t.type_name = 'EASYSDI_TYPE_DELIVERY' and a.partner_id = "+third_party);
				while(rsAddContact.next()){		     
				    res.append("<easysdi:DELIVERYADDRESS>\n");
				    res.append("<easysdi:NAME1>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name1"))+"</easysdi:NAME1>\n");
				    res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("address_corporate_name2"))+"</easysdi:NAME2>\n");
				    res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_firstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
				    res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("address_agent_lastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
				    res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("address_street1"))+"</easysdi:ADDRESSSTREET1>\n");
				    res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("address_street2"))+"</easysdi:ADDRESSSTREET2>\n");
				    res.append("<easysdi:ZIP>"+rsAddContact.getString("address_postalcode")+"</easysdi:ZIP>\n");
				    res.append("<easysdi:LOCALITY>"+rsAddContact.getString("address_locality")+"</easysdi:LOCALITY>\n");
				    res.append("<easysdi:COUNTRY>"+rsAddContact.getString("country_code")+"</easysdi:COUNTRY>\n");
				    res.append("<easysdi:EMAIL>"+rsAddContact.getString("address_email")+"</easysdi:EMAIL>\n");
				    res.append("<easysdi:PHONE>"+rsAddContact.getString("address_phone")+"</easysdi:PHONE>\n");
				    res.append("<easysdi:FAX>"+rsAddContact.getString("address_fax")+"</easysdi:FAX>\n");
				    res.append("</easysdi:DELIVERYADDRESS>\n");																
				}	
				res.append("</easysdi:TIERCE>\n");
			
			}else{
			    res.append("<easysdi:TIERCE></easysdi:TIERCE>\n");  
			}
			
			//res.append("<easysdi:DISCOUNT>0</easysdi:DISCOUNT>\n");
			// Insertion du rabais s'il y en a un, sinon 0
			// HACK ASITVD: Insertion du rabais s'il est applicable selon le profil de l'utilisateur
			  
			if(isRebate==1 && ApplicableRebate==true){
			res.append("<easysdi:REBATE>"+rebate+"</easysdi:REBATE>\n");		    
			}
			else
			{
				res.append("<easysdi:REBATE>0</easysdi:REBATE>\n");		    
			}
			
			// Insertion du buffer de la commande
			if(buffer==0){
				res.append("<easysdi:BUFFER>0</easysdi:BUFFER>\n");		    
			}
			else
			{
				res.append("<easysdi:BUFFER>"+ buffer+"</easysdi:BUFFER>\n");		    
			}
			
			Statement stmtPerim = conn.createStatement();
			ResultSet rsPerim = stmtPerim.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_order_product_perimeters op, "+getJoomlaPrefix()+"easysdi_perimeter_definition pd where op.perimeter_id = pd.id and order_id = "+order_id);
	
			res.append("<easysdi:PERIMETER>\n");
			if (rsPerim.next()){
			    if (rsPerim.getString("id_field_name")==null || rsPerim.getString("id_field_name").length() ==0){
				res.append("<easysdi:TYPE>COORDINATES</easysdi:TYPE>\n");
			    }else{
				res.append("<easysdi:TYPE>VALUES</easysdi:TYPE>\n");
			    }		 
			    res.append("<easysdi:CODE>"+rsPerim.getString("perimeter_code")+"</easysdi:CODE>\n");
			    do{							   		
				res.append("<easysdi:CONTENT>"+encodeSpecialChars(rsPerim.getString("value"))+"</easysdi:CONTENT>\n");
			    }
			    while(rsPerim.next());
			}
			res.append("</easysdi:PERIMETER>\n");
			
			res.append("<easysdi:PRODUCTS>\n");
	
			rsPerim.close();
			stmtPerim.close();
			
			Statement stmtProducts = conn.createStatement();
			ResultSet rsProducts = stmtProducts.executeQuery("SELECT *,p.id as product_id FROM "+getJoomlaPrefix()+"easysdi_order_product_list pl ,"+getJoomlaPrefix()+"easysdi_product p, "+getJoomlaPrefix()+"easysdi_product_treatment_type treatment, "+getJoomlaPrefix()+"easysdi_community_partner part, "+getJoomlaPrefix()+"users u WHERE p.id=pl.product_id AND treatment.id=p.treatment_type AND pl.order_id = "+order_id + " AND p.partner_id = part.partner_id AND part.user_id = u.id AND u.username='"+userName+"' AND treatment.code='AUTO'");
	
			while(rsProducts.next()){
			    res.append("<easysdi:PRODUCT>\n");		
			    res.append("<easysdi:METADATA_ID>"+rsProducts.getString("metadata_id")+"</easysdi:METADATA_ID>\n");
			    res.append("<easysdi:ID>"+rsProducts.getString("product_id")+"</easysdi:ID>\n");
			    res.append("<easysdi:NAME>"+encodeSpecialChars(rsProducts.getString("data_title"))+"</easysdi:NAME>\n");
				
				res.append("<easysdi:PROPERTIES>\n");
	
				Statement stmtProp = conn.createStatement();
				ResultSet rsProp = stmtProp.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_order_product_properties op, "+getJoomlaPrefix()+"easysdi_product_properties_values_definition pd, "+getJoomlaPrefix()+"easysdi_order_product_list opl  where  opl.order_id = "+order_id+" and opl.id = op.order_product_list_id and op.property_id = pd.id and opl.product_id = " + rsProducts.getString("product_id"));
	
				while(rsProp.next()){
				    
				    res.append("<easysdi:PROPERTY>\n");
					    	 
				    res.append("<easysdi:CODE>"+rsProp.getString("code")+"</easysdi:CODE>\n");
				    res.append("<easysdi:VALUE>"+encodeSpecialChars(rsProp.getString("value"))+"</easysdi:VALUE>\n");		    		    	    
				    
				    res.append("</easysdi:PROPERTY>\n");
				}
				
				res.append("</easysdi:PROPERTIES>\n");
				res.append("</easysdi:PRODUCT>\n");		
			}
			res.append("</easysdi:PRODUCTS>\n");
	
			res.append("</easysdi:order>\n");
	
			rsProducts.close();
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

		stmt.executeUpdate("update "+getJoomlaPrefix()+"easysdi_order set status =(SELECT id FROM "+getJoomlaPrefix()+"easysdi_order_status_list where code='AWAIT') ,order_update = NOW()  where order_id in ("+orderIdClause+")");	    		    

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

	List lInputs = execute.getDataInputs().getInput();
	java.util.Iterator it = lInputs.iterator();
	String responseDate=null;
	String order_id=null;   
	String product_id=null;
	String data = "";
	String filename= "";
	String rebate ="0";
	String price="0";
	String remark="";
	    
	try{
	    while(it.hasNext())
        {
			net.opengis.wps._1_0.InputType inputType = (net.opengis.wps._1_0.InputType)it.next();
					
		    if (inputType.getIdentifier().getValue().equalsIgnoreCase("DATE")){
			    responseDate  = inputType.getData().getLiteralData().getValue();
	
			}
		    else if (inputType.getIdentifier().getValue().equalsIgnoreCase("REQUEST_ID")){
			order_id  = inputType.getData().getLiteralData().getValue();

		    }
		    else if (inputType.getIdentifier().getValue().equalsIgnoreCase("PRODUCT_ID")){
			    product_id  = inputType.getData().getLiteralData().getValue();

			}
		    else if (inputType.getIdentifier().getValue().equalsIgnoreCase("FILENAME")){
				filename  = inputType.getData().getLiteralData().getValue();

		    }
		    else if (inputType.getIdentifier().getValue().equalsIgnoreCase("REBATE")){
			rebate  = inputType.getData().getLiteralData().getValue();

		    }
		    else if (inputType.getIdentifier().getValue().equalsIgnoreCase("BILL")){
			price  = inputType.getData().getLiteralData().getValue();

		    }
		    else if (inputType.getIdentifier().getValue().equalsIgnoreCase("REMARK")){
			remark  = inputType.getData().getLiteralData().getValue();

		    }
	
			if (inputType.getData().getComplexData() != null)
			{
				if(inputType.getData().getComplexData().getContent() != null && inputType.getData().getComplexData().getContent().size() != 0)
					data = inputType.getData().getComplexData().getContent().get(0).toString();		
			}
	
	    }

		if (data == null) data ="";
	    if (filename == null) filename ="";
	    
	    if (responseDate!=null && order_id!=null && product_id!=null && filename !=null && data !=null)
	    {
			Connection conn = null;
			Class.forName(jdbcDriver).newInstance();
	
	
			conn =  DriverManager.getConnection(connexionString);
	
			Statement stmt = conn.createStatement();
			stmt.executeUpdate("update "+getJoomlaPrefix()+"easysdi_order set response_send = '1' ,response_date = str_to_date('"+responseDate+"', '%d.%m.%Y %H:%i:%s')  where order_id = "+order_id);
	
			PreparedStatement pre;
			if (data != "")
			{	
				pre = conn.prepareStatement("update "+getJoomlaPrefix()+"easysdi_order_product_list set price = "+price+",remark = '"+remark+"', filename =  '"+ filename+ "', status = (SELECT id FROM "+getJoomlaPrefix()+"easysdi_order_product_status_list where code='AVAILABLE'),data=? where order_id = "+order_id +" AND product_id = "+product_id);
				ByteArrayInputStream bais = new ByteArrayInputStream(Base64Coder.decode(data));
				
				pre.setBinaryStream(1,bais,data.length());
			}
			else
				pre = conn.prepareStatement("update "+getJoomlaPrefix()+"easysdi_order_product_list set price = "+price+",remark = '"+remark+"', status = (SELECT id FROM "+getJoomlaPrefix()+"easysdi_order_product_status_list where code='AVAILABLE') where order_id = "+order_id +" AND product_id = "+product_id);
			// Mise a jour de la requete
			pre.executeUpdate();
		
			int count = stmt.executeUpdate("update "+getJoomlaPrefix()+"easysdi_order set status =(SELECT id FROM "+getJoomlaPrefix()+"easysdi_order_status_list where code='FINISH')   where order_id = "+order_id +" AND (SELECT COUNT(*) FROM "+getJoomlaPrefix()+"easysdi_order_product_list WHERE order_id = "+order_id +")=(SELECT COUNT(*) FROM "+getJoomlaPrefix()+"easysdi_order_product_list WHERE status = (SELECT id FROM "+getJoomlaPrefix()+"easysdi_order_product_status_list where code='AVAILABLE') AND order_id = "+order_id +")");
			if (count == 0){
			    stmt.executeUpdate("update "+getJoomlaPrefix()+"easysdi_order set status = (SELECT id FROM "+getJoomlaPrefix()+"easysdi_order_status_list where code='PROGRESS')   where order_id = "+order_id );
			}
	
			pre.close();
			stmt.close();
			
			// Mail de notification
			Statement stmtTotal = conn.createStatement();
			ResultSet rsTotal = stmtTotal.executeQuery("SELECT COUNT(*) as total FROM "+getJoomlaPrefix()+"easysdi_order_product_list p,"+getJoomlaPrefix()+"easysdi_order_product_status_list sl WHERE p.status=sl.id and p.order_id="+order_id+" AND sl.code = 'AWAIT'");
			
			int total = 0;
			while(rsTotal.next()){
				total = rsTotal.getInt("total");
			}
			
			rsTotal.close();
			stmtTotal.close();
	
			Statement stmtTotalProduct = conn.createStatement();
			ResultSet rsTotalProduct = stmtTotalProduct.executeQuery("SELECT COUNT(*) as total FROM "+getJoomlaPrefix()+"easysdi_order_product_list p,"+getJoomlaPrefix()+"easysdi_order_product_status_list sl WHERE p.status=sl.id and p.order_id="+order_id);
			
			int totalProduct = 0;
			while(rsTotalProduct.next()){
				totalProduct = rsTotalProduct.getInt("total");
			}
			
			rsTotalProduct.close();
			stmtTotalProduct.close();
			
			Statement stmtRow = conn.createStatement();
			ResultSet rsRow = stmtRow.executeQuery("SELECT o.user_id as user_id FROM "+getJoomlaPrefix()+"easysdi_order o,"+getJoomlaPrefix()+"users u WHERE o.user_id = u.id AND order_id="+order_id);
			int user_id = 0;
			while(rsRow.next()){
				user_id = rsRow.getInt("user_id");
			}	
			rsRow.close();
			stmtRow.close();

			Statement stmtPartner = conn.createStatement();
			ResultSet rsPartner = stmtPartner.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_partner WHERE user_id="+user_id);
			
			int notify_order_ready = 0;
			while(rsPartner.next()){
				notify_order_ready = rsPartner.getInt("notify_order_ready");
			}
			
			rsPartner.close();
			stmtPartner.close();
			conn.close();
			
			if (total == 0 && notify_order_ready == 1)
			{
				notifyUserByEmail(Integer.decode(order_id), getLanguageValue(languageFile, "EASYSDI_CMD_READY_MAIL_SUBJECT"),getLanguageValue(languageFile, "EASYSDI_CMD_READY_MAIL_BODY"));
			}
			else if (total == totalProduct -1 && notify_order_ready == 1)
			{
				notifyUserByEmail(Integer.decode(order_id), getLanguageValue(languageFile, "EASYSDI_CMD_FIRST_PROD_READY_MAIL_SUBJECT"), getLanguageValue(languageFile, "EASYSDI_CMD_FIRST_PROD_READY_MAIL_BODY"));
			}
			
			
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
		    m.setProperty(Marshaller.JAXB_ENCODING, "UTF-8");
		    m.setProperty(Marshaller.JAXB_FRAGMENT, Boolean.FALSE);

		    ByteArrayOutputStream baos = new ByteArrayOutputStream();

		    m.marshal(er, baos);

		    return baos.toString();
	    }
	}catch (Exception e)
	{
	    e.printStackTrace();
	    // Ecriture d'un log texte sur le serveur en cas de probleme
	    try
	    {
		StringWriter sw = new StringWriter();
	        PrintWriter pw = new PrintWriter(sw);
	        e.printStackTrace(pw);
	        File f = new File("/home/users/asitvd/tomcat/logs/errorStream.txt");
	        //File f = new File("errorStream.txt");
	        FileWriter fw = new FileWriter(f,true);
	        fw.write(sw.toString());
	        fw.write(e.getMessage());
		fw.write("responseDate:"+responseDate);
		fw.write("order_id:"+order_id);
		fw.write("product_id:"+product_id);
		fw.write("data:"+data);
		fw.write("filename:"+filename);
		fw.write("rebate:"+rebate);
		fw.write("price:"+price);
		fw.write("remark:"+remark);
	        fw.close();
	        pw.close();
	    }
	    catch (Exception ex){
		    ex.printStackTrace();
	    }
	}

	return error ("ERROR","An error just occured");
    }

    public static String encodeSpecialChars(String s){
	s = s.replace("&", "&#38;");
	return s;
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
    
    private void notifyUserByEmail(int order_id, String subject, String body){
		/*
		 * Envoi un mail a l'utilisateur pour le prevenir que la commande est traitee.
		 */
    	try
    	{
	    	Connection conn = null;
	    	conn =  DriverManager.getConnection(getConnexionString());
		    
	    	Statement stmtRow = conn.createStatement();
	    	ResultSet rsRow = stmtRow.executeQuery("SELECT o.order_id as order_id, o.user_id as user_id,u.email as email,o.name as data_title FROM "+getJoomlaPrefix()+"easysdi_order o,"+getJoomlaPrefix()+"users u WHERE o.user_id = u.id AND order_id="+order_id);
			String orderId = "";
			String email = "";
			String data_title = "";
			int user_id = 0;
			while(rsRow.next()){
				orderId = rsRow.getString("order_id");
				email = rsRow.getString("email");
				data_title = rsRow.getString("data_title");
				user_id = rsRow.getInt("user_id");
			}
			
			rsRow.close();
			stmtRow.close();
			
			Statement stmtPartner = conn.createStatement();
			ResultSet rsPartner = stmtPartner.executeQuery("SELECT * FROM "+getJoomlaPrefix()+"easysdi_community_partner WHERE user_id="+user_id);
			
			int notify_order_ready = 0;
			while(rsPartner.next()){
				notify_order_ready = rsPartner.getInt("notify_order_ready");
			}
			
			rsPartner.close();
			stmtPartner.close();
			
			if (notify_order_ready == 1) 
			{
			    Mailer mailer = new Mailer();
			    //the domains of these email addresses should be valid,
			    //or the example will fail:
			    //Special treatment for escape characters
			    body = body.replace("\\n", "\n");
			    body = body.replace("\\t", "\t");
			    body = body.replace("\\r", "\r");
			    subject = subject.replace("\\n", "\n");
			    subject = subject.replace("\\t", "\t");
			    subject = subject.replace("\\r", "\r");
			    String[] aStr = {data_title, orderId};
			    
			    mailer.sendEmail(senderEmail, senderName, email, new PrintfFormat(subject).sprintf(aStr), new PrintfFormat(body).sprintf(aStr));
			}
	}
    	catch (Exception e)
    	{
    		e.printStackTrace();
    	}
	}
    
    public static String getLanguageValue(String fileName, String searched)  
    {
        String line = null;
        String body = null;

        BufferedReader in = null;
        try {
            in = new BufferedReader (new FileReader(fileName));
            
            while ((line=in.readLine())!=null)
			{
				if (line.contains(searched))
				{
					body =  line.substring(line.indexOf("=")+1);
				}
			}			
        } 
        catch (IOException ex) 
        {
            System.out.println("Problem reading file.\n" + ex.getMessage());
        } 
        finally 
        {
            try { if (in!=null) in.close(); } catch(IOException ignore) {}
        }
        
        return body;
    }
}
