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
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CheckNodeType;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * 
 * @author Matthias Braun
 * 
 */
public class ChildIterator extends CurrentBasedIterator {
	private int constr = 0;
	
	private String step;

	protected String lastStep;

	private boolean match = false;

	private String step_local = null;

	private String step_uri = null;

    int step_type = Type.ITEM;
	
	private TypeInfo stepData = null;
	
	int position = 0;
	
	
	
	Token boundNode = null;
	
	private boolean newParent = false;
	
	public ChildIterator(Context ctx, String step, XDMIterator[] subIters, QueryLocation location) throws MXQueryException {
		super(ctx,subIters, location);
		constr = 1;
		if (subIters == null || subIters.length == 0 || subIters.length != 1) {
			throw new IllegalArgumentException();
		}
		
		//current = subIters[0];
		this.step = step;
		stepData = new TypeInfo();
//		stepData.setType(Type.TYPE_NK_ELEM_TEST);
		stepData.setType(Type.START_TAG);
	}

	public ChildIterator(Context ctx, TypeInfo stepD, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		constr = 2;
		stepData = stepD;
		step = stepData.getName();
		step_uri = stepData.getNameSpaceURI();
	}	

	
	public String getStepName() {
		return step;
	}
	
	private void init() throws MXQueryException {
		if (step == null)
			return;
		if (step.equals("*")) {
			step_local = "*";
		} else {
			String str[] = QName.parseQName(step);
			step_local = str[1];
			if (str[0] != null) {
				if (str[0].equals("*")) {
					this.step_uri = "*";
				} else {
					Namespace ns = this.context.getNamespace(str[0]);
					if (ns != null)
						step_uri = ns.getURI();
					else
						throw new StaticException(ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE, "Namespace not known", loc);
				}
			}
		} 
		if (stepData.getNameSpaceURI() != null) {
			step_uri = stepData.getNameSpaceURI();
		}
	}

	public Token next() throws MXQueryException {
		if (this.called == 0) {
			this.init();
            step_type = stepData.getType();
			if (subIters != null)
				this.current = subIters[0];
			else {
				VariableHolder contextVarHolder = context.getContextItem();
				if (contextVarHolder != null && contextVarHolder.getIter() != null) {
					if (contextVarHolder.getIter().isExprParameter(EXPR_PARAM_WINDOW, false))
						current = ((Window)contextVarHolder.getIter()).getNewWindow(1, Window.END_OF_STREAM_POSITION);
					if (current == null)
						throw new DynamicException(ErrorCodes.E0002_DYNAMIC_NO_VALUE_ASSIGNED, "Context Item Iterator not set", loc);
				} else {
					throw new DynamicException(ErrorCodes.E0002_DYNAMIC_NO_VALUE_ASSIGNED, "Context Item not set", loc);	
				}
			}
		}
		this.called++;

		Token tok = Token.START_SEQUENCE_TOKEN;
        int type = tok.getEventType();
		while (type != Type.END_SEQUENCE) {
			// next on child iterator
			tok = getNext();
			
			type = tok.getEventType();
			if (type == Type.END_SEQUENCE)
				return tok;
		
			if (!Type.isNode(type))
				throw new TypeException(ErrorCodes.E0019_TYPE_STEP_RESULT_IS_ATOMIC, "Child axis applied on non-node",loc );
			
			if (depth == 1 && tok.getEventType() == Type.START_TAG || tok.getEventType() == Type.START_DOCUMENT)
				newParent = true;
			int targetDepth = 1;
			if (match) { // if current node is already matching the child
				// condition

				if (type == Type.END_TAG && depth == targetDepth
						&& (tok.getName().equals(lastStep))) { // child
					// if the current depth is equal to initial depth, it's the
					// end of the matching sequence
					match = false;
				}
				return tok;
			} else {

					switch(step_type) {
						case Type.PROCESSING_INSTRUCTION:
							if (depth != targetDepth || !CheckNodeType.checkPI(tok, step)) 
								continue;
							break;
						case Type.COMMENT:
							if (depth != targetDepth ||  !CheckNodeType.checkComment(tok))
								continue;
							break;
						case Type.TYPE_NK_TEXT_TEST:
							if (depth != targetDepth || !CheckNodeType.checkText(tok))
								continue;
							break;
						case Type.TYPE_NK_ANY_NODE_TEST:
							if (boundNode == null && (type == Type.START_DOCUMENT || type == Type.START_TAG)) {
								boundNode = tok;
								tok = getNext();
                                type = tok.getEventType();
							}
							if (depth == 0)
								boundNode = null;
							if ((depth != targetDepth && depth != targetDepth+1) || Type.isAttribute(tok.getEventType()) || !CheckNodeType.checkNode(tok))
								continue;
							else if (type == Type.START_TAG)
								match = true; // set match flag to true if there is nested content
								// skip over the bound node's END token
								if (depth == 1 && type == Type.END_TAG && boundNode != null && boundNode.getName() != null && boundNode.getName().equals(tok.getName()))
									continue;
							break;
							
						case Type.START_TAG:
							targetDepth = 2;
							while (depth == 1) {
								tok = getNext();
							}
							type = tok.getEventType();

							if (depth != targetDepth || type != Type.START_TAG
									|| (!CheckNodeType.step_comparison(tok,step_uri,step_local)))
								continue;
							else {
								match = true; // set match flag to true
							}
						break;	
							
						default:
							throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Node kind test type " + step_type +" is not supported", loc);
					
					}// end switch
					
					if (!newParent)
						position++;
					else {
						newParent = false;
						position = 1;
					}
					context.setPosition(position);
					lastStep = tok.getName();
					return tok;
				}
			}
		return Token.END_SEQUENCE_TOKEN;
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		if (subIters != null)
			current = subIters[0];
		match = false;
		lastStep = null;
		position = 0;
		boundNode = null;
		newParent = false;
	}

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
			throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "step", stepData.toString());
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		if (constr == 1) {
			return new ChildIterator(context, step, subIters,loc);	
		} else {
			ChildIterator copy = new ChildIterator(context, stepData.copy(),loc);
			copy.setSubIters(subIters);
			
			return copy;
		}
	}
}
