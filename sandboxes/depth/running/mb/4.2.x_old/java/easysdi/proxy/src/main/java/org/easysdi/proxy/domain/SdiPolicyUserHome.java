package org.easysdi.proxy.domain;

// Generated Oct 3, 2013 1:38:15 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiPolicyUser.
 * @see org.easysdi.proxy.domaintransitoire.SdiPolicyUser
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiPolicyUserHome {

	private static final Log log = LogFactory
			.getLog(SdiPolicyUserHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiPolicyUser findById(Integer id) {
		log.debug("getting SdiPolicyUser instance with id: " + id);
		try {
			SdiPolicyUser instance = (SdiPolicyUser) sessionFactory
					.getCurrentSession().get(SdiPolicyUser.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiPolicyUser transientInstance) {
		log.debug("save SdiPolicyUser instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiPolicyUser transientInstance) {
		log.debug("saveOrUpdate SdiPolicyUser instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiPolicyUser transientInstance) {
		log.debug("update SdiPolicyUser instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiPolicyUser transientInstance) {
		log.debug("delete SdiPolicyUser instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiPolicyUser transientInstance) {
		log.debug("merge SdiPolicyUser instance");
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
