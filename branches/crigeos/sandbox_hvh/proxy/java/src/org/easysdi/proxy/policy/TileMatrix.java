package org.easysdi.proxy.policy;

import java.io.Serializable;

import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlAttribute;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlType;

@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "", propOrder = { "TileMinCol","TileMaxCol","TileMinRow","TileMaxRow" })
@XmlRootElement(name = "TileMatrix")
public class TileMatrix implements Serializable {

    private static final long serialVersionUID = -8172685268234953663L;
    @XmlAttribute(name = "id")
	private String id;
	@XmlAttribute(name = "All")
	private Boolean all;
	@XmlElement(name = "TileMinCol")
	private String TileMinCol;
	@XmlElement(name = "TileMaxCol")
	private String TileMaxCol;
	@XmlElement(name = "TileMinRow")
	private String TileMinRow;
	@XmlElement(name = "TileMaxRow")
	private String TileMaxRow;
	
	@Override
	public int hashCode() {
		int hashCode = 0;
		if (getId() != null)
			hashCode += getId().hashCode();
		if (isAll() != null)
			hashCode += isAll().hashCode();
		if (TileMinCol != null)
			hashCode += TileMinCol.hashCode();
		if (TileMaxCol != null)
			hashCode += TileMaxCol.hashCode();
		if (TileMinRow != null)
			hashCode += TileMinRow.hashCode();
		if (TileMaxRow != null)
			hashCode += TileMaxRow.hashCode();
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
	 * @param tileMinCol the tileMinCol to set
	 */
	public void setTileMinCol(String tileMinCol) {
	    TileMinCol = tileMinCol;
	}

	/**
	 * @return the tileMinCol
	 */
	public String getTileMinCol() {
	    return TileMinCol;
	}

	/**
	 * @param tileMaxCol the tileMaxCol to set
	 */
	public void setTileMaxCol(String tileMaxCol) {
	    TileMaxCol = tileMaxCol;
	}

	/**
	 * @return the tileMaxCol
	 */
	public String getTileMaxCol() {
	    return TileMaxCol;
	}

	/**
	 * @param tileMinRow the tileMinRow to set
	 */
	public void setTileMinRow(String tileMinRow) {
	    TileMinRow = tileMinRow;
	}

	/**
	 * @return the tileMinRow
	 */
	public String getTileMinRow() {
	    return TileMinRow;
	}

	/**
	 * @param tileMaxRow the tileMaxRow to set
	 */
	public void setTileMaxRow(String tileMaxRow) {
	    TileMaxRow = tileMaxRow;
	}

	/**
	 * @return the tileMaxRow
	 */
	public String getTileMaxRow() {
	    return TileMaxRow;
	}

	
	
}
