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
public class VersionNotSupportedException extends ProxyServletException {

	/**
	 * @param message
	 * @param code
	 * @param locator
	 * @param cause
	 */
	public VersionNotSupportedException(String message, String code,
			String locator,Integer httpCode, Throwable cause) {
		super(message, code, locator,httpCode, cause);
		// TODO Auto-generated constructor stub
	}

	/**
	 * 
	 */
	private static final long serialVersionUID = 1136522124569373687L;

	/**
	 * 
	 */
	public VersionNotSupportedException() {
	}

	/**
	 * @param message
	 */
	public VersionNotSupportedException(String message) {
		super(message);
	}

	/**
	 * @param cause
	 */
	public VersionNotSupportedException(Throwable cause) {
		super(cause);
	}

	/**
	 * @param message
	 * @param cause
	 */
	public VersionNotSupportedException(String message, Throwable cause) {
		super(message, cause);
	}

}
