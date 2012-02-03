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
import org.easysdi.publish.biz.diffuser.Diffuser;
import org.easysdi.publish.biz.layer.Layer;
import org.easysdi.publish.diffusion.GeoserverDiffuserControllerAdapter;
import org.easysdi.publish.diffusion.IDiffuserAdapter;
import org.easysdi.publish.diffusion.PublishLayerResponse;
import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.DiffuserException;
import org.easysdi.publish.exception.DiffuserNotFoundException;
import org.easysdi.publish.exception.FeatureSourceException;
import org.easysdi.publish.exception.LayerNotFoundException;
import org.easysdi.publish.exception.PublicationException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.PublishGeneralException;
import org.easysdi.publish.gui.ExceptionMessage;
import org.easysdi.publish.transformation.FeatureSourceManager;
import org.easysdi.publish.util.Utils;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.dao.DataAccessException;


public class DeleteLayerProcesslet implements Processlet{

	private static final Logger logger = LoggerFactory.getLogger(DeleteLayerProcesslet.class);
	public FeatureSourceManager fsm;

	@Override
	public void destroy() {
		// TODO Auto-generated method stub

	}

	@Override
	public void init() {

		fsm = FeatureSourceManager.getInstance();
	}

	@Override
	public void process(ProcessletInputs in, ProcessletOutputs out,
			ProcessletExecutionInfo info) throws ProcessletException {

		String LayerId = ((LiteralInput)in.getParameter("LayerId")).getValue();		
		logger.info("LayerId: "+LayerId);

		try{
			IDiffuserAdapter diffuserController = new GeoserverDiffuserControllerAdapter();        
			logger.info("Layer id to remove: " + LayerId );
			Layer layerFromWebTier = Layer.getFromGuid( LayerId );

			if( layerFromWebTier == null)
			{
				logger.info("No layer with guid found: " + LayerId );
				throw new LayerNotFoundException("no layer with guid '"+LayerId+"' found.");
			}

			Diffuser myDiffuser = layerFromWebTier.getFeatureSource().getDiffuser();
			if( myDiffuser == null)
			{
				logger.info("No diffuser found that corresponds to layer" + LayerId);
				throw new DiffuserNotFoundException("No diffuser found that corresponds to layer: " + LayerId);
			}

			logger.info("Layer Name to remove: " + layerFromWebTier.getName());

			//remove from remote server
			boolean res = diffuserController.removeLayer( myDiffuser, layerFromWebTier );
			logger.info("RemoveLayer DataTier Result: " + res );
			if( false == res){
				throw new LayerNotFoundException("no layer with guid '"+LayerId+"' found on the server.");
			}
			
			try{
				layerFromWebTier.delete();
			}catch(DataAccessException e){
				System.out.println("Error occured, cause:"+e.getCause() +" message:"+ e.getMessage());
				throw new DataInputException("Delete failed, error when deleting layer: "+e.getCause() +" message:"+ e.getMessage());
			}

			((LiteralOutput)out.getParameter("LayerId")).setValue(LayerId);
		}
		catch (DiffuserNotFoundException e){
			logger.info("DiffuserNotFoundException: " + e.getMessage());
			throw new ProcessletException( ExceptionMessage.get("Diffuser")+e.getMessage());
		}
		catch (LayerNotFoundException e){
			logger.info("LayerNotFoundException: " + e.getMessage());
			throw new ProcessletException( ExceptionMessage.get("Layer")+e.getMessage());
		}
		catch (PublicationException e){
			logger.info("PublicationException: " + e.getMessage());
			throw new ProcessletException( ExceptionMessage.get("Publication")+e.getMessage());
		}
		catch (DiffuserException e) {
			logger.info("DiffuserException: " + e.getMessage());
			throw new ProcessletException( ExceptionMessage.get("Diffuser")+e.getMessage());
		}
		catch (PublishConfigurationException e) {
			logger.info("PublishConfigurationException: " + e.getMessage());
			throw new ProcessletException( ExceptionMessage.get("PublishConfiguration")+e.getMessage());
		}
		catch (PublishGeneralException e) {
			logger.info("PublishGeneralException: " + e.getMessage());
			throw new ProcessletException( ExceptionMessage.get("PublishGeneral")+e.getMessage());
		}
		catch (Exception e) {
			e.printStackTrace();
			logger.info("Exception: " + e.getMessage());
			throw new ProcessletException( ExceptionMessage.get("PublishGeneral")+e.getMessage());
		}
	}

}
