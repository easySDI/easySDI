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

package org.easysdi.proxy.core;

import java.io.IOException;
import java.io.OutputStream;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.security.Principal;
import java.util.Collections;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBException;
import net.sf.ehcache.Cache;
import net.sf.ehcache.CacheManager;
import net.sf.ehcache.Element;
import org.easysdi.proxy.csw.CSWExceptionReport;
import org.easysdi.proxy.exception.PolicyNotFoundException;
import org.easysdi.proxy.exception.ProxyServletException;
import org.easysdi.proxy.exception.VersionNotSupportedException;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.ows.v200.OWS200ExceptionReport;
import org.easysdi.proxy.wfs.WFSExceptionReport;
import org.easysdi.proxy.wms.WMSExceptionReport;
import org.easysdi.proxy.wms.v130.WMSExceptionReport130;
import org.easysdi.proxy.wmts.v100.WMTSExceptionReport100;
import org.easysdi.proxy.policy.Policy;
import org.easysdi.proxy.security.JoomlaProvider;
import org.easysdi.xml.documents.Config;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.context.ApplicationContext;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.web.context.support.WebApplicationContextUtils;

/**
 * Reads the configuration file and dispatch the request to the right class.
 * 
 * @author Administrateur
 * 
 */
public class OgcProxyServlet extends HttpServlet {

	/**
	 * 
	 */
	private static final long serialVersionUID = 5619994356764480389L;


	private Cache configCache;
	private Config configuration;
	public String configFile;
	private JoomlaProvider joomlaProvider;
	private Logger logger = LoggerFactory.getLogger("OgcProxyServlet");
	private HttpServletResponse servletResponse;

	public static HashMap<String, Double> executionCount = new HashMap<String, Double>();


	/* (non-Javadoc)
	 * @see javax.servlet.GenericServlet#init(javax.servlet.ServletConfig)
	 */
	public void init(ServletConfig config) throws ServletException {
		super.init(config);
		configFile = config.getInitParameter("configFile");
		ApplicationContext context = WebApplicationContextUtils.getWebApplicationContext(getServletContext());
		CacheManager cm = (CacheManager) context.getBean("cacheManager");
		joomlaProvider = (JoomlaProvider) context.getBean("joomlaProvider");

		if (cm != null) {
			configCache = cm.getCache("configCache");
		}
		System.setProperty("org.geotools.referencing.forceXY", "true");
		logger.info("OgcProxyServlet initialization done.");
	}


	/* (non-Javadoc)
	 * @see javax.servlet.http.HttpServlet#doGet(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
	 */
	public void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		servletResponse = resp;
		ProxyServlet obj = null;

		try {
			obj = createProxy(req.getPathInfo().substring(1), req, resp);
			double maxRequestNumber = -1;
			if (obj != null)
				maxRequestNumber = obj.getConfiguration().getMaxRequestNumber();

			waitWhenConnectionsExceed(req, maxRequestNumber);

			if (obj != null) {
				obj.doGet(req, resp);
			}
		} catch (Exception e) {
			logger.error("Error occured processing doGet: "+e.getMessage());
			StringBuffer out = new OWS200ExceptionReport().generateExceptionReport(e.getMessage(), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, null) ;
			servletResponse.setContentType("text/xml");
			servletResponse.setContentLength(out.length());
			OutputStream os;
			try {
				os = servletResponse.getOutputStream();
				os.write(out.toString().getBytes());
				os.flush();
				os.close();
			} catch (IOException ex) {
				logger.error("Error occured processing doGet: ",ex);
			} 
		} finally {
			/**
			 * What ever happens, always decrease the connection number when
			 * finished.
			 */
			if (obj != null)
				decreaseConnections(req, obj.getConfiguration().getMaxRequestNumber());
		}
	}

	/* (non-Javadoc)
	 * @see javax.servlet.http.HttpServlet#doPost(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
	 */
	public void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		servletResponse = resp;
		ProxyServlet obj = null;
		try {
			obj = createProxy(req.getPathInfo().substring(1), req, resp);
			double maxRequestNumber = -1;
			if (obj != null)
				maxRequestNumber = obj.getConfiguration().getMaxRequestNumber();

			waitWhenConnectionsExceed(req, maxRequestNumber);

			if (obj != null) {
				obj.doPost(req, resp);

			}
		} catch (Exception e) {
			logger.error("Error occured processing doPost: "+e.getMessage());
			StringBuffer out = new OWS200ExceptionReport().generateExceptionReport(e.toString(), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, null) ;
			resp.setContentType("text/xml");
			resp.setContentLength(out.length());
			OutputStream os;
			try {
				os = resp.getOutputStream();
				os.write(out.toString().getBytes());
				os.flush();
				os.close();
			} catch (IOException ex) {
				logger.error("Error occured processing doPost: ",ex);
			} 
		} finally {
			/**
			 * What ever happens, always decrease the connection number when
			 * finished.
			 */
			if (obj != null)
				decreaseConnections(req, obj.getConfiguration().getMaxRequestNumber());
		}
	}

	/***
	 * Instanciate a ProxyServlet according to the configuration file.
	 * 
	 * @param servletName
	 * @return
	 * @throws JAXBException
	 */
	private ProxyServlet createProxy(String servletName, HttpServletRequest req, HttpServletResponse resp) throws JAXBException {
		// L'existance de la config et des policies dans le cache est assuré par
		// un servlet filter déclaré dans spring.
		// Voir org.easysdi.proxy.core.EasySdiConfigFilter.java
		try {
			Element configE = configCache.get(servletName + "configFile");
			if (configE == null){
				logger.error(servletName + " config not found ! Servlet can not be created.");
				return null;
			}

			configuration = (Config) configE.getValue();

			//Get the servlet class name define in the config file
			String className = configuration.getServletClass();

			//Parse the request into a ProxyServletRequestObject
			ProxyServletRequest request = null;
			String requestClassName;
			try{
				requestClassName = className + "Request";
				Class<?> requestClasse = Class.forName(requestClassName);
				Constructor<?> requestConstructeur = requestClasse.getConstructor(new Class [] {Class.forName ("javax.servlet.http.HttpServletRequest")});
				request = (ProxyServletRequest) requestConstructeur.newInstance(req);
			} catch (ClassNotFoundException e) {
				//Class request does not exist, keep going on (it can be the case for old version connector)
			} catch (NoSuchMethodException e) {
				//The constructor does not exist, keep going on
			} catch (InstantiationException e) {
				//Problem parsing the request
				logger.error("Problem parsing request in OgcProxyServlet: ",e);
				sendException(new ProxyServletException("Problem parsing request: "+e.toString()), configuration.getServletClass(), configuration.getRequestNegotiatedVersion(request.getVersion(),request.getService()));
				return null;
			} catch (IllegalAccessException e) {
				//Problem parsing the request
				logger.error("Problem parsing request in OgcProxyServlet: ",e);
				sendException(new ProxyServletException("Problem parsing request: "+e.toString()), configuration.getServletClass(), configuration.getRequestNegotiatedVersion(request.getVersion(),request.getService()));
				return null;
			} catch (java.lang.reflect.InvocationTargetException e) {
				//Problem parsing the request
				logger.error("Problem parsing request in OgcProxyServlet: ",e);
				sendException(new ProxyServletException("Problem parsing request: "+e.getCause().getCause().toString()), configuration.getServletClass(), configuration.getRequestNegotiatedVersion(request.getVersion(),request.getService()));
				return null;
			} catch (IllegalArgumentException e) {
				//Problem parsing the request
				logger.error("Problem parsing request in OgcProxyServlet: ",e);
				sendException(new ProxyServletException("Problem parsing request: "+e.toString()), configuration.getServletClass(), configuration.getRequestNegotiatedVersion(request.getVersion(),request.getService()));
				return null;
			} catch (ProxyServletException e) {
				logger.error("ProxyServletException in OgcProxyServlet: ",e);
				sendException(e, configuration.getServletClass(), null);
				return null;
			}

			//Get the correct CSWProxyServlet class name according to the EasySDI version currently running
			if (className.equalsIgnoreCase("org.easysdi.proxy.csw.CSWProxyServlet") && joomlaProvider.getVersion() != null
					&& Integer.parseInt(joomlaProvider.getVersion()) >= 200) {
				className += "2";
			}

			//Add the version to get the complete class name
			String reqVersion= null;
			if(request != null){
				if(request.getOperation().equalsIgnoreCase("GetCapabilities")){
					reqVersion = configuration.getRequestNegotiatedVersion(request.getVersion(), request.getService());
				}else{
					List<String> supportedVersions =  configuration.getSupportedVersions();
					if(!supportedVersions.contains(request.getVersion())){
						//requested version not supported
						//Send back an OGC Exception
						sendException( new VersionNotSupportedException(request.getVersion()),configuration.getServletClass(), configuration.getRequestNegotiatedVersion(request.getVersion(),request.getService()));
						return null;
					}else{
						reqVersion = request.getVersion();
					}
				}
				request.setVersion(reqVersion);
				className += reqVersion.replaceAll("\\.", "");
			}

			//Instantiate the servlet class
			Class<?> classe = Class.forName(className);
			Constructor<?> constructeur = classe.getConstructor();
			ProxyServlet ps = (ProxyServlet) constructeur.newInstance();
			//Set the configuration
			try {
				ps.setConfiguration(configuration);
			} catch (Exception e) {
				logger.error("Problem occured in configuration parsing and/or logging settings.",e);
				sendException(new ProxyServletException("Problem occured in configuration parsing and/or logging settings."), configuration.getServletClass(), null);
				return null;
			}
			//Set the request object
			ps.setProxyRequest(request);

			// Use to access EasySDI Joomla data
			ps.setJoomlaProvider(joomlaProvider);

			String user = null;
			Principal principal = SecurityContextHolder.getContext().getAuthentication();
			if (principal != null)
				user = principal.getName();
			Element policyE = configCache.get(servletName + user + "policyFile");

			if (policyE != null) {
				ps.setPolicy((Policy) policyE.getValue());
			} else {
				// If no policy found, return an OGC Exception and quit.
				//				ps.sendOgcExceptionBuiltInResponse(resp, ps.generateOgcException("No policy found.", "NoApplicableCode", null, ""));
				logger.error("No policy found!");
				sendException(new PolicyNotFoundException(PolicyNotFoundException.NO_POLICY_FOUND), configuration.getServletClass(), reqVersion);
			}
			logger.info("OgcProxyServlet Proxy Servlet creation done.");
			return ps;

		} catch (Exception e) {
			//Enable to instanciate the request proxy servlet class due to bad service or bad version parameter
			logger.error("Invalid service and/or version.",e);
			sendException(new ProxyServletException("Invalid service and/or version."), configuration.getServletClass(), null);
			return null;
		} 
	}

	/**
	 * @param e
	 * @param servletClass
	 * @param version
	 */
	public void sendException (Exception e, String servletClass, String version)
	{
		String errorMessage = "";
		String code = "";
		String locator = "";

		String t = e.getClass().getName();
		if(e.getMessage().contains("VersionNotSupportedException") || e.getClass().getName().contains("VersionNotSupportedException")){
			errorMessage = OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED;
			errorMessage += " : ";
			errorMessage += e.getMessage();
			code = OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE;
			locator = "version";
		}else if(e.getMessage().contains("InvalidServiceNameException") || e.getClass().getName().contains("InvalidServiceNameException")){
			errorMessage = OWSExceptionReport.TEXT_INVALID_SERVICE_NAME;
			code = OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE;
			locator = "service";
		}else {
			errorMessage = e.toString();
			code = OWSExceptionReport.CODE_NO_APPLICABLE_CODE;
			locator = null;
		}

		StringBuffer out = new StringBuffer() ;
		try {
			if(servletClass.equalsIgnoreCase("org.easysdi.proxy.wms.WMSProxyServlet")){
				Class<?> supportedClasse = Class.forName("org.easysdi.proxy.wms.v"+version.replace(".", "")+".WMSExceptionReport"+version.replace(".", ""));
				Constructor<?> supportedClasseConstructeur = supportedClasse.getConstructor(new Class [] {});
				WMSExceptionReport exceptionReport = (WMSExceptionReport) supportedClasseConstructeur.newInstance();
				out = exceptionReport.generateExceptionReport(errorMessage, code, locator);
			}else if (servletClass.equalsIgnoreCase("org.easysdi.proxy.wfs.WFSProxyServlet")){
				out = new WFSExceptionReport().generateExceptionReport(errorMessage, code, locator,version);
			}else if (servletClass.equalsIgnoreCase("org.easysdi.proxy.wmts.WMSTProxyServlet")){
				out = new WMTSExceptionReport100().generateExceptionReport(errorMessage, code, locator,version);
			}else if (servletClass.equalsIgnoreCase("org.easysdi.proxy.csw.CSWProxyServlet")){
				out = new CSWExceptionReport().generateExceptionReport(errorMessage, code, locator,version);
			}else{
				out = new OWS200ExceptionReport().generateExceptionReport(e.toString(), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, null) ;
			}

			servletResponse.setContentType("text/xml; charset=utf-8");
			servletResponse.setContentLength(out.length());

			OutputStream os;
			os = servletResponse.getOutputStream();
			os.write(out.toString().getBytes());
			os.flush();
			os.close();
			logger.error( "OgcProxyServlet sends exception", e.toString());
		} catch (Exception e1) {
			logger.error("Error occured trying to send exception to client.",e1);
		} 
	}
	/***
	 * Decrease the number of executed processes used to managed the
	 * max-request-number parameter
	 * 
	 * @param req
	 * @param maxRequestByUser
	 */
	private synchronized void decreaseConnections(HttpServletRequest req, double maxRequestByUser) {
		// can handle each incoming request
		if (maxRequestByUser < 0)
			return;
		// We should return an error message, because no request is allowed for
		// this server.
		if (maxRequestByUser == 0)
			return;
		// No user connected, cannot restrict the connections
		if (SecurityContextHolder.getContext().getAuthentication() == null)
			return;
		String userPrincipalName = SecurityContextHolder.getContext().getAuthentication().getName();
		if (userPrincipalName == null)
			return;

		Double executed = new Double(0);

		if (userPrincipalName != null) {
			Double d2 = null;

			d2 = executionCount.get(userPrincipalName);

			if (d2 == null) {
				d2 = new Double(0);
			}
			executed = new Double(d2);
		}
		executed--;
		executionCount.put(userPrincipalName, executed);
		synchronized (executionCount) {
			executionCount.notifyAll();
		}

	}

	/***
	 * When the number of executed processes is greater than the parameter
	 * max-request-number for a registered user then waits. The
	 * max-request-number parameter allows to restrict the number of requests
	 * executed in parallel by one user.
	 * 
	 * @param req
	 *            the HttpServletRequest
	 * @param maxRequestByUser
	 *            the number of allowed requests
	 */
	private void waitWhenConnectionsExceed(HttpServletRequest req, double maxRequestByUser) {
		// can handle each incoming request
		if (maxRequestByUser < 0)
			return;
		// We should return an error message, because no request is allowed for
		// this server.
		if (maxRequestByUser == 0)
			return;
		// No user connected, cannot restrict the connections
		if (SecurityContextHolder.getContext().getAuthentication() == null)
			return;
		String userPrincipalName = SecurityContextHolder.getContext().getAuthentication().getName();
		if (userPrincipalName == null)
			return;

		Double executed = new Double(0);
		synchronized (executionCount) {
			if (userPrincipalName != null) {
				Double d2 = null;

				d2 = executionCount.get(userPrincipalName);

				if (d2 == null) {
					d2 = new Double(0);
				}
				executed = new Double(d2);
			}
			while (executed > maxRequestByUser - 1) {

				try {
					/*
					 * Too many connections. Wait until one is released.
					 */
					executionCount.wait();

				} catch (InterruptedException e) {
					e.printStackTrace();
				}
				Double d2 = null;

				d2 = executionCount.get(userPrincipalName);

				if (d2 == null) {
					d2 = new Double(0);
				}
				executed = new Double(d2);

			}
			executed++;
			executionCount.put(userPrincipalName, executed);
		}
	}
}
