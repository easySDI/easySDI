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
package org.easysdi.proxy.wfs;

import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.io.StringReader;
import java.io.StringWriter;
import java.security.Principal;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.UUID;
import java.util.Vector;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.LinkedBlockingQueue;
import java.util.concurrent.ThreadPoolExecutor;
import java.util.concurrent.TimeUnit;
import java.util.logging.Level;

import javax.naming.NoPermissionException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;
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
import org.easysdi.xml.handler.RequestHandler;
import org.easysdi.xml.resolver.ResourceResolver;
import org.geotools.data.ows.FeatureSetDescription;
import org.geotools.feature.Feature;
import org.geotools.feature.FeatureCollection;
import org.geotools.feature.FeatureIterator;
import org.geotools.filter.Filter;
import org.geotools.filter.FilterDOMParser;
import org.geotools.gml.producer.FeatureTransformer;
import org.geotools.referencing.CRS;
import org.geotools.renderer.shape.FilterTransformer;
import org.geotools.xml.DocumentFactory;
import org.geotools.xml.XSISAXHandler;
import org.geotools.xml.gml.GMLFeatureCollection;
import org.geotools.xml.schema.ComplexType;
import org.geotools.xml.schema.Schema;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.ls.LSSerializer;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

/**
 * @author rmi
 * 
 */
public class WFSProxyServlet extends ProxyServlet {
	// Debug tb 02.10.2009
	private SAXParser parser;
	private List<Integer> serversIndex = new Vector<Integer>(); // list of
	// server index
	// corresponding
	// to the
	// response file
	// index
	private List<String> policyServersPrefix = new Vector<String>(); // list of
	// prefix
	// per
	// server
	private List<String> policyServersNamespace = new Vector<String>(); // list
	// of
	// namespace
	// per
	// server
	private List<WFSProxyGeomAttributes> WFSProxyGeomAttributesList = new Vector<WFSProxyGeomAttributes>(); // list

	// of
	// WFSProxyGeomAttributes
	// objects,
	// un
	// objet
	// contient
	// tout
	// les
	// liens
	// Server-Prefix-Namespace-AuthorizedFeature-ifExist(GeomAttribute)
	// Fin de debug

	// ***************************************************************************************************************************************
	protected StringBuffer buildCapabilitiesXSLT(HttpServletRequest req, int remoteServerIndex) {

		try {

			String url = getServletUrl(req);

			StringBuffer WFSCapabilities100 = new StringBuffer();

			WFSCapabilities100
					.append("<xsl:stylesheet version=\"1.00\" xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");

			WFSCapabilities100.append("<xsl:template match=\"wfs:OnlineResource\">");
			// WFSCapabilities100.append("<OnlineResource>");
			WFSCapabilities100.append("<xsl:element name=\"OnlineResource\" namespace=\"http://www.opengis.net/wfs\">" + url + "</xsl:element>");
			// Changer seulement la partie racine de l'URL, pas les
			// param après '?'
			WFSCapabilities100.append("</xsl:template>");
			WFSCapabilities100.append("<xsl:template match=\"@onlineResource\">");
			WFSCapabilities100.append("<xsl:param name=\"thisValue\">");
			WFSCapabilities100.append("<xsl:value-of select=\".\"/>");
			WFSCapabilities100.append("</xsl:param>");
			WFSCapabilities100.append("<xsl:attribute name=\"onlineResource\">");
			WFSCapabilities100.append(url);
			// Changer seulement la partie racine de l'URL, pas les
			// param après '?'
			WFSCapabilities100.append("<xsl:value-of select=\"substring-after($thisValue,'" + getRemoteServerUrl(remoteServerIndex) + "')\"/>");
			WFSCapabilities100.append("</xsl:attribute>");
			WFSCapabilities100.append("</xsl:template>");

			if (hasPolicy) {
				if (!policy.getOperations().isAll()) {

					Iterator<Operation> it = policy.getOperations().getOperation().iterator();

					while (it.hasNext()) {

						String text = it.next().getName();
						if (text != null) {
							WFSCapabilities100.append("<xsl:template match=\"wfs:Capability/wfs:Request/wfs:");

							WFSCapabilities100.append(text);

							WFSCapabilities100.append("\">");

							WFSCapabilities100.append("<!-- Copy the current node -->");
							WFSCapabilities100.append("<xsl:copy>");
							WFSCapabilities100.append("<!-- Including any attributes it has and any child nodes -->");
							WFSCapabilities100.append("<xsl:apply-templates select=\"@*|node()\"/>");
							WFSCapabilities100.append("</xsl:copy>");

							WFSCapabilities100.append("</xsl:template>");
						}
					}

					if (policy.getOperations().getOperation().size() == 0) {
						WFSCapabilities100.append("<xsl:template match=\"wfs:Capability/wfs:Request/\"></xsl:template>");
					}
				}
			}
			WFSCapabilities100.append("  <!-- Whenever you match any node or any attribute -->");
			WFSCapabilities100.append("<xsl:template match=\"node()|@*\">");
			WFSCapabilities100.append("<!-- Copy the current node -->");
			WFSCapabilities100.append("<xsl:copy>");
			WFSCapabilities100.append("<!-- Including any attributes it has and any child nodes -->");
			WFSCapabilities100.append("<xsl:apply-templates select=\"@*|node()\"/>");
			WFSCapabilities100.append("</xsl:copy>");
			WFSCapabilities100.append("</xsl:template>");

			if (hasPolicy) {

				Map hints = new HashMap();
				hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);

				org.geotools.data.ows.WFSCapabilities doc = (org.geotools.data.ows.WFSCapabilities) DocumentFactory.getInstance(new File(wfsFilePathList
						.get(remoteServerIndex)).toURI(), hints, Level.WARNING);
				if (doc != null) {
					List<FeatureSetDescription> l = doc.getFeatureTypes();

					for (int i = 0; i < l.size(); i++) {
						String ftName = l.get(i).getName();
						if (hasPolicy) {
							String tmpFT = ftName;
							if (tmpFT != null) {
								String[] s = tmpFT.split(":");
								tmpFT = s[s.length - 1];
							}
							if (!isFeatureTypeAllowed(tmpFT, getRemoteServerUrl(remoteServerIndex))) {

								WFSCapabilities100.append("<xsl:template match=\"//wfs:FeatureType[starts-with(wfs:Name,'" + ftName + "')]\">");
								WFSCapabilities100.append("</xsl:template>");
							}
						}
					}
				}
				// Debug tb 03.07.2009
				// Add the prefix before the feature type: devenu inutile,
				// FeatureType>Name contient déjà le préfix du serveur concerné
				// if(getRemoteServerInfo(remoteServerIndex).getPrefix().length()>0){
				// WFSCapabilities100.append("<xsl:template match=\"//wfs:FeatureType/wfs:Name\">");
				// WFSCapabilities100.append("<Name>");
				// WFSCapabilities100.append("<xsl:if test=\"contains(.,':')\">");
				// WFSCapabilities100.append("<xsl:value-of select=\"substring-before(.,':')\"/>"+":"+getRemoteServerInfo(remoteServerIndex).getPrefix()+"<xsl:value-of select=\"substring-after(., ':')\"/>");
				// WFSCapabilities100.append("</xsl:if>");
				// WFSCapabilities100.append("<xsl:if test=\"not(contains(.,':'))\">");
				// WFSCapabilities100.append(getRemoteServerInfo(remoteServerIndex).getPrefix()+"<xsl:value-of select=\".\"/>");
				// WFSCapabilities100.append("</xsl:if>");
				//
				// WFSCapabilities100.append("</Name>");
				// WFSCapabilities100.append("</xsl:template>");
				// }
				// Fin de Debug
			}
			WFSCapabilities100.append("</xsl:stylesheet>");

			return WFSCapabilities100;
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
	protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp) {
		try {

			XMLReader xr = XMLReaderFactory.createXMLReader(); // Contient la
			// requête
			// utilistateur
			// sous forme
			// d'un xml
			HashMap<String, String> env = new HashMap<String, String>();
			RequestHandler rh = new RequestHandler();
			xr.setContentHandler(rh);

			StringBuffer paramSB = new StringBuffer();

			String param = ""; // Contient la requête utilistateur sous forme
			// d'une string
			String input;
			BufferedReader in = new BufferedReader(new InputStreamReader(req.getInputStream()));
			while ((input = in.readLine()) != null) {
				paramSB.append(input);
			}
			param = paramSB.toString();
			dump("SYSTEM", "Request", param);

			xr.parse(new InputSource(new InputStreamReader(new ByteArrayInputStream(param.toString().getBytes()))));

			String version = rh.getVersion();
			if (version != null)
				version = version.replaceAll("\\.", "");

			String currentOperation = rh.getOperation();

			String user = "";
			Principal principal = req.getUserPrincipal();
			if (principal != null) {
				user = principal.getName();
			}
			if (hasPolicy) {
				if (!isOperationAllowed(currentOperation))
					throw new NoPermissionException("operation is not allowed");
			}

			String paramOrig = param;

			// *****************************************************************************************************************************
			// Construction des FeaturesTypes et Attributs associés authorisés
			// -> modification de la requête utilisateur en conséquence
			// Passage de la requête utilisateur à plusieurs serveurs si défini
			// dans Policy <server>!!!
			// ATTENTION: Dans cette fonction iServer == à l'index du serveur
			// défini dans CONFIG
			// L'ordre des balises <server> dans POLICY doit être le même!!!
			int queryCount = 0;
			for (int iServer = 0; iServer < getRemoteServerInfoList().size(); iServer++) {

				param = paramOrig;

				Vector<String> featureTypeListToKeep = new Vector<String>();
				// Debug tb 12.05.2009
				List<String> attributeListToKeepPerFT = new Vector<String>(); // see
				// the
				// parent
				// ProxyServlet
				// class
				// var
				List<Integer> attributeListToKeepNbPerFT = new Vector<Integer>(); // see
				// the
				// parent
				// ProxyServlet
				// class
				// var
				List<String> attributeListToRemove = new Vector<String>();
				// Fin de debug
				List<String> featureTypeListToRemove = new Vector<String>();

				// Construcation de la liste des FeatureType autorisé ou non à
				// partir de celles contenues dans la requête utilisateur
				// Si le featureType est autorisé faire de même avec les
				// attributs
				// Attention: dans le cas où la requête utiliseur GetFeature
				// comporte plusieurs Query (typeName), ->
				// featureTypeListToKeep.length()>1
				String filePath = "";
				if (currentOperation.equalsIgnoreCase("GetFeature") || currentOperation.equalsIgnoreCase("DescribeFeatureType")) {
					ExecutorService pool = Executors.newFixedThreadPool(5);
					DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
					DocumentBuilder builder = factory.newDocumentBuilder();
					TransformerFactory tFactory = TransformerFactory.newInstance();
					Transformer transformer = tFactory.newTransformer();
					XPathFactory xpathFactory = XPathFactory.newInstance();
					XPath xpath = xpathFactory.newXPath();

					Object[] fields = (Object[]) rh.getTypeName().toArray();
					int iii = 0;
					for (Object o : fields) {
						/*
						 * featureTypeListToKeep = new Vector<String>();
						 * attributeListToKeepPerFT = new Vector<String>();
						 * attributeListToKeepNbPerFT = new Vector<Integer>();
						 * attributeListToRemove = new Vector<String>();
						 * featureTypeListToRemove = new Vector<String>();
						 */Object[] hFields = new Object[] { o };
						Document dQuery = builder.parse(new InputSource(new StringReader(paramOrig)));
						String xpathStr = "//*[local-name()='Query'][" + (iii + 1) + "]/@typeName";
						XPathExpression typeNameExp = xpath.compile(xpathStr);
						String typeName = (String) typeNameExp.evaluate(dQuery, XPathConstants.STRING);
						XPathExpression expr = xpath.compile("//*[local-name()='Query'][@typeName!='" + typeName + "']");
						NodeList nodes = (NodeList) expr.evaluate(dQuery, XPathConstants.NODESET);
						for (int ni = 0; ni < nodes.getLength(); ni++) {
							Node n = nodes.item(ni);
							n.getParentNode().removeChild(n);
						}
						StringWriter sw = new StringWriter();
						transformer.transform(new DOMSource(dQuery), new StreamResult(sw));
						param = sw.toString();
						if (hasPolicy) {
							int ii = 0; // Authorized FeatureType counter
							attributeListToKeepNbPerFT.add(0);

							for (int i = 0; i < hFields.length; i++) {
								// Proxy est non compatible avec les multi Query
								// dans les requêtes GetFeature!
								if (currentOperation.equalsIgnoreCase("GetFeature") && i > 0) {
									break;
								}

								String tmpFT = hFields[i].toString();
								if (tmpFT != null) {
									String[] s = tmpFT.split(":");
									tmpFT = s[s.length - 1];
									// Debug tb 03.07.2009
									// Fait doublon avec split":"
									// }
									// if
									// (tmpFT.startsWith(getRemoteServerInfo(iServer).getPrefix())||getRemoteServerInfo(iServer).getPrefix().length()==0)
									// {
									if (!tmpFT.equals("")) {
										// tmpFT =
										// tmpFT.substring((getRemoteServerInfo(iServer).getPrefix()).length());
										// Fin de Debug
										if (isFeatureTypeAllowed(tmpFT, getRemoteServerUrl(iServer))) {
											featureTypeListToKeep.add(tmpFT);
											// Debug tb 04.06.2009
											// Filtrage des attributs autorisés
											if (currentOperation.equalsIgnoreCase("GetFeature")) {
												Object[] fieldsAttribute = (Object[]) rh.getPropertyName().toArray(); // Si
												// req
												// PropertyNames
												// non
												// défini
												// ->
												// RequestHandler
												// retourne
												// un
												// élément
												// avec
												// ""

												// Au cas: req PropertyNames
												// défini
												// et [Policy Attributs sur ALL
												// ou
												// sur sous ensemble limité]
												// ou
												// req PropertyNames non défini
												// et
												// Policy Attributs sur sous
												// ensemble limité
												// ou
												// req PropertyNames non défini
												// et
												// Policy Attributs sur All
												for (int k = 0; k < fieldsAttribute.length; k++) //
												{
													String tmpFA = fieldsAttribute[k].toString();
													if (!"".equals(tmpFA) && tmpFA != null) {
														String[] ss = tmpFA.split(":");
														tmpFA = ss[ss.length - 1];
													}
													// Comparaison avec le
													// contenu
													// de la Policy
													// Si attributs dans req et
													// aussi dans Policy->
													// isAttributeAllowed
													// retourne
													// vrai pour l'attribut en
													// cours: tmpFA =
													// AttributFromReq
													// Si pas d'attributs dans
													// req
													// et Policy sur All ->
													// isAttributeAllowed edit
													// attributeListToKeepNbPerFT=0
													// et retourne vrai: pour
													// l'attribut en cours:
													// tmpFA =
													// ""
													// Si attributs dans req
													// mais
													// pas dans Policy->
													// isAttributeAllowed
													// retourne
													// faux pour l'attribut en
													// cours: tmpFA =
													// AttributFromReq
													// Si pas d'attributs dans
													// req
													// -> isAttributeAllowed
													// ajoute
													// ceux de la policy dans la
													// globale var
													// policyAttributeListToKeepPerFT
													// et retourne faux pour
													// l'attribut en cours:
													// tmpFA =
													// ""
													if (isAttributeAllowed(getRemoteServerUrl(iServer), tmpFT, tmpFA)) {
														if (!fieldsAttribute[k].toString().equals("")) // au
														// cas:
														// req
														// PropertyNames
														// défini
														// et
														// aussi
														// dans
														// Policy
														{
															attributeListToKeepPerFT.add(tmpFA); // Without
															// namespace
															attributeListToKeepNbPerFT.set(ii, attributeListToKeepNbPerFT.get(ii) + 1);
														} else // au cas: req
														// PropertyNames non
														// défini et Policy
														// sur All
														{
															attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
														}
													} else {
														if (!fieldsAttribute[k].toString().equals("")) // au
														// cas:
														// req
														// PropertyNames
														// défini
														// mais
														// pas
														// dans
														// Policy
														{
															attributeListToRemove.add(tmpFA); // Without
															// namespace
														} else if (fieldsAttribute[k].toString().equals("")) // au
														// cas:
														// req
														// PropertyNames
														// non
														// défini,
														// ajout
														// de
														// ceux
														// de
														// la
														// policy
														{
															attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
															attributeListToKeepPerFT.addAll(policyAttributeListToKeepPerFT);
														}
													}
												}
												// Au cas: Aucun attribut
												// demandé
												// dans req n'est autorisé dans
												// Policy
												// -> isAttributeAllowed à
												// ajouté
												// ceux de la policy à la
												// globale
												// var
												// policyAttributeListToKeepPerFT
												if (attributeListToKeepNbPerFT.get(ii) == 0 && !fieldsAttribute[0].toString().equals("")) // ajout
												// de
												// ceux
												// de
												// la
												// policy
												{
													isAttributeAllowed(getRemoteServerUrl(iServer), tmpFT, "");
													attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
													attributeListToKeepPerFT.addAll(policyAttributeListToKeepPerFT);
												}
											}
											// Fin de Debug
											// Debug tb 08.06.2009
											ii += 1;
											attributeListToKeepNbPerFT.add(0);
											// Fin de Debug
											// Suppression des features type non
											// autorisés
											// et
											// Suppression des Attributs
											// respectifs
											param = removeTypesFromPOSTUrl(featureTypeListToKeep, attributeListToKeepPerFT, attributeListToKeepNbPerFT, param,
													iServer, currentOperation);

											if (param.indexOf("Query") < 0)
												param = null;
											if (param != null) {
												WFSGetFeatureRunnable r = new WFSGetFeatureRunnable("POST", getRemoteServerUrl(iServer), param, this,
														serversIndex, wfsFilePathList, iServer, queryCount);
												pool.execute(r);
												iii++;
												queryCount++;
											}
										} else {
											featureTypeListToRemove.add(tmpFT);
										}
									}
								} else {
									if (tmpFT.equals("")) {
										featureTypeListToKeep.add("");
									}
								}
							}
						}
					}
					pool.shutdown();
					pool.awaitTermination(120L, TimeUnit.SECONDS);
				}
				boolean send = false;

				// Debug tb 23.06.2009
				// Vérifier que la requête avec opération DescribeFeatureType
				// comporte encore au moins 1 TypeName sinon voici la réponse à
				// retourner
				if ("DescribeFeatureType".equalsIgnoreCase(currentOperation)) {
					if (featureTypeListToKeep.size() == 0) {
						String s = "<?xml version='1.0' encoding='utf-8' ?>"
								+ "<ogcwfs:FeatureCollection xmlns:ogcwfs=\"http://www.opengis.net/wfs\"   xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" >"
								+ "<gml:boundedBy>" + "<gml:null>unavailable</gml:null>" + "</gml:boundedBy>" + "</ogcwfs:FeatureCollection>";
						File tempFile = createTempFile("requestPreTreatmentPOST" + UUID.randomUUID().toString(), ".xml");

						FileOutputStream tempFos = new FileOutputStream(tempFile);
						tempFos.write(s.getBytes());
						tempFos.flush();
						tempFos.close();
						in.close();
						filePath = tempFile.toString();
						send = false;
					}
				}
				// Fin de Debug

				// Ajout du remote filter (BBOX) à la requête utilisateur si
				// opération GetFeature
				if ("GetFeature".equalsIgnoreCase(currentOperation)) {
					// Si la requête modifiée ne comporte plus de TypeName voici
					// la réponse à retourner
					if (featureTypeListToKeep.size() == 0) {
						String s = "<?xml version='1.0' encoding='utf-8' ?>"
								+ "<ogcwfs:FeatureCollection xmlns:ogcwfs=\"http://www.opengis.net/wfs\"   xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" >"
								+ "<gml:boundedBy>" + "<gml:null>unavailable</gml:null>" + "</gml:boundedBy>" + "</ogcwfs:FeatureCollection>";
						File tempFile = createTempFile("requestPreTreatmentPOST" + UUID.randomUUID().toString(), ".xml");

						FileOutputStream tempFos = new FileOutputStream(tempFile);
						tempFos.write(s.getBytes());
						tempFos.flush();
						tempFos.close();
						in.close();
						filePath = tempFile.toString();
						send = false;
					}

					// Ajouter le "remoteFilter" à la requête posée
					// ********************************************
					// Attention corr: le namespace est ajouté explicitement
					// Concernant les filtres spatiaux de la policy:
					// RemoteFilter s'applique à la requête, LocalFilter
					// s'applique à la réponse
					// Il existe les cas possibles suivants:
					// Policy RemoteFilter is Not Set -> Policy Local Filter is
					// Not Set -> L'attribut géom n'est pas obligatoire
					// Policy RemoteFilter is Set -> Policy Local Filter is Not
					// Set -> L'attribut géom n'est pas obligatoire
					// Policy RemoteFilter is Set -> Policy Local Filter is Set
					// -> L'attribut géom est OBLIGATOIRE
					// ->Lecture de son nom dans RemoteFilter, vérification de
					// sa présence dans attributeListToKeepPerFT
					// -> Si présent: pas de modification de la requête
					// -> Si absent: modification de la requête par ajout de
					// <PropertyName>, puis suppression par filtrage du
					// résultat!
					// 

					// Recherche du remoteFilter dans la Policy et Ajout du
					// FeatureType autorisé
					String userFilter = null;

					if (featureTypeListToKeep.size() > 0) {
						userFilter = getFeatureTypeRemoteFilter(getRemoteServerUrl(iServer), featureTypeListToKeep.get(0));
						featureTypePathList = featureTypeListToKeep;
					}

					// Modification de la requête user avec le remoteFilter
					// Debug tb 21.05.2009
					// In case remoteFilter is activ but not Set
					if (send && userFilter != null && !userFilter.equals(""))
					// Fin de Debug
					{
						InputStream isRequestFilter = new ByteArrayInputStream(param.getBytes());
						InputStream isUserFilter = new ByteArrayInputStream(userFilter.getBytes());
						DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
						db.setNamespaceAware(true);
						Document documentRequestFilter = db.newDocumentBuilder().parse(isRequestFilter);
						Document documentUserFilter = db.newDocumentBuilder().parse(isUserFilter);

						DOMImplementationLS implLS = null;
						if (documentRequestFilter.getImplementation().hasFeature("LS", "3.0")) {
							implLS = (DOMImplementationLS) documentRequestFilter.getImplementation();
						} else {
							DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
							implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
						}
						NodeList nlRequestFilter = documentRequestFilter.getElementsByTagName("Filter");
						if (nlRequestFilter.getLength() == 0)
							nlRequestFilter = documentRequestFilter.getElementsByTagNameNS("http://www.opengis.net/ogc", "Filter");
						NodeList nlUserFilter = documentUserFilter.getElementsByTagName("Filter");
						if (nlUserFilter.getLength() == 0)
							nlUserFilter = documentUserFilter.getElementsByTagNameNS("http://www.opengis.net/ogc", "Filter");

						// A filter is existing
						// Could cause some performance issue, when query some
						// WFs based onOracle.
						if (nlRequestFilter.getLength() > 0) {
							Node nodeRequestFilter = nlRequestFilter.item(0);
							Node nodeUserFilter = nlUserFilter.item(0);
							Node andNode = documentRequestFilter.createElement("And");

							for (int i = 0; i < nodeRequestFilter.getChildNodes().getLength(); i++) {
								andNode.appendChild(nodeRequestFilter.getChildNodes().item(i).cloneNode(true));
							}

							for (int i = 0; i < nodeUserFilter.getChildNodes().getLength(); i++) {
								andNode.appendChild(documentRequestFilter.adoptNode(nodeUserFilter.getChildNodes().item(i).cloneNode(true)));
							}

							while (nodeRequestFilter.hasChildNodes()) {
								nodeRequestFilter.removeChild(nodeRequestFilter.getChildNodes().item(0));
							}

							nodeRequestFilter.appendChild(andNode);
						}

						OutputStream fluxSortie = new ByteArrayOutputStream();

						LSSerializer serialiseur = implLS.createLSSerializer();
						LSOutput sortie = implLS.createLSOutput();
						sortie.setEncoding("UTF-8");
						sortie.setByteStream(fluxSortie);
						serialiseur.write(documentRequestFilter, sortie);
						fluxSortie.flush();
						fluxSortie.close();
						param = fluxSortie.toString();
					}
					// Fin d'ajout du "remoteFilter"
				}

				// Exécution de la requête utilisateur modifiée au serveur en
				// cours ->s'il y a plusieurs serveurs, alors cet appel se fait
				// plus d'une fois!!!
				if (send) {
					filePath = sendData("POST", getRemoteServerUrl(iServer), param);
				}
			}
			// Fin de la phase de reconstruction de la requête: wfsFilePathList
			// contient les réponses de chaque serveur (une par serveur)
			// *****************************************************************************************************************************

			// *****************************************************************************************************************************
			// Lancement du post traitement
			if (wfsFilePathList.size() > 0) {
				version = version.replaceAll("\\.", "");
				if (version.equalsIgnoreCase("100")) {
					transform(version, currentOperation, req, resp);
				} else {
					dump("ERROR", "Bad wfs version request: 1.0.0 only");
				}
			} else {
				dump("ERROR", "This request has no authorized results!");
			}
			// *****************************************************************************************************************************
			// Fin du post traitement

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
	protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp) {

		try {
			String currentOperation = null;
			String version = "000";
			String service = "";
			Enumeration<String> parameterNames = req.getParameterNames();
			String requestParamUrl = "";
			String paramUrl = "";
			String filter = null;

			HashMap<String, String> env = new HashMap<String, String>();
			String typeName = "";
			// Debug tb 24.06.2009
			String propertyName = "";
			// Fin de Debug

			// Récupération des valeurs des paramètres de la requête utilisateur
			while (parameterNames.hasMoreElements()) {
				String key = (String) parameterNames.nextElement();
				String value = req.getParameter(key);

				if (!key.equalsIgnoreCase("FILTER")) {
					// Le FILTER sera ajouté par la suite s'il existe dans la
					// requête utilisateur
					paramUrl = paramUrl + key + "=" + value + "&";
				}

				if (key.equalsIgnoreCase("TYPENAME")) {
					typeName = value;
				}
				// Debug tb 24.06.2009
				else if (key.equalsIgnoreCase("PROPERTYNAME")) {
					propertyName = value;
				}
				// Fin de Debug
				else if (key.equalsIgnoreCase("Request")) {
					// Gets the requested Operation
					if (value.equalsIgnoreCase("capabilities")) {
						currentOperation = "GetCapabilities";
					} else {
						currentOperation = value;
					}
				} else if (key.equalsIgnoreCase("version")) {
					// Gets the requested version
					version = value;
				} else if (key.equalsIgnoreCase("service")) {
					// Gets the requested service
					service = value;
				} else if (key.equalsIgnoreCase("FILTER")) {
					// Gets the requested Filter
					filter = value;
				}
			}
			// Debug tb 24.06.2009
			requestParamUrl = paramUrl;
			// Fin de debug

			String user = "";
			if (req.getUserPrincipal() != null) {
				user = req.getUserPrincipal().getName();
			}
			if (hasPolicy) {
				if (!isOperationAllowed(currentOperation))
					throw new NoPermissionException("operation is not allowed");
			}

			// *****************************************************************************************************************************
			// Construction des FeaturesTypes et Attributs associés authorisés
			// -> modification de la requête utilisateur en conséquence
			// Passage de la requête utilisateur à plusieurs serveurs si défini
			// dans Policy <server>!!!
			// ATTENTION: Dans cette fonction iServer == à l'index du serveur
			// défini dans CONFIG
			for (int iServer = 0; iServer < getRemoteServerInfoList().size(); iServer++) {
				// Reset the original request url
				paramUrl = requestParamUrl;

				List<String> featureTypeListToKeep = new Vector<String>();
				// Debug tb 24.06.2009
				List<String> attributeListToKeepPerFT = new Vector<String>(); // see
				// the
				// parent
				// ProxyServlet
				// class
				// var
				List<Integer> attributeListToKeepNbPerFT = new Vector<Integer>(); // see
				// the
				// parent
				// ProxyServlet
				// class
				// var
				List<String> attributeListToRemove = new Vector<String>();
				// Fin de debug
				List<String> featureTypeListToRemove = new Vector<String>();
				if (currentOperation != null) {
					if (currentOperation.equalsIgnoreCase("GetFeature") || currentOperation.equalsIgnoreCase("DescribeFeatureType")) {
						String[] fields = typeName.split(",");

						// Begin
						// removeTypesFromGetUrl********************************************************************************
						if (hasPolicy) {
							// Debug tb 25.06.2009
							int ii = 0; // Authorized FeatureType counter
							attributeListToKeepNbPerFT.add(0);
							// Fin de Debug

							// Passe en revue les typesName(=FeatureTypes) de la
							// requête
							for (int i = 0; i < fields.length; i++) {
								// Proxy est non compatible avec les multi Query
								// dans les requêtes GetFeature!
								if (currentOperation.equalsIgnoreCase("GetFeature") && i > 0) {
									break;
								}

								String tmpFT = fields[i];
								if (tmpFT != null) {
									String[] s = tmpFT.split(":");
									tmpFT = s[s.length - 1];
									// Debug tb 03.07.2009
									// Fait doublon avec split":"
									// }
									// if
									// (tmpFT.startsWith(getRemoteServerInfo(iServer).getPrefix())
									// ||
									// getRemoteServerInfo(iServer).getPrefix().length()==0)
									// {
									if (!tmpFT.equals("")) {
										// tmpFT =
										// tmpFT.substring((getRemoteServerInfo(iServer).getPrefix()).length());
										// Fin de Debug
										if (isFeatureTypeAllowed(tmpFT, getRemoteServerUrl(iServer))) {
											featureTypeListToKeep.add(tmpFT);
											// Debug tb 25.06.2009
											// Filtrage des attributs autorisés
											if (currentOperation.equalsIgnoreCase("GetFeature")) {
												String[] fieldsAttribute = propertyName.split(","); // Si
												// req
												// PropertyNames
												// non
												// défini
												// ->
												// Split
												// retourne
												// un
												// élément
												// avec
												// ""

												// Au cas: req PropertyNames
												// défini et [Policy Attributs
												// sur ALL ou sur sous ensemble
												// limité]
												// ou
												// req PropertyNames non défini
												// et Policy Attributs sur sous
												// ensemble limité
												// ou
												// req PropertyNames non défini
												// et Policy Attributs sur All
												for (int k = 0; k < fieldsAttribute.length; k++) //
												{
													String tmpFA = fieldsAttribute[k].toString();
													if (tmpFA != null) {
														String[] ss = tmpFA.split(":");
														tmpFA = ss[ss.length - 1];
													}
													// Comparaison avec le
													// contenu de la Policy
													// Si attributs dans req et
													// aussi dans Policy->
													// isAttributeAllowed
													// retourne vrai pour
													// l'attribut en cours:
													// tmpFA = AttributFromReq
													// Si pas d'attributs dans
													// req et Policy sur All ->
													// isAttributeAllowed edit
													// attributeListToKeepNbPerFT=0
													// et retourne vrai: pour
													// l'attribut en cours:
													// tmpFA = ""
													// Si attributs dans req
													// mais pas dans Policy->
													// isAttributeAllowed
													// retourne faux pour
													// l'attribut en cours:
													// tmpFA = AttributFromReq
													// Si pas d'attributs dans
													// req -> isAttributeAllowed
													// ajoute ceux de la policy
													// dans la globale var
													// policyAttributeListToKeepPerFT
													// et retourne faux pour
													// l'attribut en cours:
													// tmpFA = ""
													if (isAttributeAllowed(getRemoteServerUrl(iServer), tmpFT, tmpFA)) {
														if (!fieldsAttribute[k].toString().equals("")) // au
														// cas:
														// req
														// PropertyNames
														// défini
														// et
														// aussi
														// dans
														// Policy
														{
															attributeListToKeepPerFT.add(tmpFA); // Without
															// namespace
															attributeListToKeepNbPerFT.set(ii, attributeListToKeepNbPerFT.get(ii) + 1);
														} else // au cas: req
														// PropertyNames
														// non défini et
														// Policy sur
														// All
														// (policyAttributeListNb
														// == 0)
														{
															attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
														}
													} else {
														if (!fieldsAttribute[k].toString().equals("")) // au
														// cas:
														// req
														// PropertyNames
														// défini
														// mais
														// pas
														// dans
														// Policy
														{
															attributeListToRemove.add(tmpFA); // Without
															// namespace
														} else if (fieldsAttribute[k].toString().equals("")) // au
														// cas:
														// req
														// PropertyNames
														// non
														// défini,
														// ajout
														// de
														// ceux
														// de
														// la
														// policy
														{
															// policyAttributeListNb
															// est éditer par
															// isAttributeAllowed
															// dans ce cas
															attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
															attributeListToKeepPerFT.addAll(policyAttributeListToKeepPerFT);
														}
													}
												}
												// Au cas: Aucun attribut
												// demandé dans req n'est
												// autorisé dans Policy
												// -> isAttributeAllowed à
												// ajouté ceux de la policy à la
												// globale var
												// policyAttributeListToKeepPerFT
												if (attributeListToKeepNbPerFT.get(ii) == 0 && !fieldsAttribute[0].toString().equals("")) // ajout
												// de
												// ceux
												// de
												// la
												// policy
												{
													isAttributeAllowed(getRemoteServerUrl(iServer), tmpFT, "");
													attributeListToKeepNbPerFT.set(ii, policyAttributeListNb);
													attributeListToKeepPerFT.addAll(policyAttributeListToKeepPerFT);
												}
											}
											// Fin de Debug
										} else {
											featureTypeListToRemove.add(tmpFT);
										}
									}
									// Debug tb 25.06.2009
									ii += 1;
									attributeListToKeepNbPerFT.add(0);
									// Fin de Debug
								} else {
									if (tmpFT.equals("")) {
										featureTypeListToKeep.add("");
									}
								}
							}
							if (!currentOperation.equalsIgnoreCase("DescribeFeatureType")) {
								// Recherche du remoteFilter dans la Policy et
								// Modification de la requête user en
								// conséquence **************

								// S'il n'y a pas de filtre dans la requête
								// utilisateur -> ajouter celui de la policy si
								// défini
								if (filter == null) {
									String userFilter = null;
									if (featureTypeListToKeep.size() > 0) {
										userFilter = getFeatureTypeRemoteFilter(getRemoteServerUrl(iServer), featureTypeListToKeep.get(0));
										featureTypePathList.add(featureTypeListToKeep.get(0));
									}
									// Debug tb 21.05.2009
									// In case remoteFilter is activ but not Set
									if (userFilter != null && !userFilter.equals(""))
									// Fin de Debug
									{
										if (!paramUrl.endsWith("&")) {
											paramUrl = paramUrl + "&";
										}
										paramUrl = paramUrl + "FILTER=" + java.net.URLEncoder.encode(userFilter);
									}
								}
								// S'il y a un filtre dans la requête
								// utilisateur -> ajouter ce dernier et ajouter
								// celui de la policy si définie
								else {
									String userFilter = null;
									if (featureTypeListToKeep.size() > 0) {
										userFilter = getFeatureTypeRemoteFilter(getRemoteServerUrl(iServer), featureTypeListToKeep.get(0));
										// Debug tb 24.06.2009
										featureTypePathList.add(featureTypeListToKeep.get(0));
										// Fin de Debug
									}

									// Debug tb 21.05.2009
									// True if remoteFilter is activ but not Set
									if (userFilter != null && userFilter.equals("")) {
										if (!paramUrl.endsWith("&")) {
											paramUrl = paramUrl + "&";
										}
										paramUrl = paramUrl + "FILTER=" + java.net.URLEncoder.encode(filter);
									}
									// True if remoteFilter is activ and Set
									else if (userFilter != null && !userFilter.equals(""))
									// Fin de Debug
									{
										if (!paramUrl.endsWith("&")) {
											paramUrl = paramUrl + "&";
										}

										InputStream isRequestFilter = new ByteArrayInputStream(filter.getBytes());
										InputStream isUserFilter = new ByteArrayInputStream(userFilter.getBytes());
										DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
										db.setNamespaceAware(true);
										Document documentRequestFilter = db.newDocumentBuilder().parse(isRequestFilter);
										Document documentUserFilter = db.newDocumentBuilder().parse(isUserFilter);

										DOMImplementationLS implLS = null;
										if (documentRequestFilter.getImplementation().hasFeature("LS", "3.0")) {
											implLS = (DOMImplementationLS) documentRequestFilter.getImplementation();
										} else {
											DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
											implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
										}
										NodeList nlRequestFilter = documentRequestFilter.getElementsByTagName("Filter");
										if (nlRequestFilter.getLength() == 0)
											nlRequestFilter = documentRequestFilter.getElementsByTagNameNS("http://www.opengis.net/ogc", "Filter");
										NodeList nlUserFilter = documentUserFilter.getElementsByTagName("Filter");
										if (nlUserFilter.getLength() == 0)
											nlUserFilter = documentUserFilter.getElementsByTagNameNS("http://www.opengis.net/ogc", "Filter");

										// A filter is existing
										// Cause some performance issue. Disable
										// it
										Node nodeRequestFilter = nlRequestFilter.item(0);
										Node nodeUserFilter = nlUserFilter.item(0);
										Node andNode = documentRequestFilter.createElement("And");

										for (int i = 0; i < nodeRequestFilter.getChildNodes().getLength(); i++) {
											andNode.appendChild(nodeRequestFilter.getChildNodes().item(i).cloneNode(true));
										}

										for (int i = 0; i < nodeUserFilter.getChildNodes().getLength(); i++) {
											andNode.appendChild(documentRequestFilter.adoptNode(nodeUserFilter.getChildNodes().item(i).cloneNode(true)));
										}

										while (nodeRequestFilter.hasChildNodes()) {
											nodeRequestFilter.removeChild(nodeRequestFilter.getChildNodes().item(0));
										}

										nodeRequestFilter.appendChild(andNode);

										OutputStream fluxSortie = new ByteArrayOutputStream();

										LSSerializer serialiseur = implLS.createLSSerializer();
										LSOutput sortie = implLS.createLSOutput();
										sortie.setEncoding("UTF-8");
										sortie.setByteStream(fluxSortie);
										serialiseur.write(documentRequestFilter, sortie);
										fluxSortie.flush();
										fluxSortie.close();

										paramUrl = paramUrl + "FILTER=" + java.net.URLEncoder.encode(fluxSortie.toString());
									}
								}
								// Fin de modification de la requête user avec
								// RemoteFilter
								// ***************************************
							}
							// Suppression des features type non autorisés et
							// Suppression des Attributs respectifs
							// Debug tb 25.06.2009
							paramUrl = removeTypesFromGetUrl(featureTypeListToKeep, attributeListToKeepPerFT, attributeListToKeepNbPerFT, paramUrl, iServer,
									currentOperation);
							// Fin de Debug
						}
						// End
						// removeTypesFromGetUrl********************************************************************************

						boolean send = true;
						String filePath = "";

						// Debug tb 23.06.2009
						// Vérifier que la requête avec opération
						// DescribeFeatureType comporte encore au moins 1
						// TypeName sinon voici la réponse à retourner
						if ("DescribeFeatureType".equalsIgnoreCase(currentOperation)) {
							if (featureTypeListToKeep.size() == 0) {
								String s = "<?xml version='1.0' encoding='utf-8' ?>"
										+ "<ogcwfs:FeatureCollection xmlns:ogcwfs=\"http://www.opengis.net/wfs\"   xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" >"
										+ "<gml:boundedBy>" + "<gml:null>unavailable</gml:null>" + "</gml:boundedBy>" + "</ogcwfs:FeatureCollection>";
								File tempFile = createTempFile("requestPreTreatmentPOST" + UUID.randomUUID().toString(), ".xml");

								FileOutputStream tempFos = new FileOutputStream(tempFile);
								tempFos.write(s.getBytes());
								tempFos.flush();
								tempFos.close();
								filePath = tempFile.toString();
								send = false;
							}
						}
						// Fin de Debug

						if ("GetFeature".equalsIgnoreCase(currentOperation)) {
							// Si la requête modifiée ne comporte plus de
							// TypeName voici la réponse à retourner
							if (featureTypeListToKeep.size() == 0) {
								String s = "<?xml version='1.0' encoding='utf-8' ?>"
										+ "<ogcwfs:FeatureCollection xmlns:ogcwfs=\"http://www.opengis.net/wfs\"   xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" >"
										+ "<gml:boundedBy>" + "<gml:null>unavailable</gml:null>" + "</gml:boundedBy>" + "</ogcwfs:FeatureCollection>";
								File tempFile = createTempFile("requestPreTreatmentPOST" + UUID.randomUUID().toString(), ".xml");

								FileOutputStream tempFos = new FileOutputStream(tempFile);
								tempFos.write(s.getBytes());
								tempFos.flush();
								tempFos.close();
								filePath = tempFile.toString();
								send = false;
							}
						}
						// Exécution de la requête utilisateur modifiée au
						// serveur en cours ->s'il y a plusieurs serveurs, alors
						// cet appel se fait plus d'une fois!!!
						if (send)
						// Debug tb 24.06.2009
						{
							// Fin de Debug
							filePath = sendData("GET", getRemoteServerUrl(iServer), paramUrl);
							// Debug tb 24.06.2009
							serversIndex.add(iServer);
							// Fin de Debug
							wfsFilePathList.put(iServer, filePath);
							// Debug tb 24.06.2009
						}
						// Fin de Debug
					}

					// Si l'opération courante est différente de DescribeFeature
					// ou GetFeature
					else {
						String filePath = sendData("GET", getRemoteServerUrl(iServer), paramUrl);
						// Debug tb 24.06.2009
						serversIndex.add(iServer);
						// Fin de Debug
						wfsFilePathList.put(iServer, filePath);
					}
				}
				// Debug tb 24.06.2009
			}
			// Fin de Debug
			// Fin de la phase de reconstruction de la requête: wfsFilePathList
			// contient les réponses de chaque serveur (une par serveur)
			// *****************************************************************************************************************************

			// *****************************************************************************************************************************
			// Lancement du post traitement
			// Debug tb 24.06.2009
			if (wfsFilePathList.size() > 0) {
				// Fin de Debug
				version = version.replaceAll("\\.", "");
				if (version.equalsIgnoreCase("100")) {
					transform(version, currentOperation, req, resp);
				} else {
					dump("ERROR", "Bad wfs version request: 1.0.0 only");
				}
				// Debug tb 24.06.2009
			} else {
				dump("ERROR", "This request has no authorized results!");
			}
			// Fin de Debug
			// *****************************************************************************************************************************
			// Fin du post traitement
			// Debug tb 24.06.2009
			// }
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
	// Debug tb 10.06.2009
	// Retourne l'attribut géométrique de localFilter
	private String getLocalFilterGeomAttribut(int iServer, String featureTypeToKeep) {
		String geomAttributName = "";
		try {
			// Recherche du LocalFilter dans la Policy
			String localFilter = null;

			localFilter = getFeatureTypeLocalFilter(getRemoteServerUrl(iServer), featureTypeToKeep);

			// Récupération de l'attribut geom du localFilter
			// In case localFilter is activ but not Set
			if (localFilter != null && !localFilter.equals("")) {
				InputStream isLocalFilter = new ByteArrayInputStream(localFilter.getBytes());
				DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
				db.setNamespaceAware(true);
				Document documentLocalFilter = db.newDocumentBuilder().parse(isLocalFilter);

				NodeList nlLocalFilter = documentLocalFilter.getElementsByTagName("PropertyName");
				if (nlLocalFilter.getLength() == 0)
					nlLocalFilter = documentLocalFilter.getElementsByTagNameNS("http://www.opengis.net/ogc", "PropertyName");

				// A filter is existing
				// Could cause some performance issue, when query some WFs based
				// onOracle.
				if (nlLocalFilter.getLength() > 0) {
					String tmpFTA = nlLocalFilter.item(0).getTextContent();
					String[] s = tmpFTA.split(":");
					tmpFTA = s[s.length - 1];
					geomAttributName = tmpFTA;
				}
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
		return geomAttributName;
	}

	// Fin de Debug

	// ***************************************************************************************************************************************
	// private String removeTypesFromPOSTUrl(List<String>
	// featureTypeListToKeep,String paramUrl,int iServer,String operation)
	private String removeTypesFromPOSTUrl(List<String> featureTypeListToKeep, List<String> attributeListToKeepPerFT, List<Integer> attributeListToKeepNbPerFT,
			String paramUrl, int iServer, String operation) {
		try {
			List<Node> nodeListToRemove = new Vector<Node>();
			List<Node> nodeAttributeListToRemove = new Vector<Node>();

			// lecture du contenu de la requête utilisateur (paramUrl) dans un
			// document
			InputStream is = new ByteArrayInputStream(paramUrl.getBytes());
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(true);
			Document documentMaster = db.newDocumentBuilder().parse(is);

			DOMImplementationLS implLS = null;
			if (documentMaster.getImplementation().hasFeature("LS", "3.0")) {
				implLS = (DOMImplementationLS) documentMaster.getImplementation();
			} else {
				DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
				implLS = (DOMImplementationLS) enregistreur.getDOMImplementation("LS 3.0");
			}

			// Dans le cas d'un requête portant sur une opération GetFeature
			if ("GetFeature".equalsIgnoreCase(operation)) {
				NodeList nl = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs", "Query");

				// Debug tb 11.06.2009
				// Sauvegarde du prefix et namespace pour le iServer
				String policyServerPrefix = getServerPrefix(getRemoteServerUrl(iServer));
				policyServersPrefix.add(policyServerPrefix);
				String policyServerNamespace = getServerNamespace(getRemoteServerUrl(iServer));
				policyServersNamespace.add(policyServerNamespace);
				// Fin de Debug

				// A) Recherche des FeaturesTypes autorisés
				// for (int i = 0; i < nl.getLength(); i++) {
				boolean isInList = false;
				for (int j = 0; j < featureTypeListToKeep.size(); j++) {
					// Recherche le nom de l'authorized featureType courant
					String tmpFT = nl.item(0).getAttributes().getNamedItem("typeName").getTextContent();
					if (tmpFT != null) {
						String[] s = tmpFT.split(":");
						tmpFT = s[s.length - 1];
					}
					// Debug tb 03.07.2009
					// Fait doublon avec split":"
					// if
					// (tmpFT.startsWith(getRemoteServerInfo(iServer).getPrefix()))
					// {
					// tmpFT =
					// tmpFT.substring((getRemoteServerInfo(iServer).getPrefix()).length());
					// }
					// Fin de Debug

					if (tmpFT.equals(featureTypeListToKeep.get(j))) {
						// Debug tb 05.06.2009
						// Edit l'attribut typeName de <wfs:Query
						// typeName="tmpFT"> avec tmpFT
						// nl.item(i).getAttributes().getNamedItem("typeName").setTextContent(tmpFT);
						// // Non nécessaire de réécrire ce qui est déjà
						// présent
						NodeList antltemp = documentMaster.getElementsByTagNameNS("http://www.opengis.net/ogc", "PropertyName");
						// Debug tb 29.09.2009
						// Pour établir la liste des noeuds PropertyName
						// directement sous la balise <Query> tiré de la
						// requête user
						for (int k = 0; k < antltemp.getLength(); k++) {
							Node node = (Node) antltemp.item(k);
							// renommer les noeuds "PropertyName" autre que
							// ceux directement et uniquement sous <Query>:
							// ex ceux de <Filter>
							if (!"Query".equals(node.getParentNode().getLocalName())) {
								documentMaster.renameNode(node, "http://www.opengis.net/ogc", "MyFilterPropertyName");
							}
						}
						NodeList atnl = documentMaster.getElementsByTagNameNS("http://www.opengis.net/ogc", "PropertyName");

						// XPathFactory factory =
						// XPathFactory.newInstance();
						// XPath xPath = factory.newXPath();
						// NodeList shows = (NodeList)
						// xPath.evaluate("GetFeature/Query/PropertyName",
						// documentMaster, XPathConstants.NODESET);
						// Integer nbn = shows.getLength();
						// shows.item(0).getTextContent();
						// for (int k=0;k<atnl.getLength();k++)
						// {
						// Node node = atnl.item(k);
						// if(!"Query".equals(node.getParentNode().getLocalName()))
						// {
						// node.getParentNode().removeChild(node);
						// }
						// }
						// Fin de Debug

						// Au cas: PropertyNames dans req utilisateur, et
						// restriction dans Policy Attributes
						// A') Recherche des attibuts autorisés pour le
						// FeatureType courant
						if (atnl.getLength() != 0) {
							for (int k = 0; k < atnl.getLength(); k++) {
								boolean isAtInList = false;
								for (int l = 0; l < attributeListToKeepNbPerFT.get(j); l++) {
									String tmpFTA = atnl.item(k).getTextContent();
									if (tmpFTA != null) {
										String[] s = tmpFTA.split(":");
										tmpFTA = s[s.length - 1];
									}
									// Debug tb 03.07.2009
									// Fait doublon avec split":"
									// if
									// (tmpFTA.startsWith(getRemoteServerInfo(iServer).getPrefix())
									// ||
									// getRemoteServerInfo(iServer).getPrefix().length()==0)
									// {
									// tmpFTA =
									// tmpFTA.substring((getRemoteServerInfo(iServer).getPrefix()).length());
									// }
									// Fin de Debug
									if (tmpFTA.equals(attributeListToKeepPerFT.get(j * attributeListToKeepNbPerFT.get(j) + l))) {
										// atnl.item(k).setTextContent(tmpFTA);
										// // Non nécessaire de réécrire ce
										// qui est déjà présent
										isAtInList = true;
									}
								}
								if (!isAtInList) {
									// Attribut présent dans la requête,
									// mais non-autorisés
									nodeAttributeListToRemove.add(atnl.item(k));
								}
							}

							// B') Supressions des Attribut non-autorisés
							// présents dans la requête
							for (int m = 0; m < nodeAttributeListToRemove.size(); m++) {
								nodeAttributeListToRemove.get(m).getParentNode().removeChild(nodeAttributeListToRemove.get(m));
							}
						}

						// Traitement des cas de l'attribut géométrique de
						// localFilter
						String geomAttribut = getLocalFilterGeomAttribut(iServer, featureTypeListToKeep.get(j));
						Boolean hasGeomAttribut = false;

						// Au cas: pas de PropertyName dans req utilisateur
						// ou tous retirés par le if() précédent, mais
						// restriction dans Policy Attributes
						// A') Ajoute des attibuts autorisés de Policy pour
						// le FeatureType courant
						// ou ne fait rien dans le cas ou Attributs
						// autorisée de Policy sur All
						// (attributeListToKeepNbPerFT.get(j)=0)
						if (atnl.getLength() == 0) {
							for (int k = 0; k < attributeListToKeepNbPerFT.get(j); k++) {
								// Debug tb 16.10.2009
								// Si le namespace ogc n'est pas déclaré
								// dans la balise Query de la requete user
								// GetFeature
								// Element docElem =
								// documentMaster.createElement("ogc:PropertyName");
								Element docElem = documentMaster.createElement("PropertyName");
								// Fin de Debug
								docElem.setTextContent(policyServerPrefix + ":" + attributeListToKeepPerFT.get(j * attributeListToKeepNbPerFT.get(j) + k));
								nl.item(0).insertBefore(docElem, nl.item(0).getFirstChild());
								if (geomAttribut.equalsIgnoreCase(attributeListToKeepPerFT.get(j * attributeListToKeepNbPerFT.get(j) + k))) {
									hasGeomAttribut = true;
								}
							}
						}

						// Au cas: localFilter is Set et geom Attribut
						// absent de attributeListToKeepPerFT
						// A') Ajoute l'attribut geom pour le FeatureType
						// courant,
						// -> ce dernier devra ensuite être retiré par XSLT
						// au moment de transform()!!!
						// ou ne s'applique pas dans le cas ou Attributs
						// autorisée de Policy sur All
						// (attributeListToKeepNbPerFT.get(j)=0)
						if (!geomAttribut.equals("") && attributeListToKeepNbPerFT.get(j) != 0) {
							if (atnl.getLength() != 0) {
								for (int k = 0; k < attributeListToKeepNbPerFT.get(j); k++) {
									String tmpFTA = atnl.item(k).getTextContent();
									if (tmpFTA != null) {
										String[] s = tmpFTA.split(":");
										tmpFTA = s[s.length - 1];
									}
									// Debug tb 03.07.2009
									// Fait doublon avec split":"
									// if
									// (tmpFTA.startsWith(getRemoteServerInfo(iServer).getPrefix())
									// ||
									// getRemoteServerInfo(iServer).getPrefix().length()==0)
									// {
									// tmpFTA =
									// tmpFTA.substring((getRemoteServerInfo(iServer).getPrefix()).length());
									// }
									// Fin de Debug
									if (tmpFTA.equals(geomAttribut)) {
										hasGeomAttribut = true;
									}
								}
							}
							if (!hasGeomAttribut) {
								// Contient tout les liens
								// Server-Prefix-Namespace-AuthorizedFeature-GeomAttribute
								WFSProxyGeomAttributes geomAttributesObj = new WFSProxyGeomAttributes(iServer, policyServerPrefix, policyServerNamespace);
								geomAttributesObj.setFeatureTypeName(featureTypeListToKeep.get(j));
								geomAttributesObj.setGeomAttributName(geomAttribut);
								WFSProxyGeomAttributesList.add(geomAttributesObj);

								// Ajoute l'attribut géométrique à la
								// requête utilisateur
								// Debug tb 16.10.2009
								// Si le namespace ogc n'est pas déclaré
								// dans la balise Query de la requete user
								// GetFeature
								// Element docElem =
								// documentMaster.createElement("ogc:PropertyName");
								Element docElem = documentMaster.createElement("PropertyName");
								// Fin de Debug
								docElem.setTextContent(policyServerPrefix + ":" + geomAttribut);
								nl.item(0).insertBefore(docElem, nl.item(0).getFirstChild());
							}
						}
						// Fin de Debug
						// Debug tb 29.09.2009
						// Pour rétablir la liste des noeuds PropertyName de
						// la requête user
						// renommer les noeuds "PropertyName" autre que ceux
						// directement et uniquement sous <Query>: ex ceux
						// de <Filter>
						antltemp = documentMaster.getElementsByTagNameNS("http://www.opengis.net/ogc", "MyFilterPropertyName");
						for (int k = 0; k < antltemp.getLength(); k++) {
							Node node = (Node) antltemp.item(k);
							if (!"Query".equals(node.getParentNode().getLocalName())) {
								documentMaster.renameNode(node, "http://www.opengis.net/ogc", "ogc:PropertyName");
							}
						}
						// Fin de Debug
						isInList = true;
					}
				}
				if (!isInList) {
					// FeatureType présent dans la requête, mais
					// non-autorisés
					nodeListToRemove.add(nl.item(0));
				}
				// }

				// B) Supressions des FeaturesTypes non-autorisés présents dans
				// la requête
				for (int i = 0; i < nodeListToRemove.size(); i++) {
					nodeListToRemove.get(i).getParentNode().removeChild(nodeListToRemove.get(i));
				}
			}

			// Dans le cas d'un requête portant sur une opération
			// DescribeFeatureType
			if ("DescribeFeatureType".equalsIgnoreCase(operation)) {
				NodeList nl = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs", "TypeName");
				for (int i = 0; i < nl.getLength(); i++) {
					boolean isInList = false;
					for (int j = 0; j < featureTypeListToKeep.size(); j++) {
						String tmpFT = nl.item(i).getTextContent();
						if (tmpFT != null) {
							String[] s = tmpFT.split(":");
							tmpFT = s[s.length - 1];
						}
						// Debug tb 03.07.2009
						// Fait doublon avec split":"
						// if
						// (tmpFT.startsWith(getRemoteServerInfo(iServer).getPrefix())
						// ||
						// getRemoteServerInfo(iServer).getPrefix().length()==0)
						// {
						// tmpFT =
						// tmpFT.substring((getRemoteServerInfo(iServer).getPrefix()).length());
						// }
						// Fin de Debug

						if (tmpFT.equals(featureTypeListToKeep.get(j))) {
							String policyServerPrefix = getServerPrefix(getRemoteServerUrl(iServer));
							policyServersPrefix.add(policyServerPrefix);
							nl.item(i).setTextContent(policyServerPrefix + ":" + tmpFT);
							isInList = true;
						}
					}
					if (!isInList) {
						nodeListToRemove.add(nl.item(i));
					}
				}
				for (int i = 0; i < nodeListToRemove.size(); i++) {
					nodeListToRemove.get(i).getParentNode().removeChild(nodeListToRemove.get(i));
				}
			}

			// Ecriture de la requête utilisateur suite aux modifications
			OutputStream fluxSortie = new ByteArrayOutputStream();
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			sortie.setByteStream(fluxSortie);
			serialiseur.write(documentMaster, sortie);
			fluxSortie.flush();
			fluxSortie.close();
			return fluxSortie.toString();
		} catch (Exception e) {
			e.printStackTrace();
		}
		return paramUrl;
	}

	// ***************************************************************************************************************************************
	private String removeTypesFromGetUrl(List<String> featureTypeListToKeep, List<String> attributeListToKeepPerFT, List<Integer> attributeListToKeepNbPerFT,
			String paramUrl, int iServer, String operation) {

		// Debug tb 25.06.2009
		// Sauvegarde du prefix et namespace pour le iServer
		String policyServerPrefix = getServerPrefix(getRemoteServerUrl(iServer));
		policyServersPrefix.add(policyServerPrefix);
		String policyServerNamespace = getServerNamespace(getRemoteServerUrl(iServer));
		policyServersNamespace.add(policyServerNamespace);
		Boolean hasPropertyName = false;
		Boolean isPolicyAll = false;
		if (attributeListToKeepNbPerFT.get(0) == 0) {
			// Indique le fait que Policy est sur ALL pour la featureType
			// courant et que tout les attributs sont demandés
			isPolicyAll = true;
		}
		// Fin de Debug

		// A) Recherche des paramètres de la requête: TYPENAME->FeatureType et
		// PROPERTYNAME->Attributes
		String[] paramFields = paramUrl.split("&");
		for (int i = 0; i < paramFields.length; i++) {
			String[] keyValue = paramFields[i].split("=");
			// B) Ajout des FeaturesTypes autorisés
			if ("TYPENAME".equalsIgnoreCase(keyValue[0])) {
				paramFields[i] = keyValue[0] + "=";
				for (int j = 0; j < featureTypeListToKeep.size(); j++) {
					paramFields[i] = paramFields[i] + policyServerPrefix + ":" + featureTypeListToKeep.get(j) + ",";
				}
				if (paramFields[i].endsWith(",")) {
					paramFields[i] = paramFields[i].substring(0, paramFields[i].length() - 1);
				}
				// break;
			}
			// Debug tb 25.06.2009
			// C) Ajout des Attributs autorisés
			if ("PROPERTYNAME".equalsIgnoreCase(keyValue[0])) // N'est vrai que
			// si
			// opération==GetFeature
			{
				paramFields[i] = keyValue[0] + "=";
				for (int j = 0; j < attributeListToKeepPerFT.size(); j++) {
					paramFields[i] = paramFields[i] + policyServerPrefix + ":" + attributeListToKeepPerFT.get(j) + ",";
				}

				// Au cas: localFilter is Set et geom Attribut absent de
				// attributeListToKeepPerFT
				// D) Ajoute l'attribut geom pour le FeatureType courant,
				// -> ce dernier devra ensuite être retiré par XSLT au moment de
				// transform()!!!
				int attributeIndex = 0;
				for (int j = 0; j < featureTypeListToKeep.size(); j++) {
					String geomAttribut = getLocalFilterGeomAttribut(iServer, featureTypeListToKeep.get(j));
					if (!geomAttribut.equals("")) {
						Boolean hasGeomAttribut = false;

						for (int k = 0; k < attributeListToKeepNbPerFT.get(j); k++) {
							String tmpFTA = attributeListToKeepPerFT.get(attributeIndex + k);
							if (tmpFTA != null) {
								String[] s = tmpFTA.split(":");
								tmpFTA = s[s.length - 1];
							}
							if (tmpFTA.equals(geomAttribut)) {
								hasGeomAttribut = true;
								break;
							}
						}
						if (!hasGeomAttribut) {
							// Contient tout les liens
							// Server-Prefix-Namespace-AuthorizedFeature-GeomAttribute
							WFSProxyGeomAttributes geomAttributesObj = new WFSProxyGeomAttributes(iServer, policyServerPrefix, policyServerNamespace);
							geomAttributesObj.setFeatureTypeName(featureTypeListToKeep.get(j));
							geomAttributesObj.setGeomAttributName(geomAttribut);
							WFSProxyGeomAttributesList.add(geomAttributesObj);

							// Ajoute l'attribut géométrique à la requête
							// utilisateur
							paramFields[i] = paramFields[i] + policyServerPrefix + ":" + geomAttribut + ",";
						}

						attributeIndex += attributeListToKeepNbPerFT.get(j);
					}
				}

				// Supression de la virgule finale
				if (paramFields[i].endsWith(",")) {
					paramFields[i] = paramFields[i].substring(0, paramFields[i].length() - 1);
				}

				hasPropertyName = true;
			}
			// Fin de Debug
		}
		// Debug tb 25.06.2009
		// C') Ajout des Attributs autorisés si pas de demande explicite dans la
		// requête (absence de PROPERTYNAME)
		// et si Policy Attributes != de ALL
		String propertyNameParam = "propertyName=";
		if (!hasPropertyName && "GetFeature".equalsIgnoreCase(operation) && !isPolicyAll) {
			for (int j = 0; j < attributeListToKeepPerFT.size(); j++) {
				propertyNameParam = propertyNameParam + policyServerPrefix + ":" + attributeListToKeepPerFT.get(j) + ",";
			}

			// Au cas: localFilter is Set et geom Attribut absent de
			// attributeListToKeepPerFT
			// D') Ajoute l'attribut geom pour le FeatureType courant,
			// -> ce dernier devra ensuite être retiré par XSLT au moment de
			// transform()!!!
			int attributeIndex = 0;
			for (int j = 0; j < featureTypeListToKeep.size(); j++) {
				String geomAttribut = getLocalFilterGeomAttribut(iServer, featureTypeListToKeep.get(j));
				if (!geomAttribut.equals("")) {
					Boolean hasGeomAttribut = false;

					for (int k = 0; k < attributeListToKeepNbPerFT.get(j); k++) {
						String tmpFTA = attributeListToKeepPerFT.get(attributeIndex + k);
						if (tmpFTA != null) {
							String[] s = tmpFTA.split(":");
							tmpFTA = s[s.length - 1];
						}
						if (tmpFTA.equals(geomAttribut)) {
							hasGeomAttribut = true;
							break;
						}
					}
					if (!hasGeomAttribut) {
						// Contient tout les liens
						// Server-Prefix-Namespace-AuthorizedFeature-GeomAttribute
						WFSProxyGeomAttributes geomAttributesObj = new WFSProxyGeomAttributes(iServer, policyServerPrefix, policyServerNamespace);
						geomAttributesObj.setFeatureTypeName(featureTypeListToKeep.get(j));
						geomAttributesObj.setGeomAttributName(geomAttribut);
						WFSProxyGeomAttributesList.add(geomAttributesObj);

						// Ajoute l'attribut géométrique à la requête
						// utilisateur
						propertyNameParam = propertyNameParam + policyServerPrefix + ":" + geomAttribut + ",";
					}

					attributeIndex += attributeListToKeepNbPerFT.get(j);
				}
			}

			// Supression de la virgule finale
			if (propertyNameParam.endsWith(",")) {
				propertyNameParam = propertyNameParam.substring(0, propertyNameParam.length() - 1);
			}
		}
		// Fin de Debug

		// Réécriture de la requête après modification
		paramUrl = "";
		for (int i = 0; i < paramFields.length; i++) {
			paramUrl = paramUrl + paramFields[i] + "&";
		}
		if (!"propertyName=".equalsIgnoreCase(propertyNameParam)) {
			paramUrl = paramUrl + propertyNameParam;
		} else {
			paramUrl = paramUrl.substring(0, paramUrl.length() - 1);
		}

		return paramUrl;
	}

	// ************************************************************************************************************************************************************************************************************
	public void transform(String version, String currentOperation, HttpServletRequest req, HttpServletResponse resp) {
		try {
			// ******************************************************************************************
			// Création d'un fichier XSLT correspondant à celui possiblement
			// spécifié par l'utilisateur
			String userXsltPath = getConfiguration().getXsltPath();

			if (req.getUserPrincipal() != null) {
				userXsltPath = userXsltPath + "/" + req.getUserPrincipal().getName() + "/";
			}

			userXsltPath = userXsltPath + "/" + version + "/" + currentOperation + ".xsl";
			String globalXsltPath = getConfiguration().getXsltPath() + "/" + version + "/" + currentOperation + ".xsl";
			;

			File xsltFile = new File(userXsltPath);
			boolean isPostTreat = false; // Devient vrai si un xslt user est
			// défini! -> voir fin de la fct
			// transform
			if (!xsltFile.exists()) {
				dump("User postreatment file " + xsltFile.toString() + " does not exist");
				xsltFile = new File(globalXsltPath);
				if (xsltFile.exists()) {
					isPostTreat = true;
				} else {
					dump("Global postreatment file " + xsltFile.toString() + " does not exist");
				}
			} else {
				isPostTreat = true;
			}

			// ******************************************************************************************************
			// Transforms the results using a xslt before sending the response
			// back

			InputStream xml = null;// new
			// FileInputStream(wfsFilePathList.get(0));
			TransformerFactory tFactory = TransformerFactory.newInstance();

			File tempFile = null;
			OutputStream tempFos = null;

			Transformer transformer = null;

			// Selon le choix de l'opération exécutée
			// ***************************************************************
			if (currentOperation != null) {
				if (currentOperation.equalsIgnoreCase("GetCapabilities")) {
					List<File> tempFileCapaList = new Vector<File>();

					// Posttraitement par Server intérrogé ->
					// wfsFilePathList.get(i) contient le fichier réponse
					// *******
					for (int i = 0; i < wfsFilePathList.size(); i++) {

						tempFile = createTempFile("transform_GetCapabilities" + UUID.randomUUID().toString(), ".xml");
						tempFos = new FileOutputStream(tempFile);
						ByteArrayInputStream xslt = null;

						StringBuffer sb = buildCapabilitiesXSLT(req, serversIndex.get(i));
						xslt = new ByteArrayInputStream(sb.toString().getBytes());

						transformer = tFactory.newTransformer(new StreamSource(xslt));
						// Write the result in a temporary file
						xml = new BufferedInputStream(new FileInputStream(wfsFilePathList.get(i)));
						transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
						tempFos.close();
						tempFileCapaList.add(tempFile);
					}
					tempFile = mergeCapabilities(tempFileCapaList);

				} else if (currentOperation.equalsIgnoreCase("DescribeFeatureType")) {
					if (hasPolicy) {
						List<File> tempFileDescribeType = new Vector<File>(); // Utile
						// si
						// wfsFilePathList.size()>1

						// Posttraitement par Server intérrogé ->
						// wfsFilePathList.get(i) contient le fichier réponse
						// ********************
						for (int j = 0; j < wfsFilePathList.size(); j++) {
							// Debug tb 12.05.2009
							Boolean isWFSDescribeFeatureTypeEdit = false;
							// Fin de Debug
							StringBuffer WFSDescribeFeatureType = new StringBuffer();
							WFSDescribeFeatureType
									.append("<xsl:stylesheet version=\"1.00\"  xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:ogcwfs=\"http://www.opengis.net/wfs\" xmlns:gml=\"http://www.opengis.net/gml\"  xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
							// On récupère le gml
							InputStream dataSourceInputStream = new FileInputStream(wfsFilePathList.get(j));
							// PBM à la Ligne suivante: attention "schema" est
							// un singleton: n'est pas cleaner entre chaque
							// appel sur les servlets!
							// Or, "SchemaFactory" merge chaque schéma lu s'il
							// est différent des appels précédents
							// Schema schema =
							// org.geotools.xml.SchemaFactory.getInstance(null,
							// dataSourceInputStream);
							// Debug tb 02.10.2009
							Schema schema = getSchema(dataSourceInputStream);
							// XSISAXHandler contentHandler = new
							// XSISAXHandler(null);
							// XSISAXHandler.setLogLevel(Level.WARNING);
							// SAXParserFactory factory =
							// SAXParserFactory.newInstance();
							// factory.setNamespaceAware(true);
							// factory.setValidating(false);
							// SAXParser parser = factory.newSAXParser();
							// parser.parse(dataSourceInputStream,
							// contentHandler);
							//
							// Schema schema = contentHandler.getSchema();
							// "registerSchema" seulement nécessaire si appeler
							// dans une autre méthode
							// URI targetNamespace=schema.getTargetNamespace();
							// org.geotools.xml.SchemaFactory.registerSchema(targetNamespace,schema);
							// Fin de Debug
							ComplexType[] ct = schema.getComplexTypes();
							// Debug tb 09.06.2009
							org.geotools.xml.schema.Element[] el = schema.getElements();
							dump("transform_DescribeFeature_ComplexType: " + ct.length);
							// Fin de Debug

							for (int i = 0; i < ct.length; i++) {
								// Debug tb 05.10.2009
								// Préparation de tmpFT, récupération du nom du
								// featureType: ComplexType.name correspondant
								// au Element.name
								String tmpFT = "";
								tmpFT = el[i].getName();
								String[] s = tmpFT.split(":");
								tmpFT = s[s.length - 1];
								// Fin de Debug
								if (isFeatureTypeAllowed(tmpFT, getRemoteServerUrl(serversIndex.get(j)))) {
									org.geotools.xml.schema.Element[] elem = ct[i].getChildElements();
									for (int k = 0; k < elem.length; k++) {
										if (!isAttributeAllowed(getRemoteServerUrl(serversIndex.get(j)), tmpFT, elem[k].getName())) {
											// Cela supprime, de la réponse, les
											// Attributs non autorisés du
											// FeatureType courant qui est
											// autorisé
											WFSDescribeFeatureType.append("<xsl:template match=\"//xsd:complexType[@name ='" + ct[i].getName()
													+ "']//xsd:element[@name='" + elem[k].getName() + "']\">");
											WFSDescribeFeatureType.append("</xsl:template>");
											// Debug tb 12.05.2009
											isWFSDescribeFeatureTypeEdit = true;
											// Fin de Debug
										}
									}
								} else {
									// Cela supprime, de la réponse, le
									// FeatureType courant qui est non autorisé
									WFSDescribeFeatureType.append("<xsl:template match=\"//xsd:complexType[@name ='" + ct[i].getName() + "']\">");
									WFSDescribeFeatureType.append("</xsl:template>");
									WFSDescribeFeatureType.append("<xsl:template match=\"//xsd:element[@name ='" + ct[i].getName() + "']\">");
									WFSDescribeFeatureType.append("</xsl:template>");
									// Debug tb 12.05.2009
									isWFSDescribeFeatureTypeEdit = true;
									// Fin de Debug
								}
							}
							// Debug tb 12.05.2009
							// Si une transformation XSLT doit être éxecutée
							if (isWFSDescribeFeatureTypeEdit) {
								// Fin de Debug
								// Cela applique une copie du contenu de la
								// réponse
								WFSDescribeFeatureType.append("  <!-- Whenever you match any node or any attribute -->");
								WFSDescribeFeatureType.append("<xsl:template match=\"node()|@*\">");
								WFSDescribeFeatureType.append("<!-- Copy the current node -->");
								WFSDescribeFeatureType.append("<xsl:copy>");
								WFSDescribeFeatureType.append("<!-- Including any attributes it has and any child nodes -->");
								WFSDescribeFeatureType.append("<xsl:apply-templates select=\"@*|node()\"/>");
								WFSDescribeFeatureType.append("</xsl:copy>");
								WFSDescribeFeatureType.append("</xsl:template>");
								WFSDescribeFeatureType.append("</xsl:stylesheet>");

								// Debug de 15.06.2009
								// Log le timing avant transformation
								DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
								Date d = new Date();
								dump("SYSTEM", "DescribeFeatureTypeBeginTransfoDateTime", dateFormat.format(d));
								// Fin de debug
								xml = new BufferedInputStream(new FileInputStream(wfsFilePathList.get(j)));
								tempFile = createTempFile("transform_DescribeFeatureType" + UUID.randomUUID().toString(), ".xml");
								tempFos = new FileOutputStream(tempFile);
								ByteArrayInputStream xslt = null;
								xslt = new ByteArrayInputStream(WFSDescribeFeatureType.toString().getBytes());
								transformer = tFactory.newTransformer(new StreamSource(xslt));
								// Write the result in a temporary file
								transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
								tempFos.close();
								tempFileDescribeType.add(tempFile);
								// Debug de 15.06.2009
								// Log le timing après transformation
								d = new Date();
								dump("SYSTEM", "DescribeFeatureTypeEndTransfoDateTime", dateFormat.format(d));
								// Fin de debug
								// Debug tb 12.05.2009
							} else {
								xml = new BufferedInputStream(new FileInputStream(wfsFilePathList.get(j)));
								tempFile = createTempFile("transform_DescribeFeatureType" + UUID.randomUUID().toString(), ".xml");
								tempFos = new FileOutputStream(tempFile);
								BufferedOutputStream BufTempFos = new BufferedOutputStream(tempFos);

								// Write the result in a temporary file
								byte byteRead[] = new byte[32768];
								int index = xml.read(byteRead, 0, 32768);
								try {
									while (index != -1) {
										BufTempFos.write(byteRead, 0, index);
										index = xml.read(byteRead, 0, 32768);
									}
									BufTempFos.flush();
									xml.close();
									tempFileDescribeType.add(tempFile);
								} catch (Exception e) {
									e.printStackTrace();
									dump("transform_DescribeFeatureType_BufferedOutputStream ERROR", e.getMessage() + " " + e.getLocalizedMessage() + " "
											+ e.getCause());
									dump("transform_DescribeFeatureType_BufferedOutputStream ERROR", e.toString());
								}
							}
							// Fin de Debug
						}
						// Colle les "tempFile" en un résultat: Util si
						// wfsFilePathList.size()>1
						if (wfsFilePathList.size() > 1)
							tempFile = mergeDescribeFeatureType(tempFileDescribeType);
					}
				} else if (currentOperation.equalsIgnoreCase("GetFeature")) {
					if (hasPolicy) {
						List<File> tempGetFeatureFile = new Vector();

						// Posttraitement par Server intérrogé ->
						// wfsFilePathList.get(i) contient le fichier réponse
						// ***********************
						// Voir dans la Policy <server>
						for (int iFileServer = 0; iFileServer < wfsFilePathList.size(); iFileServer++) {

							// Debug tb 07.05.2009
							// On récupère le srs de la réponse -> srsSource
							// ATTENTION: Cela ne retourne que le srsSource du
							// premier featureType venu dans la réponse du
							// serveur courant
							// DataInputStream est trop lent pour les réponses
							// avec bcp d'entrées (>1000), cette lecture plante
							// Tomcat
							// lors de l'instruction readLine() -> pas de retour
							// à la ligne trouvé
							// La fonction de remplacement lit progressivement
							// le fichier (buffer de 512 caractères) afin de
							// retourner la valeur du "srsName"
							BufferedReader dis = new BufferedReader(new FileReader(wfsFilePathList.get(iFileServer)));
							boolean breakOut = false;
							int bufSize = 512;
							int srsIndex = 1;
							char[] cbuf = new char[bufSize];
							String s = null;

							String srsSource = null;

							dump("GetFeature begin srsName extract");
							if (dis.ready() && dis.markSupported()) {
								dis.mark(bufSize + 1);
								while (dis.read(cbuf, 0, bufSize) != -1) {
									s = new String(cbuf);
									if ((srsIndex = s.indexOf("srsName")) > 0) {
										dis.reset();
										dis.skip(srsIndex); // index of
										// 'srsName' in the
										// buffer
										dis.mark(bufSize + 1);
										dis.read(cbuf, 0, bufSize);
										srsSource = new String(cbuf);
										if (srsSource.indexOf("\"") > 0) {
											srsIndex = srsSource.indexOf("\"") + 1;
											srsSource = srsSource.substring(srsSource.indexOf("\"") + 1);
											if (srsSource.indexOf("\"") > 0) {
												srsSource = srsSource.substring(0, srsSource.indexOf("\""));
												break;
											} else {
												dis.reset();
												dis.skip(srsIndex); // index of
												// first '"'
												// in the
												// buffer
												dis.mark(bufSize + 1);
												srsSource = "";
												while (dis.read(cbuf, 0, bufSize) != -1) {
													s = new String(cbuf);
													srsSource = srsSource.concat(s);
													if (srsSource.indexOf("\"") > 0) {
														srsSource = srsSource.substring(0, srsSource.indexOf("\""));
														breakOut = true;
														break;
													}
												}
											}
										} else if (srsSource.indexOf("\'") > 0) {
											srsIndex = srsSource.indexOf("\'") + 1;
											srsSource = srsSource.substring(srsSource.indexOf("\'") + 1);
											if (srsSource.indexOf("\'") > 0) {
												srsSource = srsSource.substring(0, srsSource.indexOf("\'"));
												break;
											} else {
												dis.reset();
												dis.skip(srsIndex); // index of
												// first "'"
												// in the
												// buffer
												dis.mark(bufSize + 1);
												srsSource = "";
												while (dis.read(cbuf, 0, bufSize) != -1) {
													s = new String(cbuf);
													srsSource = srsSource.concat(s);
													if (srsSource.indexOf("\'") > 0) {
														srsSource = srsSource.substring(0, srsSource.indexOf("\'"));
														breakOut = true;
														break;
													}
												}
											}
										}
									}
									if (breakOut == false) {
										dis.reset();
										dis.skip(bufSize - 8); // 8 because
										// 'srsName'
										// have 7 char
										dis.mark(bufSize + 1);
										srsSource = null;
									} else {
										break;
									}
								}
							}
							dis.close();
							dump("GetFeature end srsName extract");

							// fin de Debug

							// Remplissage de tempFile avec la
							// réponse*******************************************
							dump("GetFeature begin to fill tempFile");
							String tempFileName = "transform_GetFeature" + UUID.randomUUID().toString();
							tempFile = createTempFile(tempFileName, ".xml");
							tempFos = new FileOutputStream(tempFile);

							XMLReader xmlReader = XMLReaderFactory.createXMLReader();
							String user = (String) getUsername(getRemoteServerUrl(serversIndex.get(iFileServer)));
							String password = (String) getPassword(getRemoteServerUrl(serversIndex.get(iFileServer)));
							ResourceResolver rr = null;
							if (user != null && user.length() > 0) {
								rr = new ResourceResolver(user, password);
								xmlReader.setEntityResolver(rr);
							}

							xml = new BufferedInputStream(new FileInputStream(wfsFilePathList.get(iFileServer)));
							BufferedOutputStream BufTempFos = new BufferedOutputStream(tempFos);

							// Write the result in a temporary file
							byte byteRead[] = new byte[32768];
							int index = xml.read(byteRead, 0, 32768);
							try {
								while (index != -1) {
									BufTempFos.write(byteRead, 0, index);
									index = xml.read(byteRead, 0, 32768);
								}
								BufTempFos.flush();
								xml.close();
								tempFos.close();
							} catch (Exception e) {
								e.printStackTrace();
								dump("transform_GetFeature_BufferedOutputStream ERROR", e.getMessage() + " " + e.getLocalizedMessage() + " " + e.getCause());
								dump("transform_GetFeature_BufferedOutputStream ERROR", e.toString());
							}
							dump("GetFeature end to fill tempFile");
							// Fin de remplissage de tempFile avec la réponse

							// Application du filtrage LocalFilter géométrique:
							// polygon de
							// droits*******************************************

							// Récupération de localFilter
							dump("GetFeature begin to apply localFilter");
							String filter = null;
							if (featureTypePathList.size() > 0) {
								// Si localFilter est actif mais not Set ->
								// filter = ""
								if (serversIndex.contains(iFileServer) && featureTypePathList.contains(iFileServer))
									filter = getFeatureTypeLocalFilter(getRemoteServerUrl(serversIndex.get(iFileServer)), featureTypePathList.get(iFileServer));
							}
							// Debug tb 15.06.2009
							// Si un localFilter est défini
							if (filter != null) {
								if (!filter.equals("")) {
									// Fin de Debug
									// Création de "doc" -> un tempFile formaté
									// en GMLFeatureCollection
									Map hints = new HashMap();
									hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);

									GMLFeatureCollection doc = null; // Réutilise
									// le
									// résultat
									// TempFile
									// Debug tb 28.09.2009
									// if (user!=null && user.length()>0)
									// {
									// doc =
									// (GMLFeatureCollection)DocumentFactory.getInstance(tempFile.toURI(),hints,Level.WARNING,user,password);
									// }
									// else
									// Utilisation de la classe Java
									// "Authenticator" qui ajoute
									// l'authentication, selon les besoins, à la
									// classe java "URLConnection".
									// Pour des raisons de vérification de
									// schema xsd (requete DescribeFeatureType),
									// la classe "DocumentFactory" nécessite
									// l'authentication au cas où geoserver
									// défini un compte de service.
									// Voir ProxyServlet.getPassword
									// org.easysdi.security.EasyAuthenticator.setCredientials(user,
									// password);
									// Fin de Debug
									// doc =
									// (GMLFeatureCollection)org.geotools.xml.SchemaFactory.getInstance(null,
									// tempFile.toURI(), Level.WARNING, user,
									// password); //SchemaFactory Instance non
									// castable en une instance
									// GMLFeatureCollection
									// doc =
									// (GMLFeatureCollection)DocumentFactory.getInstance(tempFile.toURI(),hints,Level.WARNING);
									doc = (GMLFeatureCollection) documentFactoryGetInstance(tempFile, user, password);
									// Création du fichier résultat de la
									// proachaine transformation
									File tempFile2 = createTempFile("transform_GetFeature_2_" + UUID.randomUUID().toString(), ".xml");
									tempFos = new FileOutputStream(tempFile2);
									if (filter != null) {
										dump(filter);
									}

									// Application du filtrage LocalFilter
									// géométrique, il s'agit d'une
									// transformation
									filterFC(tempFos, filter, doc, getServletUrl(req), srsSource, serversIndex.get(iFileServer)); // l'entrée
									// "TempFile"
									// a
									// été
									// 'copié'
									// dans
									// "doc"
									// et
									// la
									// sortie
									// "tempFos"
									// édite
									// "TempFile2"

									// Copy du contenu transformé dans tempFile
									if (filter != null) {
										tempFos.close();

										if (tempFile != null)
											tempFile.delete();
										tempFile = tempFile2; // Copy du fichier
										// suite à la
										// transformation
										// géométrique
									}
									// Debug tb 15.06.2009
								}
							}
							dump("GetFeature end to apply localFilter");
							// Fin de Debug

							// Fin de l'application du filtrage LocalFilter
							// géométrique: "TempFile" a été filtré une première
							// fois
							// et est stocké dans "TempFile"

							// Debug tb 10.06.2009
							// Application du filtrage de l'attribut géom s'il
							// existe et qu'a la base il n'est pas
							// autorisé*******************************************
							// Filtrage de l'attribut geom si ajouté uniquement
							// pour les besoins de localFilter
							for (int a = 0; a < WFSProxyGeomAttributesList.size(); a++) {
								dump("GetFeature begin to apply geomRemover");
								if (WFSProxyGeomAttributesList.get(a).getRequestServerIndex() == serversIndex.get(iFileServer)) {
									// Le test qui suit n'a aucune utilité car
									// le proxy actuel ne prend pas en charge
									// les multi featureType avec géométrie
									// non-autorisée
									// -> on parle en effet d'un LocalFilter par
									// Server à l'étape précédente et l'algo de
									// recherche srsName ne retourne que celui
									// du premier featureType venu!
									// Donc pour un serveur déterminé, il ne
									// peut correspondre que un featureType dans
									// ca cas
									// Donc le test if Serveur n'est valable que
									// lorsque a ==
									// serversIndex.get(iFileServer)
									if (WFSProxyGeomAttributesList.get(a).getFeatureTypeName() == WFSProxyGeomAttributesList.get(a).getFeatureTypeName()) {
										// Log le timing avant transformation
										DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
										Date d = new Date();
										dump("SYSTEM", "GetFeatureTypeGeomBeginTransfoDateTime", dateFormat.format(d));

										// Création du fichier résultat de la
										// prochaine transformation
										File tempFile2 = createTempFile("transform_GetFeature_3_" + UUID.randomUUID().toString(), ".xml");
										tempFos = new FileOutputStream(tempFile2);
										ByteArrayInputStream xslt = null;

										// Construction du fichier xslt:
										// traitement intermédiaire indépendant
										xslt = new ByteArrayInputStream(buildGetFeatureXSLT(a, a).toString().getBytes());

										xml = new BufferedInputStream(new FileInputStream(tempFile));
										SAXSource saxSource = new SAXSource(xmlReader, new InputSource(xml));
										transformer = tFactory.newTransformer(new StreamSource(xslt));
										// write the tempFile via tempFos
										transformer.transform(saxSource, new StreamResult(tempFos));

										tempFos.close();

										if (tempFile != null)
											tempFile.delete();
										tempFile = tempFile2; // Copy du fichier
										// suite à la
										// suppression
										// de l'attribut
										// geom
										// Log le timing après transformation
										d = new Date();
										dump("SYSTEM", "GetFeatureTypeGeomEndTransfoDateTime", dateFormat.format(d));
									}
								}
								dump("GetFeature end to apply geomRemover");
							}
							// Fin de l'application du filtrage de l'attribut
							// géom: la réponse à la requête a été filtrée
							// une deuxiême fois et est stocké dans "TempFile"
							tempGetFeatureFile.add(tempFile);
						}
						// Fin de Debug

						// Construction du fichier xslt: phase 2, jointure des
						// résultats des différents serveurs
						// Debug tb 13.05.2009
						// Au cas où il exite plus d'un serveur qui donne
						// réponse: isWFSGetFeatureRenameFtEdit = true
						if (wfsFilePathList.size() > 1) {
							// Fin de Debug
							dump("GetFeature begin to merge servers Results");
							// Appel à la fonction d'application de la
							// transformation XSLT finale (contenu de
							// "WFSGetFeatureRenameFt")
							// "tempGetFeatureFile" contient entre autre
							// "TempFile" (en fait ici uniquement "TempFile"
							// si seulement un serveur est appelé)
							// Attention: filtrage LocalFilter rend impossible
							// l'application de la nouvelle transformation xslt
							// ci-après
							// Le fichier "tempFile" est devenu non
							// utilisable!!! -> "internal serveur error" lorsque
							// trop de résultats
							tempFile = mergeGetFeatures(tempGetFeatureFile, tFactory, transformer);
							dump("GetFeature end to merge servers Results");
							// Debug tb 13.05.2009
						}
						// Fin de Debug
					}
				}

				// Envoie du résultat des traitements "TempFile" sur la réponse
				// à la requête *******************************************

				/*
				 * if a user xslt file exists then post-treat again "TempFile"
				 * and write the response
				 */
				if (isPostTreat) {
					dump("GetFeature begin to apply user xslt");
					PrintWriter out = resp.getWriter();
					transformer = tFactory.newTransformer(new StreamSource(xsltFile)); // Voir
					// definition
					// en
					// haut
					// de
					// fct
					// transform
					if (tempFile != null)
						transformer.transform(new StreamSource(tempFile), new StreamResult(out));
					// Pourquoi le fichier du premier serveur??? ->
					// wfsFilePathList.get(0) et pas le résultat joint des
					// transformations sur les serveur: TempFile
					else
						transformer.transform(new StreamSource(wfsFilePathList.get(0)), new StreamResult(out));
					// delete the temporary file
					tempFile.delete();
					out.close();
					// the job is done. we can go out
					dump("GetFeature end to apply user xslt");
					return;
				}
			}

			// No post rule to apply.
			// Copy the file result on the output stream

			resp.setContentType("text/xml");

			InputStream is = null;
			if (tempFile == null) {
				is = new FileInputStream(wfsFilePathList.get(0));
				resp.setContentLength((int) new File(wfsFilePathList.get(0)).length());
			} else {
				is = new FileInputStream(tempFile);
				resp.setContentLength((int) tempFile.length());
			}

			// Ecriture du fichier dans le flux de sortie
			// OutputStream os = resp.getOutputStream();
			BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());

			byte byteRead[] = new byte[32768];
			int index = is.read(byteRead, 0, 32768);
			try {
				while (index != -1) {
					os.write(byteRead, 0, index);
					index = is.read(byteRead, 0, 32768);
				}
				os.flush();
				is.close();
			} catch (Exception e) {
				e.printStackTrace();
				dump("BufferedOutputStream ERROR", e.getMessage() + " " + e.getLocalizedMessage() + " " + e.getCause());
				dump("BufferedOutputStream ERROR", e.toString());
			} finally {
				DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
				Date d = new Date();
				dump("SYSTEM", "ClientResponseDateTime", dateFormat.format(d));

				if (tempFile != null) {
					dump("SYSTEM", "ClientResponseLength", tempFile.length());
					tempFile.delete();
				}
			}
		} catch (Exception e) {
			e.printStackTrace();
			dump("Transform ERROR", e.toString());
		}
	}

	// ***************************************************************************************************************************************
	private File mergeGetFeatures(List<File> tempGetFeaturesList, TransformerFactory tFactory, Transformer transformer) {
		if (tempGetFeaturesList.size() == 0)
			return null;

		try {
			File fMaster = tempGetFeaturesList.get(0);
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(true);
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
				return fMaster;
			}
			NodeList nlMaster = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs", "FeatureCollection");
			Node ItemMaster = nlMaster.item(0);
			for (int i = 1; i < tempGetFeaturesList.size(); i++) {
				File fChild = tempGetFeaturesList.get(i);
				Document documentChild = db.newDocumentBuilder().parse(fChild);
				NodeList nlFeatureMember = documentChild.getElementsByTagNameNS("http://www.opengis.net/gml", "featureMember");
				for (int j = 0; j < nlFeatureMember.getLength(); j++) {
					ItemMaster.insertBefore(documentMaster.importNode(nlFeatureMember.item(j).cloneNode(true), true), null);
				}
			}

			File f = createTempFile("mergeGetFeatures_f_" + UUID.randomUUID().toString(), ".xml");
			FileOutputStream fluxSortie = new FileOutputStream(f);
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			sortie.setSystemId(f.toString());
			sortie.setByteStream(fluxSortie);
			serialiseur.write(documentMaster, sortie);
			fluxSortie.flush();
			fluxSortie.close();

			return f;
		} catch (Exception e) {
			e.printStackTrace();
			dump("mergeGetFeatures ERROR", e.getMessage());
			return null;
		}
	}

	// ***************************************************************************************************************************************
	private File mergeDescribeFeatureType(List<File> tempFileDescribeType) {
		if (tempFileDescribeType.size() == 0)
			return null;
		try {
			File fMaster = tempFileDescribeType.get(0);
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(true);
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
				return fMaster;
			}
			NodeList nlMaster = documentMaster.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema", "schema");
			Node ItemMaster = nlMaster.item(0);

			NodeList nlElements = documentMaster.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema", "element");
			for (int i = 0; i < nlElements.getLength(); i++) {
				if (nlElements.item(i).getAttributes().getNamedItem("substitutionGroup") != null) {
					if ("gml:_Feature".equals(nlElements.item(i).getAttributes().getNamedItem("substitutionGroup").getTextContent())) {
						String origName = nlElements.item(i).getAttributes().getNamedItem("name").getTextContent();
						nlElements.item(i).getAttributes().getNamedItem("name").setTextContent(getRemoteServerInfo(0).getPrefix() + ":" + origName);
					}
				}

			}

			for (int i = 1; i < tempFileDescribeType.size(); i++) {
				Document documentChild = db.newDocumentBuilder().parse(tempFileDescribeType.get(i));

				NodeList nl1 = documentChild.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema", "schema");
				NodeList nl2 = nl1.item(0).getChildNodes();

				nlElements = documentChild.getElementsByTagNameNS("http://www.w3.org/2001/XMLSchema", "element");
				for (int j = 0; j < nlElements.getLength(); j++) {
					if (nlElements.item(j).getAttributes().getNamedItem("substitutionGroup") != null) {
						if ("gml:_Feature".equals(nlElements.item(j).getAttributes().getNamedItem("substitutionGroup").getTextContent())) {
							String origName = nlElements.item(j).getAttributes().getNamedItem("name").getTextContent();
							nlElements.item(j).getAttributes().getNamedItem("name").setTextContent(getRemoteServerInfo(i).getPrefix() + ":" + origName);
						}
					}

				}

				for (int j = 0; j < nl2.getLength(); j++) {
					ItemMaster.insertBefore(documentMaster.importNode(nl2.item(j).cloneNode(true), true), null);
				}

			}

			File f = createTempFile("mergeDescribeFeatureType_f_" + UUID.randomUUID().toString(), ".xml");

			FileOutputStream fluxSortie = new FileOutputStream(f);
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			sortie.setSystemId(f.toString());
			sortie.setByteStream(fluxSortie);
			serialiseur.write(documentMaster, sortie);
			fluxSortie.flush();
			fluxSortie.close();

			return f;
		} catch (Exception e) {
			e.printStackTrace();
			dump("ERROR", e.getMessage());
			return null;
		}
	}

	// ***************************************************************************************************************************************
	private File mergeCapabilities(List<File> tempFileCapa) {

		if (tempFileCapa.size() == 0)
			return null;
		try {
			File fMaster = tempFileCapa.get(0);
			DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
			db.setNamespaceAware(true);
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
				return fMaster;
			}
			NodeList nlMaster = documentMaster.getElementsByTagNameNS("http://www.opengis.net/wfs", "FeatureTypeList");
			Node ItemMaster = nlMaster.item(0);

			for (int i = 1; i < tempFileCapa.size(); i++) {

				Document documentChild = db.newDocumentBuilder().parse(tempFileCapa.get(i));
				NodeList nl = documentChild.getElementsByTagNameNS("http://www.opengis.net/wfs", "FeatureType");

				for (int j = 0; j < nl.getLength(); j++) {
					ItemMaster.insertBefore(documentMaster.importNode(nl.item(j).cloneNode(true), true), null);
				}
			}

			File f = createTempFile("mergeCapabilities_f_" + UUID.randomUUID().toString(), ".xml");

			FileOutputStream fluxSortie = new FileOutputStream(f);
			LSSerializer serialiseur = implLS.createLSSerializer();
			LSOutput sortie = implLS.createLSOutput();
			sortie.setEncoding("UTF-8");
			sortie.setSystemId(f.toString());
			sortie.setByteStream(fluxSortie);
			serialiseur.write(documentMaster, sortie);
			fluxSortie.flush();
			fluxSortie.close();

			return f;
		} catch (Exception e) {
			e.printStackTrace();
			dump("ERROR", e.getMessage());
			return null;
		}
	}

	// ***************************************************************************************************************************************
	private Schema getSchema(InputStream dataSourceInputStream) throws SAXException {
		try {
			XSISAXHandler contentHandler = new XSISAXHandler(null);
			XSISAXHandler.setLogLevel(Level.WARNING);
			setParser();
			parser.parse(dataSourceInputStream, contentHandler);
			return contentHandler.getSchema();
		} catch (IOException e) {
			e.printStackTrace();
			return null;
		}
	}

	// ***************************************************************************************************************************************

	private void setParser() throws SAXException {
		if (parser == null) {
			SAXParserFactory spfactory = SAXParserFactory.newInstance();
			spfactory.setNamespaceAware(true);
			spfactory.setValidating(false);

			try {
				parser = spfactory.newSAXParser();
			} catch (ParserConfigurationException e) {
				throw new SAXException(e);
			} catch (SAXException e) {
				throw new SAXException(e);
			}
		}
	}

	// ***************************************************************************************************************************************
	/**
	 * Construit le xslt du filtrage de l'attribut géométrique servant au
	 * LocalFilter
	 * 
	 * @param int listIndex : index pointant l'item de la liste
	 *        WFSProxyGeomAttributesList comportant l'attribut géom à retirer
	 * @param int iServer : index pointant le serveur courant
	 * @return list<String> : code de la transformation XSLT
	 */
	private StringBuffer buildGetFeatureXSLT(int listIndex, int iServer) {
		StringBuffer WFSGetFeature = new StringBuffer();

		// Debug tb 15.06.2009
		String prefix = WFSProxyGeomAttributesList.get(iServer).getpolicyServerPrefix();
		String featureType = WFSProxyGeomAttributesList.get(listIndex).getFeatureTypeName();
		String geomAttribut = WFSProxyGeomAttributesList.get(listIndex).getGeomAttributName();

		String nameSpace = WFSProxyGeomAttributesList.get(iServer).getpolicyServerNamespace();
		// On le considère ici indépendant de la requête utilisateur, à la seul
		// fin de satisfaire la contrainte xsl!!!
		// Faux la transformation ne s'applique correctement que si nameSpace
		// est défini en fonction de la requête d'origine...

		WFSGetFeature
				.append("<xsl:stylesheet version=\"1.00\" xmlns:ogcwfs=\"http://www.opengis.net/wfs\" xmlns:gml=\"http://www.opengis.net/gml\"  xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n");
		WFSGetFeature.append("<xsl:template xmlns:" + prefix + "=\"" + nameSpace + "\" match=\"//" + prefix + ":" + featureType + "/" + prefix + ":"
				+ geomAttribut + "\">\n");
		WFSGetFeature.append("</xsl:template>\n");
		WFSGetFeature.append("  <!-- Whenever you match any node or any attribute -->");
		WFSGetFeature.append("<xsl:template match=\"node()|@*\">\n");
		WFSGetFeature.append("<!-- Copy the current node -->\n");
		WFSGetFeature.append("<xsl:copy>\n");
		WFSGetFeature.append("<!-- Including any attributes it has and any child nodes -->\n");
		WFSGetFeature.append("<xsl:apply-templates select=\"@*|node()\"/>\n");
		WFSGetFeature.append("</xsl:copy>\n");
		WFSGetFeature.append("</xsl:template>\n");
		WFSGetFeature.append("</xsl:stylesheet>\n");
		return WFSGetFeature;
		// Fin de debug
	}

	// ***************************************************************************************************************************************

	// Applique le filtrage LocalFilter géométrique, il s'agit d'une
	// transformation
	public void filterFC(OutputStream os, String customFilter, GMLFeatureCollection doc, String urlServlet, String srsDest, int iServer) {

		try {

			// Debug de 15.06.2009
			// Log le timing avant transformation
			DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
			Date d = new Date();
			dump("SYSTEM", "LocalFilterBeginTransfoDateTime", dateFormat.format(d));
			// Fin de debug

			Filter filter = null;
			// Lecture du contenu de localFilter(ici le param
			// customFilter)**********************************
			if (customFilter != null) {
				InputStream is = new ByteArrayInputStream(customFilter.getBytes());

				DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
				DocumentBuilder db = dbf.newDocumentBuilder();
				Document dom = db.parse(is);
				// first grab a filter node
				NodeList nodes = dom.getElementsByTagName("Filter");
				if (nodes.getLength() == 0) {
					nodes = dom.getElementsByTagNameNS("http://www.opengis.net/ogc", "Filter");
				}
				for (int j = 0; j < nodes.getLength(); j++) {
					Element filterNode = (Element) nodes.item(j);
					NodeList list = filterNode.getChildNodes();
					Node child = null;

					for (int k = 0; k < list.getLength(); k++) {
						child = list.item(k);
						if ((child == null) || (child.getNodeType() != Node.ELEMENT_NODE)) {
							continue;
						}
						filter = FilterDOMParser.parseFilter(child);
					}
				}
			}

			// Application de
			// filter************************************************************
			FeatureTransformer ft = new FeatureTransformer();
			ft.setNamespaceDeclarationEnabled(true);

			ft.setCollectionPrefix(null);
			ft.setGmlPrefixing(true);
			ft.setIndentation(2);
			FeatureCollection fc = null;
			if (filter != null) {
				System.setProperty("org.geotools.referencing.forceXY", "true");
				// Transform the srs of the filter if needed.
				String srsSource = customFilter.substring(customFilter.indexOf("srsName"));
				if (srsSource.indexOf("\"") > 0) {
					srsSource = srsSource.substring(srsSource.indexOf("\"") + 1);
					srsSource = srsSource.substring(0, srsSource.indexOf("\""));
				} else {
					srsSource = srsSource.substring(srsSource.indexOf("\'") + 1);
					srsSource = srsSource.substring(0, srsSource.indexOf("\'"));
				}
				if (srsDest == null)
					srsDest = srsSource;

				CoordinateReferenceSystem sourceCRS = CRS.decode(srsSource);
				CoordinateReferenceSystem targetCRS = CRS.decode(srsDest);
				if (!srsSource.equals(srsDest)) {

					/*
					 * AffineTransform
					 * t=AffineTransform.getTranslateInstance(0,0);
					 * DefaultMathTransformFactory fac=new
					 * DefaultMathTransformFactory(); MathTransform mt =
					 * fac.createAffineTransform(new GeneralMatrix(t));
					 */

					FilterTransformer filterTransformer = new FilterTransformer(CRS.findMathTransform(sourceCRS, targetCRS));
					// Due to an issue in GeoTools
					// MinX MinY MaxX and MaxY are not set for the BBOXImpl.
					// Do it ourself
					if (filter instanceof org.geotools.filter.spatial.BBOXImpl) {
						Object obj = null;
						if (((org.geotools.filter.spatial.BBOXImpl) filter).getRightGeometry() instanceof org.geotools.filter.LiteralExpressionImpl) {
							obj = (((org.geotools.filter.LiteralExpressionImpl) ((org.geotools.filter.spatial.BBOXImpl) filter).getRightGeometry()).getValue());
						}
						if (((org.geotools.filter.spatial.BBOXImpl) filter).getLeftGeometry() instanceof org.geotools.filter.LiteralExpressionImpl) {
							obj = (((org.geotools.filter.LiteralExpressionImpl) ((org.geotools.filter.spatial.BBOXImpl) filter).getLeftGeometry()).getValue()
									.getClass());
						}
						if (obj != null && obj instanceof com.vividsolutions.jts.geom.Polygon) {
							((org.geotools.filter.spatial.BBOXImpl) filter)
									.setMinX(((com.vividsolutions.jts.geom.Polygon) obj).getEnvelopeInternal().getMinX());
							((org.geotools.filter.spatial.BBOXImpl) filter)
									.setMinY(((com.vividsolutions.jts.geom.Polygon) obj).getEnvelopeInternal().getMinY());
							((org.geotools.filter.spatial.BBOXImpl) filter)
									.setMaxY(((com.vividsolutions.jts.geom.Polygon) obj).getEnvelopeInternal().getMaxY());
							((org.geotools.filter.spatial.BBOXImpl) filter)
									.setMaxX(((com.vividsolutions.jts.geom.Polygon) obj).getEnvelopeInternal().getMaxX());
						}
					}
					Filter filter2 = (Filter) filter.accept(filterTransformer, null);
					// Si filter à srsSource (de LocalFilter) != srsDest (de
					// TempFile, la réponse du serveur courant) le contenu de
					// doc est modfié selon filter2
					fc = doc.subCollection(filter2);
				}
				// Si filter à srsSource (de LocalFilter) == srsDest (de
				// TempFile, la réponse du serveur courant) le contenu de doc
				// est modfié selon filter
				else {
					fc = doc.subCollection(filter);
				}
			}
			// filter est null, aucun changement n'est effectué sur le contenu
			// de doc (-> TempFile)
			else
				fc = doc;

			// Debug de 15.06.2009
			// Construction des pramètres de ft afin de préparer la sortie du
			// résultat filtré
			// -> option désactivée: renomage du prefix
			FeatureIterator it = fc.features();
			int i = 0;
			String lastTypeName = "";
			while (it.hasNext()) {
				Feature feature = it.next();

				if (!feature.getFeatureType().getTypeName().equals(lastTypeName)) {
					String prefix = policyServersPrefix.get(iServer);
					ft.getFeatureTypeNamespaces().declareNamespace(feature.getFeatureType(), prefix, feature.getFeatureType().getNamespace().toString());
					ft.setSrsName((String) feature.getDefaultGeometry().getUserData());
					lastTypeName = feature.getFeatureType().getTypeName();
				}
				i++;
			}
			// Ecriture du résultat dans le fichier pointé par os
			ft.transform(fc, os);

			// Log le timing après transformation
			d = new Date();
			dump("SYSTEM", "LocalFilterEndTransfoDateTime", dateFormat.format(d));
			// Fin de debug

		} catch (Exception e) {
			e.printStackTrace();
			dump("FilterFC ERROR", e.getMessage());
		}

	}
}