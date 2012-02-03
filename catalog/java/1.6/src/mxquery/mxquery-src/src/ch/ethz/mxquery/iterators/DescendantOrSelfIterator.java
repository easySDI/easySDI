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
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
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
import ch.ethz.mxquery.util.IntegerList;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * 
 * @author Matthias Braun
 * 
 */
public class DescendantOrSelfIterator extends CurrentBasedIterator {
	private String step;

	private boolean match = false;

	//private int depth = 0;

	public static final int DESC_AXIS_DESCENDANT = 0;
	public static final int DESC_AXIS_DESCENDANT_OR_SELF = 1;
	public static final int DESC_AXIS_SLASHSLASH = 2;
	
	private int descAxis;
	
	private int saved_depth = 0;

	private boolean exists = false; // indicates that a materialization exists

	private boolean materialize = false; // flag, that invokes
											// materialization

	private boolean cInited = false; // remembers, if there is a backup of
										// the current item

	private Vector tokens = new Vector(); // materialized tokens

	private XDMIterator currentSave; // backup of the current item

	private IntegerList iList = new IntegerList(); // list of all materialized
													// start-tags that match the
													// step

	private int startTags = 0; // counts the start-tags

	private int endTags = 0; // counts the end-tags

	private int currentIndex = -1; // index for current position in token array

	private String step_local = null;

	private String step_uri = null;
	
	private TypeInfo stepData = null;
	
	private Vector ids = null;
	
	private int idType = 0;
	
	int position = 0;
	private boolean newParent = false;
	
	
	Token boundNode = null;
	
	protected DescendantOrSelfIterator(Context ctx, String step, int descAxis, XDMIterator[] subIters, QueryLocation location) throws MXQueryException {
		super(ctx,subIters, location);
		if (subIters == null || subIters.length == 0 || subIters.length != 1) {
			throw new IllegalArgumentException();
		}

		this.current = subIters[0];

		this.step = step;
	}

	public DescendantOrSelfIterator(Context ctx, TypeInfo step, int descAxis, XDMIterator[] subIters, QueryLocation location) throws MXQueryException {
		super(ctx,subIters, location);
		if (subIters == null || subIters.length == 0 || subIters.length != 1) {
			throw new IllegalArgumentException();
		}

		this.current = subIters[0];

		stepData = step;
		this.step = stepData.getName();
		this.descAxis = descAxis;
	}

	
	public DescendantOrSelfIterator(Context ctx, TypeInfo stepD, QueryLocation location, int descAxis) throws MXQueryException {
		super(ctx, location);
		stepData = stepD;
		step = stepData.getName();
		this.descAxis = descAxis;
	}	
	
	public DescendantOrSelfIterator(Context ctx, Vector ids, int idType, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		if (idType == CheckNodeType.CHECK_IDTYPE_ID)
			stepData = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_EXACTLY_ONE,"*",null);
		else 
			stepData = new TypeInfo(Type.TYPE_NK_ANY_NODE_TEST,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		step = stepData.getName();
		this.ids = ids;
		this.idType = idType;
		this.descAxis = DESC_AXIS_SLASHSLASH;
	}	
	
	
	
	private void init() throws MXQueryException {
		if (step == null)
			return;
		String str[] = QName.parseQName(step);
		this.step_local = str[1];
		if (str[0] != null) {
			if (str[0].equals("*")) {
				this.step_uri = "*";
			} else {
				Namespace ns = this.context.getNamespace(str[0]);
				if (ns != null)
					this.step_uri = ns.getURI();
				else 
					throw new StaticException(ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE, "Namespace prefix "+str[0]+" not bound", loc);
			}
		} else if (stepData.getNameSpaceURI() != null) {
			step_uri = stepData.getNameSpaceURI();
		}
	}

	public Token next() throws MXQueryException {
		if (this.called <= 0) {
			this.init();
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
			this.called++;
		}
		
		// if there is a materialized token array and the materialization is
		// finished, process it!
		if (this.exists && !this.materialize) {
			// save a backup of the current item (it will be overwritten!)
			if (!this.cInited) {
				this.currentSave = this.current;
				this.cInited = true;
			}

			// if there is anything to do
			if (this.iList.size() > 0 || this.currentIndex >= 0) {
				// get position in the token array
				if (this.currentIndex < 0) {
					this.currentIndex = this.iList.remove(0);
				}

				// get a token
				Token currToken = (Token)this.tokens.elementAt(this.currentIndex);
				
				//this.current.next();
				this.currentIndex++;
				
				// increase startTags / endTags variable
				if (currToken.getEventType() == Type.START_TAG
						&& (stepData.getType() == 
							Type.TYPE_NK_ANY_NODE_TEST ||
							CheckNodeType.step_comparison(currToken,
								this.step_uri, this.step_local))) {
					this.startTags++;
				} else if (currToken.getEventType() == Type.END_TAG
						&& (stepData.getType() == 
									Type.TYPE_NK_ANY_NODE_TEST || 
									CheckNodeType.step_comparison(currToken,
								this.step_uri, this.step_local))) {
					this.endTags++;
				}

				// if there are more start-tags then end-tags, return the
				// current token
				// else the currently returned node is at its end.
				if (this.startTags > this.endTags) {
					return currToken;
				} else if (this.startTags == this.endTags) {
					// at the end of current node
					this.currentIndex = -1;
					this.startTags = 0;
					this.endTags = 0;

					return currToken;
				}
			} else {
				// reset the variables
				this.exists = false;
				this.cInited = false;
				this.current = this.currentSave;
				this.tokens.removeAllElements();
			}
		}
		Token tok = Token.START_SEQUENCE_TOKEN;
		while (tok.getEventType() != Type.END_SEQUENCE) {
			tok = getNext();
			
			if (!(Type.isNode(tok.getEventType())||tok == Token.END_SEQUENCE_TOKEN)) 
				throw new TypeException(ErrorCodes.E0019_TYPE_STEP_RESULT_IS_ATOMIC, "// or descendant axis applied on non-node",loc );
			if (tok.getEventType() == Type.START_TAG && depth == 1)
				newParent = true;
			if (tok.getEventType() == Type.START_DOCUMENT && descAxis != DESC_AXIS_DESCENDANT_OR_SELF) {
				boundNode = tok;
				tok = getNext();
			}
			
			boolean currentMatch = false;

			if (descAxis == DESC_AXIS_DESCENDANT_OR_SELF) {
				switch( stepData.getType() ) {
				case Type.PROCESSING_INSTRUCTION:
					if (depth >= 0  && CheckNodeType.checkPI(tok, step)) 
						currentMatch = true;
					break;
				case Type.COMMENT:
					if (depth >= 0 &&  CheckNodeType.checkComment(tok))
						currentMatch = true;
					break;
				case Type.TYPE_NK_TEXT_TEST:
					if (depth >= 0 && CheckNodeType.checkText(tok))
						currentMatch = true;
					break;
				case Type.TYPE_NK_ANY_NODE_TEST:
					//FIXME! depth!
//					if (boundNode == null) {
//						boundNode = tok;
//						tok = getNext();
//					}
//					if (depth == 0)
//						boundNode = null;
					if (depth >= 0 && CheckNodeType.checkNode(tok) && 
							((ids == null && (!Type.isAttribute(tok.getEventType())|| depth ==0)|| 
									(tok instanceof NamedToken && CheckNodeType.checkIdElement((NamedToken)tok, ids, idType))))) 
						currentMatch = true;
					// skip over the bound node's END token
					if (tok.getEventType() == Type.END_TAG || tok.getEventType() == Type.END_DOCUMENT)
						currentMatch = false;
					break;

				case Type.START_TAG:
					// if the current node matches the step criterion and at least one level down
					if ((tok.getEventType() == Type.START_TAG)
							&& CheckNodeType.step_comparison(tok,
									this.step_uri, this.step_local) && depth > 0 && 
									(ids == null || CheckNodeType.checkIdElement((NamedToken)tok, ids, idType)))
						currentMatch = true;
					break;	

				default:
					throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Node kind test type " + stepData.getType() +" is not supported", loc);

				}// end switch				
			} else {
				switch( stepData.getType() ) {
				case Type.PROCESSING_INSTRUCTION:
					if (depth >= 1  && CheckNodeType.checkPI(tok, step)) 
						currentMatch = true;
					break;
				case Type.COMMENT:
					if (depth >= 1 &&  CheckNodeType.checkComment(tok))
						currentMatch = true;
					break;
				case Type.TYPE_NK_TEXT_TEST:
					if (depth >= 1 && CheckNodeType.checkText(tok))
						currentMatch = true;
					break;
				case Type.TYPE_NK_ANY_NODE_TEST:
					if (boundNode == null) {
						boundNode = tok;
						tok = getNext();
					}
					if (depth == 0)
						boundNode = null;
					if (depth >= 1 && CheckNodeType.checkNode(tok) && 
							((ids == null && !Type.isAttribute(tok.getEventType())|| 
									(tok instanceof NamedToken && CheckNodeType.checkIdElement((NamedToken)tok, ids, idType))))) 
						currentMatch = true;
					// skip over the bound node's END token
					if (tok.getEventType() == Type.END_TAG)
						currentMatch = false;
					break;

				case Type.START_TAG:
					// if the current node matches the step criterion and at least one level down
					if ((tok.getEventType() == Type.START_TAG)
							&& CheckNodeType.step_comparison(tok,
									this.step_uri, this.step_local) && depth > 1 && 
									(ids == null || CheckNodeType.checkIdElement((NamedToken)tok, ids, idType)))
						currentMatch = true;
					break;	

				default:
					throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Node kind test type " + stepData.getType() +" is not supported", loc);

				}// end switch
			}
			if (currentMatch){
				if (this.match) {
					// if match is already set, this is a nested node
					this.iList.add(this.tokens.size()); // remember the position
														// in the token array of
														// this start-tag
					this.tokens.addElement(tok);

					this.exists = true; // indicate that there is a
										// materialization
					this.materialize = true; // start materialization

					return tok;
				} else {
					if (tok.getEventType() == Type.START_TAG || tok.getEventType() == Type.START_DOCUMENT)
						this.match = true;
					this.saved_depth = this.depth;
					if (this.materialize) {
						this.tokens.addElement(tok);
					}
					if (!newParent)
						position++;
					else {
						position = 1;
						newParent = false;
					}
					context.setPosition(position);
					return tok;
				}
			}

			// if the current token is not a start-tag with step as name
			if (this.match) {
				// if the current token is at depth 1 and an end-tag matching
				// the step criterion
				if (this.depth == this.saved_depth - 1
						&& ( (tok.getEventType() == Type.END_TAG && CheckNodeType.step_comparison(tok, this.step_uri, this.step_local) ) || tok.getEventType() == Type.END_DOCUMENT) ) {
					this.match = false;
					this.materialize = false; // stop materialization
				}
				if (this.materialize) {
					this.tokens.addElement(tok);
				}
				return tok;
			}
		}

		return Token.END_SEQUENCE_TOKEN;
	}

	public int getDepth() {
		return -1;
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		this.current = this.subIters[0];
		this.newParent = false;
		this.match = false;
		this.exists = false;
		this.materialize = false;
		this.cInited = false;
		this.tokens = new Vector();
		this.currentSave = null;
		this.iList = new IntegerList();
		this.startTags = 0;
		this.endTags = 0;
		this.currentIndex = -1;
		position = 0;
		boundNode = null;
	}

	
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
	throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "step", stepData.toString());
		return serializer;
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		if (stepData != null) {
			DescendantOrSelfIterator iter = new DescendantOrSelfIterator(context, stepData.copy(), loc, this.descAxis);
			iter.ids = this.ids;
			iter.idType = this.idType;
			iter.setSubIters(subIters);
			return iter; 
		} else {
			return new DescendantOrSelfIterator(context, step, this.descAxis, subIters, loc);
		}
	}
}
