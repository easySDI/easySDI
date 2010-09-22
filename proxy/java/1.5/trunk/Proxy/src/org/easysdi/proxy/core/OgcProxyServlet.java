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

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.OutputStream;
import java.lang.reflect.Constructor;
import java.security.Principal;
import java.util.HashMap;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBException;

import net.sf.ehcache.Cache;
import net.sf.ehcache.CacheManager;
import net.sf.ehcache.Element;

import org.easysdi.proxy.exception.PolicyNotFoundException;
import org.easysdi.proxy.policy.Policy;
import org.easysdi.security.JoomlaProvider;
import org.easysdi.xml.documents.Config;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.context.ApplicationContext;
import org.springframework.web.context.support.WebApplicationContextUtils;
import org.xml.sax.SAXException;

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

	public static HashMap<String, Double> executionCount = new HashMap<String, Double>();

	public void init(ServletConfig config) throws ServletException {
		// Debug tb 28.09.2009
		super.init(config);
		// Fin de Debug
		configFile = config.getInitParameter("configFile");
		ApplicationContext context = WebApplicationContextUtils.getWebApplicationContext(getServletContext());
		CacheManager cm = (CacheManager) context.getBean("cacheManager");
		joomlaProvider = (JoomlaProvider) context.getBean("joomlaProvider");

		if (cm != null) {
			configCache = cm.getCache("configCache");
		}
		System.setProperty("org.geotools.referencing.forceXY", "true");
	}

	public void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		ProxyServlet obj = null;

		try {
			obj = createProxy(req.getPathInfo().substring(1), req, resp);
			waitWhenConnectionsExceed(req, obj.getConfiguration().getMaxRequestNumber());

			if (obj != null) {
				obj.doGet(req, resp);
			}
		} catch (PolicyNotFoundException e) {

		} catch (Exception e) {
			StringBuffer sb = generateOgcError(e.getMessage());
			e.printStackTrace();
			resp.setContentType("text/xml");
			resp.setContentLength(sb.length());
			OutputStream os = resp.getOutputStream();
			os.write(sb.toString().getBytes());
			os.flush();
			os.close();
		} finally {
			if (obj != null)
				decreaseConnections(req, obj.getConfiguration().getMaxRequestNumber());
		}

	}

	public void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		ProxyServlet obj = null;
		try {
			obj = createProxy(req.getPathInfo().substring(1), req, resp);
			waitWhenConnectionsExceed(req, obj.getConfiguration().getMaxRequestNumber());

			if (obj != null) {
				obj.doPost(req, resp);

			}
		} catch (PolicyNotFoundException e) {
			e.printStackTrace();

		} catch (Exception e) {
			e.printStackTrace();
			StringBuffer sb = generateOgcError(e.getMessage());
			resp.setContentType("text/xml");
			resp.setContentLength(sb.length());
			OutputStream os = resp.getOutputStream();
			os.write(sb.toString().getBytes());
			os.flush();
			os.close();
		} finally {
			/**
			 * What ever happens, always decrease the connection number when
			 * finished.
			 */
			if (obj != null)
				decreaseConnections(req, obj.getConfiguration().getMaxRequestNumber());
		}
	}

	private StringBuffer generateOgcError(String errorMessage) {
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>");
		sb
				.append("<ServiceExceptionReport xmlns=\"http://www.opengis.net/ogc\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengis.net/ogc\" version=\"1.2.0\">");
		sb.append("<ServiceException>");
		sb.append(errorMessage);
		sb.append("</ServiceException>");
		sb.append("</ServiceExceptionReport>");
		return sb;
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
		if (req.getUserPrincipal() == null)
			return;
		String userPrincipalName = req.getUserPrincipal().getName();
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
		if (req.getUserPrincipal() == null)
			return;
		String userPrincipalName = req.getUserPrincipal().getName();
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

	/***
	 * Instanciate a ProxyServlet according to the configuration file.
	 * 
	 * @param servletName
	 * @return
	 * @throws JAXBException
	 */
	private ProxyServlet createProxy(String servletName, HttpServletRequest req, HttpServletResponse resp) throws JAXBException, PolicyNotFoundException {
		// L'existance de la config et des policies dans le cache est assuré par
		// un servlet filter déclaré dans spring.
		// Voir org.easysdi.proxy.core.EasySdiConfigFilter.java
		try {
			// File configF = new File(configFile).getAbsoluteFile();
			// long lastmodified = configF.lastModified();
			logger.info("Looking for " + servletName + " config");
			Element configE = configCache.get(servletName + "configFile");
			if (configE == null)
				logger.error(servletName + " config not found !");
			// if (configE != null && configE.getVersion() != lastmodified)
			// configE = null;
			// if (configE == null) {
			// XMLReader xr = XMLReaderFactory.createXMLReader();
			// ConfigFileHandler confHandler = new
			// ConfigFileHandler(servletName);
			// Debug tb 28.09.2009
			// Dans le fichier web.xml, si le chemin d'accès au fichier
			// config.xml est donné en relatif à la servlet
			// InputStream is = new
			// java.io.FileInputStream(getServletContext().getRealPath(configFile));
			// Dans le fichier web.xml, si le chemin d'accès au fichier
			// config.xml est donné en absolu
			// InputStream is = new java.io.FileInputStream(configFile);
			// Fin de Debug
			// xr.setContentHandler(confHandler);
			// xr.parse(new InputSource(is));
			// configuration = confHandler.getConfig();
			// configE = new Element(servletName + "configFile", configuration);
			// configE.setVersion(lastmodified);
			// configCache.put(configE);
			// } else

			configuration = (Config) configE.getValue();

			String className = configuration.getServletClass();
			logger.info("Servlet " + className + " found for config " + servletName);
			if (className.equalsIgnoreCase("org.easysdi.proxy.csw.CSWProxyServlet") && joomlaProvider.getVersion() != null
					&& Integer.parseInt(joomlaProvider.getVersion()) >= 200) {
				className += "2";
			}

			// Class<?> classe = Class.forName(configuration.getServletClass());
			Class<?> classe = Class.forName(className);
			Constructor<?> constructeur = classe.getConstructor();
			ProxyServlet ps = (ProxyServlet) constructeur.newInstance();
			ps.setConfiguration(configuration);

			// Use to access EasySDI Joomla data
			ps.setJoomlaProvider(joomlaProvider);

			// String filePath = new
			// File(configuration.getPolicyFile()).getAbsolutePath();
			// File policyF = new File(filePath).getAbsoluteFile();
			// long plastmodified = policyF.lastModified();
			String user = null;
			Principal principal = req.getUserPrincipal();
			if (principal != null)
				user = principal.getName();
			Element policyE = configCache.get(servletName + user + "policyFile");
			// if (policyE != null && policyE.getVersion() != plastmodified)
			// policyE = null;
			// if (policyE == null) {

			// JAXBContext jc =
			// JAXBContext.newInstance(org.easysdi.proxy.policy.PolicySet.class);
			// Unmarshaller u = jc.createUnmarshaller();
			// Debug tb 28.09.2009
			// PolicySet policySet = (PolicySet) u.unmarshal(new
			// FileInputStream(getServletContext().getRealPath(configuration.getPolicyFile())));
			// PolicySet policySet = (PolicySet) u.unmarshal(new
			// FileInputStream(filePath));
			// Fin de Debug
			// PolicyHelpers ph = new PolicyHelpers(policySet, servletName);
			// Policy policy = ph.getPolicy(user, req);
			// ps.setPolicy(policy);
			// policyE = new Element(servletName + user + "policyFile", policy);
			// policyE.setVersion(plastmodified);
			// configCache.put(policyE);
			// } else

			if (policyE != null) {
				ps.setPolicy((Policy) policyE.getValue());
			} else {
				// If no policy found, return an OGC Exception and quit.
				ps.sendOgcExceptionBuiltInResponse(resp, ps.generateOgcError("No policy found.", "NoApplicableCode", null, ""));
				throw new PolicyNotFoundException(PolicyNotFoundException.NO_POLICY_FOUND);
			}
			return ps;

		} catch (ClassNotFoundException e) {
			e.printStackTrace();
			// La classe n'existe pas
		} catch (NoSuchMethodException e) {
			e.printStackTrace();
			// La classe n'a pas le constructeur recherché
		} catch (InstantiationException e) {
			e.printStackTrace();
			// La classe est abstract ou est une interface
		} catch (IllegalAccessException e) {
			e.printStackTrace();
			// La classe n'est pas accessible
		} catch (java.lang.reflect.InvocationTargetException e) {
			e.printStackTrace();
			// Exception déclenchée si le constructeur invoqué
			// a lui-même déclenché une exception
		} catch (IllegalArgumentException e) {
			e.printStackTrace();
			// Mauvais type de paramètre
			// (Pas obligatoire d'intercepter IllegalArgumentException)
		}
		return null;
	}
}
