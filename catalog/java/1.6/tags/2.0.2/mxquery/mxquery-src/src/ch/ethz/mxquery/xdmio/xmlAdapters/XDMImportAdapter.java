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

package ch.ethz.mxquery.xdmio.xmlAdapters;

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.IdentifierFactory;
import ch.ethz.mxquery.datamodel.MXQueryBigDecimal;
import ch.ethz.mxquery.datamodel.MXQueryBinary;
import ch.ethz.mxquery.datamodel.MXQueryDate;
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryDuration;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.MXQueryGregorian;
import ch.ethz.mxquery.datamodel.MXQueryTime;
import ch.ethz.mxquery.datamodel.MXQueryYearMonthDuration;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.datamodel.xdm.AnyURIAttrToken;
import ch.ethz.mxquery.datamodel.xdm.BinaryAttrToken;
import ch.ethz.mxquery.datamodel.xdm.DateAttrToken;
import ch.ethz.mxquery.datamodel.xdm.DateTimeAttrToken;
import ch.ethz.mxquery.datamodel.xdm.DayTimeDurAttrToken;
import ch.ethz.mxquery.datamodel.xdm.DecimalAttrToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleAttrToken;
import ch.ethz.mxquery.datamodel.xdm.DurationAttrToken;
import ch.ethz.mxquery.datamodel.xdm.FloatAttrToken;
import ch.ethz.mxquery.datamodel.xdm.GregorianAttrToken;
import ch.ethz.mxquery.datamodel.xdm.LongAttrToken;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.QNameAttrToken;
import ch.ethz.mxquery.datamodel.xdm.TextAttrToken;
import ch.ethz.mxquery.datamodel.xdm.TimeAttrToken;
import ch.ethz.mxquery.datamodel.xdm.UntypedAtomicAttrToken;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.datamodel.xdm.YearMonthDurAttrToken;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Stack;
import ch.ethz.mxquery.xdmio.XMLSource;

public abstract class XDMImportAdapter extends Iterator implements XMLSource {

	/**
	 * URI to denote the origin of this XML data This URI will be used to
	 * compute node identity/document identity
	 */
	protected String uri;
	protected short level = 0;
	protected Identifier last_id;
	private boolean generateNodeIds = true;
	protected String systemid = null;
	protected String publicid = null;
	protected String dtdRootElem = null;
	private int nodeIdCount = 0;
	Stack scopeDepth = new Stack();
	XDMScope curNsScope = new XDMScope();
	protected Vector idsVector;
	protected Vector idRefsVector;
	protected Vector idRefVector;
	
	public XDMImportAdapter(Context ctx, QueryLocation loc) {
		super(ctx,loc);
		this.idRefsVector = new Vector();
		this.idRefVector = new Vector();
		this.idsVector = new Vector();
		scopeDepth.push(-1);  //dummy scope
	}
	
	public String getSystemID() {
		return systemid;
	}

	public String getPublicID() {
		return publicid;
	}

	public String getRootElemDTD() {
		return dtdRootElem;
	}
	
	protected abstract XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException;

	public int compare(Source store) {
		if (store instanceof XDMImportAdapter && uri != null && store.getURI() != null) {
			return this.uri.compareTo(store.getURI());
		} else {
			return -2;
		}
	}

	public void setURI(String uriToSet) {
		uri = uriToSet;
	}

	public String getURI() {
		return uri;
	}
	protected Identifier createNextTokenId(int eventType, String name) {
		if (generateNodeIds) {
			// TODO: Do not assign end_element etc. a separate node id
			last_id = IdentifierFactory.createIdentifier(nodeIdCount++,this,last_id,level);
			return last_id;
		} else
			return null;
	}
	
	static String changeEntityRef(String val) {

		if (val.equals("amp"))
			return "&";
		if (val.equals("gt"))
			return ">";
		if (val.equals("lt"))
			return "<";
		if (val.equals("apos"))
			return "'";
		if (val.equals("quot"))
			return "\"";
		// FIXME: what to do with other entity references?
		// for now, pass them on
		return "&" + val + ";";
	}
	
	public Source copySource(Context ctx, Vector nestedPredCtxStack) throws MXQueryException {
		return (Source) copy(ctx, null, false, nestedPredCtxStack);
	}
	
	protected NamedToken createAttributeToken(int type, String value, QName qname, XDMScope scope) throws MXQueryException {
		// System.out.println(Type.getTypeQName(type));
		NamedToken attrToken;
		int attrType = Type.createAttributeType(type);
		if (type == Type.UNTYPED_ATOMIC || type == Type.ANY_SIMPLE_TYPE)
			attrToken = new UntypedAtomicAttrToken(createNextTokenId(attrType,null), value, qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.ANY_URI, Context.getDictionary()))
			attrToken = new AnyURIAttrToken(createNextTokenId(attrType,null), value, qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.BASE64_BINARY, Context.getDictionary()))
			attrToken = new BinaryAttrToken(createNextTokenId(attrType,null), new MXQueryBinary(value, type), qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.DATE, Context.getDictionary()))
			attrToken = new DateAttrToken(createNextTokenId(attrType,null), new MXQueryDate(value), qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.DATE_TIME, Context.getDictionary()))
			attrToken = new DateTimeAttrToken(createNextTokenId(attrType,null), new MXQueryDateTime(value), qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.DAY_TIME_DURATION, Context.getDictionary()))
			attrToken = new DayTimeDurAttrToken(createNextTokenId(attrType,null), new MXQueryDayTimeDuration(value), qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.YEAR_MONTH_DURATION, Context.getDictionary()))
			attrToken = new YearMonthDurAttrToken(createNextTokenId(attrType,null), new MXQueryYearMonthDuration(value),qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.INTEGER, Context.getDictionary())) {
			attrToken = new LongAttrToken(type, createNextTokenId(attrType,null), Long.parseLong(value), qname, scope);
		}
		else if (Type.isTypeOrSubTypeOf(type, Type.DECIMAL, Context.getDictionary()))
			attrToken = new DecimalAttrToken(createNextTokenId(attrType,null), new MXQueryBigDecimal(value), qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.DOUBLE, Context.getDictionary()))
			attrToken = new DoubleAttrToken(createNextTokenId(attrType,null), new MXQueryDouble(value), qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.DURATION, Context.getDictionary()))
			attrToken = new DurationAttrToken(createNextTokenId(attrType,null), new MXQueryDuration(value), qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.FLOAT, Context.getDictionary()))
			attrToken = new FloatAttrToken(createNextTokenId(attrType,null), new MXQueryFloat(value), qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.G_DAY, Context.getDictionary()) || Type.isTypeOrSubTypeOf(type, Type.G_MONTH, Context.getDictionary()) || Type.isTypeOrSubTypeOf(type, Type.G_MONTH_DAY, Context.getDictionary()) || Type.isTypeOrSubTypeOf(type, Type.G_YEAR, Context.getDictionary()) || Type.isTypeOrSubTypeOf(type, Type.G_YEAR_MONTH, Context.getDictionary()))
			attrToken = new GregorianAttrToken(createNextTokenId(attrType,null), new MXQueryGregorian(value, type), qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.QNAME, Context.getDictionary()))
			attrToken = new QNameAttrToken(createNextTokenId(attrType,null), new QName(value), qname, scope);
		else if (Type.isTypeOrSubTypeOf(type, Type.TIME, Context.getDictionary()))
			attrToken = new TimeAttrToken(createNextTokenId(attrType,null), new MXQueryTime(value), qname, scope);
		else
			attrToken = new TextAttrToken(type,createNextTokenId(attrType,null), value, qname, scope);
		int tokenType = attrToken.getEventType();
		attrToken.setEventType(tokenType | type);
		return attrToken;

	}
	void checkCloseNsScope() {
		if (level == scopeDepth.peek()) {
			curNsScope = curNsScope.getParent();
			scopeDepth.pop();
		}
	}

	
	protected boolean checkOpenNsScopeAddNs(boolean createdNSScope, QName qname,
			String attVal) throws MXQueryException {
		if (qname.getNamespacePrefix() == null || qname.getNamespacePrefix().equals("")) {
			if (qname.getLocalPart().equals(XQStaticContext.NS_XMLNS)) {
				createdNSScope = checkOpenXDMScope(createdNSScope);
				Namespace nm = new Namespace("", attVal);
				curNsScope.addNamespace(nm);
			}
		} else {
			if (qname.getNamespacePrefix().equals(XQStaticContext.NS_XMLNS)) {
				Namespace nm = new Namespace(qname.getLocalPart(), attVal);
				createdNSScope = checkOpenXDMScope(createdNSScope);
				curNsScope.addNamespace(nm);
			}
			if (qname.getLocalPart().equals("base")) {
				createdNSScope = checkOpenXDMScope(createdNSScope);
				if (TypeLexicalConstraints.isRelativeURI(attVal)) {
					String parentURI = curNsScope.getBaseURI();
					curNsScope.setBaseURI(parentURI+attVal);
				} else 
					curNsScope.setBaseURI(attVal);
			}
			if (qname.getLocalPart().equals("lang")) {
				createdNSScope = checkOpenXDMScope(createdNSScope);
				curNsScope.setLanguage(attVal);
			}
		}
		
		
		return createdNSScope;
	}
	
	/** Check if a new XDM scope needs to be opened and opens it if needed*/
	private boolean checkOpenXDMScope(boolean createdNSScope) {
		if (!createdNSScope) {
			scopeDepth.push(level);
			curNsScope = new XDMScope(curNsScope);
			createdNSScope = true;
		}
		return createdNSScope;
	}
/**
 * Checks whether an attribute is an ID attribute (based on attribute name or DTD information)
 * @param attrName
 * @param elementName
 * @return true if this is a candidate for an ID attribute, false otherwise
 */
	public boolean isXMLId(QName attrName,QName elementName){
		String attrPrefix = attrName.getNamespacePrefix();
		Namespace attrNs  = context.getNamespace(attrPrefix);
		String attr_ns_uri ="";
		if (attrNs != null)
		attr_ns_uri = attrNs.getURI();
	
		//check for xml:id attribute
		if (attr_ns_uri.equals(XQStaticContext.URI_XML) &&
				attrName.getLocalPart().equals("id")) return true;
		
		if (idsVector.contains(elementName.toString()+"#"+attrName.toString()))
			return true;
		else 
		return false;
	}
	
	/**
	 * Checks whether an attribute is an IDREF attribute (based on  DTD information)
	 * @param attrName
	 * @param elementName
	 * @return true if this is a candidate for an IDREF attribute, false otherwise
	 */
	public boolean isIDREF(QName attrName,QName elementName){
		if (idRefVector.contains(elementName.toString()+"#"+attrName.toString()))
			return true;
		else 
		return false;
	}
	
	/**
	 * Checks whether an attribute is an IDREFS attribute (based on  DTD information)
	 * @param attrName
	 * @param elementName
	 * @return true if this is a candidate for an IDREFS attribute, false otherwise
	 */
	public boolean isIDREFS(QName attrName,QName elementName){
		if (idRefsVector.contains(elementName.toString()+"#"+attrName.toString()))
			return true;
		else 
		return false;
	}
	public Window getIterator(Context ctx) throws MXQueryException {
		return WindowFactory.getNewWindow(context, this);
	}	
}
