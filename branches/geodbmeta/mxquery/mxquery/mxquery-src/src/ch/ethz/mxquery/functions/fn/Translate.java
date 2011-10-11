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

import java.util.Hashtable;
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

public class Translate extends TokenBasedIterator {
	private Hashtable map = new Hashtable();
	private Vector remove = new Vector();
	
	protected void init() throws MXQueryException {
		Token arg = subIters[0].next();
		Token mapString = subIters[1].next();
		Token transString = subIters[2].next();
		
		if (arg.getEventType() == Type.END_SEQUENCE) {
			currentToken = new TextToken(null, "");
			return;
		}
		
		if (arg.getEventType() != Type.STRING || 
				mapString.getEventType() != Type.STRING ||
				transString.getEventType() != Type.STRING) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type for fn:translate!", loc);
		}
		
		String sArg = arg.getText();
		String sMap = mapString.getText();
		String sTrans = transString.getText();
		
		int transPos = 0;
		for (int i=0; i<sMap.length(); i++) {
			int sMapChar = sMap.codePointAt(i);
			if (sMapChar > 65535) i++;
			if (sTrans.length() > transPos) {
				// sTrans contains the character mapping
				int tMapChar = sTrans.codePointAt(transPos);
				if (tMapChar > 65535) transPos++;
				map.put(new Integer(sMapChar), new Integer(tMapChar));
			} else {
				remove.addElement(new Integer(sMapChar));
			}
			transPos++;
		}
		
		
		currentToken = new TextToken(null, translate(sArg));
	}
	
	private String translate(String input) {
		StringBuffer result = new StringBuffer();
		
		for (int i=0; i<input.length(); i++) {
			Integer character = new Integer(input.codePointAt(i));
			if (!remove.contains(character)) {
				if (map.containsKey(character)) {
					result.append(Character.toChars(((Integer)map.get(character)).intValue()));
				} else {
					result.append(Character.toChars(character.intValue()));
				}
			}
			if (character.intValue() > 65535) i++;
		}
		
		return result.toString();
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Translate();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}
