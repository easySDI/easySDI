package org.easysdi.publish.gui.wps.provider;

import java.util.ArrayList;
import java.util.List;

import org.deegree.services.controller.OGCFrontController;
import org.deegree.services.wps.Processlet;
import org.deegree.services.wps.ProcessletException;
import org.deegree.services.wps.ProcessletExecutionInfo;
import org.deegree.services.wps.ProcessletInputs;
import org.deegree.services.wps.ProcessletOutputs;
import org.deegree.services.wps.input.LiteralInput;
import org.deegree.services.wps.input.ProcessletInput;
import org.deegree.services.wps.output.LiteralOutput;
import org.easysdi.publish.biz.database.Geodatabase;
import org.easysdi.publish.transformation.FeatureSourceManager;
import org.easysdi.publish.transformation.TransformerHelper;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;


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

			//do the url list:
			List<String> URLList = new ArrayList<String>();
			for(ProcessletInput PIUrl : URLFileList){
				URLList.add(((LiteralInput)PIUrl).getValue());
			}

			logger.info("diffusionServerName: "+diffusionServerName);
			logger.info("featureSourceId: "+featureSourceId);
			for(ProcessletInput PIUrl : URLFileList){
				logger.info("URLFile: "+((LiteralInput)PIUrl).getValue());
			}
			logger.info("ScriptName: "+ScriptName);
			logger.info("SourceDataType: "+SourceDataType);
			logger.info("CoordEpsgCode: "+CoordEpsgCode);
			logger.info("Dataset: "+Dataset);


			//Perform transformation
			//check that this type of file can be handled by any transformer that has been plugged in
			TransformerHelper.getTransformerPlugIns();
			TransformerHelper.getFileTypeToTransformerAssociation();
			//call the transformer and handle exceptions, then dispatch the relevant to the GUI
			String response = fsm.manageDataset(info, featureSourceId, diffusionServerName, URLList,ScriptName, SourceDataType, CoordEpsgCode, Dataset);

			//Fill the response
			LiteralOutput featureSourceGuid = (LiteralOutput)out.getParameter("FeatureSourceGuid");
			featureSourceGuid.setValue(response);
			logger.info("TransformDataset returned -> FeatureSourceGuid:"+response);
		}catch(Exception e){
			logger.info("Exception non gérée" + e.getMessage());
			e.printStackTrace();
			throw new ProcessletException(e.getMessage());
		}
	}	

}
