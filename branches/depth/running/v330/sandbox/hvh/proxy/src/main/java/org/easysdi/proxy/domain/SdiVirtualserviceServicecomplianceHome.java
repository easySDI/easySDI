package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 4:35:10 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiVirtualserviceServicecompliance.
 * @see org.easysdi.proxy.domain.SdiVirtualserviceServicecompliance
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiVirtualserviceServicecomplianceHome {

	private static final Log log = LogFactory
			.getLog(SdiVirtualserviceServicecomplianceHome.class);

	private SessionFactory sessionFactory;

	public SdiVirtualserviceServicecompliance findById(Integer id) {
		log.debug("getting SdiVirtualserviceServicecompliance instance with id: "
				+ id);
		try {
			SdiVirtualserviceServicecompliance instance = (SdiVirtualserviceServicecompliance) sessionFactory
					.getCurrentSession().get(
							SdiVirtualserviceServicecompliance.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiVirtualserviceServicecompliance transientInstance) {
		log.debug("save SdiVirtualserviceServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(
			SdiVirtualserviceServicecompliance transientInstance) {
		log.debug("saveOrUpdate SdiVirtualserviceServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiVirtualserviceServicecompliance transientInstance) {
		log.debug("update SdiVirtualserviceServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiVirtualserviceServicecompliance transientInstance) {
		log.debug("delete SdiVirtualserviceServicecompliance instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiVirtualserviceServicecompliance transientInstance) {
		log.debug("merge SdiVirtualserviceServicecompliance instance");
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
