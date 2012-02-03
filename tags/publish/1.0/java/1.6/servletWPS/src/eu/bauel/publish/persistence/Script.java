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
import java.util.HashMap;

/*
 * This class represent a table in the persistence. It
 * extends the JTable class to make it possible to retrieve
 * data into an object of this class, or store data from
 * an object of this class to the database.
 */
public class Script extends JTable {
	private Integer[] id = new Integer[1];
	private Integer[] transformator = new Integer[1];
	private String[] name = new String[1];
	private String[] location = new String[1];
	private String[] command = new String[1];
	private String[] arguments = new String[1];
	private String[] conditions = new String[1];
	private String[] requiredFiles = new String[1];

	private HashMap fields = new HashMap();
	//Constructor to load an empty instance
	public Script(Connection c){
		super(c, "script", "id");
		initFields();
		setFields(fields);
	}
	
	public Script(){
		super(c, "script", "id");
		initFields();
		setFields(fields);
	}
	
	//Constructor to load an instance with existing data
	public Script(int id, Connection c){
		super(c, "script", "id");
		initFields();
		setFields(fields);
		loadData(id);
	}
	
	private void initFields(){
		fields.put( "id", id );
		fields.put( "transformator", transformator );
		fields.put( "name", name );
		fields.put( "location", location );
		fields.put( "command", command );
		fields.put( "arguments", arguments );
		fields.put( "conditions", conditions );
		fields.put( "requiredfiles", requiredFiles );

	}

	public void setId(Integer id) {
		this.id[0] = id;
	}

	public Integer getId() {
		return id[0];
	}

	public void setTransformator(Integer transformator) {
		this.transformator[0] = transformator;
	}

	public Integer getTransformator() {
		return transformator[0];
	}

	public void setName(String name) {
		this.name[0] = name;
	}

	public String getName() {
		return name[0];
	}

	public void setLocation(String location) {
		this.location[0] = location;
	}

	public String getLocation() {
		return location[0];
	}

	public void setCommand(String command) {
		this.command[0] = command;
	}

	public String getCommand() {
		return command[0];
	}

	public void setArguments(String arguments) {
		this.arguments[0] = arguments;
	}

	public String getArguments() {
		return arguments[0];
	}

	public void setConditions(String conditions) {
		this.conditions[0] = conditions;
	}

	public String getConditions() {
		return conditions[0];
	}

	public void setRequiredFiles(String requiredFiles) {
		this.requiredFiles[0] = requiredFiles;
	}

	public String getRequiredFiles() {
		return requiredFiles[0];
	}
}
