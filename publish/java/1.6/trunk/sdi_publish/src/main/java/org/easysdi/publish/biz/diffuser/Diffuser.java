package org.easysdi.publish.biz.diffuser;

import java.util.List;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.dat.dao.DiffuserDaoHelper;
import org.easysdi.publish.dat.dao.hibernate.DiffuserDao;


public class Diffuser {
	private long                    diffuserId;
	private Geodatabase             geodatabase;
	private String                  name;
	private String                  url;
	private String                  user;
	private String                  pwd;
	private long                    type;
	
	
	public Diffuser(){
		
	}
	
	public long getDiffuserId() {
		return diffuserId;
	}



	public void setDiffuserId(long diffuserId) {
		this.diffuserId = diffuserId;
	}

	public Geodatabase getGeodatabase() {
		return geodatabase;
	}



	public void setGeodatabase(Geodatabase geodatabase) {
		this.geodatabase = geodatabase;
	}



	public String getName() {
		return name;
	}



	public void setName(String name) {
		this.name = name;
	}



	public String getUrl() {
		return url;
	}



	public void setUrl(String url) {
		this.url = url;
	}



	public String getUser() {
		return user;
	}



	public void setUser(String user) {
		this.user = user;
	}



	public String getPwd() {
		return pwd;
	}



	public void setPwd(String pwd) {
		this.pwd = pwd;
	}



	public long getType() {
		return type;
	}



	public void setType(long type) {
		this.type = type;
	}



	public static Diffuser getFromIdString(String idString) {

        if (idString == null || idString.equals("")) {
            throw new IllegalArgumentException(
                   "Geodatabase identifier string can't be null or empty.");
        }
                
        
        return DiffuserDaoHelper.getDiffuserDao().getDiffuserFromIdString(idString);
    }
	
    public void persist() {
    	
        DiffuserDaoHelper.getDiffuserDao().persist(this);
    
    }
    
    public void delete() {
        DiffuserDaoHelper.getDiffuserDao().delete(this);
    }
    
    public static List<Diffuser> getAllDiffusers() {

		return DiffuserDaoHelper.getDiffuserDao().getAllDiffusers();

	}
	
}
