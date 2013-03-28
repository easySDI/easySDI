package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 4:35:10 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysAuthenticationlevel.
 * @see org.easysdi.proxy.domain.SdiSysAuthenticationlevel
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysAuthenticationlevelHome {

	private static final Log log = LogFactory
			.getLog(SdiSysAuthenticationlevelHome.class);

	private SessionFactory sessionFactory;

	public SdiSysAuthenticationlevel findById(Integer id) {
		log.debug("getting SdiSysAuthenticationlevel instance with id: " + id);
		try {
			SdiSysAuthenticationlevel instance = (SdiSysAuthenticationlevel) sessionFactory
					.getCurrentSession().get(SdiSysAuthenticationlevel.class,
							id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysAuthenticationlevel transientInstance) {
		log.debug("save SdiSysAuthenticationlevel instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysAuthenticationlevel transientInstance) {
		log.debug("saveOrUpdate SdiSysAuthenticationlevel instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysAuthenticationlevel transientInstance) {
		log.debug("update SdiSysAuthenticationlevel instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysAuthenticationlevel transientInstance) {
		log.debug("delete SdiSysAuthenticationlevel instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysAuthenticationlevel transientInstance) {
		log.debug("merge SdiSysAuthenticationlevel instance");
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
