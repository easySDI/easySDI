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

package ch.ethz.mxquery.query.webservice;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.Vector;
import java.util.Hashtable;

import javax.microedition.io.Connector;
import javax.microedition.io.HttpConnection;

import org.kxml2.io.KXmlParser;

import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.FunctionSignature;
import ch.ethz.mxquery.iterators.scripting.WSFunction;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.util.LineReader;
/**
 * Class to parse a WSDL document and to add the resulting WebService methods
 * into the Function Gallery.
 * 
 * @author David Graf
 * 
 */
public class WSDLHandler {
	private String wsdlParserUrl;
	private String wsdlParserCode;


	/**
	 * Constructor.
	 * 
	 * @param wsdlUrl
	 *            URL of the WSDL file <br />
	 *            TODO: make it also possible with a URI!
	 */
	public WSDLHandler(String wsdlUrl) {
		this.wsdlParserUrl = wsdlUrl;
	}

	/**
	 * Gets the WSDL.
	 * 
	 * @return
	 * @throws IOException
	 * @throws MXQueryException
	 */
	private String getWSDL() throws IOException, MXQueryException {
		
		HttpConnection conn = null;
		LineReader br = null;
		StringBuffer wsdl = new StringBuffer();
		try {
            conn = 
                   (HttpConnection)Connector.open(this.wsdlParserUrl);
            conn.setRequestMethod("GET");
            if (conn.getResponseCode() != HttpConnection.HTTP_OK) {
            	throw new DynamicException(ErrorCodes.A0003_EC_WSDL_IS_ERROR_MSG,
    					"WSDL reponse for '" + this.wsdlParserUrl + "' is an Error Msg: "
    							+ conn.getResponseCode() + " "
    							+ conn.getResponseMessage(),null);
            }
            else {
            	  br = new LineReader(new InputStreamReader(conn.openInputStream()));
            	  String str;
            	  while ((str = br.readLine()) != null) {
            		  wsdl.append(str).append("\n");
            	  }
            }
        } catch (IOException e) {
            throw e;
        } catch (MXQueryException e) {
            throw e;
        } finally {
        	if (conn != null) {
        		conn.close();
        	}
    		if (br != null) {
    			br.close();
    		}
        }
        return wsdl.toString();        
	}

	/**
	 * Parses the WSDL file (with XQUERY!).
	 * 
	 * @param wsdl
	 *            wsdl file
	 * @param runtime
	 *            static content
	 * @param namespace
	 * @throws MXQueryException
	 */
	private void parseWSDL(String wsdl, Context context,
			Namespace namespace, CompilerOptions co) throws MXQueryException {
		String functionNamePrefix = null;
		
		if (namespace != null) {
			functionNamePrefix = namespace.getNamespacePrefix();
		}
		
		Context wsdlContext = new Context();
		wsdlContext.setVariableValue(new QName("wsdl"), 
				XDMInputFactory.createXMLInput(context, 
						new StringReader(wsdl), true, context.getInputValidationMode(), null), true, true);
		XQCompiler wsdlCompiler = new CompilerImpl();
		PreparedStatement statement;
		XDMIterator result;
		
		statement = wsdlCompiler.compile(wsdlContext, this.wsdlParserCode,co);
		result = statement.evaluate();
		
		XDMSerializer ip = new XDMSerializer();
		String filteredWSDL = ip.eventsToXML(result).toString();
		KXmlParser kxp = new KXmlParser();
		try {
			String host = null;
			QName functionName = null;
			String style = null;
			String soapAction = null;
			String inputNamespace = null;
			String inputEncoding = null;
			String responseElement = null;
			Vector paramNames = new Vector();
			Vector paramTypes = new Vector();
			kxp.setInput(new StringReader(filteredWSDL));
			int type;
			while ((type = kxp.next()) != KXmlParser.END_DOCUMENT) {
				if (type == KXmlParser.START_TAG) {
					if (kxp.getName().equals("service")) {
						host = kxp.getAttributeValue(0);
					} else if (kxp.getName().equals("function")) {
						functionName = new QName(functionNamePrefix, kxp.getAttributeValue(null,
								"name"));
						soapAction = kxp.getAttributeValue(null, "soapaction");
						inputNamespace = kxp.getAttributeValue(null,
								"inputnamespace");
						inputEncoding = kxp.getAttributeValue(null,
								"inputencoding");
						responseElement = kxp.getAttributeValue(null,
								"responseelement");
						paramNames.removeAllElements();
						paramTypes.removeAllElements();
					} else if (kxp.getName().equals("param")) {
						String paramName = kxp.getAttributeValue(0);
						paramNames.addElement(new QName(paramName));
						if (kxp.getAttributeCount() == 2) {
							paramTypes.addElement(kxp.getAttributeValue(1));
						} else {
							paramTypes.addElement(null);
						}
					}
				} else if (type == KXmlParser.END_TAG) {
					if (kxp.getName().equals("function")) {
						String[] arrPNames = new String[paramNames.size()];
						paramNames.copyInto(arrPNames);
						String[] arrPTypes = new String[paramTypes.size()];
						paramTypes.copyInto(arrPTypes);
						Hashtable x = new Hashtable();
						WSFunction wf = new WSFunction(functionName, style, 
								soapAction, inputNamespace,x, inputEncoding,
								host, arrPNames, arrPTypes, responseElement, "");

						TypeInfo [] params = new TypeInfo[arrPTypes.length];
						for (int i=0;i<params.length;i++) {
							//the TypeInfo is not really used, storage and retrieval of the functions is done using 
							// only function name and arity
							params[i] = new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
						}
						FunctionSignature signature = new FunctionSignature(functionName,params,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
						Function function = new Function(null,signature,wf);

						context.addFunction(function, true, true);
					}
				}
			}
		} catch (Exception e) {
			throw new MXQueryException(ErrorCodes.A0003_EC_WSDL_IS_ERROR_MSG, e,"Error when handling a WSDL file", null);
		}
	}

	/**
	 * Starts the generating
	 * 
	 * @param runtime
	 *            Runtime where the WebService functions are added.
	 * @param namespace
	 *            namespace of the imported service (might be null)
	 * @throws MXQueryException
	 */
	public void run(Context runtime, Namespace namespace, String serviceName, String endpointName,QueryLocation loc, CompilerOptions co)
			throws MXQueryException {
		try {
			InputStream is = this.getClass().getResourceAsStream(
					"wsdlParser.xq");
			StringBuffer str = new StringBuffer();
			try {
				String thisLine;
				LineReader br = new LineReader(new InputStreamReader(is));
				while ((thisLine = br.readLine()) != null) {

					str.append(thisLine + "\n");
				}
			} catch (IOException e) {
				throw new MXQueryException(ErrorCodes.A0007_EC_IO, e,"I/O Error when handling a WSDL file", loc);
			}
			this.wsdlParserCode = str.toString();
			String wsdl = this.getWSDL();
			this.parseWSDL(wsdl, runtime, namespace, co);
		} catch (IOException e) {
			throw new MXQueryException(ErrorCodes.A0003_EC_WSDL_IS_ERROR_MSG, e,
					"Not possible to download or parse a WSDL definition!", loc);
		}
	}
}
