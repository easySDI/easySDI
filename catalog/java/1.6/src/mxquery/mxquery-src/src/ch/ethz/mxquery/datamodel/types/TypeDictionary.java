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

import java.util.Hashtable;
import java.util.Vector;

import org.apache.xerces.xs.XSComplexTypeDefinition;
import org.apache.xerces.xs.XSObject;
import org.apache.xerces.xs.XSTypeDefinition;

import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.StaticException;

/**
 * Dictionary of types for the representation of UDT in the type system
 * 
 */
public class TypeDictionary {
	private Hashtable dictionary;

	private Hashtable indexesTable;

	private Hashtable typesTable;
	
	private Hashtable UDTFunctionNamesTable;

	private Vector namespacesSet;
	
	private Vector xsImportList; //used for marking types belonging to a different targetNamespace and have been imported in schema
	//they should be available for validation but should be explicitely imported in each module
	
	private String schemaLocations ="";
	
	public static final int COMPLEX_EMPTY = 0;
	public static final int COMPLEX_SIMPLE =1;
	public static final int COMPLEX_ELEMENT_ONLY = 2 ;
	public static final int COMPLEX_MIXED = 3 ;
	public static final int SIMPLE = 4;

	/**
	 * Constructor
	 */
	public TypeDictionary() {
		dictionary = new Hashtable();
		indexesTable = new Hashtable();
		typesTable = new Hashtable();
		namespacesSet = new Vector();
		UDTFunctionNamesTable = new Hashtable();
		xsImportList = new Vector();
	}

	/**
	 * Adds an entry to the set of target namespaces for which schema
	 * definitions have been imported
	 * 
	 * @param targetNamespace
	 */
	public synchronized void addToNamespacesSet(String targetNamespace) {
		namespacesSet.addElement(targetNamespace);
	}

	public synchronized boolean containsDefinitions4Namespace(String targetNamespace) {
		return namespacesSet.contains(targetNamespace);
	}

	/**
	 * Adds an entry to the dictionary
	 * 
	 * @param typeName
	 *            The name of the type
	 * @param schemaComponent
	 *            The XSObject representing the XML Schema component
	 * @throws StaticException
	 */

	public synchronized void addEntry(String typeName, XSObject schemaComponent) throws StaticException {
		if (!xsImportList.contains(typeName) && dictionary.get(typeName) != null && !typeName.startsWith("{" + XQStaticContext.URI_XS + "}"))
			throw new StaticException(ErrorCodes.E0035_STATIC_SAME_SCHEMA, "Type " + typeName + " defined more than once", null);
		else {
			if (xsImportList.contains(typeName)) xsImportList.removeElement(typeName); 
			Integer currIndex = new Integer(dictionary.size());
			dictionary.put(typeName, schemaComponent);
			indexesTable.put(currIndex, typeName);
			typesTable.put(typeName, currIndex);
		}
	}

	/**
	 * Performs dictionary lookup based on the type name
	 * 
	 * @param typeName
	 * @return the corresponding dictionary entry
	 * 
	 */

	public XSObject lookUpByName(String typeName) {
		return (XSObject) dictionary.get(typeName);
	}

	/**
	 * Performs dictionary lookup based on the integer representation of UDT
	 * 
	 * @param index
	 *            The integer representation of the UDT
	 * @return the corresponding dictionary entry
	 */
	public XSObject lookUpByIntegerRepresentation(int index) {
		return (XSObject) dictionary.get(indexesTable.get(new Integer(index >> 6)));
	}

	/**
	 * Returns the type name for a given integer representation of a UDT
	 * 
	 * @param index
	 *            The integer representation of the UDT
	 * @return the type name
	 * 
	 */
	public String getTypeNameByIndex(int index) {
		return (String) indexesTable.get(new Integer(index >> 6));
	}

	public Integer getTypeAsInteger(String typeName) {
		Integer I = ((Integer) typesTable.get(typeName));
		if (I != null) {
			int i = I.intValue() << 6;
			return Integer.valueOf(String.valueOf(i));
		} else
			return null;
	}
	
	/**
	 * Returns the target namespace - schema location pairs to be used by Xerces
	 */
	public synchronized String getSchemaLocations(){
		return schemaLocations;
	}
	
	/**
	 * Appends one more namespace - schemaLocation pair to the list of pairs 
	 * @param  value
	 * 
	 */
	public synchronized void updateSchemaLocation(String value){
		schemaLocations+=" "+value;
		//System.out.println(schemaLocations);
	}
	
	/**
	 * Returns the kind of content of a type
	 * @param typeAnnotation
	 * @param dict
	 * @return one of COMPLEX_*, SIMPLE
	 */
	public static int findContentType(int typeAnnotation,TypeDictionary dict) {
		if (Type.isUserDefinedType(typeAnnotation)){
		//	System.err.println(Type.getTypeQName(typeAnnotation, dict));
			typeAnnotation = typeAnnotation & Type.MASK_USER_DEFINED_TYPE_INDEX;
			XSTypeDefinition typeDef = (XSTypeDefinition) dict.lookUpByIntegerRepresentation(typeAnnotation);
			if (typeDef.getTypeCategory() == XSTypeDefinition.COMPLEX_TYPE){
				XSComplexTypeDefinition complexTypeDef = (XSComplexTypeDefinition) typeDef;
				return complexTypeDef.getContentType();
			}
			else return SIMPLE;
		}
		else if (Type.isAtomicType(typeAnnotation,dict)) return SIMPLE;
		else return -1;
	}

	/**
	 * Add a list of UDT constructor functions names to be added later to the registry (used when schemas are pre-loaded")
	 * @param targetNamespace -  The targetNamespace to which the list of function names corresponds
	 * @param functionsList   -  The list of functions to be added
	 */
	public synchronized void addUDTFunctionsListEntry(String targetNamespace, Vector functionsList) {
		if (UDTFunctionNamesTable!=null && !UDTFunctionNamesTable.contains(targetNamespace))
			UDTFunctionNamesTable.put(targetNamespace, functionsList);
	}
	
	/**
	 * Returns a list of function names for a given targetNamespace
	 * @param targetNamespace 
	 * @return List of function names for a given targetNamespace
	 */
	public Vector getUDTFunctionsList(String targetNamespace){
		Object list = UDTFunctionNamesTable.get(targetNamespace);
		if (list != null) return (Vector)list;
		else return null;
	}

	public void add2XSImportList(String string) {
		xsImportList.addElement(string);
	}

}
