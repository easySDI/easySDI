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
package org.easysdi.publish.dat.transformation.plugin;

import java.io.IOException;
import java.sql.Connection;
import java.util.ArrayList;
import java.util.List;

import com.eaio.uuid.UUID;

import org.easysdi.publish.exception.DataSourceNotFoundException;
import org.easysdi.publish.exception.DataSourceWrongFormatException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.TransformationException;

public class OGRTransformerTest {
	
	public static Connection c;
	
	public static void main(String... args){
		OGRTransformer t = new OGRTransformer();
		t.setLocation("C:\\Program Files\\FWTools2.4.7\\bin\\");
		try {			
			UUID uuid = new UUID();
			System.out.println("uuid:"+uuid);
			String postgisOutputTableName = uuid.toString().replace("-", "");
			List<String> URLs = new ArrayList<String>();
			URLs.add("http://localhost/testdata/gg25.zip");
			String coordEpsgCode = "EPSG:4326";
			String sourceDataType="SHAPE";
			String ScriptName="none";
			String dbhost = "localhost";
			String dbport = "5432";
			String dbname = "postgis";
			String dbusername = "postgres";
			String dbpassword = "pwd";
			String dbschema = "public";
			String sourceFileDir = "C:\\wamp\\www\\testdata\\";
			
			try {        
				t.transformDataset(null, postgisOutputTableName, sourceFileDir, URLs, dbhost, dbport, dbname, dbusername, dbpassword, dbschema, coordEpsgCode, "");
				System.out.println("Terminated");
			} catch (DataSourceWrongFormatException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (PublishConfigurationException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (DataSourceNotFoundException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (TransformationException e){
				e.printStackTrace();
			} 
			
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}	
	}	
}

