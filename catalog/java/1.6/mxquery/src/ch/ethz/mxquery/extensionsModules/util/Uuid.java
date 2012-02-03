package ch.ethz.mxquery.extensionsModules.util;

import java.util.UUID;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Uuid extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		currentToken = new TextToken(null,UUID.randomUUID().toString());
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
	throws MXQueryException {
		XDMIterator ret = new Uuid();
		ret.setSubIters(subIters);
		ret.setContext(context, false);
		return ret;
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		

}
