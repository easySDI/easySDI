package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 4:35:10 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysServiceconnector.
 * @see org.easysdi.proxy.domain.SdiSysServiceconnector
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysServiceconnectorHome {

	private static final Log log = LogFactory
			.getLog(SdiSysServiceconnectorHome.class);

	private SessionFactory sessionFactory;

	public SdiSysServiceconnector findById(Integer id) {
		log.debug("getting SdiSysServiceconnector instance with id: " + id);
		try {
			SdiSysServiceconnector instance = (SdiSysServiceconnector) sessionFactory
					.getCurrentSession().get(SdiSysServiceconnector.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysServiceconnector transientInstance) {
		log.debug("save SdiSysServiceconnector instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysServiceconnector transientInstance) {
		log.debug("saveOrUpdate SdiSysServiceconnector instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysServiceconnector transientInstance) {
		log.debug("update SdiSysServiceconnector instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysServiceconnector transientInstance) {
		log.debug("delete SdiSysServiceconnector instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysServiceconnector transientInstance) {
		log.debug("merge SdiSysServiceconnector instance");
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
