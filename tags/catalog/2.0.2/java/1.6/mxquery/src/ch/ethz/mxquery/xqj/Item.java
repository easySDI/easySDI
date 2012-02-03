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

package ch.ethz.mxquery.xqj;

import javax.xml.xquery.XQException;

import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.model.XDMIterator;

public interface Item {
	
	 void close();
     
	 String getAtomicValue() throws XQException;
	           
	 boolean getBoolean() throws XQException;
	           
	 byte getByte() throws XQException;
	           
	 double getDouble() throws XQException;
	           
	 float getFloat() throws XQException;
	           
	 int getInt() throws XQException;
	           
	 String getItemAsString() throws XQException;
	 
	 XDMIterator getAsIterator() throws XQException;
	           
	 long getLong() throws XQException;
	           
	 short getShort() throws XQException;
	           
	 boolean isClosed();       
	 
	 TypeInfo getType();

}
