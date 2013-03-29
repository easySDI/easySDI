package org.easysdi.proxy.domain;

// Generated Mar 29, 2013 9:59:28 AM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiWmsSpatialpolicy.
 * @see org.easysdi.proxy.domain.SdiWmsSpatialpolicy
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiWmsSpatialpolicyHome {

	private static final Log log = LogFactory
			.getLog(SdiWmsSpatialpolicyHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiWmsSpatialpolicy findById(Integer id) {
		log.debug("getting SdiWmsSpatialpolicy instance with id: " + id);
		try {
			SdiWmsSpatialpolicy instance = (SdiWmsSpatialpolicy) sessionFactory
					.getCurrentSession().get(SdiWmsSpatialpolicy.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiWmsSpatialpolicy transientInstance) {
		log.debug("save SdiWmsSpatialpolicy instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiWmsSpatialpolicy transientInstance) {
		log.debug("saveOrUpdate SdiWmsSpatialpolicy instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiWmsSpatialpolicy transientInstance) {
		log.debug("update SdiWmsSpatialpolicy instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiWmsSpatialpolicy transientInstance) {
		log.debug("delete SdiWmsSpatialpolicy instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiWmsSpatialpolicy transientInstance) {
		log.debug("merge SdiWmsSpatialpolicy instance");
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
