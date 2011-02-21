package org.easysdi.publish.biz.database;

import java.util.List;

import org.easysdi.publish.biz.diffuser.DiffuserType;
import org.easysdi.publish.dat.dao.DiffuserDaoHelper;
import org.easysdi.publish.dat.dao.GeodatabaseDaoHelper;

public class GeodatabaseType {
	private String name;
	private long   typeId;
	
    public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public long getTypeId() {
		return typeId;
	}

	public void setTypeId(long typeId) {
		this.typeId = typeId;
	}
    
    public GeodatabaseType(){}
    
    public static List<GeodatabaseType> getAllGeodatabaseTypes() {

		return GeodatabaseDaoHelper.getGeodatabaseDao().getAllGeodatabaseTypes();

	}
    
}
