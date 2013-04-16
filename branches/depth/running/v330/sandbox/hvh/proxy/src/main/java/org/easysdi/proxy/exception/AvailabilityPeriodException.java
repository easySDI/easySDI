package org.easysdi.proxy.exception;

public class AvailabilityPeriodException extends ProxyServletException {

	/**
	 * 
	 */
	public AvailabilityPeriodException() {
		super();
		// TODO Auto-generated constructor stub
	}
	/**
	 * @param message
	 * @param code
	 * @param locator
	 * @param cause
	 */
	public AvailabilityPeriodException(String message, String code,
			String locator,Integer httpCode, Throwable cause) {
		super(message, code, locator, httpCode, cause);
		// TODO Auto-generated constructor stub
	}
	/**
	 * @param message
	 * @param cause
	 */
	public AvailabilityPeriodException(String message, Throwable cause) {
		super(message, cause);
		// TODO Auto-generated constructor stub
	}
	/**
	 * @param cause
	 */
	public AvailabilityPeriodException(Throwable cause) {
		super(cause);
		// TODO Auto-generated constructor stub
	}

	private static final long serialVersionUID = -6405579273588206066L;

	public AvailabilityPeriodException(String message) {
		super(message);
	}
	
	public static final String SERVICE_IS_NULL = "No policy available"; 
	public static final String CURRENT_DATE_BEFORE_SERVICE_FROM_DATE = "Service is not activated yet"; 
	public static final String CURRENT_DATE_AFTER_SERVICE_TO_DATE = "Service is expired"; 
	public static final String SERVICE_DATES_PARSE_ERROR = "Service dates couldn't be parsed"; 
}
