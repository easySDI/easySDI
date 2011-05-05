/**
 * 
 */
package org.easysdi.proxy.wms.thread;

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.util.List;
import java.util.Vector;
import java.util.logging.Level;

import javax.servlet.http.HttpServletResponse;

import org.geotools.geometry.jts.JTS;
import org.geotools.referencing.CRS;
import org.geotools.xml.DocumentFactory;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.operation.MathTransform;

import com.vividsolutions.jts.geom.Coordinate;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.geom.GeometryFactory;
import com.vividsolutions.jts.geom.IntersectionMatrix;
import com.vividsolutions.jts.io.WKTReader;

/**
 * @author Helene
 *
 */
public class WMSProxyServerGetFeatureInfoThread extends Thread {

	protected Vector<String> serverFilePathList = new Vector<String>();
	protected Vector<String> serverLayerFilePathList = new Vector<String>();

	String operation;
	String paramUrl;
	List layerToKeepList;
	int iServer;
	List stylesToKeepList;
	String paramUrlBase;
	String width;
	String height;
	String format;
	int j;
	int layerOrder;
	HttpServletResponse resp;

	// **************************************************************************************
	public WMSProxyServerGetFeatureInfoThread(String pOperation, String pParamUrl, List pLayerToKeepList, int pIServer, List pStylesToKeepList, String pParamUrlBase,
			int pJ, String pWidth, String pHeight, String pFormat, HttpServletResponse res) {
		operation = pOperation;
		paramUrl = pParamUrl;
		layerToKeepList = pLayerToKeepList;
		iServer = pIServer;
		stylesToKeepList = pStylesToKeepList;
		paramUrlBase = pParamUrlBase;
		j = pJ;
		width = pWidth;
		height = pHeight;
		format = pFormat;
		resp = res;
	}

	// **************************************************************************************
	public void run() {

		try {
			dump("DEBUG", "Thread Server: " + getRemoteServerUrl(j) + " work begin");
			List<SendLayerThread> layerThreadList = new Vector<SendLayerThread>();

			if ("GetMap".equalsIgnoreCase(operation) || "map".equalsIgnoreCase(operation)) {
				// Test si les filtres des layers sont différents
				// les uns des autres:
				// ->envoi de 1 "thread layer" par groupe de couches
				// ayant un "policy filter" identique
				// for(int
				// iLayers=0;iLayers<layerToKeepList.size();iLayers++)
				while (layerToKeepList.size() > 0) {
					List<String> layerToKeepListPerThread = new Vector<String>();
					List<String> stylesToKeepListPerThread = new Vector<String>();

					String filter = getLayerFilter(getRemoteServerUrl(j), (String) layerToKeepList.get(0));

					layerToKeepListPerThread.add((String) layerToKeepList.remove(0));
					stylesToKeepListPerThread.add((String) stylesToKeepList.remove(0));

					// Création du polygon A à partir du filtre de
					// iLayer
					Boolean isNoFilterA = false;
					Geometry polygonA = null;

					// Par la même occasion, vérification que la
					// bbox de la requête utilisateur est dans le
					// filter de layerToKeepList(0)
					// Sinon les layers ayant le même filtre ne sont
					// pas conservées dans la requête.
					System.setProperty("org.geotools.referencing.forceXY", "true");
					String[] s = bbox.split(",");
					boolean iscoveredByfilter = true;

					if (filter != null && filter.length() > 0) {
						InputStream bis = new ByteArrayInputStream(filter.getBytes());
						Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);
						WKTReader wktReader = new WKTReader();

						polygonA = wktReader.read(object.toString());

						Geometry polygon = wktReader.read(object.toString());
						filter.indexOf("srsName");
						String srs = filter.substring(filter.indexOf("srsName"));
						srs = srs.substring(srs.indexOf("\"") + 1);
						srs = srs.substring(0, srs.indexOf("\""));
						polygon.setSRID(Integer.parseInt(srs.substring(5)));
						CoordinateReferenceSystem sourceCRS = CRS.decode("EPSG:" + (new Integer(polygon.getSRID())).toString());
						CoordinateReferenceSystem targetCRS = CRS.decode(srsName);
						double x1 = Double.parseDouble(s[0]);
						double y1 = Double.parseDouble(s[1]);
						double x2 = Double.parseDouble(s[2]);
						double y2 = Double.parseDouble(s[3]);
						MathTransform a = CRS.findMathTransform(sourceCRS, targetCRS, false);
						polygon = JTS.transform(polygon, a);
						polygon.setSRID(Integer.parseInt(srs.substring(5)));
						Coordinate[] c = { new Coordinate(x1, y1), new Coordinate(x1, y1), new Coordinate(x2, y1), new Coordinate(x2, y2),
								new Coordinate(x1, y2), new Coordinate(x1, y1) };
						GeometryFactory gf = new GeometryFactory();
						Geometry bboxGeom = gf.createPolygon(gf.createLinearRing(c), null);
						bboxGeom.setSRID(Integer.parseInt(srs.substring(5)));
						IntersectionMatrix mat1 = bboxGeom.relate(polygon);
						IntersectionMatrix mat2 = polygon.relate(bboxGeom);

						if (mat1.isIntersects() || mat2.isIntersects() || bboxGeom.overlaps(polygon) || polygon.overlaps(bboxGeom)
								|| polygon.coveredBy(bboxGeom) || bboxGeom.coveredBy(polygon) || bboxGeom.touches(polygon)
								|| polygon.touches(bboxGeom) || bboxGeom.intersects((polygon)) || bboxGeom.covers((polygon))
								|| bboxGeom.crosses((polygon)) || polygon.crosses(bboxGeom) || polygon.intersects((bboxGeom))
								|| polygon.covers((bboxGeom))) {
							iscoveredByfilter = true;
						} else {
							iscoveredByfilter = false;
						}
					} else {
						isNoFilterA = true;
					}
					for (int k = 0; k < layerToKeepList.size(); k++) {
						// Création du polygon B à partir du filtre
						// de iLayer
						Boolean isNoFilterB = false;
						filter = getLayerFilter(getRemoteServerUrl(j), (String) layerToKeepList.get(k));
						Geometry polygonB = null;
						if (filter != null && filter.length() > 0) {
							InputStream bis = new ByteArrayInputStream(filter.getBytes());
							Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);
							WKTReader wktReader = new WKTReader();
							polygonB = wktReader.read(object.toString());
						} else {
							isNoFilterB = true;
						}

						// Comparaison des filtres
						if (!isNoFilterA && !isNoFilterB) {
							if (polygonA.equalsExact(polygonB)) {
								layerToKeepListPerThread.add((String) layerToKeepList.remove(k));
								stylesToKeepListPerThread.add((String) stylesToKeepList.remove(k));
								k--;
							}
						} else if (isNoFilterA && isNoFilterB) {
							layerToKeepListPerThread.add((String) layerToKeepList.remove(k));
							stylesToKeepListPerThread.add((String) stylesToKeepList.remove(k));
							k--;
						}
					}

					if (iscoveredByfilter) {
						// Création et lancement des threads sur
						// serveur j pour chaque groupe de couches
						// (à filtres identiques)
						dump("requestPreTraitementGET send request multiLayer to thread server " + getRemoteServerUrl(j));
						SendLayerThread th = new SendLayerThread(layerThreadList.size(), layerToKeepListPerThread, stylesToKeepListPerThread,
								paramUrlBase, j, width, height, format, resp);
						th.start();
						layerThreadList.add(th);
					} else {
						//HVH - 18.12.2010 : 
						//Création d'une image vide comme réponse à la requête en dehors du filtre
						generateEmptyImage(width, height, format, true, j, resp);
						dump("INFO", "Thread Layers group: " + layerToKeepListPerThread.get(0) + " work finished on server "
								+ getRemoteServerUrl(j) + " : bbox not covered by policy filter.");
					}
				}
				// Récupération du résultat des threads sur serveur
				// j
				// Autant de filePath à ajouter que de couches
				for (int i = 0; i < layerThreadList.size(); i++) {
					layerThreadList.get(i).join();

					// Si une réponse a bien été renvoyée par le
					// thread i
					if (!serverFilePathList.isEmpty() && !((String) serverFilePathList.get(i)).equals("")) {
						synchronized (wmsFilePathList) {
							synchronized (layerFilePathList) {
								synchronized (serverUrlPerfilePathList) {
									// Insert les réponses
									layerFilePathList.put(layerOrder, serverLayerFilePathList.get(i));
									serverUrlPerfilePathList.put(layerOrder, getRemoteServerUrl(j));
									wmsFilePathList.put(layerOrder, serverFilePathList.get(i));
								}
							}
						}
					}
				}
			} else if ("GetCapabilities".equalsIgnoreCase(operation) || "capabilities".equalsIgnoreCase(operation)) {
				if (paramUrlBase.toUpperCase().indexOf("SERVICE=") == -1)
				{
					paramUrlBase += "&SERVICE=WMS";
				}
				String filePath = sendData("GET", getRemoteServerUrl(j), paramUrlBase);

				synchronized (wmsFilePathList) {
					synchronized (layerFilePathList) {
						synchronized (serverUrlPerfilePathList) {
							// Insert les réponses
							dump("requestPreTraitementGET save response capabilities from thread server " + getRemoteServerUrl(j));
							layerFilePathList.put(layerOrder, "");
							serverUrlPerfilePathList.put(layerOrder, getRemoteServerUrl(j));
							wmsFilePathList.put(layerOrder, filePath);
						}
					}
				}
			}
			// Debug tb 04.11.2009
			else if ("GetLegendGraphic".equalsIgnoreCase(operation)) {
				String filePath = sendData("GET", getRemoteServerUrl(j), paramUrlBase);

				synchronized (wmsFilePathList) {
					synchronized (layerFilePathList) {
						synchronized (serverUrlPerfilePathList) {
							// Insert les réponses
							dump("requestPreTraitementGET save response legendGraphic from thread server " + getRemoteServerUrl(j));
							layerFilePathList.put(layerOrder, "");
							serverUrlPerfilePathList.put(layerOrder, getRemoteServerUrl(j));
							wmsFilePathList.put(layerOrder, filePath);
						}
					}
				}
			}
			// Fin de Debug
			// Debug tb 15.01.2010
			else if ("GetFeatureInfo".equalsIgnoreCase(operation)) {
				// Debug tb 18.01.2010
				String queryLayersUrl = "QUERY_LAYERS=" + layerToKeepList.get(0);
				String layersUrl = "LAYERS=" + layerToKeepList.get(0);
				for (int n = 1; n < layerToKeepList.size(); n++) {
					queryLayersUrl = queryLayersUrl + "," + layerToKeepList.get(n);
					layersUrl = layersUrl + "," + layerToKeepList.get(n);
				}
				String stylesUrl = "STYLES=" + stylesToKeepList.get(0);
				for (int n = 1; n < stylesToKeepList.size(); n++) {
					stylesUrl = stylesUrl + "," + stylesToKeepList.get(n);
				}

				String filePath = sendData("GET", getRemoteServerUrl(j), paramUrlBase + queryLayersUrl + "&" + layersUrl + "&" + stylesUrl);
				// Fin de Debug
				synchronized (wmsFilePathList) {
					synchronized (layerFilePathList) {
						synchronized (serverUrlPerfilePathList) {
							// Insert les réponses
							dump("requestPreTraitementGET save response GetFeatureInfo from thread server " + getRemoteServerUrl(j));
							layerFilePathList.put(layerOrder, "");
							serverUrlPerfilePathList.put(layerOrder, getRemoteServerUrl(j));
							wmsFilePathList.put(layerOrder, filePath);
						}
					}
				}
			}
			// Fin de Debug
			dump("DEBUG", "Thread Server: " + getRemoteServerUrl(j) + " work finished");
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			dump("ERROR", "Server Thread " + getRemoteServerUrl(j) + " :" + e.getMessage());
			e.printStackTrace();
		}

			
	}

}
