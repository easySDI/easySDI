package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 6:08:17 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysServiceoperation.
 * @see org.easysdi.proxy.domain.SdiSysServiceoperation
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysServiceoperationHome {

	private static final Log log = LogFactory
			.getLog(SdiSysServiceoperationHome.class);

	private SessionFactory sessionFactory;

	public SdiSysServiceoperation findById(Integer id) {
		log.debug("getting SdiSysServiceoperation instance with id: " + id);
		try {
			SdiSysServiceoperation instance = (SdiSysServiceoperation) sessionFactory
					.getCurrentSession().get(SdiSysServiceoperation.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysServiceoperation transientInstance) {
		log.debug("save SdiSysServiceoperation instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysServiceoperation transientInstance) {
		log.debug("saveOrUpdate SdiSysServiceoperation instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysServiceoperation transientInstance) {
		log.debug("update SdiSysServiceoperation instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysServiceoperation transientInstance) {
		log.debug("delete SdiSysServiceoperation instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysServiceoperation transientInstance) {
		log.debug("merge SdiSysServiceoperation instance");
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
