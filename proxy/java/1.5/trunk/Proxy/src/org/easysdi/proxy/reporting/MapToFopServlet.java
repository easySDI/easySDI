package org.easysdi.proxy.reporting;

import java.io.IOException;
import java.util.Map;

import javax.naming.OperationNotSupportedException;
import javax.servlet.ServletContext;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.TransformerException;

import org.easysdi.security.JoomlaProvider;
import org.geotools.data.ows.Layer;
import org.geotools.ows.ServiceException;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.web.context.WebApplicationContext;
import org.springframework.web.context.support.WebApplicationContextUtils;
import org.xml.sax.SAXException;

public class MapToFopServlet extends HttpServlet {

	/**
	 * 
	 */
	private static final long serialVersionUID = 8400303568411604114L;
	private Logger logger = LoggerFactory.getLogger("maptofop");

	@Override
	protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		this.doGet(req, resp);
	}

	@Override
	protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
		MapToFop mf = new MapToFop(getServletContext().getRealPath("/WEB-INF/fop"));
		ServletContext context = getServletContext();
		WebApplicationContext applicationContext = WebApplicationContextUtils.getWebApplicationContext(context);
		JoomlaProvider provider = applicationContext.getBean(JoomlaProvider.class);
		try {
			mf.initWmsWfs(provider);
		} catch (ServiceException e1) {
			logger.error("Le service d'impression PDF n'a pas pu être initialisé", e1);
		}
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
