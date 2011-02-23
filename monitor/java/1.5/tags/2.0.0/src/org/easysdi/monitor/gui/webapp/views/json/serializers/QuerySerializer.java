package org.easysdi.monitor.gui.webapp.views.json.serializers;

import java.util.Locale;

import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ArrayNode;
import org.codehaus.jackson.node.ObjectNode;
import org.easysdi.monitor.biz.job.Query;
import org.easysdi.monitor.biz.job.QueryConfiguration;
import org.easysdi.monitor.biz.job.QueryParam;

/**
 * Tranforms a query into JSON.
 * 
 * @author Yves Grasset - arx iT
 * @version 1.2, 2010-08-06
 *
 */
public class QuerySerializer {
    
    /**
     * Dummy constructor used to prevent instantiation.
     */
    private QuerySerializer() {
        throw new UnsupportedOperationException(
                                           "This class can't be instantiated.");
    }
    
    

    /**
     * Generates the JSON representation of a query.
     * 
     * @param   query           the query to represent in JSON
     * @param   includeParams   <code>true</code> to include the parameters of
     *                          the query
     * @param   locale          the locale indicating the language in which 
     *                          localizable data should be displayed
     * @param   mapper          the object used to map the data to JSON nodes
     * @return                  a JSON node containing the data for the query
     */
    public static JsonNode serialize(Query query, boolean includeParams,
                                     Locale locale, ObjectMapper mapper) {

        if (null == query) {
            throw new IllegalArgumentException("Query can't be null");
        }

        final ObjectNode jsonQuery = mapper.createObjectNode();
        final QueryConfiguration queryConfig = query.getConfig();
        jsonQuery.put("id", query.getQueryId());
        jsonQuery.put("name", queryConfig.getQueryName());
        jsonQuery.put("status", query.getStatus().getDisplayString(locale));
        jsonQuery.put("statusCode", query.getStatusValue().name());
        jsonQuery.put("serviceMethod", queryConfig.getMethod().getName());

        if (includeParams) {
            jsonQuery.put("params", 
                          QuerySerializer.buildParamsArray(queryConfig, mapper)
            );
        }

        return jsonQuery;
    }



    /**
     * Generates the JSON representation for the parameters of a query.
     * 
     * @param   queryConfig the query configuration containing the parameters
     * 
     * @param   mapper      the object used to map the data to JSON nodes
     * @return              a JSON node containing the data for the parameters
     */
    private static ArrayNode buildParamsArray(QueryConfiguration queryConfig, 
                                              ObjectMapper mapper) {

        if (null == queryConfig) {
            throw new IllegalArgumentException("Query config can't be null");
        }

        final ArrayNode paramsArray = mapper.createArrayNode();

        for (QueryParam param : queryConfig.getParams()) {
            paramsArray.add(QueryParamSerializer.serialize(param, mapper));
        }

        return paramsArray;
    }

}
