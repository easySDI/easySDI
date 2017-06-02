package org.easysdi.proxy.domain;

// Generated Oct 3, 2013 2:18:25 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiVersion.
 * @see org.easysdi.proxy.domaintransitoire.SdiVersion
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiVersionHome {

	private static final Log log = LogFactory.getLog(SdiVersionHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiVersion findById(Integer id) {
		log.debug("getting SdiVersion instance with id: " + id);
		try {
			SdiVersion instance = (SdiVersion) sessionFactory
					.getCurrentSession().get(SdiVersion.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiVersion transientInstance) {
		log.debug("save SdiVersion instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiVersion transientInstance) {
		log.debug("saveOrUpdate SdiVersion instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiVersion transientInstance) {
		log.debug("update SdiVersion instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiVersion transientInstance) {
		log.debug("delete SdiVersion instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiVersion transientInstance) {
		log.debug("merge SdiVersion instance");
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
