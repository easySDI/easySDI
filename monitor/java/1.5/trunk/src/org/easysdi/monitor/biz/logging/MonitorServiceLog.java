package org.easysdi.monitor.biz.logging;

import java.io.ByteArrayInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

import java.util.Iterator;
import javax.imageio.ImageIO;
import javax.imageio.ImageReader;
import javax.imageio.stream.ImageInputStream;

import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathFactory;

import org.apache.log4j.Logger;
import org.deegree.portal.owswatch.ServiceConfiguration;
import org.deegree.portal.owswatch.ServiceLog;
import org.deegree.portal.owswatch.Status;
import org.deegree.portal.owswatch.ValidatorResponse;
import org.easysdi.monitor.biz.job.QueryResult;
import org.easysdi.monitor.biz.job.QueryValidationResult;
import org.easysdi.monitor.biz.job.QueryValidationSettings;
import org.easysdi.monitor.dat.dao.LogDaoHelper;

import org.easysdi.monitor.biz.job.OverviewLastQueryResult;
import org.easysdi.monitor.dat.dao.LastLogDaoHelper;
import org.xml.sax.InputSource;

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
		String xmlData = null;
	
        this.setLastResult(result);

        if (this.isResultLogged()) {
            final RawLogEntry logEntry = result.createRawLogEntry();

            if (!LogDaoHelper.getLogDao().persistRawLog(logEntry)) {
                this.logger.error(
                       "An exception was thrown while saving a log entry");
            }
            
         // Save last log
			long queryID = result.getQueryId();

			// Does a log already exist
			OverviewLastQueryResult lastQueryEntry = LastLogDaoHelper
			.getLastLogDao().exist(queryID);

			//String serviceType = serviceConfig.getServiceType().toLowerCase();
			String requestType = serviceConfig.getRequestType().toLowerCase();
			boolean error = false;
			if (response.getStatus() != Status.RESULT_STATE_AVAILABLE) {
				// Error
				error = true;
			}

			if (lastQueryEntry == null) {
				lastQueryEntry = new OverviewLastQueryResult();
				lastQueryEntry.setQueryid(queryID);
			}
			deliveryTime=logEntry.getResponseDelay();

			// Error request
			if (error) {
				lastQueryEntry.setXmlResult("");
				lastQueryEntry.setTextResult(response.getMessage());
			} else {
				// Image request
				if (requestType.equalsIgnoreCase("getmap") || requestType.equalsIgnoreCase("gettile")) {

					//Save received image in file system:
					byte[] imgBytes  = response.getImage();
					String imageType = determineImageType(imgBytes);
					String strFilePath = this.getProperties().getProperty("imagefolderLocal")+queryID + imageType;

					try{
						FileOutputStream fos = new FileOutputStream(strFilePath);
						fos.write(imgBytes);
						fos.flush();
						fos.close();
					}
					catch(IOException e)
					{
						logger.fatal("Could not write last query result image to " +strFilePath);
					}

					String tempUrl = this.getProperties().getProperty("imageFolderUrlPath")+queryID + imageType;

					lastQueryEntry.setPictureUrl(tempUrl);
					responseSize = (float)response.getImage().length;
				}
				if (requestType.equalsIgnoreCase("getcapabilities") || requestType.equalsIgnoreCase("getfeature") ||
					requestType.equalsIgnoreCase("getrecordbyid") || requestType.equalsIgnoreCase("getrecords") ||
					requestType.equalsIgnoreCase("getcoverage") || requestType.equalsIgnoreCase("describesensor") ) {
					lastQueryEntry.setXmlResult(response.getData());
					responseSize = (float)response.getData().length();
					xmlData = response.getData();
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
					if(validationSettings.isUseSizeValidation() && validationSettings.getNormSize() != null)
					{
						float max = validationSettings.getNormSize() + (validationSettings.getNormSize() * validationSettings.getNormSizeTolerance() / 100); 
						float min = validationSettings.getNormSize() - (validationSettings.getNormSize() * validationSettings.getNormSizeTolerance() / 100);
						if(responseSize < min || responseSize > max){
							validationResult.setSizeValidationResult(false);
						}			
					}
					if(validationSettings.isUseTimeValidation() && validationSettings.getNormTime() != null)
					{
						if(validationSettings.getNormTime() < (deliveryTime *1000)){
							validationResult.setTimeValidationResult(false);
						}
					}
					if(validationSettings.isUseXpathValidation() && validationSettings.getExpectedXpathOutput() != null)
					{
						String xpathValidationOutput = "";

						InputStream is = new ByteArrayInputStream(xmlData.getBytes()); 
						XPath xpath = XPathFactory.newInstance().newXPath();
						InputSource inputSource = new InputSource(is);
						try
						{
							xpathValidationOutput = xpath.evaluate
							(validationSettings.getXpathExpression(), inputSource);
						}
						catch(Exception e)
						{
							xpathValidationOutput = "Xpath evaluation failed.";
							validationResult.setXpathValidationResult(false);
						}

						validationResult.setXpathValidationOutput(xpathValidationOutput);
						if(validationSettings.getExpectedXpathOutput().compareTo(xpathValidationOutput)!= 0){
							validationResult.setXpathValidationResult(false);
						}
					}		
				}
				validationResult.setResponseSize(responseSize);
				validationResult.setDeliveryTime(deliveryTime);	
				validationResult.persist();
			}
			
			// Save or update row
			if (!LastLogDaoHelper.getLastLogDao().create(lastQueryEntry)) {
				this.logger
				.error("An exception was thrown while saving a last log entry");
			}
        }
    }

    /**
     * Finds image type
     * @param imageData
     * @return
     */
	private String determineImageType(byte[] imageData)
	{
		String formatName = "";
		try
		{
			ImageInputStream iis = ImageIO.createImageInputStream(new ByteArrayInputStream(imageData));
			Iterator<ImageReader> iter = ImageIO.getImageReaders(iis);
			if (iter.hasNext()) {
				ImageReader reader = (ImageReader)iter.next();
				formatName = "."+ reader.getFormatName();
				reader.dispose();
			}
		}catch(Exception e)
		{
			formatName = ".jpg";
		}
		return formatName;
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
    
    /**
     * Gets image config settings
     * @return
     */
	private Properties getProperties()  {
		String configFileName = "image-config.properties";
		Properties properties = new Properties();
		try {
			final InputStream propStream 
			= this.getClass().getClassLoader().getResourceAsStream(
					configFileName);
			properties.load(propStream);
		} catch (IOException e) {
			logger.fatal("Could not open configuration file " + configFileName);
		}
		return properties;
	}
}
