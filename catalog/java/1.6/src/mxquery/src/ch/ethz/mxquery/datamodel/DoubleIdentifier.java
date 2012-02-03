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

package ch.ethz.mxquery.datamodel;

import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;

public class DoubleIdentifier implements Identifier {
	private MXQueryDouble id;
	private Source store;

	private DoubleIdentifier(MXQueryDouble id, Source store) {
		this.id = id;
		this.store = store;
	}

	protected MXQueryDouble getId() {
		return this.id;
	}

	public int compare(Identifier toCompare) {
		if (toCompare == null) {
			throw new RuntimeException("Invalid node ID comparison");
		}
		if (toCompare.getStore() != this.store && toCompare.getStore().compare(store) != 0) {
			return toCompare.getStore().compare(store);
		}
		if (toCompare instanceof DoubleIdentifier) {
			return ((DoubleIdentifier) toCompare).getId().compareTo(this.id);
		} else {
			throw new RuntimeException("Invalid node ID comparison");
		}
	}

	public String toString() {
		return String.valueOf(this.id);
	}

	public Source getStore() {
		return this.store;
	}

	public boolean equals(Object obj) {
		if (obj instanceof Identifier) {
			return this.compare((Identifier) obj) == 0;
		} else {
			return false;
		}
	}

	
	
	/* (non-Javadoc)
	 * @see java.lang.Object#hashCode()
	 */
	public int hashCode() {
		try {
			return id.getIntValue()+store.hashCode();
		}catch (MXQueryException e){ throw new RuntimeException("Internal error. Not an integer value"); }	
	}

	/**
	 * Creates an Identifier with the passed id and data soource
	 * 
	 * @param newId
	 *            Every newly appended node gets an integer values as new id
	 *            (which must be bigger then the last given id). This cannot be
	 *            handled by the Identifier because it is source dependent =>
	 *            every source creates when appending its id's itself.
	 * @param store
	 * @return a new identifier
	 */
	public static Identifier createIdentifier(int newId,
			Source store) {
		return new DoubleIdentifier(new MXQueryDouble(newId),
				store);
	}

	/**
	 * Creates Identifiers for Tokens that are between the ids
	 * <code>leftBoundId</code> and <code>rightBoundId</code>.
	 * 
	 * @param leftBoundId
	 *            left bound
	 * @param rightBoundId
	 *            right bound
	 * @param number
	 *            number of ids that the method must produce
	 * @param source
	 *            data source of the newly created ids
	 * @return array of identifiers
	 */
	public static Identifier[] createInsertIdentifiers(Identifier leftBoundId,
			Identifier rightBoundId, int number, Source source) throws MXQueryException {
		MXQueryDouble leftBound, rightBound;
		Identifier[] newIds = new Identifier[number];
		if (leftBoundId == null) {
			leftBound = new MXQueryDouble(0);
		} else {
			leftBound = ((DoubleIdentifier) leftBoundId).getId();
		}
		if (rightBoundId == null) {
			rightBound = (MXQueryDouble)leftBound.add(1);
		} else {
			rightBound = ((DoubleIdentifier) rightBoundId).getId();
		}
		MXQueryDouble stepAdder = (MXQueryDouble)rightBound.subtract(leftBound).divide(
				number + 1);
		
		MXQueryDouble before = leftBound;
		for (int i = 0; i < number; i++) {
			MXQueryDouble idValue = (MXQueryDouble)leftBound.add(stepAdder.multiply(i + 1));
			if (idValue.compareTo(before) != 1) {
				throw new DynamicException(ErrorCodes.A0008_EC_TWO_SIMILAR_IDENTIFIERS,
						"Two similar identifiers generated (DoubleIdentifier)!", null);
			} else {
				before = idValue;
			}
			newIds[i] = new DoubleIdentifier(idValue, source);
		}
		if (rightBound.compareTo(before) != 1) {
			throw new DynamicException(ErrorCodes.A0008_EC_TWO_SIMILAR_IDENTIFIERS,
					"Two similar identifiers generated (DoubleIdentifier)!", null);
		}
		
		return newIds;
	}
}
