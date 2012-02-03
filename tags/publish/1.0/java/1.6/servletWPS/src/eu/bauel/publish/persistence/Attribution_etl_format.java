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
public class Attribution_etl_format extends JTable {
	protected Integer[] id = new Integer[1];
	protected Integer[] etlId = new Integer[1];
	protected Integer[] formatId = new Integer[1];

	private HashMap fields = new HashMap();
	
	//Constructor to load an empty instance
	public Attribution_etl_format(Connection c){
		super(c, "attribution_etl_format", "id");
		initFields();
		setFields(fields);
	}
	
	//Constructor to load an instance with existing data
	public Attribution_etl_format(int id, Connection c){
		super(c, "attribution_etl_format", "id");
		initFields();
		setFields(fields);
		loadData(id);
	}
	
	private void initFields(){
		fields.put( "id", id );
		fields.put( "etlId", etlId );
		fields.put( "formatId", formatId );
	}
}
