package eu.bauel.publish.transformation;

import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.PrintStream;
import java.io.Reader;
import java.io.Writer;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.nio.charset.Charset;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import ch.depth.services.wps.WPSServlet;

import com.eaio.uuid.UUID;

import eu.bauel.publish.Utils;
import eu.bauel.publish.exception.DataSourceNotFoundException;
import eu.bauel.publish.exception.DatabaseForDiffuserNotfoundException;
import eu.bauel.publish.exception.DatabaseUriException;
import eu.bauel.publish.exception.DiffuserException;
import eu.bauel.publish.exception.FeatureSourceException;
import eu.bauel.publish.exception.FeatureSourceNotFoundException;
import eu.bauel.publish.exception.IncompatibleUpdateFeatureSourceException;
import eu.bauel.publish.exception.PublishConfigurationException;
import eu.bauel.publish.exception.PublishGeneralException;
import eu.bauel.publish.exception.TransformationException;
import eu.bauel.publish.helper.Attribute;
import eu.bauel.publish.helper.IFeatureSourceInfo;
import eu.bauel.publish.helper.IHelper;
import eu.bauel.publish.persistence.FeatureSourceStatus;
import eu.bauel.publish.persistence.Featuresource;
import eu.bauel.publish.persistence.Geodatabase;

public class RunnableDatasetTransformer implements Runnable{

	Logger logger = Logger.getLogger("eu.bauel.publish.transformation.RunnableDatasetTransformer");
	String tempFileDir = "";
	Calendar cal = Calendar.getInstance();

	String scriptName;
	String featureSourceId;
	String diffusorName; 
	List<String> URLs;
	String ScriptName;
	String sourceDataType;
	String epsgProj;
	String dataset; 
	TransformDatasetResponse resp;
	Map<String, ITransformerAdapter> transMap;
	String featuresourceGuid = "";

	public void init(String scriptName, String featureSourceId, String diffusorName, List<String> URLs,
			String ScriptName, String sourceDataType, String epsgProj, String dataset, Map<String, ITransformerAdapter> transMap, String featuresourceGuid) {

		this.scriptName = scriptName;
		this.featureSourceId = featureSourceId;
		this.diffusorName = diffusorName;
		this.URLs = URLs;
		this.ScriptName = ScriptName;
		this.sourceDataType = sourceDataType;
		this.epsgProj = epsgProj;
		this.dataset = dataset;
		this.transMap = transMap;
		this.featuresourceGuid = featuresourceGuid;
	}

	@Override
	public void run()  {
		manageDataset(  scriptName,  featureSourceId,  diffusorName,  URLs,
				ScriptName,  sourceDataType,  epsgProj, dataset, featuresourceGuid);
	}

	public void manageDataset( String scriptName, String featureSourceId, String diffusorName, List<String> URLs,
			String ScriptName, String sourceDataType, String epsgProj, String dataset, String featuresourceGuid){

		TransformDatasetResponse response;
		IHelper helper = null;
		Boolean isUpdate = false;
		//The output table in Postgis
		String postgisOutputTableName;
		SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
		String strDbTypeClass = "";
		Geodatabase geoDb = null;
		String existingFsTable="";
		Featuresource fs = new Featuresource();
		Date dt = null;		

		try
		{

			//Look for the target db of the diffuser to write data
			logger.info("diffusorName"+diffusorName);
			geoDb = Geodatabase.getGeodB( diffusorName );

			if(geoDb == null)
				throw new DatabaseForDiffuserNotfoundException("No database found for diffuser: "+diffusorName);

			fs.setDiffuserId(geoDb.getId());
			fs.setScriptName(ScriptName);
			fs.setSourceDataType(sourceDataType);
			dt = cal.getTime();

			//Determine if we have an update or a new table to create
			if(featureSourceId.equals("none")){
				postgisOutputTableName = featuresourceGuid.replace("-", "");
				logger.info("create:"+featuresourceGuid);
				//create a new feature source
				fs.setFeatureGUID(featuresourceGuid);		
				fs.setTableName(postgisOutputTableName);
				//Set creation date only if new
				fs.setCreation_date(dt);
				fs.setStatus(FeatureSourceStatus.CREATING);

				//store the new feature source
				if(fs.store() == false){
					System.out.println("Error occured storing fs, cause:"+fs.errorCause +" message:"+ fs.errorMessage);
				}

				//set the fs id, so that next time it is readyy for update.
				fs.setId(Featuresource.getIdFromGuid(featuresourceGuid));

			}
			//we keep the existing featureSourceId
			else{
				isUpdate = true;
				postgisOutputTableName = featureSourceId.replace("-", "")+"_temp";
				//existing featureSource, retrieve id from Guid
				fs.setId(Featuresource.getIdFromGuid(featureSourceId));
				fs.setFeatureGUID(featuresourceGuid);
				fs.setTableName(postgisOutputTableName);
				fs.setUpdate_date(dt);
				fs.setStatus(FeatureSourceStatus.UPDATING);

				//store the new feature source
				if(fs.store() == false){
					System.out.println("Error occured storing fs, cause:"+fs.errorCause +" message:"+ fs.errorMessage);
				}

				logger.info("update:"+featuresourceGuid);
			}

			//Retrieve locally the supplied files for transformation. They go into the OS Java temp
			//folder
			tempFileDir = Utils.writeHttpGetToFileSystem(featuresourceGuid, URLs);

			//Inputs from Interface + GUI:
			logger.info("diffusor name"+diffusorName);
			for(String url : URLs)
				logger.info("input files: "+url);  

			logger.info("ScriptName is:"+ScriptName);
			logger.info("sourceDataType is:"+sourceDataType);

			//get the helper accordingly to the geodatabase to drop the table if it exists.
			helper = null;
			logger.info("geodb_name"+geoDb.getName());
			logger.info("geodb_type"+geoDb.getType());
			strDbTypeClass = geoDb.getType().substring(0, 1).toUpperCase() +  geoDb.getType().substring(1);
			logger.info("strDbTypeClass:"+strDbTypeClass+" type:"+geoDb.getType()+" url:"+geoDb.getUrl());  
			helper = (IHelper) Class.forName("eu.bauel.publish."+geoDb.getType()+"helper."+strDbTypeClass+"Helper").newInstance();
			helper.setGeodatabase(geoDb);
			helper.dropTable(postgisOutputTableName);

			//find the transformer and launch the script
			//get the transformer accordingly to the scriptName or source data type
			transMap.put(featuresourceGuid, getTransformerAdapter(scriptName, sourceDataType));
			transMap.get(featuresourceGuid).setLocation( WPSServlet.m_executionPath + WPSServlet.PLUGIN_SUBDIR );

			//Retrieve host, port from the GeoDB's URL.

			String url = geoDb.getUrl();
			String[] parts = url.split("/");
			if(parts.length != 4)
				throw new DatabaseUriException("Malformed bd URI:"+url);
			String dbname = parts[3];
			String[] parts2 = parts[2].split(":");
			if(parts2.length != 2)
				throw new DatabaseUriException("Malformed bd URI:"+url);
			String dbhost = parts2[0];
			String dbport = parts2[1];

			//Transform the dataset
			transMap.get(featuresourceGuid).transformDataset( postgisOutputTableName, tempFileDir, URLs, dbhost, dbport, dbname,
					geoDb.getUsername(), geoDb.getPassword(), geoDb.getSchema(), epsgProj, dataset);

			//Check that the feature source is ok.

			//Check if the created Fs is ok because an invalid projection may have been supplied.
			//In this case the Feature Source remains unreadable and the fsi will throws an exception.

			IFeatureSourceInfo existingFeatureInfo = null;
			IFeatureSourceInfo candidateFeatureInfo = null;
			existingFsTable =  postgisOutputTableName.split("_")[0];

			//Get a class instance of the class to instanciate.
			Class cl=Class.forName("eu.bauel.publish."+geoDb.getType()+"helper."+strDbTypeClass+"FeatureSourceInfo");
			//Get a class instance for the method signature. Here void main(String[])
			Class arg1type = Geodatabase.class;
			Class arg2type = String.class;
			Constructor constructor = cl.getConstructor(arg1type, arg2type);
			//load information of the created fs
			candidateFeatureInfo = (IFeatureSourceInfo)constructor.newInstance((Object)geoDb, (Object)postgisOutputTableName);
			if(isUpdate){
				//load information of the current fs
				//postgisOutputTableName is a temporary table with guid and _temp at the end
				existingFeatureInfo=(IFeatureSourceInfo)constructor.newInstance((Object)geoDb, (Object)existingFsTable);
			}



			/* If it's an update, we must compare that the attributes, geometry and coordinate system 
			 * are the same that actual*/
			if(isUpdate){
				//the fs in base: createdFeatureInfo
				//the fs candidate: candidateFeatureInfo
				//do the checks:
				logger.info("Start updating the feature");
				logger.info("Existing table name:"+existingFeatureInfo.getTable());
				logger.info("Existing table epsg code:"+existingFeatureInfo.getCrsCode());
				logger.info("Existing table geometry:"+existingFeatureInfo.getGeometry());
				logger.info("Existing table atrlist size:"+existingFeatureInfo.getAtrList().size());

				logger.info("candidate table:"+candidateFeatureInfo.getTable());
				logger.info("candidate table name:"+candidateFeatureInfo.getTable());
				logger.info("candidate table epsg code:"+candidateFeatureInfo.getCrsCode());
				logger.info("candidate table geometry:"+candidateFeatureInfo.getGeometry());
				logger.info("candidate table atrlist size:"+candidateFeatureInfo.getAtrList().size());

				//Check the geometry
				if(!existingFeatureInfo.getGeometry().equals(candidateFeatureInfo.getGeometry()))
					throw new IncompatibleUpdateFeatureSourceException("The geometry:"+candidateFeatureInfo.getGeometry()+" does not match with the existing Feature Source:"+existingFeatureInfo.getGeometry());
				//Check the crs
				if(!existingFeatureInfo.getCrsCode().equals(candidateFeatureInfo.getCrsCode()))
					throw new IncompatibleUpdateFeatureSourceException("The Crs code:"+candidateFeatureInfo.getCrsCode()+" does not match with the existing Feature Source:"+existingFeatureInfo.getCrsCode());

				//check the attributes.
				//first compare the number of attributes match, it must be so.
				if(existingFeatureInfo.getAtrList().size() != candidateFeatureInfo.getAtrList().size())
					throw new IncompatibleUpdateFeatureSourceException("The number of attributes:"+candidateFeatureInfo.getAtrList().size()+" does not match with the existing Feature Source:"+existingFeatureInfo.getAtrList().size());

				List<Attribute>existingFeatureInfoAtrList = existingFeatureInfo.getAtrList();
				List<Attribute>candidateFeatureInfoAtrList = candidateFeatureInfo.getAtrList();
				for(int i = 0; i<existingFeatureInfo.getAtrList().size(); i++){
					//second check the types that must also match
					if(!existingFeatureInfoAtrList.get(i).getType().equals(candidateFeatureInfoAtrList.get(i).getType()))
						throw new IncompatibleUpdateFeatureSourceException("The type:"+candidateFeatureInfoAtrList.get(i).getType().getName()+" of attribute does not match with the existing Feature Source:"+existingFeatureInfoAtrList.get(i).getType().getName());
				}

				//third, we won't compare names because of the aliases mechanism
				//we assume now the fs is compatible and we rename the attributes
				Map<String,String> mpColumns = new HashMap<String,String>(); 

				for(int i = 0; i<existingFeatureInfo.getAtrList().size(); i++){
					//take care to not rename with the same name.
					if(!candidateFeatureInfoAtrList.get(i).getName().equals(existingFeatureInfoAtrList.get(i).getName()))
						mpColumns.put(candidateFeatureInfoAtrList.get(i).getName(), existingFeatureInfoAtrList.get(i).getName());
					logger.info("Adding to the map:"+candidateFeatureInfoAtrList.get(i).getName()+" = "+ existingFeatureInfoAtrList.get(i).getName());
				}

				logger.info("Setting aliases like it was.");

				//set the alias name for the candidate table
				helper.setAttributeAliases(postgisOutputTableName, mpColumns);

				logger.info("Dropping the existing table:"+existingFsTable);

				//We drop the old table and replace with the temp table
				helper.dropTable(existingFsTable);

				logger.info("Renaming the feature");

				//Rename the table
				helper.renameTable(postgisOutputTableName, existingFsTable);

				//We give the good name to postgisOutputTableName
				postgisOutputTableName = existingFsTable;

				logger.info("finished updating the feature");

			}


			//
			//Build a FeatureSource and save it
			//
			/*
			Featuresource fs = new Featuresource();
			if(!featureSourceId.equals("none")){
				//existing featureSource, retrieve id from Guid
				fs.setId(Featuresource.getIdFromGuid(featureSourceId));
			}
			fs.setFeatureGUID(featuresourceGuid);
			fs.setDiffuserId( geoDb.getId() );
			fs.setTableName(postgisOutputTableName);
			fs.setScriptName(ScriptName);
			fs.setSourceDataType(sourceDataType);
			Calendar cal = Calendar.getInstance();
			Date dt = cal.getTime();
			//Set creation date only if new
			if(featureSourceId.equals("none")){
				fs.setCreation_date(dt);
			}
			fs.setUpdate_date(dt);

			//store the new feature source
			if(fs.store() == false){
				System.out.println("Error occured storing fs, cause:"+fs.errorCause +" message:"+ fs.errorMessage);
			}
			 */

			//store the new feature source
			fs.setStatus(FeatureSourceStatus.AVAILABLE);
			dt = cal.getTime();
			fs.setUpdate_date(dt);
			//store the new feature source
			//Store in finally close

			//Build the response
			response = new TransformDatasetResponse();
			response.featuresourceGuid = featuresourceGuid;

			//get the attributes of the table
			List<String> atrLst = helper.getColumnNameFromTable(postgisOutputTableName);
			StringBuilder stb = new StringBuilder();
			int i = 0;
			for(String attr : atrLst){
				if(i < (atrLst.size() - 1))
					stb.append(attr + ",");
				else
					stb.append(attr);
				i++;
			}
			fs.setFieldsName(stb.toString());

			fs.setCrsCode(candidateFeatureInfo.getCrsCode());


			//Delete the temp files
			File dir = new File(tempFileDir);    
			boolean success = deleteDirectory(dir);
			if (success) {
				logger.info("Directory: " + tempFileDir + " cleaned");
			}
			else{
				logger.info("Directory: " + tempFileDir + " not cleaned");
			}

		}
		//writeHttpGetToFileSystem
		catch (MalformedURLException e) {
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_DATASOURCE_URL_NOT_VALID", e);
			e.printStackTrace();
		}
		//Helper OR e.FeatureSourceInfo
		catch (InstantiationException e) {
			//Either the Helper or the FSI could not be found
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_MISSING_HELPER_OR_FSI", e);
			e.printStackTrace();
		} 
		//IHelper OR FeatureSourceInfo
		catch (IllegalAccessException e){
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_MISSING_HELPER_OR_FSI", e);
			//Either the Helper or the FSI could not be found 
			e.printStackTrace();
		}
		//FeatureSourceInfo
		catch (SecurityException e) {
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_SECURITY_EXCEPTION", e);
			System.out.println(e.getMessage());
		} 
		//FeatureSourceInfo
		catch (NoSuchMethodException e){
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_NO_SUCH_METHOD", e);
			System.out.println(e.getMessage());
		}
		//FeatureSourceInfo
		catch (InvocationTargetException e){
			//This is the interesting exception. It tells that an exception
			//has occured in the constructor of the instanciated class. in other
			//words, the fsi was unable to read the Feature Source. It's mainly
			//because of a invalid coordinate system  or a configuration exception.
			//We must cancel this fs and give
			//the user a feedback.

			//Clear the Feature Source from the geodatabase.
			System.out.println("clear the table:"+existingFsTable);
			try {
				helper.dropTable(existingFsTable);
			} catch (PublishConfigurationException e1) {
				setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_CONFIGURATION_EXCEPTION", e1);
			}

			//catch the wrapped exception from constructor
			System.out.println(e.getTargetException().getMessage());
			if(e.getTargetException().getClass() == TransformationException.class){
				System.out.println("****TRANSFORMATION EXCEPTION****");
				setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_BAD_COORDINATE_SYSTEM", e);
			}
			else if(e.getTargetException().getClass() == PublishConfigurationException.class){
				setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_CONFIGURATION_EXCEPTION", e);
			}
			else{
				setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_CONFIGURATION_EXCEPTION", e);
			}
		} 
		//IHelper
		catch (ClassNotFoundException e) {
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_MISSING_HELPER_OR_FSI", e);
			System.out.println(e.getMessage());
			logger.warning("You must provide this helper class for the DB:"+strDbTypeClass + 
					" this class:"+strDbTypeClass+"FeatureSourceInfo"+" must implement: eu.bauel.publish.helper.FeatureSourceInfo");
		}
		//Geodatabase
		catch(DatabaseForDiffuserNotfoundException e){
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_DATABASE_FOR_DIFFUSER_NOT_FOUND", e);
		}
		//Geodatabase
		catch(DatabaseUriException e){
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_DATABASE_WRONG_URI", e);
		}
		//FeatureSource
		catch(IncompatibleUpdateFeatureSourceException e){
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_INCOMPATIBLE_FEATURE_SOURCE", e);
		}
		//writeHttpGetToFileSystem
		catch (IOException e) {
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_DATASOURCE_NOT_FOUND", e);
		}
		catch (PublishConfigurationException e) {
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_CONFIGURATION_EXCEPTION", e);
		}
		catch (TransformationException e) {
			if(e instanceof eu.bauel.publish.exception.DataSourceNotFoundException)
				setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_DATASOURCE_NOT_FOUND", e);
			else if(e instanceof eu.bauel.publish.exception.DataSourceWrongFormatException)
				setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_DATASOURCE_WRONG_FORMAT", e);
			else if(e instanceof eu.bauel.publish.exception.ScriptNotFoundException)
				setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR_SCRIPT_NOT_FOUND", e);
			else
				setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_ERROR", e);
		}
		catch (FeatureSourceNotFoundException e) {
			setFsStatus(fs, FeatureSourceStatus.UNAVAILABLE, "EASYSDI_PUBLISH_WPS_FEATURESOURCE_NOT_FOUND_ERROR", e);
		}
		catch (Exception e) {
			e.printStackTrace();
		}
		finally{
			
			//Do not go above 1000 char for an exception message
			if(fs.getExcMessage() != null && fs.getExcMessage().length() > 999)
				fs.setExcMessage(fs.getExcMessage().substring(0, 999).replace("\'", "\\'"));
			//store the FS
			if(fs.store() == false){
				System.out.println("Error occured storing fs, cause:"+fs.errorCause +" message:"+ fs.errorMessage);
			}
			//set progress to 100%
			if(transMap == null)
				logger.warning("transMap is null, un exception occured before transMap.put in this file.");				
			transMap.get(featuresourceGuid).setProgress(100f);
			//unregister transformer
			transMap.remove(featuresourceGuid);
		}
	}

	private ITransformerAdapter getTransformerAdapter(String scriptName, String sourceDataType) throws PublishConfigurationException{
		ITransformerAdapter transformer = null;
		if( !scriptName.equals( "none" ) )
		{
			if( TransformerHelper.getTransformerPlugIns().contains( scriptName ) )
			{
				logger.info( scriptName + " is instantiated" );
				transformer = TransformerHelper.transformerFactory( scriptName );
			}
			else
			{
				logger.info( scriptName + " does not exists" );
			}
		}

		//Is there a plug-in corresponding to a specific File Type (e.g. shape)?
		if( !sourceDataType.equals( "none" ) )
		{
			if( TransformerHelper.getFileTypeToTransformerAssociation().keySet().contains( sourceDataType ) )
			{
				String plugInName = TransformerHelper.getFileTypeToTransformerAssociation(). get(sourceDataType);
				logger.info( plugInName + " is instantiated in order to transform a " + sourceDataType );
				transformer = TransformerHelper.transformerFactory( plugInName );
			}
			else
			{
				logger.info( "A plug in for " + sourceDataType + " does not exists" );
			}			
		}
		//If no transformer was found, throw an exception
		if( null == transformer )
		{
			logger.info( "no transformer was found for script: "+scriptName+" or source data type:" + sourceDataType);
			throw new PublishConfigurationException( "no transformer was found for script: "+scriptName+" or source data type:" + sourceDataType );
		}
		return transformer;
	}

	private boolean deleteDirectory(File path) {
		if( path.exists() ) {
			File[] files = path.listFiles();
			for(int i=0; i<files.length; i++) {
				if(files[i].isDirectory()) {
					deleteDirectory(files[i]);
				}
				else {
					files[i].delete();
				}
			}
		}
		return( path.delete() );
	}

	private void setFsStatus(Featuresource fs, FeatureSourceStatus s, String detail, Throwable t){
		fs.setStatus(FeatureSourceStatus.UNAVAILABLE);
		Date dt = cal.getTime();
		fs.setUpdate_date(dt);
		fs.setExcDetail(detail);
		fs.setExcMessage(Utils.getStackTrace(t));
	}

}
