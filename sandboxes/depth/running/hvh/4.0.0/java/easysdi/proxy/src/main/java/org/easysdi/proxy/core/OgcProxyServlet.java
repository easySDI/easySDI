/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version. This
 * program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see http://www.gnu.org/licenses/gpl.html.
 */
package org.easysdi.proxy.core;

import java.io.IOException;
import java.lang.reflect.Constructor;
import java.util.Collection;
import java.util.HashMap;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBException;

import org.easysdi.proxy.csw.CSWExceptionReport;
import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiPolicyHome;
import org.easysdi.proxy.domain.SdiSysServicecompliance;
import org.easysdi.proxy.domain.SdiUser;
import org.easysdi.proxy.domain.SdiUserHome;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.domain.SdiVirtualserviceHome;
import org.easysdi.proxy.exception.ProxyServletException;
import org.easysdi.proxy.exception.VersionNotSupportedException;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.ows.v200.OWS200ExceptionReport;
import org.easysdi.proxy.wfs.WFSExceptionReport;
import org.easysdi.proxy.wms.WMSExceptionReport;
import org.easysdi.proxy.wmts.v100.WMTSExceptionReport100;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.context.ApplicationContext;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.web.context.support.WebApplicationContextUtils;

/**
 * Reads the configuration file and dispatch the request to the right class.
 *
 * @author DEPTH SA
 *
 */
public class OgcProxyServlet extends HttpServlet {

    private static final long serialVersionUID = 5619994356764480389L;
    private Logger logger = LoggerFactory.getLogger("OgcProxyServlet");
    private HttpServletResponse servletResponse;
    private HttpServletRequest servletRequest;
    public static HashMap<String, Double> executionCount = new HashMap<String, Double>();
    public SdiVirtualserviceHome sdiVirtualserviceHome;
    public SdiPolicyHome sdiPolicyHome;
    public SdiUserHome sdiUserHome;
    ApplicationContext context;


    /* (non-Javadoc)
     * @see javax.servlet.GenericServlet#init(javax.servlet.ServletConfig)
     */
    @Override
    public void init(ServletConfig config) throws ServletException {
        super.init(config);
        context = WebApplicationContextUtils.getWebApplicationContext(getServletContext());
        sdiVirtualserviceHome = (SdiVirtualserviceHome) context.getBean("sdiVirtualserviceHome");
        sdiPolicyHome = (SdiPolicyHome) context.getBean("sdiPolicyHome");
        sdiUserHome = (SdiUserHome) context.getBean("sdiUserHome");

        System.setProperty("org.geotools.referencing.forceXY", "true");
        logger.info("OgcProxyServlet initialization done.");
    }


    /* (non-Javadoc)
     * @see javax.servlet.http.HttpServlet#doGet(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    public void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        servletResponse = resp;
        servletRequest = req;

        ProxyServlet obj = null;

        try {


            obj = createProxy(req.getServletPath().substring(1), req, resp);
            double maxRequestNumber = -1;

            waitWhenConnectionsExceed(req, maxRequestNumber);

            if (obj != null) {
                obj.doGet(req, resp);
            }

        } catch (Exception e) {
            logger.error("Error occured processing doGet: " + e.getMessage());
            resp.setHeader("easysdi-proxy-error-occured", "true");
            try {
                new OWS200ExceptionReport().sendExceptionReport(req, resp, "Error occured processing doGet: " + e.getMessage(), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);

            } catch (IOException ex) {
                logger.error("Error occured processing doGet: ", ex);
            }
        } finally {
            /**
             * What ever happens, always decrease the connection number when
             * finished.
             */
            //TODO : what this is supposed to do?
            if (obj != null) {
                decreaseConnections(req, -1);
            }
        }
    }

    /* (non-Javadoc)
     * @see javax.servlet.http.HttpServlet#doPost(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    public void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        servletResponse = resp;
        servletRequest = req;
        ProxyServlet obj = null;
        try {

            // String info  =req.getPathInfo();
            obj = createProxy(req.getServletPath().substring(1), req, resp);
            double maxRequestNumber = -1;

            waitWhenConnectionsExceed(req, maxRequestNumber);

            if (obj != null) {
                obj.doPost(req, resp);

            }
        } catch (Exception e) {
            logger.error("Error occured processing doPost: " + e.getMessage());
            resp.setHeader("easysdi-proxy-error-occured", "true");
            try {
                new OWS200ExceptionReport().sendExceptionReport(req, resp, "Error occured processing doPost: " + e.getMessage(), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR);
            } catch (IOException ex) {
                logger.error("Error occured processing doPost: ", ex);
            }
        } finally {
            /**
             * What ever happens, always decrease the connection number when
             * finished.
             */
            //TODO : what this is supposed to do?
            if (obj != null) {
                decreaseConnections(req, -1);
            }
        }
    }

    /**
     * *
     * Instanciate a ProxyServlet according to the configuration file.
     *
     * @param servletName
     * @return
     * @throws JAXBException
     */
    private ProxyServlet createProxy(String servletName, HttpServletRequest req, HttpServletResponse resp) throws JAXBException {
        String connector = "";
        ProxyServletRequest request = null;
        try {
            //Get the virtual service
            SdiVirtualservice virtualService = sdiVirtualserviceHome.findByAlias(servletName);
            if (virtualService == null) {
                logger.error("Error occurred during " + servletName + " service initialization : service does not exist.");
                sendException("Error occurred during " + servletName + " service initialization : service does not exist.", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR, connector, null);
                return null;
            }

            //Get the Policy 
            Authentication principal = SecurityContextHolder.getContext().getAuthentication();
            String username = null;
            if (principal != null) {
                username = principal.getName();
                logger.debug("Authentication : " + username);
            }
            Collection<GrantedAuthority> authorities = (Collection<GrantedAuthority>) principal.getAuthorities();
            SdiUser user = sdiUserHome.findByUserName(username);
            Integer id = null;
            if (user != null) {
                id = user.getId();
            }
            SdiPolicy policy = sdiPolicyHome.findByVirtualServiceAndUser(virtualService.getId(), id, authorities);
            if (policy == null) {
                if (((HttpServletRequest) req).getUserPrincipal() == null) {
                    //Spring Anonymous user is used to perform this request, but not policy defined for it
                    resp.setHeader("easysdi-proxy-error-occured", "true");
                    logger.error("Error occurred during " + servletName + " service initialization : No anomnymous policy found.");
                    sendException("Error occurred during " + servletName + " service initialization : No anomnymous policy found.", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK, connector, null);
                    return null;

                } else {
                    //No policy found for the authenticated user, return an ogc exception.
                    resp.setHeader("easysdi-proxy-error-occured", "true");
                    logger.error("Error occurred during " + servletName + " service initialization : No policy found for user.");
                    sendException("Error occurred during " + servletName + " service initialization : No policy found for user.", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK, connector, null);
                    return null;
                }
            }

            //Get the service connector
            connector = virtualService.getSdiSysServiceconnector().getValue();

            //Get the highest version
            String highestversion = "";
            SdiSysServicecompliance highestServicecompliance = null;

            for (SdiSysServicecompliance compliance : virtualService.getSdiSysServicecompliances()) {
                String value = compliance.getSdiSysServiceversion().getValue();
                if (value.compareTo(highestversion) > 0) {
                    highestversion = value;
                    highestServicecompliance = compliance;
                }
            }

            //Build package name
            String packagename = "org.easysdi.proxy." + connector.toLowerCase() + "." + connector.toUpperCase();

            //Bind the request to a ProxyServletRequestObject
            try {
                String requestClassName = packagename + "ProxyServletRequest";
                Class<?> requestClasse = Class.forName(requestClassName);
                Constructor<?> requestConstructeur = requestClasse.getConstructor(new Class[]{Class.forName("javax.servlet.http.HttpServletRequest")});
                request = (ProxyServletRequest) requestConstructeur.newInstance(req);
                if (request.getService() == null & request.getService().equals("")) {
                    resp.setHeader("easysdi-proxy-error-occured", "true");
                    sendException(OWSExceptionReport.TEXT_INVALID_PARAMETER_VALUE, OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "SERVICE", HttpServletResponse.SC_BAD_REQUEST, connector, request.getVersion());
                    return null;
                }
                if (!connector.equals(request.getService())) {
                    resp.setHeader("easysdi-proxy-error-occured", "true");
                    sendException(OWSExceptionReport.TEXT_INVALID_PARAMETER_VALUE, OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "SERVICE", HttpServletResponse.SC_BAD_REQUEST, connector, request.getVersion());
                    return null;
                }
            } catch (VersionNotSupportedException e) {
                //Problem parsing the request
                logger.error("Version not support", e);
                resp.setHeader("easysdi-proxy-error-occured", "true");
                sendException(OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED, OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "VERSION", HttpServletResponse.SC_INTERNAL_SERVER_ERROR, connector, highestversion);
                return null;
            } catch (ClassNotFoundException e) {
                //Class request does not exist, keep going on (it can be the case for old version connector)
            } catch (NoSuchMethodException e) {
                //The constructor does not exist, keep going on
            } catch (InstantiationException e) {
                //Problem parsing the request
                resp.setHeader("easysdi-proxy-error-occured", "true");
                logger.error("Problem parsing request in OgcProxyServlet: ", e);
                sendException("Problem parsing request.", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR, connector, highestversion);
                return null;
            } catch (IllegalAccessException e) {
                //Problem parsing the request
                resp.setHeader("easysdi-proxy-error-occured", "true");
                logger.error("Problem parsing request in OgcProxyServlet: ", e);
                sendException("Problem parsing request", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR, connector, highestversion);
                return null;
            } catch (java.lang.reflect.InvocationTargetException e) {
                if (e.getTargetException() instanceof VersionNotSupportedException) {
                    //Problem parsing the request
                    resp.setHeader("easysdi-proxy-error-occured", "true");
                    logger.error(OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED, e);
                    sendException(OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED, OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "VERSION", HttpServletResponse.SC_INTERNAL_SERVER_ERROR, connector, highestversion);
                } else {
                    //Problem parsing the request
                    resp.setHeader("easysdi-proxy-error-occured", "true");
                    logger.error("Problem parsing request in OgcProxyServlet: ", e);
                    sendException("Problem parsing request", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR, connector, highestversion);
                }
                return null;
            } catch (IllegalArgumentException e) {
                //Problem parsing the request
                resp.setHeader("easysdi-proxy-error-occured", "true");
                logger.error("Problem parsing request in OgcProxyServlet: ", e);
                sendException("Problem parsing request", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR, connector, highestversion);
                return null;
            } catch (ProxyServletException e) {
                resp.setHeader("easysdi-proxy-error-occured", "true");
                logger.error("ProxyServletException in OgcProxyServlet: ", e.toString());
                sendException(e.getMessage(), e.getCode(), e.getLocator(), e.getHttpCode(), connector, null);
                return null;
            }

            //Add the version to get the complete class name
            String className = packagename + "ProxyServlet";
            String reqVersion = null;

            if (request != null) {
                //Check if the requested version is supported by the service
                Boolean found = false;
                if (request.getVersion() != null) {
                    for (SdiSysServicecompliance compliance : virtualService.getSdiSysServicecompliances()) {
                        String value = compliance.getSdiSysServiceversion().getValue();
                        if (value.equals(request.getVersion())) {
                            found = true;
                            //ProxyRequest checks if the current requested operation is supported by the virtualservice loaded and defined by its compliances.
                            if (!request.setServiceCompliance(compliance)) {
                                sendException(OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED, OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_NOT_IMPLEMENTED, connector, request.getVersion());
                                return null;
                            }
                            break;
                        }
                    }
                }

                if (request.getOperation().equalsIgnoreCase("GetCapabilities")) {
                    //GetCapabilities can be perform without version parameter. the highest version supported by the service is used, as recommanded in the OGC standard
                    if (request.getVersion() == null || !found) {
                        reqVersion = highestversion;
                        //ProxyRequest checks if the current requested operation is supported by the virtualservice loaded and defined by its compliances.
                        if (!request.setServiceCompliance(highestServicecompliance)) {
                            sendException(OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED, OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_NOT_IMPLEMENTED, connector, request.getVersion());
                            return null;
                        }
                    } else {
                        reqVersion = request.getVersion();
                    }
                } else {
                    //Others operations are rejected if the version resquested is not supported
                    if (!found) {
                        logger.error(OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED);
                        sendException(OWSExceptionReport.TEXT_VERSION_NOT_SUPPORTED, OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "VERSION", HttpServletResponse.SC_BAD_REQUEST, connector, request.getVersion());
                        return null;
                    } else {
                        reqVersion = request.getVersion();
                    }
                }

                request.setVersion(reqVersion);
                className += reqVersion.replaceAll("\\.", "");
            }

            //CSW
            if (connector.equals("CSW")) {
                className = packagename + "ProxyServlet";
            }

            //WFS connector doesn't have yet a request handler
            if (request != null) {
                //Check if the current requested operation is allowed by the loaded policy
                if (!request.isOperationAllowedByPolicy(policy)) {
                    logger.error(OWSExceptionReport.TEXT_OPERATION_NOT_ALLOWED);
                    sendException(OWSExceptionReport.TEXT_OPERATION_NOT_ALLOWED, OWSExceptionReport.CODE_OPERATION_NOT_ALLOWED, "REQUEST", HttpServletResponse.SC_NOT_IMPLEMENTED, connector, request.getVersion());
                    return null;
                }
            }

            try {
                //Instantiate the servlet class
                Class<?> classe = Class.forName(className);
                @SuppressWarnings("rawtypes")
                Class[] intArgsClass = new Class[]{ProxyServletRequest.class, SdiVirtualservice.class, SdiPolicy.class, ApplicationContext.class};
                Constructor<?> constructeur = classe.getConstructor(intArgsClass);
                Object[] intArgs = new Object[]{request, virtualService, policy, context};
                ProxyServlet ps = (ProxyServlet) constructeur.newInstance(intArgs);
                logger.info("OgcProxyServlet Proxy Servlet creation done.");
                return ps;
            } catch (Exception e) {
                logger.error("Problem occured in configuration parsing and/or logging settings.", e);
                resp.setHeader("easysdi-proxy-error-occured", "true");
                sendException("Problem occured while instanciate the virtual service.", OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "SERVICE", HttpServletResponse.SC_BAD_REQUEST, connector, null);
                return null;
            }
        } catch (Exception e) {
            //Enable to instanciate the request proxy servlet class due to bad service or bad version parameter
            logger.error("Invalid service and/or version.", e);
            resp.setHeader("easysdi-proxy-error-occured", "true");
            sendException("Invalid service and/or version.", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_INTERNAL_SERVER_ERROR, connector, null);
            return null;
        }
    }

    /**
     * @param e
     * @param servletClass
     * @param version
     */
    public void sendException(String message, String code, String locator, Integer httpcode, String connector, String version) {
        try {
            logger.error("OgcProxyServlet sends exception", message);
            if (connector.equalsIgnoreCase("WMS")) {
                try {
                    Class<?> supportedClasse = Class.forName("org.easysdi.proxy.wms.v" + version.replace(".", "") + ".WMSExceptionReport" + version.replace(".", ""));
                    Constructor<?> supportedClasseConstructeur = supportedClasse.getConstructor(new Class[]{});
                    WMSExceptionReport exceptionReport = (WMSExceptionReport) supportedClasseConstructeur.newInstance();
                    exceptionReport.sendExceptionReport(servletRequest, servletResponse, message, code, locator, httpcode);
                } catch (Exception ex) {
                    new OWS200ExceptionReport().sendExceptionReport(servletRequest, servletResponse, message, code, locator, httpcode);
                }
            } else if (connector.equalsIgnoreCase("WFS")) {
                new WFSExceptionReport().sendExceptionReport(servletRequest, servletResponse, message, code, locator, httpcode);
            } else if (connector.equalsIgnoreCase("WMTS")) {
                new WMTSExceptionReport100().sendExceptionReport(servletRequest, servletResponse, message, code, locator, httpcode);
            } else if (connector.equalsIgnoreCase("CSW")) {
                new CSWExceptionReport().sendExceptionReport(servletRequest, servletResponse, message, code, locator, httpcode);
            } else {
                new OWS200ExceptionReport().sendExceptionReport(servletRequest, servletResponse, message, code, locator, httpcode);
            }
        } catch (Exception e1) {
            logger.error("Error occured trying to send exception to client.", e1);
        }
    }

    /**
     * *
     * Decrease the number of executed processes used to managed the
     * max-request-number parameter
     *
     * @param req
     * @param maxRequestByUser
     */
    private synchronized void decreaseConnections(HttpServletRequest req, double maxRequestByUser) {
        // can handle each incoming request
        if (maxRequestByUser < 0) {
            return;
        }
        // We should return an error message, because no request is allowed for
        // this server.
        if (maxRequestByUser == 0) {
            return;
        }
        // No user connected, cannot restrict the connections
        if (SecurityContextHolder.getContext().getAuthentication() == null) {
            return;
        }
        String userPrincipalName = SecurityContextHolder.getContext().getAuthentication().getName();
        if (userPrincipalName == null) {
            return;
        }

        Double executed = new Double(0);

        if (userPrincipalName != null) {
            Double d2 = executionCount.get(userPrincipalName);

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

    /**
     * *
     * When the number of executed processes is greater than the parameter
     * max-request-number for a registered user then waits. The
     * max-request-number parameter allows to restrict the number of requests
     * executed in parallel by one user.
     *
     * @param req the HttpServletRequest
     * @param maxRequestByUser the number of allowed requests
     */
    private void waitWhenConnectionsExceed(HttpServletRequest req, double maxRequestByUser) {
        // can handle each incoming request
        if (maxRequestByUser < 0) {
            return;
        }
        // We should return an error message, because no request is allowed for
        // this server.
        if (maxRequestByUser == 0) {
            return;
        }
        // No user connected, cannot restrict the connections
        if (SecurityContextHolder.getContext().getAuthentication() == null) {
            return;
        }
        String userPrincipalName = SecurityContextHolder.getContext().getAuthentication().getName();
        if (userPrincipalName == null) {
            return;
        }

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
