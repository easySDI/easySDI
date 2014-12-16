package org.easysdi.proxy.domain;

// Generated Apr 12, 2013 1:58:17 PM by Hibernate Tools 3.4.0.CR1

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiSysMetadataversion.
 * @see org.easysdi.proxy.domaintransitoire.SdiSysMetadataversion
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiSysMetadataversionHome {

	private static final Log log = LogFactory
			.getLog(SdiSysMetadataversionHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiSysMetadataversion findById(Integer id) {
		log.debug("getting SdiSysMetadataversion instance with id: " + id);
		try {
			SdiSysMetadataversion instance = (SdiSysMetadataversion) sessionFactory
					.getCurrentSession().get(SdiSysMetadataversion.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiSysMetadataversion transientInstance) {
		log.debug("save SdiSysMetadataversion instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiSysMetadataversion transientInstance) {
		log.debug("saveOrUpdate SdiSysMetadataversion instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiSysMetadataversion transientInstance) {
		log.debug("update SdiSysMetadataversion instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiSysMetadataversion transientInstance) {
		log.debug("delete SdiSysMetadataversion instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiSysMetadataversion transientInstance) {
		log.debug("merge SdiSysMetadataversion instance");
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
