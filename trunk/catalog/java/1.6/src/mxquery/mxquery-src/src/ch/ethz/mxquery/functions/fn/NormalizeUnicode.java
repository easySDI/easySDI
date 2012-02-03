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

import java.text.Normalizer;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class NormalizeUnicode extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		XDMIterator input = subIters[0];
		Token tok = input.next();
		TypeDictionary tdic = Context.getDictionary();
		int type = tok.getEventType();
		if (!(Type.isTypeOrSubTypeOf(type, Type.STRING,tdic)||tok == Token.END_SEQUENCE_TOKEN)) {
			throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "String expected for normalize-unicode, got "+Type.getTypeQName(type, tdic),loc);
		}
		
		if (input.next() != Token.END_SEQUENCE_TOKEN) {
			throw new DynamicException(ErrorCodes.E0001_STATIC_NO_VALUE_ASSIGNED, "More than a single value given to normalize-unicode",loc);
		}
		
		String toNormalize;
		
		if (tok == Token.END_SEQUENCE_TOKEN)
			toNormalize = "";
		else
			toNormalize = tok.getText();

		try {
		Normalizer.Form normalForm = Normalizer.Form.NFC;
		
		
		if (subIters.length == 2) {
			XDMIterator inputNF = subIters[1];
			Token tokNF = inputNF.next();
			int type1 = tokNF.getEventType();
			if (!(Type.isTypeOrSubTypeOf(type1, Type.STRING,tdic))) {
				throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "String expected for normalize-unicode, got "+Type.getTypeQName(type, tdic),loc);
			}
			
			if (inputNF.next() != Token.END_SEQUENCE_TOKEN) {
				throw new DynamicException(ErrorCodes.E0001_STATIC_NO_VALUE_ASSIGNED, "More than a single value given to normalize-unicode",loc);
			}
			
			if (tokNF.getText().equals("")) { 
				currentToken = new TextToken(null,toNormalize);
				return;
			}
			
			if (tokNF.getText().trim().equalsIgnoreCase("NFC")) 
				normalForm = Normalizer.Form.NFC;
			else if (tokNF.getText().trim().equalsIgnoreCase("NFD"))
				normalForm = Normalizer.Form.NFD;
			else if (tokNF.getText().trim().equalsIgnoreCase("NFKC"))
				normalForm = Normalizer.Form.NFKC;
			else if (tokNF.getText().trim().equalsIgnoreCase("NFKD"))
				normalForm = Normalizer.Form.NFKD;
			else
				throw new DynamicException(ErrorCodes.F0011_UNSUPPORTED_NORMALIZATION_FORM, "Normalizaton form "+tokNF.getText()+" not supported",loc);
		}
				
		String normalized = Normalizer.normalize(toNormalize, normalForm);
		currentToken = new TextToken(null,normalized);
		} catch (LinkageError _) {
			throw new DynamicException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Unicode normalization is only supported in JDK6 and higher",loc);
		}

	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		

	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		XDMIterator copy = new NormalizeUnicode();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}

}
