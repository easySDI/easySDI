package org.easysdi.proxy.core;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.security.Principal;
import javax.servlet.FilterChain;
import javax.servlet.ServletException;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Unmarshaller;
import net.sf.ehcache.Cache;
import net.sf.ehcache.CacheManager;
import net.sf.ehcache.Element;
import org.easysdi.proxy.exception.NoAnonymousPolicyFoundException;
import org.easysdi.proxy.exception.PolicyNotFoundException;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.ows.v200.OWS200ExceptionReport;
import org.easysdi.proxy.policy.Policy;
import org.easysdi.proxy.policy.PolicySet;
import org.easysdi.xml.documents.Config;
import org.easysdi.xml.handler.ConfigFileHandler;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.web.filter.GenericFilterBean;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

public class EasySdiConfigFilter extends GenericFilterBean {

	private Cache configCache;
	private Logger logger = LoggerFactory.getLogger("EasySdiConfigFilter");

	public EasySdiConfigFilter(CacheManager cm) {
		configCache = cm.getCache("configCache");
	}

	public void destroy() {

	}

	public void doFilter(ServletRequest req, ServletResponse res, FilterChain chain) throws IOException, ServletException {
		final HttpServletRequest request = (HttpServletRequest) req;
		final HttpServletResponse response = (HttpServletResponse) res;

		if( request.getPathInfo() == null ||  request.getPathInfo().equals("/")){
			StringBuffer out = new StringBuffer() ;
			logger.error("Could not determine proxy request from http request. Service name is missing.");
			out = new OWS200ExceptionReport().generateExceptionReport("Could not determine request from http request. Service name is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "service") ;
			response.setContentType("text/xml; charset=utf-8");
			response.setContentLength(out.length());
			OutputStream os;
			os = response.getOutputStream();
			os.write(out.toString().getBytes());
			os.flush();
			os.close();
			return;
		}

		if(request.getMethod().equalsIgnoreCase("GET")){
			if(request.getParameter("request") == null && request.getParameter("REQUEST") == null && request.getParameter("Request") == null){
				logger.error("Could not determine proxy request from http request. Parameter REQUEST is missing.");
				StringBuffer out = new StringBuffer() ;
				out = new OWS200ExceptionReport().generateExceptionReport("Could not determine proxy request from http request. Parameter REQUEST is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request") ;
				response.setContentType("text/xml; charset=utf-8");
				response.setContentLength(out.length());
				OutputStream os;
				os = response.getOutputStream();
				os.write(out.toString().getBytes());
				os.flush();
				os.close();
				return;
			}
		}else{
			if(request.getContentLength() == 0){
				StringBuffer out = new StringBuffer() ;
				logger.error("Could not determine proxy request from http request. Parameter REQUEST is missing.");
				out = new OWS200ExceptionReport().generateExceptionReport("Could not determine proxy request from http request. Parameter REQUEST is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request") ;

				response.setContentType("text/xml; charset=utf-8");
				response.setContentLength(out.length());

				OutputStream os;
				os = response.getOutputStream();
				os.write(out.toString().getBytes());
				os.flush();
				os.close();
				return;
			}
		}

		if ("/ogc".equals(request.getServletPath())) {
			String servletName = request.getPathInfo().substring(1);
			Config configuration = null;
			try {
				configuration = setConfig(servletName);
				if(configuration == null){
					logger.error("Error occurred during " + servletName + " config initialization : service does not exist.");
					StringBuffer out = new OWS200ExceptionReport().generateExceptionReport("Error occurred during " + servletName + " config initialization : service does not exist.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request") ;
					response.setContentType("text/xml; charset=utf-8");
					response.setContentLength(out.length());
					OutputStream os = response.getOutputStream();
					os.write(out.toString().getBytes());
					os.flush();
					os.close();
					return;
				}
				setPolicySet(configuration, request, servletName);
			}catch (NoAnonymousPolicyFoundException e) {
				logger.error("Error occurred during " + servletName + " config initialization : "+ e.toString());
				response.setStatus(HttpServletResponse.SC_UNAUTHORIZED);
				response.setHeader("WWW-Authenticate", "Basic realm=\"EasySDI Proxy "+configuration.getId()+"\"");
				response.sendError(HttpServletResponse.SC_UNAUTHORIZED,e.toString());
				return;
			} catch (PolicyNotFoundException e) {
				logger.error("Error occurred during " + servletName + " config initialization : " + e.toString());
				StringBuffer out = new OWS200ExceptionReport().generateExceptionReport(e.toString(), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "") ;
				response.setContentType("text/xml; charset=utf-8");
				response.setContentLength(out.length());
				OutputStream os = response.getOutputStream();
				os.write(out.toString().getBytes());
				os.flush();
				os.close();
				return;
			} 
			catch (Exception e) {
				logger.error("Error occurred during " + servletName + " config initialization : " + e.toString());
				StringBuffer out = new OWS200ExceptionReport().generateExceptionReport("Error occurred during " + servletName + " config initialization : "+e.toString(), OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request") ;
				response.setContentType("text/xml; charset=utf-8");
				response.setContentLength(out.length());
				OutputStream os = response.getOutputStream();
				os.write(out.toString().getBytes());
				os.flush();
				os.close();
				return;
			}
		}
		chain.doFilter(request, response);
	}

	private Config setConfig(String servletName) throws SAXException, IOException {
		Config configuration = null;
		String configFile = getServletContext().getInitParameter("configFile");
		logger.debug("Config file " + configFile);
		File configF = new File(configFile).getAbsoluteFile();
		long lastmodified = configF.lastModified();
		Element configE = configCache.get(servletName + "configFile");
		if (configE != null && configE.getVersion() != lastmodified)
			configE = null;
		if (configE == null) {
			logger.debug("Loading " + servletName + " config");
			XMLReader xr = XMLReaderFactory.createXMLReader();
			ConfigFileHandler confHandler = new ConfigFileHandler(servletName);
			InputStream is = new java.io.FileInputStream(configFile);
			xr.setContentHandler(confHandler);
			xr.parse(new InputSource(is));
			configuration = confHandler.getConfig();
			configE = new Element(servletName + "configFile", configuration);
			configE.setVersion(lastmodified);
			configCache.put(configE);
			logger.debug("Config for " + servletName + " is loaded into cache");
		} else
			configuration = (Config) configE.getValue();
		return configuration;
	}

	private void setPolicySet(Config configuration, HttpServletRequest req, String servletName) throws JAXBException, FileNotFoundException, PolicyNotFoundException, NoAnonymousPolicyFoundException {
		String filePath = new File(configuration.getPolicyFile()).getAbsolutePath();
		File policyF = new File(filePath).getAbsoluteFile();
		long plastmodified = policyF.lastModified();

		String user = null;

		//HVH - 01.12.2010
		//To allow the anonymous user to be handled as others users, we need to get throw the SecurityContextHolder
		//to get the Authentication (httpRequest.getUserPrincipal() returns null in the case of anonymous authentication).
		Principal principal = SecurityContextHolder.getContext().getAuthentication();
		//Principal principal = req.getUserPrincipal();

		if (principal != null){
			user = principal.getName();
			logger.debug("Authentication : "+user);
		}

		Element policyE = configCache.get(servletName + user + "policyFile");

		if (policyE != null && policyE.getVersion() != plastmodified)
			policyE = null;

		if (policyE == null) {
			JAXBContext jc = JAXBContext.newInstance(org.easysdi.proxy.policy.PolicySet.class);
			Unmarshaller u = jc.createUnmarshaller();
			PolicySet policySet = null;
			policySet = (PolicySet) u.unmarshal(new FileInputStream(filePath));
			PolicyHelpers ph = new PolicyHelpers(policySet, servletName);
			Policy policy = ph.getPolicy(user, req);
			if (policy != null) {
				policyE = new Element(servletName + user + "policyFile", policy);
				policyE.setVersion(plastmodified);
				configCache.put(policyE);
			}else{
				if (req.getUserPrincipal() == null){
					//Spring Anonymous user is used to perform this request, but not policy defined for it
					throw new NoAnonymousPolicyFoundException("No anomnymous policy found.");

				}else{
					//No policy found for the authenticated user, return an ogc exception.
					throw new PolicyNotFoundException("No policy found for user.");
				}
			}
		}
	}

}
