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

package ch.ethz.mxquery.datamodel.xdm;

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;


public final class ProcessingInstrToken extends Token {
	private final String contentValue;
	private String targetName;
	// additional properties to be implemented:
//	private String parent = null; 
//	private String baseURI = null;

	public ProcessingInstrToken(Identifier id, String val, String name, XDMScope curscope) throws MXQueryException{
		super(Type.PROCESSING_INSTRUCTION, id, curscope);
		if ( val != null &&  val.indexOf("?>") > -1 )
			throw new DynamicException(ErrorCodes.E0026_DYNAMIC_COMPUTED_PI_INVALID_CONTENT, "Characters '?>' not allowed", null);
		// target should be of type xs:NCName -> TODO more checks
		if ( name != null && name.indexOf(":") > -1 )
			throw new DynamicException(ErrorCodes.E0026_DYNAMIC_COMPUTED_PI_INVALID_CONTENT, "Character ':' not allowed", null);
		
		this.targetName = name; 
		if (val == null || val.equals(""))
				this.contentValue = null;
		else
			if (val.trim().length() > 0 )
				this.contentValue = " "+val;
			else
				this.contentValue = val;
		this.dynamicScope = curscope;
	}
	
	public ProcessingInstrToken(ProcessingInstrToken token) {
		super(token);
		this.targetName = token.targetName;
		this.contentValue = token.contentValue;
	}
	
	public String getText(){
		int wsPos = 0;
		if (contentValue == null)
			return "";
		while (wsPos < contentValue.length() && contentValue.charAt(wsPos) == ' ' ) {
			wsPos++;
		}
		if (wsPos < contentValue.length())
			return contentValue.substring(wsPos);
		return contentValue;
	}
	
	public String getValueAsString() {
		if (contentValue == null)
			return targetName;
		
		return targetName + contentValue;
	}
	
	public String getName() {
		return targetName;
	}	
	
	public String getLocal() {
		return targetName;	
	}

	public Token copy() {
		return new ProcessingInstrToken(this);
	}
}
