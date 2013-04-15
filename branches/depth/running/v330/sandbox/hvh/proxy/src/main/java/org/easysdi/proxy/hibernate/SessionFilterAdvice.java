package org.easysdi.proxy.hibernate;

import org.hibernate.Session;

public class SessionFilterAdvice {

	public void setupFilter(Session session)
    {
       session.enableFilter("entityState");
    }
}
