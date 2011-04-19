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
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CheckNodeType;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * Event of an Attribute Declaration in XQuery ('@attributename')
 * 
 * @author Matthias Braun
 * 
 */
public class AttributeIterator extends CurrentBasedIterator {

	private String local = null;

	private String namespace = null;

	//private int depth = 0;

	private boolean descendantOrSelf;

	private boolean init = false;
	
	private String step;

	private TypeInfo stepData;
	
	public AttributeIterator(Context ctx, TypeInfo stepD, boolean descendantOrSelf, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		this.descendantOrSelf = descendantOrSelf;
		stepData = stepD;
		step = stepData.getName();
	}	
	
	public boolean isDescendantOrSelf(){
		return descendantOrSelf;
	}
	private void init() throws MXQueryException {
		String str[] = QName.parseQName(this.step);
		if (str[0] != null) {
			if (!str[0].equals("*")) {
				Namespace ns = context.getNamespace(str[0]);
				if (ns != null)
				this.namespace = ns.getURI();
				else
					throw new StaticException(ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE, "Prefix "+str[0]+" not bound",loc);
			} else {
				this.namespace = "*";
			}
		} else {
			this.namespace = null;
		}
		this.local = str[1];
	}

	public Token next() throws MXQueryException {
		if (!init) {
			this.init();
			this.current = getNodeIteratorOrContext(subIters, 1,context, loc);
			init = true;
		}
		if (this.current == null) {
			return Token.END_SEQUENCE_TOKEN;
		}

		while (true) {
			Token tok = getNext();

			if (tok.getEventType() == Type.END_SEQUENCE) {
				this.current = null;
				return Token.END_SEQUENCE_TOKEN;
			}			
			
			if (!Type.isNode(tok.getEventType())) 
				throw new TypeException(ErrorCodes.E0019_TYPE_STEP_RESULT_IS_ATOMIC, "Attribute axis applied on non-node",loc );
			
			if ((this.descendantOrSelf || depth == 1)) {
				
				if ( Type.isAttribute(tok.getEventType()) ) {
					String str[] = QName.parseQName(tok.getName());
					if(str[0] == null){
						/*
						 * Removing Namespace declaration from the attribute comparision!
						 */
						if(!str[1].equals("xmlns")){
							if (CheckNodeType.checkAttribute(tok, this.namespace, this.local, stepData.getType(), Context.getDictionary())) {
								return tok;
							}
						}
					} else if(!str[0].equals("xmlns")){
						if (CheckNodeType.checkAttribute(tok, this.namespace, this.local, stepData.getType(), Context.getDictionary())) {
							return tok;
						}
					}
				}
			}
		}
	}

	protected void resetImpl() throws MXQueryException {
		depth = 0;
		init = false;
		this.current = null;
		super.resetImpl();
	}

	public String getAttrStep() {
		return local;
	}

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
			throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "attr", this.step);
		serializer.attribute(null, "selfOrDescendant", "" + this.descendantOrSelf);
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		AttributeIterator iter = new AttributeIterator(context, stepData.copy(), descendantOrSelf, loc);
		iter.setSubIters(subIters);
		
		return iter;
	}
}
