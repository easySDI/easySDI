package org.easysdi.proxy.core;

import java.io.IOException;
import java.util.Collection;

import javax.servlet.FilterChain;
import javax.servlet.ServletException;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import net.sf.ehcache.Cache;
import net.sf.ehcache.CacheManager;
import net.sf.ehcache.Element;

import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiPolicyHome;
import org.easysdi.proxy.domain.SdiUserHome;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.domain.SdiVirtualserviceHome;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.ows.v200.OWS200ExceptionReport;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.filter.GenericFilterBean;

@Transactional
public class EasySdiConfigFilter extends GenericFilterBean {

	@Autowired
    private CacheManager cacheManager;
	@Autowired
    private SdiVirtualserviceHome sdiVirtualserviceHome;
	@Autowired
    private SdiPolicyHome sdiPolicyHome;
	@Autowired
    private SdiUserHome sdiUserHome;
	
	private Cache virtualServiceCache;
	private Logger logger = LoggerFactory.getLogger("EasySdiConfigFilter");

	public EasySdiConfigFilter(CacheManager cacheManager, SdiVirtualserviceHome sdiVirtualserviceHome, SdiPolicyHome sdiPolicyHome, SdiUserHome sdiUserHome) {
		virtualServiceCache = cacheManager.getCache("virtualserviceCache");
		this.sdiVirtualserviceHome = sdiVirtualserviceHome;
		this.sdiPolicyHome = sdiPolicyHome;
		this.sdiUserHome = sdiUserHome;
	}

	public void destroy() {

	}

	public void doFilter(ServletRequest req, ServletResponse res, FilterChain chain) throws IOException, ServletException {
		final HttpServletRequest request = (HttpServletRequest) req;
		final HttpServletResponse response = (HttpServletResponse) res;

		if( request.getPathInfo() == null ||  request.getPathInfo().equals("/"))
		{
			logger.error("Could not determine proxy request from http request. Service name is missing.");
			new OWS200ExceptionReport().sendExceptionReport(response, "Could not determine request from http request. Service name is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "service") ;
			return;
		}

		if(request.getMethod().equalsIgnoreCase("GET"))
		{
			if(request.getParameter("request") == null && request.getParameter("REQUEST") == null && request.getParameter("Request") == null){
				logger.error("Could not determine proxy request from http request. Parameter REQUEST is missing.");
				new OWS200ExceptionReport().sendExceptionReport(response, "Could not determine proxy request from http request. Parameter REQUEST is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request") ;
				return;
			}
		}
		else
		{
			if(request.getContentLength() == 0){
				logger.error("Could not determine proxy request from http request. Parameter REQUEST is missing.");
				new OWS200ExceptionReport().sendExceptionReport(response, "Could not determine proxy request from http request. Parameter REQUEST is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request") ;
				return;
			}
		}

		if ("/ogc".equals(request.getServletPath())) {
			String servletName = request.getPathInfo().substring(1);
			SdiVirtualservice virtualservice = null;
			try {
				//SdiVirtualService is not put in the cache manually because the query used in the method findByAlias is already cached.
				virtualservice = sdiVirtualserviceHome.findByAlias(servletName);
				if(virtualservice == null){
					logger.error("Error occurred during " + servletName + " config initialization : service does not exist.");
					new OWS200ExceptionReport().sendExceptionReport(response,"Error occurred during " + servletName + " config initialization : service does not exist.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request") ;
					return;
				}
				//To allow the anonymous user to be handled as others users, we need to get throw the SecurityContextHolder to get the Authentication
				//(httpRequest.getUserPrincipal() returns null in the case of anonymous authentication).
				Authentication principal = SecurityContextHolder.getContext().getAuthentication();
				String username = null;
				if (principal != null){
					username = principal.getName();
					logger.debug("Authentication : "+username);
				}
				Collection<GrantedAuthority> authorities = (Collection<GrantedAuthority>)principal.getAuthorities();
				
				SdiPolicy policy = sdiPolicyHome.findByVirtualServiceAndUser(virtualservice.getId(), sdiUserHome.findByUserName(username).getId(), authorities);
				if (policy != null) {
				}else{
					if (((HttpServletRequest)req).getUserPrincipal() == null){
						//Spring Anonymous user is used to perform this request, but not policy defined for it
						logger.error("Error occurred during " + servletName + " config initialization : No anomnymous policy found.");
						response.setStatus(HttpServletResponse.SC_UNAUTHORIZED);
						response.setHeader("WWW-Authenticate", "Basic realm=\"EasySDI Proxy "+virtualservice.getAlias()+"\"");
						response.sendError(HttpServletResponse.SC_UNAUTHORIZED,"No anomnymous policy found.");
						return;

					}else{
						//No policy found for the authenticated user, return an ogc exception.
						logger.error("Error occurred during " + servletName + " config initialization : No policy found for user.");
						new OWS200ExceptionReport().sendExceptionReport(response,"No policy found for user.", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "") ;
						return;
					}
				}
			}
			
			catch (Exception e) {
				logger.error("Error occurred during " + servletName + " config initialization : " + e.toString());
				new OWS200ExceptionReport().sendExceptionReport(response, "Error occurred during " + servletName + " config initialization : "+e.toString(), OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request") ;
				return;
			}
		}
		chain.doFilter(request, response);
	}

//	private SdiVirtualservice getVirtualService(String servletName) throws SAXException, IOException {
//		logger.debug("Servlet name " + servletName);
//		SdiVirtualservice virtualService = null;
//		//Assuming query in findByAlias put the result in cache
//		virtualService = sdiVirtualserviceHome.findByAlias(servletName);
////		Element e = virtualServiceCache.get(servletName+"virtualservice");
////		if(e == null){
////			logger.debug("Loading " + servletName + " virtual service");
////			virtualService = sdiVirtualserviceHome.findByAlias(servletName);
////			if(virtualService == null){
////				logger.debug("Virtual service " + servletName + " does not exist.");
////				return null;
////			}
////			e = new Element (servletName+"virtualservice", virtualService);
////			virtualServiceCache.put(e);
////			logger.debug("Virtual service " + servletName + " is loaded into cache");
////		}
////		else
////		{
////			virtualService = (SdiVirtualservice)e.getValue();
////		}
//		return virtualService;
//	}

//	private void setPolicySet(Config configuration, HttpServletRequest req, String servletName) throws JAXBException, FileNotFoundException, PolicyNotFoundException, NoAnonymousPolicyFoundException {
//		String filePath = new File(configuration.getPolicyFile()).getAbsolutePath();
//		File policyF = new File(filePath).getAbsoluteFile();
//		long plastmodified = policyF.lastModified();
//
//		String user = null;
//
//		//HVH - 01.12.2010
//		//To allow the anonymous user to be handled as others users, we need to get throw the SecurityContextHolder
//		//to get the Authentication (httpRequest.getUserPrincipal() returns null in the case of anonymous authentication).
//		Principal principal = SecurityContextHolder.getContext().getAuthentication();
//		//Principal principal = req.getUserPrincipal();
//
//		if (principal != null){
//			user = principal.getName();
//			logger.debug("Authentication : "+user);
//		}
//
//		Element policyE = configCache.get(servletName + user + "policyFile");
//
//		if (policyE != null && policyE.getVersion() != plastmodified)
//			policyE = null;
//
//		if (policyE == null) { 
//			JAXBContext jc = JAXBContext.newInstance(org.easysdi.proxy.policy.PolicySet.class);
//			Unmarshaller u = jc.createUnmarshaller();
//			PolicySet policySet = null;
//			policySet = (PolicySet) u.unmarshal(new FileInputStream(filePath));
//			PolicyHelpers ph = new PolicyHelpers(policySet, servletName);
//			Policy policy = ph.getPolicy(user, req);
//			if (policy != null) {
//				policyE = new Element(servletName + user + "policyFile", policy);
//				policyE.setVersion(plastmodified);
//				configCache.put(policyE);
//			}else{
//				if (req.getUserPrincipal() == null){
//					//Spring Anonymous user is used to perform this request, but not policy defined for it
//					throw new NoAnonymousPolicyFoundException("No anomnymous policy found.");
//
//				}else{
//					//No policy found for the authenticated user, return an ogc exception.
//					throw new PolicyNotFoundException("No policy found for user.");
//				}
//			}
//		}
//	}

}
