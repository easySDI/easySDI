package org.easysdi.monitor.gui.webapp.views.json;

import java.util.Locale;
import java.util.Map;
import java.util.Set;

import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ArrayNode;
import org.codehaus.jackson.node.ObjectNode;
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

        if (model.containsKey("rawLogsCollection")
            && model.containsKey("addQueryId")) {
            
            final Set<RawLogEntry> logsCollection 
                = (Set<RawLogEntry>) model.get("rawLogsCollection");
            final Boolean addQueryId = (Boolean) model.get("addQueryId");
            final Long noPagingCount = (Long) model.get("noPagingCount");
            final ObjectMapper mapper = this.getObjectMapper();
            final ObjectNode jsonDataObject = mapper.createObjectNode();
            jsonDataObject.put("noPagingCount", noPagingCount);
            final ArrayNode rowsCollection = mapper.createArrayNode();

            for (RawLogEntry logEntry : logsCollection) {
                rowsCollection.add(RawLogSerializer.serialize(logEntry,
                                                              addQueryId,
                                                              locale, mapper));
            }
            jsonDataObject.put("rows", rowsCollection);

            return jsonDataObject;
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
