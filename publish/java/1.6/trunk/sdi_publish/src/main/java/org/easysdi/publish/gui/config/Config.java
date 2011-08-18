package org.easysdi.publish.gui.config;

import java.io.IOException;
import java.net.MalformedURLException;
import java.util.ArrayList;
import java.util.Enumeration;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import javax.annotation.security.RolesAllowed;

import javax.servlet.ServletConfig;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpSession;

import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.DataSourceWrongFormatException;
import org.easysdi.publish.exception.FeatureSourceNotFoundException;
import org.easysdi.publish.exception.NoSuchTransformerForFeatureSourceGuid;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.TransformationException;
import org.easysdi.publish.transformation.FeatureSourceManager;
import org.easysdi.publish.util.Utils;

import com.eaio.uuid.UUID;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.biz.database.GeodatabaseType;
import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.diffuser.DiffuserType;
import org.easysdi.publish.biz.layer.FeatureSource;
import org.easysdi.publish.dat.transformation.ogr.Dataset;
import org.easysdi.publish.dat.transformation.ogr.InputDatasetInfo;
import org.springframework.dao.DataAccessException;
import org.springframework.http.HttpHeaders;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.userdetails.UserDetailsService;
import javax.servlet.http.HttpSession;


public class Config {
	Logger logger = Logger.getLogger("org.easysdi.services.config");
	
	//@RolesAllowed({"proxy_user"})
	public String getPublicationServerlist(){
		StringBuilder res = new StringBuilder();
		ArrayList<Integer> alServ = new ArrayList<Integer>();
		List<Diffuser> lDif = Diffuser.getAllDiffusers();

		res.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
		res.append("<servers>");
		//loop each Publication server
		for (Diffuser diff : lDif){
			Geodatabase db = diff.getGeodatabase();

			res.append("<server id=\""+diff.getDiffuserId()+"\">");
			//diffuser
			res.append("<id>"+diff.getDiffuserId()+"</id>");
			res.append("<name>"+diff.getName()+"</name>");
			res.append("<type>"+diff.getType()+"</type>");
			res.append("<url>"+diff.getUrl()+"</url>");
			res.append("<username>"+diff.getUser()+"</username>");
			res.append("<password>"+diff.getPwd()+"</password>");
			//geodb
			res.append("<dbname>"+db.getName()+"</dbname>");
			res.append("<dbscheme>"+db.getScheme()+"</dbscheme>");
			res.append("<dbtemplate>"+db.getTemplate()+"</dbtemplate>");
			res.append("<dbtype>"+db.getGeodatabaseTypeId()+"</dbtype>");
			res.append("<dburl>"+db.getUrl()+"</dburl>");
			res.append("<dbusername>"+db.getUser()+"</dbusername>");
			res.append("<dbpassword>"+db.getPwd()+"</dbpassword>");	
			res.append("</server>");
		}

		//
		//feed server types
		//
		List<DiffuserType> lDiffTypes = DiffuserType.getAllDiffuserTypes();

		res.append("<serverTypes>");

		for (DiffuserType diffType : lDiffTypes){
			res.append("<serverType>");
			res.append("<id>"+diffType.getTypeId()+"</id>");
			res.append("<name>"+diffType.getName()+"</name>");
			res.append("</serverType>");
		}

		res.append("</serverTypes>");

		//
		//feed geodatabase types
		//
		List<GeodatabaseType> lGeodbTypes = GeodatabaseType.getAllGeodatabaseTypes();
		res.append("<dbTypes>");

		for (GeodatabaseType geodbType : lGeodbTypes){
			res.append("<dbType>");
			res.append("<id>"+geodbType.getTypeId()+"</id>");
			res.append("<name>"+geodbType.getName()+"</name>");
			res.append("</dbType>");
		}

		res.append("</dbTypes>");

		res.append("</servers>");
		return res.toString();
	}

	public String getAvailableDatasetFromSource(HttpServletRequest req){
		String[] urls = req.getParameter("files").split(",");
		List<String> URLs = new ArrayList<String>();
		List<String> outFiles = new ArrayList<String>();
		String location = new UUID().toString();
		String mainFileName = "";

		try{
			for(String url : urls){
				URLs.add(url);
				logger.info("input files: "+url);
			}

			//Retrieve locally the supplied files for transformation. They go into the OS Java temp
			//folder
			outFiles = Utils.writeHttpGetToFileSystem(location, URLs, null);

			//look for the files in the temp dir.
			String fileName = null;
			String extension = null;
			for(int i=0; i<outFiles.size(); i++){
				String url = outFiles.get(i);
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
						||candidateExt.equals("gml")
						||candidateExt.equals("mif")
						||candidateExt.equals("tab")			
				){
					mainFileName =  fileName;
					extension = arrfileName[1].toLowerCase();
					break;
				}else{
					//catch the first one if no candidate found
					if(i == (outFiles.size() - 1)){
						mainFileName =  fileName;
						extension = arrfileName[1].toLowerCase();
						break;
					}
				}	
			}

			//Look for the datasets contained into the supplied file
			InputDatasetInfo idi = new InputDatasetInfo();
			idi.getInfoForDataset(System.getProperty("java.io.tmpdir")+"/"+location+"/"+mainFileName);


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
			return Utils.exceptiontoXML("EASYSDI_PUBLISH_WPS_ERROR_DATASOURCE_WRONG_FORMAT", e.getMessage());
		} catch (MalformedURLException e) {
			return Utils.exceptiontoXML("EASYSDI_PUBLISH_WPS_ERROR_COMMUNICATION_EXCEPTION", e.getMessage());
		} catch (IOException e) {
			return Utils.exceptiontoXML("EASYSDI_PUBLISH_WPS_ERROR_COMMUNICATION_EXCEPTION", e.getMessage());
		} catch (TransformationException e) {
			return Utils.exceptiontoXML("EASYSDI_PUBLISH_WPS_ERROR_DATASOURCE_WRONG_FORMAT", e.getMessage());
		} catch (PublishConfigurationException e) {
			return Utils.exceptiontoXML("EASYSDI_PUBLISH_WPS_ERROR_CONFIGURATION_EXCEPTION", e.getMessage());
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
		String dbscheme = req.getParameter("dbscheme");
		String dbtemplate = req.getParameter("dbtemplate");
		String dbusername = req.getParameter("dbusername");
		String dbpassword = req.getParameter("dbpassword");

		Geodatabase geodb;
		Diffuser d;

		//Create a new publication server
		if(id == null || id.equals("none") || id.equals("")){
			//Database
			geodb = new Geodatabase();
			geodb.setName(dbname);
			geodb.setGeodatabaseTypeId(Long.parseLong(dbtype));
			geodb.setUrl(dburl);
			geodb.setScheme(dbscheme);
			geodb.setTemplate(dbtemplate);
			geodb.setUser(dbusername);
			geodb.setPwd(dbpassword);			

			try{
				geodb.persist();
			}catch(DataAccessException e){
				e.printStackTrace();
				System.out.println("Error occured, cause:"+e.getCause() +" message:"+ e.getMessage());
				throw new DataInputException("Insert failed, error when creating database: "+e.getCause() +" message:"+ e.getMessage());
			}

			//load the db to get the created id.
			geodb = Geodatabase.getFromIdString(dbname);

			System.out.println("geodb name:"+geodb.getName()+" id:"+geodb.getGeodatabaseId());

			//Diffuser
			d = new Diffuser();
			d.setName(name);
			d.setGeodatabase(geodb);
			d.setUrl(url);
			d.setUser(username);
			d.setPwd(password);
			d.setType(Integer.parseInt(type));

			try{
				d.persist();
			}catch(DataAccessException e){
				System.out.println("Error occured, cause:"+e.getCause() +" message:"+ e.getMessage());
				throw new DataInputException("Insert failed, error when creating diffuser: "+e.getCause() +" message:"+ e.getMessage());
			}

			return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><success type=\"insert\"/>";
		}
		//Modify existing server
		else{
			d = Diffuser.getFromIdString(id);
			if(d.getDiffuserId() < 1)
				throw new DataInputException("Update failed, no diffuser known for id:"+id);
			geodb = d.getGeodatabase();
			d.setName(name);
			d.setUrl(url);
			d.setUser(username);
			d.setPwd(password);
			d.setType(Integer.parseInt(type));

			try{
				d.persist();
			}catch(DataAccessException e){
				e.printStackTrace();
				System.out.println("Error occured, cause:"+e.getCause() +" message:"+ e.getMessage());
				throw new DataInputException("Insert failed, error when creating diffuser: "+e.getCause() +" message:"+ e.getMessage());
			}

			geodb.setName(dbname+"@"+name);
			geodb.setGeodatabaseTypeId(Long.parseLong(dbtype));
			geodb.setUrl(dburl);
			geodb.setScheme(dbscheme);
			geodb.setTemplate(dbtemplate);
			geodb.setUser(dbusername);
			geodb.setPwd(dbpassword);			
			try{
				geodb.persist();
			}catch(DataAccessException e){
				System.out.println("Error occured, cause:"+e.getCause() +" message:"+ e.getMessage());
				throw new DataInputException("Insert failed, error when creating database: "+e.getCause() +" message:"+ e.getMessage());
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
		d = Diffuser.getFromIdString(id);
		if(d.getDiffuserId() < 1)
			throw new DataInputException("Delete failed, no diffuser known for id:"+id);

		geodb = d.getGeodatabase();

		try{
			d.delete();
		}catch(DataAccessException e){
			System.out.println("Error occured, cause:"+e.getCause() +" message:"+ e.getMessage());
			throw new DataInputException("Delete failed, error when deleting diffuser: "+e.getCause() +" message:"+ e.getMessage());
		}

		try{
			geodb.delete();
		}catch(DataAccessException e){
			System.out.println("Error occured, cause:"+e.getCause() +" message:"+ e.getMessage());
			throw new DataInputException("Delete failed, error when deleting database: "+e.getCause() +" message:"+ e.getMessage());
		}

		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><success type=\"delete\"/>";
	}


	public String getFeatureSourcelist(String list){
		List<String> fsList = new ArrayList<String>();

		String [] temp = null;
		temp = list.split(",");
		for(int i=0; i<temp.length; i++){
			fsList.add(temp[i]);
		}

		StringBuilder res = new StringBuilder();
		res.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
		res.append("<featuresources>");

		for(String guid : fsList){

			FeatureSource f = FeatureSource.getFromGuid(guid);
            
			//A requested fs may not exit, so we give what we have only.
			if (f == null)
				continue;
			
			res.append("<featuresource id=\""+f.getFeatureSourceId()+"\" guid=\""+f.getGuid()+"\">");
			res.append("<id>"+f.getFeatureSourceId()+"</id>");
			res.append("<diffuserid>"+f.getDiffuser().getDiffuserId()+"</diffuserid>");
			res.append("<featureguid>"+f.getGuid()+"</featureguid>");
			res.append("<tablename>"+f.getTableName()+"</tablename>");
			res.append("<scriptname>"+f.getScriptName()+"</scriptname>");
			res.append("<sourcedatatype>"+f.getSourceDataType()+"</sourcedatatype>");
			res.append("<fieldsname>"+f.getFieldsName()+"</fieldsname>");
			res.append("<crscode>"+f.getCrsCode()+"</crscode>");
			res.append("<creation_date>"+Utils.dateFormat.format(f.getCreationDate().getTime())+"</creation_date>");
			if(f.getUpdateDate() != null)
				res.append("<update_date>"+Utils.dateFormat.format(f.getUpdateDate().getTime())+"</update_date>");
			else
				res.append("<update_date>null</update_date>");
			res.append("<status>"+f.getStatus()+"</status>");
			res.append("<excmessage><![CDATA["+f.getExcMessage()+"]]></excmessage>");
			res.append("<exccode>"+f.getExcCode()+"</exccode>");
			res.append("<excstacktrace>"+f.getExcStackTrace()+"</excstacktrace>");
			res.append("</featuresource>");
		}
		res.append("</featuresources>");

		return res.toString();
	}
}
