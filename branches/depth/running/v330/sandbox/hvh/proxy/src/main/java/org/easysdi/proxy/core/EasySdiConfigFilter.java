package org.easysdi.proxy.core;

import java.io.IOException;
import java.util.Collection;
import java.util.Date;

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
import org.easysdi.proxy.domain.SdiUser;
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
			new OWS200ExceptionReport().sendExceptionReport(request, response, "Could not determine request from http request. Service name is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "service", HttpServletResponse.SC_BAD_REQUEST) ;
			return;
		}

		if(request.getMethod().equalsIgnoreCase("GET"))
		{
			if(request.getParameter("request") == null && request.getParameter("REQUEST") == null && request.getParameter("Request") == null){
				logger.error("Could not determine proxy request from http request. Parameter REQUEST is missing.");
				new OWS200ExceptionReport().sendExceptionReport(request, response, "Could not determine proxy request from http request. Parameter REQUEST is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request", HttpServletResponse.SC_BAD_REQUEST) ;
				return;
			}
		}
		else
		{
			if(request.getContentLength() == 0){
				logger.error("Could not determine proxy request from http request. Parameter REQUEST is missing.");
				new OWS200ExceptionReport().sendExceptionReport(request, response, "Could not determine proxy request from http request. Parameter REQUEST is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request", HttpServletResponse.SC_BAD_REQUEST) ;
				return;
			}
		}

		if ("/ogc".equals(request.getServletPath())) {
			String servletName = request.getPathInfo().substring(1);
			SdiVirtualservice virtualservice = null;
			try {
//				Element elements = virtualServiceCache.get(servletName);
//				if(elements == null)
//				{
//					virtualservice = sdiVirtualserviceHome.findByAlias(servletName);
//					if(virtualservice != null)
//					{
//						Element element = new Element(servletName, virtualservice);
//						virtualServiceCache.put(element);
//					}
//				}
//				else
//					virtualservice = (SdiVirtualservice)elements.getValue();
				
				//Use of the 2nd level cache
				virtualservice = sdiVirtualserviceHome.findByAlias(servletName);
				
				if(virtualservice == null){
					logger.error("Error occurred during " + servletName + " service initialization : service does not exist.");
					new OWS200ExceptionReport().sendExceptionReport(request,response, "Error occurred during " + servletName + " service initialization : service does not exist.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request", HttpServletResponse.SC_BAD_REQUEST) ;
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
				
				SdiPolicy policy = null;
//				long start = System.nanoTime();
//				Element elementp = virtualServiceCache.get(servletName+username);
//				if(elementp != null){
//					//Get the policy from the cache
//					policy = (SdiPolicy)elementp.getValue();
//					//Check if this policy is still valid according to its date of validity
//					Date from = policy.getAllowfrom();
//					Date to = policy.getAllowto();
//					Date currentDate = new Date();
//					if (!currentDate.after(from) || !currentDate.before(to))
//					{
//						//Policy is not valid anymore, remove it from the cache
//						policy = null;
//						elementp = null;
//						virtualServiceCache.remove(servletName+username);
//					}
//				}
//				if(elementp == null)
//				{
//					SdiUser user = sdiUserHome.findByUserName(username);
//					Integer id = null;
//					if (user != null)
//						id = user.getId();
//					policy = sdiPolicyHome.findByVirtualServiceAndUser(virtualservice.getId(), id , authorities);
//					Element elementPolicy = new Element(servletName+username, policy);
//					virtualServiceCache.put(elementPolicy);
//				}
//				double elapsedTimeInSec = (System.nanoTime() - start) * 1.0e-9;
//				logger.info("ehcache : "+elapsedTimeInSec);
//				
				
				//Use of the 2nd level cache
//				start = System.nanoTime();
				SdiUser user = sdiUserHome.findByUserName(username);
				Integer id = null;
				if (user != null)
					id = user.getId();
				policy = sdiPolicyHome.findByVirtualServiceAndUser(virtualservice.getId(), id , authorities);
//				elapsedTimeInSec = (System.nanoTime() - start) * 1.0e-9;
//				logger.info("2nd level cache : "+elapsedTimeInSec);
				
				if (policy == null) {
					if (((HttpServletRequest)req).getUserPrincipal() == null){
						//Spring Anonymous user is used to perform this request, but not policy defined for it
						logger.error("Error occurred during " + servletName + " service initialization : No anomnymous policy found.");
						response.setStatus(HttpServletResponse.SC_UNAUTHORIZED);
						response.setHeader("WWW-Authenticate", "Basic realm=\"EasySDI Proxy "+virtualservice.getAlias()+"\"");
						response.sendError(HttpServletResponse.SC_UNAUTHORIZED,"No anomnymous policy found.");
						return;

					}else{
						//No policy found for the authenticated user, return an ogc exception.
						logger.error("Error occurred during " + servletName + " service initialization : No policy found for user.");
						new OWS200ExceptionReport().sendExceptionReport(request,response, "No policy found for user.", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK) ;
						return;
					}
				}
			}
			
			catch (Exception e) {
				logger.error("Error occurred during " + servletName + " service initialization : " + e.toString());
				new OWS200ExceptionReport().sendExceptionReport(request, response, "Error occurred during " + servletName + " service initialization : "+e.toString(), OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request", HttpServletResponse.SC_OK) ;
				return;
			}
		}
		chain.doFilter(request, response);
	}

}
