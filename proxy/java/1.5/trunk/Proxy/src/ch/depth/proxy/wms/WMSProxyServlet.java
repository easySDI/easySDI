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
package ch.depth.proxy.wms;

import java.awt.AlphaComposite;
import java.awt.Color;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.RenderingHints;
import java.awt.geom.Rectangle2D;
import java.awt.image.BufferedImage;
import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URL;
import java.net.URLDecoder;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Collections;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.UUID;
import java.util.Vector;
import java.util.logging.Level;

import javax.imageio.ImageIO;
import javax.imageio.ImageWriter;
import javax.imageio.stream.FileImageOutputStream;
import javax.imageio.stream.ImageOutputStream;
import javax.media.jai.GeometricOpImage;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.apache.xerces.parsers.XMLParser;
import org.geotools.data.WorldFileReader;
import org.geotools.data.memory.MemoryDataStore;
import org.geotools.data.memory.MemoryFeatureCollection;
import org.geotools.data.ows.CRSEnvelope;
import org.geotools.data.ows.FeatureSetDescription;
import org.geotools.data.ows.Layer;
import org.geotools.data.ows.WMSCapabilities;
import org.geotools.data.wms.WebMapServer;
import org.geotools.data.wms.request.GetMapRequest;
import org.geotools.data.wms.response.GetMapResponse;
import org.geotools.data.wms.xml.WMSSchema;
import org.geotools.factory.GeoTools;
import org.geotools.factory.Hints;
import org.geotools.feature.AttributeType;
import org.geotools.feature.AttributeTypeFactory;
import org.geotools.feature.FeatureCollection;
import org.geotools.feature.FeatureType;
import org.geotools.feature.FeatureTypes;
import org.geotools.feature.GeometryAttributeType;
import org.geotools.feature.type.GeometricAttributeType;
import org.geotools.gce.image.WorldImageReader;
import org.geotools.geometry.jts.GeometryCoordinateSequenceTransformer;
import org.geotools.geometry.jts.JTS;
import org.geotools.geometry.jts.ReferencedEnvelope;
import org.geotools.gml.producer.GeometryTransformer;
import org.geotools.gml2.bindings.GML2ParsingUtils;
import org.geotools.image.ImageWorker;
import org.geotools.map.DefaultMapContext;
import org.geotools.map.MapContext;
import org.geotools.referencing.CRS;
import org.geotools.referencing.FactoryFinder;
import org.geotools.referencing.NamedIdentifier;
import org.geotools.referencing.crs.DefaultGeographicCRS;
import org.geotools.referencing.factory.OrderedAxisAuthorityFactory;
import org.geotools.referencing.operation.DefaultMathTransformFactory;
import org.geotools.renderer.lite.RendererUtilities;
import org.geotools.util.Converter;
import org.geotools.util.GeometryConverterFactory;
import org.geotools.xml.DocumentFactory;
import org.geotools.xml.DocumentWriter;
import org.geotools.xml.gml.GMLFeatureCollection;
import org.geotools.xml.handlers.DocumentHandler;
import org.geotools.xml.wfs.WFSSchema;
import org.integratedmodelling.geospace.gis.FeatureRasterizer;
import org.opengis.referencing.crs.CRSAuthorityFactory;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.opengis.referencing.operation.CoordinateOperation;
import org.opengis.referencing.operation.CoordinateOperationFactory;
import org.opengis.referencing.operation.MathTransform;
import org.opengis.referencing.operation.MathTransformFactory;
import org.w3c.dom.Document;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.DOMImplementationLS;
import org.w3c.dom.ls.LSOutput;
import org.w3c.dom.ls.LSParser;
import org.w3c.dom.ls.LSSerializer;
import org.xml.sax.EntityResolver;
import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

import com.vividsolutions.jts.geom.Coordinate;
import com.vividsolutions.jts.geom.Envelope;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.geom.GeometryFactory;
import com.vividsolutions.jts.geom.IntersectionMatrix;
import com.vividsolutions.jts.geom.LineString;
import com.vividsolutions.jts.geom.LinearRing;
import com.vividsolutions.jts.geom.Point;
import com.vividsolutions.jts.geom.Polygon;
import com.vividsolutions.jts.geom.PrecisionModel;
import com.vividsolutions.jts.io.WKTReader;

import ch.depth.proxy.core.ProxyServlet;
import ch.depth.proxy.policy.Operation;
import ch.depth.xml.documents.RemoteServerInfo;
import ch.depth.xml.handler.PolicyHandler;
import ch.depth.xml.resolver.ResourceResolver;

/**
 * If no xslt is found in the path, generate the default one that will change the IP address and remove the wrong operation  
 * @author rmi
 */
public class WMSProxyServlet extends ProxyServlet {

	
//***************************************************************************************************************************************    
//Debug tb 08.07.2009
	private Vector<String> serverUrlPerfilePathList = new Vector<String>(); // Url du serveur ayant renvoy� la r�ponse i.
	//private Vector<String> filterPerFilePathList = new Vector<String>(); // Filtre du groupe de layers ayant renvoy� la r�ponse i.
//Fin de Debug
    private String layers;
    private String styles;				    					   

	
//***************************************************************************************************************************************    
	
    protected StringBuffer buildCapabilitiesXSLT(HttpServletRequest req,int remoteServerIndex)
    	{

		try 
			{
		    String user="";
		    if(req.getUserPrincipal() != null)
		    	{
		    	user= req.getUserPrincipal().getName();
		    	}
	
		    String url = getServletUrl(req);
	
		    try 
		    	{		
		    	StringBuffer WMSCapabilities111 = new StringBuffer ();		
	
	
				WMSCapabilities111.append("<xsl:stylesheet version=\"1.00\" xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
	
				WMSCapabilities111.append("<xsl:template match=\"OnlineResource\">");
				WMSCapabilities111.append("<OnlineResource>"); 
				WMSCapabilities111.append("<xsl:attribute name=\"xlink:href\">");
		
				WMSCapabilities111.append(url);				
		
				WMSCapabilities111.append("</xsl:attribute>");
				WMSCapabilities111.append("</OnlineResource>");
				WMSCapabilities111.append("</xsl:template>");
		
				//Filtrage xsl des op�rations
				if(hasPolicy)
					{
					if (!policy.getOperations().isAll())
						{
						List<Operation> operationList =policy.getOperations().getOperation();  
						for (int i=0;i<operationList.size();i++)
							{
						    if (operationList.get(i).getName() !=null)
						    	{
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
				//hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);
				hints.put(DocumentHandler.DEFAULT_NAMESPACE_HINT_KEY, WMSSchema.getInstance());
				hints.put(DocumentFactory.VALIDATION_HINT, Boolean.FALSE);
		
				WMSCapabilities capa  = (WMSCapabilities)DocumentFactory.getInstance(new File(filePathList.get(remoteServerIndex)).toURI(),hints,Level.WARNING);	
	
				//Filtrage xsl des layers
				if(hasPolicy)
					{
					Iterator<Layer> itLayer = capa.getLayerList().iterator();
					while(itLayer.hasNext())
						{
						Layer l = (Layer)itLayer.next();
//Debug tb 03.07.2009
			    		String tmpFT = l.getName();
			    		if (tmpFT!=null)
			    			{
			    			String [] s = tmpFT.split(":");
			    			tmpFT = s[s.length-1];
			    			}
			    		if (!isLayerAllowed(tmpFT, getRemoteServerUrl(remoteServerIndex)))
//Fin de Debug
						//if (!isLayerAllowed(l.getName(), getRemoteServerUrl(remoteServerIndex)))
							{
							//Si couche pas permise alors on l'enl�ve
							WMSCapabilities111.append("<xsl:template match=\"//Layer[starts-with(Name,'"+l.getName()+"')]");
							WMSCapabilities111.append("\"></xsl:template>");		    
							}
						}		
					}
				
//Debug tb 03.07.2009	
//-> le prefix est d�j� int�gr� dans l.getName!
//				//Add the WMSxx_ Prefix before the name of the layer. 
//				//This prefix will be used to find to witch remote server the layer belongs.
//				if (getRemoteServerInfo(remoteServerIndex).getPrefix().length()>0)
//					{
//				    WMSCapabilities111.append("<xsl:template match=\"//Layer/Name\">");
//				    WMSCapabilities111.append("<Name>"+getRemoteServerInfo(remoteServerIndex).getPrefix()+"<xsl:value-of select=\".\"/> </Name>");
//				    WMSCapabilities111.append("</xsl:template>");
//					}
//Fin de Debug
	
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
		    	} 
		    catch (Exception e) 
		    	{
		    	// TODO Auto-generated catch block
		    	e.printStackTrace();
		    	dump("ERROR",e.getMessage());
		    	}
	
		    //If something goes wrong, an empty stylesheet is returned.	
		    StringBuffer sb = new StringBuffer();		
		    return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
			} 
		catch (Exception e) 
			{
		    // TODO Auto-generated catch block
		    e.printStackTrace();
		    dump("ERROR",e.getMessage());
			}
	
		//If something goes wrong, an empty stylesheet is returned.	
		StringBuffer sb = new StringBuffer();		
		return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>"); 
    	}

    
//***************************************************************************************************************************************    

    public void transform(String version, String currentOperation , HttpServletRequest req,HttpServletResponse resp)
    	{	

		try 
			{
			// V�rifie et pr�pare l'application d'un fichier xslt utilisateur
		    String userXsltPath = getConfiguration().getXsltPath();
		    if(req.getUserPrincipal() != null)
		    	{			    				
		    	userXsltPath=userXsltPath+"/"+req.getUserPrincipal().getName()+"/";
		    	}
	
		    userXsltPath = userXsltPath+"/"+version+"/"+currentOperation +".xsl";
		    String globalXsltPath = getConfiguration().getXsltPath()+"/"+version+"/"+currentOperation +".xsl";;
	
		    File xsltFile = new File(userXsltPath);
		    boolean isPostTreat = false;	    
		    if(!xsltFile.exists())
		    	{	
		    	dump("Postreatment file "+xsltFile.toString()+"does not exist");
		    	xsltFile = new File(globalXsltPath);
		    	if (xsltFile.exists())
		    		{
		    		isPostTreat=true;		    
		    		}
		    	else
		    		{
		    		dump("Postreatment file "+xsltFile.toString()+"does not exist");
		    		}
		    	}
		    else
		    	{
		    	isPostTreat=true;
		    	}
	
		    // Transforms the results using a xslt before sending the response back    
		    //InputStream xml = new FileInputStream(filePathList.get(0));
		    Transformer transformer = null;
		    TransformerFactory tFactory = TransformerFactory.newInstance();
		    
		    File tempFile = null;
		    if (isXML(responseContentType))
		    	{			    
		    	tempFile = createTempFile(UUID.randomUUID().toString(), getExtension(responseContentType));			
		    	}
		    else
		    	{
				if (filePathList.size()>0)
					{
				    if(filePathList.get(0)!=null) tempFile = new File(filePathList.get(0));
					}
		    	}
		    
		    //********************************************************************************************************************
		    // Postraitement des r�ponses � la requ�te contenue dans les fichiers filePathList.get(i)
		    if (currentOperation  != null)
		    	{
		    	// Pour une requ�te utilisateur de type: Capabilities ************************************************************
		    	if ("GetCapabilities".equalsIgnoreCase(currentOperation) || "capabilities".equalsIgnoreCase(currentOperation)) 
		    		{
		    		dump("transform begin GetCapabilities operation");
		    		
				    //Contains the list of temporary modified Capabilities files. 		    
				    List<File> tempFileCapa = new Vector<File>();
				    
				    //Boucle sur les fichiers r�ponses
				    for (int iFilePath = 0;iFilePath<filePathList.size();iFilePath++)
				    	{
				    	tempFileCapa.add(createTempFile("transform_GetCapabilities_"+UUID.randomUUID().toString(), ".xml"));
				    	FileOutputStream tempFosCapa  = new FileOutputStream(tempFileCapa.get(iFilePath));
		
				    	InputStream xslt =  new ByteArrayInputStream(buildCapabilitiesXSLT(req,iFilePath).toString().getBytes());
		
				    	InputSource inputSource = new InputSource(new FileInputStream(filePathList.get(iFilePath)));
		
				    	XMLReader xmlReader = XMLReaderFactory.createXMLReader();
		
		
				    	String user =(String)getUsername(getRemoteServerUrl(iFilePath));
				    	String password = (String)getPassword(getRemoteServerUrl(iFilePath));
				    	if (user!=null && user.length()>0)
				    		{			
				    		ResourceResolver rr = new ResourceResolver(user,password);
				    		xmlReader.setEntityResolver(rr);
				    		}
				    	// END Added to hook in my EntityResolver		     
				    	SAXSource saxSource = new SAXSource(xmlReader,inputSource);
		
				    	//StreamSource ss = new StreamSource(xml);		    
		
						transformer = tFactory.newTransformer(new StreamSource(xslt));
		
						//Write the result in a temporary file
						dump("transform begin xslt transform to response file "+iFilePath);
						transformer.transform(saxSource, new StreamResult(tempFosCapa));
//Debug tb 06.07.2009						
						tempFosCapa.flush();
						tempFosCapa.close();
//Fin de Debug
						dump("transform end xslt transform to response file "+iFilePath);
				    	}
		
				    //Merge the results of all the capabilities and return it into a single file
				    dump("transform begin mergeCapabilities");
				    tempFile = mergeCapabilities(tempFileCapa);
				    dump("transform end mergeCapabilities");
				    
				    dump("transform end GetCapabilities operation");
		    		}
		    	else
		    		{
		    		// Pour une requ�te utilisateur de type: Map ************************************************************
		    		if (currentOperation.equals("GetMap") || "Map".equalsIgnoreCase(currentOperation)) 
		    			{
		    			dump("transform begin GetMap operation");
		    			
						boolean isTransparent= isAcceptingTransparency(responseContentType);
						//dump("DEBUG","LAYER N�:"+0+" "+layerFilePathList.get(0));
						
						dump("transform begin filterImage to layer "+0);
//Debug tb 08.07.2009
						BufferedImage imageSource;
						//Si les threads ont renvoy�s une r�ponse
						if(serverUrlPerfilePathList.size()>0)
							{
							imageSource = filterImage(getLayerFilter(serverUrlPerfilePathList.get(0),layerFilePathList.get(0)),filePathList.get(0),isTransparent);
//Fin de Debug
							Graphics2D g = imageSource.createGraphics();
							dump("transform end filterImage to layer "+0);
							
							 //Boucle sur les fichiers r�ponses
							for (int iFilePath = 1;iFilePath<filePathList.size();iFilePath++)
								{			
							    //dump("DEBUG","LAYER N�:"+iFilePath+" "+layerFilePathList.get(iFilePath));
							    if(layerFilePathList.get(iFilePath)!=null)
							    	{
							    	dump("transform begin filterImage to layer "+iFilePath);
//Debug tb 08.07.2009
							    	BufferedImage image = filterImage(getLayerFilter(serverUrlPerfilePathList.get(iFilePath),layerFilePathList.get(iFilePath)),filePathList.get(iFilePath),isTransparent);
//Fin de Debug
							    	if (image !=null) g.drawImage(image, null, 0, 0);
							    	dump("transform end filterImage to layer "+iFilePath);
							    	}
								}
//Debug tb 11.08.2009
							}
						//Si aucune requ�te n'a �t� envoy� au serveur, retourne: empty image
						else
							{
							imageSource = ImageIO.read(new File(filePathList.get(0)));
							}
//Fin de Debug
			
						Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(responseContentType);
			
						if (iter.hasNext()) 
							{
						    ImageWriter writer = (ImageWriter)iter.next();
			
						    tempFile = createTempFile("transform_GetMap_"+UUID.randomUUID().toString(), getExtension(responseContentType));
						    FileImageOutputStream output = new FileImageOutputStream(tempFile);			 
						    writer.setOutput(output);
						    writer.write(imageSource);
//Debug tb 06.07.2009						
						    output.flush();
						    output.close();
//Fin de Debug
							}
						
						dump("transform end GetMap operation");
					    }
		    		}
		    	}
	
		    //********************************************************************************************************************
		    // Traitement du r�sultat final avec le xslt utilisateur s'il exist (voir d�but de transform())
		    //if a xslt file exists then post-treat the response
		    if (isPostTreat && isXML(responseContentType))
		    	{
		    	dump("transform begin userTransform xslt");
		    	
		    	PrintWriter out = resp.getWriter();
		    	transformer = tFactory.newTransformer(new StreamSource(xsltFile));		    
		    	transformer.transform(new StreamSource(tempFile), new StreamResult(out));
		    	//delete the temporary file
		    	tempFile.delete();
		    	out.close();
		    	
		    	dump("transform end userTransform xslt");
		    	//the job is done. we can go out
		    	return;
		    	}
	
		    // Ou Ecriture du r�sultat final dans resp de httpServletResponse*****************************************************
		    //No post rule to apply. Copy the file result on the output stream
		    OutputStream os = resp.getOutputStream();
		    resp.setContentType(responseContentType);
		    InputStream is = new FileInputStream(tempFile);
		    int byteRead;
		    try {
		    	while((byteRead = is.read()) != -1) 
					{  
		    		os.write(byteRead);
					}
		    	} 
		    finally
		    	{		
		    	os.flush();
		    	os.close();		
		    	is.close();
		    	
		    	//Log le r�sultat et supprime les fichiers temporaires
		    	DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
		    	Date d = new Date();	
		    	dump("SYSTEM","ClientResponseDateTime",dateFormat.format(d));		
	
		    	if (tempFile !=null) 
		    		{
		    		dump("SYSTEM","ClientResponseLength",tempFile.length());
		    		tempFile.delete();	
		    		}
		    	}
		    
		    DateFormat dateFormat = new SimpleDateFormat(configuration.getLogDateFormat());
		    Date d = new Date();	
	
		    dump("SYSTEM","ClientResponseDateTime",dateFormat.format(d));
	
			} 
		catch (Exception e) 
			{
		    e.printStackTrace();
		    dump("ERROR",e.getMessage());
			}
	    }
    
    
//***************************************************************************************************************************************    
      
    /**
     * @return
     */
    private BufferedImage filterImage(String filter,String fileName,boolean isTransparent ) 
    	{
		try
			{
		    String []s = bbox.split(",");
	
	
		    if (filter !=null)
		    	{
				InputStream bis = new ByteArrayInputStream(filter.getBytes());
				System.setProperty("org.geotools.referencing.forceXY", "true");
		
				Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);			
				WKTReader wktReader= new WKTReader();
		
				Geometry polygon = wktReader.read(object.toString());
		
				filter.indexOf("srsName");
				String srs= filter.substring(filter.indexOf("srsName"));			
				srs = srs.substring(srs.indexOf("\"")+1);			
				srs = srs.substring(0,srs.indexOf("\""));						
				polygon.setSRID(Integer.parseInt(srs.substring(5)));
		
				CRSEnvelope bbox = new CRSEnvelope(srsName,Double.parseDouble(s[0]),Double.parseDouble(s[1]),Double.parseDouble(s[2]),Double.parseDouble(s[3]));			
		
				//final WorldFileReader reader = new WorldFileReader(new File(filePath));
		
				BufferedImage imageSource = ImageIO.read(new File(fileName));
		
				BufferedImage imageOut = imageFiltering(imageSource,bbox,polygon,isTransparent);
				return imageOut;
		    	}
		    else
		    	{
		    	if (fileName!=null) return (ImageIO.read(new File(fileName)));
		    	}	    
			}
		catch(Exception e)
			{
		    e.printStackTrace();
			}
		return null;
	    }

   
//***************************************************************************************************************************************    

    /**
     * @param tempFileCapa
     * @return
     */
    private File mergeCapabilities(List<File> tempFileCapa) 
    	{

    	if (tempFileCapa.size() == 0) return null;
    	
    	try
    		{
		    File fMaster = tempFileCapa.get(0);
		    DocumentBuilderFactory db = DocumentBuilderFactory.newInstance();
		    db.setNamespaceAware(false);
		    Document documentMaster = db.newDocumentBuilder().parse(fMaster);
		    DOMImplementationLS implLS = null;
		    if(documentMaster.getImplementation().hasFeature("LS", "3.0")) 
			    {
				implLS = (DOMImplementationLS)
				documentMaster.getImplementation();
			    }
		    else 
		    	{ 
				DOMImplementationRegistry enregistreur = DOMImplementationRegistry.newInstance();
				implLS = (DOMImplementationLS)
				enregistreur.getDOMImplementation("LS 3.0");
		    	}
		    if(implLS == null)
		    	{
				dump("Error", "DOM Load and Save not Supported. Multiple server is not allowed");
				return fMaster;
		    	}
	
		    for (int i=1;i<tempFileCapa.size();i++)
			    {
				Document documentChild = db.newDocumentBuilder().parse(tempFileCapa.get(i));
				NodeList nl = documentChild.getElementsByTagName("Layer");
				NodeList nlMaster = documentMaster.getElementsByTagName("Layer");
				Node ItemMaster = nlMaster.item(0);
				ItemMaster.insertBefore(documentMaster.importNode(nl.item(0).cloneNode(true), true), null);
			    }	        	        	        	        
	
		    File f = createTempFile(UUID.randomUUID().toString(),".xml");
		    //System.out.println(f.toURI());
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
    		}
    	catch(Exception e)
    		{
		    e.printStackTrace();
		    dump("ERROR",e.getMessage());
		    return null;
    		}
	    }

    
//***************************************************************************************************************************************

	protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp)
	    {
	
	    }
    
    
//***************************************************************************************************************************************

	protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp)
	    {
		try
			{
		    String operation = null;
		    String version = "000";
		    String service = "";
		    String width = "";
		    String height ="";
		    String format ="";
	
		    layers=null;
		    boolean sendRequest = true;
		    Enumeration<String> parameterNames = req.getParameterNames();
		    String paramUrlBase = "";	   	    
	
		    
		    //*********************************************************************
		    // To build the request to dispatch
		    // Lecture des param�tres de la requ�te utlisateur en mode GET
		    while (parameterNames.hasMoreElements()) 
		    	{
				String key = (String) parameterNames.nextElement();
				String value="";
				if (key.equalsIgnoreCase("LAYERS") ||key.equalsIgnoreCase("STYLES")||key.equalsIgnoreCase("BBOX")||key.equalsIgnoreCase("SRS") )
					{
				    value  = req.getParameter(key); 
					}
				else
					{
				    value = URLEncoder.encode(req.getParameter(key));
					}
	
				//String value = req.getParameter(key);
				if (!key.equalsIgnoreCase("LAYERS"))
				    if (!key.equalsIgnoreCase("STYLES")) paramUrlBase = paramUrlBase + key + "=" + value + "&";
	
				if (key.equalsIgnoreCase("Request")) 
					{
					// Gets the requested Operation
					if (value.equalsIgnoreCase("capabilities"))
						{
						operation = "GetCapabilities";
						}
					else
						{
						operation = value;
						}
	
					}
				else if (key.equalsIgnoreCase("version")) 
					{
					// Gets the requested Operation
					version = value;
					}
				else if (key.equalsIgnoreCase("wmtver")) 
					{
					// Gets the requested Operation
					version = value;
					service = "WMS";
					}
				else if (key.equalsIgnoreCase("service")) 
					{
				    // Gets the requested Operation
				    service = value;
					}
				else if (key.equalsIgnoreCase("BBOX")) 
					{
				    // Gets the requested Operation
				    bbox = value;
					}
				else if (key.equalsIgnoreCase("SRS")) 
					{
				    // Gets the requested Operation
				    srsName  = value;
					}
				else if (key.equalsIgnoreCase("LAYERS")) 
					{
					layers = value;
				    }
				else if (key.equalsIgnoreCase("STYLES")) 
					{
					styles = value;
				    }
				else  if (key.equalsIgnoreCase("WIDTH")) 
					{
					width= value;
				    }
				else  if (key.equalsIgnoreCase("HEIGHT")) 
					{
					height= value;
				    }
				else  if (key.equalsIgnoreCase("FORMAT")) 
					{
					format= value;
				    } 
		    	}
	
		    String user="";
		    if(req.getUserPrincipal() != null)
		    	{
		    	user= req.getUserPrincipal().getName();
		    	}
		    
		    
		    //*********************************************************************
		    
//Debug tb 09.07.2009		    	    
		    //D�finition de la classe des threads d'interrogation sur les serveurs	
			//**************************************************************************************
			//**************************************************************************************
			class SendServerThread extends Thread 
				{

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
			    
			    //**************************************************************************************
			    public SendServerThread(String pOperation,String pParamUrl,List pLayerToKeepList,int pIServer,List pStylesToKeepList,String pParamUrlBase,int pJ,String pWidth,String pHeight,String pFormat)
			    	{
					operation = pOperation;
					paramUrl = pParamUrl;
					layerToKeepList = pLayerToKeepList;
					iServer = pIServer;
					stylesToKeepList = pStylesToKeepList;
					paramUrlBase = pParamUrlBase;
					j = pJ;
					width = pWidth;
					height= pHeight;
					format = pFormat;
			    	}

			    //**************************************************************************************
			    public void run() 
			    	{
			    	
					//Pour cr�er les threads d'interrogation des couches du serveur j
				    // -> n�cessaire car filter peut �tre diff�rent d'une couche � l'autre du m�me serveur!!! 	
					//**************************************************************************************
					//**************************************************************************************
					class SendLayerThread extends Thread 
						{

					    List layerToKeepList;
					    int iLayers;
					    List stylesToKeepList;
					    String paramUrlBase;
					    String width;
					    String height;
					    String format;
					    int j;
					    
					    //**************************************************************************************
					    //public SendLayerThread(String pOperation,String pParamUrl,List pLayerToKeepList,int pILayers,List pStylesToKeepList,String pParamUrlBase,int pJ,String pWidth,String pHeight,String pFormat)
					    public SendLayerThread(int pILayers, List pLayerToKeepList,List pStylesToKeepList,String pParamUrlBase,int pJ,String pWidth,String pHeight,String pFormat)
					    	{
							layerToKeepList = pLayerToKeepList;
							iLayers = pILayers;
							stylesToKeepList = pStylesToKeepList;
							paramUrlBase = pParamUrlBase;
							j = pJ;
							width = pWidth;
							height= pHeight;
							format = pFormat;
					    	}

					    //**************************************************************************************
					    public void run() 
					    	{
				    		try
				    			{
				    			dump("DEBUG","Thread Layers group: "+layerToKeepList.get(0)+" work begin on server "+getRemoteServerUrl(j));
								
			    				String layersUrl = "LAYERS="+layerToKeepList.get(0);
			    				String stylesUrl = "&STYLES="+stylesToKeepList.get(0);
			    				for(int n=1;n<layerToKeepList.size();n++)
			    					{
			    					layersUrl = layersUrl+","+layerToKeepList.get(n);
			    					stylesUrl = stylesUrl+","+stylesToKeepList.get(n);
			    					}
			    				 
								String filePath  = sendData("GET", getRemoteServerUrl(j), paramUrlBase+layersUrl+stylesUrl);

								synchronized (serverFilePathList) 
									{
									synchronized (serverLayerFilePathList)
										{
										//Ecriture de la r�ponse du thread, dans le respect de l'ordre des layers de la requ�te utilisateur
										if(iLayers >= serverFilePathList.size())
											{
											serverFilePathList.add(filePath);
											serverLayerFilePathList.add((String)layerToKeepList.get(0));
											}
										else
											{
											serverFilePathList.add(iLayers,filePath);
											serverLayerFilePathList.add(iLayers,(String)layerToKeepList.get(0));
											}
										}
									}
								dump("DEBUG","Thread Layers group: "+layerToKeepList.get(0)+" work finished on server "+getRemoteServerUrl(j));
				    			}
				    		catch(Exception e)
				    			{
				    			dump("ERROR","Server "+getRemoteServerUrl(j)+" Layers group Thread "+layerToKeepList.get(0)+" :"+e.getMessage());
				    			e.printStackTrace();
				    			}   
				    		}
						}
					//**************************************************************************************
					//**************************************************************************************
					
		    		try
		    			{
		    			dump("DEBUG","Thread Server: "+getRemoteServerUrl(j)+" work begin");
		    			List<SendLayerThread> layerThreadList = new Vector<SendLayerThread>();
		    			
		    			if ("GetMap".equalsIgnoreCase(operation)||"map".equalsIgnoreCase(operation))
							{
							//Test si les filtres des layers sont diff�rents les uns des autres:
		    				//->envoi de 1 "thread layer" par groupe de couches ayant un "policy filter" identique
			    			//for(int iLayers=0;iLayers<layerToKeepList.size();iLayers++)
			    			while(layerToKeepList.size()>0)
			    				{
			    				List<String> layerToKeepListPerThread = new Vector<String>();
			    				List<String> stylesToKeepListPerThread = new Vector<String>();
			    				
							    String filter = getLayerFilter(getRemoteServerUrl(j),(String)layerToKeepList.get(0));
							    
			    				layerToKeepListPerThread.add((String)layerToKeepList.remove(0));
			    				stylesToKeepListPerThread.add((String)stylesToKeepList.remove(0));
			    				
			    				//Cr�ation du polygon A � partir du filtre de iLayer
			    				Boolean isNoFilterA = false;
							    Geometry polygonA = null;
							    
								// Par la m�me occasion, v�rification que la bbox de la requ�te utilisateur est dans le filter de layerToKeepList(0)
								// Sinon les layers ayant le m�me filtre ne sont pas conserv�es dans la requ�te.
							    System.setProperty("org.geotools.referencing.forceXY", "true");
								String []s = bbox.split(",");	
							    boolean iscoveredByfilter = true;
							    
							    if (filter!=null&&filter.length()>0)
							    	{
									InputStream bis = new ByteArrayInputStream(filter.getBytes());
									Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);			
									WKTReader wktReader= new WKTReader();
									
									polygonA = wktReader.read(object.toString());
									
									Geometry polygon = wktReader.read(object.toString());
									filter.indexOf("srsName");
									String srs= filter.substring(filter.indexOf("srsName"));			
									srs = srs.substring(srs.indexOf("\"")+1);			
									srs = srs.substring(0,srs.indexOf("\""));						
									polygon.setSRID(Integer.parseInt(srs.substring(5)));
									CoordinateReferenceSystem sourceCRS = CRS.decode("EPSG:"+(new Integer(polygon.getSRID())).toString());
									CoordinateReferenceSystem targetCRS = CRS.decode(srsName);
									double x1 = 	Double.parseDouble(s[0]);
									double y1 = Double.parseDouble(s[1]);
									double x2 =Double.parseDouble(s[2]); 
									double y2 = Double.parseDouble(s[3]);
									MathTransform a = CRS.findMathTransform(sourceCRS, targetCRS,false);
									polygon = JTS.transform(polygon, a);
									polygon.setSRID(Integer.parseInt(srs.substring(5)));
									Coordinate []c = {new Coordinate(x1,y1),new Coordinate(x1,y1),new Coordinate(x2,y1),new Coordinate(x2,y2),new Coordinate(x1,y2),new Coordinate(x1,y1)};
									GeometryFactory gf= new GeometryFactory (); 
									Geometry bboxGeom=gf.createPolygon(gf.createLinearRing(c), null);
									bboxGeom.setSRID(Integer.parseInt(srs.substring(5)));
									IntersectionMatrix mat1 = bboxGeom.relate(polygon);
									IntersectionMatrix mat2 = polygon.relate(bboxGeom);
			
									if(mat1.isIntersects()||mat2.isIntersects() ||bboxGeom.overlaps(polygon)||polygon.overlaps(bboxGeom)||polygon.coveredBy(bboxGeom)||bboxGeom.coveredBy(polygon) || bboxGeom.touches(polygon) ||polygon.touches(bboxGeom) || bboxGeom.intersects((polygon))||bboxGeom.covers((polygon))||bboxGeom.crosses((polygon))||polygon.crosses(bboxGeom)||polygon.intersects((bboxGeom))||polygon.covers((bboxGeom)))
										{
									    iscoveredByfilter=true;
										}
									else 
										{
									    iscoveredByfilter=false;
										}
							    	}
							    else
							    	{
							    	isNoFilterA = true;
							    	}
					    		for (int k=0;k<layerToKeepList.size();k++)
									{
					    			//Cr�ation du polygon B � partir du filtre de iLayer
					    			Boolean isNoFilterB = false;
								    filter = getLayerFilter(getRemoteServerUrl(j),(String)layerToKeepList.get(k));
								    Geometry polygonB = null;
								    if (filter!=null&&filter.length()>0)
								    	{
										InputStream bis = new ByteArrayInputStream(filter.getBytes());
										Object object = DocumentFactory.getInstance(bis, null, Level.WARNING);			
										WKTReader wktReader= new WKTReader();
										polygonB = wktReader.read(object.toString());
								    	}
								    else
								    	{
								    	isNoFilterB = true;
								    	}
								    
								    //Comparaison des filtres
								    if(!isNoFilterA && !isNoFilterB)
								    	{
									    if(polygonA.equalsExact(polygonB))
									    	{
						    				layerToKeepListPerThread.add((String)layerToKeepList.remove(k));
						    				stylesToKeepListPerThread.add((String)stylesToKeepList.remove(k));
						    				k--;
									    	}
								    	}
								    else if(isNoFilterA && isNoFilterB)
								    	{
					    				layerToKeepListPerThread.add((String)layerToKeepList.remove(k));
					    				stylesToKeepListPerThread.add((String)stylesToKeepList.remove(k));
					    				k--;
								    	}
									}
					    		
					    		if(iscoveredByfilter)
					    			{
									//Cr�ation et lancement des threads sur serveur j pour chaque groupe de couches (� filtres identiques)
									dump("requestPreTraitementGET send request multiLayer to thread server "+getRemoteServerUrl(j));
									SendLayerThread th = new SendLayerThread(layerThreadList.size(),layerToKeepListPerThread,stylesToKeepListPerThread,paramUrlBase,j,width,height,format);
								    th.start();		
								    layerThreadList.add(th);
					    			}
								else
									{
									dump("ERROR","Thread Layers group: "+layerToKeepListPerThread.get(0)+" work finished on server "+getRemoteServerUrl(j)+" : bbox not covered by policy filter.");
									}
			    				}
							//R�cup�ration du r�sultat des threads sur serveur j
							// Autant de filePath � ajouter que de couches
							for (int i = 0;i<layerThreadList.size();i++)
								{
								layerThreadList.get(i).join();

								// Si une r�ponse a bien �t� renvoy�e par le thread i
								if(!((String)serverFilePathList.get(i)).equals(""))
									{
									synchronized (filePathList) 
										{
										synchronized (layerFilePathList)
											{
											synchronized (serverUrlPerfilePathList)
												{
											    // Insert les r�ponses
											    filePathList.add(serverFilePathList.get(i));	
											    layerFilePathList.add(serverLayerFilePathList.get(i));
												serverUrlPerfilePathList.add(getRemoteServerUrl(j));
												}
											}
										}
									}
								}
							}
		    			else if("GetCapabilities".equalsIgnoreCase(operation) || "capabilities".equalsIgnoreCase(operation))
							{
		    				String filePath  = sendData("GET", getRemoteServerUrl(j), paramUrlBase);
		    				
							synchronized (filePathList) 
								{
								synchronized (layerFilePathList)
									{
									synchronized (serverUrlPerfilePathList)
										{
										// Insert les r�ponses
										dump("requestPreTraitementGET save response capabilities from thread server "+getRemoteServerUrl(j));
									    layerFilePathList.add("");
										serverUrlPerfilePathList.add(getRemoteServerUrl(j));
									    filePathList.add(filePath);
										}
									}
								}
							}
						dump("DEBUG","Thread Server: "+getRemoteServerUrl(j)+" work finished");
		    			}
		    		catch(Exception e)
		    			{
		    			dump("ERROR","Server Thread "+getRemoteServerUrl(j)+" :"+e.getMessage());
		    			e.printStackTrace();
		    			}
			    	}
				}
			//**************************************************************************************
			//**************************************************************************************
//Fin de Debug
		    
		    //Boucle sur les serveur d�finis dans config.xml *************************
		    List<RemoteServerInfo> grsiList = getRemoteServerInfoList();
		    List<SendServerThread> serverThreadList = new Vector<SendServerThread>();
	
		    for (int j=0;j<grsiList.size();j++)
		    	{
				String paramUrl = "";	
				List<String> layerToKeepList = new Vector<String>();
				List<String> stylesToKeepList = new Vector<String>();
				
				if(hasPolicy)
					{
				    // V�rfication de la taille image req VS policy -> si vrai: la requ�te n'est pas envoy�e 
				    if (("GetMap".equalsIgnoreCase(operation)||"map".equalsIgnoreCase(operation) )&& !isSizeInTheRightRange(Integer.parseInt(width),Integer.parseInt(height)))
				    	{
				    	dump("requestPreTraitementGET says: request ImageSize out of bounds, see the policy definition.");
				    	sendRequest=false;
				    	}		    
	
				    // V�rification de la pr�sence du pram�tre "LAYER" dans la requ�te -> si vrai: recherche des layers autoris�es et styles correspondant
					// Permet la r��criture des param�tres "LAYER" et "STYLES" de la requ�te
				    if (sendRequest && layers!=null && layers.length()>0)
				    	{
						String[] layerArray = layers.split(",");
//Debug tb 09.07.2009
						String[] layerStyleArray;
						if (styles!=null)
							{
							layerStyleArray = styles.split(",");
							}
						else
							{
							styles = "";
							layerStyleArray = styles.split(",");
							}
						
						//Le param�tre style est obligatoire par couche, mais on l'�mule s'il n'est pas pr�sent
						if (layerStyleArray.length < layerArray.length)
							{
							int diffSize = layerArray.length-layerStyleArray.length;
						    for (int i=0;i<diffSize;i++)
						    	{
						    	styles=styles+",";
						    	}
							}						
//Fin de Debug	
						layerStyleArray = styles.split(",");			


						// V�rification des couches autoris�es *********************************
						for (int i = 0; i<layerArray.length;i++)
							{
//Debug tb 03.07.2009
				    		String tmpFT = layerArray[i];
				    		if (tmpFT!=null)
				    			{
				    			String [] s = tmpFT.split(":");
				    			tmpFT = s[s.length-1];
				    			layerArray[i] = tmpFT;
//Fin de Debug
				    			// V�rification que la couche de la req est autoris�e par Policy
						    	boolean isLayerTypePermited = isLayerAllowed(layerArray[i], getRemoteServerUrl(j));
						    	String []c = bbox.split(",");
			
						    	ReferencedEnvelope re = new ReferencedEnvelope(Double.parseDouble(c[0]),Double.parseDouble(c[2]),Double.parseDouble(c[1]),Double.parseDouble(c[3]),CRS.decode(srsName));
			
						    	// V�rification que l'�chelle de la requ�te est autoris�e
								if (isLayerTypePermited)
									{
								    if (isLayerInScale(layerArray[i], getRemoteServerUrl(j), RendererUtilities.calculateOGCScale(re, Integer.parseInt(width), null)))
								    	{
								    	isLayerTypePermited = true;
								    	}
								    else
								    	{
								    	isLayerTypePermited = false;
								    	}
									}
				
								//Ajout de la couche et de son sytle associ�, si cette derni�re est autoris�e par Policy
								if (isLayerTypePermited) 
									{
								    if (layerStyleArray.length>i)
								    	{
								    	stylesToKeepList.add(layerStyleArray[i]);					
								    	}
								    else
								    	{
								    	stylesToKeepList.add("");
								    	}
								    layerToKeepList.add(layerArray[i]);
									}
							    }
							}
							
						// V�rfication de l'absence de "LAYER" autoris�es restantes -> si vrai: la requ�te n'est pas envoy�e
						if (layerToKeepList.size() <= 0)
							{
						    sendRequest=false;
							}
//Fin de Debug
					    }
					}
				
				//Si pas de fichier Policy d�fini, envoi direct de la requ�te sur le serveur j
//				else
//					{
//				    //@TODO:Manage multiple servers when no policy is existing.  
//				    if (layers!=null && layers.length()>0)
//				    	{
//				    	paramUrl="LAYERS="+layers+"&STYLES="+styles;
//				    	}
//					}
				
				// Si requ�te � envoyer sur serveur j
				if (sendRequest)
					{	
//Debug tb 08.07.2009
//Nouvelle version des threads -> par serveur -> par couche
					//Cr�ation et lancement du thread sur serveur j
					//Copie des strings pour utlisation dans threads. Originales r��ctrites dans boucle serveur courante!
					String cpOperation = new String(operation);
					String cpParamUrl = new String(paramUrl);
					String cpParamUrlBase = new String(paramUrlBase);
					String cpWidth = new String(width);
					String cpHeight = new String(height);
					String cpFormat = new String(format);
					SendServerThread s = new SendServerThread(cpOperation,cpParamUrl,layerToKeepList,serverThreadList.size(),stylesToKeepList,cpParamUrlBase,j,cpWidth,cpHeight,cpFormat);
				    s.start();		
				    serverThreadList.add(s);
					}
		    			
				//Si pas de requ�te � envoyer sur serveur j: sendRequest=false
				else
					{
					sendRequest=true;
					if ("GetMap".equalsIgnoreCase(operation) || "map".equalsIgnoreCase(operation))
						{
						dump("requestPreTraitementGET save response server "+getRemoteServerUrl(j)+": emptyImage");
						generateEmptyImage(width,height,format,true);
						}
					}	
		    	}
		    //Sortie de la boucle des serveurs*************************************************
		    
			//Attente de l'arriv�e des r�sultats des threads sur chaque serveur avant de passer � la suite du traitement
			for (int i=0;i<serverThreadList.size();i++)
				{
				serverThreadList.get(i).join();
				
			    // les r�ponses ont �t� ins�r�es, par les threads servers, dans filePathList;
				// layerFilePathList-> layer names et serverUrlPerFilePathList-> url server, ont aussi �t� mis � jour en cons�quence			    					   
				}
			
			// Si aucun des layerThread n'a pass� de requ�te, car policy filter et req bbox incompatibles
			if(filePathList.size()<=0)
				{
				sendRequest=true;
				dump("requestPreTraitementGET save response servers: emptyImage");
				generateEmptyImage(width,height,format,true);
				}
			//Fin de la phase de reconstruction de la requ�te: filePathList contient les r�ponses de chaque serveur (une par serveur)     
			//*****************************************************************************************************************************
			
			
			//*****************************************************************************************************************************
			// Lancement du post traitement
		    version = version.replaceAll("\\.", "");
	
		    dump("requestPreTraitementGET begin transform");
		    transform(version,operation,req, resp);
		    dump("requestPreTraitementGET end transform");
			//*****************************************************************************************************************************
			// Fin du post traitement
			}
		catch(Exception e)
			{
		    e.printStackTrace();
		    dump("ERROR",e.getMessage());
			}
	    }


//***************************************************************************************************************************************    

	private void generateEmptyImage(String width,String height,String format,boolean isTransparent) 
    	{
		//In the case of a GetMap, it should returns an empty image
		try
			{
		    BufferedImage imgOut = null;
		    if (isTransparent)
		    	{
		    	imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.BITMASK);		    
		    	}
		    else
		    	{
		    	imgOut = new BufferedImage((int) Double.parseDouble(width), (int) Double.parseDouble(height), BufferedImage.TYPE_INT_ARGB);
		    	}
		    responseContentType=URLDecoder.decode(format);
		    Iterator<ImageWriter> iter = ImageIO.getImageWritersByMIMEType(responseContentType);
	
		    if (iter.hasNext()) 
		    	{
				ImageWriter writer = (ImageWriter)iter.next();
				File tempFile = createTempFile(UUID.randomUUID().toString(), getExtension(responseContentType));
				FileImageOutputStream output = new FileImageOutputStream(tempFile);			 
				writer.setOutput(output);
				writer.write(imgOut);
				String filePath =tempFile.getPath();
				filePathList.add(filePath);
				layerFilePathList.add("");
		    	}
			}
		catch(Exception e)
			{
		    e.printStackTrace();
			}
	    }

	
//***************************************************************************************************************************************    

    /*
     * envelope contains the envelope of the whole image
     */
	private BufferedImage imageFiltering(BufferedImage imageSource,CRSEnvelope envelope,Geometry polygonFilter,boolean isTransparent)
    	{	
		try
			{	
		    System.setProperty("org.geotools.referencing.forceXY", "true");
		    //System.setProperty( (Hints.FORCE_STANDARD_AXIS_DIRECTIONS.toString()), "true" );
	
	
		    //Transform the srs of the filter if needed. 
		    String srsName = envelope.getEPSGCode();	    
		    CoordinateReferenceSystem crs = CRS.decode(srsName);
	
		    CoordinateReferenceSystem sourceCRS = CRS.decode("EPSG:"+(new Integer(polygonFilter.getSRID())).toString());
		    CoordinateReferenceSystem targetCRS = CRS.decode(envelope.getEPSGCode());	    
	
		    MathTransform a = CRS.findMathTransform(sourceCRS, targetCRS,false);
	
		    polygonFilter = JTS.transform(polygonFilter, a);
	
	
		    try
		    	{
		    	for(int i=0;i<crs.getIdentifiers().size();i++)
		    		{
		    		if (((NamedIdentifier)crs.getIdentifiers().toArray()[i]).getCodeSpace().equals("EPSG"))
		    			{
		    			polygonFilter.setSRID(Integer.parseInt(((NamedIdentifier)crs.getIdentifiers().toArray()[i]).getCode()));
		    			break;
		    			}
		    		}
		    	}
		    catch(Exception e)
		    	{
		    	e.printStackTrace();
		    	}
	
		    final GeometryAttributeType geom = new GeometricAttributeType("Geom", Geometry.class, false, null, crs, null);
		    final AttributeType attr1 = AttributeTypeFactory.newAttributeType("COLOR", String.class);
		    final AttributeType[] attributes = new AttributeType[] {attr1,geom};
	
		    final FeatureType schema = FeatureTypes.newFeatureType(attributes,"TEMPORARYFEATURE", new URI("depth.ch"), false, null, geom);
	
		    //Construction du masque sur la base de l'enveloppe de la bbox et du polygon de filtre
		    FeatureRasterizer fr = new FeatureRasterizer(imageSource.getHeight(),imageSource.getWidth());	        
		    double width = envelope.getMaxX()-envelope.getMinX();
		    double height = envelope.getMaxY()-envelope.getMinY();;
		    Rectangle2D.Double bounds = new Rectangle2D.Double(envelope.getMinX(),envelope.getMinY(),width,height);
	
		    fr.setBounds(bounds);
		    fr.setAttName("COLOR");
	
		    fr.addFeature(schema.create(new Object[] {Integer.toString(Color.WHITE.getRGB()),polygonFilter}));
	
		    //Construction de l'image de masquage
		    BufferedImage bimage2 = fr.getBimage();
		    int imageType = BufferedImage.TYPE_INT_RGB;
		    if (isTransparent)
		    	{
		    	imageType = BufferedImage.TYPE_INT_ARGB;
		    	}
	
		    // "dimg" contient l'image source et "bimage2" est utilis� comme masque.
		    BufferedImage dimg = new BufferedImage(imageSource.getWidth(), imageSource.getHeight(), imageType);
		    Graphics2D g = dimg.createGraphics();
		    g.setComposite(AlphaComposite.Src);
		    g.drawImage(imageSource, null, 0, 0);
		    g.dispose();
		    for(int i = 0; i < bimage2.getHeight(); i++) 
		    	{
		    	for(int j = 0; j < bimage2.getWidth(); j++) 
		    		{		
		    		if(bimage2.getRGB(j, i) == 0) 
		    			{
		    			//dimg.setRGB(j, i, 0x8F1C1C);				
		    			dimg.setRGB(j, i, 0xFFFFFF);
		    			}
		    		}
		    	}
	
		    // Une fois le masque appliqu� sur l'image source, renvoy� l'image filtr�e
		    return dimg;
			}
		catch(Exception e)
			{
		    e.printStackTrace();
		    dump("ERROR",e.getMessage());
			}		
	
		return imageSource;
	    }
    
//***************************************************************************************************************************************    

	}
