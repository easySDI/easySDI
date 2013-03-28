package org.easysdi.proxy.domain;

// Generated Mar 28, 2013 4:35:10 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiPolicyMetadatastate.
 * @see org.easysdi.proxy.domain.SdiPolicyMetadatastate
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiPolicyMetadatastateHome {

	private static final Log log = LogFactory
			.getLog(SdiPolicyMetadatastateHome.class);

	private SessionFactory sessionFactory;

	public SdiPolicyMetadatastate findById(Integer id) {
		log.debug("getting SdiPolicyMetadatastate instance with id: " + id);
		try {
			SdiPolicyMetadatastate instance = (SdiPolicyMetadatastate) sessionFactory
					.getCurrentSession().get(SdiPolicyMetadatastate.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiPolicyMetadatastate transientInstance) {
		log.debug("save SdiPolicyMetadatastate instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiPolicyMetadatastate transientInstance) {
		log.debug("saveOrUpdate SdiPolicyMetadatastate instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiPolicyMetadatastate transientInstance) {
		log.debug("update SdiPolicyMetadatastate instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiPolicyMetadatastate transientInstance) {
		log.debug("delete SdiPolicyMetadatastate instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiPolicyMetadatastate transientInstance) {
		log.debug("merge SdiPolicyMetadatastate instance");
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
