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

package ch.ethz.mxquery.datamodel;
/**
 * 
 * @author Matthias Braun
 * 
 * Representation of a Namespace with a prefix and the corresponding URI
 *
 */
public class Namespace {
	private String prefix;
	private String uri;
	
	public boolean equals(Object arg0) {
		if (arg0 instanceof Namespace) {
			Namespace new_name = (Namespace) arg0;
			if (new_name.prefix == null && prefix != null)
				return false;
			if (new_name.uri == null && uri != null)
				return false;
			if (((new_name.uri == null && uri == null) || new_name.prefix.equals(prefix)) 
					&& ((new_name.uri == null && uri == null) || new_name.uri.equals(uri)))
				return true;
		}
		return false;
	}

	public int hashCode() {
		int hc = 0;
		if (uri != null)
			hc += uri.hashCode();
		if (prefix != null)
			hc += prefix.hashCode();
		return hc;
	}

	/**
	 * Creates a namespace
	 * 
	 * @param prefix	The namespace prefix
	 * @param uri		The namespace URI
	 */
	public Namespace(String prefix, String uri) {
		if (prefix == null)
			this.prefix = "";
		else
			this.prefix = prefix;
		this.uri = uri;
	}
	
	/**
	 * 
	 * @return	The namespace prefix
	 */
	public String getNamespacePrefix() {
		return prefix;
	}
	
	/**
	 * 
	 * @return	The namespace URI
	 */
	public String getURI() {
		return uri;
	}
	
	/**
	 * 
	 * @param uri
	 */
	public void setURI(String uri) {
		this.uri = uri;
	}

	public String toString() {
		return uri;
	}
	
	public Namespace copy() {
		return new Namespace(prefix, uri);
	}
}
