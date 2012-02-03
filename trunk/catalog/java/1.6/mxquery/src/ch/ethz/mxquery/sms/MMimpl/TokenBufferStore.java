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

package ch.ethz.mxquery.sms.MMimpl;

import java.util.HashMap;
import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.IdentifierFactory;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
/**
 * Simple TokenBuffer to materialize a stream. Additional a index on the node id's is created to
 * allow easy jumping between nodes.
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 * @author Irina Carabus (Garbage collection, "get" method and attribute jumps)
 *
 */
public class TokenBufferStore extends FIFOStore {
	
	private int initialCapacity = 50;
	
	//private int initialCapacity = 1000;
	
	public static final int MAX_NODE_ID = Integer.MAX_VALUE - 1;
	
	private double capacityIncrement = 1.5;
	
	private int nodeIndexCapacity = 10;
	
	private double nodeIndexIncrement = 2;
	
	public int level;
	
	private int tokenI = 0;
	
	private int nodeI = 0;
	
	private int[] nodeIndex;
	
	public Token[] tokenBuffer;
	
	private XDMIterator sourceStream;
	
	private boolean endOfStr = false;
	
	private int toksDel = 0;
	private int nodesDel = 0;
	
	public int myId;
	
	protected Identifier last_identifier = null;
	protected Identifier last_start_tag_identifier = null;
	protected Identifier last_start_document_identifier = null;

	private HashMap attributes = new HashMap();
	//private boolean doneAttr = false;
	
	private boolean endOfItem = false;

	public void start(){
	
	}
	protected boolean generateNodeIds = false;

	protected static int descendantCounter = 0;
	protected static int nodeIdCount = 0;

	
	/**
	 * Creates a new TokenBuffer for a stream with standard paras 
	 * @param sourceStream Source Stream
	 */
	public TokenBufferStore(XDMIterator sourceStream, int id, WindowBuffer container) {
		
		super(id, container);
		myId = id;
		
		this.sourceStream = sourceStream;
		
		tokenBuffer = new Token[initialCapacity];
		nodeIndex = new int[nodeIndexCapacity];
	}
	
	public TokenBufferStore(int id, WindowBuffer container) {
		super(id, container);
		myId = id;
		
		tokenBuffer = new Token[initialCapacity];
		nodeIndex = new int[nodeIndexCapacity];
	}
	
	public void setIterator(XDMIterator it){
		this.sourceStream = it;
	}
	
	/**
	 * Sets the context of the source stream. 
	 * This is especially necassary if the window iterator is used for external variables
	 * @param context
	 * @throws MXQueryException 
	 */
	public void setContext(Context context) throws MXQueryException {
		sourceStream.setContext(context, true);
	}
	
	/**
	 * Returns the position for a given NodePosition. If the node id is higher
	 * than the availabe source nodes the latest position is returned
	 * 
	 * @param nodeId 
	 * @return the token id
	 * @throws MXQueryException
	 */
	public int getTokenIdForNode(int nodeId) throws MXQueryException {
		
		if (nodeId < nodeI ) {
			int arrayId = nodeId-nodesDel;
			
			if ( arrayId < 0 ){
//				System.out.println("Node id : "+nodeId);
//				System.out.println("Nodes deleted : "+nodesDel);
			}
			
			return nodeIndex[arrayId];
		} else {
			while ( nodeI <= nodeId && !endOfStr) {				
				bufferNext();
			}
			if (endOfStr) {
				return tokenI - 1;
			} else {
				int arrayId = nodeId-nodesDel;
				return nodeIndex[arrayId];
			}
		}
	}
	
	/**
	 * Checks if a node exists
	 * 
	 * @param node a node id
	 * @return true if the node for the given id exists
	 * @throws MXQueryException
	 */
	public boolean hasNode(int node) throws MXQueryException {
		// Makes sure we have materialized to the given node
			get(getTokenIdForNode(node));
		if (node < nodeI ) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns the NodeId for the given Token Id; The first node has the id = 0.
	 * If the position is not reachable, the last node id is returned.
	 * 
	 * @param tokenId
	 * @return the node id >=0
	 * @throws MXQueryException 
	 */
	public int getNodeIdFromTokenId(int tokenId) throws MXQueryException {
		return getNodeIdFromTokenId(0, tokenId);
	}
	
	/**
	 * Like getNodeIdFromPosition(int position) but we give a additional
	 * information that makes the search for the right nodeId faster.
	 * 
	 * @param minNodeId
	 * @param tokenId
	 * @return the nodeId
	 * @throws MXQueryException 
	 */
	public int getNodeIdFromTokenId(int minNodeId, int tokenId) throws MXQueryException  {
		// Makes sure, that the position is materialized
		get(tokenId);
		
		if (endOfStr && (tokenId >= tokenI)) {
			return nodeI;
		}
		
		int i = (minNodeId + 1) - nodesDel;
		
		if (i < 0)
			i = 1;
		
		while (i < ( nodeI-nodesDel ) && nodeIndex[i] <= tokenId ) {
			i++;
		}
		return i - 1;
	}
	
	/**
	 * Buffers the next token from the stream
	 * 
	 * @throws MXQueryException
	 */
	
	private void bufferNext() throws MXQueryException{
		
		Token token = sourceStream.next();

		/*if ((sourceStream instanceof ValidateIterator) &&  token.getEventType() == Type.START_DOCUMENT)
			while (token.getEventType()!=Type.START_TAG){
				token = sourceStream.next();
		 */
		endOfItem = false;
		int type = token.getEventType();

		if ( Type.isAttribute(type) )
			type = Type.getAttributeValueType(type);

		if ( Type.isTextNode(type) )
			type = Type.getTextNodeValueType(type);

		// only starting tags (and document) need new ids
		switch (type) {
		case Type.START_DOCUMENT:
			if (level == 0) {
				indexNewNode();
			}
			level++;
			if (generateNodeIds){
				Identifier id = createNextTokenId();
				last_start_document_identifier = id;
				token.setId(id);
			}
			break;

		case Type.END_DOCUMENT:
			level--;
			if (level == 0) {
				endOfItem = true;
			}
			token.setId(last_start_document_identifier);
			break;

		case Type.START_TAG:	
			if (level == 0) {
				indexNewNode();
			}
			level++;
			if (generateNodeIds){
				Identifier id = createNextTokenId();
				last_start_tag_identifier = id;
				token.setId(id);
			}
			break;

		case Type.END_TAG:
			level--;
			if (level == 0) {
				endOfItem = true;
			}
			//token.setId(last_start_tag_identifier);
			break;

		case Type.END_SEQUENCE:

			endOfStr = true;
			break;
		case Type.COMMENT:
		case Type.PROCESSING_INSTRUCTION:
			if (level == 0) {
				indexNewNode();
				endOfItem = true;
			}
			if (generateNodeIds)
				token.setId(createNextTokenId());
			break;
		}

		if ( Type.isAtomicType(type, Context.getDictionary()) || type == Type.UNTYPED) {   

			if (level == 0) {
				indexNewNode();
				endOfItem = true;
			}
			if (generateNodeIds){
				if ((token instanceof TextToken)|| token instanceof NamedToken){
					
					if (token instanceof TextToken || token instanceof NamedToken){
						descendantCounter ++;
						level++;
						Identifier id = createNextTokenId();
						token.setId(id);
						level--;
					}
				}
				else{
					token.setId(createNextTokenId());
				}
			}
		}


		bufferToken(token);
		
	}

	protected Identifier createNextTokenId() {
        if (generateNodeIds) {
            last_identifier = IdentifierFactory.createIdentifier(nodeIdCount++,this,last_identifier, (short)(level));
            return last_identifier;
        } else
            return null;
    } 
	
	
	public void printBuffer()
	{
		for ( int i=0; i<tokenI-toksDel; i++)
			if ( tokenBuffer[i].getEventType() == Type.INT )
				System.out.println(tokenBuffer[i].getLong());
			else
				System.out.println(tokenBuffer[i].getName());
		System.out.println("Token buffer size : "+tokenBuffer.length);
		System.out.println("Items gone : "+nodeI);
		System.out.println("Items left : "+(nodeI-nodesDel));
		System.out.println("tokens gone : "+tokenI);
		System.out.println("tokens left : "+(tokenI-toksDel));

	}
	
	/**
	 * Returns the Token for a given token id
	 * @param tokenId
	 * @return the token for this Id
	 * @throws MXQueryException 
	 */
	public Token get(int tokenId) throws MXQueryException{
		
		//System.out.println(Thread.currentThread().getName()+" request for get in token buffer sync store");
		
		if (tokenI > tokenId) {
			return tokenBuffer[tokenId-toksDel];
		} else {
			if (endOfStr) {
				return tokenBuffer[tokenI - 1 - toksDel];
			} else {
				while (tokenId >= tokenI && !endOfStr){
					bufferNext();
				}
				int i = tokenI - 1 - toksDel;
				return tokenBuffer[i];
			}
		}
	}
	
	/**
	 * Returns the token for a given tokenId. If the token is corresponds to a higher nodeId than maxNodeId, the 
	 * END_SEQUENCE token is returned. 
	 * 
	 * @param tokenId Token Id
	 * @param maxNodeId Max Token Id
	 * @return the token, or END_SEQENCE if out of range
	 * @throws MXQueryException 
	 */
	public Token get(int tokenId, int maxNodeId) throws MXQueryException{

		if ((maxNodeId + 1) < nodeI){
			if (tokenId == nodeIndex[(maxNodeId + 1)-nodesDel]) {
				return Token.END_SEQUENCE_TOKEN;
			}
		}
		
		Token token = null;
		
		if (tokenI > tokenId) {
			token = tokenBuffer[tokenId-toksDel];
		} else {
			if (endOfStr) {
				token = tokenBuffer[tokenI - 1 - toksDel];
			} else {
				while (tokenId >= tokenI && !endOfStr && (maxNodeId+1 < nodeI)){
					bufferNext();
				}
				if ( tokenId >= tokenI && !endOfStr ){
					while (tokenId >= tokenI && !endOfStr && ( !endOfItem || maxNodeId+1 > nodeI) ) {

							bufferNext();
					}
				}
				
				if ( tokenId >= tokenI)
					token = Token.END_SEQUENCE_TOKEN;
				else
					token = tokenBuffer[tokenId - toksDel];
			}
		}
		
//		Token token = get(tokenId);		
//		if ((maxNodeId + 1) < nodeI){
//			if (tokenId == nodeIndex[(maxNodeId + 1)-nodesDel]) {
//				return Token.END_SEQUENCE_TOKEN;
//			}
//		}
		return token;
	}
	
	/**
	 * Returns the current materialized nodeId
	 * @return the node id of the last materialized node
	 */
	public int getMaxNodeId() {
		return nodeI-1;
	}
	
	/**
	 * Returns the current materialized tokenId
	 * @return the token id of the last materialized node
	 */
	public int getMaxTokenId() {
		return tokenI-1;
	}
	
	/**
	 * Index a new Node
	 *
	 */
	private void indexNewNode() {
		
		if ((nodeI-nodesDel) == nodeIndex.length) {
			nodeIndexCapacity = (int) Math.ceil(nodeIndexCapacity * nodeIndexIncrement);
			int[] newIndex = new int[nodeIndexCapacity];
			System.arraycopy(nodeIndex, 0, newIndex, 0, nodeI-nodesDel);
			nodeIndex = newIndex;
		}
		try{
			nodeIndex[nodeI-nodesDel] = tokenI;
		}
		catch(ArrayIndexOutOfBoundsException e){
			System.out.println("Capacity : "+nodeIndexCapacity+" Node i : "+nodeI+" nodes del: "+nodesDel);
			throw new ArrayIndexOutOfBoundsException();
		}
			
		nodeI++;
	}
	
	/**
	 * Buffer a new token. 
	 * @param token
	 */
	private void bufferToken(Token token) {
		
		if ((tokenI-toksDel) == tokenBuffer.length) {
			
			initialCapacity = (int) Math.ceil(initialCapacity * capacityIncrement);
			Token[] newTokenBuffer = new Token[initialCapacity];
			System.arraycopy(tokenBuffer, 0, newTokenBuffer, 0, tokenI-toksDel);
			tokenBuffer = newTokenBuffer;
		}
		tokenBuffer[tokenI-toksDel] = token;
		tokenI++;
	}
	
	public int getSize(){
		return tokenBuffer.length;
	}
	
	/**
	 * Simple garbage collection; deletes all nodes that are older than the parameter
	 * @param olderThanItemId
	 * @throws MXQueryException 
	 */
	public void deleteItems(int olderThanItemId) throws MXQueryException
	{
		
		//System.out.println("Java memory in use = " + (Runtime.getRuntime().totalMemory()-Runtime.getRuntime().freeMemory()));
	
		if ( nodeI == 0 )
			return;
		
		if ( olderThanItemId <= 1 || olderThanItemId <= nodesDel){
			return;
		}			
		
			
			
			//System.out.println("Delete from : "+olderThanItemId);
			int nextTokId = -1;
			
		
			nextTokId = getTokenIdForNode(olderThanItemId);
			
			//System.out.println(this.myId + ": Delete until: " + olderThanItemId + ", Current-Node-Id:" + tokenI + ", Remaining:" + (tokenI - nextTokId) );
			
			initialCapacity = tokenI-nextTokId;
			Token[] newTokenBuffer = new Token[initialCapacity];
			
			System.arraycopy(tokenBuffer, nextTokId-toksDel , newTokenBuffer, 0, tokenI-nextTokId);								
			
			tokenBuffer = newTokenBuffer;					
			
			nodeIndexCapacity = nodeI - olderThanItemId;
			int[] newIndex = new int[nodeIndexCapacity];
			System.arraycopy(nodeIndex, olderThanItemId-nodesDel, newIndex, 0, nodeI - olderThanItemId);
			
			nodeIndex = null;
			nodeIndex = newIndex;
			
			toksDel = nextTokId;
			nodesDel = olderThanItemId;					
			
			//System.out.println("End delete from : "+olderThanItemId);
			
			//System.gc();
			
		
	}

	public int getAttributePosFromTokenId(String attrName, int activeTokenId) throws MXQueryException{
		
		if (!attributes.containsKey(attrName))
			return -1;
		
		int offset = ((Integer)attributes.get(attrName)).intValue();
		
		int node = getNodeIdFromTokenId(0,activeTokenId);
		int tokenId = -1;
		
		tokenId = getTokenIdForNode(node);
		
		if ( tokenId+offset >= tokenI )
			return -1;
		
		return tokenId+offset;
	}
	
	public int getAttributePosFromNodeId(String attrName, int nodeId) throws MXQueryException {
		
		if (!attributes.containsKey(attrName))
			return -1;
		
		int offset = ((Integer)attributes.get(attrName)).intValue();
		
		int tokenId = -1;
		
		tokenId = getTokenIdForNode(nodeId);
		
		Token tok = get(tokenId+offset,nodeId);
		
		if( tok == Token.END_SEQUENCE_TOKEN )
			return -1;
		
		if (tokenId+offset >= tokenI)
			return -1;
		
		return tokenId+offset;
	}

	public int getMyId(){
		return myId;
	}
	
	public void buffer(Token tok, int event){
		
	}
	
	public void newItem(){
		
	}
	
    protected void addNewBufferNode(){}
	
	protected BufferItem bufferToToken(int activeTokenId){ return null; }
	
	protected BufferItem bufferToItem(int nodeId) {return null;}
	
	protected void freeBuffers() {}

	public int compare(Source store) {
		return 0;
	}

	public Source copySource(Context ctx, Vector nestedPredCtxStack) throws MXQueryException {
		// TODO Auto-generated method stub
		return null;
	}

	public String getURI() {
		// TODO Auto-generated method stub
		return null;
	}


	

}
