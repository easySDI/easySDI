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

package ch.ethz.mxquery.datamodel.types;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.util.URIUtils;

public class TypeLexicalConstraints {

	
	public static boolean satisfyStringConstraints(int type, String val ) throws MXQueryException {
		//	System.out.println(Type.getTypeQName(type, Context.getDictionary()));
			type = Type.getPrimitiveAtomicType(type);
			
			switch(type) {
				case Type.STRING:
				case Type.UNTYPED_ATOMIC:
				case Type.UNTYPED:
				case Type.ANY_URI:  // validated in AnyURIToken class
					//do nothing
					return true;
				case Type.NORMALIZED_STRING:	
					return validate_NORMALIZED_STRING(type, val);
				case Type.TOKEN:
					return validate_TOKEN(type, val);
				case Type.LANGUAGE:
					return validate_LANGUAGE(type, val);
				case Type.NAME:
					return validate_NAME(type, val);
				case Type.NCNAME:
				case Type.ID:
				case Type.IDREF:
				case Type.ENTITY:
					return validate_NCNAME(type, val);
				case Type.NMTOKEN:
					return validate_NMTOKEN(type, val);
				default :
					if (Type.isUserDefinedType(type)) return validateUDT(type,val,Context.getDictionary()); 
					else
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "xs:string subtype with footprint " + type + " is not defined.", null);		
			
			}	
		}

		public static boolean validateUDT(int type, String val,TypeDictionary dict) throws MXQueryException{
			if (Type.isSubTypeOf(type, Type.STRING,dict)||
				Type.isSubTypeOf(type, Type.ANY_URI,dict)
				||Type.isSubTypeOf(type, Type.IDREF,dict)) return true;
			else if (Type.isSubTypeOf(type, Type.NORMALIZED_STRING,dict))
				return validate_NORMALIZED_STRING(type, val);
			else if (Type.isSubTypeOf(type, Type.TOKEN,dict))
				return validate_TOKEN(type, val);
			else if (Type.isSubTypeOf(type, Type.LANGUAGE,dict))
				return validate_LANGUAGE(type, val);
			else if (Type.isSubTypeOf(type, Type.NAME,dict))
				return validate_NAME(type, val);
			else if (Type.isSubTypeOf(type, Type.NCNAME,dict)||
					Type.isSubTypeOf(type, Type.ENTITY,dict))
				return validate_NAME(type, val);
			else throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "xs:string subtype with footprint " + type + " is not defined.", null);
			
		}
		public static boolean validate_NORMALIZED_STRING(int t, String val) throws MXQueryException {
			
			if (  val == null || val.indexOf("\r") > -1 || val.indexOf("\n") > -1 || val.indexOf("\t") > -1 )  
				return false;
			return true;
		}
		
		public static boolean validate_TOKEN(int t, String val) throws MXQueryException {
			if (!validate_NORMALIZED_STRING(t, val))
				return false;
			if ( val.startsWith(" ")  || val.endsWith(" ") || val.indexOf("  ") > -1)
				return false;
			return true;
		}
		
		public static boolean validate_LANGUAGE(int t, String val) throws MXQueryException {
			if (val.length() == 0)
				return false;
			if (!validate_TOKEN(t, val))
				return false;
			
			String rest = val;
			String tok= null;
			int count = 0;
			
			while( rest.length() > 0 ) {
				if (rest.indexOf("-") > -1) {
					tok = rest.substring(0, rest.indexOf("-") );
					rest = rest.substring(rest.indexOf("-")+1);
					if (rest.length() == 0) 
						return false;
				} else{
					tok = rest;
					rest = "";
				}
				
				if (! isLangContentFine(tok, count) ) { 
					return false;
				}	
				count++;
			}
			return true;
		}

//		 Byte values of the characters:	
//		65	A
//		90	Z
//		97	a
//		122	z
//		48	0
//		57	9
		// Character.getNumericValue(part.charAt(i)); is not supported in CLDC
		public static boolean isLangContentFine(String part, int count) throws MXQueryException {
			if (part == null || part.length() < 1 || part.length() > 8)
				return false;
			
			if (count > 0) {
				// second and subsequent parts (additionally numbers allowed)
				for(int i=0; i < part.length(); i++) {
					int charNumVal = (part.substring(i, i+1)).getBytes()[0];
					if ( (charNumVal >= 65 && charNumVal <= 90) ||
						 (charNumVal >= 97 && charNumVal <= 122) ||
						 (charNumVal >= 48 && charNumVal <= 57) ) {
						// ok; do nothing
					}
					else return false;
				}
			} else {
				// first part
				for(int i=0; i < part.length(); i++) {
					int charNumVal = (part.substring(i, i+1)).getBytes()[0];
					if ( (charNumVal >= 65 && charNumVal <= 90) ||
						 (charNumVal >= 97 && charNumVal <= 122) ) {
						// ok; do nothing
					}
					else return false;			}
			}
			
			return true;
		}

		
		public static boolean validate_NAME(int t, String val) throws MXQueryException {
			if (val.length() == 0)
				return false;
			if(!validate_TOKEN(t, val))
				return false;

			if (! isNameContentFine(val, false) ) { 
				return false;
			}	
			return true;
		}	
		
		public static boolean validate_NCNAME(int t, String val) throws MXQueryException {
			if (!validate_NAME(t, val))
				return false;

			if (val.indexOf(':') >= 0) { 
				return false;
			}
			return true;
		}	
		
		//TODO: add additional restricted characters
		public static boolean isNameContentFine(String name, boolean nonColonized){

//			if (nonColonized && name.indexOf(":") > -1)
//				return false;
			
			char charNumVal = name.charAt(0);
			if ( (charNumVal >= 65 && charNumVal <= 90) ||
				 (charNumVal >= 97 && charNumVal <= 122) || 
				 charNumVal == ':' || charNumVal =='_' ) {
				// ok; do nothing
			} else return false;

			//second and subsequent chars
			for(int i=1; i < name.length(); i++) {
				charNumVal = name.charAt(i);
				
				if ( (charNumVal >= 65 && charNumVal <= 90) ||
						 (charNumVal >= 97 && charNumVal <= 122) ||
						 (charNumVal >= 48 && charNumVal <= 57) || 
						 charNumVal == ':' || charNumVal =='_' ||
                         charNumVal =='-' || charNumVal ==('.') ) {
						// ok; do nothing
				}
				else return false;
			}
			
			return true;
		}
		
		public static boolean validate_NMTOKEN(int t, String val) throws MXQueryException{
			if (val.length() == 0)
				return false;
			if(!validate_TOKEN(t, val))
				return false;
			String s = null;
			int charNumVal;
			for(int i=0; i < val.length(); i++) {
				s = val.substring(i,i+1);
				charNumVal = s.getBytes()[0];
				
				if ( (charNumVal >= 65 && charNumVal <= 90) ||
						 (charNumVal >= 97 && charNumVal <= 122) ||
						 (charNumVal >= 48 && charNumVal <= 57) || 
						 s.equals(":") || s.equals("_") || s.equals("-") || s.equals(".") ) {
						// ok; do nothing
				}
				else return false;
			}
			return true;
		}
		
		
		
		public static boolean isValidURI(String value) {
			return URIUtils.isValidURI(value);
		}
		
		public static boolean isRelativeURI(String value) {
			return URIUtils.isRelativeURI(value);
		}

		public static boolean isAbsoluteURI(String value) {
			return URIUtils.isAbsoluteURI(value);
		}
		public static boolean satisfyIntegerRange(int type, long val)throws MXQueryException {
			switch(type) {
				case Type.INTEGER:
				case Type.LONG:  //not checked Long.MIN_VALUE, Long.MAX_VALUE
					return true;
				case Type.NON_POSITIVE_INTEGER:
					return (val <= 0);
				case Type.NEGATIVE_INTEGER:
					return (val <= -1);
				case Type.INT:
					return (val >= Integer.MIN_VALUE && val <= Integer.MAX_VALUE);
				case Type.SHORT:
					return (val >= Short.MIN_VALUE && val <= Short.MAX_VALUE);
				case Type.BYTE:
					return (val >= Byte.MIN_VALUE && val <= Byte.MAX_VALUE);
				case Type.NON_NEGATIVE_INTEGER:
					return (val >= 0);
				case Type.POSITIVE_INTEGER:
					return (val >= 1);
				case Type.UNSIGNED_LONG:
					return (val >= 0);   //max UNSIGNED_LONG is not checked
				case Type.UNSIGNED_INT:
					return (val >= 0 && val <= 4294967295L);
				case Type.UNSIGNED_SHORT:
					return (val >= 0 && val <= 65535);
				case Type.UNSIGNED_BYTE:
					return (val >= 0 && val <= 255);
				default :
					if (Type.isUserDefinedType(type)) return satisfyIntegerRangeUDT(type,val,Context.getDictionary());
					else 
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "xs:integer subtype with footprint " + type + " is not defined.", null);		
					
			}
		}	

		public static boolean satisfyIntegerRangeUDT(int type, long val,TypeDictionary dict) throws MXQueryException{
			if (Type.isSubTypeOf(type, Type.INTEGER,dict)||
					Type.isSubTypeOf(type, Type.LONG,dict)) return true;
			else if (Type.isSubTypeOf(type, Type.NON_POSITIVE_INTEGER,dict))
			return 	satisfyIntegerRange(Type.NON_POSITIVE_INTEGER,val);
			else if (Type.isSubTypeOf(type, Type.NEGATIVE_INTEGER,dict))
				return 	satisfyIntegerRange(Type.NEGATIVE_INTEGER,val);
			else if (Type.isSubTypeOf(type,Type.INT,dict))
				return 	satisfyIntegerRange(Type.INT,val);
			else if (Type.isSubTypeOf(type, Type.SHORT,dict))
				return 	satisfyIntegerRange(Type.SHORT,val);
			else if (Type.isSubTypeOf(type, Type.BYTE,dict))
				return 	satisfyIntegerRange(Type.BYTE,val);
			else if (Type.isSubTypeOf(type, Type.NON_NEGATIVE_INTEGER,dict))
				return 	satisfyIntegerRange(Type.NON_NEGATIVE_INTEGER,val);
			else if (Type.isSubTypeOf(type, Type.POSITIVE_INTEGER,dict))
				return 	satisfyIntegerRange(Type.POSITIVE_INTEGER,val);
			else if (Type.isSubTypeOf(type, Type.UNSIGNED_LONG,dict))
				return 	satisfyIntegerRange(Type.UNSIGNED_LONG,val);
			else if (Type.isSubTypeOf(type, Type.UNSIGNED_INT,dict))
				return 	satisfyIntegerRange(Type.UNSIGNED_INT,val);
			else if (Type.isSubTypeOf(type, Type.UNSIGNED_BYTE,dict))
				return 	satisfyIntegerRange(Type.UNSIGNED_BYTE,val);
			else throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "numerical subtype with footprint " + type + " is not defined.", null);
			
		}
	
	
}
