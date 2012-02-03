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

package ch.ethz.mxquery.functions.mxq;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.xdmio.WrapperIterator;

public class DirectXMLWrapperIterator extends WrapperIterator {
	String file;
	BufferedReader in = null;
	
	public DirectXMLWrapperIterator() throws MXQueryException{
		super(null,null);
		if (subIters != null) {
			Token inputToken = subIters[0].next();
			if (Type.getEventTypeSubstituted( inputToken.getEventType(), Context.getDictionary()) == Type.STRING)
				file = inputToken.getText();
			else throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Need String as input for File operations", loc);
		}
		else 
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Need String as input for File operations", loc);
		
		enclosingName = new QName("CarLoc");
		valueNames = new QName[9];
		types = new int [9];
		
		valueNames[0] = new QName("type");
		valueNames[1] = new QName("time");
		valueNames[2] = new QName("VID");
		valueNames[3] = new QName("Speed");
		valueNames[4] = new QName("XWay");
		valueNames[5] = new QName("Lane");
		valueNames[6] = new QName("Dir");
		valueNames[7] = new QName("Seg");
		valueNames[8] = new QName("Pos");
		
		for (int i=0;i<types.length;i++)
			types[i] = Type.STRING;
	}
	protected boolean getData() {
		String s;
		try {
			if (in == null)
				in = new BufferedReader(new FileReader(file));
			if ((s = in.readLine())!= null) {
				inputValues = ( s.split(","));
				if (inputValues.length < valueNames.length)
					return false;
				currentElement = 0;
				return true;
			}
		} catch (IOException io) {
			System.out.println("Could not get wrapped data: "+io);
		}
		// TODO Auto-generated method stub
		return false;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new DirectXMLWrapperIterator();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}

}
