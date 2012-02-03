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
package eu.bauel.publish.persistence;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;

public class TestPersistence {

	/**
	 * @param args
	 */

	static Connection c = null;
	private static Integer plop = new Integer(5);
	private static SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");  

	public static void main(String[] args) throws InstantiationException, Exception, Throwable {
		// get the connection and create a new table for the tests
		//init();

		Class.forName("org.hsqldb.jdbcDriver").newInstance();
		c = DriverManager.getConnection("jdbc:hsqldb:hsql://localhost/WpsPublish18", "sa", "");
		
		
		//Create a Fs (test the date field)
		//Timestamp yyyy-mm-dd hh:mm:ss.SSSSS 
		Featuresource fs = new Featuresource(12,c);
		fs.setDiffuserId(1);
		fs.setCreation_date(tryParseDate("2004-01-12"));

		if(fs.store() == false){
			System.out.println("Error occured, cause:"+fs.errorCause +" message:"+ fs.errorMessage);
		}

		fs = new Featuresource(0, c);
		System.out.println("fs");

		
		
		Diffuser diff = new Diffuser(0, c);
		System.out.println("diffuser:");
		printDiffuser(diff);
		//
		//An insert, please leave the id as null!
		//

		Diffuser dinsert = new Diffuser(c);
		dinsert.setName("new diffuser");
		dinsert.setDatabaseid(2);
		//ommit or leave a field null
		dinsert.setUrl(null);
		dinsert.setUsername("yves");
		dinsert.setPassword("rogne");
		dinsert.setType(1);

		System.out.println("inserting diffuser:");
		printDiffuser(dinsert);

		if(dinsert.store() == false){
			System.out.println("Error occured, cause:"+dinsert.errorCause +" message:"+ dinsert.errorMessage);
		}

		//
		//Load a created diffuser
		//
		System.out.println("loading diffuser: id: 1");
		dinsert = new Diffuser(9999, c);
		printDiffuser(dinsert);

		//
		//Update the diffuser
		//
		System.out.println("updating diffuser: id: 1");

		dinsert.setName("Updated diffuser");
		if(dinsert.store() == false){
			System.out.println("Error occured, cause:"+dinsert.errorCause +" message:"+ dinsert.errorMessage);
		}
		printDiffuser(dinsert);

		//
		// Delete the diffuser
		//

		System.out.println("before delete diffuser id:"+1);
		printTableDiffuserContent();

		//deleting diffusor
		dinsert =  new Diffuser(1, c);
		if(dinsert.delete() == false){
			System.out.println("Error occured, cause:"+dinsert.errorCause +" message:"+ dinsert.errorMessage);
		}

		System.out.println("before delete diffuser id:"+1);
		printTableDiffuserContent();





		System.out.println("demo finished...");
	}


	public static Date tryParseDate(String s){
		Date d = null;
		try {
			d = sdf.parse(s);
		} catch (ParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return d;
	}

	public static void printDiffuser(Diffuser d){
		System.out.println(
				"id:"+d.getId()+"\r\n"+
				"name:"+d.getName()+"\r\n"+
				"database:"+d.getDatabaseid()+"\r\n"+
				"url:"+d.getUrl()+"\r\n"+
				"username:"+d.getUsername()+"\r\n"+
				"password:"+d.getPassword()+"\r\n"+
				"type:"+d.getType()+"\r\n"
		);
	}

	public static void printTableDiffuserContent(){
		try {
			Statement statement = c.createStatement();
			String query= "select id, name from diffuser";
			ResultSet rs = statement.executeQuery(query);
			while(rs.next())
				System.out.println("id:"+rs.getInt("ID")+" name:"+rs.getString("NAME"));
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	public static void init(){
		//start the db
		String HSQL_DB_NAME = "test3";
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
			c = DriverManager.getConnection("jdbc:hsqldb:hsql://localhost/test3", "sa", "");
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
	}

}
