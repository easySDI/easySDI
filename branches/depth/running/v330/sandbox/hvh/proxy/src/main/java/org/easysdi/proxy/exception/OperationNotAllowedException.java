/**
 * 
 */
package org.easysdi.proxy.exception;

/**
 * @author Helene
 *
 */
public class OperationNotAllowedException extends RuntimeException {

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
