package org.easysdi.proxy.ehcache;

import java.io.UnsupportedEncodingException;
import java.net.URLDecoder;
import java.util.Collection;
import java.util.Enumeration;
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
import net.sf.ehcache.constructs.blocking.SelfPopulatingCache;
import net.sf.ehcache.constructs.web.AlreadyCommittedException;
import net.sf.ehcache.constructs.web.AlreadyGzippedException;
import net.sf.ehcache.constructs.web.PageInfo;
import net.sf.ehcache.constructs.web.filter.FilterNonReentrantException;
import net.sf.ehcache.constructs.web.filter.SimpleCachingHeadersPageCachingFilter;
import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiPolicyHome;
import org.easysdi.proxy.domain.SdiUser;
import org.easysdi.proxy.domain.SdiUserHome;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.domain.SdiVirtualserviceHome;
import org.easysdi.proxy.exception.InvalidServiceNameException;
import org.easysdi.proxy.exception.PolicyNotFoundException;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;

public final class OGCOperationBasedCacheFilter extends SimpleCachingHeadersPageCachingFilter {

    private final Logger logger = LoggerFactory.getLogger("EasySdiConfigFilter");
    private final ProxyCacheEntryFactory cacheFactory = new ProxyCacheEntryFactory();
    private String operationValue = null;
//    private static final int MILLISECONDS_PER_SECOND = 1000;
    
    private final CacheManager cm;
    @Autowired
    private final SdiVirtualserviceHome sdiVirtualserviceHome;
    @Autowired
    private final SdiPolicyHome sdiPolicyHome;
    @Autowired
    private final SdiUserHome sdiUserHome;

    public OGCOperationBasedCacheFilter(CacheManager cm, SdiVirtualserviceHome sdiVirtualserviceHome, SdiPolicyHome sdiPolicyHome, SdiUserHome sdiUserHome) throws ServletException {
        this.cm = cm;
        this.sdiVirtualserviceHome = sdiVirtualserviceHome;
        this.sdiPolicyHome = sdiPolicyHome;
        this.sdiUserHome = sdiUserHome;
        doInit(null);
    }

    @SuppressWarnings("unchecked")
    @Override
    protected void doFilter(final HttpServletRequest request, final HttpServletResponse response, final FilterChain chain) throws AlreadyGzippedException,
            AlreadyCommittedException, FilterNonReentrantException, LockTimeoutException, Exception {

        String method = request.getMethod();
        operationValue = null;

        //Request sent by POST are not handle and not cached 
        if (method.equalsIgnoreCase("POST")) {
            chain.doFilter(request, response);
            return;
        }

        Enumeration<String> operations = request.getParameterNames();
        while (operations.hasMoreElements()) {
            String operation = (String) operations.nextElement();
            if (operation.equalsIgnoreCase("REQUEST")) {
                operationValue = request.getParameter(operation);
            }
        }

        if (operationValue == null || operationValue.equals("")) {
            chain.doFilter(request, response);
            return;
        }

        if (("GetCapabilities").equalsIgnoreCase(operationValue)
                || ("DescribeRecord").equalsIgnoreCase(operationValue)
                || ("DescribeFeatureType").equalsIgnoreCase(operationValue)) {
            //Request response is put in cache
            super.doFilter(request, response, chain);
        } else {
            //No cache for this request
            chain.doFilter(request, response);
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
                if (blockingTimeoutMillis > 0) {
                    blockingCache.setTimeoutMillis(blockingTimeoutMillis);
                }
            }
        }
    }

    @Override
    protected String calculateKey(HttpServletRequest httpRequest) throws PolicyNotFoundException, InvalidServiceNameException {
        String servletName = httpRequest.getServletPath().substring(1);

        String username = null;
        Authentication principal = SecurityContextHolder.getContext().getAuthentication();
        if (principal != null) {
            username = principal.getName();
        }
        Collection<GrantedAuthority> authorities = (Collection<GrantedAuthority>) principal.getAuthorities();

        SdiUser user = sdiUserHome.findByUserName(username);
        Integer id = null;
        if (user != null) {
            id = user.getId();
        }
        SdiVirtualservice virtualservice = sdiVirtualserviceHome.findByAlias(servletName);

        if (virtualservice == null) {
            throw new InvalidServiceNameException("No service found");
        }
        SdiPolicy policy = sdiPolicyHome.findByVirtualServiceAndUser(virtualservice.getId(), id, authorities);
        if (policy == null) {
            throw new PolicyNotFoundException("No policy found.");
        }
        StringBuilder stringBuffer = new StringBuilder();
        String url;
        try {
            url = URLDecoder.decode(httpRequest.getQueryString(), "utf-8");
        } catch (UnsupportedEncodingException e) {
            url = httpRequest.getQueryString();
        }
        stringBuffer.append(policy.getId().toString()).append(url);
        String key = stringBuffer.toString();
        return key;
    }

    @Override
    protected CacheManager getCacheManager() {
        return cm;
    }

    @Override
    protected String getCacheName() {
        return "operationBasedCache";
    }

    @Override
    protected PageInfo buildPageInfo(final HttpServletRequest request, final HttpServletResponse response, final FilterChain chain)
            throws Exception {
        
        PageInfo pageInfo = super.buildPageInfo(request, response, chain);
        //If an exception was raised during request treatment and properly catched, 
        //response header contains "easysdi-proxy-error-occured".
        //This response must be removed from the cache (was put in it in the call of super.buildPageInfo(...))
        if(response.containsHeader("easysdi-proxy-error-occured")){
            final String key = calculateKey(request);
            logger.debug("Exception occured in easySDI Proxy. Putting null into cache " + blockingCache.getName() + " with key " + key);
            blockingCache.put(new Element(key, null));
        }
        return pageInfo;
    }
    
//    //Originally used to overwrite headers information in case of "easysdi-proxy-error-occured"
//    //but seems to be useless...
//    @SuppressWarnings("unchecked")
//    @Override
//    protected PageInfo buildPage(HttpServletRequest request, HttpServletResponse response, FilterChain chain) throws AlreadyGzippedException, Exception {
//        PageInfo pageInfo = super.buildPage(request, response, chain);
//        if (!response.containsHeader("easysdi-proxy-error-occured")) {
//            // add expires and last-modified headers
//            Date now = new Date();
//            List<String[]> headers = new ArrayList<String[]>(pageInfo.getResponseHeaders());
//            HttpDateFormatter httpDateFormatter = new HttpDateFormatter();
//            String lastModified = httpDateFormatter.formatHttpDate(pageInfo.getCreated());
//            long ttlMilliseconds = calculateTimeToLiveMilliseconds();
//            headers.add(new String[] { "Last-Modified", lastModified });
//            headers.add(new String[] { "Expires", httpDateFormatter.formatHttpDate(new Date(now.getTime() + ttlMilliseconds)) });
//            headers.add(new String[] { "Cache-Control", "max-age=" + ttlMilliseconds / MILLISECONDS_PER_SECOND });
//            headers.add(new String[] { "ETag", generateEtag(ttlMilliseconds) });
//        }
//        return pageInfo;
//    }
//
//    private String generateEtag(long ttlMilliseconds) {
//        StringBuffer stringBuffer = new StringBuffer();
//        Long eTagRaw = System.currentTimeMillis() + ttlMilliseconds;
//        String eTag = stringBuffer.append("\"").append(eTagRaw).append("\"").toString();
//        return eTag;
//    }

}
