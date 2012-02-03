package org.easysdi.publish.dat.dao;

import org.easysdi.publish.biz.layer.FeatureSource;
import org.easysdi.publish.biz.layer.Layer;

public interface ILayerDao {
	void delete(Layer l);
	void persist(Layer l);
	Layer getLayerFromIdString(String identifyString);
	Layer getLayerFromGuid(String guid);
	FeatureSource getFeatureSourceFromGuid(String guid);
	boolean isLayerBoundToFeatureSource(FeatureSource fs);
}
