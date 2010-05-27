package org.easysdi.proxy.reporting;

import java.io.IOException;
import java.util.HashMap;
import java.util.Map;

import javax.naming.OperationNotSupportedException;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.TransformerException;

import org.geotools.data.ows.Layer;
import org.geotools.ows.ServiceException;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.xml.sax.SAXException;

public class MapToFopServlet extends HttpServlet {

	/**
	 * 
	 */
	private static final long serialVersionUID = 8400303568411604114L;
	private Map<String, MapToFop> services = new HashMap<String, MapToFop>();

	@Override
	protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		this.doGet(req, resp);
	}

	@Override
	protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		String wms = req.getParameter("wms");
		if (wms == null)
			throw new ServletException("WMS URL must be set !");
		String wfs = req.getParameter("wfs");
		if (wfs == null)
			throw new ServletException("WFS URL must be set !");
		String key = wms + wfs;

		MapToFop mf = services.get(key);
		if (mf == null) {
			mf = new MapToFop(getServletContext().getRealPath("/WEB-INF/fop"));
			services.put(key, mf);
		}

		Authentication token = SecurityContextHolder.getContext().getAuthentication();
		if (token != null && token.getPrincipal() != null && token.getCredentials() != null) {
			mf.setCredentials(token.getPrincipal().toString(), token.getCredentials().toString());
		}

		try {
			mf.setUrlWMS(wms);
		} catch (ServiceException e) {
			e.printStackTrace();
		}
		mf.setUrlWFS(wfs);

		String baseLayerName = req.getParameter("baseLayer");
		Layer baseLayer = new Layer();
		baseLayer.setName(baseLayerName);
		String epsgCode = req.getParameter("epsg");
		Double minX = Double.valueOf(req.getParameter("minX"));
		Double minY = Double.valueOf(req.getParameter("minY"));
		Double maxX = Double.valueOf(req.getParameter("maxX"));
		Double maxY = Double.valueOf(req.getParameter("maxY"));
		int width = Integer.valueOf(req.getParameter("w"));
		int height = Integer.valueOf(req.getParameter("h"));
		String overlayNames = req.getParameter("overlays");
		String title = req.getParameter("title");
		String xsltPath = req.getParameter("xslt");
		if (xsltPath == null)
			xsltPath = "fop-map-multi-FT-default.xslt";
		try {
			resp.setContentType("application/pdf");
			resp.setHeader("Content-Disposition", "attachment; filename=\"easysdi-map.pdf\"");
			Map<String, Object> params = req.getParameterMap();
			mf.toFOP(baseLayerName, epsgCode, minX, minY, maxX, maxY, overlayNames, "png", title, width, height, xsltPath, resp.getOutputStream(), params);
		} catch (OperationNotSupportedException e) {
			e.printStackTrace();
		} catch (TransformerException e) {
			e.printStackTrace();
		} catch (SAXException e) {
			e.printStackTrace();
		} catch (ParserConfigurationException e) {
			e.printStackTrace();
		}
	}
}
