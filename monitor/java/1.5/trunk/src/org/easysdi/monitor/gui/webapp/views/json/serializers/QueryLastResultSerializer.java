package org.easysdi.monitor.gui.webapp.views.json.serializers;

import java.util.Locale;


import org.codehaus.jackson.JsonNode;
import org.codehaus.jackson.map.ObjectMapper;
import org.codehaus.jackson.node.ObjectNode;
import org.easysdi.monitor.biz.job.OverviewLastQueryResult;

public class QueryLastResultSerializer {
	private OverviewLastQueryResult lastResult;

	public QueryLastResultSerializer(OverviewLastQueryResult lastResult){
		this.setLastResult(lastResult);
	}

	public JsonNode serialize(Locale locale, ObjectMapper mapper) {
		final ObjectNode jsonOverview = mapper.createObjectNode();
		if(this.getLastResult() != null){
//			jsonOverview.put("deliveryTime", this.getLastResult().getDeliveryTime());
//			jsonOverview.put("normTime", this.getLastResult().getNormTime());
//			jsonOverview.put("size", this.getLastResult().getSize());
//			jsonOverview.put("normSize", this.getLastResult().getNormSize());
//			jsonOverview.put("foundOutput", this.getLastResult().getFoundOutput());
//			jsonOverview.put("normOutput", this.getLastResult().getNormOutput());
			jsonOverview.put("picture_url", this.getLastResult().getPictureUrl());
			jsonOverview.put("xml_result", this.getLastResult().getXmlResult());
			jsonOverview.put("text_result", this.getLastResult().getTextResult());
		}
		else
		{
//			jsonOverview.put("deliveryTime", (String)null);
//			jsonOverview.put("normTime", (String)null);
//			jsonOverview.put("size", (String)null);
//			jsonOverview.put("normSize", (String)null);
//			jsonOverview.put("foundOutput", (String)null);
//			jsonOverview.put("normOutput", (String)null);
			jsonOverview.put("picture_url", (String)null);
			jsonOverview.put("xml_result", (String)null);
			jsonOverview.put("text_result", (String)null);
		}
		return jsonOverview;
	}

	public void setLastResult(OverviewLastQueryResult lastResult) {
		this.lastResult = lastResult;
	}

	public OverviewLastQueryResult getLastResult() {
		return lastResult;
	}
}
