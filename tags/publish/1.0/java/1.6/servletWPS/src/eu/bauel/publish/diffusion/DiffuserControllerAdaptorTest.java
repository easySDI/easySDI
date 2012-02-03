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
package eu.bauel.publish.diffusion;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.Calendar;
import java.util.Date;
import java.util.UUID;

import eu.bauel.publish.persistence.Diffuser;
import eu.bauel.publish.persistence.Featuresource;
import eu.bauel.publish.persistence.JTable;

import eu.bauel.publish.persistence.Layer;

public class DiffuserControllerAdaptorTest {
	
	public static Connection c = null;

	
	public static void main(String args[]) throws Exception{

			//start the db
			String HSQL_DB_NAME = "WpsPublish12";
			String HSQL_DB_LOCATION = "C:\\Users\\Utilisateur\\temp\\";
			try {
				org.hsqldb.Server.main(new String[]{"-database.0", "file:"+HSQL_DB_LOCATION+HSQL_DB_NAME, "-dbname.0", HSQL_DB_NAME});
			}
			catch (Exception e) {
				System.out.println("ERROR: failed to start the HSQLDB.");
				e.printStackTrace();
				return;
			}
			
			//get the connection
			try {
				Class.forName("org.hsqldb.jdbcDriver").newInstance();
				c = DriverManager.getConnection("jdbc:hsqldb:hsql://localhost/WpsPublish12", "sa", "");
			} 
			catch (SQLException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			catch (Exception e) {
				System.out.println("ERROR: failed to load HSQLDB JDBC driver.");
				e.printStackTrace();
				return;
			}
			
		JTable.setConnection( c );
		
		
		
		
		//Create a new diffuser table if it doesn't exist:
		Statement statement;
		try {

			statement = c.createStatement();
			//SELECT count(*) AS RESULT FROM INFORMATION_SCHEMA.SYSTEM_TABLES WHERE TABLE_SCHEM = 'PUBLIC' AND TABLE_NAME='VERSION'
			String query= "select count(*) as result from INFORMATION_SCHEMA.SYSTEM_TABLES "+
			"where table_schem = 'PUBLIC' "+
			"and table_name = 'DIFFUSER'";

			ResultSet rs = statement.executeQuery(query);
			if(rs.next())
				//Create the table version if not exists
			{
				int count = rs.getInt("result");
				if(count == 0){
					System.out.println("diffuser doesn't exit, creating it..");
					query = "CREATE TABLE diffuser (" +
					"id INT identity PRIMARY KEY, "+
					"databaseId INT, "+
					"name VARCHAR(100), " +
					"url VARCHAR(200), " +
					"username VARCHAR(100), " +
					"password VARCHAR(100), " +
					"type INT"+
					")";		
					statement.executeUpdate(query);

					//feed some data:
					query = "INSERT INTO diffuser (databaseId, name, url, username, password, type) VALUES "+
					"('1', 'localhost', 'http://localhost:8080/geoserver', 'admin', 'geoserver', '1')";
					statement.executeUpdate(query);
				}else{
					System.out.println("diffuser already exit.");
				}		
			}
			//FeatureSource
			query= "select count(*) as result from INFORMATION_SCHEMA.SYSTEM_TABLES "+
			"where table_schem = 'PUBLIC' "+
			"and table_name = 'FEATURESOURCE'";

			rs = statement.executeQuery(query);
			if(rs.next())
				//Create the table version if not exists
			{
				int count = rs.getInt("result");
				if(count == 0){
					System.out.println("featuresource doesn't exit, creating it..");
					query = "CREATE TABLE featuresource (" +
					"id INT identity PRIMARY KEY, "+
					"diffuserId INT, "+
					"featureGUID VARCHAR(100), " +
					"tableName VARCHAR(200), " +
					"creation_date TIMESTAMP, " +
					"update_date TIMESTAMP" +
					")";			
					statement.executeUpdate(query);

				}else{
					System.out.println("featuresource already exit.");
				}		
			}
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			System.out.println(e.getMessage());
			e.printStackTrace();
		}
					
		
		
		IDiffuserAdapter mock = new GeoserverDiffuserControllerAdaptor();
		
		Featuresource homeMadeFs = Featuresource.mockFactory();
		//Layer l = Layer.mockFactory();
		
		
		
		Layer homeMadelayer = new Layer();
		homeMadelayer = new Layer();
		homeMadelayer.setLayerGUID( UUID.randomUUID().toString() );
		homeMadelayer.setLayerKeywordList( "kword" );
		homeMadelayer.setLayerTitle( "Title" );
		homeMadelayer.setLayerName( "TitleName" );
		homeMadelayer.setLayerDescription( "Abstract" );
		homeMadelayer.setStatus( 1 );
		//TODO comment in as soon as it works, and remove lower line!
		//layerFromWebTier.setFeaturesourceId( Integer.parseInt(paramMap.get("FeatureTypeId").get(0)) );
		homeMadelayer.setFeaturesourceId( homeMadeFs.getDiffuserId() );
		Calendar cal = Calendar.getInstance();
		Date dt = cal.getTime();
		homeMadelayer.setCreation_date(dt);
		homeMadelayer.setUpdate_date(dt);
		
		Diffuser diffuser2 = homeMadelayer.getDiffuser();
		
		homeMadelayer.store();
		
		
		Diffuser diffuser = new Diffuser(0);
		
		//change this to match the new api
		//mock.publishLayer( homeMadelayer.getLayerGUID(), homeMadelayer.getFeatureSource().getFeatureGUID(), AttributeAlias, Title, Quality_Area, KeywordList, Abstract);
	}

}
