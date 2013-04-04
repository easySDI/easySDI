package org.easysdi.security;

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
import net.sf.json.JSONObject;
import net.sf.json.JSONSerializer;

import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
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
    private JdbcTemplate sjt = null;
    
    @Autowired
    private SessionFactory sessionFactory;
    @Autowired
    private CacheManager cacheManager;
    @Autowired
    private AuthenticationManager authenticationManager;
    @Autowired
    private AuthenticationEntryPoint basicAuthenticationEntryPoint;
    @Autowired
    private UsersHome UsersHome;
    
    @Autowired
    private JoomlaProvider joomlaProvider;
    
    private Cache userCache;

    @Override
    public void afterPropertiesSet() 
    {
		Assert.notNull(this.authenticationManager, "An AuthenticationManager is required");
	
		if (!isIgnoreFailure()) {
		    Assert.notNull(this.basicAuthenticationEntryPoint, "An AuthenticationEntryPoint is required");
		}
    }

    @SuppressWarnings("unchecked")
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
			String sessionKey = null, username = null, password = null;
		    
			final boolean debug = logger.isDebugEnabled();
		    
			userCache = cacheManager.getCache("userCache");
		    Map<String, Object> authenticationPair = null;
		    Users user = null;
		    
		    //Retrieve cookie from cache named 'userCache'
		    Element e = userCache.get(cookies);
		    if (e != null)
		    {
		    	authenticationPair = (Map<String, Object>) e.getValue();
		    }
		    else if (cookies != null)
		    {
				for (Cookie cookie : cookies) {
				    sessionKey = cookie.getValue();
				    
				    if (sessionKey != null){
						user = UsersHome.findBySession(sessionKey);
				    					    		
//						String sql1 = "select u.username, u.password from " + joomlaProvider.getPrefix() + "session s left join " + joomlaProvider.getPrefix()
//						+ "users u " + "on (u.username = s.username) where session_id = ? limit 1";
//						try {
//						    authenticationPair = sjt.queryForMap(sql1, sessionKey);
//						}catch (EmptyResultDataAccessException er){
//						}
				    }
				    if(user != null){
				    	userCache.put(new Element(cookies, user));
				    }
//				    if (authenticationPair != null && authenticationPair.size() > 0){
//						if (authenticationPair.get("username") != null){
//						    userCache.put(new Element(cookies, authenticationPair));
//						    break;
//						}
//				    }
				}
		    }
	
		    //Case : request from a front-end component with no logged user --> use a guest account
		    if(user != null && user.getUsername() == null){
		    	e = userCache.get("guest");
		    	if (e != null){
		    		user = (Users) e.getValue();
		    	}
		    	else{
		    		user = UsersHome.findGuest();
		    		if(user != null)
		    			userCache.put(new Element("guest", user));
		    	}
		    }
		   
		    if (user != null ) {
				username = (String) authenticationPair.get("username");
				password = (String) authenticationPair.get("password");
		    }
	
		    if (debug) {
		    	logger.debug("Joomla Cookie header found for user '" + user.getUsername() + "'");
		    }
	
		    if (username != null) {
			if (authenticationIsRequired(username)) {
			    UsernamePasswordAuthenticationToken authRequest = new UsernamePasswordAuthenticationToken(username, password);
			    authRequest.setDetails(authenticationDetailsSource.buildDetails(request));
	
			    Authentication authResult;
	
			    try {
				authResult = authenticationManager.authenticate(authRequest);
			    } catch (AuthenticationException failed) {
				// Authentication failed
				if (debug) {
				    logger.debug("Authentication request for user: " + username + " failed: " + failed.toString());
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
