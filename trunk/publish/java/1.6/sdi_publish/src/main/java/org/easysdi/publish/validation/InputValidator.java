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
package org.easysdi.publish.validation;

import java.util.List;
import java.util.logging.Logger;

import javax.servlet.http.HttpServletRequest;

import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.PublicationException;


/*
 * This class merges all the input values checks for methods described in the interfaces.
 * 
 */
public class InputValidator {
	
	static Logger logger = Logger.getLogger("org.easysdi.publish.validation.InputValidator");

	/*
	 * This method is called when the wpsservlet receives the arguments.
	 * if a value fails the checks, a PublicationException must be thrown
	 */
	public static void publishLayerValidator(String layerId, String featureTypeId,
			List<String> attributeAlias, String title, String name, String qualityArea,
			String keywordList, String abstr) throws DataInputException{
		
		for(String row : attributeAlias){
			String[] temp = row.split("=");
	
		if(temp.length < 2){
			logger.info("no alias to set for: "+row);
			continue;
		}
		if(temp[1].equals("")){
			logger.info("alias is empty for: "+row);
			continue;
		}
		//skip if same name
		if(temp[0].equals(temp[1])){
			logger.info("column and alias ar the same for: "+row);
			continue;
		}
		//send exception if wrong usage
		//reserved keywords
		if(temp[1].toLowerCase().equals("the_geom")||
				temp[1].toLowerCase().equals("geom")){
			logger.info("throwing exception for:"+row);
			throw new DataInputException("The alias:"+temp[1]+" is a reserved keyword or is not allowed.");
		}
		
		/*TODO also throw an exception if two columns have the same name */
		
		
		/*TODO also throw an exception for illegal chars like comma, < > etc.. */
		
	
		}
	}
	
	/*
	 * This method is called when the wpsservlet receives the arguments.
	 * if a value fails the checks, a PublicationException must be thrown
	 */
	public static void transformDatasetValidator( String featureSourceId, String diffusorName, List<String> URLs, String ScriptName, String sourceDataType, String epsgProj, String dataset) 
	throws DataInputException {
		/*
		if(!geometry.equalsIgnoreCase("point")&&!geometry.equalsIgnoreCase("line")&&!geometry.equalsIgnoreCase("polygon")){
			throw new DataInputException("Invalid value supplied for geometry:"+geometry);
		}
		*/
	}
	
	public static void deletePublicationServer(HttpServletRequest req) throws DataInputException{
		String id = req.getParameter("id");
		if(id != null){
			try{
				Integer.parseInt(id);
			}
			catch(NumberFormatException e){
				throw new DataInputException("Invalid value supplied for id:"+id);
			}
		}
	}
	
	public static void getAvailableDatasetFromSource(HttpServletRequest req) throws DataInputException{
		
	}
	
	
	public static void listFeatureSources(HttpServletRequest req) throws DataInputException{
		String list = req.getParameter("list");
		if(list == null){
			throw new DataInputException("Missing parameter \"list\", comma separated FS guids");
		}else if(list.equals("")){
			throw new DataInputException("parameter \"list\" is empty");
		}
	}
	
	public static void getTransformationProgress(HttpServletRequest req) throws DataInputException{
		String guid = req.getParameter("guid");
		if(guid == null){
			throw new DataInputException("Missing parameter \"guid\", comma separated FS guids");
		}else if(guid.equals("")){
			throw new DataInputException("parameter \"guid\" is empty");
		}
		
	}
	
	public static void managePublicationServer(HttpServletRequest req) throws DataInputException{
		String id = req.getParameter("id");
		String name = req.getParameter("name");
		String type = req.getParameter("type");
		String url = req.getParameter("url");
		String username = req.getParameter("username");
		String password = req.getParameter("password");
		String dbname = req.getParameter("dbname");
		String dbtype = req.getParameter("dbtype");
		String dburl = req.getParameter("dburl");
		String dbusername = req.getParameter("dbusername");
		String dbpassword = req.getParameter("dbpassword");
		
		if(id != null && !id.equals("none") && !id.equals("")){
			try{
				Integer.parseInt(id);
			}
			catch(NumberFormatException e){
				throw new DataInputException("Invalid value supplied for id:"+id);
			}
		}
		if(name == null || name == "")
			throw new DataInputException("missing or wrong value for \"name\"");
		if(type == null || type == "")
			throw new DataInputException("missing or wrong value for \"type\"");
		if(url == null || url == "")
			throw new DataInputException("missing or wrong value for \"url\"");
		if(dbname == null || dbname == "")
			throw new DataInputException("missing or wrong value for \"dbname\"");
		if(dbtype == null || dbtype == "")
			throw new DataInputException("missing or wrong value for \"dbtype\"");
		if(dburl == null || dburl == "")
			throw new DataInputException("missing or wrong value for \"dburl\"");
	}
	
}
