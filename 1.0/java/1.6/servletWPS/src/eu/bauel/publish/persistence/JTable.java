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

import java.io.PrintWriter;
import java.io.StringWriter;
import java.io.Writer;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.logging.Logger;

import ch.depth.services.wps.WPSServlet;
import eu.bauel.publish.Utils;



/*	This class provides smooth marshaling and unmarschaling methods in HSQLDb
 * 	for Objects that extend it.
 * 	The use case is: we have for an object a table in BD that represent all fields.
 * 	The object fields and table column names are 1 to one represented with exactly the
 * 	SAME names. The table used for the object MUST exist before the serialization. 
 * 	Each table MUST also provide a primary key (id), with the option 'identity' (auto-
 *  increment).
 *  
 *  It's up to you to use the correct matching between HSQLDB types and Java types. For 
 *  more info, please look at:
 *  
 *  http://hsqldb.org/doc/guide/ch09.html#datatypes-section
 *  Or you can look at the class: org.hsqldb.Types
 *  
 *  
 */

public abstract class JTable {

	protected static Logger logger = Logger.getLogger("eu.bauel.publish.persistence.JTable");

	protected String dbManu = DBConnection.getInitParameter("DB_MANUFACTURER");

	//the table name for the object
	protected String table;

	//the primary key
	protected String tablekey;

	//the connection instance to the db
	static public Connection c;

	private ArrayList<String> tblFieldsName;

	private HashMap childFields;

	public String errorCause = "";

	public String errorMessage = "";

	public static void setConnection(Connection externalConnection)
	{
		c= externalConnection;		
	}

	private SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");  


	public JTable(String table, String tableKey)
	{
		//c= WPSServlet.c;

		//this.c= TestPersistence.c;

		this.table = table;
		this.tablekey =tableKey;
		this.tblFieldsName = new ArrayList<String>();
		loadColumnName();
	}


	//only for TestPersistence.java
	public JTable(Connection connec, String table, String tableKey)
	{
		c= connec;
		this.table = table;
		this.tablekey =tableKey;
		this.tblFieldsName = new ArrayList<String>();
		loadColumnName();
	}
	//only for TestPersistence.java
	public JTable(int id, Connection connec, String table, String tableKey)
	{
		c= connec;
		this.table = table;
		this.tablekey =tableKey;
		this.tblFieldsName = new ArrayList<String>();
		loadColumnName();
	}

	public final void forceStore()
	{
		StringBuilder query = new StringBuilder();
		try {
			Statement statement = c.createStatement();
			//statement.executeUpdate("INSERT INTO version (component, version) VALUES ('wpspublish', 0)");
			query.append("insert into "+this.table+" (");
			for(int i = 0; i<this.tblFieldsName.size(); i++){
				String columnName = this.tblFieldsName.get(i).toLowerCase();
				//skip the id field
				if(columnName.toUpperCase().equals(this.tablekey.toUpperCase()))
					continue;
				query.append(columnName);
				if(i <= (this.tblFieldsName.size()-2))
					query.append(", ");
				else
					query.append(") ");
			}
			query.append("VALUES (");
			for(int i = 0; i<this.tblFieldsName.size(); i++){

				String columnName = this.tblFieldsName.get(i).toLowerCase();
				//logger.info("column:"+columnName);
				//skip the id field
				if(columnName.toUpperCase().equals(this.tablekey.toUpperCase()))
					continue;
				Object[] obj2 = (Object[])childFields.get(columnName);
				//logger.info("obj2:"+obj2.toString());

				//Do all types has a good toString() representation?!?
				String columnValue = null;
				if(obj2 == null){
					query.append("null");
				}
				else if(obj2[0] == null){
					query.append("null");
				}
				else{
					String type = obj2[0].getClass().getName();
					if(obj2[0].getClass().getName() == "java.util.Date")
						columnValue = sdf.format(obj2[0]);
					else
						columnValue = obj2[0].toString();
					query.append("'"+columnValue+"'");
				}
				if(i <= (this.tblFieldsName.size()-2))
					query.append(", ");
				else
					query.append(") ");
			}

			//System.out.println("query:"+query);
			statement.executeUpdate(query.toString());
		}catch (SQLException ex) {
			// TODO Auto-generated catch block
			this.errorCause = "Exception";
			this.errorMessage = ex.getMessage() + "  " +Utils.getStackTrace(ex);
			logger.warning( this.errorMessage );
		}

		catch (Exception e) {
			// TODO Auto-generated catch block
			this.errorCause = "Exception";
			this.errorMessage = "query is:"+query+" stacktrace:"+Utils.getStackTrace(e);
			logger.warning( this.errorMessage );
		}
	}

	public final boolean store(){

		//logger.info("store: " + this.getClass().getCanonicalName() );
		StringBuilder query = new StringBuilder();
		try {
			Statement statement = c.createStatement();
			//Fix the case everywhere...
			Object[] obj = (Object[])childFields.get(tablekey.toLowerCase());
			if((Integer)obj[0] == null){
				//this is an insert
				//statement.executeUpdate("INSERT INTO version (component, version) VALUES ('wpspublish', 0)");
				query.append("insert into "+this.table+" (");
				for(int i = 0; i<this.tblFieldsName.size(); i++){
					String columnName = this.tblFieldsName.get(i).toLowerCase();
					//skip the id field
					if(columnName.toUpperCase().equals(this.tablekey.toUpperCase()))
						continue;
					query.append(columnName);
					if(i <= (this.tblFieldsName.size()-2))
						query.append(", ");
					else
						query.append(") ");
				}
				query.append("VALUES (");
				for(int i = 0; i<this.tblFieldsName.size(); i++){
					String columnName = this.tblFieldsName.get(i).toLowerCase();
					//logger.info("column:"+columnName);
					//skip the id field
					if(columnName.toUpperCase().equals(this.tablekey.toUpperCase()))
						continue;
					Object[] obj2 = (Object[])childFields.get(columnName);
					//logger.info("obj2:"+obj2.toString());

					//Do all types has a good toString() representation?!?
					String columnValue = null;
					if(obj2 == null){
						query.append("null");
					}
					else if(obj2[0] == null){
						query.append("null");
					}
					else{
						String type = obj2[0].getClass().getName();
						if(obj2[0].getClass().getName() == "java.util.Date")
							columnValue = sdf.format(obj2[0]);
						else
							columnValue = obj2[0].toString();
						query.append("'"+columnValue+"'");
					}
					if(i <= (this.tblFieldsName.size()-2))
						query.append(", ");
					else
						query.append(") ");
				}

				System.out.println("query:"+query);
				statement.executeUpdate(query.toString());
			}
			else{
				//this is an update
				//UPDATE table_name
				//SET column1=value, column2=value2,...
				//WHERE some_column=some_value
				query = new StringBuilder();
				query.append("update "+this.table+" set ");
				for(int i = 0; i<this.tblFieldsName.size(); i++){
					String columnName = this.tblFieldsName.get(i).toLowerCase();
					//skip the id field
					if(columnName.toUpperCase().equals(this.tablekey.toUpperCase()))
						continue;
					Object[] obj2 = (Object[])childFields.get(columnName);
					//Do all types has a good toString() representation?!?
					String columnValue;
					String value;
					if(obj2 == null){
						value = "null";
					}
					else if(obj2[0] == null){
						value = "null";
					}
					else{
						String type = obj2[0].getClass().getName();
						if(obj2[0].getClass().getName() == "java.util.Date")
							columnValue = sdf.format(obj2[0]);
						else
							columnValue = obj2[0].toString();
						value = "'"+columnValue+"'";
					}
					query.append(columnName + "=" + value);
					if(i <= (this.tblFieldsName.size()-2))
						query.append(", ");
				}

				query.append(" where "+this.tablekey+"="+(Integer)obj[0]);

				System.out.println("update:"+query);
				statement.executeUpdate(query.toString());

			}
		}catch (SQLException ex) {
			// TODO Auto-generated catch block
			this.errorCause = "Exception";
			this.errorMessage = ex.getMessage() + "  " +Utils.getStackTrace(ex); 
			logger.warning( this.errorMessage );
			return false;
		} 
		catch (Exception e) {
			// TODO Auto-generated catch block
			this.errorCause = "Exception";
			this.errorMessage = "query is:"+query+" stacktrace:"+Utils.getStackTrace(e);
			logger.warning( this.errorMessage );
			return false;
		}
		return true;	
	}

	public final boolean delete(){
		try 
		{	
			Object[] obj = (Object[])childFields.get(tablekey.toLowerCase());
			if((Integer)obj[0] != null){
				Statement statement = c.createStatement();
				String query = "delete from "+this.table+" where "+this.tablekey+"="+(Integer)obj[0];
				statement.executeUpdate(query);
			}
			else{
				throw new Exception("Cannot delete a row that has ID = null");
			}

		} catch (Exception e) {
			// TODO Auto-generated catch block
			this.errorCause = "Exception";
			this.errorMessage = Utils.getStackTrace(e);
			return false;
		}
		return true;	
	}

	//loads data in the object.
	protected final void loadData(int id){
		String query = "select * from "+this.table+" "+
		"where "+this.tablekey+" ="+id;
		//System.out.println("Query:"+query);
		Statement statement = null;
		ResultSet rs = null;
		try {
			statement = c.createStatement();
			rs = statement.executeQuery(query);
			//System.out.println("Fetching data from table:"+this.table+"for id:"+id);
			if(rs.next()){
				//fill in the child fields
				for(int i = 0; i<this.tblFieldsName.size(); i++){
					String columnName = this.tblFieldsName.get(i).toLowerCase();
					String strValue = rs.getString(columnName);
					//Pointer to the field of the child class
					Object[] field = null;
					if(!this.childFields.containsKey(columnName))
						throw new Exception("column:"+columnName+" doesn't exist in field list");
					else
						field = (Object[])this.childFields.get(columnName);
					//Affect new value for child field
					if(field != null)
					{
						Class clazz = field.getClass();
						Object res = clazz.cast(field);
						//System.out.println("assigning: "+columnName+" = "+strValue);
						Object value = getFieldValuefromString(field.getClass(),strValue);
						field[0] = value;
					}
				}
			}
		}catch (TypeNotFoundException e) {
			// TODO Auto-generated catch block
			this.errorCause = "TypeNotFoundException";
			this.errorMessage = e.getMessage()+"\r\n"+Utils.getStackTrace(e); 
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			this.errorCause = "SQLException";
			this.errorMessage = e.getMessage()+"\r\n"+Utils.getStackTrace(e);
			logger.warning( this.errorMessage );
		} catch (Exception e){
			this.errorCause = "Exception";
			this.errorMessage = e.getMessage()+"\r\n"+Utils.getStackTrace(e);
			logger.warning( this.errorMessage );
			logger.warning( e.getMessage() );
		}

	}

	private void loadColumnName()
	{
		String query = "";
		if(this.dbManu.equalsIgnoreCase("mysql")){
			query = "select COLUMN_NAME as col from INFORMATION_SCHEMA.COLUMNS "+
			"where table_schema = '"+DBConnection.getInitParameter("DB_NAME")+"' "+
			"and table_name = '"+this.table+"'";
		}else if(this.dbManu.equalsIgnoreCase("hsqldb")){
			query = "select COLUMN_NAME as col from INFORMATION_SCHEMA.SYSTEM_COLUMNS "+
			"where table_schem = 'PUBLIC' "+
			"and table_name = '"+this.table+"'";
		}
		logger.info(query);

		//System.out.println("Query:"+query);
		Statement statement = null;
		ResultSet rs = null;
		try {
			statement = c.createStatement();
			rs = statement.executeQuery(query);
			//System.out.println("Fetching column name of the table");
			while(rs.next()){
				//System.out.println(rs.getString("column"));
				this.tblFieldsName.add(rs.getString("col"));
			}
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			this.errorCause = "SQLException";
			this.errorMessage = e.getMessage()+"\r\n"+Utils.getStackTrace(e);
			logger.warning( this.errorMessage );
		}
	}


	protected void setFields(HashMap childFields){
		this.childFields = childFields;
	}

	private Object getFieldValuefromString(Class c, String s)throws TypeNotFoundException{
		Object o = null;
		boolean found = false;
		if(c.equals(Integer[].class)){
			o = Integer.parseInt(s);
			found = true;
		}
		else if(c.equals(Long[].class)){
			o = Long.parseLong(s);
			found = true;
		}
		else if(c.equals(Double[].class)){
			o = Double.parseDouble(s);
			found = true;
		}
		else if(c.equals(Float[].class)){
			o = Float.parseFloat(s);
			found = true;
		}
		else if(c.equals(Byte[].class)){
			o = Byte.parseByte(s);
			found = true;
		}
		else if(c.equals(String[].class)){
			o = s;
			found = true;
		}
		else if(c.equals(Short[].class)){
			o = Short.parseShort(s);
			found = true;
		}
		else if(c.equals(Character[].class)){
			o = s.charAt(0);
			found = true;
		}
		else if(c.equals(Boolean[].class)){
			o = Boolean.parseBoolean(s);
			found = true;
		}
		else if(c.equals(Date[].class)){
			o = tryParseDate(s);
			found = true;
		}
		else
			o = null;
		if(!found)
			throw new TypeNotFoundException();
		return o;
	}

	public Date tryParseDate(String s){
		if(s == null)
			return null;
		Date d = null;
		try {
			d = sdf.parse(s);
		} catch (ParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return d;
	}
}
