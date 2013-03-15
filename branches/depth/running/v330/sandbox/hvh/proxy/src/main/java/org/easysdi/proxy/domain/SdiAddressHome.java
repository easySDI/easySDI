package org.easysdi.proxy.domain;

// Generated Mar 15, 2013 2:35:39 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiAddress.
 * @see org.easysdi.proxy.domain.SdiAddress
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiAddressHome {

	private static final Log log = LogFactory.getLog(SdiAddressHome.class);

	private SessionFactory sessionFactory;

	public SdiAddress findById(Integer id) {
		log.debug("getting SdiAddress instance with id: " + id);
		try {
			SdiAddress instance = (SdiAddress) sessionFactory
					.getCurrentSession().get(SdiAddress.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiAddress transientInstance) {
		log.debug("save SdiAddress instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiAddress transientInstance) {
		log.debug("saveOrUpdate SdiAddress instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiAddress transientInstance) {
		log.debug("update SdiAddress instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiAddress transientInstance) {
		log.debug("delete SdiAddress instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiAddress transientInstance) {
		log.debug("merge SdiAddress instance");
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
