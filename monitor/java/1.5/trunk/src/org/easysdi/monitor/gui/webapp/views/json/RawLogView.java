package org.easysdi.monitor.gui.webapp.views.json;

import java.util.Locale;
import java.util.Map;
import java.util.Set;

import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ArrayNode;
import org.easysdi.monitor.biz.logging.RawLogEntry;
import org.easysdi.monitor.gui.webapp.MonitorInterfaceException;
import org.easysdi.monitor.gui.webapp.views.json.serializers.RawLogSerializer;

/**
 * Displays the JSON representation of a raw log entry collection.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.2, 2010-08-06
 *
 */
public class RawLogView extends AbstractJsonView {
    
    /**
     * Creates a new view.
     */
    public RawLogView() {
        
    }
    
    

    /**
     * Generates the JSON code for the raw log entry collection.
     * 
     * @param   model                       the model data to output
     * @param   locale                      the locale indicating the language 
     *                                      in which localizable data should be 
     *                                      shown
     * @return                              a string containing the JSON code 
     *                                      for the raw log entry collection
     * @throws  MonitorInterfaceException   <ul>
     *                                      <li>the model data is invalid</li>
     *                                      <li>an error occurred while 
     *                                      converting the raw log entry 
     *                                      collection to JSON</li>
     *                                      </ul>
     */
    @SuppressWarnings("unchecked")
    @Override
    protected JsonNode getResponseData(Map<String, ?> model, Locale locale) 
        throws MonitorInterfaceException {

        if (model.containsKey("rawLogsCollection") && model.containsKey("addQueryId")) {
            
        	if(model.containsKey("getExport") && model.containsKey("Slaname") && model.containsKey("Jobname") && model.containsKey("Queryname"))
        	{
        		final Set<RawLogEntry> logsCollection  = (Set<RawLogEntry>) model.get("rawLogsCollection");
        		final Boolean addQueryId = (Boolean) model.get("addQueryId");
        		final Boolean getExport = (Boolean) model.get("getExport");
        		final Boolean isSummary = (Boolean) model.get("isSummary");
        		final String slaName = (String)model.get("Slaname");
        		final String jobName = (String)model.get("Jobname");
        		final String queryName = (String)model.get("Queryname");
        		final ObjectMapper mapper = this.getObjectMapper();  
        		final ArrayNode rowsCollection = mapper.createArrayNode();
        		for (RawLogEntry logEntry : logsCollection) {
        			rowsCollection.add(RawLogSerializer.serialize(logEntry,addQueryId,getExport,slaName,jobName,queryName,
                                                              locale, mapper,isSummary));
        		}     
        		return rowsCollection;
        	}else
        	{
        		final Set<RawLogEntry> logsCollection  = (Set<RawLogEntry>) model.get("rawLogsCollection");
        		final Boolean addQueryId = (Boolean) model.get("addQueryId");
        		final Boolean isSummary = (Boolean) model.get("isSummary");
        		final Long noPagingCount = (Long) model.get("noPagingCount");
        		final ObjectMapper mapper = this.getObjectMapper();
        		this.getRootObjectNode().put("noPagingCount", noPagingCount);
        		final ArrayNode rowsCollection = mapper.createArrayNode();
        		for (RawLogEntry logEntry : logsCollection) {
        			rowsCollection.add(RawLogSerializer.serialize(logEntry,addQueryId,
                                                              locale, mapper,isSummary));
        		}     
        		return rowsCollection;
        	}
        }     
        throw new MonitorInterfaceException("An internal error occurred",
                                            "internal.error");
    }
    
    
    
    /**
     * {@inheritDoc}
     */
    @Override
    protected Boolean isSuccess() {
        return true;
    }
}
