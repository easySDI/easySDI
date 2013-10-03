package org.easysdi.proxy.domain;

// Generated Oct 3, 2013 2:18:25 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiMetadata.
 * @see org.easysdi.proxy.domaintransitoire.SdiMetadata
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiMetadataHome {

	private static final Log log = LogFactory
			.getLog(SdiMetadataHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiMetadata findById(Integer id) {
		log.debug("getting SdiMetadata instance with id: " + id);
		try {
			SdiMetadata instance = (SdiMetadata) sessionFactory
					.getCurrentSession().get(SdiMetadata.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiMetadata transientInstance) {
		log.debug("save SdiMetadata instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiMetadata transientInstance) {
		log.debug("saveOrUpdate SdiMetadata instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiMetadata transientInstance) {
		log.debug("update SdiMetadata instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiMetadata transientInstance) {
		log.debug("delete SdiMetadata instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiMetadata transientInstance) {
		log.debug("merge SdiMetadata instance");
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
