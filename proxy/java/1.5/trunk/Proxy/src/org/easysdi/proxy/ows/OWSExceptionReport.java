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

public interface OWSExceptionReport {

	public static final String CODE_OPERATION_NOT_SUPPORTED 		= "OperationNotSupported";
	public static final String CODE_MISSING_PARAMETER_VALUE 		= "MissingParameterValue";
	public static final String CODE_INVALID_PARAMETER_VALUE 		= "InvalidParameterValue";
	public static final String CODE_VERSION_NEGOTIATION_FAILED 		= "VersionNegotiationFailed";
	public static final String CODE_INVALID_UPDATE_SEQUENCE 		= "InvalidUpdateSequence";
	public static final String CODE_OPTION_NOT_SUPPORTED 			= "OptionNotSupported";
	public static final String CODE_NO_APPLICABLE_CODE 				= "NoApplicableCode";
	
	//WMS
	public static final String CODE_INVALID_FORMAT					= "InvalidFormat";
	public static final String CODE_INVALID_CRS 					= "InvalidCRS";
	public static final String CODE_INVALID_SRS 					= "InvalidSRS";
	public static final String CODE_LAYER_NOT_DEFINED				= "LayerNotDefined";
	public static final String CODE_STYLE_NOT_DEFINED				= "StyleNotDefine";
	public static final String CODE_LAYER_NOT_QUERYABLE				= "LayerNotQueryable";
	public static final String CODE_INVALID_POINT					= "InvalidPoint";
	public static final String CODE_CURRENT_UPDATE_SEQUENCE			= "CurrentUpdateSequence";
	public static final String CODE_MISSING_DIMENSION_VALUE			= "MissingDimensionValue";
	public static final String CODE_INVALID_DIMENSION_VALUE			= "InvalidDimensionValue";
	
	public static final String TEXT_OPERATION_NOT_SUPPORTED 		= "Request is for an operation that is not supported by the server.";	
	public static final String TEXT_OPERATION_NOT_ALLOWED			= "Request is for an operation that is not allowed by this server security options.";
	public static final String TEXT_VERSION_NOT_SUPPORTED			= "Version not supported.";
	public static final String TEXT_INVALID_LAYER_NAME 				= "Invalid layer name given in the LAYER parameter : ";
	public static final String TEXT_INVALID_SERVICE_NAME 			= "Invalid service name given in the SERVICE parameter.";
	public static final String TEXT_ERROR_IN_EASYSDI_PROXY 			= "Error in EasySDI Proxy. Consult the proxy log for more details.";
	public static final String TEXT_NO_RESULT_RECEIVED_BY_PROXY		= "Error in EasySDI Proxy. EasySDI Proxy didn't receive any result from remote server.";
	public static final String TEXT_VERSION_NEGOCIATION_FAILED		= "Remote servers did not respond with the same WMS protocol version : version number negociation in multi server context failed.";
	
	public static final String TEXT_MISSING_PARAMETER_VALUE			= "Parameter is missing.";
	public static final String TEXT_INVALID_FORMAT					= "Request contains a format not offered by the server.";
	public static final String TEXT_INVALID_CRS 					= "Request contains a CRS not offered by the server for one or more of the layers.";
	public static final String TEXT_INVALID_SRS 					= "Request contains a SRS not offered by the service instance for one or more of the layers.";
	public static final String TEXT_LAYER_NOT_DEFINED				= "At least one of the requested layers is not defined.";
	public static final String TEXT_STYLE_NOT_DEFINED				= "Request is for a layer in a Style not offered by the server.";
	public static final String TEXT_LAYER_NOT_QUERYABLE				= "GetFeatureInfo request is applied to a Layer which is not declared queryable.";
	public static final String TEXT_INVALID_POINT					= "GetFeatureInfo request contains invalid I or J value.";
	public static final String TEXT_CURRENT_UPDATE_SEQUENCE			= "Value of UpdateSequence parameter is equal to current value of service metadata update sequence number.";
	public static final String TEXT_MISSING_DIMENSION_VALUE			= "Request does not include a sample dimension value, and the server did not declare a default value for that dimension.";
	public static final String TEXT_INVALID_DIMENSION_VALUE			= "Request contains an invalid sample dimension value.";
	
	public StringBuffer generateExceptionReport (String errorMessage, String code, String locator) throws IOException;
	
}
