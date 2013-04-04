package org.easysdi.proxy.domain;

// Generated Apr 4, 2013 10:31:48 AM by Hibernate Tools 3.4.0.CR1

import java.util.Collection;
import java.util.Iterator;
import java.util.List;
import java.util.Set;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.Query;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiPolicy.
 * @see org.easysdi.proxy.domain.SdiPolicy
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiPolicyHome {

	private static final Log log = LogFactory.getLog(SdiPolicyHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiPolicy findById(Integer id) {
		log.debug("getting SdiPolicy instance with id: " + id);
		try {
			SdiPolicy instance = (SdiPolicy) sessionFactory.getCurrentSession()
					.get(SdiPolicy.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}
	
	public SdiPolicy findByVirtualServiceAndUser(Integer virtualservice, Integer user, Collection<GrantedAuthority> authorities ) {
		try {
			//Policies linked to the current user
			Query query = sessionFactory.getCurrentSession().createQuery("Select p FROM Policy p, SdiPolicyUser u WHERE u.user_id= :user AND u.policy_id = p.id AND p.virtualservice_id = :virtualservice ORDER BY p.ordering asc");
			query.setParameter("user", user);
			query.setParameter("virtualservice", virtualservice);
			List results = query.list();
			if(results != null && results.size() > 0)
			{
				return (SdiPolicy) results.get(0);
			}

			//Policies of the organisms which the current user is member of
			String condition = "";
			Iterator<GrantedAuthority> i = authorities.iterator();
			while (i.hasNext())
			{
				condition += condition.length() > 0 ? ',' : "" ;
				condition += i.next().getAuthority();
			}
			Query oQuery = sessionFactory.getCurrentSession().createQuery("Select p FROM Policy p, SdiPolicyOrganism o WHERE o.organism_id IN (:organism) AND o.policy_id = p.id AND p.virtualservice_id = :virtualservice ORDER BY p.ordering asc");
			oQuery.setParameter("organism", condition);
			oQuery.setParameter("virtualservice", virtualservice);
			List oResults = oQuery.list();
			if (oResults != null && oResults.size() > 0)
			{
				return (SdiPolicy) oResults.get(0);
			}
			
			//Public policies
			Query pQuery = sessionFactory.getCurrentSession().createQuery("Select p FROM Policy p WHERE p.virtualservice_id = :virtualservice AND p.accessscope_id = 1 ORDER BY p.ordering asc");
			pQuery.setParameter("virtualservice", virtualservice);
			List pResults = pQuery.list();
			if (pResults != null && pResults.size() > 0)
			{
				return (SdiPolicy) pResults.get(0);
			}
			
			return null;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiPolicy transientInstance) {
		log.debug("save SdiPolicy instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiPolicy transientInstance) {
		log.debug("saveOrUpdate SdiPolicy instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiPolicy transientInstance) {
		log.debug("update SdiPolicy instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiPolicy transientInstance) {
		log.debug("delete SdiPolicy instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiPolicy transientInstance) {
		log.debug("merge SdiPolicy instance");
		try {
			sessionFactory.getCurrentSession().merge(transientInstance);
			log.debug("merge successful");
		} catch (RuntimeException re) {
			log.error("merge failed", re);
			throw re;
		}
	}

	public SessionFactory getSessionFactory() {
		return sessionFactory;
	}

	public void setSessionFactory(SessionFactory sessionFactory) {
		this.sessionFactory = sessionFactory;
	}

}
