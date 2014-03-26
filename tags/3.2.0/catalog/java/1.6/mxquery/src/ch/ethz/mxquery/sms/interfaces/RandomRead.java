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

package ch.ethz.mxquery.sms.interfaces;

import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;

public interface RandomRead extends ReadInterface{
	
	/**
	 * Gets the first token id for a given node id
	 * @param nodeId
	 * @return first element id
	 */
	public int getTokenIdForNode(int nodeId) throws MXQueryException;
	
	/**
	 * Checks if the node with the given id exits
	 * @param node
	 * @return true if a with the given id exists
	 */
	public boolean hasNode(int node) throws MXQueryException;
	
	/**
	 * Returns a token for a specific Token id if it is not outside the end node id
	 * @param tokenId
	 * @param eNode
	 * @return the token, if it falls into the requested rane
	 * @throws MXQueryException
	 */
	public Token get(int tokenId, int eNode) throws MXQueryException;
	
	/**
	 * Returns the node id for a given token id 
	 * @param lastKnowNodeId Ist jus a hint to find the token id faster. If you have no clue use 0
	 * @param activetokenId
	 * @return the token Id
	 */
	public int getNodeIdFromTokenId(int lastKnowNodeId, int activetokenId) throws MXQueryException;
	
	/**
	 * For an attribute given by name, tries to guess the token id in the buffer
	 * @param attrName - name of the attribute
	 * @param activeTokenId - last known token id 
	 * @return the supposed position for the attribute given as parameter or -1 if no knowledge is available at the time of request
	 */
	public int getAttributePosFromTokenId(String attrName, int activeTokenId) throws MXQueryException;
	
	/**
	 * For an attribute given by name, tries to guess the token id in the buffer
	 * @param attrName - name of the attribute
	 * @param nodeId - last known node id 
	 * @return the supposed position for the attribute given as parameter or -1 if no knowledge is available at the time of request
	 */
	public int getAttributePosFromNodeId(String attrName, int nodeId) throws MXQueryException;
}
