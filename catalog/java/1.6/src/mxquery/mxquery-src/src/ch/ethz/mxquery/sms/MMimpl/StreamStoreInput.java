package ch.ethz.mxquery.sms.MMimpl;

import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.sms.interfaces.StreamStore;

public class StreamStoreInput {

	int level = 0;
	private boolean endOfStream = false;
	
	boolean ignoreEoS;
	
	StreamStore buffer = null;
	/**
	 * Create a new stream store input that will stop consuming if the input 
	 * @param target the stream store that consumes the input
	 */
	public StreamStoreInput(StreamStore target) {
		buffer = target;
		ignoreEoS = false;
	}
	/**
	 * Create a new stream store input. The end of sequence of an input will be ignored, 
	 * so that a stream of multiple items can be combined 
	 * @param target the stream store that consumes the input
	 * @param ignoreItemEnds if true, end of sequence tokens are discarded
	 */
	public StreamStoreInput(StreamStore target, boolean ignoreItemEnds) {
		buffer = target;
		ignoreEoS = ignoreItemEnds;
	}
	
	
	public void endStream() throws MXQueryException {
		buffer.buffer(Token.END_SEQUENCE_TOKEN, -1);
	}
	/**
	 * Place the token into the stream store, automatically determining the item boundaries
	 * @param token an XDM token
	 * @return true if the end of of the input sequence has been reached
	 * @throws MXQueryException
	 */
	public boolean bufferNext(Token token) throws MXQueryException {
		
		int event = 0;
		
		int type = token.getEventType();
		
		if ( Type.isAttribute(type) )
			type = Type.getAttributeValueType(type);
		
		if ( Type.isTextNode(type) )
			type = Type.getTextNodeValueType(type);

		
		switch (type) {
		case Type.START_DOCUMENT:
			if (level == 0) {
				buffer.newItem();
			}
			level++;
			break;
			
		case Type.END_DOCUMENT:
			level--;
			break;
			
		case Type.START_TAG:	
			event = -2;
			if (level == 0) {
				buffer.newItem();
			}
			level++;
			break;
			
		case Type.END_TAG:
			event = -3;
			level--;
			if ( level == 0 )
			break;
		
		case Type.END_SEQUENCE:
			if (!ignoreEoS) {
				event = -1;
				endOfStream = true;
			}
			break;
		case Type.COMMENT:
		case Type.PROCESSING_INSTRUCTION:
			if (level == 0) {
				buffer.newItem();
			}
			break;
		}
		
		if ( Type.isAtomicType(type, null) || type == Type.UNTYPED) {   
			event = 1;
			if (level == 0) {
				buffer.newItem();
			}
		}
		
		buffer.buffer(token, event);
		
		return endOfStream;
	}

	
}
