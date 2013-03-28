package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 6:08:17 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiOrganism.
 * @see org.easysdi.proxy.domain.SdiOrganism
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiOrganismHome {

	private static final Log log = LogFactory.getLog(SdiOrganismHome.class);

	private SessionFactory sessionFactory;

	public SdiOrganism findById(Integer id) {
		log.debug("getting SdiOrganism instance with id: " + id);
		try {
			SdiOrganism instance = (SdiOrganism) sessionFactory
					.getCurrentSession().get(SdiOrganism.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiOrganism transientInstance) {
		log.debug("save SdiOrganism instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiOrganism transientInstance) {
		log.debug("saveOrUpdate SdiOrganism instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiOrganism transientInstance) {
		log.debug("update SdiOrganism instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiOrganism transientInstance) {
		log.debug("delete SdiOrganism instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiOrganism transientInstance) {
		log.debug("merge SdiOrganism instance");
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
