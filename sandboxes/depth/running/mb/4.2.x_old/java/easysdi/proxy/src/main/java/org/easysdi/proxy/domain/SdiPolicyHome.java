package org.easysdi.proxy.domain;

// Generated Apr 9, 2013 11:54:41 AM by Hibernate Tools 3.4.0.CR1
import java.util.ArrayList;
import java.util.Collection;
import java.util.Date;
import java.util.Iterator;
import java.util.List;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.hibernate.Query;
import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.stereotype.Repository;
import org.springframework.transaction.annotation.Transactional;

/**
 * Home object for domain model class SdiPolicy.
 *
 * @see org.easysdi.proxy.domain.SdiPolicy
 * @author Hibernate Tools
 */
@Transactional
@Repository
public class SdiPolicyHome {

    private static final Log log = LogFactory.getLog(SdiPolicyHome.class);

    @Autowired
    private SessionFactory sessionFactory;

    public SdiPolicy findById(Integer id) {
        log.debug("getting SdiPolicy instance with id: " + id);
        try {
            SdiPolicy instance = (SdiPolicy) sessionFactory.getCurrentSession()
                    .get(SdiPolicy.class, id);
            log.debug("get successful");
            return instance;
        } catch (RuntimeException re) {
            log.error("get failed", re);
            throw re;
        }
    }

    public SdiPolicy findByVirtualServiceAndUser(Integer virtualservice, Integer user, Collection<GrantedAuthority> authorities) {
        try {
            Session session = sessionFactory.getCurrentSession();
            Date currentDate = new Date();
            session.enableFilter("entityState");
            //Anonymous request gives a null user here
            if (user != null) {
                //Policies linked to the current user
                Query query = session.createQuery(
                        "SELECT p FROM SdiPolicy p INNER JOIN p.sdiUsers as u "
                        + "INNER JOIN p.sdiVirtualservice as vs WHERE u.id= :user"
                        + " AND vs.id = :virtualservice "
                        + "ORDER BY p.ordering asc");
                query.setParameter("user", user);
                query.setParameter("virtualservice", virtualservice);
                List results = query.setCacheRegion("SdiPolicyQueryCache").setCacheable(true).list();
                if (results != null && results.size() > 0) {
                    for (SdiPolicy policy : (List<SdiPolicy>) results) {
                        //Date validity is check out of the SQL query to not interfer with query cache
                        Date from = policy.getAllowfrom();
                        Date to = policy.getAllowto();
                        if (currentDate.after(from) && currentDate.before(to)) {
                            return policy;
                        } else {
                            continue;
                        }
                    }
                }
            }

            //Policies of the organisms which the current user is member of
            Collection<Integer> c = new ArrayList<Integer>();
            Iterator<GrantedAuthority> i = authorities.iterator();
			//Loop through the authority to remove those which are not SdiOrganism id
            //see EasysdiProvider.getAuthorities() to know how the authorities list is built
            while (i.hasNext()) {
                try {
                    Integer authority = Integer.parseInt(i.next().getAuthority());
                    c.add(authority);
                } catch (NumberFormatException e) {
                    //Keep going on
                }
            }

            if (c.size() > 0) {
                // Check organisms
                Query oQuery = session.createQuery(
                        "SELECT p  FROM SdiPolicy p INNER JOIN p.sdiOrganisms as po "
                        + "INNER JOIN p.sdiVirtualservice as vs WHERE po.id IN (:organism) "
                        + "AND vs.id = :virtualservice "
                        + "ORDER BY p.ordering asc");
                oQuery.setParameterList("organism", c);
                oQuery.setParameter("virtualservice", virtualservice);
                List oResults = oQuery.setCacheRegion("SdiPolicyQueryCache").setCacheable(true).list();
                if (oResults != null && oResults.size() > 0) {
                    for (SdiPolicy policy : (List<SdiPolicy>) oResults) {
                        //Date validity is check out of the SQL query to not interfer with query cache
                        Date from = policy.getAllowfrom();
                        Date to = policy.getAllowto();
                        if (currentDate.after(from) && currentDate.before(to)) {
                            return policy;
                        } else {
                            continue;
                        }
                    }
                }

                // Check categories
                Query oQueryCo = session.createQuery(
                        "SELECT p  FROM SdiPolicy p INNER JOIN p.sdiCategories as pc INNER JOIN pc.sdiOrganisms as co "
                        + "INNER JOIN p.sdiVirtualservice as vs WHERE co.id IN (:organism) "
                        + "AND vs.id = :virtualservice "
                        + "ORDER BY p.ordering asc");
                
                oQueryCo.setParameterList("organism", c);
                oQueryCo.setParameter("virtualservice", virtualservice);
                
                List oResultsCo = oQueryCo.setCacheRegion("SdiPolicyQueryCache").setCacheable(true).list();
                
                if (oResultsCo != null && oResultsCo.size() > 0) {
                    for (SdiPolicy policy : (List<SdiPolicy>) oResultsCo) {
                        //Date validity is check out of the SQL query to not interfer with query cache
                        Date from = policy.getAllowfrom();
                        Date to = policy.getAllowto();
                        if (currentDate.after(from) && currentDate.before(to)) {
                            return policy;
                        } else {
                            continue;
                        }
                    }
                }

            }

			//If no authorities are SdiOrganism id, or if no policies are defined for the authorities, try to load a public policy
            //Public policies
            Query pQuery = session.createQuery(
                    "SELECT p  FROM SdiPolicy p INNER JOIN p.sdiVirtualservice as vs "
                    + "INNER JOIN p.sdiSysAccessscope as sc WHERE vs.id = :virtualservice "
                    + "AND sc.id = 1  "
                    + "ORDER BY p.ordering asc");
            pQuery.setParameter("virtualservice", virtualservice);
            List pResults = pQuery.setCacheRegion("SdiPolicyQueryCache").setCacheable(true).list();
            if (pResults != null && pResults.size() > 0) {
                for (SdiPolicy policy : (List<SdiPolicy>) pResults) {
                    //Date validity is check out of the SQL query to not interfer with query cache
                    Date from = policy.getAllowfrom();
                    Date to = policy.getAllowto();
                    if (currentDate.after(from) && currentDate.before(to)) {
                        return policy;
                    } else {
                        continue;
                    }
                }
            }

            return null;
        } catch (RuntimeException re) {
            log.error("get failed", re);
            throw re;
        }
    }

    public void save(SdiPolicy transientInstance) {
        log.debug("save SdiPolicy instance");
        try {
            sessionFactory.getCurrentSession().save(transientInstance);
            log.debug("save successful");
        } catch (RuntimeException re) {
            log.error("save failed", re);
            throw re;
        }
    }

    public void saveOrUpdate(SdiPolicy transientInstance) {
        log.debug("saveOrUpdate SdiPolicy instance");
        try {
            sessionFactory.getCurrentSession().saveOrUpdate(transientInstance);
            log.debug("saveOrUpdate successful");
        } catch (RuntimeException re) {
            log.error("saveOrUpdate failed", re);
            throw re;
        }
    }

    public void update(SdiPolicy transientInstance) {
        log.debug("update SdiPolicy instance");
        try {
            sessionFactory.getCurrentSession().update(transientInstance);
            log.debug("update successful");
        } catch (RuntimeException re) {
            log.error("update failed", re);
            throw re;
        }
    }

    public void delete(SdiPolicy transientInstance) {
        log.debug("delete SdiPolicy instance");
        try {
            sessionFactory.getCurrentSession().delete(transientInstance);
            log.debug("delete successful");
        } catch (RuntimeException re) {
            log.error("delete failed", re);
            throw re;
        }
    }

    public void merge(SdiPolicy transientInstance) {
        log.debug("merge SdiPolicy instance");
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
