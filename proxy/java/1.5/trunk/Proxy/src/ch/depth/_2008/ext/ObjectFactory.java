//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.03.07 at 04:51:42 PM CET 
//


package ch.depth._2008.ext;

import javax.xml.bind.JAXBElement;
import javax.xml.bind.annotation.XmlElementDecl;
import javax.xml.bind.annotation.XmlRegistry;
import javax.xml.namespace.QName;


/**
 * This object contains factory methods for each 
 * Java content interface and Java element interface 
 * generated in the ch.depth._2008.ext package. 
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

    private final static QName _EXExtendedMetadataType_QNAME = new QName("http://www.depth.ch/2008/ext", "EX_extendedMetadata_Type");

    /**
     * Create a new ObjectFactory that can be used to create new instances of schema derived classes for package: ch.depth._2008.ext
     * 
     */
    public ObjectFactory() {
    }

    /**
     * Create an instance of {@link EXExtendedMetadataPropertyType }
     * 
     */
    public EXExtendedMetadataPropertyType createEXExtendedMetadataPropertyType() {
        return new EXExtendedMetadataPropertyType();
    }

    /**
     * Create an instance of {@link EXExtendedMetadataType }
     * 
     */
    public EXExtendedMetadataType createEXExtendedMetadataType() {
        return new EXExtendedMetadataType();
    }

    /**
     * Create an instance of {@link JAXBElement }{@code <}{@link EXExtendedMetadataType }{@code >}}
     * 
     */
    @XmlElementDecl(namespace = "http://www.depth.ch/2008/ext", name = "EX_extendedMetadata_Type")
    public JAXBElement<EXExtendedMetadataType> createEXExtendedMetadataType(EXExtendedMetadataType value) {
        return new JAXBElement<EXExtendedMetadataType>(_EXExtendedMetadataType_QNAME, EXExtendedMetadataType.class, null, value);
    }

}
