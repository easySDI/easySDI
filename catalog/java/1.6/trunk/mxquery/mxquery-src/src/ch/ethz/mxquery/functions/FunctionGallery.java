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

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import org.kxml2.io.KXmlParser;
import org.xmlpull.v1.XmlPullParser;
import org.xmlpull.v1.XmlPullParserException;

import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;

public class FunctionGallery {
	private Hashtable functions;
	private XQStaticContext ctx;

	private FunctionGallery(XQStaticContext ctx, Hashtable functions) {
		this.ctx = ctx;
		this.functions = functions;
	}
	
	public FunctionGallery(XQStaticContext ctx) {
		functions = new Hashtable();
		this.ctx = ctx;
	}

	public void add(Function function, boolean checkExistence, boolean external) throws MXQueryException {
		FunctionSignature signature = function.getFunctionSignature();
		String prefix = signature.qname.getNamespacePrefix();
		String nsURL;
		QueryLocation loc = QueryLocation.OUTSIDE_QUERY_LOC;
		if (prefix == null || prefix.equals("")) {
			nsURL = ctx.getDefaultFunctionNamespace();
		} else {
			Namespace ns = ctx.getNamespace(signature.qname.getNamespacePrefix());
			if (ns == null) {
				throw new StaticException(ErrorCodes.E0060_STATIC_FUNCTION_NAME_IS_NOT_A_NAMESPACE, "Function prefix is in unallocated namespace",loc);
			}
			nsURL = ns.getURI();
		};
		if(signature.type != FunctionSignature.SYSTEM_FUNCTION && 
				(nsURL.equals("http://www.w3.org/XML/1998/namespace") || 
						nsURL.equals("http://www.w3.org/2001/XMLSchema") || 
						nsURL.equals("http://www.w3.org/2001/XMLSchema-instance") || 
						nsURL.equals("http://www.w3.org/2005/xpath-functions")
				)){
			throw new StaticException(ErrorCodes.E0045_STATIC_FUNCTION_NAME_IN_UNALLOWED_NAMESPACE, "Function declaration having \'" + nsURL + "\' namespace not allowed", loc);
		}
		if(signature.type != FunctionSignature.EXTERNAL_FUNCTION && function.className ==null && function.iter==null){
			throw new IllegalArgumentException("It is not allowed to register a not external function without an implementation");
		}
		
		if (checkExistence && functions.containsKey(signature) ) {
				throw new StaticException(ErrorCodes.E0034_STATIC_MODULE_DUPLICATE_FUNCTION, 
					"Function " + signature.qname.toString() + " with arity " + signature.getArity() + " already defined!", loc);
		}
		functions.put(signature, function);
	}
	
	public void add(Function function) throws MXQueryException{
		add(function, true, false);
	}
	
	public Hashtable get(String namespace){ //the parameter is not used!
		Hashtable ht = new Hashtable();
		Enumeration keys = this.functions.keys();
		while(keys.hasMoreElements()) {
			FunctionSignature fs = (FunctionSignature)keys.nextElement();
			if (fs.type == FunctionSignature.USER_DEFINED_FUNCTION){
				//FunctionSignature fs1 = new FunctionSignature(fs.getName(), fs.getArity(),FunctionSignature.LOCAL_SCOPE_FUNCTION);
				ht.put(fs, this.functions.get(fs));
			}
		}
		return ht;
	}
	
	public Hashtable getFunctionOfNS(String namespace){
		Hashtable ht = new Hashtable();
		Enumeration keys = this.functions.keys();
		while(keys.hasMoreElements()) {
			FunctionSignature fs = (FunctionSignature)keys.nextElement();
			if (fs.qname.getNamespaceURI().equals(namespace)){
				//FunctionSignature fs1 = new FunctionSignature(fs.getName(), fs.getArity(),FunctionSignature.LOCAL_SCOPE_FUNCTION);
				ht.put(fs, this.functions.get(fs));
			}
		}
		return ht;
	}
	
	
	public Function get(QName name, int arity) throws MXQueryException{
		// fix for fn:contains - any arity > 2 possible
		// TODO: refine to also include in the prefix/namespace
		if (name.getLocalPart().equals("concat") && arity > 2)
			arity = 2;
		// TODO: More elegant way. For now, create dummy signature with empty types
		TypeInfo [] typePlaceholder = new TypeInfo[arity];
		for (int i=0;i<typePlaceholder.length;i++) {
			typePlaceholder[i] = new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
		}		
		FunctionSignature signature = new FunctionSignature(name,typePlaceholder);
		Function function = (Function)functions.get(signature);
		if(function != null){
			return function;
		}else{
			return null;
		}
	}

	/**
	 * Creates a standard function library. 
	 * @param ctx The context is needed to resolve the namespaces.
	 * @return A new function gallery
	 * @throws MXQueryException
	 */
	public static FunctionGallery createStdFunctionGallery(XQStaticContext ctx) throws MXQueryException {
		FunctionGallery fg = new FunctionGallery(ctx);
		try {
			// FG_complete.xml
			InputStream is = fg.getClass().getResourceAsStream("FG.xml");
			populateFunctionGallery(ctx, fg, is, "ch.ethz.mxquery.functions.");
			is.close();
			return fg;
		} catch (IOException e) {
			throw new MXQueryException(ErrorCodes.A0007_EC_IO, e, "Function Gallery could not be loaded, IO Error",null);
		} catch (XmlPullParserException e) {
			throw new MXQueryException(ErrorCodes.A0007_EC_IO, e, "Function Gallery could not be loaded, Parse Error",null);
		}
	}

	public static void populateFunctionGallery(XQStaticContext ctx,
			FunctionGallery fg, InputStream is, String location) throws XmlPullParserException,
			IOException, MXQueryException {
		InputStreamReader br = new InputStreamReader(is);
		KXmlParser xpp = new KXmlParser();
		xpp.setInput(br);

		xpp.nextTag();
		xpp.require(XmlPullParser.START_TAG, null, "functionGallery");

		xpp.nextTag();
		while (xpp.getName() != "functionGallery" && xpp.getEventType() != XmlPullParser.END_TAG) {

			xpp.require(XmlPullParser.START_TAG, null, "functionDescription");
			xpp.nextTag();

			//Function prefix, name, and class
			String[] funcDesc = new String[3];
			//TODO Using other data structures?
			String[] parameters = new String[10];
			int currArity = 0;

			while (xpp.getName() != "functionDescription" && xpp.getEventType() != XmlPullParser.END_TAG) {

				String elementName = xpp.getName();
				if (elementName.equals("functionPrefix")) {
					String st = xpp.nextText();
					Namespace ns = ctx.getNamespace(st);
					funcDesc[0] = ns.getURI();
				} else if (elementName.equals("functionName")) {
					funcDesc[1] = xpp.nextText();
				} else if (elementName.equals("parameters")) {
					xpp.nextTag();
					int i = 0;
					while (xpp.getName() != "parameters" && xpp.getEventType() != XmlPullParser.END_TAG){
						xpp.require(XmlPullParser.START_TAG, null, "paramType");
						elementName = xpp.getName();
						if (elementName.equals("paramType")){
							parameters[i++] = xpp.nextText();
						}
						xpp.nextTag();
					}
					currArity = i;
				} else if (elementName.equals("className")) {
					funcDesc[2] = xpp.nextText();
				} else {
					// element ignored
					System.out.println("element ignored!");
				}

				xpp.nextTag();
			}

			for (int i = 0; i < funcDesc.length; i++) {
				if (funcDesc[i] == null) {
					throw new IllegalArgumentException("The function description is not complete:" + funcDesc[0]
							+ ":" + funcDesc[1] + ":" + funcDesc[2]);
				}
			}
			String prefix = ctx.getPrefix(funcDesc[0]);
			QName qn = new QName(prefix, funcDesc[1]);
			qn = qn.resolveQNameNamespace(ctx);
			
			TypeInfo [] paramTypes = new TypeInfo[currArity];
			//TODO: What about the prefix and URI?
			for (int i=0;i<paramTypes.length;i++)
				paramTypes[i] = new TypeInfo(getType(parameters[i]),getOccur(parameters[i]),null,null);
			
			FunctionSignature signature = new FunctionSignature(qn, paramTypes, FunctionSignature.SYSTEM_FUNCTION, XDMIterator.EXPR_CATEGORY_SIMPLE, false);
			Function function = new Function(location+prefix+"."+funcDesc[2],signature, null );
			fg.add(function);

			xpp.nextTag();
		}
	}
	private static int getType(String rawType) throws MXQueryException{
		//Removing the occurrence indicators
		if (rawType.indexOf('*') > -1){
			rawType = rawType.substring(0,rawType.indexOf("*")).trim();
		}
		if (rawType.indexOf('?') > -1){
			rawType = rawType.substring(0,rawType.indexOf("?")).trim();
		}
		if (rawType.indexOf('+') > -1){
			rawType = rawType.substring(0,rawType.indexOf("+")).trim();
		}
		if (rawType.equals("item()")){
			return Type.ITEM;
		}else if (rawType.equals("node()")){
			return Type.NODE;
		}else if (rawType.equals("element()")){
				return Type.ITEM;//TODO: fix it!
		}else{
			QName qname = new QName(rawType);
			if (qname.getNamespacePrefix()==null){
				if (qname.getLocalPart().equals("numeric")){
					qname = new QName(Type.NAMESPACE_MXQ,"number");
				}else{
					qname = new QName(Type.NAMESPACE_XS,qname.getLocalPart());
				}
			}
			return Type.getTypeFootprint(qname, Context.getDictionary());
		}
	}

	
	private static int getOccur(String rawType){
		if (rawType.indexOf("*") > -1){
			return Type.getOccurID('*');
		}else if (rawType.indexOf("+")>-1){
			return Type.getOccurID('+');
		}else if (rawType.indexOf("?")>-1){
			return Type.getOccurID('?');
		}else {
			return Type.getOccurID(' ');
		}
	}


	public FunctionGallery copy(Context context) throws MXQueryException {
		Hashtable functionscopy = new Hashtable();
		
		Enumeration keys = functions.keys();
		FunctionSignature fs;
		
		while(keys.hasMoreElements()) {
			fs = (FunctionSignature) (keys.nextElement());
			functionscopy.put(fs, ((Function)functions.get(fs)).copy(context, new Vector()));
		}
		
		return new FunctionGallery(context, functionscopy);
	}
}
