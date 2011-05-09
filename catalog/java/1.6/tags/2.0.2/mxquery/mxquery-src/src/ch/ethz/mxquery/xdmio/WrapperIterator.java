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

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.AnyURIToken;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.UntypedAtomicToken;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.Iterator;

public abstract class WrapperIterator extends Iterator {

	protected String [] inputValues;
	protected QName [] valueNames;
	protected int [] types;
	protected QName enclosingName;
	
	XDMScope curNsScope = new XDMScope();
	
	protected int currentElement;
	Token currentToken = Token.START_SEQUENCE_TOKEN;
//	String currentName;
//	boolean dataRead;
	int eCount = -1;
	
	public WrapperIterator(Context ctx, QueryLocation location){
		super(ctx,location);
	}

	public Token next() throws MXQueryException {
		if (inputValues == null && !getData()) {
			currentToken = Token.END_SEQUENCE_TOKEN;
		} else {
			if (currentElement <= valueNames.length || getData()) {
				switch (eCount){
				case -1: // open enclosing tag
					currentToken = new NamedToken(Type.START_TAG, null, enclosingName, curNsScope);
					eCount++;
					break;				
				case 0: // opening child tag
					currentToken = new NamedToken(Type.START_TAG, null, valueNames[currentElement], curNsScope);
					eCount++;
					break;
				case 1: // element content
					currentToken = createToken(inputValues[currentElement], types[currentElement]);
					eCount++;
					break;
				case 2: // element content
					currentToken = new NamedToken(Type.END_TAG, null, valueNames[currentElement], curNsScope);
					currentElement++;
					if (currentElement < valueNames.length)
						eCount = 0;
					else 
						eCount = 3;
					break;			
				case 3: // close enclosing tag
					currentToken = new NamedToken(Type.END_TAG, null,enclosingName, curNsScope);
					currentElement++;
					//currentElement = 0;
					eCount = -1;
					break;				
				}
			} else {
				currentToken = Token.END_SEQUENCE_TOKEN;
			}
		}
		return currentToken;
	}
	
	public Token createToken(String value, int type) throws MXQueryException {
		int savedType = type;
		Token myToken;
		type = Type.getEventTypeSubstituted(type, Context.getDictionary());
		switch (type) {
		case Type.STRING:
			myToken = new TextToken(type, null, value, curNsScope);
		break;

		case Type.UNTYPED_ATOMIC:
			myToken = new UntypedAtomicToken(null, value);
		break;
		
		case Type.UNTYPED:
			myToken = new TextToken(type,null,value, curNsScope);
			break;
		case Type.ANY_URI:
			myToken = new AnyURIToken(null, value);
		break;
		// Extend for additional types when needed
		default:
			throw new DynamicException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Unsupported type passed to the WrapperIterator: " + Type.getTypeQName(savedType, Context.getDictionary()),QueryLocation.OUTSIDE_QUERY_LOC );
		}
		return myToken;
	}		
	
	protected abstract boolean getData();
}
