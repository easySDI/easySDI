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

package ch.ethz.mxquery.xdmio;


import java.util.Hashtable;

import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.Set;

/**
 * Serializer Setting, following the specification in http://www.w3.org/TR/xslt-xquery-serialization/
 * @author Peter Fischer
 *	
 */
public class XDMSerializerSettings {
	public static final int OUTPUT_METHOD_XML = 0;
	public static final int OUTPUT_METHOD_XHMTL = 1;
	public static final int OUTPUT_METHOD_HMTL = 2;
	public static final int OUTPUT_METHOD_TEXT = 3;
	
	public static final int STANDALONE_OMIT = 0;
	public static final int STANDALONE_YES = 1;
	public static final int STANDALONE_NO = 2;
	
	public static final int NORMALIZATION_FORM_NONE = 0;
	public static final int NORMALIZATION_FORM_NFC = 1;
	public static final int NORMALIZATION_FORM_NFD = 2;
	public static final int NORMALIZATION_FORM_NFKC = 3;
	public static final int NORMALIZATION_FORM_NFKD = 4;
	public static final int NORMALIZATION_FORM_FULL = 5;
	
	private boolean printStandaloneAttributes;
	private boolean byteOrderMark;
	private Set cdataSectionElements;
	private String doctypeSystem;
	private String doctypePublic;
	private String rootElem;
	private String encoding;
	private boolean escapeURIAttributes;
	private boolean includeContentType;
	private boolean indent;
	private String mediaType;
	private int outputMethod;
	private int normalizationForm;
	private boolean omitXMLDeclaration;
	private int standAlone;
	private boolean undeclarePrefixes;
	private Hashtable characterMaps;
	private String version;

	/**
	 * Creates a serializer settings object with a default XML Output model 
	 */
	public XDMSerializerSettings() {
		this.outputMethod = OUTPUT_METHOD_XML;
		this.version = "1.0";
		this.encoding = "UTF-8";
		this.printStandaloneAttributes = false;
	}
	/**
	 * Creates a  serializer settings object with the default setting for the given output method
	 * @param outputMethod
	 */
	public XDMSerializerSettings(int outputMethod) throws MXQueryException {
		this.outputMethod = outputMethod;
		this.encoding = "UTF-8";
		this.printStandaloneAttributes = false;
		switch (outputMethod) {
		case OUTPUT_METHOD_XHMTL:
		case OUTPUT_METHOD_XML:
			this.version = "1.0";
			break;
		case OUTPUT_METHOD_HMTL:
			this.version = "4.01";
			break;
		case OUTPUT_METHOD_TEXT:
			break;
		default: 
			throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"Unsupported Serialization Mode Requested",null);
		}
	}
	/**
	 * Shall a byte order mark be generated
	 * @return true yes, false no
	 */
	public boolean isByteOrderMark() {
		return byteOrderMark;
	}
	/**
	 * Set the generation of a byte order mark
	 * @param byteOrderMark
	 */
	public void setByteOrderMark(boolean byteOrderMark) {
		this.byteOrderMark = byteOrderMark;
	}
	/**
	 * Get the set of element names whose text contents should be serialized 
	 * as CDATA, not as regular strings 
	 * @return a Set of element names
	 */
	public Set getCdataSectionElements() {
		return cdataSectionElements;
	}
	/**
	 * Provide the set of element names whose text contents should be serialized 
	 * as CDATA, not as regular strings 
	 * @param cdataSectionElements a Set of element names
	 */
	public void setCdataSectionElements(Set cdataSectionElements) {
		this.cdataSectionElements = cdataSectionElements;
	}
	/**
	 * Get the system id of a doctype declaration
	 * @return the System ID of a doctype declaration
	 */
	public String getDoctypeSystem() {
		return doctypeSystem;
	}
	/**
	 * Set the system id of a doctype declaration
	 * @param doctypeSystem the System ID of a doctype declaration
	 */
	public void setDoctypeSystem(String doctypeSystem) {
		this.doctypeSystem = doctypeSystem;
	}
	/**
	 * Get the public id of a doctype declaration
	 * @return the Public ID of a doctype declaration
	 */
	public String getDoctypePublic() {
		return doctypePublic;
	}
	/**
	 * Set the public id of a doctype declaration
	 * @param doctypePublic the Public ID of a doctype declaration
	 */
	public void setDoctypePublic(String doctypePublic) {
		this.doctypePublic = doctypePublic;
	}
	/**
	 * Get the root element name of a doctype declaration
	 * @return the root element name of a doctype declaration
	 */
	public String getDoctypeRootElem() {
		return rootElem;
	}
	/**
	 * Set the root element name of a doctype declaration
	 * This is not part of the official specification, but might simplify certain tasks
	 * @param rootElem the root element name of a doctype declaration
	 */
	public void setDoctypeRootElem(String rootElem) {
		this.rootElem = rootElem;
	}
	/**
	 * Can standalone/top-level attributes be serialized?
	 * @return true yes, false no
	 */
	public boolean isSerializeStandaloneAttributes() {
		return printStandaloneAttributes;
	}
	/**
	 * Allow serializing standalone/top-level attributes
	 * This is not part of the spec, but might be useful for debugging/trace cases
	 * @param serAttr true yes, false no
	 */
	public void setSerializeStandaloneAttributes(boolean serAttr) {
		printStandaloneAttributes = serAttr;
	}

	/**
	 * Get the encoding for the serialization
	 * @return a string values expressing the desired encoding
	 */
	public String getEncoding() {
		return encoding;
	}
	/**
	 * Set the encoding for the serialization
	 * @param encoding a string values expressing the desired encoding
	 */

	public void setEncoding(String encoding) {
		this.encoding = encoding;
	}
	/**
	 * Shall URI Attributes be escaped?
	 * @return true yes, false no
	 */
	public boolean isEscapeURIAttributes() {
		return escapeURIAttributes;
	}
	/**
	 * Set if URI Attributes will be escaped
	 * @param escapeURIAttributes true yes, false no
	 */
	public void setEscapeURIAttributes(boolean escapeURIAttributes) {
		this.escapeURIAttributes = escapeURIAttributes;
	}
	/**
	 * Shall the content type be included
	 * @return true yes, false no
	 */
	public boolean isIncludeContentType() {
		return includeContentType;
	}
	/** 
	 * Set if the content type shall be included
	 * @param includeContentType true yes, false no
	 */
	public void setIncludeContentType(boolean includeContentType) {
		this.includeContentType = includeContentType;
	}
	/**
	 * Shall the output be indented?
	 * @return true yes, false no
	 */
	public boolean isIndent() {
		return indent;
	}
	/**
	 * Set if the output should be indented (= pretty-printed)
	 * @param indent true yes, false no
	 */
	public void setIndent(boolean indent) {
		this.indent = indent;
	}
	/**
	 * Get the media type setting
	 * @return a string specifying the MIME media type
	 */
	public String getMediaType() {
		return mediaType;
	}
	/**
	 * Set the media type setting 
	 * @param mediaType a string specifying the MIME media type
	 */
	public void setMediaType(String mediaType) {
		this.mediaType = mediaType;
	}
	/**
	 * Get the output method
	 * @return one of OUTPUT_METHOD_XML, OUTPUT_METHOD_XHTML, OUTPUT_METHOD_HTML, OUTPUT_METHOD_TEXT
	 */
	public int getOutputMethod() {
		return outputMethod;
	}
	/**
	 * Set the output method
	 * @param om one of OUTPUT_METHOD_XML, OUTPUT_METHOD_XHTML, OUTPUT_METHOD_HTML, OUTPUT_METHOD_TEXT
	 * 
	 */
	public void setOutputMethod(int om) {
		outputMethod = om;
	}
	/**
	 * Get the unicode normalization format that should be used
	 * @return one of  	NORMALIZATION_FORM_NONE, NORMALIZATION_FORM_NFC, NORMALIZATION_FORM_NFD, NORMALIZATION_FORM_NFKC, NORMALIZATION_FORM_NFKD, NORMALIZATION_FORM_FULL
	 */
	public int getNormalizationForm() {
		return normalizationForm;
	}
	/**
	 * Set the unicode normalization format that should be used
	 * @param normalizationForm NORMALIZATION_FORM_NONE, NORMALIZATION_FORM_NFC, NORMALIZATION_FORM_NFD, NORMALIZATION_FORM_NFKC, NORMALIZATION_FORM_NFKD, NORMALIZATION_FORM_FULL
	 * @throws MXQueryException
	 */
	public void setNormalizationForm(int normalizationForm) throws MXQueryException{
		this.normalizationForm = normalizationForm;
	}
	/**
	 * Shall the XML declaration be omitted?
	 * @return true yes, false no
	 */
	public boolean isOmitXMLDeclaration() {
		return omitXMLDeclaration;
	}
	/**
	 * Set if the XML declaration be omitted
	 * @param omitXMLDeclaration true yes, false no
	 */
	public void setOmitXMLDeclaration(boolean omitXMLDeclaration) {
		this.omitXMLDeclaration = omitXMLDeclaration;
	}
	/**
	 * Set the standalone parameter
	 * @return one of STANDALONE_OMIT, STANDALONE_YES, STANDALONE_NO
	 */
	public int getStandalone() {
		return standAlone;
	}
	/**
	 * Set the standalone parameter
	 * @param standAlone one of STANDALONE_OMIT, STANDALONE_YES, STANDALONE_NO
	 */
	public void setStandAlone(int standAlone) {
		this.standAlone = standAlone;
	}
	/**
	 * Is it allowed to undeclare prefixes? 
	 * @return true yes, false no
	 */
	public boolean isUndeclarePrefixes() {
		return undeclarePrefixes;
	}
	/**
	 * Set if prefixes may be undeclared
	 * @param undeclarePrefixes true yes, false no
	 */
	public void setUndeclarePrefixes(boolean undeclarePrefixes) {
		this.undeclarePrefixes = undeclarePrefixes;
	}
	/**
	 * Get the list of character substitutions
	 * @return a Hashtable containing (character -> replacement) mappings
	 */
	public Hashtable getCharacterMaps() {
		return characterMaps;
	}
	/**
	 * Set the list of character substitutions
	 * @param characterMaps a Hashtable containing (character -> replacement) mappings
	 */
	public void setCharacterMaps(Hashtable characterMaps) {
		this.characterMaps = characterMaps;
	}
	/**
	 * Gets the version parameter
	 * @return the version number for this document (e.g. 1.0 for XML 1.0)
	 */
	public String getVersion() {
		return version;
	}
	/**
	 * Set the version parameter
	 * @param version
	 * @throws MXQueryException
	 */
	public void setVersion(String version) throws MXQueryException{
		if (outputMethod == OUTPUT_METHOD_XML && !(version.equals("1.0")||version.equals("1.1")))
				throw new MXQueryException(ErrorCodes.S0013_UNSUPPORTED_XML_HTML_VERSION,"",null);
		this.version = version;
	}
}
