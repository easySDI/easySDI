package eu.bauel.publish.persistence;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.logging.Logger;

public class MysqlDbInit {
	static Logger logger = Logger.getLogger("eu.bauel.publish.persistence.Mysql");

	public static void init(Connection connection){
		/* Todo, check the version of publish */ 
		Statement statement;
		Float version;
		String query;
		try {
			//check if it's a new installation or if we already have the main tables.
			//Then, manage the versions 

			//Is it a new install?
			statement = connection.createStatement();
			//SELECT count(*) AS RESULT FROM INFORMATION_scheme.SYSTEM_TABLES WHERE TABLE_SCHEM = 'PUBLIC' AND TABLE_NAME='VERSION'
			query= "select count(*) as result from INFORMATION_schema.TABLES "+
			"where table_schema = '"+DBConnection.getInitParameter("DB_NAME")+"' "+
			"and table_name = 'VERSION'";
			logger.info(query);
			ResultSet rs = statement.executeQuery(query);
			rs.next();
			if(rs.getInt("result") == 0){
				//table version not found -> new install
				version = 0f;
				logger.info( "EasySDIPublish table version not found, processing new install");
			}else{	
				logger.info( "EasySDIPublish table version found.");
				rs = statement.executeQuery("SELECT version FROM version where component='wpspublish'");
				rs.next();
				version = rs.getFloat("version");
				logger.info( "EasySDIPublish version is:"+version);
			}

			//Let's go for the main installation
			if(version == 0){

				//Create the table version if not exists	
				query = "CREATE TABLE version (" +
				"id INT NOT NULL auto_increment , "+
				"component VARCHAR(100), " +
				"version FLOAT, " +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";			
				statement.executeUpdate(query);

				statement.executeUpdate("INSERT INTO version (component, version) VALUES ('wpspublish', 0)");
				System.out.println("Installing component for version:"+version);

				//The geodatabasetype
				query = "CREATE TABLE geodatabase_type (" +
				"id INT NOT NULL auto_increment, "+
				"name VARCHAR(100), " +
				"UNIQUE(name) ," +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";			
				statement.executeUpdate(query);

				String[] geodatabase_type = DBConnection.getInitParameter("geodatabase_type").split(",");
				for(String geodatabasetype : geodatabase_type){
					query = "INSERT INTO geodatabase_type (name) VALUES ('"+geodatabasetype+"')";
					statement.executeUpdate(query);
				}

				//The geodatabase
				query = "CREATE TABLE geodatabase (" +
				"id INT NOT NULL auto_increment, "+
				"name VARCHAR(100), " +
				"url VARCHAR(200), " +
				"username VARCHAR(100), " +
				"pwd VARCHAR(100), " +
				"scheme VARCHAR(100), " +
				"type INT, "+
				"FOREIGN KEY (type) REFERENCES geodatabase_type(id), " +
				"UNIQUE(name) ," +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";			
				statement.executeUpdate(query);

				String[] geodatabaseparams = DBConnection.getInitParameter("geodatabase").split(",");
				rs = statement.executeQuery("SELECT id FROM geodatabase_type where name='"+geodatabaseparams[5]+"'");
				rs.next();
				int id = rs.getInt("id");
				query = "INSERT INTO geodatabase (name, url, username, pwd, scheme, type) VALUES "+
				"('"+geodatabaseparams[0]+"', '"+geodatabaseparams[1]+"', '"+geodatabaseparams[2]+"', '"+geodatabaseparams[3]+"','"+geodatabaseparams[4]+"', '"+id+"')";
				statement.executeUpdate(query);


				//The diffuser type
				query = "CREATE TABLE diffuser_type (" +
				"id INT NOT NULL auto_increment, "+
				"name VARCHAR(100) ," +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";			
				statement.executeUpdate(query);

				String[] diffuser_type = DBConnection.getInitParameter("diffuser_type").split(",");
				for(String diffusertype : diffuser_type){
					query = "INSERT INTO diffuser_type (name) VALUES ('"+diffusertype+"')";
					statement.executeUpdate(query);
				}


				//The diffuser
				query = "CREATE TABLE diffuser (" +
				"id INT NOT NULL auto_increment, "+
				"databaseId INT, "+
				"name VARCHAR(100), " +
				"url VARCHAR(200), " +
				"username VARCHAR(100), " +
				"pwd VARCHAR(100), " +
				"type INT, "+
				"FOREIGN KEY (databaseId) REFERENCES geodatabase(id), " +
				"FOREIGN KEY (type) REFERENCES diffuser_type(id), " +
				"UNIQUE(name) ," +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";		
				statement.executeUpdate(query);

				String[] diffuserparams = DBConnection.getInitParameter("diffuser").split(",");
				rs = statement.executeQuery("SELECT id FROM diffuser_type where name='"+diffuserparams[5]+"'");
				rs.next();
				int difftype = rs.getInt("id");
				rs = statement.executeQuery("SELECT id FROM geodatabase where name='"+diffuserparams[0]+"'");
				rs.next();
				int dbid = rs.getInt("id");
				query = "INSERT INTO diffuser (databaseId, name, url, username, pwd, type) VALUES "+
				"('"+dbid+"', '"+diffuserparams[1]+"', '"+diffuserparams[2]+"', '"+diffuserparams[3]+"','"+diffuserparams[4]+"', '"+difftype+"')";
				statement.executeUpdate(query);

				//The featureSource
				query = "CREATE TABLE featuresource (" +
				"id INT NOT NULL auto_increment, "+
				"diffuserId INT, "+
				"featureGUID VARCHAR(100), " +
				"tableName VARCHAR(200), " +
				"scriptName VARCHAR(100), " +
				"sourceDataType VARCHAR(100), " +
				"crsCode VARCHAR(50), " +
				"fieldsName VARCHAR(600), " +
				"creation_date TIMESTAMP, " +
				"update_date TIMESTAMP, " +
				"status VARCHAR(20), " +
				"excMessage VARCHAR(2000), " +
				"excDetail VARCHAR(500), " +
				"FOREIGN KEY (diffuserId) REFERENCES diffuser(id), " +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";	
				statement.executeUpdate(query);


				//Table LayerStatus 
				query = "CREATE TABLE layerstatus (" +
				"id INT NOT NULL auto_increment, "+
				"status VARCHAR(100), " +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";		
				statement.executeUpdate(query);

				String[] layerstatus = DBConnection.getInitParameter("layerstatus").split(",");
				for(String layerstatusVal : layerstatus){
					query = "INSERT INTO layerstatus (status) VALUES ('"+layerstatusVal+"')";
					statement.executeUpdate(query);
				}


				//the Layer
				query = "CREATE TABLE layer (" +
				"id INT NOT NULL auto_increment, "+
				"layerGUID VARCHAR(100), " +
				"layerKeywordList VARCHAR(100), " +				
				"layerTitle VARCHAR(100), " +
				"layerName VARCHAR(100), " +
				"layerDescription VARCHAR(100), " +
				"status INT, " +
				"featuresourceId INT, " +
				"creation_date TIMESTAMP, " +
				"update_date TIMESTAMP, " +
				"FOREIGN KEY (featuresourceId) REFERENCES featuresource(id), " +
				"FOREIGN KEY (status) REFERENCES layerstatus(id), " +
				"UNIQUE(layerName), " +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";	
				statement.executeUpdate(query);

				//the Transformator_type
				query = "CREATE TABLE transformator_type (" +
				"id INT NOT NULL auto_increment, "+
				"name VARCHAR(100), " +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";	
				statement.executeUpdate(query);

				String[] transformator_list = DBConnection.getInitParameter("transformator_type").split(",");
				for(String type : transformator_list){
					query = "INSERT INTO transformator_type (name) VALUES ('"+type+"')";
					statement.executeUpdate(query);
				}
				statement.executeUpdate(query);

				//the Transformator
				query = "CREATE TABLE transformator (" +
				"id INT NOT NULL auto_increment, "+
				"name VARCHAR(100), " +
				"type INT, "+
				"FOREIGN KEY (type) REFERENCES transformator_type(id), " +
				"UNIQUE(name), " +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";
				statement.executeUpdate(query);

				//<param-value>MySDI,sdi</param-value> name type
				String[] transparams = DBConnection.getInitParameter("transformator").split(",");
				rs = statement.executeQuery("SELECT id FROM transformator_type where name='"+transparams[1]+"'");
				rs.next();
				int transtype = rs.getInt("id");
				query = "INSERT INTO transformator (name, type) VALUES "+
				"('"+transparams[0]+"', '"+transtype+"')";
				statement.executeUpdate(query);

				//the Script
				query = "CREATE TABLE script (" +
				"id INT NOT NULL auto_increment, "+
				"transformator INT, "+
				"name VARCHAR(300), " +
				"location VARCHAR(300), " +
				"command VARCHAR(500), " +
				"arguments VARCHAR(500), " +
				"conditions VARCHAR(500), " +
				"requiredFiles VARCHAR(500), " +
				"FOREIGN KEY (transformator) REFERENCES transformator(id), " +
				"UNIQUE(name), " +
				"PRIMARY KEY (id)) " +
				"ENGINE = InnoDB";		
				statement.executeUpdate(query);

				//<param-value>MySDI,shape,c:\\bla\\shapetopostgis,commands,args,cond,requfiles</param-value>
				String[] scriptparams = DBConnection.getInitParameter("script").split(",");
				rs = statement.executeQuery("SELECT id FROM transformator where name='"+scriptparams[0]+"'");
				rs.next();
				int transftype = rs.getInt("id");
				query = "INSERT INTO script (transformator, name, location, command, arguments, conditions, requiredFiles) VALUES "+
				"('"+transftype+"', '"+scriptparams[1]+"', '"+scriptparams[2]+"', '"+scriptparams[3]+"', '"+scriptparams[4]+"', '"+scriptparams[5]+"', '"+scriptparams[6]+"')";
				statement.executeUpdate(query);


				//Update the version
				statement.executeUpdate("UPDATE version set version = 0.1 where component = 'wpspublish'");
			}

			//continue here for the next version....
			if(version == 0.1){

				//.... what you want for 0.2

				//statement.executeUpdate("UPDATE version set version = 0.2 where component = 'wpspublish'");
			}
			//and so on... 

			
			//check database consistency
			//If a feature is in state creating, change to "Unavailable" and place a little comment
			rs = statement.executeQuery("SELECT id FROM featuresource where status='CREATING'");
			ArrayList<String> ids = new ArrayList<String>();
			while(rs.next()){
				ids.add(rs.getString("id"));
			}
			for(String fsId : ids)
				statement.executeUpdate("UPDATE featuresource set status = 'UNAVAILABLE', excDetail = 'ERROR_SERVLET_REINIT_WHILE_TRANSFORMING' where id = "+fsId);
			
			
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
}
