package org.easysdi.proxy.domain;

// Generated Mar 15, 2013 2:35:39 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysCountry.
 * @see org.easysdi.proxy.domain.SdiSysCountry
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysCountryHome {

	private static final Log log = LogFactory.getLog(SdiSysCountryHome.class);

	private SessionFactory sessionFactory;

	public SdiSysCountry findById(Integer id) {
		log.debug("getting SdiSysCountry instance with id: " + id);
		try {
			SdiSysCountry instance = (SdiSysCountry) sessionFactory
					.getCurrentSession().get(SdiSysCountry.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysCountry transientInstance) {
		log.debug("save SdiSysCountry instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysCountry transientInstance) {
		log.debug("saveOrUpdate SdiSysCountry instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysCountry transientInstance) {
		log.debug("update SdiSysCountry instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysCountry transientInstance) {
		log.debug("delete SdiSysCountry instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysCountry transientInstance) {
		log.debug("merge SdiSysCountry instance");
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
