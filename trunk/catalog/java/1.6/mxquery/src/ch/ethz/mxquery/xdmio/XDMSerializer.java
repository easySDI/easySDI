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

package ch.ethz.mxquery.xdmio;

import java.io.ByteArrayOutputStream;
import java.io.PrintStream;
import java.io.UnsupportedEncodingException;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.CFException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.PlatformDependentUtils;
import ch.ethz.mxquery.util.Set;
import ch.ethz.mxquery.util.Stack;
import ch.ethz.mxquery.util.Utils;

/**
 * Serializer to turn XDM into a textual form
 * Follows the XDM Serializer specification
 * @author Peter Fischer
 *
 */

public class XDMSerializer {
	
	protected boolean startTagStarted = false;
	private static TypeDictionary dictionary = Context.getDictionary();
	private XDMSerializerSettings settings;
	//FIXME: END_DOCUMENT  missing

	//private static boolean endTagFound	= false;
	
	private final static String xhtml_empty_elems [] = {"area",
		"base","br","col","hr","img","input", "link", "meta", 
		"basefont", "frame", "isindex", "param"};
	
	private static Set xhtml_empty_set = new Set(); 
	static {
		for (int i=0;i<xhtml_empty_elems.length;i++) {
			xhtml_empty_set.add(xhtml_empty_elems[i]);
		}
	}
	
	/**
	 * Creates an XDM Serializer with XML output mode and the default settings
	 */
	public XDMSerializer() {
		this.settings = new XDMSerializerSettings();
	}

	/**
	 * Creates an XDM Serializer with the given output mode and the default settings for this mode
	 * @param outputMode One of XDMSerializerSettings.OUTPUT_METHOD_XML, XDMSerializerSettings.OUTPUT_METHOD_XHTML, XDMSerializerSettings.OUTPUT_METHOD_HTML, XDMSerializerSettings.OUTPUT_METHOD_TEXT
	 * @throws MXQueryException thrown if unsupported mode requested
	 */
	public XDMSerializer(int outputMode) throws MXQueryException{
		this.settings = new XDMSerializerSettings(outputMode);
	}

	/**
	 * Creates an XDM Serializer with the given settings	
	 * @param settings
	 */
	public XDMSerializer(XDMSerializerSettings settings) {
		this.settings = settings;
	}
	
	private void eventToXML(PrintStream stream, Token tok, int prevEventType) throws MXQueryException {
		int type = tok.getEventType();
		if (startTagStarted) {
			if (tok.isAttribute()){
				stream.print(" " + tok.getName() + "=\"" + getSerializedValue(tok) + "\"");
				stream.flush();
				return;
			} else {
				if (tok.getEventType() != Type.END_TAG)
					stream.print(">");
				startTagStarted = false;
			}
		}	
		
		switch (type) {
        case Type.START_TAG :
        	stream.print("<" + tok.getName()); 
        	startTagStarted = true;
            break;

        case Type.END_TAG :
        	if (prevEventType == Type.START_TAG || Type.isAttribute(prevEventType))
        		if (settings.getOutputMethod() != XDMSerializerSettings.OUTPUT_METHOD_XHMTL) 
        			stream.print("/>");
        		else if (xhtml_empty_set.contains(tok.getName()))
        			stream.print(" />");
        		else 
        			stream.print("></" + tok.getName() + ">");
        	else	
        		stream.print("</" + tok.getName() + ">");
        	break;

          case Type.PROCESSING_INSTRUCTION :
        	  stream.print("<?" + tok.getValueAsString() + "?>") ;
          break;            
            
          case Type.COMMENT :
        	  stream.print("<!--" + tok.getText() + "-->");
          break;        
            
        default :
            break;
		}
		
		if ( Type.isTextNode(type) )  {
			type = Type.getTextNodeValueType(type);
		}		
		
			if ( Type.isAtomicType(type, dictionary) || type == Type.UNTYPED) {
				String serValue = getSerializedValue(tok);
				stream.print(serValue);
			}
	
		if (tok.isAttribute()&& !startTagStarted){
			if (settings.isSerializeStandaloneAttributes())
				stream.print(" " + tok.getName() + "=\"" + getSerializedValue(tok) + "\"");
			else 
				throw new DynamicException(ErrorCodes.S0001_ATTRIBUTE_OR_NAMESPACE_NOT_ALLOWED_HERE, "No Attributes with an enclosing element allowed",null);
		}
		stream.flush();
		
	}
	/**
	 * Serialize the XDM instance generated by resIter into stream as character sequence
	 * @param stream PrintStream 
	 * @param resIter MXQuery Iterator producing an XDM instance
	 * @throws MXQueryException Exception generated by retrieving the XDM from resIter or during the serialization
	 */
	public void eventsToXML(PrintStream stream, XDMIterator resIter) throws MXQueryException {
		XDMScope curNsScope = new XDMScope(); // current scope coming from Tokens
		XDMScope declaredScope = new XDMScope(); // scope stack representing the actual declarations performed
		int depth = 0;
		Stack scopeDepth = new Stack();
		Vector seenScopes = new Vector();
		seenScopes.addElement(curNsScope);
		int prevEventType = Type.START_SEQUENCE;
		startTagStarted = false;
		
		XDMIterator iter = resIter;
		Token tok = null;
		try {
			tok = iter.next();
		} catch (CFException cfe) {
			if (cfe.isEarlyReturn()) {
				iter = cfe.getReturnValue();
				tok = iter.next();
			} else {
				throw cfe;
			}
		}
		// print XML declaration with version and encoding
		if ((settings.getOutputMethod() == XDMSerializerSettings.OUTPUT_METHOD_XHMTL || 
				settings.getOutputMethod() == XDMSerializerSettings.OUTPUT_METHOD_XML) && 
				!settings.isOmitXMLDeclaration()) {
			
			stream.print("<?xml version='");
			stream.print(settings.getVersion());
			stream.print("' encoding='");
			stream.print(settings.getEncoding());
			if (settings.getStandalone() != XDMSerializerSettings.STANDALONE_OMIT) {
				stream.print("' standalone=');");
				if (settings.getStandalone() == XDMSerializerSettings.STANDALONE_YES)
					stream.print("yes");
				else
					stream.print("no");
			}
			stream.print("' ?>\n");
		}
		
		// print docdecl, if given in the serializer options
		
		if (settings.getDoctypeSystem() != null) {
			stream.print("<DOCDECL ");
			stream.print(settings.getDoctypeRootElem());
			if (settings.getDoctypePublic() != null) {
				stream.print(" PUBLIC ");
				stream.print(settings.getDoctypePublic());
				stream.print(" ");
				stream.print(settings.getDoctypeSystem());
			} else {
				stream.print(" SYSTEM ");
				stream.print(settings.getDoctypeSystem());
			}
			stream.print(">");
		}
		
		while(tok.getEventType() != Type.END_SEQUENCE){

			if (depth == 0) {
				if (Type.isAtomicType(tok.getEventType(),dictionary) && (Type.isAtomicType(prevEventType, dictionary)))
					stream.print(' ');
			}
			eventToXML(stream, tok, prevEventType);
			if (Type.isStartType(tok.getEventType())){
				depth++;
			} else if (Type.isEndType(tok.getEventType())){
				if (tok instanceof NamedToken && depth == scopeDepth.peek()) {
					//curNsScope = curNsScope.getParent();
					seenScopes.removeElementAt(seenScopes.size()-1);
					curNsScope = (XDMScope)seenScopes.lastElement();
					declaredScope = declaredScope.getParent();
					scopeDepth.pop();
				}
				depth--;
			}
			if (tok instanceof NamedToken && (Type.isStartType(tok.getEventType()) || Type.isAttribute(tok.getEventType()))) {
				XDMScope elemScope = tok.getDynamicScope();
				// if scope changes (e.g. new nesting), print all definitions
				if (elemScope != curNsScope) {
					scopeDepth.push(depth);
					seenScopes.addElement(elemScope);
					curNsScope = elemScope;
					declaredScope = new XDMScope(declaredScope);
					Hashtable inScopeDefs = curNsScope.getAllNamespaces();
					Enumeration en = inScopeDefs.elements();
					while (en.hasMoreElements()) {
						Namespace ns = (Namespace)en.nextElement();
						// Don't serialize XML namespace and undeclared namespaces
						if (ns.getURI().equals(XQStaticContext.URI_XML) || (ns.getURI().equals("")&&!ns.getNamespacePrefix().equals("")))
								continue;
						
						// check if not present in declared => add to declared scope and print
						
						Namespace nm = declaredScope.getNamespace(ns.getNamespacePrefix());
						if (nm == null || !nm.getURI().equals(ns.getURI())) {
							declaredScope.addNamespace(ns);
							if (ns.getNamespacePrefix().equals("")) {
								stream.print(" xmlns=\"" + ns.getURI() + "\"");	
							} else {
								stream.print(" xmlns:" + ns.getNamespacePrefix() + "=\"" + ns.getURI() + "\"");	
							}
						}						
					}
				}
			}

			stream.flush();
			prevEventType = tok.getEventType();
			tok = iter.next();
		}
		stream.flush();
	}
	/**
	 * Serialize the XDM instance generated by resIter into a String
	 * @param resIter MXQuery Iterator producing an XDM instance
	 * @throws MXQueryException Exception generated by retrieving the XDM from resIter or during the serialization	 */
	public String eventsToXML(XDMIterator resIter) throws MXQueryException {
		ByteArrayOutputStream bout = new ByteArrayOutputStream(); 
		PrintStream stream;
		try {
			stream = new PrintStream(bout,false,"UTF-8");
			eventsToXML(stream, resIter);
			return bout.toString("UTF-8");
		} catch (UnsupportedEncodingException ue) {
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Cannot serialize",null);
		}
	}
	/**
	 * Serialize the XDM instance generated by resIter into a character stream suitable for SOAP
	 * @param stream PrintStream 
	 * @param resIter MXQuery Iterator producing an XDM instance
	 * @throws MXQueryException Exception generated by retrieving the XDM from resIter or during the serialization
	 */
	public void eventsToSOAPMsg(PrintStream stream, XDMIterator resIter) throws MXQueryException {
		XDMScope curNsScope = new XDMScope(); // current scope coming from Tokens
		XDMScope declaredScope = new XDMScope(); // scope stack representing the actual declarations performed
		int depth = 0;
		Stack scopeDepth = new Stack();
		Vector seenScopes = new Vector();
		seenScopes.addElement(curNsScope);
		int prevEventType = Type.START_SEQUENCE;
		startTagStarted = false;
		boolean sequencedResult = false;
		boolean ofAtomicTypes = false;
		TypeInfo retType = resIter.getStaticType();
		ofAtomicTypes = Type.isAtomicType(retType.getType(), Context.getDictionary());		
		if (retType.getOccurID() != Type.OCCURRENCE_IND_EXACTLY_ONE && retType.getOccurID() != -1){
			if (!ofAtomicTypes){ //sequence of atomic types is represented as a xsd:list
				sequencedResult = true;
			}
		}
		
		XDMIterator iter = resIter;
		
		Token tok = null;
		try {
			tok = iter.next();
		} catch (CFException cfe) {
			if (cfe.isEarlyReturn()) {
				iter = cfe.getReturnValue();
				tok = iter.next();
			} else {
				throw cfe;
			}
		}
		int listIndex = 0;
		while(tok.getEventType() != Type.END_SEQUENCE){
			if (ofAtomicTypes || listIndex != 0){
				stream.print(' ');
			}
			if (depth == 0 && sequencedResult) {
					stream.print("<baseType>");
				}
			eventToXML(stream,tok, prevEventType);
			if (depth == 1 && sequencedResult && tok.getEventType() == Type.END_TAG) {
				stream.print("</baseType>");
			}
			if (depth == 0 && sequencedResult && tok.getEventType() != Type.START_TAG) {
				stream.print("</baseType>");
			}			
			if (Type.isStartType(tok.getEventType())){
				depth++;
			} else if (Type.isEndType(tok.getEventType())){
				if (tok instanceof NamedToken && depth == scopeDepth.peek()) {
					//curNsScope = curNsScope.getParent();
					seenScopes.removeElementAt(seenScopes.size()-1);
					curNsScope = (XDMScope)seenScopes.lastElement();
					declaredScope = declaredScope.getParent();
					scopeDepth.pop();
				}
				depth--;
			}
			listIndex++;
			if (tok instanceof NamedToken && (Type.isStartType(tok.getEventType()) || Type.isAttribute(tok.getEventType()))) {
				XDMScope elemScope = tok.getDynamicScope();
				// if scope changes (e.g. new nesting), print all definitions
				if (elemScope != curNsScope) {
					
					scopeDepth.push(depth);
					seenScopes.addElement(elemScope);
					curNsScope = elemScope;
					declaredScope = new XDMScope(declaredScope);
					Hashtable inScopeDefs = curNsScope.getAllNamespaces();
					Enumeration en = inScopeDefs.elements();
					while (en.hasMoreElements()) {
						Namespace ns = (Namespace)en.nextElement();
						// Don't serialize XML namespace and undeclared namespaces
						if (ns.getURI().equals(XQStaticContext.URI_XML) || (ns.getURI().equals("")&&!ns.getNamespacePrefix().equals("")))
								continue;
						
						// check if not present in declared => add to declared scope and print
						
						Namespace nm = declaredScope.getNamespace(ns.getNamespacePrefix());
						if (nm == null || !nm.getURI().equals(ns.getURI())) {
							declaredScope.addNamespace(ns);
							if (ns.getNamespacePrefix().equals("")) {
								stream.print(" xmlns=\"" + ns.getURI() + "\"");	
							} else {
								stream.print(" xmlns:" + ns.getNamespacePrefix() + "=\"" + ns.getURI() + "\"");	
							}
						}						
					}					
				}
			}
			
			prevEventType = tok.getEventType();
			tok = iter.next();
		}
	}

	/**
	 * Serialize the XDM instance generated by resIter into a String containing the Token trace
	 * This is mainly suited for debugging
	 * @param iter MXQuery Iterator producing an XDM instance
	 * @throws MXQueryException Exception generated by retrieving the XDM from resIter or during the serialization
	 * @return a StringBuffer contained the event trace	 
	 */
	public static StringBuffer eventsToString(XDMIterator iter) throws MXQueryException {
		StringBuffer myBuffer = new StringBuffer();
		int i=0;
		//int type;
		Token tok;
		do{
			tok = iter.next();
			myBuffer.append(i);
			myBuffer.append(": ");
			myBuffer.append(eventToString(tok));
			myBuffer.append("\n");
			//System.out.println(myBuffer.toString());
			i++;
		}while(tok.getEventType() != Type.END_SEQUENCE);
		return myBuffer;
	}
	/**
	 * Serialize the XDM Token into a String containing the Token details
	 * This is mainly suited for debugging
	 * @param tok XDM Token
	 * @throws MXQueryException Exception generated by retrieving the XDM from resIter or during the serialization
	 * @return a StringBuffer containing the trace information for this particular event/token	 
	 */
	
	public static StringBuffer eventToString(Token tok)  throws MXQueryException {
		StringBuffer myBuffer = new StringBuffer(); 
		int type = tok.getEventType();
		
		boolean isAttribute = false; 
		if ( Type.isAttribute(type) )  {
			isAttribute = true;
			type = Type.getAttributeValueType(type);
		}		
		
		myBuffer.append("[");
		myBuffer.append(type);
		myBuffer.append("\t");
		switch (type) {
		case Type.START_DOCUMENT:
			myBuffer.append("START_DOCUMENT");
			break;
		
		case Type.START_SEQUENCE:
			myBuffer.append("START_SEQUENCE");
			break;
			
		case Type.END_DOCUMENT:
			myBuffer.append("END_DOCUMENT");
			break;	
		
		case Type.END_SEQUENCE:
			myBuffer.append("END_SEQUENCE");
			break;
			
		case Type.PROCESSING_INSTRUCTION:
			myBuffer.append("PROCESSING_INSTRUCTION " + tok.getName()+ " " + tok.getText());
			break;
		
		case Type.COMMENT:
			myBuffer.append("COMMENT " + tok.getText());			
			break;
					
        case Type.START_TAG :
        	myBuffer.append("START_TAG ");
        	myBuffer.append(tok.getName());
            break;

        case Type.END_TAG :
        	myBuffer.append("END_TAG ");
        	myBuffer.append(tok.getName());
            break;

        default :
            break;
		}
		
		if ( Type.isTextNode(type) )  {
			type = Type.getTextNodeValueType(type);
		}
		
		if ( Type.isAtomicType(type, dictionary) ) {
			
        	if (!isAttribute) {        	
	        	myBuffer.append(Type.getTypeQName(type, Context.getDictionary()) + " ");
	        	myBuffer.append(getSerializedValue(tok) );
        	} else {
            	myBuffer.append("ATTRIBUTE " + Type.getTypeQName(type, Context.getDictionary()) + " ");
            	myBuffer.append(tok.getName());
            	myBuffer.append("'");
            	myBuffer.append(getSerializedValue(tok));
            	myBuffer.append("'");
        	}	
		}		
		
		myBuffer.append("]");
		return myBuffer;
	}	
	/**
	 * Indent/beautify an String containing XML
	 * @param str A wring with XML to be intented
	 * @return A string with indented XML
 	 */
	public static String XMLPrettyPrint(String str) {
		boolean started = true;
		int depth = 0;
		StringBuffer xml = new StringBuffer();
		for (int i = 0; i < str.length(); i++){
			if (str.charAt(i) == '<') {
				if (!started) {
					xml.append("\n");
				} else {
					started = false;
				}
				if (str.charAt(i+1) == '/'){
					depth--;
				}
				for (int j = 0; j < depth; j++) {
					xml.append("  ");
				}
				if (str.charAt(i+1) != '/'){
					depth++;
				}
			}
			xml.append(str.charAt(i));
			if (str.charAt(i) == '>') {
				if (i+1 < str.length() && str.charAt(i+1) != '<') {
					xml.append("\n");
					for (int j = 0; j < depth; j++) {
						xml.append("  ");
					}
				} else  if (str.charAt(i - 1) == '/') {
					depth--;
				}
			}
		}
		return xml.toString();
	}
	private static String getSerializedValue(Token tok) throws MXQueryException {

		int t = tok.getEventType();
		boolean isAttribute = Type.isAttribute(t);
		if ( isAttribute )
			t = Type.getAttributeValueType(t);
		else
		if ( Type.isTextNode(t) )
			t = Type.getTextNodeValueType(t);
		
		if ( t == Type.STRING || t == Type.UNTYPED_ATOMIC || t == Type.ANY_URI || t == Type.UNTYPED) {
			if (isAttribute) 
				return changeEntityRef( tok.getText(), true );
			else
				return changeEntityRef( tok.getText(), false );
			
		}
		else return tok.getValueAsString();
			
	}
	private static String changeEntityRef(String val, boolean attribute){

//		System.out.println("val: " + val);
		
		val = Utils.replaceAll(val, "&", "&amp;");
		val = Utils.replaceAll(val, ">", "&gt;");
		val = Utils.replaceAll(val, "<", "&lt;");
		//val = Utils.replaceAll(val, "'", "&apos;");
		if (attribute)
			val = Utils.replaceAll(val, "\"", "&quot;");
		
		StringBuffer translated = PlatformDependentUtils.expandCharRef(val, attribute);
		return translated.toString();		
	}
}
