package org.easysdi.publish.dat.dao;

public class LayerDaoHelper {
	
	private static ILayerDao dao;
	
	private LayerDaoHelper() {

        throw new UnsupportedOperationException(
                "This class can't be instantiated.");
    }
	
    public static void setLayerDao(ILayerDao newLayerDao) {
    	LayerDaoHelper.dao = newLayerDao;
    }

    public static ILayerDao getLayerDao() {
        return LayerDaoHelper.dao;
    }

}
