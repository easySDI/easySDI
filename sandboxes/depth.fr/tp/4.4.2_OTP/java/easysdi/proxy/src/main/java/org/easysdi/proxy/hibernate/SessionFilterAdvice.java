package org.easysdi.proxy.hibernate;

import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

@Service
public class SessionFilterAdvice {
	
		public Session setupFilter(Session session)
    {
			session.enableFilter("entityState");
			return session;
    }
}
