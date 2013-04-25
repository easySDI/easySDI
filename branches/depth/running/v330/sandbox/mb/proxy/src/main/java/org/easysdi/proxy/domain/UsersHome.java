package org.easysdi.proxy.domain;

// Generated Apr 9, 2013 11:54:41 AM by Hibernate Tools 3.4.0.CR1

import net.sf.json.JSONObject;
import net.sf.json.JSONSerializer;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.Query;
import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class Users.
 * @see org.easysdi.proxy.domain.Users
 * @author Hibernate Tools
 */

@Transactional
@Repository
public class UsersHome {

	private static final Log log = LogFactory.getLog(UsersHome.class);

	@Autowired
	private SessionFactory sessionFactory;
	
	@Autowired
	private ExtensionsHome extensionsHome;

	public Users findById(Integer id) {
		log.debug("getting Users instance with id: " + id);
		try {
			Session session = sessionFactory.getCurrentSession();
//			session.enableFilter("entityState");
			Users instance = (Users) session.get(
					Users.class, id);
			log.debug("get successful");
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}
	
	public Users findBySession(String jsession) {
		try {
			//TODO replace by sessionHome.findById()
			Session session = sessionFactory.getCurrentSession();
//			session.enableFilter("entityState");
			Query qSession = session.createQuery("Select s FROM Session s WHERE session_id= :session ");
			qSession.setParameter("session", jsession);
			Object oSession = (Object) qSession.setCacheable(true).uniqueResult();
			
			if(oSession == null)
				return null;
			
			Query query = sessionFactory.getCurrentSession().createQuery("Select u FROM Session s, Users u WHERE u.username = s.username AND session_id= :session ");
			query.setParameter("session", session);
			Users instance = (Users) query.setCacheable(true).setCacheRegion("userCache").uniqueResult();
			if(instance == null)
				instance = new Users();
			
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}
	
	public Users findByUserName(String username) {
		try {
			Session session = sessionFactory.getCurrentSession();
//			session.enableFilter("entityState");
			Query query = session.createQuery(" FROM Users WHERE username= :username");
			query.setParameter("username", username);
			Users instance = (Users) query.setCacheable(true).uniqueResult();
			
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}
	
	public Users findGuest() {
		try {
			Extensions extension = extensionsHome.findByName("com_easysdi_contact");
			String params = extension.getParams();
			JSONObject json = (JSONObject) JSONSerializer.toJSON( params );        
	        int guestaccount = json.getInt("guestaccount" );
	        Users instance = this.findById(guestaccount);
			
			return instance;
		} catch (RuntimeException re) {
			log.error("get failed", re);
			throw re;
		}
	}

	public void save(Users transientInstance) {
		log.debug("save Users instance");
		try {
			sessionFactory.getCurrentSession().save(transientInstance);
			log.debug("save successful");
		} catch (RuntimeException re) {
			log.error("save failed", re);
			throw re;
		}
	}

	public void saveOrUpdate(Users transientInstance) {
		log.debug("saveOrUpdate Users instance");
		try {
			sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
			log.debug("saveOrUpdate successful");
		} catch (RuntimeException re) {
			log.error("saveOrUpdate failed", re);
			throw re;
		}
	}

	public void update(Users transientInstance) {
		log.debug("update Users instance");
		try {
			sessionFactory.getCurrentSession().update(transientInstance);
			log.debug("update successful");
		} catch (RuntimeException re) {
			log.error("update failed", re);
			throw re;
		}
	}

	public void delete(Users transientInstance) {
		log.debug("delete Users instance");
		try {
			sessionFactory.getCurrentSession().delete(transientInstance);
			log.debug("delete successful");
		} catch (RuntimeException re) {
			log.error("delete failed", re);
			throw re;
		}
	}

	public void merge(Users transientInstance) {
		log.debug("merge Users instance");
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
