package org.easysdi.publish.biz.diffuser;

import java.util.List;

import org.easysdi.publish.dat.dao.DiffuserDaoHelper;

public class DiffuserType {
	private String name;
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

	private long   typeId;
    
    public DiffuserType(){}
    
    public static List<DiffuserType> getAllDiffuserTypes() {

		return DiffuserDaoHelper.getDiffuserDao().getAllDiffuserTypes();

	}
    
}
