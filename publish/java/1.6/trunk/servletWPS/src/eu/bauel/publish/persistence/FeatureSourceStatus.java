package eu.bauel.publish.persistence;

public enum FeatureSourceStatus { 
	AVAILABLE("AVAILABLE"),
	CREATING("CREATING"),
	UPDATING("UPDATING"),
	UNAVAILABLE("UNAVAILABLE")
	;
	
	private String text;
	private FeatureSourceStatus(String s) {
		text = s; 
	} 
	public String getText() {
		return text; 
	}
}