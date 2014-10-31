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
import org.hibernate.annotations.Fetch;
import org.hibernate.annotations.FetchMode;
import org.hibernate.annotations.Filter;
import org.hibernate.annotations.FilterDef;

/**
 * SdiPolicy generated by hbm2java
 */
@Entity
@Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
@FilterDef(name = "entityState")
@Filter(name = "entityState", condition = "State = 1")
public class SdiPolicy implements java.io.Serializable {

    private static final long serialVersionUID = 5779148549398166243L;
    private Integer Id;
    private SdiWmsSpatialpolicy sdiWmsSpatialpolicy;
    private SdiSysAccessscope sdiSysAccessscope;
    private SdiVirtualservice sdiVirtualservice;
    private SdiWmtsSpatialpolicy sdiWmtsSpatialpolicy;
    private SdiCswSpatialpolicy sdiCswSpatialpolicy;
    private SdiWfsSpatialpolicy sdiWfsSpatialpolicy;
    private SdiSysMetadataversion sdiSysMetadataversion;
    private SdiSysAccessscope cswSdiSysAccessscope;
    private String Guid;
    private int Ordering;
    private int State;
    private int Checked_out;
    private Date Checked_out_time;
    private int Created_by;
    private Date Created;
    private Integer Modified_by;
    private Date Modified;
    private String Name;
    private String Alias;
    private Date Allowfrom;
    private Date Allowto;
    private boolean Anyoperation;
    private boolean Anyservice;
    private boolean Csw_anyattribute;
    private boolean Csw_anycontext;
    private boolean Csw_anystate;
    private boolean Csw_anyvisibility;
    private boolean Csw_includeharvested;
    private boolean Csw_anyresourcetype;    
    private String Wms_minimumwidth;
    private String Wms_minimumheight;
    private String Wms_maximumwidth;
    private String Wms_maximumheight;
    private String Params;
    private int Access;
    private Integer Asset_id;
    private Set<SdiPhysicalservicePolicy> sdiPhysicalservicePolicies = new HashSet<SdiPhysicalservicePolicy>(
            0);
    private Set<SdiOrganism> sdiOrganisms = new HashSet<SdiOrganism>(
            0);
    private Set<SdiCategory> sdiCategories = new HashSet<SdiCategory>(
            0);
    private Set<SdiAllowedoperation> sdiAllowedoperations = new HashSet<SdiAllowedoperation>(
            0);
    private Set<SdiUser> sdiUsers = new HashSet<SdiUser>(0);
    private Set<SdiExcludedattribute> sdiExcludedattributes = new HashSet<SdiExcludedattribute>(
            0);
    private Set<SdiPolicyMetadatastate> sdiPolicyMetadatastates = new HashSet<SdiPolicyMetadatastate>(
            0);
    private Set<SdiPolicyVisibility> sdiPolicyVisibilities = new HashSet<SdiPolicyVisibility>(
            0);
    private Set<SdiResourcetype> sdiResourcetypes = new HashSet<SdiResourcetype>(
            0);

    public SdiPolicy() {
    }

    public SdiPolicy(SdiSysAccessscope sdiSysAccessscope,
            SdiVirtualservice sdiVirtualservice, String Guid, int Ordering,
            int State, int Checked_out, Date Checked_out_time, int Created_by,
            Date Created, String Name, String Alias, Date Allowfrom,
            Date Allowto, boolean Anyoperation, boolean Anyservice,
            SdiSysMetadataversion sdiSysMetadataversion, boolean Csw_anyattribute,
            boolean Csw_anycontext, boolean Csw_anystate,
            boolean Csw_anyvisibility, int Access) {
        this.sdiSysAccessscope = sdiSysAccessscope;
        this.sdiVirtualservice = sdiVirtualservice;
        this.Guid = Guid;
        this.Ordering = Ordering;
        this.State = State;
        this.Checked_out = Checked_out;
        this.Checked_out_time = Checked_out_time;
        this.Created_by = Created_by;
        this.Created = Created;
        this.Name = Name;
        this.Alias = Alias;
        this.Allowfrom = Allowfrom;
        this.Allowto = Allowto;
        this.Anyoperation = Anyoperation;
        this.Anyservice = Anyservice;
        this.sdiSysMetadataversion = sdiSysMetadataversion;
        this.Csw_anyattribute = Csw_anyattribute;
        this.Csw_anycontext = Csw_anycontext;
        this.Csw_anystate = Csw_anystate;
        this.Csw_anyvisibility = Csw_anyvisibility;
        this.Access = Access;
    }

    public SdiPolicy(SdiWmsSpatialpolicy sdiWmsSpatialpolicy,
            SdiSysAccessscope sdiSysAccessscope,
            SdiVirtualservice sdiVirtualservice,
            SdiWmtsSpatialpolicy sdiWmtsSpatialpolicy,
            SdiCswSpatialpolicy sdiCswSpatialpolicy,
            SdiWfsSpatialpolicy sdiWfsSpatialpolicy, String Guid, int Ordering,
            int State, int Checked_out, Date Checked_out_time, int Created_by,
            Date Created, Integer Modified_by, Date Modified, String Name,
            String Alias, Date Allowfrom, Date Allowto, boolean Anyoperation,
            boolean Anyservice, SdiSysMetadataversion sdiSysMetadataversion, boolean Csw_anyattribute,
            boolean Csw_anycontext, boolean Csw_anystate, boolean Csw_anyvisibility,
            boolean Csw_includeharvested, boolean Csw_anyresourcetype, SdiSysAccessscope cswSdiSysAccessscope,
            String Wms_minimumwidth,
            String Wms_minimumheight, String Wms_maximumwidth,
            String Wms_maximumheight, String Params, int Access,
            Integer Asset_id,
            Set<SdiPhysicalservicePolicy> sdiPhysicalservicePolicies,
            Set<SdiOrganism> sdiOrganisms,
            Set<SdiCategory> sdiCategories,
            Set<SdiAllowedoperation> sdiAllowedoperations,
            Set<SdiUser> sdiUsers,
            Set<SdiExcludedattribute> sdiExcludedattributes,
            Set<SdiPolicyMetadatastate> sdiPolicyMetadatastates,
            Set<SdiPolicyVisibility> sdiPolicyVisibilities,
            Set<SdiResourcetype> sdiResourcetypes) {
        this.sdiWmsSpatialpolicy = sdiWmsSpatialpolicy;
        this.sdiSysAccessscope = sdiSysAccessscope;
        this.sdiVirtualservice = sdiVirtualservice;
        this.sdiWmtsSpatialpolicy = sdiWmtsSpatialpolicy;
        this.sdiCswSpatialpolicy = sdiCswSpatialpolicy;
        this.sdiWfsSpatialpolicy = sdiWfsSpatialpolicy;
        this.Guid = Guid;
        this.Ordering = Ordering;
        this.State = State;
        this.Checked_out = Checked_out;
        this.Checked_out_time = Checked_out_time;
        this.Created_by = Created_by;
        this.Created = Created;
        this.Modified_by = Modified_by;
        this.Modified = Modified;
        this.Name = Name;
        this.Alias = Alias;
        this.Allowfrom = Allowfrom;
        this.Allowto = Allowto;
        this.Anyoperation = Anyoperation;
        this.Anyservice = Anyservice;
        this.sdiSysMetadataversion = sdiSysMetadataversion;
        this.Csw_anyattribute = Csw_anyattribute;
        this.Csw_anycontext = Csw_anycontext;
        this.Csw_anystate = Csw_anystate;
        this.Csw_anyvisibility = Csw_anyvisibility;
        this.Csw_includeharvested = Csw_includeharvested;
        this.Csw_anyresourcetype = Csw_anyresourcetype;
        this.cswSdiSysAccessscope = cswSdiSysAccessscope;
        this.Wms_minimumwidth = Wms_minimumwidth;
        this.Wms_minimumheight = Wms_minimumheight;
        this.Wms_maximumwidth = Wms_maximumwidth;
        this.Wms_maximumheight = Wms_maximumheight;
        this.Params = Params;
        this.Access = Access;
        this.Asset_id = Asset_id;
        this.sdiPhysicalservicePolicies = sdiPhysicalservicePolicies;
        this.sdiOrganisms = sdiOrganisms;
        this.sdiCategories = sdiCategories;
        this.sdiAllowedoperations = sdiAllowedoperations;
        this.sdiUsers = sdiUsers;
        this.sdiPolicyMetadatastates = sdiPolicyMetadatastates;
        this.sdiExcludedattributes = sdiExcludedattributes;
        this.sdiPolicyVisibilities = sdiPolicyVisibilities;
        this.sdiResourcetypes = sdiResourcetypes;
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
    @JoinColumn(name = "wms_spatialpolicy_id")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    public SdiWmsSpatialpolicy getSdiWmsSpatialpolicy() {
        return this.sdiWmsSpatialpolicy;
    }

    public void setSdiWmsSpatialpolicy(SdiWmsSpatialpolicy sdiWmsSpatialpolicy) {
        this.sdiWmsSpatialpolicy = sdiWmsSpatialpolicy;
    }

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "accessscope_id", nullable = false)
    @Filter(name = "entityState", condition = "State = 1")
    public SdiSysAccessscope getSdiSysAccessscope() {
        return this.sdiSysAccessscope;
    }

    public void setSdiSysAccessscope(SdiSysAccessscope sdiSysAccessscope) {
        this.sdiSysAccessscope = sdiSysAccessscope;
    }

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "virtualservice_id", nullable = false)
    public SdiVirtualservice getSdiVirtualservice() {
        return this.sdiVirtualservice;
    }

    public void setSdiVirtualservice(SdiVirtualservice sdiVirtualservice) {
        this.sdiVirtualservice = sdiVirtualservice;
    }

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "wmts_spatialpolicy_id")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    public SdiWmtsSpatialpolicy getSdiWmtsSpatialpolicy() {
        return this.sdiWmtsSpatialpolicy;
    }

    public void setSdiWmtsSpatialpolicy(
            SdiWmtsSpatialpolicy sdiWmtsSpatialpolicy) {
        this.sdiWmtsSpatialpolicy = sdiWmtsSpatialpolicy;
    }

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "csw_spatialpolicy_id")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    public SdiCswSpatialpolicy getSdiCswSpatialpolicy() {
        return this.sdiCswSpatialpolicy;
    }

    public void setSdiCswSpatialpolicy(SdiCswSpatialpolicy sdiCswSpatialpolicy) {
        this.sdiCswSpatialpolicy = sdiCswSpatialpolicy;
    }

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "wfs_spatialpolicy_id")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    public SdiWfsSpatialpolicy getSdiWfsSpatialpolicy() {
        return this.sdiWfsSpatialpolicy;
    }

    public void setSdiWfsSpatialpolicy(SdiWfsSpatialpolicy sdiWfsSpatialpolicy) {
        this.sdiWfsSpatialpolicy = sdiWfsSpatialpolicy;
    }

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "csw_version_id", nullable = false)
    @Filter(name = "entityState", condition = "State = 1")
    public SdiSysMetadataversion getSdiSysMetadataversion() {
        return this.sdiSysMetadataversion;
    }

    public void setSdiSysMetadataversion(
            SdiSysMetadataversion sdiSysMetadataversion) {
        this.sdiSysMetadataversion = sdiSysMetadataversion;
    }

    @Column(name = "guid", nullable = false, length = 36)
    public String getGuid() {
        return this.Guid;
    }

    public void setGuid(String Guid) {
        this.Guid = Guid;
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

    @Column(name = "name", nullable = false)
    public String getName() {
        return this.Name;
    }

    public void setName(String Name) {
        this.Name = Name;
    }

    @Column(name = "alias", nullable = false, length = 20)
    public String getAlias() {
        return this.Alias;
    }

    public void setAlias(String Alias) {
        this.Alias = Alias;
    }

    @Temporal(TemporalType.DATE)
    @Column(name = "allowfrom", nullable = false, length = 10)
    public Date getAllowfrom() {
        return this.Allowfrom;
    }

    public void setAllowfrom(Date Allowfrom) {
        this.Allowfrom = Allowfrom;
    }

    @Temporal(TemporalType.DATE)
    @Column(name = "allowto", nullable = false, length = 10)
    public Date getAllowto() {
        return this.Allowto;
    }

    public void setAllowto(Date Allowto) {
        this.Allowto = Allowto;
    }

    @Column(name = "anyoperation", nullable = false)
    public boolean isAnyoperation() {
        return this.Anyoperation;
    }

    public void setAnyoperation(boolean Anyoperation) {
        this.Anyoperation = Anyoperation;
    }

    @Column(name = "anyservice", nullable = false)
    public boolean isAnyservice() {
        return this.Anyservice;
    }

    public void setAnyservice(boolean Anyservice) {
        this.Anyservice = Anyservice;
    }

    @Column(name = "csw_anyattribute", nullable = false)
    public boolean isCsw_anyattribute() {
        return this.Csw_anyattribute;
    }

    public void setCsw_anyattribute(boolean Csw_anyattribute) {
        this.Csw_anyattribute = Csw_anyattribute;
    }

    @Column(name = "csw_anycontext", nullable = false)
    public boolean isCsw_anycontext() {
        return this.Csw_anycontext;
    }

    public void setCsw_anycontext(boolean Csw_anycontext) {
        this.Csw_anycontext = Csw_anycontext;
    }

    @Column(name = "csw_anystate", nullable = false)
    public boolean isCsw_anystate() {
        return this.Csw_anystate;
    }

    public void setCsw_anystate(boolean Csw_anystate) {
        this.Csw_anystate = Csw_anystate;
    }

    @Column(name = "csw_anyvisibility", nullable = false)
    public boolean isCsw_anyvisibility() {
        return this.Csw_anyvisibility;
    }

    public void setCsw_anyvisibility(boolean Csw_anyvisibility) {
        this.Csw_anyvisibility = Csw_anyvisibility;
    }

    @Column(name = "csw_includeharvested", nullable = false)
    public boolean isCsw_includeharvested() {
        return this.Csw_includeharvested;
    }

    public void setCsw_includeharvested(boolean Csw_includeharvested) {
        this.Csw_includeharvested = Csw_includeharvested;
    }

    @Column(name = "wms_minimumwidth")
    public String getWms_minimumwidth() {
        return this.Wms_minimumwidth;
    }

    public void setWms_minimumwidth(String Wms_minimumwidth) {
        this.Wms_minimumwidth = Wms_minimumwidth;
    }

    @Column(name = "wms_minimumheight")
    public String getWms_minimumheight() {
        return this.Wms_minimumheight;
    }

    public void setWms_minimumheight(String Wms_minimumheight) {
        this.Wms_minimumheight = Wms_minimumheight;
    }

    @Column(name = "wms_maximumwidth")
    public String getWms_maximumwidth() {
        return this.Wms_maximumwidth;
    }

    public void setWms_maximumwidth(String Wms_maximumwidth) {
        this.Wms_maximumwidth = Wms_maximumwidth;
    }

    @Column(name = "wms_maximumheight")
    public String getWms_maximumheight() {
        return this.Wms_maximumheight;
    }

    public void setWms_maximumheight(String Wms_maximumheight) {
        this.Wms_maximumheight = Wms_maximumheight;
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

    @OneToMany(fetch = FetchType.LAZY, mappedBy = "sdiPolicy")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    @Fetch(FetchMode.JOIN)
    public Set<SdiPhysicalservicePolicy> getSdiPhysicalservicePolicies() {
        return this.sdiPhysicalservicePolicies;
    }

    public void setSdiPhysicalservicePolicies(
            Set<SdiPhysicalservicePolicy> sdiPhysicalservicePolicies) {
        this.sdiPhysicalservicePolicies = sdiPhysicalservicePolicies;
    }

    @OneToMany(fetch = FetchType.LAZY)
    @JoinTable(name = "SdiPolicyOrganism", joinColumns = {
        @JoinColumn(name = "policy_id")}, inverseJoinColumns = {
        @JoinColumn(name = "organism_id")})
    @Filter(name = "entityState", condition = "State = 1")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    public Set<SdiOrganism> getSdiOrganisms() {
        return this.sdiOrganisms;
    }

    public void setSdiOrganisms(Set<SdiOrganism> sdiOrganisms) {
        this.sdiOrganisms = sdiOrganisms;
    }
    
    @OneToMany(fetch = FetchType.LAZY)
    @JoinTable(name = "SdiPolicyCategory", joinColumns = {
        @JoinColumn(name = "policy_id")}, inverseJoinColumns = {
        @JoinColumn(name = "category_id")})
    @Filter(name = "entityState", condition = "State = 1")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    public Set<SdiCategory> getSdiCategories() {
        return this.sdiCategories;
    }

    public void setSdiCategories(Set<SdiCategory> sdiCategories) {
        this.sdiCategories = sdiCategories;
    }

    @OneToMany(fetch = FetchType.LAZY, mappedBy = "sdiPolicy")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    @Fetch(FetchMode.JOIN)
    public Set<SdiAllowedoperation> getSdiAllowedoperations() {
        return this.sdiAllowedoperations;
    }

    public void setSdiAllowedoperations(
            Set<SdiAllowedoperation> sdiAllowedoperations) {
        this.sdiAllowedoperations = sdiAllowedoperations;
    }

    @OneToMany(fetch = FetchType.LAZY)
    @JoinTable(name = "SdiPolicyUser", joinColumns = {
        @JoinColumn(name = "policy_id")}, inverseJoinColumns = {
        @JoinColumn(name = "user_id")})
    @Filter(name = "entityState", condition = "State = 1")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    public Set<SdiUser> getSdiUsers() {
        return this.sdiUsers;
    }

    public void setSdiUsers(Set<SdiUser> sdiUsers) {
        this.sdiUsers = sdiUsers;
    }

    @OneToMany(fetch = FetchType.LAZY, mappedBy = "sdiPolicy")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    public Set<SdiPolicyMetadatastate> getSdiPolicyMetadatastates() {
        return this.sdiPolicyMetadatastates;
    }

    public void setSdiPolicyMetadatastates(
            Set<SdiPolicyMetadatastate> sdiPolicyMetadatastates) {
        this.sdiPolicyMetadatastates = sdiPolicyMetadatastates;
    }

    @OneToMany(fetch = FetchType.LAZY, mappedBy = "sdiPolicy")
    @Cache(usage = CacheConcurrencyStrategy.READ_ONLY)
    public Set<SdiExcludedattribute> getSdiExcludedattributes() {
        return this.sdiExcludedattributes;
    }

    public void setSdiExcludedattributes(
            Set<SdiExcludedattribute> sdiExcludedattributes) {
        this.sdiExcludedattributes = sdiExcludedattributes;
    }

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "csw_accessscope_id", nullable = false)
    @Filter(name = "entityState", condition = "State = 1")
    public SdiSysAccessscope getCswSdiSysAccessscope() {
        return this.cswSdiSysAccessscope;
    }

    public void setCswSdiSysAccessscope(SdiSysAccessscope cswSdiSysAccessscope) {
        this.cswSdiSysAccessscope = cswSdiSysAccessscope;
    }

    @Column(name = "csw_anyresourcetype", nullable = false)
    public boolean isCsw_anyresourcetype() {
        return this.Csw_anyresourcetype;
    }

    public void setCsw_anyresourcetype(boolean Csw_anyresourcetype) {
        this.Csw_anyresourcetype = Csw_anyresourcetype;
    }
    
    @OneToMany(fetch = FetchType.LAZY)
    @JoinTable(name = "SdiPolicyResourcetype", joinColumns = {@JoinColumn(name = "policy_id")}, inverseJoinColumns = {@JoinColumn (name = "resourcetype_id")})
    @Filter(name = "entityState",condition="State = 1")
    @Cache (usage=CacheConcurrencyStrategy.READ_ONLY)
    public Set<SdiResourcetype> getSdiResourcetypes() {
        return this.sdiResourcetypes;
    }

    public void setSdiResourcetypes(
            Set<SdiResourcetype> sdiResourcetypes) {
        this.sdiResourcetypes = sdiResourcetypes;
    }

    @OneToMany(fetch = FetchType.LAZY, mappedBy = "sdiPolicy")
    public Set<SdiPolicyVisibility> getSdiPolicyVisibilities() {
        return this.sdiPolicyVisibilities;
    }

    public void setSdiPolicyVisibilities(
            Set<SdiPolicyVisibility> sdiPolicyVisibilities) {
        this.sdiPolicyVisibilities = sdiPolicyVisibilities;
    }
}
