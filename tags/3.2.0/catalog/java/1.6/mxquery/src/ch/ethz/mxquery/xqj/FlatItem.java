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

import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class FlatItem implements Item {

	Token tk;
	private boolean closed = false;
	
	public FlatItem(Token tk){
		this.tk = tk;
	}
	
	public void close() {
		closed = true;
		this.tk = null;
	}

	public String getAtomicValue() throws XQException {
		if (Type.isSubTypeOf(tk.getEventType(), Type.ANY_ATOMIC_TYPE, null)) {
			return tk.getValueAsString();
		}
		throw new XQException("Failed to getAtomicValue: item is a node, or is closed");
	}

	public boolean getBoolean() throws XQException {
		if (Type.isTypeOrSubTypeOf(tk.getEventType(), Type.BOOLEAN, null)) {
			return tk.getBoolean();
		}
		throw new XQException("Failed to getBoolean: item is a node, or is closed");
	}

	public byte getByte() throws XQException {
		int targetType = Type.BYTE;
		return (byte)checkIntType(targetType);
	}

	public double getDouble() throws XQException {
		if (Type.isTypeOrSubTypeOf(tk.getEventType(), Type.DOUBLE, null)) {
			MXQueryDouble doub = null;
			doub = tk.getDouble();			
			return doub.getValue(); 
		}
		throw new XQException("Failed to getDouble: item is a node, or is closed");
	}

	public float getFloat() throws XQException {
		if (Type.isTypeOrSubTypeOf(tk.getEventType(), Type.FLOAT, null)) {
			MXQueryDouble doub = null;
			doub = tk.getDouble();			
			return (float)doub.getValue(); 
		}
		throw new XQException("Failed to getFloat: item is a node, or is closed");
	}

	public int getInt() throws XQException {
		int targetType = Type.INT;
		return (int)checkIntType(targetType);
	}

	public String getItemAsString() throws XQException {
		if (Type.isTypeOrSubTypeOf(tk.getEventType(), Type.ANY_ATOMIC_TYPE, null)) {
			return getAtomicValue();
		}
		throw new XQException("Failed to getItemAsString: ");
	}
	

	public long getLong() throws XQException {
		int targetType = Type.INTEGER;
		return checkIntType(targetType);
	}

	public short getShort() throws XQException {
		int targetType = Type.SHORT;
		return (short)checkIntType(targetType);
	}

	private long checkIntType(int targetType) throws XQException {
		if (Type.isTypeOrSubTypeOf(tk.getEventType(), Type.DECIMAL, null)){
			long res = tk.getLong();
			if (!Type.isTypeOrSubTypeOf(tk.getEventType(), Type.INTEGER, null)){
				MXQueryNumber num = tk.getNumber();
				try {
					res = num.getLongValue();
				} catch (MXQueryException e) {
					throw new XQException("Invalid value for getXXX() operation");
				}
				if (num.compareTo(res)!= 0) {
					throw new XQException("Invalid value for getXXX() operation");
				}
			}
			try {
				if (!TypeLexicalConstraints.satisfyIntegerRange(targetType, res))
					throw new XQException("Invalid value for getXXX() operation");	
			} catch (MXQueryException e) {
				throw new XQException(e.toString());
			}
				
			return res;
		}
		throw new XQException("Failed to getByte: item is a node, or is closed");
	}

	
	public boolean isClosed() {
		return this.closed;
	}

	public TypeInfo getType() {
		// TODO Auto-generated method stub
		return new TypeInfo(tk.getEventType(),Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}

	public XDMIterator getAsIterator() throws XQException{
		try {
			return new TokenIterator(null,tk,null,null);
		} catch (MXQueryException me) {
			throw new XQException(me.toString());
		}
	}

}
