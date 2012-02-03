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
package ch.ethz.mxquery.query.parser;


import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.Vector;

import org.kxml2.io.KXmlParser;
import org.xmlpull.v1.XmlPullParser;
import org.xmlpull.v1.XmlPullParserException;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.exceptions.*;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.XDMIterator;

public class PlanLoader {
	
	static int depth = -1;
	Context runtime = new Context();
	
	public XDMIterator processPlan(InputStream is) throws MXQueryException {		
        KXmlParser xpp = new KXmlParser();
        XDMIterator it = null;
        
        try {
        
        	//InputStream is = getClass().getResourceAsStream(filename);
			InputStreamReader br = new InputStreamReader(is);
			
			xpp.setInput(br);
			int eventType = xpp.getEventType();
			if(eventType == XmlPullParser.START_DOCUMENT) {
				xpp.nextTag();
				if(xpp.getName().equals("StaticContext")) {
					xpp.nextTag();
					runtime = establishStaticContext(xpp);
				}
				xpp.nextTag();
				if(xpp.getName().equals("ParseTree")) {
					xpp.nextTag();
					it = processParseTree(xpp);
			   		Context context = new Context(runtime);
			   		it.setContext(context, true);					
				}
			}
        
    	} catch (IOException e) {
    		throw new MXQueryException(ErrorCodes.A0007_EC_IO,"I/O Error opening the plan: "+e.toString(),null);

    	} catch (XmlPullParserException e) {
    		throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Parse Error opening the plan: "+e.toString(),null);
    	}
    	
		return it;
        
	}
	
	Context establishStaticContext(XmlPullParser xpp) throws XmlPullParserException, IOException, MXQueryException {
		Context runtime = new Context();
		
		if(xpp.getName().equals("Variables")) {
			xpp.nextTag();
			while(xpp.getName().equals("Variable")) {
				xpp.nextTag();
				xpp.next();
				QName v = new QName(xpp.getText());
				runtime.registerVariable(v, false);
				//runtime.addVariable(var, it);
				xpp.nextTag();
				xpp.nextTag();
				xpp.nextTag();
			}	
		}

		xpp.nextTag();
//		if(xpp.getName().equals("UsedVariables")) {
//			xpp.nextTag();
//			while(xpp.getName().equals("Variable")) {
//				xpp.nextTag();
//				xpp.next();
//				QName uv = new QName(xpp.getText());
//				runtime.registerVariableReference(uv);
//				xpp.nextTag();
//				xpp.nextTag();
//				xpp.nextTag();
//			}
//		}
		xpp.nextTag();
		return runtime;
		
	}
	
    Iterator processParseTree(XmlPullParser xpp) throws XmlPullParserException, IOException, MXQueryException  {
    	
    	Iterator it = null;
    	Iterator retExpr = null;
    	Iterator whereExpr = null;
    	XDMIterator ordExpr = null;
    	Vector subIters = new Vector();
    	Vector attrName = new Vector();
    	Vector attrValue = new Vector();
    	String name = xpp.getName();
    	int returnExprSet = 0;
    	int whereExprSet = 0;
    	int orderByExprSet = 0;

    	while(xpp.getEventType() != XmlPullParser.END_DOCUMENT) {
	    	
	    	if(xpp.getEventType() == XmlPullParser.START_TAG ) {
	   			name = xpp.getName();
	   			depth++;
	   			
	   			if (name.equals("returnExpr")) {
	   				retExpr = handleSpecialExpression(xpp);
	   				returnExprSet = 1;
	   			}
	   			
	   			else if (name.equals("orderByExpr")) {
	   				ordExpr = handleSpecialExpression(xpp);
	   				orderByExprSet = 1;
	   			}
	   			
	   			else if (name.equals("whereExpr")) {
	   				whereExpr = handleSpecialExpression(xpp);
	   				whereExprSet = 1;
	   			}

	   			else if(name.indexOf("Iterator")>=0) {
	   					   			
		   			int attCount = xpp.getAttributeCount();
		   			for (int i = 0; i < attCount; i++) {
		   				attrName.addElement(xpp.getAttributeName(i));
		   				attrValue.addElement(xpp.getAttributeValue(i));
		   			}

	   				it = getIterator(name);
		   			
	   				xpp.nextTag();
		   			while(xpp.getEventType() == XmlPullParser.START_TAG && !xpp.getName().equals("subIters")) {
		   				subIters.addElement(processParseTree(xpp));
		   			}		   			
	   			}
	   			
	   			else if (name.equals("subIters")) {
	   				subIters = handleSubItersExpression(xpp);
	   			}
		   			
	    	}
	    	
	    	if(xpp.getEventType() == XmlPullParser.END_TAG) {
		    	name = xpp.getName();
		   		xpp.next();
		   		while(xpp.getEventType() == XmlPullParser.TEXT) {
		   			xpp.next();
		   		}
		   		
		   		if(name.indexOf("Iterator")>=0) {
		   			it.setSubIters(subIters);
		   			if(returnExprSet == 1) {
		   				it.setReturnExpr(retExpr);
		   				returnExprSet = 0;
		   			}
		   			if(orderByExprSet == 1) {
		   				it.setOrderByExpr(ordExpr);
		   				orderByExprSet = 0;
		   			}
		   			if(whereExprSet == 1) {
		   				it.setWhereExpr(whereExpr);
		   				whereExprSet = 0;
		   			}
//		   			if(name.equals("FunctionCallIterator")){
//		   				it.setFunctionGallery(runtime.getFunctio);
//		   			}
		   		}

		   		
		   		/**	
		   		 * parameters are serialized in reverse order!
		   		 * with that trick we know the number of parameters without having to pass it!!
		   		 * e.g. see implementation of setParam in AdditiveIterator
		   		*/
		   		for(int i = attrName.size()-1; i >= 0 ; i--) {
		   			it.setParam((String)attrName.elementAt(i), (String)attrValue.elementAt(i));
		   		}
		   		return it;
	    	}
    
    	}
    	
    	it.setSubIters(subIters);
    	return it;    	
    	
    }
    
    Iterator handleSpecialExpression(XmlPullParser xpp) throws XmlPullParserException, IOException, MXQueryException  {
    	Iterator it = null;
    	xpp.nextTag();
    	it = processParseTree(xpp);
      	xpp.nextTag();
    	return it;
    }
    
    Vector handleSubItersExpression(XmlPullParser xpp) throws XmlPullParserException, IOException, MXQueryException {
    	Vector subit = new Vector();
    	xpp.nextTag();
    	while(xpp.getEventType() == XmlPullParser.START_TAG) {
    		subit.addElement(processParseTree(xpp));
    	}
    	xpp.nextTag();
    	return subit;
    }
    		    	

    static Iterator getIterator(String name) throws MXQueryException {
    	
    	String[] path = {"ch.ethz.mxquery.model.iterators.",
    					 "ch.ethz.mxquery.model.iterators.forseq.",
    					 "ch.ethz.mxquery.model.iterators.lroad.",
    					 "ch.ethz.mxquery.model.iterators.update.",
    					 "ch.ethz.mxquery.model.iterators.xqueryp."};
    	
    	for(int i = 0; i < path.length; i++) {

	    	try {
		    	Iterator it = (Iterator)(Class.forName(path[i] + name).newInstance());
		    	return it;
	     	}
	    	
	    	 catch (ClassNotFoundException e) {
	 			// TODO Auto-generated catch block
	    		throw new RuntimeException(e.toString());
	 		} catch (InstantiationException e) {
	 			// TODO Auto-generated catch block
	 			throw new RuntimeException(e.toString());
	 		} catch (IllegalAccessException e) {
	 			// TODO Auto-generated catch block			
	 			throw new RuntimeException(e.toString());
	 		}
    	}

    	throw new DynamicException(ErrorCodes.E0017_STATIC_DOESNT_MATCH_FUNCTION_SIGNATURE, "Implementation of function '" + name + "' could not be found!",null);
    }
}
