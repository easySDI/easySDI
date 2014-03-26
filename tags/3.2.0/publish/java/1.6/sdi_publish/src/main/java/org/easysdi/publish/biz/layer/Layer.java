package org.easysdi.publish.biz.layer;

import java.util.Calendar;

import org.easysdi.publish.dat.dao.DiffuserDaoHelper;
import org.easysdi.publish.dat.dao.ILayerDao;
import org.easysdi.publish.dat.dao.LayerDaoHelper;

public class Layer {

	private long layerId;
	private FeatureSource featureSource;
	private String guid;
	private String keywordList;
	private String title;
	private String name;
	private String description;
	private String status;
	private String _abstract;
	private String quality_area;
	private String style;
	private Calendar creationDate; 
	private Calendar updateDate;
	
    public void persist() {
        LayerDaoHelper.getLayerDao().persist(this);
    }
    
    public void delete() {
        LayerDaoHelper.getLayerDao().delete(this);
    }	
	
	public static Layer getFromIdString(String idString) {

        if (idString == null || idString.equals("")) {
            throw new IllegalArgumentException(
                   "FeatureSource identifier string can't be null or empty.");
        }
        
        ILayerDao ildao = (ILayerDao)LayerDaoHelper.getLayerDao();
        return LayerDaoHelper.getLayerDao().getLayerFromIdString(idString);
	}
	
	public static Layer getFromGuid(String guid) {

        if (guid == null || guid.equals("")) {
            throw new IllegalArgumentException(
                   "FeatureSource identifier string can't be null or empty.");
        }
        
        ILayerDao ildao = (ILayerDao)LayerDaoHelper.getLayerDao();
        return LayerDaoHelper.getLayerDao().getLayerFromGuid(guid);
    }	
	
	public static boolean isLayerBoundToFeatureSource(FeatureSource fs) {

        if (fs.getGuid() == null || fs.getGuid().equals("")) {
            throw new IllegalArgumentException(
                   "FeatureSource identifier string can't be null or empty.");
        }
        
        ILayerDao ildao = (ILayerDao)LayerDaoHelper.getLayerDao();
        return LayerDaoHelper.getLayerDao().isLayerBoundToFeatureSource(fs);
    }
	
	public long getLayerId() {
		return layerId;
	}
	public void setLayerId(long layerId) {
		this.layerId = layerId;
	}
	public FeatureSource getFeatureSource() {
		return featureSource;
	}
	public void setFeatureSource(FeatureSource featureSource) {
		this.featureSource = featureSource;
	}
	public String getGuid() {
		return guid;
	}
	public void setGuid(String guid) {
		this.guid = guid;
	}
	public String getKeywordList() {
		return keywordList;
	}
	public void setKeywordList(String keywordList) {
		this.keywordList = keywordList;
	}
	public String getTitle() {
		return title;
	}
	public void setTitle(String title) {
		this.title = title;
	}
	public String getName() {
		return name;
	}
	public void setName(String name) {
		this.name = name;
	}
	public String getDescription() {
		return description;
	}
	public void setDescription(String description) {
		this.description = description;
	}
	public String getStatus() {
		return status;
	}
	public void setStatus(String status) {
		this.status = status;
	}
	public String get_abstract() {
		return _abstract;
	}

	public void set_abstract(String abstract1) {
		_abstract = abstract1;
	}

	public String getQuality_area() {
		return quality_area;
	}

	public void setQuality_area(String qualityArea) {
		quality_area = qualityArea;
	}

	public String getStyle() {
		return style;
	}

	public void setStyle(String style) {
		this.style = style;
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
	
	
	
}
