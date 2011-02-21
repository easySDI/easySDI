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
package eu.bauel.publish.transformation;

import java.io.File;
import java.io.IOException;
import java.lang.ref.WeakReference;
import java.lang.reflect.Method;
import java.net.URL;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import eu.bauel.publish.persistence.DBConnection;
import eu.bauel.publish.transformation.ITransformerAdapter;
import javax.servlet.ServletConfig;
import eu.bauel.publish.ClassPathHacker;

public class TransformerHelper {

	//The Map Containing The association between file type and 
	static Map<String, String> typeToTransformerMap = new HashMap<String, String>();
	static List<String> transformerPlugIns;
	static String plugInPathCache = "";
	static Logger logger = Logger.getLogger("ch.depth.services.wps.WPSServlet");
	static ServletConfig conf;
	
	//Inits the transformer plug-in. It will scan the plug-in path, look if the classes
	//contain a valid transformer.
	static public void init( ServletConfig config, String plugInPath )
	{
	    logger.info("Scanned Plugin Path: " + plugInPath );
		plugInPathCache = plugInPath;
		String fileTypes = DBConnection.getInitParameter("SupportedTransformedFileTypes");
		String[] typeList = fileTypes.split(","); 
			
		for( String s : typeList )
		{
			typeToTransformerMap.put(s, DBConnection.getInitParameter(s) );
		}
		
		for( String s : typeToTransformerMap.values() )
		{
			//Check that every configured type have a transformer in the plug in list
			//If this is not the case an indication that web.xml is not consistent is logged
			if( !getTransformerPlugIns().contains( s ) )
			{
				logger.warning("You Configured a file type: " + s + " that does not have a corresponding transformer plugged in");
			}			
			
		}

	}

	static public Map<String, String> getFileTypeToTransformerAssociation()
	{
		return typeToTransformerMap;
	}
	
	static public List<String> getTransformerPlugIns()
	{
		//Some sort of Singleton pattern within the method: the initialisation is made only once!
		if( transformerPlugIns == null)
		{
			transformerPlugIns = getTransformerPlugInsImpl();
		}
		
		return transformerPlugIns;
	}
	
	//Looks for valid transformer plug-in
	static private List<String> getTransformerPlugInsImpl()
	{
		List<String> plugInsL = new LinkedList<String>();
		try
		{
			
		File dir = new File(plugInPathCache);
	    
	    String[] children = dir.list();
	    if (children == null) {
	        // Either dir does not exist or is not a directory
	    	logger.warning("The directory for plugins: " + plugInPathCache + " does not exist");
	    } 
	    else
	    {
	    	if( 0 == children.length)
	    	{
		    	logger.warning("There are no transformer plugins available");
	    	}
	    	else
	    	{     
		        String className = "";
		        
				for( String s : children )
				{
					//Test if plugin is instantiable and respects defined interface
			       try {
			   	    		    	   	
			    	    String[] subClassName = s.split("\\.");
			    	    
			    		ClassLoader cl = Thread.currentThread().getContextClassLoader(); 

			    		//add required jar to classloader
			    		logger.info("Loading: " + plugInPathCache+s ); 
			    	   	ClassPathHacker.addFile(plugInPathCache+s, cl); 
			    		
			    		className = "eu.bauel.publish.transformation.plugin."+subClassName[0]; 
			    	   	System.out.println("using the classloader:"+cl.toString());    		
			    		System.out.println("try load :"+className);
			    	   	Class myclass = Class.forName(className, true, cl); 
			    	   	System.out.println(className+" sucessfully loaded");
			    	   	//cld.getResource('path/to/the/class/file.class');
			    	   	System.out.println("loaded :"+className); 

			    	   	
			            //Use reflection to list methods and invoke them
			            Method[] methods = myclass.getMethods();
			            Object object = myclass.newInstance();
			            
			            boolean isPlugInImplementingTransformerAdapter = false;
			            for (int i = 0; i < methods.length; i++) {
			            	// transformDataset is a method of the interface TransformerAdapter that every plug-in MUST implement
			            	logger.info("recognized methods:" +methods[i].getName());
			            	if (methods[i].getName().startsWith("transformDataset")) {
			                   //System.out.println(methods[i].invoke(object));
								//NB this may have a run time cost if many plugins are present
								plugInsL.add(className);
								logger.info(className + " is recognized has a valid transformer plugin");
								isPlugInImplementingTransformerAdapter = true;
								break;
			                }
			            }
		                
			            if( false == isPlugInImplementingTransformerAdapter )
			            {
			            	logger.warning( className + " is NOT recognized has a transformer plugin");
			            }
			            
			        } catch (Exception ex) {
						logger.info(className + " is NOT recognized has a transformer plugin");
						System.out.println(ex.getMessage());
			            //ex.printStackTrace();
				        }
					}
		    	}
			}
		}
		catch(Exception e)
		{
			logger.warning( e.getMessage() );
		}
		return plugInsL;
	}

	
	/**
	 * A method that instantiate a transformer from the plug in list
	 * @param plugInName
	 * @return
	 */
	public static ITransformerAdapter transformerFactory( String plugInName )
	{
		//use reflection to instanciate the transformer
	   	//String className = "eu.bauel.publish.publication."+ plugInName;
		String className = plugInName;
		logger.info(className + " will be loaded");
	   	Object object = null;

	   	Class myclass;
		try {
			ClassLoader cl = Thread.currentThread().getContextClassLoader();
			myclass = Class.forName( className, true, cl );
			object = myclass.newInstance();
		} catch (ClassNotFoundException e) {
			e.printStackTrace();
		} catch (InstantiationException e) {
			e.printStackTrace();
		} catch (IllegalAccessException e) {
			e.printStackTrace();
		}
        
        return (ITransformerAdapter)object;
	}

}

