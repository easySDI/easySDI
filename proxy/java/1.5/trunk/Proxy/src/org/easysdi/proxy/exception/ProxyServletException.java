/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */
package org.easysdi.proxy.exception;

/**
 * @author DEPTH SA
 *
 */
public class ProxyServletException extends RuntimeException {

	/**
	 * 
	 */
	private static final long serialVersionUID = 2620471556738045649L;

	/**
	 * 
	 */
	public ProxyServletException() {
	}

	/**
	 * @param message
	 */
	public ProxyServletException(String message) {
		super(message);
	}

	/**
	 * @param cause
	 */
	public ProxyServletException(Throwable cause) {
		super(cause);
	}

	/**
	 * @param message
	 * @param cause
	 */
	public ProxyServletException(String message, Throwable cause) {
		super(message, cause);
	}

}
