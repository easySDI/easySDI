package org.easysdi.proxy.domain;

// Generated Oct 4, 2013 10:20:01 AM by Hibernate Tools 3.4.0.CR1

import java.util.Date;
import java.util.List;
import java.util.Set;
import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.Query;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiAccessscope.
 * @see org.easysdi.proxy.domaintransitoire.SdiAccessscope
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class SdiAccessscopeHome {

	private static final Log log = LogFactory
			.getLog(SdiAccessscopeHome.class);

	@Autowired
	private SessionFactory sessionFactory;

	public SdiAccessscope findById(Integer id) {
		log.debug("getting SdiAccessscope instance with id: " + id);
		try {
			SdiAccessscope instance = (SdiAccessscope) sessionFactory
					.getCurrentSession().get(SdiAccessscope.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}
        
        public List findByGuid(String guid) {
		log.debug("getting SdiAccessscope instance with guid: " + guid);
		try {
			org.hibernate.Session session = sessionFactory.getCurrentSession();
			session.enableFilter("entityState");
			
			if(guid != null){
			
				Query query = session.createQuery(
						"SELECT a FROM SdiAccessscope a  WHERE a.entity_guid= :guid" );
				query.setParameter("guid", guid);
				
				List results = query.list();
				return results;
			}
                        return null;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(SdiAccessscope transientInstance) {
		log.debug("save SdiAccessscope instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(SdiAccessscope transientInstance) {
		log.debug("saveOrUpdate SdiAccessscope instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(SdiAccessscope transientInstance) {
		log.debug("update SdiAccessscope instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(SdiAccessscope transientInstance) {
		log.debug("delete SdiAccessscope instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(SdiAccessscope transientInstance) {
		log.debug("merge SdiAccessscope instance");
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
