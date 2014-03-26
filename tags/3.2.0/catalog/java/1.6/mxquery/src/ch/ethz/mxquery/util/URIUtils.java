/*   Copyright 2006 - 2009 ETH Zurich 
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */

package ch.ethz.mxquery.util;

import java.net.URI;
import java.net.URISyntaxException;

import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;

public class URIUtils {
	
	public static boolean isValidURI(String value) {
		try {
			new URI(value);
		} catch (URISyntaxException e) {
			String cleaned = Utils.replaceAll(value, "\\", "/");
			cleaned = Utils.replaceAll(cleaned, "\"", "X");
			cleaned = Utils.replaceAll(cleaned, "\n", "X");
			cleaned = Utils.replaceAll(cleaned, " ", "X");
			cleaned = Utils.replaceAll(cleaned, "#", "X");
			try {
				new URI(cleaned);
			} catch (URISyntaxException f) {
				return false;
			}
			return true;
		}
		return true;
	}
	
	public static boolean isRelativeURI(String value) {
		URI toCheck;
		try {
			toCheck = new URI(value);
		} catch (URISyntaxException e) {
			return false;
		}
		
		if (toCheck.isAbsolute())
			return false;
		return true;
	}

	public static boolean isAbsoluteURI(String value) {
		URI toCheck;
		try {
			toCheck = new URI(value);
		} catch (URISyntaxException e) {
			return false;
		}
		
		if (!toCheck.isAbsolute())
			return false;
		return true;
	}	
	
	public static String resolveURI (String base, String rel, QueryLocation loc) throws MXQueryException {
		try {
			if (!TypeLexicalConstraints.isRelativeURI(rel)) {
				return rel;
			} else {
				URI baseURI = new URI(base);
				return baseURI.resolve(rel).toString();
			}
		} catch (URISyntaxException e) {
			throw new DynamicException (ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Invalid values to resolve URI", loc);
		}
	}
	
}
