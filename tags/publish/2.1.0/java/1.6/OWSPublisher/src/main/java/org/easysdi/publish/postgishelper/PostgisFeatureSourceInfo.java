/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & Remy Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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
package org.easysdi.publish.postgishelper;

import java.io.IOException;
import java.math.BigDecimal;
import java.math.BigInteger;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.StringTokenizer;

import org.geotools.data.DataStore;
import org.geotools.data.DataStoreFinder;
import org.geotools.data.FeatureSource;
import org.opengis.feature.simple.SimpleFeature;
import org.opengis.feature.simple.SimpleFeatureType;
import org.opengis.feature.type.GeometryDescriptor;
import org.opengis.geometry.DirectPosition;
import org.opengis.geometry.MismatchedDimensionException;
import org.opengis.referencing.FactoryException;
import org.opengis.referencing.ReferenceIdentifier;
import org.opengis.referencing.crs.CRSFactory;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.crs.GeographicCRS;
import org.opengis.referencing.operation.CoordinateOperationFactory;
import org.opengis.referencing.operation.MathTransform;
import org.opengis.referencing.operation.TransformException;
import org.geotools.data.DataUtilities;
import org.geotools.data.postgis.PostgisDataStoreFactory;
import org.geotools.geometry.GeneralDirectPosition;
import org.geotools.geometry.jts.ReferencedEnvelope;
import org.geotools.referencing.CRS;
import org.geotools.referencing.ReferencingFactoryFinder;
import org.geotools.feature.FeatureCollection;

import com.sun.jmx.snmp.Timestamp;
import com.vividsolutions.jts.geom.Envelope;
import org.geotools.feature.FeatureIterator;
import org.opengis.referencing.operation.CoordinateOperation;

import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.geom.GeometryCollection;
import com.vividsolutions.jts.geom.LineString;
import com.vividsolutions.jts.geom.LinearRing;
import com.vividsolutions.jts.geom.MultiLineString;
import com.vividsolutions.jts.geom.MultiPoint;
import com.vividsolutions.jts.geom.MultiPolygon;
import com.vividsolutions.jts.geom.Point;
import com.vividsolutions.jts.geom.Polygon;
import com.vividsolutions.jts.geom.PrecisionModel;
import com.vividsolutions.jts.geom.Triangle;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.TransformationException;
import org.easysdi.publish.helper.Attribute;
import org.easysdi.publish.helper.IFeatureSourceInfo;

public class PostgisFeatureSourceInfo implements IFeatureSourceInfo {

	private List<Attribute> atrList;
	private String url;
	private String user;
	private String pwd;

	public List<Attribute> getAtrList() {
		return atrList;
	}

	public String getCrsCode() {
		return crsCode;
	}

	public HashMap<String, Double> getBbox() {
		return bbox;
	}

	public String getGeometry() {
		return geometry;
	}

	public String getTable() {
		return table;
	}

	public Map<String, String> getConnectionInfo() {
		return connectionInfo;
	}

	public String getCrsWkt() {
		return crsWkt;
	}

	private String crsCode;
	private String crsWkt = "";
	private HashMap<String, Double> bbox = new HashMap<String, Double>();
	private String geometry;
	private String table;
	Map<String, String> connectionInfo;

	public PostgisFeatureSourceInfo(){
		atrList = new java.util.ArrayList<Attribute>();
	}

	public void setConnectioninfo(Map<String, String> connectionInfo){
		this.connectionInfo = connectionInfo;
	}

	public void setTable(String table){
		this.table = table;
	}

	public PostgisFeatureSourceInfo(Map<String, String> connectionInfo, String table) throws TransformationException, PublishConfigurationException{
		atrList = new java.util.ArrayList<Attribute>();
		this.table = table;
		this.connectionInfo = connectionInfo;
		getFeatureSourceInfo();
	}

	public PostgisFeatureSourceInfo(Geodatabase geoDb, String table) throws TransformationException, PublishConfigurationException{
		atrList = new java.util.ArrayList<Attribute>();
		this.table = table;

		//Fill in the connection info to the PostGIS database
		Map<String, String> connectionInfo = new HashMap<String, String>();
		connectionInfo.put( PostgisDataStoreFactory.DBTYPE.key, geoDb.getGeodatabaseType().getName());
		connectionInfo.put( PostgisDataStoreFactory.HOST.key, geoDb.getDbHost() );
		connectionInfo.put( PostgisDataStoreFactory.PORT.key, geoDb.getDbPort().toString() );
		connectionInfo.put( PostgisDataStoreFactory.SCHEMA.key, geoDb.getScheme());
		connectionInfo.put( PostgisDataStoreFactory.DATABASE.key, geoDb.getDbName());
		connectionInfo.put( PostgisDataStoreFactory.USER.key, geoDb.getUser());
		connectionInfo.put( PostgisDataStoreFactory.PASSWD.key, geoDb.getPwd());	

		this.connectionInfo = connectionInfo;
		getFeatureSourceInfo();
	}

	public void setGeodatabase(Geodatabase geoDb) throws PublishConfigurationException
	{
		this.url = geoDb.getUrl();
		this.user = geoDb.getUser();
		this.pwd = geoDb.getPwd();

		Map<String, String> connectionInfo = new HashMap<String, String>();
		connectionInfo.put( PostgisDataStoreFactory.DBTYPE.key, geoDb.getGeodatabaseType().getName());
		connectionInfo.put( PostgisDataStoreFactory.HOST.key, geoDb.getDbHost() );
		connectionInfo.put( PostgisDataStoreFactory.PORT.key, geoDb.getDbPort().toString());
		connectionInfo.put( PostgisDataStoreFactory.SCHEMA.key, geoDb.getScheme());
		connectionInfo.put( PostgisDataStoreFactory.DATABASE.key, geoDb.getDbName() );
		connectionInfo.put( PostgisDataStoreFactory.USER.key, geoDb.getUser() );
		connectionInfo.put( PostgisDataStoreFactory.PASSWD.key, geoDb.getPwd() );	

		this.connectionInfo = connectionInfo;
	}

	@SuppressWarnings("unchecked")
	public void getFeatureSourceInfo() throws TransformationException, PublishConfigurationException {
		//get the PostGIS datastore through Geotools API
		DataStore dataStore = null;
		String[] typeNames = null;
		try {
			//connect to the PostGIS datastore
			try{
				dataStore = DataStoreFinder.getDataStore(this.connectionInfo);
			} catch (IOException e) {
				throw new PublishConfigurationException("PostgisFeatureSourceinfo, getFeatureSourceInfo(): The postgis datastore was not found with the supplied connection info:"+
						" DBTYPE:"+connectionInfo.get(PostgisDataStoreFactory.DBTYPE.key)+", "+
						" HOST:"+connectionInfo.get(PostgisDataStoreFactory.HOST.key)+", "+
						" PORT:"+connectionInfo.get(PostgisDataStoreFactory.PORT.key)+", "+
						" SCHEMA:"+connectionInfo.get(PostgisDataStoreFactory.SCHEMA.key)+", "+
						" DATABASE:"+connectionInfo.get(PostgisDataStoreFactory.DATABASE.key)+", "+
						" USER:"+connectionInfo.get(PostgisDataStoreFactory.USER.key)
				);
			}
			//get the asked table
			FeatureSource<SimpleFeatureType, SimpleFeature> featureSource;
			try {
				featureSource = dataStore.getFeatureSource(this.table);
			} catch (IOException e) {
				throw new TransformationException("PostgisFeatureSourceinfo, getFeatureSourceInfo(): The Feature Source associated to the table: "+this.table+" does not exist.");
			}
			SimpleFeatureType simpleFeatureType = featureSource.getSchema();
			String dataHeader = DataUtilities.spec( simpleFeatureType );

			//retrieve the coordinate reference system
			GeometryDescriptor gd = simpleFeatureType.getGeometryDescriptor();
			CoordinateReferenceSystem crs = gd.getCoordinateReferenceSystem();
			if(crs == null)
				throw new TransformationException("The coordinate system supplied is not valid for the dataset");
			Set <ReferenceIdentifier> ri = crs.getIdentifiers();
			Iterator<ReferenceIdentifier> ts1 = ri.iterator (  ) ; 
			while (ts1.hasNext()){  
				ReferenceIdentifier riNext = ts1.next();
				this.crsCode = riNext.getCodeSpace()+":"+riNext.getCode();
				this.crsWkt = crs.toWKT();
			}

			//retrieve the native BBOX
			FeatureCollection<SimpleFeatureType, SimpleFeature> collection;
			try {
				collection = featureSource.getFeatures();
			} catch (IOException e) {
				throw new TransformationException("PostgisFeatureSourceinfo, getFeatureSourceInfo(): the data could not be retrieved for the table:"+this.table);
			}

			Envelope bounds = new Envelope();
			FeatureIterator<SimpleFeature> features = collection.features();
			try {
				while( features.hasNext()){
					SimpleFeature feature = features.next();
					bounds.expandToInclude( new ReferencedEnvelope( feature.getBounds() ) );
				}
			}
			finally{
				features.close();
			}
			this.bbox.put("MinX_native", bounds.getMinX());
			this.bbox.put("MinY_native", bounds.getMinY());
			this.bbox.put("MaxX_native", bounds.getMaxX());
			this.bbox.put("MaxY_native", bounds.getMaxY());

			DirectPosition pMin = null;
			DirectPosition pMax = null;
			//Get the WGS84 expression of the BBOX
			if(!this.crsCode.equals("EPSG:4326")){
				GeographicCRS targetCRS = org.geotools.referencing.crs.DefaultGeographicCRS.WGS84;
				CoordinateOperationFactory coFactory = ReferencingFactoryFinder.getCoordinateOperationFactory(null);	
				CoordinateOperation co = coFactory.createOperation(crs, targetCRS);
				MathTransform transform = co.getMathTransform();
				pMin = new GeneralDirectPosition(bounds.getMinX(),bounds.getMinY());
				pMax = new GeneralDirectPosition(bounds.getMaxX(),bounds.getMaxY());

				try {
					pMin = transform.transform(pMin, pMin);
					pMax = transform.transform(pMax, pMax);
				} catch (MismatchedDimensionException e) {
					throw new TransformationException("It was not possible to convert from the source projection to WGS84 for the table:"+this.table+" because the dimensions mismatched");
				} catch (TransformException e) {
					throw new TransformationException("It was not possible to convert from the source projection to WGS84 for the table:"+this.table);
				}
			}

			if(!this.crsCode.equals("EPSG:4326")){
				this.bbox.put("MinX", pMin.getOrdinate(0));
				this.bbox.put("MinY", pMin.getOrdinate(1));
				this.bbox.put("MaxX", pMax.getOrdinate(0));
				this.bbox.put("MaxY", pMax.getOrdinate(1));
			}else{
				this.bbox.put("MinX", bounds.getMinX());
				this.bbox.put("MinY", bounds.getMinY());
				this.bbox.put("MaxX", bounds.getMaxX());
				this.bbox.put("MaxY", bounds.getMaxY());
			}

			//retrieve the attributes
			StringTokenizer st = new StringTokenizer(dataHeader, ",");
			while(st.hasMoreTokens()){
				String[] field = st.nextToken().split(":");
				//date not supported for now..
				Class c = getClassForName(field[1]);
				Boolean isGeometry = isFieldGeometry(field[1]);
				if(isGeometry)
					this.geometry = field[1];
				// Build the attribute List
				this.atrList.add(new Attribute(field[0], c, field[1], isGeometry));
			}			

		}catch(FactoryException fe){
			fe.printStackTrace();
		}finally{
			if(dataStore != null)
				dataStore.dispose();
		}
	}

	private Class getClassForName (String name) throws TransformationException{
		Class c = null;
		//JTS geometry
		if(name.equals("MultiLineString"))
			c = MultiLineString.class;
		if(name.equals("Geometry"))
			c = Geometry.class;
		if(name.equals("LineString"))
			c = LineString.class;
		if(name.equals("MultiPoint"))
			c = MultiPoint.class;
		if(name.equals("MultiPolygon"))
			c = MultiPolygon.class;
		if(name.equals("Point"))
			c = Point.class;
		if(name.equals("Polygon"))
			c = Polygon.class;
		if(name.equals("Triangle"))
			c = Triangle.class;
		if(name.equals("LinearRing"))
			c = LinearRing.class;
		if(name.equals("GeometryCollection"))
			c = GeometryCollection.class;
		if(name.equals("PrecisionModel"))
			c = PrecisionModel.class;

		//Java type
		if(name.equals("Integer") || name.equals("java.lang.Integer"))
			c = Integer.class;
		if(name.equals("Long") || name.equals("java.lang.Long"))
			c = Long.class;
		if(name.equals("Double") || name.equals("java.lang.Double"))
			c = Double.class;
		if(name.equals("Float") || name.equals("java.lang.Float"))
			c = Float.class;
		if(name.equals("Byte") || name.equals("java.lang.Byte"))
			c = Byte.class;
		if(name.equals("String") || name.equals("java.lang.String"))
			c = String.class;
		if(name.equals("Short") || name.equals("java.lang.Short"))
			c = Short.class;
		if(name.equals("Character") || name.equals("java.lang.Character"))
			c = Character.class;
		if(name.equals("Boolean") || name.equals("java.lang.Boolean"))
			c = Boolean.class;
		if(name.equals("BigDecimal") || name.equals("java.math.BigDecimal"))
			c = BigDecimal.class;
		if(name.equals("BigInteger") || name.equals("java.math.BigInteger"))
			c = BigInteger.class;
		if(name.equals("Date") || name.equals("java.util.Date"))
			c = Date.class;
		if(name.equals("java.sql.Date"))
			c = Date.class;
		if(name.equals("java.sql.Timestamp"))
			c = Timestamp.class;
		if(c == null)
			throw new TransformationException("PostgisFeatureSourceInfo, getClassForName():  The type: "+name+" could not be recognized, you must add a condition to handle this type.");
		return c;
	}
	private Boolean isFieldGeometry (String name) throws TransformationException{
		Boolean b = null;
		//JTS geometry
		if(name.equals("MultiLineString"))
			b = true;
		if(name.equals("Geometry"))
			b = true;
		if(name.equals("LineString"))
			b = true;
		if(name.equals("MultiPoint"))
			b = true;
		if(name.equals("MultiPolygon"))
			b = true;
		if(name.equals("Point"))
			b = true;
		if(name.equals("Polygon"))
			b = true;
		if(name.equals("Triangle"))
			b = true;
		if(name.equals("LinearRing"))
			b = true;
		if(name.equals("GeometryCollection"))
			b = true;
		if(name.equals("PrecisionModel"))
			b = true;

		//Java type
		if(name.equals("Integer") || name.equals("java.lang.Integer"))
			b = false;
		if(name.equals("Long") || name.equals("java.lang.Long"))
			b = false;
		if(name.equals("Double") || name.equals("java.lang.Double"))
			b = false;
		if(name.equals("Float") || name.equals("java.lang.Float"))
			b = false;
		if(name.equals("Byte") || name.equals("java.lang.Byte"))
			b = false;
		if(name.equals("String") || name.equals("java.lang.String"))
			b = false;
		if(name.equals("Short") || name.equals("java.lang.Short"))
			b = false;
		if(name.equals("Character") || name.equals("java.lang.Character"))
			b = false;
		if(name.equals("Boolean") || name.equals("java.lang.Boolean"))
			b = false;
		if(name.equals("BigDecimal") || name.equals("java.math.BigDecimal"))
			b = false;
		if(name.equals("BigInteger") || name.equals("java.math.BigInteger"))
			b = false;
		if(name.equals("Date") || name.equals("java.util.Date"))
			b = false;
		if(name.equals("java.sql.Date"))
			b = false;
		if(name.equals("java.sql.Timestamp"))
			b = false;
		if(b == null)
			throw new TransformationException("PostgisFeatureSourceInfo, isFieldGeometry():  It was not possible to determine wether or not the type: "+name+" is a geometry, you must add a condition to handle this type.");
		return b;
	}
}

