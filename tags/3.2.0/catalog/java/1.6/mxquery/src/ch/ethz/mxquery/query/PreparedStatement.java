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
package ch.ethz.mxquery.query;


import java.io.IOException;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.XQDynamicContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.xdmio.StoreSet;

/**
 * Represents a compiled XQuery statement with it dynamic context and associated stores
 * @author Peter Fischer
 *
 */

public interface PreparedStatement {
	/**
	 * Initialize the dynamic context with the relevant dynamic information (e.g. time) and 
	 * @return an MXQuery XDM Iterator
	 * @throws MXQueryException if setting up the dynamic context fails
	 */
	public XDMIterator evaluate() throws MXQueryException;
	
	/**
	 * Add a binding to an external variable. 
	 * This is mainly a convenience method in order to easily bind textual XML content
	 * Most likely, this method will be deprecated
	 * @param varname
	 * @param resource
	 * @throws MXQueryException
	 */
	public void addExternalResource(String varname, String resource)
			throws MXQueryException;
		
	/**
	 * Add a binding to an external variable using an MXQuery XDM Iterator
	 * @param varname a QName describing the 
	 * @param resource a MXQuery iterator producing XDM
	 * @throws MXQueryException if the binding failed 	 
	 */
	public void addExternalResource(QName varname, XDMIterator resource)
			throws MXQueryException;
	/**
	 * Set the context item to values produces by an XDM iterator
	 * @param resource a MXQuery iterator producing XDM
	 */
	public void setContextItem(XDMIterator resource) throws MXQueryException;
	/**
	 * Retrieves the dynamic context of this statement
	 * @return the dynamic context of this statement
	 */
	public XQDynamicContext getContext();
	
	public boolean isModuleDecl();
	
	public void exposeModule();
	
	public boolean isWebService();
	/**
	 * Generate the WSDL describing the functions and variables of the module expressed in this statement 
	 * @param serverURL the Server URL describing where to call the functions, will be embedded into the WSDL
	 * @return a WSDL describing all the functions and variables exposed by the module
	 * @throws MXQueryException if generating the WSDL failed
	 */
	public String generateWSDL(String serverURL) throws MXQueryException;
	/**
	 * Handle a SOAP call by extracting the payload, 
	 * transforming into a sequence of XDM values, 
	 * calling the referred function in the module and 
	 * then packaging the result into SOAP 
	 * Errors in the invocation are put into the SOAP payload as 
	 * error messages
	 * @param inputSoap textual representation of SOAP
	 * @return a StringBuffer containing the SOAP result 
	 */
	public StringBuffer handleSOAP(String inputSoap);
	/**
	 * Get the collection of stores associated with this statement
	 * @return the StoreSet containing the stores associated with the statement
	 */
	public StoreSet getStores();
	/**
	 * Get the static return type of the statement
	 * @return a TypeInfo containing the 
	 */
	public TypeInfo getStaticReturnType();
	/**
	 * Get all external variables declared by this statement
	 * @return A vector of QNames, denoting the external variables
	 */
	public Vector getExternalVariables();
	/**
	 * Get all external variables declared by this statement that have not yet a value assigned
	 * @return A vector of QNames, denoting the external variables without a value binding
	 */	
	public Vector getUnresolvedExternalVariables();
	/**
	 * Apply the pending update list for this statement
	 * @throws MXQueryException 
	 */
	public void applyPUL() throws MXQueryException;
	/**
	 * Serialize the stores modified by this statement to disk/network
	 * @param createBackup if true, a backup copy for each of the serialized/overwritten files
	 * @throws IOException if an I/O error occured
	 * @throws MXQueryException if an 
	 */
	public void serializeStores(boolean createBackup) throws IOException, MXQueryException ;
	/**
	 * Release all the resources acquired by running the statement. 
	 * After calling this function, new external values can be assigned, an the statement be run again (by calling evaluate)
	 * @throws MXQueryException
	 */
	public void close() throws MXQueryException;
	/**
	 * Create a copy of this prepared statement. 
	 * Can only be run if the prepared statement has not been evaluated before and has no bound values 
	 * @return A copy of this statement
	 * @throws MXQueryException
	 */
	public PreparedStatement copy() throws MXQueryException;
	
}
