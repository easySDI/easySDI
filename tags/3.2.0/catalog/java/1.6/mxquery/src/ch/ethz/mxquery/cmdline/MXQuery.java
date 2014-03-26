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

package ch.ethz.mxquery.cmdline;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintStream;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Map;


import org.apache.commons.cli.CommandLine;
import org.apache.commons.cli.CommandLineParser;
import org.apache.commons.cli.GnuParser;
import org.apache.commons.cli.HelpFormatter;
import org.apache.commons.cli.OptionBuilder;
import org.apache.commons.cli.OptionGroup;
import org.apache.commons.cli.Options;
import org.apache.commons.cli.ParseException;


import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.query.impl.PreparedStatementImpl;
import ch.ethz.mxquery.query.parser.PlanLoader;
import ch.ethz.mxquery.query.parser.SchemaParser;
import ch.ethz.mxquery.cmdline.LocalSetting;
import ch.ethz.mxquery.cmdline.TimeInfos;
import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.update.store.llImpl.LLStoreSet;
import ch.ethz.mxquery.util.IOLib;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Set;
import ch.ethz.mxquery.util.URIUtils;
import ch.ethz.mxquery.util.XPPOutputStream;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XDMSerializerSettings;
import ch.ethz.mxquery.xdmio.XMLSource;

public class MXQuery {
	
	// serializer parameter names
	private static String BYTE_ORDER_MARK = "byte-order-mark";
	private static String CD_SECTION_ELEMENTS = "cd-section-elements";
	private static String DOCTYPE_PUBLIC = "doctype-public";
	private static String DOCTYPE_SYSTEM = "doctype-system";
	private static String ENCODING = "encoding";
	private static String ESCAPE_URI_ATTRIBUTE = "escape-uri-attribute";
	private static String INCLUDE_CONTENT_TYPE = "include-content-type";
	private static String INDENT = "indent";
	private static String MEDIA_TYPE = "media-type";
	private static String METHOD = "method";
		private static String METHOD_TEXT = "text";
		private static String METHOD_HTML= "html";
		private static String METHOD_XHTML = "xhtml";
		private static String METHOD_XML = "xml";
	private static String NORMALIZATION_FORM = "normalization-form";
		private static String NORMALIZATION_FORM_NONE = "none";
		private static String NORMALIZATION_FORM_NFC = "nfc";
		private static String NORMALIZATION_FORM_NFD = "nfd";
		private static String NORMALIZATION_FORM_NFKC = "nfkc";
		private static String NORMALIZATION_FORM_NFKD = "nfkd";
		private static String NORMALIZATION_FORM_FULL = "full";
	private static String OMIT_XML_DECLARATION = "omit-xml-declaration";
	private static String STANDALONE = "standalone";
		private static String STANDALONE_YES = "true";
		private static String STANDALONE_NO = "false";
		private static String STANDALONE_OMIT = "omit";
	private static String UNDECLARED_PREFIXES = "undeclared-prefixes";
	private static String USE_CHARACTER_MAPS = "use-character-maps";
	private static String VERSION = "version";
	
	// input and validation names
	private static String VALIDATION_STRICT = "strict";
	private static String VALIDATION_LAX = "lax";
	
	// context component names
	private static String STATICALLY_KNOWN_NAMESPACES = "static-ns";
	private static String DEFAULT_ELEMENTTYPE_NAMESPACE = "default-elem-ns";
	private static String DEFAULT_FUNCTION_NAMESPACE = "default-func-ns";
	private static String CONSTRUCTION_MODE = "construction";
	private static String ORDERING_MODE = "ordering";
	private static String DEFAULT_ORDER_FOR_EMPTY_SEQUENCES = "default-es-order";
	private static String BOUNDARY_SPACE_POLICY = "boundary-space";
	private static String COPY_NAMESPACE_MODE = "copy-ns-mode";
	private static String BASE_URI = "base-uri";
	
	public static void main(String[] args) throws Exception {
		
		// Definition Stage
		Options cliOptions = defineCliOptions();
		System.out.println("MXQuery 0.6.0");
		// Parsing Stage
		CommandLine cl = parseCliOptions(cliOptions, args);
		// Interrogation Stage
		if(cl != null) {
			// initialize option objects
			LocalSetting los = new LocalSetting();
			CompilerOptions cop = new CompilerOptions();
			Context ctx = new Context();
			XDMSerializerSettings serSet = new XDMSerializerSettings();
			TimeInfos timeInfos = new TimeInfos();
			interogateCliOptions(cl, los, cop, ctx, serSet);
			/* EXECUTION STAGE */
			try {
				executeQuery(los.getInput(), ctx, timeInfos, los, serSet, cop, cl);
				if (los.isTiming()) {
					PrintStream out = System.out;
					out.print("MXQuery Engine from ETH Zurich\n");;
					out.print("Compilation Time: " + (timeInfos.getCompileEnd() - timeInfos.getCompileStart() + " milliseconds\n"));
					out.print("Execution Time: " + (timeInfos.getExecEnd() - timeInfos.getExecStart()) + " milliseconds\n");
				}
			} catch (Exception e) {
				if (los.isTiming()) {
					PrintStream out = System.out;
					out.print("MXQuery Engine from ETH Zurich -- ERROR!\n");
					out.print("Execution Time: -1 milliseconds\n");
					out.print(e.getMessage() + "\n");
					for (int i = 0; i < e.getStackTrace().length; i++) {
						StackTraceElement ste = e.getStackTrace()[i];
						out.print("    at ");
						out.print(ste.getClassName());
						out.print(".");
						out.print(ste.getMethodName());
						out.print("(");
						out.print(ste.getFileName());
						out.print(":");
						out.print(String.valueOf(ste.getLineNumber()));
						out.print(")");
						out.print("\n");
					}
				}
				throw e;
			}
		}
	}

	private static Options defineCliOptions() {
		/* DEFINITION STAGE */
		Options cliOptions = new Options();
				
		// input / output
		OptionGroup inputData = new OptionGroup();
		inputData.setRequired(true);
		inputData.addOption(OptionBuilder.hasArg().withArgName( "queryFile" ).withDescription( "specify the file of the query or plan to run" ).withLongOpt( "queryFile" ).create( "f" ));
		inputData.addOption(OptionBuilder.hasArg().withArgName( "inputQuery" ).withDescription( "specify the query in the command line" ).withLongOpt( "inlineQuery").create( "i" ));
		inputData.addOption(OptionBuilder.hasArg().withArgName( "queryPlan" ).withDescription( "specify the query plan to run" ).withLongOpt( "queryPlan" ).create( "p" ));
		OptionGroup outputData = new OptionGroup();
		outputData.setRequired(false);
		outputData.addOption(OptionBuilder.withDescription( "discard the generated result" ).withLongOpt( "discardResult" ).create( "d" ));
		outputData.addOption(OptionBuilder.hasArg().withArgName( "=fileName|-" ).withDescription( "specify where the result should be written to. either a file (=) o the standard output (-)" ).withLongOpt( "outputFile" ).create( "o" ));
		cliOptions.addOption(OptionBuilder.hasArgs().withArgName( "property=value" ).withValueSeparator(' ').withDescription( "specify a number of serializer parameters" ).withLongOpt( "serializer" ).create( "s" ));
		cliOptions.addOptionGroup(inputData);
		cliOptions.addOptionGroup(outputData);
		
		// print options
		cliOptions.addOption("ex",	"explain", 			false,	"print the query plan in XML format");
		cliOptions.addOption(OptionBuilder.hasArg().withArgName( "queryPlan" ).withDescription( "print the execution plan in XML format into the specified file" ).withLongOpt( "serializePlan" ).create( "sp" ));
		cliOptions.addOption("ps",	"printStores", 		false,	"print the state of all variables at the end of execution");
		cliOptions.addOption("t",	"timing",			false,	"print timing information in standard error stream");
		cliOptions.addOption("v",	"verbose",			false,	"print settings, query and additional information");
		
		// xquery language features
		cliOptions.addOption("sa",	"schemaAwareness",	false,	"enable XML Schema support and hence the use of the validate keyword");
		cliOptions.addOption("fm",	"fulltext",			false,	"enable XQuery Fulltext support");
		cliOptions.addOption("um",	"updateMode", 		false,	"enable the use of updatable variables");
		cliOptions.addOption("sm",	"scriptingMode",	false,	"enable the use of scripting facilities");
		cliOptions.addOption("11m",	"xquery11Mode",		false,	"enable the use of XQuery 1.1 features");
		cliOptions.addOption("cm",	"continuousMode",	false,	"enable the use of Continous/Streaming XQuery");		
		cliOptions.addOption("x",	"updateFiles", 		false,	"make updates on files persistent");
		cliOptions.addOption("b",	"backupUpdates",	false,	"backup updated files by storing the old version with a .bak extension");
		
		// input and validation
		OptionGroup validation = new OptionGroup();
		validation.addOption(OptionBuilder.withDescription( "enable DTD support/validation when fn:doc is called" ).withLongOpt("dtdAwareness").create("dtd"));
		validation.addOption(OptionBuilder.hasArg().withArgName( "validationMode" ).withDescription( "enable input (schema) validation and optionally set the validation mode [strict|lax] when fn:doc is called" ).withLongOpt( "validation" ).create( "val" ));
		cliOptions.addOptionGroup(validation);
		cliOptions.addOption("str",	"xmlStream",		false,	"enable input stream validation");
		cliOptions.addOption(OptionBuilder.hasArgs().withArgName( "schema=file" ).withDescription(  "specify a number of schema files for loading at engine instantiation time" ).withLongOpt( "schemaFiles" ).create( "xsd" ));
		
		// context
		cliOptions.addOption(OptionBuilder.hasArgs().withArgName( "component=value" ).withValueSeparator(' ').withDescription( "specify a number of components of the static context" ).withLongOpt( "context" ).create( "c" ));
		cliOptions.addOption(OptionBuilder.hasArg().withArgName( "var:=literal|var=file|var-" ).withValueSeparator(' ').withDescription( "specify external variable of context item as literal (:=), file (=) or standard input(-). Use '.' as variable name to specify the context item" ).withLongOpt( "externalVariable" ).create( "e" ));
	
		return cliOptions;
	}
	
	private static CommandLine parseCliOptions(Options cliOptions, String[] s) {
		/* PARSING STAGE */
		CommandLine cl = null;
		if (s.length == 0) {
			HelpFormatter formatter = new HelpFormatter();
			formatter.printHelp( "java -jar mxquery.jar ", cliOptions );
		}
		
		CommandLineParser parser = new GnuParser();
		try {
			cl = parser.parse(cliOptions, s);
		}  catch( ParseException exp ) {
			if (s.length != 0)
				// oops, something went wrong
				System.err.println( "Parsing failed.  Reason: " + exp.getMessage() );
			//otherwise, we already printed the usage
	    }
		
		return cl;
	}
	
	private static void interogateCliOptions(CommandLine cl, LocalSetting los, CompilerOptions cop, Context ctx, XDMSerializerSettings serSet) throws IOException, MXQueryException {
		/* INTERROGATION STAGE */
		// input
		// [queryPlan to be completed]
		if(cl.hasOption("f")) {
			String fileName = cl.getOptionValue("f");
			File file = new File(fileName);
			if (file.exists()) {
				los.setInput(ch.ethz.mxquery.util.FileReader.getFileContent(file.toURI().toString()));
			} else {
				throw new FileNotFoundException("ERROR: The specified file does not exist: "+ fileName+"\n");
			}
		} else if (cl.hasOption("i")) {
			los.setInput(cl.getOptionValue("i"));
		} else {
			los.setInput(cl.getOptionValue("p"));
			los.setIsFromPlan(true);
		}
		
		// output
		if(cl.hasOption("d")) {
			los.setIsDiscardResult(true);
		} else  if (cl.hasOption("o")){
			if (cl.getOptionValue("o").trim().equalsIgnoreCase("-")) {
				los.setIsToFile(false);
			} else {
				los.setIsToFile(true);
				los.setOutput(cl.getOptionValue("o"));
			}
		}
		
		// print options
		if(cl.hasOption("ex")) {
			los.setIsExplain(true);
		}
		if(cl.hasOption("sp")) {
			los.setIsSerializePlan(true);
			los.setSerializationPlan(cl.getOptionValue("sp"));
		}
		if(cl.hasOption("ps")) {
			los.setIsPrintStores(true);
		}
		if(cl.hasOption("t")) {
			los.setIsTiming(true);
		}	
		if(cl.hasOption("x")) {
			los.setUpdateFiles(true);
			ctx.getStores().setSerializeStores(true);
		}
		if(cl.hasOption("b")) {
			los.setBackupBeforeUpdate(true);
		}

		// serializer
		if(cl.hasOption("s")) {
			try {
				processSerializerSettings(cl, serSet);
			} catch (MXQueryException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		
		parseCompilerOptions(cl,ctx, cop);			

		// input and validation
		if(cl.hasOption("dtd")) {
			ctx.setInputValidationMode(Context.DTD_VALIDATION);
		}
		if(cl.hasOption("val")) {
			String validationMode = cl.getOptionValue("val");
			if (validationMode.equalsIgnoreCase(VALIDATION_STRICT)) {
				ctx.setInputValidationMode(Context.SCHEMA_VALIDATION_STRICT);
			} else if (validationMode.equalsIgnoreCase(VALIDATION_LAX)) {
				ctx.setInputValidationMode(Context.SCHEMA_VALIDATION_LAX);
			} else {
				System.err.println("Unrecognized validation mode value: " + validationMode);
			}
		}
		if(cl.hasOption("str")) {
			// TO DO
		}
		if(cl.hasOption("xsd")) {
			String[] schemaFiles = cl.getOptionValue("xsd").split(";");
			for (int j = 0; j < schemaFiles.length; j++) {
				String schemaFile = schemaFiles[j].trim();
				try {
					SchemaParser.preLoadSchema(schemaFile);
				} catch (MXQueryException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
		}
		
		// context
		if(cl.hasOption("c")) {
			try {
				processStaticContextOptions(cl, ctx);
			} catch (MXQueryException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		if (cl.hasOption("v")) {
			los.printDebug();
		}
	}
	
	private static void parseCompilerOptions(CommandLine cl, Context ctx, CompilerOptions cop) {
		// xquery language features (compiler options)
		if(cl.hasOption("sa")) {
			cop.setSchemaAwareness(true);
		}
		if(cl.hasOption("fm")) {
			cop.setFulltext(true);
			ctx.getStores().setUseFulltextStores(true);
		}
		if(cl.hasOption("um")) {
			cop.setUpdate(true);
			ctx.getStores().setUseUpdateStores(true);
		}
		if(cl.hasOption("sm")) {
			cop.setScripting(true);
			ctx.getStores().setUseUpdateStores(true);
		}
		if(cl.hasOption("11m")) {
			cop.setXquery11(true);
		}
		if(cl.hasOption("cm")) {
			cop.setContinuousXQ(true);
		}
	}

	private static void processSerializerSettings(CommandLine cl, XDMSerializerSettings xss)
			throws MXQueryException {
		String[] propString = cl.getOptionValues("s");
		HashMap serializerMap = getArguments(propString);
	
		// byte-order-mark
		if(serializerMap.containsKey(BYTE_ORDER_MARK)) {
			xss.setByteOrderMark(new Boolean((String) serializerMap.get(BYTE_ORDER_MARK)).booleanValue());
			serializerMap.remove(BYTE_ORDER_MARK);
		}
		// cd-section-elements
		if(serializerMap.containsKey(CD_SECTION_ELEMENTS)) {
			String qnameString = (String) serializerMap.get(CD_SECTION_ELEMENTS);
			String[] qnameArray = qnameString.split(";");
			Set qnameSet = new Set();
			for (int j=0; j<qnameArray.length; j++) {
				qnameSet.add(qnameArray[j]); 
			}
			xss.setCdataSectionElements(qnameSet);
			serializerMap.remove(CD_SECTION_ELEMENTS);
		}
		// doctype-public
		if(serializerMap.containsKey(DOCTYPE_PUBLIC)) {
			xss.setDoctypePublic((String) serializerMap.get(DOCTYPE_PUBLIC));
			serializerMap.remove(DOCTYPE_PUBLIC);
		}
		// doctype-system
		if(serializerMap.containsKey(DOCTYPE_SYSTEM)) {
			xss.setDoctypeSystem((String) serializerMap.get(DOCTYPE_SYSTEM));
			serializerMap.remove(DOCTYPE_SYSTEM);
		}
		// encoding
		if(serializerMap.containsKey(ENCODING)) {
			// may want to catch strings, which are out of the range #x21 to #x7E
			xss.setEncoding((String) serializerMap.get(ENCODING));
			serializerMap.remove(ENCODING);
		}
		// escape-uri-attribute
		if(serializerMap.containsKey(ESCAPE_URI_ATTRIBUTE)) {
			xss.setEscapeURIAttributes(new Boolean((String) serializerMap.get(ESCAPE_URI_ATTRIBUTE)).booleanValue());
			serializerMap.remove(ESCAPE_URI_ATTRIBUTE);
		}
		// include-content-type
		if(serializerMap.containsKey(INCLUDE_CONTENT_TYPE)) {
			xss.setIncludeContentType(new Boolean((String) serializerMap.get(INCLUDE_CONTENT_TYPE)).booleanValue());
			serializerMap.remove(INCLUDE_CONTENT_TYPE);
		}
		// indent
		if(serializerMap.containsKey(INDENT)) {
			xss.setIndent(new Boolean((String) serializerMap.get(INDENT)).booleanValue());
			serializerMap.remove(INDENT);
		}
		// media-type
		if(serializerMap.containsKey(MEDIA_TYPE)) {
			xss.setMediaType((String) serializerMap.get(MEDIA_TYPE));
			serializerMap.remove(MEDIA_TYPE);
		}
		// method
		String outputMethod = (String) serializerMap.get(METHOD);
		if (outputMethod != null) {
			if (outputMethod.equalsIgnoreCase(METHOD_TEXT)) {
				xss.setOutputMethod(XDMSerializerSettings.OUTPUT_METHOD_TEXT);
			} else if (outputMethod.equalsIgnoreCase(METHOD_HTML)) {
				xss.setOutputMethod(XDMSerializerSettings.OUTPUT_METHOD_HMTL);
			} else if (outputMethod.equalsIgnoreCase(METHOD_XHTML)) {
				xss.setOutputMethod(XDMSerializerSettings.OUTPUT_METHOD_XHMTL);
			} else if (outputMethod.equalsIgnoreCase(METHOD_XML)) {
				xss.setOutputMethod(XDMSerializerSettings.OUTPUT_METHOD_XML);
			} else {
				System.err.println("Unrecognized serializer output method value: " + outputMethod);
			}
			serializerMap.remove(METHOD);
		}
		// normalization-form
		String normForm = (String) serializerMap.get(NORMALIZATION_FORM);
		if (normForm != null) {
			if(normForm.equalsIgnoreCase(NORMALIZATION_FORM_NONE)) {
				xss.setNormalizationForm(XDMSerializerSettings.NORMALIZATION_FORM_NONE);
			} else if (normForm.equalsIgnoreCase(NORMALIZATION_FORM_NFC)) {
				xss.setNormalizationForm(XDMSerializerSettings.NORMALIZATION_FORM_NFC);
			} else if (normForm.equalsIgnoreCase(NORMALIZATION_FORM_NFD)) {
				xss.setNormalizationForm(XDMSerializerSettings.NORMALIZATION_FORM_NFD);
			} else if (normForm.equalsIgnoreCase(NORMALIZATION_FORM_NFKC)) {
				xss.setNormalizationForm(XDMSerializerSettings.NORMALIZATION_FORM_NFKC);
			} else if (normForm.equalsIgnoreCase(NORMALIZATION_FORM_NFKD)) {
				xss.setNormalizationForm(XDMSerializerSettings.NORMALIZATION_FORM_NFKD);
			} else if (normForm.equalsIgnoreCase(NORMALIZATION_FORM_FULL)) {
				xss.setNormalizationForm(XDMSerializerSettings.NORMALIZATION_FORM_FULL);
			} else {
				System.err.println("Unrecognized serializer normalization-form value: " + normForm);
			}
			serializerMap.remove(NORMALIZATION_FORM);
		}
		// omit-xml-declaration
		if(serializerMap.containsKey(OMIT_XML_DECLARATION)) {
			xss.setOmitXMLDeclaration(new Boolean((String) serializerMap.get(OMIT_XML_DECLARATION)).booleanValue());
			serializerMap.remove(OMIT_XML_DECLARATION);
		}
		// standalone
		String standalone = (String) serializerMap.get(STANDALONE);
		if (standalone != null) {
			if (standalone.equalsIgnoreCase(STANDALONE_YES)) {
				xss.setStandAlone(XDMSerializerSettings.STANDALONE_YES);
			} else if (standalone.equalsIgnoreCase(STANDALONE_NO)) {
				xss.setStandAlone(XDMSerializerSettings.STANDALONE_NO);
			} else if (standalone.equalsIgnoreCase(STANDALONE_OMIT)) {
				xss.setStandAlone(XDMSerializerSettings.STANDALONE_OMIT);
			} else {
				System.err.println("Unrecognized serializer standalone value: " + standalone);
			}	
			serializerMap.remove(STANDALONE);
		}
		// undeclared-prefixes
		if(serializerMap.containsKey(UNDECLARED_PREFIXES)) {
			xss.setUndeclarePrefixes(new Boolean((String) serializerMap.get(UNDECLARED_PREFIXES)).booleanValue());
			serializerMap.remove(UNDECLARED_PREFIXES);
		}
		// use-character-maps
		String usecharactermaps = (String) serializerMap.get(USE_CHARACTER_MAPS);
		if(usecharactermaps != null) {
			String argString = (String) serializerMap.get(USE_CHARACTER_MAPS);
			String[] argArray = argString.split(";");
			HashMap charMap = new HashMap();
			for (int j=0; j<argArray.length; j++) {
				String argPair = argArray[j];
				if (argPair.matches("\\(.,.*\\)")) {
					String argPairContent = argPair.substring(1, argPair.length()-1);
					Character c = new Character(argPairContent.charAt(0));
					String str = argPairContent.substring(argPairContent.indexOf(",")+1);
					charMap.put(c, str);
				} else {
					System.err.println("Unrecognized serializer use-character-map value: " + argPair);
				}
			}	
			serializerMap.remove(USE_CHARACTER_MAPS);
		}
		// version
		if(serializerMap.containsKey(VERSION)) {
			xss.setVersion((String) serializerMap.get(VERSION));
			serializerMap.remove(VERSION);
		}
		
		// output the remaining properties, which could not be parsed
		if(!serializerMap.isEmpty()) {
			System.out.println("Following serializer parameter(s) could not be parsed:");
			System.out.println(serializerMap.toString());
		}
	}

	private static void processStaticContextOptions(CommandLine cl, Context ctx)
			throws MXQueryException {
		String[] compString = cl.getOptionValues("c");
		HashMap contextMap = getArguments(compString);
		
		// statically known namespaces
		if(contextMap.containsKey(STATICALLY_KNOWN_NAMESPACES)) {
			String argString = (String) contextMap.get(STATICALLY_KNOWN_NAMESPACES);
			String[] argArray = argString.split(";");
			for (int j=0; j<argArray.length; j++) {
				String argPair = argArray[j];
				if (argPair.matches("\\(.*,.*\\)")) {
					String argPairContent = argPair.substring(1, argPair.length()-1);
					String prefix =argPairContent.substring(0,argPairContent.indexOf(","));
					String uri = argPairContent.substring(argPairContent.indexOf(",")+1);
					ctx.addNamespace(prefix, uri);
				} else {
					System.err.println("Unrecognized context statically known namespaces value: " + argPair);
				}
			}
			contextMap.remove(STATICALLY_KNOWN_NAMESPACES);
		}
		
		// default element/type namespace
		if(contextMap.containsKey(DEFAULT_ELEMENTTYPE_NAMESPACE)) {
			ctx.setDefaultElementNamespace((String) contextMap.get(DEFAULT_ELEMENTTYPE_NAMESPACE));
		}
		
	
		// default function namespace
		if(contextMap.containsKey(DEFAULT_FUNCTION_NAMESPACE)) {
			ctx.setDefaultFunctionNamespace((String) contextMap.get(DEFAULT_FUNCTION_NAMESPACE));
		}
		
		// construction mode
		if(contextMap.containsKey(CONSTRUCTION_MODE)) {
			String consMode = (String) contextMap.get(CONSTRUCTION_MODE);
			if (consMode.equalsIgnoreCase(Context.PRESERVE)) {
				ctx.setConstructionMode(consMode);
			} else if (consMode.equalsIgnoreCase(Context.STRIP)) {
				ctx.setConstructionMode(consMode);
			} else {
				System.err.println("Unrecognized context construction mode value: " + consMode);
			}
		}
						
		// ordering mode
		if(contextMap.containsKey(ORDERING_MODE)) {
			String ordMode = (String) contextMap.get(ORDERING_MODE);
			if (ordMode.equalsIgnoreCase(Context.ORDERED)) {
				ctx.setOrderingMode(ordMode);
			} else if (ordMode.equalsIgnoreCase(Context.UNORDERED)) {
				ctx.setOrderingMode(ordMode);
			} else {
				System.err.println("Unrecognized context cordering mode value: " + ordMode);
			}
		}
						
		// default order for empty sequences
		if(contextMap.containsKey(DEFAULT_ORDER_FOR_EMPTY_SEQUENCES)) {
			String defOrder = (String) contextMap.get(DEFAULT_ORDER_FOR_EMPTY_SEQUENCES);
			if (defOrder.equalsIgnoreCase(Context.ORDER_GREATEST)) {
				ctx.setDefaultOrderEmptySequence(defOrder);
			} else if (defOrder.equalsIgnoreCase(Context.ORDER_LEAST)) {
				ctx.setDefaultOrderEmptySequence(defOrder);
			} else {
				System.err.println("Unrecognized context default order for empty sequences value: " + defOrder);
			}
		}
						
		// boundary-space policy
		if(contextMap.containsKey(BOUNDARY_SPACE_POLICY)) {
			String bSpace = (String) contextMap.get(BOUNDARY_SPACE_POLICY);
			if (bSpace.equalsIgnoreCase(Context.PRESERVE)) {
				ctx.setBoundarySpaceHandling(true);
			} else if (bSpace.equalsIgnoreCase(Context.STRIP)) {
				ctx.setBoundarySpaceHandling(false);
			} else {
				System.err.println("Unrecognized context boundary-space value: " + bSpace);
			}
			
		}
						
		// copy-namespace mode
		if(contextMap.containsKey(COPY_NAMESPACE_MODE)) {
			String copyNsMode = (String) contextMap.get(COPY_NAMESPACE_MODE);
			String[] copyNsModes = copyNsMode.split(",");
			if (copyNsModes.length == 2) {
				if ((copyNsModes[0].equalsIgnoreCase(Context.COPY_MODE_PRESERVE) || copyNsModes[1].equalsIgnoreCase(Context.COPY_MODE_PRESERVE)) && (copyNsModes[0].equalsIgnoreCase(Context.COPY_MODE_INHERIT) || copyNsModes[1].equalsIgnoreCase(Context.COPY_MODE_INHERIT))) {
					ctx.setCopyNamespacesMode(true, true);
				} else if ((copyNsModes[0].equalsIgnoreCase(Context.COPY_MODE_PRESERVE) || copyNsModes[1].equalsIgnoreCase(Context.COPY_MODE_PRESERVE)) && (copyNsModes[0].equalsIgnoreCase(Context.COPY_MODE_NO_INHERIT) || copyNsModes[1].equalsIgnoreCase(Context.COPY_MODE_NO_INHERIT))) {
					ctx.setCopyNamespacesMode(true, false);
				} else if ((copyNsModes[0].equalsIgnoreCase(Context.COPY_MODE_NO_PRESERVE) || copyNsModes[1].equalsIgnoreCase(Context.COPY_MODE_NO_PRESERVE)) && (copyNsModes[0].equalsIgnoreCase(Context.COPY_MODE_INHERIT) || copyNsModes[1].equalsIgnoreCase(Context.COPY_MODE_INHERIT))){
					ctx.setCopyNamespacesMode(false, true);
				} else if ((copyNsModes[0].equalsIgnoreCase(Context.COPY_MODE_NO_PRESERVE) || copyNsModes[1].equalsIgnoreCase(Context.COPY_MODE_NO_PRESERVE)) && (copyNsModes[0].equalsIgnoreCase(Context.COPY_MODE_NO_INHERIT) || copyNsModes[1].equalsIgnoreCase(Context.COPY_MODE_NO_INHERIT))) {
					ctx.setCopyNamespacesMode(false, false);
				}
			} else {
				System.err.println("Unrecognized context copy namespace mode value: " + copyNsMode);
			}
			contextMap.remove(COPY_NAMESPACE_MODE);
		}
						
		// base uri
		if(contextMap.containsKey(BASE_URI)) {
			ctx.setBaseURI((String) contextMap.get(BASE_URI));
			contextMap.remove(BASE_URI);
		}
	}

	private static void processExternalValues(CommandLine cl, Context ctx,
			Map extValues) throws Exception, MXQueryException {
		String errorMsg = "Incorrect format to set external variable: ";
		String[] compString = cl.getOptionValues("e");
		for (int j=0; j<compString.length; j++) {
			if (compString[j].indexOf(":=") >= 0) {
				String[] argValPair = compString[j].split(":=");
				if (argValPair.length != 2 )
					throw new Exception(errorMsg+compString[j]);
				QName varName;
				if (argValPair[0].equalsIgnoreCase("."))
					varName = Context.CONTEXT_ITEM;
				else 
					varName = new QName(argValPair[0].trim());
				XMLSource xmlIt = XDMInputFactory.createXMLInput(ctx, new java.io.StringReader(argValPair[1]), true, ctx.getInputValidationMode(), null);
				extValues.put(varName,xmlIt);
			}
			else if (compString[j].indexOf('=') >= 0) {
				String[] argValPair = compString[j].split("=");
				if (argValPair.length != 2 )
					throw new Exception(errorMsg+compString[j]);
				QName varName;
				if (argValPair[0].equalsIgnoreCase("."))
					varName = Context.CONTEXT_ITEM;
				else 
					varName = new QName(argValPair[0].trim());
				String uri = URIUtils.resolveURI(ctx.getBaseURI(),argValPair[1],QueryLocation.OUTSIDE_QUERY_LOC);
	
				Reader rd = IOLib.getInput(uri, QueryLocation.OUTSIDE_QUERY_LOC);
				XMLSource xmlIt = XDMInputFactory.createXMLInput(ctx, rd, true, ctx.getInputValidationMode(), null);
				extValues.put(varName,xmlIt);
	
			} else if (compString[j].trim().endsWith("-")) {
				String [] argValPair= compString[j].split("-");
				if (argValPair.length != 1 )
					throw new Exception(errorMsg+compString[j]);
				QName varName;
				if (argValPair[0].equalsIgnoreCase("."))
					varName = Context.CONTEXT_ITEM;
				else 
					varName = new QName(argValPair[0].trim());
				XMLSource xmlIt = XDMInputFactory.createXMLInput(ctx, new InputStreamReader(System.in), true, ctx.getInputValidationMode(), null);
				extValues.put(varName,xmlIt);					} else{
					throw new Exception(errorMsg+compString[j]);
				}
		}
	}

	private static HashMap getArguments(String[] arguments) {
		HashMap args = new HashMap();
		for (int j=0; j<arguments.length; j++) {
			//arguments[j] = arguments[j].toLowerCase();
			String[] argValPair = arguments[j].split("=", 2);
			if (argValPair.length==2)
				args.put(argValPair[0], argValPair[1]);
			else
				args.put(argValPair[0], "");
		}
		return args;
	}
	
	protected static void executeQuery(String query, Context ctx, TimeInfos timeInfos, LocalSetting los, XDMSerializerSettings xss, CompilerOptions cop, CommandLine cl) throws Exception {
		XQCompiler compiler;
		PreparedStatement statement = null;

		if (!los.isFromPlan()) {

			compiler = new CompilerImpl();
			try {
				if (timeInfos != null)
					timeInfos.setCompileStart(System.currentTimeMillis());
				statement = compiler.compile(ctx, query, cop);
			} catch (StaticException err) {
				StaticException.printErrorPosition(query, err.getLocation());
				System.err.println("Error:");
				throw err;
			} finally {
				if (timeInfos != null)
					timeInfos.setCompileEnd(System.currentTimeMillis());
			}
		} else {
			PlanLoader PL = new PlanLoader();
			File file = new File(query);

			if (file.exists()) {
				FileInputStream fs = null;
				XDMIterator result;
				try {
					fs= new FileInputStream(file);
					result = PL.processPlan(fs);
				} finally {
					if (fs != null)
						fs.close();
				}
				statement = new PreparedStatementImpl(result.getContext(),result,new CompilerOptions());
			} else {
				System.err.println("Sorry! File no found!!");
				System.exit(-1);
			}
		}
						
		if (timeInfos != null)
			timeInfos.setExecStart(System.currentTimeMillis());
		
		doQuery(query, statement, ctx, los, xss, cl);
	
		if (timeInfos != null)
			timeInfos.setExecEnd(System.currentTimeMillis());
	}

	protected static void doQuery(String query, PreparedStatement statement, Context ctx,LocalSetting los, XDMSerializerSettings xss, CommandLine cl) throws Exception {
			XDMIterator result = statement.evaluate();
			
			Map extValues = new HashMap();
			if (cl.hasOption("e")) {
				processExternalValues(cl, ctx, extValues);
			}
			
			java.util.Set vals = extValues.entrySet();
			java.util.Iterator valIt = vals.iterator();
			while(valIt.hasNext()) {
				Map.Entry mp = (Map.Entry)valIt.next();
				QName qn = (QName)mp.getKey();
				XDMIterator it = (XDMIterator)mp.getValue();
				if (qn.equals(Context.CONTEXT_ITEM))
					statement.setContextItem(it);
				else
					statement.addExternalResource(qn, it);
			}

			if (los.isExplain()) {
				System.out.println("############################ query plan ############################");
				KXmlSerializer serializer = new KXmlSerializer();
				serializer.setOutput(System.out, null);
				serializer.setFeature("http://xmlpull.org/v1/doc/features.html#indent-output", true);
				result.traverseIteratorTree(serializer);
				serializer.flush();
				System.out.println();
				System.out.println("############################ query plan ############################");
				System.out.println();
			}

			if (!los.isDiscardResult()) {
				try {
					OutputStream out = System.out;
					if (los.isToFile()) 
						out = new BufferedOutputStream(new FileOutputStream(los.getOutput()));
					XDMSerializer ip = new XDMSerializer(xss);

					if (xss.isIndent())
						out = new XPPOutputStream(out);
					PrintStream stream;
					try {
						stream = new PrintStream(out,false,xss.getEncoding());
					} catch (UnsupportedEncodingException e) {
						throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Serialization not possible",null);
					}
					ip.eventsToXML(stream, result);
					if (los.isToFile()) {
						out.flush();
						out.close();
					}
				} catch (MXQueryException e) {
					System.err.println("Error occured during evaluation:\nError code: "+e.getErrorCode()+"\nError message: "+e.getMessage()+"\n");
					MXQueryException.printErrorPosition(query, e.getLocation());
					e.printStackTrace();
				} catch (IOException e) {
				}
			}

			statement.applyPUL();
			if (los.isUpdateFiles())
				statement.serializeStores(los.isBackupBeforeUpdate());

			result.close(false);

			if (los.isPrintStores()) {
				System.out.println("############################ store ############################");
				System.out.println(((LLStoreSet) statement.getStores()).toString(true));
				System.out.println("############################ store ############################");
				System.out.println();
			}

			if (los.isSerializePlan()) {
				try {
					// System.out.println("############################ execution plan ############################");
					PrintStream out = new PrintStream(new FileOutputStream(los.getSerializationPlan()));
					out.println("<StaticContext>");
					Enumeration keys = result.getContext().getAllVariables().keys();
					// Enumeration usedKeys = runtime.getUsedVariablesKeys();
					if(keys.hasMoreElements()) {
						out.println(" <Variables>");
						while (keys.hasMoreElements()) {
							out.println(" <Variable>");
							out.println(" <Key>" +keys.nextElement()+
							"</Key>");
							out.println(" </Variable>");
						}
						out.println(" </Variables>");
					} else {
						out.println(" <Variables/>");
					}
	
	//				if(usedKeys.hasMoreElements()) {
	//				System.out.println(" <UsedVariables>");
	//				while (usedKeys.hasMoreElements()) {
	//				System.out.println(" <Variable>");
	//				System.out.println(" <Key>" +usedKeys.nextElement()+
	//				"</Key>");
	//				System.out.println(" </Variable>");
	//				}
	//				System.out.println(" </UsedVariables>");
	//				} else {
					out.println(" <UsedVariables/>");
	//				}
					out.println("</StaticContext>");
					out.println("<ParseTree>");
					KXmlSerializer serializer = new KXmlSerializer();
					serializer.setOutput(out, null);
					serializer
					.setFeature(
							"http://xmlpull.org/v1/doc/features.html#indent-output",
							true);
					result.traverseIteratorTree(serializer);
					serializer.flush();
					out.println();
					out.println("</ParseTree>");
	
					// System.out.println("############################ execution plan ############################");
				} catch (MXQueryException e) {
					System.err.println("Error occured during evaluation:\nError code: "+e.getErrorCode()+"\nError message: "+e.getMessage()+"\n");
					MXQueryException.printErrorPosition(query, e.getLocation());
					e.printStackTrace();
				} catch (IOException e) {
				}
			}
		}
		//Thread.sleep(60000);
}
