package org.easysdi.proxy.domain;

// Generated Oct 3, 2013 1:38:15 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiPolicyVisibility.
 * @see org.easysdi.proxy.domaintransitoire.SdiPolicyVisibility
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiPolicyVisibilityHome {

	private static final Log log = LogFactory
			.getLog(SdiPolicyVisibilityHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiPolicyVisibility findById(Integer id) {
		log.debug("getting SdiPolicyVisibility instance with id: " + id);
		try {
			SdiPolicyVisibility instance = (SdiPolicyVisibility) sessionFactory
					.getCurrentSession()
					.get(SdiPolicyVisibility.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiPolicyVisibility transientInstance) {
		log.debug("save SdiPolicyVisibility instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiPolicyVisibility transientInstance) {
		log.debug("saveOrUpdate SdiPolicyVisibility instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiPolicyVisibility transientInstance) {
		log.debug("update SdiPolicyVisibility instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiPolicyVisibility transientInstance) {
		log.debug("delete SdiPolicyVisibility instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiPolicyVisibility transientInstance) {
		log.debug("merge SdiPolicyVisibility instance");
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
