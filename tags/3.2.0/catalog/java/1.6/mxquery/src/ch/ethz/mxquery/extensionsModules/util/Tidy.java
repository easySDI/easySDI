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

package ch.ethz.mxquery.extensionsModules.util;


import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XDMSerializerSettings;
import ch.ethz.mxquery.xdmio.XMLSource;

public class Tidy extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		XDMIterator parameter = subIters[0];
		Token tok = parameter.next(); 
		int type = Type.getEventTypeSubstituted(tok.getEventType(), Context.getDictionary());
		if (type == Type.END_SEQUENCE) {
			currentToken = Token.END_SEQUENCE_TOKEN;
			return;
		}
		if (type == Type.STRING || type == Type.UNTYPED_ATOMIC || type == Type.UNTYPED) {
			String add=tok.getText();
			XMLSource cur = XDMInputFactory.createTidyInput(context, new StringReader(add), loc);
			XDMSerializerSettings set = new XDMSerializerSettings();
			set.setOmitXMLDeclaration(true);
			XDMSerializer ser = new XDMSerializer(set);
			currentToken = new TextToken(null,ser.eventsToXML(cur));
		} else {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Type "+Type.getTypeQName(type, Context.getDictionary())+" not correct at parse-string-to-XML", loc);
		}
		return;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		XDMIterator copy = new Tidy();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}
}
