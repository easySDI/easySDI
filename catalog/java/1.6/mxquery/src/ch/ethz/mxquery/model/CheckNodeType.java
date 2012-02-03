/*   Copyright 2006 ETH Zurich 
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

/**
 * author Rokas Tamosevicius 
 */

package ch.ethz.mxquery.model;

import java.util.Vector;

import org.apache.xerces.impl.xs.XSAttributeDecl;
import org.apache.xerces.impl.xs.XSElementDecl;
import org.apache.xerces.xs.XSElementDeclaration;
import org.apache.xerces.xs.XSTypeDefinition;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;

public class CheckNodeType {

	public static boolean step_comparison(Token curr, String uri, String local) throws MXQueryException {
		
		if (uri == null && local == null)
			return true;

		if (uri == null) {
			if (curr.getNS() != null && !local.equals("*"))
				return false;
		} else {
			if (!uri.equals("*") && !uri.equals(curr.getNS()))
				return false;
		}
		if (local.equals("*")) {
			return true;
		} else {
			return local.equals(curr.getLocal());
		}
	}

	public static boolean checkPI(Token curr, String searchedNodeName) throws MXQueryException {
		if (curr.getEventType() == Type.PROCESSING_INSTRUCTION) {
			if (searchedNodeName == null)
				return true;
			else if (searchedNodeName.equals(curr.getName()))
				return true;
		}
		return false;
	}

	public static boolean checkComment(Token curr) throws MXQueryException {
		if (curr.getEventType() == Type.COMMENT)
			return true;
		return false;
	}

	public static boolean checkText(Token curr) throws MXQueryException {
		if (Type.isTextNode(curr.getEventType()))
			return true;
		return false;
	}

	public static boolean checkNode(Token curr) throws MXQueryException {
		if (curr.getEventType() != Type.END_TAG && curr.getEventType() != Type.END_DOCUMENT) {
			return Type.isNode(curr.getEventType());
		}
		return false;
	}

	public static boolean checkElement(Token curr, String uri, String name, int tInfoType, TypeDictionary dictionary) throws MXQueryException {

		/*
		 * if ( curr.getEventType() == Type.START_TAG){ if ( name == null ||
		 * name.equals("*") || CheckNodeType.step_comparison(curr, uri, name) )
		 * return true; } return false;
		 */
		if (curr.getEventType() == Type.START_TAG) {
			if (name == null || (tInfoType == -1 && name.equals("*")) || (tInfoType == -1 && CheckNodeType.step_comparison(curr, uri, name)) || (tInfoType != -1 && CheckNodeType.step_comparison(curr, uri, name) && CheckNodeType.type_Comparison(curr, tInfoType, dictionary, true)))
				return true;
		}
		return false;
	}

	public static final int CHECK_IDTYPE_ID = 1;
	public static final int CHECK_IDTYPE_IDREF = 2;
	public static final int CHECK_IDTYPE_IDREFS = 3;
	
	public static boolean checkIdElement(NamedToken curr, Vector ids, int idType) {
		String elemData = null;
		switch(idType) {
		case CHECK_IDTYPE_ID:
			elemData = curr.getID();
			break;
		case CHECK_IDTYPE_IDREF:
			elemData = curr.getIDREF();
			break;
		case CHECK_IDTYPE_IDREFS:
			elemData = curr.getIDREFS();
			break;
		}
		
		if (elemData != null) {
			for (int i=0;i<ids.size();i++) {
				if (elemData.equals(ids.elementAt(i))) {
					return true;
				}
			}
		}
		return false;
	}
	
	private static boolean type_Comparison(Token curr, int tInfoType, TypeDictionary dictionary, boolean isElement) {
		int type = curr.getTypeAnnotation();
	//	System.out.println("comparing"+ Type.getTypeQName(type, dictionary)+" with "+ Type.getTypeQName(tInfoType, dictionary));

		if (isElement) { // nilled value should also be examined for
							// ElementTest
			boolean nillable = false;
			if (Type.isNilled(type)) {
				type = Type.setNilledFalse(type);
				nillable = true;
			}
			if (Type.isNilled(tInfoType)) {
				tInfoType = Type.setNilledFalse(tInfoType);
				return Type.isTypeOrSubTypeOf(type, tInfoType, dictionary);
			} else
				return !nillable && Type.isTypeOrSubTypeOf(type, tInfoType, dictionary);
		}
		// AttributeTest
		else
			return Type.isTypeOrSubTypeOf(type, tInfoType, dictionary);
	}

	public static boolean checkAttribute(Token curr, String uri, String name, int tInfoType, TypeDictionary dictionary) throws MXQueryException {
	
		  if ( Type.isAttribute(curr.getEventType()) ){ if ( name == null || CheckNodeType.step_comparison(curr, uri, name) )
		  return true; } return false;
		 
		
		/*if (Type.isAttribute(curr.getEventType())) {
			if (name == null || (tInfoType == -1 && name.equals("*")) || (tInfoType == -1 && CheckNodeType.step_comparison(curr, uri, name)) || (tInfoType != -1 && CheckNodeType.step_comparison(curr, uri, name) && CheckNodeType.type_Comparison(curr, Type.getPrimitiveAtomicType(tInfoType), dictionary, false)))
				return true;
		}
		return false;*/
	}

	public static boolean checkAttributeWithType(Token curr, String uri, String name, int tInfoType, TypeDictionary dictionary) throws MXQueryException {
		/*
		 * if ( Type.isAttribute(curr.getEventType()) ){ if ( name == null ||
		 * name.equals("*") || CheckNodeType.step_comparison(curr, uri, name) )
		 * return true; } return false;
		 */
		
		if (Type.isAttribute(curr.getEventType())) {
			if (name == null || (tInfoType == -1 && name.equals("*")) || (tInfoType == -1 && CheckNodeType.step_comparison(curr, uri, name)) || (tInfoType != -1 && CheckNodeType.step_comparison(curr, uri, name) && CheckNodeType.type_Comparison(curr, Type.getPrimitiveAtomicType(tInfoType), dictionary, false)))
				return true;
		}
		return false;
	}

	public static boolean checkDocument(Token curr) throws MXQueryException {
		if (curr.getEventType() == Type.START_DOCUMENT) {
			return true;
		}
		return false;
	}

	public static boolean checkSchemaElement(Token tok, String node_name_uri, String node_name, TypeDictionary dictionary,QueryLocation loc) throws TypeException {
		boolean nilled;
		String subGroupElemName = "";
		String subGroupElemNamespace = "";

		XSElementDecl tokElemDecl = (XSElementDecl) dictionary.lookUpByName("(" + tok.getNS() + ")" + tok.getName());
		// find substitution group affiliation
		if (tokElemDecl != null) {
			XSElementDeclaration subGroup = tokElemDecl.getSubstitutionGroupAffiliation();
			if (subGroup != null) {
				subGroupElemName = subGroup.getName();
				subGroupElemNamespace = subGroup.getNamespace();
			}
		}
		if ((tok.getName().equals(node_name) && tok.getNS().equals(node_name_uri)) || (tok.getName().equals(subGroupElemName) && tok.getNS().equals(subGroupElemNamespace))) { 
			XSElementDecl elemDecl = (XSElementDecl) dictionary.lookUpByName("(" + node_name_uri + ")" + node_name);
			if (elemDecl != null) {
				XSTypeDefinition typeDef = elemDecl.getTypeDefinition();

				try {
					int type;
					int typeAnnotation;
					String namespace = typeDef.getNamespace();
					String local;
					if (!typeDef.getAnonymous())
						local = typeDef.getName();
					else 
						local = "#AnonType_"+tok.getLocal();
					if (!namespace.equals(XQStaticContext.URI_XS))
						type = Type.getTypeFootprint(new QName(namespace, namespace, local), dictionary);
					else
						type = Type.getTypeFootprint(new QName(Type.NAMESPACE_XS, local), dictionary);
					 typeAnnotation = tok.getTypeAnnotation();
					if (Type.isTypeOrSubTypeOf(typeAnnotation, type, dictionary)) {
						nilled = Type.isNilled(typeAnnotation);
						if (nilled)
							Type.setNilledFalse(typeAnnotation);
						if (nilled && !elemDecl.getNillable())
							throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed: Element {"+ node_name_uri + "}" + node_name+" should not be nilled",loc);
						else
							return true;
					} else
						throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed: Expected  {"+node_name_uri+"}:"+node_name+" with type: "+Type.getTypeQName(type, Context.getDictionary())+", encountered  {"+tok.getNS()+"}:"+tok.getName()+" with type "+Type.getTypeQName(tok.getTypeAnnotation(), Context.getDictionary()), loc);
				} catch (MXQueryException e) {
					return false;
				}
			} else
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed: Global Element {"+ node_name_uri + "}" + node_name+" has not been declared",loc);
		} else
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed: Expected  {"+node_name_uri+"}:"+node_name+", encountered  {"+tok.getNS()+"}:"+tok.getName(),loc);
	}

	public static boolean checkSchemaAttribute(Token tok, String node_name_uri, String node_name, TypeDictionary dictionary,QueryLocation loc) throws MXQueryException {
		if (tok.getName().equals(node_name)) {
			XSAttributeDecl attrDecl = (XSAttributeDecl) dictionary.lookUpByName("[" + node_name_uri + "]" + node_name);
			if (attrDecl != null) {
				XSTypeDefinition typeDef = attrDecl.getTypeDefinition();
				try {
					int type;
					String namespace = typeDef.getNamespace();
					String local = typeDef.getName();
					if (!namespace.equals(XQStaticContext.URI_XS))
						type = Type.getTypeFootprint(new QName(namespace, namespace, local), dictionary);
					else
						type = Type.getTypeFootprint(new QName(Type.NAMESPACE_XS, local), dictionary);
					int typeAnnotation = tok.getTypeAnnotation();
					if (Type.isTypeOrSubTypeOf(typeAnnotation, type, dictionary))
						return true;
					else
						throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed: Expected  {"+node_name_uri+"}:"+node_name+" with type: "+Type.getTypeQName(typeAnnotation, Context.getDictionary())+", encountered  {"+tok.getNS()+"}:"+tok.getName()+" with type "+Type.getTypeQName(tok.getTypeAnnotation(), Context.getDictionary()), loc);
				} catch (MXQueryException e) {
					return false;
				}
			} else
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed: Global Element {"+ node_name_uri + "}" + node_name+" has not been declared",loc);
		} else
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed: Expected  {"+node_name_uri+"}:"+node_name+", encountered  {"+tok.getNS()+"}:"+tok.getName(),loc);
	}
}