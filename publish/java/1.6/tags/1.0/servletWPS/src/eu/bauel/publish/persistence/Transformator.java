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
public class Transformator extends JTable {
	protected Integer[] id = new Integer[1];
	protected String[] name = new String[1];
	protected Integer[] type = new Integer[1];

	
	
	private HashMap fields = new HashMap();
	//Constructor to load an empty instance
	public Transformator(Connection c){
		super(c, "etl", "id");
		initFields();
		setFields(fields);
	}
	
	//Constructor to load an instance with existing data
	public Transformator(int id, Connection c){
		super(c, "etl", "id");
		initFields();
		setFields(fields);
		loadData(id);
	}
	
	private void initFields(){
		fields.put( "id", id );
		fields.put( "name", name );
		fields.put( "type", type );
	}
}
