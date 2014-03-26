package ch.ethz.mxquery.sms.MMimpl;

import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.sms.interfaces.AppendUpdate;

public interface MXQueryAppendUpdate extends AppendUpdate {

	public void setContainer(WindowBuffer buf);

	public Token get(int activeTokenId) throws MXQueryException;

	public int getNodeIdFromTokenId(int lastKnownNodeId, int activeTokenId)
			throws MXQueryException;

	public boolean hasNode(int nodeId) throws MXQueryException;

	public int getTokenIdForNode(int nodeId) throws MXQueryException;

	public void setContext(Context context) throws MXQueryException;

	public Token get(int activeTokenId, int endNode) throws MXQueryException;

}
