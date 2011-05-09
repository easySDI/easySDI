package org.easysdi.publish.dat.dao.hibernate;

import java.util.List;

import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.layer.FeatureSource;
import org.easysdi.publish.dat.dao.FeatureSourceDaoHelper;
import org.easysdi.publish.dat.dao.IFeatureSourceDao;
import org.hibernate.SessionFactory;
import org.springframework.dao.DataAccessException;
import org.springframework.orm.hibernate3.support.HibernateDaoSupport;

public class FeatureSourceDao extends HibernateDaoSupport implements IFeatureSourceDao{

	public FeatureSourceDao(SessionFactory sessionFactory) {
        this.setSessionFactory(sessionFactory);
        FeatureSourceDaoHelper.setFeatureSourceDao(this);
    }
	
	public void delete(FeatureSource fs) {
            this.getHibernateTemplate().delete(fs);
	}

	public void persist(FeatureSource fs) {
            this.getHibernateTemplate().saveOrUpdate(fs);
	}
	
	public FeatureSource getFeatureSourceFromIdString(String identifyString) {

        try {
            final long fsId = Long.parseLong(identifyString);

            return this.getFeatureSourceById(fsId);

        } catch (NumberFormatException e) {

            return this.getFeatureSourceByName(identifyString);
        }
    }
	
	public FeatureSource getFeatureSourceById(long id) {

        if (1 > id) {
            throw new IllegalArgumentException("Invalid diff identifier");
        }

        // return (Job) SessionUtil.getCurrentSession().load(Job.class, jobId);
        return this.getHibernateTemplate().get(FeatureSource.class, id);
    }



    /**
     * {@inheritDoc}
     */
    public FeatureSource getFeatureSourceByName(String name) {

        if (null == name || name.equals("")) {
            throw new IllegalArgumentException(
                                             "Diffuser name can't be null or empty");
        }

        final List<?> result 
            = this.getHibernateTemplate().findByNamedParam(
                  "from FeatureSource where name = :name", "name", name);

        if (null != result && 0 < result.size()) {
            return (FeatureSource) result.get(0);
        }

        return null;
    }

}
