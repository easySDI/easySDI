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
package ch.ethz.mxquery.iterators;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Set;

public class UnionIterator extends CurrentBasedIterator {
    private int iterToUse = -1;

    private Set set;

    IteratorState4Union[] statesOfIters = null;

    /**
     * 
     * Input are two iterators on which union needs to be done
     * 
     * @param iter1
     * @param iter2
     */
    public UnionIterator(Context ctx, XDMIterator iter1, XDMIterator iter2,
	    QueryLocation location) {
	super(ctx, location);
	this.subIters = new XDMIterator[] { iter1, iter2 };
	this.set = new Set();
	statesOfIters = new IteratorState4Union[2];
	statesOfIters[0] = new IteratorState4Union();
	statesOfIters[1] = new IteratorState4Union();
    }

    private void nextDepth(int iterID) throws MXQueryException {

	IteratorState4Union iterState = statesOfIters[iterID];
	iterState.curToken = this.subIters[iterID].next();

	if (iterState.curToken.getEventType() == Type.START_DOCUMENT
		|| iterState.curToken.getEventType() == Type.START_TAG) {
	    iterState.curDepth++;
	} else if (iterState.curToken.getEventType() == Type.END_DOCUMENT
		|| iterState.curToken.getEventType() == Type.END_TAG) {
	    iterState.curDepth--;
	}
    }

    /**
     * Precondition: inputs are sorted in the document order
     */
    public Token next() throws MXQueryException {

	if (this.endOfSeq) {
	    return Token.END_SEQUENCE_TOKEN;
	}

	if (iterToUse > -1 && statesOfIters[iterToUse].curDepth > 0) {
	    // returning tokens of the chosen node
	    this.nextDepth(iterToUse);
	    return statesOfIters[iterToUse].curToken;
	}

	// -- iterToUse - id of iterator used in previous iteration;
	// -1 first iteration of the execution
	if (iterToUse == -1) {
	    // Nodes from both iterators are needed for the first time
	    // comparison
	    nextDepth(0);
	    nextDepth(1);
	} else if (iterToUse == 0 || iterToUse == 1) {
	    // Iterator, which was used in previous iteration, needs to be
	    // iterated.
	    nextDepth(iterToUse);
	}

	IteratorState4Union stateIter0 = statesOfIters[0];
	IteratorState4Union stateIter1 = statesOfIters[1];

	if (stateIter0.curToken.getEventType() != Type.END_SEQUENCE
		&& stateIter1.curToken.getEventType() != Type.END_SEQUENCE) {

	    int res = stateIter0.curToken.getId().compare(
		    stateIter1.curToken.getId());
	    // -- Choose the iterator, which will be used in this iteration
	    // (according document order of the nodes)
	    if (res < 0)
		iterToUse = 1;
	    else
		iterToUse = 0;

	} else if (stateIter0.curToken.getEventType() == Type.END_SEQUENCE
		&& stateIter1.curToken.getEventType() == Type.END_SEQUENCE) {
	    this.endOfSeq = true;
	    return Token.END_SEQUENCE_TOKEN;

	} else if (stateIter0.curToken.getEventType() == Type.END_SEQUENCE) {
	    iterToUse = 1;
	} else {
	    // iterator with id = 1 reached end of sequence
	    iterToUse = 0;
	}

	Token tok = statesOfIters[iterToUse].curToken;

	if (!Type.isNode(tok.getEventType()))
	    throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
		    "Non-node items not possible in except", loc);

	Identifier id = tok.getId();

	if (set.contains(id)) {

	    while (statesOfIters[iterToUse].curDepth > 0) {
		nextDepth(iterToUse);
	    }
	    return this.next();
	} else {
	    set.add(id);
	    this.current = this.subIters[iterToUse];
	    return tok;
	}

    }

    protected void resetImpl() throws MXQueryException {
	super.resetImpl();
	iterToUse = -1;
	this.set = new Set();
	statesOfIters = new IteratorState4Union[2];
	statesOfIters[0] = new IteratorState4Union();
	statesOfIters[1] = new IteratorState4Union();
    }

    /***************************************************************************
     * 
     * class for storing each iterator's context, while performing union
     */
    private class IteratorState4Union {
	int curDepth = 0;
	Token curToken = null;
    }

    protected XDMIterator copy(Context context, XDMIterator[] subIters,
	    Vector nestedPredCtxStack) throws MXQueryException {
	return new UnionIterator(context, subIters[0], subIters[1], loc);
    }
}
