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
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileOutputStream;
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
import java.util.Calendar;
import java.util.Date;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;
import java.util.UUID;
import java.util.Vector;
import java.util.zip.GZIPInputStream;

import javax.naming.NoPermissionException;
import javax.net.ssl.SSLHandshakeException;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.apache.log4j.DailyRollingFileAppender;
import org.apache.log4j.FileAppender;
import org.apache.log4j.Level;
import org.apache.log4j.Logger;
import org.apache.log4j.PatternLayout;
import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.exception.AvailabilityPeriodException;
import org.easysdi.proxy.log.ProxyLogger;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.policy.AvailabilityPeriod;
import org.easysdi.proxy.policy.FeatureType;
import org.easysdi.proxy.policy.FeatureTypes;
import org.easysdi.proxy.policy.Layer;
import org.easysdi.proxy.policy.Operation;
import org.easysdi.proxy.policy.Policy;
import org.easysdi.proxy.policy.Server;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.jdom.Document;
import org.jdom.JDOMException;
import org.jdom.input.SAXBuilder;
import org.springframework.security.core.context.SecurityContextHolder;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.ls.LSSerializer;
import org.xml.sax.SAXException;

import com.google.common.collect.HashMultimap;
import com.google.common.collect.Multimap;

/**
 * @author DEPTH SA
 *
 */
public abstract class ProxyServlet extends HttpServlet {

    private static final long serialVersionUID = 3499090220094877198L;
    protected static final String PNG = "image/png";
    protected static final String GIF = "image/gif";
    protected static final String JPG = "image/jpg";
    protected static final String JPEG = "image/jpeg";
    protected static final String TIFF = "image/tiff";
    protected static final String BMP = "image/bmp";
    protected static final String XML = "text/xml";
    protected static final String TXT = "text/plain";
    protected static final String APPLICATION_XML = "application/xml";
    protected static final String XML_OGC_WMS = "application/vnd.ogc.wms_xml";
    protected static final String XML_OGC_EXCEPTION = "application/vnd.ogc.se_xml";
    protected static final String SVG = "image/svg+xml";
   
    private SdiVirtualservice sdiVirtualService;
    private SdiPolicy sdiPolicy;

    private List<String> temporaryFileList = new Vector<String>();
    
    /**
     * Configuration loaded to complete the request
     */
    @Deprecated
    protected org.easysdi.xml.documents.Config configuration;

    /**
     * Policy loaded
     */
    public Policy policy;
    protected String requestCharacterEncoding = null;
    protected String responseContentType = null;
    protected List<String> responseContentTypeList = new ArrayList<String>();
    protected Integer responseStatusCode = HttpServletResponse.SC_OK;
    protected Map<Integer, String> wfsFilePathList = new TreeMap<Integer, String>();
    public Multimap<Integer, String> wmsFilePathList = HashMultimap.create();

    /**
     * WMTS response files
     */
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
    protected boolean hasPolicy = true;

    /**
     * 
     */
    public OWSExceptionReport owsExceptionReport;

    /**
     * Value of the version parameter received in the request
     */
    protected String requestedVersion ;

    /**
     * Object representing the request
     */
    protected ProxyServletRequest proxyRequest;

    /**
     * Logger
     */
    public Logger logger;

    /**
     * 
     */
    public ProxyServlet(ProxyServletRequest proxyRequest, SdiVirtualservice virtualService, SdiPolicy policy) {
    	super();
    	this.proxyRequest = proxyRequest;
    	this.sdiVirtualService = virtualService;
    	this.sdiPolicy = policy;
    	
    	//Set the logger
    	Level level = Level.toLevel(sdiVirtualService.getSdiSysLoglevel().getValue()); 
    	logger = org.apache.log4j.Logger.getLogger("ProxyLogger");
    	logger.setLevel(level);

	    DailyRollingFileAppender appender = (DailyRollingFileAppender)logger.getAppender("logFileAppender");
	    appender.setBufferedIO(true);
	    appender.setAppend(true);
	    appender.setFile(sdiVirtualService.getLogpath()+File.separator+sdiVirtualService.getAlias()+new Date()+sdiVirtualService.getSdiSysServiceconnector().getValue());
	    //DatePattern receive the period rolling value
	    if(sdiVirtualService.getSdiSysLogroll().getValue().equalsIgnoreCase("daily"))
	    	appender.setDatePattern("'.'yyyy-MM-dd");
	    else if(sdiVirtualService.getSdiSysLogroll().getValue().equalsIgnoreCase("monthly"))
	    	appender.setDatePattern("'.'yyyy-MM");
	    else if(sdiVirtualService.getSdiSysLogroll().getValue().equalsIgnoreCase("weekly"))
	    	appender.setDatePattern("'.'yyyy-ww");
	    else 
	    	appender.setDatePattern("'.'yyyy-MM-dd");

	    appender.activateOptions();
	    PatternLayout layout = (PatternLayout) appender.getLayout();
	    String conversionPattern = layout.getConversionPattern();
	    int start = conversionPattern.indexOf("%d{")+3;
	    int end = conversionPattern.indexOf("}", start);

	    String result = conversionPattern.substring(0,start) + "dd/MM/yyyy HH:mm:ss" + conversionPattern.substring(end);
	    layout.setConversionPattern(result);
 
    	//Log initilization informations
    	logger.info("Virtualservice="+sdiVirtualService.getAlias());
    }

    /**
     * @return the proxyRequest
     */
    public ProxyServletRequest getProxyRequest() {
    	return this.proxyRequest;
    }
    
    /**
     * @return the Policy
     */
    public SdiPolicy getPolicy() {
    	return this.sdiPolicy;
    }
    
    /**
     * @return the Virtual Service
     */
    public SdiVirtualservice getVirtualService() {
    	return this.sdiVirtualService;
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
	//Set the configuration
	configuration = conf;

	//Set the logger
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
	    appender.setBufferedIO(true);
	    appender.setAppend(true);
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

	    appender.activateOptions();
	    PatternLayout layout = (PatternLayout) appender.getLayout();
	    String conversionPattern = layout.getConversionPattern();
	    int start = conversionPattern.indexOf("%d{")+3;
	    int end = conversionPattern.indexOf("}", start);

	    String result = conversionPattern.substring(0,start) + configuration.getLogDateFormat() + conversionPattern.substring(end);
	    layout.setConversionPattern(result);

	}

	//Log initilization informations
	logger.info("Config="+configuration.getId());
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
	    //To flush log with log4j we need to set the param immediateFlush to 'true'
	    //AND add a new entry to the log :  all the entries buffered during request execution are written into the log.
	    //We set immediateFlush to false for performance reason (see log4j documentation).
	    //We add the same extra entry in the ProxyLogger log to keep the content of the two kind of log equivalent.
	    if (logger instanceof ProxyLogger){
		((ProxyLogger)logger).info("ProxyServlet done.");
		((ProxyLogger)logger).writeInLog(dateFormat.format(d), req);
	    } else{
		((FileAppender)logger.getAppender("logFileAppender")).setImmediateFlush(true);
		logger.info("----------------------------------------------------------------------------------");
		((FileAppender)logger.getAppender("logFileAppender")).setImmediateFlush(false);
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
	    //To flush log with log4j we need to set the param immediateFlush to 'true'
	    //AND add a new entry to the log :  all the entries buffered during request execution are written into the log.
	    //We set immediateFlush to false for performance reason (see log4j documentation).
	    //We add the same extra entry in the ProxyLogger log to keep the content of the two kind of log equivalent.
	    if (logger instanceof ProxyLogger){
		((ProxyLogger)logger).info("ProxyServlet done.");
		((ProxyLogger)logger).writeInLog(dateFormat.format(d), req);
	    } else{
		((FileAppender)logger.getAppender("logFileAppender")).setImmediateFlush(true);
		logger.info("----------------------------------------------------------------------------------");
		((FileAppender)logger.getAppender("logFileAppender")).setImmediateFlush(false);
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
    @Deprecated
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
    @Deprecated
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

    @Deprecated
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
     * @param method . GET or POST method
     * @param urlstr : remote server url
     * @param parameters : parameters to send to the remote server
     * @return a String containing the path to the file containing the response from the remote server
     * @throws IOException
     * TODO use the server 'alias' as identifier in the method 'SendToRemoteServer' in place of the url string (this param is certainly not an identifier)
     */

    public String sendData(String method, String urlstr, String parameters) {
	try {
	    HttpURLConnection hpcon = SendToRemoteServer(method,urlstr, parameters);

	    InputStream in = null;
	    String responseExtensionContentType=null;

	    //HTTP Code handling
	    int httpCode = hpcon.getResponseCode() ;
	    responseStatusCode = httpCode;
	    if(httpCode>= 400 && httpCode <500){
		//Response with HTTP code 4xx are keeped because they can contain exception information usefull for the client
		logger.info("Remote server '"+urlstr+"' returns HTTP CODE "+httpCode+" to request ["+parameters+"]");
		try{
		    in = hpcon.getInputStream();
		}catch (IOException e){
		    StringBuffer response = null;
		    //The response is contained in the error stream, this kind of response is translated into OGC exceptions to be returned to client
		    if(owsExceptionReport == null){
			//WFS and CSW not used yet an object owsExceptionReport to generate exception message
			//Used the deprecated method to do that until WFS and CSW were updated
			response = generateOgcException("HTTP Code "+String.valueOf(httpCode), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", "");
		    }else{
			response = owsExceptionReport.generateExceptionReport(owsExceptionReport.getHttpCodeDescription(String.valueOf(httpCode)), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "");
		    }
		    in = new ByteArrayInputStream(response.toString().getBytes());
		    //Set the ContentType according to the new content of the response 
		    responseExtensionContentType = "text/xml";
		    responseStatusCode = HttpServletResponse.SC_OK;
		}
	    }else if (httpCode >=500){
		logger.info("Remote server '"+urlstr+"' returns HTTP CODE "+httpCode+" to request ["+parameters+"]");
		//All HTTP error with code > 500 are translated into OGC exceptions to be returned to the client (if the Exception management mode allowed it)
		StringBuffer response = null;
		if(owsExceptionReport == null){
			//WFS and CSW not used yet an object owsExceptionReport to generate exception message
			//Used the deprecated method to do that until WFS and CSW were updated
			response = generateOgcException("HTTP Code "+String.valueOf(httpCode), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", "");
		    }else{
			response = owsExceptionReport.generateExceptionReport(owsExceptionReport.getHttpCodeDescription(String.valueOf(httpCode)), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "");
		    }
		in = new ByteArrayInputStream(response.toString().getBytes());
		//Set the ContentType according to the new content of the response 
		responseExtensionContentType = "text/xml";
		responseStatusCode = HttpServletResponse.SC_OK;
	    }else{
		//Http Code < 400
		if (hpcon.getContentEncoding() != null && hpcon.getContentEncoding().indexOf("gzip") != -1) {
		    in = new GZIPInputStream(hpcon.getInputStream());
		    logger.trace( "return of the remote server is zipped");
		} else {
		    in = hpcon.getInputStream();
		}
	    }

	    //Content type handling
	    responseContentType = hpcon.getContentType();
	    if(hpcon.getContentType() != null && responseExtensionContentType == null){
		//Used to store the current response contentType
		responseExtensionContentType = hpcon.getContentType().split(";")[0];
		//Used to store all the remote server response ContentType : 
		//needed to separate exception contentType (eg : text/xml)
		//from valid response contentType (eg : image/png)
		responseContentTypeList.add(responseExtensionContentType);
	    }

	    //Temp file writing
	    String tmpDir = System.getProperty("java.io.tmpdir");
	    logger.debug(" tmpDir :  " + tmpDir);
	    File tempFile = createTempFile("sendData_" + UUID.randomUUID().toString(), getExtension(responseExtensionContentType));
	    FileOutputStream tempFos = new FileOutputStream(tempFile);
	    byte[] buf = new byte[1024];
	    int nread;
	    while ((nread = in.read(buf)) >= 0) {
		tempFos.write(buf, 0, nread);
	    }
	    tempFos.flush();
	    tempFos.close();
	    in.close();

	    logger.info("RemoteResponseToRequestUrl="+ urlstr);
	    logger.info("RemoteResponseLength="+ tempFile.length());
	    DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
	    Date d = new Date();
	    logger.info("RemoteResponseDateTime="+ dateFormat.format(d));

	    return tempFile.toString();

	}catch (SSLHandshakeException e){
	    e.printStackTrace();
	    logger.error("Unable to find valid certification. "+e.getCause().toString());
	    return null;

	}catch (Exception e) {
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

	HttpURLConnection hpcon = SendToRemoteServer(method,sUrl, parameters);
	InputStream in = null;
	int code = hpcon.getResponseCode();
	if( code >= 400){
	    //Response has a HTTP code error
	    //Try to get the response in the inputStream
	    //If failed, get the errorStream
	    try{
		in = hpcon.getInputStream();
	    }catch (IOException e){
		in = hpcon.getErrorStream();
	    }
	}else{
	    //HTTP code < 400
	    in = hpcon.getInputStream();
	}
	resp.setContentType(hpcon.getContentType());
	resp.setStatus(hpcon.getResponseCode());
	resp.setContentLength(hpcon.getContentLength());
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
	DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
	Date d = new Date();
	logger.info("RemoteResponseDateTime="+ dateFormat.format(d));
    }

    /**
     * @param method
     * @param sUrl
     * @param parameters
     * @return
     */
    private HttpURLConnection SendToRemoteServer (String method, String sUrl, String parameters){
	try {
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

	    return hpcon;

	}catch (Exception e){
	    return null;
	}
    }
    /**
     * @param loginServiceUrl
     * @return
     */
    protected String geonetworkLogIn(String loginServiceUrl) {

	String cookie = null;
	try {
	    URL urlLoginService = new URL(loginServiceUrl);
	    HttpURLConnection hpconLoginService = null;
	    hpconLoginService = (HttpURLConnection) urlLoginService.openConnection();
	    hpconLoginService.setRequestMethod("GET");
	    hpconLoginService.setUseCaches(false);
	    hpconLoginService.setDoInput(true);
	    hpconLoginService.setDoOutput(false);

	    BufferedReader inLoginService = new BufferedReader(new InputStreamReader(hpconLoginService.getInputStream()));
	    cookie = hpconLoginService.getHeaderField("Set-Cookie");

	    inLoginService.close();
	} catch (Exception e) {
	    e.printStackTrace();
	}

	return cookie;

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
	} else if (responseContentType.startsWith(TXT)) {
	    ext = ".txt";
	} else {
	    logger.debug("unkwnon content type " + responseContentType);
	}

	return ext;
    }

    /**
     * @param name
     * @param ext
     * @return
     */
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

    /**
     * 
     */
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

    /**
     * @param responseContentType
     * @return
     */
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
		    org.easysdi.proxy.security.EasyAuthenticator.setCredientials(getUsername(urlstr).toString(), serverInfo.getPassword());
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

    


    /**
     * If the current operation is not allowed, generate an ogc exception 
     * and send it to the client
     * @param currentOperation
     * @param resp
     */
    @Deprecated 
    /*used in CSW and WFS*/
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

	List<Server> serverList = policy.getServers().getServer();

	for (int i = 0; i < serverList.size(); i++) {
	    // Is the server overloaded?
	    if (url.equalsIgnoreCase(serverList.get(i).getUrl())) {
		List<Layer> layerList = serverList.get(i).getLayers().getLayer();
		for (int j = 0; j < layerList.size(); j++) {
		    // Is a specific layer allowed ?
		    if (layer.equals(layerList.get(j).getName()))
			return true;
		}

	    }
	}
	return false;
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
	Calendar calendar = Calendar.getInstance(); 
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
	    //toDate contains hh:mm:ss, so it is like 01:01:2011 00:00:00
	    //To include the toDate in the validity period, we add a day to compare to the current date.
	    calendar.setTime(toDate);
	calendar.add(Calendar.DAY_OF_MONTH, 1);
	toDate = calendar.getTime();
	if (currentDate.compareTo(toDate) > 0)
	    throw new AvailabilityPeriodException(AvailabilityPeriodException.CURRENT_DATE_AFTER_SERVICE_TO_DATE);
	// return false;

	return true;
    }

    

   

    

    protected void sendHttpServletResponse (HttpServletRequest req, HttpServletResponse resp, ByteArrayOutputStream tempOut, String responseContentType, Integer responseCode)
    {
	try
	{
	    BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());
	    resp.setContentType(responseContentType);
	    resp.setStatus(responseCode);
	    if (tempOut != null)
		resp.setContentLength(tempOut.size());
	    else
		resp.setContentLength(0);

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

    public void sendHttpServletResponse (HttpServletRequest req, HttpServletResponse resp, StringBuffer tempOut, String responseContentType, Integer responseCode)
    {
	try
	{
	    BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());
	    resp.setContentType(responseContentType);
	    resp.setStatus(responseCode);
	    if (tempOut != null)
		resp.setContentLength(tempOut.length());
	    else
		resp.setContentLength(0);

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
		/*if(responseCode.equals(HttpServletResponse.SC_INTERNAL_SERVER_ERROR)){
		    resp.sendError(HttpServletResponse.SC_INTERNAL_SERVER_ERROR, tempOut.toString());
		}
		else*/ if (tempOut != null)
		    os.write(tempOut.toString().getBytes());
		logger.trace("transform end response writting");
	    } finally {
		os.flush();
		os.close();
		DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
		Date d = new Date();
		logger.info("ClientResponseDateTime="+ dateFormat.format(d));
		if (tempOut != null)
		    logger.info("ClientResponseLength="+ tempOut.length());
	    }
	} 
	catch (Exception e) 
	{
	    resp.setHeader("easysdi-proxy-error-occured", "true");
	    logger.error(e.getMessage());
	}
    }

    /**
     * Return if the file at the given path is an XML OGC exception file.
     * @param path
     * @return
     * @throws SAXException
     * @throws IOException
     * @throws ParserConfigurationException
     * @throws JDOMException 
     */
    public boolean isRemoteServerResponseException(String path) throws SAXException, IOException, ParserConfigurationException, JDOMException{
	String ext = (path.lastIndexOf(".")==-1)?"":path.substring(path.lastIndexOf(".")+1,path.length());
	if (ext.equals("xml"))
	{
	    SAXBuilder sxb = new SAXBuilder();
	    Document documentMaster = sxb.build(new File(path));
	    if (documentMaster != null) 
	    {
		//ServiceExceptionReport is the root element name for WMS, WFS exception
		//ExceptionReport is the root element for OWS, WMTS, CSW exception
		if(documentMaster.getRootElement().getName().equalsIgnoreCase("ServiceExceptionReport") || documentMaster.getRootElement().getName().equalsIgnoreCase("ExceptionReport"))
		    return true;
		else
		    return false;
	    }
	}else if (ext.equals("txt")){
	    //Exception file can be sent with content txpe text/plain and temporarly store under .txt extension
	    try{
		SAXBuilder sxb = new SAXBuilder();
		Document documentMaster = sxb.build(new File(path));
		if (documentMaster != null) 
		{
		    //ServiceExceptionReport is the root element name for WMS, WFS exception
		    //ExceptionReport is the root element for OWS, WMTS, CSW exception
		    if(documentMaster.getRootElement().getName().equalsIgnoreCase("ServiceExceptionReport") || documentMaster.getRootElement().getName().equalsIgnoreCase("ExceptionReport"))
			return true;
		    else
			return false;
		}
	    }catch(JDOMException e){
		return false;
	    }
	}
	return false;
    }

    public ByteArrayOutputStream applyUserXSLT (ByteArrayOutputStream response){
	try{
        	String userXsltPath = getConfiguration().getXsltPath();
        	if (SecurityContextHolder.getContext().getAuthentication() != null) {
        	    userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
        	}
        
        	userXsltPath = userXsltPath + "/" + getProxyRequest().getVersion() + "/" + getProxyRequest().getOperation() + ".xsl";
        	String globalXsltPath = getConfiguration().getXsltPath() + "/" + getProxyRequest().getVersion() + "/" + getProxyRequest().getOperation() + ".xsl";
        
        	ByteArrayOutputStream result = new ByteArrayOutputStream();
        	File xsltFile = new File(userXsltPath);
        	if (!xsltFile.exists()) {
        	    logger.trace("Postreatment file " + xsltFile.toString() + "does not exist");
        	    xsltFile = new File(globalXsltPath);
        	} 
        
        	if (xsltFile.exists() && isXML(responseContentType)) {
        	    logger.trace("transform begin userTransform xslt");
        
        	    Transformer transformer = null;
        	    TransformerFactory tFactory = TransformerFactory.newInstance();
        	    try {
        		transformer = tFactory.newTransformer(new StreamSource(xsltFile));
        		InputStream is = new java.io.ByteArrayInputStream(response.toByteArray());
        
        		StreamSource attach = new StreamSource(is);
        		transformer.transform(attach, new StreamResult(result));
        	    } catch (TransformerConfigurationException e1) {
        		logger.error(e1.getMessage());
        	    } catch (TransformerException e) {
        		logger.error(e.getMessage());
        	    }
        	    logger.trace("transform end userTransform xslt");
        	    return result;
        	}else{
        	    return response;
        	}
	}catch (Exception e){
	    //If the XSLT transform can not be done, return the initial response 
	    return response;
	}
    }

    public File applyUserXSLT (File response){
	String userXsltPath = getConfiguration().getXsltPath();
	if (SecurityContextHolder.getContext().getAuthentication() != null) {
	    userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
	}

	userXsltPath = userXsltPath + "/" + getProxyRequest().getVersion() + "/" + getProxyRequest().getOperation() + ".xsl";
	String globalXsltPath = getConfiguration().getXsltPath() + "/" + getProxyRequest().getVersion() + "/" + getProxyRequest().getOperation() + ".xsl";

	File result = new File (response.getPath()+".xml");
	File xsltFile = new File(userXsltPath);
	if (!xsltFile.exists()) {
	    logger.trace("Postreatment file " + xsltFile.toString() + "does not exist");
	    xsltFile = new File(globalXsltPath);
	} 

	if (xsltFile.exists() && isXML(responseContentType)) {
	    logger.trace("transform begin userTransform xslt");

	    Transformer transformer = null;
	    TransformerFactory tFactory = TransformerFactory.newInstance();
	    try {
		transformer = tFactory.newTransformer(new StreamSource(xsltFile));
		transformer.transform(new StreamSource(response), new StreamResult(result));
	    } catch (TransformerConfigurationException e1) {
		logger.error(e1.getMessage());
	    } catch (TransformerException e) {
		logger.error(e.getMessage());
	    }
	    logger.trace("transform end userTransform xslt");
	    return result;
	}else{
	    return response;
	}
    }

}
