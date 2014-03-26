package org.easysdi.publish.gui.wps.provider;

import java.util.ArrayList;
import java.util.List;

import org.apache.commons.codec.binary.Base64;
import org.deegree.services.controller.OGCFrontController;
import org.deegree.services.wps.Processlet;
import org.deegree.services.wps.ProcessletException;
import org.deegree.services.wps.ProcessletExecutionInfo;
import org.deegree.services.wps.ProcessletInputs;
import org.deegree.services.wps.ProcessletOutputs;
import org.deegree.services.wps.input.ComplexInput;
import org.deegree.services.wps.input.LiteralInput;
import org.deegree.services.wps.input.ProcessletInput;
import org.deegree.services.wps.output.LiteralOutput;
import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.exception.TransformationException;
import org.easysdi.publish.security.CurrentUser;
import org.easysdi.publish.transformation.FeatureSourceManager;
import org.easysdi.publish.transformation.TransformerHelper;
import org.easysdi.publish.util.BinaryIn;
import org.easysdi.publish.util.BinaryOut;
import org.easysdi.publish.util.Utils;

import java.io.File;
import java.io.FileInputStream;
import java.io.InputStream;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.security.core.context.SecurityContextHolder;

import com.eaio.uuid.UUID;


public class TransformDatasetProcesslet implements Processlet{

	private static final Logger logger = LoggerFactory.getLogger(TransformDatasetProcesslet.class);

	//the location to the transformer plug-in directory
	public static final String PLUGIN_SUBDIR = "WEB-INF/transformerPlugIns/";
	//Execution path for the transformer plug-in.
	private String m_executionPath;
	private FeatureSourceManager fsm;

	@Override
	public void destroy() {}

	@Override
	public void init() {
	
		try{
			m_executionPath = OGCFrontController.getInstance().getServletConfig().getServletContext().getRealPath("/");

			fsm = FeatureSourceManager.getInstance();

			logger.info("init helper" + m_executionPath + PLUGIN_SUBDIR);

			TransformerHelper.init(m_executionPath + PLUGIN_SUBDIR );

			TransformerHelper.getFileTypeToTransformerAssociation();

			logger.info("Initialisation terminée");
		}catch(Exception e){
			logger.info("exception occured in TransformDatasetProcesslet init phase");
			e.printStackTrace();
		}
	}

	@Override
	public void process(ProcessletInputs in, ProcessletOutputs out,
			ProcessletExecutionInfo info) throws ProcessletException {

		try{
			//Read input values
			String diffusionServerName = ((LiteralInput)in.getParameter("DiffusionServerName")).getValue();
			String featureSourceId = ((LiteralInput)in.getParameter("FeatureSourceId")).getValue();
			List<ProcessletInput> URLFileList = in.getParameters("URLFile");
			String ScriptName = ((LiteralInput)in.getParameter("ScriptName")).getValue();
			String SourceDataType = ((LiteralInput)in.getParameter("SourceDataType")).getValue();
			String CoordEpsgCode = ((LiteralInput)in.getParameter("CoordEpsgCode")).getValue();
			String Dataset = ((LiteralInput)in.getParameter("Dataset")).getValue();
			ComplexInput ZipInput = (ComplexInput) in.getParameter( "ZipInput" );

			//Get current user from session
			String currentUser = CurrentUser.getCurrentPrincipal();
			
			//Holder for the FeatureSourceGUID
			String featuresourceGuid;
			if(featureSourceId.equals("none")){
				UUID uuid = new UUID();
				featuresourceGuid = uuid.toString();
			}
			else{
				featuresourceGuid = featureSourceId;
			}

			List<String> URLList = null;

			URLList = new ArrayList<String>();
			if(URLFileList != null){
				for(ProcessletInput PIUrl : URLFileList){
					URLList.add(((LiteralInput)PIUrl).getValue());
				}
			}
			//Build and array of path+name for all supplied files, locally accessible.
			URLList = Utils.writeHttpGetToFileSystem(featuresourceGuid, URLList, ZipInput);

			logger.info("diffusionServerName: "+diffusionServerName);
			logger.info("featureSourceId: "+featureSourceId);
			logger.info("ScriptName: "+ScriptName);
			logger.info("SourceDataType: "+SourceDataType);
			logger.info("CoordEpsgCode: "+CoordEpsgCode);
			logger.info("Dataset: "+Dataset);

			//Perform transformation
			//check that this type of file can be handled by any transformer that has been plugged in
			TransformerHelper.getTransformerPlugIns();
			TransformerHelper.getFileTypeToTransformerAssociation();
			//call the transformer and handle exceptions, then dispatch the relevant to the GUI
			fsm.manageDataset(info, featureSourceId, featuresourceGuid, diffusionServerName, URLList, ScriptName, SourceDataType, CoordEpsgCode, Dataset, currentUser);

			//Fill the response
			LiteralOutput featureSourceGuid = (LiteralOutput)out.getParameter("FeatureSourceGuid");
			featureSourceGuid.setValue(featuresourceGuid);
			logger.info("TransformDataset returned -> FeatureSourceGuid:"+featuresourceGuid);
			 
		}catch(TransformationException e){
			logger.info("Transformation exception" + e.getMessage());
			e.printStackTrace();
			throw new ProcessletException(e.getMessage());
		}
		catch(Exception e){
			logger.info("Exception non gérée" + e.getMessage());
			e.printStackTrace();
			throw new ProcessletException(e.getMessage());
		}
	}	

}
