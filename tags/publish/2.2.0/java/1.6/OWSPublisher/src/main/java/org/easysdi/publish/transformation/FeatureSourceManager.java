package org.easysdi.publish.transformation;

import java.io.File;
import java.io.IOException;
import java.io.InputStream;
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
import org.easysdi.publish.util.Utils;

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


	public void manageDataset( ProcessletExecutionInfo info, String featureSourceId, String featuresourceGuid, String diffusorName, List<String> URLs,
			String ScriptName, String sourceDataType, String epsgProj, String dataset, String currentUser){

		RunnableDatasetTransformer rdst = new RunnableDatasetTransformer();

		// Create the thread supplying it the runnable object

		rdst.init(info, featureSourceId,  diffusorName,  URLs,
				ScriptName,  sourceDataType,  epsgProj, dataset, transMap, featuresourceGuid, currentUser);

		// Start the thread transformation process
		rdst.run();
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
			IHelper helper = geoDb.getHelper();

			String postgisOutputTableName = featureSourceId.replace("-", "");

			helper.setGeodatabase(geoDb);
			helper.dropTable(postgisOutputTableName);
		}catch( Exception e)
		{
			e.printStackTrace();
			throw new DiffuserNotFoundException( e.getMessage() );
		}

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
