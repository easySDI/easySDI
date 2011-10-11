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
import ch.ethz.mxquery.util.LongDouble;

/**
 * Double implementation that is based on one long.
 * 
 * @author David Graf
 * 
 */
public class LongDblIdentifier implements Identifier {
	private LongDouble id;
	private Source store;

	public static final int idDecimalPlaces = 8;

	private LongDblIdentifier(LongDouble id, Source store) {
		this.id = id;
		this.store = store;
	}

	protected LongDouble getId() {
		return this.id;
	}

	public int compare(Identifier toCompare) {
		if (toCompare == null) {
			throw new RuntimeException("Invalid node ID comparison");
		}
		if (toCompare.getStore() != this.store && toCompare.getStore().compare(store) != 0) {
			return toCompare.getStore().compare(store);
		}
		if (toCompare instanceof LongDblIdentifier) {
			return ((LongDblIdentifier) toCompare).getId().compareTo(this.id);
		} else {
			throw new RuntimeException("Invalid node ID comparison");
		}
	}

	public String toString() {
		return this.id.toString();
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
		return id.getIntValue()+store.hashCode();
	}

	
	public static Identifier createIdentifier(int newId, Source store) {
		return new LongDblIdentifier(new LongDouble(newId,
				LongDblIdentifier.idDecimalPlaces), store);
	}

	public static Identifier[] createInsertIdentifiers(Identifier leftBoundId,
			Identifier rightBoundId, int number, Source source)
			throws MXQueryException {
		LongDouble leftBound, rightBound;
		Identifier[] newIds = new Identifier[number];
		if (leftBoundId == null) {
			leftBound = new LongDouble(LongDblIdentifier.idDecimalPlaces);
		} else {
			leftBound = ((LongDblIdentifier) leftBoundId).getId();
		}
		if (rightBoundId == null) {
			rightBound = leftBound.add(1);
		} else {
			rightBound = ((LongDblIdentifier) rightBoundId).getId();
		}
		LongDouble stepAdder = rightBound.subtract(leftBound)
				.divide(number + 1);

		LongDouble before = leftBound;
		for (int i = 0; i < number; i++) {
			LongDouble idValue = leftBound.add(stepAdder.multiply(i + 1));
			if (idValue.compareTo(before) != 1) {
				throw new DynamicException(ErrorCodes.A0008_EC_TWO_SIMILAR_IDENTIFIERS,
						"Two similar identifiers generated (IntDblIdentifier)!", null);
			} else {
				before = idValue;
			}
			newIds[i] = new LongDblIdentifier(idValue, source);
		}
		if (rightBound.compareTo(before) != 1) {
			throw new DynamicException(ErrorCodes.A0008_EC_TWO_SIMILAR_IDENTIFIERS,
					"Two similar identifiers generated (IntDblIdentifier)!", null);
		}

		return newIds;
	}

}
