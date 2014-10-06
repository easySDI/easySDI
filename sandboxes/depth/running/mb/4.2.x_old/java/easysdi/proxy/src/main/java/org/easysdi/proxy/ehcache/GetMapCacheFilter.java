package org.easysdi.proxy.ehcache;

import java.io.UnsupportedEncodingException;
import java.net.URLDecoder;
import java.security.Principal;
import java.util.Collection;
import java.util.Date;
import java.util.Enumeration;
import java.util.List;

import javax.servlet.FilterChain;
import javax.servlet.FilterConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import net.sf.ehcache.Cache;
import net.sf.ehcache.CacheException;
import net.sf.ehcache.CacheManager;
import net.sf.ehcache.Ehcache;
import net.sf.ehcache.Element;
import net.sf.ehcache.constructs.blocking.BlockingCache;
import net.sf.ehcache.constructs.blocking.LockTimeoutException;
import net.sf.ehcache.constructs.blocking.SelfPopulatingCache;
import net.sf.ehcache.constructs.web.AlreadyCommittedException;
import net.sf.ehcache.constructs.web.AlreadyGzippedException;
import net.sf.ehcache.constructs.web.HttpDateFormatter;
import net.sf.ehcache.constructs.web.PageInfo;
import net.sf.ehcache.constructs.web.filter.FilterNonReentrantException;
import net.sf.ehcache.constructs.web.filter.SimpleCachingHeadersPageCachingFilter;

import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.exception.PolicyNotFoundException;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;

public class GetMapCacheFilter extends SimpleCachingHeadersPageCachingFilter {

	private Logger logger = LoggerFactory.getLogger("EasySdiConfigFilter");
	private Cache configCache;
	private ProxyCacheEntryFactory cacheFactory = new ProxyCacheEntryFactory();
	private String operationValue = null;
	private boolean bCache = false;
	//	private String postRequestAsString = ""; 

	public GetMapCacheFilter(CacheManager cm) throws ServletException {
		this.cm = cm;
		doInit(null);
	}

	@SuppressWarnings("unchecked")
	@Override
	protected void doFilter(final HttpServletRequest request, final HttpServletResponse response, final FilterChain chain) throws AlreadyGzippedException,
	AlreadyCommittedException, FilterNonReentrantException, LockTimeoutException, Exception {

		String method = request.getMethod();
		String cacheValue = null;
		operationValue= null;
		bCache = false;
		//		postRequestAsString = "";

		//Request sent by POST are not handle and not cached 
		if(method.equalsIgnoreCase("POST")){
			chain.doFilter(request, response);
			return;
		}

		//		if(method.equalsIgnoreCase("GET")){
		Enumeration<String> operations = request.getParameterNames();
		while (operations.hasMoreElements()) { 
			String operation = (String) operations.nextElement();
			if(operation.equalsIgnoreCase("REQUEST"))
			{
				operationValue = request.getParameter(operation);
			}
			if(operation.equalsIgnoreCase("CACHE"))
			{
				cacheValue = request.getParameter(operation);
			}
		}
		//		}

		//		if(method.equalsIgnoreCase("POST")){
		//			XMLReader xr = XMLReaderFactory.createXMLReader(); 
		//			RequestHandler rh = new RequestHandler();
		//			xr.setContentHandler(rh);
		//			
		//			String input;
		//			StringBuffer paramSB = new StringBuffer();
		//			
		//			BufferedReader in = new BufferedReader(new InputStreamReader(request.getInputStream()));
		//			while ((input = in.readLine()) != null) {
		//				paramSB.append(input);
		//			}
		//			in.close();
		//			postRequestAsString = paramSB.toString();
		//			
		//			xr.parse(new InputSource(new InputStreamReader(new ByteArrayInputStream(postRequestAsString.toString().getBytes()))));
		//			operationValue = rh.getOperation();
		//			
		//			postRequestAsString = postRequestAsString.replace(" ", "");
		//		}

		if(("GetMap").equalsIgnoreCase(operationValue))
		{
			//Get Vendor specific CACHE
			bCache = Boolean.parseBoolean(cacheValue);
			if(bCache)
				super.doFilter(request, response, chain);
			else
				chain.doFilter(request, response);
		}
		else if (("GetRecords").equalsIgnoreCase(operationValue) ||
				("GetTile").equalsIgnoreCase(operationValue)||
				("GetCapabilities").equalsIgnoreCase(operationValue)||
				("DescribeRecord").equalsIgnoreCase(operationValue)||
				("GetRecordById").equalsIgnoreCase(operationValue)||
				("GetFeature").equalsIgnoreCase(operationValue) ||
				("GetFeatureInfo").equalsIgnoreCase(operationValue)||
				("Transaction").equalsIgnoreCase(operationValue))		{
			chain.doFilter(request, response);
		}
		else
		{
			super.doFilter(request, response, chain);
		}
	}

	@Override
	public void doInit(FilterConfig filterConfig) throws CacheException {
		synchronized (this.getClass()) {
			if (blockingCache == null) {
				final String localCacheName = getCacheName();
				Ehcache cache = getCacheManager().getEhcache(localCacheName);
				if (!(cache instanceof BlockingCache)) {
					SelfPopulatingCache newBlockingCache = new SelfPopulatingCache(cache, cacheFactory);
					newBlockingCache.getCacheManager().replaceCacheWithDecoratedCache(cache, newBlockingCache);
				}
				blockingCache = (BlockingCache) getCacheManager().getEhcache(localCacheName);
				Integer blockingTimeoutMillis = 60000;
				if (blockingTimeoutMillis != null && blockingTimeoutMillis > 0) {
					blockingCache.setTimeoutMillis(blockingTimeoutMillis);
				}
			}
		}
	}

	@Override
	protected String calculateKey(HttpServletRequest httpRequest) throws PolicyNotFoundException{
		String servletName = httpRequest.getPathInfo().substring(1);
		configCache = cm.getCache("virtualserviceCache");
		String user = null;
		Principal principal = SecurityContextHolder.getContext().getAuthentication();
		if (principal != null)
			user = principal.getName();
		Element policyE = configCache.get(servletName + user);
		if(policyE == null)
		{
			//No policy available
			throw new PolicyNotFoundException("No policy found.");
		}
		SdiPolicy policy = (SdiPolicy) policyE.getValue();
		StringBuffer stringBuffer = new StringBuffer();
		String url;
		try {
			url = URLDecoder.decode(httpRequest.getQueryString(), "utf-8");
		} catch (UnsupportedEncodingException e) {
			url = httpRequest.getQueryString();
		}
		stringBuffer.append(policy.hashCode()).append(url);
		String key = stringBuffer.toString();
		return key;
	}

	@Override
	protected CacheManager getCacheManager() {
		return cm;
	}

	@Override
	protected String getCacheName() {
		return "getMapCache";
	}

	@Override
	protected PageInfo buildPageInfo(final HttpServletRequest request, final HttpServletResponse response, final FilterChain chain) throws Exception {
		boolean cacheAllowed = false;

		//If it's a GetMap operation, vendor specific parameter CACHE prevails upon user's role
		if(("GetMap").equalsIgnoreCase(operationValue)){
			cacheAllowed = bCache;
		}else{
			//HVH - 01.12.2010
			//To allow the anonymous user to be handled as others users, we need to get throw the SecurityContextHolder
			//to get the Authentication (httpRequest.getUserPrincipal() returns null in the case of anonymous authentication).
			//For the same reasons, we can't use httpRequest.isUserInRole() which always returns false for anonymous user.
			Authentication  principal = SecurityContextHolder.getContext().getAuthentication();
			if ((principal == null)) {
				cacheAllowed = false;
			}
			Collection<GrantedAuthority> authorities = (Collection<GrantedAuthority>)principal.getAuthorities();
			if (authorities == null) {
				cacheAllowed = false;
			}
			for (GrantedAuthority grantedAuthority : authorities) {
				if (("EASYSDI_CACHE").equals(grantedAuthority.getAuthority())) {
					cacheAllowed = true;
					break;
				}
			} 
		}


		final String key = calculateKey(request);
		PageInfo pageInfo = null;
		String originalThreadName = Thread.currentThread().getName();
		try {
			checkNoReentry(request);
			Element element = blockingCache.get(key);
			if (element == null || element.getObjectValue() == null) {
				try {
					logger.debug("Page is not cached. Build the response, cache it, and it send it to client.");
					// Page is not cached - build the response, cache it, and
					// send to client
					pageInfo = buildPage(request, response, chain);
					if (pageInfo.isOk() && !response.containsHeader("easysdi-proxy-error-occured")) {
						logger.debug("PageInfo ok. Adding to cache " + blockingCache.getName() + " with key " + key);
						if (cacheAllowed) {
							blockingCache.put(new Element(key, pageInfo));
							blockingCache.flush();
						}
					} else {
						logger.debug("PageInfo was not ok(200). Putting null into cache " + blockingCache.getName() + " with key " + key);
						blockingCache.put(new Element(key, null));
						blockingCache.flush();
					}
				} catch (final Throwable throwable) {
					// Must unlock the cache if the above fails. Will be logged
					// at Filter
					if (cacheAllowed) {
						blockingCache.put(new Element(key, null));
						blockingCache.flush();
					}
					throw new Exception(throwable);
				}
			} else {
				logger.debug("Page is already cached. Send it to client.");
				pageInfo = (PageInfo) element.getObjectValue();
			}
		} catch (LockTimeoutException e) {
			// do not release the lock, because you never acquired it
			throw e;
		} finally {
			Thread.currentThread().setName(originalThreadName);
		}
		return pageInfo;
	}

	@SuppressWarnings("unchecked")
	@Override
	protected PageInfo buildPage(HttpServletRequest request, HttpServletResponse response, FilterChain chain) throws AlreadyGzippedException, Exception {
		PageInfo pageInfo = super.buildPage(request, response, chain);
		if (!response.containsHeader("easysdi-proxy-error-occured")) {
			// add expires and last-modified headers
			Date now = new Date();
			List<String[]> headers = pageInfo.getResponseHeaders();
			HttpDateFormatter httpDateFormatter = new HttpDateFormatter();
			String lastModified = httpDateFormatter.formatHttpDate(pageInfo.getCreated());
			long ttlMilliseconds = calculateTimeToLiveMilliseconds();
			headers.add(new String[] { "Last-Modified", lastModified });
			headers.add(new String[] { "Expires", httpDateFormatter.formatHttpDate(new Date(now.getTime() + ttlMilliseconds)) });
			headers.add(new String[] { "Cache-Control", "max-age=" + ttlMilliseconds / MILLISECONDS_PER_SECOND });
			headers.add(new String[] { "ETag", generateEtag(ttlMilliseconds) });
		}
		return pageInfo;
	}

	private String generateEtag(long ttlMilliseconds) {
		StringBuffer stringBuffer = new StringBuffer();
		Long eTagRaw = System.currentTimeMillis() + ttlMilliseconds;
		String eTag = stringBuffer.append("\"").append(eTagRaw).append("\"").toString();
		return eTag;
	}

	private static final int MILLISECONDS_PER_SECOND = 1000;

	private CacheManager cm;

}
