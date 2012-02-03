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

package examples;

import java.io.StringReader;

import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XMLSource;


public class SimpleExample {

	public static void main(String [] args) throws Exception {
		//Sample query, replace by your own
		String query = "declare variable $input external; <result>{$input,$input}</result>";

		boolean updateFiles = true;
		
		// Create new (unified) Context
		Context ctx = new Context();
		// Create a compiler options oh
		CompilerOptions co = new CompilerOptions();
		// Enable schema support
		co.setSchemaAwareness(true);
		// Enable update facility support
		co.setUpdate(true);
		// use updateable stores by default
		ctx.getStores().setUseUpdateStores(true);
		ctx.getStores().setSerializeStores(updateFiles);
		// create a XQuery compiler
		XQCompiler compiler = new CompilerImpl();
		PreparedStatement statement;
					
		try {
			//out of the context and the query "text" create a prepared statement, 
			// considering the compiler options
			statement =  compiler.compile(ctx, query,co);
			XDMIterator result;
			String strResult = "";
			// Get an iterator from the prepared statement
			// Set up dynamic context values, e.g., current time
			result = statement.evaluate();
			// Add the contents of the external variable
			String xml = "<elem/>";
			XMLSource xmlIt = XDMInputFactory.createXMLInput(result.getContext(), new StringReader(xml),true,Context.NO_VALIDATION,QueryLocation.OUTSIDE_QUERY_LOC);
			statement.addExternalResource(new QName("input"), xmlIt);
			// Create an XDM serializer, can take an XDMSerializerSettings object if needed
			XDMSerializer ip = new XDMSerializer();
			// run expression, generate XDM instance and serialize into String format
			strResult = ip.eventsToXML(result);
			// XQuery Update "programs" create a pending update list, not a normal result
			// apply the results to the relevant "stores"
			// currently in-memory stores
			statement.applyPUL();
			// serialize the contents of modified stores to disk
			// in this case, do not make a backup of the modified files
			if (updateFiles)
				statement.serializeStores(false);
			// Release all resources associated with the statement
			statement.close();
			
			System.out.println(strResult);
		} catch (MXQueryException err) {
			MXQueryException.printErrorPosition(query, err.getLocation());
			System.err.println("Error:");
			throw err;
		}

	}
}


