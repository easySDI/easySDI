package org.easysdi.proxy.domain;

// Generated Apr 9, 2013 11:54:42 AM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiCswSpatialpolicy.
 * @see org.easysdi.proxy.domain.SdiCswSpatialpolicy
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiCswSpatialpolicyHome {

	private static final Log log = LogFactory
			.getLog(SdiCswSpatialpolicyHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiCswSpatialpolicy findById(Integer id) {
		log.debug("getting SdiCswSpatialpolicy instance with id: " + id);
		try {
			SdiCswSpatialpolicy instance = (SdiCswSpatialpolicy) sessionFactory
					.getCurrentSession().get(SdiCswSpatialpolicy.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiCswSpatialpolicy transientInstance) {
		log.debug("save SdiCswSpatialpolicy instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiCswSpatialpolicy transientInstance) {
		log.debug("saveOrUpdate SdiCswSpatialpolicy instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiCswSpatialpolicy transientInstance) {
		log.debug("update SdiCswSpatialpolicy instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiCswSpatialpolicy transientInstance) {
		log.debug("delete SdiCswSpatialpolicy instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiCswSpatialpolicy transientInstance) {
		log.debug("merge SdiCswSpatialpolicy instance");
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
