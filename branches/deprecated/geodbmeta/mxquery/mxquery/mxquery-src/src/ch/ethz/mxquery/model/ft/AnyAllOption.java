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

package ch.ethz.mxquery.model.ft;


/**
 *  FTAnyAllOption: FTWords [any, all, all words, any word, phrase]
 *  @author jimhof
 */
public class AnyAllOption {

	public static final int ANY_ALL_OPT_ANY = 0;
	public static final int ANY_ALL_OPT_ANYWORD = 1;
	public static final int ANY_ALL_OPT_ALL = 2;
	public static final int ANY_ALL_OPT_ALLWORDS = 3;
	public static final int ANY_ALL_OPT_PHRASE = 4;
	
	private int anyAllValue = 0;
	
	public AnyAllOption(int anyAllVal){
		anyAllValue = anyAllVal;
	}
	
	public AnyAllOption copy() {
		return new AnyAllOption(anyAllValue);
	}

	public int getAnyAllValue() {
		return anyAllValue;
	}
}
