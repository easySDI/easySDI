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
package org.easysdi.proxy.wms;

import java.awt.AlphaComposite;
import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.Transparency;
import java.awt.geom.Rectangle2D;
import java.awt.image.BufferedImage;
import java.io.BufferedOutputStream;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.PrintWriter;
import java.net.URI;
import java.net.URLDecoder;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.TreeMap;
import java.util.UUID;
import java.util.Vector;
import java.util.logging.Level;

import javax.imageio.ImageIO;
import javax.imageio.ImageWriter;
import javax.imageio.stream.FileImageOutputStream;
import javax.imageio.stream.MemoryCacheImageOutputStream;
import javax.naming.NoPermissionException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathFactory;

import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.exception.AvailabilityPeriodException;
import org.easysdi.proxy.policy.Operation;
import org.easysdi.xml.documents.RemoteServerInfo;
import org.easysdi.xml.resolver.ResourceResolver;
import org.geotools.data.ows.CRSEnvelope;
import org.geotools.data.ows.Layer;
import org.geotools.data.ows.WMSCapabilities;
import org.geotools.data.wms.xml.WMSSchema;
import org.geotools.feature.AttributeType;
import org.geotools.feature.AttributeTypeFactory;
import org.geotools.feature.FeatureType;
import org.geotools.feature.FeatureTypes;
import org.geotools.feature.GeometryAttributeType;
import org.geotools.feature.type.GeometricAttributeType;
import org.geotools.geometry.jts.JTS;
import org.geotools.geometry.jts.ReferencedEnvelope;
import org.geotools.referencing.CRS;
import org.geotools.referencing.NamedIdentifier;
import org.geotools.renderer.lite.RendererUtilities;
import org.geotools.xml.DocumentFactory;
import org.geotools.xml.handlers.DocumentHandler;
import org.integratedmodelling.geospace.gis.FeatureRasterizer;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.operation.MathTransform;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.ls.LSSerializer;
import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import com.vividsolutions.jts.geom.Coordinate;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.geom.GeometryFactory;
import com.vividsolutions.jts.geom.IntersectionMatrix;
import com.vividsolutions.jts.io.WKTReader;

/**
 * If no xslt is found in the path, generate the default one that will change
 * the IP address and remove the wrong operation
 * 
 * @author rmi
 */
public class WMSProxyServlet extends ProxyServlet {

	// ***************************************************************************************************************************************
	// Debug tb 08.07.2009
	private Map<Integer, String> serverUrlPerfilePathList = new TreeMap<Integer, String>(); // Url
	// du
	// serveur
	// ayant
	// renvoyé
	// la
	// réponse
	// i.
	// private Vector<String> filterPerFilePathList = new Vector<String>(); //
	// Filtre du groupe de layers ayant renvoyé la réponse i.
	// Fin de Debug
	private String layers;
	private String styles;

	// ***************************************************************************************************************************************

	protected StringBuffer buildCapabilitiesXSLT(HttpServletRequest req, int remoteServerIndex, String version) {

		try {
			String user = "";
			if (req.getUserPrincipal() != null) {
				user = req.getUserPrincipal().getName();
			}

			String url = getServletUrl(req);

			try {
				StringBuffer WMSCapabilities111 = new StringBuffer();

				WMSCapabilities111
						.append("<xsl:stylesheet version=\"1.00\" xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");

				// Debug tb 19.11.2009
				if (!"100".equalsIgnoreCase(version)) {
					WMSCapabilities111.append("<xsl:template match=\"OnlineResource/@xlink:href\">");
					WMSCapabilities111.append("<xsl:param name=\"thisValue\">");
					WMSCapabilities111.append("<xsl:value-of select=\".\"/>");
					WMSCapabilities111.append("</xsl:param>");
					WMSCapabilities111.append("<xsl:attribute name=\"xlink:href\">");
					WMSCapabilities111.append(url);
					// Changer seulement la partie racine de l'URL, pas les
					// param après '?'
					WMSCapabilities111.append("<xsl:value-of select=\"substring-after($thisValue,'" + getRemoteServerUrl(remoteServerIndex) + "')\"/>");
					WMSCapabilities111.append("</xsl:attribute>");
					WMSCapabilities111.append("</xsl:template>");
				} else {
					// Add change on wmtver=1.0.0&request=capabilities support
					StringBuffer WMSCapabilities100 = new StringBuffer();
					WMSCapabilities100.append("<xsl:template match=\"OnlineResource\">");
					WMSCapabilities100.append("<xsl:param name=\"thisValue\">");
					WMSCapabilities100.append("<xsl:value-of select=\".\"/>");
					WMSCapabilities100.append("</xsl:param>");
					WMSCapabilities100.append("<OnlineResource>");
					WMSCapabilities100.append(url);
					// Changer seulement la partie racine de l'URL, pas les
					// param après '?'
					WMSCapabilities100.append("<xsl:value-of select=\"substring-after($thisValue,'" + getRemoteServerUrl(remoteServerIndex) + "')\"/>");
					WMSCapabilities100.append("</OnlineResource>");
					WMSCapabilities100.append("</xsl:template>");
					WMSCapabilities100.append("<xsl:template match=\"@onlineResource\">");
					WMSCapabilities100.append("<xsl:param name=\"thisValue\">");
					WMSCapabilities100.append("<xsl:value-of select=\".\"/>");
					WMSCapabilities100.append("</xsl:param>");
					WMSCapabilities100.append("<xsl:attribute name=\"onlineResource\">");
					WMSCapabilities100.append(url);
					// Changer seulement la partie racine de l'URL, pas les
					// param après '?'
					WMSCapabilities100.append("<xsl:value-of select=\"substring-after($thisValue,'" + getRemoteServerUrl(remoteServerIndex) + "')\"/>");
					WMSCapabilities100.append("</xsl:attribute>");
					WMSCapabilities100.append("</xsl:template>");
					WMSCapabilities111.append(WMSCapabilities100);
				}
				// Fin de Debug

				// Filtrage xsl des opérations
				if (hasPolicy) {
					if (!policy.getOperations().isAll()) {
						List<Operation> operationList = policy.getOperations().getOperation();
						for (int i = 0; i < operationList.size(); i++) {
							if (operationList.get(i).getName() != null) {
								WMSCapabilities111.append("<xsl:template match=\"Capability/Request/");

								WMSCapabilities111.append(operationList.get(i).getName());
								WMSCapabilities111.append("\">");
								WMSCapabilities111.append("<!-- Copy the current node -->");
								WMSCapabilities111.append("<xsl:copy>");
								WMSCapabilities111.append("<!-- Including any attributes it has and any child nodes -->");
								WMSCapabilities111.append("<xsl:apply-templates select=\"@*|node()\"/>");
								WMSCapabilities111.append("</xsl:copy>");

								WMSCapabilities111.append("</xsl:template>");
							}
						}
					}
				}

				Map hints = new HashMap();
				// hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);
				hints.put(DocumentHandler.DEFAULT_NAMESPACE_HINT_KEY, WMSSchema.getInstance());
				hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);

				WMSCapabilities capa = (WMSCapabilities) DocumentFactory.getInstance(new File(wmsFilePathList.get(remoteServerIndex).toArray(new String[1])[0])
						.toURI(), hints, Level.WARNING);

				// Filtrage xsl des layers
				if (hasPolicy) {
					Iterator<Layer> itLayer = capa.getLayerList().iterator();
					while (itLayer.hasNext()) {
						Layer l = (Layer) itLayer.next();
						// Debug tb 03.07.2009
						String tmpFT = l.getName();
						if (tmpFT != null) {
							String[] s = tmpFT.split(":");
							tmpFT = s[s.length - 1];
						}
						boolean allowed = isLayerAllowed(tmpFT, getRemoteServerUrl(remoteServerIndex));
						if (!allowed)
						// Fin de Debug
						// if (!isLayerAllowed(l.getName(),
						// getRemoteServerUrl(remoteServerIndex)))
						{
							// Si couche pas permise alors on l'enlève
							WMSCapabilities111.append("<xsl:template match=\"//Layer[starts-with(Name,'" + l.getName() + "')]");
							WMSCapabilities111.append("\"></xsl:template>");
						}
					}
				}

				// Debug tb 03.07.2009
				// -> le prefix est déjà intégré dans l.getName!
				// //Add the WMSxx_ Prefix before the name of the layer.
				// //This prefix will be used to find to witch remote server the
				// layer belongs.
				// if
				// (getRemoteServerInfo(remoteServerIndex).getPrefix().length()>0)
				// {
				// WMSCapabilities111.append("<xsl:template match=\"//Layer/Name\">");
				// WMSCapabilities111.append("<Name>"+getRemoteServerInfo(remoteServerIndex).getPrefix()+"<xsl:value-of select=\".\"/> </Name>");
				// WMSCapabilities111.append("</xsl:template>");
				// }
				// Fin de Debug

				WMSCapabilities111.append("  <!-- Whenever you match any node or any attribute -->");
				WMSCapabilities111.append("<xsl:template match=\"node()|@*\">");
				WMSCapabilities111.append("<!-- Copy the current node -->");
				WMSCapabilities111.append("<xsl:copy>");
				WMSCapabilities111.append("<!-- Including any attributes it has and any child nodes -->");
				WMSCapabilities111.append("<xsl:apply-templates select=\"@*|node()\"/>");
				WMSCapabilities111.append("</xsl:copy>");
				WMSCapabilities111.append("</xsl:template>");
				WMSCapabilities111.append("</xsl:stylesheet>");

				return WMSCapabilities111;
			} catch (Exception e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
				dump("ERROR", e.getMessage());
			}

			// If something goes wrong, an empty stylesheet is returned.
			StringBuffer sb = new StringBuffer();
			return sb
					.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}

		// If something goes wrong, an empty stylesheet is returned.
		StringBuffer sb = new StringBuffer();
		return sb
				.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
	}

	// ***************************************************************************************************************************************

	public void transform(String version, String currentOperation, HttpServletRequest req, HttpServletResponse resp) {

		try {
			// Vérifie et prépare l'application d'un fichier xslt utilisateur
			String userXsltPath = getConfiguration().getXsltPath();
			if (req.getUserPrincipal() != null) {
				userXsltPath = userXsltPath + "/" + req.getUserPrincipal().getName() + "/";
			}

			userXsltPath = userXsltPath + "/" + version + "/" + currentOperation + ".xsl";
			String globalXsltPath = getConfiguration().getXsltPath() + "/" + version + "/" + currentOperation + ".xsl";
			;

			File xsltFile = new File(userXsltPath);
			boolean isPostTreat = false;
			if (!xsltFile.exists()) {
				dump("Postreatment file " + xsltFile.toString() + "does not exist");
				xsltFile = new File(globalXsltPath);
				if (xsltFile.exists()) {
					isPostTreat = true;
				} else {
					dump("Postreatment file " + xsltFile.toString() + "does not exist");
				}
			} else {
				isPostTreat = true;
			}

			// Transforms the results using a xslt before sending the response
			// back
			// InputStream xml = new FileInputStream(wmsFilePathList.get(0));
			Transformer transformer = null;
			TransformerFactory tFactory = TransformerFactory.newInstance();

			// Debug tb 21.12.2009
			ByteArrayOutputStream tempOut = null; // Remplace l'utilisation de
			// tempFile!!!
			// File tempFile = null;
			// if (isXML(responseContentType))
			// {
			// //tempFile = createTempFile(UUID.randomUUID().toString(),
			// getExtension(responseContentType));
			// }
			// else
			// {
			// if (wmsFilePathList.size()>0)
			// {
			// if(wmsFilePathList.get(0)!=null) tempFile = new
			// File(wmsFilePathList.get(0));
			// }
			// }

			// ********************************************************************************************************************
			// Postraitement des réponses à la requête contenue dans les
			// fichiers filePathList.get(i)
			if (currentOperation != null) {
				// Pour une requête utilisateur de type: Capabilities
				// ************************************************************
				if ("GetCapabilities".equalsIgnoreCase(currentOperation) || "capabilities".equalsIgnoreCase(currentOperation)) {
					dump("transform begin GetCapabilities operation");

					// Contains the list of temporary modified Capabilities
					// files.
					List<File> tempFileCapa = new Vector<File>();

					// Boucle sur les fichiers réponses
					for (int iFilePath = 0; iFilePath < wmsFilePathList.size(); iFilePath++) {
						tempFileCapa.add(createTempFile("transform_GetCapabilities_" + UUID.randomUUID().toString(), ".xml"));
						FileOutputStream tempFosCapa = new FileOutputStream(tempFileCapa.get(iFilePath));

						StringBuffer sb = buildCapabilitiesXSLT(req, iFilePath, version);
						InputStream xslt = new ByteArrayInputStream(sb.toString().getBytes());

						InputSource inputSource = new InputSource(new FileInputStream(wmsFilePathList.get(iFilePath).toArray(new String[1])[0]));

						XMLReader xmlReader = XMLReaderFactory.createXMLReader();

						String user = (String) getUsername(getRemoteServerUrl(iFilePath));
						String password = (String) getPassword(getRemoteServerUrl(iFilePath));
						if (user != null && user.length() > 0) {
							ResourceResolver rr = new ResourceResolver(user, password);
							xmlReader.setEntityResolver(rr);
						}
						// END Added to hook in my EntityResolver
						SAXSource saxSource = new SAXSource(xmlReader, inputSource);

						transformer = tFactory.newTransformer(new StreamSource(xslt));

						// Write the result in a temporary file
						dump("transform begin xslt transform to response file " + iFilePath);
						transformer.transform(saxSource, new StreamResult(tempFosCapa));
						// Debug tb 06.07.2009
						tempFosCapa.flush();
						tempFosCapa.close();
						// Fin de Debug
						dump("transform end xslt transform to response file " + iFilePath);
					}

					// Merge the results of all the capabilities and return it
					// into a single file
					dump("transform begin mergeCapabilities");
					tempOut = mergeCapabilities(tempFileCapa);
					// tempFile = mergeCapabilities(tempFileCapa);
					dump("transform end mergeCapabilities");

					dump("transform end GetCapabilities operation");
				}
				// Pour une requête utilisateur de type: Map
				// ************************************************************
				else if (currentOperation.equalsIgnoreCase("GetMap") || "Map".equalsIgnoreCase(currentOperation)) {
					dump("transform begin GetMap operation");

					boolean isTransparent = isAcceptingTransparency(responseContentType);
					// dump("DEBUG","LAYER N°:"+0+" "+layerFilePathList.get(0));

					dump("transform begin filterImage to layer " + 0);
					// Debug tb 08.07.2009
					BufferedImage imageSource = null;
					// Si les threads ont renvoyés une réponse
					if (serverUrlPerfilePathList.size() > 0) {
						// imageSource =
						// filterImage(getLayerFilter(serverUrlPerfilePathList.get(0),
						// layerFilePathList.get(0)), wmsFilePathList.get(0),
						// isTransparent);
						// Fin de Debug
						Graphics2D g = null;
						dump("transform end filterImage to layer " + 0);

						// Boucle sur les fichiers réponses
						TreeMap<Integer, Collection<String>> tm = new TreeMap<Integer, Collection<String>>(wmsFilePathList.asMap());
						for (Map.Entry<Integer, Collection<String>> e : tm.entrySet()) {
							// dump("DEBUG","LAYER N°:"+iFilePath+" "+layerFilePathList.get(iFilePath));
							int iFilePath = e.getKey();
							dump("transform begin filterImage to layer " + iFilePath);
							// Debug tb 08.07.2009
							BufferedImage image = filterImage(getLayerFilter(serverUrlPerfilePathList.get(iFilePath), layerFilePathList.get(iFilePath)),
									wmsFilePathList.get(iFilePath), isTransparent);
							// Fin de Debug
							if (g == null) {
								imageSource = image;
								g = imageSource.createGraphics();
							} else if (image != null)
								g.drawImage(image, null, 0, 0);
							dump("transform end filterImage to layer " + iFilePath);
						}
						if (g != null)
							g.dispose();
						// Debug tb 11.08.2009
					}
					// Si aucune requête n'a été envoyé au serveur, retourne:
					// empty image
					else {
						imageSource = ImageIO.read(new File(wmsFilePathList.get(0).toArray(new String[1])[0]));
					}
					// Fin de Debug

					// Etape nécessaire car "resp.getOutputStream()" ne peux pas
					// lire directement le flux d' "imageSource"
					Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(responseContentType);
					if (iter.hasNext()) {
						ImageWriter writer = (ImageWriter) iter.next();
						tempOut = new ByteArrayOutputStream();
						// tempFile =
						// createTempFile("transform_GetMap_"+UUID.randomUUID().toString(),
						// getExtension(responseContentType));
						// FileImageOutputStream output = new
						// FileImageOutputStream(tempFile);
						writer.setOutput(new MemoryCacheImageOutputStream(tempOut));
						// writer.setOutput(output);
						dump("transform begin write tempOut");
						writer.write(imageSource);
						// Debug tb 06.07.2009
						dump("transform end write tempOut");
						// output.flush();
						// output.close();
						// Fin de Debug
					}
					dump("transform end GetMap operation");
				}
				// Debug tb 04.11.2009
				// Pour une requête utilisateur de type: GetLegendGraphic
				// ********************************************************
				else if (currentOperation.equalsIgnoreCase("GetLegendGraphic")) {
					dump("transform begin GetLegendGraphic operation");

					// boolean isTransparent=
					// isAcceptingTransparency(responseContentType);

					dump("transform begin add Legend Image " + 0);
					Graphics2D g = null;
					dump("transform end filterImage to layer " + 0);
					// Boucle sur les fichiers réponses

					BufferedImage imageSource = null;
					String format = "jpeg";
					for (Map.Entry<Integer, String> e : wmsFilePathList.entries()) {
						// dump("DEBUG","LAYER N°:"+iFilePath+" "+layerFilePathList.get(iFilePath));
						int iFilePath = e.getKey();
						dump("transform begin Legend Image " + iFilePath);
						// Debug tb 08.07.2009

						BufferedImage image = ImageIO.read(new File(e.getValue()));
						if (image != null && image.getWidth() > 1) {
							int type = BufferedImage.TYPE_INT_BGR;
							if (image.getTransparency() == Transparency.BITMASK) {
								type = BufferedImage.BITMASK;
								format = "png";
							} else if (image.getTransparency() == Transparency.TRANSLUCENT){
								type = BufferedImage.TRANSLUCENT;
								format = "png";

							}
							BufferedImage canvas = new BufferedImage(image.getWidth(), image.getHeight(), type);
							canvas.getGraphics().drawImage(image, 0, 0, null);

							// Fin de Debug
							if (g == null) {
								imageSource = canvas;
								g = imageSource.createGraphics();
							} else if (image != null)
								g.drawImage(canvas, null, 0, 0);
							dump("transform end add Legend Image " + iFilePath);
						}
					}
					if (g != null)
						g.dispose();

					// Si aucune requête n'a été envoyé au serveur, retourne:
					// empty image
					else {
						format = "png";
						imageSource = new BufferedImage(32, 32, BufferedImage.TRANSLUCENT);
					}
					tempOut = new ByteArrayOutputStream();
					ImageIO.write(imageSource, format, tempOut);

					// Etape nécessaire car "resp.getOutputStream()" ne peux pas
					// lire directement le flux d' "imageSource"
					// Iterator<ImageWriter> iter =
					// ImageIO.getImageWritersByMIMEType(responseContentType);

					// if (iter.hasNext()) {
					// ImageWriter writer = (ImageWriter) iter.next();

					// tempFile =
					// createTempFile("transform_GetLegendGraphic_"+UUID.randomUUID().toString(),
					// getExtension(responseContentType));
					// FileImageOutputStream output = new
					// FileImageOutputStream(tempFile);
					// writer.setOutput(new
					// MemoryCacheImageOutputStream(tempOut));
					// writer.setOutput(output);
					// writer.write(imageSource);
					// output.flush();
					// output.close();
					// Fin de Debug
					// }
					dump("transform end GetLegendGraphic operation");
				}

				else if (currentOperation.equalsIgnoreCase("GetFeatureInfo")) {
					dump("transform begin GetFeatureInfo operation");
					// resp.setHeader("Content-Encoding", "gzip");

					tempOut = new ByteArrayOutputStream();
					DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
					XPathFactory xpathFactory = XPathFactory.newInstance();
					DocumentBuilder builder = factory.newDocumentBuilder();
					Document doc = builder.newDocument();
					transformer = tFactory.newTransformer();
					XPath xpath = xpathFactory.newXPath();
					XPathExpression expr = xpath.compile("/FeatureCollection");
					Element rootNode = null;

					for (String path : wmsFilePathList.values()) {
						Document resultDoc = builder.parse(new File(path));
						if (rootNode == null) {
							Node result = (Node) expr.evaluate(resultDoc, XPathConstants.NODE);
							if (result != null) {
								rootNode = (Element) doc.importNode(result, true);
								doc.appendChild(rootNode);
							}
						} else {
							NodeList result = resultDoc.getDocumentElement().getChildNodes();
							if (result != null && result.getLength() > 0) {
								for (int i = 0; i < result.getLength(); i++) {
									Node nnode = result.item(i);
									if (!"gml:boundedBy".equals(nnode.getNodeName())) {
										nnode = doc.importNode(nnode, true);
										rootNode.appendChild(nnode);
									}
								}
							}

						}
					}
					transformer.transform(new DOMSource(doc), new StreamResult(tempOut));
					dump("transform end GetFeatureInfo operation");
				}
			}

			// ********************************************************************************************************************
			// Traitement du résultat final avec le xslt utilisateur s'il exist
			// (voir début de transform())
			// if a xslt file exists then post-treat the response
			if (isPostTreat && isXML(responseContentType)) {
				dump("transform begin userTransform xslt");

				PrintWriter out = resp.getWriter();
				transformer = tFactory.newTransformer(new StreamSource(xsltFile));
				ByteArrayInputStream is = new ByteArrayInputStream(tempOut.toByteArray());
				tempOut = new ByteArrayOutputStream();
				transformer.transform(new StreamSource(is), new StreamResult(out));
				// transformer.transform(new StreamSource(tempFile), new
				// StreamResult(out));
				// delete the temporary file
				// tempFile.delete();
				out.close();

				dump("transform end userTransform xslt");
				// the job is done. we can go out
				return;
			}

			// Ou Ecriture du résultat final dans resp de
			// httpServletResponse*****************************************************
			// No post rule to apply. Copy the file result on the output stream
			BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());
			resp.setContentType(responseContentType);
			// BufferedInputStream is = new BufferedInputStream(new
			// FileInputStream(tempFile));

			// Debug tb 06.12.2009

			// Pour une bonne performances en écriture
			// byte[] data = new byte[131072];
			// int byteRead;
			try {
				dump("transform begin response writting");
				os.write(tempOut.toByteArray());
				// while((byteRead = is.read()) != -1)
				// while((byteRead = is.read(data)) != -1)
				// {
				// os.write(byteRead);
				// os.write(data);
				// os.flush();
				// }
				dump("transform end response writting");
			} finally {
				os.flush();
				os.close();
				// is.close();
				// Fin de Debug

				// Log le résultat et supprime les fichiers temporaires
				DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
				Date d = new Date();
				dump("SYSTEM", "ClientResponseDateTime", dateFormat.format(d));
				if (tempOut != null)
					dump("SYSTEM", "ClientResponseLength", tempOut.size());
				// if (tempFile !=null)
				// {
				// dump("SYSTEM","ClientResponseLength",tempFile.length());
				// tempFile.delete();
				// }
				// Fin de Debug
			}

			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			Date d = new Date();

			dump("SYSTEM", "ClientResponseDateTime", dateFormat.format(d));

		} catch (Exception e) {
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}
	}

	// ***************************************************************************************************************************************

	/**
	 * @return
	 */
	private BufferedImage filterImage(String filter, Collection<String> fileNames, boolean isTransparent) {
		BufferedImage imageSource = null;
		Graphics2D g = null;
		for (String fileName : fileNames) {
			BufferedImage image = filterImage(filter, fileName, isTransparent);
			if (g == null) {
				imageSource = image;
				g = imageSource.createGraphics();
			} else if (image != null)
				g.drawImage(image, null, 0, 0);
		}
		if (g != null)

			g.dispose();
		return imageSource;
	}

	private BufferedImage filterImage(String filter, String fileName, boolean isTransparent) {
		try {
			String[] s = bbox.split(",");

			if (filter != null) {
				InputStream bis = new ByteArrayInputStream(filter.getBytes());
				System.setProperty("org.geotools.referencing.forceXY", "true");

				Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);
				WKTReader wktReader = new WKTReader();

				Geometry polygon = wktReader.read(object.toString());

				filter.indexOf("srsName");
				String srs = filter.substring(filter.indexOf("srsName"));
				srs = srs.substring(srs.indexOf("\"") + 1);
				srs = srs.substring(0, srs.indexOf("\""));
				polygon.setSRID(Integer.parseInt(srs.substring(5)));

				CRSEnvelope bbox = new CRSEnvelope(srsName, Double.parseDouble(s[0]), Double.parseDouble(s[1]), Double.parseDouble(s[2]), Double
						.parseDouble(s[3]));

				// final WorldFileReader reader = new WorldFileReader(new
				// File(filePath));

				BufferedImage image = ImageIO.read(new File(fileName));
				int type = BufferedImage.TYPE_INT_BGR;
				if (image.getTransparency() == Transparency.BITMASK)
					type = BufferedImage.BITMASK;
				else if (image.getTransparency() == Transparency.TRANSLUCENT)
					type = BufferedImage.TRANSLUCENT;
				BufferedImage canvas = new BufferedImage(image.getWidth(), image.getHeight(), type);
				canvas.getGraphics().drawImage(image, 0, 0, null);
				BufferedImage imageOut = imageFiltering(canvas, bbox, polygon, isTransparent);

				return imageOut;
			} else {
				if (fileName != null) {
					BufferedImage image = ImageIO.read(new File(fileName));
					int type = BufferedImage.TYPE_INT_BGR;
					if (image.getTransparency() == Transparency.BITMASK)
						type = BufferedImage.BITMASK;
					else if (image.getTransparency() == Transparency.TRANSLUCENT)
						type = BufferedImage.TRANSLUCENT;
					BufferedImage canvas = new BufferedImage(image.getWidth(), image.getHeight(), type);
					canvas.getGraphics().drawImage(image, 0, 0, null);
					return canvas;
				}
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
		return null;
	}

	// ***************************************************************************************************************************************

	/**
	 * @param tempFileCapa
	 * @return
	 */
	private ByteArrayOutputStream mergeCapabilities(List<File> tempFileCapa)
	// private File mergeCapabilities(List<File> tempFileCapa)
	{

		if (tempFileCapa.size() == 0)
			return null;

		try {
			File fMaster = tempFileCapa.get(0);
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(false);
			Document documentMaster = db.newDocumentBuilder().parse(fMaster);
			DOMImplementationLS implLS = null;
			if (documentMaster.getImplementation().hasFeature("LS", "3.0")) {
				implLS = (DOMImplementationLS) documentMaster.getImplementation();
			} else {
				DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
				implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
			}
			if (implLS == null) {
				dump("Error", "DOM Load and Save not Supported. Multiple server is not allowed");
				ByteArrayOutputStream out = new ByteArrayOutputStream();
				FileInputStream reader = new FileInputStream(fMaster);
				byte[] data = new byte[reader.available()];
				reader.read(data, 0, reader.available());
				out.write(data);
				reader.close();
				return out;
				// return fMaster;
			}

			for (int i = 1; i < tempFileCapa.size(); i++) {
				Document documentChild = db.newDocumentBuilder().parse(tempFileCapa.get(i));
				NodeList nl = documentChild.getElementsByTagName("Layer");
				NodeList nlMaster = documentMaster.getElementsByTagName("Layer");
				Node ItemMaster = nlMaster.item(0);
				ItemMaster.insertBefore(documentMaster.importNode(nl.item(0).cloneNode(true), true), null);
			}

			ByteArrayOutputStream out = new ByteArrayOutputStream();
			// File f = createTempFile(UUID.randomUUID().toString(),".xml");
			// FileOutputStream fluxSortie = new FileOutputStream(f);
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			// sortie.setSystemId(f.toString());
			// sortie.setByteStream(fluxSortie);
			sortie.setByteStream(out);
			serialiseur.write(documentMaster, sortie);
			// fluxSortie.flush();
			// fluxSortie.close();

			return out;
		} catch (Exception e) {
			e.printStackTrace();
			dump("ERROR", e.getMessage());
			return null;
		}
	}

	// ***************************************************************************************************************************************

	protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp) {

	}

	// ***************************************************************************************************************************************

	protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp) {
		try {
			String operation = null;
			String version = "000";
			String service = "";
			String layer = ""; // Pour l'opération GetLegendGraphic seulement
			String queryLayers = ""; // Pour l'opération GetFeatureInfo
			// seulement
			String width = "1";
			String height = "1";
			String format = "";

			layers = null;
			boolean sendRequest = true;
			Enumeration<String> parameterNames = req.getParameterNames();
			String paramUrlBase = "";

			// *********************************************************************
			// To build the request to dispatch
			// Lecture des paramètres de la requête utlisateur en mode GET
			while (parameterNames.hasMoreElements()) {
				String key = (String) parameterNames.nextElement();
				String value = "";
				if (key.equalsIgnoreCase("LAYER") || key.equalsIgnoreCase("QUERY_LAYERS") || key.equalsIgnoreCase("LAYERS") || key.equalsIgnoreCase("STYLES")
						|| key.equalsIgnoreCase("BBOX") || key.equalsIgnoreCase("SRS") || key.equalsIgnoreCase("CRS")) {
					value = req.getParameter(key);
				} else {
					value = URLEncoder.encode(req.getParameter(key));
				}

				// String value = req.getParameter(key);
				if (!key.equalsIgnoreCase("QUERY_LAYERS") && !key.equalsIgnoreCase("LAYERS") && !key.equalsIgnoreCase("STYLES")) {
					paramUrlBase = paramUrlBase + key + "=" + value + "&";
				}

				if (key.equalsIgnoreCase("Request")) {
					// Gets the requested Operation
					if (value.equalsIgnoreCase("capabilities")) {
						operation = "GetCapabilities";
					} else {
						operation = value;
					}
				} else if (key.equalsIgnoreCase("version")) {
					// Gets the requested version
					version = value;
				} else if (key.equalsIgnoreCase("wmtver")) {
					// Gets the requested wmtver
					version = value;
					service = "WMS";
				} else if (key.equalsIgnoreCase("service")) {
					// Gets the requested service
					service = value;
				} else if (key.equalsIgnoreCase("BBOX")) {
					// Gets the requested bbox
					bbox = value;
				} else if (key.equalsIgnoreCase("SRS")) {
					// Gets the requested srs
					srsName = value;
				} else if (key.equalsIgnoreCase("CRS")) // Version 1.3.0
				{
					// Gets the requested srs
					srsName = value;
				} else if (key.equalsIgnoreCase("LAYER")) {
					// Gets the requested layer -> GetLegendGraphic only
					layer = value;
				}
				// Debug tb 18.01.2010
				else if (key.equalsIgnoreCase("QUERY_LAYERS")) {
					// Gets the requested querylayers -> GetFeatureInfo
					queryLayers = value;
				}
				// Fin de Debug
				else if (key.equalsIgnoreCase("LAYERS")) {
					// Gets the requested layers -> GetMap
					layers = value;
				} else if (key.equalsIgnoreCase("STYLES")) {
					styles = value;
				} else if (key.equalsIgnoreCase("WIDTH")) {
					width = value;
				} else if (key.equalsIgnoreCase("HEIGHT")) {
					height = value;
				} else if (key.equalsIgnoreCase("FORMAT")) {
					format = value;
				}
			}

			// Debug tb 18.01.2010
			// Pour éviter le cas où "layers" est absent de la requête
			// GetFeatureInfo
			if (!queryLayers.equalsIgnoreCase("")) {
				layers = queryLayers;
			}
			// Fin de Debug

			String user = "";
			if (req.getUserPrincipal() != null) {
				user = req.getUserPrincipal().getName();
			}

			// Debug tb 11.11.2009
			if (hasPolicy) {
				if (!isOperationAllowed(operation))
					throw new NoPermissionException("operation is not allowed");
			}
			// Fin de Debug

			// *********************************************************************

			// Debug tb 09.07.2009
			// Définition de la classe des threads d'interrogation sur les
			// serveurs
			// **************************************************************************************
			// **************************************************************************************
			class SendServerThread extends Thread {

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

				// **************************************************************************************
				public SendServerThread(String pOperation, String pParamUrl, List pLayerToKeepList, int pIServer, List pStylesToKeepList, String pParamUrlBase,
						int pJ, String pWidth, String pHeight, String pFormat) {
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
				}

				// **************************************************************************************
				public void run() {

					// Pour créer les threads d'interrogation des couches du
					// serveur j
					// -> nécessaire car filter peut être différent d'une couche
					// à l'autre du même serveur!!!
					// **************************************************************************************
					// **************************************************************************************
					class SendLayerThread extends Thread {

						List layerToKeepList;
						int iLayers;
						List stylesToKeepList;
						String paramUrlBase;
						String width;
						String height;
						String format;
						int j;

						// **************************************************************************************
						// public SendLayerThread(String pOperation,String
						// pParamUrl,List pLayerToKeepList,int pILayers,List
						// pStylesToKeepList,String pParamUrlBase,int pJ,String
						// pWidth,String pHeight,String pFormat)
						public SendLayerThread(int pILayers, List pLayerToKeepList, List pStylesToKeepList, String pParamUrlBase, int pJ, String pWidth,
								String pHeight, String pFormat) {
							layerToKeepList = pLayerToKeepList;
							iLayers = pILayers;
							stylesToKeepList = pStylesToKeepList;
							paramUrlBase = pParamUrlBase;
							j = pJ;
							width = pWidth;
							height = pHeight;
							format = pFormat;
						}

						// **************************************************************************************
						public void run() {
							try {
								dump("DEBUG", "Thread Layers group: " + layerToKeepList.get(0) + " work begin on server " + getRemoteServerUrl(j));

								String layersUrl = "LAYERS=" + layerToKeepList.get(0);
								String stylesUrl = "&STYLES=" + stylesToKeepList.get(0);
								for (int n = 1; n < layerToKeepList.size(); n++) {
									layersUrl = layersUrl + "," + layerToKeepList.get(n);
									stylesUrl = stylesUrl + "," + stylesToKeepList.get(n);
								}

								if (paramUrlBase.toUpperCase().indexOf("TRANSPARENT=") == -1)
									paramUrlBase += "TRANSPARENT=TRUE&";
								String filePath = sendData("GET", getRemoteServerUrl(j), paramUrlBase + layersUrl + stylesUrl);

								synchronized (serverFilePathList) {
									synchronized (serverLayerFilePathList) {
										// Ecriture de la réponse du thread,
										// dans le respect de l'ordre des layers
										// de la requête utilisateur
										if (iLayers >= serverFilePathList.size()) {
											serverFilePathList.add(filePath);
											serverLayerFilePathList.add((String) layerToKeepList.get(0));
										} else {
											serverFilePathList.add(iLayers, filePath);
											serverLayerFilePathList.add(iLayers, (String) layerToKeepList.get(0));
										}
									}
								}
								dump("DEBUG", "Thread Layers group: " + layerToKeepList.get(0) + " work finished on server " + getRemoteServerUrl(j));
							} catch (Exception e) {
								dump("ERROR", "Server " + getRemoteServerUrl(j) + " Layers group Thread " + layerToKeepList.get(0) + " :" + e.getMessage());
								e.printStackTrace();
							}
						}
					}
					// **************************************************************************************
					// **************************************************************************************

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
											paramUrlBase, j, width, height, format);
									th.start();
									layerThreadList.add(th);
								} else {
									dump("ERROR", "Thread Layers group: " + layerToKeepListPerThread.get(0) + " work finished on server "
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
								if (!((String) serverFilePathList.get(i)).equals("")) {
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
								paramUrlBase += "&SERVICE=WMS";
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
						dump("ERROR", "Server Thread " + getRemoteServerUrl(j) + " :" + e.getMessage());
						e.printStackTrace();
					}
				}
			}
			// **************************************************************************************
			// **************************************************************************************
			// Fin de Debug

			// Boucle sur les serveur définis dans config.xml
			// *************************
			List<RemoteServerInfo> grsiList = getRemoteServerInfoList();
			List<SendServerThread> serverThreadList = new Vector<SendServerThread>();

			List<String> layerArray = null;
			if (layer != null && layers == null)
				layers = layer;
			if (layers != null)
				layerArray = Collections.synchronizedList(new ArrayList<String>(Arrays.asList(layers.split(","))));
			int layerOrder = 0;
			String lastServerURL = null;
			String newServerURL = null;
			String cpOperation = new String(operation);
			String cpParamUrl = "";
			String cpParamUrlBase = new String(paramUrlBase);
			String cpWidth = new String(width);
			String cpHeight = new String(height);
			String cpFormat = new String(format);
			String filter = null;
			if (operation.equalsIgnoreCase("getcapabilities")) {
				for (int jj = 0; jj < grsiList.size(); jj++) {
					SendServerThread s = new SendServerThread(cpOperation, cpParamUrl, null, serverThreadList.size(), null, cpParamUrlBase, jj, cpWidth,
							cpHeight, cpFormat);

					s.layerOrder = jj;
					s.start();
					serverThreadList.add(s);
				}

			} else {
				while (layerArray.size() > 0) {
					List<String> layerToKeepList = new Vector<String>();
					List<String> stylesToKeepList = new Vector<String>();
					int j = 0;
					if (policy != null) {
						if (hasPolicy) {
							// Vérfication de la taille image req VS policy ->
							// si vrai:
							// la requête n'est pas envoyée
							sendRequest = true;
							if (("GetMap".equalsIgnoreCase(operation) || "map".equalsIgnoreCase(operation)) || "getfeatureinfo".equalsIgnoreCase(operation)) {
								if (!isSizeInTheRightRange(Integer.parseInt(width), Integer.parseInt(height))) {
									dump("requestPreTraitementGET says: request ImageSize out of bounds, see the policy definition.");
									sendRequest = false;
									layerArray.remove(0);
								}

								// Vérification de la présence du pramètre
								// "LAYERS"
								// dans la
								// requête -> si vrai: recherche des layers
								// autorisées et
								// styles correspondant
								// Permet la réécriture des paramètres "LAYERS"
								// et
								// "STYLES"
								// de la requête
								if (sendRequest && layers != null && layers.length() > 0) {

									// Debug tb 09.07.2009
									String[] layerStyleArray;
									if (styles != null) {
										layerStyleArray = styles.split(",");
									} else {
										styles = "";
										layerStyleArray = styles.split(",");
									}

									// Le paramètre style est obligatoire par
									// couche, mais
									// on l'émule s'il n'est pas présent
									if (layerStyleArray.length < layerArray.size()) {
										int diffSize = layerArray.size() - layerStyleArray.length;
										for (int i = 0; i < diffSize; i++) {
											styles = styles + ",";
										}
									}
									// Fin de Debug
									layerStyleArray = styles.split(",");

									// Vérification des couches autorisées
									// *********************************
									int li = 0;
									boolean serverOK = false;
									String tmpFT = null;
									boolean cnt = true;
									boolean found = false;
									for (int jj = 0; jj < grsiList.size(); jj++) {
										for (int i = 0; i <= layerArray.size(); i++) {
											if (layerArray.size() == 0) {
												cnt = false;
												break;
											}
											tmpFT = layerArray.get(li);
											int sindex = tmpFT.indexOf(":") + 1;
											tmpFT = tmpFT.substring(sindex);

											if (layerToKeepList.size() > 0) {
												if (filter != null) {
													filter = null;
													cnt = false;
													break;
												}
												filter = getLayerFilter(getRemoteServerUrl(j), tmpFT);
												if (filter != null) {
													cnt = false;
													break;
												}
											}

											// Debug tb 03.07.2009
											newServerURL = grsiList.get(jj).getUrl();
											serverOK = isLayerAllowed(tmpFT, newServerURL);
											if (serverOK && tmpFT != null) {

												// Fin de Debug
												// Vérification que la couche de
												// la
												// req
												// est
												// autorisée par Policy

												String[] c = bbox.split(",");

												ReferencedEnvelope re = new ReferencedEnvelope(Double.parseDouble(c[0]), Double.parseDouble(c[2]), Double
														.parseDouble(c[1]), Double.parseDouble(c[3]), CRS.decode(srsName));

												// Vérification que l'échelle de
												// la
												// requête est
												// autorisée
												if (isLayerInScale(tmpFT, newServerURL, RendererUtilities.calculateOGCScale(re, Integer.parseInt(width), null))) {
													serverOK = true;
												} else {
													dump("requestPreTraitementGET says: request Scale out of bounds, see the policy definition.");
													serverOK = false;
													layerArray.remove(li);

												}

												// Ajout de la couche et de son
												// sytle
												// associé,
												// si cette dernière est
												// autorisée
												// par
												// Policy
												if (serverOK) {
													if (layerStyleArray.length > i) {
														stylesToKeepList.add(layerStyleArray[i]);
													} else {
														stylesToKeepList.add("");
													}
													layerArray.remove(li);
													layerToKeepList.add(tmpFT);
													found = true;
													j = jj;
													lastServerURL = newServerURL;
													i--;
												}
											}

											if ((layerToKeepList.size() > 0 && !serverOK && (newServerURL.equals(lastServerURL)))) {
												cnt = false;
												break;
											}
										}
										if (!cnt || layerArray.size() == 0)
											break;
									}
									if (!found) {
										layerArray.remove(li);
									}
									// Vérfication de l'absence de "LAYER"
									// autorisées
									// restantes -> si vrai: la requête n'est
									// pas
									// envoyée
									if (layerToKeepList.size() <= 0) {
										sendRequest = false;
									}
									// Fin de Debug
								}
								// Debug tb 04.09.2009
								// Vérification de l'authorisation policy pour
								// la
								// couche
								// "LAYER" de l'opération GetLegendGraphic
							} else if ("GetLegendGraphic".equalsIgnoreCase(operation)) {
								// Vérification que la couche de la req est
								// autorisée
								// par Policy
								String tmpFT = "";
								String[] s = layerArray.get(0).split(":");
								tmpFT = s[s.length - 1];
								layerArray.remove(0);
								for (int jj = 0; jj < grsiList.size(); jj++) {
									j = jj;
									boolean isLayerTypePermited = isLayerAllowed(tmpFT, getRemoteServerUrl(j));
									if (!isLayerTypePermited) {
										dump("requestPreTraitementGET says: GetLegendGraphic request Layer is not allowed by policy");
										sendRequest = isLayerTypePermited;
									} else {
										SendServerThread glgs = new SendServerThread(cpOperation, cpParamUrl, layerToKeepList, serverThreadList.size(),
												stylesToKeepList, cpParamUrlBase, j, null, null, cpFormat);

										glgs.layerOrder = jj;
										glgs.start();
										serverThreadList.add(glgs);

									}
								}

								// Fin de Debug
							}

							// Si pas de fichier Policy défini, envoi direct de
							// la
							// requête
							// sur le serveur j
							// else
							// {
							// //@TODO:Manage multiple servers when no policy is
							// existing.
							// if (layers!=null && layers.length()>0)
							// {
							// paramUrl="LAYERS="+layers+"&STYLES="+styles;
							// }
							// }

							// Si requête à envoyer sur serveur j
							if (sendRequest) {
								// Debug tb 08.07.2009
								// Nouvelle version des threads -> par serveur
								// ->
								// par couche
								// Création et lancement du thread sur serveur j
								// Copie des strings pour utlisation dans
								// threads.
								// Originales rééctrites dans boucle serveur
								// courante!
								if (layers != null && operation.equalsIgnoreCase("getmap") || operation.equalsIgnoreCase("getfeatureinfo")) {
									List<String> layersTabWithNS = Arrays.asList(layers.split(","));
									List<String> layersTab = new ArrayList<String>();
									for (String layerName : layersTabWithNS) {
										String[] layerNameFinal = layerName.split(":", 2);
										layersTab.add((layerNameFinal.length > 1) ? layerNameFinal[1] : layerNameFinal[0]);
									}

									if (!configuration.isGrouping() && layerToKeepList.size() > 0) {
										for (String layerToKeepElement : layerToKeepList) {
											List<String> singleLayerList = new ArrayList<String>();
											singleLayerList.add(layerToKeepElement);
											SendServerThread s = new SendServerThread(cpOperation, cpParamUrl, singleLayerList, serverThreadList.size(),
													stylesToKeepList, cpParamUrlBase, j, cpWidth, cpHeight, cpFormat);
											s.layerOrder = layersTab.indexOf(layerToKeepElement);

											s.start();
											serverThreadList.add(s);

										}
									} else {
										SendServerThread s = new SendServerThread(cpOperation, cpParamUrl, layerToKeepList, serverThreadList.size(),
												stylesToKeepList, cpParamUrlBase, j, cpWidth, cpHeight, cpFormat);

										s.layerOrder = layerOrder;
										layerOrder++;
										s.start();
										serverThreadList.add(s);

									}
								}
							}
							// Si pas de requête à envoyer sur serveur j:
							// sendRequest=false
							else {
								sendRequest = true;
								if (("GetMap".equalsIgnoreCase(operation) || "map".equalsIgnoreCase(operation) || "GetLegendGraphic"
										.equalsIgnoreCase(operation))) {
									dump("requestPreTraitementGET save response server " + getRemoteServerUrl(j)
											+ ": emptyImage. Proxy bloc the request to this server due to policy config.");
									generateEmptyImage(width, height, format, true, j);
								}
							}
						}
					}
				}
			}
			// Sortie de la boucle des
			// serveurs*************************************************

			// Attente de l'arrivée des résultats des threads sur chaque serveur
			// avant de passer à la suite du traitement
			for (int i = 0; i < serverThreadList.size(); i++) {
				serverThreadList.get(i).join();

				// les réponses ont été insérées, par les threads servers, dans
				// wmsFilePathList;
				// layerFilePathList-> layer names et serverUrlPerFilePathList->
				// url server, ont aussi été mis à jour en conséquence
			}

			// Debug tb 15.01.2010
			// Si aucun des layerThread n'a passé de requête, car policy filter
			// et req bbox incompatibles
			// if(wmsFilePathList.size()<=0 &&
			// ("GetMap".equalsIgnoreCase(operation) ||
			// "map".equalsIgnoreCase(operation)))
			// {
			// sendRequest=true;
			// dump("requestPreTraitementGET save response servers: emptyImage");
			// generateEmptyImage(width,height,format,true);
			// }
			// Fin de la phase de reconstruction de la requête: wmsFilePathList
			// contient les réponses de chaque serveur (une par serveur)
			// *****************************************************************************************************************************

			// *****************************************************************************************************************************
			if (wmsFilePathList.size() > 0) {
				// Lancement du post traitement
				version = version.replaceAll("\\.", "");

				dump("requestPreTraitementGET begin transform");
				transform(version, operation, req, resp);
				dump("requestPreTraitementGET end transform");
				// Fin du post traitement
				// *****************************************************************************************************************************
			} else {
				dump("ERROR", "This request has no authorized results!");
			}
			// Fin de Debug
		} catch (AvailabilityPeriodException e) {
			dump("ERROR", e.getMessage());
			resp.setStatus(401);
			try {
				resp.getWriter().println(e.getMessage());
			} catch (IOException e1) {
				e1.printStackTrace();
			}
		} catch (Exception e) {
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}
	}

	// ***************************************************************************************************************************************

	private void generateEmptyImage(String width, String height, String format, boolean isTransparent, int j) {
		// In the case of a GetMap, it should returns an empty image
		try {
			BufferedImage imgOut = null;
			if (isTransparent) {
				imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.BITMASK);
			} else {
				imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.TYPE_INT_ARGB);
			}
			responseContentType = URLDecoder.decode(format);
			Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(responseContentType);

			if (iter.hasNext()) {
				ImageWriter writer = (ImageWriter) iter.next();
				File tempFile = createTempFile(UUID.randomUUID().toString(), getExtension(responseContentType));
				FileImageOutputStream output = new FileImageOutputStream(tempFile);
				writer.setOutput(output);
				writer.write(imgOut);
				String filePath = tempFile.getPath();
				wmsFilePathList.put(j, filePath);
				layerFilePathList.put(j, "");
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	// ***************************************************************************************************************************************

	/*
	 * envelope contains the envelope of the whole image
	 */
	private BufferedImage imageFiltering(BufferedImage imageSource, CRSEnvelope envelope, Geometry polygonFilter, boolean isTransparent) {
		try {
			System.setProperty("org.geotools.referencing.forceXY", "true");
			// System.setProperty(
			// (Hints.FORCE_STANDARD_AXIS_DIRECTIONS.toString()), "true" );

			// Transform the srs of the filter if needed.
			String srsName = envelope.getEPSGCode();
			CoordinateReferenceSystem crs = CRS.decode(srsName);

			CoordinateReferenceSystem sourceCRS = CRS.decode("EPSG:" + (new Integer(polygonFilter.getSRID())).toString());
			CoordinateReferenceSystem targetCRS = CRS.decode(envelope.getEPSGCode());

			MathTransform a = CRS.findMathTransform(sourceCRS, targetCRS, false);

			polygonFilter = JTS.transform(polygonFilter, a);

			try {
				for (int i = 0; i < crs.getIdentifiers().size(); i++) {
					if (((NamedIdentifier) crs.getIdentifiers().toArray()[i]).getCodeSpace().equals("EPSG")) {
						polygonFilter.setSRID(Integer.parseInt(((NamedIdentifier) crs.getIdentifiers().toArray()[i]).getCode()));
						break;
					}
				}
			} catch (Exception e) {
				e.printStackTrace();
			}

			final GeometryAttributeType geom = new GeometricAttributeType("Geom", Geometry.class, false, null, crs, null);
			final AttributeType attr1 = AttributeTypeFactory.newAttributeType("COLOR", String.class);
			final AttributeType[] attributes = new AttributeType[] { attr1, geom };

			final FeatureType schema = FeatureTypes.newFeatureType(attributes, "TEMPORARYFEATURE", new URI("depth.ch"), false, null, geom);

			// Construction du masque sur la base de l'enveloppe de la bbox et
			// du polygon de filtre
			FeatureRasterizer fr = new FeatureRasterizer(imageSource.getHeight(), imageSource.getWidth());
			double width = envelope.getMaxX() - envelope.getMinX();
			double height = envelope.getMaxY() - envelope.getMinY();
			;
			Rectangle2D.Double bounds = new Rectangle2D.Double(envelope.getMinX(), envelope.getMinY(), width, height);

			fr.setBounds(bounds);
			fr.setAttName("COLOR");

			fr.addFeature(schema.create(new Object[] { Integer.toString(Color.WHITE.getRGB()), polygonFilter }));

			// Construction de l'image de masquage
			BufferedImage bimage2 = fr.getBimage();
			int imageType = BufferedImage.TYPE_INT_RGB;
			if (isTransparent) {
				imageType = BufferedImage.TYPE_INT_ARGB;
			}

			// "dimg" contient l'image source et "bimage2" est utilisé comme
			// masque.
			BufferedImage dimg = new BufferedImage(imageSource.getWidth(), imageSource.getHeight(), imageType);
			Graphics2D g = dimg.createGraphics();
			g.setComposite(AlphaComposite.Src);
			g.drawImage(imageSource, null, 0, 0);
			g.dispose();
			for (int i = 0; i < bimage2.getHeight(); i++) {
				for (int j = 0; j < bimage2.getWidth(); j++) {
					if (bimage2.getRGB(j, i) == 0) {
						// dimg.setRGB(j, i, 0x8F1C1C);
						dimg.setRGB(j, i, 0xFFFFFF);
					}
				}
			}

			// Une fois le masque appliqué sur l'image source, renvoyé l'image
			// filtrée
			return dimg;
		} catch (Exception e) {
			e.printStackTrace();
			dump("ERROR", e.getMessage());
		}

		return imageSource;
	}

	// ***************************************************************************************************************************************

}
