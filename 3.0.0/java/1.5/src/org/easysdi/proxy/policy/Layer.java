package org.easysdi.proxy.policy;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlAttribute;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlType;


@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "", propOrder = { "name", "scaleMin", "scaleMax", "filter","LatLonBoundingBox","BoundingBox","TileMatrixSet"})
@XmlRootElement(name = "Layer")
public class Layer implements Serializable {

    private static final long serialVersionUID = 970248158625276568L;
	@XmlAttribute(name = "All")
	private Boolean all = false;
	@XmlElement(name = "Name", required = true)
	protected String name;
	@XmlElement(name = "ScaleMin")
	protected Double scaleMin;
	@XmlElement(name = "ScaleMax")
	protected Double scaleMax;
	@XmlElement(name = "Filter")
	protected Filter filter;
	@XmlElement(name = "LatLonBoundingBox")
	protected String LatLonBoundingBox;
	@XmlElement(name = "BoundingBox")
	protected BoundingBox BoundingBox;
	@XmlElement(name = "TileMatrixSet")
	private List<TileMatrixSet> TileMatrixSet;

	@Override
	public int hashCode() {
		int hashCode = 0;
		if (name != null)
			hashCode += name.hashCode();
		if (scaleMin != null)
			hashCode += scaleMin.hashCode();
		if (scaleMax != null)
			hashCode += scaleMax.hashCode();
		if (filter != null)
			hashCode += filter.hashCode();
		if (LatLonBoundingBox != null)
			hashCode += LatLonBoundingBox.hashCode();
		if (BoundingBox != null)
			hashCode += BoundingBox.hashCode();
		if (TileMatrixSet != null)
			hashCode += TileMatrixSet.hashCode();
		return hashCode;
	}

	/**
	 * @param all the all to set
	 */
	public void setAll(Boolean all) {
	    this.all = all;
	}

	/**
	 * @return the all
	 */
	public Boolean isAll() {
	    return all;
	}

	/**
	 * Gets the value of the name property.
	 * 
	 * @return possible object is {@link String }
	 * 
	 */
	public String getName() {
		return name;
	}

	/**
	 * Sets the value of the name property.
	 * 
	 * @param value
	 *            allowed object is {@link String }
	 * 
	 */
	public void setName(String value) {
		this.name = value;
	}

	/**
	 * Gets the value of the scaleMin property.
	 * 
	 * @return possible object is {@link Double }
	 * 
	 */
	public Double getScaleMin() {
		return scaleMin;
	}

	/**
	 * Sets the value of the scaleMin property.
	 * 
	 * @param value
	 *            allowed object is {@link Double }
	 * 
	 */
	public void setScaleMin(Double value) {
		this.scaleMin = value;
	}

	/**
	 * Gets the value of the scaleMax property.
	 * 
	 * @return possible object is {@link Double }
	 * 
	 */
	public Double getScaleMax() {
		return scaleMax;
	}

	/**
	 * Sets the value of the scaleMax property.
	 * 
	 * @param value
	 *            allowed object is {@link Double }
	 * 
	 */
	public void setScaleMax(Double value) {
		this.scaleMax = value;
	}

	/**
	 * Gets the value of the filter property.
	 * 
	 * @return possible object is {@link Filter }
	 * 
	 */
	public Filter getFilter() {
		return filter;
	}

	/**
	 * Sets the value of the filter property.
	 * 
	 * @param value
	 *            allowed object is {@link Filter }
	 * 
	 */
	public void setFilter(Filter value) {
		this.filter = value;
	}
	
	/**
	 * Gets the value of the LatLonBoundingBox property.
	 * 
	 * @return possible object is {@link String }
	 * 
	 */
	public String getLatLonBoundingBox() {
		return LatLonBoundingBox;
	}

	/**
	 * Sets the value of the LatLonBoundingBox property.
	 * 
	 * @param value
	 *            allowed object is {@link String }
	 * 
	 */
	public void setLatLonBoundingBox(String value) {
		this.LatLonBoundingBox = value;
	}
	
	/**
	 * Gets the value of the name property.
	 * 
	 * @return possible object is {@link BoundingBox }
	 * 
	 */
	public BoundingBox getBoundingBox() {
		return this.BoundingBox;
	}

	/**
	 * Sets the value of the name property.
	 * 
	 * @param value
	 *            allowed object is {@link BoundingBox }
	 * 
	 */
	public void setBoundingBox(BoundingBox value) {
		this.BoundingBox = value;
	}

	/**
	 * @param tileMatrixSet the tileMatrixSet to set
	 */
	public void setTileMatrixSet(List<TileMatrixSet> tileMatrixSet) {
	    TileMatrixSet = tileMatrixSet;
	}

	/**
	 * @return the tileMatrixSet
	 */
	public List<TileMatrixSet> getTileMatrixSet() {
	    if (this.TileMatrixSet == null) {
		this.TileMatrixSet = new ArrayList<TileMatrixSet>();
	    }
	    return this.TileMatrixSet;
	}

}
