package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 4:35:10 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysServicecompliance.
 * @see org.easysdi.proxy.domain.SdiSysServicecompliance
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysServicecomplianceHome {

	private static final Log log = LogFactory
			.getLog(SdiSysServicecomplianceHome.class);

	private SessionFactory sessionFactory;

	public SdiSysServicecompliance findById(Integer id) {
		log.debug("getting SdiSysServicecompliance instance with id: " + id);
		try {
			SdiSysServicecompliance instance = (SdiSysServicecompliance) sessionFactory
					.getCurrentSession().get(SdiSysServicecompliance.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysServicecompliance transientInstance) {
		log.debug("save SdiSysServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysServicecompliance transientInstance) {
		log.debug("saveOrUpdate SdiSysServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysServicecompliance transientInstance) {
		log.debug("update SdiSysServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysServicecompliance transientInstance) {
		log.debug("delete SdiSysServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysServicecompliance transientInstance) {
		log.debug("merge SdiSysServicecompliance instance");
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
