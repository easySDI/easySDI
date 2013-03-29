package org.easysdi.proxy.domain;

// Generated Mar 29, 2013 9:59:28 AM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiUserRoleOrganism.
 * @see org.easysdi.proxy.domain.SdiUserRoleOrganism
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiUserRoleOrganismHome {

	private static final Log log = LogFactory
			.getLog(SdiUserRoleOrganismHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiUserRoleOrganism findById(Integer id) {
		log.debug("getting SdiUserRoleOrganism instance with id: " + id);
		try {
			SdiUserRoleOrganism instance = (SdiUserRoleOrganism) sessionFactory
					.getCurrentSession().get(SdiUserRoleOrganism.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiUserRoleOrganism transientInstance) {
		log.debug("save SdiUserRoleOrganism instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiUserRoleOrganism transientInstance) {
		log.debug("saveOrUpdate SdiUserRoleOrganism instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiUserRoleOrganism transientInstance) {
		log.debug("update SdiUserRoleOrganism instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiUserRoleOrganism transientInstance) {
		log.debug("delete SdiUserRoleOrganism instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiUserRoleOrganism transientInstance) {
		log.debug("merge SdiUserRoleOrganism instance");
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
