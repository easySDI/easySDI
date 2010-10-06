package eu.bauel.publish.transformation;

import java.io.File;
import java.io.IOException;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.io.Writer;
import java.lang.reflect.Constructor;
import java.lang.reflect.InvocationTargetException;
import java.net.MalformedURLException;
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
import eu.bauel.publish.exception.DataInputException;
import eu.bauel.publish.exception.DataSourceNotFoundException;
import eu.bauel.publish.exception.DatabaseForDiffuserNotfoundException;
import eu.bauel.publish.exception.DatabaseUriException;
import eu.bauel.publish.exception.DiffuserException;
import eu.bauel.publish.exception.DiffuserNotFoundException;
import eu.bauel.publish.exception.FeatureSourceException;
import eu.bauel.publish.exception.FeatureSourceNotFoundException;
import eu.bauel.publish.exception.IncompatibleUpdateFeatureSourceException;
import eu.bauel.publish.exception.LayerExistingForFeatureSourceException;
import eu.bauel.publish.exception.NoSuchTransformerForFeatureSourceGuid;
import eu.bauel.publish.exception.PublishConfigurationException;
import eu.bauel.publish.exception.PublishGeneralException;
import eu.bauel.publish.exception.TransformationException;
import eu.bauel.publish.helper.Attribute;
import eu.bauel.publish.helper.IFeatureSourceInfo;
import eu.bauel.publish.helper.IHelper;
import eu.bauel.publish.persistence.FeatureSourceStatus;
import eu.bauel.publish.persistence.Featuresource;
import eu.bauel.publish.persistence.Geodatabase;
import eu.bauel.publish.persistence.Layer;

public class FeatureSourceManager {

	Logger logger = Logger.getLogger("eu.bauel.publish.transformation.FeatureSourceManager");
	String tempFileDir = "";
	Calendar cal = Calendar.getInstance();
	Map<String, ITransformerAdapter> transMap = new HashMap<String, ITransformerAdapter>();

	public String manageDataset( String scriptName, String featureSourceId, String diffusorName, List<String> URLs,
			String ScriptName, String sourceDataType, String epsgProj, String dataset){

		RunnableDatasetTransformer rdst = new RunnableDatasetTransformer();
		String featuresourceGuid;
		// Create the thread supplying it the runnable object
		Thread thread = new Thread(rdst);

		if(featureSourceId.equals("none")){
			UUID uuid = new UUID();
			featuresourceGuid = uuid.toString();
		}
		else{
			featuresourceGuid = featureSourceId;
		}

		rdst.init(  scriptName,  featureSourceId,  diffusorName,  URLs,
				ScriptName,  sourceDataType,  epsgProj, dataset, transMap, featuresourceGuid);

		// Start the thread transformation process
		thread.start();
		
		//return response to the client
		return featuresourceGuid;
	}

	public void deleteFeatureSource(String fsId) throws PublishGeneralException, PublishConfigurationException, DiffuserException, FeatureSourceException{
		Featuresource fs =  new Featuresource( Featuresource.getIdFromGuid( fsId)); 

		if( null == fs)
		{
			throw new FeatureSourceNotFoundException("No FeatureSource with guid found: " + fsId);
		}

		if( Layer.isALayerAttachedToThisFeatureSource( fs.getFeatureGUID() ) )
		{
			//Can't erase because one or more layer depends on it, they need to be removed first
			throw new LayerExistingForFeatureSourceException("remove attached layer(s) for Feature Source first");
		}

		//get the transformer
		//check that this type of file can be handled by any transfomator that has been plugged in
		//TransformerHelper.getTransformerPlugIns();
		//TransformerHelper.getFileTypeToTransformerAssociation();

		//Is there a plugin corresponding to a specific script or format?
		String scriptName = fs.getScriptName();
		String sourceDataType = fs.getSourceDataType();

		//get the transformer accordingly to the scriptName or source data type
		//transformer = getTransformerAdapter(scriptName, sourceDataType);

		//transformer.setLocation( m_executionPath + PLUGIN_SUBDIR );

		//remove the dataset
		this.removeDataset(fs.getTableName(), fs.getDiffuser().getName() );

		//delete the Feature Source
		fs.delete();
	}

	private void removeDataset(String featureSourceId, String diffusorName )
	throws PublishGeneralException, PublishConfigurationException, DiffuserException, FeatureSourceException {

		try {
			//Look for the target db of the diffuser to write data
			Geodatabase geoDb = Geodatabase.getGeodB( diffusorName );

			//get the helper accordingly to the geodatabase and drop the table if it exists.
			IHelper helper = null;
			String strDbTypeClass = geoDb.getType().substring(0, 1).toUpperCase() +  geoDb.getType().substring(1);
			try {
				helper = (IHelper) Class.forName("eu.bauel.publish."+geoDb.getType()+"helper."+strDbTypeClass+"Helper").newInstance();
			} catch (InstantiationException e) {
				e.printStackTrace();
				throw new DataSourceNotFoundException( e.getMessage() );
			} catch (IllegalAccessException e) {
				e.printStackTrace();
				throw new DataSourceNotFoundException( e.getMessage() );
			} catch (ClassNotFoundException e) {
				logger.warning("You must provide this helper class for the DB:"+strDbTypeClass + 
						" this class:"+strDbTypeClass+"Helper"+" must implement: eu.bauel.publish.helper.Helper");
				e.printStackTrace();
				throw new DiffuserNotFoundException( e.getMessage() );
			}

			String postgisOutputTableName = featureSourceId.replace("-", "");

			helper.setGeodatabase(geoDb);
			helper.dropTable(postgisOutputTableName);
		}catch( Exception e)
		{
			e.printStackTrace();
			throw new DiffuserNotFoundException( e.getMessage() );
		}

	}

	private boolean writeHttpGetToFileSystem(List<String> URLs)
	throws MalformedURLException, IOException, DataSourceNotFoundException, TransformationException, PublishConfigurationException {
		int count = 0;
		UUID uuid = new UUID();
		tempFileDir = System.getProperty("java.io.tmpdir")+"/"+uuid.toString()+"/";
		logger.info("Temp file dir is: " + tempFileDir);
		boolean success = (new File(tempFileDir)).mkdir();
		if (success) {
			logger.info("Directory: " + tempFileDir + " created");
		}
		else{
			logger.info("Directory: " + tempFileDir + " not created");
		}

		for (String strUrl : URLs) {
			BinaryIn  in  = new BinaryIn(strUrl.replace(" ", "%20"));
			String[] temp = strUrl.split("/");
			BinaryOut out = new BinaryOut(tempFileDir+temp[temp.length-1]);
			// read one 8-bit char at a time
			while (!in.isEmpty()) {
				char c = in.readChar();
				out.write(c);
			}
			out.flush();
			out.close();
			in.close();
		}
		System.out.println("tempdir:"+tempFileDir);
		return true;
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

	private void setFsStatus(Featuresource fs, FeatureSourceStatus s, String detail, Throwable t){
		fs.setStatus(FeatureSourceStatus.UNAVAILABLE);
		Date dt = cal.getTime();
		fs.setUpdate_date(dt);
		fs.setExcDetail(detail);
		fs.setExcMessage(Utils.getStackTrace(t));
	}

	public float getProgressForFeatureSource(String guid) throws NoSuchTransformerForFeatureSourceGuid{
		float progress = 0f;
		ITransformerAdapter ta = transMap.get(guid);
		if(ta != null){
			return ta.getProgress();
		}
		else
		{
			throw new NoSuchTransformerForFeatureSourceGuid("No transformer for FeatureSource guid:"+guid);
		}
	}
}
