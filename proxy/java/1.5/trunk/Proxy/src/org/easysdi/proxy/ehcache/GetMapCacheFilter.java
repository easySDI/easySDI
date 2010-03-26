package org.easysdi.proxy.ehcache;

import java.io.UnsupportedEncodingException;
import java.net.URLDecoder;

import javax.servlet.FilterChain;
import javax.servlet.FilterConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import net.sf.ehcache.CacheException;
import net.sf.ehcache.CacheManager;
import net.sf.ehcache.Ehcache;
import net.sf.ehcache.Element;
import net.sf.ehcache.constructs.blocking.BlockingCache;
import net.sf.ehcache.constructs.blocking.LockTimeoutException;
import net.sf.ehcache.constructs.web.AlreadyCommittedException;
import net.sf.ehcache.constructs.web.AlreadyGzippedException;
import net.sf.ehcache.constructs.web.PageInfo;
import net.sf.ehcache.constructs.web.filter.FilterNonReentrantException;
import net.sf.ehcache.constructs.web.filter.SimpleCachingHeadersPageCachingFilter;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

public class GetMapCacheFilter extends SimpleCachingHeadersPageCachingFilter {

	private static final Logger LOG = LoggerFactory.getLogger(GetMapCacheFilter.class);

	public GetMapCacheFilter(CacheManager cm) throws ServletException {
		this.cm = cm;
		doInit(null);
	}

	@Override
	public void doInit(FilterConfig filterConfig) throws CacheException {
		synchronized (this.getClass()) {
			if (blockingCache == null) {
				final String localCacheName = getCacheName();
				Ehcache cache = getCacheManager().getEhcache(localCacheName);
				if (!(cache instanceof BlockingCache)) {
					// decorate and substitute
					BlockingCache newBlockingCache = new BlockingCache(cache);
					getCacheManager().replaceCacheWithDecoratedCache(cache, newBlockingCache);
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
	protected void doFilter(final HttpServletRequest request, final HttpServletResponse response, final FilterChain chain) throws AlreadyGzippedException,
			AlreadyCommittedException, FilterNonReentrantException, LockTimeoutException, Exception {
		boolean cache = true;
		String req = request.getParameter("REQUEST");
		if (req == null)
			req = request.getParameter("request");
		if ("getmap".equalsIgnoreCase(req)) {
			request.setAttribute("includeUser", Boolean.TRUE);
			String width = request.getParameter("WIDTH");
			if (width == null)
				width = request.getParameter("width");
			String height = request.getParameter("HEIGHT");
			if (height == null)
				height = request.getParameter("height");
			cache = ("256".equals(width) && "256".equals(height));
		} else if ("getlegendgraphic".equalsIgnoreCase(req))
			cache = true;
		else
			cache = false;
		if (cache)
			super.doFilter(request, response, chain);
		else
			chain.doFilter(request, response);
	}

	@Override
	protected String calculateKey(HttpServletRequest httpRequest) {
		StringBuffer stringBuffer = new StringBuffer();
		String url;
		try {
			url = URLDecoder.decode(httpRequest.getQueryString(), "utf-8");
		} catch (UnsupportedEncodingException e) {
			url = httpRequest.getQueryString();
		}
		if (httpRequest.getAttribute("includeUser") != null)
			stringBuffer.append(httpRequest.getUserPrincipal().getName()).append(":");
		stringBuffer.append(httpRequest.getMethod()).append(":").append(httpRequest.getRequestURI()).append(url);

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
		// Look up the cached page
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
					if (pageInfo.isOk()) {
						if (LOG.isDebugEnabled()) {
							LOG.debug("PageInfo ok. Adding to cache " + blockingCache.getName() + " with key " + key);
						}
						blockingCache.put(new Element(key, pageInfo));
						blockingCache.flush();
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
					blockingCache.put(new Element(key, null));
					blockingCache.flush();
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

	private CacheManager cm;

}
