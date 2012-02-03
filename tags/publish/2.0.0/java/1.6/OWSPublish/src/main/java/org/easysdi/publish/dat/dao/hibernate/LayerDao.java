package org.easysdi.publish.dat.dao.hibernate;

import java.util.List;

import org.easysdi.publish.biz.layer.FeatureSource;
import org.easysdi.publish.biz.layer.Layer;
import org.easysdi.publish.dat.dao.FeatureSourceDaoHelper;
import org.easysdi.publish.dat.dao.ILayerDao;
import org.easysdi.publish.dat.dao.LayerDaoHelper;
import org.hibernate.SessionFactory;
import org.hibernate.criterion.DetachedCriteria;
import org.hibernate.criterion.Restrictions;
import org.springframework.dao.DataAccessException;
import org.springframework.orm.hibernate3.support.HibernateDaoSupport;

public class LayerDao extends HibernateDaoSupport implements ILayerDao{

	public LayerDao(SessionFactory sessionFactory) {
		this.setSessionFactory(sessionFactory);
		LayerDaoHelper.setLayerDao(this);
	}

	public void delete(Layer l) {
		this.getHibernateTemplate().delete(l);
	}

	public void persist(Layer l) {
		this.getHibernateTemplate().saveOrUpdate(l);
	}

	public Layer getLayerFromGuid(String guid) {

		if (null == guid || guid.equals("")) {
			throw new IllegalArgumentException(
			"Layer guid can't be null or empty");
		}

		final List<?> result 
		= this.getHibernateTemplate().findByNamedParam(
				"from Layer where guid = :guid", "guid", guid);

		if (null != result && 0 < result.size()) {
			return (Layer) result.get(0);
		}

		return null;
	}
	
	public FeatureSource getFeatureSourceFromGuid(String guid) {

		if (null == guid || guid.equals("")) {
			throw new IllegalArgumentException(
			"FeatureSource guid can't be null or empty");
		}

		final List<?> result 
		= this.getHibernateTemplate().findByNamedParam(
				"from FeatureSource where guid = :guid", "guid", guid);

		if (null != result && 0 < result.size()) {
			return (FeatureSource) result.get(0);
		}

		return null;
	}
	
	public Layer getLayerFromIdString(String identifyString) {

		try {
			final long layerId = Long.parseLong(identifyString);

			return this.getLayerById(layerId);

		} catch (NumberFormatException e) {

			return this.getLayerByName(identifyString);
		}
	}

	public Layer getLayerById(long id) {

		if (1 > id) {
			throw new IllegalArgumentException("Invalid diff identifier");
		}
		System.out.println("id"+id);
		return this.getHibernateTemplate().get(Layer.class, id);
	}



	/**
	 * {@inheritDoc}
	 */
	public Layer getLayerByName(String name) {

		if (null == name || name.equals("")) {
			throw new IllegalArgumentException(
			"Diffuser name can't be null or empty");
		}

		final List<?> result 
		= this.getHibernateTemplate().findByNamedParam(
				"from Layer where name = :name", "name", name);

		if (null != result && 0 < result.size()) {
			return (Layer) result.get(0);
		}

		return null;
	}
	
	public boolean isLayerBoundToFeatureSource(FeatureSource fs) {

        final DetachedCriteria search = DetachedCriteria.forClass(Layer.class);

        search.add(Restrictions.eq("featureSource.featureSourceId", fs.getFeatureSourceId()));

        List<Layer> lLayers = this.getHibernateTemplate().findByCriteria(search);
        
        if(lLayers.size() > 0)
        	return true;
        else
        	return false;
    }

}
