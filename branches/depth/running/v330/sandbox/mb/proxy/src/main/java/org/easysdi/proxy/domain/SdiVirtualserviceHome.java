package org.easysdi.proxy.domain;

// Generated Apr 9, 2013 11:54:42 AM by Hibernate Tools 3.4.0.CR1

import java.util.List;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.Query;
import org.hibernate.SessionFactory;
import org.hibernate.Session;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiVirtualservice.
 * @see org.easysdi.proxy.domain.SdiVirtualservice
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiVirtualserviceHome {

	private static final Log log = LogFactory
			.getLog(SdiVirtualserviceHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiVirtualservice findById(Integer id) {
		log.debug("getting SdiVirtualservice instance with id: " + id);
		try {
			SdiVirtualservice instance = (SdiVirtualservice) sessionFactory
					.getCurrentSession().get(SdiVirtualservice.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public SdiVirtualservice findByAlias(String alias) {
		try {
			Session session = sessionFactory.getCurrentSession();
//			session.enableFilter("entityState");
			Query query = session.createQuery("from SdiVirtualservice where alias= :alias");
			query.setParameter("alias", alias);
			List<SdiVirtualservice> l = query.setCacheable(true).list();
			if(l != null && l.size() > 0 )
			{
				SdiVirtualservice instance = l.get(0);
				return instance;
			}
			return null;
		} catch (RuntimeException re) {
			throw re;
		}
	}

	public void save(SdiVirtualservice transientInstance) {
		log.debug("save SdiVirtualservice instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiVirtualservice transientInstance) {
		log.debug("saveOrUpdate SdiVirtualservice instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiVirtualservice transientInstance) {
		log.debug("update SdiVirtualservice instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiVirtualservice transientInstance) {
		log.debug("delete SdiVirtualservice instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiVirtualservice transientInstance) {
		log.debug("merge SdiVirtualservice instance");
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
