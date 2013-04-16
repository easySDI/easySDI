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
public class InvalidServiceNameException extends ProxyServletException {

	private static final long serialVersionUID = -6089495385047608602L;

	/**
	 * 
	 */
	public InvalidServiceNameException() {
	}

	/**
	 * @param message
	 */
	public InvalidServiceNameException(String message) {
		super(message);
	}

	/**
	 * @param cause
	 */
	public InvalidServiceNameException(Throwable cause) {
		super(cause);
	}

	/**
	 * @param message
	 * @param cause
	 * @param code
	 * @param locator
	 */
	public InvalidServiceNameException(String message, String code,
			String locator, Integer httpCode,Throwable cause) {
		super(message, code, locator,httpCode, cause);
	}

	/**
	 * @param message
	 * @param cause
	 */
	public InvalidServiceNameException(String message, Throwable cause) {
		super(message, cause);
	}

}
