package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 6:08:17 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysUnit.
 * @see org.easysdi.proxy.domain.SdiSysUnit
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysUnitHome {

	private static final Log log = LogFactory.getLog(SdiSysUnitHome.class);

	private SessionFactory sessionFactory;

	public SdiSysUnit findById(Integer id) {
		log.debug("getting SdiSysUnit instance with id: " + id);
		try {
			SdiSysUnit instance = (SdiSysUnit) sessionFactory
					.getCurrentSession().get(SdiSysUnit.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysUnit transientInstance) {
		log.debug("save SdiSysUnit instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysUnit transientInstance) {
		log.debug("saveOrUpdate SdiSysUnit instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysUnit transientInstance) {
		log.debug("update SdiSysUnit instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysUnit transientInstance) {
		log.debug("delete SdiSysUnit instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysUnit transientInstance) {
		log.debug("merge SdiSysUnit instance");
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
