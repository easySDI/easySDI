/**
 * 
 */
package org.easysdi.proxy.hibernate;

import java.io.IOException;
import java.util.Enumeration;
import java.util.List;
import java.util.Map;

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
import org.hibernate.SessionFactory;
import org.hibernate.Cache;
import org.hibernate.metadata.ClassMetadata;
import org.hibernate.metadata.CollectionMetadata;
import org.springframework.context.ApplicationContext;
import org.springframework.web.context.support.WebApplicationContextUtils;

/**
 * @author Helene
 *
 */
public class ProxyCacheInvalidationServlet extends HttpServlet {

	private static final long serialVersionUID = -5054942482987117794L;
	private Cache cache;
	ApplicationContext context;
	
	/* (non-Javadoc)
	 * @see javax.servlet.http.HttpServlet#doGet(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
	 */
	@Override
	protected void doGet(HttpServletRequest req, HttpServletResponse resp)
			throws ServletException, IOException {
		
		String operation 	= req.getParameter("operation");
		String entityClass 	= req.getParameter("entityclass");
		String id 			= req.getParameter("id");
		String complete 	= req.getParameter("complete");
		
		context	= WebApplicationContextUtils.getWebApplicationContext(getServletContext());
		CacheManager cacheManager 		= (CacheManager) context.getBean("cacheManager");
		SessionFactory sessionFactory 	= (SessionFactory)context.getBean("sessionFactory");
		
		cache = sessionFactory.getCache();
		
		//Invalidate all cache regions
		if (complete != null && complete.equalsIgnoreCase("TRUE")){
//			if (cacheManager.cacheExists("userCache")) cacheManager.getCache("userCache").removeAll();
//			if (cacheManager.cacheExists("operationBasedCache")) cacheManager.getCache("operationBasedCache").removeAll();
			String[] n = cacheManager.getCacheNames();
			List<CacheManager> l = cacheManager.ALL_CACHE_MANAGERS;
			
			cache.evictDefaultQueryRegion();
			cache.evictEntityRegions();
			cache.evictQueryRegions();
			cache.evictCollectionRegions();
			return;
		}
		
		//Invalidate a specific entity region
		if(entityClass != null && id == null){
			cache.evictEntityRegion(entityClass);
		}
		
		//Invalidate a specific entity
		if(entityClass != null && id != null){
			if(entityClass.equalsIgnoreCase("SdiPhysicalservice") ){
				if(operation != null && operation.equalsIgnoreCase("DELETE")){
					SdiPhysicalserviceHome sdiPhysicalServiceHome  = (SdiPhysicalserviceHome)context.getBean("sdiPhysicalServiceHome");
					InvalidateSdiPhysicalServiceCache(id, sdiPhysicalServiceHome);
				}
				cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservice.sdiPhysicalserviceServicecompliances", id);
				cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservice.sdiOrganisms", id);
				cache.evictEntity("org.easysdi.proxy.domain.SdiPhysicalservice", id);
			}
			else if(entityClass.equalsIgnoreCase("SdiVirtualservice")){
				if(operation != null && operation.equalsIgnoreCase("DELETE")){
					SdiVirtualserviceHome sdiVirtualServiceHome  = (SdiVirtualserviceHome)context.getBean("sdiVirtualServiceHome");
					InvalidateSdiVirtualServiceCache(id,sdiVirtualServiceHome);
				}
				cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiOrganisms", id);
				cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiPhysicalservices", id);
				cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiSysServicecompliances", id);
				cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiVirtualmetadatas", id);
				cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiPhysicalservicePolicies", id);
				cache.evictEntity("org.easysdi.proxy.domain.SdiVirtualservice", id);
			}
			else if(entityClass.equalsIgnoreCase("SdiPolicy")){
				SdiPolicyHome sdiPolicyHome  = (SdiPolicyHome)context.getBean("sdiPolicyHome");
				InvalidateSdiPolicyCache ( id,  sdiPolicyHome);
			}
			else if(entityClass.equalsIgnoreCase("SdiUser")){
				cache.evictCollection("org.easysdi.proxy.domain.SdiUser.sdiOrganisms", id);
				cache.evictEntity("org.easysdi.proxy.domain.SdiUser", id);
			}
		}
	}
	
	private void InvalidateSdiVirtualServiceCache(String id, SdiVirtualserviceHome sdiVirtualServiceHome){
		SdiVirtualservice virtualservice = sdiVirtualServiceHome.findById(Integer.getInteger(id));
		SdiPolicyHome sdiPolicyHome  = (SdiPolicyHome)context.getBean("sdiPolicyHome");
		for(SdiPolicy policy :virtualservice.getSdiPolicies()){
			InvalidateSdiPolicyCache ( policy.getId().toString(),sdiPolicyHome );
		}
		cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiPolicies", id);
		
		for(SdiPhysicalservice physicalservice :virtualservice.getSdiPhysicalservices()){
			cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservice.sdiVirtualservices", physicalservice.getId());
		}
	}
	
	private void InvalidateSdiPhysicalServiceCache(String id, SdiPhysicalserviceHome sdiPhysicalServiceHome){
		SdiPhysicalservice physicalservice = sdiPhysicalServiceHome.findById(Integer.getInteger(id));
		
		for(SdiPhysicalservicePolicy servicepolicy :physicalservice.getSdiPhysicalservicePolicies()){
			InvalidatesdiPhysicalServicePolicyCache ( servicepolicy);
		}
		cache.evictCollection("org.easysdi.proxy.domain.SdiPhysicalservice.sdiVirtualservices", id);
		
		for(SdiVirtualservice virtualService :physicalservice.getSdiVirtualservices()){
			cache.evictCollection("org.easysdi.proxy.domain.SdiVirtualservice.sdiPhysicalservices", virtualService.getId());
		}
	}
	
	private void InvalidateSdiPolicyCache (String id, SdiPolicyHome sdiPolicyHome){
		SdiPolicy policy = sdiPolicyHome.findById(Integer.getInteger(id));
		
		for(SdiPhysicalservicePolicy servicepolicy :policy.getSdiPhysicalservicePolicies()){
			InvalidatesdiPhysicalServicePolicyCache ( servicepolicy);
		}
		
		for(SdiExcludedattribute attribute :policy.getSdiExcludedattributes()){
			cache.evictEntity("org.easysdi.proxy.domain.SdiExcludedattribute", attribute.getId());
		}
		cache.evictCollection("org.easysdi.proxy.domain.sdiPolicy.SdiExcludedattributes", policy.getId());
		cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiPhysicalservicePolicies", id);
		cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiOrganisms", id);
		cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiAllowedoperations", id);
		cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiUsers", id);
		cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiPolicyMetadatastates", id);
		cache.evictCollection("org.easysdi.proxy.domain.SdiPolicy.sdiExcludedattributes", id);
		cache.evictEntity("org.easysdi.proxy.domain.SdiPolicy", id);
	}
	
	private void InvalidatesdiPhysicalServicePolicyCache (SdiPhysicalservicePolicy servicepolicy){
		
		//WMS
		for(SdiWmslayerPolicy layerpolicy : servicepolicy.getSdiWmslayerPolicies()){
			if(layerpolicy.getSdiWmsSpatialpolicy() != null)
				cache.evictEntity("org.easysdi.proxy.domain.SdiWmsSpatialpolicy", layerpolicy.getSdiWmsSpatialpolicy().getId());
			cache.evictEntity("org.easysdi.proxy.domain.SdiWmslayerPolicy", layerpolicy.getId());
		}
		cache.evictCollection("org.easysdi.proxy.domain.sdiPhysicalservicePolicy.sdiWmslayerPolicies", servicepolicy.getId());
		
		//WFS
		for(SdiFeaturetypePolicy layerpolicy : servicepolicy.getSdiFeaturetypePolicies()){
			if(layerpolicy.getSdiWfsSpatialpolicy() != null)
				cache.evictEntity("org.easysdi.proxy.domain.SdiWfsSpatialpolicy", layerpolicy.getSdiWfsSpatialpolicy().getId());
			for(SdiIncludedattribute attribute : layerpolicy.getSdiIncludedattributes())
				cache.evictEntity("org.easysdi.proxy.domain.SdiIncludedattribute", attribute.getId());
			cache.evictCollection("org.easysdi.proxy.domain.SdiFeaturetypePolicy.sdiIncludedattributes", layerpolicy.getId());
			cache.evictEntity("org.easysdi.proxy.domain.SdiFeaturetypePolicy", layerpolicy.getId());
		}
		cache.evictCollection("org.easysdi.proxy.domain.sdiPhysicalservicePolicy.sdiFeaturetypePolicies", servicepolicy.getId());
		
		//WMTS
		for(SdiWmtslayerPolicy layerpolicy : servicepolicy.getSdiWmtslayerPolicies()){
			if(layerpolicy.getSdiWmtsSpatialpolicy() != null)
				cache.evictEntity("org.easysdi.proxy.domain.SdiWmtsSpatialpolicy", layerpolicy.getSdiWmtsSpatialpolicy().getId());
			if(layerpolicy.getSdiTilematrixsetPolicies() != null){
				for(SdiTilematrixsetPolicy tms : layerpolicy.getSdiTilematrixsetPolicies()){
					for(SdiTilematrixPolicy tm : tms.getSdiTilematrixPolicies()){
						cache.evictEntity("org.easysdi.proxy.domain.SdiTilematrixPolicy", tm.getId());
					}
					cache.evictCollection("org.easysdi.proxy.domain.SdiTilematrixsetPolicy.sdiTilematrixPolicies", tms.getId());
					cache.evictEntity("org.easysdi.proxy.domain.SdiTilematrixsetPolicy", tms.getId());
				}
				cache.evictCollection("org.easysdi.proxy.domain.SdiWmtslayerPolicy.sdiTilematrixsetPolicies", layerpolicy.getId());
			}
				
			cache.evictEntity("org.easysdi.proxy.domain.SdiWmtslayerPolicy", layerpolicy.getId());
		}
		cache.evictCollection("org.easysdi.proxy.domain.sdiPhysicalservicePolicy.sdiWmtslayerPolicies", servicepolicy.getId());
		
		//CSW
		SdiCswSpatialpolicy layerpolicy = servicepolicy.getSdiCswSpatialpolicy();
		if(layerpolicy != null)
			cache.evictEntity("org.easysdi.proxy.domain.SdiCswSpatialpolicy", layerpolicy.getId());
		
		cache.evictEntity("org.easysdi.proxy.domain.sdiPhysicalservicePolicy", servicepolicy.getId());
		
	}
	
	
	@SuppressWarnings("unchecked")
	private static void ClearCache(SessionFactory sessionFactory, Cache cache )
	{
	    cache.evictQueryRegions();
	    
		Map<String, CollectionMetadata> colM = sessionFactory.getAllCollectionMetadata();
	    for(CollectionMetadata m : colM.values()){
	    	cache.evictCollectionRegion(m.getRole());
	    }
	    Map<String, ClassMetadata> colC = sessionFactory.getAllClassMetadata();
	    for(ClassMetadata c : colC.values()){
	    	cache.evictEntityRegion(c.getEntityName());
	    }
	    
	}

}
