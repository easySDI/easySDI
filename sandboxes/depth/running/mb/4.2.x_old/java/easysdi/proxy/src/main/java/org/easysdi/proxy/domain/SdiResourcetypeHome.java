package org.easysdi.proxy.domain;

// Generated Oct 3, 2013 1:38:15 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiResourcetype.
 * @see org.easysdi.proxy.domaintransitoire.SdiResourcetype
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiResourcetypeHome {

	private static final Log log = LogFactory
			.getLog(SdiResourcetypeHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiResourcetype findById(Integer id) {
		log.debug("getting SdiResourcetype instance with id: " + id);
		try {
			SdiResourcetype instance = (SdiResourcetype) sessionFactory
					.getCurrentSession().get(SdiResourcetype.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiResourcetype transientInstance) {
		log.debug("save SdiResourcetype instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiResourcetype transientInstance) {
		log.debug("saveOrUpdate SdiResourcetype instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiResourcetype transientInstance) {
		log.debug("update SdiResourcetype instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiResourcetype transientInstance) {
		log.debug("delete SdiResourcetype instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiResourcetype transientInstance) {
		log.debug("merge SdiResourcetype instance");
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
