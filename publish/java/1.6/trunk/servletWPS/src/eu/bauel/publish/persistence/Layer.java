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
import java.util.Date;
import java.util.HashMap;

/*
 * This class represent a table in the persistence. It
 * extends the JTable class to make it possible to retrieve
 * data into an object of this class, or store data from
 * an object of this class to the database.
 */
public class Layer extends JTable {
	private Integer[] id = new Integer[1];
	private String[] layerGUID = new String[1];
	private String[] layerKeywordList = new String[1];
	private String[] layerTitle = new String[1];
	private String[] layerName = new String[1];
	private String[] layerDescription = new String[1];
	private Integer[] status = new Integer[1];
	private Integer[] featuresourceId = new Integer[1];
	private Date[] creation_date = new Date[1];
	private Date[] update_date = new Date[1];

	private HashMap fields = new HashMap();

	public static Layer mockFactory()
	{
		SimpleDateFormat sdf = new SimpleDateFormat("dd-MM-yyyy");  

		Layer mock = new Layer();

		mock.id[0]=10;
		mock.layerGUID[0]="2d8176f7cccf40bc5a8f3cb2b87e156e";
		mock.layerKeywordList[0]="vive,vivivila,vie";
		mock.layerTitle[0]="my_beautiful_places";
		mock.layerName[0]="gzar_beautiful_places";
		mock.layerDescription[0]=" chapi ges";
		mock.status[0]=1;
		mock.featuresourceId[0]=5;

		try {
			mock.creation_date[0]= sdf.parse("01-12-2004");
			mock.update_date[0]= sdf.parse("01-12-2004");

		} catch (ParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return mock;
	}

	//Constructor to load an empty instance
	public Layer(Connection c){
		super(c, "layer", "id");
		initFields();
		setFields(fields);
	}

	public Layer(){
		super( "layer", "id");
		initFields();
		setFields(fields);
	}
	
	public Layer(int id){
		super("layer", "id");
		initFields();
		setFields(fields);
		loadData(id);
	}
	
	//Constructor to load an instance with existing data
	public Layer(int id, Connection c){
		super(c, "layer", "id");
		initFields();
		setFields(fields);
		loadData(id);
	}

	private void initFields(){
		fields.put( "id", id );
		fields.put( "layerguid", layerGUID );
		fields.put( "layerkeywordlist", layerKeywordList );
		fields.put( "layertitle", layerTitle );
		fields.put( "layername", layerName );
		fields.put( "layerdescription", layerDescription );
		fields.put( "status", status );
		fields.put( "featuresourceid", featuresourceId );
		fields.put( "creation_date", creation_date );
		fields.put( "update_date", update_date );		
	}

	public void setLayerTitle(String layerTitle) {
		this.layerTitle[0] = layerTitle;
	}

	public String getLayerTitle() {
		return layerTitle[0];
	}

	public Featuresource getFeatureSource()
	{
		return new Featuresource( featuresourceId[0] );
	}

	public Diffuser getDiffuser()
	{
		Diffuser d = null;
		try {
			if( null == featuresourceId[0] )
			{
				logger.info("No Feature Source id set for this layer! " );
				return d;
			}
			Featuresource fs = new Featuresource( featuresourceId[0] );
			if( null == fs )
			{
				logger.info("No Feature Source id: " + featuresourceId[0] );
				return d;
			}
				
			d = new Diffuser( fs.getDiffuserId() );

			logger.info("Diffuser Name for this layer: " + d.getName() );
			logger.info("Feature Source Name for this layer: " + fs.getTableName() );
			
		} catch (Exception e) {
			logger.warning(e.getMessage());
			e.printStackTrace();
		}
		return d;
	}

	public void setLayerKeywordList(String layerKeywordList) {
		this.layerKeywordList[0] = layerKeywordList;
	}

	public String getLayerKeywordList() {
		return layerKeywordList[0];
	}

	public void setLayerGUID(String layerGUID) {
		this.layerGUID[0] = layerGUID;
	}

	public String getLayerGUID() {
		return layerGUID[0];
	}

	public void setLayerName(String layerName) {
		this.layerName[0] = layerName;
	}

	public String getLayerName() {
		return layerName[0];
	}

	public void setId(Integer id) {
		this.id[0] = id;
	}

	public Integer getId() {
		return id[0];
	}

	public void setLayerDescription(String layerDescription) {
		this.layerDescription[0] = layerDescription;
	}

	public String getLayerDescription() {
		return layerDescription[0];
	}

	public void setStatus(Integer status) {
		this.status[0] = status;
	}

	public Integer getStatus() {
		return status[0];
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

	public static Layer getLayerFromGUID( String _GUID)
	{
		Layer l = null;
		String query = "";
		try {
			Statement statement;
			statement = c.createStatement();
			query = "SELECT id FROM layer where layerGUID='"+_GUID+"'";
			ResultSet rs = statement.executeQuery(query);
			rs.next();
			int id = rs.getInt("id");
			l = new Layer(id);
		} catch (SQLException e) {
			logger.info("Query was: " + query);
			logger.warning(e.getMessage());
			e.printStackTrace();
		} catch (Exception e) {
			logger.info("Query was: " + query);
			logger.warning(e.getMessage());
			e.printStackTrace();
		}

		return l;
	}
	
	//Return the first Layer with the matching name
	public static Layer getLayerFromName( String Name )
	{
		Layer l = null;
		Statement statement;
		try {
			statement = c.createStatement();
			ResultSet rs = statement.executeQuery("SELECT id FROM layer where layerName='"+Name+"'");
			if(rs.next()){
				int id = rs.getInt("id");
				l = new Layer(id);
			}
		} catch (SQLException e) {
			logger.warning("getLayerFromName for " + Name+ "returns null");
			logger.warning(e.getMessage());
			e.printStackTrace();
		}
		return l;
	}


	public void setFeaturesourceId(Integer featuresourceId) {
		this.featuresourceId[0] = featuresourceId;
	}

	public Integer getFeaturesourceId() {
		return featuresourceId[0];
	}

	public static boolean isALayerAttachedToThisFeatureSource( String FeatureSourceGuid )
	{        
		boolean b = false;
		try {
			Statement statement;
			statement = c.createStatement();
			ResultSet rs = statement.executeQuery("SELECT count(*) FROM featuresource f, layer l where l.featuresourceid=f.ID AND f.featureguid='"+FeatureSourceGuid+"'");
			rs.next();
			//if the result is bigger than null there is still at least one layer attached to this feature source
			logger.info("No of Layers Attached to FeatureSource " + FeatureSourceGuid + " is: " + rs.getInt(1) );
			b = (0 == rs.getInt(1) ) ? false : true;
		} catch (SQLException e) {
			logger.warning(e.getMessage());
			e.printStackTrace();
		} catch (Exception e) {
				logger.warning(e.getMessage());
				e.printStackTrace();
		}

		return b;
	}

}
