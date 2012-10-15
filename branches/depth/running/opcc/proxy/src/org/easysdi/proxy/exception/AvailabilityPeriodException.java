package org.easysdi.proxy.exception;

public class AvailabilityPeriodException extends RuntimeException {

	private static final long serialVersionUID = -6405579273588206066L;

	public AvailabilityPeriodException(String message) {
		super(message);
	}
	
	public static final String SERVICE_IS_NULL = "No policy available"; 
	public static final String CURRENT_DATE_BEFORE_SERVICE_FROM_DATE = "Service is not activated yet"; 
	public static final String CURRENT_DATE_AFTER_SERVICE_TO_DATE = "Service is expired"; 
	public static final String SERVICE_DATES_PARSE_ERROR = "Service dates couldn't be parsed"; 
}
