package org.easysdi.publish.dat.dao;

import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.layer.FeatureSource;

public interface IFeatureSourceDao {
	void delete(FeatureSource fs);
	void persist(FeatureSource fs);
	FeatureSource getFeatureSourceFromIdString(String identifyString);
	
}
