package org.easysdi.monitor.gui.webapp.views.json.serializers;

import java.text.SimpleDateFormat;
import java.util.Locale;

import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ObjectNode;
import org.easysdi.monitor.biz.logging.RawLogEntry;

/**
 * Utility class for representing raw log entries in JSON format.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.2, 2010-08-06
 *
 */
public final class RawLogSerializer {
    
    private static final String DATE_TIME_FORMAT = "yyyy-MM-dd HH:mm:ss";
    
    
    
    /**
     * No-argument private constructor used to prevent instantiation.
     */
    private RawLogSerializer() {
        throw new UnsupportedOperationException(
                                           "This class can't be instantiated.");
    }

    
    /**
     * Generates the JSON representation of raw log entry.
     * 
     * @param   entry           the raw log entry to represent
     * @param   addQueryId      <code>true</code> to add the query identifier to
     *                          the representation
     * @param   locale          the locale to use to display the information
     * @param   mapper          the object used to map the data to JSON nodes
     * @return                  a JSON object containing the log entry's
     *                          information
     */
    public static JsonNode serialize(RawLogEntry entry, Boolean addQueryId,
                                       Locale locale, ObjectMapper mapper,Boolean isSummary) {

        final ObjectNode jsonEntry = mapper.createObjectNode();
        final SimpleDateFormat dateFormat 
            = new SimpleDateFormat(RawLogSerializer.DATE_TIME_FORMAT);

        jsonEntry.put("time",
                      dateFormat.format(entry.getRequestTime().getTime()));
        jsonEntry.put("status", entry.getStatus().getDisplayString(locale));
        jsonEntry.put("statusCode", entry.getStatusValue().name());

        if (addQueryId) {
            jsonEntry.put("queryId", entry.getQueryId());
        }

        jsonEntry.put("message", entry.getMessage());
        jsonEntry.put("size", entry.getResponseSize());
        jsonEntry.put("delay", entry.getResponseDelay());
        jsonEntry.put("httpCode", ((null != entry.getHttpCode()) 
                                  ? entry.getHttpCode().toString() : ""));
        if(isSummary)
        {
        	jsonEntry.put("avCount",entry.getAvCount());
        	jsonEntry.put("unavCount",entry.getUnavCount());
        	jsonEntry.put("fCount",entry.getfCount());
        	jsonEntry.put("otherCount",entry.getOtherCount());
        	jsonEntry.put("maxTime",entry.getMaxTime());
        }

        return jsonEntry;
    }
}
