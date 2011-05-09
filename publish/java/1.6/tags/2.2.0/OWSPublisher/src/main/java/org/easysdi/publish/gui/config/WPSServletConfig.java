package org.easysdi.publish.gui.config;

import java.io.IOException;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.Unmarshaller;

import org.easysdi.publish.diffusion.GeoserverDiffuserControllerAdapter;
import org.easysdi.publish.diffusion.IDiffuserAdapter;
import org.easysdi.publish.diffusion.PublishLayerResponse;
import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.util.Utils;
import org.easysdi.publish.validation.InputValidator;
import org.springframework.security.core.context.SecurityContext;
import org.springframework.security.core.context.SecurityContextHolder;


public class WPSServletConfig extends HttpServlet {

	Logger logger = Logger.getLogger("org.easysdi.services.wps.WPSServlet");
	private Map<String, String> ERROR_DEFINITION_MAP = new HashMap<String, String>();
	Config conf;
	
	public void init(ServletConfig config) throws ServletException {
		conf = new Config();
		ERROR_DEFINITION_MAP.put("UndefinedIdentifier", "The identifier is not defined.");
		ERROR_DEFINITION_MAP.put("OperationNotSet", "The operation parameter is not set.");
		ERROR_DEFINITION_MAP.put("UnknownOperation", "The operation parameter is not known.");
		ERROR_DEFINITION_MAP.put("DataInput", "Wrong input value: ");
	}
	@Override
	protected void doGet(HttpServletRequest req, HttpServletResponse resp)
			throws ServletException, IOException {
		doPost(req, resp);
	}

	@Override
	protected void doPost(HttpServletRequest req, HttpServletResponse resp)
			throws ServletException, IOException {
		try{
			String operation = req.getParameter("operation");		
			if (operation != null){
				if (operation.equalsIgnoreCase("managePublicationServer")){
					resp.setContentType("text/xml");
					resp.getOutputStream().write(managePublicationServer(req).getBytes("UTF-8"));
				}
				else if (operation.equalsIgnoreCase("deletePublicationServer")){
					resp.setContentType("text/xml");
					resp.getOutputStream().write(deletePublicationServer(req).getBytes("UTF-8"));
				}
				else if (operation.equalsIgnoreCase("listPublicationServers")){
					resp.setContentType("text/xml");
					resp.getOutputStream().write(listPublicationServers().getBytes("UTF-8"));
				}
				else if (operation.equalsIgnoreCase("ListFeatureSources")){
					resp.setContentType("text/xml");
					resp.getOutputStream().write(listFeatureSources(req).getBytes("UTF-8"));
				}
				/*
				else if (operation.equalsIgnoreCase("GetTransformationProgress")){
					resp.setContentType("text/xml");
					resp.getOutputStream().write(getTransformationProgress(req).getBytes("UTF-8"));
				}
				*/
				else if (operation.equalsIgnoreCase("GetAvailableDatasetFromSource")){
					resp.setContentType("text/xml");
					resp.getOutputStream().write(getAvailableDatasetFromSource(req).getBytes("UTF-8"));
				}
				else{
					logger.info( "************************************");
					logger.info( "doPost() returned: UnknownOperation:");
					logger.info( "************************************");
					String error = String.format
					(ERROR_DEFINITION_MAP.get("UnknownOperation"), operation);
					resp.setContentType("text/xml");
					resp.getOutputStream().write(Utils.exceptiontoXML("UnknownOperation",error).getBytes("UTF-8"));
					// Type not allowed
				}
			}
			else{
				logger.info( "*************************************");
				logger.info( "Config returned: operation not set.  ");
				logger.info( "*************************************");
				resp.setContentType("text/xml");
				resp.getOutputStream().write(Utils.exceptiontoXML("OperationNotSet",ERROR_DEFINITION_MAP.get("OperationNotSet")).getBytes("UTF-8"));				
			}
		}
		catch(DataInputException e){
			logger.info( "*******************************************************");
			logger.info( "Config returned: DataInputException:" +  e.getMessage());
			logger.info( "*******************************************************");
			resp.setContentType("text/xml");
			resp.getOutputStream().write(Utils.exceptiontoXML("DataInputException", ERROR_DEFINITION_MAP.get("DataInput")+ e.getMessage()).getBytes("UTF-8"));
		}
		catch (Exception e){
			e.printStackTrace();
			resp.setContentType("text/xml");
			resp.getOutputStream().write(Utils.exceptiontoXML("PublishGeneralError",ERROR_DEFINITION_MAP.get("PublishGeneral")+e.getMessage()).getBytes("UTF-8"));
		}
		finally{
			resp.flushBuffer();
		}
	}
	
	private String managePublicationServer(HttpServletRequest req) throws DataInputException{
		
		InputValidator.managePublicationServer(req);

		String resp = conf.addPublicationServer(req);
		logger.info( "**************************************************************");
		logger.info( "publishLayer() returned: " +  resp.toString());
		logger.info( "**************************************************************");
		return resp.toString();
	}
	
	private String deletePublicationServer(HttpServletRequest req) throws DataInputException{
		
		InputValidator.deletePublicationServer(req);
		
		String resp = conf.deletePublicationServer(req.getParameter("id"));
		logger.info( "**************************************************************");
		logger.info( "publishLayer() returned: " +  resp.toString());
		logger.info( "**************************************************************");
		return resp.toString();
	}
	
	private String listPublicationServers(){
		String resp = conf.getPublicationServerlist();
		//logger.info( "**************************************************************");
		//logger.info( "publishLayer() returned: " +  resp.toString());
		//logger.info( "**************************************************************");
		return resp.toString();
	}
	
	private String listFeatureSources(HttpServletRequest req) throws DataInputException{
		
		InputValidator.listFeatureSources(req);
		
		String resp = conf.getFeatureSourcelist(req.getParameter("list"));
		//logger.info( "**************************************************************");
		//logger.info( "listFeatureSources() returned: " +  resp.toString());
		//logger.info( "**************************************************************");
		return resp.toString();
	}
	
	/* Should not be needed anymore
	private String getTransformationProgress(HttpServletRequest req) throws DataInputException{
		
		InputValidator.getTransformationProgress(req);
		
		String resp = conf.getTransformationProgress(req.getParameter("guid"));
		//logger.info( "**************************************************************");
		//logger.info( "getTransformationProgress() returned: " +  resp.toString());
		//logger.info( "**************************************************************");
		return resp.toString();
	}
	*/
	
	private String getAvailableDatasetFromSource(HttpServletRequest req) throws DataInputException{
		
		InputValidator.getAvailableDatasetFromSource(req);
		
		String resp = conf.getAvailableDatasetFromSource(req);
		//logger.info( "**************************************************************");
		//logger.info( "getTransformationProgress() returned: " +  resp.toString());
		//logger.info( "**************************************************************");
		return resp.toString();
	}
}
