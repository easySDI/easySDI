/**
 * 
 */
package org.easysdi.proxy.wms.thread;

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.ListIterator;
import java.util.Set;
import java.util.TreeMap;
import java.util.Vector;
import java.util.logging.Level;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.configuration.ProxyLayer;
import org.easysdi.proxy.wms.WMSProxyServlet;
import org.easysdi.xml.documents.RemoteServerInfo;
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
public class WMSProxyServerGetMapThread extends Thread {

	List<WMSProxyLayerThread> layerThreadList = new Vector<WMSProxyLayerThread>();

	WMSProxyServlet servlet;
	String paramUrlBase;
	TreeMap<Integer, ProxyLayer> layers;
	TreeMap<Integer, ProxyLayer> styles;
	RemoteServerInfo remoteServer;
	HttpServletResponse resp;

	// **************************************************************************************
	public WMSProxyServerGetMapThread(	WMSProxyServlet servlet, 
										String paramUrlBase,
										TreeMap<Integer, ProxyLayer> layers,
										TreeMap<Integer, ProxyLayer> styles,
										RemoteServerInfo remoteServer, 
										HttpServletResponse resp) {
		this.servlet = servlet;
		this.paramUrlBase = paramUrlBase;
		this.layers = layers;
		this.styles = styles;
		this.remoteServer = remoteServer;
		this.resp = resp;
	}

	public void run() {

		try {
			servlet.dump("DEBUG", "Thread Server: " + remoteServer.getUrl() + " work begin");
			
			//Layer order
			List<TreeMap<Integer, ProxyLayer>> layerGroupList = new ArrayList<TreeMap<Integer,ProxyLayer>>() ;
			TreeMap<Integer, ProxyLayer> layerGroup = new TreeMap<Integer, ProxyLayer>();
			Integer previous = 0;
			Iterator<Integer> itKey = layers.keySet().iterator();
			while (itKey.hasNext()){
				Integer key = itKey.next();
				if(key == previous +1){
					layerGroup.put(key, layers.get(key));
				}else{
					layerGroupList.add(layerGroup);
					layerGroup = new TreeMap<Integer, ProxyLayer>();
					layerGroup.put(key, layers.get(key));
				}
				previous = key;
			}
			layerGroupList.add(layerGroup);
			
			Iterator<TreeMap<Integer, ProxyLayer>> itLGL = layerGroupList.iterator();
			while (itLGL.hasNext()){
				TreeMap<Integer, ProxyLayer> continuousLayers = itLGL.next();
				if(continuousLayers.size()==1){
					//Send the request
					servlet.dump("requestPreTraitementGET send request multiLayer to thread server " + remoteServer.getUrl());
					WMSProxyLayerThread th = new WMSProxyLayerThread(servlet,paramUrlBase,continuousLayers,styles, remoteServer,resp);
					th.start();
					layerThreadList.add(th);
				}else{
					//Compare the filter
				}
				
			}
			
			//Wait for thread answer
			for (int i = 0; i < layerThreadList.size(); i++) {
				layerThreadList.get(i).join();
			}
			
			
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
			
			// Fin de Debug
			dump("DEBUG", "Thread Server: " + getRemoteServerUrl(j) + " work finished");
		} catch (Exception e) {
			resp.setHeader("easysdi-proxy-error-occured", "true");
			dump("ERROR", "Server Thread " + getRemoteServerUrl(j) + " :" + e.getMessage());
			e.printStackTrace();
		}

			
	}

}
