package org.easysdi.publish.biz.layer;

import java.util.Calendar;

import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.dat.dao.DiffuserDaoHelper;
import org.easysdi.publish.dat.dao.FeatureSourceDaoHelper;
import org.easysdi.publish.dat.dao.ILayerDao;
import org.easysdi.publish.dat.dao.LayerDaoHelper;

public class FeatureSource {
	private long featureSourceId;
	private Diffuser diffuser;
	private String guid;
	private String tableName;
	private String scriptName;
	private String sourceDataType;
	private String crsCode;
	private String fieldsName;
	private Calendar creationDate; 
	private Calendar updateDate;
	private String status;
	private String excMessage;
	private String excCode;
	private String excStackTrace;
	
	public void persist() {
        FeatureSourceDaoHelper.getFeatureSourceDao().persist(this);
    }
    
    public void delete() {
        FeatureSourceDaoHelper.getFeatureSourceDao().delete(this);
    }
	
	public static FeatureSource getFromIdString(String idString) {

        if (idString == null || idString.equals("")) {
            throw new IllegalArgumentException(
                   "FeatureSource identifier string can't be null or empty.");
        }
                
        
        return FeatureSourceDaoHelper.getFeatureSourceDao().getFeatureSourceFromIdString(idString);
    }
	
	public static FeatureSource getFromGuid(String guid) {

        if (guid == null || guid.equals("")) {
            throw new IllegalArgumentException(
                   "FeatureSource identifier string can't be null or empty.");
        }
        
        ILayerDao ildao = (ILayerDao)LayerDaoHelper.getLayerDao();
        return LayerDaoHelper.getLayerDao().getFeatureSourceFromGuid(guid);
    }
	
	public long getFeatureSourceId() {
		return featureSourceId;
	}
	public void setFeatureSourceId(long featureSourceId) {
		this.featureSourceId = featureSourceId;
	}
	public Diffuser getDiffuser() {
		return diffuser;
	}
	public void setDiffuser(Diffuser diffuser) {
		this.diffuser = diffuser;
	}
	public String getGuid() {
		return guid;
	}
	public void setGuid(String guid) {
		this.guid = guid;
	}
	public String getTableName() {
		return tableName;
	}
	public void setTableName(String tableName) {
		this.tableName = tableName;
	}
	public String getScriptName() {
		return scriptName;
	}
	public void setScriptName(String scriptName) {
		this.scriptName = scriptName;
	}
	public String getSourceDataType() {
		return sourceDataType;
	}
	public void setSourceDataType(String sourceDataType) {
		this.sourceDataType = sourceDataType;
	}
	public String getCrsCode() {
		return crsCode;
	}
	public void setCrsCode(String crsCode) {
		this.crsCode = crsCode;
	}
	public String getFieldsName() {
		return fieldsName;
	}
	public void setFieldsName(String fieldsName) {
		this.fieldsName = fieldsName;
	}
	public Calendar getCreationDate() {
		return creationDate;
	}
	public void setCreationDate(Calendar creationDate) {
		this.creationDate = creationDate;
	}
	public Calendar getUpdateDate() {
		return updateDate;
	}
	public void setUpdateDate(Calendar updateDate) {
		this.updateDate = updateDate;
	}
	public String getStatus() {
		return status;
	}
	public void setStatus(String status) {
		this.status = status;
	}
	public String getExcMessage() {
		return excMessage;
	}
	public void setExcMessage(String excMessage) {
		this.excMessage = excMessage;
	}
	public String getExcCode() {
		return excCode;
	}
	public void setExcCode(String excDetail) {
		this.excCode = excDetail;
	}
    public String getExcStackTrace() {
		return excStackTrace;
	}
	public void setExcStackTrace(String excStackTrace) {
		this.excStackTrace = excStackTrace;
	}
}