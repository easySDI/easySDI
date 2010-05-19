package org.easysdi.proxy.ehcache;

import java.io.UnsupportedEncodingException;
import java.net.URLDecoder;
import java.security.Principal;
import java.util.Date;
import java.util.Iterator;
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

import org.easysdi.proxy.policy.Policy;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;

public class GetMapCacheFilter extends SimpleCachingHeadersPageCachingFilter {

	private static final Logger LOG = LoggerFactory.getLogger(GetMapCacheFilter.class);
	private Cache configCache;
	private ProxyCacheEntryFactory cacheFactory = new ProxyCacheEntryFactory();

	public GetMapCacheFilter(CacheManager cm) throws ServletException {
		this.cm = cm;
		doInit(null);
	}

	@Override
	protected void doFilter(final HttpServletRequest request, final HttpServletResponse response, final FilterChain chain) throws AlreadyGzippedException,
			AlreadyCommittedException, FilterNonReentrantException, LockTimeoutException, Exception {
		String pCache = request.getParameter("CACHE");
		if (pCache == null || "".equals(pCache))
			pCache = request.getParameter("cache");
		boolean cache = Boolean.parseBoolean(pCache);
		if (cache) {
			String req = request.getParameter("REQUEST");
			if (req == null)
				req = request.getParameter("request");
			if ("getfeature".equalsIgnoreCase(req) && "getfeatureinfo".equalsIgnoreCase(req))
				cache = false;
			if (cache)
				super.doFilter(request, response, chain);
			else
				chain.doFilter(request, response);
		} else
			chain.doFilter(request, response);
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
	protected String calculateKey(HttpServletRequest httpRequest) {
		String servletName = httpRequest.getPathInfo().substring(1);
		configCache = cm.getCache("configCache");
		String user = null;
		Principal principal = httpRequest.getUserPrincipal();
		if (principal != null)
			user = principal.getName();
		Element policyE = configCache.get(servletName + user + "policyFile");
		Policy policy = (Policy) policyE.getValue();
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
		Iterator<GrantedAuthority> iterator = SecurityContextHolder.getContext().getAuthentication().getAuthorities().iterator();
		while (iterator.hasNext()) {
			GrantedAuthority authority = iterator.next();
			if ("EASYSDI_CACHE".equals(authority.getAuthority())) {
				cacheAllowed = true;
				break;
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
					// Page is not cached - build the response, cache it, and
					// send to client
					pageInfo = buildPage(request, response, chain);
					if (pageInfo.isOk() && !response.containsHeader("easysdi-proxy-error-occured")) {
						if (LOG.isDebugEnabled()) {
							LOG.debug("PageInfo ok. Adding to cache " + blockingCache.getName() + " with key " + key);
						}
						if (cacheAllowed) {
							blockingCache.put(new Element(key, pageInfo));
							blockingCache.flush();
						}
					} else {
						if (LOG.isDebugEnabled()) {
							LOG.debug("PageInfo was not ok(200). Putting null into cache " + blockingCache.getName() + " with key " + key);
						}
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
