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
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XMLSource;

public class ParseStringToXML extends CurrentBasedIterator {

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		XDMIterator ret = new ParseStringToXML();
		ret.setSubIters(subIters);
		ret.setContext(context, false);
		return ret;
	}

	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		return current.next();
	}
	
	
	public void init() throws MXQueryException {
		XDMIterator parameter = subIters[0];
		Token tok = parameter.next(); 
		int type = Type.getEventTypeSubstituted(tok.getEventType(), Context.getDictionary());
		if (type == Type.END_SEQUENCE) {
			current = new EmptySequenceIterator(context,loc);
			return;
		}
		if (type == Type.STRING || type == Type.UNTYPED_ATOMIC || type == Type.UNTYPED) {
			String add=tok.getText();
			XMLSource cur = XDMInputFactory.createXMLInput(context, new StringReader(add),false,context.getInputValidationMode(),loc);
			Source newSource = context.getStores().createStore(cur);
			current = newSource.getIterator(context);
		} else {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Type "+Type.getTypeQName(type, Context.getDictionary())+" not correct at parse-string-to-XML", loc);
		}
		return;
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		

}
