package ch.depth.services.config;

import java.io.IOException;
import java.net.MalformedURLException;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import javax.servlet.ServletConfig;
import javax.servlet.http.HttpServletRequest;

import com.eaio.uuid.UUID;

import ch.depth.services.wps.WPSServlet;
import eu.bauel.publish.Utils;
import eu.bauel.publish.exception.DataInputException;
import eu.bauel.publish.exception.DataSourceWrongFormatException;
import eu.bauel.publish.exception.FeatureSourceNotFoundException;
import eu.bauel.publish.exception.NoSuchTransformerForFeatureSourceGuid;
import eu.bauel.publish.exception.PublishConfigurationException;
import eu.bauel.publish.exception.TransformationException;
import eu.bauel.publish.persistence.Diffuser;
import eu.bauel.publish.persistence.Diffuser_type;
import eu.bauel.publish.persistence.Featuresource;
import eu.bauel.publish.persistence.Geodatabase;
import eu.bauel.publish.persistence.Geodatabase_type;
import eu.bauel.publish.persistence.DBConnection;
import eu.bauel.publish.transformation.Dataset;
import eu.bauel.publish.transformation.InputDatasetInfo;

public class Config {
	Logger logger = Logger.getLogger("ch.depth.services.config");
	static Connection c = null;

	public Config(Connection c){
		Config.c = c;
	}

	public String getPublicationServerlist(){
		StringBuilder res = new StringBuilder();
		ArrayList<Integer> alServ = new ArrayList<Integer>();
		Statement statement;
		String query;
		ResultSet rs;
		try {
			statement = c.createStatement();
			query= "select id from diffuser";
			rs = statement.executeQuery(query);
			while(rs.next()){
				Integer id = rs.getInt("ID");
				alServ.add(id);
			}
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		res.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
		res.append("<servers>");
		//loop each Publication server
		for (int id : alServ){
			Diffuser diff = new Diffuser(id, c);
			Geodatabase db = new Geodatabase(diff.getDatabaseid(), c);

			res.append("<server id=\""+diff.getId()+"\">");
			//diffuser
			res.append("<id>"+diff.getId()+"</id>");
			res.append("<name>"+diff.getName()+"</name>");
			res.append("<type>"+diff.getType()+"</type>");
			res.append("<url>"+diff.getUrl()+"</url>");
			res.append("<username>"+diff.getUsername()+"</username>");
			res.append("<password>"+diff.getPassword()+"</password>");
			//geodb
			res.append("<dbname>"+db.getName()+"</dbname>");
			res.append("<dbtype>"+Geodatabase_type.getId(db.getType())+"</dbtype>");
			res.append("<dburl>"+db.getUrl()+"</dburl>");
			res.append("<dbusername>"+db.getUsername()+"</dbusername>");
			res.append("<dbpassword>"+db.getPassword()+"</dbpassword>");	
			res.append("</server>");
		}
		//feed server types
		res.append("<serverTypes>");
		try {
			statement = c.createStatement();
			query= "select * from diffuser_type";
			rs = statement.executeQuery(query);
			while(rs.next()){
				res.append("<serverType>");
				res.append("<id>"+rs.getInt("ID")+"</id>");
				res.append("<name>"+rs.getString("NAME")+"</name>");
				res.append("</serverType>");
			}
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		res.append("</serverTypes>");

		//feed geodatabase types
		res.append("<dbTypes>");
		try {
			statement = c.createStatement();
			query= "select * from geodatabase_type";
			rs = statement.executeQuery(query);
			while(rs.next()){
				res.append("<dbType>");
				res.append("<id>"+rs.getInt("ID")+"</id>");
				res.append("<name>"+rs.getString("NAME")+"</name>");
				res.append("</dbType>");
			}
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		res.append("</dbTypes>");

		res.append("</servers>");
		return res.toString();
	}

	public String getAvailableDatasetFromSource(HttpServletRequest req){
		String[] urls = req.getParameter("files").split(",");
		List<String> URLs = new ArrayList<String>();
		String tempFileDir = "";
		String location = new UUID().toString();
		String mainFileName = "";

		try{
			for(String url : urls){
				URLs.add(url);
				logger.info("input files: "+url);
			}

			//Retrieve locally the supplied files for transformation. They go into the OS Java temp
			//folder
			tempFileDir = Utils.writeHttpGetToFileSystem(location, URLs);

			//look for the files in the temp dir.
			String fileName = null;
			String extension = null;
			for(int i=0; i<URLs.size(); i++){
				String url = URLs.get(i);
				String[] tempStr = url.split("/");
				fileName = url.split("/")[tempStr.length - 1];
				String[] arrfileName = fileName.split("\\.");
				if(arrfileName.length < 2){
					System.out.println(url);
					throw new DataSourceWrongFormatException(arrfileName[0]);
				}
				//fetch the main files of the supplied collection, this file
				//will be passed to the script
				String candidateExt = arrfileName[1].toLowerCase();
				if(
						candidateExt.equals("shp")
						||candidateExt.equals("map")
						||candidateExt.equals("gml")
						||candidateExt.equals("mif")
						||candidateExt.equals("tab")			
				){
					mainFileName =  fileName;
					extension = arrfileName[1].toLowerCase();
					break;
				}else{
					//catch the first one if no candidate found
					if(i == (URLs.size() - 1)){
						mainFileName =  fileName;
						extension = arrfileName[1].toLowerCase();
						break;
					}
				}	
			}

			//Look for the datasets contained into the supplied file
			InputDatasetInfo idi = new InputDatasetInfo();
			idi.getInfoForDataset(tempFileDir+mainFileName);


			//Build the response
			StringBuilder res = new StringBuilder();
			res.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
			res.append("<datasets>");
			for(Dataset ds : idi.getDatasets()){
				res.append("<dataset>"+ds.getName()+"</dataset>");
			}
			res.append("</datasets>");
			return res.toString();
		} catch (DataInputException e) {
			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><exception code=\"EASYSDI_PUBLISH_WPS_ERROR_DATASOURCE_WRONG_FORMAT\">"+e.getMessage()+"</exception>";
		} catch (MalformedURLException e) {
			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><exception code=\"EASYSDI_PUBLISH_WPS_ERROR_COMMUNICATION_EXCEPTION\">"+e.getMessage()+"</exception>";
		} catch (IOException e) {
			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><exception code=\"EASYSDI_PUBLISH_WPS_ERROR_COMMUNICATION_EXCEPTION\">"+e.getMessage()+"</exception>";
		} catch (TransformationException e) {
			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><exception code=\"EASYSDI_PUBLISH_WPS_ERROR_DATASOURCE_WRONG_FORMAT\">"+e.getMessage()+"</exception>";
		} catch (PublishConfigurationException e) {
			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><exception code=\"EASYSDI_PUBLISH_WPS_ERROR_CONFIGURATION_EXCEPTION\">"+e.getMessage()+"</exception>";
		}
	}

	public String addPublicationServer(HttpServletRequest req) throws DataInputException{		
		String id = req.getParameter("id");
		String name = req.getParameter("name");
		String type = req.getParameter("type");
		String url = req.getParameter("url");
		String username = req.getParameter("username");
		String password = req.getParameter("password");
		String dbname = req.getParameter("dbname");
		String dbtype = req.getParameter("dbtype");
		String dburl = req.getParameter("dburl");
		String dbusername = req.getParameter("dbusername");
		String dbpassword = req.getParameter("dbpassword");

		Geodatabase geodb;
		Diffuser d;

		//Create a new publication server
		if(id == null || id.equals("none") || id.equals("")){
			//Database
			geodb = new Geodatabase(c);
			geodb.setName(dbname+"@"+name);
			geodb.setType(Integer.parseInt(dbtype));
			geodb.setUrl(dburl);
			geodb.setUsername(dbusername);
			geodb.setPassword(dbpassword);			
			if(geodb.store() == false){
				System.out.println("Error occured, cause:"+geodb.errorCause +" message:"+ geodb.errorMessage);
				throw new DataInputException("Insert failed, error when creating database: "+geodb.errorCause +" message:"+ geodb.errorMessage);
			}

			//load the db to get the created id.
			geodb = Geodatabase.getGeodatabaseFromName(dbname+"@"+name);

			System.out.println("geodb name:"+geodb.getName()+" id:"+geodb.getId());

			//Diffuser
			d = new Diffuser(c);
			d.setName(name);
			d.setDatabaseid(geodb.getId());
			d.setUrl(url);
			d.setUsername(username);
			d.setPassword(password);
			d.setType(Integer.parseInt(type));

			if(d.store() == false){
				System.out.println("Insert failed, error occured, cause:"+d.errorCause +" message:"+ d.errorMessage);
				throw new DataInputException("Error when creating diffuser: "+d.errorCause +" message:"+ d.errorMessage);
			}
			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><success type=\"insert\"/>";
		}
		//Modify existing server
		else{
			d = new Diffuser(Integer.parseInt(id),c);
			if(d.getId() == null)
				throw new DataInputException("Update failed, no diffuser known for id:"+id);
			geodb = d.getGeodatabase();
			d.setName(name);
			d.setUrl(url);
			d.setUsername(username);
			d.setPassword(password);
			d.setType(Integer.parseInt(type));
			if(d.store() == false){
				System.out.println("Update failed, error occured, cause:"+d.errorCause +" message:"+ d.errorMessage);
				throw new DataInputException("Error when creating diffuser: "+d.errorCause +" message:"+ d.errorMessage);
			}

			geodb.setName(dbname+"@"+name);
			geodb.setType(Integer.parseInt(dbtype));
			geodb.setUrl(dburl);
			geodb.setUsername(dbusername);
			geodb.setPassword(dbpassword);			
			if(geodb.store() == false){
				System.out.println("Update failed, cause:"+geodb.errorCause +" message:"+ geodb.errorMessage);
				throw new DataInputException("Error when creating database: "+geodb.errorCause +" message:"+ geodb.errorMessage);
			}
			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><success type=\"update\"/>";
		}
	}

	public String deletePublicationServer(String id) throws DataInputException{		

		Geodatabase geodb;
		Diffuser d;

		//Create a new publication server
		if(id == null)
			throw new DataInputException("Delete failed, no diffuser id supplied");

		//Diffuser
		d = new Diffuser(Integer.parseInt(id),c);
		if(d.getId() == null)
			throw new DataInputException("Delete failed, no diffuser known for id:"+id);

		geodb = d.getGeodatabase();

		if(d.delete() == false){
			System.out.println("Error occured, cause:"+d.errorCause +" message:"+ d.errorMessage);
			throw new DataInputException("Error when deleting diffuser: "+d.errorCause +" message:"+ d.errorMessage);
		}

		if(geodb.delete() == false){
			System.out.println("Error occured, cause:"+d.errorCause +" message:"+ d.errorMessage);
			throw new DataInputException("Error when deleting database: "+geodb.errorCause +" message:"+ geodb.errorMessage);
		}

		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><success type=\"delete\"/>";
	}



	public String getFeatureSourcelist(String list){
		String [] temp = null;
		StringBuilder sqlWhere = new StringBuilder();
		temp = list.split(",");
		for(int i=0; i<temp.length; i++){
			if(i < (temp.length - 1))
				sqlWhere.append("featureguid='"+temp[i]+"' OR ");
			else
				sqlWhere.append("featureguid='"+temp[i]+"'");
		}

		StringBuilder res = new StringBuilder();
		Statement statement;
		String query;
		ResultSet rs;

		//select the feature sources
		try {
			statement = c.createStatement();
			query= "select * from featuresource where "+sqlWhere.toString();
			System.out.println(query);
			rs = statement.executeQuery(query);
			res.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
			res.append("<featuresources>");

			while(rs.next()){
				res.append("<featuresource id=\""+rs.getInt("id")+"\" guid=\""+rs.getString("featureguid")+"\">");
				res.append("<id>"+rs.getInt("id")+"</id>");
				res.append("<diffuserid>"+rs.getInt("diffuserid")+"</diffuserid>");
				res.append("<featureguid>"+rs.getString("featureguid")+"</featureguid>");
				res.append("<tablename>"+rs.getString("tablename")+"</tablename>");
				res.append("<scriptname>"+rs.getString("scriptname")+"</scriptname>");
				res.append("<sourcedatatype>"+rs.getString("sourcedatatype")+"</sourcedatatype>");
				res.append("<fieldsname>"+rs.getString("fieldsname")+"</fieldsname>");
				res.append("<crscode>"+rs.getString("crscode")+"</crscode>");
				res.append("<creation_date>"+Utils.dateFormat.format(rs.getDate("creation_date"))+"</creation_date>");
				if(rs.getDate("update_date") != null)
					res.append("<update_date>"+Utils.dateFormat.format(rs.getDate("update_date"))+"</update_date>");
				else
					res.append("<update_date>null</update_date>");
				res.append("<status>"+rs.getString("status")+"</status>");
				res.append("<excmessage><![CDATA["+rs.getString("excmessage")+"]]></excmessage>");
				res.append("<excdetail>"+rs.getString("excdetail")+"</excdetail>");
				res.append("</featuresource>");
			}
			res.append("</featuresources>");
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}


		return res.toString();
	}

	public String getTransformationProgress(String guid){
		Float prog = 0f;
		StringBuilder res = new StringBuilder();
		int fsId = 0;
		Featuresource fs = null;
		boolean isFsFound = true;
		boolean isTransFound = true;

		try {
			if(WPSServlet.fsm == null){
				isTransFound = false;
			}else{
				prog = WPSServlet.fsm.getProgressForFeatureSource(guid);
			}
		} catch (NoSuchTransformerForFeatureSourceGuid e) {
			//Assume it is finished.
			isTransFound = false;
		}

		try {
			fs = Featuresource.getFeatureSourceFromGUID(guid);
		} catch (FeatureSourceNotFoundException e) {
			// Could be the fs is not yet created
			isFsFound =false;
		} catch (NullPointerException e){
			isFsFound =false;;
		}

		res.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
		res.append("<featuresource>");

		if(isTransFound){
			if(isFsFound){
				System.out.println("--1--");

				//fs is transforming
				res.append("<status>");
				res.append(fs.getStatus().getText());
				res.append("</status>");
				res.append("<progression fsid=\""+guid+"\">");
				res.append(prog.intValue());
				res.append("</progression>");
			}else{
				System.out.println("--2--");

				//fs is transforming but not yet created
				res.append("<status>UNAVAILABLE</status>");
				res.append("<progression fsid=\""+guid+"\">");
				res.append(prog.intValue());
				res.append("</progression>");
			}
		}else{
			if(isFsFound){
				//fs exists and is finished transforming
				System.out.println("--3--");

				res.append("<status>");
				res.append(fs.getStatus().getText());
				res.append("</status>");
				res.append("<progression fsid=\""+guid+"\">");
				res.append("0");
				res.append("</progression>");
			}else{
				System.out.println("--4--");

				//fs doesn't exists and is finished transforming
				res.append("<status>UNAVAILABLE</status>");
				res.append("<progression fsid=\""+guid+"\">");
				res.append("0");
				res.append("</progression>");
			}
		}

		/*
		try {
			prog = WPSServlet.fsm.getProgressForFeatureSource(guid);
			res.append("<progression fsid=\""+guid+"\">");
			res.append(prog.intValue());
			res.append("</progression>");

		} catch (NoSuchTransformerForFeatureSourceGuid e) {
			//Assume it is finished.
			res.append("<progression fsid=\""+guid+"\">");
			res.append("100");
			res.append("</progression>");
		}

		try {
			fs = Featuresource.getFeatureSourceFromGUID(guid);
			res.append("<status>");
			res.append(fs.getStatus().getText());
			res.append("</status>");
		} catch (FeatureSourceNotFoundException e) {
			// Could be the fs is not yet created
			res.append("<status>UNAVAILABLE</status>");
		}
		 */
		res.append("</featuresource>");

		return res.toString();
	}

}
