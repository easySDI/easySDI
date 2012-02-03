package ch.depth.services.config;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

import javax.servlet.http.HttpServletRequest;

public class TestConfig {
	
	static Connection c = null;
	
	public static void main(String args[]){
		init();
		
		Config conf = new Config(c);
		String str = conf.getPublicationServerlist();
		System.out.println(str);		

	}
	
	public static void init(){

		//get the connection
		try {
			Class.forName("org.hsqldb.jdbcDriver").newInstance();
			c = DriverManager.getConnection("jdbc:hsqldb:hsql://localhost/WpsPublish17", "sa", "");
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

	}
}
