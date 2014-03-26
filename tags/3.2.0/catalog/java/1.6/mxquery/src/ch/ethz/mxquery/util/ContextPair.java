package ch.ethz.mxquery.util;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;

public class ContextPair {
	
	public XQStaticContext prevContext;
	public Context newContext;
	public ContextPair(XQStaticContext prevContext, Context newContext) {
		super();
		this.prevContext = prevContext;
		this.newContext = newContext;
	}
}
