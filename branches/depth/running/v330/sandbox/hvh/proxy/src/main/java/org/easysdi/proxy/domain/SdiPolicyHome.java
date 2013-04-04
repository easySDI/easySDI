package org.easysdi.proxy.domain;

// Generated Apr 4, 2013 10:31:48 AM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
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
