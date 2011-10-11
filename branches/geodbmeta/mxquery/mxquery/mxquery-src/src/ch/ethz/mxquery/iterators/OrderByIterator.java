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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Comparator;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.MergeSort;
import ch.ethz.mxquery.util.OrderOptions;
import ch.ethz.mxquery.util.QuickSort;

/**
 * Represents an order by expression of a flwor expression.<br/> TODO Collation &
 * stable
 * 
 * @author David Alexander Graf
 * 
 */
public class OrderByIterator extends CurrentBasedIterator {
	OrderOptions[] orderOptions;

	OrderElement[] orderElements;

	int accessIndex = 0;

	boolean stable;

	public OrderByIterator(Context ctx, XDMIterator[] orderIterators,
			OrderOptions[] orderOptions, boolean stable, QueryLocation location)
			throws MXQueryException {
		super(ctx, location);
		this.subIters = orderIterators;
		this.orderOptions = orderOptions;
		this.stable = stable;
	}
	
	class OrderElement {
		Window window;

		Token[] keys;

		public OrderElement(Window window, Token[] keys) {
			this.window = window;
			this.keys = keys;
		}
	}

	public class OrderElComp implements Comparator {
		public int compare(Object arg0, Object arg1) throws MXQueryException {
			if (arg0 instanceof OrderElement && arg1 instanceof OrderElement) {
				OrderElement oe0 = (OrderElement) arg0;
				OrderElement oe1 = (OrderElement) arg1;
				for (int i = 0; (i < oe0.keys.length) && (i < oe1.keys.length); i++) {
					Token t0 = oe0.keys[i];
					Token t1 = oe1.keys[i];
					int com = 0;

					if (t0 != null && t1 != null) {
						if (((t0.getEventType() == Type.DOUBLE || t0.getEventType() == Type.FLOAT) && t0.getDouble().isNaN()) && 
								((t1.getEventType() == Type.DOUBLE || t1.getEventType() == Type.FLOAT) && t1.getDouble().isNaN()))
							com = 0;
						else if (((t0.getEventType() == Type.DOUBLE || t0.getEventType() == Type.FLOAT) && t0.getDouble().isNaN())) {
							if (OrderByIterator.this.orderOptions[i].isEmptiesGreatest()) {
								com = 1;
							} else {
								com = -1;
							}
						}
						else if (((t1.getEventType() == Type.DOUBLE || t1.getEventType() == Type.FLOAT) && t1.getDouble().isNaN())) {
							if (OrderByIterator.this.orderOptions[i].isEmptiesGreatest()) {
								com = -1;
							} else {
								com = 1;
							}
						}
						else	
							com = Token.compare(t0, t1);
					} else if (t0 == null && t1 == null) {
						return 0;
					} else if (t0 == null || ((t0.getEventType() == Type.DOUBLE || t0.getEventType() == Type.FLOAT) && t0.getDouble().isNaN())) {
						if (OrderByIterator.this.orderOptions[i].isEmptiesGreatest()) {
							com = 1;
						} else {
							com = -1;
						}
					} else if (t1 == null|| ((t0.getEventType() == Type.DOUBLE || t0.getEventType() == Type.FLOAT) && t0.getDouble().isNaN())) {
						if (OrderByIterator.this.orderOptions[i].isEmptiesGreatest()) {
							com = -1;
						} else {
							com = 1;
						}
					} 
					if (com != 0) {
						if (OrderByIterator.this.orderOptions[i].isAscending()) {
							return com;
						} else {
							return -com;
						}
					}
				}
				return 0;
			} else {
				throw new DynamicException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "Comparison not possible!", loc);
			}
		}

	}



	/**
	 * Computes the key of the current context => Computes for every order by
	 * expression a Token that contains the key value.
	 * 
	 * @return the sequence of order keys
	 * @throws MXQueryException
	 */
	public Token[] getCurrentKey() throws MXQueryException {
		Token[] keys = new Token[this.subIters.length];
		for (int i = 0; i < this.subIters.length; ++i) {
			Token tok = subIters[i].next();
			if (tok.getEventType() != Type.END_SEQUENCE) {
				keys[i] = tok;
			} else {
				keys[i] = null;
			}
			if (subIters[i].next() != Token.END_SEQUENCE_TOKEN)
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Ordering expression evaluates to a sequence, not a single value", loc);
		}
		return keys;
	}

	/**
	 * Sorts the passed order elements.
	 * 
	 * @param orderElements
	 * @throws MXQueryException 
	 */
	public void setup(OrderElement[] orderElements) throws MXQueryException {
		if (this.stable) {
			MergeSort.sort(orderElements, new OrderElComp());
		} else {
			QuickSort.sort(orderElements, new OrderElComp());
		}
		this.orderElements = orderElements;
		if (this.orderElements.length > 0) {
			this.current = this.orderElements[0].window;
		} else {
			this.endOfSeq = true;
		}
	}

	public Token next() throws MXQueryException {
		if (this.orderElements == null) {
			throw new DynamicException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
					"OrderByIteator cannot be used befor the Order Elements are set up!", loc);
		}
		if (this.endOfSeq) {
			return Token.END_SEQUENCE_TOKEN;
		}
		Token tok = this.current.next();
		int type = tok.getEventType();
		while (type == Type.END_SEQUENCE) {
			this.accessIndex++;
			if (this.accessIndex >= this.orderElements.length) {
				this.endOfSeq = true;
				return Token.END_SEQUENCE_TOKEN;
			} else {
				this.current = this.orderElements[this.accessIndex].window;
				tok = this.current.next();
				type = tok.getEventType();
			}
		}
		return tok;
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		this.orderElements = null;
		this.accessIndex = 0;
	}

	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer)
			throws Exception {
		this.createIteratorStartTag(serializer);
		for (int i = 0; i < this.subIters.length; i++) {
			OrderOptions oOpt = this.orderOptions[i];
			serializer.startTag(null, "OrderByExpr");
			serializer.attribute(null, "ascending", String.valueOf(oOpt
					.isAscending()));
			if (oOpt.isEmptiesGreatest()) {
				serializer.attribute(null, "empties", "greatest");
			} else {
				serializer.attribute(null, "empties", "least");

			}				
			this.subIters[i].traverseIteratorTree(serializer);
			serializer.endTag(null, "OrderByExpr");
		}
		this.createIteratorEndTag(serializer);
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new OrderByIterator(context, 
				subIters, 
				copyOrderOptions(orderOptions), 
				stable, loc);
	}
	
	protected static OrderOptions[] copyOrderOptions(OrderOptions[] oos) {
		if (oos == null) {
			return null;
		}
		
		OrderOptions[] newOos = new OrderOptions[oos.length];
		for (int i=0; i<oos.length; i++) {
			newOos[i] = oos[i].copy();
		}
		
		return newOos;
	}	
	
}