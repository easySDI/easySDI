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
import java.net.HttpURLConnection;
import java.net.URI;
import java.net.URL;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Iterator;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.UUID;
import java.util.Vector;
import java.util.zip.GZIPInputStream;

import javax.net.ssl.SSLHandshakeException;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
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
import org.easysdi.proxy.domain.SdiAllowedoperation;
import org.easysdi.proxy.domain.SdiPhysicalservice;
import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.log.ProxyLogger;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.hibernate.metadata.CollectionMetadata;
import org.jdom.Document;
import org.jdom.JDOMException;
import org.jdom.input.SAXBuilder;
import org.springframework.context.ApplicationContext;
import org.springframework.security.core.context.SecurityContextHolder;
import org.xml.sax.SAXException;

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
   
    protected SdiVirtualservice sdiVirtualService;
    protected SdiPolicy sdiPolicy;
    private static final String strDateFormat = "dd/MM/yyyy HH:mm:ss:SSS";
    protected DateFormat dateFormat ;
    public HttpServletRequest request; 
    public HttpServletResponse response;
    
    /**
     * List of the physical services relayed by the current virtual service
     */
    private LinkedHashMap<String, SdiPhysicalservice> physicalServiceHashTable = new LinkedHashMap <String, SdiPhysicalservice>();
    //WFS specific
    private List<SdiPhysicalservice> physicalServiceList = new ArrayList<SdiPhysicalservice>();
    
    /**
     * 
     */
    private List<String> temporaryFileList = new Vector<String>();
    /**
     * 
     */
    protected String requestCharacterEncoding = null;
    /**
     * 
     */
    protected String responseContentType = null;
    /**
     * 
     */
    protected List<String> responseContentTypeList = new ArrayList<String>();
    
    /**
     * 
     */
    protected Integer responseStatusCode = HttpServletResponse.SC_OK;

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
    protected ApplicationContext context;

    /**
     * 
     */
    public ProxyServlet(ProxyServletRequest proxyRequest, SdiVirtualservice virtualService, SdiPolicy policy, ApplicationContext context) {
    	super();
    	this.proxyRequest = proxyRequest;
    	this.sdiVirtualService = virtualService;
    	this.sdiPolicy = policy;
        this.context = context;
    	
    	//Set the logger
    	Level level = Level.toLevel(sdiVirtualService.getSdiSysLoglevel().getValue()); 
    	logger = org.apache.log4j.Logger.getLogger("ProxyLogger");
    	logger.setLevel(level);
    	
    	//Init value
	    dateFormat = new SimpleDateFormat(strDateFormat);

	    DailyRollingFileAppender appender = (DailyRollingFileAppender)logger.getAppender("logFileAppender");
	    appender.setBufferedIO(true);
	    appender.setAppend(true);
	    appender.setFile(sdiVirtualService.getLogpath()+File.separator+sdiVirtualService.getAlias()+"."+new SimpleDateFormat("yyyy.MM.dd").format(new Date())+"."+sdiVirtualService.getSdiSysServiceconnector().getValue()+".log");
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

	    String result = conversionPattern.substring(0,start) + strDateFormat + conversionPattern.substring(end);
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
     * Get the list of physical services relayed by the current virtual service
     * in a LinkedHashMap mapping <alias,SdiPhysicalservice>
     * replace : getRemoteServerInfoList
     * @return
     */
    public LinkedHashMap<String, SdiPhysicalservice> getPhysicalServiceHastable() 
    {
    	if (!physicalServiceHashTable.isEmpty())
    		return physicalServiceHashTable;
    	
    	for(SdiPhysicalservice physicalService : sdiVirtualService.getSdiPhysicalservices()){
    		if(physicalService != null)
    			physicalServiceHashTable.put(physicalService.getAlias(),physicalService);
	    }
    	return physicalServiceHashTable;
    }

    /**
     * Get a remoteServerInfo by his alias
     * Use Hastable remoteServerInfoHashTable
     * Replace : getRemoteServerInfo()
     * @param alias
     * @return
     */
    public SdiPhysicalservice getPhysicalServiceByAlias (String alias)
    {
		if(alias == null || alias == "")
		    return null;
	
		if (physicalServiceHashTable.isEmpty())
		{
			physicalServiceHashTable = getPhysicalServiceHastable();
		}
		return physicalServiceHashTable.get(alias);
    }

    /**
     * Get the remoteServerInfo defines as the master in the config.xml
     * @return
     */
    public SdiPhysicalservice getPhysicalServiceMaster()
    {
    	if (physicalServiceHashTable.isEmpty())
		{
			physicalServiceHashTable = getPhysicalServiceHastable();
		}
    	
    	for (Map.Entry<String, SdiPhysicalservice> entry : physicalServiceHashTable.entrySet()) {
    		return entry.getValue();
    	}
		return null;
    }

    /**
     * Get the list of physical services relayed by the virtual service
     * @return
     */
    protected List<SdiPhysicalservice> getPhysicalServiceList() {
    	if(!physicalServiceList.isEmpty())
    		return  physicalServiceList;
    	
    	for(SdiPhysicalservice physicalService : sdiVirtualService.getSdiPhysicalservices()){
    		if(physicalService != null)
    			physicalServiceList.add(physicalService);
	    }
    	
    	return physicalServiceList;
    }


    /**
     * Get a SdiPhysicalservice by his index in the list
     * @param i
     * @return
     */
    protected SdiPhysicalservice getPhysicalServiceByIndex(int i) 
    {
		List<SdiPhysicalservice> l = this.getPhysicalServiceList();
		if (l != null && l.size() > 0) {
		    return (SdiPhysicalservice) l.get(i);
		}
		return null;
    }

    /**
     * Get a SdiPhysicalservice Url by is index in the list
     * @param i
     * @return
     */
    public String getPhysicalServiceURLByIndex(int i) {
    	List<SdiPhysicalservice> l = this.getPhysicalServiceList();
		if (l != null && l.size() > 0) {
		    return l.get(i).getResourceurl();
		}
		return null;
    }

  
    /* (non-Javadoc)
     * @see javax.servlet.http.HttpServlet#doPost(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    public void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException 
    {
		request = req;
    	response = resp;
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
    public void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException 
    {
    	request = req;
    	response = resp;
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
     * Builds the url from the request
     * 
     * @param req
     *            the HttpServletRequest request
     * @return returns the url
     */
    public String getServletUrl(HttpServletRequest req) {
		// http://hostname.com:80/mywebapp/servlet/MyServlet/a/b;c=123?d=789
	    if (sdiVirtualService.getReflectedurl() != null && sdiVirtualService.getReflectedurl().length() > 0) {
		    return sdiVirtualService.getReflectedurl();
		}
		String scheme = req.getScheme(); // http
		String serverName = req.getServerName(); // hostname.com
		int serverPort = req.getServerPort(); // 80
		String contextPath = req.getContextPath(); // /mywebapp
		String servletPath = req.getServletPath(); // /servlet/MyServlet
//		String pathInfo = req.getPathInfo(); // /a/b;c=123 --> always null since proxy servlet not uses anymore the 'ogc' pathInfo 
		if(servletPath.endsWith("?"))
			servletPath = servletPath.substring(0, servletPath.length()-1);
		
		String url = scheme + "://" + serverName + ":" + serverPort + contextPath + servletPath;
//		if (pathInfo != null) {
//		    url += pathInfo;
//		}
	
		return url;
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

    public String sendData(String method, String urlstr, String parameters) 
    {
		try {
		    HttpURLConnection hpcon = SendToRemoteServer(method,urlstr, parameters);
	
		    InputStream in = null;
		    String responseExtensionContentType=null;
	
		    //HTTP Code handling
		    int httpCode = hpcon.getResponseCode() ;
		    responseStatusCode = httpCode;
		    if(httpCode>= 400 && httpCode <500)
		    {
		    	//Response with HTTP code 4xx are keeped because they can contain exception information usefull for the client
		    	logger.info("Remote server '"+urlstr+"' returns HTTP CODE "+httpCode+" to request ["+parameters+"]");
				try{
				    in = hpcon.getInputStream();
				}catch (IOException e){
					StringBuffer strResponse = null;
				   	strResponse = owsExceptionReport.generateExceptionReport(request, response, owsExceptionReport.getHttpCodeDescription(String.valueOf(httpCode)), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK);
					in = new ByteArrayInputStream(strResponse.toString().getBytes());
				    //Set the ContentType according to the new content of the response 
				    responseExtensionContentType = "text/xml";
				    responseStatusCode = HttpServletResponse.SC_OK;
				}
		    }
		    else if (httpCode >=500)
		    {
				logger.info("Remote server '"+urlstr+"' returns HTTP CODE "+httpCode+" to request ["+parameters+"]");
				//All HTTP error with code > 500 are translated into OGC exceptions to be returned to the client (if the Exception management mode allowed it)
				StringBuffer strResponse = null;
				strResponse = owsExceptionReport.generateExceptionReport(request, response,owsExceptionReport.getHttpCodeDescription(String.valueOf(httpCode)), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK);
				in = new ByteArrayInputStream(strResponse.toString().getBytes());
				//Set the ContentType according to the new content of the response 
				responseExtensionContentType = "text/xml";
				responseStatusCode = HttpServletResponse.SC_OK;
			}
		    else
		    {
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
    public void sendDataDirectStream(HttpServletResponse resp, String method, String sUrl, String parameters) throws Exception
    {
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
		    } 
		    else {
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
    protected void deleteTempFileList() 
    {
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
     * Get the username for authentication on physical service
     * @param urlstr
     * @return
     */
    protected Object getUsername(String urlstr) 
    {
    	if (physicalServiceHashTable.isEmpty())
		{
			physicalServiceHashTable = getPhysicalServiceHastable();
		}
    	for(Map.Entry <String, SdiPhysicalservice> service : physicalServiceHashTable.entrySet()){
    		if(service.getValue().getResourceurl().equals(urlstr))
    			return service.getValue().getResourceusername();
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
    	if (physicalServiceHashTable.isEmpty())
		{
			physicalServiceHashTable = getPhysicalServiceHastable();
		}
    	for(Map.Entry <String, SdiPhysicalservice> service : physicalServiceHashTable.entrySet()){
    		if(service.getValue().getResourceurl().equals(urlstr)){
    			if(service.getValue().getResourceusername() != null  &&service.getValue().getResourcepassword() != null)
    			{
    				// Utilisation de la classe Java "Authenticator" qui ajoute
    				// l'authentication, selon les besoins, � la classe java
    				// "URLConnection".
    				// Pour des raisons de v�rification de schema xsd (requete
    				// DescribeFeatureType), la classe "DocumentFactory" n�cessite
    				// l'authentication au cas o� geoserver d�fini un compte de
    				// service.
    				// Do not setCredentials if no account and password were
    				// supplied
					org.easysdi.proxy.security.EasyAuthenticator.setCredientials(getUsername(urlstr).toString(), service.getValue().getResourceusername() );
					return service.getValue().getResourcepassword();
    			}
    		}
    	}
    	return null;
	}

    /**
     * @return
     */
    private String getLoginService(String urlstr) {
    	if (physicalServiceHashTable.isEmpty())
		{
			physicalServiceHashTable = getPhysicalServiceHastable();
		}
    	for(Map.Entry <String, SdiPhysicalservice> service : physicalServiceHashTable.entrySet()){
    		if(service.getValue().getResourceurl().equals(urlstr))
    			return service.getValue().getServiceurl();
    	}
    	return null;
    }

   
       /**
     * If the operation is allowed in the policy then return true, in any other
     * case return false.
     * 
     * @param operation the operation to check
     * @return true | false.
     */
    public boolean isOperationAllowed(String operation) 
    {
		if(sdiPolicy.isAnyoperation())
			return true;
		
		for(SdiAllowedoperation allowOperation :sdiPolicy.getSdiAllowedoperations()){
			if(allowOperation.getSdiSysServiceoperation().getValue().equals(operation))
				return true;
		}

		return false;
    }
 
    /**
     * 
     * @param req
     * @param resp
     * @param tempOut
     * @param responseContentType
     * @param responseCode
     */
    protected void sendHttpServletResponse (HttpServletRequest req, HttpServletResponse resp, ByteArrayOutputStream tempOut, String responseContentType, int responseCode)
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
		    } 
		    finally {
				os.flush();
				os.close();
				Date d = new Date();
				logger.info("ClientResponseDateTime="+ dateFormat.format(d));
				if (tempOut != null)
				    logger.info("ClientResponseLength="+ tempOut.size());
		    }
		
			Date d = new Date();
			logger.info( "ClientResponseDateTime="+ dateFormat.format(d));
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

    /**
     * 
     * @param response
     * @return
     */
    public ByteArrayOutputStream applyUserXSLT (ByteArrayOutputStream response){
		try{
	        	String userXsltPath = sdiVirtualService.getXsltfilename();
	        	if (SecurityContextHolder.getContext().getAuthentication() != null) {
	        	    userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
	        	}
	        
	        	userXsltPath = userXsltPath + "/" + getProxyRequest().getVersion() + "/" + getProxyRequest().getOperation() + ".xsl";
	        	String globalXsltPath = userXsltPath + "/" + getProxyRequest().getVersion() + "/" + getProxyRequest().getOperation() + ".xsl";
	        
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

    /**
     * 
     * @param response
     * @return
     */
    public File applyUserXSLT (File response){
    	String userXsltPath = sdiVirtualService.getXsltfilename();
		if (SecurityContextHolder.getContext().getAuthentication() != null) {
		    userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
		}
	
		userXsltPath = userXsltPath + "/" + getProxyRequest().getVersion() + "/" + getProxyRequest().getOperation() + ".xsl";
		String globalXsltPath = userXsltPath + "/" + getProxyRequest().getVersion() + "/" + getProxyRequest().getOperation() + ".xsl";
	
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
