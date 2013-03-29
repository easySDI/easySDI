package org.easysdi.proxy.domain;

// Generated Mar 29, 2013 9:59:28 AM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiVirtualPhysical.
 * @see org.easysdi.proxy.domain.SdiVirtualPhysical
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiVirtualPhysicalHome {

	private static final Log log = LogFactory
			.getLog(SdiVirtualPhysicalHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiVirtualPhysical findById(Integer id) {
		log.debug("getting SdiVirtualPhysical instance with id: " + id);
		try {
			SdiVirtualPhysical instance = (SdiVirtualPhysical) sessionFactory
					.getCurrentSession().get(SdiVirtualPhysical.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiVirtualPhysical transientInstance) {
		log.debug("save SdiVirtualPhysical instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiVirtualPhysical transientInstance) {
		log.debug("saveOrUpdate SdiVirtualPhysical instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiVirtualPhysical transientInstance) {
		log.debug("update SdiVirtualPhysical instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiVirtualPhysical transientInstance) {
		log.debug("delete SdiVirtualPhysical instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiVirtualPhysical transientInstance) {
		log.debug("merge SdiVirtualPhysical instance");
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
