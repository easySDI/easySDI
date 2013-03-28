package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 4:35:10 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysAuthenticationconnector.
 * @see org.easysdi.proxy.domain.SdiSysAuthenticationconnector
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysAuthenticationconnectorHome {

	private static final Log log = LogFactory
			.getLog(SdiSysAuthenticationconnectorHome.class);

	private SessionFactory sessionFactory;

	public SdiSysAuthenticationconnector findById(Integer id) {
		log.debug("getting SdiSysAuthenticationconnector instance with id: "
				+ id);
		try {
			SdiSysAuthenticationconnector instance = (SdiSysAuthenticationconnector) sessionFactory
					.getCurrentSession().get(
							SdiSysAuthenticationconnector.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysAuthenticationconnector transientInstance) {
		log.debug("save SdiSysAuthenticationconnector instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysAuthenticationconnector transientInstance) {
		log.debug("saveOrUpdate SdiSysAuthenticationconnector instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysAuthenticationconnector transientInstance) {
		log.debug("update SdiSysAuthenticationconnector instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysAuthenticationconnector transientInstance) {
		log.debug("delete SdiSysAuthenticationconnector instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysAuthenticationconnector transientInstance) {
		log.debug("merge SdiSysAuthenticationconnector instance");
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
