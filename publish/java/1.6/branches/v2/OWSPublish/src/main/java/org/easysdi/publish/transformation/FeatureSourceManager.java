package org.easysdi.publish.transformation;

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

import org.deegree.services.wps.ProcessletExecutionInfo;
import org.easysdi.publish.Utils;
import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.layer.FeatureSource;
import org.easysdi.publish.biz.layer.Layer;
import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.DataSourceNotFoundException;
import org.easysdi.publish.exception.DatabaseForDiffuserNotfoundException;
import org.easysdi.publish.exception.DatabaseUriException;
import org.easysdi.publish.exception.DiffuserException;
import org.easysdi.publish.exception.DiffuserNotFoundException;
import org.easysdi.publish.exception.FeatureSourceException;
import org.easysdi.publish.exception.FeatureSourceNotFoundException;
import org.easysdi.publish.exception.IncompatibleUpdateFeatureSourceException;
import org.easysdi.publish.exception.LayerExistingForFeatureSourceException;
import org.easysdi.publish.exception.NoSuchTransformerForFeatureSourceGuid;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.PublishGeneralException;
import org.easysdi.publish.exception.TransformationException;
import org.easysdi.publish.helper.Attribute;
import org.easysdi.publish.helper.IFeatureSourceInfo;
import org.easysdi.publish.helper.IHelper;

//import ch.depth.services.wps.WPSServlet;

import com.eaio.uuid.UUID;


public class FeatureSourceManager {

	Logger logger = Logger.getLogger("org.easysdi.publish.transformation.FeatureSourceManager");
	String tempFileDir = "";
	Calendar cal = Calendar.getInstance();
	Map<String, ITransformerAdapter> transMap = new HashMap<String, ITransformerAdapter>();
	static FeatureSourceManager fsm = null;

	public static FeatureSourceManager getInstance(){
		if(fsm == null)
			fsm = new FeatureSourceManager();
		return fsm;
	}


	public String manageDataset( ProcessletExecutionInfo info, String featureSourceId, String diffusorName, List<String> URLs,
			String ScriptName, String sourceDataType, String epsgProj, String dataset){

		String featuresourceGuid;

		if(featureSourceId.equals("none")){
			UUID uuid = new UUID();
			featuresourceGuid = uuid.toString();
		}
		else{
			featuresourceGuid = featureSourceId;
		}

		RunnableDatasetTransformer rdst = new RunnableDatasetTransformer();

		// Create the thread supplying it the runnable object

		rdst.init(info, featureSourceId,  diffusorName,  URLs,
				ScriptName,  sourceDataType,  epsgProj, dataset, transMap, featuresourceGuid);

		// Start the thread transformation process
		rdst.run();

		//return response to the client
		return featuresourceGuid;
	}

	public void deleteFeatureSource(String fsId) throws PublishGeneralException, PublishConfigurationException, DiffuserException, FeatureSourceException{
		FeatureSource fs =  FeatureSource.getFromGuid(fsId); 

		if( null == fs)
		{
			throw new FeatureSourceNotFoundException("No FeatureSource with guid found: " + fsId);
		}

		if(Layer.isLayerBoundToFeatureSource(fs))
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

	private void removeDataset(String featureSourceId, String diffuserName )
	throws PublishGeneralException, PublishConfigurationException, DiffuserException, FeatureSourceException {

		try {
			//Look for the target db of the diffuser to write data
			Diffuser diff = Diffuser.getFromIdString(diffuserName);
			Geodatabase geoDb = diff.getGeodatabase();

			//get the helper accordingly to the geodatabase and drop the table if it exists.
			IHelper helper = null;
			String strDbTypeClass = geoDb.getGeodatabaseType().getName().substring(0, 1).toUpperCase() +  geoDb.getGeodatabaseType().getName().substring(1);
			try {
				helper = (IHelper) Class.forName("org.easysdi.publish."+geoDb.getGeodatabaseType().getName()+"helper."+strDbTypeClass+"Helper").newInstance();
			} catch (InstantiationException e) {
				e.printStackTrace();
				throw new DataSourceNotFoundException( e.getMessage() );
			} catch (IllegalAccessException e) {
				e.printStackTrace();
				throw new DataSourceNotFoundException( e.getMessage() );
			} catch (ClassNotFoundException e) {
				logger.warning("You must provide this helper class for the DB:"+strDbTypeClass + 
						" this class:"+strDbTypeClass+"Helper"+" must implement: org.easysdi.publish.helper.Helper");
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

	/*
	private void setFsStatus(FeatureSource fs, String detail, Throwable t){
		fs.setStatus("UNAVAILABLE");
		fs.setUpdateDate(cal);
		fs.setExcDetail(detail);
		fs.setExcMessage(Utils.getStackTrace(t));
	}
    */
	
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
