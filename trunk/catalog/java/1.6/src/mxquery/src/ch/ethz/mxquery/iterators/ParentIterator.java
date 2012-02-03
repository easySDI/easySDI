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

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.Source;
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
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.UpdateableStore;
import ch.ethz.mxquery.util.Set;

public class ParentIterator extends CurrentBasedIterator {

	private TypeInfo stepData = null;
	private String step;
	private String step_local = null;
	private String step_uri = null;
	
	private Window itemIt;
	
	private Set seenParents = new Set();
	
	public ParentIterator(Context context, TypeInfo stepData,
			QueryLocation loc) {
		super(context,loc);
		this.stepData = stepData;
		step = stepData.getName();
		step_uri = stepData.getNameSpaceURI();
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		ParentIterator par = new ParentIterator(context,stepData,loc);
		par.setSubIters(subIters);
		return par;
	}

	private void init () throws MXQueryException{
		
		if (step != null) {
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
		
		XDMIterator it = getNodeIteratorOrContext(subIters, 1,context, loc);
		
		itemIt = WindowFactory.getNewWindow(context, it);
	}

	private void checkTokengetParentIterator(Token tok) throws TypeException,
			MXQueryException, DynamicException {
		if (!(Type.isNode(tok.getEventType()) || tok == Token.END_SEQUENCE_TOKEN)) {
			throw new TypeException(ErrorCodes.E0019_TYPE_STEP_RESULT_IS_ATOMIC, "Parent axis applied on non-node item", loc);
		}
				
		Source src = tok.getId().getStore();
		
		if (src instanceof UpdateableStore) {
			current = ((UpdateableStore)src).getParentIterator(tok.getId());
		} 
		else throw new DynamicException (ErrorCodes.A0002_EC_NOT_SUPPORTED,"parent not working here - please report",loc);
	}
	
	public Token next() throws MXQueryException {

		if (endOfSeq)
			return Token.END_SEQUENCE_TOKEN;
		
		if (called == 0) {
			init();
		}
		called++;
						
		if (current == null) {
			while (itemIt.hasNextItem()) {
			XDMIterator sourceItem = itemIt.nextItem();
			Token srcTok = sourceItem.next();
			checkTokengetParentIterator(srcTok);
			Token tok = current.next(); 
			if (seenParents.contains(tok.getId()))
				continue;
			else
				seenParents.add(tok.getId());
			if (!checkToken(tok))
				continue;
			return tok;
			}
			endOfSeq = true;
			return Token.END_SEQUENCE_TOKEN;

		}

		Token tok = current.next(); 
		if (tok == Token.END_SEQUENCE_TOKEN) { 
			while (itemIt.hasNextItem()) {
				XDMIterator sourceItem = itemIt.nextItem();
				Token srcTok = sourceItem.next();
				checkTokengetParentIterator(srcTok);
				Token tok1 = current.next(); 
				if (seenParents.contains(tok1.getId()))
					continue;
				else
					seenParents.add(tok1.getId());
				if (!checkToken(tok1))
					continue;
				return tok1;
				}
			endOfSeq = true;
			return Token.END_SEQUENCE_TOKEN;
		}
		
		return tok;
	}
	
	private boolean checkToken(Token tok) throws MXQueryException {
//		if (tok.getEventType() == Type.START_DOCUMENT) {
//			return false;
//		}
		switch( stepData.getType() ) {
		case Type.PROCESSING_INSTRUCTION:
			if (!CheckNodeType.checkPI(tok, step)) 
				return false;
			break;
		case Type.COMMENT:
			if (!CheckNodeType.checkComment(tok))
				return false;
			break;
		case Type.TYPE_NK_TEXT_TEST:
			if (!CheckNodeType.checkText(tok))
				return false;
			break;
		case Type.TYPE_NK_ANY_NODE_TEST:
			if (!CheckNodeType.checkNode(tok))
				return false;
			break;

		case Type.START_TAG:
			if (tok.getEventType()!= Type.START_TAG
					|| (!this.step_local.equals("*")
							&& !CheckNodeType.step_comparison(tok,step_uri,step_local)))
				return false;
			break;				
		default:
			throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Node kind test type " + stepData.getType() +" is not supported", loc);

		}// end switch
		return true;
	}

}
