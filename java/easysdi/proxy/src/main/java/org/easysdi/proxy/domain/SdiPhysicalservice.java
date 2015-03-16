package org.easysdi.proxy.domain;

// Generated Apr 9, 2013 11:54:41 AM by Hibernate Tools 3.4.0.CR1

import java.util.Date;
import java.util.HashSet;
import java.util.Set;
import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.FetchType;
import javax.persistence.GeneratedValue;
import static javax.persistence.GenerationType.IDENTITY;
import javax.persistence.Id;
import javax.persistence.JoinColumn;
import javax.persistence.JoinTable;
import javax.persistence.ManyToOne;
import javax.persistence.OneToMany;
import javax.persistence.Temporal;
import javax.persistence.TemporalType;
import org.hibernate.annotations.Cache;
import org.hibernate.annotations.CacheConcurrencyStrategy;
import org.hibernate.annotations.Filter;
import org.hibernate.annotations.FilterDef;

/**
 * SdiPhysicalservice generated by hbm2java
 */
@Entity
@Cache(usage=CacheConcurrencyStrategy.READ_ONLY)
@FilterDef(name="entityState")
@Filter(name = "entityState",condition="State = 1")
public class SdiPhysicalservice implements java.io.Serializable {

	private static final long serialVersionUID = -2618595347898155644L;
	private Integer Id;
	private SdiSysServicescope sdiSysServicescope;
	private SdiSysAuthenticationconnector sdiSysAuthenticationconnectorByResourceauthenticationId;
	private SdiSysAuthenticationconnector sdiSysAuthenticationconnectorByServiceauthenticationId;
	private SdiSysServiceconnector sdiSysServiceconnector;
        private SdiSysServer sdiSysServer;
	private String Guid;
	private String Alias;
	private int Created_by;
	private Date Created;
	private Integer Modified_by;
	private Date Modified;
	private Integer Ordering;
	private int State;
	private int Checked_out;
	private Date Checked_out_time;
	private String Name;
	private String Resourceurl;
	private String Resourceusername;
	private String Resourcepassword;
	private String Serviceurl;
	private String Serviceusername;
	private String Servicepassword;
	private int Catid;
	private String Params;
	private int Access;
	private Integer Asset_id;
        private Set<SdiVirtualservice> sdiVirtualservices = new HashSet<SdiVirtualservice>(
			0);
	private Set<SdiPhysicalservicePolicy> sdiPhysicalservicePolicies = new HashSet<SdiPhysicalservicePolicy>(
			0);
	private Set<SdiPhysicalserviceServicecompliance> sdiPhysicalserviceServicecompliances = new HashSet<SdiPhysicalserviceServicecompliance>(
			0);
	private Set<SdiOrganism> sdiOrganisms = new HashSet<SdiOrganism>(
			0);

	public SdiPhysicalservice() {
	}

	public SdiPhysicalservice(SdiSysServicescope sdiSysServicescope,
			SdiSysServiceconnector sdiSysServiceconnector, String Guid,
			String Alias, int Created_by, Date Created, int State,
			int Checked_out, Date Checked_out_time, int Catid, int Access) {
		this.sdiSysServicescope = sdiSysServicescope;
		this.sdiSysServiceconnector = sdiSysServiceconnector;
		this.Guid = Guid;
		this.Alias = Alias;
		this.Created_by = Created_by;
		this.Created = Created;
		this.State = State;
		this.Checked_out = Checked_out;
		this.Checked_out_time = Checked_out_time;
		this.Catid = Catid;
		this.Access = Access;
	}

	public SdiPhysicalservice(
			SdiSysServicescope sdiSysServicescope,
			SdiSysAuthenticationconnector sdiSysAuthenticationconnectorByResourceauthenticationId,
			SdiSysAuthenticationconnector sdiSysAuthenticationconnectorByServiceauthenticationId,
			SdiSysServiceconnector sdiSysServiceconnector,
                        SdiSysServer sdiSysServer,
			String Guid,
			String Alias,
			int Created_by,
			Date Created,
			Integer Modified_by,
			Date Modified,
			Integer Ordering,
			int State,
			int Checked_out,
			Date Checked_out_time,
			String Name,
			String Resourceurl,
			String Resourceusername,
			String Resourcepassword,
			String Serviceurl,
			String Serviceusername,
			String Servicepassword,
			int Catid,
			String Params,
			int Access,
			Integer Asset_id,
			Set<SdiVirtualservice> sdiVirtualservices,
			Set<SdiPhysicalservicePolicy> sdiPhysicalservicePolicies,
			Set<SdiPhysicalserviceServicecompliance> sdiPhysicalserviceServicecompliances,
			Set<SdiOrganism> sdiOrganisms) {
		this.sdiSysServicescope = sdiSysServicescope;
		this.sdiSysAuthenticationconnectorByResourceauthenticationId = sdiSysAuthenticationconnectorByResourceauthenticationId;
		this.sdiSysAuthenticationconnectorByServiceauthenticationId = sdiSysAuthenticationconnectorByServiceauthenticationId;
		this.sdiSysServiceconnector = sdiSysServiceconnector;
                this.sdiSysServer = sdiSysServer;
		this.Guid = Guid;
		this.Alias = Alias;
		this.Created_by = Created_by;
		this.Created = Created;
		this.Modified_by = Modified_by;
		this.Modified = Modified;
		this.Ordering = Ordering;
		this.State = State;
		this.Checked_out = Checked_out;
		this.Checked_out_time = Checked_out_time;
		this.Name = Name;
		this.Resourceurl = Resourceurl;
		this.Resourceusername = Resourceusername;
		this.Resourcepassword = Resourcepassword;
		this.Serviceurl = Serviceurl;
		this.Serviceusername = Serviceusername;
		this.Servicepassword = Servicepassword;
		this.Catid = Catid;
		this.Params = Params;
		this.Access = Access;
		this.Asset_id = Asset_id;
		this.sdiVirtualservices = sdiVirtualservices;
		this.sdiPhysicalservicePolicies = sdiPhysicalservicePolicies;
		this.sdiPhysicalserviceServicecompliances = sdiPhysicalserviceServicecompliances;
		this.sdiOrganisms = sdiOrganisms;
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

	@ManyToOne(fetch = FetchType.LAZY)
	@JoinColumn(name = "servicescope_id", nullable = false)
	@Filter(name = "entityState",condition="State = 1")
	public SdiSysServicescope getSdiSysServicescope() {
		return this.sdiSysServicescope;
	}

	public void setSdiSysServicescope(SdiSysServicescope sdiSysServicescope) {
		this.sdiSysServicescope = sdiSysServicescope;
	}

	@ManyToOne(fetch = FetchType.LAZY)
	@JoinColumn(name = "resourceauthentication_id")
	public SdiSysAuthenticationconnector getSdiSysAuthenticationconnectorByResourceauthenticationId() {
		return this.sdiSysAuthenticationconnectorByResourceauthenticationId;
	}

	public void setSdiSysAuthenticationconnectorByResourceauthenticationId(
			SdiSysAuthenticationconnector sdiSysAuthenticationconnectorByResourceauthenticationId) {
		this.sdiSysAuthenticationconnectorByResourceauthenticationId = sdiSysAuthenticationconnectorByResourceauthenticationId;
	}

	@ManyToOne(fetch = FetchType.LAZY)
	@JoinColumn(name = "serviceauthentication_id")
	public SdiSysAuthenticationconnector getSdiSysAuthenticationconnectorByServiceauthenticationId() {
		return this.sdiSysAuthenticationconnectorByServiceauthenticationId;
	}

	public void setSdiSysAuthenticationconnectorByServiceauthenticationId(
			SdiSysAuthenticationconnector sdiSysAuthenticationconnectorByServiceauthenticationId) {
		this.sdiSysAuthenticationconnectorByServiceauthenticationId = sdiSysAuthenticationconnectorByServiceauthenticationId;
	}

	@ManyToOne(fetch = FetchType.LAZY)
	@JoinColumn(name = "serviceconnector_id", nullable = false)
	public SdiSysServiceconnector getSdiSysServiceconnector() {
		return this.sdiSysServiceconnector;
	}

	public void setSdiSysServiceconnector(
			SdiSysServiceconnector sdiSysServiceconnector) {
		this.sdiSysServiceconnector = sdiSysServiceconnector;
	}
        
        @ManyToOne(fetch = FetchType.LAZY)
	@JoinColumn(name = "server_id", nullable = false)
	public SdiSysServer getSdiSysServer() {
		return this.sdiSysServer;
	}

	public void setSdiSysServer(
			SdiSysServer sdiSysServer) {
		this.sdiSysServer = sdiSysServer;
	}

	@Column(name = "guid", nullable = false, length = 36)
	public String getGuid() {
		return this.Guid;
	}

	public void setGuid(String Guid) {
		this.Guid = Guid;
	}

	@Column(name = "alias", nullable = false, length = 20)
	public String getAlias() {
		return this.Alias;
	}

	public void setAlias(String Alias) {
		this.Alias = Alias;
	}

	@Column(name = "created_by", nullable = false)
	public int getCreated_by() {
		return this.Created_by;
	}

	public void setCreated_by(int Created_by) {
		this.Created_by = Created_by;
	}

	@Temporal(TemporalType.TIMESTAMP)
	@Column(name = "created", nullable = false, length = 19)
	public Date getCreated() {
		return this.Created;
	}

	public void setCreated(Date Created) {
		this.Created = Created;
	}

	@Column(name = "modified_by")
	public Integer getModified_by() {
		return this.Modified_by;
	}

	public void setModified_by(Integer Modified_by) {
		this.Modified_by = Modified_by;
	}

	@Temporal(TemporalType.TIMESTAMP)
	@Column(name = "modified", length = 19)
	public Date getModified() {
		return this.Modified;
	}

	public void setModified(Date Modified) {
		this.Modified = Modified;
	}

	@Column(name = "ordering")
	public Integer getOrdering() {
		return this.Ordering;
	}

	public void setOrdering(Integer Ordering) {
		this.Ordering = Ordering;
	}

	@Column(name = "state", nullable = false)
	public int getState() {
		return this.State;
	}

	public void setState(int State) {
		this.State = State;
	}

	@Column(name = "checked_out", nullable = false)
	public int getChecked_out() {
		return this.Checked_out;
	}

	public void setChecked_out(int Checked_out) {
		this.Checked_out = Checked_out;
	}

	@Temporal(TemporalType.TIMESTAMP)
	@Column(name = "checked_out_time", nullable = false, length = 19)
	public Date getChecked_out_time() {
		return this.Checked_out_time;
	}

	public void setChecked_out_time(Date Checked_out_time) {
		this.Checked_out_time = Checked_out_time;
	}

	@Column(name = "name", unique = true)
	public String getName() {
		return this.Name;
	}

	public void setName(String Name) {
		this.Name = Name;
	}

	@Column(name = "resourceurl", length = 500)
	public String getResourceurl() {
		return this.Resourceurl;
	}

	public void setResourceurl(String Resourceurl) {
		this.Resourceurl = Resourceurl;
	}

	@Column(name = "resourceusername", length = 150)
	public String getResourceusername() {
		return this.Resourceusername;
	}

	public void setResourceusername(String Resourceusername) {
		this.Resourceusername = Resourceusername;
	}

	@Column(name = "resourcepassword", length = 150)
	public String getResourcepassword() {
		return this.Resourcepassword;
	}

	public void setResourcepassword(String Resourcepassword) {
		this.Resourcepassword = Resourcepassword;
	}

	@Column(name = "serviceurl", length = 500)
	public String getServiceurl() {
		return this.Serviceurl;
	}

	public void setServiceurl(String Serviceurl) {
		this.Serviceurl = Serviceurl;
	}

	@Column(name = "serviceusername", length = 150)
	public String getServiceusername() {
		return this.Serviceusername;
	}

	public void setServiceusername(String Serviceusername) {
		this.Serviceusername = Serviceusername;
	}

	@Column(name = "servicepassword", length = 150)
	public String getServicepassword() {
		return this.Servicepassword;
	}

	public void setServicepassword(String Servicepassword) {
		this.Servicepassword = Servicepassword;
	}

	@Column(name = "catid", nullable = false)
	public int getCatid() {
		return this.Catid;
	}

	public void setCatid(int Catid) {
		this.Catid = Catid;
	}

	@Column(name = "params", length = 1024)
	public String getParams() {
		return this.Params;
	}

	public void setParams(String Params) {
		this.Params = Params;
	}

	@Column(name = "access", nullable = false)
	public int getAccess() {
		return this.Access;
	}

	public void setAccess(int Access) {
		this.Access = Access;
	}

	@Column(name = "asset_id")
	public Integer getAsset_id() {
		return this.Asset_id;
	}

	public void setAsset_id(Integer Asset_id) {
		this.Asset_id = Asset_id;
	}

	@OneToMany(fetch = FetchType.LAZY)
	@JoinTable(name = "SdiVirtualPhysical", joinColumns = {@JoinColumn(name = "physicalservice_id")}, inverseJoinColumns = {@JoinColumn (name = "virtualservice_id")})
	public Set<SdiVirtualservice> getSdiVirtualservices() {
		return this.sdiVirtualservices;
	}

	public void setSdiVirtualservices(
			Set<SdiVirtualservice> sdiVirtualservices) {
		this.sdiVirtualservices = sdiVirtualservices;
	}

	@OneToMany(fetch = FetchType.LAZY, mappedBy = "sdiPhysicalservice")
	public Set<SdiPhysicalservicePolicy> getSdiPhysicalservicePolicies() {
		return this.sdiPhysicalservicePolicies;
	}

	public void setSdiPhysicalservicePolicies(
			Set<SdiPhysicalservicePolicy> sdiPhysicalservicePolicies) {
		this.sdiPhysicalservicePolicies = sdiPhysicalservicePolicies;
	}

	@OneToMany(fetch = FetchType.LAZY, mappedBy = "sdiPhysicalservice")
	public Set<SdiPhysicalserviceServicecompliance> getSdiPhysicalserviceServicecompliances() {
		return this.sdiPhysicalserviceServicecompliances;
	}

	public void setSdiPhysicalserviceServicecompliances(
			Set<SdiPhysicalserviceServicecompliance> sdiPhysicalserviceServicecompliances) {
		this.sdiPhysicalserviceServicecompliances = sdiPhysicalserviceServicecompliances;
	}

	@OneToMany(fetch = FetchType.LAZY)
	@JoinTable(name = "SdiPhysicalserviceOrganism", joinColumns = {@JoinColumn(name = "physicalservice_id")}, inverseJoinColumns = {@JoinColumn (name = "organism_id")})
	@Filter(name = "entityState",condition="State = 1")
	public Set<SdiOrganism> getSdiOrganisms() {
		return this.sdiOrganisms;
	}

	public void setSdiOrganisms(
			Set<SdiOrganism> sdiOrganisms) {
		this.sdiOrganisms = sdiOrganisms;
	}

}
