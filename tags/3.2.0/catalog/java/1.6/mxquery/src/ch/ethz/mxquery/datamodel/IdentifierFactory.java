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

import ch.ethz.mxquery.exceptions.MXQueryException;


public class IdentifierFactory {
	public final static int ID_DOUBLE = 1;
	public final static int ID_INT_DOUBLE = 2;
	public final static int ID_LONG_DOUBLE = 3;
	public final static int ID_DEWEY = 4;
	public static final int idType = 1;

	/**
	 * Creates an Identifier with the passed id and data source
	 * @param nrOfAppendedNode the number of nodes to append
	 * @param store
	 * @param last_identifier
	 * @param level
 
	 * @return a new node identifier
	 */	
	public static Identifier createIdentifier(int nrOfAppendedNode,
			Source store, Identifier last_identifier, short level) {
		return createIdentifier(nrOfAppendedNode,
				store, last_identifier, level, IdentifierFactory.idType); 
	}	
	
	/**
	 * Creates an Identifier with the passed id and data source
	 * @param nrOfAppendedNode
	 * @param store
	 * @param last_identifier
	 * @param level
	 * @param type
	 * @return a new node identifier
	 */
	
	
	public static Identifier createIdentifier(int nrOfAppendedNode,
			Source store, Identifier last_identifier, short level, int type) {
		Identifier id = null;
		switch (type) {
		case IdentifierFactory.ID_DOUBLE:
			id = DoubleIdentifier
					.createIdentifier(nrOfAppendedNode, store);
			break;
		case IdentifierFactory.ID_INT_DOUBLE:
			id = IntDblIdentifier
					.createIdentifier(nrOfAppendedNode, store);
			break;
		case IdentifierFactory.ID_LONG_DOUBLE:
			id = LongDblIdentifier.createIdentifier(nrOfAppendedNode,
					store);
			break;
		case IdentifierFactory.ID_DEWEY:
			
			if (last_identifier == null){
				int[] dewey_id = new int[1];
				level = 0;
				DeweyIdentifier lastidentifier = new DeweyIdentifier(dewey_id,level,store);	
				id = DeweyIdentifier.createIdentifier(lastidentifier,level,store);
			}
			
			else if (last_identifier instanceof DeweyIdentifier){
				id = DeweyIdentifier.createIdentifier(last_identifier,level,store);
			}
			break;
		}
		return id;
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
	 * @throws MXQueryException
	 */
	
	public static Identifier[] createInsertIdentifiers(Identifier leftBoundId,
			Identifier rightBoundId, int number, Source source, int[] levels)
			throws MXQueryException {
		return createInsertIdentifiers(leftBoundId,
				rightBoundId, number, source, levels, IdentifierFactory.idType);
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
	 * @param type
	 * 			  Explicit specification of the type of identifier
	 * @throws MXQueryException
	 */
	
	
	public static Identifier[] createInsertIdentifiers(Identifier leftBoundId,
			Identifier rightBoundId, int number, Source source, int[] levels, int type)
			throws MXQueryException {
		Identifier[] ids = null;
		switch (type) {
		case IdentifierFactory.ID_DOUBLE:
			ids = DoubleIdentifier.createInsertIdentifiers(leftBoundId,
					rightBoundId, number, source);
			break;
		case IdentifierFactory.ID_INT_DOUBLE:
			ids = IntDblIdentifier.createInsertIdentifiers(leftBoundId,
					rightBoundId, number, source);
			break;
		case IdentifierFactory.ID_LONG_DOUBLE:
			ids = LongDblIdentifier.createInsertIdentifiers(leftBoundId,
					rightBoundId, number, source);
			break;
		case IdentifierFactory.ID_DEWEY:
			ids = DeweyIdentifier.createInsertIdentifiers(leftBoundId,
					rightBoundId, number, source,levels);
		}
		return ids;
	}
}

