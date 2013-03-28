package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 6:08:17 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiWmtslayerPolicy.
 * @see org.easysdi.proxy.domain.SdiWmtslayerPolicy
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiWmtslayerPolicyHome {

	private static final Log log = LogFactory
			.getLog(SdiWmtslayerPolicyHome.class);

	private SessionFactory sessionFactory;

	public SdiWmtslayerPolicy findById(Integer id) {
		log.debug("getting SdiWmtslayerPolicy instance with id: " + id);
		try {
			SdiWmtslayerPolicy instance = (SdiWmtslayerPolicy) sessionFactory
					.getCurrentSession().get(SdiWmtslayerPolicy.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiWmtslayerPolicy transientInstance) {
		log.debug("save SdiWmtslayerPolicy instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiWmtslayerPolicy transientInstance) {
		log.debug("saveOrUpdate SdiWmtslayerPolicy instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiWmtslayerPolicy transientInstance) {
		log.debug("update SdiWmtslayerPolicy instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiWmtslayerPolicy transientInstance) {
		log.debug("delete SdiWmtslayerPolicy instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiWmtslayerPolicy transientInstance) {
		log.debug("merge SdiWmtslayerPolicy instance");
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
