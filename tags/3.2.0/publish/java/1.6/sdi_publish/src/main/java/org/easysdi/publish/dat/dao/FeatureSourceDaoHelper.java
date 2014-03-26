package org.easysdi.publish.dat.dao;

public class FeatureSourceDaoHelper {
	
	private static IFeatureSourceDao dao;
	
	private FeatureSourceDaoHelper() {

        throw new UnsupportedOperationException(
                "This class can't be instantiated.");
    }
	
    public static void setFeatureSourceDao(IFeatureSourceDao newFeatureSourceDao) {
    	FeatureSourceDaoHelper.dao = newFeatureSourceDao;
    }

    public static IFeatureSourceDao getFeatureSourceDao() {
        return FeatureSourceDaoHelper.dao;
    }

}
