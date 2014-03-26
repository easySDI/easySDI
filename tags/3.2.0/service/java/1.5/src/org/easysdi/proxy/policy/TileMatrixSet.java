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
@XmlType(name = "", propOrder = { "minScaleDenominator", "TileMatrix" })
@XmlRootElement(name = "TileMatrixSet")
public class TileMatrixSet implements Serializable {

    private static final long serialVersionUID = 8004496458611714049L;
	@XmlAttribute(name = "id")
	private String id;
	@XmlAttribute(name = "All")
	private Boolean all;
	@XmlElement(name = "minScaleDenominator")
	private String minScaleDenominator;
	@XmlElement(name = "TileMatrix")
	private List<TileMatrix> TileMatrix;
	
	@Override
	public int hashCode() {
		int hashCode = 0;
		if (getId() != null)
			hashCode += getId().hashCode();
		if (isAll() != null)
			hashCode += isAll().hashCode();
		if (getMinScaleDenominator() != null)
			hashCode += getMinScaleDenominator().hashCode();
		if (TileMatrix != null)
			hashCode += TileMatrix.hashCode();
		
		return hashCode;
	}

	/**
	 * @param id the id to set
	 */
	public void setId(String id) {
	    this.id = id;
	}

	/**
	 * @return the id
	 */
	public String getId() {
	    return id;
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
	 * @param minScaleDenominator the minScaleDenominator to set
	 */
	public void setMinScaleDenominator(String minScaleDenominator) {
	    this.minScaleDenominator = minScaleDenominator;
	}

	/**
	 * @return the minScaleDenominator
	 */
	public String getMinScaleDenominator() {
	    return minScaleDenominator;
	}

	/**
	 * @param tileMatrix the tileMatrix to set
	 */
	public void setTileMatrix(List<TileMatrix> tileMatrix) {
	    TileMatrix = tileMatrix;
	}

	/**
	 * @return the tileMatrix
	 */
	public List<TileMatrix> getTileMatrix() {
	    if (this.TileMatrix == null) {
		this.TileMatrix = new ArrayList<TileMatrix>();
	    }
	    return TileMatrix;
	}

	
}
