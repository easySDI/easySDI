package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 4:35:10 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiPhysicalservicePolicy.
 * @see org.easysdi.proxy.domain.SdiPhysicalservicePolicy
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiPhysicalservicePolicyHome {

	private static final Log log = LogFactory
			.getLog(SdiPhysicalservicePolicyHome.class);

	private SessionFactory sessionFactory;

	public SdiPhysicalservicePolicy findById(Integer id) {
		log.debug("getting SdiPhysicalservicePolicy instance with id: " + id);
		try {
			SdiPhysicalservicePolicy instance = (SdiPhysicalservicePolicy) sessionFactory
					.getCurrentSession()
					.get(SdiPhysicalservicePolicy.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiPhysicalservicePolicy transientInstance) {
		log.debug("save SdiPhysicalservicePolicy instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiPhysicalservicePolicy transientInstance) {
		log.debug("saveOrUpdate SdiPhysicalservicePolicy instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiPhysicalservicePolicy transientInstance) {
		log.debug("update SdiPhysicalservicePolicy instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiPhysicalservicePolicy transientInstance) {
		log.debug("delete SdiPhysicalservicePolicy instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiPhysicalservicePolicy transientInstance) {
		log.debug("merge SdiPhysicalservicePolicy instance");
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
