/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�Arche 40b, CH-1870 Monthey,
 * easysdi@depth.ch
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version. This
 * program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see http://www.gnu.org/licenses/gpl.html.
 */
package org.easysdi.proxy.csw;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.Enumeration;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.UUID;
import java.util.Vector;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamResult;
import javax.xml.transform.stream.StreamSource;

import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.methods.GetMethod;
import org.apache.commons.httpclient.methods.PostMethod;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.proxy.core.ProxyServletRequest;
import org.easysdi.proxy.domain.SdiExcludedattribute;
import org.easysdi.proxy.domain.SdiPhysicalservice;
import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiVirtualmetadata;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.exception.AvailabilityPeriodException;
import org.easysdi.proxy.jdom.filter.ElementFilter;
import org.easysdi.proxy.jdom.filter.ElementMD_MetadataNonAuthorizedFilter;
import org.easysdi.proxy.jdom.filter.ElementTransactionTypeFilter;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.ows.v200.OWS200ExceptionReport;
import org.easysdi.proxy.xml.handler.CswRequestHandler;
import org.jdom.Attribute;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.Namespace;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.springframework.security.core.context.SecurityContextHolder;
import org.xml.sax.InputSource;
import org.xml.sax.SAXParseException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;

public class CSWProxyServlet extends ProxyServlet {

    private static final long serialVersionUID = -4603521823139762369L;
    public Namespace nsSDI = Namespace.getNamespace("sdi", "http://www.easysdi.org/2011/sdi");
    private Boolean asConstraint = false;
    private CSWProxyDataAccessibilityManager cswDataManager;
    private String[] CSWOperation = {"GetCapabilities", "GetRecords", "GetRecordById", "Harvest", "DescribeRecord", "GetExtrinsicContent", "Transaction", "GetDomain"};
    private static final List<String> ServiceSupportedOperations = Arrays.asList("GetCapabilities", "GetRecords", "GetRecordById", "DescribeRecord", "Transaction");

    public CSWProxyServlet(ProxyServletRequest proxyRequest, SdiVirtualservice virtualService, SdiPolicy policy) {
        super(proxyRequest, virtualService, policy);
        owsExceptionReport = new OWS200ExceptionReport();
        cswDataManager = new CSWProxyDataAccessibilityManager(policy);
    }

    public void init(ServletConfig config) throws ServletException {
        super.init(config);
    }

    public void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        super.doPost(req, resp);
    }

    public void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        super.doGet(req, resp);

    }

    protected StringBuffer generateOgcException(String errorMessage, String code, String locator, String version) {
        logger.error(errorMessage);
        StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8'?>\n");
        sb.append("<ows:ExceptionReport xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" version=\"1.0.0\" xsi:schemaLocation=\"http://www.opengis.net/ows http://schemas.opengis.net/ows/1.0.0/owsExceptionReport.xsd\">\n");
        sb.append("\t<ows:Exception ");
        if (code != null && code != "") {
            sb.append(" exceptionCode=\"");
            sb.append(code);
            sb.append("\"");
        }
        if (locator != null && locator != "") {
            sb.append(" locator=\"");
            sb.append(locator);
            sb.append("\"");
        }
        sb.append(">\n");
        sb.append("\t\t<ows:ExceptionText>");
        sb.append(errorMessage);
        sb.append("</ows:ExceptionText>\n");
        sb.append("\t</ows:Exception>\n");
        sb.append("</ows:ExceptionReport>");
        return sb;
    }

    protected StringBuffer buildCapabilitiesXSLT(HttpServletRequest req) {

        try {
            String url = getServletUrl(req);
            List<String> permitedOperations = new Vector<String>();
            List<String> deniedOperations = new Vector<String>();

            // Fill the vectors with the corresponding information
            for (int i = 0; i < CSWOperation.length; i++) {

                if (ServiceSupportedOperations.contains(CSWOperation[i]) && isOperationAllowed(CSWOperation[i])) {
                    permitedOperations.add(CSWOperation[i]);
                    logger.trace(CSWOperation[i] + " is permitted");
                } else {
                    deniedOperations.add(CSWOperation[i]);
                    logger.trace(CSWOperation[i] + " is denied");
                }
            }
//			Set<SdiSysOperationcompliance> operationCompliances = getProxyRequest().getServiceCompliance().getSdiSysOperationcompliances();
//		   Iterator<SdiSysOperationcompliance> i = operationCompliances.iterator();
//		   while (i.hasNext())
//		   {
//			   SdiSysOperationcompliance compliance = i.next();
//			   if(compliance.getSdiSysServiceoperation().getState() == 1 && compliance.getState() == 1 && compliance.isImplemented() && isOperationAllowed(compliance.getSdiSysServiceoperation().getValue()))
//			   {
//				   permitedOperations.add(compliance.getSdiSysServiceoperation().getValue());
//				   logger.trace(compliance.getSdiSysServiceoperation().getValue() + " is permitted");
//			   }
//			   else
//			   {
//				   deniedOperations.add(compliance.getSdiSysServiceoperation().getValue());
//				   logger.trace(compliance.getSdiSysServiceoperation().getValue() + " is denied");
//				   
//			   }
//		   }

            return generateXSLTForCSWCapabilities200(url, deniedOperations, permitedOperations);
        } catch (Exception e) {
            e.printStackTrace();
            logger.error(e.getMessage());
        }

        // If something goes wrong, an empty stylesheet is returned.
        StringBuffer sb = new StringBuffer();
        return sb.append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
    }

    protected StringBuffer generateXSLTForMetadata() {
        try {
            StringBuffer CSWCapabilities200 = new StringBuffer();

            CSWCapabilities200.append("<xsl:stylesheet version=\"1.00\" "
                    + "xmlns:dc=\"http://purl.org/dc/elements/1.1/\" "
                    + "xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" "
                    + "xmlns:gco=\"http://www.isotc211.org/2005/gco\" "
                    + "xmlns:ns3=\"http://www.isotc211.org/2005/gmx\" "
                    + "xmlns:xlink=\"http://www.w3.org/1999/xlink\" "
                    + "xmlns:gml=\"http://www.opengis.net/gml\" "
                    + "xmlns:gts=\"http://www.isotc211.org/2005/gts\"   "
                    + "xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" "
                    + "xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" "
                    + "xmlns:ows=\"http://www.opengis.net/ows\"> ");


            //Remove the not allowed attributes
            List<String> notAllowedAttributeList = getAttributesNotAllowedInMetadata(getPhysicalServiceURLByIndex(0));
            if (notAllowedAttributeList.size() != 0) {
                int nsI = 0;
                for (int i = 0; i < notAllowedAttributeList.size(); i++) {
                    String text = notAllowedAttributeList.get(i);
                    CSWCapabilities200.append("<xsl:template match=\"//csw:SearchResults/" + text + "\">");
                    CSWCapabilities200.append("</xsl:template>");

                    if (text != null) {
                        if (text.indexOf("\":") < 0) {
                            // Pas de namespace.
                            CSWCapabilities200.append("<xsl:template match=\"//gmd:MD_Metadata/" + text + "\">");
                        } else {
                            CSWCapabilities200.append("<xsl:template xmlns:" + "au" + nsI + "=\"" + text.substring(1, text.indexOf("\":"))
                                    + "\" match=\"//gmd:MD_Metadata/au" + nsI + text.substring(text.indexOf("\":") + 1) + "\">");
                            nsI++;
                        }
                        CSWCapabilities200.append("</xsl:template>");
                    }
                }

            }

            //Copy all other nodes
            CSWCapabilities200.append("  <!-- Whenever you match any node or any attribute -->");
            CSWCapabilities200.append("<xsl:template match=\"node()|@*\">");
            CSWCapabilities200.append("<!-- Copy the current node -->");
            CSWCapabilities200.append("<xsl:copy>");
            CSWCapabilities200.append("<!-- Including any attributes it has and any child nodes -->");
            CSWCapabilities200.append("<xsl:apply-templates select=\"@*|node()\"/>");
            CSWCapabilities200.append("</xsl:copy>");
            CSWCapabilities200.append("</xsl:template>");

            CSWCapabilities200.append("</xsl:stylesheet>");
            return CSWCapabilities200;
        } catch (Exception e) {
            e.printStackTrace();
            logger.error(e.getMessage());
            // If something goes wrong, an empty stylesheet is returned.
            StringBuffer sb = new StringBuffer();
            return sb.append("<xsl:stylesheet version=\"1.00\" "
                    + "xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" "
                    + "xmlns:ows=\"http://www.opengis.net/ows\" "
                    + "xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
        }
    }

    /**
     * Apply an XSLT transformation on the XML response to remove unauthorized
     * attributes (according to policy file)
     *
     * @param xml
     * @return transformed File
     * @throws TransformerException
     * @throws IOException
     */
    protected File applyXSLTToRemoveAttribute(InputStream xml) throws TransformerException, IOException {

        logger.trace("Start apply XSLT on response.");
        File tempFile = createTempFile(UUID.randomUUID().toString(), ".xml");
        FileOutputStream tempFos = new FileOutputStream(tempFile);

        InputStream xslt = null;
        xslt = new ByteArrayInputStream(generateXSLTForMetadata().toString().getBytes());
        TransformerFactory tFactory = TransformerFactory.newInstance();
        Transformer transformer = tFactory.newTransformer(new StreamSource(xslt));
        // Write the result in a temporary file
        transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
        tempFos.close();
        xslt.close();
        logger.trace("- End apply XSLT on response.");
        return tempFile;
    }

    /**
     *
     */
    @SuppressWarnings("unchecked")
    public void transform(String version, String currentOperation, HttpServletRequest req, HttpServletResponse resp, List<String> filePathList) {
        try {
            String userXsltPath = sdiVirtualService.getXsltfilename();
            if (SecurityContextHolder.getContext().getAuthentication() != null) {
                userXsltPath = userXsltPath + "/" + SecurityContextHolder.getContext().getAuthentication().getName() + "/";
            }

            userXsltPath = userXsltPath + "/" + version + "/" + currentOperation + ".xsl";
            String globalXsltPath = userXsltPath + "/" + version + "/" + currentOperation + ".xsl";

            File xsltFile = new File(userXsltPath);
            boolean isPostTreat = false;
            if (!xsltFile.exists()) {
                logger.trace("Postreatment file " + xsltFile.toString() + "does not exist");
                xsltFile = new File(globalXsltPath);
                if (xsltFile.exists()) {
                    isPostTreat = true;
                } else {
                    logger.trace("Postreatment file " + xsltFile.toString() + "does not exist");
                }
            } else {
                isPostTreat = true;
            }

            // Transforms the results using a xslt before sending the response back
            InputStream xml = new FileInputStream(filePathList.get(0));
            TransformerFactory tFactory = TransformerFactory.newInstance();

            File tempFile = null;
            FileOutputStream tempFos = null;

            Transformer transformer = null;

            if (currentOperation != null) {
                if (currentOperation.equals("GetCapabilities")) {
                    //Remove unauthorized operation (according to policy file) from the capabilities document
                    logger.trace("Remove unauthorized operations from the capabilities document.");
                    tempFile = createTempFile(UUID.randomUUID().toString(), ".xml");
                    tempFos = new FileOutputStream(tempFile);
                    ByteArrayInputStream xslt = null;
                    xslt = new ByteArrayInputStream(buildCapabilitiesXSLT(req).toString().getBytes());
                    transformer = tFactory.newTransformer(new StreamSource(xslt));
                    transformer.transform(new StreamSource(xml), new StreamResult(tempFos));
                    tempFos.flush();
                    tempFos.close();
                    xslt.close();

                    //Rewrite service metadata 
                    logger.trace("Rewrite service metadatas in the capabilities document.");
                    InputStream in = new BufferedInputStream(new FileInputStream(tempFile));
                    InputSource inputSource = new InputSource(in);
                    File tempFileCapaWithMetadata = createTempFile("transform_MDGetCapabilities_" + UUID.randomUUID().toString(), ".xml");
                    FileOutputStream tempServiceMD = new FileOutputStream(tempFileCapaWithMetadata);
                    StringBuffer sb = buildServiceMetadataCapabilitiesXSLT();
                    InputStream xslt_service = new ByteArrayInputStream(sb.toString().getBytes());
                    XMLReader xmlReader = XMLReaderFactory.createXMLReader();
                    SAXSource saxSource = new SAXSource(xmlReader, inputSource);
                    transformer = tFactory.newTransformer(new StreamSource(xslt_service));
                    transformer.setOutputProperty(OutputKeys.INDENT, "yes");
                    transformer.setOutputProperty("{http://xml.apache.org/xslt}indent-amount", "2");
                    transformer.transform(saxSource, new StreamResult(tempServiceMD));
                    tempServiceMD.flush();
                    tempServiceMD.close();
                    in.close();
                    xslt_service.close();

                    tempFile = tempFileCapaWithMetadata;
                    logger.trace("transform end apply XSLT on service metadata");
                } else if ("DescribeRecord".equals(currentOperation)) {
                    if (sdiPolicy.isCsw_anyattribute()) {
                        // Keep the metadata as it is
                        tempFile = new File(filePathList.get(0));
                    } else {
                        tempFile = applyXSLTToRemoveAttribute(xml);
                    }
                } else if ("GetRecords".equals(currentOperation)) {
                    if (sdiPolicy.isCsw_anyattribute()) {
                        // Keep the metadata as it is
                        tempFile = new File(filePathList.get(0));
                    } else {
                        tempFile = applyXSLTToRemoveAttribute(xml);
                    }

                    //If the current config is used to harvest remote catalog (see config file : <harvesting-config>true</harvesting-config>),
                    //add dynamically an XML node (and its namespace definition) to the metadata to indicate that this metadata was haversting
                    if (sdiVirtualService.isHarvester()) {
                        SAXBuilder sb = new SAXBuilder();

                        Document doc = null;
                        try {
                            doc = sb.build(tempFile);
                            Element racine = doc.getRootElement();

                            //Get the metadata element from the complete response file
                            List<Element> resultListStorage = new ArrayList<Element>();
                            Iterator<Element> resultIterator = racine.getDescendants(new ElementFilter("SearchResults"));
                            while (resultIterator.hasNext()) {
                                Element result = resultIterator.next();
                                resultListStorage.addAll(result.getChildren());
                            }

                            //Add a new node to the metadata element
                            //<gmd:MD_Metadata xmlns:sdi="http://www.easysdi.org/2011/sdi">
                            //<sdi:platform harvested="true" />
                            //</gmd:MD_Metadata>
                            //Or update the existing node if the remote catalog is driven by EasySDI too
                            Iterator<Element> resultStorageIterator = resultListStorage.iterator();
                            Element result = null;

                            while (resultStorageIterator.hasNext()) {
                                result = resultStorageIterator.next();
                                List<Element> platformElementList = result.getChildren("platform", nsSDI);
                                if (platformElementList.size() > 0) {
                                    //Update the existing node
                                    Element e = platformElementList.get(0);
                                    e.setAttribute("harvested", "true");
                                } else {
                                    //Add a new node
                                    Element e = new Element("platform", nsSDI);
                                    e.setAttribute("harvested", "true");
                                    result.addContent(e);
                                }
                            }
                            if (result != null) {
                                result.getParentElement().addNamespaceDeclaration(nsSDI);
                            }

                            XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
                            FileOutputStream outStream = new FileOutputStream(tempFile);
                            sortie.output(doc, outStream);
                            outStream.close();

                        } catch (JDOMException e) {
                            e.printStackTrace();
                        } catch (IOException e) {
                            e.printStackTrace();
                        }
                    } else {
                        //Current config is not used to harvest remote catalog.
                        //If the request was made in HTTP GET, response has to be rewrote to remove unauthorized metadatas
                        //NB : a geographic filter can't be applied to this kind of request
                        if (req.getMethod().equals("GET")) {

//                            SAXBuilder sb = new SAXBuilder();
//                            Document doc = null;
//                            try {
//                                List<Element> lElementUnauthorized = new ArrayList<Element>();
//                                doc = sb.build(tempFile);
//                                Element racine = doc.getRootElement();
//                                Iterator<Element> searchResultIterator = racine.getDescendants(new ElementFilter("SearchResults"));
//                                Attribute numberOfRecordsReturnedAttribute = null;
//                                Attribute numberOfRecordsMatchedAttribute = null;
//                                Attribute nextRecordAttribute = null;
//                                while (searchResultIterator.hasNext()) {
//                                    Element e = searchResultIterator.next();
//                                    numberOfRecordsReturnedAttribute = e.getAttribute("numberOfRecordsReturned");
//                                    numberOfRecordsMatchedAttribute = e.getAttribute("numberOfRecordsMatched");
//                                    nextRecordAttribute = e.getAttribute("nextRecord");
//                                }
//
//                                Boolean isAll = cswDataManager.isAllEasySDIDataAccessible();
//                                List<String> authorizedGuidList = new ArrayList<String>();
//                                Integer numberOfRecordsActuallyMatched = 0;
//                                Integer numberOfEasySDIMetadatas = cswDataManager.getCountOfEasySDIMetadatas();
//
//                                if (isAll) {
//                                    //All metadatas are authorized to be delivered
//                                    authorizedGuidList = null;
//                                    if (sdiPolicy.isCsw_includeharvested()) {
//                                        if (numberOfRecordsMatchedAttribute == null) {
//                                            numberOfRecordsActuallyMatched = 0;
//                                        } else {
//                                            numberOfRecordsActuallyMatched = numberOfRecordsMatchedAttribute.getIntValue();
//                                        }
//                                    } else {
//                                        numberOfRecordsActuallyMatched = numberOfEasySDIMetadatas;
//                                    }
//
//                                } else {
//                                    //Get the list of authorized metadatas
//                                    List<Map<String, Object>> accessibleDataIds = cswDataManager.getAccessibleDataIds();
//                                    //Rewrite the result list to keep only the guid value
//                                    if (accessibleDataIds != null) {
//                                        for (int i = 0; i < accessibleDataIds.size(); i++) {
//                                            authorizedGuidList.add((String) accessibleDataIds.get(i).get("guid"));
//                                        }
//                                        if (sdiPolicy.isCsw_includeharvested()) {
//                                            numberOfRecordsActuallyMatched = numberOfRecordsMatchedAttribute.getIntValue() - (numberOfEasySDIMetadatas - authorizedGuidList.size());
//                                        } else {
//                                            numberOfRecordsActuallyMatched = authorizedGuidList.size();
//                                        }
//                                    } else {
//                                        //accessibleDataIds null means all Metadatas are allowed 
//                                        authorizedGuidList = null;
//                                        if (sdiPolicy.isCsw_includeharvested()) {
//                                            numberOfRecordsActuallyMatched = numberOfRecordsMatchedAttribute.getIntValue();
//                                        } else {
//                                            numberOfRecordsActuallyMatched = cswDataManager.getCountOfEasySDIMetadatas();
//                                        }
//                                    }
//                                }
//
//                                //Get all the unauthorized metadata nodes in the result document 
//                                Iterator<Element> resultIterator = racine.getDescendants(new ElementMD_MetadataNonAuthorizedFilter(authorizedGuidList, sdiPolicy.isCsw_includeharvested()));
//                                while (resultIterator.hasNext()) {
//                                    Element e = resultIterator.next();
//                                    lElementUnauthorized.add(e);
//                                }
//
//                                //Remove those nodes from the document
//                                for (int i = 0; i < lElementUnauthorized.size(); i++) {
//                                    lElementUnauthorized.get(i).getParent().removeContent(lElementUnauthorized.get(i));
//                                }
//
//                                //Rewrite the attribute 'numberOfRecordsReturned' 
//                                if (numberOfRecordsReturnedAttribute != null) {
//                                    numberOfRecordsReturnedAttribute.setValue(String.valueOf(numberOfRecordsReturnedAttribute.getIntValue() - lElementUnauthorized.size()));
//                                }
//
//                                //Rewrite the attribute 'numberOfRecordsMatched' only if the request didn't include a constraint
//                                //(with a constraint, the number of records matched can not be calculated, so we keep the original one)
//                                if (numberOfRecordsMatchedAttribute != null && !asConstraint) {
//                                    numberOfRecordsMatchedAttribute.setValue(String.valueOf(numberOfRecordsActuallyMatched));
//                                }
//                                //If harvested MD are not included, we can set a value closer to the right one in place of the value returned by the remote server
//                                //But, this value is not guaranteed to be right
//                                if (numberOfRecordsMatchedAttribute != null && asConstraint && !sdiPolicy.isCsw_includeharvested()) {
//                                    if (numberOfRecordsMatchedAttribute.getIntValue() > numberOfRecordsActuallyMatched) {
//                                        numberOfRecordsMatchedAttribute.setValue(String.valueOf(numberOfRecordsActuallyMatched));
//                                    }
//                                }
//
//                                //Rewrite the attribute 'nextRecord'
//                                if (!sdiPolicy.isCsw_includeharvested() && !asConstraint && numberOfRecordsReturnedAttribute != null && numberOfRecordsMatchedAttribute != null && numberOfRecordsActuallyMatched == numberOfRecordsReturnedAttribute.getIntValue()) {
//                                    nextRecordAttribute.setValue(String.valueOf("0"));
//                                }
//                            } catch (JDOMException e) {
//                                e.printStackTrace();
//                            } catch (IOException e) {
//                                e.printStackTrace();
//                            }
//
//                            XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
//                            FileOutputStream outStream = new FileOutputStream(tempFile);
//                            sortie.output(doc, outStream);
//                            outStream.close();
                        }
                    }
                } else if ("GetRecordById".equals(currentOperation)) {
                    if (sdiPolicy.isCsw_anyattribute()) {
                        // Keep the metadata as it is
                        tempFile = new File(filePathList.get(0));
                    } else {
                        tempFile = applyXSLTToRemoveAttribute(xml);
                    }

                    //If the current config is used to harvest remote catalog (see config file : <harvesting-config>true</harvesting-config>),
                    //add dynamically an XML node (and its namespace definition) to the metadata to indicate that this metadata was haversting
                    if (sdiVirtualService.isHarvester()) {
                        SAXBuilder sb = new SAXBuilder();

                        Document doc = null;
                        try {
                            doc = sb.build(tempFile);
                            Element racine = doc.getRootElement();

                            //Get the metadata element from the complete response file
                            List<Element> metadataListStorage = new ArrayList<Element>();
                            Iterator<Element> metadataIterator = racine.getChildren().iterator();
                            while (metadataIterator.hasNext()) {
                                Element metadata = metadataIterator.next();
                                metadataListStorage.add(metadata);
                            }

                            //Add a new node to the metadata element
                            //<gmd:MD_Metadata xmlns:sdi="http://www.easysdi.org/2011/sdi">
                            //<sdi:platform harvested="true" />
                            //</gmd:MD_Metadata>
                            //Or update the existing node if the remote catalog is driven by EasySDI too
                            Iterator<Element> metadataStorageIterator = metadataListStorage.iterator();
                            while (metadataStorageIterator.hasNext()) {
                                Element metadata = metadataStorageIterator.next();
                                Namespace nsSDI = Namespace.getNamespace("sdi", "http://www.easysdi.org/2011/sdi");
                                metadata.addNamespaceDeclaration(nsSDI);
                                List<Element> platformElementList = metadata.getChildren("platform", nsSDI);
                                if (platformElementList.size() > 0) {
                                    //Update the existing node
                                    Element e = platformElementList.get(0);
                                    e.setAttribute("harvested", "true");
                                } else {
                                    //Add a new node
                                    Element e = new Element("platform", nsSDI);
                                    e.setAttribute("harvested", "true");
                                    metadata.addContent(e);
                                }
                            }

                            XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
                            FileOutputStream outStream = new FileOutputStream(tempFile);
                            sortie.output(doc, outStream);
                            outStream.close();
                        } catch (JDOMException e) {
                            e.printStackTrace();
                        } catch (IOException e) {
                            e.printStackTrace();
                        }
                    }
                } else {
                    tempFile = new File(filePathList.get(0));
                }

                //Close the remote server temp file response
                xml.close();

                /*
                 * if a xslt file exists then post-treat the response
                 */
                if (isPostTreat) {
                    PrintWriter out = resp.getWriter();
                    transformer = tFactory.newTransformer(new StreamSource(xsltFile));
                    transformer.transform(new StreamSource(tempFile), new StreamResult(out));
                    // delete the temporary file
                    tempFile.delete();
                    out.close();
                    // the job is done. we can go out
                    return;
                }
            }

            // No post rule to apply.
            // Copy the file result on the output stream
            resp.setContentType("application/xml");
            // resp.setContentLength(Integer.MAX_VALUE);
            resp.setContentLength((int) tempFile.length());
            OutputStream os = resp.getOutputStream();
            InputStream is = new FileInputStream(tempFile);
            try {
                byte[] buf = new byte[1024];
                int nread;
                while ((nread = is.read(buf)) >= 0) {
                    os.write(buf, 0, nread);
                }
            } finally {
                os.close();
                is.close();
                Date d = new Date();
                logger.info("ClientResponseDateTime=" + dateFormat.format(d));
                if (tempFile != null) {
                    logger.info("ClientResponseLength=" + tempFile.length());
                    tempFile.delete();
                }
            }
        } catch (SAXParseException e) {
            e.printStackTrace();
            logger.error(e.getMessage());
            resp.setHeader("easysdi-proxy-error-occured", "true");
            try {
                owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_INVALID_FORMAT, OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK);
            } catch (IOException e1) {
                logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
            }
        } catch (Exception e) {
            e.printStackTrace();
            logger.error(e.toString());
            resp.setHeader("easysdi-proxy-error-occured", "true");
            try {
                owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY, OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK);
            } catch (IOException e1) {
                logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
            }
        }
    }

    /*
     * (non-Javadoc)
     * 
     * @see
     * org.easysdi.proxy.core.ProxyServlet#requestPreTreatmentGET(javax.servlet
     * .http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @SuppressWarnings("unchecked")
    @Override
    protected void requestPreTreatmentGET(HttpServletRequest req, HttpServletResponse resp) {
        try {
            String currentOperation = null;
            String version = "000";
            String requestedId = "";
            String constraint = "";
            String content = "";
            String constraintLanguage = null;
            String constraint_language_version = null;

            Enumeration<String> parameterNames = req.getParameterNames();
            String paramUrl = "";

            while (parameterNames.hasMoreElements()) {
                String key = parameterNames.nextElement();
                String value = URLEncoder.encode(req.getParameter(key), "UTF-8");
                if (key.equalsIgnoreCase("id")) {
                    requestedId = value;
                    continue;
                }
                if (key.equalsIgnoreCase("Request")) {
                    if (value.equalsIgnoreCase("capabilities")) {
                        currentOperation = "GetCapabilities";
                    } else {
                        currentOperation = value;
                    }
                    continue;
                }
                if (key.equalsIgnoreCase("version")) {
                    requestedVersion = value;
                    continue;
                }

                if (key.equalsIgnoreCase("Constraint")) {
                    constraint = value;
                    continue;
                }
                if (key.equalsIgnoreCase("constraintLanguage")) {
                    constraintLanguage = value;
                    continue;
                }
                if (key.equalsIgnoreCase("constraint_language_version")) {
                    constraint_language_version = value;
                    continue;
                }
                //Content specific vendor parameter
                if (key.equalsIgnoreCase("content")) {
                    content = value;
                    continue;
                }
            }

            logger.info("Request=" + req.getQueryString());
            logger.info("RequestOperation=" + currentOperation);

            //Generate OGC exception and send it to the client if current operation is not allowed
            if (!isOperationAllowed(currentOperation)) {
                owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_OPERATION_NOT_ALLOWED, OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_OK);
                return;
            }


            //GetRecordById
            if (currentOperation.equalsIgnoreCase("GetRecordById")) {
                logger.trace("Start - Data Accessibility");

                if (!cswDataManager.isMetadataAccessible(requestedId)) {
                    logger.info(requestedId + " - Requested metadata version is not accessible regarding policy restriction. Method isMetadataAccessible returned false.");
                    requestedId = cswDataManager.getMetadataVersionAccessible();
                    logger.info(requestedId + " - Requested metadata id was change by method getMetadataVersionAccessible.");
                }

                if (requestedId == null) {
                    owsExceptionReport.sendHttpServletResponse(request, response, cswDataManager.generateEmptyResponse(requestedVersion), "text/xml; charset=utf-8", HttpServletResponse.SC_OK);
                    return;
                }

                logger.trace("End - Data Accessibility");

            } else if (currentOperation.equalsIgnoreCase("GetRecords")) {
                //Get the constraint language
                if (constraintLanguage == null) {
                    //Use CQL_TEXT to build the constraint
                    constraintLanguage = "CQL_TEXT";
                    constraint_language_version = "1.1.0";
                }
                if (constraintLanguage.equalsIgnoreCase("CQL_TEXT")) {
                    //Add Geographical filter as CQL_TEXT additional parameter
                    constraint = cswDataManager.addCQLFilter(constraint);                    

                } else if (constraintLanguage.equalsIgnoreCase("FILTER")) {
                    //Add Geographical filter
                    //Add policy restrictions filter
                    constraint = cswDataManager.addXMLFilter(constraint);

                } else {
                    //The constraint language specified in the request is not valid, or not yet supported by the proxy
                    try {
                        owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_INVALID_PARAMETER_VALUE, OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "constraintLanguage", HttpServletResponse.SC_OK);
                    } catch (IOException e1) {
                        logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
                    }
                    return;
                }
            }

            // Build the request to dispatch
            parameterNames = req.getParameterNames();
            while (parameterNames.hasMoreElements()) {
                String key = parameterNames.nextElement();
                String value = URLEncoder.encode(req.getParameter(key), "UTF-8");

                if (key.equalsIgnoreCase("id")) {
                    paramUrl = paramUrl + key + "=" + requestedId + "&";
                } else if (key.equalsIgnoreCase("constraint")) {
                    //paramUrl = paramUrl + key + "=" + URLEncoder.encode(constraint, "UTF-8") + "&";
                    //paramUrl = paramUrl + key + "=" + constraint + "&";
                } else if (key.equalsIgnoreCase("constraint_language_version")) {
                    //paramUrl = paramUrl + key + "=" + URLEncoder.encode(constraint, "UTF-8") + "&";
                    //paramUrl = paramUrl + key + "=" + constraint_language_version + "&";
                } else if (key.equalsIgnoreCase("constraintLanguage")) {
                } else {
                    paramUrl = paramUrl + key + "=" + value + "&";
                }
            }
            if (constraint != null && constraint.length() > 0) {
                asConstraint = true;
                paramUrl = paramUrl + "constraint=" + constraint + "&";
                paramUrl = paramUrl + "constraintLanguage=" + constraintLanguage + "&";
                if (constraint_language_version != null) {
                    paramUrl = paramUrl + "constraint_language_version=" + constraint_language_version + "&";
                }
            } else {
                paramUrl = paramUrl + "constraintLanguage=" + constraintLanguage + "&";
                if (constraint_language_version != null) {
                    paramUrl = paramUrl + "constraint_language_version=" + constraint_language_version + "&";
                }
            }

            if (requestedVersion != null) {
                version = requestedVersion;
            }
            version = version.replaceAll("\\.", "");

            // Send the request to the remote server
            List<String> filePathList = new Vector<String>();
            String filePath = sendData("GET", getPhysicalServiceURLByIndex(0), paramUrl);
            filePathList.add(filePath);

            if (currentOperation.equalsIgnoreCase("GetRecords") || currentOperation.equalsIgnoreCase("GetRecordById")) {
                //If the virtual service is used to harvest remote servers, the metadatas are never completed.
                //The completion process of metadatas is only available for EasySDI metadatas (metadatas created and managed by the solution)
                if ((content.equalsIgnoreCase("") || content.equalsIgnoreCase("complete")) && !sdiVirtualService.isHarvester()) {
                    logger.trace("Start - Complete metadata");
                    //Build complete metadata
                    CSWProxyMetadataContentManager cswManager = new CSWProxyMetadataContentManager(this);
                    if (!cswManager.buildCompleteMetadata(filePathList.get(0))) {
                        try {
                            owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY + "Request can not be completed. " + cswManager.GetLastError(), OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "constraintLanguage", HttpServletResponse.SC_OK);
                        } catch (IOException e1) {
                            logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
                        }
                        return;
                    }
                    logger.trace("End - Complete metadata");
                }
            }

            //Transform the request response
            transform(version, currentOperation, req, resp, filePathList);

        } catch (AvailabilityPeriodException e) {
            logger.error(e.getMessage());
            try {
                owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED, OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_OK);
            } catch (IOException e1) {
                logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
            }
        } catch (Exception e) {
            e.printStackTrace();
            logger.error(e.toString());
            resp.setHeader("easysdi-proxy-error-occured", "true");
            try {
                owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY + "CSWProxyServlet.requestPreTreatmentGET returns : " + e.toString(), OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_OK);
            } catch (IOException e1) {
                logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
            }
        }
    }

    /**
     *
     */
    @SuppressWarnings("unchecked")
    @Override
    protected void requestPreTreatmentPOST(HttpServletRequest req, HttpServletResponse resp) {
        try {
            try {
                //Read request
                XMLReader xr = XMLReaderFactory.createXMLReader();
                CswRequestHandler rh = new CswRequestHandler();
                xr.setContentHandler(rh);

                BufferedReader r = req.getReader();
                StringBuffer param = new StringBuffer();
                char[] buf = new char[4 * 1024]; // 4Kchar buffer
                int len;
                while ((len = r.read(buf, 0, buf.length)) != -1) {
                    param.append(buf, 0, len);
                }

                logger.info("Request=" + param.toString().replace('\n', ' ').replace('\r', ' '));
                InputStreamReader in = new InputStreamReader(new ByteArrayInputStream(param.toString().getBytes()));
                xr.parse(new InputSource(in));

                String version = rh.getVersion();
                requestedVersion = version;
                if (version != null) {
                    version = version.replaceAll("\\.", "");
                }
                String currentOperation = rh.getOperation();
                logger.info("RequestOperation=" + currentOperation);

                //Generate OGC exception and send it to the client if current operation is not allowed
                if (!isOperationAllowed(currentOperation)) {
                    owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_OPERATION_NOT_ALLOWED, OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_OK);
                    return;
                }

                //Check the value of the PARAMETER 'content'
                String content = rh.getContent();
                if (!content.equalsIgnoreCase("") && !content.equalsIgnoreCase("core") && !content.equalsIgnoreCase("complete")) {
                    owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_INVALID_PARAMETER_VALUE, OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "CONTENT", HttpServletResponse.SC_OK);
                    return;
                }

                //Only one physical service is supported by virtual service for the moment,
                // default service tag will be implemented when several services will be supported
                SdiPhysicalservice physicalService = getPhysicalServiceByIndex(0);

                //Transaction
                if (currentOperation.equalsIgnoreCase("Transaction") && !sdiVirtualService.isHarvester()) {
                    //If the transaction is INSERT OR UPDATE, add the specific node to indicate that the metadata is handle by EasySDI
                    //sdi:platform harvested="false"
                    if (rh.isTransactionInsert() || rh.isTransactionUpdate()) {
//                        SAXBuilder sb = new SAXBuilder();
//                        Document doc = sb.build(new InputStreamReader(new ByteArrayInputStream(param.toString().getBytes())));
//
//                        Element racine = doc.getRootElement();
//
//                        //Get the transaction type element (Update or Insert) from the complete response file
//                        List<Element> resultListStorage = new ArrayList<Element>();
//                        Iterator<Element> resultIterator = racine.getDescendants(new ElementTransactionTypeFilter());
//                        while (resultIterator.hasNext()) {
//                            Element result = resultIterator.next();
//                            resultListStorage.addAll(result.getChildren());
//                            for (int i = resultListStorage.size() - 1; i >= 0; i--) {
//                                if (resultListStorage.get(i).getName().equalsIgnoreCase("Constraint") || resultListStorage.get(i).getName().equalsIgnoreCase("RecordProperty")) {
//                                    resultListStorage.remove(i);
//                                }
//                            }
//
//                        }
//
//                        //Add a new node to the metadata element
//                        //<gmd:MD_Metadata xmlns:sdi="http://www.easysdi.org/2011/sdi">
//                        //<sdi:platform harvested="false" />
//                        //</gmd:MD_Metadata>
//                        Iterator<Element> resultStorageIterator = resultListStorage.iterator();
//                        Element result = null;
//                        Namespace nsSDI = Namespace.getNamespace("sdi", "http://www.easysdi.org/2011/sdi");
//                        while (resultStorageIterator.hasNext()) {
//                            result = resultStorageIterator.next();
//                            if (result.getChild("harvested", nsSDI) == null) {
//                                Element e = new Element("platform", nsSDI);
//                                e.setAttribute("harvested", "false");
//                                result.addContent(e);
//                            }
//                        }
//                        if (result != null) {
//                            result.addNamespaceDeclaration(nsSDI);
//                        }
//
//                        XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
//                        ByteArrayOutputStream out = new ByteArrayOutputStream();
//                        sortie.output(doc, out);
//                        param = new StringBuffer(out.toString("UTF-8"));
                    }
                }

                if (currentOperation.equalsIgnoreCase("Transaction") && physicalService.getSdiSysAuthenticationconnectorByServiceauthenticationId() != null && physicalService.getSdiSysAuthenticationconnectorByServiceauthenticationId().getValue().equalsIgnoreCase("geonetwork")) {
                    // Send the xml
                    StringBuffer response = sendFile(physicalService.getResourceurl(), param, physicalService.getServiceurl());

                    // Get the response
                    OutputStream os = resp.getOutputStream();
                    InputStream is = new ByteArrayInputStream(response.toString().getBytes());
                    int byteRead;
                    try {
                        while ((byteRead = is.read()) != -1) {
                            os.write(byteRead);
                        }
                    } finally {
                        Date d = new Date();
                        logger.info("ClientResponseDateTime=" + dateFormat.format(d));
                        if (os != null) {
                            logger.info("ClientResponseLength=" + os.toString().length());
                        }
                        os.flush();
                        os.close();
                    }
                    os = null;
                    is = null;
                } //GetRecordById
                else if (currentOperation.equalsIgnoreCase("GetRecordById")) {
                    logger.trace("Start - Data Accessibility");
                    String dataId = rh.getRecordId();
                    if (!cswDataManager.isMetadataAccessible(dataId)) {
                        logger.info(dataId + " - Requested metadata version is not accessible regarding policy restriction. Method isMetadataAccessible returned false.");
                        dataId = cswDataManager.getMetadataVersionAccessible();
                        logger.info(dataId + " - Requested metadata id was change by method getMetadataVersionAccessible.");
                    }
                    if (dataId == null) {
                        logger.debug("Metadata id is Null. Return an empty response");
                        owsExceptionReport.sendHttpServletResponse(request, response, cswDataManager.generateEmptyResponse(requestedVersion), "text/xml; charset=utf-8", HttpServletResponse.SC_OK);
                        return;
                    }
                    if (dataId.compareTo(rh.getRecordId()) != 0) {
                        //Change the metadata's id in the request
                        int start = param.indexOf(rh.getRecordId());
                        int end = start + rh.getRecordId().length();
                        param.replace(start, end, dataId);
                    }


                    logger.trace("End - Data Accessibility");

                    List<String> filePathList = new Vector<String>();
                    String filePath = sendData("POST", getPhysicalServiceURLByIndex(0), param.toString());
                    filePathList.add(filePath);
                    if ((rh.getContent().equalsIgnoreCase("") || rh.getContent().equalsIgnoreCase("complete")) && !sdiVirtualService.isHarvester()) {
                        //If the config is used to harvest remote servers, the metadatas are never completed.
                        //The completion process of metadatas is only available for EasySDI metadatas (metadatas created and managed by the solution)
                        logger.trace("Start - Complete metadata");
                        //Build complete metadata
                        CSWProxyMetadataContentManager cswManager = new CSWProxyMetadataContentManager(this);
                        if (!cswManager.buildCompleteMetadata(filePathList.get(0))) {
                            owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY + "Request can not be completed. " + cswManager.GetLastError(), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK);
                            return;
                        }
                        logger.trace("End - Complete metadata");

                    }
                    //Transform the request response
                    transform(version, currentOperation, req, resp, filePathList);
                } //GetRecords
                else if (currentOperation.equalsIgnoreCase("GetRecords")) {
                    logger.trace("Start - Data Accessibility");
                    if (!cswDataManager.isAllDataAccessibleForGetRecords()
                            || (sdiPolicy.getSdiCswSpatialpolicy() != null && sdiPolicy.getSdiCswSpatialpolicy().isValid())
                            || !sdiPolicy.isCsw_includeharvested()) {
                        //Add a filter on the data id in the request
                        param = cswDataManager.addXMLFilterToPOST(sdiVirtualService.getIdentifiersearchattribute(), param);
                        if (param == null) {
                            owsExceptionReport.sendHttpServletResponse(request, response, cswDataManager.generateEmptyResponseForGetRecords(requestedVersion), "text/xml; charset=utf-8", HttpServletResponse.SC_OK);
                            return;
                        }
                    }
                    //Add a filter on the data id in the request
                    logger.trace("End - Data Accessibility");

                    List<String> filePathList = new Vector<String>();
                    String filePath = sendData("POST", getPhysicalServiceURLByIndex(0), param.toString());
                    filePathList.add(filePath);
                    if (rh.getContent().equalsIgnoreCase("") || rh.getContent().equalsIgnoreCase("complete")) {
                        logger.trace("Start - Complete metadata");
                        //Build complete metadata
                        CSWProxyMetadataContentManager cswManager = new CSWProxyMetadataContentManager(this);
                        if (!cswManager.buildCompleteMetadata(filePathList.get(0))) {
                            owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY + "Request can not be completed. " + cswManager.GetLastError(), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK);
                            return;
                        }
                        logger.trace("End - Complete metadata");

                    }
                    //Transform the request response
                    transform(version, currentOperation, req, resp, filePathList);
                } //Others operations
                else {
                    List<String> filePathList = new Vector<String>();
                    String filePath = sendData("POST", getPhysicalServiceURLByIndex(0), param.toString());
                    filePathList.add(filePath);
                    //Transform the request response
                    transform(version, currentOperation, req, resp, filePathList);
                }
            } catch (AvailabilityPeriodException e) {
                logger.error(e.getMessage());
                owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_OPERATION_NOT_SUPPORTED, OWSExceptionReport.CODE_OPERATION_NOT_SUPPORTED, "REQUEST", HttpServletResponse.SC_OK);
            } catch (SAXParseException e) {
                logger.error(e.toString());
                resp.setHeader("easysdi-proxy-error-occured", "true");
                owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_INVALID_PARAMETER_VALUE + "The query syntax is invalid", OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE, "", HttpServletResponse.SC_OK);
            } catch (Exception e) {
                e.printStackTrace();
                logger.error(e.toString());
                resp.setHeader("easysdi-proxy-error-occured", "true");
                owsExceptionReport.sendExceptionReport(req, resp, OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY + "CSWProxyServlet.requestPreTreatmentPOST returns : " + e.toString(), OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK);
            }
        } catch (IOException e1) {
            logger.error(OWSExceptionReport.TEXT_EXCEPTION_ERROR, e1);
        }

    }

    /**
     * *
     * Builds the xslt that will be applied against the csw capabilities version
     * 2.00 The generated xslt will remove the unauthorized operations from the
     * capabilities document and will replace the urls
     *
     * @param req the HttpServletRequest request
     * @return StringBuffer containing the xslt
     */
    protected StringBuffer generateXSLTForCSWCapabilities200(String url, List<String> deniedOperations, List<String> permitedOperations) {

        try {

            StringBuffer CSWCapabilities200 = new StringBuffer();
            CSWCapabilities200.append("<?xml version=\"1.0\" encoding=\"UTF-8\" ?><xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
            CSWCapabilities200.append("<xsl:template match=\"ows:Get\">");
            CSWCapabilities200.append("<ows:Get>");
            CSWCapabilities200.append("<xsl:attribute name=\"xlink:href\">");
            CSWCapabilities200.append(url);
            CSWCapabilities200.append("</xsl:attribute>");
            CSWCapabilities200.append("<xsl:attribute name=\"xlink:type\">");
            CSWCapabilities200.append("<xsl:value-of select=\"@xlink:type\"/>");
            CSWCapabilities200.append("</xsl:attribute>");
            CSWCapabilities200.append("</ows:Get>");
            CSWCapabilities200.append("</xsl:template>");
            CSWCapabilities200.append("<xsl:template match=\"ows:Post\">");
            CSWCapabilities200.append("<ows:Post>");
            CSWCapabilities200.append("<xsl:attribute name=\"xlink:href\">");
            CSWCapabilities200.append(url);
            CSWCapabilities200.append("</xsl:attribute>");
            CSWCapabilities200.append("<xsl:attribute name=\"xlink:type\">");
            CSWCapabilities200.append("<xsl:value-of select=\"@xlink:type\"/>");
            CSWCapabilities200.append("</xsl:attribute>");
            CSWCapabilities200.append("</ows:Post>");
            CSWCapabilities200.append("</xsl:template>");

            if (!sdiPolicy.isAnyoperation() || deniedOperations.size() > 0) {
                Iterator<String> it = permitedOperations.iterator();
                while (it.hasNext()) {
                    String text = it.next();
                    if (text != null) {
                        CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation[@name='");
                        CSWCapabilities200.append(text);
                        CSWCapabilities200.append("']\">");
                        CSWCapabilities200.append("<!-- Copy the current node -->");
                        CSWCapabilities200.append("<xsl:copy>");
                        CSWCapabilities200.append("<!-- Including any attributes it has and any child nodes -->");
                        CSWCapabilities200.append("<xsl:apply-templates select=\"@*|node()\"/>");
                        CSWCapabilities200.append("</xsl:copy>");
                        CSWCapabilities200.append("</xsl:template>");
                    }
                }

                it = deniedOperations.iterator();
                while (it.hasNext()) {
                    CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation[@name='");
                    CSWCapabilities200.append(it.next());
                    CSWCapabilities200.append("']\"></xsl:template>");
                }
            }
            if (permitedOperations.size() == 0) {
                CSWCapabilities200.append("<xsl:template match=\"ows:OperationsMetadata/ows:Operation\"></xsl:template>");
            }


            CSWCapabilities200.append("  <!-- Whenever you match any node or any attribute -->");
            CSWCapabilities200.append("<xsl:template match=\"node()|@*\">");
            CSWCapabilities200.append("<!-- Copy the current node -->");
            CSWCapabilities200.append("<xsl:copy>");
            CSWCapabilities200.append("<!-- Including any attributes it has and any child nodes -->");
            CSWCapabilities200.append("<xsl:apply-templates select=\"@*|node()\"/>");
            CSWCapabilities200.append("</xsl:copy>");
            CSWCapabilities200.append("</xsl:template>");

            CSWCapabilities200.append("</xsl:stylesheet>");
            return CSWCapabilities200;
        } catch (Exception e) {
            e.printStackTrace();
            logger.error(e.getMessage());
        }

        // If something goes wrong, an empty stylesheet is returned.
        StringBuffer sb = new StringBuffer();
        return sb
                .append("<xsl:stylesheet version=\"1.00\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> </xsl:stylesheet>");
    }

    /**
     * @param url
     * @return
     */
    protected List<String> getAttributesNotAllowedInMetadata(String url) {
        List<String> attrList = new Vector<String>();

        // Get the XPath defining excluded attributes.
        if (sdiPolicy.isCsw_anyattribute()) {
            attrList.add("%");
            return attrList;
        }
        Set<SdiExcludedattribute> excludedAttributes = sdiPolicy.getSdiExcludedattributes();
        if (!excludedAttributes.isEmpty()) {
            Iterator<SdiExcludedattribute> i = excludedAttributes.iterator();
            while (i.hasNext()) {
                attrList.add(i.next().getPath());
            }
        }

        // in any other case the attribute is not allowed
        return attrList;
    }

    protected StringBuffer buildServiceMetadataCapabilitiesXSLT() {
        StringBuffer serviceMetadataXSLT = new StringBuffer();
        serviceMetadataXSLT.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?><xsl:stylesheet version=\"1.00\" xmlns:wfs=\"http://www.opengis.net/wfs\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\">");
        serviceMetadataXSLT.append("<xsl:output method=\"xml\" omit-xml-declaration=\"no\" version=\"1.0\" encoding=\"UTF-8\" indent=\"yes\"/>");
        serviceMetadataXSLT.append("<xsl:strip-space elements=\"*\" />");
        //Copy all
        serviceMetadataXSLT.append("<xsl:template match=\"node()|@*\">");
        serviceMetadataXSLT.append("<!-- Copy the current node -->");
        serviceMetadataXSLT.append("<xsl:copy>");
        serviceMetadataXSLT.append("<!-- Including any attributes it has and any child nodes -->");
        serviceMetadataXSLT.append("<xsl:apply-templates select=\"@*|node()\"/>");
        serviceMetadataXSLT.append("</xsl:copy>");
        serviceMetadataXSLT.append("</xsl:template>");

        //Replace Service node
        serviceMetadataXSLT.append("<xsl:template match=\"ows:ServiceIdentification\">");
        serviceMetadataXSLT.append("<xsl:copy>");

        //Service type
        serviceMetadataXSLT.append("<xsl:copy-of select=\"ows:ServiceType\"/>");
        serviceMetadataXSLT.append("<xsl:copy-of select=\"ows:ServiceTypeVersion\"/>");

        if (!sdiVirtualService.isReflectedmetadata()) {
            SdiVirtualmetadata virtualMetadata = sdiVirtualService.getSdiVirtualmetadatas().iterator().next();
            //Title
            if (!virtualMetadata.isInheritedtitle()) {
                serviceMetadataXSLT.append("<xsl:element name=\"ows:Title\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getTitle() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
            }
            //Abstract
            if (!virtualMetadata.isInheritedsummary()) {
                serviceMetadataXSLT.append("<xsl:element name=\"ows:Abstract\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getSummary() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
            }
            //Keyword
            if (!virtualMetadata.isInheritedkeyword()) {
                String skeywords = virtualMetadata.getKeyword();
                String[] keywords = skeywords.split(",");
                serviceMetadataXSLT.append("<xsl:element name=\"ows:Keywords\"> ");
                for (int n = 0; n < keywords.length; n++) {
                    serviceMetadataXSLT.append("<xsl:element name=\"ows:Keyword\"> ");
                    serviceMetadataXSLT.append("<xsl:text>" + keywords[n] + "</xsl:text>");
                    serviceMetadataXSLT.append("</xsl:element>");
                }
                serviceMetadataXSLT.append("</xsl:element>");
            }
            //Fees
            if (!virtualMetadata.isInheritedfee()) {
                serviceMetadataXSLT.append("<xsl:element name=\"ows:Fees\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getFee() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
            }
            //AccesConstraints
            if (!virtualMetadata.isInheritedaccessconstraint()) {
                serviceMetadataXSLT.append("<xsl:element name=\"ows:AccessConstraints\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getAccessconstraint() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
            }
            serviceMetadataXSLT.append("</xsl:copy>");
            serviceMetadataXSLT.append("</xsl:template>");

            serviceMetadataXSLT.append("<xsl:template match=\"ows:ServiceProvider\"> ");
            serviceMetadataXSLT.append("<xsl:copy>");

            if (!virtualMetadata.isInheritedcontact()) {
                //contactInfo
                serviceMetadataXSLT.append("<xsl:element name=\"ows:ProviderName\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getContactorganization() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("<xsl:element name=\"ows:ServiceContact\"> ");//ows:ServiceContact
                serviceMetadataXSLT.append("<xsl:element name=\"ows:IndividualName\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getContactname() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("<xsl:element name=\"ows:PositionName\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getContactposition() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("<xsl:element name=\"ows:ContactInfo\"> ");//ows:ContactInfo
                serviceMetadataXSLT.append("<xsl:element name=\"ows:Phone\"> ");//ows:Phone
                serviceMetadataXSLT.append("<xsl:element name=\"ows:Voice\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getContactphone() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("<xsl:element name=\"ows:Facsimile\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getContactfax() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("</xsl:element>");//ows:Phone

                serviceMetadataXSLT.append("<xsl:element name=\"ows:Address\"> ");//ows:Address
                serviceMetadataXSLT.append("<xsl:element name=\"ows:DelivryPoint\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getContactaddress() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("<xsl:element name=\"ows:City\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getContactlocality() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("<xsl:element name=\"ows:AdministrativeArea\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getContactstate() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("<xsl:element name=\"ows:PostalCode\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getContactpostalcode() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("<xsl:element name=\"ows:Country\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getSdiSysCountry().getName() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("<xsl:element name=\"ows:ElectronicMailAddress\"> ");
                serviceMetadataXSLT.append("<xsl:text>" + virtualMetadata.getContactemail() + "</xsl:text>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("</xsl:element>");//ows:Address
                serviceMetadataXSLT.append("<xsl:element name=\"ows:OnlineResource\"> ");
                serviceMetadataXSLT.append("<xsl:attribute name=\"xlink:href\">");
                serviceMetadataXSLT.append(virtualMetadata.getContacturl());
                serviceMetadataXSLT.append("</xsl:attribute>");
                serviceMetadataXSLT.append("</xsl:element>");
                serviceMetadataXSLT.append("</xsl:element>");//ows:ContactInfo
                serviceMetadataXSLT.append("</xsl:element>");//ows:ServiceContact
            }
        }
        serviceMetadataXSLT.append("</xsl:copy>");
        serviceMetadataXSLT.append("</xsl:template>");
        serviceMetadataXSLT.append("</xsl:stylesheet>");

        return serviceMetadataXSLT;
    }

    /**
     * Only used in CSW
     *
     * @param urlstr
     * @param param
     * @param loginServiceUrl
     * @return
     */
    @SuppressWarnings("deprecation")
    protected StringBuffer sendFile(String urlstr, StringBuffer param, String loginServiceUrl) {

        try {
            Date d = new Date();
            logger.info("RemoteRequestUrl=" + urlstr);
            logger.info("RemoteRequest=" + param.toString());
            logger.info("RemoteRequestLength=" + param.length());
            logger.info("RemoteRequestDateTime=" + dateFormat.format(d));

            HttpClient client = new HttpClient();

            PostMethod post = new PostMethod(urlstr);
            post.addRequestHeader("Content-Type", "application/xml");
            post.addRequestHeader("Charset", "UTF-8");
            post.setRequestBody(new ByteArrayInputStream(param.toString().getBytes("UTF-8")));

            GetMethod loginGet = new GetMethod(loginServiceUrl);
            client.executeMethod(loginGet);
            client.executeMethod(post);
            StringBuffer response = new StringBuffer(post.getResponseBodyAsString());

            logger.info("RemoteResponseToRequestUrl=" + urlstr);
            logger.info("RemoteResponseLength=" + response.length());

            d = new Date();
            logger.info("RemoteResponseDateTime=" + dateFormat.format(d));

            return response;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return new StringBuffer();
    }
}