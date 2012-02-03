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

package ch.ethz.mxquery.functions;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.functions.fn.DateTimeAdjustTimezone;
import ch.ethz.mxquery.functions.fn.DateTimeValues;
import ch.ethz.mxquery.functions.fn.DayTimeDurationValues;
import ch.ethz.mxquery.functions.fn.Doc;
import ch.ethz.mxquery.functions.fn.MaxMin;
import ch.ethz.mxquery.functions.xs.XSBinary;
import ch.ethz.mxquery.functions.xs.XSGregorian;
import ch.ethz.mxquery.functions.xs.XSInteger;
import ch.ethz.mxquery.functions.xs.XSString;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * Holds information on the function metadata and methods to retrieve the
 * implementation, either by an explicit iterator or by the name of a class to
 * load
 * 
 * @author Peter Fischer
 * 
 */
public class Function {

    protected String className;

    protected FunctionSignature signature;

    protected XDMIterator iter;

    public Function(String className, FunctionSignature signature,
	    XDMIterator iter) {
	this.className = className;
	this.iter = iter;
	this.signature = signature;
    }

    /**
     * Adapts the signature of the function to become an external class
     * 
     * @param prefix
     *                The namespace prefix in which the function should appear
     * @return a copy of the function with an adapted signature
     */
    public Function getAsExternalFunction(String prefix) {
	QName fName = new QName(signature.getName().getNamespaceURI(), prefix,
		signature.getName().getLocalPart());
	FunctionSignature newSig = new FunctionSignature(fName, signature
		.getParameterTypes(), FunctionSignature.EXTERNAL_FUNCTION,
		signature.getExpressionCategory(), false);
	return new Function(className, newSig, iter);
    }

    /**
     * Returns an implementation of this function as an iterator.
     * 
     * @return an XDM iterator representing this function
     */
    public XDMIterator getFunctionImplementation(Context targetContext)
	    throws MXQueryException {
	if (iter != null) {
	    iter.setContext(targetContext, true);
	    return iter.copy(targetContext, null, true, new Vector());
	} else {
	    XDMIterator it = loadClass();
	    it.setContext(targetContext, true);
	    return it;
	}
    }

    /**
     * Get the signature of this function
     * 
     * @return a copy of the signature
     * @throws MXQueryException
     */
    public FunctionSignature getFunctionSignature() throws MXQueryException {
	if (signature != null) {
	    return signature.copy();
	} else {
	    // TODO: what's the right policy here?
	    return null;
	}
    }

    /**
     * Loads a specific class!
     * 
     * @return
     */
    private XDMIterator loadClass() throws MXQueryException {
	try {
	    if (className.indexOf("DateTimeValues") >= 0) {

		DateTimeValues dtvf = (DateTimeValues) Class.forName(
			"ch.ethz.mxquery.functions.fn.DateTimeValues")
			.newInstance();

		if (className.endsWith("Years")) {
		    dtvf.setTypeOfRequest(DateTimeValues.YEARS);
		} else if (className.endsWith("Months")) {
		    dtvf.setTypeOfRequest(DateTimeValues.MONTHS);
		} else if (className.endsWith("Days")) {
		    dtvf.setTypeOfRequest(DateTimeValues.DAYS);
		} else if (className.endsWith("Hours")) {
		    dtvf.setTypeOfRequest(DateTimeValues.HOURS);
		} else if (className.endsWith("Minutes")) {
		    dtvf.setTypeOfRequest(DateTimeValues.MINUTES);
		} else if (className.endsWith("Seconds")) {
		    dtvf.setTypeOfRequest(DateTimeValues.SECONDS);
		}
		if (className.endsWith("Timezone")) {
		    dtvf.setTypeOfRequest(DateTimeValues.TIMEZONE);
		}
		return dtvf;
	    }

	    if (className.indexOf("DayTimeDurationValues") >= 0) {

		DayTimeDurationValues dtdvf = (DayTimeDurationValues) Class
			.forName(
				"ch.ethz.mxquery.functions.fn.DayTimeDurationValues")
			.newInstance();

		if (className.endsWith("Days")) {
		    dtdvf.setTypeOfRequest(DayTimeDurationValues.DAYS);
		} else if (className.endsWith("Hours")) {
		    dtdvf.setTypeOfRequest(DayTimeDurationValues.HOURS);
		} else if (className.endsWith("Minutes")) {
		    dtdvf.setTypeOfRequest(DayTimeDurationValues.MINUTES);
		} else if (className.endsWith("Seconds")) {
		    dtdvf.setTypeOfRequest(DayTimeDurationValues.SECONDS);
		} else if (className.endsWith("Months")) {
		    dtdvf.setTypeOfRequest(DayTimeDurationValues.MONTHS);
		} else if (className.endsWith("Years")) {
		    dtdvf.setTypeOfRequest(DayTimeDurationValues.YEARS);
		}

		return dtdvf;
	    }

	    if (className.indexOf("DateTimeAdjustTimezone") >= 0) {

		DateTimeAdjustTimezone tz = (DateTimeAdjustTimezone) Class
			.forName(
				"ch.ethz.mxquery.functions.fn.DateTimeAdjustTimezone")
			.newInstance();

		if (className.endsWith("-DateTime")) {
		    tz
			    .setTypeOfRequest(DateTimeAdjustTimezone.DATE_TIME_TIMEZONE);
		} else if (className.endsWith("-Date")) {
		    tz.setTypeOfRequest(DateTimeAdjustTimezone.DATE_TIMEZONE);
		} else if (className.endsWith("-Time")) {
		    tz.setTypeOfRequest(DateTimeAdjustTimezone.TIME_TIMEZONE);
		}

		return tz;
	    }

	    if (className.indexOf("ch.ethz.mxquery.functions.xs.XSString") >= 0) {

		XSString strF = (XSString) Class.forName(
			"ch.ethz.mxquery.functions.xs.XSString").newInstance();

		if (className.endsWith("-string")) {
		    strF.setTargetType(Type.STRING);
		} else if (className.endsWith("-untypedAtomic")) {
		    strF.setTargetType(Type.UNTYPED_ATOMIC);
		} else if (className.endsWith("-anyURI")) {
		    strF.setTargetType(Type.ANY_URI);
		} else if (className.endsWith("-normalizedString")) {
		    strF.setTargetType(Type.NORMALIZED_STRING);
		} else if (className.endsWith("-token")) {
		    strF.setTargetType(Type.TOKEN);
		} else if (className.endsWith("-language")) {
		    strF.setTargetType(Type.LANGUAGE);
		} else if (className.endsWith("-Name")) {
		    strF.setTargetType(Type.NAME);
		} else if (className.endsWith("-NCName")) {
		    strF.setTargetType(Type.NCNAME);
		} else if (className.endsWith("-Entity")) {
		    strF.setTargetType(Type.ENTITY);
		}
		if (className.endsWith("-Nmtoken")) {
		    strF.setTargetType(Type.NMTOKEN);
		}
		if (className.endsWith("-ID")) {
		    strF.setTargetType(Type.ID);
		}
		if (className.endsWith("-IDREF")) {
		    strF.setTargetType(Type.IDREF);
		}
		return strF;
	    }

	    if (className.indexOf("ch.ethz.mxquery.functions.xs.XSGregorian") >= 0) {

		XSGregorian gregF = (XSGregorian) Class.forName(
			"ch.ethz.mxquery.functions.xs.XSGregorian")
			.newInstance();

		if (className.endsWith("-gYearMonth")) {
		    gregF.setTargetType(Type.G_YEAR_MONTH);
		} else if (className.endsWith("-gYear")) {
		    gregF.setTargetType(Type.G_YEAR);
		} else if (className.endsWith("-gMonth")) {
		    gregF.setTargetType(Type.G_MONTH);
		} else if (className.endsWith("-gMonthDay")) {
		    gregF.setTargetType(Type.G_MONTH_DAY);
		} else if (className.endsWith("-gDay")) {
		    gregF.setTargetType(Type.G_DAY);
		}

		return gregF;
	    }

	    if (className.indexOf("ch.ethz.mxquery.functions.xs.XSBinary") >= 0) {

		XSBinary binF = (XSBinary) Class.forName(
			"ch.ethz.mxquery.functions.xs.XSBinary").newInstance();

		if (className.endsWith("-base64Binary")) {
		    binF.setTargetType(Type.BASE64_BINARY);
		} else if (className.endsWith("-hexBinary")) {
		    binF.setTargetType(Type.HEX_BINARY);
		}

		return binF;
	    }

	    if (className.indexOf("ch.ethz.mxquery.functions.fn.MaxMin") >= 0) {

		MaxMin minmaxF = (MaxMin) Class.forName(
			"ch.ethz.mxquery.functions.fn.MaxMin").newInstance();

		boolean val = false;
		if (className.endsWith("Max")) {
		    val = true;
		}

		minmaxF.setMAXorMIN(val);
		return minmaxF;
	    }

	    if (className.indexOf("ch.ethz.mxquery.functions.xs.XSInteger") >= 0) {

		XSInteger intConstrF = (XSInteger) Class.forName(
			"ch.ethz.mxquery.functions.xs.XSInteger").newInstance();

		if (className.endsWith("integer")) {
		    intConstrF.setTargetType(Type.INTEGER);
		} else if (className.endsWith("long")) {
		    intConstrF.setTargetType(Type.LONG);
		} else if (className.endsWith("int")) {
		    intConstrF.setTargetType(Type.INT);
		} else if (className.endsWith("short")) {
		    intConstrF.setTargetType(Type.SHORT);
		} else if (className.endsWith("byte")) {
		    intConstrF.setTargetType(Type.BYTE);
		} else if (className.endsWith("nonPositiveInteger")) {
		    intConstrF.setTargetType(Type.NON_POSITIVE_INTEGER);
		} else if (className.endsWith("negativeInteger")) {
		    intConstrF.setTargetType(Type.NEGATIVE_INTEGER);
		} else if (className.endsWith("nonNegativeInteger")) {
		    intConstrF.setTargetType(Type.NON_NEGATIVE_INTEGER);
		} else if (className.endsWith("unsignedLong")) {
		    intConstrF.setTargetType(Type.UNSIGNED_LONG);
		} else if (className.endsWith("unsignedInt")) {
		    intConstrF.setTargetType(Type.UNSIGNED_INT);
		} else if (className.endsWith("unsignedShort")) {
		    intConstrF.setTargetType(Type.UNSIGNED_SHORT);
		} else if (className.endsWith("unsignedByte")) {
		    intConstrF.setTargetType(Type.UNSIGNED_BYTE);
		} else if (className.endsWith("positiveInteger")) {
		    intConstrF.setTargetType(Type.POSITIVE_INTEGER);
		}

		return intConstrF;
	    }
	    if (className.indexOf("Doc-Tidy") >= 0) {
		Doc doc = (Doc) (Class
			.forName("ch.ethz.mxquery.functions.fn.Doc")
			.newInstance());
		doc.setTidyInput(true);
		return doc;
	    }
	    if (className
		    .indexOf("ch.ethz.mxquery.extensionsModules.zorbaRest.Http") >= 0) {
		RequestTypeMulti httpClient = (RequestTypeMulti) (Class
			.forName("ch.ethz.mxquery.extensionsModules.zorbaRest.HttpIO")
			.newInstance());
		if (className.endsWith("Get"))
		    httpClient.setRequestType("get");
		if (className.endsWith("GetTidy"))
		    httpClient.setRequestType("getTidy");
		if (className.endsWith("Post"))
		    httpClient.setRequestType("post");
		if (className.endsWith("Put"))
		    httpClient.setRequestType("put");
		return (XDMIterator) httpClient;
	    }
	    if (className
		    .indexOf("ch.ethz.mxquery.extensionsModules.math.TransMath") >= 0) {
		RequestTypeMulti transMath = (RequestTypeMulti) (Class
			.forName("ch.ethz.mxquery.extensionsModules.math.TransMath")
			.newInstance());
		if (className.endsWith("Exp"))
		    transMath.setRequestType("exp");
		if (className.endsWith("Log"))
		    transMath.setRequestType("log");
		if (className.endsWith("Sin"))
		    transMath.setRequestType("sin");
		if (className.endsWith("Cos"))
		    transMath.setRequestType("cos");
		if (className.endsWith("Tan"))
		    transMath.setRequestType("tan");
		if (className.endsWith("Asin"))
		    transMath.setRequestType("asin");
		if (className.endsWith("Acos"))
		    transMath.setRequestType("acos");
		if (className.endsWith("Atan"))
		    transMath.setRequestType("atan");

		return (XDMIterator) transMath;
	    }

	    else {
		XDMIterator function = (XDMIterator) (Class.forName(className)
			.newInstance());
		return function;
	    }
	} catch (ClassNotFoundException e) {
	    throw new StaticException(ErrorCodes.E0017_STATIC_DOESNT_MATCH_FUNCTION_SIGNATURE,"Function "+className+" not available",QueryLocation.OUTSIDE_QUERY_LOC);
	} catch (InstantiationException e) {
	    throw new StaticException(ErrorCodes.E0017_STATIC_DOESNT_MATCH_FUNCTION_SIGNATURE,"Function "+className+" not available",QueryLocation.OUTSIDE_QUERY_LOC);
	} catch (IllegalAccessException e) {
	    throw new StaticException(ErrorCodes.E0017_STATIC_DOESNT_MATCH_FUNCTION_SIGNATURE,"Function "+className+" not available",QueryLocation.OUTSIDE_QUERY_LOC);
	}
    }

    /**
     * Create a copy of this function
     * 
     * @param context
     * @param nestedPredCtxStack
     * @return a deep copy of this function object
     * @throws MXQueryException
     */
    public Function copy(Context context, Vector nestedPredCtxStack)
	    throws MXQueryException {
	return new Function(className, signature.copy(), iter.copy(context,
		null, false, nestedPredCtxStack));
    }
}
