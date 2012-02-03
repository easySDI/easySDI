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

package ch.ethz.mxquery.functions.fn;

import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Set;

public class Deep_equal extends TokenBasedIterator {
	
	String coll = null;
	Set collations;
	Token t1, t2;
	
	protected void init() throws MXQueryException {
//		if (subIters[0] == null || subIters.length != 2) {
//			throw new IllegalArgumentException();
//	}
		Window win1, win2;

		
		win1 = WindowFactory.getNewWindow(getContext(), subIters[0]);
		win2 = WindowFactory.getNewWindow(getContext(), subIters[1]);
		
		if (subIters.length > 2 ){
			XDMIterator collIter = subIters[2];
			Token collToken = collIter.next();
			if (collToken == Token.END_SEQUENCE_TOKEN || 
					!Type.isTypeOrSubTypeOf(collToken.getEventType(),Type.STRING, null))
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Wrong type for collation", loc);
			coll = collToken.getText();
			collations = context.getCollations();
		}
		
		
		while (win1.hasNextItem()) {
			if (!win2.hasNextItem()) {
				currentToken = BooleanToken.FALSE_TOKEN;
				return;
			}
			
			XDMIterator item1 = win1.nextItem();
			XDMIterator item2 = win2.nextItem();
			
			if (!checkItemDeep(item1, item2)) {
				currentToken = BooleanToken.FALSE_TOKEN;
				return;
			}
			
		}
		if (win2.hasNextItem()) {
			currentToken = BooleanToken.FALSE_TOKEN;
		} else {
			currentToken = BooleanToken.TRUE_TOKEN;
		}
	}

	private boolean checkItemDeep(XDMIterator item1, XDMIterator item2) throws MXQueryException{
		boolean inNode = false;	
		t1 = item1.next();
		t2 = item2.next();
		
		while (t1 != Token.END_SEQUENCE_TOKEN && t2 != Token.END_SEQUENCE_TOKEN) {
			int t1Ev = t1.getEventType();
			int t2Ev = t2.getEventType();

			if (Type.isTextNode(t1Ev))
				t1Ev = Type.getTextNodeValueType(t1Ev);
			if (Type.isTextNode(t2Ev))
				t2Ev = Type.getTextNodeValueType(t2Ev);

			if ((Type.isAtomicType(t1Ev, null)||t1Ev == Type.UNTYPED) && (Type.isAtomicType(t2Ev, null)||t2Ev == Type.UNTYPED)) {
				try {
					// TODO: support NaN
//					if (Type.isNumericPrimitiveType(t1.getEventType()) && Type.isNumericPrimitiveType(t2.getEventType())
//					&& (t1.getNumber().isNaN() ^ t2.getNumber().isNaN())) {
//					currentToken = new BooleanToken(null, false);
//					return;
//					}

					if (coll != null && (Type.isTypeOrSubTypeOf(t1Ev, Type.STRING, null) || Type.isTypeOrSubTypeOf(t2Ev, Type.STRING, null))  
							&& !collations.contains(coll))
						throw new DynamicException(ErrorCodes.F0010_UNSUPPORTED_COLLATION, "Unsupported Collation", loc);

					if (t1.compareTo(t2) != 0 && t1.compareTo(t2) != -3) {
						return false;
					}
				} 
				catch (MXQueryException ex) {
					if (ex instanceof DynamicException) {
						DynamicException exd = (DynamicException)ex;
						if (exd.getErrorCode() == ErrorCodes.F0010_UNSUPPORTED_COLLATION)
							throw exd;
					}
					return false;
				}

			} else if (t1Ev == t2Ev && !Type.isAtomicType(t1Ev, null) && !Type.isAtomicType(t2Ev, null)) {
				switch (t1Ev) {
				case Type.START_TAG: 
					if (!t1.getName().equals(t2.getName())) {
						return false;
					}
					inNode = true;
					Hashtable attributes1 = collectAttributes(item1, true);
					Hashtable attributes2 = collectAttributes(item2, false);
					if (attributes1.size() != attributes2.size()) {
						return false;
					}
					Enumeration atts = attributes1.keys();
					while (atts.hasMoreElements()) {
						QName qn = (QName)atts.nextElement();
						if (!attributes2.containsKey(qn))
							return false;
						else {
							Token att1 = (Token)attributes1.get(qn);
							Token att2 = (Token)attributes2.get(qn);
							if (!att1.getText().equals(att2.getText()))
								return false;
						}
					}
					continue;
				case Type.END_TAG:
					if (!t1.getName().equals(t2.getName())) {
						return false;
					}
					break;
				case Type.START_DOCUMENT:
				case Type.END_DOCUMENT:
					inNode = true;
					break;
				case Type.COMMENT:
					if (!inNode && !t1.getText().equals(t2.getText())) {
						return false;
					}
					break;
				case Type.PROCESSING_INSTRUCTION:
					if (!inNode && !t1.equals(t2)) {
						return false;
					}
				default: 
					if (Type.isAttribute(t1Ev)) {
						if (!t1.getName().equals(t2.getName()) || !t1.getValueAsString().equals(t2.getValueAsString())) {
							return false;
						}
					} else {
						return false;
					}
				}
			} else {
				if (inNode) {
					if (t1Ev == Type.PROCESSING_INSTRUCTION || t1Ev == Type.COMMENT) {
						t1 = item1.next();
						continue;
					}
					if (t2Ev == Type.PROCESSING_INSTRUCTION || t2Ev == Type.COMMENT) {
						t2 = item2.next();
						continue;
					}
				}
				return false;
			}
			t1 = item1.next();
			t2 = item2.next();
		}
		if (t1 == t2 && t1 == Token.END_SEQUENCE_TOKEN) {
			return true;
		}
		return false;
	}
	
	private Hashtable collectAttributes(XDMIterator cur, boolean firstItem) throws MXQueryException{
		Hashtable collectedAttributes = new Hashtable();
		Token tok = cur.next();
		while (Type.isAttribute(tok.getEventType())) {
			QName qn = new QName(tok.getName());
			qn.setNamespaceURI(tok.getNS());
			collectedAttributes.put(qn, tok);
			tok = cur.next();
		}
		if (firstItem)
			t1 = tok;
		else
			t2 = tok;
		
		return collectedAttributes;
	}
	
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Deep_equal();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}
