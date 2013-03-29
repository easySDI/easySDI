package org.easysdi.proxy.domain;

// Generated Mar 29, 2013 9:59:28 AM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiPhysicalserviceServicecompliance.
 * @see org.easysdi.proxy.domain.SdiPhysicalserviceServicecompliance
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiPhysicalserviceServicecomplianceHome {

	private static final Log log = LogFactory
			.getLog(SdiPhysicalserviceServicecomplianceHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiPhysicalserviceServicecompliance findById(Integer id) {
		log.debug("getting SdiPhysicalserviceServicecompliance instance with id: "
				+ id);
		try {
			SdiPhysicalserviceServicecompliance instance = (SdiPhysicalserviceServicecompliance) sessionFactory
					.getCurrentSession().get(
							SdiPhysicalserviceServicecompliance.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiPhysicalserviceServicecompliance transientInstance) {
		log.debug("save SdiPhysicalserviceServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(
			SdiPhysicalserviceServicecompliance transientInstance) {
		log.debug("saveOrUpdate SdiPhysicalserviceServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiPhysicalserviceServicecompliance transientInstance) {
		log.debug("update SdiPhysicalserviceServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiPhysicalserviceServicecompliance transientInstance) {
		log.debug("delete SdiPhysicalserviceServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiPhysicalserviceServicecompliance transientInstance) {
		log.debug("merge SdiPhysicalserviceServicecompliance instance");
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
