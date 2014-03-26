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

package ch.ethz.mxquery.xqj;

import java.net.URI;

import javax.xml.namespace.QName;
import javax.xml.xquery.XQException;
import javax.xml.xquery.XQItemType;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;

public class MXQueryXQType implements XQItemType {

	TypeInfo ti;
	int bType = -1;
	QName q;
	boolean nillable;
	
	public MXQueryXQType(TypeInfo typeInfo) {
		ti = typeInfo;
	}
	
	public MXQueryXQType(TypeInfo typeInfo, int baseType) {
		ti = typeInfo;
		bType = baseType;
	}
	
	public MXQueryXQType(TypeInfo typeInfo, int baseType, QName qName, boolean nill) {
		ti = typeInfo;
		bType = baseType;
		q = qName;
		nillable = nill;
	}
	
	public MXQueryXQType(TypeInfo typeInfo, int baseType, boolean nill) {
		ti = typeInfo;
		bType = baseType;
		nillable = nill;
	}
	
	public MXQueryXQType(XQItemType item, int occurence) throws XQException {
		MXQueryXQType mItem = (MXQueryXQType) item;
		ti = mItem.ti;
		bType = mItem.bType;
		ti.setOccurID(occurence);
	}
	
	public int getBaseType() throws XQException {
		if(bType == -1){
			if (!(Type.isAtomicType(ti.getType(), null) || Type.isAttribute(ti.getType()) || Type.isUserDefinedType(ti.getType())|| ti.getType() == Type.START_TAG))
				throw new XQException("Getting the base type not possible");
			return ((Integer)MXQueryXQDataFactory.MXQuerytoXQJBT.get(new Integer(Type.getPrimitiveAtomicType(ti.getType())))).intValue();
		} else {
			if (!(Type.isAtomicType(bType, null) || Type.isAttribute(bType) || Type.isUserDefinedType(bType)|| bType == Type.START_TAG))
				throw new XQException("Getting the base type not possible");
			return ((Integer)MXQueryXQDataFactory.MXQuerytoXQJBT.get(new Integer(Type.getPrimitiveAtomicType(bType)))).intValue();
		}
	}

	public int getItemKind() {
		if (Type.isAtomicType(ti.getType(), null))
			return XQITEMKIND_ATOMIC;
		if (Type.isAttribute(ti.getType()))
			return XQITEMKIND_ATTRIBUTE;
		if (Type.isTypeOrSubTypeOf(ti.getType(), Type.COMMENT, null))
			return XQITEMKIND_COMMENT;
		if (ti.getType() ==  Type.START_DOCUMENT)
			return XQITEMKIND_DOCUMENT;
		if (ti.getType() ==  Type.START_TAG)
			return XQITEMKIND_ELEMENT;
		if (ti.getType() ==  Type.PROCESSING_INSTRUCTION)
			return XQITEMKIND_PI;
		if (Type.isTextNode(ti.getType()))
			return XQITEMKIND_TEXT;
		if (Type.isNode(ti.getType()))
			return XQITEMKIND_NODE;
		// No support for schema types yet, since MXQuery does not support schema
		return XQITEMKIND_ITEM;
	}

	public int getItemOccurrence() {
		int mxqOcc = ti.getOccurID();
		int res = -1;
		switch (mxqOcc) {
		case Type.OCCURRENCE_IND_EXACTLY_ONE:
			res = OCC_EXACTLY_ONE;
			break;
		case Type.OCCURRENCE_IND_ZERO_OR_ONE:
			res = OCC_ZERO_OR_ONE;
			break;
		case Type.OCCURRENCE_IND_INFINITIVE:
			res = OCC_ZERO_OR_MORE;
			break;
		default:
			res = mxqOcc;
			break;
		}
		return res;
	}

	public QName getNodeName() throws XQException {
		if (!(ti.getType()==Type.START_TAG || Type.isAttribute(ti.getType())))
			throw new XQException("Not a node");
		if(ti.getName().indexOf("{") != -1)
			return new QName(ti.getName().substring(ti.getName().indexOf("{") + 1, ti.getName().indexOf("}")), ti.getName().substring(ti.getName().indexOf("}") + 1));
		return new QName(ti.getName());
	}

	public String getPIName() throws XQException {
		if (ti.getType() != Type.PROCESSING_INSTRUCTION)
			throw new XQException("Not a node");
		return ti.getName();
	}

	public URI getSchemaURI() {
		// TODO Auto-generated method stub
		return null;
	}

	public QName getTypeName() throws XQException {
		if(q == null){
		if(bType == -1){
			if (!(Type.isAtomicType(ti.getType(), null) || Type.isAttribute(ti.getType()) || Type.isUserDefinedType(ti.getType())))
				throw new XQException("Getting the type name not possible"); 
			ch.ethz.mxquery.datamodel.QName qn;
			if(Type.isAttribute(ti.getType()))
				qn = Type.getTypeQName(Type.getPrimitiveAtomicType(ti.getType()), Context.getDictionary());
			else
				qn = Type.getTypeQName(ti.getType(), Context.getDictionary());
			javax.xml.namespace.QName retQn = new javax.xml.namespace.QName("http://www.w3.org/2001/XMLSchema", qn.getLocalPart(),qn.getNamespacePrefix());
			return retQn;
		} else {
			if (!(Type.isAtomicType(bType, null) || Type.isAttribute(bType) || Type.isUserDefinedType(bType)))
				throw new XQException("Getting the type name not possible"); 
			ch.ethz.mxquery.datamodel.QName qn;
			if(Type.isAttribute(bType))
				qn = Type.getTypeQName(Type.getPrimitiveAtomicType(bType), Context.getDictionary());
			else
				qn = Type.getTypeQName(bType, Context.getDictionary());
			javax.xml.namespace.QName retQn = new javax.xml.namespace.QName("http://www.w3.org/2001/XMLSchema", qn.getLocalPart(),qn.getNamespacePrefix());
			return retQn;
		}
		} else 
			return q;
	}

	public boolean isAnonymousType() {
		// TODO Auto-generated method stub
		return false;
	}

	public boolean isElementNillable() {
		return nillable;
	}

	public XQItemType getItemType() {
		return this;
	}

}
