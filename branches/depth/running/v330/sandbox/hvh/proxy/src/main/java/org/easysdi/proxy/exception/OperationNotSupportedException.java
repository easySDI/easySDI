/**
 * 
 */
package org.easysdi.proxy.exception;

/**
 * @author Helene
 *
 */
public class OperationNotSupportedException extends RuntimeException {

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
