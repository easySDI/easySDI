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

package ch.ethz.mxquery.iterators.update;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.TextAttrToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

class StripTypeIterator extends CurrentBasedIterator {
	
	public StripTypeIterator(Context context, XDMIterator[] subIterators, QueryLocation queryLocation) {
		super(context,subIterators,queryLocation);
		current = subIters[0];
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		// TODO Auto-generated method stub
		return new StripTypeIterator(context, subIters, this.loc);
	}

	public Token next() throws MXQueryException {
		Token cur = current.next();
		if (Type.isAttribute(cur.getEventType())) {
			NamedToken nm = (NamedToken)cur;
			QName qn = new QName(cur.getName());
			qn.setNamespaceURI(nm.getNS());
			cur = new TextAttrToken(Type.UNTYPED_ATOMIC,null,nm.getValueAsString(),qn,cur.getDynamicScope());
		} 
		if (cur.getEventType() == Type.START_TAG) {
			NamedToken nm = ((NamedToken)cur).copyStrip();
			nm.setIDREF(null);
			nm.setID(null);
			cur = nm;
		}
		return cur;
	}
	
}
