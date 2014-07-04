/**
 *
 */
package org.easysdi.proxy.hibernate;

import java.io.IOException;
import java.io.PrintWriter;
import java.lang.reflect.Method;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import net.sf.ehcache.CacheManager;

import org.easysdi.proxy.domain.SdiCswSpatialpolicy;
import org.easysdi.proxy.domain.SdiExcludedattribute;
import org.easysdi.proxy.domain.SdiFeaturetypePolicy;
import org.easysdi.proxy.domain.SdiIncludedattribute;
import org.easysdi.proxy.domain.SdiPhysicalservice;
import org.easysdi.proxy.domain.SdiPhysicalserviceHome;
import org.easysdi.proxy.domain.SdiPhysicalservicePolicy;
import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiPolicyHome;
import org.easysdi.proxy.domain.SdiTilematrixPolicy;
import org.easysdi.proxy.domain.SdiTilematrixsetPolicy;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.domain.SdiVirtualserviceHome;
import org.easysdi.proxy.domain.SdiWmslayerPolicy;
import org.easysdi.proxy.domain.SdiWmtslayerPolicy;
import org.easysdi.proxy.domain.Users;
import org.easysdi.proxy.domain.UsersHome;
import org.hibernate.Cache;
import org.hibernate.SessionFactory;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.context.ApplicationContext;
import org.springframework.web.context.support.WebApplicationContextUtils;

/**
 * @author DEPTH SA
 *
 */
public class ProxyCacheInvalidationServlet extends HttpServlet {

    private static final long serialVersionUID = -5054942482987117794L;
    private Logger logger = LoggerFactory.getLogger("EasySdiConfigFilter");
    private Cache cache;
    private ApplicationContext context;
    private CacheManager cacheManager;
    SessionFactory sessionFactory;
    private String operation;
    private String entityClass;
    private Integer id;
    private String complete;
    private String jsonresponse;

    /* (non-Javadoc)
     * @see javax.servlet.http.HttpServlet#doGet(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        try {
            operation = req.getParameter("operation");
            entityClass = req.getParameter("entityclass");
            id = Integer.parseInt(req.getParameter("id"));
            complete = req.getParameter("complete");
            context = WebApplicationContextUtils.getWebApplicationContext(getServletContext());
            cacheManager = (CacheManager) context.getBean("cacheManager");
            sessionFactory = (SessionFactory) context.getBean("sessionFactory");
            cache = sessionFactory.getCache();

            //Invalidate all cache regions
            if (complete != null && complete.equalsIgnoreCase("TRUE")) {
                //see JoomlaCookieAuthenticationFilter for the use of userCache cache
                if (cacheManager.cacheExists("userCache")) {
                    cacheManager.getCache("userCache").removeAll();
                }

                //see OGCOperationBasedCacheFilter for the use of operationBasedCache cache
                if (cacheManager.cacheExists("operationBasedCache")) {
                    cacheManager.getEhcache("operationBasedCache").removeAll();
                }

                cache.evictDefaultQueryRegion();
                cache.evictEntityRegions();
                cache.evictQueryRegions();
                cache.evictCollectionRegions();

                jsonresponse = "{\"status\": \"OK\", \"message\": \"Complete cache invalidation done.\"}";
            } //Invalidate a specific entity
            else if (entityClass != null && id != null) {
                Method invalidateMethod = this.getClass().getMethod("requestInvalidate" + entityClass, new Class[]{});
                invalidateMethod.invoke(this, new Object[]{});

                //whatever the case, clear the operationBasedCache
                if (cacheManager.cacheExists("operationBasedCache")) {
                    cacheManager.getEhcache("operationBasedCache").removeAll();
                }

                jsonresponse = "{\"status\": \"OK\", \"message\": \"" + entityClass + "#" + id + " cache invalidation done.\"}";
            }

        } catch (Exception e) {
            logger.error(e.getStackTrace().toString());
            jsonresponse = "{\"status\": \"KO\", \"message\": \"" + e.toString() + "\"}";
        } finally {
            //Return the response
            resp.setContentType("application/json");
            // Get the printwriter object from response to write the required json object to the output stream      
            PrintWriter out = resp.getWriter();
            out.print(jsonresponse);
            out.flush();
        }
    }

    public void requestInvalidateSdiPhysicalservice() {
        if (operation != null && operation.equalsIgnoreCase("DELETE")) {
            InvalidateSdiPhysicalServiceCache();
        }
        cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservice.sdiPhysicalserviceServicecompliances", id);
        cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservice.sdiOrganisms", id);
        cache.evictEntity("org.easysdi.proxy.domain.SdiPhysicalservice", id);
    }

    public void requestInvalidateSdiVirtualservice() {
        if (operation != null && operation.equalsIgnoreCase("DELETE")) {
            InvalidateSdiVirtualServiceCache();
        }
        cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiOrganisms", id);
        cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiPhysicalservices", id);
        cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiSysServicecompliances", id);
        cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiVirtualmetadatas", id);
        cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiPolicies", id);
        cache.evictEntity("org.easysdi.proxy.domain.SdiVirtualservice", id);
        //Invalidate query cache (initial loading)
        cache.evictQueryRegion("SdiVirtualServiceQueryCache");
    }

    public void requestInvalidateSdiPolicy() {
        SdiPolicyHome sdiPolicyHome = (SdiPolicyHome) context.getBean("sdiPolicyHome");
        if (operation != null && operation.equalsIgnoreCase("DELETE")) {
            SdiPolicy policy = sdiPolicyHome.findById(id);
            if (policy.getSdiVirtualservice() != null) {
                cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiPolicies", policy.getSdiVirtualservice().getId());
            }
            cache.evictEntity("org.easysdi.proxy.domain.SdiVirtualservice", policy.getSdiVirtualservice().getId());
        }

        InvalidateSdiPolicyCache(id, sdiPolicyHome);
        //Invalidate query cache (initial loading)
        cache.evictQueryRegion("SdiPolicyQueryCache");
    }

    public void requestInvalidateSdiUser() {
        cache.evictCollection("org.easysdi.proxy.domain.SdiUser.sdiUserRoleOrganisms", id);
        cache.evictEntity("org.easysdi.proxy.domain.SdiUser", id);
        cache.evictQueryRegion("SdiUserQueryCache");
        if (cacheManager.cacheExists("userCache")) {
            cacheManager.getCache("userCache").removeAll();
        }
    }

    public void requestInvalidateUsers() {
        UsersHome usersHome = (UsersHome) context.getBean("usersHome");
        Users users = usersHome.findById(id);
        Integer userid = id;
        try {
            id = users.getSdiUsers().iterator().next().getId();
            requestInvalidateSdiUser();
        } catch (Exception e) {
            //sdiUser not found
        }
        cache.evictEntity("org.easysdi.proxy.domain.Users", userid);
        cache.evictQueryRegion("UsersQueryCache");
    }

    public void requestInvalidateExtensions() {
        cache.evictEntity("org.easysdi.proxy.domain.Extensions", id);
        cache.evictQueryRegion("ExtensionsQueryCache");
        if (cacheManager.cacheExists("userCache")) {
            cacheManager.getCache("userCache").removeAll();
        }
    }

    public void requestInvalidateSdiOrganism() {
        cache.evictCollectionRegion("org.easysdi.proxy.domain.SdiUser.sdiUserRoleOrganisms");
        cache.evictEntity("org.easysdi.proxy.domain.SdiOrganism", id);
    }
    
    public void requestInvalidateSdiCategory(){
        cache.evictCollectionRegion("org.easysdi.proxy.domain.SdiOrganism.sdiCategories");
        cache.evictEntity("org.easysdi.proxy.domain.SdiCategory", id);
    }

    public void InvalidateSdiVirtualServiceCache() {
        SdiVirtualserviceHome sdiVirtualServiceHome = (SdiVirtualserviceHome) context.getBean("sdiVirtualServiceHome");
        SdiVirtualservice virtualservice = sdiVirtualServiceHome.findById(id);
        SdiPolicyHome sdiPolicyHome = (SdiPolicyHome) context.getBean("sdiPolicyHome");
        for (SdiPolicy policy : virtualservice.getSdiPolicies()) {
            InvalidateSdiPolicyCache(policy.getId(), sdiPolicyHome);
        }
        cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiPolicies", id);

        for (SdiPhysicalservice physicalservice : virtualservice.getSdiPhysicalservices()) {
            cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservice.sdiVirtualservices", physicalservice.getId());
        }
    }

    public void InvalidateSdiPhysicalServiceCache() {
        SdiPhysicalserviceHome sdiPhysicalServiceHome = (SdiPhysicalserviceHome) context.getBean("sdiPhysicalServiceHome");
        SdiPhysicalservice physicalservice = sdiPhysicalServiceHome.findById(id);

        for (SdiPhysicalservicePolicy servicepolicy : physicalservice.getSdiPhysicalservicePolicies()) {
            InvalidatesdiPhysicalServicePolicyCache(servicepolicy);
        }
        cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservice.sdiVirtualservices", id);

        for (SdiVirtualservice virtualService : physicalservice.getSdiVirtualservices()) {
            cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiPhysicalservices", virtualService.getId());
            cache.evictEntity("org.easysdi.proxy.domain.SdiVirtualservice", virtualService.getId());
        }
    }

    public void InvalidateSdiPolicyCache(Integer id, SdiPolicyHome sdiPolicyHome) {
        SdiPolicy policy = sdiPolicyHome.findById(id);

        for (SdiPhysicalservicePolicy servicepolicy : policy.getSdiPhysicalservicePolicies()) {
            InvalidatesdiPhysicalServicePolicyCache(servicepolicy);
        }

        for (SdiExcludedattribute attribute : policy.getSdiExcludedattributes()) {
            cache.evictEntity("org.easysdi.proxy.domain.SdiExcludedattribute", attribute.getId());
        }
        cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiExcludedattributes", id);
        cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiPhysicalservicePolicies", id);
        cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiOrganisms", id);
        cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiAllowedoperations", id);
        cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiUsers", id);
        cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiPolicyMetadatastates", id);

        if (policy.getSdiWfsSpatialpolicy() != null) {
            cache.evictEntity("org.easysdi.proxy.domain.SdiWfsSpatialpolicy", policy.getSdiWfsSpatialpolicy().getId());
            cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiWfsSpatialpolicy", id);
        }
        if (policy.getSdiWmsSpatialpolicy() != null) {
            cache.evictEntity("org.easysdi.proxy.domain.SdiWfsSpatialpolicy", policy.getSdiWmsSpatialpolicy().getId());
            cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiWmsSpatialpolicy", id);
        }
        if (policy.getSdiWmtsSpatialpolicy() != null) {
            cache.evictEntity("org.easysdi.proxy.domain.SdiWfsSpatialpolicy", policy.getSdiWmtsSpatialpolicy().getId());
            cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiWmtsSpatialpolicy", id);
        }
        if (policy.getSdiCswSpatialpolicy() != null) {
            cache.evictEntity("org.easysdi.proxy.domain.SdiCswSpatialpolicy", policy.getSdiCswSpatialpolicy().getId());
            cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiCswSpatialpolicy", id);
        }

        cache.evictEntity("org.easysdi.proxy.domain.SdiPolicy", id);
    }

    public void InvalidatesdiPhysicalServicePolicyCache(SdiPhysicalservicePolicy servicepolicy) {

        //WMS
        for (SdiWmslayerPolicy layerpolicy : servicepolicy.getSdiWmslayerPolicies()) {
            if (layerpolicy.getSdiWmsSpatialpolicy() != null) {
                cache.evictEntity("org.easysdi.proxy.domain.SdiWmsSpatialpolicy", layerpolicy.getSdiWmsSpatialpolicy().getId());
            }
            cache.evictEntity("org.easysdi.proxy.domain.SdiWmslayerPolicy", layerpolicy.getId());
        }
        cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservicePolicy.sdiWmslayerPolicies", servicepolicy.getId());

        //WFS
        for (SdiFeaturetypePolicy layerpolicy : servicepolicy.getSdiFeaturetypePolicies()) {
            if (layerpolicy.getSdiWfsSpatialpolicy() != null) {
                cache.evictEntity("org.easysdi.proxy.domain.SdiWfsSpatialpolicy", layerpolicy.getSdiWfsSpatialpolicy().getId());
            }
            for (SdiIncludedattribute attribute : layerpolicy.getSdiIncludedattributes()) {
                cache.evictEntity("org.easysdi.proxy.domain.SdiIncludedattribute", attribute.getId());
            }
            cache.evictCollection("org.easysdi.proxy.domain.SdiFeaturetypePolicy.sdiIncludedattributes", layerpolicy.getId());
            cache.evictEntity("org.easysdi.proxy.domain.SdiFeaturetypePolicy", layerpolicy.getId());
        }
        cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservicePolicy.sdiFeaturetypePolicies", servicepolicy.getId());

        //WMTS
        for (SdiWmtslayerPolicy layerpolicy : servicepolicy.getSdiWmtslayerPolicies()) {
            if (layerpolicy.getSdiWmtsSpatialpolicy() != null) {
                cache.evictEntity("org.easysdi.proxy.domain.SdiWmtsSpatialpolicy", layerpolicy.getSdiWmtsSpatialpolicy().getId());
            }
            if (layerpolicy.getSdiTilematrixsetPolicies() != null) {
                for (SdiTilematrixsetPolicy tms : layerpolicy.getSdiTilematrixsetPolicies()) {
                    for (SdiTilematrixPolicy tm : tms.getSdiTilematrixPolicies()) {
                        cache.evictEntity("org.easysdi.proxy.domain.SdiTilematrixPolicy", tm.getId());
                    }
                    cache.evictCollection("org.easysdi.proxy.domain.SdiTilematrixsetPolicy.sdiTilematrixPolicies", tms.getId());
                    cache.evictEntity("org.easysdi.proxy.domain.SdiTilematrixsetPolicy", tms.getId());
                }
                cache.evictCollection("org.easysdi.proxy.domain.SdiWmtslayerPolicy.sdiTilematrixsetPolicies", layerpolicy.getId());
            }

            cache.evictEntity("org.easysdi.proxy.domain.SdiWmtslayerPolicy", layerpolicy.getId());
        }
        cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservicePolicy.sdiWmtslayerPolicies", servicepolicy.getId());

        //CSW
        SdiCswSpatialpolicy layerpolicy = servicepolicy.getSdiCswSpatialpolicy();
        if (layerpolicy != null) {
            cache.evictEntity("org.easysdi.proxy.domain.SdiCswSpatialpolicy", layerpolicy.getId());
        }

        cache.evictEntity("org.easysdi.proxy.domain.SdiPhysicalservicePolicy", servicepolicy.getId());
    }
}
