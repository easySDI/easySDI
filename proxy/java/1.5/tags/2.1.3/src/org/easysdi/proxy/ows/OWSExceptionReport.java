package org.easysdi.proxy.ows;

import java.io.ByteArrayOutputStream;
import java.io.IOException;

public interface OWSExceptionReport {

	public static final String CODE_OPERATION_NOT_SUPPORTED 		= "OperationNotSupported";
	public static final String CODE_MISSING_PARAMETER_VALUE 		= "MissingParameterValue";
	public static final String CODE_INVALID_PARAMETER_VALUE 		= "InvalidParameterValue";
	public static final String CODE_VERSION_NEGOTIATION_FAILED 		= "VersionNegotiationFailed";
	public static final String CODE_INVALID_UPDATE_SEQUENCE 		= "InvalidUpdateSequence";
	public static final String CODE_OPTION_NOT_SUPPORTED 			= "OptionNotSupported";
	public static final String CODE_NO_APPLICABLE_CODE 				= "NoApplicableCode";
	
	public static final String TEXT_VERSION_NOT_SUPPORTED			= "Version not supported.";
	public static final String TEXT_INVALID_LAYER_NAME 				= "Invalid layer name given in the LAYER parameter : ";
	public static final String TEXT_INVALID_SERVICE_NAME 			= "Invalid service name given in the SERVICE parameter.";
	public static final String TEXT_OPERATION_NOT_ALLOWED 			= "Operation not allowed.";
	public static final String TEXT_ERROR_IN_EASYSDI_PROXY 			= "Error in EasySDI Proxy. Consult the proxy log for more details.";
	
	public ByteArrayOutputStream generateExceptionReport (String errorMessage, String code, String locator) throws IOException;
}
