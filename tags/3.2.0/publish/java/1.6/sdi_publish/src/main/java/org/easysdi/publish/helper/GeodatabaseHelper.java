package org.easysdi.publish.helper;

import java.util.logging.Logger;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.security.CurrentUser;

public class GeodatabaseHelper {

	Logger logger = Logger.getLogger("org.easysdi.publish.helper.GeodatabaseHelper");
	
	private static GeodatabaseHelper instance;
	private static IHelper helper;

	private GeodatabaseHelper(Geodatabase geoDb) throws InstantiationException, IllegalAccessException, ClassNotFoundException, PublishConfigurationException{
		
		String strDbTypeClass = geoDb.getGeodatabaseType().getName().substring(0, 1).toUpperCase() +  geoDb.getGeodatabaseType().getName().substring(1);
		logger.info("strDbTypeClass:"+strDbTypeClass+" type:"+geoDb.getGeodatabaseType().getName()+" url:"+geoDb.getUrl());  
		helper = (IHelper) Class.forName("org.easysdi.publish."+geoDb.getGeodatabaseType().getName()+"helper."+strDbTypeClass+"Helper").newInstance();
		helper.setConnectionInfo(geoDb,CurrentUser.getCurrentPrincipal());
	}
	
	public static IHelper getInstance(Geodatabase g) throws InstantiationException, IllegalAccessException, ClassNotFoundException, PublishConfigurationException {
        if (null == instance) {
            instance = new GeodatabaseHelper(g);
        }
        return instance.helper;
    }

	
}
