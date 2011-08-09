package org.easysdi.publish.dat.dao.hibernate;

import java.util.LinkedList;
import java.util.List;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.biz.database.GeodatabaseType;
import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.diffuser.DiffuserType;
import org.easysdi.publish.dat.dao.DiffuserDaoHelper;
import org.easysdi.publish.dat.dao.GeodatabaseDaoHelper;
import org.easysdi.publish.dat.dao.IDiffuserDao;
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
public class DiffuserDao extends HibernateDaoSupport implements IDiffuserDao {

	/**
	 * Creates a new action data access object.
	 * 
	 * @param   sessionFactory  the Hibernate session factory object
	 */
	public DiffuserDao(SessionFactory sessionFactory) {
		this.setSessionFactory(sessionFactory);
		DiffuserDaoHelper.setDiffuserDao(this);
	}



	/**
	 * {@inheritDoc}
	 */
	@SuppressWarnings("unchecked")
	public DiffuserType getType(String typeName) {
		final DetachedCriteria search 
		= DetachedCriteria.forClass(DiffuserType.class);
		search.add(Restrictions.eq("name", typeName));
		final List<DiffuserType> result 
		= this.getHibernateTemplate().findByCriteria(search);

		if (null == result || 1 > result.size()) {
			return null;
		}

		return result.get(0);
	}



	/**
	 * {@inheritDoc}
	 */
	public void persist(Diffuser diff) {
			this.getHibernateTemplate().saveOrUpdate(diff);
	}


	public Diffuser getDiffuserFromIdString(String identifyString) {

		try {
			final long diffId = Long.parseLong(identifyString);

			return this.getDiffuserById(diffId);

		} catch (NumberFormatException e) {

			return this.getDiffuserByName(identifyString);
		}
	}


	/**
	 * {@inheritDoc}
	 */
	 public void delete(Diffuser diff) {
			 this.getHibernateTemplate().delete(diff);
	 }


	 /**
	  * {@inheritDoc}
	  */
	 public Diffuser getDiffuserById(long diffId) {
		 if (1 > diffId) {
			 throw new IllegalArgumentException("Invalid diff identifier");
		 }

		 // return (Job) SessionUtil.getCurrentSession().load(Job.class, jobId);
		 return this.getHibernateTemplate().get(Diffuser.class, diffId);
	 }



	 /**
	  * {@inheritDoc}
	  */
	 public Diffuser getDiffuserByName(String name) {

		 if (null == name || name.equals("")) {
			 throw new IllegalArgumentException(
			 "Diffuser name can't be null or empty");
		 }

		 final List<?> result 
		 = this.getHibernateTemplate().findByNamedParam(
				 "from Diffuser where name = :name", "name", name);

		 if (null != result && 0 < result.size()) {
			 return (Diffuser) result.get(0);
		 }

		 return null;
	 }

	 public List<Diffuser> getAllDiffusers() {

		 final HibernateTemplate hibernateTemplate = this.getHibernateTemplate();
		 final List<Diffuser> result = this.typeDiffuserResultList(hibernateTemplate.loadAll(Diffuser.class));

		 if (null == result) {
			 return new LinkedList<Diffuser>();
		 }

		 return result;
	 }

	 private List<Diffuser> typeDiffuserResultList(List<?> resultList) {
		 final List<Diffuser> diffusersFound = new LinkedList<Diffuser>();

		 for (Object diffObject : resultList) {

			 if (diffObject instanceof Diffuser) {
				 diffusersFound.add((Diffuser) diffObject);
			 }
		 }

		 return diffusersFound;
	 }

	 public List<DiffuserType> getAllDiffuserTypes() {

		 final HibernateTemplate hibernateTemplate = this.getHibernateTemplate();
		 final List<DiffuserType> result = this.typeDiffuserTypeResultList(hibernateTemplate.loadAll(DiffuserType.class));

		 if (null == result) {
			 return new LinkedList<DiffuserType>();
		 }

		 return result;
	 }
	 
	 private List<DiffuserType> typeDiffuserTypeResultList(List<?> resultList) {
		 final List<DiffuserType> diffuserTypesFound = new LinkedList<DiffuserType>();

		 for (Object typeObject : resultList) {

			 if (typeObject instanceof DiffuserType) {
				 diffuserTypesFound.add((DiffuserType) typeObject);
			 }
		 }

		 return diffuserTypesFound;
	 }

}
