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
import org.easysdi.publish.biz.layer.FeatureSource;
import org.easysdi.publish.biz.layer.Layer;
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


public class CopyLayerProcesslet implements Processlet{

	private static final Logger logger = LoggerFactory.getLogger(CopyLayerProcesslet.class);

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
		try{
			System.out.println("thread id"+Thread.currentThread().getId());
			System.out.println(SecurityContextHolder.getContext().getAuthentication().getPrincipal().toString());

			String layerIdToCopy = ((LiteralInput)in.getParameter("LayerId")).getValue();
			//the new layer Id
			String layerId = "none";
			String Name = ((LiteralInput)in.getParameter("Name")).getValue();

			//load layer to copy
			Layer l = Layer.getFromGuid(layerIdToCopy);

			//Feed data from existing to new layer
			String FeatureSourceId = l.getFeatureSource().getGuid();
			String Title = Name;
			String Quality_Area = l.getQuality_area();
			String KeywordList = l.getKeywordList();
			String Abstract = l.get_abstract();
			String Geometry = l.getStyle();

			//do the alias list, depreciated:
			List<String> AttributeAlias = new ArrayList<String>();

			logger.info("layerId: "+layerId);
			logger.info("FeatureTypeId: "+FeatureSourceId);
			logger.info("Title: "+Title);
			logger.info("Quality_Area: "+Quality_Area);
			logger.info("KeywordList: "+KeywordList);
			logger.info("Abstract: "+Abstract);
			logger.info("Name: "+Name);
			logger.info("Geometry: "+Geometry);


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
