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
        
        jsonEntry.put("h24Unavailability", h24Stats.getUnavailability());
        jsonEntry.put("slaUnavailability",slaStats.getUnavailability());
        jsonEntry.put("h24Failure", h24Stats.getFailure());
        jsonEntry.put("slaFailure",slaStats.getFailure());
        jsonEntry.put("h24Untested", h24Stats.getUntested());
        jsonEntry.put("slaUntested",slaStats.getUntested());
        
        jsonEntry.put("date", dateFormat.format(entry.getLogDate().getTime()));

        return jsonEntry;
    }
    
    /**
     * Generates the JSON representation of an aggregate log entry.
     * 
     * @param   entry   the aggregate log entry to represent
     * @param   mapper  the JSON object used to map the data to JSON nodes
     * @return          the JSON node containing the data for the entry
     */
    public static JsonNode serialize(AbstractAggregateLogEntry entry, 
                                     ObjectMapper mapper,String jobName,
                                     String queryName) {
        final ObjectNode jsonEntry = mapper.createObjectNode();

        final AggregateStats h24Stats = entry.getH24Stats();
        final AggregateStats slaStats = entry.getSlaStats();
        final SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
        jsonEntry.put("jobname", jobName);
        jsonEntry.put("slaname", "Default");
        jsonEntry.put("date", dateFormat.format(entry.getLogDate().getTime()));
        if(queryName != "")
        {
        	jsonEntry.put("requestname",queryName);
        }
        jsonEntry.put("slaavailability", slaStats.getAvailability());
        jsonEntry.put("slaunavailability",slaStats.getUnavailability());
        jsonEntry.put("slafailure",slaStats.getFailure());
        jsonEntry.put("slauntested",slaStats.getUntested());
        jsonEntry.put("h24availability", h24Stats.getAvailability());
        jsonEntry.put("h24unavailability", h24Stats.getUnavailability());
        jsonEntry.put("h24failure", h24Stats.getFailure());
        jsonEntry.put("h24untested", h24Stats.getUntested());
        jsonEntry.put("slamaxresponsetime", slaStats.getMaxRespTime());
        jsonEntry.put("slaminresponsetime", slaStats.getMinRespTime());
        jsonEntry.put("slameanresponsetime", slaStats.getMeanRespTime());
        jsonEntry.put("h24maxresponsetime", h24Stats.getMaxRespTime());
        jsonEntry.put("h24minresponsetime", h24Stats.getMinRespTime());
        jsonEntry.put("h24meanresponsetime", h24Stats.getMeanRespTime());
        jsonEntry.put("slaconnerrors", slaStats.getNbConnErrors());
        jsonEntry.put("h24connerrors", h24Stats.getNbConnErrors());
        jsonEntry.put("slabizerrors", slaStats.getNbBizErrors());
        jsonEntry.put("h24bizerrors", h24Stats.getNbBizErrors());
        return jsonEntry;
    }
}
