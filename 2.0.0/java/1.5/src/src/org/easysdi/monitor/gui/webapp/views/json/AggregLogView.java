package org.easysdi.monitor.gui.webapp.views.json;

import java.util.Collection;
import java.util.Locale;
import java.util.Map;

import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ArrayNode;
import org.codehaus.jackson.node.ObjectNode;
import org.easysdi.monitor.biz.logging.AbstractAggregateLogEntry;
import org.easysdi.monitor.gui.webapp.MonitorInterfaceException;
import 
    org.easysdi.monitor.gui.webapp.views.json.serializers.AggregLogSerializer;

/**
 * Displays a set of aggregate logs in JSON format.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.2, 2010-08-06
 *
 */
public class AggregLogView extends AbstractJsonView {
    
    /**
     * Creates a new view.
     */
    public AggregLogView() {
        
    }
    

    
    /**
     * Generates the aggregate logs JSON.
     * 
     * @param   model                       the model data to output
     * @param   locale                      the locale indicating the language 
     *                                      in which localizable data should be 
     *                                      shown
     * @return                              a string containing the JSON code 
     *                                      for the aggregate logs
     * @throws  MonitorInterfaceException   <ul>
     *                                      <li>the model data is invalid</li>
     *                                      <li>an error occurred while 
     *                                      converting the aggregate logs to
     *                                      JSON</li>
     *                                      </ul>                   
     */
    @SuppressWarnings("unchecked")
    @Override
    public JsonNode getResponseData(Map<String, ?> model, Locale locale)
        throws MonitorInterfaceException {

        if (model.containsKey("aggregLogsCollection")) {
            final Collection<AbstractAggregateLogEntry> logsCollection 
                = (Collection<AbstractAggregateLogEntry>) model.get(
                        "aggregLogsCollection");
            final Long noPagingCount = (Long) model.get("noPagingCount");
            final ObjectMapper mapper = this.getObjectMapper();
            this.getRootObjectNode().put("noPagingCount", noPagingCount);
            final ArrayNode jsonLogsCollection = mapper.createArrayNode();

            for (AbstractAggregateLogEntry logEntry : logsCollection) {
                jsonLogsCollection.add(AggregLogSerializer.serialize(logEntry, 
                                                                     mapper));
            }
            
            return jsonLogsCollection;

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
