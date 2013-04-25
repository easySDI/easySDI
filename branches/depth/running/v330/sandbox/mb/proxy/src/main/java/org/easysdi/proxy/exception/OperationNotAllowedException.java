/**
 * 
 */
package org.easysdi.proxy.exception;

/**
 * @author Helene
 *
 */
public class OperationNotAllowedException extends ProxyServletException {

	private static final long serialVersionUID = -7245911656325136777L;

	public OperationNotAllowedException(String message, String code,
			String locator,Integer httpCode, Throwable cause) {
		super(message, code, locator,httpCode, cause);
	}

	/**
	 * 
	 */
	public OperationNotAllowedException() {
	}

	/**
	 * @param message
	 */
	public OperationNotAllowedException(String message) {
		super(message);
	}

	/**
	 * @param cause
	 */
	public OperationNotAllowedException(Throwable cause) {
		super(cause);
	}

	/**
	 * @param message
	 * @param cause
	 */
	public OperationNotAllowedException(String message, Throwable cause) {
		super(message, cause);
	}

}
