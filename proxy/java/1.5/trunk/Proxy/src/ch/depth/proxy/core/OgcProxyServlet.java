/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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

package ch.depth.proxy.core;


import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.HashMap;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Unmarshaller;

import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import ch.depth.proxy.policy.PolicySet;
import ch.depth.xml.documents.Config;
import ch.depth.xml.handler.ConfigFileHandler;

/**
 * Reads the configuration file and dispatch the request to the right class.
 * @author Administrateur
 *
 */
public class OgcProxyServlet extends HttpServlet {


    private Config configuration;
    public String configFile; 
    public static HashMap executionCount = new HashMap();

    public void init(ServletConfig config) throws ServletException {
//Debug tb 28.09.2009
    	super.init(config);
//Fin de Debug
	configFile = config.getInitParameter("configFile");
	  System.setProperty("org.geotools.referencing.forceXY", "true");
    }

    
 
    public void doGet(HttpServletRequest req, HttpServletResponse resp)
    throws ServletException, IOException {
	ProxyServlet obj = null;
		
	
	try{	   	    
	    obj = createProxy(req.getPathInfo().substring(1),req);
	    waitWhenConnectionsExceed(req,obj.getConfiguration().getMaxRequestNumber());	    

	    if (obj!=null ){				
		    obj.doGet(req, resp);		    
	    }
	}catch(Exception e){
	    StringBuffer sb = generateOgcError(e.getMessage());
	    e.printStackTrace();
	    resp.setContentType("text/xml");
	    resp.setContentLength(sb.length());
	    OutputStream os = resp.getOutputStream();
	    os.write(sb.toString().getBytes());
	    os.flush();
	    os.close();	    
	}
	finally{
	    if (obj!=null) decreaseConnections(req,obj.getConfiguration().getMaxRequestNumber());
	}


    }

    public void doPost(HttpServletRequest req, HttpServletResponse resp)    
    throws ServletException, IOException {
	ProxyServlet obj = null;
	try{
	    obj = createProxy(req.getPathInfo().substring(1),req);
	    waitWhenConnectionsExceed(req,obj.getConfiguration().getMaxRequestNumber());
	    
	    if (obj!=null ){		
		    obj.doPost(req, resp);	
		    
	    }}catch(Exception e){
		StringBuffer sb = generateOgcError(e.getMessage());
		resp.setContentType("text/xml");
		resp.setContentLength(sb.length());
		OutputStream os = resp.getOutputStream();
		os.write(sb.toString().getBytes());
		os.flush();
		os.close();
	    }
	    finally{
		/**
		 * What ever happens, always decrease the connection number when finished.
		 */
		if (obj!=null) decreaseConnections(req,obj.getConfiguration().getMaxRequestNumber());
	    }
    }



    private StringBuffer  generateOgcError(String errorMessage){
	StringBuffer sb = new StringBuffer ("<?xml version='1.0' encoding='utf-8' ?>");
	sb.append("<ServiceExceptionReport xmlns=\"http://www.opengis.net/ogc\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengis.net/ogc\" version=\"1.2.0\">");
	sb.append("<ServiceException>");
	sb.append(errorMessage);
	sb.append("</ServiceException>");
	sb.append("</ServiceExceptionReport>");
	return sb;  
    }
    /***
     * Decrease the number of executed processes
     * used to managed the max-request-number parameter 
     * @param req
     * @param maxRequestByUser
     */
    private synchronized void decreaseConnections(HttpServletRequest req,double maxRequestByUser){
	//can handle each incoming request
	if (maxRequestByUser <0 ) return;
	//We should return an error message, because no request is allowed for this server.
	if (maxRequestByUser == 0 ) return;
	//No user connected, cannot restrict the connections
	if (req.getUserPrincipal() == null) return;
	String userPrincipalName =req.getUserPrincipal().getName(); 	
	if (userPrincipalName == null) return;	

	Double executed = new Double(0); 	

	if ( userPrincipalName != null){
	    Double d2 = null;

	    d2 = (Double)executionCount.get(userPrincipalName);

	    if (d2==null){ 
		d2 =  new Double(0); 
	    }
	    executed= new Double(d2);
	}
	executed --;
	executionCount.put(userPrincipalName, executed);
	synchronized(executionCount){
	    executionCount.notifyAll();
	}

    }

    /***
     * When the number of executed processes is greater than the parameter max-request-number for a registered user then waits.
     * The max-request-number parameter allows to restrict the number of requests executed in parallel by one user. 
     * @param req the HttpServletRequest
     * @param maxRequestByUser the number of allowed requests
     */
    private void waitWhenConnectionsExceed(HttpServletRequest req,double maxRequestByUser){
	//can handle each incoming request
	if (maxRequestByUser <0 ) return;
	//We should return an error message, because no request is allowed for this server.
	if (maxRequestByUser == 0 ) return;
	//No user connected, cannot restrict the connections
	if (req.getUserPrincipal() == null) return;
	String userPrincipalName =req.getUserPrincipal().getName(); 	
	if (userPrincipalName == null) return;

	Double executed = new Double(0); 	
	synchronized (executionCount){
	    if ( userPrincipalName != null){
		Double d2 = null;

		d2 = (Double)executionCount.get(userPrincipalName);

		if (d2==null){ 
		    d2 =  new Double(0); 
		}
		executed= new Double(d2);
	    }	    
	    while (executed > maxRequestByUser-1) {

		try {
		    /*
		     * Too many connections. Wait until one is released. 
		     */
		    executionCount.wait();

		} catch (InterruptedException e) {
		    // TODO Auto-generated catch block
		    e.printStackTrace();
		}
		Double d2 = null;

		d2 = (Double)executionCount.get(userPrincipalName);

		if (d2==null){ 
		    d2 =  new Double(0); 
		}
		executed= new Double(d2);

	    }
	    executed ++;
	    executionCount.put(userPrincipalName, executed);
	}	
    }

    /***
     * Instanciate a ProxyServlet according to the configuration file. 
     * @param servletName
     * @return
     * @throws JAXBException 
     */
    private ProxyServlet createProxy(String servletName,HttpServletRequest req) throws JAXBException{
	try
	{	    
	    XMLReader xr = XMLReaderFactory.createXMLReader();
	    ConfigFileHandler confHandler = new ConfigFileHandler(servletName);
//Debug tb 28.09.2009
	    InputStream is = new java.io.FileInputStream(getServletContext().getRealPath(configFile));
//Fin de Debug
	    xr.setContentHandler(confHandler);
	    xr.parse(new InputSource(is));
	    configuration = confHandler.getConfig();

 	    
	    Class classe = Class.forName (configuration.getServletClass());
	    java.lang.reflect.Constructor constructeur = 
		classe.getConstructor ();
	    ProxyServlet ps = (ProxyServlet) constructeur.newInstance ();
	    ps.setConfiguration(configuration);

	    JAXBContext jc = JAXBContext.newInstance(ch.depth.proxy.policy.PolicySet.class);
	    Unmarshaller u = jc.createUnmarshaller();	  
//Debug tb 28.09.2009
	    PolicySet policySet = (PolicySet)u.unmarshal(new FileInputStream(getServletContext().getRealPath(configuration.getPolicyFile())));
//Fin de Debug
	    PolicyHelpers ph = new PolicyHelpers(policySet,servletName);
	    String user = null;
	    if (req.getUserPrincipal() !=null) user =req.getUserPrincipal().getName(); 
	    ps.setPolicy(ph.getPolicy(user, req));

	    return ps;

	}
	catch (ClassNotFoundException e)
	{
	    e.printStackTrace();
	    // La classe n'existe pas
	}
	catch (NoSuchMethodException e)
	{
	    e.printStackTrace();
	    // La classe n'a pas le constructeur recherché
	}
	catch (InstantiationException e)
	{
	    e.printStackTrace();
	    // La classe est abstract ou est une interface
	}
	catch (IllegalAccessException e)
	{
	    e.printStackTrace();
	    // La classe n'est pas accessible
	}
	catch (java.lang.reflect.InvocationTargetException e)
	{
	    e.printStackTrace();
	    // Exception déclenchée si le constructeur invoqué
	    // a lui-même déclenché une exception
	}
	catch (IllegalArgumentException e)
	{
	    e.printStackTrace();
	    // Mauvais type de paramètre
	    // (Pas obligatoire d'intercepter IllegalArgumentException)
	} catch (SAXException e) {
	    // TODO Auto-generated catch block
	    e.printStackTrace();
	} catch (FileNotFoundException e) {
	    // TODO Auto-generated catch block
	    e.printStackTrace();
	} catch (IOException e) {
	    // TODO Auto-generated catch block
	    e.printStackTrace();
	}
	return null;
    }
}
