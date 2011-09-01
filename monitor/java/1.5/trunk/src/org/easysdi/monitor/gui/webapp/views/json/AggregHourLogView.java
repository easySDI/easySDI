/**
 * 
 */
package org.easysdi.monitor.gui.webapp.views.json;

import java.util.Collection;
import java.util.Locale;
import java.util.Map;

import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ArrayNode;
import org.easysdi.monitor.biz.logging.AbstractAggregateHourLogEntry;
import org.easysdi.monitor.gui.webapp.MonitorInterfaceException;
import org.easysdi.monitor.gui.webapp.views.json.serializers.AggregHourLogSerializer;

/**
 * @author berg3428
 *
 */
public class AggregHourLogView extends AbstractJsonView {

    /**
     * Creates a new view.
     */
    public AggregHourLogView() {
        
    }
    
	@SuppressWarnings("unchecked")
	@Override
	protected JsonNode getResponseData(Map<String, ?> model, Locale locale)
    throws MonitorInterfaceException {

        if (model.containsKey("aggregHourLogsCollection")) {
        	if(model.containsKey("getExport") && model.containsKey("Slaname") 
        	&& model.containsKey("Jobname") && model.containsKey("Queryname"))
        	{
        	
        		final Collection<AbstractAggregateHourLogEntry> logsCollection 
                = (Collection<AbstractAggregateHourLogEntry>) model.get(
                        "aggregHourLogsCollection");
            	final Long noPagingCount = (Long) model.get("noPagingCount");
            
    			final String slaName = (String)model.get("Slaname");
    			final String jobName = (String)model.get("Jobname");
    			final String queryName = (String)model.get("Queryname");
            
            	final ObjectMapper mapper = this.getObjectMapper();
            	this.getRootObjectNode().put("noPagingCount", noPagingCount);
            	final ArrayNode jsonLogsCollection = mapper.createArrayNode();
            
            	for (AbstractAggregateHourLogEntry logEntry : logsCollection) {
                jsonLogsCollection.add(AggregHourLogSerializer.serialize(logEntry, 
                                                                     mapper,jobName,queryName,
                                                                     slaName));
            	}
            
            	return jsonLogsCollection;
        	}
        	else
        	{
        		final Collection<AbstractAggregateHourLogEntry> logsCollection 
                = (Collection<AbstractAggregateHourLogEntry>) model.get(
                        "aggregHourLogsCollection");
        		final Long noPagingCount = (Long) model.get("noPagingCount");              
            	final ObjectMapper mapper = this.getObjectMapper();
            	this.getRootObjectNode().put("noPagingCount", noPagingCount);
            	final ArrayNode jsonLogsCollection = mapper.createArrayNode();
            
            	for (AbstractAggregateHourLogEntry logEntry : logsCollection) {
                jsonLogsCollection.add(AggregHourLogSerializer.serialize(logEntry, 
                                                                     mapper));
            	}
            	return jsonLogsCollection;
        	}
        }   
        throw new MonitorInterfaceException("An internal error occurred",
                                            "internal.error");
	}

	@Override
	protected Boolean isSuccess() {
		return true;
	}

	
}
