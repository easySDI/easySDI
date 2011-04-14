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
package org.easysdi.publish.helper;

import java.util.List;
import java.util.Map;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.exception.PublishConfigurationException;


/*
 * This interface should be implemented by a class that is specific to a 
 * spatial geodatabase. It permits action on a geodata set.
 */

public interface IHelper {
	public void dropTable(String table) throws PublishConfigurationException;
	public void renameTable(String oldTable, String newTable) throws PublishConfigurationException;
	public void setAttributeAliases(String table, Map<String,String> columns) throws PublishConfigurationException;
	public void setGeodatabase(Geodatabase geoDb) throws PublishConfigurationException;
	public void setConnectionInfo(Geodatabase geoDb, String table) throws PublishConfigurationException;
	public List<String> getColumnNameFromTable(String postgisOutputTableName) throws PublishConfigurationException;
	public void initDatabaseForDiffuser(String currentUser) throws PublishConfigurationException;
}
