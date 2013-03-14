package org.easysdi.domain;

import org.hibernate.SessionFactory;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;


@Transactional
@Repository
public class ProxySdiUserDao {

	private SessionFactory sessionFactory;

	public ProxySdiUser findById(Integer id){
		return (ProxySdiUser)sessionFactory.getCurrentSession().get(ProxySdiUser.class, id);
	}
	
	public Integer save(ProxySdiUser o){
		return (Integer)sessionFactory.getCurrentSession().save(o);
	}
	
	public SessionFactory getSessionFactory() {
		return sessionFactory;
	}

	public void setSessionFactory(SessionFactory sessionFactory) {
		this.sessionFactory = sessionFactory;
	}
	
}
