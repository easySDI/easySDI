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

package ch.ethz.mxquery.datamodel.types;

/** Some of node kind types (e.g. element, attribute, PI) might have name part in the type definition.
 *  It's not possible to represent type with integer number in these cases. 
 *  */ 
public class TypeInfo {
	
	public static final int UNDEFINED = -1; 
	private int type 	= UNDEFINED;
	private int occurID = UNDEFINED;
	private String name = null;
	private String uri 	= null;
	private int typeAnnotation =UNDEFINED;
	private final static int MASK_GET_START_TAG = Integer.parseInt("0001100000000000000000000000000", 2);
	private final static int MASK_CLEAN_START_TAG = Integer.parseInt("1110011111111111111111111111111", 2);
	
	public TypeInfo(){}
	
	public TypeInfo(int type, int occurIndID, String name, String uri) {
		this.type = type;
		this.occurID = occurIndID;
		this.name = name;
		this.uri = uri;
	}
	
	public TypeInfo(int type, int occurIndID, String name, String uri,int typeAn) {
		this.type = type;
		this.occurID = occurIndID;
		this.name = name;
		this.uri = uri;
		this.typeAnnotation = typeAn;
	}
	
	public int getTypeAnnotation() {
		int t;
		if (type == -1 ) return -1;
		
	//	if (Type.isAttribute(type))
	//	t = Type.getAttributeValueType(type);
		else{ 
		t = this.type & MASK_CLEAN_START_TAG;
		}
		
		if (t == 0) return -1;
		else return t;
		//return typeAnnotation;
	}
	
	public void setTypeAnnotation(int typeAnnotation) {
		this.typeAnnotation = typeAnnotation;
	}
	
	public void setName(String name) {
		this.name = name;
	}
	/**
	 * Set the occurence ID
	 * @param occurID
	 */
	public void setOccurID(int occurID) {
		this.occurID = occurID;
	}

	public void setType(int type) {
		this.type = type;
	}

	public void setNameSpaceURI(String uri) {
		this.uri = uri;
	}	
	
	public String getName() {
		return name;
	}

	public int getType() {
//		System.out.println("itan"+Type.getTypeQName(this.eventType));
		int t = this.type;
	    t =t & MASK_GET_START_TAG;
		
	if (t == Type.START_TAG){
	//	System.out.println("egine"+Type.getTypeQName(type));
		return Type.START_TAG;
	}
	else if (t== Type.TYPE_NK_ATTR_TEST)
		return Type.TYPE_NK_ATTR_TEST;
		else {
		//	System.out.println("egine"+Type.getTypeQName(this.eventType));
		return this.type;
		}
		
		//return type;
	}

	public String getNameSpaceURI() {
		return uri;
	}
	
	public int getOccurID() {
		return occurID;
	}

	public boolean isUndefined(){
		return (type == UNDEFINED);
	}
	
	public TypeInfo copy() {
		if (typeAnnotation != UNDEFINED)
			return new TypeInfo(type,occurID,name,uri,typeAnnotation);
		else
		return new TypeInfo(type, occurID,name, uri);
	}

	public String toString() {
		return "Type: "+Type.getTypeQName(type, null)+" LocalName: "+name+" URI "+uri;
	}
	
	
}