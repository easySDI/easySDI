package org.easysdi.publish.gui.wps.provider;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.deegree.services.controller.OGCFrontController;
import org.deegree.services.wps.Processlet;
import org.deegree.services.wps.ProcessletException;
import org.deegree.services.wps.ProcessletExecutionInfo;
import org.deegree.services.wps.ProcessletInputs;
import org.deegree.services.wps.ProcessletOutputs;
import org.deegree.services.wps.input.LiteralInput;
import org.deegree.services.wps.input.ProcessletInput;
import org.deegree.services.wps.output.LiteralOutput;
import org.easysdi.publish.diffusion.GeoserverDiffuserControllerAdapter;
import org.easysdi.publish.diffusion.IDiffuserAdapter;
import org.easysdi.publish.diffusion.PublishLayerResponse;
import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.DiffuserException;
import org.easysdi.publish.exception.FeatureSourceException;
import org.easysdi.publish.exception.PublicationException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.PublishGeneralException;
import org.easysdi.publish.gui.ExceptionMessage;
import org.easysdi.publish.transformation.FeatureSourceManager;
import org.easysdi.publish.util.Utils;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;


public class DeleteFeatureSourceProcesslet implements Processlet{

	private static final Logger logger = LoggerFactory.getLogger(DeleteFeatureSourceProcesslet.class);
	public FeatureSourceManager fsm;

	@Override
	public void destroy() {
		// TODO Auto-generated method stub

	}

	@Override
	public void init() {
		//Exception definition
		fsm = FeatureSourceManager.getInstance();
	}

	@Override
	public void process(ProcessletInputs in, ProcessletOutputs out,
			ProcessletExecutionInfo info) throws ProcessletException {

		String FeatureSourceId = ((LiteralInput)in.getParameter("FeatureSourceId")).getValue();		
		logger.info("FeatureSourceId: "+FeatureSourceId);

		try{
			fsm.deleteFeatureSource(FeatureSourceId);
			((LiteralOutput)out.getParameter("FeatureSourceId")).setValue(FeatureSourceId);
		}
		catch (FeatureSourceException e){
			logger.info("FeatureSourceException: " + e.getMessage());
			throw new ProcessletException(ExceptionMessage.get("FeatureSource")+ e.getMessage());
		}
		catch (DiffuserException e) {
			logger.info("DiffuserException: " + e.getMessage());
			throw new ProcessletException(ExceptionMessage.get("Diffuser")+ e.getMessage());
		}
		catch (PublishConfigurationException e) {
			logger.info("PublishConfigurationException: " + e.getMessage());
			throw new ProcessletException(ExceptionMessage.get("PublishConfiguration")+ e.getMessage());
		}
		catch (PublishGeneralException e) {
			logger.info("PublishGeneralException: " + e.getMessage());
			throw new ProcessletException(ExceptionMessage.get("PublishGeneral")+ e.getMessage());
		}
		catch (Exception e) {
			e.printStackTrace();
			logger.info("Exception: " + e.getMessage());
			throw new ProcessletException( ExceptionMessage.get("PublishGeneral")+e.getMessage());
		}
	}

}
