package org.easysdi.monitor.gui.webapp.views.json.serializers;

import java.text.SimpleDateFormat;

import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ObjectNode;
import org.easysdi.monitor.biz.logging.AbstractAggregateLogEntry;
import org.easysdi.monitor.biz.logging.AggregateStats;

/**
 * Transforms an aggregate log entry into JSON.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.2, 2010-08-06
 *
 */
public class AggregLogSerializer {
    
    /**
     * Dummy constructor used to prevent instantiation.
     */
    private AggregLogSerializer() {
        throw new UnsupportedOperationException(
                                            "This class can't be instantiated");
    }
    
    

    /**
     * Generates the JSON representation of an aggregate log entry.
     * 
     * @param   entry   the aggregate log entry to represent
     * @param   mapper  the JSON object used to map the data to JSON nodes
     * @return          the JSON node containing the data for the entry
     */
    public static JsonNode serialize(AbstractAggregateLogEntry entry, 
                                     ObjectMapper mapper) {
        final ObjectNode jsonEntry = mapper.createObjectNode();

        final AggregateStats h24Stats = entry.getH24Stats();
        final AggregateStats slaStats = entry.getSlaStats();
        final SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
        jsonEntry.put("slaNbConnErrors", slaStats.getNbConnErrors());
        jsonEntry.put("h24NbConnErrors", h24Stats.getNbConnErrors());
        jsonEntry.put("slaNbBizErrors", slaStats.getNbBizErrors());
        jsonEntry.put("h24NbBizErrors", h24Stats.getNbBizErrors());
        jsonEntry.put("slaAvailability", slaStats.getAvailability());
        jsonEntry.put("h24Availability", h24Stats.getAvailability());
        jsonEntry.put("slaMeanRespTime", slaStats.getMeanRespTime());
        jsonEntry.put("h24MeanRespTime", h24Stats.getMeanRespTime());
        
        jsonEntry.put("slaMaxRespTime", slaStats.getMaxRespTime());
        jsonEntry.put("h24MaxRespTime", h24Stats.getMaxRespTime());
        jsonEntry.put("slaMinRespTime", slaStats.getMinRespTime());
        jsonEntry.put("h24MinRespTime", h24Stats.getMinRespTime());
        
        jsonEntry.put("date", dateFormat.format(entry.getLogDate().getTime()));

        return jsonEntry;
    }
}
