package org.easysdi.proxy.security;

import java.io.IOException;
import java.util.Map;

import javax.servlet.FilterChain;
import javax.servlet.ServletException;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;
import javax.servlet.http.Cookie;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import net.sf.ehcache.Cache;
import net.sf.ehcache.CacheManager;
import net.sf.ehcache.Element;

import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.authentication.AnonymousAuthenticationToken;
import org.springframework.security.authentication.AuthenticationDetailsSource;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.AuthenticationException;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.web.AuthenticationEntryPoint;
import org.springframework.security.web.authentication.NullRememberMeServices;
import org.springframework.security.web.authentication.RememberMeServices;
import org.springframework.security.web.authentication.WebAuthenticationDetailsSource;
import org.springframework.util.Assert;
import org.springframework.web.filter.GenericFilterBean;
import org.easysdi.proxy.domain.Users;
import org.easysdi.proxy.domain.UsersHome;

public class JoomlaCookieAuthenticationFilter extends GenericFilterBean {

    private AuthenticationDetailsSource authenticationDetailsSource = new WebAuthenticationDetailsSource();
    private RememberMeServices rememberMeServices = new NullRememberMeServices();
    private boolean ignoreFailure = false;
    private String credentialsCharset = "UTF-8";
    
    @Autowired
    private SessionFactory sessionFactory;
    @Autowired
    private CacheManager cacheManager;
    @Autowired
    private AuthenticationManager authenticationManager;
    @Autowired
    private AuthenticationEntryPoint basicAuthenticationEntryPoint;
    @Autowired
    private UsersHome usersHome;
     
    private Cache userCache;

    @Override
    public void afterPropertiesSet() 
    {
		Assert.notNull(this.authenticationManager, "An AuthenticationManager is required");
	
		if (!isIgnoreFailure()) {
		    Assert.notNull(this.basicAuthenticationEntryPoint, "An AuthenticationEntryPoint is required");
		}
    }

  public void doFilter(ServletRequest req, ServletResponse res, FilterChain chain) throws IOException, ServletException 
    {
		final HttpServletRequest request = (HttpServletRequest) req;
		final HttpServletResponse response = (HttpServletResponse) res;
		Cookie[] cookies = request.getCookies();
	
		//case : request from joomla component 
		if  ( 	request.getHeader("Authorization") == null && 
				(request.getHeader("Referer") != null && request.getHeader("Referer").contains("com_easysdi_map") || cookies != null)
			) 
		{
			final boolean debug = logger.isDebugEnabled();
		    
			Users user = null;
		    if (cookies != null)
		    {
				for (Cookie cookie : cookies) {
					String sessionKey = cookie.getValue();
					user = usersHome.findBySession(sessionKey);
					if(user != null)
						break;
				}
		    }
	
		    //Case : request from a front-end component with no logged user --> use a guest account
		    if(user != null && user.getUsername() == null){
		    		user = usersHome.findGuest();
		    }
	
		    //Joomla user found, then authenticate
		    if (user != null && user.getUsername() != null) {
				if (authenticationIsRequired(user.getUsername())) {
				    UsernamePasswordAuthenticationToken authRequest = new UsernamePasswordAuthenticationToken(user.getUsername(), user.getPassword());
				    authRequest.setDetails(authenticationDetailsSource.buildDetails(request));
		
				    Authentication authResult;
		
				    try {
				    	authResult = authenticationManager.authenticate(authRequest);
				    } catch (AuthenticationException failed) {
				    	// Authentication failed
						if (debug) {
						    logger.debug("Authentication request for user: " + user.getUsername() + " failed: " + failed.toString());
						}
			
						SecurityContextHolder.getContext().setAuthentication(null);
						rememberMeServices.loginFail(request, response);
						onUnsuccessfulAuthentication(request, response, failed);
			
						if (ignoreFailure) {
						    chain.doFilter(request, response);
						} else {
						    basicAuthenticationEntryPoint.commence(request, response, failed);
						}
						return;
				    }
		
				    // Authentication success
				    if (debug) {
				    	logger.debug("Authentication success: " + authResult.toString());
				    }
		
				    SecurityContextHolder.getContext().setAuthentication(authResult);
				    rememberMeServices.loginSuccess(request, response, authResult);
				    onSuccessfulAuthentication(request, response, authResult);
				}
		    }
		}
		
		chain.doFilter(request, response);
    }

    private boolean authenticationIsRequired(String username) {
		// Only reauthenticate if username doesn't match SecurityContextHolder
		// and user isn't authenticated
		// (see SEC-53)
		Authentication existingAuth = SecurityContextHolder.getContext().getAuthentication();
	
		if (existingAuth == null || !existingAuth.isAuthenticated()) {
		    return true;
		}
	
		// Limit username comparison to providers which use usernames (ie
		// UsernamePasswordAuthenticationToken)
		// (see SEC-348)
	
		if (existingAuth instanceof UsernamePasswordAuthenticationToken && !existingAuth.getName().equals(username)) {
		    return true;
		}
	
		// Handle unusual condition where an AnonymousAuthenticationToken is
		// already present
		// This shouldn't happen very often, as BasicProcessingFitler is meant
		// to be earlier in the filter
		// chain than AnonymousAuthenticationFilter. Nevertheless, presence of
		// both an AnonymousAuthenticationToken
		// together with a BASIC authentication request header should indicate
		// reauthentication using the
		// BASIC protocol is desirable. This behaviour is also consistent with
		// that provided by form and digest,
		// both of which force re-authentication if the respective header is
		// detected (and in doing so replace
		// any existing AnonymousAuthenticationToken). See SEC-610.
		if (existingAuth instanceof AnonymousAuthenticationToken) {
		    return true;
		}
	
		return false;
    }

    protected void onSuccessfulAuthentication(HttpServletRequest request, HttpServletResponse response, Authentication authResult) throws IOException {
    }

    protected void onUnsuccessfulAuthentication(HttpServletRequest request, HttpServletResponse response, AuthenticationException failed) throws IOException {
    }

    protected AuthenticationEntryPoint getAuthenticationEntryPoint() {
	return basicAuthenticationEntryPoint;
    }

    public void setAuthenticationEntryPoint(AuthenticationEntryPoint authenticationEntryPoint) {
	this.basicAuthenticationEntryPoint = authenticationEntryPoint;
    }

    protected AuthenticationManager getAuthenticationManager() {
	return authenticationManager;
    }

    public void setAuthenticationManager(AuthenticationManager authenticationManager) {
	this.authenticationManager = authenticationManager;
    }

    protected boolean isIgnoreFailure() {
	return ignoreFailure;
    }

    public void setIgnoreFailure(boolean ignoreFailure) {
	this.ignoreFailure = ignoreFailure;
    }

    public void setAuthenticationDetailsSource(AuthenticationDetailsSource authenticationDetailsSource) {
	Assert.notNull(authenticationDetailsSource, "AuthenticationDetailsSource required");
	this.authenticationDetailsSource = authenticationDetailsSource;
    }

    public void setRememberMeServices(RememberMeServices rememberMeServices) {
	Assert.notNull(rememberMeServices, "rememberMeServices cannot be null");
	this.rememberMeServices = rememberMeServices;
    }

    public void setCredentialsCharset(String credentialsCharset) {
	Assert.hasText(credentialsCharset, "credentialsCharset cannot be null or empty");
	this.credentialsCharset = credentialsCharset;
    }

    protected String getCredentialsCharset(HttpServletRequest httpRequest) {
	return credentialsCharset;
    }

	public SessionFactory getSessionFactory() {
		return sessionFactory;
	}

	public void setSessionFactory(SessionFactory sessionFactory) {
		this.sessionFactory = sessionFactory;
	}
}
