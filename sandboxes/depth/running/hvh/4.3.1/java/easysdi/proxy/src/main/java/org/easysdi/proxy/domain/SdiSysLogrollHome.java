package org.easysdi.proxy.domain;

// Generated Apr 9, 2013 11:54:42 AM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysLogroll.
 * @see org.easysdi.proxy.domain.SdiSysLogroll
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysLogrollHome {

	private static final Log log = LogFactory.getLog(SdiSysLogrollHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiSysLogroll findById(Integer id) {
		log.debug("getting SdiSysLogroll instance with id: " + id);
		try {
			SdiSysLogroll instance = (SdiSysLogroll) sessionFactory
					.getCurrentSession().get(SdiSysLogroll.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysLogroll transientInstance) {
		log.debug("save SdiSysLogroll instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysLogroll transientInstance) {
		log.debug("saveOrUpdate SdiSysLogroll instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysLogroll transientInstance) {
		log.debug("update SdiSysLogroll instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysLogroll transientInstance) {
		log.debug("delete SdiSysLogroll instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysLogroll transientInstance) {
		log.debug("merge SdiSysLogroll instance");
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
