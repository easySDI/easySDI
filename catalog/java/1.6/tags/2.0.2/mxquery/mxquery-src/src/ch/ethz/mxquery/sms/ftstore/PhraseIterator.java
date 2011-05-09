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

package ch.ethz.mxquery.sms.ftstore;

import java.util.Vector;

import ch.ethz.mxquery.datamodel.adm.FTToken;
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Helper iterator (over phrases)
 * @author jimhof
 */

public class PhraseIterator{
	
	private Vector tokens;
	private int called = 0;
	
	public static final int FLAT_PHRASE = 0;
	public static final int NESTED_PHRASE = 1;
	
	private int type = NESTED_PHRASE;
	
	public PhraseIterator(Vector tokens, int type){
		this.tokens = tokens;
		this.type = type;
	}
	
	public FTToken next() throws MXQueryException {
		if (tokens != null){
			if (this.called == 0) {
				if (tokens.size() == 0){
					return null;
				}
			}
			
			if (this.called < tokens.size()){
				return (FTToken)tokens.elementAt(called++);
			}
		}
		return null;
	}

	public void reset(){
		called = 0;
	}
	
	public boolean hasNext(){
		if (this.tokens == null){
			return false;
		}
		return this.called < tokens.size();
	}

	public int getNumberOfElements(){
		return this.tokens.size();
	}

	public int getType() {
		return type;
	}

}
