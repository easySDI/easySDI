/**
 * 
 */
package org.easysdi.monitor.gui.webapp.views.json.serializers;

import java.util.Locale;

import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ObjectNode;
import org.easysdi.monitor.biz.job.Overview;

/**
 * @author BERG3428
 *
 */
public class OverviewSerializer {

	   private Overview overview;
	    
	    /**
	     * Creates a new JSON job transformer.
	     * 
	     * @param   theJob  the job to represent in JSON
	     */
	    public OverviewSerializer(Overview theOverview) {
	        this.setOverview(theOverview);
	    }
	    
	    

	    public JsonNode serialize(boolean includeQueries, boolean includeQueryParams, Locale locale, ObjectMapper mapper) 
	    {
        
	        final ObjectNode jsonOverview = mapper.createObjectNode();
	        
	        jsonOverview.put("id", this.getOverview().getOverviewID());
	        jsonOverview.put("name",this.getOverview().getName());
	        jsonOverview.put("isPublic",this.getOverview().isIsPublic());
	        
	        
	       /* if (includeQueries) {
	            jsonJob.put("queries", 
	                        this.buildQueriesArray(includeQueryParams, locale, 
	                                               mapper));
	        }*/
	        return jsonOverview;
	    }

	    
	    /**
	     * Defines the job to represent.
	     * 
	     * @param   newJob the job to represent in JSON
	     */
	    private void setOverview(Overview newOverview) {

	        if (null == newOverview) {
	            throw new IllegalArgumentException("Overview can't be null");
	        }

	        this.overview = newOverview;
	    }

	    /**
	     * Gets the job to represent.
	     * 
	     * @return  the job to represent in JSON
	     */
	    private Overview getOverview() {
	        return this.overview;
	    }
	
}
