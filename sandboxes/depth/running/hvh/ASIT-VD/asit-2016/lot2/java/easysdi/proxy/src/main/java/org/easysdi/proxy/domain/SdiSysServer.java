package org.easysdi.proxy.domain;

import java.util.HashSet;
import java.util.Set;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.FetchType;
import javax.persistence.GeneratedValue;
import static javax.persistence.GenerationType.IDENTITY;
import javax.persistence.Id;
import javax.persistence.OneToMany;
import org.hibernate.annotations.Cache;
import org.hibernate.annotations.CacheConcurrencyStrategy;
import org.hibernate.annotations.Filter;
import org.hibernate.annotations.FilterDef;

/**
 * SdiSysServer 
 */
@Entity
@Cache(usage=CacheConcurrencyStrategy.READ_ONLY)
@FilterDef(name="entityState")
@Filter(name = "entityState",condition="State = 1")
public class SdiSysServer implements java.io.Serializable {

	private static final long serialVersionUID = 7905159108587400752L;
	private Integer Id;
	private int Ordering;
	private int State;
	private String Value;
	private Set<SdiPhysicalservice> sdiPhysicalservices = new HashSet<SdiPhysicalservice>(
			0);

	public SdiSysServer() {
	}

	public SdiSysServer(int Ordering, int State, String Value) {
		this.Ordering = Ordering;
		this.State = State;
		this.Value = Value;
	}

	public SdiSysServer(int Ordering, int State, String Value,
			Set<SdiPhysicalservice> sdiPhysicalservices) {
		this.Ordering = Ordering;
		this.State = State;
		this.Value = Value;
		this.sdiPhysicalservices = sdiPhysicalservices;
	}

	@Id
	@GeneratedValue(strategy = IDENTITY)
	@Column(name = "id", unique = true, nullable = false)
	public Integer getId() {
		return this.Id;
	}

	public void setId(Integer Id) {
		this.Id = Id;
	}

	@Column(name = "ordering", nullable = false)
	public int getOrdering() {
		return this.Ordering;
	}

	public void setOrdering(int Ordering) {
		this.Ordering = Ordering;
	}

	@Column(name = "state", nullable = false)
	public int getState() {
		return this.State;
	}

	public void setState(int State) {
		this.State = State;
	}

	@Column(name = "value", nullable = false, length = 150)
	public String getValue() {
		return this.Value;
	}

	public void setValue(String Value) {
		this.Value = Value;
	}

	@OneToMany(fetch = FetchType.LAZY, mappedBy = "sdiSysServer")
	public Set<SdiPhysicalservice> getSdiPhysicalservices() {
		return this.sdiPhysicalservices;
	}

	public void setSdiPhysicalservices(
			Set<SdiPhysicalservice> sdiPhysicalservices) {
		this.sdiPhysicalservices = sdiPhysicalservices;
	}

}
