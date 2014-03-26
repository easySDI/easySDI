package org.easysdi.publish.gui;

import java.util.HashMap;
import java.util.Map;

public class ExceptionMessage {
	private static Map<String, String> ERROR_DEFINITION_MAP = null;
	//Exception definition
    
	private static void init(){
		ERROR_DEFINITION_MAP = new HashMap<String, String>();
		ERROR_DEFINITION_MAP.put("DataInput", "Wrong input value: ");
		ERROR_DEFINITION_MAP.put("FeatureSource", "Problem accessing the Feature Source: ");
		ERROR_DEFINITION_MAP.put("Layer", "Problem accessing the layer: ");
		ERROR_DEFINITION_MAP.put("Diffuser", "Problem accessing the diffuser: ");	
		ERROR_DEFINITION_MAP.put("PublishConfiguration", "The server has a configuration troubles: ");
		ERROR_DEFINITION_MAP.put("Transformation", "Error performing the transformation: ");
		ERROR_DEFINITION_MAP.put("PublishGeneral", "The system encountered a fatal error: ");
		ERROR_DEFINITION_MAP.put("Publication", "Error performing the publication: ");
		ERROR_DEFINITION_MAP.put("UnknownIdentifier", "The execute type %s is not known.");
		ERROR_DEFINITION_MAP.put("UndefinedIdentifier", "The identifier is not defined.");
	}
	
	public static String get(String value){
		if(ERROR_DEFINITION_MAP == null)
			ExceptionMessage.init();
		return ERROR_DEFINITION_MAP.get(value);
	}
	
}
