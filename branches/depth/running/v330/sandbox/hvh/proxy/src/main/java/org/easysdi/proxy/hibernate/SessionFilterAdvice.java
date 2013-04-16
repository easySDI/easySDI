package org.easysdi.proxy.hibernate;

import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;

public class SessionFilterAdvice {
	
		public void setupFilter(Session session)
    {
			session.enableFilter("entityState");
    }
}
