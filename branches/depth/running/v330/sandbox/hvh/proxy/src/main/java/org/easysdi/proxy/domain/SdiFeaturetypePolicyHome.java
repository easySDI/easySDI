package org.easysdi.proxy.domain;

// Generated Apr 4, 2013 10:31:48 AM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiFeaturetypePolicy.
 * @see org.easysdi.proxy.domain.SdiFeaturetypePolicy
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiFeaturetypePolicyHome {

	private static final Log log = LogFactory
			.getLog(SdiFeaturetypePolicyHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiFeaturetypePolicy findById(Integer id) {
		log.debug("getting SdiFeaturetypePolicy instance with id: " + id);
		try {
			SdiFeaturetypePolicy instance = (SdiFeaturetypePolicy) sessionFactory
					.getCurrentSession().get(SdiFeaturetypePolicy.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiFeaturetypePolicy transientInstance) {
		log.debug("save SdiFeaturetypePolicy instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiFeaturetypePolicy transientInstance) {
		log.debug("saveOrUpdate SdiFeaturetypePolicy instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiFeaturetypePolicy transientInstance) {
		log.debug("update SdiFeaturetypePolicy instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiFeaturetypePolicy transientInstance) {
		log.debug("delete SdiFeaturetypePolicy instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiFeaturetypePolicy transientInstance) {
		log.debug("merge SdiFeaturetypePolicy instance");
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
