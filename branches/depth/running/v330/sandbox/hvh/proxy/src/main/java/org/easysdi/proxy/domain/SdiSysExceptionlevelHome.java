package org.easysdi.proxy.domain;

// Generated Mar 29, 2013 9:59:28 AM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysExceptionlevel.
 * @see org.easysdi.proxy.domain.SdiSysExceptionlevel
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysExceptionlevelHome {

	private static final Log log = LogFactory
			.getLog(SdiSysExceptionlevelHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiSysExceptionlevel findById(Integer id) {
		log.debug("getting SdiSysExceptionlevel instance with id: " + id);
		try {
			SdiSysExceptionlevel instance = (SdiSysExceptionlevel) sessionFactory
					.getCurrentSession().get(SdiSysExceptionlevel.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysExceptionlevel transientInstance) {
		log.debug("save SdiSysExceptionlevel instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysExceptionlevel transientInstance) {
		log.debug("saveOrUpdate SdiSysExceptionlevel instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysExceptionlevel transientInstance) {
		log.debug("update SdiSysExceptionlevel instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysExceptionlevel transientInstance) {
		log.debug("delete SdiSysExceptionlevel instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysExceptionlevel transientInstance) {
		log.debug("merge SdiSysExceptionlevel instance");
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
