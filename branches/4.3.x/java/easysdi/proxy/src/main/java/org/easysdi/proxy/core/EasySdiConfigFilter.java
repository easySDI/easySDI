package org.easysdi.proxy.core;

import java.io.IOException;
import java.util.Collection;
import javax.servlet.FilterChain;
import javax.servlet.ServletException;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import net.sf.ehcache.CacheManager;
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

    private Logger logger = LoggerFactory.getLogger("EasySdiConfigFilter");

    public EasySdiConfigFilter(CacheManager cacheManager, SdiVirtualserviceHome sdiVirtualserviceHome, SdiPolicyHome sdiPolicyHome, SdiUserHome sdiUserHome) {
        this.sdiVirtualserviceHome = sdiVirtualserviceHome;
        this.sdiPolicyHome = sdiPolicyHome;
        this.sdiUserHome = sdiUserHome;
    }

    @Override
    public void destroy() {
    }

    public void doFilter(ServletRequest req, ServletResponse res, FilterChain chain) throws IOException, ServletException {
        final HttpServletRequest request = (HttpServletRequest) req;
        final HttpServletResponse response = (HttpServletResponse) res;

        if (request.getPathInfo() == null || request.getPathInfo().equals("/")) {
            logger.error("Could not determine proxy request from http request. Service name is missing.");
            new OWS200ExceptionReport().sendExceptionReport(request, response, "Could not determine request from http request. Service name is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "service", HttpServletResponse.SC_BAD_REQUEST);
            return;
        }

        if (request.getMethod().equalsIgnoreCase("GET")) {
            if (request.getParameter("request") == null && request.getParameter("REQUEST") == null && request.getParameter("Request") == null) {
                logger.error("Could not determine proxy request from http request. Parameter REQUEST is missing.");
                new OWS200ExceptionReport().sendExceptionReport(request, response, "Could not determine proxy request from http request. Parameter REQUEST is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request", HttpServletResponse.SC_BAD_REQUEST);
                return;
            }
        } else {
            if (request.getContentLength() == 0) {
                logger.error("Could not determine proxy request from http request. Parameter REQUEST is missing.");
                new OWS200ExceptionReport().sendExceptionReport(request, response, "Could not determine proxy request from http request. Parameter REQUEST is missing.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request", HttpServletResponse.SC_BAD_REQUEST);
                return;
            }
        }

        if ("/ogc".equals(request.getServletPath())) {
            String servletName = request.getPathInfo().substring(1);
            SdiVirtualservice virtualservice = null;
            try {
                //Use of the 2nd level cache
                virtualservice = sdiVirtualserviceHome.findByAlias(servletName);

                if (virtualservice == null) {
                    logger.error("Error occurred during " + servletName + " config initialization : service does not exist.");
                    new OWS200ExceptionReport().sendExceptionReport(request, response, "Error occurred during " + servletName + " config initialization : service does not exist.", OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request", HttpServletResponse.SC_BAD_REQUEST);
                    return;
                }
		//To allow the anonymous user to be handled as others users, we need to get throw the SecurityContextHolder to get the Authentication
                //(httpRequest.getUserPrincipal() returns null in the case of anonymous authentication).
                Authentication principal = SecurityContextHolder.getContext().getAuthentication();
                String username = null;
                if (principal != null) {
                    username = principal.getName();
                    logger.debug("Authentication : " + username);
                }
                Collection<GrantedAuthority> authorities = (Collection<GrantedAuthority>) principal.getAuthorities();

                SdiPolicy policy = null;

                //Use of the 2nd level cache
                SdiUser user = sdiUserHome.findByUserName(username);
                Integer id = null;
                if (user != null) {
                    id = user.getId();
                }
                policy = sdiPolicyHome.findByVirtualServiceAndUser(virtualservice.getId(), id, authorities);

                if (policy == null) {
                    if (((HttpServletRequest) req).getUserPrincipal() == null) {
                        //Spring Anonymous user is used to perform this request, but not policy defined for it
                        logger.error("Error occurred during " + servletName + " config initialization : No public policy found.");
                        response.setStatus(HttpServletResponse.SC_UNAUTHORIZED);
                        response.setHeader("WWW-Authenticate", "Basic realm=\"EasySDI Proxy " + virtualservice.getAlias() + "\"");
                        response.sendError(HttpServletResponse.SC_UNAUTHORIZED, "No public policy found.");
                        return;

                    } else {
                        //No policy found for the authenticated user, return an ogc exception.
                        logger.error("Error occurred during " + servletName + " config initialization : No policy found for user.");
                        new OWS200ExceptionReport().sendExceptionReport(request, response, "No policy found for user.", OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK);
                        return;
                    }
                }
            } catch (Exception e) {
                logger.error("Error occurred during " + servletName + " config initialization : " + e.toString());
                new OWS200ExceptionReport().sendExceptionReport(request, response, "Error occurred during " + servletName + " config initialization : " + e.toString(), OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "request", HttpServletResponse.SC_OK);
                return;
            }
        }
        chain.doFilter(request, response);
    }

}
