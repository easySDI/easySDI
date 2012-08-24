/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */
package org.easysdi.proxy.ows;

import java.io.IOException;

public interface OWSIExceptionReport {

    public static final String HTTP_CODE_400								= "HTTP 400 - Bad request.";
    public static final String HTTP_CODE_401								= "HTTP 401 - Unauthorized.";
    public static final String HTTP_CODE_402								= "HTTP 402 - Payment Required.";
    public static final String HTTP_CODE_403								= "HTTP 403 - Forbidden.";
    public static final String HTTP_CODE_404								= "HTTP 404 - Not Found.";
    public static final String HTTP_CODE_405								= "HTTP 405 - Method Not Allowed.";
    public static final String HTTP_CODE_406								= "HTTP 406 - Not Acceptable.";
    public static final String HTTP_CODE_407								= "HTTP 407 - Proxy Authentication Required.";
    public static final String HTTP_CODE_408								= "HTTP 408 - Request Time-out.";
    public static final String HTTP_CODE_409								= "HTTP 409 - Conflict.";
    public static final String HTTP_CODE_410								= "HTTP 410 - Gone.";
    public static final String HTTP_CODE_411								= "HTTP 411 - Length Required.";
    public static final String HTTP_CODE_412								= "HTTP 412 - Precondition Failed.";
    public static final String HTTP_CODE_413								= "HTTP 413 - Request Entity Too Large.";
    public static final String HTTP_CODE_414								= "HTTP 414 - Request-URI Too Long.";
    public static final String HTTP_CODE_415								= "HTTP 415 - Unsupported Media Type.";
    public static final String HTTP_CODE_416								= "HTTP 416 - Requested range unsatisfiable.";
    public static final String HTTP_CODE_417								= "HTTP 417 - Expectation failed.";
    public static final String HTTP_CODE_418								= "HTTP 418 - I’m a teapot.";
    public static final String HTTP_CODE_422								= "HTTP 422 - Unprocessable entity.";
    public static final String HTTP_CODE_423								= "HTTP 423 - Locked.";
    public static final String HTTP_CODE_424								= "HTTP 424 - Method failure.";
    public static final String HTTP_CODE_425								= "HTTP 425 - Unordered Collection.";
    public static final String HTTP_CODE_426								= "HTTP 426 - Upgrade Required.";
    public static final String HTTP_CODE_449								= "HTTP 449 - Retry With.";
    public static final String HTTP_CODE_450								= "HTTP 450 - Blocked by Windows Parental Controls.";
    public static final String HTTP_CODE_500								= "HTTP 500 - Internal Server Error.";
    public static final String HTTP_CODE_501								= "HTTP 501 - Not implemented.";
    public static final String HTTP_CODE_502								= "HTTP 502 - Bad Gateway.";
    public static final String HTTP_CODE_503								= "HTTP 503 - Service Unavailable.";
    public static final String HTTP_CODE_504								= "HTTP 504 - Gateway Time-out.";
    public static final String HTTP_CODE_505								= "HTTP 505 - HTTP Version not supported.";
    public static final String HTTP_CODE_507								= "HTTP 507 - Insufficient storage.";
    public static final String HTTP_CODE_509								= "HTTP 509 - Bandwidth Limit Exceeded.";
    
    public static final String CODE_OPERATION_NOT_SUPPORTED 				= "OperationNotSupported";
    public static final String CODE_OPERATION_NOT_ALLOWED 					= "OperationNotAllowed";
    public static final String CODE_MISSING_PARAMETER_VALUE 				= "MissingParameterValue";
    public static final String CODE_INVALID_PARAMETER_VALUE 				= "InvalidParameterValue";
    public static final String CODE_VERSION_NEGOTIATION_FAILED 				= "VersionNegotiationFailed";
    public static final String CODE_MULTISERVER_VERSION_NEGOTIATION_FAILED 	= "MultiServerVersionNegotiationFailed";
    public static final String CODE_INVALID_UPDATE_SEQUENCE 				= "InvalidUpdateSequence";
    public static final String CODE_OPTION_NOT_SUPPORTED 					= "OptionNotSupported";
    public static final String CODE_NO_APPLICABLE_CODE 						= "NoApplicableCode";

    public static final String CODE_INVALID_FORMAT							= "InvalidFormat";
    public static final String CODE_INVALID_CRS 							= "InvalidCRS";
    public static final String CODE_INVALID_SRS 							= "InvalidSRS";
    public static final String CODE_LAYER_NOT_DEFINED						= "LayerNotDefined";
    public static final String CODE_STYLE_NOT_DEFINED						= "StyleNotDefine";
    public static final String CODE_LAYER_NOT_QUERYABLE						= "LayerNotQueryable";
    public static final String CODE_INVALID_POINT							= "InvalidPoint";
    public static final String CODE_CURRENT_UPDATE_SEQUENCE					= "CurrentUpdateSequence";
    public static final String CODE_MISSING_DIMENSION_VALUE					= "MissingDimensionValue";
    public static final String CODE_INVALID_DIMENSION_VALUE					= "InvalidDimensionValue";

    public static final String TEXT_OPERATION_NOT_SUPPORTED 				= "Request is for an operation that is not supported by the server.";	
    public static final String TEXT_OPERATION_NOT_ALLOWED					= "Request is for an operation that is not allowed by this server security options.";
    public static final String TEXT_VERSION_NOT_SUPPORTED					= "Version not supported.";
    public static final String TEXT_INVALID_LAYER_NAME 						= "Invalid layer name given in the LAYER parameter : ";
    public static final String TEXT_INVALID_QUERY_LAYERS_NAME 				= "Invalid layer name given in the QUERY_LAYERs parameter : ";
    public static final String TEXT_INVALID_LAYERS_NAME 					= "Invalid layer name given in the LAYERS parameter : ";
    public static final String TEXT_INVALID_SERVICE_NAME 					= "Invalid service name given in the SERVICE parameter.";
    public static final String TEXT_ERROR_IN_EASYSDI_PROXY 					= "Error in EasySDI Proxy. Consult the proxy log for more details.";
    public static final String TEXT_NO_RESULT_RECEIVED_BY_PROXY				= "Error in EasySDI Proxy. EasySDI Proxy didn't receive any result from remote server.";
    public static final String TEXT_MULTISERVER_VERSION_NEGOCIATION_FAILED	= "Remote servers did not respond with the same WMS protocol version : version number negociation in multi server context failed.";
    public static final String TEXT_INVALID_PARAMETER_VALUE 				= "Invalid parameter value given in the request.";

    public static final String TEXT_MISSING_PARAMETER_VALUE					= "Parameter is missing.";
    public static final String TEXT_INVALID_FORMAT							= "Request contains a format not offered by the server.";
    public static final String TEXT_INVALID_CRS 							= "Request contains a CRS not offered by the server for one or more of the layers.";
    public static final String TEXT_INVALID_SRS 							= "Request contains a SRS not offered by the service instance for one or more of the layers.";
    public static final String TEXT_LAYER_NOT_DEFINED						= "At least one of the requested layers is not defined.";
    public static final String TEXT_STYLE_NOT_DEFINED						= "Request is for a layer in a Style not offered by the server.";
    public static final String TEXT_LAYER_NOT_QUERYABLE						= "GetFeatureInfo request is applied to a Layer which is not declared queryable.";
    public static final String TEXT_INVALID_POINT							= "GetFeatureInfo request contains invalid I or J value.";
    public static final String TEXT_CURRENT_UPDATE_SEQUENCE					= "Value of UpdateSequence parameter is equal to current value of service metadata update sequence number.";
    public static final String TEXT_MISSING_DIMENSION_VALUE					= "Request does not include a sample dimension value, and the server did not declare a default value for that dimension.";
    public static final String TEXT_INVALID_DIMENSION_VALUE					= "Request contains an invalid sample dimension value.";

    public static final String TEXT_EXCEPTION_ERROR							= "Error sending exception.";

    public static final String TEXT_ERROR_HHTP_401_UNAUTHORIZED				= "Error sending exception.";

    public StringBuffer generateExceptionReport (String errorMessage, String code, String locator) throws IOException;
    
    public  String getHttpCodeDescription(String code);
   
}
