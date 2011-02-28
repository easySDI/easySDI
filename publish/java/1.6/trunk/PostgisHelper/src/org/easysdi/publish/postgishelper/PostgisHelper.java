/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & Remy Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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
package org.easysdi.publish.postgishelper;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.helper.IHelper;

public class PostgisHelper implements IHelper{
	private Connection connection;
	private Logger logger = Logger.getLogger("eu.bauel.publish.postgishelper.PostGisHelper");
	private Statement statement;
	private ResultSet rs;
	private String url;
	private String user;
	private String pwd;	

	public PostgisHelper(){}
	
	public PostgisHelper(Geodatabase g){
		this.url = g.getUrl();
		this.user = g.getUser();
		this.pwd = g.getPwd();
	}
	
	//Constructor for test purpose only
	public PostgisHelper(String url, String user, String pwd){
		this.url = url;
		this.user = user;
		this.pwd = pwd;
	}

	public void setGeodatabase(Geodatabase g)
	{
		this.url = g.getUrl();
		this.user = g.getUser();
		this.pwd = g.getPwd();
	}
	
	private void getConnection() throws PublishConfigurationException{
		try
		{   
			//Load the JDBC driver and establish a connection. 
			Class.forName("org.postgresql.Driver");
			connection = DriverManager.getConnection(this.url, this.user, this.pwd);
			statement = connection.createStatement();

		}catch(Exception e){
			logger.warning(e.getMessage());
			throw new PublishConfigurationException("Could not connect to: "+this.url+" cause:"+e.getMessage());
		}
	}

	private void closeConnection(){
		try {
			if(rs != null)
				rs.close();
			if(statement != null)
				statement.close();
			if(connection != null)
				connection.close();
		} catch (SQLException e) {
			logger.warning(e.getMessage());
		}
	}
	
	public void renameTable(String oldTable, String newTable) throws PublishConfigurationException{
		getConnection();
		try {
			statement.executeUpdate("ALTER TABLE \""+oldTable+"\" RENAME TO \""+newTable+"\"");
			logger.info(oldTable+" renamed to: "+newTable);
		} catch (SQLException e) {
			logger.warning(e.getMessage());
			throw new PublishConfigurationException("Could not rename table: "+oldTable+" cause:"+e.getMessage());
		}finally{
			closeConnection();
		}
	}
	
	public void dropTable(String table) throws PublishConfigurationException{
		getConnection();
		try {
			//drop only table if it exists
			String query= "select count(*) as result from INFORMATION_SCHEMA.TABLES "+
			"where table_name = \'"+table+"\'";
		
			ResultSet rs = statement.executeQuery(query);
			if(rs.next()){
				if(rs.getInt("result") == 1){
					statement.executeUpdate("DROP TABLE \""+table+"\"");
					logger.info(table+": dropped");
				}else{
					logger.info(table+": does not exist, not dropped");
				}
			}
		} catch (SQLException e) {
			logger.warning(e.getMessage());
			throw new PublishConfigurationException("Could not drop table: "+table+" cause:"+e.getMessage());
		}finally{
			closeConnection();
		}
	}

	//column_name 64 chars max.
	public void setAttributeAliases(String table, Map<String,String> columns) throws PublishConfigurationException{
		getConnection();
		try {
			for(String key : columns.keySet()){
				String query = "ALTER TABLE \""+table+"\" RENAME COLUMN \""+key+"\" TO \""+columns.get(key)+"\"";
				System.out.println(query);
				statement.executeUpdate(query);
			}
		} catch (SQLException e) {
			logger.warning(e.getMessage());
			throw new PublishConfigurationException("Could not set alias for table: "+table+" cause:"+e.getMessage());
		}finally{
			closeConnection();
		}
	}
	
	public List<String> getColumnNameFromTable(String table) throws PublishConfigurationException{
		//check if the constraint exists
		List<String> arLst = new ArrayList<String>();
		String query = "select column_name FROM information_schema.columns "+
		"WHERE table_name = '"+table+"' order by ordinal_position asc";
		System.out.println(query);
		getConnection();
		try {
			rs = statement.executeQuery(query);
			int i = 0;
			while(rs.next()){
				//skip fid record and geometry
				if(i > 1)
					arLst.add(rs.getString("column_name"));
				i++;
			}
		} catch (SQLException e) {
			logger.warning(e.getMessage());
			throw new PublishConfigurationException("Could not retrieve column name for table: "+table+" cause:"+e.getMessage());
		}finally{
			closeConnection();
		}
		return arLst;
	}
}
