package org.easysdi.monitor.biz.logging;

//import java.io.ByteArrayInputStream;
import java.io.IOException;
//import java.io.InputStream;
//import java.util.Properties;
import java.util.Set;

import java.util.Iterator;
//import javax.imageio.ImageIO;
//import javax.imageio.ImageReader;
//import javax.imageio.stream.ImageInputStream;

import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathFactory;
import javax.xml.parsers.*;
import javax.xml.namespace.NamespaceContext;
import org.w3c.dom.Document;
import com.sun.org.apache.xml.internal.utils.PrefixResolver;
import com.sun.org.apache.xml.internal.utils.PrefixResolverDefault;

import org.apache.log4j.Logger;
import org.deegree.portal.owswatch.ServiceConfiguration;
import org.deegree.portal.owswatch.ServiceLog;
import org.deegree.portal.owswatch.Status;
import org.deegree.portal.owswatch.ValidatorResponse;
import org.easysdi.monitor.biz.alert.AbstractAction;
import org.easysdi.monitor.biz.alert.Alert;
import org.easysdi.monitor.biz.job.Job;
import org.easysdi.monitor.biz.job.JobConfiguration;
import org.easysdi.monitor.biz.job.QueryResult;
import org.easysdi.monitor.biz.job.QueryValidationResult;
import org.easysdi.monitor.biz.job.QueryValidationSettings;
import org.easysdi.monitor.dat.dao.LogDaoHelper;

import org.easysdi.monitor.biz.job.OverviewLastQueryResult;
import org.easysdi.monitor.biz.job.Status.StatusValue;
import org.easysdi.monitor.dat.dao.LastLogDaoHelper;

/**
 * Processes the result of a owsWatch query polling.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.1, 2010-04-30
 *
 */
public class MonitorServiceLog extends ServiceLog {

    private static final long serialVersionUID = 5207213371103847912L;
    
    private final Logger logger = Logger.getLogger(MonitorServiceLog.class);

    private boolean           resultLogging;
    private QueryResult       lastResult;



    /**
     * Creates a new logger for Monitor queries.
     * 
     * @throws  IOException the paths are invalid.
     *                      <p>
     *                      This is an exception that may be thrown by the 
     *                      subclass. It should never happen with this class.
     */
    public MonitorServiceLog() throws IOException {
        super("", -1, "", "", "", null);

        this.setResultLogged(true);
    }



    /**
     * Processes a new polling result.
     * 
     * @param   response        the query response produced by owsWatch
     * @param   serviceConfig   the owsWatch test configuration
     */
    @Override
    public void addMessage(ValidatorResponse response,
                           ServiceConfiguration serviceConfig) {
        final QueryResult result 
            = new QueryResult(serviceConfig.getServiceid(), 
                              serviceConfig.getServiceName(), response,
                              serviceConfig.createHttpRequest(), 
                              serviceConfig.getHttpMethod());
    
        Float deliveryTime = null;
		Float responseSize = null;
		
	
        this.setLastResult(result);

        if (this.isResultLogged()) {
            final RawLogEntry logEntry = result.createRawLogEntry();
                   
            
			boolean error = false;
			if (response.getStatus() != Status.RESULT_STATE_AVAILABLE) {
				// Error
				error = true;
			}
			
			String requestType = serviceConfig.getRequestType().toLowerCase();
			
			long queryID = result.getQueryId();

			// Does a log already exist
			OverviewLastQueryResult lastQueryEntry = LastLogDaoHelper.getLastLogDao().exist(queryID);

			if (lastQueryEntry == null) {
				lastQueryEntry = new OverviewLastQueryResult();
				lastQueryEntry.setQueryid(queryID);
			}
			deliveryTime=logEntry.getResponseDelay();

			// Error request
			if (error) {
				lastQueryEntry.setTextResult(response.getMessage());
				lastQueryEntry.setData(response.getData());
				lastQueryEntry.setContentType(response.getContentType());
			} else {
				if (requestType.equalsIgnoreCase("getmap") || requestType.equalsIgnoreCase("gettile")) {
					lastQueryEntry.setData(response.getData());
					lastQueryEntry.setContentType(response.getContentType());
					// TO check response size for different image types
					responseSize = (float) response.getResponseLength();
				}else
				{
						responseSize = response.getData() != null? response.getData().length: 0.0F;
						lastQueryEntry.setData(response.getData());
						lastQueryEntry.setContentType(response.getContentType());
				}

				QueryValidationResult validationResult = result.getParentQuery().getQueryValidationResult();
				if(validationResult == null)
				{
					validationResult = new QueryValidationResult();
					validationResult.setQueryId(result.getParentQuery().getQueryId());
				}
				validationResult.setSizeValidationResult(true);
				validationResult.setTimeValidationResult(true);
				validationResult.setXpathValidationResult(true);

				QueryValidationSettings validationSettings = result.getParentQuery().getQueryValidationSettings();
				if(validationSettings != null)
				{
					// 1) Time validation
					if(validationSettings.isUseTimeValidation() && validationSettings.getNormTime() != null)
					{
						if(validationSettings.getNormTime() < (deliveryTime *1000)){
							logEntry.setMessage("Time validation fail");
							logEntry.setStatus(StatusValue.OUT_OF_ORDER);
							validationResult.setTimeValidationResult(false);
						}
					}
					
					// 2) Size validation 
					if(validationSettings.isUseSizeValidation() && validationSettings.getNormSize() != null)
					{
						float max = validationSettings.getNormSize() + (validationSettings.getNormSize() * validationSettings.getNormSizeTolerance() / 100); 
						float min = validationSettings.getNormSize() - (validationSettings.getNormSize() * validationSettings.getNormSizeTolerance() / 100);
						if(responseSize < min || responseSize > max){
							logEntry.setMessage("Size validation fail");
							logEntry.setStatus(StatusValue.UNAVAILABLE);
							validationResult.setSizeValidationResult(false);
						}			
					}
				
					// 3) XPath validation
					if(validationSettings.isUseXpathValidation() && validationSettings.getExpectedXpathOutput() != null)
					{
						String xpathValidationOutput = "";
						try
						{
							DocumentBuilderFactory xmlFact = DocumentBuilderFactory.newInstance();
				            xmlFact.setNamespaceAware(false);
				            DocumentBuilder builder = xmlFact.newDocumentBuilder();
				            Document doc = builder.parse(new java.io.ByteArrayInputStream(response.getData()));
							
				            final PrefixResolver resolver = new PrefixResolverDefault(doc.getDocumentElement());
				            NamespaceContext ctx = new NamespaceContext() {
				            	public String getNamespaceURI(String prefix) {
				            		return resolver.getNamespaceForPrefix(prefix);
				            	}
				            	@SuppressWarnings("unchecked")
								public Iterator getPrefixes(String val) {			            	
	        	                    return null;
				            	}
				            	public String getPrefix(String uri) {
				            		return null;
				            	}
				            };
							 
							XPath xpath = XPathFactory.newInstance().newXPath();
							xpath.setNamespaceContext(ctx);
							try
							{
								xpathValidationOutput = xpath.evaluate(validationSettings.getXpathExpression(), doc);
							}
							catch(Exception e)
							{
								logEntry.setMessage("Xpath evaluation failed");
								logEntry.setStatus(StatusValue.UNAVAILABLE);
								xpathValidationOutput = "Xpath evaluation failed";
								validationResult.setXpathValidationResult(false);
							}
						}catch(Exception e)
						{
							logEntry.setMessage("Xpath evaluation failed");
							logEntry.setStatus(StatusValue.UNAVAILABLE);
							xpathValidationOutput = "Xpath evaluation failed";
							validationResult.setXpathValidationResult(false);
						}

						validationResult.setXpathValidationOutput(xpathValidationOutput);
						if(validationSettings.getExpectedXpathOutput().compareTo(xpathValidationOutput)!= 0){
							logEntry.setMessage(xpathValidationOutput);
							logEntry.setStatus(StatusValue.UNAVAILABLE);
							validationResult.setXpathValidationResult(false);
						}
					}		
				}
				validationResult.setResponseSize(responseSize);
				validationResult.setDeliveryTime(deliveryTime);	
						
				try
				{	
					// Create/Update validation result
					if(!validationResult.persist())
					{
						this.logger.error("An exception was thrown while saving validationResult");
					}
				}catch(Exception e)
				{
					this.logger.error("An exception was thrown while saving validationResult: "+e.getMessage());
				}
				
			}
			
			try
			{
				JobConfiguration jobConfig = result.getParentQuery().getConfig().getParentJob().getConfig();
				if(jobConfig.isAlertsActivated() && (logEntry.getStatusValue().equals(StatusValue.UNAVAILABLE) || 
						logEntry.getStatusValue().equals((StatusValue.OUT_OF_ORDER))))
				{			
					final Alert alert = Alert.create(result.getParentQuery().getConfig().getParentJob().getStatusValue() ,
						logEntry.getStatusValue(), logEntry.getMessage(), logEntry.getResponseDelay(),null 
						,result.getParentQuery().getConfig().getParentJob(),response.getData(),response.getContentType());

					this.triggerActions(alert,result.getParentQuery().getConfig().getParentJob());
				}
			}catch(Exception e)
			{
				this.logger.error("An exception was thrown while saving alert: "+e.getMessage());
			}
			
			// Save raw log	
            if (!LogDaoHelper.getLogDao().persistRawLog(logEntry)) {
                this.logger.error("An exception was thrown while saving a log entry");
            }
            
        	// Save or update last log
			if (!LastLogDaoHelper.getLastLogDao().create(lastQueryEntry)) {
				this.logger.error("An exception was thrown while saving a last log entry");
			}
        }
    }
    
    private void triggerActions(Alert alert,Job parentJob) {
        final Set<AbstractAction> actionsSet = parentJob.getActions();

        if (null != actionsSet) {

            for (AbstractAction action : actionsSet) {
                action.trigger(alert);
            }
        }
    }


    /**
     * Defines whether the query results must be logged.
     * 
     * @param   newResultLogging    <code>true</code> if the next result must be
     *                              logged
     */
    public void setResultLogged(boolean newResultLogging) {
        this.resultLogging = newResultLogging;
    }



    /**
     * Gets if the query results are logged.
     * 
     * @return <code>true</code> if the query result are logged
     */
    private boolean isResultLogged() {
        return this.resultLogging;
    }



    /**
     * Defines the last query result to date.
     * 
     * @param   result  the latest query result
     */
    private void setLastResult(QueryResult result) {

        this.lastResult = result;

    }



    /**
     * Gets the last query result.
     * 
     * @return  the query result
     */
    public QueryResult getLastResult() {

        return this.lastResult;

    }
    
}
