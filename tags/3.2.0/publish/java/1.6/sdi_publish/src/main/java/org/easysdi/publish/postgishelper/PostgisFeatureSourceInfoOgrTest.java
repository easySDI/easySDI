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

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.TransformationException;
import org.easysdi.publish.helper.Attribute;

public class PostgisFeatureSourceInfoOgrTest {

	public static void main(String[] args) {
		Map<String, String> connectionInfo = new HashMap<String, String>();
		connectionInfo.put( "dbname", "postgis" );
		connectionInfo.put( "dbhost", "localhost" );
		connectionInfo.put( "dbport", "5432" );
		connectionInfo.put( "dbusername", "postgres" );
		connectionInfo.put( "dbpassword", "rbago000''" );	
		try {
			//Put a breakpoint here and inspect the FeatureSource info.
			PostgisFeatureSourceInfoOgr fsi = new PostgisFeatureSourceInfoOgr(connectionInfo ,"ef2e4440e04b11df9a7f00238b529631");
			List<Attribute> lAtr = fsi.getAtrList();
			for(Attribute a : lAtr){
				System.out.println("name:"+a.getName()+" value:"+a.getValue());
			}
			
			System.out.println("MinX");
			System.out.println(fsi.getBbox().get("MinX").toString());
			System.out.println("MinY");
			System.out.println(fsi.getBbox().get("MinY").toString());
			System.out.println("MaxX");
			System.out.println(fsi.getBbox().get("MaxX").toString());
			System.out.println("MaxY");
			System.out.println(fsi.getBbox().get("MaxY").toString());
			
			
		
		} catch (TransformationException e) {
			e.printStackTrace();
		} catch (PublishConfigurationException e) {
			e.printStackTrace();
		}
		System.out.println("end");
	}
}

