/**
 * 
 */
package org.easysdi.monitor.gui.webapp.views.json.serializers;

import java.text.SimpleDateFormat;

import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ObjectNode;
import org.easysdi.monitor.biz.logging.AbstractAggregateHourLogEntry;
import org.easysdi.monitor.biz.logging.AggregateStats;

/**
 * @author berg3428
 *
 */
public class AggregHourLogSerializer {

	   /**
     * Dummy constructor used to prevent instantiation.
     */
    private AggregHourLogSerializer() {
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
    public static JsonNode serialize(AbstractAggregateHourLogEntry entry, 
                                     ObjectMapper mapper) {
        final ObjectNode jsonEntry = mapper.createObjectNode();

        final AggregateStats h1Stats = entry.getH1Stats();
        final AggregateStats inspireStats = entry.getInspireStats();
        final SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        jsonEntry.put("inspireNbConnErrors", inspireStats.getNbConnErrors());
        jsonEntry.put("h1NbConnErrors", h1Stats.getNbConnErrors());
        jsonEntry.put("inspireNbBizErrors", inspireStats.getNbBizErrors());
        jsonEntry.put("h1NbBizErrors", h1Stats.getNbBizErrors());
        jsonEntry.put("inspireAvailability", inspireStats.getAvailability());
        jsonEntry.put("h1Availability", h1Stats.getAvailability());
        jsonEntry.put("inspireMeanRespTime", inspireStats.getMeanRespTime());
        jsonEntry.put("h1MeanRespTime", h1Stats.getMeanRespTime());
        
        jsonEntry.put("inspireMaxRespTime", inspireStats.getMaxRespTime());
        jsonEntry.put("h1MaxRespTime", h1Stats.getMaxRespTime());
        jsonEntry.put("inspireMinRespTime", inspireStats.getMinRespTime());
        jsonEntry.put("h1MinRespTime", h1Stats.getMinRespTime());
        
        jsonEntry.put("date", dateFormat.format(entry.getLogDate().getTime()));

        return jsonEntry;
    }
}
