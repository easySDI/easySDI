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

import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.net.HttpURLConnection;
import java.net.URI;
import java.net.URL;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Random;
import java.util.TreeMap;
import java.util.UUID;
import java.util.Vector;
import java.util.logging.Level;
import java.util.zip.GZIPInputStream;

import javax.naming.NoPermissionException;
import javax.net.ssl.SSLHandshakeException;
import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilderFactory;

import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.UsernamePasswordCredentials;
import org.apache.commons.httpclient.auth.AuthScope;
import org.apache.commons.httpclient.methods.GetMethod;
import org.apache.commons.httpclient.methods.PostMethod;
import org.apache.log4j.DailyRollingFileAppender;
import org.apache.log4j.Logger;
import org.apache.log4j.PatternLayout;
import org.easysdi.proxy.exception.AvailabilityPeriodException;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.policy.Attribute;
import org.easysdi.proxy.policy.AvailabilityPeriod;
import org.easysdi.proxy.policy.FeatureType;
import org.easysdi.proxy.policy.FeatureTypes;
import org.easysdi.proxy.policy.Layer;
import org.easysdi.proxy.policy.Operation;
import org.easysdi.proxy.policy.Policy;
import org.easysdi.proxy.policy.Server;
import org.easysdi.security.JoomlaProvider;
import org.easysdi.proxy.log.ProxyLogger;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.geotools.xml.DocumentFactory;
import org.geotools.xml.SchemaFactory;
import org.geotools.xml.XMLHandlerHints;
import org.jdom.Document;
import org.jdom.Namespace;
import org.jdom.input.SAXBuilder;
import org.springframework.security.core.context.SecurityContextHolder;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.ls.LSSerializer;

import com.google.common.collect.HashMultimap;
import com.google.common.collect.Multimap;


public abstract class ProxyServlet extends HttpServlet {

	

	private static final long serialVersionUID = 3499090220094877198L;
	private static final String PNG = "image/png";
	private static final String GIF = "image/gif";
	private static final String JPG = "image/jpg";
	private static final String JPEG = "image/jpeg";
	private static final String TIFF = "image/tiff";
	private static final String HTML = "text/html";
	private static final String PLAIN = "text/plain";
	private static final String BMP = "image/bmp";
	protected static final String XML = "text/xml";
	protected static final String APPLICATION_XML = "application/xml";
	protected static final String XML_OGC_WMS = "application/vnd.ogc.wms_xml";
	protected static final String XML_OGC_EXCEPTION = "application/vnd.ogc.se_xml";
	protected static final String SVG = "image/svg+xml";
	private List<String> temporaryFileList = new Vector();

	/**
	 * Configuration loaded to complete the request
	 */
	protected org.easysdi.xml.documents.Config configuration;

	/**
	 * Policy loaded
	 */
	public Policy policy;
	
	protected String requestCharacterEncoding = null;
	protected String responseContentType = null;
	protected List<String> responseContentTypeList = new ArrayList<String>();
	protected Integer responseStatusCode = HttpServletResponse.SC_OK;
	protected String bbox = null;
	protected String srsName = null;
	protected Map<Integer, String> wfsFilePathList = new TreeMap<Integer, String>();
	public Multimap<Integer, String> wmsFilePathList = HashMultimap.create();
		
	/**
	 * WMTS response files
	 */
	public Hashtable<String, String> wmtsFilePathTable = new Hashtable<String, String>();
	public Hashtable<String, String> ogcExceptionFilePathTable = new Hashtable<String, String>();
	
	/**
	 * List of the remote servers define in the config.xml
	 */
	public Hashtable<String, RemoteServerInfo> remoteServerInfoHashTable = new Hashtable <String, RemoteServerInfo>();

	/**
	 * Une liste des fichiers (sendData) réponse de chaque serveur WFS
	 */
	public Map<Integer, String> layerFilePathList = new TreeMap<Integer, String>();
	
	/**
	 * 
	 */
	protected Vector<String> featureTypePathList = new Vector<String>(); 
	
	/**
	 *  Contient	le featureTypetoKeep.get(0) (->reference pour le filtre remoteFilter) par Server
	 *  Debug tb 04.06.2009
	 */
	protected List<String> policyAttributeListToKeepPerFT = new Vector<String>();
	
	/**
	 * 
	 */
	protected int policyAttributeListNb = 0;
	
	/**
	 * Liste des fichiers réponses de chaque serveur qui contiennent des erreurs OGC
	 */
	protected Multimap<Integer, String> ogcExceptionFilePathList = HashMultimap.create();

	/**
	 * Store operations supported by the current version of the proxy
	 * Update this list to reflect proxy's capabilities
	 */
	public static List<String> ServiceSupportedOperations = Arrays.asList();
	
	/**
	 * Store all the operations define by the ogc norme for the specific service
	 */
	public static List<String> ServiceOperations = Arrays.asList();
	
	/**
	 * 
	 */
	private List<String> lLogs = new Vector<String>();
	
	/**
	 * 
	 */
	protected boolean hasPolicy = true;
	
	/**
	 * 
	 */
	protected OWSExceptionReport owsExceptionReport;
	
	/**
	 * Value of the version parameter received in the request
	 */
	protected String requestedVersion ;

	/**
	 * Use for accessing EasySDI Joomla data
	 */
	private JoomlaProvider joomlaProvider;
	
	/**
	 * Object representing the request
	 */
	protected ProxyServletRequest proxyRequest;
	
	public Logger logger;
	
	/**
	 * 
	 */
	public ProxyServlet() {
		super();

	}

	
	/**
	 * @param joomlaProvider the joomlaProvider to set
	 */
	protected void setJoomlaProvider(JoomlaProvider joomlaProvider) {
		this.joomlaProvider = joomlaProvider;
	}

	/**
	 * @return the joomlaProvider
	 */
	protected JoomlaProvider getJoomlaProvider() {
		return joomlaProvider;
	}
	
	/**
	 * @param proxyRequest the proxyRequest to set
	 */
	public void setProxyRequest(ProxyServletRequest proxyRequest) {
		this.proxyRequest = proxyRequest;
	}

	/**
	 * @return the proxyRequest
	 */
	public ProxyServletRequest getProxyRequest() {
		return proxyRequest;
	}

	/**
	 * Get the list of remote servers defined in the config.xml
	 * in a Hashtable mapping <alias,RemoteServerInfo>
	 * replace : getRemoteServerInfoList
	 * @return
	 */
	public Hashtable<String, RemoteServerInfo> getRemoteServerHastable() {
		
		if (configuration == null)
			return null;
		
		List<RemoteServerInfo> list = configuration.getRemoteServer();
		Iterator<RemoteServerInfo> iRS = list.iterator();
		while (iRS.hasNext())
		{
			RemoteServerInfo RS = iRS.next();
			remoteServerInfoHashTable.put(RS.getAlias(),RS);
		}
		return remoteServerInfoHashTable;
	}

	/**
	 * Get a remoteServerInfo by his alias
	 * Use Hastable remoteServerInfoHashTable
	 * Replace : getRemoteServerInfo()
	 * @param alias
	 * @return
	 */
	public RemoteServerInfo getRemoteServerInfo (String alias)
	{
		if(alias == null || alias == "")
			return null;
		
		if (remoteServerInfoHashTable.isEmpty())
		{
			remoteServerInfoHashTable = getRemoteServerHastable();
		}
		return remoteServerInfoHashTable.get(alias);
	}
	
	/**
	 * Get the remoteServerInfo defines as the master in the config.xml
	 * @return
	 */
	public RemoteServerInfo getRemoteServerInfoMaster()
	{
		if (remoteServerInfoHashTable.isEmpty())
		{
			remoteServerInfoHashTable = getRemoteServerHastable();
		}
		Iterator<Map.Entry<String, RemoteServerInfo>> i =  remoteServerInfoHashTable.entrySet().iterator();
		while (i.hasNext())
		{
			Map.Entry<String, RemoteServerInfo> entry = i.next();
			if(entry.getValue().isMaster)
				return entry.getValue();
		}
		return null;
	}
	
	/**
	 * Get the list of remote servers defined in the config.xml
	 * @deprecated
	 * @return
	 */
	protected List<RemoteServerInfo> getRemoteServerInfoList() {
		if (configuration == null)
			return null;

		return configuration.getRemoteServer();
	}
	

	/**
	 * Get a RemoteServerInfo by his index in the list
	 * @deprecated
	 * @param i
	 * @return
	 */
	protected RemoteServerInfo getRemoteServerInfo(int i) {
		if (configuration == null)
			return null;

		List<RemoteServerInfo> l = configuration.getRemoteServer();
		if (l != null && l.size() > 0) {
			return (RemoteServerInfo) l.get(i);
		}
		return null;
	}

	/**
	 * Get a RemoteServerInfo Url by is index in the list
	 * @deprecated
	 * @param i
	 * @return
	 */
	public String getRemoteServerUrl(int i) {
		if (configuration == null)
			return null;

		List<RemoteServerInfo> l = configuration.getRemoteServer();
		if (l != null && l.size() > 0) {
			return (String) ((RemoteServerInfo) l.get(i)).getUrl();
		}
		return null;
	}

	/**
	 * Get the policy file path
	 * @return file path
	 */
	protected String getPolicyFilePath() {
		return configuration.getPolicyFile();
	}

	
	/**
	 * Set the current config
	 * @param conf
	 * @throws ClassNotFoundException 
	 * @throws InvocationTargetException 
	 * @throws IllegalAccessException 
	 * @throws IllegalArgumentException 
	 * @throws NoSuchMethodException 
	 * @throws SecurityException 
	 */
	public void setConfiguration(org.easysdi.xml.documents.Config conf) throws ClassNotFoundException, IllegalArgumentException, IllegalAccessException, InvocationTargetException, SecurityException, NoSuchMethodException {
		configuration = conf;
		String loggerClassName = configuration.getClassLogger();
		org.apache.log4j.Level level = org.apache.log4j.Level.toLevel(configuration.getLogLevel()); 
		Class<?> classe;
		
		classe = Class.forName(loggerClassName);
		Method method = classe.getMethod("getLogger",new Class [] {Class.forName ("java.lang.String")} );
		logger = (Logger) method.invoke(null,new Object[]{"ProxyLogger"});
		logger.setLevel(level);
			
		
		if (logger instanceof ProxyLogger){
			((ProxyLogger)logger).setDateFormat(configuration.getLogDateFormat());
			((ProxyLogger)logger).setLogFile(configuration.getLogFile());
		}else{
			DailyRollingFileAppender appender = (DailyRollingFileAppender)logger.getAppender("logFileAppender");
			appender.setFile(configuration.getLogFile());
			//DatePattern receive the period rolling value
			if(configuration.getPeriod().equalsIgnoreCase("daily"))
				appender.setDatePattern("'.'yyyy-MM-dd");
			else if(configuration.getPeriod().equalsIgnoreCase("monthly"))
				appender.setDatePattern("'.'yyyy-MM");
			else if(configuration.getPeriod().equalsIgnoreCase("weekly"))
				appender.setDatePattern("'.'yyyy-ww");
			else 
				appender.setDatePattern("'.'yyyy-MM-dd");
			
			PatternLayout layout = (PatternLayout) appender.getLayout();
			String conversionPattern = layout.getConversionPattern();
			int start = conversionPattern.indexOf("%d{")+3;
			int end = conversionPattern.indexOf("}", start);
			
			String result = conversionPattern.substring(0,start) + configuration.getLogDateFormat() + conversionPattern.substring(end);
			layout.setConversionPattern(result);
		}
	}

	/**
	 * Get the policy file Search for the policy file path in attribute of the
	 * servlet : policy-file if not found search for the file
	 * policy-servletname.xml
	 * 
	 * @param req
	 * @return
	 */
	protected String getPolicyFile(ServletConfig config) {
		String policyFile = (String) config.getInitParameter("policy-file");
		if (policyFile == null)
			policyFile = "policy.xml";
		return policyFile;
	}

	/* (non-Javadoc)
	 * @see javax.servlet.http.HttpServlet#doPost(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
	 */
	public void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
		Date d = new Date();
		requestCharacterEncoding = req.getCharacterEncoding();
		
		try {
			logger.info("user="+SecurityContextHolder.getContext().getAuthentication().getName());
			logger.info("requestTime="+dateFormat.format(d));
			logger.info("RemoteAddr="+ req.getRemoteAddr());
			logger.info("RemoteUser="+ req.getRemoteUser());
			logger.info("QueryString="+ req.getQueryString());
			logger.info("RequestURL="+ req.getRequestURL().toString());
			
			requestPreTreatmentPOST(req, resp);
		} finally {
			deleteTempFileList();
			if (logger instanceof ProxyLogger){
				((ProxyLogger)logger).writeInLog(dateFormat.format(d), req);
			}
		}
	}

	/* (non-Javadoc)
	 * @see javax.servlet.http.HttpServlet#doGet(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
	 */
	public void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
		Date d = new Date();
		requestCharacterEncoding = req.getCharacterEncoding();
		
		try {
			logger.info("user="+SecurityContextHolder.getContext().getAuthentication().getName());
			logger.info("requestTime="+dateFormat.format(d));
			logger.info("RemoteAddr="+ req.getRemoteAddr());
			logger.info("RemoteUser="+ req.getRemoteUser());
			logger.info("QueryString="+ req.getQueryString());
			logger.info("RequestURL="+ req.getRequestURL().toString());

			requestPreTreatmentGET(req, resp);
		} finally {
			deleteTempFileList();
			if (logger instanceof ProxyLogger){
				((ProxyLogger)logger).writeInLog(dateFormat.format(d), req);
			}
		}
	}

	/**
	 * @param req
	 * @param resp
	 */
	protected abstract void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp);

	/**
	 * @param req
	 * @param resp
	 */
	protected abstract void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp);

	
	/**
	 * Aggregate exception files from remote servers in one single file
	 * The search for tags <ServiceExceptionReport> and <ServiceException> is valid for
	 * WMS version 1.1, 1.3
	 * WFS version 1.0
	 * 
	 *  WFS version 1.1 uses tags <ExceptionReport> and subTag <Exception>
	 */
	protected ByteArrayOutputStream buildResponseForOgcServiceException ()
	{
		try 
		{
			for (String path : ogcExceptionFilePathList.values()) 
			{
				DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
				db.setNamespaceAware(false);
				File fMaster = new File(path);
				org.w3c.dom.Document documentMaster = db.newDocumentBuilder().parse(fMaster);
				if (documentMaster != null) 
				{
					NodeList nl = documentMaster.getElementsByTagName("ServiceExceptionReport");
					if (nl.item(0) != null)
					{
						logger.trace("transform begin exception response writting");
						DOMImplementationLS implLS = null;
						if (documentMaster.getImplementation().hasFeature("LS", "3.0")) 
						{
							implLS = (DOMImplementationLS) documentMaster.getImplementation();
						} 
						else 
						{
							DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
							implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
						}
						
						Node ItemMaster = nl.item(0);
						//Loop on other file
						for (String pathChild : ogcExceptionFilePathList.values()) 
						{
							org.w3c.dom.Document documentChild = null;
							if(path.equals(pathChild))
								continue;
							
							documentChild = db.newDocumentBuilder().parse(pathChild);
							
							if (documentChild != null) {
								NodeList nlChild = documentChild.getElementsByTagName("ServiceException");
								if (nlChild != null && nlChild.getLength() > 0) 
								{
									for (int i = 0; i < nlChild.getLength(); i++) 
									{
										Node nnode = nlChild.item(i);
										nnode = documentMaster.importNode(nnode, true);
										ItemMaster.appendChild(nnode);
									}
								}
							}
						}
						ByteArrayOutputStream out = new ByteArrayOutputStream();
						LSSerializer serialiseur = implLS.createLSSerializer();
						LSOutput sortie = implLS.createLSOutput();
						sortie.setEncoding("UTF-8");
						sortie.setByteStream(out);
						serialiseur.write(documentMaster, sortie);
						logger.trace("transform end exception response writting");
						return out;
					}	
				}
			}
		}
		catch (Exception ex)
		{
			ex.printStackTrace();
			logger.error(ex.getMessage());
			return null;
		}
		return null;
	}
	/**
	 * Builds the url from the request
	 * 
	 * @param req
	 *            the HttpServletRequest request
	 * @return returns the url
	 */
	public String getServletUrl(HttpServletRequest req) {
		// http://hostname.com:80/mywebapp/servlet/MyServlet/a/b;c=123?d=789
		if (configuration.getHostTranslator() != null && configuration.getHostTranslator().length() > 0) {
			return configuration.getHostTranslator();
		}
		String scheme = req.getScheme(); // http
		String serverName = req.getServerName(); // hostname.com
		int serverPort = req.getServerPort(); // 80
		String contextPath = req.getContextPath(); // /mywebapp
		String servletPath = req.getServletPath(); // /servlet/MyServlet
		String pathInfo = req.getPathInfo(); // /a/b;c=123
		if(pathInfo.endsWith("?"))
			pathInfo = pathInfo.substring(0, pathInfo.length()-1);
		// String queryString = req.getQueryString(); // d=789

		String url = scheme + "://" + serverName + ":" + serverPort + contextPath + servletPath;
		if (pathInfo != null) {
			url += pathInfo;
		}

		return url;
	}

	

	

	/**
	 * Send to the client the ogc Exception build by the proxy
	 * @param resp
	 * @param ogcException
	 */
	public void sendOgcExceptionBuiltInResponse (HttpServletResponse resp,StringBuffer ogcException)
	{
		try {
			resp.setContentType("text/xml; charset=utf-8");
			resp.setContentLength(Integer.MAX_VALUE);
			
			OutputStream os;
			os = resp.getOutputStream();
			os.write(ogcException.toString().getBytes());
			os.flush();
			os.close();
		} catch (IOException e)
		{
			e.printStackTrace();
		}
		finally
		{
			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			Date d = new Date();
			logger.info("ClientResponseDateTime="+ dateFormat.format(d));
			logger.info("ClientResponseLength="+ ogcException.length());
		}
	}
	
	public void sendProxyBuiltInResponse (HttpServletResponse resp,StringBuffer xmlResponse)
	{
		try {
			resp.setContentType("text/xml; charset=utf-8");
			resp.setContentLength(Integer.MAX_VALUE);
			OutputStream os;
			os = resp.getOutputStream();
			os.write(xmlResponse.toString().getBytes());
			os.flush();
			os.close();
		} 
		catch (IOException e) 
		{
			e.printStackTrace();
		}
		finally
		{
			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			Date d = new Date();
			logger.info("ClientResponseDateTime="+ dateFormat.format(d));
			logger.info("ClientResponseLength="+ xmlResponse.length());
		}
	}
	/**
	 * Sends parameters to a remote server
	 * 
	 * @param method
	 *            GET or POST method
	 * @param urlstr
	 *            remote server url
	 * @param parameters
	 *            parameters to send to the remote server
	 * @return a String containing the path to the file containing the response
	 *         from the remote server
	 * @throws IOException
	 */

	public String sendData(String method, String urlstr, String parameters) {
		try {
			if (urlstr != null) {
				if (urlstr.endsWith("?")) {
					urlstr = urlstr.substring(0, urlstr.length() - 1);
				}
			}
			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			Date d = new Date();
			logger.info("RemoteRequestUrl="+ urlstr);
			logger.info("RemoteRequest="+ parameters.replaceAll("\r", ""));
			logger.info("RemoteRequestLength="+ parameters.length());
			logger.info("RemoteRequestDateTime="+ dateFormat.format(d));
			String cookie = null;

			if (getLoginService(urlstr) != null) {
				cookie = geonetworkLogIn(getLoginService(urlstr));
			}

			String encoding = null;

			if (getUsername(urlstr) != null && getPassword(urlstr) != null) {
				String userPassword = getUsername(urlstr) + ":" + getPassword(urlstr);
				encoding = new sun.misc.BASE64Encoder().encode(userPassword.getBytes());

			}

			if (method.equalsIgnoreCase("GET")) {
				if (!urlstr.contains("?"))
					urlstr = urlstr + "?" + parameters;
				else
					urlstr = urlstr+ "&" + parameters;
			}
			URL url = new URL(urlstr);
			HttpURLConnection hpcon = null;

			hpcon = (HttpURLConnection) url.openConnection();
			hpcon.setRequestMethod(method);
			if (cookie != null) {
				hpcon.addRequestProperty("Cookie", cookie);
			}
			if (encoding != null) {
				hpcon.setRequestProperty("Authorization", "Basic " + encoding);
			}
			hpcon.setUseCaches(false);
			hpcon.setDoInput(true);

			if (method.equalsIgnoreCase("POST")) {
				hpcon.setRequestProperty("Content-Length", "" + Integer.toString(parameters.getBytes().length));
				String contentType = XML;
				if(requestCharacterEncoding != null)
					contentType += "; " +requestCharacterEncoding;
				hpcon.setRequestProperty("Content-Type", contentType);

				hpcon.setDoOutput(true);
				DataOutputStream printout = new DataOutputStream(hpcon.getOutputStream());
				printout.writeBytes(parameters);
				printout.flush();
				printout.close();
			} else {
				hpcon.setDoOutput(false);
			}

			// getting the response is required to force the request, otherwise
			// it might not even be sent at all
			InputStream in = null;
   
			if(hpcon.getResponseCode() >= 400)
			{
				in = hpcon.getErrorStream();
				responseStatusCode = hpcon.getResponseCode();
			}
			else
			{
				if (hpcon.getContentEncoding() != null && hpcon.getContentEncoding().indexOf("gzip") != -1) 
				{
					in = new GZIPInputStream(hpcon.getInputStream());
					logger.trace( "return of the remote server is zipped");
				} else 
				{
					in = hpcon.getInputStream();
				}
			}

			responseContentType = hpcon.getContentType();
			String responseExtensionContentType=null;
			if(hpcon.getContentType() != null)
			{
				responseExtensionContentType = hpcon.getContentType().split(";")[0];
				responseContentTypeList.add(responseExtensionContentType);
			}
			
			 
			String tmpDir = System.getProperty("java.io.tmpdir");
			logger.debug(" tmpDir :  " + tmpDir);

			File tempFile = createTempFile("sendData_" + UUID.randomUUID().toString(), getExtension(responseExtensionContentType));

			FileOutputStream tempFos = new FileOutputStream(tempFile);

			byte[] buf = new byte[in.available()];
			int nread;
			while ((nread = in.read(buf)) != -1) {
				tempFos.write(buf, 0, nread);
			}

			tempFos.flush();
			tempFos.close();
			in.close();
			logger.info("RemoteResponseToRequestUrl="+ urlstr);
			logger.info("RemoteResponseLength="+ tempFile.length());

			dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			d = new Date();
			logger.info("RemoteResponseDateTime="+ dateFormat.format(d));
			return tempFile.toString();

		}catch (SSLHandshakeException e)
		{
			e.printStackTrace();
			logger.error("Unable to find valid certification. "+e.getCause().toString());
			return null;
			
		}
		catch (Exception e) {
			e.printStackTrace();
			return null;
		}
	}

	/**
	 * Send directly to client the remote response
	 * @param resp
	 * @param method
	 * @param sUrl
	 * @param parameters
	 * @throws Exception
	 */
	public void sendDataDirectStream(HttpServletResponse resp, String method, String sUrl, String parameters) throws Exception{
		
			if (sUrl != null) {
				if (sUrl.endsWith("?")) {
					sUrl = sUrl.substring(0, sUrl.length() - 1);
				}
			}
			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			Date d = new Date();
			logger.info("RemoteRequestUrl="+ sUrl);
			logger.info("RemoteRequest="+ parameters.replaceAll("\r", ""));
			logger.info("RemoteRequestLength="+ parameters.length());
			logger.info("RemoteRequestDateTime="+ dateFormat.format(d));
			String cookie = null;

			if (getLoginService(sUrl) != null) {
				cookie = geonetworkLogIn(getLoginService(sUrl));
			}

			String encoding = null;
			if (getUsername(sUrl) != null && getPassword(sUrl) != null) {
				String userPassword = getUsername(sUrl) + ":" + getPassword(sUrl);
				encoding = new sun.misc.BASE64Encoder().encode(userPassword.getBytes());
			}

			if (method.equalsIgnoreCase("GET")) {
				if (!sUrl.contains("?"))
					sUrl = sUrl + "?" + parameters;
				else
					sUrl = sUrl+ "&" + parameters;
			}
			URL url = new URL(sUrl);
			HttpURLConnection hpcon = null;

			hpcon = (HttpURLConnection) url.openConnection();
			hpcon.setRequestMethod(method);
			if (cookie != null) {
				hpcon.addRequestProperty("Cookie", cookie);
			}
			if (encoding != null) {
				hpcon.setRequestProperty("Authorization", "Basic " + encoding);
			}
			hpcon.setUseCaches(false);
			hpcon.setDoInput(true);

			if (method.equalsIgnoreCase("POST")) {
				hpcon.setRequestProperty("Content-Length", "" + Integer.toString(parameters.getBytes().length));
				String contentType = XML;
				if(requestCharacterEncoding != null)
					contentType += "; " +requestCharacterEncoding;
				hpcon.setRequestProperty("Content-Type", contentType);
				hpcon.setDoOutput(true);
				DataOutputStream printout = new DataOutputStream(hpcon.getOutputStream());
				printout.writeBytes(parameters);
				printout.flush();
				printout.close();
			} else {
				hpcon.setDoOutput(false);
			}
			
			InputStream in = null;
			int code = hpcon.getResponseCode();
			if( code >= 400)
			{
				in = hpcon.getErrorStream();
			}
			else
			{
				in = hpcon.getInputStream();
			}
//			try {
//				in = hpcon.getInputStream();
//			} catch (IOException e) {
//				in = hpcon.getErrorStream();
//			}
			
			resp.setContentType(hpcon.getContentType());
			resp.setStatus(hpcon.getResponseCode());
			resp.setContentLength(hpcon.getContentLength());
			
			
			//BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());
			OutputStream os = resp.getOutputStream();
			
			//in can be null.eg : when response code is >= 400, the error stream can be null
			if(in == null){
				os.flush();
				os.close();
			}else{
				byte[] buf = new byte[in.available()];
				int nread;
				while ((nread = in.read(buf)) != -1) {
					os.write(buf, 0, nread);
					os.flush();
				}
				os.close();
				in.close();
			}
			
			
			logger.info("RemoteResponseToRequestUrl="+ sUrl);
			dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			d = new Date();
			logger.info("RemoteResponseDateTime="+ dateFormat.format(d));
	}
	
	protected String geonetworkLogIn(String loginServiceUrl) {

		String cookie = null;
		// dump("SYSTEM","LoginServiceUrl",loginServiceUrl);
		try {
			URL urlLoginService = new URL(loginServiceUrl);
			HttpURLConnection hpconLoginService = null;
			hpconLoginService = (HttpURLConnection) urlLoginService.openConnection();
			hpconLoginService.setRequestMethod("GET");
			hpconLoginService.setUseCaches(false);
			hpconLoginService.setDoInput(true);
			hpconLoginService.setDoOutput(false);

			BufferedReader inLoginService = new BufferedReader(new InputStreamReader(hpconLoginService.getInputStream()));
			String inputLoginService;
			cookie = hpconLoginService.getHeaderField("Set-Cookie");

			while ((inputLoginService = inLoginService.readLine()) != null) {
				// dump(inputLoginService);
			}
			inLoginService.close();
		} catch (Exception e) {
			e.printStackTrace();
		}

		return cookie;

	}

	/*
	 * Send a file using mulipart/form-data
	 * 
	 * @param urlstr the url where to send
	 * 
	 * @param filePath the path of the file to send
	 * 
	 * @param loginServiceUrl Url to log in If needed
	 * 
	 * @param parameterName The name of the file paramater
	 * 
	 * @param parameterFileName The name of the file that will be published in
	 * the request
	 */
	protected StringBuffer sendFile(String urlstr, String filePath, String loginServiceUrl, String parameterName, String parameterFileName) {
		try {
			InputStream is = new FileInputStream(filePath);
			return sendFile(urlstr, is, loginServiceUrl, parameterName, parameterFileName);
		} catch (Exception e) {
			e.printStackTrace();
		}
		return new StringBuffer();
	}

	protected StringBuffer sendFile(String urlstr, InputStream in, String loginServiceUrl, String parameterName, String parameterFileName) {

		try {
			String cookie = null;
			if (loginServiceUrl != null) {
				cookie = geonetworkLogIn(loginServiceUrl);
			}

			URL url = new URL(urlstr);
			HttpURLConnection hpcon = null;

			hpcon = (HttpURLConnection) url.openConnection();
			hpcon.setRequestMethod("POST");
			hpcon.addRequestProperty("Cookie", cookie);

			hpcon.setUseCaches(false);
			hpcon.setDoInput(true);
			hpcon.setDoOutput(true);

			Random random = new Random();

			String boundary = "---------------------------" + Long.toString(random.nextLong(), 36) + Long.toString(random.nextLong(), 36)
					+ Long.toString(random.nextLong(), 36);
			hpcon.setRequestProperty("Content-Type", "multipart/form-data; boundary=" + boundary);

			hpcon.connect();
			DataOutputStream os = new DataOutputStream(hpcon.getOutputStream());

			String s1 = "--" + boundary + "\r\ncontent-disposition: form-data; name=\"" + parameterName + "\"; filename=\"" + parameterFileName
					+ "\"\r\nContent-Type: application/octet-stream\r\n\r\n";

			os.writeBytes(s1);

			byte[] buf = new byte[32768];
			int nread;
			synchronized (in) {
				while ((nread = in.read(buf, 0, buf.length)) >= 0) {
					os.write(buf, 0, nread);
				}
			}
			os.flush();

			String boundar = "\r\n--" + boundary + "--";
			os.writeBytes(boundar);
			buf = null;
			os.close();
			in = null;
			if (hpcon.getContentEncoding() != null && hpcon.getContentEncoding().indexOf("gzip") != -1) {
				in = new GZIPInputStream(hpcon.getInputStream());
			} else {
				in = hpcon.getInputStream();
			}
			BufferedReader br = new BufferedReader(new InputStreamReader(in));

			String input;
			StringBuffer response = new StringBuffer();
			while ((input = br.readLine()) != null) {
				response.append(input);
			}
			return response;
		} catch (Exception e) {
			e.printStackTrace();
		}
		return new StringBuffer();
	}

	protected StringBuffer sendFile(String urlstr, StringBuffer param, String loginServiceUrl) {

		try {
			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			Date d = new Date();
			logger.info("RemoteRequestUrl="+ urlstr);
			logger.info("RemoteRequest="+ param.toString());
			logger.info("RemoteRequestLength="+ param.length());
			logger.info("RemoteRequestDateTime="+ dateFormat.format(d));
			
			HttpClient client = new HttpClient();

			PostMethod post = new PostMethod(urlstr);
			post.addRequestHeader("Content-Type", "application/xml");
			post.addRequestHeader("Charset", "UTF-8");
			post.setRequestBody(new ByteArrayInputStream(param.toString().getBytes("UTF-8")));

			GetMethod loginGet = new GetMethod(loginServiceUrl);
			client.executeMethod(loginGet);
			client.executeMethod(post);
			StringBuffer response = new StringBuffer(post.getResponseBodyAsString());

			logger.info("RemoteResponseToRequestUrl="+ urlstr);
			logger.info("RemoteResponseLength="+ response.length());

			dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			d = new Date();
			logger.info("RemoteResponseDateTime="+ dateFormat.format(d));
			
			return response;
		} catch (Exception e) {
			e.printStackTrace();
		}
		return new StringBuffer();
	}

	protected StringBuffer send(String urlstr, String loginServiceUrl) {
		try {

			String cookie = null;
			if (loginServiceUrl != null) {
				cookie = geonetworkLogIn(loginServiceUrl);
			}

			URL url = new URL(urlstr);
			HttpURLConnection hpcon = null;

			hpcon = (HttpURLConnection) url.openConnection();
			hpcon.setRequestMethod("GET");
			if (cookie != null) {
				hpcon.addRequestProperty("Cookie", cookie);
			}

			hpcon.setUseCaches(false);
			hpcon.setDoInput(true);

			hpcon.setDoOutput(false);

			// getting the response is required to force the request, otherwise
			// it might not even be sent at all
			InputStream in = null;

			if (hpcon.getContentEncoding() != null && hpcon.getContentEncoding().indexOf("gzip") != -1) {
				in = new GZIPInputStream(hpcon.getInputStream());
			} else {
				in = hpcon.getInputStream();
			}

			BufferedReader br = new BufferedReader(new InputStreamReader(in));

			String input;
			StringBuffer response = new StringBuffer();
			while ((input = br.readLine()) != null) {
				response.append(input);
			}
			return response;

		} catch (Exception e) {
			e.printStackTrace();
		}
		return new StringBuffer();
	}

	
	protected boolean isAcceptingTransparency(String responseContentType) {
		boolean isTransparent = false;
		if (responseContentType == null)
			return true;
		if (isXML(responseContentType)) {
			isTransparent = false;
		} else if (responseContentType.startsWith(PNG)) {
			isTransparent = true;
		} else if (responseContentType.startsWith(SVG)) {
			isTransparent = true;
		} else if (responseContentType.startsWith(GIF)) {
			isTransparent = true;
		} else if (responseContentType.startsWith(JPG)) {
			isTransparent = false;
		} else if (responseContentType.startsWith(JPEG)) {
			isTransparent = false;
		} else if (responseContentType.startsWith(TIFF)) {
			isTransparent = true;
		} else if (responseContentType.startsWith(BMP)) {
			isTransparent = false;
		} else {
			logger.debug("unkwnon content type" + responseContentType);
		}

		return isTransparent;
	}

	/**
	 * @param responseContentType2
	 * @return
	 */
	protected String getExtension(String responseContentType) {
		String ext = ".unk";
		if (responseContentType == null)
			return ext;
		if (isXML(responseContentType)) {
			ext = ".xml";
		} else if (responseContentType.startsWith(PNG)) {
			ext = ".png";
		} else if (responseContentType.startsWith(SVG)) {
			ext = ".svg";
		} else if (responseContentType.startsWith(GIF)) {
			ext = ".gif";
		} else if (responseContentType.startsWith(JPG)) {
			ext = ".jpg";
		} else if (responseContentType.startsWith(JPEG)) {
			ext = ".jpeg";
		} else if (responseContentType.startsWith(TIFF)) {
			ext = ".tiff";
		} else if (responseContentType.startsWith(BMP)) {
			ext = ".bmp";
		} else {
			logger.debug("unkwnon content type" + responseContentType);
		}

		return ext;
	}

	protected File createTempFile(String name, String ext) {
		try {
			File f = File.createTempFile(name, ext);
			temporaryFileList.add(f.toURI().toString());
			return f;
		} catch (Exception e) {
			e.printStackTrace();
			return null;
		}

	}

	protected void deleteTempFileList() {

		try {
			for (int i = 0; i < temporaryFileList.size(); i++) {
				File f = new File(new URI(temporaryFileList.get(i)));
				if (f.exists()) {
					boolean deleted = f.delete();
					if (deleted)
						logger.debug("temporary file " + f.toURI().toString() + " is deleted");
					else {
						f.deleteOnExit();
						logger.warn( "temporary file " + f.toURI().toString() + " is not deleted");
					}
				}

			}
			temporaryFileList.clear();
		} catch (Exception e) {
			e.printStackTrace();
		}

	}

	protected boolean isXML(String responseContentType) {
		if (responseContentType == null)
			return false;

		if (responseContentType.startsWith(XML) || responseContentType.startsWith(XML_OGC_WMS) || responseContentType.startsWith(XML_OGC_EXCEPTION)
				|| responseContentType.startsWith(APPLICATION_XML) || responseContentType.contains("gml"))
			return true;
		return false;

	}

	/**
	 * @param urlstr
	 * @return
	 */
	protected Object getUsername(String urlstr) {

		List<RemoteServerInfo> serverInfoList = configuration.getRemoteServer();
		Iterator<RemoteServerInfo> it = serverInfoList.iterator();
		while (it.hasNext()) {
			RemoteServerInfo serverInfo = it.next();
			if (serverInfo.getUrl().equals(urlstr))
				return serverInfo.getUser();
		}

		return null;
	}

	@Deprecated
	protected abstract  StringBuffer generateOgcException(String errorMessage, String code, String locator, String version) ;
	

	/**
	 * @param urlstr
	 * @return
	 */
	protected Object getPassword(String urlstr) {
		List<RemoteServerInfo> serverInfoList = configuration.getRemoteServer();
		Iterator<RemoteServerInfo> it = serverInfoList.iterator();
		while (it.hasNext()) {
			RemoteServerInfo serverInfo = it.next();
			if (serverInfo.getUrl().equals(urlstr)) {
				// Debug tb 28.09.2009
				// Utilisation de la classe Java "Authenticator" qui ajoute
				// l'authentication, selon les besoins, � la classe java
				// "URLConnection".
				// Pour des raisons de v�rification de schema xsd (requete
				// DescribeFeatureType), la classe "DocumentFactory" n�cessite
				// l'authentication au cas o� geoserver d�fini un compte de
				// service.
				// Do not setCredentials if no account and password were
				// supplied
				if (serverInfo.getUser() != null && serverInfo.getPassword() != null) {
					org.easysdi.security.EasyAuthenticator.setCredientials(getUsername(urlstr).toString(), serverInfo.getPassword());
					// Fin de debug
					return serverInfo.getPassword();
				}
			}
		}

		return null;
	}

	/**
	 * @return
	 */
	private String getLoginService(String urlstr) {
		List<RemoteServerInfo> serverInfoList = configuration.getRemoteServer();
		Iterator<RemoteServerInfo> it = serverInfoList.iterator();
		while (it.hasNext()) {
			RemoteServerInfo serverInfo = it.next();
			if (serverInfo.getUrl().equals(urlstr))
				return serverInfo.getLoginService();
		}
		return null;
	}

	public org.easysdi.xml.documents.Config getConfiguration() {
		return configuration;
	}

	public Policy getPolicy() {
		return this.policy;
	}

	public void setPolicy(Policy p) {
		this.policy = p;
	}

	/**
	 * If the current operation is not allowed, generate an ogc exception 
	 * and send it to the client
	 * @param currentOperation
	 * @param resp
	 */
	protected boolean handleNotAllowedOperation (String currentOperation, HttpServletResponse resp)
	{
		//IF operation is not supported by the current version of the proxy
		if(!ServiceSupportedOperations.contains(currentOperation))
		{
			sendOgcExceptionBuiltInResponse(resp,generateOgcException("Operation not allowed","OperationNotSupported","request", requestedVersion));
			return true;
		}
		try
		{
			if (!isOperationAllowed(currentOperation))
			{
				sendOgcExceptionBuiltInResponse(resp,generateOgcException("Operation not allowed","OperationNotSupported","request",requestedVersion));
				return true;
				
			}
		}
		catch (AvailabilityPeriodException ex)
		{
			
			sendOgcExceptionBuiltInResponse(resp,generateOgcException(ex.getMessage(),"OperationNotSupported","request",requestedVersion));
			return true;
		}
		
		return false;
	}
	
	/**
	 * Return If the current operation is  allowed by the loaded policy
	 * @param currentOperation
	 */
	protected boolean isOperationAllowedByPolicy (String currentOperation)
	{
		try{
			if (isOperationAllowed(currentOperation)) {
				return true;
			}
		}catch (AvailabilityPeriodException ex){
			return false;
		}
		return false;
	}
	
	/**
	 * Return if the current operation is supported by the current version of the proxy
	 * @param currentOperation
	 */
	protected boolean isOperationSupportedByProxy (String currentOperation)
	{
		if(ServiceSupportedOperations.contains(currentOperation))
			return true;
		return false;
	}
	
	/**
	 * If the operation is allowed in the policy then return true, in any other
	 * case return false.
	 * 
	 * @param operation
	 *            the operation to check
	 * @return true | false.
	 */
	public boolean isOperationAllowed(String operation) {
		if (policy == null)
			return false;
		
		if (policy.getAvailabilityPeriod() != null) {
			if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
				return false;
		}

		if (policy.getOperations().isAll())
			return true;
		List<Operation> operationList = policy.getOperations().getOperation();
		for (int i = 0; i < operationList.size(); i++) {
			if (operation.equalsIgnoreCase(operationList.get(i).getName()))
				return true;
		}
		return false;
	}
	
	/**
	 * Detects if the feature type is allowed :
	 * - by being specifically allowed in the policy
	 * - by default, when <Servers All = true> or <FeatureTypes All=true> 
	 * 
	 * @param ft The feature type to test
	 * @return true if the layer is allowed, false if not
	 */
	protected boolean isFeatureTypeAllowed(String ft) {
		if (policy == null)
			return false;
		if (policy.getAvailabilityPeriod() != null) {
			if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
				return false;
		}
	
		List<Server> serverList = policy.getServers().getServer();
		for (int i = 0; i < serverList.size(); i++) {
			FeatureTypes features = serverList.get(i).getFeatureTypes();
			if (features != null) {
				List<FeatureType> ftList = features.getFeatureType();
				for (int j = 0; j < ftList.size(); j++) {
					// Is a specific feature type allowed ?
					if (ft.equals(ftList.get(j).getName()))
						return true;
				}
			}
		}
		
		return false;
	}

	/**
	 * Detects if the feature type is allowed or not against the rule.
	 * 
	 * @param ft
	 *            The feature Type to test
	 * @param url
	 *            the url of the remote server.
	 * @return true if the feature type is allowed, false if not
	 */
	protected boolean isFeatureTypeAllowed(String ft, String url) {
		if (policy == null)
			return false;
		if (policy.getAvailabilityPeriod() != null) {
			if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
				return false;
		}

		//Debug HVH 23.12.2010
		//using policy.getServers().isAll() and features.isAll()
		//return wrong results.
		//Policy content was changed to include all the feature types name in case of getServer().isAll()
		//and features.isAll().
		//So we do not care anymore about those 2 booleans and we loop on all the feature types in the policy
		//5.09.2010 - HVH 
//		if (policy.getServers().isAll())
//			return true;
		//--
//		boolean isServerFound = false;
		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			// Is the server overloaded?
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
//				isServerFound = true;
				// Are all feature Types Allowed ?
				FeatureTypes features = serverList.get(i).getFeatureTypes();
				if (features != null) {
//					if (features.isAll())
//						return true;

					List<FeatureType> ftList = features.getFeatureType();
					for (int j = 0; j < ftList.size(); j++) {
						// Is a specific feature type allowed ?
						if (ft.equals(ftList.get(j).getName()))
							return true;
					}
				}

			}
		}
		
		//5.09.2010 - HVH : moved before the loop on the servers
		// if the server is not overloaded and if all the servers are allowed
		// then
		// We can consider that's ok
//		if (!isServerFound && policy.getServers().isAll())
//			return true;
		//--
		// in any other case the feature type is not allowed
		return false;
	}

	/**
	 * Detects if the layer is allowed :
	 * - by being specifically allowed in the policy
	 * - by default, when <Servers All = true> or <Layers All=true> 
	 * 
	 * @param layer
	 *            The layer to test
	 * @return true if the layer is allowed, false if not
	 */
	protected boolean isLayerAllowed (String layer)
	{
		if (policy == null)
			return false;
		if (policy.getAvailabilityPeriod() != null) {
			if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
				return false;
		}
		if (layer == null)
			return false;
 
		List<Server> serverList = policy.getServers().getServer();
		for (int i = 0; i < serverList.size(); i++) {
			List<Layer> layerList = serverList.get(i).getLayers().getLayer();
			for (int j = 0; j < layerList.size(); j++) {
				if (layer.equals(layerList.get(j).getName()))
					return true;
			}
		}
		return false;
	}
	/**
	 * Detects if the layer is allowed or not against the rule.
	 * 
	 * @param layer
	 *            The layer to test
	 * @param url
	 *            the url of the remote server.
	 * @return true if the layer is allowed, false if not
	 */
	public boolean isLayerAllowed(String layer, String url) {
		if (policy == null)
			return false;
		if (policy.getAvailabilityPeriod() != null) {
			if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
				return false;
		}

		if (layer == null)
			return false;

		//Debug HVH 23.12.2010
		//using policy.getServers().isAll() and serverList.get(i).getLayers().isAll()
		//return wrong results.
		//Policy content was changed to include all the layers name in case of getServer().isAll()
		//and getLayers.isAll().
		//So we do not care anymore about those 2 booleans and we loop on all the layer in the policy

		//5.09.2010 - HVH 
//		if (policy.getServers().isAll())
//			return true;
		//--
//		boolean isServerFound = false;
		List<Server> serverList = policy.getServers().getServer();
			
		for (int i = 0; i < serverList.size(); i++) {
			// Is the server overloaded?
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
//				isServerFound = true;
				// Are all layers Allowed ?
//				if (serverList.get(i).getLayers().isAll())
//					return true;

				List<Layer> layerList = serverList.get(i).getLayers().getLayer();
				for (int j = 0; j < layerList.size(); j++) {
					// Is a specific layer allowed ?
					if (layer.equals(layerList.get(j).getName()))
						return true;
				}

			}
		}
		
		//Debug HVH : 20.12.2010
		//If server exists and all servers are allowed
//		if (isServerFound && policy.getServers().isAll())
//			return true;
		
		
		//--
		// in any other case the feature type is not allowed
		return false;
	}
	
	
	/**
	 * DRAFT
	 * @param url
	 * @param layer
	 * @return
	 */
	protected String getLayerBbox(String url, String layer) {
		if (policy == null)
			return null;

		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
				List<Layer> layerList = serverList.get(i).getLayers().getLayer();
				for (int j = 0; j < layerList.size(); j++) {
					if (layer.equals(layerList.get(j).getName())) {
						if (layerList.get(j).getLatLonBoundingBox() == null)
							return null;
						return layerList.get(j).getLatLonBoundingBox();
					}
				}
			}
		}
		return null;
	}

	/**
	 * Detects if the layer is an allowed or not against the rule.
	 * 
	 * @param layer
	 *            The layer to test
	 * @param url
	 *            the url of the remote server.
	 * @param scale
	 *            the current scale
	 * @return true if the layer is the allowed scale, false if not
	 */
	protected boolean isLayerInScale(String layer, String url, double scale) {
		if (policy == null)
			return false;
		if (policy.getAvailabilityPeriod() != null) {
			if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
				return false;
		}

		//5.09.2010 - HVH 
		if ( policy.getServers().isAll())
			return true;
		//--
//		boolean isServerFound = false;
		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			// Is the server overloaded?
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
//				isServerFound = true;
				// Are all layers Allowed ?
				// Debug tb 12.11.2009
				// if (serverList.get(i).getLayers().isAll())
				// return true;
				// Fin de debug
				
				//5.09.2010 - HVH 
				// Are all layers Allowed ?
				if (serverList.get(i).getLayers().isAll())
					return true;
				//--
				List<Layer> layerList = serverList.get(i).getLayers().getLayer();
				for (int j = 0; j < layerList.size(); j++) {
					// Is a specific layer allowed ?
					if (layer.equals(layerList.get(j).getName())) {
						Double scaleMin = layerList.get(j).getScaleMin();
						Double scaleMax = layerList.get(j).getScaleMax();

						if (scaleMin == null)
							scaleMin = new Double(0);
						if (scaleMax == null)
							scaleMax = new Double(Double.MAX_VALUE);
						if (scale >= scaleMin.doubleValue() && scale <= scaleMax.doubleValue())
							return true;
						else
							return false;
					}
				}

			}
		}

		//5.09.2010 - HVH : moved before the loop on the servers
		// if the server is not overloaded and if all the servers are allowed
		// then
		// We can consider that's ok
//		if (!isServerFound && policy.getServers().isAll())
//			return true;
		//--
		// in any other case the feature type is not allowed
		return false;
	}

	/**
	 * Detects if the attribute of a feature type is allowed or not against the
	 * rule.
	 * 
	 * @param ft
	 *            The feature Type to test
	 * @param url
	 *            the url of the remote server.
	 * @param isAllAttributes
	 *            if the user request need all attributes.
	 * @return true if the feature type is allowed, false if not
	 */
	protected boolean isAttributeAllowed(String url, String ft, String attribute) {
		if (policy == null)
			return false;
		if (policy.getAvailabilityPeriod() != null) {
			if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
				return false;
		}
		
		//5.09.2010 - HVH 
		if ( policy.getServers().isAll())
			return true;
		//--
		boolean isServerFound = false;
		boolean isFeatureTypeFound = false;
		boolean FeatureTypeAllowed = false;

		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			// Is the server overloaded?
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
				isServerFound = true;
				List<FeatureType> ftList = serverList.get(i).getFeatureTypes().getFeatureType();
				//5.09.2010 - HVH --
				if(serverList.get(i).getFeatureTypes().isAll())
				{
					policyAttributeListNb = 0;
					return true;
				}
				//--
				FeatureTypeAllowed = serverList.get(i).getFeatureTypes().isAll();
				for (int j = 0; j < ftList.size(); j++) {
					// Is a specific feature type allowed ?
					if (ft.equals(ftList.get(j).getName())) {
						isFeatureTypeFound = true;
						// If all the attribute are authorized, then return
						// true;
						if (ftList.get(j).getAttributes().isAll()) {
							// Debug tb 08.06.2009
							policyAttributeListNb = 0;
							// Fin de debug
							return true;
						}

						// List d'attributs de Policy pour le featureType
						// courant
						List<Attribute> attributeList = ftList.get(j).getAttributes().getAttribute();
						// Supprime les r�sultats, contenu dans la globale var,
						// issus du pr�c�dent appel de la fonction courante
						policyAttributeListToKeepPerFT.clear();
						for (int k = 0; k < attributeList.size(); k++) {
							// If attributes are listed in user req
							if (attribute.equals(attributeList.get(k).getContent()))
								return true;
							// Debug tb 04.06.2009
							// If no attributes are listed in user req -> all
							// the Policy Attributes will be returned
							else if (attribute.equals("")) {
								String tmpFA = attributeList.get(k).getContent();
								policyAttributeListToKeepPerFT.add(tmpFA);
								// then at the end of function -> return false,
								// "" is effectively not a valid attribute
							}
							// Fin de debug
						}
						// Debug tb 08.06.2009
						// Pour que attributeListToKeepNbPerFT du featureType
						// courant puisse enregistrer le nombre d'attributs de
						// la Policy
						policyAttributeListNb = attributeList.size();
						// Fin de debug
					}
				}
			}
		}

		//5.09.2010 - HVH : moved before the loop on the servers
//		// if the server is not overloaded and if all the servers are allowed
//		// then
//		// We can say that's ok
//		if (!isServerFound && policy.getServers().isAll())
//			return true;
		//--
		// if the server is overloaded and if the specific featureType was not
		// overloaded and All the featuetypes are allowed
		// We can say that's ok
		if (isServerFound && !isFeatureTypeFound && FeatureTypeAllowed)
		// Debug tb 11.11.2009
		{
			policyAttributeListNb = 0; // -> Attribure.All() = true
			return true;
		}
		// Fin de Debug

		// in any other case the feature type is not allowed
		return false;
	}

	protected String getLayerFilter(String layer) {
		if (policy == null)
			return null;
		if (layer == null)
			return null;

		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			List<Layer> layerList = serverList.get(i).getLayers().getLayer();
			for (int j = 0; j < layerList.size(); j++) {
				// Is a specific feature type allowed ?
				if (layer.equals(layerList.get(j).getName())) {
					if (layerList.get(j).getFilter() == null)
						return null;
					return layerList.get(j).getFilter().getContent();
				}
			}
		}
		return null;
	}

	public String getLayerFilter(String url, String layer) {
		if (policy == null)
			return null;

		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
				List<Layer> layerList = serverList.get(i).getLayers().getLayer();
				for (int j = 0; j < layerList.size(); j++) {
					// Is a specific feature type allowed ?
					if (layer.equals(layerList.get(j).getName())) {
						if (layerList.get(j).getFilter() == null)
							return null;
						return layerList.get(j).getFilter().getContent();
					}
				}
			}
		}
		return null;
	}

	protected String getServerNamespace(String url) {
		if (policy == null)
			return null;
		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
				if (serverList.get(i).getNamespace() == null)
					return null;
				return serverList.get(i).getNamespace();
			}
		}
		return null;
	}

	protected String getServerPrefix(String url) {
		if (policy == null)
			return null;
		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
				if (serverList.get(i).getPrefix() == null)
					return null;
				return serverList.get(i).getPrefix();
			}
		}
		return null;
	}

	protected String getFeatureTypeLocalFilter(String url, String ft) {

		if (policy == null)
			return null;
		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {

			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {

				List<FeatureType> ftList = serverList.get(i).getFeatureTypes().getFeatureType();
				for (int j = 0; j < ftList.size(); j++) {
					// Is a specific feature type allowed ?
					if (ft.equals(ftList.get(j).getName())) {
						if (ftList.get(j).getLocalFilter() == null)
							return null;
						return ftList.get(j).getLocalFilter().getContent();
					}
				}

			}
		}
		return null;
	}

	protected String getFeatureTypeRemoteFilter(String url, String ft) {

		if (policy == null)
			return null;
		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {

			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {

				List<FeatureType> ftList = serverList.get(i).getFeatureTypes().getFeatureType();
				for (int j = 0; j < ftList.size(); j++) {
					// Is a specific feature type allowed ?
					if (ft.equals(ftList.get(j).getName())) {
						if (ftList.get(j).getRemoteFilter() == null)
							return null;
						return ftList.get(j).getRemoteFilter().getContent();
					}
				}
			}
		}
		return null;
	}

	protected boolean isSizeInTheRightRange(int currentWidth, int currentHeight) {

		if (policy == null)
			return false;
		if (policy.getAvailabilityPeriod() != null) {
			if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
				return false;
		}

		int minWidth = 0;
		int minHeight = 0;
		int maxWidth = Integer.MAX_VALUE;
		int maxHeight = Integer.MAX_VALUE;
		if (policy.getImageSize() == null)
			return true;
		if (policy.getImageSize().getMinimum() != null) {
			minWidth = policy.getImageSize().getMinimum().getWidth();
			minHeight = policy.getImageSize().getMinimum().getHeight();
		}

		if (policy.getImageSize().getMaximum() != null) {
			maxWidth = policy.getImageSize().getMaximum().getWidth();
			maxHeight = policy.getImageSize().getMaximum().getHeight();
		}

		if (currentWidth >= minWidth && currentWidth <= maxWidth && currentHeight >= minHeight && currentHeight <= maxHeight) {
			return true;
		}

		return false;
	}

	protected String getServerFilter(String url) {
		if (policy == null)
			return null;
		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {

			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {

				if (serverList.get(i).getFilter() == null)
					return null;
				return serverList.get(i).getFilter().getContent();

			}

		}
		return null;
	}

	/**
	 * If the current date is not in the right date range returns an error
	 * 
	 * @param conf
	 * @return
	 * @throws NoPermissionException
	 */
	protected boolean isDateAvaillable(AvailabilityPeriod p) {
		if (policy == null)
			throw new AvailabilityPeriodException(AvailabilityPeriodException.SERVICE_IS_NULL);
		// return false;
		SimpleDateFormat sdf = new SimpleDateFormat(p.getMask());
		Date fromDate = null;
		Date toDate = null;
		try {
			if (p.getFrom() != null)
				fromDate = sdf.parse(p.getFrom().getDate());
			if (p.getTo() != null)
				toDate = sdf.parse(p.getTo().getDate());
		} catch (Exception e) {
			e.printStackTrace();
			throw new AvailabilityPeriodException(AvailabilityPeriodException.SERVICE_DATES_PARSE_ERROR);
			// return false;
		}
		Date currentDate = new Date();
		if (fromDate != null)
			if (currentDate.compareTo(fromDate) <0)
				throw new AvailabilityPeriodException(AvailabilityPeriodException.CURRENT_DATE_BEFORE_SERVICE_FROM_DATE);
		// return false;

		if (toDate != null)
			if (currentDate.compareTo(toDate) > 0)
				throw new AvailabilityPeriodException(AvailabilityPeriodException.CURRENT_DATE_AFTER_SERVICE_TO_DATE);
		// return false;

		return true;
	}

	protected List<String> getAttributesNotAllowedInMetadata(String url) {
		List<String> attrList = new Vector<String>();

		if (policy == null)
			return attrList;

		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			// Is the server overloaded?
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
				if (serverList.get(i).getMetadata().getAttributes().isAll()) {
					attrList.add("%");
					return attrList;
				}
				List<Attribute> attrList2 = serverList.get(i).getMetadata().getAttributes().getExclude().getAttribute();
				for (int j = 0; j < attrList2.size(); j++) {
					attrList.add(attrList2.get(j).getContent());
				}
			}
		}
		// in any other case the attribute is not allowed
		return attrList;
	}

	protected boolean isAttributeAllowedForMetadata(String url, String attribute) {
		if (policy == null)
			return false;
		if (policy.getAvailabilityPeriod() != null) {
			if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
				return false;
		}

		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			// Is the server overloaded?
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
				if (serverList.get(i).getMetadata().getAttributes().isAll())
					return true;
				List<Attribute> attrList = serverList.get(i).getMetadata().getAttributes().getAttribute();
				for (int j = 0; j < attrList.size(); j++) {
					if (attribute.equals(attrList.get(j).getContent())) {
						return true;
					}
				}
			}
		}

		// in any other case the attribute is not allowed
		return false;
	}

	protected boolean areAllAttributesAllowedForMetadata(String url) {
		if (policy == null)
			return false;
		if (policy.getAvailabilityPeriod() != null) {
			if (isDateAvaillable(policy.getAvailabilityPeriod()) == false)
				return false;
		}

		List<Server> serverList = policy.getServers().getServer();

		for (int i = 0; i < serverList.size(); i++) {
			// Is the server overloaded?
			if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
				if (serverList.get(i) != null && serverList.get(i).getMetadata() != null && serverList.get(i).getMetadata().getAttributes() != null) {
					if (serverList.get(i).getMetadata().getAttributes().isAll())
						return true;
					else
						return false;
				}
			}
		}

		// in any other case the attribute is not allowed
		return false;

	}

	/**
	 * Open a wfs GetFeature response file and load xsd schema correctly. See
	 * remote-server-list element in config.xml for credentials.
	 * 
	 * @param file
	 *            temporary response file to parse
	 * @param username
	 *            xsd schema provider server username.
	 * @param password
	 *            xsd schema provider server password.
	 * @return
	 */
	protected Object documentFactoryGetInstance(File file, String username, String password) {
		// File schema = new File("src/main/webapp/schemas/eai/eai.xsd");
		Map hints = new HashMap();
		Map<String, URI> namespaceMapping = new HashMap<String, URI>();
		hints.put(DocumentFactory.VALIDATION_HINT, false);
		hints.put(XMLHandlerHints.NAMESPACE_MAPPING, namespaceMapping);
		SAXBuilder sb = new SAXBuilder();
		Document doc;
		try {
			doc = sb.build(file);
			Namespace xsiNS = Namespace.getNamespace("http://www.w3.org/2001/XMLSchema-instance");
			String schemaLocation = doc.getRootElement().getAttributeValue("schemaLocation", xsiNS);
			String schemaParts[] = schemaLocation.split(" ");
			for (int i = 0; i < schemaParts.length; i += 2) {
				String namespace = schemaParts[i];
				String schemaLocationURI = schemaParts[i + 1];
				if (SchemaFactory.getInstance(new URI(namespace)) == null) {
					GetMethod get = new GetMethod(schemaLocationURI);
					HttpClient client = new HttpClient();
					if (username != null && password != null)
						client.getState().setCredentials(AuthScope.ANY, new UsernamePasswordCredentials(username, password));
					client.executeMethod(get);
					File tempSchemaFile = File.createTempFile("proxy", ".xsd");
					FileWriter writer = new FileWriter(tempSchemaFile);
					writer.write(get.getResponseBodyAsString());
					writer.close();
					namespaceMapping.put(namespace, tempSchemaFile.toURI());
				}
			}
			Object obj = DocumentFactory.getInstance(file.toURI(), hints, Level.WARNING);
			return obj;

		} catch (Exception e) {
			e.printStackTrace();
		}
		return null;
	}
	
	protected void sendHttpServletResponse (HttpServletRequest req, HttpServletResponse resp, ByteArrayOutputStream tempOut, String responseContentType, Integer responseCode)
	{
		try
		{
			// Ecriture du résultat final dans resp de
			// httpServletResponse*****************************************************
			// No post rule to apply. Copy the file result on the output stream
			BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());
			resp.setContentType(responseContentType);
			resp.setStatus(responseCode);
			resp.setContentLength(tempOut.size());
			
			try {
				logger.trace("begin response writting");
				if (req!= null && "1".equals(req.getParameter("download"))) {
					String format = req.getParameter("format");
					if (format == null)
						format = req.getParameter("FORMAT");
					if (format != null) {
						String parts[] = format.split("/");
						String ext = "";
						if (parts.length > 1)
							ext = parts[1];
						resp.setHeader("Content-Disposition", "attachment; filename=download." + ext);
					}
				}
				if (tempOut != null)
					os.write(tempOut.toByteArray());
				logger.trace("end response writting");
			} finally {
				os.flush();
				os.close();
				// Log le résultat et supprime les fichiers temporaires
				DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
				Date d = new Date();
				logger.info("ClientResponseDateTime="+ dateFormat.format(d));
				if (tempOut != null)
					logger.info("ClientResponseLength="+ tempOut.size());
			}
	
			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			Date d = new Date();
			logger.info( "ClientResponseDateTime="+ dateFormat.format(d));
		} 
		catch (Exception e) 
		{
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(e.getMessage());
		}
	}
	
	protected void sendHttpServletResponse (HttpServletRequest req, HttpServletResponse resp, StringBuffer tempOut, String responseContentType, Integer responseCode)
	{
		try
		{
			// Ecriture du résultat final dans resp de
			// httpServletResponse*****************************************************
			// No post rule to apply. Copy the file result on the output stream
			BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());
			resp.setContentType(responseContentType);
			resp.setStatus(responseCode);
			resp.setContentLength(tempOut.length());
			//resp.setCharacterEncoding("UTF-8");
			
			try {
				logger.trace("transform begin response writting");
				if (req!= null && "1".equals(req.getParameter("download"))) {
					String format = req.getParameter("format");
					if (format == null)
						format = req.getParameter("FORMAT");
					if (format != null) {
						String parts[] = format.split("/");
						String ext = "";
						if (parts.length > 1)
							ext = parts[1];
						resp.setHeader("Content-Disposition", "attachment; filename=download." + ext);
					}
				}
				if(responseCode.equals(HttpServletResponse.SC_INTERNAL_SERVER_ERROR)){
					resp.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR, tempOut.toString());
				}
				else if (tempOut != null)
					os.write(tempOut.toString().getBytes());
				logger.trace("transform end response writting");
			} finally {
				os.flush();
				os.close();
				// Log le résultat et supprime les fichiers temporaires
				DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
				Date d = new Date();
				logger.info("ClientResponseDateTime="+ dateFormat.format(d));
				if (tempOut != null)
					logger.info("ClientResponseLength="+ tempOut.length());
			}
	
//			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
//			Date d = new Date();
//			logger.info("ClientResponseDateTime="+ dateFormat.format(d));
		} 
		catch (Exception e) 
		{
			resp.setHeader("easysdi-proxy-error-occured", "true");
			logger.error(e.getMessage());
		}
	}
}
