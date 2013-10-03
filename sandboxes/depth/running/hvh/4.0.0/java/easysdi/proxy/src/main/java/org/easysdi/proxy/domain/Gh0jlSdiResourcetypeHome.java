package org.easysdi.proxy.domaintransitoire;

// Generated Oct 3, 2013 2:18:25 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class Gh0jlSdiResourcetype.
 * @see org.easysdi.proxy.domaintransitoire.Gh0jlSdiResourcetype
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class Gh0jlSdiResourcetypeHome {

	private static final Log log = LogFactory
			.getLog(Gh0jlSdiResourcetypeHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public Gh0jlSdiResourcetype findById(Integer id) {
		log.debug("getting Gh0jlSdiResourcetype instance with id: " + id);
		try {
			Gh0jlSdiResourcetype instance = (Gh0jlSdiResourcetype) sessionFactory
					.getCurrentSession().get(Gh0jlSdiResourcetype.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(Gh0jlSdiResourcetype transientInstance) {
		log.debug("save Gh0jlSdiResourcetype instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(Gh0jlSdiResourcetype transientInstance) {
		log.debug("saveOrUpdate Gh0jlSdiResourcetype instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(Gh0jlSdiResourcetype transientInstance) {
		log.debug("update Gh0jlSdiResourcetype instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(Gh0jlSdiResourcetype transientInstance) {
		log.debug("delete Gh0jlSdiResourcetype instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(Gh0jlSdiResourcetype transientInstance) {
		log.debug("merge Gh0jlSdiResourcetype instance");
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
