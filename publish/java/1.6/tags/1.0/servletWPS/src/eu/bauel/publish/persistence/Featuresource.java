/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 Remy Baud (remy.baud@asitvd.ch), Antoine Elbel (antoine@probel.eu)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the	
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */
package eu.bauel.publish.persistence;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;

import eu.bauel.publish.exception.FeatureSourceNotFoundException;

/*
 * This class represent a table in the persistence. It
 * extends the JTable class to make it possible to retrieve
 * data into an object of this class, or store data from
 * an object of this class to the database.
 */
public class Featuresource extends JTable {
	private Integer[] id = new Integer[1];
	private Integer[] diffuserId = new Integer[1];
	private String[] featureGUID = new String[1];
	private String[] tableName = new String[1];
	private String[] scriptName = new String[1];
	private String[] sourceDataType = new String[1];
	private String[] crsCode = new String[1];
	private String[] fieldsName = new String[1];
	private Date[] creation_date = new Date[1];
	private Date[] update_date = new Date[1];
	private String[] status = new String[1];
	private String[] excMessage = new String[1];
	private String[] excDetail = new String[1];

	private HashMap fields = new HashMap();
	
	public static Featuresource mockFactory()
	{
		Diffuser diffuser = new Diffuser(0);
		
	    SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");  

	    Featuresource mock = new Featuresource();
	    //mock.store();
	    
		mock.id[0]=5;
		int diffId = diffuser.getId();
		mock.diffuserId[0]=diffId;
		mock.featureGUID[0]="2d8176f7cccf40bc5a8f3cb2b87e156e";
		
		//TRAP AND DANGER: Replace the table name with an existing table in you Postgis DB
		//Otherwise the tests with this mock are going to fail
		mock.tableName[0]="_essai_sdi_plop4";
		//mock.tableName[0]="gg25_a";
		
		try {
			mock.creation_date[0]= sdf.parse("2004-12-01");
			Calendar cal = Calendar.getInstance();
			Date dt = cal.getTime();
			mock.update_date[0]= cal.getTime();
			//mock.update_date[0]= sdf.parse("2004-12-01");
			
		} catch (ParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		mock.forceStore();
		return mock;
	}
	
	//Constructor to load an empty instance
	public Featuresource(Connection c){
		super(c, "featuresource", "id");
		initFields();
		setFields(fields);
	}
	
	public Featuresource(){
		super("featuresource", "id");
		initFields();
		setFields(fields);
	}
	
	public Featuresource(int id ){
		super("featuresource", "id");
		initFields();
		setFields(fields);
		loadData(id);	
	}
	
	//Constructor to load an instance with existing data
	public Featuresource(int id, Connection c){
		super(c, "featuresource", "id");
		initFields();
		setFields(fields);
		loadData(id);
	}
	
	private void initFields(){
		fields.put( "id", id );
		fields.put( "diffuserid", diffuserId );
		fields.put( "featureguid", featureGUID );
		fields.put( "tablename", tableName );
		fields.put( "scriptname", scriptName );
		fields.put( "sourcedatatype", sourceDataType );
		fields.put( "fieldsname", fieldsName );
		fields.put( "crscode", crsCode );
		fields.put( "creation_date", creation_date );
		fields.put( "update_date", update_date );
		fields.put( "status", status );
		fields.put( "excmessage", excMessage );
		fields.put( "excdetail", excDetail );
	}

	public void setId(Integer id) {
		this.id[0] = id;
	}

	public Integer getId() {
		return id[0];
	}

	public void setDiffuserId(Integer diffuserId) {
		this.diffuserId[0] = diffuserId;
	}

	public Integer getDiffuserId() {
		return diffuserId[0];
	}

	public Diffuser getDiffuser() {
		return new Diffuser( diffuserId[0]) ;
	}

	public void setFeatureGUID(String featureGUID) {
		this.featureGUID[0] = featureGUID;
	}

	public String getFeatureGUID() {
		return featureGUID[0];
	}

	public void setTableName(String tableName) {
		this.tableName[0] = tableName;
	}

	public String getTableName() {
		return tableName[0];
	}

	public void setScriptName(String scriptName) {
		this.scriptName[0] = scriptName;
	}

	public String getScriptName() {
		return scriptName[0];
	}
	
	public void setSourceDataType(String sourceDataType) {
		this.sourceDataType[0] = sourceDataType;
	}

	public String getSourceDataType() {
		return sourceDataType[0];
	}
	
	public void setFieldsName(String fieldsName) {
		this.fieldsName[0] = fieldsName;
	}

	public String getFieldsName() {
		return fieldsName[0];
	}
	
	public void setCrsCode(String crsCode) {
		this.crsCode[0] = crsCode;
	}

	public String getCrsCode() {
		return crsCode[0];
	}
	
	public void setCreation_date(Date creation_date) {
		this.creation_date[0] = creation_date;
	}

	public Date getCreation_date() {
		return creation_date[0];
	}

	public void setUpdate_date(Date update_date) {
		this.update_date[0] = update_date;
	}

	public Date getUpdate_date() {
		return update_date[0];
	}
	
	public void setStatus(FeatureSourceStatus status) {
		this.status[0] = status.getText();
	}

	public FeatureSourceStatus getStatus() {
		if(status[0].equals("AVAILABLE"))
			return FeatureSourceStatus.AVAILABLE;
		else if(status[0].equals("CREATING"))
			return FeatureSourceStatus.CREATING;
		else if(status[0].equals("UPDATING"))
			return FeatureSourceStatus.UPDATING;
		else if(status[0].equals("UNAVAILABLE"))
			return FeatureSourceStatus.UNAVAILABLE;
		return null;
	}
	
	public void setExcMessage(String excMessage) {
		this.excMessage[0] = excMessage;
	}

	public String getExcMessage() {
		return excMessage[0];
	}
	
	public void setExcDetail(String excDetail) {
		this.excDetail[0] = excDetail;
	}

	public String getExcDetail() {
		return excDetail[0];
	}
	
	public static int getIdFromGuid(String guid) throws FeatureSourceNotFoundException{		
		ResultSet rs;
		int id = -1;
		try {
			Statement st = c.createStatement();
			rs = st.executeQuery("SELECT id FROM featuresource where featureguid='"+guid+"'");
			if(!rs.next())
				throw new FeatureSourceNotFoundException("FeatureSource with Guid: "+guid+" does not exist");
			else
				id = rs.getInt("id");
		} catch (SQLException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return id;
	}
	
	public static Featuresource getFeatureSourceFromGUID (String guid) throws FeatureSourceNotFoundException{
		int id = getIdFromGuid(guid);
		return new Featuresource(id, c);
	}
}
