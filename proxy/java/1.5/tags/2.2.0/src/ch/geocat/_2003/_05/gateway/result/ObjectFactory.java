//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.03.07 at 04:52:23 PM CET 
//


package ch.geocat._2003._05.gateway.result;

import javax.xml.bind.JAXBElement;
import javax.xml.bind.annotation.XmlElementDecl;
import javax.xml.bind.annotation.XmlRegistry;
import javax.xml.namespace.QName;


/**
 * This object contains factory methods for each 
 * Java content interface and Java element interface 
 * generated in the ch.geocat._2003._05.gateway.result package. 
 * <p>An ObjectFactory allows you to programatically 
 * construct new instances of the Java representation 
 * for XML content. The Java representation of XML 
 * content can consist of schema derived interfaces 
 * and classes representing the binding of schema 
 * type definitions, element declarations and model 
 * groups.  Factory methods for each of these are 
 * provided in this class.
 * 
 */
@XmlRegistry
public class ObjectFactory {

    private final static QName _QueryResult_QNAME = new QName("http://www.geocat.ch/2003/05/gateway/result", "queryResult");

    /**
     * Create a new ObjectFactory that can be used to create new instances of schema derived classes for package: ch.geocat._2003._05.gateway.result
     * 
     */
    public ObjectFactory() {
    }

    /**
     * Create an instance of {@link QueryResultType }
     * 
     */
    public QueryResultType createQueryResultType() {
        return new QueryResultType();
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link QueryResultType }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://www.geocat.ch/2003/05/gateway/result", name = "queryResult")
    public JAXBElement<QueryResultType> createQueryResult(QueryResultType value) {
        return new JAXBElement<QueryResultType>(_QueryResult_QNAME, QueryResultType.class, null, value);
    }

}
