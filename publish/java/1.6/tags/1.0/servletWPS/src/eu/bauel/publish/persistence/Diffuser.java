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
import java.util.HashMap;

import ch.depth.services.wps.WPSServlet;

/*
 * This class represent a table in the persistence. It
 * extends the JTable class to make it possible to retrieve
 * data into an object of this class, or store data from
 * an object of this class to the database.
 */
public class Diffuser extends JTable {
	//thank to autoboxing, we need a workaround to pass wrapper class
	//as reference to the base class. :-(
	private Integer[] id = new Integer[1];
	private Integer[] databaseid= new Integer[1];
	private String[] name = new String[1];
	private String[] url = new String[1];
	private String[] username = new String[1];
	private String[] pwd = new String[1];
	private Integer[] type = new Integer[1];
	private HashMap fields = new HashMap();
	

	//Constructor to load an empty instance
	public Diffuser(int id){
		super( "diffuser", "id");
		initFields();
		setFields(fields);
		loadData(id);
	}

	//Constructor to load an empty instance
	public Diffuser(Connection c){
		super(c, "diffuser", "id");
		initFields();
		setFields(fields);
	}
	
	//Constructor to load an instance with existing data
	public Diffuser(int id, Connection c){
		super(c, "diffuser", "id");
		initFields();
		setFields(fields);
		loadData(id);
	}
	
	private void initFields(){
		fields.put( "id", id );
		fields.put( "databaseid", databaseid );
		fields.put( "name", name );
		fields.put( "url", url );
		fields.put( "username", username );
		fields.put( "pwd", pwd );
		fields.put( "type", type );
	}

	public void setId(Integer id) {
		this.id[0] = id;
	}

	public Integer getId() {
		return id[0];
	}

	public void setDatabaseid(Integer databaseid) {
		this.databaseid[0] = databaseid;
	}

	public Integer getDatabaseid() {
		return databaseid[0];
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

	public void setType(Integer type) {
		this.type[0] = type;
	}

	public Integer getType() {
		return type[0];
	}
	
	public Geodatabase getGeodatabase()
	{
		return new Geodatabase( getDatabaseid() );
	}
		
	
}
