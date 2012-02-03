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
import org.easysdi.publish.postgishelper.PostgisHelper;

public class PostgisHelperTest {

	public static void main(String[] args) {
		
		PostgisHelper ph = new PostgisHelper("jdbc:postgresql://localhost:5432", "postgres", "rbago000''");
				
		ph.setTable("myNewDiff");
		
		try {
			ph.initDatabaseForDiffuser("myNewDiff");
		} catch (PublishConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		
		/*
		//Get columns names.
		List<String> atrLst = null;
		try {
			atrLst = ph.getColumnNameFromTable("ef2e4440e04b11df9a7f00238b529631");
		} catch (PublishConfigurationException e) {
			e.printStackTrace();
		}

		//sample drop table, do nothing if the table doesn't exit.
		try {
			ph.dropTable("gg25_a_123456");
		} catch (PublishConfigurationException e) {
			e.printStackTrace();
		}
		*/
		
		/*
		//sample setting aliases
		Map<String,String> attributes = new HashMap<String,String>();
		attributes.put("AREA", "aire");
		attributes.put("PERIMETER","périmètre");
		attributes.put("OBJECTVAL","obj value");
		attributes.put("OBJECTID","objectid");
		attributes.put("YEAROFCHAN","année de changement");
		attributes.put("SEENR","num lac");
		attributes.put("GEMTEIL","partie commune");
		attributes.put("BEZIRKSNR","région");
		attributes.put("KANTONSNR","canton");
		attributes.put("GEMNAME" ,"nom de la commune");
		attributes.put("GEMFLAECHE","surface");
		
		try {
			ph.setAttributeAliases("gg25_a_essai6", attributes);
		} catch (PublishConfigurationException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}

        */
		//Give back column names
		/*
		try {
			atrLst = ph.getColumnNameFromTable("ef2e4440e04b11df9a7f00238b529631");
		} catch (PublishConfigurationException e) {
			e.printStackTrace();
		}

		//Print out attributes
		StringBuilder stb = new StringBuilder();
		int i = 0;
		for(String attr : atrLst){
			if(i < (atrLst.size() - 1))
				stb.append(attr + ",");
			else
				stb.append(attr);
			i++;
		}
		System.out.println("attributes are:"+stb.toString());
		*/
		
		
	}
	
	
	
}
