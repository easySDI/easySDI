package examples;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.FunctionSignature;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.xdmio.XDMSerializer;

// Examples of external functions
// A function producing a single value, computed from its input
// A function with eager, pre-computed values - atomic values
// Eager functions with XML content
// A function with lazy value generation

public class ExternalFunctionExample {

    /**
     * Simple function that creates a single value, in this example an integer
     * value 42 + the a integer value
     */
    public class SingleValueExtFunction extends TokenBasedIterator {
	// TokenBasedIterator provides all the infrastructure,
	// only the function to actually compute the value is needed
	protected void init() throws MXQueryException {
	    // get Integer value from parameters
	    Token tok = subIters[0].next();
	    if (tok == Token.END_SEQUENCE_TOKEN
		    || !Type.isTypeOrSubTypeOf(tok.getEventType(),
			    Type.INTEGER, Context.getDictionary()))
		throw new DynamicException(
			ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
			"Single Integer Value expected", loc);
	    long lv = tok.getLong();
	    // compute value, put into Long Token and assign this to the current
	    // token
	    currentToken = new LongToken(Type.INTEGER, null, 42 + lv);
	}

	// MXQuery functions and operators can be copied for re-entrancy or
	// parallel execution
	protected XDMIterator copy(Context context, XDMIterator[] subIters,
		Vector nestedPredCtxStack) throws MXQueryException {
	    Iterator ret = new SingleValueExtFunction();
	    ret.setContext(context, false);
	    ret.setSubIters(subIters);
	    return ret;
	}

	// The static return type of the function
	// If this functions is not provided, ITEM is used
	// This code is in the implementation, so that the return type can be
	// computed based on the inputs
	public TypeInfo getStaticType() {
	    return new TypeInfo(Type.INTEGER, Type.OCCURRENCE_IND_EXACTLY_ONE,
		    null, null);
	}
    }

    // To be done: more complex external functions

    public void runSingleValue() throws MXQueryException {
	Context ctx = new Context();
	FunctionSignature fs = new FunctionSignature(new QName(
		XQStaticContext.URI_LOCAL, "local", "func1"),
		new TypeInfo[] { new TypeInfo(Type.INTEGER,
			Type.OCCURRENCE_IND_EXACTLY_ONE, null, null) },
		FunctionSignature.EXTERNAL_FUNCTION,
		XDMIterator.EXPR_CATEGORY_SIMPLE, false);
	Function func = new Function(null, fs, new SingleValueExtFunction());
	ctx.addFunction(func);
	XQCompiler compiler = new CompilerImpl();
	CompilerOptions co = new CompilerOptions();
	PreparedStatement statement = compiler.compile(ctx,
		"(local:func1(1),local:func1(42))", co);
	XDMIterator result = statement.evaluate();
	XDMSerializer ser = new XDMSerializer();
	System.out.println(ser.eventsToXML(result));

    }

    /**
     * @param args
     */
    public static void main(String[] args) throws MXQueryException {
	ExternalFunctionExample ex = new ExternalFunctionExample();
	ex.runSingleValue();
    }

}
