package org.easysdi.proxy.domain;

// Generated Apr 4, 2013 10:31:48 AM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiTilematrixPolicy.
 * @see org.easysdi.proxy.domain.SdiTilematrixPolicy
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiTilematrixPolicyHome {

	private static final Log log = LogFactory
			.getLog(SdiTilematrixPolicyHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiTilematrixPolicy findById(Integer id) {
		log.debug("getting SdiTilematrixPolicy instance with id: " + id);
		try {
			SdiTilematrixPolicy instance = (SdiTilematrixPolicy) sessionFactory
					.getCurrentSession().get(SdiTilematrixPolicy.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiTilematrixPolicy transientInstance) {
		log.debug("save SdiTilematrixPolicy instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiTilematrixPolicy transientInstance) {
		log.debug("saveOrUpdate SdiTilematrixPolicy instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiTilematrixPolicy transientInstance) {
		log.debug("update SdiTilematrixPolicy instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiTilematrixPolicy transientInstance) {
		log.debug("delete SdiTilematrixPolicy instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiTilematrixPolicy transientInstance) {
		log.debug("merge SdiTilematrixPolicy instance");
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
