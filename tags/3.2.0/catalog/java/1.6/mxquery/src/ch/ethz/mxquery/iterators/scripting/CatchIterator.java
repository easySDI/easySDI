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
package ch.ethz.mxquery.iterators.scripting;

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.FnErrorException;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Wildcard;
import ch.ethz.mxquery.model.XDMIterator;
//import ch.ethz.mxquery.model.Wildcard;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * Represents a catch expression of a try.
 * 
 * @author David Graf
 * 
 */
public class CatchIterator extends CurrentBasedIterator {
	private Object nametest;
	private QName errorCode;
	private QName errorDescr;
	private QName errorVal;

	public CatchIterator(Context ctx, XDMIterator catchExpr, Object nametest, QName errorCode, QName errorDescr, QName errorVal, QueryLocation location) {
		super(ctx, location);
		this.subIters = new XDMIterator[] { catchExpr };
		this.nametest = nametest;
		this.errorCode = errorCode;
		this.errorDescr = errorDescr;
		this.errorVal = errorVal;
	}

	void init(MXQueryException e) throws MXQueryException {
		if (this.errorCode != null) {
			XDMIterator errCodeIter;
			if (e.getErrorCode() != null) {
				errCodeIter = new TokenIterator(context, e.getErrorCode(),loc);
			} else {
				errCodeIter = new EmptySequenceIterator(context, loc);
			}
			VariableHolder vh = this.context.getVariable(this.errorCode);
			vh.setIter(WindowFactory.getNewWindow(this.context, errCodeIter));
		}
		if (this.errorDescr != null) {
			XDMIterator errDescrIter;
			if (e.getMessage() != null) {
				errDescrIter = new TokenIterator(context, e.getMessage(),loc);
			} else {
				errDescrIter = new EmptySequenceIterator(context, loc);
			}
			VariableHolder vh = this.context.getVariable(this.errorDescr);
			vh.setIter(WindowFactory.getNewWindow(this.context, errDescrIter));
		}
		if (this.errorVal != null) {
			XDMIterator errValIter;
			if (e instanceof FnErrorException && ((FnErrorException)e).getErrorObject() != null) {
				errValIter = ((FnErrorException)e).getErrorObject();
			} else {
				errValIter = new EmptySequenceIterator(context, loc);
			}
			VariableHolder vh = this.context.getVariable(this.errorVal);
			vh.setIter(WindowFactory.getNewWindow(this.context, errValIter));
		}
		this.current = this.subIters[0];
	}

	public void freeResources(boolean restartable) throws MXQueryException {
//		FIXME
//		throw new RuntimeException("David fix me");
//		((Context_new) this.context).freeResources();
//		super.freeResources();
	}

	public void resetImpl() throws MXQueryException {
		this.freeResources(true);
		super.resetImpl();
	}

	public Token next() throws MXQueryException {
		Token inputToken = this.current.next();
		if (inputToken.getEventType() == Type.END_SEQUENCE) {
			this.freeResources(false);
		}
		return inputToken;
	}
	
	public boolean compareErrCodes(QName otherCode) throws MXQueryException{
		if (this.nametest == null) {
			return true;
		}
		if (otherCode == null) {
			return false;
		}
		
		if (nametest instanceof Wildcard) {
			return ((Wildcard)nametest).coversQName(otherCode);
		}
		
		if (nametest instanceof QName) {
			QName qn = (QName) nametest;
			return qn.equals(otherCode);
		}
		return false;
	}

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
			throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "nametest", this.nametest.toString());
		if (this.errorCode != null) {
			serializer.attribute(null, "error_code", this.errorCode.toString());
		}
		if (this.errorDescr != null) {
			serializer.attribute(null, "error_descr", this.errorDescr.toString());
		}
		if (this.errorVal != null) {
			serializer.attribute(null, "error_val", this.errorVal.toString());
		}
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new CatchIterator(context, 
				subIters[0], 
				nametest,
				errorCode.copy(), 
				errorDescr.copy(),
				errorVal.copy(), loc);
	}
}
