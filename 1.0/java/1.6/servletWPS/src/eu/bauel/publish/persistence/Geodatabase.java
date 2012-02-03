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
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.geotools.filter.expression.ThisPropertyAccessorFactory;

import ch.depth.services.wps.WPSServlet;

/*
 * This class represent a table in the persistence. It
 * extends the JTable class to make it possible to retrieve
 * data into an object of this class, or store data from
 * an object of this class to the database.
 */
public class Geodatabase extends JTable{
	private Integer[] id = new Integer[1];
	private String[] name = new String[1];
	private String[] url = new String[1];
	private String[] username = new String[1];
	private String[] pwd = new String[1];
	private String[] scheme = new String[1];
	private Integer[] type = new Integer[1];

	private HashMap fields = new HashMap();
	
	
	//Constructor to load an empty instance
	public Geodatabase(Connection c){
		super(c, "geodatabase", "id");
		initFields();
		setFields(fields);
	}
	
	//Constructor to load an instance with existing data
	public Geodatabase(int id, Connection c){
		super(c, "geodatabase", "id");
		initFields();
		setFields(fields);
		loadData(id);
	}

	//Constructor to load an instance with existing data
	public Geodatabase(int id ){
		super( "geodatabase", "id");
		initFields();
		setFields(fields);
		loadData(id);
	}
	
	private void initFields(){
		fields.put( "id", id );
		fields.put( "name", name );
		fields.put( "url", url );
		fields.put( "username", username );
		fields.put( "pwd", pwd );
		fields.put( "scheme", scheme );
		fields.put( "type", type );
	}

	public void setId(Integer id) {
		this.id[0] = id;
	}

	public Integer getId() {
		return id[0];
	}

	public void setName(String name) {
		this.name[0] = name;
	}

	public String getName() {
		return name[0];
	}

	public void setUrl(String url) {
		this.url[0] = url;
	}

	public String getUrl() {
		return url[0];
	}

	public void setUsername(String username) {
		this.username[0] = username;
	}

	public String getUsername() {
		return username[0];
	}

	public void setPassword(String password) {
		this.pwd[0] = password;
	}

	public String getPassword() {
		return pwd[0];
	}

	public void setSchema(String schema) {
		this.scheme[0] = schema;
	}

	public String getSchema() {
		return scheme[0];
	}

	public void setType(int id) {
		this.type[0] = id;
	}

	public String getType() {
		return getTypeList().get( this.type[0] );
	}
	
	public Map<Integer, String > getTypeList() {
		Statement statement;
		Map<Integer, String> res = new HashMap<Integer, String>();
		try {
			statement = c.createStatement();
			ResultSet rs;
			rs = statement.executeQuery("SELECT id, name FROM geodatabase_type");
			while(rs.next())
				res.put(rs.getInt("id"), rs.getString("name") );
		} catch (SQLException e) {
			System.out.println(e.getMessage());
		}
		return res;
	}
	
	public static Geodatabase getGeodB( String diffusorName )
	{
		int databaseId = -1;
		
		Connection c = DBConnection.c;
		Statement statement;
		try {
			statement = c.createStatement();
			String query = "SELECT g.id as geodbId, d.id as diffId FROM diffuser d, geodatabase g where d.name='"+diffusorName+"' and d.databaseId = g.id";
			System.out.println(query);
			ResultSet rs = statement.executeQuery(query);
			rs.next();
			databaseId = rs.getInt("geodbId");
		} catch (SQLException e) {
			System.out.println(e.getMessage());
			e.printStackTrace();
		}
		  catch (Exception e) {
			System.out.println(e.getMessage());
			e.printStackTrace();
		}
		Geodatabase geoDb = new Geodatabase(databaseId, c);
		return geoDb;
	}
	
	public static Geodatabase getGeodatabaseFromName( String geodbname )
	{
		int databaseId = -1;
		
		Connection c = JTable.c;
		if(c == null)
			c = WPSServlet.c;
		Statement statement;
		try {
			statement = c.createStatement();
			String query = "SELECT g.id as geodbId FROM geodatabase g where g.name='"+geodbname+"'";
			System.out.println(query);
			ResultSet rs = statement.executeQuery(query);
			rs.next();
			databaseId = rs.getInt("geodbId");
		} catch (SQLException e) {
			System.out.println(e.getMessage());
		}
		Geodatabase geoDb = new Geodatabase(databaseId, c);
		return geoDb;
	}
	
}
