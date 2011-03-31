package org.easysdi.proxy.reporting;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.StringWriter;
import java.io.Writer;
import java.net.Authenticator;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.naming.OperationNotSupportedException;
import javax.servlet.ServletException;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.Result;
import javax.xml.transform.Source;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.sax.SAXResult;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.apache.commons.httpclient.Credentials;
import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.UsernamePasswordCredentials;
import org.apache.commons.httpclient.auth.AuthScope;
import org.apache.commons.httpclient.methods.PostMethod;
import org.apache.commons.httpclient.methods.StringRequestEntity;
import org.apache.fop.apps.FOURIResolver;
import org.apache.fop.apps.Fop;
import org.apache.fop.apps.FopFactory;
import org.apache.fop.apps.MimeConstants;
import org.apache.xerces.jaxp.DocumentBuilderFactoryImpl;
import org.easysdi.security.JoomlaProvider;
import org.geotools.data.DataStore;
import org.geotools.data.DefaultQuery;
import org.geotools.data.Query;
import org.geotools.data.ows.CRSEnvelope;
import org.geotools.data.ows.Layer;
import org.geotools.data.ows.Response;
import org.geotools.data.wfs.WFSDataStoreFactory;
import org.geotools.data.wms.WMS1_0_0;
import org.geotools.data.wms.request.AbstractGetLegendGraphicRequest;
import org.geotools.data.wms.response.GetLegendGraphicResponse;
import org.geotools.factory.CommonFactoryFinder;
import org.geotools.factory.GeoTools;
import org.geotools.feature.AttributeType;
import org.geotools.feature.FeatureType;
import org.geotools.feature.GeometryAttributeType;
import org.geotools.ows.ServiceException;
import org.geotools.xml.DocumentWriter;
import org.geotools.xml.wfs.WFSSchema;
import org.opengis.filter.FilterFactory;
import org.opengis.filter.spatial.BBOX;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.xml.sax.SAXException;

public class MapToFop {
	private String urlWMS;
	private String urlWFS;
	private WFSDataStoreFactory wfsFactory = new WFSDataStoreFactory();
	private DataStore wfs;
	private String fopDir;
	private HttpClient httpClient;
	private Authentication token;
	Map<String, Object> connectionParameters = null;

	public void initWmsWfs(JoomlaProvider provider) throws ServletException, IOException, ServiceException {
		JdbcTemplate sjt = provider.getSjt();
		String prefix = provider.getPrefix();
		String wms ="";
		String wfs ="";
		if(provider.getVersion()== null || Integer.parseInt(provider.getVersion())<200)
		{
			wms = sjt.queryForObject("select value as pubWmsUrl from " + prefix + "easysdi_map_config where name = 'pubWmsUrl' limit 1", String.class);
			if (wms == null)
				throw new ServletException("pubWmsUrl must be set !");
			wfs = sjt.queryForObject("select value as pubWfsUrl from " + prefix + "easysdi_map_config where name = 'pubWfsUrl' limit 1", String.class);
			if (wfs == null)
				throw new ServletException("pubWfsUrl must be set !");
		}
		else
		{
			wms = sjt.queryForObject("select value as pubWmsUrl from " + prefix + "sdi_configuration where name = 'pubWmsUrl' limit 1", String.class);
			if (wms == null)
				throw new ServletException("pubWmsUrl must be set !");
			wfs = sjt.queryForObject("select value as pubWfsUrl from " + prefix + "sdi_configuration where name = 'pubWfsUrl' limit 1", String.class);
			if (wfs == null)
				throw new ServletException("pubWfsUrl must be set !");
		}
		token = SecurityContextHolder.getContext().getAuthentication();
		if (token != null && token.getPrincipal().toString() != null && token.getCredentials().toString() != null) {
			this.setCredentials(token.getPrincipal().toString(), token.getCredentials().toString());
		}
		this.setUrlWMS(wms);
		this.setUrlWFS(wfs);

	}

	public MapToFop(String fopDir) {
		super();
		this.fopDir = fopDir;
		this.httpClient = new HttpClient();
	}

	public String getUrlWMS() {
		return urlWMS;
	}

	public void setUrlWMS(String urlWMS) throws ServiceException, IOException {
		this.urlWMS = urlWMS;
	}

	public String getUrlWFS() {
		return urlWFS;
	}

	public void setUrlWFS(String urlWFS) throws IOException {
		this.urlWFS = urlWFS;
		if (connectionParameters == null)
			connectionParameters = new HashMap<String, Object>();
		URL completeURL =  new URL(urlWFS + "?request=GetCapabilities&service=WFS&version=1.0.0");
		connectionParameters.put(WFSDataStoreFactory.URL.key,completeURL);
		connectionParameters.put(WFSDataStoreFactory.TIMEOUT.key, 60000);
		connectionParameters.put(WFSDataStoreFactory.TRY_GZIP.key, false);
		this.wfs = wfsFactory.createDataStore(connectionParameters);
	}

	public String getMap(Layer baseLayer, String epsgCode, CRSEnvelope bbox, List<Layer> overlays, String format, int width, int height)
			throws ServiceException, IOException {
		Layer bLayer = ("".equals(baseLayer.getName()) && overlays.size() > 0) ? overlays.get(0) : baseLayer;
		if (!"".equals(bLayer.getName())) {
			GetMapRequest request = createGetMapRequest(bLayer, bbox, overlays);
			request.setSRS(epsgCode);
			request.setDimensions(width, height);
			request.setFormat("image/" + format);
			return request.getFinalURL().toExternalForm();
		} else
			throw new ServiceException("No layer defined");
	}

	private GetMapRequest createGetMapRequest(Layer baseLayer, CRSEnvelope bbox, List<Layer> overlays) throws ServiceException, IOException {

		GetMapRequest getMapRequest = new GetMapRequest(new URL(urlWMS));
		getMapRequest.setBBox(bbox);

		for (Layer layer : overlays) {
			getMapRequest.addLayer(layer);
		}
		getMapRequest.addLayer(baseLayer);

		return getMapRequest;
	}

	public void setCredentials(String username, String password) {
		password = password.split(":")[0];
		Authenticator.setDefault(new SimpleAuthenticator(username, password));
		Credentials credentials = new UsernamePasswordCredentials(username, password);
		httpClient.getParams().setAuthenticationPreemptive(true);
		httpClient.getState().setCredentials(AuthScope.ANY, credentials);
		if (connectionParameters == null)
			connectionParameters = new HashMap<String, Object>();
		connectionParameters.put(WFSDataStoreFactory.USERNAME.key, username);
		connectionParameters.put(WFSDataStoreFactory.PASSWORD.key, password);
	}

	public String getMap(String baseLayerName, String epsgCode, double minX, double minY, double maxX, double maxY, String overlayNames, String format,
			int width, int height) throws ServiceException, IOException {
		Layer baseLayer = new Layer();
		baseLayer.setName(baseLayerName);
		List<Layer> overlays = new ArrayList<Layer>();
		String[] overlayNamesTab = (overlayNames).split(",");
		for (String overlayName : overlayNamesTab) {
			Layer overlay = new Layer();
			overlay.setName(overlayName);
			overlay.setParent(baseLayer);
			overlays.add(0, overlay);
		}

		CRSEnvelope bbox = new CRSEnvelope(epsgCode, minX, minY, maxX, maxY);
		HashMap<Object, CRSEnvelope> boundingBoxes = new HashMap<Object, CRSEnvelope>();
		boundingBoxes.put(epsgCode, bbox);
		baseLayer.setBoundingBoxes(boundingBoxes);
		return this.getMap(baseLayer, epsgCode, bbox, overlays, format, width, height);
	}

	public Document getFeatures(String baseLayerName, String epsgCode, double minX, double minY, double maxX, double maxY, String overlayNames, String mapPath,
			String title, boolean getFeature) throws IOException, OperationNotSupportedException, ParserConfigurationException, SAXException,
			TransformerException {
		Layer baseLayer = new Layer();
		baseLayer.setName(baseLayerName);
		List<Layer> overlays = new ArrayList<Layer>();
		String[] overlayNamesTab = (overlayNames).split(",");
		for (String overlayName : overlayNamesTab) {
			Layer overlay = new Layer();
			overlay.setName(overlayName);
			overlay.setParent(baseLayer);
			overlays.add(0, overlay);
		}

		CRSEnvelope bbox = new CRSEnvelope(epsgCode, minX, minY, maxX, maxY);
		HashMap<Object, CRSEnvelope> boundingBoxes = new HashMap<Object, CRSEnvelope>();
		boundingBoxes.put(epsgCode, bbox);
		baseLayer.setBoundingBoxes(boundingBoxes);
		return this.getFeatures(baseLayer, bbox, overlays, mapPath, title, getFeature);
	}

	public Document getFeatures(Layer baseLayer, CRSEnvelope bbox, List<Layer> overlays, String mapPath, String title, boolean getFeature) throws IOException,
			ParserConfigurationException, SAXException, OperationNotSupportedException, TransformerException {
		if (wfs == null)
			setUrlWFS(urlWFS);
		DocumentBuilder builder = DocumentBuilderFactoryImpl.newInstance().newDocumentBuilder();
		Document doc = builder.newDocument();
		Element featuresE = doc.createElement("features");
		featuresE.setAttribute("image", mapPath);
		featuresE.setAttribute("title", title);
		doc.appendChild(featuresE);

		FilterFactory ff = CommonFactoryFinder.getFilterFactory(GeoTools.getDefaultHints());
		Map<String, Object> hints = new HashMap<String, Object>();
		hints.put(DocumentWriter.ENCODING, "UTF-8");

		org.geotools.xml.schema.Element[] wfsElements = WFSSchema.getInstance().getElements();
		hints.put(DocumentWriter.BASE_ELEMENT, wfsElements[4]); // Query

		Writer w = new StringWriter();

		for (Layer layer : overlays) {

			try {
				Element featureE = doc.createElement("feature");
				FeatureType schema = wfs.getSchema(layer.getName());
				String ftTitle = schema.getTypeName();
				GetLegendGraphicRequest legentRequest = new GetLegendGraphicRequest(new URL(urlWMS));
				legentRequest.setLayer(layer.getName());
				legentRequest.setFormat("image/png");
				featureE.setAttribute("title", ftTitle);
				featureE.setAttribute("name", layer.getName());
				featureE.setAttribute("legend-url", legentRequest.getFinalURL().toString());
				GeometryAttributeType geomDesc = schema.getDefaultGeometry();
				String geomName = geomDesc.getLocalName();
				for (AttributeType descriptor : schema.getAttributeTypes()) {
					if (!descriptor.getLocalName().equalsIgnoreCase(geomName)) {
						Element field = doc.createElement("field");
						field.setAttribute("name", descriptor.getLocalName());
						featureE.appendChild(field);
					}
				}
				BBOX bboxFilter = ff.bbox(geomName, bbox.getMinX(), bbox.getMinY(), bbox.getMaxX(), bbox.getMaxY(), bbox.getEPSGCode());
				Query query = new DefaultQuery(layer.getName(), bboxFilter);
				DocumentWriter.writeFragment(query, WFSSchema.getInstance(), w, hints);
				featuresE.appendChild(featureE);
			} catch (Exception e) {
				System.err.println(e.getLocalizedMessage());
				// e.printStackTrace();
			}
		}
		if (getFeature) {
			String request = "<GetFeature xmlns=\"http://www.opengis.net/wfs\" xmlns:gml=\"http://www.opengis.net/gml\" xmlns:ogc=\"http://www.opengis.net/ogc\" version=\"1.0.0\" service=\"WFS\" outputFormat=\"GML2\">\n"
					+ w.toString() + "\n</GetFeature>";
			PostMethod post = new PostMethod(urlWFS);
			// wmsGet.setRequestHeader(new Head)
			post.setRequestEntity(new StringRequestEntity(request, "text/xml", "utf-8"));
			httpClient.executeMethod(post);
			if (post.getResponseHeader("Content-Type").getValue().equals("text/xml")) {
				File responseFile = File.createTempFile("easysdi-GetFeature", ".xml");
				featuresE.setAttribute("file", responseFile.getAbsolutePath());
				FileOutputStream fos = new FileOutputStream(responseFile);
				fos.write(post.getResponseBody());
				fos.close();
			}
		}
		return doc;

	}

	public void toFOP(String baseLayerName, String epsgCode, double minX, double minY, double maxX, double maxY, String overlayNames, String format,
			String title, int width, int height, String xsltPath, OutputStream out, Map<String, Object> params) throws TransformerException, IOException,
			SAXException, OperationNotSupportedException, ParserConfigurationException {
		Layer baseLayer = new Layer();
		baseLayer.setName(baseLayerName);
		List<Layer> overlays = new ArrayList<Layer>();
		if (overlayNames != null) {
			String[] overlayNamesTab = (overlayNames).split(",");
			for (String overlayName : overlayNamesTab) {
				Layer overlay = new Layer();
				overlay.setName(overlayName);
				overlay.setParent(baseLayer);
				overlays.add(0, overlay);
			}
		}

		CRSEnvelope bbox = new CRSEnvelope(epsgCode, minX, minY, maxX, maxY);
		HashMap<Object, CRSEnvelope> boundingBoxes = new HashMap<Object, CRSEnvelope>();
		boundingBoxes.put(epsgCode, bbox);
		baseLayer.setBoundingBoxes(boundingBoxes);

		FopFactory fopFactory = FopFactory.newInstance();
		fopFactory.setUserConfig(fopDir + "/fop.xconf");
		Fop fop = fopFactory.newFop(MimeConstants.MIME_PDF, out);
		FOURIResolver resolver = new EasySDIFOURIResolver(token);
		fop.getUserAgent().setURIResolver(resolver);

		Source xslt = new StreamSource(new File(fopDir + File.separator + xsltPath));
		TransformerFactory factory = TransformerFactory.newInstance();
		Transformer transformer = factory.newTransformer(xslt);
		Result res = new SAXResult(fop.getDefaultHandler());
		String mapPath = getMap(baseLayer, epsgCode, bbox, overlays, format, width, height);
		Object gf = params.get("p-showList");
		boolean isGF = (gf instanceof String[] && "1".equals(((String[]) gf)[0]));
		Document features = getFeatures(baseLayer, bbox, overlays, mapPath, title, isGF);
		DOMSource domSource = new DOMSource(features);
		for (Map.Entry<String, Object> entry : params.entrySet()) {
			String key = entry.getKey();
			if (key.startsWith("p-")) {
				Object value = entry.getValue();
				if (value instanceof String[])
					value = ((String[]) value)[0];
				transformer.setParameter(key.substring(2), value);
			}
		}
		// Transformer t = factory.newTransformer();
		// t.transform(domSource, new StreamResult(System.err));
		transformer.transform(domSource, res);
	}

	public static class GetMapRequest extends WMS1_0_0.GetMapRequest {

		public GetMapRequest(URL onlineResource) {
			super(onlineResource);
		}

		protected void initRequest() {
			setProperty(REQUEST, "GetMap");
		}

		protected void initVersion() {
			setProperty(VERSION, "1.1.0");
		}

		protected String getRequestFormat(String format) {
			return format;
		}

		protected String getRequestException(String exception) {
			return exception;
		}

		protected String processKey(String key) {
			return key.trim().toUpperCase();
		}
	}

	public static class GetLegendGraphicRequest extends AbstractGetLegendGraphicRequest {

		public GetLegendGraphicRequest(URL onlineResource) {
			super(onlineResource);
		}

		protected void initVersion() {
			setProperty(VERSION, "1.1.0");
		}

		public Response createResponse(String contentType, InputStream inputStream) throws ServiceException, IOException {
			return new GetLegendGraphicResponse(contentType, inputStream);
		}
	}
}
