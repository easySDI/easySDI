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

import ch.ethz.mxquery.datamodel.QName;

public class Wildcard {
	private String part;
	private boolean hasWildcardForPrefix;
	
	public Wildcard(String part, boolean hasWildcardForPrefix) {
		this.part = part;
		this.hasWildcardForPrefix = hasWildcardForPrefix;
	}
	
	public boolean equals(Object obj){
		if (obj instanceof Wildcard){
			Wildcard eq = (Wildcard)obj;
			return (this.part.equals(eq.part) && this.hasWildcardForPrefix == eq.hasWildcardForPrefix);
		} 
		return false;
	}
	/**
	 * Check if the QName given in eq corresponds to this wildcard 
	 * @param eq QName to check
	 * @return true if the given wildcard covers t
	 */
	public boolean coversQName(QName eq) {
			boolean b;
			if (this.hasWildcardForPrefix){
				b = this.part.equals(eq.getLocalPart());
			}else{
				if (part.equals("*"))
					b = true;
				else
					b = this.part.equals(eq.getNamespacePrefix());
			}
			return b;
	}
	/* (non-Javadoc)
	 * @see java.lang.Object#hashCode()
	 */
	public int hashCode() {
		if (hasWildcardForPrefix)
			return part.hashCode()+123456;
		else
			return part.hashCode()+654321;
	}

	public String toString() {
		if (part.equals("*"))
			return part;
		if (this.hasWildcardForPrefix) {
			return "*:" + this.part;
		} else {
			return this.part + ":*";
		}
	}
	
	/** derived from toString */
	public String getUriPart() {
		if (part.equals("*"))
			return part;
		
		if (this.hasWildcardForPrefix)
			return "*";
		else 
			return this.part;
	}
	
	/** derived from toString */
	public String getLocalPart() {
		if (part.equals("*"))
			return part;
		
		if (this.hasWildcardForPrefix)
			return this.part;
		else 
			return "*";
	}	
	
	public Wildcard copy() {
		return new Wildcard(part, hasWildcardForPrefix);
	}
}
