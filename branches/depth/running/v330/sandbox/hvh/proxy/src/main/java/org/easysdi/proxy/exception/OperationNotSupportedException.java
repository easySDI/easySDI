/**
 * 
 */
package org.easysdi.proxy.exception;

/**
 * @author Helene
 *
 */
public class OperationNotSupportedException extends ProxyServletException {

	/**
	 * @param message
	 * @param code
	 * @param locator
	 * @param cause
	 */
	public OperationNotSupportedException(String message, String code,
			String locator, Integer httpCode, Throwable cause) {
		super(message, code, locator, httpCode, cause);
		// TODO Auto-generated constructor stub
	}

	private static final long serialVersionUID = 45016549040269792L;

	public OperationNotSupportedException() {
		super();
	}

	public OperationNotSupportedException(String message, Throwable cause) {
		super(message, cause);
	}

	public OperationNotSupportedException(String message) {
		super(message);
	}

	public OperationNotSupportedException(Throwable cause) {
		super(cause);
	}

	
}
