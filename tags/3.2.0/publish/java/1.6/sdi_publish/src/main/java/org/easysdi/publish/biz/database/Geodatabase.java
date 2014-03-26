package org.easysdi.publish.biz.database;

import org.easysdi.publish.dat.dao.GeodatabaseDaoHelper;
import org.easysdi.publish.dat.dao.IGeodatabaseDao;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.helper.GeodatabaseHelper;
import org.easysdi.publish.helper.IHelper;

public class Geodatabase {
	private long                    geodatabaseId;
	private String                  name;
	private String                  url;
	private String                  user;
	private String                  pwd;
	private String                  scheme;
	private String                  template;
	private long                    geodatabaseTypeId;
	private GeodatabaseHelper		helper;
	
	public Geodatabase(){
		
	}

	public long getGeodatabaseId() {
		return geodatabaseId;
	}

	public void setGeodatabaseId(long geodatabaseId) {
		this.geodatabaseId = geodatabaseId;
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

	public String getScheme() {
		return scheme;
	}

	public void setScheme(String scheme) {
		this.scheme = scheme;
	}

	public String getTemplate() {
		return template;
	}

	public void setTemplate(String template) {
		this.template = template;
	}

	public long getGeodatabaseTypeId() {
		return geodatabaseTypeId;
	}

	public void setGeodatabaseTypeId(long geodatabaseTypeId) {
		this.geodatabaseTypeId = geodatabaseTypeId;
	}
	
	public String getDbName() throws PublishConfigurationException{
		try{
		   String[] parts = this.getUrl().split("/");
		   return parts[3];
		}catch(Exception e){
			throw new PublishConfigurationException("Null or wrong DB Url format");
		}
	}
	
	public Integer getDbPort() throws PublishConfigurationException{
		try{
		   String[] parts = this.getUrl().split("/");
		   String[] parts2 = parts[2].split(":");
		   return new Integer(parts2[1]);
		}catch(Exception e){
			throw new PublishConfigurationException("Null or wrong DB Url format");
		}
	}
	
	public String getDbHost() throws PublishConfigurationException{
		try{
		   String[] parts = this.getUrl().split("/");
		   String[] parts2 = parts[2].split(":");
		   return parts2[0];
		}catch(Exception e){
			throw new PublishConfigurationException("Null or wrong DB Url format");
		}
	}

	public static Geodatabase getFromIdString(String idString) {

        if (idString == null || idString.equals("")) {
            throw new IllegalArgumentException(
                   "Geodatabase identifier string can't be null or empty.");
        }
                
        return GeodatabaseDaoHelper.getGeodatabaseDao().getGeodatabaseFromIdString(idString);
    }
	
	public GeodatabaseType getGeodatabaseType(){
		 if (0 > this.geodatabaseId ){
	           throw new IllegalArgumentException(
	                   "id cannot be smaller than 1");
	     }
	     return GeodatabaseDaoHelper.getGeodatabaseDao().getType(this.geodatabaseTypeId);
	}
	
    public void persist() {
    	
         GeodatabaseDaoHelper.getGeodatabaseDao().persist(this);
    
    }
    
    public void delete() {
         GeodatabaseDaoHelper.getGeodatabaseDao().delete(this);
    }
    
    public IHelper getHelper() throws InstantiationException, IllegalAccessException, ClassNotFoundException, PublishConfigurationException {
    	return GeodatabaseHelper.getInstance(this);
	}
	
}
