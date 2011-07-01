package org.easysdi.monitor.dat.dao.hibernate;

import java.util.List;

import org.easysdi.monitor.biz.alert.Alert;
import org.easysdi.monitor.dat.dao.AlertDaoHelper;
import org.easysdi.monitor.dat.dao.IAlertDao;
import org.hibernate.SessionFactory;
import org.hibernate.criterion.DetachedCriteria;
import org.hibernate.criterion.Restrictions;
import org.springframework.orm.hibernate3.support.HibernateDaoSupport;

/**
 * Provides alert persistance operations through Hibernate.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
public class AlertDao extends HibernateDaoSupport implements IAlertDao {

    /**
     * Creates a new alert data access object.
     * 
     * @param   sessionFactory  the Hibernate session factory object
     */
    public AlertDao(SessionFactory sessionFactory) {
        this.setSessionFactory(sessionFactory);
        AlertDaoHelper.setAlertDao(this);
    }



    /**
     * {@inheritDoc}
     */
    public void persist(Alert alert) {

        this.getHibernateTemplate().saveOrUpdate(alert);

    }



    /**
     * {@inheritDoc}
     */
    @SuppressWarnings("unchecked")
    public List<Alert> getAlertsForJob(long jobId, boolean onlyRss) {

        final DetachedCriteria search = DetachedCriteria.forClass(Alert.class);

        search.add(Restrictions.eq("parentJob.jobId", jobId));

        if (onlyRss) {
            search.add(Restrictions.eq("exposedToRss", true));
        }

        return this.getHibernateTemplate().findByCriteria(search);
    }


    /**
     * {@inheritDoc}
     */
    @SuppressWarnings("unchecked")
	public List<Alert> getAlertsForJob(long jobId, boolean onlyRss,
			Integer start, Integer limit) {
    	final DetachedCriteria search = DetachedCriteria.forClass(Alert.class);

        search.add(Restrictions.eq("parentJob.jobId", jobId));

        if (onlyRss) {
            search.add(Restrictions.eq("exposedToRss", true));
        }
        search.getExecutableCriteria(this.getSession()).setMaxResults(limit).setFirstResult(start);
        return this.getHibernateTemplate().findByCriteria(search);
	}
}
