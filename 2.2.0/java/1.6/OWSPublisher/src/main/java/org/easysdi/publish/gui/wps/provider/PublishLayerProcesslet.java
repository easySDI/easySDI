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
import org.easysdi.publish.util.Utils;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.security.core.context.SecurityContextHolder;


public class PublishLayerProcesslet implements Processlet{

	private static final Logger logger = LoggerFactory.getLogger(PublishLayerProcesslet.class);

	@Override
	public void destroy() {
		// TODO Auto-generated method stub

	}

	@Override
	public void init() {

	}

	@Override
	public void process(ProcessletInputs in, ProcessletOutputs out,
			ProcessletExecutionInfo info) throws ProcessletException {

		System.out.println("thread id"+Thread.currentThread().getId());
		System.out.println(SecurityContextHolder.getContext().getAuthentication().getPrincipal().toString());
		
		String layerId = ((LiteralInput)in.getParameter("LayerId")).getValue();
		String FeatureSourceId = ((LiteralInput)in.getParameter("FeatureSourceId")).getValue();
		List<ProcessletInput> AttributeAliasList = in.getParameters("AttributeAlias");
		String Title = ((LiteralInput)in.getParameter("Title")).getValue();
		String Quality_Area = ((LiteralInput)in.getParameter("Quality_Area")).getValue();
		String KeywordList = ((LiteralInput)in.getParameter("KeywordList")).getValue();
		String Abstract = ((LiteralInput)in.getParameter("Abstract")).getValue();	
		String Name = ((LiteralInput)in.getParameter("Name")).getValue();
		String Geometry = ((LiteralInput)in.getParameter("Geometry")).getValue();

		//do the url list:
		List<String> AttributeAlias = new ArrayList<String>();
		for(ProcessletInput PIAA : AttributeAliasList){
			AttributeAlias.add(((LiteralInput)PIAA).getValue());
		}

		logger.info("layerId: "+layerId);
		logger.info("FeatureTypeId: "+FeatureSourceId);
		for(ProcessletInput PIAA : AttributeAliasList){
			logger.info("URLFile: "+((LiteralInput)PIAA).getValue());
		}
		logger.info("Title: "+Title);
		logger.info("Quality_Area: "+Quality_Area);
		logger.info("KeywordList: "+KeywordList);
		logger.info("Abstract: "+Abstract);
		logger.info("Name: "+Name);
		logger.info("Geometry: "+Geometry);

		try{
			IDiffuserAdapter diffusorController = new GeoserverDiffuserControllerAdapter();
			PublishLayerResponse resp = diffusorController.publishLayer( layerId, FeatureSourceId, AttributeAlias, Title, Name, Quality_Area, KeywordList, Abstract, Geometry);
			//Fill the response
			((LiteralOutput)out.getParameter("LayerGuid")).setValue(resp.layerGuid);

			for( int i = 0; i < resp.endPoints.size(); i++ )
			{
				((LiteralOutput)out.getParameter(resp.endPointsTypes.get(i))).setValue(resp.endPoints.get(i));
			}
			for( int i = 0; i < resp.bbox.size(); i++ )
			{
				((LiteralOutput)out.getParameter(resp.bboxTypes.get(i))).setValue(resp.bbox.get(i));
			}
		}
		catch (FeatureSourceException e){
			logger.info("FeatureSourceException: " + e.getMessage());
			throw new ProcessletException(ExceptionMessage.get("FeatureSource")+ e.getMessage());
		}
		catch (PublicationException e){
			logger.info("PublicationException: " + e.getMessage());
			throw new ProcessletException( ExceptionMessage.get("Publication")+ e.getMessage());
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
