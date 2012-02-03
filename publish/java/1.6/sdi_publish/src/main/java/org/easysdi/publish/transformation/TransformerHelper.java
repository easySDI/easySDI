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
package org.easysdi.publish.transformation;

import java.io.File;
import java.lang.reflect.Method;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;

import org.easysdi.publish.transformation.ITransformerAdapter;
import org.easysdi.publish.util.ClassPathHacker;
import org.easysdi.publish.util.TransformerPluginsLoader;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;


public class TransformerHelper {

	//The Map Containing The association between file type and 
	static Map<String, String> typeToTransformerMap = new HashMap<String, String>();
	static List<String> transformerPlugIns;
	static String plugInPathCache = "";
	static Logger logger = LoggerFactory.getLogger(TransformerHelper.class);
	
	//Inits the transformer plug-in. It will scan the plug-in path, look if the classes
	//contain a valid transformer.
	static public void init(String plugInPath )
	{
		logger.info("Loading plugin definitions in WEB-INF/TransformerPluginConfig.xml" );
	    logger.info("Scanning Plugin Path: " + plugInPath );
		plugInPathCache = plugInPath;
		String fileTypes = TransformerPluginsLoader.getInitParameter("SupportedTransformedFileTypes");
		String[] typeList = fileTypes.split(","); 
			
		for( String s : typeList )
		{
			typeToTransformerMap.put(s, TransformerPluginsLoader.getInitParameter(s) );
		}
		
		for( String s : typeToTransformerMap.values() )
		{
			//Check that every configured type have a transformer in the plug in list
			if( !getTransformerPlugIns().contains( s ) )
			{
				logger.warn("A file type: " + s + " is configured but its class: "+ TransformerPluginsLoader.getInitParameter(s)
						+" could not be found in: "+plugInPath+" either remove this entry from the config file or put a jar"
						+" containing this class into the plugin path");
			}			
			
		}

	}

	static public Map<String, String> getFileTypeToTransformerAssociation()
	{
		return typeToTransformerMap;
	}
	
	static public List<String> getTransformerPlugIns()
	{
		//Some sort of Singleton pattern within the method: the initialization is made only once!
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
	    	logger.warn("The directory for plugins: " + plugInPathCache + " does not exist");
	    } 
	    else
	    {
	    	if( 0 == children.length)
	    	{
		    	logger.warn("There are no transformer plugins available");
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
			    		
			    		className = "org.easysdi.publish.dat.transformation.plugin."+subClassName[0]; 
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
			            	logger.warn( className + " is NOT recognized as a valid transformer plugin");
			            }
			            
			        } catch (Exception ex) {
						logger.info(className + " is NOT recognized as a valid transformer plugin");
						System.out.println(ex.getMessage());
			            //ex.printStackTrace();
				        }
					}
		    	}
			}
		}
		catch(Exception e)
		{
			logger.warn( e.getMessage() );
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

