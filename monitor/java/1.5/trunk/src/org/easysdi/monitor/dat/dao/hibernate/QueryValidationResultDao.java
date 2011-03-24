package org.easysdi.monitor.dat.dao.hibernate;

import org.easysdi.monitor.biz.job.QueryValidationResult;
import org.easysdi.monitor.dat.dao.IQueryValidationResultDao;
import org.easysdi.monitor.dat.dao.QueryValidationResultDaoHelper;
import org.hibernate.SessionFactory;
import org.springframework.dao.DataAccessException;

public class QueryValidationResultDao extends AbstractDao implements
		IQueryValidationResultDao {

	public QueryValidationResultDao(SessionFactory sessionFactory) {
        this.setSessionFactory(sessionFactory);
        QueryValidationResultDaoHelper.setDao(this);
    }

	public boolean persist(QueryValidationResult queryValidationResult) {
		if (null == queryValidationResult) {
			throw new IllegalArgumentException("queryValidationResult can't be null");
		}

		try {
			this.getHibernateTemplate().saveOrUpdate(queryValidationResult);
			this.getHibernateTemplate().flush();
			return true;

		} catch (DataAccessException e) {
			return false;
		}
	}

}
