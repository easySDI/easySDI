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

package ch.ethz.mxquery.xdmio;

import java.io.Reader;

import org.xmlpull.v1.XmlPullParser;

import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.xdmio.XMLSource;
import ch.ethz.mxquery.xdmio.xmlAdapters.XPPImportAdapter;

public class XDMInputFactory {
	/**
	 * Get an input adapter from a textual XML stream taken from a reader to XDM 
	 * @param ctx
	 * @param xml Reader to get input from
	 * @param doc 
	 * @param validate Shall the input be validated
	 * @param location 
	 * @param validationMode TODO
	 * @return
	 */
	
	public static XMLSource createXMLInput(Context ctx, Reader xml, boolean doc, int valMode, QueryLocation location) throws MXQueryException {
		if (valMode == Context.NO_VALIDATION)
			return new XPPImportAdapter(ctx,xml,doc,location);
		else {
			throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"XML Input validation not supported on CLDC",location);	
		}
	}
	public static XMLSource createXPPInput(Context ctx, XmlPullParser pullParser, QueryLocation loc) throws MXQueryException{
		return new XPPImportAdapter(ctx,pullParser,true,loc);
	}
	public static XMLSource createTidyInput(Context ctx, Reader xml, QueryLocation location) throws MXQueryException {
		throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"Tidying input not supported on CLDC",location);
	}

}
