package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 6:08:17 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysServicescope.
 * @see org.easysdi.proxy.domain.SdiSysServicescope
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysServicescopeHome {

	private static final Log log = LogFactory
			.getLog(SdiSysServicescopeHome.class);

	private SessionFactory sessionFactory;

	public SdiSysServicescope findById(Integer id) {
		log.debug("getting SdiSysServicescope instance with id: " + id);
		try {
			SdiSysServicescope instance = (SdiSysServicescope) sessionFactory
					.getCurrentSession().get(SdiSysServicescope.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysServicescope transientInstance) {
		log.debug("save SdiSysServicescope instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysServicescope transientInstance) {
		log.debug("saveOrUpdate SdiSysServicescope instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysServicescope transientInstance) {
		log.debug("update SdiSysServicescope instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysServicescope transientInstance) {
		log.debug("delete SdiSysServicescope instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysServicescope transientInstance) {
		log.debug("merge SdiSysServicescope instance");
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
