/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dï¿½Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

import java.io.FileWriter;
import java.io.StringWriter;
import java.io.PrintWriter;

import java.io.BufferedReader;
import java.io.BufferedWriter;
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
import java.sql.SQLException;
import java.sql.Statement;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.List;
import java.util.Properties;
import java.util.Vector;
import java.util.Random;
import java.util.logging.Logger;

import javax.servlet.ServletConfig;
import javax.servlet.ServletContext;
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

import org.easysdi.security.CurrentUser;
import org.easysdi.services.wps.Base64Coder;
import org.easysdi.services.wps.Mailer;
import org.easysdi.services.wps.PrintfFormat;
import org.springframework.beans.factory.config.PropertyPlaceholderConfigurer;
import org.springframework.context.ApplicationContext;
import org.springframework.context.ApplicationContextAware;
import org.springframework.web.context.WebApplicationContext;
import org.springframework.web.context.support.WebApplicationContextUtils;
import org.springframework.web.servlet.support.RequestContextUtils;
import org.w3c.dom.Document;
import org.w3c.dom.NodeList;

public class WPSServlet extends HttpServlet {


	//Connexion string to the joomla database
	private String connexionString = "";
	//Joomla table prefix
	private String joomlaPrefix = "";
	//Jdbc Driver to connect to Joomla 
	private String jdbcDriver ="";
	//Name of the platform
	private String platformName = "";

	private String languageFile = "";

	private String senderEmail = "";

	private String senderName = "";
	
	Logger logger = Logger.getLogger(WPSServlet.class.toString());
	
	private Properties prop = new Properties();


	public void init(ServletConfig config) throws ServletException {
		//System.setProperty("org.apache.commons.logging.Log", "org.apache.commons.logging.impl.NoOpLog");

		ServletContext servletContext =config.getServletContext();
		WebApplicationContext wac = WebApplicationContextUtils.getRequiredWebApplicationContext(servletContext);
	    JDataSource jds = (JDataSource)wac.getBean("jDataSource");
		
		String conn = jds.getJdbcUrl()+"?user="+jds.getJdbcUser()+"&password="+jds.getJdbcPwd()+"&noDatetimeStringSync=true";
		String prefix = jds.getjPrefix();
		String driver = jds.getJdbcDriver();
		String platform = jds.getJplatformname();
		String language = jds.getJlanguagefile();
		String sender = jds.getjSender();
		String senderTitle = jds.getjSenderTitle();

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

	String getP(String s){
		return prop.getProperty(s);
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
					resp.getOutputStream().write(executeGetOrders(req, execute).getBytes());
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
			int rand = new Random().nextInt(10000);
			logger.info("Http Get, id:("+rand+"), returned");
			if(operation == null){		
				resp.setContentType("text/xml");
				resp.getOutputStream().write(error("OPERATIONNOTDEFINED","No operation is not defined in GET method. Response id ("+rand+")").getBytes());
			}else if (operation.equalsIgnoreCase("GetCapabilities")){		
				resp.setContentType("text/xml");		
				resp.getOutputStream().write(getCapabilities(req).getBytes());
			}else{
				resp.setContentType("text/xml");
				resp.getOutputStream().write(error("OPERATIONNOTDEFINED","This operation is not defined in GET method. Response id ("+rand+")").getBytes());
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
	private String getJP(){

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





	private String executeGetOrders (HttpServletRequest req, net.opengis.wps._1_0.Execute execute){
		Connection conn = null;
		logger.info("execute get order called by "+req.getRemoteHost());
		try {

			List lInputs = execute.getDataInputs().getInput();

			java.util.Iterator itInputs = lInputs.iterator();

			String userName = null;
			String statusToRead ="SENT";

			while(itInputs.hasNext()){
				net.opengis.wps._1_0.InputType inputType = (net.opengis.wps._1_0.InputType)itInputs.next();
				if (inputType.getIdentifier().getValue().equalsIgnoreCase("userName")){
					// Obsolete now, taking user from spring authentication
					// remove when all clients support this.
					userName = inputType.getData().getLiteralData().getValue();
				}else{
					if (inputType.getIdentifier().getValue().equalsIgnoreCase("status")){
						statusToRead = inputType.getData().getLiteralData().getValue();
					}   		    
				}
			}
			
			//Get userName from Spring Security
			userName = CurrentUser.getCurrentPrincipal();

			if (userName == null){		    
				return error("PARTNERNOTDEFINED","paremeter containaing the partner id is not defined");
			}
			
			logger.info("logged with:"+CurrentUser.getCurrentPrincipal());

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

			/* Get connection from spring */
			Class.forName(getJdbcDriver()).newInstance();
			conn =  DriverManager.getConnection(getConnexionString());
			
			Statement stmt = conn.createStatement();

			String query = "SELECT DISTINCT o.id as order_id, u.id as user_id, o.name, o.type_id, o.thirdparty_id, p.id as accountId, p.root_id, o.buffer "+
			"FROM "+getJP()+"sdi_order o, "+getJP()+"sdi_account p, "+getJP()+"sdi_list_orderstatus osl, "+getJP()+"sdi_order_product opl , "+getJP()+"sdi_product prod, "+getJP()+"sdi_account part, "+getJP()+"sdi_objectversion ov, "+getJP()+"sdi_object ob, "+getJP()+"users u "+
			"WHERE opl.order_id = o.id AND osl.id=o.status_id AND osl.code = '"+statusToRead+"' AND o.user_id = p.user_id "+
			"and opl.product_id = prod.id AND prod.objectversion_id = ov.id AND ov.object_id=ob.id AND ob.account_id = part.id AND part.user_id = u.id AND u.username='"+userName+"'";

			logger.info("query getOrder with:"+query);

			ResultSet rs = stmt.executeQuery(query);

			StringBuffer res = new StringBuffer();
			res.append("<easysdi:orders xmlns:easysdi=\"http://www.easysdi.org\">");
			List<String> orderIdList = new Vector<String>();
			while (rs.next()) 
			{
				String order_id = rs.getString("order_id");
				logger.info("GetOders, sending order->"+order_id);
				orderIdList.add(order_id);
				String name = rs.getString("name");
				int type = rs.getInt("type_id");
				String third_party = rs.getString("thirdparty_id");
				String user_id = rs.getString("user_id");
				String account_id = rs.getString("accountId");
				String root_id = rs.getString("root_id");
				int buffer = 0;
				buffer = rs.getInt("buffer");

				// Recuperation du rabais pour le fournisseur
				int isRebate = 0;
				String rebate = "0";

				
				Statement stmtRebate = conn.createStatement();
				//ResultSet rsRebate = stmtRebate.executeQuery("SELECT isrebate, rebate FROM "+getJoomlaPrefix()+"sdi_account part, "+getJoomlaPrefix()+"users u WHERE u.id=part.user_id AND u.username='"+userName+"'");
				String qry = "SELECT ap.applyRebate as isrebate, part.rebate as rebate FROM "+getJP()+"sdi_account part, "+getJP()+"users u, "+getJP()+"sdi_accountprofile ap, "+getJP()+"sdi_account_accountprofile aap ";
				qry += "WHERE part.id =aap.account_id AND aap.accountprofile_id=ap.id AND u.id=part.user_id AND u.username='"+userName+"'";
					
				ResultSet rsRebate = stmtRebate.executeQuery(qry);
				
				while(rsRebate.next()){
					isRebate = rsRebate.getInt("isrebate");
					rebate = rsRebate.getString("rebate");
				}

				rsRebate.close();
				stmtRebate.close();
				 

				// Contrôle de l'application ou pas du rabais selon
				// le profil du client qui passe la commande et/ou du tiers pour qui la commande est passï¿½e
				boolean ApplicableRebate=false;

				Statement stmtProfile = conn.createStatement();
				ResultSet rsProfile;
				
				//String qry="SELECT prof.profile_id, prof.profile_name as profile_name FROM "+getJoomlaPrefix()+"asitvd_community_partner part, "+getJoomlaPrefix()+"asitvd_community_profile prof WHERE prof.profile_id=part.profile_id AND part.partner_id='"+third_party+"'";
				qry = "SELECT ap.id as profile_id, ap.name as profile_name FROM "+getJP()+"sdi_account part, "+getJP()+"sdi_accountprofile ap, "+getJP()+"sdi_account_accountprofile aap ";
				qry += "WHERE part.id =aap.account_id AND aap.accountprofile_id=ap.id AND part.id='"+third_party+"'";
				
				if (!third_party.equalsIgnoreCase("0")){
					rsProfile = stmtProfile.executeQuery(qry);
				}
				else {
					int rootId = 0;
					Statement stmtRootId = conn.createStatement();
					ResultSet rsRootId = stmtRootId.executeQuery("SELECT root_id as root_id FROM "+getJP()+"sdi_account where user_id='"+user_id+"'");
					while(rsRootId.next()){
						rootId = rsRootId.getInt("root_id");
					}
					rsRootId.close();
					stmtRootId.close();

					if(root_id != null){
						//Not a root
						stmtRootId = conn.createStatement();
						rsRootId = stmtRootId.executeQuery("SELECT user_id as root_id FROM "+getJP()+"sdi_account where id='"+root_id+"'");
						while(rsRootId.next()){
							rootId = rsRootId.getInt("root_id");
						}
						rsRootId.close();
						stmtRootId.close();
						//qry="SELECT prof.profile_id, prof.profile_name as profile_name FROM "+getJP()+"sdi_account part, "+getJP()+"asitvd_community_profile prof WHERE prof.profile_id=part.profile_id AND part.user_id='"+rootId+"'";
						qry = "SELECT ap.id as profile_id, ap.name as profile_name FROM "+getJP()+"sdi_account part, "+getJP()+"sdi_accountprofile ap, "+getJP()+"sdi_account_accountprofile aap ";
						qry += "WHERE part.id =aap.account_id AND aap.accountprofile_id=ap.id AND part.user_id='"+rootId+"'";
						
						rsProfile = stmtProfile.executeQuery(qry);
					}else{
						//qry="SELECT prof.profile_id, prof.profile_name as profile_name FROM "+getJP()+"sdi_account part, "+getJP()+"asitvd_community_profile prof WHERE prof.profile_id=part.profile_id AND part.user_id='"+user_id+"'";
						qry = "SELECT ap.id as profile_id, ap.name as profile_name FROM "+getJP()+"sdi_account part, "+getJP()+"sdi_accountprofile ap, "+getJP()+"sdi_account_accountprofile aap ";
						qry += "WHERE part.id =aap.account_id AND aap.accountprofile_id=ap.id AND part.user_id='"+user_id+"'";
						rsProfile = stmtProfile.executeQuery(qry);
					}
				}

				while(rsProfile.next()){
					if (rsProfile.getString("profile_name").equals("ASITVD_FOUNDER") || rsProfile.getString("profile_name").equals("ASITVD_MEMBER"))
						ApplicableRebate=true;
				}

				// Dï¿½but de la construction du fichier
				res.append("<easysdi:order>\n");
				res.append("<easysdi:header>\n");
				res.append("<easysdi:VERSION>2.0</easysdi:VERSION>\n");
				res.append("<easysdi:PLATFORM>"+getPlatformName()+"</easysdi:PLATFORM>\n");
				res.append("<easysdi:SERVER>36</easysdi:SERVER>\n");
				res.append("</easysdi:header>\n");
				res.append("<easysdi:REQUEST>\n");
				res.append("<easysdi:ID>"+order_id+"</easysdi:ID>\n");

				Statement stmtType = conn.createStatement();
				ResultSet rsType = stmtType.executeQuery("SELECT code FROM "+getJP()+"sdi_list_ordertype where id = "+ type);

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
				res.append("<easysdi:ID>"+account_id+"</easysdi:ID>\n");
				Statement stmtAdd = conn.createStatement();
				query = "SELECT *, c.code as countryCode FROM "+getJP()+"sdi_address a, "+getJP()+"sdi_list_addresstype t, "+
				getJP()+"sdi_list_country c "+
				"where a.type_id = t.id and a.country_id = c.id and t.name = 'EASYSDI_TYPE_CONTACT' and a.account_id = "+account_id;
				//System.out.println(query);
				ResultSet rsAddContact = stmtAdd.executeQuery(query);

				//get the client joomla name
				String clientJoomlaName = "";
				Statement stmtClientJoomlaName = conn.createStatement();
				ResultSet rsClientJoomlaName = stmtClientJoomlaName.executeQuery("SELECT u.name FROM "+getJP()+"sdi_account a, "+getJP()+"users u where a.user_id = u.id and a.id = "+account_id);
				while(rsClientJoomlaName.next()){
					clientJoomlaName = rsClientJoomlaName.getString("name");
				}
				rsClientJoomlaName.close();
				stmtClientJoomlaName.close();

				while(rsAddContact.next()){

					res.append("<easysdi:CONTACTADDRESS>\n");
					res.append("<easysdi:NAME1>"+encodeSpecialChars(clientJoomlaName)+"</easysdi:NAME1>\n");
					res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("corporatename2"))+"</easysdi:NAME2>\n");
					res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("agentfirstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
					res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("agentlastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
					res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("street1"))+"</easysdi:ADDRESSSTREET1>\n");
					res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("street2"))+"</easysdi:ADDRESSSTREET2>\n");
					res.append("<easysdi:ZIP>"+rsAddContact.getString("postalcode")+"</easysdi:ZIP>\n");
					res.append("<easysdi:LOCALITY>"+rsAddContact.getString("locality")+"</easysdi:LOCALITY>\n");
					res.append("<easysdi:COUNTRY>"+rsAddContact.getString("countryCode")+"</easysdi:COUNTRY>\n");
					res.append("<easysdi:EMAIL>"+rsAddContact.getString("email")+"</easysdi:EMAIL>\n");
					res.append("<easysdi:PHONE>"+rsAddContact.getString("phone")+"</easysdi:PHONE>\n");
					res.append("<easysdi:FAX>"+rsAddContact.getString("fax")+"</easysdi:FAX>\n");
					res.append("</easysdi:CONTACTADDRESS>\n");																
				}
				if (root_id != null){
					query = "SELECT *, c.code as countryCode FROM "+getJP()+"sdi_address a,"+getJP()+"sdi_list_addresstype t, "+
					getJP()+"sdi_list_country c "+
					"where a.type_id = t.id and a.country_id = c.id and t.name = 'EASYSDI_TYPE_INVOICING' and a.account_id = "+root_id;
					//System.out.println(query);
					rsAddContact = stmtAdd.executeQuery(query);					
				}else{
					query = "SELECT *, c.code as countryCode FROM "+getJP()+"sdi_address a,"+getJP()+"sdi_list_addresstype t, "+
					getJP()+"sdi_list_country c "+
					"where a.type_id = t.id and a.country_id = c.id and t.name = 'EASYSDI_TYPE_INVOICING' and a.account_id = "+account_id;
					//System.out.println(query);
					rsAddContact = stmtAdd.executeQuery(query);	
				}
				while(rsAddContact.next()){		     
					res.append("<easysdi:INVOICEADDRESS>\n");
					res.append("<easysdi:NAME1>"+encodeSpecialChars(rsAddContact.getString("corporatename1"))+"</easysdi:NAME1>\n");
					res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("corporatename2"))+"</easysdi:NAME2>\n");
					res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("agentfirstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
					res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("agentlastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
					res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("street1"))+"</easysdi:ADDRESSSTREET1>\n");
					res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("street2"))+"</easysdi:ADDRESSSTREET2>\n");
					res.append("<easysdi:ZIP>"+rsAddContact.getString("postalcode")+"</easysdi:ZIP>\n");
					res.append("<easysdi:LOCALITY>"+rsAddContact.getString("locality")+"</easysdi:LOCALITY>\n");
					res.append("<easysdi:COUNTRY>"+rsAddContact.getString("countryCode")+"</easysdi:COUNTRY>\n");
					res.append("<easysdi:EMAIL>"+rsAddContact.getString("email")+"</easysdi:EMAIL>\n");
					res.append("<easysdi:PHONE>"+rsAddContact.getString("phone")+"</easysdi:PHONE>\n");
					res.append("<easysdi:FAX>"+rsAddContact.getString("fax")+"</easysdi:FAX>\n");
					res.append("</easysdi:INVOICEADDRESS>\n");																
				}

				if (root_id != null){
					query = "SELECT *, c.code as countryCode FROM "+getJP()+"sdi_address a,"+getJP()+"sdi_list_addresstype t, "+
					getJP()+"sdi_list_country c "+
					"where a.type_id = t.id and a.country_id = c.id and t.name = 'EASYSDI_TYPE_DELIVERY' and a.account_id = "+root_id;
					//System.out.println(query);
					rsAddContact = stmtAdd.executeQuery(query);
				}else{
					query = "SELECT *, c.code as countryCode FROM "+getJP()+"sdi_address a,"+getJP()+"sdi_list_addresstype t, "+
					getJP()+"sdi_list_country c "+
					"where a.type_id = t.id and a.country_id = c.id and t.name = 'EASYSDI_TYPE_DELIVERY' and a.account_id = "+account_id;
					//System.out.println(query);
					rsAddContact = stmtAdd.executeQuery(query);
				}
				while(rsAddContact.next()){		     
					res.append("<easysdi:DELIVERYADDRESS>\n");
					res.append("<easysdi:NAME1>"+encodeSpecialChars(rsAddContact.getString("corporatename1"))+"</easysdi:NAME1>\n");
					res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("corporatename2"))+"</easysdi:NAME2>\n");
					res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("agentfirstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
					res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("agentlastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
					res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("street1"))+"</easysdi:ADDRESSSTREET1>\n");
					res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("street2"))+"</easysdi:ADDRESSSTREET2>\n");
					res.append("<easysdi:ZIP>"+rsAddContact.getString("postalcode")+"</easysdi:ZIP>\n");
					res.append("<easysdi:LOCALITY>"+rsAddContact.getString("locality")+"</easysdi:LOCALITY>\n");
					res.append("<easysdi:COUNTRY>"+rsAddContact.getString("countryCode")+"</easysdi:COUNTRY>\n");
					res.append("<easysdi:EMAIL>"+rsAddContact.getString("email")+"</easysdi:EMAIL>\n");
					res.append("<easysdi:PHONE>"+rsAddContact.getString("phone")+"</easysdi:PHONE>\n");
					res.append("<easysdi:FAX>"+rsAddContact.getString("fax")+"</easysdi:FAX>\n");
					res.append("</easysdi:DELIVERYADDRESS>\n");																
				}		
				res.append("</easysdi:CLIENT>\n");


				if (!third_party.equalsIgnoreCase("0")){
					res.append("<easysdi:TIERCE>\n");
					res.append("<easysdi:ID>"+encodeSpecialChars(third_party)+"</easysdi:ID>\n");
					//Statement stmtTierce = conn.createStatement();

					query = "SELECT *, c.code as countryCode FROM "+getJP()+"sdi_address a,"+getJP()+"sdi_list_addresstype t, "+
					getJP()+"sdi_list_country c "+
					"where a.type_id = t.id and a.country_id = c.id and t.name = 'EASYSDI_TYPE_CONTACT' and a.account_id = "+third_party;
					//System.out.println(query);
					rsAddContact = stmtAdd.executeQuery(query);

					//get the tierce Joomla name
					String tierceJoomlaName = "";
					Statement stmtTierceJoomlaName = conn.createStatement();
					ResultSet rsTierceJoomlaName = stmtTierceJoomlaName.executeQuery("SELECT u.name FROM "+getJP()+"sdi_account a, "+getJP()+"users u where a.user_id = u.id and a.id = "+third_party);
					while(rsTierceJoomlaName.next()){
						tierceJoomlaName = rsTierceJoomlaName.getString("name");
					}
					rsTierceJoomlaName.close();
					stmtTierceJoomlaName.close();

					while(rsAddContact.next()){

						res.append("<easysdi:CONTACTADDRESS>\n");
						res.append("<easysdi:NAME1>"+encodeSpecialChars(tierceJoomlaName)+"</easysdi:NAME1>\n");
						res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("corporatename2"))+"</easysdi:NAME2>\n");
						res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("agentfirstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
						res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("agentlastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
						res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("street1"))+"</easysdi:ADDRESSSTREET1>\n");
						res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("street2"))+"</easysdi:ADDRESSSTREET2>\n");
						res.append("<easysdi:ZIP>"+rsAddContact.getString("postalcode")+"</easysdi:ZIP>\n");
						res.append("<easysdi:LOCALITY>"+rsAddContact.getString("locality")+"</easysdi:LOCALITY>\n");
						res.append("<easysdi:COUNTRY>"+rsAddContact.getString("countryCode")+"</easysdi:COUNTRY>\n");
						res.append("<easysdi:EMAIL>"+rsAddContact.getString("email")+"</easysdi:EMAIL>\n");
						res.append("<easysdi:PHONE>"+rsAddContact.getString("phone")+"</easysdi:PHONE>\n");
						res.append("<easysdi:FAX>"+rsAddContact.getString("fax")+"</easysdi:FAX>\n");
						res.append("</easysdi:CONTACTADDRESS>\n");																
					}
					query ="SELECT *, c.code as countryCode  FROM "+getJP()+"sdi_address a,"+getJP()+"sdi_list_addresstype t, "+
					getJP()+"sdi_list_country c "+
					"where a.type_id = t.id and a.country_id = c.id and t.name = 'EASYSDI_TYPE_INVOICING' and a.account_id = "+third_party;
					//System.out.println(query);
					rsAddContact = stmtAdd.executeQuery(query);

					while(rsAddContact.next()){		     
						res.append("<easysdi:INVOICEADDRESS>\n");
						res.append("<easysdi:NAME1>"+encodeSpecialChars(rsAddContact.getString("corporatename1"))+"</easysdi:NAME1>\n");
						res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("corporatename2"))+"</easysdi:NAME2>\n");
						res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("agentfirstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
						res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("agentlastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
						res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("street1"))+"</easysdi:ADDRESSSTREET1>\n");
						res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("street2"))+"</easysdi:ADDRESSSTREET2>\n");
						res.append("<easysdi:ZIP>"+rsAddContact.getString("postalcode")+"</easysdi:ZIP>\n");
						res.append("<easysdi:LOCALITY>"+rsAddContact.getString("locality")+"</easysdi:LOCALITY>\n");
						res.append("<easysdi:COUNTRY>"+rsAddContact.getString("countryCode")+"</easysdi:COUNTRY>\n");
						res.append("<easysdi:EMAIL>"+rsAddContact.getString("email")+"</easysdi:EMAIL>\n");
						res.append("<easysdi:PHONE>"+rsAddContact.getString("phone")+"</easysdi:PHONE>\n");
						res.append("<easysdi:FAX>"+rsAddContact.getString("fax")+"</easysdi:FAX>\n");
						res.append("</easysdi:INVOICEADDRESS>\n");																
					}
					query = "SELECT *, c.code as countryCode FROM "+getJP()+"sdi_address a,"+getJP()+"sdi_list_addresstype t, "+
					getJP()+"sdi_list_country c "+
					"where a.type_id = t.id and a.country_id = c.id and t.name = 'EASYSDI_TYPE_DELIVERY' and a.account_id = "+third_party;
					//System.out.println(query);
					rsAddContact = stmtAdd.executeQuery(query);
					while(rsAddContact.next()){		     
						res.append("<easysdi:DELIVERYADDRESS>\n");
						res.append("<easysdi:NAME1>"+encodeSpecialChars(rsAddContact.getString("corporatename1"))+"</easysdi:NAME1>\n");
						res.append("<easysdi:NAME2>"+encodeSpecialChars(rsAddContact.getString("corporatename2"))+"</easysdi:NAME2>\n");
						res.append("<easysdi:AGENTFIRSTNAME>"+encodeSpecialChars(rsAddContact.getString("agentfirstname"))+"</easysdi:AGENTFIRSTNAME>\n") ;
						res.append("<easysdi:AGENTLASTNAME>"+encodeSpecialChars(rsAddContact.getString("agentlastname"))+"</easysdi:AGENTLASTNAME>\n") ;		
						res.append("<easysdi:ADDRESSSTREET1>"+encodeSpecialChars(rsAddContact.getString("street1"))+"</easysdi:ADDRESSSTREET1>\n");
						res.append("<easysdi:ADDRESSSTREET2>"+encodeSpecialChars(rsAddContact.getString("street2"))+"</easysdi:ADDRESSSTREET2>\n");
						res.append("<easysdi:ZIP>"+rsAddContact.getString("postalcode")+"</easysdi:ZIP>\n");
						res.append("<easysdi:LOCALITY>"+rsAddContact.getString("locality")+"</easysdi:LOCALITY>\n");
						res.append("<easysdi:COUNTRY>"+rsAddContact.getString("countryCode")+"</easysdi:COUNTRY>\n");
						res.append("<easysdi:EMAIL>"+rsAddContact.getString("email")+"</easysdi:EMAIL>\n");
						res.append("<easysdi:PHONE>"+rsAddContact.getString("phone")+"</easysdi:PHONE>\n");
						res.append("<easysdi:FAX>"+rsAddContact.getString("fax")+"</easysdi:FAX>\n");
						res.append("</easysdi:DELIVERYADDRESS>\n");																
					}	
					res.append("</easysdi:TIERCE>\n");

				}else{
					res.append("<easysdi:TIERCE></easysdi:TIERCE>\n");  
				}

				// Insertion du rabais s'il y en a un, sinon 0

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
				query = "SELECT op.value, p.code, p.fieldname FROM "+getJP()+"sdi_order_perimeter op, "+getJP()+"sdi_perimeter p "+
				"where op.perimeter_id = p.id and op.order_id = "+order_id+" order by op.id";
				//System.out.println(query);
				ResultSet rsPerim = stmtPerim.executeQuery(query);

				res.append("<easysdi:PERIMETER>\n");
				if (rsPerim.next()){
					if (rsPerim.getString("fieldname")==null || rsPerim.getString("fieldname").length() == 0){
						res.append("<easysdi:TYPE>COORDINATES</easysdi:TYPE>\n");
					}else{
						res.append("<easysdi:TYPE>VALUES</easysdi:TYPE>\n");
					}		 
					res.append("<easysdi:CODE>"+rsPerim.getString("code")+"</easysdi:CODE>\n");
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

				query = "SELECT p.name as name, meta.guid as metadata_id, p.id as product_id "+
				"FROM "+getJP()+"sdi_order_product op ,"+getJP()+"sdi_product p, "+getJP()+"sdi_list_treatmenttype treatment, "+getJP()+"sdi_metadata meta, "+
				getJP()+"sdi_account acc, "+getJP()+"sdi_objectversion ov, "+getJP()+"sdi_object ob, "+getJP()+"users u "+
				"WHERE p.id=op.product_id AND treatment.id=p.treatmenttype_id AND op.order_id = "+order_id + " AND p.objectversion_id=ov.id"+" AND ov.object_id=ob.id"+" AND ob.account_id = acc.id "+" AND ov.metadata_id = meta.id "+
				"AND acc.user_id = u.id AND u.username='"+userName+"' AND treatment.code='AUTO'";

				ResultSet rsProducts = stmtProducts.executeQuery(query);

				logger.info("product query->"+query);

				while(rsProducts.next()){
					res.append("<easysdi:PRODUCT>\n");
					res.append("<easysdi:METADATA_ID>"+rsProducts.getString("metadata_id")+"</easysdi:METADATA_ID>\n");
					res.append("<easysdi:ID>"+rsProducts.getString("product_id")+"</easysdi:ID>\n");
					res.append("<easysdi:NAME>"+encodeSpecialChars(rsProducts.getString("name"))+"</easysdi:NAME>\n");

					res.append("<easysdi:PROPERTIES>\n");

					Statement stmtProp = conn.createStatement();

					query = "SELECT pv.code as value, prop.code as code FROM "+getJP()+"sdi_order_property oprop, "+getJP()+"sdi_propertyvalue pv, "+getJP()+"sdi_order_product op,  "+getJP()+"sdi_property prop  "+
					"where  op.order_id = "+order_id+" and op.id = oprop.orderproduct_id and prop.id = pv.property_id and oprop.propertyvalue_id = pv.id and op.product_id = " + rsProducts.getString("product_id");
					
					ResultSet rsProp = stmtProp.executeQuery(query);

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

			//Output response in a file
			try
			{
				Random randomGenerator = new Random();
				int randomInt = randomGenerator.nextInt(100000);
				File f = new File(System.getProperty("java.io.tmpdir")+"/"+"get_orders"+randomInt);
				System.out.println(System.getProperty("java.io.tmpdir"));
				FileWriter fw = new FileWriter(f,true);
				fw.write(res.toString());
				fw.close();
			}
			catch (Exception ex){
				ex.printStackTrace();
			}

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

				stmt.executeUpdate("update "+getJP()+"sdi_order set status_id =(SELECT id FROM "+getJP()+"sdi_list_orderstatus where code='AWAIT') ,updated = NOW() where id in ("+orderIdClause+")");	    		    

			}

			/*
			 * Verify the progression of all user's pending orders and set them to progress or finish.
			 * This is a workaround to an unsolved bug encountered in MySql that makes an update query fail
			 * with no error upon the order table. Therefore we have to do this trick by polling.
			 */
			/*
			 * Temp log
			 */
			BufferedWriter out = null;

			String BR = System.getProperty("line.separator");
			File f = new File(System.getProperty("java.io.tmpdir")+"/"+"_getOrderSetStatus v2");
			FileWriter fw = new FileWriter(f,true);
			out = new BufferedWriter(fw);

			DateFormat dateFormat = new SimpleDateFormat("dd/MM/yyyy HH:mm:ss");
			Date date = new Date();
			out.write("log datetime: "+dateFormat.format(date)+BR);


			/* We cannot do this because its dependant of the status id value, which can change...
			 * We'll have to select all order with status 'Await' and then test each on in a loop
			 * by counting achieved/await product.

			ResultSet rs1;
			//Select all pending orders.
			query = "select o.id as order_id, sum(l.status_id)/count(l.id) as sum_stat "+
			        "from "+getJP()+"sdi_order o, "+getJP()+"sdi_order_product l where "+
			        "o.id=l.order_id and o.status_id =(SELECT id FROM "+getJP()+"sdi_list_orderstatus where code='AWAIT') group by o.id order by sum_stat;";

			//System.out.println(query);

			rs1 = stmt.executeQuery(query);
			while(rs1.next()){
			 */
			/* sum_stat: 1 = no order element is treated, let the order status to 4
			 *           1<sumstat<2 = at least one element has been treated -> set order status to progress (3)
			 *           
			 */
			/*
				double sum_stat = rs1.getDouble("sum_stat");
				int id = rs1.getInt("order_id");

				if(sum_stat == 1.0){
					out.write("order:"+id+" unchanged, AWAIT."+BR);
				}
				else if(sum_stat > 1.0 && sum_stat < 2.0){
					Statement stmt2 = conn.createStatement();
					stmt2.executeUpdate("update "+getJP()+"sdi_order set status_id =(SELECT id FROM "+getJP()+"sdi_list_orderstatus where code='PROGRESS') ,updated = NOW() where id ="+id);	    		    
					out.write("order:"+id+" set to PROGRESS."+BR);
					stmt2.close();
				}
				else if(sum_stat == 2.0){
					Statement stmt2 = conn.createStatement();
					stmt2.executeUpdate("update "+getJP()+"sdi_order set status =(SELECT id FROM "+getJP()+"sdi_list_orderstatus where code='FINISH') ,updated = NOW() where id ="+id);	    		    
					out.write("order:"+id+" set to FINISH."+BR);
					stmt2.close();
				}
			}
			 */

			out.write("***************************************"+BR);
			out.close();
			//rs1.close();
			rs.close();
			stmt.close();
			conn.close();    

			return (baos.toString());

		}
		catch (Exception ex) {
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
				//System.out.println(line);
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
		Connection conn = null;

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
					remark  = inputType.getData().getLiteralData().getValue().replace("'", "\\'");
				}

				if (inputType.getData().getComplexData() != null)
				{
					if(inputType.getData().getComplexData().getContent() != null && inputType.getData().getComplexData().getContent().size() != 0)
						data = inputType.getData().getComplexData().getContent().get(0).toString();		
				}

			}

			//System.out.println("SetOrderResponse for: order id:"+order_id+" product id"+product_id);

			if (data == null) data ="";
			if (filename == null) filename ="";

			if (responseDate!=null && order_id!=null && product_id!=null && filename !=null && data !=null)
			{
				String queryBlob = "";
				Class.forName(jdbcDriver).newInstance();
				conn =  DriverManager.getConnection(connexionString);

				Statement stmt = conn.createStatement();
				PreparedStatement pre;
				String query = "";
				
				//Try to insert the blob first because it is the request that is the most likely to fail,
				//and since we use no transaction...
				if (data != "")
				{	
					query = "SELECT id FROM "+getJP()+"sdi_order_product where order_id = "+order_id +" AND product_id = "+product_id;
					ResultSet rs = stmt.executeQuery(query);
					rs.next();
					int id = rs.getInt("id");

					query = "SELECT COUNT(*) as count FROM "+getJP()+"sdi_orderproduct_file where orderproduct_id = "+id;
					rs = stmt.executeQuery(query);
					rs.next();
					int count = rs.getInt("count");

					//new record for file
					if(count == 0){
						queryBlob = "insert into "+getJP()+"sdi_orderproduct_file (filename, data, orderproduct_id) values (?, ?, "+id+")";
						//System.out.println(queryBlob);
					}
					//update existing record
					else{
						queryBlob = "update "+getJP()+"sdi_orderproduct_file set filename=?, data=? where orderproduct_id = "+id;
						//System.out.println(queryBlob);
					}

					pre = conn.prepareStatement(queryBlob);
					pre.setString(1, filename);	
					ByteArrayInputStream bais = new ByteArrayInputStream(Base64Coder.decode(data));
					pre.setBinaryStream(2,bais,data.length());
					// Mise a jour de la requete
					pre.executeUpdate();
					
					//System.out.println("With data");
					query = "update "+getJP()+"sdi_order_product set price = "+price+",remark = '"+remark+"', status_id = (SELECT id FROM "+getJP()+"sdi_list_productstatus where code='AVAILABLE') where order_id = "+order_id +" AND product_id = "+product_id;
					//System.out.println(query);
					pre = conn.prepareStatement(query);
					pre.executeUpdate();
					
				}
				else{
					//System.out.println("Without data");
					query = "update "+getJP()+"sdi_order_product set name='"+filename+"', price = "+price+",remark = '"+remark+"', status_id = (SELECT id FROM "+getJP()+"sdi_list_productstatus where code='AVAILABLE') where order_id = "+order_id +" AND product_id = "+product_id;
					//System.out.println("query");
					pre = conn.prepareStatement(query);
					// Mise a jour de la requete
					pre.executeUpdate();

				}

				//update order row
				stmt.executeUpdate("update "+getJP()+"sdi_order set responsesent = '1' ,response = str_to_date('"+responseDate+"', '%d.%m.%Y %H:%i:%s')  where id = "+order_id);

				
				//update order status
				/*
				String query = "";
				int count = stmt.executeUpdate("update "+getJoomlaPrefix()+"easysdi_order set status =(SELECT id FROM "+getJoomlaPrefix()+"easysdi_list_orderstatus where code='FINISH')   where order_id = "+order_id +" AND (SELECT COUNT(*) FROM "+getJoomlaPrefix()+"easysdi_order_product WHERE order_id = "+order_id +")=(SELECT COUNT(*) FROM "+getJoomlaPrefix()+"easysdi_order_product WHERE status = (SELECT id FROM "+getJoomlaPrefix()+"easysdi_order_product_status_list where code='AVAILABLE') AND order_id = "+order_id +")");
				if (count == 0){
					query = "update "+getJoomlaPrefix()+"easysdi_order set status = (SELECT id FROM "+getJoomlaPrefix()+"easysdi_list_orderstatus where code='PROGRESS')   where order_id = "+order_id;
					stmt.executeUpdate(query);
				}
				 */

				int totalProd = 0;
				int totalAchievedProducts = 0;

				query = "SELECT COUNT(*) as count FROM "+getJP()+"sdi_order_product WHERE order_id = "+order_id;
				ResultSet rs = stmt.executeQuery(query);
				rs.next();
				totalProd = rs.getInt("count");

				query = "SELECT COUNT(*) as count FROM "+getJP()+"sdi_order_product WHERE status_id = (SELECT id FROM "+getJP()+"sdi_list_productstatus where code='AVAILABLE') AND order_id = "+order_id;
				rs = stmt.executeQuery(query);
				rs.next();
				totalAchievedProducts = rs.getInt("count");

				//Set order status to Finish
				if(totalAchievedProducts == totalProd){
					query = "update "+getJP()+"sdi_order set status_id =(SELECT id FROM "+getJP()+"sdi_list_orderstatus where code='FINISH') where id = "+order_id;
					stmt.executeUpdate(query);
				}
				//Set order status to Progress
				else{
					query = "update "+getJP()+"sdi_order set status_id = (SELECT id FROM "+getJP()+"sdi_list_orderstatus where code='PROGRESS') where id = "+order_id;
					stmt.executeUpdate(query);
				}

				/*
				int count = pre.executeUpdate("update "+getJoomlaPrefix()+"easysdi_order set status =(SELECT id FROM "+getJoomlaPrefix()+"easysdi_list_orderstatus where code='FINISH')   where order_id = "+order_id +" AND (SELECT COUNT(*) FROM "+getJoomlaPrefix()+"easysdi_order_product WHERE order_id = "+order_id +")=(SELECT COUNT(*) FROM "+getJoomlaPrefix()+"easysdi_order_product WHERE status = (SELECT id FROM "+getJoomlaPrefix()+"easysdi_order_product_status_list where code='AVAILABLE') AND order_id = "+order_id +")");
				if (count == 0){
					query = "update "+getJoomlaPrefix()+"easysdi_order set status = (SELECT id FROM "+getJoomlaPrefix()+"easysdi_list_orderstatus where code='PROGRESS')   where order_id = "+order_id;
					pre.executeUpdate(query);
				}
				 */

				pre.close();
				stmt.close();

				/*
				 * Temp log
				 */
				BufferedWriter out = null;
				try{
					String BR = System.getProperty("line.separator");
					File f = new File(System.getProperty("java.io.tmpdir")+"/"+"executeSetOrderResponse"+order_id+"v5");
					FileWriter fw = new FileWriter(f,true);
					out = new BufferedWriter(fw);
					out.write("order_id:"+order_id+BR);
					out.write("responseDate:"+responseDate+BR);
					out.write("product_id:"+product_id+BR);
					out.write("data:"+data+BR);
					out.write("filename:"+filename+BR);
					out.write("rebate:"+rebate+BR);
					out.write("price:"+price+BR);
					out.write("remark:"+remark+BR);
					out.write("queryBlob:"+queryBlob+BR);
					out.write(BR);
					out.write(BR);
					out.write("totalProd:"+totalProd);
					out.write(BR);
					out.write(query);
					out.write(BR);
					out.write("*******************************************");
					out.close();						
				}
				catch (IOException ex){
					ex.printStackTrace();
				}
				finally{
					if(out!=null)
						try{out.close();}catch(IOException e){}
				}

				// Mail de notification
				Statement stmtTotal = conn.createStatement();
				ResultSet rsTotal = stmtTotal.executeQuery("SELECT COUNT(*) as total FROM "+getJP()+"sdi_order_product p,"+getJP()+"sdi_list_productstatus sl WHERE p.status_id=sl.id and p.order_id="+order_id+" AND sl.code = 'AWAIT'");

				int total = 0;
				while(rsTotal.next()){
					total = rsTotal.getInt("total");
				}

				rsTotal.close();
				stmtTotal.close();

				Statement stmtTotalProduct = conn.createStatement();
				ResultSet rsTotalProduct = stmtTotalProduct.executeQuery("SELECT COUNT(*) as total FROM "+getJP()+"sdi_order_product p,"+getJP()+"sdi_list_productstatus sl WHERE p.status_id=sl.id and p.order_id="+order_id);

				int totalProduct = 0;
				while(rsTotalProduct.next()){
					totalProduct = rsTotalProduct.getInt("total");
				}

				rsTotalProduct.close();
				stmtTotalProduct.close();

				Statement stmtRow = conn.createStatement();
				ResultSet rsRow = stmtRow.executeQuery("SELECT o.user_id as user_id FROM "+getJP()+"sdi_order o,"+getJP()+"users u WHERE o.user_id = u.id AND o.id="+order_id);
				int user_id = 0;
				while(rsRow.next()){
					user_id = rsRow.getInt("user_id");
				}	
				rsRow.close();
				stmtRow.close();

				Statement stmtPartner = conn.createStatement();
				ResultSet rsPartner = stmtPartner.executeQuery("SELECT * FROM "+getJP()+"sdi_account WHERE user_id="+user_id);

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
				File f = new File(System.getProperty("java.io.tmpdir")+"/"+"errorStream1.txt");
				//System.out.println("Writing error file:"+System.getProperty("java.io.tmpdir")+"errorStream.txt");
				FileWriter fw = new FileWriter(f,true);
				fw.write(sw.toString());
				fw.write(e.getMessage());
				fw.write("responseDate:"+responseDate);
				fw.write("order_id:"+order_id);
				fw.write("product_id:"+product_id);
				fw.write("filename:"+filename);
				fw.write("rebate:"+rebate);
				fw.write("price:"+price);
				fw.write("remark:"+remark);
				fw.close();
				pw.close();
			}
			catch (IOException ex){
				ex.printStackTrace();
			}
			return error ("ERROR","An error just occured:"+stack2string(e));
		}
		finally{
			if(conn != null){
				try {
					conn.close();
				} catch (SQLException e) {
					e.printStackTrace();
				}
			}

		}
		return error ("ERROR","An error just occured");


	}

	public static String stack2string(Exception e) {
		try {
			StringWriter sw = new StringWriter();
			PrintWriter pw = new PrintWriter(sw);
			e.printStackTrace(pw);
			return "------\r\n" + sw.toString() + "------\r\n";
		}
		catch(Exception e2) {
			return "bad stack2string";
		}
	}


	public static String encodeSpecialChars(String s){
		s = s.replace("&", "&#38;");
		return s;
	}

	public String getResourceAsString(String resourceName){
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
			ResultSet rsRow = stmtRow.executeQuery("SELECT o.id as order_id, o.user_id as user_id,u.email as email,o.name as data_title "+
					"FROM "+getJP()+"sdi_order o,"+getJP()+"users u "+
					"WHERE o.user_id = u.id AND o.id="+order_id);
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
			ResultSet rsPartner = stmtPartner.executeQuery("SELECT notify_order_ready FROM "+getJP()+"sdi_account WHERE user_id="+user_id);

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
			//System.out.println("Problem reading file.\n" + ex.getMessage());
		} 
		finally 
		{
			try { if (in!=null) in.close(); } catch(IOException ignore) {}
		}

		return body;
	}
}
