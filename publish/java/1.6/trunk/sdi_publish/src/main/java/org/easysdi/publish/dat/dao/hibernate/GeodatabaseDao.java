package org.easysdi.publish.dat.dao.hibernate;

import java.util.LinkedList;
import java.util.List;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.biz.database.GeodatabaseType;
import org.easysdi.publish.biz.diffuser.DiffuserType;
import org.easysdi.publish.dat.dao.GeodatabaseDaoHelper;
import org.easysdi.publish.dat.dao.IGeodatabaseDao;
import org.hibernate.SessionFactory;
import org.hibernate.criterion.DetachedCriteria;
import org.hibernate.criterion.Restrictions;
import org.springframework.dao.DataAccessException;
import org.springframework.orm.hibernate3.HibernateTemplate;
import org.springframework.orm.hibernate3.support.HibernateDaoSupport;

/**
 * Provides action persistance operations through Hibernate.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.0, 2010-03-19
 *
 */
public class GeodatabaseDao extends HibernateDaoSupport implements IGeodatabaseDao {

	/**
	 * Creates a new action data access object.
	 * 
	 * @param   sessionFactory  the Hibernate session factory object
	 */
	public GeodatabaseDao(SessionFactory sessionFactory) {
		this.setSessionFactory(sessionFactory);
		GeodatabaseDaoHelper.setGeodatabaseDao(this);
	}



	/**
	 * {@inheritDoc}
	 */
	@SuppressWarnings("unchecked")
	public GeodatabaseType getType(long id) {
		final DetachedCriteria search 
		= DetachedCriteria.forClass(GeodatabaseType.class);
		search.add(Restrictions.eq("typeId", id));
		final List<GeodatabaseType> result 
		= this.getHibernateTemplate().findByCriteria(search);

		if (null == result || 1 > result.size()) {
			return null;
		}

		return result.get(0);
	}



	/**
	 * {@inheritDoc}
	 */
	public void persist(Geodatabase geodb) {
		this.getHibernateTemplate().saveOrUpdate(geodb);
	}


	public Geodatabase getGeodatabaseFromIdString(String identifyString) {

		try {
			final long geodbId = Long.parseLong(identifyString);

			return this.getGeodatabaseById(geodbId);

		} catch (NumberFormatException e) {

			return this.getGeodatabasebByName(identifyString);
		}
	}


	/**
	 * {@inheritDoc}
	 */
	 public void delete(Geodatabase geodb) {
		 this.getHibernateTemplate().delete(geodb);
	 }


	 /**
	  * {@inheritDoc}
	  */
	 public Geodatabase getGeodatabaseById(long geodbId) {

		 if (1 > geodbId) {
			 throw new IllegalArgumentException("Invalid geodb identifier");
		 }

		 // return (Job) SessionUtil.getCurrentSession().load(Job.class, jobId);
		 return this.getHibernateTemplate().get(Geodatabase.class, geodbId);
	 }



	 /**
	  * {@inheritDoc}
	  */
	 public Geodatabase getGeodatabasebByName(String name) {

		 if (null == name || name.equals("")) {
			 throw new IllegalArgumentException(
			 "Geodatabase name can't be null or empty");
		 }

		 final List<?> result 
		 = this.getHibernateTemplate().findByNamedParam(
				 "from Geodatabase where name = :name", "name", name);

		 if (null != result && 0 < result.size()) {
			 return (Geodatabase) result.get(0);
		 }

		 return null;
	 }

	 public List<GeodatabaseType> getAllGeodatabaseTypes() {

		 final HibernateTemplate hibernateTemplate = this.getHibernateTemplate();
		 final List<GeodatabaseType> result = this.typeGeodatabaseTypeResultList(hibernateTemplate.loadAll(GeodatabaseType.class));

		 if (null == result) {
			 return new LinkedList<GeodatabaseType>();
		 }

		 return result;
	 }

	 private List<GeodatabaseType> typeGeodatabaseTypeResultList(List<?> resultList) {
		 final List<GeodatabaseType> geodatabaseTypesFound = new LinkedList<GeodatabaseType>();

		 for (Object typeObject : resultList) {

			 if (typeObject instanceof GeodatabaseType) {
				 geodatabaseTypesFound.add((GeodatabaseType) typeObject);
			 }
		 }

		 return geodatabaseTypesFound;
	 }

}
