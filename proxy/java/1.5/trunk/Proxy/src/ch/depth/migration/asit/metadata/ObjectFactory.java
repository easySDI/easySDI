//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2009.02.16 at 01:52:35 PM CET 
//


package ch.depth.migration.asit.metadata;

import javax.xml.bind.annotation.XmlRegistry;


/**
 * This object contains factory methods for each 
 * Java content interface and Java element interface 
 * generated in the ch.depth.migration.asit.metadata package. 
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


    /**
     * Create a new ObjectFactory that can be used to create new instances of schema derived classes for package: ch.depth.migration.asit.metadata
     * 
     */
    public ObjectFactory() {
    }

    /**
     * Create an instance of {@link ASITVD }
     * 
     */
    public ASITVD createASITVD() {
        return new ASITVD();
    }

    /**
     * Create an instance of {@link Class }
     * 
     */
    public Class createClass() {
        return new Class();
    }

    /**
     * Create an instance of {@link ASITVD.Metadata }
     * 
     */
    public ASITVD.Metadata createASITVDMetadata() {
        return new ASITVD.Metadata();
    }

    /**
     * Create an instance of {@link Class.Attribute }
     * 
     */
    public Class.Attribute createClassAttribute() {
        return new Class.Attribute();
    }

}
