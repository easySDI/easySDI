package org.easysdi.publish.postgishelper;

import java.io.IOException;
import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.DataSourceNotFoundException;
import org.easysdi.publish.exception.DataSourceWrongFormatException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.PublishGeneralException;
import org.easysdi.publish.exception.TransformationException;
import org.easysdi.publish.helper.Attribute;
import org.easysdi.publish.helper.IFeatureSourceInfo;
import org.easysdi.publish.util.Utils;

public class PostgisFeatureSourceInfoOgr implements IFeatureSourceInfo{

	Logger logger = Logger.getLogger("org.easysdi.publish.transformation.plugin.PostgisFeatureSourceInfoOgr");
	static String CRLF = System.getProperty("line.separator");
	private List<Attribute> atrList;
	private Connection connection;
	private Statement statement;
	private ResultSet rs;
	private String url;
	private String user;
	private String pwd;
	private String crsCode;
	private int featureCount;
	private String crsWkt = "";
	private HashMap<String, Double> bbox = new HashMap<String, Double>();
	private String geometry;
	private String geometryColumn;
	private String table;
	Map<String, String> connectionInfo;

	public PostgisFeatureSourceInfoOgr(){
		atrList = new java.util.ArrayList<Attribute>();
	}

	public void setConnectioninfo(Map<String, String> connectionInfo){
		this.connectionInfo = connectionInfo;
	}

	public void setTable(String table){
		this.table = table;
	}

	public PostgisFeatureSourceInfoOgr(Map<String, String> connectionInfo, String table) throws TransformationException, PublishConfigurationException{
		atrList = new java.util.ArrayList<Attribute>();
		this.connectionInfo = connectionInfo;
		this.table = table;
		this.user = connectionInfo.get("dbusername");
		this.pwd = connectionInfo.get("dbpassword");
		this.url = "jdbc:postgresql://"+connectionInfo.get("dbhost")+":"+connectionInfo.get("dbport")+"/"+connectionInfo.get("dbname");
		getFeatureSourceInfo();
	}

	public PostgisFeatureSourceInfoOgr(Geodatabase geoDb, String table) throws TransformationException, PublishConfigurationException{
		atrList = new java.util.ArrayList<Attribute>();
		this.table = table;

		String url = geoDb.getUrl();
		String[] parts = url.split("/");
		String dbname = parts[3];
		String[] parts2 = parts[2].split(":");
		String dbhost = parts2[0];
		String dbport = parts2[1];

		//Fill in the connection info to the PostGIS database
		Map<String, String> connectionInfo = new HashMap<String, String>();
		connectionInfo.put( "dbhost", dbhost );
		connectionInfo.put( "dbport", new Integer(dbport).toString() );
		connectionInfo.put( "dbname", dbname );
		connectionInfo.put( "dbusername", geoDb.getUser() );
		connectionInfo.put( "dbpassword", geoDb.getPwd() );	
		this.connectionInfo = connectionInfo;
		this.user = connectionInfo.get("dbusername");
		this.pwd = connectionInfo.get("dbpassword");
		this.url = "jdbc:postgresql://"+connectionInfo.get("dbhost")+":"+connectionInfo.get("dbport")+"/"+connectionInfo.get("dbname");
		getFeatureSourceInfo();
	}

	public void setGeodatabase(Geodatabase geoDb) throws PublishConfigurationException
	{
		this.url = geoDb.getUrl();
		this.user = geoDb.getUser();
		this.pwd = geoDb.getPwd();

		//Fill in the connection info to the PostGIS database
		Map<String, String> connectionInfo = new HashMap<String, String>();
		connectionInfo.put( "dbhost", geoDb.getDbHost() );
		connectionInfo.put( "dbport", geoDb.getDbPort().toString() );
		connectionInfo.put( "dbname", geoDb.getDbName() );
		connectionInfo.put( "dbusername", geoDb.getUser() );
		connectionInfo.put( "dbpassword", geoDb.getPwd() );	
		this.connectionInfo = connectionInfo;
		this.user = connectionInfo.get("dbusername");
		this.pwd = connectionInfo.get("dbpassword");
		this.url = "jdbc:postgresql://"+connectionInfo.get("dbhost")+":"+connectionInfo.get("dbport")+"/"+connectionInfo.get("dbname");
	}

	public void getFeatureSourceInfo() throws TransformationException, PublishConfigurationException {

		List<String>arguments = new ArrayList<String>();
		if(Utils.isWindows()){
			arguments.add("PG:\"dbname="+this.connectionInfo.get("dbname"));
			arguments.add("host="+this.connectionInfo.get("dbhost"));
			arguments.add("port="+this.connectionInfo.get("dbport"));
			arguments.add("user="+this.connectionInfo.get("dbusername"));
			arguments.add("password="+this.connectionInfo.get("dbpassword")+"\"");
		}else{
			arguments.add("PG:dbname="+this.connectionInfo.get("dbname")+" host="+this.connectionInfo.get("dbhost")+" port="+this.connectionInfo.get("dbport")+" user="+this.connectionInfo.get("dbusername")+" password="+this.connectionInfo.get("dbpassword"));
		}
		arguments.add(this.table);
		arguments.add("-summary");

		//String commandLine = Utils.getShellPrefix()+"ogr2ogr "+arguments+" "+sourceFileDir+mainFileName+" "+dataset;
		arguments.add(0, "-ro");
		arguments.add(0, "ogrinfo");
		if(Utils.isWindows()){
			//arguments.add(0, "/c");
			//arguments.add(0, "cmd");
		}else if(Utils.isUnix()){
			//arguments.add(0, "sh");
		}

		//starting the transformation process for all datasets found in the supplied file.
		List<String> resLines = runTransformProcess(arguments);
		if(resLines == null)
			throw new PublishConfigurationException("OGR getFeatureSourceInfo() has not been able to call the shell");
		if(resLines.size() == 0)
			throw new PublishConfigurationException("OGR getFeatureSourceInfo() the shell has been called but there is no line read");

		//Look for an error or a failure on the first line
		if(!resLines.get(0).contains("INFO:")){
			//get the content and send the exception
			StringBuilder sb = new StringBuilder();
			for(String line: resLines){
				sb.append(line+CRLF);		
			}
			throw new TransformationException(sb.toString());
		}

		//We get the content and set the values	
		int i = 0;
		for(String line: resLines){
			if(line.toUpperCase().contains("GEOMETRY:")){
				try{
					this.geometry = line.split(" ")[1];
				}catch(NullPointerException e){
					throw new TransformationException("The getFeatureSourceInfo() parser could not determine an attribute; did the format changed? ex:"+e.getMessage());
				}
			}

			if(line.toUpperCase().contains("FEATURE COUNT: ")){
				try{
					String tmp = line.toUpperCase().split("FEATURE COUNT: ")[1];
					this.featureCount = Integer.parseInt(tmp);
				}catch(NullPointerException e){
					throw new TransformationException("The getFeatureSourceInfo() parser could not determine an attribute; did the format changed? ex:"+e.getMessage());
				}
			}

			if(line.toUpperCase().contains("EXTENT: ")){
				try{
					String tmp = line.toUpperCase().split("EXTENT: ")[1];
					String[] pairs = tmp.split(" - ");
					String[] minPairs = pairs[0].split(", ");
					String[] maxPairs = pairs[1].split(", ");
					this.bbox.put("MinX_native", Double.parseDouble(minPairs[0].replace("(", "")));
					this.bbox.put("MinY_native", Double.parseDouble(minPairs[1].replace(")", "")));
					this.bbox.put("MaxX_native", Double.parseDouble(maxPairs[0].replace("(", "")));
					this.bbox.put("MaxY_native", Double.parseDouble(maxPairs[1].replace(")", "")));
				}catch(NullPointerException e){
					throw new TransformationException("The getFeatureSourceInfo() parser could not determine an attribute; did the format changed? ex:"+e.getMessage());
				}
			}

			//the last Autority is presumed to be the coord sys
			if(line.toUpperCase().contains("FID COLUMN = ")){
				try{
					//the line before should contain the authority
					if(resLines.get(i-1).toUpperCase().contains("AUTHORITY")){
						String[] tmp = resLines.get(i-1).toUpperCase().split("AUTHORITY");
						String[] tmp2 = tmp[1].split("\"");
						this.crsCode = tmp2[1]+":"+tmp2[3];
					} 
					else
						this.crsCode = null;
				}catch(NullPointerException e){
					throw new TransformationException("The getFeatureSourceInfo() parser could not determine an attribute; did the format changed? ex:"+e.getMessage());
				}
			}

			if(line.toUpperCase().contains("GEOMETRY COLUMN = ")){
				try{
					String[] tmp = line.split(" = ");
					this.geometryColumn = tmp[1];
					this.atrList.add(new Attribute(this.geometryColumn, this.geometry, "", true));

					//the next lines contain the attributes
					for(int j=1; j<resLines.size()-i; j++){
						line = resLines.get(i+j);
						tmp = line.split(" ");
						this.atrList.add(new Attribute(tmp[0].replace(":", ""), tmp[1], ""));
					}
				}catch(NullPointerException e){
					throw new TransformationException("The getFeatureSourceInfo() parser could not determine an attribute; did the format changed? ex:"+e.getMessage());
				}

				//We have all things
				break;
			}

			i++;
		}

		//Get the EPSG BBOX with sql request
		if(!this.crsCode.equalsIgnoreCase("EPSG:4326")){
			HashMap<String, Double> epsgBBOX = getEpsgBBox(this.table);
			this.bbox.put("MinX", epsgBBOX.get("MinX"));
			this.bbox.put("MinY", epsgBBOX.get("MinY"));
			this.bbox.put("MaxX", epsgBBOX.get("MaxX"));
			this.bbox.put("MaxY", epsgBBOX.get("MaxY"));
		}else{
			this.bbox.put("MinX", this.bbox.get("MinX_native"));
			this.bbox.put("MinY", this.bbox.get("MinY_native"));
			this.bbox.put("MaxX", this.bbox.get("MaxX_native"));
			this.bbox.put("MaxY", this.bbox.get("MaxY_native"));
		}
		
		//Check if the values are plausable (BBOX, EPSG)
		if(this.crsCode == null || this.crsCode.equalsIgnoreCase(""))
			throw new TransformationException("The coordinate system supplied is not valid for the dataset");
		if(this.bbox.get("MinX") == null)
			throw new TransformationException("The coordinate system supplied is not valid for the dataset");
		
	}

	public HashMap<String, Double> getEpsgBBox(String table) throws PublishConfigurationException{
		HashMap<String, Double> bbox = new HashMap<String, Double>();
		getConnection();
		try {
			String query= "SELECT extent(st_transform(wkb_geometry,4326)) as EXTENT FROM \""+table+"\"";
			ResultSet rs = statement.executeQuery(query);
			if(rs.next()){
				String extent = rs.getString("EXTENT");
				String[] pairs = extent.split(",");
				String[] minPairs = pairs[0].split(" ");
				String[] maxPairs = pairs[1].split(" ");
				bbox.put("MinX", Double.parseDouble(minPairs[0].replace("BOX(", "")));
				bbox.put("MinY", Double.parseDouble(minPairs[1]));
				bbox.put("MaxX", Double.parseDouble(maxPairs[0]));
				bbox.put("MaxY", Double.parseDouble(maxPairs[1].replace(")", "")));
				return bbox;
			}
			else
				throw new PublishConfigurationException("Table: "+table+" doesn't exit ");

		} catch (SQLException e) {
			logger.warning(e.getMessage());
			throw new PublishConfigurationException("Could access table: "+table+" cause:"+e.getMessage());
		}finally{
			closeConnection();
		}
	}

	private void getConnection() throws PublishConfigurationException{
		try
		{   
			//Load the JDBC driver and establish a connection. 
			Class.forName("org.postgresql.Driver");
			connection = DriverManager.getConnection(this.url, this.user, this.pwd);
			statement = connection.createStatement();

		}catch(Exception e){
			logger.warning(e.getMessage());
			throw new PublishConfigurationException("Could not connect to: "+this.url+" cause:"+e.getMessage());
		}
	}

	private void closeConnection(){
		try {
			if(rs != null)
				rs.close();
			if(statement != null)
				statement.close();
			if(connection != null)
				connection.close();
		} catch (SQLException e) {
			logger.warning(e.getMessage());
		}
	}

	private List<String> runTransformProcess(List<String> arguments) throws PublishConfigurationException {
		Process p = null;
		InputStream is = null;
		ProcessBuilder pb = null;
		List<String> resultLines = new ArrayList<String>();

		try {
			/*
			//this works on windows, if any problems, print out each arguments of args and arguments and compare
			String commandLine = Utils.getShellPrefix()+"ogrinfo -ro PG:\"dbname=postgis host=localhost port=5432 user=postgres password=rbago000''\" 7c6af010c68411dfa31f00238b529631 -summary";
			System.out.println(commandLine);
			String [] args = commandLine.split(" ");

			System.out.println("args:");
			for(String s: args)
			   System.out.println(s);

			System.out.println("");
			System.out.println("arguments:");
			for(String s: arguments)
				   System.out.println(s);
			 */

			pb = new ProcessBuilder(arguments);

			//pb = new ProcessBuilder(arguments);
			logger.info("Command is:"+commandToString(pb));
			pb.redirectErrorStream(true);
			p = pb.start();
			is = p.getInputStream();
			StringBuilder sb = new StringBuilder();
			Integer b;
			while ((b = is.read()) >= 0){
				String r = new String(new byte[] {b.byteValue()});
				sb.append(r);
				if(sb.toString().contains(CRLF)){
					resultLines.add(sb.toString().replace(CRLF, ""));
					sb.setLength(0);
				}
			}

			return resultLines;

		} catch (IOException e) {
			throw new PublishConfigurationException("Unable to run transformation process:"+
					"\nCommand " + commandToString(pb) + " reported " + e);
		} 
		finally{
			if(is != null)
				try {
					is.close();
				} catch (IOException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				if(p != null)
					p.destroy();
				pb = null;
		}
	}

	private static String commandToString(ProcessBuilder pb){
		List<String>args = pb.command();
		StringBuilder sb = new StringBuilder();
		for(String arg:args){
			sb.append(arg+" ");
		}
		return sb.toString();
	}

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

	public static void main(String[] args) {
		Map<String, String> connectionInfo = new HashMap<String, String>();
		connectionInfo.put( "dbname", "postgis" );
		connectionInfo.put( "dbhost", "localhost" );
		connectionInfo.put( "dbport", "5432" );
		connectionInfo.put( "dbusername", "postgres" );
		connectionInfo.put( "dbpassword", "rbago000''" );	

		try {

			//gg25_a
			PostgisFeatureSourceInfoOgr fsi = new PostgisFeatureSourceInfoOgr(connectionInfo ,"7c6af010c68411dfa31f00238b529631");
			fsi.printInfo(fsi);

			//texas traff
			fsi = new PostgisFeatureSourceInfoOgr(connectionInfo ,"01fe4e00508511dfb16100238b529631");
			fsi.printInfo(fsi);

			//chambres
			fsi = new PostgisFeatureSourceInfoOgr(connectionInfo ,"0eabdc806c2811df9b9b00238b529631");
			fsi.printInfo(fsi);

			//gpx
			fsi = new PostgisFeatureSourceInfoOgr(connectionInfo ,"15248e405ab211dfa03600238b529631");
			fsi.printInfo(fsi);


		} catch (TransformationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (PublishConfigurationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	private void printInfo(IFeatureSourceInfo fsi){
		//Put a breakpoint here and inspect the FeatureSource info.

		List<Attribute> lAtr = fsi.getAtrList();
		for(Attribute a : lAtr){
			System.out.println("name:"+a.getName()+", typeTxT:"+a.getTypeAsText()+", typeJava:"+a.getTypeName()+", is geometry:"+(a.isGeometry()?"true":"false"));
		}

		System.out.println("MinX_native : "+fsi.getBbox().get("MinX_native"));
		System.out.println("MinY_native :"+fsi.getBbox().get("MinY_native"));
		System.out.println("MaxX_native :"+fsi.getBbox().get("MaxX_native"));
		System.out.println("MaxY_native :"+fsi.getBbox().get("MaxY_native"));
		System.out.println("MinX: "+fsi.getBbox().get("MinX"));
		System.out.println("MinY :"+fsi.getBbox().get("MinY"));
		System.out.println("MaxX :"+fsi.getBbox().get("MaxX"));
		System.out.println("MaxY :"+fsi.getBbox().get("MaxY"));


		System.out.println("CRS code :"+fsi.getCrsCode());
		//System.out.println("CRS WKT :"+fsi.getCrsWkt());

		System.out.println("end");
	}

}
