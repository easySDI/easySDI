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
package org.easysdi.proxy.ows;

import java.io.IOException;
import java.lang.reflect.Field;

/**
 * @author DEPTH SA
 *
 */
public class OWSExceptionReport implements OWSIExceptionReport {

    /* (non-Javadoc)
     * @see org.easysdi.proxy.ows.OWSIExceptionReport#generateExceptionReport(java.lang.String, java.lang.String, java.lang.String)
     */
    public StringBuffer generateExceptionReport(String errorMessage,
	    String code, String locator) throws IOException {
	return null;
    }

    /* (non-Javadoc)
     * @see org.easysdi.proxy.ows.OWSIExceptionReport#getHttpCodeDescription(java.lang.String)
     */
    public String getHttpCodeDescription(String code) {
	
	try {
	    String text = "HTTP_CODE_"+code;
	    Class clas = this.getClass();
	    Field f = clas.getField(text);
	    Object o = f.get(this);
	    return o.toString();
	} catch (SecurityException e) {
	    e.printStackTrace();
	} catch (NoSuchFieldException e) {
	    e.printStackTrace();
	} catch (IllegalArgumentException e) {
	    e.printStackTrace();
	} catch (IllegalAccessException e) {
	    e.printStackTrace();
	}
	return null;
    }

}
