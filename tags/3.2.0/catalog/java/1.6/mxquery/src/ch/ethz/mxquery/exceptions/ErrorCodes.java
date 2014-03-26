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
package ch.ethz.mxquery.exceptions;

/**
 * All error code from the relevant XQuery-related standards
 * @author Matthias Braun
 * 
 * 
 *
 */
public class ErrorCodes {
	public final static String E0001_STATIC_NO_VALUE_ASSIGNED = "err:XPST0001";
	public final static String E0002_DYNAMIC_NO_VALUE_ASSIGNED = "err:XPDY0002";
	public final static String E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT = "err:XPST0003";
	public final static String E0004_TYPE_INAPPROPRIATE_TYPE = "err:XPTY0004";
	public final static String E0005_STATIC_EMPTY_SEQUENCE_TYPE = "err:XPST0005";
	public final static String E0008_STATIC_NAME_OR_PREFIX_NOT_DEFINED = "err:XPST0008";
	public final static String E0009_STATIC_SCHEMA_IMPORTS_NOT_SUPPORTED = "err:XPST0009";
	public final static String E0010_STATIC_AXIS_NOT_SUPPORTED = "err:XPST0010";
	public final static String E0012_STATIC_SCHEMA_IMPORTS_NOT_VALID = "err:XQST0012";
	public final static String E0013_STATIC_INVALID_PRAGMA_CONTENT = "err:XPST0013";
//	public final static String E0016_STATIC_MODULES_NOT_SUPPORTED = "err:XPST0016";
	public final static String E0017_STATIC_DOESNT_MATCH_FUNCTION_SIGNATURE = "err:XPST0017";
	public final static String E0018_TYPE_LAST_PATH_STEP_CONTAINS_NODES_AND_ATOMICS = "err:XPTY0018";
	public final static String E0019_TYPE_STEP_RESULT_IS_ATOMIC = "err:XPTY0019";
	public final static String E0020_TYPE_AXIS_STEP_CONTENT_IS_NOT_A_NODE = "err:XPTY0020";
	public final static String E0022_STATIC_NOT_A_VALID_URI = "err:XQST0022";
	public final static String E0024_TYPE_ATTRIBUTE_NODE_FOLLOWS_NONE_ATTRIBUTE_NODE = "err:XQTY0024";
	public final static String E0025_DYNAMIC_DUPLICATE_ATTRIBUTE_NAMES = "err:XQDY0025";
	public final static String E0026_DYNAMIC_COMPUTED_PI_INVALID_CONTENT = "err:XQDY0026";
	public final static String E0027_DYNAMIC_VALIDATE_UNEXPECTED_VALIDITY = "err:XQDY0027";
	public final static String E0030_TYPE_VALIDATE_MORE_THAN_ONE_ELEMENT = "err:XQTY0030";
	public final static String E0031_STATIC_VERSION_NOT_SUPPORTED = "err:XQST0031";
	public final static String E0032_STATIC_MORE_THAN_ONE_BASE_URI_DECL = "err:XQST0032";
	public final static String E0033_STATIC_MODULE_MULTIPLE_BINDINGS_FOR_SAME_PREFIX = "err:XQST0033";
	public final static String E0034_STATIC_MODULE_DUPLICATE_FUNCTION = "err:XQST0034";
	public final static String E0035_STATIC_SAME_SCHEMA = "err:XQST0035";
	public final static String E0036_STATIC_MODULE_UNDECLARED_SCHEMA_TYPE = "err:XQST0036";
	public final static String E0038_STATIC_MULTIPLE_DEFAULT_COLLATION_DECL_OR_STATICALLY_UNKNOWN = "err:XQST0038";
	public final static String E0039_STATIC_FUNCTION_DECL_SAME_PARAMETER_NAMES = "err:XQST0039";
	public final static String E0040_STATIC_DUPLICATE_ATTRIBUTE_NAMES = "err:XQST0040";
	public final static String E0041_DYNAMIC_NAME_EXPRESSION_NOT_CASTABLE_TO_NCNAME = "err:XQDY0041";
	public final static String E0044_DYNAMIC_NODE_NAME_OF_ATTRIBUTE_IS_XMLNS = "err:XQDY0044";
	public final static String E0045_STATIC_FUNCTION_NAME_IN_UNALLOWED_NAMESPACE = "err:XQST0045";
	public final static String E0046_STATIC_EMPTY_URI = "err:XQST0046";
	public final static String E0047_STATIC_MODULES_SPECIFY_SAME_NAMESPACE = "err:XQST0047";
	public final static String E0048_STATIC_FUNCTION_NOT_IN_LIBRARY_NAMESPACE = "err:XQST0048";
	public final static String E0049_STATIC_MODULE_DUPLICATE_VARIABLE_NAMES = "err:XQST0049";
	public final static String E0050_DYNAMIC_TREAT_OPERAND_NOT_A_SEQUENCE_TYPE = "err:XPDY0050";
	public final static String E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC = "err:XPST0051";
	public final static String E0054_STATIC_VARIABLE_DEPENDS_ON_ITSELF = "err:XQST0054";
	public final static String E0055_STATIC_PROLOG_MULTIPLE_COPY_NAMESPACES_DECL = "err:XQST0055";
	public final static String E0057_STATIC_SCHEMA_IMPORT_NS_PREFIX_WITHOUT_TARGET_NAMESPACE = "err:XQST0057";
	public final static String E0058_STATIC_SCHEMA_IMPORTS_SPECIFY_SAME_TARGET_NAMESPACE = "err:XQST0058";
	public final static String E0059_STATIC_UNABLE_TO_PROCESS_SCHEMA_OR_MODULE_IMPORT = "err:XQST0059";
	public final static String E0060_STATIC_FUNCTION_NAME_IS_NOT_A_NAMESPACE = "err:XQST0060";
	public final static String E0061_DYNAMIC_VALIDATE_MORE_THAN_ONE_ELEMENT = "err:XQDY0061";
	public final static String E0064_DYNAMIC_PI_NAME_EQUALS_XML = "err:XQDY0064";
	public final static String E0065_STATIC_PROLOG_CONTAINS_MULTIPLE_ORDERING_MODE_DECL = "err:XQST0065";
	public final static String E0066_STATIC_PROLOG_MULTIPLE_DEFAULT_NAMESPACE_DECL = "err:XQST0066";
	public final static String E0067_STATIC_PROLOG_MULTIPLE_CONSTRUCTION_DECL = "err:XQST0067";
	public final static String E0068_STATIC_PROLOG_MULTIPLE_BOUNDARY_SPACE_DECL = "err:XQST0068";
	public final static String E0069_STATIC_PROLOG_MULTIPLE_EMPTY_ORDER_DECL = "err:XQST0069";
	public final static String E0070_STATIC_INVALID_NAMESPACE_DECL_WITH_XML_OR_XMLNS = "err:XQST0070";
	public final static String E0071_STATIC_DUPLICATE_NAMESPACE_ATTRIBUTES = "err:XQST0071";
	public final static String E0072_DYNAMIC_CONTENT_WITHTWO_ADJACENT_HYPHENS_OR_ENDS_WITH_HYPHEN = "err:XQDY0072";
	public final static String E0073_STATIC_MODULE_IMPORTS_CYCLE = "err:XQST0073";
	public final static String E0074_DYNAMIC_NAME_EXPRESSION_CANNOT_BE_CONVERTED_TO_QNAME = "err:XQDY0074";
	public final static String E0075_STATIC_VALIDATION_NOT_SUPPORTED = "err:XQST0075";
	public final static String E0076_STATIC_FLWOR_ORDER_BY_UNKNOWN_COLLATION = "err:XQST0076";
	public final static String E0079_STATIC_EXTENSION_EXPRESSION_DOESNT_CONTAIN_PRAGMA_OR_CURLY_CONTENT = "err:XQST0079";
	public final static String E0080_STATIC_CAST_CASTABLE_TARGET_TYPE_IS_NOTATION_OR_ANYATOMIC = "err:XPST0080";
	public final static String E0081_STATIC_QUERY_UNKNOWN_NAMESPACE = "err:XPST0081";
	public final static String E0084_DYNAMIC_VALIDATE_STRICT_MISSING_TOP_LEVEL_DECL = "err:XQDY0084";
	public final static String E0085_STATIC_EMPTY_NAMESPACE_URI = "err:XQST0085";
	public final static String E0086_TYPE_COPIED_VALUE_IS_NAMESPACE_SENSITIVE_FORBIDDEN = "err:XQTY0086";
	public final static String E0087_STATIC_WRONG_ENCODING = "err:XQST0087";
	public final static String E0088_STATIC_MODULE_EMPTY_NAMESPACE_LITERAL = "err:XQST0088";
	public final static String E0089_STATIC_FLWOR_VARIABE_AND_POSITIONAL_VARIABLE_HAVE_SAME_NAME = "err:XQST0089";
	public final static String E0090_STATIC_CHARREF_INVALID_CHARACTER = "err:XQST0090";
	public final static String E0091_DYNAMIC_XML_ID_ERROR = "err:XQDY0091";
	public final static String E0092_DYNAMIC_XML_SPACE_DOESNT_HAVE_VALUE_PRESERVE_OR_DEFAULT = "err:XQDY0092";
	public final static String E0093_STATIC_MODULE_CYLCLE = "err:XQST0093";
	
	/* application defined Error Codes */
	public final static String A0001_EC_FILE_NOT_FOUND = "app:MXQE0001"; // File not found in filesystem
	public final static String A0002_EC_NOT_SUPPORTED= "app:MXQE0002"; // functinality not supported by the micro xquery engines
	public final static String A0003_EC_WSDL_IS_ERROR_MSG = "app:MXQE0003"; // WSDL response is an error message
	public final static String A0004_EC_WS_IS_ERROR_MSG = "app:MXQE0004"; // Web Service response is an error message
	public final static String A0005_EC_CAST_ERROR = "app:MXQE0005"; // Cast Error
	public final static String A0006_EC_URI_NOT_FOUND = "app:MXQE0006"; // URI NOT FOUND
	public final static String A0007_EC_IO = "app:MXQE0007"; // IO Exception
	public final static String A0008_EC_TWO_SIMILAR_IDENTIFIERS = "app:MXQE0008"; // Double: two similar identifiers generated
	public final static String A0009_EC_EVALUATION_NOT_POSSIBLE = "app:MXQE0009"; // general exception: evaluation not possible
	public final static String A0010_EC_LLSTORE_EXCEPTION = "app:MXQE0010"; // LLStore exception
	public final static String A0011_EC_NO_SIDE_EFFECTS_ALLOWED = "app:MXQE0011"; // no side effects allowed
	public final static String A0012_EC_XQUERYP_EXCEPTION = "app:MXQE0012"; // XQueryP Exception
	public final static String A0013_Invalid_SOAP_request =  "err:XQDY0100";
	public final static String A0014_Unspecified_Service_Name =  "err:XQST0096";
	public final static String A0015_Unspecified_Endpoint =  "err:XQST0097";
	public final static String A0016_Endpoint_Does_Not_Exist =  "err:XQST0098";

	/* Error codes from Update Facility, version 28.08.2007 */
	public final static String U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE = "err:XUST0001";
	public final static String U0002_UPDATE_STATIC_NONUPDATING_EXPRESSION_NOT_ALLOWED_HERE = "err:XUST0002";
	public final static String U0003_UPDATE_STATIC_PROLOG_MULTIPLE_REVALIDATE_DECL = "err:XUST0003";
	public final static String U0004_UPDATE_TYPE_ATTRIBUTE_NOT_ALLOWED_HERE = "err:XUTY0004";
	public final static String U0005_UPDATE_TYPE_SINGLE_ELEM_DOC_EXPECTED = "err:XUTY0005";
	public final static String U0006_UPDATE_TYPE_INSERT_SINGLE_ELEM_TEXT_CO_PI_EXPECTED = "err:XUTY0006";
	public final static String U0007_UPDATE_TYPE_ZERO_OR_MORE_NODES_EXPECTED = "err:XUTY0007";
	public final static String U0008_UPDATE_TYPE_REPLACE_SINGLE_ELEM_TEXT_CO_PI_EXPECTED = "err:XUTY0008";
	public final static String U0009_UPDATE_DYNAMIC_REPLACE_NO_PARENT = "err:XUDY0009";
	public final static String U0010_UPDATE_TYPE_REPLACE_ELEM_TEXT_CO_PI_EXPECTED = "err:XUTY0010";
	public final static String U0011_UPDATE_TYPE_REPLACE_ATTRIBUTE_EXPECTED = "err:XUTY0011";
	public final static String U0012_UPDATE_TYPE_RENAME_SINGLE_ELEM_ATTR_PI_EXPECTED = "err:XUTY0012";
	public final static String U0013_UPDATE_TYPE_TRANSFORM_SOURCE_SINGLE_NODE_EXPECTED = "err:XUTY0013";
	public final static String U0014_UPDATE_DYNAMIC_TRANSFORM_WRONG_VARIABLE_MODIFIED = "err:XUDY0014";
	public final static String U0015_UPDATE_DYNAMIC_RENAME_MULTIPLE_ON_SAME_NODE = "err:XUDY0015";
	public final static String U0016_UPDATE_DYNAMIC_REPLACE_MULTIPLE_ON_SAME_NODE = "err:XUDY0016";
	public final static String U0017_UPDATE_DYNAMIC_REPLACE_VALUE_MULTIPLE_ON_SAME_NODE = "err:XUDY0017";
	public final static String U0018_UPDATE_DYNAMIC_FUNCTION_NONUPDATING_UPDATE = "err:XUDY0018";
	public final static String U0019_UPDATE_DYNAMIC_FUNCTION_UPDATING_RESULT = "err:XUDY0019";
	public final static String U0020_UPDATE_DYNAMIC_DELETE_NO_PARENT = "err:XUDY0020";
	public final static String U0021_UPDATE_DYNAMIC_INVALID_XDM = "err:XUDY0021";
	public final static String U0022_UPDATE_TYPE_INSERT_ATTRIBUTE_DOC = "err:XUTY0022";
	public final static String U0023_UPDATE_DYNAMIC_NEW_NAMESPACE_CONFLICT = "err:XUDY0023";
	public final static String U0024_UPDATE_DYNAMIC_NEW_NAMESPACE_ATTRIBUTE_CONFLICT = "err:XUDY0024";
	public final static String U0025_UPDATE_DYNAMIC_RENAME_PI_WRONG_QNAME = "err:XUDY0025";
	public final static String U0026_UPDATE_STATIC_UNSUPPORTED_REVALIDATION_MODE = "err:XUST0026";
	public final static String U0027_UPDATE_DYNAMIC_TARGET_EMPTY = "err:XUDY0027";
	public final static String U0028_UPDATE_STATIC_FUNCTION_UPDATING_RETURNTYPE = "err:XUST0028";
	public final static String U0029_UPDATE_DYNAMIC_INSERT_BEFORE_AFTER_NO_PARENT = "err:XUDY0029";
	public final static String U0030_UPDATE_DYNAMIC_INSERT_BEFORE_AFTER_ATTRIBUTE_DOC = "err:XUDY0030";
	public final static String U0031_UPDATE_DYNAMIC_MULTIPLE_PUT_SAME_URI = "err:XUDY0031";

	public final static String UF0001_UPDATE_FUNCTION_UNSUPPORTED_NODE = "err:FOUP0001";
	public final static String UF0002_UPDATE_FUNCTION_INVALID_TARGET_URI = "err:FOUP0002"; 
	
	/* Scripting error codes */
	public final static String P0001_DYNAMIC_ERROR_IN_SCRIPTING = "err:XSDY0001";
	public final static String P0002_STATIC_ERROR_IN_SCRIPTING = "err:XSSY0001";
	
	public final static String F0001_UNIDENTIFIED_ERROR = "err:FOER0000";
	public final static String F0002_DIVISION_BY_ZERO = "err:FOAR0001";
	public final static String F0003_OVERFLOW_UNDERFLOW_NUMERIC = "err:FOAR0002";
	public final static String F0004_INPUT_VALUE_TOO_LARGE_FOR_DECIMAL = "err:FOCA0001";
	public final static String F0005_INVALID_LEXICAL_VALUE = "err:FOCA0002";
	public final static String F0006_INPUT_VALUE_TOO_LARGE_FOR_INTEGER = "err:FOCA0003";
	public final static String F0007_NAN = "err:FOCA0005";
	public final static String F0008_TOO_MANY_DIGITS_OF_PRECIsION = "err:FOCA0006";
	public final static String F0009_CODE_POINT_NOT_VALID = "err:FOCH0001";
	public final static String F0010_UNSUPPORTED_COLLATION = "err:FOCH0002";
	public final static String F0011_UNSUPPORTED_NORMALIZATION_FORM = "err:FOCH0003";
	public final static String F0012_COLLATION_DOESNT_SUPPORT_UNITS = "err:FOCH0004";
	public final static String F0013_NO_CONTEXT_DOCUMENT = "err:FODC0001";
	public final static String F0014_ERROR_RETRIEVING_RESOURCE = "err:FODC0002";
	public final static String F0015_FUNCTION_STABILITY_UNDEFINED = "err:FODC0003";
	public final static String F0016_INVALID_ARGUMENT_TO_FN_COLLECTION = "err:FODC0004";
	public final static String F0017_INVALID_ARGUMENT_TO_FN_DOC = "err:FODC0005";
	public final static String F0018_OVERFLOW_UNDERFLOW_DATE_TIME = "err:FODT0001";
	public final static String F0019_OVERFLOW_UNDERFLOW_DURATION = "err:FODT0002";
	public final static String F0020_INVALID_TIMEZONE_VALUE = "err:FODT0003";
	public final static String F0021_NO_NAMESPACE_FOUND = "err:FONS0004";
	public final static String F0022_BASEURI_UNDEFINED = "err:FONS0005";
	public final static String F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR = "err:FORG0001";
	public final static String F0024_INVALID_ARGUMENT_TO_FN_RESOLVEURI = "err:FORG0002";
	public final static String F0025_ZERO_OR_ONE_WITH_MULTIITEM_SEQUENCE = "err:FORG0003";
	public final static String F0026_ONE_OR_MORE_WITH_EMPTY_SEQUENCE = "err:FORG0004";
	public final static String F0027_EXACTLY_ONE_WITH_ZERO_OR_MULTI = "err:FORG0005";
	public final static String F0028_INVALID_ARGUMENT_TYPE = "err:FORG0006";
	public final static String F0029_BOTH_ARGUMENTS_TO_DATETIME_HAVE_SPECIFIC_TIMEZONE = "err:FORG0008";
	public final static String F0030_ERROR_RESOLVING_RELATIVE_URI = "err:FORG0009";
	public final static String F0031_INVALID_REGULAR_EXPRESSION_FLAGS = "err:FORX0001";
	public final static String F0032_INVALID_REGULAR_EXPRESSION = "err:FORX0002";
	public final static String F0033_REGULAR_EXPRESSION_MATCHES_EMPTY = "err:FORX0003";
	public final static String F0034_INVALID_REPLACEMENT_STRING = "err:FORX0004";
	public final static String F0035_AGUMENT_NOD_NO_TYPED_VALUE = "err:FOTY0012";
	
	// Serializer error codes
	public final static String S0001_ATTRIBUTE_OR_NAMESPACE_NOT_ALLOWED_HERE = "err:SENR0001";
	public final static String S0003_WELLFORMED_XML_NOT_POSSIBLE = "err:err:SERE0003";
	public final static String S0004_DOCTYPE_MULTIPLE_TEXT_ELEM_CHILDS = "err:err:SEPM0004";
	public final static String S0005_INVALID_NCNAME_CHAR= "err:SERE0005";
	public final static String S0006_INVALID_TEXT_CHAR = "err:SERE0006";
	public final static String S0007_UNSUPPORTED_ENCODING = "err:SESU0007";
	public final static String S0008_CANNOT_SERIALIZE_CHARACTER = "err:SERE0008";
	public final static String S0009_OMIT_XML_BUT_DOCDECL = "err:SEPM0009";
	public final static String S0010_XML_10_UNDECLARE_NAMESPACE = "err:SEPM0010";
	public final static String S0011_UNSUPPORTED_NORMALIZATION_FORM = "err:SESU0011";
	public final static String S0012_FULLY_NORMALIZED_COMB_CHAR = "err:SERE0012";
	public final static String S0013_UNSUPPORTED_XML_HTML_VERSION = "err:SESU0013";
	public final static String S0014_ILLEGAL_HTML_CHARACTERS = "err:SERE0014";
	public final static String S0015_HTML_PI_CONTAINS_SMALLER = "err:SERE0015";
	public final static String S0016_INVALID_SERIALIZATION_PARAMTER = "err:SEPM0016";
	
	// full text error codes
	public final static String FTST001_FTMildNotOperator_NOT_SUPPORTED = "err:FTST0001";
	public final static String FTST002_FTUnaryNotOperator_RESTRICTION_NOT_OBEYED = "err:FTST0002";
	public final static String FTST003_FTUnit_CHOICE_NOT_SUPPORTED = "err:FTST0003";
	public final static String FTST004_FTScopeOperator_NOT_SUPPORTED = "err:FTST0004";
	public final static String FTST005_FTTimesOperator_NOT_SUPPORTED = "err:FTST0005";
	public final static String FTST006_FTStopWordOption_RESTRICTION_NOT_OBEYED = "err:FTST0006";
	public final static String FTST007_FTIgnoreOption_RESTRICTION_NOT_OBEYED = "err:FTST0007";
	public final static String FTST008_StopWordList_NOT_FOUND = "err:FTST0008";
	public final static String FTST009_LANGUAGE_NOT_SUPPORTED = "err:FTST0009";
	public final static String FTST0010_FTOrder_RESTRICTION_NOT_OBEYED = "err:FTST00010";
	public final static String FTST0011_FTWindow_OR_FTDistance_RESTRICITION_NOT_OBEYED = "err:FTST00011";
	public final static String FTST0012_FTContentOperator_NOT_SUPPORTED = "err:FTST00012";
	public final static String FTST0013_FTLanguageOption_SINGLE_RESTRICTION_NOT_OBEYED = "err:FTST00013";
	public final static String FTST0014_SCORING_EXPRESSION_DOES_NOT_MEET_RESTRICTION = "err:FTST00014";
	public final static String FTST0015_FTCaseOption_RESTRICTION_VIOLATED = "err:FTST00015";
	public final static String FTDY0016_NEGATIVE_WEIGHTS_NOT_SUPPORTED = "err:FTDY00016";
	public final static String FTDY0017_FTMildNotSelection_ENCOUNTERED = "err:FTDY00017";
	public final static String FTST0018_UNDEFINED_THESAURUS = "err:FTST0018";
	public final static String FTST0019_CONFLICTING_MATCH_OPTIONS = "err:FTST0019";
	public final static String FOCH0002_ARGUMENT_DOES_NOT_IDENTIFY_SUPPORTED_COLLATION = "err:FOCH0002";
	
	
	
	public static boolean isNotSupported(String errorCode) {
		String[] notSup = new String[] {
				// not supported
				A0002_EC_NOT_SUPPORTED,
				U0026_UPDATE_STATIC_UNSUPPORTED_REVALIDATION_MODE,
				E0031_STATIC_VERSION_NOT_SUPPORTED,
				E0010_STATIC_AXIS_NOT_SUPPORTED,
				FTST001_FTMildNotOperator_NOT_SUPPORTED,
				FTST002_FTUnaryNotOperator_RESTRICTION_NOT_OBEYED,
				FTST003_FTUnit_CHOICE_NOT_SUPPORTED,
				FTST004_FTScopeOperator_NOT_SUPPORTED,
				FTST005_FTTimesOperator_NOT_SUPPORTED,
				FTST006_FTStopWordOption_RESTRICTION_NOT_OBEYED,
				FTST007_FTIgnoreOption_RESTRICTION_NOT_OBEYED,
				FTST009_LANGUAGE_NOT_SUPPORTED,
				FTST0010_FTOrder_RESTRICTION_NOT_OBEYED,
				FTST0011_FTWindow_OR_FTDistance_RESTRICITION_NOT_OBEYED,
				FTST0012_FTContentOperator_NOT_SUPPORTED,
				
		};
		
		for (int i=0; i<notSup.length; i++) {
			if (notSup[i].equals(errorCode)) {
				return true;
			}
		}
		
		return false;
	}
}
