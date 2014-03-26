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

package ch.ethz.mxquery.model;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;

public class TokenSequenceIterator extends Iterator {
		Token [] tokens;
		
		public TokenSequenceIterator(Vector inp) {
			super(null,null);
			tokens = new Token[inp.size()];
			for (int i=0;i<tokens.length;i++) {
				tokens[i] = (Token)inp.elementAt(i);
			}
		}
		TokenSequenceIterator(Token [] tok) {
			super(null,null);
			tokens = tok;
		}
		
		
		protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
				throws MXQueryException {
			return new TokenSequenceIterator(tokens);
		}

		public Token next() throws MXQueryException {
			
			if (tokens == null) {
				init();
			}
			
			if (called < tokens.length) {
				return tokens[called++];
			} else
				return Token.END_SEQUENCE_TOKEN;		
		}
		protected void init() {
			tokens = new Token[0];
			// Default case does nothing, to be overwritten 
			// if tokens are not provided using the constructor
		}
		
}
