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

/**
 * @author jimhof
 * Helper class for Phrase generation
 */
public class PhraseToken {

	private String word;
	private Vector list;
	private boolean nextWordPhraseToken = false;
	
	public PhraseToken(String word){
		this.word = word;
	}
	
	public String getWord(){
		return this.word;
	}
	
	public void setList(Vector list){
		this.list = list;
	}
	
	public Vector getList(){
		return this.list;
	}
	
	public void setNextWordFlag(){
		this.nextWordPhraseToken = true;
	}
	
	public boolean isNextWordPhraseToken(){
		return this.nextWordPhraseToken;
	}
	
}
