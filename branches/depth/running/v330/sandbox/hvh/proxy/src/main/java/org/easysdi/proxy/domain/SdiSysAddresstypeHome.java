package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 3:02:09 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysAddresstype.
 * @see org.easysdi.proxy.domain.SdiSysAddresstype
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysAddresstypeHome {

	private static final Log log = LogFactory
			.getLog(SdiSysAddresstypeHome.class);

	private SessionFactory sessionFactory;

	public SdiSysAddresstype findById(Integer id) {
		log.debug("getting SdiSysAddresstype instance with id: " + id);
		try {
			SdiSysAddresstype instance = (SdiSysAddresstype) sessionFactory
					.getCurrentSession().get(SdiSysAddresstype.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysAddresstype transientInstance) {
		log.debug("save SdiSysAddresstype instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysAddresstype transientInstance) {
		log.debug("saveOrUpdate SdiSysAddresstype instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysAddresstype transientInstance) {
		log.debug("update SdiSysAddresstype instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysAddresstype transientInstance) {
		log.debug("delete SdiSysAddresstype instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysAddresstype transientInstance) {
		log.debug("merge SdiSysAddresstype instance");
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
