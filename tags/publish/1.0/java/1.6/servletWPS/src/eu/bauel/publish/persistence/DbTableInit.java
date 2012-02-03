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
import java.util.Map;
import java.util.logging.Logger;

import javax.servlet.ServletConfig;

/*
 * This class contains methods that are called once to init
 * the persistence. It has a revision mechanism so that changes may easily
 * be added.
 */
public class DbTableInit {
	/*
	 * Create the required tables for EasySDI Publish
	 */

	static Logger logger = Logger.getLogger("eu.bauel.publish.persistence.WPSServlet");

	public static void init(Connection connection) throws Exception{

		logger.info( "initializing database...");
		String dbManu = DBConnection.getInitParameter("DB_MANUFACTURER"); 
		if(dbManu.equalsIgnoreCase("mysql")){
			MysqlDbInit.init(connection);
		}else if(dbManu.equalsIgnoreCase("hsqldb")){
			HsqlDbInit.init(connection);
		}else{
			throw new Exception("Unsupported db manufacturer:"+dbManu);
		}
	}
}
