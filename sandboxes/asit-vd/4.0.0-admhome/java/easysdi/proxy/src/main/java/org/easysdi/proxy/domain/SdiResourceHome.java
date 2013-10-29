package org.easysdi.proxy.domain;

// Generated Oct 3, 2013 2:18:25 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiResource.
 * @see org.easysdi.proxy.domaintransitoire.SdiResource
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiResourceHome {

	private static final Log log = LogFactory
			.getLog(SdiResourceHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiResource findById(Integer id) {
		log.debug("getting SdiResource instance with id: " + id);
		try {
			SdiResource instance = (SdiResource) sessionFactory
					.getCurrentSession().get(SdiResource.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}
        
	public void save(SdiResource transientInstance) {
		log.debug("save SdiResource instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiResource transientInstance) {
		log.debug("saveOrUpdate SdiResource instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiResource transientInstance) {
		log.debug("update SdiResource instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiResource transientInstance) {
		log.debug("delete SdiResource instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiResource transientInstance) {
		log.debug("merge SdiResource instance");
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
