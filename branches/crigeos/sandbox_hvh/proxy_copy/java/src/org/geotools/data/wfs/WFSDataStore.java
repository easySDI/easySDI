/*
 *    GeoTools - OpenSource mapping toolkit
 *    http://geotools.org
 *    (C) 2004-2006, Geotools Project Managment Committee (PMC)
 *
 *    This library is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU Lesser General Public
 *    License as published by the Free Software Foundation; either
 *    version 2.1 of the License, or (at your option) any later version.
 *
 *    This library is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *    Lesser General Public License for more details.
 */
package org.geotools.data.wfs;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.Reader;
import java.io.StringWriter;
import java.io.UnsupportedEncodingException;
import java.io.Writer;
import java.net.Authenticator;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.PasswordAuthentication;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.net.URLEncoder;
import java.util.Arrays;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.logging.Level;
import java.util.logging.Logger;
import java.util.zip.GZIPInputStream;

import javax.naming.OperationNotSupportedException;

import org.apache.xerces.impl.dv.util.Base64;
import org.geotools.data.AbstractDataStore;
import org.geotools.data.DataSourceException;
import org.geotools.data.DataUtilities;
import org.geotools.data.DefaultQuery;
import org.geotools.data.EmptyFeatureReader;
import org.geotools.data.FeatureReader;
import org.geotools.data.FeatureSource;
import org.geotools.data.FilteringFeatureReader;
import org.geotools.data.Query;
import org.geotools.data.ReTypeFeatureReader;
import org.geotools.data.Transaction;
import org.geotools.data.crs.ForceCoordinateSystemFeatureReader;
import org.geotools.data.ows.FeatureSetDescription;
import org.geotools.data.ows.WFSCapabilities;
import org.geotools.feature.AttributeType;
import org.geotools.feature.AttributeTypeFactory;
import org.geotools.feature.FeatureType;
import org.geotools.feature.FeatureTypeBuilder;
import org.geotools.feature.GeometryAttributeType;
import org.geotools.feature.SchemaException;
import org.geotools.filter.ExpressionType;
import org.geotools.filter.FidFilter;
import org.opengis.filter.Filter;
import org.geotools.filter.FilterType;
import org.geotools.filter.Filters;
import org.geotools.filter.GeometryFilter;
import org.geotools.filter.LiteralExpression;
import org.geotools.filter.visitor.PostPreProcessFilterSplittingVisitor;
import org.geotools.filter.visitor.PostPreProcessFilterSplittingVisitor.WFSBBoxFilterVisitor;
import org.geotools.geometry.jts.JTS;
import org.geotools.geometry.jts.ReferencedEnvelope;
import org.geotools.referencing.CRS;
import org.geotools.referencing.crs.DefaultGeographicCRS;
import org.geotools.util.logging.Logging;
import org.geotools.xml.DocumentFactory;
import org.geotools.xml.DocumentWriter;
import org.geotools.xml.SchemaFactory;
import org.geotools.xml.filter.FilterSchema;
import org.geotools.xml.gml.GMLComplexTypes;
import org.geotools.xml.gml.WFSFeatureTypeTransformer;
import org.geotools.xml.schema.Element;
import org.geotools.xml.schema.Schema;
import org.geotools.xml.wfs.WFSSchema;
import org.opengis.referencing.FactoryException;
import org.opengis.referencing.NoSuchAuthorityCodeException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.operation.MathTransform;
import org.opengis.referencing.operation.TransformException;
import org.opengis.geometry.MismatchedDimensionException;
import org.xml.sax.SAXException;

//import com.sun.org.apache.xerces.internal.impl.dv.util.Base64;
import com.vividsolutions.jts.geom.Envelope;
import com.vividsolutions.jts.geom.Geometry;

/**
 * <p>
 * DOCUMENT ME!
 * </p>
 * 
 * @author dzwiers
 * @source $URL:
 *         http://svn.geotools.org/geotools/tags/2.4.1/modules/plugin/wfs/src
 *         /main/java/org/geotools/data/wfs/WFSDataStore.java $
 */
public class WFSDataStore extends AbstractDataStore {

	protected WFSCapabilities capabilities = null;

	protected static final int AUTO_PROTOCOL = 3;
	protected static final int POST_PROTOCOL = 1;
	protected static final int GET_PROTOCOL = 2;

	protected int protocol = AUTO_PROTOCOL; // visible for transaction
	protected Authenticator auth = null; // visible for transaction

	private int bufferSize = 10;
	private int timeout = 10000;
	private final boolean tryGZIP;
	protected WFSStrategy strategy;

	protected String encoding = "UTF-8";

	private boolean lenient;

	/**
	 * Construct <code>WFSDataStore</code>.
	 * 
	 * Should NEVER be called!
	 */
	private WFSDataStore() {
		// not called
		tryGZIP = true;
	}

	protected WFSDataStore(URL host, Boolean protocol, String username, String password, int timeout, int buffer, boolean tryGZIP, boolean lenient,
			String encoding) throws SAXException, IOException {
		this(host, protocol, username, password, timeout, buffer, tryGZIP, lenient);
		if (encoding != null) {
			this.encoding = encoding;
		}
	}

	/**
	 * Construct <code>WFSDataStore</code>.
	 * 
	 * @param host
	 *            - may not yet be a capabilities url
	 * @param protocol
	 *            - true,false,null (post,get,auto)
	 * @param username
	 *            - iff password
	 * @param password
	 *            - iff username
	 * @param timeout
	 *            - default 3000 (ms)
	 * @param buffer
	 *            - default 10 (features)
	 * @param tryGZIP
	 *            - indicates to use GZIP if server supports it.
	 * 
	 * @throws SAXException
	 * @throws IOException
	 */
	protected WFSDataStore(URL host, Boolean protocol, String username, String password, int timeout, int buffer, boolean tryGZIP) throws SAXException,
			IOException {
		this(host, protocol, username, password, timeout, buffer, tryGZIP, false);
	}

	/**
	 * Construct <code>WFSDataStore</code>.
	 * 
	 * @param host
	 *            - may not yet be a capabilities url
	 * @param protocol
	 *            - true,false,null (post,get,auto)
	 * @param username
	 *            - iff password
	 * @param password
	 *            - iff username
	 * @param timeout
	 *            - default 3000 (ms)
	 * @param buffer
	 *            - default 10 (features)
	 * @param tryGZIP
	 *            - indicates to use GZIP if server supports it.
	 * @param lenient
	 *            - if true the parsing will be very forgiving to bad data.
	 *            Errors will be logged rather than exceptions.
	 * 
	 * @throws SAXException
	 * @throws IOException
	 */
	protected WFSDataStore(URL host, Boolean protocol, String username, String password, int timeout, int buffer, boolean tryGZIP, boolean lenient)
			throws SAXException, IOException {
		super(true);

		this.lenient = lenient;
		if ((username != null) && (password != null)) {
			auth = new WFSAuthenticator(username, password);
		}

		if (protocol == null) {
			this.protocol = AUTO_PROTOCOL;
		} else {
			if (protocol.booleanValue()) {
				this.protocol = POST_PROTOCOL;
			} else {
				this.protocol = GET_PROTOCOL;
			}
		}

		this.timeout = timeout;
		this.bufferSize = buffer;
		this.tryGZIP = tryGZIP;
		findCapabilities(host);
		determineCorrectStrategy(host);
	}

	private void determineCorrectStrategy(URL host) {
		if (host.toString().indexOf("mapserv") != -1)
			strategy = new MapServerWFSStrategy(this);
		else
			strategy = new StrictWFSStrategy(this);
	}

	private void findCapabilities(URL host) throws SAXException, IOException {

		Object t = null;
		Map hints = new HashMap();
		hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);

		if ((protocol & GET_PROTOCOL) == GET_PROTOCOL) {
			HttpURLConnection hc = getConnection(createGetCapabilitiesRequest(host), auth, false);
			InputStream is = getInputStream(hc);
//			if (is != null) {
//				
//				Writer writer = new StringWriter();
//				char[] buffer = new char[1024];
//				try {
//				Reader reader = new BufferedReader(
//				new InputStreamReader(is, "UTF-8"));
//				int n;
//				while ((n = reader.read(buffer)) != -1) {
//				writer.write(buffer, 0, n);
//				}
//				} finally {
//				is.close();
//				}
//				System.err.println (writer.toString());
//				} 
			t = DocumentFactory.getInstance(is, hints, WFSDataStoreFactory.logger.getLevel());
		}

		if ((false == (t instanceof WFSCapabilities)) && ((protocol & POST_PROTOCOL) == POST_PROTOCOL)) {
			HttpURLConnection hc = getConnection(host, auth, true);

			// write request
			Writer osw = getOutputStream(hc);
			hints.put(DocumentWriter.BASE_ELEMENT, WFSSchema.getInstance().getElements()[0]); // GetCapabilities

			try {
				DocumentWriter.writeDocument((Object) null, WFSSchema.getInstance(), osw, hints);
			} catch (OperationNotSupportedException e) {
				WFSDataStoreFactory.logger.warning(e.getMessage());
				throw new SAXException(e);
			}

			osw.flush();
			osw.close();

			InputStream is = getInputStream(hc);
			t = DocumentFactory.getInstance(is, hints, WFSDataStoreFactory.logger.getLevel());
		}

		if (t instanceof WFSCapabilities) {
			capabilities = (WFSCapabilities) t;
		} else {
			throw new SAXException("The specified URL Should have returned a 'WFSCapabilities' object. Returned a "
					+ ((t == null) ? "null value." : (t.getClass().getName() + " instance.")));
		}
	}

	protected HttpURLConnection getConnection(URL url, Authenticator auth, boolean isPost) throws IOException {

		HttpURLConnection connection = (HttpURLConnection) url.openConnection();

		if (isPost) {
			connection.setRequestMethod("POST");
			connection.setDoOutput(true);
			connection.setRequestProperty("Content-type", "text/xml, application/xml");
		} else {
			connection.setRequestMethod("GET");
		}
		connection.setDoInput(true);
		/*
		 * FIXME this could breaks uDig. Not quite sure what to do otherwise.
		 * Maybe have a mechanism that would allow an authenticator to ask the
		 * datastore itself for a previously supplied user/pass.
		 */
		if (auth != null) {
			synchronized (Authenticator.class) {
				Authenticator.setDefault(auth);
				WFSAuthenticator ath = (WFSAuthenticator) auth;
				String encoded = Base64.encode(new StringBuffer().append(ath.getPasswordAuthentication().getUserName()).append(":").append(
						new String(ath.getPasswordAuthentication().getPassword())).toString().getBytes());
//				encoded = encoded.replaceAll("\n","");
				connection.addRequestProperty("Authorization", "Basic "
						+ encoded);
				connection.connect();
				Authenticator.setDefault(null);
			}
		}

		if (this.tryGZIP) {
			connection.addRequestProperty("Accept-Encoding", "gzip");
		}

		return connection;
	}

	protected static URL createGetCapabilitiesRequest(URL host) {
		if (host == null) {
			return null;
		}

		String url = host.toString();

		if (host.getQuery() == null) {
			url += "?SERVICE=WFS&VERSION=1.0.0&REQUEST=GetCapabilities";
		} else {
			String t = host.getQuery().toUpperCase();

			if (t.indexOf("SERVICE") == -1) {
				url += "&SERVICE=WFS";
			}

			if (t.indexOf("VERSION") == -1) {
				url += "&VERSION=1.0.0";
			}

			if (t.indexOf("REQUEST") == -1) {
				url += "&REQUEST=GetCapabilities";
			}
		}

		try {
			return new URL(url);
		} catch (MalformedURLException e) {
			WFSDataStoreFactory.logger.warning(e.toString());

			return host;
		}
	}

	private String[] typeNames = null;
	private Map featureTypeCache = new HashMap();

	private Map fidMap = new HashMap();

	private Map xmlSchemaCache = new HashMap();

	/**
	 * @see org.geotools.data.AbstractDataStore#getTypeNames()
	 */
	public String[] getTypeNames() {
		if (typeNames == null) {
			List l = capabilities.getFeatureTypes();
			typeNames = new String[l.size()];

			for (int i = 0; i < l.size(); i++) {
				typeNames[i] = ((FeatureSetDescription) l.get(i)).getName();
			}
		}
		// protect the cache against external modifications
		String[] retVal = new String[typeNames.length];
		System.arraycopy(typeNames, 0, retVal, 0, typeNames.length);
		return retVal;
	}

	/**
	 * DOCUMENT ME!
	 * 
	 * @param typeName
	 *            DOCUMENT ME!
	 * 
	 * @return DOCUMENT ME!
	 * 
	 * @throws IOException
	 * 
	 * @see org.geotools.data.AbstractDataStore#getSchema(java.lang.String)
	 */
	public FeatureType getSchema(String typeName) throws IOException {
		if (featureTypeCache.containsKey(typeName)) {
			return (FeatureType) featureTypeCache.get(typeName);
		}

		// TODO sanity check for request with capabilities obj

		FeatureType t = null;
		SAXException sax = null;
		IOException io = null;
		if (((protocol & POST_PROTOCOL) == POST_PROTOCOL) && (t == null)) {
			try {
				t = getSchemaPost(typeName);
			} catch (SAXException e) {
				WFSDataStoreFactory.logger.warning(e.toString());
				sax = e;
			} catch (IOException e) {
				WFSDataStoreFactory.logger.warning(e.toString());
				io = e;
			}
		}

		if (((protocol & GET_PROTOCOL) == GET_PROTOCOL) && (t == null)) {
			try {
				t = getSchemaGet(typeName);
			} catch (SAXException e) {
				WFSDataStoreFactory.logger.warning(e.toString());
				sax = e;
			} catch (IOException e) {
				WFSDataStoreFactory.logger.warning(e.toString());
				io = e;
			}
		}

		if (t == null && sax != null)
			throw new DataSourceException(sax);

		if (t == null && io != null)
			throw io;

		// set crs?
		FeatureSetDescription fsd = WFSCapabilities.getFeatureSetDescription(capabilities, typeName);
		String crsName = null;
		String ftName = null;
		if (fsd != null) {
			crsName = fsd.getSRS();
			ftName = fsd.getName();

			CoordinateReferenceSystem crs;
			try {
				if (crsName != null) {
					crs = CRS.decode(crsName);
					t = WFSFeatureTypeTransformer.transform(t, crs);
				}
			} catch (FactoryException e) {
				WFSDataStoreFactory.logger.warning(e.getMessage());
			} catch (SchemaException e) {
				WFSDataStoreFactory.logger.warning(e.getMessage());
			}
		}

		if (ftName != null) {
			try {
				t = FeatureTypeBuilder.newFeatureType(t.getAttributeTypes(), ftName == null ? typeName : ftName, t.getNamespace(), t.isAbstract(), t
						.getAncestors(), t.getDefaultGeometry());

			} catch (SchemaException e1) {
				WFSDataStoreFactory.logger.warning(e1.getMessage());
			}
		}
		try {
			URL url = getDescribeFeatureTypeURLGet(typeName);
			if (url != null) {
				t = new WFSFeatureType(t, new URI(url.toString()));
			}
		} catch (URISyntaxException e) {
			throw (RuntimeException) new RuntimeException(e);
		}
		if (t != null) {
			featureTypeCache.put(typeName, t);
		}

		return t;
	}

	// protected for testing
	protected FeatureType getSchemaGet(String typeName) throws SAXException, IOException {
		URL getUrl = getDescribeFeatureTypeURLGet(typeName);
		Logging.getLogger("org.geotools.data.communication").fine("Output: " + getUrl);
		if (getUrl == null)
			return null;
		HttpURLConnection hc = getConnection(getUrl, auth, false);

		InputStream is = getInputStream(hc);
		Schema schema;
		try {
			schema = SchemaFactory.getInstance(null, is);
		} finally {
			is.close();
		}
		return parseDescribeFeatureTypeResponse(typeName, schema);
	}

	private URL getDescribeFeatureTypeURLGet(String typeName) throws MalformedURLException {
		URL getUrl = capabilities.getDescribeFeatureType().getGet();
		Logging.getLogger("org.geotools.data.communication").fine("Output: " + getUrl);

		if (getUrl == null) {
			return null;
		}

		String query = getUrl.getQuery();
		query = query == null ? null : query.toUpperCase();
		String url = getUrl.toString();

		if ((query == null) || "".equals(query)) {
			if ((url == null) || !url.endsWith("?")) {
				url += "?";
			}

			url += "SERVICE=WFS";
		} else {
			if (query.indexOf("SERVICE=WFS") == -1) {
				url += "&SERVICE=WFS";
			}
		}

		if ((query == null) || (query.indexOf("VERSION") == -1)) {
			url += "&VERSION=1.0.0";
		}

		if ((query == null) || (query.indexOf("REQUEST") == -1)) {
			url += "&REQUEST=DescribeFeatureType";
		}

		url += ("&TYPENAME=" + typeName);

		getUrl = new URL(url);
		return getUrl;
	}

	static FeatureType parseDescribeFeatureTypeResponse(String typeName, Schema schema) throws SAXException {
		Element[] elements = schema.getElements();

		if (elements == null) {
			return null; // not found
		}

		Element element = null;

		String ttname = typeName.substring(typeName.indexOf(":") + 1);

		for (int i = 0; (i < elements.length) && (element == null); i++) {
			// HACK -- namspace related -- should be checking ns as opposed to
			// removing prefix
			if (typeName.equals(elements[i].getName()) || ttname.equals(elements[i].getName())) {
				element = elements[i];
			}
		}

		if (element == null) {
			return null;
		}

		FeatureType ft = GMLComplexTypes.createFeatureType(element);

		return ft;
	}

	// protected for testing
	protected FeatureType getSchemaPost(String typeName) throws IOException, SAXException {
		URL postUrl = capabilities.getDescribeFeatureType().getPost();

		if (postUrl == null) {
			return null;
		}

		HttpURLConnection hc = getConnection(postUrl, auth, true);

		// write request
		Writer osw = getOutputStream(hc);
		Map hints = new HashMap();
		hints.put(DocumentWriter.BASE_ELEMENT, WFSSchema.getInstance().getElements()[1]); // DescribeFeatureType
		List l = capabilities.getFeatureTypes();
		Iterator it = l.iterator();
		URI uri = null;
		while (it.hasNext() && uri == null) {
			FeatureSetDescription fsd = (FeatureSetDescription) it.next();
			if (typeName.equals(fsd.getName()))
				uri = fsd.getNamespace();
		}
		if (uri != null)
			hints.put(DocumentWriter.SCHEMA_ORDER, new String[] { WFSSchema.NAMESPACE.toString(), uri.toString() });

		hints.put(DocumentWriter.ENCODING, encoding);
		try {
			DocumentWriter.writeDocument(new String[] { typeName }, WFSSchema.getInstance(), osw, hints);
		} catch (OperationNotSupportedException e) {
			WFSDataStoreFactory.logger.warning(e.getMessage());
			throw new SAXException(e);
		}

		osw.flush();
		osw.close();
		InputStream is = getInputStream(hc);

		Schema schema;
		try {
			schema = SchemaFactory.getInstance(null, is);
		} finally {
			is.close();
		}

		return parseDescribeFeatureTypeResponse(typeName, schema);
	}

	// protected for testing
	protected FeatureReader getFeatureReaderGet(Query request, Transaction transaction) throws UnsupportedEncodingException, IOException, SAXException {
		URL getUrl = capabilities.getGetFeature().getGet();

		if (getUrl == null) {
			return null;
		}

		String query = getUrl.getQuery();
		query = query == null ? null : query.toUpperCase();
		String url = getUrl.toString();

		if ((query == null) || "".equals(query)) {
			if ((url == null) || !url.endsWith("?")) {
				url += "?";
			}

			url += "SERVICE=WFS";
		} else {
			if (query.indexOf("SERVICE=WFS") == -1) {
				url += "&SERVICE=WFS";
			}
		}

		if ((query == null) || (query.indexOf("VERSION") == -1)) {
			url += "&VERSION=1.0.0";
		}

		if ((query == null) || (query.indexOf("REQUEST") == -1)) {
			url += "&REQUEST=GetFeature";
		}

		if (request != null) {
			if (request.getMaxFeatures() != Query.DEFAULT_MAX) {
				url += ("&MAXFEATURES=" + request.getMaxFeatures());
			}

			if (request.getFilter() != null) {
				if (Filters.getFilterType(request.getFilter()) == FilterType.GEOMETRY_BBOX) {
					String bb = printBBoxGet(((GeometryFilter) request.getFilter()), request.getTypeName());
					if (bb != null)
						url += ("&BBOX=" + URLEncoder.encode(bb, this.encoding));
				} else {
					if (Filters.getFilterType(request.getFilter()) == FilterType.FID) {
						FidFilter ff = (FidFilter) request.getFilter();

						if ((ff.getFids() != null) && (ff.getFids().length > 0)) {
							url += ("&FEATUREID=" + ff.getFids()[0]);

							for (int i = 1; i < ff.getFids().length; i++) {
								url += ("," + ff.getFids()[i]);
							}
						}
					} else {
						// rest
						if (request.getFilter() != Filter.INCLUDE && request.getFilter() != Filter.EXCLUDE) {
							url += "&FILTER=" + URLEncoder.encode(printFilter(request.getFilter()), this.encoding);
						}
					}
				}
			}
		}

		url += ("&TYPENAME=" + URLEncoder.encode(request.getTypeName(), this.encoding));

		Logging.getLogger("org.geotools.data.wfs").fine(url);
		Logging.getLogger("org.geotools.data.communication").fine("Output: " + url);
		getUrl = new URL(url);
		HttpURLConnection hc = getConnection(getUrl, auth, false);

		InputStream is = getInputStream(hc);
		WFSTransactionState ts = null;

		if (!(transaction == Transaction.AUTO_COMMIT)) {
			ts = (WFSTransactionState) transaction.getState(this);

			if (ts == null) {
				ts = new WFSTransactionState(this);
				transaction.putState(this, ts);
			}
		}

		WFSFeatureType schema = (WFSFeatureType) getSchema(request.getTypeName());

		FeatureType featureType;
		try {
			featureType = DataUtilities.createSubType(schema.delegate, request.getPropertyNames(), request.getCoordinateSystem());
		} catch (SchemaException e) {
			featureType = schema.delegate;
		}
		WFSFeatureReader ft = WFSFeatureReader.getFeatureReader(is, bufferSize, timeout, ts,
				new WFSFeatureType(schema.delegate, schema.getSchemaURI(), lenient));

		if (!featureType.equals(ft.getFeatureType())) {
			LOGGER.fine("Recasting feature type to subtype by using a ReTypeFeatureReader");
			return new ReTypeFeatureReader(ft, featureType, false);
		} else
			return ft;

	}

	Writer getOutputStream(HttpURLConnection hc) throws IOException {
		OutputStream os = hc.getOutputStream();

		Writer w = new OutputStreamWriter(os);
		// write request
		Logger logger = Logging.getLogger("org.geotools.data.wfs");
		if (logger.isLoggable(Level.FINE)) {
			w = new LogWriterDecorator(w, logger, Level.FINE);
		}
		// special logger for communication information only.
		logger = Logging.getLogger("org.geotools.data.communication");
		if (logger.isLoggable(Level.FINE)) {
			w = new LogWriterDecorator(w, logger, Level.FINE);
		}
		return w;
	}

	/**
	 * If the field useGZIP is true Adds gzip to the connection accept-encoding
	 * property and creates a gzip inputstream (if server supports it).
	 * Otherwise returns a normal buffered input stream.
	 * 
	 * @param hc
	 *            the connection to use to create the stream
	 * @return an input steam from the provided connection
	 */
	InputStream getInputStream(HttpURLConnection hc) throws IOException {
		InputStream is = hc.getInputStream();

		if (tryGZIP) {
			if (hc.getContentEncoding() != null && hc.getContentEncoding().indexOf("gzip") != -1) {
				is = new GZIPInputStream(is);
			}
		}
		is = new BufferedInputStream(is);
		if (WFSDataStoreFactory.logger.isLoggable(Level.FINE)) {
			is = new LogInputStream(is, WFSDataStoreFactory.logger, Level.FINE);
		}
		// special logger for communication information only.
		Logger logger = Logging.getLogger("org.geotools.data.communication");
		if (logger.isLoggable(Level.FINE)) {
			is = new LogInputStream(is, logger, Level.FINE);
		}
		return is;
	}

	private String printFilter(Filter f) throws IOException, SAXException {
		// ogc filter
		Map hints = new HashMap();
		hints.put(DocumentWriter.BASE_ELEMENT, FilterSchema.getInstance().getElements()[2]); // Filter

		StringWriter w = new StringWriter();

		try {
			DocumentWriter.writeFragment(f, FilterSchema.getInstance(), w, hints);
		} catch (OperationNotSupportedException e) {
			WFSDataStoreFactory.logger.warning(e.toString());
			throw new SAXException(e);
		}

		return w.toString();
	}

	private String printBBoxGet(GeometryFilter gf, String typename) throws IOException {
		Envelope e = null;

		if (gf.getLeftGeometry().getType() == ExpressionType.LITERAL_GEOMETRY) {
			e = ((Geometry) ((LiteralExpression) gf.getLeftGeometry()).getLiteral()).getEnvelopeInternal();
		} else {
			if (gf.getRightGeometry().getType() == ExpressionType.LITERAL_GEOMETRY) {
				LiteralExpression literal = (LiteralExpression) gf.getRightGeometry();
				Geometry geometry = (Geometry) literal.getLiteral();
				e = geometry.getEnvelopeInternal();
			} else {
				throw new IOException("Cannot encode BBOX:" + gf);
			}
		}

		if (e == null || e.isNull())
			return null;

		// Cannot check against layer bbounding box because they may be in
		// different CRS
		// We could insert ReferencedEnvelope fun here - note a check is already
		// performed
		// as part clipping the request bounding box.

		/*
		 * // find layer's bbox Envelope lbb = null; if(capabilities != null &&
		 * capabilities.getFeatureTypes() != null && typename!=null &&
		 * !"".equals(typename)){ List fts = capabilities.getFeatureTypes();
		 * if(!fts.isEmpty()){ for(Iterator i=fts.iterator();i.hasNext() && lbb
		 * == null;){ FeatureSetDescription fsd =
		 * (FeatureSetDescription)i.next(); if(fsd!=null &&
		 * typename.equals(fsd.getName())){ lbb = fsd.getLatLongBoundingBox(); }
		 * } } } if(lbb == null || lbb.contains(e))
		 */
		return e.getMinX() + "," + e.getMinY() + "," + e.getMaxX() + "," + e.getMaxY();
		// return null;
	}

	// protected for testing
	protected FeatureReader getFeatureReaderPost(Query query, Transaction transaction) throws SAXException, IOException {
		URL postUrl = capabilities.getGetFeature().getPost();

		if (postUrl == null) {
			return null;
		}

		HttpURLConnection hc = getConnection(postUrl, auth, true);

		Writer w = getOutputStream(hc);

		Map hints = new HashMap();
		hints.put(DocumentWriter.BASE_ELEMENT, WFSSchema.getInstance().getElements()[2]); // GetFeature
		hints.put(DocumentWriter.ENCODING, encoding);
		try {
			DocumentWriter.writeDocument(query, WFSSchema.getInstance(), w, hints);
		} catch (OperationNotSupportedException e) {
			WFSDataStoreFactory.logger.warning(e.toString());
			throw new SAXException(e);
		} finally {
			w.flush();
			w.close();
		}

		// JE: permit possibility for GZipped data.
		InputStream is = getInputStream(hc);

		WFSTransactionState ts = null;

		if (!(transaction == Transaction.AUTO_COMMIT)) {
			ts = (WFSTransactionState) transaction.getState(this);

			if (ts == null) {
				ts = new WFSTransactionState(this);
				transaction.putState(this, ts);
			}
		}
		WFSFeatureType schema = (WFSFeatureType) getSchema(query.getTypeName());

		FeatureType featureType;
		try {
			featureType = DataUtilities.createSubType(schema.delegate, query.getPropertyNames(), query.getCoordinateSystem());
		} catch (SchemaException e) {
			featureType = schema.delegate;
		}

		WFSFeatureReader ft = WFSFeatureReader.getFeatureReader(is, bufferSize, timeout, ts,
				new WFSFeatureType(schema.delegate, schema.getSchemaURI(), lenient));

		if (!featureType.equals(ft.getFeatureType())) {
			LOGGER.fine("Recasting feature type to subtype by using a ReTypeFeatureReader");
			return new ReTypeFeatureReader(ft, featureType, false);
		} else
			return ft;
	}

	protected FeatureReader getFeatureReader(String typeName) throws IOException {
		return getFeatureReader(typeName, new DefaultQuery(typeName));
	}

	protected FeatureReader getFeatureReader(String typeName, Query query) throws IOException {
		if ((query.getTypeName() == null) || !query.getTypeName().equals(typeName)) {
			Query q = new DefaultQuery(query);
			((DefaultQuery) q).setTypeName(typeName);

			return getFeatureReader(q, Transaction.AUTO_COMMIT);
		}

		return getFeatureReader(query, Transaction.AUTO_COMMIT);
	}

	/**
	 * @see org.geotools.data.DataStore#getFeatureReader(org.geotools.data.Query,
	 *      org.geotools.data.Transaction)
	 */
	public FeatureReader getFeatureReader(Query query, Transaction transaction) throws IOException {
		return strategy.getFeatureReader(query, transaction);
	}

	/*
	 * (non-Javadoc)
	 * 
	 * @see
	 * org.geotools.data.AbstractDataStore#getBounds(org.geotools.data.Query)
	 */
	protected Envelope getBounds(Query query) throws IOException {
		if ((query == null) || (query.getTypeName() == null)) {
			return super.getBounds(query);
		}

		List fts = capabilities.getFeatureTypes(); // FeatureSetDescription
		Iterator i = fts.iterator();
		String desiredType = query.getTypeName().substring(query.getTypeName().indexOf(":") + 1);

		while (i.hasNext()) {
			FeatureSetDescription fsd = (FeatureSetDescription) i.next();
			String fsdName = (fsd.getName() == null) ? null : fsd.getName().substring(fsd.getName().indexOf(":") + 1);

			if (desiredType.equals(fsdName)) {
				Envelope env = fsd.getLatLongBoundingBox();

				ReferencedEnvelope referencedEnvelope = new ReferencedEnvelope(env, DefaultGeographicCRS.WGS84);

				try {
					return referencedEnvelope.transform(CRS.decode(fsd.getSRS()), true);
				} catch (NoSuchAuthorityCodeException e) {
					return referencedEnvelope;
				} catch (TransformException e) {
					return referencedEnvelope;
				} catch (FactoryException e) {
					return referencedEnvelope;
				}
			}
		}

		return super.getBounds(query);
	}

	protected Filter[] splitFilters(Query q, Transaction t) throws IOException {
		// have to figure out which part of the request the server is capable of
		// after removing the parts in the update / delete actions
		// [server][post]
		if (q.getFilter() == null)
			return new Filter[] { Filter.INCLUDE, Filter.INCLUDE };
		if (q.getTypeName() == null || t == null)
			return new Filter[] { Filter.INCLUDE, q.getFilter() };

		FeatureType ft = getSchema(q.getTypeName());

		List fts = capabilities.getFeatureTypes(); // FeatureSetDescription
		boolean found = false;
		for (int i = 0; i < fts.size(); i++)
			if (fts.get(i) != null) {
				FeatureSetDescription fsd = (FeatureSetDescription) fts.get(i);
				if (ft.getTypeName().equals(fsd.getName())) {
					found = true;
				} else {
					String fsdName = (fsd.getName() == null) ? null : fsd.getName().substring(fsd.getName().indexOf(":") + 1);
					if (ft.getTypeName().equals(fsdName)) {
						found = true;
					}
				}
			}

		if (!found) {
			WFSDataStoreFactory.logger.warning("Could not find typeName: " + ft.getTypeName());
			return new Filter[] { Filter.INCLUDE, q.getFilter() };
		}
		WFSTransactionState state = (t == Transaction.AUTO_COMMIT) ? null : (WFSTransactionState) t.getState(this);
		WFSTransactionAccessor transactionAccessor = null;
		if (state != null)
			transactionAccessor = new WFSTransactionAccessor(state.getActions(ft.getTypeName()));
		PostPreProcessFilterSplittingVisitor wfsfv = new PostPreProcessFilterSplittingVisitor(capabilities.getFilterCapabilities(), ft, transactionAccessor);

		q.getFilter().accept(wfsfv, null);

		Filter[] f = new Filter[2];
		f[0] = wfsfv.getFilterPre(); // server
		f[1] = wfsfv.getFilterPost();

		return f;
	}

	/**
	 * @see org.geotools.data.AbstractDataStore#getUnsupportedFilter(java.lang.String,
	 *      org.geotools.filter.Filter)
	 */
	protected Filter getUnsupportedFilter(String typeName, Filter filter) {
		try {
			return splitFilters(new DefaultQuery(typeName, filter), Transaction.AUTO_COMMIT)[1];
		} catch (IOException e) {
			return filter;
		}
	}

	/**
	 * 
	 * @see org.geotools.data.DataStore#getFeatureSource(java.lang.String)
	 */
	public FeatureSource getFeatureSource(String typeName) throws IOException {
		if (capabilities.getTransaction() != null) {
			// if(capabilities.getLockFeature()!=null){
			// return new WFSFeatureLocking(this,getSchema(typeName));
			// }
			return new WFSFeatureStore(this, typeName);
		}

		return new WFSFeatureSource(this, typeName);
	}

	/**
	 * Runs {@link FidFilterVisitor} on the filter and returns the result as
	 * long as transaction is not AUTO_COMMIT or null.
	 * 
	 * @param filter
	 *            filter to process.
	 * @return Runs {@link FidFilterVisitor} on the filter and returns the
	 *         result as long as transaction is not AUTO_COMMIT or null.
	 */
	public Filter processFilter(Filter filter) {
		FidFilterVisitor visitor = new FidFilterVisitor(fidMap);
		Filters.accept(filter, visitor);
		return visitor.getProcessedFilter();
	}

	private static class WFSAuthenticator extends Authenticator {
		private PasswordAuthentication pa;

		private WFSAuthenticator() {
			// not called
		}

		/**
		 * 
		 * @param user
		 * @param pass
		 * @param host
		 */
		public WFSAuthenticator(String user, String pass) {
			pa = new PasswordAuthentication(user, pass.toCharArray());
		}

		protected PasswordAuthentication getPasswordAuthentication() {
			return pa;
		}
	}

	/**
	 * Adds a new fid mapping to the fid map.
	 * 
	 * @param original
	 *            the before fid
	 * @param finalFid
	 *            the final fid;
	 */
	public synchronized void addFidMapping(String original, String finalFid) {
		if (original == null)
			throw new NullPointerException();
		fidMap.put(original, finalFid);
	}

}
