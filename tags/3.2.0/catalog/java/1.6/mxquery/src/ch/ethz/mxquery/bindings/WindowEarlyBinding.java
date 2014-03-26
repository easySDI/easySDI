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
package ch.ethz.mxquery.bindings;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.WindowVariable;
import ch.ethz.mxquery.model.XDMIterator; // import
// ch.ethz.mxquery.parallel.ThreadPool;
import ch.ethz.mxquery.util.IntegerList;

public final class WindowEarlyBinding extends WindowTokenIterator {

    private boolean checkNode = false;
    private int knowMaxNode;
    private int endNode;
    private int nextWindowStartPosition;

    private XDMIterator endExpr;
    private WindowVariable[] endVars;
    private VariableHolder[] endVarHolders;

    private XDMIterator startExpr;
    private WindowVariable[] startVars;
    private VariableHolder[] startVarHolders;

    private boolean setStartVar;

    public WindowEarlyBinding(WindowBuffer mat, int id, int startPosition,
	    XDMIterator startExpr, WindowVariable[] startVars,
	    VariableHolder[] startVarHolders, XDMIterator endExpr,
	    WindowVariable[] endVars, VariableHolder[] endVarHolders) {
	super(mat, startPosition - 1, id);
	this.endExpr = endExpr;
	this.endVars = endVars;
	this.endVarHolders = endVarHolders;
	this.startExpr = startExpr;
	this.startVars = startVars;
	this.startVarHolders = startVarHolders;
	checkNode = true;
	knowMaxNode = startPosition - 1;
	endNode = WindowIterator.END_OF_STREAM_NODE;
	nextWindowStartPosition = -1;
	setStartVar = true;
    }

    public int getNextWindowStartPosition() {
	return nextWindowStartPosition;
    }

    protected void resetImpl() throws MXQueryException {
	super.resetImpl();
	checkNode = true;
	knowMaxNode = startNodeId;
	endNode = WindowIterator.END_OF_STREAM_NODE;
	nextWindowStartPosition = -1;
	setStartVar = true;
    }

    public Token next() throws MXQueryException {
	called++;
	// if (ThreadPool.isDebug()) ThreadPool.log(this,
	// "WindowEarlyBinding.next()");
	catchNextToken();
	if (endOfStream) {
	    activeToken = Token.END_SEQUENCE_TOKEN;
	    return activeToken;
	}
	if (checkNode) {
	    knowMaxNode = currentNodeId;
	    checkNode = false;
	    if (currentNodeId != startNodeId && nextWindowStartPosition == -1
		    && checkStartExpr(knowMaxNode)) {
		nextWindowStartPosition = currentNodeId + 1;
	    }
	    if (checkEndExpr(startNodeId, knowMaxNode)) {
		endNode = currentNodeId;
	    }
	}
	if (currentNodeId != nextNodeId) {
	    checkNode = true;
	    if (endNode != END_OF_STREAM_NODE) {
		endOfStream = true;
	    }
	}
	return activeToken;
    }

    /**
     * Checks the start expression
     * 
     * @param startPosition
     * @return
     * @throws MXQueryException
     */
    private boolean checkStartExpr(int startNode) throws MXQueryException {
	setStartVar = true;
	boolean value = false;
	if (startExpr != null) {
	    assignVars(startVars, startVarHolders, startNode);
	    Token tok;
	    if ((tok = startExpr.next()).getEventType() == Type.BOOLEAN) {
		value = tok.getBoolean();
	    }
	    startExpr.reset();
	}
	return value;
    }

    /**
     * Checks the end expressen for a given start and end position
     * 
     * @param startPosition
     * @param endPosition
     * @return
     * @throws MXQueryException
     */
    private boolean checkEndExpr(int startNode, int endNode)
	    throws MXQueryException {
	boolean value = false;
	if (setStartVar && startExpr != null) {
	    setStartVar = false;
	    assignVars(startVars, startVarHolders, startNode);
	}
	assignVars(endVars, endVarHolders, endNode);
	Token tok;
	if ((tok = endExpr.next()).getEventType() == Type.BOOLEAN) {
	    value = tok.getBoolean();
	}
	endExpr.reset();
	return value;
    }

    /**
     * Assigns window vars to the context
     * 
     * @param vars
     * @param position
     * @throws MXQueryException
     */
    protected void assignVars(WindowVariable[] vars,
	    VariableHolder[] varHolders, int nodeId) throws MXQueryException {
	if (vars != null) {
	    for (int i = 0; i < vars.length; i++) {
		if (vars[i].getType() != WindowVariable.WINDOW_VAR_POSITION) {
		    Window window = (Window) varHolders[i].getIter();
		    if (window != null)
			window.destroyWindow();
		}
		switch (vars[i].getType()) {
		case WindowVariable.WINDOW_VAR_CUR_ITEM:
		    varHolders[i].setIter(getVarItem(nodeId));
		    break;
		case WindowVariable.WINDOW_VAR_NEXT_ITEM:
		    varHolders[i].setIter(getVarItem(nodeId + 1));
		    break;
		case WindowVariable.WINDOW_VAR_PREV_ITEM:
		    varHolders[i].setIter(getVarItem(nodeId - 1));
		    break;
		case WindowVariable.WINDOW_VAR_POSITION:
		    varHolders[i].setIter(new TokenIterator(context,
			    new LongToken(Type.INT, null, nodeId - startNodeId
				    + 1), null, loc));
		    // varHolders[i].setIter(new TokenIterator(context, new
		    // LongToken(Type.INTEGER, null, nodeId - startNodeId + 1),
		    // null));
		    break;
		}
	    }
	}
    }

    private boolean checkItem(int node) throws MXQueryException {
	if (endNode != WindowIterator.END_OF_STREAM_NODE && node <= endNode) {
	    return true;
	} else {
	    while (node > knowMaxNode && node <= endNode) {
		tokenIdOutdated = true;
		knowMaxNode++;
		// System.out.println("Problem: " + node);
		endOfStream = !mat.hasNode(node);
		if (endOfStream) {
		    return false;
		}
		if (knowMaxNode != startNodeId && nextNodeId != -1
			&& checkStartExpr(knowMaxNode)) {
		    this.nextNodeId = knowMaxNode;
		}
		if (checkEndExpr(startNodeId, knowMaxNode)) {
		    endNode = knowMaxNode;
		}
	    }
	    if (node == knowMaxNode) {
		return true;
	    } else {
		return false;
	    }
	}
    }

    public boolean hasItem(int position) throws MXQueryException {
	int nodeId = startNodeId + position - 1;
	return checkItem(nodeId);
    }

    public XDMIterator getItem(int position) throws MXQueryException {
	int nodeId = startNodeId + position - 1;
	if (checkItem(nodeId)) {
	    return mat.getNewWindowIteratorWithNodeIds(nodeId, nodeId);
	} else {
	    return mat.getEmptySequence();
	}
    }

    public XDMIterator getVarItem(int nodeId) throws MXQueryException {
	if (mat.hasNode(nodeId)) {
	    return mat.getNewWindowIteratorWithNodeIds(nodeId, nodeId);
	} else {
	    return mat.getEmptySequence();
	}
    }

    public boolean hasNextItem() throws MXQueryException {
	return checkItem(currentNodeId + 1);
    }

    protected int getNodeId() {
	if (0 < currentNodeId) {
	    return currentNodeId;
	} else {
	    return 0;
	}
    }

    public XDMIterator nextItem() throws MXQueryException {
	currentNodeId++;
	if (checkItem(currentNodeId)) {
	    return mat.getNewWindowIterator(currentNodeId + 1,
		    currentNodeId + 1);
	} else {
	    return mat.getEmptySequence();
	}
    }

    public Window getNewItemWindow(IntegerList values) {
	IntegerList correctedList = new IntegerList();
	for (int i = 0; i < values.size(); i++) {
	    int value = values.get(i);
	    if ((this.startNodeId <= value) && value <= (this.endNode)) {
		correctedList.add(value);
	    }
	}
	if (correctedList.size() > 0) {
	    return mat.getNewWindowIterator(correctedList);
	} else {
	    return mat.getEmptySequence();
	}
    }

    public Window getNewWindow(int startPosition, int endPosition) {
	return getNewWindowIteratorWithNodes(startPosition - 1, endPosition - 1);
    }

    private WindowIterator getNewWindowIteratorWithNodes(int startNode,
	    int endNode) {
	if (!isWindowInUse() && startNode == 0 && endNode == END_OF_STREAM_NODE) {
	    setWindowInUse(true);
	    return this;
	} else {
	    return mat.getNewWindowIteratorWithNodeIds(startNodeId + startNode,
		    min(this.endNode, startNodeId + endNode));
	}
    }

    public int getEndPosition() {
	return endNode + 1;
    }

    protected XDMIterator copy(Context context, XDMIterator[] subIters,
	    Vector nestedPredCtxStack) throws MXQueryException {
	throw new RuntimeException(
		"copy of WindowEarlyBinding not yet implemented!");
    }

}
